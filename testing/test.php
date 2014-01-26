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
    $from_date = (sizeof($argv) >= 3) 
	? date("Y-m-d", strtotime($argv[2]))
	: date("Y-m-d", strtotime('-1 months'));
    $to_date = (sizeof($argv) >= 4)
	? date("Y-m-d", strtotime($argv[3]))
	: date("Y-m-d", strtotime('now'));

    $dbdm = new DBDumpManager($dump_db_name, $db_name);
    $dbdm->create_initial_dump($from_date, $to_date, $table_key_pairs);
    break;

case 'test':
    require_once 'lib/db_tester.php';
    $tester = new DBTester('aixada_dump', 'initial_dump.sql', 'test.log');
    $tester->run_test();

    break;

default:
    echo $usage_str;
    exit();
}


?>