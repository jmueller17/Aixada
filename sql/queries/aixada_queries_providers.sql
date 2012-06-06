delimiter |


/**
 * returns all providers that have "sometimes orderable" or "always orderable"
 * items not stock. 
 * independent if products have been ordered or not. 
 */
drop procedure if exists get_orderable_providers|
create procedure get_orderable_providers()
begin
  select distinct 
     pv.id, 
     pv.name
  from 
  	aixada_provider pv, 
  	aixada_product p
  where  
    pv.active = 1
    and pv.id = p.provider_id
    and p.orderable_type_id in (2,3)
  order by pv.name;
end|


/**
 * returns all providers that have orderable products for a given date
 */
drop procedure if exists get_orderable_providers_for_date|
create procedure get_orderable_providers_for_date(in the_date date)
begin
  select distinct 
     pv.id, 
     pv.name
  from 
  	aixada_provider pv, 
  	aixada_product p,
  	aixada_product_orderable_for_date po
  where  
    pv.active = 1
    and pv.id = p.provider_id
    and p.id = po.product_id
    and po.date_for_order = the_date
  order by pv.name;
end|



/**
 * returns list of providers that either have stock items or 
 * had orderable products for the given date
 */
drop procedure if exists get_shop_providers_for_date|
create procedure get_shop_providers_for_date(in the_date date)
begin
  (select distinct 
     pv.id, 
     pv.name as name
  from 
  	aixada_provider pv, 
  	aixada_product p,
  	aixada_product_orderable_for_date po
  where  
    po.date_for_order = the_date
    and po.product_id = p.id
    and p.provider_id = pv.id)
  union
  (select distinct
  	pv.id,
  	pv.name as name
  from 
  	aixada_provider pv,
 	aixada_product p
  where
  	pv.active = 1
  	and pv.id = p.provider_id
  	and p.orderable_type_id = 1)
  order by name;
end|


/**
 * returns list of providers with stock only products
 */
drop procedure if exists get_stock_providers|
create procedure get_stock_providers()
begin
  select distinct
	 pv.id, 
     pv.name
  from 
  	aixada_provider pv, 
  	aixada_product p
  where  
    pv.active = 1
    and pv.id = p.provider_id
    and p.orderable_type_id = 1
  order by pv.name;
end|


/**
 *	returns a list of all active providers
 *	with sometimes, always orderable items, stock, preorder
 */
drop procedure if exists get_all_active_providers|
create procedure get_all_active_providers()
begin
  select distinct 
     pv.id, 
     pv.name
  from 
  	aixada_provider pv
  where  
    pv.active = 1
  order by pv.name;
end|




/*************************************************
 * 
 * procedures for categories
 * 
 *************************************************/


/**
 * returns all categories that have orderable products 
 * for a given date 
 */
drop procedure if exists get_orderable_categories_for_date|
create procedure get_orderable_categories_for_date(in the_date date)
begin
  select distinct 
     pc.id, 
     pc.description
  from 
  	aixada_provider pv, 
  	aixada_product p,
  	aixada_product_orderable_for_date po,
  	aixada_product_category pc
  where  
    pv.active = 1
    and p.active = 1
    and p.category_id = pc.id
    and po.date_for_order = the_date
    and p.id = po.product_id
    and p.provider_id = pv.id
  order by pc.description;
end|


/**
 * returns all categories that have orderable and shop products 
 * for a given date
 */
drop procedure if exists get_shop_categories_for_date|
create procedure get_shop_categories_for_date(in the_date date)
begin
  (select distinct 
     pc.id, 
     pc.description as description
  from 
  	aixada_provider pv, 
  	aixada_product p,
  	aixada_product_orderable_for_date po,
  	aixada_product_category pc
  where  
    po.date_for_order = the_date
    and p.category_id = pc.id
    and po.product_id = p.id
    and p.provider_id = pv.id)
  union
  (select distinct
  	pc.id,
  	pc.description as description
  from 
  	aixada_provider pv,
 	aixada_product p,
 	aixada_product_category pc
  where
  	pv.active = 1
  	and p.active = 1
  	and p.category_id = pc.id
  	and pv.id = p.provider_id
  	and p.orderable_type_id = 1)
  order by description;
end|





delimiter ;