<?php

$queries_reading = array();
$tables_modified_by = array();

function get_table_name($buffer, $keyword)
{
    $tmp = explode(' ', trim($buffer));
    $i=0;
    while(strpos($tmp[$i++], $keyword)===false and $i < count($tmp));
    if (!isset($tmp[$i]))
        return false;
        //        echo $buffer . ' : ' . $keyword . "\n";
    else
        return $tmp[$i];
}

foreach (glob('../queries/*.sql') as $queryfilename) {
    echo($queryfilename."\n");
    $handle = @fopen($queryfilename, "r");
    $table_names = array( 'edit' => array(), 'select' => array() );
    if ($handle) {
        while (!feof($handle)) {
            $buffer = fgets($handle, 4096);
            if (strpos($buffer, 'create procedure') !== FALSE) {
                $tmp = explode(' ', str_replace("(", " ", $buffer));
                $queryname = $tmp[2];
            } 
            if (strpos($buffer, 'select') !== FALSE or
                       strpos($buffer, 'SELECT') !== FALSE) {
                $querytype = 'select';
            } 
            $buffer = str_replace('insert into', 'insert_into', $buffer);
            $buffer = str_replace('replace into', 'replace_into', $buffer);
            $buffer = str_replace('delete from', 'delete_from', $buffer);
            foreach (array('update', 'insert_into', 'replace_into', 'delete_from') as $keyword) {
                if (strpos($buffer, $keyword . ' ') !== FALSE) {
                    $querytype = 'edit';
                    $t = get_table_name($buffer, $keyword);
                    if (strpos($t, 'aixada') !== false)
                        $table_names[$querytype][] = $t;
                }
            }
            if ((strpos($buffer, 'from') !== FALSE or
                 strpos($buffer, 'FROM') !== FALSE) and
                strpos($buffer, 'prepare') === FALSE) {
                $tmp = explode(' ', trim($buffer));
                if (isset($tmp[1]) and strlen($tmp[1]) > 3) {
                    $table_names[$querytype][] = $tmp[1];
                } else {
                    $buffer = fgets($handle, 4096);
                    $tmp = explode(' ', trim($buffer));
                    $table_names[$querytype][] = $tmp[0];
                }
            } 
            if (strpos($buffer, 'join') !== FALSE) {
                $buffer = substr($buffer, strpos($buffer, 'join') + 5);
                $tmp = explode(' ', trim($buffer));
                $table_names[$querytype][] = $tmp[0];
            } 
            if (strpos($buffer, 'end|') !== FALSE) {
                //                echo($queryname . ' ' . $querytype . ' [' . implode(';', $table_names['select']) . '::' . implode(';', $table_names['edit']) . "]\n");
                foreach ($table_names['select'] as $table) {
                    if (strpos($table, 'aixada') !== false and $queryname)
                        $queries_reading[trim($table, ';')][] = $queryname;
                }
                foreach ($table_names['edit'] as $table)
                    $tables_modified_by[$queryname][] = $table;
                $queryname = $querytype = '';
                $table_names = array( 'edit' => array(), 'select' => array() );
            }
        }
        fclose($handle);
    }
  }

foreach(array_keys($queries_reading) as $key) {
    $queries_reading[$key] = array_unique($queries_reading[$key]);
}
foreach(array_keys($tables_modified_by) as $key) {
    $tables_modified_by[$key] = array_unique($tables_modified_by[$key]);
}

$q = var_export($queries_reading, true);
$t = var_export($tables_modified_by, true);

$handle = @fopen("queries_reading.php", "w");
fwrite($handle, "\n  public \$queries_reading = " . $q . ';');
fclose($handle);

$handle = @fopen("tables_modified_by.php", "w");
fwrite($handle, "\n  public \$tables_modified_by = " . $t . ';');
fclose($handle);
?>