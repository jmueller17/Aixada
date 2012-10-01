<?php include "php/inc/header.inc.php" ?>
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

		//counter if provider has deactivated products 
		var counterNotActive = 0; 

		
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
				url: "php/ctrl/Dates.php?oper=getDateRangeAsArray&fromDate="+fromDate+"&toDate="+toDate,	
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
						var pastclass = (dd < today)? 'dim40':'';
					
						apstr += '<th class="dateth clickable '+ pastclass +' '+dateclass+'" colDate="'+gdates[i]+'">'+date+'</th>';
						//tfoot += '<td class="tfootDateGenerate '+pastclass+' tf'+dateclass+'" colDate="'+gdates[i]+'"></td>';	

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
					

					//append the new header/footer with the fresh dates
					$('#dot thead tr').last().append(apstr);

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
				url:'php/ctrl/ActivateProducts.php',
				loadOnInit:false,
				rowComplete : function(rowIndex, row){		//construct table cells with product id and date
					var id =  $(row).attr("id"); 			//get the product id
					var ckbox = $("input:checkbox", row); 	//if the product is active or not
					var cellStyle = (ckbox.attr("isactive") == 1)? "notOrderable":"deactivated";
					var apstr = '';
					
					for (var i=0; i<gdates.length; i++){
						var dateidclass = "Date-"+gdates[i] + " P-"+id; 						//construct two classes to easily select column (date) or row (product id)
						dateidclass += ($(".Date-"+gdates[i]).hasClass("dim40"))? " dim40":""; 	//dim past dates
						var tdid		= gdates[i]+"_"+id; 									//construct id selector of td-cell
						apstr += '<td title="'+gdates[i]+'" id="'+tdid+'" class="'+dateidclass+' interactiveCell '+cellStyle+'"></td>';
					}
					$(row).append(apstr);

					
					//revise checkboxe attribute for each product row
					if (ckbox.attr("isactive") == 1){
						ckbox.attr("checked", "checked")
					} else {
						ckbox.removeAttr("checked");
						if ($('#limbo').html() == 0){ //sort of patch for saving "show inactive products" state
							$(row).hide();
							counterNotActive++;
						}
						
					}

				},
				//finally retrieve if products are orderable for given dates
				complete: function(rowCount){
					var provider_id = getProviderId();
					
					$.ajax({
						type: "POST",
						dataType:"xml",
						url: "php/ctrl/ActivateProducts.php?oper=getOrderableProducts4DateRange&fromDate="+gdates[0]+"&toDate="+gdates[gdates.length-1]+"&provider_id="+provider_id,	
						success: function(xml){
							$(xml).find('row').each(function(){
								var id = $(this).find('product_id').text();
								var date = $(this).find('date_for_order').text();
								var days2Closing = $(this).find('time_left').text();
								var closingDate = $(this).find('closing_date').text();
								var orderId		= $(this).find('order_id').text();
								//var fclosingDate = $.datepicker.formatDate('DD, d MM, yy',new Date(closingDate));
								var hasItems = $(this).find('has_ordered_items').text();

								var closingIcon  = (days2Closing > 0)? "ui-icon-unlocked": "ui-icon-locked"; 
								closingIcon		 = (orderId > 0)? "ui-icon-mail-closed" : closingIcon;
								var closingTitle = (days2Closing > 0)? "<?=$Text['order_closes'];?> " + closingDate + ". \n " +days2Closing + " <?=$Text['left_ordering'];?>": "<?=$Text['ostat_closed'];?>";
								closingTitle = (orderId > 0)? "<?=$Text['ostat_desc_fin_send'];?>" + orderId: closingTitle;
								var hasItems = (hasItems > 0) ? "#"+hasItems: "-";
								
								//var selector = ".Date-"+date + ".Prod-"+id;
								var selector = "#"+date+"_"+id;
								$(selector).attr('closingdate', closingDate);		//a bit overkill. could be attached to each column header				
								toggleCell(selector);

								$(selector).append('<p class="infoTdLine"><span title="'+closingTitle+'" class="floatLeft ui-icon '+closingIcon+'"></span><span class="floatRight">'+hasItems+'</span></p>');

								if (closingIcon == 'ui-icon-mail-closed'){
									$('.Date-'+date).addClass('dim60');																		
								}
								
							});
							
						},
						error : function(XMLHttpRequest, textStatus, errorThrown){
							$.showMsg({
								msg:XMLHttpRequest.responseText,
								type: 'error'});
						}, 
						complete : function(msg){
						}
					}); //end ajax		
					
					
				}
			});


		/* possibility for editing the closing date of each product
		var tdTmp = null; 
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
		*/
		

		/**
		 *	interactivity for the column actions button
		 */
		 $('.dateth')
			.live('mouseenter', function(e){
				$( "#colActionIcons" ).hide();
				var colDate = $(this).attr('colDate');
				$(".Date-"+colDate).addClass('ui-state-hover');
				
			})
			.live('mouseleave', function(e){
				var colDate = $(this).attr('colDate');
				$(".Date-"+colDate).removeClass('ui-state-hover');
				e.stopPropagation();
			})
			.live('click', function(e){
				
				var colDate = $(this).attr('colDate');

				if ($(".Date-"+colDate).hasClass("dim60") || !getProviderId()){
					return false; 
				}

				var selector = ".dateth.Date-"+colDate;
				$('#colActionIcons').attr('currentColDate',colDate);
				$( "#colActionIcons" )
		    		.show()
		    		.position({
						of: $(selector),
						my: 'left top',
						at: 'left bottom',
						offset: '0 -10',
						collision: 'flip'
	
					})
				.bind('mouseleave', function(e){
					$( "#colActionIcons" ).hide();
				});
			
			
				
			});
				
	
		/**
		 *	event handler for each table cell
		 */
		$('td.interactiveCell')
			.live('click', function(e){
				
				if ($(this).hasClass('dim40')){
					$.showMsg({
						msg:"<?=$Text['msg_err_past']; ?>",
						type: 'warning'});
					return false;
			
				} else if ($(this).hasClass('deactivated')){
					$.showMsg({
						msg:"<?=$Text['msg_err_is_deactive_p'];?>",
						type: 'warning'});
					return false;

					
				} else if ($(this).hasClass('dim60')) {  //check if product is part of a finalized order. If not, this triggers deactivation of product

					$.showMsg({
						msg:"<?=$Text['msg_err_deactivate_sent'];?>",
						type: 'warning'});
				   	return false; 
					
				} else {

					var tdid = $(this).attr('id');		//table cell id
					var dateID = tdid.split("_");	
					toggleOrderableProduct(tdid, dateID[1], dateID[0]);
					
				}
	
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
			url         : 'php/ctrl/ActivateProducts.php',				
			params 		: 'oper=listAllOrderableProviders'
		}).change(function(){
			var provider_id = getProviderId();
			var provider_name = $("option:selected", this).text();
			counterNotActive = 0; //reset the counter for the deactivated products
			
			if (provider_id){ 
				//reload the products 
				$('#dot tbody').xml2html('reload',{
					url : 'php/ctrl/ActivateProducts.php',
					params:'oper=getTypeOrderableProducts&provider_id='+provider_id,
				});
			} else {
				provider_name = '';
			}
				//set the provider name
				$('#providerName').text(provider_name);

		});



		/**
		 *	detect checkbox clicks for deactivating or activating the product as such (not for specific dates)  
		 */
		$('input[name="toggleDeActivateProducts"]').live('click', function(e){
			var isDeactivated = ($(this).attr('checked') != 'checked')? false:true;
			var product_id = $(this).attr('id').split("_")[1];
			
			if (isDeactivated){
				changeProductStatus(product_id, 'activateProduct');
			} else {
				$.showMsg({
					msg		: "<?=$Text['msg_err_deactivate_p'];?>",
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
		 *	dialog generate date pattern
		 */
		$( "#dialog-generateDates").dialog({
				autoOpen: false,
				height: 340,
				width: 480,
				modal: false,
				buttons: {  
						"<?=$Text['btn_repeat'];?>" : function(){
							generateDatePattern($(this).data('tmpData').selectedDate);
							},
					
						"<?=$Text['btn_cancel'];?>"	: function(){
							$('td, th').removeClass('ui-state-hover');
							$( this ).dialog( "close" );
							} 
					}
		});



		/**
		 *	dialog for modifying closing date for order
		 */
		$('#blip').dialog({
			autoOpen:false,
			width:500,
			height:540,
			buttons: {  
				"<?=$Text['btn_ok'];?>" : function(){
					
					setClosingDate($(this).data('tmpData').orderDate); 
					},
			
				"<?=$Text['btn_cancel'];?>"	: function(){
					$('td, th').removeClass('ui-state-hover');
					$( this ).dialog( "close" );
					} 
			}
		});



		/**
		 *	changes the orderable status of a given product for a given date. triggered
		 *  from checkOrderStatus 
		 */
		function toggleOrderableProduct(id, productId, orderDate){
				$.ajax({
					type: "POST",
					url:  "php/ctrl/ActivateProducts.php?oper=toggleOrderableProduct&product_id="+productId+"&date="+orderDate,	
					beforeSend : function (){
						//$('#deposit .loadAnim').show();
					},	
					success: function(txt){
						toggleCell('#'+id);

					},
					error : function(XMLHttpRequest, textStatus, errorThrown){
						$.showMsg({
							msg:XMLHttpRequest.responseText,
							type: 'error'});
						
					}
				}); 
			
		}
		

		/**
		 *	modifies the closing date for given provider and order
		 */
		function setClosingDate(orderDate){
			var closingDate = $.getSelectedDate('#closingDatePicker'); 
			var provider_id = getProviderId();

			if (closingDate > orderDate){
				$.showMsg({
					msg:"<?=$Text['msg_err_closing_date'];?>",
					type: 'error'});
				return false; 
			}
			
			var urlStr = 'php/ctrl/ActivateProducts.php?oper=modifyOrderClosingDate&provider_id='+provider_id+'&order_date='+orderDate+'&closing_date='+closingDate;

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
					$('#blip').dialog("close");
					$('td, th').removeClass('ui-state-hover');
				}
			}); //end ajax	

		}
		 
		
		/**
		 * utility function to generate date pattners
		 */
		function generateDatePattern(selectedDate){
			var provider_id = getProviderId();
			var nrMonth = $('#nrOfMonth option:selected').val();
			var weeklyFreq = $('#weeklyFreq option:selected').val();
			var urlStr = "php/ctrl/ActivateProducts.php?oper=generateDatePattern&date="+selectedDate+"&provider_id="+provider_id+"&nrMonth="+nrMonth+"&weeklyFreq="+weeklyFreq; 

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
					$('td, th').removeClass('ui-state-hover');
				}
			}); //end ajax	
			
		}


		/**
		 *	checks if product is active in general 
		 *  and if the column has an active product, otherwise there is nothing to repeat! 
		 */
		function checkRepeat(selDate){
			var hasActive = false; 
			$("td.Date-"+selDate).each(function(){
				hasActive = $(this).hasClass('isOrderable')
				if (hasActive) return false;
			});

			if(hasActive){
				$("#dialog-generateDates").data('tmpData', {selectedDate:selDate})
				$("#dialog-generateDates").dialog("open");
				$(".Date-"+selDate).addClass('ui-state-hover');
			} else {
				$.showMsg({
					msg:"<?=$Text['msg_err_sel_col'];?>",
					type: 'warning'});
			
			}
		}


		/**
		 *	checks the closing date of a product
		 */
		function checkSetClosing(selDate){
			var hasActive = false; 
			$("td.Date-"+selDate).each(function(){
				hasActive = $(this).hasClass('isOrderable')
				if (hasActive) return false;
			});

			if(hasActive){
				var closingDate = $('.Date-'+selDate +'.isOrderable').attr('closingdate');
				//var dd = new Date(closingDate); 
				//var fdate = $.datepicker.formatDate('DD, d MM, yy', dd);
				
				$('#infoCurrentClosing').text(closingDate);
				$("#blip").data('tmpData', {orderDate:selDate})
				$("#blip").dialog("open");				
				$(".Date-"+selDate).addClass('ui-state-hover');
			} else {
				$.showMsg({
					msg:"<?=$Text['msg_err_closing'];?>",
					type: 'warning'});
			
			}
		}

		
		
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
				url: "php/ctrl/ActivateProducts.php?oper="+oper+"&product_id="+product_id,
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
		 *	view options button
		 */
		
		$("#tblOptions")
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
					switch ($(item).attr('id')){
						case 'showInactiveProducts':
							if ($(item).children('span').hasClass('ui-icon-check')){
								$(item).children('span').removeClass('ui-icon ui-icon-check');
								$('#limbo').html(0);
							} else {
								$(item).children('span').addClass('ui-icon ui-icon-check');
								$('#limbo').html(1);
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
							break;
						case 'plus7':
							seekDateSteps += 7; 
							$("#nextDates").trigger('click');
							break;
						case 'minus7':
							if (seekDateSteps > 7 && seekDateSteps < 14) {
								seekDateSteps = 7;
							} else if (seekDateSteps > 14) {
								seekDateSteps -= 7;
							} 
							$("#prevDates").trigger('click');
							break;
							
					}; //end switch
				}//end item selected 
			});//end menu


				
		/**
		 *	col actions button
		 
		$('#btn_colActions')
			.click(function(e){
		    	$( "#colActionIcons" )
		    		.hide()
		    		.show()
		    		.position({
						of: $('#btn_colActions'),
						my: 'left top',
						at: 'left bottom',
						offset: '-10 0',
						collision: 'flip'

					})
					.bind('mouseleave', function(e){
						$( "#colActionIcons" ).hide();
					});
				
				e.stopPropagation();

			});*/

		/**
		 *	col actions menu
		 */
		$('.tfIconCol')
			.bind('mouseenter', function(e){
				$(this).addClass('ui-state-hover');
				e.stopPropagation();

			})
			.bind('mouseleave', function(e){
				$(this).removeClass('ui-state-hover');
				e.stopPropagation();
			})
			.bind('click', function(e){
				var action = $('a',this).attr('id');
				switch (action){
					case 'tfIconCol-repeat':
						
						var selDate = $('#colActionIcons').attr('currentColDate');
						checkRepeat(selDate);
						break;
					case 'tfIconCol-selrow':
						break;
					case 'tfIconCol-close':
						var selDate = $('#colActionIcons').attr('currentColDate');
						checkSetClosing(selDate);
						break;
				}
				$('#colActionIcons').hide();
		});

		$('#colActionIcons').hide();
		$('#closingDatePicker').datepicker();
			
		makeDateHeader("Yesterday", seekDateSteps+" days");

			
	});  //close document ready
