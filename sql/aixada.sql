
/***********************************************
 *	Aixada DB Structure 
 *
 **********************************************/

/**
 *  If a table has two or more foreign keys that reference the same 
 *  external table, the field names should be of the form 
 *  {external table}_{external key name}_{internal name of the field}
 *  For example 
 *  aixada_product.unit_measure_order_id,
 *  aixada_product.unit_measure_shop_id,
 *  aixada_uf_account.aixada_member_member,
 *  aixada_uf_account.aixada_member_operator 
 **/



/*
 * Family Unit: UF
 */
create table aixada_uf (
  id   	     		int				not null,
  name				varchar(255)    not null,
  active     		tinyint 		default 1,   	
  created			timestamp 		default current_timestamp,
  mentor_uf         int             default null,
  primary key (id)
) engine=InnoDB default character set utf8;



/*
 * Member
 */
create table aixada_member (
  id 	       		int				not null auto_increment,
  custom_member_ref	varchar(100)	default null,
  uf_id      		int,
  name	     		varchar(255) 	not null,
  address			varchar(255) 	not null,
  nif 				varchar(15) 	default null,
  zip				varchar(10)		default null,
  city				varchar(255) 	not null,
  phone1    		varchar(50) 	default null,
  phone2			varchar(50) 	default null,
  web				varchar(255) 	default null,
  picture           varchar(255)    default null,
  notes  	 		text 			default null,
  active     	  	tinyint			default 1, 
  participant		bool 			default true,
  adult		        bool			default true, 
  ts			  	timestamp not null default current_timestamp,
  primary key (id),
  foreign key (uf_id)  references aixada_uf(id)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;


/**
 * provider
 **/
create table aixada_provider (
  id   	          	int				not null auto_increment,
  name	     	  	varchar(255) 	not null,
  contact           varchar(255)    default null,
  address			varchar(255)    default null,
  nif               varchar(15)     default null,
  zip				varchar(10)		default null,
  city				varchar(255) 	default null,
  phone1    	  	varchar(50) 	default null,
  phone2			varchar(50) 	default null,
  fax	     	  	varchar(100) 	default null,	
  email				varchar(100) 	default null,
  web				varchar(255) 	default null,
  bank_name 		varchar(255) 	default null, 
  bank_account 		varchar(40) 	default null,
  picture 			varchar(255) 	default null,
  notes  			text 			default null,
  active     	  	tinyint 		default 1,
  responsible_uf_id	int     		default null,
  offset_order_close int			default 4, 			/* default offset closing of order in days*/
  ts			  	timestamp 		not null default current_timestamp,
  primary key (id),
  key (active),
  foreign key (responsible_uf_id) references aixada_uf(id)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;




/**
 * User
 **/
create table aixada_user (
  id   	     			int				not null auto_increment,
  login			 		varchar(50)	 	not null,
  password   			varchar(255) 	not null,
  email			 		varchar(100) 	not null,
  uf_id                 int,
  member_id             int, 
  provider_id           int,
  language              char(5)        default 'en',
  gui_theme	       		varchar(50)    default null,
  last_login_attempt    timestamp,
  last_successful_login timestamp,
  created_on            timestamp,
  primary key (id),
  foreign key (uf_id) references aixada_uf(id),
  foreign key (member_id) references aixada_member(id),
  foreign key (provider_id) references aixada_provider(id)  
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;


/**
 * Join users and roles
 **/
create table aixada_user_role (
  user_id    		int	not null,
  role  			varchar(100)	not null,
  primary key (user_id, role),
  foreign key (user_id)	references aixada_user(id)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;
  

/**
 * different product categories
 **/
create table aixada_product_category (
  id   				int				not null,
  description		varchar(255) 	not null, 
  primary key (id)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;


/**
 * different ways of ordering
 **/
create table aixada_orderable_type (
  id   				tinyint			not null auto_increment,
  description		varchar(255) 	not null, 
  primary key (id)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;


/**
 *      aixada_unit_measures
 *      stores the possible unit measures (kg, piece, 500g etc)
 */
create table aixada_unit_measure (
  id   	     		tinyint		 not null auto_increment,
  unit				varchar(50)	 not null,
  primary key (id)
) engine=InnoDB default character set utf8;



create table aixada_rev_tax_type (
  id   	     		tinyint		 not null auto_increment,
  name                  varchar(255)     not null,
  description		varchar(50)	 not null,
  rev_tax_percent	decimal(10,2),
  primary key (id)
) engine=InnoDB default character set utf8;
 

/**
 * iva types associated then to different products
 */
create table aixada_iva_type (
  id   			     	smallint		not null auto_increment,
  name                          varchar(255) not null, 
  percent			decimal(10,2) 	not null, 
  description 		        varchar(100)	default null,
  primary key (id)
) engine=InnoDB default character set utf8;


/**
 * product
 **/
 create table aixada_product (
  id   	              	int				not null auto_increment,
  provider_id        	int 			not null,
  name	     	      	varchar(255) 	not null,
  description	      	text,
  barcode 	 			varchar(50)		default null,
  active     	      	tinyint			default 1,
  responsible_uf_id     int             default null,
  orderable_type_id	tinyint				default 2,
  category_id	      	int				default 1,
  rev_tax_type_id		tinyint			default 1,
  iva_percent_id  	    smallint 		default 1,
  unit_price       		decimal(10,2) 	default 0.0,
  unit_measure_order_id	tinyint			default 1,
  unit_measure_shop_id	tinyint			default 1,
  stock_min    	      	decimal(10,4) 	default 0, 
  stock_actual 	      	decimal(10,4) 	default 0, 
  delta_stock           decimal(10,4)   default 0,
  description_url 		varchar(255)	default null,	
  picture 				varchar(255) 	default null,
  ts			  		timestamp 		not null default current_timestamp,
  primary key (id),
  foreign key (provider_id)    			references aixada_provider(id) on delete cascade,
          key (active),
  foreign key (responsible_uf_id) 		references aixada_uf(id),
  foreign key (orderable_type_id)   	references aixada_orderable_type(id),
  foreign key (category_id)    			references aixada_product_category(id),
  foreign key (rev_tax_type_id)    		references aixada_rev_tax_type(id),
  foreign key (iva_percent_id)			references aixada_iva_type(id),
  foreign key (unit_measure_order_id) 	references aixada_unit_measure(id),
  foreign key (unit_measure_shop_id) 	references aixada_unit_measure(id),
  key(delta_stock)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;




/**
 * products orderable for a given date	
 */
create table aixada_product_orderable_for_date (
  id   	                int     	not null auto_increment,
  product_id   	     	int     	not null,
  date_for_order        date    	not null,
  closing_date 			datetime 	default null,
  primary 	key (id),
  			key (date_for_order),
  foreign 	key (product_id) references aixada_product(id),
  unique 	key (product_id, date_for_order)
) engine=InnoDB default character set utf8;       
 

/**
 * aixada_order
 */
create table aixada_order (
	id 				int 			not null auto_increment,
	provider_id		int				not null,
	date_for_order	date			not null,
	ts_sent_off		timestamp		default 0,	
	date_received	date			default null,
	date_for_shop	date			default null,
	total			decimal(10,2)	default 0,
	notes			varchar(255)	default null,	
	revision_status	int				default 1,
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
 * used for grouping either entries in aixada_order_items or aixada_shop_items. Order items can
 * be grouped for favorite order carts. Shop items are always grouped through the aixada_cart(id)
 */
create table aixada_cart (
	id 				int 			not null auto_increment,
	name			varchar(255)	default null,
	uf_id 			int 			not null,
	date_for_shop	date			not null,
	operator_id		int				default null,
	ts_validated	timestamp		default 0, 
	primary key (id),
	key (date_for_shop),
	foreign key (uf_id) references aixada_uf(id),
	foreign key (operator_id) references aixada_user(id),
	unique key (uf_id, date_for_shop, ts_validated)
) engine=InnoDB default character set utf8;


/**
 *	aixada_order_item. 
 */
create table aixada_order_item (
  id  	     		int		  	not null auto_increment,
  uf_id     	  	int 		not null,	
  favorite_cart_id	int			default null,
  order_id			int 		default null,
  unit_price_stamp	decimal(10,2)	default 0,
  date_for_order 	date		not null,
  product_id	  	int 		not null,	
  quantity 	  		float(10,4) default 0.0,				
  ts_ordered   	  	timestamp 	default current_timestamp,
  primary key (id),
  foreign key (order_id) references aixada_order(id),
  foreign key (uf_id) references aixada_uf(id),
  foreign key (product_id) references aixada_product(id),
  foreign key (favorite_cart_id) references aixada_cart(id),
  foreign key (product_id, date_for_order) references aixada_product_orderable_for_date(product_id, date_for_order),
  unique  key (order_id, uf_id, product_id)
) engine=InnoDB default character set utf8; 



/**
 *	stores the individual product items, quantity, price for a given sale. 
 *  the unit_price_stamp field stores price incl. iva + rev tax! 
 */
create table aixada_shop_item (
  id 	          	int				not null auto_increment,
  cart_id			int				not null,
  order_item_id		int				default null,
  unit_price_stamp	decimal(10,2)	default 0,
  product_id  		int 			not null,
  quantity      	float(10,4) 	default 0.0,
  iva_percent		decimal(10,2)	default 0,
  rev_tax_percent	decimal(10,2)	default 0,
  primary key (id),
  foreign key (cart_id) references aixada_cart(id),
  foreign key (order_item_id) references aixada_order_item(id),
  foreign key (product_id) references aixada_product(id),	
  unique key (cart_id, product_id, order_item_id)
) ENGINE=InnoDB default character set utf8;


/**
 * temporary table where order items gets stored during revision of
 * products. once revision is finished, items get copied into aixada_shop_item
 * and deleted here. 
 */
create table aixada_order_to_shop (
  order_item_id  	int		  	not null,
  uf_id     	  	int 		not null,	
  order_id			int 		default null,
  unit_price_stamp	decimal(10,2)	default 0,
  product_id	  	int 		not null,	
  quantity 	  		float(10,4) default 0.0,
  arrived 			boolean		default true,
  revised			boolean 	default false,
  foreign key (order_id) references aixada_order(id),
  foreign key (product_id) references aixada_product(id),
  foreign key (uf_id) references aixada_uf(id)
) engine=InnoDB default character set utf8;


/**
 *	stock movements
 *	
 */
create table aixada_stock_movement (
  id          		int			not null auto_increment,
  product_id  		int 		not null,
  operator_id		int 		not null,
  amount_difference	decimal(10,4),
  description  		varchar(255),
  resulting_amount	decimal(10,4),
  ts   	  			timestamp 	default current_timestamp,
  primary key (id),
  foreign key (product_id) references aixada_product(id), 
  foreign key (operator_id) references aixada_user(id),
  key (ts)
) engine=InnoDB default character set utf8;



/**
 * Stores different payment and transfer methods
 */
create table aixada_payment_method (
  id   	     	tinyint   not null auto_increment,
  description   varchar(50) not null,
  details	 	varchar(255) default null,
  primary key (id)
) engine=InnoDB default character set utf8;


/**
 * Stores different currencies
 * one_euro says how much one euro is worth
 */
create table aixada_currency (
  id   	     	tinyint   		not null auto_increment,
  name		 	varchar(50) 	not null,
  one_euro	 	decimal(10,4)  	not null, 
  primary key (id)
) engine=InnoDB default character set utf8;


/* 
 *   Account numbers:
 *  -1          Manteniment
 *  -2	       	Consum
 *  1001..1999  regular UF accounts of the form 1000 + uf_id
 *  2001..2999  regular provider account of the form 2000 + provider_id
 */   

create table aixada_account (
  id   	     		  	int	        	not null auto_increment,
  account_id		  	int				not null,
  quantity		  		decimal(10,2) 	not null,
  payment_method_id	  	tinyint			default 1,
  currency_id		  	tinyint 		default 1,
  description		  	varchar(255),
  operator_id		  	int				not null,
  ts			  		timestamp 		not null default current_timestamp,
  balance		  		decimal(10,2)	not null default 0.0,
  primary key(id),
  key(account_id),
  key(ts),
  foreign key(operator_id) references aixada_user(id),
  foreign key(payment_method_id) references aixada_payment_method(id),
  foreign key(currency_id) references aixada_currency(id)
) engine = InnoDB default character set utf8;



/**
 *	Types of incidents 
 **/
create table aixada_incident_type (
  id        		tinyint		not null auto_increment,
  description 		varchar(255)   	not null,
  definition 		text 		not null,
  primary key (id)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;




/**
 *	Incidents
 **/
create table aixada_incident (
  id 	              	int             not null auto_increment,
  subject               varchar(255)    not null,
  incident_type_id   	tinyint         not null,
  operator_id  			int             not null,
  details	      		text,
  priority              int             default 3,
  ufs_concerned         varchar(100),
  commission_concerned  varchar(100),
  provider_concerned    varchar(100),
  ts					timestamp 	default current_timestamp,  
  status                varchar(10)     default 'Open',
  primary key (id),
  foreign key (incident_type_id) references aixada_incident_type(id),
  foreign key (operator_id) references aixada_user(id),
  key (ts)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;



