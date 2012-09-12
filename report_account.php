<?php include "inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_account'] ;?></title>
   
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
    	<script type="text/javascript" src="js/js_for_report_account.min.js"></script>
    <?php }?>
         
    <script type="text/javascript" src="js/jqueryui/i18n/jquery.ui.datepicker-<?=$language;?>.js" ></script>
         
   
	<script type="text/javascript">
	
	$(function(){

			

			//decide what to do in which section
			var what = $.getUrlVar('what');
			
			
			if (what == 'my_account'){
				$('.myAccountElements').show();
				$('.reportAccountElements').hide();				
			} else { 
				$('.myAccountElements').hide();
				$('.reportAccountElements').show();
			}

			
			/**
			 * 	account extract
			 */
			 $('#list_account tbody').xml2html('init',{
					url		: 'ctrlReport.php',
					resultsPerPage : 20,
					paginationNav : '#list_account tfoot td',
					beforeLoad : function(){
						$('#account_listing .loadAnim').show();
					},
					complete : function(rowCount){
						$('#account_listing .loadAnim').hide();
						
						if ($('#list_account tbody tr').length == 0){
							$.showMsg({
								msg:"Sorry, there are no movements for the selected account and date. Try to widen the consulted time period with the filter button.",
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
									url: 'ctrlSmallQ.php',
									params : 'oper=getAllAccounts',
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
							msg:"There is currently no account selected! Choose an account first, then filter the results!",
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
		<?php include "inc/menu2.inc.php" ?>
	</div>
	<!-- end of headwrap -->
	
	
	<div id="stagewrap">
	
		<div id="titlewrap" class="ui-widget">
			<div id="titleLeftCol50">
		    	<h1 class="reportAccountElements"><?php echo $Text['ti_report_account']; ?></h1>
		    	<h1 class="myAccountElements"><?php echo $Text['ti_my_account_money'];?></h1>
		    </div>
		    <div id="titleRightCol50">

		    	<button	id="tblAccountViewOptions" class="hideInPrint floatRight">Filter</button>
		    	<div id="tblAccountOptionsItems" class="hidden hideInPrint">
					<ul>
						<li><a href="javascript:void(null)" id="today">Today's movements</a></li>
						<li><a href="javascript:void(null)" id="past2Month">Recent ones</a></li>
						<li><a href="javascript:void(null)" id="pastYear">Last year</a></li>
						<li><a href="javascript:void(null)" id="all">All</a></li>
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
					
					<h3 class="ui-widget-header ui-corner-all"><?=$Text['latest_movements'];?> <span class="account_id"></span> <span class="loadAnim floatRight hidden"><img src="img/ajax-loader.gif"/></span></h3>
					<table id="list_account" class="table_listing">
					<thead>
						<tr>
							<th><?php echo $Text['date'];?></th>
							<th><?php echo $Text['operator']; ?></th>
							<th><?php echo $Text['description']; ?></th>
							<th class="textAlignRight"><?php echo $Text['account']; ?></th>
							<th class="textAlignRight"><?php echo $Text['amount']; ?></th>
							<th class="textAlignRight"><?php echo $Text['balance']; ?></th>
						</tr>
					</thead>
					<tbody>
						<tr class="xml2html_tpl">
							<td class="textAlignCenter">{ts}</td>
							<td class="textAlignCenter">{operator}</td>
							<td>{description}</td>
							<td>{account}</td>
							<td>{quantity} {currency}</td>
							<td>{balance} {currency}</td>
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