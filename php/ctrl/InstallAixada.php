<?php

define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(__FILE__))).DS); 

if (!is_file('../../local_config/config.php')) {
    header('HTTP/1.0 401 No Aixada config!');
    die("Configuration file 'local_config/config.php' does not exist!");
}

require_once __ROOT__ . "local_config/config.php";
require_once __ROOT__ . "php/inc/adminDatabase.php";
require_once __ROOT__ . "php/utilities/general.php";

// Logged options
try{
    if (!isset($_SESSION)) {
        session_start();
    }
	
    switch (get_param('oper')) {
        case 'aixada_update':
            $cv = configuration_vars::get_instance();
            $db = connect_by_mysqli(     
                get_config('db_host'),
                get_config('db_name'),
                get_config('db_user'),
                get_config('db_password')
            );
            $existAixada = get_row_query("SELECT table_name FROM information_schema.tables where table_schema=DATABASE() and table_name='aixada_uf'");
            $results = 
                'Aixada database install | Using PHP v' . PHP_VERSION . "\n" .
                'MySQL: host="' . get_config('db_host') . 
                    '" database="' . get_config('db_name') .
                    '" user="' . get_config('db_user') . "\"\n" .
                'Character_set="' .
                    get_row_query("SELECT *
                        FROM performance_schema.session_variables
                        WHERE VARIABLE_NAME = 'character_set_results'")['VARIABLE_VALUE'] .
                    '" collation="' .
                    get_row_query("SELECT *
                        FROM performance_schema.session_variables
                        WHERE VARIABLE_NAME = 'collation_connection'")['VARIABLE_VALUE'] .
                    "\"\n";
            if (!$existAixada) {
                $mode = 'Create';
                $results .= "\nCreate tables & insert initial data:";
                $results .= execute_sql_files($db, 'sql/', array(
                    'aixada.sql',
                    'setup/aixada_insert_defaults.sql', 
                    'setup/aixada_insert_default_user.sql'
                ));
            } else {
                $results .= "\nA previous database backup has been created as:\n  " .
                    backup_as_internal('local_config/dbBkups/', get_backup_name()) . "\n";
                
                $mode = 'Update';
                $results .= "\nUpdate tables:";
                $results .= execute_sql_files($db, 'sql/', array(
                    'dbUpgradeTo2.8.sql'
                ));
            }
            $results .= "\n{$mode} database procedures:" .
                execute_sql_files($db, 'sql/', array(
                'queries/aixada_queries_account.sql',
                'queries/aixada_queries_dates.sql',
                'queries/aixada_queries_useruf.sql',
                'queries/aixada_queries_incidents.sql',
                'queries/aixada_queries_order.sql',
                'queries/aixada_queries_products.sql',
                'queries/aixada_queries_providers.sql',
                'queries/aixada_queries_cart.sql',
                'queries/aixada_queries_report.sql',
                'queries/aixada_queries_shop.sql',
                'queries/aixada_queries_statistics.sql',
                'queries/aixada_queries_validate.sql',
                'queries/canned_queries.sql'
            ));
            if (!$existAixada) {
                $results .= "\nLoging as: user=\"admin\" password=\"admin\"\n";
            }
            echo $results . "\n** {$mode} database process has finished correctly **";
            exit;
        default:  
            throw new Exception("ctrlAdmin: oper={$_REQUEST['oper']} not supported");  
            break;
    }
} catch(Exception $e) {
    header('HTTP/1.0 401 ' . 
        str_replace(array("\n", "\r"), array(" ", " "), $e->getMessage()));
    die($e->getMessage());
}
