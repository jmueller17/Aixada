<div class="container">
	<div class="row">
		<form id="frm_export_options" class="form-horizontal" role="form">

			<div class="form-group">
				<label for="exportName" class="col-sm-2 control-label"><?=$Text['file_name'];?></label>
				<div class="col-sm-3">
					<input type="text" name="exportName" id="export_name" class="form-control" placeholder="<?=$Text['file_name']; ?>" value="">
				</div>
			</div>


			<div class="form-group section-modal sec-uf">
			    <label for="onlyActiveUfs" class="col-sm-2 control-label"><?=$Text['active_ufs'];?></label>
			    <div class="col-sm-1">
			          <input type="checkbox" name="onlyActiveUfs" id="export_active_ufs" checked="checked" class="checkbox-inline form-control"/> 
			    </div>
			 </div>


			<div id="export_provider_and_products" class="form-group section-modal sec-provider">
				<label for="includeProducts" class="col-sm-2 control-label"><?=$Text['include_products'];?></label>
				<label class="radio-inline">
				  <input name="includeProducts" type="radio" id="include_products_yes" value="1" > <?=$Text['affirm'];?>
				</label>
			
				<label class="radio-inline">
				  <input name="includeProducts" type="radio" id="include_products_no" value="0" checked="checked"> <?=$Text['negate'];?>
				</label>
			</div>



			<div class="form-group">
				<label for="exportFormat" class="col-sm-2 control-label"><?=$Text['export_format'];?></label>
				<label class="radio-inline">
				  <input name="exportFormat" type="radio" id="export_csv" value="csv" checked="checked"> CSV
				</label>
			
				<label class="radio-inline">
				  <input name="exportFormat" type="radio" id="export_xml" value="xml"> XML
				</label>
			</div>



			<!--div id="export_authentication">
			<h4><?php echo $Text['google_account']; ?></h4>
			<table>
			<tr><td><label for="export_email"><?php echo $Text['email']; ?></label> </td><td>&nbsp;</td><td><input type="text" name="email" value="" id="export_email" class="ui-widget ui-corner-all"/>  <br/></td></tr>
			<tr><td><lable for="export_pwd"><?php echo $Text['pwd'];?></lable> </td><td>&nbsp;</td><td><input type="password" name="password" value="" id="export_pwd" class="ui-widget ui-corner-all"/> </td></tr>
			</table>
			<br/><br/>
			</div-->

			<br><br>

			<h4><?=$Text['other_options']; ?></h4>
			<div class="checkbox">
			  <label>
			    <input type="checkbox" name="makePublic" id="makePublic"/> <?=$Text['export_publish']; ?>
			  </label>
			</div>
			<br/>
			<p id="exportURL">&nbsp;&nbsp;&nbsp;&nbsp;<span class="">http://yourdomain.com/loca_config/export/<span id="showExportFileName"></span></span></p>
			<br/>
			</form>
	</div>
</div>

