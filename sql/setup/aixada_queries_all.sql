delimiter |




drop  procedure if exists account_exists|
create procedure account_exists(in the_account_id int)
begin
  select
    count(*)
  from 
    aixada_account
  where
    account_id = the_account_id;
end|


/**
 * Generic procedure to handle either deposit or withdrawal for the given account. 
 */
drop procedure if exists move_money|
create procedure move_money(in the_quantity decimal(10,2), 
                            in the_account_id int, 
                            in type_id int, 
                            in the_operator_id int, 
                            in the_description varchar(255),
                            in the_currency_id int)
begin

    declare current_balance decimal(10,2);
    declare new_balance decimal(10,2);
  
  -- get the current balance -- 
    select 
      balance 
    into 
      current_balance
    from 
      aixada_account
    where 
      account_id = the_account_id
    order by ts desc, id desc
    limit 1; 
  
    set new_balance = current_balance + the_quantity; 
    
    insert into 
      aixada_account (account_id, quantity, payment_method_id, description, operator_id, balance, currency_id) 
    values 
      ( the_account_id, 
        the_quantity, 
        type_id, 
        the_description, 
        the_operator_id, 
        new_balance, 
        the_currency_id);

end|



/**
 * procedure allows to manually correct / reset account balance. Mainly 
 * should be used for the global accounts -3, -2, -1 not user accounts.
 * in any case: it always adds the correct as a new line to the account in order
 * to keep all changes traceable. 
 */
drop procedure if exists correct_account_balance|
create procedure correct_account_balance(in the_account_id int, in the_balance decimal(10,2), in the_operator_id int, in the_description varchar(255))
begin
	
	declare current_balance decimal(10,2);
	declare quantity decimal(10,2);
	
	-- get the current balance -- 
  	select 
  		balance 
  	into 
  		current_balance
  	from 
  		aixada_account
  	where 
  		account_id = the_account_id
  	order by ts desc, id desc
  	limit 1; 
	
  	set quantity = -(current_balance - the_balance); 
  	
	insert into 
  		aixada_account (account_id, quantity, payment_method_id, description, operator_id, balance) 
  	values 
  		(the_account_id, 
  	 	 quantity, 
  	 	9, 
  	 	the_description, 
  	 	the_operator_id, 
  	 	the_balance);
	
end|

/**
 * returns the current balance of a given account
 */
drop procedure if exists get_account_balance|
create procedure get_account_balance(in the_account_id int)
begin

	select
		*
	from
		aixada_account
	where
		account_id = the_account_id 
	order by
		ts desc, id desc
	limit 1;
end|

/**
 * returns the current balance of Caixa (-3), Consum (-2), Mantenimient (-1)
 */
drop procedure if exists global_accounts_balance|
create procedure global_accounts_balance()
begin

	(select
		*
	from
		aixada_account
	where
		account_id = -2 
	order by
		ts desc, id desc
	limit 1)
	union all
	(select
		*
	from
		aixada_account
	where
		account_id = -2 
	order by
		ts desc, id desc
	limit 1)
	union all
	(select
		*
	from
		aixada_account
	where
		account_id = -3 
	order by
		ts desc, id desc
	limit 1);
end|


/**
 * retrieves all ufs with negative balance
 */
drop procedure if exists negative_accounts|
create procedure negative_accounts()
begin
  select 
	uf.id as uf, 
	uf.name, 
	a.balance, 
	a.ts as last_update 
  from (select 
			account_id, max(id) as MaxId 
		from 
			aixada_account 
		group by 
			account_id) r, aixada_account a, aixada_uf uf
  where 
	a.account_id = r.account_id 
	and a.id = r.MaxId
	and a.balance < 0
    and uf.active = 1
    and uf.id = a.account_id -1000
  order by
	a.balance;
end|


/**
 * retrieves account movements for a given date range
 */
drop procedure if exists get_extract_in_range|
create procedure get_extract_in_range(in the_account_id int, in from_date date, in to_date date)
begin
	select
    	a.id,
	    a.ts, 
	    a.quantity,
	    a.description as description,
	    a.account_id as account,
	    p.description as method,
	    c.name as currency,
	    ifnull(mem.name, 'default') as operator,
	    a.balance
 	from 
 		aixada_account a,
 		aixada_payment_method p,
 		aixada_user u,
 		aixada_member mem,
 		aixada_currency c
 	where 
 		a.account_id = the_account_id
 		and a.ts >= from_date 
 		and a.ts <= to_date 
 		and a.currency_id = c.id
 		and a.payment_method_id = p.id
 		and a.operator_id = u.id
 		and u.member_id = mem.id
 	order by 
 		a.ts desc, id desc; 
 
end|


/**
 * retrieves latest account movements 
 * could and should be integrated into get_extract_in_range()
 */
drop procedure if exists latest_movements|
create procedure latest_movements()
begin
  declare tomorrow datetime default date_add(sysdate(), interval 1 day);

  select
    a.id,
  	a.account_id,
    time(a.ts) as time, 
    a.quantity,
    p.description as method,
    c.name as currency,
    concat(uf.id, ' ' , uf.name) as uf_id,
    balance
 from aixada_account a
 left join aixada_currency c
   on a.currency_id = c.id
 left join aixada_payment_method p
   on a.payment_method_id = p.id
 left join aixada_user u
   on a.operator_id = u.id
 left join aixada_member mem
   on u.member_id = mem.id
 left join aixada_uf uf
   on a.account_id - 1000 = uf.id
 where a.account_id > 0
   and a.ts < tomorrow
 order by a.ts desc, id desc
 limit 10;
end|




drop procedure if exists income_spending_balance|
create procedure income_spending_balance(in tmp_date date)
begin
   declare today date default case tmp_date when 0 then date(sysdate()) else date(tmp_date) end;
   select 
     sum( 
       case when quantity>0 then quantity else 0 end
     ) as income,
     sum(
       case when quantity<0 then quantity else 0 end
     ) as spending,
     sum(quantity) as balance
   from aixada_account a
   use index (ts)
   where a.ts between today and date_add(today, interval 1 day) and
         a.account_id = -3;
end|


delimiter ;
delimiter |


/**
 * returns all items in aixada_shop_item for 
 * a specific date and an uf OR a given cart_id,
 * The validated parameter specifies if we are interested in validated carts or only those
 * that have not a ts_validated timestamp.
 */
