<?php

/** 
 * @package Aixada
 */ 

require_once('FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start(); // Starts FirePHP output buffering
$firephp = FirePHP::getInstance(true);

  //ob_start(); // Starts FirePHP output buffering
require_once('abstract_cart_manager.php');


/**
 * The class for aixada_cart_item
 *
 * @package Aixada
 * @subpackage Shop_and_Orders
 */ 

class shop_item extends abstract_cart_row {
  

  /**
   * deducts stock if possible, and calculates the price of the row. The function Insert can throw an InsufficientStockException.
   * @see DBWrap::Insert
   * @see InsufficientStockException
   */
  public function commit_string()
  {
    return '(' 
      . $this->_uf_id . ','
      . "'" . $this->_date . "',"
      . $this->_product_id . ','
      . $this->_quantity
      . ')';
  }
}

/**
 * The class that manages a shopping cart for the groceries bought from stock on a certain day.
 *
 * There can be more than one shopping cart on any given day. This is
 * why carts have timestamps and not just dates.
 *
 * @package Aixada
 * @subpackage Shop_and_Orders
 */
class shop_cart_manager extends abstract_cart_manager {

  /**
   * Inserts an empty shopping cart into the aixada_shop_cart table in order to get a unique cart_id. 
   * This is later used to tie the individual rows to the cart. 
   */
  public function __construct($uf_id, $date_for_shop)
  {
    $this->_id_string = 'shop';
    $this->_commit_rows_prefix = 
      'INSERT INTO aixada_shop_item' .
      ' (uf_id, date_for_shop, product_id, quantity)' .
      ' VALUES ';
    parent::__construct($uf_id, $date_for_shop);
  }  


  /**
   * Overloaded function to make sale_item rows
   */
  protected function _make_rows($arrQuant, $arrPrice, $arrProdId)
  {
    for ($i=0; $i < count($arrQuant); ++$i)
      $this->_rows[] = new shop_item($this->_date, 
				     $this->_uf_id, 
				     $arrProdId[$i], 
				     $arrQuant[$i], 
				     $arrPrice[$i]);
  }


  /**
   * abstract function to delete the rows of a cart from an *_item table
   */ 
  protected function _delete_rows()
  {
    $db = DBWrap::get_instance();
    $db->Execute('DELETE FROM ' 
		 . $this->_item_table_name 
		 . ' WHERE uf_id=:1q AND date_for_'
		 . $this->_id_string 
		 . '=:2q AND ts_validated=0', $this->_uf_id, $this->_date);
  }

  /**
   * Find a shop cart with ts_validated = 0 and date_for_shop = date. 
   * If there is more than one such, raise an exception.
   */
  public function get_shop_items_for_date($date, $uf = -1)
  {
    if ($uf == -1) {
      $uf = $this->_uf_id;
    }
    return $this->_table_manager->stored_query('products_for_shopping', $date, $uf);
  }

}

?>