<?php

define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(__FILE__))).DS); 


require_once(__ROOT__ . "php/external/jquery-fileupload/UploadHandler.php");
require_once(__ROOT__ . "local_config/config.php");
require_once(__ROOT__ . "php/lib/csv_wrapper.php");
require_once(__ROOT__ . "php/lib/import_products.php");


require_once(__ROOT__ . 'FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start(); // Starts FirePHP output buffering
$firephp = FirePHP::getInstance(true);


/*function get_file_name() {
        return isset($_GET['file']) ? basename(stripslashes($_GET['file'])) : null;
}*/
 	

try{ 
   
	
	
 	switch (get_param('oper')) {

 		
 		case 'uploadFile':
			$options = array(	
				'upload_dir' => __ROOT__ . 'local_config/upload/',
				'accept_file_types' => '/\.(gif|jpe?g|png|csv)$/i'
			);
			$upload_handler = new UploadHandler($options);
			exit; 
 		
 		case 'parseFile':
 			$path = __ROOT__ .'local_config/upload/' . get_param('file');
			$csv = new csv_wrapper($path);
	
			$dt = $csv->parse();
			
			echo $dt->get_html_table();
	
			
			$_SESSION['import_file'] = $path; 
			
 			exit;

 		case 'getAllowedFields':
    		printXML(get_import_rights(get_param('table'))); 
    		exit; 
    		
    		
 		case 'import':
 			global $firephp; 
 			
 			$firephp->log($_SESSION['import_file'], "path");
			
 			//make map
 			$csv = new csv_wrapper($_SESSION['import_file']);
	
			$dt = $csv->parse();
			
			//$map = array('custom_product_ref'=>0, 'unit_price'=>1, 'name'=>2);
			$map = array();
			foreach($_REQUEST['table_col'] as $key => $value){
				$map[$value] = $key;  
			}
			
			$firephp->log($map, "the map");
			
			
			//data_table, dt to db map, provider_id
			$pi = new import_products($dt, $map, get_param('provider_id'));
			
			//append_new = true
			$pi->import(true);
		
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