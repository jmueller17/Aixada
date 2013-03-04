<?php


require_once(__ROOT__ . 'php/lib/abstract_export_manager.php');



class export_dates4products extends abstract_export_manager {

	

	/**
	 * the provider of products
	 */
	protected $provider_id = 0;
	
	protected $from_date = ''; 
	
	protected $to_date = ''; 
	
	
	
	
	
	public function __construct($filename="", $provider_id, $from_date='', $to_date=''){		
		
		$this->export_table = "product_orderable_for_date";

		
		if (!isset($provider_id) || $provider_id ==0){
			throw new Exception("Export orderable dates exception: no provider_id specified");
			exit;
		}
		
		$this->from_date = ($from_date=='')? date('Y-m-d', strtotime('now')):$from_date; 
		$this->to_date = ($to_date=='')? date('Y-m-d', strtotime('now +2 month')):$to_date; 
		
				
		$this->provider_id = $provider_id; 
		
		parent::__construct($filename);
	
	}
	

	
	protected function read_db_table(){
		$xml_tmp = "";
		
		
				
	    $xml_tmp = stored_query_XML_fields('get_orderable_products_for_dates',
			   $this->from_date, 
			   $this->to_date,
			   $this->provider_id);
		
		
		$this->xml_result = $xml_tmp; 
		
		$this->xml_metadata = array( 'name' => 'provider_and_range', 
					     'data' => array( 'provider_id' => $this->provider_id,
						    'from_date' => $this->from_date,
						    'to_date' => $this->to_date ));
		
	
	}
	

}


?>