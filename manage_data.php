<?php include "php/inc/header.inc.php" ?>
<?php 

    $table_name = get_param('table');
    $data_manager = null;
    if (check_manager_exist($table_name)) {
        include_once(__ROOT__."php/ctrl/ManageData/{$table_name}.php");
        $data_manager = new data_manager();
    }
    function check_manager_exist($table_name) {
        if ($table_name && strpos($table_name, '/') === false && strpos($table_name, "\\") === false) {
            return file_exists(__ROOT__."php/ctrl/ManageData/{$table_name}.php");
        }
        return false;
    }
    
	/**
	 * This function looks at the current_role written to the
	 * cookie and the $_SESSION['userdata'] to determine if the table
	 * currently requested may be edited by the user in the current role.
	 */
    function may_edit_table($data_table) {
        $table_aux = strstr($data_table, '_');
        $prefix = strstr($data_table, '_', true);
        if ($prefix !== 'aixada') {
            $table_aux = '_'.$prefix.$table_aux;
        }
        $property = 'may_edit'.$table_aux;
        if (in_array($property, 
                     configuration_vars::get_instance()->rights_of[$_SESSION['userdata']['current_role']])) {
            return true;
        } else {
            return false;
        }
    }
    $is_edit = 'false';
    if ($data_manager){
        if (may_edit_table($table_name)) {
            $page_title = i18n('dataman_edit',
                array('data'=>$data_manager->title()) );
            $is_edit = 'true';
        } else {
            $page_title = i18n('dataman_consult',
                array('data'=>$data_manager->title()) );
        }
    } else {
        $page_title = '??';
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$language;?>" lang="<?=$language;?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php 
        echo $Text['global_title'] . " - " . $page_title; ?>
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
	<?php echo aixada_js_src(); ?>
    
    <style>
        .ui-state-highlight, 
        .ui-widget-content .ui-state-highlight,
        .ui-widget-header .ui-state-highlight {
            background-image: none;
            background-color: #fbf9df;
        }
        #gbox_desc {
            margin-left:auto; margin-right:auto
        }
    </style>
    <!-- load js of table on docuement redy -->
    <script><?php 
        echo ($data_manager ? $data_manager->callCreateEditorJs() : '');
        ?></script>
    <script type="text/javascript">
    function createEditor(allowed_edit_modes, col_models, col_sort) {
        createEditor_aux(
            "<?php echo $data_manager ? $table_name : '' ?>",
            "<?php echo $data_manager ? to_js_str($page_title) : '???' ?>",
            (<?php echo $is_edit ?> ? 
                allowed_edit_modes :
                {edit:false,add:false,del:false}
            ),
            col_models,
            col_sort
        );
    }
	</script>    
	<script type="text/javascript">
    function createEditor_aux(current_table, page_title, edit_modes,
                                                         col_models, col_sort) {
        $.ajaxSetup({ cache: false });
        if (current_table === '') { return; }
        var selected_row;
        var lastsel = 0; 
	
		if (current_table == null || current_table == '') {
			alert("variable table not set in query");
            return;
		}
        var filter_text = '';
        var filter_cond = $.getUrlVar('filter');
        if (filter_cond.length>0) {
            filter_text = "&filter=" + filter_cond;
        }
        var col_names = [],
            col_sortIndex = 0,
            col_sortName =  (col_sort ? col_sort[0] : ''),
            col_sortOrder = (col_sort ? col_sort[1] : 'asc');
        if (col_models[col_sortIndex].name === 'id') {
            col_sortIndex++;
        }
        for (var i=0, len=col_models.length; i < len; i++) {
            var col_model = col_models[i],
                col_name = col_model.name;
            // name as default
            col_model = $.extend({ // Apply values as default
                    xmlmap:col_name,
                    index: col_name,
                    label: col_name,
                    hidden: (col_model.width ? false : true),
                    editable: true
                },
                col_model
            );
            if (col_sortIndex === i && col_model.hidden && i < len) {
                col_sortIndex++;
            }
            // editrules
            col_model.editrules = $.extend({ // Apply values as default
                    edithidden:true, searchhidden:true
                },
                col_model.editrules ? col_model.editrules : {}
            );
            // add label to col_names
            col_models[i] = col_model;
            col_names.push(col_model.label);
        }
        if (col_sortName === '') {
            col_sortName = (
                col_models[col_sortIndex].index ?
                col_models[col_sortIndex].index :
                col_models[col_sortIndex].name
            );
        }

        var last_row_sel = null;
        $("#desc").jqGrid({
            url: "php/ctrl/ManageData.php?table="+current_table+
                "&oper=listAll"+filter_text,
            height: 200,
            datatype: 'xml',
            colNames: col_names,
            colModel: col_models,
            onSelectRow: function(id) {
                if (edit_modes.edit && id && id !== last_row_sel) {
                    if (last_row_sel !== null) {
                        jQuery('#desc').saveRow(last_row_sel);
                        // TODO: Do automatic refresh without breaking anything,
                        //       one step could be (but missing something): 
                        //          jQuery("#desc").trigger("reloadGrid");
                    }
                    jQuery('#desc').editRow(id, true);
                    last_row_sel = id;
                }
            },
            // loadonce: false, // Used with "reloadGrid", see previous TODO
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
            rowList: [10, 20, 30],
            // autowidth: true, 
            height:'100%',
            pager: '#desc_pager', 
            sortname: col_sortName,
            sortorder: col_sortOrder,
            viewrecords: true,
            multiselect: false, 
            editurl:"php/ctrl/ManageData.php?table="+current_table,
            caption: page_title
        }); // close jqgrid
        
        $("#desc").navGrid('#desc_pager',  
            $.extend({}, {search:false}, edit_modes), // TODO: Allow search
            { //edit options
                reloadAfterSubmit:true, 
                width: 600,
                // dataheight:400,
                top:50, left:50,
                errorTextFormat: function(data) {
                    return data.statusText; // + ' ['+data.status+']';
                }
            }, { //add options
                reloadAfterSubmit:true,
                width: 600,
                // dataheight:400,
                top:50, left:50,
                errorTextFormat: function(data) {
                    return data.statusText; // + ' ['+data.status+']';
                }
            }, { //del options
                reloadAfterSubmit:true,
                width: 500,
                errorTextFormat: function(data) {
                    return data.statusText; // + ' ['+data.status+']';
                }
            }, { //search options
                width:600, top:50, left:50
            }
        ); 
    }
    </script>
</head> 
<body>
   	<div id="wrap">
   		<div id="headwrap">
		<?php include "php/inc/menu.inc.php" ?>
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
