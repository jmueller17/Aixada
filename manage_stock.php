<?php include "php/inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - ";?> Manage Stock</title>
	
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
   	<?php  } else { ?>
	   	<script type="text/javascript" src="js/js_for_manage_stock.min.js"></script>
    <?php }?>
   		
 	<script type="text/javascript" src="js/jqueryui/i18n/jquery.ui.datepicker-<?=$language;?>.js" ></script>
 		
 	
	<script type="text/javascript">
	
	$(function(){

	



		$('#product_list_provider tbody').xml2html("init");


	
		/**
		 * build Provider SELECT
		 */
		$("#providerSelect").xml2html("init", {
			url: 'php/ctrl/ShopAndOrder.php',
			params : 'oper=getStockProviders',
			offSet : 1,
			loadOnInit:true,
			
		}).change(function(){
				var provider_id = $("option:selected", this).val();					//get the id of the provider

				$('#product_list_provider tbody').xml2html('removeAll');	//empty the list
						
				if (provider_id < 0) { return true;}
	
				$('.loadAnimShop').show();
				$('#product_list_provider tbody').xml2html("reload",{
					params: 'oper=getShopProducts&provider_id='+provider_id,
					rowComplete : function(rowIndex, row){	//updates quantities for items already in cart
								
					},
					complete : function (rowCount){
						$('.loadAnimShop').hide();
						if (rowCount == 0){
						$.showMsg({
							msg:"This provider seems to have no stock products! ",
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
							params: 'oper=getShopProducts&like='+searchStr,
							rowComplete : function(rowIndex, row){	//updates quantities for items already in cart
								//formatRow(row);
							}, 
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


		//add stock 
		$('.inputAddStock')
			.live('focus', function(e){
				
				//hide other active field, buttons
				$('.correctStock').show();
				$('.inputCorrectStock').hide();
				$('.btn_save_new_stock .btn_correct_stock').hide();
				
				 //init and show the save button
				/*var btn = $(this).next();

				if (!btn.hasClass('exists')){
					btn.button({
						icons:{secondary:'ui-icon-disk'}
					})
					.click(function(e){
						var addQu = $.checkNumber($(this).prev(),'',3);	
						submitStock('addStock',$(this).attr('productId'),addQu);
					})
					 .addClass('exists')
					 .show();
				} else {
					btn.show();
				}*/
			})
			.live('keyup',function(e){
				//submit change on add stock
				if (e.keyCode == 13){
					
					//var absStock = $.checkNumber($(this),'',3);	
					var product_id = $(this).attr('productId'); 
					
					var addQu = $.checkNumber($(this),'',3);
					if (addQu >= 0){		
						submitStock('addStock',$(this).attr('productId'),addQu);
					} else {
						$.showMsg({
							msg: "Quantity needs to be numeric and bigger than 0!",
							buttons: {
								"<?=$Text['btn_ok'];?>":function(){						
									$(this).dialog("close");
								},
								"<?=$Text['btn_cancel'];?>" : function(){
									$( this ).dialog( "close" );
								}
							},
							type: 'warning'});
					}
					
				}

			})

		

		//correct stock
		$('.inputCorrectStock')
			.live('blur', function(e){
				$(this).toggle();
				$(this).prev().toggle();
			})
			.live('keyup', function(e){

				//submit change on enter
				if (e.keyCode == 13){
					
					var absStock = $.checkNumber($(this),'',3);	
					var product_id = $(this).attr('productId'); 
					
					if (absStock >= 0){
						$.showMsg({
							msg: "Adjusting stock this way should be the exception! New stock should always be ADDED. Are you sure to correct the stock for this product?",
							buttons: {
								"Yes, make correction!":function(){						
									submitStock('correctStock',product_id,absStock);
									$(this).dialog("close");
								},
								"<?=$Text['btn_cancel'];?>" : function(){
									$( this ).dialog( "close" );
								}
							},
							type: 'confirm'});
					
												
					} else {
						$.showMsg({
							msg: "Quantity needs to be numeric and bigger than 0!",
							buttons: {
								"<?=$Text['btn_ok'];?>":function(){						
									$(this).dialog("close");
								},
								"<?=$Text['btn_cancel'];?>" : function(){
									$( this ).dialog( "close" );
								}
							},
							type: 'warning'});
						return false; 
					}

						
				}

			})
			
			
		$('td.interactiveCell')
			.live('click',function(e){

				$('.correctStock').show();
				$('.inputCorrectStock').hide();
				$('.btn_save_new_stock, .btn_correct_stock').hide();
				
				$(this).children(':last').toggle().focus();
				$(this).children(':first').toggle();

				


			});
			

		/**
		 *	saves the stock correction / add to the database
		 * 	for "addStock" the current_stock = stock + quantity.
		 * 	for "correctStock" current_stock = quantity; 
		 */
		function submitStock(oper, product_id, quantity){

			var urlStr = 'php/ctrl/Shop.php?oper='+oper+'&product_id='+product_id+'&quantity='+quantity; 
			
			
			$.ajax({
				type: "POST",
				url: urlStr,
				beforeSend : function(){
					$('.inputAddStock, .inputCorrectStock')
						.attr('disabled','disabled')
						.val('Saving...');
					/*$('.btn_save_new_stock, .btn_correct_stock')
						.button('option','Saving...')
						.button('disable');*/
					
				},
				success: function(txt){
					/*$('#add_stock_'+product_id+ ', #correct_stock_'+product_id)
						.addClass('ui-state-success')
						.val('Ok!');*/

					setTimeout(function(){
						$('#product_list_provider tbody').xml2html("reload");						
					},500)

				},
				error : function(XMLHttpRequest, textStatus, errorThrown){
					$.showMsg({
						msg:XMLHttpRequest.responseText,
						type: 'error'});
					
				},
				complete: function(){
					$('.inputAddStock, .inputCorrectStock')
						.removeAttr('disabled')
						.val('');
					/*$('.btn_save_new_stock, .btn_correct_stock')
						.button('label','Save')
						.button('enable');	*/
				}
			});
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
					<div id="titleLeftCol50">
				    	<h1>Manage stock</h1>
		    		</div>
		    		<div id="titleRightCol50">
						<select id="providerSelect">
	                    	<option value="-1" selected="selected">Select product by provider...</option>
	                    	<option value="{id}"> {name}</option>                     
						</select>
						<p class="floatRight">Search product: <input id="search" class="ui-corner-all"/></p>
		    		</div>
				</div><!-- end titlewrap -->
 
				<div class="ui-widget aix-layout-center80">
					<div class="ui-widget-content ui-corner-all">
						<h3 class="ui-widget-header">&nbsp;</h3>
						<table id="product_list_provider" class="tblListingBorder" >
							<thead>
								<tr>
									<th class="textAlignRight"><?php echo $Text['id'];?></th>
									<th><?php echo $Text['name_item'];?></th>
									<th>Provider</th>						
									<th>Current stock</th>
									<th><?php echo $Text['unit'];?></th>
									<th class="width-280 textAlignLeft">Add stock</th>
								</tr>
							</thead>
							<tbody>
								<tr productId="{id}">
									<td><p class="textAlignRight">{id}</p></td>
									<td>{name}</td>
									<td><p class="textAlignCenter">{provider_name}</p></td>
									<td class="interactiveCell">
										<p class="textAlignRight correctStock">{stock_actual}</p>
										<input type="text" class="ui-corner-all inputCorrectStock hidden textAlignRight floatRight" value="{stock_actual}" size="5" productId="{id}" id="correct_stock_{id}" />
							
									</td>
									<td><p>{unit}</p></td>
									<td>
										<p class="floatLeft iconContainerNull"><span class="ui-icon ui-icon-plusthick"></span></p>
										&nbsp;<input class="ui-corner-all inputAddStock textAlignRight" value=""  productId="{id}" id="add_stock_{id}" size="5"/>
										&nbsp;&nbsp;&nbsp;<button class="btn_save_new_stock hidden" productId="{id}">Save</button>
									</td>
								</tr>						
							</tbody>
							<tfoot>
								<tr>
									<td colspan="3">&nbsp;</td>
									<td><p class="textAlignCenter dim80 ui-state-highlight ui-corner-all">Click cells to correct stock!</p></td>
									<td>&nbsp;</td>
									<td></td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>		
				
				<p>&nbsp;</p>
				<div class="ui-widget width-280 centerDiv">
					<p id="searchTips" class="ui-widget-content ui-state-highlight infoblurp hidden">The search produced no results!</p>
				</div>

	</div>
	<!-- end of stage wrap -->
</div>
<!-- end of wrap -->

<!-- / END -->
</body>
</html>