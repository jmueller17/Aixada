delimiter |


/**
 * returns all items in aixada_shop_item for 
 * a specific date and an uf
 * 
 */
drop procedure if exists get_shop_cart| 
create procedure get_shop_cart(in the_date date, in the_uf_id int)
begin
  select 
    p.id,
    p.name,
    p.description,
    si.quantity as quantity,
    si.cart_id,
    p.provider_id,  
    pv.name as provider_name,
    p.category_id, 
    p.unit_price * (1 + iva.percent/100) as unit_price, 
    rev.rev_tax_percent,
    um.unit
  from 
  	aixada_shop_item si,
  	aixada_product p, 
  	aixada_provider pv, 
  	aixada_rev_tax_type rev, 
  	aixada_iva_type iva, 
  	aixada_unit_measure um
  where
  	si.uf_id = the_uf_id
  	and si.date_for_shop = the_date
  	and si.ts_validated = 0
  	and si.product_id = p.id
  	and pv.id = p.provider_id
  	and rev.id = p.rev_tax_type_id
  	and iva.id = p.iva_percent_id
  	and um.id = p.unit_measure_shop_id
  order by p.provider_id, p.name; 
end|


/**
 * returns all items in aixada_order_item for 
 * a specific date and an uf
 * 
 */
drop procedure if exists get_order_cart| 
create procedure get_order_cart(in the_date date, in the_uf_id int)
begin
  select 
    p.id,
    p.name,
    p.description,
    oi.quantity as quantity,
    oi.cart_id,
    p.provider_id,  
    pv.name as provider_name,
    p.category_id, 
    oi.closing_date, 
    p.unit_price * (1 + iva.percent/100) as unit_price, 
    if (p.orderable_type_id = 4 and oi.date_for_order = '1234-01-23', 'true', 'false') as preorder, 
    rev.rev_tax_percent,
    um.unit
  from 
  	aixada_order_item oi,
  	aixada_product p, 
  	aixada_provider pv, 
  	aixada_rev_tax_type rev, 
  	aixada_iva_type iva, 
  	aixada_unit_measure um
  where
  	oi.date_for_order in (the_date, '1234-01-23')	
  	and oi.uf_id = the_uf_id
  	and oi.product_id = p.id
  	and pv.id = p.provider_id
  	and rev.id = p.rev_tax_type_id
  	and iva.id = p.iva_percent_id
  	and um.id = p.unit_measure_order_id
  	/** and orderable_type_id > 1  ... why do we need this? if items are in aixada_order_item, they are orderable **/
  order by p.provider_id, p.name; 
end|




delimiter ; 