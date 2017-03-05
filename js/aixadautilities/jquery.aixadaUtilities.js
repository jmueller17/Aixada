
$(function(){
	
	
	/**
	 * sums up shop/Order items in table list and 
	 * extracts the included rev tax and iva amount. 
	 */
	$.extend({
			sumItems : function(sel){
				var sums = [];
				var total = 0; 
				var totalIva = 0; 
				var totalRevTax = 0; 
				var totalNet = 0; 
				$(sel).each(function(){
					var price = new Number($(this).text());
					var iva = new Number($(this).attr('iva'));
					var rev = new Number($(this).attr('revTax'));
				        var net = price / (1 + rev/100) / (1 + iva/100); 
					
					total += price; 
					totalNet += net;
						
					totalIva += net * (iva/100);
					totalRevTax += net * (rev/100); 
										
				});
				
				sums['total'] = total.toFixed(2); 
				sums['totalIva'] = totalIva.toFixed(2); 
				sums['totalRevTax'] = totalRevTax.toFixed(2); 
				sums['total_net'] = totalNet.toFixed(2);
				return sums; 
			},		
			sumSimpleItems : function (sel){
				var total = 0; 
				$(sel).each(function(){
					var price = new Number($(this).text());					
					total += price; 
				});
				return total.toFixed(2); 
				
			},
			formatQuantity : function(obj, cursign){
				
				$('.formatQty',obj).each(function(){
					var b = $(this).text();
		        	var css = (new Number(b) >= 0)? 'aix-style-pos-balance':'aix-style-neg-balance';					
		        	$(this).addClass(css);
		        	if (typeof cursign != 'undefined') $(this).append(cursign);
				})
				
			}
	});
	
	
	/**
	 * utility functions for checking form fields
	 */
	$.extend({
		
		checkFormLength : function (input, min, max, callbackfn) {
			if ( input.val().length > max || input.val().length < min ) {
				input.addClass( "ui-state-error" );
				return false; 
			} else {
				return true; 
			}
		},
		
		checkRegexp : function ( o, regexp, n ) {
			if ( !( regexp.test( o.val() ) ) ) {
				o.addClass( "ui-state-error" );
				return false;
			} else {
				return true;
			}
		},
		
		checkPassword: function (pwd, retyped){
			
			if (pwd.val() != retyped.val()){
				pwd.addClass( "ui-state-error" );
				//$.updateTips('#registerMsg','error', "<?php echo $Text['msg_err_pwdctrl']; ?>");
				return false; 
			} else {
				return true; 
			}
		},
		
		//checks if the given select field has something other than the default value selected
		checkSelect : function(input, defaultValues){
			var selval = input.val();
			var ok = true; 
			for (var i=0; i<defaultValues.length; i++){
				if (selval == defaultValues[i]){
					ok = false;
					break;
				}
			}
			return ok; 
		},
		
		//checks if input is numeric and replaces "," with decimal "." and rounds to fixed. 
		checkNumber : function(input, resetValue, fixed){
			
			var num = input.val();
			num = parseFloat(num.replace(",","."));
			
			if (isNaN(num)) {

				input.addClass("ui-state-error");
				input.effect('pulsate',{},100, function callback(){
						input.val(resetValue);
						input.removeClass("ui-state-error");
					});
				return false;
			} else {
				
				return num.toFixed(fixed); 
				
			}
			
			
		}
	}); //end extend form stuff
	
	
	
	
	$.extend({
		getAixadaDates : function(oper, callbackfn){
			$.ajaxQueue({
				type: "POST",
				url: "php/ctrl/Dates.php?oper="+oper+"&responseFormat=array",		
				dataType: "JSON", 
				success: function(data){
					var availableDates = eval(data);
					if(typeof callbackfn == 'function'){
						callbackfn.call(this, availableDates);
					}
				}, 
				error : function(XMLHttpRequest, textStatus, errorThrown){
					$.showMsg({
						msg:XMLHttpRequest.responseText,
						type: 'error'});
					
				}		
			}); //end retrieve date
			
		},
		//util function to retrieve formated date from datepicker
		getSelectedDate: function(selector, format, defaultValues){
			var formatDate = (format != null && format != '')? format:'yy-mm-dd';
			
			var date = null; 

			switch(defaultValues){
			
				case "Shop":
					date = 0; 
					break;
					
				default: 
					date = $.datepicker.formatDate(formatDate, $(selector).datepicker('getDate'));
					break;
			
			}
			
			return date;
		}, 
		//util function that receives a date string as 'yy-mm-dd' and returns extended french format
		getCustomDate: function(dateString, format){
			var date = $.datepicker.parseDate('yy-mm-dd', dateString);
			var f = (format)? format:'DD, d MM, yy'; 
			return $.datepicker.formatDate(f, date);
		}
		
	});
	
	$.fn.exists = function () {
	    return this.length !== 0;
	};
	
	$.extend({
			updateTips: (function() {
				var _clear_where,
					_clear_style,
					_clear_idTimeout = null,
					_clear_func = function() {
						_clear_idTimeout = null;
						$(_clear_where).hide().text('')
							.removeClass(_clear_style);
					};
				return function(where, type, msg, timing ) {
					
					var style = 'ui-state-highlight';
					var milsecs = (timing >= 0)? timing:10000;
						
					if (type == 'success'){
						style = 'success_tips';
					} else if (type == 'error'){
						style = "ui-state-error" ;
					} else if (type == 'notice'){
						style = 'ui-state-highlight';
					}
					if (_clear_idTimeout) {
						clearTimeout(_clear_idTimeout);
						_clear_func();
					}
					$( where )
						.text( msg )
						.addClass(style).show();
					_clear_where = where;
					_clear_style = style;
					_clear_idTimeout = setTimeout(_clear_func, milsecs );
				};
			})()
	});
	
	
	
	
	/**
	 *	Custom extension to jquery to retrieve URL params of the form
	 *	?key1=value1&key2=value2
	 */
	$.extend({
	  		getUrlVars: function(){
				var vars = [], hash;
				var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
				for(var i = 0; i < hashes.length; i++){
		  			hash = hashes[i].split('=');
		  			vars.push(hash[0]);
		  			vars[hash[0]] = hash[1];
				}
				return vars;
	  		},
	  		getUrlVar: function(name){
				return $.getUrlVars()[name];
	  		}
	});
	
	
	/**
	 * shortcut handling for jquery ui dialog. 
	 */
	$.extend({
		showMsg : function(options){
			
			
			var settings = {
				msg : '',
				title : '',
				width: 400,
				type: "default", 
				autoclose : 0,
				buttons :  [
						     {
							    	icons : { primary : "ui-icon-check" }, //does not work!
									text: "OK", 
									click : function(){
										$( this ).dialog( "close" );
									}
								}

							]
				
			};
			
			if ( options ) { 
				$.extend( settings, options );
			}
							
			var str = '';

			if (settings.type == 'error'){
					settings.title = (settings.title == '')? '$#!@!Error!!!':settings.title;
					str = '<div class="ui-state-error ui-corner-all" style="padding:0.7em;"><span class="ui-icon ui-icon-alert" style="float: left; margin-right: 0.3em;"></span>'+settings.msg+'</div>';
					
			} else if (settings.type == 'info'){
				settings.title = (settings.title == '')? 'Info':settings.title;
					str = '<div>'+settings.msg+'</div>';
					
			} else if (settings.type == 'warning'){
					settings.title = (settings.title == '')? 'Warning':settings.title;
					str = '<div class="ui-state-highlight ui-corner-all" style="padding:0.7em;"><span class="ui-icon ui-icon-info" style="float: left; margin-right: 0.3em;"></span>'+settings.msg+'</div>';
					
			} else if (settings.type == 'help'){
					settings.title = (settings.title == '')? 'Help':settings.title;
					str = '<div class="ui-corner-all" style="padding:0.7em;"><span class="ui-icon ui-icon-help" style="float: left; margin-right: 0.3em;"></span>'+settings.msg+'</div>';
			
			} else if (settings.type == 'confirm')	{
				settings.title = (settings.title == '')? 'Confirm':settings.title;
					str = '<div class="ui-state-highlight ui-corner-all" style="padding:0.7em;"><span class="ui-icon ui-icon-info" style="float: left; margin-right: 0.3em;"></span>'+settings.msg+'</div>';
			
			} else if (settings.type == 'success'){
				settings.title = (settings.title == '')? 'Success':settings.title;
				str = '<div class="ui-state-success ui-corner-all" style="padding:0.7em;"><span class="ui-icon ui-icon-check" style="float: left; margin-right: 0.3em;"></span>'+settings.msg+'</div>';				
					
			} else {
				str = '<div class="ui-corner-all" style="padding:0.7em;">'+settings.msg+'</div>';
				
				
			}
	
				
			$("#aixada_msg").html(str).dialog('option',{	
									title:settings.title,
									width:settings.width,
									buttons : settings.buttons
							}).dialog("open");
			
			if (settings.autoclose > 0){
				setTimeout(function(){
					$("#aixada_msg").dialog('close');
				}, settings.autoclose)
			}
			

		}, 
		closeMsg : function(){
			$('#aixada_msg').dialog( "close" );
			
		}
	});
	
	
	/**
	 * Prepare a jquery ui dialog 
	 */
	
	$('body').append('<div id="aixada_msg" class="msg_dialog"></div>');
	$( "#aixada_msg" ).dialog({
			autoOpen: false
	});
	
	
});