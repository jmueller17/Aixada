<?php

define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(__FILE__))).DS); 

require_once(__ROOT__ . "local_config/config.php");
require_once(__ROOT__ . "php/inc/database.php");
require_once(__ROOT__ . "php/utilities/general.php");
require_once(__ROOT__ . "php/utilities/account.php");
require_once(__ROOT__ . "php/lib/report_manager.php");
require_once(__ROOT__ . "php/lib/account_movement.php");


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
	    	
	    case 'deposit':
	    	echo do_stored_query('deposit', get_param('account_id'), get_param('quantity'), get_param('description',''), get_session_user_id());
	    	exit; 
	    	
	    case 'withdraw':
	    	echo do_stored_query('withdrawal', get_param('account_id'), get_param('quantity'), get_param('description',''), get_session_user_id(),get_param('sel_withdraw_type','-1'));
	    	exit; 
	    	
	    case 'globalAccountsBalance':
	    	printXML(stored_query_XML_fields('global_accounts_balance'));
			exit;
			
	    case 'correctBalance':
	    	echo do_stored_query('correct_account_balance', get_param('account_id'), get_param('balance'), get_session_user_id(), get_param('description','') );
	    	exit;

	    case 'depositCash':
	    	$a = new account_movement(get_session_user_id()); 
	    	$a->deposit_cash_for_uf(20, 82, 'testing the new stuff');
	    	exit; 
  	
	    	
  		
	  default:
	    throw new Exception("ctrlAccount: operation {$_REQUEST['oper']} not supported");
    
  }

} 

catch(Exception $e) {
  header('HTTP/1.0 401 ' . $e->getMessage());
  die($e->getMessage());
}  
?>