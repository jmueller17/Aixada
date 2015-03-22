<?php include "php/inc/header.inc.php"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<base href="<?php echo $cv->basedir; ?>" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] ." -  " .$Text['head_ti_manage_orders']; ?></title>

    <link href="js/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/aixcss.css" rel="stylesheet">
    <link href="js/ladda/ladda-themeless.min.css" rel="stylesheet">
  	<link href="js/datepicker/bootstrap-datetimepicker.min.css" rel="stylesheet">


    
    <?php if (isset($_SESSION['dev']) && $_SESSION['dev'] == true ) { ?> 
	    <script type="text/javascript" src="js/jquery/jquery.js"></script>
	    <script type="text/javascript" src="js/bootstrap/js/bootstrap.min.js"></script>
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaXML2HTML.js" ></script>
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaSwitchSection.js" ></script>
	   	
	   	<script type="text/javascript" src="js/bootbox/bootbox.js"></script>
	   	<script type="text/javascript" src="js/ladda/spin.min.js"></script>
	   	<script type="text/javascript" src="js/ladda/ladda.min.js"></script>
	   	<script type="text/javascript" src="js/datepicker/moment-with-langs.min.js"></script>	   	
		<script type="text/javascript" src="js/datepicker/bootstrap-datetimepicker.min.js"></script>
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaUtilities.js" ></script>

   	<?php  } else { ?>
	   	<script type="text/javascript" src="js/js_for_manage_orders.min.js"></script>
    <?php }?>
     
	<script type="text/javascript">
        // Texts of the literals in the language of the user. For use in js.
        var local_lang = {
            total:           "<?php echo $Text['total']; ?>",
            ostat_revised:   "<?php echo $Text['ostat_revised']; ?>",
            uf_short:        "<?php echo $Text['uf_short']; ?>",
            click_to_edit:   "<?php echo $Text['click_to_edit']; ?>",
            ostat_desc_sent:   "<?php echo $Text['ostat_desc_sent']; ?>",
            ostat_desc_nochanges:   "<?php echo $Text['ostat_desc_nochanges']; ?>",
            ostat_desc_postponed:   "<?php echo $Text['ostat_desc_postponed']; ?>",
            ostat_desc_cancel:   "<?php echo $Text['ostat_desc_cancel']; ?>",
            ostat_desc_changes:   "<?php echo $Text['ostat_desc_changes']; ?>",
            _ostat_desc_validated:   "<?php echo $Text['or_ostat_desc_validated']; ?>",
            ostat_desc_incomp:   "<?php echo $Text['ostat_desc_incomp']; ?>", 
            _suma:           "<?php echo $Text['or_suma']; ?>",
            _gross_price:    "<?php echo $Text['or_gross_price']; ?>",
            _gross_total:    "<?php echo $Text['or_gross_total']; ?>",
            _net_price:      "<?php echo $Text['or_net_price']; ?>",
            _net_total:      "<?php echo $Text['or_net_total']; ?>",
            _saving:         "<?php echo $Text['or_saving']; ?>",
            _click_to_edit_total: "<?php echo $Text['or_click_to_edit_total']; ?>",
            _click_to_edit_gprice:"<?php echo $Text['or_click_to_edit_gprice']; ?>"
        };

        // Configuration values used by js code.        
        var local_cfg = {
            print_order_template: "<?php echo get_config(
                        'print_order_template', 'report_order1.php'); ?>",
            order_review_uf_sequence: "<?php echo get_config(
                        'order_review_uf_sequence', 'desc'); ?>"
        };
    </script>
	   
	<script type="text/javascript">

	//list of order to be bulk-printed
	var gPrintList = [];

	
	$(function(){

			
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
		//var gFilter = (typeof $.getUrlVar('filter') == "string")? $.getUrlVar('filter'):'pastMonths2Future';

		//global today date
		var gToday = '';


		//hide all sections
		$('.section').hide();

		//init change section lib
		$('.change-sec')
			.switchSection("init",{
				afterSectionSwitch : function(section){
					
					if (section == ".sec-2"){


						var title = gSelRow.children().eq(2).text();
						$('.set-provider').html(title);							
						

						$('#tbl_reviseOrder tbody').xml2html("reload", {						//load order details for revision
							params : 'oper=getOrderedProductsListPrices&order_id='+gSelRow.attr("orderId")+'&provider_id='+gSelRow.attr("providerId")+'&date='+gSelRow.attr("dateForOrder")
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
		 			}


				}

			});

		//show section 1
		$('.sec-1').show();


		bootbox.setDefaults({
			locale:"<?=$language;?>"
		})


		//order overview date filter 
		$('#datepicker-from').datetimepicker({
				pickTime:false,
				startDate : '1/1/2014',
			}).on("change.dp",function(e){
				reloadOrders();
			})

		$('#datepicker-to').datetimepicker({
				pickTime:false,
				startDate : '1/1/2014',
			}).on("change.dp",function(e){
				reloadOrders();
			})


		//set the shopping date when distributing orders 
		$('#datepicker-distribute').datetimepicker({
				pickTime:false,
				startDate : '1/1/2014',
			}).on("change.dp",function(e){
				$('.set-shopdate').text($.getCustomDate(date[0]));	
			})


		//set delivery date when closing preorder
		$('#datepicker-preorder').datetimepicker({
				pickTime:false,
				startDate : '1/1/2014',
			}).on("change.dp",function(e){
				
			})


		$.getAixadaDates('getToday', function (date){
			gToday = date[0];
			gPrevMonth = moment(gToday, "YYYY-MM-DD").subtract(6, 'months').format('YYYY-MM-DD');
			
	 		$('#datepicker-to').data("DateTimePicker").setDate(gToday);
			$('#datepicker-from').data("DateTimePicker").setDate(gPrevMonth);
			$('#datepicker-distribute').data("DateTimePicker").setDate(gToday);
			$('.set-shopdate').text($.getCustomDate(date[0]));		

			reloadOrders();
		});
	


		/*	
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
		

			
			
			$.getAixadaDates('getToday', function (date){
				gToday = $.datepicker.parseDate('yy-mm-dd', date[0]);
				$("#datepicker").datepicker('setDate', gToday);
				$("#datepicker").datepicker("refresh");		
				$("#datepicker2").datepicker("refresh");		
				$('#indicateShopDate').text($.getCustomDate(date[0]));		
			});	
			
	*/





			//STEP 1: retrieve all active ufs in order to construct the table header
			$.ajax({
					type: "POST",
					url: 'php/ctrl/UserAndUf.php?oper=getUfListing&all=0&order='+
                        local_cfg.order_review_uf_sequence,
					dataType:"xml",
					success: function(xml){
						var theadStr = '<th>'+local_lang.total+'</th>'; 
						var theadStr2 = '';
						$(xml).find('row').each(function(){
							var id = $(this).find('id').text();
							var colClass = 'Col-'+id;
							header.push(id);
							theadStr += '<th class="'+colClass+' hidden col">'+id+'</th>';
							theadStr2 += '<td class="'+colClass+' hidden col"></td>';
						});

						theadStr += '<th>'+local_lang.total+'</th>';
						theadStr2 += '<td>&nbsp;</td>';
						theadStr += 
							'<th class="grossLabel">'+local_lang._gross_price+'</th>'+
							'<th class="grossLabel">'+local_lang._gross_total+'</th>'+
							'<th class="netLabel">'+local_lang._net_price+'</th>'+
							'<th class="netLabel">'+local_lang._net_total+'</th>';
						theadStr2 += 
							'<td class="grossLabel orderTotalsDesc">'+local_lang._suma+':</td>'+
							'<td class="grossTotalOrder textAlignRight"></td>'+
							'<td class="netLabel orderTotalsDesc">'+local_lang._suma+':</td>'+
							'<td class="netTotalOrder textAlignRight"></td>';
						theadStr += '<th class="revisedCol">'+local_lang.ostat_revised+'</th>';
						theadStr2 += '<td>&nbsp;</td>';
						
						$('#tbl_reviseOrder thead tr').first().append(theadStr);
						$('#tbl_reviseOrder thead tr').last().append(theadStr2);
						$('#tbl_reviseOrder tfoot tr').last().append(theadStr2);

						tblHeaderComplete = true; 

					},
					error : function(XMLHttpRequest, textStatus, errorThrown){
						bootbox.alert("An error has occured: " + XMLHttpRequest.responseText);	
					}
			}); //end ajax	

            // Functions used for refreshing the provider delivery note totals.
            function refreshRowPrices(productId) {
                var productElement = $('#product_'+productId);
                if (productElement) {
                    var grossPrice = parseFloat(productElement.attr('gross_price')),
                        ivaCoef = 1 + 
                            parseFloat(productElement.attr('iva_percent')) / 100,                        
                        netPrice = parseFloat(productElement.attr('net_price')),
                        revTaxCoef = 1 +
                            parseFloat(productElement.attr('rev_tax_percent')) / 100,
                        ufPrice = parseFloat(productElement.attr('uf_price')),
                        grossTotal = 0,
                        netTotal = 0;
                    if ($('#ckboxArrived_'+productId).is(':checked')) {
                        var quTotal = parseFloat(
                            $('.total_'+productId+' span:first-child')
                                                               .first().text());
                        netTotal = Math.round(100 * quTotal * 
                                                    ufPrice / revTaxCoef) / 100;
                        grossTotal = Math.round(100 * quTotal * 
                                          ufPrice / revTaxCoef / ivaCoef) / 100;
                        /* individual for any UF
                        $('.Row-'+productId).each(function(){
                            var text = $(this).text();
                            if (text) {
                                var qua = parseFloat(text);                                
                                netTotal += Math.round(100 * qua * 
                                                ufPrice / revTaxCoef) / 100;
                                grossTotal += Math.round(100 * qua * 
                                      ufPrice / revTaxCoef / ivaCoef) / 100;
                            }
                        });
                        */
                    }
                    $('#grossPrice_'+productId).html('<span>'+grossPrice.toFixed(2)+'</span>');
                    $('#grossRow_'+productId).html('<span>'+grossTotal.toFixed(2)+'</span>');
                    $('#netPrice_'+productId).html('<span>'+netPrice.toFixed(2)+'</span>');
                    $('#netRow_'+productId).html('<span>'+netTotal.toFixed(2)+'</span>');
                }
            }
            function refreshTotalOrder() {
                var total = 0;
                $(".grossRow").each(function() {
                    var text = $(this).text();
                    if (text) {
                        total += parseFloat(text);
                    }
                });
                $('.grossTotalOrder').text(total.toFixed(2));
                total = 0;
                $(".netRow").each(function() {
                    var text = $(this).text();
                    if (text) {
                        total += parseFloat(text);
                    }
                });
                $('.netTotalOrder').text(total.toFixed(2));
            }
            function hideNetTotalOrder() {
                var hasIva = false;
                $(".productIdClass").each(function() {
                    if ($(this).attr('iva_percent') != 0) {
                        hasIva = true;
                        return false;
                    }
                });
                if (hasIva === false) {
                    $('.netLabel').hide();
                    $('.netTotalOrder').hide();
                    $('.netPrice').hide();
                    $('.netRow').hide();
                }
            }

			//STEP 2: construct table structure: products and col-cells. 
			$('#tbl_reviseOrder tbody').xml2html('init',{
				url : 'php/ctrl/Orders.php',
				loadOnInit : false, 
				rowComplete : function (rowIndex, row){
					
					var product_id = $(row).children(':first').text();

					var tbodyStr = '<td class="nobr totalQu total_'+product_id+'" row_tot="'+product_id+'"></td>';
					
					for (var i=0; i<header.length; i++){
						var uf_id = header[i],
							colClass = 'Col-'+uf_id,
							rowClass = 'Row-'+product_id;
						tbodyStr += '<td class="'+colClass+' '+rowClass+' hidden interactiveCell toRevise textAlignCenter" col="'+uf_id+'" row="'+product_id+'"></td>';
					}

					//product total quantities
					tbodyStr += '<td class="nobr totalQu total_'+product_id+'" row_tot="'+product_id+'"></td>';
                    if (gSection !== 'print') {
                        tbodyStr += '<td id="grossPrice_'+product_id+'" class="nobr textAlignRight grossPrice"></td>';
                        tbodyStr += '<td id="grossRow_'+product_id+'"   class="nobr textAlignRight grossRow"></td>';
                        tbodyStr += '<td id="netPrice_'+product_id+'" class="nobr textAlignRight netPrice"></td>';
                        tbodyStr += '<td id="netRow_'+product_id+'"     class="nobr textAlignRight netRow"></td>';
                    }
					
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
								quShopHTML = (gSection == 'view')? ' <span class="shopQuantity">(' +quShop +')</span> ':'';
							}
							var product_id = $(this).find('product_id').text();
							var uf_id = $(this).find('uf_id').text();
							var qu = $(this).find('quantity').text();
							var revised = $(this).find('revised').text();
							var arrived = $(this).find('arrived').text();
							var tblCol = '.Col-'+uf_id;
							var tblRow = '.Row-'+product_id;
							var pid	= product_id + '_' + uf_id; 
							
							$(tblCol+tblRow).html(qu+''+quShopHTML);
							
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
								
								var total = "<span>"+quTotal.toFixed(2)+"</span> <span class='shopQuantity'>("+quShopTotal.toFixed(2)+")</span>";
								
								$('.total_'+lastId).html(total);
								if (gSection !== 'print') {
									refreshRowPrices(lastId);
								}
								quTotal = 0; 
								quShopTotal = 0; 
							}
							
							quTotal += new Number(qu); 
							quShopTotal += new Number(quShop);
							lastId = product_id; 

						});

						
						var total = "<span>"+quTotal.toFixed(2)+"</span> <span class='shopQuantity'>("+quShopTotal.toFixed(2)+")</span>";
						$('.total_'+lastId).html(total);

                        if (gSection !== 'print') {
                            refreshRowPrices(lastId);
                            refreshTotalOrder();
                            $('.orderTotals').show();
                            $('.grossLabel').show();
                            hideNetTotalOrder()
                        } else {
                            $('.orderTotals').hide();
                            $('.grossLabel').hide();
                            $('.netLabel').hide();
                        }

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
						bootbox.alert("An error has occured: " + XMLHttpRequest.responseText);
					}
			}); //end ajax	
						
				}
			});

			
			
			/**
			 *	copies order_items after revision into aixada_shop_item only if not already
			 *	validated items exist;  
			 */
			$("#btn-distribute")
       			.click(function(e){
           			var allRevised = true;
					$('input:checkbox[name="revised"]').each(function(){
						if (!$(this).is(':checked')){
							allRevised = false; 
							return false; 
						}

					});

					if (allRevised){
						//$('#dialog_setShopDate').dialog("open");
					} else {

						/*$.showMsg({
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
							type: 'confirm'});*/

					}
       			}).hide();

			
			/*$('#dialog_setShopDate').dialog({
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
								bootbox.alert("An error has occured: " + XMLHttpRequest.responseText);
								
							}
						});
	
						
						},
				
					"<?=$Text['btn_close'];?>"	: function(){
						$( this ).dialog( "close" );
						} 
				}
			});*/
				

			/**
			 *	when closing a preorder, a delivery date needs to be set. 
			 */
			/*
			$('#dialog_convertPreorder').dialog({
				autoOpen:false,
				width:420,
				height:500,
				buttons: {  
					"<?=$Text['btn_save'];?>" : function(){
						var $this = $(this);

						if ($.getSelectedDate('#datepicker2') == $.datepicker.formatDate('yy-mm-dd', gToday)){
							bootbox.alert({
								title : "Warning",
								message : "<div class='alert alert-warning'>Please select an order date starting from at least tomorrow onwards.</div>"
							});	

							return false; 
						}
						
						$.ajax({
							type: "POST",
							url: 'php/ctrl/Orders.php?oper=preorderToOrder&provider_id='+gSelRow.attr('providerId')+'&date_for_order='+$.getSelectedDate('#datepicker2'),
							success: function(txt){

								
							},
							complete : function(){
								$this.button('disable');
								setTimeout(function(){
									$this.dialog( "close" );
									$('#tbl_orderOverview tbody').xml2html('reload',{
										params : 'oper=getOrdersListing&filter=pastMonths2Future',
									});
								},500);

							}
						});
	
						
						},
				
					"<?=$Text['btn_close'];?>"	: function(){
						$( this ).dialog( "close" );
						} 
				}
			});*/


			
			//export order options dialog
			/*
			$('#dialog_export_options').dialog({
				autoOpen:false,
				width:580,
				height:550,
				buttons: {  
					"<?=$Text['btn_ok'];?>" : function(){
							exportOrder();
						},
				
					"<?=$Text['btn_close'];?>"	: function(){
						$( this ).dialog( "close" );
						} 
				}
			});*/
			


			//adjust total quantities
			$('td.totalQu')
				.on('mouseover', function(e){
					
					if (!$(this).hasClass('editable') && gSection == 'review'){
						var pid = $(this).attr('row_tot');
						$(this).children(':first')
							.addClass('editable')
							.editable('php/ctrl/Orders.php', {			//init the jeditable plugin
									submitdata : {
										oper: 'editTotalQuantity',
										order_id : gSelRow.attr('orderId'),
										product_id : pid 
										},
									name 	: 'quantity',
									indicator: local_lang._saving,
								    tooltip:   local_lang._click_to_edit_total,
									callback: function(xml, settings){
									    var pid = settings.submitdata.product_id;
									    var total_quantity = 0;
									    $(xml).find('row').each(function(){
										    var uf_id = $(this).find('uf_id').text();
										    var quantity = $(this).find('quantity').text();
										    total_quantity = total_quantity + parseFloat(quantity);
										    var selector = '.Col-' + uf_id + '.Row-' + pid;
										    
										    $(selector)
										    	.removeClass('toRevise')
										    	.addClass('revised')
										    	.text(quantity);
										    
										});
										$('#ckboxRevised_'+pid).attr('checked','checked');
										
										$('.total_' + pid).each(function(){
											$(this).children(':first').empty().text(total_quantity.toFixed(2));
										});
										refreshRowPrices(pid);
										refreshTotalOrder();
									}//end callback 
							});
					}
			});
            //adjust gross price
            $('td.grossPrice')
            .on('mouseover', function(e) {
                if (!$(this).hasClass('editable') && gSection == 'review') {
                    var id = $(this).attr('id'),
                        product_id = id.split('_')[1];
                    $(this).children(':first')
                    .addClass('editable')
                    .editable('php/ctrl/Orders.php', { //init the jeditable plugin
                        submitdata : {
                            oper:       'editGrossPrice',
                            order_id:   gSelRow.attr('orderId'),
                            product_id: product_id 
                        },
                        name:       'gross_price',
                        indicator:  local_lang._saving,
                        tooltip:    local_lang._click_to_edit_gprice,
                        callback: function(response, settings){
                            var product_id = settings.submitdata.product_id,
                                prices = response.split(';');
                            var productElement = $('#product_'+product_id);
                            if (prices[0]="OK") {
                                productElement.attr('gross_price', prices[1]);
                                productElement.attr('net_price', prices[2]);
                                productElement.attr('uf_price', prices[3]);
                                refreshRowPrices(product_id);
                                refreshTotalOrder();
                            }
                        }//end callback 
                    });//end editable of jeditable
                }
            });
		
			//interactivity for editing cells
			$('td.interactiveCell')
				.on('mouseover', function(e){						//make each cell editable on mouseover. 
					var col = $(this).attr('col');
					var row = $(this).attr('row');
					var product = $(this).parent().children().eq(1).text();
					if (!$(this).hasClass('editable') && gSection == 'review'){
						$(this).addClass('editable')
							.editable('php/ctrl/Orders.php', {			//init the jeditable plugin
									submitdata : {
										oper: 'editQuantity',
										order_id : gSelRow.attr('orderId'),
										product_id: row,
										uf_id: col
									},
									name 	: 'quantity',
									indicator: local_lang._saving,
									placeholder:'',
								    tooltip	:
                                        local_lang.uf_short + ' ' + col + '\n' +
                                        product + '\n' +
                                        local_lang.click_to_edit,
									callback: function(value, settings){
										$(this).parent().removeClass('toRevise').addClass('revised');
										
										recalcRowTotal(row);
									} 
						});

					}

			})
			.on('mouseout', function(e){
				//var col = $(this).attr('col');
				//var row = $(this).attr('row');

				//$('.Row-'+row).removeClass('editHighlightRow');
				//$('.Col-'+col).removeClass('editHighlightCol');

			});
				
				
			/**
			 *	uncheck an entire product row (product did not arrive). 
			 */
			$('input:checkbox[name="hasArrived"]')
			.on('click', function(e){
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
						refreshRowPrices(product_id);
						refreshTotalOrder();
					},
					error : function(XMLHttpRequest, textStatus, errorThrown){
						bootbox.alert("An error has occured: " + XMLHttpRequest.responseText);
					}
				});
			});

			
			/**
			 *	mark an entire product row as revised. the status is saved in 
			 *  the order_to_shop table.  
			 */
			$('input:checkbox[name="revised"]').on('click', function(e){
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
						bootbox.alert("An error has occured: " + XMLHttpRequest.responseText);
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


			//edit/save the notes, payment_ref and deonry_ref pages
			$('.editOrderDetail')
				.on('focus', function(e){
					if (gSelRow.attr('orderId') > 0) {
					} else {
						/*$.showMsg({
							msg:"<?=$Text['msg_err_edit_order'];?>",
							type: 'warning'});*/
					}
				})
				.on('keyup', function(e){
					//hitting return saves it
					if (e.keyCode == 13 && gSelRow.attr('orderId') > 0){
						$.ajax({
							type: "POST",
							url: 'php/ctrl/Orders.php?oper=editOrderDetailInfo&order_id='+gSelRow.attr('orderId')+"&delivery_ref="+$('#orderDetailDeliveryRef').val()+"&payment_ref="+$('#orderDetailPaymentRef').val()+"&order_notes="+$('#orderDetailNotes').val(),
							beforeSend : function (xhr){
								$('.editOrderDetail').attr('disabled',true);
							},
							success: function(txt){
								/*$.showMsg({
									msg:"Saved successfully!",
									type: 'success'});*/
								
							},
							error : function(XMLHttpRequest, textStatus, errorThrown){
								bootbox.alert("An error has occured: " + XMLHttpRequest.responseText);
							},
							complete : function(){
								$('.editOrderDetail').attr('disabled',false);
							}
						});
					}

				})

				//revise button which switches from order detail view to revise screen
				$("#btn_setReview").click(function(e){
				    //TODO needs to trigger the revise buttons to check order status
			    	//$('.btn-revise-order').trigger('click');
				  });

				

			/***********************************************************
			 *		ORDER REVISION STATUS
			 **********************************************************/
			 $("#btn_revised")
       			.click(function(e){
					//$("#dialog_orderStatus").dialog("close");
       			});
			
			 $("#btn_canceled")
       			.click(function(e){
					setOrderStatus(4);
       			});
    			
			 $("#btn_postponed")
       			.click(function(e){
					setOrderStatus(3);
       			});

			 /*$('#dialog_orderStatus').dialog({
					autoOpen:false,
					width:450,
					height:420,
					modal:true
				});*/

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
						bootbox.alert("An error has occured: " + XMLHttpRequest.responseText);
					}
				});

			}


			/**
			 *	read export options and make the export call for the order. 
			 */
			function exportOrder(){
				var frmData = checkExportForm();
				if (frmData){					
					var urlStr = "php/ctrl/ImportExport.php?oper=exportOrder&order_id="+gSelRow.attr('orderId')+"&provider_id="+gSelRow.attr("providerId")+"&date_for_order="+gSelRow.attr("dateForOrder")+"&" + frmData; 
					//load the stuff through the export channel
					$('#exportChannel').attr('src',urlStr);
					setTimeout(function(){
						//$('#dialog_export_options').dialog("close");
					}, 2000);
				}	
			}


			function checkExportForm(){
				var frmData = $('#frm_export_options').serialize();
				if (!$.checkFormLength($('input[name=exportName]'),1,150)){
					bootbox.alert("File name cannot be empty!");
					return false;
				}
				return frmData; 
			}


			
			



			/***********************************************************
			 *		ORDER OVERVIEW FUNCTIONALITY
			 **********************************************************/
			
			$('#tbl_orderOverview tbody').xml2html('init',{
				url : 'php/ctrl/Orders.php',
				loadOnInit : false, 
				beforeLoad : function(){
					
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
						/*$.showMsg({
							msg:"<?=$Text['msg_err_order_filter'];?>",
							type: 'warning'});*/
					}
					$('#tbl_orderOverview tbody tr:even').addClass('rowHighlight');
									
				}
			});


			//reload orders 
			function reloadOrders(){

				var from_date = $('#datepicker-from').data("DateTimePicker").getDate();
				var to_date = $('#datepicker-to').data("DateTimePicker").getDate();
				from_date = moment(from_date).format("YYYY-MM-DD") + " 00:00:00";
				to_date = moment(to_date).format("YYYY-MM-DD") + " 23:59:59"; //we compare a time stamp! 


				$('#tbl_orderOverview tbody').xml2html('reload',{		
					url : 'php/ctrl/Orders.php',
					params : 'oper=getOrdersListing&from_date='+from_date+'&to_date='+to_date,
				});

			}


			/**
			 *	To finalize an order means no further modifications are possile. 
			 */
			$('.finalizeOrder')
				.on('click', function(e){
					gSelRow = $(this).parents('tr');
					var date = $(this).parents('tr').attr('dateForOrder');
					var providerId = $(this).parents('tr').attr('providerId');
					var timeLeft = $(this).parents('tr').children().eq(4).text();
					var msgt = "<?=$Text['msg_finalize'] ;?>";

					
					if (date == '1234-01-23'){ // is preorder, finalize means to assign also an order date
						//$("#dialog_convertPreorder").dialog("open");

						e.stopPropagation();
						return false; 
					}

					
					if (timeLeft > 0){
						msgt = "<?=$Text['msg_finalize_open'];?>"
					}

					bootbox.confirm({
						title : "<?=$Text['ti_confirm'];?>",
						message : "<div class='alert alert-warning'>"+msgt+"</div>",
						callback : function(ok){
							if (ok){
								finalizeOrder(providerId, date);
							} else {
								bootbox.hideAll();
							}	
						}
					});
					
					
					e.stopPropagation();
			});

			
			
			$('#tbl_orderOverview tbody')
				.on('click', 'tr', function(e){
					

					gSelRow = $(this); 

					//$('.col').hide();	
					
					$('.change-sec').switchSection("changeTo",".sec-2");

					
				});
			
			
			

			//revise order icon 
			$('.btn-revise-order')
				.on('click', function(e){
					$('#tbl_orderOverview tbody tr').removeClass('ui-state-highlight');
					gSelRow = $(this).parents('tr'); 
					gSelRow.addClass('ui-state-highlight');
					
					var shopDate 		= $(this).parents('tr').children().eq(6).text();
					var status = gSelRow.children().eq(8).attr('revisionStatus');
					
					$('.col').hide();
					
					//order comes from pre v2.5 database we miss some info
					if (status == "-1"){
						bootbox.alert({
							title : "Error",
							message : "<div class='alert alert-warning'><?=$Text['msg_err_miss_info'];?></div>"
						});	
						
						return false; 
					}
					
					//if table header ajax call has not finished, wait
					if (!tblHeaderComplete){
						bootbox.alert({
							title : "Error",
							message : "<div class='alert alert-warning'><?=$Text['msg_wait_tbl'];?></div>"
						});

						return false; 
					}

					//need the order id
					if (gSelRow.attr('orderId') <= 0){
						bootbox.alert({
							title : "Error",
							message : "<div class='alert alert-warning'><?=$Text['msg_err_invalid_id'];?></div>"
						});
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

								bootbox.confirm({
									title : "Warning",
									message : "<div class='alert alert-warning'><?=$Text['msg_revise_revised'];?></div>",
									callback : function(ok){
										if (ok){
											//reset this order to "finalized"
											$(this).html('<?=$Text['wait_reset'];?>');
											gSelRow.children().eq(8).attr('revisionStatus',1);
											var $this = $(this);
											resetOrder(gSelRow.attr('orderId'), function(){
												$this.html('Done!');
												setTimeout(function(){
													switchTo('review', {});
													//$this.dialog("close");
												}, 1000);
												
											});		
										} else {
											bootbox.hideAll();
										}	
									}
								});

								

							} else if (isValidated){
								bootbox.alert("<?=$Text['msg_err_already_val'];?>");

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
				$("#btn_print")
        		.click(function(e){
        			printQueue();
        		});


        		//download selected as zip
				$("#btn_zip")
	        		.click(function(e){
	        			if ($('input:checkbox[name="bulkAction"][checked="checked"]').length  == 0){
							/*$.showMsg({
								msg:"<?=$Text['msg_err_noselect'];?>",
								buttons: {
									"<?=$Text['btn_ok'];?>":function(){						
										//$(this).dialog("close");
									}
								},
								type: 'warning'});*/
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


				// export single order
				$('#btn_order_export')
					.click(function(e){
						$('#dialog_export_options')
							//.data("export", "order")
							//.dialog("open");
					 }); 
        		

				//view selected order (no editing)
				/*$('.viewOrderBtn')
					.on('click', function(e){
						gSelRow = $(this).parents('tr'); 
						$('.col').hide();	
						switchTo('view',{});
					});*/

				
				//print the selected order. If more than one is selected, confirm bulk print
				/*$('.printOrderBtn')
				.on('click', function(e){
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


				
			
				/*$("#tblViewOptions")
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
				*/


				//bulk actions
				$('input[name=bulkAction]')
					.on('click', function(e){
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
							bootbox.alert("An error has occured: " + XMLHttpRequest.responseText);
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
							td.attr("title",local_lang.ostat_desc_sent).html('<span class="tdIconCenter ui-icon ui-icon-mail-closed"></span>');
							break;
						case "2": 
							td.attr("title",local_lang.ostat_desc_nochanges).addClass('asOrdered').html('<span class="tdIconCenter ui-icon ui-icon-check"></span>');
							break;
						case "3": 
							td.attr("title",local_lang.ostat_desc_postponed).addClass('postponed').html('<span class="tdIconCenter ui-icon ui-icon-help"></span>');
							break;
						case "4": 
							td.attr("title",local_lang.ostat_desc_cancel).addClass('orderCanceled').html('<span class="tdIconCenter ui-icon ui-icon-cancel"></span>');
							break;
						case "5":
							td.attr("title",local_lang.ostat_desc_changes).addClass('withChanges').html('<span class="tdIconCenter ui-icon ui-icon-check"></span>');
							break;
						case "6":
							td.attr("title",local_lang._ostat_desc_validated).addClass('').html('<span class="tdIconCenter ui-icon ui-icon-cart"></span>');
							break;
						case "-1":
							td.attr("title",local_lang.ostat_desc_incomp).addClass('dim40').html('<p class="textAlignCenter">-</p>');
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
						/*$.showMsg({
							msg:"<?=$Text['msg_err_noselect'];?>",
							buttons: {
								"<?=$Text['btn_ok'];?>":function(){						
									$(this).dialog("close");
								}
							},
							type: 'warning'});*/

					} else {

						printWin = window.open('tpl/'+local_cfg.print_order_template);
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
						$('#tbl_reviseOrder tbody').xml2html("reload", {	//load order details for printing
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
							bootbox.alert({
								title : "Success",
								message : "<div class='success'>"+txt+"</div>"
							});
						
						},
						error : function(XMLHttpRequest, textStatus, errorThrown){
							bootbox.alert("An error has occured: " + XMLHttpRequest.responseText);
							
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
							bootbox.alert("An error has occured: " + XMLHttpRequest.responseText);
							
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
							//$('#dialog_orderStatus').dialog("open");
							$('#tbl_reviseOrder tbody').xml2html("reload", {						//load order details for revision
								params : 'oper=getOrderedProductsListPrices&order_id='+gSelRow.attr("orderId")+'&provider_id='+gSelRow.attr("providerId")+'&date='+gSelRow.attr("dateForOrder")
							})
							break;
							
						case 'view':

							
							
							
							break;
					}
					gSection = page; 
				}


				/**
				 *	recalculates the total of the revised quantities
				 */
				function recalcRowTotal(product_id){
					var totalQ = 0; 
					$('td.Row-'+product_id).filter(':not(:hidden)').each(function(){
						totalQ += new Number($(this).text());
					});
					if (totalQ.toString().length > 7) 	totalQ = totalQ.toFixed(3);
					$('.total_'+product_id+' span:first-child').text(totalQ);
					refreshRowPrices(product_id);
					refreshTotalOrder();
				}
			
			
	});  //close document ready


	
	
</script>
</head>
<body>

	<div id="headwrap">
		<?php include "php/inc/menu.inc.php" ?>
	</div>
	<!-- end of main menu / headwrap -->


	<!-- sub nav -->
	<div class="container section sec-1">
		<div class="row">
			<nav class="navbar navbar-default" role="navigation" id="ax-submenu">
			  	<div class="navbar-header">
			     	<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#sub-navbar-collapse">
				        <span class="sr-only">Toggle navigation</span>
				        <span class="icon-bar"></span>
				        <span class="icon-bar"></span>
				        <span class="icon-bar"></span>
			      	</button>
	    		</div>

	    		<div class="navbar-collapse collapse" id="sub-navbar-collapse">
		    	

		    		<div class="col-md-4">
						<button type="button" class="btn btn-success btn-sm navbar-btn section sec-2" id="btn-distribute">
		    				<span class="glyphicon glyphicon glyphicon-ok-sign"></span> <?=$Text['btn_distribute'];?>
		  				</button>
	  				</div>

	  				<div class="col-md-3 section sec-3 sec-1">
						<form class="navbar-form pull-right" role="date">
							<div class="form-group">
		                        <div class='input-group date input-group-sm' id='datepicker-from' >
		                            <input type='text' class="form-control" id="date-from" data-format="dddd, ll" placeholder="From" />
		                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
		                            </span>
		                        </div>
		                    </div>
		                </form>
		            </div>

		            <div class="col-md-3">
						<form class="navbar-form" role="date">
							<div class="form-group">
		                        <div class='input-group date input-group-sm' id='datepicker-to' >
		                            <input type='text' class="form-control" name="date-to" data-format="dddd, ll" placeholder="To" />
		                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
		                            </span>
		                        </div>
		                    </div>
		                </form>
	            	</div>

					

					<div class="btn-group col-md-1">
						<button type="button" class="btn btn-default btn-sm navbar-btn dropdown-toggle" data-toggle="dropdown">
		    				Actions <span class="caret"></span>
		  				</button>
						<ul class="dropdown-menu" role="menu">
							<li><a href="javascript:void(null)" class="ctx-nav ctx-nav-print"><span class="glyphicon glyphicon-export"></span> <?=$Text['printout'];?></a></li>
						    <li><a href="javascript:void(null)" class="ctx-nav ctx-nav-zip"><span class="glyphicon glyphicon-export"></span> Zip</a></li>
						</ul>
						
					</div>

	  				<div class="btn-group col-md-1 pull-right">
						<button type="button" class="btn btn-default btn-sm navbar-btn dropdown-toggle" data-toggle="dropdown">
							<span class="glyphicon glyphicon-filter"></span>&nbsp; <span class="caret"></span>
						</button>
						<ul class="dropdown-menu" role="menu">
							<li><a href="javascript:void(null)">Filter</a></li>
						    <li class="level-1-indent"><a href="javascript:void(null)" data="days,0" class="ctx-nav-filter"><?=$Text['filter_postponed'];?></a></li>
							<li class="level-1-indent"><a href="javascript:void(null)" data="weeks,1" class="ctx-nav-filter"><?=$Text['nav_report_preorder'];?></a></li>
						    <li class="level-1-indent"><a href="javascript:void(null)" data="days,0" class="ctx-nav-filter"><?=$Text['filter_expected'] ?></a></li>
							<li class="level-1-indent"><a href="javascript:void(null)" data="weeks,-1" class="ctx-nav-filter"><?=$Text['filter_next_week'] ;?></a></li>
							<li class="level-1-indent"><a href="javascript:void(null)" data="year,-1" class="ctx-nav-filter"><?=$Text['filter_future'];?></a></li>
							<li class="level-1-indent"><a href="javascript:void(null)" data="months,3" class="ctx-nav-filter"><?=$Text['filter_month'] ; ?></a></li>
							<li class="level-1-indent"><a href="javascript:void(null)" data="months,12" class="ctx-nav-filter"><?=$Text['filter_year'];?></a></li>

						</ul>
					</div>

		      	</div>
			</nav>
		</div>
	</div><!-- end sub nav -->



	<div class="container" id="aix-title">
		<div class="row">
			<!-- SECTION 1: ORDER LISTING -->
			<div class="col-md-10 section sec-1">
		    	<h1><?=$Text['ti_mng_orders'];?></h1>
		    </div>

		    <!-- SECTION 2: REVISE ORDER -->
			<div class="col-md-10 section sec-2">
		    	<h1>
		    		<span class="glyphicon glyphicon-chevron-left change-sec" target-section="#sec-1"></span> 
		    		<?=$Text['ti_revise'];?> <span class="set-provider"></span>
		    	</h1>
		    </div>

		    <!-- SECTION 3: VIEW ORDER DETAIL -->
		 	<div class="col-md-10 section sec-3">
		    	<h1>
		    		<span class="glyphicon glyphicon-chevron-left change-sec" target-section="#sec-1"></span> 
		    		<?=$Text['ti_order_detail'];?> <span class="set-provider"></span>
		    	</h1>
		    </div>

		</div>
	</div>



	<!-- SECTION 1: ORDER LISTING -->
	<div class="container">
		<div class="row">
			<div class="section sec-1">
				<table id="tbl_orderOverview" class="table table-hover">
				<thead>
					<tr>
						<th>&nbsp;<input type="checkbox" id="toggleBulkActions" name="toggleBulk"/></th>
						<th><?=$Text['id'];?></th>
						<th><?=$Text['provider_name'];?> </span></th>
						<th><?=$Text['ordered_for'];?></th>
						<th><?=$Text['closes_days'];?></th>
						<th><?=$Text['sent_off'];?></th>
						<th><?=$Text['date_for_shop'];?></th>
						<th><?=$Text['order_total'];?> </th>
						<th><?=$Text['status'];?></th>
						<th><?=$Text['actions'];?></th>
					</tr>
				</thead>
				<tbody>
					<tr id="{id}" orderId="{id}" dateForOrder="{date_for_order}" providerId="{provider_id}" class="clickable">
						<td><input type="checkbox" name="bulkAction"/></td>
						<td>{id}</td>
						<td><p class="text-left">{provider_name}</p></td>
						<td>{date_for_order}</td>
						<td>{time_left}</td>
						<td>{ts_sent_off}</td>
						<td>{date_for_shop}</td>
						<td><p  class="text-right">{order_total}<?php echo $Text['currency_sign']; ?>&nbsp;&nbsp;</p></td>
						<td>{revision_status}</td>
						<td>
							<span class="glyphicon glyphicon-pencil btn-revise-order pull-left" title="<?=$Text['btn_revise'];?>"></span>				
						</td>
					</tr>
				</tbody>
			</table>
			</div> 
		</div>
	</div>




	<!-- SECTION 2: ORDER INFO -->
	<div class="container">
		<div class="row">
			<div class="section sec-2">				
				<table id="tbl_orderDetailInfo" class="table table-bordered">
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
							<p class="textAlignRight  boldStuff">{total} <?php echo $Text['currency_sign'];?></p>
						</td>
					</tr>
					<tr>
						<td>
							<p><?php echo $Text['phone_pl']; ?></p>
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
							<p class="textAlignRight  boldStuff">{delivered_total} <?php echo $Text['currency_sign'];?></p>
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
							<p class="textAlignRight boldStuff">{validated_income} <?php echo $Text['currency_sign'];?></p>
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
	</div>

	


	<!-- SECTION 3: REVISE ORDER -->
	<div class="container">
		<div class="row">
			<div class="section sec-2 sec-3">	
				<table id="tbl_reviseOrder" class="table table-bordered">
					<thead>
						<tr>
							<th><?=$Text['id'];?></th>
							<th><?=$Text['product_name'];?></th>
							<th><?=$Text['unit'];?></th>
							<th class="arrivedCol"><?=$Text['arrived']; ?></th>
						</tr>
						<tr class="orderTotals">
							<td colspan="3" class="orderTotalsDesc"><?php echo $Text['or_prv_prices']; ?><td>
							<td class="arrivedCol"></td>
						</tr>
					</thead>
					<tfoot>
						<tr class="orderTotals">
							<td colspan="3"class="orderTotalsDesc"><?php echo $Text['or_prv_prices']; ?><td>
							<td class="arrivedCol"></td>
						</tr>
					</tfoot>
					<tbody>
						<tr>							
							<td id="product_{id}" class="productIdClass"
								gross_price="{gross_price}"
								iva_percent="{iva_percent}"
								net_price="{net_price}"
								rev_tax_percent="{rev_tax_percent}"
								uf_price="{uf_price}">{id}</td>
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




<div id="dialog_orderStatus" title="Set Order Status" class="section sec-4">
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


<div id="dialog_convertPreorder" title="Convert preorder to order" class="section sec-4">
	<p>&nbsp;</p>
	<p class="success_msg aix-style-ok-green ui-corner-all aix-style-padding8x8"></p>
	<p><?php echo $Text['msg_pre2Order']; ?></p>
	<p>&nbsp;</p>
	
	<form class="navbar-form pull-right" role="date">
		<div class="form-group">
            <div class='input-group date input-group-sm' id='datepicker-preorder2order' >
                <input type='text' class="form-control" id="date-from" data-format="dddd, ll" placeholder="From" />
                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </div>
    </form>


</div>

<div id="dialog_setShopDate" title="Set shopping date" class="section sec-4">
	<p>&nbsp;</p>
	<p class="success_msg aix-style-ok-green ui-corner-all aix-style-padding8x8"><?php echo $Text['msg_move_to_shop']; ?></p>
	<p><?php echo $Text['msg_confirm_move']; ?></p>
	<br/>
	<p class="textAlignCenter boldStuff set-shopdate"></p> 
	<br/>
	<p><a href="javascript:void(null)" id="showDatePicker"><?php echo $Text['alter_date']; ?></a> </p>
	<br/>
	<form class="navbar-form pull-right" role="date">
		<div class="form-group">
            <div class='input-group date input-group-sm' id='datepicker-distribute' >
                <input type='text' class="form-control" id="date-from" data-format="dddd, ll" placeholder="From" />
                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </div>
    </form>
</div>
<iframe name="dataFrame" style="display:none;"></iframe>
<form id="submitZipForm" class="hidden"></form>


<iframe id="exportChannel" src="" style="display:none; visibility:hidden;" name="exportChannel"></iframe>
<div id="dialog_export_options" title="<?php echo $Text['export_options']; ?>" class="section sec-4">
<?php include("tpl/export_dialog.php");?>
</div>


<!-- / END -->
</body>
</html>













