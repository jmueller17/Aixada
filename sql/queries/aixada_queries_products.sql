delimiter |


/**
 * given a provider_id and a certain day, make the associated orderable 
 * products of this provider orderable every X daysteps during nr_weeks into the future. 
 */
drop procedure if exists repeat_orderable_day_provider|
create procedure repeat_orderable_day_provider(in the_provider_id int, in from_date date, in daysteps int, in nr_weeks int)
begin
	
	declare done int default 0;
	declare the_product_id int; 
	declare weekday varchar(20) default date_format(from_date, '%W');
	declare today date default date(sysdate());

	
	declare pcursor cursor for 
	select distinct
		po.product_id
	from
		aixada_product_orderable_for_date po,
		aixada_product p
	where
		po.date_for_order = from_date
		and po.product_id = p.id
		and p.provider_id = the_provider_id;
	declare continue handler for not found
		set done = 1; 
	
		
	/*First, delete all existing orderable dates for this provider and day*/
	delete 
		po
	from 
		aixada_product p,
		aixada_product_orderable_for_date po
	where 
		po.date_for_order > from_date
		and p.provider_id = the_provider_id	
		and date_format(po.date_for_order, '%W') = weekday
		and po.product_id = p.id;

	/*get all  products for provider, and set the dates for each individually*/	
	open pcursor;
	set done = 0; 
	set the_product_id = 0; 
	
	read_loop: loop
		fetch pcursor into the_product_id;
		if done then 
			leave read_loop; 
		end if;
		if the_product_id > 0 then 
			/* create the date pattern for reach product */
			call repeat_orderable_product(the_product_id, from_date, daysteps, nr_weeks);
		end if; 
	end loop;
		
	close pcursor; 
end|



/**
 * sets the given product repeatedly orderable starting from the current date, 
 * advancing every time X daysteps  during nr_weeks into the future
 */
drop procedure if exists repeat_orderable_product|
create procedure repeat_orderable_product(in the_product_id int, in from_date date, in daysteps int, in nr_weeks int)
begin
	
	declare i int;
	declare next_date date; 
	set i=0;
	set next_date = date_add(from_date, interval daysteps day);

	while i < nr_weeks do
		insert into aixada_product_orderable_for_date (
			product_id, 
			date_for_order, 
			closing_date)
		select 
			the_product_id,
			next_date,
			subdate(next_date, pv.offset_order_close)
		from 
			aixada_product p,
			aixada_provider pv
		where 
			p.id = the_product_id
			and p.provider_id = pv.id;			
		
		set i = i+1; 
		set next_date = date_add(next_date, interval daysteps day);
	end while; 
end|


/**
 * activates or deactives a given product for a given date depending on its current status. 
 * If the product is active for the given date, it will be deactivated and vice versa.
 * If "repeat" is T then the product will be automatically de-/activated for all remaining (future)
 * dates.  
 */
drop procedure if exists toggle_orderable_product|
create procedure toggle_orderable_product (in the_product_id int, in the_date date, in doRepeat boolean)
begin
	
	declare done int default 0; 
	declare isActive int;
	declare dummy boolean; 

	declare the_other_dates date; 
	declare the_provider_id int default (select 
							provider_id 
						from 
							aixada_product 
						where 
							id=the_product_id);
	
	-- are there future dates where other products by this provider are active? -- 
	declare date_cursor cursor for 	
		select distinct
			po.date_for_order
		from
			aixada_product_orderable_for_date po
		where 
			product_id in (select 
							p.id 
						from 
							aixada_product p
						where 
							p.provider_id = the_provider_id
							and p.active = 1)
			and po.date_for_order > the_date; 
		
	declare continue handler for not found
		set done = 1; 
		
	-- declare exit handler for sqlexception rollback; --
	-- declare exit handler for sqlwarning rollback; 	--
	
	
	
	-- decide what to do: deactivate or activate -- 
	select 
		count(*) into isActive
	from 
		aixada_product_orderable_for_date po
	where 
		po.date_for_order = the_date
		and po.product_id = the_product_id;
	
	call write_toggle_to_db(isActive, the_date, the_product_id);
	
	-- do the same for all remaining dates... --
	if (doRepeat > 0) then	
		open date_cursor;	
		set done = 0; 
	
		read_loop: loop
			fetch date_cursor into the_other_dates;
			if done then 
				leave read_loop; 
			end if;
			
			call write_toggle_to_db(isActive, the_other_dates, the_product_id);
			
			
		end loop;
		close date_cursor;	
	end if; 
	
