delimiter |

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
		
		
	/** do the same for aixada_order **/
	update 
		aixada_order o
	set 
		o.closing_date = the_closing_date
	where 
		o.date_for_order = the_order_date
		and o.provider_id = the_provider_id;
		
end |



/**
 * returns all orders within a certain date range
 */
drop procedure if exists get_orders_listing|
create procedure get_orders_listing(in from_date date, in to_date date, in the_limit int)
begin
	
	declare today date default date(sysdate()); 
	
	select distinct 
		o.id, 
		o.provider_id, 
		o.date_for_order,
		o.date_for_shop,
		o.closing_date,
		datediff(o.closing_date, today) as time_left,
		pv.name as provider_name
	from 
		aixada_order o,
		aixada_provider pv
	where 
		o.date_for_order >= from_date
		and o.date_for_order <= to_date
		and o.provider_id = pv.id
	order by
		o.date_for_order desc
	limit the_limit;
		
end |








/**
 * A query that returns all products corresponding to a given favorite order
 */
drop procedure if exists products_for_favorite_order|
create procedure products_for_favorite_order (in the_uf_id int, in the_favorite_cart_id int)
begin
  select
      p.id,
      p.name,
      p.description,
      p.provider_id,  
      p.category_id, 
      p.unit_price * (1 + p.iva_percent/100) as unit_price, 
      p.unit_measure_order_id,
      rev_tax_percent,
      fi.quantity
  from 
      aixada_product p
      left join aixada_favorite_order_item fi
      on p.id = fi.product_id
      left join aixada_provider pv
      on p.provider_id = pv.id
      left join aixada_rev_tax_type t
      on p.rev_tax_type_id = t.id
  where 
      fi.uf_id = the_uf_id and 
      fi.favorite_order_cart_id = the_favorite_cart_id and
      p.active = 1 and
      pv.active = 1
  order by p.name;				    
end|

drop procedure if exists get_favorite_order_carts|
create procedure get_favorite_order_carts(in the_uf_id int)
begin
   select id, name from aixada_favorite_order_cart 
   where uf_id = the_uf_id;
end|

/**
 * Make a new favorite order cart and output its contents
 */
drop procedure if exists make_favorite_order_cart|
create procedure make_favorite_order_cart(in the_uf_id int, in the_date date, in the_name varchar(255))
begin
  declare cart_id int;
  insert into aixada_favorite_order_cart 
      (uf_id, name)
    values 
      (the_uf_id, the_name);

  select last_insert_id() into cart_id;

  insert into aixada_favorite_order_item 
      (favorite_order_cart_id, uf_id, product_id, quantity, ts_ordered)
    select 
      cart_id, the_uf_id, o.product_id, o.quantity, the_date
    from aixada_order_item o
    where 
      o.date_for_order = the_date and
      o.uf_id = the_uf_id;

  call products_for_favorite_order(the_uf_id, cart_id);
end|

drop procedure if exists delete_favorite_order_cart|
create procedure delete_favorite_order_cart(in the_uf_id int, in cart_id int)
begin
  delete from aixada_favorite_order_item 
  where uf_id = the_uf_id and
        favorite_order_cart_id = cart_id;

  delete from aixada_favorite_order_cart
  where uf_id = the_uf_id and
        id = cart_id;
end|


/**
 *  Move all orders from from_date to to_date.
 *  In case an order already exists at to_date, the
 *  quantity(to_date) is updated with 
 *  max( quantity(from_date), quantity(to_date) ) .
 */

drop procedure if exists move_all_orders|
create procedure move_all_orders(in from_date date, in to_date date)
begin
  /* Start with orders */
  /* first, update existing entries at to_date with the larger quantity */
  update aixada_order_item i1,
    ( select uf_id, product_id, quantity from aixada_order_item
      where date_for_order = from_date ) i2
  set i1.quantity = if(i1.quantity > i2.quantity, i1.quantity, i2.quantity)
  where i1.product_id = i2.product_id
  and i1.uf_id = i2.uf_id
  and i1.date_for_order = to_date;

  /* Then, insert new products into to_date without clobbering what's already there */
  insert ignore into aixada_order_item (
     date_for_order, uf_id, product_id, quantity, ts_ordered      
  ) select * from 
    (select to_date, uf_id, product_id, quantity, ts_ordered      
    from aixada_order_item 
    where date_for_order = from_date ) i1;

  /* ... and remove old orders */
  delete from aixada_order_item
  where date_for_order = from_date;


  /* Then, _almost_ the same code with shop items; the difference is the 
     clause "and ts_validated = 0".
     Ugly but apparently necessary, since mysql doesn't permit variable table names */

  /* first, update existing entries at to_date with the larger quantity */
  update aixada_shop_item i1,
    ( select uf_id, product_id, quantity from aixada_shop_item
      where date_for_shop = from_date ) i2
  set i1.quantity = if(i1.quantity > i2.quantity, i1.quantity, i2.quantity)
  where i1.product_id = i2.product_id
  and i1.uf_id = i2.uf_id
  and i1.date_for_shop = to_date;

  /* Then, insert new products into to_date without clobbering what's already there */
  insert ignore into aixada_shop_item (
     date_for_shop, uf_id, product_id, quantity, ts_validated
  ) select distinct * from 
    (select to_date, uf_id, product_id, quantity, ts_validated    
    from aixada_shop_item 
    where date_for_shop = from_date 
    and ts_validated = 0) i1;

  /* ... and remove old shops */
  delete from aixada_shop_item
  where date_for_shop = from_date
  and ts_validated = 0;


  /* Finally, activate the products for the new day */
  delete from aixada_product_orderable_for_date 
  where date_for_order = to_date 
    and product_id in 
        (select distinct product_id 
           from aixada_shop_item i
           where i.date_for_shop = to_date);

  insert into aixada_product_orderable_for_date (
     date_for_order, product_id 
  ) select distinct to_date, i.product_id 
           from aixada_shop_item i
           where i.date_for_shop = to_date;
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


/**
 * Convert ordered items to shop items
 */
drop procedure if exists convert_order_to_shop|
create procedure convert_order_to_shop(IN uf int, IN order_date date)
begin
  replace into aixada_shop_item (
    uf_id, date_for_shop, product_id, quantity
  ) select i.uf_id, i.date_for_order, i.product_id, i.quantity
    from aixada_order_item i
    where date_for_order = order_date 
      and uf_id = uf;
end|



delimiter ;
