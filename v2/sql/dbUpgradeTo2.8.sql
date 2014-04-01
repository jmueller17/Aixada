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

