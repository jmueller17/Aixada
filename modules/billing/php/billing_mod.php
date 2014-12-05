<?php 

require_once(__ROOT__ . 'php/external/FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start(); // Starts FirePHP output buffering
$firephp = FirePHP::getInstance(true);

require_once(__ROOT__ . 'php/lib/exceptions.php');
require_once(__ROOT__ . 'local_config/config.php');
require_once(__ROOT__ . 'php/inc/database.php');



class Bill {


	public function __construct()
    {    	

    	
    } 



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
        $bill_id = $last_insert->fetch_row()[0];  

        $firephp->log($bill_id, "bill id");

        $db->free_next_results();
    
        //and associate all carts to this bill
        foreach ($arr_cart_ids as $cart_id) {
           $db->squery("add_cart_to_bill", $bill_id, $cart_id);
           $db->free_next_results();
        }


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