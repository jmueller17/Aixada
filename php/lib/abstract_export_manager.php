<?php


ob_start(); // Probably only needed for FirePHP(no longer used)

require_once(__ROOT__ . 'php/lib/exceptions.php');
require_once(__ROOT__ . 'local_config/config.php');
require_once(__ROOT__ . 'php/inc/database.php');

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