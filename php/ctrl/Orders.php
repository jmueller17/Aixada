<?php

define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(__FILE__))).DS); 

require_once(__ROOT__ . "local_config/config.php");
require_once(__ROOT__ . "php/inc/database.php");
require_once(__ROOT__ . "php/utilities/general.php");
require_once(__ROOT__ . "php/utilities/orders.php");
require_once(__ROOT__ . "php/lib/report_manager.php");


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
            $_order_id = get_param_int('order_id');
            $_provider_id = get_param_int('provider_id');
            $_date = get_param_date('date');
            $sql = "
                select distinct
                    p.id, 
                    p.name, 
                    um.unit,
                    round( 
                        max(
                            ifnull(ots.unit_price_stamp,oi.unit_price_stamp)
                            ) / 
                        (1 + rev.rev_tax_percent/100) /
                        (1 + iva.percent/100), 2) gross_price,
                    iva.percent iva_percent,
                    round( 
                        max(
                            ifnull(ots.unit_price_stamp,oi.unit_price_stamp)
                            ) /
                        (1 + rev.rev_tax_percent/100), 2) net_price
                from
                    aixada_order_item oi
                left join 
                    aixada_order_to_shop ots
                on  oi.id = ots.order_item_id
                join (
                    aixada_product p,
                    aixada_rev_tax_type rev, 
                    aixada_iva_type iva,
                    aixada_unit_measure um
                ) on 
                    oi.product_id = p.id
                    and rev.id = p.rev_tax_type_id
                    and iva.id = p.iva_percent_id
                    and um.id =  p.unit_measure_order_id";
            if ($_order_id) {
                $sql .= " where oi.order_id = {$_order_id}";
            } else if($_date && $_provider_id) {
                $sql .= "
                    where
                        oi.date_for_order = '{$_date}'
                        and p.provider_id = {$_provider_id}";
            } else {
                $sql .= " where oi.order_id = -1"; // no rows
            }
            $sql .= "
                group by p.id, p.name, 
                        um.unit, rev.rev_tax_percent, iva.percent";
            $sql .= " order by p.name";
            printXML(query_XML_fields($sql));
            exit;

    	//retrieves the order detail uf/quantities for given order. order_id OR provider_id / date are needed. Filters for uf and/or product_id if needed. 
    	case 'getProductQuantiesForUfs':
    		printXML(stored_query_XML_fields('get_order_item_detail', get_param('order_id',0), get_param('uf_id',0), get_param('provider_id',0), get_param('date_for_order',0),0 ));
    		exit;
    		
    	//edits, modifies individual product quanties for order
    	case 'editQuantity':
    		$splitParams = explode("_", get_param('product_uf'));
    		$ok = do_stored_query('modify_order_item_detail', get_param('order_id'), $splitParams[0], $splitParams[1] , get_param('quantity'));
    		if ($ok){
	    		echo get_param('quantity');
    		} else {
    			throw new Exception("An error occured during saving the new product quantity!!");      			
    		}
    		exit;	

    	//edits, modifies individual gross price of a product into a order
    	case 'editGrossPrice':
            // Check parameters
    		$_product_id = get_param_int('product_id');
            $_order_id = get_param_int('order_id');
            $_gross_price = get_param_numeric('gross_price');        
            if ($_product_id === null || $_order_id === null || $_gross_price === null) {
                throw new Exception("An error occured during saving the new product gross price!!");     
            }
            // Insert oder items to aixada_order_to_shop table if is needed.            
            $ok = do_stored_query('modify_order_item_detail', $_order_id, 0, 0, 0);
    		if (!$ok) {
    			throw new Exception("An error occured during saving the new product gross price!!");      			
    		}
            // Get net price
            $_net_price = get_value_query("
                select round({$_gross_price} * 
                    (1 + rev.rev_tax_percent/100) * (1 + iva.percent/100), 2)
                from  aixada_product p
                join (aixada_rev_tax_type rev, aixada_iva_type iva)
                on    rev.id = p.rev_tax_type_id and iva.id = p.iva_percent_id
                where p.id = {$_product_id}
            ");
            // Ok, now update net price!!
            $ok = DBWrap::get_instance()->do_stored_query("
                update
                    aixada_order_to_shop os
                set
                    os.unit_price_stamp = {$_net_price}
                where
                    os.product_id = {$_product_id}
                    and os.order_id = {$_order_id}
            ");
    		if ($ok){
	    		echo 'OK;'.$_gross_price.';'.$_net_price;
    		} else {
    			throw new Exception("An error occured during saving the new product gross price!!");      			
    		}
    		exit;

    	//edits, modifies total quantity of a product for order and adjusts
        //  the individual quantities proportionally.
    	case 'editTotalQuantity':
    		//$splitParams = explode("_", get_param('product_id'));
    		echo edit_total_order_quantities(get_param('order_id'), get_param('product_id'), get_param('quantity')); //this is the product_id
    		exit;
    	
    	//retrieves for a given provider- or product id, and date if order is open, closed, send-off... NOT USED... DELETE?!
  		case 'checkOrderStatus':
  			printXML(stored_query_XML_fields('get_order_status', get_param('date',0), get_param('provider_id',0), get_param('product_id',0), get_param('order_id',0)  ));
  			exit;
    		
    	//set the global revisio status of the order
    	case 'setOrderStatus':
    		echo do_stored_query('set_order_status', get_param('order_id'), get_param('status')  );
    		exit; 
    		
    	//revise individual items of order
    	case 'setOrderItemStatus':
    		echo do_stored_query('set_order_item_status', get_param('order_id'), get_param('product_id'), get_param('has_arrived'), get_param('is_revised')  ); 
    		exit;

    	//have the given order items be already validated?
    	case 'checkValidationStatus':
    		printXML(stored_query_XML_fields('get_validated_status', get_param('order_id',0), get_param('cart_id',0)));
    		exit;
    		
		//moves an order from revision to shop_item (into people's cart for the given date) 
    	case 'moveOrderToShop':
    		echo do_stored_query('move_order_to_shop', get_param('order_id'), get_param('date'));
    		exit;
    		
    	//retrieves info about originally ordered quanties and available items after received orders have been revised and distributed in carts. 
  		case 'getDiffOrderShop':
    		printXML(stored_query_XML_fields('diff_order_shop', get_param('order_id'), get_session_uf_id()));    	
  			exit;
  			
  		//finalizes an order; no more modifications possible	
  		case 'finalizeOrder':
  			echo finalize_order(get_param('provider_id'), get_param('date'));
  			exit;
  			
  		case 'preorderToOrder':
  			echo do_stored_query('convert_preorder',get_param('provider_id'), get_param('date_for_order'));
  			exit;
  			
  		case 'resetOrder':
  			echo do_stored_query('reset_order_revision', get_param('order_id'));
  			exit;
  			
  		case 'editOrderDetailInfo':
  			echo do_stored_query('edit_order_detail_info', get_param('order_id'), get_param('payment_ref',''), get_param('delivery_ref',''), get_param('order_notes','') );
  			exit;
  	
  			
  		case 'orderDetailInfo':
  			printXML(stored_query_XML_fields('get_detailed_order_info', get_param('order_id',0), get_param('provider_id'), get_param('date',0) ));
  			exit;
  			
  		//make html file(s) of selected orders and bundle them into a zip
  		case 'bundleOrders':
  			$rm = new report_manager();
  			$zipfile = $rm->bundle_orders(get_param('provider_id'), get_param('date_for_order'), get_param('order_id'),0);
      		echo $zipfile;
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