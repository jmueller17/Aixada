<?php

require_once("utilities.php");
//require_once('FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start(); // Starts FirePHP output buffering
//$firephp = FirePHP::getInstance(true);

require_once('inc/cookie.inc.php');

if (!isset($_SESSION)) {
    session_start();
 }

try {
    $cookie = new Cookie();
    //    $firephp->log($cookie, 'cookie');
    $cookie->validate();
    //   $firephp->log(true, 'validated');
    $uri = (isset($_REQUEST['originating_uri']) ? $_REQUEST['originating_uri'] : 'index.php');
}   
catch (AuthException $e) {
    header('HTTP/1.0 401 ' . $e->getMessage());
    die($e->getMessage());
}

try {
    if (isset($_REQUEST['change_role_to'])) {
        $new_role = $_REQUEST['change_role_to'];
        $fp = configuration_vars::get_instance()->forbidden_pages;
        if (!$uri || 
            (isset($_SESSION['userdata']) && isset($fp[$new_role]))) {
            $forbidden = false;
            foreach($fp[$new_role] as $page) {
                if (strpos($uri, $page) !== false) {
                    $forbidden = true;
                    break;
                }
            }
            if ($forbidden) 
                $uri = 'index.php';
        }
        $cookie->change_role($new_role);
        /*
             $firephp->log($uri, 'uri');
             $firephp->log($new_role, 'role');
             $firephp->log($_SESSION, 'session');
             $firephp->log($_SERVER, 'server');
        */
        printXML('<navigation>' . $uri . '</navigation>');
        exit;
    }
    
    if (isset($_REQUEST['change_lang_to'])) {
        $new_lang = $_REQUEST['change_lang_to'];
        $cookie->change_language($new_lang);
        printXML('<navigation>' . $uri . '</navigation>');
        exit;        
    }
}

catch(Exception $e) {
  header('HTTP/1.0 401 ' . $e->getMessage());
  die($e->getMessage());
}  


?>