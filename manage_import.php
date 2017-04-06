<?php include "php/inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - " . $Text['head_ti_import'];?></title>
	
	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/smoothness/jquery-ui-1.10.0.custom.min.css"/>
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/<?=$default_theme;?>/jqueryui.css"/>
    <style>
        .darkGrayed {color:#777;}
    </style>

    <script type="text/javascript" src="js/jquery/jquery-1.9.0.js"></script>
    <script type="text/javascript" src="js/jqueryui/jquery-ui-1.10.0.custom.min.js"></script>
    <?php echo aixada_js_src(false); ?>

    <script src="js/jquery-fileupload/js/jquery.iframe-transport.js"></script>
    <script src="js/jquery-fileupload/js/jquery.fileupload.js"></script>
 	
	<script type="text/javascript">
	
	$(function(){
		$.ajaxSetup({ cache: false });

		//loading animation
		$('.loadSpinner').hide(); //attr('src', "img/ajax-loader-<?=$default_theme;?>.gif"); 
		$('.uploadMsgElements').hide();
		$('.showFileInfo').hide();

		
		//which db table data is imported to
		var gImportTo 	=	(typeof $.getUrlVar('import2Table') == "string")? $.getUrlVar('import2Table'):false;

		//for products we need provider id
		var gSelProvider = (typeof $.getUrlVar('providerId') == "string")? $.getUrlVar('providerId'):false;

		//for products we need provider id
		var gSelProviderName = (typeof $.getUrlVar('providerName') == "string")? $.getUrlVar('providerName'):false;

		
		//required table column for mapping input to db fields for the different tables. 
		var gMatchField = {'aixada_provider':'nif', 'aixada_product':'custom_product_ref'};
		

		//pass provider id to the form
		$('input[name=provider_id]').val(gSelProvider);

		//set the title import destination
        var title;
		switch(gImportTo){
	
			case 'aixada_provider':
				title = "<?=$Text['ti_import_providers'];?>";
				break;

			case 'aixada_product':
				title = "<?=$Text['ti_import_products'];?>" + decodeURIComponent(gSelProviderName);
				break;

			default: 
				title='??';

		}
		
		
		$('.setImportDestTitle').text(title);


		$('.setRequiredColumn').text(gMatchField[gImportTo]);


		

		/***********************************************************
		 *
		 *  jquery upload plugin
		 *
		 ***********************************************************/
	    $('#fileupload').fileupload({
		    url : 'php/ctrl/ImportExport.php?oper=uploadFile',
	        dataType: 'json',
	        add: function (e, data) {
		        $('.setFileName').text(data.files[0].name); 
		        $('.showFileInfo').fadeIn(1000);
		        $('#btn_fetch').button("disable");
		        $('#msg_file_upload').fadeIn(600);
		        resetUpload();
		        $('.loadSpinner').show();
		   
				data.submit();
	        },
	        done: function (e, data) {
	        	var file = data.result.files[0]; 
		        if (file.error && file.error.length > 1){
		        	 $.showMsg({
							msg: "<?=$Text['msg_err_upload'];?>" + file.error,
							type: 'error'});

				} else { 
					parseFile(file.name, '');	
				}
	        },
	        fail : function (e, data){
	        	   $.showMsg({
						msg: data.errorThrown,
						type: 'error'});
		    }
	    }); //end failupload


	    $('#importURL').focus(function(){
		    $(this).val('');
			$('#btn_fetch').button("enable");
		})
	 
		$('#assignColumns').xml2html('init',{
			url: 'php/ctrl/ImportExport.php',
			params : 'oper=getAllowedFields&table='+gImportTo,
			loadOnInit:true,
			offSet : 1
		});
        $('#import_template').xml2html('init',{
			url: 'php/ctrl/ImportExport.php',
			params : 'oper=getImportTemplates&table='+gImportTo,
			loadOnInit:true,
			offSet : 1,
			complete : function(rowCount){
                var div = $('#import_template').parent().parent();
                if (rowCount == 0){
					div.hide();
                } else {
                	div.show();
				}
			}
		});


		$("#btn_fetch")
			.button({
				disabled:true
			})
			.click(function(e){
				
				var fetchURL = "url="+encodeURIComponent($('#importURL').val());
			
				$.ajax({
				   	url: 'php/ctrl/ImportExport.php?oper=fetchFile',
				   	method: 'POST',
					data: fetchURL, 
				   	beforeSend: function(){
				   		$('.loadSpinner').show();
					    $('#msg_fetch_file').fadeIn(600);
					    $('#btn_fetch').button("disable");
				   	
					},
				   	success: function(fullPath){
						parseFile('',fullPath); 

				   	},
				   	error : function(XMLHttpRequest, textStatus, errorThrown){
					   $.showMsg({
							msg: XMLHttpRequest.responseText,
							type: 'error'});
				   	},
				   	complete : function(msg){
				   		$('.uploadMsgElements').hide();
				   		$('.loadSpinner').hide();
				   		
	
				   	}
				}); //end ajax
			

		})

		//import data
		$('.btn_import')
			.button({
			icons: {
	        		primary: "ui-icon-transferthick-e-w"
	        	}
			 })
			.click(function(e){	
				submitForm();
				e.stopPropagation();
				return false; 
			});

		//show preview table
		$('.btn_preview')
			.button()
			.click(function(e){	
				$('.previewOwnElements').hide();
				$('.previewTableElements').show();
			});


		
		function parseFile(fileName, fullPath){
			$.ajax({
				url: 'php/ctrl/ImportExport.php?oper=parseFile'+
                    '&import2Table='+gImportTo+
                    '&file='+fileName+
                    '&fullpath='+fullPath+
                    '&import_template='+$('select[name=import_template]').val()+
                    '&provider_id='+$('input[name=provider_id]').val(),
			   	method: 'POST',
			   	//data : fdata, //parse options 
			   	dataType:'html',
			   	beforeSend: function(){
			   		$('.loadSpinner').show();
				},
			   	success: function(tbl){
			   		$('.loadSpinner').hide();
			   		$('.uploadMsgElements').hide();
                    if (!isNaN(parseInt(tbl,10))) {
                        showResponse(tbl);
                    } else {
                        constructPreviewTable(tbl);
                    }
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

		}


		//constructs the HTML table to preview the uploaded spreadsheet 
		function constructPreviewTable(tbl){
			$('#preview').html(tbl);

			var thead = '<thead><tr>';
			$('#preview table tr:first td').each(function(){
				thead += '<td class="mapSelect"></td>';
			})
			thead += '</tr></thead>';

			$('#preview table').append(thead);
			$('#assignColumns').clone().appendTo('.mapSelect').show();
			
			if($('#preview table').attr('isown')){
				$('.previewOwnElements').show();
				$('.previewTableElements').hide();
			} else {
				$('.previewOwnElements').hide();
				$('.previewTableElements').show();
			}			

			$('.previewElements').fadeIn(1000);
		}



		//before submitting import, need to make sure that at least one column from the preview table is 
		//selected part from the matching column. 
		function checkForm(){
			var valid = true; 
			var err_msg = ''; 
			var nrmatches = 0; 
			
			//previewing, need to make sure at least one column and the required one is selected. 
			if ($('.previewTableElements').is(':visible')){
				valid = false; 
				//is the required matching column selected?
				$('#preview select option:selected').each(function(){
					if ($(this).val() == gMatchField[gImportTo]){
						valid = true;  
					}
				})
				
				if (!valid) err_msg = "<?=$Text['msg_import_matchcol'];?>" + "<span class='boldStuff'>"+gMatchField[gImportTo] + "</span><br/><br/>";

				//and apart from that? 
				$('#preview select option:selected').each(function(){
					if ($(this).val() != -1){
						nrmatches++; 
					}
				})

				if (nrmatches <= 1) {
					err_msg += "<?=$Text['msg_import_furthercol'];?>";
					valid = false; 
				}

				 $.showMsg({
						msg: err_msg,
						type: 'error'});
			}

			return valid; 

		}

		
		/**
		 * 
		 */
		function submitForm(){

			if (!checkForm()) return false; 

			
			//serialize 
			var sdata = $('#frmColMap').serialize() + "&" + $('#frmImpOptions').serialize();
		
			$.ajax({
			   	url: 'php/ctrl/ImportExport.php?oper=import&import2Table='+gImportTo,
			   	method: 'POST',
				data: sdata, 
			   	beforeSend: function(){
			   		$('.loadSpinner').show();
			   	
				},
			   	success: function(msg){
                    showResponse(msg);
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
		}//end submit


		function resetUpload(){
			$('.showFileInfo').hide();
			$('.setFileName').text('');
			$('.previewElements').hide();

		}

		 
        function showResponse(importedRows){
            $.showMsg({
                title: "<?=$Text['msg_success'];?>",
                msg: "<?=$Text['msg_import_done'];?>".replace("{$rows}",importedRows) + 
                     "<br><?=$Text['msg_import_another'];?>",
                buttons: {
                    "<?=$Text['btn_import_another'];?>":function(){						
                        resetUpload();
                        $(this).dialog("close");
                    },
                    "<?=$Text['btn_nothx']; ?>":function(){						
                        window.opener.reloadWhat();
                        window.close();
                    }
                },
                type: 'success'
            });
        }

							
	});  //close document ready
</script>

</head>
<body>
<div id="wrap">
	
	
	<div id="stagewrap">
	
		<div id="titlewrap" class="ui-widget">
			<div >
		    	<h1 class="pgProviderOverview setImportDestTitle"></h1>
    		</div>
		</div><!-- end titlewrap -->
 
 
 
				 <!-- 
					
					
		 -->
		 <div class="ui-widget"> 
			<h4>1. <?=$Text['import_step1']; ?></h4>
			<div class="ui-widget-content ui-corner-all aix-style-padding8x8 adaptHeight">
                <div class="wrapSelect hidden">
                    <form id="frmImportTemplate">
                        <label for="import_template"><?=$Text['direct_import_template']; ?>: </label>
                        <select id="import_template" name="import_template">
                            <option value="-1"><?='( ... )';?></option>
                            <option value="{db_field}">{db_field}</option>
                        </select>
                    </form>
                    <br>
                </div>
				<div class="floatLeft aix-layout-fixW450">
					<form id="frmFileUpload">
					<input id="fileupload" type="file" name="files[]" class="ui-widget ui-corner-all" multiple>
					</form>
					<br/>
					<p>&nbsp;<?=$Text['import_allowed']; ?>: *.csv, *.xls, *.ods, *.xlsx, *.xml</p>
					<br/>	<br/>
					<p class="showFileInfo aix-style-ok-green ui-corner-all aix-layout-fixW450 aix-style-padding8x8"><?=$Text['import_file']; ?>:<br>
						&nbsp;&nbsp;<b class="setFileName"></b></p>
				</div>
				<div class="floatLeft">
					<p class="boldStuff"><?=$Text['public_url'];?></p><br/>
					<input type="text" name="importURL" id="importURL" value="http://" class="ui-widget ui-corner-all"/>
					<br/><br/>
					<button id="btn_fetch"><?=$Text['btn_load_file']; ?></button> 
				</div>
				<span style="float:right; margin-top:-2px; margin-right:4px;"><img class="loadSpinner" src="img/ajax-loader_fff.gif"/></span>
				<div style="clear:both">
					<p id="msg_file_upload" class="uploadMsgElements"><?=$Text['msg_uploading']; ?></p>
					<p id="msg_fetch_file" class="uploadMsgElements"><?=$Text['msg_parsing']; ?></p>
					
				</div>
			</div>		
		</div>
		<br/><br/>
		
		<div class="ui-widget previewElements hidden"> 
			<h4>2. <?=$Text['import_step2'];?></h4>
			<div class="ui-widget-content ui-corner-all aix-style-padding8x8">
				<br/>
				<p class="previewTableElements"><?=$Text['import_reqcol'];?>: <span class="setRequiredColumn ui-state-highlight aix-style-padding8x8 ui-corner-all"></span></p><br/>
				<div class="ui-style-info previewOwnElements" >
					<p class="ui-style-warning"><?=$Text['import_auto'];?> </p>
					<br/>
				</div>
				
				<div class="previewTableElements">
				<form id="frmColMap">
					<input type="hidden" name="provider_id" value=""/>
					<div id="preview" style="max-height:300px; overflow:auto;">
					</div>
				</form>
				</div>
				<br/>
				
				<p><?=$Text['import_qnew'];?></p> 
				<p>
					<form id="frmImpOptions">
                    <?php
                    $importIgnoreRowsTxt = str_replace('{$match_field}',
                        '<span class="setRequiredColumn"></span>',
                        $Text['import_ignore_rows']);
                    $importIgnoreValueTxt = str_replace('{$match_field}',
                        '<span class="setRequiredColumn"></span>',
                        $Text['import_ignore_value']);
                    ?>
					<input type="radio" name="import_mode" value="2" checked="checked" />
						<?=$Text['import_create_update'];?>
						<span class="darkGrayed"><?=$importIgnoreRowsTxt;?></span><br/>
					<input type="radio" name="import_mode" value="1" />
						<?=$Text['import_createnew'];?>
						<span class="darkGrayed"><?=$importIgnoreRowsTxt;?></span><br/>
					<input type="radio" name="import_mode" value="0" />
						<?=$Text['import_update'];?>
						<span class="darkGrayed"><?=$importIgnoreValueTxt;?></span>
					</form>
				</p>
				<br/>
				<button class="btn_import previewOwnElements"><?=$Text['btn_imp_direct'];?></button>
				<button class="btn_import previewTableElements"><?=$Text['btn_import'];?></button>
				<button class="btn_preview previewOwnElements"><?=$Text['btn_preview'];?></button>


			</div>		
		</div>
		
		 
		<!-- div class="ui-widget">
			<h4>2. CSV import settings for: <span class="setFileName"></span></h4>
			<div class="ui-widget-content ui-corner-all aix-style-padding8x8">
				<form id="frm_csv_settings">
				<table class="tblFormsSettings">
					<thead>
						<tr>
							<th>New Items</th>
							<th>Seperated by</th>
							<th>Text delimiter</th>
							<th>Header</th>
							
						</tr>
					</thead>
					<tr>
						<td>
							<label><input type="radio" name="new_items" value="append" /> Append</label><br/>
							<label><input type="radio" name="new_items" value="ignore" checked="checked"/> Ignore</label>
							
						</td>
						<td>
							<label><input type="radio" name="field_delimiter" value="0" checked="checked"/> Comma </label><br/>
							<label><input type="radio" name="field_delimiter" value="1"/> Semicolon </label><br/>
							<label><input type="radio" name="field_delimiter" value="2"/> Tab </label><br/>
							<label><input type="radio" name="field_delimiter" value="3"/> Space</label><br/>
						</td>
						<td>
							<label><input type="radio" name="text_delimiter" value="4" checked="checked"/> Double quote </label><br/>
							<label><input type="radio" name="text_delimiter" value="5"/> Single quote</label>
						</td>
						<td>
							<label><input type="radio" name="header" value="true" checked="checked"> Use first row</label><br/>
							<label><input type="radio" name="header" value="false"/> None</label>
						</td>
					</tr>
				</table>
				</form>
				<br/>

			</div>
			
		</div-->	 
		<br/><br/>
		
				
		<br/><br/><br/><br/>			
	</div>
	<!-- end of stage wrap -->
</div>
<!-- end of wrap -->
<select id="assignColumns" name="table_col[]" class="hidden">
	<option value="-1"><?=$Text['sel_matchcol'];?></option>
	<option value="{db_field}">{db_field}</option>
</select>

<!-- / END -->
</body>
</html>