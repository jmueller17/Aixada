<?php include "inc/header.inc.php" ?>
<?php 
	/**
	 * This function looks at the current_role written to the
	 * cookie and the $_SESSION['userdata'] to determine if the table
	 * currently requested may be edited by the user in the current role.
	 */
function navGrid_options() {
    if (isset($_REQUEST['table'])) {
        $table = strstr($_REQUEST['table'], '_');
        $property = 'may_edit' . $table;
        if (in_array($property, 
                     configuration_vars::get_instance()->rights_of[$_SESSION['userdata']['current_role']])) {
            echo '{edit:true,add:true,del:true,search:false},';
        } else {
            echo '{edit:false,add:false,del:false,search:false},';
        }
    } else {
        echo '{edit:false,add:false,del:false,search:false},';
    }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php 
      			$what = (isset($_REQUEST['what']) ? ' - ' . $_REQUEST['what'] : '');
      			echo $Text['global_title'] . " - " . $Text['head_ti_manage'] . $what; ?>
    </title>

	<link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
  	<link rel="stylesheet" type="text/css"   media="screen" href="js/fgmenu/fg.menu.css"   />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/<?=$default_theme;?>/jqueryui.css"/>
    <link rel="stylesheet" type="text/css"   media="screen" href="js/jqGrid-4.3.1/css/ui.jqgrid.css"/>
    
    
        
	<!--  this cannot be minified because the order of the i18n file for jqgrid is important  -->
    <script type="text/javascript" src="js/jquery/jquery.js"></script>
 	<script type="text/javascript" src="js/jqGrid-4.3.1/js/i18n/grid.locale-<?=$language;?>.js"></script>
    <script type="text/javascript" src="js/jqGrid-4.3.1/js/jquery.jqGrid.min.js"></script>
    
	<script type="text/javascript" src="js/jqueryui/jqueryui.js"></script>
	<script type="text/javascript" src="js/fgmenu/fg.menu.js"></script>
	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaMenu.js"></script>     	 
	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaXML2HTML.js" ></script>
   	<script type="text/javascript" src="js/aixadautilities/jquery.aixadaUtilities.js" ></script>
	   	   	
 
   
   <script type="text/javascript">
     var selected_row; 
     var lastsel = 0; 
	
	/**
	 *	Custom extension to retrieve the detail table for a given table
	 */
	$.extend({
		getDetails: function(table){
			var tbl = (table == null)? $.getUrlVar('table'):table;
		
			var details = [];
			
			if (tbl == 'aixada_uf'){
				details['table'] = 'aixada_member';	
				details['key'] = 'uf_id';
				
			} else if (tbl == 'aixada_provider'){
				
				details['table'] = 'aixada_product';	
				details['key'] = 'provider_id';
				
			} else if (tbl == 'aixada_order_cart'){
				
				details['table'] = 'aixada_order_item';	
				details['key'] = 'order_cart_id';
			} 
			
			return details; 
		}		 
			 
	});

	
	/**	
	 *	main table 
	 */
	 
	$(document).ready(function (){ 
		var current_table = $.getUrlVar('table'); 	

		if (current_table == null || current_table == '') {
			alert("variable table not set in query");
		}
		
		$.ajax({
			type: 'POST',
	    	url: 'php/ctrl/TableManager.php?table='+current_table+'&oper=getColumnsAsJSON',
	    	dataType: 'json',
	      	error: function(xhr, ajaxOptions, thrownError) {
              alert(xhr.statusText +" "+ thrownError);   
            },
	    	success: function(result){
	      		colN = result.col_names;
	      		colM = result.col_model;
				active_fields = result.active_fields;
	      		opt = result.field_options;
				var filter_text = new String('');
				var filter_cond = $.getUrlVar('filter');
				if (filter_cond.length>0) {
			  		filter_text = "&filter=" + filter_cond;
				}
			$("#desc").jqGrid({
			  url: "php/ctrl/TableManager.php?table="+current_table+"&oper=listAll" + filter_text,
			      	height: 200,
			      	datatype: 'xml',
			      	colNames: eval(colN),
			      	colModel: eval('('+colM+')'),
			      	xmlReader: {
			        	root: 'rowset',
						row:  'row',
						page: 'page',
						total: 'total',
						records: 'records',
						repeatitems: false,
						id: 'id'
	        	   	},
	        		rowNum: 10,
	        		rowList: [10,20,30],
			      	autowidth: true, 
			      	height:'100%',
					//width:'100%',
			      	pager: '#desc_pager', 
			      	sortname: 'id',
			      	viewrecords: true,
			      	sortorder: 'asc', 
					onSelectRow: function(ids) { 
							if (current_table == 'aixada_uf' || current_table == 'aixada_provider' || current_table == 'aixada_order_cart') {
							var details = $.getDetails();
							//alert(details['table'] + " " + details['key'] + " " + ids);
							$("#detail").jqGrid('clearGridData');
							$("#detail").jqGrid('setGridParam',{url:"php/ctrl/TableManager.php?table="+details['table']+"&oper=listAll&filter="+details['key']+"="+ids,page:1}); 	
							$("#detail").jqGrid('setCaption', 'Detail for ' + current_table + ': ' +ids)
														.trigger('reloadGrid'); 														
							$("#detail").jqGrid('setGridParam',{editurl:"php/ctrl/TableManager.php?table="+details['table']+"&oper=get_by_key"+"&key="+details['key']+"&val="+ids,page:1}); 
							}
					}, //end on select row
					
	        		multiselect: false, 
					editurl:"php/ctrl/TableManager.php?table=" +  current_table,
					caption:  current_table,
				}) // close jqgrid
				
	   			$("#desc").navGrid('#desc_pager',  
						   //	      {},
						   //      {edit:false,add:true,del:true,search:false}, //options
						   <?php navGrid_options(); ?>
                                                   {reloadAfterSubmit:true, width:400}, 		//edit options
			      {reloadAfterSubmit:true}, 		//add options
			      {reloadAfterSubmit:true}, 		//del options
			      {} //search options
			      
			      ); 
				
	   		}//close success
	   });//close  ajax  
	
	
	/**	
	 *	detail Grid 
	 */
	
	if (current_table == 'aixada_uf' || current_table == 'aixada_provider' || current_table == 'aixada_order_cart'){
		
		$.ajax({
			type: 'POST',
		      url: "php/ctrl/TableManager.php?table="+$.getDetails()['table']+"&oper=getColumnsAsJSON",
	    	dataType: 'json',
	      	error: function(xhr, ajaxOptions, thrownError) {
              alert(xhr.statusText +" "+ thrownError);   
            },
	    	success: function(result){
	      		colN = result.col_names;
	      		colM = result.col_model;
	      		opt = result.field_options;
				$("#detail").jqGrid({
				  url: "", // this should be empty so that the table doesn't display junk on the first load of the page
			      	height: 200,
                                            width: 1600,
			      	datatype: 'xml',
			      	colNames: eval(colN),
			      	colModel: eval('('+colM+')'),
			      	xmlReader: {
			        	root: 'rowset',
						row:  'row',
						page: 'page',
						total: 'total',
						records: 'records',
						repeatitems: false,
						id: 'id'
	        	   	},
			      	autowidth: true, 
			      	height:'100%',
			      	pager: '#detail_pager', 
			      	sortname: 'id',
			      	viewrecords: true,
			      	sortorder: 'asc', 
	        		multiselect: false, 
				editurl:"php/ctrl/TableManager.php?table=" + $.getDetails()['table']
					
				}) // close jqgrid
				
	   			$("#detail").navGrid('#detail_pager',  
				
						   <?php navGrid_options(); ?>

                  {reloadAfterSubmit:true, width:500}, 		//edit options
			      {reloadAfterSubmit:true}, 		//add options
			      {reloadAfterSubmit:true}, 		//del options
			      {} //search options
			      
			      ); 
				
	   		}//close success detail
	   });//close  ajax  detail 
		
	}//close if tables 

	
		
    }); //close document ready 
	
								 
		
	 
		</script>
		</head> 
   <body>
   	<div id="wrap">
   		<div id="headwrap">
     		<?php include "inc/menu2.inc.php" ?>
   		</div>

	   <br />
	   <table id="desc"></table>
	   <div id="desc_pager"></div>
	   <br />
	
	   <br />
	   <table id="detail" class="hideInPrint"></table>
	   <div id="detail_pager"></div>
	   <!--input type="button" id="editData" value="Edit Selected" /--> 
	   <br />
	
	</div>
  </body>

</html>
