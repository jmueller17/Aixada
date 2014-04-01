/*
-- add some fake orders for the future

insert 
  into aixada.aixada_order_cart (
    uf_id, date_for_order
  ) 
  select distinct 
    lcuf, DATE_ADD(DATE_ADD(DATE_ADD(lcdata, INTERVAL 1 YEAR), INTERVAL -1 MONTH), INTERVAL +1 DAY)
  from aixada_old.LiniaComanda
  where year(lcdata)=2009 and month(lcdata)=12;

update
  aixada.aixada_order_cart 
  set	
  aixada_order_cart.total_price = 
  (select sum(lcpreuunitat * lcquantitat) from aixada_old.LiniaComanda where 
  	 DATE_ADD(DATE_ADD(DATE_ADD(lcdata, INTERVAL 1 YEAR), INTERVAL -1 MONTH), INTERVAL +1 DAY) = date_for_order and lcuf = uf_id);
    
insert 
  into aixada.aixada_order_item (
    order_cart_id, date_for_order, uf_id, product_id, quantity, ts_ordered, ts_validated
  )
  select 
    aixada_order_cart.id, DATE_ADD(DATE_ADD(DATE_ADD(lcdata, INTERVAL 1 YEAR), INTERVAL -1 MONTH), INTERVAL +1 DAY), lcuf, lcprod, lcquantitat, lcdata, lcdata
  from 
    aixada_old.LiniaComanda 
    left join aixada.aixada_order_cart
    on DATE_ADD(DATE_ADD(DATE_ADD(lcdata, INTERVAL 1 YEAR), INTERVAL -1 MONTH), INTERVAL +1 DAY) = date_for_order
    where lcuf = uf_id
  on duplicate key update quantity = quantity + values(quantity);
*/

/*
insert into aixada_order_cart (
       id, uf_id, date_for_order, total_price
     )      
     values
       (10001, 81, '2010-12-22', 100.0),
       (10002, 81, '2010-12-29', 200.0),
       (10003, 82, '2010-12-22', 150.4),
       (10004, 82, '2010-12-29', 250.3),
       (10005, 81, '2010-11-10', 350),
       (10006, 82, '2010-11-10', 380),
       (10007, 81, '2010-11-17', 760),
       (10008, 82, '2010-11-17', 880);

insert into aixada_order_item (
       order_cart_id, date_for_order, uf_id, product_id, quantity
     )
     values
       (10001, '2010-12-22', 81, 739, 8.0),
       (10001, '2010-12-22', 81, 485, 1.0),
       (10001, '2010-12-22', 81, 487, 2.0),
       (10002, '2010-12-29', 81, 739, 7.0),
       (10002, '2010-12-29', 81, 66, 2.0),
       (10003, '2010-12-22', 82, 739, 6.0),
       (10003, '2010-12-22', 82, 66, 3.0),
       (10003, '2010-12-22', 82, 12, 1.0),
       (10004, '2010-12-29', 82, 739, 3.0),
       (10004, '2010-12-29', 82, 19, 2.0),
       (10003, '2010-12-22', 81, 66, 5.0),
       (10005, '2010-11-10', 81, 739, 7.0),
       (10005, '2010-11-10', 81, 66, 4.0),
       (10005, '2010-11-10', 81, 12, 2.0),
       (10006, '2010-11-10', 82, 739, 4.0),
       (10006, '2010-11-10', 82, 66, 3.0),
       (10006, '2010-11-10', 82, 12, 1.0),
       (10007, '2010-11-17', 81, 739, 17.0),
       (10007, '2010-11-17', 81, 66, 17.0),
       (10007, '2010-11-17', 81, 12, 17.0),
       (10008, '2010-11-17', 82, 739, 4.0),
       (10008, '2010-11-17', 82, 66, 3.0),
       (10008, '2010-11-17', 82, 12, 1.0);
*/