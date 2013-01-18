<?php include "php/inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " .$Text['head_ti_provider'];?></title>
	
	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
  	<link rel="stylesheet" type="text/css"   media="print"  href="css/print.css" />
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
	   	<script type="text/javascript" src="js/js_for_manage_providers.min.js"></script>
    <?php }?>
   		
 		
 	
	<script type="text/javascript">
	
	$(function(){

		//loading animation
		$('.loadSpinner').attr('src', "img/ajax-loader-<?=$default_theme;?>.gif"); 
		

		//marks the selected provider row
		var gSelProvider = null;


		//marks selected product row
		var gSelProduct = null; 


		//needed for setup of new provider form
		var gFirstTimeNewProvider = true; 

		//setup of new product form
		var gFirstTimeNewProduct = true; 

		//edit/save data 
		var gProviderListReload = false; 

		//when to reload the product list
		var gProductListReload = false; 
		

		//data for constructing the select options for the provider form
		var gProviderSelects = [['aixada_uf', 'sResponsibleUfId','id','name']];

		//data for constructing the select options for the product form
		//format is: [table, destinationClassSelector, field1, field2,...]
		var gProductSelects = [	
								['aixada_orderable_type', 'sOrderableTypeId', 'id', 'description'],
								['aixada_uf', 'sResponsibleUfId','id','name'],
								['aixada_product_category','sCategoryId', 'id', 'description'],
								['aixada_rev_tax_type','sRevTaxTypeId', 'id', 'name'],
								['aixada_iva_type','sIvaPercentId','id','name'],
								['aixada_unit_measure','sUnitMeasureOrderId','id','name'],
								['aixada_unit_measure','sUnitMeasureShopId','id','name']
						];
		

		/***********************************************************
		 *
		 *  PROVIDER LISTING AND EDIT/NEW STUFF
		 *
		 ***********************************************************/

		//list providers
		$('#tbl_providers tbody').xml2html("init", {
			url: 'php/ctrl/Providers.php',
			params : 'oper=getProviders&all=1',
			loadOnInit:true,
			beforeLoad: function(){
				$('.loadSpinner').show();
			},
			rowComplete : function (rowIndex, row){

			},
			complete : function(rowCount){
				$('.loadSpinner').hide();
				$('tr:even', this).addClass('rowHighlight');
				$('p.providerActiveStatus').each(function(){
					if ($(this).text() == "1"){
						$(this).html('<span class="ui-icon ui-icon-check"></span>').addClass('aix-style-ok-green ui-corner-all')
					} else {
						$(this).html('<span class="ui-icon ui-icon-cancel"></span>').addClass("noRed ui-corner-all");
					}
				});
			}
		});

		//interactivity of provider listing table
		$('#tbl_providers tbody tr')
			.live('mouseenter', function(){
				$(this).addClass('ui-state-hover');
			})
			.live('mouseleave',function(){
					$(this).removeClass('ui-state-hover');
			})
			//click on table row
			.live("click", function(e){
				$('#tbl_providers tbody tr').removeClass('ui-state-highlight');
				gSelProvider = $(this);
				gSelProvider.addClass('ui-state-highlight');
				gProductListReload = true; 
				switchTo('overviewProducts');
			});

		
		//load provider for editing
		$('#tbl_provider_edit tbody').xml2html('init',{
			url : 'php/ctrl/Providers.php',
			loadOnInit:false,
			rowComplete : function (rowIndex, row){
				setCheckBoxes('#frm_provider_edit');
				populateSelect(gProviderSelects,'#tbl_provider_edit');
			}

		});
			


		//PROVIDER BUTTONS
		//new provider
		$('#btn_new_provider')
			.button({
				icons: {
	        		primary: "ui-icon-plus"
	        	}
			})
			.click(function(){
				prepareProviderForm();			
				switchTo('newProvider');
			});

		
		//edit provider
		$('.btn_edit_provider')
			.live('click', function(e){
				$('#tbl_providers tbody tr').removeClass('ui-state-highlight');
				gSelProvider = $(this).parents('tr');
				gSelProvider.addClass('ui-state-highlight');
				
				$('#tbl_provider_edit tbody').xml2html('reload',{
					params: 'oper=getProviders&all=1&provider_id='+gSelProvider.attr('providerId')
				});
				
				switchTo('editProvider');
				e.stopPropagation();
			});

		//save edited provider new provider
		$('.btn_save_provider')
			.button({
				icons: {
	        		primary: "ui-icon-disk"
	        	}
			})
			.click(function(e){	
				if ($(this).hasClass('edit')){
					if (checkProviderForm('#pgProviderEdit')){
						submitForm('#pgProviderEdit', 'edit', 'provider');
					}		 
				} else if ($(this).hasClass('add')){
					if (checkProviderForm('#pgProviderNew')){
						submitForm('#pgProviderNew', 'add', 'provider');
					}
				}
				e.stopPropagation();
				return false; 
			});

		//cancel edits/forms
		$('.btn_cancel_provider')
		.button({
				icons: {
	        		primary: "ui-icon-disk"
	        	}
			})
			.click(function(e){			
				switchTo('overviewProvider');
				return false; 
		});

		//delete provider
		$('.btn_del_provider')
			.live('click', function(e){

				var providerId = $(this).parents('tr').attr('providerId');
					
				$.showMsg({
					msg: "<?php echo $Text['msg_confirm_del_provider']; ?>",
					buttons: {
						"<?=$Text['btn_ok'];?>":function(){
							$this = $(this);
							var urlStr = 'php/ctrl/TableManager.php?oper=del&table=aixada_provider&id='+providerId; 

							$.ajax({
							   	url: urlStr,
							   	type: 'POST',
							   	success: function(msg){
									//reload all members listing on overiew. 
							   		$('#tbl_providers tbody').xml2html('reload');
							   		$this.dialog( "close" ); 
							   	},
							   	error : function(XMLHttpRequest, textStatus, errorThrown){
									if (XMLHttpRequest.responseText.indexOf("ERROR 10") != -1){
										$this.dialog("close");
										$.showMsg({
												msg: "<?=$Text['msg_err_del_provider']; ?>" + XMLHttpRequest.responseText,
												type: 'error'});

									}
								   	
	
							   	}
							}); //end ajax
													
							
						},
						"<?=$Text['btn_cancel'];?>" : function(){
							$( this ).dialog( "close" );
						}
					},
					type: 'confirm'});

				e.stopPropagation();
				
			})

			

		/**
		 *	provider utility functions
		 */
		function checkProviderForm(mi){

			var isValid = true; 
			var isValidItem = true; 
			var err_msg = ''; 
			
			isValidItem = $.checkFormLength($(mi +' input[name=name]'),2,150);
			if (!isValidItem){
				isValid = false; 
				err_msg += "<?=$Text['msg_err_providershort'];?>" + "<br/><br/>"; 
			}

			
			if ($(mi+' input[name="email"]').val().length > 0){
				isValidItem =  $.checkRegexp($(mi+' input[name="email"]'),/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
				if (!isValidItem){
					isValid = false; 
					err_msg += "<?=$Text['msg_err_email'] ?>"+ "<br/><br/>";
				}
		 	}				


			isValidItem = $.checkSelect($(mi +' input[name=responsible_uf_id]'),['']);
			if (!isValidItem){
				isValid = false; 
				err_msg += "<?=$Text['msg_err_select_responsibleuf'];?>" + "<br/><br/>"; 
			}

			if (isValid) {
				return true; 
			} else {
				$.showMsg({
					msg:err_msg,
					type: 'error'});
				return false; 
			}

		}

		 
		//creates the new provider form, which is copied one time from the provider edit form template
		//resets input fields. 
		function prepareProviderForm(){
			var frm = $('#frm_provider_new'); 
			//prepare the provider form the first time it is called
			if (gFirstTimeNewProvider){
				
				//copy the provider form 
				var tblStr = $('#tbl_provider_edit tbody').xml2html("getTemplate");
				$('#tbl_provider_new tbody').append(tblStr);

				//construct the responsible uf select
				populateSelect(gProviderSelects,'#tbl_provider_new');

				//new providers have no id
				$('#tbl_provider_new input[name=id]').remove();

				gFirstTimeNewProvider = false; 
			}

			//reset all textfields
			$('input:text, input:hidden, textarea', frm).val('');

			//assume that a new provider is active
			$('input:checkbox', frm).each(function(){
				$(this).attr('checked','checked');
			});

			//reset provider id. 
			$('.setProviderId', frm).html('&nbsp;');
			$('.setProviderName').text('');
			
			
		}

		
		
		/*****************************************
		 *
		 *	PRODUCT LISTING BY PROVIDER
		 *
		 *****************************************/
		 
		$('#tbl_products tbody').xml2html("init",{
			url : 'php/ctrl/ShopAndOrder.php',
			loadOnInit:false,
			beforeLoad: function(){
				$('.loadSpinner').show();
			},
			rowComplete : function(rowIndex, row){	
				var tds = $(row).children();

				//stock
				if (tds.eq(3).text() == "1"){
					tds.eq(3).text("<?=$Text['stock'];?>");
					$.formatQuantity(tds.eq(9));
				//orderable
				} else if (tds.eq(3).text() == "2"){
					tds.eq(3).text("<?=$Text['orderable'];?>");
					tds.eq(9).text(""); //delete stock info
				}

				
				if (tds.eq(4).children('p:first').text() == "1"){
					tds.eq(4).children('p:first').html('<span class="ui-icon ui-icon-check"></span>').addClass('aix-style-ok-green ui-corner-all');
				} else {
					tds.eq(4).children('p:first').html('<span class="ui-icon ui-icon-closethick"></span>').addClass('noRed ui-corner-all');
				}

			},
			complete : function (rowCount){
				$('.loadSpinner').hide();
				$('tr:even', this).addClass('rowHighlight');
				
				if (gSelProduct != null && gSelProduct.attr('productId') > 0){
					gSelProduct.addClass('ui-state-highlight');
				}
		
				
			}						
		});			

		//products listing behavior
		$('#tbl_products tbody tr')
			.live('mouseenter', function(){
				$(this).addClass('ui-state-hover');
			})
			.live('mouseleave',function(){
					$(this).removeClass('ui-state-hover');
			})
			//click on table row
			.live("click", function(e){
	
				$('#tbl_products tbody tr').removeClass('ui-state-highlight');
				gSelProduct = $(this);
				gSelProduct.addClass('ui-state-highlight');

				$('#tbl_product_edit tbody').xml2html('reload',{
					params: 'oper=getProductDetail&product_id='+gSelProduct.attr('productId')
				});
				
				switchTo('editProduct');
				e.stopPropagation();
				
			});

		

		//load product for editing
		$('#tbl_product_edit tbody').xml2html('init',{
			url : 'php/ctrl/ShopAndOrder.php',
			loadOnInit:false,
			rowComplete : function (rowIndex, row){
				setCheckBoxes('#frm_product_edit');
				populateSelect(gProductSelects,'#tbl_product_edit');
			}

		});



		//product buttons
		$('#btn_new_product')
			.button({
				icons: {
	        		primary: "ui-icon-plus"
	        	}
			})
			.click(function(){
				prepareProductForm();			
				switchTo('newProduct');
			});

		
		//edit provider
		$('.btn_edit_product')
			.live('click', function(e){


			});

		//save edited provider new provider
		$('.btn_save_product')
			.button({
				icons: {
	        		primary: "ui-icon-disk"
	        	}
			})
			.click(function(e){	
				
				if ($(this).hasClass('edit')){
					if (checkProductForm('#pgProductEdit')){
						submitForm('#pgProductEdit', 'edit', 'product');
					}		 
				} else if ($(this).hasClass('add')){
					if (checkProductForm('#pgProductNew')){
						submitForm('#pgProductNew', 'add', 'product', 'overviewProducts');
					}
				}
				e.stopPropagation();
				return false; 
			});

		//cancel edits/forms
		$('.btn_cancel_product')
		.button({
				icons: {
	        		primary: "ui-icon-disk"
	        	}
			})
			.click(function(e){			
				switchTo('overviewProducts');
				return false;
		});

		//delete prodcut
		$('.btn_del_product')
			.live('click', function(e){

				var productId = $(this).parents('tr').attr('productId');
					
				$.showMsg({
					msg: "<?php echo $Text['msg_confirm_del_product']; ?>",
					buttons: {
						"<?=$Text['btn_ok'];?>":function(){
							$this = $(this);
							var urlStr = 'php/ctrl/TableManager.php?oper=del&table=aixada_product&id='+productId; 

							$.ajax({
							   	url: urlStr,
							   	type: 'POST',
							   	success: function(msg){
									//reload all members listing on overiew. 
							   		$('#tbl_products tbody').xml2html('reload');
							   		$this.dialog( "close" ); 
							   	},
							   	error : function(XMLHttpRequest, textStatus, errorThrown){
									if (XMLHttpRequest.responseText.indexOf("ERROR 10") != -1){
										$this.dialog("close");
										$.showMsg({
												msg: "<?=$Text['msg_err_del_product']; ?>" + XMLHttpRequest.responseText,
												type: 'error'});

									}
								   	
	
							   	}
							}); //end ajax
													
							
						},
						"<?=$Text['btn_cancel'];?>" : function(){
							$( this ).dialog( "close" );
						}
					},
					type: 'confirm'});

				e.stopPropagation();
				
			})
		



		//product utility functions
		/**
		 *	provider utility functions
		 */
		function checkProductForm(mi){

			var isValid = true; 
			var isValidItem = true; 
			var err_msg = ''; 
			
			isValidItem = $.checkFormLength($(mi +' input[name=name]'),2,150);
			if (!isValidItem){
				isValid = false; 
				err_msg += "<?=$Text['msg_err_productshort'];?>" + "<br/><br/>"; 
			}
					

			isValidItem = $.checkSelect($(mi +' input[name=responsible_uf_id]'),['']);
			if (!isValidItem){
				isValid = false; 
				err_msg += "<?=$Text['msg_err_select_responsibleuf'];?>" + "<br/><br/>"; 
			}

			isValidItem = $.checkSelect($(mi +' input[name=category_id]'),['','1']);
			if (!isValidItem){
				isValid = false; 
				err_msg += "<?=$Text['msg_err_product_category'];?>" + "<br/><br/>"; 
			}

			//if this is an order item, make sure order unit measure is set
			if ($(mi +' input[name=orderable_type_id]').val() == 2){
				isValidItem = $.checkSelect($(mi +' input[name=unit_measure_order_id]'),['','1']);
				if (!isValidItem){
					isValid = false; 
					err_msg += "<?=$Text['msg_err_order_unit'];?>" + "<br/><br/>"; 
				}
			}

			isValidItem = $.checkSelect($(mi +' input[name=unit_measure_shop_id]'),['','1']);
			if (!isValidItem){
				isValid = false; 
				err_msg += "<?=$Text['msg_err_shop_unit'];?>" + "<br/><br/>"; 
			}

			isValidItem =  $.checkNumber($(mi+' input[name="unit_price"]'),0.00, 2);
			if (!isValidItem){
				isValid = false; 
				err_msg += "<?php echo $Text['unit_price'] .  $Text['msg_err_only_num']; ?>"+ "<br/><br/>";
			}

			if (isValid) {
				return true; 
			} else {
				$.showMsg({
					msg:err_msg,
					type: 'error'});
				return false; 
			}

		}

		//creates the new product from first time called; otherwise resets fields 
		function prepareProductForm(){
			var frm = $('#frm_product_new'); 
			//prepare the provider form the first time it is called
			if (gFirstTimeNewProduct){
				
				//copy the provider form 
				var tblStr = $('#tbl_product_edit tbody').xml2html("getTemplate");
				
				$('#tbl_product_new tbody').append(tblStr);

				//construct the responsible uf select
				populateSelect(gProductSelects,'#tbl_product_new');

				//new providers have no id
				$('#tbl_product_new input[name=id]').remove();

				gFirstTimeNewProduct = false; 
			}

			//reset all textfields
			$('input:text, input:hidden, textarea', frm).val('');

			//assume that a new provider is active
			$('input:checkbox', frm).each(function(){
				$(this).attr('checked','checked');
			});

			//reset provider id. 
			$('.setProductId', frm).html('&nbsp;');
			$('.setProductName').text('');
			
			//set provider id
			$('#frm_product_new input[name=provider_id]').val(gSelProvider.attr('providerId'));

			/*$('select', frm).each(function(){
				var firstElementValue = $(this).children(':first').val();
				$(this).val(firstElementValue).attr('selected','selected').parent().prev().attr('value',firstElementValue);
			})*/
			
		}



		
		/*****************************
		 *
		 *	GLOBAL (UTIL) FUNCTIONS
		 *
		 ****************************/
		function switchTo(section){


			switch(section){

				case 'overviewProvider':
					$('.pgProductOverview, .pgProviderEdit, .pgProviderNew, .pgProductEdit, .pgProductNew').hide();
					if (gProviderListReload) { //if provider has been edited/new reload listing
						$('#tbl_providers tbody').xml2html("reload"); 
					}
					

					$('.pgProviderOverview').fadeIn(1000);
					gProviderListReload = false; 
					
					break;
	
				case 'overviewProducts':
					if (gSelProvider.attr('providerId') > 0) { 
						if (gProductListReload){
							$('#tbl_products tbody').xml2html("reload",{
								params: 'oper=getShopProducts&provider_id='+gSelProvider.attr('providerId')+"&all=1",
							});
						}

						$('.setProviderName').html(gSelProvider.children().eq(2).text());
					
						$('.pgProviderOverview, .pgProviderEdit, .pgProviderNew, .pgProductEdit, .pgProductNew').hide();
						$('.pgProductOverview').fadeIn(1000);
							
						gProductListReload = false; 
					}
					break;

					
				case 'editProvider':
					$('.setProviderName').html(gSelProvider.children().eq(2).text());
					$('.pgProviderOverview, .pgProductOverview, .pgProviderNew, .pgProductEdit, .pgProductNew').hide();
					$('.pgProviderEdit').fadeIn(1000);
					
					break;

				case 'newProvider':
					$('.pgProviderOverview, .pgProductOverview, .pgProviderEdit, .pgProductEdit, .pgProductNew').hide();
					$('.pgProviderNew').fadeIn(1000);
					
					break;


				case 'editProduct':
					$('.setProviderName').html(gSelProvider.children().eq(2).text());
					$('.setProductName').html(gSelProduct.children().eq(2).text());
					
					$('.pgProviderOverview, .pgProductOverview, .pgProviderEdit, .pgProviderNew, .pgProductNew').hide();
					$('.pgProductEdit').fadeIn(1000);
					break;


				case 'newProduct':
					$('.pgProviderOverview, .pgProductOverview, .pgProviderEdit, .pgProviderNew, .pgProductEdit').hide();
					$('.pgProductNew').fadeIn(1000);
					break;
					
					
					
					
			}

		}

		switchTo('overviewProvider');


		/**
		 *	submits the create/edit provider data
		 *  mi: is the selector string of the layer that contains the whole member form and info
		 * 	assume form has been checked		 
		 */
		function submitForm(mi, action, table, returnTo){


			//make sure an active=0 gets send if checkbox is unchecked
			$(mi +' input:checkbox').each(function(){
				var isChecked = $(this).attr('checked'); 
				
				if(isChecked){
					$(this).val(1);
				} else {
					$(this).val(0);
					$(mi + ' form').append('<input type="hidden" name="active" value="0"/>')
				}	
			});
						
			var sdata = $(mi + ' form').serialize();

			$.ajax({
			   	url: 'php/ctrl/TableManager.php?oper='+action+'&table=aixada_'+table,
			   	method: 'POST',
				data: sdata, 
			   	beforeSend: function(){
			   		$('.loadSpinner').show();
			   		$('.btn_save_provider, .btn_save_product').button('disable');
				},
			   	success: function(msg){
			   	 	$.showMsg({
						msg: "<?php echo $Text['msg_edit_success']; ?>",
						type: 'success',
						autoclose:1000});
			   		
			   	},
			   	error : function(XMLHttpRequest, textStatus, errorThrown){
				   $.showMsg({
						msg: XMLHttpRequest.responseText,
						type: 'error'});
			   	},
			   	complete : function(msg){
			   		$('.loadSpinner').hide();
			   		$('.btn_save_provider, .btn_save_product').button('enable');
			   		if (table == 'provider'){
				   		gProviderListReload = true; 
			   		} else if (table == 'product'){
						gProductListReload = true; 
				   	}

				   	if (undefined != returnTo){
						switchTo(returnTo);
					}

			   	}
			}); //end ajax

			
		}


		//tranfors checkbox value in ticked box
		function setCheckBoxes(selStr){
			$(selStr +' input:checkbox').each(function(){
				var bool = $(this).val(); 
				if (bool == "1") $(this).attr('checked',true);
			});
		}

		function loadSelectHTML(urlStr, destination){
			$.post(urlStr, function(html){
				var selValue = $(destination).append(html).prev().val(); 

				//new provider/product have no value, so we take the first option
				//this needs to be set manually, otherwise with a new form, no values get send
				if (selValue == ''){
					var selValue = $(destination).children('select:first').val();				
					$(destination).prev().attr('value',selValue);
				} else {
				
					$(destination).children('select').val(selValue).attr('selected','selected');
				}						
				
			})	

		}

		//loads the options for select boxes. The whole story is  that the selects
		//delivered from the getFieldsOptions of the TableManger don't have the name attribute set
		//and they get added after the xml2html template was triggered. So we mirror the value with a simple 
		//hidden intput field and the corresponding name
		function populateSelect(tbls, destTable){
			for (var i=0; i<tbls.length; i++){
				var urlStr = 'php/ctrl/SmallQ.php?oper=getFieldOptions&table='+tbls[i][0]; 
				var destination = destTable +' .'+tbls[i][1]; 

				for (var j=2; j<tbls[i].length; j++){
					urlStr += '&field'+(j-1)+'='+tbls[i][j]; 	
				}

				loadSelectHTML(urlStr, destination);	
			}
		} 
		

		//trick for setting the chosen option of the selects since generated selects don't have name!
		$('select')
			.live('change', function(){
				$(this).parent().prev().val($('option:selected',this).val());
			})
			
		//remove all eventual error styles on input fields. 
		$('input')
			.live('focus', function(e){
				$(this).removeClass('ui-state-error');
			});
		
		$('input[name=bulkAction]')
			.live('click', function(e){
				e.stopPropagation();
			})		

		
		//overview buttons
		$("#btn_overview_provider").button({
			icons: {
	        		primary: "ui-icon-circle-arrow-w"
	        	}
			 })
    		.click(function(e){
				switchTo('overviewProvider'); 
    		});

		$("#btn_overview_product").button({
			icons: {
	        		primary: "ui-icon-circle-arrow-w"
	        	}
			 })
    		.click(function(e){
				switchTo('overviewProducts'); 
    		});
		 
							
	});  //close document ready
</script>

</head>
<body>
<div id="wrap">
	<div id="headwrap">
		<?php include "php/inc/menu.inc.php" ?>
	</div>
	<!-- end of headwrap -->
	
	
	<div id="stagewrap">
	
				<div id="titlewrap" class="ui-widget">
					<div id="titleLeftCol50">
						<button id="btn_overview_provider" class="floatLeft btn_back pgProductOverview pgProviderEdit pgProviderNew"><?php echo $Text['overview'];?></button>
						<button id="btn_overview_product" class="floatLeft btn_back pgProductEdit pgProductNew"><?php echo $Text['overview'];?></button>
				    	<h1 class="pgProviderOverview"> <?php echo $Text['head_ti_provider']; ?></h1>
				    	<h1 class="pgProductOverview setProviderName"></h1>
				    	<h1 class="pgProviderEdit">&nbsp;&nbsp;<?php echo $Text['edit']; ?> - <span class="setProviderName"></span></h1>
				    	<h1 class="pgProviderNew">&nbsp;&nbsp;<?php echo $Text['ti_create_provider'] ; ?></h1>
				    	<h1 class="pgProductEdit">&nbsp;&nbsp;<?php echo $Text['edit']; ?> - <span class="setProviderName"></span> - <span class="setProductName"></span></h1>
				    	<h1 class="pgProductNew">&nbsp;&nbsp;<span class="setProviderName"></span> - <?php echo $Text['ti_add_product']; ?></h1>
		    		</div>
		    		<div id="titleRightCol50">
						<button class="floatRight pgProviderOverview" id="btn_new_provider"><?php echo $Text['btn_new_provider']; ?></button>
						<button class="floatRight pgProductOverview" id="btn_new_product"><?php echo $Text['btn_new_product']; ?></button>
						<!-- p class="providerOverview"><?php echo $Text['search_provider'];?>: <input id="search" class="ui-corner-all"/></p-->

		    		</div>
				</div><!-- end titlewrap -->
 
 
 
				 <!-- 
							PROVIDER LISTING
							
				 -->
				<div class="ui-widget pgProviderOverview">
					<div class="ui-widget-content ui-corner-all">
					<h4 class="ui-widget-header">&nbsp;</h4>
						<table id="tbl_providers" class="tblListingDefault" >
							<thead>
								<tr>
									<th>&nbsp;&nbsp;<input type="checkbox" id="toggleBulkActionsProviders" name="toggleBulk"/></th>
									<th><p class="textAlignCenter"><?php echo $Text['id'];?></p></th>
									<th><?php echo $Text['provider_name']; ?></th>						
									<th><?php echo $Text['phone_pl']; ?></th>
									<th><?php echo $Text['email']; ?></th>
									<th><?php echo $Text['active']; ?></th>
									<th><?php echo $Text['responsible_uf'];?></th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<tr class="clickable" providerId="{id}" >
									<td><input type="checkbox" name="bulkAction"/></td>
									<td><p class="textAlignRight">{id}</p></td>
									<td title="<?php echo $Text['click_to_list']; ?>">{name}</td>
									<td>{phone1} / {phone2}</p></td>
									<td>{email}</td>
									<td><p class="providerActiveStatus iconContainer">{active}</p></td>
									<td><?php echo $Text['uf_short'];?>{responsible_uf_id} {responsible_uf_name}</td>
									<td><a href="javascript:void(null)" class="btn_edit_provider"><?php echo $Text['edit']; ?></a> | <a href="javascript:void(null)" class="btn_del_provider"><?php echo $Text['btn_del']; ?></a></td>
								</tr>						
							</tbody>
							<tfoot>
								<tr>

								</tr>
							</tfoot>
						</table>
					</div>
				</div>		
				
				
				
				<!-- 
							PRODUCT LISTING
							
				 -->
				<div class="pgProductOverview ui-widget">
					<div class="ui-widget-content ui-corner-all">
						<h4 class="ui-widget-header"><span class="setProviderName"></span></h4>
						<table id="tbl_products" class="tblListingDefault">
							<thead>
								<tr>
									<th>&nbsp;<input type="checkbox" id="toggleBulkActionsProducts" name="toggleBulk"/></th>
									<th><?php echo $Text['id'];?></th>
									<th><?php echo $Text['name_item'];?></th>						
									<th><?php echo $Text['orderable_type']; ?></th>
									<th><p class="textAlignCenter"><?php echo $Text['active']; ?></p></th>
									<th><?php echo $Text['revtax_abbrev']; ?></th>
									<th><?php echo $Text['iva']; ?></th>
									<th><?php echo $Text['unit'];?></th>
									<th><?php echo $Text['price'];?></th>
									<th><?php echo $Text['stock'];?></th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<tr id="{id}" class="clickable" productId="{id}">
									<td><input type="checkbox" name="bulkAction"/></td>
									<td>{id}</td>
									<td title="<?php echo $Text['click_row_edit']; ?>">{name}</td>
									<td>{orderable_type_id}</td>
									<td><p class="textAlignCenter iconContainer">{active}</p></td>
									<td>{rev_tax_percent}%</td>
									<td>{iva_percent}%</td>
									<td>{unit}</td>	
									<td>{unit_price}</td>	
									<td><p class="formatQty">{stock_actual}</p></td>
									<td><a href="javascript:void(null)" class="btn_del_product"><?php echo $Text['btn_del'];?></a></td>
								</tr>						
							</tbody>
						</table>
					</div>
				</div>
				
				
				
				
				<!-- 
							PRODUCT EDIT
							
				 -->
				 <div class="pgProductEdit ui-widget" id="pgProductEdit">
					<div class="ui-widget-content ui-corner-all">
						<h4 class="ui-widget-header"><span class="setProviderName"></span> - <span class="setProductName"></span> </h4>
						<form id="frm_product_edit">
						<table id="tbl_product_edit" class="tblForms">
							  <tbody>
							  <tr productId="{id}" responsibleUfId="{responsible_uf_id}">
									<td><label for="product_id"><?php echo $Text['id']; ?></label></td>
									<td><p class="textAlignLeft ui-corner-all setProductId">{id}</p></td>
									<td><label for="active"><?php echo $Text['active'];?></label></td>
									<td><input type="checkbox" name="active" value="{active}" class="floatLeft" />
										<input type="hidden" name="id" value="{id}" />
									</td>							
							  </tr>
							  <tr>
							    <td><label for="name"><?php echo $Text['name_item']; ?></label></td>
							    <td><input type="text" name="name" value="{name}" tabindex="1" class="ui-widget-content ui-corner-all inputTxtLarge" /></td>
							 	<td></td>
							 	<td></td>
							   </tr>
							  <tr>
							    <td><label for="description"><?php echo $Text['description']; ?></label></td>
							    <td>
							      <textarea class="ui-widget-content ui-corner-all textareaLarge" name="description">{description}</textarea>
							 	</td>
							 	<td></td>
							 	<td></td>
							  </tr>
							  <tr>
							    <td><label for="description_url"><?php echo $Text['web']; ?></label></td>
							    <td colspan="3"><input type="text" name="description_url" value="{description_url}" class="inputTxtLarge ui-widget-content ui-corner-all" /></td>
							  </tr>
							  <tr>
							    <td><label for="barcode"><?php echo $Text['barcode']; ?></label></td>
							    <td colspan="3"><input type="text" name="barcode" value="{barcode}" class="ui-widget-content ui-corner-all" /></td>
							  </tr>
							  <tr>
								<td><label for="responsible_uf_id"> <?php echo $Text['responsible_uf']; ?></label></td>
								<td>
							    	<input type="hidden" name="responsible_uf_id" value="{responsible_uf_id}"/>
							    	<span class="textAlignLeft sResponsibleUfId"></span>
							    </td>
							  </tr>
							  <tr>
							    <td>&nbsp;</td>
							    <td colspan="3">&nbsp;</td>
							  </tr>
							  <tr>
							    <td><label for="orderable_type_id"><?php echo $Text['orderable_type']; ?></label></td>
							    <td colspan="3">
							    	<input type="hidden" name="orderable_type_id" value="{orderable_type_id}"/>
							    	<span class="textAlignLeft sOrderableTypeId"></span></td>
							  </tr>
							  <tr>
							    <td><label for="category_id"><?php echo $Text['category']; ?></label></td>
							    <td colspan="3">
							    	<input type="hidden" name="category_id" value="{category_id}"/>
							    	<span class="textAlignLeft sCategoryId"></span></td>
							  </tr>
							  <tr>
							    <td><label for="unit_measure_order_id"><?php echo $Text['unit_measure_order']; ?></label></td>
							    <td colspan="3">
							    	<input type="hidden" name="unit_measure_order_id" value="{unit_measure_order_id}"/>
							    	<span class="textAlignLeft sUnitMeasureOrderId"></span></td>
							  </tr>
							  <tr>
							    <td><label for="unit_measure_shop_id"><?php echo $Text['unit_measure_shop']; ?></label></td>
							    <td colspan="3">
							    	<input type="hidden" name="unit_measure_shop_id" value="{unit_measure_shop_id}"/>
							    	<span class="textAlignLeft sUnitMeasureShopId"></span></td>
							  </tr>
							  <tr>
							    <td><label for="order_min_quantity"><?php echo $Text['order_min']; ?></label></td>
							    <td colspan="3"><input type="text" name="order_min_quantity" value="{order_min_quantity}" class="ui-widget-content ui-corner-all" /></td>
							  </tr>
							   <tr>
							    <td>&nbsp;</td>
							    <td colspan="3">&nbsp;</td>
							  </tr>
							  
							
							  <tr>
							    <td><label for="unit_price"><?php echo $Text['unit_price']; ?></label></td>
							    <td><input type="text" name="unit_price" value="{unit_price}" class="ui-widget-content ui-corner-all" /></td>
							  </tr>
							  <tr>
							    <td><label for="iva_percent_id"><?php echo $Text['iva_percent']; ?></label></td>
							    <td>
							    	<input type="hidden" name="iva_percent_id" value="{iva_percent_id}"/>
							    	<span class="textAlignLeft sIvaPercentId"></span></td>
							  </tr>
							  <tr>
							  <td><label for="rev_tax_type_id"><?php echo $Text['rev_tax_type']; ?></label></td>
							    <td>
							    	<input type="hidden" name="rev_tax_type_id" value="{rev_tax_type_id}"/>
							    	<span class="textAlignLeft sRevTaxTypeId"></span></td>
							  </tr>
							  
							  
							  <tr>
							    <td>&nbsp;</td>
							    <td colspan="3">&nbsp;</td>
							  </tr>
							  <tr>
							    <td><label for="stock_min"><?php echo $Text['stock_min']; ?></label></td>
							    <td><input type="text" name="stock_min" value="{stock_min}" class="ui-widget-content ui-corner-all" /></td>
							    <td>&nbsp;</td>
							    <td>&nbsp;</td>
							  </tr>
							  
							  <tr>
							    <td>&nbsp;</td>
							    <td colspan="3">&nbsp;</td>
							  </tr>
							  </tbody>
							  <tfoot>
								<tr>
									<td colspan="4">
										<p class="floatRight">
											<button class="btn_cancel_product"><?php echo $Text['btn_cancel']; ?></button>
											&nbsp;&nbsp;
											<button class="btn_save_product edit"><?php echo $Text['btn_save'];?></button>
										</p>
									</td>
								</tr>
							</tfoot>
						</table>
						</form>
					</div>	
				</div>
				 
				<p>&nbsp;</p> 
				<!-- 
							PRODUCT NEW
							
				 -->
				 <div class="pgProductNew ui-widget" id="pgProductNew">
					<div class="ui-widget-content ui-corner-all">
						<h4 class="ui-widget-header"><span class="setProviderName"></span> - <span class="setProductName"></span> </h4>
						<form id="frm_product_new">
						<input type="hidden" name="provider_id" value=""/>
						<table id="tbl_product_new" class="tblForms">
							  <tbody>
							  </tbody>
							  <tfoot>
								<tr>
									<td colspan="2"></td>
									
									<td colspan="2">
										<p class="floatRight">
											<button class="btn_cancel_product"><?php echo $Text['btn_cancel']; ?></button>
											&nbsp;&nbsp;
											<button class="btn_save_product add"><?php echo $Text['btn_save'];?></button>
										</p>
									</td>
								</tr>
							</tfoot>
						</table>
						</form>
					</div>	
				</div>
				 
				<p>&nbsp;</p> 
				
				
				
				<!-- 
							PROVIDER EDIT
							
				 -->
				<div class="pgProviderEdit ui-widget" id="pgProviderEdit">
					<div class="ui-widget-content ui-corner-all">
						<h4 class="ui-widget-header"><span class="setProviderName"></span></h4>
						<form id="frm_provider_edit">
						<table id="tbl_provider_edit" class="tblForms">
						<tbody>
						<tr providerId="{id}" responsibleUfId="{responsible_uf_id}">
							<td><label for="provider_id"><?php echo $Text['id']; ?></label></td>
							<td><p class="textAlignLeft ui-corner-all setProviderId">{id}</p></td>
							<td><label for="active"><?php echo $Text['active'];?></label></td>
							<td><input type="checkbox" name="active" value="{active}" class="floatLeft" />
								<input type="hidden" name="id" value="{id}" />
							</td>							
						</tr>
						
						<tr>
							<td><label for="name"><?php echo $Text['name'];?></label></td>
							<td><input type="text" name="name"  value="{name}" class="inputTxtLarge ui-widget-content ui-corner-all" /></td>
							<td><label for="nif"><?php echo $Text['nif'];?></label></td>
							<td><input type="text" name="nif" value="{nif}" class="ui-widget-content ui-corner-all" /></td>
						</tr>
						<tr>
							<td><label for="contact"><?php echo $Text['contact'];?></label></td>
							<td><input type="text" name="contact"  value="{contact}" class="inputTxtLarge ui-widget-content ui-corner-all" /></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td><label for="address"><?php echo $Text['address'];?></label></td>
							<td colspan="5"><input type="text" name="address" value="{address}" class="inputTxtMax ui-widget-content ui-corner-all" /></td>
						</tr>
						<tr>
							<td><label for="city"><?php echo $Text['city'];?></label></td>
							<td><input type="text" name="city" value="{city}" class="ui-widget-content ui-corner-all" /></td>
							<td><label for="zip"><?php echo $Text['zip'];?></label></td>
							<td><input type="text" name="zip"  value="{zip}" class=" ui-widget-content ui-corner-all" /></td>
							
						</tr>
						<tr>
							<td><label for="phone1"><?php echo $Text['phone1'];?></label></td>
							<td><input type="text" name="phone1" value="{phone1}" class="ui-widget-content ui-corner-all" /></td>
						
							<td><label for="phone2"><?php echo $Text['phone2'];?></label></td>
							<td><input type="text" name="phone2" value="{phone2}" class="ui-widget-content ui-corner-all" /></td>
						</tr>
						<tr>
							<td><label for="email"><?php echo $Text['email'];?></label></td>
							<td colspan="5"><input type="text" name="email" value="{email}" class=" inputTxtLarge ui-widget-content ui-corner-all" /></td>
						</tr>
						<tr>
							<td><label for="web"><?php echo $Text['web'];?></label></td>
							<td colspan="5"><input type="text" name="web" value="{web}" class="inputTxtMax ui-widget-content ui-corner-all" /></td>
						</tr>
						<tr>
							<td><label for="notes"><?php echo $Text['notes'];?></label></td>
							<td colspan="5"><textarea class="ui-widget-content ui-corner-all textareaMax" id="notes" name="notes">{notes}</textarea></td>
						</tr>
						<tr>
							<td colspan="5">&nbsp;</td>
						</tr>
						
						<tr>
							<td><label for="responsible_uf_id">&nbsp; <?php echo $Text['responsible_uf']; ?></label></td>
							<td>
								<input type="hidden" name="responsible_uf_id" value="{responsible_uf_id}" />
								<span class="textAlignLeft sResponsibleUfId"></span>
							</td>
							<td></td>
							<td></td>						
						</tr>
						<tr>
							<td colspan="5">&nbsp;</td>
						</tr>
						<tr>
							<td><label for="bank_name"><?php echo $Text['bank_name'];?></label></td>
							<td colspan="5"><input type="text" name="bank_name"  value="{bank_name}" class="inputTxtLarge ui-widget-content ui-corner-all" /></td>
							
						</tr>
						<tr>
							<td><label for="bank_account"><?php echo $Text['bank_account'];?></label></td>
							<td colspan="5"><input type="text" name="bank_account"  value="{bank_account}" class="inputTxtLarge ui-widget-content ui-corner-all" /></td>
							
						</tr>
						<tr>
							<td colspan="5">&nbsp;</td>
						</tr>
						<tr>
							<td><label for="offset_order_close"><?php echo $Text['offset_order_close']; ?></label></td>
							<td><input type="text" name="offset_order_close"  value="{offset_order_close}" class="ui-widget-content ui-corner-all" /></td>
							<td></td>
							<td></td>
						</tr>
						</tbody>
						<tfoot>
						<tr>
							<td colspan="2"></td>
							
							<td colspan="2">
								<p class="floatRight">
									<button class="btn_cancel_provider"><?php echo $Text['btn_cancel']; ?></button>
									&nbsp;&nbsp;
									<button class="btn_save_provider edit"><?php echo $Text['btn_save'];?></button>
								</p>
							</td>
						</tr>
						</tfoot>
						</table>
					</form>
					<p>&nbsp;</p>
				</div>
			</div>
			
			
			<!-- 
							PROVIDER NEW
							
			-->
			<div class="pgProviderNew ui-widget hidden" id="pgProviderNew">
					<div class="ui-widget-content ui-corner-all">
						<h4 class="ui-widget-header"><span class="setProviderName"></span></h4>
						<form id="frm_provider_new">
						<table id="tbl_provider_new" class="tblForms">
							<tbody>
							
							</tbody>
							<tfoot>
							<tr>
								<td colspan="2"></td>
								<td colspan="2"><p class="floatRight">
										<button class="btn_cancel_provider" ><?php echo $Text['btn_cancel']; ?></button>
										&nbsp;&nbsp;
										<button class="btn_save_provider add"><?php echo $Text['btn_save'];?></button>
									</p>
								</td>
							</tr>
							</tfoot>
						</table>
					</form>


					<p>&nbsp;</p>
						
				
						
				</div>
			</div>
				
			
				
				
				

	</div>
	<!-- end of stage wrap -->
</div>
<!-- end of wrap -->


<!-- / END -->
</body>
</html>