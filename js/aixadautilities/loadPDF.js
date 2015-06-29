/**
 * 
 */

function downloadPDF(outputFormat, fileName){


		//var str = window.frames['dataFrame'].document.documentElement.innerHTML;
		var str = document.documentElement.innerHTML;

		if (str.length < 100) return false; 

		var fstr = '<form id="printPDForm" action="../php/ctrl/SmallQ.php" method="post">';
		fstr += '<input type="hidden" name="oper" value="printPDF"/>'; 
		fstr += '<textarea name="htmlStr">'+str+'</textarea>';
		fstr += '<input type="hidden" name="outParam" value="'+outputFormat+'"/>'; 
		fstr += '<input type="hidden" name="fileName" value="'+fileName+'"/></form>'; 

		
		//returns the file name to download
		if (outputFormat == 'F') {
			var formData = jQuery(fstr).appendTo('body').serialize(); 
			

			$.ajax({
				type: "POST",
				data: formData,
				url : "../php/ctrl/SmallQ.php", 
				success : function(fileName){
					
					var newWin = window.open("", "win"); 
					newWin.document.open("text/html", "replace");
					var html = '<html><head></head><body>';
					html +='<div style="background: transparent url(../img/ajax-loader_fff.gif) no-repeat">';
					html +='<object height="1250px" width="100%" type="application/pdf" data="../local_config/reports/'+fileName+'">';
					html +='<param value="../local_config/reports/'+fileName+'" name="src"/>';
					html +='<param value="transparent" name="wmode"/>'
					html +='</object></div></body></html>';
					
					newWin.document.write(html);
					newWin.document.close();
					
				}, 
				error : function(XMLHttpRequest, textStatus, errorThrown){
					$.showMsg({
						msg:XMLHttpRequest.responseText,
						type: 'error'});	
				}
				
				
			});
			
			$('#printPDForm').remove();
		
		//returns the pdf directly
		} else {
			
			jQuery(fstr).appendTo('body').submit().remove(); 
		
		}
}