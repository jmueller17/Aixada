delimiter |

drop procedure if exists initialize_caixa|
create procedure initialize_caixa()
begin
  declare ct int;

  select count(*) 
  into ct
  from aixada_account 
  where account_id = -3 
    and date(ts) = date(sysdate());

  if ct=0 then 
    select id
    into ct
    from aixada_account
    where account_id = -3 
    order by ts desc limit 1;

    update aixada_account 
    set ts = sysdate()
    where id=ct;
  end if;
end|

drop procedure if exists get_ufs_for_validation|
create procedure get_ufs_for_validation(in tmp_shop_date date)
begin
  declare the_shop_date date default tmp_shop_date;
  if (the_shop_date=0) then 
    select date_for_order 
    into the_shop_date
    from aixada_product_orderable_for_date 
    where date_for_order > date(sysdate()) limit 1;
  end if;
  select distinct u.id, u.name
  from aixada_shop_item i
  left join aixada_uf u
  on u.id = i.uf_id
  where i.date_for_shop = the_shop_date
  and i.ts_validated = 0
  order by u.id;
end|

drop procedure if exists get_active_ufs|
create procedure get_active_ufs()
begin
  select u.id, u.name
  from aixada_uf u
  where active = 1
  order by u.id;
end|

drop procedure if exists products_for_validating| 
create procedure products_for_validating(IN the_uf_id int, in the_date date)
begin
  select
      p.id,
      last_order_quantities(i.uf_id, i.date_for_shop, p.id) as last_orders,
      p.name,
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
      i.uf_id = the_uf_id 
      and i.date_for_shop = the_date
      and i.ts_validated = 0
  order by pv.name, p.name;
end|

/**
 * A query that returns all products eligible for shopping while validating
 */
drop procedure if exists products_for_validate_like|
create procedure products_for_validate_like (IN the_like varchar(255))
begin
  select 
      p.id,
      p.name,
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
      left join aixada_provider pv
      on p.provider_id = pv.id
      left join aixada_product_orderable_for_date o
      on p.id = o.product_id
      left join aixada_unit_measure u
      on p.unit_measure_shop_id = u.id
  where 
      p.active = 1 and
      pv.active = 1 and
      p.name LIKE ConCAT('%', the_like, '%')
  order by pv.id, p.id;
end|

drop procedure if exists validate_shop_items|
create procedure validate_shop_items(in the_date date, in the_uf_id int, in the_op_id int)
begin
  update aixada_shop_item
     set ts_validated = sysdate(),
         operator_id = the_op_id
   where uf_id = the_uf_id
     and date_for_shop = the_date
     and ts_validated = 0;
end|


drop procedure if exists deduct_stock_and_pay|
create procedure deduct_stock_and_pay(IN the_date date, in the_uf_id int, in the_operator_id int)
begin
  declare current_balance decimal(10,2) default 0.0;
  declare total_price decimal(10,2) default 0.0;
  declare the_account_id int;

  /* process the stock */
  update aixada_shop_item i
    left join aixada_product p
    on i.product_id = p.id
    set p.stock_actual = p.stock_actual - i.quantity
    where date_for_shop = the_date
      and uf_id = the_uf_id
      and ts_validated = 0;

  /* The UF pays */
  select balance 
  into current_balance
  from aixada_account_balance 
  where account_id = the_uf_id + 1000;

  select total_price_of_shop_items(the_date, the_uf_id)
  into total_price;

/* OJO: the_date should really be an exact timestamp, to distinguish different sales */

  insert into aixada_account (
    account_id, quantity, description, operator_id, balance
  ) select 
    the_uf_id + 1000,
    - total_price,
    concat('venda uf ', the_uf_id),
    the_operator_id,
    current_balance - total_price;

  update aixada_account_balance
  set balance = current_balance - total_price
  where account_id = the_uf_id + 1000;

end|

drop procedure if exists income_spending_balance|
create procedure income_spending_balance(in tmp_date date)
begin
   declare today date default case tmp_date when 0 then date(sysdate()) else date(tmp_date) end;
   select 
     sum( 
       case when quantity>0 then quantity else 0 end
     ) as income,
     sum(
       case when quantity<0 then quantity else 0 end
     ) as spending,
     sum(quantity) as balance
   from aixada_account a
   use index (ts)
   where a.ts between today and date_add(today, interval 1 day) and
         a.account_id = -3;
end|


drop procedure if exists products_below_min_stock|
create procedure products_below_min_stock()
begin
  select
        p.id,
        p.name as stock_item,
        pv.name as stock_provider,
        p.stock_actual,
        p.stock_min
  from aixada_product p use index(delta_stock), aixada_provider pv
  where 
 	p.provider_id = pv.id 	
  	and p.active = 1
    and p.orderable_type_id = 1
    and pv.active = 1
    and p.stock_actual < p.stock_min
  order by pv.name;
end|


drop function if exists balance_of_account|
create function balance_of_account (the_account_id int)
returns decimal(10,2)
reads sql data
begin
  declare b decimal(10,2);

  select balance 
  into b
  from aixada_account 
  use index (ts) 
  where account_id = the_account_id
  order by ts desc limit 1;

  return b;
end|

drop procedure if exists dates_with_unvalidated_shop_carts|
create procedure dates_with_unvalidated_shop_carts ()
begin
  select distinct date_for_shop as date_for_validation
  from aixada_shop_item 
  where ts_validated = 0
  order by date_for_shop desc;
end|


drop procedure if exists validated_shop_carts|
create procedure validated_shop_carts(in the_day date)
begin
  select distinct uf_id, ts_validated
  from aixada_shop_item
  where ts_validated between the_day and date_add(the_day, interval 1 day)
  order by uf_id;
end|


/*drop procedure if exists undo_validate|
create procedure undo_validate(in the_uf_id int, in the_ts datetime, in the_operator int)
begin
  declare the_shop_date date, 
    uf_balance decimal(10,2), 
    caixa_balance decimal(10,2), 
    shop_amount decimal(10,2);

  start transaction;

  select date_for_shop 
  into the_shop_date
  from aixada_shop_item where uf_id = the_uf_id and ts_validated = the_ts
  limit 1;

  start transaction;
  update aixada_shop_item 
  set ts_validated = 0
  where uf_id = the_uf_id
    and date_for_shop = the_shop_date;

  select balance 
  into uf_balance
  from aixada_account_balance
  where account_id = 1000 + the_uf_id;

  select balance 
  into caixa_balance
  from aixada_account_balance
  where account_id = -3;


  select total_
  into caixa_balance
  from aixada_account_balance
  where account_id = -3;

  insert into aixada_account (
    account_id, quantity, description, operator_id, balance    
  ) values (
    1000 + the_uf_id, 
  )

 commit;

  select date_for_shop 
  into the_shop_date
  from aixada_shop_item where uf_id = the_uf_id and ts_validated = the_ts
  limit 1;

  update aixada_shop_item 
  set ts_validated = 0
  where uf_id = the_uf_id
    and date_for_shop = the_shop_date;

  select balance 
  into uf_balance
  from aixada_account_balance
  where account_id = 1000 + the_uf_id;

  select balance 
  into caixa_balance
  from aixada_account_balance
  where account_id = -3;


  select total_
  into caixa_balance
  from aixada_account_balance
  where account_id = -3;

  insert into aixada_account (
    account_id, quantity, description, operator_id, balance    
  ) values (
    1000 + the_uf_id, 
  )

 commit;
end|
*/


delimiter ;
