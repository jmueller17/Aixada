<?php


require_once(__ROOT__ . 'php/lib/abstract_export_manager.php');



class export_order extends abstract_export_manager {

	
	//the order id 	
	protected $order_id = 0;
	
	protected $provider_id = 0; 
	
	protected $date_for_order = 0; 
	
	
	public function __construct($filename="", $order_id = 0, $provider_id=0, $date_for_order=0){

		
		$this->export_table = "aixada_order_item";
				
		
		if ($order_id > 0) {
			$this->order_id = $order_id;
		} else if ($provider_id > 0 && $date_for_order > 0){
			$this->provider_id = $provider_id; 
			$this->date_for_order = $date_for_order; 	
		} else {
			throw new Exception("Export order exeption: no order_id or provider_id / date specified!");	
			exit; 			
		}
		
		
		parent::__construct($filename);
	
	}
	

	
	protected function read_db_table(){
		$xml_tmp = "";
		
		
		$xml_tmp = stored_query_XML_fields('get_order_item_detail', $this->order_id, 0,$this->provider_id,$this->date_for_order,0); 

		$this->xml_result = $xml_tmp; 
		
		
		//$this->xml_add_metadata();
	
	}
	

}


?>