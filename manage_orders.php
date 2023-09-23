<?php include "php/inc/header.inc.php"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] ." -  " .$Text['head_ti_manage_orders']; ?></title>

 	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/fgmenu/fg.menu.css"   />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/<?=$default_theme;?>/jqueryui.css"/>
    <!-- link rel="stylesheet" type="text/css" 	 media="screen" href="js/tablesorter/themes/blue/style.css"/-->
    <style>
        .tblReviseOrder td.grossPrice,
        .tblReviseOrder td.netPrice {
             background-color:#dee; text-align:right;
        }
        .tblReviseOrder td.grossRow {
             background-color:#ece; text-align:right;
        }
        .tblReviseOrder td.grossTotalOrder {
             background-color:#cce; text-align:right;
        }
        .tblReviseOrder td.netRow {
            background-color:#ebe; text-align:right;
        }
        .tblReviseOrder td.netTotalOrder {
            background-color:#bbe; text-align:right;
        }
        .orderTotalsDesc {
            text-align: right;
            color:#aaa;
        }
    </style>
    
    <script type="text/javascript" src="js/jquery/jquery.js"></script>
    <script type="text/javascript" src="js/jqueryui/jqueryui.js"></script>
    <?php echo aixada_js_src(); ?>
    
    <script type="text/javascript" src="js/tablesorter/jquery.tablesorter.js" ></script>
    <script type="text/javascript" src="js/jeditable/jquery.jeditable.mini.js" ></script>
    <script type="text/javascript" src="js/jqueryui/i18n/jquery.ui.datepicker-<?=$language;?>.js" ></script>
    <script type="text/javascript" src="js/aixadautilities/scroll-table.js"></script>
     
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
            _confirm:        "<?php echo $Text['msg_confirm']; ?>",
            _warning:        "<?php echo $Text['msg_warning']; ?>",
            _click_to_edit_total: "<?php echo $Text['or_click_to_edit_total']; ?>",
            _click_to_edit_gprice:"<?php echo $Text['or_click_to_edit_gprice']; ?>"
        };

        // Configuration values used by js code.        
        var local_cfg = {
            order_distribution_method: "<?php echo get_config(
                        'order_distribution_method', 'only_distribute'); ?>",
            record_provider_invoice: <?php 
                $cfg_accounts = get_config('accounts', array());
                $cfg_record_provider_invoice = get_config(
                    'order_distributeValidate_invoce', 
                    (isset($cfg_accounts['use_providers']) &&
                            $cfg_accounts['use_providers'] ? 1 : 0)
                );
                echo $cfg_record_provider_invoice; ?>,
            print_order_template: "<?php echo get_config(
                        'print_order_template', 'report_order1.php'); ?>",
            order_review_uf_sequence: "<?php echo get_config(
                        'order_review_uf_sequence', 'desc'); ?>",
            revision_fixed_uf: <?php echo get_config('revision_fixed_uf', 0); ?>
        };
    </script>
	   
	<script type="text/javascript">

	//list of order to be bulk-printed
	var gPrintList = [];

	
	$(function(){
		$.ajaxSetup({ cache: false });

			$('.reviewElements, .viewElements').hide();
			$('.success_msg').hide();

			//loading animation
			$('.loadSpinner').attr('src', "img/ajax-loader-<?=$default_theme;?>.gif");
			$('.loadSpinner_order').attr('src', "img/ajax-loader-<?=$default_theme;?>.gif"); 
			

			var header = [];

			var tblHeaderComplete = false; 

			//the selected order row that is currently revised or viewed
			var gSelRow = null; 

			//indicates page subsection: overview | review | view
			var gSection = 'overview';

			//index for current order that is loaded/printed during bulk actions
			var gPrintIndex  = -1; 

			//order revision status states. 
			var gRevStatus = [null, 'finalized','revised','postponed','canceled','revisedMod'];
			var gRevStatusI18n = [null, local_lang.ostat_desc_sent, local_lang.ostat_desc_nochanges, local_lang.ostat_desc_postponed, local_lang.ostat_desc_cancel, local_lang.ostat_desc_changes];
			var gRevStatusClass = ['dim40', 'isSend','asOrdered','postponed','orderCanceled','withChanges'];



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
					url: 'php/ctrl/UserAndUf.php?oper=getUfListing&all=0&order='+
                        local_cfg.order_review_uf_sequence,
					dataType:"xml",
					success: function(xml){
						var theadStr = '<th>'+local_lang.total+'</th>'; 
						var theadStr2 = '';
						$(xml).find('row').each(function(){
							var id = $(this).find('id').text(),
								uf_name = $(this).find('name').text();
							var colClass = 'Col-'+id;
							header.push(id);
							theadStr += '<th class="'+colClass+' hidden col"'+
								' uf_name="'+uf_name+'" id="ror_th_uf-'+id+'">'+
								id+'</th>';
							theadStr2 += '<td class="'+colClass+' hidden col"></td>';
						});

						theadStr += '<th>'+local_lang.total+'</th>';
						theadStr2 += '<td>&nbsp;</td>';
						theadStr += 
							'<th class="grossCol grossLabel">'+local_lang._gross_price+'</th>'+
							'<th class="grossCol grossLabel">'+local_lang._gross_total+'</th>'+
							'<th class="netCol netLabel">'+local_lang._net_price+'</th>'+
							'<th class="netCol netLabel">'+local_lang._net_total+'</th>';
						theadStr2 += 
							'<td class="grossCol grossLabel orderTotalsDesc">'+local_lang._suma+':</td>'+
							'<td class="grossCol grossTotalOrder"></td>'+
							'<td class="netCol netLabel orderTotalsDesc">'+local_lang._suma+':</td>'+
							'<td class="netCol netTotalOrder"></td>';
						theadStr += '<th class="revisedCol">'+local_lang.ostat_revised+'</th>';
						theadStr2 += '<td>&nbsp;</td>';
						
						$('#tbl_reviseOrder thead tr').first().append(theadStr);
						$('#tbl_reviseOrder thead tr').last().append(theadStr2);
						$('#tbl_reviseOrder tfoot tr').last().append(theadStr2);

						tblHeaderComplete = true; 

					},
					error : function(XMLHttpRequest, textStatus, errorThrown){
						$.showMsg({
							msg:XMLHttpRequest.responseText,
							type: 'error'});	
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

			//STEP 2: construct table structure: products and col-cells. 
			$('#tbl_reviseOrder tbody').xml2html('init',{
				url : 'php/ctrl/Orders.php',
				loadOnInit : false,
				beforeLoad : function() {
					$('.loadSpinner_order').show();
				},
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
					tbodyStr += '<td id="grossPrice_'+product_id+'" class="grossCol grossPrice"></td>';
					tbodyStr += '<td id="grossRow_'+product_id+'"   class="grossCol grossRow"></td>';
					tbodyStr += '<td id="netPrice_'+product_id+'"   class="netCol netPrice"></td>';
					tbodyStr += '<td id="netRow_'+product_id+'"     class="netCol netRow"></td>';
					
					//revised checkbox for product
					tbodyStr += '<td class="textAlignCenter revisedCol"><input type="checkbox" isRevisedId="'+product_id+'" id="ckboxRevised_'+product_id+'" name="revised" /></td>';
					$(row).last().append(tbodyStr);
					
				},
				complete : function (rowCount){
					if (!gSelRow) {
                        $('.loadSpinner_order').hide();
                        return;
                    }
					//STEP 3: populate cells with product quantities
					$.ajax({
					type: "POST",
					url: 'php/ctrl/Orders.php?oper=getProductQuantiesForUfs&order_id='+ gSelRow.attr('orderId')+'&provider_id='+gSelRow.attr("providerId")+'&date_for_order='+gSelRow.attr("dateForOrder"),
					dataType:"xml",
					success: function(xml){
						$('.loadSpinner_order').hide();
						var quTotal = 0;
						var quShopTotal = 0; 
						var quShop = 0; 
						var lastId = -1; 
						var quShopHTML = '';  
						var hasIva = false;
						
						$(xml).find('row').each(function(){
						
							//for the view section, ordered quantities and revised (shop) quantities are shown
							if (gSection == 'view' && gSelRow.attr('orderId') > 0){
								quShop = $(this).find('shop_quantity').text();
								quShop = (quShop == '')? 0:quShop;  //items that did not arrived produce a null value. 
								quShopHTML = (gSection == 'view')? ' <span class="shopQuantity">(' +quShop +')</span> ':'';
							}
							var product_id = $(this).find('product_id').text();
							if ($(this).find('iva_percent').text() !== '0') {
								hasIva = true;
							}
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
								
								var total = "<span>"+quTotal.toFixed(3)+"</span> <span class='shopQuantity'>("+quShopTotal.toFixed(2)+")</span>";
								
								$('.total_'+lastId).html(total);
								refreshRowPrices(lastId);
								quTotal = 0; 
								quShopTotal = 0; 
							}
							
							quTotal += new Number(qu); 
							quShopTotal += new Number(quShop);
							lastId = product_id; 

						});

						
						var total = "<span>"+quTotal.toFixed(3)+"</span> <span class='shopQuantity'>("+quShopTotal.toFixed(2)+")</span>";
						$('.total_'+lastId).html(total);

                        refreshRowPrices(lastId);
                        refreshTotalOrder();
                        $('.orderTotals').show();
                        $('.grossCol').show();
                        if (hasIva === true) {
                            $('.netCol').show();
                        } else {
                            $('.netCol').hide();
                        }
                        if (local_cfg.revision_fixed_uf) {
                            $('.Col-'+local_cfg.revision_fixed_uf)
                                .removeClass('hidden').show();
                        }

						//don't need revised and arrived column for viewing order
						if (gSection == 'view') {
							$('.revisedCol, .arrivedCol').hide();
							$('.shopQuantity').show();
							$('tr, td').removeClass('toRevise revised missing');
						} else {
							$('.revisedCol, .arrivedCol').show();
							$('.shopQuantity').hide();
						}

						$('#tbl_reviseOrder').show();

                        new ScrollTable(document.getElementById('tbl_reviseOrder'), {
                            height: 500
                        }).show();
					},
					error : function(XMLHttpRequest, textStatus, errorThrown){
						$('.loadSpinner_order').hide();
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

            // Add item to order
            $("#btn_addToOrder").button({
                icons: {
                        primary: "ui-icon-plus"
                }
            }).click(function(e) {
                $('#dialog_addToOrder').dialog("open");
            }).hide();
            $('#dialog_addToOrder').dialog({
                autoOpen: false,
                width: 600,
                height: 300,
                buttons: {  
                    "<?=i18n_js('btn_addToOrder');?>": function() {
                        $('#tbl_reviseOrder').hide();
                        var $this = $(this);
                        $.ajax({
                            type: "POST",
                            url: 'php/ctrl/Orders.php?oper=editQuantity' +
                                '&order_id=' +gSelRow.attr('orderId') +
                                '&product_id=' + $('#ordItemAdd_product').val() +
                                '&uf_id=' + $('#ordItemAdd_uf').val() +
                                '&quantity=' + $('#ordItemAdd_quantity').val(),
                            success: function(txt) {
                                $('#tbl_reviseOrder tbody').xml2html("reload", {
                                    //reload order details for revision
                                    params: 'oper=getOrderedProductsListPrices' + 
                                        '&order_id=' + gSelRow.attr("orderId") +
                                        '&provider_id=' + gSelRow.attr("providerId") +
                                        '&date=' + gSelRow.attr("dateForOrder") +
                                        '&page=review'
                                });
                                $('#dialog_addToOrder').dialog("close");
                            },
                            error: function(XMLHttpRequest, textStatus, errorThrown){
                                $('#tbl_reviseOrder').show();
                                $.showMsg({
                                    msg:XMLHttpRequest.responseText,
                                    type: 'error'});
                                
                            }
                        });
                    },
                    "<?=$Text['btn_close'];?>": function() {
                        $(this).dialog("close");
                    } 
                }
            });
            $('#ordItemAdd_product').xml2html("init", {
                url: "php/ctrl/Orders.php",
                offSet: 1,
                loadOnInit: false,
                complete: function() { }
            });
            $('#ordItemAdd_uf').xml2html("init", {
                url: "php/ctrl/Orders.php",
                offSet: 1,
                loadOnInit: false,
                complete: function() { }
            });
			
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
							title: local_lang._confirm,
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
								$('.ui-dialog-buttonpane button', $this.parent()).hide();
								$('#showDatePicker').hide();
								$('.success_msg').show().next().hide();
								$this.button('disable');
								setTimeout(function(){
									$('.ui-dialog-buttonpane button', $this.parent()).show();
									$('#showDatePicker').show();
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
				
            /**
             * Distribute and validate
             */
            $("#btn_disValidate").button({
                icons: { primary: "ui-icon-cart" }
            }).click(function(e) {
                var allRevised = true;
                $('input:checkbox[name="revised"]').each( function(){
                    if (!$(this).is(':checked')){
                        allRevised = false; 
                        return false; 
                    }
                });
                $.showMsg({
                    msg: (allRevised ? "" : "<?=$Text['msg_err_unrevised']?><hr><br>") +
                        "<?=str_replace( array("\r", "\n"), array("\\r", "\\n"),
                                $cfg_record_provider_invoice ? 
                                i18n('msg_con_disValitate_prvInv') :
                                i18n("msg_con_disValitate") );?>",
                    buttons: {
                        "<?=$Text['btn_disValitate_ok'];?>": function(){
                            var $this = $(this);
                            var _orderId = gSelRow.attr('orderId');
                            $('.ui-dialog-buttonpane button', $this.parent()).hide();
                            $this.html('<?=$Text['wait_work'];?>');
                            $.ajax({
                                type: "POST",
                                url: 'php/ctrl/Orders.php?oper=directlyValidateOrder&order_id='+_orderId+
                                    '&record_provider_invoice='+local_cfg.record_provider_invoice,
                                success: function(txt){
                                    $this.dialog("close");
                                    //reload order list
                                    $('#tbl_orderOverview tbody').xml2html('reload');
                                    switchTo('overview');
                                    $.showMsg({
                                        msg: txt,
                                        autoclose: 3000,
                                        buttons: {},
                                        title: "<?php echo $Text['msg_success']; ?>",
                                        type: 'success'
                                    });
                                },
                                error : function(XMLHttpRequest, textStatus, errorThrown){
                                    switchTo('overview');
                                    $this.dialog("close");
                                    $.showMsg({
                                        msg: "<?=i18n('msg_err_disValitate');?>"+ _orderId +
                                            "<hr><br>" + XMLHttpRequest.responseText,
                                        type: 'error'});
                                }
                            });
                        },
                        "<?=$Text['btn_bakToRevise'];?>": function(){
                            $(this).dialog("close");
                        }
                    },
                    title: "<?=i18n('msg_confirm');?>",
                    type: 'confirm'
                });
            }).hide();

			/**
			 *	when closing a preorder, a delivery date needs to be set. 
			 */
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
								title: local_lang._warning,
								type: 'warning'});
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
										params : 'oper=getOrdersListing&filter=pastMonths2Future'
									});
								},500);

							}
						});
	
						
						},
				
					"<?=$Text['btn_close'];?>"	: function(){
						$( this ).dialog( "close" );
						} 
				}
			});


			
			//export order options dialog
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
			});
			


			//adjust total quantities
			$('td.totalQu')
				.live('mouseover', function(e){
					
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
											$(this).children(':first').empty().text(total_quantity.toFixed(3));
										});
										refreshRowPrices(pid);
										refreshTotalOrder();
									}//end callback 
							});
					}
			});
            //adjust gross price
            $('td.grossPrice').live('mouseover', function(e) {
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
				.live('mouseover', function(e){						//make each cell editable on mouseover. 
					var col = $(this).attr('col'),
						uf_name = $('#ror_th_uf-'+col).attr('uf_name');
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
                                        local_lang.uf_short + ' ' + col +
                                            ' '+uf_name+'\n'+
                                        product + '\n' +
                                        local_lang.click_to_edit,
									callback: function(value, settings){
										$(this).parent().removeClass('toRevise').addClass('revised');
										
										recalcRowTotal(row);
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
						refreshRowPrices(product_id);
						refreshTotalOrder();
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
				var $this = $(this);
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
							title: local_lang._warning,
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
						$('#dialog_export_options').dialog("close");
					}, 2000);
				}	
			}


			function checkExportForm(){
				var frmData = $('#frm_export_options').serialize();
				if (!$.checkFormLength($('input[name=exportName]'),1,150)){
					$.showMsg({
						msg:"File name cannot be empty!",
						type: 'error'});
					return false;
				}
				return frmData; 
			}


			
			
			/***********************************************************
			 *		ORDER OVERVIEW FUNCTIONALITY
			 **********************************************************/
			//var timePeriod = (gFilter != '')? gFilter:'pastMonths2Future';
			var _date_todayOverview;
			$('#tbl_orderOverview tbody').xml2html('init',{
				url : 'php/ctrl/Orders.php',
				params : 'oper=getOrdersListing&filter='+gFilter, 
				loadOnInit : true, 
				beforeLoad : function(){
					$('.loadSpinner').show();
					// refresh date
					_date_todayOverview = new Date();
					_date_todayOverview.setHours(0,0,0,0);
				},
				rowComplete : function (rowIndex, row){
					var tds = $(row).children();
					var orderId = $(row).attr("id");
					var timeLeft = parseInt(tds.eq(4).text());
					var status = tds.eq(8).text();
					var isPreorder = (tds.eq(3).text() == '1234-01-23')? true:false;

					if (isPreorder){ //preorder has no closing date
						tds.eq(3).text("<?php echo $Text['special_offer']?>");
						tds.eq(4).text('');
						tds.eq(6).text('-');
					}
					
					if (timeLeft >= 0){ // order is still open
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

						//if order has been send but not yet received, it can be reopened
						if (status == 1 || status == 3 || status == 4) {
							var date_for_order = new Date(tds.eq(3).text());
							if (date_for_order.getTime() >= _date_todayOverview.getTime() || isPreorder) {
								tds.eq(6).html(
									'<a href="javascript:void(null)" class="reopenOrderBtn">'+
									'<?php echo i18n_js('os_reopen_order_a'); ?>'+' #'+orderId+
									'</a>'
								);
							}
						}

					} else {
						//while open and not sent off, no order_id exists. show the finalize button
						tds.eq(1).html('<p>-</p>');
						tds.eq(5).html('<p><a href="javascript:void(null)" class="finalizeOrder"><?=$Text['finalize_now'];?></a></p>');
						tds.eq(9).html(
						    '<a href="javascript:void(null)" class="cancelOrderBtn">' +
						    '<?php echo i18n_js('or_cancel_order_a'); ?>' +
						    '</a>'
						);
					}
				},
				complete : function (rowCount){
					$("#tbl_orderOverview").trigger("update"); 
					if (rowCount == 0){
						$.showMsg({
							msg:"<?=$Text['msg_err_order_filter'];?>",
							title: local_lang._warning,
							type: 'warning'});
					}
					$('#tbl_orderOverview tbody tr:even').addClass('rowHighlight');
					$('.loadSpinner').hide(); 					
				}
			});

			$('.cancelOrderBtn').live("click", function(e) {
                var rowTr = $(this).parents('tr');
                var _dateForOrder = rowTr.attr("dateForOrder"),
                    _providerId = rowTr.attr("providerId"),
                    timeLeft = rowTr.children().eq(4).text(),
                    msg;
                if (timeLeft > 0){
                    msg = "<?=i18n_js('or_cancel_order_open');?>";
                } else {
                    msg = "<?=i18n_js('or_cancel_order');?>";
                }
                $.showMsg({
                    msg: msg,
                    buttons: {
                        "<?=$Text['btn_ok'];?>": function() {
                            var $this = $(this);
                            $.ajax({
                                type: "POST",
                                url: 'php/ctrl/Orders.php?oper=finalizeOrder' +
                                    '&revision_status=4' + // 4 = Cancel
                                    '&provider_id=' + _providerId +
                                    '&date=' + _dateForOrder,
                                success: function(txt){
                                        $('#tbl_orderOverview tbody').xml2html('reload');
                                },
                                error : function(XMLHttpRequest, textStatus, errorThrown){
                                    $.showMsg({
                                        msg:XMLHttpRequest.responseText,
                                        type: 'error'});
                                },
                                complete:function(){
                                    $this.dialog( "close" );
                                }
                            });
                        },
                        "<?=$Text['btn_cancel'];?>": function() {
                            $( this ).dialog( "close" );
                        }
                    },
                    title: local_lang._warning,
                    type: 'warning'
                });
                e.stopPropagation();
            });

			$('.reopenOrderBtn')
				.live("click", function(e){
					var orderId = $(this).parents('tr').attr("id");
						
						$.showMsg({
							msg:"<?=i18n_js('os_reopen_order');?>",
							buttons: {
								"<?=$Text['btn_ok'];?>":function(){	
										var $this = $(this);
										$.ajax({
											type: "POST",
											url: 'php/ctrl/Orders.php?oper=reopenOrder&order_id='+orderId,
											success: function(txt){
													$('#tbl_orderOverview tbody').xml2html('reload');
											},
											error : function(XMLHttpRequest, textStatus, errorThrown){
												$.showMsg({
													msg:XMLHttpRequest.responseText,
													type: 'error'});
											},
											complete:function(){
												$this.dialog( "close" );
											}
										});

									},
								"<?=$Text['btn_cancel'];?>" : function(){
										$( this ).dialog( "close" );
									}
							},
							title: local_lang._warning,
							type: 'warning'
						});

					e.stopPropagation();

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
						title: local_lang._confirm,
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
						$.ajaxQueue({
							type: "POST",
							url: 'php/ctrl/Orders.php?oper=checkValidationStatus&order_id='+gSelRow.attr('orderId'),
							success: function(xml) {
							
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
								var _reset_butt = function(clear) {
									return function() {
										$('.ui-dialog-buttonpane button', $(this).parent()).hide();
										$(this).html('<?=$Text['wait_reset'];?>');
										gSelRow.children().eq(8).attr('revisionStatus',1);
										var $this = $(this);
										resetOrder(gSelRow.attr('orderId'), clear, function(){
											$this.html('<?=$Text['msg_done'];?>');
											setTimeout(function(){
												switchTo('review', {});
												$this.dialog("close");
											}, 1000);
										});
									};
								};
								$.showMsg({
									msg:"<?php echo str_replace(array("\n","\r" ),array("\\n","\\r"),$Text['msg_revise_revised']); ?>",
									buttons: {
										"<?php echo $Text['btn_modify'];?>": _reset_butt(false),
										"<?php echo $Text['btn_delete'];?>": _reset_butt(true),
										"<?php echo $Text['btn_cancel'];?>" : function(){
											$( this ).dialog( "close" );
										}
									},
									title: local_lang._warning,
									type: 'warning'});

							} else if (isValidated){
								$.showMsg({
									msg:"<?=$Text['msg_err_already_val'];?>",
									type: 'error'});
							} else {
								switchTo('review', {});
							}		 
						}
						});
					} else {
						switchTo('review', {});
					}

					e.stopPropagation();
				});

			
				// header print buttons
				$('#dialog_printOpt').dialog({
				    autoOpen:false,
				    width:600,
				    height:200
				});
				$("#btn_printOpt").button().click(function(e) {
					$("#dialog_printOpt").dialog("open");
				});

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
								title: local_lang._warning,
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


				// export single order
				$('#btn_order_export')
					.button({
						icons: {
							primary: "ui-icon-transferthick-e-w"
			        	}
					})
					.click(function(e){
						$('#dialog_export_options')
							.data("export", "order")
							.dialog("open");
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
							params : 'oper=getOrdersListing&filter='+filter
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
							td.attr("title",local_lang.ostat_desc_sent).addClass('isSend').html('<span class="tdIconCenter ui-icon ui-icon-mail-closed"></span>');
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
				function printQueue() {
					var _queryStr = '';
                    var _dates = {};
                    var _orders = [];
                    var _countOrd = 0;
                    $('input:checkbox[name="bulkAction"]').each(function(){
                        if ($(this).is(':checked')){
                            var gSelRow = $(this).parents('tr');
                            _queryStr += 
                                '&order_id[]=' + gSelRow.attr("orderId") + 
                                '&provider_id[]=' + gSelRow.attr("providerId") +
                                '&date[]=' + gSelRow.attr("dateForOrder");
                            _dates[gSelRow.attr("dateForOrder")] = gSelRow.attr("dateForOrder");
                            if (gSelRow.attr("orderId")) {
                                _orders.push(gSelRow.attr("orderId"));
                            }
                            _countOrd++;
                        } 
                    });
                    if (_queryStr === '') {
                        $.showMsg({
                            type: 'warning',
                            title: local_lang._warning,
                            msg: "<?=$Text['msg_err_noselect'];?>",
                            buttons: {
                                "<?=$Text['btn_ok'];?>": function() {
                                    $(this).dialog("close");
                                }
                            }
                        });
                    } else {
                        var _title = $('#printOpt_format').val();
                        if (_title === 'default') {
                            _title = 'cfg_<?=get_config('email_order_format', 'notSet')?>';
                        }
                        _title = 'Orders-' + _title;
                        for (var prop in _dates) {
                            _title += ' ' + (prop == '1234-01-23' ? "<?=$Text['special_offer']?>" : prop);
                        }
                        if (_orders.length) {
                            _title += ' ' + _orders.join(',');
                        }
                        _title += ' [' + _countOrd + ']'
                        _queryStr += '&format=' + $('#printOpt_format').val() + 
                                     '&prices=' + $('#printOpt_prices').val();
                        var _printWin = window.open( 
                                'tpl/'+local_cfg.print_order_template 
                            ),
                            _done = false,
                            _count = 0;
                        _printWin.focus();
                        var _pull = function() {
                            setTimeout(function() {
                                _count++;
                                if (_done) { return; }
                                if (_count > 10) { return; }
                                var orderWrap = $('#orderWrap', _printWin.document);
                                if (orderWrap.length == 0) {
                                    _pull();
                                } else {
                                    _done = true;
                                    $('.anOrder', _printWin.document).hide();
                                    if (!$('#printOpt_header').prop('checked')) {
                                        $('#header', _printWin.document).hide();
                                    }
                                    _printWin.document.title =_title;
                                    orderWrap.load(
                                        'php/ctrl/Orders.php?oper=reportOrders' + _queryStr,
                                        function () {
                                            $('.loadingMsg', _printWin.document).hide();
                                        }
                                    );
                                }
                            }, 250);
                        };
                        _pull();
                    }
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
				function resetOrder(orderId, clear, callbackfn){
					$.ajax({
						type: "POST",
						url: 'php/ctrl/Orders.php?oper=resetOrder&order_id='+orderId+
							'&clear='+(clear?1:0),
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
							$('#btn_setShopDate').hide();
							$('#btn_disValidate').hide();
							$('#btn_addToOrder').hide();
							$('#dialog_addToOrder').dialog("close");
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
                            // Show review elements
                            $('.reviewElements').fadeIn(1000);
                            switch (local_cfg.order_distribution_method) {
                            case 'only_distribute':
                                $('#btn_setShopDate').show();
                                break;
                            case 'distribute_and_validate':
                                $('#btn_disValidate').show();
                                break;
                            case 'choice':
                                $('#btn_setShopDate').show();
                                $('#btn_disValidate').show();
                            }
                            $('#ordItemAdd_product').xml2html("reload",{
                                params: 'oper=getAllProductsToOrder&order_id=' +
                                    gSelRow.attr('orderId')
                                    
                            });
                            $('#ordItemAdd_uf').xml2html("reload",{
                                params: 'oper=getAllUfsToOrder&order_id=' +
                                    gSelRow.attr('orderId')
                            });
                            $('#btn_addToOrder').show();

							$('#dialog_orderStatus button').button('enable');
							$('#btn_'+gRevStatus[sindex]).button('disable');
							$('#currentOrderStatus')
								.html(gRevStatusI18n[sindex])
								.removeClass()
								.addClass("ui-corner-all aix-style-padding3x3 "+
									gRevStatusClass[sindex]);
							$('#dialog_orderStatus').dialog("open");
							$('.orderTotals').hide();
							$('#tbl_reviseOrder').hide();
							$('#tbl_reviseOrder tbody').xml2html("reload", {						//load order details for revision
								params : 'oper=getOrderedProductsListPrices&order_id='+gSelRow.attr("orderId")+
									'&provider_id='+gSelRow.attr("providerId")+
									'&date='+gSelRow.attr("dateForOrder")+
									'&page=review'
							});
							break;
							
						case 'view':
							var title = gSelRow.children().eq(2).text();

							//$('#viewOrderRevisionStatus') set the order status here. 
							$('.providerName').html(title);							
							$('.overviewElements').hide();
							$('.viewElements').fadeIn(1000);
							$('.orderTotals').hide();
							$('#tbl_reviseOrder').hide();
							$('#tbl_orderDetailInfo').hide();
							$('#tbl_reviseOrder tbody').xml2html("reload", {						//load order details for revision
								params : 'oper=getOrderedProductsListPrices&order_id='+gSelRow.attr("orderId")+'&provider_id='+gSelRow.attr("providerId")+'&date='+gSelRow.attr("dateForOrder")
							})
							$('#tbl_orderDetailInfo tbody').xml2html('reload',{						//load the info of this order
								params : 'oper=orderDetailInfo&order_id='+gSelRow.attr("orderId")+'&provider_id='+gSelRow.attr("providerId")+'&date='+gSelRow.attr("dateForOrder"),
								complete : function(rowCount){
                      				if (!gSelRow) {
                                        return;
                                    }
									$('#orderDetailDateForOrder').text($.getCustomDate(gSelRow.attr('dateForOrder')));
									$('#orderDetailShopDate').text($.getCustomDate($('#orderDetailShopDate').text()));
									//copy the order status 
									var tdStatus = gSelRow.children().eq(8).clone();
									$('#orderDetailRevisionStatus').before(tdStatus).remove();
									$('#tbl_orderDetailInfo').show();
								}
			 				});
							
							$('#btn_setShopDate').hide();
							$('#btn_disValidate').hide();
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
<div id="wrap">
	<div id="headwrap">
		<?php include "php/inc/menu.inc.php" ?>
	</div>
	<!-- end of headwrap -->
	
	<div id="stagewrap" class="<?= negative_balances_stagewrap_class() ?>">
	
		<div id="titlewrap" class="ui-widget">
			<div id="titleLeftCol50">
				<button id="btn_overview" class="floatLeft reviewElements viewElements"><?php echo $Text['overview'];?></button>
				<h1 class="reviewElements"><?=$Text['ti_revise'];?> <span class="providerName"></span></h1>
				<h1 class="viewElements"><?=$Text['ti_order_detail'];?> <span class="providerName aix-style-provider-name"></span></h1>
		    	<h1 class="overviewElements"><?=$Text['ti_mng_orders'];?></h1>
		    </div>
		   	<div id="titleRightCol50">
		   		<!-- button id="btn_setReview" class="viewElements btn_right"><?=$Text['btn_revise']; ?></button-->
		   		
		   		<button id="btn_disValidate" class="btn_right" title="<?=$Text['btn_disValitate'];?>"><?=$Text['btn_disValitate'];?></button>
		   		<button id="btn_setShopDate" class="btn_right" title="<?=$Text['distribute_desc'];?>"><?=$Text['btn_distribute'];?></button>
		   		<button id="btn_addToOrder" class="btn_right" title="<?=i18n('addToOrder_desc');?>"><?=i18n('btn_addToOrder');?></button>
				<button	id="tblViewOptions" class="overviewElements btn_right"><?=$Text['filter_orders']; ?></button>
				<button id="btn_order_export" class="floatRight viewElements" ><?php echo $Text['btn_export']; ?></button>
				<span style="float:right; margin-top:0px; margin-right:5px;"><img class="loadSpinner_order hidden" src="img/ajax-loader.gif"/></span>
				
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
                <button id="btn_printOpt"
                    title="<?=$Text['order_printOpt_dialog']?>"
                    class="overviewElements btn_right"
                    style="padding:4px 0"><span 
                        class="ui-button-icon-primary ui-icon ui-icon-gear" ></span></button>
                <div id="dialog_printOpt" title="<?=$Text['order_printOpt_dialog']?>" class="hidden">
                    <table>
                    <tr>
                        <td colspan="2">
                            <input type="checkbox" id="printOpt_header" value="true" checked style="height: 1em;"/>&nbsp;
                            <label for="printOpt_header" ><?=$Text['order_printOpt_header']?></label>
                        </td>
                    </tr><tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td><label for="printOpt_format"><?=$Text['order_printOpt_format']?></label>&nbsp;</td>
                        <td class="freeInput">
                            <select id="printOpt_format">
                                <option value="default" selected ><?=$Text['order_printOpt_default']?></option>
                                <option value="Prod"><?=$Text['prvOrdF_prod']?></option>
                                <option value="Matrix"><?=$Text['prvOrdF_matrix']?></option>
                                <option value="Prod_Matrix"><?=$Text['prvOrdF_prod_matrix'];?></option>
                                <option value="ProdUf"><?=$Text['prvOrdF_prodUf'];?></option>
                                <option value="Prod_ProdUf"><?=$Text['prvOrdF_prod_prodUf']?></option>
                                <option value="UfProd"><?=$Text['prvOrdF_ufProd']?></option>
                                <option value="GroupByUf"><?=$Text['prvOrdF_GroupByUf']?></option>
                            </select>
                        </td>
                    </tr>
                    <tr><td colspan="2">&nbsp;</td></tr>
                    <tr>
                        <td><label for="printOpt_prices"><?=$Text['order_printOpt_prices']?></label>&nbsp;</td>
                        <td class="freeInput">
                            <select id="printOpt_prices">
                                <option value="default" selected ><?=$Text['order_printOpt_default']?></option>
                                <option value="cost_amount"><?=$Text['prvOrdP_cost_amount']?></option>
                                <option value="cost"><?=$Text['prvOrdP_cost_price']?></option>
                                <option value="final_amount"><?=$Text['prvOrdP_final_amount']?></option>
                                <option value="final"><?=$Text['prvOrdP_final_price']?></option>
                                <option value="none"><?=$Text['prvOrdP_no_amount']?></option>
                            </select>
                        </td>
                    </tr>
                    </table>
                </div>
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
						<td><p  class="textAlignRight">{order_total}<?php echo $Text['currency_sign']; ?>&nbsp;&nbsp;</p></td>
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
				
				<table id="tbl_orderDetailInfo" class="hidden tblListingBorder2">
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
						<tr class="orderTotals">
							<td colspan="3" class="orderTotalsDesc"><?php echo $Text['or_prv_prices']; ?><td>
							<td class="arrivedCol"></td>
						</tr>
					</thead>
					<tfoot>
						<tr class="orderTotals">
							<td colspan="3" class="orderTotalsDesc"><?php echo $Text['or_prv_prices']; ?><td>
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
	<!-- end of stage wrap -->
</div>
<!-- end of wrap -->

<div id="dialog_orderStatus" title="<?php echo $Text['tit_set_orStatus'] ?>">
	<p>&nbsp;</p>
	<p><?php echo $Text['msg_cur_status'];?>: <span id="currentOrderStatus" class="ui-corner-all aix-style-padding3x3"></span>.</p>
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

<div id="dialog_addToOrder" title="<?php echo i18n('title_addToOrder') ?>">
    <table><tr>
        <td><?php echo i18n('product_name');?>:</td>
        <td><select id="ordItemAdd_product" class="longSelect">
                <option value="" selected="selected">(...)</option>
                <option value="{id}">{product_name}</option>
            </select>
        </td>
    </tr><tr>
        <td><?php echo i18n('uf_long');?>:</td>
        <td><select id="ordItemAdd_uf" class="longSelect">
                <option value="" selected="selected">(...)</option>
                <option value="{id}">{uf_name}</option>
            </select>
        </td>
    </tr><tr>
        <td><?php echo i18n('quantity');?>:</td>
        <td><input type="text" id="ordItemAdd_quantity"
            class="inputTxtMiddle ui-widget-content ui-corner-all"
            autocomplete="off"
            value=""
        ></td>
    </tr></table>
</div>

<div id="dialog_convertPreorder" title="Convert preorder to order">
	<p>&nbsp;</p>
	<p class="success_msg aix-style-ok-green ui-corner-all aix-style-padding8x8"></p>
	<p><?php echo $Text['msg_pre2Order']; ?></p>
	<p>&nbsp;</p>
	<div id="datepicker2"></div>
</div>

<div id="dialog_setShopDate" title="<?php echo $Text['tit_set_shpDate']; ?>">
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


<iframe id="exportChannel" src="" style="display:none; visibility:hidden;" name="exportChannel"></iframe>
<div id="dialog_export_options" title="<?php echo $Text['export_options']; ?>">
<?php include("tpl/export_dialog.php");?>
</div>


<!-- / END -->
</body>
</html>













