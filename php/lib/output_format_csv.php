<?php

require_once(__ROOT__ . 'php/external/FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start(); // Starts FirePHP output buffering
$firephp = FirePHP::getInstance(true);


require_once(__ROOT__ . 'php/lib/abstract_output_format.php');



/** 
 *	Custom class that accepts a mysqli_result and returns a CSV array, string, or file
 *	
 */
class output_format_csv extends abstract_output_format{


	/**
	 *	the resulting array of csv values that can be passed to a fputcsv function
	 * 	@var array 
	 */
	public $csv_array = null; 



	/**
	 *	if the mysql column names should be included in the header of the CSV
	 *	@var bool
	 */
	public $header = true; 




	public function __construct($rs, $header=true){

		$this->header = $header; 

		parent::__construct($rs);
	}




	/** 
	 *	Converts the mysqli result set into CSV. The delimiter and enclose params are only affective for
	 *	return format "string" or "file". 
	 *	@var return_format string. "array", "string", "file". 
	 *	@var delimiter string. specifies the field separator in the CSV
	 *	@var enclose string. specified the enclosing quote character. Only one char allowd. 
	 * 	@var outhandle handle  In case return_format == "file", a valid file handle needs to be specified. 
	 */
	public function format($return_format="array", $delimiter="\t", $enclose="\"", $outhandle=""){

		//tmp array of mysql result as array rows
		$this->csv_array = array();

		//result to be returned, 
		$output = null; 


		//construct header as first row of array
		if ($this->header){			
			$header = array(); 
			for ( $i = 0; $i < $this->db_result_set->field_count; $i++ )
			{
				$finfo = $this->db_result_set->fetch_field_direct($i);
			    array_push($header,  $finfo->name);
			}

			array_push($this->csv_array, $header);
		}

		//construct rows from db result set
		while( $data_row = $this->db_result_set->fetch_array(MYSQLI_NUM) )
		{
			array_push($this->csv_array, $data_row);
		}


		if ($return_format == "array"){
			$output = $this->csv_array; 
		}


		//want the csv as string? 
		if ($return_format == "string"){
			$memhandle = fopen('php://temp/maxmemory:'. (5*1024*1024), 'r+');

			foreach ($array_data as $row) {
	      			fputcsv($memhandle, $row, $delimiter, $enclose);
		  	}
			
			rewind($memhandle);
			// put it all in a variable
			$output = stream_get_contents($memhandle);
		}


		//of write directly to file. The return value is boolean "true"
		//since the filehandle exists outside of this instance. 
		if ($return_format == "file"){
			if (!$outhandle || $outhandle=="")
	        	throw new Exception("Output format exception: CSV should be written to file but not file handled specified!");
			
			foreach ($data as $row) {
	      			fputcsv($outhandle, $row, $delimiter, $enclose);
		  	}

		  	$output = true; 
		}



		return $output;

	} //end write_format


}



?>