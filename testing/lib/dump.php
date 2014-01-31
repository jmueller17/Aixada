<?php

$result = process_options(array('f', 'from-time', '-1 months', 
				't', 'to-time', '-1 days'));
$dump_from_time = date('Y-m-d@H:i', strtotime($result['f']));
$dump_to_time   = date('Y-m-d@H:i', strtotime($result['t']));

echo "dumping from {$result['f']} ($dump_from_time) to {$result['t']} ($dump_to_time) ...\n"; 

require_once 'testing/lib/dump_manager.php';
$ctime = time();
$dbdm = new DBDumpManager($dump_db_name, $dump_from_time, $dump_to_time, $table_key_pairs);
$dumpfile = $dbdm->create_initial_dump();
echo time()-$ctime . "s for creating dump\n";
?>