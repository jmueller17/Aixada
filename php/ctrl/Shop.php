<?php


require_once("local_config/config.php");
require_once("php/inc/database.php");
require_once("utilities.php");
require_once("utilities_shop.php");


if (!isset($_SESSION)) {
    session_start();
}

try{

    switch (get_param('oper')) {
    	
    	//returns a list of all purchases for given uf. 
    	case 'getShopListingForUf':
    		echo get_purchase_in_range(get_param('filter'), get_param('uf_id'), get_param('fromDate',0), get_param('toDate',0), get_param('steps',0), get_param('range',0)   );
    		exit;   

    	case 'getShopDetail':
    		printXML(stored_query_XML_fields('get_shop_cart', get_param('date',0), get_session_uf_id(), get_param('shop_id',0),1));
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