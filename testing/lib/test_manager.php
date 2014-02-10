<?php

require_once('testing/lib/manager_base.php');

class TestManager extends ManagerBase {

    private $dump_db_name;
    private $initial_dump_file;
    private $log_file;
    private $test_dir;
    private $db;
    private $rhandle;

    private $statement = 'Initializing database';
    private $checkmd5;
    private $realmd5;

    public function __construct($dump_db_name, $initial_dump_file, $log_file) {
	parent::__construct();
	global $dumppath, $logpath, $testrunpath, $tmpdump, $utilpath;

	$this->_initialize_names($dump_db_name, $initial_dump_file, $log_file);
	$this->_make_dir_for_test_runs();
	$this->_prepare_resources();
	$this->_does_log_belong_to_hash();
    }

    private function _initialize_names($dump_db_name, $initial_dump_file, $log_file) {
	$this->dump_db_name = $dump_db_name;
	$this->initial_dump_file =  (strlen($initial_dump_file)==0)
	    ? $this->_latest_dump()
	    : $initial_dump_file;
	do_log("Using dump file\n");
	do_log("{$this->initial_dump_file}\n", 'blue');

	$log_from_date = extract_to_date($this->initial_dump_file);
	if (strlen($log_file) > 0)
	    $this->log_file = $log_file;
	else {
	    if (($this->log_file = exec("ls -rt $logpath$dump_db_name.$log_from_date*.annotated_log")) === false) {
		do_log("Please specify a file of the form '{$logpath}*.annotated_log'");
		exit();
	    }
	}
	do_log("Using database log file\n");
	do_log("{$this->log_file}\n", 'blue');
    }
    
    private function _make_dir_for_test_runs() {
	global $testrunpath;
	$day = date("Y-m-d");
	$now = date("H:i:m");
	$this->testdir = $testrunpath . $day . '/' . $now . '/';
	exec("mkdir -p {$testrunpath}$day/$now");
	do_log("writing results into\n");
	do_log("{$this->testdir}\n", 'blue');
    }

    private function _prepare_resources() {
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
	    log_error("Could not open log file {$this->log_file} for processing\n");
	    exit();
	}
    }

    private function _does_log_belong_to_hash() {
	if ($this->one_hash_ok() != 1) {
	    log_error("The database dump is not the one used to generate the log entries.\n");
	    do_log("The hash of the database should have been\n");
	    do_log("{$this->checkmd5}\n", 'green');
	    do_log("but was\n");
	    log_error("{$this->realmd5}\n");
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

	do_log("The checksum\n");
	do_log("{$this->checkmd5}\n", 'green');
	do_log("for the reference dump disagreed with the checksum\n");
	log_error("{$this->realmd5}\n");
	do_log("for the current dump.\n"
	       . "The offending query was\n");
	log_error(str_replace('\n', "\n", $this->statement) . "\n");
	do_log("The difference is\n");
	$diffstr1 = "{$reference_dump_dir}{$this->checkmd5} ";
	$diffstr2 = "{$this->testdir}{$this->realmd5}";
	do_log('diff ', 'blue');
	do_log($diffstr1, 'green');
	log_error($diffstr2 . "\n");
	do_log(exec('diff ' . $diffstr1 . $diffstr2) . "\n", 'blue');
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
	    log_error("Error: expected a hash value in {$this->log_file}.\n");
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
	    log_error("A test failed.\n");
	} elseif ($result == -1) {
	    do_log("All tests ran successfully.\n");
	} else {
	    log_error("Unexpected return value $result\n");
	}
    }
}

?>