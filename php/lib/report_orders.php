<?php
/** 
 * Aixada
 */
require_once(__ROOT__ . 'local_config/config.php');
require_once(__ROOT__ . 'php/utilities/general.php');
if (!isset($_SESSION)) {
    session_start();
}
require_once(__ROOT__ . 'local_config/lang/' . get_session_language() . '.php');
require_once(__ROOT__ . 'php/inc/database.php');

/**
 *
 */
class report_order
{
    protected $priceType = null;
    protected $dataColsCount = 7; // 5 data cols + 2 indent cols;
    protected $amountColsCount = 0;
    
    public function __construct($priceType = 'none')
    {
        $this->setPriceType($priceType);
    }
    
    public function setPriceType($priceType) {
        $this->priceType = $priceType;
        switch ($this->priceType) {
            case 'cost':
            case 'final':
                $this->amountColsCount = 2;
                break;
            case 'cost_amount':
            case 'final_amount':
                $this->amountColsCount = 1;
                break;
            default:
                $this->amountColsCount = 0;
                break;
        }
    }
    
    /**
     * 
     */
    public function getHtml_ufOrderProd($order_id, $provider_id = null, $date_for_order = null) {
        $db = DBWrap::get_instance();
        $strSQL = 
            $this->getSql_orderDetail($order_id, $provider_id, $date_for_order) .
            'uf_name, date_for_order, pv_name, order_id, p_name';
        $rs = $db->Execute($strSQL);
        $html = '';
        $tbody = '';
        $brk_uf = null;
        $brk_order = null;
        while ($row = $rs->fetch_assoc()) {
            $cur_order = "{$row['provider_id']}|{$row['date_for_order']}|{$row['order_id']}";
            if ($brk_uf != $row['uf_id']) {
                if ($brk_uf) {
                    $html .= $this->t_table('', $tbody) . '<br>';
                }
                $brk_uf = $row['uf_id'];
                $brk_order = null;
                $tbody .= $this->t_uf_head(0, $row['uf_id'], $row['uf_name']);
            }
            if ($brk_order != $cur_order) {
                $brk_order = $cur_order;
                $tbody .= 
                    $this->t_order_head(1,
                        $this->dataColsCount + $this->amountColsCount,
                        $row['order_id'], $row['pv_name'], $row['date_for_order']
                    ) .
                    $this->t_title_head(2);
            }
            $tbody .= $this->t_data_row(2, false,
                $row['p_name'],
                $row['p_desc'],
                $row['orderable_type_id'],
                $row['order_notes'],
                $row['order_quantity'],
                $row['current_quantity'],
                $row['unit'],
                $row['cost_price'],
                $row['final_price']
            );
        }
        if ($brk_uf) {
            $html .= $this->t_table('', $tbody);
        }
        return $html . $this->getHtml_priceDescription();
    }
    
    public function getHtml_orderUfProd($order_id, $provider_id = null, $date_for_order = null) {
        $db = DBWrap::get_instance();
        $strSQL = 
            $this->getSql_orderDetail($order_id, $provider_id, $date_for_order) .
            'pv_name, date_for_order, order_id, uf_name, p_name';
        $rs = $db->Execute($strSQL);
        $html = '';
        $tbody = '';
        $brk_order = null;
        $brk_uf = null;
        while ($row = $rs->fetch_assoc()) {
            $cur_order = "{$row['provider_id']}|{$row['date_for_order']}|{$row['order_id']}";
            if ($brk_order != $cur_order) {
                if ($brk_order) {
                    $html .= $this->t_table('', $tbody) . '<br>';
                }
                $brk_order = $cur_order;
                $brk_uf = null;
                $tbody = $this->t_order_head(0,
                    $this->dataColsCount + $this->amountColsCount,
                    $row['order_id'], $row['pv_name'], $row['date_for_order']
                );
            }
            if ($brk_uf != $row['uf_id']) {
                if ($brk_uf) {
                    
                }
                $brk_uf = $row['uf_id'];
                $tbody .= $this->t_uf_head(1, $row['uf_id'], $row['uf_name']);
            }
            $tbody .= $this->t_data_row(2, false,
                $row['p_name'],
                $row['p_desc'],
                $row['orderable_type_id'],
                $row['order_notes'], 
                $row['order_quantity'],
                $row['current_quantity'],
                $row['unit'],
                $row['cost_price'],
                $row['final_price']
            );
        }
        if ($brk_order) {
            $html .= $this->t_table('', $tbody);
        }
        return $html . $this->getHtml_priceDescription();
    }
    
