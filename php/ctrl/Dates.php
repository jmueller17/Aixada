<?php

define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(__FILE__))).DS); 

require_once(__ROOT__ . "local_config/config.php");
require_once(__ROOT__ . "php/inc/database.php");
require_once(__ROOT__ . "php/utilities/dates.php");
require_once(__ROOT__ . "php/utilities/general.php");

DBWrap::get_instance()->debug = true;

function extract_data($what) {
    return (isset($_REQUEST[$what]) ? $_REQUEST[$what] : '');
}

try{
    validate_session(); // The user must be logged in.
    
    $oper = extract_data('oper');
    $date = extract_data('date');

    
  switch ($oper) {
  	
  	case 'getToday':
  		echo get_dates('today', $format='array');
  		exit;

  	case 'getOrderableDates':
  	  	echo get_orderable_dates();
  	  	exit;
  	  	
  	case 'getUpcomingOrders':
  		echo get_upcoming_orders(get_param('range'));
  		exit; 
      	
  	case 'getDateRangeAsXML':
		 printXML(dateRange($_REQUEST['fromDate'],$_REQUEST['toDate'],'D d'));
  		exit;
  
  	case 'getDateRangeAsArray':
  		echo dateRange($_REQUEST['fromDate'], $_REQUEST['toDate'],'Y-m-d', 'array');
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