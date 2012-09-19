<?php


//require_once('FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
//ob_start(); // Starts FirePHP output buffering
//$firephp = FirePHP::getInstance(true);

require_once 'inc/cookie.inc.php';
require_once 'inc/authentication.inc.php';
require_once 'lib/exceptions.php';
require_once 'local_config/config.php';
require_once('utilities.php');

$default_theme = get_session_theme();
$language = get_session_language();
$dev = configuration_vars::get_instance()->development;
require_once('local_config/lang/' . $language  . '.php');


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
	
	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/<?=$default_theme;?>/jqueryui.css"/>
	
   <?php if (isset($dev) && $dev == true ) { ?> 
	    <script type="text/javascript" src="js/jquery/jquery.js"></script>
		<script type="text/javascript" src="js/jqueryui/jqueryui.js"></script>
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaXML2HTML.js" ></script>
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaUtilities.js" ></script>	
   	<?php  } else { ?>
	   	<script type="text/javascript" src="js/js_for_login.min.js"></script>
    <?php }?>
	   	
	
	   	
	<script type="text/javascript">
		$(function(){


			
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
			 *	incidents
			 */
			$('#newsWrap').xml2html('init',{
					url: 'ctrlIncidents.php',
					params : 'oper=getIncidentsListing&filter=pastWeek&type=3',
					loadOnInit: true,
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
		
		<div class="oneQuarterCol floatLeft"></div>
		
		<div id="middleCol" class="floatLeft">
			<div id="newsWrap">
				<div class="portalPost">
					<h2 class="subject">{subject}</h2>
					<p class="info">By {user_name} (UF{uf_id}),  on {ts} </p>
					<p class="msg">{details}</p>
				</div>
			</div>
		</div>
	
		
		<div id="logonWrap" class="ui-widget oneQuarterCol">
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
</body>
</html>




