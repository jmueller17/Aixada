<?php include "inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_manage'] . " " . $Text['head_ti_manage_uf'] ;?></title>
	
	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
	<link rel="stylesheet" type="text/css"   media="print"  href="css/print.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/fgmenu/fg.menu.css"   />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/jquery-ui/ui-lightness/jquery-ui-1.8.custom.css"/>

	<?php if (isset($_SESSION['dev']) && $_SESSION['dev'] == true ) { ?> 
	    <script type="text/javascript" src="js/jquery/jquery.js"></script>
		<script type="text/javascript" src="js/jquery/jquery-ui-1.8.custom.min.js"></script>
		<script type="text/javascript" src="js/fgmenu/fg.menu.js"></script>
		<script type="text/javascript" src="js/jquery/jquery.aixadaMenu.js"></script>     	 
	   	<script type="text/javascript" src="js/jquery/jquery.aixadaXML2HTML.js" ></script>
	   	<script type="text/javascript" src="js/jquery/jquery.aixadaUtilities.js" ></script>
   	<?php  } else { ?>
   	    <script type="text/javascript" src="js/js_for_manage_db.min.js"></script>
    <?php }?>
    
   
	<script type="text/javascript">
	
	$(function(){

				
		//init the uf listing
		$('#uf_list tbody').xml2html('init',{
			url: 		'smallqueries.php',
			params : 	'oper=getAllUFs',
			loadOnInit:	true,
			resultsPerPage:20,
			paginationNav : '#uf_list tfoot td',
			beforeLoad : function(){
				$('#uf_listing .loadAnim').show();
			},
			rowComplete: function(index, row){
				var ckbx = row.children().first().find('input');
				if (ckbx.val() == "1") ckbx.attr('checked',true); //set the checkbox if uf is active or not
			},
			complete : function(){
				$('#uf_listing .loadAnim').hide();
			}
		});	

		 


		
		

		/**
		 *	edit ufs
		 */
		$('.btn_edit_uf').live('click',function(e){
			//if we are editing, save it
			if ($(this).hasClass('ui-icon-disk')){
				$('#uf_edit').submit();
				//$('#uf_list tbody').xml2html('reload'); 				
			//if we click pencil, make fields editable
			} else {
				var id = $(this).parent().parent().attr('ufid');

				if (id != prev_id){
					resetPreviousRow();
					highlightCurrentRow($(this).parent().parent());
				}
				prev_id = id; 
				
				$(this).removeClass('ui-icon-pencil').addClass('ui-icon-disk');
				makeEditable($(this).parent().prev().prev(),'uf_name','uf_name');
				return false; //prevent event propagation to uf table row click

			}
		});

			
		

		
		

		/**
		 *	assign stuff
		 */
		$('#btn_backup')
			.button({
				icons: {
					primary: "ui-icon-copy"}
			})
			.click(function(){

				$.ajax({
					   url: "ctrlValidate.php?oper=backupDatabase",
					   beforeSend: function(){
						   $('.loadAnim').show();
						   $('#btn_backup').button( "option", "disabled", true );
						},
					   success: function(msg){
							 $('#msg_link').html("<a href='"+msg+"'>Download db-zipped</a>");
					   },
					   error : function(XMLHttpRequest, textStatus, errorThrown){
						   $.showMsg({
								msg:XMLHttpRequest.responseText,
								type: 'error'});
						  
					   },
					   complete : function(msg){
						   $('.loadAnim').hide();
						   $('#btn_backup').button( "option", "disabled", false );
					   }
				}); //end ajax
				
			});

		
			
            
            
                    
		
				
			
	});  //close document ready
</script>
</head>
<body>
<div id="wrap">
	<div id="headwrap">
		<?php include "inc/menu2.inc.php" ?>
	</div>
	<!-- end of headwrap -->
	
	
	<div id="stagewrap" class="ui-widget">
	
		<div id="titlewrap">
		    <h1><?php echo $Text['ti_mng_db']; ?></h1>	  	
		</div>
			
		<p id="msg_link"><?php echo $Text['confirm_db_backup'];?></p>
		<br/><br/>
		<p><span class="loadAnim floatLeft hidden"><img src="img/ajax-loader_fff.gif"/></span>&nbsp;&nbsp; <button id="btn_backup">OK, backup!</button></p>
		<br/><br/>
		<p id="dbError" class="width-280"></p>
		
	</div>
	<!-- end of stage wrap -->
</div>
<!-- end of wrap -->



<!-- / END -->
</body>
</html>