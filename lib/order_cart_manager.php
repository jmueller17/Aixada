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
 * The class that manages a row of a shopping cart for placing an order for a future day.
 *
 * @package Aixada
 * @subpackage Shop_and_Orders
 */
class order_item extends abstract_cart_row {

	
    public function commit_string() 
    {
        return '(null, '					//order_id is always null; set when order is send_off/closed
        	. $this->_cart_id . ","
            . "'" . $this->_date . "',"
            . $this->_uf_id . ','
            . $this->_product_id . ','
            . $this->_quantity 
            . ')';
            
     //        (order_id, cart_id, date_for_order, uf_id, product_id, quantity)
    }
}

//replace into aixada_order_item (date_for_order, uf_id, product_id, quantity) values (2012-06-21,'82',860,2,12.5); 


/**
 * The class that manages a shopping cart for placing an order for a future day.
 *
 * There can be at most one shopping cart for any given day. This is
 * why carts have only dates.
 *
 * @package Aixada
 * @subpackage Shop_and_Orders
 */
class order_cart_manager extends abstract_cart_manager {

    /**
     * Although aixada_order_item has a field order_id, this is set to null by default.
     * @param int $uf_id
     * @param date $date_for_order
     */
    public function __construct($uf_id, $date_for_order)
    {
        $this->_id_string = 'order';
        $this->_commit_rows_prefix = 
            'replace into aixada_order_item' .
            ' (order_id, cart_id, date_for_order, uf_id, product_id, quantity)' .
            ' values ';
        parent::__construct($uf_id, $date_for_order); 
    }

    

	/**
	 * Overload function of _make_rows of abstract cart manager. 
	 * 
	 * @param array $arrQuant quantity of product bought
	 * @param array $arrProdId product id of item bought
	 * @param array $arrIva	iva in percent of product
	 * @param array $arrRevTax	rev tax percent of product
	 * @param array $arrOrderItemId	the id from aixada_order_item(id). not used in order
	 * @param array $arrCartId		the id of aixada_cart(id). If set, this indicates favorite cart
	 * @param array $arrPreOrder		true/false if item is preorder
	 */
    protected function _make_rows($arrQuant, $arrProdId, $arrIva, $arrRevTax, $arrOrderItemId, $cart_id, $arrPreOrder)
    {
    	//set the cartid to null for most orders. order_items have cart_id only if bookmarked as "favorite" cart
    	$this->_cart_id = (isset($cart_id) && is_int($cart_id))? $cart_id:'null';
    	
        for ($i=0; $i < count($arrQuant); ++$i) {
            if ($arrPreOrder[$i] == 'false'){
            	
                $this->_rows[] = new order_item($this->_date,
                                                $this->_uf_id,
                                                $arrProdId[$i], 
                                                $arrQuant[$i],  
                                                $this->_cart_id);
            } 
                                                
        }
    }

    /**
     * Overloaded function to commit the cart to the database
     */
    protected function _postprocessing($arrQuant, $arrProdId, $arrIva, $arrRevTax, $arrOrderItemId, $cart_id, $arrPreOrder)
    {
        //do_stored_query('convert_order_to_shop', $this->_uf_id, $this->_date);

        // now store preorder items
       	$this->_rows = array();
        for ($i=0; $i < count($arrQuant); ++$i) {
            if ($arrPreOrder[$i] == 'true')
             	$this->_rows[] = new order_item('1234-01-23',
                                                $this->_uf_id,
                                                $arrProdId[$i], 
                                                $arrQuant[$i],  
                                                'null');
                                                
        }
        $this->_commit_rows();
    }

    /**
     * Read products for an order. Only products with active=1, status>1 are listed.
     * @return string an XML string of available products, grouped by provider
     */
    public function products_for_order_XML()
    {
        /*$strXML = '<aixada_product_row_set>';
        $strXML .= $this->rowset_to_XML($this->get_products_for_order());
        $strXML .= '</aixada_product_row_set>';
        return $strXML;*/
    }


}



?>