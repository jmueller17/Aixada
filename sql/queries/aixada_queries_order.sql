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
		p.name, 
		um.unit
	from 
		aixada_order_item oi,
		aixada_product p,
		aixada_unit_measure um
	where
		oi.order_id = the_order_id
		and oi.product_id = p.id
		and p.unit_measure_order_id = um.id
	order by
		p.name;
	
end|



/**
 * returns for a given order_id, all products, and ordered quanties per uf. 
 * need for revise order tables. 
 * If order info is currently edited, the info comes from aixada_order_to_shop, otherwise
 * directly from aixada_order_item 
 */
drop procedure if exists get_order_item_detail|
create procedure get_order_item_detail (in the_order_id int)
begin
	
	declare edited boolean default is_under_revision(the_order_id);
	
	-- if the order items are edited, retrieve them from aixada_order_to_shop--
	if (edited is true) then 
		select 
			*
		from 
			aixada_order_to_shop
		where
			order_id = the_order_id
		order by
			product_id; 
	-- otherwise get them from the order_item table		
	else 
		select 
			oi.*,
			1 as arrived, 
			0 as revised
		from
			aixada_order_item oi
		where 
			oi.order_id = the_order_id
		order by
			oi.product_id;
	end if;
end |



/**
 * modifies an order item quantity. This is needed for revising orders and adjusting the quantities 
 * for each item and uf. operates with a sort of temporary table aixada_order_to_shop where
 * the whole revision process is stored. This table (and not aixada_order_item) will then be 
 * copied to aixada_shop_item
 */
drop procedure if exists modify_order_item_detail|
create procedure modify_order_item_detail (in the_order_id int, in the_product_id int, in the_uf_id int,  in the_quantity float(10,4))
begin
	
	declare edited boolean default is_under_revision(the_order_id);
	
	-- if not under revision, then copy the order from aixada_order_item --
	if (edited is not true) then
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
		
	
	if (the_uf_id > 0) then
		-- update quantity if uf_id is set--
		update
			aixada_order_to_shop os
		set
			os.quantity = the_quantity,
			os.revised = 1
		where
			os.product_id = the_product_id
			and os.order_id = the_order_id
			and os.uf_id = the_uf_id; 
	end if; 
end |


/**
 * the revision table stores if a given item has been revised and has arrived in general. 
 * These flags can be set here. 
 */
drop procedure if exists set_order_item_status|
create procedure set_order_item_status (in the_order_id int, in the_product_id int, in has_arrived boolean, in is_revised boolean)
begin
	
	declare edited boolean default is_under_revision(the_order_id);
	
	-- if not under revision, then copy the order from aixada_order_item --
	if (edited is not true) then
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
	
	update 
		aixada_order_to_shop os
	set 
		os.arrived = has_arrived,
		os.revised = is_revised
	where
		os.product_id = the_product_id
		and os.order_id = the_order_id;	
end|



/**
 * checks if a given order_id is edited, i.e. its entires exist
 * in aixada_order_to_shop
 */
drop function if exists is_under_revision|
create function is_under_revision(the_order_id int)
returns boolean
begin
	declare is_edited int default 0; 
	
	-- check if this order has been copied to the temp table aixada_order_to_shop --
	set is_edited = (select
		count(order_id)
	from 
		aixada_order_to_shop
	where 
		order_id = the_order_id);
	
	return if(is_edited, true, false);
end|

	

/**
 * converts an order into something shoppable, i.e. 
 * ordered items will appear in people's cart for the given date. 
 */
drop procedure if exists move_order_to_shop |
create procedure move_order_to_shop (in the_order_id int, in the_shop_date date)
begin

	declare done int default 0; 
	declare the_uf_id int default 0; 
	declare the_cart_id int default 0;
	declare the_validated int default 0; 
	declare uf_cursor cursor for 
		select distinct
			os.uf_id,
			c.id, 
			c.ts_validated
		from
			aixada_order_to_shop os
			left join aixada_cart c on
			os.uf_id = c.uf_id;
		
	declare continue handler for not found
		set done = 1; 
		
	open uf_cursor;	
	set done = 0; 

	read_loop: loop
		fetch uf_cursor into the_uf_id, the_cart_id, the_validated;
		if done then 
			leave read_loop; 
		end if;
		
		/** create new cart if none exists for uf and date**/
		if (the_cart_id is null) then
			insert into aixada_cart (uf_id, date_for_shop)
			values (the_uf_id, the_shop_date);
			
			set the_cart_id = last_insert_id();
		end if; 
		
		/** copy the revised items into shop_item **/
		if (the_cart_id > 0 and (the_validated = 0 or the_validated is null)) then
			replace into
				aixada_shop_item (cart_id, order_item_id, unit_price_stamp, product_id, quantity, iva_percent, rev_tax_percent)
			select
				the_cart_id, 
				os.order_item_id,
				os.unit_price_stamp,
				os.product_id,
				os.quantity, 
				p.iva_percent_id,
				p.rev_tax_type_id
			from
				aixada_order_to_shop os,
				aixada_product p
			where 
				os.order_id = the_order_id
				and p.id = os.product_id
				and os.uf_id = the_uf_id
				and os.arrived = 1;
		end if; 
	end loop;
	close uf_cursor;
	
	/**remove tmp revison items**/
	delete from 
		aixada_order_to_shop
	where 
		order_id=the_order_id; 
		
	/**update the shop_date in the order listing**/
	update aixada_order
	set date_for_shop = the_shop_date
	where id = the_order_id; 
	
