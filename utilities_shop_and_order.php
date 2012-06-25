<?php

require_once('inc/database.php');
require_once('local_config/config.php');





function get_active_ufs_or_account()
{
    global $Text;
    $res = stored_query_XML('get_active_ufs', 'ufs', 'name');
    $mant = '<ufs><row><id f="id">-2</id><name f="name"><![CDATA['
        . $Text['consum_account'] 
        . ']]></name></row>';
    $ans = substr_replace($res, $mant, strpos($res, '<ufs>'), 5); 
    return $ans;
}

function get_negative_accounts()
{
    $cache = new QueryCache('negative_accounts');
    if ($cache->exists())
        return $cache->read();

    $strXML = '<negative_accounts>';
    $rs = do_stored_query('negative_accounts');
    while ($row = $rs->fetch_assoc()) {
        $strXML .= '<account>';
        foreach ($row as $field => $value) {
            $strXML .= "<{$field}><![CDATA[{$value}]]></{$field}>";
        }
        $strXML .= '</account>';
    }
    $strXML .= '</negative_accounts>';
    $cache->write($strXML);
    return $strXML;
}

	
?>