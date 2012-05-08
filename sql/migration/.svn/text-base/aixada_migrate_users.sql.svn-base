-- establish users

insert 
  into aixada.aixada_user (
    id, login, password, uf_id, member_id
  )
  select
    memid,
    memlogin,
    mempassword,
    memuf,
    memid
  from aixada_old.Membre
  where memuf <> 0;    

/*
insert into aixada.aixada_user (
    id, login, password, provider_id
  )
  select 
    1000 + provid,
    provnom,
    '4bJ093jdpP',
    provid
  from
    aixada_old.Proveidor
  where provid <> 53
    and provid <> 54;
*/
