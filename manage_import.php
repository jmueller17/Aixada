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
		$('.loadSpinner').attr('src', "img/ajax-loader-<?=$default_theme;?>.gif"); 

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
		    url : 'php/ctrl/Import.php?oper=uploadFile',
	        dataType: 'json',
	        add: function (e, data) {
		        
		        $('.setFileName').text(data.files[0].name).fadeIn(1000); 
		        $("#btn_upload").fadeIn(1000);

					data.context = $('#btn_upload').button().click(function(e){
 			 			data.submit();
						});
	        },
	        done: function (e, data) {

	        	var file = data.result.files[0]; 
	        	

		        if (file.error && file.error.length > 1){
		        	 $.showMsg({
							msg: "Somethign went wrong during the upload: " + file.error,
							type: 'error'});

				} else { //ok, 
					var fdata = $('#frm_csv_settings').serialize();
					
					$.ajax({
						url: 'php/ctrl/Import.php?oper=parseFile&file='+file.name,
					   	method: 'POST',
					   	data : fdata, 
					   	dataType:'html',
					   	beforeSend: function(){
					   		$('.loadSpinner').show();
					   	
						},
					   	success: function(tbl){
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
	        },
	        fail : function (e, data){
	        	   $.showMsg({
						msg: data.errorThrown,
						type: 'error'});
		    }
	    }); //end failupload


	  //load mentor uf select listing
		$('#assignColumns').xml2html('init',{
			url: 'php/ctrl/Import.php',
			params : 'oper=getAllowedFields&table=aixada_product',
			loadOnInit:true,
			offSet : 1
		});


		$("#btn_upload").button()
			.hide();

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
			   	url: 'php/ctrl/Import.php?oper=import',
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
		    	<h1 class="pgProviderOverview"> Import products</h1>
    		</div>
    		<div id="titleRightCol50">

    		</div>
		</div><!-- end titlewrap -->
 
 
 
				 <!-- 
					
					
		 -->
		 <div class="ui-widget aix-layout-fixW450"> 
			<h4>1. Choose a file</h4>
			<div class="ui-widget-content ui-corner-all aix-style-padding8x8">
				<form id="frmFileUpload">
				<input id="fileupload" type="file" name="files[]" class="ui-widget ui-corner-all" multiple>
				</form>
				<br/>	<br/>
				<button id="btn_upload">Preview</button> <span class="setFileName"></span>
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
	<option value="-1">Please select...</option>
	<option value="{db_field}">{db_field}</option>
</select>

<!-- / END -->
</body>
</html>