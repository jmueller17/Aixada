<?php


require_once(__ROOT__ . 'php/lib/abstract_export_manager.php');



class export_providers extends abstract_export_manager {

	
	
	/**
	 * 
	 * Array of provider ids. If id is int and 0, all active providers will be exported
	 * If id is numeric and > 0 then only the partiular provider. Otherwise, all providers
	 * contained in the array. 
	 * @var unknown_type
	 */
	protected $provider_ids = 0; 
	
	
	
	
	public function __construct($filename="", $provider_ids = 0){

		
		$this->export_table = "provider";
		
		
		if (is_numeric($provider_ids) && $provider_ids > 0){
			$this->provider_ids = array($provider_ids);
			
		} else if (is_array($provider_ids)){
			if (count($provider_ids)==0){
				throw new Exception("Export exception: missing ID(s) for provider!");
				exit; 
			}
			$this->provider_ids = $provider_ids; 
		}
		
		parent::__construct($filename);
	
	}
	

	
	protected function read_db_table(){
		$xml_tmp = "";
		
		//retrieve all active providers
		if ($this->provider_ids == 0){
			$xml_tmp = stored_query_XML_fields('aixada_provider_list_all_query', 
						   'aixada_provider.name', 
						   'asc', 0,  1000000, 
						   'aixada_provider.active=1');
			
		//otherwise retrieve specified providers 
		} else {
			
			$ids_str = '('; 
			
			foreach ($this->provider_ids as $id) {
				$ids_str .= $id.',';
			}
			$ids_str = rtrim($ids_str,',') . ')';
			
			$xml_tmp .= stored_query_XML_fields('aixada_provider_list_all_query', 
					   'aixada_provider.name', 
					   'asc', 0,  1000000, 
					   'aixada_provider.id in '.$ids_str);
		
			
		}

		$this->xml_result = $xml_tmp; 
	
	}
	

}


?>