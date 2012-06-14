<?php include "inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title']; ?> Manage Orders - Overview</title>

 	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/fgmenu/fg.menu.css"   />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/<?=$default_theme;?>/jqueryui.css"/>
    <link rel="stylesheet" type="text/css" 	 media="screen" href="js/tablesorter/themes/blue/style.css"/>
    
    <?php if (isset($_SESSION['dev']) && $_SESSION['dev'] == true ) { ?> 
	    <script type="text/javascript" src="js/jquery/jquery.js"></script>
		<script type="text/javascript" src="js/jqueryui/jqueryui.js"></script>
		<script type="text/javascript" src="js/fgmenu/fg.menu.js"></script>
		<script type="text/javascript" src="js/aixadautilities/jquery.aixadaMenu.js"></script>     	 
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaXML2HTML.js" ></script>   	
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaUtilities.js" ></script>
	   	<script type="text/javascript" src="js/tablesorter/jquery.tablesorter.js" ></script>
	   	

	   	  
   	<?php  } else { ?>
	   	<script type="text/javascript" src="js/js_for_manage_orders.min.js"></script>
    <?php }?>
     
	   
	<script type="text/javascript">
	$(function(){

			$('#loadingMsg').hide();
	

			$('#tbl_orderOverview tbody').xml2html('init',{
				url : 'ctrlOrders.php',
				params : 'oper=getOrdersListing&filter=prevMonth', 
				loadOnInit : true, 
				rowComplete : function (rowIndex, row){
					var orderId = $(row).attr("id");
					var timeLeft = parseInt($(row).children().eq(3).text());
					
					if (orderId > 0 || timeLeft <= 0){ // order is closed
						$('#orderClosedIcon'+orderId).removeClass('ui-icon-unlocked').addClass('ui-icon-locked');
					} else {
						//while open and not send off, no order_id exists
						$(row).children(':first').html('<p>-</p>');
						$(row).children().eq(5).html('<p class="ui-state-highlight minPadding"><span class="ui-icon ui-icon-alert floatLeft"></span>not yet send to provider</p>');
					}
					 	
				},
				complete : function (rowCount){
					$("#tbl_orderOverview").trigger("update"); 
				}
			});


			$("#tbl_orderOverview").tablesorter(); 

			$('.iconContainer')
				.live('mouseover', function(e){
					$(this).addClass('ui-state-hover');
				})
				.live('mouseout', function (e){
					$(this).removeClass('ui-state-hover');
				});

			$('.ui-icon-cart')
				.live('click', function(e){
					var id = $(this).parents('tr').attr('id');
					alert(id);
				});

			/**
			$('tbody tr')
				.live('mouseover', function(e){
					$(this).addClass('ui-state-hover');
				})
				.live('mouseout', function(e){
					$(this).removeClass('ui-state-hover');
				});**/
			//$('tbody tr:even').addClass('ui-state-hover');
			
			

		//$('#test').hicon();	
			
	});  //close document ready
</script>


</head>
<body>
<div id="wrap">
	<div id="headwrap">
		<?php include "inc/menu2.inc.php" ?>
	</div>
	<!-- end of headwrap -->
	
	<div id="stagewrap">
	
		<div id="titlewrap" class="ui-widget">
			<div id="titleLeftCol">
		    	<h1>Manage orders</h1>
		    </div>
		   	<div id="titleRightCol">
								
		   	</div> 	
		</div> <!--  end of title wrap -->
	
		<div id="orderOverview" class="ui-widget">
			<div class="ui-widget-header ui-corner-all">
				<p>&nbsp;</p>
			</div>
			<div class="ui-widget-content ui-corner-all">
			<table id="tbl_orderOverview" class="tblOverviewOrders">
				<thead>
					<tr>
						<th class="clickable">id <span class="ui-icon ui-icon-triangle-2-n-s floatRight"></span></th>
						<th class="clickable">Ordered for <span class="ui-icon ui-icon-triangle-2-n-s floatRight"></span></th>
						<th class="clickable">Provider <span class="ui-icon ui-icon-triangle-2-n-s floatRight"></span></th>
						<th>Days left</th>
						<th>&nbsp;</th>
						<th>Send off to provider</th>
						<th class="clickable">Shop date <span class="ui-icon ui-icon-triangle-2-n-s floatRight"></span></th>
						<th>Total</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<tr id="{id}">
						<td>{id}</td>
						<td class="textAlignCenter">{date_for_order}</td>
						<td class="textAlignRight minPadding">{provider_name}</td>
						<td class="textAlignCenter">{time_left}</td>
						<td class="textAlignCenter"><span id="orderClosedIcon{id}" class="tdIconCenter ui-icon ui-icon-unlocked"></span></td>
						<td><span class="floatRight">{ts_send_off}</span> <span class="ui-icon ui-icon-check floatRight"></span>&nbsp;</td>
						<td>{date_for_shop}</td>
						<td class="textAlignRight">{order_total} €</td>
						<td class="textAlignCenter">
							<p class="ui-corner-all iconContainer ui-state-default floatLeft"><span class="ui-icon ui-icon-cart" title="Set shop date"></span></p>
							<p class="ui-corner-all iconContainer ui-state-default floatRight"><span class="ui-icon ui-icon-check" title="Revise order"></span></p>
						</td>
					</tr>
				</tbody>
				<tfoot>
				
				</tfoot>
			</table>
			</div> <!-- widget content -->
		</div>
		
	<p id="test"></p>
	
	</div>
	<!-- end of stage wrap -->
</div>
<!-- end of wrap -->
<!-- / END -->
</body>
</html>













