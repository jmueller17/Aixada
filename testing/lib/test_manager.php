<?php

class TestManager {

    private $dump_db_name;
    private $initial_dump_file;
    private $log_file;
    private $test_dir;
    private $reference_dump_dir;

    private $db;
    private $rhandle;

    private $statement;
    private $checkmd5;
    private $realmd5;

    // neutralize timestamps
    private $sed = "sed 's/[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9] [0-9][0-9]:[0-9][0-9]:[0-9][0-9]/timestamp/g' ";

    private $tmpdump = '/tmp/testdump.sql';


    public function __construct($dump_db_name, 
				$initial_dump_file, 
				$log_file) {

	$logpath = 'testing/logs/';
	$dumppath = 'testing/dumps/';
	$testrunpath = 'testing/test_runs/';

	$this->dump_db_name = $dump_db_name;
	$this->initial_dump_file = $dumppath . $initial_dump_file;
	$this->log_file = $logpath . $log_file;

	$day = date("Y-m-d");
	$now = date("H:i:m");
	$this->testdir = $testrunpath . '/' . $day . '/' . $now . '/';
	exec("mkdir -p {$testrunpath}$day/$now");

	$this->reference_dump_dir = $testrunpath . 'reference_dumps/';

	$handle = @fopen('/tmp/init_test.sql', 'w');
	fwrite ($handle, <<<EOD
drop database {$this->dump_db_name};
create database {$this->dump_db_name};
use {$this->dump_db_name};
source {$this->initial_dump_file};
source sql/setup/aixada_queries_all.sql;

EOD
		);
	fclose($handle);
	exec("mysql -u dumper -pdumper $this->dump_db_name < /tmp/init_test.sql");
	$this->db = DBWrap::get_instance($this->dump_db_name, 
					 'mysql',
					 'localhost',
					 'dumper',
					 'dumper');

	$this->rhandle = @fopen($this->log_file, 'r');
	if (!$this->rhandle) {
	    echo "Could not open {$this->log_file}\n";
	    exit();
	}
	
	if ($this->one_hash_ok() != 1) {
	    echo "The database dump is not the one used to generate the log entries.\n";
	    echo "The hash of the database should have been\n{$this->checkmd5}";
	    echo "but was\n{$this->realmd5}\n";
	    exit();
	}
    }

    private function clean(&$s) {
	$s = substr($s, 0, strpos($s, ' '));
    }

    private function one_hash_ok() {
	if (($this->checkmd5 = fgets($this->rhandle)) === false) {
	    echo "No more hashes.\n";
	    return -1;
	}
	$this->checkmd5 = trim($this->checkmd5);

	$this->realmd5 = exec("mysqldump -udumper -pdumper --skip-opt aixada_dump | head -n -2 | {$this->sed} > {$this->tmpdump}; md5sum {$this->tmpdump}");
	$this->clean($this->realmd5);

	// store the dump for future reference
	exec("mv -n {$this->tmpdump} {$this->testdir}{$this->realmd5}");

	if (strcmp($this->checkmd5, $this->realmd5) == 0) return 1;

	echo "The checksum\n{$this->checkmd5}\n";
	echo "for the reference dump disagreed with the checksum\n{$this->realmd5}\n";
	echo "for the current dump. The difference is\n";
	echo exec("diff {$this->reference_dump_dir}{$this->checkmd5} {$this->testdir}{$this->realmd5}");
	return 0;
    }

    private function one_statement_ok() {
	if (($this->statement = fgets($this->rhandle)) === false) {
	    echo "All tests ran successfully.\n";
	    return -1;
	}	
	$this->db->Execute($this->statement);
	$this->db->free_next_results();
	return $this->one_hash_ok();
    }

    public function test() {
	while (($result = $this->one_statement_ok()) != -1);
	if ($result == 0) {
	    echo "test failed on statement\n$statement\n";
	    echo "Hash of database should have been {$this->checkmd5}";
	    echo "but was {$this->realmd5}\n";
	    exit();
	}
    }
}

?>