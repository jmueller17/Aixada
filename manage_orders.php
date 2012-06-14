<?php include "inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title']; ?> Manage Orders - Overview</title>

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
	   	<script type="text/javascript" src="js/js_for_manage_orders.min.js"></script>
    <?php }?>
     
	   
	<script type="text/javascript">
	$(function(){

			$('#loadingMsg').hide();
	

			$('#tbl_orderOverview tbody').xml2html('init',{
				url : 'ctrlOrders.php',
				params : 'oper=getOrdersListing&filter=prev3month', 
				loadOnInit : true, 
				rowComplete : function (rowIndex, row){
					var orderId = $(row).attr("id");

					if (orderId > 0){ // order is closed
						$('#orderClosedIcon'+orderId).removeClass('ui-icon-unlocked').addClass('ui-icon-locked');
					} else {
						//while open and not send off, no order_id exists
						$(row).children(':first').html('<p>-</p>');
						$(row).children().eq(5).html('<p class="ui-state-highlight minPadding"><span class="ui-icon ui-icon-alert floatLeft"></span>not yet send to provider</p>');
					}
					 	
				}
			});


			$('.iconContainer')
				.live('mouseover', function(e){
					$(this).addClass('ui-state-hover');
				})
				.live('mouseout', function (e){
					$(this).removeClass('ui-state-hover').addClass('ui-state-default');
				});

			$('.ui-icon-cart')
				.live('click', function(e){
					var id = $(this).parents('tr').attr('id');
					alert(id);
				});
						
			
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
			<table id="tbl_orderOverview" class="table_listing">
				<thead>
					<tr>
						<th>id</th>
						<th>Ordered for</th>
						<th>Provider</th>
						<th>Days left</th>
						<th>&nbsp;</th>
						<th>Send off to provider</th>
						<th>Shop date</th>
						<th>Total</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<tr id="{id}">
						<td>{id}</td>
						<td>{date_for_order}</td>
						<td>{provider_name}</td>
						<td><p class="textAlignCenter">{time_left}</p></td>
						<td><span id="orderClosedIcon{id}" class="tdIconCenter ui-icon ui-icon-unlocked"></span></td>
						<td><span class="floatRight">{ts_send_off}</span> <span class="ui-icon ui-icon-check floatRight"></span>&nbsp;</td>
						<td>{date_for_shop}</td>
						<td>{order_total}</td>
						<td>
							<p class="ui-corner-all iconContainer ui-state-default floatLeft"><span class="ui-icon ui-icon-cart" title="Set shop date"></span></p>
							<p class="ui-corner-all iconContainer ui-state-default floatLeft"><span class="ui-icon ui-icon-pencil" title="Revise order"></span></p>
						</td>
					</tr>
				</tbody>
				<tfoot>
				
				</tfoot>
			</table>
			</div> <!-- widget content -->
		</div>
		
	
	
	</div>
	<!-- end of stage wrap -->
</div>
<!-- end of wrap -->
<!-- / END -->
</body>
</html>













