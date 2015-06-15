<?php
define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(__FILE__))).DS); 

require_once(__ROOT__ . "local_config/config.php");
require_once(__ROOT__ . "php/inc/database.php");
require_once(__ROOT__ . "php/utilities/general.php");

if (!isset($_SESSION)) {
    session_start();
}
$table_name = get_param('table');
$oper = get_param('oper');
if ($oper) {
    if (check_manager_exist($table_name)) {
        include_once(__ROOT__."php/ctrl/ManageData/{$table_name}.php");
        $data_manager = new data_manager();
        try {
            switch ($oper) {
            case 'edit':
                echo $data_manager->update($_REQUEST);
                exit;
            case 'add':
                echo $data_manager->insert($_REQUEST);
                exit;
            case 'del':
                echo $data_manager->delete($_REQUEST);
                exit;
            case 'listAll':
                printXML($data_manager->select($_REQUEST));
                exit;
            default:  
                throw new Exception("ctrl/ManageData: oper={$oper} not supported");  
                break;
            }
        } catch (Exception $e) {
            header('HTTP/1.0 401 ' . $e->getMessage());
            die ($e->getMessage());
        }
    } else {
        throw new Exception("ctrl/ManageData: table={$table_name} not defined");  
                break;
    }
}

function check_manager_exist($table_name) {
    if ($table_name && strpos($table_name, '/') === false && strpos($table_name, "\\") === false) {
        return file_exists(__ROOT__."php/ctrl/ManageData/{$table_name}.php");
    }
    return false;
}
?>
