-- establish products with fake amounts in stock
set foreign_key_checks = 0;

insert 
  into aixada.aixada_product (
    id, provider_id, name, barcode, active, unit_price, iva_percent, stock_min, 
    stock_actual, delta_stock
  )
  select
    prodid,
    prodprov,
    convert(prodnom using utf8),
    prodcode,
    prodactive,
    prodpreuinicial,
    prodiva,
    ifnull(prodstockmin, 0),
    prodstockactual,
    prodstockactual - ifnull(prodstockmin, 0)
  from aixada_old.Producte;

update aixada_product set unit_measure_order_id = 3 where id = 1;
update aixada_product set unit_measure_order_id = 5 where id = 66;

update aixada_product set name = 'Formatge tendre de cabra de 500gr' where id=687;
update aixada_product set unit_measure_order_id = 4 where id = 485;
update aixada_product set unit_measure_shop_id = 3 where id = 485;

update aixada_product set name = 'Formatge tendre de cabra GRAN' where id=688;
update aixada_product set unit_measure_order_id = 6 where id = 486;
update aixada_product set unit_measure_shop_id = 3 where id = 486;

update aixada_product set unit_measure_order_id = 7 where id = 536;

update aixada_product set name='Fonoll (unitat) (1 E)' where barcode=269;
/*
        update aixada_product set name='Kiwi (kg) (3E)' where id=280;
*/

update aixada_product set name = 'Spaguettis de blat (50% sèmola integral de blat dur, 50% farina blanca de blat), UNA CARMANYOLA = 500Grams DEMANAR PER QUILO. comandes a multiples de 250g.  6€/quilo' where id = 752;

update aixada_product set name = 'Tirabuixons de blat (50% sèmola integral de blat dur, 50% farina blanca de blat). UNA CARMANYOLA = 500Grams DEMANAR PER QUILO Comprar a multiples de 250g.  6€/quilo' where id = 753;

update aixada_product set name = 'Tallarines d’espelta (50% farina integral d’espelta, 50% farina blanca d’espelta). UNA CARMANYOLA = 500Grams DEMANAR PER QUILO. Comprar a multiples de 250g.  10€/quilo' where id = 754;

update aixada_product set name = 'Macarrons d.espelta, PREU PER QUILO (kg) UNA CARMANYOLA = 500Grams DEMANAR PER QUILO. comandes a multiples de 250g' where id = 763;

update aixada_product set name = 'Formatge Eco Volcarol de vaca (kg)' where id = 907;

update aixada_product 
   set orderable_type_id = 1 
   where provider_id in (26,20,6,31,4,11,24,27,29,43,42,39,38,33,32,2,5);

update aixada_product 
   set orderable_type_id = 4 
   where provider_id in (30,55,44);

/*
insert into aixada_product_orderable_for_date (product_id, date_for_order)
select distinct product_id, date_for_order from aixada_order_item 
where date_for_order > '1234-01-23';

insert into aixada_product_orderable_for_date(product_id,date_for_order) values (17,'2011-05-11'),(43,'2011-05-11'),(17,'2011-05-18'),(17,'2011-05-25'),(17,'2011-06-01'),(17,'2011-06-08');
*/

insert into aixada_product_orderable_for_date (product_id, date_for_order)
select pc.pcprodid, pc.pcdata from aixada_old.ProducteComanda pc
order by pc.pcdata;

set foreign_key_checks = 1;
