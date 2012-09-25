<?php include "php/inc/header.inc.php" ?>
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

	//list of order to be bulk-printed
	var gPrintList = [];
	
	$(function(){

			$('.reviewElements, .viewElements').hide();
			$('.success_msg').hide();

			var header = [];

			var tblHeaderComplete = false; 

			//the selected order row that is currently revised or viewed
			var gSelRow = null; 

			//indicates page subsection: overview | review | view
			var gSection = 'overview';

			//new window for printing
			var printWin = null;

			//index for current order that is loaded/printed during bulk actions
			var gPrintIndex  = -1; 

			//order revision status states. 
			var gRevStatus = [null, 'finalized','revised','postponed','canceled','revisedMod'];


			//if this page has been called from torn...
			var gLastPage = $.getUrlVar('lastPage');

			//order overview filter option
			var gFilter = (typeof $.getUrlVar('filter') == "string")? $.getUrlVar('filter'):'pastMonths2Future';
			

			$("#datepicker").datepicker({
				dateFormat 	: 'DD, d MM, yy',
				onSelect : function (dateText, instance){
					//var nd = $.getCustomDate(dateText)
					$('#indicateShopDate').text(dateText);
				}
			}).hide();

			$('#showDatePicker').click(function(){
				$('#datepicker').toggle();
			});

			$.getAixadaDates('getToday', function (date){
				var today = $.datepicker.parseDate('yy-mm-dd', date[0]);
				$("#datepicker").datepicker('setDate', today);
				$("#datepicker").datepicker("refresh");		
				$('#indicateShopDate').text($.getCustomDate(date[0]));		
			});	
			
	

			//STEP 1: retrieve all active ufs in order to construct the table header
			$.ajax({
					type: "POST",
					url: 'php/ctrl/UserAndUf.php?oper=getUfListing&all=0',
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
				url : 'php/ctrl/Orders.php',
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
					url: 'php/ctrl/Orders.php?oper=getProductQuantiesForUfs&order_id='+ gSelRow.attr('orderId')+'&provider_id='+gSelRow.attr("providerId")+'&date_for_order='+gSelRow.attr("dateForOrder"),
					dataType:"xml",
					success: function(xml){

						var quTotal = 0;
						var quShopTotal = 0; 
						var quShop = 0; 
						var lastId = -1; 
						var quShopHTML = '';  
						
						$(xml).find('row').each(function(){
						
							//for the view section, ordered quantities and revised (shop) quantities are shown
							if (gSection == 'view' && gSelRow.attr('orderId') > 0){
								quShop = $(this).find('shop_quantity').text();
								quShop = (quShop == '')? 0:quShop;  //items that did not arrived produce a null value. 
								quShopHTML = (gSection == 'view')? '<span class="shopQuantity">(' +quShop +')</span>':'';
							}
							var product_id = $(this).find('product_id').text();
							var uf_id = $(this).find('uf_id').text();
							var qu = $(this).find('quantity').text();
							var revised = $(this).find('revised').text();
							var arrived = $(this).find('arrived').text();
							var tblCol = '.Col-'+uf_id;
							var tblRow = '.Row-'+product_id;
							var pid	= product_id + '_' + uf_id; 
							
							$(tblCol+tblRow).append('<p id="'+pid+'" class="textAlignCenter">'+qu+''+quShopHTML+'</p>')
							
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
								
								var total = quTotal + "<span class='shopQuantity'>("+quShopTotal+")</span> " + $('#unit_'+lastId).text();
								
								$('#total_'+lastId).html(total);
								quTotal = 0; 
								quShopTotal = 0; 
							}
							
							quTotal += parseFloat(qu); 
							quShopTotal += parseFloat(quShop);
							lastId = product_id; 

						});

						
							var total = quTotal + "<span class='shopQuantity'>("+quShopTotal+")</span> " + $('#unit_'+lastId).text();
						
						$('#total_'+lastId).html(total);


						//don't need revised and arrived column for viewing order
						if (gSection == 'view' || gSection == 'print'){
							$('.revisedCol, .arrivedCol').hide();
							$('.shopQuantity').show();
							$('tr, td').removeClass('toRevise revised missing');
						} else {
							$('.revisedCol, .arrivedCol').show();
							$('.shopQuantity').hide();
						}

						//if we print, copy the table to the printWindow
						if (gSection == 'print' && printWin != null){

							var wrapDiv = $('#orderWrap', printWin.document).children(':first').clone();
							var pname = gPrintList[gPrintIndex].children().eq(2).text(); //get provider name
							var odate = gPrintList[gPrintIndex].children().eq(3).text(); //get order date	
							var tbl = $('#tbl_reviseOrder').clone();			//clone the table with the current order data
							
							$(tbl).attr('id', 'print_order_'+gPrintIndex);	
							$('thead', tbl).prepend('<tr><th colspan="100"><h2>Order for (#'+gSelRow.attr('orderId')+') for '+pname+' for: '+odate+' </h2></th></tr>');	
							$(wrapDiv).prepend(tbl); //add the table to the wrapper							
							$('#orderWrap', printWin.document).append(wrapDiv); //and add the wrapper to the doc in the new window

							if (gPrintIndex == gPrintList.length-1){
								$('.loadingMsg', printWin.document).html("<p>Finished loading</p>").fadeOut(2000);
								$('#orderWrap', printWin.document).children(':first').hide();
								//printWin.print();
							} else {
								$('.loadingMsg', printWin.document).html("<p>Please wait while loading " + (gPrintIndex+1) + "/"+gPrintList.length+" order(s)</p>");
							}

							loadPrintOrder();
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
				width:480,
				height:600,
				buttons: {  
					"<?=$Text['btn_ok'];?>" : function(){
						
						var $this = $(this);
						$.ajax({
							type: "POST",
							url: 'php/ctrl/Orders.php?oper=moveOrderToShop&order_id='+gSelRow.attr('orderId')+'&date='+$.getSelectedDate('#datepicker'),
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
					
					if (!$(this).hasClass('editable') && gSection == 'review'){
						$(this).children(':first')
							.addClass('editable')
							.editable('php/ctrl/Orders.php', {			//init the jeditable plugin
									submitdata : {
										oper: 'editQuantity',
										order_id : gSelRow.attr('orderId')
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
					url: 'php/ctrl/Orders.php?oper=setOrderItemStatus&order_id='+gSelRow.attr('orderId')+'&product_id='+product_id+'&has_arrived='+has_arrived+'&is_revised='+is_revised,
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
					url: 'php/ctrl/Orders.php?oper=setOrderItemStatus&order_id='+gSelRow.attr('orderId')+'&product_id='+product_id+'&has_arrived='+has_arrived+'&is_revised='+is_revised,
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
			 *		ORDER VIEW  / DETAILED INFO
			 **********************************************************/
			 $('#tbl_orderDetailInfo tbody').xml2html('init',{
					url : 'php/ctrl/Orders.php',
					loadOnInit : false
			 });




				

			/***********************************************************
			 *		ORDER REVISION STATUS
			 **********************************************************/
			 $("#btn_revised").button({
				 icons: {
		        		primary: "ui-icon-check"
		        	}
				 })
       			.click(function(e){
					$("#dialog_orderStatus").dialog("close");
       			});
			
			 $("#btn_canceled").button({
				 icons: {
		        		primary: "ui-icon-cancel"
		        	}
				 })
       			.click(function(e){
					setOrderStatus(4);
       			});
    			
			 $("#btn_postponed").button({
				 icons: {
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
					url: 'php/ctrl/Orders.php?oper=setOrderStatus&order_id='+gSelRow.attr('orderId')+'&status='+status,
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
			//var timePeriod = (gFilter != '')? gFilter:'pastMonths2Future';
			$('#tbl_orderOverview tbody').xml2html('init',{
				url : 'php/ctrl/Orders.php',
				params : 'oper=getOrdersListing&filter='+gFilter, 
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
						statusTd.attr('revisionStatus',status);
						switch(status){
							case "1": 
								statusTd.attr('title','Order has been sent to provider').html('<span class="tdIconCenter ui-icon ui-icon-mail-closed"></span>');
								break;
							case "2": 
								statusTd.attr('title','Revised and distributed without changes').addClass('asOrdered').html('<span class="tdIconCenter ui-icon ui-icon-check"></span>');
								break;
							case "3": 
								statusTd.attr('title','Order has been postponed').addClass('postponed').html('<span class="tdIconCenter ui-icon ui-icon-help"></span>');
								break;
							case "4": 
								statusTd.attr('title','Order has been canceled').addClass('orderCanceled').html('<span class="tdIconCenter ui-icon ui-icon-cancel"></span>');
								break;
							case "5":
								statusTd.attr('title','Revised with some modifications').addClass('withChanges').html('<span class="tdIconCenter ui-icon ui-icon-check"></span>');
						}
					}

					
					if (orderId > 0){ 

					} else {
						//while open and not sent off, no order_id exists
						$(row).children().eq(1).html('<p>-</p>');
						$(row).children().eq(5).html('<p><a href="javascript:void(null)" class="finalizeOrder">Finalize now</a></p>');
					}
				},
				complete : function (rowCount){
					$("#tbl_orderOverview").trigger("update"); 
					if (rowCount == 0){
						$.showMsg({
							msg:'Sorry, no orders match the selected criteria. ',
							type: 'warning'});
					}
					$('#tbl_orderOverview tbody tr:even').addClass('rowHighlight'); 					
				}
			});


			/**
			 *	To finalize an order means no further modifications are possile. 
			 */
			$('.finalizeOrder')
				.live('click', function(e){
					var date = $(this).parents('tr').attr('dateForOrder');
					var providerId = $(this).parents('tr').attr('providerId');
					var timeLeft = $(this).parents('tr').children().eq(4).text();
					var msgt = 'You are about to finalize an order. This means that no further modifications are possible to this order. Are you sure to continue?';
					
					if (timeLeft > 0){
						msgt = 'This order is still open. Finalizing it now means that no further items can be ordered for this date and provider. Are you sue you want to continue?'
					}
					
					$.showMsg({
						msg: msgt,
						buttons: {
							"<?=$Text['btn_ok'];?>":function(){						
								finalizeOrder(providerId, date);
								$(this).dialog("close");
							},
							"<?=$Text['btn_cancel'];?>" : function(){
								$( this ).dialog( "close" );
							}
						},
						type: 'confirm'});
			});

			
			
			$('#tbl_orderOverview tbody tr')
			.live('mouseover', function(){
				$(this).removeClass('highlight').addClass('ui-state-hover');
				
			})
			.live('mouseout',function(){
				$(this).removeClass('ui-state-hover');
				
			});
			
			
			$("#tbl_orderOverview").tablesorter(); 
			$("#tbl_orderOverview").bind('sortEnd', function(){
				$('tr',this).removeClass('rowHighlight')
				$('tr:even',this).addClass('rowHighlight');
			});

			
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
					gSelRow = $(this).parents('tr'); 
					var shopDate 		= $(this).parents('tr').children().eq(6).text();

					$('.col').hide();

					//if table header ajax call has not finished, wait
					if (!tblHeaderComplete){
						$.showMsg({
							msg:'The table header is still being constructed. Depending on your internet connection this might take a little while. Try again in 5 seconds. ',
							type: 'error'});
						return false; 
					}

					//need the order id
					if (gSelRow.attr('orderId') <= 0){
						$.showMsg({
							msg:'No valid ID for order found! This order has not been sent off to the provider!!',
							type: 'error'});
						return false; 
					}

					
					//if shop date exists, check if it items have already been moved to shop_item and/or validated
					if (shopDate != ''){
						$.post('php/ctrl/Orders.php?oper=checkValidationStatus&order_id='+gSelRow.attr('orderId'), function(xml) {
							
							var hasCart = false; 
							var isValidated = false; 
							$(xml).find('row').each(function(){

								 if ($(this).find('cart_id').text() > 0) hasCart = true; 
								 if ($(this).find('validated').text() > 0) isValidated = true; 
								 

							});

							if (hasCart && !isValidated){
								$.showMsg({
									msg:'The items of this order have already been revised and placed into people\'s carts for the indicated shop date. Revising them again will override the modifications already made and potentially interfere with people\'s own corrections. <br/><br/> Are you really sure you want to proceed anyway?! <br/><br/>Pressing OK will delete the items from the existing shopping carts and start the order-revision process again.',
									buttons: {
										"<?=$Text['btn_ok'];?>":function(){	
											//reset this order to "finalized"
											$(this).html('Please wait while the order is being reset....');
											gSelRow.children().eq(8).attr('revisionStatus',1);
											var $this = $(this);
											resetOrder(gSelRow.attr('orderId'), function(){
												$this.html('Done!');
												setTimeout(function(){
													switchTo('review', {});
													$this.dialog("close");
												}, 1000);
												
											});					
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
								switchTo('review', {});
							}		 
						});
					} else {
						switchTo('review', {});
					}
				});


				//view selected order (no editing)
				$('.viewOrderBtn')
					.live('click', function(e){
						gSelRow = $(this).parents('tr'); 
						$('.col').hide();	
						switchTo('view',{});
					});

				
				//print the selected order. If more than one is selected, confirm bulk print
				$('.printOrderBtn')
				.live('click', function(e){
					$this = $(this);
					if ($('input:checkbox[name="bulkAction"][checked="checked"]').length > 1){
						$.showMsg({
							msg:'There is more than one order currently selected. Do you want to print them all in one go?',
							width:500,
							buttons: {
								"Yes, print all":function(){						
									printQueue();
									$(this).dialog("close");
								},
								
								"No, just one" : function(){
									$('input:checkbox[name="bulkAction"]').attr('checked', false);
									$this.parents('tr').children('td:first').find('input').attr('checked','checked');
									printQueue();
									$( this ).dialog( "close" );
								},
								"Cancel" : function(){
									$( this ).dialog( "close" );
								}
							},
							type: 'warning'});

					} else {
						$(this).parents('tr').children('td:first').find('input').attr('checked','checked');
						printQueue();
					}
				});


				
			
				$("#tblViewOptions")
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

	
				
				//do selected stuff with bunch of orders (from overview)
				$('#bulkActions')
					.change(function(){

						switch ($("option:selected", this).val()){
							case "print": 
								printQueue();
								break;
							case "download":
								alert("todo");
								break;						
						}
				});



				/**
				 *	prepares the printing queue of the selected orders. 
				 */
				function printQueue(){
					gPrintIndex = -1; 
					gPrintList = [];
					gSelRow = null;  

					if ($('input:checkbox[name="bulkAction"][checked="checked"]').length  == 0){
						$.showMsg({
							msg:'There are no orders selected!',
							buttons: {
								"<?=$Text['btn_ok'];?>":function(){						
									$(this).dialog("close");
								}
							},
							type: 'warning'});
					} else {

						printWin = window.open('tpl/<?=$tpl_print_orders;?>');
						printWin.focus();
										
						var i = 0;  						
						$('input:checkbox[name="bulkAction"]').each(function(){
							if ($(this).is(':checked')){
								gPrintList[i++] = $(this).parents('tr');
							} 
						});
						
	
						loadPrintOrder();
					}
				}
				

				/**
				 *  part of a call sequence to load the marked orders one after
				 *  the other, clone the data in the table and then copy it to the new
				 *  print window. 
				 */
				function loadPrintOrder(){

					gPrintIndex++;
					
					if (gPrintIndex == gPrintList.length) return false; 
					
					$('.col').hide();
					gSection = 'print';
					gSelRow = gPrintList[gPrintIndex]; 

					//need to introduce a delay here in order to load all orders correctly. don't ask me why.... 
					setTimeout(function(){
						$('#tbl_reviseOrder tbody').xml2html("reload", {						//load order details for printing
							params : 'oper=getOrderedProductsList&order_id='+gSelRow.attr("orderId")+'&provider_id='+gSelRow.attr("providerId")+'&date='+gSelRow.attr("dateForOrder")
						})
					}, 1000); 
				}


				/**
				 *	Finalizes an order: synon. for sending it to the 
				 * 	provider: an order ID is assigned, no more modifications are possible. 
				 */
				function finalizeOrder(providerId, orderDate){
					$.ajax({
						type: "POST",
						url: 'php/ctrl/Orders.php?oper=finalizeOrder&provider_id='+providerId+'&date='+orderDate,
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
				
					

				/**
				 *	if an already revised order is changed in its status (again revised, postponed, etc.) 
				 *  need to make sure that already distributed items get deleted. 
				 */
				function resetOrder(orderId, callbackfn){
					$.ajax({
						type: "POST",
						url: 'php/ctrl/Orders.php?oper=resetOrder&order_id='+orderId,
						success: function(txt){
							callbackfn.call(this);
						},
						error : function(XMLHttpRequest, textStatus, errorThrown){
							$.showMsg({
								msg:XMLHttpRequest.responseText,
								type: 'error'});
							
						}
					});		

				}

				
				/**
				 *	switch between the order overview page and the revision/detail page
				 */
				function switchTo(page, options){

					switch (page){
						case 'overview':
							$('.reviewElements, .viewElements').hide();
		    				$('.overviewElements').fadeIn(1000);
		    				gSelRow = null;	
		    				$('#tbl_orderOverview tbody').xml2html('reload');	
							break;

						case 'review':
							var title = gSelRow.children().eq(2).text() + " (#"+gSelRow.attr('orderId')+") for " + $.getCustomDate(gSelRow.attr('dateForOrder'));
							var sindex = gSelRow.children().eq(8).attr('revisionStatus');
							
							$('.providerName').text(title);							
							$('.overviewElements').hide();
							$('.reviewElements').fadeIn(1000);

							$('#dialog_orderStatus button').button('enable');
							$('#btn_'+gRevStatus[sindex]).button('disable');
							$('#currentOrderStatus').html(gRevStatus[sindex]);
							$('#dialog_orderStatus').dialog("open");
							$('#tbl_reviseOrder tbody').xml2html("reload", {						//load order details for revision
								params : 'oper=getOrderedProductsList&order_id='+gSelRow.attr("orderId")+'&provider_id='+gSelRow.attr("providerId")+'&date='+gSelRow.attr("dateForOrder")
							})
							break;
							
						case 'view':
							var title = gSelRow.children().eq(2).text() + " for " + $.getCustomDate(gSelRow.attr('dateForOrder'));
							//$('#viewOrderRevisionStatus') set the order status here. 
							$('.providerName').text(title);							
							$('.overviewElements').hide();
							$('.viewElements').fadeIn(1000);
							$('#tbl_reviseOrder tbody').xml2html("reload", {						//load order details for revision
								params : 'oper=getOrderedProductsList&order_id='+gSelRow.attr("orderId")+'&provider_id='+gSelRow.attr("providerId")+'&date='+gSelRow.attr("dateForOrder")
							})
							
							$('#tbl_orderDetailInfo tbody').xml2html('reload',{						//load the info of this order
								params : 'oper=orderDetailInfo&order_id='+gSelRow.attr("orderId")+'&provider_id='+gSelRow.attr("providerId")+'&date='+gSelRow.attr("dateForOrder"),
								complete : function(rowCount){

								}
			 				});
							
							$('#btn_setShopDate').hide();
							break;
					}
					gSection = page; 
				}


				
			
			
	});  //close document ready


	
	
</script>


</head>
<body>
<div id="wrap">
	<div id="headwrap">
		<?php include "php/inc/menu2.inc.php" ?>
	</div>
	<!-- end of headwrap -->
	
	<div id="stagewrap">
	
		<div id="titlewrap" class="ui-widget">
			<div id="titleLeftCol">
				<button id="btn_overview" class="floatLeft reviewElements viewElements"><?php echo $Text['overview'];?></button>
				<h1 class="reviewElements">Manage order detail for <span class="providerName"></span></h1>
				<h1 class="viewElements">Order detail for <span class="providerName"></span></h1>
		    	<h1 class="overviewElements">Manage orders</h1>
		    </div>
		   	<div id="titleRightCol">
		   		<button id="btn_setShopDate" class="reviewElements btn_right" title="Place order-items into HU shopping carts">Distribute!</button>
				<button	id="tblViewOptions" class="overviewElements btn_right">Filter orders</button>
				<div id="tblOptionsItems" class="hidden">
					<ul>
						<li><a href="javascript:void(null)" id="ordersForToday">Expected today</a></li>
						<li><a href="javascript:void(null)" id="nextWeek">Next week</a></li>
						<li><a href="javascript:void(null)" id="futureOrders">All future orders</a></li>
						<li><a href="javascript:void(null)" id="pastMonth">Last month</a></li>
						<li><a href="javascript:void(null)" id="pastYear">Last year</a></li>
						<li><a href="javascript:void(null)" id="limboOrders">Postponed</a></li>
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
						<th>Sent off to provider</th>
						<th class="clickable">Shop date <span class="ui-icon ui-icon-triangle-2-n-s floatRight"></span></th>
						<th class="clickable">Order total  <span class="ui-icon ui-icon-triangle-2-n-s floatRight"></span></th>
						<th>Status</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<tr id="{id}" orderId="{id}" dateForOrder="{date_for_order}" providerId="{provider_id}">
						<td><input type="checkbox" name="bulkAction"/>
						<td>{id}</td>
						<td class="textAlignRight minPadding">{provider_name}</td>
						<td class="textAlignCenter">{date_for_order}</td>
						<td class="textAlignCenter">{time_left}</td>
						<td class="textAlignCenter">{ts_send_off}</td>
						<td class="textAlignCenter">{date_for_shop}</td>
						<td class="textAlignRight">{order_total} €&nbsp;&nbsp;</td>
						<td class="textAlignCenter">{revision_status}</td>
						<td class="maxwidth-100">				
							<p class="ui-corner-all iconContainer ui-state-default floatLeft viewOrderBtn"><span class="ui-icon ui-icon-zoomin" title="View order"></span></p>
							<p class="ui-corner-all iconContainer ui-state-default floatLeft printOrderBtn"><span class="ui-icon ui-icon-print" title="Print order"></span></p>							
							<p class="ui-corner-all iconContainer ui-state-default floatRight reviseOrderBtn"><span class="ui-icon ui-icon-check" title="Revise order"></span></p>
						</td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<td><span class="ui-icon ui-icon-arrowreturnthick-1-e"></span></td>
						<td colspan="6">
							<select id="bulkActions">
								<option value="-1">With selected...</option>
								<option value="print">Print</option>
								<option value="download">Download as zip (TODO)</option>
							</select>
						</td>
					</tr>
				</tfoot>
			</table>
			</div> <!-- widget content -->
		</div>
		
		<div id="viewOrderInfo" class="ui-widget viewElements">
			<div class="ui-widget-header ui-corner-all textAlignCenter">
				<h3>&nbsp;</h3>
			</div>
			<div class="ui-widget-content ui-corner-all">
				<table id="tbl_orderDetailInfo">
					<thead>
						<tr>
							<th colspan="2">Provider</th>
							<th colspan="3">Order</th>
							<th colspan="3">Money</th>
							<th colspan="2">Responsible UF</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><em>{name}</em><br/>
								NIE/NIF: {nif}<br/><br/>
								Contact:<br/> 
								{contact}<br/>
								{address}<br/>
								{zip} {city}<br/>
								Email:&nbsp;{email}<br/>
								Phone: {phone1} / {phone2}<br/>
								Bank: {bank_name}<br/>
								Account number: {bank_account}
							</td>
							<td class="maxwidth-100">&nbsp;</td>
							<td>
								Order id: <br/>
								Ordered for: <br/>
								Finalized : <br/>
								Shop date: <br/><br/>
								Status: <br/>
								Notes: <br/>
								Delivery reference: <br/>
								Payment reference
							</td>
							<td>
								{order_id}<br/>
								{date_for_order} <br/>
								{ts_sent_off} <br/>
								{date_for_shop} <br/><br/>
								<span id="viewOrderRevisionStatus">{revision_status}</span><br/>
								{notes}<br/>
								{delivery_ref}<br/>
								{payment_ref}
								
							</td>
							<td class="maxwidth-100">&nbsp;</td>
							<td>
								Total (original order): <br/>
								Total (after revision): <br/>
								Total (validated income): 
							</td>
							<td class="textAlignRight">
								{total}<br/>
								{delivered_total}<br/>
								{validated_income}
							</td>
							<td class="maxwidth-100">&nbsp;</td>
							<td>
								{uf_name} ({uf_id})
							</td>
						</tr>
						
					</tbody>
				</table>
			</div>
		</div>
		<p>&nbsp;</p>
		<div id="reviseOrder" class="ui-widget reviewElements viewElements">
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
	<p>You are about to change the status of this order. Currently your order is marked as "<span id="currentOrderStatus" class="boldStuff"></span>". Change it to one of the following options: </p>
	<p>&nbsp;</p>
	<table>
		<tr><td class="textAlignCenter"><button id="btn_revised">Arrived!</button></td><td>&nbsp;</td><td>Most or all ordered items have arrived. Proceed to revise and distribute the products to shopping carts...</td></tr>					
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr><td class="textAlignCenter"><button id="btn_postponed">Postpone!</button></td><td>&nbsp;</td><td>The order did not arrive for the ordered date but probably will in the upcoming weeks.</td></tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr><td class="textAlignCenter"><button id="btn_canceled">Cancel!</button></td><td>&nbsp;</td><td>Ordered items will never arrive.</td></tr>
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













