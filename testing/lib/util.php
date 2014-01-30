<?php

function init_dump() {
	// the mysql interface doesn't permit sourcing files, so we have to brute-force it
	// using the command line
	$ctime = time();
	$handle = @fopen('/tmp/init_dump.sql', 'w');
	fwrite ($handle, <<<EOD
drop database {$this->dump_db_name};
create database {$this->dump_db_name};
use {$this->dump_db_name};
source sql/aixada.sql;
source sql/setup/aixada_queries_all.sql;

EOD
		);
	fclose($handle);
	do_exec("mysql -u dumper --password=dumper $this->dump_db_name < /tmp/init_dump.sql");
	echo time()-$ctime . "s for setting up the test database\n";
    }

function process_options($opts) {
    $lopts = $opts;
    $shortopts = array();
    $longopts = array();
    $defaults = array();

    while (sizeof($lopts)>0) {
	$shortopts[] = array_shift($lopts) . '::';
	$longopts[] = array_shift($lopts) . '::';
	$defaults[] = array_shift($lopts);
    }
    
    $options = getopt(join('', $shortopts), $longopts);

    $result = array();
    for ($i=0; $i < sizeof($shortopts); $i++) {
	$result[] = isset($options[$shortopts[$i]]) 
	    ? $options[$shortopts[$i]] 
	    : (isset($options[$longopts[$i]]) 
	       ? $options[$longopts[$i]]
	       : $defaults[$i]);
    }
    
    return $result;
}

?>