<?php include "php/inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - "  ;?></title>


	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
  	
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/fgmenu/fg.menu.css"   />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/<?=$default_theme;?>/jqueryui.css"/>
	
	<?php if (isset($_SESSION['dev']) && $_SESSION['dev'] == true ) { ?> 
	    <script type="text/javascript" src="js/jquery/jquery.js"></script>
		<script type="text/javascript" src="js/jqueryui/jqueryui.js"></script>
		<script type="text/javascript" src="js/fgmenu/fg.menu.js"></script>
		<script type="text/javascript" src="js/aixadautilities/jquery.aixadaMenu.js"></script>     	 
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaXML2HTML.js" ></script>
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaUtilities.js" ></script>
	   	
   	<?php  } else { ?>
    	<script type="text/javascript" src="js/js_for_report_stock.min.js"></script>
    <?php }?>
	
   
	<script type="text/javascript">
	$(function(){

		//loading animation
		$('.loadSpinner').attr('src', "img/ajax-loader-<?=$default_theme;?>.gif"); 

		
		//load the stock value for given provider
		$('#tbl_stock_value tbody').xml2html('init',{
				url		: 'php/ctrl/Report.php',
				params	: 'oper=getStockValue', //would load all stock products
				loadOnInit: false,
				beforeLoad : function(){
					$('.loadSpinner').show();
				},
				complete : function(rowCount){
					$('.loadSpinner').hide();

					//calculate totals
					sumStockValue();					
				}
		});

		//hover function
		$('#tbl_stock_value tbody tr')
			.live('mouseenter', function(){
				$(this).addClass('ui-state-hover');
			})
			.live('mouseleave',function(){
					$(this).removeClass('ui-state-hover');
			})

		
		/**
		 * build Stock Provider SELECT
		 */
		$("#providerSelect").xml2html("init", {
			url: 'php/ctrl/ShopAndOrder.php',
			params : 'oper=getStockProviders',
			offSet : 1,
			loadOnInit:true,
			complete : function(){
			}
		}).change(function(){
				var provider_id = $("option:selected", this).val();
				var provider_name = $("option:selected", this).text();			
				$('#tbl_stock_value tbody').xml2html('removeAll');	
				$('.setProvider').text("");
						
				if (provider_id < 0) { return true;}

				$('.setProvider').text(provider_name);
				
	
				$('.loadAnimShop').show();
				$('#tbl_stock_value tbody').xml2html("reload",{
					params	: 'oper=getStockValue&provider_id='+provider_id					
				});		

				$("#tbl_stock_movements tbody").xml2html("reload", {
					params : 'oper=stockMovements&provider_id='+provider_id
				});
									
		}); //end select change


		$("#tbl_stock_movements tbody").xml2html("init", {
			url: 'php/ctrl/Shop.php',
			params : 'oper=stockMovements',
			loadOnInit:false,
			rowComplete : function (rowIndex, row){
				$.formatQuantity(row);
			},
			complete : function(rowCount){
				$('tr:even', this).addClass('rowHighlight');
				sumStockMovementsValue();
				
			}
		});

		$('#tbl_stock_movements tbody tr')
		.live('mouseenter', function(){
			$(this).addClass('ui-state-hover');
		})
		.live('mouseleave',function(){
				$(this).removeClass('ui-state-hover');
		})


		/**
		 *	if checkboxes are checked, then item is included in the overall 
		 *	brutto / netto stock value sum
		 */
		$('#toggleSumStock')
			.click(function(e){
				if ($(this).is(':checked')){
					$('input[name="sumStock"]').attr('checked','checked');
				} else {
					$('input[name="sumStock"]').attr('checked',false);
				}

				$('input[name=sumStock]').each(function(){
					toggleSumStock($(this).parents('tr'), $(this).is(':checked'));
				})
				sumStockValue();
			});

		$('input[name="sumStock"]')
			.live('click',function(e){
				toggleSumStock($(this).parents('tr'), $(this).is(':checked'));				
				sumStockValue();
			})


		/**
		 *	if stock movement is checked, then it is included in the overall
		 *	accumulated loss sum. 
		 */
		$('#toggleSumStockMovements')
			.click(function(e){
				if ($(this).is(':checked')){
					$('input:checkbox[name="sumStockMovements"]').each(function(){
							$(this).attr('checked','checked');
							var tds = $(this).parents('tr').children(); 
							tds.eq(7).children(':first-child').addClass('stockDeltaPriceCell');
						})
					
				} else {
					$('input:checkbox[name="sumStockMovements"]')
					.each(function(){
							$(this).attr('checked',false)
							var tds = $(this).parents('tr').children(); 
							tds.eq(7).children(':first-child').removeClass('stockDeltaPriceCell');
						})
					
				}
				sumStockMovementsValue();
			});


		$('input[name="sumStockMovements"]')
		.live('click',function(e){
			if ($(this).is(':checked')){
				$(this).parents('tr').children().eq(7).children(':first-child').addClass('stockDeltaPriceCell');
			} else {
				$(this).parents('tr').children().eq(7).children(':first-child').removeClass('stockDeltaPriceCell');
			}
			
			sumStockMovementsValue();
		})
		
				
		$('#stock_tabs').tabs();	



		function sumStockValue(){
			var tnetto = $.sumSimpleItems('.nettoCol');
			var tbrutto = $.sumSimpleItems('.bruttoCol');
			$('#nettoTotal').text(tnetto +'€');
			$('#bruttoTotal').text(tbrutto+'€');
			$('#tbl_stock_value tbody tr:even').addClass('rowHighlight'); 
		}


		function sumStockMovementsValue(){
			var acc_loss_ever = $.sumSimpleItems('.stockDeltaPriceCell');
			$('.setAccLossEver').text(acc_loss_ever+'€');
		}


		function toggleSumStock(seltr, checked){
			if (checked){
				seltr.children().eq(6).children(':first-child').addClass('nettoCol');
				seltr.children().eq(9).children(':first-child').addClass('bruttoCol');
			} else {
				seltr.children().eq(6).children(':first-child').removeClass('nettoCol');
				seltr.children().eq(9).children(':first-child').removeClass('bruttoCol');
			}
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
			<div id="titleLeftCol">
		    	<h1><?=$Text['ti_stock_report']; ?> <span class="setProvider"></span></h1>
		    </div>
		    <div id="titleRightCol">
		    	<select id="providerSelect" class="overviewElements floatRight">
                    	<option value="-1" selected="selected"><?php echo $Text['sel_provider']; ?></option>
                    	<option value="{id}"> {name}</option>                     
				</select>
    			<p class="floatRight detailElements"><?php echo $Text['stock_acc_loss_ever']; ?>: <span class="setAccLossEver ui-state-highlight aix-style-padding3x3 ui-corner-all">?</span></p>
		    </div>
		</div>
	
	
		
		<div id="stock_tabs" class="ui-widget">  
		
			<ul>
				<li><a href="#tabs-1"><h2><?=$Text['curStock'];?></h2></a></li>
				<li><a href="#tabs-2"><h2><?=$Text['ti_mgn_stock_mov'];?></h2></a></li>
			</ul>
		
		  
        	<div id="tabs-1" class="ui-widget-content ui-corner-all">
	        	<div class="ui-widget-content ui-corner-all">    
				<table id="tbl_stock_value" class="tblListingDefault">
					<thead>
						<tr>
							<th>&nbsp;<input type="checkbox" id="toggleSumStock" name="toggleSumStock" checked="checked"/></th>
							<th> <?php echo $Text['id'];?></th>
							<th><?php echo $Text['name_item'];?></th>
							<th><?php echo $Text['curStock'];?></th>
							<th><?php echo $Text['unit']; ?></th>
							<th><?php echo $Text['price_net']; ?></th>
							<th><p class="textAlignRight"><?php echo $Text['netto_stock']; ?></p></th>
							<th><?php echo $Text['iva'] ?></th>
							<th><?php echo $Text['revtax_abbrev']; ?></th>
							<th><p class="textAlignRight"><?php echo $Text['brutto_stock']; ?></p></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><input type="checkbox" name="sumStock" checked="checked"/></td>
							<td>{product_id}</td>
							<td>{name}</td>
							<td><p class="textAlignRight">{stock_actual}</p> </td>
							<td>{shop_unit}</td>
							<td><p class="textAlignRight">{unit_price}</p></td>
							<td><p class="nettoCol textAlignRight">{total_netto_stock_value}</p></td>
							<td>{iva_percent}%</td>
							<td>{rev_tax_percent}%</td>
							<td><p class="bruttoCol textAlignRight">{total_brutto_stock_value}</p></td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td class="boldStuff" colspan="2"><?php echo $Text['total_netto_stock']?>: <span id="nettoTotal"></span></td>
							<td></td>
							
							<td class="boldStuff" colspan="2"><?php echo $Text['total_brutto_stock']?>: <span id="bruttoTotal"></span></td></td>
						</tr>
					</tfoot>
				</table>
				</div>
			</div>
			
			<div id="tabs-2" class="ui-widget-content ui-corner-all">
			
				<div class="ui-widget-content ui-corner-all">
					<table id="tbl_stock_movements" class="tblListingDefault">
						<thead>
							<tr>
								<th>&nbsp;<input type="checkbox" checked="checked" id="toggleSumStockMovements" name="toggleSumStockMovements"/></th>
								<th>id</th>
								<th>Name</th>
								<th><?php echo $Text['operator'] ; ?></th>
								<th><?php echo $Text['description'] ; ?></th>
								<th><?php echo $Text['date']; ?></th>
								<th><p class="textAlignRight"><?php echo $Text['dff_qty']; ?></p></th>
								<th><p class="textAlignRight"><?php echo $Text['dff_price']; ?></p></th>
								<th><p class="textAlignRight"><?php echo $Text['balance']; ?></p></th>
								<th>Unit</th>
							</tr>
							
						</thead>
						<tbody>
							<tr>
								<td><input type="checkbox" name="sumStockMovements" checked="checked"/></td>
								<td>{product_id}</td>
								<td>{product_name}</td>
								<td>{member_name}</td>
								<td>{description}</td>
								<td class="stockDeltaTSCell">{ts}</td>
								<td class="stockDeltaQtyCell"><p class="textAlignRight formatQty">{amount_difference}</p></td>
								<td><p class="textAlignRight formatQty stockDeltaPriceCell">{delta_price}</p></td>
								<td><p class="textAlignRight formatQty">{resulting_amount}</p></td>
								<td>{unit}</td>
								
							</tr>
						
						</tbody>
						<tfoot>
							<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td><p class="textAlignRight boldStuff"><?php echo $Text['total']; ?>:</p></td>
							<td><span class="setAccLossEver boldStuff"></span></td>
							<td></td>
							<td></td>
							</tr>
						
						</tfoot>
					
					</table>
				
				
				</div>
			
			</div>
			
		</div>	
			
		
			
		
			
		
		
	</div>
	<!-- end of stage wrap -->
</div>
<!-- end of wrap -->
<!-- / END -->
</body>
</html>