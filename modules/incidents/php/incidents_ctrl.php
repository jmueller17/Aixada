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
	    case 'mngIncident':
			$in = new Incident();
			$in->read_form_submit();

			//if id is given, edit existing incident, otherwise create new
			if ($id >get_param('incident_id',0)){
				$in->edit($id);
			} else {
				$in->insert();
			}	

	        exit;
				
	    case 'delIncident':
		    $in = new Incident();
		    $in->delete(get_param('incident_id')); 
	        exit;

	    case 'getIncidentsListing':
	        print_stored_query('xml','get_incidents_listing', get_param('from_date',''), get_param('to_date',''), get_param('type','1') );            
	    	exit; 
	    	
	    case 'getIncidentsById':
	    	print_stored_query('xml','get_incidents_by_ids', get_param('idlist'), get_param('type',0)  );
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