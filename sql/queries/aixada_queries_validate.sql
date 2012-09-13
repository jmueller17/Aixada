delimiter |


/**
 *  returns all ufs with unvalidated carts for given date
 */
drop procedure if exists get_ufs_for_validation|
create procedure get_ufs_for_validation(in tmp_shop_date date)
begin
  
	declare the_shop_date date default tmp_shop_date;
	
  	if (the_shop_date=0) then 
    	select 
    		date_for_shop
    	into 
    		the_shop_date
    	from 
    		aixada_cart
    	where 
    		date_for_shop >= date(sysdate()) 
    	limit 1;
  	end if;
  
  	
	select distinct 
  		u.id, u.name
  	from 
  		aixada_cart c
  	left join 
  		aixada_uf u on u.id = c.uf_id
  	where 
  		c.date_for_shop = the_shop_date
  		and c.ts_validated = 0
  	order by 
  		u.id;
end|


/**
 * validates a shopping cart by setting the ts_validated to the current datetime 
 */
drop procedure if exists validate_shop_cart|
create procedure validate_shop_cart(in the_cart_id int, in the_op_id int)
begin
  declare current_balance decimal(10,2) default 0.0;
  declare total_price decimal(10,2) default 0.0;
  declare the_account_id int;
  
  set the_account_id = (
	  select
	  	(uf_id + 1000)
	  from 
	  	aixada_cart
	  where
	  	id = the_cart_id
	  limit 1); 
  	
	
  update 
  	aixada_cart
  set 
  	ts_validated = sysdate(),
    operator_id = the_op_id
   where 
   	id = the_cart_id
    and ts_validated = 0;

 
  update 
  	aixada_product p,
  	aixada_shop_item si
  set 
  	p.stock_actual = p.stock_actual - si.quantity
  where
  	si.cart_id = the_cart_id
  	and si.product_id = p.id;
  	

  select 
  	balance 
  into 
  	current_balance
  from 
  	aixada_account
  where 
  	account_id = the_account_id
  order by ts desc
  limit 1; 

  select 
  	get_purchase_total(the_cart_id)
  into 
  	total_price;


  /** new entry into account **/
  insert into 
  	aixada_account (account_id, quantity, description, operator_id, balance) 
  select 
   the_account_id,
    - total_price,
    concat('validate_cart_', the_cart_id),
    the_op_id,
    current_balance - total_price;

end|




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


drop procedure if exists get_active_ufs|
create procedure get_active_ufs()
begin
  select u.id, u.name
  from aixada_uf u
  where active = 1
  order by u.id;
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
