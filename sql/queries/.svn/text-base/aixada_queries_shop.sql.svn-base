delimiter |

drop procedure if exists providers_with_active_products_for_shop|
create procedure providers_with_active_products_for_shop (in the_date date) 
begin
  select distinct
    pv.id, 
    pv.name 
  from 
    aixada_provider pv 
  left join aixada_product pr   
    on pr.provider_id = pv.id 
  left join aixada_product_orderable_for_date o
    on pr.id = o.product_id
  where 
    pr.active = 1 and
    pv.active = 1
  order by pv.name;
end|


/**
 * A query that returns all product categories
 */
drop procedure if exists product_categories_for_shop|
create procedure product_categories_for_shop (in the_date date) 
begin
  select distinct
    pc.id,
    pc.description
  from 
    aixada_product pr
  left join aixada_product_category pc
    on pr.category_id = pc.id
  left join aixada_product_orderable_for_date o
    on pr.id = o.product_id
  left join aixada_provider pv
    on pr.provider_id = pv.id
  where 
    pr.active = 1 and
    pv.active = 1;
end|

/**
 * A query that returns all products eligible for shop
 */
drop procedure if exists products_for_shop_by_provider|
create procedure products_for_shop_by_provider (IN the_provider_id int, IN the_uf_id int, in the_date date)
begin
  declare the_shop_date datetime default the_date;
  if (the_shop_date=0) then set the_shop_date = sysdate(); end if;
  select distinct
      p.id,
      last_order_quantities(the_uf_id, the_shop_date, p.id) as last_orders,
      p.name, 
      p.description,
      pv.name as provider_name,
      category_id,
      unit_price * (1 + iva_percent/100) as unit_price,
      u.unit,
      rev_tax_percent,
      stock_actual
  from 
      aixada_product p
   left join aixada_rev_tax_type t
      on p.rev_tax_type_id = t.id
   left join aixada_product_orderable_for_date o
      on p.id = o.product_id
   left join aixada_unit_measure u
      on p.unit_measure_shop_id = u.id
   left join aixada_provider pv
      on p.provider_id = pv.id
  where 
      p.active = 1 and 
      pv.active = 1 and
      provider_id = the_provider_id
  order by pv.id, p.name;
end|

/**
 * A query that returns all products eligible for shop of a certain category
 */
drop procedure if exists products_for_shop_by_category|
create procedure products_for_shop_by_category (IN the_category_id int, IN the_uf_id int, IN the_date date)
begin
  declare the_shop_date datetime default the_date;
  if (the_shop_date=0) then set the_shop_date = sysdate(); end if;
  select distinct
      p.id,
      last_order_quantities(the_uf_id, the_shop_date, p.id) as last_orders,
      p.name, 
      p.description,
      pv.name as provider_name, 
      category_id,
      unit_price * (1 + iva_percent/100) as unit_price,
      u.unit,
      rev_tax_percent,
      stock_actual
  from 
      aixada_product p
   left join aixada_provider pv
      on p.provider_id = pv.id
   left join aixada_rev_tax_type t
        on p.rev_tax_type_id = t.id
   left join aixada_product_orderable_for_date o
      on p.id = o.product_id
   left join aixada_unit_measure u
      on p.unit_measure_shop_id = u.id
  where 
      p.active = 1 and 
      pv.active = 1 and
      category_id = the_category_id
  order by pv.id, p.name;
end|

/**
 * A query that returns all products eligible for shop
 * the variable uf_id is not used, unlike in the two preceding queries
 */
drop procedure if exists products_for_shop_like|
create procedure products_for_shop_like (in the_like varchar(255), IN the_uf_id int, in the_date date)
begin
  select distinct
      p.id,
      p.name, 
      p.description,
      pv.id as provider_id,
      category_id,
      unit_price * (1 + iva_percent/100) as unit_price,
      u.unit,
      rev_tax_percent,
      stock_actual,
      pv.name as provider_name
  from 
      aixada_product p
      left join aixada_provider pv
      on p.provider_id = pv.id
      left join aixada_rev_tax_type t
      on p.rev_tax_type_id = t.id
      left join aixada_unit_measure u
      on p.unit_measure_shop_id = u.id
      left join aixada_product_orderable_for_date o
      on p.id = o.product_id
  where 
      p.active = 1 and
      pv.active = 1
    and
      p.name LIKE ConCAT('%', the_like, '%')
  order by pv.id, p.name;   
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

/**
 * A query that returns all ordered products eligible for shopping
 */
drop procedure if exists products_for_shopping| 
create procedure products_for_shopping(IN the_date date, in the_uf int)
begin
  select
      p.id,
      last_order_quantities(i.uf_id, i.date_for_shop, p.id) as last_orders,
      p.name,
      p.description,
      p.provider_id,  
      pv.name as provider_name,
      p.category_id, 
      p.unit_price * (1 + p.iva_percent/100) as unit_price, 
      u.unit,
      rev_tax_percent,
      i.quantity as quantity
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
      i.date_for_shop = the_date
  and i.ts_validated = 0
  and i.uf_id = the_uf
  order by p.provider_id, p.name;
end|

/*
 * A query that calculates the total of all sales items for a given uf and date
 */ 
drop function if exists total_price_of_shop_items|
create function total_price_of_shop_items(the_date date, the_uf_id int)
returns float(10,2)
reads sql data
begin
  declare total_price decimal(10,2);
  select 
    sum(i.quantity * p.unit_price * (1 + p.iva_percent/100) * (1 + t.rev_tax_percent/100)) 
    into total_price
    from aixada_shop_item i
      left join aixada_product p
        on i.product_id = p.id
      left join aixada_rev_tax_type t
        on t.id = p.rev_tax_type_id
    where i.date_for_shop = the_date
      and uf_id = the_uf_id
      and i.ts_validated = 0;
  return total_price;
end|

delimiter ;
