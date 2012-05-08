<?php

//require_once('FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start(); // Starts FirePHP output buffering

require_once("local_config/config.php");
require_once("inc/database.php");
require_once("utilities_dates.php");

//$firephp = FirePHP::getInstance(true);

$use_session_cache = configuration_vars::get_instance()->use_session_cache;

// This controls if the table_manager objects are stored in $_SESSION or not.
// It looks like doing it cuts down considerably on execution time.

if (!isset($_SESSION)) {
    session_start();
 }

//$firephp->log($_SESSION, 'session');
DBWrap::get_instance()->debug = true;

function extract_data($what) {
    return (isset($_REQUEST[$what]) ? $_REQUEST[$what] : '');
}

try{
    $oper = extract_data('oper');
    $date = extract_data('date');

  switch ($oper) {

  case 'availableDates':
      echo get_next_shop_dates();
      exit;

  case 'datesWithOrders':
      echo get_dates_with_orders();
      exit;

  case 'addDate':
      echo add_date($date);
      exit;

  case 'removeDate':
      echo remove_date($date);
      exit;

  default:
    throw new Exception("ctrlUser: \"oper=" . $_REQUEST['oper'] . "\" not valid in query");
  }
} 

catch(Exception $e) {
  header('HTTP/1.0 401 ' . $e->getMessage());
  die($e->getMessage());
}  


?>