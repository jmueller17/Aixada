<?php

define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(__FILE__))).DS); 

require_once(__ROOT__ . "php/utilities/general.php");
require_once(__ROOT__ . 'php/inc/cookie.inc.php');

try {
    $cookie = new Cookie();
    $cookie->validate();
    $uri = (isset($_REQUEST['originating_uri']) ? $_REQUEST['originating_uri'] : 'index.php');
}   
catch (AuthException $e) {
    header('HTTP/1.0 401 ' . $e->getMessage());
    die($e->getMessage());
}

try {
    if (isset($_REQUEST['change_role_to'])) {
        $new_role = $_REQUEST['change_role_to'];
        $cookie->change_role($new_role);
        $fp = configuration_vars::get_instance()->forbidden_pages;
        if (!$uri || isset($fp[$new_role])) {
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
        printXML('<row><navigation>' . $uri . '</navigation></row>');
        exit;
    }
    
    if (isset($_REQUEST['change_lang_to'])) {
        $new_lang = $_REQUEST['change_lang_to'];
        $cookie->change_language($new_lang);
        printXML('<row><navigation>' . $uri . '</navigation></row>');
        exit;        
    }
}

catch(Exception $e) {
  header('HTTP/1.0 401 ' . $e->getMessage());
  die($e->getMessage());
}  


?>