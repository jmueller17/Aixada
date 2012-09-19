<?php
/** 
 * @package Aixada
 */ 

require_once 'php/lib/exceptions.php';
require_once 'php/inc/database.php';
// require_once('FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
// ob_start(); // Starts FirePHP output buffering
// $firephp = FirePHP::getInstance(true);

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
  private $can_checkout = false;
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
  //  static $array_glue1 = '*';

  static $checkout_config_file = 'local_config/who.can.checkout';

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
                              $can_checkout=false,
                              $theme=false) {
      //    global $firephp;
    
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
        $this->can_checkout = $can_checkout;
        $this->theme = $theme;
        return;
    } else {
        //    $firephp->log($_COOKIE, 'cookie');
        //    $firephp->log(self::$cookiename, 'cookie');
    
      if (array_key_exists(self::$cookiename, $_COOKIE)) {
	$buffer = $this->_unpackage($_COOKIE[self::$cookiename]);
      } else {
          header("Location: login.php?originating_uri=".$_SERVER['REQUEST_URI']);
      }
    }
  }
  /**
   * This function packages, encrypts and sets the cookie. Moreover,
   * it copies all relevant data into $_SESSION['userdata'] 
   */
  public function set() {
    $cookie = $this->_package();
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
                    'can_checkout' => $this->can_checkout,
    				'theme' => $this->theme);
    $_SESSION['userdata'] = $userdata;
    setcookie(self::$cookiename, $cookie);
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
                        'can_checkout' => $this->can_checkout,
        				'theme' => $this->theme);
      $_SESSION['userdata'] = $userdata;
    }
//     global $firephp;
//     $firephp->log($_SESSION['userdata']);
//     exit();
  }

  /**
   * This function writes a checkout control file
   */
  private function write_checkout_control_file($s) {
      $filename = self::$checkout_config_file;
      $handle = @fopen($filename, 'w');
      if (!$handle)
          throw new Exception("Could not write to local configuration file $filename for managing checkouts. Is the directory universally writable?");
      fwrite($handle, "<?php {$s} ?>");
      fclose($handle);
  }

  private function do_enable_checkout() {
      if (!file_exists(self::$checkout_config_file)) {
          $who_can_checkout = array($this->login => time());
          $s = var_export($who_can_checkout, true);
          $this->write_checkout_control_file($s);
          return true;
      }
      include self::$checkout_config_file;
      if (!isset($who_can_checkout))
          $who_can_checkout = array();
      if (isset($who_can_checkout[$this->login])) {
          $who_can_checkout[$this->login] = time();
          $s = var_export($who_can_checkout, true);
          $this->write_checkout_control_file($s);
          return true;
      }
      $n_users = configuration_vars::get_instance()->n_checkout_enabled_users;
      if (count($who_can_checkout) >= $n_users) {
          // then we check to see whether we kick out anybody
          $now = time();
          $max_time= configuration_vars::get_instance()->n_seconds_enabled_for_checkout;
          foreach($who_can_checkout as $login => $when) {
              if ($now - $when > $max_time) {
                  unset($who_can_checkout[$login]);
                  $who_can_checkout[$this->login] = $now;
                  $s = var_export($who_can_checkout, true);
                  $this->write_checkout_control_file($s);
                  return true;
              }
          }
          return false;
      }
      $who_can_checkout[$this->login] = time();
      $s = var_export($who_can_checkout, true);
      $this->write_checkout_control_file($s);
      return true;
  }

  /** 
   * This function tries to enable the user to check out goods
   */
  private function enable_checkout() {
      $this->can_checkout = $_SESSION['userdata']['can_checkout']   
          = $this->do_enable_checkout();
      //      $this->set();
//       global $firephp;
//       $firephp->log($this, 'cookie');
      return $this->can_checkout;
  }

  private function do_disable_checkout($strict_check = false) {
      if ((include self::$checkout_config_file) != 'OK' and $strict_check) {
      //      if ((include 'local_config/test') != 'OK' and $strict_check) {
          throw new Exception("Could not read local configuration file for managing checkouts");
      } 
      if (!isset($who_can_checkout))
          $who_can_checkout = array();
      if (isset($who_can_checkout[$this->login]))
          unset($who_can_checkout[$this->login]);
      $s = var_export($who_can_checkout, true);
      $this->write_checkout_control_file($s);
      return true;
  }

  /** 
   * This function disables the user from checking out goods
   */
  private function disable_checkout($strict_check = false) {
      $this->can_checkout = $_SESSION['userdata']['can_checkout']   
          = !$this->do_disable_checkout($strict_check);
      //      $this->set();
      return $this->can_checkout;
  }

  /**
   * Call this function to change the role of a user. The information
   * is written to the cookie and to $_SESSION['userdata'].
   */
  public function change_role($new_role) {
      if ($new_role == 'Checkout') {
          if (!$this->enable_checkout()) {
              throw new Exception("Could not change role to checkout.");
          }
          DBWrap::get_instance()->do_stored_query('call initialize_caixa()');
      }
      if ($this->current_role == 'Checkout') {
          $this->disable_checkout();
      }
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
      //    setcookie(self::$cookiename, "", 0);
    unset($_SESSION['userdata']);
    $this->logged_in = false;
    $this->can_checkout = !$this->disable_checkout(false);
    $this->set();
//     global $firephp;
//     $firephp->log($this);
//     exit();
  }

  /**
   *  Package the cookie. 
   */
  private function _package() {
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
           $this->can_checkout,
           $this->theme);
    $cookie = implode(self::$glue, $parts);
    //return $this->_encrypt($cookie);
    return $cookie;
  }

  private function _unpackage($cookie) {
    //$buffer = $this->_decrypt($cookie);
     $buffer = $cookie;
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
        $this->can_checkout,
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