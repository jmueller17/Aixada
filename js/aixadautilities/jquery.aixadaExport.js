
$(function(){
	


	//create public available copy of export file option
	$('#makePublic').on('click', function(){
		if ($(this).attr("checked") == "checked"){
			$('#exportURL').show();
		} else {
			$('#exportURL').hide();
		}
	
	})
	
	//indicate file name for publishing on own web
	$('input[name=exportName]').on('keyup', function(){
		var fext = ($('input[name=exportFormat]:checked').val() == 'gdrive')? 'csv':$('input[name=exportFormat]:checked').val();   
		$('#showExportFileName').text($(this).val() + "." + fext);
	})
	
	
	//control switching between export format options
	$('input[name=exportFormat]').on('click', function(){
		var name = ''; 
		if ($(this).attr("checked") == "checked" && $(this).val() == "gdrive"){
			$('#export_authentication').fadeIn(1000);
			name = $('input[name=exportName]').val() + ".csv"; 
			 
		} else {
			$('#export_authentication').fadeOut(1000);
			name = $('input[name=exportName]').val() + "." + $('input[name=exportFormat]:checked').val();
		}
	
		$('#showExportFileName').text(name);
	
	})
	
	//initially hide authenticate and specific uf stuff. 
	$('#export_authentication').hide();
	$('#export_ufs').hide();

	
	
});