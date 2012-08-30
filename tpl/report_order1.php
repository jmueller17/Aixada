<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Aixada - Report - Order</title>


	<style type="text/css">
		table 				{width:100%;}
		
		
		.section 			{width:90%; clear:both; margin-bottom:10px;}
		.txtAlignRight		{text-align:right;}
		.txtAlignCenter		{text-align:center;}
		.tdAlignTop			{vertical-align:top;}
		.bold				{font-weight:bold;}


		.b4					{border:2px solid black;}	 
		.p4-5				{padding:5px;}
		.hidden				{display:none;}
		.pBreak 			{border-bottom:dashed 1px gray; margin-bottom:40px; display:block;}
		td	{border:solid 1px black; border-collapse:collapse; padding:1px 1px;}
		th	{border:solid 1px black; background:#efefef;}
		
		
		div#logo			{width:500px; height:180px; float:left; border:1px solid black; margin-bottom:20px;}
		div#address			{}
		div#member_info		{width:48%; margin-bottom:10px;}

		@media print {
	  		.pBreak  { display:block; page-break-before: always; }
		}

		
	</style>
	
	
	<script type="text/javascript" src="../js/jquery/jquery.js"></script>
	<script type="text/javascript" src="../js/jqueryui/jqueryui.js"></script>

   	<script type="text/javascript" src="../js/aixadautilities/jquery.aixadaXML2HTML.js" ></script>
	<script type="text/javascript" src="../js/aixadautilities/jquery.aixadaUtilities.js" ></script>
 

	<script type="text/javascript">

		
	
		$(function(){


		window.opener.$('input:checkbox[name="bulkAction"]').each(function(){
			if ($(this).is(':checked')){
				
				//var orderId = $(this).parents('tr').attr('orderId');
			
				//var orderDate 		= $(this).parents('tr').children().eq(3).text();

				//var provider_name 	= $(this).parents('tr').children().eq(2).text() + ' (#'+orderId+')' ;
				
				//global_print_list[i][1] = $(this).parents('tr').attr('dateForOrder');
				//global_print_list[i][2] = $(this).parents('tr').attr('providerId');
				//i++;
			} 
		});

		$('.clickPageBreak')
			.live('click', function(e){

				if ($(this).hasClass('pBreak')){
					$(this).removeClass('pBreak');
					$(this).find('span').text('add');
				} else {
					$(this).addClass('pBreak');
					$(this).find('span').text('remove');
				}

					

			})
		
		//var p1 = $.getUrlVar('dates');
		//var p2 = $.getUrlVar('provider_ids');

		//var dates = 
		
		 /*var tbl = $(opener.getTable()).clone();

		 $(tbl).addClass('cellBorderList');
		 $('.revisedCol, .arrivedCol', tbl).hide();
		 		 
		 $(stage).after(tbl);*/

			//alert(opener.global_print_list);
			

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
		
	</div>

	<div id="orderWrap" class="section">
		
	</div>
</body>



</html>