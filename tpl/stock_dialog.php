<div id="container">
<form id="frm_stock">
<h3 class="addStockElements"><?php echo $Text['add_stock'];?></h3>
<h3 class="correctStockElements"><?php echo $Text['correct_stock'];?></h3>
<br/>

<p>
<span class="addStockElements"><?php echo $Text['add_stock_frase']; ?>&nbsp;</span><span class="addStockElements sumStock"></span> 
<span class="addStockElements">+</span>
<span class="correctStockElements floatleft"><?php echo $Text['correct_stock_frase']; ?></span>&nbsp;<span class="correctStockElements sumStock"></span>&nbsp;<span class="correctStockElements"><?php echo $Text['stock_but'];?> &nbsp;</span>
<input type="text" name="stockValue" id="stock_value" class="ui-widget ui-corner-all inputTxtTiny freeInput"/> <span id="setStockUnit"></span>
</p>
<br/>
<table class="tblForms">
	<tr class="correctStockElements">
		<td><label for="stock_movement_type_id">&nbsp; <?php echo $Text['stock_mov_type'];?> </label></td>
		<td>
			<input type="hidden" name="stock_movement_type_id" id="stock_movement_type_id" value="1"/>
			<span class="textAlignLeft sMovementTypeId"></span>			
		</td>
	</tr>
	<tr>
		<td><label for="description">&nbsp; <?php echo $Text['comment']; ?>  </label></td>
		<td>
			<input type="text" name="description" id="stock_movement_description" value="" class="ui-widget ui-corner-all inputTxtMiddle">
		</td>
	</tr>
<tr></tr>
</table>
<!--p class="correctStockElements ui-state-highlight aix-style-padding8x8 ui-corner-all"><?php echo $Text['msg_correct_stock']; ?></p-->
<p id="infoStockPage" class="aix-style-padding8x8"><?php echo $Text['stock_info']; ?></p>
<p id="infoStockProductPage" class="aix-style-padding8x8"><?php echo $Text['stock_info_product']; ?></p>
</form>
</div>