delimiter |

drop procedure if exists get_sales_dates|
create procedure get_sales_dates(in the_date date, in num int)
begin
  declare start_date date default the_date;
  if start_date = 0 then set start_date = date(sysdate()); end if;
  set @q = concat("select distinct date_for_order from aixada_order_item where date_for_order > '",
        start_date, "' limit ", num);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists get_next_equal_shop_date|
create procedure get_next_equal_shop_date()
begin
  select date_for_order 
  from aixada_product_orderable_for_date 
  where date_for_order >= date(sysdate()) 
  limit 1;
end|

drop procedure if exists shopping_dates|
create procedure shopping_dates()
begin
   declare d0 date default sysdate();
   declare d1 date default date_add(d0, interval 3 month);
   select shopping_date, available 
   from aixada_shopping_dates d
   where shopping_date between d0 and d1;
end|

delimiter ;