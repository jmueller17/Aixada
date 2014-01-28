<?php
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

    options:
        dump_from_time: from which time on the database will be dumped. 
                        default -1 month
        dump_to_time:   until which date the database will be dumped. 
                        default -1 day

  log [<log_to_time> [<dumpfile>] ]: apply each query in $db_log in turn and hash each result.

        Those commands from the logfile $db_log that modify the database are written to 
	$dumppath, along with the hash value of the database dump after each command. 
        Timestamps are neutralized to make the hash value meaningful.
        The results of executing all these queries in turn are stored in $reference_dump_dir.

    options:
        log_to_time:  the logfile will be processed from dump_to_time to log_to_time.
                      default now
        dumpfile:     the database dump to be used; default the newest dump


  test [<dump-file>]: rerun the logfile on the dumped database <dump-file>, and check whether it is modified
        in the same way as when the dump was created. 
        The default dump is the latest one found in $dumppath. 
        The database '$dump_db_name' is smashed and restored from that dump file.
        The results of each run are stored in testing/runs/ under the current time.

EOD;
?>