    public function getHtml_orderProd($order_id, $provider_id = null, $date_for_order = null)
    {
        return $this->getHtml_orderProdUfSel($order_id, $provider_id, $date_for_order, false);
    }
    
    public function getHtml_orderProdUf($order_id, $provider_id = null, $date_for_order = null)
    {
        return $this->getHtml_orderProdUfSel($order_id, $provider_id, $date_for_order, true);
    }
    
    protected function getHtml_orderProdUfSel($order_id, $provider_id, $date_for_order, $detail)
    {
        global $Text;
        $db = DBWrap::get_instance();
        $strSQL = 
            $this->getSql_orderDetail($order_id, $provider_id, $date_for_order) .
            'pv_name, date_for_order, order_id, p_name, uf_name';
        $rs = $db->Execute($strSQL);
        $html = '';
        $tbody = '';
        $p_tbody = '';
        $p_order_quantity = 0;
        $p_current_quantity = 0;
        $brk_order = null;
        $brk_product = null;
        while ($row = $rs->fetch_assoc()) {
            $cur_order = "{$row['provider_id']}|{$row['date_for_order']}|{$row['order_id']}";
            if ($brk_order != $cur_order) {
                if ($brk_order) {
                    $tbody .= $p_end();
                    $html .= $this->t_table($thead, $tbody) . '<br>';
                }
                $brk_order = $cur_order;
                $brk_product = null;
                $thead = 
                    $this->t_order_head(0,
                        $this->dataColsCount + $this->amountColsCount,
                        $row['order_id'], $row['pv_name'], $row['date_for_order']
                    ) .
                    $this->t_title_head(1);
            }
            if ($brk_product != $row['product_id']) {
                if ($brk_product) {
                    $tbody .= $p_end();
                }
                $brk_product = $row['product_id'];
                $p_tbody = '';
                $p_name = $row['p_name'];
                $p_desc = $row['p_desc'];
                $p_order_quantity = 0;
                $p_current_quantity = 0;
                $p_cost_price = $row['cost_price'];
                $p_final_price = $row['final_price'];
                $p_unit = $row['unit'];
                $p_orderable_type_id = $row['orderable_type_id'];
                $p_end = function() use(
                    $detail,
                    &$p_name,
                    $p_desc,
                    &$p_orderable_type_id,
                    &$p_order_quantity,
                    &$p_current_quantity,
                    &$p_unit,
                    &$p_cost_price,
                    &$p_final_price,
                    &$p_tbody
                ) {
                    return $this->t_data_row(1, $detail,
                        $p_name,
                        $p_desc,
                        $p_orderable_type_id,
                        null,
                        $p_order_quantity,
                        $p_current_quantity,
                        $p_unit,
                        $p_cost_price,
                        $p_final_price
                    ) . $p_tbody;
                };
            }
            $p_order_quantity += $row['order_quantity'];
            $p_current_quantity += $row['current_quantity'];
            if ($detail) {
                $p_tbody .= $this->t_data_row(2, false,
                    "{$row['uf_name']} {$Text['uf_short']}-{$row['uf_id']}",
                    null,
                    $row['orderable_type_id'],
                    $row['order_notes'],
                    $row['order_quantity'],
                    $row['current_quantity'],
                    null,
                    null,
                    null     
                );
            }
        }
        if ($brk_order) {            
            $tbody .= $p_end();
            $html .= $this->t_table($thead, $tbody) . '<br>';
        }
        return $html . $this->getHtml_priceDescription();
    }
    
    public function getHtml_orderMatrix($order_id, $provider_id = null, $date_for_order = null)
    {
        $db = DBWrap::get_instance();
        $strSQL = 
            $this->getSql_orderDetail($order_id, $provider_id, $date_for_order) .
            'pv_name, date_for_order, order_id, p_name, uf_name';
        $rs = $db->Execute($strSQL);
        
        $html = '';
        $brk_order = null;
        $brk_product = null;
        while ($row = $rs->fetch_assoc()) {
            $cur_order = "{$row['provider_id']}|{$row['date_for_order']}|{$row['order_id']}";
            if ($brk_order != $cur_order) {

                if ($brk_order) {
                    if ($brk_product) {
                        $matrix[$p_name]['Total_qty'] = $p_current_quantity;
                    }
                    $html .= $this->getHtml_orderMatrixTable($order_id, $pv_name, $date_for_order, $uf_list, $matrix) . '<br>';
                }
                $brk_order = $cur_order;
                $order_id = $row['order_id'];
                $pv_name = $row['pv_name'];
                $date_for_order = $row['date_for_order'];
                $matrix = array();
                $uf_list = array();
                $brk_product = null;
            }
            if ($brk_product != $row['product_id']) {
                if ($brk_product) {
                    $matrix[$p_name]['Total_qty'] = $p_current_quantity;
                }
                $brk_product = $row['product_id'];
                $p_name = $row['p_name'];
                $p_current_quantity = 0;
                $matrix[$p_name] = array();
                $matrix[$p_name]['unit'] = $row['unit'];
            }
            
            $p_current_quantity += $row['current_quantity'];
            $matrix[$p_name][$row['uf_id']] = $row['current_quantity'];
            $uf_list[] = $row['uf_id'];
        }
        if ($brk_order) {
            if ($brk_product) {
                $matrix[$p_name]['Total_qty'] = $p_current_quantity;
            }
            $html .= $this->getHtml_orderMatrixTable($order_id, $pv_name, $date_for_order, $uf_list, $matrix);
        }
        return $html;
    }
    
