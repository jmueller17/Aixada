<?php

require_once 'php/lib/table_manager.php';
require_once 'php/inc/database.php';
require_once 'php/utilities/general.php';
require_once 'php/utilities/tables.php';

function fill_queries($from_date, $to_date, $table_key_pairs, $old_db, $new_db)
{
    $fill_queries = array();
    foreach ($table_key_pairs as $tkpair) {
	list ($table, $date_key) = $tkpair;
	$fkm = new foreign_key_manager($table);
	foreach ($fkm->foreign_key_info() as $key => $info) {
	    if (sizeof($info)==0) { continue; }
	    $ft = $info['fTable'];
	    $fk = $info['fIndex']; 
	    $fill_queries[$ft][] = <<<EOD
insert into {$new_db}.{$ft}
select distinct {$old_db}.{$ft}.* 
from {$old_db}.{$table}
left join {$old_db}.{$ft}
on {$old_db}.{$table}.{$key}={$old_db}.{$ft}.{$fk}
where {$old_db}.{$table}.{$date_key} between '{$from_date}' and '{$to_date}'
order by {$old_db}.{$ft}.{$fk};
EOD;
	    }
    }    
    return $fill_queries;
}

function partial_dump($from_date, $to_date, $table_key_pairs, $old_db, $new_db)
{
    $fq = fill_queries($from_date, $to_date, $table_key_pairs, $old_db, $new_db);
    var_dump($fq);
}

partial_dump('2014-01-01', '2014-02-01', 
	     array(
		   ['aixada_cart', 'date_for_shop'],
		   ),
	     "aixada",
	     "aixada_test"
	     );
?>