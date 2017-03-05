<?php 
	include "php/inc/header.inc.php";
	require_once(__ROOT__.'php/lib/account_writers.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_stats'] ;?></title>


	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
  	<link rel="stylesheet" type="text/css"   media="print"  href="css/print.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/fgmenu/fg.menu.css"   />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/<?=$default_theme;?>/jqueryui.css"/>
	
    <script type="text/javascript" src="js/jquery/jquery.js"></script>
    <script type="text/javascript" src="js/jqueryui/jqueryui.js"></script>
    <?php echo aixada_js_src(); ?>
   
	<script type="text/javascript">
	$(function(){
		$.ajaxSetup({ cache: false });

			/**
			 * 	MONITOR Money, daily stats, negative ufs, stock
			 */

			//negative stock
			 $('#min_stock tbody').xml2html('init',{
					url		: 'php/ctrl/Shop.php',
					params	: 'oper=getProductsBelowMinStock',
					loadOnInit: true,
                    autoReload: 100010 /*10000*/
			});


			$('.right-icons')
			.bind("mouseenter", function(){
				$(this).removeClass('ui-icon-triangle-1-s').addClass('ui-icon-circle-triangle-s');
			})
			.bind("mouseleave", function(){
				$(this).removeClass('ui-icon-circle-triangle-s').addClass('ui-icon-triangle-1-s');
			})
			.bind("click", function(){
				$(this).parent().next().toggle();
			});

			
	});  //close document ready
</script>

</head>
<?php flush(); ?>
<body>
<div id="wrap">
	<div id="headwrap">
		<?php include "php/inc/menu.inc.php" ?>
	</div>
	<!-- end of headwrap -->
	
	
	
	<div id="stagewrap">
	
		
		<div id="titlewrap" class="ui-widget">
			<div id="titleLeftCol">
		    	<h1><?php echo $Text['ti_stats']; ?> </h1>
		    </div>
		    
		</div>
	
		<?php write_dailyStats("oneThirdCol"); ?>
		<?php write_negative_ufs("oneThirdCol"); ?>
			
			<div id="monitorStock" class="ui-widget oneThirdCol">
				<div class="ui-widget-content ui-corner-all">
					<h3 class="ui-widget-header ui-corner-all"><?php echo $Text['negativeStock'];?></h3>
					
						<table id="min_stock" class="printStats table_listing">
							<thead>
								<tr>
									<th><?php echo $Text['id'];?></th>
									<th><?php echo $Text['product_name'];?></th>
									<th><?php echo $Text['minStock'];?></th>
									<th><?php echo $Text['curStock'];?></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>{id}</td>
									<td>{stock_item}</td>
									<td>{stock_min}</td>
									<td><span class="negativeBalance">{stock_actual}</span></td>
								</tr>
							</tbody>
						</table>
					
				</div>
			</div>
			
		
		
	</div>
	<!-- end of stage wrap -->
</div>
<!-- end of wrap -->
<!-- / END -->
</body>
</html>