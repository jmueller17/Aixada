<?php
$startResponse = '';
if (filesize('js/jquery/jquery.js') < 100) {
    copy('js/jquery/jquery-1.7.1.min.js', 'js/jquery/jquery.js');
    copy('js/jqueryui/jquery-ui-1.8.20.custom.min.js', 'js/jqueryui/jqueryui.js');
    copy('css/ui-themes/redmond/jquery-ui-1.8.20.custom.css', 'css/ui-themes/redmond/jqueryui.css');
    copy('css/ui-themes/start/jquery-ui-1.8.20.custom.css', 'css/ui-themes/start/jqueryui.css');
    copy('css/ui-themes/ui-lightness/jquery-ui-1.8.20.custom.css', 'css/ui-themes/ui-lightness/jqueryui.css');
    copy('css/ui-themes/smoothness/jquery-ui-1.8.20.custom.css', 'css/ui-themes/smoothness/jqueryui.css');
    $startResponse .= "The symbolic link files (.js & .css) have been copied correctly.\n";
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
        .logMessage {padding: 0.5em; border: 1px solid #aaa; max-width: 60em;}
        .warningStart {background-color: yellow}
        .correctEnd  {background-color: #ccffcc}
        .wrongEnding {background-color: #ffcccc}
    </style>
    <?php echo aixada_js_src(false); ?>	
</head>
<body>
    <div id="wrap">
        <div id="stagewrap" class="ui-widget">
            <div id="titlewrap">
                <h1>Install or uptate Aixada database</h1>
            </div>
            <h2 style="color: blue;">
                <span id="install_mode_1"></span>
            </h2>
            <p style="color: #999; margin-left:1em">
                <span id="install_mode_2"></span><br>
                <button id="btn_sql_info" 
                    style="color: #777; border-radius: 5px; padding: 3px">
                    Get Session sql information
                </button>
            </p>
            <br/>
            <p>
                <span class="loadAnim floatLeft hidden">
                    <img src="img/ajax-loader_fff.gif"/>
                </span>
                <button id="btn_install">Do "<span id="install_mode_bt">install</span>"</button>
            </p>
            <br/><br/>
            <p id="dbError" class="width-280"></p>
            <pre id="install_log"
                class="<?php if($startResponse) {echo 'logMessage warningStart';} ?>"><?=$startResponse?></pre>
        </div>
    </div>
    <script type="text/javascript">
        $.ajaxSetup({ cache: false });
        
        $('#btn_sql_info').click(function(e) {
            $.ajax({
                url: 'php/ctrl/InstallAixada.php?oper=sql_info',
                beforeSend: function() {
                    $('.loadAnim').show();
                    $('#install_log')
                        .removeClass('warningStart correctEnd wrongEnding')
                        .hide();
                },
                success: function(msg) {
                    $('#install_log').addClass('logMessage correctEnd').text(msg).show();
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $('#install_log')
                        .addClass('logMessage wrongEnding')
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
        
        $('#btn_install').button({
            icons: {primary: "ui-icon-script"}
        }).click(function(e) {
            $.ajax({
                url: 'php/ctrl/InstallAixada.php?oper=aixada_update',
                beforeSend: function() {
                    $('.loadAnim').show();
                    $('#install_log')
                        .removeClass('warningStart correctEnd wrongEnding')
                        .hide();
                    $('#btn_install').button("disable");
                },
                success: function(msg) {
                    $('#install_log').addClass('logMessage correctEnd').text(msg).show();
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $('#install_log')
                        .addClass('logMessage wrongEnding')
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
                    .removeClass('warningStart correctEnd wrongEnding')
                    .hide();
                $('#btn_install').button("disable");
            },
            success: function(msg) {
                var msgArr = (msg + '\n').split('\n');
                $('#install_mode_bt').text(msgArr[0]).show();
                $('#install_mode_1').text(msgArr[0]).show();
                $('#install_mode_2').text(msgArr[1]).show();
                $('#btn_install').button("enable");
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                $('#install_mode_1').text('').hide();
                $('#install_mode_2').text('').hide();
                $('#install_log')
                    .addClass('logMessage wrongEnding')
                    .text(XMLHttpRequest.responseText)
                    .show();
                $('#btn_install').button("disable");
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
</body>
</html>
