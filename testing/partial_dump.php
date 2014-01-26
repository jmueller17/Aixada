<?php

require_once 'php/lib/table_manager.php';
require_once 'php/inc/database.php';
require_once 'php/utilities/general.php';
require_once 'php/utilities/tables.php';

class DBDumpManager {
    private $dump_db_name, $dump_db;
    private $db_name, $db;

    public function __construct($dump_db_name) {
	$this->dump_db_name = $dump_db_name;
	$this->dump_db = DBWrap::get_instance($dump_db_name,
					      'mysql',
					      'localhost',
					      'dumper',
					      'dumper');
	/*
	  create user 'dumper'@'localhost' identified by 'dumper';
	  grant all privileges on aixada_dump.* to 'dumper'@'localhost';
	  flush privileges;
	 */

	$this->db_name = configuration_vars::get_instance()->db_name;
	$this->db = DBWrap::get_instance('');
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

EOD
		);
	fclose($handle);
	exec("mysql -u dumper --password=dumper $this->dump_db_name < /tmp/init_dump.sql");
    }

    private function _fill_queries($from_date, $to_date, $table_key_pairs)
    {
	$fill_queries = array();
	foreach ($table_key_pairs as $tkpair) {
	    list ($table, $date_key) = $tkpair;
	    $fkm = new foreign_key_manager($table);
	    foreach ($fkm->foreign_key_info() as $key => $info) {
		if (sizeof($info)==0) { continue; }
		$ft = $info['fTable'];
		$fk = $info['fIndex']; 
		$fill_queries[$ft][] = <<<EOD
insert into {$this->dump_db_name}.{$ft}
select distinct {$this->db_name}.{$ft}.* 
from {$this->db_name}.{$table}
left join {$this->db_name}.{$ft}
on {$this->db_name}.{$table}.{$key}={$this->db_name}.{$ft}.{$fk}
where {$this->db_name}.{$table}.{$date_key} between '{$from_date}' and '{$to_date}'
order by {$this->db_name}.{$ft}.{$fk};
EOD;
	    }
	}    
	return $fill_queries;
    }


    public function dump($from_date, $to_date, $table_key_pairs) {
	$this->_init_dump();
	foreach($this->_fill_queries($from_date, $to_date, $table_key_pairs) as $table => $queries) {
	    echo "table $table:\n";
	    foreach ($queries as $query) {
		echo "  query $query\n";
	    }
	}

	
    }
}



function partial_dump($from_date, $to_date, $table_key_pairs, $dump_db_name, $db_name)
{
    $dbdm = new DBDumpManager($dump_db_name, $db_name);
    $dbdm->dump($from_date, $to_date, $table_key_pairs);
}

partial_dump('2014-01-01', '2014-02-01', 
	     array(
		   ['aixada_cart', 'date_for_shop'],
		   ),
	     'aixada_dump',
	     'aixada'
	     );
?>