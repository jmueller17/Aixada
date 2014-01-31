<?php

$dumpfile = '';
for ($i=2; $i < sizeof($argv); $i++) {
    if (strpos($argv[$i], '.sql') !== false) {
	$dumpfile = $argv[$i];
	break;
    }
}

$result = process_options(array('f', 'from-time', 
				't', 'to-time'));

if ($dumpfile == '' && !isset($result['f'])) {
    $dumpfile = exec("ls -rt ${dumppath}{$dump_db_name}.*sql");
    $from_str = extract_to_date($dumpfile);
} elseif (strlen($dumpfile) > 0) {
    $from_str = extract_to_date($dumpfile);
} elseif (isset($result['f'])) {
    $from_str = $result['f'];
}
$to_str = (isset($result['t'])) 
      ? $result['t']
      : 'now';

$log_from_time_str = (strpos($from_str, '@') !== false)
    ? $from_str
    : date('Y-m-d@H:i', strtotime($from_str));
$log_to_time_str   = (strpos($to_str, '@') !== false)
    ? $to_str
    : date('Y-m-d@H:i', strtotime($to_str));

echo "executing the entries in $db_log between {$from_str} ($log_from_time_str) and {$to_str} ($log_to_time_str) onto $dumpfile ...\n"; 

require_once 'testing/lib/log_manager.php';
echo "creating log of modifying queries...\n"; 
$ctime = time();
$logm = new LogManager($dump_db_name, $logfile, $log_from_time_str, $log_to_time_str);
$logm->create_bare_log_of_modifying_queries();
echo time()-$ctime . "s for creating bare log\n";

echo "creating annotated log...\n"; 
$ctime = time();
$logm->create_annotated_log();
echo time()-$ctime . "s for creating annotated log\n";

?>