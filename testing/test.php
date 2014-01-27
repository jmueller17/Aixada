<?php


$dump_db_name = 'aixada_dump';
$db_name = 'aixada';
$table_key_pairs = array(
			 ['aixada_cart', 'date_for_shop'],
			 );
$usage_str = <<<EOD
Usage:
    php {$argv[0]} dump [from_date=-1month [to_date=now]]
    php {$argv[0]} test ...

EOD;

if (sizeof($argv) < 2) {
    echo $usage_str;
    exit();
}

switch ($argv[1]) {
case 'dump':
    require_once 'lib/dump_manager.php';
    $from_time = (sizeof($argv) >= 3) 
	? strtotime($argv[2])
	: strtotime('-1 months');
    $from_date = date("Y-m-d", $from_time);
    $to_time = (sizeof($argv) >= 4)
	? strtotime($argv[3])
	: strtotime('now');
    $to_date = date("Y-m-d", $to_time);

    echo "dumping...\n"; 
    $dbdm = new DBDumpManager($dump_db_name, $db_name);
    $dumpfile = $dbdm->create_initial_dump($from_date, $to_date, $table_key_pairs);

    require_once 'lib/log_manager.php';
    echo "creating initial log of modifying queries...\n"; 
    $logm = new LogManager($dump_db_name, $dumpfile);
    $logm->create_bare_log_of_modifying_queries('aixada.log');

    echo "creating annotated log...\n"; 
    $logm->create_annotated_log($from_time, $to_time);

    break;

case 'test':
    require_once 'lib/dump_manager.php';
    require_once 'lib/test_manager.php';
    $testm = new TestManager('aixada_dump', 'initial_dump.sql', 'annotated_log_of_modifying_queries.aixada_dump.log');
    $testm->test();

    break;

default:
    echo $usage_str;
    exit();
}


?>