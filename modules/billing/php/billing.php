<?php include "../../../php/inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_sales'] ;?></title>

    <link href="../../../js/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../../css/aixcss.css" rel="stylesheet">
    <link href="../../../js/ladda/ladda-themeless.min.css" rel="stylesheet">



	<?php if (isset($_SESSION['dev']) && $_SESSION['dev'] == true ) { ?>
	    <script type="text/javascript" src="../../../js/jquery/jquery.js"></script>
   	    <script type="text/javascript" src="../../../js/bootstrap/js/bootstrap.min.js"></script>
	   	<script type="text/javascript" src="../../../js/aixadautilities/jquery.aixadaXML2HTML.js" ></script>
	   	<script type="text/javascript" src="../../../js/aixadautilities/jquery.aixadaSwitchSection.js" ></script>
	   	
	   	<script type="text/javascript" src="../../../js/bootbox/bootbox.js"></script>
	   	<script type="text/javascript" src="../../../js/ladda/spin.min.js"></script>
	   	<script type="text/javascript" src="../../../js/ladda/ladda.min.js"></script>
	   	<script type="text/javascript" src="../../../js/datepicker/moment-with-langs.min.js"></script>
	   	
		<script type="text/javascript" src="../../../js/datepicker/bootstrap-datetimepicker.min.js"></script>

	   	<script type="text/javascript" src="../../../js/aixadautilities/jquery.aixadaUtilities.js"></script>
	   	<script type="text/javascript" src="../../../js/aixadautilities/jquery.aixadaExport.js" ></script>

    <?php }?>
   		

 	   
   
	<script type="text/javascript">
	$(function(){

		

		//saves the selected purchase row
		var gSelShopRow = null; 


		//coming from other page
		var gDetail = (typeof $.getUrlVar('detailForCart') == "string")? $.getUrlVar('detailForCart'):false;


		//order overview filter option
		var gBackTo = (typeof $.getUrlVar('lastPage') == "string")? $.getUrlVar('lastPage'):false;
		


		$('.section').hide();


		$('.change-sec')
			.switchSection("init");

		$('.sec-3').show();

		bootbox.setDefaults({
			locale:"<?=$language;?>"
		})



		$('#datepicker-from').datetimepicker({
				pickTime:false,
				startDate : '1/1/2014',
			}).on("change.dp",function(e){
			})

		$('#datepicker-to').datetimepicker({
				pickTime:false,
				startDate : '1/1/2014',
			}).on("change.dp",function(e){
			})
		



		/********************************************************
		 *    BILL LISTING
		 ********************************************************/
		$('#tbl_bill tbody').xml2html('init',{
				url : 'billing_ctrl.php',
				params : 'oper=getBillListing&uf_id=102', 
				loadOnInit : true, 
				beforeLoad : function(){
				},
				rowComplete : function(rowIndex, row){
					
				},
				complete : function(){

				}
		});


		$('#tbl_billDetail tbody').xml2html('init',{
				url : 'billing_ctrl.php',
				params : 'oper=getBillDetail', 
				loadOnInit : false, 
				beforeLoad : function(){
				},
				rowComplete : function(rowIndex, row){
					
				},
				complete : function(){

				}
		});



		$('#tbl_bill tbody')
			.on('click', 'tr', function(e){
	
					
				$('#tbl_billDetail tbody').xml2html('reload',{
					params : 'oper=getBillDetail&bill_id='+$(this).attr('billId')
				});

				$(".setBillID").text($(this).attr("billId"));
				$(".setUFID").text($(this).attr("ufId"));

				$('.change-sec').switchSection("changeTo",".sec-4");
		});




		/********************************************************
		 *    PURCHASE LISTING
		 ********************************************************/
		var shopDateSteps = 1;
		var srange = 'year';

		


		//load purchase listing
		$('#tbl_Shop tbody').xml2html('init',{
				url : '../../../php/ctrl/Shop.php',
				params : 'oper=getShopListing&filter=prev3Month', 
				loadOnInit : true, 
				beforeLoad : function(){
				},
				rowComplete : function(rowIndex, row){
					var validated = $(row).children().eq(4).text();

					if (validated == '0000-00-00 00:00:00'){
						$(row).children().eq(4).html("<p class='text-center'>-</p>");	
					} else {
						$(row).children().eq(4).html('<span class="ui-icon ui-icon-check tdIconCenter" title="<?php echo $Text['validated_at'];?>: '+validated+'"></span>');
					}	

					if (gDetail > 0 && $(row).attr('shopId') == gDetail){
						gSelShopRow = $(row); 
					}	
				},
				complete : function(){

					if (gSelShopRow != null) {
						//gSelShopRow.trigger('click');
					}

				}
		});

		


		$('#tbl_Shop tbody')
			.on('click', 'tr', function(e){
	
				//if (gSelShopRow != null) gSelShopRow.removeClass('ui-state-highlight');
			
				//$(this).addClass('ui-state-highlight');
		
				gSelShopRow = $(this); 
					
				$('#tbl_purchaseDetail tbody').xml2html('reload',{
					params : 'oper=getShopCart&shop_id='+$(this).attr('shopId')
				});

				//$('.setUfId').text($(this).attr('ufId'));
				//$('.setCartId').text($(this).attr('shopId'));
				//var cd = $.getCustomDate($(this).attr('dateForShop'));
				//$('.setShopDate').text(cd);
				/*
				var dateValidated = $(this).attr('validated');
				$('.setValidateStatus').removeClass('noRed okGreen');

				if (dateValidated == "0000-00-00 00:00:00"){
					$('.setValidateStatus').addClass('noRed').text('<?php echo $Text['not_yet_val']; ?>');
				} else {
					var opUf = $(this).attr('operatorUf'); 
					var opName = $(this).attr('operatorName'); 
					var dv = $.getCustomDate(dateValidated.substring(0,10),'DD M m, yy');
					$('.setValidateStatus')
						.addClass('okGreen')
						.html('<span title="<?php echo $Text['val_by'];?> '+opUf+' '+opName+'">'+dv+'</span>');
				}*/
	
				$('.change-sec').switchSection("changeTo",".sec-2");
		});
		

		//prevent page change when checkbox provider
		$('#tbl_Shop tbody')
			.on('click','td:first-child, input',  function(e){
				e.stopPropagation();
			})



		//download selected as zip
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
						url: 'billing_ctrl.php?oper=createBill',
						data : $('#flexform').serialize(),
						success: function(txt){
							
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
				url : '../../../php/ctrl/Shop.php',
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



			//print incidents accoring to current incidents template in new window or download as pdf
			/*$("#btn_print")
			.button({
				icons: {
					primary: "ui-icon-print",
		        	secondary: "ui-icon-triangle-1-s"
				}
		    })
		    .menu({
				content: $('#printOptionsItems').html(),	
				showSpeed: 50, 
				width:180,
				flyOut: true, 
				itemSelected: function(item){	
					
					var link = $(item).attr('id');

					var shopId = gSelShopRow.attr('shopId');
    				var date = gSelShopRow.attr('dateForShop');
    				var op_name = gSelShopRow.attr('operatorName');
    				var op_uf = gSelShopRow.attr('operatorUf');
    			
    				
					
					switch (link){
						case "printWindow": 
							printWin = window.open('tpl/<?=$tpl_print_bill;?>?shopId='+shopId+'&date='+date+'&operatorName='+op_name+'&operatorUf='+op_uf);
			    			
							printWin.focus();
							printWin.print();
							break;
		
						case "printPDF": 
							window.frames['dataFrame'].window.location = 'tpl/<?=$tpl_print_bill;?>?shopId='+shopId+'&date='+date+'&operatorName='+op_name+'&operatorUf='+op_uf+'&asPDF=1&outputFormat=D' 
							break;
					}
									
				}//end item selected 
			});//end print menu
	    	*/
		
			
			$('#btn-filter-uf')
				.click(function(){
					var uf_id = $("#input-filter-uf").val();
					if (isNaN(uf_id) || uf_id < 1){
						$('#tbl_Shop tbody').xml2html('reload',{
							params : 'oper=getShopListing&filter=prev3Month'
						})
					} else {
					
						$('#tbl_Shop tbody').xml2html('reload',{
							params : 'oper=getShopListing&uf_id='+uf_id+'&filter=all'
						})
					}

				})


			
			
	});  //close document ready
</script>

</head>
<body>

	<div id="headwrap">
		<?php include "../../../php/inc/menu.inc.php" ?>
	</div>
	<!-- end of headwrap -->

	<div class="container sec-1 sec-3">
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
		    	
					<button type="button" class="btn btn-success btn-sm navbar-btn section sec-1" id="btn-create-bill">
	    				<span class="glyphicon glyphicon glyphicon-ok-sign"></span> Create bill
	  				</button>

					<div class="btn-group">
						<button type="button" class="btn btn-default btn-sm navbar-btn dropdown-toggle" data-toggle="dropdown">
							View <span class="caret"></span>
						</button>
						<ul class="dropdown-menu" role="menu">
							<li><a href="#sec-1" class="change-sec">Carts</a></li>
							<li><a href="#sec-3" class="change-sec">Bills</a></li>
						</ul>
					</div>



	  				<div class="btn-group pull-right">
						<button type="button" class="btn btn-default btn-sm navbar-btn dropdown-toggle" data-toggle="dropdown">
							<span class="glyphicon glyphicon-filter"></span> <span class="caret"></span>
						</button>
						<ul class="dropdown-menu" role="menu">
							<li><a href="#"><?=$Text['filter_incidents'];?></a></li>
						    <li class="level-1-indent"><a href="#today" class="ctx-nav-filter"><?=$Text['filter_todays'];?></a></li>
							<li class="level-1-indent"><a href="#week" class="ctx-nav-filter">Last week</a></li>
							<li class="level-1-indent"><a href="#month" class="ctx-nav-filter">Last month</a></li>
							<li class="level-1-indent"><a href="#range" class="ctx-nav-filter">Date range</a></li>
						</ul>
					</div>

					<p class="navbar-text navbar-right">&nbsp;</p>

					<form class="navbar-form navbar-right" role="date">
						<div class="form-group">
	                        <div class='input-group date input-group-sm' id='datepicker-to' >
	                            <input type='text' class="form-control max-width" id="inputField" data-format="dddd, ll" placeholder="To" />
	                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
	                            </span>
	                        </div>
	                    </div>
	                </form>
					<p class="navbar-text navbar-right ax-reduce-margin">-</p>
					<form class="navbar-form navbar-right" role="date">
						<div class="form-group">
	                        <div class='input-group date input-group-sm' id='datepicker-from' >
	                            <input type='text' class="form-control max-width" id="inputField" data-format="dddd, ll" placeholder="From" />
	                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
	                            </span>
	                        </div>
	                    </div>
	                </form>
					
	                

		    		<form class="navbar-form navbar-right" role="search">
			      		<div class="input-group input-group-sm">
					    	<input type="text" class="form-control max-width" id="input-filter-uf" placeholder="Filter UF">
					      	<span class="input-group-btn">
					        	<button class="btn btn-default" id="btn-filter-uf" type="button"><span class="glyphicon glyphicon-search"></span>&nbsp;</button>
					      	</span>
						</div>
					</form>

		  			
					
					

		      	</div>
			</nav>
		</div>
	</div><!-- end sub nav -->

	<div class="container" id="ax-title">
		<div class="row">
 			<div class="col-md-10 section sec-1 sec-2 sec-3 sec-4">
		    	<h1 class="section sec-1">Billing - Overview carts </h1>
		    	<h1 class="section sec-3">Billing - Overview</h1>
		    	<h1 class="section sec-4"><span class="glyphicon glyphicon-chevron-left change-sec" target-section="#sec-3"></span> Bill - #<span class="setBillID"></span> <span class="setUFID"></span> </h1>
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
						<tr class="clickable" id="shop_{id}" shopId="{id}" ufId={uf_id} dateForShop="{date_for_shop}" validated="{ts_validated}" operatorName="{operator_name}" operatorUf="{operator_uf}">
							<td><input type="checkbox" name="bulk_cart"></td>
							<td>{bill_id}</td>
							<td>{id}</td>
							<td>{uf_id} {uf_name}</td>
							<td>{date_for_shop}</td>
							<td>{ts_validated}</td>
							<td><p class="text-right">{purchase_total}<?=$Text['currency_sign'];?></p></td>
						</tr>
					</tbody>
				</table>
			</div>

	
		
		
			<div id="shop_detail" class="section sec-2">

				<h3><?php echo $Text['purchase_uf']; ?><span class="setUfId"></span>, <span class="setShopDate"></span></h3>
				<table id="tbl_purchaseDetail" class="table" currentShopId="" currenShopDate="">
					<thead>
						<tr>
							<th><p>Validated</p></th>
							<th><p>Cart</p></th>
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
							<td>{ts_validated}</td>
							<td>{cart_id}</td>
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
							
							<td colspan="9" class="boldStuff"><?php echo $Text['total'];?></td>
							<td id="total" class="boldStuff dblBorderBottom dblBorderTop"></td>
						</tr>
						
						<tr>
							<td colspan="9"><?php echo $Text['incl_iva']; ?></td>
							<td id="total_iva" class="dblBorderTop"></td>
						</tr>
						<tr>
						
							<td colspan="9"><?php echo $Text['incl_revtax']; ?></td>
							<td id="total_revTax"></td>
						</tr>

						<tr>
							<td colspan="8"><p>&nbsp;</p></td>
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
							<th>Total</th>
						</tr>
					</thead>
					<tbody>
						<tr validated="{ts_validated}" billId="{id}" ufId="{uf_id}">
							<td><input type="checkbox" name="bulk_bill"></td>
							<td>{id}</td>
							<td>{date_for_bill}</td>
							<td>{uf_id} {uf_name}</td>
							<td>{ref_bill}</td>
							<td>{description}</td>
							<td>{operator}</td>
							<td><p class="text-right">{total}<?=$Text['currency_sign'];?></p></td>
						</tr>
					</tbody>
				</table>
			</div>


			<div id="bill_detail" class="section sec-4">		
				<table id="tbl_billDetail" class="table table-hover">
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
				</table>
			</div>
		</div>




		</div>
	</div>





<!-- end of wrap -->
<!-- / END -->
<div class="sec-4">
	<form id="flexform"></form>


<iframe name="dataFrame" style="display:none"></iframe>


</div>


</body>
</html>