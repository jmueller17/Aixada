<?php

if (!file_exists('../../sql/aixada.sql')) {
    echo 'Could not open database definition sql/aixada.sql for reading\n';
    exit();
}

$db_def = file_get_contents('../../sql/aixada.sql');
$token = 'create table ';
$tables = array();
foreach (explode("\n", $db_def) as $line) {
    if (($p = strpos($line, $token)) === false) continue;
    $p += strlen($token);
    $table = substr($line, $p, strpos($line, ' ', $p) - $p);
    $tables[] = $table;
}

if (!file_exists('../../sql/setup/aixada_queries_all.sql')) {
    echo 'Could not open query definitions ../../sql/setup/aixada_queries_all for reading\n';
    exit();
}
$queries = file_get_contents('../../sql/setup/aixada_queries_all.sql');
$used_in = array();
$token = 'drop procedure if exists ';
$query = '';
foreach (explode("\n", $queries) as $line) {
    if (($p = strpos($line, $token)) !== false) {
	$p += strlen($token);
	$query = substr($line, $p, strpos($line, '|', $p) - $p);
	if (strpos($query, 'list_') !== false
	    || strpos($query, 'get_') !== false) 
	    continue;
	$used_in[$query] = array();
    }
    if ($query == '' 
	|| strpos($query, 'list_') !== false
	|| strpos($query, 'get_') !== false) 
	continue;
    foreach ($tables as $table) {
	if (strpos($line, $table) !== false) {
	    if (in_array($table, $used_in[$query])) 
		continue;
	    $used_in[$query][] = $table;
	    break;
	}
    }
}

file_put_contents('tables_used_in_queries.php', print_r($used_in, true));
?>