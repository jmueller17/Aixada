<?php

define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(__FILE__))).DS); 

require_once(__ROOT__ . "local_config/config.php");
require_once(__ROOT__ . "php/inc/database.php");
require_once(__ROOT__ . "php/utilities/general.php");
require_once(__ROOT__ . "php/utilities/dates.php");
require_once(__ROOT__ . "php/utilities/shop_and_order.php");
require_once(__ROOT__ . "php/lib/validation_cart_manager.php");

$use_session_cache = configuration_vars::get_instance()->use_session_cache;

if (!isset($_SESSION)) {
    session_start();
 }

DBWrap::get_instance()->debug = true;

try{

	
  switch (get_param('oper')) {

  		//worx but not used
	  	case 'getDatesForValidation':
	    	printXML(get_dates_for_validation());
	    	exit;
	
	    //worx but not used in validate
	  	case 'getUFsForValidation':
	    	printXML(stored_query_XML('get_ufs_for_validation', 'ufs', 'name', get_param('date',0)));
	    	exit;
	    
	    //returns list of all active ufs and the #of non-validated carts
	  	case 'getUFsCartCount':
	  		printXML(stored_query_XML_fields('get_uf_listing_cart_count'));
	    	exit;

	    case 'getNonValidatedCarts':
    		printXML(stored_query_XML_fields('get_non_validated_carts', get_param('uf_id',0)));
    		exit;
	    	
    	//retrieves a given shopping cart, non-validated. 
    	//this is the same as in /ctrl/Shop.php except the uf_id parameter which is flexible here
	    case 'getShopCart':
  			printXML(stored_query_XML_fields('get_shop_cart', get_param('date',0), get_param('uf_id',0), get_param('cart_id',0),get_param('validated',0))); 
			exit;

        case 'getShopCartHead':
            $cart_id = get_param_int('cart_id', 0);
            printXML(query_XML_fields(
                "select id cart_id, ts_last_saved from aixada_cart
                where ts_validated = 0 and id = {$cart_id}")); 
            exit; 

        case 'createEmptyCart':
            echo create_empty_cart(get_param_int('uf_id'),get_param('date'));
            exit;

	  	case 'commit':
       		try {
		  		$vm = new validation_cart_manager(get_session_user_id(), get_param('uf_id'), get_param('date')); 
		  		$emptyArr = array();
        		$cid = $vm->commit(	get_param('quantity',$emptyArr), 
        					get_param('product_id',$emptyArr), 
        					get_param('iva_percent',$emptyArr), 
        					get_param('rev_tax_percent',$emptyArr), 
        					get_param('order_item_id',$emptyArr), 
        					get_param('cart_id',0),
        					get_param('ts_last_saved',0), 
        					get_param('preorder',$emptyArr), 
        					get_param('price', $emptyArr),
        					null);
        		echo ($cid);
        		
       		} catch(Exception $e) {
	            header('HTTP/1.0 401 ' . $e->getMessage());
	            die ($e->getMessage());
	        }
    		break;
	    

	      
	  default:  
    	throw new Exception("ctrlValidate: oper={$_REQUEST['oper']} not supported");  
        break;
  }

 
} 

catch(Exception $e) {
  header('HTTP/1.0 401 ' . $e->getMessage());
  die($e->getMessage());
}  


?>
