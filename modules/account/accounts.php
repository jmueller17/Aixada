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

			
			
			/**
			 * 	account extract
			 */
			 $('#list_account tbody').xml2html('init',{
					url		: 'php/ctrl/Account.php',
					resultsPerPage : 20,
					paginationNav : '#list_account tfoot td',
					beforeLoad : function(){
						$('.loadSpinner').show();
					},
					rowComplete : function (rowIndex, row){
						$.formatQuantity(row, "<?=$Text['currency_sign'];?>");
					},
					complete : function(rowCount){
						$('.loadSpinner').hide();
						
						if ($('#list_account tbody tr').length == 0){
							$.showMsg({
								msg:"<?php echo $Text['msg_err_nomovements']; ?>",
								type: 'warning'});
						} else {
							$('#list_account tbody tr:even').addClass('rowHighlight'); 
						}
					}
			});
			
			/**				
			   -1          	Manteniment
			   -2          	Consum
			   -3 			Cashbox
			   1..999      Uf cash sources (money that comes out of our pockets or goes in)
			   1001..1999  regular UF accounts  (1000 + uf.id)
			   2001..2999  regular provider account (2000 + provider.id)
			*/				
							

			/**
			 * build account SELECT
			 */
			$("#account_select").xml2html("init", {
									url: 'php/ctrl/Account.php',
									params : 'oper=getActiveAccounts',
									offSet : 1,
									loadOnInit: true
						}).change(function(){
							//get the id of the provider
							var id = $("option:selected", this).val();
							
							if (id <= -100) {
								$('#list_account tbody').xml2html('removeAll');
								return true;
							}

							$('.account_id').html(id); 
							
							
							$('#list_account tbody').xml2html('reload',{
								params	: 'oper=accountExtract&account_id='+id+'&filter=pastYear',
							});						
			}); //end select change


			$("#tblAccountViewOptions")
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
						$('#list_account tbody').xml2html('reload',{
							params	: 'oper=accountExtract&account_id='+id+'&filter='+filter,
						});
					}
					
				}//end item selected 
			});//end menu


			if (what == 'my_account'){
				$('#list_account tbody').xml2html('reload',{
					params	: 'oper=accountExtract&filter=pastYear',
				});
			} 
						
			
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
		    	<h1 class="reportAccountElements"><?php echo $Text['ti_report_account']; ?></h1>
		    	<h1 class="myAccountElements"><?php echo $Text['ti_my_account_money'];?></h1>
		    </div>
		    <div id="titleRightCol50">

		    	<button	id="tblAccountViewOptions" class="hideInPrint floatRight"><?php echo $Text['btn_filter'];?></button>
		    	<div id="tblAccountOptionsItems" class="hidden hideInPrint">
					<ul>
						<li><a href="javascript:void(null)" id="today"><?php echo $Text['filter_acc_todays']; ?></a></li>
						<li><a href="javascript:void(null)" id="past2Month"><?php echo $Text['filter_recent']; ?></a></li>
						<li><a href="javascript:void(null)" id="pastYear"><?php echo $Text['filter_year'] ;?></a></li>
						<li><a href="javascript:void(null)" id="all"><?php echo $Text['filter_all'];?></a></li>
					</ul>
				</div>		
				
  				<p class="reportAccountElements textAlignCenter"> 
  					<select id="account_select" class="longSelect">
                    	<option value="-100" selected="selected"><?php echo $Text['sel_account']; ?></option> 
		    			<option value="{id}">{id} {name}</option>
		    		</select>
		    	</p>
		    </div>
		</div>
		
		<div id="account_listing" class="ui-widget">
			<div class="ui-widget-content ui-corner-all">
					
					<h3 class="ui-widget-header ui-corner-all"><?=$Text['latest_movements'];?> <span class="account_id"></span> <span style="float:right; margin-top:-4px;"><img class="loadSpinner" src="img/ajax-loader.gif"/></span></h3>
					<table id="list_account" class="tblListingDefault">
					<thead>
						<tr>
							<th><?php echo $Text['date'];?></th>
							<th><?php echo $Text['operator']; ?></th>
							<th><?php echo $Text['description']; ?></th>
							<th>Type</th>
							<th><p class="textAlignCenter"><?php echo $Text['account']; ?></p></th>
							<th><p class="textAlignRight"><?php echo $Text['amount']; ?></p></th>
							<th><p class="textAlignRight"><?php echo $Text['balance']; ?></p></th>
						</tr>
					</thead>
					<tbody>
						<tr class="xml2html_tpl">
							<td>{ts}</td>
							<td>{operator}</td>
							<td>{description}</td>
							<td>{method}</td>
							<td><p class="textAlignCenter">{account}</p></td>
							<td><p class="textAlignRight"><span class="formatQty">{quantity}</span></p></td>
							<td><p class="textAlignRight"><span class="formatQty">{balance}</span></p></td>
						</tr>
					</tbody>
					<tfoot>
						<tr><td></td></tr>
					</tfoot>
					</table>
					
					
			</div>	
		</div>

		
	</div>
	<!-- end of stage wrap -->
</div>
<!-- end of wrap -->

<!-- / END -->
</body>
</html>