<?php

define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(__FILE__))).DS); 

require_once(__ROOT__ . "local_config/config.php");
require_once(__ROOT__ . "php/inc/database.php");
require_once(__ROOT__ . "php/utilities/general.php");

if (!isset($_SESSION)) {
    session_start();
 }

DBWrap::get_instance()->debug = true;

function get_deactivated_roles($member_id)
{

}

try{
  $op_id = $_SESSION['userdata']['uf_id'];
  $user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : '';


  switch($_REQUEST['oper']) {

  case 'listUsers':
      printXML(stored_query_XML_fields('get_users'));
      exit;

  case 'getActivatedRoles':
      printXML(stored_query_XML_fields('get_active_roles', $user_id));
      exit;

  case 'getDeactivatedRoles':
      $rs = do_stored_query('get_active_roles', $user_id);
      $roles = array_keys(configuration_vars::get_instance()->forbidden_pages);
      $active_roles = array();
      while ($row = $rs->fetch_array()) {
          $active_roles[] = $row[0];
      }
      $inactive_roles = array_diff($roles, $active_roles);
      $XML = '<rowset>';
      foreach ($inactive_roles as $role) {
          $XML .= "<row><role>{$role}</role></row>";
      }
      printXML($XML . '</rowset>');
      exit;

  case 'getActivatedUsers':
      printXML(stored_query_XML_fields('get_active_users_for_role', $_REQUEST['role']));
      exit;

  case 'getDeactivatedUsers':
      printXML(stored_query_XML_fields('get_inactive_users_for_role', $_REQUEST['role']));
      exit;
      
  case 'activateRoles':
      $sql =  'delete from aixada_user_role where user_id=' . $user_id . '; ';
      $sql .= 'insert into aixada_user_role values ';
      $first = true;
      foreach (explode(',', $_REQUEST['role_ids']) as $role) {
          if ($first) {
              $first = false;
          } else {
              $sql .= ', ';
          }
          $sql .= '(' . $user_id . ",'" . $role . "')";
      }
      $sql .= ';';
      $db = DBWrap::get_instance();
      $db->MultiExecute($sql);
      exit;

  case 'activateUsers':
      $role = $_REQUEST['role'];
      $sql =  "delete from aixada_user_role where role='$role'; ";
      $sql .= 'insert into aixada_user_role values ';
      $first = true;
      foreach (explode(',', $_REQUEST['user_ids']) as $user_id) {
          if ($first) {
              $first = false;
          } else {
              $sql .= ', ';
          }
          $sql .= '(' . $user_id . ",'" . $role . "')";
      }
      $sql .= ';';
      $db = DBWrap::get_instance();
      $db->MultiExecute($sql);      
      exit;

  default:
    throw new Exception("ctrlActivateProducts: variable oper not recognized in query");
    
  }
} 

catch(Exception $e) {
  header('HTTP/1.0 401 ' . $e->getMessage());
  die ($e->getMessage());
}  


?>