/*******************************************
 * UPGRADE FILE 
 * to switch from Aixada v 2.6.1 to Aixada 2.6.2
 * 
 * NOTE: source the dump of v2.6.1 Then source this file. 
 */

/**
 *	Evolution of prices
 **/
create table aixada_price (
  product_id     	int   not null,
  ts                    timestamp       default current_timestamp,
  current_price    	decimal(10,2)   not null,
  operator_id           int,
  primary key (product_id, ts),
  foreign key (product_id) references aixada_product(id),
  foreign key (operator_id) references aixada_user(id)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

replace into aixada_price 
	(product_id, ts, current_price) 
select distinct 
       s.product_id, date(c.ts_validated), s.unit_price_stamp 
from aixada_cart c 
left join aixada_shop_item s 
on c.id=s.cart_id;
