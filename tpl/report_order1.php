<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Aixada - Report - Order</title>


	<style type="text/css">
		body 				{font-size:0.8em; font-family:arial, helvetica;}
		table 				{width:100%; margin-bottom:10px;}
		td					{border:solid 1px black; border-collapse:collapse; padding:2px 2px;}
		th					{border:solid 1px black; background:#efefef;}
		p 					{margin:0px;}
		th h2				{margin:4px;}
		
		.section 			{width:90%; clear:both; margin-bottom:10px;}
		.txtAlignRight		{text-align:right;}
		.txtAlignCenter		{text-align:center;}
		.tdAlignTop			{vertical-align:top;}
		.bold				{font-weight:bold;}


		.b4					{border:2px solid black;}	 
		.p4-5				{padding:5px;}
		.hidden				{display:none;}
		

		
		
		div#logo			{width:500px; height:180px; float:left; border:1px solid black; margin-bottom:20px;}
		div#address			{}

		.loadingMsg			{font-weight:bold; font-size:2em; text-align:center; display:block;}
		

		.clickPageBreak 	{display:block; margin-bottom:50px;}
		.pageBreakBtn		{padding:5px; border:solid 1px black; background-color:#f9f9f9; cursor:pointer;}
		.pageBreak 			{display:block;border-bottom:dashed 1px gray;}
	
		@media print {
	  		.pageBreak  	{display:block; page-break-before:always; border-bottom:dashed 1px gray; margin-bottom:50px;}
	  		.pageBreakBtn 	{display:none;}
	  		.loadingMsg		{display:none;}
		}

		
	</style>
	
	
	<script type="text/javascript" src="../js/jquery/jquery.js"></script>
	<script type="text/javascript" src="../js/jqueryui/jqueryui.js"></script>
   	<script type="text/javascript" src="../js/aixadautilities/jquery.aixadaXML2HTML.js" ></script>
	<script type="text/javascript" src="../js/aixadautilities/jquery.aixadaUtilities.js" ></script>
 

	<script type="text/javascript">

		
	
		$(function(){


			//for the moment being does nothing...
			window.opener.$('input:checkbox[name="bulkAction"]').each(function(){
				if ($(this).is(':checked')){
					
				} 
			});


			//add remove page breaks for printing
			$('.clickPageBreak')
				.live('click', function(e){
	
					if ($(this).hasClass('pageBreak')){
						$(this).removeClass('pageBreak');
						$(this).find('span').text('Click to ADD here a page break while printing');
					} else {
						$(this).addClass('pageBreak');
						$(this).find('span').text('Click to REMOVE this page break');
					}
				})
			

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

	<p>&nbsp</p>
	<p>&nbsp</p>
	<p>&nbsp</p>
	<p>&nbsp</p>
	
	<h2 class="loadingMsg">Please wait while loading...</h2>
	
	<div id="orderWrap" class="section">
		
		<div class="anOrder">
			<p class="clickPageBreak txtAlignCenter"><span class="pageBreakBtn">Click to ADD here a page break while printing</span></p>
		</div>
	</div>
</body>



</html>