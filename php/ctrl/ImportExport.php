<?php

define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(__FILE__))).DS); 


require_once(__ROOT__ . "php/external/jquery-fileupload/UploadHandler.php");
require_once(__ROOT__ . "local_config/config.php");
//require_once(__ROOT__ . "php/lib/csv_wrapper.php");
require_once(__ROOT__ . "php/lib/import_products.php");
require_once(__ROOT__ . "php/utilities/general.php");

 require(__ROOT__ . 'php/external/spreadsheet-reader/php-excel-reader/excel_reader2.php');
 require(__ROOT__ . 'php/external/spreadsheet-reader/SpreadsheetReader.php');



require_once(__ROOT__ . 'php/external/FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start(); // Starts FirePHP output buffering
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

function csv_filename($root, $provider_id, $provider_name)
{
    $filename = $root;
    if ($provider_name != null)
	$filename .= '_' . $provider_name;
    else
	$filename .= '_provider_' . $provider_id;
    $filename .= '_' . date('Y-m-d_h:i');
    $filename .= '.csv';
    return $filename;
}

function XML_add_metadata($xml, $what, $provider_id=null, $provider_name=null)
{
    return 
	'<' . $what . '>'
	. '<timestamp>' 
	  . date('Y-m-d_h:i') 
	. '</timestamp>'
	. (($provider_id != null || $provider_name != null) ? 
	   '<provider>'
	   .   '<provider_id>' . $provider_id . '</provider_id>'
	   .   '<provider_name>' . $provider_name . '</provider_name>'
	   . '</provider>'
	   : '')
	. $xml
	. '</' . $what . '>';
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
 			
 			
	case 'exportProviderInfo':
	    $format = get_param('format', 'csv'); // or xml
	    $provider_id = get_param('provider_id',0);
	    $provider_name = get_param('provider_name', null);
	    $xml = stored_query_XML_fields('aixada_provider_list_all_query', 'aixada_provider.name', 'asc', 0, 1, 'aixada_provider.id = ' . $provider_id);
	    $what = 'product_info';
	    switch ($format) {
	    case 'csv':
		printCSV(XML2csv($xml),  csv_filename($what, $provider_id, $provider_name));
		exit;

	    case 'xml':
		printXML(XML_add_metadata($xml, $what, $provider_id, $provider_name));
		exit;
	    default:
		throw new Exception('Export file format"' . $format . '" not supported');
	    }
	    break;


	case 'exportProviderProducts':
	    $format = get_param('format', 'csv'); // or xml
	    $provider_id = get_param('provider_id',0);
	    $provider_name = get_param('provider_name', null);
	    $xml = stored_query_XML_fields('aixada_product_list_all_query', 
					   'aixada_product.name', 
					   'asc', 
					   0, 
					   1000000, 
					   'aixada_product.provider_id = ' . $provider_id);
	    $what = 'product_info';
	    switch ($format) {
	    case 'csv':
		printCSV(XML2csv($xml),  csv_filename($what, $provider_id, $provider_name));
		exit;

	    case 'xml':
		printXML(XML_add_metadata($xml, $what, $provider_id, $provider_name));
		exit;
		
	    default:
		throw new Exception('Export file format"' . $format . '" not supported');
	    }
	    break;


	case 'exportProducts':
	    $format = get_param('format', 'csv'); // or xml
	    $ids = '(' . get_param('product_ids', 0, 'array2String') . ')';
	    $xml = stored_query_XML_fields('aixada_product_list_all_query', 
					   'aixada_product.name', 
					   'asc', 
					   0, 
					   1000000, 
					   'aixada_product.id in ' . $ids);
	    switch ($format) {
	    case 'csv':
		printCSV(XML2csv($xml), 'product_list.csv');
		exit;

	    case 'xml':
		printXML(XML_add_metadata($xml, 'product_list'));
		exit;

	    default:
		throw new Exception('Export file format"' . $format . '" not supported');
	    }
	    break;


	case 'exportMembers':
	    $format = get_param('format', 'csv'); // or xml
	    $xml = stored_query_XML_fields('aixada_member_list_all_query', 
					   'aixada_member.name', 
					   'asc', 
					   0, 
					   1000000, 
					   'active=1');
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
	    get_param('account_id', -3);
	    get_param('from_date', null); // null means one month ago
	    get_param('to_date', null); // null means today
	    exit;
	    
	default:
	    throw new Exception("ctrl Import: operation {$_REQUEST['oper']} not supported");
    
  	}

} 

catch(Exception $e) {
  header('HTTP/1.0 401 ' . $e->getMessage());
  die($e->getMessage());
}  
?>