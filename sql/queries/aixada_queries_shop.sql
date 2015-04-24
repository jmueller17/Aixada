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

