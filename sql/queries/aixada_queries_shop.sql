delimiter |


/**
 * returns listing of aixada_cart's for given uf and date range. 
 */
drop procedure if exists get_purchase_listing|
create procedure get_purchase_listing(in from_date date, in to_date date, in the_uf_id int)
begin
	
	select 
		c.id,
		c.uf_id, 
		c.date_for_shop, 
		c.ts_validated, 
		get_purchase_total(c.id) as purchase_total
	from 
		aixada_cart c
	where 
		c.date_for_shop between from_date and to_date
		and c.uf_id = the_uf_id; 
end |



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
