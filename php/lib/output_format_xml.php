<?php

require_once(__ROOT__ . 'php/external/FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start(); // Starts FirePHP output buffering
$firephp = FirePHP::getInstance(true);


require_once(__ROOT__ . 'php/lib/abstract_output_format.php');



/** 
 *	Custom class that accepts a mysqli_result and returns a xml String or simplexml doc
 *	
 */
class output_format_xml extends abstract_output_format{


	/**
	 *	the resulting array of csv values that can be passed to a fputcsv function
	 * 	@var array 
	 */
	public $xml_str = null; 



	public function __construct($rs){

		parent::__construct($rs);
	}




	/** 
	 *	Converts the mysqli result set into XML. 
	 * 	@var return_format string Two possibilities "string" | "xmldoc"
	 *	@var rowset_name string is the grouping tag for the dataset, e.g. "billing" or simply rowset
	 *	@var row_name string is the grouping tag for each row of data. 
	 *
	 */
	public function format($return_format="string", $rowset_name="rowset", $row_name="row"){

		//result to be returned, 
		$output = null; 

		if ($rowset_name != ""){
			$this->xml_str = '<'.$rowset_name.'>';
		} else {
			$this->xml_str = '';
		}

	    global $Text;

	    while ($row = $this->db_result_set->fetch_assoc()) {
	    	
	    	if ($row_name != ""){
		        $this->xml_str .= '<'.$row_name;
		        if (isset($row['id'])) 
		            $this->xml_str .= ' id ="' . $row['id'] . '"';
		        $this->xml_str .= '>';
	        }

	        foreach ($row as $field => $value) {
	            if ($field == 'description' and isset($Text[$value])) 
	                $value = $Text[$value];
	            $this->xml_str 
	                .= '<' . $field . '>'
	                . '<![CDATA[' . clean_zeros($value) . ']]></'.$field.'>';
	        }

	        if ($row_name != ""){
		        $this->xml_str .= '</'.$row_name.'>';
		    }
	    }

	    if ($rowset_name != ""){
		    $this->xml_str .= '</'.$rowset_name.'>';
		}


		 //return the plain xml string
		if ($return_format == "string"){
		    $output = $this->xml_str; 
		}


		//take the xml string and parse it. 
		if ($return_format == "xmldoc"){
			$newstr = '<?xml version="1.0" encoding="utf-8"?>' . $this->xml_str;  
			$output=simplexml_load_string($newstr);

		}

		if (!($return_format=="string" || $return_format=="xmldoc"))
			throw new Exception("XML output format exception: specified format {$return_format} is not valid. Accepted formats are 'string' or 'xmldoc'!");

		return $output;

	}




}



?>