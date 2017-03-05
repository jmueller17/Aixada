<?php 
	include "php/inc/header.inc.php";
	require_once(__ROOT__.'php/lib/account_writers.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_stats'];?></title>

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
			 *	incidents
			 */
			$('#tbl_incidents tbody').xml2html('init',{
					url: 'php/ctrl/Incidents.php',
					params : 'oper=getIncidentsListing&filter=today',
					loadOnInit: true
			});



			/**
			 * 	MONITOR Money, daily stats, negative ufs, stock
			 */

			//negative stock
			 $('#min_stock tbody').xml2html('init',{
					url		: 'php/ctrl/Shop.php',
					params	: 'oper=getProductsBelowMinStock',
					rowComplete : function(rowIndex, row){
						//reformat numbers to two decimal places
						var currentStock = new Number($(row).children().last().text());
						var minStock = new Number($(row).children().eq(3).text());
						
						$(row).children().last().text(currentStock.toFixed(2));
						$(row).children().eq(3).text(minStock.toFixed(2));
					},
					loadOnInit: true,
                    autoReload: 100010 /*10000*/
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
	
	
	<div id="stagewrap" class="ui-widget">
	
		<div id="titlewrap">
			<div id="titleLeftCol">
		    	<h1><?=$Text['ti_report_torn']; ?></h1>
		    </div>
		</div>
		
		<div id="incidents_listing ui-widget-content">
					<h3 class="ui-widget-header ui-corner-all"><?php echo $Text['ti_report_incidents'];?> </h3>
					<div id="tbl_div2">
					<table id="tbl_incidents" class="ui-widget">
					<thead>
						<tr>
							<th class="mwidth-30"><?php echo $Text['id'];?></th>
							<th><?php echo $Text['priority'];?></th>
							<th class="mwidth-150"><?php echo $Text['created_by'];?></th>
							<th class="mwidth-150"><?php echo $Text['created'];?></th>
							<th><?php echo $Text['status'];?></th>
							<!-- th><?php echo $Text['incident_type'];?></th-->
							<th><?php echo $Text['provider_name'];?></th>
							<th><?php echo $Text['ufs_concerned'];?></th>
							<th><?php echo $Text['comi_concerned'];?></th>
							
						</tr>
					</thead>
					<tbody>
						<tr>

							<td field_name="incident_id">{id}</td>
							<td field_name="priority">{priority}</td>
							<td field_name="operator">{uf_id} {user_name}</td>
							<td field_name="date_posted">{ts}</td>
							<td field_name="status">{status}</td>
							<!-- td field_name="type">{distribution_level}</td-->
							<td field_name="provider">{provider_concerned}</td>
							<td field_name="ufs_concerned">{ufs_concerned}</td>
							<td field_name="commission">{commission_concerned}</td>
						</tr>
						<tr>
							<td class="noBorder"></td>
							<td class="noBorder"><?php echo $Text['subject'];?>:</td>
							<td colspan="10" class="noBorder">{subject}</td>
						</tr>
						<tr>
							<td class="noBorder"></td>
							<td class="noBorder"><?php echo $Text['message'];?>:</td>
							<td class="noBorder"colspan="10">{details}</td>
							
						</tr>
						<tr><td colspan="12" class="noBorder"><p class="spacingEnd">&nbsp;</p></td></tr>
					</tbody>
					</table>
					</div>
					
					
			
		</div>


		
	</div>


<!-- now the statistics -->

<div/>
<br/><br/>	
			
		
		<?php write_dailyStats("oneThirdCol floatLeft"); ?>
		<?php write_negative_ufs("oneThirdCol floatLeft"); ?>
			
			<div id="monitorStock" class="ui-widget oneThirdCol floatLeft">
				<div class="ui-widget-content ui-corner-all">
					<h3 class="ui-widget-header ui-corner-all"><?php echo $Text['negativeStock'];?></h3>
					
						<table id="min_stock" class="printStats table_listing">
							<thead>
								<tr>
									<th><?php echo $Text['id'];?></th>
									<th><?php echo $Text['product_name'];?></th>
									<th><?php echo $Text['provider_name'];?></th>
									<th><?php echo $Text['minStock'];?></th>
									<th><?php echo $Text['curStock'];?></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>{id}</td>
									<td>{stock_item}</td>
									<td>{stock_provider}</td>
									<td>{stock_min}</td>
									<td class="negativeBalance">{stock_actual}</td>
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