drop procedure if exists get_shop_cart|
create procedure get_shop_cart(in the_date_for_shop date, in the_uf_id int, in the_cart_id int, in validated boolean)
begin

  declare wherec varchar(255) default "";

  if (the_cart_id > 0) then
		set wherec = concat("c.id = ", the_cart_id); 
  else
		set wherec = concat("c.date_for_shop='",the_date_for_shop,"' and c.uf_id=",the_uf_id);
  end if; 
  
  -- retrieve only non-validated carts (for aixada cart). for report retrieve all including validated ones -- 
  if (validated is false) then
  	set wherec = concat(wherec, " and c.ts_validated = 0"); 
  end if;
	
  set @q = concat("select 
    p.id,
    p.name,
    p.description,
    c.id as cart_id,
	c.date_for_shop,
	c.ts_last_saved,
    si.quantity as quantity,
    si.iva_percent, 
    si.order_item_id,
    si.unit_price_stamp as unit_price,
    p.provider_id,  
    pv.name as provider_name,
    p.category_id,  
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
  	",wherec,"
  	and si.cart_id = c.id
  	and si.product_id = p.id
  	and pv.id = p.provider_id
  	and rev.id = p.rev_tax_type_id
  	and iva.id = p.iva_percent_id
  	and um.id = p.unit_measure_shop_id
  order by p.provider_id, p.name; ");
  
  prepare st from @q;
  execute st;
  deallocate prepare st;
  
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
    if (oi.date_for_order = '1234-01-23', 'true', 'false') as preorder, 
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
	

end |


delimiter ; 
delimiter |


/**
 * returns all orderable dates, irrespective if they have ordered items or not. 
 *
 */
drop procedure if exists get_orderable_dates|
create procedure get_orderable_dates(in from_date date, in the_limit int)
begin
	declare from_date_onward date default from_date;
	
  	if from_date = 0 then 
  		set from_date_onward = date(sysdate()); 
  	end if;
		
	set @q = concat("select distinct
		po.date_for_order
	from 
		aixada_product_orderable_for_date po
	where
		po.date_for_order > '", from_date_onward,"'
	order by
		po.date_for_order asc
	limit ", the_limit , ";");

	prepare st from @q;
  	execute st;
  	deallocate prepare st;
end|


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
delimiter |


/**
 * returns incidents for given list of ids
 */
drop procedure if exists get_incidents_by_ids|
create procedure get_incidents_by_ids(in the_ids text, in the_type int)
begin
	
	set @q = concat("select 
		i.*,
		mem.name as user_name,
	    mem.uf_id as uf_id,		
	    it.id as distribution_level,
	    it.description as type_description,
	    pv.id as provider_id,
	    pv.name as provider_name
	from
  		aixada_incident_type it,
  		aixada_user u,
  		aixada_member mem, 
 		aixada_incident i 
  	left join 
  		aixada_provider pv
	on 
		i.provider_concerned = pv.id
  	where
		i.id in (", the_ids,")
	  	and i.operator_id = u.id
	    and	u.member_id = mem.id
	    and i.incident_type_id >= ",the_type,"
	    and it.id = i.incident_type_id
	order by
		i.ts desc;");
	
	prepare st from @q;
  	execute st;
  	deallocate prepare st;
	
end|



/**
 * returns list of incident types.  
 */
drop procedure if exists get_incident_types|
create procedure get_incident_types()
begin
  select 
  	id, 
  	description, 
  	definition
  from 
  	aixada_incident_type;
end|

/**
 * creates new incidents, edits existing one if id is given. 
 */
drop procedure if exists manage_incident|
create procedure manage_incident(in the_id int,
                              	 in the_subject varchar(100),
								 in the_type tinyint,
                              	 in the_operator int,
                             	 in the_details text,
                              	 in the_priority int,
                              	 in the_ufs_concerned varchar(100),
                              	 in the_comm varchar(100),
                              	 in the_prov varchar(100),
                              	 in the_status varchar(10))
begin
	
	if (the_id > 0) then
	 	update 
	 		aixada_incident 
	 	set
     		incident_type_id = the_type, 
     		priority = the_priority,
     		subject = the_subject,
     		operator_id = the_operator,
     		details = the_details,
     		ufs_concerned = the_ufs_concerned,
     		commission_concerned = the_comm,
     		provider_concerned = the_prov,
     		status = the_status
  		where
     		id = the_id;
	else 

		insert into 
			aixada_incident (incident_type_id, priority, subject, operator_id, details, ufs_concerned, commission_concerned, provider_concerned, status) 
     	values 
     		(the_type, the_priority, the_subject, the_operator, the_details, the_ufs_concerned, the_comm, the_prov, the_status);
	
	end if; 
	

end |


/**
 * deletes an incident without remorse
 */
drop procedure if exists delete_incident|
create procedure delete_incident(in the_id int)
begin
  delete from aixada_incident
  where id = the_id;
end|


/**
 *  retrieves listing of incidents
 */
drop procedure if exists get_incidents_listing|
create procedure get_incidents_listing(in from_date date, in to_date date, in the_type int)
begin
	
	
	select 
		i.*, 
	    mem.name as user_name,
	    mem.uf_id as uf_id,		
	    it.id as distribution_level,
	    it.description as type_description,
	    pv.id as provider_id,
	    pv.name as provider_name
  	from 
  		aixada_incident_type it,
  		aixada_user u,
  		aixada_member mem, 
 		aixada_incident i 
  	left join 
  		aixada_provider pv
	on 
		i.provider_concerned = pv.id
  	where
	  	i.ts >= from_date and i.ts <= to_date
	  	and i.operator_id = u.id
	    and	u.member_id = mem.id
	    and i.incident_type_id >= the_type
	    and it.id = i.incident_type_id
	    
  order by 
  	i.ts desc;
	
end |


delimiter ;
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
		oi.order_id, 
		oi.quantity,
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
 *	Checks if a product_id has ordered items (i.e entries in aixada_order_item). 
 *	Counts can be made filtering order_status:
 *			0 -> is still open
 *			1 -> send off, order_id is set
 *			2 -> doesn't matter closed and open. 
 *	and counts can be restricted by date range. 
 */
drop procedure if exists order_item_count|
create procedure order_item_count (	in the_product_id int, 
									in order_status int,
									in the_from_date date, 
									in the_to_date date)
begin

	declare from_date date default date("1234-01-01"); 
	declare to_date date default date("9999-01-01");

	declare wherec varchar(255) default "";

	if (order_status=0) then
		set wherec = " and oi.order_id is NULL";
	elseif (order_status = 1) then
		set wherec = " and oi.order_id > 0";
	elseif (order_status = 2) then
		set wherec = ""; 
	end if; 

	if (the_from_date > 0) then
		set wherec = concat(wherec, " and oi.date_for_order >= '",the_from_date,"'");
	end if; 
	
	if (the_to_date > 0) then
		set wherec = concat(wherec, " and oi.date_for_order <= '",the_to_date,"'");
	end if; 

	set @q = concat("select
		count(*) as total_ordered_items
	from
		aixada_order_item oi
	where
		oi.product_id =", the_product_id, wherec,";");
		
	
	prepare st from @q;
  	execute st;
  	deallocate prepare st;
end|



/**
 * By default only those dates in table aixada_product_orderable_for_date can be 
 * deactivated that have no ordered items associated. This procedure can delete
 * those dates where orders have been made, which implies to delete the associated items 
 * from order carts as well. 
 *
 * If a specific date is supplied, then the order items for this order date only will
 * be deleted. If date is 0, then order_items of the given product for ALL open orders 
 * will be deleted. This usually happens when an orderable product is deactivated. 
 */
drop procedure if exists deactivate_locked_order_date|
create procedure deactivate_locked_order_date (in the_product_id int, in the_date date)
begin

	declare done int default 0; 
	declare the_order_date date; 
	-- get a list of all order-dates for the given product that are not sent off yet --
	declare date_cursor cursor for 
		select
			date_for_order
		from
			aixada_order_item
		where 
			product_id = the_product_id
			and order_id is NULL;	
	
	declare continue handler for not found
		set done = 1; 
	declare exit handler for sqlexception rollback; 
	declare exit handler for sqlwarning rollback; 

	
	start transaction; 
	
	if (the_date > 0) then
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

	else 

	
		open date_cursor;	
		set done = 0; 

		read_loop: loop
			fetch date_cursor into the_order_date;
			if done then 
				leave read_loop; 
			end if;

			delete from
				aixada_order_item
			where
				product_id = the_product_id
				and date_for_order = the_order_date; 

			delete from
				aixada_product_orderable_for_date
			where
				product_id = the_product_id
				and date_for_order = the_order_date; 

		end loop;
		close date_cursor;

	end if; 

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
create procedure get_type_orderable_products (in the_provider_id int,
		in ge_orderable_type_id tinyint)
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
		and p.orderable_type_id >= ge_orderable_type_id
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
		p.name as product_name,
		p.provider_id,
		pv.name as provider_name,
		po.date_for_order,
		po.closing_date,
		datediff(po.closing_date, today) as time_left,
		(select
			max(o.id) -- This prevent error: "Subquery returns more than 1 row"
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
		aixada_product_orderable_for_date po
        left join
		aixada_product p
        on 
	        p.id = po.product_id
        left join 
		aixada_provider pv
        on 
	        p.provider_id = pv.id
	where 
		    p.provider_id = the_provider_id
		and po.date_for_order between fromDate and toDate;
end|



/**
 *  returns all products (with details). This query is needed for the shop/order pages and its
 *  different mechanisms for searching: by provider, by category, or direct search. 
 *  As such this query shows available products for ordering or for purchase but does not handle 
 *  any real ordered or bought products. 
 *  The search functionality is also called from the validate page. 
 *
 * 
 *  If a provider_id is set, 
 *     it returns the associated products for the provider. 
 *     If, additionally, date is set, these products are orderable, otherwise stock. 
 *
 *  If category_id is set, it returns products by category. 
 *     If, additionally, date is set, these products are orderable, otherwise stock.
 * 
 *  If provider_id = 0 and category_id = 0 and the_like is set, then searches for product 
 * 
 *
 *  Furthermore it is important to note that the price delivered includes IVA and Rev Tax!! 
 *  There is no need to calcuate this at a later point in time (upon validation for example). 
 */
drop procedure if exists get_products_detail|
create procedure get_products_detail(in the_provider_id int, 
       		 		     in the_category_id int, 
				     in the_like varchar(255),
				     in the_date date,
				     in include_inactive boolean,
				     in the_product_id int)
begin
	
    declare today date default date(sysdate());
    declare wherec varchar(255) default "";
    declare fromc varchar(255) default "";
    declare fieldc varchar(255) default "";
     
    
    /** show active products only or include inactive products as well? **/
    if (include_inactive = 0) then 
       set wherec = "and p.active=1 and pv.active=1";
    end if;	
   
    
    /** no date provided we assume that we are shopping, i.e. all active products are shown stock + orderable **/
    if the_date = 0 then
    	set wherec = concat(wherec, " and p.unit_measure_shop_id = u.id ");
    
    /** hack: date=-1 works to filter stock only products **/ 	
    elseif the_date = '1234-01-01' then 
    	set wherec = concat(wherec, " and (p.orderable_type_id = 1 or p.orderable_type_id = 4) and p.unit_measure_shop_id = u.id ");
    
    /** otherwise search for products with orderable dates **/
    else 
    	set fieldc = concat(", datediff(po.closing_date, '",today,"') as time_left");
       	set fromc = 	"aixada_product_orderable_for_date po, ";
    	set wherec = 	concat(wherec, " and po.date_for_order = '",the_date,"' and po.product_id = p.id and p.unit_measure_order_id = u.id ");	
    end if;
     
    
    
    /** get a specific product **/
    if the_product_id > 0 then 
    	set wherec = concat(wherec, " and p.id = '", the_product_id, "' ");
    end if;
	
    /** get products by provider_id **/
    if the_provider_id > 0 then
	set wherec = concat(wherec, " and pv.id = '", the_provider_id, "' ");
    end if;
    	
    /** get products by category_id **/
    if the_category_id > 0 then 
    	set fromc = concat(fromc, "aixada_product_category pc,");
    	set wherec = concat(wherec, " and pc.id = '", the_category_id, "' and p.category_id = pc.id ");
    end if;
	
    /** search for product name **/
    if the_like != "" then
    	set wherec 	= concat(wherec, " and p.name LIKE '%", the_like,"%' ");
    end if;
    
  
	set @q = concat("
	select
		p.*,
		round((p.unit_price * (1 + iva.percent/100) * (1 + t.rev_tax_percent/100)),2) as unit_price,
		p.unit_price as unit_price_netto, 
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
   		round((p.unit_price * (1 + iva.percent/100) * (1 + r.rev_tax_percent/100)),2) as unit_price,
		iva.percent as iva_percent,
   		r.rev_tax_percent
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
  		and p.unit_measure_order_id = u.id
		and p.iva_percent_id = iva.id 
  	order by p.id, p.name;
end|


/**
 * retrieves the value of available stock for the given provider
 */
drop procedure if exists get_stock_value|
create procedure get_stock_value(in the_provider_id int)
begin
	
	declare wherec varchar(255) default "p.active = 1";
	
	if (the_provider_id > 0) then
		set wherec = concat("p.provider_id=", the_provider_id, " and p.active = 1");
	end if; 
	
	set @q = concat("select
		p.id as product_id, 
		p.stock_actual,
		p.orderable_type_id,
		p.name,	
		p.unit_price,
		round((p.stock_actual * p.unit_price), 2) as total_netto_stock_value,
		round((p.stock_actual * p.unit_price * (1 + iva.percent/100) * (1 + rev.rev_tax_percent/100)), 2) as total_brutto_stock_value,
		iva.percent as iva_percent,
		rev.rev_tax_percent,
		u.unit as shop_unit
	from 
		aixada_product p,
		aixada_iva_type iva,
		aixada_rev_tax_type rev,
		aixada_unit_measure u
	where 
		",wherec,"
		and p.rev_tax_type_id = rev.id
		and p.iva_percent_id = iva.id
		and p.unit_measure_shop_id = u.id
		and p.orderable_type_id = 1;");
		
		
	prepare st from @q;
  	execute st;
  	deallocate prepare st;
	
end|


/**
 * correct stock. this should be the exception since stock is normally added
 * and then sold which deduces automatically the correct amount. 
 * However, stock disappears.... somehow
 */
drop procedure if exists correct_stock|
create procedure correct_stock(in the_product_id int, 
								in the_current_stock decimal(10,4), 
								in the_description varchar(255),
								in the_movement_type_id int,	
								in the_operator_id int)
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
   		aixada_stock_movement (product_id, operator_id, amount_difference, description, movement_type_id, resulting_amount) 
   	select
     	the_product_id,
     	the_operator_id,
     	the_current_stock - p.stock_actual,
		the_description,
		the_movement_type_id,
     	the_current_stock
    from 
    	aixada_product p
    where 
    	p.id = the_product_id;
 		
 	-- update the product quantity to the new value -- 
 	update
 		aixada_product
 	set
 		stock_actual = the_current_stock,
		delta_stock = the_current_stock - stock_min
 	where
 		id = the_product_id;
    	
    	
    commit; 
	
end|



/**
 * add stock
 */
drop procedure if exists add_stock|
create procedure add_stock(in the_product_id int, 
			   in delta_amount decimal(10,4), 
			   in the_operator_id int, 
			   in the_movement_type_id int,
			   in the_description varchar(255))
begin
   	start transaction;
	   
   	update 
		aixada_product
	set 
		stock_actual = stock_actual + delta_amount,
	        delta_stock  = delta_stock + delta_amount /* maintain the invariant delta_stock = stock_actual - stock_min */
	where 
		id = the_product_id;

	/* register the movement */
   	insert into 
   		aixada_stock_movement (product_id, operator_id, amount_difference, description, movement_type_id, resulting_amount) 
   	select
     	the_product_id,
     	the_operator_id,
     	delta_amount,
     	the_description,
     	the_movement_type_id,
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
        p.active = 1
    and p.orderable_type_id = 1
    and p.stock_actual <= p.stock_min  /* "<=" to catch zero stock; often, stock_min=0 */
    and p.provider_id = pv.id 	
    and pv.active = 1
  order by pv.name;
end|


/**
 * retrieves info about all stock movements of a given product
 * or all products. 
 */
drop procedure if exists stock_movements|
create procedure stock_movements(	in the_product_id int, 
									in the_provider_id int, 
									in from_date varchar(255), 
									in to_date varchar(255), 
									in the_limit varchar(255))
begin
  
	declare wherec varchar(255) default ""; 
	declare limitc varchar(255) default "";
	declare datec varchar(255) default " and sm.ts between '1234-01-02' and '9999-01-01' ";


	if (the_provider_id > 0) then
		set wherec = concat(" and p.provider_id = ", the_provider_id);
	elseif (the_product_id > 0) then
		set wherec = concat(" and p.id = ", the_product_id);
	end if; 

	if (the_limit != "") then 
		set limitc = concat(" limit ", the_limit);
	end if; 

	if (from_date != "" and to_date != "") then
		set datec = concat(" and sm.ts between '",from_date,"' and '",to_date,"' ");
	end if; 

	
	set @q = concat("select
		sm.*,
		mem.id as member_id,
		mem.name as member_name,
		p.name as product_name,
		p.id as product_id,
		calc_delta_price(sm.amount_difference, p.unit_price, iva.percent) as delta_price,
		um.unit,
		smt.name as movement_type
	from
		aixada_stock_movement sm,
		aixada_member mem,
		aixada_product p, 
		aixada_iva_type iva,
		aixada_unit_measure um,
		aixada_stock_movement_type smt
	where
		mem.id = sm.operator_id
		",wherec," 
		",datec,"
		and p.unit_measure_shop_id = um.id
		and p.iva_percent_id = iva.id
		and sm.product_id = p.id
		and smt.id = sm.movement_type_id
	order by
		sm.ts desc, sm.product_id desc ",limitc,";");
		
	prepare st from @q;
  	execute st;
  	deallocate prepare st;
		
end|


/**
 * calculates the accumulated loss of stock corrections
 */
drop function if exists calc_delta_price|
create function calc_delta_price(the_diff_amount decimal(10,4), the_unit_price decimal(10,2), the_iva_percent decimal(10,2))
returns decimal(10,2)
DETERMINISTIC
begin
	
	declare result decimal(10,2) default 0.00;
	
	if (the_diff_amount < 0) then
		set result = the_diff_amount  * the_unit_price *  (1 + the_iva_percent/100);
	end if;
		
	return result;
end|



delimiter ;
delimiter |



/**
 *	deactivates or actives a provider. basically means that deactivated providers
 *  does not appear anymore in the listings. This does NOT deactivate its products. 
 */
drop procedure if exists change_active_status_provider|
create procedure change_active_status_provider (in the_active_state boolean, in the_provider_id int)
begin

	update 
		aixada_provider
	set 
		active = the_active_state
	where 
		id = the_provider_id;

end|



/**
 * returns the responsible users for a provider. 
 */
drop procedure if exists get_responsible_uf|
create procedure get_responsible_uf(in the_provider_id int)
begin
	
	select 
		u.*
	from 
		aixada_user u,
		aixada_provider pv
	where
		pv.id = the_provider_id
		and pv.responsible_uf_id = u.uf_id;
end|


/**
 * returns all providers that have "sometimes orderable" or "always orderable"
 * items not stock. 
 * independent if products have been ordered or not. 
 */
drop procedure if exists get_orderable_providers|
create procedure get_orderable_providers(in ge_orderable_type_id tinyint)
begin
  select distinct 
     pv.id, 
     pv.name
  from 
  	aixada_provider pv, 
  	aixada_product p
  where  
    pv.active = 1
    and pv.id = p.provider_id
    and p.orderable_type_id >= ge_orderable_type_id
  order by pv.name;
end|


/**
 * returns all providers that have orderable products for a given date
 */
drop procedure if exists get_orderable_providers_for_date|
create procedure get_orderable_providers_for_date(in the_date date)
begin
  select distinct 
     pv.id, 
     pv.name
  from 
  	aixada_provider pv, 
  	aixada_product p,
  	aixada_product_orderable_for_date po
  where  
    pv.active = 1
    and pv.id = p.provider_id
    and p.id = po.product_id
    and po.date_for_order = the_date
  order by pv.name;
end|



/**
 * returns list of providers that are active and that
 * have at least one active product. This is a list of providers
 * from which potentially something can be bought. 
 */
drop procedure if exists get_shop_providers|
create procedure get_shop_providers()
begin	
	  select
	  	pv.id,
	  	pv.name as name
	  from 
	  	aixada_provider pv,
	 	aixada_product p
	  where
	  	pv.active = 1
	  	and p.active = 1
	  	and p.provider_id = pv.id
	  group by 
	  	pv.name
	  order by 
	  	pv.name;
end|


/**
 * returns list of providers with stock only products, 
 * or cumulative order
 */
drop procedure if exists get_stock_providers|
create procedure get_stock_providers()
begin
  select distinct
	 pv.id, 
     pv.name
  from 
  	aixada_provider pv, 
  	aixada_product p
  where  
    pv.active = 1
    and pv.id = p.provider_id
    and p.active = 1
    and (p.orderable_type_id = 1 or p.orderable_type_id = 4) 
  order by pv.name;
end|


/**
 *	returns a list of all active providers
 *	with sometimes, always orderable items, stock, preorder
 */
drop procedure if exists get_all_active_providers|
create procedure get_all_active_providers()
begin
  select distinct 
     pv.id, 
     pv.name
  from 
  	aixada_provider pv
  where  
    pv.active = 1
  order by pv.name;
end|


/**
 *	returns a list of all active providers
 *	with sometimes, always orderable items, stock, preorder
 */
drop procedure if exists get_provider_listing|
create procedure get_provider_listing(in the_provider_id int, in include_inactive boolean)
begin
	
	declare wherec varchar(255) default '';
	
	-- show all providers including inactive or just active ones -- 
	set wherec = if(include_inactive=1,"","and pv.active = 1");	
	
	-- filter for specific provider --
	if the_provider_id > 0 then
		set wherec = concat(wherec, " and pv.id=",the_provider_id);
	end if; 
		
	set @q = concat("select
	     pv.*, 
		 uf.id as responsible_uf_id, 
		 uf.name as responsible_uf_name
	  from 
	  	aixada_provider pv,
	  	aixada_uf uf
	  where  
	  	pv.responsible_uf_id = uf.id
	    ",wherec,"
	  order by pv.name, pv.id;");
	  
	  prepare st from @q;
	  execute st;
	  deallocate prepare st;
	  
end|



/*************************************************
 * 
 * procedures for categories
 * 
 *************************************************/


/**
 * returns all categories that have orderable products 
 * for a given date 
 */
drop procedure if exists get_orderable_categories_for_date|
create procedure get_orderable_categories_for_date(in the_date date)
begin
  select distinct 
     pc.id, 
     pc.description
  from 
  	aixada_provider pv, 
  	aixada_product p,
  	aixada_product_orderable_for_date po,
  	aixada_product_category pc
  where  
    pv.active = 1
    and p.active = 1
    and p.category_id = pc.id
    and po.date_for_order = the_date
    and p.id = po.product_id
    and p.provider_id = pv.id
  order by pc.description;
end|


/**
 * returns all categories that have orderable and shop products 
 * for a given date if date is > 0. Otherwise returns all categories
 * for all active provider/Products. 
 */
drop procedure if exists get_shop_categories_for_date|
create procedure get_shop_categories_for_date(in the_date date)
begin
	
  if (the_date > 0) then
	
	  (select distinct 
	     pc.id, 
	     pc.description as description
	  from 
	  	aixada_provider pv, 
	  	aixada_product p,
	  	aixada_product_orderable_for_date po,
	  	aixada_product_category pc
	  where  
	    po.date_for_order = the_date
	    and p.category_id = pc.id
	    and po.product_id = p.id
	    and p.provider_id = pv.id)
	  union
	  (select distinct
	  	pc.id,
	  	pc.description as description
	  from 
	  	aixada_provider pv,
	 	aixada_product p,
	 	aixada_product_category pc
	  where
	  	pv.active = 1
	  	and p.active = 1
	  	and p.category_id = pc.id
	  	and pv.id = p.provider_id
	  	and p.orderable_type_id = 1)
	  order by description;
	  
  else 
  	 
  	  select
	  	pc.id,
	  	pc.description as description
	  from 
	  	aixada_provider pv,
	 	aixada_product p,
	 	aixada_product_category pc
	  where
	  	pv.active = 1
	  	and p.active = 1
	  	and p.category_id = pc.id
	  	and pv.id = p.provider_id
	  group by
	  	description
	  order by 
	    description;
	    
  end if; 
  
end|



delimiter ;
delimiter |


/**
 *  most queries here are used by lib/report_manager.php for generating the order reports 
 *  for download. 
 */


/**
* return all orders for a given date and provider
*/
drop procedure if exists orders_for_date_and_provider|
create procedure orders_for_date_and_provider(IN order_date DATE, IN the_provider_id int) 
begin
  select
    product_id,
    uf_id,
    quantity
  from 
    aixada_order_item
  left join
    aixada_product
  on
    aixada_order_item.product_id = aixada_product.id
  where
    date_for_order = order_date and
    aixada_product.provider_id = the_provider_id;
end|


/**
 * report the total orders for one provider for a given date
 */
drop procedure if exists total_orders_for_date_and_provider|
create procedure total_orders_for_date_and_provider(IN order_date date, IN the_provider_id int)
begin
  select
    prov.name as provider_name,
    convert(sum(i.quantity * prod.price), decimal(10,2)) as total_price
  from 
    aixada_provider prov
    left join aixada_product prod
    on prov.id = prod.provider_id
    left join aixada_order_item i
    on prod.id = i.product_id
    where prov.id = the_provider_id 
      and i.date_for_order = order_date;      
end|

drop procedure if exists providers_with_orders_for_date|
create procedure providers_with_orders_for_date(in order_date date)
begin
  select distinct
    prov.id as id
  from aixada_provider prov
  left join aixada_product prod
  on prod.provider_id=prov.id
  left join aixada_order_item i
  on prod.id = i.product_id
  where prov.id = the_provider_id 
    and i.date_for_order = order_date;      
end|

/**
 * report the total orders for all providers for a given date, in summary
 */
drop procedure if exists summarized_orders_for_date|
create procedure summarized_orders_for_date(in order_date date)
begin
  select 
    pv.id as provider_id,
    pv.name as provider_name,
    pv.email as provider_email,
    pv.phone1 as provider_phone,
    uf.id as responsible_uf,
    uf.name as responsible_uf_name,
    gm.the_phone as responsible_uf_phone,
    convert(sum(i.quantity * p.unit_price),decimal(10,2)) as total_price,
    count(distinct i.uf_id) as total_ufs
  from 
    aixada_order_item i
    left join 
    aixada_product p
    on i.product_id = p.id
    left join aixada_provider pv
    on p.provider_id = pv.id
    left join aixada_uf uf
    on pv.responsible_uf_id = uf.id
    left join (
      select m.uf_id, group_concat(phone1) as the_phone
      from aixada_member m
      group by uf_id
    ) gm
    on uf.id = gm.uf_id
  where 
    i.date_for_order = order_date
  group by pv.id
  order by pv.id;
end|

drop procedure if exists summarized_orders_for_provider_and_date|
create procedure summarized_orders_for_provider_and_date(in the_provider int, in order_date date)
begin
  select 
    p.name as product_name, 
    p.description,
    m.unit,
    sum(i.quantity) as total_quantity,
    convert(sum(i.quantity * p.unit_price), decimal(10,2)) as total_price
  from
    aixada_order_item i
    left join aixada_product p
    on i.product_id = p.id
    left join aixada_unit_measure m
    on p.unit_measure_order_id = m.id
  where 
    i.date_for_order = order_date
    and p.provider_id = the_provider
  group by p.name;
end|

/**
 * report the total orders for all providers for a given date, in summary
 */
drop procedure if exists summarized_preorders|
create procedure summarized_preorders()
begin
  select 
    pv.id,
    pv.name,
    pv.email,
    convert(sum(i.quantity * p.unit_price), decimal(10,2)) as total_price,
    count(distinct i.uf_id) as total_ufs
  from 
    aixada_order_item i
    left join 
    aixada_product p
    on i.product_id = p.id
    left join aixada_provider pv
    on p.provider_id = pv.id
  where 
    i.date_for_order = '1234-01-23'
  group by pv.id
  order by pv.id;
end|

/**
 * report the detailed orders for a provider for a given date
 */
drop procedure if exists detailed_orders_for_provider_and_date|
create procedure detailed_orders_for_provider_and_date(in the_provider int, in order_date date)
begin
  select
    p.name as product_name, 
    p.description,
    u.id as uf,
    i.quantity as qty,
    m.unit,
    sum(i.quantity) as total_quantity,
    convert(sum(i.quantity * p.unit_price), decimal(10,2)) as total_price
  from
    aixada_order_item i
    left join aixada_product p
    on i.product_id = p.id
    left join aixada_unit_measure m
    on p.unit_measure_order_id = m.id
    left join aixada_uf u
    on i.uf_id = u.id
  where 
    i.date_for_order = order_date
    and p.provider_id = the_provider
  group by p.name, u.id
  with rollup;
end|

/**
 * report the detailed preorders for a provider for a given date
 */
drop procedure if exists detailed_preorders_for_provider_and_date|
create procedure detailed_preorders_for_provider_and_date(in the_provider int)
begin
  select
    p.name as product_name, 
    p.description,
    u.id as uf,
    i.quantity as qty,
    m.unit,
    sum(i.quantity) as total_quantity,
    convert(sum(i.quantity * p.unit_price), decimal(10,2)) as total_price
  from
    aixada_order_item i
    left join aixada_product p
    on i.product_id = p.id
    left join aixada_unit_measure m
    on p.unit_measure_order_id = m.id
    left join aixada_uf u
    on i.uf_id = u.id
  where 
    i.date_for_order = '1234-01-23'
    and p.provider_id = the_provider
  group by p.name, u.id
  with rollup;
end|

/**
 * report the total orders for all providers for a given date, in detail
 */
drop procedure if exists detailed_total_orders_for_date|
create procedure detailed_total_orders_for_date(IN order_date date)
begin
  select
    pv.name as provider_name, 
    pv.email as email,
    p.name as product_name, 
    p.description,
    p.iva_percent as iva,
    i.uf_id as uf,
    sum(i.quantity) as qty,
    convert(sum(i.quantity * p.unit_price), decimal(10,2)) as total_price,
    m.unit as unit
  from 
    aixada_order_item i 
    left join aixada_product p
    on i.product_id = p.id
    left join aixada_provider pv
    on p.provider_id = pv.id
    left join aixada_unit_measure m
    on p.unit_measure_order_id = m.id
  where
    i.date_for_order = order_date
  group by
    pv.id,
    p.name,
    i.uf_id
  with rollup;
end|

drop procedure if exists spending_per_provider|
create procedure spending_per_provider(in start_date date)
begin
  select 
        prov.id, 
        prov.name, 
        month(i.date_for_order) m, 
        convert(sum(i.quantity * prod.unit_price), decimal(10,2)) s   
  from aixada_order_item i    
  left join aixada_product prod 
         on i.product_id = prod.id 
  left join aixada_provider prov
         on prod.provider_id=prov.id  
  where i.date_for_order >= start_date 
  group by prov.name, m;
end|


drop procedure if exists last_shop_times_for_uf|
create procedure last_shop_times_for_uf(in the_uf int)
begin
  declare first_shop_date datetime default date_add(sysdate(), interval -6 month);
  select concat(id, ' ', date_for_shop, ' ',
                if(ts_validated=0, 'Comanda no validada', 
                                    concat('Validada: ', ts_validated))) as shop_time
  from aixada_shop_item 
  where uf_id = the_uf
  and (ts_validated >= first_shop_date or ts_validated = 0)
  group by date_for_shop
  order by date_for_shop desc;
end|



drop procedure if exists shopped_items_by_id|
create procedure shopped_items_by_id(in the_id int)
begin
  select distinct
      p.id,
      p.name,
      p.description,
      p.category_id,
      pv.name as provider_name,
      p.category_id,
      p.unit_price * (1 + p.iva_percent*0.01) as unit_price, 
      u.unit,
      rev_tax_percent,
      'false' as preorder,
      i.quantity
  from
      (
        select uf_id, date_for_shop
        from aixada_shop_item 
        where id = the_id
      ) uf_and_date 
      left join
      aixada_shop_item i
      on i.uf_id = uf_and_date.uf_id and i.date_for_shop = uf_and_date.date_for_shop
      left join aixada_product p
      on i.product_id = p.id 
      left join aixada_provider pv
      on p.provider_id = pv.id
      left join aixada_rev_tax_type t
      on p.rev_tax_type_id = t.id
      left join aixada_unit_measure u
      on p.unit_measure_shop_id = u.id
  order by pv.name desc, p.name desc;  
        /* the desc is so that the entries will appear sorted correctly on the screen */
end|

drop procedure if exists shop_for_uf_and_time|
create procedure shop_for_uf_and_time(in the_uf int, in shop_time datetime)
begin
  select
      p.id,
      p.name,
      p.description,
      p.category_id,
      pv.name as provider_name,
      p.category_id,
      p.unit_price * (1 + p.iva_percent*0.01) as unit_price, 
      u.unit,
      rev_tax_percent,
      'false' as preorder,
      i.quantity
  from 
      aixada_shop_item i
      left join aixada_product p
      on i.product_id = p.id 
      left join aixada_provider pv
      on p.provider_id = pv.id
      left join aixada_rev_tax_type t
      on p.rev_tax_type_id = t.id
      left join aixada_unit_measure u
      on p.unit_measure_shop_id = u.id
  where 
      i.ts_validated = shop_time
  and i.uf_id = the_uf
  order by pv.name, p.name;

/*  order by p.provider_id, p.category_id, p.name;*/
end|


delimiter ;
delimiter |



/**
 * returns the total of sold items for a given provider
 */
drop procedure if exists get_purchase_total_by_provider|
create procedure get_purchase_total_by_provider (	in from_date date, 
													in to_date date, 
													in the_provider_id int, 
													in the_group_by varchar(20))
begin
	
	declare wherec varchar(255) default "";
	declare groupby varchar(20) default "";
	
	if (the_provider_id > 0) then
		set wherec = concat("and pv.id=",the_provider_id);
	end if;
	
	if (the_group_by = "shop_date") then
		set groupby = ", c.date_for_shop";
	end if; 
	
	
	set @q = concat("select
		round(sum(si.quantity * si.unit_price_stamp),2) as total_sales_brutto,
		round(sum(si.quantity * (si.unit_price_stamp / (1+si.rev_tax_percent/100) / (1+ si.iva_percent/100) )),2) as total_sales_netto,
		round(sum(si.quantity * (si.unit_price_stamp / (1+ si.iva_percent/100) )),2) as total_sales_rev,
		round(sum(si.quantity * (si.unit_price_stamp / (1+ si.rev_tax_percent/100) )),2) as total_sales_iva,
	 	pv.name as provider_name,
	 	pv.id as provider_id,
	 	c.date_for_shop
	from 
		aixada_shop_item si, 
		aixada_provider pv,
		aixada_product p,
		aixada_cart c
	where
		c.date_for_shop between '",from_date,"' and '",to_date,"'
		and c.id = si.cart_id
		and si.product_id = p.id
		and p.provider_id = pv.id
		",wherec,"
	group by
		pv.id ", groupby,"
	order by
		pv.name asc, c.date_for_shop desc;");
		
	prepare st from @q;
  	execute st;
  	deallocate prepare st;	
  	
end|

drop function if exists sum_ordered_total|
create function sum_ordered_total(the_date_for_shop date, the_provider_id int)
returns decimal(10,2)
reads sql data
begin
  declare total_price decimal(10,2);
  
  select 
	sum(total) into total_price
  from 
	aixada_order
  where
  	date_for_shop = the_date_for_shop
  	and provider_id = the_provider_id; 
  
  return total_price;
end|



/**
 * returns total of sold amount for given products of provider
 * total netto, total brutto in given date range
 */
drop procedure if exists get_purchase_total_of_products|
create procedure get_purchase_total_of_products(	in from_date date, 
													in to_date date, 
													in the_provider_id int, 
													in is_validated boolean, 
													in the_group_by varchar(20))
begin
	
	declare wherec varchar(255) default "";
	declare groupby varchar(20) default ""; 
	
	-- default, we use only validated carts
	if (is_validated) then
		set wherec = concat(" and c.ts_validated > 0 ");
	end if; 
	
	-- filter for products of certain provider --
	if (the_provider_id > 0) then
		set wherec = concat(wherec, " and p.provider_id=", the_provider_id);
	end if; 
	
	if (the_group_by = "shop_date") then
		set groupby = "c.date_for_shop,";
	end if; 
	
	
	
	set @q = concat("select 
		p.name as product_name, 
		p.active, 
		p.orderable_type_id,
		si.*,
		c.date_for_shop,
		pv.id as provider_id, 
		pv.name as provider_name,
		u.unit as shop_unit,
		round(sum(si.quantity * si.unit_price_stamp),2) as total_sales_brutto,
		round(si.unit_price_stamp / (1+si.rev_tax_percent/100 ) / (1+ si.iva_percent/100),2) as unit_price_stamp_netto,
		round(sum(si.quantity * (si.unit_price_stamp / (1+si.rev_tax_percent/100) / (1+ si.iva_percent/100) )),2) as total_sales_netto,
		sum(si.quantity) as total_sales_quantity
	from
		aixada_shop_item si, 
		aixada_product p, 
		aixada_provider pv, 
		aixada_cart c,
		aixada_unit_measure u
	where
		c.date_for_shop between '",from_date,"' and '",to_date,"'
		and si.cart_id = c.id
		and si.product_id = p.id
		and p.provider_id = pv.id
		and p.unit_measure_shop_id = u.id
		", wherec, "
	group by
		", groupby," p.id
	order by
		p.name asc;");
	
	prepare st from @q;
  	execute st;
  	deallocate prepare st;

		
end|



/**
 * retrieves non-validated carts for all ufs or every uf
 * also non-active ufs where there may be a non-validated cart
 * remaining...
 * could and should be integrated into get_purchase_listing
 */
drop procedure if exists get_non_validated_carts|
create procedure get_non_validated_carts(in the_uf_id int)
begin
	
	-- for a specififc uf --
	if (the_uf_id > 0) then
	
		select
			uf.id as uf_id, 
			uf.name as uf_name, 
			c.*,
			get_purchase_total(c.id) as purchase_total
		from
			aixada_cart c,
			aixada_uf uf
		where
			c.ts_validated = 0
			and c.uf_id = the_uf_id
			and uf.id = the_uf_id
		order by
			c.date_for_shop;
	
	-- every uf -- 
	else 
		select
			uf.id as uf_id, 
			uf.name as uf_name, 
			c.*,
			get_purchase_total(c.id) as purchase_total
		from
			aixada_cart c,
			aixada_uf uf
		where
			c.ts_validated = 0
			and c.uf_id = uf.id
		order by
			uf.id, c.date_for_shop; 
	end if; 
	
end|


/**
 * returns listing of aixada_cart's for given uf and date range. 
 * including the name and uf of the validation operator. 
 * if uf_id is not set (0), then returns for all ufs 
 */
drop procedure if exists get_purchase_listing|
create procedure get_purchase_listing(in from_date date, in to_date date, in the_uf_id int, in the_limit varchar(255))
begin
	
	declare wherec varchar(255) default "";
	declare set_limit varchar(255) default ""; 
	
	-- filter by uf_id --
	if (the_uf_id > 0) then
		set wherec = concat(" and c.uf_id = ",the_uf_id," and uf.id = ",the_uf_id);
	else 
		set wherec = concat(" and c.uf_id = uf.id");
	end if; 
	
	-- set a limit?
    if (the_limit <> "") then
    	set set_limit = concat("limit ", the_limit);
    end if;
	
	
	
	set @q =  concat("select 
		c.*, 
		uf.id as uf_id,
		uf.name as uf_name,
		m.name as operator_name,
		m.uf_id as operator_uf,
		get_purchase_total(c.id) as purchase_total
	from 
		aixada_uf uf,
		aixada_cart c
	left join 
		aixada_user u
	on 
		c.operator_id = u.id
	left join
		aixada_member m
	on 
		u.member_id = m.id
	where 
		c.date_for_shop between '",from_date,"' and '",to_date,"'
		",wherec,"
	order by 
		c.date_for_shop desc, uf.id desc
		",set_limit,";"); 
			
	prepare st from @q;
  	execute st;
  	deallocate prepare st;
end |


/**
 * for retrieving shop details (purchased products, quantities), see aixada_queries_cart.sql > procedure get_shop_cart
 *
 */


/**
 * returns the total of a given purchase by uf - cart. 
 * Important: the unit_price_stamp of the shop item
 * already contains IVA and Rev-tax!
 */ 
drop function if exists get_purchase_total|
create function get_purchase_total(the_cart_id int)
returns float(10,2)
reads sql data
begin
  declare total_price decimal(10,2);
  
  select 
	sum( CAST(si.quantity * si.unit_price_stamp as decimal(10,2)) ) into total_price
  from 
	aixada_shop_item si
  where
	si.cart_id = the_cart_id;
      
  return total_price;
end|




delimiter ;

delimiter |

drop procedure if exists most_bought_products|
create procedure most_bought_products(in the_year int)
begin
  select prod.name, sum(i.quantity) s 
  from aixada_product prod 
  left join aixada_order_item i 
  on prod.id=i.product_id 
  where i.date_for_order between concat(the_year, '-01-01') and concat(the_year, '-12-30') 
  group by prod.name 
  order by s desc 
  limit 10;
end|

drop procedure if exists least_bought_products|
create procedure least_bought_products(in the_year int)
begin
  select prod.name, sum(i.quantity) s 
  from aixada_product prod 
  left join aixada_order_item i 
  on prod.id=i.product_id 
  where i.date_for_order between concat(the_year, '-01-01') and concat(the_year, '-12-30') 
  group by prod.name 
  order by s asc 
  limit 10;
end|

drop function if exists first_order|
create function first_order(the_uf int)
returns date
reads sql data
begin
  declare first_date date;
  select date_for_order 
  into first_date
  from aixada_order_item 
  where uf_id=the_uf 
  and date_for_order > '1234-01-23'
  order by date_for_order 
  limit 1;
  return first_date;
end|

drop function if exists last_order|
create function last_order(the_uf int)
returns date
reads sql data
begin
  declare last_date date;
  select date_for_order 
  into last_date
  from aixada_order_item 
  where uf_id=the_uf 
  order by date_for_order desc
  limit 1;
  return last_date;
end|

drop procedure if exists active_times|
create procedure active_times()
begin
  declare birth date;
  select first_order(1) into birth;
  select 
    id, 
    name,
    datediff(first_order(id), birth) fo, 
    datediff(last_order(id),birth) lo
  from aixada_uf
  order by fo, id;
end|

drop procedure if exists uf_weekly_orders|
create procedure uf_weekly_orders(in the_uf int)
begin
  declare birth date;
  select first_order(1) into birth;
  select 
     datediff(i.date_for_order, birth) / 7 as order_week,
     sum(i.quantity * p.unit_price) as total_price
  from aixada_order_item i
  left join aixada_product p
  on i.product_id = p.id
  where uf_id = the_uf
  and date_for_order > '1234-01-23'
  group by date_for_order
  order by date_for_order;
end|

drop procedure if exists provider_weekly_orders|
create procedure provider_weekly_orders(in the_provider int)
begin
  declare birth date;
  select first_order(1) into birth;
  select 
     datediff(i.date_for_order, birth) / 7 as order_week,
     sum(i.quantity * prod.unit_price) as total_price
  from aixada_provider prov
  left join aixada_product prod
  on prod.provider_id = prov.id
  left join aixada_order_item i
  on i.product_id = prod.id
  where prov.id = the_provider
  and date_for_order > '1234-01-23'
  group by date_for_order
  order by date_for_order;
end|

drop procedure if exists product_weekly_orders|
create procedure product_weekly_orders(in the_product int)
begin
  declare birth date;
  select first_order(1) into birth;
  select 
     datediff(i.date_for_order, birth) / 7 as order_week,
     sum(i.quantity * prod.unit_price) as total_price
  from aixada_product prod
  left join aixada_order_item i
  on i.product_id = prod.id
  where prod.id = the_product
  and date_for_order > '1234-01-23'
  group by date_for_order
  order by date_for_order;
end|

drop procedure if exists uf_weekly_balance|
create procedure uf_weekly_balance()
begin
  declare start_date date default date_add(sysdate(), interval -1 day);
  select 
    floor(datediff(sysdate(), ts) / 7) as week,
    date(ts) as date,
    account_id - 1000 as uf, 
    balance 
  from aixada_account
  where account_id between 1000 and 2000
    and ts < start_date
  order by ts desc, uf
  limit 1000;
end|

drop procedure if exists product_prices_in_year|
create procedure product_prices_in_year(in the_product_id int, in the_year int)
begin
  declare first_day date default makedate(the_year, 1);
  select
     round(datediff(ts, first_day)/7.0, 1) as week, 
     current_price as price
  from 
     aixada_price
  where
     product_id = the_product_id and
     year(ts) = the_year;
end|
    

delimiter ;
delimiter |


/*******************************************
 * UF STUFF
 *******************************************/


/**
 * 	retrieves list of active/nonactive ufs
 */
drop procedure if exists get_uf_listing|
create procedure get_uf_listing(in incl_inactive boolean)
begin
	declare wherec varchar(255) default '';
	set wherec = if(incl_inactive=1,"","where active = 1"); 
		
	set @q = concat("select
			*
		from 
			aixada_uf 
		", wherec , "
		order by
			id
		desc");
		
	prepare st from @q;
  	execute st;
  	deallocate prepare st;
end |


/**
 * creates a new uf
 */
drop procedure if exists create_uf|
create procedure create_uf(in the_name varchar(255), in the_mentor_uf int, in the_operator_id int)
begin
   declare last_id int;
   start transaction;
   
   select
      max(id) 
	into 
		last_id 
    from 
    	aixada_uf;

   	insert into 
   		aixada_uf (id, name, active, mentor_uf) 
    values 
    	(last_id + 1, the_name, 1, the_mentor_uf);

    	 
   	insert into 
   		aixada_account (account_id, quantity, payment_method_id, description, operator_id, ts, balance) 
   	values (
   		1000 + last_id + 1, 
   		0,
   		11,
   		'account setup',
   		the_operator_id,
   		now(),
   		0); 
   
   	commit;
   
   	select 
   		* 
   	from 
   		aixada_uf 
   	where 
   		id = last_id + 1;
end|


/**
 * edit existing uf
 */
drop procedure if exists update_uf|
create procedure update_uf(in the_uf_id int, in the_name varchar(255), in is_active tinyint, in the_mentor_uf int)
begin
	update 
  		aixada_uf
  	set 
  		name = the_name, active = is_active, mentor_uf = the_mentor_uf
  	where 
  		id = the_uf_id;
 
  		
   -- update also the active state of the members of this uf --
   update 
   		aixada_member
   	set
   		active = is_active
   	where
   		uf_id = the_uf_id; 
   		
end|



/*******************************************
 * MEMBER STUFF
 *******************************************/
drop procedure if exists get_member_listing|
create procedure get_member_listing(in incl_inactive boolean)
begin
	
	declare wherec varchar(255) default '';
	set wherec = if(incl_inactive=1,"","and mem.active = 1"); 

	set @q = concat("select
		mem.*,
		uf.id as uf_id,
		uf.name as uf_name, 
		u.email, 
		u.last_successful_login
	from 
		aixada_member mem,
		aixada_uf uf, 
		aixada_user u
	where
		u.member_id = mem.id
		and mem.uf_id is not null
		and u.uf_id = uf.id
		",wherec,"
	order by
		uf.id desc");
		
	prepare st from @q;
  	execute st;
  	deallocate prepare st;
  
	
end|


/**
 * 	retrieves detailed member info either for individual member
 *  or for all members of uf. 
 */
drop procedure if exists get_member_info|
create procedure get_member_info (in the_member_id int, in the_uf_id int)
begin
	
	declare wherec varchar(255) default '';
	set wherec = if(the_uf_id>0, concat("uf.id=", the_uf_id),concat("mem.id=",the_member_id)); 
	
	
	
	set @q = concat("select
			mem.*,
			uf.id as uf_id, 
			uf.name as uf_name, 
			uf.mentor_uf,
			u.id as user_id,
			u.login, 
			u.email,
			u.last_login_attempt,
        	u.last_successful_login,
        	u.language,
			u.gui_theme,
        	get_roles_of_member(mem.id) as roles,
    		get_providers_of_member(mem.id) as providers,
    		get_products_of_member(mem.id) as products
    	from 
    		aixada_member mem, 
    		aixada_user u, 
    		aixada_uf uf
    	where
			",wherec,"
			and mem.uf_id = uf.id	    	
			and u.member_id = mem.id");
	
	prepare st from @q;
  	execute st;
  	deallocate prepare st;
	
end|


/**
 * edit members. new members cannot be created without 
 * existing user. 
 */
drop procedure if exists update_member|
create procedure update_member(in the_member_id int, 
 	 	in the_custom_ref varchar(100),
		in the_name varchar(255), 
		in the_nif varchar(15),
        in the_address varchar(255), 
        in the_city varchar(255), 
        in the_zip varchar(10), 
        in the_phone1 varchar(50), 
        in the_phone2 varchar(50),
        in the_web	varchar(255),
        in the_notes text,
        in the_active boolean, 
        in the_participant boolean,
        in the_adult boolean,
        in the_language char(5),
        in the_gui_theme varchar(50),
        in the_email varchar(100)
)
begin
	
	-- update member info -- 
	update 
  		aixada_member 
  	set
  		custom_member_ref = the_custom_ref,
  		nif = the_nif,
       	name = the_name, 
       	address = the_address, 
       	zip = the_zip, 
       	city = the_city, 
       	phone1 = the_phone1, 
       	phone2 = the_phone2, 
       	web = the_web,
       	notes = the_notes,
       	active = the_active,
       	participant = the_participant,
       	adult = the_adult        
  	where 
  		id = the_member_id;
  		
  		
  	-- update related user fields -- 	
  	update 
  		aixada_user u
  	set
  		u.email = the_email, 
  		u.language = the_language,
  		u.gui_theme = the_gui_theme
  	where 
  		u.member_id = the_member_id; 
  	
end|



/**
 * removes a member from its uf. 
 */
drop procedure if exists remove_member_from_uf|
create procedure remove_member_from_uf(in the_member_id int)
begin
	update 
		aixada_member
	set
		uf_id = null
	where 
		id = the_member_id; 
	
end|


drop procedure if exists del_user_member|
create procedure del_user_member(in the_member_id int)
begin
	
	
	
		delete from 
			aixada_user_role
		where
			user_id = the_member_id; 
	
		delete from 
			aixada_user 
		where 
			member_id = the_member_id; 
		
		delete from 
			aixada_member
		where
			id = the_member_id; 
	
	
	

end|


/**
 * returns all members that are currently not assigned to any UF
 */
drop procedure if exists get_unassigned_members|
create procedure get_unassigned_members()
begin
	select
		mem.*,
		u.email
	from
		aixada_member mem, 
		aixada_user u
	where 
		mem.uf_id is null
		and u.member_id = mem.id;
end|


drop procedure if exists search_members|
create procedure search_members(in searchStr varchar(255))
begin
	
	select 
		*
	from 
		aixada_member
	where
		name like concat('%', searchStr, '%')		
	union distinct
	select
		mem.*
	from 
		aixada_member mem,
		aixada_user u
	where 
		u.login like concat('%', searchStr, '%')
		and u.member_id = mem.id;

	
end|



/*******************************************
 * USER STUFF
 *******************************************/


/**
 * creates a new user and member. the member does not need an uf
 * at this point but could be assigned in a second step. 
 */
drop procedure if exists new_user_member|
create procedure new_user_member(
        in the_login varchar(50), 
        in the_password varchar(255),
        in the_uf_id int,
        in the_custom_ref varchar(100),
		in the_name varchar(255), 
		in the_nif varchar(15),
        in the_address varchar(255), 
        in the_city varchar(255), 
        in the_zip varchar(10), 
        in the_phone1 varchar(50), 
        in the_phone2 varchar(50),
        in the_web	varchar(255),
        in the_notes text,
        in the_active boolean, 
        in the_participant boolean,
        in the_adult boolean,
        in the_language char(5),
        in the_gui_theme varchar(50),
        in the_email varchar(100)
        )
begin
  declare the_user_id int;
  start transaction;
  	
  	select 
  		max(id)+1 
  	into 
  		the_user_id
  	from 
  		aixada_user 
  	where 
  		id<1000;
  	
  	
  	if the_user_id=0 or isnull(the_user_id) then set the_user_id=1; end if;
  
  	insert into 
  		aixada_member (id, custom_member_ref, uf_id, nif, name, address, zip, city, phone1, phone2, web, notes, active, participant, adult) 
  	values 
  		(the_user_id, the_custom_ref, the_uf_id, the_nif, the_name, the_address, the_zip, the_city, the_phone1, the_phone2, the_web, the_notes, the_active, the_participant, the_adult);
  
  
  	insert into 
  		aixada_user (id, login, password, email, uf_id, member_id, language, gui_theme, created_on) 
  	values 
 		(the_user_id, the_login, the_password, the_email, the_uf_id, the_user_id, the_language, the_gui_theme, sysdate());
  	
  	
  	insert into 
  		aixada_user_role (user_id, role) 
  	values 
     ( the_user_id, 'Consumer' ),
     ( the_user_id, 'Checkout');
  
	commit;
end|



/**
 * update an existing password
 */
drop procedure if exists update_password|
create procedure update_password(in the_user_id int, in new_password varchar(255))
begin
  update 
  	aixada_user
  set 
  	password = new_password
  where 
  	id = the_user_id;
end|



/**
 * this procedures returns the credentials for given user
 * the actual check if pwd matches with login / or user_id is performed
 * on the php side of things. 
 * NOTE: this procedure always returns values as long as the given user_id or login 
 * exists!!!
 */
drop procedure if exists retrieve_credentials|
create procedure retrieve_credentials(in user_login varchar(50), in the_user_id int)
begin	
	
	declare the_login varchar(50) default ''; 
	
	-- if we provide a user id, use the login nevertheless -- 
	if (the_user_id > 0 and user_login = '') then
		set the_login = (select 
			login
		from 
			aixada_user
		where
			id = the_user_id);
	else  
	
		set the_login = user_login;
	end if; 
	
	-- update as last login attempt -- 
	update 
   		aixada_user 
   	set 
   		last_login_attempt = sysdate() 
   	where 
   		login=the_login;
   	
   
   	-- check credentials, see if user is active --
	select 
   		u.*,
   		mem.active as is_active_member,
   		pv.active as is_active_provider
   	from 
   		aixada_user u
   	left join
   		aixada_member mem on u.member_id = mem.id
   	left join
   		aixada_provider pv on u.provider_id = pv.id
   	where 
   		u.login=the_login; 
end|





drop procedure if exists register_special_user|
create procedure register_special_user(
        in the_login varchar(50), 
        in the_password varchar(255),
        in the_language char(5),
        in the_uf_name varchar(255)
        )
begin
  declare the_user_id int default 1;
  start transaction;

  insert into aixada_uf (
     id, name
  ) values (
     the_user_id, the_uf_name
  );

  delete from aixada_member where id=1;

  insert into aixada_member (
     id, name, uf_id
  ) values (
     the_user_id, the_login, the_user_id
  ) on duplicate key update name=the_login;

  insert into aixada_user (
      id, login, password, email, uf_id, member_id, language, created_on
  ) values (
      the_user_id, the_login, the_password, '', the_user_id, the_user_id, the_language, sysdate()
  ) on duplicate key update login=the_login, password=the_password, member_id=the_user_id, language=the_language, created_on=sysdate();

  insert into aixada_user_role (
      user_id, role
  ) values 
     (the_user_id, 'Consumer'),
     (the_user_id, 'Checkout'),
     (the_user_id, 'Hacker Commission');
  commit;
end|





drop procedure if exists remove_user_roles|
create procedure remove_user_roles(in the_user_id int)
begin
  delete from aixada_user_role
  where user_id = the_user_id;
end|

drop procedure if exists add_user_role|
create procedure add_user_role(in the_user_id int, in the_role varchar(100))
begin
  insert into aixada_user_role (
     user_id, role
  ) values (
     the_user_id, the_role
  );
end|

drop procedure if exists update_user|
create procedure update_user(
        in the_id int,
        in the_login varchar(50), 
        in the_password varchar(255), 
        in the_email varchar(100),
        in the_uf_id int,
        in the_member_id int,
        in the_provider_id int,
        in the_language char(5),
        in the_color_scheme tinyint)
begin
  update aixada_user 
  set 
        login = the_login,
        password = if(the_password='', password, the_password),
        email = the_email,
        uf_id = the_uf_id,
        member_id = the_member_id,
        provider_id = the_provider_id,
        language = the_language,
        color_scheme = the_color_scheme
  where id = the_id;
  select id 
  from aixada_user 
  where id = the_id;
end|


/**
 * assigns existing member to uf
 */
drop procedure if exists assign_member_to_uf|
create procedure assign_member_to_uf(in the_member_id int, in the_uf_id int)
begin
  update 
  	aixada_user
  set 
  	uf_id = the_uf_id 
  where 
  	member_id = the_member_id;

  update 
  	aixada_member 
  set 
  	uf_id = the_uf_id
  where 
  	id = the_member_id;
end|





drop procedure if exists get_users|
create procedure get_users()
begin
  select 
        u.id, concat(" UF ", u.uf_id, " ", m.name) as name
  from aixada_user u
  left join aixada_member m
  on u.member_id = m.id
  left join aixada_uf uf
  on u.uf_id = uf.id
  where 
        m.active = true and
        uf.active=true
  order by u.uf_id; 
end|

drop procedure if exists get_active_roles|
create procedure get_active_roles(in the_user_id int)
begin
  select 
        r.role
  from aixada_user u
  left join aixada_user_role r
  on u.id = r.user_id
  where u.id = the_user_id; 
end|

drop procedure if exists get_active_users_for_role|
create procedure get_active_users_for_role(in the_role varchar(100))
begin
  select r.user_id, concat(m.id, ' UF ', m.uf_id, ' ', m.name) as name
  from aixada_user_role r
  left join aixada_member m
  on r.user_id = m.id
  where r.user_id between 1 and 999
    and r.role = the_role
    and m.active=1
  order by m.uf_id;
end|

drop procedure if exists get_inactive_users_for_role|
create procedure get_inactive_users_for_role(in the_role varchar(100))
begin
  select distinct r1.user_id, concat(m.id, ' UF ', m.uf_id, ' ', m.name) as name
  from aixada_user_role r1 
  left join aixada_member m
  on r1.user_id = m.id
  where user_id between 1 and 999
    and m.active=1
    and user_id not in 
     (select user_id 
      from aixada_user_role r2 
      where role=the_role)
  order by m.uf_id;
end|





/**
 * utility functions to extract roles of member, 
 * providers member is responsible for, 
 * and products responsible for.  
 */
drop function if exists get_roles_of_member|
create function get_roles_of_member(the_member_id int)
returns varchar(255)
reads sql data
begin
  declare roles varchar(255);
  select group_concat(role separator ', ') 
  into roles
  from aixada_user_role
  where user_id = the_member_id;
  return roles;
end|

drop function if exists get_providers_of_member|
create function get_providers_of_member(the_member_id int)
returns varchar(255)
reads sql data
begin
  declare providers varchar(255);
  select group_concat(distinct p.name  separator ', ') 
  into providers
  from aixada_member m
  left join aixada_provider p
  on m.uf_id = p.responsible_uf_id
  where m.id = the_member_id;
  return providers;
end|

drop function if exists get_products_of_member|
create function get_products_of_member(the_member_id int)
returns varchar(255)
reads sql data
begin
  declare products varchar(255);
  select group_concat(distinct p.name  separator ', ') 
  into products
  from aixada_member m
  left join aixada_product p
  on m.uf_id = p.responsible_uf_id
  where m.id = the_member_id;
  return products;
end|





delimiter ;
delimiter |


/**
 * returns a list of all active ufs and the number of their non-validated
 * shoppping carts.
 */
drop procedure if exists get_uf_listing_cart_count|
create procedure get_uf_listing_cart_count()
begin
	
	select
		uf.id as uf_id,
		uf.name as uf_name,
		count(if(c.ts_validated=0,1,NULL)) as non_validated_carts,
		count(if(c.ts_validated>0,1,NULL)) as validated_carts
	from 
		aixada_uf uf
	left join 
		aixada_cart c on c.uf_id = uf.id
	where
		uf.active = 1
	group by
		uf.id
	order by
		uf.id;
	
end|



/**
 *  returns all ufs with unvalidated carts for given date
 */
drop procedure if exists get_ufs_for_validation|
create procedure get_ufs_for_validation(in tmp_shop_date date)
begin
  
	declare the_shop_date date default tmp_shop_date;
	
  	if (the_shop_date=0) then 
    	select 
    		date_for_shop
    	into 
    		the_shop_date
    	from 
    		aixada_cart
    	where 
    		date_for_shop >= date(sysdate()) 
    	limit 1;
  	end if;
  
  	
	select distinct 
  		u.id, u.name
  	from 
  		aixada_cart c
  	left join 
  		aixada_uf u on u.id = c.uf_id
  	where 
  		c.date_for_shop = the_shop_date
  		and c.ts_validated = 0
  	order by 
  		u.id;
end|


/**
 * validates a shopping cart by setting the ts_validated to the current datetime
 * and registers the money in the corresponding accounts.  
 */
drop procedure if exists validate_shop_cart|
create procedure validate_shop_cart(in the_cart_id int, in the_op_id int, in the_desc_pay varchar(50), in use_transaction boolean)
begin
  declare current_balance decimal(10,2) default 0.0;
  declare total_price decimal(10,2) default 0.0;
  declare the_account_id int;
  
  if (use_transaction is true) then
    start transaction;
  end if;
  
  set the_account_id = (
	  select
	  	(uf_id + 1000)
	  from 
	  	aixada_cart
	  where
	  	id = the_cart_id
	  limit 1); 
  	
  -- do the actual validation: set timestamp! --	
  update 
  	aixada_cart
  set 
  	ts_validated = sysdate(),
  	ts_last_saved = now(),
    operator_id = the_op_id
   where 
   	id = the_cart_id
    and ts_validated = 0;

 
  update 
  	aixada_product p,
  	aixada_shop_item si
  set 
  	p.stock_actual = p.stock_actual - si.quantity,
	p.delta_stock  = p.delta_stock  - si.quantity
  where
  	si.cart_id = the_cart_id
  	and si.product_id = p.id;
  	

  select 
  	balance 
  into 
  	current_balance
  from 
  	aixada_account
  where 
  	account_id = the_account_id
  order by ts desc
  limit 1; 

  select 
  	get_purchase_total(the_cart_id)
  into 
  	total_price;


  /** new entry into account **/
  insert into 
  	aixada_account (account_id, quantity, payment_method_id, description, operator_id, balance) 
  select 
   the_account_id,
    - total_price,
    6,
    concat(the_desc_pay, the_cart_id),
    the_op_id,
    current_balance - total_price;
  if (use_transaction is true) then
   commit;
  end if;

end|








/*drop procedure if exists undo_validate|
create procedure undo_validate(in the_uf_id int, in the_ts datetime, in the_operator int)
begin
  declare the_shop_date date, 
    uf_balance decimal(10,2), 
    caixa_balance decimal(10,2), 
    shop_amount decimal(10,2);

  start transaction;

  select date_for_shop 
  into the_shop_date
  from aixada_shop_item where uf_id = the_uf_id and ts_validated = the_ts
  limit 1;

  start transaction;
  update aixada_shop_item 
  set ts_validated = 0
  where uf_id = the_uf_id
    and date_for_shop = the_shop_date;

  select balance 
  into uf_balance
  from aixada_account_balance
  where account_id = 1000 + the_uf_id;

  select balance 
  into caixa_balance
  from aixada_account_balance
  where account_id = -3;


  select total_
  into caixa_balance
  from aixada_account_balance
  where account_id = -3;

  insert into aixada_account (
    account_id, quantity, description, operator_id, balance    
  ) values (
    1000 + the_uf_id, 
  )

 commit;

  select date_for_shop 
  into the_shop_date
  from aixada_shop_item where uf_id = the_uf_id and ts_validated = the_ts
  limit 1;

  update aixada_shop_item 
  set ts_validated = 0
  where uf_id = the_uf_id
    and date_for_shop = the_shop_date;

  select balance 
  into uf_balance
  from aixada_account_balance
  where account_id = 1000 + the_uf_id;

  select balance 
  into caixa_balance
  from aixada_account_balance
  where account_id = -3;


  select total_
  into caixa_balance
  from aixada_account_balance
  where account_id = -3;

  insert into aixada_account (
    account_id, quantity, description, operator_id, balance    
  ) values (
    1000 + the_uf_id, 
  )

 commit;
end|
*/


delimiter ;
/* 
 * The contents of this file are generated automatically. 
 * Do not edit it, but instead run
 * php make_canned_responses.php
 */
delimiter |

drop procedure if exists aixada_account_list_all_query|
create procedure aixada_account_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter text)
begin
  set @q = "select
      aixada_account.id,
      aixada_account.account_id,
      aixada_account.quantity,
      aixada_account.payment_method_id,
      aixada_payment_method.description as payment_method,
      aixada_account.currency_id,
      aixada_currency.name as currency,
      aixada_account.description,
      aixada_account.operator_id,
      aixada_account.ts,
      aixada_account.balance 
    from aixada_account 
    left join aixada_payment_method as aixada_payment_method on aixada_account.payment_method_id=aixada_payment_method.id
    left join aixada_currency as aixada_currency on aixada_account.currency_id=aixada_currency.id";
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_account_desc_list_all_query|
create procedure aixada_account_desc_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter text)
begin
  set @q = "select
      aixada_account_desc.id,
      aixada_account_desc.description,
      aixada_account_desc.account_type,
      aixada_account_desc.active 
    from aixada_account_desc ";
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_cart_list_all_query|
create procedure aixada_cart_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter text)
begin
  set @q = "select
      aixada_cart.id,
      aixada_cart.name,
      aixada_cart.uf_id,
      aixada_uf.name as uf_name,
      aixada_cart.date_for_shop,
      aixada_cart.operator_id,
      aixada_cart.ts_validated,
      aixada_cart.ts_last_saved 
    from aixada_cart 
    left join aixada_uf as aixada_uf on aixada_cart.uf_id=aixada_uf.id";
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_currency_list_all_query|
create procedure aixada_currency_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter text)
begin
  set @q = "select
      aixada_currency.id,
      aixada_currency.name,
      aixada_currency.one_euro 
    from aixada_currency ";
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_incident_list_all_query|
create procedure aixada_incident_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter text)
begin
  set @q = "select
      aixada_incident.id,
      aixada_incident.subject,
      aixada_incident.incident_type_id,
      aixada_incident_type.description as incident_type,
      aixada_incident.operator_id,
      aixada_incident.details,
      aixada_incident.priority,
      aixada_incident.ufs_concerned,
      aixada_incident.commission_concerned,
      aixada_incident.provider_concerned,
      aixada_incident.ts,
      aixada_incident.status 
    from aixada_incident 
    left join aixada_incident_type as aixada_incident_type on aixada_incident.incident_type_id=aixada_incident_type.id";
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_incident_type_list_all_query|
create procedure aixada_incident_type_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter text)
begin
  set @q = "select
      aixada_incident_type.id,
      aixada_incident_type.description,
      aixada_incident_type.definition 
    from aixada_incident_type ";
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_iva_type_list_all_query|
create procedure aixada_iva_type_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter text)
begin
  set @q = "select
      aixada_iva_type.id,
      aixada_iva_type.name,
      aixada_iva_type.percent,
      aixada_iva_type.description 
    from aixada_iva_type ";
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_member_list_all_query|
create procedure aixada_member_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter text)
begin
  set @q = "select
      aixada_member.id,
      aixada_member.custom_member_ref,
      aixada_member.uf_id,
      aixada_uf.name as uf_name,
      aixada_member.name,
      aixada_user.email as email,
      aixada_member.address,
      aixada_member.nif,
      aixada_member.zip,
      aixada_member.city,
      aixada_member.phone1,
      aixada_member.phone2,
      aixada_member.web,
      aixada_member.bank_name,
      aixada_member.bank_account,
      aixada_member.picture,
      aixada_member.notes,
      aixada_member.active,
      aixada_member.participant,
      aixada_member.adult,
      aixada_member.ts 
    from aixada_member 
    left join aixada_uf as aixada_uf on aixada_member.uf_id=aixada_uf.id
    left join aixada_user as aixada_user on aixada_user.member_id=aixada_member.id";
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_order_list_all_query|
create procedure aixada_order_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter text)
begin
  set @q = "select
      aixada_order.id,
      aixada_order.provider_id,
      aixada_provider.name as provider,
      aixada_order.date_for_order,
      aixada_order.ts_sent_off,
      aixada_order.date_received,
      aixada_order.date_for_shop,
      aixada_order.total,
      aixada_order.notes,
      aixada_order.revision_status,
      aixada_order.delivery_ref,
      aixada_order.payment_ref 
    from aixada_order 
    left join aixada_provider as aixada_provider on aixada_order.provider_id=aixada_provider.id";
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_order_item_list_all_query|
create procedure aixada_order_item_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter text)
begin
  set @q = "select
      aixada_order_item.id,
      aixada_order_item.uf_id,
      aixada_uf.name as uf_name,
      aixada_order_item.favorite_cart_id,
      aixada_cart.name as favorite_cart,
      aixada_order_item.order_id,
      aixada_order_item.unit_price_stamp,
      aixada_order_item.iva_percent,
      aixada_order_item.rev_tax_percent,
      aixada_order_item.date_for_order,
      aixada_order_item.product_id,
      aixada_product.name as product,
      aixada_order_item.quantity,
      aixada_order_item.ts_ordered 
    from aixada_order_item 
    left join aixada_uf as aixada_uf on aixada_order_item.uf_id=aixada_uf.id
    left join aixada_cart as aixada_cart on aixada_order_item.favorite_cart_id=aixada_cart.id
    left join aixada_product as aixada_product on aixada_order_item.product_id=aixada_product.id";
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_order_to_shop_list_all_query|
create procedure aixada_order_to_shop_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter text)
begin
  set @q = "select
      aixada_order_to_shop.order_item_id,
      aixada_order_to_shop.uf_id,
      aixada_uf.name as uf_name,
      aixada_order_to_shop.order_id,
      aixada_order_to_shop.unit_price_stamp,
      aixada_order_to_shop.iva_percent,
      aixada_order_to_shop.rev_tax_percent,
      aixada_order_to_shop.product_id,
      aixada_product.name as product,
      aixada_order_to_shop.quantity,
      aixada_order_to_shop.arrived,
      aixada_order_to_shop.revised,
      aixada_order_to_shop.aixada_order_to_shop_ibfk_1,
      aixada_order_to_shop.aixada_order_to_shop_ibfk_2,
      aixada_order_to_shop.aixada_order_to_shop_ibfk_3 
    from aixada_order_to_shop 
    left join aixada_uf as aixada_uf on aixada_order_to_shop.uf_id=aixada_uf.id
    left join aixada_product as aixada_product on aixada_order_to_shop.product_id=aixada_product.id";
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_orderable_type_list_all_query|
create procedure aixada_orderable_type_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter text)
begin
  set @q = "select
      aixada_orderable_type.id,
      aixada_orderable_type.description 
    from aixada_orderable_type ";
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_payment_method_list_all_query|
create procedure aixada_payment_method_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter text)
begin
  set @q = "select
      aixada_payment_method.id,
      aixada_payment_method.description,
      aixada_payment_method.details 
    from aixada_payment_method ";
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_price_list_all_query|
create procedure aixada_price_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter text)
begin
  set @q = "select
      aixada_price.product_id,
      aixada_product.name as product,
      aixada_price.ts,
      aixada_price.current_price,
      aixada_price.operator_id 
    from aixada_price 
    left join aixada_product as aixada_product on aixada_price.product_id=aixada_product.id";
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_product_list_all_query|
create procedure aixada_product_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter text)
begin
  set @q = "select
      aixada_product.id,
      aixada_product.provider_id,
      aixada_provider.name as provider,
      aixada_product.name,
      aixada_product.description,
      aixada_product.barcode,
      aixada_product.custom_product_ref,
      aixada_product.active,
      aixada_uf.id as responsible_uf_id,
