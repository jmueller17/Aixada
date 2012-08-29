<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Aixada - Order</title>


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
		div#member_info		{width:48%; margin-bottom:10px;}
		
		
		
	</style>
	
	<script type="text/javascript" src="../js/jquery/jquery.js"></script>
	<script type="text/javascript" src="../js/jqueryui/jqueryui.js"></script>

   	<script type="text/javascript" src="../js/aixadautilities/jquery.aixadaXML2HTML.js" ></script>
	<script type="text/javascript" src="../js/aixadautilities/jquery.aixadaUtilities.js" ></script>
 

	<script type="text/javascript">
		$(function(){

			var dateForOrder = $.getUrlVar('date');

			$('#orderDate').text(dateForOrder);
			
			//load purchase detail (products and quantities)
			$('#tbl_orderList tbody').xml2html('init',{
				url : '../ctrlShopAndOrder.php',
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
					var total = 0; 
					var totalIva = 0; 
					var totalRevTax = 0; 
					$('.itemPrice').each(function(){
						var price = new Number($(this).text());
						total += price; 

						var iva = $(this).attr('iva');
						var rev = $(this).attr('revTax');
						var tax = (price * (iva + rev))/100; 
						totalIva += (tax*iva)/100;
						totalRevTax += (tax*rev)/100;
						
					});
					$('#total').text(total.toFixed(2));
					$('#total_iva').text(totalIva.toFixed(2));
					$('#total_revTax').text(totalRevTax.toFixed(2));
					
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
		<div id="logo">
			<img alt="coop logo" width="500" height="180"/>
		</div>
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
		<div id="member_info" class="b4">
			<h2 class="memberTitle">Order for <span id="orderDate"></span></h2>
			<div class="p4-5">
				<table id="memberAddress" border="1">
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
						<td rowspan="3" class="txtAlignCenter">
							<h2>UF{uf_id}</h2>
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
					<th class="width-50">Ref.code</th>
					<th>Product</th>
					<th>Provider</th>
					<th class="width-80">Quantity</th>
					<th class="width-80">Unit</th>
					<th class="width-80">Price</th>
					<th>Total</th>
				</tr>
			</thead>
			<tbody>
				<tr isPreorder="{preorder}">
					<td class="width-50">{id}</td>
					<td>{name}</td>
					<td>{provider_name}</td>
					<td class="width-80 txtAlignRight">{quantity}</td>
					<td class="width-80 txtAlignCenter">{unit}</td>
					<td class="width-80 txtAlignRight">{unit_price}</td>
					<td class="txtAlignRight itemPrice" iva="{iva_percent}" revTax="{rev_tax_percent}"></td>				
				</tr>
			</tbody>
						
			<tfoot>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td colspan="3" class="txtAlignRight">included IVA</td>
					<td id="total_iva" class="txtAlignRight"></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td colspan="3" class="txtAlignRight">included RevTax</td>
					<td id="total_revTax" class="txtAlignRight"></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td colspan="3" class="txtAlignRight bold">Total</td>
					<td id="total" class="txtAlignRight bold"></td>
				</tr>
			</tfoot>
		</table>
		
	</div>
</body>



</html>