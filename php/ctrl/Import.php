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
 			
 			
 			
  		
	  default:
	    throw new Exception("ctrl Import: operation {$_REQUEST['oper']} not supported");
    
  	}

} 

catch(Exception $e) {
  header('HTTP/1.0 401 ' . $e->getMessage());
  die($e->getMessage());
}  
?>