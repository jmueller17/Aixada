<?php

require_once 'dump_manager.php';

$db = DBWrap::get_instance('aixada_dump', 
			   'mysql',
			   'localhost',
			   'dumper',
			   'dumper');

exec("cp testing/initial_dump.sql testing/old_dump.sql");

$rhandle = @fopen('testing/clean.log', 'r');
if (!$rhandle) {
    echo "Could not open clean.log\n";
    exit();
}

$whandle = @fopen('testing/test.log', 'w');
if (!$whandle) {
    echo "Could not open test.log\n";
    exit();
}

while (($line = fgets($rhandle)) !== false) {
    $db->Execute($line);
    $db->free_next_results();
    exec("mysqldump -udumper -pdumper --skip-opt aixada_dump > testing/new_dump.sql");
    $md5sum = exec("head -n -2 testing/old_dump.sql > /tmp/x; head -n -2 testing/new_dump.sql > /tmp/y; diff /tmp/x /tmp/y | md5sum");
    fwrite($whandle, $line . $md5sum . "\n");
}

?>