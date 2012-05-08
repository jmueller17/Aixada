<?php

/** 
 * @package Aixada
 */ 

require_once('FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start(); // Starts FirePHP output buffering
$firephp = FirePHP::getInstance(true);

  //ob_start(); // Starts FirePHP output buffering
require_once('shop_cart_manager.php');

/**
 * The class that manages a validation cart for the groceries bought from stock on a certain day.
 *
 * @package Aixada
 * @subpackage Validation
 */
class validation_cart_manager extends shop_cart_manager {

  /**
   * @var int stores the UF in charge of the register
   */
  private $_op_id;

  /**
   * Constructor
   */
  public function __construct($op_id, $uf_id, $date_for_shop)
  {
    $this->_op_id = $op_id;
    parent::__construct($uf_id, $date_for_shop);
  }  

  /**
   * helper function to ask the database for a list of how much is in stock
   */
  private function _get_scarce_items()
  {
    $id_list = '(';
    $qty = array();
    foreach ($this->_rows as $si) {
      $id = $si->get_product_id();
      $id_list .= $id . ',';
      $qty[$id] = $si->get_quantity();
    }
    $id_list = rtrim($id_list, ',') . ')';
    $db = DBWrap::get_instance();
    $rs = $db->Execute('select id, name, stock_actual from aixada_product where id in ' . $id_list);
    $scarce_list = array();
    while ($row = $rs->fetch_array()) {
      if ($qty[$row[0]] > $row[2]) {
	$scarce_list[] = $row[1];
      }
    }
    return $scarce_list;
  }

  /**
   * Overloaded function to check the rows for sufficient stock
   */
  protected function _check_rows()
  {
      return;
      /*
      global $Text;
      $msg='';
      $scarce_list = $this->_get_scarce_items();
      if (count($scarce_list) > 0) {
          foreach($scarce_list as $name) {
              $msg .= $name . ',';
          }
          $msg = $Text['msg_err_insufficient_stock'] . rtrim($msg, ',');
          throw new Exception($msg);
      }
      */
  }

  /**
   * Overloaded function to commit the cart to the database
   */
  protected function _postprocessing()
  {
    do_stored_query('deduct_stock_and_pay', $this->_date, $this->_uf_id, $this->_op_id);
    do_stored_query('validate_shop_items', $this->_date, $this->_uf_id, $this->_op_id);
  }

}

?>