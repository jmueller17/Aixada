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

$LOG_FOLDER = 'local_config/dbBkups/';

try{
    switch (get_param('oper')) {
        case 'aixada_check':
            logInstall('--> Check install');
            
            $response = (CheckExistAndLogin() ? "Update Aixada" : "INSTALL: A new Aixada");
            logInstall("Is: '{$response}'");
            echo $response;
            exit;
        case 'aixada_update':
            logInstall('--> Start install');
            
            $existAixada = CheckExistAndLogin();
            
            // Start info.
            $results = 
                ($existAixada ?
                    'Aixada database update' :
                    'Aixada database NEW INSTALL') .
                    ' | Using PHP v' . PHP_VERSION . "\n" .
                'MySQL: host="' . get_config('db_host') . 
                    '" database="' . get_config('db_name') .
                    '" user="' . get_config('db_user') . "\"\n";

            // Do it.
            $db = connect_by_mysqli(     
                get_config('db_host'),
                get_config('db_name'),
                get_config('db_user'),
                get_config('db_password')
            );
            if (!$existAixada) {
                // Create tables
                $mode = 'Create';
                $results .= "\nCreate tables & insert initial data:";
                $results .= execute_sql_files($db, 'sql/', array(
                    'aixada.sql',
                    'setup/aixada_insert_defaults.sql', 
                    'setup/aixada_insert_default_user.sql'
                ));
            } else {
                // Do it!
                $results .= "\n\nDone by: '" . get_session_login() . "' at " . date('Y-m-d H:i:s') . "\n\n";
                
                // Do a previous backup.
                $results .= "\nA database backup has been created as:\n  "  .
                    backup_as_internal($LOG_FOLDER, get_backup_name()) . "\n";
                    
                // Update tables.
                $mode = 'Update';
                $results .= "\nUpdate tables:";
                $results .= execute_sql_files($db, 'sql/', array(
                    'dbUpgradeTo2.8.sql'
                ));
            }
            
            // Crate or replace procedures.
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
            
            // Inform the new username and password.
            if (!$existAixada) {
                $results .= "\nLoging as: user=\"admin\" password=\"admin\"\n";
            }
            
            // Finished correctly.
            $results .= "\n** {$mode} database process has finished correctly **";
            logInstall($results);
            echo $results;
            exit;
        default:  
            throw new Exception("ctrlAdmin: oper={$_REQUEST['oper']} not supported");  
            break;
    }
} catch(Exception $e) {
    header('HTTP/1.0 401 Install error');
    logInstall("ERROR:\n" . $e->getMessage());
    die($e->getMessage());
}

function logInstall($text) {
    global $LOG_FOLDER;
    try{
        file_put_contents(
            __ROOT__ . $LOG_FOLDER . 'InstallAixada.log', 
            "\n\n- - - - - - - - - - - - - - - - - - - - - - - - - - - - - -" .
            "\n- - - - - - - - - - " . date('Y-m-d H:i:s') . " - - - - - - - - - -\n" .
            $text,
            FILE_APPEND
        );
    } catch(Exception $e) {
    }
}

function CheckExistAndLogin() {
    $existAixada = get_row_query("SELECT table_name FROM information_schema.tables where table_schema=DATABASE() and table_name='aixada_uf'");
    if ($existAixada) {
        // Logged options
        if (!is_created_session()) {
            throw new Exception("Must identify as a user before starting an database update!
            <br><a target=\"_blank\" href=\"login.php?\">login</a>");
        }
        $roles = get_session_value('roles');
        if (!in_array('Hacker Commission', $roles)) {
            throw new Exception("Only a user with role \"Hacker Commission\" can do an database update!
            <br><a target=\"_blank\" href=\"login.php?\">login</a>");
        }
        // Is updatable?
        $isUpdatable = !!get_row_query(
            "SELECT table_name FROM information_schema.tables where table_schema=DATABASE() and table_name='aixada_price'"
        );
        if (!$isUpdatable) {
            throw new Exception("The version of Aixada is previous to 2.7!\n" .
                "It must be updated manually!!\n\n" .
                "(see 'sql/dbUpgradeTo2.6.2.sql' and also previous versions)"
            );
        }
    }
    return $existAixada;
}
