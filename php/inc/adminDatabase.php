<?php
function get_backup_name() {
    return get_config('db_name'). '.' . date('Y-m-d H:i');
}

function backup_as_internal($output_folder, $backup_name) {
    // All warning is an error!
    set_error_handler(
        function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
            throw new Exception($err_msg);
        }
    );
    try{
        $cv = configuration_vars::get_instance();
        
        return $output_folder . backup_by_mysqli(
            $output_folder, $backup_name,        
            $cv->db_host, $cv->db_name, $cv->db_user, $cv->db_password
        );
    } catch(Exception $e) {
        throw new Exception($e->getMessage());  
    }
    restore_error_handler();
}

function connect_by_mysqli($host, $db_name, $user, $pass)
{
    $host_ = explode(":", $host);
    if (count($host_) > 1) {
        $db = new mysqli($host_[0], $user, $pass, $db_name, $host_[1]);
    } else {
        $db = new mysqli($host, $user, $pass, $db_name);
    }
    if ($db->connect_errno) {
        ob_clean();
        throw new Exception(
            "MySQL Error: {$db->connect_errno}-{$db->connect_error}\n" .
            "Connecting to: host='{$host}' database='{$db_name}' user='{$user}'\n"
        );
    }
    if (!$db->set_charset("utf8")) {
        ob_clean();
        throw new Exception(
            "Not able to set charset='utf8', charset is: {$db->character_set_name()}"
        );
    }
    return $db;
}

// Do a db update.
function execute_sql_files($db, $sql_folder, $sqlFilesArray) {
    $result = '';
    try{
        foreach ($sqlFilesArray as $file) {
            $result .= "\n * " . execute_sql_file($db, __ROOT__ . $sql_folder, $file);
        }
    } catch(Exception $e) {
        throw new Exception("Error running \"{$file}\": " . $e->getMessage());  
    }
    return $result . "\n";
}

/**
 * Do a DB backup using a internal instance of mysqli.
 * Initial points of inspiration: 
 *  * backup using mysqli: 
 *      http://davidwalsh.name/backup-mysql-database-php
 *  * compression in chunks: 
 *      http://stackoverflow.com/questions/6073397/how-do-you-create-a-gz-file-using-php
 */
function backup_by_mysqli($output_folder, $backup_name, $host, $db_name, $user, $pass)
{
    $db = connect_by_mysqli($host, $db_name, $user, $pass);
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
            "     - date-time:   ". date('Y-m-d H:i') ."\n".
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

function execute_sql_file($db, $folder, $file)
{
    $text = file_get_contents($folder . $file);
    $text = str_replace(array("\r\n", "\r"), array("\n", "\n"), $text);
    $delimeted = preg_split("/delimiter /i", $text);
    $text2 = $delimeted[0] . "\n";
    for ($i = 1; $i < count($delimeted); $i++) {
        $pos = strpos($delimeted[$i], "\n");
        $deli = substr($delimeted[$i], 0, $pos);
        $text2 .= 
            "-- START_DELIMETER_REMOVED '{$deli}' --\n" .
            str_replace(
                $deli . "\n",
                "; -- DELIMETER_TO_SEMICOLON --\n",
                substr($delimeted[$i] . "\n", $pos + 1)
            );
    }
    $i = 0;
    if ($db->multi_query($text2)) {
        do {
            $i++;
            if (!$db->more_results()) {
                break;
            }
        } while ($db->next_result());
    }
    if ($db->errno) {
        throw new Exception($db->error);
    }
    return "{$file}: {$i} staments executed correctly.";
} 
