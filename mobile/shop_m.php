<?php include "inc/header.inc.php" ?>
<!DOCTYPE html> 
<html> 
	<head> 
		<title><?=$Text['global_title']; ?></title> 
		<meta name="viewport" content="width=device-width, initial-scale=1"> 
		<link rel="stylesheet" href="js/jquery.mobile-1.0.1/jquery.mobile-1.0.1.min.css" />
		<link rel="stylesheet"  href="js/jquery.mobile-1.0.1/jquery.ui.datepicker.mobile.css" /> 
		<!-- link rel="stylesheet" type="text/css"   media="screen" href="js/aixadacart/aixadacart.css" /-->
    
		<script src="js/jquery/jquery-1.7.1.min.js"></script>
		<script src="js/jquery.mobile-1.0.1/jquery.mobile-1.0.1.min.js"></script>
		
		<script src="js/jquery.mobile-1.0.1/jQuery.ui.datepicker.js"></script>
		<script src="js/jquery.mobile-1.0.1/jquery.ui.datepicker.mobile.js"></script>
		
		<script type="text/javascript" src="js/jquery/jquery.aixadaXML2HTML.js" ></script>
	   	<script type="text/javascript" src="js/jquery/jquery.aixadaUtilities.js" ></script>
	   	<script type="text/javascript" src="js/aixadacart/jquery.aixadacart.mobile.js" ></script> 
	   	<script type="text/javascript" src="js/aixadacart/i18n/cart.locale-<?php echo $language; ?>.js" ></script>
	  	<!-- link rel="stylesheet" type="text/css"   media="screen" href="css/jquery-ui/ui-lightness/jquery-ui-1.8.custom.css"/-->
	

	<script>

	//intercept page changes
	$(document).bind( "pagebeforechange", function( e, data ) {
		
	});

	
		$(document).delegate('#shop', 'pageinit', function(){
			$('#cartLayer').aixadacart("init",{
				saveCartURL : 'php/ctrl/ShopAndOrder.php?what=Shop&oper=commit',
				loadCartURL : 'shopAndOrder?what=Shop',
				cartType	: 'simple',
				btnType		: 'save',
				loadSuccess : function(){
				
					$('.deleteStuff').button();
				},
				//submitComplete : updateCartLabel
			});

			/*$("#datepicker").datepicker({
				dateFormat 	: 'DD, d MM, yy',
				showAnim	: '',
				beforeShowDay: function(date){		
						 return [true, ""];						
				},
				onSelect 	: function (dateText, instance){
					alert(1);
					//refreshSelects(getSelectedDate());							
				}//end select

			});*/

			$("#datepicker").change(function(){
				alert(1);
			});

			$.ajax({
				type: "GET",
				url: "smallqueries.php?oper=getNextEqualShopDate",
				dataType: "xml", 
				success: function(xml){
					var date = $(xml).find('date_for_order').text();
					//set the next date in the datepicker 
					$("#datepicker").datepicker('setDate', $.datepicker.parseDate('yy-mm-dd', date));

					$(".date-nav-button").html(date);
					//$( ".date-nav-button" ).button( "option", "label",date);

					//refresh the selects (providers, etc.) for th enew date
					//refreshSelects(date);
				},
				error : function(XMLHttpRequest, textStatus, errorThrown){
					$.showMsg({
						msg:XMLHttpRequest.responseText,
						type: 'error'});
				}
			}); //end ajax retrieve date
		
	 	}); 


		$( document ).delegate("#cart", "pageinit", function() {
			$('#cartLayer').aixadacart('loadCart',{
				loadCartURL		: 'php/ctrl/ShopAndOrder.php?oper=getShopItemsForDate&what=Shop&date=2012-04-04',
				date 			: '2012-04-04'
			}); //end loadCart
		});

		

		
		
		$( document ).delegate("#by_provider", "pageinit", function() {
			var what = "Shop";
			//alert(1);
			$("#providerSelect").xml2html("init", {
					params : 'oper=listProviders&what=Shop&date=2012-04-04',	
					loadOnInit:true,
			});

			$('#product_list tbody').xml2html("init",{
				url         	: 'php/ctrl/ShopAndOrder.php'					
			});
		});

		
		$( document ).delegate("#by_category", "pageinit", function() {
			//alert(2);
			$("#categorySelect").xml2html("init", {
					params : 'oper=listCategories&what=Shop&date=2012-04-04',	
					loadOnInit:true,
			});

			$('#product_list tbody').xml2html("init",{
				url         	: 'php/ctrl/ShopAndOrder.php'					
			});
		});


		$( document ).delegate("#by_search", "pageinit", function() {
			//alert(2);

			$('#product_list_search tbody').xml2html("init");
		
			$("#search").keyup(function(e){
						//search with min of X characters
						var minLength = 3; 
						
						//retrieve search input
						var searchStr = $("#search").val(); 
						
						if (searchStr.length >= minLength){
							//$('.loadAnimShop').show();
						  	$('#product_list_search tbody').xml2html("reload",{
								params: 'oper=listProductsLike&what=Shop&date=2012-04-04&like='+searchStr,
								rowComplete : function(rowIndex, row){	//updates quantities for items already in cart
									var id =  $(row).attr("id"); 
									var qu = parseFloat($("#cart_quantity_"+id).val());
									if (qu > 0){
										$("#quantity_"+id).val(qu.toFixed(3));
									}
									
								}, 
								complete : function(rowCount){
									//$('.loadAnimShop').hide();
									
									
								}						
							});	
						} else {
							//delete all product entries in the table if we are below minLength; 
							$('#product_list_search tbody').xml2html("removeAll");						
							
						}
				//prevent default event propagation. once the list is build, just stop here. 		
				e.preventDefault();
			}); //end autocomplete

		});


		
		//attach event listeners for the product input fields; change of quantity will put the 
		//item into the cart. 
		$('.productListing tbody').find("input").live("change", function (e){						
									
									//retrieve the current table row where quantity has been changed
									var row = $(this).parents("tr");
									
									//check if this is a preorder item
									var isPreorder = $(this).parents("tr").attr('preorder')? true:false;

									//don't add nonsense values
									var qu = $(this).val();
									$(this).val(parseFloat(qu.replace(",",".")));

									if (isNaN($(this).val())) {
										//$(this).val(0);
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
									$('#cartLayer').aixadacart("saveCart");
									//sets nr of items in cart hide/view button
									//updateCartLabel();
																	
		});//end event listener for product list 

		
		
		
		$('a.providerLink').live('click', function(){
			var prov_id = $(this).attr('provider_id');
			
			if (isNaN(prov_id) || prov_id < 0) return false; 

			$('#product_list tbody').xml2html("removeAll");
			$.mobile.changePage("#products");
			
			$('#product_list tbody').xml2html("reload",{
				params: 'oper=listProducts&provider_id='+prov_id+'&what=Shop&date=2012-04-04',
				rowComplete : function(rowIndex, row){	//updates quantities for items already in cart
					var id =  $(row).attr("id"); 
					var qu = parseFloat($("#cart_quantity_"+id).val());
					if (qu > 0){
						$("#quantity_"+id).val(qu.toFixed(3));
					}
				},	
				complete : function(rowCount){
					//$('#products').page();
				}
			});		
		});

		
		$('a.categoryLink').live('click', function(){
			var category_id = $(this).attr('category_id');
			
			if (isNaN(category_id) || category_id < 0) return false; 

			$('#product_list tbody').xml2html("removeAll");
	
			$.mobile.changePage("#products");
			
			$('#product_list tbody').xml2html("reload",{
				params: 'oper=listProducts&category_id='+category_id+'&what=Shop&date=2012-04-04',
				rowComplete : function(rowIndex, row){	//updates quantities for items already in cart
					var id =  $(row).attr("id"); 
					var qu = parseFloat($("#cart_quantity_"+id).val());
					if (qu > 0){
						$("#quantity_"+id).val(qu.toFixed(3));
					}
				}						
			});		
		});
		


		/**
		 *	Datepicker stuff
		 */
		//dates available to make orders; start with dummy date
		var availableDates = ["2011-00-00"];

		//dates that are orderable and have already items -> need moving, cannot be deleted
		var datesWithOrders = ["2011-00-00"];
		
		

		
 		

		
	
	</script>

</head> 
<body> 

<div data-role="page" id="shop" data-title="Aixada - Buy stuff">
	<div data-role="header" class="ui-bar ui-grid-c" >
		<div class="ui-block-a"><a href="index_m.php" data-role="button" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a></div>
		<div class="ui-block-b"><h1>Buy stuff </h1></div>	
		<div class="ui-block-c"><a href="#pick_date" data-role="button" data-mini="true" title="Change date" data-theme="b" class="date-nav-button">2004-23-12</a></div>	
		<div class="ui-block-d"><a href="#cart" data-icon="cart" data-role="button" style="float:right; margin-right:25px;" data-theme="a">View cart</a></div>		
	</div><!-- /header -->

	<div data-role="content" data-inset="true">	
		<ul data-role="listview">
			<li><a href="#by_provider">... by provider</a></li>
			<li><a href="#by_category">... by category</a></li>
			<li><a href="#by_search">... or by searching</a></li>
		</ul>
	
	</div><!-- /content -->
</div><!-- /page main-->



<div data-role="page" id="pick_date" data-title="Aixada - Buy stuff - Change date">
	<div data-role="header">
		<a href="#shop" data-icon="arrow-l" data-iconpos="notext" data-direction="reverse">Back</a>			
		<h1>Change date</h1>
	</div><!-- /header -->

	<div data-role="content">
		<div data-role="fieldcontain">
	     	    <label for="datepicker">Date: </label>
	     	    <input type="date" id="datepicker" value=""  />
		</div>	
	</div>
	
</div><!-- /page date -->



<div data-role="page" id="by_provider" data-title="Aixada - Buy stuff - By Provider">
	<div data-role="header" class="ui-bar ui-grid-c" >
		<div class="ui-block-a"><a href="#shop" data-role="button" data-icon="arrow-l" data-iconpos="notext" data-rel="back">Back</a></div>
		<div class="ui-block-b"><h1>Buy stuff by provider</h1></div>	
		<div class="ui-block-c"><a href="#pick_date" data-role="button" title="Change date" data-theme="b" class="date-nav-button">2004-23-12</a></div>	
		<div class="ui-block-d"><a href="#cart" data-icon="cart" data-role="button" style="float:right; margin-right:25px;" data-theme="a">View cart</a></div>		
	</div><!-- /header -->

	<div data-role="content" data-inset="true">	
		<ul id="providerSelect" data-role="listview"> 
           <li><a href="#" class="providerLink" provider_id="{id}"> {name}</a></li>                     
		</ul>

	</div><!-- /content -->
</div><!-- /page by provider -->


<div data-role="page" id="by_category" data-title="Aixada - Buy stuff - By Category">
	<div data-role="header" class="ui-bar ui-grid-c" >
		<div class="ui-block-a"><a href="#shop" data-role="button" data-icon="arrow-l" data-iconpos="notext" data-rel="back">Back</a></div>
		<div class="ui-block-b"><h1>Buy stuff by category </h1></div>	
		<div class="ui-block-c"><a href="#pick_date" data-role="button" title="Change date" data-theme="b" class="date-nav-button">2004-23-12</a></div>	
		<div class="ui-block-d"><a href="#cart" data-icon="cart" data-role="button" style="float:right; margin-right:25px;" data-theme="a">View cart</a></div>		
	</div><!-- /header -->

	<div data-role="content" data-inset="true">	
		<ul id="categorySelect" data-role="listview"> 
           <li><a href="#" class="categoryLink" category_id="{id}"> {description}</a></li>                     
		</ul>

	</div><!-- /content -->
</div><!-- /page by category-->



<div data-role="page" id="by_search" data-title="Aixada - Buy stuff - By Category">
	<div data-role="header" class="ui-bar ui-grid-c" >
		<div class="ui-block-a"><a href="index_m.php" data-role="button" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a></div>
		<div class="ui-block-b"><h1>Search for products </h1></div>	
		<div class="ui-block-c"><a href="#pick_date" data-role="button" title="Change date" data-theme="b" class="date-nav-button">2004-23-12</a></div>	
		<div class="ui-block-d"><a href="#cart" data-icon="cart" data-role="button" style="float:right; margin-right:25px;" data-theme="a">View cart</a></div>		
	</div><!-- /header -->

	<div data-role="content" data-inset="true">	
		<ul data-role="listview">
			<li data-role="fieldcontain">
	        	<label for="name">Search: </label>
	        	<input type="search" name="search" id="search" value=""  />
			</li>
		</ul>
		<p><br/></p>
		<table id="product_list_search" class="productListing" width="100%">
						<thead>
							<tr>
								<th><?php echo $Text['name_item'];?></th>						
								<th><?php echo $Text['provider_name'];?></th>
								<th><?php echo $Text['quantity'];?></th>
								<th><?php echo $Text['unit'];?></th>							
								<th><?php echo $Text['price'];?></th>
							</tr>
						</thead>
						<tbody>
							<tr id="{id}">
								<td class="item_name">{name}</td>
								<td class="item_provider_name">{provider_name}</td>
								<td class="item_quantity"><input  name="{id}" value="0.00" size="4" id="quantity_{id}"/></td>
								<td class="item_unit">{unit}</td>	
								<td class="item_price">{unit_price}</td>			
							</tr>						
						</tbody>
						
						
		</table>
	
	</div><!-- /content -->
</div><!-- /page by category-->
		


<div data-role="page" id="products" data-title="Aixada - Buy stuff - Products">
  <div data-role="header" class="ui-bar ui-grid-c" >
		<div class="ui-block-a"><a href="#shop" data-role="button" data-icon="arrow-l" data-iconpos="notext" data-rel="back">Back</a></div>
		<div class="ui-block-b"><h1>Buy stuff </h1></div>	
		<div class="ui-block-c"><a href="#pick_date" data-role="button" title="Change date" data-theme="b" class="date-nav-button">2004-23-12</a></div>	
		<div class="ui-block-d"><a href="#cart" data-icon="cart" data-role="button" style="float:right; margin-right:25px;" data-theme="a">View cart</a></div>		
	</div><!-- /header -->

	<div data-role="content" data-inset="true">	

			<table id="product_list" class="productListing"  width="100%">
						<thead>
							<tr>
								<th><?php echo $Text['name_item'];?></th>						
								<th><?php echo $Text['provider_name'];?></th>
								<th><?php echo $Text['quantity'];?></th>
								<th><?php echo $Text['unit'];?></th>							
								<th><?php echo $Text['price'];?></th>
							</tr>
						</thead>
						<tbody>
							<tr id="{id}">
								<td class="item_name">{name}</td>
								<td class="item_provider_name">{provider_name}</td>
								<td class="item_quantity"><input  name="{id}" value="0.00" size="4" id="quantity_{id}"/></td>
								<td class="item_unit">{unit}</td>	
								<td class="item_price">{unit_price}</td>			
							</tr>						
						</tbody>
			</table>
			
			<!-- div class="ui-grid-d" id="plist">
				<div class="ui-block-a">{name}</div>
				<div class="ui-block-b">{provider_name}</div>
				<div class="ui-block-c"><input  name="{id}" value="0.00" size="4" id="quantity_{id}"/></div>
				<div class="ui-block-d">{unit}</div>
				<div class="ui-block-e">{unit_price}</div>
			</div-->
			
			
	</div><!-- /content -->
</div><!-- /page -->


<div data-role="page" id="cart" data-title="Aixada - Buy stuff - Cart">

	<div data-role="header">
		<a href="#shop" data-icon="arrow-l" data-iconpos="notext" data-rel="back">Back</a>		
		<h1>My Cart Shopping</h1>		
	</div><!-- /header -->

	<div id="cartLayer" data-role="content">		
	</div><!-- /content -->

</div><!-- /page -->



</body>
</html>