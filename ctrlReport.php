<?php

 require_once('FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
 ob_start(); // Starts FirePHP output buffering

require_once("local_config/config.php");
require_once("inc/database.php");
require_once("utilities.php");
require_once("utilities_report.php");
require_once("utilities_dates.php");
require_once("lib/report_manager.php");

$firephp = FirePHP::getInstance(true);

$use_session_cache = true; 
// This controls if the table_manager objects are stored in $_SESSION or not.
// It looks like doing it cuts down considerably on execution time.

if (!isset($_SESSION)) {
    session_start();
 }

DBWrap::get_instance()->debug = true;

try{ 
    $uf_logged_in = $_SESSION['userdata']['uf_id'];
    $uf_for_shops = (isset($_SESSION['userdata']) and
                     ( $_SESSION['userdata']['current_role'] == 'Checkout' or
                       $_SESSION['userdata']['current_role'] == 'Hacker Commission') and
                     isset($_REQUEST['uf_id'])) ? $_REQUEST['uf_id'] : $uf_logged_in;
        
   
    switch ($_REQUEST['oper']) {
        
    case 'accountExtract':
        $account_id = ((isset($_REQUEST['account_id']) and 
                        $_REQUEST['account_id']!=0) ? 
                       $_REQUEST['account_id'] : 
                       ((int)$uf_logged_in));
        if (0< $account_id and $account_id < 1000)
            $account_id += 1000;
        $start_date = (isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : 0);
        // a date of 0 defaults to sysdate - 3 months
        $num_rows = ((isset($_REQUEST['num_rows']) and $_REQUEST['num_rows'] != 0) ? $_REQUEST['num_rows'] : 10);
        printXML(account_extract($account_id, $start_date, $num_rows));
        exit;

    }
    
    $rm = new report_manager;
    $next_order = get_dates('get_orderable_dates', $format='array', $limit=1);

    //$the_date = ((isset($_REQUEST['date']) and $_REQUEST['date'] != '0') ? $_REQUEST['date'] : get_next_shop_date());
    
    $the_date = (isset($_REQUEST['date']) and $_REQUEST['date'] != '0') ? $_REQUEST['date'] : $next_order[0];
    
  	$provider_id = (isset($_REQUEST['provider_id']) ? $_REQUEST['provider_id'] : 0);

  switch ($_REQUEST['oper']) {

  case 'listSummarizedOrdersForDate':
    printXML(stored_query_XML_fields('summarized_orders_for_date', $the_date));
    exit;
    
  case 'listSummarizedPreOrders':
    printXML(stored_query_XML_fields('summarized_preorders'));
    exit;
    
  case 'listCompactOrdersForProviderAndDate':
    echo $rm->compact_orders_for_provider_and_dateHTML($provider_id, $the_date, false);
    exit;

  case 'listExtendedOrdersForProviderAndDate':
    echo $rm->extended_orders_for_provider_and_dateHTML($provider_id, $the_date, false);
    exit;
    
  case 'listCompactPreOrderProductsForProvider':
      echo $rm->compact_preorders_for_provider($provider_id);
      exit;
    
  case 'listExtendedPreOrderProductsForProvider':
      echo $rm->extended_preorders_for_provider($provider_id);
      exit;

  case 'bundleOrdersForDate':
      $zipfile = $rm->bundle_orders_for_date($the_date);
      echo $zipfile;
      exit;
        
  case 'total_orders_for_next_sale':
    $strHTML = $rm->total_orders_for_dateHTML(get_next_shop_date(), false);
    HTMLwrite($strHTML, 'the_report.html');
    echo $strHTML;
    exit;

  case 'total_orders_for_date':
    $strHTML = $rm->total_orders_for_dateHTML($the_date, false);
    HTMLwrite($strHTML, 'the_report.html');
    echo $strHTML;
    exit;

  case 'latest_movements':
    printXML(latest_movements());
    exit;

  case 'accountList':
    printXML(account_list());
    exit;

  case 'spending_per_provider':
      $the_date = ((isset($_REQUEST['date']) and $_REQUEST['date'] != '0') ? $_REQUEST['date'] : date("Y").'-01-01');
      echo spending_per_provider_JSON($the_date);
      exit;


  case 'getFutureShopTimes':
    printXML(stored_query_XML_fields('future_shop_times_for_uf', $uf_for_shops));    	
  	exit;
      
  case 'getAllShopTimes':
      //printXML('<row></row>');
  	  printXML(stored_query_XML_fields('shop_times_for_uf', $uf_for_shops));
      exit;
      
  case 'getShoppedItems':
      $the_id = (isset($_REQUEST['shop_id']) ? $_REQUEST['shop_id'] : '');
      printXML(stored_query_XML_fields('shopped_items_by_id', $the_id));
      exit;

  default:
    throw new Exception("ctrlReport: operation {$_REQUEST['oper']} not supported");
    
  }

} 

catch(Exception $e) {
  header('HTTP/1.0 401 ' . $e->getMessage());
  die($e->getMessage());
}  


?>