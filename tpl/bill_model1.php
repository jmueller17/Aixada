<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Aixada - Bill - Member - info</title>


	<style type="text/css">
		table 				{width:100%;}
		
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
		.cellBorderList td	{border:solid 1px black; border-collapse:collapse; padding:2px 4px;}
		.cellBorderList th	{border:solid 1px black; background:#efefef;}
		
		
		div#logo			{width:500px; height:180px; float:left; border:1px solid black; margin-bottom:20px;}
		div#address			{}
		div#bill_info		{width:48%; margin-right:10px; float:left;}
		div#member_info		{width:48%; float:right; margin-bottom:10px;}
		
		
		
	</style>
	
	<script type="text/javascript" src="../js/jquery/jquery.js"></script>

   	<script type="text/javascript" src="../js/aixadautilities/jquery.aixadaXML2HTML.js" ></script>
 

	<script type="text/javascript">
		$(function(){

			
			
			//load purchase detail (products and quantities)
			$('#tbl_purchaseList tbody').xml2html('init',{
				url : '../ctrlShop.php',
				params : 'oper=getShopDetail&shop_id=2468', 
				loadOnInit : true, 
				rowComplete : function (rowIndex, row){
					var price = new Number($(row).children().eq(4).text());
					var qu = new Number($(row).children().eq(2).text());
					var totalPrice = price * qu;
					totalPrice = totalPrice.toFixed(2);
					$(row).children().eq(5).text(totalPrice);

					if (rowIndex == 1) $('#cart_id').text($(row).children().eq(6).text()) 
				},
				complete : function(rowCount){
					var total = 0; 
					$('.itemPrice').each(function(){
						var price = new Number($(this).text());
						total += price; 
					});
					$('#total').text(total.toFixed(2));

					
				}
			});

			

			//load purchase detail (products and quantities)
			$('#memberAddress').xml2html('init',{
				url : '../smallqueries.php',
				params : 'oper=getMemberInfo&member_id=-1', 
				loadOnInit : true,
				rowComplete : function (rowIndex, row){ 
				}
			});
				


		}); //close document ready
	</script>
	
</head>
<body>
	
	<div id="header" class="section">
		<div id="logo"></div>
		<div id="address">
			<h2 class="txtAlignRight">COOPERATIVA XXXXXX</h2>
			<h2 class="txtAlignRight">CIF/NIF: F650000000</h2>
			<p class="txtAlignRight">Street<br/>
			Zip City<br/>
			email@bla.com
			</p>
		</div>
	</div>

	<div id="info" class="section">
		<div id="bill_info">
			<table class="b4 cellBorderList">
				<tr>
					<th>FACTURA</th>
					<th>DATA</th>
					<th>REALITZADA PER</th>
				</tr>	
				<tr>
					<td class="txtAlignCenter" id="cart_id"></td>
					<td class="txtAlignCenter" id="date_for_shop"></td>
					<td class="txtAlignCenter" id="operator"></td>
				</tr>							
				
			</table>
		</div>
		<div id="member_info" class="b4">
			<p class="memberTitle">SOCI</p>
			<div class="p4-5">
				<table id="memberAddress">
					<tr>
						<td class="tdAlignTop">{name}<br/>
							{address}<br/>
							{zip} {city}
						</td>
						<td>
							CIF/NIF: {nif}<br/>
							Codi soci: {member_id}<br/>
							EmaiL: {email}<br/>
							Phone(s): {phone1} / {phone2}
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
					<th class="width-50">Ref.Codi</th>
					<th>Concepte</th>
					<th class="width-80">Quantitat</th>
					<th class="width-80">Unitat</th>
					<th class="width-80">Preu</th>
					<th>Total</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="width-50">{id}</td>
					<td>{name}</td>
					<td class="width-80 txtAlignRight">{quantity}</td>
					<td class="width-80 txtAlignCenter">{unit}</td>
					<td class="width-80 txtAlignRight">{unit_price}</td>
					<td class="txtAlignRight itemPrice"></td>
					<td class="hidden">{cart_id}</td>
					<td class="hidden">{iva_percent}</td>
					<td class="hidden">{rev_tax_percent}</td>
					
					
				</tr>
			</tbody>
						
			<tfoot>
				<tr>
					<td></td>
					<td></td>
					<td colspan="3" class="txtAlignRight">Import brut</td>
					<td id="import_brut"></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td colspan="3" class="txtAlignRight">Import net</td>
					<td id="import_net"></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td class="txtAlignRight">IVA</td>
					<td id="iva"></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td colspan="3" class="txtAlignRight bold">Total factura</td>
					<td id="total" class="txtAlignRight bold"></td>
				</tr>
			</tfoot>
		</table>
		
	</div>
</body>



</html>