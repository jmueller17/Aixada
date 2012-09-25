<?php


define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(__FILE__))).DS); 

require_once(__ROOT__ . "local_config/config.php");
require_once(__ROOT__ . "php/inc/database.php");
require_once(__ROOT__ . "php/utilities/general.php");


if (!isset($_SESSION)) {
    session_start();
 }

DBWrap::get_instance()->debug = true;

try{
   

    switch ($_REQUEST['oper']) {

	    case 'configMenu':
	        printXML(get_config_menu($_REQUEST['user_role']));
	        exit;
	        
	    case 'getFieldOptions':
	        echo get_field_options_live($_REQUEST['table'], $_REQUEST['field1'], $_REQUEST['field2']);
	        exit;
	
	        
	    case 'getRoles':
	        printXML(get_roles());
	        exit;
	
	    case 'getCommissions':
	        printXML(get_commissions());
	        exit;

		case 'getExistingLanguages':
	        printXML(existing_languages_XML());
	        exit;
	        
		case 'getActiveProviders':
			printXML(stored_query_XML_fields('get_all_active_providers'));
			exit;
			
		case 'getExistingThemes':
			printXML(get_existing_themes_XML());
			exit;
        

        
        
    case 'getProductsBelowMinStock':
        printXML(stored_query_XML_fields('products_below_min_stock'));
        exit;

    case 'addStock':
        printXML(stored_query_XML_fields('add_stock', $_REQUEST['product_id'], $_REQUEST['delta_amount'], $_REQUEST['operator_id'], $_REQUEST['description']));
        exit;

    case 'stockMovements':
        printXML(stored_query_XML_fields('stock_movements', $_REQUEST['product_id'], $date, $_REQUEST['num_rows']));
        exit;

    

    

    

    default:
        throw new Exception('ctrlSmallQ.php: Operation ' . $_REQUEST['oper'] . ' not supported.');
    }
} 

catch(Exception $e) {
    header('HTTP/1.0 401 ' . $e->getMessage());
    die($e->getMessage());
}  
?>