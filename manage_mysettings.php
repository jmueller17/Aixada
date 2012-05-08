<?php include "inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_manage'] . " " . $Text['head_ti_manage_uf'] ;?></title>
	
	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/fgmenu/fg.menu.css"   />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/jquery-ui/ui-lightness/jquery-ui-1.8.custom.css"/>
     
    <?php if (isset($_SESSION['dev']) && $_SESSION['dev'] == true ) { ?> 
	    <script type="text/javascript" src="js/jquery/jquery.js"></script>
		<script type="text/javascript" src="js/jquery/jquery-ui-1.8.custom.min.js"></script>
		<script type="text/javascript" src="js/fgmenu/fg.menu.js"></script>
		<script type="text/javascript" src="js/jquery/jquery.aixadaMenu.js"></script>     	 
	   	<script type="text/javascript" src="js/jquery/jquery.aixadaXML2HTML.js" ></script>
	   	<script type="text/javascript" src="js/jquery/jquery.aixadaUtilities.js" ></script>
   	<?php  } else { ?>
    	<script type="text/javascript" src="js/js_for_manage_mysettings.min.js"></script>   	
     <?php }?>
     
     
   
	<script type="text/javascript">
	
	$(function(){

		//decide what to do in which section
		var what = $.getUrlVar('what');

		//preorder tab is only available for ordering
		if (what == "pwd") { 
			$('#pwdWrap').show();
			$('#edit_member').hide();
			
		} else {
			$('#pwdWrap').hide();
			$('#edit_member').show();
		}
		
			
		$('#btn_mem_save')
			.button({
				icons: {
				primary: "ui-icon-check"}
			})
			.click(function(){
				//$('#member_form').submit();
			});

		/*$('#btn_mem_cancel')
			.button({icons: {primary: "ui-icon-close"}})
			.click(function(){
	
			});*/

		$('#btn_pwd_save')
			.button({
				icons: {
				primary: "ui-icon-check"}
			})
			.click(function(){
				//$('#change_pwd').submit();
			});

		$('#btn_pwd_cancel')
			.button({icons: {primary: "ui-icon-close"}});
		
		
		/**
		 *	language
		 */
		 $("#languageSelect")
			.xml2html("init", {
					url: "smallqueries.php",
					params : "oper=getExistingLanguages",
					rowName : "language",
					loadOnInit: true
		});


		

		//send the member form
		$('#member_form').submit(function(){
			var dataSerial = $(this).serialize();
			
			$.ajax({
				   type: "POST",
				   url: "ctrlUser.php?oper=updateMember",
				   data: dataSerial,
				   beforeSend: function(){
		   			$('#edit_member .loadAnim').show();
					},
				   success: function(msg){
						$.updateTips("#memberMsg", "success", "<?=$Text['msg_edit_mysettings_success'];?>" );
				   },
				   error : function(XMLHttpRequest, textStatus, errorThrown){
				    	$.updateTips("#memberMsg","error", XMLHttpRequest.responseText);
				   },
				   complete : function(msg){
					   $('#edit_member .loadAnim').hide();
				   }
			}); //end ajax
			return false; 
		});//end submit


		
		//send pwd edit form
		$('#change_pwd').submit(function(){
			var dataSerial = $(this).serialize();


			
			$.ajax({
				   type: "POST",
				   url: "ctrlUser.php?oper=changePassword",
				   data: dataSerial,
				   beforeSend: function(){
				   		$('#pwdWrap .loadAnim').show();
					},
				   success: function(msg){
						$.updateTips("#pwdMsg", "success", "<?=$Text['msg_pwd_changed_success'];?>" );
				   },
				   error : function(XMLHttpRequest, textStatus, errorThrown){
				    	$.updateTips("#pwdMsg","error", XMLHttpRequest.responseText);
				   },
				   complete : function(msg){
					   $('#pwdWrap .loadAnim').hide();
				   }
			});
			
			return false; 
		});//end submit

			
		//get info of current user
		$.ajax({
			   type: "POST",
			   url: "smallqueries.php?oper=getMemberInfo&member_id=<?php echo $_SESSION['userdata']['member_id'];?>",
			   dataType: "xml", 
			   success: function(xml){
				   $(xml).find('row:first').children().each(function(){
						var input_name = $(this).attr('f');
						var value = $(this).text();

						if (input_name == 'active' || input_name == 'participant' ){
								$('#'+input_name).attr('checked', parseInt(value));

						} else if (input_name == 'language'){
								$('#languageSelect').val(value).attr('selected',true);

						} else if (input_name == 'roles'){
							var active_roles = value.split(",");							
							for (var i=0; i<active_roles.length; i++){
								$("#rolesInfo").append("<p>"+active_roles[i]+"</p>");
							}
						} else if (input_name == 'providers'){
							var active_providers = value.split(",");
							if (active_providers.length > 1) $("#providersInfo").empty();							
							for (var i=0; i<active_providers.length; i++){
								$("#providersInfo").append("<p>"+active_providers[i]+"</p>");
							}
						} else if (input_name == 'products'){
							var active_products = value.split(",");							
							if (active_products.length > 1) $("#productsInfo").empty();							
							for (var i=0; i<active_products.length; i++){
								$("#productsInfo").append("<p>"+active_products[i]+"</p>");
							}
						} else {
								$('#'+input_name).val(value);
						}

					});
			   },
			   error : function(XMLHttpRequest, textStatus, errorThrown){
			    	$.updateTips("#memberMsg","error", XMLHttpRequest.responseText);
			   },
			   complete : function(msg){
		
			   }
		}); //end ajax
				
			
	});  //close document ready
