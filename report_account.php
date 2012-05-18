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
				$('#titleRightCol50').children().last().hide();
				$('#titleLeftCol50').children().first().hide(); 
				$('#titleLeftCol50').children().last().show(); 
				//.last().show();
			} else {
				$('#titleRightCol50').children().last().show();
				$('#titleLeftCol50').children().first().show(); 
				$('#titleLeftCol50').children().last().hide(); 
			}

			/**
			 * 	account listing
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
						if (rowCount == 0){
							$.showMsg({
								msg:"<?=$Text['msg_no_movements'];?>",
								type: 'info'});
						}
					}
			});
			
			/*				
			   -1          Manteniment
			   -2           Consum
			   1..999      Uf cash sources (money that comes out of our pockets or goes in)
			   1001..1999  regular UF accounts  (1000 + uf.id)
			   2001..2999  regular provider account (2000 + provider.id)
			*/				
							

			$("#datepicker").datepicker({
				dateFormat 	: 'DD, d MM, yy',
				showAnim	: '',
				onSelect : function (dateText, instance){
				 
					var id = (what == 'my_account')? $.getUrlVar('uf_id'): $("option:selected", '#account_select').val(); 
					
					
					if (id > -100) {
						$('#list_account tbody').xml2html('reload',{
							params	: 'oper=accountExtract&account_id='+id+'&start_date='+getSelectedDate()+'&num_rows=100',
						});
					}
						
					
				}//end select
			}).show();//end date pick
			
			//util function to retrieve and format selected date
			function getSelectedDate(){
				return $.datepicker.formatDate('yy-mm-dd', $("#datepicker").datepicker('getDate'));
			}
			
			//retrieve today
			$.ajax({
				type: "GET",
				url: "smallqueries.php?oper=getNextDate",		
				dataType: "xml", 
				success: function(xml){
					date = $(xml).find('date_for_order').text();
					$("#datepicker").datepicker('setDate', $.datepicker.parseDate('yy-mm-dd', date));

					if (what == 'my_account'){
						var uf_id = $.getUrlVar('uf_id');
						$('#list_account tbody').xml2html('reload',{
							params	: 'oper=accountExtract&account_id='+uf_id+'&start_date='+date+'&num_rows=100',
						});
					}
					
				} //end success
			});  //end ajax retrieve date


			

			/**
			 * build account SELECT
			 */
			$("#account_select").xml2html("init", {
									url: 'smallqueries.php',
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
								params	: 'oper=accountExtract&account_id='+id+'&start_date='+getSelectedDate()+'&num_rows=100',
							});						
			}); //end select change
			
						
			
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
		    	<h1><?php echo $Text['ti_report_account']; ?></h1>
		    	<h1><?php echo $Text['ti_my_account_money'];?></h1>
		    </div>
		    <div id="titleRightCol50">
		    	<p class="textAlignRight"><?php echo $Text['start_date']; ?> <input  type="text" class="datePickerInput ui-widget-content ui-corner-all" id="datepicker"></p>
		    	<p class="textAlignRight"> 	<select id="account_select" class="longSelect">
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
							<th><?php echo $Text['account']; ?></th>
							<th><?php echo $Text['amount']; ?></th>
							<th><?php echo $Text['balance']; ?></th>
						</tr>
					</thead>
					<tbody>
						<tr class="xml2html_tpl">
							<td>{ts}</td>
							<td>{operator}</td>
							<td>{description}</td>
							<td>{account}</td>
							<td>{quantity}</td>
							<td>{balance}</td>
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