</script>
</head>
<body>
<div id="wrap">
	<div id="headwrap">
		<?php include "php/inc/menu2.inc.php" ?>
	</div>
	<!-- end of headwrap -->
	
	
	<div id="stagewrap" class="ui-widget">
	
		<div id="titlewrap">
			<div id="titleLeftCol">
		    	<h1><?php echo $Text['ti_mng_activate_products'];  ?></h1>
		    </div>
		   <div id="titleRightCol">
		   		<div class="wrapSelect textAlignRight">
					<select id="providerSelect" class="longSelect">
					        <option value="-1" selected="selected"><?php echo $Text['sel_provider']; ?></option>                    
	                    	<option value="{id}">{id} {name}</option>
					</select>
				</div>
		   		<div class="textAlignRight"><button	id="tblOptions"><?php echo $Text['view_opt']; ?></button></div>
				<div id="tblOptionsItems" class="hidden">
					<ul>
						<li><a href="javascript:void(null)" id="showInactiveProducts" isChecked="false"><span class="floatLeft"></span>&nbsp;&nbsp;<?php echo $Text['show_deactivated']; ?></a></li>
						<li><a href="javascript:void(null)">&nbsp;&nbsp;<?php echo $Text['days_display'];?></a>
							<ul>
								<li><a href="javascript:void(null)" id="plus7"><?php echo $Text['plus_seven']; ?></a></li>
								<li><a href="javascript:void(null)" id="minus7"><?php echo $Text['minus_seven']; ?></a></li></ul>
						</li>
						
					</ul>
				</div>
				
		   </div> 
		    	
		</div>
		
		
		
		
		
		
		<div id="productDateOverview" class="ui-widget">
			<div class="ui-widget-header">
				<h3 id="providerName" class="minPadding floatLeft">&nbsp;</h3>
				<p class="textAlignCenter">
					<button id="prevDates"><?php echo $Text['btn_earlier']; ?></button>
					<span class="dateTableMonthYear"></span>							
					<button id="nextDates"><?php echo $Text['btn_later']; ?></button>
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

			</table>
		</div>
		<br/>
		<br/>
	</div><!-- end of stage wrap -->
