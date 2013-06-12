<?php include "inc/header.inc.php" ?>
<!DOCTYPE html> 
<html> 
	<head> 
	<title><?=$Text['global_title']; ?></title> 
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<link rel="stylesheet" href="js/jquery.mobile-1.0.1/jquery.mobile-1.0.1.min.css" />
	<script src="js/jquery/jquery-1.7.1.min.js"></script>
	<script src="js/jquery.mobile-1.0.1/jquery.mobile-1.0.1.min.js"></script>

	<script>
		//$( document ).delegate("#buy", "pageinit", function() {
		  //alert('A page with an ID of "aboutPage" was just created by jQuery Mobile!');
		//});
	
	</script>

</head> 
<body> 
<div data-role="page" id="home" data-title="Aixada Mobile Home">

	<div data-role="header">
		<h1>Welcome to Aixada Mobile</h1>
		
	</div><!-- /header -->
	
	<div data-role="content">	
		<p>What do you want to do?</p>
	
		<p><a href="shop_m.php" data-role="button" data-ajax="false">Buy stuff</a></p>
	
		<p><a href="order_m.php" data-role="button" data-ajax="false">Make an order</a></p>
	
		<p><a href="php/ctrl/Login.php?oper=logout" data-role="button" data-ajax="false">Logout</a></p>
			
	</div><!-- /content -->

</div><!-- /page -->


</body>
</html>