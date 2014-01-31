<?php

function init_dump($dump_db_name) {
	// the mysql interface doesn't permit sourcing files, so we have to brute-force it
	// using the command line
	$ctime = time();
	$handle = @fopen('/tmp/init_dump.sql', 'w');
	fwrite ($handle, <<<EOD
drop database {$dump_db_name};
create database {$dump_db_name};
use {$dump_db_name};
source sql/aixada.sql;
source sql/setup/aixada_queries_all.sql;

EOD
		);
	fclose($handle);
	do_exec("mysql -u dumper --password=dumper $dump_db_name < /tmp/init_dump.sql");
	echo time()-$ctime . "s for setting up the test database\n";
    }

function process_options($opts) {

    global $argv;

    $lopts = $opts;
    $shortopts = array();
    $longopts = array();
    $defaults = array();

    while (sizeof($lopts)>0) {
	$s = array_shift($lopts);
	$shortopts[] = $s;
	$longopts[$s] = new LongOpt(array_shift($lopts), REQUIRED_ARGUMENT, null, $s);
	$defaults[$s] = array_shift($lopts);
    }
    $getopt = new Getopt($argv, join(':', $shortopts) . ':', $longopts);
    $result = array();
    while (($c = $getopt->getopts()) != -1) {
	$result[$c] = $getopt->getOptarg();
    }
    $n_set = 0;
    foreach ($shortopts as $s) {
	if (isset($result[$s])) $n_set++;
	else $result[$s] = $defaults[$s];
    }
    $result['n_set'] = $n_set;
    return $result;
}

?>