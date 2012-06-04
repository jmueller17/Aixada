<?php

require_once('inc/database.php');
require_once('local_config/config.php');
require_once ('utilities.php');

$firephp = FirePHP::getInstance(true);
ob_start(); // Starts FirePHP output buffering



function generate_date_product_pattern($provider_id, $fromDate, $weeklyFreq, $nrMonth)
{
	
	$daySteps =  $weeklyFreq * 7; 
  	$nrWeeks = $nrMonth * 4; 

  	//do_store_query('delete_orderable_products', $provider_id, $fromDate);
  	
  	echo do_stored_query('repeat_orderable_day_provider', $provider_id, $fromDate, $daySteps, $nrWeeks);
  	

}



/**
 * 
 * Enter description here ...
 * @param unknown_type $first
 * @param unknown_type $last
 * @param unknown_type $outDateFormat
 * @param unknown_type $step
 * @param unknown_type $dataFormat
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

		
	} else if ($dataFormat == 'arrayStr'){
		$dates = '[';
	
		while( $current <= $last ) {
			$date = date( $outDateFormat, $current );
			$dates .= "'".$date."',";
			$current = strtotime( $step, $current );
		}
		$dates = rtrim($dates, ',') . ']';
		
		
	} else {
		
		
	}
	
	//global $firephp;
	//$firephp->log($dates);

	return $dates;
}


/**
 * 
 * Generic function to return the orderable dates of different types
 * @param String $type  getEmptyOrderableDates | getDatesWithOrders | getDatesWithSometimesOrderable | getAllOrderableDates
 */
/*
function get_orderable_dates($type)
{
	$rs = do_stored_query($type);
	
	$dates = '[';
	while ($row = $rs->fetch_array()) {
        $dates .= '"' . $row[0] . '",';
    }
	return rtrim($dates, ',') . ']';
}*/

/**
 * 
 * Adds dates to the dB; currently expets format yyyy-mm-dd as does mysql.
 * @param array $date_array  array of strings in the format yyyy-mm-dd
 */
/*
function add_orderable_dates($date_array)
{
	//TODO check if dates have correct format
	foreach($date_array as $date){
		do_stored_query('add_orderable_date',$date);
	}
	unset($date);
	
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