/*******************************************
 * UPGRADE FILE 
 * to switch from Aixada v 2.5 to Aixada 2.5.1
 * 
 * NOTE: source the dump of v2.5. Then source this file. 
 * be sure to edit the current date for the updates of aixada_order (see below)
 */



/**
 * IVA_TYPE
 * table, associated then to different products
 */
alter table 
      aixada_iva_type 
      add name varchar(255) not null after id;

update aixada_iva_type set name = percent;


alter table 
      aixada_rev_tax_type
      add name varchar(255) not null after id;

update aixada_rev_tax_type set name = rev_tax_percent;


alter table 
      aixada_unit_measure
      add name varchar(255) not null after id;

update aixada_unit_measure set name = unit;

      