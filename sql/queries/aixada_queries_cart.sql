delimiter |


/**
 * returns all items in aixada_shop_item for 
 * a specific date and an uf OR a given cart_id,
 * The validated parameter specifies if we are interested in validated carts or only those
 * that have not a ts_validated timestamp.
 */
drop procedure if exists get_shop_cart|
create procedure get_shop_cart(in the_date_for_shop date, in the_uf_id int, in the_cart_id int, in validated boolean)
begin

  declare wherec varchar(255) default "";

  if (the_cart_id > 0) then
		set wherec = concat("c.id = ", the_cart_id); 
  else
		set wherec = concat("c.date_for_shop='",the_date_for_shop,"' and c.uf_id=",the_uf_id);
  end if; 
  
  -- retrieve only non-validated carts (for aixada cart). for report retrieve all including validated ones -- 
  if (validated is false) then
  	set wherec = concat(wherec, " and c.ts_validated = 0"); 
  end if;
	
  set @q = concat("select 
    p.id,
    p.name,
    p.description,
    p.orderable_type_id,
    c.id as cart_id,
	c.date_for_shop,
	c.ts_last_saved,
    si.quantity as quantity,
    si.iva_percent, 
    si.order_item_id,
    si.unit_price_stamp as unit_price,
    p.provider_id,  
    pv.name as provider_name,
    p.category_id,  
    iva.percent as iva_percent,
    rev.rev_tax_percent,
    um.unit
  from
  	aixada_cart c, 
  	aixada_shop_item si,
  	aixada_product p, 
  	aixada_provider pv, 
  	aixada_rev_tax_type rev, 
  	aixada_iva_type iva, 
  	aixada_unit_measure um
  where
  	",wherec,"
  	and si.cart_id = c.id
  	and si.product_id = p.id
  	and pv.id = p.provider_id
  	and rev.id = p.rev_tax_type_id
  	and iva.id = p.iva_percent_id
  	and um.id = p.unit_measure_shop_id
  order by p.provider_id, p.name; ");
  
  prepare st from @q;
  execute st;
  deallocate prepare st;
  
end|


/**
 * returns all aixada_order_items for 
 * a specific date and an uf
 * 
 */
drop procedure if exists get_order_cart|
create procedure get_order_cart(in the_date date, in the_uf_id int)
begin
  declare today date default date(sysdate());	
	
  select 
    p.id,
    p.name,
    p.description,
    p.orderable_type_id,
    oi.quantity as quantity,
    oi.notes,
    oi.favorite_cart_id,
    oi.order_id,
    oi.unit_price_stamp as unit_price,
    p.provider_id,  
    pv.name as provider_name,
    p.category_id, 
    po.closing_date, 
    datediff(po.closing_date, today) as time_left,
    if (oi.date_for_order = '1234-01-23', 'true', 'false') as preorder, 
    rev.rev_tax_percent,
    iva.percent as iva_percent,
    um.unit
  from 
  	aixada_order_item oi,
  	aixada_product_orderable_for_date po,
  	aixada_product p, 
  	aixada_provider pv, 
  	aixada_rev_tax_type rev, 
  	aixada_iva_type iva, 
  	aixada_unit_measure um
  where
  	oi.date_for_order in (the_date, '1234-01-23')
  	and oi.uf_id = the_uf_id
  	and oi.product_id = p.id
  	and po.product_id = oi.product_id
  	and po.date_for_order = oi.date_for_order
  	and pv.id = p.provider_id
  	and rev.id = p.rev_tax_type_id
  	and iva.id = p.iva_percent_id
  	and um.id = p.unit_measure_order_id
  	/** and orderable_type_id > 1  ... why do we need this? if items are in aixada_order_item, they are orderable **/
  order by p.provider_id, p.name; 
end|


delimiter ; 
