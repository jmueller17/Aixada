<?php

define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__)))).DS); 



require_once(__ROOT__ . "php/utilities/general.php");
require_once(__ROOT__ . "php/lib/aixmodel.php");

require_once("account_mod.php");

/*require_once(__ROOT__ . "php/utilities/general.php");
require_once(__ROOT__ . "php/utilities/account.php");
require_once(__ROOT__ . "php/lib/report_manager.php");
require_once(__ROOT__ . "php/lib/account_movement.php");*/




if (!isset($_SESSION)) {
    session_start();
 }


try{ 
   	
 	switch ($_REQUEST['oper']) {

 		case 'getAllAccounts':
 			$acs = Account::list_accounts(1); 
 			Deliver::serve_str($acs, "xml");
	        exit;
 		
	    case 'getActiveAccounts':
		    $acs = Account::list_accounts(0); 
 			Deliver::serve_str($acs, "xml");
	        exit;   
	        
  		case 'accountExtract':
  			print_stored_query('xml','get_extract_in_range', get_param('account_id', get_session_uf_id() ), get_param('from_date',0), get_param('to_date',0)  );
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
	    	
	    	
	    case 'globalAccountsBalance':
	    	printXML(stored_query_XML_fields('global_accounts_balance'));
			exit;
			
	    case 'correctBalance':
	    	echo do_stored_query('correct_account_balance', get_param('account_id'), get_param('balance'), get_session_user_id(), get_param('description','') );
	    	exit;


	    case 'depositCashForUf':
	    	$a = new account_movement(get_session_user_id()); 
	    	$a->deposit_cash_for_uf(get_param('quantity'), get_param('account_id'), get_param('description',''));
	    	exit; 

	    case 'depositCash':
			$a = new account_movement(get_session_user_id()); 
	    	$a->deposit_cash(get_param('quantity'), get_param('description',''));
	    	exit; 

	    case 'depositSalesCash':
			$a = new account_movement(get_session_user_id()); 
	    	$a->deposit_sales_cash(get_param('quantity'), get_param('description',''));
	    	exit; 
	    	
	    case 'payProviderCash':
			$a = new account_movement(get_session_user_id()); 
	    	$a->pay_provider_cash(get_param('quantity'), 0, get_param('description',''));
	    	exit; 
	    	
	    case 'payProviderBank':
			$a = new account_movement(get_session_user_id()); 
	    	$a->pay_provider_bank(get_param('quantity'), 0,  get_param('description',''));
	    	exit; 
	    	
	    case 'withdrawCash':
			$a = new account_movement(get_session_user_id()); 
	    	$a->withdraw_cash(get_param('quantity'), get_param('description',''));
	    	exit; 
	    	
	   	case 'withdrawCashForBank':
			$a = new account_movement(get_session_user_id()); 
	    	$a->withdraw_cash_for_bank(get_param('quantity'), get_param('description',''));
	    	exit; 

	   	case 'withdrawCashFormUFAccount':
			$a = new account_movement(get_session_user_id()); 
	    	$a->withdraw_cash_from_uf_account(get_param('quantity'), get_param('account_id'), get_param('description',''));
	    	exit;
	    	
	   	case 'withdrawMemberQuota':
			$a = new account_movement(get_session_user_id()); 
	    	$a->withdraw_member_quota(get_param('quantity'), get_param('account_id'), get_param('description',''));
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