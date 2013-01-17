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


		//clone the provider form. One is used as xml2html template the other for new providers
		var tblProvider = $('#tbl_provider_edit tbody').clone();
		$('#tbl_provider_new').append(tblProvider);

		//load mentor uf select listing
		$('#ufSelect').xml2html('init',{
				url: 'php/ctrl/UserAndUf.php',
				params : 'oper=getUfListing&all=0',
				loadOnInit:true,
				offSet : 1,
				complete : function(s){

				}
			}).change(function(){
			
			});	

		

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
				var selectedUf = $('#frm_provider_edit .responsibleUfSelect').text();
				
				var ufSelect = $('#ufSelect').clone();
				 
				$(ufSelect).val(selectedUf).attr('selected','selected');

				$('#frm_provider_edit .responsibleUfSelect').html('').append(ufSelect);

				$('#frm_provider_edit input:checkbox').each(function(){
					var bool = $(this).val(); 
					if (bool == "1") $(this).attr('checked',true);
				});


			},
			complete : function(s){
				
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
				prepareForm($('#frm_provider_new'));			
				switchTo('newProvider');
			});

		
		//edit provider
		$('.btn_edit_provider')
			.live('click', function(e){

				$('#tbl_providers tbody tr').removeClass('ui-state-highlight');
				gSelProvider = $(this).parents('tr');
				gSelProvider.addClass('ui-state-highlight');
				
				$('#tbl_provider_edit tbody').xml2html('reload',{
					params: 'oper=getProviders&all=1&provider_id='+gSelProvider.attr('providerId'),
					complete: function(rows){
						//prepareForm($('#frm_provider_edit'));
					}
				});

				//$('#frm_provider_edit input[name=id]').val(gSelProvider.attr('providerId'));
				
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
					submitProvider('#pgProviderEdit', 'edit'); 
				} else if ($(this).hasClass('new')){
					submitProvider('#pgProviderNew', 'add');
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

		$('.checkPActive')
			.live('click', function(e){
				e.stopPropagation();
			});


		
		function prepareForm(frm){

			//prepare the provider form the first time it is called
			if (gFirstTimeNewProvider){

				//clear id field
				$('.setProviderId').html('&nbsp;');
				
				//insert responsible uf
				var ufSelect = $('#ufSelect').clone();
				$(ufSelect).val(-1).attr('selected','selected');
				$('#frm_provider_new .responsibleUfSelect').html('').append(ufSelect);

				gFirstTimeNewProvider = false; 
			}

			
			$('input:text, input:hidden, textarea', frm).val('');
			//$('select', frm).

			
			//set the checkboxes
			$('input:checkbox', frm).each(function(){
				$(this).attr('checked','checked');
			});
			
		}



		/**
		 *	submits the create/edit provider data
		 *  mi: is the selector string of the layer that contains the whole member form and info
		 */
		function submitProvider(mi, action){

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


			isValidItem = $.checkSelect($(mi +' select[name=responsible_uf_id]'),-1);
			if (!isValidItem){
				isValid = false; 
				err_msg += "<?=$Text['msg_err_select_responsibleuf'];?>" + "<br/><br/>"; 
			}
			

			
			if (isValid){

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
							type: 'success'});
				   		
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

			//form is not valid		 
			} else {
				$.showMsg({
					msg:err_msg,
					type: 'error'});
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
				
				//switchTo('overviewProducts');
			});

		
		
	
		/**
		 *	provider SEARCH functionality 
		 */
		$("#search").keyup(function(e){
					var minLength = 3; 						//search with min of X characters
					var searchStr = $("#search").val(); 
					
					if (searchStr.length >= minLength){
						
						$('.loadAnimShop').show();
						$('#tbl_providers tbody').xml2html("reload",{
							params: 'oper=getShopProducts&like='+searchStr+'&date=1234-01-01',
							complete : function(rowCount){
								if (rowCount == 0){
									$('#searchTips').show();
								}
								$('.loadAnimShop').hide();
							}						
						});	
					} else {					 
						$('#product_list_search tbody').xml2html("removeAll");				//delete all product entries in the table if we are below minLength;		
						
					}
			e.preventDefault();						//prevent default event propagation. once the list is build, just stop here. 		
		}); //end autocomplete


		

			





		function switchTo(section){


			switch(section){

				case 'overviewProvider':
					$('.pgProductListing, .pgProviderEdit, .pgProviderNew').hide();
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
					
						$('.pgProviderOverview, .pgProviderEdit, .pgProviderNew').hide();
						$('.pgProductListing').fadeIn(1000);
					}
					break;

				case 'editProvider':
					$('.setProviderName').html(gSelProvider.children().eq(2).text());
					$('.pgProductListing, .pgProviderOverview, .pgProviderNew').hide();
					$('.pgProviderEdit').fadeIn(1000);
					
					break;

				case 'newProvider':
					$('.pgProductListing, .pgProviderOverview, .pgProviderEdit').hide();
					$('.pgProviderNew').fadeIn(1000);
					
					break;
					
					
					
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
								<tr id="{id}" productId="{id}">
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
						<h4 class="ui-widget-header">Edit <span class="setProviderName"></span> - <span class="setProductName"></span> </h4>
						<form id="frm_product_edit">
						<table id="tbl_product_edit" class="tblListingDefault">
							  <tr>
							    <td>Provider</td>
							    <td colspan="3"><select></select></td>
							  </tr>
							  <tr>
							    <td><label for="product_name">Name</label></td>
							    <td><input type="text" name="product_name" id="product_name" tabindex="1" /></td>
							    <td><label for="responsible_uf_id">Responsible UF id</label></td>
							    <td><select id="responsible_uf_id" tabindex="2"></select></td>
							  </tr>
							  <tr>
							    <td><label for="description">Description</label></td>
							    <td colspan="3">
							      <textarea name="description" id="description" cols="45" rows="2" tabindex="3"></textarea>
							 </td>
							  </tr>
							  <tr>
							    <td>Web page</td>
							    <td colspan="3">&nbsp;</td>
							  </tr>
							  <tr>
							    <td><label for="name3">Barcode</label></td>
							    <td colspan="3">&nbsp;</td>
							  </tr>
							  <tr>
							    <td>Active</td>
							    <td colspan="3">&nbsp;</td>
							  </tr>
							  <tr>
							    <td>&nbsp;</td>
							    <td colspan="3">&nbsp;</td>
							  </tr>
							  <tr>
							    <td>Product type</td>
							    <td colspan="3">&nbsp;</td>
							  </tr>
							  <tr>
							    <td>Product category</td>
							    <td colspan="3">&nbsp;</td>
							  </tr>
							  <tr>
							    <td>Order units</td>
							    <td colspan="3">&nbsp;</td>
							  </tr>
							  <tr>
							    <td>Purchase units</td>
							    <td colspan="3">&nbsp;</td>
							  </tr>
							  <tr>
							    <td>Unit Price neto</td>
							    <td colspan="3">&nbsp;</td>
							  </tr>
							  <tr>
							    <td>&nbsp;</td>
							    <td colspan="3">&nbsp;</td>
							  </tr>
							  <tr>
							    <td>Iva</td>
							    <td>&nbsp;</td>
							    <td>rev tax</td>
							    <td>&nbsp;</td>
							  </tr>
							  <tr>
							    <td>&nbsp;</td>
							    <td colspan="3">&nbsp;</td>
							  </tr>
							  <tr>
							    <td>Min stock</td>
							    <td>&nbsp;</td>
							    <td>&nbsp;</td>
							    <td>&nbsp;</td>
							  </tr>
							  <tr>
							    <td>Min order amount</td>
							    <td colspan="3">&nbsp;</td>
							  </tr>
							  <tr>
							    <td>&nbsp;</td>
							    <td colspan="3">&nbsp;</td>
							  </tr>
							</table>
						</table>
						</form>
					</di
				 
				 
				
				
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
							<td><span class="textAlignLeft responsibleUfSelect">{responsible_uf_id}</span></td>
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