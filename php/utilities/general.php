<?php

require_once(__ROOT__ . 'php'.DS.'inc'.DS.'database.php');
require_once(__ROOT__ . 'local_config'.DS.'config.php');


/**
 * 
 * Returns the user_id of the logged user; wraps a check around this, in order to make sure
 * the value is set. 
 */
function get_session_user_id() {
	
	if (isset($_SESSION['userdata']['user_id']) && $_SESSION['userdata']['user_id'] > 0 ) {
		return $_SESSION['userdata']['user_id'];
	} else {
		throw new Exception("$_Session data user_id is not set!! ");
	}
}


/**
 * 
 * returns the uf of the logged user. 
 */
function get_session_uf_id() {
	
	if (isset($_SESSION['userdata']['uf_id']) && $_SESSION['userdata']['uf_id'] > 0 ) {
		return $_SESSION['userdata']['uf_id'];
	} else {
		throw new Exception("$_Session data uf_id is not set!! ");
	}
}


/**
 * 
 * Returns the member_id of the logged user. 
 */
function get_session_member_id() {
	
	if (isset($_SESSION['userdata']['member_id']) && $_SESSION['userdata']['member_id'] > 0 ) {
		return $_SESSION['userdata']['member_id'];
	} else {
		throw new Exception("$_Session data member_id is not set!! ");
	}	
}

/**
 * 
 * Returns the login of the logged user. 
 */
function get_session_login() {
	
	if (isset($_SESSION['userdata']['login']) && $_SESSION['userdata']['login'] != "") {
		return $_SESSION['userdata']['login'];
	} else {
		throw new Exception("$_Session data login is not set!! ");
	}	
}


/**
 * 
 * returns the language for the logged user
 */
function get_session_language() {
    if (isset($_SESSION['userdata']['language']) and $_SESSION['userdata']['language'] != '') {
	return $_SESSION['userdata']['language'];
    } else {
	return	configuration_vars::get_instance()->default_language;
    }
}


/**
 * 
 * returns the theme for the logged user
 */
function get_session_theme() {
    if (isset($_SESSION['userdata']['theme']) and $_SESSION['userdata']['theme'] != '') {
		return $_SESSION['userdata']['theme'];
    } else {
		return	configuration_vars::get_instance()->default_theme;
    }	 
}


/**
 * 
 * retrieves active role of the logged user
 */
function get_current_role()
{
	 if (isset($_SESSION['userdata']['current_role']) and $_SESSION['userdata']['current_role'] != '') {
		return $_SESSION['userdata']['current_role'];
    } else {
		return false; 
    }
}


/**
 * 
 * Provides some basic logic to retrieve values from URL parameters. 
 * @param str $param_name the name of the parameter passed along 
 * @param $default a default value. if the parameter is not set, the default value will be used
 * @param str $transform basic string transforms applied to the value of the parameter
 * @throws Exception
 */
function get_param($param_name, $default=null, $transform = '') {
	$value; 

	if (isset($_REQUEST[$param_name])) {
		$value = $_REQUEST[$param_name];
		if (($value == '' || $value == 'undefined') && isset($default)) {
			$value = $default;
		} else if (($value == '' || $value == 'undefined') && !isset($default)) {
			throw new Exception("get_param: Parameter: {$param_name} has no value and no default value");
		}	
			
	} else if (isset($default) and $default !== null) {
		$value= $default;
	} else {
		throw new Exception("get_param: Missing or wrong parameter name: {$param_name} in URL");
	}
	
	//utility hack to retrieve uf_id or user_id from session. e.g. &uf_id=-1
	if ($param_name == "uf_id" && $value==-1) {
		$value = get_session_uf_id();	
	} else if ($param_name == "user_id" && $value==-1) {
		$value = get_session_user_id();
	} else if ($param_name == "member_id" && $value==-1) {
		$value = get_session_member_id();
	}
	
	
	switch ($transform) {
		case 'lowercase':
			$value = strtolower($value);
			break;
			
		case '':
			$value = $value; 
			break;
		
		case 'array2String':
			$str = "";
			foreach ($value as $v) {
				$str .= $v.",";
			}
			$value = rtrim($str,",");
			break;
			
		default: 
			throw new Exception("get_param: transform '{$transform}' on URL parameter not supported. ");
			break;
	}
	return $value;
}

