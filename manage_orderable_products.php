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

		//dates to be displayed in header
		var gdates = [];

		//default number of dates at display
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
					var today = new Date();
					var visMonthYear = new Array(new Date(gdates[0]));
				
					
					for (var i=0; i<gdates.length; i++){
						var dd = new Date(gdates[i]); 
						var date = $.datepicker.formatDate(outDateFormat, dd);
						var dateclass = "Date-"+gdates[i];
						
						if (dd < today) {
							apstr += '<th class="dateth pastDates '+dateclass+'">'+date+'</th>';
						} else {
							apstr += '<th class="dateth '+dateclass+'">'+date+'</th>';
						}
						tfoot += '<td class="tfootDateGenerate" colDate="'+gdates[i]+'"></td>';	

						if (dd.getMonth() != visMonthYear[visMonthYear.length-1].getMonth()){
							visMonthYear.push(dd);
						} 
 
					}

					//construct month year str for title bar of widget
					for (var i=0; i<visMonthYear.length; i++){
						visMonthYear[i] = $.datepicker.formatDate('MM yy',visMonthYear[i]);
					}
					var monthYearStr = visMonthYear.join("/ "); 
					$('.dateTableMonthYear').html(monthYearStr);
					
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
					var cellStyle = (ckbox.attr("isactive") == 1)? "notOrderable":"deactivated";
					var apstr = '';
					
					for (var i=0; i<gdates.length; i++){
						var dateidclass = "Date-"+gdates[i] + " P-"+id;
						var tdid		= gdates[i]+"_"+id; 
						apstr += '<td title="'+gdates[i]+'" id="'+tdid+'" class="'+dateidclass+' interactiveCell '+cellStyle+'"></td>';
					}
					$(row).append(apstr);

					//revise checkboxe attribute for each product row
					if (ckbox.attr("isactive") == 1){
						ckbox.attr("checked", "checked")
					} else {
						ckbox.removeAttr("checked");
						if (!$('#showInactiveProducts').children('span:first').hasClass('ui-icon-check')){
							$(row).hide();
						}
						
					}

					//hide deactivated products
					
					
					
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
								var id = $(this).find('product_id').text();
								var date = $(this).find('date_for_order').text();
								var closing = $(this).find('time_left').text();
								
								//var selector = ".Date-"+date + ".Prod-"+id;
								var selector = "#"+date+"_"+id;
								//$(selector).attr("time_left",closing);
								
								toggleCell(selector);

								$(selector).append('<p class="tdIconRight ui-corner-all"><span class="editClosingDate ui-icon ui-icon-arrowthickstop-1-e"></span></p>'); 
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

		$('span.editClosingDate')
			.live('click', function(e){
				var td = $(this).parent().parent();
				
				if (tdTmp != null){
					tdTmp.insertBefore(td);
					td.removeAttr('colspan');
					tdTmp = null;
				} else {
					var days = 2; //$(this).attr("time_left");
					tdTmp = td.prevAll(':lt('+days+')').detach();
					td.attr('colspan',days+1);
				}
				e.stopPropagation();
			})
			.live('mouseenter', function(e){
				$(this).parent().addClass('ui-state-hover');
			})
			.live('mouseleave', function(e){
				$(this).parent().removeClass('ui-state-hover');
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

		var tdTmp = null; 

		
		
		/**
		 *	event handler for each table cell
		 */
		$('td.interactiveCell')
			.live('mouseover', function(e){
				
				
				
			})
			.live('mouseout', function(e){

			})
			.live('click', function(e){
				
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
			
	

		/**
		 *	date forward backward buttons
		 */
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
			params 		: 'oper=listAllOrderableProviders'
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
				$.showMsg({
					title 	: "Confirm deactivate product", 
					msg		: '<p>You are about to deactivate a product. This means that all associated "orderable" dates will be erased as well.<br/><br/>Are you sure you want to deactivate the product as such? As an alternative you can deactivate selected dates by clicking the corresponding table cells.</p>',
					buttons: {
						"<?=$Text['btn_deactivate'];?>":function(){						
							changeProductStatus(product_id,'deactivateProduct');
							$(this).dialog("close");
						},
						"<?=$Text['btn_cancel'];?>" : function(){
							$( this ).dialog( "close" );
						}
					},
					type: 'confirm'});
			}
		});


		/**
		 *	interactivity for the date generate buttons
		 */
		$('.tfootDateGenerate')
			.live('mouseenter', function(e){
				$(this).append('<p class="textAlignCenter ui-state-hover"><span class="ui-icon ui-icon-circle-arrow-e tdIconCenter" title="Click to repeat this!"></span></p>')
			})
			.live('mouseleave', function(e){
				$(this).empty();
			})
			.live('click', function(e){
				var selDate = $(this).attr("colDate"); 
				var hasActive = false; 
				$("td.Date-"+selDate).each(function(){
					hasActive = $(this).hasClass('isOrderable')
					if (hasActive) return false;
				});

				if(hasActive){
					$("#dialog-generateDates").data('tmpData', {selectedDate:selDate})
					$("#dialog-generateDates").dialog("open");
					$(".Date-"+selDate).addClass('highlight');
				} else {
					$.showMsg({
						msg:'The selected column/date has no orderable products! You have to make at least one product orderable first in order to be able to generate a date pattern.',
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
				buttons: [
				     {
				    	icons : { primary : "ui-icon-check" }, //does not work!
						text: "<?=$Text['btn_repeat'];?>", 
						click : function(){
							generateDatePattern($(this).data('tmpData').selectedDate);
						}
					},
					{
						icons 	: { primary: "ui-icon-close"},
						text	: "<?=$Text['btn_cancel'];?>",
						click	: function(){
							$('td, th').removeClass('highlight');
							$( this ).dialog( "close" );
						} 


					}

				]
		});


		
		/**
		 * utility function to generate date pattners
		 */
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
					$('#dialog-generateDates').dialog("close");
					$('td, th').removeClass('highlight');
				}
			}); //end ajax	
			
		}



		/**
		 *	options button
		 */
		 $("#tblOptions").button({
				icons: {
		        	secondary: "ui-icon-triangle-1-s"
				}
		    }).menu({
				content: $('#tblOptionsItems').html(),	
				showSpeed: 50, 
				width:280,
				flyOut: true, 
				itemSelected: function(item){
					//show hide deactivated products
					if ($(item).attr('id') == "showInactiveProducts"){
						if ($(item).children('span').hasClass('ui-icon-check')){
							$(item).children('span').removeClass('ui-icon ui-icon-check');
						} else {
							$(item).children('span').addClass('ui-icon ui-icon-check');
						}
						$('input[name="toggleDeActivateProducts"]').each(function(){
							if ($(this).attr('checked') != 'checked'){
								if ($(item).children('span').hasClass('ui-icon-check')){
									$(this).parent().parent().show();
								} else {
									$(this).parent().parent().hide();
								}
							} 
						});
					} ;
				} 
			});
		

		makeDateHeader("Yesterday", seekDateSteps+" days");
		
			
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
			<div id="titleLeftCol">
		    	<h1><?php echo $Text['ti_mng_activate_products'];  ?>
		    </div>
		   <div id="titleRightCol">
		   		<div class="wrapSelect textAlignRight">
					<select id="providerSelect" class="longSelect">
					        <option value="-1" selected="selected"><?php echo $Text['sel_provider']; ?></option>                    
	                    	<option value="{id}">{id} {name}</option>
					</select>
				</div>
		   		<div class="textAlignRight"><button	id="tblOptions">Options</button></div>
				<div id="tblOptionsItems" class="hidden">
					<ul>
						<li><a href="javascript:void(null)" id="showInactiveProducts"><span class="floatLeft"></span>&nbsp;&nbsp;Show deactivated products</a></li>
						<li><a href="javascript:void(null)">&nbsp;&nbsp;Number of dates at display</a>
							<ul>
								<li><a href="javascript:void(null)" id="plusTen">+ 10 Dates</a></li>
								<li><a href="javascript:void(null)" id="minusTen">- 10 Dates</a></li></ul>
						</li>
						
					</ul>
				</div>
				
		   </div> 
		    	
		</div>
		
		
		
		
		
		
		<div id="productDateOverview" class="ui-widget">
			<div class="ui-widget-header">
				<h3 id="providerName" class="minPadding floatLeft">&nbsp;</h3>
				<p class="textAlignCenter">
					<button id="prevDates">Earlier dates</button>
					<span class="dateTableMonthYear"></span>							
					<button id="nextDates">Next Dates</button>
				</p>
			</div>
			
			<table id="dot" class="table_datesOrderableProducts ui-widget-content">
				<thead>
				
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
						<td><input type="checkbox" name="toggleDeActivateProducts" id="ckbox_{id}" isactive="{is_active}"/></td>
									
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
	<p><em>NOTE:</em> This action will re-generate all dates and products from the selected date onwards!</p>
</div>


<!-- / END -->
</body>
</html>