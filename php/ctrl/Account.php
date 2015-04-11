<?php

define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(__FILE__))).DS); 

require_once(__ROOT__ . "local_config/config.php");
require_once(__ROOT__ . "php/inc/database.php");
require_once(__ROOT__ . "php/utilities/general.php");
require_once(__ROOT__ . "php/lib/report_manager.php");
require_once(__ROOT__ . "php/lib/account_movement.php");
require_once(__ROOT__ . "php/lib/account_operations.php");


$use_session_cache = true; 


if (!isset($_SESSION)) {
    session_start();
 }


try{ 
   	$ao = new account_operations();
 	switch ($_REQUEST['oper']) {
        case 'getAccounts':
	        printXML($ao->get_accounts_XML(
                get_param_int('all', 0), 
                get_param_array_int('account_types'), 
                get_param_int('show_uf'), 
                get_param_int('show_providers')
            ));
            exit;

 		case 'getAllAccounts':
	        printXML($ao->get_accounts_XML(1, array(1,2), 1, 1));
	        exit;
 		
	    case 'getActiveAccounts':
	        printXML($ao->get_accounts_XML(0, array(1,2), 1, 1));
	        exit;   
	        
  		case 'accountExtract':
  			printXML($ao->get_account_extract_XML(
				get_param_int('account_id', get_session_uf_id() ), 
				get_param('filter','today'),
				get_param('fromDate',0),
				get_param('toDate',0)
			));
  			exit; 
  		
  	 	case 'latestMovements':  	 		
            printXML($ao->latest_movements_XML(
                get_param_int('limit', 10), 
                get_param_array_int('account_types'), 
                get_param_int('show_uf', 1), 
                get_param_int('show_providers', 0)
            ));
	    	exit;
	    	
	    case 'getBalances':
            printXML(
				$ao->get_balances_XML(get_param_array_int('account_types'))
			);
            exit;

	   	case 'getUfBalances':
			printXML($ao->get_uf_balances_XML(
					get_param_int('all', 0),                
					get_param_int('negative', 0)
			));
	    	exit;
			
	   	case 'getNegativeAccounts':
			printXML($ao->get_uf_balances_XML(0, 1));
	    	exit;
	    	
	    case 'getIncomeSpendingBalance':
			printXML($ao->get_income_spending_XML(
					get_param_date('date', date("Y-m-d")),
					get_param_array_int('account_types', array(1))
			));
	    	exit;
	    	
	    case 'globalAccountsBalance':
	    	printXML(stored_query_XML_fields('global_accounts_balance'));
			exit;
			
        case 'addOperation':
            echo $ao->add_operation(
                get_param('account_operation',''),
                array(
                    'account_from_id' => get_param_int('account_from_id'),	
                    'uf_from_id' => get_param_int('uf_from_id'),	
                    'provider_from_id' => get_param_int('provider_from_id'),	
                    'account_to_id' => get_param_int('account_to_id'),	
                    'uf_to_id' => get_param_int('uf_to_id'),
                    'provider_to_id' => get_param_int('provider_to_id')
                ),	
                get_param_numeric('quantity'),
                get_param('description','')
            );
            exit;
        
        case 'correctBalance':
	    	echo do_stored_query('correct_account_balance', get_param('account_id'), get_param('balance'), get_session_user_id(), get_param('description','') );
	    	exit;


	    case 'depositCashForUf':
            $ao->add_operation('deposit_uf',
                array(
                    'uf_from_id' => get_param_int('account_id'),	
                    'account_to_id' => -3
                ),	
                get_param_numeric('quantity'),
                get_param('description','Cash deposit')
            );
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