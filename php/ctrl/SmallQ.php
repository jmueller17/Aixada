<?php


define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(__FILE__))).DS); 

require_once(__ROOT__ . "local_config/config.php");
require_once(__ROOT__ . "php/inc/database.php");
require_once(__ROOT__ . "php/utilities/general.php");
require_once(__ROOT__ . 'local_config/lang/'.get_session_language() . '.php');

include(__ROOT__ . "php/external/mpdf54/mpdf.php");


if (!isset($_SESSION)) {
    session_start();
 }

global $Text; 

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
			
		case 'printPDF':
			$mpdf=new mPDF(); 
			$mpdf->WriteHTML(get_param('htmlStr'));
			$defaultFileName = $Text['coop_name'].date('Y-m-d', strtotime('Today')).'.pdf';
			// 'F' writes a file into local_config/reports and sends back the name ; 
			//'D' sends back binary pdf and forces download
			if (get_param('outParam') == 'F'){
				$fileName = __ROOT__. 'local_config/reports/'.get_param('fileName', $defaultFileName).'.pdf';
			} else {
				$fileName = get_param('fileName', $defaultFileName).'.pdf';
				
			}
			$mpdf->Output($fileName,get_param('outParam', 'D'));
			echo $fileName;
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