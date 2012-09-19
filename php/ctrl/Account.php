<?php


require_once("local_config/config.php");
require_once("php/inc/database.php");
require_once("utilities.php");
require_once("utilities_account.php");
require_once("lib/report_manager.php");


$use_session_cache = true; 


if (!isset($_SESSION)) {
    session_start();
 }


try{ 
   
	//$rm = new report_manager;
	
	
 	switch ($_REQUEST['oper']) {

 		 case 'getAllAccounts':
	        printXML(get_accounts(1));
	        exit;
 		
	      case 'getActiveAccounts':
	        printXML(get_accounts(0));
	        exit;   
	        
  		case 'accountExtract':
  			echo get_account_extract(get_param('account_id', get_session_uf_id() ), get_param('filter','today'), get_param('fromDate',0), get_param('toDate',0)  );
  			exit; 
  		
  	 	case 'latestMovements':
  	 		printXML(stored_query_XML_fields('latest_movements'));
	    	exit;
	    	
	   	case 'getNegativeAccounts':
	  		printXML(get_negative_accounts());
	    	exit;
	    	
	    case 'getIncomeSpendingBalance': 
	    	printXML(stored_query_XML_fields('income_spending_balance', get_param('date',0)));
	    	exit;
	    
	    case 'DepositForUF':
	   		return do_stored_query('deposit_for_uf', get_param('uf_id'), get_param('quantity'), get_param('description',''), get_session_user_id());
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