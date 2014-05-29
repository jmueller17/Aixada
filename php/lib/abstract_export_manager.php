<?php


require_once(__ROOT__ . 'php/external/FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start(); // Starts FirePHP output buffering
$firephp = FirePHP::getInstance(true);



require_once(__ROOT__ . 'php/lib/exceptions.php');
require_once(__ROOT__ . 'local_config/config.php');
require_once(__ROOT__ . 'php/inc/database.php');
require_once(__ROOT__ . 'php/lib/gdrive.php');
require_once(__ROOT__ . 'php/utilities/general.php');


/**
 * @see Zend_Loader
 */
//$dirs = array(__ROOT__.'php/external/ZendGdata-1.12.2/library'); 



class abstract_export_manager {
  
	
	/**
	 * 
	 * Publish data to the public export directory of this site? 
	 * @var boolean
	 */
	protected $public = true;  
	
	
	/**
	 * 
	 * Should the file be offered for download? Default is yes but can be turned off. 
	 * @var boolean
	 */
	public $download = true; 
	
	
	/**
	 * 
	 * If several export files have been created, if set to true, 
	 * a zip file will be created containing all of them and offered for download/published instead.  
	 * @var boolean
	 */
	public $zipall	= false; 
	
	
	/**
	 * Directory where published export files will be saved
	 */
	protected $export_dir = null;
	
	
	
	/**
	 * 
	 * Directory where temporary files are created. 
	 * @var string
	 */
	private $tmp_dir = null; 
	
	
	
	/**
	 * 
	 * Hashtable holding the filenames and the corresponding xml result strings to be exported. 
	 * The hash key is the filename; the entry the xml data to be exported. 
	 * In case of exporting the products for a given list or providers, several files will be created: the provider list
	 * and one file for each product list. 
	 * @var array
	 */
	protected $xml_result = null; 
	

	
	/**
	 * 
	 * The filename provided by the user; This is name of the file returned to the user
	 * @var string
	 */
	protected $filename = "";
	
	
	
	/**
	 * 
	 * List of all files that have been created during the export process.
	 * Usually these are all the files that will be bundled.  The array contains
	 * the absolute file paths of the created files. Usually added in function write_file()
	 * @var array automatically created
	 */
	private $created_files = null; 
	
	

	
	/**
	 * 
	 * Format data should be exported to: CSV | XML
	 * @var string
	 */
	protected $format = "csv";
	
	
	
	/**
	 * 
	 * Which table will be exported. Needs to be overwritten by subclasses
	 * @var string
	 */
	protected $export_table = "";


	/**
	 * 
	 * Additional information about the exported data to be included in the XML file
	 * @var string
	 */
	protected $xml_metadata = "";
	
	
	
	
	
    public function __construct($filename="")
    {    	

    	//if no filename is given, construct one
    	if ($filename == ""){
			$this->filename = "Export_" . $export_table . date('Y-m-d_h:i');    		
    	} else {
	  		$this->filename = $filename;        	   
    	}
    	
    	$this->export_dir = __ROOT__ . 'local_config/export/';

    	$this->tmp_dir = __ROOT__ . 'local_config/tmp/';
    	
    	$this->created_files = array();
    	
    	//get the data from the database
    	$this->read_db_table();
    } 
    
    
    
    /**
     * 
     * Needs to be overwritten by subclasses according to the database table to be
     * exported. Returns xml result set.
     */
    protected function read_db_table(){
    }
    
    

    
    /**
     * 
     * Returns the xml result strings of the tables to be exported. Available after read_db_table() has been 
     * executed, i.e. after constructer has been called.  
     */
    public function get_xml_results(){    	
    	return $this->xml_result; 

    }
    
    
    /**
     * 
     * Calls the sub routines for writing the actual files according to the given format. 
     * @param boolean $publish
     * @param string $format  CSV | XML
     * @param unknown_type $email
     * @param unknown_type $pwd
     */
    public function export($public=false, $format='csv', $email='', $pwd=''){
    	
    	switch($format){
    		
    		case "csv":
    		case "CSV":
    			$this->write_csv();
    			break;
    		case "xml":
    		case "XML":
    			$this->write_xml();
    			break;
    		case "gdrive":
    		case "gDrive":
    		case "google":
    			$this->write_gdrive($email, $pwd);
    			break;
    		default:
    			throw new Exception("Export format {$format} not supported.");
    			exit;		
    	}  

    	//if several files have been created, bundle them. 
    	if ($this->zipall || count($this->created_files)>1){
    		$this->zipall = true; 
    		$this->write_zip();
    	}
    	
    	if ($this->download) $this->force_download();
    	
    	if ($public) $this->publish();
    	
    	$this->clean_up();
    }
    	
    
    /**
     * 
     * Remove all files from the temporary directory 
     * and reset the create_files array. 
     */
    private function clean_up(){
    	foreach($this->created_files as $filepath) {
				@unlink($filepath);
		}
    }
    

    /**
     * 
     * Copies the written files into the export directory. 
     */
    private function publish(){
    	
    	//several files
    	if ($this->zipall){
    		//the zip archive should always be the last file created
    		$path_to_file = end($this->created_files); 
    		
    	//we have just one file
    	} else {
    		$path_to_file = $this->created_files[0];     		
    	}

    	//move the file to the export directory
    	if(!rename($path_to_file, $this->export_dir . basename($path_to_file) ) ) {
      		throw new Exception ("Export error: files could not be published in export directory!");
    	}	
    }

    
    
    /**
     * 
     * Offers the exported files for download to the user. 
     * @throws Exception
     */
    private function force_download(){
    	
    	$path_to_file = null; 
    	
    	//several files
    	if ($this->zipall){
    		header('Content-Type: application/zip');
    		
    		//the zip archive should always be the last file created
    		$path_to_file = end($this->created_files); 
    		
    		
    	//we have just one file
    	} else {
    		$path_to_file = $this->created_files[0]; 
    		//set the filename and the content type, now based on export format. 
    		$this->filename = basename($path_to_file); 
			header('Content-Type: text/'.$this->format); //sofar csv | xml
			    			    		
    	}
    	
    	//rest of the headers
    	header('Content-Disposition: attachment;filename='.$this->filename);
    	header('Last-Modified: '.date(DATE_RFC822));
		header('Pragma: no-cache');
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: '. date(DATE_RFC822, time() - 3600));
		header("Content-Length: " . filesize($path_to_file));

	    readfile($path_to_file);
        	
    }
    
    
	/**
	 * Bundle all created files into zip archive. Either can be done by setting @zipall to true
	 * or happens automatically if more than one file has been created during export. 
	 */    
    private function write_zip(){

    		if (pathinfo($this->filename, PATHINFO_EXTENSION) != 'zip') {
	   	 		$this->filename = $this->filename . '.zip';
    		}
    		
    		$destination = $this->tmp_dir . "/" . $this->filename; 
    		
    		if (create_zip($this->created_files, $destination,true)){ 
	    		array_push($this->created_files, $destination);

    		} else {	
    			throw new Exception("Export exception: could not create {$destination} zip archive!");
    		}
    }
    
    
    
    
    /**
     * 
     * Returns the CSV file for the export data and creates local public copy in 
     * export directory. 
     */
    private function write_csv(){

    	global $firephp; 
    	
    	    		
    	$this->format = 'csv';
    	    	
    	foreach ($this->xml_result as $filename => $xml_result_str) {
    	
			$firephp->log($filename, "the file name");
    		
			if (pathinfo($filename, PATHINFO_EXTENSION) != 'csv') {
	   	 		$filename = $filename . '.csv';
    		}

	    	//convert the xml result to csv
    		if ($xml_result_str == null || strlen($xml_result_str)==0){
    			throw new Exception("Export exception: Empty dataset. Nothing to be exported!");
    		}
    	
	    	//convert to csv
    		$csv_result = $this->xml2csv($xml_result_str); 
    	
    		//create the file 
    		$this->write_file($csv_result, $filename);
	    	
    	}
    	
    }
    
    
    
    
    /**
     * 
     * Returns the xml version of the data to be exported. Creates a local copy if publish=true
     * @param boolean $download download file to client can be turned off
     * @throws Exception
     */
    private function write_xml(){
    	
    	$this->format = 'xml';
		
    	
    	foreach ($this->xml_result as $filename => $xml_result_str) {
    	
    	
	    	if (pathinfo($filename, PATHINFO_EXTENSION) != 'xml') {
		    	$filename = $filename . '.xml';
	    	}
	    	
			//any results?
	    	if ($xml_result_str == null || strlen($xml_result_str)==0){
	    		throw new Exception("Export exception: Empty dataset. Nothing to be exported!");
	    		exit; 
	    	}
	    	
	    	//create the file 
    		$this->write_file($xml_result_str, $filename);
	    	

    	}    	
    }
    
    
    
    
    
    /**
     * 
     * Exports data to google drive. 
     * Uses the Zend Gdata library to do this which is "slightly" outdated as Google API has moved on to v3. 
     * If this is really a feature that will be used the whole thing should probably be rewritten using the
     * Google Drive Api @link https://developers.google.com/drive/quickstart-php
     * @param string $email google account authentication email
     * @param string $pwd	google account authentication pwd
     */
	private function write_gdrive($email, $pwd){
    	global $firephp; 
    	
        
    	$remove_local_copy = !$this->publish; 
    	
    	//create local copy 
    	$this->publish = true; 
 		$this->write_csv(false);
 		
 		//create file upload handler
 		$cd = new gDrive($email, $pwd);
 		
    	$file = $this->export_dir . $this->filename; 
    	$info = pathinfo($file);
		$file_name =  basename($file,'.'.$info['extension']);
    	
		//if file with same name exists, delete it first. 
		//otherwise a new copy will be created with the identical name?!
		//this could also work as long as we import then the latest version. 
		//maybe this should be set as an option.  
        if($gdrive_id = $cd->nameExists($file_name) ){
    		$cd->deleteDoc($gdrive_id);
    	}
    	
    	//make new copy... otherwise upload won't work. 
    	//Probably has to do with the headers of the connection, protocol version etc. 
    	$cd = new gDrive($email, $pwd);
        //upload to google
        $cd->uploadDocument($file, $this->filename);
        
        //restore true publish settings. 
		if($remove_local_copy){
			unlink($file);
		}
		$this->publish = !$remove_local_copy;	
            
    }
    
    
   
   /**
    * Writes the export data to the corresponding file into the temporary directory. 
    * @param string $data the export data, either csv rows or xml string.
    * @param string $filename the filename the data will be written to. 
    */
   private function write_file($data, $filename){ 	
    	
   		global $firephp; 
   	
		$publish_filename = $this->tmp_dir . $filename; 
	  	$outhandle = @fopen($publish_filename, 'w');

	  	if (!$outhandle)
	        	throw new Exception("Export exception. Could not open {$publish_filename} for writng. Make sure that local_config/tmp_dir is a writable directory");
	  
	        	
	    switch($this->format){
	    	case "csv":
	    		foreach ($data as $row) {
	      			fputcsv($outhandle, $row);
		  		}
		  		break;
		  		
	    	case "xml":
	    		fwrite($outhandle, $data);	
	    		break;
	    }
		  		
		fclose($outhandle);
		
		//$firephp->log($this->created_files, "created files array");
		//$firephp->log($publish_filename, "the published file name");
		
		
		//add the current file to the records for eventual zipping. 
		if (file_exists($publish_filename)) {
			array_push($this->created_files, $publish_filename);
		} else {
			throw Exception("Export exception: could not write file {$publish_filename}!");
		}
    }
    
    
    
       
    
    /**
     * 
     * Converst the xml result set into array of csv
     */
	protected function xml2csv($xml){
		
	    $fieldnames = array();
	    $csv_rows = array();
	    $tok = strtok($xml, '<>');
	    $expected = 'rowset';
	    if ($tok != $expected)
			throw new XMLParseException($expected, $tok, $xml);
	    $tok = strtok('<>');
	    $first_row = true;
	    while ($tok != '/rowset') {
			$ex = explode(' ', $tok);
			$fieldname = $ex[0];
			$expected = 'row';
			if ($fieldname != $expected)
			    throw new XMLParseException($expected, $fieldname, $xml);
			$tok = strtok('<>');
			$csv_row = array();
			while ($tok != '/row') {
			    $ex = explode(' ', $tok);
			    $fieldname = $ex[0];
			    if ($first_row)
				$fieldnames[] = $fieldname;
			    $tok = strtok('<>');
			    $expected = '![CDATA[';
			    $l_expected = strlen($expected);
			    if (substr($tok, 0, $l_expected) != $expected) 
					throw new XMLParseException($expected, $tok, $xml);
			    $value = substr($tok, $l_expected, strpos($tok, ']]', $l_expected)-$l_expected);
			    $csv_row[] = $value;
			    $tok = strtok('<>');
			    $expected = '/' . $fieldname;
			    if ($tok != $expected)
					throw new XMLParseException($expected, $tok, $xml);
			    $tok = strtok('<>');
			}
			$tok = strtok('<>');
			$first_row = false;
			$csv_rows[] = $csv_row;
	    }    
	    array_unshift($csv_rows, $fieldnames);
	    return $csv_rows;
	}
	
	
	protected function xml_add_metadata($metadata=null)
	{
	    $xml_out = 
		'<' . $this->export_table . '>'
		. '<timestamp>' 
		  . date('Y-m-d_h:i') 
		. '</timestamp>';
	    if (isset($metadata)) {
			$xml_out .= '<' . $metadata['name'] . '>';
			foreach($metadata['data'] as $key => $value) 
			    $xml_out .= '<' . $key . '>' . $value . '</' . $key . '>';
				$xml_out .= '</' . $metadata['name'] . '>';
	    	}
	    	$xml_out .= $this->xml_result
		. '</' . $this->export_table . '>';
	    
		$this->xml_result = $xml_out; 
		
	}
    
	

}



?>