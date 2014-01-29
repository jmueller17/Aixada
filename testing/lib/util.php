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
