<?php 
define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(__FILE__))).DS);
require_once(__ROOT__ . "/php/inc/header.inc.base.php");

$tpl_print_orders = configuration_vars::get_instance()->print_order_template;
$tpl_print_myorders = configuration_vars::get_instance()->print_my_orders_template;
$tpl_print_bill = configuration_vars::get_instance()->print_bill_template;
$tpl_print_incidents = configuration_vars::get_instance()->print_incidents_template;

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
