<?php

require_once 'php/lib/table_manager.php';
require_once 'php/inc/database.php';
require_once 'php/utilities/general.php';
require_once 'php/utilities/tables.php';

function partial_dump($from_date, $to_date, $tables)
{
    $fill_queries = array();

    $af_tablenames = array();
    $af_names = array();
    $af_aliases = array();
    $af_join_clauses = array();
    $af_after_which_field = array();

    foreach ($tables as $table) {
	echo "processing table " . $table . "\n";
	$fill_queries[$table] = array();
	$fkm = new foreign_key_manager($table);
	$tm = new table_manager($table);
	foreach (get_active_field_names($tm) as $field) {
	    echo "field: $field\n";
	    list ($ftable_and_field, $ftable_id, $ftable_name) = get_substituted_names($table, array($field), $fkm->get_keys());
	    foreach ($ftable_name as $t) {
		$ffkm = new foreign_key_manager($t);
		$fill_queries[$t][] = $ffkm->make_canned_list_all_query($af_tablenames, $af_names, $af_aliases, $af_join_clauses, $af_after_which_field) 
		    . ' where ' . $table . '.ts between ' . $from_date . ' and ' . $to_date . ';';
	    }
	}
    }    
    var_dump($fill_queries);
}

partial_dump('2014-01-01', '2014-02-01', array('aixada_shop_item'));
?>