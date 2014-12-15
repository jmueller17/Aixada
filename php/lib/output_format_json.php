<?php

require_once(__ROOT__ . 'php/external/FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start(); // Starts FirePHP output buffering
$firephp = FirePHP::getInstance(true);


require_once(__ROOT__ . 'php/lib/abstract_output_format.php');




/** 
 *	Custom class that accepts a mysqli_result and returns a json encoded string. 
 *	
 */
class output_format_json extends abstract_output_format{


	/**
	 *	the resulting array of json string
	 * 	@var array 
	 */
	public $json_str = null; 



	public function __construct($rs){

		parent::__construct($rs);
	}




	/** 
	 *	Converts the mysqli result set into JSON. 
	 *	Both params put through the params for json_encode. 
	 *
	 */
	public function format($options=0, $depth=512){

		$tmp_rows = array();

	    while ($row = $this->db_result_set->fetch_assoc()) {
	    	$tmp_rows[] = $row; 
	    }

	    $this->json_str = json_encode($tmp_rows, $options, $depth);

		return $this->json_str;
	}




}



?>