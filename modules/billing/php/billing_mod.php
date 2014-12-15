<?php 

require_once(__ROOT__ . 'php/external/FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start(); // Starts FirePHP output buffering
$firephp = FirePHP::getInstance(true);

require_once(__ROOT__ . 'php/lib/exceptions.php');
require_once(__ROOT__ . 'local_config/config.php');
require_once(__ROOT__ . 'php/inc/database.php');
require_once('export_bill.php');



class Bill {


    /**
     *  The id of the generate bill. Will be automatically assigned through db create
     *  @var int
     */
    private $bill_id = null;


    /**
     *  Contains the database xml result set of the given bill.
     *  This refers to the products boughts.  
     */
    private $bill_xml_detail = null;



    /**
     *  Contains the accounting infor for this bill, including IVA groups, bill number, accountable number of member
     *  date, number, name and NIF of member. 
     */
    private $bill_xml_accounting = null; 



    /**
     *  Constructs a bill. If bill_id is provided, the xml result set for this bill is retrieved. 
     *
     */
	public function __construct($bill_id=0)
    {    	

        if (is_numeric($bill_id) && $bill_id > 0 ){          
            $this->bill_id = $bill_id; 
            $this->load_data();
        } 

    } 


    private function load_data(){
        $this->bill_xml_detail =  stored_query_XML_fields("get_bill_detail", $this->bill_id);
        DBWrap::get_instance()->free_next_results();

        $tmp1 = stored_query_XML_fields("get_bill_accounting_detail", $this->bill_id);
        DBWrap::get_instance()->free_next_results();

        $tmp2 = stored_query_XML_fields("get_tax_groups", $this->bill_id);
        DBWrap::get_instance()->free_next_results();

        //get rid of </rowset><rowset> when joining two results sets
        $tmp1 = substr($tmp1, 0, strlen($tmp1)-15);
        $tmp2 = substr($tmp2, 13);


        //needs some specific formatting!!
        $this->bill_xml_accounting = $tmp1.$tmp2; 



        global $firephp;
        $firephp->log($this->bill_xml_accounting, "bill accounting detail");


    }


    /**
     *  Receives a list of cart_ids and creates a new bill. 
     *
     */
    public function create($arr_cart_ids, $description="", $ref_bill="", $date_for_bill="",  $operator_id)
    {

    	global $firephp; 

        if (!is_numeric($operator_id) || $operator_id <=0 ){
            throw new Exception("Billing exception: no operator (user) ID provided!");
        } else if (count($arr_cart_ids)==0){
            throw new Exception("Billing exception: missing ID(s) for carts!");
        } else if ($date_for_bill == ""){
            echo "date";
        }


    	$db = DBWrap::get_instance(); 

 		$uf_ids = array();               //get all uf_ids of carts
        $not_validated = array();        //save non-validate carts

        //get all carts 
    	foreach ($arr_cart_ids as $id){
    		
            $result = $db->squery('get_cart', $id);
            $assoc = $result->fetch_assoc();

            //save uf_id of cart
            array_push($uf_ids, $assoc["uf_id"]);

            //save non-validated carts. 
            if ($assoc["ts_validated"] == "0000-00-00 00:00:00"){
                array_push($not_validated, $assoc["id"]);
            }

            $db->free_next_results();
    	}



        //check if uf_id is consistent. can't put different ufs into same bill
        if (count(array_unique($uf_ids)) > 1) {
            throw new Exception("Billing error: can't include different UFs into same bill!!");
        }


        //validate open carts
        if (count($not_validated) >= 1){
            foreach ($not_validated as $cart_id) {
                $firephp->log($cart_id, "validating... ");
                
                $db->squery('validate_shop_cart', $cart_id, $operator_id);
                $db->free_next_results();
            }

        }

         $firephp->log($uf_ids[0], "uf id");


    	//create bill
        $last_insert = $db->squery('create_bill', $ref_bill, $uf_ids[0], $operator_id, $description);
        $this->bill_id = $last_insert->fetch_row()[0];  

        $firephp->log($this->bill_id, "bill id");

        $db->free_next_results();
    
        //and associate all carts to this bill
        foreach ($arr_cart_ids as $cart_id) {
           $db->squery("add_cart_to_bill", $this->bill_id, $cart_id);
           $db->free_next_results();
        }

        $this->load_data();
    }



    /**
     *  Exports a given bill to specified format. 
     *  @var details boolean    if set to true, exports the products of the bill. if set to false, exports the 
     *                          rather the accounting information. 
     *
     */
    public function export($filename="", $format="csv", $publish=0, $details=false){

        if ($details){
            $ep = new export_bill(0, $this->bill_xml_detail,  $filename);
        } else {
            $ep = new export_bill(0, $this->bill_xml_accounting, $filenmae);

        }

        $ep->export(false, "csv");

    }
    






    /**
     * Deletes an existing bill; implies to delete all entries from bill_rel_cart table and 
     * unvalidate carts. 
     */
    public function delete($id)
    {



    }


}



?>