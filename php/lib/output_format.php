<?php

require_once(__ROOT__ . 'php/external/FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start(); // Starts FirePHP output buffering
$firephp = FirePHP::getInstance(true);


require_once(__ROOT__ . 'php/lib/exceptions.php');
require_once(__ROOT__ . 'local_config/config.php');




/**
 *
 *	Class for formatting mysqli result sets to different output formats. 
 *	Specific output formats are implemented by subclasses. 
 */
class output_format{
	

	/**
	 *
	 *	The mysql result set
	 *	@var mysqli_result_set	
	 */
	protected $db_result_set =  null;



	/**
	 *
	 *	The database result set transformed into a 2d array, where the first 
	 *	row contains the column headers.  
	 *	@var array 2d array
	 */
	protected $db_data_table = null;



	/**
	 *	
	 *	The resulting output string in the corresponding format (csv, xml, json, ...) produced by the
	 *	subclass implementations. 
	 *	@var string
	 */
	protected $out_str = "";
	


	/** 
	 *	The short name of the format implemented by the subclass. 
	 *	Corresponds basically to the file extension. 
	 *	@var string 
	 *
	 */
	protected $format_short = "";


	/**
	 * 
	 * Directory where temporary files are created. This is read from the config file
	 * @var string
	 */
	private $write_to_folder = ""; 




	/**
	 *	Constructor that expects either a mysqli_result set or a data matrix (2d array) and a file extension of 
	 *	the desired output format.
	 *	The conversion of the mysqli_result or data matrix to different output formats is done by subclasses. 
	 * 	@param mysqli_result|array $data A database or 2d array result set to be formatted. 
	 *	@param string $format_short A string of the file extension of the desired output format such as csv, xml, json, etc. This gets passed by the subclass. 
	 */
	public function __construct($data, $format_short=""){

		if (is_array($data) && count($data) > 0){
			if (is_array($data[0]) && count($data[0]) > 0)
				$this->db_data_table = $data; 
		} else if (is_a($data, 'mysqli_result')){
			$this->db_result_set = $data; 
		} else {
			throw new Exception("Output format exception: supplied data is not array or mysqli_result and/or empty!!");
		}



		$this->format_short = $format_short; 

    	$this->write_to_folder = __ROOT__ . configuration_vars::get_instance()->private_dir; 

	}


	/**
	 *	
	 *	Convenient function to format either the result set or data table if either of both is available. 
	 *	If both are given, the mysqli_result set takes precedence. 
	 */
	public function format(){

		if (!is_null($this->db_result_set)){
			return $this->format_rs();
		} else if (!is_null($this->db_data_table)){
			return $this->format_data_table();
		} else {
			throw new Exception("Output format exception: both data table and mysqli_result is empty. Nothing to format!");
		}
	}

	/**
	*
	*	Needs to be overwritten by subclass, according to the specific output format. 
	*	Converts the result set into target format. 
	*/
	public function format_rs(){}


	/**
	 *	Overwritten by subclass, according to specified output format. 
	 *	Converts the assoc array of the result set into target format. 
	 */
	public function format_data_table(){}


	/**
	 *
	 * 	Subclasses implement the conversion from result set / data_tables to specific 
	 *	output formats. The result of this conversion is saved in the $out_str which can be 
	 *	manually retrieved. Something like the cached version of the format....() method calls. 
	 *	@return string	
	 */
	public function get_formatted_str(){
		return $this->out_str; 
	}



	/**
	 *
	 *	Returns the short name of the format, usually the file extension. To be 
	 *	set when initialized through subclass. 
	 */
	public function get_format(){
		return $this->format_short; 
	}


	/** 
	 *
	 *  Overwrites the data_table of this instance. This is useful when the data_table has been
	 *	manipulated manually and needs to be set before converting it to the final output format. 
	 *	@param array $dt The 2D datatable. 
	 */
	public function set_data_table($dt){
		$this->db_data_table = $dt;
	}


	/**
	 *
	 *	Overwrites the mysqli_result set of this instance. 
	 *	@param mysqli_result $rs The mysql query result set
	 */
	public function set_result_set($rs){
		if (is_a($rs, 'mysqli_result')){
			$this->db_result_set = $rs; 
		} else {
			throw new Exception("Output format exception: set_result_set the given rs is empty");
		}
	}


	/**
	 *	In certain cases, the result set of the query might need modifications. This function
	 *	retrieves the data table representation for the result set of this instance, i.e. an array for easy manipulation. 
	 *	@param bool $header If the database table column names should be included as first row of the array
	 *	@param bool $force_refresh If the data_table will be rebuild even if it already exists
	 *	@return array
	 */
	public function get_data_table($header=true, $force_refresh=false){
	
		if (is_null($this->db_data_table) || $force_refresh){

			//db_data_table is initially null
			$this->db_data_table=array();

			//reset the db result set
			$this->db_result_set->data_seek(0);

			if ($header){
				//construct header as first row of array
				$header = array(); 
				for ( $i = 0; $i < $this->db_result_set->field_count; $i++ )
				{
					$finfo = $this->db_result_set->fetch_field_direct($i);
				    array_push($header,  $finfo->name);
				}

				array_push($this->db_data_table, $header);
			}

			//construct rows from db result set
			while( $data_row = $this->db_result_set->fetch_array(MYSQLI_NUM) )
			{
				array_push($this->db_data_table, $data_row);
			}

		}

		return $this->db_data_table;
	}



	/**
	 * 	Since we can initialize this object with either a mysqli_result set or data table, this function
	 *	checks if we have a mysqli_result set. Does not check if result set is empty. 
	 *	@return bool 
	 */
	public function rs_exists(){
		if (!is_null($this->db_result_set) && is_a($this->db_result_set, 'mysqli_result')){
			return true; 
		} else {
			return false; 
		}
	}



	/** 
	 *	Checks if a data table exists. This does not check if the data table is empty. 
	 *	@param bool
	 */
	public function data_table_exists(){
		if (!is_null($this->db_data_table) && is_array($this->db_data_table)){
			return true;
		} else {
			return false; 
		}
	}



	/**
	 *	Writes a string to a file. If not out_str is provided, it will try to use the already generated one
	 *	or generate it on the fly by classing the format_rs() method. 
	 *	Subclasses can overwrite this method for specific file formats.  
	 *	@param string $out_str Contains a string to be written to the file
	 *	@param string $path The path where the file will be saved
	 *	@param string $filename The name of the file 
	 *	@return string Full path including file name. 
	 *
	 */
	public function write_file($filename="", $path="", $out_str=""){


		if (!is_string($out_str) || !is_string($this->out_str))
				throw new Exception("File write exception in output formatting: Content of file must be string");


		//if $out_str gets passed as parameter it preceedes generated string of this instance
		if (isset($out_str) && $out_str != "") {
			$data = $out_str; 

		//if no string is provided and local string is empty, try to generate it. 
		} else if ($this->out_str == "") {
			$data = $this->out_str = $this->format();

		} else if ($this->out_str != ""){
			$data = $this->out_str; 

		} else {
			throw new Exception("File write exception during output formatting: no data provided!!");	
		}


		//if no filename is given, construct one
    	if ($filename == ""){
			$filename = "AixTmpFile" . mt_rand(100,100000) . "_" .date('Y-m-d_h:i');    		
    	} 

    	if (pathinfo($filename, PATHINFO_EXTENSION) != $this->format_short) {
	   	 		$filename = $filename . ".".$this->format_short;
    	}



    	//if no path is given, use tmp directory from config. 
    	if ($path == ""){
    		$path = $this->write_to_folder; 
    	}

		$publish_filename = $path . $filename; 

		//global $firephp; 
		//$firephp->log($publish_filename, "publish filename");

	  	$outhandle = @fopen($publish_filename, 'w');

	  	if (!$outhandle)
	        	throw new Exception("File write exception during output formatting: could not open {$publish_filename} for writing. Make sure that local_config/tmp_dir is a writable directory");
	  
	        	
	   	fwrite($outhandle, $data);	
	  	  		
		fclose($outhandle);


		if (file_exists($publish_filename)) {
			return $publish_filename;
		} else {
			throw Exception("File write exception in output formatter: could not write file {$publish_filename}!");
		}
	
	} //write_File


}


?>