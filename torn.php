<?php include "php/inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_torn']; ?></title>

 	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/fgmenu/fg.menu.css"   />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/<?=$default_theme;?>/jqueryui.css"/>
     
    <script type="text/javascript" src="js/jquery/jquery.js"></script>
    <script type="text/javascript" src="js/jqueryui/jqueryui.js"></script>
    <?php echo aixada_js_src(); ?>
	   
	<script type="text/javascript">
	$(function(){
		$.ajaxSetup({ cache: false });

		$('button')
			.button()
			.click(function(e){

				var btnId = $(this).attr('id'); 
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
		<?php include "php/inc/menu.inc.php" ?>
	</div>
	
	<!-- end of headwrap -->
	<div id="stagewrap">
	
		<div class="aix-layout-center60 ui-widget"> 
			
			<div class="aix-style-entry-widget">
				<table>
					<tr>
						<td>
							<button class="aix-layout-fixW150" id="btn_nav_validate"><?php echo $Text['nav_wiz_validate']; ?></button>
						</td>
						<td><p><?php echo $Text['desc_validate']; ?></p></td>
					</tr>
				</table>
			</div>
	
			<div class="aix-style-entry-widget">
				<h2><?php echo $Text['primer_torn'];?></h2>
				<table>
					<tr>
						<td>
							<button class="aix-layout-fixW150" id="btn_nav_revise"><?php echo $Text['nav_wiz_revise_order']; ?></button>
						</td>
						<td><p><?php echo $Text['desc_revise']; ?></p></td>
					</tr>
					
					<tr>
						<td>
							<button class="aix-layout-fixW150" id="btn_nav_cash"><?php echo $Text['nav_wiz_cashbox']; ?></button>
						</td>
						<td><p><?php echo $Text['desc_cashbox']; ?></p></td>
					</tr>
				</table>
			</div>		

			<div class="aix-style-entry-widget">
				<h2><?php echo $Text['segon_torn']; ?></h2>
				<table>
					<tr>
						<td>
							<button class="aix-layout-fixW150" id="btn_nav_stock"><?php echo $Text['nav_mng_stock']; ?></button>
						</td>
						<td><p><?php echo $Text['desc_stock']; ?></p></td>
					</tr>
					
					<tr>
						<td>
							<button class="aix-layout-fixW150" id="btn_nav_orders"><?php echo $Text['nav_mng_orders']; ?></button>
						</td>
						<td><p><?php echo $Text['desc_print_orders']; ?></p></td>
					</tr>
					<tr>
						<td>
							<button class="aix-layout-fixW150" id="btn_nav_stats"><?php echo $Text['nav_report_status']; ?></button>
						</td>
						<td><p><?php echo $Text['desc_stats']; ?></p></td>
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