</div><!-- end of wrap -->

<div id="mylog"></div>

<div id="dialog-generateDates" title="Generate date-product pattern">
	<p>&nbsp;</p>
	<p><?php echo $Text['pattern_intro']; ?> 
							<select id="nrOfMonth" name="nrOfMonth" >
									<?php 
										for ($i=0; $i<configuration_vars::get_instance()->max_month_orderable_dates; $i++){
											$month = $i +1;
											printf("<option value='%s'> %s </option>",$month, $month);	
										}	
									?>
							</select> 
							<?php echo $Text['pattern_scale']; ?> 
							<select id="weeklyFreq" name="weeklyFreq">
								<option value="1"><?php echo $Text['week']; ?></option>
								<option value="2"><?php echo $Text['second'] . " " . $Text['week']; ?></option>
								<option value="3"><?php echo $Text['third'] . " " . $Text['week']; ?></option>
								<option value="4"><?php echo $Text['fourth'] . " " . $Text['week']; ?></option>
							</select>
	</p>
							
	<br/>
	<p><<?php echo $Text['msg_pattern']; ?></p>
</div>


<div id="blip" title="Modify closing date for selected provider and order">
	<p>&nbsp;</p>
	<h3><?php echo $Text['order_closes']; ?>: <span id="infoCurrentClosing"></span></h3>
	<br/>
	<p><?php echo $Text['sel_closing_date']; ?>: </p>
	<br/>
	<div id="closingDatePicker"></div>
</div>
<div id="limbo" class="hidden">0</div>
<div id="colActionIcons" class="ui-widget ui-widget-content ui-corner-all hidden" currentColDate="">
	<p class="tfIconCol ui-corner-all"><a href="javascript:void(null)" id="tfIconCol-close"><span class="ui-icon ui-icon-locked tfIcon" title="Modify closing date"></span> <?php echo $Text['btn_mod_date']; ?></a></p>
	<!-- p class="tfIconCol ui-corner-all"><a href="javascript:void(null)" id="tfIconCol-selrow"><span class="ui-icon ui-icon-circle-arrow-n tfIcon" title="de-/activate entire row"></span> <?php echo $Text['btn_entire_row']; ?></a></p-->
	<p class="tfIconCol ui-corner-all"><a href="javascript:void(null)" id="tfIconCol-repeat"><span class="ui-icon ui-icon-circle-arrow-e tfIcon" title="Click to repeat this!"></span> <?php echo $Text['btn_repeat']; ?></a></p>
</div>


<!-- / END -->
</body>
</html>