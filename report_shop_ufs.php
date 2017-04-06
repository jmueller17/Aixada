<?php include "php/inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_sales'] ;?></title>

	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
  	<link rel="stylesheet" type="text/css"   media="print"  href="css/print.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/fgmenu/fg.menu.css"   />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/<?=$default_theme;?>/jqueryui.css"/>
    
    <script type="text/javascript" src="js/jquery/jquery.js"></script>
    <script type="text/javascript" src="js/jqueryui/jqueryui.js"></script>
    <?php echo aixada_js_src(); ?>
    <script type="text/javascript" src="js/aixadautilities/jquery.aixadaExport.js" ></script>

    <script type="text/javascript" src="js/tablesorter/jquery.tablesorter.js" ></script>
   		
 	<script type="text/javascript" src="js/jqueryui/i18n/jquery.ui.datepicker-<?=$language;?>.js" ></script>
 	   
   
	<script type="text/javascript">
	$(function(){
		$.ajaxSetup({ cache: false });

		//loading animation
		$('.loadSpinner').attr('src', "img/ajax-loader-<?=$default_theme;?>.gif").hide(); 
		

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
				beforeLoad : function(){
					$('.loadSpinner').show();
				},
				rowComplete : function(rowIndex, row){
					var validated = $(row).children().eq(4).text();

					if (validated == '0000-00-00 00:00:00'){
						$(row).children().eq(4).html("<p class='textAlignCenter'>-</p>");	
					} else {
						$(row).children().eq(4).html('<span class="ui-icon ui-icon-check tdIconCenter" title="<?php echo $Text['validated_at'];?>: '+validated+'"></span>');
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
					$('.loadSpinner').hide();
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
				var cd = $.getCustomDate($(this).attr('dateForShop'));
				$('.setShopDate').text(cd);

				var dateValidated = $(this).attr('validated');
				$('.setValidateStatus').removeClass('noRed okGreen');

				if (dateValidated == "0000-00-00 00:00:00"){
					$('.setValidateStatus').addClass('noRed').text('<?php echo $Text['not_yet_val']; ?>');
				} else {
					var opUf = $(this).attr('operatorUf'); 
					var opName = $(this).attr('operatorName'); 
					var dv = $.getCustomDate(dateValidated.substring(0,10),'DD M m, yy');
					$('.setValidateStatus')
						.addClass('okGreen')
						.html('<span title="<?php echo $Text['val_by'];?> '+opUf+' '+opName+'">'+dv+'</span>');
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
				beforeLoad : function(){
					$('.loadSpinner').show();
				},
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
					$('.loadSpinner').hide();
				}
			});



			//print incidents accoring to current incidents template in new window or download as pdf
			$("#btn_print")
			.button({
				icons: {
					primary: "ui-icon-print",
		        	secondary: "ui-icon-triangle-1-s"
				}
		    })
		    .menu({
				content: $('#printOptionsItems').html(),	
				showSpeed: 50, 
				width:180,
				flyOut: true, 
				itemSelected: function(item){	
					
					var link = $(item).attr('id');

					var shopId = gSelShopRow.attr('shopId');
    				var date = gSelShopRow.attr('dateForShop');
    				var op_name = gSelShopRow.attr('operatorName');
    				var op_uf = gSelShopRow.attr('operatorUf');
    			
    				
					
					switch (link){
						case "printWindow": 
							var printWin = window.open('tpl/<?=$tpl_print_bill;?>?shopId='+shopId+'&date='+date+'&operatorName='+op_name+'&operatorUf='+op_uf);
			    			
							printWin.focus();
							printWin.print();
							break;
		
						case "printPDF": 
							window.frames['dataFrame'].window.location = 'tpl/<?=$tpl_print_bill;?>?shopId='+shopId+'&date='+date+'&operatorName='+op_name+'&operatorUf='+op_uf+'&asPDF=1&outputFormat=D' 
							break;
					}
									
				}//end item selected 
			});//end print menu
	    	
		
			
			
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



			
			//EXPORT STUFF
			$('#dialog_export_options').dialog({
				autoOpen:false,
				width:520,
				height:500,
				buttons: {  
					"<?=$Text['btn_ok'];?>" : function(){
						exportCart(); 
					},
					"<?=$Text['btn_close'];?>"	: function(){
						$( this ).dialog( "close" );
					} 
				}
			});


			$('#btn_export')
				.button({
					icons: {
						primary: "ui-icon-transferthick-e-w"
			    	}
				})
				.click(function(e){
					$('#dialog_export_options')
						.dialog("open");
				 })
				 .hide(); 

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

			//export cart
			function exportCart(){
				var frmData = checkExportForm(); 
				if (frmData){
					var urlStr = "php/ctrl/ImportExport.php?oper=exportCart&shopId="+gSelShopRow.attr('shopId')+"&" + frmData; 
					//load the stuff through the export channel
					$('#exportChannel').attr('src',urlStr);
				}
			}



			
			/**********************************
			 *
			 *  SWITCHING STUFF 
			 *
			 **********************************/
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
		<?php include "php/inc/menu.inc.php" ?>
	</div>
	<!-- end of headwrap -->
	
	
	
	<div id="stagewrap">
	
		
		<div id="titlewrap" class="ui-widget">
			<div id="titleLeftCol">
				<button id="btn_overview" class="floatLeft detailElements"><?php echo $Text['overview'];?></button>
		    	<h1 class="overviewElements"><?php echo $Text['ti_all_sales']; ?></h1>
		    	<h1 class="detailElements"><?php echo $Text['purchase_details']; ?><span class="setCartId"></span></h1>	
		    	<p class="floatRight detailElements"><?php echo $Text['validated'];?>: <span class="ui-corner-all padding5x5 setValidateStatus"></span></p>
		    
		    </div>
		    <div id="titleRightCol">		    	
		    	<button id="btn_print" class="detailElements btn_right"><?=$Text['printout'];?></button>
		    		<div id="printOptionsItems" class="hidden hideInPrint">
					<ul>
					 <li><a href="javascript:void(null)" id="printWindow"><?=$Text['print_new_win'];?></a></li>
					 <li><a href="javascript:void(null)" id="printPDF"><?=$Text['print_pdf'];?></a></li>
					</ul>
					</div>	
				<button id="btn_export" class="detailElements floatRight"><?php echo $Text['btn_export']; ?></button>
					
		    	<p class="textAlignRight overviewElements">
		    		<select id="uf_select">
		    			<option value="-10" selected="selected"><?php echo $Text['filter_uf']; ?></option>
		    			<option value="{id}">{id} {name}</option>
		    		</select>
		    	</p>
		    </div>
		</div>
	

	
				
        <div id="shop_list" class="ui-widget overviewElements">    
        	<div class="ui-widget-content ui-corner-all">
	        	<h3 class="ui-widget-header ui-corner-all">&nbsp;<span style="float:right; margin-top:-4px;"><img class="loadSpinner" src="img/ajax-loader.gif"/></span></h3>    
				<table id="tbl_Shop" class="tblListingDefault">
					<thead>
						<tr>
							<th></th>
							<th class="textAlignCenter clickable">Cart id <span class="ui-icon ui-icon-triangle-2-n-s floatLeft"></span></th>
							<th  class="clickable"><?php echo $Text['uf_long']; ?><span class="ui-icon ui-icon-triangle-2-n-s floatLeft"></span></th>
							<th  class="clickable"><p class="textAlignCenter"><?php echo $Text['purchase_date'];?> <span class="ui-icon ui-icon-triangle-2-n-s floatLeft"></span></p></th>
							<th><p class="textAlignCenter"><?php echo $Text['validated'];?></p></th>
							<th><p class="textAlignRight"><?php echo $Text['total'];?></p></th>
						</tr>
					</thead>
					<tbody>
						<tr class="clickable" id="shop_{id}" shopId="{id}" ufId={uf_id} dateForShop="{date_for_shop}" validated="{ts_validated}" operatorName="{operator_name}" operatorUf="{operator_uf}">
							<td></td>
							<td><p>{id}</p></td>
							<td><p>{uf_id} {uf_name}</p></td>
							<td><p class="textAlignCenter">{date_for_shop}</p></td>
							<td>{ts_validated}</td>
							<td><p class="textAlignRight">{purchase_total}<?=$Text['currency_sign'];?></p></td>
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
				<h3 class="ui-widget-header"><?php echo $Text['purchase_uf']; ?><span class="setUfId"></span>, <span class="setShopDate"></span><span style="float:right; margin-top:-4px;"><img class="loadSpinner" src="img/ajax-loader.gif"/></span></h3>
				<table id="tbl_purchaseDetail" class="tblListingGrid" currentShopId="" currenShopDate="">
					<thead>
						<tr>
							
							<th><?php echo $Text['name_item'];?></th>	
							<th><?php echo $Text['provider_name'];?></th>					
							<th><p class="textAlignRight"><?php echo $Text['quantity_short']; ?></p></th>
							<th><?php echo $Text['unit'];?></th>
							<th><p class="textAlignRight"><?php echo $Text['revtax_abbrev']; ?></p></th>
							<th><p class="textAlignRight"><?php echo $Text['iva']; ?></p></th>
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
							<td><p class="textAlignRight">{rev_tax_percent}%</p></td>
							<td><p class="textAlignRight">{iva_percent}%</p></td>
							<td><p class="textAlignRight up">{unit_price}</p></td>
							<td><p class="textAlignRight itemPrice" iva="{iva_percent}" revTax="{rev_tax_percent}"></p></td>	

						</tr>						
					</tbody>
					<tfoot>
						<tr>
							
							<td colspan="7" class="boldStuff"><?php echo $Text['total'];?></td>
							<td id="total" class="boldStuff dblBorderBottom dblBorderTop"></td>
						</tr>
						
						<tr>
							<td colspan="7"><?php echo $Text['incl_iva']; ?></td>
							<td id="total_iva" class="dblBorderTop"></td>
						</tr>
						<tr>
						
							<td colspan="7"><?php echo $Text['incl_revtax']; ?></td>
							<td id="total_revTax"></td>
						</tr>

						<tr>
							<td colspan="8"><p>&nbsp;</p></td>
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
<iframe name="dataFrame" style="display:none"></iframe>
<iframe id="exportChannel" src="" style="display:none; visibility:hidden;"></iframe>
<div id="dialog_export_options" title="<?php echo $Text['export_options']; ?>">
<?php include("tpl/export_dialog.php");?>
</div>
</body>
</html>