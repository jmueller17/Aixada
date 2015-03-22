<?php



require_once(__ROOT__ . 'php/inc/database.php');
require_once(__ROOT__ . 'local_config/config.php');
require_once(__ROOT__ . 'php/utilities/general.php');
require_once(__ROOT__ . 'local_config/lang/'.get_session_language() . '.php');



/**
 * 
 * recalculates the individual order quanties when adjusting the total delivered quantity. 
 * @param unknown_type $order_id
 * @param unknown_type $product_id
 * @param unknown_type $new_total_quantity
 */
function edit_total_order_quantities($order_id, $product_id, $new_total_quantity){
	
	$rs = do_stored_query('get_order_item_detail', $order_id, 0,0, 0,$product_id );
	
	$uf_qu = array();
	
	$total_quantity = 0; 
	//for each uf 
	while ($row = $rs->fetch_assoc()) {
		$uf_qu[$row['uf_id']] =  $row['quantity'];
	 	$total_quantity = $total_quantity + $row['quantity'];	 
	}		
	DBWrap::get_instance()->free_next_results();
	
	//calc and save adjusted quantities for each uf
	$xml = '<total>'.$total_quantity.'</total>';
	$xml .= '<rows>';
	foreach ($uf_qu as $uf_id => $quantity){
	    $new_quantity = round(($quantity / $total_quantity) * $new_total_quantity, 3);
	    do_stored_query('modify_order_item_detail', $order_id, $product_id, $uf_id, $new_quantity);
	    $xml .= "<row><uf_id>${uf_id}</uf_id><quantity>${new_quantity}</quantity></row>";
	}
	DBWrap::get_instance()->free_next_results();
	
	printXML($xml . '</rows>');
}


/**
 * Prepare the order to review: Insert order items into aixada_order_to_shop 
 *      if is needed.    
 */        
function prepare_order_to_shop($order_id) {
    $ok = do_stored_query('modify_order_item_detail', $order_id, 0, 0, 0);
    if (!$ok) {
        throw new Exception(
                "An error occured during preparing aixada_order_to_shop!!");      			
    }
}

/**
 * Change individual order quantity. 
 * @param integer $order_id
 * @param integer $product_id
 * @param integer $uf_id
 * @param number $quantity
 * @return number The quantity if all is ok
 */
