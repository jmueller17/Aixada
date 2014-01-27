<?php
 
class LogManager {

    private $dump_db_name;
    private $original_dumpfile;
    private $bare_log_name;
    private $annotated_log_name;
    private $reference_dump_dir;
    private $exclusion_patterns;
    private $db;
    private $whandle;
    private $query = 'init';
    private $prev_md5sum;

    // neutralize timestamps
    private $sed = "sed 's/[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9] [0-9][0-9]:[0-9][0-9]:[0-9][0-9]/timestamp/g' ";

    private $tmpdump = '/tmp/testdump.sql';

    public function __construct($dump_db_name, $original_dumpfile) {

	$logpath = 'testing/logs/';
	$utilpath = 'testing/lib/';
	$dumppath = 'testing/dumps/';
	$testrunpath = 'testing/runs/';

	$this->dump_db_name = $dump_db_name;	
	$this->original_dumpfile = $dumppath . $original_dumpfile;
	$this->bare_log_name = $logpath . 'bare_log_of_modifying_queries.' 
	    . $dump_db_name . '.log';
	$this->annotated_log_name = $logpath . 'annotated_log_of_modifying_queries.' 
	    . $dump_db_name . '.log';
	$this->reference_dump_dir = $testrunpath . 'reference_dumps/';
	$this->exclusion_patterns = $utilpath . 'non_modifying_query_patterns.for_grep';
	$this->db = DBWrap::get_instance($dump_db_name, 
					 'mysql',
					 'localhost',
					 'dumper',
					 'dumper');
	exec ("rm -f {$this->tmpdump}");
    }

    public function create_bare_log_of_modifying_queries($original_log) {
	exec("egrep -iv -f {$this->exclusion_patterns} $original_log > {$this->bare_log_name}");
    }

    private function clean(&$s) {
	$s = substr($s, 0, strpos($s, ' '));
    }

    private function dump() {
	exec("mysqldump -udumper -pdumper --skip-opt {$this->dump_db_name} | head -n -2 | {$this->sed} > {$this->tmpdump}");
    }

    private function hash() {
	$md5sum = exec("md5sum {$this->tmpdump}");
	$this->clean($md5sum);
	return $md5sum;
    }

    private function store($md5sum, $dir_to_store) {
	if (!strcmp($md5sum, $this->prev_md5sum)) return;
	if ($this->query != 'init')
	    fwrite($this->whandle, $this->query);
	fwrite($this->whandle, $md5sum . "\n");
	exec("mv {$this->tmpdump} {$dir_to_store}{$md5sum}");
	$this->prev_md5sum = $md5sum;
    }

    private function dump_hash_and_store() {
	$this->dump();
	$this->store($this->hash(), $this->reference_dump_dir);
    }

    private function process_line($line, $start_time, $end_time) {
	$pos_second_blank = strpos($line, ' ', strpos($line, ' ')+1);
	$logtime = strtotime(substr($line, 0, $pos_second_blank));
	if ($logtime >= $start_time && 
	    $logtime <= $end_time) {
	    $this->query = substr($line, $pos_second_blank+1);
	    $this->db->Execute($this->query);
	    $this->db->free_next_results();
	    $this->dump_hash_and_store();
	}
    }

    public function create_annotated_log($start_time, $end_time) {
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
	
	while (($line = fgets($rhandle)) !== false) {
	    $this->process_line($line, $start_time, $end_time);
	}
    }

}

?>