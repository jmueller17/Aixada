
/**
 * iva types associated then to different products
 */
create table aixada_iva_type (
  id   				tinyint			not null auto_increment,
  percent			smallint 		not null, 
  primary key (id)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;


/**
 * allowed days for ordering
 */
create table aixada_orderable_dates (
  orderable_date    date,
  primary key(orderable_date)
) engine=InnoDB default character set utf8;  



							
alter table aixada_provider add picture varchar(255) default null after web, 
 							add default_closing_offset tinyint after responsible_uf_id,
 							add bank_name varchar(255) default null after web, 
 							add bank_account varchar(40) default null after bank_name;

alter table aixada_member 	add nif varchar(15) default null after address; 							
alter table aixada_user change color_scheme_id gui_theme varchar(50) default null;
alter table aixada_product add picture varchar(255) after description_url;
alter table aixada_order_item 	add closing_date date not null after date_for_order,
								add foreign key (date_for_order) references aixada_orderable_dates(orderable_date);
alter table aixada_product_orderable_for_date add closing_date date not null after date_for_order,
								add foreign key (date_for_order) references aixada_orderable_dates(orderable_date);

drop table aixada_distributor;
drop table aixada_providers_of_distributor;
drop table aixada_account_balance;