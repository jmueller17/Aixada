<?php

require_once(__ROOT__ . 'php'.DS.'inc'.DS.'name_mangling.php');

/** 
 * @package Aixada
 */ 

/**
 * The following class encapsulates an individual column in a table.
 * @param string $_field stores the SQL field name
 * @param string $_type stores the SQL type of the column, without length information.
 * @var int $_max_length is false or contains the length information 
 * @package Aixada
 * @subpackage General_Table_Management
 */
class table_col {
    private $_field, $_type, $_max_length;
    public function __construct ($field, $type) 
    {
	$this->_field = $field; 
	if (!strstr($type, '(')) {
	    $this->_type  = $type;
	    $_max_length = false;
	} else {
	    $this->_type = strtok($type, '(');
	    $this->_max_length = strtok(',)'); // this correctly parses "float(10,2)" -> 10 and "varchar(255) -> 255"
	}
    }

    public function get_field()
    {
	return $this->_field;
    }

    public function get_type()
    {
	return $this->_type;
    }

    public function get_max_length()
    {
	return $this->_max_length;
    }
}

/**
 * This is the base class that manages the foreign keys of tables.
 * @package Aixada
 * @subpackage General_Table_Management
 */
class foreign_key_manager {

  /**
   * @var string $_table_name stores the name internally
   */
  protected $_table_name;
  
  /**
   * @var array $_table_cols is an array $field => table_col 
   * @see table_cols
   */
  protected $_table_cols = array();

  /**
   * @var array $_keys is a hash of foreign keys of the form 
   *   ['key'] => ''    for keys that are not foreign keys
   *   ['foreign key'] => array('referred table', 'name of foreign index field', 'name of explanatory field' or '')
   */
  protected $_keys = array();
   
  /**
   * @var array $_key_cache stores the descriptive names of all foreign keys encountered, in the form
   *   ['foreign key'] => array(key_val => descriptor)
   */
  protected $_key_cache = array();

  /**
   * @var array $_reverse_key_cache stores the descriptive names of all foreign keys encountered, in the form
   *   ['foreign key'] => array(descriptor => key_val)
   */
  protected $_reverse_key_cache = array();

  /**
   * @var string $_primary_key is the SQL name of the primary key 
   */
  protected $_primary_key;

  /**
   * @var bool $_primary_key_unique is true if the primary key is UNIQUE, 
   * false if it is auto_increment
   */
  protected $_primary_key_unique = false;

  /**
   * The constructor.
   * @param $table_name : The name of the table
   */

  public function __construct ($table_name)
  {
    $this->_table_name = $table_name;
    $this->_get_col_and_key_descriptions();
  }


  /**
   * @return array classes that define columns
   */
  public function get_table_cols()
  {
    return $this->_table_cols;
  }

  /**
   * @return string the name of the primary key
   */
  public function get_primary_key()
  {
    return $this->_primary_key;
  }

  /**
   * @return array the foreign keys
   */
  public function get_keys()
  {
    return $this->_keys;
  }

  /**
   * @return string the name
   */
  public function get_table_name()
  {
    return $this->_table_name;
  }

  /**
   * Called upon initialization to process the definition in the database
   */
  private function _get_col_and_key_descriptions ()
  {
      $db = DBWrap::get_instance();
      $rs = $db->Execute('SHOW CREATE TABLE :1', $this->_table_name);
      if (!$rs) throw new InternalException("Could not retrieve table description for " . $this->_table_name);
      $row = $rs->fetch_assoc();
      $rs->free();
      $desc = $row['Create Table'];
      $this->_get_col_descriptions($desc);
      $this->_get_key_descriptions($desc);
   }

