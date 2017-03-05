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
   
	<script type="text/javascript">
	$(function(){
		$.ajaxSetup({ cache: false });

			//init tabs
			$("#tabs").tabs();

			//init xml2html template stuff
			$('#inactiveRoles').xml2html('init');
			$('#activeRoles').xml2html('init');
		        $("#userSelect").xml2html("init", {
				loadOnInit  : true,
				offSet		: 1,
				url         : 'php/ctrl/ActivateRoles.php',				
				params 		: 'oper=listUsers'
			}).change(function(){
				//get the id of the role
				var id = $("option:selected", this).val(); 
				if (id < 0) return true; 
				
				$('#inactiveRoles').xml2html("reload",{
					url     : 'php/ctrl/ActivateRoles.php',
					tpl		: '<option value="{role}">{role}</option>',
					params	: 'oper=getDeactivatedRoles&user_id='+id
				});	

				$('#activeRoles').xml2html("reload",{
					url     : 'php/ctrl/ActivateRoles.php',
					tpl		: '<option value="{role}">{role}</option>',
					params	: 'oper=getActivatedRoles&user_id='+id					
				});	

			});

			$('#activate').button({
				icons: { primary: "ui-icon-arrowthick-1-e"}
			}).click(function(){
				$("#inactiveRoles option:selected").each(function (){
					$("#activeRoles").append($(this).detach());
				}); 
				
			});
			
			$('#deactivate').button({
				icons: {secondary: "ui-icon-arrowthick-1-w"}
			}).click(function(){
				$("#activeRoles option:selected").each(function (){
					$("#inactiveRoles").append($(this).detach());
				});  
				
			});
			
			
			$("input:submit").button();
			$("input:reset").button().click(function(){
				$("#userSelect").children(':lt(2)').attr('selected',true);
				$('#inactiveRoles').empty();
				$('#activeRoles').empty(); 	
			});
			
			$('#activeRolesForm').submit(function(){

				$("input:submit").button( "option", "disabled", true );
				$('#deactivate').button( "option", "disabled", true );
				$('#activate').button( "option", "disabled", true );

				var role_id = [];
				var i=0; 
				$('#activeRoles option').each(function(){
						if ($(this).val() == "{role}") return true;
						role_id[i++] = $(this).val();
				});
				
				var dataSerial = "user_id="+$("#userSelect option:selected").val() +"&role_ids="+role_id;
				
				$.ajax({
					type: "GET",
					url: "php/ctrl/ActivateRoles.php?oper=activateRoles",
					data: dataSerial,
					success: function(msg){
						$.showMsg({
							msg:"<?php echo $Text['msg_edit_success']; ?>",
							type: 'success'});
						//$("input:submit").button( "option", "disabled", false);
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
			
		    	<h1><?php echo $Text['ti_mng_activate_roles'];  ?>
		    	
		</div>
		<form id="activeRolesForm">
		<div class="wrapSelect">
				<select id="userSelect" class="longSelect">
                    	<option value="-1" selected="selected"><?php echo $Text['sel_user']; ?></option>   
                    	<option value="{id}">{id} {name}</option>
				</select>
		</div>
		
		<div id="threeColWrap">
			<div id="leftColumn">
				<h4><?php echo $Text['mo_inact_role'];?></h4>
				<select multiple="multiple" size="15" class="multipleSelect" id="inactiveRoles">						
				</select>
			</div>
			<div id="middleColumn">
				<button id="activate">&nbsp;<?php echo $Text['btn_activate']; ?>&nbsp;&nbsp;</button><br/><br/>
				<button id="deactivate"><?php echo $Text['btn_deactivate']?></button>
			</div>
			<div id="rightColumn">
				<h4><?php echo $Text['active_roles'];?></h4>
				<select multiple="multiple" size="15" class="multipleSelect" id="activeRoles">					
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
