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

function csv_filename($root, $provider_id, $provider_name)
{
    $filename = $root;
    if ($provider_name != null && $provider_name != "")
	$filename .= '_' . $provider_name;
    else
	$filename .= '_providers_' .date('Y-m-d_h:i') . '.csv'; 
    return $filename;
}

function XML_add_metadata($xml, $what, $metadata=null)
{
    $xml_out = 
	'<' . $what . '>'
	. '<timestamp>' 
	  . date('Y-m-d_h:i') 
	. '</timestamp>';
    if (isset($metadata)) {
	$xml_out .= '<' . $metadata['name'] . '>';
	foreach($metadata['data'] as $key => $value) 
	    $xml_out .= '<' . $key . '>' . $value . '</' . $key . '>';
	$xml_out .= '</' . $metadata['name'] . '>';
    }
    $xml_out .= $xml
	. '</' . $what . '>';
    return $xml_out;
}


function get_products($provider_id, $product_ids=null){
	$xml = stored_query_XML_fields('aixada_product_list_all_query', 
						   'aixada_product.name', 
						   'asc', 
						   0, 
						   1000000, 
						   'aixada_product.provider_id = ' . $provider_id);		   
	
	DBWrap::get_instance()->free_next_results();
	return $xml; 

}

function get_provider($provider_id){
	 $xml = stored_query_XML_fields('aixada_provider_list_all_query', 
						   'aixada_provider.name', 
						   'asc', 
						   0, 
						   1000000, 
						   'aixada_provider.id = ' . $provider_id);	
	DBWrap::get_instance()->free_next_results();					   
	return $xml; 

	
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
			
		    $format = get_param('format', 'csv'); // or xml
		    $file_name = get_param('fileName');
		    $destination = get_param('destination'); //to disc or online google drive
		    $make_public = get_param('makePublic', 0); //share on the web
		    $firephp->log($make_public, "make_public");
		    $provider_ids = '(' . get_param('provider_id', 0, 'array2String') . ')';
		    
		    /*$provider_ids = get_param('provider_id');

		    $xmlstr = ''; 
	 		foreach ($provider_ids as $id) {
				$xmlstr .= get_provider($id);
				if (get_param('include_products', true)){
					$xmlstr .= '<products>';
					$xmlstr .= get_products($id);
					$xmlstr .= '</products>';
				}
			}*/

		    $xml = stored_query_XML_fields('aixada_provider_list_all_query', 
						   'aixada_provider.name', 
						   'asc', 
						   0, 
						   1000000, 
						   'aixada_provider.id in ' . $provider_ids);
					   
	    	
	    	switch ($format) {
	    		case 'csv':
					printCSV(XML2csv($xml), $file_name .".csv", $make_public);
					exit;

			    case 'xml':
					$metadata = array();
					/*$metadata = array( 'name' => 'provider', 
						   'data' => array( 'provider_id' => $provider_id,
								    'provider_name' => $provider_name));*/
			    	$xmlstr = XML_add_metadata($xml, 'provider_info'); 
					downloadXML($xmlstr, $file_name.'.xml', $make_public);
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
					printCSV(XML2csv($xml), csv_filename($what, $provider_id, $provider_name));
					exit;
		
			    case 'xml':
					$metadata = array( 'name' => 'provider', 
						   'data' => array( 'provider_id' => $provider_id,
								    'provider_name' => $provider_name));
					printXML(XML_add_metadata($xml, $what, $metadata));
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


	case 'orderableProductsForDateRange':
	    $from_date = get_param('from_date', date('Y-m-d', strtotime('now')));
	    $to_date = get_param('to_date', date('Y-m-d', strtotime('now +1 month'))); 
	    $provider_id = get_param('provider_id', 0);
	    $format = get_param('format', 'csv'); // or xml
	    $xml = stored_query_XML_fields('get_orderable_products_for_dates',
					   $from_date, 
					   $to_date,
					   $provider_id);

	    switch ($format) {
	    case 'csv':
		printCSV(XML2csv($xml), 'orderable_products_' . $provider_id . '_' . $from_date . '_' . $to_date . '.csv');
		exit;

	    case 'xml':
		$metadata = array( 'name' => 'provider_and_range', 
				   'data' => array( 'provider_id' => $provider_id,
						    'from_date' => $from_date,
						    'to_date' => $to_date ));
		printXML(XML_add_metadata($xml, 'orderable_products', $metadata));
		exit;

	    default:
		throw new Exception('Export file format"' . $format . '" not supported');
	    }
	    
	default:
	    throw new Exception("ctrl Import: operation {$_REQUEST['oper']} not supported");
    
  	}

} 

catch(Exception $e) {
  header('HTTP/1.0 401 ' . $e->getMessage());
  die($e->getMessage());
}  
?>