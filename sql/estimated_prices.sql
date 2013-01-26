drop table if exists aixada_estimated_prices;
create table aixada_estimated_prices (
  product_id     	int		not null,
  ts                  	timestamp       not null default current_timestamp,
  min_estimated_price   decimal(10,2)   default null,
  max_estimated_price   decimal(10,2)   default null,
  true_price 		decimal(10,2)   default null,
  primary key (product_id, ts)  
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;
