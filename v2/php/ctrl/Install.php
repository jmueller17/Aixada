<?php

define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(__FILE__))).DS); 
require_once(__ROOT__ . 'local_config/config.php');
require_once(__ROOT__ . 'php'.DS.'utilities'.DS.'general.php');


$config_dir = __ROOT__ . 'local_config/';

function process_language_file($coop_name, $lang_file)
{
    $tmpname = $lang_file . '.tmp';
    copy($lang_file, $tmpname);
    $inhandle = @fopen($tmpname, 'r');
    if (!$inhandle) {
        throw new Exception("Couldn't open {$tmpname} for reading. Make sure that {$lang_file} and all directories above it can be universally written!");
    }
    $outhandle = @fopen($lang_file, 'w');

    if ($outhandle==FALSE) {
        throw new Exception("Couldn't open {$lang_file} for writing. Make sure that {$lang_file} has 'www-data' as owner.");
    }
    while(!feof($inhandle)) {
        $buffer = fgets($inhandle, 4096);
        if (strpos($buffer, 'Aixada') !== false) {
            $buffer = str_replace('Aixada', $coop_name, $buffer);
            $buffer = str_replace('The New ', '', $buffer);
        }
        fwrite($outhandle, $buffer);
    }
    unlink($tmpname);
    return 0;
}

function process_languages($coop_name)
{
    global $config_dir;
    $lang_files = glob($config_dir . "lang/*.php");
    if (!is_array($lang_files))
	throw new Exception("Couldn't find language files in {$lang_files}");
    foreach ($lang_files as $lang_file) {
        process_language_file($coop_name, $lang_file);
    }
}

function create_setup_file($db_name)
{

    global $config_dir;
    $aixada_filename = $config_dir . 'aixada_setup.sql';
    $db_filename = $config_dir . "{$db_name}_setup.sql";
    $inhandle =  @fopen($aixada_filename, 'r');
    if (!$inhandle) {
        throw new Exception("Couldn't open {$aixada_filename} for reading");
    }
    $outhandle = @fopen($db_filename, 'w');
    if (!$outhandle) {
        throw new Exception("Couldn't open {$db_filename} for writing.");
    }
    while (!feof($inhandle)) {
        $buffer = fgets($inhandle);
        if (strpos($buffer, 'drop database') !== false or
            strpos($buffer, 'create database') !== false or
            strpos($buffer, 'use ') !== false) {
            $buffer = str_replace('aixada', $db_name, $buffer);
        } 
	//        if (strpos($buffer, 'source') !== false)
	//            $buffer = str_replace('source ', 'source sql/', $buffer);
        fwrite($outhandle, $buffer);
    }
    return 0;
}

function clear_results($mysqli)
{
      while ($mysqli->more_results()) {
          $mysqli->next_result();
          $rs = $mysqli->use_result();
          if ($rs instanceof mysqli_result)
              $rs->free();
      }
}

function create_table($handle, $mysqli)
{
    $cmd = '';
    while(!feof($handle)) {
        $buffer = fgets($handle, 100000);
        if (strpos($buffer, 'source') === false and
            strpos($buffer, '*') === false and
            strpos($buffer, '--') === false)
            $cmd .= $buffer;
    }
    $cmd_array = explode(';', $cmd);
    foreach($cmd_array as $cmd) {
        $rs = $mysqli->query($cmd.';');
    }
 }

function create_tables($db_name, $mysqli)
{
    global $config_dir;
    $files = array( $config_dir . "{$db_name}_setup.sql",
                    $config_dir . '../sql/aixada.sql',
                    $config_dir . '../sql/setup/aixada_setup_details.sql');
    $dump_files = array();
    $dump_dir = 'sql/dumps';
    if (file_exists($dump_dir) && is_dir($dump_dir)) { 
        foreach (glob($dump_dir . '/*') as $dump_file) {
            $files[] = $dump_file;
            $dump_files[] = $dump_file;
        }
    }
    foreach ($files as $filename) {
        $handle = @fopen($filename, 'r');
        if (!$handle) {
            throw new Exception("Couldn't open {$filename} for reading");
	}
        if (in_array($filename, $dump_files)) {
            $mysqli->query('call delete_member(-1);');
            clear_results($mysqli);
        }
        create_table($handle, $mysqli);
        fclose($handle);
    }
    return 0;
}

