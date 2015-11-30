<?php


require_once(__ROOT__ . 'php/lib/abstract_import_manager.php');



class import_dates4products extends abstract_import_manager {

	
	/**
	 * internal provider id to which products belong
	 */
	protected $provider_id = 0; 
	
	
	/**
	 * 
	 * matches the db of the product table to the export file format
	 * @var array
	 */
	private $auto_map = array('name' => 'name',
								'description' => 'description',
								'barcode' => '',
								'custom_product_ref' => 'custom_product_ref',
								'active' => 'active',
								'responsible_uf_id' => 'responsible_uf_id',
								'orderable_type_id' => 'orderable_type',
								'order_min_quantity' => 'order_min_quantity',
								'category_id' => 'category',
								'rev_tax_type_id' => 'rev_tax_type',
								'iva_percent_id' => 'iva_percent_type',
								'unit_price' => 'unit_price',
								'unit_measure_order_id' => 'unit_measure_order',
								'unit_measure_shop_id' => 'unit_measure_shop',
								'stock_min' => 'stock_min',
								'stock_actual' => 'stock_actual',
								'description_url' => 'description_url',
								'picture' => 'picture',
								'ts' => 'ts');
	
	
	/**
	 * 
	 * Constructor of import manager specific for products_orderable_for_date. 
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
		$this->provider_id = $provider_id;
		
		//set field used for matching external and internal entries. 
		$this->_db_match_field = 'product_id';
		
		
		//no columns are matched manually; try automatic
		/*if (($map== null || count($map) == 1) && $data_table->is_match()){
			$map = array();
			//construct map automatically 
			foreach($this->auto_map as $dbfield => $colname){
				$map[$dbfield] = $data_table->get_col_index($colname); //assign the index 
			}
		}*/
		
		parent::__construct('aixada_product_orderable_for_date', $data_table, $map);
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
    	
    	//which of the given entries do already exist in the db
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