/**
 * 
 * Sends a email message as html. Use internally php mail. The `from` email
 * address is set acording the key `$admin_email` defined in `config.php`.
 * @param str $to, 
 * @param str $subject
 * @param str $bodyHTML only the body of the html message.
 * @param array $options valid keys are: 'reply_to', 'cc', 'bcc'
 * @return boolean as response of php mail.
 */
function send_mail($to, $subject, $bodyHTML, $options=null) {
    if (!isset($options)) {
        $options = array();
    }
    $cfg = configuration_vars::get_instance();
    $from = $cfg->admin_email;

    // get URL of aixada root
    $pos_root = strrpos($_SERVER['SCRIPT_NAME'], '/php/ctrl/');
    if (!$pos_root) {
        $pos_root = strrpos($_SERVER['SCRIPT_NAME'], '/');
    }
    $ssl_on = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    $url_root = (isset($_SERVER['HTTP_HOST']) ? 
                    $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME']).
                substr($_SERVER['SCRIPT_NAME'],0,$pos_root);

    // get HTML message
    $subject = $cfg->coop_name.': '.$subject;
    $messageHTML = 
        '<html><head><title>'.$subject."</title></head>\r\n".
        '<body style="font-family: Lucida Grande, Lucida Sans, Arial, sans-serif;">'.
        "\r\n".$bodyHTML."\r\n".
        '<hr><div style="color:#888; text-align: center;">'.
                $cfg->coop_name.': <a href="'.
                    ($ssl_on ? 'https://' : 'http://').
                    $url_root.
                    '/index.php" style="color:#888;">'.$url_root.'</a>'.
            "</div>\r\n".
        "</body></html>";
    $headers = 
        'From: '.$from."\r\n".
        'Reply-To: '.
            (isset($options['reply_to']) ? $options['reply_to'] : $from)."\r\n".
        (isset($options['cc']) ? 'Cc :'.$options['cc']."\r\n" : '').
        (isset($options['bcc']) ? 'Bcc :'.$options['bcc']."\r\n" : '').
        'Return-Path: '.$from."\r\n".
        "X-Mailer: PHP\r\n".
        "MIME-Version: 1.0\r\n".
        "Content-Type: text/html; charset=UTF-8\r\n";
    mb_language("uni");
    mb_internal_encoding("UTF-8");
    $subject64 = mb_encode_mimeheader($subject);
    $response = mail($to, $subject64, $messageHTML, $headers);
    // start debug
   /*
        error_log('send_mail(): '.$to."\r\n".$headers);
        error_log($messageHTML);
        return true;
     */
    // end debug
    return $response;
}

/**
 * Execute a stored query
 * @param array $args the arguments to be passed to the stored query; possibly empty
 * @return the result set
 */
function do_stored_query()
{
  $args = func_get_args();
  if (is_array($args[0])) {
    $args = $args[0];
  }
  for ($i=1; $i<count($args); ++$i) {
    if (is_array($args[$i])) {
      $args[$i] = $args[$i][0];
    }
  }

  $sql_func = array_shift($args);

  $strSQL = 'CALL ' . $sql_func . '(';
  foreach ($args as $arg) {
      if (strpos($arg, "'") !== false) {
          if (strpos($arg, '"') !== false)
              throw new DataException('Cannot use both symbols \' and " in text');
          $strSQL .= '"' . $arg . '",';
      } else 
          $strSQL .= "'" . $arg . "',";
  }
  if (count($args))
    $strSQL = rtrim($strSQL, ',');
  $strSQL .= ')';

  return DBWrap::get_instance()->do_stored_query($strSQL);
}

/**
 * Execute a SQL query and returs a list as a string of values on firt column.
 * @param string $strSQL A SQL query
 * @param string $separator
 * @param string $text_delimiter
 * @return string The list as a string or '' if no rows or are null value.
 */
function get_list_query($strSQL, $separator=',', $text_delimiter='') {
    return get_list_rs(
        DBWrap::get_instance()->Execute($strSQL),
        0,
        $separator,
        $text_delimiter
    );
}

/**
 * Walk a result set to assemble a list  as a string of values on firt column.
 * @param mysqli_query_type $rs
 * @param integer|string $field Field on result set, default is 0
 * @param string $separator
 * @param string $text_delimiter
 * @return string The list as a string or '' if no rows or are null value.
 */
function get_list_rs($rs, $field=0, $separator=',', $text_delimiter='') {
    $list = array();
    while ($row = $rs->fetch_array()) {
        if (isset($row[$field])) {
            array_push($list, $text_delimiter.$row[$field].$text_delimiter);
        }
    }
    $db = DBWrap::get_instance();
    $db->free_next_results();
    return implode($separator, $list);
}

class output_formatter {
  public function rowset_to_jqgrid_XML($rs, $total_entries=0, $page=0, $limit=0, $total_pages=0)
  {
    $strXML = '';
    if ($rs) {
      $strXML .= '<rowset>';
      if ($page) 
	$strXML .= '<page>' . $page . '</page>'; 
      if ($total_pages)
	$strXML .= '<total>' . $total_pages . '</total>';
      $strXML .= '<records>' . $total_entries . '</records>';
      $strXML .= "<rows>";
      while ($row = $rs->fetch_assoc()) 
	$strXML .= $this->row_to_XML($row);
      $rs->free();
      $strXML .= "</rows>";
      $strXML .= "</rowset>";
    }
    return $strXML;
  }

  public function row_to_XML($row)
  {

      global $Text;
      $strXML = '<row id="' . $row['id'] . '">';
      $rowXML = '';
      foreach ($row as $field => $value) {
          if (isset($Text[$value]))
              $value = $Text[$value];
          $rowXML 
              .= '<' . $field . ' f="' . $field 
              . '"><![CDATA[' . clean_zeros($value) . "]]></$field>";
      }

      $strXML .= $rowXML . '</row>';
      return $strXML;
  }
}

function stored_query_XML() //$queryname, $group_tag, $row_tag, $param)
{
  $params = func_get_args();
  $strSQL = array_shift($params);
  $group_tag = array_shift($params);
  $row_tag = array_shift($params);
  array_unshift($params, $strSQL);

  $strXML = "<$group_tag>";
//   $rs = ((count($params)>0) ? 
// 	 do_stored_query($strSQL, $params)
// 	 : do_stored_query($strSQL));
  $rs = do_stored_query($params);
  global $Text;
  while ($row = $rs->fetch_array()) {
      $value = ( ($row_tag == 'description' and isset($Text[$row[1]])) ? 
                 $Text[$row[1]] : $row[1] );
      $strXML 
          .= '<row><id f="id">' . $row[0] 
          . '</id><' . $row_tag 
          . ' f="' . $row_tag 
          . '"><![CDATA[' . clean_zeros($value) . ']]></' . $row_tag 
          . '></row>'; 
  }
  $strXML .= "</$group_tag>";
  return $strXML;
}

// make variable argument list
function stored_query_XML_fields()
{
    $strXML = '<rowset>';
    global $Text;
    $rs = do_stored_query(func_get_args());
    while ($row = $rs->fetch_assoc()) {
        $strXML .= '<row';
        if (isset($row['id'])) 
            $strXML .= ' id ="' . $row['id'] . '"';
        $strXML .= '>';
        foreach ($row as $field => $value) {
            if ($field == 'description' and isset($Text[$value])) 
                $value = $Text[$value];
            $strXML 
                .= '<' . $field . ' f="' . $field 
                . '"><![CDATA[' . clean_zeros($value) . "]]></$field>";
        }
        $strXML .= '</row>';
    }
    $strXML .= '</rowset>';
    return $strXML;
}

function query_XML() //$strSQL, $group_tag, $row_tag, $param1=0, $param2=0)
{
  $params = func_get_args();
  $strSQL = array_shift($params);
  $group_tag = array_shift($params);
  $row_tag = array_shift($params);
  array_unshift($params, $strSQL);

  $strXML = "<$group_tag>";
  $rs = DBWrap::get_instance()->Execute($params);
  while ($row = $rs->fetch_array()) {
      $value = ( ($row_tag == 'description' and isset($Text[$row[1]])) ? 
                 $Text[$row[1]] : $row[1] );
      $strXML 
          .= '<row><id f="id">' . $row[0] 
          . '</id><' . $row_tag 
          . ' f="' . $row_tag 
          . '"><![CDATA[' . $value . ']]></' . $row_tag 
          . '></row>'; 
  }
  $strXML .= "</$group_tag>";
  return $strXML;
}

function query_XML_compact()
{
  $params = func_get_args();
  $strSQL = array_shift($params);
  $group_tag = array_shift($params);
  $row_tag = array_shift($params);
  array_unshift($params, $strSQL);

  $strXML = "<$group_tag>";
  $rs = DBWrap::get_instance()->Execute($params);
  while ($row = $rs->fetch_array()) {
    $strXML 
      .= '<' . $row_tag 
      . ' f="' . $row_tag 
      . '"><![CDATA[' . $row[0] . ']]></' . $row_tag 
      . '>'; 
  }
  $strXML .= "</$group_tag>";
  return $strXML;
}

function query_XML_noparam($queryname)
{
  $strXML = "<$queryname>";
  $rs = do_stored_query($queryname);
  while ($row = $rs->fetch_assoc()) {
  	 $strXML .= "<row>";
      foreach ($row as $field => $value) {
          if ($field == 'description' and isset($Text[$value])) 
              $value = $Text[$value];
          $strXML .= "<{$field}>{$value}</{$field}>";
      }
       $strXML .= "</row>";
  }
  $strXML .= "</$queryname>";
  return $strXML;
}

function printXML($str) {
  $newstr = '<?xml version="1.0" encoding="utf-8"?>';  
  $newstr .= $str.'   ';
  header('Content-Type: text/xml');
  header('Last-Modified: '.date(DATE_RFC822));
  header('Pragma: no-cache');
  header('Cache-Control: no-cache, must-revalidate');
  header('Expires: '. date(DATE_RFC822, time() - 3600));
  header('Content-Length: ' . strlen($newstr));
  echo $newstr;
}





function HTMLwrite($strHTML, $filename)
{
  if(is_writeable($filename)) {
    if (!$handle = fopen($filename, 'w')) {
      echo "Cannot open file ($filename)";
      exit;
    }
    if (fwrite($handle, $strHTML) === FALSE) {
      echo "Cannot write to file ($filename)";
      exit;
    }
    fclose($handle);
  } else {
      echo "The file $filename is not writable";
  }
}

function get_config_menu($user_role)
{
    $XML = "<navigation>\n";
    $mconf = configuration_vars::get_instance()->menu_config;
    if (!isset($mconf[$user_role])) {
        throw new Exception("Role '" . $user_role . "' not defined in local_config/config.php");
    }
    foreach ($mconf[$user_role] as $navItem => $status) {
        $XML .= '<' . $navItem . '>' . $status . '</' . $navItem . ">\n";
    } 
    return $XML . '</navigation>';
}


function get_import_rights($db_table_name)
{
	//get the import rights for the db table and fields
	$import_rights = configuration_vars::get_instance()->allow_import_for; 
	
	if (!isset($import_rights[$db_table_name])) {
        throw new Exception("Import error: no imports allowed for '" . $db_table_name . ".' Check local_config/config.php");
    }
    $xml = '<rows>';
    
	foreach ($import_rights[$db_table_name] as $field => $value) {
	    		if ($value == 'allow') {
		    		$xml .= '<row><db_field>'.$field.'</db_field></row>';
	    		} 
    		}
	return $xml . "</rows>";
}

/**
 * Returns a XML body with a list of template names defined in 'config.php'
 * for a database table name.
 * @param string $db_table_name The database table name.
 * @return string The XML list of teplate names.
 */
function get_import_templates_list($db_table_name) {
    $templates = get_import_templates($db_table_name);
    $xml = '<rows>';
    foreach ($templates as $field => $value) {
        $xml .= '<row><db_field>'.$field.'</db_field></row>';
    }
    return $xml.'</rows>';
}

/**
 * Returns a array of templates defined in 'config.php' for a database
 * table name.
 * @param string $db_table_name The database table name.
 * @return array The array of teplates, returns an empty array if no defined
 *      templates for this table.
 */
function get_import_templates($db_table_name) {
    $cfg = configuration_vars::get_instance();
    if (isset($cfg->import_templates)) {
        $import_templates = $cfg->import_templates;
        if (isset($import_templates[$db_table_name])) {
            return $import_templates[$db_table_name];
        }
    }
    // If not exists any template for this table returns a empty array.
    return array();
}

function get_field_options_live($table, $field1, $field2, $field3='')
{
    global $Text;
    $strXML = '<select>';
    if ($field3 != ''){
    	$strSQL = 'select :1, :2, :3 from :4';
    } else {
    	$strSQL = 'select :1, :2 from :3';
    }
    
    if (in_array($table, array('aixada_unit_measure'))) {
	$strSQL .= ' order by name';
    } else if (in_array($table, array('aixada_orderable_type'))) {
	$strSQL .= ' order by description';
    }

   
	if ($field3 != ''){
         $rs = DBWrap::get_instance()->Execute($strSQL, $field1, $field2, $field3, $table);    	       	
        } else {
        $rs = DBWrap::get_instance()->Execute($strSQL, $field1, $field2, $table);
    }
    
    if ($table == 'aixada_uf') {
        $strXML .= "<option value='-1'>".$Text['sel_uf']."</option>";
    }
    while ($row = $rs->fetch_array()) {
        $ot = (isset($Text[$row[1]]) ? $Text[$row[1]] : $row[1]);
        if ($table == 'aixada_uf'){
            $ot = //$Text['uf_short'] . ' ' . 
                $row[0] . ' ' . $ot;
        }
        
        if ($field3 != ''){
         $strXML .= "<option value='{$row[0]}' addInfo='{$row[2]}'";        	       	
        } else {
        	$strXML .= "<option value='{$row[0]}'";
        }
                

	if ($row[0] == 1) {
	    $strXML .= ' selected';
	}
	$strXML .= ">{$ot}</option>";
    }
    return $strXML . '</select>';
}


function get_existing_themes_XML()
{
    $exclude_list = array(".", "..", "example.txt");
    $folders = array_diff( scandir(__ROOT__ . 'css/ui-themes'), $exclude_list);
     
    $XML = '<themes>';
    foreach ($folders as $theme) {
        $XML .= "<theme><name>{$theme}</name></theme>";
    }
    return $XML . '</themes>';
}


function existing_languages()
{
    // We require that a line of the form 
    // $Text['es_es'] = 'Español'
    // exists in each language file
    $languages = array();
    foreach (glob(__ROOT__ . "local_config/lang/*.php") as $lang_file) {
        $a = strpos($lang_file, 'lang/');
        $lang = substr($lang_file, $a+5, strpos($lang_file, '.', $a)-$a-5);
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
        $languages[$lang] = $lang_desc;
    }
    return $languages;
}

function existing_languages_XML()
{
	$static = false; 
    // We require that a line of the form 
    // $Text['es_es'] = 'Español'
    // exists in each language file
    $XML = '<languages>';
    if ($static){
    	$XML .= "<language><id>ca-va</id><description>Català (ca-va)</description></language><language><id>en</id><description>English (en)</description></language><language><id>es</id><description>Castellano (es)</description></language>";
    } else {
	    foreach (existing_languages() as $lang => $lang_desc) {
	        $XML .= "<language><id>{$lang}</id><description>{$lang_desc} ({$lang})</description></language>";
	    }
    }
    return $XML . '</languages>';
}

function get_roles()
{
    $XML = '<roles>';
    foreach (array_keys(configuration_vars::get_instance()->forbidden_pages) as $role) {
        $XML .= "<role><description>{$role}</description></role>";
    }
    return $XML . '</roles>';
}

function get_commissions()
{

    $XML = '<rows>';
    foreach (array_keys(configuration_vars::get_instance()->forbidden_pages) as $role) {
        if (!in_array($role, array('Consumer', 'Checkout', 'Producer'))) {

            $XML .= "<row><description>{$role}</description></row>";
        }
    }
    return $XML . '</rows>';
}


function clean_zeros($value)
{
  return ((strpos($value, '.') !== false) ?
	  rtrim(rtrim($value, '0'), '.') 
	  : $value);
}

?>
