<?php


$dump_db_name = 'aixada_dump';
$db_name = 'aixada';

// This array controls which tables are dumped. 
// For each table, you need to specify a datetime key, 
// for example 'date_for_shop' for the table 'aixada_cart'.
// Only the entries of the table within the time interval specified by the user
// will be dumped, along with all the entries in other tables pointed to 
// by foreign keys.
$table_key_pairs = array(
			 ['aixada_cart', 'date_for_shop'],
			 );

$usage_str = <<<EOD
Usage:
    php {$argv[0]} <command> [<options>]

Commands: 

  init: create a mysql user for dumping and querying the data. 
        You need to enter a mysql username and password with sufficient privileges for doing this.

  dump: create the database aixada_dump from entries in the database aixada, and execute the logfile aixada.log on it.
        To control which tables are written to aixada_dump, please edit the variable \$table_key_pairs in this file.
        The database aixada_dump is then dumped to testing/dumps/initial_dump.sql.
        This logfile aixada.log is actually created in the process of running this script, so there's no problem if 
        it doesn't exist initially.
        The logfile is preprocessed to strip all non-modifying commands from it, and the result of this is stored 
        in the directory testing/logs/.
        The results of executing all surviving queries in turn are stored in testing/runs/reference_dumps.
    options:
        from_date: from which date on the database will be dumped. 
                   default -1 month
        to_date:   until which date the database will be dumped. 
                   default now

  test: rerun the logfile on the dumped database, and check whether the database is modified
        in the same way as when the dump was created. 
        The results of each run are stored in testing/runs/ under the current time.

EOD;

if (sizeof($argv) < 2) {
    echo $usage_str;
    exit();
}

if ($argv[1] != 'init') {
    require_once 'lib/dump_manager.php';
    try {
	$dump_db = DBWrap::get_instance($dump_db_name,
					'mysql',
					'localhost',
					'dumper',
					'dumper');
    } catch (InternalException $e) {
	echo "caught exception $e\n\n";
	echo "It appears that you haven't yet created a mysql user for testing the database.\n";
	echo "Please run \"{$argv[0]} init\" now to do this.\n\n";
	exit();
    }
}

switch ($argv[1]) {
case 'init':
    $user = readline('mysql username with sufficient privileges [default=root]:');
    if ($user == '') $user = 'root';
    echo "password for $user: ";
    $pwd = preg_replace('/\r?\n$/', '', `stty -echo; head -n1 ; stty echo`);
    echo "\n";

    $handle = @fopen('/tmp/init_user.sql', 'w');
    $script = <<<EOD
create user 'dumper'@'localhost' identified by 'dumper';
grant all privileges on {$dump_db_name}.* to 'dumper'@'localhost';
grant select on aixada.* to 'dumper'@'localhost';
flush privileges;

EOD
    ;
    fwrite ($handle, $script);
    fclose($handle);
    echo $script;
    $result = exec("mysql -u $user --password=$pwd $dump_db_name < /tmp/init_user.sql");
    break;

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