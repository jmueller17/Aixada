<?php

//require_once('FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
//ob_start(); // Starts FirePHP output buffering

require_once("local_config/config.php");
require_once("inc/database.php");
require_once("inc/authentication.inc.php");
require_once("utilities.php");
require_once("lib/exceptions.php");
require_once 'inc/cookie.inc.php';

if (!isset($_SESSION)) {
    session_start();
 }

$language = ( (isset($_SESSION['userdata']['language']) and 
               $_SESSION['userdata']['language'] != '') ? 
              $_SESSION['userdata']['language'] : 
              configuration_vars::get_instance()->default_language );
require_once('local_config/lang/' . $language . '.php');


//$firephp = FirePHP::getInstance(true);


DBWrap::get_instance()->debug = true;

function extract_data($what) {
    return (isset($_REQUEST[$what]) ? $_REQUEST[$what] : '');
}


try{

    $active = isset($_REQUEST['active']);
    $adult = isset($_REQUEST['adult']);
    $address = extract_data('address');
    $city = extract_data('city');
    $color_scheme = extract_data('color_scheme');
    $email = extract_data('email');
    $id = extract_data('id');
    $pref_lang = (isset($_REQUEST['pref_lang']) ? $_REQUEST['pref_lang'] : $language);
    $login = extract_data('login');
    $member_id = extract_data('member_id');
    $mentor_uf = extract_data('mentor_uf');
    $name   = extract_data('name');
    $notes   = extract_data('notes');
    $participant = isset($_REQUEST['participant']);
    $login = (isset($_REQUEST['login']) ? $_REQUEST['login'] : '');
    $password = (isset($_REQUEST['password']) ? crypt($_REQUEST['password'], "ax") : '');
    $phone1 = extract_data('phone1');
    $phone2 = extract_data('phone2');
    $roles_select = extract_data('rolesSelect');
    $uf_id = extract_data('uf_id');
    $urls = extract_data('urls');
    $zip = extract_data('zip');

//     $firephp->log($_REQUEST['oper']);
//     exit();



  switch ($_REQUEST['oper']) {

  case 'logout':
      try {
          $cookie=new Cookie();
          $cookie->logout();
          //           $firephp->log($cookie);
          header("Location:login.php");
          exit;
      } 
      catch (AuthException $e) {
          //echo "Already logged out"; 
      }
      

  case 'login':
      $uri = isset($_REQUEST['originating_uri']) ? $_REQUEST['originating_uri'] : '';
      if (!$uri) {
          $uri = 'index.php';
      }
      
      try {
          //          global $firephp;
          $auth = new Authentication();
          list($user_id, 
               $login, 
               $uf_id, 
               $member_id, 
               $provider_id, 
               $roles, 
               $current_role, 
               $current_language_key) 
              = $auth->check_credentials($login, $password);
          //          $firephp->log($current_role, 'current_role');
          //          $firephp->log($login, 'login');
          $langs = existing_languages();
          $cookie = new Cookie(true, 
                               $user_id, 
                               $login, 
                               $uf_id, 
                               $member_id, 
                               $provider_id, 
                               $roles, 
                               $current_role, 
                               array_keys($langs), 
                               array_values($langs), 
                               $current_language_key);
          //          $firephp->log($cookie, 'cookie in ctrlogin');
          $cookie->set();
      }	catch (AuthException $e) {
          header("HTTP/1.1 401 Unauthorized " . $e->getMessage());
          die($e->getMessage());
      }	
      
      
  case 'usersWithoutUFs':
      printXML(stored_query_XML_fields('users_without_ufs'));
      exit;

  case 'registerUser':
      $email = $_REQUEST['email'];
      $XML_response = stored_query_XML_fields('find_user_by_login_or_email', $login, $email);
      if (strpos($XML_response, 'id') !== false) {
          throw new Exception("ctrlLogin: User $login or email $email already exists");
      }
      DBWrap::get_instance()->free_next_results();
      do_stored_query('register_user', $login, $password, $name, $pref_lang, $address, $zip, $city, $phone1, $phone2, $email, $urls, $notes);
      exit;

  default:
    throw new Exception("ctrlLogin: operation {$_REQUEST['oper']} not supported");
    
  }

} 

catch(Exception $e) {
   header('HTTP/1.0 419 ' . $e->getMessage());
   die($e->getMessage());
}  


?>