delimiter |


/**
 * returns all orderable dates, irrespective if they have ordered items or not. 
 *
 */

drop procedure if exists get_orderable_dates|
create procedure get_orderable_dates(in from_date date, in the_limit int)
begin
	declare from_date_onward date default from_date;
	
  	if from_date = 0 then 
  		set from_date_onward = date(sysdate()); 
  	end if;
		
	select distinct
		po.date_for_order
	from 
		aixada_product_orderable_for_date po
	where
		po.date_for_order > from_date_onward
	limit the_limit;
		
end|



delimiter ;