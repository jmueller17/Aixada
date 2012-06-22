<?php include "inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title']; ?></title>

 	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
  	<link rel="stylesheet" type="text/css"   media="print"  href="css/print.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/aixadacart/aixadacart.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/fgmenu/fg.menu.css"   />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/<?=$default_theme;?>/jqueryui.css"/>
     
    
    <?php if (isset($_SESSION['dev']) && $_SESSION['dev'] == true ) { ?> 
	    <script type="text/javascript" src="js/jquery/jquery.js"></script>
		<script type="text/javascript" src="js/jqueryui/jqueryui.js"></script>
		<script type="text/javascript" src="js/fgmenu/fg.menu.js"></script>
		<script type="text/javascript" src="js/aixadautilities/jquery.aixadaMenu.js"></script>     	 
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaXML2HTML.js" ></script>
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaUtilities.js" ></script>
	   	<script type="text/javascript" src="js/aixadacart/jquery.aixadacart.js" ></script>   	    
   	<?php  } else { ?>
	   	<script type="text/javascript" src="js/js_for_index.min.js"></script>
    <?php }?>
     
    <script type="text/javascript" src="js/aixadacart/i18n/cart.locale-<?=$language;?>.js" ></script>
	   
	<script type="text/javascript">
	$(function(){

			$('#loadingMsg').hide();


			$( "#rightSummaryCol" ).tabs();

			$('#tmp').hide();



			/********************************************************
			 *      My ORDERS
			 ********************************************************/
			 
			var lastDate = '';
			$('#tbl_Orders tbody').xml2html('init',{
				url : 'ctrlOrders.php',
				params : 'oper=getOrdersListingForUf&filter=prevMonth', 
				loadOnInit : true, 
				rowComplete : function(rowIndex, row){
					var order_id = $(row).attr('orderId');
					if (order_id == ''){
						 $(row).children().eq(0).addClass('dim40');
						 $(row).children().eq(4).text('not yet sent');		
					}
					
					var date = $(row).children().eq(2).text();
					if (date != lastDate) $(row).before('<tr><td colspan="6" class="dateRow">Ordered for <span class="boldStuff">'+date+'</span></td></tr>');
					lastDate=date; 	
				}
			});

			//tmp table to load the order - shop comparison
			$('#tbl_diffOrderShop tbody').xml2html('init',{
				url : 'ctrlOrders.php',
				params : 'oper=getDiffOrderShop', 
				loadOnInit : false, 
				complete : function(rowCount){
					if (rowCount >0){
						var header = $('#tbl_diffOrderShop thead tr').clone()
						var itemRows = $('#tbl_diffOrderShop tbody tr').clone();
						$('#order_'+global_order_id).after(itemRows).after(header);
					} else {
						$('#order_'+global_order_id+' span').removeClass('ui-icon-minus').addClass('ui-icon-plus');
						$.showMsg({
							msg:'Sorry, no items have been found for your Uf and this order.',
							type: 'error'});
					}
				}
			});
			

			var global_order_id = 0; 
			$('.expandOrderIcon').live('click', function(){

				global_order_id = $(this).parents('tr').attr('orderId');
				if (global_order_id == '') return false; 
				
				if ($('span',this).hasClass('ui-icon-plus')){
					$('span',this).removeClass('ui-icon-plus').addClass('ui-icon-minus');

					if ($('.detail_'+global_order_id).exists()){
						$('.detail_'+global_order_id).show().prev().show();
					} else {
						$('#tbl_diffOrderShop tbody').xml2html('reload', {
							params : 'oper=getDiffOrderShop&order_id='+global_order_id, 
						});						
					}

					
				} else {
					$('span',this).removeClass('ui-icon-minus').addClass('ui-icon-plus');
					$('.detail_'+global_order_id).hide().prev().hide();
					
				}

				
				
				
				
			})

			
		
			//show purchases, validate and non validated
			$('#tbl_PastValidated tbody').xml2html('init',{
				url : 'ctrlReport.php',
				params : 'oper=getAllShopTimes', 
				loadOnInit : false, 
				rowComplete : function(rowIndex, row){	
					var last_td = $(row).children().last(); //change the text displayed when purchse has not been validated 
					if (last_td.text() == '0000-00-00 00:00:00') {
						last_td.text('<?php echo $Text["not_validated"];?>');
						last_td.prev().html('<span class="ui-icon ui-icon-minus"></span>');
					}
				},
			});

			
			//load the purchase details and show in dialog box
			$('.shopId').live('click',function(){	
				$('#loadingMsg').show();
				
				$('#cartLayer').aixadacart('resetCart');
				var shop_id = $(this).parent().prev().html();
				
				//reload the list
				$('#cartLayer').aixadacart('loadCart',{
					loadCartURL : 'ctrlReport.php?oper=getShoppedItems&shop_id='+shop_id
				}); //end loadCart

				$( "#dialog-message" )
					.dialog({ title: shop_id })
					.dialog("open");
			});	
			
			//init cart 
			$('#cartLayer').aixadacart("init",{
				saveCartURL : 'ctrlValidate.php',
				cartType	: 'simple',
				btnType		: 'hidden',
				loadSuccess : function(){
					$('input').attr('disabled','disabled');
					$('#loadingMsg').hide();
					$('.ui-icon-close').hide();
				}
			});

			$( "#dialog-message" ).dialog({
				modal: true,
				width:600,
				height:480,
				autoOpen:false,
				buttons: {
					Ok: function() {
						$( this ).dialog( "close" );
					}
				}
			});

			$('.iconContainer')
			.live('mouseover', function(e){
				$(this).addClass('ui-state-hover');
			})
			.live('mouseout', function (e){
				$(this).removeClass('ui-state-hover');
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
		<div id="homeWrap">
			<div id="leftIconCol">
				<div class="homeIcon">
					<a href="shop_and_order.php?what=Shop"><img src="img/cesta.png"/></a>
					<p><a href="shop_and_order.php?what=Shop"><?php echo $Text['icon_purchase'];?></a></p>
				</div>
				<div class="homeIcon">
					<a href="shop_and_order.php?what=Order"><img src="img/pedido.png"/></a>
					<p><a href="shop_and_order.php?what=Order"><?php echo $Text['icon_order'];?></a></p>
				</div>
				<div class="homeIcon">
					<a href="incidents.php"><img src="img/incidencias.png"/></a>
					<p><a href="incidents.php"><?php echo $Text['icon_incidents'];?></a></p>
				</div>
			</div>
			<div id="rightSummaryCol">
				<ul>
					<li><a href="#tabs-1"><h2>My Order(s)</h2></a></li>
					<li><a href="#tabs-2"><h2>My Purchase(s)</h2></a></li>	
				</ul>
			
				<div id="tabs-1">
					<table id="tbl_Orders" class="">
						<thead>
							<tr>
								<th colspan="2"></th>
								<th>Closes in #days</th>
								<th>Status</th>
								<th>Total</th>
							</tr>
						</thead>
						<tbody>
							<tr id="order_{id}" orderId="{id}">
								<td><p class="iconContainer ui-corner-all ui-state-default expandOrderIcon"><span class="ui-icon ui-icon-plus"></span></p></td>
								<td>{provider_name}</td>
								<td class="hidden">{date_for_order}</td>
								<td class="textAlignCenter">{time_left}</td>
								<td  class="textAlignCenter">not revised</td>
								<td class="textAlignRight">{order_total}€</td>
							</tr>
						</tbody>
					</table>
				</div>
				
				<div id="tabs-2">
				
				
				</div>
			
			
				
			
		
			</div>			
		</div>
	</div>
	<!-- end of stage wrap -->
</div>

<div id="tmp">
<table id="tbl_diffOrderShop" class="">
	<thead>
		<tr>
			<th>id</th>
			<th>Product</th>
			<th>Order-qu</th>
			<th>Shop-qua</th>
			<th>Price</th>		
		</tr>
	</thead>
	<tbody>
		<tr class="detail_{order_id}">
			<td>{product_id}</td>
			<td>{product_name}</td>
			<td>{order_quantity}</td>
			<td>{shop_quantity}</td>
			<td>{unit_price}</td>
			
		</tr>
	</tbody>
</table>
				
</div>



<!-- end of wrap -->
<div id="dialog-message" title="">
		 <p id="loadingMsg" class="ui-state-highlight"><?php echo $Text['loading'];?></p>
		 <div id="cartLayer"></div>
</div>

<!-- / END -->
</body>
</html>