delimiter |


/**
 * just updates three fields for the order: payment reference, delivery refence, notes. 
 */
drop procedure if exists edit_order_detail_info|
create procedure edit_order_detail_info (in the_order_id int, in the_payment_ref varchar(255), in the_delivery_ref varchar(255), in the_notes varchar(255))
begin
	update
		aixada_order
	set
		delivery_ref = the_delivery_ref,
		payment_ref = the_payment_ref,
		notes = the_notes
	where
		id = the_order_id; 

end|


/**
 * delivers detailed info about an order: provider stuff, resposible uf stuff, etc. 
 * usually called from manage order detail view. 
 */
drop procedure if exists get_detailed_order_info|
create procedure get_detailed_order_info (in the_order_id int, in the_provider_id int, in the_date date)
begin
	
	declare delivered_total decimal(10,2) default 0;
	declare validated_income decimal(10,2) default 0;
	
	-- if there have been revisions, calc the new order total -- 
	set delivered_total = 		
		(select
			sum(ots.unit_price_stamp * ots.quantity)
		from
			aixada_order_to_shop ots
		where 
			ots.arrived = 1
			and ots.order_id = the_order_id);
	
	-- show how much of the order has been validated as uf carts. if people pay this is real income --
	set validated_income = 
		(select
			sum(si.unit_price_stamp * si.quantity)
		from
			aixada_cart c,
			aixada_shop_item si,
			aixada_order_item oi
		where
			oi.order_id = the_order_id
			and oi.id = si.order_item_id
			and si.cart_id = c.id
			and c.ts_validated > 0);
	
	if (the_order_id > 0) then 
	
		select 
			o.id as order_id,
			o.date_for_order,
			o.ts_sent_off, 
			o.date_for_shop,
			o.total,
			delivered_total,
			validated_income,
			o.notes as order_notes, 
			o.revision_status,
			o.delivery_ref,
			o.payment_ref,
			pv.*,
			uf.id as uf_id,
			uf.name as uf_name
		from
			aixada_order o,
			aixada_provider pv, 
			aixada_uf uf
		where
			o.id = the_order_id
			and o.provider_id = pv.id
			and pv.responsible_uf_id = uf.id;
			
	-- if we have no order_id, the whole info associated with the order is not available yet. --
	else 
		select 
			0 as order_id,
			the_date as date_for_order,
			0 as ts_sent_off, 
			0 as date_for_shop,
			0 as total,
			0 as delivered_total,
			0 as validated_income,
			0 as order_notes, 
			0 as revision_status,
			0 as delivery_ref,
			0 as payment_ref,
			pv.*,
			uf.id as uf_id,
			uf.name as uf_name
		from
			aixada_provider pv, 
			aixada_uf uf
		where
			pv.id = the_provider_id
			and pv.responsible_uf_id = uf.id;
	
	end if; 
end|
	


/**
 *	Returns the list or products that have been ordered. No quantities are returned at this point.   
 *  This query is used to construct the basic report table on orders. 
 *  Either requires an order_id OR a provider and date_for_order
 */
drop procedure if exists get_ordered_products_list|
create procedure get_ordered_products_list (in the_order_id int, in the_provider_id int, in the_date date)
begin
	
	if (the_order_id > 0) then
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
	else 
		select distinct
			p.id, 
			p.name, 
			um.unit
		from 
			aixada_order_item oi,
			aixada_product p,
			aixada_unit_measure um
		where
			oi.date_for_order = the_date
			and oi.product_id = p.id
			and p.provider_id = the_provider_id
			and p.unit_measure_order_id = um.id
		order by
			p.name;
		
	end if; 
end|



/**
 * Returns for a given order_id, all products, and ordered quanties per uf. Order_id can be replaced
 * by date_for_order and provider_id.  
 * Needed e.g. for revise order tables. 
 * If order info is currently edited, the info comes from aixada_order_to_shop, otherwise
 * directly from aixada_order_item.
 */
