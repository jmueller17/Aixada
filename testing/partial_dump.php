<?php

require_once 'testing/dump_manager.php';

function partial_dump($from_date, $to_date, $table_key_pairs, $dump_db_name, $db_name)
{
    $dbdm = new DBDumpManager($dump_db_name, $db_name);
    $dbdm->dump($from_date, $to_date, $table_key_pairs);
}

partial_dump('2014-01-01', '2014-02-01', 
	     array(
		   ['aixada_cart', 'date_for_shop'],
		   ),
	     'aixada_dump',
	     'aixada'
	     );
?>