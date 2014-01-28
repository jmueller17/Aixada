<?php

$log_to_time = '';
$dumpfile = '';
if (sizeof($argv) >= 3) {
    $log_to_time = $argv[2];

    global $dumppath;
    $ls = "ls {$dumppath}{$dump_db_name}.*{$log_to_time}.sql";
    $dumpfile = exec("ls {$dumppath}{$dump_db_name}.*{$log_to_time}.sql");
    if (!strcmp($dumpfile, '')) {
	echo "No dumpfile found that ends at $log_to_time.\n";
	echo "Usage: {$argv[0]} log [<log_to_time> [<dumpfile>] ]\n";
	exit();
    } 
    if (exec($ls . '|wc -l') != 1) {
	echo "More than one dumpfile found that ends at $log_to_time.\n";
	echo "Usage: {$argv[0]} log [<log_to_time> [<dumpfile>] ]\n";
	exit();
    }
}

echo $dumpfile . "\n";
exit();

$logfile = '';
if (sizeof($argv) >= 3) {
    global $logpath;
    $log_extension = 'annotated_log';
    if (strcmp(substr($argv[2],-strlen($log_extension)), $log_extension) 
	|| !file_exists($argv[2])) {
	echo "Usage: {$argv[0]} log <{$logpath}{$dump_db_name}.time-to-time.{$log_extension}>\n";
	exit();
    }
    $logfile = $argv[2];
}

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