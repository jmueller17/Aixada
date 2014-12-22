

/**
 *	Types of incidents 
 **/
create table aixada_incident_type (
  id        		tinyint		not null auto_increment,
  description 		varchar(255)   	not null,
  definition 		text 		not null,
  primary key (id)
) engine=InnoDB default character set utf8 collate utf8_general_ci;




/**
 *	Incidents
 **/
create table aixada_incident (
  id 	              	int             not null auto_increment,
  subject               varchar(255)    not null,
  incident_type_id   	tinyint         not null,
  operator_id  			int             not null,
  details	      		text,
  priority              int             default 3,
  ufs_concerned         int,
  commission_concerned  int,
  provider_concerned    int,
  ts					timestamp 	default current_timestamp,  
  status                varchar(10)     default 'Open',
  primary key (id),
  foreign key (incident_type_id) references aixada_incident_type(id),
  foreign key (operator_id) references aixada_user(id),
  key (ts)
) engine=InnoDB default character set utf8 collate utf8_general_ci;
