
$(function(){
	
	
	$.extend({
		getAixadaDates : function(oper, callbackfn){
			$.ajax({
				type: "GET",
				url: "ctrlDates.php?oper="+oper+"&responseFormat=array",		
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
		getSelectedDate: function(selector, format){
			formatDate = (format != null && format != '')? format:'yy-mm-dd';
			return $.datepicker.formatDate(formatDate, $(selector).datepicker('getDate'));
		}		
		
	});
	
	$.extend({
			updateTips: function(where, type, msg, timing ) {
					
					var style = 'ui-state-highlight';
					var milsecs = (timing >= 0)? timing:10000;
						
					if (type == 'success'){
						style = 'success_msg';
					} else if (type == 'error'){
						style = "ui-state-error" ;
					} else if (type == 'notice'){
						style = 'ui-state-highlight';
					}

					$( where )
						.text( msg )
						.addClass(style);
					setTimeout(function() {
						$(where)
							.text('')
							.removeClass(style);
					}, milsecs );
				}
		
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
				
			} else {
				str = '<div class="ui-corner-all" style="padding:0.7em;">'+settings.msg+'</div>';
				
				
			}
	
				
			$("#aixada_msg").html(str).dialog('option',{	
									title:settings.title,
									width:settings.width,
									buttons : settings.buttons
							}).dialog("open");
			

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
			autoOpen: false,
	});
	
	
});