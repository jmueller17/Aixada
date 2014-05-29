<?php include "php/inc/header.inc.php" ?>
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
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaExport.js" ></script>
   	<?php  } else { ?>
   	    <script type="text/javascript" src="js/js_for_manage_ufs.min.js"></script>
    <?php }?>
    
   
	<script type="text/javascript">
	
	$(function(){

		//loading Spinner
		$('.loadSpinner').attr('src', "img/ajax-loader-<?=$default_theme;?>.gif").hide(); 

		//saves selected uf row 
		var gSelUfRow = null;


		//saves currently selecte member row
		var gSelMemberRow = null;


		//var gEmptyMemberForm = $('#uf_detail_member_list').children(':first').clone();

		//set to true once theme and lang select have been loaded
		var gFormComplete = false; 

		//should be changed to non-global 
		var gMentorUf = -1; 
		


		//show/hide reset pwd. 
		var isAdmin = "<?=$_SESSION['userdata']['current_role'];?>";
		isAdmin = (isAdmin == "Hacker Commission")? true:false; 

			
		$('#member_listing').tabs();
		
		
		//load available languages
		 $("#languageSelect")
			.xml2html("init", {
					url: "php/ctrl/SmallQ.php",
					params : "oper=getExistingLanguages",
					rowName : "language",
					loadOnInit: false,
					complete : function(s){
						//insert the language and theme select into the create member form. 
						prepareAddMemberForm(true);
					}
		});

		//load available themes
		 $("#themeSelect")
			.xml2html("init", {
					url: "php/ctrl/SmallQ.php",
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
			url: 		'php/ctrl/UserAndUf.php',
			params : 	'oper=getUfListing&all=1',
			loadOnInit:	true,
			beforeLoad: function(){
				$('.loadSpinner').show();
			},
			//resultsPerPage:20,
			//paginationNav : '#uf_list tfoot td',
			beforeLoad : function(){
				//$('#uf_listing .loadAnim').show();
			},
			rowComplete: function(index, row){
				var ckbx = row.children().first().find('input');
				if (ckbx.val() == "1") ckbx.attr('checked',true); //set the checkbox if uf is active or not
			},
			complete : function(){
				$('.loadSpinner').hide();
				$('#uf_list tbody tr:even').addClass('rowHighlight'); 
			}
		});	


		
		//handle events on uf list
		$('#uf_list tbody tr')
			.live('mouseenter', function(){
				var uf_id = $(this).attr('ufId');
				$(this).addClass('ui-state-hover');
				
				//$('#member_list tbody tr.memberOfUf_'+uf_id).addClass('ui-state-hover');
				
			})
			.live('mouseleave',function(){
				var uf_id = $(this).attr('ufId');
				$(this).removeClass('ui-state-hover');
				//$('#member_list tbody tr.memberOfUf_'+uf_id).removeClass('ui-state-hover');
			})
			//click on uf table row
			.live('click',function(e){

				if (gSelUfRow != null) gSelUfRow.removeClass('ui-state-highlight');
			
				$(this).addClass('ui-state-highlight');
				
				gSelUfRow = $(this); 

				//only load fresh ones. otherwise use cash
				//if (!$.inArray(gSelUfRow.attr('ufid'), gUfMemberViewCache)) 	
					$('#uf_detail_member_list').xml2html('reload',{
						params: "oper=getMemberInfo&uf_id="+gSelUfRow.attr('ufid')
					});
				//}
				switchTo('ufMemberView');

			});
		


		
		//load mentor uf select listing
		$('#mentor_uf').xml2html('init',{
			url: 'php/ctrl/UserAndUf.php',
			params : 'oper=getUfListing&all=0',
			loadOnInit:true,
			offSet : 1,
			complete:function(rowCount){

				 var sel = $('#mentor_uf').clone();
				 $('.mentor_uf').append(sel);

			}
		}).change(function(){
			gMentorUf = $("option:selected", this).val();
			});	

			 
		//new uf
		$("#btn_new_uf")
			.button({
				icons : {primary:"ui-icon-plus"}
			})
		    .click(function(e){
		    	$('#create_uf_name').val('');
		    	//$('#mentor_uf').val("-1").attr('selected','selected')
				//$('#mentor_uf').xml2html('reload');
				$('#dialog-uf').dialog( "open" );

			});

		//edit uf
		$("#btn_edit_uf")
			.button({
				icons : {primary:"ui-icon-pencil"}
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
				var uf_id =  $(this).parents('tr').attr('ufid'); 

				$('.loadSpinner').show();
				//toggle active state of uf. 
				$.ajax({
					type: "POST",
	                url: 'php/ctrl/UserAndUf.php?oper=editUF&is_active='+is_active+'&uf_id='+$(this).parents('tr').attr('ufid')+'&name='+$(this).parents('tr').attr('ufname')+'&mentor_uf='+$(this).parents('tr').attr('mentoruf'), 
			        success :  function(msg){
			        	$.showMsg({
							msg: '<?php echo $Text['active_changed_uf']; ?>'+uf_id,
							autoclose: 1000, 
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
				   		$('.loadSpinner').hide();	
					}  		
				});

				e.stopPropagation(); 

		});



	    /**
	     *	export ufs
	     */
		$('#dialog_export_options').dialog({
			autoOpen:false,
			width:520,
			height:500,
			buttons: {  
				"<?=$Text['btn_ok'];?>" : function(){
						exportUfs(); 
					},
			
				"<?=$Text['btn_close'];?>"	: function(){
					$( this ).dialog( "close" );
					} 
			}
		});
	    
		$('#btn_export')
			.button({
				icons: {
					primary: "ui-icon-transferthick-e-w"
	        	}
			})
			.click(function(e){
				$('#dialog_export_options')
					.dialog("open");
			 })
			 .hide(); 

		function checkExportForm(){
			var frmData = $('#frm_export_options').serialize();
			if (!$.checkFormLength($('input[name=exportName]'),1,150)){
				$.showMsg({
					msg:"File name cannot be empty!",
					type: 'error'});
				return false;
			}
			return frmData; 
		}
		
		//initially hide authenticate and specific uf stuff. 
		$('#export_authentication').hide();


		/**
		 * EXPORT ufs
		 */
		function exportUfs(){
			var frmData = checkExportForm(); 
			if (frmData){
				var urlStr = "php/ctrl/ImportExport.php?oper=exportMembers&" + frmData; 
				//load the stuff through the export channel
				$('#exportChannel').attr('src',urlStr);
			}
		}


		/**
		 *	submits create/edit uf data
		 */
		function submitUF(ufId){

			
		   	var mentorUf = $('#mentor_uf option:selected').val(); 
			mentorUf = (mentorUf == -1)? gMentorUf:mentorUf; 

			//edit
			if (ufId > 0){
		   		var isActive = $('#uf_info').find('input:checkbox').attr('checked')? 1:0;
			   	var ufName = encodeURIComponent($('#uf_info').find('input:text').val());  
				var urlStr =  'php/ctrl/UserAndUf.php?oper=editUF&uf_id='+ufId+'&is_active='+isActive+'&name='+ufName+'&mentor_uf='+mentorUf; 
				stupidHack = 's9328820398023948'; //this is for the editing of uf name - prevents check if uf name already exists!
				if (ufId == mentorUf){
					$.showMsg({
						msg: "<?php echo $Text['msg_err_mentoruf']; ?>",
						type: 'error'
					});
					return false; 
				}

			//create 
			} else {
				var ufName = encodeURIComponent($('#create_uf_name').val()); 
				var urlStr = 'php/ctrl/UserAndUf.php?oper=createUF&name='+ufName+'&mentor_uf='+mentorUf;
				stupidHack = ''; 
			}

			//$('.loadSpinner').show();
			$.ajax({
				type: "POST",
                url: 'php/ctrl/UserAndUf.php?oper=checkFormField&table=aixada_uf&field=name&value='+ufName+stupidHack, 
		        success :  function(msg){
		        	//check if uf name exists
			       if (msg == 1){
			    	   $.showMsg({
							msg: "<?php echo $Text['msg_err_ufexists']; ?> ",
							type: 'error'
						});
				   } else { 
					   
					 	//create/edit uf
						$.ajax({
							type: "POST",
							url : urlStr,
					        success :  function(msg){
					        	$.showMsg({
									msg: "<?php echo $Text['msg_edit_success'] ; ?>",
									autoclose: 1000,
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
			url : "php/ctrl/UserAndUf.php",
			params: "oper=getMemberListing&all=1",
			loadOnInit:true,
			beforeLoad : function(){
				$('.loadSpinner').show();
			},
			rowComplete : function(rowIndex, row){
				var tds = $(row).children();
				//active
				if (tds.eq(2).children('p:first').text() == "1"){
					tds.eq(2).children('p:first').html('<span class="ui-icon ui-icon-check"></span>').addClass('aix-style-ok-green ui-corner-all');
				} else {
					tds.eq(2).children('p:first').html('<span class="ui-icon ui-icon-closethick"></span>').addClass('noRed ui-corner-all');
				}
			},
			complete : function(){
				$('.loadSpinner').hide();
				$('#member_list tbody tr:even').addClass('rowHighlight'); 
			}
		});



		//handle events on uf list
		$('#member_list tbody tr, #member_list_search tbody tr')
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
		$('#member_list_unassigned tbody').xml2html('init',{
			url : "php/ctrl/UserAndUf.php",
			params : "oper=getMembersWithoutUF",
			loadOnInit:true,
			beforeLoad: function(){
				$('.loadSpinner').show();
			},
			complete : function(){
				$('#member_list_unassigned tbody tr:even').addClass('rowHighlight');
				$('.loadSpinner').hide(); 
			}
		});

		//init search listing
		$('#member_list_search tbody').xml2html('init',{
			url : "php/ctrl/UserAndUf.php",
			params : "oper=searchMember",
			beforeLoad: function(){
				$('.loadSpinner').show();
			},
			rowComplete : function(rowIndex, row){
				var tds = $(row).children();
				//active
				if (tds.eq(2).children('p:first').text() == "1"){
					tds.eq(2).children('p:first').html('<span class="ui-icon ui-icon-check"></span>').addClass('aix-style-ok-green ui-corner-all');
				} else {
					tds.eq(2).children('p:first').html('<span class="ui-icon ui-icon-closethick"></span>').addClass('noRed ui-corner-all');
				}
			},
			complete : function(){
				$('#member_list_search tbody tr:even').addClass('rowHighlight');
				$('.loadSpinner').hide(); 
			}
		});


		//member search function
		$("#search").keyup(function(e){
					var minLength = 3; 						//search with min of X characters
					var searchStr = $("#search").val(); 
					
					if (searchStr.length >= minLength){
						//$('.loadAnimShop').show();
						$('#member_list_search tbody').xml2html('reload',{
							url : "php/ctrl/UserAndUf.php",
							params : "oper=searchMember&like="+searchStr,
							
						});
						
					} else {					 
						$('#member_list_search tbody').xml2html("removeAll");				//delete all product entries in the table if we are below minLength;		
						
					}
			e.preventDefault();						//prevent default event propagation. once the list is build, just stop here. 		
		}); //end autocomplete


		//load assign member uf select listing
		$('#sel_assign_uf').xml2html('init',{
				url: 'php/ctrl/UserAndUf.php',
				params : 'oper=getUfListing&all=0',
				loadOnInit:false,
				offSet : 1
			});	
		
		//assign un-assigned members to UF
		$('.btn_assign')
			.live('click', function(e){
				var member_id = $(this).parents('tr').attr('memberId');
				$('#sel_assign_uf').xml2html('reload');
				$('#dialog_assign_uf').dialog('option','memberId',member_id).dialog('open');
			});

		//delete unassigned member from database; throws error if member has existing references
		//i.e. can only delete members that never really got active.  
		$('.btn_delete')
			.live('click', function(e){
				var member_id = $(this).parents('tr').attr('memberId');

				$.showMsg({
					msg: "<?php echo $Text['msg_confirm_del_mem']; ?>",
					buttons: {
						"<?=$Text['btn_ok'];?>":function(){
							$this = $(this);
							var urlStr = 'php/ctrl/UserAndUf.php?oper=delMember&member_id='+member_id; 

							$.ajax({
							   	url: urlStr,
							   	type: 'POST',
							   	success: function(msg){
									//reload all members listing on overiew. 
							   		$('#member_list_unassigned tbody').xml2html('reload');
							   		$this.dialog( "close" ); 
							   	},
							   	error : function(XMLHttpRequest, textStatus, errorThrown){
									if (XMLHttpRequest.responseText.indexOf("ERROR 10") != -1){
										$this.dialog("close");
										$.showMsg({
												msg: "<?=$Text['msg_err_del_member'];?>" + XMLHttpRequest.responseText,
												type: 'error'});

									}
								   	
	
							   	}
							}); //end ajax
													
							
						},
						"<?=$Text['btn_cancel'];?>" : function(){
							$( this ).dialog( "close" );
						}
					},
					type: 'confirm'});
				
				
			});

		
		//create new uf	 
		$("#dialog_assign_uf").dialog({
			autoOpen: false,
			height: 330,
			width: 450,
			modal: true,
			buttons: {
				"<?=$Text['btn_assign'];?>": function() {

					var uf_id = $('#sel_assign_uf option:selected').val();

					if (uf_id < 0) {
						alert("<?php echo $Text['sel_uf']; ?>");
						return false; 
					}

					var member_id = $(this).dialog('option','memberId'); 
					
					var urlStr = "php/ctrl/UserAndUf.php?oper=assignUF&member_id="+member_id+"&uf_id="+uf_id; 
						
					$.post(urlStr, function(ok){
						if (ok == '1'){
							$("#dialog_assign_uf").dialog( "close" );
							$('#member_list_unassigned tbody').xml2html('reload'); 
						}
					})	
	
					
				},
				"<?php echo $Text['btn_cancel'];?>": function() {
					$( this ).dialog( "close" );
				}
			}
		});	
		
		
		/*******************************************
		 *		UF & MEMBER EDIT
		 *******************************************/
		
		$('#uf_detail_member_list').xml2html('init',{
			url : "php/ctrl/UserAndUf.php",
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

				//set the checkboxes
				$('.tblForms input:checkbox', row).each(function(){
					var bool = $(this).val(); 
					if (bool == "1") $(this).attr('checked',true);
				});

				
			},
			complete : function(){
				
				//each member gets an edit button
				$('.btn_save_edit_member').button({
						icons: {primary: "ui-icon-disk"}
					}).live('click', function(e){
						submitMember('update','#detail_member_'+$(this).attr('memberid'));
						return false; 
					});

				//if admin is logged user, show reset pwd button.
				if (isAdmin){
					$('.btn_reset_pwd')
						.button({
							icons: {primary: "ui-icon-locked"}
						}).live('click', function(e){
							
							$.ajax({
							   	url: 'php/ctrl/UserAndUf.php?oper=resetPassword&user_id='+$(this).attr('userId'),
							   	beforeSend: function(){
							   		$('.loadSpinner').show();
								},
							   	success: function(new_pwd_msg){
							   	 	$.showMsg({
										msg: new_pwd_msg,
										type: 'success'});
							   	},
							   	error : function(XMLHttpRequest, textStatus, errorThrown){
								   $.showMsg({
										msg: XMLHttpRequest.responseText,
										type: 'error'});
							   	},
							   	complete : function(msg){
							   		$('.loadSpinner').hide();
							   	}
							}); //end ajax
										
							return false; 
						}).show();
				} 


				//remove member from uf: member is now listed in the un-assigned members tab. 
				$('.ibtn_remove_member')
					.live('mouseover', function(e){
						$(this).addClass('ui-state-hover');
					})
					.live('mouseout', function(e){
						$(this).removeClass('ui-state-hover');
					})
			    	.live('click',function(e){
				    	var $this = $(this);
			    		$.showMsg({
							msg: "<?php echo $Text['msg_confirm_del']; ?>",
							buttons: {
								"<?=$Text['btn_ok'];?>":function(){
									var dlog = $(this);
									var urlStr = 'php/ctrl/UserAndUf.php?oper=removeMember&member_id='+$this.attr('memberId'); 
									$.post(urlStr, function(ok){
										if (ok == '1'){
											$('#detail_member_'+$this.attr('memberId')).fadeOut(1000, function(){$(this).remove()});
											dlog.dialog( "close" );
											$('#member_list_unassigned tbody').xml2html('reload'); 
											$('#member_list tbody').xml2html('reload'); 
										}
									})						
									
								},
								"<?=$Text['btn_cancel'];?>" : function(){
									$( this ).dialog( "close" );
								}
							},
							type: 'confirm'});
						
			    		//switchTo('ufMemberView');
						return false;
					
					});	
				
			}
		});


		//create new member button
		$("#btn_add_member")
			.button({
				icons:{primary:'ui-icon-plus'}
			})
		    .click(function(e){
				prepareAddMemberForm(false);
				switchTo('createMemberView');
			});

		
		//remove all eventual error styles on input fields. 
		$('input')
			.live('focus', function(e){
				$(this).removeClass('ui-state-error');
			});


		

		/**
		 *	initializes and resets the add new member form. 
		 */
		function prepareAddMemberForm(firstTime){

			if (firstTime){

				//add language and theme select
				var langSelect = $('#languageSelect').clone(); 
				var themeSelect = $('#themeSelect').clone(); 

				$('#add_member_div .memberLanguageSelect').append(langSelect);						
				$('#add_member_div .memberThemeSelect').append(themeSelect);

				 
				
				//save button
				$('#frm_add_member .btn_save_new_member').button({
						icons: {primary: "ui-icon-check"}
					})
					.bind('click', function(e){					
						submitMember('create', '#add_member_div');
						return false;  
					});
	
				//new member cancel
				$('#frm_add_member .btn_cancel_new_member')
					.button({
						icons: {secondary:'ui-icon-cancel'}
					})
				    .bind('click',function(e){
				    	switchTo('ufMemberView');
						return false;
						
					});

				//header close icon
				$('#add_member_div .ibtn_cancel_new_member')
					.bind('mouseover', function(e){
						$(this).addClass('ui-state-hover');
					})
					.bind('mouseout', function(e){
						$(this).removeClass('ui-state-hover');
					})
			    	.bind('click',function(e){
			    		switchTo('ufMemberView');
						return false;
					
					});
				
				gFormComplete = true;
			} else {

				$('#frm_add_member input:text, input:hidden').val('');
				
				//set the checkboxes
				$('#frm_add_member input:checkbox').each(function(){
					$(this).attr('checked','checked');
				});
				
				//set the uf_id in the form and heading. 
				$('.setUfId').text(gSelUfRow.attr('ufid'));
				$('#frm_add_member input[name=uf_id]').val(gSelUfRow.attr('ufid'));
			}
		}
		

		/**
		 *	submits the create/edit member data
		 * 	urlStr : either updateMember or createNewMember
		 *  mi: is the selector string of the layer that contains the whole member form and info
		 */
		function submitMember(action, mi){

			var urlStr = 'php/ctrl/UserAndUf.php?oper=updateMember';
			var isValid = true; 
			var isValidItem = true; 
			var err_msg = ''; 

			//run some local checks
			if (action == 'create'){

				urlStr = "php/ctrl/UserAndUf.php?oper=createUserMember";
			
				isValidItem = $.checkFormLength($(mi +' input[name=login]'),3,50);
				if (!isValidItem){
					isValid = false; 
					err_msg += "<?=$Text['msg_err_usershort'];?>" + "<br/><br/>"; 
				}

				isValidItem = $.checkFormLength($(mi+' input[name=password]'),4,15);
				if (!isValidItem){
					isValid = false; 
					err_msg += "<?=$Text['msg_err_passshort'];?>" + "<br/><br/>"; 
				}
				
				isValidItem = $.checkPassword($(mi+' input[name=password]'), $('input[name=password_ctrl]'));
				if (!isValidItem){
					isValid = false; 
					err_msg += "<?=$Text['msg_err_pwdctrl']; ?>"+ "<br/><br/>";
				}

				
			}

			isValidItem = $.checkFormLength($(mi+' input[name="name"]'),2,150);
			if (!isValidItem){
				isValid = false; 
				err_msg += "<?php echo $Text['msg_err_namelength']; ?>"+ "<br/><br/>";
			}

			isValidItem =  $.checkRegexp($(mi+' input[name="phone1"]'),/^([0-9\s\+])+$/);
			if (!isValidItem){
				isValid = false; 
				err_msg += "<?php echo $Text['phone1'] .  $Text['msg_err_only_num']; ?>"+ "<br/><br/>";
			}

			isValidItem =  $.checkRegexp($(mi+' input[name="email"]'),/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
			if (!isValidItem){
				isValid = false; 
				err_msg += "<?=$Text['msg_err_email'] ?>"+ "<br/><br/>";
			}

			if (isValid){

				var sdata = $(mi + ' form').serialize();
								
				$.ajax({
				   	url: urlStr,
				   	type: 'POST',
					data: sdata, 
				   	beforeSend: function(){
				   		$('.loadSpinner').show();
				   		$('.btn_save_new_member').button('disable');
					   	//$('button',mi).button('disable');
					   	//myButton.button('disable');
					  
					},
				   	success: function(msg){
				   	 	$.showMsg({
							msg: "<?php echo $Text['msg_edit_success']; ?>",
							autoclose: 1000,
							type: 'success'});

						//reload all members of this uf
				   	 	$('#uf_detail_member_list').xml2html('reload',{
							params: "oper=getMemberInfo&uf_id="+gSelUfRow.attr('ufid'),
						});
						//show them
				   		switchTo('ufMemberView');
				   		
						//reload all members listing on overiew. 
				   		$('#member_list tbody').xml2html('reload'); 
				   	},
				   	error : function(XMLHttpRequest, textStatus, errorThrown){
					   $.showMsg({
							msg: XMLHttpRequest.responseText,
							type: 'error'});
				   	},
				   	complete : function(msg){
				   		$('.loadSpinner').hide();
				   		$('.btn_save_new_member').button('enable');
				   	}
				}); //end ajax

			//form is not valid		 
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
					$('.viewMemberElements, .ufDetailElements, .createMemberElements').hide();
					$('.btn_save_edit_member').button('destroy');
					$('.btn_save_edit_member').die('click');		//otherwise, save edits will duplicate, triplicate...
					$('.overviewElements').show();
					break;

				//show single member	
				case 'memberView':
					$('.overviewElements, .ufDetailElements, .createMemberElements').hide();
					$('.viewMemberElements').fadeIn(1000, function(){$('.loadSpinner').hide();}); 
					break;

				//show uf info and list all members
				case 'ufMemberView':
					$('.overviewElements, .createMemberElements').hide();
					$('.setUfId').text(gSelUfRow.attr('ufid'));									//indicate uf id + name
					$('#uf_info input:text')
						.val(gSelUfRow.attr('ufname'))
						.attr('disabled','disabled');
						
					if (gSelUfRow.children(':first').find('input:checkbox').attr('checked')){	//copy active state of uf to the info window
						$('#uf_info input:checkbox').attr('checked','checked')
					}
					$('#uf_info input:checkbox').attr('disabled','disabled');	

					
					selMentorUf = (gSelUfRow.attr('mentoruf') > 0)? gSelUfRow.attr('mentoruf'):-1; 	//copy mentor uf and disable select

					$('#mentor_uf')
						.val(selMentorUf)
						.attr('selected','selected')
						.attr('disabled','disabled');

					$('.ufDetailElements, viewMemberElements').fadeIn(1000, function(){$('.loadSpinner').hide();});
					break;

				//show create form for new member	
				case 'createMemberView':
					if (!gFormComplete){
						$.showMsg({
							msg: "<?php echo $Text['msg_err_form_init']; ?>",
							type: 'error'
						});
						return false; 
					} 
					$('.viewMemberElements').hide();
					$('.createMemberElements').fadeIn(2000, function(){$('.loadSpinner').hide();});
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
		<?php include "php/inc/menu.inc.php" ?>
	</div>
	<!-- end of headwrap -->
	
	
	<div id="stagewrap" class="ui-widget">
	
		<div id="titlewrap">
			
			<div id="titleLeftCol">
					<button id="btn_overview" class="floatLeft ufDetailElements viewMemberElements createMemberElements"><?php echo $Text['overview'];?></button>
		    		<h1 class="overviewElements"><?php echo $Text['ti_mng_hu_members']; ?></h1>
		    		<h1 class="viewMemberElements"><?php echo $Text['ti_mng_members'];?> </h1>
		    </div>
		    <div id="titleRightCol">
		    	<!-- p class="textAlignRight"><?php echo $Text['search_memberuf'];?>: <input type="text" name="search_member" id="search_member" class="inputTxtMiddle ui-widget-content ui-corner-all" /></p-->
		    	<button id="btn_new_uf" class="overviewElements floatRight"><?php echo $Text['create_uf']; ?>...</button>
		    	<button id="btn_export" class="overviewElements floatRight"><?php echo $Text['btn_export']; ?></button>
		    </div>	  	
		  
		</div>
		
		
		
		
		<!-- 
					MEMBER LISTING TABS
		 -->
		<div id="member_listing" class="ui-widget overviewElements">
		
			<ul>
				<li><a href="#tabs-1"><h2><?php echo $Text['list_ufs']; ?></h2></a></li>
				<li><a href="#tabs-2"><h2><?php echo $Text['member_pl']; ?></h2></a></li>
				<li><a href="#tabs-3"><h2><?php echo $Text['search_members']; ?></h2></a></li>	
				<li><a href="#tabs-4"><h2><?php echo $Text['unassigned_members']; ?></h2></a></li>
			</ul>
			<span style="float:right; margin-top:-46px; margin-right:9px;"><img class="loadSpinner" src="img/ajax-loader.gif"/></span>
			<div id="tabs-1" class="ui-widget-content ui-corner-all">
				<table id="uf_list" class="tblListingDefault">
						<thead>
						<tr>
							<th><?=$Text['active'];?></th>
							<th><?=$Text['uf_short'];?></th>
							<th class="textAlignLeft"><?=$Text['name_person'];?></th>
							<th><p class="textAlignCenter"><?=$Text['mentor_uf'];?></p></th>
						</tr>
						</thead>
						<tbody>
							<tr ufid="{id}" ufname="{name}" mentoruf="{mentor_uf}" class="clickable">
								<td><input type="checkbox" name="uf_active" value="{active}"/></td>
								<td><?=$Text['uf_short'];?>{id}</td>
								<td><p class="textAlignLeft">{name}</p></td>
                                <td><p class="textAlignCenter">{mentor_uf}</p></td>
							</tr>						
						</tbody>
						<tfoot>
							<tr>
								<td colspan="5"></td>
							</tr>
						</tfoot>
					</table>
			</div>

		
			<div id="tabs-2" class="ui-widget-content ui-corner-all">
				<table id="member_list" class="tblListingDefault">
						<thead>
						<tr>
							<th class="aix-layout-fixW80"><?=$Text['id'];?></th>
							<th><?=$Text['name_person'];?></th>
							<th class="aix-layout-fixW80"><?=$Text['active'];?></th>	
							<th class="aix-layout-fixW80"><?=$Text['uf_short'];?></th>	
							<th><?php echo $Text['phone_pl']; ?></th>
							<th><?php echo $Text['email']; ?></th>
						</tr>
						</thead>
						<tbody>
							<tr class="clickable memberOfUf_{uf_id}" memberId="{id}" ufId="{uf_id}">
								<td>{id}</td>
								<td><p>{name}</p></td>
								<td><p class="textAlignCenter iconContainer">{active}</p></td>
								<td><?=$Text['uf_short'];?>{uf_id}</td>
								<td>{phone1} / {phone2}</p></td>
								<td>{email}</td>
							</tr>						
						</tbody>
				</table>
			</div>
			
			
			<div id="tabs-3" class="ui-widget-content ui-corner-all">
				<p><?php echo $Text['search_memberuf']; ?>: <input type="text" id="search" class="ui-widget-content ui-corner-all"/></p>
				<p>&nbsp;</p>
				<table id="member_list_search" class="tblListingDefault">
						<thead>
						<tr>
							<th><?=$Text['id'];?></th>
							<th class="textAlignLeft"><?=$Text['name_person'];?></th>
							<th><?=$Text['active'];?></th>	
							<th><?=$Text['uf_short'];?></th>	
							<th class="textAlignLeft">Contact</th>
						</tr>
						</thead>
						<tbody>
							<tr class="clickable" memberId="{id}">
								<td>{id}</td>
								<td><p class="textAlignLeft">{name}</p></td>
								<td><p class="textAlignCenter iconContainer">{active}</p></td>
								<td><?=$Text['uf_short'];?>{uf_id}</td>
								<td><p class="textAlignLeft">
									{phone1} / {phone2}<br/>
									{email}
									</p>
								</td>
							</tr>						
						</tbody>
				</table>
			</div>
			
			
			<div id="tabs-4" class="ui-widget-content ui-corner-all">
				<table id="member_list_unassigned" class="tblListingDefault">
						<thead>
						<tr>
							<th>&nbsp;&nbsp; <?=$Text['id'];?></th>
							<th class="textAlignLeft"><?=$Text['name_person'];?></th>
							<th><?=$Text['active'];?></th>	
								
							<th class="textAlignLeft"><?=$Text['phone_pl'];?></th>
							<th><?=$Text['email'];?></th>
							<th></th>
						</tr>
						</thead>
						<tbody>
							<tr memberId="{id}">
								<td>{id}</td>
								<td><p class="textAlignLeft">{name}</p></td>
								<td>{active}</td>
								<td><p class="textAlignLeft">
									{phone1} / {phone2}
									</p>
								</td>
								<td>{email}</td>
								<td>
									<a href="javascript:void(null)" class="btn_assign"><?php echo $Text['btn_assign'];?></a> | 
									<a href="javascript:void(null)" class="btn_delete"><?php echo $Text['btn_del']; ?></a>
								</td>
							</tr>						
						</tbody>
				</table>
			</div>
			
			
		</div><!-- END OF MEMBER LISTING TABS -->
		
		
		
		<div id="uf_member_detail" class="ui-widget">
			<div class="ui-widget-content ui-corner-all ufDetailElements adaptHeight">
				<h2  class="ui-widget-header ui-corner-all"><?php echo $Text['mng_members_uf'];?><span class="setUfId"></span> </h2> 
				<div id="uf_info" class="padding15x10 splitCol floatLeft">
					<table class="tblforms">
						<tr>
							<td class="minwidth-180"><?php echo $Text['name']; ?></td>
							<td><input class="ui-widget-content ui-corner-all" type="text" name="uf_info_name" id="uf_name" value="" disabled="disabled" /></td>
							<td>&nbsp;</td>
							<td><?php echo $Text['active']?></td>
							<td><input type="checkbox" name="uf_info_active" /></td>
							<td colspan="5">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="10">&nbsp;</td>
						</tr>
						<tr>
							<td class="minwidth-180"><?php echo $Text['mentor_uf'] ;?>: </td>
							<td>
								<p class="mentor_uf"></p>
							</td>
							<td>&nbsp;</td>
							<td colspan="2">&nbsp;</td>
							<td class="minwidth-180">
								<button id="btn_edit_uf" class="floatRight"><?php echo $Text['edit_uf']; ?></button>
								<button id="btn_edit_uf_save" class="floatRight"><?php echo $Text['btn_save']; ?></button>
							</td>
							<td>&nbsp;</td>
							<td> 
								<button id="btn_add_member"><?php echo $Text['btn_new_member'];?></button>
							</td>
						</tr>
					</table>		
				</div>
				
			</div>
			<p>&nbsp;</p>
			
			
			
			<!-- 
						LIST AND EDIT MEMBERS OF UF 
			-->
			
			<div id="uf_detail_member_list" class="ufDetailElements viewMemberElements">
				<div class="ui-widget-content ui-corner-all member-info" id="detail_member_{id}">
				<h3 class="ui-widget-header padding10x5">
					{name} (<span class="setUfId"><?php echo $Text['uf_short'];?>{uf_id}</span>)
					<p class="iconContainer ui-corner-all floatRight ibtn_remove_member" memberid="{id}"><span class="ui-icon ui-icon-closethick"></span></p>
					<span style="float:right; margin-top:-2px; margin-right:4px;"><img class="loadSpinner" src="img/ajax-loader.gif"/></span>	
				</h3>
				<form id="frm_save_member_{id}">
				<input type="hidden" name="member_id" value="{id}"/>
				<input type="hidden" name="user_id" value="{user_id}"/>
				<input type="hidden" name="uf_id" value="{uf_id}"/>
				<?php include('php/inc/memberuf.inc.php');?>
			
			</div>		
		</div><!-- end uf_detail_member_list -->
		
		
		
		<!-- 
					CREATE NEW MEMBER 
		-->
		<div id="add_member_div" class="createMemberElements">
				<div class="ui-widget-content ui-corner-all member-add">
				<h3 class="ui-widget-header padding10x5">
					<?php echo $Text['ti_add_member']; ?><span class="setUfId"></span>
					<p class="iconContainer ui-corner-all floatRight ibtn_cancel_new_member"><span class="ui-icon ui-icon-closethick"></span></p>
					<span style="float:right; margin-top:-2px; margin-right:4px;"><img class="loadSpinner" src="img/ajax-loader.gif"/></span>
				</h3>
				<form id="frm_add_member">
				<input type="hidden" name="member_id"/>
				<input type="hidden" name="user_id"/>
				<input type="hidden" name="uf_id" value="{uf_id}"/>
				<table class="tblForms">
						<tr>
							<td><label for="login"><?php echo $Text['login'];?></label></td>
							<td>
								<input class="ui-widget-content ui-corner-all" type="text" name="login" value=""/>
							</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							
						</tr>
						<tr>
							<td colspan="2"></td>
							<td><label for="custom_member_ref"><?php echo $Text['custom_member_ref']; ?></label></td>
							<td><input type="text" name="custom_member_ref" value="{custom_member_ref}" class="ui-widget-content ui-corner-all" /></td>
						</tr>
						
						<tr>
							<td><label for="password"><?=$Text['pwd'];?>:</label></td>
							<td><input type="password" class="ui-widget-content ui-corner-all" name="password"></td>
						</tr>
						
						<tr>
							<td><label for="password_ctrl"><?=$Text['retype_pwd'];?>:</label></td>
							<td><input type="password" class="ui-widget-content ui-corner-all " name="password_ctrl"></td>
						</tr>
						
						<tr><td>&nbsp;</td></tr>
						
						<tr>
							<td><label for="name"><?php echo $Text['name_person'];?></label></td>
							<td><input type="text" name="name"  value="" class="ui-widget-content ui-corner-all" /></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td><label for="nif"><?php echo $Text['nif']; ?></label></td>
							<td><input type="text" name="nif" id="nif" value="" class="ui-widget-content ui-corner-all" /></td>
							<td></td>
							<td></td>
						</tr>
						<tr>
							<td><label for="address"><?php echo $Text['address'];?></label></td>
							<td colspan="5"><input type="text" name="address" value="" class="inputTxtMax ui-widget-content ui-corner-all" /></td>
						</tr>
						<tr>
							<td><label for="city"><?php echo $Text['city'];?></label></td>
							<td><input type="text" name="city" value="" class="ui-widget-content ui-corner-all" /></td>
							<td><label for="zip"><?php echo $Text['zip'];?></label></td>
							<td><input type="text" name="zip" value="" class=" ui-widget-content ui-corner-all" /></td>
							
						</tr>
						<tr>
							<td><label for="phone1"><?php echo $Text['phone1'];?></label></td>
							<td><input type="text" name="phone1" value="" class="ui-widget-content ui-corner-all" /></td>
						
							<td><label for="phone2"><?php echo $Text['phone2'];?></label></td>
							<td><input type="text" name="phone2" value="" class="ui-widget-content ui-corner-all" /></td>
						</tr>
						<tr>
							<td><label for="email"><?php echo $Text['email'];?></label></td>
							<td colspan="5"><input type="text" name="email" value="" class="ui-widget-content ui-corner-all" /></td>
						</tr>
						<tr>
							<td><label for="web"><?php echo $Text['web'];?></label></td>
							<td colspan="5"><input type="text" name="web"value="" class="inputTxtMax ui-widget-content ui-corner-all" /></td>
						</tr>
						<tr>
							<td><label for="notes"><?php echo $Text['notes'];?></label></td>
							<td colspan="5"><input type="text" name="notes" value="" class="inputTxtMax ui-widget-content ui-corner-all" /></td>
						</tr>
						
						<tr>
							<td><label for="member_active"><?php echo $Text['active'];?></label></td>
							<td><input type="checkbox" name="member_active" value="" class="floatLeft" /></td>
							<td><label for="participant"><?php echo $Text['participant'];?></label></td>
							<td><input type="checkbox" name="participant" value=""  class="floatLeft" /></td>
						</tr>
						<tr>
							<td>&nbsp;
							</td>
						</tr>

						<tr>
							<td><label for="default_theme"><?php echo $Text['theme']; ?>:</label></td>
							<td colspan="2">
								<div class="memberThemeSelect"></div>
							</td>
						</tr>
						<tr>
							<td><label for="languageSelect"><?php echo $Text['lang']; ?>:</label></td>
							<td>
								<div class="memberLanguageSelect"></div>
							</td>
						</tr>
						<tr>
							<td colspan="2"></td>
							<td>
								<p class="floatRight">
									<button class="btn_save_new_member"><?php echo $Text['btn_save']?></button>
								</p>
							</td>
							<td>
								<p class="floatRight">	
									<button class="btn_cancel_new_member"><?php echo $Text['btn_cancel'];?></button>
								</p>
							</td>
						</tr>
					</table>
					</form>
					<p>&nbsp;</p>
			</div>		
		</div><!-- end member_create -->
		
			
		
		
		
		
		
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

<div id="dialog_assign_uf" title="Assign member to household">
	<p>&nbsp;</p>
	
	<select id="sel_assign_uf">
		<option value="-1" selected="selected"><?=$Text['sel_uf']; ?></option>
		<option value="{id}">{id} {name}</option>		
	</select>
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

<iframe id="exportChannel" src="" style="display:none; visibility:hidden;"></iframe>
<div id="dialog_export_options" title="<?php echo $Text['export_options']; ?>">
<?php include("tpl/export_dialog.php");?>
</div>

<!-- / END -->
</body>
</html>