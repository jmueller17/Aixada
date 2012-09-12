delimiter |

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
 * creates new incidents, edits existing one if id is given. 
 */
drop procedure if exists manage_incident|
create procedure manage_incident(in the_id int,
                              	 in the_subject varchar(100),
								 in the_type tinyint,
                              	 in the_operator int,
                             	 in the_details text,
                              	 in the_priority int,
                              	 in the_ufs_concerned varchar(100),
                              	 in the_comm varchar(100),
                              	 in the_prov varchar(100),
                              	 in the_status varchar(10))
begin
	
	if (the_id > 0) then
	 	update 
	 		aixada_incident 
	 	set
     		incident_type_id = the_type, 
     		priority = the_priority,
     		subject = the_subject,
     		operator_id = the_operator,
     		details = the_details,
     		ufs_concerned = the_ufs_concerned,
     		commission_concerned = the_comm,
     		provider_concerned = the_prov,
     		status = the_status
  		where
     		id = the_id;
	else 

		insert into 
			aixada_incident (incident_type_id, priority, subject, operator_id, details, ufs_concerned, commission_concerned, provider_concerned, status) 
     	values 
     		(the_type, the_priority, the_subject, the_operator, the_details, the_ufs_concerned, the_comm, the_prov, the_status);
	
	end if; 
	

end |


/**
 * deletes an incident without remorse
 */
drop procedure if exists delete_incident|
create procedure delete_incident(in the_id int)
begin
  delete from aixada_incident
  where id = the_id;
end|


/**
 *  retrieves listing of incidents
 */
drop procedure if exists get_incidents_listing|
create procedure get_incidents_listing(in from_date date, in to_date date, in the_limit int)
begin
	
	
	select 
	    i.id,
	    t.id as type,
	    t.description as type_description,
	    i.subject,
	    i.details,
	    i.priority,
	    i.ufs_concerned,
	    i.commission_concerned,
	    p.id as provider,
	    p.name as provider_name,
	    m.name as user,
	    m.uf_id as uf,
	    i.ts as date_posted,
	    i.status
  	from 
  		aixada_incident i, 
  		aixada_incident_type t,
  		aixada_user u,
  		aixada_member m,
  		aixada_provider p
  	where
	  	i.ts >= from_date and i.ts <= to_date
	  	and i.incident_type_id = t.id
	 	and i.operator_id = u.id
	    and	u.member_id = m.id
	   	and i.provider_concerned = p.id
  order by 
  	i.ts desc;
	
end |


delimiter ;
