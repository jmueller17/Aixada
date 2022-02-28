<?php
$startResponse = '';
if (filesize('js/jquery/jquery.js') < 100) {
    copy('js/jquery/jquery-1.7.1.min.js', 'js/jquery/jquery.js');
    copy('js/jqueryui/jquery-ui-1.8.20.custom.min.js', 'js/jqueryui/jqueryui.js');
    copy('css/ui-themes/redmond/jquery-ui-1.8.20.custom.css', 'css/ui-themes/redmond/jqueryui.css');
    copy('css/ui-themes/start/jquery-ui-1.8.20.custom.css', 'css/ui-themes/start/jqueryui.css');
    copy('css/ui-themes/ui-lightness/jquery-ui-1.8.20.custom.css', 'css/ui-themes/ui-lightness/jqueryui.css');
    copy('css/ui-themes/smoothness/jquery-ui-1.8.20.custom.css', 'css/ui-themes/smoothness/jqueryui.css');
    $startResponse .= "Symbolic link files (.js & .css) have been copied correctly.\n";
}
if (!is_file('local_config/config.php')) {
    copy('local_config/config.php.sample', 'local_config/config.php');
    $startResponse .= 
        "\n****\n" .
        "Configuration file 'local_config/config.php' created by copy of 'config.php.sample' file.\n\n" .
        "**********************************************************************\n" .
        "**  The database parameters MUST BE CONFIGURED before proceeding!!  **\n" .
        "**  => edit file: 'local_config/config.php'                         **\n" .
        "**********************************************************************\n" .
        "\n****\n";
}
require_once 'php/inc/header.inc.base.php';
require_once __ROOT__ . 'php/inc/authentication.inc.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language?>" lang="<?=$language?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>INSTALL Aixada</title>
    
    <link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/<?=$default_theme;?>/jqueryui.css"/>
    <script type="text/javascript" src="js/jquery/jquery.js"></script>
    <script type="text/javascript" src="js/jqueryui/jqueryui.js"></script>
    <style>
        .container {margin-right:auto;margin-left:auto;padding-left:15px;padding-right:15px}
        .container {width:750px}
        .explication {font-size: 90%; color: #888; padding:.5em 0}
        .logMessage {padding: 0.5em; border: 1px solid #aaa;}
        .noticeText {background-color: #ffffee}
        .correctEnd {background-color: #ddffdd}
        .wrongEnd   {background-color: #ffdddd}
        h2 {margin-top:1em}
        button#btn_sql_info {font-size: 75%; color: #886}
    </style>
    <?php echo aixada_js_src(false); ?>	
</head>
<body>
    <div class="container">
        <div class="ui-widget">
            <h2 style="color: blue;">
                <span id="install_mode_1">Install or uptate Aixada database</span>
            </h2>
            <br/>
            <p>
                <span class="loadAnim floatLeft hidden">
                    <img src="img/ajax-loader_fff.gif"/>
                </span>
                <button id="btn_do">Do "<span id="install_mode_bt">install</span>"</button>
            </p>
            <p id="text_pending_config" class="explication" style="display:none">
                You must configure a correct database connection in 
                '<span style="color: #966">local_config/config.php</span>'.
                <br><br>
                Neither installation nor upgrade procedures can be executed
                without a properly defined database connection.
            <p>
            <p id="text_install" class="explication" style="display:none">
                Installation process will create the tables in database and
                put initial data to start using Aixada.
            <p>
            <p id="text_update" class="explication" style="display:none">
                The execution of update can be repeated without problems, if you doubt, do update!
            <p>
            <hr>
            <br>
            <br>
            <p>
                <button id="btn_sql_info"><span style="">Show sql info</span></button>
            </p>
            <br>
            <pre id="install_mode_2"></pre>
            <pre id="install_log" class="logMessage"><?=$startResponse?></pre>
        </div>
    </div>
    <script type="text/javascript">
        $.ajaxSetup({ cache: false });
        
        $('#btn_sql_info').button().click(function(e) {
            $.ajax({
                url: 'php/ctrl/InstallAixada.php?oper=sql_info',
                beforeSend: function() {
                    $('.loadAnim').show();
                    $('#install_log')
                        .removeClass('noticeText correctEnd wrongEnd')
                        .hide();
                },
                success: function(msg) {
                    $('#install_log').addClass('noticeText').text(msg).show();
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $('#install_log')
                        .addClass('wrongEnd')
                        .text(XMLHttpRequest.responseText)
                        .show();
                    $.showMsg({
                        msg: 'An error has occured!' + 
                            '<br><b>' + XMLHttpRequest.responseText + '</b>',
                        type: 'error'
                    });
                },
                complete : function(msg) {
                    $('.loadAnim').hide();
                }
            });
        });
        
        $('#btn_do').button({
            icons: {primary: "ui-icon-script"}
        }).click(function(e) {
            $.ajax({
                url: 'php/ctrl/InstallAixada.php?oper=aixada_update',
                beforeSend: function() {
                    $('.loadAnim').show();
                    $('#install_log')
                        .removeClass('noticeText correctEnd wrongEnd')
                        .hide();
                    $('#btn_do').button("disable");
                },
                success: function(msg) {
                    $('#install_log').addClass('correctEnd').text(msg).show();
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $('#install_log')
                        .addClass('wrongEnd')
                        .text(XMLHttpRequest.responseText)
                        .show();
                    $.showMsg({
                        msg: 'An error has occured during the db install!' + 
                            '<br><b>' + XMLHttpRequest.responseText + '</b>',
                        type: 'error'
                    });
                },
                complete : function(msg) {
                   $('.loadAnim').hide();
                }
            });
        });
        
        $.ajax({
            url: 'php/ctrl/InstallAixada.php?oper=aixada_check',
            beforeSend: function() {
                $('.loadAnim').show();
                $('#install_log')
                    .removeClass('noticeText correctEnd wrongEnd')
                    .hide();
                $('#btn_do').button("disable");
            },
            success: function(msg) {
                var msgArr = (msg + '\n').split('\n');
                $('#install_mode_bt').text(msgArr[0]).show();
                $('#install_mode_1').text(msgArr[0]).show();
                $('#install_mode_2').text(msgArr[1]).show();
                switch (msgArr[0].substr(0, 2)) {
                    case 'IN': 
                        $('#text_install').show();
                        break;
                    case 'Up':
                        $('#text_update').show();
                        break;
                    case 'Pe':
                        $('#text_pending_config').show();
                        $('#btn_do').hide();
                        $('#btn_sql_info').hide();
                        $('#install_mode_2').hide();
                        break;
                }
                $('#btn_do').button("enable");
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                $('#install_mode_1').text('').hide();
                $('#install_mode_2').text('').hide();
                $('#install_log')
                    .addClass('wrongEnd')
                    .text(XMLHttpRequest.responseText)
                    .show();
                $('#btn_do').button("disable");
                $.showMsg({
                    msg: 'Previous verification warning' + 
                        '<br><hr><span style="color: #777">' + XMLHttpRequest.responseText + '</span>',
                    type: 'warning'
                });
            },
            complete : function(msg) {
               $('.loadAnim').hide();
            }
        });
    </script>
    <?php 
        if($startResponse) {
            echo "
            <script> 
            $('#install_log').addClass('noticeText').show();
            </script>";
        }
    ?>
</body>
</html>
