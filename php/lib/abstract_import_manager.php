<?php


require_once(__ROOT__ . 'FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start(); // Starts FirePHP output buffering
$firephp = FirePHP::getInstance(true);

  /** 
   * @package Aixada
   */ 

require_once(__ROOT__ . 'php/lib/exceptions.php');
require_once(__ROOT__ . 'local_config/config.php');
require_once(__ROOT__ . 'php/inc/database.php');


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
    			$firephp->log("Import warning: import to field '{$field}' is now allowed. Column will be ignored!");
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
			throw new Exception ("Import error: unique reference required but duplicate key found in table column '{$this->_db_match_field}': " . implode(",",$dup));
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
    

}


/**
 * 
 * Represents the data to be imported in a 2d array (table). Defines some common util functions
 * in order to retrieve all values of a certain column, but also specific functions that need
 * to be overwritten with respect to the values of the database. 
 * @author joerg
 *
 */
class data_table {
	
	protected $_data_table = null; 
	
	
	protected $_header = false; 
	
	
	private $_nr_rows = 0; 
	
	
	private $_nr_cols = 0; 
	

	/**
	 * 
	 * Table class to hold the import data in form of a two dimensional array. Defines some utility functions
	 * to operate on the data table. 
	 * @param array $data_table 2D representation of the data to be imported
	 * @param boolean $header indicates if the first row is interpreted as header containing column names
	 */	
	public function __construct($data_table, $header=false){
		
		if (count($data_table) == 0){
    		throw new Exception ("Import error: the data table is empty. Nothing to import!!");
    		exit; 
    	}
    	
		$this->_data_table = $data_table; 
		
		$this->_header = $header; 
		
		$this->_nr_rows = count($data_table);
		
		//returns always max nr. of cols??
		$this->_nr_cols = count($data_table[0]);
	}
	
	
	/**
	 * 
	 * Returns the row index of the data table by search for the given $needle in the specified column
	 * @param unknown_type $col_index
	 * @param unknown_type $needle
	 */
	public function search_row_index($col_index, $needle){
		$row_index = false; 
		//find the row in the data_table that corresponds to the current custom_ref field
		for($i=0; $i<$this->_nr_rows; $i++){
			
			if ($this->_data_table[$i][$col_index] == $needle){
				$row_index = $i; 
				break;
			}; 
		}
		return $row_index; 
	}
	
	
	public function search_row($col_index, $needle){
		$row_index = $this->search_row_index($col_index, $needle);
		
		if ($row_index !== false){
			return $this->_data_table[$row_index];	
		} else {
			return array(); 
		}
	}
	
	
	
	public function get_row($rindex){
		return $this->_data_table[$rindex];
	}
	
	
	
	/**
	 * 
	 * Returns the row values of the specified column of the table. 
	 * @param int or string $col_ref returns all values of the specified column. Either is the index (starting at 0) 
	 * or the name of the column. If a name is given as $col_ref then the data_table needs a header which contains column 
	 * names
	 */
	public function get_col_as_array($col_ref){
		$col_data = array(); 
		
		if (is_string($col_ref)){
			
			
			if (!$this->_header){
				throw new Exception("Import error: missing data table header; can't access columns by name!");	
				exit; 			
			}
						
			$index = array_search($col_ref, $this->_data_table[0]);
			
			//column name exists in data table
			if ($index === false){
				throw new Exception("Import error: specified column name '{$col_ref}' does not exist in data table");
				exit; 
			} else {
				$col_ref = $index;
			}
		
		}
		
		if (is_numeric($col_ref)){
			$c = 0; 
			$start = ($this->_header) ? 1:0; 
			for ($i = $start; $i < $this->_nr_rows; $i++){
				$col_data[$c++] = $this->_data_table[$i][$col_ref];
			}
		}
		
		return $col_data; 
	}
	
	/**
	 * 
	 * Returns the row-wise data of the specified column as a string joined by the separator
	 * @param mixed $col_ref the name of the column of the original data table
	 * @param unknown_type $separator
	 * @throws Exception
	 */
	public function get_col_as_string($col_ref, $separator=","){
		$col_data = $this->get_col_as_array($col_ref);
		return implode($separator, $col_data);
	}
	

	/**
	 * 
	 * Returns the contents of the data_table as HTML table. 
	 * @param string $css an existing CSS rule to be applied to the table
	 */
 	public function get_html_table($css=''){
	  	//global $firephp; 
	    //$firephp->log($this->_data, "data");
	    
	  	$table = '<table class="'.$css.'">';
	  	for ($r=0; $r<$this->_nr_rows; $r++){
	  		$table .= '<tr>';
	  		for ($c=0; $c<$this->_nr_cols; $c++){
	  			$table .= '<td>'.$this->_data_table[$r][$c] .'</td>';
	  			
	  		}
	  		$table .= '</tr>';
	  	}
	  	$table .= '</table>';
	
	  	return $table; 
 	}
 	
		
}



?>