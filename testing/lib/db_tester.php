<?php

class DBTester {

    private $dump_db_name;
    private $initial_dump_file;
    private $log_file;
    private $db;

    public function __construct($dump_db_name, 
				$initial_dump_file, 
				$log_file) {
	$this->dump_db_name = $dump_db_name;
	$this->initial_dump_file = $initial_dump_file;
	$this->log_file = $log_file;

	$handle = @fopen('/tmp/init_test.sql', 'w');
	fwrite ($handle, <<<EOD
drop database {$this->dump_db_name};
create database {$this->dump_db_name};
use {$this->dump_db_name};
source {$this->initial_dump_file};

EOD
		);
	fclose($handle);
	exec("mysql -u dumper --password=dumper $this->dump_db_name < /tmp/init_test.sql");
	$this->db = DBWrap::get_instance($this->dump_db_name, 
					 'mysql',
					 'localhost',
					 'dumper',
					 'dumper');
    }

    private function check_one($statement, $checksum) {
	$db->Execute($statement);
	$db->free_next_results();
	exec("mysqldump -udumper -pdumper --skip-opt aixada_dump > testing/test_dump.sql");
	$md5sum = exec("head -n -2 testing/old_dump.sql > /tmp/x; head -n -2 testing/new_dump.sql > /tmp/y; diff /tmp/x /tmp/y | md5sum");

    }

    public function run_test() {
	$rhandle = @fopen($this->log_file, 'r');
	if (!$rhandle) {
	    echo "Could not open $this->log_file\n";
	    exit();
	}
	while (($db_line = fgets($rhandle)) !== false) {
	    $checksum = fgets($rhandle);
	    

	
    }
}

?>