  /**
   *  Read the columns from the table, and construct the
   *  encapsulating objects. 
   * @param string $desc the description returned by SHOW CREATE TABLE.
   * Example description:
   * CREATE TABLE `aixada_member` (
   *   `id` int(11) NOT NULL,
   *   `uf_id` int(11) NOT NULL, ...
   */
  private function _get_col_descriptions ($desc)
  {
    $da = preg_split("/,+\s+/", $desc);
    $da[0] = substr($da[0], strpos($da[0], '(') + 1); // remove CREATE TABLE
    foreach ($da as $fieldstr) {
      $parts = explode('`', $fieldstr);
      if (strpos($parts[0], 'PRIMARY') !== false)
	break;
      $field = $parts[1];
      $type = trim($parts[2]);
      $pp = strpos($type, ')');
      if ($pp !== false)
	$type = substr($type, 0, $pp+1);
      $this->_table_cols[$field] = new table_col($field, $type);      
    }
  }

  private function _set_primary_key ($key)
  {
    $tmp = explode('`', $key);
    $this->_primary_key = $tmp[1]; // the first element is the primary key
    $db = DBWrap::get_instance();
    $result = $db->Execute('SELECT * FROM :1 LIMIT 1', $this->_table_name);
    if (!$result) throw new InternalException('Could not read table ' . $this->_table_name);
    $flags = $result->fetch_field_direct(0)->flags;
    if (!($flags & MYSQLI_AUTO_INCREMENT_FLAG))
      $this->_primary_key_unique = true; // true means it's unique, but not auto_increment
  }

  /**
   * Parse one foreign key part of the column description. Examples are
   * keys[3] = '\n CONSTRAINT `aixada_member_ibfk_1` FOREIGN KEY (`uf_id`) REFERENCES `aixada_uf` (`id`)' 
   *
   * keys[4] = '\n CONSTRAINT `aixada_member_ibfk_2` FOREIGN KEY (`committee_id`) REFERENCES `aixada_committee` (`id`) ) ENGINE=InnoDB AUTO_INCREMENT=5

   */
  private function _parse_foreign_key($keystr)
  {
    $keystr_array = explode('`', $keystr);
    if (!strcmp(substr($keystr_array[0], 0, 3),  'KEY') or
	!strcmp(substr($keystr_array[0], 0, 10), 'UNIQUE KEY')) {  
      // it's a key
      $this->_keys[$keystr_array[1]] = '';
    } else if (!strcmp(substr($keystr_array[0], 0, 10), 'CONSTRAINT')) { 
      // it's a foreign key.
      $key    = $keystr_array[3];
      $fTable = $keystr_array[5];
      $fIndex = $keystr_array[7];
      $fDField = $this->_get_foreign_description_field($fTable); 
      // Answer is either a fieldname referencing a meaningful 
      // description from the other table, or ''.
      if ($fDField) {
	$this->_keys[$key] = array($fTable, $fIndex, $fDField);
	$this->_read_foreign_keys($key, $fTable, $fIndex, $fDField);
      }
    } else throw new InternalException("Something unexpected in the syntax of SHOW CREATE TABLE");
  }

  /**
   * Parse the syntax of the SHOW CREATE TABLE statement to extract keys and foreign keys.  
   * We only keep those foreign keys whose foreign table has a field
   * called 'name' or 'description', so that we can substitute the
   * foreign key value for the value of that field in the queries.
   * Example for aixada_members: 
   *
   * keys[0] = 'PRIMARY KEY(`id`)' 
   *
   * keys[1] = '\n KEY `uf_id` (`uf_id`)'
   *
   * keys[2] = '\n KEY `committee_id` (`committee_id`)' 
   *
   * keys[3] = '\n CONSTRAINT `aixada_member_ibfk_1` FOREIGN KEY (`uf_id`) REFERENCES `aixada_uf` (`id`)' 
   *
   * keys[4] = '\n CONSTRAINT `aixada_member_ibfk_2` FOREIGN KEY (`committee_id`) REFERENCES `aixada_committee` (`id`) ) ENGINE=InnoDB AUTO_INCREMENT=5
   * DEFAULT CHARSET=utf8'
   * @see _table_name
   * @see _primary_key
   * @see _key_cache
   * @see _reverse_key_cache
   * @see _primary_key_unique
   */
  private function _get_key_descriptions($desc)
  {
    $pos = strpos($desc, 'PRIMARY KEY');
    $keys = array();
    if ($pos!==false) {
	/*
	  Primary key found in table ' . $this->_table_name . '. with description=' . $desc);
	*/
	$keys = explode('  ', substr($desc, $pos)); 
	$this->_set_primary_key($keys[0]);
	array_shift($keys);   // we already know about the primary key, so let's delete it
    } else {
	$keys = explode('  ', substr($desc, strpos($desc, 'KEY')));
    }
    foreach ($keys as $keystr) {
      $this->_parse_foreign_key($keystr);
    }

  }



