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
        Timestamps are neutralized.
        The results of executing all these queries in turn are stored in $reference_dump_dir.

    options:
        from_date: from which date on the database will be dumped. 
                   default -1 month
        to_date:   until which date the database will be dumped. 
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
    $dbdm = new DBDumpManager($dump_db_name, $from_date, $to_date, $table_key_pairs);
    $dumpfile = $dbdm->create_initial_dump();

    require_once 'lib/log_manager.php';
    echo "creating initial log of modifying queries...\n"; 
    $logm = new LogManager($dump_db_name, $dumpfile, $from_date, $to_date);
    $logm->create_bare_log_of_modifying_queries();

    echo "creating annotated log...\n"; 
    $logm->create_annotated_log();

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
    $testm = new TestManager($dump_db_name, $dumpfile);
    $testm->test();

    break;

default:
    echo $usage_str;
    exit();
}


?>