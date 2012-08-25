<?php

require_once('inc/database.php');
require_once('local_config/config.php');
require_once ('utilities.php');


/**
 * 
 * Finalizes an order which means, that an order is send to the provider (email, printed out, fetched by provider directly).
 * No more modifications are possible and the ordered items receive an order_id
 * @param int $provider_id
 * @param date $date_for_order
 */
function finalize_order($provider_id, $date_for_order)
{
	
	return do_stored_query('finalize_order', $provider_id, $date_for_order);
	
	
}

/**
 * 
 * custom interface for order listing which converts most common query params into 
 * calls to stored procedures. 
 * @param str $time_period today | all | prevMonth | etc. 
 */
function get_orders_in_range($time_period='today', $uf_id=0)
{
	
	//TODO server - client difference in time/date?!
	
	$today = date('Y-m-d', strtotime("Today"));
	$tomorrow = date('Y-m-d', strtotime("Tomorrow"));
	$prevMonth = date('Y-m-d', strtotime('Today - 1 month'));
	$prevYear = date('Y-m-d', strtotime('Today - 1 year'));
	$very_distant_future = '9999-12-30';
	$very_distant_past	= '1980-01-01';

	
	switch ($time_period) {
		// all orders where date_for_order = today
		case 'ordersForToday':
			printXML(stored_query_XML_fields('get_orders_listing', $today, $today, $uf_id,0));
			break;
		
		//all orders 
		case 'all':
			printXML(stored_query_XML_fields('get_orders_listing', $very_distant_past, $very_distant_future, $uf_id,0));
			break;

		case 'pastMonth2Future':
			printXML(stored_query_XML_fields('get_orders_listing', $prevMonth, $very_distant_future, $uf_id,0));
			break;
			
		//last month
		case 'prevMonth':
			printXML(stored_query_XML_fields('get_orders_listing', $prevMonth, $tomorrow, $uf_id,0));
			break;
			
		//last year
		case 'prevYear':
			printXML(stored_query_XML_fields('get_orders_listing', $prevYear, $tomorrow, $uf_id,0));
			break;
		
		//all orders that have been send off, but have no shop date assigned
		case 'limboOrders':
			printXML(stored_query_XML_fields('get_orders_listing', $prevYear, $very_distant_future, $uf_id, 3));
			break;
		
		case 'futureOrders':
			printXML(stored_query_XML_fields('get_orders_listing', $today, $very_distant_future, $uf_id,0));
			break;
		
		
		default:
			throw new Exception("get_orders_in_range: param={$time_period} not supported");  
			break;
	}

	
}

	
?>