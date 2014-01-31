<?php

$result = process_options(array('f', 'from-time', 
				't', 'to-time'));
$from_str = isset($result['f'])
    ? $result['f']
    : '-1 months';
$to_str = isset($result['t'])
    ? $result['t']
    : '-1 days';
$dump_from_time = date('Y-m-d@H:i', strtotime($from_str));
$dump_to_time   = date('Y-m-d@H:i', strtotime($to_str));

echo "dumping from {$from_str} ($dump_from_time) to {$to_str} ($dump_to_time) ...\n"; 

require_once 'testing/lib/dump_manager.php';
$ctime = time();
$dbdm = new DBDumpManager($dump_db_name, $dump_from_time, $dump_to_time, $table_key_pairs);
$dumpfile = $dbdm->create_initial_dump();
echo time()-$ctime . "s for creating dump\n";
?>