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
<br/><br/>
<p id="infoStockPage"><?php echo $Text['info_stock_page']; ?></p>
<p id="infoStockProductPage"><?php echo $Text['stock_info_product']; ?></p>
</form>
</div>