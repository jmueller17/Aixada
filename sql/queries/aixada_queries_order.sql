delimiter |



/**
 *	Returns the list or products that have been ordered. No quantities are returned at this point.   
 *  This query is used to construct the basic report table on orders. 
 */
drop procedure if exists get_ordered_products_list|
create procedure get_ordered_products_list (in the_order_id int)
begin
	
	select distinct
		p.id, 
		p.name
	from 
		aixada_order_item oi,
		aixada_product p
	where
		oi.order_id = the_order_id
		and oi.product_id = p.id
	order by
		p.name;
	
end|


/**
 * modifies an order item quantity. This is needed for revising orders and adjusting the quantities 
 * for each item and uf. 
 */
drop procedure if exists modify_order_item_detail|
create procedure modify_order_item_detail (in the_order_id int, in the_product_id int, in the_uf_id int,  in the_quantity float(10,4), in did_arrive int)
begin

	declare is_edited int default 0; 
	
	-- check if this order has been compied to the temp table aixada_order_to_shop --
	set is_edited = (select
		count(order_id)
	from 
		aixada_order_to_shop
	where 
		order_id = the_order_id);
		
		
	-- if it is not edited, then copy the order from aixada_order_item --
	if is_edited = 0 then
		insert into
			aixada_order_to_shop (order_item_id, uf_id, order_id, unit_price_stamp, product_id, quantity)
		select
			oi.id, 
			oi.uf_id,
			oi.order_id,
			oi.unit_price_stamp,
			oi.product_id,
			oi.quantity
		from
			aixada_order_item oi
		where
			oi.order_id = the_order_id; 
	end if; 
		
	
	if did_arrive = null then
		-- update quantity --
		update
			aixada_order_to_shop os
		set
			os.quantity = the_quantity
		where
			os.product_id = the_product_id
			and os.order_id = the_order_id
			and os.uf_id = the_uf_id; 
	else 
		-- de-/activate entire product row
		update 
			aixada_order_to_shop os
		set 
			os.has_arrived = did_arrive
		where
			os.product_id = the_product_id
			and os.order_id = the_order_id;
	end if;
end |


/**
 * returns for a given order_id, all products, and ordered quanties per uf. 
 * need for revise order tables 
 */
drop procedure if exists get_order_item_detail|
create procedure get_order_item_detail (in the_order_id int)
begin
	
	select 
		*
	from
		aixada_order_item oi
	where 
		oi.order_id = the_order_id;
		
end |



/**
 * modify the closing date of an order. The closing date is calculated on
 * a default basis for each provider. However, once an order date exists for a 
 * provider, the closing date can be modified
 */
drop procedure if exists modify_order_closing_date|
create procedure modify_order_closing_date (in the_provider_id int, in the_order_date date, in the_closing_date date)
begin
	
	update 
		aixada_product_orderable_for_date po,
		aixada_product p,
		aixada_provider pv
	set 
		po.closing_date = the_closing_date
	where
		po.date_for_order = the_order_date
		and po.product_id = p.id
		and p.provider_id = the_provider_id;
		
end |



/**
 * returns all orders for all providers within a certain date range
 */
drop procedure if exists get_orders_listing|
create procedure get_orders_listing(in from_date date, in to_date date, in the_limit int)
begin
	
	declare today date default date(sysdate()); 
	
	/** open orders, that have no order_id yet **/
	select distinct
		o.*,
		oi.date_for_order, 
		pv.name as provider_name,
		po.closing_date,
		get_order_total(oi.order_id) as order_total, /**quite intensive... **/
		datediff(po.closing_date, today) as time_left
	from 
		aixada_provider pv,
		aixada_product p,
		aixada_product_orderable_for_date po,
		aixada_order_item oi left join 
		aixada_order o on oi.order_id = o.id
	where
		oi.date_for_order >= from_date
		and oi.date_for_order <= to_date
		and oi.product_id = p.id
		and p.provider_id = pv.id
		and oi.date_for_order = po.date_for_order
		and po.product_id = p.id
	order by 
		oi.date_for_order desc
	limit 
		the_limit;
	
end |




drop function if exists get_order_total|
create function get_order_total(the_order_id int)
returns decimal(10,2)
begin
	
	declare order_total decimal(10,2) default 0.00; 
	
	/** needs iva included ?? **/
	set order_total = 
	(select
    	convert(sum(oi.quantity * oi.unit_price_stamp), decimal(10,2))
  	from 
  		aixada_order_item oi
  	where
  		oi.order_id = the_order_id);
  		
  	return order_total;
end|





