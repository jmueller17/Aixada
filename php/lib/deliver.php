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



	/**
	 *	
	 *	Sends a string back to the client, usually in response to an ajax call. 
	 *	@param string $out_str The output string to be send to the client
	 *	@param string $format The format of the string, i.e. csv, xml, json, etc. 
	 */
	public static function serve_str($out_str, $format){
		
		//see http://stackoverflow.com/questions/22121282/php-inserts-hex-number-of-characters-before-the-content
		//as checked by Xavier M
		$out_str = $out_str . '   '; 

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
	public static function serve_outf_str($output_format){

		//an instance of class output_format
		if (strpos(get_class($output_format), "tput_forma") == 2 ){
			Deliver::serve_str($output_format->format(), $output_format->get_format());
		} else {
			throw new Exception("Deliver exception: server_outf_str $output_format needs to be an instance of class output_format");
		}
	}



	/**
	 *
	 *	Sends a file back to the client, i.e. forces a download. 
	 *	@param string $path_to_file The full path to the file, including file name
	 *	@param string $format The format of the generate file as file ending. 
	 */
	public static function serve_file($path_to_file, $format){

		$filename = basename($path_to_file);

		if (!isset($filename) || $filename == "" || is_null($filename)){
			throw new Exception("Deliver file exception: no filename set!!");
		}

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
	public static function serve_outf_file($output_format){

		//check if we really have an instance of class output_format
		if (strpos(get_class($output_format), "tput_forma") == 2 ){
			Deliver::serve_file($output_format->write_file(), $output_format->get_format());
		} else {
			throw new Exception("Deliver exception: server_outf_file $output_format needs to be an instance of class output_format");
		}

	}


	/**
	 *
	 *	Shortcut function to create and serve zip file via Deliver::create_zip() + Deliver::serve_file()
	 */
	public static function serve_zip($files, $filename="", $destination_folder ="", $overwrite=true){
		$zip = Deliver::create_zip($files, $filename, $destination_folder, $overwrite);
		Deliver::serve_file($zip, "zip");
	}


	/**
	 *
	 *	Utility function to zip several files. Code from http://davidwalsh.name/create-zip-php
	 *	@param array $files Array containing the full paths to the files to be zipped. 
	 *	@param string $filename The name of the zip archive. If empty, tmp file name will be constructed. 
	 *	@param string $destination_folder The full path to the folder where the zip folder should be written. If empty, zip will be written to tmp dir set in config.  
	 *	@param bool $overwrite If true, overwrites any existing zip archive. 
	 *
	 */
	public static function create_zip($files, $filename="", $destination_folder ="", $overwrite=true) {
		

		if (count($files)==0) {
			throw new Exception("Deliver create zip exception: no file names given in $files");
		}

		if(file_exists($destination) && !$overwrite) { 
			return false; 
		}

		//if no filename is given, construct one
    	if ($filename == ""){
			$filename = "AixTmpZip" . mt_rand(100,100000) . "_" .date('Y-m-d_h:i').".zip";    		

    	} else if (pathinfo($filename, PATHINFO_EXTENSION) != 'zip') {
	   	 		$filename = $filename . '.zip';
    	}
    		
    	if ($destination_folder == ""){
    		$destination_folder = __ROOT__ . configuration_vars::get_instance()->private_dir; 
    	}	
    	
    	//get the trailing slash right
    	$destination_folder = rtrim($destination_folder, '/') . '/';

    	//path + filename
    	$path = $destination_folder . $filename; 

		
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
			if($zip->open($path, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
				return false;
			}
		
			//add the files
			foreach($valid_files as $filepath) {
				$file_name = basename($filepath);
				$zip->addFile($filepath,$file_name);
			}
		
			//close the zip -- done!
			$zip->close();
		
			//check to make sure the file exists and send its path
			if (file_exists($path)){
				return $path; 
			} else {
				return false; 
			}
		} else {
			return false;
		}
	}



	/**
	 *	Utility function to create a pdf from an existing file (such as html) or a string directly. 
	 */
	public static function create_pdf($file, $str){


	}


	/**
	 *	@param array $files Array of files containing full file path. 
	 */
	public static function clean_up($files){
		foreach($files as $filepath) {
				@unlink($filepath);
		}
	}


}


?>