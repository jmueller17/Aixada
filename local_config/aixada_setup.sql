
drop database aixada;
create database aixada;
use aixada;

source ../sql/aixada.sql;
source ../sql/setup/aixada_setup_details.sql;
source ../sql/aixada_setup_queries.sql;
/*
 * load queries after migration 
 * so that optimizer can take statistics into account
 */
