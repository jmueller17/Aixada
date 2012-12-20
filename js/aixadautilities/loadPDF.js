/**
 * 
 */

function downloadPDF(){

		var str = window.frames['dataFrame'].document.documentElement.innerHTML;

		if (str.length < 100) return false; 

		var fstr = '<form action="php/ctrl/SmallQ.php" method="post">';
		fstr += '<input type="hidden" name="oper" value="printPDF"/>'; 
		fstr += '<textarea name="htmlStr">'+str+'</textarea>'; 
		fstr += '<input type="hidden" name="fileName" value="Incidents"/></form>'; 

		
		jQuery(fstr).appendTo('body').submit().remove();
}