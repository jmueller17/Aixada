<?php

define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(__FILE__))).DS); 


require_once(__ROOT__ . "php/external/jquery-fileupload/UploadHandler.php");
require_once(__ROOT__ . "local_config/config.php");

require_once(__ROOT__ . "php/lib/import_products.php");
require_once(__ROOT__ . "php/lib/export_providers.php");
require_once(__ROOT__ . "php/lib/export_products.php");
require_once(__ROOT__ . "php/utilities/general.php");

 require(__ROOT__ . 'php/external/spreadsheet-reader/php-excel-reader/excel_reader2.php');
 require(__ROOT__ . 'php/external/spreadsheet-reader/SpreadsheetReader.php');



require_once(__ROOT__ . 'php/external/FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start();
$firephp = FirePHP::getInstance(true);



function parseSpreadSheet($path){
	
	$row = 0;
  	$_data_table = null; 						
									
 	$Reader = new SpreadsheetReader($path);
	foreach ($Reader as $Row){
		    
	  	$_data_table[$row++] = $Row; 

	}						
									
	return new data_table($_data_table, false);
} 	





try{ 
   
	global $firephp; 
	
 	switch (get_param('oper')) {

 		
 		case 'uploadFile':
			$options = array(	
				'upload_dir' => __ROOT__ . 'local_config/upload/',
				'accept_file_types' => '/\.(gif|jpe?g|png|csv|xlsx|xls|ods)$/i'
			);
			$upload_handler = new UploadHandler($options);
			exit; 
 		
 		case 'parseFile':
 			$path = __ROOT__ .'local_config/upload/' . get_param('file');

			$dt = parseSpreadSheet($path);
			
			echo $dt->get_html_table();
	
			
			$_SESSION['import_file'] = $path; 
			
 			exit;

 		case 'getAllowedFields':
    		printXML(get_import_rights(get_param('table'))); 
    		exit; 
    		
    		
 		case 'import':
			$dt = parseSpreadSheet($_SESSION['import_file']);
			//$map = array('custom_product_ref'=>0, 'unit_price'=>1, 'name'=>2);
			$map = array();
			foreach($_REQUEST['table_col'] as $key => $value){
				$map[$value] = $key;  
			}
			$firephp->log($map, "the map");
			$pi = new import_products($dt, $map, get_param('provider_id'));
			$pi->import(get_param('new_items', false));
		
			echo 1; 
 			exit; 
 			
 		//exports provider info only. Should have option for including provider products?!!	
		case 'exportProviderInfo':
		    $ep = new export_providers(get_param('fileName'), get_param('provider_id',0));
		    $ep->export(get_param('makePublic', 0), get_param('format', 'csv'), get_param('email',''), get_param('password',''));
	    	break;


		case 'exportProducts':			
			$ep = new export_products(get_param('fileName'), get_param('provider_id',0), get_param('product_ids',0));
		    $ep->export(get_param('makePublic', 0), get_param('format', 'csv'), get_param('email',''), get_param('password',''));
	    	break;
	    	
		case 'orderableProductsForDateRange':
			$ep = new export_dates4product(get_param('fileName'), get_param('provider_id'), get_param('from_date',''), get_param('to_date',''));
			$ep->export(get_param('makePublic', 0), get_param('format', 'csv'), get_param('email',''), get_param('password',''));		    
			break;


		/*
		case 'exportMembers':
		    $format = get_param('format', 'csv'); // or xml
		    $xml = stored_query_XML_fields('aixada_member_list_all_query', 
						   'aixada_member.uf_id', 
						   'desc', 
						   0, 
						   1000000, 
						   'aixada_member.active=1');
		    switch ($format) {
			    case 'csv':
					printCSV(XML2csv($xml), 'member_list.csv');
					exit;
	
			    case 'xml':
					printXML(XML_add_metadata($xml, 'member_list'));
					exit;
	
			    default:
					throw new Exception('Export file format"' . $format . '" not supported');
		    }
		    
		   	break;


	case 'exportAccountMovements':
	    // require user to be econo-legal or hacker
	    if ($_SESSION['userdata']['current_role'] != 'Hacker Commission' and
		$_SESSION['userdata']['current_role'] != 'Econo-Legal Commission') {
		throw new Exception('You do not have sufficient privileges to see account movements');
	    }
	    $account_id = get_param('account_id', -3);
	    $from_date = get_param('from_date', strtotime('-1 month'));
	    $from_date_day = date('Y-m-d', $from_date);
	    $to_date = get_param('to_date', strtotime('now')); 
	    $to_date_day = date('Y-m-d', $to_date);
	    $to_date_next_day = date('Y-m-d', strtotime($to_date_day . ' +1 day'));
	    $format = get_param('format', 'csv'); // or xml
	    $xml = stored_query_XML_fields('aixada_account_list_all_query', 
					   'ts', 
					   'desc', 
					   0, 
					   1000000, 
					   'account_id=' . $account_id . ' and ts between "' . $from_date_day . '" and "' . $to_date_next_day . '"');
	    switch ($format) {
	    case 'csv':
		printCSV(XML2csv($xml), 'account_movements_' . $account_id . '_' . $from_date_day . '_' . $to_date_day . '.csv');
		exit;

	    case 'xml':
		$metadata = array( 'name' => 'account', 
				   'data' => array( 'account_id' => $account_id,
						    'from_date' => $from_date_day,
						    'to_date' => $to_date_day ));
		printXML(XML_add_metadata($xml, 'account_movements', $metadata));
		exit;

	    default:
		throw new Exception('Export file format"' . $format . '" not supported');
	    }
	    break;
	    */
	    
	default:
	    throw new Exception("ctrl Import: operation {$_REQUEST['oper']} not supported");
    
  	}

} 

catch(Exception $e) {
  header('HTTP/1.0 401 ' . $e->getMessage());
  die($e->getMessage());
}  
?>