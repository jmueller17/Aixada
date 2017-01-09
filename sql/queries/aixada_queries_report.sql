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
