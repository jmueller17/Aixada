<?php
/** 
 * @package Aixada
 */ 

require_once(__ROOT__ . 'local_config'.DS.'config.php');
require_once(__ROOT__ . 'php'.DS.'utilities'.DS.'general.php');
require_once(__ROOT__ . 'php'.DS.'inc'.DS.'database.php');
require_once(__ROOT__ . 'php'.DS.'lib'.DS.'table_with_ref.php');
require_once(__ROOT__ . 'local_config'.DS.'lang'.DS. get_session_language() . '.php');


if (configuration_vars::get_instance()->development){
	require_once(__ROOT__ . 'php/external/FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
	ob_start();
	$firephp = FirePHP::getInstance(true);
}


/**
 * The following class implements checking for authentication, based on an
 * implementation from George Schlossnagle, Advanced PHP Programming, p.341
 *
 * @package Aixada
 * @subpackage Authentication
 */

class Authentication {

	private function _ask_roles($db, $user_id)
	{
	    $strSQL = 'SELECT role FROM aixada_user_role WHERE user_id = :1q';
	    $rs = $db->Execute($strSQL, $user_id);
	    $roles = array();
	    while ($row = $rs->fetch_assoc()) {
	      $roles[] = $row['role'];
	    }
	    return $roles;
	 }
  
  	/**
  	 * 
  	 * Generates MD5 password hash using a random salt. 
  	 * @param string $password
  	 */
	public function generate_password_hash($password)
	{
	   $random_seed =  substr(md5(rand()), 0, 7);
	   return crypt($password,'$1$'.$random_seed.'$');
	}
	
	/**
	 * 
	 * Compatability with old password encryption. Checks if has is
	 * old "ax" style or newer random cryp salt
	 * @param string $password the plain password
	 * @param unknown_type $password_hash the password hash as currently stored in the db
	 */
	public function check_password_hash($password,$password_hash)
	{ 		
		if (!strncmp($password_hash, 'ax' , 2)){
       		// an old aixada two character CRYPT_STD_DES crypt() salt
       		return crypt($password,'ax')==$password_hash;
   		} else if (!strncmp($password_hash,'$1$', 3)) {
      		// new aixada CRYPT_MD5 random crypt() salt
      		$random_seed = substr($password_hash,0,11);
      		$password_hash_check = crypt($password,$random_seed);
      		return $password_hash_check == $password_hash;
   		} else {
	   		return false;
   		}
	}
	
	
	/**
	 * 
	 * Checks if password for given login / user_id is correct. Either 
	 * login or user_id can be submitted.  
	 * @param string $login The login of the user
	 * @param int $user_id The user_id of the logged user. 
	 * @param string $plain_text_pwd
	 */
	public function check_password($login, $plain_text_pwd, $user_id=0){
		$db = DBWrap::get_instance();
 
    	//compatability with old password encryption 
    	$rs = do_stored_query('retrieve_credentials', $login, $user_id);
    	$row = $rs->fetch_assoc();
    	$db->free_next_results();  	
    	
  		return $this->check_password_hash($plain_text_pwd, $row['password']);
		
	}

   /**
    * This function authenticates a user, based on his login and
    * password. If successful, it queries all properties associated to
    * the username in various tables in the database
    *
    * @param string $login the login name
    * @param string $password the given password
    * @return a list of properties: user_id, uf_id, member_id, provider_id, roles, current_role_id, current_role_description. The last two can be 0 and '', respectively.
    */
  	public function check_credentials($login, $password) 
  	{
  		global $Text;
    	
  		$db = DBWrap::get_instance();
 
    	//compatability with old password encryption 
    	$rs = do_stored_query('retrieve_credentials', $login, 0);
    	$row = $rs->fetch_assoc();
    	$db->free_next_results();  	
    	
  		$pwdMatches = $this->check_password_hash($password, $row['password']);

  		if ($pwdMatches){
  			
  			if (!array_key_exists('uf_id', $row) or intval($row['uf_id']) == 0){
  				throw new AuthException($Text['msg_err_noUfAssignedYet']);	
  			}
  			
  			if (!array_key_exists('is_active_member', $row) or intval($row['is_active_member']) == 0){
  				 throw new AuthException($Text['msg_err_deactivatedUser']);
  			}
  			
  			$user_id = $row['id'];
		    $login = $row['login'];
		    $uf_id = $row['uf_id'];
		    $member_id = $row['member_id'];
		    $provider_id = $row['provider_id'];
		    $language = $row['language'];
		    $roles = $this->_ask_roles($db, $user_id);
		    $theme	= $row['gui_theme'];
		    $current_role = ( in_array('Consumer', $roles) ? 'Consumer' : (isset($roles[0]) ? $roles[0] : '' ) );
		    
	
	    	return array($user_id, $login, $uf_id, $member_id, $provider_id, $roles, $current_role, $language, $theme);
  			
  		} else {
  			throw new AuthException($Text['msg_err_incorrectLogon']);
  		}
	    
  }
}
?>