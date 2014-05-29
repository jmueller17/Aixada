<?php


require_once(__ROOT__ . 'php/lib/abstract_export_manager.php');



class export_products extends abstract_export_manager {

	
	
	/**
	 * 
	 * Array of product ids. If id is int and 0, all active products for given provider will be exported
	 * If id is numeric and > 0 then only the partiular provider. 
	 */
	protected $product_ids = 0;

	
	/**
	 * the provider of products
	 */
	protected $provider_id = 0;
	
	
	
	
	
	public function __construct($filename="", $provider_id = 0, $product_ids = 0){
		global $firephp; 
		
		$this->export_table = "product";
		
		//get one product
		if (is_numeric($product_ids) && $product_ids > 0){
			$this->product_ids = array($product_ids);

		//get several selected products
		} else if (is_array($product_ids)){
			if (count($product_ids)==0){
				throw new Exception("Export products exception: missing ID(s) for products!");
				exit; 
			}
			$this->product_ids = $product_ids; 
				
		} else {
			$this->products_ids = 0;
			
		}
		
		if ($provider_id > 0) {
			$this->provider_id = $provider_id; 
		}
		
		
		if ($provider_id == 0 && $product_ids==0){
				throw new Exception("Export products exeption: no product ids and no provider id specified! Do either of both!");	
				exit; 
		}
	
		$firephp->log($provider_id, "the product provider_id ");
		
		parent::__construct($filename);
	
	}
	

	
	protected function read_db_table(){
		$xml_tmp = "";
		
		
		if ($this->provider_id > 0 && $this->product_ids == 0) {
			$xml_tmp = stored_query_XML_fields('aixada_product_list_all_query', 
						   'aixada_product.name', 'asc', 0, 1000000, 
						   'aixada_product.provider_id = ' . $this->provider_id);
		
		//get specified products	
		} else {			
			
			$ids_str = '('; 
			foreach ($this->product_ids as $id) {
				$ids_str .= $id.',';
			}
			$ids_str = rtrim($ids_str,',') . ')';
			
			$xml_tmp = stored_query_XML_fields('aixada_product_list_all_query', 
						   'aixada_product.name', 'asc', 0, 1000000, 
						   'aixada_product.id in ' . $ids_str);
		
			
		}
		
		//global $firephp; 
		//$firephp->log($this->xml_result, "the product result");
		
		$this->xml_result[$this->filename] = $xml_tmp; 
		
		DBWrap::get_instance()->free_next_results(); 
		
		
		//$this->xml_add_metadata();
	
	}
	

}


?>