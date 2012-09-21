<?php
/** 
 * @package Aixada
 */ 

$slash = explode('/', getenv('SCRIPT_NAME'));
$app = getenv('DOCUMENT_ROOT') . '/' . $slash[1] . '/';

require_once($app . 'local_config/config.php');
require_once($app . 'php/utilities/general.php');
require_once($app . 'php/inc/database.php');
require_once($app . 'php/lib/table_with_ref.php');
require_once($app . 'local_config/lang/' . get_session_language() . '.php');
require_once($app . 'FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
//ob_start(); // Starts FirePHP output buffering

$firephp = FirePHP::getInstance(true);

DBWrap::get_instance()->debug = true;

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
    $db = DBWrap::get_instance();

    $rs = do_stored_query('check_credentials', $login, $password);
    $row = $rs->fetch_assoc();
    $db->free_next_results();

    if (!$row or !array_key_exists('id', $row)) {
        global $Text;
        throw new AuthException($Text['msg_err_incorrectLogon']);
    }

    if (!array_key_exists('uf_id', $row) or intval($row['uf_id']) == 0) {
        global $Text;
        throw new AuthException($Text['msg_err_noUfAssignedYet']);
    }

    $user_id = $row['id'];
    $login = $row['login'];
    $uf_id = $row['uf_id'];
    $member_id = $row['member_id'];
    $provider_id = $row['provider_id'];
    $language = $row['language'];
    $roles = $this->_ask_roles($db, $user_id);
    $theme	= $row['gui_theme'];
    $current_role = ( in_array('Consumer', $roles) ? 'Consumer' 
                      : ( isset($roles[0]) ? $roles[0] : '' ) );

    return array($user_id, $login, $uf_id, $member_id, $provider_id, $roles, $current_role, $language, $theme);
  }
}
?>