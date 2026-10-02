<?php
/**
 * El proyecto en `/php/reports/` propone incorporar los deserrollos propios de
 * la coop *La Cistella* en *Aixada* y así dejar de mantenerlo como un rama
 * independiente.
 *
 * #### Commit-2:
 *
 * Incorporar informes para realizar el reparto de pedidos de *La Cistella* a *Aixada*.
 *
 * Los informes vía url:
 *
 * - `/php/reports/order_report_prv_fam.php[?date=aaaa-mm-dd][&detail=S|N][&order_id=list_of_ids]`  
 *   - Proveedores, productos pedidos y en cada producto la cantidad que ha
 *     pedido cada UF.  
 *   - Este informe es ideal para hacer el reparto e ir apuntando las unidades o
 *     pesos entregados realmente a cada UF.
 *   - Sigue el orden de UFs indicado en `$order_review_uf_sequence` de
 *     `config.php`, con lo cual garantiza que los id de las UF estén el mismo
 *     orden que en la pantalla de revisión de pedidos.
 *   - Si ya se ha realizado la revisión de los pedidos las cantidades
 *     mostradas son las entregadas realmente. reparto 
 *
 * - `/php/reports/order_report_prv_fam_diff.php[?date=aaaa-mm-dd][&detail=S|N][&order_id=list_of_ids]`
 *   - Es similar al anterior pero indicando las catidades pedidas y la repartidas.
 * 
 * - `/php/reports/order_report_fam_prv.php[?date=aaaa-mm-dd][&detail=S|N][&order_id=list_of_ids]`
 *   - Ufs, proveedores y detalle de cada producto de cantidad e importe.
 *
 * - `/php/reports/dir_report_pro_fam.php`
 *   - El directorio permite tener a mano en papel telefonos y correos de
 *     los proveedores y de los mienbros de las UFs.
 */

/**
 * Get title for order repots
 */
function report_get_title($text, $date_for_order, $order_id, $detail) {
    return $text . ' '. 
        ( $order_id ? "orders = " . implode(',', $order_id) :
            ( $date_for_order ? $date_for_order : '(no_date)')
        ) . ( $detail ? '' : ' summary' );
}

/**
 * Obtener $for_date via GET de parametros date o for_date o via SQL la fecha
 * futura de pedidos más pòxima
 */
function report_get_for_date() {
    $for_date = get_param_date('date');
    $for_date = $for_date ? $for_date : get_param_date('for_date');
    // Se usa el parametro ?date=aaaa-mm-dd o ?for_date=aaaa-mm-dd
    if ( !$for_date ) {
    // Se busca en los pedidos la proxima fecha de reparto más cercana
        $db = DBWrap::get_instance();
        $rs = $db->Execute(
            'SELECT min(date_for_order) for_date' .
            ' FROM aixada_product_orderable_for_date'.
            ' where date_for_order >= date(sysdate())'
        );
        $row = $rs->fetch_array();
        if ($row) {
            $for_date = $row['for_date'];
        } 
        if ($for_date == null) {
            $rs = $db->Execute(
                'SELECT max(date_for_order) for_date' .
                ' FROM aixada_product_orderable_for_date');
            $row = $rs->fetch_array();
            if ($row) {
                $for_date = $row['for_date'];
            }
        }
    }
    return $for_date;
}

 
/**
 * Devuelve el texto SQL para recuperar pedidos según el filtro.
 */
function get_SQL_orders($date_for_order, $provider_id, $order_id) {
    $sql = "select 	
            oi.date_for_order, oi.product_id,
            oi.quantity orderQuantity,
            if(os.quantity is not null, os.quantity*os.arrived, oi.quantity) quantity,
            ifnull(os.unit_price_stamp, oi.unit_price_stamp) final_price, 
            if( os.unit_price_stamp is not null,
                round(
                    os.unit_price_stamp / 
                    (1 + os.iva_percent/100) / 
                    (1 + os.rev_tax_percent/100), 2),
                p.unit_price
            ) cost_price,
            oi.order_id,
            o.revision_status,
            uf.id uf_id, uf.name uf_name,
            p.name p_name, p.provider_id, pv.name pv_name,
            um.name um_name
        from aixada_order_item oi
        join (
            aixada_uf uf,
            aixada_product p,
            aixada_unit_measure um,
            aixada_provider pv )
        on 
            oi.uf_id = uf.id and
            oi.product_id = p.id and
            p.unit_measure_order_id = um.id and
            p.provider_id = pv.id
        left join (
            aixada_order o )
        on 
            oi.order_id=o.id
        left join (
            aixada_order_to_shop os )
        on 
            oi.id = os.order_item_id
        where ";
    if ( is_array($order_id) ) {   // order_id
        $sql .= 'oi.order_id in(' . implode(',', $order_id) . ')';
    } elseif ( $order_id ) {   // order_id
        $sql .= "oi.order_id='{$order_id}'";
    } elseif ( $date_for_order ) {
        // date for order
        $sql .= "oi.date_for_order='{$date_for_order}'";
        if ( $provider_id ) {
            $sql .= " and p.provider_id={$provider_id}";
        } else {
            $sql .= " and ( revision_status is null or revision_status in (1,2,5) )";
        }
    } else {
        // no filter, so nothing!
        $sql .= "1=0";
    }
    return "select * from ({$sql}) r order by ";
}

