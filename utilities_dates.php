<?php

require_once('inc/database.php');
require_once('local_config/config.php');
require_once ('utilities.php');

$firephp = FirePHP::getInstance(true);
ob_start(); // Starts FirePHP output buffering

function get_orderable_dates($type)
{
	$rs = do_stored_query($type);
	
	$dates = '[';
	while ($row = $rs->fetch_array()) {
        $dates .= '"' . $row[0] . '",';
    }
	return rtrim($dates, ',') . ']';
}

function add_orderable_dates($date_array)
{
	foreach($date_array as $date){
		do_stored_query('add_orderable_date',$date);
	}
	unset($date);
	
}

function generate_date_pattern($weekDays, $nrMonth, $frequency)
{
	$dates = array();
	$gc = 0; 
	$totalweeks = $nrMonth * 4; 
	
	for ($w=0; $w<$totalweeks; $w+=$frequency){
		foreach($weekDays as $day){
				$dates[$gc] = 	strftime('%Y-%m-%d', strtotime('next ' . $day . ' + '.$w.' week'));
				$gc++;	
		}
		unset($day);
	}	
	return $dates;
}

function generate_and_add_date_pattern($weekDays, $nrMonth, $frequency){
	$dates = generate_date_pattern($weekDays, $nrMonth, $frequency);
	if (count($dates)> 0){
		add_orderable_dates($dates);
		return 1; 
	}	else {
		return 0;
	}
}


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