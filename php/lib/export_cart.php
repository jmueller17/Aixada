<?php


require_once(__ROOT__ . 'php/lib/abstract_export_manager.php');



class export_cart extends abstract_export_manager {

	
	protected $cart_id = 0; 
	
	
	
	public function __construct($filename="", $cart_id = 0){
	
		$this->export_table = "shop_item";
	
		
		if ($cart_id == 0){
				throw new Exception("Export cart: no cart ID specified.");	
				exit; 
		}

		$this->cart_id = $cart_id; 
		
		parent::__construct($filename);
	
	}
	

	
	protected function read_db_table(){
		$xml_tmp = "";
		

    	$xml_tmp = stored_query_XML_fields('get_shop_cart', 0, get_session_uf_id(), $this->cart_id, 1);
    	
	
		$this->xml_result = $xml_tmp; 
		
		
		//$this->xml_add_metadata();
	
	}
	

}


?>