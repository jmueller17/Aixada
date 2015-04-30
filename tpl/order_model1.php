<?php 
    include "../php/inc/header.inc.php";
    require_once "report_header_writer.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Aixada - Order</title>


	<style type="text/css">
		body				{font-family:arial;}
		table 				{width:100%; border-collapse:collapse;}
		
		.section 			{width:90%; clear:both; margin-bottom:10px;}
		.txtAlignRight		{text-align:right;}
		.txtAlignCenter		{text-align:center;}
		.tdAlignTop			{vertical-align:top;}
		.bold				{font-weight:bold;}
		.halfWidth			{width:48%; float:left;}
		.width-50			{width:50px;}	
		.width-80			{width:80px;}	
		.memberTitle		{background-color:#efefef; text-align:center; margin-top:0px; padding:2px; font-weight:bold; margin-bottom:-5px;}	
		.b4					{border:2px solid black;}	 
		.p4-5				{padding:5px;}
		.hidden				{display:none;}
		.cellBorderList td	{border:solid 1px black; padding:2px 4px;}
		.cellBorderList th	{border:solid 1px black; background:#efefef;}
		.revTaxCol			{display:none;}
		
		
		div#logo			{width:500px; float:left;}
		div#address			{}
		div#member_info		{width:48%; margin-bottom:10px;}
		table#memberAddress {border:none;}
		
		
		
	</style>
	
	<script type="text/javascript" src="../js/jquery/jquery.js"></script>
	<script type="text/javascript" src="../js/jqueryui/jqueryui.js"></script>

   	<script type="text/javascript" src="../js/aixadautilities/jquery.aixadaXML2HTML.js" ></script>
	<script type="text/javascript" src="../js/aixadautilities/jquery.aixadaUtilities.js" ></script>
 

	<script type="text/javascript">
		$(function(){

			//prevent error msg when opening saved page
			if (window.opener == null) return false;
			
			var dateForOrder = $.getUrlVar('date');

			$('#orderDate').text(dateForOrder);
			
			//load purchase detail (products and quantities)
			$('#tbl_orderList tbody').xml2html('init',{
				url : '../php/ctrl/ShopAndOrder.php',
				params : 'oper=getOrderCart&date='+dateForOrder, 
				loadOnInit : true, 
				rowComplete : function (rowIndex, row){

					//filter out the preorder items. 
					var isPreorder = $(row).attr('isPreorder');
					if (isPreorder == "true"){
						$(row).remove();
						return false; 
					}
					
					var price = new Number($(row).children().eq(5).text());
					var qu = new Number($(row).children().eq(3).text());
					var totalPrice = price * qu;
					
					totalPrice = totalPrice.toFixed(2);
					$(row).children().eq(6).text(totalPrice);
					

				},
				complete : function(rowCount){
					var totals = $.sumItems('.itemPrice');

					$('#total').text(totals['total'] + "<?=$Text['currency_sign'];?>");
					$('#total_iva').text(totals['totalIva']+ "<?=$Text['currency_sign'];?>");
					$('#total_revTax').text(totals['totalRevTax']+ "<?=$Text['currency_sign'];?>");
					
				}
			});

			

			//load purchase detail (products and quantities)
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
		<div id="member_info" class="b4">
			<h2 class="memberTitle"><?php echo $Text['ordered_for']; ?> <span id="orderDate"></span></h2>
			<div class="p4-5">
				<table id="memberAddress">
					<tr>
						<td class="tdAlignTop">{name}<br/>
							{address}<br/>
							{zip} {city}
						</td>
						<td>
							<?php echo $Text['cif_nif'];?>: {nif}<br/>
							<?php echo $Text['member_id']; ?>: {id} / {custom_member_ref}<br/>
							<?php echo $Text['email'];?>: {email}<br/>
							<?php echo $Text['phone_pl'];?>: {phone1} / {phone2}
						</td>
						<td rowspan="3" class="txtAlignCenter">
							<h2><?php echo $Text['uf_short']; ?>{uf_id}</h2>
						</td>
					</tr>
				</table>
			</div>
		</div> 
	</div>

	<div id="item_list" class="section">
		<table id="tbl_orderList" class="cellBorderList">
			<thead>
				<tr>
					<th class="width-50"><?php echo $Text['id'];?></th>
					<th><?php echo $Text['bill_product_name'];?></th>
					<th><?php echo $Text['provider_name'];?></th>
					<th class="width-80"><?php echo $Text['quantity'];?></th>
					<th class="width-80"><?php echo $Text['unit']; ?></th>
					<th class="width-80"><?php echo $Text['price']; ?></th>
					<th><?php echo $Text['total'];?></th>
				</tr>
			</thead>
			<tbody>
				<tr isPreorder="{preorder}">
					<td class="width-50 txtAlignRight">{id}</td>
					<td>{name}</td>
					<td>{provider_name}</td>
					<td class="width-80 txtAlignRight">{quantity}</td>
					<td class="width-80">{unit}</td>
					<td class="width-80 txtAlignRight">{unit_price}</td>
					<td class="txtAlignRight itemPrice" iva="{iva_percent}" revTax="{rev_tax_percent}"></td>				
				</tr>
			</tbody>
						
			<tfoot>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td colspan="3" class="txtAlignRight"><?php echo $Text['incl_iva']; ?></td>
					<td id="total_iva" class="txtAlignRight"></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td colspan="3" class="txtAlignRight"><?php echo $Text['incl_revtax']; ?></td>
					<td id="total_revTax" class="txtAlignRight"></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td colspan="3" class="txtAlignRight bold"><?php echo $Text['total'];?></td>
					<td id="total" class="txtAlignRight bold"></td>
				</tr>
			</tfoot>
		</table>
		
	</div>
</body>



</html>