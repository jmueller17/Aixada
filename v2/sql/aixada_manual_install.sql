/**
 * This script should be called from your mysql terminal inside the 
 * Aixada/sql directory. It will create the database structure, 
 * insert some default values including an admin user and 
 * setup all the stored procedures for you. 
 * 
 * The default admin user/pwd is: admin/admin. Be sure to change
 * the admin password upon first logon!!  
 */
-- create db tables --
source aixada.sql;

-- insert default values--
source setup/aixada_insert_defaults.sql;

-- create admin --
source setup/aixada_insert_default_user.sql;

-- source the stored procedures -- 
source setup/aixada_setup_queries.sql;