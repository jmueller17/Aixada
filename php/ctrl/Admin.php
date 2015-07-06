<?php

define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(__FILE__))).DS); 

require_once(__ROOT__ ."local_config/config.php");
require_once(__ROOT__ ."php/inc/database.php");
require_once(__ROOT__ ."php/utilities/general.php");


if (!isset($_SESSION)) {
    session_start();
 }

try{

	
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
  header('HTTP/1.0 401 ' . $e->getMessage());
  die($e->getMessage());
}

// Utilities
function get_backup_name() {
    return get_config('db_name').'.'
        .strftime('%Y.%m.%d_%H%M', strtotime("now"));
}
// Do a db-Aixada backup using internal method.
function backup_as_internal($output_folder, $backup_name) {
    // All warning is an error!
    set_error_handler(
        function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
            throw new Exception($err_msg);
        }
    );
    try{
        $cv = configuration_vars::get_instance();
        
        return $output_folder.backup_by_mysqli(
            $output_folder, $backup_name,        
            $cv->db_host, $cv->db_name, $cv->db_user, $cv->db_password
        );
    } catch(Exception $e) {
        throw new Exception($e->getMessage());  
    }
    restore_error_handler();
}

/**
 * Do a DB backup using a internal instance of mysqli.
 * Initial points of inspiration: 
 *  * backup using mysqli: 
 *      http://davidwalsh.name/backup-mysql-database-php
 *  * compression in chunks: 
 *      http://stackoverflow.com/questions/6073397/how-do-you-create-a-gz-file-using-php
 */
function backup_by_mysqli($output_folder, $backup_name, $host, $db_name, $user, $pass) { //,$tables = '*')
    $db = new mysqli($host, $user, $pass, $db_name); 
    if ($db->connect_errno) {
        throw new InternalException(
            'Error connecting to database: errno='.$db->connect_errno);
    }
    if (!$db->set_charset("utf8")) {
        throw new InternalException(
            'Not able to set charset="utf8", current charset is: '.
                    $db->character_set_name());
    }
    $tables = array();
    $result = $db->query('SHOW TABLES;');
    while($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }
    $result->close();
    // set output gz_file name
    $gzfile_name = $backup_name.'.sql.gz';
    // Open the gz file (w9 is the highest compression)
    $chunck_max_len = 1024*256;
    try {
        $fp = gzopen(__ROOT__.$output_folder.$gzfile_name, 'w9');
    } catch(Exception $e) {
        throw new Exception($e->getMessage());
    }
    $from = "/* =========\n".
            "   Backup by mysqli from:\n".
            "     - server http: ".$_SERVER['HTTP_HOST']."\n".
            "     - mysql host:  $host \n".
            "     - db_name:     $db_name \n".
            "     - user:        $user \n".
            "     - date-time:   ".
                            strftime('%Y-%m-%d %H:%M', strtotime("now"))."\n".
            "   ========= */\n";
    gzwrite($fp, $from, strlen($from));

    // drop & crate tables
    $db->query('set sql_quote_show_create=0;');
    $drop_tables =      "\n\n/* =========\n   DROP TABLES\n   ========= */\n".
                        "SET SESSION FOREIGN_KEY_CHECKS=0;\n\n";
    $create_tables =    "\n\n/* =========\n   CREATE TABLES\n   ========= */\n";
    foreach($tables as $table) {
        $drop_tables.= 'DROP TABLE IF EXISTS '.$table.";\n";
        $rs2 = $db->query('SHOW CREATE TABLE '.$table);
        $row2 = $rs2->fetch_row();
        $create_tables.= $row2[1].";\n\n";
    }
    gzwrite($fp, $drop_tables, strlen($drop_tables));
    gzwrite($fp, $create_tables, strlen($create_tables));

    // data
    $data="\n\n/* =========\n   INSERTS\n   ========= */\n";
    $data .= "SET SESSION SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n".
            "-- SET SESSION time_zone = '+00:00';\n".
            "SET SESSION UNIQUE_CHECKS=0;\n".
            "SET SESSION FOREIGN_KEY_CHECKS=0;\n".
            "SET SESSION SQL_NOTES=0;\n".
            "\n";
    foreach($tables as $table) {
        $result = $db->query('SELECT * FROM '.$table);
        $num_fields = $result->field_count;

        $fields = $result->fetch_fields();
        $fields_list = '';
        foreach ($fields as $val) {
            $fields_list .= ',`'.$val->name.'`';
        }
        if ($fields_list == '') {
            continue;
        }
        $insert_into = 'INSERT INTO '.$table.
            ' ('.substr($fields_list, 1).") VALUES\n";
        $row_count = 0;
        $from_count = 1;
        $sub_count = 100;
        while($row = $result->fetch_row()) {
            if ($sub_count >= 100) {
                if ($row_count > 0) {
                    $data.= '); -- form:'.$from_count.' to:'.$row_count."\n";
                    $from_count += $sub_count;
                }
                $sub_count = 0;
                $data.= $insert_into;
            } else if ($row_count > 0) {
                $data.= "),\n";
            }
            $row_count++;
            $sub_count++;
            $data.= '(';
            for($j=0; $j<$num_fields; $j++) {
                //$row[$j] = ereg_replace("\n","\\n",$row[$j]);
                if (isset($row[$j])) { 
                    $row[$j] = $db->real_escape_string($row[$j]);
                    $data.= '"'.$row[$j].'"' ;
                } else if (is_null(($row[$j]))) { 
                    $data.= 'NULL';
                } else {
                    $data.= '""';
                }
                if ($j<($num_fields-1)) {
                    $data.= ',';
                }
            }
            if (strlen($data) > $chunck_max_len) {
                // Compress data chunck
                gzwrite($fp,
                        substr($data,0,$chunck_max_len), $chunck_max_len);
                $data = substr($data, $chunck_max_len);
            }
        }
        if ($row_count > 0) {
            $data.= '); -- form:'.$from_count.' to:'.$row_count."  [END OF TABLE]\n";
        } else {
            $data.= '-- No rows on table: `'.$table."`  [END OF TABLE]\n";
        }
        $result->close();
        $data.="\n\n";
    }
    // Compress remaining data
    if ($data !== '') {
        gzwrite($fp, $data, strlen($data));
    }
    // Close the gz file and we're done
    gzclose($fp);

    // End
    return $gzfile_name;
}

?>
