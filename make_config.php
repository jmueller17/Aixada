<?php
$config_filename = 'local_config/config.php';
$tmp_filename = 'local_config/tmp.php';
$chandle = @fopen($config_filename, "r");
$thandle = @fopen($tmp_filename, "w");
$buffer = fgets($chandle, 4096);
while (strpos($buffer, 'this point on') === false and !feof($chandle)) {
    fwrite($thandle, $buffer);
    $buffer = fgets($chandle, 4096);
 }
fwrite($thandle, $buffer);
$buffer = fgets($chandle, 4096);
fclose($chandle);
fwrite($thandle, $buffer);

$qhandle = @fopen("sql/setup/queries_reading.php", "r");
if (!$qhandle) exit;
while (!feof($qhandle)) {
    $buffer = fgets($qhandle, 4096);
    fwrite($thandle, $buffer);
 }
fclose($qhandle);

$qhandle = @fopen("sql/setup/tables_modified_by.php", "r");
while (!feof($qhandle)) {
    $buffer = fgets($qhandle, 4096);
    fwrite($thandle, $buffer);
 }
fclose($qhandle);
fwrite($thandle, "}\n?>");
fclose($thandle);
rename($tmp_filename, $config_filename);
?>