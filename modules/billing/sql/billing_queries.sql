delimiter |

/**
 *	create a new bill. this does not create the connection to the 
 *  individual carts. this is done with add_cart. 
 */
drop procedure if exists create_bill|
create procedure create_bill(in the_ref_bill varchar(150), 
							 in the_uf_id int, 
							 in the_operator_id int, 
							 in the_description varchar(255))
begin

	insert into
		aixada_bill (ref_bill, uf_id, operator_id, description, date_for_bill, ts_validated)
	values
		(the_ref_bill, the_uf_id, the_operator_id, the_description, now(), now());

	select last_insert_id();


end| 



/**
 *	
 *	Retrieve all fields of aixada_bill table for given bill id
 */
drop procedure if exists get_bill|
create procedure get_bill (in the_bill_id int)
begin
	select
		*
	from 
		aixada_bill
	where
		id = the_bill_id; 
end|



drop procedure if exists delete_bill|
create procedure delete_bill (in the_bill_id int)
begin

	delete from 
		aixada_bill_rel_cart
	where
		bill_id = the_bill_id;


	delete from 
		aixada_bill
	where
		id = the_bill_id; 

end|



/**
 *	creates the relations between the bill and corresponding carts. 
 */
drop procedure if exists add_cart_to_bill|
create procedure add_cart_to_bill(in the_bill_id int, in the_cart_id int )
begin

	insert into
		aixada_bill_rel_cart (bill_id, cart_id)
	values
		(the_bill_id, the_cart_id);

end| 



/**
 *	retrieve row of given cart. check if validated, the uf it pertains to. 
 */
drop procedure if exists get_cart|
create procedure get_cart(in cart_id int)
begin
	select
		*
	from 
		aixada_cart
	where
		id=cart_id;

end|


/**
 *	retrieves bills in given date range and/or with uf by uf_id
 */
drop procedure if exists get_bills| 
create procedure get_bills(in the_uf_id int, in from_date date, to_date date, in the_limit varchar(255))
begin

	declare wherec varchar(255) default "";
	declare set_limit varchar(255) default ""; 
	
	-- filter by uf_id --
	if (the_uf_id > 0) then
		set wherec = concat(" and b.uf_id = ",the_uf_id);
	end if; 
	
	-- set a limit?
    if (the_limit <> "") then
    	set set_limit = concat("limit ", the_limit);
    end if;
	
	set @q =  concat("select 
		b.*,
		ifnull(mem.name, 'default') as operator,
		uf.name as uf_name,
		(select
				sum(get_purchase_total(c.id))
			from 
				aixada_cart c,
				aixada_bill_rel_cart bc
			where
				bc.cart_id = c.id
				and bc.bill_id = b.id
			) as total
		
	from 
		aixada_bill b,
		aixada_member mem,
		aixada_user u,
		aixada_uf uf 
	where
		b.date_for_bill  between '",from_date,"' and '",to_date,"'
		and b.uf_id = uf.id
		and b.operator_id = u.id
 		and u.member_id = mem.id
 		",wherec,"
 	order by 
		b.id desc
		",set_limit,";"); 
			
	prepare st from @q;
  	execute st;
  	deallocate prepare st;
end|


/**
 *
 *	Calculates the total amount of tax per tax group for given bill 
 */
drop procedure if exists get_tax_groups| 
create procedure get_tax_groups(in the_bill_id int)
begin

	select
		si.iva_percent,
		round(sum(si.quantity * (si.unit_price_stamp / (1+si.rev_tax_percent/100) / (1+si.iva_percent/100) )),2) as total_sale_netto,
		round(sum(si.quantity * (si.unit_price_stamp / (1+si.rev_tax_percent/100) / (1+si.iva_percent/100) ) * (si.iva_percent/100)),2) as iva_sale
	from 
		aixada_cart c, 
		aixada_shop_item si,
		aixada_bill_rel_cart bc
  	where
  		bc.bill_id = the_bill_id
  		and bc.cart_id = c.id
  		and c.id = si.cart_id
  	group by
  		si.iva_percent;

end|


/** 
 *
 *	Returns bill + accounting details (such as member, banc, nif, etc. ) for given bill. 
 */
drop procedure if exists get_bill_accounting_detail|
create procedure get_bill_accounting_detail(in the_bill_id int)
begin
	
	select
		b.id as bill_id,
		b.ref_bill,
		b.description, 
		b.date_for_bill, 
		m.custom_member_ref, 
		m.name as member_name,
		b.uf_id, 
		u.login,
		m.nif, 
		m.bank_name, 
		m.bank_account, 
		(select
				sum(get_purchase_total(c.id))
			from 
				aixada_cart c,
				aixada_bill_rel_cart bc
			where
				bc.cart_id = c.id
				and bc.bill_id = b.id
			) as total
	from 
		aixada_bill b,
		aixada_member m,
		aixada_user u
	where
		b.id = the_bill_id
		and b.uf_id = m.uf_id
		and u.uf_id = m.uf_id
	limit 1;

end|


/**
 *	retrieves product details of carts grouped in this bill
 */
drop procedure if exists get_bill_detail| 
create procedure get_bill_detail(in the_bill_id int)
begin
	
	
  select 
  	b.*,
    p.id as product_id,
    p.name as product_name,
    c.id as cart_id,
	c.date_for_shop,
    si.quantity as quantity,
    si.iva_percent,
    si.rev_tax_percent, 
    si.unit_price_stamp as unit_price,
    p.provider_id,  
    pv.name as provider_name,
    um.unit,
    (si.quantity * si.unit_price_stamp) as total
  from
  	aixada_cart c, 
  	aixada_shop_item si,
  	aixada_product p, 
  	aixada_provider pv, 
  	aixada_unit_measure um,
  	aixada_bill b,
  	aixada_bill_rel_cart bc
  where
  	b.id = the_bill_id
  	and bc.bill_id = b.id
  	and bc.cart_id = c.id
  	and si.cart_id = c.id
  	and si.product_id = p.id
  	and pv.id = p.provider_id
  	and um.id = p.unit_measure_shop_id
  order by c.id, p.provider_id;
end|



/**
 * returns listing of aixada_cart's for given uf and date range. 
 * including the name and uf of the validation operator. 
 * if uf_id is not set (0), then returns for all ufs 
 * This is an almost identical procedure as "get_purchase_listing" in aixada_queries_shop, the difference being
 * that this delivers also the bill_id if available. 
 */
drop procedure if exists get_cart_listing|
create procedure get_cart_listing(in the_uf_id int, in from_date date, in to_date date, in the_limit varchar(255))
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
		uf.name as uf_name, 
		(select 
			bc.bill_id
		from 
			aixada_bill_rel_cart bc
		where
			bc.cart_id = c.id limit 1) as bill_id,
		get_purchase_total(c.id) as purchase_total
	from 
		aixada_cart c, 
		aixada_uf uf
	where
		c.date_for_shop between '",from_date,"' and '",to_date,"'
		",wherec,"
	order by 
		c.date_for_shop desc
		",set_limit,";"); 
			
	prepare st from @q;
  	execute st;
  	deallocate prepare st;
end |


delimiter ; 