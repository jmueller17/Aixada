-- establish members
-- the user_id equals the old member id
    
insert 
  into aixada.aixada_member (
    id, uf_id, name, address, city, phone1, active, adult
  )
  select 
    aixada_old.Membre.memid,
    aixada_old.Membre.memuf,
    aixada_old.Membre.memnom,
    aixada_old.UnitatFamiliar.ufaddress,
    'Barcelona',
    aixada_old.Membre.memtel,
    aixada_old.Membre.memactive,
    not aixada_old.Membre.memtipus
  from
    aixada_old.Membre left join aixada_old.UnitatFamiliar
    on aixada_old.Membre.memuf = aixada_old.UnitatFamiliar.ufid
  where
    aixada_old.Membre.memuf <> 0;    

update aixada_member 
   set address = 'Verdi, 38, 2º 1ª', phone1='932180062' 
 where id in (89, 150);

update aixada_member 
   set name = 'Oriol Arechavala y Andraina Rockstroh' 
 where id = 114;

update aixada_member 
   set name = 'Oriol Arechavala y Andraina Rockstroh' 
 where id = 114;




