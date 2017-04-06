<?php 
if (!defined('__ROOT__')) {
    define('DS', DIRECTORY_SEPARATOR);
    define('__ROOT__', dirname(dirname(dirname(__FILE__))).DS); 
}

require_once("header.inc.version.php"); // To obtain: $aixada_vesion_lastDate
require_once(__ROOT__ . 'php'.DS.'inc'.DS.'cookie.inc.php');
//require_once(__ROOT__ . 'php'.DS.'lib'.DS.'exceptions.php'); Is required by 'cookie.inc.php'
require_once(__ROOT__ . 'local_config'.DS.'config.php');
require_once(__ROOT__ . 'php'.DS.'utilities'.DS.'general.php');

//$dev = configuration_vars::get_instance()->development;
$language = get_session_language(); 
$default_theme = get_session_theme();

require_once(__ROOT__ . 'local_config'.DS.'lang'.DS. get_session_language() . '.php');

// To declare common JavaScript sources used by a Aixada page.
function aixada_js_version() {
    global $aixada_vesion_lastDate;
    return $aixada_vesion_lastDate;
}
function aixada_js_src($useMenus = true) {
    global $aixada_vesion_lastDate;
    $src = "<!-- aixada_js_src -->";
    if ($useMenus) {
        $src .= "
            <script src=\"js/fgmenu/fg.menu.js?v={$aixada_vesion_lastDate}\"></script>
            <script src=\"js/aixadautilities/jquery.aixadaMenu.js?v={$aixada_vesion_lastDate}\"></script>";
    }
    $src .= "
        <script src=\"js/aixadautilities/jquery.aixadaXML2HTML.js?v={$aixada_vesion_lastDate}\"></script>
        <script src=\"js/aixadautilities/jquery.aixadaUtilities.js?v={$aixada_vesion_lastDate}\"></script>";
    if (get_config('use_ajaxQueue')) {
        $src .= "
            <script src=\"js/jquery-ajaxQueue/jQuery.ajaxQueue.js?v={$aixada_vesion_lastDate}\"></script>";
    } else {
        $src .= "
            <script src=\"js/jquery-ajaxQueue/jQuery.ajaxQueueNo.js?v={$aixada_vesion_lastDate}\"></script>";
    }
    return $src . "\n";
}
