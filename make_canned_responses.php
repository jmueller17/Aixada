<?php

require_once('FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start(); // Starts FirePHP output buffering

require_once 'lib/table_manager.php';
require_once 'inc/database.php';
require_once 'php/utilities/general.php';
require 'php/utilities/tables.php';

$db = DBWrap::get_instance();

function print_response($name, $response) 
{
  $strPHP = <<<EOD

  public function get_{$name}_as_JSON(\$table)
  {
    switch(\$table) {

EOD;
 foreach ($response as $table => $res) {
   $JSON = json_encode($res);
//    echo '\n\n' . $JSON;
//    echo '\n\n' . $JSON;
   $JSON = str_replace('\"', '"', $JSON);
   //   $JSON = str_replace('},', "},\n", $JSON);
//     $JSON = rtrim('"', $JSON);
//     $JSON = ltrim('"', $JSON);
   $JSON = str_replace('"', '\\\\\"', $JSON);
   $JSON = substr($JSON, strpos($JSON, '['));
   $JSON = substr($JSON, 0, strrpos($JSON, ']')+1);
   $strPHP .= <<<EOD
 case '$table':
     return "$JSON";


EOD;
 }
 $strPHP .= <<<EOD
    }
  }
EOD;
 return $strPHP;
}

function get_list_all_query ($tm)
{
  global $db;
  $strXML = do_list_all($tm, 1, 10, $tm->get_primary_key(), 'asc');
  return array ($db->next_to_last_query_SQL, 
		$db->last_query_SQL);
}

function write_file($filename, $content)
{
  if(!is_writeable($filename)) {
    echo "The file $filename is not writable";
    exit;
  }
  if (!$handle = fopen($filename, 'w')) {
    echo "Cannot open file ($filename)";
    exit;
  }
  $commentstr = <<<EOD
/* 
 * The contents of this file are generated automatically. 
 * Do not edit it, but instead run
 * php make_canned_responses.php
 */

EOD;
  if (strpos($filename, 'php')!==FALSE) {
    $commentstr = "<?php\n" . $commentstr;
  }
  if (fwrite($handle, $commentstr) === FALSE) {
    echo "Cannot write to file ($filename)";
    exit;
  }
  if (fwrite($handle, $content) === FALSE) {
    echo "Cannot write to file ($filename)";
    exit;
  }
  fclose($handle);
}

function make_canned_responses($language = 'en')
{
    $strPHP = <<<EOD
class canned_table_manager {

EOD;

$tables = array();
$col_names = array();
$col_models = array();
$active_fields = array();
global $db;
global $Text;
$Text = '';
require 'local_config/lang/' . $language . '.php';


$rs = $db->Execute('SHOW TABLES');
while ($row = $rs->fetch_array()) {
  $tables[] = $row[0];
  $tm = new table_manager($row[0]);
  $col_names     [$row[0]] = get_names($tm);
  $col_models    [$row[0]] = get_model($tm);
  $active_fields [$row[0]] = get_active_field_names($tm);
 }
$strPHP .= print_response('col_names', $col_names);
$strPHP .= print_response('col_model', $col_models);
$strPHP .= print_response('active_fields', $active_fields);

$strPHP .= <<<EOD

  public function get_list_all_queries(\$table, \$page, \$limit)
  {
    return array("SELECT COUNT(*) AS count FROM \$table",
		 "SELECT * FROM \$table ORDER BY active desc, id asc LIMIT \$page, \$limit");
  }
}
?>
EOD;

 return $strPHP;
}

function make_canned_queries()
{
  $strSQL = "delimiter |\n\n";

  global $db;
  $rs = $db->Execute('SHOW TABLES');
  while ($row = $rs->fetch_array()) {
    $tables[] = $row[0];
  }
  
  foreach ($tables as $table) {
    $query_name = $table . '_list_all_query';
    $fkm = new foreign_key_manager($table);
    $order_by_clause = ($table != 'aixada_unit_measure' ? 
                        "' order by active desc, '" :
                        "' order by '");
    $strSQL .= <<<EOD
drop procedure if exists {$query_name}|
create procedure {$query_name} (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter char(100))
begin
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, {$order_by_clause}, the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
  set @q = "{$fkm->make_canned_list_all_query()}";
  set @q = concat(@q, @lim);
  prepare st from @q;
  execute st;
  deallocate prepare st;
end|


EOD;

  }
  $strSQL .= "\ndelimiter ;\n";
  return $strSQL;
}

foreach (glob("local_config/lang/*.php") as $lang_file) {
    $lang = basename($lang_file, '.php');
    $handle = @fopen('canned_responses_' . $lang . '.php', "w");
    fwrite($handle, "<?php\n");
    fwrite($handle, make_canned_responses($lang));
    fclose($handle);
}

write_file('sql/queries/canned_queries.sql', make_canned_queries());


?>