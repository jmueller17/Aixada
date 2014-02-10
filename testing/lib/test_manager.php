<?php

require_once('testing/lib/config.php');

class TestManager {

    private $dump_db_name;
    private $initial_dump_file;
    private $log_file;
    private $test_dir;
    private $db;
    private $rhandle;
    private $testloghandle;

    private $statement = 'Initializing database';
    private $checkmd5;
    private $realmd5;
    
    public function __construct($dump_db_name, $initial_dump_file, $log_file) {

	global $dumppath, $logpath, $testrunpath, $tmpdump, $utilpath, 
	    $testlogfilename, $testloghandle;
	if (($testloghandle = fopen($testlogfilename, 'w')) === false) {
	    echo "Could not open log file {$testlogfilename} for processing\n";
	    exit();
	}
	

	// initialize names
	$this->dump_db_name = $dump_db_name;
	$this->initial_dump_file =  (strlen($initial_dump_file)==0)
	    ? $this->_latest_dump()
	    : $initial_dump_file;
	do_log("Using dump file {$this->initial_dump_file}\n");

	$log_from_date = extract_to_date($this->initial_dump_file);
	if (strlen($log_file) > 0)
	    $this->log_file = $log_file;
	else {
	    if (($this->log_file = exec("ls -rt $logpath$dump_db_name.$log_from_date*.annotated_log")) === false) {
		do_log("Please specify a file of the form '{$logpath}*.annotated_log'");
		exit();
	    }
	}
	do_log("Using database log file {$this->log_file}\n");

	// make directory for test runs
	$day = date("Y-m-d");
	$now = date("H:i:m");
	$this->testdir = $testrunpath . $day . '/' . $now . '/';
	exec("mkdir -p {$testrunpath}$day/$now");
	do_log("writing results into {$this->testdir}\n");

	// prepare resources
	init_dump($this->dump_db_name, $this->initial_dump_file);
	do_log("Initialized database\n");
	$this->db = DBWrap::get_instance($this->dump_db_name, 
					 false,
					 'mysql',
					 'localhost',
					 'dumper',
					 'dumper');
	do_log("Acquired database handler\n");

	if (($this->rhandle = fopen($this->log_file, 'r')) === false) {
	    do_log("Could not open log file {$this->log_file} for processing\n");
	    exit();
	}

	// does the log really belong to the hash?
	if ($this->one_hash_ok() != 1) {
	    do_log("The database dump is not the one used to generate the log entries.\n"
		. "The hash of the database should have been\n{$this->checkmd5}\n"
		. "but was\n{$this->realmd5}\n");
	    exit();
	}
    }

    private function _latest_dump() {
	global $dumppath;
	$listing = exec("ls -rt {$dumppath}*.sql");
	return strtok($listing, " \n\t");
    }

    private function clean(&$s) {
	$s = substr($s, 0, strpos($s, ' '));
    }

    private function output_error() {
	global $reference_dump_dir;

	do_log("The checksum\n{$this->checkmd5}\n"
	       . "for the reference dump disagreed with the checksum\n{$this->realmd5}\n"
	       . "for the current dump.\n"
	       . "The offending query was\n"
	       . str_replace('\n', "\n", $this->statement)
	       . "\n"
	       . "The difference is\n"
	       . exec("diff {$reference_dump_dir}{$this->checkmd5} {$this->testdir}{$this->realmd5}")
	       . "\n");
    }

    /**
     *  Check one hash.
     *
     *  Return value: 
     *     1 if hash value passed test
     *     0 if it did not
     */
    private function one_hash_ok() {
	global $debug;
	if (($this->checkmd5 = fgets($this->rhandle)) === false) {
	    do_log("Error: expected a hash value in {$this->log_file}.\n");
	    exit();
	}
	$this->checkmd5 = trim($this->checkmd5);
	if ($debug) do_log("Checking against hash\n{$this->checkmd5}\n");

	global $tmpdump, $sed;
	$ctime = time();
	$this->realmd5 = do_exec("mysqldump -udumper -pdumper --skip-opt aixada_dump | head -n -2 | {$sed} > $tmpdump; md5sum $tmpdump");
	$this->clean($this->realmd5);
	if ($debug) do_log("found following hash after executing statement\n{$this->realmd5}\n");
	do_log(time() - $ctime . "s for dumping database\n");

	// store the dump for future reference
	do_exec("mv -n $tmpdump {$this->testdir}{$this->realmd5}");

	if (strcmp($this->checkmd5, $this->realmd5) == 0) return 1;

	$this->output_error();
	return 0;
    }

    /**
     *  Check one statement.
     *
     *  Return value: 
     *     1 if statement passed test
     *     0 if it did not
     *    -1 if no more statements
     */
    private function one_statement_ok() {
	if (($this->statement = fgets($this->rhandle)) === false) {
	    return -1;
	}	
	global $debug;
	if ($debug) do_log("will execute {$this->statement}\n");
	$this->db->Execute("set foreign_key_checks=0;");
	$this->db->Execute(str_replace('\n', '', $this->statement));
	$this->db->free_next_results();
	return $this->one_hash_ok();
    }

    public function test() {
	while (($result = $this->one_statement_ok()) == 1);
	if ($result == 0) {
	    do_log("A test failed.\n");
	} elseif ($result == -1) {
	    do_log("All tests ran successfully.\n");
	} else {
	    do_log("Unexpected return value $result\n");
	}
    }
}

?>