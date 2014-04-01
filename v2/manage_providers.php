<?php include "php/inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " .$Text['head_ti_provider'];?></title>
	


    <link href="js/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/aixcss.css" rel="stylesheet">
    <link href="js/ladda/ladda-themeless.min.css" rel="stylesheet">



	<?php if (isset($_SESSION['dev']) && $_SESSION['dev'] == true ) { ?>
	    <script type="text/javascript" src="js/jquery/jquery.js"></script>
   	    <script type="text/javascript" src="js/bootstrap/js/bootstrap.min.js"></script>
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaXML2HTML.js" ></script>
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaSwitchSection.js" ></script>
	   	
	   	<script type="text/javascript" src="js/bootbox/bootbox.js"></script>
	   	<script type="text/javascript" src="js/ladda/spin.min.js"></script>
	   	<script type="text/javascript" src="js/ladda/ladda.min.js"></script	
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaUtilities.js" ></script>

    <?php }?>
   		
 		
 	
	<script type="text/javascript">

	function reloadWhat(){
		window.location.href = "manage_providers.php"; 
	}

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
								['aixada_rev_tax_type','sRevTaxTypeId', 'id', 'name', 'rev_tax_percent'],
								['aixada_iva_type','sIvaPercentId','id','name', 'percent'],
								['aixada_unit_measure','sUnitMeasureOrderId','id','name'],
								['aixada_unit_measure','sUnitMeasureShopId','id','name']
						];


		$('.section').hide();


		$('.change-sec').switchSection("init");

		$('.sec-1').show();
		

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
				$('p.providerActiveStatus').each(function(){
					if ($(this).text() == "1"){
						$(this).html('<span class="glyphicon glyphicon-ok"></span>').parent().addClass('bg-success')
					} else {
						$(this).html('<span class="glyphicon glyphicon-remove-sign"></span>').parent().addClass("bg-danger");
					}
				});
				$("#tbl_providers").trigger("update"); 
			}
		});

		/*$("#tbl_providers").tablesorter({
			textExtraction: function(node){
				  //should be made faster??	
		          if ($(node).find('.aix-style-ok-green').length == 1) {
		            return 1;
		          } else if ($(node).find('.noRed').length == 1){
					return 0; 
			      } else {
		            return $(node).text();
		          }
			}
		}); */
		
		/*$("#tbl_providers").bind('sortEnd', function(){
			$('tr',this).removeClass('rowHighlight')
			$('tr:even',this).addClass('rowHighlight');
		});*/

		

		//interactivity of provider listing table
		$('#tbl_providers tbody')
			.on("click", "tr", function(e){
				$('#tbl_providers tbody tr').removeClass('active');
				gSelProvider = $(this);
				gSelProvider.addClass('active');
				gProductListReload = true; 

				if (gSelProvider.attr('providerId') > 0) { 
						if (gProductListReload){
							$('#tbl_products tbody').xml2html("reload",{
								params: 'oper=getShopProducts&provider_id='+gSelProvider.attr('providerId')+"&all=1",
							});
						}

						$('.set-provider').html(gSelProvider.children().eq(2).text());
						gProductListReload = false; 
					}


				$('.change-sec').switchSection("changeTo",".sec-2");
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
		
		
		//import providers
		$('#btn_import_provider')
			.button({
				icons: {
					primary: "ui-icon-transferthick-e-w"
	        	}
			})
			.click(function(e){
				var myWin = window.open("manage_import.php?import2Table=aixada_provider", "aname", "height=600, width=950, toolbar=0, status=0, scrollbars=1, menubar=0, location=0");
				myWin.focus();
				
			});
		
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
		$('.edit-provider')
			.on('click', function(e){
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
						submitForm('#pgProviderNew', 'add', 'provider', 'overviewProvider');
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

	    // export products
		$('#btn_provider_export')
			.button({
				icons: {
					primary: "ui-icon-transferthick-e-w"
	        	}
			})
			.click(function(e){
				//anything selected? 
				if ($('input:checkbox[name="providerBulkAction"][checked="checked"]').length  == 0){
					$.showMsg({
						msg:"<?=$Text['msg_confirm_prov'];?>",
						buttons: {
							"<?=$Text['btn_ok'];?>":function(){						
								$('#dialog_export_options')
								.data('export', 'provider')
								.dialog("open");
								$(this).dialog("close");
							},
							"<?=$Text['btn_cancel'];?>":function(){						
								$(this).dialog("close");
							}
						},
						type: 'warning'});
				} else {
					$('#dialog_export_options')
						.data('export', 'provider')
						.dialog("open");
				}
			}); // end function
				

		//delete provider
		$('.del-provider')
			.on('click', function(e){
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
		 * de-/activate provider
		 */
		$('input[name=active_dummy_provider]').on("click", function(e){

			//if false, means deactivate product
			var status = $(this).is(":checked");
			var providerId = $(this).parents("tr").attr("providerId");

			//activate provider
			if (status){
				setActiveFlagProvider(providerId, status);

			//decativate provider
			} else {
				setActiveFlagProvider(providerId, status);
			}
		})

			
			
		$('#toggleProviderBulkActions')
			.click(function(e){
				if ($(this).is(':checked')){
					$('input:checkbox[name="providerBulkAction"]').attr('checked','checked');
				} else {
					$('input:checkbox[name="providerBulkAction"]').attr('checked',false);
				}
				e.stopPropagation();
			});

		//bulk actions
		$('input[name=providerBulkAction]')
			.on('click', function(e){
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


			isValidItem = $.checkSelect($(mi +' input[name=responsible_uf_id]'),['-1', '{responsible_uf_id}']);
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
			$('input:text, textarea', frm).val('');

			//assume that a new provider is active
			$('input:checkbox', frm).each(function(){
				$(this).attr('checked','checked');
			});

			//reset provider id. 
			$('.setProviderId', frm).html('&nbsp;');
			$('.set-provider').text('');
			
			
		}


		/**
		 * 	Deactivate provider 
		 */
		function setActiveFlagProvider(providerId, status){
			
			$('.loadSpinner').show();
			var oper = (status==1)? "activateProvider":"deactivateProvider";
			var successMsg = (status==1)? "<?php echo $Text['msg_activate_prov_ok']; ?>":"<?php echo $Text['msg_deactivate_prov_ok'] ?>";

			$.ajax({
			   	url: "php/ctrl/Providers.php?oper="+oper+"&provider_id="+providerId,
			   	type: 'POST',
			   	success: function(msg){
					$('input[name=active_dummy_provider]').attr('checked', status);
					$.showMsg({
						msg: successMsg,
						type: 'success',
						autoclose:1500});
			   	},
			   	error : function(XMLHttpRequest, textStatus, errorThrown){
					
						$this.dialog("close");
						$.showMsg({
								msg: XMLHttpRequest.responseText,
								type: 'error'});

				},
				complete : function(){
					$('.loadSpinner').hide();
					$('#tbl_providers tbody').xml2html("reload"); 
				}				   	
			}); //end ajax
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
					//$.formatQuantity(tds.eq(9));
					
				//orderable
				} else if (tds.eq(3).text() == "2"){
					tds.eq(3).text("<?=$Text['orderable'];?>");
					tds.eq(10).text(""); //delete stock info
					tds.eq(11).text("");
				}

				//active
				if (tds.eq(4).children('p:first').text() == "1"){
					tds.eq(4).children('p:first').html('<span class="glyphicon glyphicon-ok"></span>').parent().addClass('bg-success');
				} else {
					tds.eq(4).children('p:first').html('<span class="glyphicon glyphicon-remove-sign"></span>').parent().addClass('bg-danger');
				}

			},
			complete : function (rowCount){
				$('.loadSpinner').hide();
				
				if (gSelProduct != null && gSelProduct.attr('productId') > 0){
					gSelProduct.addClass('active');
				}
				$("#tbl_products").trigger("update"); 
		
				
			}						
		});			

		/*$("#tbl_products").tablesorter({
			textExtraction: function(node){
				  //should be made faster??	
		          if ($(node).find('.aix-style-ok-green').length == 1) {
		            return 1;
		          } else if ($(node).find('.noRed').length == 1){
					return 0; 
			      } else {
		            return $(node).text();
		          }
			}
		}); */

		//products listing behavior
		$('#tbl_products tbody')
			//click on table row
			.on("click", "tr", function(e){

				//if we come from product search
				if (gSelProvider == null) {
					var pvid = $(this).attr('providerId');
					gSelProvider = $('#tbl_providers tbody tr[providerId='+pvid+']');

				}
				
				$('#tbl_products tbody tr').removeClass('active');
				gSelProduct = $(this);
				gSelProduct.addClass('active');				

				$('#tbl_product_edit').xml2html('reload',{
					params: 'oper=getProductDetail&product_id='+gSelProduct.attr('productId')
				});

				if (gSelProduct.index() == 0) {
					$('#btn_prev_product').button('disable');
				}

				if (gSelProduct.index() == gSelProduct.parent().children().length -1) {
					$('#btn_next_product').button('disable');
				}

				$('#setProductPagination').html((gSelProduct.index()+1) + "/" + gSelProduct.parent().children().length+"&nbsp;&nbsp;")
				
				
				//switchTo('editProduct');

				$('.set-provider').html(gSelProvider.children().eq(2).text());
				$('.set-product').html(gSelProduct.children().eq(2).text());

				$('.change-sec').switchSection("changeTo",".sec-3");


				e.stopPropagation();
				
			});

		

		//load product for editing
		$('#tbl_product_edit').xml2html('init',{
			url : 'php/ctrl/ShopAndOrder.php',
			loadOnInit:false,
			rowComplete : function (rowIndex, row){
				setCheckBoxes('#frm_product_edit');
				populateSelect(gProductSelects,'#tbl_product_edit');

			}, complete : function(){
				//populate stock movement type select
				populateSelect([['aixada_stock_movement_type','sMovementTypeId','id','name']],'#frm_stock');
			}

		});


		/**
		 *	search products
		 */
		$("#search").keyup(function(e){
					var minLength = 3; 						//search with min of X characters
					var searchStr = $("#search").val();
					
					if (searchStr.length >= minLength){
						switchTo('searchProducts');
						
					  	$('#tbl_products tbody').xml2html("reload",{
							params: 'oper=getShopProducts&date=&like='+searchStr
						});
					} else {
						$('#tbl_products tbody').xml2html("removeAll");				//delete all product entries in the table if we are below minLength;
						switchTo('cancelSearch');
					}
			e.preventDefault();						//prevent default event propagation. once the list is build, just stop here.
		}); //end autocomplete


		//import produts
		$('#btn_import_products')
			.button({
				icons: {
					primary: "ui-icon-transferthick-e-w"
	        	}
			})
			.click(function(e){
				var myWin = window.open("manage_import.php?import2Table=aixada_product&providerName="+gSelProvider.children().eq(2).text()+"&providerId="+gSelProvider.attr('providerId'), "aname", "height=600, width=950, toolbar=0, status=0, scrollbars=1, menubar=0, location=0");
				myWin.focus();
				
			});

	    // export products
		$('#btn_product_export')
			.button({
				icons: {
					primary: "ui-icon-transferthick-e-w"
	        	}
			})
			.click(function(e){
				/**$('#dialog_export_options')
					.data("export", "product")
					.dialog("open");**/
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
			.on('click', function(e){


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
			.on('click', function(e){

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
							   		//$this.dialog( "close" ); 
							   	},
							   	error : function(XMLHttpRequest, textStatus, errorThrown){
									if (XMLHttpRequest.responseText.indexOf("ERROR 10") != -1){
										//$this.dialog("close");
										$.showMsg({
												msg: "<?=$Text['msg_err_del_product']; ?>" + XMLHttpRequest.responseText,
												type: 'error'});

									}
								   	
	
							   	}
							}); //end ajax
													
							
						},
						"<?=$Text['btn_cancel'];?>" : function(){
							//$( this ).dialog( "close" );
						}
					},
					type: 'confirm'});

				e.stopPropagation();
				
			})

			
		//jump to stock editing page
		/*$('.btn_edit_stock')
			.on("click", function(e){
					//var incidentId = $(this).parents('tr').attr('incidentId'); 
					window.location.href = 'manage_stock.php?lastPage=manage_stock.php&stockProvider='+gSelProvider.attr("providerId");
					e.stopPropagation();
			});	*/

		
		/**
		 * de-/activate product
		 * the active_dummy_product field is NOT passed when the form is edited; the de-/activation works 
		 * in parallel because it involves many dependencie
		 */
		$('input[name=active_dummy_product]').on("click", function(e){

			//if false, means deactivate product
			var status = $(this).is(":checked");
			var productId = $(this).parents("tr").attr("productid");
			
			//activate product
			if (status){
				setActiveFlagProduct(productId, true);

			//decativate product
			} else {

				//check if product has ordered items
				$.ajax({
					type: "POST",
					url: "php/ctrl/ActivateProducts.php?oper=count_ordered_items&product_id="+productId+"&order_status=0",
					success: function(xml){
						xmlDoc = $.parseXML(xml);
  						count = $(xmlDoc).find( "total_ordered_items" ).text();
						
						if (count > 0){
							$.showMsg({
								msg:"<?=$Text['msg_err_deactivate_product'];?>",
								buttons: {
									"<?=$Text['btn_ok_go']; ?>": function(){
										$( this ).dialog( "close" );
										releaseDatesAndProduct(productId);
									},
									"<?=$Text['btn_cancel'];?>":function(){
										$( this ).dialog( "close" );
									}
								},
								type: 'warning'});

						} else {
							//deactivate straight away
							setActiveFlagProduct(productId, false)
						}	
					},
					error : function(XMLHttpRequest, textStatus, errorThrown){
						$.showMsg({
							msg:XMLHttpRequest.responseText,
							type: 'error'});
					},
					complete : function(){
						//$('.loadSpinner').hide();
					}
				});

			}
		}); //deactivate product trigger

		
			
		//next product when on editing form
		$('#btn_next_product')
		.button({
				icons: {
	        		secondary: "ui-icon-triangle-1-e"
	        	},text:false
			})
			.click(function(e){			
				gSelProduct.next().trigger("click");
				$('#btn_prev_product').button('enable');	
				return false; 
		});

		$('#btn_prev_product')
		.button({
				icons: {
	        		primary: "ui-icon-triangle-1-w"
	        	},text:false
			})
			.click(function(e){			
				gSelProduct.prev().trigger("click");
				$('#btn_next_product').button('enable');	
				return false; 
		});

		
		//edit price, updates brutto field
		$('input[name=unit_price]')	
			.on("blur", function(e){
				var frm = $(this).parents('form');
				calcBruttoPrice(frm);
		})
		
		$('#toggleProductBulkActions')
			.click(function(e){
				if ($(this).is(':checked')){
					$('input:checkbox[name="productBulkAction"]').attr('checked','checked');
				} else {
					$('input:checkbox[name="productBulkAction"]').attr('checked',false);
				}
				e.stopPropagation();
			});

		//bulk actions
		$('input[name=productBulkAction]')
			.on('click', function(e){
				e.stopPropagation();
			})

		/********************************************************
		*  product util functions
		*********************************************************/


		/**
		 *  If item is orderable and has ordered_items, deactivating product requires to delete 
		 *  ordered items from order_cart for each uf and delete corresponding orderable dates.
		 */
		function releaseDatesAndProduct(pid){

			//removes all order items for this product from non-finalized orders. &date=0 means all open orders
			$.ajax({
			   	url: "php/ctrl/ActivateProducts.php?oper=unlockOrderableDate&product_id="+pid+"&date=0",
			   	type: 'POST',
			   	success: function(msg){
			   		//now set active flag
					setActiveFlagProduct(pid, false);
			   	},
			   	error : function(XMLHttpRequest, textStatus, errorThrown){
						$this.dialog("close");
						$.showMsg({
								msg: XMLHttpRequest.responseText,
								type: 'error'});

				}				   	
			}); //end ajax

		}

		/**
		 * 	Deactivates product. If stock simply sets active flag. If orderable also removes all associated
		 * 	orderable dates. This presumes that no order items are active anymore, i.e. for order items
		 * 	this needs to be called after releaseDatesAndProduct() has been called. 
		 */
		function setActiveFlagProduct(pid, status){
			
			$('.loadSpinner').show();
			var oper = (status==1)? "activateProduct":"deactivateProduct";

			var successMsg = (status==1)? "<?php echo $Text['msg_activate_prod_ok']; ?>":"<?php echo $Text['msg_deactivate_prod_ok']; ?>";

			$.ajax({
			   	url: "php/ctrl/ActivateProducts.php?oper="+oper+"&product_id="+pid,
			   	type: 'POST',
			   	success: function(msg){
					$('input[name=active_dummy_product]').attr('checked', status);
					$.showMsg({
						msg: successMsg,
						type: 'success',
						autoclose:1500});
			   	},
			   	error : function(XMLHttpRequest, textStatus, errorThrown){
					
						$this.dialog("close");
						$.showMsg({
								msg: XMLHttpRequest.responseText,
								type: 'error'});

				},
				complete : function(){
					$('.loadSpinner').hide();
					$('#tbl_products tbody').xml2html("reload");
				}				   	
			}); //end ajax
		}


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
					

			isValidItem = $.checkSelect($(mi +' input[name=responsible_uf_id]'),['-1', '{responsible_uf_id}']);
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


		//creates the new product form first time called; otherwise resets fields 
		function prepareProductForm(){
			var frm = $('#frm_product_new'); 
			//prepare the provider form the first time it is called
			if (gFirstTimeNewProduct){
				
				//copy the provider form 
				var tblStr = $('#tbl_product_edit').xml2html("getTemplate");
				
				$('#tbl_product_new tbody').append(tblStr);

				//construct the responsible uf select
				populateSelect(gProductSelects,'#tbl_product_new');

				//new providers have no id
				$('#tbl_product_new input[name=id]').remove();

				//clear all fields first time
				$('input:text, input:hidden, textarea', frm).val('');
				
				gFirstTimeNewProduct = false; 
			}

			//reset some textfields
			$('input[name=name], textarea[name=description], input[name=custom_product_ref], input[name=unit_price], input[name=barcode], input[name=description_url]', frm).val('');

			//clear unit_price paragraph
			$('.unit_price_brutto').text('');
			
			//assume that a new product is active
			$('input:checkbox', frm).each(function(){
				$(this).attr('checked','checked');
			});

			//reset provider id. 
			$('.setProductId', frm).html('&nbsp;');
			$('.set-product').text('');

			//nuevos productos se tienen que crear primero antes de introducir stock
			$('.btn_edit_stocks').button('disable');

			
			$('input[name=stock_actual]').val(0);			
			
			//set provider id
			$('#frm_product_new input[name=provider_id]').val(gSelProvider.attr('providerId'));

			//set responsible_uf same as provider. Doesn't work the first time when the form select gets constructed for the first time...
			var rufid = gSelProvider.attr('responsibleUfId');
			$('#tbl_product_new span.sResponsibleUfId').prev().val(rufid);
			$('#tbl_product_new span.sResponsibleUfId').children('select').val(rufid).attr('selected','selected');
			
			

						
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

						$('.set-provider').html(gSelProvider.children().eq(2).text());
					
						$('.pgProviderOverview, .pgProviderEdit, .pgProviderNew, .pgProductEdit, .pgProductNew').hide();
						$('.pgProductOverview').fadeIn(1000);
							
						gProductListReload = false; 
					}
					break;

				case 'searchProducts':
					$('.setProviderName').html("&nbsp;");
					$('.pgProviderOverview, .pgProviderEdit, .pgProviderNew, .pgProductEdit, .pgProductNew').hide();
					$('.pgProductOverview').show();
					$('#groupButtons').hide();
					break;

				case 'cancelSearch':
					$('.pgProductOverview, .pgProviderEdit, .pgProviderNew, .pgProductEdit, .pgProductNew').hide();
					$('.pgProviderOverview').show();
					$('#groupButtons').show();
					break;
					
				case 'editProvider':
					$('.set-provider').html(gSelProvider.children().eq(2).text());
					$('.pgProviderOverview, .pgProductOverview, .pgProviderNew, .pgProductEdit, .pgProductNew').hide();
					$('.pgProviderEdit').fadeIn(1000);
					
					break;

				case 'newProvider':
					$('.pgProviderOverview, .pgProductOverview, .pgProviderEdit, .pgProductEdit, .pgProductNew').hide();
					$('.pgProviderNew').fadeIn(1000);
					
					break;


				case 'editProduct':
					$('.set-provider').html(gSelProvider.children().eq(2).text());
					$('.set-product').html(gSelProduct.children().eq(2).text());
					
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
			/*$(mi +' input:checkbox').each(function(){
				var isChecked = $(this).attr('checked'); 
				
				if(isChecked){
					$(this).val(1);
				} else {
					$(this).val(0);
					$(mi + ' form').append('<input type="hidden" name="active" value="0"/>')
				}	
			});*/

			var cus = null;
			//custom_product_ref has unique index and needs null value
			if ($('input[name=custom_product_ref]').val() == '') {
				cus = $('input[name=custom_product_ref]').remove();
			}

			//serialize 
			var sdata = $(mi + ' form').serialize();

			//append again the custom_product_id input
			$(cus).appendTo('.customProdutRefHook');
			
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
						autoclose:800});
			   		
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
			   			$('.btn_edit_stocks').button('enable');
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


		//loads the options for select boxes. The whole story is  that the selects
		//delivered from the getFieldsOptions of the TableManger don't have the name attribute set
		//and they get added after the xml2html template was triggered. So we mirror the value with a simple 
		//hidden input field and the corresponding name
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
		

		
		function loadSelectHTML(urlStr, destination){
			$.post(urlStr, function(html){
				var selValue = $(destination).empty().append(html).prev().val(); 
				//new provider/product have no value, so we take the first option
				//this needs to be set manually, otherwise with a new form, no values get send
				if (selValue == ''){
					var selValue = $(destination).children('select:first').val();				
					$(destination).prev().attr('value',selValue);
				} else {
					$(destination).children('select').addClass('form-control').val(selValue).attr('selected','selected');
				}						
				
				if (destination.indexOf('sOrderableTypeId') > 0){// && !$('#btn_edit_stocks').is(':data(autocomplete)') ){
					 manageEditStockBtn();
				}

				if (destination.indexOf('sOrderableTypeId') > 0 && selValue == 1){
					$('.sec-2-stock').show();
				} else if (destination.indexOf('sOrderableTypeId') > 0 && selValue == 2) {
					$('.sec-2-stock').hide();
				}
				
			})	
		}


		function manageEditStockBtn(){
			//since the whole product form runs through the xml2html class
			//we need to init this stock edit button after it has 
			//been inserted into the dom. make sure this happens only once  
			
				$('.btn_edit_stocks')
				.button({
						icons: {
			        		primary: "ui-icon-pencil",
			        		secondary: "ui-icon-triangle-1-s"
			        	},text:true
					})

				/*.menu({
				content: $('#StockOptionsItems').html(),	
				showSpeed: 50, 
				width:280,
				flyOut: true, 
				itemSelected: function(item){
					var action = $(item).attr('id');
					var stockActual = gSelProduct.children().eq(10).text();
					var unit = gSelProduct.children().eq(9).text();
					switch (action){
					case 'correct':
						prepareStockForm('correct',stockActual,unit, gSelProduct.attr('productId'));  						
						break;
					case 'add':
						prepareStockForm('add',stockActual,unit, gSelProduct.attr('productId'));  						
						break;
					case 'consult':
						setTimeout(function(){window.location.href = 'manage_stock.php?lastPage=manage_stock.php&stockProvider='+gSelProvider.attr("providerId")},500);
						
						break;
					}
				}//end item selected 
			});*///end menu

		}



		/*$('#dialog_edit_stock').dialog({
			autoOpen:false,
			width:480,
			height:400,
			modal:true,
			buttons: {  
				"<?=$Text['btn_save'];?>" : function(){
					
						if ($(this).data('info').edit == "add"){
							addStock($(this).data('info').id);
						} else if ($(this).data('info').edit == "correct"){
							correctStock($(this).data('info').id);
						}
					},
			
				"<?=$Text['btn_cancel'];?>"	: function(){
					$( this ).dialog( "close" );
					} 
			}
		});*/


		

		
		
		$('#infoStockProductPage').show();
		$('#infoStockPage').hide();
			
		
		

		//for a given unit price, apply rev tax and iva and indicate the final price
		function calcBruttoPrice(frm){

			var price = $.checkNumber($('input[name="unit_price"]', frm),0.00, 2);
			$('input[name=unit_price]', frm).val(price);
			
			var revp = $('span.sIvaPercentId', frm).children('select').children('option:selected').attr('addInfo');
			var ivap = $('span.sRevTaxTypeId', frm).children('select').children('option:selected').attr('addInfo');
			
			var rev = new Number(revp);
			var iva = new Number(ivap);
			
			var price = price * (1 + iva/100) * (1 + rev/100); 

			$('.unit_price_brutto', frm).text(price.toFixed(2));
		}


		
		/**
		 * EXPORT selected providers
		 */
		//export options dialog
		/*$('#dialog_export_options').dialog({
			autoOpen:false,
			width:580,
			height:550,
			buttons: {  
				"<?=$Text['btn_ok'];?>" : function(){
						if ($(this).data('export') == "provider"){
							exportProviders();
						} else if ($(this).data('export') == "product") {
							exportProducts();
						}
					},
			
				"<?=$Text['btn_close'];?>"	: function(){
					$( this ).dialog( "close" );
					} 
			}
		});*/

		function checkExportForm(){
			var frmData = $('#frm_export_options').serialize();
			if (!$.checkFormLength($('input[name=exportName]'),1,150)){
				$.showMsg({
					msg:"File name cannot be empty!",
					type: 'error'});
				return false;
			}
			return frmData; 
		}

		 
		function exportProviders(){
			var frmData = checkExportForm();
			if (frmData){			
				var urlStr = "php/ctrl/ImportExport.php?oper=exportProviderInfo&"+frmData;
				$('input:checkbox[name="providerBulkAction"][checked="checked"]').each(function(){
					urlStr += "&providerId[]=" + $(this).parents('tr').attr('providerId');
				})
			
				//load the stuff through the export channel
				$('#exportChannel').attr('src',urlStr);
			}
		}


		/**
		 *	read export options and make the export call for products. 
		 */
		function exportProducts(){
			var frmData = checkExportForm();
			if (frmData){	
			
				var urlStr = "php/ctrl/ImportExport.php?oper=exportProducts&providerId=" + gSelProvider.attr('providerId') +"&" + frmData; 
			
				$('input:checkbox[name="productBulkAction"][checked="checked"]').each(function(){
					urlStr += "&productIds[]=" + $(this).parents('tr').attr('productId');
				})

				//load the stuff through the export channel
				$('#exportChannel').attr('src',urlStr);
			}	
		}






		
		
		//trick for setting the chosen option of the selects since generated selects don't have name!
		$('select')
			.on('change', function(){

				var selOption = $('option:selected',this).val(); 
				$(this).parent().prev().val(selOption);
								
				//update the brutto price if iva or rev tax is changed
				var which = $(this).parent().prev().attr('name');
				if (which == 'rev_tax_type_id' || which == 'iva_percent_id'){
					var frm = $(this).parents('form');
					calcBruttoPrice(frm);
				}	

				//show hide stock btn depending on orderable_type
				if (which == 'orderable_type_id' && selOption == 1){
					$('.sec-2-stock').fadeIn(500);
				} else if (which == 'orderable_type_id' && selOption == 2){
					$('.sec-2-stock').fadeOut(500);
				}
			})
			
		//remove all eventual error styles on input fields. 
		$('input')
			.on('focus', function(e){
				$(this).removeClass('ui-state-error');
			});
		
		$('input[name=bulkAction]')
			.on('click', function(e){
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


		<?php include('js/aixadautilities/stock.js.php'); ?> 
		
		 
							
	});  //close document ready
