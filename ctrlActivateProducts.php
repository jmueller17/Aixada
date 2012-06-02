<?php

//require_once('FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start(); // Starts FirePHP output buffering

require_once("local_config/config.php");
require_once("inc/database.php");
require_once("utilities.php");
require_once("utilities_dates.php");
require_once("utilities_shop_and_order.php");



//$firephp = FirePHP::getInstance(true);

$use_session_cache = configuration_vars::get_instance()->use_session_cache;

// This controls if the table_manager objects are stored in $_SESSION or not.
// It looks like doing it cuts down considerably on execution time.

if (!isset($_SESSION)) {
    session_start();
 }

DBWrap::get_instance()->debug = true;

try{
  $op_id = $_SESSION['userdata']['uf_id'];
  $the_date = (isset($_REQUEST['date']) ? $_REQUEST['date'] : '');

  switch($_REQUEST['oper']) {

  case 'listProviders':
      printXML(stored_query_XML('list_all_providers_short', 'providers', 'name'));
      exit;
      
  case 'getOrderableProducts4DateRange':
  	printXML(stored_query_XML('get_orderable_products_for_dates', 'products', 'date', $_REQUEST['fromDate'], $_REQUEST['toDate'], $_REQUEST['provider_id']));
  	exit; 	
  		
  case 'toggleOrderableProduct':
  	echo do_stored_query('toggle_orderable_product', $_REQUEST['product_id'], $the_date);
    exit;
        
  case 'getTypeOrderableProducts':
  	printXML(stored_query_XML_fields('get_type_orderable_products', $_REQUEST['provider_id'] ));
  	exit;
  		
  case 'activateProduct':
  	echo do_stored_query('change_active_status_product', 1, $_REQUEST['product_id']);
  	exit;
  		
  case 'deactivateProduct':
  	echo do_stored_query('change_active_status_product', 0, $_REQUEST['product_id']);
  	exit;
  	
  case 'generateDatePattern':
  	echo generate_date_product_pattern($_REQUEST['provider_id'], $the_date, $_REQUEST['weeklyFreq'],  $_REQUEST['nrMonth'] );
  	exit; 
        

  	
  	
  	
  case 'getActivatedProducts':
      printXML(stored_query_XML('get_activated_products', 'products', 'name', $_REQUEST['provider_id'], $_REQUEST['date']));
      exit;
        
  case 'getDeactivatedProducts':
      printXML(stored_query_XML('get_deactivated_products', 'products', 'name', $_REQUEST['provider_id'], $_REQUEST['date']));
    exit;

  case 'activateProducts':
    printXML(activate_products($_REQUEST['provider_id'], $_REQUEST['product_ids'], $_REQUEST['date']));
    exit;

  case 'listOrderedProviders':
      printXML(stored_query_XML('list_all_ordered_providers_short', 'providers', 'name', $_REQUEST['date']));
      exit;

  case 'getArrivedProducts':
      printXML(stored_query_XML('get_arrived_products', 'products', 'name', $_REQUEST['provider_id'], $_REQUEST['date']));
      exit;

  case 'getNotArrivedProducts':
      printXML(stored_query_XML('get_not_arrived_products', 'products', 'name', $_REQUEST['provider_id'], $_REQUEST['date']));
    exit;

  case 'productsHaveArrived':
    printXML(arrived_products($_REQUEST['provider_id'], $_REQUEST['product_ids'], $_REQUEST['date']));
    exit;

  default:
    throw new Exception("ctrlActivateProducts: variable oper not set in query");
    
  }
} 

catch(Exception $e) {
  header('HTTP/1.0 401 ' . $e->getMessage());
  die ($e->getMessage());
}  


?>