<?php

require_once('php/inc/database.php');
require_once('local_config/config.php');




/*
function get_active_ufs_or_account()
{
    global $Text;
    $res = stored_query_XML('get_active_ufs', 'ufs', 'name');
    $mant = '<ufs><row><id f="id">-2</id><name f="name"><![CDATA['
        . $Text['consum_account'] 
        . ']]></name></row>';
    $ans = substr_replace($res, $mant, strpos($res, '<ufs>'), 5); 
    return $ans;
}*/


	
?>