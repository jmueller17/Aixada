<?php

define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(__FILE__))).DS); 


require_once(__ROOT__ . "local_config/config.php");
require_once(__ROOT__ . "php/inc/database.php");
require_once(__ROOT__ . "php/inc/authentication.inc.php");
require_once(__ROOT__ . "php/utilities/general.php");
require_once(__ROOT__ . "php/lib/exceptions.php");
require_once(__ROOT__ . 'php/inc/cookie.inc.php');

if (!isset($_SESSION)) {
    session_start();
}

require_once(__ROOT__ . 'FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start(); // Starts FirePHP output buffering
$firephp = FirePHP::getInstance(true);

DBWrap::get_instance()->debug = true;



try{
    $firephp->log($_REQUEST, 'REQUEST');

  switch ($_REQUEST['oper']) {
	
	  case 'logout':
	      try {
		  global $firephp;
	          $cookie=new Cookie();
		  $firephp->log($cookie, 'Login.php');
	          $cookie->logout();
		  $firephp->log($cookie, 'Login.php logged out');
		  $h = 'Location:' . __ROOT__ . 'login.php';
		  $firephp->log($h);
		  //		  header($h);
		  //	          exit;
	      } 
	      catch (AuthException $e) {
		  header('Location:' . __ROOT__ . 'login.php');
	          //echo "Already logged out"; 
	      }
	      exit;
	
	  case 'login':
	      $uri = get_param('originating_uri',''); 
	      if (!$uri) {
	          $uri = 'index.php';
	      }
	      
	      try {
	          $auth = new Authentication();
	          list($user_id, 
	               $login, 
	               $uf_id, 
	               $member_id, 
	               $provider_id, 
	               $roles, 
	               $current_role, 
	               $current_language_key, 
	               $theme) 
	              = $auth->check_credentials(get_param('login'), crypt(get_param('password'), "ax"));
	      
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
	                               $current_language_key,
	                               false,
	                               $theme);

	          $cookie->set();
		  $firephp->log($cookie, 'cookie in Login');
	      }	catch (AuthException $e) {
	          header("HTTP/1.1 401 Unauthorized " . $e->getMessage());
	          die($e->getMessage());
	      }	
	      print $cookie->package();
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