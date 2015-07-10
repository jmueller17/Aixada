delimiter |



/**
 *	deactivates or actives a provider. basically means that deactivated providers
 *  does not appear anymore in the listings. This does NOT deactivate its products. 
 */
drop procedure if exists change_active_status_provider|
create procedure change_active_status_provider (in the_active_state boolean, in the_provider_id int)
begin

	update 
		aixada_provider
	set 
		active = the_active_state
	where 
		id = the_provider_id;

end|



/**
 * returns the responsible users for a provider. 
 */
drop procedure if exists get_responsible_uf|
create procedure get_responsible_uf(in the_provider_id int)
begin
	
	select 
		u.*
	from 
		aixada_user u,
		aixada_provider pv
	where
		pv.id = the_provider_id
		and pv.responsible_uf_id = u.uf_id;
end|


/**
 * returns all providers that have "sometimes orderable" or "always orderable"
 * items not stock. 
 * independent if products have been ordered or not. 
 */
drop procedure if exists get_orderable_providers|
create procedure get_orderable_providers(in ge_orderable_type_id tinyint)
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
    and p.orderable_type_id >= ge_orderable_type_id
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
 * returns list of providers that are active and that
 * have at least one active product. This is a list of providers
 * from which potentially something can be bought. 
 */
drop procedure if exists get_shop_providers|
create procedure get_shop_providers()
begin	
	  select
	  	pv.id,
	  	pv.name as name
	  from 
	  	aixada_provider pv,
	 	aixada_product p
	  where
	  	pv.active = 1
	  	and p.active = 1
	  	and p.provider_id = pv.id
	  group by 
	  	pv.name
	  order by 
	  	pv.name;
end|


/**
 * returns list of providers with stock only products, 
 * or cumulative order
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
    and p.active = 1
    and (p.orderable_type_id = 1 or p.orderable_type_id = 4) 
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


/**
 *	returns a list of all active providers
 *	with sometimes, always orderable items, stock, preorder
 */
drop procedure if exists get_provider_listing|
create procedure get_provider_listing(in the_provider_id int, in include_inactive boolean)
begin
	
	declare wherec varchar(255) default '';
	
	-- show all providers including inactive or just active ones -- 
	set wherec = if(include_inactive=1,"","and pv.active = 1");	
	
	-- filter for specific provider --
	if the_provider_id > 0 then
		set wherec = concat(wherec, " and pv.id=",the_provider_id);
	end if; 
		
	set @q = concat("select
	     pv.*, 
		 uf.id as responsible_uf_id, 
		 uf.name as responsible_uf_name
	  from 
	  	aixada_provider pv,
	  	aixada_uf uf
	  where  
	  	pv.responsible_uf_id = uf.id
	    ",wherec,"
	  order by pv.name, pv.id;");
	  
	  prepare st from @q;
	  execute st;
	  deallocate prepare st;
	  
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
 * for a given date if date is > 0. Otherwise returns all categories
 * for all active provider/Products. 
 */
drop procedure if exists get_shop_categories_for_date|
create procedure get_shop_categories_for_date(in the_date date)
begin
	
  if (the_date > 0) then
	
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
	  
  else 
  	 
  	  select
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
	  group by
	  	description
	  order by 
	    description;
	    
  end if; 
  
end|



delimiter ;
