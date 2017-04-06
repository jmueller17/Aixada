<?php


require_once 'php/lib/table_manager.php';
require_once 'php/inc/database.php';
require_once 'php/utilities/general.php';
require_once 'php/utilities/tables.php';

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

function write_column_names($col_names)
{
    file_put_contents('col_names.php', serialize($col_names));
}

function make_canned_responses($language = 'en')
{
    $strPHP = <<<EOD
class canned_table_manager {

EOD;

$tables = array();
$col_names = array();
$col_names_raw = array();
$col_models = array();
$active_fields = array();
global $db;
global $Text;
$Text = '';
require 'local_config/lang/' . $language . '.php';


$rs = $db->Execute("SHOW TABLES LIKE 'aixada_%'");
while ($row = $rs->fetch_array()) {
    $current_table = $row[0];
    $tables[] = $current_table;
    $tm = new table_manager($current_table);
    $col_names     [$current_table] = get_names($tm);
    $col_names_raw [$current_table] = array_keys($tm->get_table_cols());
    $col_models    [$current_table] = get_model($tm);
    $active_fields [$current_table] = get_active_field_names($tm);
}
write_column_names($col_names_raw);
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
  $rs = $db->Execute("SHOW TABLES LIKE 'aixada_%'");
  while ($row = $rs->fetch_array()) {
    $tables[] = $row[0];
  }
  
  foreach ($tables as $table) {
    $query_name = $table . '_list_all_query';
    $fkm = new foreign_key_manager($table);
    $order_by_clause = (in_array($table, array('aixada_unit_measure', 
					       'aixada_iva_type',
					       'aixada_account', 
                 'aixada_stock_movement_type')) ? 
                        "' order by '" :
                        "' order by active desc, '");

    $af_tablenames = array();
    $af_names = array();
    $af_aliases = array();
    $af_join_clauses = array();
    $af_after_which_field = array();

    if ($table == 'aixada_member') {
	// af = "additional field"
	$af_tablenames[] = "aixada_user";
	$af_names[] = "email";
	$af_aliases[] = "email";
	$af_join_clauses[] = "left join aixada_user as aixada_user on aixada_user.member_id=aixada_member.id";
	$af_after_which_field[] = "aixada_member.name";
    }

    $strSQL .= <<<EOD
drop procedure if exists {$query_name}|
create procedure {$query_name} (in the_index char(50), in the_sense char(4), in the_start int, in the_limit int, in the_filter text)
begin
  set @q = "{$fkm->make_canned_list_all_query($af_tablenames, $af_names, $af_aliases, $af_join_clauses, $af_after_which_field)}";
  set @lim = ' ';				 
 if the_filter is not null and length(the_filter) > 0 then set @lim = ' where '; end if;
  set @lim = concat(@lim, the_filter, {$order_by_clause}, the_index, ' ', the_sense, ' limit ', the_start, ', ', the_limit);
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

/*
foreach (glob("local_config/lang/*.php") as $lang_file) {
    $lang = basename($lang_file, '.php');
    $handle = @fopen('canned_responses_' . $lang . '.php', "w");
    fwrite($handle, "<?php\n");
    fwrite($handle, make_canned_responses($lang));
    fclose($handle);
}
*/
make_canned_responses();

write_file('sql/queries/canned_queries.sql', make_canned_queries());

write_file(
    'php/inc/header.inc.version.php',
    "\$aixada_vesion_lastDate = '" . date('Ymd_His') . "';\n"
);
