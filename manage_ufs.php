<?php include "inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_manage'] . " " . $Text['head_ti_manage_uf'] ;?></title>
	
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
   	    <script type="text/javascript" src="js/js_for_manage_ufs.min.js"></script>
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

		//load mentor uf select listing
		$('#mentor_uf').xml2html('init',{
			url: 'smallqueries.php',
			params : 'oper=getActiveUFs',
			loadOnInit:true,
			offSet : 1
		});	

		//init the uf member listing
		$('#uf_member_list tbody').xml2html('init',{
			url : "smallqueries.php",
			beforeLoad : function(){
				$('#member_listing .loadAnim').show();
			},
			complete : function(){
				$('#member_listing .loadAnim').hide();
			}
		});

		//init the non / new member listing
		$('#non_uf_member_list tbody').xml2html('init',{
			url : "smallqueries.php",
			params : "oper=getUsersWithoutUF",
			loadOnInit:true
		});
		

		/**
		 *		create uf
		 */
		$("#create_uf")
			.button({
				icons: {
					primary: "ui-icon-plus"}
			})
			.click(function() {
				$("#create_uf_name").val('');
				$('#mentor_uf').xml2html('reload');
				$("#dialog-uf").dialog( "open" );
		});
		 
		$("#dialog-uf").dialog({
			autoOpen: false,
			height: 180,
			width: 450,
			modal: true,
			buttons: {
				"<?=$Text['btn_create'];?>": function() {
					var bValid = true;
					if ( bValid ) {
						
						//function to create new uf
						$.ajax({
							type: "POST",
                            url: 'ctrlUser.php?oper=createUF&name='+$('#create_uf_name').val()+'&active=true&mentor_uf='+$('#mentor_uf').val(), 
							dataType : 'xml',
					        success :  function(xml){
								$("#dialog-uf").dialog( "close" );
								//$("#uf_select").xml2html("reload");
								$('#uf_list tbody').xml2html("reload");
								
							}, 
							error : function(XMLHttpRequest, textStatus, errorThrown){
						    	$.updateTips('#ufCreateMsg','error', XMLHttpRequest.responseText);
						   	},
						   	complete: function(){
						   		$('#dialog_uf .loadAnim').hide();
						   		
							}  		
						});
					}
				},
				"<?php echo $Text['btn_cancel'];?>": function() {
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				//allFields.val( "" ).removeClass( "ui-state-error" );
			}
		});

		
		
		
		//handle events on uf list
		$('#uf_list tbody tr')
			.live('mouseenter', function(){
				$(this).children().addClass('ui-state-highlight');
			})
			.live('mouseleave',function(){
				if (!$(this).hasClass('active_row')){
					$(this).children().removeClass('ui-state-highlight');
				}
			})
			//click on uf table row
			.live('click',function(e){
				var id = $(this).attr('ufid');

				//deselect previsou active row and make this one active  but only if row changes
				if (id != prev_id){
					resetPreviousRow();
					highlightCurrentRow($(this));
				}
				prev_id = id; 		
				
				//load members for this uf
				$(".uf_id").html(id);
				$('#uf_member_list tbody').xml2html('reload',{
					params	: 'oper=getMembersOfUF&uf_id='+id,
				});				
			});
		var prev_id = -1;
		

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

		//activate / deactive uf
		$('input[name=uf_active]')
			.live('click',function(){

				if ($(this).closest('tr').children('td:last').find('span').hasClass('ui-icon-pencil')){
					
					$(this).closest('tr').children('td:last').find('span').removeClass('ui-icon-pencil').addClass('ui-icon-disk');			
					var id = $(this).parent().parent().attr('ufid');
					
					if (id != prev_id){
						resetPreviousRow();
						highlightCurrentRow($(this).parent().parent());
					}
					prev_id = id; 
					makeEditable($(this).parent().next().next(),'uf_name','uf_name');
				}
			});
		
		//in order to restore values of an edited row which does not get saved, the orignal is cloned and then restored. 
		var current_row = null;
		function highlightCurrentRow(table_tr){
			var tmp = table_tr.clone();
			table_tr.after(tmp);
			table_tr.next().hide();
			table_tr.addClass('active_row');
			current_row = table_tr;

		}

		function resetPreviousRow(){
			if (current_row != null) {
				current_row.next().show().children().removeClass('ui-state-highlight');
				current_row.remove();
				current_row = null;
			}
		}

		function makeEditable(uf_nameTD, fieldID, fieldName){
			var txt = uf_nameTD.html();
			uf_nameTD.empty().append('<input type="text" class="ui-widget-content ui-corner-all" id="'+fieldID+'" name="'+fieldName+'" value="'+txt+'"/>');
			$('#mentor_uf').clone().appendTo(uf_nameTD.next().empty()); 
			
		}

		
		$('#uf_edit').submit(function(){
			
			var uf_id = $('.active_row').attr('ufid'); 
			var uf_name = $('.active_row').find('input[name=uf_name]').val(); 
			var active = $('.active_row').find('input[name=uf_active]').attr('checked'); 
			
			$('.active_row').find('.btn_edit_uf').removeClass('ui-icon-disk').addClass('ui-icon-pencil');
			
			
			$.ajax({
				   url: "ctrlUser.php?oper=updateUF&uf_id="+uf_id +"&name="+uf_name+"&active="+active+'&mentor_uf='+$('#mentor_uf').val(),
				   beforeSend: function(){
					   $('#uf_listing .loadAnim').show();
					},
				   success: function(msg){
						//closeEditable($('#uf_name'));	
						prev_id = -1;
					   $('#uf_list tbody').xml2html('reload'); 			
				   },
				   error : function(XMLHttpRequest, textStatus, errorThrown){
					   $.updateTips('#ufMsg','error', XMLHttpRequest.responseText);
				   },
				   complete : function(msg){
					   $('#uf_listing .loadAnim').hide();
				   }
			}); //end ajax
			
			
			return false;
		});

		
		

		/**
		 *	assign stuff
		 */
		$('#btn_assign')
			.button({
				icons: {
					primary: "ui-icon-arrowthickstop-1-w"}
			})
			.click(function(){
				$('#assign_user2uf').submit();
			});

		
		$('#assign_user2uf').submit(function(){
			var dataSerial = $(this).serialize();
            var uf_id = parseInt($(".uf_id").text());

          //check if non-member is selected
            var nonChecked = true; 
			$("#non_member_listing input[type=checkbox]").each( function() { 
				if($(this).attr("checked")){
       				nonChecked = false;
				} 
    		});
    		
			if (nonChecked) {
	            $.showMsg({
					msg:"<?=$Text['msg_err_select_non_member'];?>",
					type: 'error'
				});
				return false;
	        }
            
			//check if an uf is selected
            if (isNaN(uf_id) || uf_id < 0) {
            	$.showMsg({
					msg:"<?=$Text['msg_err_select_uf'];?>",
					type: 'error'
				});
				return false;
            }

			
			
            
            $('#btn_assign').button( "option", "disabled", true );
                    
			$.ajax({
				   url: "smallqueries.php?oper=assignUsersToUF&uf_id="+uf_id,
				   data: dataSerial,
				   success: function(msg){
						$('#uf_member_list tbody').xml2html('reload',{
							params	: 'oper=getMembersOfUF&uf_id='+uf_id,
						});		
						$('#non_uf_member_list tbody').xml2html('reload');				
				   },
				   error : function(XMLHttpRequest, textStatus, errorThrown){
					   $.updateTips('#assignMsg','error', XMLHttpRequest.responseText);
				   },
				   complete : function(msg){
					   $('#btn_assign').button( "option", "disabled", false );
				   }
			}); //end ajax
			return false; 

		});
		
		$('#btn_remove').button({
			icons: {secondary: "ui-icon-arrowthick-1-w"}
		}).click(function(){
			 
			
		}).hide();
		
				
			
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
				
			<div id="titleLeftCol">
		    		<h1><?php echo $Text['ti_mng_ufs']; ?></h1>
		    </div>
		    <div id="titleRightCol">
		    	<p>&nbsp;</p>
		    	<!-- p class="textAlignRight"><?php echo $Text['search_memberuf'];?>: <input type="text" name="search_member" id="search_member" class="inputTxtMiddle ui-widget-content ui-corner-all" /></p-->
		    </div>
		    <div id="titleSub">
		    	 <p><button id="create_uf"><?=$Text['create_uf'];?></button></p>
		    </div>
				  	
		</div>

		<div id="uf_listing" class="ui-widget floatLeft">
			<div class="ui-widget-content ui-corner-all">
				<h2 class="ui-widget-header ui-corner-all"> <?=$Text['uf_short'];?> <span class="loadAnim floatRight hidden"><img src="img/ajax-loader.gif"/></span></h2>
				<p id="ufMsg"></p>
				<form id="uf_edit">
				<table id="uf_list">
						<thead>
						<tr>
							<th><?=$Text['active'];?></th>
							<th><?=$Text['id'];?></th>
							<th><?=$Text['name_person'];?></th>
							<th><?=$Text['mentor_uf'];?></th>
							<th>&nbsp;</th>
							
						</tr>
						</thead>
						<tbody>
							<tr ufid="{id}">
								<td><input type="checkbox" name="uf_active" value="{active}"/></td>
								<td>{id}</td>
								<td>{name}</td>
                                <td>{mentor_uf}</td>
								<td><span class="btn_edit_uf ui-icon ui-icon-pencil" title="<?php echo $Text['edit_uf'];?>"></td>
							</tr>						
						</tbody>
						<tfoot>
							<tr>
								<td colspan="5"></td>
							</tr>
						</tfoot>
					</table>
					</form>
			</div>
		</div>
		
		
		<div id="member_listing" class="ui-widget floatLeft minSidesMargin">
			<div class="ui-widget-content ui-corner-all">
				<h2 class="ui-widget-header ui-corner-all"> <?php echo $Text['members_uf'];?> <span class="uf_id">???</span><span class="loadAnim floatRight hidden"><img src="img/ajax-loader.gif"/></span></h2>
			
				<table id="uf_member_list" class="product_list" >
						<thead>
						<tr>
							<th><?=$Text['id'];?></th>
							<th><?=$Text['name_person'];?></th>
							<th><?=$Text['phone1'];?></th>
							<th><?=$Text['phone2'];?></th>
							<th><?=$Text['email'];?></th>
							<th><?=$Text['active'];?></th>
						</tr>
						</thead>
						<tbody>
							<tr class="xml2html_tpl">
								<td field_name="member_id">{id}</td>
								<td field_name="name">{name}</td>
								<td field_name="phone1">{phone1}</td>
								<td field_name="phone2">{phone2}</td>
								<td field_name="email">{email}</td>
								<td field_name="active">{active}</td>
							</tr>						
						</tbody>
					</table>
			</div>
		</div>
		
			
		<div id="non_member_listing" class="ui-widget floatRight">
			<div class="ui-widget-content ui-corner-all">
				<h2 class="ui-widget-header ui-corner-all"> <?php echo $Text['unassigned_members'];?></h2>
				<p id="assignMsg"></p>
				<form id="assign_user2uf">
				<table id="non_uf_member_list" class="product_list" >
						<thead>
						<tr>
							<th>&nbsp;</th>
							<th><?php echo $Text['id'];?></th>
							<th><?php echo $Text['logon'];?></th>
							<th><?php echo $Text['created'];?></th>
						</tr>
						</thead>
						<tbody>
							<tr class="xml2html_tpl">
								<td><input type="checkbox" name="user_id[]" value="{id}"/></td>
								<td>{id}</td>
								<td>{login}</td>
								<td>{created}</td>					
							</tr>						
						</tbody>
						<tfoot>
							<tr>
								<td colspan="2" class="floatLeft"><button id="btn_assign"><?=$Text['btn_assign'] ?></button></td>
							</tr>
						</tfoot>
					</table>
					</form>
			</div>
		</div>
		
		
		
		
	</div>
	<!-- end of stage wrap -->
</div>
<!-- end of wrap -->


<div id="dialog-uf" title="<?php echo $Text['create_uf'];?>">
	<p id="ufCreateMsg"></p>
	<!-- span class="loadAnim floatRight hidden"><img src="img/ajax-loader.gif"/></span-->
	<form id="uf_form">	
		<table>
				<tr>
					<td><label for="create_uf_name"><?php echo $Text['name_person'];?></label></td>
					<td><input type="text" name="create_uf_name" id="create_uf_name" class="ui-widget-content ui-corner-all"/></td>
				</tr>
				<tr>
					<td><label for="mentor_uf"><?php echo $Text['mentor_uf'];?></label></td>
					<td><select id="mentor_uf">
						<option value="-1" selected="selected"><?=$Text['sel_uf']; ?></option>
						<option value="{id}">{id} {name}</option>
							
						</select></td>
				</tr>
		</table>
	</form>
</div>


<!-- / END -->
</body>
</html>