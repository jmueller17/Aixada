<?php include "php/inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_manage'] . " " . $Text['head_ti_manage_uf'] ;?></title>
	
	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
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
	    <script type="text/javascript" src="js/js_for_manage_user.min.js"></script>
    <?php }?>
        	
   
	<script type="text/javascript">
	
	$(function(){
			

		//init the uf member listing
		$('#uf_member_list tbody').xml2html('init',{
			url : "smallqueries.php",
			beforeLoad : function(){
				$('#member_listing .loadAnim').show();
			},
			complete : function(){
				$('#member_listing .loadAnim').hide();
			}
		});
		

		/**
		 *	language
		 */
		 $("#languageSelect")
			.xml2html("init", {
					url: 'smallqueries.php',
					params : 'oper=getExistingLanguages',
					rowName : "language",
					loadOnInit: true
		});

        /**
		 *	brigades
		 */
		 /*$("#rolesSelect")
			.xml2html("init", {
					url: 'smallqueries.php',
					params : 'oper=getRoles',
					offSet : 1,
					rowName : 'role',
					loadOnInit: true
		});*/



		/**
		 *	change pwd existing member
		 */
		$( "#dialog-pwd").dialog({
				autoOpen: false,
				height: 220,
				width: 420,
				modal: true,
				buttons: {
					"<?=$Text['btn_save'];?>":function(){
						//do consistency checking.... here 
						$('#change_pwd').trigger('submit');
					},
					"<?=$Text['btn_close'];?>" : function(){
						mem_id = -1;
						$( this ).dialog( "close" );
					}
				}
		});

		
		//globel mem_id of the currently selected user
		var mem_id = -1;

		//the "key" button is clicked
		$('.btn_edit_pwd').live("click", function(){
			//current member for changing the pwd. 
			mem_id = $(this).parent().next().text();
			$( "#dialog-pwd" ).dialog( "open" );
		});


		/**
		 *	product search functionality 
		 */
		$(".checkInput").keyup(function(e){
					
					var minLength = 4; 
					var pwd = $('#reg_password').val(); 
					var pwdctrl = $('#reg_password_ctrl').val();


					if (pwd == pwdctrl && pwd.length >= minLength ){
						$('#reg_password_ctrl').removeClass('ui-state-error');
					} else {
						$('#reg_password_ctrl').addClass('ui-state-error');
					}

			//prevent default event propagation. once the list is build, just stop here. 		
			e.preventDefault();
		}); //end autocomplete

		
		function checkPassword(pwd, retyped){
			if (pwd.val() != retyped.val()){
		
				return false; 
			} else {
				return true; 
			}
		}
			
		
		/**
		 *	edit existing member
		 */
		$( "#dialog-member" ).dialog({
				autoOpen: false,
				height: 520,
				width: 620,
				modal: true,
				buttons: {
					"<?=$Text['btn_save'];?>":function(){
						//do consistency checking.... here 
						$('#member_form').trigger('submit');
					},
					"<?=$Text['btn_close'];?>" : function(){
						$( this ).dialog( "close" );
					}
				}
		});


		

	
		 
		$('.btn_edit_member').live("click", function(){
			//reset form
			$(':input','#member_form').not(':checkbox').val('');
			$('input:checkbox').attr('checked',false);
			$("#rolesInfo").empty();
			
			
			//get the current uf /name
			var id = $("#uf_select option:selected").val(); 
			var uf = $("#uf_select option:selected").text(); 
			
			//do the settings for editing existing member			
			$('#uf_id').val(id);

			//populate the form
			$(this).closest('tr').find('td:gt(0)').each(function(){
				var input_name = $(this).attr('field_name');
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
				} else {
						$('#'+input_name).val(value);
				}

				if (input_name == 'member_id'){
					$('.user_id').text(value);
				}

				if (input_name == 'login'){
					$('.user_login').text(value);
				}

			});

			$( "#dialog-member" ).dialog( "open" );
		
			
			
		});


		//send the member form
		$('#member_form').submit(function(){
			var dataSerial = $(this).serialize();
			
			$.ajax({
				   type: "POST",
				   url: "php/ctrl/User.php?oper=updateMember",
				   data: dataSerial,
				   success: function(msg){
						$.updateTips('#memberMsg', 'success', "<?=$Text['msg_edit_success'];?>" );
						$('#uf_member_list tbody').xml2html('reload');
						$( "#dialog-member" ).dialog( "close" );
							
				   },
				   error : function(XMLHttpRequest, textStatus, errorThrown){
				    	$.updateTips('#memberMsg','error', XMLHttpRequest.responseText);
				   },
				   complete : function(msg){
			
				   }
			}); //end ajax
			return false; 
		});//end submit



		//do password edit form
		$('#change_pwd').submit(function(){
			if (mem_id <= 0){
				alert("no member id for changing pwd"); 
				return false;
			}

			var pwd = $('#reg_password').val(); 
			if (pwd.length < 4){
				$.updateTips('#pwdMsg','error', "<?echo $Text['msg_err_passshort'];?>");
				return false;
			}
					
			//set the user_id in the form
			$('#user_id').val(mem_id);
			var dataSerial = $(this).serialize();

			$.ajax({
				   type: "POST",
				   url: "php/ctrl/User.php?oper=changeOtherPassword",
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

		
		
		/**
		 *	Deactivate uf member
		 */
		$('.btn_del_member')
			.live("mouseenter", function(){
				$(this).removeClass('ui-icon-close').addClass('ui-icon-circle-close');
			})
			.live("mouseleave", function(){
				$(this).removeClass('ui-icon-circle-close').addClass('ui-icon-close');
			})
			.live("click", function(){
				var member_id = $(this).parent().next().text();
				$.showMsg({
								msg: "<?=$Text['msg_confirm_del']?>",
								type: 'confirm',
								buttons : {
										"<?=$Text['btn_ok'];?>": function() {
											$this = $(this);
											$.ajax({
												   type: "POST",
												   url: "php/ctrl/User.php?oper=deactivateMember&id="+member_id,
												   success: function(msg){
														$this.dialog( "close" );
															
												   },
												   error : function(XMLHttpRequest, textStatus, errorThrown){
												    	$.updateTips('#memberMsg','error', XMLHttpRequest.responseText);
												    	$this.dialog( "close" );
												   }, 
												   complete : function(){
													   $('#uf_member_list tbody').xml2html('reload');	
													   
													   
													}
											}); //end ajax
									  	}, 
									  	"<?=$Text['btn_cancel'];?>":function(){
									  		$(this).dialog( "close" );
											
									  	}
									}
					});
			});


		/**
		 * build uf SELECT
		 */
		$("#uf_select")
			.xml2html("init", {
					url: 'smallqueries.php',
					params : 'oper=getAllUFs',
					offSet : 1,
					loadOnInit: true
			})
			.change(function(){
					//get the id of the provider
					var id = $("option:selected", this).val(); 
					
					if (id > 0) {
						$(".uf_id").html(id);
						$("#btn_new_member").button('enable');
					} else {
						resetUFs();
					}
					
					$('#uf_member_list tbody').xml2html('reload',{
						params	: 'oper=getMembersOfUF&uf_id='+id,
					});				
		}); //end select change

		
		/**
		 *	reset the buttons if no uf is selected
		 */
		function resetUFs(){
			$("#btn_new_member").button('disable');
			$(".uf_id").html('???');
			$(".user_id").html('???');
		}
			 
				
			
	});  //close document ready
</script>
</head>
<body>
<div id="wrap">
	<div id="headwrap">
		<?php include "php/inc/menu2.inc.php" ?>
	</div>
	<!-- end of headwrap -->
	
	
	<div id="stagewrap">
	
		<div id="titlewrap" class="ui-widget">
				
			<div id="titleLeftCol">
		    		<h1><?php echo $Text['ti_mng_members']; ?></h1>
		    </div>
		    <div id="titleRightCol">
		    	<p>&nbsp;</p>
		    	<!-- p class="textAlignRight"><?php echo $Text['search_memberuf'];?>: <input type="text" name="search_member" id="search_member" class="inputTxtMiddle ui-widget-content ui-corner-all" /></p-->
		    </div>
		    <div id="titleSub">
		    	 <p>	    	 
		    	 <?=$Text['browse_memberuf'];?>:&nbsp;<select id="uf_select" class="longSelect">
		    			<option value="-1" selected="selected"><?php echo $Text['sel_uf']; ?></option>
		    			<option value="{id}">{id} {name}</option>
		    		</select>
		    	</p>
		    </div>
				  	
		</div>

		
		<div id="member_listing" class="ui-widget floatLeft">
			<div class="ui-widget-content ui-corner-all">
				<h2 class="ui-widget-header ui-corner-all"> <?=$Text['members_uf'];?> <span class="uf_id">???</span><span class="loadAnim floatRight hidden"><img src="img/ajax-loader.gif"/></span></h2>
			
				<table id="uf_member_list" class="product_list" >
						<thead>
						<tr>
							<th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
							<th><?php echo $Text['id'];?></th>
							<th><?php echo $Text['name_person'];?></th>
							<th><?php echo $Text['address'];?></th>
							<th><?php echo $Text['zip'];?></th>
							<th><?php echo $Text['city'];?></th>
							<th><?php echo $Text['phone1'];?></th>
							<th><?php echo $Text['phone2'];?></th>
							<th><?php echo $Text['email'];?></th>
							<th><?php echo $Text['last_logon'];?></th>
							<th><?php echo $Text['active'];?></th>
							<th colspan="6"><?php echo $Text['notes'];?></th>
						</tr>
						</thead>
						<tbody>
							<tr>
								<td style="float:left">&nbsp;<span style="float:left" class="btn_edit_member ui-icon ui-icon-pencil"></span> <span style="float:left" class="btn_edit_pwd ui-icon ui-icon-key"></span></td>
								<td field_name="member_id">{id}</td>
								<td field_name="name">{name}</td>
								<td field_name="address">{address}</td>
								<td field_name="zip">{zip}</td>	
								<td field_name="city">{city}</td>	
								<td field_name="phone1">{phone1}</td>
								<td field_name="phone2">{phone2}</td>
								<td field_name="email">{email}</td>
								<td field_name="last_login">{last_login}</td>
								<td field_name="last_login_attempt" class="hidden">{last_login_attempt}</td>
								<td field_name="login" class="hidden">{login}</td>
								<td field_name="active">{active}</td>
								<td field_name="participant" class="hidden">{participant}</td>
								<td field_name="adult" class="hidden">{adult}</td>
								<td field_name="notes">{notes}</td>
								<td field_name="language" class="hidden">{language}</td>
								<td field_name="roles" class="hidden">{roles}</td>
								
																					
							</tr>						
						</tbody>
					</table>
			</div>
		</div>
		
		
		<div id="dialog-member" title="Edit user">
			<form id="member_form">
				<input type="hidden" name="login" id="login" value=""/>
				<input type="hidden" name="id" id="member_id" value=""/>
				<div id="edit_member"><br/>
					<p id="memberMsg" class="user_tips ui-corner-all"></p>
					
					<table>
						<tr>
							<td class="textAlignRight"><?php echo $Text['login'];?></td>
							<td><span class="user_login"></span></td>
							<td class="textAlignRight"><?php echo $Text['uf_long'];?></td>
							<td>#<span class="uf_id"></span></td>
							
						</tr>
						<tr>
							<td class="textAlignRight"><?php echo $Text['id'];?></td>
							<td>#<span class="user_id"></span></td>
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
							<td><label for="languageSelect"><?php echo $Text['lang']; ?></label></td>
							<td colspan="2">
								<select id="languageSelect" name="language">
									<option value="{id}"> {description}</option>
								</select>
							</td>
							
						</tr>
					</table>
					</div>
					
			</form>
		</div>
		
		
		
		<div id="dialog-pwd" class="ui-widget-content ui-corner-all" title="<?php echo $Text['nav_changepwd'];?>">
				<!--  h3 class="ui-widget-header ui-corner-all"> <span class="loadAnim floatRight hidden"><img src="img/ajax-loader.gif"/></span></h3-->
				<p id="pwdMsg" class="user_tips"></p>
				<form id="change_pwd">
					<input type="hidden" id="user_id" name="user_id" value=""/>
					<table>
						<tr>
						<td>&nbsp;</td>
						<td></td>
						</tr>
						
						<tr>
							<td><label class="formLabel" for="reg_password"><?=$Text['pwd'];?>:</label></td>
							<td><input type="password" class="ui-widget-content ui-corner-all checkInput" name="password" id="reg_password"></td>
						</tr>
						<tr>
							<td><label class="formLabel" for="reg_password_ctrl"><?=$Text['retype_pwd'];?>:</label></td>
							<td><input type="password" class="ui-widget-content ui-corner-all checkInput" name="password_ctrl" id="reg_password_ctrl"></td>
						</tr>
						
						<tr>
							<td colspan="2">&nbsp;</td>
						</tr>
						<tr>
							<!-- td colspan="2"><p class="floatRight"><button id="btn_pwd_cancel" type="reset"><?php echo $Text['btn_cancel'];?></button> <button id="btn_pwd_save" type="submit"><?php echo $Text['btn_save'];?></button></p></td-->
								
						</tr>
					</table>
	
				</form>
			
			</div>
		
		
	</div>
	<!-- end of stage wrap -->
</div>
<!-- end of wrap -->

<!-- / END -->
</body>
</html>