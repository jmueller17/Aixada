
set foreign_key_checks = 0;

insert 
  into aixada.aixada_order_item (
    date_for_order, uf_id, product_id, quantity, ts_ordered
  )
  select 
    lcdata, lcuf, lcprod, lcquantitat, date_add(lcdata, interval -1 week)
  from 
    aixada_old.LiniaComanda 
  on duplicate key update quantity = cast(quantity + values(quantity) as decimal(10,2));

/*
insert 
  into aixada.aixada_order_item (
   date_for_order, uf_id, product_id, quantity
  ) select 
   '2011-02-02', uf_id, product_id, quantity
   from aixada.aixada_order_item as oi2
   where oi2.date_for_order = '2010-12-01';
*/

delete from aixada_order_item where date_for_order=0 or uf_id=0 or product_id=NULL;


insert into aixada_order_item (
  date_for_order, uf_id, product_id, quantity
) values 
  ('1234-01-23', 81, 590, 2),
  ('1234-01-23', 81, 812, 3),
  ('1234-01-23', 81, 909, 2),
  ('1234-01-23', 81, 910, 3),
  ('1234-01-23', 82, 590, 2),
  ('1234-01-23', 82, 812, 3),
  ('1234-01-23', 82, 910, 2),
  ('1234-01-23', 82, 911, 3);

set @q = date_add(sysdate(), interval -5 month);

insert into aixada_product_orderable_for_date (
  product_id, date_for_order
) select pcprodid, pcdata
  from aixada_old.ProducteComanda
  where pcdata >= @q;

select concat(year(sysdate()),'-01-01') into @q;

replace into aixada_shop_item (
    uf_id, date_for_shop, product_id, quantity
  ) select i.uf_id, i.date_for_order, i.product_id, i.quantity
    from aixada_order_item i
    where date_for_order > @q;


set foreign_key_checks = 1;

-- 