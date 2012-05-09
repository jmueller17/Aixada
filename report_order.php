<?php include "inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_reports'] ;?></title>
		
	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
  	<link rel="stylesheet" type="text/css"   media="print"  href="css/print.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/aixadacart/aixadacart.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/fgmenu/fg.menu.css"   />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/<?=$default_theme;?>/jquery-ui-1.8.20.custom.css"/>

	<?php if (isset($_SESSION['dev']) && $_SESSION['dev'] == true ) { ?> 
    	<script type="text/javascript" src="js/jquery/jquery.js"></script>
		<script type="text/javascript" src="js/jqueryui/jquery-ui-1.8.20.custom.min.js"></script>
		<script type="text/javascript" src="js/fgmenu/fg.menu.js"></script>
		<script type="text/javascript" src="js/aixadautilities/jquery.aixadaMenu.js"></script>     	 
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaXML2HTML.js" ></script>
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaUtilities.js" ></script>

   	<?php  } else { ?>
        <script type="text/javascript" src="js/js_for_report_order.min.js"></script>
    <?php }?>
   		
 	<script type="text/javascript" src="js/jqueryui/i18n/jquery.ui.datepicker-<?=$language;?>.js" ></script>

 	
	<script type="text/javascript">
	
	$(function(){

			

			//decide what to do in which section
			var what = $.getUrlVar('what').toLowerCase();					//should contain report | move | preorder

			$("#OrderListing_"+what).show().removeClass('hideInPrint');

			//check if we have rows for given date
			var nrRows = 0; 
			
			$("h1").first().hide();
			$('#noItemsMsg').hide();

			//dates that are orderable and have already items -> need moving, cannot be deleted
			var datesWithOrders = ["2011-00-00"];
		
			
			/**
			 *	init date pickers
			 */
			
			$("#datepicker").datepicker({
							dateFormat 	: 'DD, d MM, yy',
							showAnim	: '',
							beforeShowDay: function(date){		//activate only those dates that are available for ordering. smallqueries.php order retrieval does not work...
								if (what == 'report'){
									var ymd = $.datepicker.formatDate('yy-mm-dd', date);
									
									if ($.inArray(ymd, datesWithOrders) == -1 ) {
									    return [false,"","Unavailable"];			    
									} else {
										  return [true, ""];
									}
								}
							},
							onSelect 	: function (dateText, instance){
								//$('#noItemsMsg').show();
								$("#OrderListing_"+what).xml2html("reload",{
									params : 'oper=listSummarizedOrdersForDate&date='+getSelectedDate('#datepicker'), 
								});
							}//end select
				}).show();//end date pick

			

			//make all datepickers dragable
			$('#ui-datepicker-div').draggable();

			//util function to retrieve and format selected date
			function getSelectedDate(selector){	
				return $.datepicker.formatDate('yy-mm-dd', $(selector).datepicker('getDate'));
			}


			//retrieve date for upcoming order and set it in the report
			$.ajax({
				type: "GET",
   			    url: "smallqueries.php?oper=getNextDate",
				dataType: "xml", 
				success: function(xml){
					var date = $(xml).find('date_for_order').text();
					//set the next date in the datepicker 
					$("#datepicker").datepicker('setDate', $.datepicker.parseDate('yy-mm-dd', date));
				},
				error : function(XMLHttpRequest, textStatus, errorThrown){
					$.showMsg({
						msg:XMLHttpRequest.responseText,
						type: 'error'});
				}
			}); //end ajax retrieve date
			
			//mark available dates in the calendar
			$.getDatesWithOrders(function(dates){
					datesWithOrders = dates;
					$("#datepicker").datepicker("refresh");
					//$("#datepicker").datepicker('setDate', $.datepicker.parseDate('yy-mm-dd', datesWithOrders[0]));
					$("#OrderListing_"+what).xml2html("reload",{
						params : 'oper=listSummarizedOrdersForDate&date='+getSelectedDate('#datepicker'), 
					});	
			});
			

			
			var nurl = '';
			var nparams = '';
			if (what == 'report'){
				nurl = 'ctrlReport.php';
				nparams = 'oper=listSummarizedOrdersForDate';
			} else if (what == 'preorder'){
				nurl = 'ctrlReport.php';
				nparams = 'oper=listSummarizedPreOrders';
			}

			//select table style for reports Compact | Extended
			var listStyle = "Compact";
			$('#toggleListStyle').bind("click",function(){
				listStyle = (listStyle == "Compact")? "Extended":"Compact";
				$("#OrderListing_"+what).xml2html("reload");
				$('#toggleProviders').attr('checked','checked')
			})
			
			$('#toggleProviders').bind("click",function(){
					$('.toggleProvider').trigger('click');
			})
			
			$('#togglePrint').bind("click",function(){
				$('input[name=printout]').trigger('click');
			})
			
			/**
			 * build list of providers
			 */
			$("#OrderListing_"+what).xml2html("init",{
						url : nurl, 
						params : nparams, 
						loadOnInit : true,
						rowComplete : function(index, row){
							$('#noItemsMsg').hide();

							// class="page-break"
							row.parent().append('<p class="order4Provider showBlock" id="row_'+index+'"></p>');
							 
							//the given provider
							var provider_id = $('a',row).attr('provider_id');
							
							//load the items right away if we want the report
							if (what == 'report'){								
								$('#row_'+index).load('ctrlReport.php?oper=list'+listStyle+'OrdersForProviderAndDate&provider_id='+provider_id+'&date='+getSelectedDate("#datepicker"));
							} else if (what == 'preorder'){
								$('#row_'+index).load('ctrlReport.php?oper=list'+listStyle+'PreOrderProductsForProvider&provider_id='+provider_id);
							}

							nrRows++;
						}
			});

			

			$('.toggleProvider')
			.live("mouseenter", function(){
				if ($(this).children(':first').hasClass('ui-icon-triangle-1-s')){
					$(this).children(':first').removeClass('ui-icon-triangle-1-s').addClass('ui-icon-circle-triangle-s');
				} else {
					$(this).children(':first').removeClass('ui-icon-triangle-1-e').addClass('ui-icon-circle-triangle-e');
				}
			})
			.live("mouseleave", function(){
				if ($(this).children(':first').hasClass('ui-icon-circle-triangle-s')){
					$(this).children(':first').removeClass('ui-icon-circle-triangle-s').addClass('ui-icon-triangle-1-s');
				} else {
					$(this).children(':first').removeClass('ui-icon-circle-triangle-e').addClass('ui-icon-triangle-1-e');
				}
				
			})
			.live("click", function(){
				//close the block
				if ($(this).children(':first').hasClass('ui-icon-circle-triangle-s')){
					$(this).children(':first').removeClass('ui-icon-circle-triangle-s').addClass('ui-icon-circle-triangle-e');		
				} else {
					$(this).children(':first').removeClass('ui-icon-circle-triangle-e').addClass('ui-icon-circle-triangle-s');
				}

				//open close, with reload
				
				if ($(this).parent().next().hasClass('showBlock')){
					$(this).parent().next().removeClass('showBlock').addClass('hidden');
				} else {
					$(this).parent().next().removeClass('hidden').addClass('showBlock');
				}
					//$(this).parent().next().toggle();
				
			});

			
			//if user clicks on printout checkbox hide/show items in printout
			$('input[name=printout]')
				.live('click',function(){
					if ($(this).attr('checked')){
						$(this).closest('h4').removeClass('hideInPrint').next().removeClass('hideInPrint'); 
					} else {
						$(this).closest('h4').addClass('hideInPrint').next().addClass('hideInPrint');	
					}
									
				});
			

			//download zip with all orders. 
			$('#dwnZip').click(function(){
				var url = 'ctrlReport.php?oper=bundleOrdersForDate&date='+ getSelectedDate('#datepicker');
				$.get(url, function(zipURL) {
					 window.frames['dataFrame'].window.location = zipURL;					  
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
	
	
	<div id="stagewrap" >
	
		<div id="titlewrap" class="ui-widget">
			
				<?php 
					print '<h1>'.$Text['move_success'].' </h1>';
				
					if (strtolower($_REQUEST['what']) == 'report') {	
						print '	<div id="titleLeftCol">';		
						print '<h1>' .$Text['ti_report_report'] . '<input  type="text" class="datePickerInput  ui-widget-content ui-corner-all" id="datepicker"> </h1>';
						print '</div><div id="titleRightCol>"';
						print '<p  class="textAlignRight hideInPrint">'.$Text['show_compact'].' <input type="checkbox" class="hideInPrint" id="toggleListStyle" checked/></p>';
						print '<p  class="textAlignRight hideInPrint">'.$Text['show_all_providers'].' <input type="checkbox" class="hideInPrint" id="toggleProviders" checked/></p>';
						//print '<p  class="textAlignRight hideInPrint">'.$Text['show_all_print'].' <input type="checkbox" class="hideInPrint" id="togglePrint" checked/></p>';
						print '<p class="textAlignRight hideInPrint"><a href="javascript:void(null)" id="dwnZip">' . $Text['Download zip'] . '</a></p>';
						print '</div>';
					
					} else if (strtolower($_REQUEST['what']) == 'preorder'){
						print '<h1>' .$Text['ti_report_preorder'] . '</h1>';
						print '<p id="reportShowExtended" class="hideInPrint">'.$Text['show_compact'].' <input class="hideInPrint" type="checkbox" id="toggleListStyle" checked/></p>';
					
					}

				?>
				
				
				
		    	
		</div>
		
	
		
		<div id="mainReportListing" class="ui-widget">
			<div id="OrderListing_report" class="OrderListing hidden hideInPrint">
                    <h4 class="provider ui-widget-header ui-corner-all"><a href="javascript:void(null)" class="toggleProvider" provider_id="{provider_id}"><span class="left-icons ui-icon ui-icon-triangle-1-s"></span>&nbsp;&nbsp;&nbsp; {provider_name}</a> <span class="report_total"><?=$Text['nr_ufs']; ?>: {total_ufs}</span> <span class="report_total"><?=$Text['total_amount'];?>: {total_price} Euro</span> <span class="report_printout hideInPrint"><?=$Text['printout']; ?>&nbsp;&nbsp;<input type="checkbox" name="printout" class="floatRight showInPrint" value="{id}" checked/></span> <span class="report_email">{provider_email} <input type="checkbox" name="email[]" value="{}"/></span></h4>
			</div>
			
			
			<div id="OrderListing_preorder" class="OrderListing hidden hideInPrint">
					<h4 class="provider ui-widget-header ui-corner-all"><a href="javascript:void(null)" class="toggleProvider" provider_id="{id}"><span class="left-icons ui-icon ui-icon-triangle-1-s"></span>&nbsp;&nbsp;&nbsp; {name}</a> <span class="report_total"><?=$Text['nr_ufs']; ?>: {total_ufs}</span> <span class="report_total"><?=$Text['total_amount'];?>: {total_price} Euro</span> <span class="report_printout hideInPrint"><?=$Text['printout']; ?>&nbsp;&nbsp;<input type="checkbox" name="printout" class="floatRight showInPrint" value="{id}" checked/></span> <span class="report_email">email <input type="checkbox" name="email[]" value="{}"/></span></h4>
			</div>
			<div id='noItemsMsg'><h3><?php echo $Text['msg_no_report'];?></h3></div>
			<br/>
			<div>
				
			</div>
		</div>
		
		
	</div>
	<!-- end of stage wrap -->
</div>
<!-- end of wrap -->
<iframe name="dataFrame" style="display:none;"></iframe>
<!-- / END -->
</body>
</html>