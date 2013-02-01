<?php include "php/inc/header.inc.php" ?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<style>
		table, td {border:1px solid blue; border-collapse:collapse; padding:4px;}
		
	</style>
	<script type="text/javascript" src="js/jquery/jquery.js"></script>
	<!-- script type="text/javascript" src="js/jqueryui/jqueryui.js"></script-->
	<script src="js/jquery-fileupload/js/vendor/jquery.ui.widget.js"></script>
	<script src="js/jquery-fileupload/js/jquery.iframe-transport.js"></script>
	<script src="js/jquery-fileupload/js/jquery.fileupload.js"></script>
	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaXML2HTML.js" ></script>
	

	<script>
	

	$(function () {

		
	    $('#fileupload').fileupload({
		    //url : 'php/external/jquery-fileupload/server/php/',
		    url : 'php/ctrl/Import.php?oper=uploadFile',
	        dataType: 'json',
	        add: function (e, data) {
		        
	            data.context = $('<button/>').text('Upload ' +data.files[0].name)
	                .appendTo(document.body)
	                .click(function () {
	                    $(this).replaceWith($('<p/>').text('Uploading...'));
	                    data.submit();
	                });
	        },
	        done: function (e, data) {

	        	var file = data.result.files[0]; 
	        	

		        if (file.error && file.error.length > 1){
					alert("Somethign went wrong during the upload: " + file.error);

				} else { //ok, 
					urlstr = 'php/ctrl/Import.php?oper=parseFile&file='+file.name;
					$.post(urlstr, function(tbl){
						$('#preview').html(tbl);

						thead = '<thead><tr>';
						$('#preview table tr:first td').each(function(){
							thead += '<th class="mapSelect"></th>';
						})
						thead += '</tr></thead>';

						$('#preview table').append(thead);

						//alert($('#opt').clone().html()); //appendTo('.mapSelect');

						$('#opt').clone().appendTo('.mapSelect');
						
					})
					
				}
		        
	            /*$.each(data.result.files, function (index, file) {
	                $('<p/>').text(file.name).appendTo(document.body);
	                 
	            });*/
	        },
	        fail : function (e, data){
					alert(data.errorThrown);
		    }
	    }); //end failupload



	  //load mentor uf select listing
		$('#opt').xml2html('init',{
			url: 'php/ctrl/Import.php',
			params : 'oper=getAllowedFields&table=aixada_product',
			loadOnInit:true,
			offSet : 1,
			complete:function(rowCount){
				
			}
		}).change(function(){
			});


		function submitForm(){
		
			var sdata = $('#colSelect').serialize();
			
			$.ajax({
			   	url: 'php/ctrl/Import.php?oper=import',
			   	method: 'POST',
				data: sdata, 
			   	beforeSend: function(){
			   		$('.loadSpinner').show();
	
				},
			   	success: function(msg){
				   	alert(1);
			   		
			   	},
			   	error : function(XMLHttpRequest, textStatus, errorThrown){
			   	},
			   	complete : function(msg){
	
			   	}
			}); //end ajax
	
		}




		//save edited provider new provider
		$('#btn_submit')
			.click(function(e){	
				submitForm();
				e.stopPropagation();
				return false; 
			});
		
	    
	});
	
	</script>

</head>
<body>
<select id="opt" name="table_col[]">
	<option value="-1">Please select...</option>
	<option value="{db_field}">{db_field}</option>
</select>
<br/><br/><br/>


<input id="fileupload" type="file" name="files[]" multiple>

<form id="colSelect">
<input type="hidden" name="provider_id" value="49"/>
<div id="preview" style="height:100px; width:800px; overflow:auto;">

</div>
</form>

<button id="btn_submit">import</button>

<?php

//require_once('php/lib/abstract_import_export_format.php');
//require_once('php/lib/csv_wrapper.php');
//require_once('php/lib/import_products.php');




		/*try {
			$csv = new csv_wrapper('local_config/tmp/boletsPricelist.csv');
	
			$dt = $csv->parse();
		

			$map = array('custom_product_ref'=>0, 'unit_price'=>1, 'name'=>2);
		
			//data_table, dt to db map, provider_id
			$pi = new import_products($dt, $map, 49);
			
			//append_new = true
			$pi->import(true);
			
			
		} catch(Exception $e) {
    		header('HTTP/1.0 401 ' . $e->getMessage());
    		die ($e->getMessage());
		}   */
	

?>
</body>
</html>