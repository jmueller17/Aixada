<?php include "inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_prev_orders'] ;?></title>


	<!-- link rel="stylesheet" type="text/css"   media="screen" href="css/css_for_validate.min.css" /-->
	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
	<link rel="stylesheet" type="text/css"   media="screen" href="js/aixadacart/aixadacart.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/fgmenu/fg.menu.css"   />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/jquery-ui/ui-lightness/jquery-ui-1.8.custom.css"/>
	
	
    
    <?php if (isset($_SESSION['dev']) && $_SESSION['dev'] == true ) { ?> 
	    <script type="text/javascript" src="js/jquery/jquery.js"></script>
		<script type="text/javascript" src="js/jquery/jquery-ui-1.8.custom.min.js"></script>
		<script type="text/javascript" src="js/fgmenu/fg.menu.js"></script>
		<script type="text/javascript" src="js/jquery/jquery.aixadaMenu.js"></script>     	 
	   	<script type="text/javascript" src="js/jquery/jquery.aixadaXML2HTML.js" ></script>
	   	<script type="text/javascript" src="js/jquery/jquery.aixadaUtilities.js" ></script>
	   	<script type="text/javascript" src="js/aixadacart/jquery.aixadacart.js" ></script>   	
	   
   	<?php  } else { ?>
	   	<script type="text/javascript" src="js/js_for_validate.min.js"></script>
    <?php }?>
    
  
    <script type="text/javascript" src="js/aixadacart/i18n/cart.locale-<?php echo $language; ?>.js" ></script>
   
   
	<script type="text/javascript">
	$(function(){

		

			//retrieve all dates available
			$('#shopTimes').xml2html('init',{
						url : 'ctrlReport.php',
						offSet	: 1,
			}).change(function(){

				$('#cartLayer').aixadacart('resetCart');
				var shop_id = $("option:selected", this).val(); 

				var uf_id = $("#uf_select option:selected").val();
			
				if (uf_id < 0){
					$.showMsg({
						msg:"<?php echo $Text['msg_err_selectFirstUF'];?>",
						type: 'error'});
					return false;
				}
				
				//reload the list
				$('#cartLayer').aixadacart('loadCart',{
					loadCartURL : 'ctrlReport.php?oper=getShoppedItems&shop_id='+shop_id
				}); //end loadCart

			});
			

			function getSelectedDate(){
				return $('#shopTimes option:selected').val();
			}


			//uf select
			$('#uf_select').xml2html('init',{
				offSet	: 1,
				url 	: 'smallqueries.php',
				params 	: 'oper=getActiveUFs',
				loadOnInit:true
			//event listener to load items for this uf to validate
			}).change(function(){
		
				//get the id of the uf
				var uf_id = $("option:selected", this).val();
				if (uf_id < 1){
					resetFields();K
					 return false; 
				}
				
				$('.insert_uf_id').html('<strong>'+uf_id+'</strong>');	
				$('#shopTimes').xml2html('reload',{ 
					params : 'oper=getAllShopTimes&uf_id='+uf_id,
				});

			});

			function resetFields(){
				$('.insert_uf_id').html('<strong>??</strong>');
				$('#cartLayer').aixadacart('resetCart');
				$('#shopTimes').xml2html('removeAll');
				
			}
			
			//init cart 
			$('#cartLayer').aixadacart("init",{
				saveCartURL : 'ctrlValidate.php',
				cartType	: 'simple',
				btnType		: 'hidden',
				loadSuccess : function(){
					$('input').attr('disabled','disabled');
					$('.ui-icon-close').hide();
				
				}
			});



			

			
	});  //close document ready
</script>

</head>
<?php flush(); ?>
<body>
<div id="wrap">
	<div id="headwrap">
		<?php include "inc/menu2.inc.php" ?>
	</div>
	<!-- end of headwrap -->
	
	
	
	<div id="stagewrap">
	
		
		<div id="titlewrap" class="ui-widget">
			<div id="titleLeftCol">
		    	<h1><?php echo $Text['ti_all_sales']; ?> <span class="insert_uf_id">??</span></h1>
		    </div>
		    <div id="titleRightCol">
		    	<p class="textAlignRight"><select id="uf_select">
		    												<option value="-10" selected="selected"><?php echo $Text['sel_uf']; ?></option>
		    												<option value="{id}">{id} {name}</option>
		    											</select></p>
		    	<p class="textAlignRight"><?php echo $Text['set_date'];?>: <select id="shopTimes">
		    																	<option value="-1"><?php echo $Text['please_select'];?></option>
		    																	<option value="{id}">{id} - {date_for_shop}</option>
		    																</select></p>
		    </div>
		</div>
	

	
				
        <div class="ui-widget">    
        <h3 class="ui-widget-header ui-corner-all">&nbsp;</h3>    
		 <div id="cartLayer"></div>
		</div>		
				
	
			
		
		
	</div>
	<!-- end of stage wrap -->
</div>
<!-- end of wrap -->
<!-- / END -->
</body>
</html>