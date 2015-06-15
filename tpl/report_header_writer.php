<?php
function write_tpl_header_item($content) {
    if ($content) {
        echo  $content.'<br>';
    }
}
function write_tpl_header() {
    $coop_header_logo = get_config('coop_header_logo', 'img/tpl_header_logo.png');
    if ($coop_header_logo) {
        echo '<div id="logo"><img alt="coop logo" src="../'.$coop_header_logo.
                '"/></div>';
    }
    ?>
    <div id="address" class="floatRight">
        <h2 class="txtAlignRight"><?php echo get_config('coop_name');?></h2>
        <h4 class="txtAlignRight">CIF/NIF: <?php
                    echo get_config('coop_VAT_number');?></h4>
        <p class="txtAlignRight"><?php
            write_tpl_header_item(get_config('coop_address'));
            write_tpl_header_item(get_config('coop_city'));
            write_tpl_header_item(get_config('coop_contact_inf')); ?></p>
    </div>
    <div style="clear: both; margin-bottom: 10px;">&nbsp;</div>
    <?php
}
?>
