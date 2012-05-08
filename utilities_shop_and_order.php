<?php

require_once('inc/database.php');
require_once('local_config/config.php');
//$firephp = FirePHP::getInstance(true);

//DBWrap::get_instance()->debug = true;


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



/**
 *  manage orders
 */

function activate_products($provider_id, $prod_list, $date)
{
    // delete _all_ products for $date from the same provider as the products in $prod_list
    $db = DBWrap::get_instance();
    $db->Execute(
		 'delete from aixada_product_orderable_for_date '
		 . 'where date_for_order=:1q '
		 . 'and product_id in '
		 . '   (select id from aixada_product where provider_id=:2q);',
		 $date, $provider_id);

    // if nothing is to be inserted, we're done
    if (!$prod_list) 
        return; 

    // else, insert the new products
    $is = 'insert into aixada_product_orderable_for_date (product_id, date_for_order) values ';
    $prod_array = explode(',', $prod_list);
    foreach ($prod_array as $id) {
        $is .= "($id,'$date'),";
    }
    $is = rtrim($is, ',') . ';';
    clear_cache("aixada_product_orderable_for_date");
    $db->Execute($is);
}

function arrived_products($provider_id, $prod_list, $date)
{
    $db = DBWrap::get_instance();
    $db->Execute(
      'delete i
         from aixada_shop_item i
         left join aixada_product p
           on i.product_id = p.id
        where i.date_for_shop = :1q
          and i.ts_validated = 0
          and p.provider_id = :2q;', 
      $date, $provider_id);
    clear_cache('aixada_shop_item');
    $db->Execute(
      'insert into aixada_shop_item (
         uf_id, date_for_shop, product_id, quantity
       ) select i.uf_id, i.date_for_order, i.product_id, i.quantity
      from aixada_order_item i
      where i.date_for_order = :1q
        and i.product_id in (:2);',
      $date, $prod_list);
}
	
?>