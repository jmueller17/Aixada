<?php

require_once('inc/database.php');
require_once('local_config/config.php');
require_once ('utilities.php');

$firephp = FirePHP::getInstance(true);
ob_start(); // Starts FirePHP output buffering


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




/**
 * Returns $num=10 possible sales dates later than the given start
 * date. The dates are guaranteed to be active. 
 * @param $_start_date date defaults to today
 * @param $num int how many sales days to output. Defaults to 10.
 * @return $sales_dates array(date) 
 */
/*
function get_10_sales_dates_XML($_start_date=0, $num=10)
{
    $xml = stored_query_XML_fields('get_sales_dates', 0, $num);
    if (strpos('<rowset></rowset>', $xml) !== false) {
        $today = '<row><date_for_order f="date_for_order"><![CDATA[' 
            . strftime('%Y-%m-%d', strtotime("now")) 
            . ']]></date_for_order></row>';
        $xml = str_replace('<rowset></rowset>', 
                           '<rowset>' . $today . '</rowset>', $xml);
    }
    return $xml;
}*/

/*

function get_next_equal_shop_date_XML()
{
    return stored_query_XML_fields('get_next_equal_shop_date');
}*/

/**
 * Calculate the date of the next sales day not including the given
 * $start_date (which defaults to the current date). 
 * @param $start_date date default 0 means 'counting from today'.
 */ 
/*
function get_next_shop_date_XML($start_date=0)
{
    return  stored_query_XML_fields('get_sales_dates', $start_date, 1);
}*/

/*

function get_next_shop_date($start_date=0)
{
    $rs = do_stored_query('get_sales_dates', $start_date, 1);
    $row = $rs->fetch_array();  // putative date of sale
    DBWrap::get_instance()->free_next_results();
    return $row[0];
}*/





/*

function make_next_shop_dates()
{
    $dates = array();
    $sday = configuration_vars::get_instance()->shopping_day;
    $num_weeks = 15;
    for ($i=-2; $i<$num_weeks; $i++) {
        $dates[strftime('%Y-%m-%d', strtotime('next ' . $sday . ' + ' . $i . ' week'))] = 0;
    }
    //    global $firephp;
    //    $firephp->log($dates);
    $k = array_keys($dates);
    $first_date = strftime('%Y-%m-%d', strtotime('today - 2 week'));
    //    $firephp->log($first_date);
    $last_date = array_pop($k);
    $db = DBWrap::get_instance();

    $rs = $db->Execute("select shopping_date, available from aixada_shopping_dates where shopping_date between :1q and :2q", $first_date, $last_date);
    $forbidden = $allowed = array();
    while ($row = $rs->fetch_assoc()) {
        if ($row['available']) {
            $allowed[$row['shopping_date']] = 0;
        } else {
            $forbidden[$row['shopping_date']] = 0;
        }
    }
    $dates = array_diff_key($dates, $forbidden);
    $dates = array_merge($dates, $allowed);
    ksort($dates);

    $db->free_next_results();
    $rs =  $db->Execute("select distinct date_for_order from aixada_order_item where date_for_order between :1q and :2q", $first_date, $last_date);
    while ($row = $rs->fetch_assoc()) {
        $dates[$row['date_for_order']] = 1;
    }
    return $dates;
}

function get_next_shop_dates() 
{
    $json = '[';
    foreach(make_next_shop_dates() as $d => $has_order) {
        $json .= '"' . $d . '",';
    }
    return rtrim($json, ',') . ']';
}
*/





	
?>