<?php


require_once(__ROOT__ . "php/lib/abstract_export_manager.php");
require_once(__ROOT__ . "php/lib/export_products.php");



class export_providers extends abstract_export_manager {

	
	
	/**
	 * 
	 * Array of provider ids. If id is int and 0, all active providers will be exported
	 * If id is numeric and > 0 then only the partiular provider. Otherwise, all providers
	 * contained in the array. 
	 * @var unknown_type
	 */
	protected $provider_ids = 0; 
	
	
	
	/**
	 * Should the products for each provider be exported as well?
	 */
	protected $export_products = false; 
	
	
	
	
	public function __construct($filename="", $provider_ids = 0, $export_products=false){

		
		$this->export_table = "provider";
		
		$this->export_products = $export_products; 
		
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
		
		global $firephp; 
		
		//retrieve all active providers
		if ($this->provider_ids == 0){
			$this->provider_ids = array();
			$xml_tmp = stored_query_XML_fields('aixada_provider_list_all_query', 
						   'aixada_provider.name', 
						   'asc', 0,  1000000, 
						   'aixada_provider.active=1');

			//extract provider ids eventually needed for exporting product lists of each
			$xmldoc = new SimpleXMLElement($xml_tmp);
			$resultado = $xmldoc->xpath('/rowset/row/@id');
			foreach ($resultado as $rows) {
    			array_push($this->provider_ids, (string)$rows->id);
			}

			
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
		
		DBWrap::get_instance()->free_next_results(); 
		
		//export products as well? 
		if ($this->export_products){
			
			//the exported providers
			$this->xml_result["providers"] = $xml_tmp; 
			
			//$firephp->log($this->provider_ids, "provider ids");
			
			//get the products of each provider
			foreach ($this->provider_ids as $id) {

				$ep = new export_products("products4pv_".$id, $id, 0);
		    	
				$this->xml_result = array_merge($this->xml_result, $ep->get_xml_results());

			}
			
			//make sure we bundle the provider csv and the products csv and download it as a zip archive. 
			$this->zipall = true;
			
			
		//no products are exported, so we just have the single, main file! 
		} else  {
			
			$this->xml_result[$this->filename] = $xml_tmp; 
		
		}
		
	
	}
	

}


?>