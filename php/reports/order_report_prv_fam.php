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
        if ($tipus_ordre === "FAM" &&
                $detail) {
            $html_f = write_order_prod_fam(
                $row['date_for_order'], 
                $row['provider_id'], 
                $row['order_id'],
                $ppcDescompte,
                false, true, false
            );
            write_order_fam_prod(
                $row['date_for_order'], 
                $row['provider_id'], 
                $row['order_id'],
                $ppcDescompte,
                $detail, false, false
            );
            echo $html_f;
        } else {
            write_order_prod_fam(
                $row['date_for_order'], 
                $row['provider_id'], 
                $row['order_id'],
                $ppcDescompte,
                $detail
            );
        }
    }
}

function write_order_prod_fam($date_for_order, $provider_id, $order_id, 
        $ppcDescompte, $detail,
        $head = true, $foot = true) {
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
    $total_cost = 0;
    $total_amount = 0;
    $aaa_quantity =0;
    $pro_count = 0;
    $html = '';
    while ($row = $rs->fetch_array()) {
        if ($row['quantity'] == 0) { continue; }
        if ($brk['1_provider_id'] != $row['provider_id'] || 
                    $brk['1_order_id'] != $row['order_id']) {
            // end previus order
            $html .= break2Html_end($brk, $detail);
            $html .= orderHtml_end($total_cost, $total_amount,  $ppcDescompte,
                $brk['1_desc_order']);
            // start order
            $total_cost = 0;
            $total_amount = 0;
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
            $sum_quantity = $sum_quantity_row['sum_quantity'];
            // Rounded by product
            $total_cost += round($row['cost_price']*$sum_quantity*$coefQu, 2);
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
            $html .= '<h3><div class="cel13">'.$row['p_name'].
                    '<div class="lite" style="float:right">'.$row['um_name'].'</div>'.
                '</div>'.
                '<div class="cel1 num">'.
                    clean_zeros($sum_quantity).'</div>'.
                'x<div class="cel1h aprov">'.
                    number_format($row['cost_price'], 2).'</div>'.
                '=<div class="cel2 aprov">'.
                    number_format($row['cost_price']*$sum_quantity*$coefQu, 2).'</div>'.
                "</h3>\n";
            if ($detail) {
                $html .= '<div class="block-body">' . "\n";
            }
        }
        // Rounded by UF
        $total_amount += round($row['final_price']*$row['quantity'], 2);
        if ($detail) {
            $html .= '<div>';
            $html .= '<div class="cel1 num">'.clean_zeros($row['quantity']).'</div>';
            $html .= '&nbsp;<div class="cel8">';
            $html .= '#'.$row['uf_id'].'-'.$row['uf_name'];
            $html .= "</div></div>\n";
        }
    }
    $html .= break2Html_end($brk, $detail);
    $html_f = orderHtml_end($total_cost, $total_amount, $ppcDescompte,
        $brk['1_desc_order']);
    if ($foot) {
        $html .= $html_f;
    }
    echo $html;
    return $html_f;
}

function write_order_fam_prod(
        $date_for_order, $provider_id, $order_id, $ppcDescompte, $detail,
        $head = true, $foot = true) {
    $db = DBWrap::get_instance();
    
    $coefQu = 100.0/(100.0 - $ppcDescompte);
    $strSQL = get_SQL_orders($date_for_order, $provider_id, $order_id);
    if ( get_config('order_review_uf_sequence', 'desc') == 'asc' ) {
        $strSQL .= 'pv_name, order_id, uf_id, p_name';
    } else {
        $strSQL .= 'pv_name, order_id, uf_id desc, p_name';
    }
    $rs = $db->Execute($strSQL);
    $brk = array(
        'date_for_order' => $date_for_order,
        '1_provider_id' => null,
        '1_order_id_n' => null,
        '1_desc_order' => null,
        '2_id_break' => null
    );
    $total_cost = 0;
    $total_amount = 0;
    $aaa_quantity =0;
    $sum_quantity=0;
    $html = '';
    while ($row = $rs->fetch_array()) {        
        if ($row['quantity'] == 0) { continue; }
        if ($brk['1_provider_id'] != $row['provider_id'] || 
                    $brk['1_order_id'] != $row['order_id']) {
            // end previus order
            $html .= break2Html_end($brk, $detail);
            $html .= orderHtml_end($total_cost, $total_amount, $ppcDescompte,
                $brk['1_desc_order']);
            // start order
            $total_cost = 0;
            $total_amount = 0;
            $html_h = orderHtml_start($brk, $row);
            if ($head) {
                $html .= $html_h;
            }
        }
        if ($brk['2_id_break'] != $row['uf_id']) {
            $html .= break2Html_end($brk, $detail);
            // start uf
            $det_lin = 0;
            $brk['2_id_break'] = $row['uf_id'];
            $html .= '<div class="block">';
            $html .= '<h3><div class="cel10">'.
                    '#'.$row['uf_id'].'-<b>'.$row['uf_name'].'</b></div>'.
                "</h3>\n";
            if ($detail) {
                $html .= '<div class="block-body">' . "\n";
            }
        }
        // Rounded by UF
        $total_amount += round($row['final_price']*$row['quantity'], 2);
        $total_cost += round($row['cost_price']*$row['quantity']*$coefQu, 2);
        if ($detail) {
            $border_det = "border-bottom: dotted 1px #777;";
            if ($det_lin === 0) {
                $border_det .= "border-top: dotted 1px #777;";
            }
            $det_lin++;
            $html .= '<div style="'.$border_det.' padding:.05cm 0">';
            $html .= 
                '<div class="cel1h num">'.
                        clean_zeros($row['quantity']).'</div>'.
                '<div class="cel0h center">x</div>'.
                '<div class="cel12">'.$row['p_name'].'</div>'.
                '<div class="cel3 lite">('.$row['um_name'].')</div>';
            $html .= "</div>\n";
        }
    }
    $html .= break2Html_end($brk, $detail);
    $html_f = orderHtml_end($total_cost, $total_amount, $ppcDescompte,
            $brk['1_desc_order']);
    if ($foot) {
        $html .= $html_f;
    }
    echo $html;
    return $html_f;
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

function orderHtml_end($total_cost, $total_amount, $ppcDescompte, $desc) {
    if ($desc == null) {
        return '';
    }
    return '<div class="foot">'.
                '<div class="cel8 lite gray">'.$desc.'</div>'.
                '<div class="cel3 num lite gray">' . i18n('final_amount') .
                    ':</div>'.
                '<span class="cel2 num gray">'.
                    number_format($total_amount,2).
                '</span>'.
                '<div class="cel3 num lite gray">Cost sense IVA:'.
                    ($ppcDescompte ? "<br>(falta {$ppcDescompte}%)" : '').
                    '</div>'.
                '<div class="cel2 aprov">'.
                    number_format($total_cost,2).
                '</div>'.
            '</div>';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo report_get_title('Prv-Fam', $date_for_order, $order_id, $detail); ?></title>
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
