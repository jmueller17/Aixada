<?
    $dumpfile = '';
    if (sizeof($argv) >= 3) {
	global $dumppath;
	if (!file_exists($argv[2])) {
	    echo "Usage: {$argv[0]} test <{$dumppath}dump-file.sql>\n";
	    exit();
	}
	$dumpfile = $argv[2];
    }
    require_once 'testing/lib/dump_manager.php';
    require_once 'testing/lib/test_manager.php';
    $ctime = time();
    $testm = new TestManager($dump_db_name, $dumpfile);
    $testm->test();
    echo time()-$ctime . "s for testing.\n";

?>