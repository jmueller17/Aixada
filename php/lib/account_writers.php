<?php
function write_negative_ufs(
		$addClasses = '',  $hidden = false,
		$autoReload = 103020, $loadOnInit = true) {
	global $Text;
	?>
	<div id="monitorUFs" class="ui-widget <?php echo $addClasses; ?>">
		<div class="ui-widget-content ui-corner-all aix-style-observer-widget">
			<h3 class="ui-widget-header ui-corner-all"><span 
				class="left-icons ui-icon <?php 
					echo ($hidden ? 'ui-icon-triangle-1-e' : 'ui-icon-triangle-1-s');
					?>"></span><?php 
					echo $Text['negativeUfs'];?><span 
				class="loadAnim floatRight"><img 
				class="loadSpinner" src="img/ajax-loader.gif"/></span></h3>
				<table id="negative_ufs" class="tblListingDefault <?php 
					echo ($hidden ? 'hidden' : ''); ?>">
					<thead>
						<tr>
							<th class="textAlignRight"><?php 
								echo $Text['uf_short'];?></th>
							<th class="textAlignLeft"><?php 
								echo $Text['name'];?></th>
							<th class="textAlignRight"><?php
								echo $Text['mon_balance'];?>&nbsp;</th>
							<th><?php echo $Text['mon_lastOper'];?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="textAlignRight">{uf}</td>
							<td class="textAlignLeft">{name}</td>
							<td class="textAlignRight formatQty">{balance}</td>
							<td>{last_update}</td>
						</tr>
					</tbody>
				</table>

		</div>
	</div>
	<script>
		function load_write_negative_ufs(
				p_currency_sign, p_autoReload, p_loadOnInit) {
	  		//negative ufs
			$('#negative_ufs tbody').xml2html('init',{
				url		: 'php/ctrl/Account.php',
				params	: 'oper=getNegativeAccounts',
				loadOnInit: p_loadOnInit,
				autoReload: p_autoReload,
				beforeLoad : function(){
					$('#monitorUFs .loadSpinner').show();
				},
				rowComplete : function (rowIndex, row){
					$.formatQuantity(row, p_currency_sign);
				},
				complete : function(){
					$('#monitorUFs .loadSpinner').hide();
					$('#negative_ufs tbody tr:odd').addClass('rowHighlight');
				}
			});
		}
	</script>
	<script>
		load_write_negative_ufs("<?php 
			echo $Text['currency_sign']; ?>",<?php 
			echo $autoReload; ?>,<?php 
			echo $loadOnInit?'true':'false'; ?>);
	</script>
	<?php
}

function write_dailyStats(
		$addClasses = '', $hidden = false, 
		$autoReload = 100200, $loadOnInit = true) {
	global $Text;
	?>
	<div id="monitorGlobals" class="ui-widget <?php echo $addClasses; ?>">
		<div class="ui-widget-content ui-corner-all aix-style-observer-widget">
			<h3 class="ui-widget-header ui-corner-all"><span 
				class="left-icons ui-icon <?php 
					echo ($hidden ? 'ui-icon-triangle-1-e' : 'ui-icon-triangle-1-s');
					?>"></span><?php
					echo $Text['mon_dailyTreasurySummary']; ?><span 
				class="loadAnim floatRight hidden"><img 
				class="loadSpinner" src="img/ajax-loader.gif"/></span></h3>
			<table id="dailyStats" class="tblListingDefault <?php 
					echo ($hidden ? 'hidden' : ''); ?>">
			<thead>
				<tr>
					<th><?php echo $Text['account'];?></th>
					<th class="textAlignRight"><?php 
						echo $Text['totalIncome'];?>&nbsp;</th>
					<th class="textAlignRight"><?php
						echo $Text['totalSpending'];?>&nbsp;</th>
					<th class="textAlignRight"><?php
						echo $Text['mon_dailyBalance'];?>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{account_id} - {name}</td>
					<td><p class="textAlignRight formatQty">{income}</p></td>
					<td><p class="textAlignRight formatQty">{spending}</p></td>
					<td><p class="textAlignRight formatQty">{balance}</p></td>
				 </tr>
			</tbody>
			</table>
		</div>
	</div>
	<script>
		function load_write_dailyStats(
				p_currency_sign, p_autoReload, p_loadOnInit) {
			$('#dailyStats tbody').xml2html('init',{
				url		: 'php/ctrl/Account.php',
				params	: 'oper=getIncomeSpendingBalance',
				autoReload: p_autoReload,
				loadOnInit: p_loadOnInit,
				beforeLoad : function(){
					$('#monitorGlobals .loadSpinner').show();
				},
				rowComplete : function (rowIndex, row){
					$.formatQuantity(row, p_currency_sign);
				},
				complete : function(){
					$('#monitorGlobals .loadSpinner').hide();
					$('#dailyStats tbody tr:odd').addClass('rowHighlight');
				}
			});
		}
	</script>
	<script>
		load_write_dailyStats("<?php 
			echo $Text['currency_sign']; ?>",<?php 
			echo $autoReload; ?>,<?php 
			echo $loadOnInit?'true':'false'; ?>);
	</script>	
	<?php
}

