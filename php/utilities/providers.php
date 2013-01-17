<?php


require_once(__ROOT__. 'php/inc/database.php');
require_once(__ROOT__. 'local_config/config.php');
require_once ('general.php');
require_once(__ROOT__ . 'local_config/lang/'.get_session_language() . '.php');


/**
 * 
 * Creates new provider or edits existing one if provider_id is given. 
 */
function manage_provider($provider_id){
	
	global $Text;  	
	
	$params = extract_provider_form_values();


	
	echo do_stored_query('manage_provider', 
						$provider_id, 
						$params["name"], 
						$params["contact"],
						$params["address"],
						$params["nif"],
						$params["zip"],					
						$params["city"],
						$params["phone1"],
						$params["phone2"],
						$params["fax"],
						$params["email"],
						$params["web"],
						$params["bank_name"],
						$params["bank_account"],
						$params["picture"],
						$params["notes"],
						$params["active"],
						$params["responsible_uf_id"],
						$params["offset_order_close"]);
						 
	
} // end manage incidents


/**
 * 
 * Extracts the form field values for providers and assigns defaults
 * values if a given param is empty. 
 */
function extract_provider_form_values(){
	
		
	$fields["name"] = get_param('name');
	$fields["contact"] = get_param('contact','');
	$fields["address"] = get_param('address','');
	$fields["nif"] = get_param('nif','');
	$fields["zip"] = get_param('zip','');
	$fields["city"] = get_param('city','');
	$fields["phone1"] = get_param('phone1','');
	$fields["phone2"] = get_param('phone2','');
	$fields["fax"] = get_param('fax','');
	$fields["email"] = get_param('email','');
	$fields["web"] = get_param('web','');
	$fields["bank_name"] = get_param('bank_name','');
	$fields["bank_account"] = get_param('bank_account','');
	$fields["picture"] = get_param('picture','');
	$fields["notes"] = get_param('notes','');
	$fields["active"] = isset($_REQUEST['active'])? 1:0; //this is a checkbox; gets send when checked, otherwise not
	$fields["responsible_uf_id"] = get_param('responsible_uf_id','');
	$fields["offset_order_close"] = get_param('offset_order_close','');
	
	return $fields;
}


/**
 * 
 * Retrieves incident listing. 
 * @param string $filter shortcut to generate dates for a given time range. 
 * @param date $from_date if filter is set to "exact", requires a starting date for filtering the incidents
 * @param date $to_date
 * @param int $filterType integer from 1-4 specying the distribution level of the incident: internal, email, portal or all together. 
 */
function get_incidents_in_range($filter, $from_date, $to_date, $filterType=1){
	$today = date('Y-m-d', strtotime('Today'));
	$tomorrow = date('Y-m-d', strtotime('Today + 2 day'));
	$yesterday = date('Y-m-d', strtotime('Today - 1 day'));
	$prev_2month = date('Y-m-d', strtotime('Today - 2 month'));
	$prev_year	 = 	date('Y-m-d', strtotime('Today - 13 month'));
	$prev_week = date('Y-m-d', strtotime('Today - 1 week'));
	$very_distant_future = '9999-12-30';
	$very_distant_past	= '1980-01-01';
	
	switch ($filter) {
		// all orders where date_for_order = today
		case 'past2Month':
			printXML(stored_query_XML_fields('get_incidents_listing', $prev_2month, $tomorrow, $filterType));
			break;
		
		case 'pastWeek':
			printXML(stored_query_XML_fields('get_incidents_listing', $prev_week, $tomorrow, $filterType));
			break;
			
		case 'pastYear':
			printXML(stored_query_XML_fields('get_incidents_listing', $prev_year, $tomorrow, $filterType));
			break;
			
		case 'today':
			printXML(stored_query_XML_fields('get_incidents_listing', $yesterday, $tomorrow, $filterType));
			break;
			
			
		default:
			throw new Exception("get_incidents_in_range: param={$filter} not supported");  
			break;
	}
	
}






	
?>