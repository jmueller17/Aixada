delimiter |


drop procedure if exists get_purchase_total_by_product|
create procedure get_purchase_total_by_product(in from_date date, in to_date date, in the_provider_id int, in the_product_id int)
begin
	
	
	
end|

/**
 * returns the total of sold items for a given provider
 */
drop procedure if exists get_purchase_total_by_provider|
create procedure get_purchase_total_by_provider (in from_date date, in to_date date, in the_provider_id int)
begin
	
	if (the_provider_id > 0) then
	
		select 
			sum(si.quantity * si.unit_price_stamp) as total,
		 	pv.name as provider_name,
		 	the_provider_id as provider_id,
		 	c.date_for_shop
		from 
			aixada_shop_item si, 
			aixada_provider pv,
			aixada_product p,
			aixada_cart c
		where
			c.date_for_shop between from_date and to_date
			and c.id = si.cart_id
			and si.product_id = p.id
			and p.provider_id = the_provider_id
			and pv.id = the_provider_id
		group by
			the_provider_id, c.date_for_shop
		order by
			pv.name asc, c.date_for_shop desc; 
	
	else 
	
		select 
			sum(si.quantity * si.unit_price_stamp) as total,
		 	pv.name as provider_name,
		 	pv.id as provider_id,
		 	c.date_for_shop
		from 
			aixada_shop_item si, 
			aixada_provider pv,
			aixada_product p,
			aixada_cart c
		where
			c.date_for_shop between from_date and to_date
			and c.id = si.cart_id
			and si.product_id = p.id
			and p.provider_id = pv.id
		group by
			pv.id, c.date_for_shop
		order by
			pv.name asc, c.date_for_shop desc; 
	end if;
	
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
 * returns the total of a given purchase (cart). 
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
	sum(si.quantity * si.unit_price_stamp) into total_price
  from 
	aixada_shop_item si
  where
	si.cart_id = the_cart_id;
      
  return total_price;
end|




delimiter ;

