<?php


require_once(__ROOT__ . 'php/inc/database.php');
require_once(__ROOT__ . 'local_config/config.php');
require_once ('general.php');



/**
 * 
 * custom interface for retrieving purchase listings which converts most common query params into 
 * calls to stored procedures. 
 * @param str $filter  prevMonth | steps | exact  
 *  
 */
function get_purchase_in_range($filter='prevMonth', $uf_id=0, $from_date=0, $to_date=0, $steps=1, $range="month", $limit='')
{
	
	//TODO server - client difference in time/date?!
	
	$today = date('Y-m-d', strtotime("Today"));
	$stepsFromDate = date('Y-m-d', strtotime("Today -". ($steps--) . " " .$range));
	$stepsToDate	 = date('Y-m-d', strtotime("Today -". ($steps) . " " .$range));	
	

	$prevMonth = date('Y-m-d', strtotime('Today - 1 month'));
	$prev3Month = date('Y-m-d', strtotime('Today - 3 month'));
	$prevYear = date('Y-m-d', strtotime('Today - 1 year'));
	$very_distant_future = '9999-12-30';
	$very_distant_past	= '1980-01-01';
	
	
	switch ($filter) {
		
		case 'today':
			printXML(stored_query_XML_fields('get_purchase_listing', $today, $today, $uf_id, $limit));
			break;
			
		case 'prevMonth':
			printXML(stored_query_XML_fields('get_purchase_listing', $prevMonth, $today, $uf_id, $limit));
			break;
			
		case 'prev3Month':
			printXML(stored_query_XML_fields('get_purchase_listing', $prev3Month, $today, $uf_id, $limit));
			break;
			
			
		case 'steps':
			printXML(stored_query_XML_fields('get_purchase_listing', $stepsFromDate, $stepsToDate, $uf_id, $limit));
			break;
			
		case 'all':
			printXML(stored_query_XML_fields('get_purchase_listing', $very_distant_past, $very_distant_future, $uf_id, $limit));
			break;
			
		case 'exact':
			printXML(stored_query_XML_fields('get_purchase_listing', $from_date, $to_date, $uf_id, $limit));
			break;
			
		default:
			throw new Exception("get_orders_in_range: param={$time_period} not supported");  
			break;
	}
	
}


	
?>