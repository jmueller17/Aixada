<?php

require_once("testing/lib/config.php");
 
class LogManager {

    private $dump_db_name;
    private $original_dumpfile;
    private $log_from_time;
    private $log_to_time;
    private $bare_log_name;
    private $annotated_log_name;
    private $exclusion_patterns;
    private $db;
    private $whandle;
    private $query = 'init';
    private $prev_md5sum;
    private $tables_in_database;
    private $tables_used_in_calls;

    public function __construct($dump_db_name, $original_dumpfile, $log_from_time, $log_to_time) {

	global $dumppath, $logpath, $testrunpath, $tmpdump, $utilpath;

	$this->dump_db_name = $dump_db_name;	
	$this->original_dumpfile = $dumppath . $original_dumpfile;
	$this->log_from_time = $log_from_time;
	$this->log_to_time = $log_to_time;
	$this->bare_log_name = "$logpath$dump_db_name.$log_from_time-to-$log_to_time.bare_log";
	$this->annotated_log_name = "$logpath$dump_db_name.$log_from_time-to-$log_to_time.annotated_log";
	$this->exclusion_patterns = $utilpath . 'non_modifying_query_patterns.for_grep';
	$this->db = DBWrap::get_instance($dump_db_name, 
					 false,
					 'mysql',
					 'localhost',
					 'dumper',
					 'dumper');
	exec ("rm -f {$tmpdump}");
	$this->tables_in_database = tables_in_database();
	$this->tables_used_in_calls = tables_used_in_calls($this->tables_in_database);
    }

    public function create_bare_log_of_modifying_queries() {
	global $db_log;
	exec("egrep -iv -f {$this->exclusion_patterns} $db_log > {$this->bare_log_name}");
    }

    private function clean(&$s) {
	$s = substr($s, 0, strpos($s, ' '));
    }

    private function _tables_used_by_query($query) {
	$use_tables = array();
	if ($query == '') return $use_tables;
	foreach ($this->tables_used_in_calls as $call => $tables) 
	    if (strpos($query, $call) !== false)
		foreach ($tables as $table) 
		    if (!in_array($table, $use_tables))
			$use_tables[] = $table;

	foreach ($this->tables_in_database as $table) 
	    if (strpos($query, $call) !== false)
		foreach ($tables as $table) 
		    if (!in_array($table, $use_tables))
			$use_tables[] = $table;

	return $use_tables;
    }

    private function dump($query) {
	global $tmpdump, $sed;
	$exec_str = "mysqldump -udumper -pdumper --skip-opt {$this->dump_db_name} " . join(' ', $this->_tables_used_by_query($query)) . " | head -n -2 | {$sed} > $tmpdump";
	$ctime = time();
	do_exec($exec_str);
	echo time()-$ctime . "s for dumping the database\n";
    }

    private function hash() {
	global $tmpdump;
	$md5sum = do_exec("md5sum $tmpdump");
	$this->clean($md5sum);
	return $md5sum;
    }

    private function store($md5sum, $dir_to_store) {
	if (!strcmp($md5sum, $this->prev_md5sum)) return;
	if ($this->query != 'init') 
	    fwrite($this->whandle, $this->query);
	fwrite($this->whandle, $md5sum . "\n");
	global $tmpdump;
	do_exec("mv $tmpdump {$dir_to_store}{$md5sum}");
	$this->prev_md5sum = $md5sum;
    }

    private function dump_hash_and_store($query) {
	global $reference_dump_dir;
	$this->dump($query);
	$this->store($this->hash(), $reference_dump_dir);
    }

    private function process_line($line) {
	$pos_first_blank  = strpos($line, ' ');
	$pos_second_blank = strpos($line, ' ', $pos_first_blank + 1);
	$logtime = strtotime(substr($line, 0, $pos_first_blank));
	if ($logtime >= $this->log_from_time && 
	    $logtime <= $this->log_to_time) {
	    $this->query = substr($line, $pos_second_blank+1);
	    $this->db->Execute($this->query);
	    $this->db->free_next_results();
	    $this->dump_hash_and_store($query);
	}
    }

    public function create_annotated_log() {
	$rhandle = @fopen($this->bare_log_name, 'r');
	if (!$rhandle) {
	    echo "Could not open {$this->bare_log_name} for reading\n";
	    exit();
	}

	// create empty file
	$this->whandle = @fopen($this->annotated_log_name, 'w');
	if (!$this->whandle) {
	    echo "Could not open {$this->annotated_log_name} for writing\n";
	    exit();
	}

	$this->dump_hash_and_store('');
	
	while (($line = fgets($rhandle)) !== false) 
	    $this->process_line($line);
    }

}

?>