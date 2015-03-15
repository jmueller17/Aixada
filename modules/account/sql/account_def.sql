
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
) engine=InnoDB default character set utf8 collate utf8_general_ci;

