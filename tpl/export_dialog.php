<div id="container">
<form id="frm_export_options">
<h4><?php echo $Text['file_name']; ?></h4>
<input type="text" name="exportName" value="" id="export_name" class="ui-widget ui-corner-all"/> 
<p class="floatRight" id="export_ufs"><input type="checkbox" name="onlyActiveUfs" id="export_active_ufs" checked="checked" class="freeInput"/> <label for="onlyActiveUfs"><?php echo $Text['active_ufs']; ?></label>&nbsp;</p>
<br/><br/>

<h4><?php echo $Text['export_format'];?></h4>
<input type="radio" name="exportFormat" id="export_csv" value="csv" checked="checked" class="freeInput"/> <label for="export_csv">CSV</label>&nbsp;&nbsp;&nbsp;&nbsp;
<input type="radio" name="exportFormat" id="export_xml" value="xml" class="freeInput"/> <label for="export_xml">XML</label>
<br/><br/>

<h4><?php echo $Text['other_options']; ?></h4>
<input type="checkbox" name="makePublic" id="makePublic" checked="checked" class="freeInput"/> <label for="makePublic"><?php echo $Text['export_publish']; ?></label>
<br/>
<p id="exportURL">&nbsp;&nbsp;&nbsp;&nbsp;<span class="">http://yourdomain.com/local_config/export/<span id="showExportFileName"></span></span></p>
<br/>
</form>
</div>