drop procedure if exists activate_preorder_products|
create procedure activate_preorder_products(in the_date date, in product_id_list varchar(255))
begin
  set @q = 
  concat("update aixada_order_item
          set date_for_order = '", the_date,
         "' where date_for_order = '1234-01-23'
	    and product_id in ", product_id_list, ";");
  prepare st from @q;
  execute st;
  deallocate prepare st;  
end|


drop procedure if exists deactivate_preorder_products|
create procedure deactivate_preorder_products(in the_date date, in product_id_list varchar(255))
begin
  set @q = 
  concat("update aixada_order_item 
          set date_for_order = '1234-01-23' 
          where date_for_order = '", the_date,
	   "' and product_id in ", product_id_list, ";");
  prepare st from @q;
  execute st;
  deallocate prepare st;  
end|


drop procedure if exists list_preorder_providers|
create procedure list_preorder_providers()
begin
   select distinct pv.id, pv.name
   from aixada_product p
   left join aixada_provider pv
   on p.provider_id = pv.id
   left join aixada_order_item i
   on p.id = i.product_id
   where p.orderable_type_id = 4 
     and i.date_for_order = '1234-01-23'
   order by pv.name;
end|

drop procedure if exists list_preorder_products|
create procedure list_preorder_products(in prov_id int)
begin
   select 
        p.id, 
        p.name, 
        p.description,
        sum(i.quantity) as total
   from aixada_product p
   left join aixada_order_item i
   on p.id = i.product_id
   where p.provider_id = prov_id
   and p.orderable_type_id = 4
   and i.date_for_order = '1234-01-23'
   group by p.id;
end|



/**
 * Convert ordered items to shop items
 */
drop procedure if exists convert_order_to_shop|
create procedure convert_order_to_shop(IN uf int, IN order_date date)
begin
  replace into aixada_shop_item (
    uf_id, date_for_shop, product_id, quantity
  ) select i.uf_id, i.date_for_order, i.product_id, i.quantity
    from aixada_order_item i
    where date_for_order = order_date 
      and uf_id = uf;
end|





/**
 *  Move all orders from from_date to to_date.
 *  In case an order already exists at to_date, the
 *  quantity(to_date) is updated with 
 *  max( quantity(from_date), quantity(to_date) ) .
 * 
 * 
 *  still necessary???
 */

drop procedure if exists move_all_orders|
create procedure move_all_orders(in from_date date, in to_date date)
begin
  /* Start with orders */
  /* first, update existing entries at to_date with the larger quantity */
  update aixada_order_item i1,
    ( select uf_id, product_id, quantity from aixada_order_item
      where date_for_order = from_date ) i2
  set i1.quantity = if(i1.quantity > i2.quantity, i1.quantity, i2.quantity)
  where i1.product_id = i2.product_id
  and i1.uf_id = i2.uf_id
  and i1.date_for_order = to_date;

  /* Then, insert new products into to_date without clobbering what's already there */
  insert ignore into aixada_order_item (
     date_for_order, uf_id, product_id, quantity, ts_ordered      
  ) select * from 
    (select to_date, uf_id, product_id, quantity, ts_ordered      
    from aixada_order_item 
    where date_for_order = from_date ) i1;

  /* ... and remove old orders */
  delete from aixada_order_item
  where date_for_order = from_date;


  /* Then, _almost_ the same code with shop items; the difference is the 
     clause "and ts_validated = 0".
     Ugly but apparently necessary, since mysql doesn't permit variable table names */

  /* first, update existing entries at to_date with the larger quantity */
  update aixada_shop_item i1,
    ( select uf_id, product_id, quantity from aixada_shop_item
      where date_for_shop = from_date ) i2
  set i1.quantity = if(i1.quantity > i2.quantity, i1.quantity, i2.quantity)
  where i1.product_id = i2.product_id
  and i1.uf_id = i2.uf_id
  and i1.date_for_shop = to_date;

  /* Then, insert new products into to_date without clobbering what's already there */
  insert ignore into aixada_shop_item (
     date_for_shop, uf_id, product_id, quantity, ts_validated
  ) select distinct * from 
    (select to_date, uf_id, product_id, quantity, ts_validated    
    from aixada_shop_item 
    where date_for_shop = from_date 
    and ts_validated = 0) i1;

  /* ... and remove old shops */
  delete from aixada_shop_item
  where date_for_shop = from_date
  and ts_validated = 0;


  /* Finally, activate the products for the new day */
  delete from aixada_product_orderable_for_date 
  where date_for_order = to_date 
    and product_id in 
        (select distinct product_id 
           from aixada_shop_item i
           where i.date_for_shop = to_date);

  insert into aixada_product_orderable_for_date (
     date_for_order, product_id 
  ) select distinct to_date, i.product_id 
           from aixada_shop_item i
           where i.date_for_shop = to_date;
end|




delimiter ;
