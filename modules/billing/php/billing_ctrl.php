<?php
define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(dirname(__FILE__)))).DS); 

require_once(__ROOT__ . 'php/external/FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start(); // Starts FirePHP output buffering
$firephp = FirePHP::getInstance(true);


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

        case 'deleteBill':

            $b = new Bill(get_param("bill_id"));
            $b->delete();

            exit; 

        case 'getBillListing':
            print_stored_query('xml','get_bills', get_param('uf_id',0), get_param('from_date',''), get_param('to_date',''), get_param('limit','') );            
            exit;


        case 'getBillDetail':
            print_stored_query('xml','get_bill_detail', get_param('bill_id',0));
            exit;

        case 'getBillTaxGroups':
            print_stored_query('xml','get_tax_groups', get_param('bill_id',0));
            exit; 

        case 'getCartListing':
            print_stored_query('xml','get_cart_listing', get_param('uf_id',0), get_param('from_date', ''), get_param('to_date',''), get_param('limit','')  );
            exit; 

        case 'exportAccounting':

            $bills = get_param("bill_ids");
            $ofname = "output_format_" . get_param("format", "csv");
            
            foreach($bills as $id){

                $b = new Bill($id);
                $dt = $b->get_accounting_info();

                
                $out_formatter = new $ofname($dt);

                Deliver::get_instance()->serve_outf_file($out_formatter);
               
            }

            

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