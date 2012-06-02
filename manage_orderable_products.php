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
		
		/**
		 * retrieve dates for a given time period and constructs the table header
		 * according to the date format specified
		 */
		function makeDateHeader(fromDate, toDate){

			//the format in which the date is shown in the header
			var outDateFormat = 'D d, M';
			var provider_id = getProviderId();
			 
			$.ajax({
				type: "POST",
				url: "ctrlDates.php?oper=getDateRangeAsArray&fromDate="+fromDate+"&toDate="+toDate,	
				beforeSend : function (){
					
				},	
				success: function(txt){

					gdates = eval(txt);
					
					var apstr = '';
					var tfoot = '';
					for (var i=0; i<gdates.length; i++){
						var dt = $.datepicker.parseDate('yy-mm-dd', gdates[i]);
						var date = $.datepicker.formatDate(outDateFormat, dt);
						apstr += '<th class="dateth">'+date+'</th>';
						tfoot += '<td class="tfootDateGenerate" colDate="'+gdates[i]+'"></td>';	
					}
					//add table footer
					
				
					

					//remove previous dates and table cells if any
					$('#dot thead tr .dateth').empty().remove();
					$('#dot tbody tr .interactiveCell').empty().remove();
					$('#dot tfoot tr .tfootDateGenerate').empty().remove();
					

					//append the new header/footer with the fresh dates
					$('#dot thead tr').last().append(apstr);
					$('#dot tfoot tr').last().append(tfoot);

					//load the products 
					if (provider_id){
						$('#dot tbody').xml2html('reload');
					}	
				},
				error : function(XMLHttpRequest, textStatus, errorThrown){
					$.showMsg({
						msg:XMLHttpRequest.responseText,
						type: 'error'});
				}
			}); //end ajax						

		}

		
		/**
		 *	generate the tables cells 
		 */
		$('#dot tbody').xml2html({
				url:'ctrlActivateProducts.php',
				loadOnInit:false,
				rowComplete : function(rowIndex, row){		//construct table cells with product id and date
					var id =  $(row).attr("id"); 			//get the product id
					var ckbox = $("input:checkbox", row); 	//if the product is active or not
					var cellStyle = (ckbox.attr("isActive") == 1)? "notOrderable":"deactivated";
					var apstr = '';
					
					for (var i=0; i<gdates.length; i++){
						var dateidclass = "Date-"+gdates[i] + " P-"+id;
						var tdid		= gdates[i]+"_"+id; 
						apstr += '<td title="'+gdates[i]+'" id="'+tdid+'" class="'+dateidclass+' interactiveCell '+cellStyle+'"></td>';
					}
					$(row).append(apstr);

					//revise checkboxe attribute
					if (ckbox.attr("isActive") == 1){
						ckbox.attr("checked", "checked")
					} else {
						ckbox.removeAttr("checked");
					}

					//hide deactivated products
					if ($('#showInactiveProducts').attr('checked') != 'checked' && ckbox.attr("isActive") != 1){
						$(row).hide();
					}
					
					
				},
				//finally retrieve if products are orderable for given dates
				complete: function(rowCount){
					var provider_id = getProviderId();
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
							$.showMsg({
								msg:XMLHttpRequest.responseText,
								type: 'error'});
						}
					}); //end ajax		
					
					
				}
			});


		/**
		 *	activate / deactive a given product for a given date
		 */
		function toggleCell(id){
			if ($(id).hasClass('isOrderable')){
				$(id)
				.removeClass('isOrderable')
				.addClass('notOrderable')
				.empty();
			} else {
				$(id)
				.removeClass('notOrderable deactivated')
				.addClass('isOrderable')
				.append('<span class="ui-icon ui-icon-check tdIconCenter"></span>');
			}
		}

		
		/**
		 *	sends off the request to deactivate or activate a product in general
		 */
		function changeProductStatus(product_id, oper){
			$.ajax({
				type: "POST",
				url: "ctrlActivateProducts.php?oper="+oper+"&product_id="+product_id,
				success: function(msg){
					if (oper == 'activateProduct'){
						$('td.P-'+product_id).each(function(){
							$(this).removeClass('deactivated isOrderable').addClass('notOrderable');
						});
						$('#ckbox_'+product_id).attr('checked','checked');

					} else if (oper == 'deactivateProduct'){
						$('td.P-'+product_id).each(function(){
							$(this).removeClass('notOrderable isOrderable').addClass('deactivated').empty();
						});
						$('#ckbox_'+product_id).removeAttr('checked');
					}
				},
				error : function(XMLHttpRequest, textStatus, errorThrown){
					$.showMsg({
						msg:XMLHttpRequest.responseText,
						type: 'error'});
				}
			});
		}

		/**
		 *	utility function to retrieve the provider_id from the select
		 */
		function getProviderId(){
			var id = $("#providerSelect option:selected").val(); 
			if (id <= 0){
				return false;
			} else {
				return id; 
			}
		}

		
		
		/**
		 *	event handler for each table cell
		 */
		$('td.interactiveCell').live('click', function(e){
				if($(this).hasClass('deactivated')){
					$.showMsg({
						msg:'This product is currently deactive. In order to set an orderable date, you have to activate this product first by clicking its "active" checkbox.',
						type: 'warning'});
					return false;
				}				

				var tdid = $(this).attr('id');
				var dateID = tdid.split("_");
				var urlStr = "ctrlActivateProducts.php?oper=toggleOrderableProduct&product_id="+dateID[1]+"&date="+dateID[0]; 

				$.ajax({
					type: "POST",
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
			
	

		$("#prevDates").button({
			icons:{
				primary: "ui-icon-circle-triangle-w"
			}
			
		})		
		.click(function(e){

        	var a = new Date(gdates[0]);
   			a.setDate(a.getDate() - seekDateSteps);
   			
   			var date = $.datepicker.formatDate('yy-mm-dd',a);
         	makeDateHeader(date,gdates[0]);
			
        })

        $("#nextDates").button({
        	icons: {
        		secondary: "ui-icon-circle-triangle-e"
        	}
        })
        
        .click(function(e){
           
            var a = new Date(gdates[gdates.length-1]);
  			a.setDate(a.getDate() + seekDateSteps);
  			
  			var date = $.datepicker.formatDate('yy-mm-dd',a);
        	makeDateHeader(gdates[gdates.length-1], date);
        });



		/**
		 *	switch providers
		 */
		$("#providerSelect").xml2html("init", {
			loadOnInit  : true,
			offSet		: 1,
			url         : 'ctrlActivateProducts.php',				
			params 		: 'oper=listProviders'
		}).change(function(){
			var provider_id = getProviderId();
			var provider_name = $("option:selected", this).text();
			
			if (provider_id){ 
				//reload the products 
				$('#dot tbody').xml2html('reload',{
					params:'oper=getTypeOrderableProducts&provider_id='+provider_id,
				});
			} else {
				provider_name = '';
			}
				//set the provider name
				$('#providerName').text(provider_name);

		});



		
		/**
		 *	detect clicks for deactivating or activating the product as such (not for specific dates)  
		 */
		$('input[name="toggleDeActivateProducts"]').live('click', function(e){
			var isDeactivated = ($(this).attr('checked') != 'checked')? false:true;
			var product_id = $(this).attr('id').split("_")[1];
			
			if (isDeactivated){
				changeProductStatus(product_id, 'activateProduct');
			} else {
				//$("#dialog-deactivateProduct").dialog("open");
				changeProductStatus(product_id, 'deactivateProduct');
				
			}
		});


		/**
		 *	interactivity for the date generate buttons
		 */
		$('.tfootDateGenerate')
			.live('mouseenter', function(e){
				$(this).append('<span class="ui-icon ui-icon-circle-arrow-e tdIconCenter" title="Click to repeat this!"></span>')
			})
			.live('mouseleave', function(e){
				$(this).empty();
			})
			.live('click', function(e){
				var selDate = $(this).attr("colDate"); 
				var hasActive = false; 
				$(".Date-"+selDate).each(function(){
					hasActive = $(this).hasClass('isOrderable')
					if (hasActive) return false;
				});

				if(hasActive){
					$("#dialog-generateDates").data('tmpData', {selectedDate:selDate})
					$("#dialog-generateDates").dialog("open");
					$(".Date-"+selDate).addClass('highlight');
				} else {
					$.showMsg({
						msg:'The selected column/date has no orderable products! You have to make at least one product orderable first in order to be able to generate a date pattern.  ',
						type: 'warning'});
				
				}
			});
	

		/**
		 *	dialog generate date pattern
		 */
		$( "#dialog-generateDates").dialog({
				autoOpen: false,
				height: 340,
				width: 480,
				modal: false,
				buttons: {
					"Ok, repeat!":function(){
						generateDatePattern($(this).data('tmpData').selectedDate);
					},
					"<?=$Text['btn_close'];?>" : function(){ 
						$('td').removeClass('highlight');
						$( this ).dialog( "close" );
					}
				}
		});

		/**
		 *	dialog confirm deactivate product
		 */
		$( "#dialog-confirmDeactivate").dialog({
				autoOpen: false,
				height: 220,
				width: 420,
				modal: false,
				buttons: {
					"<?=$Text['btn_ok'];?>":function(){
						changeProductStatus(product_id, 'deactivateProduct');
					},
					"<?=$Text['btn_close'];?>" : function(){
						 
						$( this ).dialog( "close" );
					}
				}
		});

		

		function generateDatePattern(selectedDate){
			var provider_id = getProviderId();
			var nrMonth = $('#nrOfMonth option:selected').val();
			var weeklyFreq = $('#weeklyFreq option:selected').val();
			var urlStr = "ctrlActivateProducts.php?oper=generateDatePattern&date="+selectedDate+"&provider_id="+provider_id+"&nrMonth="+nrMonth+"&weeklyFreq="+weeklyFreq; 

			$.ajax({
				type: "POST",
				url: urlStr,	
				beforeSend : function (){
					//$('#deposit .loadAnim').show();
				},	
				success: function(txt){
					$('#dot tbody').xml2html('reload');
				},
				error : function(XMLHttpRequest, textStatus, errorThrown){
					$.showMsg({
						msg:XMLHttpRequest.responseText,
						type: 'error'});
					
				},
				complete : function(msg){
					$('#dialog-confirmDeactivate').dialog("close");
				}
			}); //end ajax	
			
		}

		
		/**
		 *	Hide/show deactivated products
		 */
		$('#showInactiveProducts').click(function(e){

			$('input[name="toggleDeActivateProducts"]').each(function(){
				if ($(this).attr('checked') != 'checked'){
					$(this).parent().parent().toggle();
				}
			});
		});


		makeDateHeader("Yesterday", "20 days");
		
			
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
		
		Show deactivated products as well: <input type="checkbox" id="showInactiveProducts" />
		
		
		<div id="productDateOverview">
			
			<table id="dot" class="table_datesOrderableProducts">
				<thead>
				<tr>
					<td colspan="3" class="beforeHeader">
						<div class="ui-widget-header">
							<h3 id="providerName" class="">&nbsp;</h3>
						</div>
					</td>
					<td colspan="100" class="beforeHeader">
						<div class="ui-widget-header minPadding">								
							
								<button id="prevDates">Earlier dates</button>							
								<button id="nextDates" class="floatRight">Next Dates</button>
						</div>
					</td>
				</tr>
				<tr>
					<th><?php echo $Text['id'];?></th>
					<th><?php echo $Text['name_item'];?></th>
					<th><?php echo$Text['active'];?></th>
				</tr>
				</thead>
				<tbody>
					<tr id="{id}">
						<td class="prodActive">{id}</td>
						<td>{name}</td>
						<td><input type="checkbox" name="toggleDeActivateProducts" id="ckbox_{id}" isActive="{is_active}"/></td>
									
					</tr>						
				</tbody>
				<tfoot>
					<tr>
						<td>&nbsp;</td>
						<td></td>
						<td></td>
					</tr>
				</tfoot>
			</table>
		</div>
		<br/>
		<br/>
	</div><!-- end of stage wrap -->
</div><!-- end of wrap -->

<div id="mylog"></div>
<div id="dialog-confirmDeactivate"></div>
<div id="dialog-generateDates" title="Generate date-product pattern">
	<p>&nbsp;</p>
	<p>Activate the selected day and products for the next 
							<select id="nrOfMonth" name="nrOfMonth" >
									<?php 
										for ($i=0; $i<configuration_vars::get_instance()->max_month_orderable_dates; $i++){
											$month = $i +1;
											printf("<option value='%s'> %s </option>",$month, $month);	
										}	
									?>
							</select> 
							month(s) every 
							<select id="weeklyFreq" name="weeklyFreq">
								<option value="1">week</option>
								<option value="2">second week</option>
								<option value="3">third week</option>
								<option value="4">four weeks</option>
							</select>
	</p>
							
	<br/>
	<p><em>NOTE:</em> This action will re-generate all dates and products from the current date onwards!</p>
</div>


<!-- / END -->
</body>
</html>