drop procedure if exists get_order_item_detail|
create procedure get_order_item_detail (in the_order_id int, in the_uf_id int, in the_provider_id int, in the_date_for_order date, in the_product_id int)
begin
	
	declare edited boolean default is_under_revision(the_order_id);
	declare wherec varchar(255) default ""; 
	
	-- filter for ufs
	if (the_uf_id > 0 and edited is true) then
		set wherec = concat(" and ots.uf_id=", the_uf_id);
	elseif (the_uf_id > 0 and edited is false) then
		set wherec = concat(" and oi.uf_id=", the_uf_id);
	end if;
	
	-- filter for product_id -- 
	if (the_product_id > 0 and edited is true) then
		set wherec = concat(wherec, " and ots.product_id=", the_product_id);
	elseif (the_product_id > 0 and edited is false ) then
		set wherec = concat(wherec, " and oi.product_id = p.id and oi.product_id=", the_product_id);
	elseif (edited is false) then
		set wherec = concat(wherec, " and oi.product_id = p.id");
	end if; 
	
	-- if the order items are edited, retrieve them from aixada_order_to_shop--
	if (edited is true) then 
		set @q = concat("select 
				ots.*
			from 
				aixada_order_to_shop ots
			where
				ots.order_id = ",the_order_id,"
				",wherec,"
			order by
				product_id;");
 
	-- otherwise get them from the order_item table, depending on the params available 	
	else 
		
		if (the_order_id > 0) then		
			set @q = concat("select 
					oi.*,
					p.name, 
					p.provider_id,
					p.orderable_type_id,
					1 as arrived, 
					0 as revised, 
					si.quantity as shop_quantity
				from
					aixada_product p,
					aixada_order_item oi 
				left join 
					aixada_shop_item si
				on 
					oi.id = si.order_item_id
				where
					oi.order_id = ",the_order_id," 
					",wherec,"
				order by 
					oi.product_id;");
					
		
		elseif (the_provider_id > 0 and the_date_for_order > 0) then
			set @q = concat("select
					oi.*,
					p.name,
					p.provider_id,
					p.orderable_type_id,
					1 as arrived, 
					0 as revised,
					si.quantity as shop_quantity
				from
					aixada_product p,
					aixada_order_item oi
				left join 
					aixada_shop_item si
				on 
					oi.id = si.order_item_id 	
				where
					oi.date_for_order = '",the_date_for_order,"'
					and p.provider_id = ",the_provider_id,"
					", wherec ,"
				order by
					oi.product_id;");
			
		end if;
		
	end if;
	
	prepare st from @q;
  	execute st;
  	deallocate prepare st;
  	set @q = ""; 
	
end |


/*
drop procedure if exists sum_product_quantity|
create procedure sum_product_quantity (in the_order_id int, in the_product_id int)
begin
	
	select
		sum(oi.quantity) as ordered_total_quantity
	from
		aixada_order_item oi
	where
		oi.order_id = the_order_id
		and oi.product_id = the_product_id; 
end|*/


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
 * set the revision status flag for each order. 
 * 	   -1 : transfer from aixada.sql < 2.5 where reference from shop_item to order_item is not possible
 * 		1 : default value. Finalized, send off to provider 
 * 		2 : arrived and items have been revised: everything is complete. This is set automatically once items have been copied from order to shop. 
 * 		3 : posponed. Did not arrive for the originally ordered date but could arrive in the near future
 * 		4 : canceled. Did not arrive for the originally ordered date and we are pretty sure that it will never arrive
 * 		5 : arrived and revised but with changes in quantities. Automaticall set in move_order_to_shop
 */
drop procedure if exists set_order_status|
create procedure set_order_status (in the_order_id int, in the_status int)
begin
	update
		aixada_order
	set
		revision_status = the_status
	where
		id = the_order_id; 
end|



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
reads sql data
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
	declare the_date_for_shop date; 
	declare qu_diff float(10,4) default 0;
	declare the_revision_status int default 2; 
	declare edited boolean default is_under_revision(the_order_id);
	declare uf_cursor cursor for 
		select distinct
			os.uf_id
		from
			aixada_order_to_shop os
		where 
			os.order_id = the_order_id;	
	
	declare continue handler for not found
		set done = 1; 
	declare exit handler for sqlexception rollback; 
	declare exit handler for sqlwarning rollback; 	
		
	
	start transaction;	
		
	-- check if order is under revision; if not (should be exception) then copy it to tmp move_order_to_shp table -- 
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
	
	
		
	open uf_cursor;	
	set done = 0; 

	read_loop: loop
		fetch uf_cursor into the_uf_id;
		if done then 
			leave read_loop; 
		end if;
		
		/** check if uf already has a non validated cart for the same shop date **/
		set the_cart_id = (
			select 
				c.id 
			from 
				aixada_cart c
			where
				c.uf_id = the_uf_id
				and c.ts_validated = 0
				and c.date_for_shop = the_shop_date); 
		
		
		/** create new cart if none exists for uf and date**/
		if (the_cart_id is null or the_cart_id = 0) then
			insert into 
				aixada_cart (uf_id, date_for_shop)
			values 
				(the_uf_id, the_shop_date);
			
			set the_cart_id = last_insert_id();
		end if; 
		
		/** copy the revised items into aixada_shop_item with its corresponding cart **/
		if (the_cart_id > 0) then
			replace into
				aixada_shop_item (cart_id, order_item_id, unit_price_stamp, product_id, quantity, iva_percent, rev_tax_percent)
			select
				the_cart_id, 
				os.order_item_id,
				os.unit_price_stamp,
				os.product_id,
				os.quantity, 
				iva_percent,
				rev_tax_percent
			from
				aixada_order_to_shop os
			where 
				os.order_id = the_order_id
				and os.uf_id = the_uf_id
				and os.arrived = 1;
				
			-- update the last saved info of the cart -- 
			update
				aixada_cart
			set
				ts_last_saved = now()
			where
				id = the_cart_id; 
			
		end if; 
	end loop;
	close uf_cursor;	
	
	
	/**checks if quantities have changed between original order and revised one by computing difference for each quantity row **/
	set qu_diff = ( select
						sum(abs(oi.quantity - ( select 
													os.quantity * os.arrived
												from 
													aixada_order_to_shop os
												where 
													os.order_id = the_order_id
													and os.order_item_id = oi.id)))
					from
						aixada_order_item oi
					where
						oi.order_id = the_order_id);
	
						
	/** if quantities have changed, revision status is 5; otherwise it is 2. **/
	if (qu_diff > 0 ) then
		set the_revision_status = 5;  
	end if; 					
	
	
	/**update the shop_date and revision status  in the order listing.**/
	update 
		aixada_order
	set 
		date_for_shop = the_shop_date,
		revision_status = the_revision_status
	where 
		id = the_order_id;
	
	
	/**remove tmp revison items**/
	/**delete from 
		aixada_order_to_shop
	where 
		order_id=the_order_id;**/ 
		
		
	commit;	
	
			
end |


/**
 * returns the order_item info and shop_item info reflecting eventual modifications
 * (products that did not arrive, quantities that changed). 
 */
drop procedure if exists diff_order_shop|
create procedure diff_order_shop (in the_order_id int, in the_uf_id int)
begin
	
	select 
		p.id as product_id,
		p.name, 
		p.orderable_type_id,
		oi.order_id, 
		oi.quantity,
		oi.notes,
		si.quantity as shop_quantity, 
		oi.unit_price_stamp as unit_price
	from 
		aixada_product p,
		aixada_order_item oi
	left join 
		aixada_shop_item si
	on 
		oi.id = si.order_item_id
	where 
		oi.order_id = the_order_id
		and oi.uf_id = the_uf_id
		and p.id = oi.product_id
	group by
		p.id;
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


/**
 * retrieves order status. expects either a date and provider_id or product_id, OR order_id. In case the order has
 * not yet finalized, no order_id will exist and the corresponding fields of aixada_order will return null. 
 */
drop procedure if exists get_order_status|
create procedure get_order_status (in the_date_for_order date, in the_provider_id int, in the_product_id int, in the_order_id int)
begin
	
	if the_order_id > 0 then
		select 
			o.*
		from 
			aixada_order o
		where
			o.id = the_order_id; 
			
	end if; 
	
	
	if the_product_id > 0 then
	
		set the_provider_id = 
			(select
				p.provider_id
			 from 
				aixada_product p
			where 
				p.id = the_product_id);
	end if; 
	
	if (the_provider_id > 0 and the_date_for_order > 0) then
		
		select
			oi.order_id,
			oi.date_for_order,
			p.provider_id,
			o.ts_sent_off,
			o.date_received,
			o.date_for_shop,
			o.total,
			o.revision_status
		from 
			aixada_product p,
			aixada_order_item oi
		left join
			aixada_order o
		on 
			oi.order_id = o.id
		where 
			p.provider_id = the_provider_id
			and p.id = oi.product_id
			and oi.date_for_order = the_date_for_order
		group by
			p.provider_id; 
			
	end if;

end |


/**
 * finalizes an order, i.e. no further changes in date, quantity can be made. a order_id is assigned 
 * and an entry in aixada_order made. 
 * while an order is open there does not exist an order_id because this would imply to query each time an item is added, if
 * an order for this provider/date already exists.  
 */
drop procedure if exists finalize_order|
create procedure finalize_order (in the_provider_id int, in the_date_for_order date)
begin
	
	declare order_total decimal(10,2) default 0;
	declare fix_closing_date date default date(sysdate());
	declare last_order_id int default 0; 
	
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
		aixada_order (provider_id, date_for_order, ts_sent_off, total)
	values
		(the_provider_id, the_date_for_order, now(), order_total);
	
	set last_order_id = last_insert_id();
		
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
		
	/** return last order entry **/
	select
		* 
	from 
		aixada_order
	where
		id = last_order_id; 
	
end |


/**
 *	If an order has been accidentally closed, it can be reopened. If internet connection is 
 *	active, this might be confusing with providers who receive several orders for the same 
 *	date...!! 
 */
drop procedure if exists reopen_order|
create procedure reopen_order (in the_order_id int)
begin

	declare order_date date default NULL;
	declare closing_date date default NULL;  

	/** save order date **/
	set order_date= 
		(select
			date_for_order
		from 
		 	aixada_order o
		where 
			o.id = the_order_id);


	/** calculate new closing date **/
	set closing_date = 
		(select 
			subdate(order_date, pv.offset_order_close)
		from
			aixada_order o,
			aixada_provider pv
		where
			o.provider_id = pv.id
		limit 1);


	/** update closing date for products of this order/provider **/
	update 
		aixada_product_orderable_for_date po,
		aixada_product p, 
		aixada_order o,
		aixada_provider pv 
	set
		po.closing_date = closing_date
	where
		o.id = the_order_id
		and po.date_for_order = o.date_for_order
		and pv.id = o.provider_id
		and p.provider_id = pv.id
		and p.id = po.product_id;


	/** reset order_id of order_items to NULL **/
	update 
		aixada_order_item oi
	set 
		oi.order_id = NULL
	where
		oi.order_id = the_order_id;


	/** delete order **/
	delete from 
		aixada_order_to_shop
	where
		order_id = the_order_id;

	delete from 
		aixada_order
	where
		id = the_order_id; 


end|


/**
 * resets the revision process for an order. This means that its revision_status is set to 1 (send off)
 * and that already distributed items are delted from shop carts (only if the cart is not yet validated). 
 */
drop procedure if exists reset_order_revision|
create procedure reset_order_revision (in the_order_id int)
begin
	/** TODO check that only non-validated items can be deleted **/

	/** delete distributed shop items of this cart **/
	delete from
		aixada_shop_item
	where
		order_item_id in 
		   (select
				id
			from
				aixada_order_item
			where 
				order_id = the_order_id);
	
	/** reset the shop_date and revision status for this order**/
	update 
		aixada_order
	set 
		date_for_shop = null,
		revision_status = 1
	where 
		id = the_order_id;
	
	
	/**make sure that tmp revison items have been deleted**/
	delete from 
		aixada_order_to_shop
	where 
		order_id=the_order_id; 
end |


/**
 * returns all orders for all providers within a certain date range.
 * also provides info about status of order and order_items: if available for sale, validate. 
 * revision_filter selects only those entries with a specific revision_status value (sent, cancel, postponed...)
 */
drop procedure if exists get_orders_listing|
create procedure get_orders_listing(in from_date date, in to_date date, in the_uf_id int, in revision_filter int, in the_limit varchar(255))
begin

	declare today date default date(sysdate()); 
	declare outer_wherec varchar(255) default "";
    declare totalc varchar(255) default "";
    declare filter_wherec varchar(255) default "";
    declare set_limit varchar(255) default ""; 
    
    if (the_uf_id > 0) then
    	set outer_wherec = 	concat("oi.uf_id = ", the_uf_id ," and");
    	set totalc = 		concat("(select
	    								round(sum(ois.quantity * ois.unit_price_stamp),2)
	  								from 
	  									aixada_order_item ois
	  								where
	  									ois.order_id = oi.order_id
										and ois.uf_id =",the_uf_id,")");
	else 
		set totalc = "o.total "; 
    end if; 
    
    -- filter according to revision_status --
    if (revision_filter > 0) then
    	set filter_wherec = concat("and o.revision_status = ", revision_filter);
    end if; 
    
    -- set a limit?
    if (the_limit <> "") then
    	set set_limit = concat("limit ", the_limit);
    end if; 

	set @q = concat("select distinct
		o.id,
		o.ts_sent_off,
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
		",filter_wherec,"
	order by 
		oi.date_for_order desc, o.id desc 
		",set_limit,";");
		
	prepare st from @q;
  	execute st;
  	deallocate prepare st;
end |



/**
 * converts a preorder (which have no order date but are kept open until a certain quantity has been reached)
 * into an order which is supposed to arrive at the specified date. 
 * converting also sets the order status to "sent off to provider" and assigns an order id.  
 */
drop procedure if exists convert_preorder|
create procedure convert_preorder(in the_provider_id int, in the_date_for_order date)
begin
	
	
	start transaction;
	
	-- insert first entry in aixada_products_orderable_for_date -- 
	replace into
		aixada_product_orderable_for_date (date_for_order, product_id, closing_date)
	select
		the_date_for_order, p.id, now()
	from
		aixada_product p,
		aixada_order_item oi
	where
		p.provider_id = the_provider_id
		and oi.product_id = p.id
		and oi.date_for_order = '1234-01-23'
	group by
		p.id;
		
		
	-- set the new date_for_order on ordered items-- 
	update 
		aixada_order_item oi,
		aixada_product p
	set 
		oi.date_for_order = the_date_for_order
	where
		oi.date_for_order = '1234-01-23'
		and oi.product_id = p.id
		and p.provider_id = the_provider_id; 
		
		
	-- delete the preorder date from products_orderable_for_date -- 
	delete
		po
	from
		aixada_product_orderable_for_date po,
		aixada_product p
	where
		po.date_for_order = '1234-01-23'
		and po.product_id = p.id
		and p.provider_id = the_provider_id; 
		
	commit;
	
	-- finalize it -- 
	call finalize_order(the_provider_id, the_date_for_order);
	
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



delimiter ;
