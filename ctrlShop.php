<?php


require_once("local_config/config.php");
require_once("inc/database.php");
require_once("utilities.php");
require_once("utilities_shop.php");


if (!isset($_SESSION)) {
    session_start();
}

try{

    switch (get_param('oper')) {
    	
    	//returns a list of all purchases for given uf. 
    	case 'getShopListing':
    		echo get_purchase_in_range(get_param('filter'), get_param('uf_id',0), get_param('fromDate',0), get_param('toDate',0), get_param('steps',0), get_param('range',0)   );
    		exit;   

    	//returns shopping cart(s). 
    	case 'getShopDetail':
    		printXML(stored_query_XML_fields('get_shop_cart', get_param('date',0), get_session_uf_id(), get_param('shop_id',0),1));
    		exit;

    	//updates the stock for given product. The "quantity" is added to current_stock
    	case 'addStock':
    		echo do_stored_query('add_stock', get_param('product_id'), get_param('quantity'), get_session_user_id(), get_param('description','stock added'));
			exit;

		//corrects stock for product. The "quantity" replaces current_stock. 
    	case 'correctStock':
    		echo do_stored_query('correct_stock', get_param('product_id'), get_param('quantity'), get_session_user_id());
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