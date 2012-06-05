<?php

//require_once('FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start(); // Starts FirePHP output buffering

require_once("local_config/config.php");
require_once("inc/database.php");
require_once("utilities.php");
require_once("utilities_shop_and_order.php");

//$firephp = FirePHP::getInstance(true);

if (!isset($_SESSION)) {
    session_start();
}

DBWrap::get_instance()->debug = true;

try{
    $uf_logged_in = $_SESSION['userdata']['uf_id'];
    $what         = (isset($_REQUEST['what']) ? strtolower($_REQUEST['what']) : '');
    $the_date     = (isset($_REQUEST['date']) ? $_REQUEST['date'] : 0);

    // first we process those requests that don't need to construct a cart manager
    switch ($_REQUEST['oper']) {
    
    case 'getOrderProviders':
    	printXML(stored_query_XML_fields('get_orderable_providers_for_date', $the_date));
    	exit;
    	
    case 'getShopProviders':
    	printXML(stored_query_XML_fields('get_shop_providers_for_date', $the_date));
    	exit;
    	
    case 'getProducts':
    	printXML(stored_query_XML_fields('get_products_for_provider',$_REQUEST['provider_id'], $the_date);
    	exit;
    	
    /*case 'listProviders':
        printXML(stored_query_XML_fields('providers_with_active_products_for_' . $what, $the_date));
        exit;*/
    /*case 'get10Dates':
        printXML(get_10_sales_dates_XML($the_date));
        exit;*/
	
    case 'listCategories':
        printXML(stored_query_XML_fields('product_categories_for_' . $what, $the_date));
        exit;
        

    /*case 'listProducts':
        if (isset($_REQUEST['provider_id']))
            printXML(stored_query_XML_fields('products_for_' . $what . '_by_provider', 
                                             $_REQUEST['provider_id'], $uf_logged_in, $the_date));
        else if (isset($_REQUEST['category_id']))
            printXML(stored_query_XML_fields('products_for_' . $what . '_by_category', 
                                             $_REQUEST['category_id'], $uf_logged_in, $the_date));
        else throw new Exception("You can only search for products by provider or category.");
        exit;*/
    
    case 'listProductsLike':
        printXML(stored_query_XML_fields('products_for_' . $what . '_like', 
                                         $_REQUEST['like'], $uf_logged_in, $the_date));
        exit;
    
    case 'getOrderItemsForDate':
        printXML(stored_query_XML_fields('products_for_order_by_date', $the_date, $uf_logged_in));
        exit;

    case 'getOrderItemsByDateAndProvider':
        printXML(stored_query_XML_fields('products_for_order_by_date_and_provider', $the_date, $_REQUEST['provider_id']));
        exit;

    case 'getShopItemsForDate':
        printXML(stored_query_XML_fields('products_for_shopping', $the_date, $uf_logged_in));
        exit;

    case 'getShopItemsForDateAndUf':
        printXML(stored_query_XML_fields('products_for_shopping', $the_date, $_REQUEST['uf']));
        exit;

    case 'makeFavoriteOrderCart':
        printXML(stored_query_XML_fields('make_favorite_order_cart', $uf_logged_in, $the_date, $_REQUEST['cart_name']));
        exit;

    case 'getFavoriteOrderCarts':
        printXML(stored_query_XML_fields('get_favorite_order_carts', $uf_logged_in));
        exit;

    case 'getFavoriteOrdersOfCart':
        printXML(stored_query_XML_fields('products_for_favorite_order', $uf_logged_in, $_REQUEST['cart_id']));
        exit;

    case 'deleteFavoriteOrderCart':
        printXML(stored_query_XML_fields('delete_favorite_order_cart', $uf_logged_in, $_REQUEST['cart_id']));
        exit;

    case 'moveAllOrders':
        do_stored_query('move_all_orders', $_REQUEST['from_date'], $_REQUEST['to_date']);
        printXML('<ok>1</ok>'); //this is probably not the right way to do this... but printXML does not create tags for automatically!!
        exit;

    case 'listPreOrderProviders':
        printXML(stored_query_XML_fields('list_preorder_providers'));
        exit;

    case 'listPreOrderProducts':
        printXML(stored_query_XML_fields('list_preorder_products', $_REQUEST['provider_id']));
        exit;

    case 'activatePreOrderProducts':
        $activate_array = $_REQUEST['activate']; 
        $pi_array = $_REQUEST['product_id']; 
        $activate_list = '(';
        $deactivate_list = '(';

        foreach ($activate_array as $i => $product_id) { 
            $activate_list .= $product_id . ',';
        }

        $deactivate_array = array_diff($pi_array, $activate_array);
        foreach ($deactivate_array as $i => $product_id) { 
            $deactivate_list .= $product_id . ',';
        }

        $activate_list = rtrim($activate_list, ',') . ')';
        $deactivate_list = rtrim($deactivate_list, ',') . ')';

        //         $firephp->log($activate_list, 'activate_list');
        //         $firephp->log($deactivate_list, 'deactivate_list');

        if ($activate_list != '()')
            do_stored_query('activate_preorder_products', $the_date, $activate_list);
        if ($deactivate_list != '()')
            do_stored_query('deactivate_preorder_products', $the_date, $deactivate_list);
        echo 'ok';
        exit;

    default:    
        break;
    }

  
    // now come  the requests that need a cart manager

    switch ($what) {
    case 'shop':
        require_once("lib/shop_cart_manager.php");
        $cm = new shop_cart_manager($uf_logged_in, $the_date); 
        break;
      
    case 'order':
        require_once("lib/order_cart_manager.php");
        $cm = new order_cart_manager($uf_logged_in, $the_date); 
        break;
      
    case 'favorite_order':
        require_once("lib/favorite_order_cart_manager.php");
        $cm = new favorite_order_cart_manager($uf_logged_in, $_REQUEST['name']); 
        break;
      
    default:
        throw new Exception("ctrlShopAndOrder: request what={$_REQUEST['what']} not supported");
    }
  
 
    //  $firephp->log($_REQUEST);

    switch($_REQUEST['oper']) {
    
    case 'commit':
        try {
            $cm->commit($_REQUEST['quantity'], $_REQUEST['price'], $_REQUEST['product_id'], $_REQUEST['preorder']);
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