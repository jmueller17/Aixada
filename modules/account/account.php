<?php include "../../php/inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<base href="<?php echo $cv->basedir; ?>" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_account'] ;?></title>
   
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
    	<script type="text/javascript" src="js/js_for_account.min.js"></script>
    <?php }?>
         
         
   
	<script type="text/javascript">
	
	$(function(){


		$('.section').hide();


		$('.change-sec')
			.switchSection("init",{
				beforeSectionSwitch : function(section){
					//if (section == ".sec-3") 
					//	resetDetails();
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
			gStartDate = moment(gToday, "YYYY-MM-DD").subtract(3, 'months').format('YYYY-MM-DD');
			
	 		$('#datepicker-to').data("DateTimePicker").setDate(gToday);
			$('#datepicker-from').data("DateTimePicker").setDate(gStartDate);

			reloadListings();
		});
		
		//shortcuts for filtering date range. 
		$(".ctx-nav-filter")
			.click(function(){
				var range = $(this).attr("data"); 
				if (range == "precise"){
					$('.section-date-picker').toggle();
				} else {
					range = range.split(",");

					fromDate = moment(gToday, "YYYY-MM-DD").subtract(parseInt(range[1]), range[0]).format('YYYY-MM-DD');
			 		
			 		$('#datepicker-to').data("DateTimePicker").setDate(gToday);
					$('#datepicker-from').data("DateTimePicker").setDate(fromDate);

					reloadListings();
				}

			})



		/**********************
		 * ACCOUNTS LISTING
		 *********************/

		/**				
		   -1          	Manteniment
		   -2          	Consum
		   -3 			Cashbox
		   1..999      Uf cash sources (money that comes out of our pockets or goes in)
		   1001..1999  regular UF accounts  (1000 + uf.id)
		   2001..2999  regular provider account (2000 + provider.id)
		*/				
						

		//available accounts
		$("#accountSelect").xml2html("init", {
				url: 'modules/account/php/account_ctrl.php',
				params : 'oper=getActiveAccounts',
				offSet : 1,
				loadOnInit: true
			}).change(function(){
			
				reloadListings(); 

								
		}); //end select change



			
		$('#tbl_account tbody').xml2html('init',{
				url		: 'modules/account/php/account_ctrl.php',
				//resultsPerPage : 20,
				//paginationNav : '#tbl_account tfoot td',
				loadOnInit:false, 
				beforeLoad : function(){
					
				},
				rowComplete : function (rowIndex, row){
					$.formatQuantity(row, "<?=$Text['currency_sign'];?>");
				},
				complete : function(rowCount){
					bootbox.hideAll();
					
					if ($('#tbl_account tbody tr').length == 0){
						bootbox.alert({
							title : "Warning",
							message : "<div class='alert alert-warning'><?php echo $Text['msg_err_nomovements']; ?></div>"
						});
						$("#tbl_account").hide();
					} else {
						$("#tbl_account").show();
					}

				}
		});
			
			

			/*$("#tblAccountViewOptions")
			.button({
				icons: {
		        	secondary: "ui-icon-triangle-1-s"
				}
		    })
		    .menu({
				content: $('#tblAccountOptionsItems').html(),	
				showSpeed: 50, 
				width:280,
				flyOut: true, 
				itemSelected: function(item){

					var filter = $(item).attr('id');
					var id = $("#account_select option:selected").val();
					
					if (id == -100 && what != 'my_account'){
						$.showMsg({
							msg:"<?php echo $Text['msg_sel_account'];?>",
							buttons: {
								"<?=$Text['btn_ok'];?>":function(){	
									$(this).dialog('close');
								}
							},
							type: 'warning'});

					} else {
						id = (what == 'my_account')? '':id; 
						$('#tbl_account tbody').xml2html('reload',{
							params	: 'oper=accountExtract&account_id='+id+'&filter='+filter,
						});
					}
					
				}//end item selected 
			}); */ //end menu


			/*if (what == 'my_account'){
				$('#tbl_account tbody').xml2html('reload',{
					params	: 'oper=accountExtract&filter=pastYear',
				});
			} */

			function reloadListings(){
				var id = $("#accountSelect option:selected").val();
				
				if (id <= -100) {
					$('#tbl_account tbody').xml2html('removeAll');
					return true;
				}

				$('.account_id').html(id); 
				
				var from_date = $('#datepicker-from').data("DateTimePicker").getDate();
				var to_date = $('#datepicker-to').data("DateTimePicker").getDate();
				from_date = moment(from_date).format("YYYY-MM-DD") + " 00:00:00";
				to_date = moment(to_date).format("YYYY-MM-DD") + " 23:59:59"; //we compare a time stamp! 


				$('#tbl_account tbody').xml2html('reload',{
					params	: 'oper=accountExtract&account_id='+id+'&from_date='+from_date+'&to_date='+to_date
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
		    	
					<div class="col-md-1 section sec-1">
						<button type="button" class="btn btn-success btn-sm navbar-btn" id="btn-transfer">
		    				<span class="glyphicon glyphicon glyphicon-ok-sign"></span> Transfer
		  				</button>
	  				</div>

	  				<div class="col-md-3 section section-date-picker">
						<form class="navbar-form" role="date">
							<div class="form-group">
		                        <div class='input-group date input-group-sm' id='datepicker-from' >
		                            <input type='text' class="form-control" id="date-from" data-format="dddd, ll" placeholder="From" />
		                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
		                            </span>
		                        </div>
		                    </div>
		                </form>
		            </div>

		            <div class="col-md-3 section section-date-picker">
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

	            	<!--div class="col-md-3 section sec-1">
	            		<form class="navbar-form">
							<select id="accountSelect" class="form-control">
	                    		<option value="-100" selected="selected"><?=$Text['sel_account']; ?></option> 
			    				<option value="{id}">{id} {name}</option>
			    			</select>
		    			</form>
					</div-->

				

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
							<li class="level-1-indent"><a href="javascript:void(null)" data="months,3" class="ctx-nav-filter">Last 3 months</a></li>
							<li class="level-1-indent"><a href="javascript:void(null)" data="months,12" class="ctx-nav-filter">Last year</a></li>
							<li class="level-1-indent"><a href="javascript:void(null)" data="precise" class="ctx-nav-filter">Precise range</a></li>

						</ul>
					</div>
					


		      	</div>
			</nav>
		</div>
	</div><!-- end sub nav -->


	<!-- title section -->
	<div class="container" id="aix-title">
		<div class="row">
		    <div class="col-md-6 section sec-1">
		    	<h1><?=$Text['latest_movements']; ?></h1>
		    </div>
		    <div class="col-md-6 section sec-1">
        		<form class="navbar-form pull-right">
        			<h3>
						<select id="accountSelect" class="form-control">
	                		<option value="-100" selected="selected"><?=$Text['sel_account']; ?></option> 
		    				<option value="{id}">{id} {name}</option>
		    			</select>
	    			</h3>
    			</form>
			</div>
			
			<div class="col-md-10 section sec-2">
		    	<h1><?=$Text['ti_my_account_money']; ?></h1>
		    </div>
		</div>

		<div class="row">
			<div class="col-md-10 section sec-2">
				<h3><?=$Text['latest_movements'];?> <span class="account_id"></span></h3>
			</div>
		</div>
	</div>
	<!-- end of title section -->


	<div class="container">
		<div class="row">
			<div id="incidents_listing" class="section sec-1">
				<table id="tbl_account" class="table table-hover">
				<thead>
					<tr>
						<th><?=$Text['date'];?></th>
						<th><?=$Text['operator']; ?></th>
						<th><?=$Text['description']; ?></th>
						<th>Type</th>
						<th><p class="text-center"><?=$Text['account']; ?></p></th>
						<th><p class="text-right"><?=$Text['amount']; ?></p></th>
						<th><p class="text-right"><?=$Text['balance']; ?></p></th>
					</tr>
				</thead>
				<tbody>
					<tr class="xml2html_tpl">
						<td>{ts}</td>
						<td>{operator}</td>
						<td>{description}</td>
						<td>{method}</td>
						<td><p class="text-center">{account}</p></td>
						<td><p class="text-right"><span class="formatQty">{quantity}</span></p></td>
						<td><p class="text-right"><span class="formatQty">{balance}</span></p></td>
					</tr>
				</tbody>
				<tfoot>
					<tr><td></td></tr>
				</tfoot>
				</table>
				
					
			</div>	
		</div>
	</div>


<!-- / END -->
</body>
</html>