function create_queries($mysqli)
{
    global $config_dir;
    $querydir = $config_dir . '../sql/queries/';
    $direntries = scandir($querydir);
    if (!is_array($direntries))
	throw new Exception("Couldn't find the directory '{$querydir}', or it is empty");
    foreach ($direntries as $filename) {
        $handle = @fopen($querydir . $filename, 'r');
        if (!$handle) {
            throw new Exception("Couldn't open {$filename}");
	}
        $cmd = '';
        while(!feof($handle)) {// and strpos($buffer, 'end|') === false) {
            $buffer = fgets($handle, 4096);
            if (strpos($buffer, 'delimiter') === false)
                $cmd .= $buffer;
        }
        $mysqli->multi_query(str_replace('|', ';', $cmd));
        clear_results($mysqli);
    }
    return 0;
}

function create_database($db_name, $mysqli)
{
    return create_tables($db_name, $mysqli);
}

function create_database_queries($db_name, $mysqli)
{
    return create_queries($mysqli);
}

function create_config_file($host, $user, $password, $db_name, $language)
{
    global $config_dir;
    $filename = $config_dir . 'config.php';
    $tmpname = $filename . '.tmp';
    copy($filename, $tmpname);
    $inhandle = @fopen($tmpname, 'r');
    if (!$inhandle)
        throw new Exception("Couldn't open {$tmpname} for reading");
    $outhandle = @fopen($filename, 'w');
    if (!$outhandle)
        throw new Exception("Couldn't open {$filename} for writing. Make sure that it is universally writable, and has 'www-data' as owner.");
    while (!feof($inhandle)) {
        $buffer = fgets($inhandle, 4096);
        if (strpos($buffer, 'db_host') !== false)
	  $buffer = "  public \$db_host = '$host';\n";
        if (strpos($buffer, 'db_name') !== false) {
	  $buffer = "  public \$db_name = '$db_name';\n";
	}
        if (strpos($buffer, 'db_user') !== false)
	  $buffer = "  public \$db_user = '$user';\n";
        if (strpos($buffer, 'db_password') !== false)
	  $buffer = "  public \$db_password = '$password';\n";
        if (strpos($buffer, 'default_language') !== false)
	  $buffer = "  public \$default_language = '$language';\n";
        fwrite($outhandle, $buffer);
    }
    unlink($tmpname);
}

try {
    $host = configuration_vars::get_instance()->db_host;
    $user = configuration_vars::get_instance()->db_user;
    $password = configuration_vars::get_instance()->db_password;
    $db_name = configuration_vars::get_instance()->db_name;
    $pref_lang = configuration_vars::get_instance()->default_language;
    $first_uf = get_param('first_uf');
    $user_login = get_param('user_login');
    //this has to be changed to the real encryption... see authentication.inc.php
    //once we'll work again on the installer
    //$user_password = crypt(get_param('user_password'), 'ax');
    //$retype_password = crypt(get_param('retype_password'), 'ax');
    if ($user_password != $retype_password)
	throw new Exception("The two instances of the password for the first user don't match");
    $oper = get_param('oper');
    if ($oper=='validate') return 0;

    $mysqli = new mysqli($host, $user, $password, $db_name);
    if (mysqli_connect_errno()) 
	throw new Exception('Unable to connect to database. ' . mysqli_connect_error());
    if ($oper == 'connect') return 0;

    switch ($oper) {
    case 'create_database':
	return create_database($db_name, $mysqli);

    case 'create_database_queries':
	return create_database_queries($db_name, $mysqli);

    case 'create_user':
	return $mysqli->query("call register_special_user('{$user_login}', '{$user_password}', '{$pref_lang}', '{$first_uf}');");
 
    default:
	throw new Exception ('Action "' . $action . '" not recognized');
    }
}

catch(Exception $e) {
    echo $e->getMessage();
    header('HTTP/1.0 401 ' . $e->getMessage());
}  
?>