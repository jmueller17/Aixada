delimiter |




drop  procedure if exists account_exists|
create procedure account_exists(in the_account_id int)
begin
  select
    count(*)
  from 
    aixada_account
  where
    account_id = the_account_id;
end|


/**
 * Generic procedure to handle either deposit or withdrawal for the given account. 
 */
drop procedure if exists move_money|
create procedure move_money(in the_quantity decimal(10,2), 
                            in the_account_id int, 
                            in type_id int, 
                            in the_operator_id int, 
                            in the_description varchar(255),
                            in the_currency_id int)
begin

    declare current_balance decimal(10,2);
    declare new_balance decimal(10,2);
  
  -- get the current balance -- 
    select 
      balance 
    into 
      current_balance
    from 
      aixada_account
    where 
      account_id = the_account_id
    order by ts desc, id desc
    limit 1; 
  
    set new_balance = current_balance + the_quantity; 
    
    insert into 
      aixada_account (account_id, quantity, payment_method_id, description, operator_id, balance, currency_id) 
    values 
      ( the_account_id, 
        the_quantity, 
        type_id, 
        the_description, 
        the_operator_id, 
        new_balance, 
        the_currency_id);

end|



/**
 * procedure allows to manually correct / reset account balance. Mainly 
 * should be used for the global accounts -3, -2, -1 not user accounts.
 * in any case: it always adds the correct as a new line to the account in order
 * to keep all changes traceable. 
 */
drop procedure if exists correct_account_balance|
create procedure correct_account_balance(in the_account_id int, in the_balance decimal(10,2), in the_operator_id int, in the_description varchar(255))
begin
	
	declare current_balance decimal(10,2);
	declare quantity decimal(10,2);
	
	-- get the current balance -- 
  	select 
  		balance 
  	into 
  		current_balance
  	from 
  		aixada_account
  	where 
  		account_id = the_account_id
  	order by ts desc, id desc
  	limit 1; 
	
  	set quantity = -(current_balance - the_balance); 
  	
	insert into 
  		aixada_account (account_id, quantity, payment_method_id, description, operator_id, balance) 
  	values 
  		(the_account_id, 
  	 	 quantity, 
  	 	9, 
  	 	the_description, 
  	 	the_operator_id, 
  	 	the_balance);
	
end|

/**
 * returns the current balance of a given account
 */
drop procedure if exists get_account_balance|
create procedure get_account_balance(in the_account_id int)
begin

	select
		*
	from
		aixada_account
	where
		account_id = the_account_id 
	order by
		ts desc, id desc
	limit 1;
end|

/**
 * returns the current balance of Caixa (-3), Consum (-2), Mantenimient (-1)
 */
drop procedure if exists global_accounts_balance|
create procedure global_accounts_balance()
begin

	(select
		*
	from
		aixada_account
	where
		account_id = -2 
	order by
		ts desc, id desc
	limit 1)
	union all
	(select
		*
	from
		aixada_account
	where
		account_id = -2 
	order by
		ts desc, id desc
	limit 1)
	union all
	(select
		*
	from
		aixada_account
	where
		account_id = -3 
	order by
		ts desc, id desc
	limit 1);
end|


/**
 * retrieves all ufs with negative balance
 */
drop procedure if exists negative_accounts|
create procedure negative_accounts()
begin
  select 
	uf.id as uf, 
	uf.name, 
	a.balance, 
	a.ts as last_update 
  from (select 
			account_id, max(id) as MaxId 
		from 
			aixada_account 
		group by 
			account_id) r, aixada_account a, aixada_uf uf
  where 
	a.account_id = r.account_id 
	and a.id = r.MaxId
	and a.balance < 0
    and uf.active = 1
    and uf.id = a.account_id -1000
  order by
	a.balance;
end|


/**
 * retrieves account movements for a given date range
 */
drop procedure if exists get_extract_in_range|
create procedure get_extract_in_range(in the_account_id int, in from_date date, in to_date date)
begin
	select
    	a.id,
	    a.ts, 
	    a.quantity,
	    a.description as description,
	    a.account_id as account,
	    p.description as method,
	    c.name as currency,
	    ifnull(mem.name, 'default') as operator,
	    a.balance
 	from 
 		aixada_account a,
 		aixada_payment_method p,
 		aixada_user u,
 		aixada_member mem,
 		aixada_currency c
 	where 
 		a.account_id = the_account_id
 		and a.ts >= from_date 
 		and a.ts <= to_date 
 		and a.currency_id = c.id
 		and a.payment_method_id = p.id
 		and a.operator_id = u.id
 		and u.member_id = mem.id
 	order by 
 		a.ts desc, id desc; 
 
end|


/**
 * retrieves latest account movements 
 * could and should be integrated into get_extract_in_range()
 */
drop procedure if exists latest_movements|
create procedure latest_movements()
begin
  declare tomorrow datetime default date_add(sysdate(), interval 1 day);

  select
    a.id,
  	a.account_id,
    time(a.ts) as time, 
    a.quantity,
    p.description as method,
    c.name as currency,
    concat(uf.id, ' ' , uf.name) as uf_id,
    balance
 from aixada_account a
 left join aixada_currency c
   on a.currency_id = c.id
 left join aixada_payment_method p
   on a.payment_method_id = p.id
 left join aixada_user u
   on a.operator_id = u.id
 left join aixada_member mem
   on u.member_id = mem.id
 left join aixada_uf uf
   on a.account_id - 1000 = uf.id
 where a.account_id > 0
   and a.ts < tomorrow
 order by a.ts desc, id desc
 limit 10;
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


delimiter ;
