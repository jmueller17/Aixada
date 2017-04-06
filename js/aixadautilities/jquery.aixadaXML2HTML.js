/**
 * TODO: right now, the same page can only have one pagination. Should be that pagination with all associated fields is instance specific!!!
 * 
 */

(function( $ ){
	
	 var methods = {
			  init : function( options ) {

		 		var settings = {
					url         	: 'php/ctrl/ShopAndOrder.php',				
					params 			: 'oper=listProviders',
					rowName			: 'row', 
					xml 			: null,							//the xml once loaded/received
					offSet			: 0,							//indicates the offset when deleting dynamic rows. 
					listHeader		: null,							//fixed content that always should be insert before the actual dynamic content
					tpl				: '',							//template HTML string if no template is provided in the HTML.
					paginationNav	: '',							//the selector-element for pagination of results. 
					type			: 'GET',
					resultsPerPage	: 10,							//default results per page, if paginationNav is active
					loadOnInit		: false, 						//if the xml is loaded on "ini" call
					rowCount		: 0,							//nr of total xml result rows (specified by rowName)
					autoReload		: 0,							//milliseconds before this.reload is issued. 0 = never
					beforeLoad	: function(){},
					complete	: function(row_count){},			//called, once loop through xml has finished
					rowComplete : function(current_row_index, row){}//called, after each row 
		  		};
		  		
		
		  		
		  		
		  		return this.each(function(){
					
					if ( options ) { 
						$.extend( settings, options );
					}
	   					
					var $this = $(this);
					var data = $this.data('xml2html');
	   	
					//if an offset is specified, then we look for a list header
					//the container element can have a list header which can be specified with the lt(index) which counts
					//back to 0. 
					if (settings.offSet > 0){
						var listHeader =  $this.children(':lt('+settings.offSet+')').detach(); 
						if (listHeader.size()>0){
							settings.listHeader = listHeader;
						} 
					}

					
					//a template is either directly provided as string, if not, everything that 
					//remains after a potential header has been detached is detached from the DOM and used as template. 
					//set the template string
					settings.tpl = (settings.tpl != '')? settings.tpl:$this.html();
					
					//delete all content / templates in the original DOM
					$this.empty();
				   
					
					//construct pagination nav if a selector has been specified
					if (settings.paginationNav != ''){
						_constructPagination.call($this,settings.paginationNav);
						$('#selectResultsPP').val(settings.resultsPerPage);
						
					}
					
					
					// If the plugin hasn't been initialized yet
					if ( ! data ) {		
						$(this).data('xml2html', {
							url				: settings.url,
							params 			: settings.params,
							rowName			: settings.rowName,
							xml				: settings.xml,
							offSet			: settings.offSet,
							listHeader		: settings.listHeader,
							tpl				: settings.tpl,	
							type			: settings.type,
							resultsPerPage	: settings.resultsPerPage,
							paginationNav	: (settings.paginationNav == '')? false:true,
							loadOnInit		: settings.loadOnInit,
							rowCount		: settings.rowCount,
							
							autoReload		: settings.autoReload,
							beforeLoad		: settings.beforeLoad,
							complete 		: settings.complete,
							rowComplete 	: settings.rowComplete,
							_pageIndex		: 0
							
						});
					}//end if
					if (settings.loadOnInit) $this.xml2html("reload");
				}); //end for each
				
		  		
				
	  		}, //end init
			
	  		removeAll : function (){
	  			return this.each(function(){
	  				$(this).empty();
	  				$(this).append($(this).data('xml2html').listHeader);
	  			});
	  		},
	  		
	  		getXML : function(){
	  			
	  			return $(this).data('xml2html').xml;
	  		},
	  		
	  		getTemplate : function(){
	  			return $(this).data('xml2html').tpl;
	  			
	  		},
	  		
	  		reload : function( options ) {
					
	  			
	  				if ( options ) { 
	  					$.extend( $(this).data('xml2html'), options );
	  				}
	  			
					return this.each(function(){
					    
				    	var $this = $(this);
				    	
				    	
				    	var type 	= $this.data('xml2html').type;
				    	var url 	=  $this.data('xml2html').url;
				    	var params 	=  $this.data('xml2html').params;
				    	var rowName =  $this.data('xml2html').rowName;
	    	
				    	//load the initial list
				    	$.ajaxQueue({
									type: type,
									url: url + "?" + params,		
									dataType: "xml", 
									beforeSend : function(jqXHR, settings){
				    					//$this.empty().append('Loading...');
				    					$this.data('xml2html').beforeLoad.call(this,0);
				    				},
									success: function(xml){
										
				    					//save xml result set
				    					$this.data('xml2html').xml = xml;
				    					//determine its size
				    					$this.data('xml2html').rowCount = $(xml).find(rowName).size();
				    					
				    					
										var i=0;
										//extract all available tag names
										$(xml).find(rowName+':first').children().each(function(){
											_tagNames[i++] = $(this)[0].tagName; 
										});
														
										//calculate how many results pages this makes
				    					_calcResultPages.call($this);
				    					
										_loopXML.call($this);
										
										
									},//end success
									
									error : function(XMLHttpRequest, textStatus, errorThrown){
	  							    	alert('An error "' + errorThrown + '", status "' + textStatus + '" occurred during loading data: ' + XMLHttpRequest.responseText);

	  							    },
	  							   complete : function(msg){
	  							 	
	  							    }
						});	//end
				    	
				    	
				    	
				    	//alert("in "+rowIndex )
				    	
				    	
				 
						if ($this.data('xml2html').autoReload > 1){
							
							setTimeout(function(){
								$this.xml2html("reload");
							},$this.data('xml2html').autoReload);
							
						
						}
				    	
				    }); //end this.each
					
			 
		 		} //end reload
	  };	

	 
	 function _constructPagination(sel){
		
		var $this = $(this);
		var str = '<div id="pagNav">'; 
		str += '<span class="ui-icon ui-icon-seek-first"></span><span class="ui-icon ui-icon-seek-prev"></span>';
		str += '<span><input class="pageIndex ui-widget-content ui-corner-all"  type="text" size="2"/> /</span> <span class="totalResultPages"></span>';
		str += '<span class="ui-icon ui-icon-seek-next"></span><span class="ui-icon ui-icon-seek-end"></span>';
		str += '<select id="selectResultsPP"><option value="10">10</option><option value="20">20</option><option value="30">30</option><option value="40">40</option><option value="50">50</option></select>';
		str += '</div>';
		
		$(sel).append(str);
		
		$('#pagNav .ui-icon')
			.bind('mouseenter', function(){
				$(this).addClass('ui-highlight');
			})
			.bind('mouseleave', function(){
				$(this).removeClass('ui-highlight');
			});
		
		$('#pagNav .ui-icon-seek-first')
			.bind('click', function(){
				$this.data('xml2html')._pageIndex = 0; 
				_loopXML.call($this);
			});

		$('#pagNav .ui-icon-seek-prev')
			.bind('click', function(){
				if ($this.data('xml2html')._pageIndex >=1) $this.data('xml2html')._pageIndex--;
				_loopXML.call($this);
			});
		
		$('#pagNav .ui-icon-seek-next')
			.bind('click', function(){
				var totalPages = Math.floor($this.data('xml2html').rowCount / $this.data('xml2html').resultsPerPage); 
				if ($this.data('xml2html')._pageIndex < totalPages) $this.data('xml2html')._pageIndex++;
				_loopXML.call($this);
			});
		
		$('#pagNav .ui-icon-seek-end')
			.bind('click', function(){
				var totalPages = Math.floor($this.data('xml2html').rowCount / $this.data('xml2html').resultsPerPage);
				$this.data('xml2html')._pageIndex = totalPages; 
				_loopXML.call($this);
			});
		
		$('#selectResultsPP').change(function(){
			$this.data('xml2html').resultsPerPage = parseInt($("option:selected", this).val()); 
			_calcResultPages.call($this);
			$this.data('xml2html')._pageIndex = 0; 
			_loopXML.call($this);
			
		});
		
		$('#pagNav .pageIndex').change(function(){
			var totalPages = Math.floor($this.data('xml2html').rowCount / $this.data('xml2html').resultsPerPage);
			var index = parseInt($(this).val())-1;
			if (index > totalPages || isNaN(index)) return false; 
			
			$this.data('xml2html')._pageIndex = index; //(index == totalPages)? index:(index-1);
			_loopXML.call($this);
			
		});

		 
	 }
	 
	 function _calcResultPages(){
			var totalPages = Math.floor($(this).data('xml2html').rowCount / $(this).data('xml2html').resultsPerPage);
			if ($(this).data('xml2html').paginationNav){
				$('#pagNav .totalResultPages').html(totalPages+1);
			}
	 }

	 
	 var _tagNames = [];
	 
	 function _loopXML(){
		 
		 $(this).empty();
		 $(this).append($(this).data('xml2html').listHeader)
		 $('.pageIndex').val($(this).data('xml2html')._pageIndex+1);
		 
		 var rowName = $(this).data('xml2html').rowName; 
		 var xml = $(this).data('xml2html').xml; 
		 var xmlSize = $(this).data('xml2html').rowCount;
		 var resultsPP = ($(this).data('xml2html').paginationNav)? $(this).data('xml2html').resultsPerPage:xmlSize;
		 var startIndex = $(this).data('xml2html')._pageIndex * resultsPP; 
		 var nextSet = startIndex + resultsPP;
		 

		
		 for (var i=startIndex; (i<xmlSize && i<nextSet); i++){
			//get the current row in the xml result set
			var row = $(xml).find(rowName)[i];
			//reset the template string
			var templateStr = $(this).data('xml2html').tpl; 	
			

			//for each tag	of the row								
			for (var j=0; j<_tagNames.length; j++){
				//get the actual value from the xml for the given tag
				var xmlValue = $(row).find(_tagNames[j]).text();
				//construct the string we are searching for
				var pattern = new RegExp('\{'+_tagNames[j]+'\}','gi');		
				templateStr = templateStr.replace(pattern, xmlValue);
				
			}			
			$(this).append(templateStr);
			$(this).data('xml2html').rowComplete.call(this,i,$(this).children(":last"));
		 } //end for
		 
		$(this).data('xml2html').complete.call(this,startIndex+resultsPP);
			
	 } //end loop
	 
	 
	
  $.fn.xml2html = function(method) {
  
	
	// Method calling logic
    if ( methods[method] ) {
      return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
    } else if ( typeof method === 'object' || ! method ) {
      return methods.init.apply( this, arguments );
    } else {
      $.error( 'Method ' +  method + ' does not exist on jQuery.xml2html' );
    }  
	
	
  };
  
})( jQuery );
