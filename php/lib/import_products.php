<?php


require_once(__ROOT__ . 'php/lib/abstract_import_manager.php');



class import_products extends abstract_import_manager {

	
	protected $provider_id = 0; 
	
	
	
	
	/**
	 * 
	 * Constructor of import manager specific for products. 
	 * @param int $provider_id requires the id of an existing provider to which products pertain
	 * @throws Exception
	 */
	public function __construct($data_table, $map=null, $provider_id){

		$db = DBWrap::get_instance();
		
		//check if provider exists
		try {
	    	$rs = $db->Execute('select id from aixada_provider where id=:1q', $provider_id);
    	} catch(Exception $e) {
    		header('HTTP/1.0 401 ' . $e->getMessage());
    		die ($e->getMessage());
		}  
		
		if ($rs->num_rows == 0){
			throw new Exception("Import error: can't find provider #{$provider_id} in database!" );
			exit;
		}
		
		$db->free_next_results();

		
		
		$this->provider_id = $provider_id;
		
		
		
		$this->_db_match_field = 'custom_product_ref';
		
		
		parent::__construct('aixada_product', $data_table, $map);
		
	}
	
	
	
	/**
	 * Populates two arrays, one with the custom_ref_ids that already exist in the database and the other with 
	 * the rows of the file to import that do not exist in the db.  
	 */
	protected function match_db_entries(){
		
		$db = DBWrap::get_instance();

		
		$checkIds = $this->_import_data_table->get_col_as_array($this->_match_col_index);
		
		$sql = "select id, custom_product_ref from aixada_product where provider_id=$this->provider_id and custom_product_ref in (";
		foreach($checkIds as $id){
			$sql .= $id . ",";
		}		
		$sql = rtrim($sql, ",") .")";
	
		
    	$rs =  $db->Execute($sql);
    	
    	
    	$_existing_rows = array();    	
    	
    	while ($row = $rs->fetch_array()){
    		$_existing_rows[$row['id']] = $row['custom_product_ref']; 
    	}
    	
    	return $_existing_rows;
    	
	}
	

    
	protected function insert_rows($insert_ids){
    	$db = DBWrap::get_instance();
		
		global $firephp; 
    	
    	foreach($insert_ids as $id => $match_id){
    		
    		//retrieve row from import data table
    		$row = $this->_import_data_table->search_row($this->_match_col_index, $match_id);

    		
    		$db_insert_row = array("provider_id"=>$this->provider_id);

    		//take fields to be imported
    		foreach($this->_import_fields as $db_field){
    			//lookup its corresponding column in the import data table 	
    			$col_index = $this->_col_map[$db_field];

    			//add it to the import_row	
				$db_insert_row[$db_field] = $row[$col_index];	
			}

			
			//do sql
			try {
				$db->Insert($this->_db_table, $db_insert_row);
			}  catch(Exception $e) {
    			header('HTTP/1.0 401 ' . $e->getMessage());
    			die ($e->getMessage());
			} 
    		
    		
    	}  
    }
	

	
	

}


?>