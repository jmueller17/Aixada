<?php 
    include "../php/inc/header.inc.php";
    require_once "report_header_writer.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Aixada - Bill - Member - info</title>


	<style type="text/css">
		body 				{font-family:arial; font-size:10px;}
		table 				{width:100%; border-collapse:collapse;}
		
		.section 			{width:90%; clear:both; margin-bottom:10px;}
		.txtAlignRight		{text-align:right;}
		.txtAlignCenter		{text-align:center;}
		.tdAlignTop			{vertical-align:top;}
		.bold				{font-weight:bold;}
		.halfWidth			{width:48%; float:left;}
		.width-50			{width:50px;}	
		.width-80			{width:80px;}	
		.memberTitle		{background-color:#efefef; text-align:center; margin-top:0px; padding:2px; font-weight:bold; margin-bottom:-5px; text-transform:uppercase; }	
		.b4					{border:2px solid black;}	 
		.p4-5				{padding:5px;}
		.hidden				{display:none;}
		.cellBorderList td	{border:solid 1px black; padding:2px 5px;}
		.cellBorderList th	{border:solid 1px black; padding:2px 5px; background:#efefef;}
		.billHead			{text-transform:uppercase;}
		.revTaxCol			{display:none;}
		
		
		div#logo			{width:500px; float:left;}
		div#address			{}
		div#bill_info		{width:48%; margin-right:10px; float:left;}
		div#member_info		{width:48%; float:right; margin-bottom:10px;}
		
		
		
	</style>
	
	<script type="text/javascript" src="../js/jquery/jquery.js"></script>
	<script type="text/javascript" src="../js/jqueryui/jqueryui.js"></script>

   	<script type="text/javascript" src="../js/aixadautilities/jquery.aixadaXML2HTML.js" ></script>
	<script type="text/javascript" src="../js/aixadautilities/jquery.aixadaUtilities.js" ></script>
	<script type="text/javascript" src="../js/aixadautilities/loadPDF.js" ></script>
	
 

	<script type="text/javascript">
		$(function(){
			$.ajaxSetup({ cache: false });

			//boolean to generate a pdf of this bill 
			var asPDF = $.getUrlVar('asPDF');

			//"F" opens the pdf in browser window, "D" forces file download
			var outputFormat =  $.getUrlVar('outputFormat');
			

			
			//prevent error msg when opening saved page
			//if (window.opener == null) return false;

			var shopId = $.getUrlVar('shopId');
			var date = $.getUrlVar('date');
			var operatorName = decodeURIComponent($.getUrlVar('operatorName'));
			var operatorUf = $.getUrlVar('operatorUf');

			
			$('#cart_id').text(shopId);
			$('#date_for_shop').text(date);

			if (operatorUf == ''){
				$('#operator').text("<?php echo $Text['not_yet_val']; ?>");
			} else {
				$('#operator').text(operatorName + " (<?php echo $Text['uf_short']; ?>"+operatorUf+")");
			}		
			
			//load purchase detail (products and quantities)
			$('#tbl_purchaseList tbody').xml2html('init',{
				url : '../php/ctrl/Shop.php',
				params : 'oper=getShopCart&shop_id='+shopId, 
				loadOnInit : true, 
				rowComplete : function (rowIndex, row){
					var price = new Number($(row).children().eq(4).text());
					var qu = new Number($(row).children().eq(2).text());
					var totalPrice = price * qu;
					
					totalPrice = totalPrice.toFixed(2);
					$(row).children().eq(7).text(totalPrice);
					

				},
				complete : function(rowCount){

					var totals = $.sumItems('.itemPrice');

					$('#total').text(totals['total'] + "<?=$Text['currency_sign'];?>");
					$('#total_iva').text(totals['totalIva']+ "<?=$Text['currency_sign'];?>");
					$('#total_revTax').text(totals['totalRevTax']+ "<?=$Text['currency_sign'];?>");
					$('#import_net').text(totals['total_net']+ "<?=$Text['currency_sign'];?>");

					if (asPDF) {
						var pathToImg = $('#coopLogo').attr('src');
						$('#coopLogo').attr('src', "../"+pathToImg);
						downloadPDF(outputFormat, '<?=$Text['bill'];?>');
					}
					
				}
			});

			

			//load load member info
			$('#memberAddress').xml2html('init',{
				url : '../php/ctrl/UserAndUf.php',
				params : 'oper=getMemberInfo&member_id=-1', 
				loadOnInit : true,
				rowComplete : function (rowIndex, row){ 
				}
			});

			

		}); //close document ready
	</script>
	
</head>
<body>
	
	<div id="header" class="section"><?php write_tpl_header(); ?></div>

	<div id="info" class="section">
		<div id="bill_info">
			<table class="b4 cellBorderList">
				<tr>
					<th class="billHead"><?php echo $Text['bill']; ?></th>
					<th class="billHead"><?php echo $Text['date']; ?></th>
					<th class="billHead"><?php echo $Text['operator']; ?></th>
				</tr>	
				<tr>
					<td class="txtAlignCenter" id="cart_id"></td>
					<td class="txtAlignCenter" id="date_for_shop"></td>
					<td class="txtAlignCenter" id="operator"></td>
				</tr>							
				
			</table>
		</div>
		<div id="member_info" class="b4">
			<p class="memberTitle"><?php echo $Text['member']; ?></p>
			<div class="p4-5">
				<table id="memberAddress">
					<tr>
						<td class="tdAlignTop">{name}<br/>
							{address}<br/>
							{zip} {city}
						</td>
						<td>
							<?php echo $Text['cif_nif'];?>: {nif}<br/>
							<?php echo $Text['member_id']; ?>: {id}  / {custom_member_ref}<br/>
							<?php echo $Text['email'];?>: {email}<br/>
							<?php echo $Text['phone_pl'];?>: {phone1} / {phone2}
						</td>
					</tr>
				</table>
			</div>
		</div> 
	</div>

	<div id="item_list" class="section">
		<table id="tbl_purchaseList" class="cellBorderList">
			<thead>
				<tr>
					<th class="width-50"><?php echo $Text['id'];?></th>
					<th><?php echo $Text['bill_product_name'];?></th>
					<th class="width-80"><?php echo $Text['quantity'];?></th>
					<th class="width-80"><?php echo $Text['unit']; ?></th>
					<th class="width-80 txtAlignRight"><?php echo $Text['price']; ?></th>
					<th class="width-80 txtAlignRight"><?php echo $Text['iva']; ?></th>
					<th class="width-80 txtAlignRight revTaxCol"><?php echo $Text['revtax_abbrev']; ?></th>
					<th class="txtAlignRight"><?php echo $Text['total'];?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="width-50 txtAlignRight">{id} </td>
					<td>{name}</td>
					<td class="width-80 txtAlignRight">{quantity}</td>
					<td class="width-80 txtAlignLeft">{unit}</td>
					<td class="width-80 txtAlignRight">{unit_price}</td>
					<td class="width-80 txtAlignRight">{iva_percent}%</td>
					<td class="width-80 txtAlignRight revTaxCol">{rev_tax_percent}%</td>
					<td class="txtAlignRight itemPrice" iva="{iva_percent}" revTax="{rev_tax_percent}"></td>
									
				</tr>
			</tbody>
						
			<tfoot>
				<tr>
					<td></td>
					<td></td>
					<td class="revTaxCol"></td>
					<td colspan="4" class="txtAlignRight"><?php echo $Text['gross_amount']; ?></td>
					<td id="import_brut"></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td class="revTaxCol"></td>
					<td colspan="4" class="txtAlignRight"><?php echo $Text['net_amount']; ?></td>
					<td id="import_net" class="txtAlignRight"></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td class="revTaxCol"></td>
					<td colspan="4" class="txtAlignRight"><?php echo $Text['incl_iva']; ?></td>
					<td id="total_iva" class="txtAlignRight"></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td class="revTaxCol"></td>
					<td colspan="4" class="txtAlignRight"><?php echo $Text['incl_revtax']; ?></td>
					<td id="total_revTax" class="txtAlignRight"></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td class="revTaxCol"></td>
					<td colspan="4" class="txtAlignRight bold"><?php echo $Text['bill_total']; ?></td>
					<td id="total" class="txtAlignRight bold"></td>
				</tr>
			</tfoot>
		</table>
		
	</div>
</body>



</html>