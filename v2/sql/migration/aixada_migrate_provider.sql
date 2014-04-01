-- establish providers
-- the user id gets assigned automatically

insert
  into aixada.aixada_provider (
    id, name, phone1, fax, notes, active
  ) 
  select 
    provid,
    provnom,
    provtelefon,
    provfax,
    provextrainfo,
    provactive
  from
    aixada_old.Proveidor
  where provid <> 53
    and provid <> 54;

