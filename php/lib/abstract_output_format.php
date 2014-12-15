<?php

require_once(__ROOT__ . 'php/external/FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start(); // Starts FirePHP output buffering
$firephp = FirePHP::getInstance(true);


require_once(__ROOT__ . 'php/lib/exceptions.php');
require_once(__ROOT__ . 'local_config/config.php');




/**
 *
 *	Abstract class for formatting mysqli result sets to different output formats. 
 *	Specific output formats are implemented by subclasses. 
 */
class abstract_output_format{
	

	/**
	 *
	 *	The mysql result set
	 *	@var mysqli_result_set	
	 */
	protected $db_result_set =  null;




	/**
	 *	Expects a result set. the RS might be empty. 
	 * 	@var rs mysqli_result set
	 */
	public function __construct($rs){

		if (!is_a($rs, 'mysqli_result')){
			throw new Exception("Output format exception: supplied result set needs to be of type 'mysqli_result' ");
		}

		$this->db_result_set = $rs; 

	}


	/**
	*
	*	Needs to be overwritten by subclass, according to the specific output format. 
	*/
	public function format(){

	}





	/**
     * 
     * Utility function to convert xml file into into array of csv
     */
	public function xml2csv($xml){
		
	    $fieldnames = array();
	    $csv_rows = array();
	    $tok = strtok($xml, '<>');
	    $expected = 'rowset';
	    if ($tok != $expected)
			throw new XMLParseException($expected, $tok, $xml);
	    $tok = strtok('<>');
	    $first_row = true;
	    while ($tok != '/rowset') {
			$ex = explode(' ', $tok);
			$fieldname = $ex[0];
			$expected = 'row';
			if ($fieldname != $expected)
			    throw new XMLParseException($expected, $fieldname, $xml);
			$tok = strtok('<>');
			$csv_row = array();
			while ($tok != '/row') {
			    $ex = explode(' ', $tok);
			    $fieldname = $ex[0];
			    if ($first_row)
				$fieldnames[] = $fieldname;
			    $tok = strtok('<>');
			    $expected = '![CDATA[';
			    $l_expected = strlen($expected);
			    if (substr($tok, 0, $l_expected) != $expected) 
					throw new XMLParseException($expected, $tok, $xml);
			    $value = substr($tok, $l_expected, strpos($tok, ']]', $l_expected)-$l_expected);
			    $csv_row[] = $value;
			    $tok = strtok('<>');
			    $expected = '/' . $fieldname;
			    if ($tok != $expected)
					throw new XMLParseException($expected, $tok, $xml);
			    $tok = strtok('<>');
			}
			$tok = strtok('<>');
			$first_row = false;
			$csv_rows[] = $csv_row;
	    }    
	    array_unshift($csv_rows, $fieldnames);
	    return $csv_rows;
	}



}


?>