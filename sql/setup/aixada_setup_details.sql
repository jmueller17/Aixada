/*
insert into aixada_user (
    id, login, password
) values (-1, 'aixada', 'xxXx0Xx1Xx2Xx4Xx9Xx11');
*/

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
       (1, 'default revolutionary tax', 1.03),
       (2, 'basic necessities', 1.0),
       (3, 'luxury', 1.05);

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


insert into aixada_payment_method (id, description)
       values 
       (1, 'cash'), 
       (2, 'envelope'), 
       (3, 'transfer'),
       (4, 'check');

insert into aixada_currency 
       values
       (1, 'Euro', 1.0),
       (2, 'Solidary Currency', 1.0);

insert into aixada_incident_type
   values        
      (1, 'Missing Stock', 'Usar si no hay suficiente cantidad de un producto'),
      (2, 'Products did not arrive', 'Usar si no llegó genero de un productor'),
      (3, 'Stock arrived', 'Usar para decir que entró stock'),
      (4, 'Sin determinar', 'qué pasó?');
