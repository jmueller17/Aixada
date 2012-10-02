<?php 
function existing_languages_selectbox()
{
    // We require that a line of the form 
    // $Text['es_es'] = 'Español'
    // exists in each language file
    $sbox = '<select id="pref_lang" name="pref_lang">';
    foreach (glob("local_config/lang/*.php") as $lang_file) {
        $a = strpos($lang_file, 'lang/');
        $lang = substr($lang_file, $a+5, strpos($lang_file, '.')-$a-5);
        $handle = @fopen($lang_file, "r");
        $line = fgets($handle);
        while (strpos($line, "Text['{$lang}']") === false and !feof($handle)) {
            $line = fgets($handle);            
        }
        if (feof($handle))
            $lang_desc = '';
        else {
            $tmp = trim(substr($line, strpos($line, '=')));
            $lang_desc = trim($tmp, " =;'\"");
        }
        $sbox .= "<option value=\"{$lang}\">{$lang_desc}</option>";
    }
    return $sbox . '</select>';
}
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
   <title>Install</title>
   <link rel="stylesheet" type="text/css"   media="screen" href="css/aixada_main.css" />
    <link rel="stylesheet" type="text/css"   media="screen" href="css/ui-themes/smoothness/jqueryui.css"/>
    
</head>
<body>
    <script type="text/javascript" src="js/jquery/jquery.js"></script>
    <script type="text/javascript" src="js/jqueryui/jqueryui.js"></script>
<script type="text/javascript">
   function callInstall(action, dataSerial) {
    var result = 2;
    $('#' + action)
    .removeClass('grayed')
    .addClass('processing')
    .attr({style:'display:block'});
    $.ajax({
	type: "POST",
		url: "php/ctrl/Install.php?oper=" + action + dataSerial,
		success: function() {
		$('#' + action)
		    .removeClass('processing')
		    .removeClass('noRed')
		    .addClass('okGreen')
		    .attr({style:'display:block'});
		$('#' + action + '_result')
		    .removeClass('noRed')
		    .removeClass('grayed')
		    .addClass('okGreen')
		    .attr({style:'display:inline'})
		    .text(' ok');
		result = 0;
	    },
		error : function(XMLHttpRequest, textStatus, errorThrown){
		$('#' + action)
		    .removeClass('processing')
		    .addClass('noRed')
		    .attr({style:'display:block'});
		$('#' + action + '_result')
		    .text(XMLHttpRequest.responseText)
		    .addClass('noRed')
		    .attr({style:'display:inline'});
		result = 1;
	    },
		async:false
	});
    return result;
    }
    $(function(){
	    $('#btn_install').button();
	    $('#btn_install').click(function(){
		    var items = ['coop_name', 'db_name', 'db_host', 'db_user', 'db_pwd', 'first_uf', 'user_login', 'user_password', 'retype_password'];
		    var dataSerial = '';		    
		    for (var i=0; i<items.length; i++) {
			dataSerial += '&' + items[i] + '=' + $('#' + items[i]).val();
		    }
		    var actions = ['validate', 'connect', 'lang', 'create_setup', 'create_database', 'create_config_file', 'create_user'];
		    for (var i=0; i<actions.length; i++) {
			$('#' + actions[i])
			    .removeClass('okGreen')
			    .removeClass('noRed')
			    .addClass('grayed')
			    .attr({style:'display:none'});
			$('#' + actions[i] + '_result')
			    .removeClass('okGreen')
			    .removeClass('noRed')
			    .addClass('grayed')
			    .attr({style:'display:none'});
		    }
		    var result = 0;
		    for (var i=0; i<actions.length && result==0; i++) {
			result = callInstall(actions[i], dataSerial);
		    }
		});
	    return false;
	});
</script>

<br/><br/><br/>
  <div id="wrap">
   <div id="wrapForm">
   
      <form id="install">
         <h2>Install your Aixada platform</h2>   
         <br/><br/><br/>
         <p>What you need:
            <ul>
            <li>The name of an existing mySQL database</li>
            <li>An existing mySQL user (name, password) with sufficient rights for your database</li>
            </ul>         

                    
         </p>
         <p><br/><br/></p>
         
               <table>
                   
                  <tr>
                     <td><label for="coop_name">Name of your platform</label> <br/>(appears in the title bar of the site)</td>
                     <td><input type="text" id="coop_name" class="ui-widget-content ui-corner-all" /></td>
                     
                  </tr>
                  
                  <tr>
                     <td><br/></td>
                     <td></td>
                  </tr>
                    

                  <tr>
                     <td><label for="db_name">Database name:</label></td>
                     <td><input type="text" id="db_name" class="ui-widget-content ui-corner-all"/></td>
                  </tr>

                     <td class="textAlignRight"><label for="db_host">Database host:</label><br/>(usually 'localhost')</td>
                     <td><input type="text" id="db_host" class="ui-widget-content ui-corner-all"/></td>
                  </tr>
                  
                  
                  <tr>
                     <td><label for="db_user">Database user:</label></td>
                     <td><input type="text" id="db_user" class="ui-widget-content ui-corner-all" /></td>
                     
                  </tr>
                  <tr>
                     <td><label for="db_pwd">Password</label></td>
                     <td><input type="text" id="db_pwd" class="ui-widget-content ui-corner-all" /></td>
                  </tr>
                  <tr>
                     <td>&nbsp;</td>
                  </tr>
                  <tr>
                     <td><label for="pref_lang">Default language</label></td>
                     <td colspan="2">
     <?php echo existing_languages_selectbox(); ?>
                     </td>
                  </tr>
                  <tr>
                     <td><label for="first_Uf">Name of first UF</label></td>
                     <td><input type="text" id="first_uf" class="ui-widget-content ui-corner-all" /></td>
                  </tr>
                  <tr>
                     <td><label for="user_login">First User's login</label></td>
                     <td><input type="text" id="user_login" class="ui-widget-content ui-corner-all" /></td>
                  </tr>
                  <tr>
                     <td><label for="user_password">First User's password</label></td>
                     <td><input type="password" id="user_password" class="ui-widget-content ui-corner-all" /></td>
                  </tr>
                  <tr>
                     <td><label for="retype_password">Retype password</label></td>
                     <td><input type="password" id="retype_password" class="ui-widget-content ui-corner-all" /></td>
                  </tr>
                  <tr>
                     <td colspan="2" class="textAlignRight"><br/><br/><p id="btn_install" type="submit">Install :-)</p></td>
                  </tr>
               </table>
      
      </form>
   </div>
<br/>
   <div id="wrapFeedback">
      <p id="validate" class="grayed " style="display:none">Validating input ... <b id="validate_result"></b></p>
      <p id="connect" class="grayed " style="display:none">Connect to database ... <b id="connect_result"></b></p>
      <p id="lang" class="grayed" style="display:none">Process language files ... <b id="lang_result"/></p>
      <p id="create_setup" class="grayed" style="display:none">Create setup file for database ... <b id="create_setup_result"/></p>
      <p id="create_db" class="grayed" style="display:none">Create database ... <b id="create_db_result"/></p>
      <p id="create_config" class="grayed" style="display:none">Create configuration file ... <b id="create_config_result"/></p>
      <p id="create_user" class="grayed" style="display:none">Create special user ... <b id="create_user_result"/></p>
      <p id="ok" class="grayed" style="display:none">Success!</p>
   </div>
  <br/>
  <div>
    <p id="installMsg" class="user_tips  minPadding"></p>
  </div>
 </div>
</body>
</html>

