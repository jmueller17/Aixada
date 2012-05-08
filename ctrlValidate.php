<?php

//require_once('FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
//ob_start(); // Starts FirePHP output buffering

require_once("local_config/config.php");
require_once("inc/database.php");
require_once("utilities.php");
require_once("utilities_shop_and_order.php");
require_once("lib/validation_cart_manager.php");


//$firephp = FirePHP::getInstance(true);

$use_session_cache = configuration_vars::get_instance()->use_session_cache;

// This controls if the table_manager objects are stored in $_SESSION or not.
// It looks like doing it cuts down considerably on execution time.

if (!isset($_SESSION)) {
    session_start();
 }

//$firephp->log($_SESSION, 'session');
DBWrap::get_instance()->debug = true;

try{

  // First we process requests that don't need any extra input
  switch ($_REQUEST['oper']) {

  case 'getDatesForValidation':
      $xml = stored_query_XML_fields('dates_with_unvalidated_shop_carts');
      $today = strftime('%Y-%m-%d', strtotime('today'));
      $pos = strpos($xml, $today);
      if ($pos !== false) {
	  $xml = substr_replace($xml, ' today="true"', $pos-10, 0);
      }
      printXML($xml);
      exit;

  case 'getProductsBelowMinStock':
    printXML(query_XML_noparam('products_below_min_stock'));
    exit;

  case 'getNegativeAccounts':
    printXML(get_negative_accounts());
    exit;

  case 'listProductsLike':
    printXML(stored_query_XML_fields('products_for_validate_like', $_REQUEST['like']));
    exit;

  case 'backupDatabase':
      $cv = configuration_vars::get_instance();
      $filename = 'local_config/dbBkups/' . $cv->db_name . '.' . strftime('%Y.%m.%d', strtotime("now")) . '.sql';
      $output = array();
      $retval = 0;
      $cmds = array('rm -f ' . $filename . '.bz2',
      				//if the path to the mysqldump is not included in the PHP environment variables a error code 127 is thrown!! 
      				//of course the path could be different on each machine?!!
      				//'/opt/lampp/bin/mysqldump --default-character-set=utf8'
                    'mysqldump --default-character-set=utf8'
                    . ' -u '. $cv->db_user
                    . ' -p' . $cv->db_password 
                    . ' --host=' . $cv->db_host
                    . ' ' . $cv->db_name 
                    . ' > ' . $filename,
                    'bzip2 ' . $filename);
      foreach ($cmds as $cmd) {
          exec($cmd . ' 2>&1', $output, $retval);
          if ($retval) {
              $errstr = $retval . ' ';
              foreach ($output as $out) {
                  $errstr .= $out . ';';
              }
              throw new InternalException('Could not execute "' . $cmd 
                                          . '". Error message: ' . $errstr
                                       	);
          }
      }
      echo $filename . '.bz2';
      
      exit;
  }

  // then we gather extra information
  $the_date = ((isset($_REQUEST['date']) and $_REQUEST['date'] != '' and $_REQUEST['date'] != '0') ? $_REQUEST['date'] : get_next_shop_date());
  //  $firephp->log($_REQUEST, 'request');
  //  $firephp->log($the_date, 'the_date');

  switch($_REQUEST['oper']) {

  case 'getIncomeSpendingBalance':
      printXML(stored_query_XML_fields('income_spending_balance', $the_date));
    exit;

  case 'GetUFsForValidation':
    printXML(stored_query_XML('get_ufs_for_validation', 'ufs', 'name', $the_date));
    exit;

  }

  // then we gather even more extra information
  if (!isset($_REQUEST['uf_id']))
    throw new Exception ('UF id not set in query');

  $uf_id = $_REQUEST['uf_id'];
  $op_id = $_SESSION['userdata']['user_id']; 

  switch($_REQUEST['oper']) {

  case 'getShopItemsForDateAndUf':
    printXML(stored_query_XML_fields('products_for_validating', $uf_id, $the_date));
    exit;

  case 'DepositForUF':
      return do_stored_query('deposit_for_uf', $uf_id, $_REQUEST['quantity'], $_REQUEST['description'], $op_id);
      exit;

      //  case 'UndoValidation':
      //      $the_validation = $_REQUEST['validation'];
      //      $the_id = substr($the_validation, 0, strpos($the_validation, ' '));
      //      return do_stored_query('undo_validation', $the_id, $op_id);
      //      exit;
  }

  $vm = new validation_cart_manager($op_id, $uf_id, $the_date); 
  switch($_REQUEST['oper']) {

  case 'commit':
      $vm->commit($_REQUEST['quantity'], $_REQUEST['price'], 
                  $_REQUEST['product_id'], $_REQUEST['preorder']);
    break;

  default:
    throw new Exception("ctrlShopAndOrder: variable oper not set in query");
  }
} 

catch(Exception $e) {
  header('HTTP/1.0 401 ' . $e->getMessage());
  die($e->getMessage());
}  


?>