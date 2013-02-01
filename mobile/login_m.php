<?php
/*
 * implementation from George Schlossnagle, Advanced PHP Programming, p.341
 */



require_once 'inc/cookie.inc.php';
require_once 'inc/authentication.inc.php';
require_once 'lib/exceptions.php';
require_once 'local_config/config.php';
$language = ( (isset($_SESSION['userdata']['language']) and 
               	$_SESSION['userdata']['language'] != '') ? 
              	$_SESSION['userdata']['language'] : 
              	configuration_vars::get_instance()->default_language );
require_once('local_config/lang/' . $language . '.php');



$use_session_cache = configuration_vars::get_instance()->use_session_cache;

if (!isset($_SESSION)) {
    session_start();
}

$_SESSION['dev'] = true;

$lang = configuration_vars::get_instance()->default_language; 
?>
<!DOCTYPE html> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$lang?>" lang="<?=$lang?>">
	<head> 
	<title> <?=$Text['global_title']; ?></title> 
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<link rel="stylesheet" href="js/jquery.mobile-1.0.1/jquery.mobile-1.0.1.min.css" />
	<script src="js/jquery/jquery-1.7.1.min.js"></script>
	<script src="js/jquery.mobile-1.0.1/jquery.mobile-1.0.1.min.js"></script>
	
	<script>
		$(document).bind( "pagebeforechange", function( e, data ) {

			$('#loginForm').submit(function(){
				var dataSerial = $(this).serialize();
				$.ajax({
					type: "POST",
                	url: "php/ctrl/Login.php?oper=login",
					data:dataSerial,		
					success: function(msg){			
                                    top.location.href = 'index_m.php';
					},
					error : function(XMLHttpRequest, textStatus, errorThrown){
						$.updateTips('#logonMsg','error',XMLHttpRequest.responseText);
                                      
					}
				}); //end ajax retrieve date
				return false;
			});
		});
	
	</script>
	
</head> 
<body> 

<div data-role="page">

	<div data-role="header">
		<h1>Login to ... </h1>
	</div><!-- /header -->

	<div data-role="content">	
		<form action="php/ctrl/Login.php?oper=login" method="post" id="loginForm">
			<label for="login" class="ui-hidden-accessible">Username:</label>
			<input type="text" name="login" id="login" value="" placeholder="Username"/><br/>
			
			<label for="password" class="ui-hidden-accessible">Password:</label>
			<input type="password" name="password" id="password" value="" placeholder="Password"/>
		
			<button type="submit" name="submit" value="submit-value">Submit</button>
		</form>	
	</div><!-- /content -->

</div><!-- /page -->

</body>
</html>