delimiter |


/**
 * returns incidents for given list of ids
 */
drop procedure if exists get_incidents_by_ids|
create procedure get_incidents_by_ids(in the_ids text, in the_type int)
begin
	
	set @q = concat("select 
		i.*,
		mem.name as user_name,
	    mem.uf_id as uf_id,		
	    it.id as distribution_level,
	    it.description as type_description,
	    pv.id as provider_id,
	    pv.name as provider_name
	from
  		aixada_incident_type it,
  		aixada_user u,
  		aixada_member mem, 
 		aixada_incident i 
  	left join 
  		aixada_provider pv
	on 
		i.provider_concerned = pv.id
  	where
		i.id in (", the_ids,")
	  	and i.operator_id = u.id
	    and	u.member_id = mem.id
	    and i.incident_type_id >= ",the_type,"
	    and it.id = i.incident_type_id
	order by
		i.ts desc;");
	
	prepare st from @q;
  	execute st;
  	deallocate prepare st;
	
end|



/**
 * returns list of incident types.  
 */
drop procedure if exists get_incident_types|
create procedure get_incident_types()
begin
  select 
  	id, 
  	description, 
  	definition
  from 
  	aixada_incident_type;
end|



/**
 *  retrieves listing of incidents
 */
drop procedure if exists get_incidents_listing|
create procedure get_incidents_listing(in from_date timestamp, in to_date timestamp, in the_type int)
begin
	
	
	select 
		i.*, 
	    mem.name as user_name,
	    mem.uf_id as uf_id,		
	    it.id as distribution_level,
	    it.description as type_description,
	    pv.id as provider_id,
	    pv.name as provider_name
  	from 
  		aixada_incident_type it,
  		aixada_user u,
  		aixada_member mem, 
 		aixada_incident i 
  	left join 
  		aixada_provider pv
	on 
		i.provider_concerned = pv.id
  	where
	  	i.ts between from_date and to_date
	  	and i.operator_id = u.id
	    and	u.member_id = mem.id
	    and i.incident_type_id >= the_type
	    and it.id = i.incident_type_id
	    
  order by 
  	i.ts desc;
	
end |


delimiter ;
