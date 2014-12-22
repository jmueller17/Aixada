/*******************************************
 * UPGRADE FILE 
 * to switch from Aixada v 2.03 to Aixada 2.5
 * 
 * NOTE: source the dump of v2.03. Then source this file. 
 * be sure to edit the current date for the updates of aixada_order (see below)
 */



/**
 * IVA_TYPE
 * table, associated then to different products
 */
create table aixada_iva_type (
  id   				smallint 		not null auto_increment,
  percent 			decimal(10,2)  	default 0, 
  description 		varchar(100)	default null,
  primary key (id)
) engine=InnoDB default character set utf8;  


/**
 * populate it, according to the values that exist
 */
insert into
	aixada_iva_type (percent)
select distinct 
	p.iva_percent
from
	aixada_product p
order by p.iva_percent asc;


/**
 * PAYMENT METHOD 
 */
insert into
	aixada_payment_method (description, details)
values
	('stock', 'register gain or loss of stock'),
	('validation', 'register validation of cart'),
	('deposit','register the inpayment of cash'),
	('bill','register withdrawal for bill payment to provider'),
	('correction','by-hand correction of account balance'),
	('withdrawal','default cash withdrawal'),
	('setup', 'account setup');


/**
 *		PRODUCT 
 */
alter table 
	aixada_product
	add iva_percent_id smallint default 1 after rev_tax_type_id,
	add picture varchar(255) after description_url,
	add foreign key (iva_percent_id) references aixada_iva_type(id);

/* replace iva_percent with iva_percent_id */
update 
	aixada_product p,
	aixada_iva_type it
set 
	p.iva_percent_id = it.id
where 
	p.iva_percent = it.percent;
	
alter table
	aixada_product
	drop iva_percent;
	
/* convert orderable_type_id 2,3,4 to 2 */
update 
	aixada_product p
set
	orderable_type_id = 2
where
	orderable_type_id in (3,4);
	
/* update orderable types */
delete from
	aixada_orderable_type
where 
	id > 2; 

update
	aixada_orderable_type
set
	description = "orderable"
where
	id = 2; 



/**
 * AIXADA_PRODUCT_ORDERABLE_FOR_DATE
 */
/* get rid of old orderable dates which are exceptions */
delete from 
	aixada_product_orderable_for_date 
where id > 0;


alter table 
	aixada_product_orderable_for_date 
add closing_date datetime default null after date_for_order,
add unique key (product_id, date_for_order);


/* insert existing dates, based on order items; required due to foreign key constraints. For these old entries, set closing_date = date_for_order */
insert into 
	aixada_product_orderable_for_date (product_id, date_for_order, closing_date)
select distinct
    oi.product_id,
	oi.date_for_order,
	oi.date_for_order
from
	aixada_order_item oi;
	
	
/**
 * create new table AIXADA_ORDER
 */
create table aixada_order (
	id 				int 			not null auto_increment,
	provider_id		int				not null,
	date_for_order	date			not null,
	ts_sent_off		timestamp		default 0,	
	date_received	date			default null,
	date_for_shop	date			default null,
	total			decimal(10,2)  	default 0,
	notes			varchar(255)	default null,	
	revision_status int				default 1,
	delivery_ref	varchar(255)	default null,
	payment_ref		varchar(255)	default null,
	primary key (id),
	key (date_for_order),
	key (date_for_shop),
	key (ts_sent_off),
	foreign key (provider_id) references aixada_provider(id),
	foreign key (date_for_order) references aixada_product_orderable_for_date(date_for_order),
	unique key (date_for_order, provider_id, ts_sent_off)
) engine=InnoDB default character set utf8;      	



/**
 * create new table AIXADA_ORDER_TO_SHOP
 */
