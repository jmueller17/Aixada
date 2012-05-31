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


		//var fromDate = "Yesterday";
		//var toDate = "30 days";
		var gdates = [];
		var seekDateSteps = 20;
		var provider_id = 0; 
		
		/**
		 * retrieve dates for a given time period and constructs the table header
		 * according to the date format specified
		 */
		function makeDateHeader(fromDate, toDate){

			//the format in which the date is shown in the header
			var outDateFormat = 'D d, M';
			 
			$.ajax({
				type: "POST",
				url: "ctrlDates.php?oper=getDateRangeAsArray&fromDate="+fromDate+"&toDate="+toDate,	
				beforeSend : function (){
					
				},	
				success: function(txt){

					gdates = eval(txt);
					
					apstr = '';
					for (var i=0; i<gdates.length; i++){
						var dt = $.datepicker.parseDate('yy-mm-dd', gdates[i]);
						var date = $.datepicker.formatDate(outDateFormat, dt);
						apstr += '<th class="dateth">'+date+'</th>';
						
					}

					//remove previous dates and table cells if any
					$('#dot thead tr .dateth').empty().remove();
					$('#dot tbody tr .interactiveCell').empty().remove();

					//append the new header with the fresh dates
					$('#dot thead tr').last().append(apstr);

					//load the products 
					$('#dot tbody').xml2html('reload');
		
				},
				error : function(XMLHttpRequest, textStatus, errorThrown){
					
				},
				complete : function(msg){
					
				}
			}); //end ajax						

		}

		
		/**
		 *	generate the tables cells 
		 */

		$('#dot tbody').xml2html({
				url:'ctrlShopAndOrder.php',
				params:'oper=listProducts&provider_id='+provider_id+'&what=Shop&date=2012-05-01',
				loadOnInit:false,
				rowComplete : function(rowIndex, row){	//construct table cells with product id and date
					var id =  $(row).attr("id"); 

					apstr = '';
					for (var i=0; i<gdates.length; i++){
						var tdid = gdates[i]+"_"+id; 
						apstr += '<td title="'+gdates[i]+'" id="'+tdid+'" class="interactiveCell deactive"></td>';
					}
					$(row).append(apstr);
					
				},
				//finally retrieve if products are orderable for given dates
				complete: function(rowCount){
					$.ajax({
						type: "POST",
						dataType:"xml",
						url: "ctrlActivateProducts.php?oper=getOrderableProducts4DateRange&fromDate="+gdates[0]+"&toDate="+gdates[gdates.length-1]+"&provider_id="+provider_id,	
						success: function(xml){

							$(xml).find('row').each(function(){

								var id = $(this).find('id').text();
								var date = $(this).find('date').text()
								//var selector = ".Date-"+date + ".Prod-"+id;
								var selector = "#"+date+"_"+id;

								toggleCell(selector); 
																
							});
										},
						error : function(XMLHttpRequest, textStatus, errorThrown){
							
						},
						complete : function(msg){
							
						}
					}); //end ajax		
					
					
				}
			});


		

		/**
		 *	activate / deactive a given product for a given date
		 */
		function toggleCell(id){
			if ($(id).hasClass('active')){
				$(id)
				.removeClass('active')
				.addClass('deactive')
				.empty();
			} else {
				$(id)
				.removeClass('deactive')
				.addClass('active')
				.append('<span class="ui-icon ui-icon-check" style="margin:auto"></span>');
			}
		}
		
		/**
		 *	event handler for each table cell
		 */
		$('td.interactiveCell')
			.live('click', function(e){

				var tdid = $(this).attr('id');
				var dateID = tdid.split("_");
				var urlStr = "ctrlActivateProducts.php?oper=toggleOrderableProduct&product_id="+dateID[1]+"&date="+dateID[0]; 

				
				$.ajax({
					type: "POST",
					//dataType:"xml",
					url: urlStr,	
					beforeSend : function (){
						//$('#deposit .loadAnim').show();
					},	
					success: function(txt){
						
						toggleCell('#'+tdid); 
			
					},
					error : function(XMLHttpRequest, textStatus, errorThrown){
						$.showMsg({
							msg:XMLHttpRequest.responseText,
							type: 'error'});
						
					},
					complete : function(msg){
						
					}
				}); //end ajax	
				
					
			});
			
	
	
	
		

		makeDateHeader("Yesterday", "20 days");


		$("#prevDates").button({
            icons: {
                primary: "ui-icon-seek-prev"
            },
            text: false
        }).click(function(e){

        	var a = new Date(gdates[0]);
   			a.setDate(a.getDate() - seekDateSteps);
   			
   			var date = $.datepicker.formatDate('yy-mm-dd',a);
         	makeDateHeader(date,gdates[0]);
			
        })

        $("#nextDates").button({
            icons: {
                primary: "ui-icon-seek-next"
            },
            text: false
        }).click(function(e){
           
            var a = new Date(gdates[gdates.length-1]);
  			a.setDate(a.getDate() + seekDateSteps);
  			
  			var date = $.datepicker.formatDate('yy-mm-dd',a);
        	makeDateHeader(gdates[gdates.length-1], date);
        });



		$("#providerSelect").xml2html("init", {
			loadOnInit  : true,
			offSet		: 1,
			url         : 'ctrlActivateProducts.php',				
			params 		: 'oper=listProviders'
		}).change(function(){
			//get the id of the provider
			provider_id = $("option:selected", this).val(); 
			
			if (provider_id < 0) return true; 
			//load the products 
			$('#dot tbody').xml2html('reload',{
				params:'oper=listProducts&provider_id='+provider_id+'&what=Shop&date=2012-05-01'
			});

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
	
	
	<div id="stagewrap" class="ui-widget">
	
		<div id="titlewrap">
			
		    	<h1><?php echo $Text['ti_mng_activate_products'];  ?>
		    	
		</div>
		
		<div class="wrapSelect">
				<select id="providerSelect" class="longSelect">
				        <option value="-1" selected="selected"><?php echo $Text['sel_provider']; ?></option>                    
                    	<option value="{id}">{id} {name}</option>
				</select>
		</div>
		
		<div id="productDateOverview">
			
			<table id="dot" class="table_datesOrderableProducts">
						<thead>
						<tr>
							<td colspan="3"><button id="prevDates" class="floatRight">&nbsp;</button></td>
							<td colspan="100"><button id="nextDates" class="floatRight">&nbsp;</button></td>
						</tr>
						<tr>
							<th><?php echo $Text['id'];?></th>
							<th><?php echo $Text['provider_name'];?></th>
							<th><?php echo $Text['name_item'];?></th>
						</tr>
						</thead>
						<tbody>
							<tr id="{id}">
								<td class="item_it prodActive">{id}</td>
								<td class="item_provider_name">{provider_name}</td>
								<td class="item_name">{name}</td>
											
							</tr>						
						</tbody>
					</table>
		
		
		</div>
		
		
	</div><!-- end of stage wrap -->
</div>
<div id="mylog">

</div>
<!-- end of wrap -->

<!-- / END -->
</body>
</html>