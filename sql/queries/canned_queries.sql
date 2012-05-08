delimiter |
/* The contents of this file are generated automatically.  Do not edit it, but instead run php make_canned_responses.php */

drop procedure if exists aixada_account_list_all_query|
create procedure aixada_account_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter char(100))
begin
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = "select
      aixada_account.id,
      aixada_account.account_id,
      aixada_account.quantity,
      aixada_payment_method.description as payment_method,
      aixada_currency.name as currency,
      aixada_account.description,
      aixada_account.operator_id,
      aixada_account.ts,
      aixada_account.balance 
    from aixada_account 
    left join aixada_payment_method as aixada_payment_method on aixada_account.payment_method_id=aixada_payment_method.id
    left join aixada_currency as aixada_currency on aixada_account.currency_id=aixada_currency.id";
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_account_balance_list_all_query|
create procedure aixada_account_balance_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter char(100))
begin
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = "select
      aixada_account_balance.account_id,
      aixada_account_balance.balance,
      aixada_account_balance.last_update 
    from aixada_account_balance ";
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_currency_list_all_query|
create procedure aixada_currency_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter char(100))
begin
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = "select
      aixada_currency.id,
      aixada_currency.name,
      aixada_currency.one_euro 
    from aixada_currency ";
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_distributor_list_all_query|
create procedure aixada_distributor_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter char(100))
begin
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = "select
      aixada_distributor.id,
      aixada_distributor.name,
      aixada_distributor.contact,
      aixada_distributor.address,
      aixada_distributor.nif,
      aixada_distributor.zip,
      aixada_distributor.city,
      aixada_distributor.phone1,
      aixada_distributor.phone2,
      aixada_distributor.fax,
      aixada_distributor.email,
      aixada_distributor.web,
      aixada_distributor.notes,
      aixada_distributor.active,
      concat(aixada_uf.id, ' ', aixada_uf.name) as responsible_uf 
    from aixada_distributor 
    left join aixada_uf as aixada_uf on aixada_distributor.responsible_uf_id=aixada_uf.id";
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_favorite_order_cart_list_all_query|
create procedure aixada_favorite_order_cart_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter char(100))
begin
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = "select
      aixada_favorite_order_cart.id,
      aixada_uf.name as uf,
      aixada_favorite_order_cart.name 
    from aixada_favorite_order_cart 
    left join aixada_uf as aixada_uf on aixada_favorite_order_cart.uf_id=aixada_uf.id";
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_favorite_order_item_list_all_query|
create procedure aixada_favorite_order_item_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter char(100))
begin
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = "select
      aixada_favorite_order_item.id,
      aixada_favorite_order_cart.name as favorite_order_cart,
      aixada_uf.name as uf,
      aixada_product.name as product,
      aixada_favorite_order_item.quantity,
      aixada_favorite_order_item.ts_ordered 
    from aixada_favorite_order_item 
    left join aixada_favorite_order_cart as aixada_favorite_order_cart on aixada_favorite_order_item.favorite_order_cart_id=aixada_favorite_order_cart.id
    left join aixada_uf as aixada_uf on aixada_favorite_order_item.uf_id=aixada_uf.id
    left join aixada_product as aixada_product on aixada_favorite_order_item.product_id=aixada_product.id";
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_incident_list_all_query|
create procedure aixada_incident_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter char(100))
begin
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = "select
      aixada_incident.id,
      aixada_incident.subject,
      aixada_incident_type.description as incident_type,
      aixada_incident.operator_id,
      aixada_incident.details,
      aixada_incident.priority,
      aixada_incident.ufs_concerned,
      aixada_incident.commission_concerned,
      aixada_incident.provider_concerned,
      aixada_incident.ts,
      aixada_incident.status 
    from aixada_incident 
    left join aixada_incident_type as aixada_incident_type on aixada_incident.incident_type_id=aixada_incident_type.id";
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_incident_type_list_all_query|
create procedure aixada_incident_type_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter char(100))
begin
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = "select
      aixada_incident_type.id,
      aixada_incident_type.description,
      aixada_incident_type.definition 
    from aixada_incident_type ";
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_member_list_all_query|
create procedure aixada_member_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter char(100))
begin
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = "select
      aixada_member.id,
      aixada_uf.name as uf,
      aixada_member.name,
      aixada_member.address,
      aixada_member.zip,
      aixada_member.city,
      aixada_member.phone1,
      aixada_member.phone2,
      aixada_member.web,
      aixada_member.picture,
      aixada_member.notes,
      aixada_member.active,
      aixada_member.participant,
      aixada_member.adult 
    from aixada_member 
    left join aixada_uf as aixada_uf on aixada_member.uf_id=aixada_uf.id";
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_order_item_list_all_query|
create procedure aixada_order_item_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter char(100))
begin
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = "select
      aixada_order_item.id,
      aixada_order_item.date_for_order,
      aixada_uf.name as uf,
      aixada_product.name as product,
      aixada_order_item.quantity,
      aixada_order_item.ts_ordered 
    from aixada_order_item 
    left join aixada_uf as aixada_uf on aixada_order_item.uf_id=aixada_uf.id
    left join aixada_product as aixada_product on aixada_order_item.product_id=aixada_product.id";
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_orderable_type_list_all_query|
create procedure aixada_orderable_type_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter char(100))
begin
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = "select
      aixada_orderable_type.id,
      aixada_orderable_type.description 
    from aixada_orderable_type ";
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_payment_method_list_all_query|
create procedure aixada_payment_method_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter char(100))
begin
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = "select
      aixada_payment_method.id,
      aixada_payment_method.description,
      aixada_payment_method.details 
    from aixada_payment_method ";
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_product_list_all_query|
create procedure aixada_product_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter char(100))
begin
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = "select
      aixada_product.id,
      aixada_provider.name as provider,
      aixada_product.name,
      aixada_product.description,
      aixada_product.barcode,
      aixada_product.active,
      concat(aixada_uf.id, ' ', aixada_uf.name) as responsible_uf,
      aixada_orderable_type.description as orderable_type,
      aixada_product_category.description as category,
      aixada_rev_tax_type.description as rev_tax_type,
      aixada_product.unit_price,
      aixada_product.iva_percent,
      aixada_unit_measure_order.unit as unit_measure_order,
      aixada_unit_measure_shop.unit as unit_measure_shop,
      aixada_product.stock_min,
      aixada_product.stock_actual,
      aixada_product.delta_stock,
      aixada_product.description_url 
    from aixada_product 
    left join aixada_provider as aixada_provider on aixada_product.provider_id=aixada_provider.id
    left join aixada_uf as aixada_uf on aixada_product.responsible_uf_id=aixada_uf.id
    left join aixada_orderable_type as aixada_orderable_type on aixada_product.orderable_type_id=aixada_orderable_type.id
    left join aixada_product_category as aixada_product_category on aixada_product.category_id=aixada_product_category.id
    left join aixada_rev_tax_type as aixada_rev_tax_type on aixada_product.rev_tax_type_id=aixada_rev_tax_type.id
    left join aixada_unit_measure as aixada_unit_measure_order on aixada_product.unit_measure_order_id=aixada_unit_measure_order.id
    left join aixada_unit_measure as aixada_unit_measure_shop on aixada_product.unit_measure_shop_id=aixada_unit_measure_shop.id";
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_product_category_list_all_query|
create procedure aixada_product_category_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter char(100))
begin
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = "select
      aixada_product_category.id,
      aixada_product_category.description 
    from aixada_product_category ";
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_product_orderable_for_date_list_all_query|
create procedure aixada_product_orderable_for_date_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter char(100))
begin
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = "select
      aixada_product_orderable_for_date.id,
      aixada_product.name as product,
      aixada_product_orderable_for_date.date_for_order 
    from aixada_product_orderable_for_date 
    left join aixada_product as aixada_product on aixada_product_orderable_for_date.product_id=aixada_product.id";
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_provider_list_all_query|
create procedure aixada_provider_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter char(100))
begin
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = "select
      aixada_provider.id,
      aixada_provider.name,
      aixada_provider.contact,
      aixada_provider.address,
      aixada_provider.nif,
      aixada_provider.zip,
      aixada_provider.city,
      aixada_provider.phone1,
      aixada_provider.phone2,
      aixada_provider.fax,
      aixada_provider.email,
      aixada_provider.web,
      aixada_provider.notes,
      aixada_provider.active,
      concat(aixada_uf.id, ' ', aixada_uf.name) as responsible_uf 
    from aixada_provider 
    left join aixada_uf as aixada_uf on aixada_provider.responsible_uf_id=aixada_uf.id";
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_providers_of_distributor_list_all_query|
create procedure aixada_providers_of_distributor_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter char(100))
begin
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = "select
      aixada_providers_of_distributor.id,
      aixada_distributor.name as distributor,
      aixada_provider.name as provider 
    from aixada_providers_of_distributor 
    left join aixada_distributor as aixada_distributor on aixada_providers_of_distributor.distributor_id=aixada_distributor.id
    left join aixada_provider as aixada_provider on aixada_providers_of_distributor.provider_id=aixada_provider.id";
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_rev_tax_type_list_all_query|
create procedure aixada_rev_tax_type_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter char(100))
begin
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = "select
      aixada_rev_tax_type.id,
      aixada_rev_tax_type.description,
      aixada_rev_tax_type.rev_tax_percent 
    from aixada_rev_tax_type ";
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_shop_item_list_all_query|
create procedure aixada_shop_item_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter char(100))
begin
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = "select
      aixada_shop_item.id,
      aixada_uf.name as uf,
      aixada_shop_item.date_for_shop,
      aixada_product.name as product,
      aixada_shop_item.quantity,
      aixada_shop_item.ts_validated,
      aixada_shop_item.operator_id 
    from aixada_shop_item 
    left join aixada_uf as aixada_uf on aixada_shop_item.uf_id=aixada_uf.id
    left join aixada_product as aixada_product on aixada_shop_item.product_id=aixada_product.id";
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_shopping_dates_list_all_query|
create procedure aixada_shopping_dates_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter char(100))
begin
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = "select
      aixada_shopping_dates.shopping_date,
      aixada_shopping_dates.available 
    from aixada_shopping_dates ";
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_stock_movement_list_all_query|
create procedure aixada_stock_movement_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter char(100))
begin
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = "select
      aixada_stock_movement.id,
      aixada_product.name as product,
      aixada_stock_movement.operator_id,
      aixada_stock_movement.amount_difference,
      aixada_stock_movement.description,
      aixada_stock_movement.resulting_amount,
      aixada_stock_movement.ts 
    from aixada_stock_movement 
    left join aixada_product as aixada_product on aixada_stock_movement.product_id=aixada_product.id";
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_uf_list_all_query|
create procedure aixada_uf_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter char(100))
begin
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = "select
      aixada_uf.id,
      aixada_uf.name,
      aixada_uf.active,
      aixada_uf.created,
      aixada_uf.mentor_uf 
    from aixada_uf ";
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_unit_measure_list_all_query|
create procedure aixada_unit_measure_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter char(100))
begin
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = "select
      aixada_unit_measure.id,
      aixada_unit_measure.unit 
    from aixada_unit_measure ";
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_user_list_all_query|
create procedure aixada_user_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter char(100))
begin
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = "select
      aixada_user.id,
      aixada_user.login,
      aixada_user.password,
      aixada_user.email,
      aixada_uf.name as uf,
      aixada_member.name as member,
      aixada_provider.name as provider,
      aixada_user.language,
      aixada_user.color_scheme_id,
      aixada_user.last_login_attempt,
      aixada_user.last_successful_login,
      aixada_user.created_on 
    from aixada_user 
    left join aixada_uf as aixada_uf on aixada_user.uf_id=aixada_uf.id
    left join aixada_member as aixada_member on aixada_user.member_id=aixada_member.id
    left join aixada_provider as aixada_provider on aixada_user.provider_id=aixada_provider.id";
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|

drop procedure if exists aixada_user_role_list_all_query|
create procedure aixada_user_role_list_all_query (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter char(100))
begin
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, ' order by active desc, ', the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = "select
      aixada_user_role.user_id,
      aixada_user_role.role 
    from aixada_user_role ";
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|


delimiter ;
