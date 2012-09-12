<?php


require_once("local_config/config.php");
require_once("inc/database.php");
require_once("utilities.php");
require_once("utilities_incidents.php");



if (!isset($_SESSION)) {
    session_start();
}

try{

    switch (get_param('oper')) {
    	    	
    	 case 'getIncidentTypes':
	        printXML(stored_query_XML_fields('get_incident_types'));
	        exit;        

	    //if incident_id > 0 edit, otherwise create new
	    case 'mngIncident':
	       	echo manage_incident(get_param('incident_id',0));
	        exit;
				
	    case 'delIncident':
	        echo do_stored_query('delete_incident', get_param('incident_id'));
	        //echo 1;
	        exit;
	       
	    case 'getIncidentsListing':
	        echo get_incidents_in_range(get_param('filter', 'prev2Month'), get_param('fromDate',0), get_param('toDate',0), get_param('limit',1) );
	    	exit; 
	    	
	    default:  
	    	 throw new Exception("ctrlIncidents: oper={$_REQUEST['oper']} not supported");  
	        break;
    }


} 

catch(Exception $e) {
    header('HTTP/1.0 401 ' . $e->getMessage());
    die ($e->getMessage());
}  


?>