function getSumaryOrders_SQL($date_for_order, $provider_id, $order_id) {
    $sql = "select 	
            oi.date_for_order,
            p.provider_id,
            oi.order_id,
            o.revision_status,
            pv.name pv_name
        from aixada_order_item oi
        join (
            aixada_product p,
            aixada_provider pv )
        on 
            oi.product_id = p.id and
            p.provider_id = pv.id
        left join aixada_order o
            on oi.order_id=o.id
        left join aixada_order_to_shop os
            on oi.id = os.order_item_id
        where ";
    if (is_array($order_id)) {   // order_id
        $sql .= 'oi.order_id in(' . implode(',', $order_id) . ')';
    } elseif ( $order_id ) {   // order_id
        $sql .= "oi.order_id='{$order_id}'";
    } elseif ( $date_for_order ) { 	        // date for orrder
        $sql .= "oi.date_for_order='{$date_for_order}'";
        if ( $provider_id ) {
            $sql .= " and p.provider_id={$provider_id}";
        } else {
            $sql .= " and ( revision_status is null or revision_status in (1,2,5) )";
        }
    } else {								// no filter, so nothing!
        $sql .= "1=0";
    }
    $sql .= 
        " group by
            oi.date_for_order,
            p.provider_id,
            oi.order_id,
            o.revision_status,
            pv.name";
    return "select * from ({$sql}) r order by ";
}

function formatOrderStatus($revision_status) {
    global $Text;
        switch ($revision_status){
            case null:
                $text_key = 'not_yet_sent';
                break;                
            case "1":
                $text_key = 'ostat_yet_received';
                break;
            case "2": 
               $text_key = 'ostat_is_complete';
                break;
            case "3": 
                $text_key = 'ostat_postponed';
                break;
            case "4": 
                $text_key = 'ostat_canceled';
                break;
            case "5": 
                $text_key = 'ostat_changes';
                break;
            default:
                return '<span class="DATA-not_yet_sent">??OrderStatus="'.
                    $revision_status.'"??</span>';
        
    }
    return '<span class="DATA-'.$text_key.'">'.$Text[$text_key].'</span>';
}

function break2Html_end(&$brk, $detail) {
    $html = '';
    if ($brk['2_id_break'] != null) {
        if ($detail) {
            $html .= '</div>';
        }
        $html .= "</div>\n";
        $brk['2_id_break'] = null;
    }
    return $html;
}

function get_sum($db, $date_for_order, $provider_id, $order_id, $whereSQL) {
    $sql = 
        "select
            oi.quantity orderQuantity,
            if(os.quantity is not null, os.quantity*os.arrived, oi.quantity) quantity,
            ifnull(os.unit_price_stamp, oi.unit_price_stamp) final_price, 
            if( os.unit_price_stamp is not null,
                round(
                    os.unit_price_stamp / 
                    (1 + os.iva_percent/100) / 
                    (1 + os.rev_tax_percent/100), 2),
                p.unit_price
            ) cost_price
        from aixada_order_item oi
        join (
            aixada_product p )
        on
            oi.product_id = p.id 
        left join (
            aixada_order_to_shop os )
        on
            oi.id = os.order_item_id
        where {$whereSQL} ";
    if ($order_id === null) {
        $sql .=	" and oi.date_for_order='{$date_for_order}' 
            and oi.order_id is null
            and p.provider_id={$provider_id}";
    } else {
        $sql .=	" and oi.order_id ={$order_id}";
    }
    $sql_sum = "
        select 
            sum(orderQuantity) sum_orderQuantity,
            sum(quantity) sum_quantity,
            sum(round(quantity * cost_price, 2)) sum_cost,
            sum(round(quantity * final_price, 2)) sum_amount 
        from ({$sql}) r;";
    return get_row_query($sql_sum);
}
