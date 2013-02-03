<?php


require_once(__ROOT__ . 'php/inc/database.php');
require_once(__ROOT__ . 'php/utilities/general.php');
require_once(__ROOT__ . 'local_config/config.php');


function product_prices_json($product_id_array, $year_array)
{
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
		$piy .= '{"day":' . $row['day'] 
		    . ',"price":' . $row['price'] . '}';
	    }
	    $piy .= ']';
	    if ($first_series) {
		$first_series = false;
	    } else $json .= ',';
	    $json .= $piy;
	}
    }
    return $json;
}

	
?>