<?php

define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(__FILE__))).DS); 

require_once(__ROOT__ . "local_config/config.php");
require_once(__ROOT__ . "php/inc/database.php");
require_once(__ROOT__ . "php/utilities/general.php");
require_once(__ROOT__ . "php/utilities/orders.php");


if (!isset($_SESSION)) {
    session_start();
}

try{
	

    switch (get_param('oper')) {
    	
    	//returns a list of all orders summarized by provider within a given date range
    	case 'getOrdersListing':
    		echo get_orders_in_range(get_param('filter'), get_param('uf_id',0), get_param('fromDate',0), get_param('toDate',0), get_param('steps',0), get_param('range',0));
    		exit; 
    	
    	//returns a list of all orders for the given uf
    	case 'getOrdersListingForUf':
    		echo get_orders_in_range(get_param('filter'), get_param('uf_id'), get_param('fromDate',0), get_param('toDate',0), get_param('steps',0), get_param('range',0), get_param('limit',''));
    		exit; 

    	//retrieves list of products that have been ordered.
    	case 'getOrderedProductsList':
    		printXML(stored_query_XML_fields('get_ordered_products_list', get_param('order_id',0), get_param('provider_id',0), get_param('date',0) ));
    		exit;

        //retrieves list of products with prices that have been ordered, used to
        //  construct order revision table.
        case 'getOrderedProductsListPrices':
            printXML(rs_XML_fields(get_ordered_products_with_prices(
                get_param_int('order_id'), get_param_int('provider_id'),
                get_param_date('date'),
				get_param('page', '-')
            )));
            exit;
        case 'getAllProductsToOrder':
            printXML(rs_XML_fields(get_all_products_to_order(
                get_param_int('order_id')
            )));
            exit;
        case 'getAllUfsToOrder':
            printXML(rs_XML_fields(get_all_ufs_to_order(
                get_param_int('order_id')
            )));
            exit;

    	//retrieves the order detail uf/quantities for given order. order_id OR provider_id / date are needed. Filters for uf and/or product_id if needed. 
    	case 'getProductQuantiesForUfs':
    		printXML(stored_query_XML_fields('get_order_item_detail', get_param('order_id',0), get_param('uf_id',0), get_param('provider_id',0), get_param('date_for_order',0),0 ));
    		exit;
    		
    	//edits, modifies individual product quanties for order
    	case 'editQuantity':
    		echo edit_order_quantity(
                get_param_int('order_id'), get_param_int('product_id'), 
                get_param_int('uf_id'), get_param_numeric('quantity'));
    		exit;	

    	//edits, modifies individual gross price of a product into a order
    	case 'editGrossPrice':
            echo edit_order_gross_price(
                get_param_int('order_id'), get_param_int('product_id'),
                get_param_numeric('gross_price'));
            exit;

    	//edits, modifies total quantity of a product for order and adjusts
        //  the individual quantities proportionally.
    	case 'editTotalQuantity':
    		echo edit_total_order_quantities(get_param('order_id'), get_param('product_id'), get_param('quantity')); //this is the product_id
    		exit;
    	
    	//retrieves for a given provider- or product id, and date if order is open, closed, send-off... NOT USED... DELETE?!
  		case 'checkOrderStatus':
  			printXML(stored_query_XML_fields('get_order_status', get_param('date',0), get_param('provider_id',0), get_param('product_id',0), get_param('order_id',0)  ));
  			exit;
    		
    	//set the global revisio status of the order
    	case 'setOrderStatus':
    		chk_no_validate_order(get_param_int('order_id', 0));
    		echo do_stored_query('set_order_status', get_param('order_id'), get_param('status')  );
    		exit; 
    		
    	//revise individual items of order
    	case 'setOrderItemStatus':
			prepare_order_to_shop(get_param_int('order_id')); // and check $order_id parameter
    		echo do_stored_query('set_order_item_status', get_param('order_id'), get_param('product_id'), get_param('has_arrived'), get_param('is_revised')  ); 
    		exit;

    	//have the given order items be already validated?
    	case 'checkValidationStatus':
    		printXML(stored_query_XML_fields('get_validated_status', get_param('order_id',0), get_param('cart_id',0)));
    		exit;
    		
		//moves an order from revision to shop_item (into people's cart for the given date) 
    	case 'moveOrderToShop':
    		$order_id = get_param_int('order_id', 0);
    		chk_no_validate_order($order_id);
    		prepare_order_to_shop($order_id);
    		echo do_stored_query('move_order_to_shop', get_param('order_id'), get_param('date'));
    		exit;
            
    	//Distribute and directly validate an order
    	case 'directlyValidateOrder':
    		echo directly_validate_order(get_param_int('order_id'), get_param_int('record_provider_invoice', 0));
    		exit;
    		
    	//retrieves info about originally ordered quanties and available items after received orders have been revised and distributed in carts. 
  		case 'getDiffOrderShop':
    		printXML(stored_query_XML_fields('diff_order_shop', get_param('order_id'), get_session_uf_id()));    	
  			exit;
  			
  		//finalizes an order; no more modifications possible	
  		case 'finalizeOrder':
  			echo finalize_order(get_param_int('provider_id'), get_param('date'), get_param_int('revision_status', 1));
  			exit;

      case 'reopenOrder':
        echo do_stored_query('reopen_order', get_param('order_id'));
        exit;
  			
  		case 'preorderToOrder':
  			echo do_stored_query('convert_preorder',get_param('provider_id'), get_param('date_for_order'));
  			exit;
  			
  		case 'resetOrder':
            reset_order_to_shop(get_param_int('order_id'), get_param_int('clear'));
  			exit;
  			
  		case 'editOrderDetailInfo':
  			echo do_stored_query('edit_order_detail_info', get_param('order_id'), get_param('payment_ref',''), get_param('delivery_ref',''), get_param('order_notes','') );
  			exit;
  	
  			
  		case 'orderDetailInfo':
  			printXML(stored_query_XML_fields('get_detailed_order_info', get_param('order_id',0), get_param('provider_id'), get_param('date',0) ));
  			exit;
  			
  		//make html file(s) of selected orders and bundle them into a zip
  		case 'bundleOrders':
            require_once(__ROOT__ . "php/lib/report_manager.php");
  			$rm = new report_manager();
  			$zipfile = $rm->bundle_orders(get_param('provider_id'), get_param('date_for_order'), get_param('order_id'),0);
      		echo $zipfile;
      		exit;
            
      	case 'reportOrders':
            require_once(__ROOT__ . "php/lib/report_orders.php");
  			echo report_order::getHtml_orders($_GET);
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