<?php include "php/inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_manage'] . " " . $Text['head_ti_manage_uf'] ;?></title>
	
	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
  	<link rel="stylesheet" type="text/css"   media="print"  href="css/print.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/fgmenu/fg.menu.css"   />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/<?=$default_theme;?>/jqueryui.css"/>

    <script type="text/javascript" src="js/jquery/jquery.js"></script>
    <script type="text/javascript" src="js/jqueryui/jqueryui.js"></script>
    <?php echo aixada_js_src(); ?>
   
	<?php 
        $backup_method = get_config('db_backup_method','');
        if ($backup_method === '') {
            $backup_method = 'backupDatabase';
        } else {
            $backup_method = 'backupDatabase_'.$backup_method;
        }
    ?>
	<script type="text/javascript">
	$(function(){
		$.ajaxSetup({ cache: false });

		/**
		 *	backup button!
		 */
		$('#btn_backup')
			.button({
				icons: {
					primary: "ui-icon-copy"}
			})
			.click(function(e){

				$.ajax({
					   url: "php/ctrl/Admin.php?oper=<?php echo $backup_method ?>",
					   beforeSend: function(){
						   $('.loadAnim').show();
						   $('#btn_backup').button( "option", "disabled", true );
						},
					   success: function(msg){
					    $('#msg_link').html('<a href="'+msg+'"><?=$Text['download_db_zipped'];?></a>');
					   },
					   error : function(XMLHttpRequest, textStatus, errorThrown){
						   $.showMsg({
								msg:"An error has occured during the db backup!",
								type: 'error'});
						  
					   },
					   complete : function(msg){
						   $('.loadAnim').hide();
						   $('#btn_backup').button( "option", "disabled", false );
					   }
				}); //end ajax
				
			});

		
				
			
	});  //close document ready
</script>
</head>
<body>
<div id="wrap">
	<div id="headwrap">
		<?php include "php/inc/menu.inc.php" ?>
	</div>
	<!-- end of headwrap -->
	
	
	<div id="stagewrap" class="ui-widget">
	
		<div id="titlewrap">
		    <h1><?php echo $Text['ti_mng_db']; ?></h1>	  	
		</div>
			
		<p id="msg_link"><?php echo $Text['confirm_db_backup'];?></p>
		<br/><br/>
		<p><span class="loadAnim floatLeft hidden"><img src="img/ajax-loader_fff.gif"/></span>&nbsp;&nbsp; <button id="btn_backup"><?=$Text['backup'];?></button></p>
		<br/><br/>
		<p id="dbError" class="width-280"></p>
		
	</div>
	<!-- end of stage wrap -->
</div>
<!-- end of wrap -->



<!-- / END -->
</body>
</html>
