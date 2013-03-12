<?php

require(__ROOT__ . 'php/external/spreadsheet-reader/php-excel-reader/excel_reader2.php');
require(__ROOT__ . 'php/external/spreadsheet-reader/SpreadsheetReader.php');

require_once(__ROOT__ . 'php/external/FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start(); // Starts FirePHP output buffering
$firephp = FirePHP::getInstance(true);

  /** 
   * @package Aixada
   */ 

require_once(__ROOT__ . 'php/lib/exceptions.php');
require_once(__ROOT__ . 'local_config/config.php');
require_once(__ROOT__ . 'php/inc/database.php');
require_once(__ROOT__ . 'php/lib/data_table.php');



/**
 * 
 * Abstract class to handle different file formats for import and export files. Each subclass
 * has to overwrite this abstract class for the specific file format in question.
 *
 * @package Aixada
 */
class abstract_import_manager {
  
	
	/**
	 * Destination table in the database for the import data
	 * @var string
	 */
	protected $_db_table = null;
	
	/**
	 * 
	 * Data table that holds the import data
	 * @var data_table
	 */
	protected $_import_data_table = null;
	
	/**
	 * The list of fields that are available for importing as speciied in config.php for each table. import_fields 
	 * contains those fields that can be imported and that have been mapped in the original data file.
	 * @var array 
	 */
	protected $_import_fields = array(); 
	
	
	/**
	 * Mapping of data columns to db-fields
	 * @var hash
	 */
	protected $_col_map = null;
	
	
	/**
	 * 
	 * Every subclass has to define the field in the database whose entries are used to match up
	 * the incomeing data rows. This is overwritten by the sub class. 
	 * @var string
	 */
	protected $_db_match_field = '';
	
	
	/**
	 * 
	 * Shortcut to the matching values of the original data table. 
	 * The match_col can be set once the data table and the db_match_field are known. 
	 * @var array
	 */
	protected $_match_col = array();
	
			
	/**
	 * Stores the index to the data table column that contains the values of the db match values. s 
	 * @var int
	 */
	protected $_match_col_index = null; 
	
	
	/**
	 * 
	 * array that stores the foreign keys for given import data table columns. 
	 * @var 2d array
	 */
	protected $_foreign_keys = array();

	
	/**
	 * 
	 * Abstract import manager class. Expects the name of the destination table in the database, the source data_table and 
	 * a map. 
	 * @param string $destination_table name of destination database table 
	 * @param data_table $data_table two dimensional array holding the parsed source data
	 * @param array $map Establishes the mapping of the data table columns to the database table columns. Format 
	 * should be array('db_table_field_name'=> data_col_index, ... )
	 */
    public function __construct($destination_table, $data_table, $map)
    {

    	//get the import rights for the db table and fields
		$import_rights = configuration_vars::get_instance()->allow_import_for; 

    	//check if destination table exists in allowed tables
    	if (array_key_exists($destination_table, $import_rights)){
    		$this->_db_table = $destination_table; 
    	} else {
    		throw new Exception("Import error: can't find table '{$destination_table}' in the list of allowed import destinations. Check your config.php file.");      	
    		exit;
    	}

    	
        if (count($data_table) == 0){
    		throw new Exception ("Import error: the data table is empty. Nothing to import!!");
    		exit; 
    	} else {
    		$this->_import_data_table = $data_table; 
    	}
    	
    	
    	//check which db table fields are available for importing and which ones are specified in the map. 
    	foreach ($import_rights[$this->_db_table] as $field => $value) {
    		if ($value == 'allow'){
    			if (isset($map[$field])){
	    			array_push($this->_import_fields, $field);
    			}
    		} else {
    			global $firephp; 
    			$firephp->log("Import warning: import to field '{$field}' is not allowed. Column will be ignored!");
    		}
    	}
    	
    	$this->_col_map = $map;
		
    	
        	
		if (count($this->_import_fields) == 0){
			throw new Exception("Import error: can't find any allowed fields for importing into table '{$destination_table}'. Check your config.php!  ");	
			exit; 
		}
		
		
		//the index of the column for matching table with db values
		$this->_match_col_index = $this->_col_map[$this->_db_match_field];
		
		//retrieves all rows of the data_table column against which the db entries are matched 
		$this->_match_col = $this->_import_data_table->get_col_as_array($this->_match_col_index);
		
		
		//should be unique values
		$dup = $this->_check_duplicates($this->_match_col);
		if (count($dup) > 0){
			throw new Exception ("Import error: unique reference required but empty/duplicate key found in table column '{$this->_db_match_field}': " . implode(",",$dup));
			exit; 
		}    	       	   
    	   
    } 
        
    
	/**
	 * 
	 * Executes the sequence of the import: 
	 * 		1: get existing rows for updating, 
	 * 		2: get new rows for insert, 
	 * 		3: construct rows and execute sql in each case
	 * @param boolean $append_new control insert behavior of new data rows
	 */
    public function import($append_new=false){
    	
    	//format array('db_id'=>'custom_ref', ...)
    	$update_ids = $this->match_db_entries();

    	//data table rows that do not match existing rows in the database  
    	$insert_ids = array_diff($this->_match_col, $update_ids);
    	
    	
    	if (count($update_ids) > 0){
    		$this->update_rows($update_ids);
    	}
    	
    	if ($append_new && count($insert_ids)){
    		$this->insert_rows($insert_ids);
    		
    	}

        
    }
    
    
    
    /**
     * Function to be overwritten by each subclass and that constructs an array with the already existing rows in the database
     * given a certain data table to be imported. 
     */
    protected function match_db_entries(){
    }
    
   

    /**
     * 
     * Constructs update rows for already existing entries in the corresponding database table. 
     * @param array $update_ids of the format array('id'=>'custom ref value',...)
     */
    protected function update_rows($update_ids){

    	$db = DBWrap::get_instance();

    	
    	foreach($update_ids as $id => $match_id){
    		
    		//retrieve row from import data table
    		$row = $this->_import_data_table->search_row($this->_match_col_index, $match_id);

    		//real db id to the row; required for the database wrapper update function
    		$db_update_row = array("id"=>$id);

    		
    		//take fields to be imported
    		foreach($this->_import_fields as $db_field){
    			//lookup its corresponding column in the import data table 	
    			$col_index = $this->_col_map[$db_field];

    			//add it to the import_row	
				$db_update_row[$db_field] = $row[$col_index];	
			}

			
			//do sql
			try {
				$db->Update($this->_db_table, $db_update_row);
			}  catch(Exception $e) {
    			header('HTTP/1.0 401 ' . $e->getMessage());
    			die ($e->getMessage());
			} 
    		
    		
    	} 
    }
    
    

    /**
     * 
     * Constructs array of db field => value pairs that can is passed to the dbWrapper-Insert function
     * @param array $insert_ids array of custom ref values that identify the rows to be inserted in the data table
     */
	protected function insert_rows($insert_ids){
    	$db = DBWrap::get_instance();
    	
    	foreach($insert_ids as $id => $match_id){
    		
    		//retrieve row from import data table
    		$row = $this->_import_data_table->search_row($this->_match_col_index, $match_id);

    		
    		$db_insert_row = array();

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
    
    
    /**
     * 
     * Utility function that checks if the matching column of the datatable contains duplicated entries. 
     * @param array $in_array array with unique values
     * @return array with duplicated values 
     */
    private function _check_duplicates($in_array){
   		//check for duplicate values in array
		$idcount = array_count_values($this->_match_col);
   		
		$duplicates = array();
		foreach($idcount as $id => $count ){
		    if( $count > 1 ){
		        array_push($duplicates, $id );
		    }
		}
		
		return $duplicates; 
    }
    
    
    
    /**
     * 
     * utility wrapper for parsing different uploaded files and returning a 2d array (data_table) with the values
     * @param string $path2File the full path to the file 
     */
    public static function parse_file($path2File, $db_table=''){
    	$rowc = 0;
  		$_data_table = null; 
  		$_header = false; 		

  		$extension = substr($path2File, -4);

  	 	if ($extension == '.xml') {

  	 		$xml = simplexml_load_file($path2File);

			foreach ($xml->children() as $row) {
				$values = array();
				$fieldnames = array();
				foreach($row->children() as $elem){
					$values[] = (string)$elem;
					$fieldnames[] = $elem->getName(); 
				}
				$_data_table[$rowc++] = $values; 
			}
			array_unshift($_data_table, $fieldnames);
			$_header=true; 
			global $firephp; 
			$firephp->log($_data_table, "xml table");
			
  		} else if (in_array($extension, array('.csv', '.tsv', '.txt', '.xlsx','.ods', '.xls'))) {
	 		$Reader = new SpreadsheetReader($path2File);
			foreach ($Reader as $Row){    
			  	$_data_table[$rowc++] = $Row; 
	
			}			
		
  		}
							
		return new data_table($_data_table, $_header, $db_table);
    }
    
    

} //end class abstract_import_manager






?>