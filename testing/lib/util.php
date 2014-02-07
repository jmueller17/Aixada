<?php

function init_dump($dump_db_name, $initial_dump_file) {
    // the mysql interface doesn't permit sourcing files, so we have to brute-force it
    // using the command line
    global $init_db_script;
    $ctime = time();
    if (($handle = fopen($init_db_script, 'w')) === false) {
	echo "could not open $init_db_script for writing.\n";
	exit();
    }
    fwrite ($handle, <<<EOD
drop database {$dump_db_name};
create database {$dump_db_name};
use {$dump_db_name};
set foreign_key_checks = 0;
source {$initial_dump_file}; 
source sql/setup/aixada_queries_all.sql;

EOD
	    );
    fclose($handle);
    do_exec("mysql -u dumper --password=dumper $dump_db_name < $init_db_script");
    echo time()-$ctime . "s for setting up the test database\n";
}

function process_options($opts) {

    global $argv;

    $lopts = $opts;
    $shortopts = array();
    $longopts = array();

    while (sizeof($lopts)>0) {
	$s = array_shift($lopts);
	$shortopts[] = $s;
	$longopts[$s] = new LongOpt(array_shift($lopts), REQUIRED_ARGUMENT, null, $s);
    }
    $getopt = new Getopt($argv, join(':', $shortopts) . ':nd', $longopts);
    $result = array();
    while (($c = $getopt->getopts()) != -1) {
	$result[$c] = $getopt->getOptarg();
    }
    return $result;
}

function tables_in_database() {
    global $db_definition;
    if (!file_exists($db_definition)) {
	echo "Could not open database definition $db_definition for reading\n";
	exit();
    }

    $db_def = file_get_contents($db_definition);
    $token = 'create table ';
    $tables = array();
    foreach (explode("\n", $db_def) as $line) {
	if (($p = strpos($line, $token)) === false) continue;
	$p += strlen($token);
	$table = substr($line, $p, strpos($line, ' ', $p) - $p);
	$tables[] = $table;
    }
    return $tables;
}

function tables_used_in_calls($tables) {
    global $all_queries_file;
    if (!file_exists($all_queries_file)) {
	echo "Could not open query definitions $all_queries_file for reading\n";
	exit();
    }
    $queries = file_get_contents($all_queries_file);
    $used_in = array();
    $token = 'drop procedure if exists ';
    $query = '';
    foreach (explode("\n", $queries) as $line) {
	if (($p = strpos($line, $token)) !== false) {
	    $p += strlen($token);
	    $query = substr($line, $p, strpos($line, '|', $p) - $p);
	    if (strpos($query, 'list_') !== false
		|| strpos($query, 'get_') !== false) 
		continue;
	    $used_in[$query] = array();
	}
	if ($query == '' 
	    || strpos($query, 'list_') !== false
	    || strpos($query, 'get_') !== false) 
	    continue;
	foreach ($tables as $table) {
	    if (strpos($line, $table) !== false) {
		if (in_array($table, $used_in[$query])) 
		    continue;
		$used_in[$query][] = $table;
		break;
	    }
	}
    }
    return $used_in;
}

function extract_from_date($dumpfile) {
    global $dateformat;
    // Expect a filename of the form 
    // testing/dumps+logs/aixada_dump.2013-12-31@17:38-to-2014-01-30@17:38.sql
    $to_pos = strpos($dumpfile, '-to-');
    return substr($dumpfile, $to_pos-strlen($dateformat), strlen($dateformat));
}

function extract_to_date($dumpfile) {
    global $dateformat;
    // Expect a filename of the form 
    // testing/dumps+logs/aixada_dump.2013-12-31@17:38-to-2014-01-30@17:38.sql
    $to_pos = strpos($dumpfile, '-to-') + strlen('-to-');
    return substr($dumpfile, $to_pos, strlen($dateformat));
}

function do_log($str) {
    echo $str;
    global $testloghandle;
    fwrite($testloghandle, $str);
    flush();
}

?>