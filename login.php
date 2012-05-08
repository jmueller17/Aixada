<?php


//require_once('FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start(); // Starts FirePHP output buffering
//$firephp = FirePHP::getInstance(true);

require_once 'inc/cookie.inc.php';
require_once 'inc/authentication.inc.php';
require_once 'lib/exceptions.php';
require_once 'local_config/config.php';
$language = ( (isset($_SESSION['userdata']['language']) and $_SESSION['userdata']['language'] != '') ? $_SESSION['userdata']['language'] : configuration_vars::get_instance()->default_language );
$default_theme = configuration_vars::get_instance()->default_theme; 
require_once('local_config/lang/' . $language . '.php');


// This controls if the table_manager objects are stored in $_SESSION or not.
// It looks like doing it cuts down considerably on execution time.
$use_session_cache = configuration_vars::get_instance()->use_session_cache;

if (!isset($_SESSION)) {
    session_start();
 }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language?>" lang="<?=$language?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title> <?php print $Text['global_title']; ?> </title>
	
	<link rel="stylesheet" type="text/css" media="screen" href="css/aixada_main.css"  />
	<link rel="stylesheet" type="text/css" media="screen" href="css/ui-themes/<?=$default_theme;?>/jquery-ui-1.8.20.custom.css"/>
	
    <script type="text/javascript" src="js/jquery/jquery.js"></script>
	<script type="text/javascript" src="js/jqueryui/jquery-ui-1.8.20.custom.min.js"></script>
   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaXML2HTML.js" ></script>
	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaUtilities.js" ></script>
	   	
	   	
	<script type="text/javascript">
		$(function(){

			$('#registerWrap').hide();

			//detect form submit and prevent page navigation; we use ajax. 
 			$('form').submit(function() { 
 				return false; 
 			});


			function checkLength(input, min, max, where, msg ) {
				if ( input.val().length > max || input.val().length < min ) {
					input.addClass( "ui-state-error" );
					$.updateTips(where,'error', msg);
					return false;
				} else {
					return true;
				}
			}

			function checkRegexp( o, regexp, n ) {
				if ( !( regexp.test( o.val() ) ) ) {
					o.addClass( "ui-state-error" );
					$.updateTips($('#registerMsg'), 'error', n );
					return false;
				} else {
					return true;
				}
			}

			function checkPassword(pwd, retyped){
				
				if (pwd.val() != retyped.val()){
					pwd.addClass( "ui-state-error" );
					$.updateTips('#registerMsg','error', "<?php echo $Text['msg_err_pwdctrl']; ?>");
					return false; 
				} else {
					return true; 
				}
			}

			function resetForms(){
				$('input').not(':submit, :button').val('').removeClass('ui-state-error');
				$('.user_tips').removeClass('ui-state-error success_msg').text('');
				$('#register').show();
			}

			
			/**
			 *	logno stuff
			 */
			$('#btn_logon').button();
			$('#login').submit(function(){
				var dataSerial = $(this).serialize();
				$.ajax({
					type: "POST",
                    url: "ctrlLogin.php?oper=login",
					data:dataSerial,		
					success: function(msg){			
                        top.location.href = 'index.php';
					},
					error : function(XMLHttpRequest, textStatus, errorThrown){
						$.updateTips('#logonMsg','error',XMLHttpRequest.responseText);
                                          
					}
				}); //end ajax retrieve date
 				return false;
			});

			
			/**
			 *	register stuff
			 */
			$('#btn_register').button({
				icons: {
				primary: "ui-icon-check"}
			});

			$('#switch2register').bind('click',function(){
				$('#stagewrap').hide();
				$('#registerWrap').show();
				$('#registerMsg')
					.removeClass('success_msg')
					.text('');
				$('#register').show();
			});
			
					


			//send the register form
			$('#register').submit(function(){

				var dataSerial = $(this).serialize();
				var bValid = true; 

				 
				bValid = bValid && checkLength($('#reg_login'),3,50,'#registerMsg',"<?echo $Text['msg_err_usershort'];?>");
				bValid = bValid && checkLength($('#reg_password'),4,15,'#registerMsg',"<?echo $Text['msg_err_passshort'];?>"); 
				bValid = bValid && checkPassword($('#reg_password'), $('#reg_password_ctrl'));
				bValid = bValid && checkLength($('#name'),1,50,'#registerMsg',"<?php echo $Text['name_person'] .  $Text['msg_err_notempty'];?>");
				bValid = bValid && checkRegexp($('#phone1'),/^([0-9\s\+])+$/,"<?php echo $Text['phone1'] .  $Text['msg_err_only_num'];?>");
								
				// From jquery.validate.js (by joern), contributed by Scott Gonzalez: http://projects.scottsplayground.com/email_address_validation/
				bValid = bValid && checkRegexp( $('#email'), /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "<?=$Text['msg_err_email'];?>" );
				//bValid = bValid && checkRegexp( password, /^([0-9a-zA-Z])+$/, "Password field only allow : a-z 0-9" );
			
				
				 if (bValid){
						
						$.ajax({
							type: "POST",
							url: "ctrlLogin.php?oper=registerUser",	
							data : dataSerial, 	
							success: function(msg){	
								//$.updateTips("#registerMsg", "success","", 600000 );	

								 $( "#dialog-message p" )
							    	.html("<?php echo $Text['msg_reg_success']; ?>")
							    	.addClass('success_msg');
							    
								$( "#dialog-message" )
									.dialog({ title: 'Succesful registratio' })
									.dialog("open");
									
								$('#registerWrap').hide();
								
							},
							error : function(XMLHttpRequest, textStatus, errorThrown){
						 
							    $( "#dialog-message p" )
							    	.html(XMLHttpRequest.responseText)
							    	.addClass('ui-state-error');
							    
								$( "#dialog-message" )
									.dialog({ title: 'Error' })
									.dialog("open");
							},
							complete : function(msg){
								
							}
						}); //end ajax retrieve date

				}

				return false; 
				
				 
			});//end submit

			/**
			 * Error message dialog 
			 */
			$( "#dialog-message" ).dialog({
				modal: false,
				autoOpen:false,
				buttons: {
					Ok: function() {
						$( this ).dialog( "close" );

						if ($("p",this).hasClass('success_msg')){
							resetForms();
							$('#stagewrap').show();
						}
						
						$("p",this)
				    	.html('')
				    	.removeClass('ui-state-error success_msg');
					}
				}
			});

			
			$('#btn_cancel')
				.button({
					icons: {
					primary: "ui-icon-close"}
				})
				.click(function(){
					resetForms();
					$('#registerWrap').hide();
					$('#stagewrap').show();
					
				});
	
				
			/**
			 *	incidents
			 */
			$('#tbl_incidents tbody').xml2html('init',{
					url: 'smallqueries.php',
					params : 'oper=latestIncidents',
					loadOnInit: true,
					paginationNav : '#tbl_incidents tfoot td'
			});


			/**
			 *	language
			 */
			 $("#pref_lang")
				.xml2html("init", {
						url: "smallqueries.php",
						params : "oper=getExistingLanguages",
						rowName : "language",
						loadOnInit: true,
						complete : function(){
							$("#pref_lang").val("<?php echo $language; ?>"); 
						}
			});

			$('a.toggleIncidentDetails').live('click',function(){
				$(this).closest('tr').next().toggle();
			});


			/**
			 *	reset different intput fields
			 */
			$('input').focus(function(){
				$(this).removeClass('ui-state-error');
				}); 

			$('#login, #password').focus(function(){
					$('#logonMsg')
						.text('')
						.removeClass('ui-state-error');
				}); 

		

		});
	</script>    
	