    protected function getHtml_orderMatrixTable($order_id, $pv_name, $date_for_order, $uf_list, $matrix)
    {
        global $Text;
        $uf_list = array_unique($uf_list);
        natsort($uf_list);
        
        // Head
        $thead = $this->t_order_head(0, 
            count($uf_list) + 2,
            $order_id, $pv_name, $date_for_order
        );
        $thead .= "<tr>\n" . $this->t_th(1, $Text['product_name'], '" rowspan="2');
        $thead .= $this->t_th(count($uf_list), "UFs");
        $thead .= $this->t_th(1, $Text['quantity'], '" rowspan="2') . "</tr>\n";
        $thead .= "<tr>\n";
        foreach ($uf_list as $uf) {
            $thead .= $this->t_th(1, $uf);
        }
        $thead .= "</tr>\n";
        
        // Body
        $tbody = '';
        foreach (array_keys($matrix) as $product) {
            $tbody .= 
                "<tr>" .
                $this->t_cel(1, 
                    $product .
                    ($matrix[$product]['unit'] ? 
                        " [{$matrix[$product]['unit']}]" : '')
                );
            foreach ($uf_list as $uf) {
                if (isset($matrix[$product][$uf])) {
                    $tbody .= $this->t_celNum(1, $matrix[$product][$uf]);
                } else {
                    $tbody .= $this->t_cel(1, '&nbsp;');
                }
            }
            $tbody .=
                $this->t_celNum(1, $matrix[$product]['Total_qty'], 'font-weight: bold;') .
                "</tr>\n";
        }
        return $this->t_table($thead, $tbody) . '<br>';
    }
    
    protected function getHtml_priceDescription() 
    {
        global $Text;
        switch ($this->priceType) {
            case 'none':
                return '';
            case 'cost':
            case 'cost_amount':
                $desc = $Text['cost_amount_desc'];
                break;
            case 'final':
            case 'final_amount':
                $desc = $Text['final_amount_desc'];
                break;
            default:
                $desc = '??? ' . $this->priceType . ' ???';
                break;
        }
        return "<p style=\"text-align:center;\">{$desc}</p>\n";
    }
    
    /**
     * 
     */
    protected function get_statusDesc($revision_status)
    {
        global $Text;
        switch ($revision_status){
            case null:
                $text_key = 'not_yet_sent';
                $style = '';
                break;                
            case "1":
                $text_key = 'ostat_yet_received';
                $style = 'background:#D1F3D1; color:green;';
                break;
            case "2": 
               $text_key = 'ostat_is_complete';
               $style = 'color:green;';
                break;
            case "3": 
                $text_key = 'ostat_postponed';
                $style = 'background:#efd2f8;';
                break;
            case "4": 
                $text_key = 'ostat_canceled';
                $style = 'background:#ff7a75; color:#900;';
                break;
            case "5": 
                $text_key = 'ostat_changes';
                $style = 'background:#FFEDA1;color:green;';
                break;
            default:
                return '<span class="DATA-not_yet_sent">??OrderStatus="'.
                    $revision_status.'"??</span>';
        }
        return "[<span style=\"padding:0 3px; {$style}\">".$Text[$text_key].'</span>]';
    }
    
    /**
     * 
     */
    
