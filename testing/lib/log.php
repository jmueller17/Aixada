<?php

$result = process_options(array('f', 'from-time', '-1 months', 
				't', 'to-time', '-1 days'));
$dump_from_time = date('Y-m-d@H:i', strtotime($result['f']));
$dump_to_time   = date('Y-m-d@H:i', strtotime($result['t']));

$dumpfile = exec("ls -rt ${dumppath}{$dump_db_name}.*sql");
$log_to_time = 'now';

if (sizeof($argv) >= 4) {
    $dumpfile = $argv[2];
    $log_to_time = $argv[3];
}

$log_to_time = date('Y-m-d@H:i', strtotime($log_to_time));

echo "logging from $dumpfile until $log_to_time\n";

require_once 'testing/lib/log_manager.php';
echo "creating log of modifying queries...\n"; 
$ctime = time();
$logm = new LogManager($dump_db_name, $logfile, $dump_to_time, $log_to_time);
$logm->create_bare_log_of_modifying_queries();
echo time()-$ctime . "s for creating bare log\n";

echo "creating annotated log...\n"; 
$ctime = time();
$logm->create_annotated_log();
echo time()-$ctime . "s for creating annotated log\n";

?>