<?php
define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(dirname(dirname(__FILE__))).DS); 

require_once(__ROOT__ . "local_config/config.php");
require_once(__ROOT__ . "php/inc/database.php");
require_once(__ROOT__ . "php/utilities/general.php");
require_once(__ROOT__ . 'local_config/lang/'.get_session_language() . '.php');

if (!isset($_SESSION)) {
    session_start();
}
/* 
  Operations
  ===========
*/
try {
    switch (get_param('table')) {
    case 'aixada_uf_active':
        echo get_form_select(
            'select uf.id, CONCAT(uf.id ,\' \', uf.name)'.
                ' from aixada_uf uf where uf.active=1'.
                ' order by uf.id',
            'sel_uf'
        );
        break;
    default:
        throw new Exception('Operation "_control.php?oper='.
            $_REQUEST['oper'].'&table='.get_param('table').
            '" not supported.');
    }
} catch(Exception $e) {
    header('HTTP/1.0 401 '.$e->getMessage());
    die($e->getMessage());
} 

/* 
  Utilities
  ===========
*/
function get_form_select($strSQL, $blank_text='', $field3='') {
    global $Text;
    $strXML = "<select>\n";
    $rs = DBWrap::get_instance()->Execute($strSQL);
    
    if ($blank_text != '') {
        $strXML .= '<option value="">'.
            (isset($Text[$blank_text]) ? $Text[$blank_text] : '(...)').
            "</option>\n";
    }
    while ($row = $rs->fetch_array()) {
        if ($field3 != ''){
            $strXML .= "<option value='{$row[0]}' addInfo='{$row[2]}'";
        } else {
            $strXML .= "<option value='{$row[0]}'";
        }
        $strXML .= '>'.
            (isset($Text[$row[1]]) ? $Text[$row[1]] : $row[1])."</option>\n";
    }
    return $strXML . "</select>\n";
}
?>