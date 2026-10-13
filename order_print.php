<?php 
require_once "php/inc/header.inc.php";
require_once "php/reports/torns_report__include.php";
require_once "php/reports/order_report__include.php";
/**
 * El proyecto en `/php/reports/` propone incorporar los deserrollos propios de
 * la coop *La Cistella* en *Aixada* y así dejar de mantenerlo como un rama
 * independiente.
 *
 * #### Commit-3:
 *
 * Pantalla de integración para imprimir los informes de reparto de pedidos.
 * (se llama desde un icono en home)
 *
 * Está pensada para que tengan acceso a todos los miembros de la coop.
 *
 * Da acceso a cerrar pedidos, imprimir (optimizado para din A4) pedidos, turnos
 * y directorio con proveedores y com miembros de las UFs.
 * 
 * Principalmente `/php/reports/order_report_prv_fam.php` permite hacer el
 * reparto basado en papel.
 *   - Este informe es ideal para hacer el reparto en papel e ir apuntando las
 *     unidades o pesos entregados realmente a cada UF.
 *   - Sigue el orden de UFs indicado en `$order_review_uf_sequence` de
 *     `config.php`, con lo cual garantiza que los id de las UF estén el mismo
 *     orden que en la pantalla de revisión de pedidos.
 *   - Si ya se ha realizado la revisión de los pedidos las cantidades
 *     mostradas son las entregadas realmente.
 *
 * El directorio permite tener a mano en papel telefonos y correos de
 * los proveedores y de los mienbros de las UFs.
 */
    
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php 
            $page_title = i18n('orpr_titol');
            echo $Text['global_title'] . " - " . $page_title;  ?></title>

    <link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
    <link rel="stylesheet" type="text/css"   media="screen" href="js/fgmenu/fg.menu.css"   />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/<?=$default_theme;?>/jqueryui.css"/>
    <link rel="stylesheet" type="text/css"                  href="php/reports/css/reports_layout.css" />

    <script type="text/javascript" src="js/jquery/jquery.js"></script>
    <script type="text/javascript" src="js/jqueryui/jqueryui.js"></script>
    <?php echo aixada_js_src(); ?>
    <style>
        .order-print td {
          vertical-align: middle;
        }
        .orpr-repartiment b {
            font-size: 115%;
        }
        .uf_turns {
            color: #555;;
        }        
        
        .bt_active {
            color: #000000;
            border-color: #0056b3;
        }
        .order-td1 {
            width: 4em;
        }
        
        /* Tablas */
        table  {
            width: 100%;
        }
        
        /* Tabla con 3 columnas */
        .table-3 td:nth-child(1) {
            width: 4em;
        }
        .table-3 td:nth-child(2) {
            width: calc(100% - 8em);
        }  
        .table-3 td:nth-child(3) {
            width: 4em;
        }
        
        /* Tabla con 2 columnas */
        .table-2 td:nth-child(1) {
            width: 10em;
        }
        .table-2 td:nth-child(2) {
            width: calc(100% - 10em);
        }


    </style>
<?php

/**
 * Establecer $for_date
 */
$for_date = report_get_for_date();

/**
 * Buscar si hay pedidos implicados
 */
$order_null = false;
$order_not_null = false;
if ($for_date) {
    $order_null = ( 0 != get_list_query(
        'select count(*) order_null from aixada_order_item'.
        " where order_id is null and date_for_order='".$for_date."'")
    );
    $order_not_null = ( 0 != get_list_query(
        'select count(*) order_null from aixada_order_item'.
        " where order_id is not null and date_for_order='".$for_date."'")
    );
}

/**
 * Utilidad para mostrar botones en html
 */
function echo_button($text, $url, $target_blank = false) {
    echo '
    <button 
    class="aix-layout-fixW150 ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"
    role="button" aria-disabled="false" ';
    if ($url) {
        $onclik = 'onclick=';
        if ( strncmp($url, $onclik, strlen($onclik)) === 0 ) {
            // Es un onclik
            echo ' ' . $url . ' ';
        } else {
            // En caso contraio es url
            echo ' onclick="window.open(\'' . $url . "'" . ( $target_blank ? ", '_blank'" : "") . ')" ';
        }
    }
    echo '><span class="ui-button-text">' . $text . '</span></button>';
}

