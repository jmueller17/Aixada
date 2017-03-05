<?php include "php/inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_incidents'];?></title>

	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
  	<link rel="stylesheet" type="text/css"   media="print"  href="css/print.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/fgmenu/fg.menu.css"   />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/<?=$default_theme;?>/jqueryui.css"/>

    <script type="text/javascript" src="js/jquery/jquery.js"></script>
    <script type="text/javascript" src="js/jqueryui/jqueryui.js"></script>
    <?php echo aixada_js_src(); ?>
   
	<script type="text/javascript">

	
	$(function(){
		$.ajaxSetup({ cache: false });

		//loading animation
		$('.loadSpinner').attr('src', "img/ajax-loader-<?=$default_theme;?>.gif"); 
		

		$('.detailElements').hide();		

		
		function switchTo(section){
			switch (section){
				case 'detail':
					$('.overviewElements, .detailCreateElements').hide();
					$('.detailElements, .detailEditElements').fadeIn(1000); 
					break;
					
				case 'new':
					resetDetails();
					$('.overviewElements, .detailEditElements').hide();
					$('.detailElements, .detailCreateElements').fadeIn(1000); 
					break;
					
				case 'overview':
					$('.detailElements, .detailEditElements, .detailCreateElements').hide(); 
					$('.overviewElements').fadeIn(1000);
			}
		}
		

		/** 
		 *	resets the incidents form when creating a new one. 
		 */
		function resetDetails(){
			$('#subject, #incidents_text, #ufs_concerned').val('');
			$('#statusSelect option:first').attr('selected',true);
			$('#typeSelect option:first').attr('selected',true);
			$('#prioritySelect option:first').attr('selected',true);
			$('#providerSelect  option:first').attr('selected',true);
			$('#commissionSelect  option:first').attr('selected',true);
			$('#ufs_concerned option:first').attr('selected',true);
			$('#incident_id').val('');
			$('#incident_id_info').html('');

		}


		//returns to incident overview. 
		$("#btn_overview").button({
			 icons: {
	        		primary: "ui-icon-circle-arrow-w"
	        	}
			 })
    		.click(function(e){
				switchTo('overview'); 
    		}).hide();

		
		$('#btn_new_incident')
			.button({
				icons: {
	        		primary: "ui-icon-plus"
	        	}

			})
			.click(function(){
				switchTo('new');
		});

		
		$('#btn_cancel')
			.button({
				icons: {
				primary: "ui-icon-close"}
			})
			.click(function(){
				switchTo('overview');
		});


		$('#btn_save')
			.button({
				icons: {
				primary: "ui-icon-check"}
			});

		
		$('.btn_view_incident')
			.live('click', function(){
				switchTo('detail');
				//$('#tbl_incidents tbody tr').trigger("click");
			});


		$("#tblIncidentsViewOptions")
		.button({
			icons: {
	        	secondary: "ui-icon-triangle-1-s"
			}
	    })
	    .menu({
			content: $('#tblIncidentsOptionsItems').html(),	
			showSpeed: 50, 
			width:180,
			flyOut: true, 
			itemSelected: function(item){					//TODO instead of using this callback function make your own menu; if jquerui is updated, this will  not work
				//show hide deactivated products
				var filter = $(item).attr('id');
				$('#tbl_incidents tbody').xml2html('reload',{
					params : 'oper=getIncidentsListing&filter='+filter
				});
				
			}//end item selected 
		});//end menu


		//print incidents accoring to current incidents template in new window or download as pdf
		$("#btn_print")
		.button({
			icons: {
				primary: "ui-icon-print",
	        	secondary: "ui-icon-triangle-1-s"
			}
	    })
	    .menu({
			content: $('#printOptionsItems').html(),	
			showSpeed: 50, 
			width:180,
			flyOut: true, 
			itemSelected: function(item){	
				if ($('input:checkbox[name="bulkAction"][checked="checked"]').length  == 0){
					$.showMsg({
						msg:"<?=$Text['msg_err_noselect'];?>",
						buttons: {
							"<?=$Text['btn_ok'];?>":function(){						
								$(this).dialog("close");
							}
						},
						type: 'warning'});
					return false; 
				}

				var link = $(item).attr('id');

				var idList = "";
				$('input:checkbox[name="bulkAction"][checked="checked"]').each(function(){
						idList += $(this).parents('tr').attr('incidentId')+",";
				});
				idList = idList.substring(0,idList.length-1);
				
				switch (link){
					case "printWindow": 
						var printWin = window.open('tpl/<?=$tpl_print_incidents;?>?idlist='+idList);
						printWin.focus();
						printWin.print();
						break;
	
					case "printPDF": 
						window.frames['dataFrame'].window.location = "tpl/<?=$tpl_print_incidents;?>?idlist="+idList+"&asPDF=1&outputFormat=D"; 
						break;
				}
								
			}//end item selected 
		});//end print menu
		

	
		//bulk actions
		$('input[name=bulkAction]')
			.live('click', function(e){
				e.stopPropagation();
			})
			
		$('#toggleBulkActions')
			.click(function(e){
				if ($(this).is(':checked')){
					$('input:checkbox[name="bulkAction"]').attr('checked','checked');
				} else {
					$('input:checkbox[name="bulkAction"]').attr('checked',false);
				}
				e.stopPropagation();
			});

		

		//download selected as zip
		/*$("#btn_zip").button({
			 icons: {
	        		primary: "ui-icon-suitcase"
	        	}
			 })
    		.click(function(e){
    			if ($('input:checkbox[name="bulkAction"][checked="checked"]').length  == 0){
					$.showMsg({
						msg:"<?=$Text['msg_err_noselect'];?>",
						buttons: {
							"<?=$Text['btn_ok'];?>":function(){						
								$(this).dialog("close");
							}
						},
						type: 'warning'});
    			} else {
        		
        			var orderRow = ''; 								
					$('input:checkbox[name="bulkAction"][checked="checked"]').each(function(){
						orderRow += '<input type="hidden" name="order_id[]" value="'+$(this).parents('tr').attr('orderId')+'"/>';
						orderRow += '<input type="hidden" name="provider_id[]" value="'+$(this).parents('tr').attr('providerId')+'"/>';
						orderRow += '<input type="hidden" name="date_for_order[]" value="'+$(this).parents('tr').attr('dateforOrder')+'"/>';
					});
					$('#submitZipForm').empty().append(orderRow);
					getZippedOrders();
    			}
    		});*/

		
		
		//DELETE incidents
		$('.btn_del_incident')
			.live("click", function(e){
					var incidentId = $(this).parents('tr').attr('incidentId'); 
					$.showMsg({
								msg: '<?php echo $Text['msg_delete_incident'];?>',
								type: 'confirm',
								buttons : {
										"<?php echo $Text['btn_ok'];?>": function() {
											
											$.ajax({
											    type: "POST",
											    url: "php/ctrl/Incidents.php?oper=delIncident&incident_id="+incidentId,
											    success: function(msg){
													resetDetails();
													$('#tbl_incidents tbody').xml2html('reload');
											    	
											    },
											    error : function(XMLHttpRequest, textStatus, errorThrown){
											    	$.showMsg({
														msg:XMLHttpRequest.responseText,
														type: 'error'});
											    }
											}); //end ajax
											$(this).dialog( "close" );
											
									  	}, 
									  	"<?php echo $Text['btn_cancel'];?>":function(){
									  		$(this).dialog( "close" );
											
									  	}
									}
					});

					e.stopPropagation();
			});

		
		//detect form submit and prevent page navigation
		$('form').submit(function() { 

			var dataSerial = $(this).serialize();
			
			$('button').button( "option", "disabled", true );

			
			$.ajax({
				    type: "POST",
				    url: "php/ctrl/Incidents.php?oper=mngIncident",
				    data: dataSerial,
				    beforeSend: function(){
				   		$('#editorWrap .loadSpinner').show();
					},
				    success: function(msg){
						switchTo('overview');
						resetDetails();
				    },
				    error : function(XMLHttpRequest, textStatus, errorThrown){
				    	 $.updateTips('#incidentsMsg','error','Error: '+XMLHttpRequest.responseText);
				    },
				    complete : function(msg){
				    	$('button').button( "option", "disabled", false );
				    	$('#tbl_incidents tbody').xml2html('reload');
				    	$('#editorWrap .loadSpinner').hide();//
				    	
				    	
				    }
			}); //end ajax
			
			return false; 
		});

			
		/**
		 *	incidents
		 */
		$('#tbl_incidents tbody').xml2html('init',{
				url: 'php/ctrl/Incidents.php',
				params : 'oper=getIncidentsListing&filter=past2Month',
				loadOnInit: true, 
				beforeLoad: function(){
					$('.loadSpinner').show();
				},
				complete : function(rowCount){
					$('#tbl_incidents tbody tr:even').addClass('rowHighlight'); 
					$('.loadSpinner').hide();	
				}
		});


		
		$('#tbl_incidents tbody tr')
			.live('mouseenter', function(){
				$(this).addClass('ui-state-highlight');
			})
			.live('mouseleave',function(){
				if (!$(this).hasClass('active_row')){
					$(this).removeClass('ui-state-highlight');
				}
			})
			//click on table row
			.live("click", function(e){

				resetDetails();
				
				//populate the form
				$(this).children('td[field_name]').each(function(){
					
					var input_name = $(this).attr('field_name');
					var value = $(this).text();

	
					if (input_name == 'incident_id') $('#incident_id_info').html('#'+value);
			
					//set the values of the select boxes
					if (input_name == 'type' || input_name == 'status' || input_name == 'priority' || input_name == 'commission' || input_name == 'provider'){
						$("#"+input_name+"Select").val(value).attr("selected",true);
						
					} else if (input_name == 'ufs_concerned'){
						var ufs = value.split(",");
						$('#ufs_concerned').val(ufs);
					} else {
						$('#'+input_name).val(value);
					}

					switchTo('detail');
					e.stopPropagation();
					
	
				});
				
		});
		
		

		//build provider select
		$("#providerSelect")
			.xml2html("init", {
				url: 'php/ctrl/SmallQ.php',
				params:'oper=getActiveProviders',
				offSet:1,
				loadOnInit:true
		});

		//build ufs select
		$("#ufs_concerned")
			.xml2html("init", {
				url: 'php/ctrl/UserAndUf.php',
				params:'oper=getUfListing&all=1',
				offSet:1,
				loadOnInit:true
		});

		//build type select
		/*$("#typeSelect")
			.xml2html("init", {
				url: 'php/ctrl/Incidents.php',
				params:'oper=getIncidentTypes',
				loadOnInit:false,
				complete: function(){
					$("#typeSelect option:last").attr("selected",true);
				}
		});*/

		//build commission select
		$("#commissionSelect")
			.xml2html("init", {
				url: 'php/ctrl/SmallQ.php',
				params : 'oper=getCommissions',
				offSet : 1,
				loadOnInit: true
		});


		
		
		switchTo('overview');
						
			
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
				<button id="btn_overview" class="floatLeft detailElements"><?php echo $Text['overview'];?></button>
		    	<h1><?=$Text['ti_incidents']; ?></h1>
		    </div>
		    <div id="titleRightCol50">
		 		<button	id="tblIncidentsViewOptions" class="overviewElements btn_right hideInPrint"><?=$Text['filter_incidents'];?></button>
		    		<div id="tblIncidentsOptionsItems" class="hidden hideInPrint">
					<ul>
					 <li><a href="javascript:void(null)" id="today"><?=$Text['filter_todays'];?></a></li>
					 <li><a href="javascript:void(null)" id="past2Month"><?=$Text['filter_recent'];?></a></li>
					 <li><a href="javascript:void(null)" id="pastYear"><?=$Text['filter_year'];?></a></li>
					</ul>
					</div>		
		    	
		    	<button id="btn_new_incident" class="overviewElements  hideInPrint"><?php echo $Text['btn_new_incident'];?></button>
		    	
		    	
		    	<button id="btn_print" class="overviewElements btn_right"><?=$Text['printout'];?></button>
		    		<div id="printOptionsItems" class="hidden hideInPrint">
					<ul>
					 <li><a href="javascript:void(null)" id="printWindow"><?=$Text['print_new_win'];?></a></li>
					 <li><a href="javascript:void(null)" id="printPDF"><?=$Text['print_pdf'];?></a></li>
					</ul>
					</div>		
		   		<!-- button id="btn_zip" class="overviewElements">Zip</button-->
		    	
		    			
		    </div>
		</div>
		
		<div id="incidents_listing" class="ui-widget overviewElements">
			<div class="ui-widget-content ui-corner-all">
					<h2 class="ui-widget-header ui-corner-all hideInPrint"><?php echo $Text['overview'];?>&nbsp;&nbsp;<span style="float:right;"><img class="loadSpinner" src="img/ajax-loader.gif"/></span></h2>
					<div id="tbl_div">
					<table id="tbl_incidents" class="tblListingDefault">
					<thead>
						<tr>
							<th>&nbsp;<input type="checkbox" id="toggleBulkActions" name="toggleBulk"/></th>
							<th class="mwidth-30"><p class="textAlignCenter"><?php echo $Text['id'];?></p></th>
							<th hideInPrint"><?php echo $Text['subject'];?></th>
							<th><?php echo $Text['priority'];?>&nbsp;&nbsp;</th>
							<th><?php echo $Text['created_by'];?></th>
							<th><p class="textAlignLeft"><?php echo $Text['created'];?></p></th>
							<th><?php echo $Text['status'];?></th>
							<th class="hidden"><?php echo $Text['incident_type'];?></th>
							<th class="hidden"><?php echo $Text['provider_name'];?></th>
							<th class="hidden"><?php echo $Text['ufs_concerned'];?></th>
							<th class="hidden"><?php echo $Text['comi_concerned'];?></th>
							<th class="hidden hideInPrint"><?=$Text['details'];?></th>
							<th class="maxwidth-100 hideInPrint textAlignRight"><?=$Text['actions'];?></th>
						</tr>
					</thead>
					<tbody>
						<tr class="clickable" incidentId="{id}">
							<td><input type="checkbox" name="bulkAction"/></td>
							<td field_name="incident_id">{id}</td>
							<td field_name="subject" class="hideInPrint"><p class="incidentsSubject">{subject}</p></td>
							<td field_name="priority"><p  class="textAlignCenter">{priority}</p></td>
							<td field_name="operator">{uf_id} {user_name}</td>
							<td field_name="date_posted"><p class="textAlignLeft">{ts}</p></td>
							<td field_name="status" class="textAlignCenter">{status}</td>
							<td field_name="type" class="hidden hideInPrint">{distribution_level}</td>
							<td field_name="type_description" class="hidden">{type_description}</td>
							<td field_name="provider" class="hidden hideInPrint">{provider_concerned}</td>
							<td field_name="provider_name" class="hidden">{provider_name}</td>
							<td field_name="ufs_concerned" class="hidden">{ufs_concerned}</td>
							<td field_name="commission" class="hidden">{commission_concerned}</td>
							<td field_name="incidents_text" class="hidden hideInPrint">{details}</td>
							<td class="hideInPrint">
								<p class="ui-corner-all iconContainer ui-state-default floatRight" title="Delete incident">
									<span class="btn_del_incident ui-icon ui-icon-trash"></span>
								</p>
								<!--  p class="ui-corner-all iconContainer ui-state-default" title="View incident">
									<span class="btn_view_incident ui-icon ui-icon-zoomin"></span>
								</p-->
							</td>
						</tr>
						<tr class="hidden">
							<td class="noBorder"></td>
							<td colspan="11" class="noBorder">{subject}</td>
						</tr>
						<tr class="hidden">
							<td class="noBorder"></td>
							<td colspan="11" class="hidden noBorder">{details}<p>&nbsp;</p></td>
						</tr>
					</tbody>
					
				</table>
			</div>
					
					
			</div>	
		</div>

		<div id="editorWrap" class="ui-widget hideInPrint detailElements">
			<div class="ui-widget-content ui-corner-all">
				<h3 class="ui-widget-header ui-corner-all">
					&nbsp;
					<span class="detailCreateElements"><?php echo $Text['create_incident'];?></span>
					<span class="detailEditElements"><?=$Text['incident_details'];?></span> 
					<span id="incident_id_info" class="detailEditElements">#</span>
					<span style="float:right; margin-top:-5px;"><img class="loadSpinner" src="img/ajax-loader.gif"/></span>
				</h3>
				<p id="incidentsMsg" class="user_tips"></p>
				<form>
					<input type="hidden" id="incident_id" name="incident_id" value=""/>
					<table class="tblForms">
						<tr>
							<td><label for="subject"><?php echo $Text['subject'];?>:</label></td>
							<td><input type="text" name="subject" id="subject" class="inputTxtLarge inputTxt ui-corner-all" value=""/></td>
							
							
						</tr>
						<tr>
							<td><label for="incidents_text"><?php echo $Text['message'];?>:</label></td>
							<td rowspan="5"><textarea id="incidents_text" name="incidents_text" class="textareaLarge inputTxt ui-corner-all"></textarea></td>
							
							<td><label for="prioritySelect"><?php echo $Text['priority'];?></label></td>
							<td><select id="prioritySelect" name="prioritySelect" class="mediumSelect"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option></select></td>
						</tr>
						<tr>
						
							<td></td>
							<td><label for="statusSelect"><?php echo $Text['status'];?></label></td>
							<td><select id="statusSelect" name="statusSelect" class="mediumSelect"><option value="open"> <?php echo $Text['status_open'];?></option><option value="closed"> <?php echo $Text['status_closed'];?></option></select></td>
						</tr>
						<tr>
							
							<td></td>
							<td><label for="typeSelect"><?=$Text['distribution_level'];?></label></td>
							<td>
								<select id="typeSelect" name="typeSelect" class="mediumSelect">
									<option value="1"><?=$Text['internal_private'];?></option>
									<option value="2"><?=$Text['internal_email_private'];?></option>
									<option value="3"><?=$Text['internal_post'];?></option>
									<option value="4"><?=$Text['internal_email_post'];?></option>
								</select></td>
						</tr>
						<tr>
							
							<td></td>
							<td><label for="ufs_concerned"><?php echo $Text['ufs_concerned']; ?></label></td>
							<td>
								<select id="ufs_concerned" name="ufs_concerned[]" multiple size="6">
								 	<option value="-1" selected="selected"><?php echo $Text['sel_none'];?></option>  
									<option value="{id}"> {id} {name}</option>	
								</select>
							</td>
						</tr>
						<tr>
							
							<td></td>
							<td><label for="providerSelect"><?php echo $Text['provider_concerned'];?></label></td>
							<td>
								<select id="providerSelect" name="providerSelect" class="mediumSelect">
	                    			<option value="-1" selected="selected"><?php echo $Text['sel_none'];?></option>                     
	                    			<option value="{id}"> {name}</option>
								</select>
							</td>
						</tr>
						<tr>
						<td></td>
							<td></td>
							<td><label for="commissionSelect"><?php echo $Text['comi_concerned'];?></label></td>
							<td><select id="commissionSelect" name="commissionSelect" class="mediumSelect">
									<option value="-1" selected="selected"><?php echo $Text['sel_none'];?></option>
									<option value="{description}"> {description}</option>
								</select>
							</td>
						</tr>
						<tr>
							<td></td>
							<td colspan="2" class="textAlignRight">
								<button id="btn_save" type="submit"><?php echo $Text['btn_save'];?></button>
								&nbsp;&nbsp;
								<button id="btn_cancel" type="reset"><?php echo $Text['btn_cancel'];?></button>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>


		
	</div>
	<!-- end of stage wrap -->
</div>
<!-- end of wrap -->

<!-- / END -->
<iframe name="dataFrame" style="display:none"></iframe>

</body>
</html>