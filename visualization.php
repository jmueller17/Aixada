<?php include "php/inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_manage'] . " " . $Text['head_ti_manage_uf'] ;?></title>
	
	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/fgmenu/fg.menu.css"   />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/aixadacart/aixadacart.css" />
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
	    <script type="text/javascript" src="js/js_for_visualization.min.js"></script>
    <?php }?>
 	<script type="text/javascript" src="js/aixadacart/i18n/cart.locale-<?=$language;?>.js" ></script>
        	
   
	<script type="text/javascript">
	
      $(function(){
			
	$("#tabs").tabs();

	//init cart
	$('#cartLayer').aixadacart("init",{
		saveCartURL : 'php/ctrl/Statistics.php?oper=product_prices',
		loadCartURL : '',
		cartType	: 'standalone_preorder',
		btnType		: 'save',
		autoSave	: 5000,
		submitError : function (err_msg){
				$.showMsg({
					msg:err_msg + " Your cart will be reloaded.",
					buttons: {
						"<?=$Text['btn_ok'];?>":function(){

							$('#cartLayer').aixadacart('resetCart');

							refreshSelects($.getSelectedDate('#datepicker'));

							$(this).dialog("close");
						}
					},
					type: 'error'});

		}
	});

	$('#product_list_provider tbody').xml2html("init");
	$('#product_list_category tbody').xml2html("init");
	$('#product_list_search tbody').xml2html("init");
	$('#product_list_preorder tbody').xml2html("init",{
			url: 'php/ctrl/ShopAndOrder.php',
			params : 'oper=getPreorderableProducts',
			loadOnInit : true
	});

	$("#providerSelect").xml2html("init", {
							offSet : 1
				}).change(function(){
					var id = $("option:selected", this).val();					//get the id of the provider
					$('#product_list_provider tbody').xml2html('removeAll');	//empty the list
					$('#providerClosedStatus').hide();
					counterClosed = 0;

					if (id < 0) { return true;}

					$('.loadSpinner').show();
					$('#product_list_provider tbody').xml2html("reload",{
					    params: 'oper=getShopProducts&provider_id='+id+'&all=1',
						rowComplete : function(rowIndex, row){	//updates quantities for items already in cart
							formatRow(row);
						},
						complete : function (rowCount){
							$('.loadSpinner').hide();
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
			offSet : 1,
			loadOnInit: false
		}).change(function(){
					//get the id of the provider
					var id = $("option:selected", this).val();
					$('#product_list_category tbody').xml2html('removeAll');

					if (id < 0) {return true;}

					$('.loadSpinner').show();
					$('#product_list_category tbody').xml2html("reload",{
					    params: 'oper=getShopProducts&category_id='+id+'&all=1',
						rowComplete : function(rowIndex, row){	//updates quantities for items already in cart
							formatRow(row);
						},
						complete : function (rowCount){
							$('.loadSpinner').hide();
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

				if (searchStr.length >= minLength){
					$('.loadSpinner').show();
				  	$('#product_list_search tbody').xml2html("reload",{
						params: 'oper=getShopProducts&like='+searchStr,
						rowComplete : function(rowIndex, row){	//updates quantities for items already in cart
							formatRow(row);
						},
						complete : function(rowCount){
							$('.loadSpinner').hide();
						}
					});
				} else {
					$('#product_list_search tbody').xml2html("removeAll");				//delete all product entries in the table if we are below minLength;

				}
		e.preventDefault();						//prevent default event propagation. once the list is build, just stop here.
	}); //end autocomplete



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
        <h1><?php echo $Text['ti_visualization']; ?></h1>
      </div>
    </div>

		<div id="leftCol" class="aix-layout-splitW60 floatLeft">
		<div id="tabs">
			<ul>
				<li><a href="#tabs-1"><?php echo $Text['by_provider']; ?></a></li>
				<li><a href="#tabs-2"><?php echo $Text['by_category']; ?></a></li>
				<li><a href="#tabs-3"><?php echo $Text['search']; ?></a></li>
				<li><a href="#tabs-4"><?php echo $Text['special_offer']; ?></a></li>
			</ul>
			<span style="float:right; margin-top:-40px; margin-right:5px;"><img class="loadSpinner" src="img/ajax-loader.gif"/></span>
			<div id="tabs-1">
				<div class="wrapSelect">
					<select id="providerSelect" class="longSelect">
                    	<option value="-1" selected="selected"><?php echo $Text['sel_provider']; ?></option>
                    	<option value="{id}"> {name}</option>
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
								<th><?php echo $Text['price'];?></th>

							</tr>
						</thead>
						<tbody>
							<tr id="{id}" closingdate="{time_left}">
								<td class="item_it">{id}</td>
								<td class="item_info"><p class="ui-corner-all iconContainer textAlignCenter rowProductInfo" stock="{stock_actual}" iva_percent="{iva_percent}" rev_tax_percent="{rev_tax_percent}" description="{description}"><span class="ui-icon ui-icon-info"></span></p></td>
								<td class="item_name">{name}</td>
								<td class="item_provider_name hidden">{provider_name}</td>
								<td class="item_quantity"><input  class="ui-corner-all" name="{id}" value="0.00" size="4" id="quantity_{id}"/></td>
								<td class="item_unit">{unit}</td>
								<td class="item_rev_tax_percent hidden">{rev_tax_percent}</td>
								<td class="item_price">{unit_price}</td>
								<td class="item_iva_percent hidden">{iva_percent}</td>
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
							<!-- th><?php echo $Text['revtax_abbrev'];?></th-->
							<th><?php echo $Text['price'];?></th>
						</tr>
						</thead>
						<tbody>
							<tr id="{id}" closingdate="{time_left}">
								<td class="item_it">{id}</td>
								<td class="item_info"><p class="ui-corner-all iconContainer textAlignCenter rowProductInfo" stock="{stock_actual}" iva_percent="{iva_percent}" rev_tax_percent="{rev_tax_percent}" description="{description}"><span class="ui-icon ui-icon-info"></span></p></td>
								<td class="item_name">{name}</td>
								<td class="item_provider_name">{provider_name}</td>
								<td class="item_quantity"><input class="ui-corner-all"  name="{id}" value="0.00" size="4" id="quantity_{id}"/></td>
								<td class="item_unit">{unit}</td>
								<td class="item_rev_tax_percent hidden">{rev_tax_percent}</td>
								<td class="item_price">{unit_price}</td>
								<td class="item_iva_percent hidden">{iva_percent}</td>
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
				<p>&nbsp;</p>
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
							<!-- th><?php echo $Text['revtax_abbrev'];?></th-->
							<th><?php echo $Text['price'];?></th>
						</tr>
						</thead>
						<tbody>
							<tr id="{id}" closingdate="{time_left}">
								<td class="item_it">{id}</td>
								<td class="item_info"><p class="ui-corner-all iconContainer textAlignCenter rowProductInfo" stock="{stock_actual}" iva_percent="{iva_percent}" rev_tax_percent="{rev_tax_percent}" description="{description}"><span class="ui-icon ui-icon-info"></span></p></td>
								<td class="item_name">{name}</td>
								<td class="item_provider_name">{provider_name}</td>
								<td class="item_quantity"><input  class="ui-corner-all" name="{id}" value="0.00" size="4" id="quantity_{id}"/></td>
								<td class="item_unit">{unit}</td>
								<td class="item_rev_tax_percent hidden">{rev_tax_percent}</td>
								<td class="item_price">{unit_price}</td>
								<td class="item_iva_percent hidden">{iva_percent}</td>
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
								<!-- th><?php echo $Text['revtax_abbrev'];?></th-->
								<th><?php echo $Text['price'];?></th>
							</tr>
						</thead>
						<tbody>
							<tr id="{id}" preorder="true">
								<td class="item_it">{id}</td>
								<td class="item_info"><p class="ui-corner-all iconContainer textAlignCenter rowProductInfo" stock="{stock_actual}" iva_percent="{iva_percent}" rev_tax_percent="{rev_tax_percent}" description="{description}"><span class="ui-icon ui-icon-info"></span></p></td>
								<td class="item_name">{name}</td>
								<td class="item_provider_name">{provider_name}</td>
								<td class="item_quantity"><input class="ui-corner-all" name="{id}" value="0.00" size="4" id="quantity_{id}"/></td>
								<td class="item_unit">{unit}</td>
								<td class="item_rev_tax_percent hidden">{rev_tax_percent}</td>
								<td class="item_price">{unit_price}</td>
								<td class="item_iva_percent hidden">{iva_percent}</td>
							</tr>
						</tbody>
					</table>


			</div>
		</div><!-- end tabs -->
		</div><!-- end left Col -->

  </div>
  <!-- end of stage wrap -->
</div>
<!-- end of wrap -->

<!-- / END -->
</body>
</html>