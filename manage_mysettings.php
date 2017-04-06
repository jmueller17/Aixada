<?php include "php/inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_manage'] . " " . $Text['head_ti_manage_uf'] ;?></title>
	
	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/fgmenu/fg.menu.css"   />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/<?=$default_theme;?>/jqueryui.css"/>

    <script type="text/javascript" src="js/jquery/jquery.js"></script>
    <script type="text/javascript" src="js/jqueryui/jqueryui.js"></script>
    <?php echo aixada_js_src(); ?>
   
	<script type="text/javascript">
	
	$(function(){
		$.ajaxSetup({ cache: false });

		//loading Spinner
		$('.loadSpinner').attr('src', "img/ajax-loader-<?=$default_theme;?>.gif").hide(); 
		

		//decide what to do in which section
		var what = $.getUrlVar('what');

		//preorder tab is only available for ordering
		if (what == "pwd") { 
			$('.changePwdElements').show();
			$('.editMemberElements').hide();
			
		} else {
			$('.changePwdElements').hide();
			$('.editMemberElements').show();
		}
		
			
		
		

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

		//remove all eventual error styles on input fields. 
		$('input')
			.live('focus', function(e){
				$(this).removeClass('ui-state-error');
			});
		
		


		 /*******************************************
		  *		EDIT MY SETTINGS
		  *******************************************/
			
		//init the uf member listing
		$('#edit_my_settings').xml2html('init',{
				url : 'php/ctrl/UserAndUf.php',
				params: "oper=getMemberInfo&member_id="+<?php echo get_session_member_id();?>,
				loadOnInit:true,
				complete: function(){

					constructSelects();

					//set the checkboxes
					$('.tblForms input:checkbox').each(function(){
						var bool = $(this).val(); 
						if (bool == "1") $(this).attr('checked',true);
					});


					//each member gets an edit button
					$('.btn_save_edit_member').button({
							icons: {primary: "ui-icon-disk"}
						}).live('click', function(e){
							submitMember('update','#detail_member_'+$(this).attr('memberid'));
							return false; 
						});
				}
		});


		/**
		 *	submits the create/edit member data
		 * 	urlStr : either updateMember or createNewMember
		 *  mi: is the selector string of the layer that contains the whole member form and info
		 */
		function submitMember(action, mi){

			var urlStr = 'php/ctrl/UserAndUf.php?oper=updateMember';
			var isValid = true; 
			var isValidItem = true; 
			var err_msg = ''; 

			//run some local checks
			if (action == 'pwd'){

				urlStr = "php/ctrl/UserAndUf.php?oper=changePassword";


				isValidItem = $.checkFormLength($(mi +' input[name=login]'),3,50);
				if (!isValidItem){
					isValid = false; 
					err_msg += "<?=$Text['msg_err_usershort'];?>" + "<br/><br/>"; 
				}

				isValidItem = $.checkFormLength($(mi+' input[name=password]'),4,15);
				if (!isValidItem){
					isValid = false; 
					err_msg += "<?=$Text['msg_err_passshort'];?>" + "<br/><br/>"; 
				}
				
				isValidItem = $.checkPassword($(mi+' input[name=password]'), $('input[name=password_ctrl]'));
				if (!isValidItem){
					isValid = false; 
					err_msg += "<?=$Text['msg_err_pwdctrl']; ?>"+ "<br/><br/>";
				}

								
			}

			isValidItem = $.checkFormLength($(mi+' input[name="name"]'),2,150);
			if (!isValidItem){
				isValid = false; 
				err_msg += "<?php echo $Text['msg_err_namelength']; ?>"+ "<br/><br/>";
			}

			isValidItem =  $.checkRegexp($(mi+' input[name="phone1"]'),/^([0-9\s\+])+$/);
			if (!isValidItem){
				isValid = false; 
				err_msg += "<?php echo $Text['phone1'] .  $Text['msg_err_only_num']; ?>"+ "<br/><br/>";
			}

			isValidItem =  $.checkRegexp($(mi+' input[name="email"]'),/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
			if (!isValidItem){
				isValid = false; 
				err_msg += "<?=$Text['msg_err_email'] ?>"+ "<br/><br/>";
			}


			if (isValid){

				var sdata = $(mi + ' form').serialize();
								
				$.ajax({
				   	url: urlStr,
					data: sdata, 
				   	beforeSend: function(){
					   	//$('button',mi).button('disable');
					   	//myButton.button('disable');
					   //$('#uf_listing .loadAnim').show();
					},
				   	success: function(msg){
				   	 	$.showMsg({
							msg: "<?=$Text['msg_edit_success'];?>",
							type: 'success'});
				   	},
				   	error : function(XMLHttpRequest, textStatus, errorThrown){
					   $.showMsg({
							msg: XMLHttpRequest.responseText,
							type: 'error'});
				   	},
				   	complete : function(msg){
					   	//$('button',mi).button('enable');
					   //myButton.button('enable');
					   //$('#uf_listing .loadAnim').hide();
				   	}
				}); //end ajax

			//form is not valid		 
			} else {
				$.showMsg({
					msg:err_msg,
					type: 'error'});
			}
		}

		/**
		 *	construct and set language and theme select
		 */
		function constructSelects(){


			//load available languages
			 $("#languageSelect")
				.xml2html("init", {
						url: "php/ctrl/SmallQ.php",
						params : "oper=getExistingLanguages",
						rowName : "language",
						loadOnInit: true,
						complete : function(s){
							//copy the language select with the right option selected
							var selectedLang = $('.memberLanguageSelect').prev().text();
							var langSelect = $('#languageSelect').clone(); 
							$(langSelect).val(selectedLang).attr('selected','selected');
							$('.memberLanguageSelect').append(langSelect);
						}
			});

			//load available themes
			 $("#themeSelect")
				.xml2html("init", {
						url: "php/ctrl/SmallQ.php",
						params : "oper=getExistingThemes",
						rowName : "theme",
						loadOnInit: true,
						complete : function(s){
							//copy the theme select with the right option selected
							var selectedTheme = $('.memberThemeSelect').prev().text();
							var themeSelect = $('#themeSelect').clone(); 
							$(themeSelect).val(selectedTheme).attr('selected','selected');
							$('.memberThemeSelect').append(themeSelect);

						}
			});

		}
		


		/*******************************************
		 *		PASSWORD CHANGE
		 *******************************************/
		
		//send pwd edit form
		$('#change_pwd').submit(function(){
			var dataSerial = $(this).serialize();

			var isValid = true; 
			var err_msg = '';
			var mi = '#change_pwd';

			isValid = isValid &&  $.checkFormLength($(mi+' input[name=password]'),4,15);
			if (!isValid){
				err_msg += "<?=$Text['msg_err_passshort'];?><br/><br/>" ; 
			}
			
			isValid = isValid &&  $.checkPassword($(mi+' input[name=password]'), $('input[name=password_ctrl]'));
			if (!isValid){
				err_msg += "<?=$Text['msg_err_pwdctrl']; ?>";
			}

				

			if (isValid){
				$.ajax({
					   type: "POST",
					   url: "php/ctrl/UserAndUf.php?oper=changePassword",
					   data: dataSerial,
					   beforeSend: function(){
					   		$('#pwdWrap .loadAnim').show();
						},
					   success: function(msg){
						   $.showMsg({
								msg: "<?=$Text['msg_pwd_changed_success'];?>",
								type: 'success'});
					   },
					   error : function(XMLHttpRequest, textStatus, errorThrown){
						   $.showMsg({
								msg: XMLHttpRequest.responseText,
								type: 'error'});
					   },
					   complete : function(msg){
						   $('#pwdWrap .loadAnim').hide();
					   }
				});
			} else {
				$.showMsg({
					msg:err_msg,
					type: 'error'});
			}			
			return false; 
		});//end submit

	
				
			
	});  //close document ready
</script>
</head>
<body>
<div id="wrap">
	<div id="headwrap">
		<?php include "php/inc/menu.inc.php" ?>
	</div>
	<!-- end of headwrap -->
	
	
	<div id="stagewrap">
		<div id="titlewrap" class="ui-widget">
				
			<div id="titleLeftCol">
		    		<h1><?=$Text['ti_my_account']; ?></h1>
		    </div>  	
		</div>
			<div id="pwdWrap" class="ui-widget changePwdElements">
				<div class="ui-widget-content ui-corner-all">
				<h3 class="ui-widget-header ui-corner-all"><?php echo $Text['nav_changepwd'];?> <span class="loadAnim floatRight hidden"><img src="img/ajax-loader.gif"/></span></h3>
				
				<form id="change_pwd" class="padding15x10">
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
			</div>
		
		
			<div id="edit_my_settings" class="ui-widget editMemberElements">
					<div class="ui-widget-content ui-corner-all member-info" id="detail_member_{id}">
					<h3 class="ui-widget-header ui-corner-all">&nbsp;
						<span style="float:right; margin-top:-2px; margin-right:4px;"><img class="loadSpinner" src="img/ajax-loader.gif"/></span>
					</h3>
						<p>&nbsp;</p>
						<form id="frm_save_member_{id}">
							<input type="hidden" name="member_id" value="{id}"/>
							<input type="hidden" name="user_id" value="{user_id}"/>
							<input type="hidden" name="uf_id" value="{uf_id}"/>
							<?php include('php/inc/memberuf.inc.php');?>
						</form>
					
			</div><!--  end edit member -->
	</div>
	<!-- end of stage wrap -->
</div>
<!-- end of wrap -->

<div id="loadLanguageSelect" class="hidden">
<select id="languageSelect" name="language">
	<option value="{id}"> {description}</option>
</select>
</div>

<div id="loadThemeSelect" class="hidden">
<select id="themeSelect" name="gui_theme">
	<option value="{name}"> {name}</option>
</select>
</div>
<!-- / END -->
</body>
</html>