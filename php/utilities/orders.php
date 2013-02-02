<?php



require_once(__ROOT__ . 'php/inc/database.php');
require_once(__ROOT__ . 'local_config/config.php');
require_once(__ROOT__ . 'php/utilities/general.php');
require_once(__ROOT__ . 'local_config/lang/'.get_session_language() . '.php');



/**
 * 
 * recalculates the individual order quanties when adjusting the total delivered quantity. 
 * @param unknown_type $order_id
 * @param unknown_type $product_id
 * @param unknown_type $new_total_quantity
 */
function edit_total_order_quantities($order_id, $product_id, $new_total_quantity){
	
	$rs = do_stored_query('get_order_item_detail', $order_id, 0,0, 0,$product_id );
	
	$uf_qu = array();
	
	$total_quantity = 0; 
	//for each uf 
	while ($row = $rs->fetch_assoc()) {
		$uf_qu[$row['uf_id']] =  $row['quantity'];
	 	$total_quantity = $total_quantity + $row['quantity'];	 
	}		
	DBWrap::get_instance()->free_next_results();
	
	//calc and save adjusted quantities for each uf
	$xml = '<total>'.$total_quantity.'</total>';
	$xml .= '<rows>';
	foreach ($uf_qu as $uf_id => $quantity){
	    $new_quantity = round(($quantity / $total_quantity) * $new_total_quantity, 3);
	    do_stored_query('modify_order_item_detail', $order_id, $product_id, $uf_id, $new_quantity);
	    $xml .= "<row><uf_id>${uf_id}</uf_id><quantity>${new_quantity}</quantity></row>";
	}
	DBWrap::get_instance()->free_next_results();
	
	printXML($xml . '</rows>');
}



/**
 * 
 * Finalizes an order which means, that an order is send to the provider (email, printed out, fetched by provider directly).
 * No more modifications are possible and the ordered items receive an order_id
 * @param int $provider_id
 * @param date $date_for_order
 */
function finalize_order($provider_id, $date_for_order)
{
	global $Text;  	
	$config_vars = configuration_vars::get_instance(); 
	$msg = ''; 
	
	
	//check here if an order_id already exists for this date and provider. 
	
	
	
	if ($config_vars->internet_connection && $config_vars->email_orders){
		
		
		$rm = new report_manager();
		
		$message = '<html>';
		$message .= '<body>';   
		
		if ($config_vars->email_order_format == 1){ 
			$message .= "<h2>".$config_vars->coop_name.". Summarized order ".$date_for_order."</h2>";
			$message .= $rm->write_summarized_orders_html($provider_id, $date_for_order);
			$message .= "<p><br/></p>";
		} else if ($config_vars->email_order_format == 2){
			$message .= "<h2>".$config_vars->coop_name.". Detail order ".$date_for_order."</h2>";
			$message .= $rm->extended_orders_for_provider_and_dateHTML($provider_id, $date_for_order);
			$message .= "<p><br/></p>";
		} else if ($config_vars->email_order_format == 3){
			$message .= "<h2>".$config_vars->coop_name.". Summarized order ".$date_for_order."</h2>";
			$message .= $rm->write_summarized_orders_html($provider_id, $date_for_order);
			$message .= "<p><br/></p>";
			$message .= "<h2>".$config_vars->coop_name.". Detail order ".$date_for_order."</h2>";
			$message .= $rm->extended_orders_for_provider_and_dateHTML($provider_id, $date_for_order);
			$message .= "<p><br/></p>";
		}
		
		$message .= '</body></html>';
		
		$db = DBWrap::get_instance();
		$db->free_next_results();
		$strSQL = 'SELECT email FROM aixada_provider WHERE id = :1q';
    	$rs = $db->Execute($strSQL, $provider_id);
		if($rs->num_rows == 0){
    		throw new Exception("The provider does not have an email.");
		}
    	
		while ($row = $rs->fetch_assoc()) {
      		$toEmail = $row['email'];
    	}
    	
    	$db->free_next_results();
    	
		$rs = do_stored_query('get_responsible_uf', $provider_id);
		
		if($rs->num_rows == 0){
    		throw new Exception($Text['msg_err_responsible_uf']);
		}

		$uf_emails = array();
		//get emails of reponsible ufs
		while ($row = $rs->fetch_assoc()) {
      		array_push($uf_emails, $row['email']);
    	}
    	
    	$responsible_ufs = implode(", ", $uf_emails);
    	
    	//also send order to responsible uf 
    	$toEmail = $toEmail . ", " . $responsible_ufs; 
    	
    	$db->free_next_results();

    	
    	
		$subject = " " . $config_vars->coop_name . " " .  $Text['order'] . " " .$Text['for'] . " " .$date_for_order;
		//$subject = $config_vars->coop_name . " order for " .$date_for_order;
    	$from = $config_vars->admin_email; 
		
		$headers = "From: $from \r\n";
		$headers .= "Reply-To: $responsible_ufs \r\n";
		$headers .= "Return-Path: $from\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
		
		
		if (mail($toEmail,$subject,$message,$headers)){
			$msg = $Text['msg_order_emailed'];			
		} else {
			$msg =  $Text['msg_err_emailed'];		
		}
		
		
	}
	
	
	
	
	if ($rs = do_stored_query('finalize_order', $provider_id, $date_for_order)){
		while ($row = $rs->fetch_assoc()) {
      		$order_id = $row['id'];
    	}
		$msg .= $Text['ostat_desc_fin_send'] . $order_id;
	} else {
		throw new Exception ($Text['msg_err_finalize']);
	}
	
	return $msg; 
	
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
	$pre_order	= '1234-01-23';
	
	
	//get results based on time periods and then go backward/foward a certain amount of time 
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
		
		case 'preOrders':
			printXML(stored_query_XML_fields('get_orders_listing', $pre_order, $pre_order, $uf_id, 0,$limit));
			break;
			
		case 'nextWeek':
			printXML(stored_query_XML_fields('get_orders_listing', $tomorrow, $next_week, $uf_id, 0,$limit));
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