create table aixada_order_to_shop (
  order_item_id   	int 		not null,
  uf_id       	  	int  		not null,	
  order_id 			int  		default null,
  unit_price_stamp 	decimal(10,2) default 0,
  product_id 	  	int  		not null,	
  quantity  	  	float(10,4)  default 0.0,
  arrived  			boolean 		default true,
  revised 			boolean  	default false,
  foreign key (order_id) references aixada_order(id),
  foreign key (product_id) references aixada_product(id),
  foreign key (uf_id) references aixada_uf(id)
)engine=InnoDB default character set utf8;


/**
 * create new table AIXADA_CART
 */
create table aixada_cart (
	id 				int 			not null auto_increment,
	name 			varchar(255) 	default null,
	uf_id 	 		int  			not null,
	date_for_shop 	date 			not null,
	operator_id 		int 		default null,
	ts_validated 	timestamp 		default 0, 
	primary key (id),
	key (date_for_shop),
	foreign key (uf_id) references aixada_uf(id),
	foreign key (operator_id) references aixada_user(id),
	unique key (uf_id, date_for_shop, ts_validated)
) engine=InnoDB default character set utf8;


/**
 * AIXADA_ORDER_ITEM 
 */		
alter table 
	aixada_order_item 	
add order_id int default null after id,
add favorite_cart_id int default null after uf_id,
add unit_price_stamp decimal(10,2) default 0 after order_id,
drop key date_for_order,
drop key product_id,
drop key uf_id;


/** looks like that we have order_items where product_id does not exist anymore!! **/
/** dates run up to 2009-09-23, more recent do not exist. Since there is no ref for price, 
 *  it is really useless. 105 different product over 9950 order_items **/
delete from 
	aixada_order_item
where
	product_id not in (select id from aixada_product);


/** 
 * group and assign order_items to aixada_order 
 * many of which before 2011 are actually not order items but stock?! 
 *  **/
insert into 
	aixada_order (date_for_order, provider_id)
select distinct
	oi.date_for_order,
	p.provider_id
from 
	aixada_order_item oi,
	aixada_product p
where 
	p.id = oi.product_id 
	and oi.date_for_order < '2012-10-10'
order by 
	oi.date_for_order asc;

/** assign order_id to each order item. **/	
update 
	aixada_order_item oi,
	aixada_order o,
	aixada_product p
set
	oi.order_id = o.id
where 
	o.date_for_order = oi.date_for_order
	and p.id = oi.product_id
	and o.provider_id = p.provider_id;
	
	
/** set date_for_shop for past orders assuming that shop conicides with date_for_order date!
 *  produces error... best done by hand.  
declare today date default date(sysdate()); */
update
	aixada_order o
set
	o.date_for_shop = o.date_for_order,
	o.revision_status = -1
where
	o.date_for_order < '2012-10-10';
	
	
	
/** copy the unit_price to the unit_price_stamp. This ignores all past iva and price changes and uses the current price!! **/
update
	aixada_order_item oi,
	aixada_product p,
	aixada_iva_type iva,
	aixada_rev_tax_type r
set
	oi.unit_price_stamp = p.unit_price * (1 + iva.percent/100) * (1 + r.rev_tax_percent/100)
where
	oi.product_id = p.id
	and p.iva_percent_id = iva.id
	and p.rev_tax_type_id = r.id;	
		
	
/** add the keys to order_item **/
alter table 
	aixada_order_item
add foreign key (order_id) references aixada_order(id),
add foreign key (uf_id) references aixada_uf(id),
add foreign key (product_id) references aixada_product(id),
add foreign key (favorite_cart_id) references aixada_cart(id),
add foreign key (product_id, date_for_order) references aixada_product_orderable_for_date(product_id, date_for_order),
add unique key (order_id, uf_id, product_id );


/** calculate the order total for each order and update aixada_order **/
update 
	aixada_order o, 
	aixada_order_item oi
set
	o.total = 	(select
	    		sum(ois.quantity * ois.unit_price_stamp)
	  		from 
	  			aixada_order_item ois
	  		where
	  			ois.order_id = oi.order_id)
where 
	oi.order_id = o.id; 



