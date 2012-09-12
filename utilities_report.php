<?php

require_once('inc/database.php');
require_once('local_config/config.php');



/**
 * 
 * Lists all accounts, active and non-active ufs. 
 */
function get_all_accounts()
{
  $strXML = '<accounts>'
    . '<row><id f="id">-3</id><name f="name">Caixa</name></row>'
    . '<row><id f="id">-2</id><name f="name">Consum</name></row>'
    . '<row><id f="id">-1</id><name f="name">Manteniment</name></row>';
  $rs = DBWrap::get_instance()->Execute("SELECT id+1000, id, name FROM aixada_uf");
  while ($row = $rs->fetch_array()) {
    $strXML 
      .= '<row>'
      . '<id f="id">' . $row[0] . '</id>'
      . '<name f="name"><![CDATA[UF ' . $row[1] . ' ' . $row[2] . ']]></name>'
      . '</row>';
  }
  return $strXML . '</accounts>';
}


/**
 * 
 * produces an extract of the money movements for the selected account and time-period
 * @param unknown_type $account_id
 * @param unknown_type $filter
 * @param unknown_type $from_date
 * @param unknown_type $to_date
 */
function get_account_extract($account_id, $filter, $from_date, $to_date)
{

	$today = date('Y-m-d', strtotime('Today'));
	$tomorrow = date('Y-m-d', strtotime('Today + 1 day'));
	$prev_2month = date('Y-m-d', strtotime('Today - 2 month'));
	$prev_year	 = 	date('Y-m-d', strtotime('Today - 13 month'));
	
	$very_distant_future = '9999-12-30';
	$very_distant_past	= '1980-01-01';
	
	$account_id = (0< $account_id and $account_id < 1000)? $account_id+1000:$account_id;
	
	
	switch ($filter) {
		// all orders where date_for_order = today
		case 'past2Month':
			printXML(stored_query_XML_fields('get_extract_in_range', $account_id, $prev_2month, $tomorrow));
			break;		

		case 'pastYear':
			printXML(stored_query_XML_fields('get_extract_in_range', $account_id, $prev_year, $tomorrow));
			break;		
	
		case 'today':
			printXML(stored_query_XML_fields('get_extract_in_range', $account_id, $today, $tomorrow));
			break;
			
		case 'exact':
			printXML(stored_query_XML_fields('get_extract_in_range', $account_id, $from_date, $to_date));
			break;
			
		case 'all':
			printXML(stored_query_XML_fields('get_extract_in_range', $account_id, $very_distant_past, $very_distant_future));
			break;
			
			
		default:
			throw new Exception("account_extract: param={$filter} not supported");  
			break;
	}
}



function spending_per_provider_JSON($the_date) 
{
    $data = array(); 
    $cum_sum = 0;
    $cum = array();
    $curr_prov = '';
    $first_prov = true;
    $min_month = 13;
    $max_month = 0;
    $rs = do_stored_query('spending_per_provider', $the_date);
    while ($row = $rs->fetch_assoc()) {
        if ($row['name'] != $curr_prov) {
            $curr_prov = $row['name'];
            $prov_name = $row['id'] . ' ' . $curr_prov;
            $data[$prov_name] = array();
            $cum[$prov_name] = array();
            $cum_sum = 0;
        } 
        $month = $row['m'];
        $data[$prov_name][$month] = $row['s'];
        $cum[$prov_name][$month] = $cum_sum;
        $cum_sum += $row['s'];
        $min_month = min($min_month, $month);
        $max_month = max($max_month, $month);
    }
    $json = '[';
    $first_prov = true;
    foreach($data as $prov_name => $spending) {
        if (count($spending) < 2) continue;
        if (!$first_prov) {
            $json .= ',';
        } else {
            $first_prov = false;
        }
        //        $json .= '"' . $prov_name . '":[';
        $json .= '[';
        $first_data = true;
        $cum_sum = 0;
        for ($m=$min_month; $m<=$max_month; $m++) {
            if (!$first_data) {
                $json .= ',';
            } else {
                $first_data = false;
            }
            if (isset($spending[$m])) {
                $s = $spending[$m];
                $c = $cum[$prov_name][$m];
                $cum_sum = $c;
            } else {
                $s = 0;
                $c = $cum_sum;
            }
            $json .= '{"x":' . $m . ',"y":' . $c 
                //                . ',"y0":' . $c
                . '}';
        }
        $json .= ']';
    }
    $json .= ']';
    return $json;
}

	
?>