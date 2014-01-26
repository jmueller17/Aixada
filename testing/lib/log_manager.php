<?php
 
class LogManager {

    private $dump_db_name;
    private $dumpfile;
    private $bare_log_name;
    private $annotated_log_name;
    private $exclusion_patterns;
    private $db;

    public function __construct($dump_db_name, $dumpfile) {

	$logpath = 'testing/logs/';
	$utilpath = 'testing/util/';
	$dumppath = 'testing/dumps/';

	$this->dump_db_name = $dump_db_name;	
	$this->dumpfile = $dumppath . $dumpfile;
	$this->bare_log_name = $logpath . 'bare_log_of_modifying_queries.' 
	    . $dump_db_name . '.log';
	$this->annotated_log_name = $logpath . 'annotated_log_of_modifying_queries.' 
	    . $dump_db_name . '.log';
	$this->exclusion_patterns = $utilpath . 'non_modifying_query_patterns.for_grep';
	$this->db = DBWrap::get_instance($dump_db_name, 
					 'mysql',
					 'localhost',
					 'dumper',
					 'dumper');
    }

    public function create_bare_log_of_modifying_queries($original_log) {
	exec("egrep -iv -f {$this->exclusion_patterns} $original_log > {$this->bare_log_name}");
    }

    public function create_annotated_log($start_time, $end_time) {
	$rhandle = @fopen($this->bare_log_name, 'r');
	if (!$rhandle) {
	    echo "Could not open {$this->bare_log_name} for reading\n";
	    exit();
	}

	// create empty file
	$whandle = @fopen($this->annotated_log_name, 'w');
	if (!$whandle) {
	    echo "Could not open {$this->annotated_log_name} for writing\n";
	    exit();
	}

	// neutralize timestamps
	$sed = "sed 's/[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9] [0-9][0-9]:[0-9][0-9]:[0-9][0-9]/timestamp/g' ";

	// initial hash
	fwrite($whandle, exec("head -n -2 {$this->dumpfile} | {$sed} | md5sum") . "\n");

	while (($line = fgets($rhandle)) !== false) {
	    $pos_second_blank = strpos($line, ' ', strpos($line, ' ')+1);
	    $logtime = strtotime(substr($line, 0, $pos_second_blank));
	    if ($logtime >= $start_time && 
		$logtime <= $end_time) {
		$query = substr($line, $pos_second_blank+1);
		$this->db->Execute($query);
		$this->db->free_next_results();
		$md5sum = exec("mysqldump -udumper -pdumper --skip-opt {$this->dump_db_name} | head -n -2 | {$sed} | md5sum");
		fwrite($whandle, $query . $md5sum . "\n");
	    }
	}
    }

}

?>