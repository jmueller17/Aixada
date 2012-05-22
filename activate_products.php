<?php include "inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_active_products']; ?></title>
	
	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/fgmenu/fg.menu.css"   />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/<?=$default_theme;?>/jqueryui.css"/>
   	
    <?php if (isset($_SESSION['dev']) && $_SESSION['dev'] == true ) { ?> 
	    <script type="text/javascript" src="js/jquery/jquery.js"></script>
		<script type="text/javascript" src="js/jqueryui/jquery-ui-1.8.20.custom.min.js"></script>
		<script type="text/javascript" src="js/fgmenu/fg.menu.js"></script>
		<script type="text/javascript" src="js/aixadautilities/jquery.aixadaMenu.js"></script>     	 
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaXML2HTML.js" ></script>
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaUtilities.js" ></script>
	   
   	<?php  } else { ?>
	   	 <script type="text/javascript" src="js/js_for_activate_products.min.js"></script>
    <?php }?>
   	
 	<script type="text/javascript" src="js/jqueryui/i18n/jquery.ui.datepicker-<?=$language;?>.js" ></script>   
    
   
	<script type="text/javascript">
	$(function(){

						
			//init xml2html template stuff
			$('#inactiveProducts').xml2html('init');
			$('#activeProducts').xml2html('init');

			//dates available to make orders; start with dummy date
			var availableDates = ["2011-00-02"];

			//dates that are orderable and have already items -> need moving, cannot be deleted
			var datesWithOrders = ["2011-11-02"];

			var datesWithSometimesOrderable  = ["2011-11-02"];
			
			
			//datepicker for setting orderable date
			$("#datepicker").datepicker({
				dateFormat 	: 'DD, d MM, yy',
				showAnim	: '',
				minDate : 0,
				beforeShowDay: function(date){		
					var ymd = $.datepicker.formatDate('yy-mm-dd', date);
					//not orderable
					if ($.inArray(ymd, availableDates) == -1 && $.inArray(ymd, datesWithOrders) == -1 ) {
					    return [false,"","Unavailable"];			    
					} else { //is orderable date
						  return [true, ""];
					}
				},
				onSelect : function (dateText, instance){
						$('button:reset').trigger('click').end();		
				}//end select
		
			}).show();//end date pick

			
			//retrieve date for upcoming order
			$.ajax({
				type: "GET",
				url: "smallqueries.php?oper=getNextDate",		
				dataType: "xml", 
				success: function(xml){
					var date = $(xml).find('date_for_order').text();
					//set the next date in the datepicker 
					$("#datepicker").datepicker('setDate', $.datepicker.parseDate('yy-mm-dd', date));
				}, //end success
				error : function(XMLHttpRequest, textStatus, errorThrown){
					$.showMsg({
						msg:XMLHttpRequest.responseText,
						type: 'error'});
				}
			}); //end ajax retrieve date

			
			$("#providerSelect").xml2html("init", {
				loadOnInit  : true,
				offSet		: 1,
				url         : 'ctrlActivateProducts.php',				
				params 		: 'oper=listProviders'
			}).change(function(){
				//get the id of the provider
				var id = $("option:selected", this).val(); 

				if (id < 0) return true; 
				
				$('#inactiveProducts').xml2html("reload",{
					url     : 'ctrlActivateProducts.php',
					tpl		: '<option value="{id}">{id} {name}</option>',
					params	: 'oper=getDeactivatedProducts&provider_id='+id+'&date='+$.getSelectedDate("#datepicker")					
				});	

				$('#activeProducts').xml2html("reload",{
					url     : 'ctrlActivateProducts.php',
					tpl		: '<option value="{id}">{id} {name}</option>',
					params	: 'oper=getActivatedProducts&provider_id='+id+'&date='+$.getSelectedDate("#datepicker")				
				});	

			});

			$('#activate').button({
				icons: { primary: "ui-icon-arrowthick-1-e"}
			}).click(function(){
				$("#inactiveProducts option:selected").each(function (){
					$("#activeProducts").append($(this).detach());
				}); 
				
			});
			
			$('#deactivate').button({
				icons: {secondary: "ui-icon-arrowthick-1-w"}
			}).click(function(){
				$("#activeProducts option:selected").each(function (){
					$("#inactiveProducts").append($(this).detach());
				});  
				
			});
			
			
			
			$("button:reset").button().click(function(){
				$("#providerSelect").children(':lt(2)').attr('selected',true);
				$('#inactiveProducts').empty();
				$('#activeProducts').empty(); 	
			});

			$('#submitBtn').button({
					icons : {secondary: "ui-icon-disk"}
			});
			
			$('#activeProductsForm').submit(function(){

				$("button:submit").button( "option", "disabled", true );
				$('#deactivate').button( "option", "disabled", true );
				$('#activate').button( "option", "disabled", true );

				var product_id = [];
				var i=0; 
				$('#activeProducts option').each(function(){
						if ($(this).val() == "{id}") return true;
						product_id[i++] = $(this).val();
				});
				
				var dataSerial = "provider_id="+$("#providerSelect option:selected").val() +"&product_ids="+product_id + "&date="+$.getSelectedDate("#datepicker");
				
				$.ajax({
					type: "GET",
					url: "ctrlActivateProducts.php?oper=activateProducts",
					data: dataSerial,
					success: function(msg){
						//$("input:submit").button( "option", "disabled", false);
					},
					complete : function(){
						$("button:submit").button( "option", "disabled", false );
						$('#deactivate').button( "option", "disabled", false );
						$('#activate').button( "option", "disabled", false );
					}
				}); //end ajax
				return false; 
			});//end submit



			$.getOrderableDates('getEmptyOrderableDates', function (dates){
				availableDates = dates;
				$("#datepicker").datepicker("refresh");
			});

			$.getOrderableDates('getDatesWithOrders', function (dates){
				datesWithOrders = dates;
				$("#datepicker").datepicker("refresh");
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
		    	
		    	<input  type="text" class="datePickerInput ui-widget-content ui-corner-all" size="10" id="datepicker"> </h1>
		</div>
		<form id="activeProductsForm">
		<div class="wrapSelect">
				<select id="providerSelect" class="longSelect">
				        <option value="-1" selected="selected"><?php echo $Text['sel_provider']; ?></option>                    
                    	<option value="{id}">{id} {name}</option>
				</select>
		</div>
		
		<div id="threeColWrap">
			<div id="leftColumn">
				<h4><?php echo $Text['mo_inact_prod'];?></h4>
				<select multiple="multiple" size="30" class="multipleSelect" id="inactiveProducts">						
				</select>
			</div>
			<div id="middleColumn">
				<button id="activate">&nbsp;<?php echo $Text['btn_activate']; ?>&nbsp;&nbsp;</button><br/><br/>
				<button id="deactivate"><?php echo $Text['btn_deactivate']?></button>
			</div>
			<div id="rightColumn">
				<h4><?php echo $Text['mo_act_prod'];?></h4>
				<select multiple="multiple" size="30" class="multipleSelect" id="activeProducts">					
				</select>
			</div>
			
		</div><!-- end product wrap -->		
		<div id="mngOrderBtn">
			<button type="reset"><?php echo $Text['btn_reset']; ?></button>
			<button type="submit" id="submitBtn"><?php echo $Text['btn_save']; ?></button>
						
		</div>
		</form>

		
	</div>
	<!-- end of stage wrap -->
</div>
<!-- end of wrap -->

<!-- / END -->
</body>
</html>