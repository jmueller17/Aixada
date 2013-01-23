<?php

require_once(__ROOT__ . 'php/lib/abstract_import_manager.php');


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