<?php

require_once("testing/lib/manager_base.php");
 
class LogManager extends ManagerBase {

    private $dump_db_name;
    private $original_dumpfile;
    private $log_from_time;
    private $log_to_time;
    private $bare_log_name;
    private $annotated_log_name;
    private $exclusion_patterns;
    private $db;
    private $rhandle;
    private $whandle;
    private $query = 'init';
    private $prev_md5sum;
    private $tables_in_database;
    private $tables_used_in_calls;

    public function __construct($dump_db_name, $original_dumpfile, $log_from_time_str, $log_to_time_str) {
	parent::__construct();
	global $dumppath, $logpath, $testrunpath, $tmpdump, $utilpath;

	$this->dump_db_name = $dump_db_name;	
	$this->original_dumpfile = $dumppath . $original_dumpfile;
	$this->log_from_time = strtotime(str_replace('@', ' ', $log_from_time_str)); 
	$this->log_to_time =   strtotime(str_replace('@', ' ', $log_to_time_str));
	$this->bare_log_name = "$logpath$dump_db_name.$log_from_time_str-to-$log_to_time_str.bare_log";
	$this->annotated_log_name = "$logpath$dump_db_name.$log_from_time_str-to-$log_to_time_str.annotated_log";
	$this->exclusion_patterns = $utilpath . 'non_modifying_query_patterns.for_grep';
	$this->db = DBWrap::get_instance($dump_db_name, 
					 false,
					 'mysql',
					 'localhost',
					 'dumper',
					 'dumper');
	$this->db->Execute('set foreign_key_checks=0;');
	exec ("rm -f {$tmpdump}");
	$this->tables_in_database = tables_in_database();
	$this->tables_used_in_calls = tables_used_in_calls($this->tables_in_database);
    }

    public function create_bare_log_of_modifying_queries() {
	global $db_log;
	exec("egrep -iv -f {$this->exclusion_patterns} $db_log > {$this->bare_log_name}");
	echo "created bare log {$this->bare_log_name}\n";
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
	    if (strpos($query, $table . ' ') !== false)
		$use_tables[] = $table;

	do_log("tables used by query: " . join(',', $use_tables) . "\n");
	return $use_tables;
    }

    // if $query=='', will dump entire database
    private function dump($query, $dumpfile) {
	global $sed;
	$exec_str = "mysqldump -udumper -pdumper --skip-opt {$this->dump_db_name} " . join(' ', $this->_tables_used_by_query($query)) . " | head -n -2 | {$sed} > $dumpfile";
	$ctime = time();
	do_exec($exec_str);
	do_log(time()-$ctime . "s for dumping the database\n");
    }

    private function clean(&$s) {
	$s = substr($s, 0, strpos($s, ' '));
    }

    private function hash($dumpfile) {
	$md5sum = do_exec("md5sum $dumpfile");
	$this->clean($md5sum);
	return $md5sum;
    }

    private function store($md5sum, $dir_to_store) {
	if (!strcmp($md5sum, $this->prev_md5sum)) return;
	if ($this->query != 'init') {
	    $clean_query = preg_replace('/\s+/', ' ', str_replace("\n", '\n', $this->query));
	    fwrite($this->whandle, $clean_query . "\n");
	}
	fwrite($this->whandle, "$md5sum\n");
	global $tmpdump;
	do_exec("mv $tmpdump {$dir_to_store}{$md5sum}");
    }

    private function split_line($line) {
	$pos_first_blank  = strpos($line, ' ');
	$pos_second_blank = strpos($line, ' ', $pos_first_blank + 1);
	$time_str = substr($line, 0, $pos_second_blank);
	$query = substr($line, $pos_second_blank + 1);
	return [$time_str, $query];
    }

    private function is_time_in_scope($time_str) {
	$logtime = strtotime($time_str);
	return ($logtime >= $this->log_from_time && 
		$logtime <= $this->log_to_time);
    }
    
    private function execute_query($query){
	$query = str_replace('\n', "\n", $query);
	do_log("will execute query $query");
	$ctime = time();
	$this->db->Execute($query);
	$this->db->free_next_results();
	do_log(time()-$ctime . "s for executing the query\n");
    }

    private function prepare_output_files() {
	if (($this->rhandle = fopen($this->bare_log_name, 'r')) === false) {
	    do_log("Could not open {$this->bare_log_name} for reading\n");
	    exit();
	}

	// create empty file
	$this->whandle = @fopen($this->annotated_log_name, 'w');
	if (!$this->whandle) {
	    echo "Could not open {$this->annotated_log_name} for writing\n";
	    exit();
	}
    }

    private function dump_hash_and_store($query) {
	global $tmpdump;

	if (strlen($query)>0) {
	    $this->dump($query, $tmpdump);
	    $old_hash = $this->hash($tmpdump);

	    $this->execute_query($query);
	}
	$this->dump($query, $tmpdump);
	$new_hash = $this->hash($tmpdump);

	if (strlen($query)>0 &&
	    !strcmp($old_hash, $new_hash)) {
	    do_log("line does not modify the database.\n"); 
	} else {
	    global $reference_dump_dir;
	    $this->store($new_hash, $reference_dump_dir);
	}
    }

    public function create_annotated_log() {
	$this->prepare_output_files();
	$this->dump_hash_and_store('');
	
	while (($line = fgets($this->rhandle)) !== false) {
	    do_log("\nprocessing $line");
	    list ($time_str, $query) = $this->split_line($line);
	    if (!$this->is_time_in_scope($time_str)) {
		do_log("line is not in time scope.\n"); continue;
	    }
	    $this->dump_hash_and_store($query);
	}
	echo "created annotated log {$this->annotated_log_name}\n";
    }

}

?>