<?php
// Define decimals to use in price_stamp
$price_stamp_decimals = 6;


require_once(__ROOT__ . 'php/inc/database.php');
require_once(__ROOT__ . 'local_config/config.php');
require_once(__ROOT__ . 'php/utilities/general.php');
require_once(__ROOT__ . 'local_config/lang/' . get_session_language() . '.php');
require_once(__ROOT__ . 'php/lib/account_operations.php');
require_once(__ROOT__ . 'php/lib/report_orders.php');

/**
 * 
 * recalculates the individual order quanties when adjusting the total delivered quantity. 
 * @param unknown_type $order_id
 * @param unknown_type $product_id
 * @param unknown_type $new_total_quantity
 */
function edit_total_order_quantities($order_id, $product_id, $new_total_quantity){
	prepare_order_to_shop($order_id); // and check $order_id parameter
	$rs = do_stored_query('get_order_item_detail', $order_id, 0,0, 0,$product_id );
	
	$uf_qu = array();
	
	$total_quantity = 0; 
	//for each uf 
	while ($row = $rs->fetch_assoc()) {
		$uf_qu[$row['uf_id']] =  $row['quantity'];
	 	$total_quantity = $total_quantity + $row['quantity'];	 
	}
    $db = DBWrap::get_instance();
    $db->free_next_results();
    if ($total_quantity == 0) {
        if ($new_total_quantity == 0) { // Set to 0, but is already 0
            $total_quantity = 1;
        } else {
            $rs = $db->Execute(
                "select uf_id
                from aixada_order_item oi
                where oi.order_id = {$order_id}
                group by uf_id;"
            );
            $total_quantity = 0;
            while ($row = $rs->fetch_assoc()) {
                $uf_qu[$row['uf_id']] = 1;
                $total_quantity += 1;	 
            }
            $db->free_next_results();
        }
    }
    $uf_new_qu = array();
    $new_total = 0;
    foreach ($uf_qu as $uf_id => $quantity) {
        $uf_new_qu[$uf_id] = round(($quantity / $total_quantity) * $new_total_quantity, 3);
        $new_total += $uf_new_qu[$uf_id];
    }
    $to_round = $new_total_quantity - $new_total;
    if (abs($to_round) >= 0.001) {
        $c001 = $to_round < 0 ? -0.001 : 0.001;
        foreach ($uf_qu as $uf_id => $quantity) {
            $uf_new_qu[$uf_id] += $c001;
            $new_total += $c001;
            if ($new_total_quantity == round($new_total, 3)) {
                break;
            }
        }
    }
	
	//calc and save adjusted quantities for each uf
	$xml = '<total>'.$total_quantity.'</total>';
	$xml .= '<rows>';
	$new_total_quantity = round($new_total_quantity, 3);
	foreach ($uf_new_qu as $uf_id => $new_quantity) {
	    edit_order_quantity($order_id, $product_id, $uf_id, $new_quantity);
	    $xml .= "<row><uf_id>${uf_id}</uf_id><quantity>${new_quantity}</quantity></row>";
	}
	DBWrap::get_instance()->free_next_results();
	
	printXML($xml . '</rows>');
}


/**
 * Prepare the order to review: Insert order items into aixada_order_to_shop 
 *      if is needed.
 * @param integer $order_id
 */        
function prepare_order_to_shop($order_id) {
	// Check parameters
	if (!is_numeric($order_id)) {
        throw new Exception("`order_id` must be integer!");
    }
	$is_edited = get_row_query(
		"select order_id from aixada_order_to_shop
		where order_id = {$order_id}
		limit 1;"
	);
	if (!$is_edited) {
		global $price_stamp_decimals;
		$sql = "
			insert into aixada_order_to_shop (
				order_item_id, uf_id, order_id,
				unit_price_stamp,
				iva_percent, rev_tax_percent,
				product_id, quantity
			)
			select
				oi.id, oi.uf_id, oi.order_id,
				-- get price with 2 decimal and calculate final price with 6.
				round(
					round(oi.unit_price_stamp / 
						(1 + iva.percent/100) / 
						(1 + rev.rev_tax_percent/100), 2) * 
					(1 + iva.percent/100) * 
					(1 + rev.rev_tax_percent/100), {$price_stamp_decimals}),
				iva.percent, rev.rev_tax_percent,
				oi.product_id, oi.quantity
			from
				aixada_order_item oi
        	left join (
				aixada_product p,
				aixada_rev_tax_type rev,
				aixada_iva_type iva
			)
			on
				p.id = oi.product_id and
				rev.id = p.rev_tax_type_id and
				iva.id = p.iva_percent_id
			where
				oi.order_id = {$order_id};";
		$ok = DBWrap::get_instance()->Execute($sql);
		if (!$ok) {
			throw new Exception(
					"An error occurred during preparing aixada_order_to_shop!!");
		}
	}
}


