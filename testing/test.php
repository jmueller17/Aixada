<?php

class DBTester {

    private $dump_db_name;
    private $initial_dump_file;
    private $log_file;

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
    }
}

$tester = new DBTester('aixada_dump', 'initial_dump', 'test.log');

?>