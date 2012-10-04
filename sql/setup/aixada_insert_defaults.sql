

insert into aixada_product_category values 
        (1, 'SET_ME'),
		(1000, 'prdcat_vegies'),
		(2000, 'prdcat_fruit'),
		(3000, 'prdcat_mushrooms'),
		(4000, 'prdcat_dairy'), 			
		(5000, 'prdcat_meat'),	
		(6000, 'prdcat_bakery'),
		(7000, 'prdcat_cheese'),
		(8000, 'prdcat_sausages'),					
		(9000, 'prdcat_infant'),
		(10000, 'prdcat_cereals_pasta'),
		(11000, 'prdcat_canned'),
		(12000, 'prdcat_cleaning'),				
		(13000, 'prdcat_body'),
		(14000, 'prdcat_seasoning'),
		(15000, 'prdcat_sweets'),
		(16000, 'prdcat_drinks_alcohol'),
		(17000, 'prdcat_drinks_soft'),
		(18000, 'prdcat_drinks_hot'),
		(19000, 'prdcat_driedstuff'),
		(20000, 'prdcat_paper'),
		(21000, 'prdcat_health'),
		(22000, 'prdcat_misc');



insert into aixada_orderable_type values
       (1, 'stock'),
       (2, 'sometimes orderable'),
       (3, 'always orderable'),
       (4, 'cumulative order');
       
insert into aixada_rev_tax_type values
       (1, 'default revolutionary tax', 3.00),
       (2, 'basic necessities', 0.00),
       (3, 'luxury', 5.00);
       
insert into aixada_iva_type values
		(1, 0.00, 'no tax'),
		(2, 18.00, 'a category');

insert into aixada_unit_measure values
       (1, 'SET_ME'),
       (2, 'u'),
       (3, 'g'),
       (4, 'kg'),
       (5, '250g'),
       (6, '500g'),
       (7, '100g'),
       (8, '1,7kg'),
       (9, 'ml'),
       (10, 'l'),
       (11, '250ml'),
       (12, '500ml'),
       (13, '750ml'),
       (14, 'bunch'),
       (15, 'quarter'),
       (16, 'half');


insert into aixada_payment_method (id, description, details)
       values 
       	(1, 'cash', 'cash payment'), 
       	(2, 'envelope',''), 
       	(3, 'transfer',''),
       	(4, 'check',''),
       	(5,'stock', 'register gain or loss of stock'),
		(6,'validation', 'register validation of cart'),
		(7,'deposit','register the inpayment of cash'),
		(8,'bill','register withdrawal for bill payment to provider'),
		(9,'correction','by-hand correction of account balance'),
		(10,'withdrawal','default cash withdrawal'),
		(11,'setup','account setup');
       

insert into aixada_currency 
	values
       (1, 'Euro', 1.0),
       (2, 'Solidary Currency', 1.0);
		

insert into aixada_incident_type
   values        
      (1, 'internal', 'incidents are restricted to loggon in users.'),
      (2, 'internal + email', 'like 1 + incidents are send out as email if possible'),
      (3, 'internal + portal', 'like 1 + incidents are posted on the portal'),
      (4, 'internal + email + portal', 'Incidents are posted internally, send out as email and posted on the portal');
            
      
      
