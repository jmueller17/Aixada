<?php

/*$slash = explode('/', getenv('SCRIPT_NAME'));
if (isset($slash[1])) {
    $app = getenv('DOCUMENT_ROOT') . '/' . $slash[1] . '/';
} else { // this happens when called by make
    $app = '';
}*/

require_once(__ROOT__ . 'php/inc/database.php');
require_once(__ROOT__ . 'local_config/config.php');

function do_list_all ($tm, $page, $limit, $sidx, $sord, $options = array()) {
  list($rs, $total_pages)
    = $tm->list_all(array('filter' => (isset($options['filter']) ? 
				      $options['filter'] : ''), 
			 'fields' => (isset($options['fields']) ?
				      $options['fields'] : array('*')),
			 'use_distinct' => (isset($options['use_distinct']) ?
					    $options['use_distinct'] : false),
			 'order_by' => $sidx,
			 'order_sense' => $sord,
			 'page' => $page,
			 'limit' => $limit)); 
  $strXML = $tm->rowset_to_jqGrid_XML($rs, $page, $limit, $total_pages); 
  return $strXML;
}

function is_editable ($field) {
  if($field == 'id'
     or $field == 'created' 
     or $field == 'ts' 
     or $field == 'balance')
    return 'false';
  else return 'true';  
}

function get_names ($tm)
{
    global $Text;
    list ($substituted_name, $substituted_alias, $table_alias) = 
        get_substituted_names($tm->get_table_name(), array_keys($tm->get_table_cols()), $tm->get_keys());
    $keys = $tm->get_keys();
    $col_names = '[';
    foreach ($tm->get_table_cols() as $field => $col_class) {
        $the_name = isset($substituted_alias[$field]) ? $substituted_alias[$field] : $field;
        //        $firephp->log($field . '->' . $the_name);
        $the_text = (isset($Text[$the_name]) ? $Text[$the_name] : "TRANSLATION($the_name)");
        $the_text = str_replace("'", "_", $the_text);
        $col_names 
            .= "'" 
            . $the_text
            . "',";
    }
    $col_names = rtrim($col_names, ",");
    $col_names .= ']';
    return $col_names;
}

function get_model ($tm)
{
    global $Text;
    $show_array = array('id', 'name', 'provider', 'description', 
                        'active', 'mentor_uf', 'phone1', 'unit',
                        'responsible_uf_id', 'unit_price',
                        'orderable_type_id', 'unit_measure_order_id', 
                        'unit_measure_shop_id', 'stock_actual', 'stock_min',
			'percent');
    $keys = $tm->get_keys();
    list ($substituted_name, $substituted_alias, $table_alias) = 
        get_substituted_names($tm->get_table_name(), array_keys($tm->get_table_cols()), $tm->get_keys());
    $col_model = '[';
    foreach ($tm->get_table_cols() as $field => $col_class) {
        $max_length = $col_class->get_max_length();
        $width = (($max_length>0 && !$tm->is_foreign_key($field)) ? $max_length : 300);
        if (in_array($width, array(4,10,11))) // tinyint or float(10,2) or int
            $width = 150;
        $field_name = (isset($substituted_alias[$field]) ? 
                       $substituted_alias[$field] : $field);
        $the_text = (isset($Text[$field]) ? $Text[$field] : "TRANSLATION($field)");
        $the_text = str_replace("'", "_", $the_text);
        $col_model 
            .= "{name:'" . $field
            . "',index:'" . $field_name 
            . "',label:'" . $the_text 
            . "',width:'" . $width
            . "',xmlmap:'" . $field_name
            . "',editable:" . is_editable($field)
            . ",hidden:" . (in_array($field, $show_array) ? 'false' : 'true')
        . ",editrules:{edithidden:true,searchhidden:true}";
   if ($tm->is_foreign_key($field)) {
      $col_model 
	.= ",edittype:'select',editoptions:{"
	. "dataUrl:'php/ctrl/SmallQ.php?oper=getFieldOptions&table="
	. $keys[$field][0] 
	. '&field1=' . $keys[$field][1] 
	. '&field2=' . $keys[$field][2] 
	. "'}";
    } else if (in_array($field, array('active', 'participant'))) {
		$col_model .= ",edittype:'checkbox',editoptions:{value:'1:0'}";	
	} else if ($field == 'password') {
		$col_model .= ",edittype:'password'";	
	}
    $col_model .= '},';
  }
  return rtrim($col_model, ",") . ']';
}

function get_active_field_names ($tm)
{
  $active_fields = '[';
  foreach ($tm->get_table_cols() as $field => $col_class) {
    if ($tm->is_foreign_key($field)) {
      $active_fields .= $field . ',';
    }
  }
  $active_fields = rtrim($active_fields, ',');
  $active_fields .= ']';
  return $active_fields;
}

function get_field_options ($tm, $field)
{
  $editvalues = ''; 
  foreach ($tm->get_key_cache($field) as $val => $desc)
    $editvalues .= $val . ':' . $desc . ';';
  $editvalues = rtrim($col_model, ";");
  return $editvalues;
}

?>