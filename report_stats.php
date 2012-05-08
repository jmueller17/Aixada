<?php include "inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_stats'] ;?></title>


	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
	<link rel="stylesheet" type="text/css"   media="print" href="css/print.css" />
	<link rel="stylesheet" type="text/css"   media="screen" href="js/fgmenu/fg.menu.css"   />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/jquery-ui/ui-lightness/jquery-ui-1.8.custom.css"/>
	
	<?php if (isset($_SESSION['dev']) && $_SESSION['dev'] == true ) { ?> 
	    <script type="text/javascript" src="js/jquery/jquery.js"></script>
		<script type="text/javascript" src="js/jquery/jquery-ui-1.8.custom.min.js"></script>
		<script type="text/javascript" src="js/fgmenu/fg.menu.js"></script>
		<script type="text/javascript" src="js/jquery/jquery.aixadaMenu.js"></script>     	 
	   	<script type="text/javascript" src="js/jquery/jquery.aixadaXML2HTML.js" ></script>
	   	<script type="text/javascript" src="js/jquery/jquery.aixadaUtilities.js" ></script>
   	<?php  } else { ?>
    	<script type="text/javascript" src="js/js_for_report_stats.min.js"></script>
    <?php }?>
	
   
	<script type="text/javascript">
	$(function(){

		
			

			/**
			 * 	MONITOR Money, daily stats, negative ufs, stock
			 */


  			 //balance
			 $('#dailyStats').xml2html('init',{
					url		: 'ctrlValidate.php',
					params	: 'oper=getIncomeSpendingBalance&date=undefined',
					loadOnInit: true,
                    autoReload: 100200, /*10000*/
			});
				
  			 //negative ufs
			 $('#negative_ufs tbody').xml2html('init',{
					url		: 'ctrlValidate.php',
					params	: 'oper=getNegativeAccounts',
					loadOnInit: true,
					rowName : 'account',
                    autoReload: 103020, /*10000*/
			});

			//negative stock
			 $('#min_stock tbody').xml2html('init',{
					url		: 'ctrlValidate.php',
					params	: 'oper=getProductsBelowMinStock',
					loadOnInit: true,
                    autoReload: 100010, /*10000*/
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
		<?php include "inc/menu2.inc.php" ?>
	</div>
	<!-- end of headwrap -->
	
	
	
	<div id="stagewrap">
	
		
		<div id="titlewrap" class="ui-widget">
			<div id="titleLeftCol">
		    	<h1><?php echo $Text['ti_stats']; ?> </h1>
		    </div>
		    
		</div>
	
			
		
		<div id="monitorGlobals" class="ui-widget oneThirdCol">
				<div class="ui-widget-content ui-corner-all">
					<h3 class="ui-widget-header ui-corner-all"><?php echo $Text['dailyStats']?> </h3>
					<div id="dailyStats">
						<p><?php echo $Text['totalIncome'];?>: {income}<br/>
						<?php echo $Text['totalSpending'];?>: {spending}<br/>
						<?php echo $Text['balance'];?>: {balance}</p>
					</div>
				</div>
		</div>
			
		<div id="monitorUFs" class="ui-widget oneThirdCol">
				<div class="ui-widget-content ui-corner-all">
					<h3 class="ui-widget-header ui-corner-all"><?php echo $Text['negativeUfs'];?></h3>
					
						<table id="negative_ufs" class="printStats table_listing" >
							<thead>
								<tr>
									<th><?php echo $Text['uf_short'];?></th>
									<th><?php echo $Text['name'];?></th>
									<th><?php echo $Text['balance'];?></th>
									<th><?php echo $Text['lastUpdate'];?></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>{uf}</td>
									<td>{name}</td>
									<td><span class="negativeBalance">{balance}</span></td>
									<td>{last_update}</td>
								</tr>
							</tbody>
						</table>
					
				</div>
			</div>
			
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