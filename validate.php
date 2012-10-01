<?php include "php/inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_validate'] ;?></title>


	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
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
	   	<script type="text/javascript" src="js/js_for_validate.min.js"></script>
    <?php }?>
   
    <script type="text/javascript" src="js/jqueryui/i18n/jquery.ui.datepicker-<?=$language;?>.js" ></script>
 	<script type="text/javascript" src="js/aixadacart/i18n/cart.locale-<?=$language;?>.js" ></script>
 	
   
  
	<script type="text/javascript">
	$(function(){

			//get the operator user id to prevent own validation
			var gTornUfId = <?=get_session_uf_id();?>;


			//stores todays date
			var gToday = null;

			$.getAixadaDates('getToday', function (date){
				gToday = date[0]; 
				//var today = $.datepicker.parseDate('yy-mm-dd', date[0]);	
			});	
			


			/**
			 *	UF LISTING FUNCTIONALITY
			 */
			$('#uf_cart_select').xml2html('init',{
					offSet	: 1,
					url 	: 'php/ctrl/Validate.php',
					params 	: 'oper=getUFsCartCount',
					rowComplete : function(rowIndex, row){
					
						//show number of non_validated carts for uf
						var non_validated_carts = $(row).attr('cc');
						non_validated_carts = (non_validated_carts > 0)? '&nbsp;&nbsp;(#'+non_validated_carts+')':'';
						$(row).append(non_validated_carts);

						//dim out own uf
						if ($(row).val() == gTornUfId){	//cannot validate yourself
							$(row).addClass('dim60');
						}
					}, 
					loadOnInit:true
				
				}).change(function(){
					//get the id of the uf
					var uf_id = $("option:selected", this).val(); 
					
					if (uf_id <=0) {
						resetFields();
						resetDeposit();
						return false; 
						
					} else if (uf_id == gTornUfId){ //cannot validate your own cart
						$.showMsg({
							msg:"<?php echo $Text['msg_err_validate_self'];?>",
							type: 'error'});
						resetFields();
						resetDeposit();
						return false;
					} 
	
	
					//activate the deposit pane
					$('.insert_uf_id').html('<strong>'+uf_id+'</strong>');
					$('.showCartDate').removeClass('ui-state-highlight dim40').text('');
					$('.cartTitle').show();
					$('.noCartTitle, .validatedCartTitle').hide();
					$('#deposit_submit').button('enable');
					$('#deposit_amount, #deposit_note, #search').attr('disabled',false);
	
					//how many carts are there? 
					$('#tbl_Shop tbody').xml2html('reload',{
						url : 'php/ctrl/Validate.php',
						params : 'oper=getNonValidatedCarts&uf_id='+uf_id
					});
			}); //end of listing 

			

			//load purchase listing: how may carts does this uf have?
			$('#tbl_Shop tbody').xml2html('init',{
					url : 'php/ctrl/Shop.php',
					loadOnInit : false, 
					rowComplete : function(rowIndex, row){
						var validated = $(row).children().eq(2).text();
						if (validated == "0000-00-00 00:00:00"){
							$(row).children().eq(2).html('<span class="ui-state-error ui-corner-all"><?php echo $Text['not_validated'];?></span>');
						}
					}, 
					complete: function(rowCount){
						//more than one cart to validate
						if (rowCount > 1){
							$('#dialog_select_cart').dialog('open');

						} else if (rowCount == 0){
							$.showMsg({
								msg:"<?php echo $Text['nothing_to_val'] ;?>",
								type: 'warning'});
								$('#cartLayer').aixadacart('resetCart');
								//resetFields();
						
						//one, should be today!
						} else {
							var cart_id = $('#tbl_Shop tbody tr').attr('shopId');
							var date_for_shop = $('#tbl_Shop tbody tr').attr('dateForShop');
							var css = (date_for_shop != gToday)? 'ui-state-highlight':'dim40'; 
							var df = '('+$.getCustomDate(date_for_shop, 'D, d M, yy')+')';
							
							$('.showCartDate').removeClass('ui-state-highlight dim40').addClass(css).text(df);
							
																			
							if (cart_id > 0 ){
								loadCart(cart_id, date_for_shop);
							} else {
								$('.cartTitle, .validatedCartTitle').hide();
								$('.noCartTitle').show();
							}
						}
					}
			});



		
			/**
			 * MULTIPLE CARTS TO VALIDATE / SELECT
			 */	
			$('#dialog_select_cart').dialog({
				autoOpen:false,
				width:500,
				buttons: {  
					"<?=$Text['btn_cancel'];?>"	: function(){
						
						$( this ).dialog( "close" );
						} 
				}
			});

			$('#tbl_Shop tbody tr, #tbl_cart_listing tbody tr')
			.live('mouseover', function(){
				$(this).removeClass('highlight').addClass('ui-state-hover');
				
			})
			.live('mouseout',function(){
				$(this).removeClass('ui-state-hover');
				
			})
			.live('click',function(e){

				$('.showCartDate').removeClass('ui-state-highlight dim40');
				
				var uf_id = $(this).attr('ufId'); 
				var validated = $(this).attr('validated');
				var cart_id = $(this).attr('shopId'); 
				var date_for_shop = $(this).attr('dateForShop'); 

				//means we come from cart observe
				if (uf_id > 0){
					$('#uf_cart_select').val(uf_id).attr('selected','selected');
					$('.insert_uf_id').html('<strong>'+uf_id+'</strong>');
					
					$('#deposit_submit').button('enable');
					$('#deposit_amount, #deposit_note, #search').attr('disabled',false);
				}

				if (validated == "0000-00-00 00:00:00") {
					$('.cartTitle').show();
					$('.noCartTitle, .validatedCartTitle').hide();

					var css = (date_for_shop != gToday)? 'ui-state-highlight':'dim40'; 
					var df = '('+$.getCustomDate(date_for_shop, 'D, d M, yy')+')';
					$('.showCartDate').addClass(css).text(df);

					
					loadCart(cart_id, date_for_shop);
				} else {					
					resetFields();
					$.showMsg({
						msg:"<?php echo $Text['msg_already_validated']; ?>",
						width:500,
						buttons: {
							"Yes":function(){						
								window.location.href = "report_shop.php?detailForCart="+cart_id+"&lastPage=validate";
								$( this ).dialog( "close" );
							},
							"Cancel" : function(){
								$( this ).dialog( "close" );
							}
						},
						type: 'warning'});
				}
			
				
				$('#dialog_select_cart').dialog('close');
			});
			

			
			//init tabs
			$("#tabs").tabs();
			
			//init cart 
			$('#cartLayer').aixadacart("init",{
				saveCartURL : 'php/ctrl/Validate.php',
				cartType	: 'simple',
				btnType		: 'validate',
				saveOnDelete: false,
				submitSuccess : function (msg){
					$('#tbl_cart_listing tbody').xml2html('reload'); 
														
					//empty the cart
					$(this).aixadacart("resetCart");	
				}
			});


	
			
		    //init xml2html product search
			$('#product_list_search tbody').xml2html('init',{
					url : 'php/ctrl/ShopAndOrder.php'
				});

				
			/**
			 *	MAKE A DEPOSIT
			 */
			$('#deposit_submit')
				.button()
				.click(function(){

					var description = $('#deposit_note').val();
					var uf_account_id = 1000 + new Number($("#uf_cart_select option:selected").val());
					var quantity = $.checkNumber($('#deposit_amount'), '', 2);		
					
					if (!quantity) { 
						$.showMsg({
									msg:"<?php echo $Text['msg_enter_deposit_amount'];?>",
									type: 'warning'});
						return false;
						
					} else if (uf_account_id <= -4){
						$.showMsg({
									msg:"<?php echo $Text['msg_please_set_ufid_deposit'];?>",
									type: 'error'});
						return false; 
					}
					
	
					$('#deposit_submit').button('disable');
				
					$.ajax({
						type: "POST",
						url: "php/ctrl/Account.php?oper=deposit&account_id="+uf_account_id+"&quantity="+quantity+"&description="+description,	
						beforeSend : function (){
							$('#deposit .loadAnim').show();
						},	
						success: function(msg){
							$.updateTips("#depositMsg", "success", "<?=$Text['msg_deposit_success'];?>" );
							resetDeposit();
							
							$('#dailyStats tbody').xml2html('reload');		
							$('#list_account tbody').xml2html('reload');
						},
						error : function(XMLHttpRequest, textStatus, errorThrown){
							$.updateTips("#depositMsg","error", XMLHttpRequest.responseText);
						},
						complete : function(msg){
							$('#deposit_submit').button('enable');
							$('#deposit .loadAnim').hide();
						}
					}); //end ajax

			});



			

			/**
			 * 	MONITOR Money, daily stats, negative ufs, stock
			 */
			 $('#list_account tbody').xml2html('init',{
					url		: 'php/ctrl/Account.php',
					params	: 'oper=latestMovements',
					loadOnInit: true,
					rowComplete : function (rowIndex, row){
						$.formatQuantity(row);						
					}, 
					complete : function (rowCount){
						$('#list_account tbody tr:even').addClass('rowHighlight'); 
					}
			});


			
  			 //carts to validate today
			 $('#tbl_cart_listing tbody').xml2html('init',{
					url		: 'php/ctrl/Shop.php',
					params : 'oper=getShopListing&filter=today', 
                    autoReload: 100200,
                    loadOnInit:true, 
                    beforeLoad : function(){
						//$('#dailyStats .loadAnim').show();
					},
					rowComplete : function(rowIndex, row){
						var validated = $(row).children().eq(3).text();

						if (validated == '0000-00-00 00:00:00'){
							$(row).children().eq(3).html("-");	
						} else {
							$(row).children().eq(3).html('<span class="ui-icon ui-icon-check tdIconCenter" title="<?php echo $Text['validated_at']; ?>: '+validated+'"></span>');
						}		
					},
					complete : function(){
						$('tr:even', this).addClass('rowHighlight');
						//$('#dailyStats .loadAnim').hide();
					}
			});
				
				
  			 //negative ufs
			 $('#negative_ufs tbody').xml2html('init',{
					url		: 'php/ctrl/Account.php',
					params	: 'oper=getNegativeAccounts',
					loadOnInit: true,
					rowName : 'account',
                    autoReload: 103020, 
                    beforeLoad : function(){
						$('#negative_ufs .loadAnim').show();
					},
					complete : function(){
						$('#negative_ufs .loadAnim').hide();
					}
			});

  			 //daily stats
			 $('#dailyStats tbody').xml2html('init',{
					url		: 'php/ctrl/Account.php',
					params	: 'oper=getIncomeSpendingBalance',
                 	//autoReload: 100200, 
                 	loadOnInit:true,
                 	beforeLoad : function(){
						$('#dailyStats .loadAnim').show();
					},
					complete : function(){
						$('#dailyStats .loadAnim').hide();
					}
			});	

			//negative stock
			 $('#min_stock tbody').xml2html('init',{
					url		: 'php/ctrl/Shop.php',
					params	: 'oper=getProductsBelowMinStock',
					loadOnInit: false,
                    autoReload: 100010, 
                    beforeLoad : function(){
						$('#min_stock .loadAnim').show();
					},
					complete : function(){
						$('#min_stock .loadAnim').hide();
					},
					rowComplete : function(rowIndex, row){
						//reformat numbers to two decimal places
						var currentStock = new Number($(row).children().last().text());
						var minStock = new Number($(row).children().eq(3).text());
						
						$(row).children().last().text(currentStock.toFixed(2));
						$(row).children().eq(3).text(minStock.toFixed(2));
					},
			});


			$('.left-icons')
			.bind("mouseenter", function(){
				if ($(this).hasClass('ui-icon-triangle-1-s')){
					$(this).removeClass('ui-icon-triangle-1-s').addClass('ui-icon-circle-triangle-s');
				} else {
					$(this).removeClass('ui-icon-triangle-1-e').addClass('ui-icon-circle-triangle-e');
				}
			})
			.bind("mouseleave", function(){
				if ($(this).hasClass('ui-icon-circle-triangle-s')){
					$(this).removeClass('ui-icon-circle-triangle-s').addClass('ui-icon-triangle-1-s');
				} else {
					$(this).removeClass('ui-icon-circle-triangle-e').addClass('ui-icon-triangle-1-e');
				}
				
			})
			.bind("click", function(){
				$(this).parent().next().toggle();
				if ($(this).hasClass('ui-icon-circle-triangle-s')){
					$(this).removeClass('ui-icon-circle-triangle-s').addClass('ui-icon-circle-triangle-e');
				} else {
					$(this).removeClass('ui-icon-circle-triangle-e').addClass('ui-icon-circle-triangle-s');
				}
			});

			

			

			/**
			 *	init ufs for account deposit select 
			 */
			$('#uf_account_select').hide();	
			$('#toggle_uf_account_select').click(function(){
				$('#uf_account_select').toggle();
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
						  	$('#product_list_search tbody').xml2html('reload',{
								params: 'oper=getShopProducts&date='+getSelectedDate()+'&like='+searchStr,
								rowComplete : function(rowIndex, row){	//updates quantities for items already in cart
									var id =  $(row).attr("id"); 
									var qu = $("#cart_quantity_"+id).val();
									$("#quantity_"+id).val(qu);
								}							
							});		
						} else {
							//delete all product entries in the table if we are below minLength; but 
							$('#product_list_search tbody').xml2html("removeAll");						
						}
				//prevent default event propagation. once the list is build, just stop here. 		
				e.preventDefault();
			}); //end autocomplete
			

			/**
			 * attach event listeners for the product input fields; change of quantity will put the
			 * item into the cart. 
			 */ 
			$('.product_list tbody').find("input").live("change", function (e){						
										
									//check if a cart/UF is selected, only then can one add items
									if ($('#uf_cart_select option:selected').val() > 0){
				
										//retrieve the current table row where quantity has been changed
										var row = $(this).parents("tr");

										//don't add nonsense values
										var qu = $(this).val();
										if (isNaN(parseFloat(qu.replace(",",".")))) {
											$(this).val(0);
											return false;
										}
										
										//if quantity has changed, add it to the cart.
										$('#cartLayer').aixadacart("addItem",{
												id 				: $(row).attr("id"),
												isPreorder 		: false, 
												name 			: $("td.item_name", row).text(),
												provider_name 	: $("td.item_provider_name", row).text(),
												price 			: parseFloat($("td.item_price", row).text()),
												quantity 		: $(this).val(),
												unit 			: $("td.item_unit", row).text(),
												rev_tax_percent : parseFloat( $("td.item_rev_tax_percent", row).text()),
												iva_percent		: $("td.item_iva_percent", row).text()
												
										}); //end addItem to cart	
										

										//switch back to item listing
										$("#tabs").tabs('select',0);					

									//if no cart is selected 
									} else {
										
										$.showMsg({
											msg: "<?php echo $Text['msg_select_cart_first'];?>",
											type: 'error'
											});
									}					
			});//end event listener for product list 




			
			/**
			 * resets all input fields 
			 */
			function resetFields(){
				
				$('#cartLayer').aixadacart('resetCart');		
				$('.cartTitle').show();
				$('.noCartTitle, .validatedCartTitle').hide();
								
				$('.insert_uf_id').html('<strong>??</strong>');
				$('.showCartDate').removeClass('ui-state-highlight dim40').text('');
				$('#uf_cart_select').val(-10);

				$('#deposit_submit').button('disable');
				$('#deposit_amount, #deposit_note').attr('disabled',true);

			
			}

			//reset the deposit pane
			function resetDeposit(){
				$('#deposit_amount').val(''); 
				$('#deposit_note').val('');	
				$('.deposit_status').hide();
			}

			//realod the uf listing, update the (#) carts to be validated
			function reloadValidationUfs(){
				$('#uf_cart_select').xml2html('reload',{
					url 	: 'php/ctrl/Validate.php',
					params : 'oper=getUFsCartCount'
				});

			}


			/**
			 *	load items for given cart. 
			 *	date_for_shop is not needed for loading, however for submitting(!)
			 */
			function loadCart(cart_id, date_for_shop){
				var uf_id = $("#uf_cart_select option:selected").val();
				var account = 1000+parseInt(uf_id); 

				//set the uf_id and date for the saveCartURL when submitting
				//TODO this should be replaced with the cart_id...! however this means
				//to change the lib/validation_cart_manager, abstract_cart_manager!!
				$('#cartLayer').aixadacart('options',{
					date : date_for_shop,
					saveCartURL : 'php/ctrl/Validate.php?oper=commit&uf_id='+uf_id
				});

				$('#cartAnim').show();
				//the url to load the items for given uf/date
				$('#cartLayer').aixadacart('loadCart',{
					loadCartURL		: 'php/ctrl/Validate.php?oper=getShopCart&cart_id='+cart_id,
					loadSuccess : function (){
						$('#cartAnim').hide();
					}
				}); //end loadCart

			}

			
			function getSelectedDate(){
				return $('#selDate4Validation option:selected').val();
			}


			//prevent accidental submit on return when editing input
			$('input')
				.live('keydown', function(e){
				if (e.keyCode == 13){
					//var ti = $(this).attr("tabindex");
					//$(this).attr("tabindex",ti+1);
					e.stopPropagation();
					return false; 
				}
			})
			
			
			resetFields();
			resetDeposit();
			
	});  //close document ready
