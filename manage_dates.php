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
								var selectedDate = getSelectedDate("#setOrderable");
								var oper = "";
								
								//has items 
								if ($.inArray(selectedDate, datesWithOrders) > -1){
									$("#moveDate").dialog("open");
									
								//is available -> deselect it	
								} else if ($.inArray(selectedDate, availableDates) > -1) {
									oper = "delOrderableDate";
											   
								//is not available  -> set it orerable
								} else {
									oper="addOrderableDate";
								}

								if (oper != ""){
									//send de/select date to db and retrieve / update array "available Dates"
									$.ajax({
										type: "POST",
										url: "ctrlDates.php?oper="+oper+"&date="+selectedDate,		
										dataType: "JSON", 
										success: function(msg){
											if (oper == "delOrderableDate"){
												//remove date from array.					
												availableDates = jQuery.grep(availableDates, function(value) {
												  return value != selectedDate;
												});
											} else if (oper == "addOrderableDate"){
												availableDates.push(selectedDate);
											}
	
										},  //end success
										complete : function(){
											$("#setOrderable").datepicker("refresh");
										}
									}); //end ajaxz
								}

								
								
							}//end select
				}).show();//end date pick

			
			//util function to retrieve and format selected date
			function getSelectedDate(selector){	
				return $.datepicker.formatDate('yy-mm-dd', $(selector).datepicker('getDate'));
			}
			

			$.getEmptyOrderableDates(function (dates){
					availableDates = dates;
					$("#setOrderable").datepicker("refresh");
			});

			$.getDatesWithOrders(function(dates){
					datesWithOrders = dates;
					$("#setOrderable").datepicker("refresh");		
			});


			/**
			 *	if user deselects date for which orders exist, this dialoge to move orders opens
			 */
			$("#moveDate").dialog({
					autoOpen: false,
					height: 480,
					width: 400,
					modal: true,
					buttons: {
						"<?=$Text['btn_cancel'];?>" : function(){
							$( this ).dialog( "close" );
						},
						"<?=$Text['btn_move'];?>" : function(){

							var from_date = getSelectedDate("#setOrderable");
							var to_date = getSelectedDate("#pickerToDate");
							
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

					var selectedDate = getSelectedDate("#setOrderable");	//get the not yet deselected Date
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
		
		<div id="setOrderable">
		</div>
		<br/>
		
		<div class="ui-widget">
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
		
		
	</div>
	<!-- end of stage wrap -->
</div>
<!-- end of wrap -->
<p id="log"></p>

<div id="moveDate" title="Move Order">
	<p class="ui-state-error ui-corner-all minPadding">You cannot deselect the current date because there exist already ordered items. As an alternative you can move it to a new date, 
	including all its orders for all providers!  Choose a new date for this order below:</p>
	<br/>
	<div id="pickerToDate">
	</div>
	
</div>


<!-- / END -->
</body>
</html>