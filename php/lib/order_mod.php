<?php


require_once(__ROOT__ . "php/lib/aixmodel.php");


class Order extends Aixmodel {



	public function __construct(){

		parent::__construct("aixada_order");
	}


	
	/**
	 * Retrieves products with prices that have been ordered.
	 * @param integer|null $order_id Order id (if is null uses $date)
	 * @param integer $provider_id
	 * @param string|null $date Date for order (used only when $order_id is null)
	 * @return mysqli_result
	 */
	public function get_ordered_products_with_prices($order_id, $provider_id, $date) {
	    $sql = "
	        select distinct
	            p.id, 
	            p.name, 
	            um.unit,
	            round( 
	                max(ifnull(ots.unit_price_stamp, oi.unit_price_stamp)) / 
	                (1 + rev.rev_tax_percent/100) /
	                (1 + iva.percent/100), 2) gross_price,
	            iva.percent iva_percent,
	            round( 
	                max(ifnull(ots.unit_price_stamp, oi.unit_price_stamp)) /
	                (1 + rev.rev_tax_percent/100), 2) net_price,
	            rev.rev_tax_percent,
	            max(ifnull(ots.unit_price_stamp,oi.unit_price_stamp)
	                ) uf_price
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
	    $sql .= "
	        group by p.id, p.name, 
	                um.unit, rev.rev_tax_percent, iva.percent";
	    $sql .= " order by p.name";

	    return DBWrap::get_instance()->Execute($sql);
	}



}


	
?>