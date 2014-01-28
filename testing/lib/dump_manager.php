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
    private $process_queue = array(); 
    private $fill_queries = array();
    private $seen_tables = array();
    private $query_dump;

    public function __construct($dump_db_name, $from_date, $to_date, $table_key_pairs) {
	global $query_dump_dir;
	
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
	$this->query_dump = "$query_dump_dir$dump_db_name.$from_date-to-$to_date.query_log";

	/*
	  to model the query

	  select table3.* 
	  from table1
	  left join table2 on table1.key1 = table2.fkey1
	  left join table3 on table2.key2 = table3.fkey1
	  where table1.date_key in range;

	  we initialize process_queue with entries of the form

	  [ [ [table1, date_key], 
	      [table2, table1key1, table2fkey1], 
	      [table3, table2key2, table3fkey1] 
	    ], 
	    ... 
	  ]
	*/

	foreach ($table_key_pairs as $tkpair) {
	    list ($table, $date_key) = $tkpair;
	    $this->seen_tables[] = $table;
	    $qentry = [ [$table, $date_key] ];
	    $locally_seen_tables = array($table);
	    $this->_dfs($qentry, $table, $locally_seen_tables);
	}

	$this->_init_dump();
	$this->_fill_queries();

    }

    // do a depth-first search on the dependency graph induced by the foreign key constraints
    private function _dfs($qentry, $table, &$locally_seen_tables) {
	$fkm = new foreign_key_manager($table);
	$local_qentry = $qentry;
	foreach ($fkm->foreign_key_info() as $key => $info) {
	    if (sizeof($info)==0) continue; 
	    $ft = $info['fTable']; $fk = $info['fIndex']; 
	    if (in_array($ft, $locally_seen_tables)) continue;
	    $locally_seen_tables[] = $ft;
 	    $save_qentry = $local_qentry;
	    $local_qentry[] = [ $ft, $key, $fk ]; // [table2, table1key1, table2fkey1]
	    $this->_dfs($local_qentry, $ft, $locally_seen_tables);
	    $local_qentry = $save_qentry;
	}
	$this->process_queue[] = $local_qentry;
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

    private function _fill_queries() {
	foreach ($this->process_queue as $q) {
	    $e = end($q); $t = $e[0];
	    $query = "insert ignore into {$this->dump_db_name}.{$t}\n"
		. "select distinct {$this->db_name}.{$t}.*\n";
	    reset($q);
	    $query .= "from {$this->db_name}.{$q[0][0]}\n";
            for ($i=1; $i < sizeof($q); $i++) {
		$query .= "left join {$this->db_name}.{$q[$i][0]} "
		    . "on {$this->db_name}.{$q[$i-1][0]}.{$q[$i][1]} = "
		    . "{$this->db_name}.{$q[$i][0]}.{$q[$i][2]}\n";
	    }
	    $query .= "where {$this->db_name}.{$q[0][0]}.{$q[0][1]} "
		. "between '{$this->from_date}' and '{$this->to_date}'\n";
            $this->fill_queries[] = $query;
	}
    }

    public function create_initial_dump() {
	$qdhandle = @fopen($this->query_dump, 'w');
	if (!$qdhandle) {
	    echo "Could not open {$this->query_dump} for writing\n";
	    exit();
	}

	$this->dump_db->Execute("set FOREIGN_KEY_CHECKS=0;");
	foreach($this->fill_queries as $query) {
	    fwrite($qdhandle, $query . "\n");
	    $this->dump_db->Execute($query);
	}
	$this->dump_db->Execute("set FOREIGN_KEY_CHECKS=1;");
	fclose($qdhandle);

	global $dumppath;
	$dumpname = "$dumppath{$this->dump_db_name}.{$this->from_date}-to-{$this->to_date}.sql";
	echo "generating $dumpname ...\n"; 
	exec("mysqldump -udumper -pdumper --skip-opt {$this->dump_db_name} > $dumpname");
	return $dumpname;
    }
}

?>