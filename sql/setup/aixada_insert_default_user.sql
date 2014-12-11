
	insert into
		aixada_uf (id, name, active, created)
	values
		(1, "Admin",1, sysdate());


	insert into 
		aixada_member (id, name, uf_id, address, city) 
	values (
     	1, 
     	'admin', 
     	1,
     	'', '');
		

	-- insert admin user manuall --
	insert into 
		aixada_user (id, login, password, uf_id, member_id, language, created_on,
				email) 
	values (1, 
      'admin', 
      'axgSb.Vaqch9E', 
      1,
      1,
      'es', 
      sysdate(),
      '');
      
     /*on duplicate key update login='admin', password='axgSb.Vaqch9E', language='en', created_on=sysdate();*/

     
     -- create roles --
  	insert into 
  		aixada_user_role (user_id, role) 
  	values 
    (1, 'Hacker Commission');
    
    
    -- create accounts --
    insert into aixada_account (account_id, quantity, payment_method_id, currency_id, description, operator_id, ts, balance)
	values
		(-3, 0, 11, 1, 'cashbox setup', 1, now(), 0),
		(-2, 0, 11, 1, 'consum setup', 1, now(), 0),
		(-1, 0, 11, 1, 'maintenance setup', 1, now(), 0),
	   	(1001,0, 11, 1, 'admin account setup', 1, now(), 0 );
    
    	
    
    
    