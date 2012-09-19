<?php

require_once('FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start(); // Starts FirePHP output buffering

require_once("local_config/config.php");
require_once("php/inc/database.php");
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

  	 /*case 'getEmptyOrderableDates':
  	  	echo get_orderable_dates('get_empty_orderable_dates');
  	  	exit;

  	case 'getDatesWithOrders':
  	  	echo get_orderable_dates('get_nonempty_orderable_dates');
      	exit;
      
   case 'getDatesWithSometimesOrderable':
  	  	echo get_orderable_dates('get_sometimes_orderable_dates');
      	exit;
     */ 
  	
  	case 'getToday':
  		echo get_dates('today', $format='array');
  		exit;

  	case 'getNextAvailableOrderDate':
  		echo get_dates('get_orderable_dates', $format='xml', $limit=1);
  		exit;
  		
  	case 'getAllOrderableDates':
  	  	echo get_dates('get_orderable_dates', $format='array');
  	  	exit;
      	
  	case 'getDateRangeAsXML':
		 printXML(dateRange($_REQUEST['fromDate'],$_REQUEST['toDate'],'D d'));
  		exit;
  
  	case 'getDateRangeAsArray':
  		echo dateRange($_REQUEST['fromDate'], $_REQUEST['toDate'],'Y-m-d', 'array');
  		exit;
  	
  		
  		
  		
   /*case 'get10Dates':
        printXML(get_10_sales_dates_XML($date));
        exit;

    case 'getNextDate':
        printXML(get_next_shop_date_XML());
        exit;
    
    case 'getNextEqualShopDate':
        printXML(get_next_equal_shop_date_XML());
        exit;

    case 'setOrderableDate':
        set_orderable_date($date, $available);
        exit;

    case 'getYearsOfOrders':
        printXML(query_XML_compact('SELECT DISTINCT YEAR(date_for_order) FROM aixada_order_item', 'years', 'year'));
        exit;

    case 'getOrderDatesOfYear':
        printXML(next_shop_dates());
        exit;*/
  		  		

  	default:
    	throw new Exception("ctrlDates: \"oper=" . $_REQUEST['oper'] . "\" not valid in query");
  }
} 

catch(Exception $e) {
  header('HTTP/1.0 401 ' . $e->getMessage());
  die($e->getMessage());
}  


?>