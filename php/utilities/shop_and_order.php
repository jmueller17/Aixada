<?php

require_once(__ROOT__ . 'php/inc/database.php');
require_once(__ROOT__ . 'local_config/config.php');

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

?>
