<?php


require_once(__ROOT__ . 'php/lib/abstract_export_manager.php');



class export_bill extends abstract_export_manager {
	

	protected $bill_ids = 0; 

	/**
	 *	return different types 
	 */
	protected $type = 1;



	/**
	 *	
	 *
	 */
	public function __construct($bill_ids=0, $type=1, $xml_result="", $filename=""){		
		

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

		$this->type = $type; 
		
		parent::__construct($filename, $xml_result);
	}
	


	/**
     *  Creates the accounting infor for this bill, including IVA groups, bill number, accountable number of member
     *  date, number, name and NIF of member. 
    */
	protected function read_db_table(){
		global $firephp;

		$xml_tmp = "";
		

 		$db = DBWrap::get_instance(); 

        $rs1 = $db->squery("get_bill_accounting_detail", $this->bill_id);
        $db->free_next_results();

        $rs2 = $db->squery("get_tax_groups", $this->bill_id);
        $db->free_next_results();


 		$of1 = new output_format_csv($rs1); 
        $csv1 = $of->format("array"); 


		$of1 = new output_format_csv($rs2); 
        $csv2 = $of->format("array"); 


        //format $rs1 + rs2 as csv array. construct new array  
		
		/*$xml_tmp = stored_query_XML_fields("get_bill_detail", 29);
		DBWrap::get_instance()->free_next_results(); 

		
		$this->xml_result[$this->filename] = $xml_tmp; 
		
		DBWrap::get_instance()->free_next_results(); */
		
	
	}


}


?>