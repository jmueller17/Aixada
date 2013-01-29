<?php


/**
 * 
 * Base class for all import/export format wrappers
 * @author joerg
 *
 */
class abstract_wrapper_format {
	
	protected $_uri = ''; 
	
	
	
	public function __construct($uri){
		
		$this->_uri = $uri; 
	}
	
	
	public function parse(){}
	
	
	protected function authenticate(){
		
	}
	
}

?>