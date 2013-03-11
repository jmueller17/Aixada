<?php


require_once(__ROOT__ . 'php/external/FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start(); // Starts FirePHP output buffering
$firephp = FirePHP::getInstance(true);



require_once(__ROOT__ . 'php/lib/exceptions.php');
require_once(__ROOT__ . 'local_config/config.php');
require_once(__ROOT__ . 'php/inc/database.php');
require_once(__ROOT__ . 'php/lib/gdrive.php');

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
	protected $publish = true;  
	
	
	
	/**
	 * Directory where published export files will be saved
	 */
	protected $export_dir = null;
	
	
	
	/**
	 * 
	 * The xml string of the exported database table
	 * @var string
	 */
	protected $xml_result = null; 
	
	
	/**
	 * 
	 * Array holding the rows of the CSV
	 * @var array
	 */
	protected $csv_result = null;
	

	
	/**
	 * 
	 * The file name the export data will be saved to
	 * @var string
	 */
	protected $filename = "";
	
	

	
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
    
    
    
    public function export($publish=false, $format='csv', $email='', $pwd=''){

    	$this->publish = $publish;
    	
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
    	
    }
    
    
    
    /**
     * 
     * Returns the CSV file for the export data and creates local public copy in 
     * export directory. 
     */
    private function write_csv($download=true){
    	
    	$this->format = 'csv';
		if (pathinfo($this->filename, PATHINFO_EXTENSION) != 'csv') {
	   	 	$this->filename = $this->filename . '.csv';
    	}

    	//convert the xml result to csv
    	if ($this->xml_result == null || strlen($this->xml_result)==0){
    		throw new Exception("Export exception: Empty dataset. Nothing to be exported!");
    	}
    	
    	//convert to csv
    	$this->csv_result = $this->xml2csv(); 
    	
    	//publish to this web
    	$this->publish_copy();

    	if($download){
		 	//offer file for download
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment;filename='.$this->filename);
			header('Last-Modified: '.date(DATE_RFC822));
			header('Pragma: no-cache');
			header('Cache-Control: no-cache, must-revalidate');
			header('Expires: '. date(DATE_RFC822, time() - 3600));
			$fp = fopen('php://output', 'w');
			foreach ($this->csv_result as $row) 
			    fputcsv($fp, $row);
			fclose($fp);
    	}
    	
    }
    
    
    /**
     * 
     * Returns the xml version of the data to be exported. Creates a local copy if publish=true
     * @param boolean $download download file to client can be turned off
     * @throws Exception
     */
    private function write_xml($download=true){
    	
    	$this->format = 'xml';
	if (pathinfo($this->filename, PATHINFO_EXTENSION) != 'xml') {
	    $this->filename = $this->filename . '.xml';
    	}
    	
		//any results?
    	if ($this->xml_result == null || strlen($this->xml_result)==0){
    		throw new Exception("Export exception: Empty dataset. Nothing to be exported!");
    		exit; 
    	}
    	
    	//publish to public folder
    	$this->publish_copy();
    
		if($download){
		  	$newstr = '<?xml version="1.0" encoding="utf-8"?>';  
		  	$newstr .= $this->xml_result; 
		  	header('Content-Type: text/xml');
		  	header("Content-disposition: attachment; filename=\"".$this->filename."\""); 
		  	header('Content-Type: application/octet-stream');
		  	header('Last-Modified: '.date(DATE_RFC822));
		  	header('Pragma: no-cache');
		  	header('Cache-Control: no-cache, must-revalidate');
		  	header('Expires: '. date(DATE_RFC822, time() - 3600));
		  	header('Content-Length: ' . strlen($newstr));
		  	echo $newstr;
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
     * 
     * Creates a local public available copy of the export file for easy
     * sharing, importing by other platform.  
     */
    private function publish_copy(){
    	
    	if (!$this->publish) return false; 
    	
		$publish_filename = $this->export_dir . $this->filename; 
	  	$outhandle = @fopen($publish_filename, 'w');

	  	if (!$outhandle)
	        	throw new Exception("Export exception. Could not open {$publish_filename} for publishing to the web. Make sure that local_config/export is a writable directory");
	  
	    switch($this->format){
	    	
	    	case "csv":
	    		foreach ($this->csv_result as $row) {
	      			fputcsv($outhandle, $row);
		  		}
		  		break;
	    	case "xml":
	    		fwrite($outhandle, $this->xml_result);	
	    		break;	
	    	
	    }
		  		
		fclose($outhandle);
    }
    
    
    /**
     * 
     * Converst the xml result set into array of csv
     */
	protected function xml2csv(){
		
	    $fieldnames = array();
	    $csv_rows = array();
	    $tok = strtok($this->xml_result, '<>');
	    $expected = 'rowset';
	    if ($tok != $expected)
			throw new XMLParseException($expected, $tok, $this->xml_result);
	    $tok = strtok('<>');
	    $first_row = true;
	    while ($tok != '/rowset') {
			$ex = explode(' ', $tok);
			$fieldname = $ex[0];
			$expected = 'row';
			if ($fieldname != $expected)
			    throw new XMLParseException($expected, $fieldname, $this->xml_result);
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
					throw new XMLParseException($expected, $tok, $this->xml_result);
			    $value = substr($tok, $l_expected, strpos($tok, ']]', $l_expected)-$l_expected);
			    $csv_row[] = $value;
			    $tok = strtok('<>');
			    $expected = '/' . $fieldname;
			    if ($tok != $expected)
					throw new XMLParseException($expected, $tok, $this->xml_result);
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