end|

/**
 * utility function to write the changes for the orderable products in the table
 * if is_active, the product for the given date will be deactivated and vice versa
 */
drop procedure if exists write_toggle_to_db|
create procedure write_toggle_to_db(in is_active int, in the_date date, in the_product_id int)
begin
	
	-- activate / deactivate the choosen cell
	if is_active > 0 then
		delete from 
			aixada_product_orderable_for_date
		where 
			product_id = the_product_id
			and date_for_order = the_date;
	else 
		replace into aixada_product_orderable_for_date (
			product_id, 
			date_for_order, 
			closing_date)
		select 
			the_product_id,
			the_date,
			subdate(the_date, pv.offset_order_close)
		from 
			aixada_product p,
			aixada_provider pv
		where 
			p.id = the_product_id
			and p.provider_id = pv.id;
	end if;	
	
end|


/**
 *  converts a product into preorderable. all available order dates will be deleted 
 *  and replaced by the fictive date  1234-01-23
 */
drop procedure if exists toggle_preorder_product|
create procedure toggle_preorder_product(in the_product_id int, in the_date date)
begin
	
	declare isPreorder int;
	declare today date default date(sysdate());
	
	
	select
		count(*) into isPreorder
	from 
		aixada_product_orderable_for_date po
	where
		po.date_for_order = '1234-01-23'
		and po.product_id = the_product_id;
	
	-- if is preorder convert back to normal order -- 
	if isPreorder > 0 then	
		
		delete from
			aixada_product_orderable_for_date
		where
			product_id = the_product_id
			and date_for_order = '1234-01-23';
		
	-- otherwise delete active dates and make it preorderable 		
	else 
	
	    delete
			po.*
		from
			aixada_product_orderable_for_date po
		left join
			aixada_order_item oi on
			po.date_for_order = oi.date_for_order
			and po.product_id = oi.product_id
		where
			po.date_for_order > today
			and po.product_id = the_product_id
			and oi.date_for_order is null;
		
		-- create preorder entry in table -- 
		insert into 
			aixada_product_orderable_for_date(product_id, date_for_order, closing_date)
		values
			(the_product_id, '1234-01-23', '9999-01-01');
			
		-- if the product is currently not active, make sure it is activated -- 
		update 
			aixada_product
		set
			active = 1
		where
			id = the_product_id; 
			
	end if; 	
end|


/**
 * By default only those dates in table aixada_product_orderable_for_date can be 
 * deactivated that have no ordered items associated. This procedure can delete
 * those dates where orders have been made, which implies to delete the associated items 
 * from order carts as well. 
 */
drop procedure if exists deactivate_locked_order_date|
create procedure deactivate_locked_order_date (in the_product_id int, in the_date date)
begin
	
	start transaction; 
	
	delete from
		aixada_order_item
	where
		product_id = the_product_id
		and date_for_order = the_date; 
		
	delete from
		aixada_product_orderable_for_date
	where
		product_id = the_product_id
		and date_for_order = the_date; 
		
	
	commit; 
	
end|


/**
 * sets the active status of a product. this is usually set automatically within the table_manapge jqgrid stuff
 * but can also be manipulated when the user sets the orderable status of the product.
 * If a product gets deactivated, this will also delete all entries from aixada_product_orderable_for_date that 
 * don't have any items ordered yet. 
 */
drop procedure if exists change_active_status_product|
create procedure change_active_status_product (in the_active_state boolean, in the_product_id int)
begin
	
	declare today date default date(sysdate());
	
	update 
		aixada_product
	set
		active = the_active_state
	where
		id = the_product_id;
		
	
	/** 
	 * product has been deactivated in general, i.e.: delete all those entries in po 
	 * that have no ordered items 
	 */	
	if the_active_state = 0 then		
			delete
				po.*
			from
				aixada_product_orderable_for_date po
			left join
				aixada_order_item oi on
				po.date_for_order = oi.date_for_order
				and po.product_id = oi.product_id
			where
				(po.date_for_order > today or po.date_for_order = '1234-01-23')
				and po.product_id = the_product_id
				and oi.date_for_order is null;	
	end if;
