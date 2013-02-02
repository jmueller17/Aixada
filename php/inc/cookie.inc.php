<?php
/** 
 * @package Aixada
 */ 

require_once(__ROOT__ . 'php'.DS.'lib'.DS.'exceptions.php');
require_once(__ROOT__ . 'php'.DS.'inc'.DS.'database.php');

if (!isset($_SESSION)) {
    session_start();
}



/**
 * The following class implements cookies, based on an
 * implementation from George Schlossnagle, Advanced PHP Programming, p.341
 *
 * @param int $user_id stores the unique user id
 * @param int $uf_id stores the associated uf identifier, or empty
 * @param int $member_id stores the associated member identifier, or empty
 * @param int $provider_id stores the associated provider identifier, or empty
 * @param array $roles stores the associated role identifiers
 * @param string $current_role stores the current role
 * @param string $language stores the current language
 *
 * @package Aixada
 * @subpackage Authentication
 */


class Cookie {
  // Aixada data
  private $logged_in = false;
  private $user_id;
  private $login;
  private $uf_id; 
  private $member_id; 
  private $provider_id;
  private $roles;
  private $current_role;
  private $language_keys;
  private $language_names;
  private $current_language_key;
  private $theme; 

  // Cookie data
  private $created;
  private $version;
  // the mcrypt handle
  private $td;

  // mcrypt information
  static $cypher  = 'blowfish';
  static $mode    = 'cfb';
  static $key     = 'aIxAdA947AiXaDa';

  // cookie format information
  static $cookiename = 'USERAUTH';
  static $myversion  = '1';
  // when to expire the cookie
  static $expiration = '6000';
  // when to reissue 
  static $resettime = '3000';
  static $glue  = '|';
  static $array_glue = '~';

  public function __construct($logged_in=false, 
                              $user_id=false, 
                              $login = false, 
                              $uf_id=false, 
                              $member_id=false, 
                              $provider_id=false, 
                              $roles=false, 
                              $current_role=false, 
                              $language_keys=false, 
                              $language_names=false, 
                              $current_language_key=false, 
                              $theme=false) {
      $this->td = mcrypt_module_open(self::$cypher, '', self::$mode, '');
      if (!$this->td) 
	  die("<br>Error in opening mcrypt with cypher=".self::$cypher .", mode=".self::$mode."<br>");
      if ($logged_in) {
		  $this->logged_in = true;
		  $this->user_id = $user_id;
		  $this->login = $login;
		  $this->uf_id = $uf_id; 
		  $this->member_id = $member_id; 
		  $this->provider_id = $provider_id;
		  $this->roles = $roles;
		  $this->current_role = $current_role;
		  $this->language_keys = $language_keys;
		  $this->language_names = $language_names;
		  $this->current_language_key = $current_language_key;
		  $this->theme = $theme;
		  return;
      } else {
		  if (array_key_exists(self::$cookiename, $_COOKIE)) {
		      $buffer = $this->_unpackage($_COOKIE[self::$cookiename]);
		  } else {
		      var_dump($_COOKIE);
		      throw new AuthException("Bad cookie");
		  }
      }
  }

  /**
   * This function packages, encrypts and sets the cookie. Moreover,
   * it copies all relevant data into $_SESSION['userdata'] 
   */
  public function set() {
      $cookie = $this->package();
      $userdata=array('user_id'      => $this->user_id, 
		      'login'         => $this->login, 
		      'uf_id'        => $this->uf_id, 
		      'member_id'    => $this->member_id, 
		      'provider_id'  => $this->provider_id,
		      'roles'   => $this->roles,
		      'current_role' => $this->current_role,
		      'language_keys' => $this->language_keys,
		      'language_names' => $this->language_names,
		      'language' => $this->current_language_key,
		      'theme' => $this->theme);
      $_SESSION['userdata'] = $userdata;
      $_COOKIE[self::$cookiename] = $cookie;
      //      setcookie(self::$cookiename, $cookie); 
      //  don't do this, because it leads to a duplicate cookie.
  }

