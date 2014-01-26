<?php

class LogManager {

    private $dump_db_name;
    private $db;

    public function __construct($dump_db_name) {
	$this->dump_db_name = $dump_db_name;
	$this->db = DBWrap::get_instance('aixada_dump', 
					 'mysql',
					 'localhost',
					 'dumper',
					 'dumper');
    }

    public function create_initial_log_of_modifying_queries() {
	exec("egrep -iv -f testing/util/non_modifying_query_patterns.for_grep aixada.log > initial_log_of_modifying_queries.log");
    }

    public function create_annotated_log() {
	$rhandle = @fopen('testing/logs/initial_log_of_modifying_queries.log', 'r');
	if (!$rhandle) {
	    echo "Could not open initial_log_of_modifying_queries.log for reading\n";
	    exit();
	}

	$whandle = @fopen('testing/annotated_original.log', 'w');
	if (!$whandle) {
	    echo "Could not open annotated_original.log for writing\n";
	    exit();
	}

	while (($line = fgets($rhandle)) !== false) {
	    $db->Execute($line);
	    $db->free_next_results();
	    exec("mysqldump -udumper -pdumper --skip-opt aixada_dump > testing/dumps/new_dump.sql");
	    $md5sum = exec("head -n -2 testing/dumps/new_dump.sql > /tmp/x; md5sum /tmp/x");
	    fwrite($whandle, $line . $md5sum . "\n");
	}
    }

}

?>