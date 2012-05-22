<?php include "inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_arrived_products']; ?></title>
	   	
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
	   	 <script type="text/javascript" src="js/js_for_arrived_products.min.js"></script>
    <?php }?>
   		
 	<script type="text/javascript" src="js/jqueryui/i18n/jquery.ui.datepicker-<?=$language;?>.js" ></script>	
   
    
  
	<script type="text/javascript">
	$(function(){

						

			//init tabs
			$("#tabs").tabs();

			//init xml2html template stuff
			$('#notArrivedProducts').xml2html('init');
			$('#arrivedProducts').xml2html('init');

			//dates available to make orders; start with dummy date
			var availableDates = ["2011-00-02"];

			//dates that are orderable and have already items -> need moving, cannot be deleted
			var datesWithOrders = ["2011-11-02"];

			
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
						$('input:reset').trigger('click').end();		
				}//end select
		
			}).show();//end date pick

			
			//retrieve date for upcoming order
			$.ajax({
				type: "GET",
				url: "smallqueries.php?oper=getNextEqualShopDate",		
				dataType: "xml", 
				success: function(xml){
					var date = $(xml).find('date_for_order').text();
					//set the next date in the datepicker 
					$("#datepicker").datepicker('setDate', $.datepicker.parseDate('yy-mm-dd', date));
							
				} //end success
			}); //end ajax retrieve date

			


			
			$("#providerSelect").xml2html("init", {
				loadOnInit  : true,
				offSet		: 1,
				url         : 'ctrlActivateProducts.php',				
			    params 		: 'oper=listOrderedProviders&date=2011-12-14'+$.getSelectedDate('#datepicker')
			}).change(function(){
				//get the id of the provider
				var id = $("option:selected", this).val(); 

				if (id < 0) return true; 
				
				$('#notArrivedProducts').xml2html("reload",{
					url     : 'ctrlActivateProducts.php',
					tpl		: '<option value="{id}">{id} {name}</option>',
					params	: 'oper=getNotArrivedProducts&provider_id='+id+'&date='+$.getSelectedDate('#datepicker')					
				});	

				$('#arrivedProducts').xml2html("reload",{
					url     : 'ctrlActivateProducts.php',
					tpl		: '<option value="{id}">{id} {name}</option>',
					params	: 'oper=getArrivedProducts&provider_id='+id+'&date='+$.getSelectedDate('#datepicker')				
				});	

			});

			$('#activate').button({
				icons: { primary: "ui-icon-arrowthick-1-e"}
			}).click(function(){
				$("#notArrivedProducts option:selected").each(function (){
					$("#arrivedProducts").append($(this).detach());
				}); 
				
			});
			
			$('#deactivate').button({
				icons: {secondary: "ui-icon-arrowthick-1-w"}
			}).click(function(){
				$("#arrivedProducts option:selected").each(function (){
					$("#notArrivedProducts").append($(this).detach());
				});  
				
			});
			
			
			$("input:submit").button();
			$("input:reset").button().click(function(){
				$("#providerSelect").children(':lt(2)').attr('selected',true);
				$('#notArrivedProducts').empty();
				$('#arrivedProducts').empty(); 	
			});
			
			$('#arrivedProductsForm').submit(function(){

				$("input:submit").button( "option", "disabled", true );
				$('#deactivate').button( "option", "disabled", true );
				$('#activate').button( "option", "disabled", true );

				var product_id = [];
				var i=0; 
				$("#arrivedProducts option").each(function(){
						if ($(this).val() == "{id}") return true;
						product_id[i++] = $(this).val();
				});
				
				var dataSerial = "provider_id="+$("#providerSelect option:selected").val() +"&product_ids="+product_id + "&date="+$.getSelectedDate('#datepicker');
				
				$.ajax({
					type: "GET",
					url: "ctrlActivateProducts.php?oper=productsHaveArrived",
					data: dataSerial,
					success: function(msg){
						//$("input:submit").button( "option", "disabled", false);
					},
					complete : function(){
						$("input:submit").button( "option", "disabled", false );
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
			
		    	<h1><?php echo $Text['ti_mng_arrived_products'];  ?>
		    	
		    	<input  type="text" class="datePickerInput ui-widget-content ui-corner-all" size="10" id="datepicker"> </h1>
		</div>
		<form id="arrivedProductsForm">
		<div class="wrapSelect">
				<select id="providerSelect" class="longSelect">
				        <option value="-1" selected="selected"><?php echo $Text['sel_provider']; ?></option>                    
                    	<option value="{id}">{id} {name}</option>
				</select>
		</div>
		
		<div id="threeColWrap">
			<div id="leftColumn">
				<h4><?php echo $Text['mo_notarr_prod'];?></h4>
				<select multiple="multiple" size="30" class="multipleSelect" id="notArrivedProducts">						
				</select>
			</div>
			<div id="middleColumn">
				<button id="activate">&nbsp;<?php echo $Text['btn_arrived']; ?>&nbsp;&nbsp;</button><br/><br/>
				<button id="deactivate"><?php echo $Text['btn_notarrived']?></button>
			</div>
			<div id="rightColumn">
				<h4><?php echo $Text['mo_arr_prod'];?></h4>
				<select multiple="multiple" size="30" class="multipleSelect" id="arrivedProducts">					
				</select>
			</div>
			
		</div><!-- end product wrap -->		
		<!-- div id="mngOrderBtn">
			<input type="reset" value="<?php echo $Text['btn_reset']; ?>"/>
			<input type="submit" value="<?php echo $Text['btn_save']; ?>"/>
		</div-->
		</form>

		
	</div>
	<!-- end of stage wrap -->
</div>
<!-- end of wrap -->

<!-- / END -->
</body>
</html>