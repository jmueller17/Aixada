<?php include "inc/header.inc.php" ?>
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
	   	<script type="text/javascript" src="js/jeditable/jquery.jeditable.mini.js" ></script>
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
			url: 'ctrlShopAndOrder.php',
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
							msg:"<?php echo $Text['msg_no_active_products'];?>",
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


	

		//attach event listeners for the product input fields; change of quantity will put the 
		//item into the cart. 
		$('.product_list tbody')
			.find("input")
			.live("change", function (e){						
				var row = $(this).parents("tr");			
	
				var qu = $(this).val();													//don't add nonsense values
				$(this).val(parseFloat(qu.replace(",",".")));
	
				if (isNaN($(this).val())) {
					var $this = $(this);
					$(this).addClass("ui-state-error");
					$(this).effect('pulsate',{},100, function callback(){
							var nv = new Number(0);
							$this.val(nv.toFixed(2));
							$this.removeClass("ui-state-error");
						});
					return false;
				}
				
																				
		});//end event listener for product list 


		$('.iconContainer')
			.live('mouseover', function(e){
				$(this).addClass('ui-state-hover');
			})
			.live('mouseout', function (e){
				$(this).removeClass('ui-state-hover');
			});

		$('.btn_correct_stock')
			.live('click', function(e){

			})

		$('td.interactiveCell')
			.live('mouseover', function(e){						//make each cell editable on mouseover. 
				var pid = $(this).parent().attr('productId');
				
				if (!$(this).hasClass('editable')){
					$(this).children(':first')
						.addClass('editable')
						.editable('ctrlShop.php', {			//init the jeditable plugin
								submitdata : {
									product_id : pid
									},
								id 		: 'oper',
								name 	: 'current_stock',
								indicator: 'Saving',
							    tooltip	: 	'click to edit!',
							    cssclass : 'inputTxtSmall',
								callback: function(value, settings){
									$('#product_list_provider tbody').xml2html("reload");
									//alert(value);
									//$(this).parent().removeClass('toRevise').addClass('revised');
								} 
						});	
				}
		
			});
							
	});  //close document ready
</script>

</head>
<body>
<div id="wrap">
	<div id="headwrap">
		<?php include "inc/menu2.inc.php" ?>
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
 
				<div class="ui-widget">
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
									<th>Correct stock</th>
									<th>Add stock</th>
								</tr>
							</thead>
							<tbody>
								<tr productId="{id}">
									<td><p class="textAlignRight">{id}</p></td>
									<td>{name}</td>
									<td>{provider_name}</td>
									<td class="interactiveCell"><p class="textAlignRight" id="correctStock">{stock_actual}</p></td>
									<td><p>{unit}</p></td>
									<td><p class="ui-corner-all ui-state-default iconContainer btn_correct_stock" productId={id}><span class="ui-icon ui-icon-pencil"></span></p></td>
									<td><p class="floatLeft iconContainerNull"><span class="ui-icon ui-icon-plusthick"></span></p>&nbsp;<input class="ui-corner-all" name="{id}" value="" size="4" id="add_stock_{id}"/></td>
								</tr>						
							</tbody>
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