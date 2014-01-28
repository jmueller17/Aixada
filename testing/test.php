<?php

require_once('testing/lib/config.php');

$usage_str = <<<EOD
Usage:
    php {$argv[0]} <command> [<options>]

Commands: 

  init: create a mysql user for dumping and querying the data. 
        You need to enter a mysql username and password with sufficient privileges for doing this.

  dump: for a specified time interval, create a dump of the database and the operations on it 
        that fall inside the interval. The default interval is one month into the past.

        More precisely, the tables in the database '$db_name' specified in the variable \$table_key_pairs in 
        the directory {$utilpath}config.php are written to the database '$dump_db_name', along with all 
        entries from other tables that recursively depend on them via foreign key constraints. The database
        '$dump_db_name' is then dumped to the directory $dumppath.

        Next, those commands from the logfile $db_log that modify the database are written to 
	$dumppath, along with the hash value of the database dump after each command. 
        Timestamps are neutralized to make the hash value meaningful.
        The results of executing all these queries in turn are stored in $reference_dump_dir.

    options:
        dump_from_time: from which time on the database will be dumped. 
                        default -1 month
        dump_to_time:   until which date the database will be dumped. 
                        default -1 day
        log_to_time:    the logfile will be processed from dump_to_time to log_to_time.
                        default now

  test [<dump-file>]: rerun the logfile on the dumped database <dump-file>, and check whether it is modified
        in the same way as when the dump was created. 
        The default dump is the latest one found in $dumppath. If a dump file is explicitly given, 
        the database '$dump_db_name' is smashed and restored from that dump file.
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
					false,
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
    echo time()-$ctime . "s for creating initial dump\n";

    require_once 'lib/log_manager.php';
    echo "creating initial log of modifying queries...\n"; 
    $ctime = time();
    $logm = new LogManager($dump_db_name, $dumpfile, $dump_to_time, $log_to_time);
    $logm->create_bare_log_of_modifying_queries();
    echo time()-$ctime . "s for creating bare log\n";

    echo "creating annotated log...\n"; 
    $ctime = time();
    $logm->create_annotated_log();
    echo time()-$ctime . "s for creating annotated log\n";

    break;

case 'test':
    $dumpfile = '';
    if (sizeof($argv) >= 3) {
	global $dumppath;
	if (!file_exists($argv[2])) {
	    echo "Usage: {$argv[0]} test <{$dumppath}dump-file.sql>\n";
	    exit();
	}
	$dumpfile = $argv[2];
    }
    require_once 'lib/dump_manager.php';
    require_once 'lib/test_manager.php';
    $ctime = time();
    $testm = new TestManager($dump_db_name, $dumpfile);
    $testm->test();
    echo time()-$ctime . "s for testing.\n";

    break;

default:
    echo $usage_str;
    exit();
}


?>