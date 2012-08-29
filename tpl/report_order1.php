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

			
		 var tbl = $(opener.getTable()).clone();

		 $(tbl).addClass('cellBorderList');
		 $('.revisedCol, .arrivedCol', tbl).hide();
		 		 
		 $(stage).after(tbl);
					

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

	<div id="stage" class="section">
		
	</div>
</body>



</html>