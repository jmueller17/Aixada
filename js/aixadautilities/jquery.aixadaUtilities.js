
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
			alert(selval)
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
			$.ajax({
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
			}); //end ajax retrieve date
			
		},
		//util function to retrieve formated date from datepicker
		getSelectedDate: function(selector, toFormat, inFormat, defaultValues){
			var toDateFormat = (toFormat != null && toFormat != '')? toFormat:'YYYY-MM-DD';
			var inDateFormat = (inFormat != null && inFormat != '')? inFormat:'dddd, ll';
			
			var date = null; 

			switch(defaultValues){
			
				case "Shop":
					date = 0; 
					break;
					
				default: 
					date = moment($(selector).data("DateTimePicker").getDate(), inDateFormat).format(toDateFormat);
					break;
			
			}
			return date;
		}, 
		//util function that receives a date string as 'yy-mm-dd' and returns extended french format
		getCustomDate: function(dateString, format){
			var date = moment(dateString, 'YYYY-MM-DD');
			var f = (format)? format:'dddd, ll'; 
			return moment(date).format(f);
		}
		
	});
	
	$.fn.exists = function () {
	    return this.length !== 0;
	};
	
	
	
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
	
	
	
});