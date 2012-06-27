<?php


require_once("local_config/config.php");
require_once("inc/database.php");
require_once("utilities.php");


if (!isset($_SESSION)) {
    session_start();
}

try{
	

    switch (get_param('oper')) {
    	
    	//returns a list of all purchases for given uf. 
    	case 'getShopListingForUf':
    		echo get_purchase_in_range(get_param('filter'), get_param('uf_id',0));
    		exit; 
    	
    		
    default:  
    	 throw new Exception("ctrlShop: oper={$_REQUEST['oper']} not supported");  
        break;
    }


} 

catch(Exception $e) {
    header('HTTP/1.0 401 ' . $e->getMessage());
    die ($e->getMessage());
}  


?>