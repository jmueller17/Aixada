<?php


define('DS', DIRECTORY_SEPARATOR);
define('__ROOT__', dirname(__FILE__).DS); 

require_once(__ROOT__ . 'php'.DS.'inc'.DS.'cookie.inc.php');
require_once(__ROOT__ . 'php'.DS.'inc'.DS.'authentication.inc.php');
require_once(__ROOT__ . 'php'.DS.'lib'.DS.'exceptions.php');
require_once(__ROOT__ . 'local_config'.DS.'config.php');
require_once(__ROOT__ . 'php'.DS.'utilities'.DS.'general.php');


$lang = get_session_language();

require_once(__ROOT__ . 'local_config'.DS.'lang'.DS.'' . $lang  . '.php');


if (!isset($_SESSION)) {
    session_start();
 }

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$lang;?>" lang="<?=$lang;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title> <?php print $Text['global_title'] . " - " . $Text['ti_login_news'];?> </title>

	<link href="js/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/aixcss.css" rel="stylesheet">
    <link href="js/ladda/ladda-themeless.min.css" rel="stylesheet">   	


   	<script type="text/javascript" src="js/jquery/jquery.js"></script>
	<script type="text/javascript" src="js/bootstrap/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaXML2HTML.js" ></script>
	   	
	<script type="text/javascript" src="js/bootbox/bootbox.js"></script>
	<script type="text/javascript" src="js/ladda/spin.min.js"></script>
	<script type="text/javascript" src="js/ladda/ladda.min.js"></script>
	 		
   
	
	
	   	
	<script type="text/javascript">
		$(function(){

			document.cookie = 'USERAUTH=';


			bootbox.setDefaults({
				locale:"<?=$lang;?>"
			})

			
			/**
			 *	logon stuff
			 */

			$('#login').submit(function(){
				var dataSerial = $(this).serialize();
				
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
						bootbox.alert({
							title : "Warning",
							message : "<div class='alert alert-warning'>"+XMLHttpRequest.responseText+"</div>"
						});	
                                          
					}
				}); //end ajax retrieve date
 				return false;
			});

			
		

		});
	</script>    
	
</head>
<body>


	<div class="container">
		<div class="row">
			<div class="col-md-3">
    <form id="login" method="post" class="form-signin">       
      <h2 class="form-signin-heading">Dev3.0 <?php echo $Text['login'];?></h2>
      <input type="text" class="form-control" name="login" placeholder="<?=$Text['logon'];?>" required="" autofocus="" />
      <input type="password" class="form-control" name="password" placeholder="<?=$Text['pwd'];?>" required=""/>      
      <br>
      <button class="btn btn-lg btn-primary btn-block" type="submit"><?=$Text['btn_login'];?></button>   
	  <input type="hidden" name="originating_uri" value="<?=(isset($_REQUEST['originating_uri']) ? $_REQUEST['originating_uri'] : 'login.php') ?>">
    </form>
  </div>
</div>
  </div>

		
	

</body>
</html>