</script>

</head>
<?php flush(); ?>
<body>
<div id="wrap">
	<div id="headwrap">
		<?php include "php/inc/menu2.inc.php" ?>
	</div>
	<!-- end of headwrap -->
	
	
	
	<div id="stagewrap" class="ui-widget">
	
		
		<div id="titlewrap">
			<div id="titleLeftCol">
				<p class="floatLeft"><img src="img/validar.png" style="margin-top:4px; height:60px;"/></p>
		    	<div class="aix-layout-title-with-icon">
		    		<h1 class="cartTitle"><?php echo $Text['ti_validate']; ?><span class="insert_uf_id cart">??</span> <span class="ui-corner-all aix-style-padding3x3 showCartDate"></span> </h1>
		    		<h1 class="noCartTitle ui-state-highlight ui-corner-all"><?php echo $Text['nothing_to_val']; ?><span class="insert_uf_id cart">??</span> </h1>
		    		<h1 class="validatedCartTitle ui-state-highlight ui-corner-all"><?php echo $Text['ti_validate']; ?><span class="insert_uf_id cart">??</span></h1>
		    	</div>
		    </div>
		    <div id="titleRightCol">
				
		    	<p class="textAlignRight">
		    		<select id="uf_cart_select">
		    			<option value="-10" selected="selected"><?php echo $Text['sel_uf']; ?></option>
		    			<option value="{uf_id}" cc="{non_validated_carts}"> {uf_id} {uf_name}</option>
		    		</select>
		    	</p>
		    </div>
		</div>
	

		<div class="aix-layout-splitW60 floatLeft">
			<div id="tabs">
			<span class="loadAnimValidate hidden" id="cartAnim"><img src="img/ajax-loader.gif"/></span>
			<ul>
				<li><a href="#tabs-1"><?php echo $Text['validate']; ?></a></li>
				<li><a href="#tabs-2"><?php echo $Text['search_add']; ?></a></li>
			</ul>
			<div id="tabs-1">
				
                
						<div id="cartLayer"></div>
				
				
			</div>
			
			<div id="tabs-2">
				<div class="ui-widget">
                                 <label for="search"><?php echo $Text['search'];?></label>
						<input id="search" class="ui-widget-content ui-corner-all" value="" />
				</div>
				<p>&nbsp;</p>
				<div>
					<table id="product_list_search" class="product_list" >
						<thead>
						<tr>
							
							<th><?php echo $Text['id'];?></th>
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
								<td class="item_name">{name}</td>
								<td class="item_provider_name">{provider_name}</td>
								<td class="item_quantity"><input name="{id}" class="ui-corner-all" value="0.00"  size="4" id="quantity_{id}"/></td>
								<td class="item_unit">{unit}</td>
								<td class="item_iva_percent hidden">{iva_percent}</td>	
								<td class="item_rev_tax_percent">{rev_tax_percent}</td>	
								<td class="item_price">{unit_price}</td>			
							</tr>						
						</tbody>
						
						
					</table>
				</div>

			</div>
		</div><!-- end tabs -->
		</div><!-- end left col -->
		
		<div class="aix-layout-splitW40 floatLeft">
			
			<div id="deposit" class="ui-widget">
				<div class="aix-style-observer-widget ui-widget-content ui-corner-all" >
					<h3 class="ui-widget-header ui-corner-all"><?php echo $Text['make_deposit'];?> <span class="insert_uf_id account">??</span><span class="loadAnim floatRight hidden"><img src="img/ajax-loader.gif"/></span></h3>
					<p id="depositMsg"></p>
					<div id="deposit_content">
						<table class="tblForms">
						<tr><td><?php echo $Text['amount'];?>:&nbsp;&nbsp;</td><td><input type="text" name="quantity" id="deposit_amount" class="inputTxtMiddle ui-widget-content ui-corner-all" value=""/></td></tr>
						<tr><td><?php echo $Text['comment'];?>:&nbsp;&nbsp;</td><td><input type="text" name="description" id="deposit_note" class="inputTxtMiddle ui-widget-content ui-corner-all" value=""/></td></tr>
						<tr>
							<td></td>
							<td><button id="deposit_submit"><?=$Text['btn_make_deposit']; ?></button></td>
						</tr>
						
						<tr>
							<td colspan="2">
							
							</td>
						</tr>
						</table>
					</div>
				</div>
			</div>
			
			<div id="monitorFlows" class="ui-widget">
				<div class="ui-widget-content ui-corner-all aix-style-observer-widget">
					<h3 class="ui-widget-header ui-corner-all"><span class="left-icons ui-icon ui-icon-triangle-1-s"></span><?php echo $Text['latest_movements'];?> <span class="loadAnim floatRight hidden"><img src="img/ajax-loader.gif"/></span></h3>
					<table id="list_account" class="tblListingDefault">
					<thead>
						<tr>
							<th><?php echo $Text['account'];?></th>
							<th><?php echo $Text['uf_short'];?></th>
							<th><?php echo $Text['transfer_type'];?></th>
							
							<th class="textAlignRight"><?php echo $Text['amount'];?></th>
							<th class="textAlignRight"><?php echo $Text['balance'];?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>{account_id}</td>
							<td>{uf_id}</td>
							<td>{method}</td>
							<td><p class="textAlignRight"><span class="formatQty">{quantity}</span></p></td>
							<td><p class="textAlignRight"><span class="formatQty">{balance}</span></p></td>
						</tr>
					</tbody>
					</table>
				</div>
			</div>
		
			
			
			
			<div id="monitorCarts" class="ui-widget">
				<div class="ui-widget-content ui-corner-all aix-style-observer-widget">
					<h3 class="ui-widget-header ui-corner-all"><span class="left-icons ui-icon ui-icon-triangle-1-s"></span><?php echo $Text['todays_carts']; ?><span class="loadAnim floatRight hidden"><img src="img/ajax-loader.gif"/></span></h3>
					<table id="tbl_cart_listing" class="tblListingDefault">
						<thead>
							<tr >
								<th><?=$Text['cart_id'];?></th>
								<th><?=$Text['uf_short'];?></th>
								<th class="textAlignCenter"><?=$Text['date_of_purchase'];?></th>
								<th class="textAlignCenter"><?=$Text['validated'];?></th>
								<th class="textAlignRight"><?=$Text['total'];?></th>
							</tr>
						</thead>
						<tbody>
							<tr id="shop_{id}" shopId="{id}" dateForShop="{date_for_shop}" ufId="{uf_id}" validated="{ts_validated}" class="clickable">
								<td>{id}</td>
								<td>{uf_id}</td>
								<td>{date_for_shop}</td>
								<td>{ts_validated}</td>
								<td><p class="textAlignRight">{purchase_total}€</p></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			
		
			<div id="monitorUFs" class="ui-widget">
				<div class="ui-widget-content ui-corner-all aix-style-observer-widget">
					<h3 class="ui-widget-header ui-corner-all"><span class="left-icons ui-icon ui-icon-triangle-1-s"></span><?php echo $Text['negativeUfs'];?><span class="loadAnim floatRight hidden"><img src="img/ajax-loader.gif"/></span></h3>
					
						<table id="negative_ufs" class="tblListingDefault">
							<thead>
								<tr>
									<th class="textAlignRight"><?php echo $Text['uf_short'];?></th>
									<th class="textAlignLeft"><?php echo $Text['name'];?></th>
									<th class="textAlignRight"><?php echo $Text['balance'];?></th>
									<th><?php echo $Text['lastUpdate'];?></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td><p class="textAlignRight">{uf}</p></td>
									<td><p class="textAlignLeft">{name}</p></td>
									<td><p class="textAlignRight"><span class="negativeBalance">{balance}</span></p></td>
									<td>{last_update}</td>
								</tr>
							</tbody>
						</table>
					
				</div>
			</div>
			
			<div id="monitorGlobals" class="ui-widget">
				<div class="ui-widget-content ui-corner-all aix-style-observer-widget">
					<h3 class="ui-widget-header ui-corner-all"><span class="left-icons ui-icon ui-icon-triangle-1-s"></span><?php echo $Text['name_cash_account']; ?><span class="loadAnim floatRight hidden"><img src="img/ajax-loader.gif"/></span></h3>
					<table id="dailyStats" class="tblListingDefault">
						<tbody>
							<tr><td><p><?php echo $Text['totalIncome'];?></p></td><td><p class="textAlignRight">{income}</p></td></tr>
							<tr><td><p><?php echo $Text['totalSpending'];?></p></td><td><p class="textAlignRight">{spending}</p></td></tr>
							<tr><td><p><?php echo $Text['balance'];?></p></td><td><p class="textAlignRight">{balance}</p></td></tr>							
						</tbody>
					</table>
					
				</div>
			</div>
			
			
			<div id="monitorStock" class="ui-widget hidden">
				<div class="rightCol-Observer ui-widget-content ui-corner-all  aix-style-observer-widget">
					<h3 class="ui-widget-header ui-corner-all"><span class="left-icons ui-icon ui-icon-triangle-1-s"></span><?php echo $Text['negativeStock'];?><span class="loadAnim floatRight hidden"><img src="img/ajax-loader.gif"/></span></h3>
					
						<table id="min_stock" class="tblListingDefault">
							<thead>
								<tr>
									<th><?php echo $Text['id'];?></th>
									<th><?php echo $Text['product_name'];?></th>
									<th><?php echo $Text['provider_name'];?></th>
									<th><?php echo $Text['minStock'];?></th>
									<th><?php echo $Text['curStock'];?></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>{id}</td>
									<td>{stock_item}</td>
									<td>{stock_provider}</td>
									<td>{stock_min}</td>
									<td class="negativeBalance">{stock_actual}</td>
								</tr>
							</tbody>
						</table>
					
				</div>
			</div>
			
		
		</div>
		
		
		
	</div>
	<!-- end of stage wrap -->
</div>
<!-- end of wrap -->
<!-- / END -->

<div id="dialog_select_cart" title="Non-validated carts for household">
	<p>&nbsp;</p>
	<p class="ui-state-highlight ui-corner-all aix-style-padding8x8"><?php echo $Text['msg_several_carts']; ?> </p>
	<p>&nbsp;</p>
	<table id="tbl_Shop" class="tblListingDefault">
		<thead>
			<tr >
				<th><?php echo $Text['cart_id'];?></th>
				<th class="textAlignCenter"><?=$Text['date_of_purchase'];?></th>
				<th class="textAlignCenter"><?=$Text['validated'];?></th>
				<th class="textAlignRight"><?=$Text['total'];?></th>
			</tr>
		</thead>
		<tbody>
			<tr id="shop_{id}" shopId="{id}" dateForShop="{date_for_shop}" validated="{ts_validated}" class="clickable">
				<td>{id}</td>
				<td class="textAlignCenter">{date_for_shop}</td>
				<td class="textAlignCenter">{ts_validated}</td>
				<td class="textAlignRight">{purchase_total}€</td>
			</tr>
		</tbody>
	</table>
</div>


</body>
</html>