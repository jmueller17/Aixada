<?php

define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__)))).DS); 


require_once(__ROOT__ . "php/utilities/general.php");

require_once(__ROOT__ . "php/lib/aixmodel.php");

require_once("incidents_mod.php");




if (!isset($_SESSION)) {
    session_start();
}

try{

    switch (get_param('oper')) {

    	case 'testAix':
    		$ax = new aixmodel("aixada_unit_measure");

    		$ax->read_form_submit();
    		//$data = array("id"=>43, "name"=>"poundxx", "unit"=>"pxx");
    		//$ax->delete(43);

    		exit; 
    	    	
    	 case 'getIncidentTypes':
	        printXML(stored_query_XML_fields('get_incident_types'));
	        exit;        

	    //if incident_id > 0 edit, otherwise create new
	    case 'mngIncident':

			$ax = new Incident();
			$ax->read_form_submit();

	       	//echo manage_incident(get_param('incident_id',0));
	        exit;
				
	    case 'delIncident':
	        echo do_stored_query('delete_incident', get_param('incident_id'));
	        //echo 1;
	        exit;

	    case 'getIncidentsListing':

	        //echo get_incidents_in_range(get_param('filter', 'month'), get_param('fromDate',0), get_param('toDate',0), get_param('type',1) );
	    	exit; 
	    	
	    case 'getIncidentsById':
	    	printXML(stored_query_XML_fields('get_incidents_by_ids', get_param('idlist'), get_param('type',0)));	
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