<?php


require_once(__ROOT__ . 'php/inc/database.php');
require_once(__ROOT__ . 'php/utilities/general.php');
require_once(__ROOT__ . 'local_config/config.php');

require_once(__ROOT__ .'php/external/FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start(); // Starts FirePHP output buffering
$firephp = FirePHP::getInstance(true);

function product_prices_times_years($product_id_array, $year_array)
{
    $query = 'select id, name from aixada_product where id in (';
    foreach ($product_id_array as $product_id) {
	$query .= $product_id . ',';
    }
    $query = rtrim($query, ',');
    $query .= ');';
    $rs = DBWrap::get_instance()->Execute($query);
    $name = array();
    while ($row = $rs->fetch_assoc()) {
	$name[$row['id']] = '"' . $row['name'] . '"';
    }
    DBWrap::get_instance()->free_next_results();

    $json = '[';
    $first_series = true;
    foreach($product_id_array as $product_id) {
	foreach ($year_array as $year) {
	    $rs = do_stored_query('product_prices_in_year', $product_id, $year);
	    $piy = '[';
	    $first_piy = true;
	    while ($row = $rs->fetch_assoc()) {
		if ($first_piy) {
		    $first_piy = false;
		} else $piy .= ',';
		$piy .= '{"week":' . $row['week'] 
		    . ',"price":' . $row['price'] . '}';
	    }
	    DBWrap::get_instance()->free_next_results();
	    $piy .= ']';
	    if ($first_series) {
		$first_series = false;
	    } else $json .= ',';
	    $json .= '['
		.'[' . $product_id . ',' . $name[$product_id] . ',' . $year . '],'
		. $piy
		. ']';
	}
    }
    $json .= ']';
    global $firephp;
    $firephp->log($json);
    return $json;
}

	
?>