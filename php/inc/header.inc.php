<?php 

define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(__FILE__))).DS); 

require_once(__ROOT__ . 'php'.DS.'inc'.DS.'cookie.inc.php');
require_once(__ROOT__ . 'local_config'.DS.'config.php');
require_once(__ROOT__ . 'php'.DS.'utilities'.DS.'general.php');


$cv = configuration_vars::get_instance(); 

$default_theme = get_session_theme();

$tpl_print_orders = $cv->print_order_template;
$tpl_print_myorders = $cv->print_my_orders_template;
$tpl_print_bill = $cv->print_bill_template;
$tpl_print_incidents = $cv->print_incidents_template;


require_once(__ROOT__ . 'local_config'.DS.'lang'.DS. get_session_language() . '.php');
$language = get_session_language(); 


require_once(__ROOT__ . 'php'.DS.'lib'.DS.'menu.php');

//should be deleted in the end, and globally set. 
$_SESSION['dev'] = configuration_vars::get_instance()->development;

   try {
       $cookie = new Cookie();
       $cookie->validate();
       if (isset($_SESSION['userdata']) 
	   and isset($_SESSION['userdata']['current_role']) 
	   and $_SESSION['userdata']['current_role'] !== false) {
	   $fp = configuration_vars::get_instance()->forbidden_pages;
	   $uri = $_SERVER['REQUEST_URI'];
	   $role = $_SESSION['userdata']['current_role'];
	   $forbidden = false;
	   foreach($fp[$role] as $page) {
	       if (strpos($uri, $page) !== false) {
		   $forbidden = true;
		   break;
	       }
	   }
	   if ($forbidden) {
	       /* $firephp->log('forbidden'); */
	       /* $firephp->log($uri, 'uri'); */
	       /* $firephp->log($role, 'role'); */
	       /* $firephp->log($_SESSION, 'session'); */
	       /* $firephp->log($_SERVER, 'server'); */
	       header("Location: index.php");
	   }
     }
     
   }   
   catch (AuthException $e) {
       //       var_dump($_COOKIE);
     echo("caught AuthException: $e");
     header("Location: login.php?originating_uri=".$_SERVER['REQUEST_URI']);
     exit;
   }
   

?>