<?php
require_once("php/inc/header.inc.base.php");

require_once(__ROOT__ . 'php'.DS.'inc'.DS.'authentication.inc.php');

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
	<title> <?php print $Text['global_title'] . " - " . $Text['ti_login_news'];?> </title>
	
	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/<?=$default_theme;?>/jqueryui.css"/>
	
   
	<script type="text/javascript" src="js/jquery/jquery.js"></script>
	<script type="text/javascript" src="js/jqueryui/jqueryui.js"></script>
	<?php echo aixada_js_src(false); ?>	
   	
    <style><?php
        $login_header_image =
            get_config('login_header_image', 'img/aixada_header800.150.png');
        if ($login_header_image) {
            echo "p#logonHeader {background-image: url({$login_header_image});}";
        } else {
            echo "p#logonHeader {background-image: none;}";
        }
    ?></style>
	   	
	
	   	
	<script type="text/javascript">
		$(function(){
			$.ajaxSetup({ cache: false });

			document.cookie = 'USERAUTH=';
			
			/**
			 *	logon stuff
			 */
			$('#btn_logon').button();
			$('#login').submit(function(){
				var dataSerial = $(this).serialize();
				//alert(dataSerial);
				$.ajax({
					type: "POST",
                    url: "php/ctrl/Login.php?oper=login",
					data:dataSerial,		
					success: function(returned_cookie){
					    /*
					      FIXME
					      there are two very basic security issues here:
					      1. the dataSerial is posted unencrypted, and so is visible to everyone!
					      Even encrypting the username/password is no solution, because anyone who intercepts the communication
					      can just send the encrypted text without knowing what it decrypts to, but can log in anyways.
					      The solution could be to implement an SSL protocol.
					      2. The cookie never expires.
					      This has two parts: here in document.cookie we could set an expiry date;
					      on the other hand, if the cookie is seen to have expired in cookie.inc.php, 
					      it is just renewed without any consequence.
					     */
					    document.cookie = 'USERAUTH=' + escape(returned_cookie);
					    
					    top.location.href = 'index.php';
					    
					},
					error : function(XMLHttpRequest, textStatus, errorThrown){
						$.updateTips('#logonMsg','error',XMLHttpRequest.responseText);
                                          
					}
				}); //end ajax retrieve date
 				return false;
			});

			
			
			/**
			 * forgot pwd dialog
			 */
			$('#dialog-recuperatePwd').dialog({
				autoOpen:false,
				buttons: {  
					"<?=$Text['btn_ok'];?>" : function(){
							$.ajax({
								type: "POST",
								url: '',
								success: function(txt){
									
								},
								error : function(XMLHttpRequest, textStatus, errorThrown){
									$.showMsg({
										msg:XMLHttpRequest.responseText,
										type: 'error'});
									
								}
							});
		
						
						},
							
					"<?=$Text['btn_close'];?>"	: function(){
							$( this ).dialog( "close" );
						}
				}
			});
			
	
				
			/**
			 *	incidents
			 */
			$('#newsWrap').xml2html('init',{
					url: 'php/ctrl/Incidents.php',
					params : 'oper=getIncidentsListing&filter=pastWeek&type=3',
					loadOnInit: true
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
		<p id="logonHeader"><span><?php 
            if (get_config('login_header_show_name', false)) {
                echo $Text['coop_name']; 
            } ?></span></p>
	</div>

	<div id="stagewrap" class="ui-widget">
		
		<div class="floatLeft aix-layout-splitW20 aix-layout-widget-left-col hidden">
			<div class="ui-widget-content ui-corner-all">
				<h4 class="ui-widget-header">Global info</h4>
			</div>
		</div>
		
		<div class="floatLeft aix-layout-splitW50 aix-layout-widget-center-col">
			<div id="newsWrap">
				<div class="portalPost">
					<h2 class="subject">{subject}</h2>
					<p class="info"><?php echo $Text['posted_by']; ?> {user_name} (<?php echo $Text['uf_short'] ;?>{uf_id}),  {ts} </p>
					<p class="msg">{details}</p>
				</div>
			</div>
		</div>
	
		
		<div id="logonWrap" class="aix-layout-splitW20">
			<div class="ui-widget-content ui-corner-all">
			<h4 class="ui-widget-header ui-corner-all"><?php echo $Text['login'];?></h4>
			<p id="logonMsg" class="user_tips  minPadding"></p>
			<form id="login" method="post" class="padding15x10">
				<table class="tblForms">
					<tr>
						<td><label class="formLabel" for="login"><?=$Text['logon'];?>:</label></td>
						<td><input type="text" class="inputTxtSmall ui-widget-content ui-corner-all " name="login" id="login"/></td>
					</tr>
					<tr>
						<td><label class="formLabel" for="password"><?=$Text['pwd'];?>:</label></td>
						<td><input type="password" class="inputTxtSmall ui-widget-content ui-corner-all" name="password" id="password"/></td>
					</tr>
					<tr>
						<td colspan="2"><div>&nbsp;</div></td>
					</tr>
					<tr>
						
						<td colspan="2">
							<div class="textAlignLeft">
								<button name="submitted" id="btn_logon"><?=$Text['btn_login'];?></button>
							</div>
						</td>
					</tr>
				</table>
				<input type="hidden" name="originating_uri" value="<?=(isset($_REQUEST['originating_uri']) ? $_REQUEST['originating_uri'] : 'login.php') ?>">
			</form>
		</div>
	</div><!-- end logonwrap -->
	
	
	
	</div><!-- end stagewrap -->
	


</div>
<div id="dialog-message" title="">
	<p class="minPadding ui-corner-all"></p>
</div>
<div id="dialog-recuperatePwd">
		<p>Please enter your email address here:</p>
		<input type="text" name="email" value="" />
</div>

</body>
</html>




