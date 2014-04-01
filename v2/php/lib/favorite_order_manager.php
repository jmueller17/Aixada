<?php

  /** 
   * @package Aixada
   */ 



require_once(__ROOT__ . 'php/lib/abstract_cart_manager.php');

/**
 * The class that manages a row of a favorite shopping cart.
 *
 * @package Aixada
 * @subpackage Shop_and_Orders
 */
class favorite_order_item extends abstract_cart_row {
  
    public function __construct($order_cart_id, $uf_id, $product_id, $quantity, $price)
    {
        parent::__construct(0, $product_id, $uf_id, $quantity, $price);
        $this->_order_cart_id = $order_cart_id;
    }

    public function commit_string() 
    {
        return '('
            . $this->_order_cart_id . ','
            . $this->_uf_id . ','
            . $this->_product_id . ','
            . $this->_quantity 
            . ')';
    }
}

/**
 * The class that manages a favorite shopping cart for placing an order for a future day.
 *
 * @package Aixada
 * @subpackage Shop_and_Orders
 */
class favorite_order_cart_manager extends abstract_cart_manager {

    /**
     * @var string stores the name of the favorite order cart
     */
    private $_name;

    /**
     * @var string stores the id of the favorite order cart
     */
    private $_cart_id;

    public function __construct($uf_id, $name)
    {
        $this->_id_string = 'favorite_order';
        $this->_name = $name;
        $this->_commit_rows_prefix = 
            'insert into aixada_favorite_order_item' .
            ' (favorite_order_cart_id, uf_id, product_id, quantity)' .
            ' values ';
        parent::construct($uf_id);
        $rs = DBWrap::get_instance()->Execute("call find_or_create_favorite_order cart (:1, :2q)", $uf_id, $name);
        $row = $rs->fetch_array();
        $this->_cart_id = $row[0];
    }

    /**
     * Overloaded function to make sale_item rows
     */
    protected function _make_rows($arrQuant, $arrPrice, $arrProdId, $arrPreOrder)
    {
        for ($i=0; $i < count($arrQuant); ++$i)
            $this->_rows[] = new favorite_order_item($this->_cart_id,
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
        $db->Execute('delete from ' 
                     . $this->_item_table_name 
                     . ' where uf_id=:1q and favorite_order_cart_id=:2q', 
                     $this->_uf_id, $this->_cart_id);
    }
}

?>