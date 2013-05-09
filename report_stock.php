<?php include "php/inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - "  ;?></title>


	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
  	
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
    	<script type="text/javascript" src="js/js_for_report_stock.min.js"></script>
    <?php }?>
	
   
	<script type="text/javascript">
	$(function(){

		//loading animation
		$('.loadSpinner').attr('src', "img/ajax-loader-<?=$default_theme;?>.gif"); 

		
		//load the stock value for given provider
		$('#tbl_stock_value tbody').xml2html('init',{
				url		: 'php/ctrl/Report.php',
				params	: 'oper=getStockValue', //would load all stock products
				loadOnInit: false,
				beforeLoad : function(){
					$('.loadSpinner').show();
				},
				complete : function(rowCount){
					$('.loadSpinner').hide();

					//calculate totals
					var tnetto = $.sumSimpleItems('.nettoCol');
					var tbrutto = $.sumSimpleItems('.bruttoCol');
					$('#nettoTotal').text(tnetto);
					$('#bruttoTotal').text(tbrutto);
					
					
				}
		});

		$('#tbl_stock_value tbody tr')
			.live('mouseenter', function(){
				$(this).addClass('ui-state-hover');
			})
			.live('mouseleave',function(){
					$(this).removeClass('ui-state-hover');
			})

		
		/**
		 * build Stock Provider SELECT
		 */
		$("#providerSelect").xml2html("init", {
			url: 'php/ctrl/ShopAndOrder.php',
			params : 'oper=getStockProviders',
			offSet : 1,
			loadOnInit:true,
			complete : function(){
				if (gStockProvider > 0){
					$("#providerSelect").val(gStockProvider);
					$("#providerSelect").trigger("change");
				}
			}
		}).change(function(){
				var provider_id = $("option:selected", this).val();			
				$('#tbl_stock_value tbody').xml2html('removeAll');	
						
				if (provider_id < 0) { return true;}
	
				$('.loadAnimShop').show();
				$('#tbl_stock_value tbody').xml2html("reload",{
					params	: 'oper=getStockValue&provider_id='+provider_id					
				});							
		}); //end select change

				
  			

			
	});  //close document ready
</script>

</head>
<body>
<div id="wrap">
	<div id="headwrap">
		<?php include "php/inc/menu.inc.php" ?>
	</div>
	<!-- end of headwrap -->
	
	
	
	<div id="stagewrap">
	
		
		<div id="titlewrap" class="ui-widget">
			<div id="titleLeftCol">
		    	<h1>Stock reports</h1>
		    </div>
		    <div id="titleRightCol">
		    	<select id="providerSelect" class="overviewElements">
                    	<option value="-1" selected="selected"><?php echo $Text['sel_provider']; ?></option>
                    	<option value="{id}"> {name}</option>                     
				</select>
		    </div>
		</div>
	
		<div class="ui-widget">    
        	<div class="ui-widget-content ui-corner-all">
	        	<h3 class="ui-widget-header ui-corner-all">&nbsp;<span style="float:right; margin-top:-4px;"><img class="loadSpinner" src="img/ajax-loader.gif"/></span></h3>    
				<table id="tbl_stock_value" class="tblListingDefault">
					<thead>
						<tr>
							<th>id</th>
							<th>product</th>
							<th>Current stock</th>
							<th>Shop unit</th>
							<th>Netto unit price</th>
							<th>Netto stock value</th>
							<th>IVa</th>
							<th>Rev</th>
							<th>Brutto stock value</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>{product_id}</td>
							<td>{name}</td>
							<td>{stock_actual}</td>
							<td>{shop_unit}</td>
							<td>{unit_price}</td>
							<td class="nettoCol">{total_netto_stock_value}</td>
							<td>{iva_percent}</td>
							<td>{rev_tax_percent}</td>
							<td class="bruttoCol">{total_brutto_stock_value}</td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
							<td>Total netto:</td>
							<td id="nettoTotal"></td>
							<td></td>
							<td>Total brutto:</td>
							<td id="bruttoTotal"></td>
						</tr>
					</tfoot>
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