insert into aixada_incident_type
   values        
      (1, 'internal', 'incidents are restricted to loggon in users.'),
      (2, 'internal + email', 'like 1 + incidents are send out as email if possible'),
      (3, 'internal + portal', 'like 1 + incidents are posted on the portal'),
      (4, 'internal + email + portal', 'Incidents are posted internally, send out as email and posted on the portal');