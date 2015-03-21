<?php


require_once(__ROOT__ . 'php/inc/database.php');
require_once(__ROOT__ . 'local_config/config.php');


/**
 * 
 * retrieves list of UFs with negative account balance
 */
function get_negative_accounts()
{
    $strXML = '<negative_accounts>';
    $rs = do_stored_query('negative_accounts');
    while ($row = $rs->fetch_assoc()) {
        $strXML .= '<account>';
        foreach ($row as $field => $value) {
            $strXML .= "<{$field}><![CDATA[{$value}]]></{$field}>";
        }
        $strXML .= '</account>';
    }
    $strXML .= '</negative_accounts>';
    return $strXML;
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

?>