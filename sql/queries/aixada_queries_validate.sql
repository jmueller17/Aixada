delimiter |


/**
 * returns a list of all active ufs and the number of their non-validated
 * shoppping carts.
 */
drop procedure if exists get_uf_listing_cart_count|
create procedure get_uf_listing_cart_count()
begin
	
	select
		uf.id as uf_id,
		uf.name as uf_name,
		count(if(c.ts_validated=0,1,NULL)) as non_validated_carts,
		count(if(c.ts_validated>0,1,NULL)) as validated_carts
	from 
		aixada_uf uf
	left join 
		aixada_cart c on c.uf_id = uf.id
	where
		uf.active = 1
	group by
		uf.id
	order by
		uf.id;
	
end|



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
 * and registers the money in the corresponding accounts.  
 */
drop procedure if exists validate_shop_cart|
create procedure validate_shop_cart(in the_cart_id int, in the_op_id int, in the_desc_pay varchar(50), in use_transaction boolean)
begin
  declare current_balance decimal(10,2) default 0.0;
  declare total_price decimal(10,2) default 0.0;
  declare the_account_id int;
  
  if (use_transaction is true) then
    start transaction;
  end if;
  
  set the_account_id = (
	  select
	  	(uf_id + 1000)
	  from 
	  	aixada_cart
	  where
	  	id = the_cart_id
	  limit 1); 
  	
  -- do the actual validation: set timestamp! --	
  update 
  	aixada_cart
  set 
  	ts_validated = sysdate(),
  	ts_last_saved = now(),
    operator_id = the_op_id
   where 
   	id = the_cart_id
    and ts_validated = 0;

 
  update 
  	aixada_product p,
  	aixada_shop_item si
  set 
  	p.stock_actual = p.stock_actual - si.quantity,
	p.delta_stock  = p.delta_stock  - si.quantity
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
  	aixada_account (account_id, quantity, payment_method_id, description, operator_id, balance) 
  select 
   the_account_id,
    - total_price,
    6,
    concat(the_desc_pay, the_cart_id),
    the_op_id,
    current_balance - total_price;
  if (use_transaction is true) then
   commit;
  end if;

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
