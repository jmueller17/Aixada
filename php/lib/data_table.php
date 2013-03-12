<?php

require_once(__ROOT__ . 'php/lib/exceptions.php');
require_once(__ROOT__ . 'local_config/config.php');
require_once(__ROOT__ . 'php/inc/database.php');


/**
 * 
 * Represents the data to be imported in a 2d array (table). Defines some common util functions
 * in order to retrieve all values of a certain column, but also specific functions that need
 * to be overwritten with respect to the values of the database. 
 * @author joerg
 *
 */
class data_table {
	
	/**
	 * 2d data matrix 
	 */
	protected $_data_table = null; 
	
	
	/**
	 *  
	 * if the first row of the data table holds the table header (usually table column names)
	 * @var boolean
	 */
	protected $_header = false; 
	
	
	/**
	 * 
	 * Total number of rows
	 * @var int
	 */
	private $_nr_rows = 0; 
	
	
	/**
	 * 
	 * Total number of columns 
	 * @var int
	 */
	private $_nr_cols = 0; 
	
	
	/**
	 * 
	 * Indicates if the column names of the data to be imported matches the db field names. 
	 * This gets checked by the subclasses. Set to true, we assume that the file to be imported 
	 * comes from another aixada db (export). 
	 * @var boolean
	 */
	protected $_global_match = false; 
	
	
	/**
	 * 
	 * The reference data table in the database. This is needed to check if data_table column names match the db
	 * @var string The name of the matching aixada database table name
	 */
	protected $_db_table = '';
	

	
	
	/**
	 * 
	 * Table class to hold the import data in form of a two dimensional array. Defines some utility functions
	 * to operate on the data table. 
	 * @param array $data_table 2D representation of the data to be imported
	 * @param boolean $header indicates if the first row is interpreted as header containing column names
	 */	
	public function __construct($data_table, $header=false, $db_table=''){
		
		if (count($data_table) == 0){
    		throw new Exception ("Import error: the data table is empty. Nothing to import!!");
    		exit; 
    	}
    	
		$this->_data_table = $data_table; 
		
		$this->_header = $header; 
		
		$this->_nr_rows = count($data_table);
		
		//returns always max nr. of cols??
		$this->_nr_cols = count($data_table[0]);

		//destination db table
		//check if destination table exists in allowed tables
		$import_rights = configuration_vars::get_instance()->allow_import_for; 
		
    	if (array_key_exists($db_table, $import_rights)){
    		$this->_db_table = $db_table; 
    	} else {
    		throw new Exception("Import error: can't find table '{$db_table}' in the list of allowed import destinations. Check your config.php file.");      	
    		exit;
    	}
		
		
		
		//is data possibily coming from other aixada platform?
		if ($this->_header && $this->_db_table != ''){
		
			$db = DBWrap::get_instance();
			$rs = $db->Execute('select * from '.$this->_db_table.' limit 1');
			$row = $rs->fetch_assoc();
		
			
			$exact_matches = 0; 
			$moreorless = 0; 
			$dbfieldstr = ""; 
			
			//get the exact matches
			foreach ($row as $key => $value){	
				$dbfieldstr .= $key; 
				if($exists = in_array($key, $this->_data_table[0])){
					$exact_matches++;
				}
    		}
    		$db->free_next_results();
    		
    		//and the more or less matches
    		foreach($this->_data_table[0] as $colname){
    			if (stripos($dbfieldstr, $colname) > 0){
					$moreorless++;
				}
    		}
    		
			$ratio1 = $exact_matches / count($this->_data_table[0]); 
			$ratio2 = $moreorless /  count($this->_data_table[0]);
			
		
    		//do this heuristically... most fields match means this is our table
			if ($ratio1 > .5 && $ratio2 > .8){
				$this->_global_match = true; 
			}	
		}
	}//end constructor
	
	
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
	
	
	public function is_match(){
		return $this->_global_match;	
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
	 * Returns the index of a data table column specified by the header name
	 * @param string $col_name
	 */
	public function get_col_index($col_name){
		if (!$this->_header){
				throw new Exception("Import error: missing data table header; can't access columns by name!");	
				exit; 			
			}
						
		return array_search($col_name, $this->_data_table[0]);
	}

	/**
	 * 
	 * Returns the contents of the data_table as HTML table. 
	 * @param string $css an existing CSS rule to be applied to the table
	 */
 	public function get_html_table($css=''){
	  	//global $firephp; 
	    //$firephp->log($this->_data, "data");
	    
	  	$table = '<table class="'.$css.'" isown="'.$this->_global_match.'">';
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
} //end class abstract_data_table




?>