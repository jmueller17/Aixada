<?php

require_once 'testing/lib/dump_manager.php';
require_once 'testing/lib/util.php';

list ($from_str, $to_str) = process_options(array('f', 'from-time', '-1 months', 
						  't', 'to-time', '-1 days'));		       
$dump_from_time = date('Y-m-d@H:i', strtotime($from_str));
$dump_to_time   = date('Y-m-d@H:i', strtotime($to_str));

echo "dumping from $from_str($dump_from_time) to $to_str($dump_to_time) ...\n"; 
exit();
$ctime = time();
$dbdm = new DBDumpManager($dump_db_name, $dump_from_time, $dump_to_time, $table_key_pairs);
$dumpfile = $dbdm->create_initial_dump();
echo time()-$ctime . "s for creating dump\n";
?>