<?php

/**
 * @package   Aixada
 */ 

define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(__FILE__))).DS);

ob_start(); // Starts FirePHP output buffering

// global abbreviations: 
// rs  =  mysqli result resource

/**
 * include files
 */

/*$slash = explode('/', getenv('SCRIPT_NAME'));
if (isset($slash[1])) {
    $app = getenv('DOCUMENT_ROOT') . '/' . $slash[1] . '/';
} else { // this happens when called by make
    $app = '';
}*/

require_once(__ROOT__ . 'php/lib/table_with_ref.php');

/**
 * The following class encapsulates a table.
 *
 * @param string $table_name the SQL name of the table
 *    @package    Aixada
 * @subpackage    General_Table_Management
 */
class table_manager extends table_with_ref
{
    /**
     *    The constructor.
     *
     * @see     table_with_ref
     * @see     get_instance
     * @param     $table_name : The name of the table
     */
     public function __construct ($table_name)
     {
       parent::__construct($table_name);
     }

  /*
      * Call this function to retrieve an instance of the class.  The
      * class is defined static, so that only one instance for each table
      * has to be created. This saves database accesses.
      *
  public static function get_instance ($table_name)
  {
    if (!array_key_exists($table_name, self::$_managed_tables)) {
      self::$_managed_tables[$table_name] = new table_manager($table_name);
    }
    return self::$_managed_tables[$table_name];
  }
  */

  /** return an empty form, which the jquery/user then fills in to
      *  create a new row.
      */
  public function get_empty()
  {
    $strXML = '<' . $this->_table_name .'_colnames>';
    foreach($this->_table_cols as $col) {
      $f = $col->get_field();
      $strXML .= '<field name="' .$f . '">';
      if (array_key_exists($f, $this->_reverse_key_cache)) {
	$strXML .= '<choices>';
	foreach($this->_reverse_key_cache[$f] as $field => $value)
	  $strXML .= "<c>$field</c>";
	$strXML .= '</choices>';
      }
      $strXML .= "</field>";
    }
    $strXML .= '</' . $this->_table_name .'_colnames>';
    return $strXML;
  }


  /**
      * Create a new row.
      * @param array $arrData is an array { field_i => value_i }
      * @return int the new unique key
      */
  public function create ($arrData = array())
  {
    $this->_clean_and_validate_data($arrData);
    $db = DBWrap::get_instance();
    $db->Insert($this->_table_name, $arrData);
    if (array_key_exists($this->_primary_key, $arrData))
      return $arrData[$this->_primary_key];
    else { // the primary key was auto_increment
      $rs = $db->Execute('SELECT LAST_INSERT_ID()');
      $row = mysqli_fetch_assoc($rs);
      return $row['LAST_INSERT_ID()'];
    }
  }


  /**
      * The general query function.
      * @param string $filter the filter to be applied
      * @param string $order_by an optional index field
      *
      * @param array $fields an optional array of fields to be
      * returned. All entries that do not correspond to a field name in
      * the actual table are stripped away before sending the query to
      * the database.
      *
      * @return mysqli_result $rs the query result
      */
  public function list_all ($args)
  {
    //    global $firephp;
    $fields = $args['fields'];
    if ($fields !== array('*')) {
      // We query only those fields that are actually present in the table.
      $fields = explode(',', $fields);
      $present_fields = array();
      foreach ($this->_table_cols as $col) 
	array_push($present_fields, $col->get_field());
      $fields = array_intersect($fields, $present_fields);
    }
    $db = DBWrap::get_instance();
    //    $firephp->log(1, 'before select');
	//$db->debug = true; 
    list($rs, $total_pages) 
      = $db->Select($fields, 
		    $this->_table_name, 
		    $args['filter'], 
		    $args['order_by'] ? $args['order_by'] : $this->_primary_index,
		    $args['order_sense'] ? $args['order_sense'] : 'asc',
		    $args['page'],
		    $args['limit']);
    //    $firephp->log($rs, 'after select');
    //    $firephp->log($total_pages, 'after select total pages');
    if (!$rs) throw new Exception('The statement $strSQL could not retrieve records from' . $this->_table_name . '<br/>' . mysqli_error());
    return array($rs, $total_pages);
  }	

  /**
      * Update a record.
      * @param array $arrData an associative array that contains the fields to 
      * be updated. This array must contain an entry indexed by a field called 
      * 'id'. The data is cleaned and validated before being sent to the database.
      * @see _clean_and_validate
      * @return mysqli_result $rs the result of the query
      */
  public function update ($arrData)
  {
    if (!array_key_exists('id', $arrData))
      throw new Exception('Table ' . $this->_table_name . ': Update needs "id" entry in the following data: ' . $arrData);
    $this->_clean_and_validate_data($arrData);
    $db = DBWrap::get_instance();
    $rs = $db->Update($this->_table_name, $arrData); 
    return $rs;
  }

