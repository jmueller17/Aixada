<?php include "inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_manage_orders'] ;?></title>
	
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
	   	<script type="text/javascript" src="js/js_for_manage_preorders.min.js"></script>
    <?php }?>
   		
 	<script type="text/javascript" src="js/jqueryui/i18n/jquery.ui.datepicker-<?=$language;?>.js" ></script>
   
   
	<script type="text/javascript">
	
	$(function(){


			$('#product_list_provider tbody').xml2html("init");
			$('#btn_submit').button({
				disabled:true,
				icons : {
					secondary: "ui-icon-check"
				},
			}); 
			
			$("#btn_reset").button();
			
			$("#btn_reset").bind('click', function(){
				$("#datepicker").datepicker('setDate',null);
				date = 0; 
				setStateSubmit();
				$('#th_date').html('??');
				$('#product_list_provider tbody').xml2html('removeAll');
						
			});
			
			var gotItems = false;
			
			
			/**
			 * build Provider SELECT
			 */
			$("#providerSelect").xml2html("init", {
							params : 'oper=listPreOrderProviders',
							loadOnInit : true,
							offSet : 1
						}).change(function(){

							//get the id of the provider
							var id = $("option:selected", this).val(); 
							
							if (id < 0) return true; 
	
							$('#product_list_provider tbody').xml2html("reload",{
									params: 'oper=listPreOrderProducts&provider_id='+id,
									complete : function(rc){
										gotItems = (rc > 0)? true:false;
										setStateSubmit();
								}					
							});							
			}); //end select change
			
			//dates available to make orders; start with dummy date
			var availableDates = ["2011-00-00"];

			//dates that are orderable and have already items -> need moving, cannot be deleted
			var datesWithOrders = ["2011-00-00"];
			
			
			$("#datepicker").datepicker({
				dateFormat 	: 'DD, d MM, yy',
				showAnim	: '',
				min : 0,
				beforeShowDay: function(date){		//activate only those dates that are available for ordering. smallqueries.php order retrieval does not work...
					
					var ymd = $.datepicker.formatDate('yy-mm-dd', date);
					if ($.inArray(ymd, availableDates) == -1 && $.inArray(ymd, datesWithOrders) == -1 ) {
					    return [false,"","Unavailable"];			    
					} else {
						  return [true, ""];
					}
				
				},
				onSelect : function (dateText, instance){
					$('#th_date').html(dateText);	 
					setStateSubmit();
				}//end select

			}).show();//end date pick
			
			$('#ui-datepicker-div').draggable();
		

			var today = 0;
			$.ajax({
				type: "GET",
				url: "smallqueries.php?oper=getNextDate",		
				dataType: "xml", 
				success: function(xml){					
					var td = $(xml).find('date_for_order').text();
					today = $.datepicker.parseDate('yy-mm-dd',td);
					
				}
			}); //end ajax retrieve date

			
			$.getOrderableDates('getEmptyOrderableDates', function (dates){
				availableDates = dates;
				$("#datepicker").datepicker("refresh");
			});

			$.getOrderableDates('getDatesWithOrders', function (dates){
				datesWithOrders = dates;
				$("#datepicker").datepicker("refresh");
			});

			
			function setStateSubmit(){
				var date = $.getSelectedDate('#datepicker'); 
				
				if (gotItems && date != 0) {
					$("#btn_submit").button( "option", "disabled", false ); 
				} else {
					$("#btn_submit").button( "option", "disabled", true ); 
				}
			}

		
			
			$('form').submit(function() { 				
  				var dataSerial = $(this).serialize();
  				var date =  $.getSelectedDate('#datepicker'); 
  				$.ajax({
  					   url: 'ctrlShopAndOrder.php?oper=activatePreOrderProducts&date='+date,
  					   data: dataSerial,
  					   success: function(msg){
		  					$.showMsg({
									msg:"<?php echo $Text['msg_preorder_success'];?>" + date,
									type: 'info'});
		  					$("#btn_reset").trigger('click');
  					    },
  					    error : function(XMLHttpRequest, textStatus, errorThrown){
  					    	$.showMsg({
								msg: XMLHttpRequest.responseText,
								type: 'error'});
  					    		
  					    }
  				}); //end ajax
  				return false; 
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
	
	<form id="activatePreOrderForm" method="post" >
	<div id="stagewrap" class="ui-widget">
	
		<div id="titlewrap">
		
			<div id="titleLeftCol">
		    	<h1><?php echo $Text['ti_mng_activate_preorders']  ?></h1>
		    </div>
		    <div id="titleRightCol">
					
		    </div>	
		</div>
		
		<div>
		
			<div id="titleLeftCol">
		    	<p><select id="providerSelect" class="longSelect">
                    	<option value="-1" selected="selected"><?php echo $Text['sel_provider']; ?></option>      
                    	<option value="{id}">{id} {name}</option>               
				</select></p>	
		    </div>
		    <div>
				<p>Select the date: <input  type="text" name="order_date" class="datePickerInput ui-widget-content ui-corner-all" id="datepicker"></p>
		    </div>	
				
				
				
				<div class="product_list_wrap">
					<table id="product_list_provider" class="product_list_mng" >
						<thead>
						<tr>
							<th><?php echo $Text['id'];?></th>
							<th><?php echo $Text['activate_for_date'];?> <span id="th_date">??</span></th>
							<th><?php echo $Text['name_item'];?></th>
							<th><?php echo $Text['total_qty'];?></th>

						</tr>
						<tbody>
							<tr id="{id}">
								<td>{id}</td>
								<td><input type="checkbox" name="activate[]" value="{id}" checked="checked"><input type="hidden" name="product_id[]" value="{id}"/></td>
								<td>{name}</td>
								<td>{total}</td>
							
							</tr>						
						</tbody>
						</thead>
						
					</table>
					<br/><br/>
					<button id="btn_submit" type="submit"><?php echo $Text['btn_save'];?></button> <button id="btn_reset" type="reset"><?php echo $Text['btn_reset'];?></button>
				</div>
					
		</div>
		
		
		

		
	</div>
	<!-- end of stage wrap -->
	</form>	
</div>
<!-- end of wrap -->

<!-- / END -->
</body>
</html>