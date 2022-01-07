<?php

define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(__FILE__))).DS); 


require_once(__ROOT__ . "local_config/config.php");
require_once(__ROOT__ . "php/inc/database.php");
require_once(__ROOT__ . "php/inc/authentication.inc.php");
require_once(__ROOT__ . "php/utilities/general.php");
require_once(__ROOT__ . "php/lib/exceptions.php");

ob_start(); // Probably only needed for FirePHP(no longer used)

DBWrap::get_instance()->debug = true;



try{
  switch ($_REQUEST['oper']) {
	
	  case 'logout':
	      try {
	        logout_session();
		  	$h = 'Location:' . __ROOT__ . 'login.php';
	      } 
	      catch (AuthException $e) {
	        die($e->getMessage());
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
	               $theme) = $auth->check_credentials(get_param('login'), get_param('password'));
              /* FIXME
                    There a security issues here: 'login' and 'password' are posted unencrypted, and so is visible to everyone!
                    Even encrypting the username/password is no solution, because anyone who intercepts the communication
                    can just send the encrypted text without knowing what it decrypts to, but can log in anyways.
                    The solution could be to implement an SSL protocol.
              */
	      	  
	          $langs = existing_languages();
	          create_session( 
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
	                               $theme);
	      }	catch (AuthException $e) {
		  	header("HTTP/1.1 401 Unauthorized " . $e->getMessage());
	        die($e->getMessage());
	      }	
	      exit; 
	      	
	  default:
	    throw new Exception("ctrl/Login: operation {$_REQUEST['oper']} not supported");
	    
  }

} 

catch(Exception $e) {
   header('HTTP/1.0 419 ' . $e->getMessage());
   die($e->getMessage());
}  


?>