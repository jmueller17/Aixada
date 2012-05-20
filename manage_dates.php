<?php include "inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_manage_dates'] ;?></title>
	
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
        <script type="text/javascript" src="js/js_for_manage_dates.min.js"></script>
    <?php }?>
   		
 	<script type="text/javascript" src="js/jqueryui/i18n/jquery.ui.datepicker-<?=$language;?>.js" ></script>
 	
	<script type="text/javascript">
	
	$(function(){

			

			//decide what to do in which section
			var what = $.getUrlVar('what').toLowerCase();					//should contain report | move | preorder


			//dates available to make orders; start with dummy date
			var availableDates = ["2011-00-02"];

			//dates that are orderable and have already items -> need moving, cannot be deleted
			var datesWithOrders = ["2011-00-02"];

			//date that are active and that have sometimes orderable products activated
			var datesWithSometimesOrderable = ["2011-00-02"];


			/**
			 * add/delete date ajax calls
			 */
			function doAjaxDates(oper, date){
				if (oper != ""){
					//send de/select date to db and retrieve / update array "available Dates"
					$.ajax({
						type: "POST",
						url: "ctrlDates.php?oper="+oper+"&date="+date,		
						dataType: "JSON", 
						success: function(msg){
							if (oper == "delOrderableDate"){
								//remove date from array.					
								availableDates = jQuery.grep(availableDates, function(value) {
								  return value != date;
								});
								datesWithSometimesOrderable = jQuery.grep(datesWithSometimesOrderable, function(value) {
									  return value != date;
									});
							} else if (oper == "addOrderableDate"){
								availableDates.push(date);
							}

						},  //end success
						complete : function(){
							$("#setOrderable").datepicker("refresh");
						}
					}); //end ajaxz
				}
				
			}


			/**
			 *	load date arrays upon loading of page
			 */	
			function loadAllOrderableDates(){
				$.getOrderableDates('getEmptyOrderableDates', function (dates){
					availableDates = dates;
					$("#setOrderable").datepicker("refresh");
				});

				$.getOrderableDates('getDatesWithOrders', function (dates){
					datesWithOrders = dates;
					$("#setOrderable").datepicker("refresh");
				});
	
				$.getOrderableDates('getDatesWithSometimesOrderable', function (dates){
					datesWithSometimesOrderable = dates;
				});
			}
			
						
			
			/**
			 *	init date pickers
			 */
			$("#setOrderable").datepicker({
							dateFormat 	: 'DD, d MM, yy',
							showAnim	: '',
							numberOfMonths: 3,
							minDate : 0,
							beforeShowDay: function(date){		//activate only those dates that are available for ordering. smallqueries.php order retrieval does not work...
									var ymd = $.datepicker.formatDate('yy-mm-dd', date);
									if ($.inArray(ymd, datesWithOrders) > -1) {
										return [true,"ui-state-active","Date moveable "];	//is orderable and has items										
									} else if ($.inArray(ymd, availableDates) == -1) {
										return [true, ""];	//normal 
									} else  { 				//orderable
										return [true,"ui-state-highlight","Date orderable"];	
									}

							},
							onSelect 	: function (dateText, instance){
								var selectedDate = $.getSelectedDate("#setOrderable");
								
								//has items -> needs moving, cannot be deleted
								if ($.inArray(selectedDate, datesWithOrders) > -1){
									$("#dialog-moveDate").dialog("open");

								//is available and has sometimes orderable items activiated, prompt to be sure for deletion
								} else if ($.inArray(selectedDate, datesWithSometimesOrderable) > -1){
									 $("#dialog-confirmDateDelete").dialog("open");
	
								//is available -> deselect it	
								} else if ($.inArray(selectedDate, availableDates) > -1) {
									doAjaxDates("delOrderableDate", selectedDate);
											   
								//is not available  -> set it orerable
								} else {
									doAjaxDates("addOrderableDate", selectedDate);
								}

								
								
							}//end select
				}).show();//end date pick


			
			
			/**
			 * confirm date dialog
			 */
			 $("#dialog-confirmDateDelete").dialog({
				 	autoOpen: false,
					resizable: true,
					height:320,
					width:360, 
					modal: true,
					buttons: {
						"Delete date": function() {
							doAjaxDates("delOrderableDate", $.getSelectedDate("#setOrderable"));
							$( this ).dialog( "close" );
						},
						"Cancel": function() {
							$( this ).dialog( "close" );
						}
					}
				});
			 
			

			/**
			 *	if user deselects date for which orders exist, this dialoge to move orders opens
			 */
			$("#dialog-moveDate").dialog({
					autoOpen: false,
					height: 480,
					width: 400,
					modal: true,
					buttons: {
						"<?=$Text['btn_cancel'];?>" : function(){
							$( this ).dialog( "close" );
						},
						"<?=$Text['btn_move'];?>" : function(){

							var from_date = $.getSelectedDate("#setOrderable");
							var to_date = $.getSelectedDate("#pickerToDate");
							
							//move the dates
							$.ajax({
								type: "POST",
								url: "ctrlShopAndOrder.php?oper=moveAllOrders&from_date="+from_date+"&to_date="+to_date,		
								dataType: "xml", 
								success: function(xml){
									datesWithOrders = jQuery.grep(datesWithOrders, function(value) {
									  return value != from_date;
									});
									
									datesWithOrders.push(to_date);
									$("#setOrderable").datepicker("refresh");
									$(this).dialog( "close" );
								}, 
								error : function(XMLHttpRequest, textStatus, errorThrown){
									var errmsg = "<?php echo $Text['msg_err_move_date'] ;?>" + ": " + XMLHttpRequest.responseText
									$.showMsg({
										msg:errmsg,
										type: 'error'});
								}
							}); //end ajax moove Date dat
						}
						
					}
			});

			//disable the move button
			$('.ui-dialog-buttonpane button:eq(1)').button('disable');
			
			
			
			
			$("#pickerToDate").datepicker({
				dateFormat 	: 'DD, d MM, yy',
				showAnim	: '', 
				minDate		: 0,
				beforeShowDay: function(date){								//activate only those dates that are available for ordering. smallqueries.php order retrieval does not work...

					var selectedDate = $.getSelectedDate("#setOrderable");	//get the not yet deselected Date
					var dmy = date.getDate() + "-" + (date.getMonth()+1) + "-" + date.getFullYear();

					if (dmy ==  selectedDate) { 							//make sure that users don't select the date they want to move
					    return [false,"","Unavailable date; that's the date you want to move!!"];			    
					} else {
						//other dates are available
						 return [true,"",""];
					}
			
				},
				onSelect	: function (dateText, instance){ 
					//enable the move button
					$('.ui-dialog-buttonpane button:eq(1)').button('enable');

					
				}//end selec
			}).show();


			//detect form submit and prevent page navigation; we use ajax. 
			$('#generateOrderableDates').submit(function() { 


				var fdata = $('#generateOrderableDates').serialize();
				

				$.ajax({
					type: "POST",
					data: fdata,
					url: "ctrlDates.php?oper=generateDates",	
					beforeSend : function (){
						//$('#deposit .loadAnim').show();
					},	
					success: function(msg){
						loadAllOrderableDates();
					},
					error : function(XMLHttpRequest, textStatus, errorThrown){
						
					},
					complete : function(msg){
						
					}
				}); //end ajax
				
				return false; 
			});		
			

			$('#generateDates').button();


			loadAllOrderableDates();
			
				
			
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
	
		<div id="titlewrap" class="ui-widget">	
				<?php 
					if (strtolower($_REQUEST['what']) == 'move'){
						print '<h1>'.$Text['ti_mng_move_orders'].' </h1>';
						
					} else if (strtolower($_REQUEST['what']) == 'setdates'){
						print '<h1>1. '.$Text['ti_mng_dates'].' </h1>';
						
					}
					
				?>
		</div>
		
		<div id="setOrderable" class="floatLeft">
		</div>
		
		<div class="ui-widget" id="dateLegend">
			<p>Legend:</p>
			<table>
				<tr>
					<td class="maxwidth-30">
						<div class="ui-state-highlight textAlignRight">
							<p class="ui-widget-content dateNum">1 </p>
						</div> 
					</td>
                 <td><p><?php echo $Text['msg_can_be_ordered'];?></p></td>
				</tr>
				<tr>
					<td class="maxwidth-30">
						<div class="ui-state-highlight textAlignRight">
							<p class="ui-widget-content ui-state-active dateNum">2 </p>
						</div> 
					</td>
                 <td><p><?php echo $Text['msg_has_ordered_items'];?></p></td>
				</tr>
				<tr>
					<td class="maxwidth-30">
						<div class="ui-state-highlight textAlignRight">
							<p class="dateNum">3 </p>
						</div> 
					</td>
                 <td><p><?php echo $Text['msg_today']; ?></p></td>
				</tr>
				<tr>
					<td class="maxwidth-30">
						<div class="ui-widget-content ui-state-default textAlignRight">
							<p class="dateNum">4 </p>
						</div> 
					</td>
					<td><p><?php echo $Text['msg_default_day']; ?></p></td>
				</tr>
			</table>
		</div>
		
		<div class="ui-widget">	
			<h1 class="clearBoth">2.<?php echo $Text['ti_mng_dates_pattern'];?></h1>
		</div>
		
		<div class="ui-widget">
			<form id="generateOrderableDates">
			<p>Activate for the next <select id="nrOfMonth" name="nrOfMonth" >
									<?php 
										for ($i=0; $i<configuration_vars::get_instance()->max_month_orderable_dates; $i++){
											$month = $i +1;
											printf("<option value='%s'> %s </option>",$month, $month);	
										}	
									?>
									</select> month(s) the following days of the week: </p>
		
			<p id="weekDays">		
				<input type="checkbox" name="weekday[]" id="Mon" value="Monday"/> <label for="Mon"><?=$Text['mon'];?></label><br/>
				<input type="checkbox" name="weekday[]" id="Tue" value="Tuesday"/> <label for="Tue"><?=$Text['tue'];?></label><br/>
				<input type="checkbox" name="weekday[]" id="Wed" value="Wednesday"/> <label for="Wed"><?=$Text['wed'];?></label><br/>
				<input type="checkbox" name="weekday[]" id="Thu" value="Thursday"/> <label for="Thu"><?=$Text['thu'];?></label><br/>
				<input type="checkbox" name="weekday[]" id="Fri" value="Friday"/> <label for="Fri"><?=$Text['fri'];?></label><br/>
				<input type="checkbox" name="weekday[]" id="Sat" value="Saturday"/> <label for="Sat"><?=$Text['sat'];?></label><br/>
				<input type="checkbox" name="weekday[]" id="Sun" value="Sunday"/> <label for="Sun"><?=$Text['sun'];?></label><br/>
			</p>
			<p>Do this for 	<select id="frequency" name="frequency">
								<option value="1">every week</option>
								<option value="2">bi-weekly</option>
								<option value="3">every third week</option>
								<option value="4">every four weeks</option>
							</select></p>
							
			<p>
				<button id="generateDates" type="submit">Generate</button>	
			</p>
			</form>
	
		</div>
		
		
	</div>
	<!-- end of stage wrap -->
</div>
<!-- end of wrap -->
<p id="log"></p>

<div id="dialog-moveDate" title="Move Order">
	<p class="ui-state-error ui-corner-all minPadding">You cannot deselect the current date because there exist already ordered items. As an alternative you can move it to a new date, 
	including all its orders for all providers!  Choose a new date for this order below:</p>
	<br/>
	<div id="pickerToDate">
	</div>
</div>

<div id="dialog-confirmDateDelete" title="Confirm date delete">
	<p class="ui-state-highlight ui-corner-all minPadding">Some products have been activated as "orderable" for the marked date, none of which have been ordered yet. <br/><br/>However, 
	 deleting this date will also delete the associated orderable products. Are you sure you want to deactivate this date and all its 
	 associated products?  
	</p>
</div>


<!-- / END -->
</body>
</html>