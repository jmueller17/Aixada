<?php include "inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_active_products']; ?></title>
	
	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/fgmenu/fg.menu.css"   />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/<?=$default_theme;?>/jqueryui.css"/>


    <script type="text/javascript" src="js/jquery/jquery.js"></script>
	    
	    
	<script type="text/javascript" src="js/jqueryui/jqueryui.js"></script>
	<script type="text/javascript" src="js/fgmenu/fg.menu.js"></script>
	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaMenu.js"></script>     	 

   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaXML2HTML.js" ></script>
   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaUtilities.js" ></script>
	   
   	
 	<script type="text/javascript" src="js/jqueryui/i18n/jquery.ui.datepicker-<?=$language;?>.js" ></script>   
    
   
	<script type="text/javascript">
	$(function(){


		$('#dot tbody').xml2html({
				url:'ctrlShopAndOrder.php?oper=listProducts&provider_id=52&what=Shop&date=2012-05-01',
				loadOnInit:false,
				rowComplete : function(rowIndex, row){	//updates quantities for items already in cart
					var id =  $(row).attr("id"); 
					//var qu = $("#cart_quantity_"+id).val();
					//$("#quantity_"+id).val(qu);
					apstr = '';
					for (var i=0; i<dates.length; i++){
					
							apstr += '<td title="r'+id+':d'+i+'">x</td>';
						
					}
					$(row).append(apstr);
					
				},
			});

			var dates = [];
		
		$.ajax({
			type: "POST",
			//dataType:"xml",
			url: "ctrlDates.php?oper=getDateRangeAsArray&fromDate=2012-05-01&toDate=2012-05-30",	
			beforeSend : function (){
				//$('#deposit .loadAnim').show();
			},	
			success: function(txt){

				dates = eval(txt);

				apstr = '';
				for (var i=0; i<dates.length; i++){
					apstr += '<th>'+dates[i]+'</th>';
					
				}
				$('#dot thead tr').append(apstr);

				$('#dot tbody').xml2html('reload');
				
				//alert(txt.length);
				/*
				var apstr = '<tr>';
				$(xml).find('date').each(function(){

					apstr += '<th>'+$(this).text()+'</th>'; 

				});
				apstr += '</tr>';
				*/
				
				//$('#dot thead').append(apstr);				

			},
			error : function(XMLHttpRequest, textStatus, errorThrown){
				
			},
			complete : function(msg){
				
			}
		}); //end ajax						
		
			
	});  //close document ready
</script>
</head>
<body>
<div id="wrap">
	<div id="headwrap">
		<?php include "inc/menu2.inc.php" ?>
	</div>
	<!-- end of headwrap -->
	
	
	<div id="stagewrap" class="ui-widget">
	
		<div id="titlewrap">
			
		    	<h1><?php echo $Text['ti_mng_activate_products'];  ?>
		    	
		</div>
		
		<div id="productDateOverview">
		
			<table id="dot" class="product_list" >
						<thead>
						<tr>
							<th><?php echo $Text['id'];?></th>
							<th><?php echo $Text['name_item'];?></th>
							<th><?php echo $Text['provider_name'];?></th>
						</tr>
						</thead>
						<tbody>
							<tr id="{id}">
								<td class="item_it">{id}</td>
								<td class="item_provider_name">{provider_name}</td>
								<td class="item_name">{name}</td>
								
											
							</tr>						
						</tbody>
					</table>
		
		
		</div>
		
		
	</div><!-- end of stage wrap -->
</div>
<!-- end of wrap -->

<!-- / END -->
</body>
</html>