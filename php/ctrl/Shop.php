<?php
define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(__FILE__))).DS); 

require_once(__ROOT__ . "local_config/config.php");
require_once(__ROOT__ . "php/inc/database.php");
require_once(__ROOT__ . "php/utilities/general.php");
require_once(__ROOT__ . "php/utilities/shop.php");

if (!isset($_SESSION)) {
    session_start();
}

try{

    switch (get_param('oper')) {
    	
    	//returns a list of all purchases for given uf. 
    	case 'getShopListing':
    		echo get_purchase_in_range(get_param('filter'), get_param('uf_id',0), get_param('fromDate',0), get_param('toDate',0), get_param('steps',0), get_param('range',0), get_param('limit','')   );
    		exit;   

    	//returns shopping cart(s) for logged user!  
    	case 'getShopCart':
    		printXML(stored_query_XML_fields('get_shop_cart', get_param('date',0), get_session_uf_id(), get_param('shop_id',0),1), get_param('validated',0));
    		exit;
    		

    	//updates the stock for given product. The "quantity" is added to current_stock
    	case 'addStock':
    		echo do_stored_query('add_stock', get_param('product_id'), get_param('quantity'),  get_session_user_id(), get_param('movement_type_id',4), get_param('description','stock added'));
			exit;

		//corrects stock for product. The "quantity" replaces current_stock. 
    	case 'correctStock':
    		echo do_stored_query('correct_stock', get_param('product_id'), get_param('quantity'), get_param('description',''), get_param('movement_type_id',1), get_session_user_id());
			exit;
    		
		case 'getProductsBelowMinStock':
	    	printXML(query_XML_noparam('products_below_min_stock'));
	    	exit;
	    
	    case 'stockMovements':
        	printXML(stored_query_XML_fields('stock_movements', get_param('product_id',0), get_param('provider_id',0), get_param('from_date',""), get_param('to_date',""), get_param('limit', 0)));
        	exit;
        	
	    case 'getTotalSalesByProviders':
	    	printXML(stored_query_XML_fields('get_purchase_total_by_provider', get_param('from_date',0), get_param('to_date',0), get_param('provider_id',0), get_param('groupby','')  ));
			exit;
			
	    case 'getDetailSalesByProvider':
	    	printXML(stored_query_XML_fields('get_purchase_total_of_products', get_param('from_date',0), get_param('to_date',0), get_param('provider_id',0), get_param('validated',1), get_param('groupby','')));
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