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
