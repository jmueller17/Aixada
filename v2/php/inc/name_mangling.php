<?php
/** 
 * @package Aixada
 */ 

/** 
 * @package Aixada
 * @subpackage Naming_conventions
 */ 

  /**
   * There are various usages of names:
   * 1. The name of a table as it appears in the database definition.
   * 2. The alias of a table if it is referred to more than once as a foreign key.
   *    Examples include unit_measure_order_id and unit_measure_shop_id, which
   *    both refer to aixada_unit_measure.id.
   * 3. The name of a field as it appears in the database
   * 4. The alias of a field that is a foreign key. Generally, the _id suffix 
   *    should disappear
   * 5. The name of that field as it is presented to the user in the jqGrid.
   *    This should be a shorter name to go easy on column real estate.
   */

  /**
   * convert aixada_unit_measure, unit_measure_order_id to aixada_unit_measure_order
   * covert aixada_payment_method, payment_method_in_id to aixada_payment_method_in
   */
function get_table_alias($table_name, $field)
{
  $tmp = substr($field, 0, strlen($field)-3); // remove '_id'
  return $table_name . substr($tmp, strrpos($tmp, '_'));
}

/**
 * Strip trailing "_id".
 */
function get_fkey_alias($foreign_key)
{
  if (substr($foreign_key, -3) != '_id')
    throw new Exception('get_field_alias: ' . $foreign_key . ' doesnt end in _id');
  return substr($foreign_key, 0, strlen($foreign_key)-3);
}

function get_doubled_foreign_keys($fields, $foreign_keys)
{
  $doubled_foreign_keys = array();
  $key_count = array();
  foreach($fields as $field) {
    if (isset($foreign_keys[$field]) and $foreign_keys[$field] != '') {
      list ($ftable_name, $ftable_id, $ftable_desc) = $foreign_keys[$field];
      if (!isset($key_count[$ftable_name])) {
	$key_count[$ftable_name] = 1;
      } else {
	$key_count[$ftable_name]++;
	$doubled_foreign_keys[] = $ftable_name;
      }
    }
  }
  return $doubled_foreign_keys; 
}

function get_substituted_names($table_name, $fields, $foreign_keys)
{
  $substituted_name = array();
  $substituted_alias = array();
  $table_alias = array();

  // first look for tables that are referenced more than once as foreign keys
  $doubled_foreign_keys = get_doubled_foreign_keys($fields, $foreign_keys);

  // then assign names
  foreach($fields as $field) {
      if (!isset($foreign_keys[$field]) or $foreign_keys[$field] == '') {
	  $substituted_name[$field] = $table_name . '.' . $field;
      } else {
	  list ($ftable_name, $ftable_id, $ftable_desc) = $foreign_keys[$field];
	  $substituted_alias[$field] = get_fkey_alias($field);
	  $table_alias[$field] = (in_array($ftable_name, $doubled_foreign_keys) ? 
				  get_table_alias($ftable_name, $field) : $ftable_name);
	  $substituted_name[$field] = $table_alias[$field] . '.' . $ftable_desc;
      } 
  }
//    if ($table_name == 'aixada_account') {
//      var_dump($substituted_name);
//      var_dump($substituted_alias);
//      var_dump($table_alias);
//    }
  return array($substituted_name, $substituted_alias, $table_alias);
}
?>