<?php include "inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title']; ?> Manage Orders - Overview</title>

 	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/fgmenu/fg.menu.css"   />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/<?=$default_theme;?>/jqueryui.css"/>
    <!-- link rel="stylesheet" type="text/css" 	 media="screen" href="js/tablesorter/themes/blue/style.css"/-->
    
    <?php if (isset($_SESSION['dev']) && $_SESSION['dev'] == true ) { ?> 
	    <script type="text/javascript" src="js/jquery/jquery.js"></script>
		<script type="text/javascript" src="js/jqueryui/jqueryui.js"></script>
		<script type="text/javascript" src="js/fgmenu/fg.menu.js"></script>
		<script type="text/javascript" src="js/aixadautilities/jquery.aixadaMenu.js"></script>     	 
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaXML2HTML.js" ></script>   	
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaUtilities.js" ></script>
	   	<script type="text/javascript" src="js/tablesorter/jquery.tablesorter.js" ></script>
	   	<script type="text/javascript" src="js/jeditable/jquery.jeditable.mini.js" ></script>
	   	
	   	

	   	  
   	<?php  } else { ?>
	   	<script type="text/javascript" src="js/js_for_manage_orders.min.js"></script>
    <?php }?>
     
	   
	<script type="text/javascript">
	$(function(){

			$('.detailElements').hide();

			var header = [];

			var tblHeaderComplete = false; 

			var global_oder_id = 0; 

			var today = 0; 

			
			$.post('ctrlDates.php?oper=getToday', function(data) {
					today = eval(data)[0];		 
			});


			//STEP 1: retrieve all active ufs in order to construct the table header
			$.ajax({
					type: "POST",
					url: 'smallqueries.php?oper=getActiveUFs',
					dataType:"xml",
					success: function(xml){
						var theadStr = ''; 
						$(xml).find('row').each(function(){
							var id = $(this).find('id').text();
							var colClass = 'Col-'+id;
							header.push(id);
							theadStr += '<th class="'+colClass+' hidden col">'+id+'</th>'
						});

						theadStr += '<th>Total</th>';
						theadStr += '<th>Revised</th>';
						
						$('#tbl_reviseOrder thead tr').last().append(theadStr);

						tblHeaderComplete = true; 

					},
					error : function(XMLHttpRequest, textStatus, errorThrown){
						$.showMsg({
							msg:XMLHttpRequest.responseText,
							type: 'error'});	
					}
			}); //end ajax	

			
			//STEP 2: construct table structure: products and col-cells. 
			$('#tbl_reviseOrder tbody').xml2html('init',{
				url : 'ctrlOrders.php',
				loadOnInit : false, 
				rowComplete : function (rowIndex, row){
					var tbodyStr = '';
					var product_id = $(row).children(':first').text();
					
					for (var i=0; i<header.length; i++){
						var colClass = 'Col-'+header[i];
						var rowClass = 'Row-'+product_id;
						//var tdid 	 = header[i] + "_" + product_id
						tbodyStr += '<td class="'+colClass+' '+rowClass+' hidden interactiveCell toRevise" col="'+header[i]+'" row="'+product_id+'"></td>';
					}

					//product total quantities
					tbodyStr += '<td id="total_'+product_id+'"></td>';
					
					//revised checkbox for product
					tbodyStr += '<td class="textAlignCenter"><input type="checkbox" isRevisedId="'+product_id+'" id="ckboxRevised_'+product_id+'" name="revised" /></td>';
					$(row).last().append(tbodyStr);
					
				},
				complete : function (rowCount){
					
					//STEP 3: populate cells with product quantities
					$.ajax({
					type: "POST",
					url: 'ctrlOrders.php?oper=getProductQuantiesForUfs&order_id='+ global_order_id,
					dataType:"xml",
					success: function(xml){

						var quTotal = 0;
						var lastId = -1;  
						$(xml).find('row').each(function(){
							
							var product_id = $(this).find('product_id').text();
							var uf_id = $(this).find('uf_id').text();
							var qu = $(this).find('quantity').text();
							var tblCol = '.Col-'+uf_id;
							var tblRow = '.Row-'+product_id;
							var pid	= product_id + '_' + uf_id; 
							
							//$(tblCol+tblRow).append('<p id="'+pid+'" class="textAlignCenter">'+qu+'</p><p><input type="checkbox" class="floatLeft" /></p>')
							$(tblCol+tblRow).append('<p id="'+pid+'" class="textAlignCenter">'+qu+'</p>')

							
							$(tblCol).show();

							//calculate total quantities and update last table cell
							if (lastId == -1) {lastId = product_id}; 							
							if (lastId != product_id){
								var total = quTotal.toFixed(2) + " " + $('#unit_'+lastId).text();
								$('#total_'+lastId).text(total);
								quTotal = 0; 
							}
							
							quTotal += parseFloat(qu); 
							lastId = product_id; 

						});

						var total = quTotal.toFixed(2) + " " + $('#unit_'+lastId).text();
						$('#total_'+lastId).text(total);


					},
					error : function(XMLHttpRequest, textStatus, errorThrown){
						$.showMsg({
							msg:XMLHttpRequest.responseText,
							type: 'error'});
						
					}
			}); //end ajax	
						
				}
			});

			
			/**
			 *	returns to order overview
			 */
			$("#btn_overview").button({
				 icons: {
		        		primary: "ui-icon-circle-arrow-w"
		        	}
				 })
        		.click(function(e){
    				switchTo('overview'); 
        		}).hide();

			
			/**
			 *	copies order_items after revision into aixada_shop_item only if not already
			 *	validated items exist;  
			 */
			$("#btn_setShopDate").button({
				 icons: {
		        		primary: "ui-icon-cart"
		        	}
				 })
       			.click(function(e){
           			var allRevised = true;
					$('input:checkbox[name="revised"]').each(function(){
						if (!$(this).is(':checked')){
							allRevised = false; 
							return false; 
						}

					});

					if (allRevised){
	       				$.showMsg({
							msg:"Are you sure to move these items into people's cart for today?!",
							buttons: {
								"<?=$Text['btn_ok'];?>":function(){	
									var $this = $(this);
									$.ajax({
										type: "POST",
										url: 'ctrlOrders.php?oper=moveOrderToShop&order_id='+global_order_id+'&date='+today,
										success: function(txt){
											$this.dialog( "close" );
											$('.interactiveCell').hide();
											switchTo('overview');
										},
										error : function(XMLHttpRequest, textStatus, errorThrown){
											$.showMsg({
												msg:XMLHttpRequest.responseText,
												type: 'error'});
											
										}
									});


										
									//$(this).dialog("close");
								},
								"<?=$Text['btn_cancel'];?>" : function(){
									$( this ).dialog( "close" );
								}
							},
							type: 'confirm'});
					} else {
						$.showMsg({
							msg:"There are still unrevised items in this order. Please make sure all 'revised' checkboxes are checked!",
							type: "error"
						});

					}
       			}).hide();

			
			
		
			//interactivity for editing cells
			$('td.interactiveCell')
				.live('mouseover', function(e){						//make each cell editable on mouseover. 
					var col = $(this).attr('col');
					var row = $(this).attr('row');

					$('.Row-'+row).addClass('editHighlightRow');
					$('.Col-'+col).addClass('editHighlightCol');
					
					if (!$(this).hasClass('editable')){
						$(this).children(':first')
							.addClass('editable')
							.editable('ctrlOrders.php', {			//init the jeditable plugin
									submitdata : {
										oper: 'editQuantity',
										order_id : global_order_id
										},
									id : 'product_uf',
									name : 'quantity',
									indicator: 'Saving',
									tooltip: 	'click to edit'
						});

					}

			})
			.live('mouseout', function(e){
				var col = $(this).attr('col');
				var row = $(this).attr('row');

				$('.Row-'+row).removeClass('editHighlightRow');
				$('.Col-'+col).removeClass('editHighlightCol');

			});
				
				
			//uncheck an entire product row (did not arrive)
			$('input:checkbox[name="hasArrived"]').live('click', function(e){
				var product_id = $(this).attr('hasArrivedId');
				var has_arrived = $(this).is(':checked'); // )? 1:0;
				$.ajax({
					type: "POST",
					url: 'ctrlOrders.php?oper=toggleProduct&order_id='+global_order_id+'&product_id='+product_id+'&has_arrived='+has_arrived,
					success: function(txt){
						if (has_arrived){
							$('.Row-'+product_id).removeClass('missing'); //not working yet...?!
						} else {
							$('.Row-'+product_id).addClass('missing');
							$('#ckboxRevised_'+product_id).attr('checked','checked');
							
						}
					},
					error : function(XMLHttpRequest, textStatus, errorThrown){
						$.showMsg({
							msg:XMLHttpRequest.responseText,
							type: 'error'});
						
					}
				});
			});

			
			//mark an entire product row as revised
			$('input:checkbox[name="revised"]').live('click', function(e){
				var product_id = $(this).attr('isRevisedId');	
				if ($(this).is(':checked')){
					$(this).attr('checked','checked');
					$('.Row-'+product_id).removeClass('toRevise').addClass('revised');
				} else {
					$('.Row-'+product_id).removeClass('revised').addClass('toRevise');
				}
			});
					
			

			
			/***********************************************************
			 *		ORDER OVERVIEW FUNCTIONALITY
			 **********************************************************/
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
						$(row).children().eq(5).html('<p class="minPadding iconContainer"><span class="ui-icon ui-icon-alert ui-state-highlight floatLeft"></span>not yet send to provider</p>');
					}

					//set shopping date

				
					 	
				},
				complete : function (rowCount){
					$("#tbl_orderOverview").trigger("update"); 
					$('#tbl_orderOverview tbody tr:even').addClass('highlight'); //TODO update highlight after sorting!! 
					
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
			

			//revise order icon 
			$('.ui-icon-check')
				.live('click', function(e){
					global_order_id = $(this).parents('tr').attr('id');
					var shopDate 	= $(this).parents('tr').children().eq(6).text();
					var provider_name = $(this).parents('tr').children().eq(2).text();

					$('.col').hide();

					//if table header ajax call has not finished, wait
					if (!tblHeaderComplete){
						$.showMsg({
							msg:'The table header is still being constructed. Depending on your internet connection this might take a little while. Try again in 5 seconds. ',
							type: 'error'});
						return false; 
					}

					//need the order id
					if (global_order_id <= 0){
						$.showMsg({
							msg:'No valid ID for order found!',
							type: 'error'});
						return false; 
					}

					
					//if shop date exists, check if it items have been validated
					if (shopDate != ''){
						$.post('ctrlOrders.php?oper=checkValidationStatus&order_id='+global_order_id, function(xml) {
							if ($(xml).find('validated').text() > 0){
								$.showMsg({
									msg:'Some or all order items have already been validated! Sorry, but it is not possible to make any further changes!!',
									type: 'warning'});
							} else {
								switchTo('details', {name:provider_name});
							}		 
						});
					} else {
						switchTo('details', {name:provider_name});
					}
				});


				/**
				 *	switch between the order overview page and the revision/detail page
				 */
				function switchTo(page, options){
					switch (page){
						case 'overview':
							$('.detailElements').hide();
		    				$('.overviewElements').fadeIn(1000);
		    				global_order_id = 0;
							break;

						case 'details':
							$('#providerName').text(options.name);							
							$('.overviewElements').hide();
							$('.detailElements').fadeIn(1000);
							$('#tbl_reviseOrder tbody').xml2html("reload", {						//load order details for revision
								params : 'oper=getOrderedProductsList&order_id='+global_order_id
							})
							break;
					}
				}
			
			

			/*$('#dialog_setShopDate').dialog({
				autoOpen:false,
				width:500,
				height:540,
				buttons: {  
					"<?=$Text['btn_ok'];?>" : function(){
						
						//setClosingDate($(this).data('tmpData').orderDate); 
						},
				
					"<?=$Text['btn_cancel'];?>"	: function(){
						//$('td, th').removeClass('ui-state-hover');
						$( this ).dialog( "close" );
						} 
				}
			});*/

		
			
			
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
				<button id="btn_overview" class="floatLeft detailElements">Overview</button>
				<h1 class="detailElements">Manager order detail for <span id="providerName"></span></h1>
		    	<h1 class="overviewElements">Manage orders</h1>
		    </div>
		   	<div id="titleRightCol">
		   		<button id="btn_setShopDate" class="detailElements floatRight">Activate for today's shopping!</button>
								
		   	</div> 	
		</div> <!--  end of title wrap -->
	
		<div id="orderOverview" class="ui-widget overviewElements">
			<div class="ui-widget-header ui-corner-all">
				<p>&nbsp;</p>
			</div>
			<div class="ui-widget-content">
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
						<th class="clickable">Order total  <span class="ui-icon ui-icon-triangle-2-n-s floatRight"></span></th>
						<th>Revise</th>
					</tr>
				</thead>
				<tbody>
					<tr id="{id}">
						<td>{id}</td>
						<td class="textAlignCenter">{date_for_order}</td>
						<td class="textAlignRight minPadding">{provider_name}</td>
						<td class="textAlignCenter">{time_left}</td>
						<td class="textAlignCenter"><span id="orderClosedIcon{id}" class="tdIconCenter ui-icon ui-icon-unlocked"></span></td>
						<td class="textAlignCenter">{ts_send_off}</td>
						<td class="textAlignCenter">{date_for_shop}</td>
						<td class="textAlignRight">{order_total} €</td>
						<td class="textAlignCenter">				
							<p class="ui-corner-all iconContainer ui-state-default floatRight"><span class="ui-icon ui-icon-check" title="Revise order"></span></p>
						</td>
					</tr>
				</tbody>
				<tfoot>
				
				</tfoot>
			</table>
			</div> <!-- widget content -->
		</div>
		
		
		<div id="reviseOrder" class="ui-widget detailElements">
			<div class="ui-widget-header ui-corner-all textAlignCenter">
				<h3 id="orderInfoDate"></h3>
			</div>
			<div class="ui-widget-content">
				<table id="tbl_reviseOrder" class="tblReviseOrder">
					<thead>
						<tr>
							<th>id</th>
							<th>Name</th>
							<th>Unit</th>
							<th>Arrived</th>
						</tr>
					</thead>
					<tbody>
						<tr>							
							<td>{id}</td>
							<td>{name}</td>
							<td id="unit_{id}">{unit}</td>
							<td class="textAlignCenter"><input type="checkbox" name="hasArrived" hasArrivedId="{id}" id="ckboxArrived_{id}" checked="checked" /></td>
						</tr>
					</tbody>
					<tfoot>
					
					</tfoot>
				</table>
			</div>
		</div>		
	
	</div>
	<!-- end of stage wrap -->
</div>
<!-- end of wrap -->


<!-- div id="dialog_setShopDate" title="Set shopping date">
	<p>&nbsp;</p>
	<p>If the current order has arrived, you can set a shopping date. This will place the corresponding products 
	into the shopping cart. 
	</p>
	<br/>
	<p>Select new closing date: </p>
	<br/>
	<div id="closingDatePicker"></div>
</div-->

<!-- / END -->
</body>
</html>













