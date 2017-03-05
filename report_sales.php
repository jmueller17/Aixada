<?php include "php/inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_sales'] . " - " . $Text['nav_report_shop_pv'] ;?></title>

	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
  	<link rel="stylesheet" type="text/css"   media="print"  href="css/print.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/fgmenu/fg.menu.css"   />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/<?=$default_theme;?>/jqueryui.css"/>
    
    <script type="text/javascript" src="js/jquery/jquery.js"></script>
    <script type="text/javascript" src="js/jqueryui/jqueryui.js"></script>
    <?php echo aixada_js_src(); ?>
    <script type="text/javascript" src="js/tablesorter/jquery.tablesorter.js" ></script>
    
 	<script type="text/javascript" src="js/jqueryui/i18n/jquery.ui.datepicker-<?=$language;?>.js" ></script>
 	   
   
	<script type="text/javascript">
	$(function(){
		$.ajaxSetup({ cache: false });

		//var gUniqueProviders = [];

		//current provider 
		var gProviderId = 0; 

		var gSelProvider = null; 

		//if product sales list breaks down purchase by dates or not
		var gGroupBy = ""; // = "shop_date" shows each item x individual shop date. "" sums over all dates in range

		
		var gSection = "overview"; 	

		//filter for orderable / stock product type in product listing
		var gShowStock = 1; 

		var gShowOrderable = 1; 	

		
		//loading animatio
		$('.loadSpinner').attr('src', "img/ajax-loader-<?=$default_theme;?>.gif").hide(); 
		

		$("#datepicker_from").datepicker({
			dateFormat 	: 'D, d M, yy',
			onSelect : function (dateText, instance){
				$('#setFromDate').text(dateText);

				(gSection == 'overview')? reloadProviders():reloadProducts();
				
			}
		});

		$("#datepicker_to").datepicker({
			dateFormat 	: 'D, d M, yy',
			onSelect : function (dateText, instance){
				$('#setToDate').text(dateText);
				(gSection == 'overview')? reloadProviders():reloadProducts();
			}
		});


		//default, show sales for today
		$.getAixadaDates('getToday', function (date){
			$("#datepicker_to, #datepicker_from").datepicker('setDate', $.datepicker.parseDate('yy-mm-dd', date[0]));
		});


		

		/********************************************************
		 *    PURCHASE PROVIER LISTING
		 ********************************************************/
		
		//the table sorter plugin
		//$("#tbl_Providers").tablesorter();
		$("#tbl_Providers").bind('sortEnd', function(){
			$('tr',this).removeClass('rowHighlight')
			$('tr:even',this).addClass('rowHighlight');
		});


		
		//load purchase listing
		$('#tbl_Providers tbody').xml2html('init',{
				url : 'php/ctrl/Shop.php',
				params : 'oper=getTotalSalesByProviders&filter=exact&provider_id='+gProviderId+'&from_date='+$.getSelectedDate('#datepicker_from')+'&to_date='+$.getSelectedDate('#datepicker_to'), 
				loadOnInit : true, 
				beforeLoad : function(){
					$('.loadSpinner').show();
				},
				rowComplete : function(rowIndex, row){

					//update rev tax and iva amounts included for the given period 
					var revTaxQt = $('.sumRevQt', row).text() - $('.sumTotalNetto', row).text();
					$('.sumRevQt', row).text(revTaxQt.toFixed(2)+"<?=$Text['currency_sign'];?>");

					var ivaTaxQt = $('.sumIvaQt', row).text() - $('.sumTotalNetto', row).text();
					$('.sumIvaQt', row).text(ivaTaxQt.toFixed(2)+"<?=$Text['currency_sign'];?>");

					$('.sumTotalNetto', row).append("<?=$Text['currency_sign'];?>");
					$('.sumTotalBrutto', row).append("<?=$Text['currency_sign'];?>");
					
				},
				complete : function(rowCount){
					$('tr:even', this).addClass('rowHighlight');
					//$("#tbl_Providers").trigger("update"); 
				
					filterProviderList();
				
					$('.loadSpinner').hide();
					if (rowCount == 0){
						$.showMsg({
							msg:"<?=$Text['msg_err_order_filter'];?>",
							autoclose:2000,
							type: 'warning'});
					}
				}
		});

		
		$('#tbl_Providers tbody tr')
			.live('mouseover', function(){
				$(this).removeClass('highlight').addClass('ui-state-hover');
				var pvid = $(this).attr('providerId');
				$('#sumCell_'+pvid).addClass('ui-state-hover');
				
			})
			.live('mouseout',function(){
				$(this).removeClass('ui-state-hover');
				var pvid = $(this).attr('providerId');
				$('#sumCell_'+pvid).removeClass('ui-state-hover');
				
			})
			.live('click',function(e){
				if (gSelProvider != null) gSelProvider.removeClass('ui-state-highlight');
				gSelProvider = $(this);
				gProviderId = $(this).attr('providerId');
				$('.setProviderName').text($(this).attr('providerName'));
				$(this).addClass('ui-state-highlight');
				reloadProducts();
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

				if (filter == 'listDates'){
						if (gGroupBy == ""){
							$(item).children('span').addClass('ui-icon ui-icon-check');
							gGroupBy = "shop_date"; 
						} else {
							$(item).children('span').removeClass('ui-icon ui-icon-check');
							gGroupBy = "";
						}

						if (gSection == 'detail'){
							reloadProducts();
						} else {
							reloadProviders();
						}
					
				}  else if (filter == 'stock'){
					if (gShowStock){
						$(item).children('span').removeClass('ui-icon ui-icon-check');
						gShowStock = 0;  
					} else if (gShowStock == 0) {
						$(item).children('span').addClass('ui-icon ui-icon-check');
						gShowStock = 1;  
					}
					filterProductType();
					
				} else if (filter == 'orderable'){
					if (gShowOrderable){
						$(item).children('span').removeClass('ui-icon ui-icon-check');
						gShowOrderable = 0;  
					} else if (gShowOrderable == 0) {
						$(item).children('span').addClass('ui-icon ui-icon-check');
						gShowOrderable = 1;  
					}
					filterProductType();

				}
				
			}//end item selected 
		});//end menu

		

		
		/********************************************************
		 *    DETAIL PURCHASE VIEW
		 ********************************************************/	

		 $("#tbl_products tbody").xml2html("init", {
				url : 'php/ctrl/Shop.php',
				params : 'oper=getDetailSalesByProvider&provider_id='+gProviderId+'&groupby='+gGroupBy+'&from_date='+$.getSelectedDate('#datepicker_from')+'&to_date='+$.getSelectedDate('#datepicker_to'),
				loadOnInit: false,
				rowComplete: function(rowIndex, row){
					var tds = $(row).children();
					//stock
					if (tds.eq(4).text() == "1"){
						tds.eq(4).text("<?=$Text['stock'];?>");
					//orderable
					} else if (tds.eq(4).text() == "2"){
						tds.eq(4).text("<?=$Text['orderable'];?>");
					}
					
					if (gGroupBy == ""){
						tds.eq(3).html("<span title='Select Filter &gt; List Dates to view details'>various..</span>");
					}
					
				},
				complete: function(rowCount){
					filterProductType();
					switchTo('detail');

				}
			})
			
				
		$('#tbl_products tbody tr')
			.live('mouseover', function(){
				$(this).addClass('ui-state-hover');
				
			})
			.live('mouseout',function(){
				$(this).removeClass('ui-state-hover');
			});


		 $('#toggleProductBulkActions')
			.click(function(e){
				if ($(this).is(':checked')){
					$('input:checkbox[name="sumProduct"]').attr('checked','checked');
				} else {
					$('input:checkbox[name="sumProduct"]').attr('checked',false);
				}

				$('input[name=sumProduct]').each(function(){
					if ($(this).is(':visible')){
						toggleSumProduct($(this).parents('tr'), $(this).is(':checked'));
					}
				})
				sumTotalSales();
				
			});

		//bulk actions
		$('input[name=sumProduct]')
			.live('click', function(e){
				toggleSumProduct($(this).parents('tr'), $(this).is(':checked'));
				sumTotalSales();
			})
		


		//overview buttons
		$("#btn_overview").button({
			icons: {
	        		primary: "ui-icon-circle-arrow-w"
	        	}
			 })
    		.click(function(e){
				switchTo('overview'); 
    		});



			
		 /********************************************************
		  *    GLOBAL UTIL FUNCTIONS
		  ********************************************************/
						
		function switchTo(section){
			gSection = section; 
			switch (section){
				case 'detail':
					$('.overviewElements').hide();
					$('.salesDetailElements').fadeIn(1000);
					break;

				case 'overview':
					$('.salesDetailElements').hide();
					$('.overviewElements').fadeIn(1000);
					break;
				}

		}

		function reloadProviders(){
			$('#tbl_Providers tbody').xml2html('reload',{
				params : 'oper=getTotalSalesByProviders&provider_id=0&groupby='+gGroupBy+'&from_date='+$.getSelectedDate('#datepicker_from')+'&to_date='+$.getSelectedDate('#datepicker_to')
			});
		}

		function reloadProducts(){
			$("#tbl_products tbody").xml2html("reload", {
				params : 'oper=getDetailSalesByProvider&provider_id='+gProviderId+'&groupby='+gGroupBy+'&from_date='+$.getSelectedDate('#datepicker_from')+'&to_date='+$.getSelectedDate('#datepicker_to')
			})

		}

		/**
		 *	show hide stock or fresh products in listing
		 */
		function filterProductType(){
			if (gGroupBy == ""){
				$('.shopDateCol').hide(); 
			} else {
				$('.shopDateCol').show();
			}
			
			$('#tbl_products tbody tr').each(function(){
				var type = $(this).attr('orderableTypeId'); 

				if (type == 1 && gShowStock || type == 2 && gShowOrderable){
					$(this).show();
					$(this).children('td:last-child').children('p:first-child').addClass('bruttoCol');
					$(this).children('td:last-child').prev().children('p:first-child').addClass('nettoCol');
				} else if (type == 1 && !gShowStock || type == 2 && !gShowOrderable){
					$(this).hide();
					$(this).children('td:last-child').children('p:first-child').removeClass('bruttoCol');
					$(this).children('td:last-child').prev().children('p:first-child').removeClass('nettoCol');
				}
			})
			sumTotalSales();
		}


		function filterProviderList(){
			if (gGroupBy == ""){
				$('.shopDateCol').hide(); 
			} else {
				$('.shopDateCol').show();
			}

		}


		function sumTotalSales(){
			var tnetto = $.sumSimpleItems('.nettoCol');
			var tbrutto = $.sumSimpleItems('.bruttoCol');
			$('#nettoTotal').text(tnetto +"<?=$Text['currency_sign'];?>");
			$('#bruttoTotal').text(tbrutto+"<?=$Text['currency_sign'];?>"); 
		}


		/**
		 *	switches individual product rows on/off for the overall sum calculation
		 *  i.e calculates the sum over just stock, orderable or both depending on what is visible 
		 */
		function toggleSumProduct(seltr, checked){
			if (checked){
				seltr.children('td:last-child').children('p:first-child').addClass('bruttoCol');
				seltr.children('td:last-child').prev().children('p:first-child').addClass('nettoCol');
			} else {
				seltr.children('td:last-child').children('p:first-child').removeClass('bruttoCol');
				seltr.children('td:last-child').prev().children('p:first-child').removeClass('nettoCol');
			}

		}


		switchTo('overview');
			
		
			
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
				<button id="btn_overview" class="floatLeft salesDetailElements"><?php echo $Text['overview'];?></button>
		    	<h1 class="overviewElements"><?php echo $Text['ti_report_shop_pv']; ?></h1>
		    	<h1 class="salesDetailElements"><?php echo $Text['sales_total_pv']; ?> <span class="setProviderName"></span></h1>
		    	
		    </div>
		    <div id="titleRightCol50">
		    
		    
		    
		    	<button	id="tblViewOptions" class="btn_right"><?=$Text['btn_filter']; ?></button>
				<div id="tblOptionsItems" class="hidden">
					<ul>
						<li><a href="javascript:void(null)" id="listDates" ><span class="floatLeft"></span>&nbsp;&nbsp;<?php echo $Text['dates_breakdown']; ?></a></li>
						<li><a href="javascript:void(null)" id="stock" ><span class="floatLeft ui-icon ui-icon-check"></span>&nbsp;&nbsp;<?php echo $Text['stock'];?></a></li>
						<li><a href="javascript:void(null)" id="orderable" ><span class="floatLeft ui-icon ui-icon-check"></span>&nbsp;&nbsp;<?php echo $Text['orderable'];?></a></li>
					</ul>
				</div>	
				
				<table class="floatRight">
				<tr>
					<td><?php echo $Text['date_from']; ?>: </td>
					<td><input type="text" id="datepicker_from" class="ui-corner-all"/></td>
					<td>&nbsp;&nbsp;</td>
					<td><?php echo $Text['date_to']; ?>: </td>
					<td><input type="text" id="datepicker_to" class="ui-corner-all"/></td>
				</tr>
				</table>
		    	
		    </div>
		</div>
	

	
		<!--  
				PROVIDER LISTING
		 -->		
        <div id="purchase_list" class="ui-widget overviewElements">    
        	<div class="ui-widget-content ui-corner-all">
	        	<h3 class="ui-widget-header ui-corner-all">&nbsp;<span style="float:right; margin-top:-4px;"><img class="loadSpinner" src="img/ajax-loader.gif"/></span></h3>    
				<table id="tbl_Providers" class="tblListingDefault">
					<thead>
						<tr>
							<th><?php echo $Text['id']; ?></th>
							<th class="textAlignCenter"><?php echo $Text['provider']; ?></th>
							<th class="shopDateCol textAlignCenter"><?php echo $Text['purchase_date']; ?></th>
							<th><p class="textAlignRight"><?php echo $Text['total_netto']; ?></p></th>
							<th><p class="textAlignRight"><?php echo $Text['revtax_abbrev']; ?></p></th>
							<th><p class="textAlignRight"><?php echo $Text['iva']; ?></p></th>
							<th><p class="textAlignRight"><?php echo $Text['total_brutto']; ?></p></th>
						</tr>
					</thead>
					<tbody>
						<tr providerId="{provider_id}" providerName="{provider_name}">
							<td>{provider_id}</td>
							<td class="clickable">{provider_name}</td>
							<td class="shopDateCol">{date_for_shop}</td>
							<td><p class="sumTotalNetto textAlignRight">{total_sales_netto}</p></td>
							<td><p class="sumRevQt textAlignRight">{total_sales_rev}</p></td>
							<td><p class="sumIvaQt textAlignRight">{total_sales_iva}</p></td>
							<td><p class="sumTotalBrutto textAlignRight">{total_sales_brutto}</p></td>
																			
						</tr>
					</tbody>
					<tfoot>
					</tfoot>
				</table>
			</div>
		</div>	
		
		
		<!--  
				DETAIL PRODUCT SALES LISTING FOR GIVEN PROVIDER
		 -->		
		<div id="purchase_detail" class="ui-widget salesDetailElements">    
        	<div class="ui-widget-content ui-corner-all">
	        	<h3 class="ui-widget-header ui-corner-all">&nbsp; <span style="float:right; margin-top:-4px;"><img class="loadSpinner" src="img/ajax-loader.gif"/></span></h3>		
			
				<table id="tbl_products" class="tblListingDefault">
					<thead>
						<th>&nbsp;<input type="checkbox" id="toggleProductBulkActions" name="toggleProductBulk" checked="checked" title="Toggle sum items &Sigma;"/></th>
						<th><p class="textAlignCenter"><?php echo $Text['id'];?></p></th>
						<th><p class="floatLeft"><?php echo $Text['name_item'];?></p></th>						
						<th class="shopDateCol textAlignCenter"><?php echo $Text['purchase_date']; ?></th>
						<th><p class="floatLeft"><?php echo $Text['orderable_type']; ?></p></th>
						<th><p class="textAlignRight"><?php echo $Text['price_net']; ?></p></th>
						<th><p class="textAlignCenter"><?php echo $Text['revtax_abbrev']; ?></p></th>
						<th><p class="textAlignCenter"><?php echo $Text['iva']; ?></p></th>
						
						<th><p class="textAlignRight"><?php echo $Text['price_brutto']; ?></p></th>
						
						<th><p class="textAlignRight"><?php echo $Text['total_qty']; ?></p></th>		
						<th><p class="textAlignLeft"><?php echo $Text['unit'];?></p></th>				
						<th><p class="textAlignRight"><?php echo $Text['total_netto']; ?></p></th>
						<th><p class="textAlignRight"><?php echo $Text['total_brutto']; ?></p></th>
					</thead>
					
					<tbody>
						<tr orderableTypeId="{orderable_type_id}">
							<td><input type="checkbox" name="sumProduct" checked="checked" title="Include / exclude in &Sigma;"/></td>
							<td><p class="textAlignCenter">{product_id}</p></td>
							<td>{product_name}</td>
							<td class="shopDateCol">{date_for_shop}</td>
							<td><p class="textAlignCenter">{orderable_type_id}</p></td>
							
							<td><p class="textAlignRight">{unit_price_stamp_netto}</p> </td>							
							
							
							<td><p class="textAlignCenter">{rev_tax_percent}%</p></td>
							<td><p class="textAlignCenter">{iva_percent}%</p></td>
							
							<td><p class="textAlignRight">{unit_price_stamp}</p> </td>
							
							<td><p class="textAlignRight">{total_sales_quantity} </p></td>
							<td><p class="textAlignCenter">{shop_unit}</p></td>	
							<td><p class="textAlignRight nettoCol">{total_sales_netto}</p></td>	
							<td><p class="textAlignRight bruttoCol">{total_sales_brutto}</p></td>	
						
						</tr>
					
					
					</tbody>
					<tfoot>
						<tr>
						
							<td></td>						
							<td></td>
							<td></td>
							<td class="shopDateCol"></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td class="boldStuff"><?php echo $Text['total']?>: <span id="nettoTotal"></span></td>
							<td class="boldStuff"><?php echo $Text['total']?>: <span id="bruttoTotal"></span></td></td>
						</tr>
						
						
					
					</tfoot>
				
				</table>
			
			</div>
		</div>		
		
		
		
	<p>&nbsp;</p>
	<p>&nbsp;</p>
	<p>&nbsp;</p>
			
			
			
			
			
			
		
		
	</div>
	<!-- end of stage wrap -->
</div>
<!-- end of wrap -->
<!-- / END -->



</body>
</html>