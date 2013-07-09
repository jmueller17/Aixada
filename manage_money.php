<?php include "php/inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_cashbox'];?></title>

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
	   
   	<?php  } else { ?>
	    <script type="text/javascript" src="js/js_for_manage_money.min.js"></script>
    <?php }?>

   
	<script type="text/javascript">
	
	$(function(){

			//set the balance of cashbox(-3) or consume account (-2)
			var gSetBalanceAId = -3; 

		
			/**
			 * 	account listing
			 */
			 $('#list_account tbody').xml2html('init',{
					url				: 'php/ctrl/Account.php',
					params			: 'oper=accountExtract&account_id=-3&start_date=2200-01-01&num_rows=400',
					resultsPerPage 	: 20,
					loadOnInit 		: false, 
					paginationNav 	: '#list_account tfoot td',
					beforeLoad 		: function(){
						$('#cashbox_listing .loadAnim').show();
					},
					complete 		: function(rowCount){
						$('#cashbox_listing .loadAnim').hide();

						var balance = $('tr td', this).eq(5).html();
						$('#balance').html(balance);
					}
			});


			/**
			 *	load ufs. make depost in cashbox and account. 
			 */
			 $('.uf_account_select').xml2html('init',{
					url 	: 'php/ctrl/Account.php',
					params 	: 'oper=getAllAccounts', 
					offSet	: 1, 
					loadOnInit : true, 
					complete : function(){
						$('option[value="-1"], option[value="-2"], option[value="-3"]', this).hide();
						//$('option[value="-3"]', this).attr('selected','selected');
						
					}
				//event listener to load items for this uf to validate
				}).change(function(e){
					allowDeposit();		
					allowWithdraw();		
								
			});


			/**
			 * hide show specific deposit fields
			 */
			$('.uf_sel_tr, .comment_tr').hide();
			$('#sel_deposite_type').change(function(e){
				var sel_id = parseInt($("option:selected", this).val());
				$('.comment_tr').show(); 
				//make deposit for UF
				if (sel_id == 1){
					$('.uf_account_select option[value="-10"]').attr('selected','selected')
					$('.uf_sel_tr').show();
				
				//other type of deposits
				} else if (sel_id == 2) {
					$('.uf_account_select option[value="-3"]').attr('selected','selected');
					$('.uf_sel_tr').hide();

				} else if (sel_id == -1){
					$('.uf_sel_tr, .comment_tr').hide();
				}
				allowDeposit();
			});


			/**
			 * hide show specific withdraw fields
			 */			
			$('.uf_sel_tr, .comment_tr').hide();
			$('#sel_withdraw_type').change(function(){
				var sel_id = parseInt($("option:selected", this).val()); 
				$('.comment_tr').show();
				$('.uf_account_select option[value="-3"]').attr('selected','selected');
				$('.uf_sel_tr').hide();
				
				//widthdraw to pay provider
				if (sel_id == 1){
					
					
				//withdraw to take money to the bank
				} else if (sel_id == 2) {
					

				//uf stuff
				} else if (sel_id == 3 || sel_id == 4) {
					$('.uf_account_select option[value="-10"]').attr('selected','selected')
					$('.uf_sel_tr').show();

				//all other stuff
				} else if (sel_id == 5) {
					
				} else if (sel_id == -1){
					//$('.uf_sel_tr, .comment_tr').hide();
				} 
				
				allowWithdraw();
			});

			
			/**
			 * 	checks if all deposit submit criteria are met. 
			 */
  			function allowDeposit(){
  				var allow = true; 
  	  			var deposit_type = $('#sel_deposite_type option:selected').val();
  	  			var amount = $.checkNumber($('#deposit_amount'), '', 2);			
  				var uf_id = parseInt($("#deposit_form .uf_account_select option:selected").val()) - 1000;

  				$('#deposit_amount').val(amount);
  				
  	  			allow = allow && (amount > 0) && (deposit_type==3 || (deposit_type==2 || ((uf_id > 0) && (deposit_type == 1))));

  	  			if (allow) {
  	  				$('#deposit_submit').button('enable');
  	  	  		} else {
	  	  	  		$('#deposit_submit').button('disable');
  	  	  	  	}
  	  		} 	


  			/**
			 * 	checks if all withdraw submit criteria are met. 
			 */
  			function allowWithdraw(){
  				var allow = true; 
  	  			var withdraw_type = $('#sel_withdraw_type option:selected').val()
  	  			var amount = $.checkNumber($('#withdraw_amount'), '', 2);
  	  			var uf_id = parseInt($("#withdraw_form .uf_account_select option:selected").val()) - 1000;

  	  			$('#withdraw_amount').val(amount);
				
  	  			allow = allow && (amount > 0) && (((withdraw_type == 2 || withdraw_type == 1 || withdraw_type == 5 || withdraw_type == 6) || ((uf_id > 0) && (withdraw_type == 3 || withdraw_type == 4))));

  	  			if (allow) {
  	  				$('#withdrawal_submit').button('enable');
  	  	  		} else {
	  	  	  		$('#withdrawal_submit').button('disable');
  	  	  	  	}
  	  		} 			


  			
  			/**
  			 *	reset deposit / withdrawal
  			 */
			function resetDeposit(){
				var n = new Number(0); 
				$('#deposit_amount').val(n.toFixed(2));
				$('#sel_deposite_type').trigger("change");
			}

			function resetWithdrawal(){
				var n = new Number(0); 
				$('#withdraw_amount').val(n.toFixed(2));
				$('#sel_withdraw_type').trigger("change");
			}

			

  			$('#deposit_amount')
  	  			.blur(function(e){
  	  				allowDeposit();
  	  	  		});
  	  		
  			$('#withdraw_amount')
  	  			.blur(function(e){
  	  				allowWithdraw();
  	  	  		})


  			
  	
  			/**
  			 *	DEPOSIT SUBMIT
  			 */
			$('#deposit_submit').button({
					disabled:true,
					icons: {
		                primary: "ui-icon-arrowthick-1-s"
		            }
			}).click(function(){
  				var dataSerial = $('#deposit_form').serialize();
				$('#deposit_submit').button('disable');

				//decide which money movement
				var sel_id  = parseInt($('#sel_deposite_type option:selected').val());
				var oper = ""; 

				switch (sel_id){
					case 1:
						oper = "depositCashForUf";
						break;
					case 2: 
						oper = "depositCash";
						break;
					case 3: 
						oper = "depositSalesCash";
						break;
				}
				

				$.ajax({
					type: "POST",
					url: "php/ctrl/Account.php?oper="+oper,
					data: dataSerial,	
					beforeSend : function (){
						$('#depositAnim').show();
					},	
					success: function(msg){
						$.updateTips("#depositMsg", "success", "<?=$Text['msg_deposit_success'];?>" );
						resetDeposit();
								
						$('#list_account tbody').xml2html('reload',{
							params: 'oper=latestMovements'
						});
					},
					error : function(XMLHttpRequest, textStatus, errorThrown){
						$.updateTips("#depositMsg","error", XMLHttpRequest.responseText);
					},
					complete : function(msg){
						$('#deposit_submit').button('enable');
						$('#depositAnim').hide();
					}
				}); //end ajax

			});


  			/**
  			 *	WITHDRAW SUBMIT
  			 */
			$('#withdrawal_submit')
				.button({
					disabled:true,
					icons: {
	                	primary: "ui-icon-arrowthick-1-n"
	            	}
				})
				.click(function(){
	  				var dataSerial = $('#withdraw_form').serialize();
					$('#withdrawal_submit').button('disable');


					//decide which money movement
					var sel_id  = parseInt($('#sel_withdraw_type option:selected').val());
					var oper = ""; 

					switch (sel_id){
						case 1:
							oper = "payProviderCash";
							break;
						case 2: 
							oper = "withdrawCashForBank";
							break;
						case 3: 
							oper = "withdrawCashFormUFAccount";
							break;

						case 4: 
							oper = "withdrawMemberQuota";
							break;
						case 5: 
							oper = "withdrawCash";
							break;
						case 6: 
							oper = "payProviderBank";
							break;
					}	
					

					$.ajax({
						type: "POST",
						url: "php/ctrl/Account.php?oper="+oper,
						data: dataSerial,	
						beforeSend : function (){
							$('#withdrawAnim').show();
						},	
						success: function(msg){
							$.updateTips("#withdrawMsg", "success", "<?=$Text['msg_withdrawal_success'];?>" );
							resetWithdrawal();
									
							$('#list_account tbody').xml2html('reload',{
								params	: 'oper=latestMovements'
							});
						},
						error : function(XMLHttpRequest, textStatus, errorThrown){
							$.updateTips("#withdrawMsg","error", XMLHttpRequest.responseText);
						},
						complete : function(msg){
							$('#withdrawal_submit').button('enable');
							$('#withdrawAnim').hide();
						}
					}); //end ajax

			});


					


  			 
  			$("#dialog_c_balance").dialog({
  				autoOpen: false,
  				width: 450,
  				modal: true,
  				buttons: {
  					"<?=$Text['btn_submit'];?>": function() {

							var amount = $.checkNumber($('#c_balance_quantity'), '', 2); 
  							var notes = $('#c_balance_description').val();
					        var css = (new Number(amount) > 0)? 'aix-style-pos-balance':'aix-style-neg-balance';


							if (!amount){
								$.updateTips("#c_balance_Msg","error", "<?php echo $Text['msg_err_only_num'];?>");
								return false; 
							}
	  							
  							//
  							$.ajax({
  								type: "POST",
								url : "php/ctrl/Account.php?oper=correctBalance&account_id="+gSetBalanceAId+"&balance="+amount+"&description="+notes,
  						        success :  function(msg){

  						        	//$('.setTotalCashbox').addClass(css).text(amount).fadeIn(1000);
  						        	getGlobalBalances();

  									$('#c_balance_quantity').val("");  
  									$("#dialog_c_balance").dialog( "close" );
  								}, 
  								error : function(XMLHttpRequest, textStatus, errorThrown){
  							    	$.updateTips('#c_balance_Msg','error', XMLHttpRequest.responseText);
  							   	},
  							   	complete: function(){
  								}  		
  							});
  						
  					},
  					"<?=$Text['btn_cancel'];?>": function() {
  						$('#c_balance_quantity').val("");  
  						$( this ).dialog( "close" );
  					}
  				}
  			});		




  			//$('button').button();

  			$("#btn_overview").button({
				 icons: {
		        		primary: "ui-icon-circle-arrow-w"
		        	}
				 })
        		.click(function(e){
    				switchTo('overview'); 
        		}).hide();
  			

  			/**
  			 *	Main cashbox menu buttons
  			 */
  			$('#btn_nav_dCashbox')
  				.button()
  				.click(function(e){
					switchTo('depositCash');
  	  			});

  			$('#btn_nav_wCashbox')
				.button()
				.click(function(e){
					switchTo('withdrawCash');
	  			});

  			$('#btn_nav_cCashbox')
				.button()
				.click(function(e){
					gSetBalanceAId = -3; 
					$("#dialog_c_balance").dialog('open');
					$("#dialog_c_balance").dialog({ title: "<?php echo $Text['set_balance'];?>" });
  				});

  			

  			/**
  			 *	Main consum account menu buttons
  			 */
  			$('#btn_nav_dCAccount')
  				.button()
  				.click(function(e){
					switchTo('depositBanc');
  	  			});

  			$('#btn_nav_wCAccount')
				.button()
				.click(function(e){
					switchTo('withdrawBanc');
	  			});

  			$('#btn_nav_cCAccount')
				.button()
				.click(function(e){
					gSetBalanceAId = -2; 
					$("#dialog_c_balance").dialog('open');
					$("#dialog_c_balance").dialog({ title: "Set balance for consume account" });

  				});

	  			
			function getGlobalBalances(){
	  			//retrieve balance of global accounts
	  			$.ajax({
						type: "POST",
						url : "php/ctrl/Account.php?oper=globalAccountsBalance",
						dataType : 'xml',
				        success :  function(xml){
	
					        	$(xml).find('row').each(function(){
						        	
					        		var id = $(this).find('account_id').text();
						        	var balance = $(this).find('balance').text();
	
						        	var css = (new Number(balance) > 0)? 'aix-style-pos-balance':'aix-style-neg-balance';
						        	
						        	switch(id){
						        		case '-3':
							        		$('.setTotalCashbox').addClass(css).text(balance);
							        		break;
						        		case '-2':
						        			$('.setTotalConsum').addClass(css).text(balance);
						       				break;
						        		case '-1':
						        			$('.setTotalMaintenance').addClass(css).text(balance);
						       				break;
						        	}
	
						        });
		
						}, 
						error : function(XMLHttpRequest, textStatus, errorThrown){
							$.showMsg({
								msg:XMLHttpRequest.responseText,
								type: 'error'});
					   	} 		
				});
			}
	

			function switchTo(section){
								
				//reset selects; 
				$('#sel_withdraw_type option[value="-1"]').attr('selected','selected');
				$('#sel_deposite_type option[value="-1"]').attr('selected','selected');

				resetDeposit();
				resetWithdrawal();

				$('.overviewElements, .withdrawElements, .withdrawCashElements, .withdrawBancElements, .depositElements, .depositBancElements, .depositCashElements, .movementElements').hide();
				
				switch(section){

				case 'overview':
					//$('.depositElements, .withdrawElements, .movementElements').hide();
					$('.overviewElements').fadeIn(1000);
					break;
					
				case 'depositCash':
					$('.depositElements, .depositCashElements').fadeIn(1000);
					break;

				case 'depositBanc':
					//$('.overviewElements, .withdrawElements').hide();
					$('.depositElements, .depositBancElements').fadeIn(1000);
					break;
				

				case 'withdrawCash':
					//$('.overviewElements, .depositElements').hide();
					$('.withdrawElements, .withdrawCashElements').fadeIn(1000);
					break;

				case 'withdrawBanc':
					//$('.overviewElements, .depositElements').hide();
					$('.withdrawElements, .withdrawBancElements').fadeIn(1000);
					break;
				
					

				}
			}
  			
			switchTo('overview');	
			getGlobalBalances();
					
	});  //close document ready
	</script>