    /**
     * 
     */
    protected function getSql_orderDetail($order_id, $provider_id, $date_for_order)
    {
        $sql = "select 	
                oi.order_id, oi.date_for_order,
                o.revision_status order_status,
                oi.product_id, p.name p_name, p.description p_desc, p.orderable_type_id,
                oi.quantity order_quantity,
                oi.notes order_notes,
                if(os.quantity is not null, os.quantity*os.arrived, oi.quantity) current_quantity,
                ifnull(os.unit_price_stamp, oi.unit_price_stamp) final_price, 
                if( os.unit_price_stamp is not null,
                    round(
                        os.unit_price_stamp / 
                        (1 + os.iva_percent/100) / 
                        (1 + os.rev_tax_percent/100), 2),
                    p.unit_price
                ) cost_price,
                oi.uf_id, uf.name uf_name,
                p.provider_id, pv.name pv_name,
                if(p.orderable_type_id = 3, null, um.unit) unit
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
        if (is_array($order_id)) {   // order_id
            $sql .= "oi.order_id in( " . implode(',', $order_id) . ")";
        } elseif ($order_id !== -1 && $order_id !== null) {   // order_id
            $sql .= "oi.order_id={$order_id}";
        } elseif ($date_for_order !== null) { 	        // date for orrder
            $sql .= "oi.date_for_order='{$date_for_order}'";
            if ($provider_id !== -1 && $provider_id !== null) {
                $sql .= " and p.provider_id={$provider_id}";
            } else {
                $sql .= " and ( revision_status is null or revision_status in (1,2,5) )";
            }
        } else { // no filter, so nothing to show!
            $sql .= "1=0";
        }
        return "select * from ({$sql}) r order by ";
    }
    
    /**
     *  Templates
     */
    protected function t_table($thead, $tbody, $style = '') 
    {
        return 
            "<table style=\"margin-left:30px; border-collapse:collapse; {$style}\">\n" .
                "<thead>\n{$thead}\n</thead>\n" .
                "<tbody>\n{$tbody}\n</tbody>\n" .
            "</table>\n";
    }

    protected function t_order_head($level, $colCount, $order_id, $pv_name, $date_for_order) 
    {
        $html = '<tr>';
        if ($level) {
            $html .= $this->t_celBlanck($level, 'border:transparent;');
        }
        $status = '';
        if ($order_id) {
            $row =  get_row_query(
                "SELECT revision_status FROM aixada_order WHERE id = {$order_id}"
            );
            if ($row) {
                $status = $this->get_statusDesc($row['revision_status']);
            }
        } else {
            $status = $this->get_statusDesc(null);
        }
        return $html .
            $this->t_cel(
                $colCount - $level,
                "{$pv_name}: {$date_for_order}&nbsp; #{$order_id}&nbsp; " .
                    "<span style=\"color:#777; font-size:80%\">{$status}</span>",
                'border:transparent; padding:6px 0 3px 0',
                true
            ) .
            "</tr>\n";
    }
    protected function t_title_head($level)
    {
        global $Text;
        $html = '<tr>';
        if ($level) {
            $html .= $this->t_celBlanck($level, 'border:transparent;');
        }
        $html .= 
            $this->t_th(
                2 + (2 - $level),
                $Text['product_name']
            ) . // $this->t_th(1, $Text['description']) .
            $this->t_th(2, $Text['quantity']) .
            $this->t_th(1, $Text['unit']);
        switch ($this->priceType) {
            case 'cost':
                $html .= $this->t_th(1, $Text['price']);
                $html .= $this->t_th(1, $Text['cost_amount']);
                break;
            case 'cost_amount':
                $html .= $this->t_th(1, $Text['cost_amount']);
                break;
            case 'final':
                $html .= $this->t_th(1, $Text['price']);
                $html .= $this->t_th(1, $Text['final_amount']);
                break;
            case 'final_amount':
                $html .= $this->t_th(1, $Text['final_amount']);
                break;
        }
        $html .= "</tr>\n";
        return $html;
    }
    
    protected function t_uf_head($level, $uf_id, $uf_name) 
    {
        global $Text;
        $html = '<tr>';
        if ($level) {
            $html .= $this->t_celBlanck($level, 'border:transparent;');
        }
        return $html .
            $this->t_cel(
                $this->dataColsCount + $this->amountColsCount - $level,
                "{$uf_name} {$Text['uf_short']}-{$uf_id}",
                'border:transparent;',
                true
            ) .
            "</tr>\n";
    }


