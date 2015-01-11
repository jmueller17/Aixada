<?php

require_once(__ROOT__ . 'php/external/FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start(); // Starts FirePHP output buffering
$firephp = FirePHP::getInstance(true);


require_once(__ROOT__ . 'php/lib/output_format.php');



/** 
 *	Custom class that accepts a mysqli_result and returns a xml String or simplexml doc
 *	
 */
class output_format_xml extends output_format{


	/** 
	 * 	Should the xml string have a XML header? 
	 *	@var bool
	 */
	public $xml_header = true; 


	/**
	 *	The xml header used
	 *	@var string
	 */
	public $xml_header_str = '<?xml version="1.0" encoding="utf-8"?>';




	public function __construct($data){

		parent::__construct($data, "xml");
	}




	/** 
	 *	Converts the mysqli result set into XML. 
	 * 	@param bool $xml_header Decides if the xml string gets a valid xml header prepended. 
	 *	@param string $rowset_name is the grouping tag for the dataset, e.g. "billing" or simply rowset
	 *	@param string $row_name is the grouping tag for each row of data. 
	 *	@return string The formated XML string. 
	 *
	 */
	public function format_rs($xml_header=true, $rowset_name="rowset", $row_name="row"){

		//include the xml header? 
		$this->xml_header = $xml_header; 

		if ($this->xml_header){
			$xml_str = $this->xml_header_str;
		} else {
			$xml_str = '';
		}

		if ($rowset_name != ""){
			$xml_str .= '<'.$rowset_name.'>';
		} 

	    global $Text;

	    while ($row = $this->db_result_set->fetch_assoc()) {
	    	
	    	if ($row_name != ""){
		        $xml_str .= '<'.$row_name;
		        if (isset($row['id'])) 
		            $xml_str .= ' id ="' . $row['id'] . '"';
		        $xml_str .= '>';
	        }

	        foreach ($row as $field => $value) {
	            if ($field == 'description' and isset($Text[$value])) 
	                $value = $Text[$value];
	            $xml_str 
	                .= '<' . $field . '>'
	                . '<![CDATA[' . clean_zeros($value) . ']]></'.$field.'>';
	        }

	        if ($row_name != ""){
		        $xml_str .= '</'.$row_name.'>';
		    }
	    }

	    if ($rowset_name != ""){
		    $xml_str .= '</'.$rowset_name.'>';
		}


		$this->out_str = $xml_str; 
		return $this->out_str; 

	}




	/**
	 *	Converts a data_base table to xml string. 
	 *
	 */
	public function format_data_table($xml_header=true, $rowset_name="rowset", $row_name="row"){

		//construct data table with column names header as first row. 
		$dt = $this->get_data_table();

		$field_names = array_shift($dt);

		//is there an id field contained? 
		$id_index = array_search('id', $field_names);


		//include the xml header? 
		$this->header = $header; 

		if ($this->header){
			$xml_str = $this->xml_header_str;
		} else {
			$xml_str = '';
		}

		if ($rowset_name != ""){
			$xml_str .= '<'.$rowset_name.'>';
		} 

	    global $Text;

	    foreach($dt as $row){
	    	
	    	if ($row_name != ""){
	    		
		        $xml_str .= '<'.$row_name;
		        if (is_numeric($id_index) && $id_index >=0 && $id_index > count($row)){ 
		            $xml_str .= ' id ="' . $row[$id_index] . '"';
		        }
		        $xml_str .= '>';
	        }

	        for ($i=0; $i<count($row); $i++) {
	        	$field = $field_names[$i];
	        	$value = $row[$i];

	            if ($field == 'description' and isset($Text[$value])) 
	                $value = $Text[$value];
	            $xml_str 
	                .= '<' . $field . '>'
	                . '<![CDATA[' . clean_zeros($value) . ']]></'.$field.'>';
	        }

	        if ($row_name != ""){
		        $xml_str .= '</'.$row_name.'>';
		    }
	    }

	    if ($rowset_name != ""){
		    $xml_str .= '</'.$rowset_name.'>';
		}


		$this->out_str = $xml_str; 
		return $this->out_str; 



	}



	/**
	 *
	 *	Returns a parsed XML doc of the string. 
	 */
	public function getXMLDoc(){

		if (!$this->header){
			$newstr = $this->xml_header_str . $this->out_str; 
			return simplexml_load_string($newstr);
		} else {
			return simplexml_load_string($this->out_str);
		}

	}



	/**
     * 
     * Function to convert xml file into into array of csv
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