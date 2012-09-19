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
  
	
	protected $_iva_percent = 0; 
	
	protected $_rev_tax_percent = 0; 
	
	protected $_order_item_id = 0; 
 
	public function __construct($product_id, $quantity, $cart_id, $iva, $revtax, $order_item_id, $unit_price_stamp){
		$this->_iva_percent = $iva; 
		$this->_rev_tax_percent = $revtax;
		$this->_order_item_id = $order_item_id;
		
		 
		$this->_product_id = $product_id;
        $this->_quantity = $quantity;
        $this->_cart_id = $cart_id; 
        $this->_unit_price_stamp = $unit_price_stamp;
		
		//parent::__construct(0, 0, $product_id, $quantity, $cart_id);
	}

	 //(cart_id, order_item_id, product_id, quantity, iva_percent, rev_tax_percent)
	public function commit_string()
	{
	   return '('
	   			. $this->_cart_id 		. ','
	   			. $this->_order_item_id . ','
	   			. $this->_product_id 	. ','
	   			. $this->_quantity 		. ','
	   			. $this->_iva_percent	. ','
	   			. $this->_rev_tax_percent . ','
	   			. $this->_unit_price_stamp . ')';
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
      'insert into aixada_shop_item' .
      ' (cart_id, order_item_id, product_id, quantity, iva_percent, rev_tax_percent, unit_price_stamp)' .
      ' values ';
    parent::__construct($uf_id, $date_for_shop);
  }  


  /**
   * Overloaded function to make sale_item rows
   */
  	protected function _make_rows($arrQuant, $arrProdId, $arrIva, $arrRevTax, $arrOrderItemId, $cartId, $arrPreorder, $arrPrice)
    {

    	$this->_cart_id = (isset($cartId) && $cartId>0)? $cartId:0; 
    	
    	
    	$db = DBWrap::get_instance();
    	
    	//create a new one if it doesn't exist. 
		if ($this->_cart_id == 0){
    		
    		try {
	    		$rs = $db->Execute('insert into aixada_cart (uf_id, date_for_shop) values (:1q, :2q)', $this->_uf_id, $this->_date);
	        	$this->_cart_id = $db->get_last_id();
	        	
    		} catch(Exception $e) {
    			header('HTTP/1.0 401 ' . $e->getMessage());
    			die ($e->getMessage());
			}  
    		    
    	//check if cart is already validated
		} else if ($this->_cart_id > 0){
    		
    		$rs = $db->Execute('select * from aixada_cart where id=:1q and ts_validated=0', $this->_cart_id);
    		
    		global $firephp;
            $firephp->log($rs->num_rows, "not validated? num_rows=1 true");
    		
    		if ($rs->num_rows == 0) {
    		 throw new Exception('shop_cart_manager::_make_rows: shop cart has already been validated!!');
            	exit;
    		}
    		$db->free_next_results();
    		
    	}
    	
   
    	for ($i=0; $i < count($arrQuant); ++$i){
    		
    		//if item is stock, set order_item_id to null	    	
    		$order_item_id = (isset($arrOrderItemId[$i]) && $arrOrderItemId[$i]>0)? $arrOrderItemId[$i]:'null';
    		
    		$this->_rows[] = new shop_item(
					     				 $arrProdId[$i], 
					     				 $arrQuant[$i],
					     				 $this->_cart_id, 
					     				 $arrIva[$i],
					     				 $arrRevTax[$i],
					     				 $order_item_id,
					     				 $arrPrice[$i]
					     				 );
    	}
  	}


  /**
   * abstract function to delete the rows of a cart from an aixada_shop_item
   */ 
  protected function _delete_rows()
  {
    $db = DBWrap::get_instance();
		 
    $db->Execute('DELETE FROM aixada_shop_item WHERE cart_id=:1q', $this->_cart_id);
  }
  
  
  /**
   * 
   */
  protected function _delete_cart()
  {
  	if ($this->_cart_id > 0){
  		$db = DBWrap::get_instance(); 
    	$db->Execute('delete from aixada_cart where id=:1q', $this->_cart_id);
    	$this->_cart_id = 0; 
  	}  	
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