<?php


require_once(__ROOT__. 'php/inc/database.php');
require_once(__ROOT__. 'local_config/config.php');
require_once ('general.php');


/**
 * 
 * Creates new incident or edits existing one if incident_id is given. 
 */
function manage_incident($incident_id){
	
	$params = extract_incident_form_values();
	
	echo do_stored_query('manage_incident', 
						$incident_id, 
						$params["subject"], 
						$params["type"],
						$params["author"],
						$params["msg"],
						$params["priority"],					
						$params["ufs"],
						$params["comis"],
						$params["prov"],
						$params["status"]);
	
}


/**
 * 
 * Extracts the form field values for incidents and assigns defaults
 * values if a given param is empty. 
 */
function extract_incident_form_values(){
	
		
	$fields["subject"] = get_param('subject','-');
	$fields["type"] = get_param('typeSelect',4);
	$fields["author"] = get_session_user_id();
	$fields["msg"] = get_param('incidents_text','');
	$fields["priority"] = get_param('prioritySelect',1);
	$fields["ufs"] = '';
	foreach($_REQUEST['ufs_concerned'] as $uf){
		$fields["ufs"] .= $uf . ',';
	}
	$fields["ufs"] = rtrim($fields["ufs"], ',');
	$fields["comis"] = get_param('commissionSelect','');
	$fields["prov"] = get_param('providerSelect','');
	$fields["status"] = get_param('statusSelect',1);
	
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
			printXML(stored_query_XML_fields('get_incidents_listing', $today, $tomorrow, $filterType));
			break;
			
		case 'exact':
			printXML(stored_query_XML_fields('get_purchase_listing', $from_date, $to_date, $filterType));
			break;
			
		case 'all':
			printXML(stored_query_XML_fields('get_purchase_listing', $very_distant_past, $very_distant_future, $filterType));
			break;
			
			
		default:
			throw new Exception("get_incidents_in_range: param={$filter} not supported");  
			break;
	}
	
}

	
?>