aixada_uf.name as responsible_uf_name,
      aixada_product.orderable_type_id,
      aixada_orderable_type.description as orderable_type,
      aixada_product.order_min_quantity,
      aixada_product.category_id,
      aixada_product_category.description as category,
      aixada_product.rev_tax_type_id,
      aixada_rev_tax_type.name as rev_tax_type,
      aixada_product.iva_percent_id,
      aixada_iva_type.name as iva_percent,
      aixada_product.unit_price,
      aixada_product.unit_measure_order_id,
      aixada_unit_measure_order.name as unit_measure_order,
      aixada_product.unit_measure_shop_id,
      aixada_unit_measure_shop.name as unit_measure_shop,
      aixada_product.stock_min,
      aixada_product.stock_actual,
      aixada_product.delta_stock,
      aixada_product.description_url,
      aixada_product.picture,
      aixada_product.ts 
    from aixada_product 
    left join aixada_provider as aixada_provider on aixada_product.provider_id=aixada_provider.id
    left join aixada_uf as aixada_uf on aixada_product.responsible_uf_id=aixada_uf.id
    left join aixada_orderable_type as aixada_orderable_type on aixada_product.orderable_type_id=aixada_orderable_type.id
    left join aixada_product_category as aixada_product_category on aixada_product.category_id=aixada_product_category.id
    left join aixada_rev_tax_type as aixada_rev_tax_type on aixada_product.rev_tax_type_id=aixada_rev_tax_type.id
    left join aixada_iva_type as aixada_iva_type on aixada_product.iva_percent_id=aixada_iva_type.id
    left join aixada_unit_measure as aixada_unit_measure_order on aixada_product.unit_measure_order_id=aixada_unit_measure_order.id
    left join aixada_unit_measure as aixada_unit_measure_shop on aixada_product.unit_measure_shop_id=aixada_unit_measure_shop.id";
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_product_category_list_all_query|
create procedure aixada_product_category_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter text)
begin
  set @q = "select
      aixada_product_category.id,
      aixada_product_category.description 
    from aixada_product_category ";
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_product_orderable_for_date_list_all_query|
create procedure aixada_product_orderable_for_date_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter text)
begin
  set @q = "select
      aixada_product_orderable_for_date.id,
      aixada_product_orderable_for_date.product_id,
      aixada_product.name as product,
      aixada_product_orderable_for_date.date_for_order,
      aixada_product_orderable_for_date.closing_date 
    from aixada_product_orderable_for_date 
    left join aixada_product as aixada_product on aixada_product_orderable_for_date.product_id=aixada_product.id";
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_provider_list_all_query|
create procedure aixada_provider_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter text)
begin
  set @q = "select
      aixada_provider.id,
      aixada_provider.name,
      aixada_provider.contact,
      aixada_provider.address,
      aixada_provider.nif,
      aixada_provider.zip,
      aixada_provider.city,
      aixada_provider.phone1,
      aixada_provider.phone2,
      aixada_provider.fax,
      aixada_provider.email,
      aixada_provider.web,
      aixada_provider.bank_name,
      aixada_provider.bank_account,
      aixada_provider.picture,
      aixada_provider.notes,
      aixada_provider.active,
      aixada_uf.id as responsible_uf_id,
