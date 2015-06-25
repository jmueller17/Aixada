<?php


require_once(__ROOT__ . 'php/inc/database.php');
require_once(__ROOT__ . 'php/inc/authentication.inc.php');
require_once(__ROOT__ . 'local_config/config.php');
require_once ('general.php');
require_once(__ROOT__ . 'local_config/lang/'.get_session_language() . '.php');

if (configuration_vars::get_instance()->development){
	require_once(__ROOT__ . 'php/external/FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
	ob_start();
	$firephp = FirePHP::getInstance(true);
}

/**
 * 
 * Updates database fields for given member
 * @param int $member_id
 */
function update_member($member_id){
	
	$params = extract_user_form_values();
	
	echo do_stored_query('update_member', $member_id, 
			$params["custom_member_ref"],
			$params["name"],
			$params["nif"],
			$params["address"],
			$params["city"],
			$params["zip"],
			$params["phone1"],
			$params["phone2"],
			$params["web"],
			$params["notes"],
			$params["active"],
			$params["participant"],
			$params["adult"],
			$params["language"],
			$params["gui_theme"],
			$params["email"]	
	);
	
}


/**
 * 
 * creates a new user and member for the given UF
 * @param unknown_type $uf_id
 */
function create_user_member($uf_id){
	
	
	$params = extract_user_form_values();
	
	$login_exists = validate_field('aixada_user', 'login', $params['login']);
	
	if($login_exists) {
		throw new Exception("The login '" .$params['login']. "' already exists. Please choose another one");
		exit; 
		
	} else {
		echo do_stored_query('new_user_member', 
				$params["login"],
				$params["password"],
				$uf_id,
				$params["custom_member_ref"],
				$params["name"],
				$params["nif"],
				$params["address"],
				$params["city"],
				$params["zip"],
				$params["phone1"],
				$params["phone2"],
				$params["web"],
				$params["notes"],
				$params["active"],
				$params["participant"],
				$params["adult"],
				$params["language"],
				$params["gui_theme"],
				$params["email"]	
			);
	}
}


function extract_user_form_values(){
	
	//login and pwd is ignored by update member calls. the '' assumes empty password 
	$fields["login"] = get_param('login','');
	
	//generate password hash. the password field is only used when new users are created. 
	//during user info update, it is ignored. 
	$auth = new Authentication();
	$fields["password"] = $auth->generate_password_hash(get_param('password','')); 
		
	$fields["custom_member_ref"] = get_param('custom_member_ref','');
	$fields["name"] = get_param('name');
	$fields["nif"] = get_param('nif','');
	$fields["address"] = get_param('address','');
	$fields["city"] = get_param('city','');
	$fields["zip"] = get_param('zip','');
	$fields["phone1"] = get_param('phone1','');
	$fields["phone2"] = get_param('phone2','');
	$fields["web"] = get_param('web','');
	$fields["notes"] = get_param('notes','');
	$fields["active"] = isset($_REQUEST['member_active'])? 1:0; //this is a checkbox; gets send when checked, otherwise not
	$fields["participant"] = isset($_REQUEST['participant'])? 1:0;
	$fields["adult"] = get_param('adult',1);	
	
	$fields["language"] = get_param('language','en');
	$fields["gui_theme"] = get_param('gui_theme','start');
	$fields["email"] = get_param('email','');
	
	$fields["uf_id"] = get_param('uf_id',0);
	
	return $fields;
	
}


/**
 * 
 * performs quick checks on fields for a specific table. 
 * @param unknown_type $table
 * @param unknown_type $field
 * @param unknown_type $value
 * @param unknown_type $type
 */
function validate_field($table, $field, $value, $type='exists'){
	

	$db = DBWrap::get_instance(); 
    
	switch ($type){
		case 'exists':
			$rs = $db->Execute('select * from '.$table.' where '.$field.'=:1q', $value);
			if ($rs->fetch_assoc()) {
				return 1;
		    } else {
		    	return 0;
		    }
		    DBWrap::get_instance()->free_next_results();
		break;	
	}
	
}

/**
 * 
 * change user password. Only logged users can change their own password. 
 * @throws Exception
 */
function change_password($old_password, $new_password){
		global $Text; 
		
		$user_id = get_session_user_id(); 
		
		//check if password for logged user is ok
		$auth = new Authentication();
		$pwdMatch = $auth->check_password('', $old_password, $user_id);
	
		
      	if ($pwdMatch) {
      		do_stored_query('update_password', $user_id, $auth->generate_password_hash($new_password));
      		return 1; 
      	} else {
          	throw new Exception($Text['msg_err_oldPwdWrong']);
      	}
      	      	
}


/**
 * 
 * reset password for given user. only admin can do that. 
 * @param int $user_id
 * @throws Exception
 */
function reset_password($user_id)
{
	global $Text;  
	
	//only admin 
	 if (get_current_role() != 'Hacker Commission')
          throw new Exception($Text['msg_err_adminStuff']);
	
          
    $sendAsEmail = configuration_vars::get_instance()->internet_connection;
    
    
    $newPwd = createPassword();

    
    $auth = new Authentication();
    $db = DBWrap::get_instance(); 
    do_stored_query('update_password', $user_id, $auth->generate_password_hash($newPwd));
    
    
    if ($sendAsEmail){
    	DBWrap::get_instance()->free_next_results();     
		$strSQL = 'SELECT email, login FROM aixada_user WHERE id = :1q';
    	$rs = $db->Execute($strSQL, $user_id);
    	if($rs->num_rows == 0){
    		throw new Exception("This user has no valid email.");
		}
		
		while ($row = $rs->fetch_assoc()) {
      		$toEmail = $row['email'];
            $login =  $row['login'];
    	}
    	
        $subject = $Text['msg_pwd_email_reset'];
        $message = '<p>'.$Text['msg_pwd_change'].
            '<span style="color:red">'. $newPwd ."</span></p>\n";
        $message .= '<p>'.i18n('msg_pwd_email_logon',
                array('user' => '<span style="color:blue">'.$login.'</span>')
            )."</p>\n";
        $message .= '<p><span style="color:#666">'.i18n(
                'msg_pwd_email_change', 
                array('menu' => '"<span style="color:green">'
                        .$Text['nav_myaccount']
                        .'</span>"&gt;"<span style="color:green">'
                        .$Text['nav_myaccount_settings'].'</span>"')
            )."</span></p>\n";
        if (send_mail($toEmail, $subject, $message)){
            echo $Text['msg_pwd_emailed'];
        } else {
            echo $Text['msg_pwd_change'].$newPwd.'<br>'.
                 '<span style="color:red">'.$Text['msg_err_emailed'].'</span>';
		}
    	

    } else {
    	
    	echo $Text['msg_pwd_change'] . $newPwd; 
    	
    }
}



function createPassword($length=8) {
	$chars = "234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$i = 0;
	$password = "";
	while ($i <= $length) {
		$password .= $chars{mt_rand(0,strlen($chars))};
		$i++;
	}
	return $password;
}







	
?>