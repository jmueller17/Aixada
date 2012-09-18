<?php

require_once('inc/database.php');
require_once('local_config/config.php');


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


function extract_user_form_values(){
	
	$fields["login"] = get_param('login','');
	$fields["password"] = crypt(get_param('password',''), "ax");
	
	
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
	$fields["active"] = get_param('member_active',1);
	$fields["participant"] = get_param('participant',1);
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
function validate_field($table, $field, $value, $type='unique'){
	

	$db = DBWrap::get_instance(); 
    
	switch ($type){
		case 'unique':
			$rs = $db->Execute('select * from '.$table.' where '.$field.'=:1q', $value);
			if ($rs->fetch_assoc()) {
				echo 1;
		    } else {
		    	echo 0;
		    }
		    DBWrap::get_instance()->free_next_results();
		break;	
	}
	
}

	
?>