  /**
   * Is the cookie ok? If the cookie is older than $resettime, it is
   * reset. If the data in $_SESSION['userdata'] doesn't exist, it is
   * also set.
   */
  public function validate() {
    if (!$this->version || !$this->created || !$this->user_id) {
      throw new AuthException("Malformed cookie");
    }
    if ($this->version != self::$myversion) {
      throw new AuthException("Version mismatch");
    }
    if (!$this->logged_in) {
        throw new AuthException("Not logged in");
    }
    if ((time() - $this->created) > self::$resettime) {
      $this->set();
    }
    if (!isset($_SESSION['userdata'])) {
        $userdata=array('logged_in'    => $this->logged_in,
                        'user_id'      => $this->user_id, 
                        'login'        => $this->login, 
                        'uf_id'        => $this->uf_id, 
                        'member_id'    => $this->member_id, 
                        'provider_id'  => $this->provider_id,
                        'roles'        => $this->roles,
                        'current_role' => $this->current_role,
                        'language_keys'     => $this->language_keys,
                        'language_names'     => $this->language_names,
                        'language' => $this->current_language_key,
						'theme' => $this->theme);
      $_SESSION['userdata'] = $userdata;
    }
  }


  /**
   * Call this function to change the role of a user. The information
   * is written to the cookie and to $_SESSION['userdata'].
   */
  public function change_role($new_role) {
      $this->current_role   = $new_role;
      $_SESSION['userdata']['current_role']   = $this->current_role;
      $this->set();
  }

  /**
   * Call this function to change the role of a user. The information
   * is written to the cookie and to $_SESSION['userdata'].
   */
  public function change_language($new_language_key) {
      $this->current_language_key = $new_language_key;
      $_SESSION['userdata']['language'] = $this->current_language_key;
      $this->set();
  }

  public function logout() {
    unset($_SESSION['userdata']);
    unset($_COOKIE[self::$cookiename]);
    setcookie(self::$cookiename, '', time() - 3600);
    /* $this->logged_in = false; */
    /* $this->user_id=false;  */
    /* $this->login = false;  */
    /* $this->uf_id=false;  */
    /* $this->member_id=false;  */
    /* $this->provider_id=false;  */
    /* $this->roles=false;  */
    /* $this->current_role=false;  */
    /* $this->language_keys=false;  */
    /* $this->language_names=false;  */
    /* $this->current_language_key=false;  */
    /* $this->theme=false; */

    /* $this->set(); */
  }

  /**
   *  Package the cookie. 
   */
  public function package() {
      if ($this->logged_in) {
	  $parts = array(self::$myversion, 
			 time(), 
			 $this->logged_in,
			 $this->user_id, 
			 $this->login,
			 $this->uf_id, 
			 $this->member_id, 
			 $this->provider_id,
			 implode(self::$array_glue, $this->roles),
			 $this->current_role,
			 implode(self::$array_glue, $this->language_keys),
			 implode(self::$array_glue, $this->language_names),
			 $this->current_language_key,
			 $this->theme);
	  $cookie = implode(self::$glue, $parts);
      } else {
	  $cookie = 'x';
      }
      return urlencode($this->_encrypt($cookie));
  }

  private function _unpackage($cookie) {
      if ($cookie == '') {
	  $this->logout();
	  return;
      }
      $buffer = $this->_decrypt(urldecode($cookie));
      list($this->version, 
	   $this->created, 
	   $this->logged_in,
	   $this->user_id, 
	   $this->login,
	   $this->uf_id, 
	   $this->member_id, 
	   $this->provider_id, 
	   $role_array,
	   $this->current_role,
	   $lang_key_array,
	   $lang_name_array,
	   $this->current_language_key,
	   $this->theme) = explode(self::$glue, $buffer);
        
      $this->roles = explode(self::$array_glue, $role_array);
      $this->language_keys = explode(self::$array_glue, $lang_key_array);
      $this->language_names = explode(self::$array_glue, $lang_name_array);
      if ($this->version != self::$myversion || !$this->created || ! $this->user_id) {
	  throw new AuthException();
      }
  }

  protected function _encrypt($plaintext) {
      $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($this->td), MCRYPT_RAND);
      if (!$iv) throw new AuthException("error in iv ");
      mcrypt_generic_init($this->td, self::$key, $iv);
      $crypttext = mcrypt_generic($this->td, $plaintext);
      mcrypt_generic_deinit($this->td);
      return $iv.$crypttext;
  }

  protected function _decrypt($crypttext) {
      $ivsize = mcrypt_enc_get_iv_size($this->td);
      $iv = substr($crypttext, 0, $ivsize);
      $subtext = substr($crypttext, $ivsize);
      mcrypt_generic_init($this->td, self::$key, $iv);
      $plaintext = mdecrypt_generic ($this->td, $subtext);
      mcrypt_generic_deinit($this->td);
      return $plaintext;
  }

  private function _reissue() {
      $this->created = time();
  }

}
?>