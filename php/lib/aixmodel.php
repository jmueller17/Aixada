<?php 



/**
 *	Base class for all aixada objects, usually referring to the different database tables like products, members, etc. 
 *
 */
class aixmodel {
	

	/**
	 * 	@param string $table Name of the aixada database table the instance refers to 
	 */
	public $table; 

	
	/**
	 *	@param array $fields Assoc array of database field names. 
	 */
	public $fields; 



	public function __construct($table=""){

		$this->table = $table;  

	}



	/**
	 *
	 *	Retrieves the submitted values by the URL/form. Needs to be overwritten by the subclass. 
	 */
	public function read_form_submit(){}



	


}



?>