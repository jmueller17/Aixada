<?php include "php/inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_active_roles']; ?></title>
	   	
    <link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/fgmenu/fg.menu.css"   />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/<?=$default_theme;?>/jqueryui.css"/>
    
    <script type="text/javascript" src="js/jquery/jquery.js"></script>
    <script type="text/javascript" src="js/jqueryui/jqueryui.js"></script>
    <?php echo aixada_js_src(); ?>
   		
 	<script type="text/javascript" src="js/jqueryui/i18n/jquery.ui.datepicker-<?=$language;?>.js" ></script>
     
   
   
    <?php $the_role = $_SESSION['userdata']['current_role']; ?>
	<script type="text/javascript">
	$(function(){
		$.ajaxSetup({ cache: false });

			//init tabs
			$("#tabs").tabs();

			//init xml2html template stuff
			$('#inactiveUsers').xml2html('init');
			$('#activeUsers').xml2html('init');

            var theRole = <?php echo '"' . $the_role . '"'; ?>

            $('#inactiveUsers').xml2html("reload",{
                                    url     : 'php/ctrl/ActivateRoles.php',
                                    tpl		: '<option value="{user_id}">{name}</option>',
                                    params	: 'oper=getDeactivatedUsers&role='+theRole
            });	

            $('#activeUsers').xml2html("reload",{
				    url     : 'php/ctrl/ActivateRoles.php',
				    tpl		: '<option value="{user_id}">{name}</option>',
				    params	: 'oper=getActivatedUsers&role='+theRole
            });	

			$('#activate').button({
				icons: { primary: "ui-icon-arrowthick-1-e"}
			}).click(function(){
				$("#inactiveUsers option:selected").each(function (){
					$("#activeUsers").append($(this).detach());
				}); 
				
			});
			
			$('#deactivate').button({
				icons: {secondary: "ui-icon-arrowthick-1-w"}
			}).click(function(){
				$("#activeUsers option:selected").each(function (){
					$("#inactiveUsers").append($(this).detach());
				});  
				
			});
			
			
			$("input:submit").button();
			$("input:reset").button().click(function(){
				$('#inactiveUsers').empty();
				$('#activeUsers').empty(); 	
			});
			
			$('#activeUsersForm').submit(function(){

				$("input:submit").button( "option", "disabled", true );
				$('#deactivate').button( "option", "disabled", true );
				$('#activate').button( "option", "disabled", true );

				var user_id = [];
				var i=0; 
				$('#activeUsers option').each(function(){
						if ($(this).val() == "{user_id}") return true;
						user_id[i++] = $(this).val();
				});
				
				var dataSerial = "user_ids="+user_id;
				$.ajax({
					type: "POST",
					url: "php/ctrl/ActivateRoles.php?oper=activateUsers&role="+theRole,
					data: dataSerial,
					success: function(msg){
						//$("input:submit").button( "option", "disabled", false);
						$.showMsg({
							msg:"<?php echo $Text['msg_edit_success']; ?>",
							type: 'success'});
					},
					error : function(XMLHttpRequest, textStatus, errorThrown){
						$.showMsg({
							msg:XMLHttpRequest.responseText,
							type: 'error'});
						
					},
					complete : function(){
						$("input:submit").button( "option", "disabled", false );
						$('#deactivate').button( "option", "disabled", false );
						$('#activate').button( "option", "disabled", false );
					}
				}); //end ajax
				return false; 
			});//end submit

			/*$('form').submit(function() { 
				// submit the form 
				//$(this).ajaxSubmit(); 
		    	// return false to prevent normal browser submit
				return false; 
			});*/	
			
			
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
				
    	  <h1><?php echo $Text['ti_mng_activate_users'] . $Text[$the_role];  ?>
		    	
		</div>
		<form id="activeUsersForm">
		
		<div id="threeColWrap" >
			<div id="leftColumn">
				<h4><?php echo $Text['mo_inact_user'];?></h4>
				<select multiple="multiple" size="15" class="multipleSelect" id="inactiveUsers">						
				</select>
			</div>
			<div id="middleColumn">
				<button id="activate">&nbsp;<?php echo $Text['btn_activate']; ?>&nbsp;&nbsp;</button><br/><br/>
				<button id="deactivate"><?php echo $Text['btn_deactivate']?></button>
			</div>
			<div id="rightColumn">
				<h4><?php echo $Text['mo_act_user'];?></h4>
				<select multiple="multiple" size="15" class="multipleSelect" id="activeUsers">					
				</select>
			</div>
			
		</div><!-- end role wrap -->		
		<!-- div id="mngOrderBtn">
			<input type="reset" value="<?php echo $Text['btn_reset']; ?>"/>
			<input type="submit" value="<?php echo $Text['btn_save']; ?>"/>
		</div-->
		</form>

		
	</div>
	<!-- end of stage wrap -->
</div>
<!-- end of wrap -->

<!-- / END -->
</body>
</html>
