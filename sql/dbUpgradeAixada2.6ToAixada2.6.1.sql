/*******************************************
 * UPGRADE FILE 
 * to switch from Aixada v 2.6 to Aixada 2.6.1
 * 
 * NOTE: source the dump of v2.6 Then source this file. 
 */



/**
 * PRODUCT has now minimum order quantity
 */
alter table 
      aixada_product
      add  custom_product_ref	varchar(100)	default null after barcode,		
      add  order_min_quantity	decimal(10,4)	default 0 after orderable_type_id,
      add unique key (custom_product_ref, provider_id);

      
/**
 * MEMBER has now bank_name and bank_account
 */
alter table 
	aixada_member
	add bank_account 	varchar(40) 	default null after web,
	add bank_name 		varchar(255) 	default null after web;





      