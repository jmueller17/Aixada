/**
 * shared functions for adding / editing stock. 
 * used in manage_stock.php and manage_providers.php
 */

function prepareStockForm(section, stockActual, unit, productId){
			if (section == 'add'){
				$('.addStockElements').show();
				$('.correctStockElements').hide();


				$('.sumStock').text(" " + stockActual+" "+unit + " ");
				$('#setStockUnit').text(unit)
				$('#dialog_edit_stock')
					.data('info', {edit:'add', id:productId})
					.dialog('open');

				$('#stock_movement_type_id').val(4);

				//$('.sMovementTypeId').hide(); //children("select").val("4").attr("selected",true);


			} else if (section == 'correct'){
				$('.addStockElements').hide();
				$('.correctStockElements').show();
				$('.sumStock').text(" " + stockActual+" "+unit+ " ");
				$('#setStockUnit').text(unit)
				
				$('#dialog_edit_stock')
					.data('info', {edit:'correct', id:productId})
					.dialog('open');
			}

}


function addStock(productId){
	var addQu = $.checkNumber($('#stock_value'),'',3);
	if (addQu >= 0){
		var st = new Number($('.setStockActualProductPage').text()) + parseFloat(addQu); 
		$('.setStockActualProductPage').text(st);
		submitStock('addStock',productId,addQu);
		
	} else {
		$.showMsg({
			msg: "<?php echo $Text['msg_err_qu']; ?>",
			buttons: {
				"<?=$Text['btn_ok'];?>":function(){					
					$(this).dialog("close");
				},
				"<?=$Text['btn_cancel'];?>" : function(){
					$( this ).dialog( "close" );
				}
			},
			type: 'warning'});
	}
}



function correctStock(productId){
	var absStock = $.checkNumber($('#stock_value'),'',3);	
	
	if (absStock >= 0){
		$('.setStockActualProductPage').text(absStock);
		submitStock('correctStock',productId,absStock);
		
	} else {
		$.showMsg({
			msg: "<?php echo $Text['msg_err_qu']; ?>",
			buttons: {
				"<?=$Text['btn_ok'];?>":function(){						
					$(this).dialog("close");
				},
				"<?=$Text['btn_cancel'];?>" : function(){
					$( this ).dialog( "close" );
				}
			},
			type: 'warning'});
		return false; 
	}

}


/**
 *	saves the stock correction / add to the database
 * 	for "addStock" the current_stock = stock + quantity.
 * 	for "correctStock" current_stock = quantity; 
 */
function submitStock(oper, product_id, quantity){

	var mov_id = $('#stock_movement_type_id').val();
	var comment = $('#stock_movement_description').val();	


	var urlStr = 'php/ctrl/Shop.php?oper='+oper+'&product_id='+product_id+'&quantity='+quantity+'&movement_type_id='+mov_id+'&description='+comment; 
	
	
	$.ajax({
		type: "POST",
		url: urlStr,
		beforeSend : function(){
			$('#stock_actual').attr('disabled', 'disabled');
		},
		success: function(txt){
			$.showMsg({
				msg: "<?php echo $Text['msg_edit_success']; ?>",
				type: 'success',
				autoclose:800});
			
			$('#dialog_edit_stock').dialog("close");
			
			setTimeout(function(){
				$('#product_list_provider tbody, #tbl_products tbody').xml2html("reload");						
			},500);
			

		},
		error : function(XMLHttpRequest, textStatus, errorThrown){
			$.showMsg({
				msg:XMLHttpRequest.responseText,
				type: 'error'});
			
		},
		complete: function(){
			$('#stock_actual')
				.removeAttr('disabled')
				.val('');
			/*$('.btn_save_new_stock, .btn_correct_stock')
				.button('label','Save')
				.button('enable');	*/
		}
	});
}

