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