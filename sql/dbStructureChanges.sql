
/**
 * iva types associated then to different products
 */
drop table if exists aixada_iva_type;
create table aixada_iva_type (
  id   				smallint		not null auto_increment,
  percent			decimal(10,2) 	not null, 
  primary key (id)
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

insert into
	aixada_iva_type (percent)
select distinct 
	p.iva_percent
from
	aixada_product p
order by p.iva_percent asc;


/* get rid of old orderable dates which are exceptions */
delete from 
	aixada_product_orderable_for_date 
where id > 0;

/* insert existing dates, based on order items; required due to foreign key constraints */
insert into 
	aixada_product_orderable_for_date (product_id, date_for_order)
select distinct
    oi.product_id,
	oi.date_for_order
from
	aixada_order_item oi;
	

alter table 
	aixada_order_item 	
	/*add cart_id int default 0,*/
	add closing_date datetime not null after date_for_order,
	add foreign key (date_for_order) references aixada_product_orderable_for_date(date_for_order);
	
/*alter table
	aixada_shop_item
	add cart_id int default 0;*/
							
alter table 
	aixada_provider 
	add picture varchar(255) default null after web, 
	add offset_order_close int default 1 after responsible_uf_id,
	add bank_name varchar(255) default null after web, 
	add bank_account varchar(40) default null after bank_name;


alter table 
	aixada_member 	
	add nif varchar(15) default null after address;


alter table 
	aixada_user 
	change color_scheme_id gui_theme varchar(50) default null;

alter table 
	aixada_product 
	add iva_percent_id smallint default 1 after iva_percent,
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
	
	
alter table aixada_product_orderable_for_date 
	add closing_date datetime not null after date_for_order;
	


drop table aixada_distributor;
drop table if exists aixada_providers_of_distributor;
drop table if exists aixada_account_balance;




