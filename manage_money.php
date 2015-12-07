<?php 
	include "php/inc/header.inc.php";    
	require_once(__ROOT__.'php/lib/account_operations.config.php');
	require_once(__ROOT__.'php/lib/account_writers.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_cashbox'];?></title>

	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
	<link rel="stylesheet" type="text/css"   media="print"  href="css/print.css" />
	<link rel="stylesheet" type="text/css"   media="screen" href="js/fgmenu/fg.menu.css"   />
	<link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/<?=$default_theme;?>/jqueryui.css"/>
<style>
	table.tblForms td label { float: none; }
	/* ol_selectable */
	.ol_selectable .ui-selecting { background: #FECA40;  }
	.ol_selectable .ui-selected  {
        background: #F39814; border-color: #555;
        color: white;
    }
	.ol_selectable { 
		list-style-type: none; 
		margin: 0; padding: 0; 
		line-height: 200%;
	}
	.ol_selectable li {
		display: inline-block; 
		font-weight: normal;
		cursor: pointer; 
		margin: 3px; padding: 0 0.4em; 
		border-radius: 4px;
	}
    .vertical li {
        display: block;
        text-align: center;
    }
    /* layout */
    #ops {
        float:left; 
        width:7.4em; 
        margin-left:-1.2em; 
        padding:2px; 
        background-color:#fdd; 
        border-radius: 4px; border: 1px solid #bbb;
    }
    #ops-1, #ops-2 {
        margin-left:6.8em;
        padding:0 .5em 0 0;
    }
	table.tblListingDefault th,
	table.tblListingDefault td {
		vertical-align: middle
	}
</style>
	
    <script type="text/javascript" src="js/jquery/jquery.js"></script>
    <script type="text/javascript" src="js/jqueryui/jqueryui.js"></script>
    <script type="text/javascript" src="js/fgmenu/fg.menu.js"></script>
    <script type="text/javascript" src="js/aixadautilities/jquery.aixadaMenu.js"></script>     	 
    <script type="text/javascript" src="js/aixadautilities/jquery.aixadaXML2HTML.js" ></script>
    <script type="text/javascript" src="js/aixadautilities/jquery.aixadaUtilities.js" ></script>

<?php
// Generate html to use in the form.
	function write_account_html_select($name, $text_dir, $text_name, $ctr_options) {
		$html = 
			'<tr class="'.$name.'_tr"><td>'.i18n($text_dir).
				' <span style="color:#777">'.i18n($text_name).'</span></td>'.
			'<td><select name="'.$name.'_id" id="'.$name.'_id">'.
				'<option value="" selected="selected">(...)</option>'.
				'<option value="{id}">{name}</option>'.
				"</select>\n".
			'<script>'.
			'set_optionsLoad_controller("'.$name.'_id","'.$ctr_options.'");'.
			"</script></td></tr>\n";
		echo $html;
	}
	$local_accounts = get_config('accounts', array());
	$local_use_providers = isset($local_accounts['use_providers']) && 
			$local_accounts['use_providers'];
	function write_operation_html_ul($use_providers, $correction) { // filter by local_config
		global $local_accounts;
		global $config_account_operations;
		if (isset($local_accounts['account_operations'])) {
			$local_operation_accounts = $local_accounts['account_operations'];
			foreach ($local_operation_accounts as $operation_name) {
                write_operation_html_li(
                        $use_providers, $correction, $operation_name);
			}
		} else {
			foreach ($config_account_operations as $operation_name => $cfg_operation) {
                write_operation_html_li(
                        $use_providers, $correction, $operation_name);
			}
		}
	}
	function write_operation_html_li($use_prov, $correction, $operation_name) {
        global $config_account_operations;
        $cfg_operation = $config_account_operations[$operation_name];
        $is_corr = isset($cfg_operation['correction']) && $cfg_operation['correction'];
        if ($correction !== !!$is_corr) {
            return;
        }
		if ($use_prov || (
				!array_key_exists('provider_from', $cfg_operation['accounts']) && 
				!array_key_exists('provider_to', $cfg_operation['accounts'])) ) {
			echo '<li class="ui-widget-header" val="'.$operation_name.'">'.
					i18n('mon_op_'.$operation_name).'</li>';
		}
	}
	function write_cfg_operations_to_js() {
		global $config_account_operations;
		foreach ($config_account_operations as $operation_name => $cfg_operation) {
			$id_names = array();
			$description = '';
			foreach ($cfg_operation['accounts'] as $account_id_name => $o_params) {
				if (isset($o_params['default_desc'])) {
					$description = i18n('mon_desc_'.$o_params['default_desc']);
				}
				array_push($id_names, '"'.$account_id_name.'"');
			}
			echo "
			{$operation_name}: {
				op:[".implode(', ', $id_names)."],
				desc: \"{$description}\"
			},";
		}
		echo "  false: null //end of list;\n";
	}
