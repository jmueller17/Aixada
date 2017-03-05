<?php 
	include "php/inc/header.inc.php";
	require_once(__ROOT__.'php/lib/account_writers.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_account'] ;?></title>
   
   	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
	<link rel="stylesheet" type="text/css"   media="print"  href="css/print.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/fgmenu/fg.menu.css"   />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/<?=$default_theme;?>/jqueryui.css"/>
 
    <script type="text/javascript" src="js/jquery/jquery.js"></script>
    <script type="text/javascript" src="js/jqueryui/jqueryui.js"></script>
    <?php echo aixada_js_src(); ?>
     
    <script type="text/javascript" src="js/jqueryui/i18n/jquery.ui.datepicker-<?=$language;?>.js" ></script>
         
   
	<script type="text/javascript">
	
	$(function(){
		$.ajaxSetup({ cache: false });

			//loading animation
			$('.loadSpinner').attr('src', "img/ajax-loader-<?=$default_theme;?>.gif").hide(); 
		
			

			//decide what to do in which section
			var what = $.getUrlVar('what');
			
			
			if (what == 'my_account'){
				$('.myAccountElements').show();
				$('.reportAccountElements').hide();
				$('#account_listing .account_id').text("<?php 
					$row_uf = get_row_query('SELECT name from aixada_uf where id='.
						get_session_uf_id());
					echo (1000 + get_session_uf_id()).' '.$row_uf['name']; 
				?>");
			} else { 
				$('.myAccountElements').hide();
				$('.reportAccountElements').show();
			}


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
							params	: 'oper=accountExtract&account_id='+id+'&filter='+filter
						});
					}
					
				}//end item selected 
			});//end menu


			if (what == 'my_account'){
				$('#list_account tbody').xml2html('reload',{
					params	: 'oper=accountExtract&filter=pastYear'
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
					<?php write_list_account_select(); ?>
		    	</p>
		    </div>
		</div>
		
		<?php write_list_account('', $Text['msg_err_nomovements']); ?>
	</div>
	<!-- end of stage wrap -->
</div>
<!-- end of wrap -->

<!-- / END -->
</body>
</html>