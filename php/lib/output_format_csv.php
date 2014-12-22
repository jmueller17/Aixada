<?php

require_once(__ROOT__ . 'php/external/FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start(); // Starts FirePHP output buffering
$firephp = FirePHP::getInstance(true);


require_once(__ROOT__ . 'php/lib/output_format.php');



/** 
 *	Custom class that accepts a mysqli_result and returns a CSV array, string, or file
 *	
 */
class output_format_csv extends output_format{



	/**
	 *	if the mysql column names should be included in the header of the CSV
	 *	@var bool
	 */
	public $header = true; 


	/**
	 *	The field delimiter of the CSV 
	 *	@var string
	 */
	public $delimiter = "\t";


	/**
	 * 	The enclosing character
	 *	@var string 
	 */
	public $enclose = "\"";




	public function __construct($data, $header=true, $delimiter="\t", $enclose="\""){

		$this->header = $header; 
		$this->delimiter = $delimiter; 
		$this->enclose = $enclose;

		parent::__construct($data, "csv");
	}




	/** 
	 *
	 *	Converts the mysqli result set into CSV string. Since we need the column names from the result set, 
	 *	we need to work always from the data_table representation and not the db result set directly. 
	 */
	public function format_rs(){

		if (!$this->rs_exists()){
			throw new Exception("CSV format mysqli_result exception: result set is null");
		}

		//for the csv function, we need the data as matrix. 
		$this->get_data_table($this->header);

		$memhandle = fopen('php://temp/maxmemory:'. (5*1024*1024), 'r+');

		foreach ($this->db_data_table as $row) {
      			fputcsv($memhandle, $row, $this->delimiter, $this->enclose);
	  	}
		
		rewind($memhandle);

		// put it all in a variable
		$this->out_str = stream_get_contents($memhandle);

		return $this->out_str; 

	} //end 




	/**
	 *
	 *	Converts the data_table representation to CSV.  Assumes that a valid 
	 *	data table exists. 
	 */
	public function format_data_table(){

		if (!$this->data_table_exists()){
			throw new Exception("CSV format data table exception: data table is null");
		}

		$memhandle = fopen('php://temp/maxmemory:'. (5*1024*1024), 'r+');

		foreach ($this->db_data_table as $row) {
      			fputcsv($memhandle, $row, $this->delimiter, $this->enclose);
	  	}
		
		rewind($memhandle);

		// put it all in a variable
		$this->out_str = stream_get_contents($memhandle);

		return $this->out_str; 

	}


}



?>