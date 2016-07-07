<?php include "php/inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " /*.  $Text['head_ti_'.strtolower($_REQUEST['what'])]*/; ?></title>

	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
  	<link rel="stylesheet" type="text/css"   media="print"  href="css/print.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/aixadacart/aixadacart.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/fgmenu/fg.menu.css"   />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/<?=$default_theme;?>/jqueryui.css"/>

    <script type="text/javascript" src="js/jquery/jquery.js"></script>
    <script type="text/javascript" src="js/jqueryui/jqueryui.js"></script>
    <script type="text/javascript" src="js/fgmenu/fg.menu.js"></script>
    <script type="text/javascript" src="js/aixadautilities/jquery.aixadaMenu.js"></script>
    <script type="text/javascript" src="js/aixadautilities/jquery.aixadaXML2HTML.js" ></script>
    <script type="text/javascript" src="js/aixadautilities/jquery.aixadaUtilities.js" ></script>
    <script type="text/javascript" src="js/aixadacart/jquery.aixadacart.js" ></script>

 	<script type="text/javascript" src="js/jqueryui/i18n/jquery.ui.datepicker-<?=$language;?>.js" ></script>
 	<script type="text/javascript" src="js/aixadacart/i18n/cart.locale-<?=$language;?>.js" ></script>


	<script type="text/javascript">

	$(function(){
		$.ajaxSetup({ cache: false });

	//decide what to do in which section
	var what = $.getUrlVar('what');

	//allow pruchase of stock_actual < 0 items? 
	var preventOutofStock = <?php echo configuration_vars::get_instance()->prevent_out_of_stock_purchase;?>


	//detect form submit and prevent page navigation; we use ajax.
	$('form').submit(function() {
		// submit the form
		$(this).ajaxSubmit();
    	// return false to prevent normal browser submit
		return false;
	});

	$("#tabs").tabs();

	//layer inform that provider has passed closing date for order
	var counterClosed = 0;
	$("#providerClosedStatus").hide();


	//init cart
	$('#cartLayer').aixadacart("init",{
		saveCartURL : 'php/ctrl/ShopAndOrder.php?what='+what+'&oper=commit',
		loadCartURL : 'php/ctrl/ShopAndOrder.php?oper=get'+what+'Cart',
		cartType	: (what=='Shop')? 'standalone':'standalone_preorder',
		btnType		: 'save',
		autoSave	: 5000,
		loadSuccess : updateCartLabel,
		submitComplete : updateCartLabel,
		submitError : function (err_msg){

			//foreign key exception; could be that orderable products have been changed while ordering and
			//the cart needs to be reloaded.
			if (err_msg.indexOf("ERROR 20") != -1){

				$.showMsg({
					msg:"<?=$Text['msg_err_modified_order'];?>",
					type: 'warning'});

				//remove items from cart
				$('#cartLayer').aixadacart('resetCart');

				//refresh page, including cart.
				refreshSelects($.getSelectedDate('#datepicker'));

			//another serious error, now for real
			} else {

				$.showMsg({
					msg: err_msg +
						"<br><span style=\"color:black\"><?=$Text['msg_err_cart_reloaded'];?></span>",
					buttons: {
						"<?=$Text['btn_ok'];?>":function(){

							$('#cartLayer').aixadacart('resetCart');

							refreshSelects($.getSelectedDate('#datepicker'));

							$(this).dialog("close");
						}
					},
					type: 'error'});

			}
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


	/**
	 * build Provider SELECT
	 */
	$("#providerSelect").xml2html("init", {
							offSet : 1
				}).change(function(){
					var id = $("option:selected", this).val();					//get the id of the provider
					$('#product_list_provider tbody').xml2html('removeAll');	//empty the list
					$('#providerClosedStatus').hide();
					counterClosed = 0;

					if (id < 0) { return true;}

					$('.loadSpinner').show();
					//alert($.getSelectedDate('#datepicker','',what)); //for shop the date needs to be 0!!
					$('#product_list_provider tbody').xml2html("reload",{
						params: 'oper=get'+what+'Products&provider_id='+id+'&date='+$.getSelectedDate('#datepicker','',what),
						rowComplete : function(rowIndex, row){	//updates quantities for items already in cart
							formatRow(row);
						},
						complete : function (rowCount){
							$('.loadSpinner').hide();
							if (rowCount == 0){
								$.showMsg({
									msg:"<?php echo $Text['msg_no_active_products'];?>",
									type: 'info'});
							} else if (rowCount == counterClosed){

								$('#providerClosedStatus').show();
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
						params: 'oper=get'+what+'Products&category_id='+id+'&date='+$.getSelectedDate('#datepicker','',what),
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
						params: 'oper=get'+what+'Products&date='+$.getSelectedDate('#datepicker','',what)+'&like='+searchStr,
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



	//dates available to make orders; start with dummy date
	var availableDates = ["2011-00-00"];


	$("#datepicker").datepicker({
				dateFormat 	: 'DD d M, yy',
				showAnim	: '',
				beforeShowDay: function(date){		//activate only those dates that are available for ordering.
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


   	/**
   	 *	init the datepicker
   	 */
	if (what == "Shop") {
		$('#tabs ul').children('li:gt(2)').hide(); 			//preorder tab is only available for ordering
		$("#datepicker").hide();							//hide date input field for shop

		$.getAixadaDates('getToday', function (date){
			$("#datepicker").datepicker('setDate', $.datepicker.parseDate('yy-mm-dd', date[0]));
			$("#datepicker").datepicker("refresh");
			refreshSelects(date[0]);
		});

	} else {

		$.getAixadaDates('getAllOrderableDates', function (dates){
			//if no dates are available, products have to be activated first!!
			if (dates.length == 0){
				$.showMsg({
					msg:"<?php echo $Text['msg_no_active_products'];?>",
					type: 'error'});
				return false;
			}

			availableDates = dates;
			$("#datepicker").datepicker('setDate', $.datepicker.parseDate('yy-mm-dd', availableDates[0]));
			$("#datepicker").datepicker("refresh");
			refreshSelects(dates[0]);
		});
	}


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
	 *	product item info column. Constructs context menu for item
	 */
	$(".rowProductInfo")
		.live("mouseenter", function(){
			$(this).addClass('ui-state-hover');
			if (!$(this).attr("hasMenu")){
				//selected tab
				var selTab = $("#tabs").tabs('option', 'selected')

				var itemInfo = '<ul>';
				//only show stock if we buy; order has no stock
				if (what == 'Shop') itemInfo += '<li><?=$Text["curStock"];?>: ' + $(this).attr("stock") + '</li>';
				//add description of product
				itemInfo += '<li><?=$Text['description'];?>: '+$(this).attr("description")+'</li>';
				itemInfo += '<li><?=$Text['iva'];?>: '+$(this).attr("iva_percent")+'%</li>';
				itemInfo += '<li><?=$Text['revtax_abbrev'];?>: '+$(this).attr("rev_tax_percent")+'%</li>'
				itemInfo += '</ul>';

				//init the context menu
				$(this).menu({
					content: itemInfo,
					width: 280,
					showSpeed: 50,
					flyOut: false
				});

				$(this).attr("hasMenu", 1);
			}
		})
		.live("mouseleave", function(){
			$(this).removeClass('ui-state-hover');
	});


	//attach event listeners for the product input fields; change of quantity will put the
	//item into the cart.
	$('.product_list tbody')
		.find("input")
		.live("change", function (e){
			var row = $(this).parents("tr");										//retrieve the current table row where quantity has been changed
			var isPreorder = $(this).parents("tr").attr('preorder')? true:false; 	//check if this is a preorder item

			//TODO should be replaced with global $.checkNumber...
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

			//if quantity has changed, add it to the cart.
			$('#cartLayer').aixadacart("addItem",{
					id 				: $(row).attr("id"),
					isPreorder 		: isPreorder,
					provider_name 	: $("td.item_provider_name", row).text(),
					name 			: $("td.item_name", row).text(),
					price 			: parseFloat($("td.item_price", row).text()),
					quantity 		: $(this).val(),
					unit 			: $("td.item_unit", row).text(),
					rev_tax_percent : $("td.item_rev_tax_percent", row).text(),
					iva_percent		: $("td.item_iva_percent", row).text()
			}); //end addItem to cart

			//sets nr of items in cart hide/view button
			updateCartLabel();

	});//end event listener for product list

	//update the cart show/hide button
	function updateCartLabel (){
    	var nItems = $('#cartLayer').aixadacart('countItems');
        var strItems = (nItems == 1) ? "<?=$Text['product_singular']?>" : "<?= $Text['product_plural']?>";
    	var label = "<?=$Text['btn_view_cart'];?> ("+nItems+")";
		$( "#view_cart" ).button( "option", "label",label);
	}


	//if date gets changed, then selects need a refresh because providers available might change
	function refreshSelects(dateText){

		$('#cartLayer').aixadacart('loadCart',{
			loadCartURL		: 'php/ctrl/ShopAndOrder.php?oper=get'+what+'Cart&date='+dateText,
			date 			: dateText
		}); //end loadCart

		$("#providerSelect").xml2html("reload", {
			params : 'oper=get'+what+'Providers&date='+dateText,
			rowComplete : function(rowIndex, row){
				//read here if provider's order is still open or not.

			}
		})

		$("#categorySelect").xml2html("reload", {
			params : 'oper=get'+what+'Categories&date='+dateText
		})

		$('#product_list_provider tbody').xml2html("removeAll");
		$('#product_list_category tbody').xml2html("removeAll");
		$('#product_list_search tbody').xml2html("removeAll");

		$('#providerClosedStatus').hide();
		counterClosed = 0;

	};


	/**
	 *	utility function to format product rows
	 */
	function formatRow(row){

		var days2Closing = $(row).attr("closingdate");
		var id =  $(row).attr("id");
		var qu = $("#cart_quantity_"+id).val();
		qu = (qu > 0)? qu:0;
		$("#quantity_"+id).val(qu);


		if (!days2Closing || days2Closing <0){
			$(row).addClass('dim60');
			$('td', row).addClass('ui-state-error');
			$('input', row).attr('disabled','disabled');
			counterClosed++;
		}

		var stockActual = $(row).attr("stock");
		var orderType	= $(row).attr("ordertype");

		//this is a stock product without any stock left: can't be bought. 
		if (preventOutofStock == true && orderType == 1 && stockActual <=0){
			$(row).addClass('dim60');
			$('td', row).addClass('ui-state-highlight');
			$('input', row).attr('disabled','disabled');
			$('td:eq(1)', row).empty().append("<?php echo $Text['no_stock']; ?>")
		}



	}


	/**
	 *	show hide the datepicker
	 */
	$('.toggleShopDate').click(function(){
		$("#datepicker").toggle();
	});


	//loading animation
	$('.loadSpinner').attr('src', "img/ajax-loader-<?=$default_theme;?>.gif");
	$('#leftCol .loadSpinner').hide();

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


	<div id="stagewrap" class="ui-widget">

		<div id="titlewrap">
			<div id="titleLeftCol">
		    	<h1><?php if ($_REQUEST['what'] == 'Order') {
		    					echo $Text['ti_order'];
		    				} else if ($_REQUEST['what'] == 'Shop') {
		    					echo $Text['ti_shop'];
		    					printf('<sup class="toggleShopDate" title="%s">(*)</sup>',$Text['show_date_field']);
		    				}?>
		    	&nbsp; <input  type="text" class="datePickerInput ui-widget-content ui-corner-all" id="datepicker" title="Click to edit"></h1>
    		</div>
    		<div id="titleRightCol">
    			<div id="ViewChoice">
					<input type="radio" id="view_list" name="viewCol" /><label for="view_list" title="<?php echo $Text['btn_view_list_lng'];?>"><?php echo $Text['btn_view_list'];?></label>
					<input type="radio" id="view_cart" name="viewCol"  /><label for="view_cart" title="<?php echo $Text['btn_view_cart_lng'];?>"><?php echo $Text['btn_view_cart'];?> (0)</label>
					<input type="radio" id="view_both" name="viewCol" checked="checked"/><label for="view_both" title="<?php echo $Text['btn_view_both_lng'];?>"><?php echo $Text['btn_view_both'];?></label>
				</div>
    		</div>
		</div><!-- end titlewrap -->

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
				<div class="orderStatus ui-widget" id="providerClosedStatus"><p class="padding5x10 ui-corner-all ui-state-highlight"><?php echo $Text['order_closed']; ?> <span class="ui-icon ui-icon-locked floatRight"></span></p></div>
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
							<tr id="{id}" closingdate="{time_left}" stock="{stock_actual}" ordertype="{orderable_type_id}">
								<td class="item_it">{id}</td>
								<td class="item_info"><p class="ui-corner-all iconContainer textAlignCenter rowProductInfo" stock="{stock_actual}" iva_percent="{iva_percent}" rev_tax_percent="{rev_tax_percent}" description="{description}"><span class="ui-icon ui-icon-info"></span></p></td>
								<td class="item_name">{name}</td>
								<td class="item_provider_name hidden">{provider_name}</td>
								<td class="item_quantity"><input  class="ui-corner-all quantity_{id}" name="{id}" value="0.00" size="4" id="quantity_{id}"/></td>
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
							<tr id="{id}" closingdate="{time_left}" stock="{stock_actual}" ordertype="{orderable_type_id}">
								<td class="item_it">{id}</td>
								<td class="item_info"><p class="ui-corner-all iconContainer textAlignCenter rowProductInfo" stock="{stock_actual}" iva_percent="{iva_percent}" rev_tax_percent="{rev_tax_percent}" description="{description}"><span class="ui-icon ui-icon-info"></span></p></td>
								<td class="item_name">{name}</td>
								<td class="item_provider_name">{provider_name}</td>
								<td class="item_quantity"><input class="ui-corner-all quantity_{id}"  name="{id}" value="0.00" size="4" id="quantity_{id}"/></td>
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
							<tr id="{id}" closingdate="{time_left}" stock="{stock_actual}" ordertype="{orderable_type_id}">
								<td class="item_it">{id}</td>
								<td class="item_info"><p class="ui-corner-all iconContainer textAlignCenter rowProductInfo" stock="{stock_actual}" iva_percent="{iva_percent}" rev_tax_percent="{rev_tax_percent}" description="{description}"><span class="ui-icon ui-icon-info"></span></p></td>
								<td class="item_name">{name}</td>
								<td class="item_provider_name">{provider_name}</td>
								<td class="item_quantity"><input  class="ui-corner-all quantity_{id}" name="{id}" value="0.00" size="4" id="quantity_{id}"/></td>
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
								<td class="item_quantity"><input class="ui-corner-all quantity_{id}" name="{id}" value="0.00" size="4" id="quantity_{id}"/></td>
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

		<!-- Shopping cart starts -->
		<div id="rightCol" class="aix-layout-splitW40 floatLeft aix-layout-widget-right-col">
			<div id="cartLayer"></div>
		</div>

	</div>
	<!-- end of stage wrap -->
</div>
<!-- end of wrap -->

<!-- / END -->
</body>
</html>