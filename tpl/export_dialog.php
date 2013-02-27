<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head></head>
<body>

<div id="container">
<form id="frm_export_options">
<h4>File name</h4>
<input type="text" name="exportName" value="" id="export_name" class="ui-widget ui-corner-all"/> 
<p class="floatRight" id="export_ufs"><input type="checkbox" name="onlyActiveUfs" id="export_active_ufs" checked="checked" class="freeInput"/> <label for="onlyActiveUfs">Only active HUs</label>&nbsp;</p>
<br/><br/>

<h4>Export format</h4>
<input type="radio" name="exportFormat" id="export_csv" value="csv" checked="checked" class="freeInput"/> <label for="export_csv">CSV</label>&nbsp;&nbsp;&nbsp;&nbsp;
<input type="radio" name="exportFormat" id="export_xml" value="xml" class="freeInput"/> <label for="export_xml">XML</label>&nbsp;&nbsp;&nbsp;&nbsp;
<input type="radio" name="exportFormat" id="export_gdrive" value="gdrive" class="freeInput"/> <label for="export_gdrive">Google Spreadsheet</label>
<br/><br/>

<div id="export_authentication">
<h4>Google account authentication</h4>
<table>
<tr><td><label for="export_email">Email</label> </td><td>&nbsp;</td><td><input type="text" name="email" value="" id="export_email" class="ui-widget ui-corner-all"/>  <br/></td></tr>
<tr><td><lable for="export_pwd">Password</lable> </td><td>&nbsp;</td><td><input type="password" name="password" value="" id="export_pwd" class="ui-widget ui-corner-all"/> </td></tr>
</table>
<br/><br/>
</div>

<!-- h4>Destination</h4>
<input type="radio" name="exportDestination" id="export_dest_local" value="disc" checked="checked" class="freeInput"/> <label for="export_dest_local">Download</label>&nbsp;&nbsp;&nbsp;&nbsp;
<input type="radio" name="exportDestination" id="export_dest_gdrive" value="gdrive" class="freeInput"/> <label for="export_dest_gdrive">Google drive</label>
<br/><br/-->

<h4>Others</h4>
<input type="checkbox" name="makePublic" id="makePublic" checked="checked" class="freeInput"/> <label for="makePublic">Make export file public at:</label>
<br/>
<p id="exportURL">&nbsp;&nbsp;&nbsp;&nbsp;<span class="">http://yourdomain.com/loca_config/export/<span id="showExportFileName"></span></span></p>
<br/>
</form>
</div>
</body>
</html>
