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
						$(this).parent().addClass("aix-style-ok-green");
					} else {
						$(this).parent().addClass("noRed");
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
				var selectedUf = $('#responsibleUfSelect').text();
				
				var ufSelect = $('#ufSelect').clone(); 
				$(ufSelect).val(selectedUf).attr('selected','selected');
				$('#responsibleUfSelect').text('').append(ufSelect);

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
				//prepareForm($('#frm_provider_new'));			
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
			.click(function(){			
				
			});

		//cancel edits/forms
		$('.btn_cancel')
		.button({
				icons: {
	        		primary: "ui-icon-disk"
	        	}
			})
			.click(function(e){			
				if ($(this).hasClass('editProvider')){
					 switchTo('overviewProvider');
				}
		});


		
		function prepareForm(frm){

			$('input:text, input:hidden', frm).val('');
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
		function submitMember(action, mi){

			var urlStr = 'php/ctrl/Provider.php?oper=mngProvider';
			var isValid = true; 
			var err_msg = ''; 

			

			isValid = isValid && $.checkFormLength($(mi +' input[name=login]'),3,50);
			if (!isValid){
				err_msg += "<?=$Text['msg_err_usershort'];?>"; 
			}
	
			
			isValid = isValid &&  $.checkRegexp($(mi+' input[name="phone1"]'),/^([0-9\s\+])+$/);
			if (!isValid){
				err_msg += "<br/><br/>" + "<?php echo $Text['phone1'] .  $Text['msg_err_only_num']; ?>";
			}

			isValid = isValid &&  $.checkRegexp($(mi+' input[name="email"]'),/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
			if (!isValid){
				err_msg += "<br/><br/>" + "<?=$Text['msg_err_email'] ?>";
			}


			
			if (isValid){

				var sdata = $(mi + ' form').serialize();
								
				$.ajax({
				   	url: urlStr,
					data: sdata, 
				   	beforeSend: function(){
				   		$('.loadSpinner').show();
				   		$('.btn_save_new_member').button('disable');
					   	//$('button',mi).button('disable');
					   	//myButton.button('disable');
					  
					},
				   	success: function(msg){
				   	 	$.showMsg({
							msg: "<?php echo $Text['msg_edit_success']; ?>",
							type: 'success'});

						//reload all members of this uf
				   	 	$('#uf_detail_member_list').xml2html('reload',{
							params: "oper=getMemberInfo&uf_id="+gSelUfRow.attr('ufid'),
						});
						//show them
				   		switchTo('ufMemberView');
				   		
						//reload all members listing on overiew. 
				   		$('#member_list tbody').xml2html('reload'); 
				   	},
				   	error : function(XMLHttpRequest, textStatus, errorThrown){
					   $.showMsg({
							msg: XMLHttpRequest.responseText,
							type: 'error'});
				   	},
				   	complete : function(msg){
				   		$('.loadSpinner').hide();
				   		$('.btn_save_new_member').button('enable');
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

				
				if (tds.eq(4).text() == "1"){
					tds.eq(4).addClass("aix-style-ok-green");
				} else {
					tds.eq(4).addClass("noRed");
				}

			},
			complete : function (rowCount){
				$('.loadSpinner').hide();
				$('tr:even', this).addClass('rowHighlight');
				if (rowCount == 0){
					$.showMsg({
						msg:"<?php echo $Text['msg_no_active_products'];?>",
						type: 'info'});
				} 

				
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


		

			

		/**
		 *	saves the stock correction / add to the database
		 * 	for "addStock" the current_stock = stock + quantity.
		 * 	for "correctStock" current_stock = quantity; 
		 */
		function submitStock(oper, product_id, quantity){

			var urlStr = 'php/ctrl/Shop.php?oper='+oper+'&product_id='+product_id+'&quantity='+quantity; 
			
			
			$.ajax({
				type: "POST",
				url: urlStr,
				beforeSend : function(){
					$('.inputAddStock, .inputCorrectStock')
						.attr('disabled','disabled')
						.val('Saving...');
					/*$('.btn_save_new_stock, .btn_correct_stock')
						.button('option','Saving...')
						.button('disable');*/
					
				},
				success: function(txt){
					/*$('#add_stock_'+product_id+ ', #correct_stock_'+product_id)
						.addClass('ui-state-success')
						.val('Ok!');*/

					setTimeout(function(){
						$('#product_list_provider tbody').xml2html("reload");						
					},500)

				},
				error : function(XMLHttpRequest, textStatus, errorThrown){
					$.showMsg({
						msg:XMLHttpRequest.responseText,
						type: 'error'});
					
				},
				complete: function(){
					$('.inputAddStock, .inputCorrectStock')
						.removeAttr('disabled')
						.val('');
					/*$('.btn_save_new_stock, .btn_correct_stock')
						.button('label','Save')
						.button('enable');	*/
				}
			});
		}



		




		function switchTo(section){


			switch(section){

				case 'overviewProvider':
					$('.pgProductListing, .pgProviderEdit, .pgProviderNew').hide();
					$('.pgProviderOverview').fadeIn(1000);
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
				    	<h1 class="pgProviderEdit">Edit provider <span class="setProviderName"></span></h1>
				    	<h1 class="pgProviderNew">Create new provider</h1>
		    		</div>
		    		<div id="titleRightCol50">
						<button class="floatRight pgProviderOverview" id="btn_new_provider"><?php echo $Text['btn_new_provider']; ?></button>
						<!-- p class="providerOverview"><?php echo $Text['search_provider'];?>: <input id="search" class="ui-corner-all"/></p-->

		    		</div>
				</div><!-- end titlewrap -->
 
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
									<td><p class="providerActiveStatus textAlignCenter">{active}</p></td>
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
									<td><p class="textAlignCenter">{active}</p></td>
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
				
				
				
				
				
				<div class="pgProviderEdit ui-widget">
					<div class="ui-widget-content ui-corner-all">
						<h4 class="ui-widget-header"><span class="setProviderName"></span></h4>
						<form id="frm_provider_edit">
						<input type="hidden" name="provider_id" value="" />
						<table id="tbl_provider_edit" class="tblForms">
						<tbody>
						<tr providerId="{id}" responsibleUfId="{responsible_uf_id}">
							<td><label for="provider_id"><?php echo $Text['id']; ?></label></td>
							<td><p class="textAlignLeft ui-corner-all">{id}</p></td>
							<td><label for="active"><?php echo $Text['active'];?></label></td>
							<td><input type="checkbox" name="active" value="{active}" class="floatLeft" /></td>							
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
							<td><p class="textAlignLeft ui-corner-all" id="responsibleUfSelect">{responsible_uf_id}</p></td>
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
								<button class="btn_cancel editProvider"><?php echo $Text['btn_cancel']; ?></button>
								&nbsp;&nbsp;
								<button class="btn_save_provider"><?php echo $Text['btn_save'];?></button>
							</p>
							</td>
						</tr>
						</tfoot>
						</table>
					</form>


					<p>&nbsp;</p>
						
				
						
				</div>
			</div>
			
			
			
			<div class="pgProviderNew ui-widget hidden">
					<div class="ui-widget-content ui-corner-all">
						<h4 class="ui-widget-header"><span class="setProviderName"></span></h4>
						<form id="frm_provider_new">
						<table id="tbl_provider_new" class="tblForms">
						
						
						
							<tfoot>
							<tr>
								<td colspan="2"></td>
								<td>
								
								</td>
								<td><p class="floatRight">
										<button class="btn_save_provider"><?php echo $Text['btn_save'];?></button>
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