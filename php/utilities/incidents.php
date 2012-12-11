<?php


require_once(__ROOT__. 'php/inc/database.php');
require_once(__ROOT__. 'local_config/config.php');
require_once ('general.php');
require_once(__ROOT__ . 'local_config/lang/'.get_session_language() . '.php');


include("../external/mpdf54/mpdf.php");

function get_incidents_as_pdf($idlist){
	
	global $Text; 

	$htmlstr = '<html><head><title>Incidents</title>';
	$htmlstr .= '<style type="text/css">body {font-family:arial; font-size:10px; } table	{width:100%; border-collapse:collapse;} thead {background:#efefef;}th {border:solid 1px black; padding:2px 5px; background:#efefef;} td.headc	 		{border:solid 1px black; padding:2px 5px; background:#efefef; text-align:center;} td {padding:3px;} .section  {width:90%; clear:both; margin-bottom:10px;} .txtAlignRight		{text-align:right;} .txtAlignCenter		{text-align:center;} .tdAlignTop			{vertical-align:top;} .cellBorderList td	{border:solid 1px black; padding:2px 5px;}';
	$htmlstr .= '</style></head><body>'; 
	$htmlstr .= '<div id="logo"><img alt="coop logo" src="../../img/tpl_header_logo.png" width="500" height="180"/></div><br/><br/><br/>';
	$htmlstr .= '<h2>'.$Text['ti_incidents'] .'</h2>';
	$htmlstr .= '<table id="tbl_incidents" class="ui-widget"><thead>';
	$htmlstr .= '<tr><th>id</th><th>priority</th><th>created_by</th>'; 
	$htmlstr .=	'<th>'. $Text['created'] . '</th>';
	$htmlstr .=	'<th>'.  $Text['status']. '</th>';
	$htmlstr .=	'<th>'.  $Text['provider_name']. '</th>';
	$htmlstr .=	'<th>'.  $Text['ufs_concerned']. '</th>';
	$htmlstr .=	'<th>'.  $Text['comi_concerned']. '</th></tr></thead><tbody>';
							

	$rs = do_stored_query('get_incidents_by_ids', $idlist, 0);
	
	while ($row = $rs->fetch_assoc()) {
								
		$htmlstr .= '<tr><td class="headc">'.$row["id"].'</td>';
		$htmlstr .= '<td class="headc">'.$row["priority"].'</td>';
		$htmlstr .= '<td class="headc">'.$row["uf_id"].'</td>';
		$htmlstr .= '<td class="headc">'.$row["ts"].'</td>';
		$htmlstr .= '<td class="headc">'.$row["status"].'</td>';
		$htmlstr .= '<td class="headc">'.$row["provider_name"].'</td>';
		$htmlstr .= '<td class="headc">'.$row["ufs_concerned"].'</td>';
		$htmlstr .= '<td class="headc">'.$row["commission_concerned"].'</td>';

		$htmlstr .= '<tr><td></td>';
		$htmlstr .= '<td>'.$Text['subject'].':</td>';
		$htmlstr .= '<td colspan="10" class="noBorder">'.$row["subject"].'</td>';
		$htmlstr .= '</tr><tr><td class="noBorder"></td>';
		$htmlstr .= '<td class="tdAlignTop">'.$Text['message'].'</td>';
		$htmlstr .= '<td class="tdAlignTop" colspan="10">'.$row["details"].'</td>';
		$htmlstr .= '</tr><tr><td colspan="12" class="noBorder"><br/></td></tr>';
		
		
		
	}

	$htmlstr .= '</tbody></table></body></html>';

	$mpdf=new mPDF(); 

	$mpdf->WriteHTML($htmlstr);
	return $mpdf; 

}


/**
 * 
 * Creates new incident or edits existing one if incident_id is given. 
 */
function manage_incident($incident_id){
	
	
	$params = extract_incident_form_values();
	
	$sendEmail = configuration_vars::get_instance()->internet_connection;
	
	//email incident
	if ($sendEmail &&  ($params["type"] == 2 || $params["type"] == 4)){
		
		$to = "someone@someplace.com"; //here goes the user email list
		$subject = "[Aixada] ".$params["subject"];
		$message = $params["msg"];
		$from = "admin@aixada.org";
		$headers = "From:" . $from;
		mail($to,$subject,$message,$headers);
		
	}
	
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