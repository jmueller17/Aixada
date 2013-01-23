<?php include "php/inc/header.inc.php" ?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<style>
		table, td {border:1px solid blue; border-collapse:collapse; padding:4px;}
		
	</style>
</head>
<body>
<?php

//require_once('php/lib/abstract_import_export_format.php');
require_once('php/lib/csv_wrapper.php');
require_once('php/lib/import_products.php');




		try {
			$csv = new csv_wrapper('local_config/tmp/boletsPricelist.csv');
	
			$dt = $csv->parse();
		

			$map = array('custom_product_ref'=>0, 'unit_price'=>1, 'name'=>2);
		
			//data_table, dt to db map, provider_id
			$pi = new import_products($dt, $map, 49);
			
			//append_new = true
			$pi->import(true);
			
			
		} catch(Exception $e) {
    		header('HTTP/1.0 401 ' . $e->getMessage());
    		die ($e->getMessage());
		}   
	

?>
</body>
</html>