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


		var gEmptyMemberForm = $('#uf_detail_member_list').children(':first').clone();

		//set to true once theme and lang select have been loaded
		var gFormComplete = false; 

		
		//load available languages
		 $("#languageSelect")
			.xml2html("init", {
					url: "ctrlSmallQ.php",
					params : "oper=getExistingLanguages",
					rowName : "language",
					loadOnInit: false,
					complete : function(s){
						//insert the language and theme select into the member form. 
						
						var langSelect = $('#languageSelect').clone(); 
						var themeSelect = $('#themeSelect').clone(); 

						$('.memberLanguageSelect', gEmptyMemberForm).append(langSelect);						
						$('.memberThemeSelect', gEmptyMemberForm).append(themeSelect);

						//clone it for further use. 
						gEmptyMemberForm.find('input:text, input:hidden').val('');

						gFormComplete = true; 
							
					}
		});

		//load available themes
		 $("#themeSelect")
			.xml2html("init", {
					url: "ctrlSmallQ.php",
					params : "oper=getExistingThemes",
					rowName : "theme",
					loadOnInit: true,
					complete : function(s){
						$("#languageSelect").xml2html("reload");

					}
		});


			
		
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
			offSet : 1,
			complete:function(rowCount){

				 var sel = $('#mentor_uf').clone();
				 $('.mentor_uf').append(sel);

			}
		});	


		//new uf
		$("#btn_new_uf")
			.button()
		    .click(function(e){
		    	$('#create_uf_name').val('');
				//$('#mentor_uf').xml2html('reload');
				$('#dialog-uf').dialog( "open" );

			});

		//edit uf
		$("#btn_edit_uf")
			.button({
				icons : {secondary:"ui-icon-pencil"}
			})
		    .click(function(e){
		    	$('#uf_info').find('input').removeAttr('disabled');
		    	$('#mentor_uf').removeAttr('disabled');
				$(this).hide();
		    	$('#btn_edit_uf_save').show();
			});
		
		$("#btn_edit_uf_save")
			.button({
				icons : {secondary:"ui-icon-disk"}
			})
			.click(function(e){
				submitUF(gSelUfRow.attr('ufid'));
			})
			.hide();
		

				

		//create new uf	 
		$("#dialog-uf").dialog({
			autoOpen: false,
			height: 330,
			width: 450,
			modal: true,
			buttons: {
				"<?=$Text['btn_create'];?>": function() {
					submitUF(0);
				},
				"<?php echo $Text['btn_cancel'];?>": function() {
					$( this ).dialog( "close" );
				}
			}
		});



		//activate / deactive uf
		$('input[name=uf_active]')
			.live('click',function(e){

				var is_active = $(this).attr('checked')? 1:0; 
				
				//toggle active state of uf. 
				$.ajax({
					type: "POST",
	                url: 'ctrlUserAndUf.php?oper=editUF&is_active='+is_active+'&uf_id='+$(this).parents('tr').attr('ufid')+'&name='+$(this).parents('tr').attr('ufname')+'&mentor_uf='+$(this).parents('tr').attr('mentoruf'), 
			        success :  function(msg){
			        	$.showMsg({
							msg: 'The active state has been successful changed for HU'+$(this).parents('tr').attr('ufid'),
							type: 'success'
						});
					}, 
					error : function(XMLHttpRequest, textStatus, errorThrown){
						$.showMsg({
							msg: XMLHttpRequest.responseText,
							type: 'error'
						});
				   	},
				   	complete: function(){
				   		//$('#dialog_uf .loadAnim').hide();	
					}  		
				});

				e.stopPropagation(); 

		});


		/**
		 *	submits create/edit uf data
		 */
		function submitUF(ufId){

			
		   	var mentorUf = $('#mentor_uf option:selected').val(); 
			
			if (ufId > 0){
		   		var isActive = $('#uf_info').find('input:checkbox').attr('checked')? 1:0;
			   	var ufName = $('#uf_info').find('input:text').val();   							   	
				var urlStr =  'ctrlUserAndUf.php?oper=editUF&uf_id='+ufId+'&is_active='+isActive+'&name='+ufName+'&mentor_uf='+mentorUf; 
				var stupidHack = 's9328820398023948'; 
				if (ufId == mentorUf){
					$.showMsg({
						msg: 'The mentor household must be different from the HU itself! ',
						type: 'error'
					});
					return false; 
				}

			//create 
			} else {
				var ufName = $('#create_uf_name').val(); 
				var urlStr = 'ctrlUserAndUf.php?oper=createUF&name='+ufName+'&mentor_uf='+mentorUf;
			}

			$.ajax({
				type: "POST",
                url: 'ctrlUserAndUf.php?oper=checkFormField&table=aixada_uf&field=name&value='+ufName+stupidHack, 
		        success :  function(msg){
		        	//check if uf name exists
			       if (msg == 1){
			    	   $.showMsg({
							msg: 'Your UF name already exist. Please choose another one! ',
							type: 'error'
						});
				   } else { 
					   
					 	//create/edit uf
						$.ajax({
							type: "POST",
							url : urlStr,
					        success :  function(msg){
					        	$.showMsg({
									msg: "The data has been successfully saved!",
									type: 'success'
								});
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
						   		$('#uf_info')
						   			.find('input').attr('disabled', 'disabled')
						   			.find('select').attr('disabled', 'disabled');
								$('#btn_edit_uf_save').hide();
						    	$('#btn_edit_uf').show();
						   			
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
		}


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
			rowComplete: function(rowIndex, row){
				//copy the language select with the right option selected
				var selectedLang = $('.memberLanguageSelect', row).prev().text();
				var langSelect = $('#languageSelect').clone(); 
				$(langSelect).val(selectedLang).attr('selected','selected');
				$('.memberLanguageSelect', row).append(langSelect);

				//copy the theme select with the right option selected
				var selectedTheme = $('.memberThemeSelect', row).prev().text();
				var themeSelect = $('#themeSelect').clone(); 
				$(themeSelect).val(selectedTheme).attr('selected','selected');
				$('.memberThemeSelect', row).append(themeSelect);
			},
			complete : function(){
				//$('#member_listing .loadAnim').hide();

				
				//set the checkboxes
				$('.tblForms input:checkbox').each(function(){
					var bool = $(this).val(); 
					if (bool == "1") $(this).attr('checked',true);
				});

				//each member gets an edit button
				$('.btn_save_edit_member').button({
						icons: {primary: "ui-icon-disk"}
					}).live('click', function(e){
						var ds = $(this).parents('form').serialize();
						submitMember("ctrlUserAndUf.php?oper=updateMember", ds, $(this));
						return false; 
					});

				
			}
		});

		//new member
		$("#btn_add_member")
			.button({
				icons:{secondary:'ui-icon-plus'}
			})
		    .click(function(e){
				var emptyForm = gEmptyMemberForm.clone();

				//save button
				$('.btn_save_new_member', emptyForm).button({
					icons: {primary: "ui-icon-disk"}
				}).live('click', function(e){
					var ds = $(this).parents('form').serialize();
					submitMember("ctrlUserAndUf.php?oper=createUserMember", ds, $(this));
					return false;  
				});

				//new member cancel
				$(".btn_cancel_new_member")
					.button({
						icons:{secondary:'ui-icon-cancel'}
					})
				    .live('click',function(e){
						$(this).parents('div').remove();	

					});

				//set the checkboxes
				$('.tblForms input:checkbox', emptyForm).each(function(){
					$(this).attr('checked','checked');
				});

				//set the uf_id we are creating the member for. 
				$('input[name=uf_id]', emptyForm).val(gSelUfRow.attr('ufid'));
				$('h3',emptyForm).text('New member for HU'+gSelUfRow.attr('ufid'));
				$('.createMemberFormElements', emptyForm).show();
				
				$(emptyForm).prependTo('#uf_detail_member_list');

				switchTo('createMemberView');
				

			});

		//remove all eventual error styles on input fields. 
		/*$('input')
			.live('focus', function(e){
				$(this).removeClass('ui-state-error');
			});*/
		

		/**
		 *	submits the create/edit member data
		 */
		function submitMember(urlStr, sdata, myButton){

			var isValid = true; 
			var err_msg = ''; 
			//run some checks
			
			isValid = isValid && $.checkFormLength($('#login'),3,50);
			if (!isValid){
				err_msg += "<?=$Text['msg_err_usershort'];?>"; 
			}

			isValid = isValid &&  $.checkFormLength($('#reg_password'),4,15);
			if (!isValid){
				err_msg += "<br/><br/>" + "<?=$Text['msg_err_passshort'];?>"; 
			}
			
			isValid = isValid &&  $.checkPassword($('#reg_password'), $('#reg_password_ctrl'));
			if (!isValid){
				err_msg += "<br/><br/>" + "<?=$Text['msg_err_pwdctrl']; ?>";
			}

			isValid = isValid &&  $.checkFormLength($('#name'),4,15);
			if (!isValid){
				err_msg += "<br/><br/>" + "<?php echo $Text['name_person'] . $Text['msg_err_notempty']; ?>";
			}

			isValid = isValid &&  $.checkRegexp($('#phone1'),/^([0-9\s\+])+$/);
			if (!isValid){
				err_msg += "<br/><br/>" + "<?php echo $Text['phone1'] .  $Text['msg_err_only_num']; ?>";
			}

			isValid = isValid &&  $.checkRegexp($('#email'),/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
			if (!isValid){
				err_msg += "<br/><br/>" + "<?=$Text['msg_err_email'] ?>";
			}
			
			

			if (isValid){
			
				$.ajax({
				   	url: urlStr,
					data: sdata, 
				   	beforeSend: function(){
					   	myButton.button('disable');
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
					   myButton.button('enable');
					   //$('#uf_listing .loadAnim').hide();
				   	}
				}); //end ajax
				 
			} else {
				$.showMsg({
					msg:err_msg,
					type: 'error'});
				
			}

			

		}


		/**
		 * 	pages contained in one: uf list/edit, members list/edit, 
		 */
		function switchTo(section){

			switch(section){
				case 'overview':
					$('.overviewElements').show();
					$('.viewMemberElements, .ufDetailElements, .createMemberFormElements').hide();
					break;
					
				case 'memberView':
					$('.overviewElements, .ufDetailElements, .createMemberFormElements').hide();
					$('.viewMemberElements').fadeIn(200, function(){
						$('.createMemberFormElements').hide();
					});
		
					break;
					
				case 'ufMemberView':
					$('.overviewElements').hide();
					$('.setUfId').text(gSelUfRow.attr('ufid'));
					$('#uf_info input:text').val(gSelUfRow.attr('ufname'));
					if (gSelUfRow.children(':first').find('input:checkbox').attr('checked')){
						$('#uf_info input:checkbox').attr('checked','checked')
					}
					$('#uf_info input:checkbox').attr('disabled','disabled');
					$('#mentor_uf')
						.val(gSelUfRow.attr('mentoruf'))
						.attr('selected','selected')
						.attr('disabled','disabled');
					//$('.viewMemberElements:not(.createMemberFormElements)').show(); .. not working?

					$('.ufDetailElements, viewMemberElements').fadeIn(200, function(){
						$('.createMemberFormElements').hide();
					});

					break;
					
				case 'createMemberView':
					$('.viewMemberFormElements').hide();
					$('.createMemberFormElements, .viewMemberElements ').fadeIn(200);
					break;
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
		    		<h1 class="overviewElements">Manage households and its members </h1>
		    		<h1 class="viewMemberElements">Manage member </h1>
		    </div>
		    <div id="titleRightCol">
		    	<!-- p class="textAlignRight"><?php echo $Text['search_memberuf'];?>: <input type="text" name="search_member" id="search_member" class="inputTxtMiddle ui-widget-content ui-corner-all" /></p-->
		    	<button id="btn_new_uf" class="overviewElements floatRight">New UF...</button>
		    </div>	  	
		</div>
		<div id="uf_listing" class="ui-widget overviewElements splitCol floatLeft">
			<div class="ui-widget-content ui-corner-all">
				<h2 class="ui-widget-header ui-corner-all">List of households <span class="loadAnim floatRight hidden"><img src="img/ajax-loader.gif"/></span></h2>
				<p id="ufMsg"></p>
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
							<tr ufid="{id}" ufname="{name}" mentoruf="{mentor_uf}" class="clickable">
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
					
			</div>
		</div>
		
		
		<div id="member_listing" class="ui-widget overviewElements splitCol floatRight">
			<div class="ui-widget-content ui-corner-all">
				<h2 class="ui-widget-header ui-corner-all"> List of members <span class="loadAnim floatRight hidden"><img src="img/ajax-loader.gif"/></span></h2>
			
				<table id="member_list" class="tblListingDefault">
						<thead>
						<tr>
							<th><?=$Text['id'];?> &nbsp;&nbsp;</th>
							<th class="textAlignLeft"><?=$Text['name_person'];?></th>
							<th><?=$Text['active'];?></th>	
							<th><?=$Text['uf_short'];?></th>	
							<th>Contact</th>
						</tr>
						</thead>
						<tbody>
							<tr class="clickable" memberId="{id}">
								<td field_name="member_id">{id}&nbsp;&nbsp;</td>
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
			<div class="ui-widget-content ui-corner-all ufDetailElements adaptHeight">
				<h2  class="ui-widget-header ui-corner-all">Manage members of <?php echo $Text['uf_short'];?><span class="setUfId"></span> </h2> 
				<div id="uf_info" class="padding15x10 splitCol floatLeft">
					<table class="tblforms">
						<tr>
							<td class="minwidth-180">Name </td>
							<td><input class="ui-widget-content ui-corner-all" type="text" name="uf_info_name" id="uf_name" value="" disabled="disabled" /></td>
							<td>&nbsp;</td>
							<td>Active </td>
							<td><input type="checkbox" name="uf_info_active" /></td>
							<td colspan="5">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="10">&nbsp;</td>
						</tr>
						<tr>
							<td class="minwidth-180">Mentor HU: </td>
							<td>
								<p class="mentor_uf"></p>
							</td>
							<td>&nbsp;</td>
							<td colspan="2">&nbsp;</td>
							<td class="minwidth-180">
								<button id="btn_edit_uf" class="floatRight">Edit UF</button>
								<button id="btn_edit_uf_save" class="floatRight">Save</button>
							</td>
							<td>&nbsp;</td>
							<td class="minwidth-180"> 
								<button id="btn_add_member">New member</button>
							</td>
						</tr>
					</table>		
				</div>
				
			</div>
			<p>&nbsp;</p>
			
			<div id="uf_detail_member_list" class="ufDetailElements viewMemberElements">
				<div class="ui-widget-content ui-corner-all member-info">
				<h3 class="ui-widget-header padding10x5">{name} (<span class="setUfId"><?php echo $Text['uf_short'];?>{uf_id}</span>)</h3>
				<form id="frm_member">
				<input type="hidden" name="member_id" value="{id}"/>
				<input type="hidden" name="user_id" value="{user_id}"/>
				<input type="hidden" name="uf_id" value="{uf_id}"/>
				<table class="tblForms">
						<tr>
							<td><label for="login"><?php echo $Text['login'];?></label></td>
							<td>
								<p class="viewMemberFormElements textAlignLeft ui-corner-all">{login}</p>
								<input class="createMemberFormElements ui-widget-content ui-corner-all" type="text" name="login" id="login" value=""/>
							</td>
							<td><label for="member_id" class="viewMemberFormElements">Member id</label></td>
							<td><p class="textAlignLeft ui-corner-all viewMemberFormElements">{id}</p></td>
							
						</tr>
						<tr>
							<td colspan="2"></td>
							<td><label for="custom_member_ref">Custom ref</label></td>
							<td><input type="text" name="custom_member_ref" id="custom_member_ref" value="{custom_member_ref}" class="ui-widget-content ui-corner-all" /></td>
						</tr>
						
						<tr class="createMemberFormElements">
							<td><label class="formLabel" for="reg_password"><?=$Text['pwd'];?>:</label></td>
							<td><input type="password" class="ui-widget-content ui-corner-all" name="password" id="reg_password"></td>
						</tr>
						
						<tr class="createMemberFormElements">
							<td><label class="formLabel" for="reg_password_ctrl"><?=$Text['retype_pwd'];?>:</label></td>
							<td><input type="password" class="ui-widget-content ui-corner-all " name="password_ctrl" id="reg_password_ctrl"></td>
						</tr>
						
						<tr><td>&nbsp;</td></tr>
						
						<tr>
							<td><label for="name"><?php echo $Text['name_person'];?></label></td>
							<td><input type="text" name="name" id="name" value="{name}" class="ui-widget-content ui-corner-all" /></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td><label for="nif">NIF</label></td>
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
							<td colspan="5"><input type="text" name="email" id="email" value="{email}" class="ui-widget-content ui-corner-all" /></td>
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
						<tr class="viewMemberFormElements">
							<td><label for="last_seen" >Last seen:</label></td>
							<td colspan="2"><p class="textAlignLeft ui-corner-all">{last_successful_login}</p></td>
						</tr>
						<tr>
							<td><label for="default_theme">Theme:</label></td>
							<td colspan="2">
								<p class="textAlignLeft ui-corner-all hidden">{gui_theme}</p>
								<div class="memberThemeSelect"></div>
							</td>
						</tr>
						<tr>
							<td><label for="languageSelect"><?php echo $Text['lang']; ?>:</label></td>
							<td>
								<p class="textAlignLeft ui-corner-all hidden">{language}</p>
								<div class="memberLanguageSelect"></div>
							</td>
						</tr>
						<tr>
							<td colspan="2"></td>
							<td>
								<p class="floatRight">
									<button class="btn_save_edit_member viewMemberFormElements" memberid="{id}">Save changes</button>
									<button class="btn_save_new_member createMemberFormElements">Create member</button>
								</p>
							</td>
							<td>
								<p class="floatRight">
									<button class="btn_cancel_new_member createMemberFormElements"><?php echo $Text['btn_cancel'];?></button>
								</p>
							</td>
						</tr>
					</table>
					</form>
					<p>&nbsp;</p>
					<table class="tblForms viewMemberFormElements">
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
		<table class="tblForms">
				<tr>
					<td><label for="create_uf_name"><?php echo $Text['name_person'];?></label></td>
					<td><input type="text" name="create_uf_name" id="create_uf_name" class="ui-widget-content ui-corner-all"/></td>
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr>
					<td><label for="mentor_uf"><?php echo $Text['mentor_uf'];?></label></td>
					<td><select id="mentor_uf">
							<option value="-1" selected="selected"><?=$Text['sel_uf']; ?></option>
							<option value="{id}">{id} {name}</option>		
						</select>
					</td>
				</tr>
		</table>
	</form>
</div>

<div id="loadLanguageSelect" class="hidden">
<select id="languageSelect" name="language">
	<option value="{id}"> {description}</option>
</select>
</div>

<div id="loadThemeSelect" class="hidden">
<select id="themeSelect" name="gui_theme">
	<option value="{name}"> {name}</option>
</select>
</div>
<!-- / END -->
</body>
</html>