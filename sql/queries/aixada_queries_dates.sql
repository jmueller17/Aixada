delimiter |


/**
 * returns list of open orders from today onwards until a 
 * certain date in the near future. This is used to get an overview
 * of which orders are upcoming and about to close. 
 */
drop procedure if exists get_upcoming_orders|
create procedure get_upcoming_orders (in until date)
begin
	
	declare today date default date(sysdate()); 
	
	select distinct
		po.date_for_order,
		pv.name as provider_name,
		po.closing_date,
		datediff(po.closing_date, today) as time_left
	from
		aixada_product_orderable_for_date po,
		aixada_provider pv,
		aixada_product p
	where
		po.closing_date >= today
		and po.date_for_order <= until
		and po.product_id = p.id
		and p.provider_id = pv.id
	order by 
		po.closing_date asc; 
end |



/**
 * returns dates that have unvalidated shopping carts. 
 */
drop procedure if exists dates_with_unvalidated_shop_carts|
create procedure dates_with_unvalidated_shop_carts ()
begin
  select distinct 
  	date_for_shop as date_for_validation
  from 
  	aixada_cart 
  where 
  	ts_validated = 0
  order by 
  	date_for_shop desc;
end|



delimiter ;
