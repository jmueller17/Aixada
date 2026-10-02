<?php 
require_once '../inc/header.inc.php';
require_once 'order_report__include.php';

$date_for_order = report_get_for_date();
$detail = ( get_param('detail', 'S') !== 'N' );
$order_id = get_param_array_int('order_id');
$order_id = $order_id ? $order_id : get_param_array_int('id');

// Funciones del report
function write_orders_prod($date_for_order, $provider_id, $order_id, $detail) {
    
    $db = DBWrap::get_instance();
    
    $strSQL = getSumaryOrders_SQL($date_for_order, $provider_id, $order_id);
    $strSQL .= 'pv_name, order_id';
    $rs = $db->Execute($strSQL);

    $report_orderCost = 0;
    $report_cost = 0;
    while ($row = $rs->fetch_array()) {
        $ppcDescompte = 0;
        $row_pv = get_row_query(
            "SELECT notes FROM aixada_provider where id={$row['provider_id']}");
        if (is_null($row_pv['notes'])) {
            $tipus_ordre = 'PRO';            
        } else {
            $tipus_ordre = substr($row_pv['notes'],0,3);
            if (is_numeric(substr($row_pv['notes'],3))) {
                $ppcDescompte = substr($row_pv['notes'],3) + 0;
            } 
        }
        write_order_prod_fam_diff(
            $report_orderCost, $report_cost,
            $row['date_for_order'], 
            $row['provider_id'], 
            $row['order_id'],
            $ppcDescompte,
            $detail
        );
    }
    echo orderHtml_end(
        $report_orderCost, $report_cost, $ppcDescompte, '<b>' . strtoupper(i18n('total')) .'</b>'
    );
}
function write_order_prod_fam_diff(
    &$report_orderCost, &$report_cost,
    $date_for_order, $provider_id, $order_id, 
    $ppcDescompte, $detail,
    $head = true, $foot = true
) {
    
    $db = DBWrap::get_instance();
    
    $coefQu = 100.0/(100.0 - $ppcDescompte);
    $strSQL = get_SQL_orders($date_for_order, $provider_id, $order_id);
    if ( get_config('order_review_uf_sequence', 'desc') == 'asc' ) {
        $strSQL .= 'pv_name, order_id, p_name, uf_id';
    } else {
        $strSQL .= 'pv_name, order_id, p_name, uf_id desc';
    }
    $rs = $db->Execute($strSQL);
    $brk = array(
        'date_for_order' => $date_for_order,
        '1_provider_id' => null,
        '1_order_id_n' => null,
        '1_desc_order' => null,
        '2_id_break' => null
    );
    $total_orderCost = 0;
    $total_cost = 0;
    $aaa_quantity =0;
    $pro_count = 0;
    $html = '';
    while ($row = $rs->fetch_array()) {
        //if ($row['quantity'] == 0) { continue; }
        if ($brk['1_provider_id'] != $row['provider_id'] || 
                    $brk['1_order_id'] != $row['order_id']) {
            // end previus order
            $html .= break2Html_end($brk, $detail);
            $report_orderCost += $total_orderCost;
            $report_cost +=$total_cost ;
            $html .= orderHtml_end($total_orderCost, $total_cost,  $ppcDescompte,
                $brk['1_desc_order']);
            // start order
            $total_orderCost = 0;
            $total_cost = 0;
            $html_h = orderHtml_start($brk, $row);
            if ($head) {
                $html .= $html_h;
            }
        }
        if ($brk['2_id_break'] != $row['product_id']) {
            $html .= break2Html_end($brk, $detail);
            // start uf
            $brk['2_id_break'] = $row['product_id'];
            $sum_quantity_row = get_sum($db, 
                $brk['date_for_order'], 
                $brk['1_provider_id'], $brk['1_order_id'],
                'oi.product_id = '.$brk['2_id_break']);
            $sum_orderQuantity = $sum_quantity_row['sum_orderQuantity'];
            $sum_quantity = $sum_quantity_row['sum_quantity'];
            // Rounded by product
            $total_orderCost += 
                round($row['cost_price']*$sum_orderQuantity*$coefQu, 2);
            $total_cost += 
                round($row['cost_price']*$sum_quantity*$coefQu, 2);
            if ($detail) {
                $html .= '<div class="block">';
            } else {
                $border_det = "border-bottom: dotted 1px #777;";
                if ($pro_count === 0) {
                    $border_det .= "border-top: dotted 1px #777;";
                }
                $html .= '<div class="block" style="'.$border_det.
                    'margin-top: 0.25cm; ">';
            }
            $pro_count++;
            $orderAmLin = number_format($row['cost_price']*$sum_orderQuantity*$coefQu, 2);
            $finalAmLin = number_format($row['cost_price']*$sum_quantity*$coefQu, 2);
            $showOrderQ = ($orderAmLin !== $finalAmLin);
            $html .= '<h3>' . 
                '<div class="cel10">'.$row['p_name'].
                // '<div class="lite" style="float:right">'.$row['um_name'].'</div>'.
                '</div>'.
                '<div class="cel1h aprov">'.
                    number_format($row['cost_price'], 2).'</div>'.
                '<div class="lite cel1h num">'.
                    ($showOrderQ ? clean_zeros($sum_orderQuantity).' =' : '' ).'</div>'.
                '<div class="lite cel1 aprov">'.
                    ($showOrderQ ? $orderAmLin : '' ).'</div>'.
                '<div class="cel2 num">' .
                    ($finalAmLin !== '0.00' ? clean_zeros($sum_quantity).' =' : '(no)' ) . '</div>'.
                '<div class="cel1h aprov">' .
                    ($finalAmLin !== '0.00' ? $finalAmLin : '') . '</div>'.
                "</h3>\n";
            if ($detail) {
                $html .= '<div class="block-body">' . "\n";
            }
        }
        // Rounded by UF
        if ($detail) {
            $html .= '<div>';
            $html .= '<div class="cel1 num">'.clean_zeros($row['quantity']).'</div>';
            $html .= '&nbsp;<div class="cel8">';
            $html .= '#'.$row['uf_id'].'-'.$row['uf_name'];
            $html .= '</div>';
            $html .= "</div>\n";
        }
    }
    $html .= break2Html_end($brk, $detail);
    $report_orderCost += $total_orderCost;
    $report_cost +=$total_cost ;
    $html .= orderHtml_end(
        $total_orderCost, $total_cost, $ppcDescompte,
        $brk['1_desc_order']
    );
    echo $html;
}

