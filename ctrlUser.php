<?php

//require_once('FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
//ob_start(); // Starts FirePHP output buffering

require_once("local_config/config.php");
require_once("inc/database.php");
require_once("utilities.php");

//$firephp = FirePHP::getInstance(true);

$use_session_cache = configuration_vars::get_instance()->use_session_cache;

// This controls if the table_manager objects are stored in $_SESSION or not.
// It looks like doing it cuts down considerably on execution time.

if (!isset($_SESSION)) {
    session_start();
 }

//$firephp->log($_SESSION, 'session');
DBWrap::get_instance()->debug = true;

function extract_data($what) {
    return (isset($_REQUEST[$what]) ? $_REQUEST[$what] : '');
}


try{
    $a = extract_data('active');
    $active = ($a == 'true' or $a == '1' ? 1 : 0);
    $adult = (isset($_REQUEST['adult']) ? true : false);
    $address = extract_data('address');
    $city = extract_data('city');
    $color_scheme = extract_data('color_scheme');
    $email = extract_data('email');
    $id = extract_data('id');
    $language = extract_data('language');
    $login = extract_data('login');
    $member_id = extract_data('member_id');
    $mentor_uf = extract_data('mentor_uf');
    $name   = extract_data('name');
    $notes   = extract_data('notes');
    $participant = (isset($_REQUEST['participant']) ? true : false);
    $password = extract_data('password');
    $old_password = extract_data('old_password');
    $new_password = extract_data('password');
    $phone1 = extract_data('phone1');
    $phone2 = extract_data('phone2');
    $roles_select = extract_data('rolesSelect');
    $uf_id = extract_data('uf_id');
    $urls = extract_data('urls');
    $zip = extract_data('zip');


  switch ($_REQUEST['oper']) {

  case 'createUF':
      $rs = do_stored_query('find_uf_by_name', $name);
      if ($rs->fetch_assoc()) {
          throw new Exception("An UF named {$name} already exists");
      }
      DBWrap::get_instance()->free_next_results();
      printXML(stored_query_XML_fields('create_uf', $name, $active, $mentor_uf));
      exit;

  case 'updateUF':
      printXML(stored_query_XML_fields('update_uf', $uf_id, $name, $active, $mentor_uf));
      exit;

  case 'createMember':
      printXML(stored_query_XML_fields('create_member', $login, $password, $name, $uf_id, $language, $color_scheme, $address, $zip, $city, $phone1, $phone2, $email, $active));
      exit;

  case 'updateMember':
      /* $firephp->log($active, 'active'); */
      /* $firephp->log($participant, 'participant'); */
      do_stored_query('update_member', $id, $name, $address, $zip, $city, $phone1, $phone2, $urls, $notes, $active, $participant, $adult);
      do_stored_query('update_user_email_language_login', $id, $email, $language, $login);
      echo('1');
      exit;

  case 'deactivateMember':
      do_stored_query('deactivate_member', $id);
      echo('1');
      exit;

  case 'changePassword':
      $user_id = $_SESSION['userdata']['user_id'];
      $rs = do_stored_query('check_password', $user_id, crypt($old_password, 'ax'));
      $row = $rs->fetch_assoc();
      if (!$row or $row['id'] != $user_id) {
          throw new Exception("Wrong username or password given");
      }
      DBWrap::get_instance()->free_next_results();      
      do_stored_query('update_password', $user_id, crypt($new_password, 'ax'));
      echo '1';
      exit;

  case 'changeOtherPassword':
      $user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : 0;
      if (!$user_id)
          throw new Exception("User id " . $user_id . ' not valid');
      if ($_SESSION['userdata']['current_role'] != 'Hacker Commission')
          throw new Exception("Only Hackers can do that!");
      do_stored_query('update_password', $user_id, crypt($new_password, "ax"));
      echo '1';
      exit;

  default:
    throw new Exception("ctrlUser: \"oper=" . $_REQUEST['oper'] . "\" not valid in query");
  }
} 

catch(Exception $e) {
  header('HTTP/1.0 401 ' . $e->getMessage());
  die($e->getMessage());
}  


?>