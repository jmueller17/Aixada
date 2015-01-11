<?php include "../../php/inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<base href="<?php echo $cv->basedir; ?>" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_sales'] ;?></title>

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
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaUtilities.js"></script>
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaExport.js" ></script>

    <?php  } else { ?>
	    <script type="text/javascript" src="js/js_for_billing.min.js"></script>
    <?php }?>
   		

 	   
   
	<script type="text/javascript">
	$(function(){

		

		//saves the selected purchase row
		var gSelShopRow = null; 

		//saves the selected bill row
		var gBillRow = null


		//coming from other page
		var gDetail = (typeof $.getUrlVar('detailForCart') == "string")? $.getUrlVar('detailForCart'):false;


		//order overview filter option
		var gBackTo = (typeof $.getUrlVar('lastPage') == "string")? $.getUrlVar('lastPage'):false;
		
		//todays date
		var gToday = null; 

		//custom date: Today  - 1 month
		var gPrevMonth = null;

		$('.section').hide();


		$('.change-sec')
			.switchSection("init");

		$('.sec-3').show();

		bootbox.setDefaults({
			locale:"<?=$language;?>"
		})

		//navbar search form. prevents submitting / reload of page
		$('form')
			.submit(function() {
				return false;
			});
		

		$('#datepicker-from').datetimepicker({
				pickTime:false,
				startDate : '1/1/2014',
			}).on("change.dp",function(e){
				reloadListings("all");
			})

		$('#datepicker-to').datetimepicker({
				pickTime:false,
				startDate : '1/1/2014',
			}).on("change.dp",function(e){
				reloadListings("all");
			})

		$.getAixadaDates('getToday', function (date){
			gToday = date[0];
			gPrevMonth = moment(gToday, "YYYY-MM-DD").subtract(1, 'months').format('YYYY-MM-DD');
			
	 		$('#datepicker-to').data("DateTimePicker").setDate(gToday);
			$('#datepicker-from').data("DateTimePicker").setDate(gPrevMonth);

			reloadListings(".sec-3");
		});
		



		/********************************************************
		 *    BILL LISTING
		 ********************************************************/
		$('#tbl_bill tbody').xml2html('init',{
				url : 'modules/billing/php/billing_ctrl.php',
				params : 'oper=getBillListing', 
				loadOnInit : true, 
				beforeLoad : function(){
				},
				rowComplete : function(rowIndex, row){
					
				},
				complete : function(){

				}
		});


		$('#tbl_billDetail tbody').xml2html('init',{
				url : 'modules/billing/php/billing_ctrl.php',
				params : 'oper=getBillDetail', 
				loadOnInit : false, 
				complete : function(){
					$('#tbl_billDetail tfoot').xml2html('reload',{
						params: 'oper=getBillTaxGroups&bill_id='+gBillRow.attr("billId")
					}); 
				}
		});

		$('#tbl_billDetail tfoot').xml2html('init',{
				url : 'modules/billing/php/billing_ctrl.php',
				params : 'oper=getBillTaxGroups', 
				loadOnInit : false, 
				rowComplete : function(rowIndex, row){
					
				}
		});


		//switch to bill detail
		$('#tbl_bill tbody')
			.on('click', 'tr', function(e){

				gBillRow = $(this);
		
				$('#tbl_billDetail tbody').xml2html('reload',{
					params : 'oper=getBillDetail&bill_id='+$(this).attr('billId')
				});

				$(".setBillID").text($(this).attr("billId"));
				$(".setUFID").text($(this).attr("ufId"));

				$('.change-sec').switchSection("changeTo",".sec-4");
		});


		//bill checkboxes.
		$('#tbl_bill tbody')
			.on('click','td:first-child, input',  function(e){				
				e.stopPropagation();
			})

		//bill export
		$('.ctx-nav-export-bill')
			.click(function(){

				if ($('input[name="bulk_bill"]:checked').length  == 0){
    				bootbox.alert({
						title : "Epp!!",
						message : "<div class='alert alert-warning'><?=$Text['msg_err_noselect'];?></div>"
					});	
					return false;
    			} else {

        			var billRow = ''; 
        			var opt = $(this).attr("data");
					opt = opt.split(",");

					$('input[name="bulk_bill"]:checked').each(function(){
						billRow += '<input type="hidden" name="bill_ids[]" value="'+$(this).parents('tr').attr('billId')+'"/>';
					});
					billRow += '<input type="hidden" name="oper" value="export'+opt[0]+'" >';
					billRow += '<input type="hidden" name="format" value="'+opt[1]+'">';
					
					$('#flexform').empty().append(billRow);

					var frmData =  $('#flexform').serialize();

					var urlStr = "modules/billing/php/billing_ctrl.php?" + frmData; 
					
					//load the stuff through the export channel
					$('#dataFrame').attr('src',urlStr);


    			}
    		});


		//delete bill
		$('#tbl_bill tbody')
			.on('click', '.btn-del-bill', function(e){
				var billId = $(this).parents('tr').attr('billId');
				bootbox.confirm({
					title  : "<?=$Text['ti_confirm'];?>",
					message: "<div class='alert alert-danger'>Are you sure you want to delete this bill?</div>",
					callback : function(ok){
						if (ok){
							$this = $(this);
							var urlStr = 'modules/billing/php/billing_ctrl.php?oper=deleteBill&bill_id='+billId; 
							$.ajax({
							   	url: urlStr,
							   	type: 'POST',
							   	success: function(msg){
									//reload all members listing on overiew. 
							   		$('#tbl_bill tbody').xml2html('reload');
							   		bootbox.hideAll();
							   	},
							   	error : function(XMLHttpRequest, textStatus, errorThrown){
							   		bootbox.hideAll();
									if (XMLHttpRequest.responseText.indexOf("ERROR 10") != -1){
										bootbox.alert({
												title : "<?=$Text['ti_error_exec'];?>",
												message : "<div class='alert alert-danger'><?=$Text['msg_err_del_provider']; ?> <br><br> "+XMLHttpRequest.responseText+"</div>",
										});	
									}
							   	}
							}); //end ajax	
						} else {
							bootbox.hideAll();
						}	
					}
				});

				e.stopPropagation();
			})




		/********************************************************
		 *    PURCHASE (CART) LISTING
		 ********************************************************/

		//load cart listing
		$('#tbl_Shop tbody').xml2html('init',{
				url : 'modules/billing/php/billing_ctrl.php',
				params : 'oper=getCartListing', 
				loadOnInit : false, 
				beforeLoad : function(){
				},
				rowComplete : function(rowIndex, row){
					var validated = $(row).children().eq(5).text();
					var billId = $(row).children().eq(1).text();
					
					if (billId >0 ){
						$(row).children().eq(0).children(":first-child").prop("disabled", true)
					}

					
					if (validated == '0000-00-00 00:00:00'){
						$(row).children().eq(5).html("<p class='text-center'>-</p>");	
					} else {
						$(row).children().eq(5).html('<span class="glyphicon glyphicon-ok-sign" title="<?php echo $Text['validated_at'];?>: '+validated+'"></span>');
					}	

					if (gDetail > 0 && $(row).attr('shopId') == gDetail){
						gSelShopRow = $(row); 
					}
				},
				complete : function(){

				}
		});

		

		//switch to cart detail 
		$('#tbl_Shop tbody')
			.on('click', 'tr', function(e){
			
				gSelShopRow = $(this); 
					
				$('#tbl_purchaseDetail tbody').xml2html('reload',{
					params : 'oper=getShopCart&shop_id='+$(this).attr('shopId')
				});

				$('.setUfId').text($(this).attr('ufId'));
				$('.setCartId').text($(this).attr('shopId'));
				var cd = $(this).attr('dateForShop');
				$('.setShopDate').text(cd);
	
				$('.change-sec').switchSection("changeTo",".sec-2");
		});
		

		//cart checkboxes. 
		$('#tbl_Shop tbody')
			.on('click','td:first-child, input',  function(e){

				//activate or deactivate create bill button only if carts are selected
				if ( $('input[name="bulk_cart"]:checked').length  == 0 ){
					$("#btn-create-bill").prop('disabled', true);
				} else {
					$("#btn-create-bill").prop('disabled', false);
				}
				
				e.stopPropagation();
			})


		//create bill
		$("#btn-create-bill")
    		.click(function(e){

    			if ($('input[name="bulk_cart"]:checked').length  == 0){
    				bootbox.alert({
						title : "Epp!!",
						message : "<div class='alert alert-warning'><?=$Text['msg_err_noselect'];?></div>"
					});	
					return false;
    			} else {

        			var billRow = ''; 								
					$('input[name="bulk_cart"]:checked').each(function(){
						billRow += '<input type="hidden" name="cart_ids[]" value="'+$(this).parents('tr').attr('shopId')+'"/>';
					});
					$('#flexform').empty().append(billRow);

					$.ajax({
						type: "POST",
						url: 'modules/billing/php/billing_ctrl.php?oper=createBill',
						data : $('#flexform').serialize(),
						success: function(txt){
							$('.change-sec').switchSection("changeTo",".sec-3");
						},
						error : function(XMLHttpRequest, textStatus, errorThrown){
							bootbox.hideAll();
							bootbox.alert({
								title : "<?=$Text['ti_error_exec'];?>",
								message : "<div class='alert alert-danger'>"+XMLHttpRequest.responseText+"</div>"})
						},
						complete : function(){
							
						}
					});
    			}
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
				
				
			}
		});


			
		$('#btn-filter-uf')
			.click(function(){
				reloadListings("all");
			})

		$("#input-filter-uf")
			.keyup(function(e){
			if(e.keyCode == 13){ //hit enter
    			reloadListings("all");
			}
		}); 

		//shortcuts for filtering date range. 
		$(".ctx-nav-filter")
			.click(function(){
				var range = $(this).attr("data")
				range = range.split(",");

				fromDate = moment(gToday, "YYYY-MM-DD").subtract(parseInt(range[1]), range[0]).format('YYYY-MM-DD');
		 		
		 		$('#datepicker-to').data("DateTimePicker").setDate(gToday);
				$('#datepicker-from').data("DateTimePicker").setDate(fromDate);

				reloadListings("all");

			})



		/** 
		 *  switching sections can trigger reloading of listings. 
		 */
		$(".sectionSwitchListener")
			.bind("beforeSectionSwitch", function(e, toSection	){
				//alert("before " + toSection)
				reloadListings(toSection)

			})
			.bind("afterSectionSwitch", function(e, toSection){
				//alert("after " + toSection)
				
			})


		/**
		 *	reloads cart and bill listings
		 */
		function reloadListings(sec){

			var uf_id = $("#input-filter-uf").val();
			var from_date = $('#datepicker-from').data("DateTimePicker").getDate();
			var to_date = $('#datepicker-to').data("DateTimePicker").getDate();
			from_date = moment(from_date).format("YYYY-MM-DD");
			to_date = moment(to_date).format("YYYY-MM-DD");

			if (sec == ".sec-3" || sec == "all") { //reload bill listing
					$('#tbl_bill tbody').xml2html('reload',{
						params : 'oper=getBillListing&uf_id='+uf_id+"&from_date="+from_date+"&to_date="+to_date, 
					});
			}

			if (sec == ".sec-1" || sec=="all" ){ //reload cart listing
				$('#tbl_Shop tbody').xml2html('reload',{
					params : "oper=getCartListing&uf_id="+uf_id+"&from_date="+from_date+"&to_date="+to_date, 
				});
			}
		}

			
			
	});  //close document ready
