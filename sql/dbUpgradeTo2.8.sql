/*******************************************
 * UPGRADE FILE 
 * to switch from Aixada v 2.7. to Aixada 2.8
 * 
 * NOTE: source the dump of v2.7 Then source this file. 
 */


/**
 * Types of stock movements such as stock corrected, loss, etc. 
 */
create table aixada_stock_movement_type(
  id              int     not null auto_increment,
  name            varchar(30) not null, 
  description     varchar(255),
  primary key (id)
) engine=InnoDB default character set utf8 collate utf8_general_ci;


/**
 * insert default values
 */
insert into 
	aixada_stock_movement_type (name, description)
values
	('SET_ME', 'Temp solution for old movements before stock_movement_type existed.'),
	('Merma', 'Lo que se pierde por bichos, caidas, caducado, ... '),
	('Descuadre', 'Lo que no debería pasar pero siempre pasa. '),
	('Added', 'Llega un pedido de stock y se añade.');


/**
 * make changes to stock_movement in order to reference the movement type. 
 */
alter table
	aixada_stock_movement
	add movement_type_id int default 1 after operator_id,
	add foreign key (movement_type_id) references aixada_stock_movement_type(id);



/**
 *	db version + upgrade history
 */
create table aixada_version (
  id int not null auto_increment,
  module_name varchar(100) default 'main' not null,
  version varchar(42) not null,
  primary key(id)
) engine=InnoDB default character set utf8 collate utf8_general_ci;

insert into 
	aixada_version (version) 
values 
		('2.8'); 


/**
 *	aixada_order_to_shop now is not a temporal table
 */
insert into 
	aixada_order_to_shop (
        order_item_id, uf_id, order_id, unit_price_stamp, product_id,
        quantity, arrived, revised
    )
select
	si.order_item_id, oi.uf_id,oi.order_id, si.unit_price_stamp, si.product_id,
    si.quantity, 1 arrived, 1  revised
from
    aixada_shop_item si,
    aixada_order_item oi
where 
    oi.id = si.order_item_id;

/**
 * "unit_price_stamp": Add 4 decimals, and always supplemented
 *      with `iva_percent` & `rev_tax_percent`.
 */
ALTER TABLE aixada_order_item 
	MODIFY unit_price_stamp		decimal(14,6)	default 0,
	ADD COLUMN iva_percent 		decimal(5,2)	default 0 AFTER unit_price_stamp,
	ADD COLUMN rev_tax_percent	decimal(5,2)	default 0 AFTER iva_percent;
ALTER TABLE aixada_shop_item 
	MODIFY unit_price_stamp		decimal(14,6)	default 0;
ALTER TABLE aixada_order_to_shop
	MODIFY unit_price_stamp		decimal(14,6)	default 0,
	ADD COLUMN iva_percent 		decimal(5,2)	default 0 AFTER unit_price_stamp,
	ADD COLUMN rev_tax_percent	decimal(5,2)	default 0 AFTER iva_percent;
   
/**
 * Fill `iva_percent` & `rev_tax_percent` in existing records
 *      on `aixada_order_to_shop`.
 */
SET SQL_SAFE_UPDATES = 0;
-- from: aixada_shop_item
    update aixada_order_to_shop ots
    join (aixada_shop_item si)
    on ots.order_item_id = si.order_item_id
    set ots.iva_percent = si.iva_percent,
        ots.rev_tax_percent = si.rev_tax_percent
    where ots.iva_percent = 0 and 
        ots.rev_tax_percent = 0;
-- from: aixada_product (ots.order_item_id not exist on aixada_shop_item)
    update aixada_order_to_shop ots
    left join (aixada_shop_item si)
    on ots.order_item_id = si.order_item_id
    join ( aixada_product p,
        aixada_rev_tax_type rev,
        aixada_iva_type iva)
    on  p.id = ots.product_id and
        rev.id = p.rev_tax_type_id and
        iva.id = p.iva_percent_id
    set ots.iva_percent = iva.percent,
        ots.rev_tax_percent = rev.rev_tax_percent
    where ots.iva_percent = 0 and 
        ots.rev_tax_percent = 0 and 
        si.order_item_id is null;
SET SQL_SAFE_UPDATES = 1;
