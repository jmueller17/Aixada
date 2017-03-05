<?php include "php/inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " .$Text['ti_mng_stock'];?></title>
	
	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
  	<link rel="stylesheet" type="text/css"   media="print"  href="css/print.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/fgmenu/fg.menu.css"   />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/<?=$default_theme;?>/jqueryui.css"/>

    <script type="text/javascript" src="js/jquery/jquery.js"></script>
    <script type="text/javascript" src="js/jqueryui/jqueryui.js"></script>
    <?php echo aixada_js_src(); ?>
    
 	<script type="text/javascript" src="js/jqueryui/i18n/jquery.ui.datepicker-<?=$language;?>.js" ></script>
 		
 	
	<script type="text/javascript">
	
	$(function(){
		$.ajaxSetup({ cache: false });

		//coming from other page
		var gStockProvider = (typeof $.getUrlVar('stockProvider') == "string")? $.getUrlVar('stockProvider'):false;


		//go back to manage provider/products page
		var gBackTo = (typeof $.getUrlVar('lastPage') == "string")? $.getUrlVar('lastPage'):false;
		
	



		$('#product_list_provider tbody').xml2html("init");
		$('#product_list_provider tbody tr')
			.live('mouseover', function(){
				$(this).removeClass('highlight').addClass('ui-state-hover');
				
			})
			.live('mouseout',function(){
				$(this).removeClass('ui-state-hover');
				
			});
		
		$('.showStockMovement')
			.live('click',function(e){
				var pname = $(this).parents('tr').attr('productName');
				var pid = $(this).parents('tr').attr('productId');
				$('.setProductName').text(pname);
				
				
				$("#tbl_stock_movements tbody").xml2html("reload", {
					params : 'oper=stockMovements&product_id='+	pid
				});
				switchTo('detail');

			});


	
		/**
		 * build Provider SELECT
		 */
		$("#providerSelect").xml2html("init", {
			url: 'php/ctrl/ShopAndOrder.php',
			params : 'oper=getStockProviders',
			offSet : 1,
			loadOnInit:true,
			complete : function(){
				if (gStockProvider > 0){
					$("#providerSelect").val(gStockProvider);
					$("#providerSelect").trigger("change");
				}
			}
			
		}).change(function(){
				var provider_id = $("option:selected", this).val();					//get the id of the provider
				$('#product_list_provider tbody').xml2html('removeAll');			//empty the list
						
				if (provider_id < 0) { return true;}
	
				$('.loadAnimShop').show();
				$('#product_list_provider tbody').xml2html("reload",{
					url : 'php/ctrl/ShopAndOrder.php',
					params: 'oper=getShopProducts&provider_id='+provider_id+'&date=1234-01-01', //get stock only products, not all active ones
					complete : function (rowCount){
						$('.loadAnimShop').hide();
						if (rowCount == 0){
						$.showMsg({
							msg:"<?php echo $Text['msg_err_no_stock']; ?> ",
							type: 'info'});
						} 
							
					}						
				});							
		}); //end select change

		
		/**
		 *	product SEARCH functionality 
		 */
		$("#search").keyup(function(e){
					var minLength = 3; 						//search with min of X characters
					var searchStr = $("#search").val(); 
	
					$("#providerSelect").val('-1').attr('selected','selected');
					$('#searchTips').hide();
					
					if (searchStr.length >= minLength){
						
						$('.loadAnimShop').show();
					  	$('#product_list_provider tbody').xml2html("reload",{
							params: 'oper=getShopProducts&like='+searchStr+'&date=1234-01-01',
							complete : function(rowCount){
								if (rowCount == 0){
									$('#searchTips').show();
								}
								$('.loadAnimShop').hide();
							}						
						});	
					} else {					 
						$('#product_list_search tbody').xml2html("removeAll");				//delete all product entries in the table if we are below minLength;		
						
					}
			e.preventDefault();						//prevent default event propagation. once the list is build, just stop here. 		
		}); //end autocomplete


		
		$('.iconContainer')
		.live('mouseover', function(e){
			$(this).addClass('ui-state-hover');
		})
		.live('mouseout', function (e){
			$(this).removeClass('ui-state-hover');
		});




		/***********************************************************
		 *
		 *  STOCK DETAIL MOVEMENTS, incl. accumulated loss
		 *
		 ***********************************************************/



		$("#tbl_stock_movements tbody").xml2html("init", {
			url: 'php/ctrl/Shop.php',
			params : 'oper=stockMovements',
			loadOnInit:false,
			rowComplete : function (rowIndex, row){
				$.formatQuantity(row);
			},
			complete : function(rowCount){
				$('tr:even', this).addClass('rowHighlight');
				var acc_loss_ever = 0; 
				$('.stockDeltaPriceCell').each(function(){
					acc_loss_ever += parseFloat($(this).text());
				});
				
				$('.setAccLossEver').text(acc_loss_ever.toFixed(2)+"<?=$Text['currency_sign'];?>");


				if (rowCount == 0){
					$.showMsg({
						msg:"<?php echo $Text['msg_err_stock_mv']; ?>",
						type: 'warning'});

				}
				
			}
		});





		function switchTo(section){
			switch(section){

				case 'overview':
					$('.detailElements').hide();
					$('.overviewElements').fadeIn(1000);
					if (gStockProvider > 0) {
						$('.backElements').fadeIn(1000);
					}
					break;
	
				case 'detail':
					$('.overviewElements, .backElements').hide();
					$('.detailElements').fadeIn(1000);
					break;
			}

	

		}

		

		$("#btn_overview").button({
			icons: {
	        		primary: "ui-icon-circle-arrow-w"
	        	}
			 })
    		.click(function(e){
				switchTo('overview'); 
    		});

		$("#btn_back_products").button({
			icons: {
	        		primary: "ui-icon-circle-arrow-w"
	        	}
			 })
			.hide()
    		.click(function(e){
    			if (gBackTo != ''){
					window.location.href = 'manage_providers.php';
				}
    		});

		$('.btn_add_stock')
			.live('click',function(e){
				prepareStockForm('add',$(this).attr('stockActual'),$(this).attr('unit'), $(this).attr('productId'));  
			})
		
		
		$('.btn_correct_stock')
			.live('click',function(e){
				prepareStockForm('correct',$(this).attr('stockActual'),$(this).attr('unit'), $(this).attr('productId'));  
			})
			
			

		$('#dialog_edit_stock').dialog({
			autoOpen:false,
			width:480,
			height:400,
			modal:true,
			buttons: {  
				"<?=$Text['btn_save'];?>" : function(){
					
						if ($(this).data('info').edit == "add"){
							addStock($(this).data('info').id);
						} else if ($(this).data('info').edit == "correct"){
							correctStock($(this).data('info').id);
						}
					},
			
				"<?=$Text['btn_cancel'];?>"	: function(){
					$( this ).dialog( "close" );
					} 
			}
		});

	    
		$('#infoStockProductPage').hide();
		
		
		switchTo('overview');

		
		<?php include('js/aixadautilities/stock.js.php'); ?> 
							
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
						<button id="btn_overview" class="floatLeft detailElements"><?php echo $Text['overview'];?></button>
						<button id="btn_back_products" class="floatLeft btn_back backElements"><?php echo $Text['btn_back_products'];?></button>
				    	<h1 class="overviewElements"> <?php echo $Text['ti_mng_stock']; ?></h1>
				    	<h1 class="detailElements"> <?php echo $Text['ti_mgn_stock_mov']; ?></h1>
		    		</div>
		    		<div id="titleRightCol50">
						<select id="providerSelect" class="overviewElements">
	                    	<option value="-1" selected="selected"><?php echo $Text['sel_provider']; ?></option>
	                    	<option value="{id}"> {name}</option>                     
						</select>
						<p class="floatRight  overviewElements"><?php echo $Text['search_product'];?>: <input id="search" class="ui-corner-all"/></p>
		    			<p class="floatRight detailElements"><?php echo $Text['stock_acc_loss_ever']; ?>: <span class="setAccLossEver ui-state-highlight aix-style-padding3x3 ui-corner-all">?</span></p>
		    		</div>
				</div><!-- end titlewrap -->
 
				<div class="ui-widget aix-layout-center80 overviewElements">
					<div class="ui-widget-content ui-corner-all">
						<h3 class="ui-widget-header">&nbsp;</h3>
						<table id="product_list_provider" class="tblListingBorder" >
							<thead>
								<tr>
									<th class="textAlignRight"><?php echo $Text['id'];?></th>
									<th><?php echo $Text['name_item'];?></th>
									<th><?php echo $Text['provider_name']; ?></th>						
									<th><?php echo $Text['curStock']; ?></th>
									<th><?php echo $Text['unit'];?></th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<tr productId="{id}" productName="{name}">
									<td><p class="textAlignRight">{id}</p></td>
									<td class="clickable showStockMovement">{name}</td>
									<td><p class="textAlignCenter">{provider_name}</p></td>
									<td>
										<p class="textAlignRight setStockActual">{stock_actual}</p>
							
									</td>
									<td><p class="textAlignCenter">{unit}</p></td>
									<td>
										<p class="textAlignCenter"><a class="btn_add_stock" unit="{unit}" productId="{id}" stockActual="{stock_actual}" href="javascript:void(null)"><?php echo $Text['add_stock']; ?></a>&nbsp;&nbsp; | &nbsp;&nbsp;<a class="btn_correct_stock" productId="{id}" stockActual="{stock_actual}" unit="{unit}" href="javascript:void(null)"><?php echo $Text['correct_stock']; ?></a> </p>
									</td>
								</tr>						
							</tbody>
						</table>
					</div>
				</div>		
				
				<p>&nbsp;</p>
				<div class="ui-widget aix-layout-fixW250 aix-layout-centerDiv">
					<p id="searchTips" class="ui-widget-content ui-state-highlight infoblurp hidden"><?php echo $Text['no_results']; ?></p>
				</div>
				
				
				<div class="detailElements ui-widget">
					<div class="ui-widget-content ui-corner-all">
					<h4 class="ui-widget-header"><span class="setProductName"></span></h4>
					<table id="tbl_stock_movements" class="tblListingDefault">
						<thead>
							<tr>
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
								<td>{member_name}</td>
								<td>{description}</td>
								<td class="stockDeltaTSCell">{ts}</td>
								<td class="stockDeltaQtyCell"><p class="textAlignRight formatQty">{amount_difference}</p></td>
								<td class="stockDeltaPriceCell"><p class="textAlignRight formatQty">{delta_price}</p></td>
								<td><p class="textAlignRight formatQty">{resulting_amount}</p></td>
								<td>{unit}</td>
								
							</tr>
						
						</tbody>
					
					
					</table>
				
				
				</div>
				</div>
				

	</div>
	<!-- end of stage wrap -->
</div>
<!-- end of wrap -->


<div id="dialog_edit_stock">
<?php include('tpl/stock_dialog.php');?>
</div>

<!-- / END -->
</body>
</html>