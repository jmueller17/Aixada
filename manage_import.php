<?php include "php/inc/header.inc.php" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $Text['global_title'] . " - "?></title>
	
	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/smoothness/jquery-ui-1.10.0.custom.min.css"/>


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
		
		//which db table data is imported to
		var gImportTo 	=	(typeof $.getUrlVar('import2Table') == "string")? $.getUrlVar('import2Table'):false;

		//for products we need provider id
		var gSelProvider = (typeof $.getUrlVar('providerId') == "string")? $.getUrlVar('providerId'):false;

		
		$('input[name=provider_id]').val(gSelProvider);

		

		/***********************************************************
		 *
		 *  jquery upload plugin
		 *
		 ***********************************************************/
	    $('#fileupload').fileupload({
		    url : 'php/ctrl/ImportExport.php?oper=uploadFile',
	        dataType: 'json',
	        add: function (e, data) {
		       
		        //$('.setFileName').text(data.files[0].name).fadeIn(1000); 
		        //$("#btn_upload").fadeIn(1000);

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
			params : 'oper=getAllowedFields&table=aixada_product',
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

		//save edited provider new provider
		$('#btn_import')
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


		
		function parseFile(fileName, fullPath){
			$.ajax({
				url: 'php/ctrl/ImportExport.php?oper=parseFile&file='+fileName+'&fullpath='+fullPath,
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

			$('.opDataPreview').fadeIn(1000);

		}
		

		/**
		 * 
		 */
		function submitForm(){
	
			
			//serialize 
			var sdata = $('#frmColMap').serialize() +$('#frm_csv_settings').serialize();
		
			$.ajax({
			   	url: 'php/ctrl/ImportExport.php?oper=import',
			   	method: 'POST',
				data: sdata, 
			   	beforeSend: function(){
			   		$('.loadSpinner').show();
			   	
				},
			   	success: function(msg){
			   	 	$.showMsg({
						msg: "<?php echo $Text['msg_edit_success']; ?>",
						type: 'success',
						autoclose:1000});
			   		
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

		 
							
	});  //close document ready
</script>

</head>
<body>
<div id="wrap">
	
	
	<div id="stagewrap">
	
		<div id="titlewrap" class="ui-widget">
			<div id="titleLeftCol50">
		    	<h1 class="pgProviderOverview">Import data</h1>
    		</div>
    		<div id="titleRightCol50">

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
				</div>
				<div class="floatLeft">
					<p class="boldStuff">Public URL</p><br/>
					<input type="text" name="importURL" id="importURL" value="http://" class="ui-widget ui-corner-all"/>
					<br/><br/>
					<button id="btn_fetch">Load file</button> <span class="setFileName"></span>
				</div>
				<span style="float:right; margin-top:-2px; margin-right:4px;"><img class="loadSpinner" src="img/ajax-loader_fff.gif"/></span>
				<div style="clear:both">
					<p id="msg_file_upload" class="uploadMsgElements">Uploading file and generating preview, please wait...!</p>
					<p id="msg_fetch_file" class="uploadMsgElements">Reading file from server and parsing, please wait...!</p>
					
				</div>
			</div>		
		</div>
		<br/><br/>
		
		<div class="ui-widget opDataPreview hidden"> 
			<h4>2. Preview data and match columns</h4>
			<div class="ui-widget-content ui-corner-all aix-style-padding8x8">
				<form id="frmColMap">
					<input type="hidden" name="provider_id" value=""/>
					<div id="preview" style="max-height:300px; overflow:auto;">
				
					</div>
				</form>
				<br/>
				<button id="btn_import">Import</button>
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