    protected function t_data_row(
        $level,
        $underline,
        $name,
        $desc,
        $orderable_type_id,
        $order_notes,
        $order_quantity,
        $current_quantity,
        $unit,
        $p_cost_price,
        $p_final_price
    ) {
        $html = '<tr>';
        $html .= $this->t_celBlanck($level);
        $colspan1 = 3 - $level;
        if ($orderable_type_id == 3) { // product.orderable_type_id=3 => Use product as order notes
            $text = $name;
            if ($order_notes) {
                $text .=
                    "\n<div style=\"padding: 0 5px 5px 5px;\">\n" .
                    "<pre style=\"margin:0; border:1px dotted #ccc;padding:0px 5px;\" \n" .
                    ">{$order_notes}</pre></div>";
            }
            $html .= $this->t_cel($colspan1 + $this->dataColsCount, $text, $underline);
            return $html . "</tr>\n";
        } else { // product as order notes
            if ($desc) {
                $html .= $this->t_cel($colspan1, $name, $underline, $underline);
                $html .= $this->t_cel(1, $desc, $underline, $underline);
            } else {
                $html .= $this->t_cel($colspan1 + 1, $name, $underline, $underline);
            }
            if ($order_quantity != $current_quantity) {
                $html .= 
                    $this->t_cel(1, 
                        '( ' . clean_zeros($order_quantity) . ' )', 
                        'color:#777;XXfont-size:80%;text-align:right;',
                        $underline
                    ) .
                    $this->t_celNum(1, $current_quantity, '', $underline);
            } else {
                $html .= $this->t_celNum(2, $current_quantity, '', $underline);
            }
            $html .= $this->t_cel(1, $unit, 'max-width:9em', $underline);
            switch ($this->priceType) {
                case 'cost':
                    if ($p_cost_price) {
                        $html .= $this->t_celAmo(1,
                            $p_cost_price, 'color:#555;font-size:80%');
                        $html .= $this->t_celAmo(1,
                            $p_cost_price * $current_quantity, '', $underline);
                    } else {
                        $html .= $this->t_cel(2, '&nbsp;', '', $underline);
                    }
                    break;
                case 'cost_amount':
                    if ($p_cost_price) {
                        $html .= $this->t_celAmo(1,
                            $p_cost_price * $current_quantity, '', $underline);
                    } else {
                        $html .= $this->t_cel(1, '&nbsp;', '', $underline);
                    }
                    break;
                case 'final':
                    if ($p_final_price) {
                        $html .= 
                            $html .= $this->t_celAmo(1,
                                $p_final_price, 'color:#555;font-size:80%');
                            $html .= $this->t_celAmo(1,
                                $p_final_price * $current_quantity, '', $underline);
                    } else {
                        $html .= $this->t_cel(2, '&nbsp;', '', $underline);
                    }
                    break;
                case 'final_amount':
                    if ($p_final_price) {
                        $html .= $html .= $this->t_celAmo(1,
                            $p_final_price * $current_quantity, '', $underline);
                    } else {
                        $html .= $this->t_cel(1, '&nbsp;', '', $underline);
                    }
                    break;
            }
            return $html . "</tr>\n";
        }
    }

    protected function t_th($colspan, $text, $style = '')
    {
        $html = "<th";
        if ($colspan > 1) {
            $html .= " colspan=\"{$colspan}\"\n";
        }
        return $html . 
            " style=\"text-align:center; border:1px solid #ccc; padding:6px 4px; background-color:#ddd; {$style}\"\n>{$text}</th>\n";
    }
    
    protected function t_cel($colspan, $text, $style = '', $underline = false)
    {
        $html = "<td";
        if ($colspan > 1) {
            $html .= " colspan=\"{$colspan}\"\n";
        }
        if ($underline) {
            return $html .=  
                " style=\"border:1px solid #ccc; padding:6px 0; {$style}\"><div \n" .
                " style=\"margin-top:5px; 2px;border-bottom:1px solid #333;padding:0 4px;\"\n" .
                ">{$text}</div></td>\n";
        } else {
            return $html .= 
                " style=\"border:1px solid #ccc; padding:6px 4px; {$style}\"\n" .
                ">{$text}</td>\n";
            
        }
    }
    
    protected function t_celNum($colspan, $number, $style = '', $underline = false)
    {
        return $this->t_cel(
            $colspan,
            clean_zeros($number),
            'text-align:right;' . $style,
            $underline
        );
    }
        
    protected function t_celAmo($colspan, $number, $style = '', $underline = false)
    {
        return $this->t_cel(
            $colspan,
            ($number ? number_format($number, 2) : '&nbsp;'),
            'text-align:right;' . $style,
            $underline
        );
    }
    
    protected function t_celBlanck($colspan, $style = '')
    {
        $html = "<td";
        if ($colspan > 1) {
            $html .= " colspan=\"{$colspan}\"\n";
        }
        return $html . 
            " style=\"border-right:1px solid #ccc; padding:0; width:{$colspan}em; {$style}\">&nbsp;</td>\n";
    }
}
?>