function orderHtml_start(&$brk, $row) {
    $brk['1_order_id'] = $row['order_id'];
    $brk['1_provider_id'] = $row['provider_id'];
    $brk['1_desc_order'] = $row['pv_name'].
        ': '.$row['date_for_order'].
        ' #'.($row['order_id']==null ? '??' : $row['order_id']);
    return '<h2>'.$brk['1_desc_order'].
        '['.formatOrderStatus($row['revision_status']).']'.
        '</h2>';
}

function orderHtml_end($total_orderCost, $total_cost, $ppcDescompte, $desc) {
    if ($desc == null) {
        return '';
    }
    return 
        '<div class="foot">'.
            '<div class="cel8">'. $desc . '</div>'.
            '<div class="lite cel4 num">' . i18n('ordered2') .
                ' </div>'.
            '<div class="lite cel2 num">' . number_format($total_orderCost, 2) . '</div>'.
            '<div class="cel2 num">' . i18n('final') . 
                ' </div>'.
            '<div class="cel2 num">' . number_format($total_cost, 2) . '</div>'.
        "</div>\n";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo report_get_title('Prv-Fam-Diff', $date_for_order, $order_id, $detail); ?></title>
    <link rel="stylesheet" type="text/css"               href="css/reports_paper.css"/>
    <link rel="stylesheet" type="text/css"               href="css/reports_layout.css"/>
    <link rel="stylesheet" type="text/css" media="print" href="css/reports-print.css"/>
</head>
<body>
<div class="page-A4p">
<?php write_orders_prod($date_for_order, null, $order_id, $detail); ?>
</div>
</body>
</html>