  /**
   * Find out if there exists a field in the table called 'name' or
   * 'description'. Such a field will be used to substitute key values
   * in other tables referring to this one.  
   * @param string $fTable the name of a table containing descriptions of foreign keys
   * @return string $col_name the name of the description field, or empty
   */
  private function _get_foreign_description_field($fTable)
  {
    $strSQL = 'SELECT COLUMN_NAME FROM information_schema.columns WHERE table_name=:1q AND column_name=:2q';
    $db = DBWrap::get_instance();
    $test_col_names = array('name', 'description', 'unit');
    foreach ($test_col_names as $col_name) {
      $rs = $db->Execute($strSQL, $fTable, $col_name);
      //      if (!$rs) throw new Exception("Could not execute column name query for table $fTable using $strSQL2");
      if ($rs && mysqli_num_rows($rs)) 
	return $col_name;
    }
    // If we're here, we haven't found any of the above names in the foreign table, so we use the $index directly
    return '';
  }


  /**
   * Fill the foreign key cache for a certain foreign key.
   * From each row in a foreign table, read index and description.
   * Store this as arrays 'description' => 'index' and 'index' => 'description'
   * @param string $key the name of the index field; for example, committee_id
   * @param string $fTable the foreign table; for example, aixada_committee
   * @param string $fIndex the index of the foreign table; id
   * @param string $fDField the descriptive foreign field; description
   *
   * @see _key_cache
   * @see _reverse_key_cache 
   */
  private function _read_foreign_keys($key, $fTable, $fIndex, $fDField)
  {
    $db = DBWrap::get_instance();
    $rs = $db->Select(array($fDField, $fIndex), $fTable, '', '');
    if (!$rs) throw new Exception("Could not read foreign key descriptions from table $fTable using $strSQL . Error: " . mysqli_error());
    //    if (!mysqli_num_rows($rs)) throw new Exception('No foreign keys found in table ' . $fTable);
    $cache = array();
    $rcache = array();
    while ($row = mysqli_fetch_assoc($rs)) {
		$tmp_field_val = $row[$fDField];
// 		if (stristr(PHP_OS, 'WIN')) {
// 			$tmp_field_val = utf8_decode($tmp_field_val);
// 		} 
	
      $cache[$row[$fIndex]] = $tmp_field_val;
      $rcache[$row[$fDField]] = $row[$fIndex];
    }
    $rs->free();
    $this->_key_cache[$key] = $cache;
    $this->_reverse_key_cache[$key] = $rcache;
  }



  /**
   * This function takes the name of a field and an associated
   * value. It checks if the field has a foreign key associated to it.
   * If this is the case, the value is converted to the description
   * stored in_key_cache .  Otherwise, the field value is
   * returned unchanged.  A boolean parameter in the returned array
   * keeps track of how to interpret the returned value.
   *
   * @param string $field contains the name of the field
   * @param mixed $value contains the value to be checked
   * @see _key_cache
   */
  protected function _get_field_value($field, $field_value) 
  {
    if ($this->_table_name != 'aixada_account_movement' and array_key_exists($field, $this->_key_cache)) { 
//       if (!array_key_exists($field_value, $this->_key_cache[$field]))
// 	throw new Exception('Key cache of field ' . $field . ' does not contain the key ' . $field_value);
      return array($this->_key_cache[$field][$field_value], true);
    }
    return array ($field_value, false);
  }


