<?php include "php/inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title']; ?></title>

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
	   	<script type="text/javascript" src="js/js_for_torn.min.js"></script>
    <?php }?>
     
	   
	<script type="text/javascript">
	$(function(){

		$('button')
			.button()
			.click(function(e){

				btnId = $(this).attr('id'); 
				switch(btnId){
					case 'btn_nav_validate':
						window.location.href = "validate.php";
						break;
						
					case 'btn_nav_revise':
						window.location.href = "manage_orders.php?filter=ordersForToday&lastPage=torn.php";
						break;

					case 'btn_nav_cash':
						window.location.href = "manage_money.php"; 
						break;

					case 'btn_nav_stock':
						window.location.href = "manage_stock.php";
						break;

					case 'btn_nav_orders':
						window.location.href = "manage_orders.php?filter=nextWeek&lastPage=torn.php";
						break;

					case 'btn_nav_stats':
						window.location.href = "report_torn.php";
						break;
				}

			});



		
			
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
	
		<div class="aix-layout-center60 ui-widget"> 
			
			<div class="aix-style-torn-widget">
				<table>
					<tr>
						<td>
							<button class="aix-layout-fixW150" id="btn_nav_validate">Validate</button>
						</td>
						<td><p>Validate shopping carts.</p></td>
					</tr>
				</table>
			</div>
	
			<div class="aix-style-torn-widget">
				<h2>1st Shift</h2>
				<table>
					<tr>
						<td>
							<button class="aix-layout-fixW150" id="btn_nav_revise">Revise</button>
						</td>
						<td><p>Revise orders and distribute to shopping carts</p></td>
					</tr>
					
					<tr>
						<td>
							<button class="aix-layout-fixW150" id="btn_nav_cash">Cashbox</button>
						</td>
						<td><p>Revise and set starting balance for this shift</p></td>
					</tr>
				</table>
			</div>		

			<div class="aix-style-torn-widget">
				<h2>2nd Shift</h2>
				<table>
					<tr>
						<td>
							<button class="aix-layout-fixW150" id="btn_nav_stock">Stock</button>
						</td>
						<td><p>Add and/or control stock of products</p></td>
					</tr>
					
					<tr>
						<td>
							<button class="aix-layout-fixW150" id="btn_nav_orders">Orders</button>
						</td>
						<td><p>Print and download orders for next week</p></td>
					</tr>
					<tr>
						<td>
							<button class="aix-layout-fixW150" id="btn_nav_stats">Stats</button>
						</td>
						<td><p>Download incidents, negative ufs, products with negative stock</p></td>
					</tr>
					
				</table>
			</div>				
		</div>
		
	
	<!-- end of stage wrap -->
	</div>
</div>
<!-- end of wrap -->

<!-- / END -->
</body>
</html>