end|



/**
 * returns all products for a given provider that are marked as 
 * either "always orderable" or "sometimes orderable" or "preorder"
 * independent if they are active or not active. 
 */
drop procedure if exists get_type_orderable_products|
create procedure get_type_orderable_products (in the_provider_id int)
begin
	
	select
		p.id,
		p.name as name,
		pv.name as provider_name,
		p.active as is_active,
		(select 
			count(*)
		from 
			aixada_product_orderable_for_date po
		where
			po.product_id = p.id
			and po.date_for_order = '1234-01-23') as preorder
	from 
		aixada_provider pv,
		aixada_product p
	where 
		pv.id = the_provider_id
		and p.provider_id = pv.id
		and pv.active = 1
		and p.orderable_type_id in (2,3,4)
	order by is_active desc, name asc;
end|


/**
 * returns all products that have been marked "orderable" for a given provider within a given date range
 * This corresponds basically to the entries in aixada_products_orderable_for_date
 * It also calculates the remaining days before the order closes, the quantity of the ordered products and if the order has been 
 * finalized (send off to the provider). 
 * 
 * TODO: maybe the two sub-selects (especially the order_id) is not the most elegant and the query could be simplified?!!
 */
drop procedure if exists get_orderable_products_for_dates|
create procedure get_orderable_products_for_dates(in fromDate date, in toDate date, in the_provider_id int)
begin
	
	declare today date default date(sysdate()); 
	
	select
		po.product_id,
		po.date_for_order,
		po.closing_date,
		datediff(po.closing_date, today) as time_left,
		(select
			o.id
		 from
		 	aixada_order o,
		 	aixada_product pp
		 where
		 	o.provider_id = p.provider_id
		 	and pp.id = p.id
		 	and o.date_for_order = po.date_for_order) as order_id,

		(select 
			count(oi.id)
		 from 
		 	aixada_order_item oi 
		 where 
		 	p.id=oi.product_id
		 	and oi.date_for_order = po.date_for_order) as has_ordered_items
	from 
		aixada_product_orderable_for_date po,
		aixada_product p
	where 
		p.id = po.product_id
		and p.provider_id = the_provider_id
		and po.date_for_order >= fromDate
		and po.date_for_order <= toDate;
end|



/**
 *  returns all products (with details). This query is needed for the shop/order pages and its
 *  different mechanisms for searching: by provider, by category, or direct search. 
 *  As such this query shows available products for ordering or for purchase but does not handle any real 
 *  ordered or bought products. The search functionality is also called from the validate page. 
 * 
 *  If a provider_id is set, it returns the associated products for the provider. If date is set, then these
 *  are orderable products, otherwise stock. 
 * 
 * 	if category_id is set, it returns products by category. If date is set, these products by category are 
 *  orderable, otherwise stock.
 * 
 * 	if provider_id and category_id = 0 and the_like is set, then searches for product 
 * 
 *  Furthermore it is important to note that the price delivered includes IVA and Rev Tax!! There is no 
 *  need to calcuate this at a later point in time (upon validation for example). 
 */
drop procedure if exists get_products_detail|
create procedure get_products_detail(	in the_provider_id int, 
										in the_category_id int, 
										in the_like varchar(255),
										in the_date date,
										in include_inactive boolean,
										in the_product_id int)