?>
<script>
	function set_optionsLoad_controller(id, ctr_options) {
		$('#'+id).xml2html("init",{
			url: "php/ctrl/Account.php",
			params: 'oper=getAccounts&all=0'+ctr_options,
			offSet	: 1,
			loadOnInit : true,
			complete : function(){ }
		})
		.change(function(e){
			allowOperation();
		});
	}
</script>

<script type="text/javascript">
	// Texts of the literals in the language of the user. For use in js.
	var local_lang = {
		mon_send: "<?php echo i18n('mon_send'); ?>",
		currency_sign: "<?php echo i18n('currency_sign'); ?>"
	};

	// Configuration values used by js code.
	var local_cfg = {
		default_theme: "<?=$default_theme;?>",
		use_providers: <?=($local_use_providers?'true':'false')?>
	};
	
	// Operation configuration
	var locat_cfg_operations = {<?php write_cfg_operations_to_js(); ?>};

</script>
<script>
	var _operationAccounts = [];
	function allowOperation(){
		var allow = true;
		if (!$.isNumeric($('#operation_amount').val())) {
			allow = false;
		};
		for (var i = 0, len = _operationAccounts.length; i <len; i++) {
			var aa=$('#'+_operationAccounts[i]+'_id');
			if (!$.isNumeric($('#'+_operationAccounts[i]+'_id').val())) {
				allow = false;
			};
		}
		if (allow) {
			$('#operation_submit').button('enable');
		} else {
			$('#operation_submit').button('disable');
		}
	}
	function account_operation_change(operation_code){
		$('.account_to_tr').hide();
		$('.account_from_tr').hide();
		$('.uf_to_tr').hide();
		$('.uf_from_tr').hide();
		$('.provider_to_tr').hide();
		$('.provider_from_tr').hide();
		var _operationAccounts = locat_cfg_operations[operation_code];
		if (!_operationAccounts) {
			_operationAccounts = {op:[], desc:''};
			$('.mov_text_tr').hide();
			return;
		}
		for (var i = 0, len = _operationAccounts.op.length; i <len; i++) {
			$('.'+_operationAccounts.op[i]+'_tr').show();
		}
		$('#operation_note')
			.val('')
			.attr("placeholder", _operationAccounts.desc);
		$('.mov_text_tr').show();
	}
	function endOperation() {
		$('#operation_amount').val('');
		allowOperation();
		reloadOperationPanels();
	}
	function reloadOperationPanels() {
		var _reload = function(selector){
			var sel_items = selector.split(" ");
			if ($(sel_items[0]).css('display') !== "none") {
				$(selector).xml2html('reload');
			}
		};
		_reload('#latestMovements tbody');
		_reload('#accountBalances tbody');
		_reload('#dailyStats tbody');
		_reload('#negative_ufs tbody');
		_reload('#uf_balances tbody');
		if (local_cfg.use_providers) {
			_reload('#provider_balances tbody');

		}
	}
	
	$(function(){
		$.ajaxSetup({ cache: false });

		//loading animation
		$('.loadSpinner').attr('src', "img/ajax-loader-"+
			local_cfg.default_theme+".gif").hide();
	
		//init tabs
		$("#tabs").tabs({
			'select': function(event, ui) {
				// Reload the list account
				if (ui.tab && $(ui.tab).attr('href') === "#tabs-2") {
					if ($('#account_select').val()) {
						$('#list_account tbody').xml2html('reload');
					}
				}
			}
		});

		/**
		 * 	MONITOR Money, daily stats, negative ufs
		 */
		$('#latestMovements tbody').xml2html('init',{
				url		: 'php/ctrl/Account.php',
				params	: 'oper=latestMovements&account_types=1,1000,2000',
				beforeLoad : function(){
					$('#latestMovements_ui .loadSpinner').show();
				},
				rowComplete : function (rowIndex, row){
					$.formatQuantity(row, local_lang.currency_sign);
				},
				complete : function (rowCount){
					$('#latestMovements_ui .loadSpinner').hide();
					$('#latestMovements tbody tr:odd').addClass('rowHighlight');
				}
		});

		//Account balances
		$('#accountBalances tbody').xml2html('init',{
			url		: 'php/ctrl/Account.php',
			params	: 'oper=getBalances&account_types=1,1000,2000',
			beforeLoad : function(){
				$('#accountBalances_ui .loadSpinner').show();
			},
			rowComplete : function (rowIndex, row){
				$.formatQuantity(row, local_lang.currency_sign);
			},
			complete : function(){
				$('#accountBalances_ui .loadSpinner').hide();
				$('#accountBalances tbody tr:odd').addClass('rowHighlight');
			}
		});
		
		//UF balances
		$('#uf_balances tbody').xml2html('init',{
			url		: 'php/ctrl/Account.php',
			params	: 'oper=getUfBalances',
			beforeLoad : function(){
				$('#uf_balances_ui .loadSpinner').show();
			},
			rowComplete : function (rowIndex, row){
				$.formatQuantity(row, local_lang.currency_sign);
			},
			complete : function(){
				$('#uf_balances_ui .loadSpinner').hide();
				$('#uf_balances tbody tr:odd').addClass('rowHighlight');
			}
		});

		//Providers balances
		if (local_cfg.use_providers) {
			$('#provider_balances tbody').xml2html('init',{
				url		: 'php/ctrl/Account.php',
				params	: 'oper=getProviderBalances',
				beforeLoad : function(){
					$('#provider_balances_ui .loadSpinner').show();
				},
				rowComplete : function (rowIndex, row){
					$.formatQuantity(row, local_lang.currency_sign);
				},
				complete : function(){
					$('#provider_balances_ui .loadSpinner').hide();
					$('#provider_balances tbody tr:odd').addClass('rowHighlight');
				}
			});
		}
		
		// Show/hide blocks
		$('.left-icons').bind("mouseenter", function(){
			if ($(this).hasClass('ui-icon-triangle-1-s')){
				$(this).removeClass('ui-icon-triangle-1-s')
					.addClass('ui-icon-circle-triangle-s');
			} else {
				$(this).removeClass('ui-icon-triangle-1-e')
					.addClass('ui-icon-circle-triangle-e');
			}
		}).bind("mouseleave", function(){
			if ($(this).hasClass('ui-icon-circle-triangle-s')){
				$(this).removeClass('ui-icon-circle-triangle-s')
					.addClass('ui-icon-triangle-1-s');
			} else {
				$(this).removeClass('ui-icon-circle-triangle-e')
					.addClass('ui-icon-triangle-1-e');
			}
		}).bind("click", function(){
			$(this).parent().next().toggle();
			reloadOperationPanels();
			if ($(this).hasClass('ui-icon-circle-triangle-s')){
				$(this).removeClass('ui-icon-circle-triangle-s')
					.addClass('ui-icon-circle-triangle-e');
			} else {
				$(this).removeClass('ui-icon-circle-triangle-e')
					.addClass('ui-icon-circle-triangle-s');
			}
		});
		
		// Operations
        $("#ops").selectable({
            selected: 1,
            selecting: function (event, ui) {
				// Can select only one button
				$(event.target).children('.ui-selected').removeClass('ui-selected');
			},
            stop: function() {
                $( ".ui-selected", this ).each(function() {
                    var selEle = $(this).attr("val");
                    var noSel = selEle === '#ops-1' ? '#ops-2' : '#ops-1';
                    $(noSel).hide()
                        .children('.ui-selected').removeClass('ui-selected');
					$(selEle).show()
                        .children('.ui-selected').removeClass('ui-selected');
                    // No operation is selected.
                    account_operation_change("");
                    allowOperation();
				});
			}
        });
        var selectableObj = {
			selecting: function (event, ui) {
				// Can select only one button
				$(event.target).children('.ui-selected').removeClass('ui-selected');
			},
			stop: function() {
				var operation_code ='';
				$( ".ui-selected", this ).each(function() {
					// Set text button
					$('#operation_submit .ui-button-text').text(
						local_lang.mon_send+': '+$(this).text()
					);
					// Set operation code
					operation_code = $(this).attr("val");
				});
				$("#account_operation").val(operation_code);
				account_operation_change(operation_code);
				allowOperation();
			}
		};
		$("#ops-1").selectable(selectableObj);
        $("#ops-2").selectable(selectableObj);
		$('#operation_amount').change(function(e){
			allowOperation();
		});
		$('#operation_submit').button({
			disabled:true,
			icons: {
				primary: "ui-icon-arrowthick-1-s"
			}
		}).click(function(){
			var dataSerial = $('#operation_form').serialize();
			$('#operation_submit').button('disable');
			$.ajax({
				type: "POST",
				url: "php/ctrl/Account.php?oper=addOperation",
				data: dataSerial,
				beforeSend : function (){
					$('#operationAnim').show();
				},
				success: function(msg){
					$.updateTips("#submitMsg", "success", msg);
					endOperation();
				},
				error: function(XMLHttpRequest, textStatus, errorThrown){
					$.updateTips("#submitMsg", "error", XMLHttpRequest.responseText);
				},
				complete : function(msg){
					$('#operation_submit').button('enable');
					$('#operationAnim').hide();
				}
			}); //end ajax
		});
		account_operation_change("");
		allowOperation();
		reloadOperationPanels();
	});  //close document ready
