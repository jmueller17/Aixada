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
    c.id as cart_id,
    si.quantity as quantity,
    si.iva_percent, 
    si.order_item_id,
    si.unit_price_stamp as unit_price,
    p.provider_id,  
    pv.name as provider_name,
    p.category_id, 
    -- p.unit_price * (1 + si.iva_percent/100) as unit_price, 
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
  	c.uf_id = the_uf_id
  	and c.date_for_shop = the_date
  	and c.ts_validated = 0
  	and c.id = si.cart_id
  	and si.product_id = p.id
  	and pv.id = p.provider_id
  	and rev.id = p.rev_tax_type_id
  	and iva.id = p.iva_percent_id
  	and um.id = p.unit_measure_shop_id
  order by p.provider_id, p.name; 
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
    oi.quantity as quantity,
    oi.favorite_cart_id,
    oi.order_id,
    oi.unit_price_stamp as unit_price,
    p.provider_id,  
    pv.name as provider_name,
    p.category_id, 
    po.closing_date, 
    datediff(po.closing_date, today) as time_left,
    --p.unit_price * (1 + iva.percent/100) as unit_price, 
    if (p.orderable_type_id = 4 and oi.date_for_order = '1234-01-23', 'true', 'false') as preorder, 
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


/**
 * TODO
 * retrieves all favorite order carts for a given uf_id. An order cart exists if a aixada_cart(id) 
 * exists for aixada_order_items. 
 */
drop procedure if exists get_favorite_order_carts|
create procedure get_favorite_order_carts (in the_uf_id int)
begin
	
	select 
		c.id,
		c.name
	from
		aixada_cart c,
		aixada_order_item oi
	where 
		oi.uf_id = the_uf_id
		and oi.favorite_cart_id = c.id;
	
end |



/**
 * TODO
 * creates a favorite order cart. requires existing items in aixada_order_item and 
 * then creates an cart_id for it which then gets saved to each order_item. 
 */
drop procedure if exists make_favorite_order_cart|
create procedure make_favorite_order_cart (in the_name varchar(255), in the_uf_id int, in the_date date)
begin
	
end |


/**
 * TODO
 * delete a favorite order cart
 */
drop procedure if exists delete_favorite_order_cart|
create procedure delete_favorite_order_cart (in the_cart_id int)
begin
	
	/** delete from aixada_cart **/
	
	/** set oi.favorite_cart_id to null ... automatic with foreign key constraint **/
	
end |















delimiter ; 