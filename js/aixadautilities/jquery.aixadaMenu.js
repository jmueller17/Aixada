
$(function(){
	
	
	$("#navHome").button();
	$("#navWizard").button({
		icons: {
        	secondary: "ui-icon-triangle-1-s"
		}
    }).menu({
		content: $('#navWizardItems').html(),	
		showSpeed: 50, 
		flyOut: true
	});
	$("#navShop").button();
	$("#navOrder").button();
    
	$("#navManage").button({
		icons: {
            secondary: "ui-icon-triangle-1-s"
		}
	}).menu({
		content: $('#navManageItems').html(),	
		showSpeed: 50, 
		flyOut: true
	});

	$("#navReport").button({
		icons: {
            secondary: "ui-icon-triangle-1-s"
		}
	}).menu({
		content: $('#navReportItems').html(),	
		showSpeed: 50, 
		flyOut: true
	});

	$("#navIncidents").button({
		icons: {
            secondary: "ui-icon-triangle-1-s"
		}
	}).menu({
		content: $('#navIncidentsItems').html(),	
		showSpeed: 50, 
		flyOut: true
	});
	
	$("#navMyAccount").button({
		icons: {
            secondary: "ui-icon-gear"
		}
	}).menu({
		content: $('#navMyAcountItems').html(),	
		showSpeed: 50, 
		flyOut: true
	});
	
	
	$('#role_select').change(function (){
   		var new_role = $("#role_select option:selected").val(); 
		var rq_uri = window.location;
                    // <?php echo '"' . $_SERVER['HTTP_REFERER'] . '"' ?>;
                //   		var rq_uri = "index.php";
   		$.ajax({
   			type: "POST",
                            url: "ctrlCookie.php?change_role_to=" + new_role + "&originating_uri=" + rq_uri,
                            dataType: "xml",
                            success:  function(xml){
   			//alert( $(xml).find('navigation').text());
                            window.location.href = $(xml).find('navigation').text();
				//reload the window to make the role change take effect
                            // BUT, make it reload new_location !
                            //                            window.location.reload();
		      //     				
   			}   		
   		});
   	}); 
	
	var role =  $("#role_select option:selected").val();
	
	//function to retrieve menu access rights
	$.ajax({
		type: "POST",
                    url: "smallqueries.php?oper=configMenu&user_role="+role,
                    dataType: "xml", 
                    success:  function(xml){
			$(xml).find('navigation').children().each(function(){
				var tag = $(this)[0].tagName; 
				var val = $(this).text();
				$('#'+tag).button(val);
			});
		}   		
	});
	
	$('#lang_select').change(function (){
   		var new_lang = $("#lang_select option:selected").val(); 
		var rq_uri = window.location;
                    // <?php echo '"' . $_SERVER['HTTP_REFERER'] . '"' ?>;
                //   		var rq_uri = "index.php";
   		$.ajax({
   			type: "POST",
                            url: "ctrlCookie.php?change_lang_to=" + new_lang + "&originating_uri=" + rq_uri,
                            dataType: "xml",
                            success:  function(xml){
   			//alert( $(xml).find('navigation').text());
                            window.location.href = $(xml).find('navigation').text();
				//reload the window to make the role change take effect
                            // BUT, make it reload new_location !
                            //                            window.location.reload();
		      //     				
   			}   		
   		});
   	}); 
	
});