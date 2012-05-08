delimiter |

drop procedure if exists list_all_providers_short|
create procedure list_all_providers_short()
begin
  select distinct 
     pv.id, 
     pv.name
  from aixada_provider pv
  left join aixada_product p 
  on pv.id = p.provider_id 
  where p.active = 1 
    and pv.active = 1
    and p.orderable_type_id in (2,3)
  order by pv.name;
end|

drop procedure if exists list_all_ordered_providers_short|
create procedure list_all_ordered_providers_short(in the_date date)
begin
  select distinct pv.id, pv.name 
  from aixada_provider pv 
  left join aixada_product p 
  on pv.id = p.provider_id 
  left join aixada_order_item i
  on p.id = i.product_id
  where p.active = 1 
    and pv.active = 1
    and i.date_for_order = the_date
  order by pv.name;
end|

drop procedure if exists get_activated_products|
create procedure get_activated_products(in the_provider_id int, in the_date date)
begin
  select distinct
        p.id, 
        p.name,
        p.description
  from aixada_product p 
  left join aixada_product_orderable_for_date od on p.id=od.product_id 
  where p.provider_id = the_provider_id and 
        p.orderable_type_id = 2 and
        p.active = true and 
	od.date_for_order=the_date
  order by p.name, p.id;
end|

drop procedure if exists get_deactivated_products|
create procedure get_deactivated_products(in the_provider_id int, in the_date date)
begin
  select distinct
        p.id, 
        p.name,
        p.description 
  from aixada_product p
  where p.provider_id = the_provider_id and 
        p.orderable_type_id = 2 and
        p.active = true 
        and not exists
	  (select od.product_id from aixada_product_orderable_for_date od
	   where od.product_id = p.id and 
	         od.date_for_order = the_date)
  order by p.name, p.id;
end|

drop procedure if exists get_arrived_products|
create procedure get_arrived_products(in the_provider_id int, in the_date date)
begin
  select distinct
        i.product_id, 
        p.name,
        p.description
  from aixada_shop_item i
  left join aixada_product p 
     on p.id = i.product_id 
  where i.date_for_shop = the_date
    and p.provider_id = the_provider_id and 
        p.active = true 
  order by p.name;
end|

drop procedure if exists get_not_arrived_products|
create procedure get_not_arrived_products(in the_provider_id int, in the_date date)
begin
  select distinct
        i.product_id, 
        p.name,
        p.description
  from aixada_order_item i
  left join aixada_product p 
     on p.id = i.product_id 
  where i.date_for_order = the_date
    and p.provider_id = the_provider_id 
    and p.active = true 
    and not exists (
       select si.product_id
       from aixada_shop_item si
       where si.date_for_shop = the_date
         and si.product_id = i.product_id
    )
  order by p.name;
end|


drop procedure if exists add_stock|
create procedure add_stock(in the_product_id int, in delta_amount decimal(10,4), in the_operator_id int, in the_description varchar(255))
begin
   start transaction;
   update aixada_product
   set stock_actual = stock_actual + delta_amount,
       delta_stock  = delta_stock + delta_amount /* delta_stock = stock_actual - stock_min */
   where id = the_product_id;

   insert into aixada_stock_movement (
     product_id, operator_id, amount_difference, description, resulting_amount
   ) select
     the_product_id,
     the_operator_id,
     delta_amount,
     the_description,
     p.stock_actual
     from aixada_product p
     where p.id = the_product_id;
   commit;
end|

drop procedure if exists stock_movements|
create procedure stock_movements(in product_id int, in tmp_start_date date, in num_rows int)
begin
  declare start_date date default tmp_start_date;
  if start_date = 0 then set start_date = date_add(sysdate(), interval -3 month); end if;

  set @q = concat("select
    id, 
    operator_id,
    amount_difference, 
    description,
    resulting_amount,
    ts
    from aixada_stock_movement
    where ts >= '", start_date, "'
    order by ts limit ", num_rows);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

delimiter ;