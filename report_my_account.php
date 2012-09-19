<?php include "inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_account'] ;?></title>

	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
	<link rel="stylesheet" type="text/css"   media="print"  href="css/print.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/fgmenu/fg.menu.css"   />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/<?=$default_theme;?>/jqueryui.css"/>
     
  	<?php if (isset($_SESSION['dev']) && $_SESSION['dev'] == true ) { ?> 
	    <script type="text/javascript" src="js/jquery/jquery.js"></script>
		<script type="text/javascript" src="js/jqueryui/jqueryui.js"></script>
		<script type="text/javascript" src="js/fgmenu/fg.menu.js"></script>
		<script type="text/javascript" src="js/aixadautilities/jquery.aixadaMenu.js"></script>     	 
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaXML2HTML.js" ></script>
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaUtilities.js" ></script>
   	<?php  } else { ?>
	   	<script type="text/javascript" src="js/js_for_report_my_account.min.js"></script>
    <?php }?>
   
   
    	<script type="text/javascript" src="js/jqueryui/i18n/jquery.ui.datepicker-<?=$language;?>.js" ></script>
   
   
   
	<script type="text/javascript">
	
	$(function(){

			

			//decide what to do in which section
			//var report = $.getUrlVar('report');
/*				
   -1          Manteniment
   -2           Consum
   1..999      Uf cash sources (money that comes out of our pockets or goes in)
   1001..1999  regular UF accounts  (1000 + uf.id)
   2001..2999  regular provider account (2000 + provider.id)
*/				
							

			$("#datepicker").datepicker({
						dateFormat : 'yy-mm-dd',
						onSelect : function (dateText, instance){
							date = dateText; 
						
							
						}//end select
		
			}).show();//end date pick
			
			//retrieve date for upcoming order
			var date = 0; 
			

			/**
			 * 	account listing
			 */
			 $('#list_account tbody').xml2html('init',{
					url		: 'php/ctrl/Report.php',
			});
			
						
			
	});  //close document ready
</script>
</head>
<body>
<div id="wrap">
	<div id="headwrap">
		<?php include "inc/menu2.inc.php" ?>
	</div>
	<!-- end of headwrap -->
	
	
	<div id="stagewrap">
	
		<div id="titlewrap">
			<div id="titleLeftCol">
		    	<h1><?php echo $Text['ti_report_my_account']; ?></h1>
		    </div>
		    <div id="titleRightCol">
		    	<p class="textAlignRight">Select date: <input  type="text" class="datePickerInput" id="datepicker"></p>
		    </div>
		</div>
		
		<div id="account_listing" class="ui-widget">
			<div class="ui-widget-content ui-corner-all">
					
					<h3 class="ui-widget-header ui-corner-all">Latest movements</h3>
					<table id="list_account" class="table_listing">
					<thead>
						<tr>
							<th>Date</th>
							<th>Operator</th>
							<th>Description</th>
							<th>Account</th>
							<th>Amount</th>
							<th>Balance</th>
						</tr>
					</thead>
					<tbody>
						<tr class="xml2html_tpl">
							<td>{ts}</td>
							<td>{operator}</td>
							<td>{description}</td>
							<td>{account}</td>
							<td>{quantity}</td>
							<td>{balance}</td>
						</tr>
					</tbody>
					</table>
					
					
			</div>	
		</div>

		
	</div>
	<!-- end of stage wrap -->
</div>
<!-- end of wrap -->

<!-- / END -->
</body>
</html>