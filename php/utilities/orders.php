<?php



require_once(__ROOT__ . 'php/inc/database.php');
require_once(__ROOT__ . 'local_config/config.php');
require_once(__ROOT__ . 'php/utilities/general.php');


/**
 * 
 * Finalizes an order which means, that an order is send to the provider (email, printed out, fetched by provider directly).
 * No more modifications are possible and the ordered items receive an order_id
 * @param int $provider_id
 * @param date $date_for_order
 */
function finalize_order($provider_id, $date_for_order)
{
	
	$sendEmail = configuration_vars::get_instance()->internet_connection;
	
	if ($sendEmail){
		
		$rm = new report_manager(); 
		$message = $rm->write_summarized_orders_html($provider_id, $date_for_order);
		
		
		$db = DBWrap::get_instance();
		$strSQL = 'SELECT email FROM aixada_provider WHERE id = :1q';
    	$rs = $db->Execute($strSQL, $provider_id);
		$toEmail = mysql_result($rs, 1);
		
		
				
		$subject = "Comanda Aixada";
		$from = "info@aixada.org"; //should be responsible uf!! 
		$headers = "From:" . $from;
		mail($to,$subject,$message,$headers);
		echo "Mail Sent.";
	}
	
	
	return do_stored_query('finalize_order', $provider_id, $date_for_order);
	
	
}

/**
 * 
 * custom interface for order listing which converts most common query params into 
 * calls to stored procedures. 
 * @param str $time_period today | all | prevMonth | etc. 
 */
function get_orders_in_range($time_period='ordersForToday', $uf_id=0, $from_date=0, $to_date=0, $steps=1, $range="month", $limit='')
{
	
	//TODO server - client difference in time/date?!
	$today = date('Y-m-d', strtotime("Today"));
	$tomorrow = date('Y-m-d', strtotime("Tomorrow"));
	$next_week =  date('Y-m-d', strtotime('Tomorrow + 6 days'));
	$prev_month = date('Y-m-d', strtotime('Today - 1 month'));
	$prev_2month = date('Y-m-d', strtotime('Today - 2 month'));
	$prev_year = date('Y-m-d', strtotime('Today - 1 year'));
	$very_distant_future = '9999-12-30';
	$very_distant_past	= '1980-01-01';
	
	//this could be removed.... doesn't make much sense anymore. 
	$steps_from_date 	 = date('Y-m-d', strtotime("Today -". ($steps--) . " " .$range));
	$steps_to_date	 = date('Y-m-d', strtotime("Today -". ($steps) . " " .$range));	
	

	//stored procedure 'get_orders_listing(fromDate, toDate, uf_id, filter_revision_status_id, limit_str)'
	
	switch ($time_period) {
		// all orders where date_for_order = today
		case 'ordersForToday':
			printXML(stored_query_XML_fields('get_orders_listing', $today, $today, $uf_id,0,$limit));
			break;
		
		//all orders 
		case 'all':
			printXML(stored_query_XML_fields('get_orders_listing', $very_distant_past, $very_distant_future, $uf_id,0,$limit));
			break;

		case 'pastMonths2Future':
			printXML(stored_query_XML_fields('get_orders_listing', $prev_2month, $very_distant_future, $uf_id,0,$limit));
			break;
			
		//last month
		case 'pastMonth':
			printXML(stored_query_XML_fields('get_orders_listing', $prev_month, $tomorrow, $uf_id,0,$limit));
			break;
			
		//last year
		case 'pastYear':
			printXML(stored_query_XML_fields('get_orders_listing', $prev_year, $tomorrow, $uf_id,0,$limit));
			break;
		
		//all orders that have been send off, but have no shop date assigned
		case 'limboOrders':
			printXML(stored_query_XML_fields('get_orders_listing', $prev_year, $very_distant_future, $uf_id, 3,$limit));
			break;
			
		case 'nextWeek':
			printXML(stored_query_XML_fields('get_orders_listing', $today, $next_week, $uf_id, 0,$limit));
			break;
					
		case 'futureOrders':
			printXML(stored_query_XML_fields('get_orders_listing', $today, $very_distant_future, $uf_id,0,$limit));
			break;
			
		case 'steps':
			printXML(stored_query_XML_fields('get_orders_listing', $steps_from_date, $steps_to_date, $uf_id,0,$limit));
			break;
			
		case 'exact':
			printXML(stored_query_XML_fields('get_orders_listing', $from_date, $to_date, $uf_id,0,$limit));
			break;
		
		
		default:
			throw new Exception("get_orders_in_range: param={$time_period} not supported");  
			break;
	}

	
}
?>