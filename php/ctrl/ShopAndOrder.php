<?php

define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(__FILE__))).DS); 


require_once(__ROOT__ . "local_config/config.php");
require_once(__ROOT__ . "php/inc/database.php");
require_once(__ROOT__ . "php/utilities/general.php");
require_once(__ROOT__ . "php/utilities/shop_and_order.php");




if (!isset($_SESSION)) {
    session_start();
}

DBWrap::get_instance()->debug = true;

try{
	
	
    // first we process those requests that don't need to construct a cart manager
    switch (get_param('oper')) {
    
	    /**
	     *  retrieves provider and category selects for Shop or Order
	     */
	    case 'getOrderProviders':
	    	printXML(stored_query_XML_fields('get_orderable_providers_for_date', get_param('date')));
	    	exit;
	    
	   	case 'getShopProviders':
	    	printXML(stored_query_XML_fields('get_shop_providers'));
	    	exit;
	    	
	    case 'getOrderCategories':
	    	printXML(stored_query_XML_fields('get_orderable_categories_for_date', get_param('date')));
	    	exit;

	    //retrieves all categories of all products active; optional the date parameter
	    //would retrieve all categories for stock products and orderable for the given date. 	
	    case 'getShopCategories':
	    	printXML(stored_query_XML_fields('get_shop_categories_for_date', 0));
	    	exit;
	    	
	    case 'getStockProviders':
	    	printXML(stored_query_XML_fields('get_stock_providers'));
	    	exit;
    	

	    /**
	     * retrieve products for providers. We assume
	     * if date = 0, look for all active products (stock + orderable)
	     * else 		look for orderable products for specified date
	     * if product_id > 0 	get specific product
	     * elseif provider_id > 0   get products according to provider_id
	     * elseif category_id > 0 	get products according to category_id
	     * elseif like != '' 		search product names
	     */
	    case 'getOrderProducts':
	    case 'getToOrderProducts':
	    	printXML(stored_query_XML_fields('get_products_detail',get_param('provider_id',0), get_param('category_id',0), get_param('like',''), get_param('date'), get_param('all',0), get_param('product_id',0)));
	    	exit;
	
	    case 'getShopProducts':
	    	printXML(stored_query_XML_fields('get_products_detail',get_param('provider_id',0), get_param('category_id',0), get_param('like',''), get_param('date',0), get_param('all',0), get_param('product_id',0)));
	    	exit;
	
  		case 'getToShopProducts':
	    	printXML(stored_query_XML_fields('get_products_detail',get_param('provider_id',0), get_param('category_id',0), get_param('like',''), 0, get_param('all',0), -1));
	    	exit;
	    	
  		case 'getPreorderableProducts':
	        printXML(stored_query_XML_fields('get_preorderable_products'));
	        exit;
	        
  		case 'getProductDetail':
  			printXML(stored_query_XML_fields('get_products_detail', 0,0,'',0,get_param('all',1),get_param('product_id')));
			exit;	        
	

	   	/**
	   	 * retrieves the shop | order items for the logged in user. 
	   	 */
  		case 'getOrderCart':
  			printXML(stored_query_XML_fields('get_order_cart', get_param('date'), get_session_uf_id()));
  			exit;
  			
  		case 'getShopCart':
  			printXML(stored_query_XML_fields('get_shop_cart', get_param('date'), get_session_uf_id(),0,0)); 
			exit; 

    	default:  
    	 //throw new Exception("ctrlShopAndOrder: oper={$_REQUEST['oper']} not supported");  
        break;
    }

  
    // now come  the requests that need a cart manager
    switch (get_param('what', $default='')) {
	    case 'Shop':
	        require_once(__ROOT__ . "php/lib/shop_cart_manager.php");
	        $cm = new shop_cart_manager($_SESSION['userdata']['uf_id'], get_param('date')); 
	        break;
	      
	    case 'Order':
	        require_once(__ROOT__ . "php/lib/order_cart_manager.php");
	        $cm = new order_cart_manager($_SESSION['userdata']['uf_id'], get_param('date')); 
	        break;
	      
	    case 'favorite_order':
	        require_once(__ROOT__ . "php/lib/favorite_order_cart_manager.php");
	        $cm = new favorite_order_cart_manager($_SESSION['userdata']['uf_id'], get_param('name')); 
	        break;
	      
	    default:
	        throw new Exception("ctrlShopAndOrder: request what={$_REQUEST['what']} not supported");
    }
  


    switch($_REQUEST['oper']) {
    
	    case 'commit':
	        try {
				$emptyArr = array();
	        	$cid = $cm->commit(	get_param('quantity',$emptyArr), 
	        						get_param('product_id',$emptyArr), 
	        						get_param('iva_percent',$emptyArr), 
	        						get_param('rev_tax_percent',$emptyArr), 
	        						get_param('order_item_id',$emptyArr), 
	        						get_param('cart_id',0), 
	        						get_param('ts_last_saved',0),
	        						get_param('preorder',$emptyArr), 
	        						get_param('price', $emptyArr),
	        						get_param('notes', $emptyArr)
	        	);
	            echo ($cid);
	        }
	        catch(Exception $e) {
	            header('HTTP/1.0 401 ' . $e->getMessage());
	            die ($e->getMessage());
	        }  
	        break;
	
	    default:
	        throw new Exception("ctrlShopAndOrder: variable oper not set in query");
	    
	    }
} 

catch(Exception $e) {
    header('HTTP/1.0 401 ' . $e->getMessage());
    die ($e->getMessage());
}  


?>