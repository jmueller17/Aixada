 



 -- create accounts --
insert into aixada_account (account_id, quantity, payment_method_id, currency_id, description, operator_id, ts, balance)
	values
		(-3, 0, 11, 1, 'cashbox setup', 1, now(), 0),
		(-2, 0, 11, 1, 'consum setup', 1, now(), 0),
		(-1, 0, 11, 1, 'maintenance setup', 1, now(), 0),
	   	(1001,0, 11, 1, 'admin account setup', 1, now(), 0 );


insert into aixada_payment_method (id, description, details)
       values 
       	(1, 'cash', 'cash payment'), 
       	(5,'stock', 'register gain or loss of stock'),
		(6,'validation', 'register validation of cart'),
		(7,'deposit','register the inpayment of cash'),
		(8,'bill','register withdrawal for bill payment to provider'),
		(9,'correction','by-hand correction of account balance'),
		(10,'withdrawal','default cash withdrawal'),
		(11,'setup','account setup');
       