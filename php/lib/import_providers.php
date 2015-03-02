<?php


require_once(__ROOT__ . 'php/lib/abstract_import_manager.php');



class import_providers extends abstract_import_manager {

		
	
	/**
	 * 
	 * Creates a new object for importing providers 
	 */
	public function __construct($data_table, $map=null){

		
		//the nif should be unique  
		$this->_db_match_field = 'nif';
		
		//if new providers will be created, need a responsible uf_id
		$this->_db_insert_row_prefix = array('table'=>'aixada_provider', 'responsible_uf_id'=>get_session_uf_id());
		
		
		//no columns are matched manually; try automatic
		if (($map== null || count($map) == 1) && $data_table->is_match()){
			$db = DBWrap::get_instance();
			$rs = $db->Execute('select * from aixada_provider limit 1');
			$row = $rs->fetch_assoc();
			
			$map = array();
			//construct automatically map db-field -> is available in col nr X of data table. 
			foreach($row as $key => $value){
				$map[$key] = $data_table->get_col_index($key); //assign the index 
			}
		}
		
		
		parent::__construct('aixada_provider', $data_table, $map);
	}
	
	
	
	/**
	 * Populates two arrays, one with the custom_ref_ids that already exist in the database and the other with 
	 * the rows of the file to import that do not exist in the db.  
	 */
	protected function match_db_entries(){
		$db = DBWrap::get_instance();
		$sql = "select id, nif from aixada_provider where nif in (";
		$match_col_list = $this->get_match_col_values();
		$_existing_rows = array();    	
		if ($match_col_list != ''){
			$sql .= $match_col_list .")";
    		$rs =  $db->Execute($sql);
	    	//which of the given entries do already exist in the db
	    	
	    	while ($row = $rs->fetch_array()){
	    		$_existing_rows[$row['id']] = $row['nif']; 
	    	}
    	}
    	return $_existing_rows;
	}
	
	
}

?>
