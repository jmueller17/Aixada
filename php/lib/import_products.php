<?php


require_once(__ROOT__ . 'php/lib/abstract_import_manager.php');



class import_products extends abstract_import_manager {

	
	/**
	 * internal provider id to which products belong
	 * @var integer
	 */
	protected $provider_id = 0; 
	
	
	
	/**
	 * 
	 * Constructor of import manager specific for products. 
	 * @param int $provider_id requires the id of an existing provider to which products pertain
	 * @throws Exception
	 */
	public function __construct($data_table, $map=null, $provider_id){

		$db = DBWrap::get_instance();
	    $rs = $db->Execute('select id from aixada_provider where id=:1q', $provider_id);
		if ($rs->num_rows == 0){
			throw new Exception("Import error: can't find provider #{$provider_id} in database!" );
			exit;
		}
		$db->free_next_results();
		
		//the provider
		$this->provider_id = intval($provider_id);
		
		//every product needs the provider id
		$this->_db_insert_row_prefix = array("provider_id"=>$this->provider_id);
		
		
		//set field used for matching external and internal entries. 
		$this->_db_match_field = 'custom_product_ref';
		
		
		//no columns are matched manually; try automatic
		if (($map== null || count($map) == 1) && $data_table->is_match()){
			$map = array();
			
			$db = DBWrap::get_instance();
			$rs = $db->Execute('select * from aixada_product limit 1');
			$row = $rs->fetch_assoc();
			
			foreach($row as $key => $value){
				$map[$key] = $data_table->get_col_index($key); //assign the index 
			}
			$db->free_next_results();
			
		}
		
		parent::__construct('aixada_product', $data_table, $map);
	}
	
    /**
     * Deactivates all products from the provider, this method makes
     * it easier the deactivation prior to import.
     */
    public function deactivate_products() {
        $match_col_list = $this->get_match_col_values();
        if ($match_col_list != ''){
            $db = DBWrap::get_instance();
            $rs = $db->Execute(
                'SELECT id from aixada_product where active = 1'.
                ' and provider_id = '.$this->provider_id.
                ' and '.$this->_db_match_field.' not in ('.$match_col_list.')'
            );
            while ($row = $rs->fetch_array()){
                do_stored_query('change_active_status_product', 0, $row['id']);
            }
            $db->free_next_results();
        }
    }
	
	
	/**
	 * Populates two arrays, one with the custom_ref_ids that already exist in the database and the other with 
	 * the rows of the file to import that do not exist in the db.  
	 */
	protected function match_db_entries(){
		
		$db = DBWrap::get_instance();
		$match_col_list = $this->get_match_col_values();
		
		$_existing_rows = array();
		$sql = "select id, custom_product_ref from aixada_product where provider_id=$this->provider_id and custom_product_ref in (";
		if ($match_col_list != ''){
			$sql .= $match_col_list.")" ;
	    	$rs =  $db->Execute($sql);
	    	//which of the given entries do already exist in the db
	    	while ($row = $rs->fetch_array()){
	    		$_existing_rows[$row['id']] = $row['custom_product_ref']; 
	    	}
	    	$db->free_next_results();
		} 
   
    	return $_existing_rows;
    	
	}
	

    
	
}

?>
