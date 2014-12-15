<?php


require_once(__ROOT__ . 'php/lib/abstract_export_manager.php');



class export_bill extends abstract_export_manager {
	

	protected $bill_ids = 0; 

	
	public function __construct($bill_ids=0, $xml_result="", $filename=""){		
		

		$this->export_table = "bill";


		//export one bill
		if (is_numeric($bill_ids) && $bill_ids > 0){
			$this->bill_ids = array($bill_ids);

		//export several bills
		} else if (is_array($bill_ids)){
			if (count($bill_ids)==0){
				throw new Exception("Export bill exception: missing ID(s) for bills!");
				exit; 
			}
			$this->bill_ids = $bill_ids; 
				
		} 
		
		parent::__construct($filename, $xml_result);
	}
	

	
	protected function read_db_table(){
		global $firephp;

		$xml_tmp = "";
		
		
		$xml_tmp = stored_query_XML_fields("get_bill_detail", 29);
		DBWrap::get_instance()->free_next_results(); 

		
		$this->xml_result[$this->filename] = $xml_tmp; 
		
		DBWrap::get_instance()->free_next_results(); 
		
	
	}


}


?>