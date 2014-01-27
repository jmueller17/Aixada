<?php

require_once 'php/lib/table_manager.php';
require_once 'php/inc/database.php';
require_once 'php/utilities/general.php';
require_once 'php/utilities/tables.php';
require_once 'testing/lib/config.php';

class DBDumpManager {
    private $dump_db_name, $dump_db;
    private $db_name, $db;
    private $from_date;
    private $to_date;
    private $table_key_pairs;

    public function __construct($dump_db_name, $from_date, $to_date, $table_key_pairs) {
	$this->dump_db_name = $dump_db_name;
	$this->dump_db = DBWrap::get_instance($dump_db_name,
					      'mysql',
					      'localhost',
					      'dumper',
					      'dumper');
	$this->db_name = configuration_vars::get_instance()->db_name;
	$this->db = DBWrap::get_instance('');
	$this->from_date = $from_date;
	$this->to_date = $to_date;
	$this->table_key_pairs = $table_key_pairs;
    }

    private function _init_dump() {
	// the mysql interface doesn't permit sourcing files, so we have to brute-force it
	// using the command line
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
	exec("mysql -u dumper --password=dumper $this->dump_db_name < /tmp/init_dump.sql");
    }

    private function _fill_queries()
    {
	$fill_queries = array();
	foreach ($this->table_key_pairs as $tkpair) {
	    list ($table, $date_key) = $tkpair;
	     echo "generating queries for $table...\n"; 
	    $fkm = new foreign_key_manager($table);
	    foreach ($fkm->foreign_key_info() as $key => $info) {
		if (sizeof($info)==0) { continue; }
		$ft = $info['fTable'];
		$fk = $info['fIndex']; 
		$query = <<<EOD
insert ignore into {$this->dump_db_name}.{$ft}
select distinct {$this->db_name}.{$ft}.* 
from {$this->db_name}.{$table}
left join {$this->db_name}.{$ft}
on {$this->db_name}.{$table}.{$key}={$this->db_name}.{$ft}.{$fk}
where {$this->db_name}.{$table}.{$date_key} between '{$this->from_date}' and '{$this->to_date}'
order by {$this->db_name}.{$ft}.{$fk};
EOD
    ;
		$fill_queries[$ft][] = $query;
	    }
	}    
	return $fill_queries;
    }


    public function create_initial_dump() {
	$this->_init_dump();
	$this->dump_db->Execute("set FOREIGN_KEY_CHECKS=0;");
	foreach($this->_fill_queries() as $table => $queries) {
	    echo "Executing queries for $table...\n"; 
	    foreach ($queries as $query) {
		$this->dump_db->Execute($query);
	    }
	}
	$this->dump_db->Execute("set FOREIGN_KEY_CHECKS=1;");

	global $dumppath;
	$dumpname = "$dumppath{$this->dump_db_name}.{$this->from_date}-to-{$this->to_date}.sql";
	echo "generating $dumpname ...\n"; 
	exec("mysqldump -udumper -pdumper --skip-opt {$this->dump_db_name} > $dumpname");
	return $dumpname;
    }
}

?>