function edit_order_quantity($order_id, $product_id, $uf_id, $quantity){
    // Check parameters
    if (!is_numeric($order_id)) {
        throw new Exception("`order_id` must be integer!");      			
    }
    if (!is_numeric($product_id)) {
        throw new Exception("`product_id` must be integer!");      			
    }
    if (!is_numeric($uf_id)) {
        throw new Exception("`uf_id` must be integer!");     			
    }
    if (!is_numeric($quantity)) {
        throw new Exception("`quantity` must be numeric!");      			
    }
    // Check if exist
    prepare_order_to_shop($order_id);
    $item = get_row_query("
        select uf_id from aixada_order_to_shop os
        where os.product_id = {$product_id}
            and os.order_id = {$order_id}
            and os.uf_id = {$uf_id};");
	if ($item) {
        // Update quantity
        $ok = do_stored_query('modify_order_item_detail', 
                                $order_id, $product_id, $uf_id, $quantity);
    } else {
        // New aixada_order_item and aixada_order_to_shop
        // 0. Insert oder items to aixada_order_to_shop table if is needed.            
        // 1. Copy any aixada_order_item on order and protduct with 0 quantity
        $db = DBWrap::get_instance();
    //TODO: Use $db->beginTransaction() $db->commit() and $db->rollback()
        $ok = $db->Execute("
            INSERT INTO aixada_order_item (
                uf_id, order_id, unit_price_stamp, date_for_order, 
                product_id, quantity )
            SELECT {$uf_id}, order_id, unit_price_stamp, date_for_order,
                product_id, 0
            FROM aixada_order_item 
            WHERE order_id={$order_id} and product_id = {$product_id}
            LIMIT 1;");
        if (!$ok) {
            throw new Exception("No order for this product!!");
        }
        $new_order_item_id = $db->last_insert_id();
        if (!$new_order_item_id) {
            throw new Exception("No order id for this product!!");
        }
        // 2. Insert new aixada_order_to_shop with the quantity
        $ok = $db->Execute("
            INSERT INTO aixada_order_to_shop (
                order_item_id, uf_id, order_id, unit_price_stamp, product_id,
                quantity, arrived, revised
            )
            SELECT
                id, uf_id, order_id, unit_price_stamp, product_id,
                {$quantity}, 1, 1
            FROM aixada_order_item
            WHERE id = {$new_order_item_id};"
        );
    }
    if (!$ok) {
        throw new Exception(
                   "An error occured during saving the new product quantity!!");
    }
    return $quantity;
}

/**
 * Change the gross price of a product into a order, all 
 *      `aixada_order_to_shop.unit_price_stamp` of the product of the order are
 *      updated as final UF price (gross price + VAT + Rev.Tax)
 * @param integer $order_id
 * @param integer $product_id
 * @param number $gross_price
 * @return string If all is ok returns a sting as
 *      "OK;_the_gross_price_;_the_net_price_;_the_uf_price_".
 */
function edit_order_gross_price($order_id, $product_id, $gross_price) {
    // Check parameters
    if (!is_numeric($order_id)) {
        throw new Exception("`order_id` must be integer!");      			
    }
    if (!is_numeric($product_id)) {
        throw new Exception("`product_id` must be integer!");      			
    }
    if (!is_numeric($gross_price)) {
        throw new Exception("`gross_price` must be numeric!");      			
    }
    // Insert oder items to aixada_order_to_shop table if is needed.            
    prepare_order_to_shop($order_id);
    
    // Get net price            
    $row = get_row_query("
        select 
            round({$gross_price} * 
                (1 + iva.percent/100), 2) net_price,
            round({$gross_price} * 
                (1 + iva.percent/100) * 
                (1 + rev.rev_tax_percent/100), 2) uf_price
        from  aixada_product p
        join (aixada_rev_tax_type rev, aixada_iva_type iva)
        on    rev.id = p.rev_tax_type_id and iva.id = p.iva_percent_id
        where p.id = {$product_id}
    ");
    $_uf_price = $row['uf_price'];
    // Ok, now update net price!!
    $ok = DBWrap::get_instance()->do_stored_query("
        update
            aixada_order_to_shop os
        set
            os.unit_price_stamp = {$_uf_price}
        where
            os.product_id = {$product_id}
            and os.order_id = {$order_id}
    ");
    if (!$ok){
        throw new Exception(
            "An error occured during saving the new product gross price!!");      			
    }
    return 'OK;'.$gross_price.';'.$row['net_price'].';'.$_uf_price;
}

/**
 * 
 * Finalizes an order which means, that an order is send to the provider (email, printed out, fetched by provider directly).
 * No more modifications are possible and the ordered items receive an order_id
 * @param int $provider_id
 * @param date $date_for_order
 */
function finalize_order($provider_id, $date_for_order)
{
	global $Text;  	
	$config_vars = configuration_vars::get_instance(); 
	$msg = ''; 
	
	
	//check here if an order_id already exists for this date and provider. 
	
	
	
	if ($config_vars->internet_connection && $config_vars->email_orders){
		
        $provider_name = get_list_query(array(
            'SELECT name FROM aixada_provider WHERE id = :1q',
            $provider_id));
		$rm = new report_manager();
		
        $message = '<h2>'.$config_vars->coop_name."</h2>\n";
        $email_order_format = $config_vars->email_order_format;
        if ($email_order_format == 1 || $email_order_format == 3){ 
            $message .= "<h2>".$Text['summarized_orders'].' '.$Text['for'].' "'.
                    $provider_name.'" '.$Text['for'].' '.
                    $date_for_order."</h2>\n";
            $message .= $rm->write_summarized_orders_html($provider_id, $date_for_order);
            $message .= "<p><br/></p>\n";
        }
        if ($email_order_format == 2 || $email_order_format == 3){
            $message .= "<h2>".$Text['detailed_orders'].' '.$Text['for'].' "'.
                    $provider_name.'" '.$Text['for'].' '.
                    $date_for_order."</h2>\n";
            $message .= $rm->extended_orders_for_provider_and_dateHTML($provider_id, $date_for_order);
            $message .= "<p><br/></p>\n";
        }
		
		$db = DBWrap::get_instance();
		$db->free_next_results();
		$strSQL = 'SELECT name, email FROM aixada_provider WHERE email is not null and id = :1q';
    	$rs = $db->Execute($strSQL, $provider_id);
		if($rs->num_rows == 0){
    		throw new Exception("The provider does not have an email.");
		}
    	
		while ($row = $rs->fetch_assoc()) {
      		$toEmail = $row['email'];
            $providerName = $row['name'];
    	}
    	
    	$db->free_next_results();
    	
		$rs = do_stored_query('get_responsible_uf', $provider_id);
    	
    	$responsible_ufs = get_list_rs($rs, 'email');
    	
		$subject = $Text['order'].' '.$Text['for'].' "'.$providerName.'" '.
            $Text['for'].' '.$date_for_order;		
        if (send_mail($toEmail, $subject, $message, array(
                        'reply_to'=>$responsible_ufs,
                        //also send order to responsible uf as cc
                        'cc'=>$responsible_ufs
                    ))) {
			$msg = $Text['msg_order_emailed'];			
		} else {
			$msg = '<span style="color:red">'.$Text['msg_err_emailed'].'</span>';
		}
		
		
	}
	
	
	
	
	if ($rs = do_stored_query('finalize_order', $provider_id, $date_for_order)){
		while ($row = $rs->fetch_assoc()) {
      		$order_id = $row['id'];
    	}
        $msg .= $msg!=='' ? '<br>' : '';
		$msg .= $Text['ostat_desc_fin_send'] . $order_id;
	} else {
		throw new Exception ($Text['msg_err_finalize']);
	}
	
	return $msg; 
	
}



?>