</script>

</head>
<?php flush(); ?>
<body>
<div id="wrap">
<div id="headwrap">
	<?php include "php/inc/menu.inc.php" ?>
</div><!-- end of headwrap -->
<div id="stagewrap" class="ui-widget">
	<div id="titlewrap">			
		<h1><?php echo $Text['head_ti_cashbox']; ?></h1>
	</div>
	<div class="aix-layout-splitW60 floatLeft">
	<div id="tabs">
		<span style="float:right; margin-top:6px; margin-right:10px;" ><img id="cartAnim" class="loadSpinner" src="img/ajax-loader.gif"/></span>
		<ul>
			<li><a href="#tabs-1"><?php echo $Text['mon_operation_account']; ?></a></li>
			<li><a href="#tabs-2"><?php echo $Text['mon_list_account']; ?></a></li>
		</ul>
		<div id="tabs-1">
			<div class="ui-widget">
			<form id="operation_form" onsubmit="return false;">
                <div>
					<input type="hidden" name="account_operation" id="account_operation" value=""/>
                    <ul id="ops" class="ol_selectable vertical">
                        <li class="ui-widget-header ui-selected" val="#ops-1"><?php echo $Text['mon_ops_standard']; ?></li>
                        <li class="ui-widget-header" val="#ops-2"><?php echo $Text['mon_ops_corrections']; ?></li>
                    </ul>
					<ul id="ops-1" class="ol_selectable"><?php 
						write_operation_html_ul($local_use_providers, false); 
					?></ul>
                    <ul id="ops-2" class="ol_selectable hidden"><?php 
						write_operation_html_ul($local_use_providers, true);
					?></ul -->
				</div>
				<div id="submitMsg" style="clear:both"></div>
				<table class="tblForms" style="width:100%;clear:both">
				<tr><td style="padding-bottom:5px" colspan="2">
				<?php
					write_account_html_select(
						'account_from', 'mon_from', 'account',      '&account_types=1');
					write_account_html_select(
						'uf_from',      'mon_from', 'uf_long',      '&account_types=1000');
					write_account_html_select(
						'provider_from','mon_from', 'provider_name','&account_types=2000');
					write_account_html_select(
						'account_to',   'mon_to',   'account',      '&account_types=1');
					write_account_html_select(
						'uf_to',        'mon_to',   'uf_long',      '&account_types=1000,1999');
					write_account_html_select(
						'provider_to',  'mon_to',   'provider_name','&account_types=2000');
				?>
				<tr class="mov_text_tr"><td><?php echo $Text['amount'];?>:&nbsp;&nbsp;</td>
					<td><input type="text" autocomplete="off" name="quantity" id="operation_amount" class="inputTxtMiddle ui-widget-content ui-corner-all" value=""/></td></tr>
				<tr class="mov_text_tr"><td><?php echo $Text['comment'];?>:&nbsp;&nbsp;</td>
					<td><input type="text" autocomplete="off" name="description" id="operation_note" class="inputTxtMax ui-widget-content ui-corner-all" value=""/></td></tr>
				<tr><td style="width:10em">&nbsp;</td><td>
					<button id="operation_submit"><?=$Text['btn_make_deposit']; ?></button>
					<span class="loadAnim floatRight hidden" id="operationAnim">
						<img src="img/ajax-loader.gif"/></span>
					</td>
				</tr>
				</table>
			</form>
			</div>
		</div><!-- end tab-1 -->
		<div id="tabs-2">
			<div class="ui-widget">
				<div><?php write_list_account_select(); ?></div>
				<?php write_list_account('', '', '1,1000,2000'); ?>
			</div>
		</div><!-- end tab-2 -->
	</div><!-- end tabs -->
	<br>
	<div id="latestMovements_ui" class="ui-widget">
		<div class="ui-widget-content ui-corner-all aix-style-observer-widget">
			<h3 class="ui-widget-header ui-corner-all"><span 
				class="left-icons ui-icon ui-icon-triangle-1-s"></span><?php
					echo $Text['latest_movements'];?> <span 
				class="loadAnim floatRight"><img class="loadSpinner" src="img/ajax-loader.gif"/></span></h3>
			<table id="latestMovements" class="tblListingDefault">
			<thead>
				<tr>
					<th colspan="2"><?php echo $Text['account'];?></th>
					<th><?php echo $Text['transfer_type'];?></th>
					<th class="textAlignRight"><?php echo $Text['mon_amount'];?>&nbsp;</th>
					<th class="textAlignRight"><?php echo $Text['mon_balance'];?>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{account_id}</td>
					<td>{account_name}</td>
					<td>{method}</td>
					<td><p class="textAlignRight"><span class="formatQty">{quantity}</span></p></td>
					<td><p class="textAlignRight"><span class="formatQty">{balance}</span></p></td>
				</tr>
			</tbody>
			</table>
		</div>
	</div>
	</div><!-- end left col -->
	<div class="aix-layout-splitW40 floatRight">
		<?php write_dailyStats('', true, 0, false); ?>
		<div class="ui-widget">
			<div id="accountBalances_ui" 
					class="ui-widget-content ui-corner-all aix-style-observer-widget">
				<h3 class="ui-widget-header ui-corner-all"><span 
					class="left-icons ui-icon ui-icon-triangle-1-e"></span><?php
						echo $Text['mon_accountBalances']; ?> <span 
					class="loadAnim floatRight hidden"><img 
					class="loadSpinner" src="img/ajax-loader.gif"/></span></h3>
				<table id="accountBalances" class="tblListingDefault hidden">
					<thead>
						<tr>
							<th colspan="2"><?php echo $Text['account'];?></th>
							<th class="textAlignRight"><?php 
								echo $Text['mon_balance'];?>&nbsp;</th>
							<th class="textAlignRight"><?php
								echo $Text['mon_result'];?>&nbsp;</th>
							<th><?php echo $Text['mon_lastOper'];?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>{account_id}</td>
							<td>{name}</td>
							<td><p class="textAlignRight">
								<span class="formatQty">{balance}</span>
							</p></td>
							<td><p class="textAlignRight">
								<span class="formatQty">{result}</span>
							</p></td>
							<td>{last_update}</td>
						 </tr>
					</tbody>
				</table>
			</div>         
		</div>
		<?php write_negative_ufs('', true, 0, false); ?>
		<div id="uf_balances_ui" class="ui-widget">
			<div class="ui-widget-content ui-corner-all aix-style-observer-widget">
				<h3 class="ui-widget-header ui-corner-all"><span 
					class="left-icons ui-icon ui-icon-triangle-1-e"></span><?php
						echo $Text['mon_uf_balances'];?><span 
					class="loadAnim floatRight"><img 
					class="loadSpinner" src="img/ajax-loader.gif"/></span></h3>
					<table id="uf_balances" class="tblListingDefault hidden">
						<thead>
							<tr>
								<th class="textAlignRight"><?php echo $Text['uf_short'];?></th>
								<th class="textAlignLeft"><?php echo $Text['name'];?></th>
								<th class="textAlignRight"><?php echo $Text['mon_balance'];?>&nbsp;</th>
								<th><?php echo $Text['mon_lastOper'];?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><p class="textAlignRight">{uf}</p></td>
								<td><p class="textAlignLeft">{name}</p></td>
								<td><p class="textAlignRight"><span class="formatQty">{balance}</span></p></td>
								<td>{last_update}</td>
							</tr>
						</tbody>
					</table>

			</div>
		</div>
		<?php if ($local_use_providers) { ?>		
		<div id="provider_balances_ui" class="ui-widget">
			<div class="ui-widget-content ui-corner-all aix-style-observer-widget">
				<h3 class="ui-widget-header ui-corner-all"><span 
					class="left-icons ui-icon ui-icon-triangle-1-e"></span><?php
						echo $Text['mon_provider_balances'];?><span 
					class="loadAnim floatRight"><img 
					class="loadSpinner" src="img/ajax-loader.gif"/></span></h3>
				<table id="provider_balances" class="tblListingDefault hidden">
					<thead>
						<tr>
							<th class="textAlignLeft" colspan="2"><?php echo $Text['provider_name'];?></th>
							<th class="textAlignRight"><?php echo $Text['mon_balance'];?>&nbsp;</th>
							<th style="width:125px"><?php echo $Text['mon_lastOper'];?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><p class="textAlignRight">{prv_id}</p></td>
							<td><p class="textAlignLeft">{name}</p></td>
							<td><p class="textAlignRight"><span class="formatQty">{balance}</span></p></td>
							<td style="width:125px">{last_update}</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<?php } ?>
	</div><!-- end of right col -->
</div><!-- end of stage wrap -->
</div><!-- end of wrap -->
<!-- / END -->
</body>
</html>