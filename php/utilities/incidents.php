<?php


require_once(__ROOT__. 'php/inc/database.php');
require_once(__ROOT__. 'local_config/config.php');
require_once ('general.php');
require_once(__ROOT__ . 'local_config/lang/'.get_session_language() . '.php');


/**
 * 
 * Creates new incident or edits existing one if incident_id is given. 
 * If internet connection is available and the distribution level of the incident
 * is set to 2 (email) or 4 (post to portal and email) the incident will be emailed 
 * to all members or to a distribution list set in the config vars. 
 */
function manage_incident($incident_id){
	
	global $Text;  	
	
	$gVars = configuration_vars::get_instance(); 
	
	$params = extract_incident_form_values();
	
	$msg = ""; 
	
	
	//new incident with distribution level email or all
	if ($incident_id == 0 && ($params["type"] == 2 || $params["type"] == 4) && $gVars->internet_connection){
		

		//configuration_vars::get_instance()->$coop_name
		
		$to = "";
		$reply_to = null;  
		
		//if an email list has been set, use it 
		if ($gVars->incidents_email_list != ""){
			$to = $gVars->incidents_email_list; 
			$reply_to = $gVars->incidents_email_list; 
			
		//otherwise email to individual active users. 
		} else {
			//get active members
			$rs = do_stored_query('get_member_listing', 1); 
	    	$to = get_list_rs($rs, 'email');
			
		}
		

		$subject = "[".$Text['ti_incidents']."] " . $params["subject"];
		$message = "Info: " . $params["author"] . " | " . $params["comis"] . " | " .$params["prov"] . " | " . $params["priority"]; 
        $message .= "\n";
		$message .= '<div style="margin:1em; padding: 5px; font-size:120%;'
            .'border:1px #888 solid; background-color:#ddd;">'
            .str_replace(
                array("<br>   ",          "<br> ",      "<br>"),
                array("<br>&nbsp;&nbsp;", "<br>&nbsp;", "<br>\n"),
                str_replace(
                    array("\r\n","\r","\n"),
                    array("<br>","<br>","<br>"),
                    $params["msg"]
            ) )."</div>\n";

		if (send_mail($to,$subject,$message,array('reply_to'=>$reply_to))){
			$msg = $Text['msg_incident_emailed'];			
		} else {
			$msg =  $Text['msg_err_emailed'];		
		}

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
						 
	
} // end manage incidents


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