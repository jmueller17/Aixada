delimiter |


/**
 * given a provider_id and a certain day, make the associated orderable products of this provider
 * orderable every X daysteps during nr_weeks into the future. 
 */
drop procedure if exists repeat_orderable_day_provider|
create procedure repeat_orderable_day_provider(in the_provider_id int, in from_date date, in daysteps int, in nr_weeks int)
begin
	
	declare done int default 0;
	declare the_product_id int; 
	
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
	
	open pcursor;
	set done = 0; 
	set the_product_id = 0; 
	
	read_loop: loop
		fetch pcursor into the_product_id;
		if done then 
			leave read_loop; 
		end if;
		if the_product_id > 0 then 
			call repeat_orderable_product(the_product_id, from_date, daysteps, nr_weeks);
		end if; 
	end loop;
		
	close pcursor; 
end|

/**
 * sets the given product repeatedly orderable starting from the current date, advancing every time X daysteps 
 * during nr_weeks into the future
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
 * procedure that deletes all orderable product entries from the current date onwards. 
 * This works on a "day" basis. For a given provider_id, we check all its products that are orderable
 * for a given weekday and then delete all the corresponding dates for this weekday
 */
drop procedure if exists delete_orderable_products|
create procedure delete_orderable_products(in the_provider_id int, in from_date date)
begin
	
	delete 
		aixada_product_orderable_for_date
	from 
		aixada_product,
		aixada_product_orderable_for_date
	where 
		date_format(aixada_product_orderable_for_date.date_for_order, '%W') = date_format(from_date, '%W')
		and aixada_product_orderable_for_date.product_id = aixada_product.id
		and aixada_product.provider_id = the_provider_id; 
end|



/**
 * returns all providers that have items orderable: "sometimes orderable" or "always orderable"
 */
drop procedure if exists list_all_providers_short|
create procedure list_all_providers_short()
begin
  select distinct 
     pv.id, 
     pv.name
  from aixada_provider pv
  left join aixada_product p 
  on pv.id = p.provider_id 
  where  
    pv.active = 1
    and p.orderable_type_id in (2,3)
  order by pv.name;
end|



/**
 * returns all products that have been marked "orderable" for a given provider within a given date range
 * This corresponds basically to the entries in aixada_products_orderable_for_date
 */
drop procedure if exists get_orderable_products_for_dates|
create procedure get_orderable_products_for_dates(in fromDate date, in toDate date, in the_provider_id int)
begin
	select
		po.product_id,
		po.date_for_order
	from 
		aixada_product_orderable_for_date po,
		aixada_product pr
	where 
		pr.id = po.product_id
		and pr.provider_id = the_provider_id
		and po.date_for_order >= fromDate
		and po.date_for_order <= toDate;
end|


/**
 * activates or deactives a given product for a given date depending on its current status. If the product is active
 * for the given date, it will be deactivate and vice versa. 
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
 * returns all products for a given provider that are marked as either "always orderable" or "sometimes orderable" 
 * active or not active. 
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
 * sets the active status of a product. this is usually set automatically within the table_manapge jqgrid stuff
 * but can also be manipulated when the user sets the orderable status of the product
 */
drop procedure if exists change_active_status_product|
create procedure change_active_status_product (in the_active_state boolean, in the_product_id int)
begin
	update 
		aixada_product
	set
		active = the_active_state
	where
		id = the_product_id;
		
	if the_active_state = 0 then
		delete from aixada_product_orderable_for_date
		where 
			product_id = the_product_id;
	end if;
end|













drop procedure if exists list_all_ordered_providers_short|
create procedure list_all_ordered_providers_short(in the_date date)
begin
  select distinct pv.id, pv.name 
  from aixada_provider pv 
  left join aixada_product p 
  on pv.id = p.provider_id 
  left join aixada_order_item i
  on p.id = i.product_id
  where p.active = 1 
    and pv.active = 1
    and i.date_for_order = the_date
  order by pv.name;
end|

/*
drop procedure if exists get_activated_products|
create procedure get_activated_products(in the_provider_id int, in the_date date)
begin
  select distinct
        p.id, 
        p.name,
        p.description
  from aixada_product p 
  left join aixada_product_orderable_for_date od on p.id=od.product_id 
  where p.provider_id = the_provider_id and 
        p.orderable_type_id = 2 and
        p.active = true and 
	od.date_for_order=the_date
  order by p.name, p.id;
end|

drop procedure if exists get_deactivated_products|
create procedure get_deactivated_products(in the_provider_id int, in the_date date)
begin
  select distinct
        p.id, 
        p.name,
        p.description 
  from aixada_product p
  where p.provider_id = the_provider_id and 
        p.orderable_type_id = 2 and
        p.active = true 
        and not exists
	  (select od.product_id from aixada_product_orderable_for_date od
	   where od.product_id = p.id and 
	         od.date_for_order = the_date)
  order by p.name, p.id;
end|
*/

drop procedure if exists get_arrived_products|
create procedure get_arrived_products(in the_provider_id int, in the_date date)
begin
  select distinct
        i.product_id, 
        p.name,
        p.description
  from aixada_shop_item i
  left join aixada_product p 
     on p.id = i.product_id 
  where i.date_for_shop = the_date
    and p.provider_id = the_provider_id and 
        p.active = true 
  order by p.name;
end|

drop procedure if exists get_not_arrived_products|
create procedure get_not_arrived_products(in the_provider_id int, in the_date date)
begin
  select distinct
        i.product_id, 
        p.name,
        p.description
  from aixada_order_item i
  left join aixada_product p 
     on p.id = i.product_id 
  where i.date_for_order = the_date
    and p.provider_id = the_provider_id 
    and p.active = true 
    and not exists (
       select si.product_id
       from aixada_shop_item si
       where si.date_for_shop = the_date
         and si.product_id = i.product_id
    )
  order by p.name;
end|


drop procedure if exists add_stock|
create procedure add_stock(in the_product_id int, in delta_amount decimal(10,4), in the_operator_id int, in the_description varchar(255))
begin
   start transaction;
   update aixada_product
   set stock_actual = stock_actual + delta_amount,
       delta_stock  = delta_stock + delta_amount /* delta_stock = stock_actual - stock_min */
   where id = the_product_id;

   insert into aixada_stock_movement (
     product_id, operator_id, amount_difference, description, resulting_amount
   ) select
     the_product_id,
     the_operator_id,
     delta_amount,
     the_description,
     p.stock_actual
     from aixada_product p
     where p.id = the_product_id;
   commit;
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