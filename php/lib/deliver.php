<?php 


/**
 *	Class that delivers generated output to the client in a variety of formats. It accepts formatted output 
 *	produced by instances of class abstract_output_format, usually a string which then can be saved as file 
 *	on the server, downloaded by the user. In most cases, the produced string (xml or json) will be send back 
 *	directly to the client. 
 *	
 *	Subclasses can implement more complex deliver methods such as email, saving to remote server (google drive) or others
 *
 */
class Deliver {


 	private static $instance = false;


	/** 
	 *	Constructor. Not public. 
	 *
	 */
	private function __construct(){
	}




	/**
   	* The Deliver class is implemented as a Singleton. Call this
   	* function to instantiate it, and not the constructor.
   	*/
  	public static function get_instance(){
    	if (self::$instance === false)
      		self::$instance = new deliver;
    	
    	return self::$instance;
  	}



	/**
	 *	
	 *	Sends a string back to the client. 
	 *	@var string $out_str The output string to be send to the client
	 *	@var string $format The format of the string, i.e. csv, xml, json, etc. 
	 */
	public function serve_str($out_str, $format){

		header('Content-Type: text/'.$format);
		header('Last-Modified: '.date(DATE_RFC822));
		header('Pragma: no-cache');
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: '. date(DATE_RFC822, time() - 3600));
		header('Content-Length: ' . strlen($out_str));
		echo $out_str;

	}


	/**
	 *	Shortcut for serving an output string from the given output_format class. Retrieves
	 *	the formatted string to be send back to the client and its format. 
	 *  @param output_format $output_format Instance of class output_format
	 */
	public function serve_outf_str($output_format){

		//an instance of class output_format
		if (strpos(get_class($output_format), "tput_forma") == 2 ){
			$this->serve_str($output_format->format(), $output_format->get_format());
		} else {
			throw new Exception("Deliver exception: server_outf_str $output_format needs to be an instance of class output_format");
		}
	}



	/**
	 *
	 *	Sends a file back to the client, i.e. forces a download. 
	 *	@var string $path_to_file The full path to the file, including file name
	 *	@var string $format The format of the generate file as file ending. 
	 */
	public function serve_file($path_to_file, $format){

		$filename = basename($path_to_file);

		if ($format == "zip"){
			header('Content-Type: application/zip');
		} else {
			header('Content-Type: text/'.$format); //csv | xml | json | zip ... 			
		}

		header('Content-Disposition: attachment;filename='.$filename);
    	header('Last-Modified: '.date(DATE_RFC822));
		header('Pragma: no-cache');
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: '. date(DATE_RFC822, time() - 3600));
		header("Content-Length: " . filesize($path_to_file));

	    readfile($path_to_file);
	}


	/**
	 *
	 *	Servers a file from the given output_format class. This is a shorthand for 
	 *	retrieving the file from the formatter and the file extension. 
	 *	@param output_format $output_format. 
	 */
	public function serve_outf_file($output_format){

		//check if we really have an instance of class output_format
		if (strpos(get_class($output_format), "tput_forma") == 2 ){
			$this->serve_file($output_format->write_file(), $output_format->get_format());
		} else {
			throw new Exception("Deliver exception: server_outf_file $output_format needs to be an instance of class output_format");
		}

	}





	/**
	 *
	 *	Utility function to zip several files. Code from http://davidwalsh.name/create-zip-php
	 *	@var array $files Array containing the full path to the file to be zipped. 
	 *
	 */
	public static function create_zip($files = array(),$destination = '', $overwrite = false) {
		
		//if the zip file already exists and overwrite is false, return false
		if(file_exists($destination) && !$overwrite) { 
			return false; 
		}
		
		$valid_files = array();
	
		//if files were passed in...
		if(is_array($files)) {
			//cycle through each file
			foreach($files as $file) {
				//make sure the file exists
				if(file_exists($file)) {
					$valid_files[] = $file;
				}
			}
		}
		
		
		//if we have good files...
		if(count($valid_files)) {
		
			//create the archive
			$zip = new ZipArchive();
			if($zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
				return false;
			}
		
			//add the files
			foreach($valid_files as $filepath) {
				$file_name = basename($filepath);
				$zip->addFile($filepath,$file_name);
			}
		

			//close the zip -- done!
			$zip->close();
		
			//check to make sure the file exists
			return file_exists($destination);
		} else {
			return false;
		}
	}



	/**
	 *	Utility function to create a pdf from an existing file (such as html) or a string directly. 
	 */
	public static function create_pdf($file, $str){


	}



}


?>