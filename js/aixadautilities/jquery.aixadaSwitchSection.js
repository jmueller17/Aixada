
/**
 * library to faciliate switching between different interface sections in one 
 * and the same HTML page (that are usually loaded with ajax)
 * Usage: links or buttons that trigger a section change should have the class="change-to" attribute
 * attached. The target is specified with the href="#sec-1" #sec-2 etc. 
 * The different sections of the page are marked <div class="section sec-1"></div> or <div class="section sec-2"></div>
 * All the rest happens automatically. 
 */


(function( $ ){
	
	 var methods = {
			  init : function( options ) {

		 		var settings = {
					fadeInTime   		: 1000,
					fadeOutTime			: 0,
					hist 				: false,  			//TODO: php side save to session last page
					gSectionSel			: '.section',	 	//selector to hide all sections. 
					gListenerSel		: '.sectionSwitchListener',	//selector for all section change event listerens			
					gCurrentSel			: '', //currently active selection
					beforeSectionSwitch	: function(targetsection){},
					afterSectionSwitch	: function(targetsection){}
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
							gCurrentSel		: settings.gCurrentSel,
							beforeSectionSwitch 	: settings.beforeSectionSwitch,
							afterSectionSwitch 		: settings.afterSectionSwitch
							});
					}

					//detect clicks on "change-to" selectors. 
					$this.bind("click", function () {
							var target = null; //target = $(this).attr('href'); 

							if (typeof $(this).attr('href') != 'undefined'){
								target = $(this).attr('href');	
								target = target.slice(1,target.length);
         													

							} 

							if (typeof $(this).attr('target-section') != 'undefined' ){
								target = $(this).attr('target-section')
								target = target.slice(1,target.length);
         					
							}

							if (typeof $(this).attr('toggle-section') != 'undefined'){
								toggle = $(this).attr('toggle-section')
								targets = toggle.split(",")

								for (var i=0; i<targets.length; i++){
									targets[i] = targets[i].slice(1,targets[i].length);
								}

								target = ($("."+targets[0]).is(':visible'))? targets[1]:targets[0]
							
							}

							if (target==null) {
								alert("No target section defined on switch")
								return false; 
							}

							
							$this.switchSection("changeTo", "."+target);
       				});  	


				}); //end for each
				
		  		
				
	  		}, //end init

	  		//returns the currently active selection (the last selection, faded to)
	  		getCurrentSel : function(){
	  			return $(this).data('mem').gCurrentSel;
	  		},
			
	  		changeTo : function (toSectionSel,options){


				if ( options ) { 
	  					$.extend( $(this).data('mem'), options );
	  			}

  				var $this = $(this);	

  				//this is not contained in an each iterator because we just want to trigger 
  				//the transition once and not once-for-every-matching-element

  				//some vars to make sure the events are only triggered once. 
  				var gSelCounter = 0;
  				var gToCounter = 0;  
  				var gSelTotal = $($this.data('mem').gSectionSel).length;	
  				var gToTotal = $(toSectionSel).length;
  				var gSectionSel = $this.data('mem').gSectionSel;
  				var gListenerSel = $this.data('mem').gListenerSel; 

  				$this.data('mem').beforeSectionSwitch.call(this, toSectionSel);
			    $(gListenerSel).trigger('beforeSectionSwitch', [toSectionSel]);


			    //hide all elements
			    $(gSectionSel).fadeOut($this.data('mem').fadeOutTime, function(){
			    	gSelCounter++; 

			    	//trigger fade in animation once everthing is faded out. 
			    	//depending on nr of matching selectors this is called many times!
			    	if (gSelCounter == gSelTotal){ 
			    	
						$(toSectionSel).fadeIn($this.data('mem').fadeInTime, function(){
							gToCounter++;

							//trigger events only once, not for each matching element!
							if (gToCounter == gToTotal){
								$this.data('mem').gCurrentSel = toSectionSel;
			  					$this.data('mem').afterSectionSwitch.call(this, toSectionSel);
								$(gListenerSel).trigger('afterSectionSwitch', [toSectionSel]);
							}

					   	 });
					}

			    });

	  		}//end changeTo
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
