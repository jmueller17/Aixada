<?php
ini_set('display_errors', 0); // Needed for CheckDbAccess() using PHP < v8.1 where mysqli produces warnings 

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
$LOG_FILE = $LOG_FOLDER . 'InstallAixada.log';

$results = '';
try{
    switch (get_param('oper')) {
        case 'aixada_check':
            logInstall("--> Check install\n", true);
            if (!CheckDbAccess()) {
                logInstall("Pendig configure DB in 'local_config/config.php'");
                echo "Pendig configure DB" . 
                    "\nLogfile: " . $LOG_FILE;
                exit;
            }
            $response = 
                (CheckExistAndLogin() ? "Update Aixada" : "INSTALL a new Aixada") . 
                "\nLogfile: " . $LOG_FILE;
            logInstall("Is: '{$response}'");
            echo $response;
            exit;
        case 'sql_info':
            logInstall("--> Get Session sql information\n", true);
            $existAixada = CheckExistAndLogin();
            $php_datetime = new Datetime("now");
            $rs = get_row_query("SELECT concat(
                '\n version = \'' , @@version, '\'',
                '\n version_comment = \'' , @@version_comment, '\'\n',
                
                '\n SQL_MODE = \'' , @@SQL_MODE, '\'',
                '\n group_concat_max_len = ' , @@group_concat_max_len, '\n',
                
                '\n character_set_client = \'' , @@character_set_client, '\'',
                '\n character_set_results = \'' , @@character_set_results, '\'',
                '\n collation_connection = \'' , @@collation_connection, '\'\n',
                
                '\n current_timestamp = \'' , current_timestamp, '\'',
                '\n time_zone = \'' , @@time_zone, '\'',
                '\n PHP-datetime = \'{$php_datetime->format("Y-m-d H:i:s")}\'\n',
                
                '\n schema = \'' , DATABASE(), '\'',
                ''
            ) r;");
            $response = 
                ($existAixada? "Update Aixada" : "INSTALL a new Aixada") . "\n\n" .
                "Session sql information: \n" .
                $rs['r'];
            logInstall($response);
            echo $response;
            exit;
        case 'aixada_update':
            ini_set('max_execution_time', 300);
            logInstall("--> Start install\n\n", true);
            
            $existAixada = CheckExistAndLogin();
            
            // Start info.
            $results .= logInstall( 
                $existAixada ?
                'Aixada database update' :
                'Aixada database NEW INSTALL'
            );
            $mySqlVersion = get_row_query('SELECT @@version v;');
            $results .= logInstall(
                ' | Using PHP v' . PHP_VERSION . "\n" .
                $mySqlVersion['v'] .
                    ': host="' . get_config('db_host') . 
                    '" database="' . get_config('db_name') .
                    '" user="' . get_config('db_user') . "\"\n"
            );

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
                $results .= logInstall(
                    "\nCreate tables & insert initial data:"
                );
                $results .= logInstall(
                    execute_sql_files($db, 'sql/', array(
                        'aixada.sql',
                        'setup/aixada_insert_defaults.sql', 
                        'setup/aixada_insert_default_user.sql'
                    ))
                );
            } else {
                // Do it!
                $results .= logInstall(
                    "\nDone by: '" . get_session_login() . "' at " . date('Y-m-d H:i:s') . "\n"
                );
                
                // Do a previous backup.
                $results .= logInstall(
                    "\nA database backup has been created as:\n  "
                );
                $results .= logInstall(
                    backup_as_internal($LOG_FOLDER, get_backup_name()) . "\n"
                );
                    
                // Update tables.
                $mode = 'Update';
                $results .= logInstall("\nUpdate tables:");
                $results .= logInstall(
                    execute_sql_files($db, 'sql/', array('dbUpgradeTo2.8.sql'))
                );
            }
            
            // Crate or replace procedures.
            $results .= logInstall("\n{$mode} database procedures:");
            $results .= logInstall(
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
                ))
            );
            
            // Inform the new username and password.
            if (!$existAixada) {
                $results .= logInstall(
                    "\nLoging as: user=\"admin\" password=\"admin\"\n"
                );
            }
            
            // Finished correctly.
            $results .= logInstall(
                "\n** {$mode} database process has finished correctly **"
            );
            echo $results;
            
            logInstall('--> Finished correctly!', true);
            exit;
        default:  
            throw new Exception("ctrlAdmin: oper={$_REQUEST['oper']} not supported");  
            break;
    }
} catch(Exception $e) {
    header('HTTP/1.0 401 Install error');
    logInstall("ERROR:\n" . $e->getMessage(), true);
    ob_clean();
    die($results . "\n...aborted!\n\n" . $e->getMessage());
}

function logInstall($text, $showTime = false) {
    global $LOG_FILE;
    try{
        file_put_contents(
            __ROOT__ . $LOG_FILE, 
            ( $showTime ? 
            (
            "\n\n- - - - - - - - - - - - - - - - - - - - - - - - - - - - - -" .
            "\n- - - - - - - - - - " . date('Y-m-d H:i:s') . " - - - - - - - - - -\n") :
            "" ) .
            $text,
            FILE_APPEND
        );
        return $text;
    } catch(Exception $e) {
    }
}

function CheckDbAccess() {
    $response = 0;
    try {
        $response = get_row_query("SELECT 1 from dual");
    } catch (Exception $e) {
    }
    return $response;
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