begin
	
	declare today date default date(sysdate());
    declare wherec varchar(255);
    declare fromc varchar(255);
    declare fieldc varchar(255);
     
    
    /** show active products only or include inactive products as well **/
    set wherec = if(include_inactive=1,"","and p.active=1 and pv.active = 1");	
   
    
    /** no date provided we assume that we are shopping, i.e. all active products are shown stock + orderable **/
    if the_date = 0 then
    	set fieldc = "";
    	set fromc = "";
    	set wherec = 	concat(wherec, " and p.unit_measure_shop_id = u.id ");
    
    /** hack: date=-1 works to filter stock only products **/ 	
    elseif the_date = '1234-01-01' then 
    	set fieldc = "";
    	set fromc = "";
    	set wherec = concat(wherec, " and p.unit_measure_shop_id = u.id and (p.orderable_type_id = 1 or p.orderable_type_id = 4) ");
    
    /** otherwise search for products with orderable dates **/
    else 
    	set fieldc = concat(", datediff(po.closing_date, '",today,"') as time_left");
       	set fromc = 	"aixada_product_orderable_for_date po, ";
    	set wherec = 	concat(wherec, " and po.date_for_order = '",the_date,"' and po.product_id = p.id and p.unit_measure_order_id = u.id ");	
    end if;
     
    
    
    /** get a specific product **/
    if the_product_id > 0 then 
    	set wherec = concat(wherec, " and p.id = '", the_product_id, "' ");
    	
    /** get products by provider_id **/
    elseif the_provider_id > 0 then
		set wherec = concat(wherec, " and pv.id = '", the_provider_id, "' ");
    	
    /** get products by category_id **/
    elseif the_category_id > 0 then 
    	set fromc = concat(fromc, "aixada_product_category pc,");
    	set wherec = concat(wherec, " and pc.id = '", the_category_id, "' and p.category_id = pc.id ");
	
    /** search for product name **/
    elseif the_like != "" then
    	set wherec 	= concat(wherec, " and p.name LIKE '%", the_like,"%' ");
    	
    end if;
    
  
	set @q = concat("
	select
		p.*,
		round((p.unit_price * (1 + (iva.percent+t.rev_tax_percent)/100)),2) as unit_price, 
		pv.name as provider_name,	
		u.unit,
		iva.percent as iva_percent,
		t.rev_tax_percent
		",fieldc,"
	from
		",fromc,"
		aixada_product p,
		aixada_provider pv, 
		aixada_rev_tax_type t,
		aixada_iva_type iva,
		aixada_unit_measure u 
	where 
		pv.id = p.provider_id	
		",wherec,"
		and p.rev_tax_type_id = t.id
		and p.iva_percent_id = iva.id 
	order by p.name asc, p.id asc;");
	
	prepare st from @q;
  	execute st;
  	deallocate prepare st;
  
end|


/**
 * retrieves all products of given provider, irrespective of stock|orderable|accumulative
 * but filters for active/non-active products
 */
drop procedure if exists get_products_of_provider|
create procedure get_products_of_provider (in the_provider_id int, in the_active int)
begin
	
	select 
		p.id,
		p.name,
		p.description,
		p.category_id,
		p.stock_actual,
		p.active,
		if (p.orderable_type_id = 4, 'true', 'false') as preorder, 
		pv.name as provider_name,
		pv.id as provider_id
	from 
		aixada_product p, 
		aixada_provider pv
	where
		p.active = the_active
		and p.provider_id = the_provider_id
		and pv.id = the_provider_id
	order by
		p.id; 
end|



/**
 *  retrieves all products of type preorderable
 */
drop procedure if exists get_preorderable_products|
create procedure get_preorderable_products()
begin
   select 
        p.id, 
        p.name,
        p.description,
        pv.id as provider_id,
        pv.name as provider_name,
        u.unit,
   		round((p.unit_price * (1 + (iva.percent+r.rev_tax_percent)/100)),2) as unit_price,
		iva.percent as iva_percent,
   		r.rev_tax_percent,
        p.unit_price
        
   from 
	   	aixada_product p,
	   	aixada_provider pv,
		aixada_unit_measure u,
		aixada_rev_tax_type r,
		aixada_iva_type iva,
		aixada_product_orderable_for_date po
   where 
  		p.orderable_type_id in (2,3,4)
  		and po.date_for_order = '1234-01-23'
  		and po.product_id = p.id
  		and p.active = 1	
     	and pv.active = 1
  		and p.provider_id = pv.id
  		and p.rev_tax_type_id = r.id
  		and	p.unit_measure_order_id = u.id
		and p.iva_percent_id = iva.id 
  	order by p.id, p.name;
end|



/**
 * correct stock. this should be the exception since stock is normally added
 * and then sold which deduces automatically the correct amount. 
 * However, stock disappears.... somehow
 */
drop procedure if exists correct_stock|
create procedure correct_stock(in the_product_id int, in the_current_stock decimal(10,4), in the_operator_id int)
begin
	
	declare err_amount decimal(10,4);
	declare current_balance decimal(10,2) default 0.0;
	
	start transaction;
	
	-- what's the difference in order to calculate the loss; loss includes iva but not revTax -- 
	select
		(the_current_stock - p.stock_actual) * p.unit_price * (1 + iva.percent/100)
	into 
		err_amount
	from
		aixada_product p,
		aixada_iva_type iva
	where
		p.id = the_product_id
		and p.iva_percent_id = iva.id; 
			
		
	-- get the current balance from consum account -- 
	select 
  		balance 
  	into 
  		current_balance
  	from 
  		aixada_account
  	where 
  		account_id = -2
  	order by ts desc
  		limit 1; 
  		
  	-- register the loss in the bank-- 
  	insert into 
  		aixada_account (account_id, quantity, payment_method_id, description, operator_id, balance) 
  	select 
   		-2,
    	err_amount,
    	5,
    	concat('Stocked corrected for product #',the_product_id),
    	the_operator_id,
 		current_balance + err_amount;
 		
 	 
 		
 	-- reg the stock movement -- 	
 	insert into 
   		aixada_stock_movement (product_id, operator_id, amount_difference, description, resulting_amount) 
   	select
     	the_product_id,
     	the_operator_id,
     	the_current_stock - p.stock_actual,
     	concat('stock corrected.'),
     	the_current_stock
    from 
    	aixada_product p
    where 
    	p.id = the_product_id;
 		
 	-- update the product quantity to the new value -- 
 	update
 		aixada_product
 	set
 		stock_actual = the_current_stock
 	where
 		id = the_product_id;
    	
    	
    commit; 
	
end|



/**
 * add stock
 */
drop procedure if exists add_stock|
create procedure add_stock(	in the_product_id int, 
							in delta_amount decimal(10,4), 
							in the_operator_id int, 
							in the_description varchar(255))
begin
   	start transaction;
	   
   	update 
		aixada_product
	set 
		stock_actual = stock_actual + delta_amount,
	    delta_stock  = delta_stock + delta_amount /* delta_stock = stock_actual - stock_min */
	where 
		id = the_product_id;

   	insert into 
   		aixada_stock_movement (product_id, operator_id, amount_difference, description, resulting_amount) 
   	select
     	the_product_id,
     	the_operator_id,
     	delta_amount,
     	the_description,
     	p.stock_actual
    from 
    	aixada_product p
    where 
    	p.id = the_product_id;
   commit;
end|


/**
 * returns a list of all products with current_stock below min_stock
 */
drop procedure if exists products_below_min_stock|
create procedure products_below_min_stock()
begin
  select
        p.id,
        p.name as stock_item,
        pv.name as stock_provider,
        p.stock_actual,
        p.stock_min
  from 
  	aixada_product p use index(delta_stock), 
  	aixada_provider pv
  where 
 	p.provider_id = pv.id 	
  	and p.active = 1
    and p.orderable_type_id = 1
    and pv.active = 1
    and p.stock_actual < p.stock_min
  order by pv.name;
end|


/**
 * retrieves info about all stock movements of a given product
 * or all products. 
 */
drop procedure if exists stock_movements|
create procedure stock_movements(in the_product_id int, in the_limit varchar(255))
begin
  
	declare wherec varchar(255) default ''; 
	
	if (the_product_id > 0) then
		set wherec = concat('and sm.product_id = ', the_product_id);
	end if; 
	
	select 
		sm.*,
		mem.id as member_id,
		mem.name as member_name,
		p.name as product_name,
		calc_delta_price(sm.amount_difference, p.unit_price, iva.percent) as delta_price,
		um.unit
	from
		aixada_stock_movement sm,
		aixada_member mem,
		aixada_product p, 
		aixada_iva_type iva,
		aixada_unit_measure um
	where
		mem.id = sm.operator_id
		and p.id = sm.product_id
		and p.unit_measure_shop_id = um.id
		and p.iva_percent_id = iva.id
		and sm.product_id = the_product_id
	order by
		sm.ts desc, sm.product_id desc;  
		
end|


/**
 * calculates the accumulated loss of stock corrections
 */
drop function if exists calc_delta_price|
create function calc_delta_price(the_diff_amount decimal(10,4), the_unit_price decimal(10,2), the_iva_percent decimal(10,2))
returns decimal(10,2)
begin
	
	declare result decimal(10,2) default 0.00;
	
	if (the_diff_amount < 0) then
		set result = the_diff_amount  * the_unit_price *  (1 + the_iva_percent/100);
	end if;
		
	return result;
end|



delimiter ;
