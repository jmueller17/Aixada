delimiter |


/**
 * returns listing of aixada_cart's for given uf and date range. 
 * including the name and uf of the validation operator. 
 * if uf_id is not set (0), then returns for all ufs 
 */
drop procedure if exists get_purchase_listing|
create procedure get_purchase_listing(in from_date date, in to_date date, in the_uf_id int)
begin
	
	if (the_uf_id > 0) then
		select 
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
			c.date_for_shop between from_date and to_date
			and c.uf_id = the_uf_id
			and uf.id = the_uf_id
		order by 
			c.date_for_shop desc; 
	else 
		select 
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
			c.date_for_shop between from_date and to_date
			and c.uf_id = uf.id
		order by 
			c.date_for_shop desc;
	
	
	end if; 
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