/**
 * Reset order to review again after distribution
 * @param integer $order_id
 */
function reset_order_to_shop($order_id, $clear) {
    if (!is_numeric($order_id)) {
        throw new Exception("`order_id` must be integer!");
    }
    $db = DBWrap::get_instance();

    // Start transaction
    $db->start_transaction();
    
    // Delete shop items
    $sql = "delete from aixada_shop_item
            where order_item_id in (
                select id from aixada_order_item
                where order_id = {$order_id});";
    $ok = $db->Execute($sql);
    if (!$ok) {
        $db->rollback();
        throw new Exception("An error occurred during reset_order_to_shop-1!");
    }
    // Delete empty carts
    $sql = "delete from aixada_cart
            where id not in(
                select cart_id from aixada_shop_item);";
    $ok = $db->Execute($sql);
    if (!$ok) {
        $db->rollback();
        throw new Exception("An error occurred during reset_order_to_shop-2!");
    }
    // Reset the shop_date and revision status for this order
    $sql = "update aixada_order
            set 
                date_for_shop = null,
                revision_status = 1
            where id = {$order_id};";
    $ok = $db->Execute($sql);
    if (!$ok) {
        $db->rollback();
        throw new Exception("An error occurred during reset_order_to_shop-3!");
    }
    // Delete tmp revison items
    if ($clear) {
        $sql = "delete from aixada_order_to_shop where order_id = {$order_id};";
        $ok = $db->Execute($sql);
        if (!$ok) {
            $db->rollback();
            throw new Exception("An error occurred during reset_order_to_shop-4!");
        }
    }
    
    // End transaction
    $db->commit();
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
    if (!is_numeric($product_id)) {
        throw new Exception(i18n('product_name') . ': ' . i18n('msg_err_only_num'));      			
    }
    if (!is_numeric($uf_id)) {
        throw new Exception(i18n('uf_long') . ': ' . i18n('msg_err_only_num'));     			
    }
    if (!is_numeric($quantity)) {
        throw new Exception(i18n('quantity') . ': ' . i18n('msg_err_only_num'));      			
    }
    // Check if exist
    prepare_order_to_shop($order_id); // and check $order_id parameter
    $item = get_row_query(
        "select uf_id from aixada_order_to_shop os
        where os.product_id = {$product_id}
            and os.order_id = {$order_id}
            and os.uf_id = {$uf_id};");
    $quantity = round($quantity, 3);

    $db = DBWrap::get_instance();
    $db->start_transaction();
    if ($item) {
        // Update quantity
        $ok = do_stored_query('modify_order_item_detail', 
                                $order_id, $product_id, $uf_id, $quantity);
    } else {
        // CREATE aixada_order_item and aixada_order_to_shop
        // 0. Insert oder items to aixada_order_to_shop table if is needed.            
        // 1. Copy any aixada_order_item on order and product with 0 quantity
        $other_item = get_row_query(
            "select 
                date_for_order, 
                unit_price_stamp
                -- TODO (phase 2): , iva_percent, rev_tax_percent
            from aixada_order_item oi
            where oi.product_id = {$product_id} and oi.order_id = {$order_id}
            LIMIT 1;"
        );
        $create_item = null;
        if (!$other_item) { // Product is not in the order, so add it to the order.
            $create_item = edit_order_quantity_addProduct($order_id, $product_id);
            $other_item = $create_item;
        }
        $ok = $db->Execute(
            "INSERT INTO aixada_order_item (
                uf_id, order_id,
                date_for_order,
                product_id,
                unit_price_stamp, quantity 
                -- TODO (phase 2): , iva_percent, rev_tax_percent
            )
            VALUES (
                {$uf_id}, {$order_id},
                '{$other_item['date_for_order']}',
                {$product_id},
                {$other_item['unit_price_stamp']}, 0
            )"
        );
        if (!$ok) {
            $db->rollback();
            throw new Exception("No order for this product!!");
        }
        $new_order_item_id = $db->last_insert_id();
        if (!$new_order_item_id) {
            $db->rollback();
            throw new Exception("No order id for this product!!");
        }
        // 2. Insert new aixada_order_to_shop with the quantity
        if ($create_item) {
            // Create a item reviewed using product price
            $ok = $db->Execute(
                "INSERT INTO aixada_order_to_shop (
                    order_item_id, uf_id, order_id, product_id,
                    unit_price_stamp, iva_percent, rev_tax_percent, 
                    quantity, arrived, revised
                )
                SELECT
                    id, uf_id, order_id, product_id,
                    {$create_item['unit_price_stamp']},
                    {$create_item['iva_percent']},
                    {$create_item['rev_tax_percent']},
                    {$quantity}, 1, 1
                FROM aixada_order_item
                WHERE id={$new_order_item_id};"
            );
        } else {
            // Copy other item reviewed from the same product:
            // - The price under review may be different from the order price
            $ok = $db->Execute(
                "INSERT INTO aixada_order_to_shop (
                    order_item_id, uf_id, order_id, product_id,
                    unit_price_stamp, iva_percent, rev_tax_percent, 
                    quantity, arrived, revised
                )
                SELECT
                    {$new_order_item_id}, {$uf_id}, order_id, product_id,
                    unit_price_stamp, iva_percent, rev_tax_percent,
                    {$quantity}, 1, 1
                FROM aixada_order_to_shop
                WHERE order_id={$order_id} and product_id = {$product_id}
                LIMIT 1;"
            );
        }
    }
    if (!$ok) {
        $db->rollback();
        throw new Exception(
                   "An error occured during saving the new product quantity!!");
    }
    $db->commit();
    return $quantity;
}

