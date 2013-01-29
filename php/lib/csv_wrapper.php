<?php



/** 
 * @package Aixada
 */ 

require_once(__ROOT__ . 'php/lib/abstract_wrapper_format.php');



class csv_wrapper extends abstract_wrapper_format {
	  

	public $delimiter = "\t"; //single quotes does not work

	public $quote 	  = ""; 
	
	public $header 	= true; 
	
	
	
	public function __construct($uri, $header=true, $delimiter="\t", $quote="\'")
	{
		
		$this->delimiter = $delimiter;
		$this->quote = $quote;
		$this->header = $header;  
	    
	    parent::__construct($uri);
	}  
  
  
	
  public function parse(){
  	
  	
  	$row = 0;
  	$_data_table = null; 
	if (($handle = fopen($this->_uri, "r")) !== FALSE){
	    while (($data = fgetcsv($handle, 0, $this->delimiter)) !== FALSE) {
	    	$_data_table[$row++] = $data; 
	    }	    
	    fclose($handle);
	}

	return new data_table($_data_table, $this->header); 
	
  }
  
  
 
  
  
  
  
  
}

?>