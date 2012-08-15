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
						var pastclass = (dd < today)? 'dim40':'';
					
						apstr += '<th class="dateth '+ pastclass +' '+dateclass+'" colDate="'+gdates[i]+'">'+date+'</th>';
						tfoot += '<td class="tfootDateGenerate '+pastclass+' tf'+dateclass+'" colDate="'+gdates[i]+'"></td>';	

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

					$('#btn_colActions').appendTo('#btn_colActionsLimbo');
					
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
						if (!$('#showInactiveProducts').children('span:first').hasClass('ui-icon-check')){
							$(row).hide();
							counterNotActive++;
						}
						
					}

				},
				//finally retrieve if products are orderable for given dates
				complete: function(rowCount){
					//indicate deactivated products for this provider
					
					
					var provider_id = getProviderId();
				
					$.ajax({
						type: "POST",
						dataType:"xml",
						url: "ctrlActivateProducts.php?oper=getOrderableProducts4DateRange&fromDate="+gdates[0]+"&toDate="+gdates[gdates.length-1]+"&provider_id="+provider_id,	
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
								var closingTitle = (days2Closing > 0)? "Closes " + closingDate + ". \n " +days2Closing + " days left for ordering": "order is closed";
								closingTitle = (orderId > 0)? "Order has been finalized and send to provider: #" + orderId: closingTitle;
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

							//modifies closing icons to "finalized/ send off" icons for the whole column. 
							/*$('.ui-icon-mail-closed').each(function(){

								var tdid = $(this).parents('td').attr('id');
								var dateID = tdid.split("_");
								var title = $(this).attr('title');

								$('.Date-'+dateID[0]+'.isOrderable')
									.find('span.ui-icon-locked, span.ui-icon-unlocked')
									.removeClass('ui-icon-locked ui-icon-unlocked')
									.addClass('ui-icon-mail-closed')
									.attr('title', title);

							});*/
							
						},
						error : function(XMLHttpRequest, textStatus, errorThrown){
							$.showMsg({
								msg:XMLHttpRequest.responseText,
								type: 'error'});
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
				
				//$('#colActionIcons').hide();
				/*if (!$(this).hasClass('dim40')){
					var coldate = $(this).attr('colDate');
					$('#btn_colActions').appendTo($('tfoot td.tfDate-'+coldate)).show(); //does not work for opera
				}*/		
			})
			.live('mouseleave', function(e){
				var colDate = $(this).attr('colDate');

				$(".Date-"+colDate).removeClass('ui-state-hover');
				//$('#btn_colActions').hide(); // .deltach($(this));
			})
			.live('click', function(e){
				var colDate = $(this).attr('colDate');
				if ($(".Date-"+colDate).hasClass("dim60")){
					return false; 
				}
				var selector = ".dateth.Date-"+colDate;
				$('#colActionIcons').attr('currentColDate',colDate);
				$( "#colActionIcons" )
		    		.hide()
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
			
			//e.stopPropagation();

				
			});
				
			
		$('.tfootDateGenerate')
			.live('mouseover', function(e){
				$('#colActionIcons').hide();
				if (!$(this).hasClass('dim40')){
					$('#btn_colActions').appendTo($(this)).show(); //does not work for opera
				}		
			})
			.live('mouseleave', function(e){
				//$('#btn_colActions').hide(); // .detach($(this));
			})
		
			
					

	
		/**
		 *	event handler for each table cell
		 */
		$('td.interactiveCell')
			.live('mouseover', function(e){
				$('#btn_colActions').appendTo('#btn_colActionsLimbo');
			})
			.live('mouseout', function(e){
			})
			.live('click', function(e){

				
				
				if ($(this).hasClass('dim40')){
					$.showMsg({
						msg:'This is the past! <br/> Too late to change anything here.',
						type: 'warning'});
					return false;
			
				} else if ($(this).hasClass('deactivated')){
					$.showMsg({
						msg:'This product is currently deactive. In order to set an orderable date, you have to activate this product first by clicking its "active" checkbox.',
						type: 'warning'});
					return false;

					
				} else if ($(this).hasClass('dim60')) {  //check if product is part of a finalized order. If not, this triggers deactivation of product

					$.showMsg({
						msg:'The given product cannot be de/activated because the corresponding order has already been sent to the provider. No further changes are possible! ',
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
		$("#prevDates").button({6
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
			counterNotActive = 0; //reset the counter for the deactivated products
			
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
		 *	detect checkbox clicks for deactivating or activating the product as such (not for specific dates)  
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
		 *	utility function called before a given product can be deactivated for the given date. 
		 * 	if the product forms part of an order that already has been send off, no further
		 *  changes are possible
		 */
		 /*
		function checkOrderStatus(tdid, productId, orderDate){

			 $.ajax({
					type: "POST",
					dataType:"xml",
					url: 'ctrlOrders.php?oper=checkOrderStatus&product_id='+productId+'&date='+orderDate,	
					beforeSend : function (){
						//$('#deposit .loadAnim').show();
					},	
					success: function(xml){
						var allowDeactivate = true; 
						var hasItems = false;  
						$(xml).find('row').each(function(){
							var orderId = $(this).find('order_id').text(); 
							//var orderedItems = $(this).find('quantity').text();
							
							if (orderId > 0){
								$.showMsg({
										msg:'The given product cannot be de/activated because the corresponding order has already been sent to the provider. You can check the detailed status of this order #'+orderId + ' under Manage > Order' ,
										type: 'warning'});
								allowDeactivate = false; 
							}

						});
						if (allowDeactivate){
							toggleOrderableProduct(tdid, productId, orderDate);
						}
						 
					},
					error : function(XMLHttpRequest, textStatus, errorThrown){
						$.showMsg({
							msg:XMLHttpRequest.responseText,
							type: 'error'});
						return false; 
						
					}
				}); //end ajax	
		}*/


		/**
		 *	changes the orderable status of a given product for a given date. triggered
		 *  from checkOrderStatus 
		 */
		function toggleOrderableProduct(id, productId, orderDate){
				$.ajax({
					type: "POST",
					url:  "ctrlActivateProducts.php?oper=toggleOrderableProduct&product_id="+productId+"&date="+orderDate,	
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
					msg:'The closing date cannot be later than the order date!',
					type: 'error'});
				return false; 
			}
			
			var urlStr = 'ctrlActivateProducts.php?oper=modifyOrderClosingDate&provider_id='+provider_id+'&order_date='+orderDate+'&closing_date='+closingDate;

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
					msg:'The selected column/date has no orderable products! You have to make at least one product orderable in order to be able to generate a date pattern.',
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
					msg:'In order to modify the closing date, you need to make at least one product orderable.',
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
		<?php include "inc/menu2.inc.php" ?>
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
		   		<div class="textAlignRight"><button	id="tblOptions">View Options</button></div>
				<div id="tblOptionsItems" class="hidden">
					<ul>
						<li><a href="javascript:void(null)" id="showInactiveProducts"><span class="floatLeft"></span>&nbsp;&nbsp;Show deactivated products</a></li>
						<li><a href="javascript:void(null)">&nbsp;&nbsp;Number of dates at display</a>
							<ul>
								<li><a href="javascript:void(null)" id="plus7">Show +7 days</a></li>
								<li><a href="javascript:void(null)" id="minus7">Show -7 days</a></li></ul>
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
						<td><!-- a href="javascript:void(null)" id=""><span class="ui-icon ui-icon-plus-thick"></span> Show deactivated products</a--></td>
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


<div id="blip" title="Modify closing date for selected provider and order">
	<p>&nbsp;</p>
	<h3>Currently your order will be closed: <span id="infoCurrentClosing"></span></h3>
	<br/>
	<p>Select new closing date: </p>
	<br/>
	<div id="closingDatePicker"></div>
</div>

<div id="btn_colActionsLimbo" class="hidden"></div>
<div id="btn_colActions">Col<span class="ui-icon ui-icon-triangle-1-s floatLeft" ></span></div>
<div id="colActionIcons" class="ui-widget ui-widget-content ui-corner-all hidden" currentColDate="">
	<p class="tfIconCol ui-corner-all"><a href="javascript:void(null)" id="tfIconCol-close"><span class="ui-icon ui-icon-locked tfIcon" title="Modify closing date"></span> Modify closing date</a></p>
	<p class="tfIconCol ui-corner-all"><a href="javascript:void(null)" id="tfIconCol-selrow"><span class="ui-icon ui-icon-circle-arrow-n tfIcon" title="de-/activate entire row"></span> De-/active entire row</a></p>
	<p class="tfIconCol ui-corner-all"><a href="javascript:void(null)" id="tfIconCol-repeat"><span class="ui-icon ui-icon-circle-arrow-e tfIcon" title="Click to repeat this!"></span> Repeat pattern!</a></p>
</div>


<!-- / END -->
</body>
</html>