?>
</head>
<body>
<div id="wrap">
    <div id="headwrap">
        <?php include "php/inc/menu.inc.php" ?>
    </div>
    <div class="order-print" id="stagewrap">
        <div class="aix-layout-center60 ui-widget">
            <?php 
            if ($for_date) { ?>
            <div class="aix-style-entry-widget">
                <h2><?php echo i18n('orpr_informes_per_al', ['for_date' => $for_date]); ?></h2>
                <table class="table-3">
                <tr>
                <?php
                if ($order_null){ ?>
                    <td class="order-td1"><?php echo_button(i18n('orpr_bt_enviar'), 'manage_orders.php?filter=nextWeek&lastPage=torn.php', true); ?></td>
                    <td><p><?php echo i18n('orpr_tancar_i_enviar'); ?></p></td>
                <?php 
                } elseif($order_not_null) { ?>
                    <td colspan="3"><?php echo i18n('orpr_comandes_tancades', ['for_date' => $for_date]); ?></td>
                <?php
                } else { ?>
                    <td colspan="3"><?php echo i18n('orpr_cap_comanda', ['for_date' => $for_date]); ?></td>
                <?php 
                } ?>
                </tr>
                <tr><td colspan="3"><hr></td></tr>
                <tr>
                    <td>
                        <span class="op_detall"><?php echo_button(i18n('orpr_bt_prov_ufs'), 'php/reports/order_report_prv_fam.php?date=' .$for_date, true); ?></span>
                        <span class="op_resum"><?php echo_button(i18n('orpr_bt_resum_prov'), 'php/reports/order_report_prv_fam.php?detail=N&date=' .$for_date, true); ?></span>
                    </td>
                    <td colspan="3">
                        <p class="op_detall">
                            <?php echo i18n('orpr_desc_prov_ufs_detall'); ?><br>
                            <span class="orpr-repartiment" style="color:#555"><?php echo i18n('orpr_desc_repartiment'); ?></span><br>
                            <span style="font-size:90%; color:#555"><?php echo i18n('orpr_desc_repartiment_nota'); ?></span>
                        </p>
                        <p class="op_resum"><?php echo i18n('orpr_desc_prov_ufs_resum'); ?></p>
                    </td>
                </tr>
                <tr style="color:#555">
                    <td>
                        <span class="op_detall"><?php 
                            echo_button(i18n('orpr_bt_dif_prov_ufs'),  'php/reports/order_report_prv_fam_diff.php?date=' .$for_date, true); ?></span>
                        <span class="op_resum"><?php 
                            echo_button(i18n('orpr_bt_dif_resum_prov'), 'php/reports/order_report_prv_fam_diff.php?detail=N&date=' .$for_date, true); ?></span>
                    </td>
                    <td>
                        <p class="op_detall"><?php echo i18n('orpr_desc_dif_detall'); ?></p>
                        <p class="op_resum"><?php echo i18n('orpr_desc_dif_resum'); ?></p>
                    </td>
                    <td rowspan="2">
                        <?php echo_button(i18n('orpr_bt_detall'), 'onclick="alternar(1, 2)" id="bt_1"'); ?><br>
                        <?php echo_button(i18n('orpr_bt_resum'),  'onclick="alternar(2, 1)" id="bt_2"'); ?>
                    </td>
                </tr>
                <tr style="color:#555">
                    <td>
                        <span class="op_detall"><?php 
                            echo_button(i18n('orpr_bt_ufs_prov'),  'php/reports/order_report_fam_prv.php?date=' .$for_date, true); ?></span>
                        <span class="op_resum"><?php 
                            echo_button(i18n('orpr_bt_resum_ufs'), 'php/reports/order_report_fam_prv.php?detail=N&date=' .$for_date, true); ?></span>
                    </td>
                    <td>
                        <p class="op_detall"><?php echo i18n('orpr_desc_ufs_prov_detall'); ?></p>
                        <p class="op_resum"><?php echo i18n('orpr_desc_ufs_prov_resum'); ?></p>
                    </td>
                </tr>
                </table>
            </div>
            <?php
            } else { ?>
            <div class="aix-style-entry-widget">
                <h2><?php echo i18n('orpr_cap_data'); ?></h2>
            </div>
            <?php 
            } ?>
            <div class="aix-style-entry-widget">
                <h2><?php echo i18n('orpr_info_interes'); ?></h2>
                <table class="table-2">
                <?php if ( get_config('calendari', true) ) {
                    $text_turns = write_turn_uf(get_session_uf_id(),     date("Y-m-d")); // $for_date);
                    if ($text_turns !="") { ?>
                        <tr><td colspan="2" class="uf_turns"><?php echo i18n('orpr_propers_torns') . ' ' . $text_turns; ?></td></tr>
                    <?php } ?>
                    <tr>
                        <td><?php echo_button(i18n('orpr_bt_torns'), 'php/reports/torns_report.php', true); ?></td>
                        <td><?php echo i18n('orpr_desc_torns'); ?></td>                    
                    </tr>
                    <tr>
                        <td><?php echo_button(i18n('orpr_bt_editar_torns'), 'manage_calendar.php', false); ?></td>
                        <td><?php echo i18n('orpr_desc_editar_torns'); ?></td>                    
                    </tr>
                    <tr><td colspan="2" class="order-td100"><hr></td></tr>
                <?php } ?>
                <tr>
                    <td><?php echo_button(i18n('orpr_bt_directori'), 'php/reports/dir_report_prv_fam.php', true); ?></td>
                    <td><?php echo i18n('orpr_desc_directori'); ?></td>
                </tr>
                </table>
            </div>
        </div>

    </div>
</div>
<script>
    function alternar(id_sel, id_no) {
        $('#bt_'+id_sel).addClass('bt_active');
        $('#bt_'+id_no).removeClass('bt_active');
        if ( id_sel == 1 ) {
            $('.op_detall').show();
            $('.op_resum').hide();
        } else {
            $('.op_detall').hide();
            $('.op_resum').show();
        }
    }
    $(document).ready(function() {
        alternar(1, 2);
    });
</script>
</body>
</html>