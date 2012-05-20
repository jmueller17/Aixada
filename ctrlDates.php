<?php

require_once('FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start(); // Starts FirePHP output buffering

require_once("local_config/config.php");
require_once("inc/database.php");
require_once("utilities_dates.php");
require_once("utilities.php");

$firephp = FirePHP::getInstance(true);

$use_session_cache = configuration_vars::get_instance()->use_session_cache;

if (!isset($_SESSION)) {
    session_start();
 }


DBWrap::get_instance()->debug = true;

function extract_data($what) {
    return (isset($_REQUEST[$what]) ? $_REQUEST[$what] : '');
}

try{
    $oper = extract_data('oper');
    $date = extract_data('date');

    
  switch ($oper) {

  	case 'getEmptyOrderableDates':
  	  	echo get_orderable_dates('get_empty_orderable_dates');
  	  	exit;

  	case 'getDatesWithOrders':
  	  	echo get_orderable_dates('get_nonempty_orderable_dates');
      	exit;
      
    case 'getDatesWithSometimesOrderable':
  	  	echo get_orderable_dates('get_sometimes_orderable_dates');
      	exit;
      
  	case 'getAllOrderableDates':
  	  	echo get_orderable_dates('get_all_orderable_dates');
  	  	exit;
      
  	case 'addOrderableDate':
      	do_stored_query('add_orderable_date',$date);
      	exit;

  	case 'delOrderableDate':
      	do_stored_query('del_orderable_date',$date);
      	exit;
  	case 'generateDates':
  		$weekDays = extract_data('weekday');		
  		$nrMonth = extract_data('nrOfMonth'); 
  		$frequency = extract_data('frequency');
  		echo generate_and_add_date_pattern($weekDays, $nrMonth, $frequency);
  	  	exit;

  	default:
    	throw new Exception("ctrlDates: \"oper=" . $_REQUEST['oper'] . "\" not valid in query");
  }
} 

catch(Exception $e) {
  header('HTTP/1.0 401 ' . $e->getMessage());
  die($e->getMessage());
}  


?>