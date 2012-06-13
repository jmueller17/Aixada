<?php include "inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_manage'] . " " . $Text['head_ti_manage_uf'] ;?></title>
	
	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
  	<link rel="stylesheet" type="text/css"   media="print"  href="css/print.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/fgmenu/fg.menu.css"   />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/<?=$default_theme;?>/jqueryui.css"/>

	<?php if (isset($_SESSION['dev']) && $_SESSION['dev'] == true ) { ?> 
	   <script type="text/javascript" src="js/jquery/jquery.js"></script>
		<script type="text/javascript" src="js/jqueryui/jqueryui.js"></script>
		<script type="text/javascript" src="js/fgmenu/fg.menu.js"></script>
		<script type="text/javascript" src="js/aixadautilities/jquery.aixadaMenu.js"></script>     	 
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaXML2HTML.js" ></script>
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaUtilities.js" ></script>
	   	
   	<?php  } else { ?>
   	    <script type="text/javascript" src="js/js_for_manage_db.min.js"></script>
    <?php }?>
    
   
	<script type="text/javascript">
	
	$(function(){

						

		/**
		 *	backup button!
		 */
		$('#btn_backup')
			.button({
				icons: {
					primary: "ui-icon-copy"}
			})
			.click(function(){

				$.ajax({
					   url: "ctrlValidate.php?oper=backupDatabase",
					   beforeSend: function(){
						   $('.loadAnim').show();
						   $('#btn_backup').button( "option", "disabled", true );
						},
					   success: function(msg){
							 $('#msg_link').html("<a href='"+msg+"'>Download db-zipped</a>");
					   },
					   error : function(XMLHttpRequest, textStatus, errorThrown){
						   $.showMsg({
								msg:XMLHttpRequest.responseText,
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
		<?php include "inc/menu2.inc.php" ?>
	</div>
	<!-- end of headwrap -->
	
	
	<div id="stagewrap" class="ui-widget">
	
		<div id="titlewrap">
		    <h1><?php echo $Text['ti_mng_db']; ?></h1>	  	
		</div>
			
		<p id="msg_link"><?php echo $Text['confirm_db_backup'];?></p>
		<br/><br/>
		<p><span class="loadAnim floatLeft hidden"><img src="img/ajax-loader_fff.gif"/></span>&nbsp;&nbsp; <button id="btn_backup">OK, backup!</button></p>
		<br/><br/>
		<p id="dbError" class="width-280"></p>
		
	</div>
	<!-- end of stage wrap -->
</div>
<!-- end of wrap -->



<!-- / END -->
</body>
</html>