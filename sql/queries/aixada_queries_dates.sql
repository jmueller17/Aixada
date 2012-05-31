delimiter |

/**
 * returns all those dates that are orderable but for which 
 * there are no items ordered yet. 
 */
drop procedure if exists get_empty_orderable_dates|
create procedure get_empty_orderable_dates()
begin
	select 
		od.orderable_date 
	from 
		aixada_orderable_dates od 
		left join aixada_order_item oi
	on 
		(od.orderable_date = oi.date_for_order)
	where 
		oi.date_for_order is null
		and od.orderable_date >= date(sysdate());
end|

  
/**
 *  returns all those dates for which there are ordered items
 */
drop procedure if exists get_nonempty_orderable_dates|
create procedure get_nonempty_orderable_dates()
begin
	select distinct date_for_order 
	from aixada_order_item
	where date_for_order >= date(sysdate());
end|



/**
 * returns all orderable dates, irrespective if they have ordered items or not. 
 * this correspond basically to the entries in aixada_order_dates
 */
drop procedure if exists get_all_orderable_dates|
create procedure get_all_orderable_dates()
begin
	select orderable_date 
	from aixada_orderable_dates
	where orderable_date >= date(sysdate());
end|


/**
 * returns all products that have been marked "orderable" for a given provider within a given date range
 * This corresponds basically to the entries in aixada_products_orderable_for_date
 */
drop procedure if exists get_orderable_products_for_dates|
create procedure get_orderable_products_for_dates(in fromDate date, in toDate date, in provider_id int)
begin
	select
		po.product_id,
		po.date_for_order
	from 
		aixada_product_orderable_for_date po,
		aixada_product pr
	where 
		pr.id = po.product_id
		and pr.provider_id = provider_id
		and po.date_for_order >= fromDate
		and po.date_for_order <= toDate;
end|


/**
 * activates or deactives a given product for a given date depending on its current status. If the product is active
 * for the given date, it will be deactivate and vice versa. 
 */
drop procedure if exists toggle_orderable_product|
create procedure toggle_orderable_product (in prod_id int, in the_date date)
begin
	declare isActive int;
	
	select 
		count(*) into isActive
	from 
		aixada_product_orderable_for_date po
	where 
		po.date_for_order = the_date
		and po.product_id = prod_id;
			
		
	if isActive > 0 then
		delete from 
			aixada_product_orderable_for_date
		where 
			product_id = prod_id
			and date_for_order = the_date;
	else 
		insert into aixada_product_orderable_for_date (
			product_id, 
			date_for_order, 
			closing_date)
		select 
			prod_id,
			the_date,
			subdate(the_date, pv.offset_order_close)
		from 
			aixada_product p,
			aixada_provider pv
		where 
			p.id = prod_id
			and p.provider_id = pv.id;
	end if;
	
end|

/**
 * 
 */
drop procedure if exists get_sometimes_orderable_dates|
create procedure get_sometimes_orderable_dates()
begin
	select 
		od.orderable_date
	from 
		aixada_orderable_dates od,
		aixada_product_orderable_for_date po,
		aixada_product pr
	where 
		od.orderable_date = po.date_for_order
		and po.product_id = pr.id
		and pr.orderable_type_id = 2
		and orderable_date >= date(sysdate());
end|


/**
 * insert into aixada_orderable_date new dates available for ordering.
 * and, activate always orderable products for this date. 
 */
drop procedure if exists add_orderable_date|
create procedure add_orderable_date(in the_date date)
begin
	replace into aixada_orderable_dates
	values (the_date);
	
	/* activate always orderable products*/
	insert into aixada_product_orderable_for_date (
		product_id, 
		date_for_order, 
		closing_date)
	select 
		p.id,
		the_date,
		subdate(the_date, pv.offset_order_close)
	from 
		aixada_product p,
		aixada_provider pv		
	where 
		p.orderable_type_id = 3
		and p.active = 1
		and p.provider_id = pv.id; 
end|


/**
 * delete dates from aixada_orderable_date. also delete always orderable products 
 * associated with this date 
 */
drop procedure if exists del_orderable_date|
create procedure del_orderable_date(in the_date date)
begin
	declare hasOrderItems int;
	
	select 
		count(*) into hasOrderItems
	from aixada_order_item
	where date_for_order = the_date; 
	
	if hasOrderItems = 0 then
		delete from aixada_orderable_dates 
		where orderable_date = the_date;
	end if;

end|






drop procedure if exists get_sales_dates|
create procedure get_sales_dates(in the_date date, in num int)
begin
  declare start_date date default the_date;
  if start_date = 0 then set start_date = date(sysdate()); end if;
  set @q = concat("select distinct date_for_order from aixada_order_item where date_for_order > '",
        start_date, "' limit ", num);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists get_next_equal_shop_date|
create procedure get_next_equal_shop_date()
begin
  select date_for_order 
  from aixada_product_orderable_for_date 
  where date_for_order >= date(sysdate()) 
  limit 1;
end|

drop procedure if exists shopping_dates|
create procedure shopping_dates()
begin
   declare d0 date default sysdate();
   declare d1 date default date_add(d0, interval 3 month);
   select shopping_date, available 
   from aixada_shopping_dates d
   where shopping_date between d0 and d1;
end|

delimiter ;