/**
 * AIXADA_SHOP_ITEM
 */
alter table
	aixada_shop_item
	add cart_id int not null after id,
	add order_item_id int default null after cart_id,
	add unit_price_stamp	decimal(10,2)	default 0 after order_item_id,
	add iva_percent decimal(10,2) default 0.00 after quantity,
	add rev_tax_percent decimal(10,2) default 3.0 after iva_percent;
	

/** group and assign shop_items to aixada_cart **/
insert into 
	aixada_cart (uf_id, date_for_shop, operator_id, ts_validated)
select distinct
	si.uf_id, 
	si.date_for_shop,
	si.operator_id, 
	si.ts_validated
from 
	aixada_shop_item si;
	
	
/** assign cart_id to each shop item. **/	
update 
	aixada_shop_item si,
	aixada_cart c
set
	si.cart_id = c.id
where 
	si.uf_id = c.uf_id
	and si.date_for_shop = c.date_for_shop
	and si.ts_validated = c.ts_validated;
	

/** copy te price info into aixada_shop_item. This is based on the current price since we did not keep
 *  track of price changes!!
 */
update
	aixada_shop_item si,
	aixada_product p,
	aixada_iva_type iva,
	aixada_rev_tax_type r
set
	si.unit_price_stamp = p.unit_price * (1 + iva.percent/100) * (1 + r.rev_tax_percent/100)
where
	si.product_id = p.id
	and p.iva_percent_id = iva.id
	and p.rev_tax_type_id = r.id;
	
	

alter table aixada_shop_item
	drop key date_for_shop,
	drop key ts_validated,
	drop foreign key aixada_shop_item_ibfk_1, /** uf_id **/
	drop foreign key aixada_shop_item_ibfk_2, /** product_id **/
	drop foreign key aixada_shop_item_ibfk_3, /** operator_id **/
	drop operator_id,
	drop uf_id, 
	drop date_for_shop,
	drop ts_validated,
	add foreign key (cart_id) references aixada_cart(id),
  	add foreign key (order_item_id) references aixada_order_item(id),
  	add foreign key (product_id) references aixada_product(id),	
  	add unique key (cart_id, product_id, order_item_id);
	

	
/**
 * incidents. since many incidents were concnerned with products and stock
 * we can change their functionlity: incident type comes to specify distribution level 
 * from private (internal) to public (portal, email, twitter?). 
 * Set all incidents to internal(private) 
 */
update 
	aixada_incident_type
set
	description = "internal",
	definition = "incidents are restricted to loggon in users."
where 
	id=1; 
	
update 
	aixada_incident_type
set
	description = "internal + email",
	definition = "like 1 + incidents are sent out as email if possible"
where 
	id=2; 

update 
	aixada_incident_type
set
	description = "internal + portal",
	definition = "like 1 + incidents are posted on the portal"
where 
	id=3;
	
update 
	aixada_incident_type
set
	description = "internal + email + portal",
	definition = "incidents are posted internally, sent out as email and posted on the portal"
where 
	id=4; 	
	
update 
	aixada_incident
set
	incident_type_id = 1;
	
	
	
/**
 * PROVIDER
 */	
alter table 
	aixada_provider 
	add picture varchar(255) default null after web, 
	add offset_order_close int default 4 after responsible_uf_id,
	add bank_name varchar(255) default null after web, 
	add bank_account varchar(40) default null after bank_name;


/**
 * MEMBER
 */		
alter table 
	aixada_member 	
	add nif varchar(15) default null after address,
	add custom_member_ref varchar(100) default null after id;

/**
 * USER
 */	
alter table 
	aixada_user 
	change color_scheme_id gui_theme varchar(50) default null;

update 
	aixada_user
set
	gui_theme = null;

drop table if exists aixada_providers_of_distributor;
drop table if exists aixada_account_balance;
drop table aixada_distributor;
drop table aixada_favorite_order_item;
drop table aixada_favorite_order_cart;








