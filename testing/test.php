<?php

require_once('testing/lib/config.php');
require_once('gnu/getopt/Getopt.php');
require_once('gnu/getopt/Longopt.php');

$usage_str = <<<EOD
Usage:

    php {$argv[0]} init 
    php {$argv[0]} dump [-f|-from-time='...'] [-t|-to-time='...']
    php {$argv[0]} log [dump_file.sql | -f|-from-time='...'] [-t|-to-time='...']
    php {$argv[0]} test [logfile] [dumpfile]
    php {$argv[0]} clean

See the README file for details. An additional flag -n activates dry run mode; -d activates debug mode. 
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

$logfile = ''; // for later use

require_once 'lib/util.php';

$getopt = new Getopt($argv, 'nd');
$getopt->setOpterr(false);
while (($c = $getopt->getopts()) != -1) {
    if ($c == 'n') {
	echo "Dry run mode activated.\n";
	$debug = true;
	$dry_run = true;
    }
    if ($c == 'd') {
	echo "Debug mode activated.\n";
	$debug = true;
    }
}


switch ($argv[1]) {
case 'init':
    require('testing/lib/init.php');
    break;

case 'dump':
    require('testing/lib/dump.php');
    break;

case 'log':
    require('testing/lib/log.php');
    break;

case 'test':
    require('testing/lib/test.php');
    break;

case 'clean':
    require('testing/lib/clean.php');
    break;

default:
    echo $usage_str;
    exit();
}


?>