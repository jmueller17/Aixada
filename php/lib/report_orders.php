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
    protected $prices = null;
    private $countCols = 0;
    public function __construct($prices = 'none')
    {
        $this->prices = $prices;
        $this->countCols = 6;
        if ($this->prices !== 'none') {
            $this->countCols += 2;
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
                $tbody = $this->t_uf_head(0, 
                    $row['uf_id'], $row['uf_name']
                );
            }
            if ($brk_order != $cur_order) {
                $brk_order = $cur_order;
                $tbody .= $this->t_order_head(1, 
                    $row['order_id'], $row['pv_name'], $row['date_for_order']
                );
            }
            $tbody .= $this->t_name_row(2,
                $row['p_name'],
                $row['order_quantity'],
                $row['current_quantity'],
                $row['cost_price'],
                $row['final_price'],
                $row['unit'],
                $row['orderable_type_id'],
                $row['order_notes'] 
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
                    $row['order_id'], $row['pv_name'], $row['date_for_order']
                );
            }
            if ($brk_uf != $row['uf_id']) {
                if ($brk_uf) {
                    
                }
                $brk_uf = $row['uf_id'];
                $tbody .= $this->t_uf_head(1,
                    $row['uf_id'], $row['uf_name']
                );
            }
            $tbody .= $this->t_name_row(2,
                $row['p_name'],
                $row['order_quantity'],
                $row['current_quantity'],
                $row['cost_price'],
                $row['final_price'],
                $row['unit'],                
                $row['orderable_type_id'],
                $row['order_notes'] 
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
    
    protected function getHtml_orderProdUfSel($order_id, $provider_id, $date_for_order, $detail) {
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
                    $html .= $this->t_table('', $tbody) . '<br>';
                }
                $brk_order = $cur_order;
                $brk_product = null;
                $tbody = $this->t_order_head(0,
                    $row['order_id'], $row['pv_name'], $row['date_for_order']
                );
            }
            if ($brk_product != $row['product_id']) {
                if ($brk_product) {
                    $tbody .= $p_end();
                }
                $brk_product = $row['product_id'];
                $p_tbody = '';
                $p_name = $row['p_name'];
                $p_order_quantity = 0;
                $p_current_quantity = 0;
                $p_cost_price = $row['cost_price'];
                $p_final_price = $row['final_price'];
                $p_unit = $row['unit'];
                $p_orderable_type_id = $row['orderable_type_id'];
                $p_end = function() use(
                    $detail,
                    &$p_name,
                    &$p_order_quantity,
                    &$p_current_quantity,
                    &$p_cost_price,
                    &$p_final_price,
                    &$p_unit,
                    &$p_orderable_type_id,
                    &$p_tbody
                ) {
                    if ($detail) {
                        $pp_name = 
                            "<div style=\"margin-top:5px; 2px;border-bottom:1px solid #333;padding:0px 2px;\">\n{$p_name}</div>\n";
                        $pp_unit = 
                            "<div style=\"margin-top:5px; 2px;border-bottom:1px solid #333;padding:0px 2px;\">\n{$p_unit}</div>\n";
                    } else {
                        $pp_unit = $p_unit;
                        $pp_name = $p_name;
                    }
                    return $this->t_name_row(1,
                        $pp_name,
                        $p_order_quantity,
                        $p_current_quantity,
                        $p_cost_price,
                        $p_final_price,
                        $pp_unit,
                        $p_orderable_type_id,
                        null
                    ) . $p_tbody;
                };
            }
            $p_order_quantity += $row['order_quantity'];
            $p_current_quantity += $row['current_quantity'];
            if ($detail) {
                $p_tbody .= $this->t_name_row(2,
                    $row['uf_name'],
                    $row['order_quantity'],
                    $row['current_quantity'],
                    null,
                    null,
                    null,                    
                    $row['orderable_type_id'],
                    $row['order_notes'] 
                );
            }
        }
        if ($brk_order) {
            $tbody .= $p_end();
            $html .= $this->t_table('', $tbody);
        }
        return $html . $this->getHtml_priceDescription();
    }
    protected function getHtml_priceDescription() 
    {
        switch ($this->prices) {
            case 'none':
                return '';
            case 'cost':
                $desc = '=cost';
                break;
            case 'final':
                $desc = '=final';
                break;
            default:
                $desc = '??? ' . $this->prices . ' ???';
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
    
    /**
     * 
     */
    protected function getSql_orderDetail($order_id, $provider_id, $date_for_order)
    {
        $sql = "select 	
                oi.order_id, oi.date_for_order,
                o.revision_status order_status,
                oi.product_id, p.name p_name, p.orderable_type_id,
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

    protected function t_order_head($level, $order_id, $pv_name, $date_for_order) 
    {
        $html = '<tr>';
        if ($level) {
            $html .= $this->t_celBlanck($level, 'border:transparent;');
        }
        return $html .
            $this->t_cel($this->countCols - $level,
                "<div style=\"margin-top:5px; 2px;border-bottom:1px solid #333;padding:0px 2px;\">\n{$pv_name}: {$date_for_order} #{$order_id}</div>\n",
                'border:transparent;'
            ) . "</tr>\n";
    }
    
    protected function t_uf_head($level, $uf_id, $uf_name) 
    {
        $html = '<tr>';
        if ($level) {
            $html .= $this->t_celBlanck($level, 'border:transparent;');
        }
        return $html .
            $this->t_cel($this->countCols - $level,
                "<div style=\"margin-top:5px; 2px;border-bottom:1px solid #333;padding:0px 2px;\">\n{$uf_name} UF{$uf_id}</div>\n",
                'border:transparent;'
            ) . "</tr>\n";
    }

    protected function t_name_row(
        $level,
        $name,
        $order_quantity,
        $current_quantity,
        $p_cost_price,
        $p_final_price,
        $unit,
        $orderable_type_id,
        $order_notes
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
            $html .= $this->t_cel($colspan1 + ($this->countCols - 2), $text);
            return $html . "</tr>\n";
        } else { // product as order notes
            $html .= $this->t_cel($colspan1, $name);
            if ($order_quantity != $current_quantity) {
                $html .= 
                    $this->t_cel(1, 
                        '( ' . clean_zeros($order_quantity) . ' )', 
                        'color:#777;font-size:80%;text-align:right;'
                    ) .
                    $this->t_cel(1, clean_zeros($current_quantity), 'text-align:right;');
            } else {
                $html .= 
                    $this->t_cel(2, clean_zeros($current_quantity), 'text-align:right;');
            }
            $html .= $this->t_cel(1, $unit, 'max-width:9em');
            switch ($this->prices) {
                case 'cost':
                    if ($p_cost_price) {
                        $html .= 
                            $this->t_cel(1, 
                                number_format($p_cost_price, 2) . '=',
                                'text-align:right;'
                            ) . $this->t_cel(1, 
                                number_format($p_cost_price * $current_quantity, 2),
                                'text-align:right;'
                            );
                    } else {
                        $html .= $this->t_cel(2, '&nbsp;');
                    }
                    break;
                case 'final':
                    if ($p_final_price) {
                        $html .= 
                            $this->t_cel(1, 
                                number_format($p_final_price, 2) . '=',
                                'text-align:right;'
                            ) . $this->t_cel(1, 
                                number_format($p_final_price * $current_quantity, 2),
                                'text-align:right;'
                            );
                    } else {
                        $html .= $this->t_cel(2, '&nbsp;');
                    }
                    break;
            }
            return $html . "</tr>\n";
        }
    }
    
    protected function t_cel($colspan, $text, $style = '')
    {
        $html = "<td";
        if ($colspan > 1) {
            $html .= " colspan=\"{$colspan}\"\n";
        }
        return $html . 
            " style=\"border:1px solid #ccc; padding:3px 4px; {$style}\"\n>{$text}</td>\n";
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