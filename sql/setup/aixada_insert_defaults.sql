

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
       (2, 'orderable'),
       (3, 'order_notes');
       
insert into aixada_rev_tax_type values
       (1, 'default revolutionary tax', 'what everybody pays', 3.00),
       (2, 'no revolutionary tax', 'zero tax', 0),
       (3, 'luxury', 'for capitalists', 5.00);
       
insert into aixada_iva_type values
		(1, 'no tax', 0, 'the best'),
		(2, '10 percent', 10, 'group XYZ products');

insert into aixada_unit_measure values
       (1, 'unit is not set', 'SET_ME'),
       (2, 'unit', 'u'),
       (3, 'grams', 'g'),
       (4, 'kilograms', 'kg'),
       (5, 'unit of 250g', '250g'),
       (6, 'unit of half kilo','500g'),
       (7, 'mililiters','ml'),
       (8, 'liter','L'),
       (9, 'one liter', '1L'),
       (10, 'quarter of a liter','250ml'),
       (11, 'half a liter','500ml'),
       (12, 'three quarters of a liter','750ml'),
       (13, 'bunch','bunch');


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
            
insert into 
	aixada_stock_movement_type (name, description)
values
	('SET_ME', 'Temp solution for old movements before stock_movement_type existed.'),
	('Merma', 'Lo que se pierde por bichos, caidas, caducado, ... '),
	('Descuadre', 'Lo que no debería pasar pero siempre pasa. '),
	('Added', 'Llega un pedido de stock y se añade.');
	
insert into 
	aixada_version (version) 
values 
		('2.8'); 

-- create accounts descriptions --
insert into
    aixada_account_desc (id, description, account_type)
values
    (1, 'Manteniment',                  2),
    (2, 'Consum (stock adjustments)',   2),
    (3, 'Cashbox',                      1);
