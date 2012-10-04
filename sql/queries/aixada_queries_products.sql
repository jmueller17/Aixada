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
 * If the product is active for the given date, it will be deactivate and vice versa. 
 */
drop procedure if exists toggle_orderable_product|
create procedure toggle_orderable_product (in the_product_id int, in the_date date)
begin
	declare isActive int;
	
	select 
		count(*) into isActive
	from 
		aixada_product_orderable_for_date po
	where 
		po.date_for_order = the_date
		and po.product_id = the_product_id;
			
		
	if isActive > 0 then
		delete from 
			aixada_product_orderable_for_date
		where 
			product_id = the_product_id
			and date_for_order = the_date;
	else 
		insert into aixada_product_orderable_for_date (
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
			where
				po.date_for_order > today
				and po.product_id = the_product_id
				and oi.date_for_order is null;	
	end if;
end|



/**
 * returns all products for a given provider that are marked as 
 * either "always orderable" or "sometimes orderable" 
 * independent if they are active or not active. 
 */
drop procedure if exists get_type_orderable_products|
create procedure get_type_orderable_products (in the_provider_id int)
begin
	
	select
		p.id,
		p.name as name,
		pv.name as provider_name,
		p.active as is_active
	from 
		aixada_product p,
		aixada_provider pv 
	where 
		p.provider_id = the_provider_id
		and pv.id = the_provider_id
		and pv.active = 1
		and p.orderable_type_id in (2,3)
	order by is_active desc, name desc;
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
 *  different mechanismos for searching: by provider, by category, or direct search. 
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
										in the_date date)
begin
	
	declare today date default date(sysdate());
    declare wherec varchar(255);
    declare fromc varchar(255);
    declare fieldc varchar(255);
    
    /** no date provided we assume that we are looking for stock **/
    if the_date = 0 then
    	set fieldc = "";
    	set fromc = "";
    	set wherec = 	" and p.orderable_type_id = 1 and p.unit_measure_shop_id = u.id ";
    /** otherwise search for products with orderable dates **/
    else 
    	set fieldc = concat(", datediff(po.closing_date, '",today,"') as time_left");
       	set fromc = 	"aixada_product_orderable_for_date po, ";
    	set wherec = 	concat(" and po.date_for_order = '",the_date,"' and po.product_id = p.id and p.unit_measure_order_id = u.id ");	
    end if;
    
    
    
    /** get products by provider_id **/
    if the_provider_id > 0 then
		set wherec = concat(wherec, " and pv.id = '", the_provider_id, "' ");
    	
    /** get products by category_id **/
    elseif the_category_id > 0 then 
    	set fromc = concat(fromc, "aixada_product_category pc,");
    	set wherec = concat(wherec, " and pc.id = '", the_category_id, "' and p.category_id = pc.id ");
	
    /** search for product name **/
    elseif the_like != "" then
    	set wherec 	= concat (wherec, " and p.name LIKE '%", the_like,"%' ");
    end if;
    
  
	set @q = concat("
	select
		p.id,
		p.name,
		p.description,
		p.category_id,
		p.stock_actual,
		round((p.unit_price * (1 + iva.percent/100) * (1+t.rev_tax_percent/100)),2) as unit_price,
		if (p.orderable_type_id = 4, 'true', 'false') as preorder, 
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
		p.active = 1
		and pv.active = 1
		and pv.id = p.provider_id	
		",wherec,"
		and p.rev_tax_type_id = t.id
		and p.iva_percent_id = iva.id 
	order by p.id, p.name;");
	
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
   		round((p.unit_price * (1 + iva.percent/100) * (1+r.rev_tax_percent/100)),2) as unit_price,
		iva.percent as iva_percent,
   		r.rev_tax_percent,
        p.unit_price
        
   from 
	   	aixada_product p,
	   	aixada_provider pv,
		aixada_unit_measure u,
		aixada_rev_tax_type r,
		aixada_iva_type iva
   where 
  		p.orderable_type_id = 4
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
	
	-- what's the difference in order to calculate the loss -- 
	select
		(the_current_stock - p.stock_actual) * p.unit_price
	into 
		err_amount
	from
		aixada_product p
	where
		p.id = the_product_id; 
			
		
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
     	concat('stock corrected. Delta amount: ',err_amount, 'Euros'),
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



drop procedure if exists stock_movements|
create procedure stock_movements(in product_id int, in tmp_start_date date, in num_rows int)
begin
  declare start_date date default tmp_start_date;
  if start_date = 0 then set start_date = date_add(sysdate(), interval -3 month); end if;

  set @q = concat("select
    id, 
    operator_id,
    amount_difference, 
    description,
    resulting_amount,
    ts
    from aixada_stock_movement
    where ts >= '", start_date, "'
    order by ts limit ", num_rows);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

delimiter ;