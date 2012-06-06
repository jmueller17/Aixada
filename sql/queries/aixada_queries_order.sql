delimiter |

/*
drop procedure if exists providers_with_active_products_for_order|
create procedure providers_with_active_products_for_order (IN the_order_date date)
begin
  select distinct
    pv.id, 
    pv.name 
  from 
    aixada_provider pv
  left join aixada_product p
    on p.provider_id = pv.id 
  left join aixada_product_orderable_for_date o
    on p.id = o.product_id
  where 
    p.active = 1 
    and pv.active = 1
    and ( 
      p.orderable_type_id = 2 and 
      o.date_for_order = the_order_date
      or p.orderable_type_id = 3
    )
  order by pv.name;
end|
*/


/**
 * A query that returns all product categories
 */
/*
drop procedure if exists product_categories_for_order|
create procedure product_categories_for_order (IN the_order_date date)
begin
  select distinct
    pc.id,
    pc.description
  from 
    aixada_product p
  left join aixada_product_orderable_for_date o
    on o.product_id = p.id
  left join aixada_product_category pc
    on p.category_id = pc.id
  left join aixada_provider pv
    on p.provider_id = pv.id
  where o.date_for_order = the_order_date
    and p.active = 1
    and pv.active = 1
    and ( 
      p.orderable_type_id = 2 and
      o.date_for_order = the_order_date
      or p.orderable_type_id >= 3
    );
end|
*/

/** 
 * A function that returns the total quantity of product
 * the_product_id bought by UF the_uf_id, in the num_qty
 * many sales weeks prior to and including the_order_date.
 * For each week, the sales are searched for in a +-3 day
 * interval around the predicted sales date. 
 * The last returned quantity is the most recent one.
 *
 * The reason for the subquery is that even zero values for shop dates
 * where nothing was bought are returned.  
*/
/*
drop function if exists last_order_quantities|
create function last_order_quantities (the_uf_id int, the_order_date date, the_product_id int)
returns char(100)
reads sql data
begin
  declare done int default 0;
  declare qty float(10,1) default 0.0;
  declare res char(100) default '';
  declare cur cursor for
    select sum(the_quantity)
      from 
      ( select distinct date_for_order as po_date
        from aixada_product_orderable_for_date
        where date_for_order between date_add(the_order_date, interval -3 month) and the_order_date
      ) as po_table
      left join 
      ( select date_for_order as oi_date, 
               quantity as the_quantity
	 from aixada_order_item
	where date_for_order between date_add(the_order_date, interval -3 month) and the_order_date
	  and uf_id = the_uf_id
      	  and product_id = the_product_id
      ) as oi_table 
      on po_date = oi_date
      group by po_date
      order by po_date desc;

  declare continue handler for not found set done = 1;

  open cur;
  
  read_loop: loop
    fetch cur into qty;
    if done then leave read_loop; end if;
    set res = concat(ifnull(qty, '0'), ',', res);
  end loop;
  
  close cur;
  return trim(trailing ',' from res);
end|
*/

/**
 * A query that returns all products eligible for order
 */
/*
drop procedure if exists products_for_order_by_provider|
create procedure products_for_order_by_provider (IN the_provider_id int, IN the_uf_id int, IN the_order_date date)
begin
  select distinct
      p.id,
      last_order_quantities(the_uf_id, the_order_date, p.id) as last_orders,
      p.name, 
      p.description,
      category_id,
      unit_price * (1 + iva_percent*0.01) as unit_price,
      m.unit,
      rev_tax_percent,
      if (p.orderable_type_id = 4, 'true', 'false') as preorder, 
      stock_actual,
      pv.name as provider_name
  from 
      aixada_product p
      left join aixada_product_orderable_for_date o
      on p.id = o.product_id
      left join aixada_rev_tax_type t
      on p.rev_tax_type_id = t.id
      left join aixada_unit_measure m
      on p.unit_measure_order_id = m.id
      left join aixada_provider pv
      on p.provider_id = pv.id
  where 
      provider_id = the_provider_id and
      p.active = 1 and
      pv.active = 1
      and (
        p.orderable_type_id = 2 and
        o.date_for_order = the_order_date
	or p.orderable_type_id >= 3
      )
  order by p.name;
end|
*/

