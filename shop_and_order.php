<?php include "inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " .  $Text['head_ti_'.strtolower($_REQUEST['what'])]; ?></title>
	
	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
  	<link rel="stylesheet" type="text/css"   media="print"  href="css/print.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/aixadacart/aixadacart.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/fgmenu/fg.menu.css"   />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/<?=$default_theme;?>/jqueryui.css"/>


	<?php if (isset($_SESSION['dev']) && $_SESSION['dev'] == true ) { ?> 
	    <script type="text/javascript" src="js/jquery/jquery.js"></script>
		<script type="text/javascript" src="js/jqueryui/jqueryui.js"></script>
		<script type="text/javascript" src="js/fgmenu/fg.menu.js"></script>
		<script type="text/javascript" src="js/aixadautilities/jquery.aixadaMenu.js"></script>     	 
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaXML2HTML.js" ></script>
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaUtilities.js" ></script>
	   	<script type="text/javascript" src="js/aixadacart/jquery.aixadacart.js" ></script>   	    	
   	<?php  } else { ?>
	   	<script type="text/javascript" src="js/js_for_shop_and_order.min.js"></script>
    <?php }?>
   		
 	<script type="text/javascript" src="js/jqueryui/i18n/jquery.ui.datepicker-<?=$language;?>.js" ></script>
 	<script type="text/javascript" src="js/aixadacart/i18n/cart.locale-<?=$language;?>.js" ></script>
 		
 	
	<script type="text/javascript">
	
	$(function(){

			
		
			//decide what to do in which section
			var what = $.getUrlVar('what');


			//detect form submit and prevent page navigation; we use ajax. 
			$('form').submit(function() { 
				// submit the form 
				$(this).ajaxSubmit(); 
		    	// return false to prevent normal browser submit
				return false; 
			});			

			//init tabs
			$("#tabs").tabs();


			//init cart 
			$('#cartLayer').aixadacart("init",{
				saveCartURL : 'ctrlShopAndOrder.php?what='+what+'&oper=commit',
				loadCartURL : 'shopAndOrder?what='+what,
				cartType	: (what=='Shop')? 'standalone':'standalone_preorder',
				btnType		: 'save',
				autoSave	: 5000,
				loadSuccess : updateCartLabel,
				submitComplete : updateCartLabel
			});

			
			//load favorites
			/*$('#cart_favorites_list').xml2html({
				url : 'smallqueries.php',
				params : 'oper=getFavorites', 
				rowName : 'cart', 
				offSet : 0
			});*/

			$('#product_list_provider tbody').xml2html("init");
			$('#product_list_category tbody').xml2html("init");
			$('#product_list_search tbody').xml2html("init");
			$('#product_list_preorder tbody').xml2html("init",{
					url: 'smallqueries.php',
					params : 'oper=getPreorderableProducts',
					loadOnInit : true
			});
			

			/**
			 * build Provider SELECT
			 */
			$("#providerSelect").xml2html("init", {
									offSet : 1
						}).change(function(){
							//get the id of the provider
							var id = $("option:selected", this).val(); 

							//empty the list
							$('#product_list_provider tbody').xml2html('removeAll');
							
							if (id < 0) {
								return true;
							}

							$('.loadAnimShop').show();
							$('#product_list_provider tbody').xml2html("reload",{
								//params: 'oper=listProducts&provider_id='+id+'&what='+what + "&date="+$.getSelectedDate('#datepicker'),
								params: 'oper=getProducts&provider_id='+id+'&what='+what + "&date="+$.getSelectedDate('#datepicker'),
								rowComplete : function(rowIndex, row){	//updates quantities for items already in cart
									var id =  $(row).attr("id"); 
									var qu = $("#cart_quantity_"+id).val();
									$("#quantity_"+id).val(qu);
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
			 * build Product Category SELECT
			 */
			$("#categorySelect").xml2html("init",{
					offSet : 1
				}).change(function(){
							//get the id of the provider
							var id = $("option:selected", this).val(); 
							$('#product_list_category tbody').xml2html('removeAll');
							
							if (id < 0) {	
								return true;
							} 

							$('.loadAnimShop').show();
							$('#product_list_category tbody').xml2html("reload",{
								params: 'oper=listProducts&category_id='+id+'&what='+what + "&date="+$.getSelectedDate('#datepicker'),
								rowComplete : function(rowIndex, row){	//updates quantities for items already in cart
									var id =  $(row).attr("id"); 
									var qu = $("#cart_quantity_"+id).val();
									$("#quantity_"+id).val(qu);
								},
								complete : function (rowCount){
									$('.loadAnimShop').hide();
								}						
							});							
			}); //end select change

			//dates available to make orders; start with dummy date
			var availableDates = ["2011-00-00"];

			
			$("#datepicker").datepicker({
						dateFormat 	: 'DD, d MM, yy',
						showAnim	: '',
						beforeShowDay: function(date){		//activate only those dates that are available for ordering. smallqueries.php order retrieval does not work...
							if (what == 'Order'){
								var ymd = $.datepicker.formatDate('yy-mm-dd', date);
								if ($.inArray(ymd, availableDates) == -1) {
								    return [false,"","Unavailable"];			    
								} else {
									  return [true, ""];
								}
							} else {
								 return [true, ""];
							}
						},
						onSelect 	: function (dateText, instance){
							refreshSelects($.getSelectedDate('#datepicker'));							
						}//end select
		
			}).show();//end date pick


	    	
			if (what == "Shop") { 
				//preorder tab is only available for ordering
				$('#tabs ul').children('li:gt(2)').hide();

				//hide date input field for shop
				$("#datepicker").hide();

				$.getAixadaDates('getToday', function (date){
					//availableDates = dates;
					$("#datepicker").datepicker('setDate', $.datepicker.parseDate('yy-mm-dd', date[0]));
					$("#datepicker").datepicker("refresh");
					refreshSelects(date[0]);
				});	

			} else {
			
				$.getAixadaDates('getAllOrderableDates', function (dates){
					availableDates = dates;
					$("#datepicker").datepicker('setDate', $.datepicker.parseDate('yy-mm-dd', availableDates[0]));
					$("#datepicker").datepicker("refresh");
					refreshSelects(dates[0]);
				});
			}
            
			//retrieve date for upcoming order
			/*$.ajax({
				type: "GET",
   			    url: date_url,
				dataType: "xml", 
				success: function(xml){
					var date = $(xml).find('date_for_order').text();
					//set the next date in the datepicker 
					$("#datepicker").datepicker('setDate', $.datepicker.parseDate('yy-mm-dd', date));

					//refresh the selects (providers, etc.) for th enew date
					refreshSelects(date);
				},
				error : function(XMLHttpRequest, textStatus, errorThrown){
					$.showMsg({
						msg:XMLHttpRequest.responseText,
						type: 'error'});
				}
			});*/ //end ajax retrieve date

			

		
			/*$.getOrderableDates('getDatesWithOrders', function (dates){
				datesWithOrders = dates;
				$("#datepicker").datepicker("refresh");
			});*/
			
			
			//make tabs widget resizeable
			/*$("#tabs").resizable({
						minHeight:400,
						resize: function() {
							//resizing tabs will update width of cart layer
							var nw = $('#stagewrap').innerWidth() - $('#leftCol').width() - 100;
							$('#rightCol').css('width', nw);		
						}	 
			});*/ //end resizable


			/**
			 *  show hide item list, cart or both
			*/
			var leftColWidth = $('#leftCol').innerWidth();
			var rightColWidth = $('#rightCol').innerWidth();
			$('#ViewChoice')
				.buttonset()
				.click(function(){
					var which = $("input[@name=viewCol]:checked").attr('id');

					if (which == "view_list"){
						$('#rightCol').hide();						
						$('#leftCol').css('width', $('#stagewrap').innerWidth()).show();
					} else if (which == "view_cart"){
						$('#leftCol').hide();						
						$('#rightCol').css('width', $('#stagewrap').innerWidth()).show();
					} else {
						$('#leftCol').css('width', leftColWidth).show();
						$('#rightCol').css('width', rightColWidth).show();						
					}
				});

			
			/**
			 *	product search functionality 
			 */
			$("#search").keyup(function(e){
						//search with min of X characters
						var minLength = 3; 
						
						//retrieve search input
						var searchStr = $("#search").val(); 
						
						if (searchStr.length >= minLength){
							$('.loadAnimShop').show();
						  	$('#product_list_search tbody').xml2html("reload",{
								params: 'oper=listProductsLike&what='+what+'&date='+$.getSelectedDate('#datepicker')+'&like='+searchStr,
								rowComplete : function(rowIndex, row){	//updates quantities for items already in cart
									var id =  $(row).attr("id"); 
									var qu = $("#cart_quantity_"+id).val();
									$("#quantity_"+id).val(qu);
								}, 
								complete : function(rowCount){
									$('.loadAnimShop').hide();
								}						
							});	
						} else {
							//delete all product entries in the table if we are below minLength; 
							$('#product_list_search tbody').xml2html("removeAll");						
							
						}
				//prevent default event propagation. once the list is build, just stop here. 		
				e.preventDefault();
			}); //end autocomplete
			

		

			//attach event listeners for the product input fields; change of quantity will put the 
			//item into the cart. 
			$('.product_list tbody').find("input").live("change", function (e){						
				
										//retrieve the current table row where quantity has been changed
										var row = $(this).parents("tr");
										
										//check if this is a preorder item
										var isPreorder = $(this).parents("tr").attr('preorder')? true:false;

										//don't add nonsense values
										var qu = $(this).val();
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
										
										//if quantity has changed, add it to the cart.
										$('#cartLayer').aixadacart("addItem",{
												id 				: $(row).attr("id"),
												isPreorder 		: isPreorder, 
												provider_name 	: $("td.item_provider_name", row).text(),
												name 			: $("td.item_name", row).text(),
												price 			: parseFloat($("td.item_price", row).text()),
												quantity 		: $(this).val(),
												unit 			: $("td.item_unit", row).text(),
												rev_tax_percent : parseFloat( $("td.item_rev_tax_percent", row).text())
			
										}); //end addItem to cart

										//sets nr of items in cart hide/view button
										updateCartLabel();
																		
			});//end event listener for product list 


			//if date gets changed, then selects need a refresh because providers available might change
			function refreshSelects(dateText){
				
				$('#cartLayer').aixadacart('loadCart',{
					loadCartURL		: 'ctrlShopAndOrder.php?oper=get'+what+'ItemsForDate&what='+what+'&date='+dateText,
					date 			: dateText
				}); //end loadCart

				$("#providerSelect").xml2html("reload", {
					params : 'oper=get'+what+'Providers&date='+dateText,
				})

				$("#categorySelect").xml2html("reload", {
					params : 'oper=listCategories&what='+what+'&date='+dateText,
				})

				$('#product_list_provider tbody').xml2html("removeAll");
				$('#product_list_category tbody').xml2html("removeAll");
				$('#product_list_search tbody').xml2html("removeAll");

			};

			//update the cart show/hide button
			function updateCartLabel (){
                            var nItems = $('#cartLayer').aixadacart('countItems');
                            var strItems = (nItems == 1) ? "<?=$Text['product_singular']?>" : "<?= $Text['product_plural']?>";
			    			var label = "<?=$Text['btn_view_cart'];?> ("+nItems+")";
							$( "#view_cart" ).button( "option", "label",label);
			}

			/**
			 *	product item info column. Constructs context menu for item 
			 */
			$(".product_info")
				.live("mouseenter", function(){
					$(this).parent().addClass('ui-state-hover');
					if (!$(this).attr("hasMenu")){
						//selected tab
						var selTab = $("#tabs").tabs('option', 'selected')
			
						var itemInfo = '<ul>';
						//only show stock if we buy; order has no stock
						if (what == 'Shop') itemInfo += '<li><?=$Text["curStock"];?>: ' + $(this).attr("stock");
						//if search tab, no sparklines					
						if (selTab != 2) itemInfo += '<li><?=$Text['items_bought'];?>: <span class="sparkvalues">'+$(this).attr("last_orders")+'</span><span class="spark"></span></li>';
						//add description of product
						itemInfo += '<li><?=$Text['description'];?>: '+$(this).attr("description")+'</li>';
						itemInfo += '</ul>';
	
						//init the context menu
						$(this).parent().menu({
							content: itemInfo,	
							width: 280,
							showSpeed: 50, 
							flyOut: false
						});
						
						$(this).attr("hasMenu", 1);			
					}
				})
				.live("mouseleave", function(){
					$(this).parent().removeClass('ui-state-hover');
				});

				/*$('.sparkvalues').live('mouseenter',function(){
						if (!$(this).attr("hasSpark")){
							$(this).sparkline();
							//var val = $(this).text();
							//$(this).next().sparkline(val);
							$(this).attr("hasSpark",1)
						}
				});*/

				$('.toggleShopDate').click(function(){
					$("#datepicker").toggle();
				});
							
	});  //close document ready
</script>

</head>
<?php flush(); ?>
<body>
<div id="wrap">
	<div id="headwrap">
		<?php include "inc/menu2.inc.php" ?>
	</div>
	<!-- end of headwrap -->
	
	
	<div id="stagewrap" class="ui-widget">
	
		<div id="titlewrap">
			<div id="titleLeftCol">
		    	<h1><?php if ($_REQUEST['what'] == 'Order') { 
		    					echo $Text['ti_order'];
		    				} else if ($_REQUEST['what'] == 'Shop') {
		    					echo $Text['ti_shop'];
		    					printf('<sup class="toggleShopDate" title="%s">(*)</sup>',$Text['show_date_field']);
		    				}?>
		    	&nbsp;&nbsp;&nbsp;<input  type="text" class="datePickerInput ui-widget-content ui-corner-all" id="datepicker"></h1>
    		</div>
    		<div id="titleRightCol">
    			<div id="ViewChoice">
					<input type="radio" id="view_list" name="viewCol" /><label for="view_list" title="<?php echo $Text['btn_view_list_lng'];?>"><?php echo $Text['btn_view_list'];?></label>
					<input type="radio" id="view_cart" name="viewCol"  /><label for="view_cart" title="<?php echo $Text['btn_view_cart_lng'];?>"><?php echo $Text['btn_view_cart'];?> (0)</label>
					<input type="radio" id="view_both" name="viewCol" checked="checked"/><label for="view_both" title="<?php echo $Text['btn_view_both_lng'];?>"><?php echo $Text['btn_view_both'];?></label>
				</div>
    		</div>
		</div><!-- end titlewrap -->

		<div id="leftCol">
		<div id="tabs">
			<ul>
				<li><a href="#tabs-1"><?php echo $Text['by_provider']; ?></a></li>
				<li><a href="#tabs-2"><?php echo $Text['by_category']; ?></a></li>
				<li><a href="#tabs-3"><?php echo $Text['search']; ?></a></li>
				<li><a href="#tabs-4"><?php echo $Text['special_offer']; ?></a></li>
			</ul>
					<span class="loadAnimShop floatRight hidden"><img src="img/ajax-loader.gif"/></span>
			<div id="tabs-1">
				<div class="wrapSelect">
					<select id="providerSelect" class="longSelect">
                    	<option value="-1" selected="selected"><?php echo $Text['sel_provider']; ?></option>
                    	<option value="{id}">{id} {name}</option>                     
					</select>

				</div>
				<div class="product_list_wrap">
					<table id="product_list_provider" class="product_list" >
						<thead>
							<tr>
								<th><?php echo $Text['id'];?></th>
								<th><?php echo $Text['info'];?></th>
								<th><?php echo $Text['name_item'];?></th>						
								<th><?php echo $Text['quantity'];?></th>
								<th><?php echo $Text['unit'];?></th>
								<th><?php echo $Text['revtax_abbrev'];?></th>
								<th><?php echo $Text['price'];?></th>
							</tr>
						</thead>
						<tbody>
							<tr id="{id}">
								<td class="item_it">{id}</td>
								<td class=""><span class="product_info ui-icon ui-icon-info" stock="{stock_actual}" last_orders="{last_orders}" description="{description}"></span></td>
								<td class="item_name">{name}</td>
								<td class="item_provider_name hidden">{provider_name}</td>
								<td class="item_quantity"><input  name="{id}" value="0.00" size="4" id="quantity_{id}"/></td>
								<td class="item_unit">{unit}</td>	
								<td class="item_rev_tax_percent">{rev_tax_percent}</td>	
								<td class="item_price">{unit_price}</td>			
							</tr>						
						</tbody>
					</table>
				</div>
			</div>
			<div id="tabs-2">
			 <div class="wrapSelect">
					<label for="categorySelect"></label>
					<select id="categorySelect" class="longSelect">
						<option value="-1" selected="selected"><?php echo $Text['sel_category']; ?></option>
                    	<option value="{id}">{description}</option>                 	
					</select>
				</div>
				<div class="product_list_wrap">
					<table id="product_list_category" class="product_list" >
						<thead>
						<tr>
							<th><?php echo $Text['id'];?></th>
							<th><?php echo $Text['info'];?></th>
							<th><?php echo $Text['name_item'];?></th>
							<th><?php echo $Text['provider_name'];?></th>
							<th><?php echo $Text['quantity'];?></th>
							<th><?php echo $Text['unit'];?></th>
							<th><?php echo $Text['revtax_abbrev'];?></th>
							<th><?php echo $Text['price'];?></th>
						</tr>
						</thead>
						<tbody>
							<tr id="{id}">
								<td class="item_it">{id}</td>
								<td class="item_stock"><span class="product_info ui-icon ui-icon-info" stock="{stock_actual}" last_orders="{last_orders}" description="{description}"></span></td>
								<td class="item_name">{name}</td>
								<td class="item_provider_name">{provider_name}</td>
								<td class="item_quantity"><input name="{id}" value="0.00"  size="4" id="quantity_{id}"/></td>
								<td class="item_unit">{unit}</td>	
								<td class="item_rev_tax_percent">{rev_tax_percent}</td>	
								<td class="item_price">{unit_price}</td>			
							</tr>						
						</tbody>
					</table>
				</div>
			</div>
			<div id="tabs-3">
				<div class="ui-widget">
                                 <label for="search"><?php echo $Text['search'];?></label>
						<input id="search" value="" class="ui-widget-content ui-corner-all"/>
				</div>
				
				<div class="product_list_wrap">
					<table id="product_list_search" class="product_list" >
						<thead>
						<tr>
							<th><?php echo $Text['id'];?></th>
							<th><?php echo $Text['info'];?></th>
							<th><?php echo $Text['name_item'];?></th>
							<th><?php echo $Text['provider_name'];?></th>
							<th><?php echo $Text['quantity'];?></th>
							<th><?php echo $Text['unit'];?></th>
							<th><?php echo $Text['revtax_abbrev'];?></th>
							<th><?php echo $Text['price'];?></th>
						</tr>
						</thead>
						<tbody>
							<tr id="{id}">
								<td class="item_it">{id}</td>
								<td class="item_stock"><span class="product_info ui-icon ui-icon-info" stock="{stock_actual}" description="{description}"></span></td>
								<td class="item_name">{name}</td>
								<td class="item_provider_name">{provider_name}</td>
								<td class="item_quantity"><input class="ui-widget-content ui-corner-all" name="{id}" value="0.00"  size="5" id="quantity_{id}"/></td>
								<td class="item_unit">{unit}</td>	
								<td class="item_rev_tax_percent">{rev_tax_percent}</td>	
								<td class="item_price">{unit_price}</td>			
							</tr>						
						</tbody>
					</table>
				</div>

			</div>
			<div id="tabs-4">
				
				<table id="product_list_preorder" class="product_list" >
						<thead>
							<tr>
								<th><?php echo $Text['id'];?></th>
								<th><?php echo $Text['info'];?></th>
								<th><?php echo $Text['provider_name'];?></th>
								<th><?php echo $Text['name_item'];?></th>
								<th><?php echo $Text['quantity'];?></th>
								<th><?php echo $Text['unit'];?></th>
								<th><?php echo $Text['revtax_abbrev'];?></th>
								<th><?php echo $Text['price'];?></th>
							</tr>
						</thead>
						<tbody>
							<tr id="{id}" preorder="true">
								<td class="item_it">{id}</td>
								<td class="item_stock"><span class="product_info ui-icon ui-icon-info" stock="{stock_actual}" last_orders="{last_orders}" description="{description}"></span></td>
								<td class="item_provider_name">{provider_name}</td>
								<td class="item_name">{name}</td>
								<td class="item_quantity"><input name="{id}" value="0.00"  size="4" id="quantity_{id}"/></td>
								<td class="item_unit">{unit}</td>	
								<td class="item_rev_tax_percent">{rev_tax_percent}</td>	
								<td class="item_price">{unit_price}</td>			
							</tr>						
						</tbody>
					</table>
				
				
			</div>
		</div><!-- end tabs -->
		</div><!-- end left Col -->
		
		<!-- Shopping cart starts -->
		<div id="rightCol">	
			<div id="cartLayer"></div>
		</div>
		
	</div>
	<!-- end of stage wrap -->
</div>
<!-- end of wrap -->

<!-- / END -->
</body>
</html>