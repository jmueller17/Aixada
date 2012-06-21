delimiter |





/*
 * A query that calculates the total of all sales items for a given uf and date
 */ 
drop function if exists total_price_of_shop_items|
create function total_price_of_shop_items(the_date date, the_uf_id int)
returns float(10,2)
reads sql data
begin
  declare total_price decimal(10,2);
  select 
    sum(i.quantity * p.unit_price * (1 + p.iva_percent/100) * (1 + t.rev_tax_percent/100)) 
    into total_price
    from aixada_shop_item i
      left join aixada_product p
        on i.product_id = p.id
      left join aixada_rev_tax_type t
        on t.id = p.rev_tax_type_id
    where i.date_for_shop = the_date
      and uf_id = the_uf_id
      and i.ts_validated = 0;
  return total_price;
end|

delimiter ;