  /**
   * Look up the description of a row in a foreign key table and convert it back to an index.
   * @param string $field the name of the field carrying a foreign key, e.g. committee_id
   * @param mixed $value the description corresponding to the foreign key, e.g. Logistica
   * @param array_ref &$arrData the array in which the substitution should take place; this can get modified
   * @see _reverse_key_cache
   */
  protected function _convert_foreign_desc_to_index($field, $value, &$arrData)
  {
    if (!array_key_exists($value, $this->_reverse_key_cache[$field]))
      throw new Exception("Value $value does not exist in reverse key cache for table $this->_table_name");
    $arrData[$field] = $this->_reverse_key_cache[$field][$value];
  }

  /**
   * Traverse the @see _table_cols and build an SQL SELECT ALL query, in
   * which the fields with foreign key id's are replaced by the
   * corresponding descriptive names, according to @see _keys.
   * The arguments correspond to af="additional fields" that should be 
   * included in the query
   */
  public function make_canned_list_all_query ($af_tablenames, $af_names, $af_aliases, $af_join_clauses, $af_after_which_field)
  {
      global $Text;
    $select_clause = 'select';
    $join_clause = "\n    from " . $this->_table_name . ' ';
    list ($substituted_name, $substituted_alias, $table_alias) = 
      get_substituted_names($this->_table_name, array_keys($this->_table_cols), $this->_keys);

    foreach(array_keys($this->_table_cols) as $field) {
      if (isset($this->_keys[$field]) and $this->_keys[$field] != '') {
	list ($ftable_name, $ftable_id, $ftable_desc) = $this->_keys[$field];
        if ($substituted_alias[$field] == 'responsible_uf') {
            $select_clause
                .= "\n      "  //"concat('" . $Text['uf_short'] 
		. "aixada_uf.id as responsible_uf_id,\n"
		. "aixada_uf.name as responsible_uf_name,";
        } else if ($substituted_alias[$field] == 'uf') {
	    // add both the number and the name of the UF
	    $select_clause
		.= "\n      "
		. $this->_table_name . ".uf_id,"
                . "\n      " . $substituted_name[$field]
                . ' as uf_name,';
	} else {
            $select_clause 
                .= "\n      " . $this->_table_name . '.' . $field . ','
		. "\n      " . $substituted_name[$field]
                . ' as ' . $substituted_alias[$field] . ',';
        }
	$join_clause 
	  .= "\n    left join " . $ftable_name;
	$join_clause .= ' as ' . $table_alias[$field];
	$join_clause
	  .= ' on ' . $this->_table_name . '.' . $field 
	  . '=' . $table_alias[$field] . '.' . $ftable_id;
      } else {
	$select_clause .= "\n      " . $this->_table_name . '.' . $field . ',';
      }

      $qualified_field_name = $this->_table_name . '.' . $field;
      if (in_array($qualified_field_name, $af_after_which_field)) {
	  $i=0;
	  while ($af_after_which_field[$i] != $qualified_field_name) {
	      $i++;
	  }
	  $select_clause .= "\n      " . $af_tablenames[$i] . '.' . $af_names[$i]
	      . ' as ' . $af_aliases[$i] . ',';
	  $join_clause .= "\n    " . $af_join_clauses[$i];
      }
    }
    $select_clause = rtrim($select_clause, ',');
    return $select_clause . ' ' . $join_clause;
  }

}

/** 
 * This is the base class managing database access for all tables that have foreign keys.
 * @package Aixada
 * @subpackage General_Table_Management
 */
class table_with_ref extends foreign_key_manager {

