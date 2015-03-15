

create table aixada_bill (
  	id int not null auto_increment,
  	ref_bill varchar(150) default null,
  	uf_id int not null, 
  	operator_id int not null,
    description varchar(255),
    date_for_bill date default null,
    ts_validated  timestamp default 0, 
  	primary key(id),
  	foreign key(uf_id) references aixada_uf(id),
	  foreign key(operator_id) references aixada_user(id)
) engine=InnoDB default character set utf8 collate utf8_general_ci;


create table aixada_bill_rel_cart(
	id int not null auto_increment,
	bill_id int not null,
	cart_id int not null,
	primary key(id),
  key(cart_id),
	foreign key(bill_id) references aixada_bill(id),
	foreign key(cart_id) references aixada_cart(id), 
  unique  key(bill_id, cart_id)
)engine=InnoDB default character set utf8 collate utf8_general_ci;
