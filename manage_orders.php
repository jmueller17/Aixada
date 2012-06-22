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
			$('.success_msg').hide();

			var header = [];

			var tblHeaderComplete = false; 

			var global_oder_id = 0; 


			$("#datepicker").datepicker({
				dateFormat 	: 'DD, d MM, yy',
				onSelect : function (dateText, instance){
					$('#indicateShopDate').text(dateText);
				}
			}).hide();

			$('#showDatePicker').click(function(){
				$('#datepicker').toggle();
			});

			$.getAixadaDates('getToday', function (date){
				var today = $.datepicker.parseDate('yy-mm-dd', date[0]);
				$("#datepicker").datepicker('setDate', today);
				//$("#datepicker").datepicker( "option", "minDate", today);
				$("#datepicker").datepicker("refresh");		
				$('#indicateShopDate').text(date[0]);		
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
							var revised = $(this).find('revised').text();
							var arrived = $(this).find('arrived').text();
							var tblCol = '.Col-'+uf_id;
							var tblRow = '.Row-'+product_id;
							var pid	= product_id + '_' + uf_id; 
							
							//$(tblCol+tblRow).append('<p id="'+pid+'" class="textAlignCenter">'+qu+'</p><p><input type="checkbox" class="floatLeft" /></p>')
							$(tblCol+tblRow).append('<p id="'+pid+'" class="textAlignCenter">'+qu+'</p>')

							if (revised == true) {
								$(tblCol+tblRow).removeClass('toRevise').addClass('revised');
							} 

							if (arrived == false && !$(tblRow).hasClass('missing')){
								$(tblRow).removeClass('toRevise revised').addClass('missing');
								$('#ckboxRevised_'+product_id).attr('checked','checked');
								$('#ckboxArrived_'+product_id).attr('checked',false);
							}
							
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
			 *	returns to order overview  TODO: check for unsaved changes
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

						$('#dialog_setShopDate').dialog("open");
						
	       				
					} else {
						$.showMsg({
							msg:"There are still unrevised items in this order. Please make sure all 'revised' checkboxes are checked!",
							type: "error"
						});

					}
       			}).hide();

			$('#dialog_setShopDate').dialog({
				autoOpen:false,
				width:450,
				height:360,
				buttons: {  
					"<?=$Text['btn_ok'];?>" : function(){
						
						var $this = $(this);
						$.ajax({
							type: "POST",
							url: 'ctrlOrders.php?oper=moveOrderToShop&order_id='+global_order_id+'&date='+$.getSelectedDate('#datepicker'),
							success: function(txt){
								$('.success_msg').show().next().hide();
								setTimeout(function(){
									$this.dialog( "close" )
									$('.interactiveCell').hide();
									switchTo('overview');
								},3000);
							},
							error : function(XMLHttpRequest, textStatus, errorThrown){
								$.showMsg({
									msg:XMLHttpRequest.responseText,
									type: 'error'});
								
							}
						});
	
						
						},
				
					"<?=$Text['btn_cancel'];?>"	: function(){
						
						$( this ).dialog( "close" );
						} 
				}
			});
				
			
		
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
									id 		: 'product_uf',
									name 	: 'quantity',
									indicator: 'Saving',
									tooltip	: 	'click to edit',
									callback: function(value, settings){
										$(this).parent().removeClass('toRevise').addClass('revised');
									} 
						});

					}

			})
			.live('mouseout', function(e){
				var col = $(this).attr('col');
				var row = $(this).attr('row');

				$('.Row-'+row).removeClass('editHighlightRow');
				$('.Col-'+col).removeClass('editHighlightCol');

			});
				
				
			/**
			 *	uncheck an entire product row (product did not arrive). 
			 */
			$('input:checkbox[name="hasArrived"]').live('click', function(e){
				var product_id = $(this).attr('hasArrivedId');
				var has_arrived = $(this).is(':checked')? 1:0; 
				var is_revised = 1; 
				$.ajax({
					type: "POST",
					url: 'ctrlOrders.php?oper=setOrderItemStatus&order_id='+global_order_id+'&product_id='+product_id+'&has_arrived='+has_arrived+'&is_revised='+is_revised,
					success: function(txt){
						if (has_arrived){
							$('.Row-'+product_id).removeClass('missing').addClass('toRevise'); 
						} else {
							$('.Row-'+product_id).removeClass('toRevise').addClass('missing');
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

			
			/**
			 *	mark an entire product row as revised. the status is saved in 
			 *  the order_to_shop table.  
			 */
			$('input:checkbox[name="revised"]').live('click', function(e){
				var product_id = $(this).attr('isRevisedId');	
				var is_revised = $(this).is(':checked')? 1:0;
				var has_arrived = $('#ckboxArrived_'+product_id).is(':checked')? 1:0;
				$this = $(this);
				$.ajax({
					type: "POST",
					url: 'ctrlOrders.php?oper=setOrderItemStatus&order_id='+global_order_id+'&product_id='+product_id+'&has_arrived='+has_arrived+'&is_revised='+is_revised,
					success: function(txt){
						if (is_revised){
							$this.attr('checked','checked');
							$('.Row-'+product_id).removeClass('toRevise').addClass('revised');
						} else {
							$('.Row-'+product_id).removeClass('revised').addClass('toRevise');
						}

					},
					error : function(XMLHttpRequest, textStatus, errorThrown){
						$.showMsg({
							msg:XMLHttpRequest.responseText,
							type: 'error'});
					}
				});
				
				
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
						$(row).children().eq(5).html('<p><p class="minPadding iconContainer floatLeft ui-state-highlight ui-corner-all"><span class="ui-icon ui-icon-alert"></span></p> not yet send to provider</p>');
					}
				},
				complete : function (rowCount){
					$("#tbl_orderOverview").trigger("update"); 
					if (rowCount == 0){
						$.showMsg({
							msg:'Sorry, no orders match the selected criteria. ',
							type: 'warning'});
					}
					//$('#tbl_orderOverview tbody tr:even').addClass('rowHighlight'); 					
				}
			});


			$('#tbl_orderOverview tbody tr')
			.live('mouseover', function(){
				$(this).removeClass('highlight').addClass('ui-state-highlight');
				
			})
			.live('mouseout',function(){
				$(this).removeClass('ui-state-highlight');
				
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
					global_order_id 	= $(this).parents('tr').attr('id');
					var shopDate 		= $(this).parents('tr').children().eq(6).text();
					var order_id 		= $(this).parents('tr').children().eq(0).text();
					var provider_name 	= $(this).parents('tr').children().eq(2).text() + ' (#'+order_id+')' ;

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

					
					//if shop date exists, check if it items have already been moved to shop_item and/or validated
					if (shopDate != ''){
						$.post('ctrlOrders.php?oper=checkValidationStatus&order_id='+global_order_id, function(xml) {
							//alert($(xml).find('validated').text())
							
							var hasCart = false; 
							var isValidated = false; 
							$(xml).find('row').each(function(){

								 if ($(this).find('cart_id').text() > 0) hasCart = true; 
								 if ($(this).find('validated').text() > 0) isValidated = true; 
								 

							});

							if (hasCart && !isValidated){
								$.showMsg({
									msg:'The items of this order have already been revised and placed into people\'s carts. Revising them again will override the modifications already made and potentially interfere with people\'s own corrections. <br/><br/> Are you really sure you want to proceed anyway?!',
									buttons: {
										"<?=$Text['btn_ok'];?>":function(){						
											switchTo('details', {name:provider_name});
											$(this).dialog("close");
										},
										"<?=$Text['btn_cancel'];?>" : function(){
											$( this ).dialog( "close" );
										}
									},
									type: 'warning'});

							} else if (isValidated){
								$.showMsg({
									msg:'Some or all order items have already been validated! Sorry, but it is not possible to make any further changes!!',
									type: 'error'});
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

				$("#tblOptions")
				.button({
					icons: {
			        	secondary: "ui-icon-triangle-1-s"
					}
			    })
			    .menu({
					content: $('#tblOptionsItems').html(),	
					showSpeed: 50, 
					width:280,
					flyOut: true, 
					itemSelected: function(item){					//TODO instead of using this callback function make your own menu; if jquerui is updated, this will  not work
						//show hide deactivated products
						var filter = $(item).attr('id');
						$('#tbl_orderOverview tbody').xml2html('reload',{
							params : 'oper=getOrdersListing&filter='+filter,
						});
						
					}//end item selected 
				});//end menu
			


			
			
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
		   		<button id="btn_setShopDate" class="detailElements btn_right" title="Place order-items into HU shopping carts">Distribute!</button>
				<button	id="tblOptions" class="overviewElements btn_right">Filter orders</button>
				<div id="tblOptionsItems" class="hidden">
					<ul>
						<li><a href="javascript:void(null)" id="ordersForToday">Expected today</a></li>
						<li><a href="javascript:void(null)" id="futureOrders">Upcoming: next week + beyond</a></li>
						<li><a href="javascript:void(null)" id="prevMonth">Last month</a></li>
						<li><a href="javascript:void(null)" id="limboOrders">That never arrived</a></li>
					</ul>
				</div>				
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


<div id="dialog_setShopDate" title="Set shopping date">
	<p>&nbsp;</p>
	<p class="success_msg">The items have been successfully moved to the shopping carts of the corresponding date.</p>
	<p>Are you sure you want to make this order available for shopping? All corresponding products will be  
	placed into the shopping cart for the following date: 
	</p>
	<br/>
	<p class="textAlignCenter boldStuff" id="indicateShopDate"></p> 
	<br/>
	<p>You can also <a href="javascript:void(null)" id="showDatePicker">choose an alternative date</a> </p>
	<br/>
	<div id="datepicker"></div>
</div>

<!-- / END -->
</body>
</html>













