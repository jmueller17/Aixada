<?php

require_once(__ROOT__ . 'local_config/config.php');
require_once(__ROOT__ . 'php/inc/database.php');
require_once(__ROOT__ . "php/utilities/general.php");

function create_empty_cart($uf_id, $date_for_shop) {
    if (!$uf_id || !$date_for_shop) {
        return 0;
    }
    $db = DBWrap::get_instance();
    $db->Execute(
        "insert into aixada_cart (
            uf_id, date_for_shop
        )
        values (
            {$uf_id}, '{$date_for_shop}'
        );"
    );
    return $db->last_insert_id();
}

function getSql_shop_providers() {
    $sql = "select pv.id, pv.name
        from aixada_provider pv
	 	join aixada_product p on p.provider_id = pv.id
        where
            pv.active = 1
            and p.active = 1";
    $use_shop = get_config('use_shop', 'order_and_stock');
    if ($use_shop === 'only_stock') {
        $sql .= " and p.orderable_type_id in(1, 4)";
    } elseif ($use_shop === false) {
        $sql .= " and 1=0";
    }
    return $sql . "
        group by pv.id, pv.name
        order by pv.name";
}

function getSql_shop_categories() {
    $sql = "select pc.id, pc.description
        from aixada_product_category pc
	 	join aixada_product p on p.category_id = pc.id
        join aixada_provider pv on pv.id = p.provider_id
        where
            pv.active = 1
            and p.active = 1";
    $use_shop = get_config('use_shop', 'order_and_stock');
    if ($use_shop === 'only_stock') {
        $sql .= " and p.orderable_type_id in(1, 4)";
    } elseif ($use_shop === false) {
        $sql .= " and 1=0";
    }
    return $sql . "
        group by pc.id, pc.description
        order by pc.description";
}
?>
