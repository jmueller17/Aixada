<?php include "php/inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_sales'] . " - " . $Text['nav_report_shop_pv'] ;?></title>

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
	   	<script type="text/javascript" src="js/tablesorter/jquery.tablesorter.js" ></script>
   	<?php  } else { ?>
	   	<script type="text/javascript" src="js/js_for_report_shop_providers.min.js"></script>
    <?php }?>
   		
 	<script type="text/javascript" src="js/jqueryui/i18n/jquery.ui.datepicker-<?=$language;?>.js" ></script>
 	   
   
	<script type="text/javascript">
	$(function(){

		var gUniqueProviders = [];

		var gProviderId = 0; 

		
		//loading animation
		$('.loadSpinner').attr('src', "img/ajax-loader-<?=$default_theme;?>.gif").hide(); 
		

		$("#datepicker_from").datepicker({
			dateFormat 	: 'DD, d MM, yy',
			onSelect : function (dateText, instance){
				$('#setFromDate').text(dateText);
			}
		});

		$("#datepicker_to").datepicker({
			dateFormat 	: 'DD, d MM, yy',
			onSelect : function (dateText, instance){
				$('#setToDate').text(dateText);
			}
		});
		

		/********************************************************
		 *    PURCHASE LISTING
		 ********************************************************/
		
		//the table sorter plugin
		//$("#tbl_Providers").tablesorter();
		$("#tbl_Providers").bind('sortEnd', function(){
			$('tr',this).removeClass('rowHighlight')
			$('tr:even',this).addClass('rowHighlight');
		});
		
		//load purchase listing
		$('#tbl_Providers tbody').xml2html('init',{
				url : 'php/ctrl/Shop.php',
				params : 'oper=getTotalSalesByProviders&filter=prevMonth&provider_id=0', 
				loadOnInit : true, 
				beforeLoad : function(){
					$('.loadSpinner').show();
				},
				rowComplete : function(rowIndex, row){
					var pvid = $(row).children().eq(0).text();
					var date = $(row).children().eq(2).text();
					var total = new Number($(row).children().eq(3).text());

					
					$(row).children().eq(2).text($.getCustomDate(date, 'd MM, yy'));
					$(row).children().eq(3).text(total.toFixed(2));

					gUniqueProviders.push(pvid);
					
				},
				complete : function(rowCount){
					//$('tr:even', this).addClass('rowHighlight');
					//$("#tbl_Providers").trigger("update"); 
					
					
					$('.loadSpinner').hide();

					var unique=gUniqueProviders.filter(function(itm,i,a){
					    return i==gUniqueProviders.indexOf(itm);
					});
					
				
					for (var i=0; i<unique.length; i++){
						var sum_provider = 0;
						$('.total_'+unique[i]).each(function(){
							sum_provider += new Number($(this).text());
						});

						var nrows = $('.pvid_'+unique[i]).length; 
						
						$('.pvid_'+unique[i]).first().append('<td rowspan="'+nrows+'" id="sumCell_'+unique[i]+'" ><p class="textAlignRight boldStuff">'+sum_provider.toFixed(2)+'</p></td>');
						

					}

					if (rowCount == 0){
						$.showMsg({
							msg:"<?=$Text['msg_err_order_filter'];?>",
							type: 'warning'});
					}
					
				}
		});

		
		$('#tbl_Providers tbody tr')
			.live('mouseover', function(){
				$(this).removeClass('highlight').addClass('ui-state-hover');
				var pvid = $(this).attr('providerId');
				$('#sumCell_'+pvid).addClass('ui-state-hover');
				
			})
			.live('mouseout',function(){
				$(this).removeClass('ui-state-hover');
				var pvid = $(this).attr('providerId');
				$('#sumCell_'+pvid).removeClass('ui-state-hover');
				
			})
			.live('click',function(e){
	
				
		});


		$("#tblViewOptions")
		.button({
			icons: {
	        	secondary: "ui-icon-triangle-1-s"
			}
	    })
	    .menu({
			content: $('#tblOptionsItems').html(),	
			showSpeed: 50, 
			width:280,
			flyOut: true, 
			itemSelected: function(item){					//TODO instead of using this callback function make your own menu; if jquerui is updated, this will  not work
				//show hide deactivated products
				var filter = $(item).attr('id');

				if (filter == 'provider'){
					$("#providerSelect").val(-1).attr('selected', true);
					//open dialog
					$('#dialog_providerSelect').dialog("open");
				} else if (filter == 'exact'){
					//open datepicker dialog
					$('#dialog_exactDate').dialog("open");
				} else {
					$('#tbl_Providers tbody').xml2html('reload',{
						params : 'oper=getTotalSalesByProviders&filter='+filter+'&provider_id='+gProviderId, 
					});
				}
				
			}//end item selected 
		});//end menu


		

		
		
		$('#dialog_exactDate').dialog({
			autoOpen:false,
			width:680,
			height:500,
			buttons: {  
				"<?=$Text['btn_ok'];?>" : function(){

					$('#tbl_Providers tbody').xml2html('reload',{
						params : 'oper=getTotalSalesByProviders&filter=exact&provider_id='+gProviderId+'&from_date='+$.getSelectedDate('#datepicker_from')+'&to_date='+$.getSelectedDate('#datepicker_to'), 
					});
					$( this ).dialog( "close" );
					
				},
			
				"<?=$Text['btn_close'];?>"	: function(){
					$( this ).dialog( "close" );
					} 
			}
		});

		$('#dialog_providerSelect').dialog({
			autoOpen:false,
			width:400,
			height:200,
			buttons: {  
			
				"<?=$Text['btn_close'];?>"	: function(){
					$( this ).dialog( "close" );
					} 
			}
		});


		$("#providerSelect").xml2html("init", {
			url : 'php/ctrl/ShopAndOrder.php',
			params : 'oper=getShopProviders',
			loadOnInit: true,
			offSet : 2
		}).change(function(){
			gProviderId = $("option:selected", this).val();	
			
			if (gProviderId == 0){
				var filter = 'prevMonth';
			} else {
				var filter = 'all'; 
			}
			$('#dialog_providerSelect').dialog("close");
			$('#tbl_Providers tbody').xml2html('reload',{
				params : 'oper=getTotalSalesByProviders&filter='+filter+'&provider_id='+gProviderId, 
			});	
		}); //end select change
		

		
		/********************************************************
		 *    DETAIL PURCHASE VIEW
		 ********************************************************/	
		
	
			
		function switchTo(section){
			switch (section){
				case 'detail':
					$('.overviewElements').hide();
					$('.detailElements').fadeIn(1000);
					break;

				case 'overview':
					$('.detailElements').hide();
					$('.overviewElements').fadeIn(1000);
					break;
				}

		}


			
		
			
	});  //close document ready
