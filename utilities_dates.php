<?php

require_once('inc/database.php');
require_once('local_config/config.php');
require_once ('utilities.php');
//$firephp = FirePHP::getInstance(true);


function get_empty_orderable_dates()
{
	$rs = do_stored_query('get_empty_orderable_dates');
	
	$dates = '[';
	while ($row = $rs->fetch_array()) {
        $dates .= '"' . $row[0] . '",';
    }
	return rtrim($dates, ',') . ']';
}


function get_dates_with_orders() 
{
	$rs = do_stored_query('get_nonempty_orderable_dates');
	
	$dates = '[';
	while ($row = $rs->fetch_array()) {
        $dates .= '"' . $row[0] . '",';
    }
	return rtrim($dates, ',') . ']';
}


function get_all_orderable_dates()
{
	$rs = do_stored_query('get_all_orderable_dates');
	$dates = '[';
	while ($row = $rs->fetch_array()) {
        $dates .= '"' . $row[0] . '",';
    }
	return rtrim($dates, ',') . ']';
}


function add_date($date)
{
	try{
		do_stored_query('add_orderable_dates',$date);
		return 1; 
	} catch(Exception $e) {
  		return $e->getMessage();
	} 

}

function remove_date($date)
{
    DBWrap::get_instance()->Execute("replace into aixada_orderable_dates values (:1q, 0)", $date);
    return 1;
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