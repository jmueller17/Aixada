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


		//saves selected uf row 
		var gSelUfRow = null;


		//saves currently selecte member row
		var gSelMemberRow = null;

		
		
		
		/*******************************************
		 *		UFS LISTING
		 *******************************************/
				
		//init the uf listing
		$('#uf_list tbody').xml2html('init',{
			url: 		'ctrlUserAndUf.php',
			params : 	'oper=getUfListing&all=1',
			loadOnInit:	true,
			//resultsPerPage:20,
			//paginationNav : '#uf_list tfoot td',
			beforeLoad : function(){
				$('#uf_listing .loadAnim').show();
			},
			rowComplete: function(index, row){
				var ckbx = row.children().first().find('input');
				if (ckbx.val() == "1") ckbx.attr('checked',true); //set the checkbox if uf is active or not
			},
			complete : function(){
				$('#uf_listing .loadAnim').hide();
				$('#uf_list tbody tr:even').addClass('rowHighlight'); 
			}
		});	


		
		//handle events on uf list
		$('#uf_list tbody tr')
			.live('mouseenter', function(){
				$(this).addClass('ui-state-hover');
			})
			.live('mouseleave',function(){
				$(this).removeClass('ui-state-hover');
			})
			//click on uf table row
			.live('click',function(e){

				if (gSelUfRow != null) gSelUfRow.removeClass('ui-state-highlight');
			
				$(this).addClass('ui-state-highlight');
				
				gSelUfRow = $(this); 
					
				$('#uf_detail_member_list').xml2html('reload',{
					params: "oper=getMemberInfo&uf_id="+gSelUfRow.attr('ufid')
				});
				switchTo('ufMemberView');				
			});
		


		
		//load mentor uf select listing
		$('#mentor_uf').xml2html('init',{
			url: 'ctrlUserAndUf.php',
			params : 'oper=getUfListing&all=0',
			loadOnInit:true,
			offSet : 1
		});	


		//menu for creating new uf or member
		$("#btn_new")
			.button({
				icons: {
		        	secondary: "ui-icon-triangle-1-s"
				}
		    })
		    .menu({
				content: $('#createNewItems').html(),	
				showSpeed: 50, 
				width:150,
				flyOut: true, 
				itemSelected: function(item){	

					switch ($(item).attr('id')){
						case 'newUf':
							$("#create_uf_name").val('');
							$('#mentor_uf').xml2html('reload');
							$("#dialog-uf").dialog( "open" );
							break;
						case 'newMember':
							break;
					}
				}//end item selected 
			});//end menu
		

		//create new uf	 
		$("#dialog-uf").dialog({
			autoOpen: false,
			height: 330,
			width: 450,
			modal: true,
			buttons: {
				"<?=$Text['btn_create'];?>": function() {

					//check if uf name exists
					$.ajax({
						type: "POST",
	                    url: 'ctrlUserAndUf.php?oper=checkFormField&table=aixada_uf&field=name&value='+$('#create_uf_name').val(), 
				        success :  function(msg){
					       if (msg == 1){
					    	   $.showMsg({
									msg: 'Your UF name already exist. Please choose another one!',
									type: 'error'
								});
								
						   } else {

							 	//create new uf
								$.ajax({
									type: "POST",
					                url: 'ctrlUserAndUf.php?oper=createUF&name='+$('#create_uf_name').val()+'&&mentor_uf='+$('#mentor_uf').val(), 
									dataType : 'xml',
							        success :  function(xml){
										$("#dialog-uf").dialog( "close" );
										$('#uf_list tbody').xml2html("reload");
										
									}, 
									error : function(XMLHttpRequest, textStatus, errorThrown){
										$.showMsg({
											msg: XMLHttpRequest.responseText,
											type: 'error'
										});
								   	},
								   	complete: function(){
								   		$('#dialog_uf .loadAnim').hide();	
									}  		
								});
							 	
							}
						}, 
						error : function(XMLHttpRequest, textStatus, errorThrown){
							$.showMsg({
								msg: XMLHttpRequest.responseText,
								type: 'error'
							});
					   	}
					});

					
					
				},
				"<?php echo $Text['btn_cancel'];?>": function() {
					$( this ).dialog( "close" );
				}
			}
		});


		

		


		/*******************************************
		 *		MEMBER LISTING
		 *******************************************/


		//init the uf member listing
		$('#member_list tbody').xml2html('init',{
			url : "ctrlUserAndUf.php",
			params: "oper=getMemberListing&all=1",
			loadOnInit:true,
			beforeLoad : function(){
				$('#member_listing .loadAnim').show();
			},
			complete : function(){
				$('#member_listing .loadAnim').hide();
				$('#member_list tbody tr:even').addClass('rowHighlight'); 
			}
		});



		//handle events on uf list
		$('#member_list tbody tr')
			.live('mouseenter', function(){
				$(this).addClass('ui-state-hover');
			})
			.live('mouseleave',function(){
				$(this).removeClass('ui-state-hover');

			})
			//click on uf table row
			.live('click',function(e){

				if (gSelMemberRow != null) gSelMemberRow.removeClass('ui-state-highlight');

				$(this).addClass('ui-state-highlight');
				
				gSelMemberRow = $(this);

				$('#uf_detail_member_list').xml2html('reload',{
					params: "oper=getMemberInfo&member_id="+gSelMemberRow.attr('memberId')
				});
				
				switchTo('memberView');				
			});

		

		//init the non / new member listing
		$('#non_uf_member_list tbody').xml2html('init',{
			url : "ctrlUserAndUf.php",
			params : "oper=getUsersWithoutUF",
			loadOnInit:false
		});
		

		
		
		/*******************************************
		 *		UF MEMBER EDIT
		 *******************************************/

			
		$('#uf_detail_member_list').xml2html('init',{
			url : "ctrlUserAndUf.php",
			params: "oper=getMemberInfo&uf_id=",
			loadOnInit:false,
			beforeLoad : function(){
				//$('#member_listing .loadAnim').show();
			},
			rowComplete : function (rowIndex, row){
			},
			complete : function(){
				//$('#member_listing .loadAnim').hide();

				$('.tblForms input:checkbox').each(function(){
					var bool = $(this).val(); 
					if (bool == "1") $(this).attr('checked',true);
				});
				
				$('.btn_edit_member').button({
					icons: {primary: "ui-icon-check"}
				}).click(function(e){
					var ds = $(this).parents('form').serialize();
					var $this = $(this);
					$.ajax({
						   	url: "ctrlUserAndUf.php?oper=updateMember",
							data: ds, 
						   	beforeSend: function(){
							   	$this.button('disable');
							   //$('#uf_listing .loadAnim').show();
							},
						   	success: function(msg){
						   	 	$.showMsg({
									msg: "The new member data has been successfully saved!",
									type: 'success'});
						   	},
						   	error : function(XMLHttpRequest, textStatus, errorThrown){
							   $.showMsg({
									msg: XMLHttpRequest.responseText,
									type: 'error'});
								
						   	},
						   	complete : function(msg){
							   $this.button('enable');
							   //$('#uf_listing .loadAnim').hide();
						   	}
					}); //end ajax

					return false; 
				});
				
				$('.btn_reset_pwd').button();

				
			}
		});



		
		
		
		//activate / deactive uf
		$('input[name=uf_active]')
			.live('click',function(){


			});
		
	

		

		
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
						gPrevId = -1;
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
						$('#member_list tbody').xml2html('reload',{
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
		


		/**
		 * 	three pages contained in one: uf list/edit, members list/edit, 
		 */
		function switchTo(section){

			switch(section){
				case 'overview':
					$('.overviewElements').show();
					$('.viewMemberElements, .ufDetailElements').hide();
					break;
				case 'memberView':
					$('.overviewElements, .ufDetailElements').hide();
					$('.viewMemberElements').fadeIn(1000);
					break;
				case 'ufMemberView':
					$('.overviewElements').hide();
					$('.setUfId').text(gSelUfRow.attr('ufid'));
					$('.ufDetailElements, viewMemberElements').fadeIn(1000);
					break;
					
				default: 
					$('.overviewElements').show();
					$('.viewMemberElements, .ufDetailElements').hide();
			}

		}

		/**
		 *	returns to order overview 
		 */
		$("#btn_overview").button({
			 icons: {
	        		primary: "ui-icon-circle-arrow-w"
	        	}
			 })
    		.click(function(e){
				switchTo('overview'); 
    		});
		

		switchTo('overview');
				
			
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
					<button id="btn_overview" class="floatLeft ufDetailElements viewMemberElements"><?php echo $Text['overview'];?></button>
		    		<h1 class="overviewElements">Manage households and its members</h1>
		    		<h1 class="viewMemberElements">Manage member</h1>
		    		<h1 class="ufDetailElements">Household <span class="setUfId"></span> and its members</h1>
		    </div>
		    <div id="titleRightCol">
		    	<p>&nbsp;</p>
		    	<!-- p class="textAlignRight"><?php echo $Text['search_memberuf'];?>: <input type="text" name="search_member" id="search_member" class="inputTxtMiddle ui-widget-content ui-corner-all" /></p-->
		    	<button id="btn_new" class="overviewElements floatRight">New...</button>
		    	<div id="createNewItems" class="hidden hideInPrint">
					<ul>
						<li><a href="javascript:void(null)" id="newUf">Household</a></li>
						<li><a href="javascript:void(null)" id="newMember">Member</a></li>
					</ul>
				</div>	
		    </div>	  	
		</div>

		<div id="uf_listing" class="ui-widget overviewElements splitCol floatLeft">
			<div class="ui-widget-content ui-corner-all">
				<h2 class="ui-widget-header ui-corner-all">List of households <span class="loadAnim floatRight hidden"><img src="img/ajax-loader.gif"/></span></h2>
				<p id="ufMsg"></p>
				<form id="uf_edit">
				<table id="uf_list" class="tblListingDefault">
						<thead>
						<tr>
							<th>Active</th>
							<th><?=$Text['uf_short'];?></th>
							<th class="textAlignLeft"><?=$Text['name_person'];?></th>
							<th><?=$Text['mentor_uf'];?></th>
							<th>&nbsp;</th>
							
						</tr>
						</thead>
						<tbody>
							<tr ufid="{id}" class="clickable">
								<td><input type="checkbox" name="uf_active" value="{active}"/></td>
								<td><?=$Text['uf_short'];?>{id}</td>
								<td><p class="textAlignLeft">{name}</p></td>
                                <td>{mentor_uf}</td>
								<td><!-- span class="btn_edit_uf ui-icon ui-icon-pencil" title="<?php echo $Text['edit_uf'];?>"</span--></td>
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
		
		
		<div id="member_listing" class="ui-widget overviewElements splitCol floatRight">
			<div class="ui-widget-content ui-corner-all">
				<h2 class="ui-widget-header ui-corner-all"> List of members <span class="loadAnim floatRight hidden"><img src="img/ajax-loader.gif"/></span></h2>
			
				<table id="member_list" class="tblListingDefault">
						<thead>
						<tr>
							<th><?=$Text['id'];?></th>
							<th><?=$Text['name_person'];?></th>
							<th><?=$Text['active'];?></th>	
							<th><?=$Text['uf_short'];?></th>	
							<th>Contact</th>
						</tr>
						</thead>
						<tbody>
							<tr class="clickable" memberId="{id}">
								<td field_name="member_id">{id}</td>
								<td field_name="name"><p class="textAlignLeft">{name}</p></td>
								<td field_name="active">{active}</td>
								<td field_name="uf_id"><?=$Text['uf_short'];?>{uf_id}</td>
								<td field_name="phone1"><p class="textAlignLeft">
									{phone1} / {phone2}<br/>
									{email}
									</p>
								</td>
							</tr>						
						</tbody>
					</table>
			</div>
		</div>
		
		
		
		<div id="uf_member_detail" class="ui-widget">
			<div class="ui-widget-content ui-corner-all ufDetailElements">
				<h2  class="ui-widget-header ui-corner-all">uf sinfo</h2> 
				<div class="padding15x10">
				<p>Name: </p> 
				<p>Active:</p> 
				<p>Mentor uf</p>			
				</div>
			</div>
			<p>&nbsp;</p>
			
			<div id="uf_detail_member_list" class="ufDetailElements viewMemberElements">
				<div class="ui-widget-content ui-corner-all member-info">
				<h3 class="ui-widget-header padding10x5">{name} (<span class="setUfId"><?php echo $Text['uf_short'];?>{uf_id}</span>)</h3>
				<form>
				<input type="hidden" name="member_id" value="{id}"/>
				<input type="hidden" name="user_id" value="{user_id}"/>
				<table class="tblForms">
						<tr>
							<td><label for="login"><?php echo $Text['login'];?></label></td>
							<td><p class="textAlignLeft ui-corner-all">{login}</p></td>
							<td><label for="member_id">(=tr)Member id</label></td>
							<td><p class="textAlignLeft ui-corner-all">{id}</p></td>
							
						</tr>
						<tr>
							<td colspan="2"></td>
							<td><label for="custom_member_ref">(=tr)Custom ref</label></td>
							<td><input type="text" name="custom_member_ref" id="custom_member_ref" value="{custom_member_ref}" class="ui-widget-content ui-corner-all" /></td>
						</tr>
						<tr>
							<td><label for="name"><?php echo $Text['name_person'];?></label></td>
							<td><input type="text" name="name" id="name" value="{name}" class="ui-widget-content ui-corner-all" /></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td><label for="nif">(=tr)NIF</label></td>
							<td><input type="text" name="nif" id="nif" value="{nif}" class="ui-widget-content ui-corner-all" /></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td><label for="address"><?php echo $Text['address'];?></label></td>
							<td colspan="5"><input type="text" name="address" id="address" value="{address}" class="inputTxtMax ui-widget-content ui-corner-all" /></td>
						</tr>
						<tr>
							<td><label for="city"><?php echo $Text['city'];?></label></td>
							<td><input type="text" name="city" id="city" value="{city}" class="ui-widget-content ui-corner-all" /></td>
							<td><label for="zip"><?php echo $Text['zip'];?></label></td>
							<td><input type="text" name="zip" id="zip" value="{zip}" class=" ui-widget-content ui-corner-all" /></td>
							
						</tr>
						<tr>
							<td><label for="phone1"><?php echo $Text['phone1'];?></label></td>
							<td><input type="text" name="phone1" id="phone1" value="{phone1}" class="ui-widget-content ui-corner-all" /></td>
						
							<td><label for="phone2"><?php echo $Text['phone2'];?></label></td>
							<td><input type="text" name="phone2" id="phone2" value="{phone2}" class="ui-widget-content ui-corner-all" /></td>
						</tr>
						<tr>
							<td><label for="email"><?php echo $Text['email'];?></label></td>
							<td><input type="text" name="email" id="email" value="{email}" class="ui-widget-content ui-corner-all" /></td>
						</tr>
						<tr>
							<td><label for="web"><?php echo $Text['web'];?></label></td>
							<td colspan="5"><input type="text" name="web" id="web" value="{web}" class="inputTxtMax ui-widget-content ui-corner-all" /></td>
						</tr>
						<tr>
							<td><label for="notes"><?php echo $Text['notes'];?></label></td>
							<td colspan="5"><input type="text" name="notes" id="notes" value="{notes}" class="inputTxtMax ui-widget-content ui-corner-all" /></td>
						</tr>
						<tr>
							<td><label for="member_active"><?php echo $Text['active'];?></label></td>
							<td><input type="checkbox" name="member_active" value="{active}" id="member_active" class="floatLeft" /></td>
							<td><label for="participant"><?php echo $Text['participant'];?></label></td>
							<td><input type="checkbox" name="participant" value="{participant}" id="participant" class="floatLeft" /></td>
						</tr>
						<tr>
							<td>&nbsp;
							</td>
						</tr>
						<tr>
							<td><label for="default_theme">(=tr)Theme:</label></td>
							<td colspan="2"><p class="textAlignLeft ui-corner-all">{gui_theme}</p></td>
						</tr>
						<tr>
							<td><label for="last_seen">(=tr)Last seen:</label></td>
							<td colspan="2"><p class="textAlignLeft ui-corner-all">{last_successful_login}</p></td>
						</tr>
						<tr>
							<td><label for="languageSelect"><?php echo $Text['lang']; ?>:</label></td>
							<td>
								<p class="textAlignLeft ui-corner-all">{language}</p>
								<select id="languageSelect" name="language" class="hidden">
									<option value="{language}"> </option>
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="2"></td>
							<td><p class="floatRight"><button class="btn_edit_member" memberid="{id}"><?php echo $Text['btn_save'];?></button></p></td>
							<td><p class="floatRight"><button class="btn_reset_pwd" memberid="{id}"><?php echo $Text['btn_reset_pwd'];?></button></p></td>
						</tr>
					</table>
					</form>
					<p>&nbsp;</p>
					<table class="tblForms">
						<tr>
							<td><?php echo $Text['active_roles'];?></td>
							<td><p class="textAlignLeft">{roles}</p></td>
						</tr>
						<tr>
							<td><?php echo $Text['providers_cared_for'];?>:</td>
							<td><p class="textAlignLeft">{providers}</p></td>
							
						</tr>
						<tr>
							<td><?php echo $Text['products_cared_for'];?>:</td>
							<td><p class="textAlignLeft">{products}</p></td>
						</tr>
						<tr><td>&nbsp;</td></tr>
					</table>
			</div>
			
						
		</div>
		
		
			
		<div id="non_member_listing" class="ui-widget hidden">
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
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr>
					<td class="textAlignRight"><label for="mentor_uf"><?php echo $Text['mentor_uf'];?></label></td>
					<td><select id="mentor_uf">
							<option value="-1" selected="selected"><?=$Text['sel_uf']; ?></option>
							<option value="{id}">{id} {name}</option>		
						</select>
					</td>
				</tr>
		</table>
	</form>
</div>




<!-- / END -->
</body>
</html>