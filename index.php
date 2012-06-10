<?php include "inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title']; ?></title>

 	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
  	<link rel="stylesheet" type="text/css"   media="print"  href="css/print.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/aixadacart/aixadacart.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/fgmenu/fg.menu.css"   />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/<?=$default_theme;?>/jqueryui.css"/>
     
    
    <?php if (isset($_SESSION['dev']) && $_SESSION['dev'] == true ) { ?> 
	    <script type="text/javascript" src="js/jquery/jquery.js"></script>
		<script type="text/javascript" src="js/jqueryui/jqueryui.js"></script>
		<script type="text/javascript" src="js/fgmenu/fg.menu.js"></script>
		<script type="text/javascript" src="js/aixadautilities/jquery.aixadaMenu.js"></script>     	 
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaXML2HTML.js" ></script>
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaUtilities.js" ></script>
	   	<script type="text/javascript" src="js/aixadacart/jquery.aixadacart.js" ></script>   	    
   	<?php  } else { ?>
	   	<script type="text/javascript" src="js/js_for_index.min.js"></script>
    <?php }?>
     
    <script type="text/javascript" src="js/aixadacart/i18n/cart.locale-<?=$language;?>.js" ></script>
	   
	<script type="text/javascript">
	$(function(){

			$('#loadingMsg').hide();
	

			$('#tbl_FutureOrders tbody').xml2html('init',{
				url : 'ctrlReport.php',
				params : 'oper=getFutureShopTimes', 
				loadOnInit : false
			});

		
			//show purchases, validate and non validated
			$('#tbl_PastValidated tbody').xml2html('init',{
				url : 'ctrlReport.php',
				params : 'oper=getAllShopTimes', 
				loadOnInit : false, 
				rowComplete : function(rowIndex, row){	
					var last_td = $(row).children().last(); //change the text displayed when purchse has not been validated 
					if (last_td.text() == '0000-00-00 00:00:00') {
						last_td.text('<?php echo $Text["not_validated"];?>');
						last_td.prev().html('<span class="ui-icon ui-icon-minus"></span>');
					}
				},
			});

			
			//load the purchase details and show in dialog box
			$('.shopId').live('click',function(){	
				$('#loadingMsg').show();
				
				$('#cartLayer').aixadacart('resetCart');
				var shop_id = $(this).parent().prev().html();
				
				//reload the list
				$('#cartLayer').aixadacart('loadCart',{
					loadCartURL : 'ctrlReport.php?oper=getShoppedItems&shop_id='+shop_id
				}); //end loadCart

				$( "#dialog-message" )
					.dialog({ title: shop_id })
					.dialog("open");
			});	
			
			//init cart 
			$('#cartLayer').aixadacart("init",{
				saveCartURL : 'ctrlValidate.php',
				cartType	: 'simple',
				btnType		: 'hidden',
				loadSuccess : function(){
					$('input').attr('disabled','disabled');
					$('#loadingMsg').hide();
					$('.ui-icon-close').hide();
				}
			});

			$( "#dialog-message" ).dialog({
				modal: true,
				width:600,
				height:480,
				autoOpen:false,
				buttons: {
					Ok: function() {
						$( this ).dialog( "close" );
					}
				}
			});
				
			
	});  //close document ready
</script>


</head>
<?php flush(); ?>
<body>
<div id="wrap">
	<div id="headwrap">
		<?php include "inc/menu2.inc.php" ?>
	</div>
	<!-- end of headwrap -->
	<div id="stagewrap">
		<div id="homeWrap">
			<div id="leftIconCol">
				<div class="homeIcon">
					<a href="shop_and_order.php?what=Shop"><img src="img/cesta.png"/></a>
					<p><a href="shop_and_order.php?what=Shop"><?php echo $Text['icon_purchase'];?></a></p>
				</div>
				<div class="homeIcon">
					<a href="shop_and_order.php?what=Order"><img src="img/pedido.png"/></a>
					<p><a href="shop_and_order.php?what=Order"><?php echo $Text['icon_order'];?></a></p>
				</div>
				<div class="homeIcon">
					<a href="incidents.php"><img src="img/incidencias.png"/></a>
					<p><a href="incidents.php"><?php echo $Text['icon_incidents'];?></a></p>
				</div>
			</div>
			<div id="rightSummaryCol">
				<div id="FutureOrders" class="ui-widget ui-widget-content ui-corner-all">
					<h2 class="ui-widget-header ui-corner-all"><?php echo $Text['purchase_future'];?></h2>
					<div><br/></div>
					<table id="tbl_FutureOrders" class="purchase_listing">
						<thead>
							<tr>
								<th>id</th>
								<th><?php echo $Text['ordered_for'];?></th>
								<th><?php echo $Text['status'];?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>{id}</td>
								<td><a href="javascript:void(null)" class="shopId">{date_for_shop}</a></td>
								<td class="floatRight">open/closed</td>
							</tr>
						</tbody>
					</table>
				</div><br/>
				<div id="PastValidated" class="ui-widget ui-widget-content ui-corner-all">
					<h2 class="ui-widget-header ui-corner-all"><?php echo $Text['purchase_current'];?></h2>
					<div><br/></div>
					<table id="tbl_PastValidated" class="purchase_listing">
						<thead>
							<tr>
								<th>id</th>
								<th><?php echo $Text['purchase_date'];?></th>
								<th><?php echo $Text['status'];?></th>
								<th><?php echo $Text['purchase_validated'];?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>{id}</td>
								<td><a href="javascript:void(null)" class="shopId">{date_for_shop}</a></td>
								<td class="floatRight"><span class="ui-icon ui-icon-check"></span></td>
								<td>{validated}</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>			
		</div>
		
	
	
		
	</div>
	<!-- end of stage wrap -->
</div>
<!-- end of wrap -->
<div id="dialog-message" title="">
		 <p id="loadingMsg" class="ui-state-highlight"><?php echo $Text['loading'];?></p>
		 <div id="cartLayer"></div>
</div>

<!-- / END -->
</body>
</html>