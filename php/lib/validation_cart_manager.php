<?php

/** 
 * @package Aixada
 */ 



require_once(__ROOT__.'php/lib/shop_cart_manager.php');

/**
 * The class to validate a cart
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
   * Overloaded function to commit the cart to the database
   */
 protected function _postprocessing($arrQuant, $arrProdId, $arrIva, $arrRevTax, $arrOrderItemId, $cart_id, $arrPreOrder, $arrPrice, $addNotes)
  {
    //do_stored_query('deduct_stock_and_pay', $cart_id);
    do_stored_query('validate_shop_cart', $cart_id, $this->_op_id, 'cart #', 1);
  }

}

?>
