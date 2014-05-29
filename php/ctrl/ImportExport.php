<?php

define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(__FILE__))).DS); 


require_once(__ROOT__ . "php/external/jquery-fileupload/UploadHandler.php");
require_once(__ROOT__ . "local_config/config.php");

require_once(__ROOT__ . "php/lib/import_products.php");
require_once(__ROOT__ . "php/lib/import_providers.php");

require_once(__ROOT__ . "php/lib/export_providers.php");
require_once(__ROOT__ . "php/lib/export_products.php");
require_once(__ROOT__ . "php/lib/export_cart.php");
require_once(__ROOT__ . "php/lib/export_order.php");
require_once(__ROOT__ . "php/lib/export_dates4products.php");
require_once(__ROOT__ . "php/lib/export_members.php");
require_once(__ROOT__ . "php/utilities/general.php");

 

require_once(__ROOT__ . 'php/lib/gdrive.php'); //Zend Gdata throws stupid PHP Strict Warning 


require_once(__ROOT__ . 'php/external/FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start();
$firephp = FirePHP::getInstance(true);



try{ 
   
	global $firephp; 
	
 	switch (get_param('oper')) {

 		//upload files from clients computer
 		case 'uploadFile':
			$options = array(	
				'upload_dir' => __ROOT__ . 'local_config/upload/',
				'accept_file_types' => '/\.(gif|jpe?g|png|csv|xlsx|xls|ods|xml|tsv|txt)$/i'
			);
			$upload_handler = new UploadHandler($options);
			exit; 
			
		//fetch file from online URL	
 		case 'fetchFile':
 			$saveFileTo = __ROOT__ . 'local_config/upload/tmpdownload.csv'; 			
 	 		gDrive::fetchFile(get_param('url'), 'csv', $saveFileTo);
 	 		echo $saveFileTo; 
 			exit; 
 		
 		//parse the file and retur HTML table
 		case 'parseFile':
 			$path = get_param('fullpath','');
 			if ($path == ''){ 
	 			$path = __ROOT__ .'local_config/upload/' . get_param('file');
 			}
 			
 			$dt = abstract_import_manager::parse_file($path, get_param('import2Table',''));
			echo $dt->get_html_table();
			$_SESSION['import_file'] = $path; 
 			exit;

 	
    		
    		
 		case 'import':
 			
 			
 			//$map = array('custom_product_ref'=>0, 'unit_price'=>1, 'name'=>2);
		 	$map = array();
			foreach($_REQUEST['table_col'] as $key => $value){
				$map[$value] = $key;
			}
 			
 			 
 			switch(get_param('import2Table')){
 				case 'aixada_product':
 					$dt = abstract_import_manager::parse_file($_SESSION['import_file'], 'aixada_product');
 					$pi = new import_products($dt, $map, get_param('provider_id'));
					$pi->import(get_param('append_new', false));
 					exit; 
 				
 				case 'aixada_product_orderable_for_date':
 					exit;  
 					
 				case 'aixada_provider':
 					$dt = abstract_import_manager::parse_file($_SESSION['import_file'], 'aixada_provider');
 					$pi = new import_providers($dt, $map);
 					$pi->import(get_param('append_new', false));
 					exit; 
 				
 			}
			

			echo 1; 
 			exit; 
 	
 		case 'getAllowedFields':
    		printXML(get_import_rights(get_param('table'))); 
    		exit; 
 			
 		//exports provider info only. Should have option for including provider products?!!	
		case 'exportProviderInfo':
			$publish = (get_param('makePublic','off')=='on')? 1:0; 	
		    $ep = new export_providers(get_param('exportName'), get_param('providerId',0), get_param('includeProducts',0));
		    $ep->export($publish, get_param('exportFormat', 'csv'), get_param('email',''), get_param('password',''));
	    	break;


		case 'exportProducts':	
			$publish = (get_param('makePublic','off')=='on')? 1:0; 		
			$ep = new export_products(get_param('exportName'), get_param('providerId',0), get_param('productIds',0));
		    $ep->export($publish, get_param('exportFormat', 'csv'), get_param('email',''), get_param('password',''));
	    	break;
	    	
		case 'orderableProductsForDateRange':
			$publish = (get_param('makePublic','off')=='on')? 1:0; 
			$ep = new export_dates4products(get_param('exportName'), get_param('providerId'), get_param('fromDate',''), get_param('toDate',''));
			$ep->export($publish, get_param('exportFormat', 'csv'), get_param('email',''), get_param('password',''));		    
			break;
			
		case 'exportMembers':
			$publish = (get_param('makePublic','off')=='on')? 1:0; 
			$active = (get_param('onlyActiveUfs','off')=='on')? 1:0;
			$ep = new export_members(get_param('exportName'), $active);
			$ep->export($publish, get_param('exportFormat', 'csv'), get_param('email',''), get_param('password',''));
			break;
			
		case 'exportCart':
			$publish = (get_param('makePublic','off')=='on')? 1:0;
			$ep = new export_cart(get_param('exportName'), get_param('shopId'));
			$ep->export($publish, get_param('exportFormat', 'csv'), get_param('email',''), get_param('password',''));
			break;
			
		case 'exportOrder':
			$publish = (get_param('makePublic','off')=='on')? 1:0;
			$ep = new export_order(get_param('exportName'), get_param('order_id',0), get_param('provider_id',0), get_param('date_for_order',0));
			$ep->export($publish, get_param('exportFormat', 'csv'), get_param('email',''), get_param('password',''));
			break;


	/*
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