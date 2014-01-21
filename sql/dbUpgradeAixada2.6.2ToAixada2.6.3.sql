/*******************************************
 * UPGRADE FILE 
 * to switch from Aixada v 2.6.2 to Aixada 2.6.3
 * 
 * NOTE: source the dump of v2.6.2 Then source this file. 
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
	('Merma', 'Lo que se pierde por bichos, caidas, caducado, ... '),
	('Descuadre', 'Lo que no debería pasar pero siempre pasa. '),
	('SET_ME', 'Temp solution for old movements before stock_movement_type existed.');


/**
 * make changes to stock_movement in order to reference the movement type. 
 */
alter table
	aixada_stock_movement
	add movement_type_id int default 3 after operator_id,
	add foreign key (movement_type_id) references aixada_stock_movement_type(id);




      