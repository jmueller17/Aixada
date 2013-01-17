<?php include "php/inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - ";?></title>
	
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

		//edit/save data 
		var gTransaction = false; 

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
						//$(this).html('<input class="checkPActive" type="checkbox" checked/>')
						//$(this).parent().addClass("aix-style-ok-green");
						$(this).html('<span class="ui-icon ui-icon-check"></span>').addClass('aix-style-ok-green ui-corner-all')
					} else {
						//$(this).parent().addClass("noRed");
						//$(this).html('<input class="checkPActive" type="checkbox" />');
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
						submitForm('#pgProviderEdit', 'edit');
					}		 
				} else if ($(this).hasClass('new')){
					if (checkProviderForm('#pgProviderNew')){
						submitForm('#pgProviderNew', 'add');
					}
				}
				e.stopPropagation();
				return false; 
			});

		//cancel edits/forms
		$('.btn_cancel')
		.button({
				icons: {
	        		primary: "ui-icon-disk"
	        	}
			})
			.click(function(e){			
				if ($(this).hasClass('edit') || $(this).hasClass('new')){
					 switchTo('overviewProvider');
				}
				return false; 
		});

		
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

			
		/**
		 *	submits the create/edit provider data
		 *  mi: is the selector string of the layer that contains the whole member form and info
		 * 	assume form has been checked		 
		 */
		function submitForm(mi, action){


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
			   	url: 'php/ctrl/TableManager.php?oper='+action+'&table=aixada_provider',
				data: sdata, 
			   	beforeSend: function(){
			   		$('.loadSpinner').show();
			   		$('.btn_save_provider').button('disable');
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
			   		$('.btn_save_provider').button('enable');
			   		gTransaction = true; 
			   	}
			}); //end ajax

			
		}


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


			isValidItem = $.checkSelect($(mi +' input[name=responsible_uf_id]'),'');
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

			$('select', frm)
			
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
				$(destination).children('select').val(selValue).attr('selected','selected');
				
				//$(destination).find('option:first').remove();
			})	

		}

		//loads the options for select boxes
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
					//tds.eq(4).addClass("aix-style-ok-green");
					tds.eq(4).children('p:first').html('<span class="ui-icon ui-icon-check"></span>').addClass('aix-style-ok-green ui-corner-all');
					//tds.eq(4).html('<span class="ui-icon ui-icon-check"></span>');
				} else {
					tds.eq(4).children('p:first').html('<span class="ui-icon ui-icon-closethick"></span>').addClass('noRed ui-corner-all');
				}

			},
			complete : function (rowCount){
				$('.loadSpinner').hide();
				$('tr:even', this).addClass('rowHighlight');
				/*if (rowCount == 0){
					$.showMsg({
						msg:"<?php echo $Text['msg_no_active_products'];?>",
						type: 'info'});
				} */
				
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





		/*****************************
		 *
		 *	GLOBAL (UTIL) FUNCTIONS
		 *
		 ****************************/
		function switchTo(section){


			switch(section){

				case 'overviewProvider':
					$('.pgProductListing, .pgProviderEdit, .pgProviderNew, .pgProductEdit, .pgProductNew').hide();
					if (gTransaction) { //if provider has been edited/new reload listing
						$('#tbl_providers tbody').xml2html("reload"); 
					}
					

					$('.pgProviderOverview').fadeIn(1000);
					gTransaction = false; 
					
					break;
	
				case 'overviewProducts':
					if (gSelProvider.attr('providerId') > 0) { 
						$('#tbl_products tbody').xml2html("reload",{
							params: 'oper=getShopProducts&provider_id='+gSelProvider.attr('providerId')+"&all=1",
						});

						$('.setProviderName').html(gSelProvider.children().eq(2).text());
					
						$('.pgProviderOverview, .pgProviderEdit, .pgProviderNew, .pgProductEdit, .pgProductNew').hide();
						$('.pgProductListing').fadeIn(1000);
					}
					break;

					
				case 'editProvider':
					$('.setProviderName').html(gSelProvider.children().eq(2).text());
					$('.pgProviderOverview, .pgProductListing, .pgProviderNew, .pgProductEdit, .pgProductNew').hide();
					$('.pgProviderEdit').fadeIn(1000);
					
					break;

				case 'newProvider':
					$('.pgProviderOverview, .pgProductListing, .pgProviderEdit, .pgProductEdit, .pgProductNew').hide();
					$('.pgProviderNew').fadeIn(1000);
					
					break;


				case 'editProduct':
					$('.pgProviderOverview, .pgProductListing, .pgProviderEdit, .pgProviderEdit, .pgProductNew').hide();
					$('.pgProductEdit').fadeIn(1000);
					
					
					
			}

		}

		switchTo('overviewProvider');


		$("#btn_overview").button({
			icons: {
	        		primary: "ui-icon-circle-arrow-w"
	        	}
			 })
    		.click(function(e){
				switchTo('overviewProvider'); 
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
						<button id="btn_overview" class="floatLeft pgProductListing pgProviderEdit pgProviderNew"><?php echo $Text['overview'];?></button>
				    	<h1 class="pgProviderOverview">Manage providers & products</h1>
				    	<h1 class="pgProductListing setProviderName"></h1>
				    	<h1 class="pgProviderEdit">Edit - <span class="setProviderName"></span></h1>
				    	<h1 class="pgProviderNew">Create new provider</h1>
		    		</div>
		    		<div id="titleRightCol50">
						<button class="floatRight pgProviderOverview" id="btn_new_provider"><?php echo $Text['btn_new_provider']; ?></button>
						<!-- p class="providerOverview"><?php echo $Text['search_provider'];?>: <input id="search" class="ui-corner-all"/></p-->

		    		</div>
				</div><!-- end titlewrap -->
 
 
 
				 <!-- 
							PROVIDER LISTING
							
				 -->
				<div class="ui-widget pgProviderOverview">
					<div class="ui-widget-content ui-corner-all">
						<h3 class="ui-widget-header">&nbsp;
								<!-- p class="ui-corner-all iconContainer ui-state-default floatRight" title="Edit incident">
									<span class="btn_del_incident ui-icon ui-icon-pencil"></span>
								</p -->
						</h3>
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
								<tr class="clickable" providerId="{id}">
									<td><input type="checkbox" name="bulkAction"/></td>
									<td><p class="textAlignRight">{id}</p></td>
									<td>{name}</td>
									<td>{phone1} / {phone2}</p></td>
									<td>{email}</td>
									<td><p class="providerActiveStatus iconContainer">{active}</p></td>
									<td><?php echo $Text['uf_short'];?>{responsible_uf_id} {responsible_uf_name}</td>
									<td><a href="javascript:void(null)" class="btn_edit_provider">Edit</a></td>
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
				<div class="pgProductListing ui-widget">
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
									<th>revtax</th>
									<th>iva</th>
									<th><?php echo $Text['unit'];?></th>
									<th><?php echo $Text['price'];?></th>
									<th>stock</th>
								</tr>
							</thead>
							<tbody>
								<tr id="{id}" class="clickable" productId="{id}">
									<td><input type="checkbox" name="bulkAction"/></td>
									<td>{id}</td>
									<td>{name}</td>
									<td>{orderable_type_id}</td>
									<td><p class="textAlignCenter iconContainer">{active}</p></td>
									<td>{rev_tax_percent}%</td>
									<td>{iva_percent}%</td>
									<td>{unit}</td>	
									<td>{unit_price}</td>	
									<td><p class="formatQty">{stock_actual}</p></td>
								</tr>						
							</tbody>
						</table>
					</div>
				</div>
				
				
				
				
				<!-- 
							PRODUCT EDIT
							
				 -->
				 <div class="pgProductEdit ui-widget">
					<div class="ui-widget-content ui-corner-all">
						<h4 class="ui-widget-header"><span class="setProviderName"></span> - <span class="setProductName"></span> </h4>
						<form id="frm_product_edit">
						<table id="tbl_product_edit" class="tblListingBorder">
							  <tbody>
							  <tr>
							    <td><label for="name">Name</label></td>
							    <td><input type="text" name="name" value="{name}" tabindex="1" class="ui-widget-content ui-corner-all inputTxtLarge" /></td>
							    <td><label for="responsible_uf_id"> Responsible household</label></td>
							    <td>
							    	<input type="hidden" name="responsible_uf_id" value="{responsible_uf_id}"/>
							    	<span class="textAlignLeft sResponsibleUfId"></span></td>
							  </tr>
							  <tr>
							    <td><label for="description">Description</label></td>
							    <td>
							      <textarea class="ui-widget-content ui-corner-all textareaLarge" name="description">{description}</textarea>
							 	</td>
							 	<td></td>
							 	<td></td>
							  </tr>
							  <tr>
							    <td>Web page</td>
							    <td colspan="3"><input type="text" name="description_url" value="{description_url}" class="inputTxtLarge ui-widget-content ui-corner-all" /></td>
							  </tr>
							  <tr>
							    <td><label for="barcode">Barcode</label></td>
							    <td colspan="3"><input type="text" name="barcode" value="{barcode}" class="ui-widget-content ui-corner-all" /></td>
							  </tr>
							  <tr>
							    <td>Active</td>
							    <td colspan="3"><input type="checkbox" name="active" value="{active}"/></td>
							  </tr>
							  <tr>
							    <td>&nbsp;</td>
							    <td colspan="3">&nbsp;</td>
							  </tr>
							  <tr>
							    <td>Product type</td>
							    <td colspan="3">
							    	<input type="hidden" name="orderable_type_id" value="{orderable_type_id}"/>
							    	<span class="textAlignLeft sOrderableTypeId"></span></td>
							  </tr>
							  <tr>
							    <td>Product category</td>
							    <td colspan="3">
							    	<input type="hidden" name="category_id" value="{category_id}"/>
							    	<span class="textAlignLeft sCategoryId"></span></td>
							  </tr>
							  <tr>
							    <td>Order units</td>
							    <td colspan="3">
							    	<input type="hidden" name="unit_measure_order_id" value="{unit_measure_order_id}"/>
							    	<span class="textAlignLeft sUnitMeasureOrderId"></span></td>
							  </tr>
							  <tr>
							    <td>Purchase units</td>
							    <td colspan="3">
							    	<input type="hidden" name="unit_measure_shop_id" value="{unit_measure_shop_id}"/>
							    	<span class="textAlignLeft sUnitMeasureShopId"></span></td>
							  </tr>
							  <tr>
							    <td>Unit Price neto</td>
							    <td colspan="3"><input type="text" name="unit_price" value="{unit_price}" class="ui-widget-content ui-corner-all" /></td>
							  </tr>
							  <tr>
							    <td>&nbsp;</td>
							    <td colspan="3">&nbsp;</td>
							  </tr>
							  <tr>
							    <td>Iva</td>
							    <td>
							    	<input type="hidden" name="iva_percent_id" value="{iva_percent_id}"/>
							    	<span class="textAlignLeft sIvaPercentId"></span></td>
							  </tr>
							  <tr>
							  <td>rev tax</td>
							    <td>
							    	<input type="hidden" name="rev_tax_type_id" value="{rev_tax_type_id}"/>
							    	<span class="textAlignLeft sRevTaxTypeId"></span></td>
							  
							  </tr>
							  <tr>
							    <td>&nbsp;</td>
							    <td colspan="3">&nbsp;</td>
							  </tr>
							  <tr>
							    <td>Min stock</td>
							    <td><input type="text" name="{stock_min}" value="{stock_min}" class="ui-widget-content ui-corner-all" /></td>
							    <td>&nbsp;</td>
							    <td>&nbsp;</td>
							  </tr>
							  <tr>
							    <td>Min order amount</td>
							    <td colspan="3"><input type="text" name="order_min_quantity" value="{order_min_quantity}" class="ui-widget-content ui-corner-all" /></td>
							  </tr>
							  <tr>
							    <td>&nbsp;</td>
							    <td colspan="3">&nbsp;</td>
							  </tr>
							  </tbody>
							  <tfoot>
								<tr>
									<td colspan="2"></td>
									
									<td colspan="2">
										<p class="floatRight">
											<button class="btn_cancel edit_product"><?php echo $Text['btn_cancel']; ?></button>
											&nbsp;&nbsp;
											<button class="btn_save_provider edit_product"><?php echo $Text['btn_save'];?></button>
										</p>
									</td>
								</tr>
							</tfoot>
						</table>
						</form>
					</div>	
				</div>
				 
				 
				<!-- 
							PRODUCT NEW
							
				 -->
				 <div class="pgProductNew ui-widget hidden">
					<div class="ui-widget-content ui-corner-all">
						<h4 class="ui-widget-header"><span class="setProviderName"></span> - <span class="setProductName"></span> </h4>
						<form id="frm_product_new">
						<table id="tbl_product_new" class="tblListingDefault" border="1">
							  
							  <tfoot>
								<tr>
									<td colspan="2"></td>
									
									<td colspan="2">
										<p class="floatRight">
											<button class="btn_cancel new_product"><?php echo $Text['btn_cancel']; ?></button>
											&nbsp;&nbsp;
											<button class="btn_save_provider new_product"><?php echo $Text['btn_save'];?></button>
										</p>
									</td>
								</tr>
							</tfoot>
						</table>
						</form>
					</div>	
				</div>
				 
				
				
				
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
							<td><label for="offset_order_close">Processing time</label></td>
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
									<button class="btn_cancel edit"><?php echo $Text['btn_cancel']; ?></button>
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
										<button class="btn_cancel new" ><?php echo $Text['btn_cancel']; ?></button>
										&nbsp;&nbsp;
										<button class="btn_save_provider new"><?php echo $Text['btn_save'];?></button>
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


<div id="loadUfListing" class="hidden">
<select id="ufSelect" name="responsible_uf_id">
	<option value="-1" selected="selected"><?=$Text['sel_uf']; ?></option>
	<option value="{id}">{id} {name}</option>	
</select>
</div>


<!-- / END -->
</body>
</html>