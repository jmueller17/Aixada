<?php

require_once("testing/lib/config.php");
 
class LogManager {

    private $dump_db_name;
    private $original_dumpfile;
    private $from_date;
    private $to_date;
    private $bare_log_name;
    private $annotated_log_name;
    private $exclusion_patterns;
    private $db;
    private $whandle;
    private $query = 'init';
    private $prev_md5sum;

    public function __construct($dump_db_name, $original_dumpfile, $from_date, $to_date) {

	global $dumppath, $logpath, $testrunpath, $tmpdump, $utilpath;

	$this->dump_db_name = $dump_db_name;	
	$this->original_dumpfile = $dumppath . $original_dumpfile;
	$this->from_date = $from_date;
	$this->to_date = $to_date;
	$this->bare_log_name = "$logpath$dump_db_name.$from_date-to-$to_date.bare_log";
	$this->annotated_log_name = "$logpath$dump_db_name.$from_date-to-$to_date.annotated_log";
	$this->exclusion_patterns = $utilpath . 'non_modifying_query_patterns.for_grep';
	$this->db = DBWrap::get_instance($dump_db_name, 
					 false,
					 'mysql',
					 'localhost',
					 'dumper',
					 'dumper');
	exec ("rm -f {$tmpdump}");
    }

    public function create_bare_log_of_modifying_queries() {
	global $db_log;
	exec("egrep -iv -f {$this->exclusion_patterns} $db_log > {$this->bare_log_name}");
    }

    private function clean(&$s) {
	$s = substr($s, 0, strpos($s, ' '));
    }

    private function dump() {
	global $tmpdump, $sed;
	$ctime = time();
	exec("mysqldump -udumper -pdumper --skip-opt {$this->dump_db_name} | head -n -2 | {$sed} > $tmpdump");
	echo time()-$ctime . "s for dumping the database\n";
    }

    private function hash() {
	global $tmpdump;
	$md5sum = exec("md5sum $tmpdump");
	$this->clean($md5sum);
	return $md5sum;
    }

    private function store($md5sum, $dir_to_store) {
	if (!strcmp($md5sum, $this->prev_md5sum)) return;
	if ($this->query != 'init') 
	    fwrite($this->whandle, $this->query);
	fwrite($this->whandle, $md5sum . "\n");
	global $tmpdump;
	exec("mv $tmpdump {$dir_to_store}{$md5sum}");
	$this->prev_md5sum = $md5sum;
    }

    private function dump_hash_and_store() {
	global $reference_dump_dir;
	$this->dump();
	$this->store($this->hash(), $reference_dump_dir);
    }

    private function process_line($line) {
	$pos_first_blank  = strpos($line, ' ');
	$pos_second_blank = strpos($line, ' ', $pos_first_blank + 1);
	$logdate = strtotime(substr($line, 0, $pos_first_blank));
	if ($logdate >= $this->start_date && 
	    $logtime <= $this->end_date) {
	    $this->query = substr($line, $pos_second_blank+1);
	    $this->db->Execute($this->query);
	    $this->db->free_next_results();
	    $this->dump_hash_and_store();
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

	$this->dump_hash_and_store();
	
	while (($line = fgets($rhandle)) !== false) 
	    $this->process_line($line);
    }

}

?>