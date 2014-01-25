<?php

require_once 'php/lib/table_manager.php';
require_once 'php/inc/database.php';
require_once 'php/utilities/general.php';
require_once 'php/utilities/tables.php';

function partial_dump($from_date, $to_date, $table_key_pairs)
{
    $fill_queries = array();

    $af_tablenames = array();
    $af_names = array();
    $af_aliases = array();
    $af_join_clauses = array();
    $af_after_which_field = array();

    foreach ($table_key_pairs as $tkpair) {
	list ($table, $date_key) = $tkpair;
	echo "processing table " . $table . "\n";
	$fkm = new foreign_key_manager($table);
	$foreign_key_info = $fkm->foreign_key_info();
	echo "foreign_key_info:\n";
	var_dump($foreign_key_info);
	foreach ($foreign_key_info as $key) {
	    echo "field: $foreign_key\n";
	    list ($ftable_and_field, $ftable_id, $ftable_name) = get_substituted_names($table, array($key), $foreign_keys);
	    foreach ($ftable_name as $t) {
		$fill_queries[$t][] = <<<EOD
select distinct {$t}.* 
from {$table}
left join {$t}
on {$table}.{$key}={$t}.{$foreign_keys[$key][1]}
where {$table}.{$date_key} between '{$from_date}' and '{$to_date}'
order by {$t}.{$keys[$key][1]};
EOD;
	    }
	}
    }    
    var_dump($fill_queries);
}

partial_dump('2014-01-01', '2014-02-01', 
	     array(
		   ['aixada_cart', 'date_for_shop'],
		   )
	     );
?>