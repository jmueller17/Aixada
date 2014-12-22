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
            if ($row != null) { // Prevent if the db_table is empty, otherwise
                                // the first import (no data in the table) may 
                                // fail here.
                foreach ($row as $key => $value){	
                    $dbfieldstr .= $key; 
                    if($exists = in_array($key, $this->_data_table[0])){
                        $exact_matches++;
                    }
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
			
			global $firephp; 
			$firephp->log($ratio1."-".$ratio2, "nr of matches with dbfields");
		
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
	
	public function has_header(){
		return $this->_header; 
	}
	
	public function get_nrows(){
		return $this->_nr_rows; 	
	}
	
	public function get_nrcols(){
		return $this->_nr_cols; 
		
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
                if (isset($this->_data_table[$r][$c])) {
                    $table .= '<td>'.$this->_data_table[$r][$c] .'</td>';
                } else {
                    $table .= '<td></td>';
                }
	  			
	  		}
	  		$table .= '</tr>';
	  	}
	  	$table .= '</table>';
	
	  	return $table; 
 	}	

    /**
     *
     * Parses a datatable with a template. If template is not applicable returns
     * null.
     * @param string $template_name A teplate name defined in $import_templates
     * key of 'config.php' for table of this data_table instance (if table is ''
     * templates are not applicabes)
     * @return null|array Returns an array with three keys:
     *      'data' => data_table: New datatable after parse data.
     *      'map' => array: New map to apply to 'data' after parse 'data'.
     *      'template_options' => array: Options of this template.
     */
    public function parse_data($template_name) {
        $templates = get_import_templates($this->_db_table);
        if (isset($templates[$template_name])) {
            $import_template = $templates[$template_name];
            $map = array();
            if (isset($import_template['match_columns'])) {
                $match_columns = $import_template['match_columns'];
                $r0 = $this->_data_table[0];
                foreach ($match_columns as $key => $value) {
                    $value = strtolower($value);
                    for ($c=0; $c<$this->_nr_cols; $c++){
                        if (isset($r0[$c])) {
                            if (substr($value,0,1) == '/') {
                                if (preg_match($value, $r0[$c])) {
                                    $map[$key] = $c;
                                    break;
                                }
                            } else {
                                if($value == strtolower($r0[$c])) {
                                    $map[$key] = $c;
                                    break;
                                }
                            }
                        }
                    }
                }
                if (count($map) != count($match_columns)) {
                // There are some colums not matched.
                    return null;
                }
            }
            if (isset($import_template['forced_values'])) {
                $forced_values = $import_template['forced_values'];
                foreach ($forced_values as $key => $value) {
                    $map[$key] = -1;
                }
            }
            if (count($map)) {
                if (isset($import_template['required_fields'])) {
                    $required_fields = $import_template['required_fields'];
                } else {
                    $required_fields = array();
                }
                //Build a new data
                $data = array();
                $rowc = 0;
                $map_cols = count($map);
                //headers
                $data[$rowc++] = array_keys($map);
                //data
                for ($r = 1; $r < $this->_nr_rows; $r++) {
                    $row = array();
                    for ($c=0; $c<$map_cols; $c++){
                        $key = $data[0][$c];
                        $index = $map[$key];
                        if ($index >= 0) {
                            if (isset($this->_data_table[$r][$index])) {
                                $row[$c] = $this->_data_table[$r][$index];
                            } else {
                                $row[$c] = '';
                            }
                        } else {
                            $row[$c] = $forced_values[$key];
                        }
                        if (in_array($key, $required_fields) && $row[$c] == '') {
                            $row = null;
                            break;
                        }
                    }
                    if ($row) {
                        $data[$rowc++] = $row;
                    }
                }
                //Build a new map
                $data_map = array();
                for ($c = 0; $c < count($data[0]); $c++){
                    $data_map[$data[0][$c]] = $c;
                }
                if (isset($import_template['updatable_columns'])) {
                    $updatable_columns = $import_template['updatable_columns'];
                    $data_map_update = array();
                    for ($c=0; $c<count($updatable_columns); $c++) {
                        $col_name = $updatable_columns[$c];
                        $data_map_update[$col_name] = $data_map[$col_name];
                    }
                    $data_map = array(
                        'map_insert' => $data_map,
                        'map_update' => $data_map_update,
                    );
                }
                return array(
                    'data' => new data_table($data, true, $this->_db_table),
                    'map' => $data_map,
                    'template_options' => (
                        isset($import_template['options']) ?
                        $import_template['options'] :
                        array()
                    )
                );
            }
        }
        return null;
 	}
} //end class abstract_data_table




?>
