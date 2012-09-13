<?php include "inc/header.inc.php" ?>
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
	    <script type="text/javascript" src="js/js_for_manage_cashbox.min.js"></script>
    <?php }?>

   
	<script type="text/javascript">
	
	$(function(){



		
			/**
			 * 	account listing
			 */
			 $('#list_account tbody').xml2html('init',{
					url				: 'ctrlReport.php',
					params			: 'oper=accountExtract&account_id=-3&start_date=2200-01-01&num_rows=400',
					resultsPerPage 	: 20,
					loadOnInit 		: true, 
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
			 $('#uf_account_select').xml2html('init',{
					url 	: 'smallqueries.php',
					params 	: 'oper=getAllAccounts', 
					offSet	: 1, 
					loadOnInit : true, 
					complete : function(){
						$('option[value="-1"], option[value="-2"], option[value="-3"]', this).remove();
					}
				//event listener to load items for this uf to validate
				}).change(function(){
					allowDeposit();					
			});


			/**
			 *	provider select
			 */
			$("#providerSelect").xml2html("init", {
					params : 'oper=listProviders&what=Shop&date=2012-03-14',
					offSet : 1, 
					loadOnInit: true
				}).change(function(){
					allowWithdraw();	
			});


			/**
			 * hide show specific deposit fields
			 */
			$('#uf_sel_tr, #uf_comment_tr, #other_comment_tr').hide();
			$('#sel_deposite_type').change(function(){
				var sel_id = parseInt($("option:selected", this).val()); 
				//make deposit for UF
				if (sel_id == 1){
					$('#other_comment_tr').hide();
					$('#uf_sel_tr').show();
					$('#uf_comment_tr').show();
				//other type of deposits
				} else if (sel_id == 2) {
					
					$('#uf_sel_tr').hide();
					$('#uf_comment_tr').hide();
					$('#other_comment_tr').show();
				} else if (sel_id == -1){
					$('#uf_sel_tr, #uf_comment_tr, #other_comment_tr').hide();
				}
				allowDeposit();
			});


			/**
			 * hide show specific withdraw fields
			 */			
			$('#provider_sel_tr, #provider_comment_tr, #other_withdraw_comment_tr').hide();
			$('#sel_withdraw_type').change(function(){
				var sel_id = parseInt($("option:selected", this).val()); 

				//widthdraw for provider
				if (sel_id == 1){
					$('#other_withdraw_comment_tr').hide();
					$('#provider_sel_tr').show();
					$('#provider_comment_tr').show();
				//other type of deposits
				} else if (sel_id == 2) {
					$('#provider_sel_tr').hide();
					$('#provider_comment_tr').hide();
					$('#other_withdraw_comment_tr').show();
				} else if (sel_id == -1){
					$('#provider_sel_tr, #provider_comment_tr, #other_withdraw_comment_tr').hide();
				}
				allowWithdraw();
			});

			
			/**
			 * 	checks if all deposit submit criteria are met. 
			 */
  			function allowDeposit(){
  				var allow = true; 
  	  			var deposit_type = $('#sel_deposite_type option:selected').val()
  	  			var amount = parseFloat($('#deposit_amount').val());  			
  				var uf_id = parseInt($("#uf_account_select option:selected").val()) - 1000;
  	  			allow = allow && (amount > 0) && ((deposit_type == 1) && (uf_id > 0) || (deposit_type == 2));
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
  	  			var amount = parseFloat($('#withdraw_amount').val());
  				var provider_id = parseInt($("#providerSelect option:selected").val());
  	  			allow = allow && (amount > 0) && ((withdraw_type == 1) && (provider_id > 0) || (withdraw_type == 2));
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
				$('#deposit_amount').val("")
				$('#sel_deposite_type').trigger("change");
			}

			function resetWithdrawal(){
				$('#withdraw_amount').val("");
				$('#sel_withdraw_type').trigger("change");
			}

			

  			$('#deposit_amount').keyup(function(e){
				allowDeposit();
  	  		});
  	  		
  			$('#withdraw_amount').keyup(function(e){
  				allowWithdraw();
  	  		});


  			
  			/**
  			 *	Buttons
  			 */
			$('#deposit_submit').button({
					disabled:true,
					icons: {
		                primary: "ui-icon-arrowthick-1-s"
		            }
			}).click(function(){
  				var dataSerial = $('#deposit_form').serialize();
				$('#deposit_submit').button('disable');

				$.ajax({
					type: "POST",
					url: "ctrlCash.php?oper=deposit",
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

					$.ajax({
						type: "POST",
						url: "ctrlCash.php?oper=withdrawal",
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

			
			$('#correct_balance').button({
				disabled:false,
				icons: {
	                primary: "ui-icon-pencil"
	            }
			}).click(function(){
				$("#dialog_c_balance").dialog("open");

			});

					


  			 
  			$("#dialog_c_balance").dialog({
  				autoOpen: false,
  				height: 180,
  				width: 450,
  				modal: true,
  				buttons: {
  					"<?=$Text['btn_submit'];?>": function() {

  							var amount = $('#c_balance_quantity').val();  
  						
							if (isNaN(amount)){
								$.updateTips("#c_balance_Msg","error", "<?php echo $Text['msg_err_only_num'];?>");
								return false; 
							}
	  							
  							//function to create new uf
  							$.ajax({
  								type: "POST",
								url : "ctrlCash.php?oper=correctBalance&amount="+amount,
  								dataType : 'xml',
  						        success :  function(xml){
  									$("#dialog_c_balance").dialog( "close" );
  									$('#c_balance_quantity').val("");  
  								}, 
  								error : function(XMLHttpRequest, textStatus, errorThrown){
  							    	$.updateTips('#c_balance_Msg','error', XMLHttpRequest.responseText);
  							   	},
  							   	complete: function(){
  								}  		
  							});
  						
  					},
  					"<?php echo $Text['btn_cancel'];?>": function() {
  						$('#c_balance_quantity').val("");  
  						$( this ).dialog( "close" );
  					}
  				}
  			});			
			
	});  //close document ready
	</script>
</head>
<body>
<div id="wrap">

	<div id="headwrap">
		<?php include "inc/menu2.inc.php" ?>
	</div>
	<!-- end of headwrap -->
	
	
	<div id="stagewrap" class="ui-widget">
	
		<div id="titlewrap">
		    	<h2 class="floatRight"><?=$Text['current_balance'];?>: <span id="balance"></span> <button id="correct_balance" title="<?=$Text['correct_balance'];?>"></button></h2>
		    	<h1><?=$Text['ti_mng_cashbox'];?></h1>
		</div><!-- end titlewrap -->
		

		<div id="deposit_cash" class="ui-widget floatLeft">
		<div class="highlight-deposit ui-corner-all" >
			<div class="ui-widget-content ui-corner-all" >
				<h2 class="ui-widget-header ui-corner-all minPadding"><?=$Text['deposit_cashbox'];?> <span class="loadAnim floatRight hidden" id="depositAnim"><img src="img/ajax-loader.gif"/></span></h2>
				<p id="depositMsg" class="minPadding ui-corner-all"></p>
				<div id="deposit_cash_content" class="padding10x5">
					<form id="deposit_form">
					<table class="table_listing_cashbox">
						<tr>
							<td><?=$Text['amount'];?>:&nbsp;&nbsp;</td>
							<td><input type="text" name="quantity" id="deposit_amount" class="inputTxtMiddle ui-widget-content ui-corner-all " value=""/></td>
						</tr>
						<tr>
							<td colspan="2"><br/></td>
						</tr>
						<tr>
							<td><?=$Text['deposit_type'];?>:&nbsp;&nbsp;</td>
							<td>
								<select id="sel_deposite_type">
									<option value="-1"><?=$Text['please_select'];?></option>
									<option value="1"><?=$Text['deposit_by_uf'];?></option>
									<option value="2"><?=$Text['deposit_other'];?></option>
								</select>
							</td>
						</tr>
						<tr id="uf_sel_tr">
							<td><?=$Text['make_deposit_4HU'];?>:&nbsp;&nbsp;</td>
							<td>
								<select id="uf_account_select">
		    						<option value="-10" selected="selected"><?php echo $Text['sel_uf_or_account']; ?></option>
		    						<option value="{id}">{id} {name}</option>
			    				</select>
							</td>
						</tr>
						
						<tr id="uf_comment_tr">
							<td><?=$Text['comment'];?>:&nbsp;&nbsp;</td>
							<td>
								<input type="text" name="description" id="deposit_note_uf" class="inputTxtMiddle ui-widget-content ui-corner-all" value=""/>
							</td>
						</tr>
						<tr id="other_comment_tr">
							<td><?=$Text['short_desc'];?>:&nbsp;&nbsp;</td>
							<td>
								<input type="text" name="description" id="deposit_note_other" class="inputTxtMiddle ui-widget-content ui-corner-all" value=""/>
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
		
		
		<div id="withdraw_cash" class="ui-widget floatRight">
			<div class="highlight-withdrawl ui-corner-all"> 
			<div class="ui-widget-content ui-corner-all" >
				<h2 class="ui-widget-header ui-corner-all"><?php echo $Text['widthdraw_cashbox'];?> <span class="loadAnim floatRight hidden" id="withdrawAnim"><img src="img/ajax-loader.gif"/></span></h2>
				<p id="widthdrawMsg" class="minPaddig ui-corner-all></p>
				<div id="withdraw_cash_content" class="padding10x5">
					<form id="withdraw_form">
					<table class="table_listing_cashbox">
						<tr>
							<td><?=$Text['amount'];?>:&nbsp;&nbsp;</td>
							<td><input type="text" name="quantity" id="withdraw_amount" class="inputTxtMiddle ui-widget-content ui-corner-all " value=""/></td>
						</tr>
						<tr>
							<td colspan="2"><br/></td>
						</tr>
						<tr>
							<td><?=$Text['withdraw_type'];?>:&nbsp;&nbsp;</td>
							<td>
								<select id="sel_withdraw_type">
									<option value="-1"><?=$Text['please_select'];?></option>
									<option value="1"><?=$Text['withdraw_for_provider'];?></option>
									<option value="2"><?=$Text['withdraw_other'];?></option>
								</select>
							</td>
						</tr>
						<tr id="provider_sel_tr">
							<td><?=$Text['withdraw_provider'];?>:&nbsp;&nbsp;</td>
							<td>
								<select id="providerSelect">
                    				<option value="-1" selected="selected"><?php echo $Text['sel_provider']; ?></option>
                    				<option value="{id}">{id} {name}</option>                     
								</select>
							</td>
						</tr>
						
						<tr id="provider_comment_tr">
							<td><?=$Text['comment'];?>:&nbsp;&nbsp;</td>
							<td>
								<input type="text" name="description" id="withdraw_note_provider" class="inputTxtMiddle ui-widget-content ui-corner-all" value=""/>
							</td>
						</tr>
						
						<tr id="other_withdraw_comment_tr">
							<td><?=$Text['short_desc'];?>:&nbsp;&nbsp;</td>
							<td>
								<input type="text" name="description" id="withdraw_note_other" class="inputTxtMiddle ui-widget-content ui-corner-all" value=""/>
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
		

		<div id="cashbox_listing" class="ui-widget">
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
<div id="dialog_c_balance" title="<?php echo $Text['set_balance'];?>">
	<p id="c_balance_Msg" class="minPadding ui-corner-all"></p>
	<p><br/></p>
	<form id="c_balance_form">	
		<table>
			<tr>
				<td><?=$Text['current_balance'];?>:&nbsp;&nbsp;</td>
				<td><input type="text" name="c_balance_quantity" id="c_balance_quantity" class="inputTxtMiddle ui-widget-content ui-corner-all " value=""/></td>
			</tr>				
		</table>
	</form>
</div>

</body>
</html>