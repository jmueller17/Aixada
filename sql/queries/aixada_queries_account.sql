delimiter |


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
			account_id, max(ts) as MaxDate 
		from 
			aixada_account 
		group by 
			account_id) r, aixada_account a, aixada_uf uf
  where 
	a.account_id = r.account_id 
	and a.ts = r.MaxDate
	and a.balance < 0
    and uf.active = 1
    and uf.id = a.account_id -1000
  order by
	a.balance;
end|


/**
 * retrieves account info 
 */
drop procedure if exists account_extract|
create procedure account_extract(in the_account_id int, in tmp_start_date date, in num_rows int)
begin
  declare start_date datetime default if(tmp_start_date=0, date(sysdate()), date_add(tmp_start_date, interval 1 day)); 
        /* This is to also catch movements on the same day as the start date */
		/*  if tmp_start_date = 0 then set start_date = date_add(sysdate(), interval -3 month); end if; */
  set @q = concat("select
    a.id,
    a.ts, 
    a.quantity,
    a.description as description,
    a.account_id as account,
    p.description as method,
    c.name as currency,
    ifnull(mem.name, 'default') as operator,
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
 where 
   a.account_id = ", the_account_id, " and
   a.ts <= '", start_date, "'
 order by ts desc limit ", num_rows);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|



drop procedure if exists latest_movements|
create procedure latest_movements()
begin
  declare tomorrow datetime default date_add(sysdate(), interval 1 day);

  select
    a.id,
/*
    case a.account_id 
      when -3 then 'Caixa' when -2 then 'Consum' when -1 then 'Manteniment' else a.account_id
    end as account_id,
*/  a.account_id,
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
 order by a.ts desc limit 10;
end|


/**
 * make a deposit into aixada_account for a given uf. 
 */
drop procedure if exists deposit_for_uf|
create procedure deposit_for_uf(in the_account_id int, in qty decimal(10,2), in the_description varchar(255), in op int)
begin
  declare current_balance decimal(10,2);

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
  
        
  insert into 
  	aixada_account (account_id, quantity, description, operator_id, balance) 
  values 
  	(the_account_id, qty, the_description, op, current_balance + qty);

  /** Account 3 is Caixa. So we update Caixa whenever we haven't inserted directly into Caixa. */
  if the_account_id != -3 then  
    select 
    	balance
    into 
    	current_balance
    from 
    	aixada_account
    where 
    	account_id = -3
    order by ts desc
    limit 1;

  /* ufs make a positive deposit, movements to Consum(-2) make a negative deposit to caixa */
    insert into aixada_account (
      account_id, quantity, description, operator_id, balance
    ) values (
      -3, if(the_account_id > 0, qty, -qty), the_description, op, 
          current_balance + if(the_account_id > 0, qty, -qty)
    );

  end if;

end|





delimiter ;

   