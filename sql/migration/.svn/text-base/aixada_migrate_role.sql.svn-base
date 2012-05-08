-- assign roles

insert 
  into aixada.aixada_user_role (
    user_id, role
  )
  select
    id,
    'Consumer'
  from aixada_member;

insert 
  into aixada.aixada_user_role (
    user_id, role
  )
  select
    id,
    'Checkout'
  from aixada_member;

/*
insert 
  into aixada.aixada_user_role (
    user_id, role
  )
  select
    1000 + id,
    'Provider'
  from aixada_provider;
*/

insert into aixada.aixada_user_role values
       (4,   'Hacker Commission'),
       (87,  'Hacker Commission'),
       (112, 'Hacker Commission'),
       (113, 'Hacker Commission');
       
