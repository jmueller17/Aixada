<?php
define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__)))).DS); 

require_once(__ROOT__ . "local_config/config.php");
require_once(__ROOT__ . "php/inc/database.php");
require_once(__ROOT__ . "php/utilities/general.php");

require_once("billing_mod.php");

if (!isset($_SESSION)) {
    session_start();
}

try{

    switch (get_param('oper')) {
    	
    	//returns a list of all purchases for given uf. 
    	case 'createBill':
            $emptyArr = array();

            $bill = new Bill();
            $bill->create(get_param('cart_ids',$emptyArr), get_param('description',""), get_param('ref_bill_id',""), "", get_session_user_id());

    		exit; 

        case 'getBillListing':
            printXML(stored_query_XML_fields('get_bills', get_param('uf_id',0), get_param('from_date',''), get_param('to_date',''), get_param('limit','') ));  
            exit;


        case 'getBillDetail':
            printXML(stored_query_XML_fields('get_bill_detail', get_param('bill_id',0)));
            exit;

    		
    default:  
    	 throw new Exception("ctrl billing: oper={$_REQUEST['oper']} not supported");  
        break;
    }


} 

catch(Exception $e) {
    header('HTTP/1.0 401 ' . $e->getMessage());
    die ($e->getMessage());
}  


?>