<?php

define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(__FILE__))).DS); 

require_once(__ROOT__ . "local_config/config.php");
require_once(__ROOT__ . "php/inc/database.php");
require_once(__ROOT__ . "php/utilities/general.php");
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
                get_param_array_int('account_types')
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
				get_param('account_id', get_session_uf_id() ), 
				get_param('filter','today'),
				get_param('fromDate',0),
				get_param('toDate',0)
			));
  			exit; 
  		
  	 	case 'latestMovements':
            printXML($ao->latest_movements_XML(
                get_param_int('limit', 10), 
                get_param_array_int('account_types',array(1000))
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

		case 'getProviderBalances':
			printXML($ao->get_provider_balances_XML(
					get_param_int('all', 0),                
					get_param_int('negative', 0)
			));
			exit;
	    	
	    case 'getIncomeSpendingBalance':
			printXML($ao->get_income_spending_XML(
					get_param_date('date', date("Y-m-d")),
					get_param_array_int('account_types', array(1))
			));
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
	    	
		default:
			throw new Exception(
					"ctrlAccount: operation {$_REQUEST['oper']} not supported");
	}

} 

catch(Exception $e) {
  header('HTTP/1.0 401 ' . $e->getMessage());
  die($e->getMessage());
}  
?>
