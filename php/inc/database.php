<?php

require_once(__ROOT__ .'php/external/FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start(); // Starts FirePHP output buffering
$firephp = FirePHP::getInstance(true);

/** 
 * @package Aixada
 */ 

/*$slash = explode('/', getenv('SCRIPT_NAME'));
if (isset($slash[1])) {
	$app = getenv('DOCUMENT_ROOT') . '/' . $slash[1] . '/';
} else { // this happens when called by make
    $app = '';
}*/

require_once(__ROOT__ . 'local_config'.DS.'config.php');
require_once(__ROOT__ . 'php'.DS.'utilities'.DS.'general.php');
require_once(__ROOT__ . 'php'.DS.'lib'.DS.'exceptions.php');

if (!isset($_SESSION)) {
    session_start();
 }


require_once(__ROOT__ . 'local_config'.DS.'lang'.DS. get_session_language() . '.php');



/** 
 * The Singleton class that abstracts the database interface. 
 * @package Aixada
 * @subpackage Database_Management
 */ 

class DBWrap {
  public $debug = true;

  private $type;
  private $host;
  private $name;
  private $user;
  private $password;
  private $mysqli = false;
  private $key_info = array();
  private static $instance = false;

  /**
   * @var string stores the last query string sent to the SQL engine
   */
  public $last_query_SQL = '';

  /**
   * @var string stores the next-to-last query string sent to the SQL engine
   */
  public $next_to_last_query_SQL = '';
  
  private function __construct() 
  {
    $cv = configuration_vars::get_instance();
    $this->type = $cv->db_type;
    $this->host = $cv->db_host;
    $this->db_name = $cv->db_name;
    $this->user = $cv->db_user;
    $this->password = $cv->db_password;
    $this->mysqli = new mysqli($this->host, $this->user, $this->password, $this->db_name);
    if (mysqli_connect_errno())
      throw new InternalException('Unable to connect to database. ' . mysqli_connect_error());
    if (!$this->mysqli->set_charset("utf8"))
        throw new InternalException('Unable to select charset utf8. Current character set: ' 
                                    . $mysqli->character_set_name());
    $this->mysqli->query("SET SESSION SQL_MODE = '';");
  }
  /**
   * The DBWrap class is implemented as a Singleton. Call this
   * function to instantiate it, and not the constructor.
   */
  public static function get_instance()
  {
    if (self::$instance === false)
      self::$instance = new DBWrap;
    return self::$instance;
  }
  
  /**
   * Executes a start_transaction (uses mysql "START TRANSACTION")
   */
  public function start_transaction() {
    return $this->mysqli->query("START TRANSACTION;");
  }

  /**
   * Executes a commit of current transaction (uses mysqli "COMMIT")
   */
  public function commit() {
    return $this->mysqli->query("COMMIT;");
  }
  
  /**
   * Rolls back current transaction (uses mysql "ROLLBACK")
   */
  public function rollback() {
    return $this->mysqli->query("ROLLBACK;");
  }

  /**
   * Raise exception to report an error, if necessary. Right now, only 
   * foreign key constraint violations get special treatment.
   * @param int $errno the error number
   * @param string $error the error message
   * @param string $safe_sql_string the statement that was executed
   */ 
  private function handle_execute_error($errno, $error, $safe_sql_string)
  {
      switch ($errno) {
      case 1451:
          /*
           foreign key constraint violated. sample error message: 
           Cannot delete or update a parent row: a foreign key constraint fails (`aixada`.`aixada_order_item`, CONSTRAINT `aixada_order_item_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `aixada_product` (`id`))
          */
          $msg_array = explode('`', $error);
          $tmp = $msg_array[5]; // aixada_order_item_ibfk_2 in the example
          $upos = strrpos($tmp, '_');
          $upos = strrpos($tmp, '_', $upos-strlen($tmp)-1);
          throw new ForeignKeyException('ERROR 10: Cannot modifiy this entry because entries in ' . substr($tmp, 0, $upos) . ' depend on it');
       
      case 1452:
          /*
           Cannot add or update a child row: a foreign key constraint fails (`aixada`.`aixada_provider`, CONSTRAINT `aixada_provider_ibfk_1` FOREIGN KEY (`responsible_uf_id`) REFERENCES `aixada_uf` (`id`)
          */
          $msg_array = explode('`', $error);
          $bad_field = $msg_array[7]; // responsible_uf_id in the example
          global $Text;
          if (isset($Text[$bad_field]))
              $bad_field = $Text[$bad_field];
          $tmp = substr($bad_field, 0, strrpos($bad_field, '_'));
          if (isset($Text[$tmp]))
              $bad_field = $Text[$tmp];
          throw new ForeignKeyException('ERROR 20: Foreign Key exception. Please check the field "' . 
                                        $bad_field . 
                                        '". It either does not exist in the db or does not fullfil a foreign key constraint?');

      default:
          throw new DataException($safe_sql_string . ' generated error ' . $errno . ': ' . $error);
      }
  }

  /**
   * Executes an SQL query.
   *
   * @param string $safe_sql_string a sanitized SQL query. In
   * particular, this function should not be called directly (that's
   * why it's private), but only from other member functions of the
   * class that know what they're doing.
   * @return mysqli_query_type $rs the result of the query
   */
  private function do_Execute($safe_sql_string, $multi = false)
  {
    $rs = ($multi ? 
	   $this->mysqli->multi_query($safe_sql_string) :
	   $this->mysqli->query($safe_sql_string));
    if (!$rs) 
      $this->handle_execute_error($this->mysqli->errno, $this->mysqli->error, $safe_sql_string);
    if ($this->debug) {
      global $firephp;
      $firephp->log($safe_sql_string, 'query');
    }
    $this->next_to_last_query_SQL = $this->last_query_SQL;
    $this->last_query_SQL = $safe_sql_string;
    return $rs;
  }

  /**
   * This function provides a publicly accessible wrapper for the
   * private class @see do_Execute. It's supposed to only be used for
   * executing stored queries.
   */ 
  public function do_stored_query ($strSQL)
  {
    return $this->do_Execute($strSQL);
  } 
  
  /**
   * If the last sql command executed was an insert, returns
   * the id generate by auto_increment. 
   */
  public function last_insert_id()
  {
      return $this->mysqli->insert_id; 
  }

  
  /** 
   * Clean / free up mysql results. 
   */
  public function free_next_results()
  {
      while ($this->mysqli->more_results()) {
          $this->mysqli->next_result();
          $rs = $this->mysqli->use_result();
          if ($rs instanceof mysqli_result)
              $rs->free();
      }
  }

  
  /**
   * This function scapes special characters in a string for use in an SQL
   * statement.
   *
   * @param string $text
   * @return string The string to use in a sql stament.
   */
   public function escape_string($text) {
        return $this->mysqli->real_escape_string($text);
   }
  
  /**
   * This function accepts an SQL query string with placeholders of
   * the form :1, :2, ..., :999, and substitutes the placeholders by
   * the entries in the input array, after those have been passed to
   * mysqli->real_escape_string(). Optionally, the placeholders may be
   * of the form :4q, in which case the corresponding entry in the
   * input array (here, the fourth) gets surrounded by single quotes.
   *
   * @param array $binds an array of the form (0 => $strSQL, 1 => arg1, ..., n => argn)
   */
  private function make_safe_sql_str (&$binds)
  {
    $strSQL = array_shift($binds); 
    foreach ($binds as $index => $name) {
      $replace = $this->mysqli->real_escape_string($name);
      $i  = $index+1;
      $qpos = strpos($strSQL, ":$i") + 2;
      if ($i>9) $qpos++;
      if ($i>99) $qpos++;
      if (strpos($strSQL, 'q', $qpos) == $qpos) {
	$replace = "'" . $replace . "'";
	$strSQL = str_replace(":{$i}q", $replace, $strSQL);
      } else {
	$strSQL = str_replace(":$i", $replace, $strSQL);
      }
    }
    return $strSQL;
  }

  /**
   * A catch-all function that executes an SQL query.
   *
   * @param list $args the argument list provided should start with
   * the MySQL query string with possible placeholders, and then list
   * the replacements for the placeholders.
   *
   * @see make_safe_sql_str
   */
  public function Execute ()
  {
    $binds = func_get_args();
    if (is_array($binds[0])) {
      $binds = $binds[0];
    }
    return $this->do_Execute($this->make_safe_sql_str($binds));
  }

  /** 
   * A version of Execute() that works for stored queries
   * @see Execute
   */ 
  public function MultiExecute ()
  {
    $binds = func_get_args();
    $this->do_Execute($this->make_safe_sql_str($binds), true);
    return $this->mysqli->use_result();
  }

  /**
   * Inserts arbitrary columns into a table.
   *
   * @param array $arrData an array with entries of the form field => value . The values are inserted into the corresponding fields of the table.
   *
   */
  public function Insert ($arrData)
  {
      if (!array_key_exists('table', $arrData))
	  	throw new InternalException('Insert: Input array ' . $arrData . ' does not contain a field named "table"');
      $table_name = $arrData['table'];

      $strSQL = 'INSERT INTO ' . $this->mysqli->real_escape_string($table_name) . ' (';
      $strVAL = 'VALUES (';
      $all_col_names = unserialize(file_get_contents(__ROOT__ .'col_names.php'));
      if (!array_key_exists($table_name, $all_col_names)) {
	  throw new InternalException('Inserting into table ' . $table_name . ' not permitted');
      }
      $col_names = $all_col_names[$table_name];
      $ct = 0;
      foreach ($arrData as $field => $value) {
	  if (in_array($field, $col_names)) {
	      if ($ct > 0) {
		  $strSQL .= ',';
		  $strVAL .= ',';
	      } else $ct++;

	      $strSQL .= $this->mysqli->real_escape_string($field);
	      $strVAL .= "'" . $this->mysqli->real_escape_string($value) . "'";
	  }
      }
      $strSQL .= ') ' . $strVAL . ');';
      if (isset($_SESSION['fkeys'][$table_name]))
	  unset($_SESSION['fkeys'][$table_name]);
      return $this->do_Execute($strSQL); // TODO: extract new index
  }

  /**
   * Generic update function. Works just like Insert.
   * @param string $table_name the name of the database table
   * @param array $arrData the array that contains the data to be updated must contain a field named 'id' that contains the unique id.
   * @see Insert
   */ 
  public function Update($arrData)
  {
      if (!array_key_exists('table', $arrData))
	  throw new InternalException('Update: Input array ' . $arrData . ' does not contain a field named "table"');
      $table_name = $arrData['table'];

      if (!array_key_exists('id', $arrData))
	  throw new InternalException('Update: Input array ' . $arrData . ' for table ' . $table_name . ' does not contain a field named "id"');
      $strSQL = 'UPDATE ' . $this->mysqli->real_escape_string($table_name) . ' SET ';

      $all_col_names = unserialize(file_get_contents(__ROOT__ .'col_names.php'));
      if (!array_key_exists($table_name, $all_col_names)) {
	  throw new InternalException('Updating table ' . $table_name . ' not permitted');
      }
      $col_names = $all_col_names[$table_name];

      $ct=0;
      foreach ($arrData as $field => $value) {
	  if ($field != 'id' and in_array($field, $col_names)) {
	      if ($ct > 0) $strSQL .= ','; else $ct++;
	      $strSQL .= $this->mysqli->real_escape_string($field) . "='" 
		  . $this->mysqli->real_escape_string($value) . "'";
	  }
      }
      $strSQL .= ' WHERE id=' . $this->mysqli->real_escape_string($arrData['id']) . ';';
      if (isset($_SESSION['fkeys'][$table_name]))
	  unset($_SESSION['fkeys'][$table_name]);
      
      $success = $this->do_Execute($strSQL);
    
      //the check happens on the client side now
      //for import data, updates should be possible without updating the responsible_uf_id
      /*if ($table_name == 'aixada_provider') {
	  $strSQL = "update aixada_product set responsible_uf_id='"
	      . $this->mysqli->real_escape_string($arrData['responsible_uf_id'])
	      . "' where provider_id="
	      . $this->mysqli->real_escape_string($arrData['id']);
	  $success = $this->do_Execute($strSQL);
      }*/
      return $success;

  }


  public function Delete($_tn, $_id)
  {
      $table_name = $this->mysqli->real_escape_string($_tn);
      $all_col_names = unserialize(file_get_contents(__ROOT__ .'col_names.php'));
      if (!array_key_exists($table_name, $all_col_names)) {
	  throw new InternalException('Deleting from table ' . $table_name . ' not permitted');
      }
      $id = $this->mysqli->real_escape_string($_id);
      if ($table_name == 'aixada_product') {
	  $strSQL = "
start transaction; 
delete from aixada_price where product_id='{$id}'; 
delete from aixada_product where id='{$id}';
commit;";
	  $multi = true;
      } else {
	  $strSQL = "delete from {$table_name} where id='{$id}'";
	  $multi = false;
      }
      return $this->do_Execute($strSQL, $multi);
  }

  /**
   * @param $count int the total number of entries in the table
   * @param $page int the required page number 
   * @param $limit int the number of entries per page
   * @return $start int the index of the entry that opens page $page
   * @return $total_pages int the total number of pages in the table
   */
  public function calculate_page_limits ($count, $page, $limit) 
  {
    
    $total_pages = ($count>0) ? ceil($count/$limit) : 0;
    if ($page < 0)
      $page = 0;
    if ($page > $total_pages && $total_pages>0)
      $page = $total_pages;
    $start = $limit * $page - $limit;
    return array($start, $total_pages);
  }

  public function make_count_string ($table_name, $filter)
  {
    $strSQL = 'SELECT COUNT(*) AS count';
    $strSQL .= ' FROM ' . $this->mysqli->real_escape_string($table_name);
    if ($filter)
      $strSQL .= ' WHERE ' . $this->mysqli->real_escape_string($filter);
    return $strSQL;
  }

  /**
   * Make the string to be passed to SELECT.
   */
  public function make_select_string ($fields, $table_name, $filter, $order_by, $order_sense='asc', $page=-1, $limit=-1)
  {
//     global $firephp;
//     if ($this->debug) {
//       $firephp->log($table_name, 'entering Select');
//       $firephp->log($fields, '..fields');
//       $firephp->log($filter, '..filter');
//       $firephp->log($order_by, '..order_by');
//       $firephp->log($order_sense, '..order_sense');
//       $firephp->log($page, '..page');
//       $firephp->log($limit, '..limit');
//     }
    $the_table = $this->mysqli->real_escape_string($table_name);
    $the_filter = $this->mysqli->real_escape_string($filter);
    
    $strSQL = 'SELECT ';
    if (is_string($fields))
      $strSQL .= $this->mysqli->real_escape_string($fields);
    else if (is_array($fields)) {
      $ct=0;
      foreach ($fields as $field) {
	if ($ct>0)
	  $strSQL .= ',';
	else $ct++;
	$strSQL .= $this->mysqli->real_escape_string($field);
      }
    } else throw new InternalException('Argument ' . $field . ' is neither string nor array');
    
    $strSQL .= ' FROM ' . $the_table;
    if ($filter)
      $strSQL .= ' WHERE ' . $the_filter;
    if ($order_by)
      $strSQL .= ' ORDER BY ' . $this->mysqli->real_escape_string($order_by) . ' ' . $order_sense; 
    return $strSQL;
  }

  public function canned_select($count_querySQL, $real_querySQL, $page=-1, $limit=-1)
  {
    if ($page != -1 && $limit != null) {
      $rs = $this->do_Execute($count_querySQL);
      $row = $rs->fetch_array();
      $count = $row[0];
      list($start, $total_pages) = $this->calculate_page_limits($count, $page, $limit);
      $real_querySQL .= ' LIMIT ' . $start . ', ' . $limit;
    }
    $rs = $this->do_Execute($real_querySQL);
    if ($page != -1)
      return array($rs, $count, $total_pages);
    else return $rs;
  }

  /**
   * The encapsulating select function.
   * <b>There is a security leak in the WHERE clause.</b>
   * @param int $page the page requested by jqGrid
   * @param int $limit the number of rows in the grid
   */

  public function Select($fields, $table_name, $filter, $order_by, $order_sense='asc', $page=-1, $limit=-1)
  {
    $count_querySQL = $this->make_count_string($table_name, $filter);
    $real_querySQL = $this->make_select_string($fields, $table_name, $filter, $order_by, $order_sense, $page, $limit);
    return $this->canned_select($count_querySQL, $real_querySQL, $page, $limit);
  }
}

?>
