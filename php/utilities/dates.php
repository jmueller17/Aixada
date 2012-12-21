<?php


require_once(__ROOT__ . 'php/inc/database.php');
require_once(__ROOT__ . 'local_config/config.php');
require_once ('general.php');



/**
 * 
 * returns a list of upcoming orders that are not closed
 * @param unknown_type $time_range number of weeks to look into the future
 */
function get_upcoming_orders ($nr_weeks){
	
	$until_when = date('Y-m-d', strtotime("Today + ".$nr_weeks." week"));
	
	printXML(stored_query_XML_fields('get_upcoming_orders', $until_when));
}

/**
 * 
 * Activates or deactivates all products as orderable/not orderable for a given provider and for a given date. 
 * @param unknown_type $provider_id
 * @param unknown_type $date
 * @param unknown_type $activate
 */
function activate_all_for_date($provider_id, $date, $activate)
{
	$product_ids = array();
	$rs = do_stored_query('get_products_of_provider', $provider_id, 1);
	
	//for each product 
	while ($row = $rs->fetch_assoc()) {
	 	array_push($product_ids, $row['id']);	 
	}
	DBWrap::get_instance()->free_next_results();
	
	
	foreach($product_ids as $id){
	 	do_stored_query('toggle_orderable_product', $id, $date, 0);	
	}
	DBWrap::get_instance()->free_next_results();
	
	echo 1; //printXML(stored_query_XML_fields('get_products_of_provider', $provider_id, 1 ));
}



/**
 * 
 * Returns dates that have unvalidated shopping carts. If among the dates
 * is "today", gets true mark. 
 */
function get_dates_for_validation()
{
	$xml = stored_query_XML_fields('dates_with_unvalidated_shop_carts');
	$today = strftime('%Y-%m-%d', strtotime('today'));
	$pos = strpos($xml, $today);
	if ($pos !== false) {
	  	$xml = substr_replace($xml, ' today="true"', $pos-10, 0);
	}
	return $xml; 
}



/**
 * 
 * For a given provider and a given date, all orderable products for this date will be set orderable
 * depending on weekly frequency and duration 
 * @param int $provider_id
 * @param string $fromDate   		starting date 
 * @param int $weeklyFreq			every week, two weeks, ... four weeks
 * @param int $nrMonth				for how many months
 */
function generate_date_product_pattern($provider_id, $fromDate, $weeklyFreq, $nrMonth)
{
	$daySteps =  $weeklyFreq * 7; 
  	$nrWeeks = $nrMonth * 4; 
  	
  	echo do_stored_query('repeat_orderable_day_provider', $provider_id, $fromDate, $daySteps, $nrWeeks);
}



/**
 * 
 * Generates a date range starting from $first until $last date, both inclusive. 
 * @param date string $first
 * @param date string $last
 * @param date format $outDateFormat  xml, or array 
 * @param int $step
 * @param date format $dataFormat
 */
function dateRange( $first, $last, $outDateFormat='Y-m-d', $dataFormat='xml', $step = '+1 day' ) {
	$current = strtotime( $first );  //TODO check if dates are valid. However, if they are feed by datepicker, this should be ok.
	$last = strtotime( $last );
	
	
	if ($dataFormat == 'xml'){
		$dates = '<dates>';
	
		while( $current <= $last ) {
			$date = date( $outDateFormat, $current );
			
			$dates .= '<date>' . $date .'</date>';
			
			$current = strtotime( $step, $current );
		}
		$dates .= '</dates>';

		
	} else if ($dataFormat == 'array'){
		$dates = '[';
	
		while( $current <= $last ) {
			$date = date( $outDateFormat, $current );
			$dates .= "'".$date."',";
			$current = strtotime( $step, $current );
		}
		$dates = rtrim($dates, ',') . ']';
		
		
	} else {
		
		
	}
	

	return $dates;
}


/**
 * 
 * Generic function to retrieve dates from the database
 * @param string $which  		specifies which stored procedure to call: today | get_orderable_dates
 * @param string $format		xml | array
 * @param datestr $from_date	a starting date from which onwards dates will be returned. expects mysql standard format 
 * 								yy-mm-dd. if none is specified, takes today
 * @param int $limit			the limit parameter for th sql query. limit=1 returns the next available date 
 */
function get_dates($which, $format = 'xml', $limit=117111451111, $from_date=0)
{
	if ($from_date == 0){
		//TODO server - client difference in time/date?!
		$from_date = date('Y-m-d', strtotime("Today")); 
	}
	
	switch ($format){
		case 'xml':
			if ($which == 'today'){
				$today = date('Y-m-d', strtotime("Today"));	
				return '<row><date>'.$today.'</date></row>';
			} else {
				return printXML(stored_query_XML_fields($which, $from_date, $limit));
			}
			exit; 
			
		case 'array':
			if ($which=='today'){
				$today = date('Y-m-d', strtotime("Today"));	
				return '["'.$today.'"]';
			} else {

				$rs = do_stored_query($which, $from_date, $limit);
				$dates = '[';
				while ($row = $rs->fetch_array()) {
			        $dates .= '"' . $row[0] . '",';
			    }
				return rtrim($dates, ',') . ']';
			}
			exit;
			 
			
		default: 
	    	throw new Exception("utility_dates: \"format=" . $format . "\" not a valid output format");			
	
	}
}




	
?>