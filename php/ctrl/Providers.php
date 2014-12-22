<?php


define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(__FILE__))).DS); 

require_once(__ROOT__ . "local_config/config.php");
require_once(__ROOT__ . "php/inc/database.php");
require_once(__ROOT__ . "php/utilities/general.php");



if (!isset($_SESSION)) {
    session_start();
 }
 

try{

    switch ($_REQUEST['oper']) {

	       
		case 'getProviders':
			printXML(stored_query_XML_fields('get_provider_listing', get_param('provider_id',0), get_param('all',0)));
			exit;

        case 'deactivateProvider':
            echo do_stored_query('change_active_status_provider', 0, get_param('provider_id'));
            exit;

        case 'activateProvider':
            echo do_stored_query('change_active_status_provider', 1, get_param('provider_id'));
            exit;
			

    default: 
        throw new Exception('ctrlProviders.php: Operation ' . $_REQUEST['oper'] . ' not supported.');
    }
} 

catch(Exception $e) {
    header('HTTP/1.0 401 ' . $e->getMessage());
    die($e->getMessage());
}  
?>