</script>

</head>
<?php flush(); ?>
<body>
<div id="wrap">
	<div id="headwrap">
		<?php include "php/inc/menu.inc.php" ?>
	</div>
	<!-- end of headwrap -->
	
	
	
	<div id="stagewrap">
	
		
		<div id="titlewrap" class="ui-widget">
			<div id="titleLeftCol">
				<!-- button id="btn_overview" class="floatLeft detailElements"><?php echo $Text['overview'];?></button-->
		    	<h1 class="overviewElements"><?php echo $Text['ti_report_shop_pv']; ?></h1>
		    </div>
		    <div id="titleRightCol">
		    <button	id="tblViewOptions" class="overviewElements btn_right"><?=$Text['btn_filter']; ?></button>
				
				<div id="tblOptionsItems" class="hidden">
					<ul>
						<li><a href="javascript:void(null)" id="today"><?=$Text['filter_todays'];?></a></li>
						<li><a href="javascript:void(null)" id="prevMonth"><?=$Text['filter_month'] ; ?></a></li>
						<li><a href="javascript:void(null)" id="all"><?=$Text['filter_all_sales']; ?></a></li>
						<li><a href="javascript:void(null)" id="exact"><?=$Text['filter_exact'];?></a></li>
						<li><a href="javascript:void(null)" id="provider"><?=$Text['by_provider']  ; ?></a></li>
					</ul>
				</div>	
		    	
		    </div>
		</div>
	

	
				
        <div id="purchase_list" class="ui-widget overviewElements">    
        	<div class="ui-widget-content ui-corner-all">
	        	<h3 class="ui-widget-header ui-corner-all">&nbsp;<span style="float:right; margin-top:-4px;"><img class="loadSpinner" src="img/ajax-loader.gif"/></span></h3>    
				<table id="tbl_Providers" class="tblListingDefault">
					<thead>
						<tr>
							<th class="textAlignCenter"><?php echo $Text['provider']; ?></th>
							<th class="textAlignCenter"><?php echo $Text['purchase_date']; ?> </th>
							<th><p class="textAlignRight"><?php echo $Text['total_4date']; ?></p> </th>
							<th><p class="textAlignRight"><?php echo $Text['total_4provider']; ?></p></th>
						</tr>
					</thead>
					<tbody>
						<tr class="pvid_{provider_id}" providerId="{provider_id}">
							<td class="hidden">{provider_id}</td>
							<td>{provider_name}</td>
							<td>{date_for_shop}</td>
							<td class="total_{provider_id} floatRight">{total}</td>
							
						</tr>
					</tbody>
					<tfoot>
					</tfoot>
				</table>
			</div>
		</div>	
		
		
	
			
		
		
	</div>
	<!-- end of stage wrap -->
</div>
<!-- end of wrap -->
<!-- / END -->

<div id="dialog_exactDate" title="<?php echo $Text['sel_sales_dates_ti']; ?>">
	<p>&nbsp;</p>
	<p><?php echo $Text['sel_sales_dates']; ?></p>
	<br/>
	<table>
		<tr>
			<td><?php echo $Text['date_from']; ?>:</td>
			<td><input type="text" id="datepicker_from" class="ui-corner-all"/></td>
			<td>&nbsp;&nbsp;</td>
			<td><?php echo $Text['date_to']; ?>:</p></td>
			<td><input type="text" id="datepicker_to"/></td>
		</tr>
	</table>

</div>


<div id="dialog_providerSelect" title="<?php echo $Text['by_provider'];?>">
	<p>&nbsp;</p>
	<select id="providerSelect" class="longSelect">
    	<option value="-1" selected="selected"><?php echo $Text['sel_provider']; ?></option>
    	<option value="0"><?php echo $Text['filter_all'];  ?></option>
        <option value="{id}"> {name}</option>                     
	</select>
</div>


</body>
</html>