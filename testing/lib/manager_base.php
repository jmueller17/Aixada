<?php

require_once('testing/lib/config.php');

class ManagerBase {
    protected function __construct() {
	global $testlogfilename, $testloghandle;
	if (($testloghandle = fopen($testlogfilename, 'w')) === false) {
	    echo "Could not open log file {$testlogfilename} for processing\n";
	    exit();
	}
    }
}
?>