delimiter |


/**
 * returns all orderable dates, irrespective if they have ordered items or not. 
 * this correspond basically to the entries in aixada_order_dates.
 */

drop procedure if exists get_orderable_dates|
create procedure get_orderable_dates(in from_date date, in the_limit int)
begin
	declare from_date_onward date default from_date;
	
  	if from_date = 0 then 
  		set from_date_onward = date(sysdate()); 
  	end if;
		
	select distinct
		po.date_for_order
	from 
		aixada_product_orderable_for_date po
	where
		po.date_for_order > from_date_onward
	limit the_limit;
		
end|




/**
 * returns all those dates that are orderable but for which 
 * there are no items ordered yet. 
 */
/*
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
*/

  
/**
 *  returns all those dates for which there are ordered items
 */
/*
drop procedure if exists get_nonempty_orderable_dates|
create procedure get_nonempty_orderable_dates()
begin
	select distinct date_for_order 
	from aixada_order_item
	where date_for_order >= date(sysdate());
end|
*/







/*
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
*/

delimiter ;