<?
$dumpfile = '';
$logfile = '';
for ($i=2; $i < sizeof($argv); $i++) {
    if (strpos($argv[$i], '.sql') !== false) {
	$dumpfile = $argv[$i];
    }
    if (strpos($argv[$i], '.annotated_log') !== false) {
	$logfile = $argv[$i];
    }
}

if ($dumpfile != '' && 
    !file_exists($dumpfile)) {
    echo "Dump file $dumpfile does not exist.\n";
    exit();
}

if ($logfile != '' && 
    !file_exists($logfile)) {
    echo "Log file $logfile does not exist.\n";
    exit();
}

require_once 'testing/lib/dump_manager.php';
require_once 'testing/lib/test_manager.php';
$ctime = time();
$testm = new TestManager($dump_db_name, $dumpfile, $logfile);
$testm->test();
echo time()-$ctime . "s for testing.\n";

?>