end |


/**
 * determines if order_items of a given order have already been moved to aixada_shop_item and if they have been
 * validated. returns the nr of validate items. Accepts either order_id or cart_id
 */
drop procedure if exists get_validated_status|
create procedure get_validated_status(in the_order_id int, in the_cart_id int) 
begin
	
	if the_order_id > 0 then
		select 
			c.id as cart_id, 
			if (c.ts_validated>0, 1, 0) as validated
		from
			aixada_order_item oi,
			aixada_shop_item si, 
			aixada_cart c
		where 
			oi.order_id = the_order_id
			and oi.id = si.order_item_id
			and si.cart_id = c.id; 
				
	elseif the_cart_id > 0 then
		select
			id as cart_id,
			if (ts_validated>0, 1, 0) as validated 
		from 
			aixada_cart 
		where 
			id = the_cart_id; 
	end if; 
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


drop procedure if exists finalize_order|
create procedure finalize_order (in the_provider_id int, in the_date_for_order date)
begin
	
	declare order_total decimal(10,2) default 0;
	declare fix_closing_date date default date(sysdate());
	
	set fix_closing_date = date_sub(fix_closing_date, interval 1 day);
	
	/** calc the order total **/
	set order_total = 
		(select 
			sum(oi.quantity * oi.unit_price_stamp)
		 from 
		 	aixada_order_item oi,
		 	aixada_product p
		 where
		 	oi.date_for_order = the_date_for_order
		 	and oi.product_id = p.id
		 	and p.provider_id = the_provider_id);
	
	
	/** new order_id **/
	insert into
		aixada_order (provider_id, date_for_order, ts_send_off, total)
	values
		(the_provider_id, the_date_for_order, now(), order_total);
		
		
	/** set order id to order_items **/
	update 
		aixada_order_item oi,
		aixada_product p
	set 
		oi.order_id = last_insert_id()
	where
		oi.date_for_order = the_date_for_order
		and oi.product_id = p.id
		and p.provider_id = the_provider_id; 
		
	/** update closing date for this product **/
	update 
		aixada_product_orderable_for_date po,
		aixada_product p
	set
		po.closing_date = fix_closing_date
	where
		closing_date > fix_closing_date
		and po.date_for_order = the_date_for_order
		and p.id = po.product_id
		and p.provider_id = the_provider_id;
		
		/*select 
		closing_date 
	into	
		the_closing_date
	from 
		aixada_product_orderable_for_date po,
		aixada_product p
	where
		po.date_for_order = the_date_for_order
		and p.id = po.product_id
		and p.provider_id = the_provider_id; */
	
		
	
	
end |



/**
 * returns all orders for all providers within a certain date range.
 * also provides info about status of order and order_items: if available for sale, validate. 
 */
drop procedure if exists get_orders_listing|
create procedure get_orders_listing(in from_date date, in to_date date, in the_uf_id int)
begin

	declare today date default date(sysdate()); 
	declare outer_wherec varchar(255) default "";
    declare totalc varchar(255) default "";
    
    if (the_uf_id > 0) then
    	set outer_wherec = 	concat("oi.uf_id = ", the_uf_id ," and");
    	set totalc = 		concat("(select
	    								sum(ois.quantity * ois.unit_price_stamp)
	  								from 
	  									aixada_order_item ois
	  								where
	  									ois.order_id = oi.order_id
										and ois.uf_id =",the_uf_id,")");
	else 
		set totalc = "o.total "; 
    end if; 

	set @q = concat("select distinct
		o.id,
		o.ts_send_off,
		o.date_for_shop,
		o.date_received,
		o.total,
		o.notes, 
		o.revision_status, 
		o.delivery_ref,
		o.payment_ref,
		oi.date_for_order, 
		pv.id as provider_id,
		pv.name as provider_name,
		po.closing_date,
		datediff(po.closing_date, '",today,"') as time_left,
		",totalc," as order_total
	from 
		aixada_provider pv,
		aixada_product p,
		aixada_product_orderable_for_date po,
		aixada_order_item oi left join 
		aixada_order o on oi.order_id = o.id
	where
		",outer_wherec,"
		oi.date_for_order >= '",from_date,"'
		and oi.date_for_order <= '",to_date,"'
		and oi.product_id = p.id
		and p.provider_id = pv.id
		and oi.date_for_order = po.date_for_order
		and po.product_id = p.id
	order by 
		oi.date_for_order desc;");
		
	prepare st from @q;
  	execute st;
  	deallocate prepare st;
end |








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
