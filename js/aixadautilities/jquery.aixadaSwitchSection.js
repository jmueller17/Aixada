



(function( $ ){
	
	 var methods = {
			  init : function( options ) {

		 		var settings = {
					fadeInTime   		: 1000,
					fadeOutTime			: 1000,
					hist 				: false, 
					gSectionSel			: '.section',
					gListenerSel		: '.sectionSwitchListener',				
					beforeSectionSwitch	: function(){},
					afterSectionSwitch	: function(){}
		  		};
		  		
		  		return this.each(function(){
					
					if ( options ) { 
						$.extend( settings, options );
					}
	   					
					var $this = $(this);
					data = $this.data('mem');	   	
					
					
					// If the plugin hasn't been initialized yet
					if ( ! data ) {		
						$(this).data('mem', {
							fadeInTime		: settings.fadeInTime,
							fadeOutTime		: settings.fadeOutTime,
							hist			: settings.hist,
							gSectionSel 	: settings.gSectionSel,
							gListenerSel 	: settings.gListenerSel,
							beforeSectionSwitch 	: settings.beforeSectionSwitch,
							afterSectionSwitch 		: settings.afterSectionSwitch
							});
					}
				}); //end for each
				
		  		
				
	  		}, //end init
			
	  		changeTo : function (toSectionSel,options){


				if ( options ) { 
	  					$.extend( $(this).data('mem'), options );
	  			}

	  			return this.each(function(){
	  				var $this = $(this);	

	  				var gSelCounter = 0;
	  				var gToCounter = 0;  
	  				var gSelTotal = $($this.data('mem').gSectionSel).length;	
	  				var gToTotal = $(toSectionSel).length;

	  				$this.data('mem').beforeSectionSwitch.call(this);
				    $($this.data('mem').gListenerSel).trigger('beforeSectionSwitch', [toSectionSel]);


				    //hide all elements
				    $($this.data('mem').gSectionSel).fadeOut($this.data('mem').fadeOutTime, function(){
				    	gSelCounter++; 

				    	//trigger fade in animation once everthing is faded out. 
				    	if (gSelCounter == gSelTotal){ 
				    	
							$(toSectionSel).fadeIn($this.data('mem').fadeInTime, function(){
								gToCounter++;

								//trigger events only once, not for each matching element!
								if (gToCounter == gToTotal){
				  					$this.data('mem').afterSectionSwitch.call(this);
									$($this.data('mem').gListenerSel).trigger('afterSectionSwitch', [toSectionSel]);
								}

						   	 });
						}

				    });

				    


				})
	  			
	  		}
	  	}
	  		

	 
	
  $.fn.switchSection = function(method) {
  
	
	// Method calling logic
    if ( methods[method] ) {
      return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
    } else if ( typeof method === 'object' || ! method ) {
      return methods.init.apply( this, arguments );
    } else {
      $.error( 'Method ' +  method + ' does not exist on jQuery.switchSection' );
    }  
	
	
  };
  
})( jQuery );
