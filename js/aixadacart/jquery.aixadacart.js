(function( $ ){

	
  	var methods = {
  			
     	init : function( options ) {
  		
	  		var settings = {
	  				loadCartURL		: 'php/ctrl/ShopAndOrder.php',
	  				saveCartURL		: 'php/ctrl/ShopAndOrder.php',
	  				autoSave		: 0,						//0 means never. Value should be above 3000 miliseconds, like min. time to add stuff and move away. 
	  	  			date			: 0, 
	  	  			preOrder		: false,
	  	  			unsavedItems	: false,
	  	  			isLoading		: false,
	  	  			saveOnDelete	: true, 					//if items get deleted, the cart is automatically saved
	  	  			cartType		: 'standalone',
	  	  			ajaxType		: 'POST',
	  	  			btnType			: 'submit',
	  	  			decimalsQu		: 2,
	  	  			decimalsPrice	: 2,
	  	  			beforeSubmit 	: function(){},
	  	  			submitSuccess 	: function(){},
	  	  			submitError		: function(msg){},
	  	  			submitComplete	: function(){},
	  	  			loadSuccess		: function(){},
	  	  			countItems		: function(){}
	  		};

  			return this.each(function(){
				
  				var $this = $(this); 
  				
  				if ( options ) { 
					$.extend( settings, options );
				}

  				var data = $this.data('aixadacart');
  			   
				// If the plugin hasn't been initialized yet
				if ( ! data ) {		
					$(this).data('aixadacart', {
						loadCartURL			: settings.loadCartURL,
						saveCartURL 		: settings.saveCartURL,
						autoSave			: settings.autoSave,
						date				: settings.date,
						preOrder			: settings.preOrder,
						unsavedItems		: settings.unsavedItems,
						isLoading			: settings.isLoading,
						saveOnDelete		: settings.saveOnDelete, 
						cartType			: settings.cartType,
						ajaxType			: settings.ajaxType,
						btnType				: settings.btnType,
						decimalsQu			: settings.decimalsQu,
						decimalsPrice		: settings.decimalsPrice,
						beforeSubmit		: settings.beforeSubmit,
						submitSuccess		: settings.submitSuccess,
						submitError 		: settings.submitError,
						submitComplete		: settings.submitComplete,
						loadSuccess			: settings.loadSuccess,
						countItems			: settings.countImtes
						
					});
				}//end if
  				
  				//init a dialog layer to display messages
				constructDialog.call($this);
  				
				//construct the cart: append the str to the DOM
  				constructCart.call($this,$this.data('aixadacart').cartType);  				
  				  				
  				//if we have a preorder, we use tabs	
				if ($this.data('aixadacart').cartType == 'standalone_preorder') $('#cart_tabs').tabs();
				
				//init the submit button; choose "validate" or "save" icon depending on where we are
			    switch($this.data('aixadacart').btnType) {
			    case 'validate':
					$('#btn_submit').button({
						disabled:true,
						icons : {
							secondary: "ui-icon-check"
						},
						label: $.aixadacart.validate
					}); break;
			    case 'save':
					$('#btn_submit').button({
						disabled:true,
						icons : {
							secondary: "ui-icon-disk"
						},
						label: $.aixadacart.save
					}); break;
			    case 'hidden':
					$('#btn_submit').button().hide();
				break;
			    default:
					$('#btn_submit').button({
						disabled:true,
						label: $.aixadacart.submit
					}); 
				}
				
				//check if autoSave has not a nonsense value. Should be at least 3 seconds
				if ($this.data('aixadacart').autoSave>0 && $this.data('aixadacart').autoSave<3000){
						alert("Autosave value must be bigger than 3000 milliseconds!!");
						$this.data('aixadacart').autoSave = 3000;
				}
				
  				//detect the submit
  				$('form',this).submit(function(){
  				
  						$this.data('aixadacart').beforeSubmit.call($this); 
  						hideCartTips.call($this);
  						
  						$('#btn_submit').button({
  								label: $.aixadacart.saving,
  								disabled:true});
  					
  						
  						var btn_label = ($this.data('aixadacart').btnType == 'validate')? $.aixadacart.validate:$.aixadacart.save;
  						
  						var dataSerial = $(this).serialize();
  						$.ajax({
  							   type: $this.data('aixadacart').ajaxType,
  							   url: $this.data('aixadacart').saveCartURL,
  							   data: dataSerial,
  							   dataType: "JSON",
  							   beforeSend : function(){
  							 		$('#cart .cartLoadAnim').show();
  								},
  							   success: function(obj){
  								    $('#global_cart_id').val(obj.cart_id);
  								    $('#global_ts_last_saved').val(obj.ts_last_saved);
								    
  								    //alert("cart load: " + obj.cart_id + " " + obj.ts_last_saved);
  								    
  									$this.data('aixadacart').submitSuccess.call($this, obj);
  									$this.data('aixadacart').unsavedItems = false;
  									updateCartTips.call($this,'success',$.aixadacart.msg.saveSuccess,2000);
  									
  									//if we save, then activate the button right away; for validate we have empty cart, button stays deactivated
  									var keep_disabled = ($this.data('aixadacart').btnType == 'validate')? true:false;
  										$('#btn_submit').button({
							    				label: btn_label,
							    				disabled: keep_disabled});	
  									
  							    },
  							   error : function(XMLHttpRequest, textStatus, errorThrown){
  							    	$this.data('aixadacart').submitError.call($this,XMLHttpRequest.responseText);
  							    	//alert(errorThrown);
  							    	
  							    	//updateCartTips.call($this,'error',XMLHttpRequest.responseText);
  							    	//upon saving/validating error, try again :)
  							    	$('#btn_submit').button({
						    				label: btn_label,
						    				disabled:false})
  							    },
  							   complete : function(msg){
  							    	$('#cart .cartLoadAnim').hide();
  							    	$this.data('aixadacart').submitComplete.call($this);
  							    }
  						}); //end ajax
  						return false; 
  				});//end submit
				
  				//sets in motion the autosave interval. 
  				if ($this.data('aixadacart').autoSave > 0){
  					setInterval(function() {
  								if ($this.data('aixadacart').unsavedItems && $this.data('aixadacart').isLoading == false){
  									$('#cart').submit();
  								}
  							}, $this.data('aixadacart').autoSave );
  				}
			
       }); //end for each
     }, //end init
     
     /**
      * adds an item to the cart. triggers the recalc of the total cost. If the item already exists
      * updates the quantity of the item. 
      */
     addItem : function(itemObj) {
				
				var $this = 
				(itemObj.isPreorder == "true" || itemObj.isPreorder == 1 ) 
				    ? $('#aixada_cart_list_preorder')
   				    : $('#aixada_cart_list');

				//we have unsaved items	
				var _self = $(this);

				//check if input field with given id exists, this means item is already in cart
				var exists = $('#cart_quantity_'+itemObj.id, $this).val(); 

				var emptyRow = false;
				if (itemObj.orderable_type_id == 3) {
					if (itemObj.notes.replace(/ /g, ''
						).replace(/\r/g, ''
						).replace(/\n/g, '') == '') {
						itemObj.notes = '';
					}
					emptyRow = (itemObj.notes === '');
					itemObj.quantity = 0;
				} else {
					if (itemObj.quantity.replace(/ /g, '') === '') {
					    itemObj.quantity = '0';
					}
					itemObj.quantity = formatNumInput(itemObj.quantity);
					emptyRow = (itemObj.quantity == 0);
					itemObj.notes = '';
				}

				
				if (exists) {
					if (emptyRow) { // Remove a empty row
						$('#' + +itemObj.id, $this).remove();
						$this.aixadacart("saveCart");
						return itemObj;
					}
					_self.data('aixadacart').unsavedItems = true; 
				} else { //if it is not, create a new row
					if (emptyRow) { // Don't add a empty row
						return itemObj;
					}
					_self.data('aixadacart').unsavedItems = true; 
					
					//deactivates items whose provider/order has been already closed
					var deaTr = (itemObj.time_left < 0)? 'dim60':'';
					var deaTd = (itemObj.time_left < 0)? 'ui-state-error':'';
					var deaIn = (itemObj.time_left < 0)? 'disabled':'';
					
					//add it as row
					var str = '<tr id="'+itemObj.id+'" class="'+deaTr+'" >'; 
					str += '<td class="hidden">' +
						'<span class="hidden orderable_type_id" >'+itemObj.orderable_type_id+'</span>' +
						'<input type="hidden" name="order_item_id[]" value="'+itemObj.order_item_id+'" id="cart_order_item_id_'+itemObj.id+'" />' +
						'<input type="hidden" name="preorder[]" value="'+itemObj.isPreorder+'" id="preorder_'+itemObj.id+'" />' +
						'<input type="hidden" name="price[]" value="'+itemObj.price+'" id="cart_price_'+itemObj.id+'" />' +
						'<input type="hidden" name="product_id[]" value="'+itemObj.id+'" />' +
						'<input type="hidden" name="iva_percent[]" value="'+itemObj.iva_percent+'" id="cart_iva_percent_'+itemObj.id+'" />' +
						'<input type="hidden" name="rev_tax_percent[]" value="'+itemObj.rev_tax_percent+'" id="cart_rev_tax_percent_'+itemObj.id+'" />' +
					'</td>';
					if (itemObj.time_left < 0){
						str += '<td class="'+deaTd+'"></td>';
					} else {
						str += '<td><span id="del_'+itemObj.id +
							'" class="ui-icon ui-icon-trash cart_clickable" title="' +
							$.aixadacart.deleteRow + '"></span></td>';
					}
                    if (itemObj.orderable_type_id == 3) {
                        str += '<td class="item_text" colspan="5">' +
                                '<span class="item_name '+deaTd+'">'+itemObj.name+'</span> | ' +
                                '<span class="item_provider_name '+deaTd+'">'+itemObj.provider_name+'</span><br>' +
                                '<textarea name="notes[]" class="ui-widget-content ui-corner-all textareaLarge inputTxtMax" id="cart_notes_'+itemObj.id+'">'+itemObj.notes+'</textarea>' +
                            '</td>';
                        str += '<td class="item_quantity '+deaTd+' hidden">' +
                            '<input name="quantity[]" value="'+itemObj.quantity+'" id="cart_quantity_'+itemObj.id+'" size="4" class="ui-corner-all" />'; 
                        str += '<td class="item_unit '+deaTd+' hidden">' + itemObj.unit + '</td>';
                        str += '<td class="item_total '+deaTd+' hidden" id="item_total_'+itemObj.id+'"></td>';
                    } else {
                        str += '<td class="item_name '+deaTd+'">'+itemObj.name+'</td>';
                        str += '<td class="item_provider_name '+deaTd+'">'+itemObj.provider_name+'</td>';
                        str += '<td class="item_quantity '+deaTd+'">' +
                            '<input name="quantity[]" value="'+itemObj.quantity+'" id="cart_quantity_'+itemObj.id+'" size="4" class="ui-corner-all" />' +
                            '<textarea name="notes[]" class="hidden" id="cart_notes_'+itemObj.id+'"></textarea>';
                        str += '<td class="item_unit '+deaTd+'">' + itemObj.unit + '</td>';
                        str += '<td class="item_total '+deaTd+'" id="item_total_'+itemObj.id+'"></td>';
                    }
					str += '</tr>';

					
					$this.prepend(str);

					//event listener to remove items from cart
					$("#del_"+itemObj.id, $this)
						.bind("mouseenter", function(){
								$(this).removeClass('ui-icon-trash').addClass('ui-icon-circle-close');
						})
						.bind("mouseleave", function(){
							$(this).removeClass('ui-icon-circle-close').addClass('ui-icon-trash');
						})
						.bind("click", function(e){
							$(this).parents('tr')
								.find('textarea').val('').end()
								.find('input').val(0).trigger('change').end();
								// Trigger chage when quantity=0 forces a remove and a save
						}
					);

					//event listener to detect quantity changes in cart
					$("#cart_quantity_"+itemObj.id, $this)
						.bind("focus", function(e){
							if($(this).parent().hasClass('ui-state-error')){
				    			$("#cart_dialog")
				    				.html($.aixadacart.msg.orderClosed)
				    				.dialog('option','title','Warning')
				    				.dialog("open");
								$(this).blur();
								return false; 
							}
						
							
						})
						.bind("change", function(e){
												
						//we have unsaved items
						_self.data('aixadacart').unsavedItems = true;
						
						//retrieve all the info of the current cart item
						var objItem = $this.aixadacart("getRowData", {
			  												type : 'table',
			  												row :  $(this).parents("tr")
			  			});
						//update the row / calculate the price
						updateRow.call($(this),objItem);
						
						//update the row in the all product list
						$('.quantity_'+objItem.id).val( 
						    objItem.quantity != 0 ? objItem.quantity : ''
						);
						$('.notes_'+objItem.id).val(objItem.notes); // Used in delete row

						//recalculate total cost
						calculateTotal.call();

						if (objItem.quantity == 0) { // remove empty row
						    $(this).parents('tr').remove();
						    $this.aixadacart("saveCart");
						}
						
					});//end event listener

                    $("#cart_notes_"+itemObj.id, $this).bind(
                        "focus", function(e){
                            if($(this).parent().hasClass('ui-state-error')){
                                $("#cart_dialog")
                                    .html($.aixadacart.msg.orderClosed)
                                    .dialog('option','title','Warning')
                                    .dialog("open");
                                $(this).blur();
                                return false; 
                            }
                        }
                    ).bind(
                        "change", function(e){
                            //we have unsaved items
                            _self.data('aixadacart').unsavedItems = true;
                            //retrieve all the info of the current cart item
                            var objItem = $this.aixadacart("getRowData", {
                                type : 'table',
                                row :  $(this).parents("tr")
                            });
                            //update the cart and notes on other list
                            updateRow.call($(this),objItem);
                            $('.notes_'+objItem.id).val(objItem.notes);
                            
                            if (objItem.notes.replace(/ /g, ''
                            ).replace(/\r/g, ''
                            ).replace(/\n/g, '') == '') { // remove empty row
                                $(this).parents('tr').remove();
                                $this.aixadacart("saveCart");
                            }
                        }
                    );//end event listener
				}
				
				
				//calculate and set cost of item
				updateRow.call($this, itemObj);
				$('.quantity_'+itemObj.id).val(itemObj.quantity);
				$('.notes_'+itemObj.id).val(itemObj.notes);
				calculateTotal.call();
				
				//make sure the submit button is active
				$('#btn_submit').button( "option", "disabled", false );
			
				return itemObj;
     }, //end addItem 
     
     
     /**
      * sets the options for the cart 
      */
     options : function (options){
    	 return this.each(function(){
    		 var data = $(this).data('aixadacart');
    		 
    		 //if the new date is different from the current one, emtpy the cart!
    		 if (options.date != undefined && data.date != options.date) {
    			 $(this).aixadacart("setDate",options.date);
    		 }
    		 
    		 if (options) { 
    			$.extend(data, options );
    		 }
    			
    	 });//end each
     },
     
     
     /**
	   * sets the date for the shopping cart. A hidden input field will pass the date
	   * to the server as part of the form data. 
	   */
	  setDate : function(date){
		  return this.each(function(){
			  
			  //save it with the plugin
			  $(this).data('aixadacart').date = date; 
			  
			  //update the hidden input date field of the cart
			  $('#cart_date',$(this)).val(date);
			 
			  //remove all items
			  $(this).aixadacart("removeAll");
			  
		  });
	  }, //end set date
     
	  
     /**
      * loads all order items for a given date. 
      */
	 loadCart : function (options){
    	 
    	 return this.each(function(){
    		 
    		 var $this = $(this);
    		 
    		 if (options.saveCartURL) {
    			 $("#cart_dialog").html('Should not change the save cart URL while loading!').dialog('option','title','Warning').dialog("open");
    		 }
    			 
    		 $this.aixadacart("options",options);
    		 var _loadCartHeadURL = (options.loadCartHeadURL ?
                    options.loadCartHeadURL : null);
    		 $this.data('aixadacart').isLoading = true;
    		 $('#cart .cartLoadAnim').show();
    		 
    		 $.ajax({ 
    			type:"GET", 
 				url: $this.data('aixadacart').loadCartURL,
 				dataType: "xml",
 				success: function(xml){
 					
 					var lastCartId = -1;
 					var ts_last_saved = 0; 
 					
    			 	$(xml).find('row').each(function(){
			  			var objItem = $this.aixadacart("getRowData", {
			  				type : 'xml',
			  				row : $(this)			  				
			  			});
			  			if (lastCartId > 0 && lastCartId != objItem.cart_id){
			  				updateCartTips.call($this,'error','cart_id mismatch: ' + lastCartId + " and " + objItem.cart_id);
			  				return false; 
			  			}
			  			lastCartId = objItem.cart_id; 
			  			ts_last_saved = objItem.ts_last_saved;
			  			$this.aixadacart("addItem",objItem);
			  		 
			  		});// end each row	
    			 	
                    if (lastCartId === -1 && _loadCartHeadURL) {
                        // Is a empty cart but is set loadCartHeadURL option.
                        $.ajax({ 
                            type:"GET", 
                            url: _loadCartHeadURL,
                            dataType: "xml",
                            success: function(xml){
                                var lastCartId = -1;
                                var ts_last_saved = 0; 
                                $(xml).find('row').each(function(){
                                    lastCartId = $(this).find('cart_id').text();
                                    ts_last_saved = $(this).find('ts_last_saved').text();
                                });
                                $('#global_cart_id').val(lastCartId);
                                $('#global_ts_last_saved').val(ts_last_saved);
                            }
                        });
                    } else {
                        $('#global_cart_id').val(lastCartId);
                        $('#global_ts_last_saved').val(ts_last_saved);
                    }
    			 	
    			 	$this.data('aixadacart').loadSuccess.call(this);
    			 	$this.data('aixadacart').unsavedItems = false;
    			 	
    			 	
     		 	},//end success ajax
     		 	error : function(XMLHttpRequest, textStatus, errorThrown){
     		 		//alert(errorThrown);
     		 		updateCartTips.call('error',XMLHttpRequest.responseText);
					
     		 	},
     		 	complete : function(){
     	    		 $this.data('aixadacart').isLoading = false;
     	    		 $('#cart .cartLoadAnim').hide();
     		 	}
    		 }); //end ajax
    	 });//end each
	  },  //end loadCart   
	  
	  /**
	   * remove event listeners and free memory
	   */
	  destroy : function( ) {
	       return this.each(function(){
	           var data = $(this).data('aixadacart');
	    	   $(window).unbind('.aixadacart');
	    	   data.aixadacart.remove();
	    	   $(this).removeData('aixadacart');
	       })
	  },	  
	  
	  /**
	   * resets the cart: removes all items, and sets the date to 0
	   */
	  resetCart : function(){
		  return this.each(function(){
			  $(this).aixadacart('setDate',0); 
		  });
		  
	  },
	  
	  /**
	   * removes all items from the cart. similar to resetCart but does not delete date
	   */
	  removeAll : function(){
		  return this.each(function(){
			  $("#aixada_cart_list tbody tr").empty().remove();
			  calculateTotal.call();
			 $('#btn_submit').button( "option", "disabled", true );
		  });
		  
	  },//end remove all
	  
	  /**
	   * returns if a cart has unsaved items. 
	   */
	  hasUnsavedItems : function(){
			  return $(this).data('aixadacart').unsavedItems;
	  },
	 
	  saveCart : function(){
		  //NOTE: cartLayer has to be changed, this is tempfix because $(this) is not returning the correct reference. 
		  //something wrong with plugin architecture?!?!?!?!?!?!?!?!?
		 if ($('#cartLayer').data('aixadacart').saveOnDelete){ 
			 $('#cart').submit();  
		 }
	  },
	  
	  countItems : function(){
		  var nrOfItems = 0; 
		  $("#aixada_cart_list tbody tr").each(function(){
				nrOfItems++;
			});
		  return nrOfItems;
	  },
	  
	  /**
	   * retrieves the data fields / values from a given table or xml row 
	   * some values are retrieved from the shop/order item listing, others
	   * always come directly from the server such as order_item_id, cart_id
	   */
	  getRowData : function(options){
		  
		  var objItem; 
		  
		  if (options.type == "xml"){
			  var row = options.row;
			  objItem = {
					id 					: $(row).find('id').text(),
					isPreorder			: $(row).find('preorder').text(),
					provider_name 		: $(row).find('provider_name').text(),
					name 				: $(row).find('name').text(),
					orderable_type_id	: $(row).find('orderable_type_id').text(),
					notes				: $(row).find('notes').text(),
					quantity 			: $(row).find('quantity').text(),
					unit 				: $(row).find('unit').text(),
					price 				: parseFloat($(row).find('unit_price').text()),
					rev_tax_percent		: parseFloat($(row).find('rev_tax_percent').text()),
					iva_percent			: $(row).find('iva_percent').text(),
					order_item_id		: $(row).find('order_item_id').text(),
					cart_id 			: $(row).find('cart_id').text(),
					time_left			: $(row).find('time_left').text(),
					ts_last_saved		: $(row).find('ts_last_saved').text()
				};
			 
		  } else if (options.type == "table"){
			  var row = options.row;
			  var id =  $(row).attr("id"); 
			  quantity = $("#cart_quantity_"+id).val();
			  if (quantity.replace(/ /g, '') === '') {
			    quantity = '0';
			  }
			  objItem =  {
					id 				: id,
					isPreorder		: $('#preorder_'+id).val(),
					name 			: $(".item_name", row).text(),
					orderable_type_id: $(".orderable_type_id", row).text(),
					notes			: $("#cart_notes_"+id).val(),
					provider_name 	: $("td.item_provider_name", row).text(),
					quantity 		: formatNumInput(quantity),
					unit 			: $("td.item_unit", row).text(),
					price 			: parseFloat($("#cart_price_"+id, row).val()),
					rev_tax_percent	: parseFloat($("#cart_rev_tax_percent_"+id, row).val()),
					iva_percent		: $("td.item_iva_percent", row).text(),
					order_item_id	: $("#cart_order_item_id_"+id,row).val(),
					cart_id 		: $("#global_cart_id").val(),
					ts_last_saved   : $("#global_ts_last_saved").val()
				};
			  //alert(objItem.id + " qu: " + objItem.quantity + " price: " + objItem.price + " tax: " + objItem.rev_tax_percent);
			  
		  }
		  return objItem;

	  } //end getRowData

  	};//end public plugin methods

    // See: http://stackoverflow.com/questions/21091727/javascript-tofixed-function
  	function round2dNum(num){
        return Math.round(num * 100) / 100;
    }
    function round2dText(num){
        return (Math.round(num * 100) / 100).toFixed(2);
    }
  	/**
  	 *  utility function; replaces "," with "." and makes sure no letters are in the quantity input
  	 */
  	function formatNumInput(value){
  			
  		var fmtInput = null;
		fmtInput = parseFloat(value.replace(",","."));
				
		if (isNaN(fmtInput)){
			$("#cart_dialog")
			.html($.aixadacart.msg.errInput)
			.dialog('option','title','Warning')
			.dialog("open");
			return 0;
		} else {
			return fmtInput; 
		}
  	}
  	
	/**
	 *	updates the information for given cart row
	 *	when quantity has changed, calculates new item total cost
	 */
	function updateRow(itemObj){
		var item_total 		= itemObj.price * itemObj.quantity; 
		//set/update quantity (set the value if quantity has been changed in product list for example
		$("#cart_quantity_"+itemObj.id).val(itemObj.quantity);
		$("#cart_notes_"+itemObj.id).val(itemObj.notes);
			
		//set/update the total cost for item
		$("#item_total_"+itemObj.id).text(String(round2dText(item_total)));
	}
	
	
	/** 
	 *  recalculates the total cost of all items in the cart 
	 */
	function calculateTotal(){
		 
			var total = 0; 
			var total_net = 0;
			var rev_tax_total = 0; 
			var iva_tax_total = 0; 
			
			$("#aixada_cart_list tbody tr").each(function(){
				
				var id = $(this).attr("id");
				var quantity 	= parseFloat($("#cart_quantity_"+id, $(this)).val());
				var price    	= parseFloat($("#cart_price_"+id, $(this)).val());
				var rev_tax 	= parseFloat($("#cart_rev_tax_percent_"+id, $(this)).val());
				var iva_tax 	= parseFloat($("#cart_iva_percent_"+id, $(this)).val());
				
				var item_total 	= round2dNum(price * quantity); 
				
  			        var item_net = item_total / (1 + rev_tax/100) / (1 + iva_tax/100);
				
				//iva and revtax is contained in the unit_price
				rev_tax_total += item_net * (rev_tax/100);
				iva_tax_total += item_net * (1+(rev_tax/100)) * (iva_tax/100);
	
				total_net += item_net;
				
				total += item_total;
			});

			$('#aixada_cart_list td.total_net').text(String(round2dText(total_net)));
			$('#aixada_cart_list td.iva_tax_total').text(String(round2dText(iva_tax_total)));
			$('#aixada_cart_list td.rev_tax_total').text(String(round2dText(rev_tax_total)));
			$('#aixada_cart_list td.total').text(String(round2dText(total)));
	}
  	
	
	function updateCartTips (type, msg, timing ) {
		
		var style = 'ui-state-highlight';
		var milsecs = (timing >= 0)? timing:10000;
			
		if (type == 'success'){
			style = 'cart_success_msg';
		} else if (type == 'error'){
			style = 'ui-state-error';
		} else if (type == 'notice'){
			style = 'ui-state-highlight';
		}

		msg = (!msg)? 'error during cart loading/saving':msg; 
		
		$('#cartMsg').html(msg).addClass(style);
		setTimeout(function() {
			$('#cartMsg')
				.html('')
				.removeClass(style);
		}, milsecs );
	}
	
	function hideCartTips(){
		$('#cartMsg')
			.html('')
			.removeClass('cart_success_msg, ui-state-error, ui-state-highlight');
	}
	
	function constructDialog(){
		
		//create the dialog for the messages
			$(this).append('<div id="cart_dialog"></div>');
			$( "#cart_dialog" ).dialog({
				autoOpen: false,
				buttons: {
					Ok: function() {
						$( this ).dialog( "close" );
					}
				}
			});
		
	}
	
	
	function constructCart(which){
		
		var str = '';
		
		var tbl_head = '<thead>';
		tbl_head += '<tr>';
		tbl_head += '	<th></th>';
		tbl_head += '	<th>'+$.aixadacart.tblCol.productName+'</th>';
		tbl_head += '	<th>'+$.aixadacart.tblCol.providerName+'</th>';
		tbl_head += '	<th>'+$.aixadacart.tblCol.quantityAbbrev+'</th>';
		tbl_head += '	<th>'+$.aixadacart.tblCol.unit+'</th>';
		tbl_head += '	<th>'+$.aixadacart.total+'</th>';
		tbl_head += '</tr>';
		tbl_head += '</thead>';
		
		var tbl_foot = 	'<tfoot>';
		tbl_foot += '		<tr><td colspan="4">&nbsp;</td><td class="total_net_label">'+$.aixadacart.total_net+'</td><td class="total_net cart_dblBorderTop">0.00</td></tr>';
		tbl_foot += '		<tr><td colspan="5" class="rev_tax_label">'+$.aixadacart.revTaxInclAbbrev+'</td><td class="rev_tax_total">0.00</td></tr>';
		tbl_foot += '		<tr><td colspan="5" class="iva_tax_label">'+$.aixadacart.ivaTaxInclAbbrev+'</td><td class="iva_tax_total">0.00</td></tr>';
		tbl_foot += '		<tr><td colspan="4">&nbsp;</td><td class="total_label">'+$.aixadacart.total+'</td><td class="total">0.00</td></tr>';
		tbl_foot += '		<tr><td colspan="3"><p id="cartMsg"></p></td><td colspan="3"><button type="submit" id="btn_submit">'+$.aixadacart.submit+'</button></td></tr>';
		tbl_foot += '	</tfoot>';
		
		if(which == 'simple'){
			str += '<form id="cart">';
			str += '<input type="hidden" name="date" id="cart_date" value="0" />';
			str += '<input type="hidden" name="cart_id" id="global_cart_id" value="" />';	
			str += '<input type="hidden" name="ts_last_saved" id="global_ts_last_saved" value="" />';	
			str += '<table id="aixada_cart_list" class="cart_product_list">';
			str += tbl_head;
			str += '<tbody></tbody>';
			str += tbl_foot;
			str += '</table>';
			//str += '<p id="cartMsg"></p>';
			str += '</form><br/><br/><br/>';
			
			
			
		//standalone widget with main and preorder tabs
		} else if (which == 'standalone_preorder'){
		
			str += '<div id="aixada_cart_standalone_preorder" class="ui-widget">';
			
			str += '<form id="cart">';
			str += '<input type="hidden" name="date" id="cart_date" value="0" />';
			str += '<input type="hidden" name="cart_id" id="global_cart_id" value="" />';	
			str += '<input type="hidden" name="ts_last_saved" id="global_ts_last_saved" value="" />';	
			str += '<div id="cart_tabs">';
			str += '<ul>';
			str += '<li><a href="#tabsx-1" style="font-size:1.6em;">'+$.aixadacart.title.order+'</a></li>';
			str += '<li><a href="#tabsx-2" style="padding:1.20em;">'+$.aixadacart.title.preorder+'</a></li>';
			str += '</ul>';
			str += '<div id="tabsx-1">';
			str += '<span class="cartLoadAnim cart_floatRight cart_animOrder cart_hidden"><img class="loadSpinner" src="img/ajax-loader.gif"/></span>';
			str += '	<table id="aixada_cart_list" class="cart_product_list">';
			str += tbl_head;
			str += '	<tbody></tbody>';
			str += tbl_foot;
			str += '</table>';
			//str += '<p id="cartMsg"></p>';
			str += '</div>';
			str += '<div id="tabsx-2">';
			str += '		<table id="aixada_cart_list_preorder" class="cart_product_list">';
			str += tbl_head;
			str += '		<tbody></tbody>';
			str += '</table>';
			//str += '<p id="cartMsg"></p>';
			str += '</div></div></form></div><br/><br/><br/>';
			
		//default is standalone
		} else {
			str += '<div id="aixada_cart_standalone" class="ui-widget">';
			str += '<form id="cart">';
			str += '<input type="hidden" name="date" id="cart_date" value="0" />';
			str += '<input type="hidden" name="cart_id" id="global_cart_id" value="" />';	
			str += '<input type="hidden" name="ts_last_saved" id="global_ts_last_saved" value="" />';	
			str += '<div class="cart_container ui-widget-content ui-corner-all">';
			str += '<h2 class="ui-widget-header ui-corner-all"> '+$.aixadacart.title.shop+'<span class="cartLoadAnim cart_floatRight cart_hidden"><img class="loadSpinner" src="img/ajax-loader.gif"/></span></h2>';
			str += '<table id="aixada_cart_list" class="cart_product_list">';
			str += tbl_head;
			str += '	<tbody></tbody>';
			str += tbl_foot;
			str += '</table>';
			//str += '<p id="cartMsg"></p>';
			str += '</div>';
			str += '</form></div><br/><br/><br/>';
			
			
		} //end if which
		
		
		$(this).append(str);	
	}
  	
  	
  	/**
  	 * init routine for the plugin. 
  	 */
	$.fn.aixadacart = function( method ) {
		if ( methods[method] ) {
			return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
    	} else if ( typeof method === 'object' || ! method ) {
    		return methods.init.apply( this, arguments );
    	} else {
    		$.error( 'Method ' +  method + ' does not exist on jQuery.aixadacart' );
    	}
		

  };

})( jQuery );