  /**
   * The constructor should not be called directly.
   * Instead, use get_instance($table_name) of the descendant class.
   * @param $table_name : The name of the table
   */
  public function __construct ($table_name)
  {
    parent::__construct($table_name);
  }


  
  /**
   * Preprocess the data array to be sent to the database.
   * @param array &$arrData contains the data array to be sent to the database.
   * The steps are: 
   *
   * 1. Entries indexed by 'oper', 'table' and 'retype_password' are
   *    removed from $arrData.
   *
   * 2. If the array does not contain a value for the primary index,
   *    and primary index is declared UNIQUE but not AUTO_INCREMENT, 
   *    we find the next available index value.
   *
   * 3. Check if each field in the array exists in the database definition
   *
   * 4. If a foreign key occurs, we expect it to be in form of the
   *    description in the foreign table. For each $field => $value
   *    pair int the array, we check if $value occurs in
   *    $this->_key_cache [$field]. If so, we use that value;
   *    otherwise, we look up the $value in the foreign table and
   *    convert it back to the appropriate index.
   *
   * 5. Finally, the converted values are validated.
   *   
   * @see _get_next_free_key
   * @see _table_cols
   * @see _primary_key
   * @see _primary_key_unique
   * @see _key_cache
   * @see _reverse_key_cache
   * @see _validate
   */
  protected function _clean_and_validate_data(&$arrData)
  {
    if (!array_key_exists($this->_primary_key, $arrData) ||
	$arrData[$this->_primary_key] == '_empty')
      if ($this->_primary_key_unique) 
	$this->_get_next_free_key($arrData);
    $arrData = array_diff_key($arrData, array('oper' => 1, 'table' => 2, 'key' => 3, 'val' => 4, 'retype_password' => 5)); // we need the => 1 etc values for array_diff_key to work.
    foreach ($arrData as $field => $value) {
      if (!array_key_exists($field,  $this->_table_cols)) 
	throw new Exception('Field ' . $field . ' does not exist in database ' . $this->_table_name);
      if (array_key_exists($field, $this->_reverse_key_cache)
	  &&!array_key_exists($value, $this->_key_cache[$field])) 
	$this->_convert_foreign_desc_to_index($field, $value, $arrData);
      $strErr = $this->_validate($field, $value);
      if ($strErr) throw new Exception('Failed validation: ' . $strErr);
    }
  }

  /**
   *  @return the maximal valid primary key
   */
  public function get_max_key()
  {
    $db = DBWrap::get_instance();
    $strSQL = 'SELECT MAX(:1) FROM :2';
    $rs = $db->Execute($strSQL, $this->_primary_key, $this->_table_name);
    $row = mysqli_fetch_assoc($rs);
    return $row['MAX(' . $this->_primary_key . ')'];
  }


  /**
   *  This function gets called if the primary key of the table is
   *  declared UNIQUE instead of AUTO_INCREMENT. It looks for the next
   *  free key using a SELECT MAX query, and stores it in the
   *  referenced array.
   * @param array &$arrData contains the fields to be sent to the database
   */
  protected function _get_next_free_key(&$arrData)
  {
    $arrData[$this->_primary_key] = $this->get_max_key() + 1;
  }


  /**
   * Validate an entry of a field. Currently, only the length of an
   * entry corresponding to a VARCHAR(n) field is checked if it fits.
   * @param string $field the name of the field to check
   * @param mixed $value the entry to validate
   * @see table_col::get_type()
   * @see table_col::get_max_length()
   * @return string $strErr An error string or null
   */
  protected function _validate ($field, $value) 
  {
    $strErr = null;
    if ($this->_table_cols[$field]->get_type() == 'varchar') 
      if (strlen($value) > $this->_table_cols[$field]->get_max_length()) 
	$strErr = 'The value ' . $value . ' is too long for the field ' . $field;
    return $strErr;
  }

  /**
   * Checks if a given field contains a foreign key
   */
  public function is_foreign_key ($field)
  {
    return array_key_exists($field, $this->_key_cache);
  }

  /**
   * Retrieves the stored foreign key cache of a certain key field
   */ 
  public function get_key_cache ($field)
  {
    return $this->_key_cache[$field];
  }

  /**
   * Retrieves the stored reverse foreign key cache of a certain key field
   */ 
  public function get_reverse_key_cache ($field)
  {
    return $this->_reverse_key_cache[$field];
  }
}
?>