<?php include "../../php/inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<base href="<?php echo $cv->basedir; ?>" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_incidents'];?></title>

    <link href="js/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/aixcss.css" rel="stylesheet">
    <link href="js/ladda/ladda-themeless.min.css" rel="stylesheet">
  	<link href="js/datepicker/bootstrap-datetimepicker.min.css" rel="stylesheet">



    <?php if (isset($_SESSION['dev']) && $_SESSION['dev'] == true ) { ?> 
	    <script type="text/javascript" src="js/jquery/jquery.js"></script>
	    <script type="text/javascript" src="js/bootstrap/js/bootstrap.min.js"></script>
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaXML2HTML.js" ></script>
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaSwitchSection.js" ></script>
	   	
	   	<script type="text/javascript" src="js/bootbox/bootbox.js"></script>
	   	<script type="text/javascript" src="js/ladda/spin.min.js"></script>
	   	<script type="text/javascript" src="js/ladda/ladda.min.js"></script>
	   	<script type="text/javascript" src="js/datepicker/moment-with-langs.min.js"></script>	   	
		<script type="text/javascript" src="js/datepicker/bootstrap-datetimepicker.min.js"></script>
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaUtilities.js" ></script>

   	<?php  } else { ?>
	    <script type="text/javascript" src="js/js_for_incidents.min.js"></script>
    <?php }?>

   
	<script type="text/javascript">

	
	$(function(){

		$('.section').hide();


		$('.change-sec')
			.switchSection("init",{
				beforeSectionSwitch : function(section){
					if (section == ".sec-3") //new incident
						resetDetails();
				}

			});

		$('.sec-1').show();

		bootbox.setDefaults({
			locale:"<?=$language;?>"
		})


		$('#datepicker-from').datetimepicker({
				pickTime:false,
				startDate : '1/1/2014',
			}).on("change.dp",function(e){
				reloadListings();
			})

		$('#datepicker-to').datetimepicker({
				pickTime:false,
				startDate : '1/1/2014',
			}).on("change.dp",function(e){
				reloadListings();
			})

		$.getAixadaDates('getToday', function (date){
			gToday = date[0];
			gPrevMonth = moment(gToday, "YYYY-MM-DD").subtract(1, 'months').format('YYYY-MM-DD');
			
	 		$('#datepicker-to').data("DateTimePicker").setDate(gToday);
			$('#datepicker-from').data("DateTimePicker").setDate(gPrevMonth);

			reloadListings();
		});
		
		//shortcuts for filtering date range. 
		$(".ctx-nav-filter")
			.click(function(){
				var range = $(this).attr("data")
				range = range.split(",");

				fromDate = moment(gToday, "YYYY-MM-DD").subtract(parseInt(range[1]), range[0]).format('YYYY-MM-DD');
		 		
		 		$('#datepicker-to').data("DateTimePicker").setDate(gToday);
				$('#datepicker-from').data("DateTimePicker").setDate(fromDate);

				reloadListings();

			})

		//create new incident
		$("#btn-create-incident")
    		.click(function(e){

    			resetDetails();

    			$('.change-sec').switchSection("changeTo",".sec-2");

    		});		

		//print incidents accoring to current incidents template in new window or download as pdf
		/*$(".ctx-nav-exp").click(function(e){

			if ($('input:checkbox[name="bulkAction"][checked="checked"]').length  == 0){
				
				bootbox.alert({
						title : "Warning",
						message : "<div class='alert alert-warning'><?php echo $Text['msg_err_noselect']; ?></div>"
					});	
			}
			

			var opt = $(this).attr("href");

			var idList = ""; //make list of all checked incidents
			$('input:checkbox[name="bulkAction"][checked="checked"]').each(function(){
					idList += $(this).parents('tr').attr('incidentId')+",";
			});
			idList = idList.substring(0,idList.length-1);

			switch ("export-win"){
				case "printWindow": 
					printWin = window.open('tpl/<?=$tpl_print_incidents;?>?idlist='+idList);
					printWin.focus();
					printWin.print();
					break;

				case "export-pdf": 
					window.frames['dataFrame'].window.location = "tpl/<?=$tpl_print_incidents;?>?idlist="+idList+"&asPDF=1&outputFormat=D"; 
					break;
			}


		})*/

	
		/***********************
		 *	INCIDENTS LISTING
		 ***********************/
		$('#tbl_incidents tbody').xml2html('init',{
				url: 'modules/incidents/php/incidents_ctrl.php',
				loadOnInit: false,
				progressbar : true
		});


		$('#tbl_incidents tbody')
			.on("click", "tr", function(e){

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
					
					e.stopPropagation();
				});

				$('.change-sec').switchSection("changeTo",".sec-2");
		});


		//incidents checkboxes.
		$('#tbl_incidents tbody')
			.on('click','td:first-child, input',  function(e){				
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

		//delete incidents
		$('#tbl_incidents tbody')
			.on("click", ".del-incident",  function(e){
				var id = $(this).parents('tr').attr('incidentId'); 
				bootbox.confirm({
						title : "<?=$Text['ti_confirm'];?>",
						message : "<div class='alert alert-warning'><?php echo $Text['msg_delete_incident'];?></div>",
						callback : function(ok){
							if (ok){
								deleteIncident(id)
							} else {
								bootbox.hideAll();
							}	
						}
				});

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

		

		/*************************************
		 *	DETAILS (CREATE, VIEW, EDIT)
		 *************************************/
		

		//detect form submit and prevent page navigation
		$('#save-btn').click(function(e) { 			
			var dataSerial = $("#frm-incidents").serialize();
			
			e.preventDefault(e);

    		var l = Ladda.create(this)
    		l.start();

			$.ajax({
				    type: "POST",
				    url: "modules/incidents/php/incidents_ctrl.php?oper=mngIncident",
				    data: dataSerial,
				    success: function(msg){
						resetDetails();
				    	$('#tbl_incidents tbody').xml2html('reload');
						$('.change-sec').switchSection("changeTo",".sec-1");
				    },
				    error : function(XMLHttpRequest, textStatus, errorThrown){
				    	 bootbox.alert("An error has occured: " + XMLHttpRequest.responseText);
				    },
				    complete : function(msg){
				    	l.stop();
				    }
			}); //end ajax
			
			
		});


		function deleteIncident(id){
			$.ajax({
			    type: "POST",
			    url: "modules/incidents/php/incidents_ctrl.php?oper=delIncident&incident_id="+id,
			    success: function(msg){
					resetDetails();
					$('#tbl_incidents tbody').xml2html('reload');
			    	
			    },
			    error : function(XMLHttpRequest, textStatus, errorThrown){
			    	bootbox.alert("An error has occured: " + XMLHttpRequest.responseText);
			    }
			}); //end ajax

		}

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


		//build commission select
		$("#commissionSelect")
			.xml2html("init", {
				url: 'php/ctrl/SmallQ.php',
				params : 'oper=getCommissions',
				offSet : 1,
				loadOnInit: true
		});


		

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


		/**
		 *	reloads cart and bill listings
		 */
		function reloadListings(){

			var from_date = $('#datepicker-from').data("DateTimePicker").getDate();
			var to_date = $('#datepicker-to').data("DateTimePicker").getDate();
			from_date = moment(from_date).format("YYYY-MM-DD") + " 00:00:00";
			to_date = moment(to_date).format("YYYY-MM-DD") + " 23:59:59"; //we compare a time stamp! 


			$('#tbl_incidents tbody').xml2html('reload',{
				params : "oper=getIncidentsListing&from_date="+from_date+"&to_date="+to_date, 
			});


		}
	

		
	});  //close document ready
	</script>
</head>
<body>

	<div id="headwrap">
		<?php include "../../php/inc/menu.inc.php" ?>
	</div>
	<!-- end of main menu / headwrap -->

	<!-- sub nav -->
	<div class="container section sec-1">
		<div class="row">
			<nav class="navbar navbar-default" role="navigation" id="ax-submenu">
			  	<div class="navbar-header">
			     	<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#sub-navbar-collapse">
				        <span class="sr-only">Toggle navigation</span>
				        <span class="icon-bar"></span>
				        <span class="icon-bar"></span>
				        <span class="icon-bar"></span>
			      	</button>
	    		</div>

	    		<div class="navbar-collapse collapse" id="sub-navbar-collapse">
		    	
					<div class="col-md-4">
						<button type="button" class="btn btn-success btn-sm navbar-btn section sec-1" id="btn-create-incident">
		    				<span class="glyphicon glyphicon glyphicon-ok-sign"></span> New incident
		  				</button>
	  				</div>

	  				<div class="col-md-3 section sec-3 sec-1">
						<form class="navbar-form pull-right" role="date">
							<div class="form-group">
		                        <div class='input-group date input-group-sm' id='datepicker-from' >
		                            <input type='text' class="form-control" id="date-from" data-format="dddd, ll" placeholder="From" />
		                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
		                            </span>
		                        </div>
		                    </div>
		                </form>
		            </div>

		            <div class="col-md-3">
						<form class="navbar-form" role="date">
							<div class="form-group">
		                        <div class='input-group date input-group-sm' id='datepicker-to' >
		                            <input type='text' class="form-control" name="date-to" data-format="dddd, ll" placeholder="To" />
		                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
		                            </span>
		                        </div>
		                    </div>
		                </form>
	            	</div>

					

					<div class="btn-group col-md-1">
						<button type="button" class="btn btn-default btn-sm navbar-btn dropdown-toggle" data-toggle="dropdown">
		    				Actions <span class="caret"></span>
		  				</button>
						<ul class="dropdown-menu" role="menu">
							<li><a href="javascript:void(null)" class="ctx-nav ctx-nav-export-bill"><span class="glyphicon glyphicon-export"></span> <?=$Text['btn_export'];?></a></li>
						</ul>
						
					</div>



	  				<div class="btn-group col-md-1 pull-right">
						<button type="button" class="btn btn-default btn-sm navbar-btn dropdown-toggle" data-toggle="dropdown">
							<span class="glyphicon glyphicon-filter"></span>&nbsp; <span class="caret"></span>
						</button>
						<ul class="dropdown-menu" role="menu">
							<li><a href="javascript:void(null)">Filter</a></li>
						    <li class="level-1-indent"><a href="javascript:void(null)" data="days,0" class="ctx-nav-filter">Today</a></li>
							<li class="level-1-indent"><a href="javascript:void(null)" data="weeks,1" class="ctx-nav-filter">Last week</a></li>
							<li class="level-1-indent"><a href="javascript:void(null)" data="months,1" class="ctx-nav-filter">Last month</a></li>
							<li class="level-1-indent"><a href="javascript:void(null)" data="months,3" class="ctx-nav-filter">Last 3 month</a></li>
							<li class="level-1-indent"><a href="javascript:void(null)" data="months,12" class="ctx-nav-filter">Last year</a></li>

						</ul>
					</div>
					


		      	</div>
			</nav>
		</div>
	</div><!-- end sub nav -->

	
	<div class="container" id="aix-title">
		<div class="row">
		    <div class="col-md-10 section sec-2">
		    	<h1><span class="glyphicon glyphicon-chevron-left change-sec" target-section="#sec-1"></span> <?=$Text['incident_details'];?>	<span id="incident_id_info">#</span></h1>
		    </div>
			
			<div class="col-md-10 section sec-1">
		    	<h1><?=$Text['ti_incidents']; ?></h1>
		    </div>

		    <div class="col-md-10 section sec-3">
		    	<h1><?=$Text['create_incident'];?></h1>
		    </div>
		</div>
	</div>


	<div class="container">
		<div class="row">
			<div id="incidents_listing" class="section sec-1">
				<table id="tbl_incidents" class="table table-hover">
					<thead>
						<tr>
							<th><input type="checkbox" id="toggleBulkActions" name="toggleBulk"/></th>
							<th><?php echo $Text['id'];?></th>
							<th><?php echo $Text['subject'];?></th>
							<th><?php echo $Text['priority'];?>&nbsp;&nbsp;</th>
							<th><?php echo $Text['created_by'];?></th>
							<th><?php echo $Text['created'];?></th>
							<th><?php echo $Text['status'];?></th>
							<th class="hidden"><?php echo $Text['incident_type'];?></th>
							<th class="hidden"><?php echo $Text['provider_name'];?></th>
							<th class="hidden"><?php echo $Text['ufs_concerned'];?></th>
							<th class="hidden"><?php echo $Text['comi_concerned'];?></th>
							<th class="hidden"><?=$Text['details'];?></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<tr class="clickable" incidentId="{id}">
							<td><input type="checkbox" name="bulkAction"/></td>
							<td field_name="incident_id">{id}</td>
							<td field_name="subject"><p class="incidentsSubject">{subject}</p></td>
							<td field_name="priority"><p  class="textAlignCenter">{priority}</p></td>
							<td field_name="operator">{uf_id} {user_name}</td>
							<td field_name="date_posted"><p class="textAlignLeft">{ts}</p></td>
							<td field_name="status" class="textAlignCenter">{status}</td>
							<td field_name="type" class="hidden">{distribution_level}</td>
							<td field_name="type_description" class="hidden">{type_description}</td>
							<td field_name="provider" class="hidden">{provider_concerned}</td>
							<td field_name="provider_name" class="hidden">{provider_name}</td>
							<td field_name="ufs_concerned" class="hidden">{ufs_concerned}</td>
							<td field_name="commission" class="hidden">{commission_concerned}</td>
							<td field_name="incidents_text" class="hidden">{details}</td>
							<td>
								<span class="glyphicon glyphicon-remove-circle del-incident"></span>
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

	<!-- editor -->
	<div class="section sec-2 sec-3">
		<div class="container">
			<p id="incidentsMsg" class="user_tips"></p>
			
			<form class="form-horizontal" role="form" id="frm-incidents">
				<input type="hidden" id="incident_id" name="id" value=""/>
				<div class="form-group">
					<label for="subject" class="col-sm-2 control-label"><?php echo $Text['subject'];?></label>
					<div class="col-sm-6">
						<input type="text" name="subject" id="subject" class="form-control" placeholder="<?php echo $Text['subject'];?>" value="">
					</div>
					</div>

					<div class="form-group">
						<label for="details" class="col-sm-2 control-label"><?php echo $Text['message'];?></label>
					<div class="col-sm-6">
						<textarea id="incidents_text" name="details" class="form-control" placeholder="Your message here"></textarea>
					</div>
					</div>

					<div class="form-group">
						<label for="priority" class="col-sm-2 control-label"><?php echo $Text['priority'];?></label>
					<div class="col-sm-2">
						<select id="prioritySelect" name="priority" class="form-control"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option></select>
					</div>
					</div>
	
				<div class="form-group">
						<label for="status" class="col-sm-2 control-label"><?php echo $Text['status'];?></label>
					<div class="col-sm-2">
						<select id="statusSelect" name="status" class="form-control"><option value="open"> <?php echo $Text['status_open'];?></option><option value="closed"> <?php echo $Text['status_closed'];?></option></select>
					</div>
					</div>

				<div class="form-group">
						<label for="priority" class="col-sm-2 control-label"><?php echo $Text['distribution_level'];?></label>
					<div class="col-sm-4">
						<select id="typeSelect" name="incident_type_id" class="form-control">
								<option value="1"><?=$Text['internal_private'];?></option>
								<option value="2"><?=$Text['internal_email_private'];?></option>
								<option value="3"><?=$Text['internal_post'];?></option>
								<option value="4"><?=$Text['internal_email_post'];?></option>
						</select>
					</div>
					</div>

					<div class="form-group">
						<label for="ufs_concerned" class="col-sm-2 control-label"><?php echo $Text['ufs_concerned'];?></label>
					<div class="col-sm-4">
						<select id="ufs_concerned" name="ufs_concerned[]" multiple class="form-control" size="6">
							 	<option value="-1" selected="selected"><?php echo $Text['sel_none'];?></option>  
								<option value="{id}"> {id} {name}</option>	
						</select>
					</div>
					</div>

				<div class="form-group">
						<label for="provider_concerned" class="col-sm-2 control-label"><?php echo $Text['provider_concerned'];?></label>
					<div class="col-sm-4">
						<select id="providerSelect" name="provider_concerned" class="form-control">
                    			<option value="-1" selected="selected"><?php echo $Text['sel_none'];?></option>                     
                    			<option value="{id}"> {name}</option>
						</select>
					</div>
					</div>

				<div class="form-group">
						<label for="commission_concerned" class="col-sm-2 control-label"><?php echo $Text['comi_concerned'];?></label>
					<div class="col-sm-4">
						<select id="commissionSelect" name="commission_concerned" class="form-control">
								<option value="-1" selected="selected"><?php echo $Text['sel_none'];?></option>
								<option value="{description}"> {description}</option>
						</select>
					</div>
					</div>
					<div>&nbsp;</div>

					<div class="form-group">
					<div class="col-sm-5"></div>
						<div class="cols-sm-1">
						<button type="reset" class="btn btn-default change-sec" target-section="#sec-1"><?php echo $Text['btn_cancel'];?></button>
						&nbsp;&nbsp;
						<button type="submit" id="save-btn" class="btn btn-primary ladda-button" data-style="slide-left" ><span class="ladda-label"><?php echo $Text['btn_save'];?></span></button>

					</div>

				</div>
				<div>&nbsp;</div>
				<div>&nbsp;</div>
			</form>
		</div>
	</div>


<!-- / END -->
<iframe name="dataFrame" style="display:none"></iframe>


<div class="modal js-loading-bar">
 <div class="modal-dialog">
   <div class="modal-content">
     <div class="modal-body">
      <p></p>
       <div class="progress progress-striped active">
        <div class="progress-bar"></div>
       </div>
     </div>
   </div>
 </div>
</div>


</body>
</html>