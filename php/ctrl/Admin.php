<?php

define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(__FILE__))).DS); 

require_once __ROOT__ . "local_config/config.php";
require_once __ROOT__ . "php/inc/adminDatabase.php";
require_once __ROOT__ . "php/utilities/general.php";

try{
  validate_session(); // The user must be logged in.
	
  ini_set('max_execution_time', 300);
  switch (get_param('oper')) {
	
	  	case 'backupDatabase':
	      $cv = configuration_vars::get_instance();
	      $backup_name = get_backup_name();
	      $filename = __ROOT__ . 'local_config/dbBkups/' . $backup_name . '.sql';
	      $output = array();
	      $retval = 0;
	      $cmds = array('rm -f ' . $filename . '.bz2',
	      				//if the path to the mysqldump is not included in the PHP environment variables a error code 127 is thrown!! 
	      				//of course the path could be different on each machine?!!
	      				//'/opt/lampp/bin/mysqldump --default-character-set=utf8'
	                    'mysqldump --default-character-set=utf8'
	                    . ' -u '. $cv->db_user
	                    . ' -p' . $cv->db_password 
	                    . ' --host=' . $cv->db_host
	                    . ' ' . $cv->db_name 
	                    . ' > ' . $filename,
	                    'bzip2 ' . $filename);
          $error_cmd = '';
          try{
            foreach ($cmds as $cmd) {
              exec($cmd . ' 2>&1', $output, $retval);
              if ($retval) {
                  $errstr = $retval . ' ';
                  foreach ($output as $out) {
                      $errstr .= $out . ';';
                  }
                  throw new InternalException('Could not execute "' . $cmd 
                                              . '". Error message: ' . $errstr
                                            );
              }
            }
          } catch(Exception $e) {
              // throw new Exception($e->getMessage());
              $error_cmd = $e->getMessage();
          }
          if ($error_cmd === '') {
            echo 'local_config/dbBkups/'.$backup_name.'.sql.bz2';
              
          } else {
            echo backup_as_internal('local_config/dbBkups/', $backup_name);
          }
          exit;
	  case 'backupDatabase_int':
            echo backup_as_internal('local_config/dbBkups/', get_backup_name());
            exit;    
	  default:  
    	throw new Exception("ctrlAdmin: oper={$_REQUEST['oper']} not supported");  
        break;
  }

 
} 

catch(Exception $e) {
    header('HTTP/1.0 401 ' .
        str_replace(array("\n", "\r"), array(" ", " "), $e->getMessage()));
    die($e->getMessage());
}