</script>
</head>
<body>
<div id="wrap">
	<div id="headwrap">
		<?php include "inc/menu2.inc.php" ?>
	</div>
	<!-- end of headwrap -->
	
	
	<div id="stagewrap">
	
		<div id="titlewrap" class="ui-widget">
				
			<div id="titleLeftCol">
		    		<h1><?=$Text['ti_my_account']; ?></h1>
		    </div>
				  	
		</div>


			<div id="pwdWrap" class="ui-widget-content ui-corner-all" >
				<h3 class="ui-widget-header ui-corner-all"><?php echo $Text['nav_changepwd'];?> <span class="loadAnim floatRight hidden"><img src="img/ajax-loader.gif"/></span></h3>
				<p id="pwdMsg" class="user_tips"></p>
				<form id="change_pwd">
					
					<table>
						
						<tr>
							<td><label class="formLabel" for="old_password"><?=$Text['old_pwd'];?>:</label></td>
							<td><input type="password" class="ui-widget-content ui-corner-all" name="old_password" id="old_password"></td>
						</tr>
						<tr>
						<td>&nbsp;</td>
						<td></td>
						</tr>
						
						<tr>
							<td><label class="formLabel" for="reg_password"><?=$Text['pwd'];?>:</label></td>
							<td><input type="password" class="ui-widget-content ui-corner-all" name="password" id="reg_password"></td>
						</tr>
						<tr>
							<td><label class="formLabel" for="reg_password_ctrl"><?=$Text['retype_pwd'];?>:</label></td>
							<td><input type="password" class="ui-widget-content ui-corner-all " name="password_ctrl" id="reg_password_ctrl"></td>
						</tr>
						
						<tr>
							<td colspan="2">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="2"><p class="floatRight"><button id="btn_pwd_cancel" type="reset"><?php echo $Text['btn_cancel'];?></button> <button id="btn_pwd_save" type="submit"><?php echo $Text['btn_save'];?></button></p></td>
								
						</tr>
					</table>
	
				</form>
			
			</div>
		
		
		<div id="edit_member" class="ui-widget-content ui-corner-all" >
				<h3 class="ui-widget-header ui-corner-all"><?=$Text['edit_my_settings'];?> <span class="loadAnim floatRight hidden"><img src="img/ajax-loader.gif"/></span></h3>
				<p id="memberMsg"></p>
				<form id="member_form">
					<input type="hidden" name="login" id="login" value=""/>
					<input type="hidden" name="id" id="member_id" value="<?php echo $_SESSION['userdata']['member_id'];?>"/>
					<input type="hidden" name="uf_id" id="uf_id" value="<?php echo $_SESSION['userdata']['uf_id'];?>"/>
					<div id="edit_member"><br/>
						<p id="memberMsg" class="user_tips ui-corner-all"></p>
						
						<table>
							<tr>
								<td class="textAlignRight"><?php echo $Text['login'];?></td>
								<td><span class="user_login"><?php echo $_SESSION['userdata']['login'];?></span></td>
								<td class="textAlignRight"><?php echo $Text['uf_long'];?></td>
								<td>#<span class="uf_id"><?php echo $_SESSION['userdata']['uf_id'];?></span></td>
								
							</tr>
							<tr>
								<td class="textAlignRight"><?php echo $Text['id'];?></td>
								<td>#<span class="user_id"><?php echo $_SESSION['userdata']['member_id'];?></span></td>
							</tr>
							<tr>
								<td><label for="name"><?php echo $Text['name_person'];?></label></td>
								<td><input type="text" name="name" id="name" class="ui-widget-content ui-corner-all" /></td>
								<td></td>
								<td></td>
							</tr>
							<tr>
								<td><label for="address"><?php echo $Text['address'];?></label></td>
								<td colspan="5"><input type="text" name="address" id="address" class="inputTxtMax ui-widget-content ui-corner-all" /></td>
							</tr>
							<tr>
								<td><label for="city"><?php echo $Text['city'];?></label></td>
								<td><input type="text" name="city" id="city" class="ui-widget-content ui-corner-all" /></td>
								<td><label for="zip"><?php echo $Text['zip'];?></label></td>
								<td><input type="text" name="zip" id="zip" class=" ui-widget-content ui-corner-all" /></td>
								
							</tr>
							<tr>
								<td><label for="phone1"><?php echo $Text['phone1'];?></label></td>
								<td><input type="text" name="phone1" id="phone1" class="ui-widget-content ui-corner-all" /></td>
							
								<td><label for="phone2"><?php echo $Text['phone2'];?></label></td>
								<td><input type="text" name="phone2" id="phone2" class="ui-widget-content ui-corner-all" /></td>
							</tr>
							<tr>
								<td><label for="email"><?php echo $Text['email'];?></label></td>
								<td><input type="text" name="email" id="email" class="ui-widget-content ui-corner-all" /></td>
							</tr>
							<tr>
								<td><label for="urls"><?php echo $Text['web'];?></label></td>
								<td colspan="5"><input type="text" name="urls" id="urls" class="inputTxtMax ui-widget-content ui-corner-all" /></td>
							</tr>
							<tr>
								<td><label for="notes"><?php echo $Text['notes'];?></label></td>
								<td colspan="5"><input type="text" name="notes" id="notes" class="inputTxtMax ui-widget-content ui-corner-all" /></td>
							</tr>
							<tr>
								<td><label for="active"><?php echo $Text['active'];?></label></td>
								<td><input type="checkbox" name="active" value="1" id="active"  /></td>
								<td><label for="participant"><?php echo $Text['participant'];?></label></td>
								<td><input type="checkbox" name="participant" value="1" id="participant"  /></td>
							</tr>
							<tr>
								<td>&nbsp;
								</td>
							</tr>
							<tr>
								<td><?php echo $Text['active_roles'];?></td>
								<td colspan="2">
									<div id="rolesInfo">
									</div>
								</td>
								
								
							</tr>
							<tr>
								<td><?php echo $Text['providers_cared_for'];?>:</td>
								<td colspan="2">
									<div id="providersInfo">
										--
									</div>
								</td>
								
								
							</tr>
							<tr>
								<td><?php echo $Text['products_cared_for'];?>:</td>
								<td colspan="2">
									<div id="productsInfo">
										--
									</div>
								</td>
								
								
							</tr>
							<tr>
								<td><label for="languageSelect"><?php echo $Text['lang']; ?></label></td>
								<td colspan="2">
									<select id="languageSelect" name="language">
										<option value="{id}"> {description}</option>
									</select>
								</td>
								
							</tr>
							<tr>
								<td colspan="5"><p class="floatRight"><button id="btn_mem_save" type="submit"><?php echo $Text['btn_save'];?></button></p></td>
								
							</tr>
						</table>
						</div>
						
				</form>
				
		</div><!--  end edit member -->
		
		
	
		
		
		
	</div>
	<!-- end of stage wrap -->
</div>
<!-- end of wrap -->

<!-- / END -->
</body>
</html>