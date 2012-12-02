<?php include "../php/inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Aixada - Bill - Member - info</title>


	<style type="text/css">
		body 				{font-family:arial; }
		table 				{width:100%; border-collapse:collapse;}
		thead				{background:#efefef;}
		th	 				{border:solid 1px black; padding:2px 5px; background:#efefef;}
		td.headc	 		{border:solid 1px black; padding:2px 5px; background:#efefef; text-align:center;}
		td 					{padding:3px;}
		
		.section 			{width:90%; clear:both; margin-bottom:10px;}
		.txtAlignRight		{text-align:right;}
		.txtAlignCenter		{text-align:center;}
		.tdAlignTop			{vertical-align:top;}
		
		
		.cellBorderList td	{border:solid 1px black; padding:2px 5px;}
		
		
		
		div#logo			{width:500px; height:180px; float:left; border:1px solid black; margin-bottom:20px;}
		div#address			{}
		div#bill_info		{width:48%; margin-right:10px; float:left;}
		div#member_info		{width:48%; float:right; margin-bottom:10px;}
		
		
		
	</style>
	
	<script type="text/javascript" src="../js/jquery/jquery.js"></script>
	<script type="text/javascript" src="../js/jqueryui/jqueryui.js"></script>

   	<script type="text/javascript" src="../js/aixadautilities/jquery.aixadaXML2HTML.js" ></script>
	<script type="text/javascript" src="../js/aixadautilities/jquery.aixadaUtilities.js" ></script>
 

	<script type="text/javascript">
		$(function(){

			//prevent error msg when opening saved page
			//if (window.opener == null) return false;

			var incidentsIdList = $.getUrlVar('idlist');
			


			/**
			 *	incidents
			 */
			$('#tbl_incidents tbody').xml2html('init',{
					url: '../php/ctrl/Incidents.php',
					params : 'oper=getIncidentsById&idlist='+incidentsIdList,
					loadOnInit: true,
					complete : function(count){
						
					}
			});


	
			

		}); //close document ready
	</script>
	
</head>
<body>
	
	<div id="header" class="section">
		<div id="logo">
			<img alt="coop logo" src="../img/tpl_header_logo.png" width="500" height="180"/>
		</div>
		<div id="address">
			<h2 class="txtAlignRight">COOPERATIVA NAME</h2>
			<h2 class="txtAlignRight">CIF/NIF: F650000</h2>
			<p class="txtAlignRight">Street<br/>
			Zip City<br/>
			email@bla.com
			</p>
		</div>
	</div>

	
				<table id="tbl_incidents" class="ui-widget">
					<thead>
						<tr>
							<th><?php echo $Text['id'];?></th>
							<th><?php echo $Text['priority'];?></th>
							<th><?php echo $Text['created_by'];?></th>
							<th><?php echo $Text['created'];?></th>
							<th><?php echo $Text['status'];?></th>
							<th><?php echo $Text['provider_name'];?></th>
							<th><?php echo $Text['ufs_concerned'];?></th>
							<th><?php echo $Text['comi_concerned'];?></th>
							
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="headc">{id}</td>
							<td class="headc">{priority}</td>
							<td class="headc">{uf_id} {user_name}</td>
							<td class="headc">{ts}</td>
							<td class="headc">{status}</td>
							<td class="headc">{provider_name}</td>
							<td class="headc">{ufs_concerned}</td>
							<td class="headc">{commission_concerned}</td>
						</tr>

						<tr>
							<td></td>
							<td><?php echo $Text['subject'];?>:</td>
							<td colspan="10" class="noBorder">{subject}</td>
						</tr>
						<tr>
							<td class="noBorder"></td>
							<td class="tdAlignTop"><?php echo $Text['message'];?>:</td>
							<td class="tdAlignTop" colspan="10">{details}</td>
							
						</tr>
						<tr><td colspan="12" class="noBorder"><br/></td></tr>
					</tbody>
					</table>
	
	
</body>
</html>