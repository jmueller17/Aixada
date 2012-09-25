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

	  	case 'getDatesForValidation':
	    	printXML(get_dates_for_validation());
	    	exit;

	  	case 'getProductsBelowMinStock':
	    	printXML(query_XML_noparam('products_below_min_stock'));
	    	exit;
	
	  	case 'GetUFsForValidation':
	    	printXML(stored_query_XML('get_ufs_for_validation', 'ufs', 'name', get_param('date',0)));
	    	exit;

	    case 'getShopCart':
  			printXML(stored_query_XML_fields('get_shop_cart', get_param('date'), get_param('uf_id'),get_param('cart_id',0),get_param('validated',0))); 
			exit; 
	    
	  	case 'commit':
       		$vm = new validation_cart_manager(get_session_user_id(), get_param('uf_id'), get_param('date')); 
	  		$emptyArr = array();
        	$vm->commit(get_param('quantity',$emptyArr), get_param('product_id',$emptyArr), get_param('iva_percent',$emptyArr), get_param('rev_tax_percent',$emptyArr), get_param('order_item_id',$emptyArr), get_param('cart_id',0), get_param('preorder',$emptyArr), get_param('price', $emptyArr));      
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