</head>
<body>


<div id="wrap">

	<div id="headwrap">
		<p id="logonHeader"></p>
	</div>

	<div id="stagewrap">
	
		<div id="newsWrap" class="ui-widget">
			<div class="ui-widget-content ui-corner-all">
			<h2 class="ui-widget-header ui-corner-all"><?php echo $Text['ti_login_news'];?></h2>
			<table id="tbl_incidents">
				<thead>
					<tr>
						<th><?php echo $Text['subject'];?></th>
						<th><?php echo $Text['created_by'];?></th>
						<th><?php echo $Text['created'];?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><a href="javascript:void(null)" class="toggleIncidentDetails">{subject}</a></td>
						<td>{uf}-{user}</td>
						<td>{date_posted}</td>
					</tr>
					<tr class="hidden">
							<td colspan="3" class="noBorder incidentsDetails">{details}</td>
							
					</tr>
				</tbody>
				<tfoot>
				<tr>
					<td colspan="2"></td>
				</tr>
				</tfoot>
			</table>
			</div>
		</div>
		
		<div id="logonWrap" class="ui-widget">
			<div class="ui-widget-content ui-corner-all">
			<h4 class="ui-widget-header ui-corner-all"><?php echo $Text['login'];?></h4>
			<p id="logonMsg" class="user_tips  minPadding"></p>
			<form id="login" method="post">
				<table>
					<tr>
						<td><label class="formLabel" for="login"><?=$Text['logon'];?>:</label></td>
						<td><input type="text" class="inputTxtSmall ui-widget-content ui-corner-all " name="login" id="login"/></td>
					</tr>
					<tr>
						<td><label class="formLabel" for="password"><?=$Text['pwd'];?>:</label></td>
						<td><input type="password" class="inputTxtSmall ui-widget-content ui-corner-all" name="password" id="password"/></td>
					</tr>
					<tr>
						<td>
							<a href="javascript:void(null)" id="switch2register"><?=$Text['register']; ?></a>	
						</td>
						<td class="textAlignRight">
							<button name="submitted" id="btn_logon"><?=$Text['btn_login'];?></button>
							
						</td>
					</tr>
				</table>
				<input type="hidden" name="originating_uri" value="<?=(isset($_REQUEST['originating_uri']) ? $_REQUEST['originating_uri'] : 'login.php') ?>">
			</form>
		</div>
	</div><!-- end logonwrap -->
	</div><!-- end stagewrap -->
	
	<div id="registerWrap" class="ui-widget ui-corner-all">
		<div class="ui-widget-content ui-corner-all">
			<h4 class="ui-widget-header ui-corner-all"><?=$Text['register'];?></h4>
			<p id="registerMsg" class="user_tips minPadding"></p>
			<form id="register" method="post">
				<table>
					<tr>
						<td><label class="formLabel" for="reg_login"><?=$Text['logon'];?>:</label></td>
						<td><input type="text" class="ui-widget-content ui-corner-all " name="login" id="reg_login"> <sup>*</sup> &nbsp;</td>
					</tr>
					<tr>
						<td><label class="formLabel" for="reg_password"><?=$Text['pwd'];?>:</label></td>
						<td><input type="password" class="ui-widget-content ui-corner-all" name="password" id="reg_password"> <sup>*</sup> &nbsp;</td>
					</tr>
					<tr>
						<td><label class="formLabel" for="reg_password_ctrl"><?=$Text['retype_pwd'];?>:</label></td>
						<td><input type="password" class="ui-widget-content ui-corner-all " name="password_ctrl" id="reg_password_ctrl"> <sup>*</sup> &nbsp;</td>
					</tr>
					<tr>
							<td><label for="name"><?php echo $Text['name_person'];?>:</label></td>
							<td><input type="text" name="name" id="name" class="ui-widget-content ui-corner-all" /> <sup>*</sup> &nbsp;</td>
						</tr>
						<tr>
							<td><label for="address"><?php echo $Text['address'];?>:</label></td>
							<td colspan="4"><input type="text" name="address" id="address" class="inputTxtMax ui-widget-content ui-corner-all" /></td>
						</tr>
						<tr>
							<td><label for="city"><?php echo $Text['city'];?>:</label></td>
							<td><input type="text" name="city" id="city" class="ui-widget-content ui-corner-all" /></td>
							<td><label for="zip"><?php echo $Text['zip'];?>:</label></td>
							<td><input type="text" name="zip" id="zip" class="inputTxtSmall ui-widget-content ui-corner-all" /></td>
							
						</tr>
						<tr>
							<td><label for="phone1"><?php echo $Text['phone1'];?>:</label></td>
							<td><input type="text" name="phone1" id="phone1" class="ui-widget-content ui-corner-all" /> <sup>*</sup> &nbsp;</td>
						</tr>
						<tr>
							<td><label for="phone2"><?php echo $Text['phone2'];?>:</label></td>
							<td><input type="text" name="phone2" id="phone2" class="ui-widget-content ui-corner-all" /></td>
						</tr>
						<tr>
							<td><label for="email"><?php echo $Text['email'];?>:</label></td>
							<td><input type="text" name="email" id="email" class="ui-widget-content ui-corner-all" /> <sup>*</sup> &nbsp;</td>
						</tr>
						<tr>
							<td><label for="urls"><?php echo $Text['web'];?>:</label></td>
							<td colspan="5"><input type="text" name="urls" id="urls" class="inputTxtMax ui-widget-content ui-corner-all" /></td>
						</tr>
						<tr>
							<td><label for="notes"><?php echo $Text['notes'];?>:</label></td>
							<td colspan="4"><textarea name="notes" id="notes" class="textareaMax ui-widget-content ui-corner-all"></textarea>
							
							</td>
						</tr>
					<tr>
						<td><label class="formLabel" for="pref_lang"><?=$Text['lang'];?>:</label></td>
						<td>
							<select id="pref_lang" name="pref_lang">
									<option value="{id}">{description}</option>
							</select>
						</td>
					</tr>
					<tr>
						<td></td>
						<td colspan="3"><sup>*</sup><?=$Text['required_fields']; ?></td>
					</tr>
					<tr>
						<td colspan="4" class="textAlignRight">
							<button name="cancel_register" type="reset" id="btn_cancel"><?=$Text['btn_cancel'];?></button>
							<button name="submitted" type="submit" id="btn_register"><?=$Text['btn_submit'];?></button>
						</td>
					</tr>
				</table>

			</form>
		</div>	
	</div>
</div>
<div id="dialog-message" title="">
	<p class="minPadding ui-corner-all"></p>
</div>
</body>
</html>




