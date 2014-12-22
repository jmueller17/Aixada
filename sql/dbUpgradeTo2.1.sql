source migration/aixada_migrate_uf.sql;
source migration/aixada_migrate_member.sql;
source migration/aixada_migrate_provider.sql;
source migration/aixada_migrate_users.sql;
source migration/aixada_migrate_role.sql;
source migration/aixada_migrate_product.sql;
source migration/aixada_migrate_favorite.sql;
source migration/aixada_migrate_orders.sql;
source migration/aixada_migrate_incident.sql;
source migration/aixada_migrate_account.sql;


-- load queries AGAIN after migration 
-- so that optimizer can take statistics into account

source aixada_setup_queries.sql;

