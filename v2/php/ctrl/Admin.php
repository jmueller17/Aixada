<?php

define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(__FILE__))).DS); 

require_once(__ROOT__ ."local_config/config.php");
require_once(__ROOT__ ."php/inc/database.php");
require_once(__ROOT__ ."php/utilities/general.php");
require_once(__ROOT__ ."php/lib/validation_cart_manager.php");


if (!isset($_SESSION)) {
    session_start();
 }

try{

	
  switch (get_param('oper')) {
	
	  	case 'backupDatabase':
	      $cv = configuration_vars::get_instance();
	      $filename = __ROOT__ . 'local_config/dbBkups/' . $cv->db_name . '.' . strftime('%Y.%m.%d', strtotime("now")) . '.sql';
	      //$filename = $cv->db_name . '.' . strftime('%Y.%m.%d', strtotime("now")) . '.sql';
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
	      echo 'local_config/dbBkups/' . $cv->db_name . '.' . strftime('%Y.%m.%d', strtotime("now")) . '.sql.bz2';
	      exit;
	      
	  default:  
    	throw new Exception("ctrlAdmin: oper={$_REQUEST['oper']} not supported");  
        break;
  }

 
} 

catch(Exception $e) {
  header('HTTP/1.0 401 ' . $e->getMessage());
  die($e->getMessage());
}  


?>