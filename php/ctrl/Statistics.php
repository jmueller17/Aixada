<?php


define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(__FILE__))).DS); 

require_once(__ROOT__ . "php/utilities/statistics.php");
require_once(__ROOT__ . "php/utilities/visualization.php");
require_once __ROOT__ . "php/utilities/general.php";

try{
    validate_session(); // The user must be logged in.
    
    switch ($_REQUEST['oper']) {
	    case 'uf':
	        echo make_active_time_lines('uf');
	        exit;

	    case 'provider':
	        echo make_active_time_lines('provider');
	        exit;

	    case 'product':
	        echo make_active_time_lines('product');
	        exit;
	
	    case 'balances':
	        echo make_balances();
	        exit;

            case 'product_prices_times_years':
		echo product_prices_times_years($_REQUEST['product_id_array'], $_REQUEST['year_array']);
		exit;

    default:
        throw new Exception("ctrlStatistics: operation {$_REQUEST['oper']} not supported");
        
    }
    
} 

catch(Exception $e) {
    header('HTTP/1.0 401 ' . $e->getMessage());
    die($e->getMessage());
}  

?>