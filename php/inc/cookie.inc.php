<?php
/** 
 * @package Aixada
 */ 

require_once(__ROOT__ . 'php'.DS.'lib'.DS.'exceptions.php');
require_once(__ROOT__ . 'php'.DS.'inc'.DS.'database.php');

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
  private $session; 

  // when to reissue 
  static $resettime = '3000';

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
      if (!isset($_SESSION)) {
          session_cache_expire(120);
          session_start();
      }
      if ($logged_in) {
		  $this->session = array(
              'user_id' => $user_id,
              'login' => $login,
              'uf_id' => $uf_id, 
              'member_id' => $member_id, 
              'provider_id' => $provider_id,
              'roles' => $roles,
              'current_role' => $current_role,
              'language_keys' => $language_keys,
              'language_names' => $language_names,
              'language' => $current_language_key,
              'theme' => $theme
          );
          $this->save();
      } else if (isset($_SESSION['userdata'])) {
          $this->session = $_SESSION['userdata'];
      }
  }

  /**
   * This function packages, encrypts and sets the cookie. Moreover,
   * it copies all relevant data into $_SESSION['userdata'] 
   */
  private function save() {
      if (!isset($this->session)) {
          throw new AuthException("Bad session");
      }
      $_SESSION['userdata'] = $this->session;
      session_commit();
  }

  /**
   * Is the cookie ok? If the cookie is older than $resettime, it is
   * reset. If the data in $_SESSION['userdata'] doesn't exist, it is
   * also set.
   */
  public function validate() {
    if (isset($this->session)) {
        return $this->session;
    } else {
        throw new AuthException("Not logged in");
    }
  }


  /**
   * Call this function to change the role of a user. The information
   * is written to the cookie and to $_SESSION['userdata'].
   */
  public function change_role($new_role) {
      if (!in_array($new_role, $this->session['roles'])) {
          throw new AuthException("Not logged in role");
      }
      $this->session['current_role'] =  $new_role;
      $this->save();
  }

  /**
   * Call this function to change the role of a user. The information
   * is written to the cookie and to $_SESSION['userdata'].
   */
  public function change_language($new_language_key) {
      if (!in_array($new_language_key, $this->session['language_keys'])) {
          throw new AuthException("Language is not valid");
      }
      $this->session['language'] = $new_language_key;
      $this->save();
  }

  public function logout() {
    session_destroy();
  }
}
