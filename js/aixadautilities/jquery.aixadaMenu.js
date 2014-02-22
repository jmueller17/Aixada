
$(function(){
	
	
	
	
	$('#role_select').change(function (){
   		var new_role = $("#role_select option:selected").val(); 
		var rq_uri = window.location;
   		$.ajax({
   			type: "POST",
            url: "php/ctrl/Cookie.php?change_role_to=" + new_role + "&originating_uri=" + rq_uri,
            dataType: "xml",
            success:  function(xml){
		document.cookie = 'USERAUTH=' + escape($(xml).find('cookie').text());
		window.location.href = $(xml).find('navigation').text(); 
   			}   		
   		});
   	}); 
	

	var role =  $("#role_select option:selected").val();
	
	//function to retrieve menu access rights
	if (typeof(role) == "string" ){
		$.ajax({
			type: "POST",
	            url: "php/ctrl/SmallQ.php?oper=configMenu&user_role="+role,
	            dataType: "xml", 
	            success:  function(xml){
				$(xml).find('navigation').children().each(function(){
					var tag = $(this)[0].tagName; 
					var val = $(this).text();
					$('#'+tag).button(val);
				});
			}   		
		});
	}
	
	$('#lang_select').change(function (){
   		var new_lang = $("#lang_select option:selected").val(); 
		var rq_uri = window.location;
   		$.ajax({
   			type: "POST",
            url: "php/ctrl/Cookie.php?change_lang_to=" + new_lang + "&originating_uri=" + rq_uri,
            dataType: "xml",
            success:  function(xml){
            window.location.href = $(xml).find('navigation').text();
							
   			}   		
   		});
   	});
	
	$('#logoutRef').click(function(e){
		
		 $.ajax({
			type: 'POST',
			url: 'php/ctrl/Login.php?oper=logout',
			success : function(msg){
				top.location.href = 'login.php';
			},
			error : function(XMLHttpRequest, textStatus, errorThrown){
				 alert(XMLHttpRequest.responseText);
			}, 
			compplete: function(){
				top.location.href = 'login.php';
			}
		 });
		
	});

	
	
});