</script>

</head>
<body>

	<div id="headwrap">
		<?php include "../../php/inc/menu.inc.php" ?>
	</div>
	<!-- end of main menu -->

	<!-- sub nav menu -->
	<div class="container">
		<div class="row">
			<nav class="navbar navbar-default" role="navigation" id="ax-submenu">
			  	<div class="navbar-header">
			     	<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#sub-navbar-collapse">
				        <span class="sr-only">Toggle navigation</span>
				        <span class="icon-bar"></span>
				        <span class="icon-bar"></span>
				        <span class="icon-bar"></span>
			      	</button>
	    		</div>

	    		<div class="navbar-collapse collapse" id="sub-navbar-collapse">
		    	
					<div class="col-md-1">
						<button type="button" class="change-sec btn btn-default btn-sm navbar-btn section sec-3" target-section="#sec-1">
		    				<span class="glyphicon glyphicon glyphicon-shopping-cart"></span> View carts
		  				</button>
		  				<button type="button" class="change-sec btn btn-default btn-sm navbar-btn section sec-1" target-section="#sec-3">
		    				<span class="glyphicon glyphicon glyphicon-list"></span> View bills
		  				</button>
					</div>

					<div class="col-md-1">
						<button type="button" class="btn btn-success btn-sm navbar-btn section sec-1" disabled="disabled" id="btn-create-bill">
		    				<span class="glyphicon glyphicon glyphicon-ok-sign"></span> Create bill
		  				</button>
	  				</div>

					
					<div class="col-md-3 section sec-3 sec-1">
						<form class="navbar-form pull-right" role="date">
							<div class="form-group">
		                        <div class='input-group date input-group-sm' id='datepicker-from' >
		                            <input type='text' class="form-control" id="date-from" data-format="dddd, ll" placeholder="From" />
		                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
		                            </span>
		                        </div>
		                    </div>
		                </form>
		            </div>

		            <div class="col-md-3 section sec-3 sec-1">
						<form class="navbar-form" role="date">
							<div class="form-group">
		                        <div class='input-group date input-group-sm' id='datepicker-to' >
		                            <input type='text' class="form-control" name="date-to" data-format="dddd, ll" placeholder="To" />
		                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
		                            </span>
		                        </div>
		                    </div>
		                </form>
	            	</div>

					
					<div class="col-md-2 section sec-3 sec-1">
						<form class="navbar-form" role="search">
				      		<div class="input-group input-group-sm">
						    	<input type="text" class="form-control" id="input-filter-uf" placeholder="Filter UF">
						      	<span class="input-group-btn">
						        	<button class="btn btn-default btn-sm" id="btn-filter-uf"><span class="glyphicon glyphicon-search"></span></button>
						      	</span>
							</div>
						</form>
					</div>


					<div class="btn-group col-md-1 section sec-1 sec-3">
						<button type="button" class="btn btn-default btn-sm navbar-btn dropdown-toggle" data-toggle="dropdown">
		    				Actions <span class="caret"></span>
		  				</button>
						<ul class="dropdown-menu" role="menu">
							<li><a href="javascript:void(null)" class="ctx-nav ctx-nav-export-bill"><span class="glyphicon glyphicon-export"></span> <?=$Text['btn_export'];?></a></li>
	 						<li class="level-1-indent"><a href="javascript:void(null)" data="Accounting,csv" class="ctx-nav ctx-nav-export-bill"> Accounting (csv)</a></li>
	 						<li class="level-1-indent"><a href="javascript:void(null)" data="SEPA,xml" class="ctx-nav ctx-nav-export-bill"> SEPA</a></li>
	 						<!--li class="level-1-indent"><a href="javascript:void(null)" data="Billitems,csv" class="ctx-nav ctx-nav-export-bill"> Shop items (csv)</a></li>
	 						<li class="level-1-indent"><a href="javascript:void(null)" data="advanced" class="ctx-nav ctx-nav-export-bill"> Advanced</a></li-->
						</ul>
						
					</div>



	  				<div class="btn-group col-md-1 pull-right section sec-3 sec-1">
						<button type="button" class="btn btn-default btn-sm navbar-btn dropdown-toggle" data-toggle="dropdown">
							<span class="glyphicon glyphicon-filter"></span>&nbsp; <span class="caret"></span>
						</button>
						<ul class="dropdown-menu" role="menu">
							<li><a href="javascript:void(null)">Filter</a></li>
						    <li class="level-1-indent"><a href="javascript:void(null)" data="days,0" class="ctx-nav-filter">Today</a></li>
							<li class="level-1-indent"><a href="javascript:void(null)" data="weeks,1" class="ctx-nav-filter">Last week</a></li>
							<li class="level-1-indent"><a href="javascript:void(null)" data="months,1" class="ctx-nav-filter">Last month</a></li>
							<li class="level-1-indent"><a href="javascript:void(null)" data="months,3" class="ctx-nav-filter">Last 3 month</a></li>

						</ul>
					</div>

		      	</div>
			</nav>
		</div>
	</div><!-- end sub nav -->


	<div class="container" id="ax-title">
		<div class="row">
 			<div class="col-md-12 section sec-1 sec-2 sec-3 sec-4">
		    	<h1 class="section sec-1">Overview carts </h1>
				<h3 class="section sec-2"><a href="javascript:void(null)" class="change-sec" target-section="#sec-1">Overview carts</a> <span class="glyphicon glyphicon-chevron-left sp-sm" target-section="#sec-1"></span> Purchase for UF<span class="setUfId"></span>, cart #<span class="setCartId"></span> on <span class="setShopDate"></span></h3>
		    	<h1 class="section sec-3">Overview bills</h1>
		    	<h3 class="section sec-4"><a href="javascript:void(null)" class="change-sec" target-section="#sec-3">Overview bills</a> <span class="glyphicon glyphicon-chevron-left change-sec" target-section="#sec-3"></span> Bill - #<span class="setBillID"></span> UF<span class="setUFID"></span> </h3>
		    </div>
		</div>
	</div>


	<!-- CART + DETAILS -->

	<div class="container">	
		<div class="row">
			<div id="shop_list" class="section sec-1">		

				<table id="tbl_Shop" class="table table-hover">
					<thead>
						<tr>
							<th></th>
							<th>Bill</th>
							<th>Cart id <span class="ui-icon ui-icon-triangle-2-n-s floatLeft"></span></th>
							<th><?php echo $Text['uf_long']; ?><span class="ui-icon ui-icon-triangle-2-n-s floatLeft"></span></th>
							<th><p class="text-center"><?php echo $Text['purchase_date'];?> <span class="ui-icon ui-icon-triangle-2-n-s floatLeft"></span></p></th>
							<th><p class="text-center"><?php echo $Text['validated'];?></p></th>
							<th><p class="text-right"><?php echo $Text['total'];?></p></th>
						</tr>
					</thead>
					<tbody>
						<tr class="clickable" id="shop_{id}" shopId="{id}" ufId={uf_id} dateForShop="{date_for_shop}" validated="{ts_validated}">
							<td><input type="checkbox" name="bulk_cart"></td>
							<td>{bill_id}</td>
							<td>{id}</td>
							<td>{uf_id} {uf_name}</td>
							<td class="text-center">{date_for_shop}</td>
							<td class="text-center">{ts_validated}</td>
							<td><p class="text-right">{purchase_total}<?=$Text['currency_sign'];?></p></td>
						</tr>
					</tbody>
				</table>
			</div>

	
		
		
			<div id="shop_detail" class="section sec-2">
				<table id="tbl_purchaseDetail" class="table" currentShopId="" currenShopDate="">
					<thead>
						<tr>
							<th><p><?php echo $Text['name_item'];?></p></th>	
							<th><p><?php echo $Text['provider_name'];?></p></th>					
							<th><p class="text-right"><?php echo $Text['quantity_short']; ?></p></th>
							<th><p><?php echo $Text['unit'];?></p></th>
							<th><p class="text-right"><?php echo $Text['revtax_abbrev']; ?></p></th>
							<th><p class="text-right"><?php echo $Text['iva']; ?></p></th>
							<th><p class="text-right"><?php echo $Text['unit_price'];?></p></th>
							<th><p class="text-right"><?php echo $Text['price'];?></p></th>
						</tr>
					</thead>
					<tbody>
						<tr class="detail_shop_{cart_id}">
							<td>{name}</td>
							<td>{provider_name}</td>
							<td><p class="text-right qu">{quantity}</p></td>
							<td>{unit}</td>
							<td><p class="text-right">{rev_tax_percent}%</p></td>
							<td><p class="text-right">{iva_percent}%</p></td>
							<td><p class="text-right up">{unit_price}</p></td>
							<td><p class="text-right itemPrice" iva="{iva_percent}" revTax="{rev_tax_percent}"></p></td>	

						</tr>						
					</tbody>
					<tfoot>
						<tr>
							<td colspan="6">&nbsp;</td>
							<td><p class="text-right ax-txt-strong"><?php echo $Text['total'];?></p></td>
							<td><p id="total" class="text-right ax-txt-strong"></p></td>
						</tr>
						
						<tr>
							<td colspan="6">&nbsp;</td>
							<td><p class="text-right"><?php echo $Text['incl_iva']; ?></p></td>
							<td><p id="total_iva" class="text-right"></p></td>
						</tr>
						<tr>
							<td colspan="6">&nbsp;</td>
							<td><p class="text-right"><?php echo $Text['incl_revtax']; ?></p></td>
							<td><p id="total_revTax" class="text-right"></p></td>
						</tr>

					</tfoot>
				</table>
			</div>	
		</div>			
	</div>
	<!-- end of cart + details  -->


	<!-- BILLS + DETAILS -->
	<div class="container">	
		<div class="row">
			<div id="bill_list" class="section sec-3">		
				<table id="tbl_bill" class="table table-hover">
					<thead>
						<tr>
							<th></th>
							<th>Bill</th>
							<th>Date</th>
							<th>UF</th>
							<th>Reference</th>
							<th>Notes</th>
							<th>Operator</th>
							<th><p class="text-right">Total</p></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<tr class="clickable" validated="{ts_validated}" billId="{id}" ufId="{uf_id}">
							<td><input type="checkbox" name="bulk_bill"></td>
							<td>{id}</td>
							<td>{date_for_bill}</td>
							<td>{uf_id} {uf_name}</td>
							<td>{ref_bill}</td>
							<td>{description}</td>
							<td>{operator}</td>
							<td><p class="text-right">{total}<?=$Text['currency_sign'];?></p></td>
							<td>
								<span class="glyphicon glyphicon-remove-circle btn-del-bill pull-right" title="<?=$Text['btn_del'];?>"></span>
							</td>
						</tr>
					</tbody>
				</table>
			</div>


			<div id="bill_detail" class="section sec-4">		
				<table id="tbl_billDetail" class="table table-hover table-bordered">
					<thead>
						<tr>
							<th>Cart</th>
							<th>Date</th>
							<th>Product</th>
							<th>Qu</th>
							<th>Unit</th>
							<th>Price</th>
							<th>IVA</th>
							<th>Rev</th>
							<th>Total</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>{cart_id}</td>
							<td>{date_for_shop}</td>
							<td><span title="{provider_name}">{product_name}</span></td>
							<td>{quantity}</td>
							<td>{unit}</td>
							<td>{unit_price}</td>
							<td>{iva_percent}</td>
							<td>{rev_tax_percent}</td>
							<td><p class="text-right">{total}<?=$Text['currency_sign'];?></p></td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="6">&nbsp;</td>
							<td><p class="text-right">Total of IVA group</p></td>
							<td><p class="text-right">{iva_percent}%</p></td>
							<td><p class="text-right">{iva_sale}<?=$Text['currency_sign'];?></p></td>
						</tr>
					</tfoot>

				</table>
			</div>
		</div>




		</div>
	</div>



<div class="sec-4">
	<form id="flexform"></form>


	<iframe name="dataFrame" id="dataFrame" style="display:none"></iframe>
</div>

<div class="sectionSwitchListener"></div>

</body>
</html>