</head>
<body>
<div id="wrap">

	<div id="headwrap">
		<?php include "php/inc/menu.inc.php" ?>
	</div>
	<!-- end of headwrap -->
	
	
	<div id="stagewrap" class="ui-widget">
	
		<div id="titlewrap" class="ui-widget">
			<div id="titleLeftCol">
				<button id="btn_overview" class="floatLeft depositElements withdrawElements"><?php echo $Text['overview'];?></button>
			</div>
		</div><!-- end titlewrap -->
		
		<div class="aix-layout-center60 ui-widget"> 
		<div id="account_overview" class="overviewElements aix-style-entry-widget">
		
			<h2 class="clickable"><?php echo $Text['name_cash_account']; ?> <span class="floatRight"><?=$Text['current_balance'];?>: <span class="setTotalCashbox"">-</span> <?php echo $Text['currency_sign'];?></span></h2>
			<table>
				<tr>
					<td>
						<button class="aix-layout-fixW150" id="btn_nav_dCashbox"><?php echo $Text['btn_deposit']; ?></button>
					</td>
					<td><p><?php echo $Text['deposit_desc']; ?></p></td>
				</tr>
				
				<tr>
					<td>
						<button class="aix-layout-fixW150" id="btn_nav_wCashbox"><?php echo $Text['btn_make_withdrawal']; ?></button>
					</td>
					<td><p><?php echo $Text['withdraw_desc']; ?></p></td>
				</tr>
				<tr>
					<td>
						<button class="aix-layout-fixW150" id="btn_nav_cCashbox"><?php echo $Text['btn_set_balance']; ?></button>
					</td>
					<td><p><?php echo $Text['set_bal_desc']; ?></p></td>
				</tr>
				
			</table>
			<p>&nbsp;</p>
	
			<h2><?php echo $Text['consum_account']; ?> <span class="floatRight"><?=$Text['current_balance'];?>: <span class="setTotalConsum">-</span> <?php echo $Text['currency_sign'];?></span></h2>
			<table>
				<tr>
					<td>
						<button class="aix-layout-fixW150" id="btn_nav_dCAccount"><?php echo $Text['btn_deposit']; ?></button>
					</td>
					<td><p><?php echo $Text['deposit_desc_banc']; ?></p></td>
				</tr>
				
				<tr>
					<td>
						<button class="aix-layout-fixW150" id="btn_nav_wCAccount"><?php echo $Text['btn_make_withdrawal']; ?></button>
					</td>
					<td><p><?php echo $Text['withdraw_desc_banc']; ?></p></td>
				</tr>
				<tr>
					<td>
						<button class="aix-layout-fixW150" id="btn_nav_cCAccount"><?php echo $Text['btn_set_balance']; ?></button>
					</td>
					<td><p><?php echo $Text['set_bal_desc']; ?></p></td>
				</tr>
				
			</table>
			<p>&nbsp;</p>
			<span class="hidden">
			<h2><?php echo $Text['maintenance_account']; ?>  <span class="floatRight"><?=$Text['current_balance'];?><span class="setTotalMaintenance">-</span></span></h2>
			<table>
				<tr>
					<td class="aix-layout-fixW150">
						
					</td>
					<td><p>...</p></td>
				</tr>
				
				<tr>
					<td>
						
					</td>
					<td><p></p></td>
				</tr>
			</table>
			</span>
		</div>
		</div>


		<!-- DESPOT CASH / BANC SECTION -->

		<div id="deposit_cash" class="ui-widget aix-layout-center60 depositElements">
		<div class="aix-style-highlight-deposit ui-corner-all" >
			<div class="ui-widget-content ui-corner-all">
				<h2 class="ui-widget-header ui-corner-all"><span class="depositCashElements"><?=$Text['deposit_cashbox'];?></span><span class="depositBancElements"><?=$Text['deposit_banc'];?></span> <span class="loadAnim floatRight hidden" id="depositAnim"><img src="img/ajax-loader.gif"/></span></h2>
				<p id="depositMsg" class="ui-corner-all"></p>
				<div id="deposit_cash_content" class="padding10x5">
					<form id="deposit_form">
					<table class="tblForms">
						<tr>
							<td><?=$Text['amount'];?>:&nbsp;&nbsp;</td>
							<td><input type="text" name="quantity" id="deposit_amount" class="inputTxtMiddle ui-widget-content ui-corner-all " value="0.00"/>&nbsp;<?php echo $Text['currency_sign'];?></td>
						</tr>
						<tr>
							<td colspan="2"><br/></td>
						</tr>
						<tr>
							<td><?=$Text['deposit_type'];?>:&nbsp;&nbsp;</td>
							<td>
								<select id="sel_deposite_type">
									<option value="-1"><?=$Text['please_select'];?></option>
									<option class="depositCashElements" value="1"><?=$Text['deposit_by_uf'];?></option>
									<option class="depositBancElements" value="3"><?=$Text['deposit_sales_cash']; ?></option>
									<option value="2"><?=$Text['deposit_other'];?></option>

									
								</select>
							</td>
						</tr>
						<tr class="uf_sel_tr">
							<td><?=$Text['make_deposit_4HU'];?>:&nbsp;&nbsp;</td>
							<td>
								<select class="uf_account_select" name="account_id">
		    						<option value="-10" selected="selected"><?php echo $Text['sel_uf_or_account']; ?></option>
		    						<option value="{id}">{name}</option>
			    				</select>
							</td>
						</tr>
						
						<tr class="comment_tr">
							<td><?=$Text['comment'];?>:&nbsp;&nbsp;</td>
							<td>
								<input type="text" name="description" id="deposit_description" class="inputTxtLarge ui-widget-content ui-corner-all" value=""/>
							</td>
						</tr>
						<tr>
							<td colspan="2"><br/></td>
						</tr>
						<tr>
							<td></td>
							<td><button id="deposit_submit"><?=$Text['btn_make_deposit']; ?></button></td>
						</tr>
						</table>
						</form>
					</div>
				</div>
		</div>
		</div>
		
		
		<div id="withdraw_cash" class="ui-widget  aix-layout-center60 withdrawElements">
			<div class="aix-style-highlight-withdrawl ui-corner-all"> 
			<div class="ui-widget-content ui-corner-all" >
				<h2 class="ui-widget-header ui-corner-all"><span class="withdrawCashElements"><?php echo $Text['widthdraw_cashbox'];?></span> <span class="withdrawBancElements"><?php echo $Text['withdraw_banc'];?></span> <span class="loadAnim floatRight hidden" id="withdrawAnim"><img src="img/ajax-loader.gif"/></span></h2>
				<p id="withdrawMsg" class="ui-corner-all></p>
				<div id="withdraw_cash_content" class="padding10x5">
					<form id="withdraw_form">
						<input type="hidden" name="account_id" value="-3"/>
					<table class="tblForms">
						<tr>
							<td><?=$Text['amount'];?>:&nbsp;&nbsp;</td>
							<td>-<input type="text" name="quantity" id="withdraw_amount" class="inputTxtLarge  ui-widget-content ui-corner-all " value="0.00"/>&nbsp;<?php echo $Text['currency_sign'];?></td>
						</tr>
						<tr>
							<td colspan="2"><br/></td>
						</tr>
						<tr>
							<td><?=$Text['withdraw_type'];?>:&nbsp;&nbsp;</td>
							<td>
								<select id="sel_withdraw_type">
									<option value="-1"><?php echo $Text['please_select'];?></option>
									<option class="withdrawCashElements" value="1"><?php echo $Text['withdraw_provider']; ?></option>
									<option class="withdrawCashElements" value="2"><?php echo $Text['withdraw_to_bank']; ?></option>
									<option class="withdrawCashElements" value="3"><?php echo $Text['withdraw_uf']; ?></option>
									<option class="withdrawCashElements" value="4"><?php echo $Text['withdraw_cuota']; ?></option>
									<option value="5"><?php echo $Text['withdraw_other'];?></option>
									<option class="withdrawBancElements" value="6"><?php echo $Text['withdraw_provider']; ?></option>
								</select>
							</td>
						</tr>						
						<tr class="uf_sel_tr">
							<td><?php echo $Text['withdraw_from']; ?>:&nbsp;&nbsp;</td>
							<td>
								<select class="uf_account_select" name="account_id">
		    						<option value="-10" selected="selected"><?php echo $Text['sel_uf_or_account']; ?></option>
		    						<option value="{id}">{name}</option>
			    				</select>
							</td>
						</tr>
						
						<tr class="comment_tr">
							<td><?=$Text['comment'];?>:&nbsp;&nbsp;</td>
							<td>
								<input type="text" name="description" id="withdraw_description" class="inputTxtLarge ui-widget-content ui-corner-all" value=""/>
							</td>
						</tr>
						<tr>
							<td colspan="2"><br/></td>
						</tr>

						
						<tr>
							<td></td>
							<td><button id="withdrawal_submit"><?=$Text['btn_make_withdrawal']; ?>!</button></td>
						</tr>
					</table>
					</form>
				</div>
			</div>
		</div>
		</div>
		

		<div id="cashbox_listing" class="ui-widget movementElements">
			<div class="ui-widget-content ui-corner-all">
					<h2 class="ui-widget-header ui-corner-all minPadding"><?=$Text['latest_movements'];?> <?php echo $Text['name_cash_account'];?><span class="loadAnim floatRight hidden"><img src="img/ajax-loader.gif"/></span></h2>
					<table id="list_account"  class="table_listing">
					<thead>
						<tr>
							<th><?php echo $Text['date'];?></th>
							<th><?php echo $Text['operator']; ?></th>
							<th><?php echo $Text['description']; ?></th>
							<th><?php echo $Text['account']; ?></th>
							<th><?php echo $Text['amount']; ?></th>
							<th><?php echo $Text['balance']; ?></th>
						</tr>
					</thead>
					<tbody>
						<tr class="xml2html_tpl">
							<td>{ts}</td>
							<td>{operator}</td>
							<td>{description}</td>
							<td>{account}</td>
							<td>{quantity}</td>
							<td>{balance}</td>
						</tr>
					</tbody>
					<tfoot>
						<tr><td></td></tr>
					</tfoot>
					</table>
					
					
			</div>	
		</div>
	</div><!-- end of stage wrap -->
<div/>
<!-- end of wrap -->
<div id="dialog_c_balance" title="">
	<p id="c_balance_Msg" class="minPadding ui-corner-all"></p>
	<p><br/></p>
	<form id="c_balance_form">	
		<table class="tblForms">
			<tr>
				<td><?php echo $Text['current_balance']; ?>:&nbsp;&nbsp;</td>
				<td><input type="text" name="c_balance_quantity" id="c_balance_quantity" class="ui-widget-content ui-corner-all " value=""/>&nbsp;<?php echo $Text['currency_sign'];?></td>
			</tr>
			<tr>
				<td><?=$Text['comment'];?>:&nbsp;&nbsp;</td>
				<td><input type="text" name="c_balance_description" id="c_balance_description" class=" ui-widget-content ui-corner-all " value=""/></td>
			</tr>								
		</table>
	</form>
</div>

</body>
</html>