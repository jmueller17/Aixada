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

			var torn_uf_id = <?=$_SESSION['userdata']['uf_id'];?>;
		
			/**
			 * resets all input fields 
			 */
			function resetFields(){
				
				$('#cartLayer').aixadacart('resetCart');						
				$('.insert_uf_id').html('<strong>??</strong>');
				$('#uf_cart_select').val(-10);

				 $('#dailyStats').xml2html('reload',{
						url		: 'php/ctrl/Account.php',
						params	: 'oper=getIncomeSpendingBalance&date=' + getSelectedDate()
				 });				
			}

			function resetDeposit(){
				$('#uf_account_select').hide();	
				$('#deposit_amount').val(''); 
				$('#deposit_note').val('');	
				$('.deposit_status').hide();
				
				$('#deposit_submit').button('disable');
				$('#deposit_amount, #deposit_note').attr('disabled',true);

				if ($("#uf_cart_select option:selected").val() <= -10){
					$('.account').html('<strong>??</strong>');
					$('#uf_account_select').val(-10);
				}
			}

			function reloadValidationUfs(){
				$('#uf_cart_select').xml2html('reload',{
					url 	: 'php/ctrl/Validate.php',
					params 	: 'oper=GetUFsForValidation&date='+getSelectedDate()
				});

				 $('#dailyStats').xml2html('reload',{
					 params	: 'oper=getIncomeSpendingBalance&date=' + getSelectedDate(),
				 });
			}


			/**
			 *	load all UF-carts to validate
			 */
			$('#uf_cart_select').xml2html('init',{
					offSet	: 1,
					url 	: 'php/ctrl/Validate.php',
					params 	: 'oper=GetUFsForValidation',
					rowComplete : function(rowIndex, row){
						if ($(row).val() == torn_uf_id){	//cannot validate yourself
							$(row).addClass('dim60');
						}
					}, 
					loadOnInit:true
				//event listener to load items for this uf to validate
			}).change(function(){
			
				//get the id of the uf
				var uf_id = $("option:selected", this).val(); 
				var account = 1000+parseInt(uf_id); 

				if (uf_id <=0) {
					resetFields();
					resetDeposit();
					return false; 
					
				} else if (uf_id == torn_uf_id){ //cannot validate your own cart
					$.showMsg({
						msg:"<?php echo $Text['msg_err_validate_self'];?>",
						type: 'error'});
					resetFields();
					resetDeposit();
					return false;
				}
				
				//activate the deposit pane
				$('.insert_uf_id').html('<strong>'+uf_id+'</strong>');
				$('#deposit_submit').button('enable');
				$('#deposit_amount, #deposit_note, #search').attr('disabled',false);
				$('#uf_account_select').val(account).hide();

				
				//set the uf_id for the saveCartURL when submitting
				$('#cartLayer').aixadacart('options',{
					saveCartURL : 'php/ctrl/Validate.php?oper=commit&uf_id='+uf_id
				});

				$('#cartAnim').show();
				//the url to load the items for given uf/date
				$('#cartLayer').aixadacart('loadCart',{
					loadCartURL		: 'php/ctrl/Validate.php?oper=getShopCart&date='+getSelectedDate() + '&uf_id='+uf_id,
					//loadCartURL : 'php/ctrl/Validate.php?oper=getShopItemsForDateAndUf&date='+getSelectedDate()+'&uf_id='+uf_id,
					loadSuccess : function (){
						$('#cartAnim').hide();
					}
				}); //end loadCart
			});



			//retrieve all dates with available ufs for validation
			$('#selDate4Validation').xml2html('init',{
						url : 'php/ctrl/Validate.php',
						params : 'oper=getDatesForValidation',
						loadOnInit:true,
						complete : function(){
							//make sure "today" is selected automatically from the select box							
							var xmlr = $(this).xml2html('getXML');
							$(xmlr).find('date_for_validation').each(function(){
								if($(this).attr('today')) {
									$('#selDate4Validation option[value="'+$(this).text()+'"]').attr("selected","selected");
								}
							}); 
							reloadValidationUfs();
						}
				
			}).change(function(){
				$('#cartLayer').aixadacart('setDate',getSelectedDate());
				reloadValidationUfs();
			});
			

			function getSelectedDate(){
				return $('#selDate4Validation option:selected').val();
			}
			
			//init tabs
			$("#tabs").tabs();
			
			//init cart 
			$('#cartLayer').aixadacart("init",{
				saveCartURL : 'php/ctrl/Validate.php',
				cartType	: 'simple',
				btnType		: 'validate',
				saveOnDelete: false,
				submitSuccess : function (msg){
					reloadValidationUfs();
					
					//empty the cart
					$(this).aixadacart("resetCart");	

					$('#list_account tbody').xml2html('reload');				
				}
			});

		    //init xml2html product search
			$('#product_list_search tbody').xml2html('init',{
					url : 'php/ctrl/ShopAndOrder.php'
				});

				
			/**
			 *	define and submit the deposit
			 */
			$('#deposit_submit')
				.button()
				.click(function(){
					var quantity = $('#deposit_amount').val(); 
					var description = $('#deposit_note').val();
				
					if (isNaN(quantity) || quantity == '') { 
						$.showMsg({
									msg:"<?php echo $Text['msg_enter_deposit_amount'];?>",
									type: 'warning'});
						return false;
					}
	
					var uf_account_id = $("#uf_account_select option:selected").val();
	
					if (uf_account_id <= -10){
						$.showMsg({
									msg:"<?php echo $Text['msg_please_set_ufid_deposit'];?>",
									type: 'error'});
						return false; 
					}
	
					$('#deposit_submit').button('disable');
				
					$.ajax({
						type: "POST",
						url: "php/ctrl/Account.php?oper=DepositForUF&uf_id="+uf_account_id+"&quantity="+quantity+"&description="+description,	
						beforeSend : function (){
							$('#deposit .loadAnim').show();
						},	
						success: function(msg){
							$.updateTips("#depositMsg", "success", "<?=$Text['msg_deposit_success'];?>" );
							resetDeposit();
									
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
					loadOnInit: true
			});


  			 //balance
			 $('#dailyStats').xml2html('init',{
					url		: 'php/ctrl/Account.php',
					params	: 'oper=getIncomeSpendingBalance&date=' + getSelectedDate(),
                    autoReload: 100200, 
                    beforeLoad : function(){
						$('#dailyStats .loadAnim').show();
					},
					complete : function(){
						$('#dailyStats .loadAnim').hide();
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

			//negative stock
			 $('#min_stock tbody').xml2html('init',{
					url		: 'php/ctrl/Validate.php',
					params	: 'oper=getProductsBelowMinStock',
					loadOnInit: true,
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
			
			 
			$('#uf_account_select').xml2html('init',{
						url 	: 'php/ctrl/Account.php',
						params 	: 'oper=getActiveAccounts', 
						offSet	: 1, 
						loadOnInit : true
				//event listener to load items for this uf to validate
			}).change(function(){

					resetFields();
				
					//get the id of the uf
					var uf_id = parseInt($("option:selected", this).val()) - 1000; 

					if (uf_id <= -10) {
						resetDeposit();
						return false; 
					}
					
					//activate the deposit pane
					$('.account').html('<strong>'+uf_id+'</strong>');
					$('#deposit_submit').button('enable');
					$('#deposit_amount, #deposit_note').attr('disabled',false);		
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
		    	<h1><img src="img/validar.png" style="float:left; margin-top:-15px;  height:60px; margin-right:20px" /><?php echo $Text['ti_validate']; ?> <span class="insert_uf_id cart">??</span> </h1>
		    </div>
		    <div id="titleRightCol">
		    	<p class="textAlignRight"><?php echo $Text['set_date'];?>: <select id="selDate4Validation">
		    																	<option value="{date_for_validation}">{date_for_validation}</option>
		    																</select></p>
		    	<p class="textAlignRight"><?php echo $Text['get_cart_4_uf'];?>: <select id="uf_cart_select">
		    			<option value="-10" selected="selected"><?php echo $Text['sel_uf']; ?></option>
		    			<option value="{id}">{id} {name}</option>
		    	</select>
		    	</p>
		    </div>
		</div>
	

		<div id="leftCol">
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
				
				<div class="product_list_wrap">
					<table id="product_list_search" class="product_list" >
						<thead>
						<tr>
							
							<th><?php echo $Text['id'];?></th>
							<!-- th><?php echo $Text['info'];?></th-->
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
								<!-- td class="item_stock"><span class="product_info ui-icon ui-icon-info" stock="{stock_actual}"></span></td-->
								<td class="item_name">{name}</td>
								<td class="item_provider_name">{provider_name}</td>
								<td class="item_quantity"><input name="{id}" value="0.00"  size="4" id="quantity_{id}"/></td>
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
		
		<div id="rightCol">
			
			<div id="deposit" class="ui-widget">
				<div class="rightCol-Observer ui-widget-content ui-corner-all" >
					<h3 class="ui-widget-header ui-corner-all"><?php echo $Text['make_deposit'];?> <span class="insert_uf_id account">??</span><span class="loadAnim floatRight hidden"><img src="img/ajax-loader.gif"/></span></h3>
					<p id="depositMsg"></p>
					<div id="deposit_content">
						<table class="table_listing">
						<tr><td><?php echo $Text['amount'];?>:&nbsp;&nbsp;</td><td><input type="text" name="quantity" id="deposit_amount" class="inputTxtMiddle ui-widget-content ui-corner-all" value=""/></td></tr>
						<tr><td><?php echo $Text['comment'];?>:&nbsp;&nbsp;</td><td><input type="text" name="description" id="deposit_note" class="inputTxtMiddle ui-widget-content ui-corner-all" value=""/></td></tr>
						<tr>
							<td></td>
							<td><button id="deposit_submit"><?=$Text['btn_make_deposit']; ?></button></td>
						</tr>
						<tr>
							<td colspan="2"><p class="textAlignLeft"><a class="optionLink" id="toggle_uf_account_select" href="javascript:void(null)"><?php echo $Text['deposit_other_uf'];?></a>&nbsp;
							<select id="uf_account_select">
		    						<option value="-10" selected="selected"><?php echo $Text['sel_uf_or_account']; ?></option>
		    						<option value="{id}">{id} {name}</option>
		    				</select></p>
							</td>
							
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
				<div class="rightCol-Observer ui-widget-content ui-corner-all">
					<h3 class="ui-widget-header ui-corner-all"><span class="left-icons ui-icon ui-icon-triangle-1-s"></span><?php echo $Text['latest_movements'];?> <span class="loadAnim floatRight hidden"><img src="img/ajax-loader.gif"/></span></h3>
					<table id="list_account" class="table_listing">
					<thead>
						<tr>
							<th><?php echo $Text['time'];?></th>
							<th><?php echo $Text['account'];?></th>
							<th><?php echo $Text['uf_short'];?></th>
							<th><?php echo $Text['amount'];?></th>
							<th><?php echo $Text['balance'];?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>{time}</td>
							<td>{account_id}</td>
							<td>{uf_id}</td>
							<td>{quantity}</td>
							<td>{balance}</td>
						</tr>
					</tbody>
					</table>
				</div>
			</div>
			<div id="monitorGlobals" class="ui-widget">
				<div class="rightCol-Observer ui-widget-content ui-corner-all">
					<h3 class="ui-widget-header ui-corner-all"><span class="left-icons ui-icon ui-icon-triangle-1-s"></span><?php echo $Text['dailyStats']?><span class="loadAnim floatRight hidden"><img src="img/ajax-loader.gif"/></span></h3>
					<div id="dailyStats">
						<p><?php echo $Text['totalIncome'];?>: {income}</p>
						<p><?php echo $Text['totalSpending'];?>: {spending}</p>
						<p><?php echo $Text['balance'];?>: {balance}</p><br/><br/>
					</div>
				</div>
			</div>
			
			<div id="monitorUFs" class="ui-widget">
				<div class="rightCol-Observer ui-widget-content ui-corner-all">
					<h3 class="ui-widget-header ui-corner-all"><span class="left-icons ui-icon ui-icon-triangle-1-s"></span><?php echo $Text['negativeUfs'];?><span class="loadAnim floatRight hidden"><img src="img/ajax-loader.gif"/></span></h3>
					
						<table width="100%" id="negative_ufs" class="table_listing">
							<thead>
								<tr>
									<th><?php echo $Text['uf_short'];?></th>
									<th><?php echo $Text['name'];?></th>
									<th><?php echo $Text['balance'];?></th>
									<th><?php echo $Text['lastUpdate'];?></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>{uf}</td>
									<td>{name}</td>
									<td><span class="negativeBalance">{balance}</span></td>
									<td>{last_update}</td>
								</tr>
							</tbody>
						</table>
					
				</div>
			</div>
			<div id="monitorStock" class="ui-widget">
				<div class="rightCol-Observer ui-widget-content ui-corner-all">
					<h3 class="ui-widget-header ui-corner-all"><span class="left-icons ui-icon ui-icon-triangle-1-s"></span><?php echo $Text['negativeStock'];?><span class="loadAnim floatRight hidden"><img src="img/ajax-loader.gif"/></span></h3>
					
						<table width="100%" id="min_stock" class="table_listing">
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
</body>
</html>