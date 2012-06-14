<?php

require_once('inc/database.php');
require_once('local_config/config.php');
require_once ('utilities.php');



function get_orders_in_range($time_period='today', $limit=117111451111)
{
	
	//TODO server - client difference in time/date?!
	$today = date('Y-m-d', strtotime("Today"));
	$prevMonth = date('Y-m-d', strtotime('Today - 1 month'));
	$very_distant_future = '9999-12-30';
	$very_distant_past	= '1980-01-01';
	
	switch ($time_period) {
		// all orders where date_for_order = today
		case 'today':
			printXML(stored_query_XML_fields('get_orders_listing', $today, $today, $limit));
			break;
		
		//all orders 
		case 'all':
			printXML(stored_query_XML_fields('get_orders_listing', $very_distant_past, $very_distant_future, $limit));
			break;

		//last 100
		case 'prevMonth':
			printXML(stored_query_XML_fields('get_orders_listing', $prevMonth, $very_distant_future, $limit));
			break;
		
		//all orders without a shop date assigned
		case 'limbo':
			echo '';
		break;
		
		//all orders revised, shopped, validated
		case 'closed':
			echo '';
		break;		
		
		default:
			throw new Exception("get_orders_in_range: param={$time_period} not supported");  
			break;
	}

	
}

	
?>