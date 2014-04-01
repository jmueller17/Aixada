<?php include "php/inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " /*.  $Text['head_ti_'.strtolower($_REQUEST['what'])]*/; ?></title>

  	<!--link rel="stylesheet" type="text/css"   media="screen" href="js/aixadacart/aixadacart.css" /-->


    <link href="js/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/aixcss.css" rel="stylesheet">
    <link href="js/ladda/ladda-themeless.min.css" rel="stylesheet">
    <link href="js/datepicker/bootstrap-datetimepicker.min.css" rel="stylesheet">



	<?php if (isset($_SESSION['dev']) && $_SESSION['dev'] == true ) { ?>
	    <script type="text/javascript" src="js/jquery/jquery.js"></script>
   	    <script type="text/javascript" src="js/bootstrap/js/bootstrap.min.js"></script>
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaXML2HTML.js" ></script>
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaSwitchSection.js" ></script>
	   	
	   	<script type="text/javascript" src="js/bootbox/bootbox.js"></script>
	   	<script type="text/javascript" src="js/ladda/spin.min.js"></script>
	   	<script type="text/javascript" src="js/ladda/ladda.min.js"></script>
	   	<script type="text/javascript" src="js/datepicker/moment-with-langs.min.js"></script>
	   	
		<script type="text/javascript" src="js/datepicker/bootstrap-datetimepicker.min.js"></script>
	


	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaUtilities.js" ></script>
	   	<script type="text/javascript" src="js/aixadacart/jquery.aixadacart.js" ></script>
   	<?php  } else { ?>
	   	<script type="text/javascript" src="js/js_for_shop_and_order.min.js"></script>
    <?php }?>

 	<!--script type="text/javascript" src="js/jqueryui/i18n/jquery.ui.datepicker-<?=$language;?>.js" ></script-->
 	<script type="text/javascript" src="js/aixadacart/i18n/cart.locale-<?=$language;?>.js" ></script>


	<script type="text/javascript">

	$(function(){



	//decide what to do in which section
	var what = $.getUrlVar('what');

	//hide all sections
	$('.section').hide();


	//allow purchase of stock_actual < 0 items? 
	var preventOutofStock = <?php echo configuration_vars::get_instance()->prevent_out_of_stock_purchase;?>

	//load today's date from server 
	var gToday = null; 

	//detect form submit and prevent page navigation; we use ajax.
	$('form').submit(function() {
		// submit the form
		$(this).ajaxSubmit();
    	// return false to prevent normal browser submit
		return false;
	});


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
		cartType	: 'simple',
		loadSuccess : updateCartLabel,
		submitComplete : updateCartLabel,
		submitError : function (err_msg){

			//foreign key exception; could be that orderable products have been changed while ordering and
			//the cart needs to be reloaded.
			if (err_msg.indexOf("ERROR 20") != -1){

				bootbox.alert({
						title : "Epp!!",
						message : "<div class='alert alert-warning'><?php echo $Text['msg_err_modified_order']; ?></div>"
				});	
	
				//remove items from cart
				$('#cartLayer').aixadacart('resetCart');

				//refresh page, including cart.
				refreshSelects($.getSelectedDate('#datepicker'));

			//another serious error, now for real
			} else {

				bootbox.confirm({
						title : "Epp!",
						message : "<div class='alert alert-warning'>"+err_msg + " Your cart will be reloaded.</div>",
						buttons : {
							ok : {
								callback : function(ok){
									$('#cartLayer').aixadacart('resetCart');
									refreshSelects($.getSelectedDate('#datepicker'));
								}
							}

						}
				});

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
					//for Shop the date needs to be 0!!
					$('#product_list_provider tbody').xml2html("reload",{
						params: 'oper=get'+what+'Products&provider_id='+id+'&date='+$.getSelectedDate('#datepicker','','', what),
						rowComplete : function(rowIndex, row){	//updates quantities for items already in cart
							formatRow(row);
						},
						complete : function (rowCount){
							$('.loadSpinner').hide();
							if (rowCount == 0){
								bootbox.alert({
										title : "Epp!!",
										message : "<div class='alert alert-info'><?php echo $Text['msg_no_active_products'];?></div>"
								});
								
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
						params: 'oper=get'+what+'Products&category_id='+id+'&date='+$.getSelectedDate('#datepicker','','', what),
						rowComplete : function(rowIndex, row){	//updates quantities for items already in cart
							formatRow(row);
						},
						complete : function (rowCount){
							$('.loadSpinner').hide();
							if (rowCount == 0){
								bootbox.alert({
										title : "Epp!!",
										message : "<div class='alert alert-info'><?php echo $Text['msg_no_active_products'];?></div>"
								});
								
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
						params: 'oper=get'+what+'Products&date='+$.getSelectedDate('#datepicker','','', what)+'&like='+searchStr,
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



	
	
   	/**
   	 *	init the datepicker
   	 */
	if (what == "Shop") {
		//$('#tabs ul').children('li:gt(2)').hide(); 			//preorder tab is only available for ordering
		$("#datepicker").hide();							//hide date input field for shop

		$.getAixadaDates('getToday', function (date){
			gToday = date[0];
			$('#datepicker').datetimepicker({
				pickTime:false,
				startDate : '1/1/2014',
				defaultDate : gToday
			}).on("change.dp",function(e){
				refreshSelects($.getSelectedDate('#datepicker'));
			})

			refreshSelects(date[0]);
		});

	} else {

		// init date picker with orderable dates and set todays date
		$.getAixadaDates('getAllOrderableDates', function (dates){
			//if no dates are available, products have to be activated first!!
			if (dates.length == 0){
				bootbox.alert({
						title : "Epp!!",
						message : "<div class='alert alert-warning'><?php echo $Text['msg_no_active_products'];?></div>"
				});	
				return false;
			}		

			$('#datepicker').datetimepicker({
				pickTime:false,
				startDate : '1/1/2014',
				endDate : dates[dates.length-1], 
				enabledDates: dates
			}).on("change.dp",function(e){
				refreshSelects($.getSelectedDate('#datepicker'));
			})

			$.getAixadaDates('getToday', function (date){
				gToday = date[0];
				$('#datepicker').data("DateTimePicker").setDate(date[0]);
				refreshSelects(date[0]);
			});
		});
	
	}


	/**
	 *  show hide item list, cart or both
	
	var leftColWidth = $('#leftCol').innerWidth();
	var rightColWidth = $('#rightCol').innerWidth();
	$('#ViewChoice')
		//.buttonset()
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
		});*/



	/**
	 *	product item info column. Constructs context menu for item
	 */
	/*$(".rowProductInfo")
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
	});*/


	//attach event listeners for the product input fields; change of quantity will put the
	//item into the cart.
	$('#product_list_provider, #product_list_category, #product_list_search')
		.on("change","input", function (e){			
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
        //var strItems = (nItems == 1) ? "<?=$Text['product_singular']?>" : "<?= $Text['product_plural']?>";
    	var label = "<?=$Text['btn_view_cart'];?> ("+nItems+")";
		//$( "#view_cart" ).button( "option", "label",label);
		$('#itemCount').text(nItems)
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
			params : 'oper=get'+what+'Categories&date='+dateText,
		})

		if (what == "Order") $('.set-date').text(dateText)

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
			//$(row).addClass('dim60');
			$('td', row).addClass('bg-warning');
			$('input', row).attr('disabled','disabled');
			counterClosed++;
		}

		var stockActual = $(row).attr("stock");
		var orderType	= $(row).attr("ordertype");

		//this is a stock product without any stock left: can't be bought. 
		if (preventOutofStock == true && orderType == 1 && stockActual <=0){
			//$(row).addClass('dim60');
			$('td', row).addClass('bg-warning');
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


	$('.sec-1').show();

	$('.change-sec')
			.switchSection("init");


	});  //close document ready
</script>

</head>
<?php flush(); ?>
<body>

	<div id="headwrap">
		<?php include "php/inc/menu.inc.php" ?>
	</div>
	<!-- end of headwrap -->


	<div class="container">
		<!-- page header -->
		<div class="row">
	    		<div class="col-md-4">
	    			<h1 class="section sec-1">
							<?php if ($_REQUEST['what'] == 'Order') {
								echo $Text['ti_order'];
							} else if ($_REQUEST['what'] == 'Shop') {
								echo $Text['ti_shop'];
							}?>
					</h1>
					<h1 class="section sec-2"> 
						<a href="#sec-1" class="change-sec">
							<?php if ($_REQUEST['what'] == 'Order') {
								echo "Order";
							} else if ($_REQUEST['what'] == 'Shop') {
								echo $Text['ti_shop'];
							}?>

						</a> <span class="glyphicon glyphicon-chevron-right sp-sm"></span> cart <span class="set-date sp-sm"></span></h1>
				</div>
	    		<div class="col-md-4 pull-left">
	    			<h1 class="section sec-1">
					<div class="form-group">
                        <div class='input-group date' id='datepicker' >
                            <input type='text' class="form-control" id="inputField" data-format="dddd, ll" />
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                    </h1>
                </div>
                <div class="col-md-4">
                	<div class="btn-group pull-right">
	                	<butto class="btn btn-primary change-sec" type="button" toggle-section="#sec-2,#sec-1">
                			<span class="glyphicon glyphicon-shopping-cart pull-left"></span>
                			&nbsp;&nbsp;&nbsp;
                			<?php if ($_REQUEST['what'] == 'Order') {
									echo 'Order cart';
								} else if ($_REQUEST['what'] == 'Shop') {
									echo 'Shop cart';
								}?>

                			&nbsp;&nbsp;&nbsp;&nbsp;
                			<span class="badge" id="itemCount"></span>
                		</button>
					</div>
                </div>


					
		</div>


		<div class="row ax-container section sec-1">
			<div class="col-md-12">
				<ul class="nav nav-pills">
					<li class="active"><a href="#tabs-1" data-toggle="tab"><?php echo $Text['by_provider']; ?></a></li>
					<li><a href="#tabs-2" data-toggle="tab"><?php echo $Text['by_category']; ?></a></li>
					<li><a href="#tabs-3" data-toggle="tab"><?php echo $Text['search']; ?></a></li>
					<li><a href="#tabs-4" data-toggle="tab"><?php echo $Text['special_offer']; ?></a></li>
				</ul>
			
				<div class="tab-content">

				<!-- provider select tab-->
				<div id="tabs-1" class="tab-pane active">
					<div class="form-group col-md-4">
						<label for="providerSelect">&nbsp;</label>
						<select id="providerSelect" class="form-control">
							<option value="-1" selected="selected"><?php echo $Text['sel_provider']; ?></option>
	                    	<option value="{id}"> {name}</option>
						</select>
					</div>

					<div class="bg-warning" id="providerClosedStatus">
						<p><?php echo $Text['order_closed']; ?> <span class="glyphicon glyphicon-lock"></span></p>
					</div>
				
					<table id="product_list_provider" class="table">
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
							<td class="item_quantity"><input  class="form-control max-width" name="{id}" value="0.00" size="4" id="quantity_{id}"/></td>
							<td class="item_unit">{unit}</td>
							<td class="item_rev_tax_percent hidden">{rev_tax_percent}</td>
							<td class="item_price">{unit_price}</td>
							<td class="item_iva_percent hidden">{iva_percent}</td>
						</tr>
					</tbody>
					</table>
				</div>

				<!-- category select tab-->
				<div id="tabs-2" class="tab-pane">
			 		<div class="form-group  col-md-4">
					<label for="categorySelect">&nbsp;</label>
					<select id="categorySelect" class="form-control">
						<option value="-1" selected="selected"><?php echo $Text['sel_category']; ?></option>
                    	<option value="{id}">{description}</option>
					</select>
				</div>

					<table id="product_list_category" class="table" >
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
								<td class="item_quantity"><input class="form-control max-width"  name="{id}" value="0.00" size="4" id="quantity_{id}"/></td>
								<td class="item_unit">{unit}</td>
								<td class="item_rev_tax_percent hidden">{rev_tax_percent}</td>
								<td class="item_price">{unit_price}</td>
								<td class="item_iva_percent hidden">{iva_percent}</td>
							</tr>
						</tbody>
					</table>
				</div>


				<!-- search tab -->
				<div id="tabs-3" class="tab-pane">
					<div class="col-lg-4">
						<label for="search">&nbsp;</label>
					    <div class="input-group">
					      <input type="text" id="search" class="form-control" placeholder="Product name">
					      <span class="input-group-btn">
					        <button class="btn btn-default" type="button"><span class="glyphicon glyphicon-search"></span> Search!</button>
					      </span>
					    </div><!-- /input-group -->
					  </div><!-- /.col-lg-6 -->

						
				<p>&nbsp;</p>
					<table id="product_list_search" class="table table-condensed table-hover" >
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
								<td class="item_quantity"><input  class="form-control max-width" name="{id}" value="0.00" size="4" id="quantity_{id}"/></td>
								<td class="item_unit">{unit}</td>
								<td class="item_rev_tax_percent hidden">{rev_tax_percent}</td>
								<td class="item_price">{unit_price}</td>
								<td class="item_iva_percent hidden">{iva_percent}</td>
							</tr>
						</tbody>
					</table>
				
				</div>


				<!-- preorder tab -->
				<div id="tabs-4" class="tab-pane">
				<table id="product_list_preorder" class="table" >
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
								<td class="item_quantity"><input class="form-control max-width" name="{id}" value="0.00" size="4" id="quantity_{id}"/></td>
								<td class="item_unit">{unit}</td>
								<td class="item_rev_tax_percent hidden">{rev_tax_percent}</td>
								<td class="item_price">{unit_price}</td>
								<td class="item_iva_percent hidden">{iva_percent}</td>
							</tr>
						</tbody>
					</table>
					</div>
				</div><!-- end tab content -->
			</div>
		</div><!-- end main row -->

		<div class="row ax-container section sec-2">
			<div class="col-md-1">
				<?php if ($_REQUEST['what'] == 'Order') {
						echo '<h1><span class="glyphicon glyphicon-phone-alt"></span> </h1>';
				} else if ($_REQUEST['what'] == 'Shop') {
						echo '<h1><span class="glyphicon glyphicon-shopping-cart"></span></h1>';
				}?>
			</div>
			<div class="col-md-10">
			<!-- Shopping cart starts -->
				<div id="cartLayer"></div>
			</div><!-- end right col -->
		</div>

	</div>
	<!-- end container -->

</body>
</html>