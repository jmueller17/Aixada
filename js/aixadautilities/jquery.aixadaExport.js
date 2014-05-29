
$(function(){
	

	//create public available copy of export file option
	$('#exportDialog').on('click','input[name=makePublic]', function(){
		if ($(this).attr("checked") == "checked"){
			$('#modalExportDialog #exportURL').addClass('ax-txt-strong');
		} else {
			$('#modalExportDialog #exportURL').removeClass('ax-txt-strong');
		}
	
	})
	
	//indicate file name for publishing on own web
	$('#exportDialog').on('keyup', 'input[name=exportName]', function(){
		var fext = $('#modalExportDialog input[name=exportFormat]:checked').val();   
		$('#modalExportDialog #showExportFileName').text($(this).val() + "." + fext);
	})
	
	
	//control switching between export format options
	$('#exportDialog').on('click','input[name=exportFormat]', function(){
		var name = ''; 
		if ($(this).attr("checked") == "checked" && $(this).val() == "gdrive"){
			$('#modalExportDialog #export_authentication').fadeIn(1000);
			name = $('input[name=exportName]').val() + ".csv"; 
			 
		} else {
			$('#modalExportDialog #export_authentication').fadeOut(1000);
			name = $('#modalExportDialog input[name=exportName]').val() + "." + $('#modalExportDialog input[name=exportFormat]:checked').val();
		}
	
		$('#modalExportDialog #showExportFileName').text(name);
	
	})
	

});