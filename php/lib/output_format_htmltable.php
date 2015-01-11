<?php

require_once(__ROOT__ . 'php/external/FirePHPCore/lib/FirePHPCore/FirePHP.class.php');
ob_start(); // Starts FirePHP output buffering
$firephp = FirePHP::getInstance(true);


require_once(__ROOT__ . 'php/lib/output_format.php');



/** 
 *	Custom class that accepts a mysqli_result and returns a plain HTML table
 *	
 */
class output_format_htmltable extends output_format{



	/**
	 *	if the mysql column names should be included as header into the table
	 *	@var bool $header Should the table use the database columnnames as headers?
	 */
	public $header = true; 



	/**
	 *	Array of attributes to be insert to the table tag, such as for example a CSS style for the class. The 
	 *	entries have to have the form attr=>"value". For example to add a CSS for the table: array('class'=>'mytblcss') 
	 *	@var array $tableattr attribute=>value pairs to be included to the table tag. 
	 */
	public $tableattr; 



	/**
	 *	Numeric array containing the column index to skip when constructing the table. 
	 *	@var array $skipcols 
	 *
	 */
	public $skipcols; 



	public function __construct($data, $tableattr=array(), $skipcols=array(), $header=true){

		$this->header = $header; 

		$this->tableattr = $tableattr; 

		$this->skipcols = $skipcols; 

		parent::__construct($data, "html");
	}




	/** 
	 *
	 *	Converts the mysqli result set into a HTML table. 
	 *	@return string The result set formatted as HTML table.  
	 */
	public function format_rs(){

		if (!$this->rs_exists()){
			throw new Exception("HTML table format mysqli_result exception: result set is null");
		}

		global $Text; 

		$out_str = '<table ';

		//attach attributes to the table tag
		if (count($this->tableattr) > 0){
			foreach($this->tableattr as $attr=>$value){
				$out_str .= ' ' . $attr . '="' . $value . '" ';
			}
		}

		$out_str .= '>';

		if ($this->header){
			$out_str .= '<thead><tr>';

				for ( $i = 0; $i < $this->db_result_set->field_count; $i++ )
				{
					if (in_array($i, $this->skipcols)) continue;

					$finfo = $this->db_result_set->fetch_field_direct($i);
					if (isset($Text[$finfo->name])){
						$finfo->name = $Text[$finfo->name]; 
					}
					$out_str .= '<td>' . $finfo->name . '</td>';
				}

				$out_str .= '</tr></thead>';
		}

		$out_str .= "<tbody>";

		//construct rows from db result set
		while( $data_row = $this->db_result_set->fetch_array(MYSQLI_NUM) )
		{
			$out_str .= '<tr>';
			$colindex = 0; 
			foreach ($data_row as $cell){
				if (in_array($colindex++, $this->skipcols)) continue; 
				$out_str .= '<td>' . $cell . '</td>';
			}
			$out_str .= '</tr>';
		}

		$out_str .= '</tbody></table>';
		
		$this->out_str= $out_str; 

		return $this->out_str; 

	} //end 




	/**
	 *
	 *	Converts the data_table representation to HTML table.  Assumes that a valid 
	 *	data table exists. 
	 */
	public function format_data_table(){

		//construct data table with column names header as first row. 
		$dt = $this->get_data_table();

		$field_names = array_shift($dt);

		$out_str = '<table ';

		//attach attributes to the table tag
		if (count($this->tableattr) > 0){
			foreach($this->tableattr as $attr=>$value){
				$out_str .= ' ' . $attr . '="' . $value . '" ';
			}
		}

		$out_str .= '>';

		if ($this->header){
			global $Text; 
			$out_str .= '<thead><tr>';
			$colindex = 0; 
			foreach ($field_names as $colname)
			{
				if (in_array($colindex++, $this->skipcols)) continue; 
				if (isset($Text[$colname])){
					$colname = $Text[$colname]; 
				}
				$out_str .= '<td>' . $colname . '</td>';
			}

			$out_str .= '</tr></thead>';
		}

		$out_str .= "<tbody>";

		//construct rows from db result set
		foreach($dt as $data_row)
		{
			$out_str .= '<tr>';
			$colindex = 0; 
			foreach ($data_row as $cell){
				if (in_array($colindex++, $this->skipcols)) continue; 
				$out_str .= '<td>' . $cell . '</td>';
			}
			$out_str .= '</tr>';
		}

		$out_str .= '</tbody></table>';
		
		$this->out_str= $out_str; 

		return $this->out_str; 

	}


}



?>