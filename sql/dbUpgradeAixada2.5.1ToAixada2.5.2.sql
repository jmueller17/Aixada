/*******************************************
 * UPGRADE FILE 
 * to switch from Aixada v 2.5.1 to Aixada 2.5.2
 * 
 * NOTE: source the dump of v2.5.1 Then source this file. 
 */



/**
 * CART last saved timestamp
 */
alter table 
      aixada_cart 
      add ts_last_saved timestamp default current_timestamp;

      