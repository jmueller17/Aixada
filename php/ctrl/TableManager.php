<?php

define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(__FILE__))).DS); 

require_once(__ROOT__ . "local_config/config.php");
require_once(__ROOT__ . "php/inc/database.php");
require_once(__ROOT__ . "php/utilities/general.php");

if (!isset($_SESSION)) {
    session_start();
 }

              
require_once(__ROOT__ . 'local_config/lang/' . get_session_language() . '.php');
require_once(__ROOT__ . "php/utilities/tables.php");

$use_session_cache = configuration_vars::get_instance()->use_session_cache;
$use_canned_responses = configuration_vars::get_instance()->use_canned_responses;



function get_columns_as_JSON()
{
  global $special_table;
  global $Text;
  $Text = array();
  require(__ROOT__ . 'canned_responses_' . get_session_language() . '.php');
  $ctm = new canned_table_manager();
  $table = $_REQUEST['table'];
  return '{"col_names":"' . $ctm->get_col_names_as_JSON($table)
    . '","col_model":"' . $ctm->get_col_model_as_JSON($table)
    . '","active_fields":"' . $ctm->get_active_fields_as_JSON($table)
    . '"}';
}

function get_options()
{    
  $options = array( 'filter' => '' );
  if (isset($_REQUEST['filter']) and substr($_REQUEST['filter'],0,8) != 'function') {
    $options['filter'] .= $_REQUEST['filter'];
  }
  
  switch ($_REQUEST['table']) {
  case 'aixada_account':
    if (strlen($options['filter'])>0) {
      $options['filter'] .= ' and ';
    }
    $uf_id = 1000 + (int)($_SESSION['userdata']['uf_id']);
    $options['filter'] .= "aixada_account.account_id=$uf_id";
    // we do this here so that the user can't hijack other users' account data 
    //by mangling the request in the browser
    break;

  case 'aixada_incident':
    switch ($options['filter']) {
    case 'today': 
      $options['filter'] = 'date(aixada_incident.ts)=date(sysdate())';
      break;
    case 'this_month':
      $options['filter'] = 'date(aixada_incident.ts)>=date_add(sysdate(), interval -32 day)';
      break;
    case 'all':
      $options['filter'] = '';
      break;
    default:
      throw new Exception('Filter option not supported in aixada_incident');
    }
    break;
  }
  if (isset($_REQUEST['fields'])) {
    $options['fields'] = str_replace('"', '', $_REQUEST['fields']);
  }

  return $options;
}

function get_list_all_XML()
{
  $req_page = (isset($_REQUEST['page']) ? $_REQUEST['page'] : '');
  $req_rows = (isset($_REQUEST['rows']) ? $_REQUEST['rows'] : '');
  $req_sidx = (isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : '');
  $req_sord = (isset($_REQUEST['sord']) ? $_REQUEST['sord'] : '');

  if ($_REQUEST['table'] == 'aixada_account' and
      !isset($_REQUEST['sidx'])) {
    $req_sidx = 'ts';
  }
  if ($_REQUEST['table'] == 'aixada_order_cart') {
    $count_querySQL = 
      'select count(distinct order_cart_id) from aixada_order_item where ts_validated=0';
    $real_querySQL = 
      'select distinct aixada_order_cart.* from aixada_order_cart left join aixada_order_item on aixada_order_cart.id = aixada_order_item.order_cart_id where aixada_order_item.ts_validated=0';
    $page = $req_page;
    $limit = $req_rows;
    list($rs, $total_entries, $total_pages) = 
      DBWrap::get_instance()->canned_select($count_querySQL, $real_querySQL, $page, $limit);
    $of = new output_formatter();
    return $of->rowset_to_jqGrid_XML($rs, $total_entries, $page, $limit, $total_pages); 
  }

  $options = get_options();
  $db = DBWrap::get_instance();
  $filter_str = $options['filter'];
  $strSQL = 'SELECT COUNT(*) AS count FROM :1';
  if ($filter_str != '') {
    $strSQL .= ' WHERE ' . $filter_str;
  }
  
  $row = $db->Execute($strSQL, $_REQUEST['table'])->fetch_array();
  $total_entries = $row[0];
  list($start, $total_pages) = $db->calculate_page_limits($total_entries, $req_page, $req_rows); 
  $rs = do_stored_query($_REQUEST['table'] . '_list_all_query', $req_sidx, $req_sord, $start, $req_rows, $filter_str);
  $of = new output_formatter();
  return $of->rowset_to_jqgrid_XML($rs, $total_entries, $req_page, $req_rows, $total_pages);
}


function post_edit_hook($request) 
{
    switch ($request['table']) {
    case 'aixada_product': 
	$db = DBWrap::get_instance();
	$row = $db->Execute("select current_price from aixada_price where product_id=:1", $request['id'])->fetch_array();
	if ($row[0] != $request['unit_price']) {
	    $db->Execute("insert into aixada_price (product_id, current_price, operator_id) values (:1, :2, :3);", $request['id'], $request['unit_price'], $_SESSION['userdata']['user_id']);
	}
	break;
    default:
	break;
    }
}

function post_create_hook($index, $request) 
{
    switch ($request['table']) {
    case 'aixada_product': 
	DBWrap::get_instance()->Execute("insert into aixada_price (product_id, current_price, operator_id) values (:1, :2, :3);", $index, $request['unit_price'], $_SESSION['userdata']['user_id']);
	break;
    default:
	break;
    }
}

// code starts here


try{
  $special_table = ($_REQUEST['table'] == "aixada_user");

  if (!isset($_REQUEST['oper']))
    throw new Exception("ctrlTableManager: variable oper not set in query");

  switch($_REQUEST['oper']) {
  case 'getColumnsAsJSON':
      echo get_columns_as_JSON();
      exit;

  case 'listAll':
    printXML(get_list_all_XML());
    exit;

  case 'get_by_key':
    printXML(query_XML("select * from {$_REQUEST['table']} where :1=:2q", 'rowset', 'row', $_REQUEST['key'], $_REQUEST['val']));
    exit;

  case 'edit':
    DBWrap::get_instance()->Update($_REQUEST);
    post_edit_hook($_REQUEST);
    echo '1';
    exit;
    
  case 'add':
    $db = DBWrap::get_instance();
    $db->Insert($_REQUEST);
    $index = $db->last_insert_id();
    post_create_hook($index, $_REQUEST);
    echo $index;
    exit;
    
  case 'del':
      DBWrap::get_instance()->Delete($_REQUEST['table'], $_REQUEST['id']);
      echo '1';
      exit;
  }

  require_once(__ROOT__ . 'php/lib/table_manager.php');
  if (!$special_table)
    $tm = new table_manager($_REQUEST['table'], 
			    configuration_vars::get_instance()->use_session_cache);

  switch ($_REQUEST['oper']) {
  case 'get_by_id':
    $id = $_REQUEST['id'];  // FIXME
    $rs = $tm->get_by_id($id);
    $strXML = $tm->rowset_to_jqGrid_XML($rs); 
    
    printXML($strXML);
    break;

    
  case 'get_empty':
    $strXML = $tm->get_empty();
    printXML($strXML);
    break;
        
  }
} 

catch(Exception $e) {
  header('HTTP/1.0 401 ' . $e->getMessage());
  die($e->getMessage());
}  


?>