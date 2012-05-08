-- establish UFs

insert 
  into aixada.aixada_uf (
    id, name, active
  )
  select 
    ufid,
    ufname,
    ufactive
  from
    aixada_old.UnitatFamiliar
  where aixada_old.UnitatFamiliar.ufid <> 0;

