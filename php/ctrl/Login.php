<?php

$slash = explode('/', getenv('SCRIPT_NAME'));
$app = getenv('DOCUMENT_ROOT') . '/' . $slash[1] . '/';

require_once($app . "local_config/config.php");
require_once($app . "php/inc/database.php");
require_once($app . "php/inc/authentication.inc.php");
require_once($app . "php/utilities/general.php");
require_once($app . "php/lib/exceptions.php");
require_once($app . 'php/inc/cookie.inc.php');

if (!isset($_SESSION)) {
    session_start();
 }

 
DBWrap::get_instance()->debug = true;



try{

  switch ($_REQUEST['oper']) {
	
	  case 'logout':
	      try {
	          $cookie=new Cookie();
	          $cookie->logout();
	          header("Location:login.php");
	          exit;
	      } 
	      catch (AuthException $e) {
	          //echo "Already logged out"; 
	      }
	      
	
	  case 'login':
	      $uri = get_param('originating_uri',''); 
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
	      }	catch (AuthException $e) {
	          header("HTTP/1.1 401 Unauthorized " . $e->getMessage());
	          die($e->getMessage());
	      }	
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