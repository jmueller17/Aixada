<?php

require_once('inc/database.php');
require_once('local_config/config.php');
//$firephp = FirePHP::getInstance(true);

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

function get_dates_with_orders() 
{
    $json = '[';
    foreach(make_next_shop_dates() as $d => $has_order) {
        if ($has_order) {
            $json .= '"' . $d . '",';
        }
    }
    return rtrim($json, ',') . ']';
}

function add_date($date)
{
    DBWrap::get_instance()->Execute("replace into aixada_shopping_dates values (:1q, 1)", $date);
    return 1;
}

function remove_date($date)
{
    DBWrap::get_instance()->Execute("replace into aixada_shopping_dates values (:1q, 0)", $date);
    return 1;
}

	
?>