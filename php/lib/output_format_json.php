<?php

require_once(__ROOT__ . 'php/external/FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start(); // Starts FirePHP output buffering
$firephp = FirePHP::getInstance(true);


require_once(__ROOT__ . 'php/lib/output_format.php');




/** 
 *	Custom class that accepts a mysqli_result and returns a json encoded string. 
 *	
 */
class output_format_json extends output_format{



	public function __construct($data){

		parent::__construct($data, "json");
	}




	/** 
	 *	Converts the mysqli result set into JSON. 
	 *	Both params put through the params for json_encode. 
	 *
	 */
	public function format_rs($options=0, $depth=512){

		$tmp_rows = array();

	    while ($row = $this->db_result_set->fetch_assoc()) {
	    	$tmp_rows[] = $row; 
	    }

	    $this->json_str = json_encode($tmp_rows, $options, $depth);

		return $this->json_str;
	}


	/**
	 *	To do.... 
	 *
	 */
	public function format_data_table($options=0, $depth=512){

		//$this->get_data_table();

	}




}



?>