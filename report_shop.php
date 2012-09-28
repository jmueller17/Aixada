<?php include "php/inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_prev_orders'] ;?></title>

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
	   	<script type="text/javascript" src="js/js_for_report_shop.min.js"></script>
    <?php }?>
   		
 	<script type="text/javascript" src="js/jqueryui/i18n/jquery.ui.datepicker-<?=$language;?>.js" ></script>
 	   
   
	<script type="text/javascript">
	$(function(){


		//saves the selected purchase row
		var gSelShopRow = null; 


		//coming from other page
		var gDetail = (typeof $.getUrlVar('detailForCart') == "string")? $.getUrlVar('detailForCart'):false;


		//order overview filter option
		var gBackTo = (typeof $.getUrlVar('lastPage') == "string")? $.getUrlVar('lastPage'):false;
		
		

		/********************************************************
		 *    PURCHASE LISTING
		 ********************************************************/
		var shopDateSteps = 1;
		var srange = 'year';

		//the table sorter plugin
		$("#tbl_Shop").tablesorter();
		$("#tbl_Shop").bind('sortEnd', function(){
			$('tr',this).removeClass('rowHighlight')
			$('tr:even',this).addClass('rowHighlight');
		});
		
		//load purchase listing
		$('#tbl_Shop tbody').xml2html('init',{
				url : 'php/ctrl/Shop.php',
				params : 'oper=getShopListing&filter=prev3Month', 
				loadOnInit : true, 
				rowComplete : function(rowIndex, row){
					var validated = $(row).children().eq(4).text();

					if (validated == '0000-00-00 00:00:00'){
						$(row).children().eq(4).html("<p class='textAlignCenter'>-</p>");	
					} else {
						$(row).children().eq(4).html('<span class="ui-icon ui-icon-check tdIconCenter" title="Validated at: '+validated+'"></span>');
					}	

					if (gDetail > 0 && $(row).attr('shopId') == gDetail){
						gSelShopRow = $(row); 
					}	
				},
				complete : function(){
					$('tr:even', this).addClass('rowHighlight');
					$("#tbl_Shop").trigger("update"); 
					if (gSelShopRow != null) {
						gSelShopRow.trigger('click');
					}
				}
		});

		
		$('#tbl_Shop tbody tr')
			.live('mouseover', function(){
				$(this).removeClass('highlight').addClass('ui-state-hover');
				
			})
			.live('mouseout',function(){
				$(this).removeClass('ui-state-hover');
				
			})
			.live('click',function(e){
	
				if (gSelShopRow != null) gSelShopRow.removeClass('ui-state-highlight');
			
				$(this).addClass('ui-state-highlight');
				
				gSelShopRow = $(this); 
					
				$('#tbl_purchaseDetail tbody').xml2html('reload',{
					params : 'oper=getShopCart&shop_id='+$(this).attr('shopId')
				});

				$('.setUfId').text($(this).attr('ufId'));
				$('.setCartId').text($(this).attr('shopId'));
				$('.setShopDate').text($(this).attr('dateForShop'));

				var dateValidated = $(this).attr('validated');
				$('.setValidateStatus').removeClass('noRed okGreen');

				if (dateValidated == "0000-00-00 00:00:00"){
					$('.setValidateStatus').addClass('noRed').text('Not yet validated');
				} else {
					var opUf = $(this).attr('operatorUf'); 
					var opName = $(this).attr('operatorName'); 
					
					$('.setValidateStatus')
						.addClass('okGreen')
						.html('<span title="Validated by '+opUf+' '+opName+'">'+dateValidated.substring(0,10)+'</span>');
				}
	
				switchTo('detail');
		});
		

		

		
		/********************************************************
		 *    DETAIL PURCHASE VIEW
		 ********************************************************/	
		
		//load purchase detail (products and quantities)
			$('#tbl_purchaseDetail tbody').xml2html('init',{
				url : 'php/ctrl/Shop.php',
				params : 'oper=getShopCart', 
				loadOnInit : false, 
				rowComplete : function (rowIndex, row){
					var price = new Number($('.up',row).text());
					$('.up',row).text(price.toFixed(2));
					var qu = new Number($('.qu',row).text());
					var totalPrice = price * qu;
					totalPrice = totalPrice.toFixed(2);
					$('.itemPrice',row).text(totalPrice);
					
				},
				complete : function(rowCount){

					var totals = $.sumItems('.itemPrice');

					$('#total').text(totals['total']);
					$('#total_iva').text(totals['totalIva']);
					$('#total_revTax').text(totals['totalRevTax']);
					
					$('tr:even', this).addClass('rowHighlight');
					$("#tbl_Shop").trigger("update"); 
					
				}
			});


			$("#btn_print_detail").button({
				 icons: {
		        		primary: "ui-icon-print"
		        	}
				 })
	    		.click(function(e){

    				var shopId = gSelShopRow.attr('shopId');
    				var date = gSelShopRow.attr('dateForShop');
    				var op_name = gSelShopRow.attr('operatorName');
    				var op_uf = gSelShopRow.attr('operatorUf');
    			
    				
    				printWin = window.open('tpl/<?=$tpl_print_bill;?>?shopId='+shopId+'&date='+date+'&operatorName='+op_name+'&operatorUf='+op_uf);
    				printWin.focus();
    				printWin.print();
					
	    	});
			
		

			
			
			//uf select
			$('#uf_select').xml2html('init',{
				offSet	: 1,
				url 	: 'php/ctrl/UserAndUf.php',
				params 	: 'oper=getUfListing',
				loadOnInit:true
			//event listener to load items for this uf to validate
			}).change(function(){
		
				//get the id of the uf
				var uf_id = $("option:selected", this).val();
				if (uf_id < 1){
					$('#tbl_Shop tbody').xml2html('reload',{
						params : 'oper=getShopListing&filter=prev3Month'
					})
				} else {
				
					$('#tbl_Shop tbody').xml2html('reload',{
						params : 'oper=getShopListing&uf_id='+uf_id+'&filter=all'
					})
				}

			});			


			
			function switchTo(section){
				switch (section){
					case 'detail':
						$('.overviewElements').hide();
						$('.detailElements').fadeIn(1000);
						break;
	
					case 'overview':
						$('.detailElements').hide();
						$('.overviewElements').fadeIn(1000);
						break;
					}

			}


			
			
			/**
			 *	returns to order overview 
			 */
			var label = 'Overview';
			if (gBackTo != ''){
				label = 'Back to validate';
			}
			 
			$("#btn_overview").button({
				icons: {
		        		primary: "ui-icon-circle-arrow-w"
		        	},
        		label: label
				 })
	    		.click(function(e){

					if (gBackTo != ''){
						window.location.href = 'validate.php';
					} else {
						switchTo('overview'); 
					}
		    		

	    		});
			
			switchTo('overview');

			
	});  //close document ready
