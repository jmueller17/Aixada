<?php
    require_once 'testing/lib/dump_manager.php';
    $dump_from_time = (sizeof($argv) >= 3) 
	? date('Y-m-d@H:i', strtotime($argv[2]))
	: date('Y-m-d@H:i', strtotime('-1 months'));
    $dump_to_time = (sizeof($argv) >= 4)
	? date('Y-m-d@H:i', strtotime($argv[3]))
	: date('Y-m-d@H:i', strtotime('-1 day'));
    $log_to_time = (sizeof($argv) >= 5)
	? date('Y-m-d@H:i', strtotime($argv[4]))
	: date('Y-m-d@H:i', strtotime('now'));

    echo "dumping from " . date($dump_from_time) . " to " . date($dump_to_time) . "...\n"; 
    $ctime = time();
    $dbdm = new DBDumpManager($dump_db_name, $dump_from_time, $dump_to_time, $table_key_pairs);
    $dumpfile = $dbdm->create_initial_dump();
    echo time()-$ctime . "s for creating dump\n";
?>