/**
 * A query that returns all products eligible for order of a certain category
 */
/*
drop procedure if exists products_for_order_by_category|
create procedure products_for_order_by_category (IN the_category_id int, IN the_uf_id int, IN the_order_date date)
begin
  select distinct
      p.id,
      last_order_quantities(the_uf_id, the_order_date, p.id) as last_orders,
      p.name, 
      p.description,
      category_id,
      unit_price * (1 + iva_percent*0.01) as unit_price,
      m.unit,
      rev_tax_percent,
      if (p.orderable_type_id = 4, 'true', 'false') as preorder, 
      stock_actual,
      pv.name as provider_name
  from 
      aixada_product p
      left join aixada_product_orderable_for_date o
      on p.id = o.product_id
      left join aixada_rev_tax_type t
      on p.rev_tax_type_id = t.id
      left join aixada_unit_measure m
      on p.unit_measure_order_id = m.id
      left join aixada_provider pv
      on p.provider_id = pv.id
  where 
      p.active = 1 and 
      pv.active = 1 and
      category_id = the_category_id 
      and (
        p.orderable_type_id = 2 and
        o.date_for_order = the_order_date
	or p.orderable_type_id >= 3
      )
  order by p.name;
end|
*/

/**
 * A query that returns all products eligible for sale
 */
/*
drop procedure if exists products_for_order_like|
create procedure products_for_order_like (in the_like varchar(255), in the_uf_id int, in the_order_date date)
begin
  select distinct 
      p.id,
      p.name, 
      p.description,
      category_id,
      unit_price * (1 + iva_percent*0.01) as unit_price,
      m.unit,
      rev_tax_percent,
      if (p.orderable_type_id = 4, 'true', 'false') as preorder, 
      stock_actual,
      pv.name as provider_name
  from 
      aixada_product p
      left join aixada_provider pv
      on p.provider_id = pv.id
      left join aixada_product_orderable_for_date o
      on p.id = o.product_id
      left join aixada_rev_tax_type t
      on p.rev_tax_type_id = t.id
      left join aixada_unit_measure m
      on p.unit_measure_order_id = m.id
  where 
      p.active = 1  and
      pv.active = 1
      and (
        p.orderable_type_id = 2 and
        o.date_for_order = the_order_date
	or p.orderable_type_id >= 3
      )
      and p.name LIKE ConCAT('%', the_like, '%')
  order by p.name;
end|*/


/**
 * A query that returns all products eligible for ordering at a given date
 */
drop procedure if exists products_for_order_by_date|
create procedure products_for_order_by_date (in order_date date, in the_uf_id int)
begin
  select
      p.id,
      p.name,
      p.description,
      p.category_id, 
      pv.name as provider_name,
      p.unit_price * (1 + iva.percent*0.01) as unit_price, 
      m.unit,
      rev_tax_percent,
      if (p.orderable_type_id = 4 and i.date_for_order = '1234-01-23', 'true', 'false') as preorder, 
      i.quantity
  from 
      aixada_order_item i
      left join aixada_product p
      on i.product_id = p.id
      left join aixada_provider pv
      on p.provider_id = pv.id
      left join aixada_rev_tax_type t
      on p.rev_tax_type_id = t.id
      left join aixada_unit_measure m
      on p.unit_measure_order_id = m.id
      left join aixada_iva_type iva
      on p.iva_percent_id = iva.id
  where
      i.date_for_order in (order_date, '1234-01-23')
  and i.uf_id = the_uf_id
  and orderable_type_id > 1
  order by pv.id, p.id; 
end|

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



delimiter ;
