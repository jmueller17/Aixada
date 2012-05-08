set foreign_key_checks = 0;

/* 
   Account numbers:
   -1          Manteniment
   -2	       Consum
   1..999      Uf cash sources (money that comes out of our pockets or goes in)
   1001..1999  regular UF accounts
   2001..2999  regular provider account
*/   



/*
When an UF orders something, nothing is written to the accounts.

When an UF buys things, the corresponding quantity is removed from its
account, and written to the 'Consum' account.
*/


/* move money from UF account to Consum account */
insert into aixada.aixada_account (
   account_id, quantity, description, operator_id, ts
 )
 select
   venuf+1000, -vensubtotal - venafegit, concat('venda uf ', venuf), venvendedor, vendata
 from aixada_old.Venda;

insert into aixada.aixada_account (
   account_id, quantity, description, operator_id, ts
 )
 select
   -2, vensubtotal + venafegit, concat('venda uf ', venuf), venvendedor, date_add(vendata, interval 1 second)
 from aixada_old.Venda;


/* Move money from UF cash source to UF account */
insert into aixada.aixada_account(
   account_id, quantity, description, operator_id, ts
 )
 select
   inuf, -inquantitat, concat('ingres uf ', inuf, ' ', innota), inmemid, date_add(indata, interval 30 second)
 from aixada_old.Ingres; 

insert into aixada.aixada_account(
   account_id, quantity, description, operator_id, ts
 )
 select
   inuf+1000, inquantitat, concat('ingres uf ', inuf, ' ', innota), inmemid, date_add(indata, interval 31 second)
 from aixada_old.Ingres; 


/* Move money from Consum to provider account */
insert into aixada.aixada_account (
   account_id, quantity, description, operator_id, ts
 )
 select
   -2, -sum(lcquantitat*lcpreuunitat), concat('Cda ', provnom), -1, date_add(lcdata, interval 60 second)
   from aixada_old.LiniaComanda 
   left join aixada_old.Producte on lcprod=prodid 
   left join aixada_old.Proveidor on prodprov=provid 
   where lcdata > 0 group by lcdata, provid order by lcdata;

insert into aixada.aixada_account (
   account_id, quantity, description, operator_id, ts
 )
 select
   provid+2000, sum(lcquantitat*lcpreuunitat), concat('Cda ', provnom), -1, date_add(lcdata, interval 61 second)
   from aixada_old.LiniaComanda 
   left join aixada_old.Producte on lcprod=prodid 
   left join aixada_old.Proveidor on prodprov=provid 
   where lcdata > 0 group by lcdata, provnom order by lcdata;


/* Update account balances */ 

set @account_balance=0;

drop procedure if exists do_update_account_movement;
create procedure do_update_account_movement (in account int)
    update aixada_account
    set 
      balance = (@account_balance:=@account_balance + quantity)
    where 
      account_id = account
    order by
      ts, id;

delimiter |
drop procedure if exists prepare_update_account_movement|
create procedure prepare_update_account_movement(account int)
begin
  set @account_balance=0;

  insert into aixada_account_balance (
    account_id, balance
  ) values (account, 0.0)
  on duplicate key update balance = 0.0;

  call do_update_account_movement(account);

  update aixada_account_balance
  set balance = @account_balance
  where account_id = account;
end| 

drop procedure if exists update_account_movements|
create procedure update_account_movements (in delta int)
begin
  declare account int;
  declare done int default 0;
  declare cur cursor for 
    select distinct account_id
    from aixada_account;

  declare continue handler for not found set done = 1;
  open cur;
  read_loop: loop
    fetch cur into account;
    if done then leave read_loop; end if;
    call prepare_update_account_movement(account+delta);
  end loop;
  close cur;
end|

delimiter ;

call update_account_movements(0);
call update_account_movements(1000);
call update_account_movements(2000);
call prepare_update_account_movement(-2);
insert into aixada_account (account_id , quantity , operator_id, balance ) values (-3, 0, 112,0);
call prepare_update_account_movement(-3);

/*
 *  Now fudge the results in order to agree with what's in 
 *  aixada_old.UnitatFamiliar.ufval
 */

insert into aixada_account (
   account_id, quantity, description, operator_id, balance
) select 
        bal.account_id, 
        old_uf.ufval - bal.balance, 
        'Correction after migration to version 2', 
        -1, 
        old_uf.ufval
  from aixada_old.UnitatFamiliar old_uf
  left join aixada_account_balance bal
  on bal.account_id = 1000+old_uf.ufid;

update aixada_account_balance b, aixada_old.UnitatFamiliar u
   set b.balance = u.ufval
 where b.account_id = u.ufid + 1000;

set foreign_key_checks = 1;
