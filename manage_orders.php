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
	function getTable(){
		return document.getElementById('tbl_reviseOrder');
	}
	
	$(function(){

			$('.detailElements').hide();
			$('.success_msg').hide();

			var header = [];

			var tblHeaderComplete = false; 

			//the order_id that is currently revised or viewed
			var global_order_id = 0; 

			//indicates page subsection: overview | details | view
			var global_section = 'overview';


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
						theadStr += '<th class="revisedCol">Revised</th>';
						
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
					tbodyStr += '<td class="textAlignCenter revisedCol"><input type="checkbox" isRevisedId="'+product_id+'" id="ckboxRevised_'+product_id+'" name="revised" /></td>';
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

						//printWin = window.open('tpl/report_order1.php');
						//printWin.focus();

						//don't need revised and arrived column for viewing order
						if (global_section == 'view'){
							$('.revisedCol, .arrivedCol').hide();
						} else {
							$('.revisedCol, .arrivedCol').show();
						}


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
				height:460,
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
					var product = $(this).parent().children().eq(1).text();

					//$('.Row-'+row).addClass('editHighlightRow');
					//$('.Col-'+col).addClass('editHighlightCol');
					
					if (!$(this).hasClass('editable') && global_section == 'details'){
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
								    tooltip	: 	'UF ' + col + '\n' + product + '\nClick to edit!',
									callback: function(value, settings){
										$(this).parent().removeClass('toRevise').addClass('revised');
									} 
						});

					}

			})
			.live('mouseout', function(e){
				var col = $(this).attr('col');
				var row = $(this).attr('row');

				//$('.Row-'+row).removeClass('editHighlightRow');
				//$('.Col-'+col).removeClass('editHighlightCol');

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
			 *		ORDER REVISION STATUS
			 **********************************************************/
			 $("#btn_revision").button({
				 icons: {
		        		primary: "ui-icon-check"
		        	}
				 })
       			.click(function(e){
					$("#dialog_orderStatus").dialog("close");
       			});
			
			 $("#btn_cancel").button({
				 icons: {
		        		primary: "ui-icon-cancel"
		        	}
				 })
       			.click(function(e){
					setOrderStatus(4);
       			});
    			
			 $("#btn_postpone").button({
				 icons: {
		        		primary: "ui-icon-info"
		        	}
				 })
       			.click(function(e){
					setOrderStatus(3);
       			});

			 $('#dialog_orderStatus').dialog({
					autoOpen:false,
					width:450,
					height:420,
					modal:true
				});

			function setOrderStatus(status){
				$.ajax({
					type: "POST",
					url: 'ctrlOrders.php?oper=setOrderStatus&order_id='+global_order_id+'&status='+status,
					success: function(txt){
						$("#dialog_orderStatus").dialog("close");
						switchTo('overview');
					},
					error : function(XMLHttpRequest, textStatus, errorThrown){
						$.showMsg({
							msg:XMLHttpRequest.responseText,
							type: 'error'});
					}
				});

			}
			
			/***********************************************************
			 *		ORDER OVERVIEW FUNCTIONALITY
			 **********************************************************/
			$('#tbl_orderOverview tbody').xml2html('init',{
				url : 'ctrlOrders.php',
				params : 'oper=getOrdersListing&filter=prevMonth', 
				loadOnInit : true, 
				rowComplete : function (rowIndex, row){
					var orderId = $(row).attr("id");
					var timeLeft = parseInt($(row).children().eq(4).text());
					var status = $(row).children().eq(8).text();
					
					if (timeLeft > 0){ 	// order is still open
						$(row).children().eq(8).html('<span class="tdIconCenter ui-icon ui-icon-unlocked" title="Order is open"></span>');
						
					} else {			//order is closed
						$(row).children().eq(4).text("closed");
						var statusTd = $(row).children().eq(8); 
						switch(status){
							case "1": 
								statusTd.html('<span class="tdIconCenter ui-icon ui-icon-mail-closed" title="Order has been send to provider"></span>');
								break;
							case "2": 
								statusTd.addClass('revised').html('<span class="tdIconCenter ui-icon ui-icon-check" title="Order has been revised"></span>');
								break;
							case "3": 
								statusTd.addClass('postponed').html('<span class="tdIconCenter ui-icon ui-icon-info" title="Order has been postponed"></span>');
								break;
							case "4": 
								statusTd.addClass('orderCanceled').html('<span class="tdIconCenter ui-icon ui-icon-cancel" title="Order has been canceled"></span>');
								break;
						}
					}

					
					if (orderId > 0){ 

					} else {
						//don't have an order id yet. construct one with date + provider_id
						var date_pvid = $(row).children().eq(3).text() + "_" + $(row).children().eq(2).attr("providerId");
						//while open and not send off, no order_id exists
						$(row).children().eq(1).html('<p>-</p>');
						$(row).children().eq(5).html('<p><a href="javascript:void(null)" class="finalizeOrder" datePvId="'+date_pvid+'">Finalize now</a></p>');
						

						
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

			$('.finalizeOrder').live('click', function(e){
				var tmp = $(this).attr("datePvId").split("_");
				var timeLeft = $(this).parents('tr').children().eq(4).text();
				var msgt = 'You are about to finalize an order. This means that no further modifications are possible to this order. Are you sure to continue?';
				
				if (timeLeft > 0){
					msgt = 'This order is still open. Finalizing it now means that no further items can be ordered for this date and provider. Are you sue you want to continue?'
				}
				
				$.showMsg({
					msg: msgt,
					buttons: {
						"<?=$Text['btn_ok'];?>":function(){						
							finalizeOrder(tmp[1], tmp[0]);
							$(this).dialog("close");
						},
						"<?=$Text['btn_cancel'];?>" : function(){
							$( this ).dialog( "close" );
						}
					},
					type: 'confirm'});
			
			});

			/**
			 *	Finalizes an order: synon. for sending it to the provider: an order ID is assigned, no more modifications are possible. 
			 */
			function finalizeOrder (providerId, orderDate){
				$.ajax({
					type: "POST",
					url: 'ctrlOrders.php?oper=finalizeOrder&provider_id='+providerId+'&date='+orderDate,
					success: function(txt){
						$('#tbl_orderOverview tbody').xml2html('reload');
					},
					error : function(XMLHttpRequest, textStatus, errorThrown){
						$.showMsg({
							msg:XMLHttpRequest.responseText,
							type: 'error'});
						
					}
				});			
			}
			

			
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
			$('.reviseOrderBtn')
				.live('click', function(e){
					global_order_id 	= $(this).parents('tr').attr('id');
					var shopDate 		= $(this).parents('tr').children().eq(6).text();
					var order_id 		= $(this).parents('tr').children().eq(1).text();
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


				$('.viewOrderBtn')
					.live('click', function(e){
						global_order_id 	= $(this).parents('tr').attr('id');
						var shopDate 		= $(this).parents('tr').children().eq(6).text();
						var order_id 		= $(this).parents('tr').children().eq(1).text();
						var provider_name 	= $(this).parents('tr').children().eq(2).text() + ' (#'+order_id+')' ;

						$('.col').hide();
						
						switchTo('view', {name:provider_name});

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
							$('#dialog_orderStatus').dialog("open");
							$('#tbl_reviseOrder tbody').xml2html("reload", {						//load order details for revision
								params : 'oper=getOrderedProductsList&order_id='+global_order_id
							})
							break;
							
						case 'view':
							$('#providerName').text(options.name);							
							$('.overviewElements').hide();
							$('.detailElements').fadeIn(1000);
							$('#tbl_reviseOrder tbody').xml2html("reload", {						//load order details for revision
								params : 'oper=getOrderedProductsList&order_id='+global_order_id
							})
							$('#btn_setShopDate').hide();
							break;
					}
					global_section = page; 
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
			
				var printWin;
				$('#bulkActions').change(function(){

					var sel = $("option:selected", this).val();

					if (sel == "print"){

						printWin = window.open('tpl/report_orders1.php');
						printWin.focus();
					}

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
						<li><a href="javascript:void(null)" id="nextWeek">Next week</a></li>
						<li><a href="javascript:void(null)" id="futureOrders">All future orders</a></li>
						<li><a href="javascript:void(null)" id="prevMonth">Last month</a></li>
						<li><a href="javascript:void(null)" id="prevYear">Last year</a></li>
						<li><a href="javascript:void(null)" id="limboOrders">postponed</a></li>
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
						<th></th>
						<th class="clickable">id <span class="ui-icon ui-icon-triangle-2-n-s floatRight"></span></th>
						<th class="clickable">Provider <span class="ui-icon ui-icon-triangle-2-n-s floatRight"></span></th>
						<th class="clickable">Ordered for <span class="ui-icon ui-icon-triangle-2-n-s floatRight"></span></th>
						<th>Closes in days</th>
						<th>Send off to provider</th>
						<th class="clickable">Shop date <span class="ui-icon ui-icon-triangle-2-n-s floatRight"></span></th>
						<th class="clickable">Order total  <span class="ui-icon ui-icon-triangle-2-n-s floatRight"></span></th>
						<th>Status</th>
						<th>Revise</th>
					</tr>
				</thead>
				<tbody>
					<tr id="{id}">
						<td><input type="checkbox" name="bulkOrderList"/>
						<td>{id}</td>
						<td class="textAlignRight minPadding" providerId="{provider_id}">{provider_name}</td>
						<td class="textAlignCenter">{date_for_order}</td>
						<td class="textAlignCenter">{time_left}</td>
						<td class="textAlignCenter">{ts_send_off}</td>
						<td class="textAlignCenter">{date_for_shop}</td>
						<td class="textAlignRight">{order_total} €&nbsp;&nbsp;</td>
						<td class="textAlignCenter">{revision_status}</td>
						<td>				
							<p class="ui-corner-all iconContainer ui-state-default floatLeft viewOrderBtn"><span class="ui-icon" title="View order"></span></p>
							<p class="ui-corner-all iconContainer ui-state-default floatRight reviseOrderBtn"><span class="ui-icon ui-icon-check" title="Revise order"></span></p>
						</td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<td></td>
						<td colspan="6">
							<select id="bulkActions">
								<option value="-1">With selected...</option>
								<option value="print">Print</option>
								<option value="download">Download as zip</option>
							</select>
						</td>
					</tr>
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
							<th class="arrivedCol">Arrived</th>
						</tr>
					</thead>
					<tbody>
						<tr>							
							<td>{id}</td>
							<td>{name}</td>
							<td id="unit_{id}">{unit}</td>
							<td class="textAlignCenter arrivedCol"><input type="checkbox" name="hasArrived" hasArrivedId="{id}" id="ckboxArrived_{id}" checked="checked" /></td>
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

<div id="dialog_orderStatus" title="Set Order Status">
	<p>&nbsp;</p>
	<table>
		<tr><td class="textAlignCenter"><button id="btn_revision">Arrived!</button></td><td>&nbsp;</td><td>Most or all ordered items have arrived. Proceed to revise and distribute the products to shopping carts...</td></tr>					
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr><td class="textAlignCenter"><button id="btn_postpone">Postpone</button></td><td>&nbsp;</td><td>The order did not arrive for the ordered date but probably will in the upcoming weeks.</td></tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr><td class="textAlignCenter"><button id="btn_cancel">Cancel</button></td><td>&nbsp;</td><td>Order did and will not arrive.</td></tr>
	</table>
</div>	


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