aixada_uf.name as responsible_uf_name,
      aixada_provider.offset_order_close,
      aixada_provider.ts 
    from aixada_provider 
    left join aixada_uf as aixada_uf on aixada_provider.responsible_uf_id=aixada_uf.id";
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_rev_tax_type_list_all_query|
create procedure aixada_rev_tax_type_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter text)
begin
  set @q = "select
      aixada_rev_tax_type.id,
      aixada_rev_tax_type.name,
      aixada_rev_tax_type.description,
      aixada_rev_tax_type.rev_tax_percent 
    from aixada_rev_tax_type ";
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_shop_item_list_all_query|
create procedure aixada_shop_item_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter text)
begin
  set @q = "select
      aixada_shop_item.id,
      aixada_shop_item.cart_id,
      aixada_cart.name as cart,
      aixada_shop_item.order_item_id,
      aixada_shop_item.unit_price_stamp,
      aixada_shop_item.product_id,
      aixada_product.name as product,
      aixada_shop_item.quantity,
      aixada_shop_item.iva_percent,
      aixada_shop_item.rev_tax_percent 
    from aixada_shop_item 
    left join aixada_cart as aixada_cart on aixada_shop_item.cart_id=aixada_cart.id
    left join aixada_product as aixada_product on aixada_shop_item.product_id=aixada_product.id";
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_stock_movement_list_all_query|
create procedure aixada_stock_movement_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter text)
begin
  set @q = "select
      aixada_stock_movement.id,
      aixada_stock_movement.product_id,
      aixada_product.name as product,
      aixada_stock_movement.operator_id,
      aixada_stock_movement.movement_type_id,
      aixada_stock_movement_type.name as movement_type,
      aixada_stock_movement.amount_difference,
      aixada_stock_movement.description,
      aixada_stock_movement.resulting_amount,
      aixada_stock_movement.ts 
    from aixada_stock_movement 
    left join aixada_product as aixada_product on aixada_stock_movement.product_id=aixada_product.id
    left join aixada_stock_movement_type as aixada_stock_movement_type on aixada_stock_movement.movement_type_id=aixada_stock_movement_type.id";
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_stock_movement_type_list_all_query|
create procedure aixada_stock_movement_type_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter text)
begin
  set @q = "select
      aixada_stock_movement_type.id,
      aixada_stock_movement_type.name,
      aixada_stock_movement_type.description 
    from aixada_stock_movement_type ";
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_uf_list_all_query|
create procedure aixada_uf_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter text)
begin
  set @q = "select
      aixada_uf.id,
      aixada_uf.name,
      aixada_uf.active,
      aixada_uf.created,
      aixada_uf.mentor_uf 
    from aixada_uf ";
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_unit_measure_list_all_query|
create procedure aixada_unit_measure_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter text)
begin
  set @q = "select
      aixada_unit_measure.id,
      aixada_unit_measure.name,
      aixada_unit_measure.unit 
    from aixada_unit_measure ";
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_user_list_all_query|
create procedure aixada_user_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter text)
begin
  set @q = "select
      aixada_user.id,
      aixada_user.login,
      aixada_user.password,
      aixada_user.email,
      aixada_user.uf_id,
      aixada_uf.name as uf_name,
      aixada_user.member_id,
      aixada_member.name as member,
      aixada_user.provider_id,
      aixada_provider.name as provider,
      aixada_user.language,
      aixada_user.gui_theme,
      aixada_user.last_login_attempt,
      aixada_user.last_successful_login,
      aixada_user.created_on 
    from aixada_user 
    left join aixada_uf as aixada_uf on aixada_user.uf_id=aixada_uf.id
    left join aixada_member as aixada_member on aixada_user.member_id=aixada_member.id
    left join aixada_provider as aixada_provider on aixada_user.provider_id=aixada_provider.id";
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_user_role_list_all_query|
create procedure aixada_user_role_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter text)
begin
  set @q = "select
      aixada_user_role.user_id,
      aixada_user_role.role 
    from aixada_user_role ";
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_version_list_all_query|
create procedure aixada_version_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter text)
begin
  set @q = "select
      aixada_version.id,
      aixada_version.module_name,
      aixada_version.version 
    from aixada_version ";
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|


delimiter ;
