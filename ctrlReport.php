<?php


require_once("local_config/config.php");
require_once("inc/database.php");
require_once("utilities.php");
require_once("utilities_report.php");
require_once("lib/report_manager.php");


$use_session_cache = true; 


if (!isset($_SESSION)) {
    session_start();
 }


try{ 
   
	//$rm = new report_manager;
	
	
 	switch ($_REQUEST['oper']) {

 		
  		case 'accountExtract':
  			echo get_account_extract(get_param('account_id', get_session_uf_id() ), get_param('filter','today'), get_param('fromDate',0), get_param('toDate',0)  );
  			exit; 
  		
  	 	case 'latest_movements':
  	 		printXML(stored_query_XML_fields('latest_movements'));
	    	//printXML(latest_movements());
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