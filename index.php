<?php include "php/inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title']; ?></title>

 	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
  	<link rel="stylesheet" type="text/css"   media="print"  href="css/print.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/aixadacart/aixadacart.css?v=<?=aixada_js_version();?>" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/fgmenu/fg.menu.css"   />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/<?=$default_theme;?>/jqueryui.css"/>
     
	<style>
		.ui-state-disabled a {pointer-events: none;}
		table.tblListingDefault td.MyOrderItem {vertical-align: top;}
		.has_notes div {
		    margin:1px .5em;
		    padding:0 2px;
		    border:dotted #777 1px;
		}
	</style>
    
    <script type="text/javascript" src="js/jquery/jquery.js"></script>
    <script type="text/javascript" src="js/jqueryui/jqueryui.js"></script>
    <?php echo aixada_js_src(); ?>
    <script type="text/javascript" src="js/aixadacart/jquery.aixadacart.js?v=<?=aixada_js_version();?>" ></script>
    <script type="text/javascript" src="js/aixadacart/i18n/cart.locale-<?=$language;?>.js?v=<?=aixada_js_version();?>" ></script>
     
   	<script type="text/javascript" src="js/jqueryui/i18n/jquery.ui.datepicker-<?=$language;?>.js" ></script> 
	   
	<script type="text/javascript">
	$(function(){
		$.ajaxSetup({ cache: false });

			//loading animation
			$('.loadSpinner').attr('src', "img/ajax-loader-<?=$default_theme;?>.gif").hide(); 
				
			
			//sql result set limit for order
			var gOrderLimit = 10; 

			//index
			var gOrderLimitIndex = 0; 


			//sql result set limti for purchase
			var gShopLimit = 10; 


			var gShopLimitIndex = 0; 

			$('#loadingMsg').hide();

			$( "#rightSummaryCol" ).tabs({
				select : function (e, ui){
					if ($(this).tabs( "option", "selected" ) == 0){
						$('#tbl_Shop tbody').xml2html('reload'); //load purchase list when switching tabs
					}
				}
	
			});

			$('#tmp').hide();


			// To apply menu access rights to icons
			var role =  $("#role_select option:selected").val();
			if (typeof(role) == "string" ) {
				$.ajax({
					type: "POST",
					url: "php/ctrl/SmallQ.php?oper=configMenu&user_role="+role,
					dataType: "xml", 
					success: function(xml){
						$(xml).find('navigation').children().each( function(){
							var tag = $(this)[0].tagName; 
							var val = $(this).text();
							if (val == 'disable') {
								$('.index_'+tag).addClass('ui-state-disabled');
							} else if (val == 'enable') {
								$('.index_'+tag).removeClass('ui-state-disabled');
							}
						} );
					}
				});
			}
	

			/********************************************************
			 *      My ORDERS
			 ********************************************************/

			//show older orders dates			
				$('#btn_prevOrders').button({
					icons : {
						primary:"ui-icon-seek-prev"
						}
				})
				.click( function(e){
						gOrderLimitIndex++;
						$('#tbl_Orders tbody').xml2html('reload',{
							url : 'php/ctrl/Orders.php',
							params : 'oper=getOrdersListingForUf&uf_id=-1&filter=all&limit='+getOrderLimit(gOrderLimitIndex)
						});

				});

				//show more recent order dates
				$('#btn_nextOrders').button({
					icons : {
						secondary:"ui-icon-seek-next"
						},
					disabled: true
				})
				.click( function(e){
					gOrderLimitIndex--;
					$('#tbl_Orders tbody').xml2html('reload',{
						url : 'php/ctrl/Orders.php',
						params : 'oper=getOrdersListingForUf&uf_id=-1&filter=all&limit='+getOrderLimit(gOrderLimitIndex)
					});

				});
			 
			 
			var lastDate = '';

			//load the current orders by provider. introduces a date row when date changes
			$('#tbl_Orders tbody').xml2html('init',{
				url : 'php/ctrl/Orders.php',
				params : 'oper=getOrdersListingForUf&uf_id=-1&filter=pastMonths2Future',
				loadOnInit : true, 
				beforeLoad : function(){
					$('.loadSpinner').show();
				},
				rowComplete : function(rowIndex, row){
					var orderId = $(row).attr('orderId');
					var timeLeft = $(row).children().eq(2).text();
					
					var revisionStatus = $(row).attr('revisionStatus');
					
					if (orderId > 0){ //order has been sent
						var st = formatOrderStatus(revisionStatus);
						$(row).children().eq(3).addClass(st[1]).html('<p class="textAlignCenter">'+st[0]+'</p>');
								
					} else {
						 $(row).children().eq(3).html("<p class='textAlignCenter'><?=$Text["not_yet_sent"];?></p>");		
					} 

					if (timeLeft < 0){
						$(row).children().eq(2).html('<span class="ui-icon ui-icon-locked tdIconCenter" title="order is closed"></span>');
					}

					
					//create date heading row
					var date = $(row).attr('dateForOrder');
					if (date != lastDate) $(row).before('<tr><td colspan="6">&nbsp;</td></tr><tr><td colspan="5"><p class="overviewDateRow"><?=$Text['ordered_for'];?> <span class="boldStuff">'+$.getCustomDate(date, "D d M, yy")+'</span></p></td><td><p class="ui-corner-all iconContainer ui-state-default printOrderIcon" dateForOrder="'+date+'"><span class="ui-icon ui-icon-print" title="Print order"></span></p></td></tr>');
					lastDate=date; 	

				},
				complete : function(rowCount){
					if (rowCount == 0){
						$.showMsg({
							msg:"<?php echo $Text['msg_err_noorder']; ?>",
							type: 'warning'});	
					}
					$('.loadSpinner').hide();
					
				}
			});



			/**
			 * load and show the order-shop comparison 
			 */
			function loadOrderDetails(orderId, dateForOrder, providerId){

				$('#tbl_diffOrderShop').attr('currentOrderId','');
				$('#tbl_diffOrderShop').attr('currentDateForOrder','');
				$('#tbl_diffOrderShop').attr('currentProviderId','');
				
                var rowOrderComplete = function (rowIndex, row){
                    var orderable_type_id = $(row).attr("orderable_type_id");
                    if (orderable_type_id == 3) {
                        var html = $('.has_notes div', row).html();
                        html = html.replace(/</g, '&lt;'
                            ).replace(/>/g, '&gt;'
                            ).replace(/\r\n/g, '<br>'
                            ).replace(/\r/g, '<br>'
                            ).replace(/\n/g, '<br>');
                        $('.has_notes div', row).html(html);
                        
                        $('.has_notes', row).show();
                        $('.no_notes', row).hide();
                    } else {
                        $('.has_notes', row).hide();
                        $('.no_notes', row).show();
                    }
                };
								
				if (orderId > 0) {
					$('#tbl_diffOrderShop').attr('currentOrderId',orderId);
					$('#tbl_diffOrderShop tbody').xml2html('reload', {
						params : 'oper=getDiffOrderShop&order_id='+orderId,
						rowComplete: rowOrderComplete
					});	

				} else if (providerId > 0){
					$('#tbl_diffOrderShop').attr('currentDateForOrder',dateForOrder);
					$('#tbl_diffOrderShop').attr('currentProviderId',providerId);
					$('#tbl_diffOrderShop tbody').xml2html('reload', {
						params : 'oper=getProductQuantiesForUfs&uf_id=-1&provider_id='+providerId + '&date_for_order='+dateForOrder,
						rowComplete: rowOrderComplete
					});	
					
				} 
			} //end loadOrderDetails



			
			//tmp table to load the order - shop comparison
			$('#tbl_diffOrderShop tbody').xml2html('init',{
				url : 'php/ctrl/Orders.php',
				params : 'oper=getDiffOrderShop', 
				loadOnInit : false, 
				beforeLoad : function (){
					$('.loadSpinner').show();
				},
				rowComplete : function (rowIndex, row){
					var qu = $(row).children().eq(3).text();
					if (isNaN(qu)) $(row).children().eq(3).text("-");
				},
				complete : function(rowCount){
					$('.loadSpinner').hide();
					if (rowCount >0){
						
						var orderId = $('#tbl_diffOrderShop').attr('currentOrderId');
						var dateForOrder = $('#tbl_diffOrderShop').attr('currentDateForOrder');
						var providerId = $('#tbl_diffOrderShop').attr('currentProviderId');

						var selector = (orderId > 0)? '.detail_'+orderId:'.detail_date_'+dateForOrder+'.detail_provider_'+providerId; 
						//alert(selector);
						var header = $('#tbl_diffOrderShop thead tr').clone();
						var itemRows = $('#tbl_diffOrderShop tbody tr').clone();

						if (orderId > 0){
							//var revision = $('#order_'+orderId).attr('revisionStatus');
							$('#order_'+orderId).after(itemRows).after(header);
							$(selector).show().prev().show();
					
							//$('#order_'+orderId).children().eq(3).addClass(modClass).html('<p class="textAlignCenter">'+modTxt+'</p>');		

						} else if (providerId > 0){  //not yet send / closed order
							$('.Date_'+dateForOrder+'.Provider_'+providerId).after(itemRows).after(header);
							$(selector).show().prev().show();
						
						}
	
					} 
				}
			});


			/**
			 *	converst the order status INT into CSS and text
			 */
			function formatOrderStatus(intStatus){
				var modClass = '';
				var modTxt = ''; 

				switch (intStatus){
					case "1":
						modTxt = "<?=$Text['ostat_yet_received']; ?>";
						break;
					case "2": 
						modClass = "asOrdered";
						modTxt = "<?=$Text['ostat_is_complete']; ?>"; 
						break;
					case "3": 
						modClass = 'postponed'
						modTxt = "<?=$Text['ostat_postponed'];?>";
						break;
					case "4": 
						modClass="orderCanceled";
						modTxt = "<?=$Text['ostat_canceled'];?>";
						break;
					case "5": 
						modClass = "withChanges";
						modTxt = "<?=$Text['ostat_changes']; ?>";
						break;	
				}

				//$(row).children().eq(3).html("<p class='textAlignCenter'><?=$Text["expected"];?></p>");
				
				return [modTxt, modClass];
			}
			

			/**
			 *	expand order details
			 */
			$('.expandOrderIcon').live('click', function(){

				var curTr = $(this).parents('tr'); 
				
				var orderId = curTr.attr('orderId');
				var dateForOrder =  curTr.attr('dateForOrder');
				var providerId =  curTr.attr('providerId');

				var selector = (orderId > 0)? '.detail_'+orderId:'.detail_date_'+dateForOrder+'.detail_provider_'+providerId; 
				var isLoaded = ($(selector).length > 0)? true:false; 

							
					if ($('span',this).hasClass('ui-icon-plus')){
						if (!isLoaded){
							loadOrderDetails(orderId, dateForOrder, providerId);
						} else {
							$(selector).show().prev().show();							
						}
						$('span',this).removeClass('ui-icon-plus').addClass('ui-icon-minus');
						curTr.children().addClass('ui-state-highlight ui-corner-all');
						
					} else {
						$('span',this).removeClass('ui-icon-minus').addClass('ui-icon-plus');
						$(selector).hide().prev().hide();
						curTr.children().removeClass('ui-state-highlight');
						
					} 
				
			})
			
			
			/**
			 *	print stuff
			 */
			var printWin = null;
			$('.printOrderIcon').live('click', function(){

				var dateForOrder = $(this).attr('dateForOrder');
				
				printWin = window.open('tpl/<?=$tpl_print_myorders;?>?date='+dateForOrder);
				printWin.focus();
			});

			
			//calculates index for sql result set
			function getOrderLimit(index)
			{
				
				if (index == 0) {
					$('#btn_nextOrders').button('disable');  
				} else {
					$('#btn_nextOrders').button('enable');  
				}	
				return index*gOrderLimit+","+(gOrderLimit);
			}


			
			
			/********************************************************
			 *      My PURCHASE
			 ********************************************************/
			//var shopDateSteps = 3;
			//var srange = 'month';

			//show older purchase dates
			$('#btn_prevPurchase').button({
				icons : {
					primary:"ui-icon-seek-prev"
					}
			})
			.click( function(e){
					gShopLimitIndex++;	
					$('#tbl_Shop tbody').xml2html('reload',{
						url : 'php/ctrl/Shop.php',
						params : 'oper=getShopListing&uf_id=-1&filter=all&limit='+getShopLimit(gShopLimitIndex)
						//params : 'oper=getShopListing&uf_id=-1&filter=steps&steps='+shopDateSteps+'&range='+srange
					});

			});

			//show older purchase dates
			$('#btn_nextPurchase').button({
				icons : {
					secondary:"ui-icon-seek-next"
					}
			})
			.click( function(e){
				gShopLimitIndex--;	
				$('#tbl_Shop tbody').xml2html('reload',{
					url : 'php/ctrl/Shop.php',
					params : 'oper=getShopListing&uf_id=-1&filter=all&limit='+getShopLimit(gShopLimitIndex)
					//params : 'oper=getShopListing&uf_id=-1&filter=steps&steps='+shopDateSteps+'&range='+srange
				});

			});
			
			
			
			
			//load purchase listing
			$('#tbl_Shop tbody').xml2html('init',{
					url : 'php/ctrl/Shop.php',
					params : 'oper=getShopListing&uf_id=-1&filter=all&limit='+getShopLimit(0),
					loadOnInit : false, 
					beforeLoad : function(){
						$('.loadSpinner').show();
					},
					rowComplete : function(rowIndex, row){
						var validated = $(row).children().eq(2).text();

						if (validated == '0000-00-00 00:00:00'){
							$(row).children().eq(2).html("-");	
						} else {
							$(row).children().eq(2)
								.addClass('okGreen')
								.html('<span class="ui-icon ui-icon-check tdIconCenter" title="<?=$Text['validated_at'];?>: '+validated+'"></span>');
						}
					},
					complete : function(){
						$('.loadSpinner').hide();
						$('#tbl_Shop tbody td.shopDate').each(function(){
							var date = $(this).text();

							$(this).text($.getCustomDate(date, "D d M, yy")); 

						})
					}
			});

			//load purchase detail (products and quantities)
			$('#tbl_purchaseDetail tbody').xml2html('init',{
				url : 'php/ctrl/Shop.php',
				params : 'oper=getShopCart', 
				loadOnInit : false, 
				beforeLoad : function(){
					$('.loadSpinner').show();
				},
				rowComplete : function (rowIndex, row){
					var price = new Number($(row).children().eq(5).text());
					var qu = new Number($(row).children().eq(3).text());
					var totalPrice = price * qu;
					totalPrice = totalPrice.toFixed(2);
					$(row).children().eq(5).text(totalPrice);
					
				},
				complete : function(rowCount){

					var shopId = $('#tbl_purchaseDetail').attr('currentShopId');
					var header = $('#tbl_purchaseDetail thead tr').clone();
					var itemRows = $('#tbl_purchaseDetail tbody tr').clone();

					$('#shop_'+shopId).after(itemRows).after(header);
					$('.loadSpinner').hide();

				}
			});
			

			$('.expandShopIcon').live('click', function(){

				var shopId = $(this).parents('tr').attr('shopId');
				var dateForShop = $(this).parents('tr').attr('dateForShop');

				$('#tbl_purchaseDetail').attr('currentShopId', shopId);
				$('#tbl_purchaseDetail').attr('currentDateForShop', dateForShop);
				
				
							
				if ($('span',this).hasClass('ui-icon-plus')){
					$('span',this).removeClass('ui-icon-plus').addClass('ui-icon-minus');
					$(this).parents('tr').children().addClass('ui-state-highlight ui-corner-all');

					$('#tbl_purchaseDetail tbody').xml2html('reload',{
						params : 'oper=getShopCart&shop_id='+shopId
					});

					
				} else {
					$('span',this).removeClass('ui-icon-minus').addClass('ui-icon-plus');
					$(this).parents('tr').children().removeClass('ui-state-highlight');
					$('#shop_'+shopId).next().hide();
					$('.detail_shop_'+shopId).hide();
				}
			})
			
			//print purchase / order
			$('.printShopIcon').live('click', function(){

				var shopId = $(this).parents('tr').prev().attr('shopId');
				var date = $(this).parents('tr').prev().attr('dateForShop');
				var op_name = $(this).parents('tr').prev().attr('operatorName');
				var op_uf = $(this).parents('tr').prev().attr('operatorUf');
				

				
				printWin = window.open('tpl/<?=$tpl_print_bill;?>?shopId='+shopId+'&date='+date+'&operatorName='+op_name+'&operatorUf='+op_uf);
				printWin.focus();
			});

			
			$('.iconContainer')
			.live('mouseover', function(e){
				$(this).addClass('ui-state-hover');
			})
			.live('mouseout', function (e){
				$(this).removeClass('ui-state-hover');
			});


			//calculates index for sql result set
			function getShopLimit(index)
			{
				if (index == 0) {
					$('#btn_nextPurchase').button('disable');  
				} else {
					$('#btn_nextPurchase').button('enable');  
				}	
				return index*gShopLimit+","+(gShopLimit);
			}


			/**
			 *	UPCOMING ORDERS
			 */
			 $('#tbl_UpcomingOrders tbody').xml2html('init',{
					url : 'php/ctrl/Dates.php',
					params : 'oper=getUpcomingOrders&range=3',  //time range counts in weeks. Here three weeks ahead. 
					loadOnInit : true, 
					complete : function(count){
						$('#tbl_UpcomingOrders tbody tr:even').addClass('rowHighlight');

						$('#tbl_UpcomingOrders tbody td.dateForOrder').each(function(){
							var date = $(this).text();

							$(this).text($.getCustomDate(date, "D d M, yy")); 

						})	
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
	<div id="stagewrap" class="ui-widget">
	
		<div id="homeWrap">
			<div class="aix-layout-fixW150 floatLeft">
				<?php if ($cfg_use_shop) {  // USE SHOP: start  ?>
				<div class="homeIcon index_navShop">
					<a href="shop_and_order.php?what=Shop"><img src="img/cesta.png"/></a>
					<p><a href="shop_and_order.php?what=Shop"><?php echo $Text['icon_purchase'];?></a></p>
				</div>
				<?php } // - - - - - - - - - - USE SHOP: end ?>
				<div class="homeIcon index_navOrder">
					<a href="shop_and_order.php?what=Order"><img src="img/pedido.png"/></a>
					<p><a href="shop_and_order.php?what=Order"><?php echo $Text['icon_order'];?></a></p>
				</div>
				<div class="homeIcon index_navIncidents">
					<a href="incidents.php"><img src="img/incidencias.png"/></a>
					<p><a href="incidents.php"><?php echo $Text['icon_incidents'];?></a></p>
				</div>
			</div>
			<div id="rightSummaryCol" class="aix-style-layout-splitW80 floatLeft aix-layout-widget-center-col">

				<ul>
					<li><a href="#tabs-1"><h2><?=$Text['my_orders'];?></h2></a></li>
					<li><a href="#tabs-2"><h2><?=$Text['my_purchases'];?></h2></a></li>	
					<li><a href="#tabs-3"><h2><?=$Text['upcoming_orders'];?></h2></a></li>	
					
				</ul>
				<span style="float:right; margin-top:-45px; margin-right:12px;"><img class="loadSpinner" src="img/ajax-loader.gif"/></span>
				<div id="tabs-1">
					<table id="tbl_Orders" class="tblListingDefault">
						<tbody>
							<tr id="order_{id}" orderId="{id}" dateForOrder="{date_for_order}" providerId="{provider_id}" class="Date_{date_for_order} Provider_{provider_id}" revisionStatus="{revision_status}">
								<td><p class="iconContainer ui-corner-all ui-state-default expandOrderIcon"><span class="ui-icon ui-icon-plus"></span></p></td>
								<td title="Order id: #{id}">{provider_name}</td>
								<td>{time_left}</td>
								<td><?=$Text['loading_status_info'];?></td>
								<td><p class="textAlignRight">{order_total}<?=$Text['currency_sign'];?></p></td>
								
							</tr>
						</tbody>
						<tfoot>
						<tr>
								<td colspan="6">&nbsp;</td>
							</tr>
							<tr>
								<td colspan="6">
									<p class="textAlignCenter">
										<button id="btn_prevOrders"><?=$Text['previous'];?></button>&nbsp;&nbsp;&nbsp;&nbsp;
										<button id="btn_nextOrders"><?=$Text['next'];?></button></p>
									</td>
								
								
							</tr>
						
						</tfoot>
					</table>
				</div>
				
				<div id="tabs-2">
					<table id="tbl_Shop" class="table_overviewShop">
						<thead>
							<tr >
								<th></th>
								<th class="textAlignCenter"><?=$Text['date_of_purchase'];?></th>
								<th class="textAlignCenter" colspan="3"><?=$Text['validated'];?></th>
								<th class="textAlignRight"><?=$Text['total'];?></th>
							</tr>
						</thead>
						<tbody>
							<tr id="shop_{id}" shopId="{id}" dateForShop="{date_for_shop}" operatorName="{operator_name}" operatorUf="{operator_uf}">
																				  <td><p class="iconContainer ui-corner-all ui-state-default expandShopIcon"><span class="ui-icon ui-icon-plus"></span></p></td>
								<td class="textAlignLeft shopDate">{date_for_shop}</td>
								<td class="textAlignCenter" colspan="3">{ts_validated}</td>
								<td class="textAlignRight">{purchase_total}<?=$Text['currency_sign'];?></td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<td colspan="6">&nbsp;</td>
							</tr>
							<tr>
								<td colspan="6">
									<p class="textAlignCenter">
										<button id="btn_prevPurchase"><?=$Text['previous'];?></button>&nbsp;&nbsp;&nbsp;&nbsp;
										<button id="btn_nextPurchase"><?=$Text['next'];?></button></p>
									</td>
								
								
							</tr>
						</tfoot>
					</table>
				</div>
				
				<div id="tabs-3">
					<table id="tbl_UpcomingOrders" class="tblListingDefault">
						<thead>
							<tr>
									<th class="textAlignLeft"><?=$Text['provider_name'];?></th>
									<th class=""><?=$Text['ordered_for'];?></th>
									<th><?=$Text['closes_days'];?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="minPadding"><p class="textAlignLeft">{provider_name}</p></td>
								<td class="dateForOrder textAlignLeft">{date_for_order}</td>
								<td>{time_left}</td>
							</tr>
						</tbody>
						
					</table>
				</div>
				
				
			</div>	
					
		</div>
	</div>
	
	<!-- end of stage wrap -->
</div>

<div id="tmp">
<table id="tbl_diffOrderShop" currentOrderId="" currentDateForOrder="" currentProviderId="">
	<thead>
		<tr>
			<td class="tdMyOrder"><?=$Text['id'];?></td>
			<td class="tdMyOrder" colspan="2"><?=$Text['product_name'];?></td>
			<td class="tdMyOrder"><?=$Text['ordered'];?></td>
			<td class="tdMyOrder"><?=$Text['delivered'];?></td>
			<!-- td class="tdMyOrder"><?=$Text['price'];?></td-->		
		</tr>
	</thead>
	<tbody>
		<tr class="detail_{order_id} detail_date_{date_for_order} detail_provider_{provider_id}" orderable_type_id="{orderable_type_id}">
			<td class="MyOrderItem">{product_id}</td>
			<td class="MyOrderItem no_notes" colspan="2">{name}</td>
			<td class="MyOrderItem has_notes hidden" colspan="4">{name}<br>
			    <div>{notes}</div>
			</td>
			<td class="MyOrderItem no_notes">{quantity}</td>
			<td class="MyOrderItem no_notes">{shop_quantity}</td>
		</tr>
	</tbody>
</table>

<table id="tbl_purchaseDetail" currentShopId="" currenShopDate="">
	<thead>
		<tr>
			<td><p class="ui-corner-all iconContainer ui-state-default printShopIcon"><span class="ui-icon ui-icon-print" title="Print bill"></span></p></td>
			<th><?php echo $Text['name_item'];?></th>	
			<th><?php echo $Text['provider_name'];?></th>					
			<th class="textAlignCenter"><?=$Text['qu']?></th>
			<th><?php echo $Text['unit'];?></th>
			<th class="textAlignRight"><?=$Text['price'];?></th>
			
			
			
		</tr>
	</thead>
	<tbody>
		<tr class="detail_shop_{cart_id}">
			<td></td>
			<td class="MyShopItem">{name}</td>
			<td class="MyShopItem">{provider_name}</td>
			<td class="MyShopItem textAlignCenter">{quantity}</td>
			<td class="MyShopItem">{unit}</td>
			<td class="MyShopItem textAlignRight">{unit_price}</td>	
			
		</tr>						
	</tbody>
	<tfoot>
		<tr>
			<td>&nbsp;</td>
			<td colspan="5">			
		</tr>
	</tfoot>
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