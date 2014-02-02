<?
$dumpfile = '';
for ($i=2; $i < sizeof($argv); $i++) {
    if (strpos($argv[$i], '.sql') !== false) {
	$dumpfile = $argv[$i];
	break;
    }
}

require_once 'testing/lib/dump_manager.php';
require_once 'testing/lib/test_manager.php';
$ctime = time();
$testm = new TestManager($dump_db_name, $dumpfile);
$testm->test();
echo time()-$ctime . "s for testing.\n";

?>