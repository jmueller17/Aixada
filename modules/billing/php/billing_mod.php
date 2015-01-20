<?php 

require_once(__ROOT__ . 'php/lib/exceptions.php');
require_once(__ROOT__ . 'local_config/config.php');
require_once(__ROOT__ . 'php/inc/database.php');
require_once(__ROOT__ . 'php/lib/output_format.php');


class Bill {


    /**
     *  The id of the generate bill. Will be automatically assigned through db create
     *  @var int
     */
    private $bill_id = null;



    /**
     *  Constructs a bill. If bill_id is provided, the xml result set for this bill is retrieved. 
     *  @param int $bill_id The id of an existing bill. 
     *
     */
	public function __construct($bill_id=0)
    {    	

        //for given id, check if it exists. 
        if (is_numeric($bill_id) && $bill_id > 0 ){          

            $db = DBWrap::get_instance(); 

            //check if bill with give id exists
            $rs = $db->squery("get_bill", $bill_id);
            $db->free_next_results();

            if ($rs->num_rows != 1){
                throw new Exception("Bill execption: provided bill_id {$bill_id} does not exist!");
            }

        } 

        $this->bill_id = $bill_id; 

    } 




    /**
     *  Returns the billing info (not shop details of bill) including tax groups of the current bill. 
     *  @return array
     *
     */
    public function get_accounting_info(){
        
        $db = DBWrap::get_instance(); 

        global $Text; 

        $rs1 = $db->squery("get_bill_accounting_detail", $this->bill_id);
        $db->free_next_results();

        $rs2 = $db->squery("get_tax_groups", $this->bill_id);
        $db->free_next_results();


        $of1 = new output_format($rs1);
        $dt1 = $of1->get_data_table();

        $of2 = new output_format($rs2);
        $dt2 = $of2->get_data_table(false); 


        foreach ($dt2 as $row){
            $colname = $Text["iva"] ."_" . $row[0];
            $dt1[0][] = $colname;
            $dt1[1][] = $row[2];
        }

        return $dt1; 
    }




    /**
     *  Receives a list of cart_ids and creates a new bill. Non-validated carts will be validated. 
     *
     */
    public function create($arr_cart_ids, $description="", $ref_bill="", $date_for_bill="",  $operator_id)
    {

    	//global $firephp; 

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
                //$firephp->log($cart_id, "validating... ");
                
                $db->squery('validate_shop_cart', $cart_id, $operator_id);
                $db->free_next_results();
            }

        }

        //$firephp->log($uf_ids[0], "carts belong to uf");


    	//create bill
        $last_insert = $db->squery('create_bill', $ref_bill, $uf_ids[0], $operator_id, $description);
        $this->bill_id = $last_insert->fetch_row()[0];  

        //$firephp->log($this->bill_id, "bill id");

        $db->free_next_results();
    
        //and associate all carts to this bill
        foreach ($arr_cart_ids as $cart_id) {
           $db->squery("add_cart_to_bill", $this->bill_id, $cart_id);
           $db->free_next_results();
        }

        return $this->bill_id; 
    }




    /**
     * Deletes an existing bill; implies to delete all entries from bill_rel_cart table. 
     *  @return int $bill_id Returns the id of the deleted bill. 
     */
    public function delete()
    {
        $db = DBWrap::get_instance();
        $db->squery('delete_bill', $this->bill_id);
        $db->free_next_results();

        return $this->bill_id; 
    }

}



?>