function write_list_account_select() {
	global $Text;
	?>
	<select id="account_select" class="longSelect">
		<option value=""><?php echo $Text['sel_account']; ?></option> 
		<option value="{id}">{id} {name}</option>
	</select>
	<?php
}

function write_list_account($addClasses = '', $p_msg_err_nomovements='', 
        $account_types='1,2,1000,2000') {
	global $Text;
	?>
	<div id="account_listing" class="ui-widget">
		<div class="ui-widget-content ui-corner-all">
			<h3 class="ui-widget-header ui-corner-all"><span
				style="color:#777"><?=$Text['latest_movements'];?>:</span> <span
				class="account_id"></span> <span
				style="float:right; margin-top:-4px;"><img
				class="loadSpinner hidden" src="img/ajax-loader.gif"/></span></h3>
			<table id="list_account" class="tblListingDefault">
			<thead>
				<tr>
					<th><?php echo $Text['date'];?></th>
					<th><?php echo $Text['operator']; ?></th>
					<th><?php echo $Text['description']; ?></th>
					<th>Type</th>
					<th class="textAlignRight"><?php 
						echo $Text['mon_amount']; ?></th>
					<th class="textAlignRight"><?php 
						echo $Text['mon_balance']; ?></th>
				</tr>
			</thead>
			<tbody>
				<tr class="xml2html_tpl">
					<td>{ts}</td>
					<td>{operator}</td>
					<td>{description}</td>
					<td>{method}</td>
					<td class="textAlignRight formatQty">{quantity}</td>
					<td class="textAlignRight formatQty">{balance}</td>
				</tr>
			</tbody>
			<tfoot>
				<tr><td colspan="2"></td></tr>
			</tfoot>
			</table>
		</div>	
	</div>
	<script>
		/**
		 * 	account extract
		 */
		function load_write_list_account(p_currency_sign,
				p_msg_err_nomovements, account_types) {
			$("#account_select").xml2html("init", {
				url: 'php/ctrl/Account.php',
				params : 'oper=getAccounts&all=0&account_types='+account_types,
				offSet : 1,
				loadOnInit: true
			}).change(function(){
				//get the id of the provider
				var account_sel = $("option:selected", this),
					account_id = account_sel.val(),
					account_name = account_sel.text();
				if (!account_id) {
					$('#list_account tbody').xml2html('removeAll');
					$('#account_listing .account_id').text('');
					return true;
				}
				$('#account_listing .account_id').text(account_name); 
				$('#list_account tbody').xml2html('reload', {
					params: 'oper=accountExtract&account_id='+account_id+
						'&filter=pastYear'
				});
			}); //end select change
			$('#list_account tbody').xml2html('init',{
				url		: 'php/ctrl/Account.php',
				loadOnInit: false,
				resultsPerPage : 20,
				paginationNav : '#list_account tfoot td',
				beforeLoad : function(){
					$('#account_listing .loadSpinner').show();
				},
				rowComplete : function (rowIndex, row){
					$.formatQuantity(row, p_currency_sign);
				},
				complete : function(rowCount){
					$('#account_listing .loadSpinner').hide();
					if ($('#list_account tbody tr').length == 0 &&
							p_msg_err_nomovements){
						$.showMsg({
							msg: p_msg_err_nomovements,
							type: 'warning'
						});
					} else {
						$('#list_account tbody tr:odd').addClass('rowHighlight'); 
					}
				}
			});
		}
	</script>
	<script>
		load_write_list_account("<?php 
			echo $Text['currency_sign']; ?>", "<?php 
			echo $p_msg_err_nomovements; ?>", "<?php 
			echo $account_types; ?>");
	</script>
	<?php
}
?>