</script>

</head>
<?php flush(); ?>
<body>
<div id="wrap">
	<div id="headwrap">
		<?php include "php/inc/menu2.inc.php" ?>
	</div>
	<!-- end of headwrap -->
	
	
	
	<div id="stagewrap">
	
		
		<div id="titlewrap" class="ui-widget">
			<div id="titleLeftCol">
				<button id="btn_overview" class="floatLeft detailElements"><?php echo $Text['overview'];?></button>
		    	<h1 class="overviewElements"><?php echo $Text['ti_all_sales']; ?></h1>
		    	<h1 class="detailElements">Purchase details of cart #<span class="setCartId"></span></h1>
		    </div>
		    <div id="titleRightCol">
		    	<p class="floatLeft detailElements">Validated: <span class="ui-corner-all padding5x5 setValidateStatus"></span></p>
		    	<button id="btn_print_detail" class="detailElements floatRight">Print</button>
		    	<p class="textAlignRight overviewElements">
		    		<select id="uf_select">
		    			<option value="-10" selected="selected">Filter by Household</option>
		    			<option value="{id}">{id} {name}</option>
		    		</select>
		    	</p>
		    	
		    </div>
		</div>
	

	
				
        <div id="shop_list" class="ui-widget overviewElements">    
        	<div class="ui-widget-content ui-corner-all">
	        	<h3 class="ui-widget-header ui-corner-all">&nbsp;</h3>    
				<table id="tbl_Shop" class="tblListingDefault">
					<thead>
						<tr>
							<th></th>
							<th class="textAlignCenter clickable">Cart id <span class="ui-icon ui-icon-triangle-2-n-s floatLeft"></span></th>
							<th  class="clickable"><?php echo $Text['uf_long']; ?><span class="ui-icon ui-icon-triangle-2-n-s floatLeft"></span></th>
							<th  class="clickable"><p class="textAlignCenter">Date of purchase <span class="ui-icon ui-icon-triangle-2-n-s floatLeft"></span></p></th>
							<th><p class="textAlignCenter">Validated</p></th>
							<th><p class="textAlignRight">Total</p></th>
						</tr>
					</thead>
					<tbody>
						<tr class="clickable" id="shop_{id}" shopId="{id}" ufId={uf_id} dateForShop="{date_for_shop}" validated="{ts_validated}" operatorName="{operator_name}" operatorUf="{operator_uf}">
							<td></td>
							<td><p>{id}</p></td>
							<td><p>{uf_id} {uf_name}</p></td>
							<td><p class="textAlignCenter">{date_for_shop}</p></td>
							<td>{ts_validated}</td>
							<td><p class="textAlignRight">{purchase_total}€</p></td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="6">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="6">
								<!-- p class="textAlignCenter">
									<button id="btn_prevPurchase">Previous</button>&nbsp;&nbsp;Dates&nbsp;&nbsp;
									<button id="btn_nextPurchase">Next</button></p-->
								</td>
							
							
						</tr>
					</tfoot>
				</table>
			</div>
		</div>	
		
		
		<div id="shop_detail" class="ui-widget detailElements">
			<div class="ui-widget-content ui-corner-all">
				<h3 class="ui-widget-header">Purchase of HU<span class="setUfId"></span>, <span class="setShopDate"></span></h3>
				<table id="tbl_purchaseDetail" class="tblListingGrid" currentShopId="" currenShopDate="">
					<thead>
						<tr>
							
							<th><?php echo $Text['name_item'];?></th>	
							<th><?php echo $Text['provider_name'];?></th>					
							<th><p class="textAlignRight">Qu</p></th>
							<th><?php echo $Text['unit'];?></th>
							<th><p class="textAlignRight"><?php echo $Text['unit_price'];?></p></th>
							<th class="width-180"><p class="textAlignRight"><?php echo $Text['price'];?></p></th>
							
							
							
						</tr>
					</thead>
					<tbody>
						<tr class="detail_shop_{cart_id}">
							
							<td>{name}</td>
							<td>{provider_name}</td>
							<td><p class="textAlignRight qu">{quantity}</p></td>
							<td>{unit}</td>
							<td><p class="textAlignRight up">{unit_price}</p></td>
							<td><p class="textAlignRight itemPrice" iva="{iva_percent}" revTax="{rev_tax_percent}"></p></td>	

						</tr>						
					</tbody>
					<tfoot>
						<tr>
							<td colspan="5">included IVA</td>
							<td id="total_iva" class="dblBorderTop"></td>
						</tr>
						<tr>
						
							<td colspan="5">included RevTax</td>
							<td id="total_revTax"></td>
						</tr>
						<tr>
							
							<td colspan="5" class="boldStuff">Total</td>
							<td id="total" class="boldStuff dblBorderBottom"></td>
						</tr>
						<tr>
							<td colspan="6"><p>&nbsp;</p></td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>	
				
	
			
		
		
	</div>
	<!-- end of stage wrap -->
</div>
<!-- end of wrap -->
<!-- / END -->

</body>
</html>