  /**
      * Delete the record indexed by id.
      * @param int $id the unique index of the record to be deleted
      * @return mysqli_result $rs the result of the operation
      */
  public function delete($id) 
  {
    $strSQL = 'DELETE FROM :1 WHERE :2=:3q';
    $db = DBWrap::get_instance();
    $rs = $db->Execute($strSQL, $this->_table_name, $this->_primary_key, $id);
    return $rs;
  }

  /**
      * Function for querying a table via a unique identifier.
      * @param int $id the unique index value
      * @param string $fields optional list of fields to retrieve. Default value '*'
      * @return mysqli_result $rs the query result
      */
  public function get_by_id($id, $fields = '*')
  {
    $db = DBWrap::get_instance();
    $rs = $db->Select($fields, $this->_table_name, $this->_primary_key .'='.$id, '');
    if (!$rs) throw new Exception('The statement ' . $strSQL . ' could not retrieve records from ' . $this->_table_name . ' for given id: ' . $id . '<br/>' . mysqli_error());
    return $rs;
  }

  /**
      * Function for querying a table via come indexed field.
      * @param string $key the name of the index to search
      * @param int $val the unique index value
      * @param string $fields optional list of fields to retrieve. Default value '*'
      * @return mysqli_result $rs the query result
      */
  public function get_by_key($key, $val, $fields = '*')
  {
    $db = DBWrap::get_instance();
    $rs = $db->Select($fields, $this->_table_name, "$key=$val", '');
    if (!$rs) throw new Exception('get_by_key: could not retrieve records from ' . $this->_table_name . ' for given key name ' . $key . ' and value ' . $val . '<br/>' . mysqli_error());
    return $rs;
  }

  /**
      * Write the fields in the row $row as XML
      * @param mysqli_row $row the row to be converted to XML
      * @return string $strXML the XML string
      */
  public function row_to_XML($row) 
  {
    $strXML .= '<' . $this->_table_name . '_row>';
    foreach ($row as $field => $value) {
      if ($value) { 
	list ($conv_value, $looked_up_value) = $this->_get_field_value($field, $value);
// 	if (stristr(PHP_OS, 'WIN')) {
// 		$conv_value = utf8_encode($conv_value);
// 	} 
	$strXML .= '<' . $field
	  . (($looked_up_value==true) ? ' attrib = "looked_up"' : '')
	    . '><![CDATA[' . $conv_value . '>>]</' . $field . '>';
      }
    }
    $strXML .= '</' . $this->_table_name . '_row>';
    return $strXML;
  }

  /**
      * Write the fields in the row set $rs as XML
      * @param mysqli_result $rs the row set to be converted to XML
      * @return string $strXML the XML string
      */
  public function rowset_to_XML($rs) 
  {
    $strXML = '<' . $this->_table_name . '_rowset>';
    if ($rs) {
      while ($row = $rs->fetch_assoc()) 
	$strXML .= $this->row_to_XML($row);
      $rs->free();
    }
    $strXML .= '</' . $this->_table_name . '_rowset>';
    return $strXML;
  }
  
  public function row_to_jqGrid_XML($row)
  {
//     global $firephp;
//     $firephp->log($row, 'row');
    $strXML = '<row id="' . $row['id'] . '">';
    foreach ($row as $field => $value) {
      list ($conv_value, $looked_up_value) = $this->_get_field_value($field, $value);
//       if (stristr(PHP_OS, 'WIN')) {
// 	$conv_value = utf8_encode($conv_value);
//       } 
      $strXML .= '<' . $field . ' f="' . $field . '"><![CDATA[' . $conv_value . "]]></$field>";
      //      $firephp->log($field, 'row_to_jqGrid_XML');
    }
    $strXML .= '</row>';
    return $strXML;
  }

  /**
      * @param mysqli_recordset $rs the result
      * @param int $page the requested page of the result set
      * @param int $limit the number of rows to be written to the table
      */
  public function rowset_to_jqGrid_XML($rs, $total_entries=0, $page, $limit=0, $total_pages=0)
  {
    $strXML = '';
    if ($rs) {
      $strXML .= '<rowset>';
      if ($page)
	$strXML .= '<page>' . $page . '</page>'; 
      if ($total_pages)
	$strXML .= '<total>' . $total_pages . '</total>';
      $strXML .= '<records>' . $total_entries . '</records>';
      $strXML .= "<rows>";
      while ($row = $rs->fetch_assoc()) 
	$strXML .= $this->row_to_jqGrid_XML($row);
      $rs->free();
      $strXML .= "</rows>";
      $strXML .= "</rowset>";
    }
    return $strXML;
  }

  /**
      * @param string $name the function name
      * @param var the parameters to pass to the function
      */
  public function stored_query ()
  {
    $args = func_get_args();
//      global $firephp;
//      $firephp->log($args, 'stored_query args');
    $rs = do_stored_query($args);
    $count = mysqli_num_rows($rs);
    $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10;
    $total_pages =  ($count<=0) ? 0 : ceil($count/$limit);
    $page =  isset($_REQUEST['page']) ? $_REQUEST['page'] : -1;
    
    $strXML = $this->rowset_to_jqGrid_XML($rs, $page, $limit, $total_pages);
    DBWrap::get_instance()->free_next_results();
    return $strXML;
  }

}

?>