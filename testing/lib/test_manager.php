<?php

class TestManager {

    private $dump_db_name;
    private $initial_dump_file;
    private $log_file;
    private $db;
    private $rhandle;

    private $statement;
    private $checkmd5;
    private $realmd5;

    private $sed = "sed 's/[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9] [0-9][0-9]:[0-9][0-9]:[0-9][0-9]/timestamp/g' ";

    public function __construct($dump_db_name, 
				$initial_dump_file, 
				$log_file) {

	$logpath = 'testing/logs/';
	$dumppath = 'testing/dumps/';

	$this->dump_db_name = $dump_db_name;
	$this->initial_dump_file = $dumppath . $initial_dump_file;
	$this->log_file = $logpath . $log_file;

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
	
	if (($result = $this->one_hash_ok()) != 1) {
	    echo "The database dump is no good; result $result.\n";
	    echo "Hash should have been\n{$this->checkmd5}";
	    echo "but was\n{$this->realmd5}\n";
	    exit();
	}
    }

    private function one_hash_ok() {
	if (($this->checkmd5 = fgets($this->rhandle)) === false) {
	    echo "No more hashes.\n";
	    return -1;
	}
	$this->realmd5 = exec("mysqldump -udumper -pdumper --skip-opt aixada_dump | head -n -2 | {$this->sed} | md5sum");
	if (strcmp(trim($this->checkmd5), $this->realmd5) == 0) // the first has a trailing newline
	    return 1;
	else return 0;
    }

    private function one_statement_ok() {
	if (($this->statement = fgets($this->rhandle)) === false) {
	    echo "No more statements.\n";
	    return -1;
	}	
	$this->db->Execute($this->statement);
	$this->db->free_next_results();
	return $this->one_hash_ok();
    }

    public function test() {
	while (($result = $this->one_statement_ok()) != -1);
	if ($result == false) {
	    echo "test failed on statement\n$statement\n";
	    echo "Hash should have been {$this->checkmd5},\n";
	    echo "but was {$this->realmd5}\n";
	    exit();
	}
    }
}

?>