<?php

//require_once('FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
//$firephp = FirePHP::getInstance(true);
//ob_start(); // Starts FirePHP output buffering

require_once("local_config/config.php");
require_once("inc/database.php");
require_once("utilities.php");
require_once("utilities_orders.php");


if (!isset($_SESSION)) {
    session_start();
}

try{
	
	
    // first we process those requests that don't need to construct a cart manager
    switch (get_param('oper')) {
    	
    	case 'getOrdersListing':
    		echo get_orders_in_range(get_param('filter'), get_param('limit',117111451111));
    		exit; 
    	    		
    	case 'getOrderedProductsList':
    		printXML(stored_query_XML_fields('get_ordered_products_list', get_param('order_id')));
    		exit;
    		
    	case 'getProductQuantiesForUfs':
    		printXML(stored_query_XML_fields('get_order_item_detail', get_param('order_id')));
    		exit;
    		
    	case 'editQuantity':
    		$splitParams = explode("_", get_param('product_uf'));
    		$ok = do_stored_query('modify_order_item_detail', get_param('order_id'), $splitParams[0], $splitParams[1] , get_param('quantity'), 'null');
    		if ($ok){
	    		echo get_param('quantity');
    		} else {
    			throw new Exception("An error occured during saving the new quantity!!");      			
    		}
    		exit;
    		
    	case 'toggleProduct':
    		echo do_stored_query('modify_order_item_detail', get_param('order_id'), get_param('product_id'), 0, 0, get_param('has_arrived')  ); 
    		exit;
    		
    default:  
    	 throw new Exception("ctrlOrders: oper={$_REQUEST['oper']} not supported");  
        break;
    }


} 

catch(Exception $e) {
    header('HTTP/1.0 401 ' . $e->getMessage());
    die ($e->getMessage());
}  


?>