</script>

</head>
<body>

	<div id="headwrap">
		<?php include "php/inc/menu.inc.php" ?>
	</div>
	<!-- end of headwrap -->
	
	<div class="container">	
		<div class="row">
			<div class="col-md-6"></div>
			<div class="col-md-4 section sec-1 sec-2">
				<div class="input-group">
			      <input type="text" id="search" class="form-control" placeholder="<?=$Text['search_product'];?>">
			      <span class="input-group-btn">
			        <button class="btn btn-default" type="button"><span class="glyphicon glyphicon-search"></span> Search!</button>
			      </span>
				</div>
			</div>
			<div class="col-md-2 section sec-1 sec-2">
				<div class="btn-group pull-right">
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
	    				Actions <span class="caret"></span>
	  				</button>
					<ul class="dropdown-menu" role="menu">
					    <li class="section sec-1"><a href="#sec-3" class="change-sec"><?=$Text['btn_new_provider'];?></a></li>
					    <li class="section sec-2"><a href="#sec-4" class="change-sec"><?=$Text['btn_new_product'];?></a></li>
					    
					    <li><a href="" class=""><?=$Text['btn_import'] ; ?></a></li>
					</ul>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-10">
		    	<h1 class="section sec-1"> <?php echo $Text['head_ti_provider']; ?></h1>
				<h1 class="section sec-2"><span class="glyphicon glyphicon-chevron-left change-sec" target-section="#sec-1"></span> <span class="set-provider"></span></h1>
   				<h1 class="section sec-3"><span class="glyphicon glyphicon-chevron-left change-sec" target-section="#sec-1"></span> <span class="set-provider"></span> <span class="glyphicon glyphicon-chevron-left change-sec" target-section="#sec-2"></span> <span class="set-product"></span></h1>

		    	<!--h1 class="pgProviderEdit">&nbsp;&nbsp;<?php echo $Text['edit']; ?> - <span class="set-provider"></span></h1>
		    	<h1 class="pgProviderNew">&nbsp;&nbsp;<?php echo $Text['ti_create_provider'] ; ?></h1>
		    	<h1 class="pgProductEdit">&nbsp;&nbsp;<?php echo $Text['edit']; ?> - <span class="set-provider"></span> - <span class="set-product"></span></h1>
		    	<h1 class="pgProductNew">&nbsp;&nbsp;<span class="set-provider"></span> - <?php echo $Text['ti_add_product']; ?></h1-->
			</div>
			<div class="col-md-2 section sec-3">
				<p>&nbsp;</p>
				<button type="button" class="btn btn-default btn-sm" id="btn_prev_product"><?=$Text['previous'];?></button>
				<span id="setProductPagination">1/5</span> 
				<button type="button" class="btn btn-default btn-sm pull-right" id="btn_next_product"><?=$Text['next'];?></button>&nbsp;
			</div>
		</div>
	</div>

	<div class="container">
 
		 <!-- 
					PROVIDER LISTING 	sec-1		
					
		 -->
		<div class="section sec-1">
				<table id="tbl_providers" class="table table-hover table-condensed">
					<thead>
						<tr>
							<th>&nbsp;&nbsp;<input type="checkbox" id="toggleProviderBulkActions" name="toggleProviderBulk"/></th>
							<th class="clickable"><?php echo $Text['id'];?></th>
							<th class="clickable"><?php echo $Text['provider_name']; ?></th>						
							<th class="clickable"><?php echo $Text['phone_pl']; ?></th>
							<th class="clickable"><?php echo $Text['email']; ?></th>
							<th class="clickable"><?php echo $Text['active']; ?>&nbsp; </th>
							<th class="clickable"><?php echo $Text['responsible_uf'];?></th>
							<th class="ax-min-width">&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						<tr class="clickable" providerId="{id}" responsibleUfId="{responsible_uf_id}" >
							<td><input type="checkbox" name="providerBulkAction"/></td>
							<td>{id}</td>
							<td title="<?php echo $Text['click_to_list']; ?>">{name}</td>
							<td>{phone1} / {phone2}</p></td>
							<td>{email}</td>
							<td><p class="providerActiveStatus text-center">{active}</p></td>
							<td><?php echo $Text['uf_short'];?>{responsible_uf_id} {responsible_uf_name}</td>
							<td>
								<span class="glyphicon glyphicon-pencil edit-provider pull-left" title="<?=$Text['edit'];?>"></span>&nbsp;&nbsp;
								<span class="glyphicon glyphicon-remove-circle del-provider" title="<?=$Text['btn_del'];?>"></span>
								
								<!--a href="javascript:void(null)" class="btn_edit_provider"></a> | 
								<a href="javascript:void(null)" class="btn_del_provider"></a-->
							</td>
						</tr>						
					</tbody>
					<tfoot>
						<tr>

						</tr>
					</tfoot>
				</table>
		</div>		
				
					
		<!-- 
					PRODUCT LISTING		sec-2
		 -->
		<div class="section sec-2">
				<table id="tbl_products" class="table table-hover table-condensed">
					<thead>
						<tr>
							<th>&nbsp;<input type="checkbox" id="toggleProductBulkActions" name="toggleProductBulk"/></th>
							<th class="clickable"><?php echo $Text['id'];?></th>
							<th class="clickable"><?php echo $Text['name_item'];?></th>						
							<th class="clickable"><?php echo $Text['orderable_type']; ?></th>
							<th class="clickable"><?php echo $Text['active']; ?></th>
							<th class="clickable text-right"><?php echo $Text['price_net'];?></th>
							<th class="clickable"><?php echo $Text['revtax_abbrev']; ?></th>
							<th class="clickable"><?php echo $Text['iva']; ?></th>
							<th class="clickable text-right"><?php echo $Text['price'];?></th>
							<th class="clickable text-right"><?php echo $Text['unit'];?></th>
							<th class="text-right"><?php echo $Text['stock'];?></th>
							<th></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<tr id="{id}" class="clickable" productId="{id}" providerId="{provider_id}">
							<td><input type="checkbox" name="productBulkAction"/></td>
							<td>{id}</td>
							<td title="<?php echo $Text['click_row_edit']; ?>">{name}</td>
							<td>{orderable_type_id}</td>
							<td><p class="text-center">{active}</p></td>
							<td><p class="text-right">{unit_price_netto}</p> </td>
							<td><p class="text-center">{rev_tax_percent}%</p></td>
							<td><p class="text-center">{iva_percent}%</p></td>
							<td><p class="text-right">{unit_price} </p></td>
							<td><p class="text-right">{unit}</p></td>	
							<td>
								<p class="formatQty text-right">{stock_actual}</p>
							</td>
							<td>
							</td>
							<td><span class="glyphicon glyphicon-remove-sign" title="<?=$Text['btn_del'];?>"></td>
						</tr>						
					</tbody>
				</table>
		</div>
				
				
				
				
		<!-- 
					PRODUCT EDIT 		sec-3
					
		 --> 
		<p>&nbsp;</p>
		<div class="section sec-3" id="pgProductEdit">
			<form id="frm_product_edit" class="form-horizontal" role="form">
		
			<div id="tbl_product_edit">
			
				<div class="form-group">
						<label for="name" class="col-sm-2 control-label"><?=$Text['name_item'];?></label>
					<div class="col-sm-6">
						<input type="text" name="name" value="{name}" tabindex="1" class="form-control" placeholder="Name">
					</div>
				</div>

				<div class="form-group">
						<label for="description" class="col-sm-2 control-label"><?=$Text['description'];?></label>
					<div class="col-sm-6">
				      <textarea class="form-control" name="description">{description}</textarea>					</div>
				</div>

				<div class="form-group">
						<label for="description_url" class="col-sm-2 control-label"><?=$Text['web'];?></label>
					<div class="col-sm-6">
						<input type="text" name="description_url" value="{description_url}" class="form-control" placeholder="">
					</div>
				</div>
				
				<div class="form-group">
						<label for="custom_product_ref" class="col-sm-2 control-label"><?=$Text['custom_product_ref'];?></label>
					<div class="col-sm-4">
						<input type="text" name="custom_product_ref" value="{custom_product_ref}" class="form-control" placeholder="">
					</div>
				</div>

				<div class="form-group">
						<label for="barcode" class="col-sm-2 control-label"><?=$Text['barcode'];?></label>
					<div class="col-sm-4">
						<input type="text" name="barcode" value="{barcode}" class="form-control" placeholder="">
					</div>
				</div>
				<div class="form-group">
						<label for="responsible_uf_id" class="col-sm-2 control-label"><?=$Text['responsible_uf'];?></label>
					<div class="col-sm-4">
				    	<input type="hidden" name="responsible_uf_id" value="{responsible_uf_id}"/>
				    	<span class="sResponsibleUfId"></span>
					</div>
				</div>
				<div class="form-group">
						<label for="orderable_type_id" class="col-sm-2 control-label"><?=$Text['orderable_type'];?></label>
					<div class="col-sm-2">
				    	<input type="hidden" name="orderable_type_id" value="{orderable_type_id}"/>
				    	<span class="sOrderableTypeId"></span>
					</div>
				</div>

				<div class="form-group">
						<label for="category_id" class="col-sm-2 control-label"><?=$Text['category'];?></label>
					<div class="col-sm-2">
				    	<input type="hidden" name="category_id" value="{category_id}"/>
				    	<span class="sCategoryId"></span></td>
					</div>
				</div>

				<div class="form-group">
						<label for="unit_measure_order_id" class="col-sm-2 control-label"><?=$Text['unit_measure_order'];?></label>
					<div class="col-sm-2">
				    	<input type="hidden" name="unit_measure_order_id" value="{unit_measure_order_id}"/>
				    	<span class="sUnitMeasureOrderId"></span></td>
					</div>
				</div>
				<div class="form-group">
						<label for="unit_measure_shop_id" class="col-sm-2 control-label"><?=$Text['unit_measure_shop'];?></label>
					<div class="col-sm-2">
				    	<input type="hidden" name="unit_measure_shop_id" value="{unit_measure_shop_id}"/>
				    	<span class="sUnitMeasureShopId"></span></td>
					</div>
				</div>
				<div class="form-group">
						<label for="order_min_quantity" class="col-sm-2 control-label"><?=$Text['order_min'];?></label>
					<div class="col-sm-1">
				    	<input type="text" name="order_min_quantity" value="{order_min_quantity}" class="form-control"/>
					</div>
				</div>

				<p>&nbsp;</p>

				<div class="form-group">
						<label for="unit_price" class="col-sm-2 control-label"><?=$Text['price_net'];?></label>
					<div class="col-sm-1">
				    	<input type="text" name="unit_price" value="{unit_price_netto}" class="form-control"/>
					</div>
				</div>

				<div class="form-group">
						<label for="iva_percent_id" class="col-sm-2 control-label"><?=$Text['iva_percent'];?></label>
					<div class="col-sm-2">
				    	<input type="hidden" name="iva_percent_id" value="{iva_percent_id}"/>
				    	<span class="sIvaPercentId"></span></td>
					</div>
				</div>


				<div class="form-group">
						<label for="rev_tax_type_id" class="col-sm-2 control-label">+ <?=$Text['rev_tax_type'];?></label>
					<div class="col-sm-2">
				    	<input type="hidden" name="rev_tax_type_id" value="{rev_tax_type_id}"/>
				    	<span class="sRevTaxTypeId"></span></td>
					</div>
				</div>

				<div class="form-group">
						<label for="unit_price_brutto" class="col-sm-2 control-label"><?=$Text['unit_price'];?></label>
					<div class="col-sm-1">
				    	<input type="text" name="unit_price_brutto" value="{unit_price}" class="form-control" disabled/>
					</div>
				</div>

				<p>&nbsp;</p>

				<div class="form-group sec-2-stock">
						<label for="stock_actual" class="col-sm-2 control-label"><?=$Text['stock'];?></label>
					<div class="col-sm-1">
				    	<input type="text" name="stock_actual" value="{stock_actual}" class="form-control" disabled/>
					</div>
					<div class="col-sm-2">
						<div class="btn-group">
							<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
							    <?=$Text['btn_edit_stock'];?> <span class="caret"></span>
							</button>
							<ul class="dropdown-menu" role="menu">
								<li><a href="javascript:void(null)" id="add"><?php echo $Text['add_stock'];?></a></li>
								<li><a href="javascript:void(null)" id="correct"><?php echo $Text['correct_stock'];?></a></li>
								<li><a href="javascript:void(null)" id="consult"><?php echo $Text['consult_mov_stock'];?></a></li>
							</ul>
						</div>
					</div>
				</div>

				<div class="form-group sec-2-stock">
					<label for="stock_min" class="col-sm-2 control-label"><?=$Text['stock_min'];?></label>
					<div class="col-sm-1">
				    	<input type="text" name="stock_min" value="{stock_min}" class="form-control"/>
					</div>
				</div>

				
				<div class="form-group">
					<div class="col-sm-5"></div>
						<div class="cols-sm-1">
							<button type="reset" class="btn btn-default change-sec" target-section="#sec-2"><?php echo $Text['btn_cancel'];?></button>
							&nbsp;&nbsp;
							<button type="submit" id="save-btn" class="btn btn-primary ladda-button" data-style="slide-left" ><span class="ladda-label"><?php echo $Text['btn_save'];?></span></button>
						</div>
					</div>
				</div>	

			</form>
		</div>	
				 
		<p>&nbsp;</p> 

		<!-- 
					PRODUCT NEW 	sec-4
					
		 -->
		 <div class="secton sec-4" id="pgProductNew">
			<h3><span class="set-provider"></span> - <span class="set-product"></span></h3>
			<form id="frm_product_new">
			<input type="hidden" name="provider_id" value=""/>
			<table id="tbl_product_new" class="tblForms">
				<thead><tr><td colspan="4">&nbsp;</td></tr></thead>
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
				 
		<p>&nbsp;</p> 
				
				
				
		<!-- 
					PROVIDER EDIT 	sec-5
					
		 -->
		<div class="section sec-5" id="pgProviderEdit">
				<h3><span class="set-provider"></span>
				</h3>
				<form id="frm_provider_edit">
				<table id="tbl_provider_edit">
					<thead>
						<tr><td colspan="4">&nbsp;</td></tr>
					</thead>
				<tbody>
				<tr providerId="{id}" responsibleUfId="{responsible_uf_id}">
					<td><label for="provider_id"><?php echo $Text['id']; ?></label></td>
					<td><p class="textAlignLeft ui-corner-all setProviderId">{id}</p></td>
					<td><label for="active"><?php echo $Text['active'];?></label></td>
					<td><input type="checkbox" name="active_dummy_provider" value="{active}" class="floatLeft" />
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
		</div>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
			
			
		<!-- 
						PROVIDER NEW  	sec-6
						
		-->
		<div class="section sec-6" id="pgProviderNew">
			<h3><span class="set-provider"></span></h3>
			<form id="frm_provider_new">
				<table id="tbl_provider_new" class="tblForms">
				<thead><tr><td colspan="4">&nbsp;</td></tr></thead>
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
		</div>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
								
				

	</div>
	<!-- end of container wrap -->

<iframe id="exportChannel" src="" style="display:none; visibility:hidden;"></iframe>
<div id="dialog_export_options" title="<?php echo $Text['export_options']; ?>">
<?php include("tpl/export_dialog.php");?>
</div>
<div id="dialog_edit_stock">
<?php include('tpl/stock_dialog.php');?>
</div>
<!-- / END -->
</body>
</html>