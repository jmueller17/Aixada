<?php include "php/inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] ." -  " .$Text['head_ti_manage_orders']; ?></title>

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

			//loading animation
			$('.loadSpinner').attr('src', "img/ajax-loader-<?=$default_theme;?>.gif"); 
			

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

			//global today date
			var gToday = '';

			
			//set shoping date
			$("#datepicker").datepicker({
				dateFormat 	: 'DD, d MM, yy',
				onSelect : function (dateText, instance){
					$('#indicateShopDate').text(dateText);
				}
			}).hide();

			
			//date for order for convert preorder
			$("#datepicker2").datepicker({
				dateFormat 	: 'DD, d MM, yy',
				onSelect : function (dateText, instance){

				},
				beforeShowDay: function(date){ //deactivate past dates
						var today = $.datepicker.formatDate('yy-mm-dd', gToday)
						var ymd = $.datepicker.formatDate('yy-mm-dd', date);
						if (ymd <= today) {
						    return [false,"","Unavailable"];			    
						} else {
							  return [true, ""];
						}

				}
			});

			
			$('#showDatePicker').click(function(){
				$('#datepicker').toggle();
			});

			$.getAixadaDates('getToday', function (date){
				gToday = $.datepicker.parseDate('yy-mm-dd', date[0]);
				$("#datepicker").datepicker('setDate', gToday);
				$("#datepicker").datepicker("refresh");		
				$("#datepicker2").datepicker("refresh");		
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

						theadStr += '<th><?=$Text['total'];?></th>';
						theadStr += '<th class="revisedCol"><?=$Text['ostat_revised'];?></th>';
						
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
						tbodyStr += '<td class="'+colClass+' '+rowClass+' hidden interactiveCell toRevise" col="'+header[i]+'" row="'+product_id+'"></td>';
					}

					//product total quantities
					tbodyStr += '<td id="total_'+product_id+'" class="nobr totalQu"></td>';
					
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
							
							$(tblCol).removeClass('hidden').show();
							

							//calculate total quantities and update last table cell
							if (lastId == -1) {lastId = product_id}; 							
							if (lastId != product_id){
								
								var total = "<span>"+quTotal.toFixed(2)+"</span><span class='shopQuantity'>("+quShopTotal+")</span> " + $('#unit_'+lastId).text();
								
								$('#total_'+lastId).html(total);
								quTotal = 0; 
								quShopTotal = 0; 
							}
							
							quTotal += new Number(qu); 
							quShopTotal += new Number(quShop);
							lastId = product_id; 

						});

						
						var total = "<span>"+quTotal.toFixed(2)+"</span><span class='shopQuantity'>("+quShopTotal+")</span> " + $('#unit_'+lastId).text();
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
							$('thead', tbl).prepend("<tr><th colspan='100'><h2><?=$Text['order'];?> (#"+gSelRow.attr('orderId')+") <?=$Text['for'];?> "+pname+".&nbsp;&nbsp;&nbsp; <?=$Text['date_for_order'];?>: "+odate+" </h2></th></tr>");	
							$(wrapDiv).prepend(tbl); //add the table to the wrapper							
							$('#orderWrap', printWin.document).append(wrapDiv); //and add the wrapper to the doc in the new window

							if (gPrintIndex == gPrintList.length-1){
								$('.loadingMsg', printWin.document).html("<p><?=$Text['finished_loading'];?></p>").fadeOut(2000);
								$('#orderWrap', printWin.document).children(':first').hide();
								//printWin.print();
							} else {
								$('.loadingMsg', printWin.document).html("<p><?=$Text['loading']; ?> " + (gPrintIndex+1) + "/"+gPrintList.length+" order(s)</p>");
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
							msg:"<?=$Text['msg_err_unrevised']?>",
							buttons: {
								"<?=$Text['btn_dis_anyway'];?>":function(){						
									$('#dialog_setShopDate').dialog("open");
									$(this).dialog("close");
								},
								"<?=$Text['btn_remaining'];?>" : function(){
									$( this ).dialog( "close" );
								}
							},
							type: 'confirm'});

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
								$this.button('disable');
								setTimeout(function(){
									$this.dialog( "close" )
									$('.interactiveCell').hide();
									$('.success_msg').hide().next().show();
									//reload order list
									$('#tbl_orderOverview tbody').xml2html('reload');
									switchTo('overview');
								},2000);
							},
							error : function(XMLHttpRequest, textStatus, errorThrown){
								$.showMsg({
									msg:XMLHttpRequest.responseText,
									type: 'error'});
								
							}
						});
	
						
						},
				
					"<?=$Text['btn_close'];?>"	: function(){
						$( this ).dialog( "close" );
						} 
				}
			});
				

			$('#dialog_convertPreorder').dialog({
				autoOpen:false,
				width:420,
				height:500,
				buttons: {  
					"<?=$Text['btn_save'];?>" : function(){
						var $this = $(this);

						if ($.getSelectedDate('#datepicker2') == $.datepicker.formatDate('yy-mm-dd', gToday)){
							$.showMsg({
								msg:"Please select an order date starting from at least tomorrow onwards.",
								type: 'warning'});
							return false; 
						}
						
						$.ajax({
							type: "POST",
							url: 'php/ctrl/Orders.php?oper=preorderToOrder&provider_id='+gSelRow.attr('providerId')+'&date_for_order='+$.getSelectedDate('#datepicker2'),
							success: function(txt){

								$this.button('disable');
								setTimeout(function(){
									$this.dialog( "close" )

									//switchTo('overview');
								},2000);
							},
							error : function(XMLHttpRequest, textStatus, errorThrown){
								$.showMsg({
									msg:XMLHttpRequest.responseText,
									type: 'error'});
								
							}
						});
	
						
						},
				
					"<?=$Text['btn_close'];?>"	: function(){
						$( this ).dialog( "close" );
						} 
				}
			});


			//adjust total quantities
			$('td.totalQu')
				.live('mouseover', function(e){
					
					if (!$(this).hasClass('editable') && gSection == 'review'){
						var pid = $(this).prev().attr('row')
						$(this).children(':first')
							.addClass('editable')
							.editable('php/ctrl/Orders.php', {			//init the jeditable plugin
									submitdata : {
										oper: 'editTotalQuantity',
										order_id : gSelRow.attr('orderId'),
										product_id : pid 
										},
									name 	: 'quantity',
									indicator: 'Saving',
								    tooltip	: 	'Click to adjust total quantities',
									callback: function(xml, settings){
									    var pid = settings.submitdata.product_id;
									    var total_quantity = 0;
									    $(xml).find('row').each(function(){
										    var uf_id = $(this).find('uf_id').text();
										    var quantity = $(this).find('quantity').text();
										    total_quantity = total_quantity + parseFloat(quantity);
										    var selector = '.Col-' + uf_id + '.Row-' + pid;
										    
										    $(selector).removeClass('toRevise').addClass('revised').text(quantity);
										    
										})
										$('#ckboxRevised_'+pid).attr('checked','checked');
										$('#total_' + pid).children(':first').empty().text(total_quantity.toFixed(2));
									}//end callback 
							});
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
								    tooltip	: 	'<?=$Text['uf_short'];?> ' + col + '\n' + product + '\n<?=$Text['click_to_edit'];?>',
									callback: function(value, settings){
										$(this).parent().removeClass('toRevise').addClass('revised');
									} 
						});

					}

			})
			.live('mouseout', function(e){
				//var col = $(this).attr('col');
				//var row = $(this).attr('row');

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


			//edit/save the notes, payment_ref and delivery_ref pages
			$('.editOrderDetail')
				.live('focus', function(e){
					if (gSelRow.attr('orderId') > 0) {
					} else {
						$.showMsg({
							msg:"<?=$Text['msg_err_edit_order'];?>",
							type: 'warning'});
					}
				})
				.live('keyup', function(e){
					//hitting return saves it
					if (e.keyCode == 13 && gSelRow.attr('orderId') > 0){
						$.ajax({
							type: "POST",
							url: 'php/ctrl/Orders.php?oper=editOrderDetailInfo&order_id='+gSelRow.attr('orderId')+"&delivery_ref="+$('#orderDetailDeliveryRef').val()+"&payment_ref="+$('#orderDetailPaymentRef').val()+"&order_notes="+$('#orderDetailNotes').val(),
							beforeSend : function (xhr){
								$('.editOrderDetail').attr('disabled',true);
							},
							success: function(txt){
								$.showMsg({
									msg:"Saved successfully!",
									type: 'success'});
								
							},
							error : function(XMLHttpRequest, textStatus, errorThrown){
								$.showMsg({
									msg:XMLHttpRequest.responseText,
									type: 'error'});
							},
							complete : function(){
								$('.editOrderDetail').attr('disabled',false);
							}
						});
					}

				})

				//revise button which switches from order detail view to revise screen
				$("#btn_setReview")
				.button({
					icons: {
			        	primary: "ui-icon-check"
					}
			    }).click(function(e){
				    //TODO needs to trigger the revise buttons to check order status
			    	//$('.reviseOrderBtn').trigger('click');
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

			//upon entering the revision page, the overall order status is set. 
			function setOrderStatus(status){
				$.ajax({
					type: "POST",
					url: 'php/ctrl/Orders.php?oper=setOrderStatus&order_id='+gSelRow.attr('orderId')+'&status='+status,
					success: function(txt){
						$("#dialog_orderStatus").dialog("close");
						$('#tbl_orderOverview tbody').xml2html('reload');
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
				beforeLoad : function(){
					$('.loadSpinner').show();
				},
				rowComplete : function (rowIndex, row){
					var tds = $(row).children();
					var orderId = $(row).attr("id");
					var timeLeft = parseInt(tds.eq(4).text());
					var status = tds.eq(8).text();
					var isPreorder = (tds.eq(3).text() == '1234-01-23')? true:false;

					if (isPreorder){ //preorder has no closing date
						tds.eq(3).text('preorder!');
						tds.eq(6).text('-');
					}
					
					if (timeLeft > 0){ 	// order is still open
						tds.eq(8).html('<span class="tdIconCenter ui-icon ui-icon-unlocked" title="<?=$Text['order_open'];?>"></span>');

					} else if (timeLeft < 0 && isPreorder){ //preorder is not closed 
						tds.eq(4).text("-");
						
					} else {			//order is closed
						tds.eq(4).text("<?=$Text['closed'];?>");
						var statusTd = $(row).children().eq(8); 
						statusTd.attr('revisionStatus',status);
						formatRevisionStatus(statusTd);
					}


					if (orderId > 0 && !isPreorder){ //if it has order id, it has been sent off to provider
						var ts = tds.eq(5).text();
						var str = (ts == "0000-00-00 00:00:00")? '-':'<p title="'+ts+'">'+ts.substr(0,10)+'</p>';
						tds.eq(5).html(str);
					} else {
						//while open and not sent off, no order_id exists. show the finalize button
						tds.eq(1).html('<p>-</p>');
						tds.eq(5).html('<p><a href="javascript:void(null)" class="finalizeOrder"><?=$Text['finalize_now'];?></a></p>');
					}
				},
				complete : function (rowCount){
					$("#tbl_orderOverview").trigger("update"); 
					if (rowCount == 0){
						$.showMsg({
							msg:"<?=$Text['msg_err_order_filter'];?>",
							type: 'warning'});
					}
					$('#tbl_orderOverview tbody tr:even').addClass('rowHighlight');
					$('.loadSpinner').hide(); 					
				}
			});


			/**
			 *	To finalize an order means no further modifications are possile. 
			 */
			$('.finalizeOrder')
				.live('click', function(e){
					gSelRow = $(this).parents('tr');
					var date = $(this).parents('tr').attr('dateForOrder');
					var providerId = $(this).parents('tr').attr('providerId');
					var timeLeft = $(this).parents('tr').children().eq(4).text();
					var msgt = "<?=$Text['msg_finalize'] ;?>";

					
					if (date == '1234-01-23'){ // is preorder, finalize means to assign also an order date
						$("#dialog_convertPreorder").dialog("open");

						e.stopPropagation();
						return false; 
					}

					
					if (timeLeft > 0){
						msgt = "<?=$Text['msg_finalize_open'];?>"
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
					
					e.stopPropagation();
			});

			
			
			$('#tbl_orderOverview tbody tr')
				.live('mouseover', function(e){
					$(this).addClass('ui-state-hover');
					
				})
				.live('mouseout',function(e){
					$(this).removeClass('ui-state-hover');
					
				})
				.live('click', function(e){
					$('#tbl_orderOverview tbody tr').removeClass('ui-state-highlight');
					gSelRow = $(this); 
					gSelRow.addClass('ui-state-highlight');
					$('.col').hide();	
					switchTo('view',{});
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
					$('#tbl_orderOverview tbody tr').removeClass('ui-state-highlight');
					gSelRow = $(this).parents('tr'); 
					gSelRow.addClass('ui-state-highlight');
					
					var shopDate 		= $(this).parents('tr').children().eq(6).text();
					var status = gSelRow.children().eq(8).attr('revisionStatus');
					
					$('.col').hide();
					
					//order comes from pre v2.5 database we miss some info
					if (status == "-1"){
						$.showMsg({
							msg:"<?=$Text['msg_err_miss_info'];?>",
							type: 'error'});
						return false; 
					}
					
					//if table header ajax call has not finished, wait
					if (!tblHeaderComplete){
						$.showMsg({
							msg:"<?=$Text['msg_wait_tbl'];?>",
							type: 'error'});
						return false; 
					}

					//need the order id
					if (gSelRow.attr('orderId') <= 0){
						$.showMsg({
							msg:"<?=$Text['msg_err_invalid_id'];?>",
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
							//when migrating from old database, we have no order_id reference in shop_item and this
							//test fails!! 
							//alert("has cart " + hasCart + "  isvalidated "  + isValidated);
							if (hasCart && !isValidated){
								$.showMsg({
									msg:"<?=$Text['msg_revise_revised'];?>",
									buttons: {
										"<?=$Text['btn_ok'];?>":function(){	
											//reset this order to "finalized"
											$(this).html('<?=$Text['wait_reset'];?>');
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
									msg:"<?=$Text['msg_err_already_val'];?>",
									type: 'error'});
							} else {
								switchTo('review', {});
							}		 
						});
					} else {
						switchTo('review', {});
					}

					e.stopPropagation();
				});

			
				//global header print buttun
				$("#btn_print").button({
				 icons: {
		        		primary: "ui-icon-print"
		        	}
				 })
        		.click(function(e){
        			printQueue();
        		});


        		//download selected as zip
				$("#btn_zip").button({
					 icons: {
			        		primary: "ui-icon-suitcase"
			        	}
					 })
	        		.click(function(e){
	        			if ($('input:checkbox[name="bulkAction"][checked="checked"]').length  == 0){
							$.showMsg({
								msg:"<?=$Text['msg_err_noselect'];?>",
								buttons: {
									"<?=$Text['btn_ok'];?>":function(){						
										$(this).dialog("close");
									}
								},
								type: 'warning'});
	        			} else {


		        		
		        			var orderRow = ''; 								
							$('input:checkbox[name="bulkAction"][checked="checked"]').each(function(){
								orderRow += '<input type="hidden" name="order_id[]" value="'+$(this).parents('tr').attr('orderId')+'"/>';
								orderRow += '<input type="hidden" name="provider_id[]" value="'+$(this).parents('tr').attr('providerId')+'"/>';
								orderRow += '<input type="hidden" name="date_for_order[]" value="'+$(this).parents('tr').attr('dateforOrder')+'"/>';
							});
							$('#submitZipForm').empty().append(orderRow);
							getZippedOrders();
	        			}
	        		});

				//view selected order (no editing)
				/*$('.viewOrderBtn')
					.live('click', function(e){
						gSelRow = $(this).parents('tr'); 
						$('.col').hide();	
						switchTo('view',{});
					});*/

				
				//print the selected order. If more than one is selected, confirm bulk print
				/*$('.printOrderBtn')
				.live('click', function(e){
					$this = $(this);
					if ($('input:checkbox[name="bulkAction"][checked="checked"]').length > 1){
						$.showMsg({
							msg:"<?=$Text['print_several'];?>",
							width:500,
							buttons: {
								"<?=$Text['btn_yes_all'];?>":function(){						
									printQueue();
									$(this).dialog("close");
								},
								"<?=$Text['btn_just_one']?>" : function(){
									$('input:checkbox[name="bulkAction"]').attr('checked', false);
									$this.parents('tr').children('td:first').find('input').attr('checked','checked');
									printQueue();
									$( this ).dialog( "close" );
								},
								"<?=$Text['btn_cancel'];?>" : function(){
									$( this ).dialog( "close" );
								}
							},
							type: 'warning'});

					} else {
						$(this).parents('tr').children('td:first').find('input').attr('checked','checked');
						printQueue();
					}
					e.stopPropagation();
				});*/


				
			
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


				//bulk actions
				$('input[name=bulkAction]')
					.live('click', function(e){
						e.stopPropagation();
					})
				
				//do selected stuff with bunch of orders (from overview)
				$('#bulkActionsTop, #bulkActionsBottom')
					.change(function(e){

						switch ($("option:selected", this).val()){
							case "print": 
								printQueue();
								break;
							case "download":
								var orderRow = ''; 								
								$('input:checkbox[name="bulkAction"][checked="checked"]').each(function(){
									orderRow += '<input type="hidden" name="order_id[]" value="'+$(this).parents('tr').attr('orderId')+'"/>';
									orderRow += '<input type="hidden" name="provider_id[]" value="'+$(this).parents('tr').attr('providerId')+'"/>';
									orderRow += '<input type="hidden" name="date_for_order[]" value="'+$(this).parents('tr').attr('dateforOrder')+'"/>';
								});
								$('#submitZipForm').empty().append(orderRow);
								getZippedOrders();
								break;						
						}
					});

				$('#toggleBulkActions')
					.click(function(e){
						if ($(this).is(':checked')){
							$('input:checkbox[name="bulkAction"]').attr('checked','checked');
						} else {
							$('input:checkbox[name="bulkAction"]').attr('checked',false);
						}
						e.stopPropagation();
					});


				
				/**
				 *	download selected orders in zipfile
				 */
				function getZippedOrders(){
					$.ajax({
						type: "POST",
						url: 'php/ctrl/Orders.php?oper=bundleOrders',
						data : $('#submitZipForm').serialize(),
						success: function(zipURL){
							window.frames['dataFrame'].window.location = zipURL;
						},
						error : function(XMLHttpRequest, textStatus, errorThrown){
							$.showMsg({
								msg:XMLHttpRequest.responseText,
								type: 'error'});
						},
						complete : function(){
							$('#bulkActionsTop, #bulkActionsBottom').val(-1).attr('selected','selected');
						}
					});
				}


				/**
				 * formats table cells according to order status
				 */
				function formatRevisionStatus(td){
				
					switch(td.text()){
						case "1": 
							td.attr("title","<?php echo $Text['ostat_desc_sent'];?>").html('<span class="tdIconCenter ui-icon ui-icon-mail-closed"></span>');
							break;
						case "2": 
							td.attr("title","<?php echo $Text['ostat_desc_nochanges']; ?>").addClass('asOrdered').html('<span class="tdIconCenter ui-icon ui-icon-check"></span>');
							break;
						case "3": 
							td.attr("title","<?php echo $Text['ostat_desc_postponed']; ?>").addClass('postponed').html('<span class="tdIconCenter ui-icon ui-icon-help"></span>');
							break;
						case "4": 
							td.attr("title","<?php echo $Text['ostat_desc_cancel']; ?>").addClass('orderCanceled').html('<span class="tdIconCenter ui-icon ui-icon-cancel"></span>');
							break;
						case "5":
							td.attr("title","<?php echo $Text['ostat_desc_changes'];?>").addClass('withChanges').html('<span class="tdIconCenter ui-icon ui-icon-check"></span>');
							break;
						case "6":
							td.attr("title","Items of this order have been validated").addClass('').html('<span class="tdIconCenter ui-icon ui-icon-cart"></span>');
							break;
						case "-1":
							td.attr("title","<?php echo $Text['ostat_desc_incomp']; ?>").addClass('dim40').html('<p class="textAlignCenter">-</p>');
							break;
					}
				}
				

				/**
				 *	prepares the printing queue of the selected orders. 
				 */
				function printQueue(){
					gPrintIndex = -1; 
					gPrintList = [];
					gSelRow = null;  

					if ($('input:checkbox[name="bulkAction"][checked="checked"]').length  == 0){
						$.showMsg({
							msg:"<?=$Text['msg_err_noselect'];?>",
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
							$.showMsg({
								msg:txt,
								type: 'success'});
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
		    				$('#tbl_orderOverview tbody tr').removeClass('ui-state-highlight');
							gSelRow.addClass('ui-state-highlight');
		    				gSelRow = null;	
		    				//$('#tbl_orderOverview tbody').xml2html('reload');	
							break;

						case 'review':
							$('.overviewElements, .viewElements').hide();
							
							var title = "(#"+gSelRow.attr('orderId')+"), <span class='aix-style-provider-name'>" +gSelRow.children().eq(2).text() + "</span>, "  + $.getCustomDate(gSelRow.attr('dateForOrder'), 'D d M, yy');
							var sindex = gSelRow.children().eq(8).attr('revisionStatus');
							
							$('.providerName').html(title);							
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
							var title = gSelRow.children().eq(2).text();

							//$('#viewOrderRevisionStatus') set the order status here. 
							$('.providerName').html(title);							
							$('.overviewElements').hide();
							$('.viewElements').fadeIn(1000);
							$('#tbl_reviseOrder tbody').xml2html("reload", {						//load order details for revision
								params : 'oper=getOrderedProductsList&order_id='+gSelRow.attr("orderId")+'&provider_id='+gSelRow.attr("providerId")+'&date='+gSelRow.attr("dateForOrder")
							})
							
							$('#tbl_orderDetailInfo tbody').xml2html('reload',{						//load the info of this order
								params : 'oper=orderDetailInfo&order_id='+gSelRow.attr("orderId")+'&provider_id='+gSelRow.attr("providerId")+'&date='+gSelRow.attr("dateForOrder"),
								complete : function(rowCount){
									$('#orderDetailDateForOrder').text($.getCustomDate(gSelRow.attr('dateForOrder')));
									$('#orderDetailShopDate').text($.getCustomDate($('#orderDetailShopDate').text()));
									//copy the order status 
									var tdStatus = gSelRow.children().eq(8).clone();
									$('#orderDetailRevisionStatus').before(tdStatus).remove();
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
		<?php include "php/inc/menu.inc.php" ?>
	</div>
	<!-- end of headwrap -->
	
	<div id="stagewrap">
	
		<div id="titlewrap" class="ui-widget">
			<div id="titleLeftCol50">
				<button id="btn_overview" class="floatLeft reviewElements viewElements"><?php echo $Text['overview'];?></button>
				<h1 class="reviewElements"><?=$Text['ti_revise'];?> <span class="providerName"></span></h1>
				<h1 class="viewElements"><?=$Text['ti_order_detail'];?> <span class="providerName aix-style-provider-name"></span></h1>
		    	<h1 class="overviewElements"><?=$Text['ti_mng_orders'];?></h1>
		    </div>
		   	<div id="titleRightCol50">
		   		<!-- button id="btn_setReview" class="viewElements btn_right"><?=$Text['btn_revise']; ?></button-->
		   		
		   		<button id="btn_setShopDate" class="reviewElements btn_right" title="<?=$Text['distribute_desc'];?>"><?=$Text['btn_distribute'];?></button>
				<button	id="tblViewOptions" class="overviewElements btn_right"><?=$Text['filter_orders']; ?></button>
				
				<div id="tblOptionsItems" class="hidden">
					<ul>
						<li><a href="javascript:void(null)" id="ordersForToday"><?=$Text['filter_expected'] ?></a></li>
						<li><a href="javascript:void(null)" id="nextWeek"><?=$Text['filter_next_week'] ;?></a></li>
						<li><a href="javascript:void(null)" id="futureOrders"><?=$Text['filter_future'];?></a></li>
						<li><a href="javascript:void(null)" id="pastMonth"><?=$Text['filter_month'] ; ?></a></li>
						<li><a href="javascript:void(null)" id="pastYear"><?=$Text['filter_year'];?></a></li>
						<li><a href="javascript:void(null)" id="limboOrders"><?=$Text['filter_postponed'];?></a></li>
						<li><a href="javascript:void(null)" id="preOrders"><?=$Text['nav_report_preorder'];?></a></li>
					</ul>
				</div>	
				<button id="btn_print" class="overviewElements btn_right"><?=$Text['printout'];?></button>
		   		<button id="btn_zip" class="overviewElements btn_right">Zip</button>			
		   	</div> 	
		</div> <!--  end of title wrap -->
		<div class="ui-widget overviewElements" id="withSelected">
			<!-- p  class="textAlignLeft">
				<select id="bulkActionsTop">
					<option value="-1"><?=$Text['with_sel'];?></option>
					<option value="print"><?=$Text['printout'];?></option>
					<option value="download"><?=$Text['dwn_zip'];?></option>
				</select>
			</p-->
		</div>
		<div id="orderOverview" class="ui-widget overviewElements">
			<div class="ui-widget-header ui-corner-all">
				<p style="height:30px;"><span style="float:right; margin-top:0px; margin-right:5px;"><img class="loadSpinner" src="img/ajax-loader.gif"/></span></p>
			</div>
			<div class="ui-widget-content">
			<table id="tbl_orderOverview" class="tblListingDefault">
				<thead>
					<tr>
						<th>&nbsp;<input type="checkbox" id="toggleBulkActions" name="toggleBulk"/></th>
						<th class="clickable"><?=$Text['id'];?><span class="ui-icon ui-icon-triangle-2-n-s floatRight"></span></th>
						<th class="clickable textAlignLeft"><?=$Text['provider_name'];?> <span class="ui-icon ui-icon-triangle-2-n-s floatRight"></span></th>
						<th class="clickable"><?=$Text['ordered_for'];?> <span class="ui-icon ui-icon-triangle-2-n-s floatRight"></span></th>
						<th><?=$Text['closes_days'];?></th>
						<th><?=$Text['sent_off'];?></th>
						<th class="clickable"><?=$Text['date_for_shop'];?> <span class="ui-icon ui-icon-triangle-2-n-s floatRight"></span></th>
						<th class="clickable"><?=$Text['order_total'];?>  <span class="ui-icon ui-icon-triangle-2-n-s floatRight"></span></th>
						<th><?=$Text['status'];?></th>
						<th><?=$Text['actions'];?></th>
					</tr>
				</thead>
				<tbody>
					<tr id="{id}" orderId="{id}" dateForOrder="{date_for_order}" providerId="{provider_id}" class="clickable">
						<td><input type="checkbox" name="bulkAction"/></td>
						<td>{id}</td>
						<td class="textAlignRight minPadding"><p class="textAlignLeft">{provider_name}</p></td>
						<td>{date_for_order}</td>
						<td>{time_left}</td>
						<td>{ts_sent_off}</td>
						<td>{date_for_shop}</td>
						<td><p  class="textAlignRight">{order_total} €&nbsp;&nbsp;</p></td>
						<td>{revision_status}</td>
						<td class="aix-layout-fixW100s">				
							<a href="javascript:void(null)" class="reviseOrderBtn nobr"><?php echo $Text['btn_revise']; ?></a>
						</td>
					</tr>
				</tbody>
				<tfoot>
					<tr>
						<td><span class="ui-icon ui-icon-arrowreturnthick-1-e"></span></td>
						<td colspan="6">
							<p  class="textAlignLeft">
							<select id="bulkActionsBottom">
								<option value="-1"><?=$Text['with_sel'];?></option>
								<option value="print"><?=$Text['printout'];?></option>
								<option value="download"><?=$Text['dwn_zip'];?></option>
							</select>
							</p>
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
				
				<table id="tbl_orderDetailInfo" class="tblListingBorder2">
					<thead>	
						<tr>
							<th colspan="2"><p><?=$Text['provider_name'];?></p></th>
							<th colspan="2"><p><?=$Text['order'];?></p></th>
							<th colspan="2" class="aix-layout-fixW250"><p><?=$Text['responsible_uf'];?></p></th>
						</tr>
					</thead>
					<tbody>
					<tr>
						<td colspan="2"><p class="aix-style-provider-name">{name}</p></td>
						<td></td>
						<td></td>
						<td colspan="2" rowspan="2"><p>{uf_id} {uf_name}</p></td>
					</tr>
					<tr>
						<td class="aix-layout-fixW150"><p><?php echo $Text['nif']; ?></p></td>
						<td>{nif}</td>
						<td class="aix-layout-fixW150"><p><?php echo $Text['order']." ".$Text['id'];?></p></td>
						<td><p class="boldStuff">{order_id}</p></td>
					</tr>
					<tr>
						<td>
							<p><?=$Text['contact'];?></p>
						</td>
						<td>
							{contact}<br/>
							{address}<br/>
							{zip} {city}
						</td>
						<td>
							<p><?php echo $Text['ordered_for']; ?></p>
						</td>
						<td>
							<p id="orderDetailDateForOrder" class="boldStuff">{date_for_order}</p>
						</td>
						<th colspan="2" class="aix-layout-fixW250">
							<p><?php echo $Text['total'];?></p>
						</th>
					</tr>
					<tr>
						<td>
							<p><?php echo $Text['email'];?></p>
						</td>
						<td>
							{email}
						</td>
						<td>
							<p><?php echo $Text['ostat_finalized']; ?></p>
						</td>
						<td>
							{ts_sent_off}
						</td>
						<td>
							<p><?php echo $Text['total_orginal_order']; ?></p>
						</td>
						<td>
							<p class="textAlignRight  boldStuff">{total} €</p>
						</td>
					</tr>
					<tr>
						<td>
							<p><?php echo $Text['phone1']; ?></p>
						</td>
						<td>
							 {phone1} / {phone2}
						</td>
						<td>
							<p><?php echo $Text['date_for_shop']; ?></p>
						</td>
						<td>
							<p id="orderDetailShopDate">{date_for_shop}</p>
						</td>
						<td>
							<p><?php echo $Text['total_after_revision']; ?></p>
						</td>
						<td>
							<p class="textAlignRight  boldStuff">{delivered_total} €</p>
						</td>
					</tr>
					<tr>
						<td>
							<p><?php echo $Text['bank_name']; ?></p>
						</td>
						<td>
							{bank_name}
						</td>
						<td>
							<p><?php echo $Text['status']; ?></p>
						</td>
						<td id="orderDetailRevisionStatus">
							{revision_status}
						</td>
						<td>
							<p><?php echo $Text['validated'];?></p>
						</td>
						<td>
							<p class="textAlignRight boldStuff">{validated_income} €</p>
						</td>
					</tr>
					<tr>
						<td>
							<p><?php echo $Text['bank_account'];?></p>
						</td>
						<td>
							{bank_account}
						</td>
						<td>
							<p class="floatLeft"><?php echo $Text['notes']; ?></p>
						</td>
						<td>
							<textarea class="ui-widget-content ui-corner-all editOrderDetail textareaMax" id="orderDetailNotes" name="order_notes">{order_notes}</textarea>
						</td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td>
							<p><?php echo $Text['delivery_ref']; ?></p>
						</td>
						<td>
							<input type="text" class="editOrderDetail ui-widget-content ui-corner-all" id="orderDetailDeliveryRef" name="delivery_ref" value="{delivery_ref}" />
						</td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td>
							<p><?php echo $Text['payment_ref']; ?></p>
						</td>
						<td>
							<input type="text" class="editOrderDetail ui-widget-content ui-corner-all" id="orderDetailPaymentRef" name="payment_ref" value="{payment_ref}" />
						</td>
						<td></td>
						<td></td>
					</tr>
					</tbody>
				</table>
				
				
				
				
			</div>
		</div>

		<div id="reviseOrder" class="ui-widget reviewElements viewElements">
			<div class="ui-widget-header ui-corner-all textAlignCenter reviewElements">
				<h3 id="orderInfoDate"></h3>
			</div>
			<div class="ui-widget-content">
				<table id="tbl_reviseOrder" class="tblReviseOrder">
					<thead>
						<tr>
							<th><?=$Text['id'];?></th>
							<th><?=$Text['product_name'];?></th>
							<th><?=$Text['unit'];?></th>
							<th class="arrivedCol"><?=$Text['arrived']; ?></th>
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
	<p><?php echo $Text['msg_cur_status'];?>: <span id="currentOrderStatus" class="ui-state-highlight ui-corner-all aix-style-padding3x3"></span>.</p>
	<p><?php echo $Text['msg_change_status']; ?>: </p>
	<p>&nbsp;</p>
	<table>
		<tr><td class="textAlignCenter"><button id="btn_revised"><?php echo $Text['set_ostat_arrived'];?>!</button></td><td>&nbsp;</td><td><?php echo $Text['set_ostat_desc_arrived']; ?></td></tr>					
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr><td class="textAlignCenter"><button id="btn_postponed"><?php echo $Text['set_ostat_postpone']; ?></button></td><td>&nbsp;</td><td><?php echo $Text['set_ostat_desc_postpone'] ; ?></td></tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr><td class="textAlignCenter"><button id="btn_canceled"><?php echo $Text['set_ostat_cancel']; ?></button></td><td>&nbsp;</td><td><?php echo $Text['set_ostat_desc_cancel'] ; ?></td></tr>
	</table>
</div>	


<div id="dialog_convertPreorder" title="Convert preorder to order">
	<p>&nbsp;</p>
	<p class="success_msg aix-style-ok-green ui-corner-all aix-style-padding8x8"></p>
	<p>Conver this preorder to a regular order. This will assign an order date, i.e. when the expected items will arrive.</p>
	<p>&nbsp;</p>
	<div id="datepicker2"></div>
</div>

<div id="dialog_setShopDate" title="Set shopping date">
	<p>&nbsp;</p>
	<p class="success_msg aix-style-ok-green ui-corner-all aix-style-padding8x8"><?php echo $Text['msg_move_to_shop']; ?></p>
	<p><?php echo $Text['msg_confirm_move']; ?></p>
	<br/>
	<p class="textAlignCenter boldStuff" id="indicateShopDate"></p> 
	<br/>
	<p><a href="javascript:void(null)" id="showDatePicker"><?php echo $Text['alter_date']; ?></a> </p>
	<br/>
	<div id="datepicker"></div>
</div>
<iframe name="dataFrame" style="display:none;"></iframe>
<form id="submitZipForm" class="hidden"></form>

<!-- / END -->
</body>
</html>













