<?php include "php/inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - "?></title>
	
	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/smoothness/jquery-ui-1.10.0.custom.min.css"/>
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/<?=$default_theme;?>/jqueryui.css"/>


	<?php if (isset($_SESSION['dev']) && $_SESSION['dev'] == true ) { ?> 
	    <script type="text/javascript" src="js/jquery/jquery-1.9.0.js"></script>
		<script type="text/javascript" src="js/jqueryui/jquery-ui-1.10.0.custom.min.js"></script>
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaXML2HTML.js" ></script>
	   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaUtilities.js" ></script>
	   	

		<script src="js/jquery-fileupload/js/jquery.iframe-transport.js"></script>
		<script src="js/jquery-fileupload/js/jquery.fileupload.js"></script>
	   	
	   	
   	<?php  } else { ?>
	   	<script type="text/javascript" src="js/js_for_manage_import.min.js"></script>
    <?php }?>
   		
 		
 	
	<script type="text/javascript">
	
	$(function(){

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
		switch(gImportTo){
	
			case 'aixada_provider':
				title = "Import providers";
				break;

			case 'aixada_product':
				title = "Import or update products for " + decodeURIComponent(gSelProviderName);
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
		        $('.loadSpinner').show();
		        
				/*data.context = $('#btn_upload').button().click(function(e){
 			 		data.submit();
				});*/
				data.submit();
	        },
	        done: function (e, data) {
				
	        	var file = data.result.files[0]; 
	        	

		        if (file.error && file.error.length > 1){
		        	 $.showMsg({
							msg: "Somethign went wrong during the upload: " + file.error,
							type: 'error'});

				} else { //ok, 
					//var fdata = $('#frm_csv_settings').serialize();
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
				url: 'php/ctrl/ImportExport.php?oper=parseFile&import2Table='+gImportTo+'&file='+fileName+'&fullpath='+fullPath,
			   	method: 'POST',
			   	//data : fdata, //parse options 
			   	dataType:'html',
			   	beforeSend: function(){
			   		$('.loadSpinner').show();
			   	
				},
			   	success: function(tbl){
			   		$('.loadSpinner').hide();
			   		$('.uploadMsgElements').hide();
			   		constructPreviewTable(tbl);			   		
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


		
		function constructPreviewTable(tbl){
			$('#preview').html(tbl);

			thead = '<thead><tr>';
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
				
				if (!valid) err_msg = "Need to match up database entries with table rows! Please assign the required matching column " + "<span class='boldStuff'>"+gMatchField[gImportTo] + "</span><br/><br/>";

				//and apart from that? 
				$('#preview select option:selected').each(function(){
					if ($(this).val() != -1){
						nrmatches++; 
					}
				})

				if (nrmatches <= 1) {
					err_msg += "Apart from the required column which table columns do you want to import?";
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
			   	 	$.showMsg({
						msg: "Import has been successful. Do you want to import another file?",
						buttons: {
							"Import another":function(){						
								resetUpload();
								$(this).dialog("close");
							},
							"No, thanks!":function(){						
								//$(this).dialog("close");
								window.opener.reloadWhat();
								window.close();
							}
						},
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
		}//end submit


		function resetUpload(){
			$('.showFileInfo').hide();
			$('.setFileName').text('');
			$('.previewElements').hide();

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
			<h4>1. Choose a file</h4>
			<div class="ui-widget-content ui-corner-all aix-style-padding8x8 adaptHeight">
				<div class="floatLeft aix-layout-fixW450">
					<form id="frmFileUpload">
					<input id="fileupload" type="file" name="files[]" class="ui-widget ui-corner-all" multiple>
					</form>
					<br/>
					<p>&nbsp;Allowed formats: *.csv, *.xls, *.ods, *.xlsx, *.xml</p>
					<br/>	<br/>
					<p class="showFileInfo aix-style-ok-green ui-corner-all aix-layout-fixW350 aix-style-padding8x8">Import file: <span class="setFileName"></span></p>
				</div>
				<div class="floatLeft">
					<p class="boldStuff">Public URL</p><br/>
					<input type="text" name="importURL" id="importURL" value="http://" class="ui-widget ui-corner-all"/>
					<br/><br/>
					<button id="btn_fetch">Load file</button> 
				</div>
				<span style="float:right; margin-top:-2px; margin-right:4px;"><img class="loadSpinner" src="img/ajax-loader_fff.gif"/></span>
				<div style="clear:both">
					<p id="msg_file_upload" class="uploadMsgElements">Uploading file and generating preview, please wait...!</p>
					<p id="msg_fetch_file" class="uploadMsgElements">Reading file from server and parsing, please wait...!</p>
					
				</div>
			</div>		
		</div>
		<br/><br/>
		
		<div class="ui-widget previewElements hidden"> 
			<h4>2. Preview data and match columns</h4>
			<div class="ui-widget-content ui-corner-all aix-style-padding8x8">
				<br/>
				<p class="previewTableElements">Required column: <span class="setRequiredColumn ui-state-highlight aix-style-padding8x8 ui-corner-all"></span></p><br/>
				<div class="ui-style-info previewOwnElements" >
					<p class="ui-style-warning">Good news: most data (columns) could be recognized and you could try to automatically import
					the file. As a more secure alternative, preview the content first and match the table columns by hand. </p>
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
				
				<p>What should happen with data that does not exist in the database?</p> 
				<p>
					<form id="frmImpOptions">
					<input type="radio" name="append_new" value="1" /> Create new entries <br/>
					<input type="radio" name="append_new" value="0" checked="checked"/> Just update existing rows
					</form>
				</p>
				<br/>
				<button class="btn_import previewOwnElements">Import directly</button>
				<button class="btn_import previewTableElements">Import</button>
				<button class="btn_preview previewOwnElements">Preview first</button>


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
	<option value="-1">Match column...</option>
	<option value="{db_field}">{db_field}</option>
</select>

<!-- / END -->
</body>
</html>