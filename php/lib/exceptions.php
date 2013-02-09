<?php

/** 
 * @package Aixada
 * @subpackage Exceptions
 */ 

/** 
 * @package Aixada
 * @subpackage Exceptions
 */ 

class DataException extends Exception {}

/** 
 * @package Aixada
 * @subpackage Exceptions
 */ 

class ForeignKeyException extends Exception {}


/** 
 * @package Aixada
 * @subpackage Exceptions
 */ 
class InsufficientStockException extends Exception 
{
  public function __construct($product_id, $quantity, $after)
  {
    parent::__construct();
    $this->message = 'Insufficient stock of product ' . $product_id . '. Wanted ' . $quantity . ', but only ' . ($quantity + $after) . ' available';
  }
}

/** 
 * @package Aixada
 * @subpackage Exceptions
 */ 
class DateException extends Exception
{
  public function __construct($date)
  {
    parent::__construct();
    $this->message = 'The date ' . $date . ' is not activated for ordering.';
  }
}

/** 
 * @package Aixada
 * @subpackage Exceptions
 */ 
class InternalException extends Exception {}

/** 
 * @package Aixada
 * @subpackage Exceptions
 */ 
class AuthException extends Exception {}

/** 
 * @package Aixada
 * @subpackage Exceptions
 */ 
class SignonException extends Exception {}

/** 
 * @package Aixada
 * @subpackage Exceptions
 */ 
class XMLParseException extends Exception {
    public function __construct($expected, $found, $xml)
    {
	parent::__construct();
	$this->message = 'XML parse error. Expected ' . $expected
	    . ', found ' . $found 
	    . ' in ' . $xml;
    }
}

?>