function edit_order_quantity_addProduct($order_id, $product_id)
{
    $db = DBWrap::get_instance();
    $orderRow = get_row_query(
        "select provider_id, date_for_order
        from aixada_order
        where id = {$order_id};"
    );
    if (!$orderRow) {
        $db->rollback();
        throw new Exception("Order #{$order_id} does not exist!!");
    }
    $provider_id = $orderRow['provider_id'];
    $date_for_order = $orderRow['date_for_order'];
    $productRow = get_row_query(
        "select
            iva.percent iva_percent,
            rev.rev_tax_percent,
            round(p.unit_price * (1 + iva.percent/100)
                * (1 + rev.rev_tax_percent/100), 2) unit_price_stamp
        from aixada_product p
        join (aixada_rev_tax_type rev, aixada_iva_type iva)
        on rev.id = p.rev_tax_type_id and iva.id = p.iva_percent_id
        where active = 1 and  p.id = {$product_id} 
            and provider_id={$provider_id};");
    if (!$productRow) {
        $db->rollback();
        throw new Exception(
            "Product for this provider does not exist or is not active!!"
        );
    }
    $iva_percent = $productRow['iva_percent'];
    $rev_tax_percent = $productRow['rev_tax_percent'];
    $unit_price_stamp = $productRow['unit_price_stamp'];
    $orderDateRow = get_row_query(
        "select closing_date
        from aixada_product_orderable_for_date
        where product_id = {$product_id}
            and date_for_order = '{$date_for_order}'
        LIMIT 1;");
    if (!$orderDateRow) {
        $orderDateRow = get_row_query(
            "select closing_date
            from aixada_product_orderable_for_date od
            join aixada_product p on od.product_id = p.id
            where p.provider_id = {$provider_id}
                and date_for_order = '{$date_for_order}'
            LIMIT 1;");
        if (!$orderDateRow) {
            $db->rollback();
            throw new Exception(
                "No products of provider #{$provider_id} is not active for this date!!"
            );
        }
        $closing_date = $orderDateRow['closing_date'];
        $db = DBWrap::get_instance();
        $ok = $db->Execute(
            "insert into aixada_product_orderable_for_date (
                product_id, date_for_order, closing_date
            ) values (
                {$product_id}, '{$date_for_order}', '{$closing_date}');"
        );
        if (!$ok) {
            $db->rollback();
            throw new Exception(
                "Error when product is activated for this date!!"
            );
        }
    }
    return array(
        'iva_percent' => $iva_percent ,
        'rev_tax_percent' => $rev_tax_percent,
        'unit_price_stamp' => $unit_price_stamp,
        'date_for_order' => $date_for_order
    );
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
    if (!is_numeric($product_id)) {
        throw new Exception("`product_id` must be integer!");      			
    }
    if (!is_numeric($gross_price)) {
        throw new Exception("`gross_price` must be numeric!");      			
    }
    // Insert oder items to aixada_order_to_shop table if is needed.            
    prepare_order_to_shop($order_id); // and check $order_id parameter
    
    // Get net price            
    
	global $price_stamp_decimals;
    $gross_price = round($gross_price, 2);
	$row = get_row_query("
        SELECT 
            round({$gross_price} * 
                (1 + iva_percent/100), {$price_stamp_decimals}) net_price,
            round({$gross_price} * 
                (1 + iva_percent/100) * 
                (1 + rev_tax_percent/100), {$price_stamp_decimals}) uf_price
        FROM aixada_order_to_shop
        WHERE order_id={$order_id} and product_id = {$product_id}
        LIMIT 1;"
    );
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
function finalize_order($provider_id, $date_for_order, $revision_status = 1)
{
    global $Text;  	

    //check here if an order_id already exists for this date and provider. 
    if (!get_row_query(
        "select oi.id
        from 
            aixada_order_item oi,
            aixada_product p
        where
            oi.date_for_order = '{$date_for_order}'
            and oi.order_id is null -- so, order is not closed
            and oi.product_id = p.id
            and p.provider_id = {$provider_id};")
    ) { // No open orders for this date
        throw new Exception ($Text['ostat_closed']);
    }

    // finalize
    $msg = ''; 
    if ($rs = do_stored_query('finalize_order', $provider_id, $date_for_order)){
        while ($row = $rs->fetch_assoc()) {
            $order_id = $row['id'];
        }
        $db = DBWrap::get_instance();
        $db->free_next_results();
        $msg .= '<br>' . $Text['ostat_desc_fin_send'] . $order_id;
        if ($revision_status == 3 || $revision_status == 4) {
            $ok = $db->Execute(
                "update aixada_order
                set revision_status={$revision_status} where id={$order_id}"
            );
	    	$db->free_next_results();
            if ($ok) {
                switch ($revision_status) {
                    case 3:
                        $msg .= '<br>' . $Text['order']  . ' ' . $Text['ostat_postponed'];
                        break;
                    case 4:
                        $msg .= '<br>' . $Text['order']  . ' ' . $Text['ostat_canceled'];
                        break;
                }
            } else {
                $msg = '<br><span style="color:red">An error occurred changing order status!</span>';
            }
        }
    } else {
        throw new Exception ($Text['msg_err_finalize']);
    }

    if ($revision_status == '1') {
        $msg .= '<br>' . send_order($order_id);
    }

    return $msg ? substr($msg, 4) : '';
}
    
function send_order($order_id) 
{
    global $Text;
    
    // Send order by email to provider
    if (get_config('internet_connection') && get_config('email_orders')) {
        
        $row = get_row_query(
            "SELECT provider_id, date_for_order
            FROM aixada_order WHERE id = {$order_id}"
        );
        if (!$row) {
            return '';
        }
        $provider_id = $row['provider_id'];
        $date_for_order = $row['date_for_order'];
        
        $sendOp = report_order::get_sendOptions($provider_id);
        if (!$sendOp || $sendOp['order_send_format'] === 'none') {
            return '';
        }
        $toEmail = $sendOp['email'];
        $providerName = $sendOp['name'];
        if (is_null($toEmail)) {
            return '';
        }
        
        $subject = i18n('orderToFor', array(
            'id' => '#' . $order_id,
            'provider' => $providerName,
            'date' => $date_for_order
        ));
        $message = '<h2>' . get_config('coop_name'). "</h2>\n";
        $message .= "<h2>{$subject}</h2>\n";
        $message .= report_order::getHtml_order($order_id);

        $rs = do_stored_query('get_responsible_uf', $provider_id);
        $responsible_ufs = get_list_rs($rs, 'email');
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
    return $msg; 
}

/**
 * Check no validated carts for this order. Throw error if exist a validated cart
 * @param integer $order_id
 */
function chk_no_validate_order($order_id) {
    $rs = do_stored_query('get_validated_status',$order_id, 0);
    $row = $rs->fetch_array();
    $db = DBWrap::get_instance();
    $db->free_next_results();
    if ($row) {
        throw new Exception(i18n('msg_err_already_val'));
    }
}

/**
 * Distribute and directly validate an order
 * @param integer $order_id
 */
function directly_validate_order($order_id, $record_provider_invoice) {
    chk_no_validate_order($order_id);
    
    // Ok, do it
    prepare_order_to_shop(get_param_int('order_id'));
    $db = DBWrap::get_instance();
    try {
        
        // Check date for shop non empty carts.
        $row_or = get_row_query(
            "select provider_id, date_for_order 
            from aixada_order where id = {$order_id};");
        $date_for_shop = $row_or['date_for_order'];
        $provider_id = $row_or['provider_id'];
        $carts_for_date = get_row_query(
            "select c.id
            from aixada_cart c 
            inner join aixada_shop_item si on si.cart_id = c.id
            where c.date_for_shop = '{$date_for_shop}' and c.ts_validated = 0
            limit 1;"
        );
        if ($carts_for_date) {
            throw new Exception(i18n(
                'msg_err_disVal_nonEmpyCatrs',
                array('date_for_shop'=>$date_for_shop)
            ));
        }
        
        // Set date for shop as date for order
        $db->start_transaction();
        $operator_id = get_session_user_id();
        //For each uf
        $rs = $db->Execute("select distinct os.uf_id
                from aixada_order_to_shop os
                where os.arrived = 1 and os.order_id = {$order_id};");
        while ($row = $rs->fetch_assoc()) {
            $uf_id = $row['uf_id'];
            // Create cart
            $cart_for_date = get_row_query(
                "select c.id
                from aixada_cart c
                left join aixada_shop_item si on si.cart_id = c.id
                where 
                    si.cart_id is null 
                    and c.uf_id = {$uf_id}
                    and c.date_for_shop = '{$date_for_shop}'
                    and c.ts_validated = 0
                limit 1;"
            );
            if ($cart_for_date) {
                $cart_id = $cart_for_date['id'];
            } else {
                $db->Execute(
                    "insert into aixada_cart (
                        uf_id, date_for_shop
                    )
                    values (
                        {$uf_id}, '{$date_for_shop}'
                    );"
                );
                $cart_id = $db->last_insert_id();
            }
            // Copy revised items into aixada_shop_item with to corresponding cart
            $db->Execute(
                "insert into aixada_shop_item (
                    cart_id, order_item_id, unit_price_stamp, product_id,
                    quantity, iva_percent, rev_tax_percent)
                select
                    {$cart_id}, 
                    os.order_item_id,
                    os.unit_price_stamp,
                    os.product_id,
                    os.quantity, 
                    iva_percent,
                    rev_tax_percent
                from
                    aixada_order_to_shop os
                where 
                    os.order_id = {$order_id}
                    and os.uf_id = {$uf_id}
                    and os.arrived = 1;"
            );
            
            // If quantities have changed, revision status is 5; otherwise it is 2.
            $qu_diff_row = get_row_query(
                "select sum(abs(oi.quantity - (
                    select os.quantity * os.arrived
                    from aixada_order_to_shop os
                    where os.order_id = {$order_id}
                          and os.order_item_id = oi.id) ) ) qu_diff
                from aixada_order_item oi
                where oi.order_id = {$order_id};"
            );
            $db->Execute(
                "update aixada_order
                set
                    date_for_shop = '{$date_for_shop}',
                    revision_status = ".($qu_diff_row['qu_diff'] === 0 ? 2 : 5)."
                where id = {$order_id};"
            );
            // Validate carts
            do_stored_query('validate_shop_cart', $cart_id, $operator_id, 
                    "order#{$order_id} {$date_for_shop} cart#", 0);
        }
        if ($record_provider_invoice) {
            // Add provider invoice
            $ao = new account_operations();
            $ao->use_transaction = false;
            if ($ao->uses_providers()) {
                $prv_tot_row = get_row_query(
                    "select sum( 
                                CAST(
                                    quantity * round(
                                        unit_price_stamp /
                                            (1 + rev_tax_percent/100),
                                        2
                                    )
                                    as decimal(10,2)
                                )
                            ) prv_tot
                    from (
                        select 
                            sum(os.quantity) quantity, unit_price_stamp, rev_tax_percent, iva_percent
                        from aixada_order_to_shop os
                        join aixada_product p
                        on os.product_id = p.id
                        where
                            p.orderable_type_id >= 2 /* only not stock */
                            and os.order_id = {$order_id}
                            and os.arrived = 1
                        group by
                            unit_price_stamp, rev_tax_percent, iva_percent) r;"
                );
                $prv_tot = $prv_tot_row['prv_tot'];
                if ($prv_tot > 0) {
                    $ao->add_operation(
                        'invoice_pr',
                        array('provider_from_id' => $provider_id + 2000),
                        $prv_tot, 
                        "validation order#{$order_id} {$date_for_shop}"
                    );
                }
            }
        }
        $db->commit();
    } catch (Exception $e) {
        $db->rollback();
        throw new Exception($e->getMessage());
    } 
    return i18n('btn_disValitate_done', array('order_id'=>$order_id));
}


/**
 * 
 * custom interface for order listing which converts most common query params into 
 * calls to stored procedures. 
 * @param str $time_period today | all | prevMonth | etc. 
 */
function get_orders_in_range($time_period='ordersForToday', $uf_id=0, $from_date=0, $to_date=0, $steps=1, $range="month", $limit='')
{
	
	//TODO server - client difference in time/date?!
	$today = date('Y-m-d', strtotime("Today"));
	$tomorrow = date('Y-m-d', strtotime("Tomorrow"));
	$next_week =  date('Y-m-d', strtotime('Tomorrow + 6 days'));
	$prev_month = date('Y-m-d', strtotime('Today - 1 month'));
	$prev_2month = date('Y-m-d', strtotime('Today - 2 month'));
	$prev_year = date('Y-m-d', strtotime('Today - 1 year'));
	$very_distant_future = '9999-12-30';
	$very_distant_past	= '1980-01-01';
	$pre_order	= '1234-01-23';
	
	
	//get results based on time periods and then go backward/foward a certain amount of time 
	$steps_from_date 	 = date('Y-m-d', strtotime("Today -". ($steps--) . " " .$range));
	$steps_to_date	 = date('Y-m-d', strtotime("Today -". ($steps) . " " .$range));	
	

	//stored procedure 'get_orders_listing(fromDate, toDate, uf_id, filter_revision_status_id, limit_str)'
	
	switch ($time_period) {
		// all orders where date_for_order = today
		case 'ordersForToday':
			printXML(stored_query_XML_fields('get_orders_listing', $today, $today, $uf_id,0,$limit));
			break;
			
		// all orders where date_for_order = today
		case 'ordersForTodayLimbo':
			//printXML(stored_query_XML_fields('get_orders_listing', $today, $today, $uf_id,30,$limit));
			break;
		
		//all orders 
		case 'all':
			printXML(stored_query_XML_fields('get_orders_listing', $very_distant_past, $very_distant_future, $uf_id,0,$limit));
			break;

		case 'pastMonths2Future':
			printXML(stored_query_XML_fields('get_orders_listing', $prev_2month, $very_distant_future, $uf_id,0,$limit));
			break;
			
		//last month
		case 'pastMonth':
			printXML(stored_query_XML_fields('get_orders_listing', $prev_month, $tomorrow, $uf_id,0,$limit));
			break;
			
		//last year
		case 'pastYear':
			printXML(stored_query_XML_fields('get_orders_listing', $prev_year, $tomorrow, $uf_id,0,$limit));
			break;
		
		//all orders that have been send off, but have no shop date assigned
		case 'limboOrders':
			printXML(stored_query_XML_fields('get_orders_listing', $prev_year, $very_distant_future, $uf_id, 3,$limit));
			break;
		
		case 'preOrders':
			printXML(stored_query_XML_fields('get_orders_listing', $pre_order, $pre_order, $uf_id, 0,$limit));
			break;
			
		case 'nextWeek':
			printXML(stored_query_XML_fields('get_orders_listing', $tomorrow, $next_week, $uf_id, 0,$limit));
			break;
					
		case 'futureOrders':
			printXML(stored_query_XML_fields('get_orders_listing', $today, $very_distant_future, $uf_id,0,$limit));
			break;
			
		case 'steps':
			printXML(stored_query_XML_fields('get_orders_listing', $steps_from_date, $steps_to_date, $uf_id,0,$limit));
			break;
			
		case 'exact':
			printXML(stored_query_XML_fields('get_orders_listing', $from_date, $to_date, $uf_id,0,$limit));
			break;
		
		
		default:
			throw new Exception("get_orders_in_range: param={$time_period} not supported");  
			break;
	}

	
}

/**
 * Retrieves products with prices that have been ordered.
 * @param integer|null $order_id Order id (if is null uses $date)
 * @param integer $provider_id
 * @param string|null $date Date for order (used only when $order_id is null)
 * @return mysqli_result
 */
function get_ordered_products_with_prices(
        $order_id, 
        $provider_id,
        $date,
		$page = '-'
) {
    global $Text;
	if ($page === 'review') {
		prepare_order_to_shop($order_id);
	}
    $db = DBWrap::get_instance();
    $sql = "
        select distinct
            p.id, p.name,
            if(p.orderable_type_id=3, 
                '" . $db->escape_string($Text['order_notes']) . "',
                um.unit) unit,
            ifnull(ots.unit_price_stamp, oi.unit_price_stamp) uf_price,
            ifnull(ots.rev_tax_percent, rev.rev_tax_percent) rev_tax_percent,
            ifnull(ots.iva_percent, iva.percent) iva_percent            
        from
            aixada_order_item oi
        left join 
            aixada_order_to_shop ots
        on  oi.id = ots.order_item_id
        join (
            aixada_product p,
            aixada_rev_tax_type rev, 
            aixada_iva_type iva,
            aixada_unit_measure um
        ) on 
            oi.product_id = p.id
            and rev.id = p.rev_tax_type_id
            and iva.id = p.iva_percent_id
            and um.id =  p.unit_measure_order_id";
    if ($order_id) {
        $sql .= " where oi.order_id = {$order_id}";
    } else if($date && $provider_id) {
        $sql .= " where
                    oi.date_for_order = '{$date}'
                    and p.provider_id = {$provider_id}";
    } else {
        $sql .= " where oi.order_id = -1"; // no rows
    }
    $sql2 = "
        SELECT id, name, unit,
            max(r.uf_price) uf_price,
            max(r.rev_tax_percent) rev_tax_percent,
            max(r.iva_percent) iva_percent,
            round( max(r.uf_price) / 
                (1 + max(r.rev_tax_percent)/100) /
                (1 + max(r.iva_percent)/100), 2 ) gross_price,
            round( max(r.uf_price) / 
                (1 + max(r.rev_tax_percent)/100), 2 ) net_price
        FROM({$sql}) r
        group by id, name, unit
        order by name;";
    return $db->Execute($sql2);
}

function get_all_products_to_order($order_id)
{
    $rowOrder = get_row_query(
        "select provider_id 
        from aixada_order
        where id = {$order_id};"
    );
    if (!$rowOrder) {
        throw new Exception("Order not exists!");
    }
    $provider_id = $rowOrder['provider_id'];
    $ge_orderable_type_id = get_config('orders_allow_stock', false) ? 1 : 2;
    $db = DBWrap::get_instance();
    $sql = "
        select p.id,
        concat(p.name, if((
                select count(*)
                from aixada_order_item oi 
                where oi.order_id = {$order_id}
                and p.id = oi.product_id
                ) > 0
                , '', ' (+)'
            )) product_name
        from aixada_product p
        where p.active = 1 
        and p.orderable_type_id >= {$ge_orderable_type_id}
        and p.orderable_type_id <> 3
        and p.provider_id = {$provider_id}
        group by p.id, p.name
        order by p.name;";
    return $db->Execute($sql);
}

function get_all_ufs_to_order($order_id)
{
    $db = DBWrap::get_instance();
    $sql = "
        select uf.id, concat(uf.name, ' #', uf.id, if((
                select count(*)
                from aixada_order_item oi 
                where oi.order_id = {$order_id}
                and uf.id = oi.uf_id
                ) > 0
                , '', ' (+)'
            )) uf_name
        from aixada_uf uf
        where uf.active = 1
        order by uf.name;";
    return $db->Execute($sql);
}
?>
