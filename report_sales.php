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
    
    
   <?php if (isset($_SESSION['dev']) && $_SESSION['dev'] == true ) { ?> 
	    <script type="text/javascript" src="js/jquery/jquery.js"></script>
		<script type="text/javascript" src="js/jqueryui/jqueryui.js"></script>
		<script type="text/javascript" src="js/fgmenu/fg.menu.js"></script>
		<script type="text/javascript" src="js/aixadautilities/jquery.aixadaMenu.js"></script>     	 
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaXML2HTML.js" ></script>
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaUtilities.js" ></script>
	   	<script type="text/javascript" src="js/tablesorter/jquery.tablesorter.js" ></script>
   	<?php  } else { ?>
	   	<script type="text/javascript" src="js/js_for_report_shop_providers.min.js"></script>
    <?php }?>
   		
 	<script type="text/javascript" src="js/jqueryui/i18n/jquery.ui.datepicker-<?=$language;?>.js" ></script>
 	   
   
	<script type="text/javascript">
	$(function(){

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
				},
				complete : function(rowCount){
					$('tr:even', this).addClass('rowHighlight');
					//$("#tbl_Providers").trigger("update"); 
				
					$('.loadSpinner').hide();
					if (rowCount == 0){
						$.showMsg({
							msg:"<?=$Text['msg_err_order_filter'];?>",
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
					reloadProducts();
					
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
					if (tds.eq(3).text() == "1"){
						tds.eq(3).text("<?=$Text['stock'];?>");
					//orderable
					} else if (tds.eq(3).text() == "2"){
						tds.eq(3).text("<?=$Text['orderable'];?>");
					}
					
					if (gGroupBy == ""){
						tds.eq(2).html("<span title='Select Filter &gt; List Dates to view details'>various..</span>");
					}
					
				},
				complete: function(rowCount){
					sumTotalSales();
					switchTo('detail');

				}
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
				params : 'oper=getTotalSalesByProviders&filter=exact&provider_id='+gProviderId+'&from_date='+$.getSelectedDate('#datepicker_from')+'&to_date='+$.getSelectedDate('#datepicker_to'), 
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
			$('#tbl_products tbody tr').each(function(){
				var type = $(this).attr('orderableTypeId'); 

				if (type == 1 && gShowStock || type == 2 && gShowOrderable){
					$(this).show();
				} else if (type == 1 && !gShowStock || type == 2 && !gShowOrderable){
					$(this).hide();
				}
			})
			sumTotalSales();
		}


		function sumTotalSales(){
			var tnetto = $.sumSimpleItems('.nettoCol');
			var tbrutto = $.sumSimpleItems('.bruttoCol');
			$('#nettoTotal').text(tnetto +'€');
			$('#bruttoTotal').text(tbrutto+'€'); 
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
		    	<h1 class="salesDetailElements">Purchase total for <span class="setProviderName"></span></h1>
		    	
		    </div>
		    <div id="titleRightCol50">
		    
		    <table class="floatLeft">
				<tr>
					<td><?php echo $Text['date_from']; ?>:</td>
					<td><input type="text" id="datepicker_from" class="ui-corner-all"/></td>
					<td>&nbsp;&nbsp;</td>
					<td><?php echo $Text['date_to']; ?>:</p></td>
					<td><input type="text" id="datepicker_to" class="ui-corner-all"/></td>
				</tr>
			</table>
		    
		    
		    <button	id="tblViewOptions" class="btn_right salesDetailElements"><?=$Text['btn_filter']; ?></button>
				<div id="tblOptionsItems" class="hidden">
					<ul>
						<li><a href="javascript:void(null)" id="listDates" ><span class="floatLeft"></span>&nbsp;&nbsp;List dates</a></a></li>
						<li><a href="javascript:void(null)" id="stock" ><span class="floatLeft ui-icon ui-icon-check"></span>&nbsp;&nbsp;Stock</a></a></li>
						<li><a href="javascript:void(null)" id="orderable" ><span class="floatLeft ui-icon ui-icon-check"></span>&nbsp;&nbsp;Orderable</a></a></li>
					</ul>
				</div>	
		    	
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
							
							<th><p class="textAlignRight"><?php echo $Text['total_4provider']; ?></p></th>
						</tr>
					</thead>
					<tbody>
						<tr providerId="{provider_id}" providerName="{provider_name}">
							<td>{provider_id}</td>
							<td class="clickable">{provider_name}</td>
							<td class="total_{provider_id} floatRight">{total}</td>						
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
	        	<h3 class="ui-widget-header ui-corner-all">Sales for Pv name <span style="float:right; margin-top:-4px;"><img class="loadSpinner" src="img/ajax-loader.gif"/></span></h3>		
			
				<table id="tbl_products" class="tblListingDefault">
					<thead>
						<th><p class="textAlignCenter"><?php echo $Text['id'];?></p><span class="ui-icon ui-icon-triangle-2-n-s"></span></th>
						<th><p class="floatLeft"><?php echo $Text['name_item'];?></p><span class="ui-icon ui-icon-triangle-2-n-s"></span></th>						
						<th>Date</th>
						<th><p class="floatLeft">Type</p></th>

						<th><p class="textAlignRight">Price netto</p></th>
						
						<th><p class="textAlignCenter"><?php echo $Text['revtax_abbrev']; ?></p></th>
						<th><p class="textAlignCenter"><?php echo $Text['iva']; ?></p></th>
						
						<th><p class="textAlignRight">Price brutto</p></th>
						
						<th><p class="textAlignRight">Sold Quantity</p></th>		
						<th><p class="textAlignCenter"><?php echo $Text['unit'];?></p></th>				
						<th><p class="textAlignRight">Netto total </p></th>
						<th><p class="textAlignRight">Brutto total </p></th>
					</thead>
					
					<tbody>
						<tr orderableTypeId="{orderable_type_id}">
							<td><p class="textAlignCenter">{product_id}</p></td>
							<td>{product_name}</td>
							<td>{date_for_shop}</td>
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
						<tr>
													
							<td></td>
							<td></td>
							<td></td>
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