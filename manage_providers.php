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
	   	<script type="text/javascript" src="js/ladda/ladda.min.js"></script>
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaUtilities.js"></script>
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaExport.js" ></script>

    <?php }?>
   		
 		
 	
	<script type="text/javascript">

	function reloadWhat(){
		window.location.href = "manage_providers.php"; 
	}

	$(function(){

		
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

		//result limit for stock movements
		var gStockMoveLimit = 100;
		

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

		//init animated save buttons
		Ladda.bind('.btn-save')

		//hide all sections
		$('.section').hide();

		//init switch sectino plugin
		$('.change-sec').switchSection("init");

		//show home section
		$('.sec-1').show();


		//init boostrap modal 
		bootbox.setDefaults({
			locale:"<?=$language;?>"
		})


		//init export section switch
		$('.section-modal').hide();

		$('.change-sec-modal').switchSection("init", {gSectionSel : '.section-modal'});
		


		/***********************************************************
		 *
		 *  CONSULT STOCK MOVEMENTS
		 *
		 ***********************************************************/


		$("#tbl_stock_movements tbody").xml2html("init", {
			url: 'php/ctrl/Shop.php',
			params : 'oper=stockMovements&limit=50',
			loadOnInit:true,
			rowComplete : function (rowIndex, row){
				$.formatQuantity(row);
			},
			complete : function(rowCount){
				
				//sumStockMovementsValue();
				
			}
		});



 		$("#tbl_products tbody")
 			.on("click", ".ctx-nav-stock-movements",function(){

				var params = 'oper=stockMovements&limit='+gStockMoveLimit; 

				/*if (gSelProvider != null && gSelProvider.attr('providerId') > 0 ){
					params += '&provider_id='+ gSelProvider.attr('providerId');
				}*/

				gSelProduct = $(this).parents("tr");

				if (gSelProduct != null &&  gSelProduct.attr('productId') > 0){
					params += '&product_id='+ gSelProduct.attr('productId'); 
				}

				$("#tbl_stock_movements tbody").xml2html("reload", {
							params :  params
				});

				$('.change-sec').switchSection("changeTo",".sec-7");
 			})


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
			complete : function(rowCount){
				$('p.providerActiveStatus').each(function(){
					if ($(this).text() == "1"){
						$(this).html('<span class="glyphicon glyphicon-ok"></span>').parent().addClass('bg-success')
					} else {
						$(this).html('<span class="glyphicon glyphicon-remove-sign"></span>').parent().addClass("bg-danger");
						//hide inactive if option is checked
						if ($('.ctx-nav-filter-active-provider').children(":first-child").hasClass("glyphicon")){
							$(this).parents("tr").hide();
						}
					}
				});


				//$("#tbl_providers").trigger("update"); 
			}
		});


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


		//click pencil of provider for editing
		$('#tbl_providers tbody')
			.on('click', '.btn-edit-provider', function(e){
				$('#tbl_providers tbody tr').removeClass('active');
				gSelProvider = $(this).parents('tr');
				gSelProvider.addClass('active');
				
				$('#tbl_provider_edit').xml2html('reload',{
					params: 'oper=getProviders&all=1&provider_id='+gSelProvider.attr('providerId')
				});
				
				$('.set-provider').html(gSelProvider.children().eq(2).text());

				$('.change-sec').switchSection("changeTo",".sec-5");

				e.stopPropagation();
			});


		//de-/activate provider from provider listing
		$('#tbl_providers tbody')
			.on('click', '.bg-success, .bg-danger', function(e){
				var status = !$(this).hasClass("bg-success")
				var providerId = $(this).parents("tr").attr("providerId")
				//deactivate warning message
				if (!status){
					bootbox.confirm({
						title  : "<?=$Text['ti_confirm'];?>",
						message: "<div class='alert alert-warning'><?=$Text['deactivate_provider'] ; ?></div>",
						callback : function(ok){
							if (ok){
								setActiveFlagProvider(providerId, status);
							} else {
								bootbox.hideAll();
							}	
						}
					});
				} else {
					setActiveFlagProvider(providerId, status);

				}
				e.stopPropagation();
			});



		//delete provider
		$('#tbl_providers tbody')
			.on('click', '.btn-del-provider', function(e){
				var providerId = $(this).parents('tr').attr('providerId');
				bootbox.confirm({
					title  : "<?=$Text['ti_confirm'];?>",
					message: "<div class='alert alert-danger'><?=$Text['msg_confirm_del_provider']; ?></div>",
					callback : function(ok){
						if (ok){
							$this = $(this);
							var urlStr = 'php/ctrl/TableManager.php?oper=del&table=aixada_provider&id='+providerId; 
							$.ajax({
							   	url: urlStr,
							   	type: 'POST',
							   	success: function(msg){
									//reload all members listing on overiew. 
							   		$('#tbl_providers tbody').xml2html('reload');
							   		bootbox.hideAll();
							   	},
							   	error : function(XMLHttpRequest, textStatus, errorThrown){
							   		bootbox.hideAll();
									if (XMLHttpRequest.responseText.indexOf("ERROR 10") != -1){
										bootbox.alert({
												title : "<?=$Text['ti_error_exec'];?>",
												message : "<div class='alert alert-danger'><?=$Text['msg_err_del_provider']; ?> <br><br> "+XMLHttpRequest.responseText+"</div>",
										});	
									}
							   	}
							}); //end ajax	
						} else {
							bootbox.hideAll();
						}	
					}
				});

				e.stopPropagation();
			})


		//load provider for editing
		$('#tbl_provider_edit').xml2html('init',{
			url : 'php/ctrl/Providers.php',
			loadOnInit:false,
			rowComplete : function (rowIndex, row){
				setCheckBoxes('#frm_provider_edit');
				populateSelect(gProviderSelects,'#tbl_provider_edit');
			}

		});
			



		//BUTTON AND CONTEXT MENU STUFF FOR PROVIDER
		
		//save edited provider
		$('#frm_provider_edit')
			.on("click", ".btn-save", function(e){	
				if (checkProviderForm('#pgProviderEdit')){
					submitForm('#pgProviderEdit', 'edit', 'provider');
				}		 
				e.stopPropagation();
				return false; 
			});

		//save new provider
		$('#frm_provider_new')
			.on("click", ".btn-save", function(e){	
				if (checkProviderForm('#pgProviderNew')){
					submitForm('#pgProviderNew', 'add', 'provider', '.sec-1');
				}
				e.stopPropagation();
				return false; 
			});


		//create new provider
		$('.ctx-nav-new-provider').click(function(){	
			prepareProviderForm();
		})
		
	
		//import provider
		$('.ctx-nav-import-provider').click(function(){	
			var myWin = window.open("manage_import.php?import2Table=aixada_provider", "aname", "height=600, width=950, toolbar=0, status=0, scrollbars=1, menubar=0, location=0");
				myWin.focus();
		})

		//filter all/active providers only
		$('.ctx-nav-filter-active-provider').click(function(){
			$(this).children(":first-child").hasClass("glyphicon")? $(this).children(":first-child").removeClass("glyphicon glyphicon-check"):$(this).children(":first-child").addClass("glyphicon glyphicon-check");
			$('#tbl_providers td.bg-danger').parent().toggle();
		})
		
	    //export providers
		$('.ctx-nav-export-provider').click(function(){	
				//anything selected? 
				if ($('#tbl_providers input:checked').length  == 0){
					bootbox.alert({
						message : "<div class='alert alert-warning ax-modal-tmargin'><?=$Text['sel_prov_export'] ;?></div>",
					});	
				} else {
					bootbox.confirm({
						title : '<?=$Text['export_options']; ?>',
						message : '<div id="modalExportDialog"></div>',
						callback : function(ok){
							if (ok){
								exportProviders();
							} else {
								bootbox.hideAll();
							}
						}
					});

					$("#exportDialog")	//copy the export dialog from the template into the modal
						.clone(true)
						.removeClass("hidden")
						.appendTo( "#modalExportDialog" );
					$('.change-sec-modal').switchSection("changeTo",".sec-provider");
				}
			}); // end function
			


		//de-/activate provider
		$('#pgProviderEdit').on("click", "input[name=active_dummy_provider]", function(e){

			//if false, means deactivate product
			var status = $(this).is(":checked");
			var providerId = $(this).next().val();

			setActiveFlagProvider(providerId, status);
		})

			
		//de/select all providers
		$('#toggleProviderBulkActions')
			.click(function(e){
				if ($(this).is(':checked')){
					$('input:checkbox[name="providerBulkAction"]').attr('checked','checked');
				} else {
					$('input:checkbox[name="providerBulkAction"]').attr('checked',false);
				}
				e.stopPropagation();
			});


		//prevent page change when checkbox provider
		$('#tbl_providers tbody')
			.on('click','td:first-child, input[name=providerBulkAction]',  function(e){
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
				bootbox.alert({
						title : "<?=$Text['ti_error_form'];?>",
						message : "<div class='alert alert-danger'>"+err_msg+"</div>",
				});	
				Ladda.stopAll();
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
				var tblStr = $('#tbl_provider_edit').xml2html("getTemplate");
				$('#tbl_provider_new').append(tblStr);

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
			var oper = (status==1)? "activateProvider":"deactivateProvider";
			var successMsg = (status==1)? "<?php echo $Text['msg_activate_prov_ok']; ?>":"<?php echo $Text['msg_deactivate_prov_ok'] ?>";

			$.ajax({
			   	url: "php/ctrl/Providers.php?oper="+oper+"&provider_id="+providerId,
			   	type: 'POST',
			   	success: function(msg){
					$('input[name=active_dummy_provider]').attr('checked', status);
					bootbox.alert({
						message : "<div class='alert alert-success ax-modal-tmargin'>"+successMsg+"</div>",
					});	
					$('.modal-dialog').addClass('ax-modal-dialog-small');
					setTimeout(function(){
						bootbox.hideAll();
					},2000)
			   	},
			   	error : function(XMLHttpRequest, textStatus, errorThrown){
					
						bootbox.hideAll();
						bootbox.alert({
						title : "<?=$Text['ti_error_exec'];?>",
						message : "<div class='alert alert-danger'>"+XMLHttpRequest.responseText+"</div>"})
				},
				complete : function(){
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
				if (gSelProduct != null && gSelProduct.attr('productId') > 0){
					gSelProduct.addClass('active');
				}
				//$("#tbl_products").trigger("update"); 
			}						
		});			


		//edit/view product
		$('#tbl_products tbody')
			.on("click", ".btn-edit-product", function(e){

				//if we come from product search
				if (gSelProvider == null) {
					var pvid = $(this).parents('tr').attr('providerId');
					gSelProvider = $('#tbl_providers tbody tr[providerId='+pvid+']');

				}
				
				$('#tbl_products tbody tr').removeClass('active');
				gSelProduct = $(this).parents('tr');
				gSelProduct.addClass('active');				

				$('#tbl_product_edit').xml2html('reload',{
					params: 'oper=getProductDetail&product_id='+gSelProduct.attr('productId')
				});

				if (gSelProduct.index() == 0) {
					$('#btn_prev_product').attr("disabled","disabled");
				}

				if (gSelProduct.index() == gSelProduct.parent().children().length -1) {
					$('#btn_next_product').attr("disabled","disabled");
				}

				$('#setProductPagination').html((gSelProduct.index()+1) + "/" + gSelProduct.parent().children().length+"&nbsp;&nbsp;")
				
	
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
					var curSel = $('.change-sec').switchSection("getCurrentSel");

					if (searchStr.length >= minLength){
						if(curSel != ".sec-2-search-product"){
							$('.change-sec').switchSection("changeTo",".sec-2-search-product");
						}
						
					  	$('#tbl_products tbody').xml2html("reload",{
							params: 'oper=getShopProducts&date=&like='+searchStr
						});
					} else {
						$('#tbl_products tbody').xml2html("removeAll");				//delete all product entries in the table if we are below minLength;
						if(curSel != ".sec-1"){
							$('.change-sec').switchSection("changeTo",".sec-1");
						}
					}
			e.preventDefault();						//prevent default event propagation. once the list is build, just stop here.
		}); //end autocomplete


		//import products
		$('.ctx-nav-import-product')
			.click(function(e){
				var myWin = window.open("manage_import.php?import2Table=aixada_product&providerName="+gSelProvider.children().eq(2).text()+"&providerId="+gSelProvider.attr('providerId'), "aname", "height=600, width=950, toolbar=0, status=0, scrollbars=1, menubar=0, location=0");
				myWin.focus();
				
			});

	    // export products
		$('.ctx-nav-export-product')
			.click(function(e){
				
			 }); 
			    

		//product buttons
		$('.ctx-nav-new-product')
			.click(function(){
				prepareProductForm();			
			});


		//save edited product
		$('#frm_product_edit')
			.on("click", ".btn-save", function(e){	
				if (checkProductForm('#pgProductEdit')){
					submitForm('#pgProductEdit', 'edit', 'product');
				}		 	 
				e.stopPropagation();
				return false; 
			});

		//save new product
		$('#frm_product_new')
			.on("click", ".btn-save", function(e){	
				if (checkProductForm('#pgProductNew')){
					submitForm('#pgProductNew', 'add', 'product', '.sec-2');
				}		 	 
				e.stopPropagation();
				return false; 
			});



		//delete prodcut
		$('#tbl_products tbody')
			.on('click', '.btn-del-product',  function(e){

				var productId = $(this).parents('tr').attr('productId');

				bootbox.confirm({
				title  : "<?=$Text['ti_confirm'];?>",
				message: "<div class='alert alert-danger'><?=$Text['msg_confirm_del_product']; ?></div>",
				callback : function(ok){
					if (ok){
						$this = $(this);
						var urlStr = 'php/ctrl/TableManager.php?oper=del&table=aixada_product&id='+productId; 

						$.ajax({
						   	url: urlStr,
						   	type: 'POST',
						   	success: function(msg){
								//reload all members listing on overiew. 
						   		$('#tbl_products tbody').xml2html('reload');
						   		bootbox.hideAll();
						   	},
						   	error : function(XMLHttpRequest, textStatus, errorThrown){
						   		bootbox.hideAll();

								if (XMLHttpRequest.responseText.indexOf("ERROR 10") != -1){
									bootbox.alert({
											title : "<?=$Text['ti_error_exec'];?>",
											message : "<div class='alert alert-danger'><?=$Text['msg_err_del_product']; ?> <br><br> "+XMLHttpRequest.responseText+"</div>",
									});	
								}
						   	}
						}); //end ajax	
					} else {
						bootbox.hideAll();
					}	
				}
			});

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
			.click(function(e){			
				gSelProduct.next().trigger("click");
				$('#btn_prev_product').removeAttr("disabled");	
				return false; 
		});

		$('#btn_prev_product')
			.click(function(e){			
				gSelProduct.prev().trigger("click");
				$('#btn_next_product').removeAttr("disabled");
				return false; 
		});

		
		//edit price, updates brutto field
		$('#pgProductEdit, #pgProductNew')
			.on("blur", "input[name=unit_price]", function(){
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
					bootbox.alert({
						message : "<div class='alert alert-success ax-modal-tmargin'>"+successMsg+"</div>",
					});	
					$('.modal-dialog').addClass('ax-modal-dialog-small');
					setTimeout(function(){
						bootbox.hideAll();
					},1000)
					
			   	},
			   	error : function(XMLHttpRequest, textStatus, errorThrown){
						bootbox.hideAll();
						bootbox.alert({
						title : "<?=$Text['ti_error_exec'];?>",
						message : "<div class='alert alert-danger'>"+XMLHttpRequest.responseText+"</div>"})
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
				bootbox.alert({
						title : "<?=$Text['ti_error_form'];?>",
						message : "<div class='alert alert-danger'>"+err_msg+"</div>",
				});	
				Ladda.stopAll();
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
				$('#tbl_product_new').append(tblStr);

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
			

		}



		/**
		 *	submits the create/edit provider data
		 *  mi: is the selector string of the layer that contains the whole member form and info
		 * 	assume form has been checked		 
		 */
		function submitForm(mi, action, table, returnTo){


			var cus = null;
			//custom_product_ref has unique index and needs null value
			if ($('input[name=custom_product_ref]').val() == '') {
				cus = $('input[name=custom_product_ref]').remove();
			}

			//serialize 
			var sdata = $(mi + ' form').serialize();

			//append again the custom_product_id input
			$(cus).appendTo('.customProdutRefHook');

			$('.btn_edit_stocks').attr("disabled","disabled");
			

			$.ajax({
			   	url: 'php/ctrl/TableManager.php?oper='+action+'&table=aixada_'+table,
			   	method: 'POST',
				data: sdata, 
			   	success: function(msg){
					bootbox.alert({
						message : "<div class='alert alert-success ax-modal-tmargin'><?=$Text['msg_edit_success']; ?></div>",
					});	
					$('.modal-dialog').addClass('ax-modal-dialog-small')

					setTimeout(function(){
						bootbox.hideAll()
					},1000)
			   	},
			   	error : function(XMLHttpRequest, textStatus, errorThrown){
			   		bootbox.alert("An error has occured: " + XMLHttpRequest.responseText);

			   	},
			   	complete : function(msg){
			   		Ladda.stopAll();

			   		if (table == 'provider'){
				   		$('#tbl_providers tbody').xml2html("reload"); 

			   		} else if (table == 'product'){
			   			
						$('.btn_edit_stocks').removeAttr("disabled");
			
						
						$('#tbl_products tbody').xml2html("reload",{
							params: 'oper=getShopProducts&provider_id='+gSelProvider.attr('providerId')+"&all=1",
						});
						
						$('.set-provider').html(gSelProvider.children().eq(2).text());
				   	}

				   	if (undefined != returnTo){
				   		//alert(returnTo)
						$('.change-sec').switchSection("changeTo",returnTo);
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

			$('input[name=unit_price_brutto]', frm).val(price.toFixed(2));
		}



		function exportProviders(){
			var frmData = checkExportForm();
			if (frmData){			
				var urlStr = "php/ctrl/ImportExport.php?oper=exportProviderInfo&"+frmData;
				$('#tbl_providers input:checked').each(function(){
					urlStr += "&providerId[]=" + $(this).parents('tr').attr('providerId');
				})
				//load the stuff through the export channel
				$('#exportChannel').attr('src',urlStr);
			}
		}

		function checkExportForm(){
			var frmData = $('#modalExportDialog #frm_export_options').serialize();
			if (!$.checkFormLength($('#modalExportDialog input[name=exportName]'),1,150)){
				alert("<?=$Text['msg_err_file']; ?>");
				return false;
			}
			return frmData; 
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
		$('#pgProviderEdit, #pgProviderNew, #pgProductEdit, #pgProductNew')
			.on('change', 'select', function(e){
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
		$('body')
			.on('focus', 'input', function(e){
				$(this).removeClass('ax-has-error');
			});
		
		$('input[name=bulkAction]')
			.on('click', function(e){
				e.stopPropagation();
			})		



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
			<div class="col-md-4 section sec-1 sec-2 sec-2-search-product">
				<div class="input-group">
			    	<input type="text" id="search" class="form-control" placeholder="<?=$Text['search_product'];?>">
			      	<span class="input-group-btn">
			        	<button class="btn btn-default" type="button"><span class="glyphicon glyphicon-search"></span>&nbsp;</button>
			      	</span>
				</div>
			</div>
			<div class="col-md-1 section sec-1 sec-2 sec-2-search-product">
				<div class="btn-group">
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
	    				Actions <span class="caret"></span>
	  				</button>
					<ul class="dropdown-menu" role="menu">
						<li class="section sec-1"><a href="#sec-6" class="change-sec ctx-nav ctx-nav-new-provider"><span class="glyphicon glyphicon-plus-sign"></span> <?=$Text['btn_new_provider'];?></a></li>
						<li class="section sec-1"><a href="javascript:void(null)" class="ctx-nav ctx-nav-import-provider"><span class="glyphicon glyphicon-import"></span> <?=$Text['btn_import'];?></a></li>
						<li class="section sec-1"><a href="javascript:void(null)" class="ctx-nav ctx-nav-export-provider"><span class="glyphicon glyphicon-export"></span> <?=$Text['btn_export'];?></a></li>

						<li class="section sec-2"><a href="#sec-4" class="change-sec ctx-nav ctx-nav-new-product"><span class="glyphicon glyphicon-plus-sign"></span> <?=$Text['btn_new_product'];?></a></li>
					   	<li class="section sec-2"><a href="javascript:void(null)" class="ctx-nav ctx-nav-import-product"><span class="glyphicon glyphicon-import"></span> <?=$Text['btn_import'];?></a></li>
						<li class="section sec-2"><a href="javascript:void(null)" class="ctx-nav ctx-nav-export-product"><span class="glyphicon glyphicon-export"></span> <?=$Text['btn_export'];?></a></li>
 						
					</ul>
				</div>
			</div>
			<div class="col-md-1 section sec-1 sec-2 sec-2-search-product">
				<div class="btn-group pull-right">
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" title="<?=$Text['btn_filter'];?>">
						<span class="glyphicon glyphicon-filter"></span> <span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu">
					    <li class="section sec-1"><a href="javascript:void(null)" class="ctx-nav ctx-nav-filter-active-provider"><span class=""></span> <?=$Text['active_providers'];?></a></li>
					    <li class="section sec-2"><a href="javascript:void(null)" class="ctx-nav ctx-nav-filter-active-product"><span class=""></span> <?=$Text['active_products'];?></a></li>
					</ul>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-9">
		    	<h1 class="section sec-1"> <?php echo $Text['head_ti_provider']; ?></h1>
				<h1 class="section sec-2"><span class="glyphicon glyphicon-chevron-left change-sec" target-section="#sec-1"></span> <span class="set-provider"></span></h1>
				<h1 class="section sec-2-search-product"><span class="glyphicon glyphicon-chevron-left change-sec" target-section="#sec-1"></span> <?=$Text['ti_search_results'];?></h1>
   				<h1 class="section sec-3"><span class="glyphicon glyphicon-chevron-left change-sec" target-section="#sec-1"></span> <span class="set-provider"></span> <span class="glyphicon glyphicon-chevron-left change-sec" target-section="#sec-2"></span> <span class="set-product"></span></h1>
				<h1 class="section sec-4"><span class="glyphicon glyphicon-chevron-left change-sec" target-section="#sec-1"></span> <span class="set-provider"></span> <span class="glyphicon glyphicon-chevron-left change-sec" target-section="#sec-2"></span> <?=$Text['ti_create_product']; ?> </h1>
				<h1 class="section sec-5"><span class="glyphicon glyphicon-chevron-left change-sec" target-section="#sec-1"></span> <?=$Text['edit'] ; ?> <span class="set-provider"></span></h1>
				<h1 class="section sec-6"><span class="glyphicon glyphicon-chevron-left change-sec" target-section="#sec-1"></span> <?=$Text['ti_create_provider']  ; ?> <span class="set-provider"></span></h1>
				<h1 class="section sec-7"><span class="glyphicon glyphicon-chevron-left change-sec" target-section="#sec-1"></span> <?=$Text['ti_mgn_stock_mov'];?> :: <span class="set-provider"></span> :: <span class="set-product"></span> </h1>

			</div>
			<div class="col-md-3 section sec-3">
				<p>&nbsp;</p>
				<button type="button" class="btn btn-default btn-sm pull-right" id="btn_next_product"><?=$Text['next'];?> <span class="glyphicon glyphicon-chevron-right"></span></button>&nbsp;				
				<span id="setProductPagination" class="pull-right" style="margin-left:10px;">1/5</span>
				<button type="button" class="btn btn-default btn-sm pull-right" id="btn_prev_product"><span class="glyphicon glyphicon-chevron-left"></span> <?=$Text['previous'];?></button>
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
								<span class="glyphicon glyphicon-pencil btn-edit-provider pull-left" title="<?=$Text['edit'];?>"></span>&nbsp;&nbsp;
								<span id="btn-del-provider" class="glyphicon glyphicon-remove-circle btn-del-provider" title="<?=$Text['btn_del'];?>"></span>
								
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
		<div class="section sec-2 sec-2-search-product">
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
						<tr id="{id}" productId="{id}" providerId="{provider_id}">
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
								<div class="pull-right">
									<p class="formatQty badge">{stock_actual}</p>
									<div class="btn-group">
										<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" title="<?=$Text['btn_stock'];?>">
											<span class="glyphicon glyphicon-cog"></span> <span class="caret"></span>
										</button>
										<ul class="dropdown-menu" role="menu">
											<li><a href="javascript:void(null)" class="ctx-nav ctx-nav-stock-movements"> <?=$Text['consult_mov_stock'];?></a></li>
	 										<li><a href="javascript:void(null)" class="ctx-nav ctx-nav-stock-add" ><?php echo $Text['add_stock'];?></a></li>
											<li><a href="javascript:void(null)" class="ctx-nav ctx-nav-stock-correct" ><?php echo $Text['correct_stock'];?></a></li>
										</ul>
									</div>
								</div>
							</td>
							<td>
							</td>
							<td>
								<span class="glyphicon glyphicon-pencil btn-edit-product pull-left clickable" title="<?=$Text['edit'];?>"></span>&nbsp;&nbsp;
								<span class="glyphicon glyphicon-remove-sign btn-del-product clickable" title="<?=$Text['btn_del'];?>"></td>
						</tr>						
					</tbody>
				</table>
		</div>


		<!-- 
					PROVIDER STOCK MOVEMENT LISTING		sec-7
		 -->
		<div class="section sec-7">
			<table id="tbl_stock_movements" class="table table-hover table-condensed">
				<thead>
					<tr>
						<th>id</th>
						<th>Name</th>
						<th><?php echo $Text['operator'] ; ?></th>
						<th><?php echo $Text['stock_mov_type']; ?></th>
						<th><?php echo $Text['comment'] ; ?></th>
						<th><?php echo $Text['date']; ?></th>
						<th><?php echo $Text['dff_qty']; ?></th>
						<th><?php echo $Text['dff_price']; ?></th>
						<th><?php echo $Text['balance']; ?></th>
						<th>Unit</th>
					</tr>
					
				</thead>
				<tbody>
					<tr productId="{product_id}">
						<td>{product_id}</td>
						<td>{product_name}</td>
						<td>{member_name}</td>
						<td>{movement_type}</td>
						<td>{description}</td>
						<td class="stockDeltaTSCell">{ts}</td>
						<td class="stockDeltaQtyCell"><p class="textAlignRight formatQty">{amount_difference}</p></td>
						<td><p class="text-right formatQty stockDeltaPriceCell">{delta_price}</p></td>
						<td><p class="text-right formatQty">{resulting_amount}</p></td>
						<td>{unit}</td>
					</tr>
				</tbody>
			</table>
		</div>
				


				
		<!-- 
					PRODUCT EDIT 		sec-3
					
		 --> 
	
		<div class="section sec-3" id="pgProductEdit">
			<form id="frm_product_edit" class="form-horizontal" role="form">
		
				<div id="tbl_product_edit"><!-- start form template -->
				


					<div class="form-group">
						<label for="id" class="col-sm-2 control-label"><?=$Text['id'];?></label>
						<div class="col-sm-1">
							<input type="text" name="id" value="{id}" class="form-control" disabled>
						</div>
						<label for="active_dummy_product" class="col-sm-2 control-label"><?=$Text['active'];?></label>
						<div class="col-sm-1">
							<input type="checkbox" name="active_dummy_product" value="{active}" tabindex="1" class="form-control">
							<input type="hidden" name="id" value="{id}" />
						</div>
					</div>

					
					<div class="form-group">
						<label for="name" class="col-sm-2 control-label"><?=$Text['name_item'];?></label>
						<div class="col-sm-6">
							<input type="text" name="name" value="{name}" tabindex="1" class="form-control" placeholder="Name">
						</div>
					</div>

					<div class="form-group">
						<label for="description" class="col-sm-2 control-label"><?=$Text['description'];?></label>
						<div class="col-sm-6">
					      <textarea class="form-control" name="description">{description}</textarea>
					     </div>
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
					    	<span class="sCategoryId"></span>
						</div>
					</div>

					<div class="form-group">
						<label for="unit_measure_order_id" class="col-sm-2 control-label"><?=$Text['unit_measure_order'];?></label>
						<div class="col-sm-2">
					    	<input type="hidden" name="unit_measure_order_id" value="{unit_measure_order_id}"/>
					    	<span class="sUnitMeasureOrderId"></span>
						</div>
					</div>

					<div class="form-group">
						<label for="unit_measure_shop_id" class="col-sm-2 control-label"><?=$Text['unit_measure_shop'];?></label>
						<div class="col-sm-2">
					    	<input type="hidden" name="unit_measure_shop_id" value="{unit_measure_shop_id}"/>
					    	<span class="sUnitMeasureShopId"></span>
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
					    	<span class="sIvaPercentId"></span>
						</div>
					</div>


					<div class="form-group">
						<label for="rev_tax_type_id" class="col-sm-2 control-label">+ <?=$Text['rev_tax_type'];?></label>
						<div class="col-sm-2">
					    	<input type="hidden" name="rev_tax_type_id" value="{rev_tax_type_id}"/>
					    	<span class="sRevTaxTypeId"></span>
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
				</div><!-- end template form string -->
				<div class="form-group">
					<div class="col-sm-5"></div>
					<div class="cols-sm-3">
						<button type="reset" class="btn btn-default change-sec" target-section="#sec-2"><?php echo $Text['btn_cancel'];?></button>
						&nbsp;&nbsp;
						<button class="btn btn-primary btn-save ladda-button" data-style="expand-right" ><span class="glyphicon glyphicon-save"></span> <span class="ladda-label"> <?php echo $Text['btn_save'];?></span></button>
					</div>
				</div>
			</form>
		</div>	
				 
		
		<!-- 
					PRODUCT NEW 	sec-4	
		 -->
		<div class="section sec-4" id="pgProductNew">
			<form id="frm_product_new"  class="form-horizontal" role="form">
				<input type="hidden" name="provider_id" value=""/>
				<div id="tbl_product_new">
		
				</div>
				<div class="form-group">
					<div class="col-sm-5"></div>
					<div class="cols-sm-3">
						<button type="reset" class="btn btn-default change-sec" target-section="#sec-2"><?php echo $Text['btn_cancel'];?></button>
						&nbsp;&nbsp;
						<button class="btn btn-primary btn-save ladda-button" data-style="expand-right" ><span class="glyphicon glyphicon-save"></span> <span class="ladda-label"> <?php echo $Text['btn_save'];?></span></button>
					</div>
				</div>
			</form>	
		</div>
				 
		
				
				
				
		<!-- 
					PROVIDER EDIT 	sec-5
					
		 -->
		<div class="section sec-5" id="pgProviderEdit">
				
			<form id="frm_provider_edit" class="form-horizontal" role="form">
				
				<div id="tbl_provider_edit">

					<div class="form-group">
						<label for="id" class="col-sm-2 control-label"><?=$Text['id'];?></label>
						<div class="col-sm-1">
							<input type="text" name="id" value="{id}" class="form-control" disabled>
						</div>
					</div>

					<div class="form-group">
						<label for="active_dummy_provider" class="col-sm-2 control-label"><?=$Text['active'];?></label>
						<div class="col-sm-1">
							<input type="checkbox" class="pull-left" name="active_dummy_provider" value="{active}" tabindex="1" class="form-control">
							<input type="hidden" name="id" value="{id}" />
						</div>
					</div>

					<div class="form-group">
						<label for="name" class="col-sm-2 control-label"><?=$Text['name'];?></label>
						<div class="col-sm-6">
							<input type="text" name="name" value="{name}" tabindex="2" class="form-control">
						</div>
					</div>

					<div class="form-group">
						<label for="nif" class="col-sm-2 control-label"><?=$Text['nif'];?></label>
						<div class="col-sm-2">
							<input type="text" name="nif" value="{nif}" tabindex="2" class="form-control">
						</div>
					</div>

					<div class="form-group">
						<label for="contact" class="col-sm-2 control-label"><?=$Text['contact'];?></label>
						<div class="col-sm-6">
							<input type="text" name="contact" value="{contact}" tabindex="2" class="form-control">
						</div>
					</div>

					<div class="form-group">
						<label for="address" class="col-sm-2 control-label"><?=$Text['address'];?></label>
						<div class="col-sm-6">
							<input type="text" name="address" value="{address}" tabindex="2" class="form-control">
						</div>
					</div>

					<div class="form-group">
						<label for="city" class="col-sm-2 control-label"><?=$Text['city'];?></label>
						<div class="col-sm-2">
							<input type="text" name="city" value="{city}" tabindex="2" class="form-control">
						</div>
					</div>

					<div class="form-group">
						<label for="zip" class="col-sm-2 control-label"><?=$Text['zip'];?></label>
						<div class="col-sm-2">
							<input type="text" name="zip" value="{zip}" tabindex="2" class="form-control">
						</div>
					</div>

					<div class="form-group">
						<label for="phone1" class="col-sm-2 control-label"><?=$Text['phone1'];?></label>
						<div class="col-sm-2">
							<input type="text" name="phone1" value="{phone1}" tabindex="2" class="form-control">
						</div>
					</div>

					<div class="form-group">
						<label for="phone2" class="col-sm-2 control-label"><?=$Text['phone2'];?></label>
						<div class="col-sm-2">
							<input type="text" name="phone2" value="{phone2}" tabindex="2" class="form-control">
						</div>
					</div>

					<div class="form-group">
						<label for="email" class="col-sm-2 control-label"><?=$Text['email'];?></label>
						<div class="col-sm-6">
							<input type="text" name="email" value="{email}" tabindex="2" class="form-control">
						</div>
					</div>

					<div class="form-group">
						<label for="web" class="col-sm-2 control-label"><?=$Text['web'];?></label>
						<div class="col-sm-6">
							<input type="text" name="web" value="{web}" tabindex="2" class="form-control">
						</div>
					</div>

					<div class="form-group">
						<label for="notes" class="col-sm-2 control-label"><?=$Text['notes'];?></label>
						<div class="col-sm-6">
							<textarea name="notes" tabindex="2" class="form-control">{notes}</textarea>
						</div>
					</div>


					<div class="form-group">
						<label for="responsible_uf_id" class="col-sm-2 control-label"><?=$Text['responsible_uf'];?></label>
						<div class="col-sm-2">
				    		<input type="hidden" name="responsible_uf_id" value="{responsible_uf_id}"/>
				    		<span class="sResponsibleUfId"></span>
						</div>
					</div>
					
					<div class="form-group">
						<label for="bank_name" class="col-sm-2 control-label"><?=$Text['bank_name'];?></label>
						<div class="col-sm-6">
							<input type="text" name="bank_name" value="{bank_name}" tabindex="2" class="form-control">
						</div>
					</div>
					

					<div class="form-group">
						<label for="bank_account" class="col-sm-2 control-label"><?=$Text['bank_account'];?></label>
						<div class="col-sm-6">
							<input type="text" name="bank_account" value="{bank_account}" tabindex="2" class="form-control">
						</div>
					</div>

					<div class="form-group">
						<label for="offset_order_close" class="col-sm-2 control-label"><?=$Text['offset_order_close'];?></label>
						<div class="col-sm-1">
							<input type="text" name="offset_order_close" value="{offset_order_close}" tabindex="2" class="form-control">
						</div>
					</div>
					
				</div>	

				<p>&nbsp;</p>
				<div class="form-group">
					<div class="col-sm-6"></div>
					<div class="col-sm-3">
						<button type="reset" class="btn btn-default change-sec" target-section="#sec-1"><?=$Text['btn_cancel'];?></button>
						&nbsp;&nbsp;
						<button class="btn btn-primary btn-save ladda-button" data-style="expand-right" ><span class="glyphicon glyphicon-save"></span> <span class="ladda-label"> <?php echo $Text['btn_save'];?></span></button>
					</div>
				</div>
			</form>
		</div>
			
			
		<!-- 
						PROVIDER NEW  	sec-6
						
		-->
		<div class="section sec-6" id="pgProviderNew">

			<form id="frm_provider_new" class="form-horizontal" role="form">
				
				<div id="tbl_provider_new">

				</div>
				<div class="form-group">
					<div class="col-sm-6"></div>
					<div class="col-sm-3">
						<button type="reset" class="btn btn-default change-sec" target-section="#sec-1"><?=$Text['btn_cancel'];?></button>
						&nbsp;&nbsp;
						<button class="btn btn-primary btn-save ladda-button" data-style="slide-right" ><span class="glyphicon glyphicon-save"></span> <span class="ladda-label"> <?php echo $Text['btn_save'];?></span></button>
					</div>
				</div>
			</form>
		</div>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
								
				

	</div>
	<!-- end of container wrap -->

<iframe id="exportChannel" src="" style="display:none; visibility:hidden;"></iframe>

<div id="dialog_edit_stock" class="hidden">
	<?php include('tpl/stock_dialog.php');?>
</div>

<div id="exportDialog" class="hidden">
	<?php include('tpl/export_dialog.php');?>
</div>

<div class="change-sec-modal"></div>
<!-- / END -->
</body>
</html>