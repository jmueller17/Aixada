<?php 



/**
 *	Base class for all aixada objects, usually referring to the different database tables like products, members, etc. 
 *
 */
class aixmodel {
	

	/**
	 * 	@var string $table Name of the aixada database table the instance refers to 
	 */
	public $table; 

	
	/**
	 *	@var array $fields An array containing the database field names
	 */
	public $fields; 


	/**
	 *	@var array $arrRow An assoc array where each entry contains $field_name => $value pair
	 *
	 */
	protected $arrRow; 



	/**
	 *	@var int The ID (in the database) of the last insert or current instance
	 */
	private $id;




	/** 
	 *	Base object of all aixada models. Expects the name of the database table the instance represents. Optionally
	 *	can receive an array of database field names. If none is provided, tries to read the field names from
	 *	the automatically generated file "col_names.php"
	 *
	 *	@param string $table The exact name of the table in the database
	 *	@param array $fields The exact matches of the field names in the database. If empty, read automatically from col_names.php
	 */
	public function __construct($table="", $fields=array()){

    	$this->debug = configuration_vars::get_instance(); 

		$this->table = $table;  
		$this->fields = $fields;
		$this->arrRow = array(); 

		//get fields for this table from automatically generated file (via "make")
		if ($this->table != "" && count($this->fields)==0){
			$all_col_names = unserialize(file_get_contents(__ROOT__ . 'col_names.php'));

	    	if (array_key_exists($this->table, $all_col_names)) {
			   $this->fields = $all_col_names[$this->table];
		    }
	     	
		} else {
			if ($this->debug){
				global $firephp; 
				$firephp->log($this->table, "Warning: aixmodel initalized with empty fields!!");
			}
		}

		if ($this->debug){
				global $firephp; 
				$firephp->log($this->fields, "Aixmodel initialized fields.");
		}


	}



	/**
	 *
	 *	Retrieves the submitted values by the URL/form if there is an exact match between submitted form names
	 *	and database field names. Also, no default values are given if a field is missing. If a field name cannot
	 *	be found in the REQUEST object, it will be skipped. Hence, $arrRow does not necessarily contain all fields. 
	 */
	public function read_form_submit(){

		$this->arrRow = array(); 

		foreach($this->fields as $fname){
			if (isset($_REQUEST[$fname])  && $_REQUEST[$fname] != 'undefined' )
				$this->arrRow[$fname] = $_REQUEST[$fname];
		}

		return $this->arrRow; 
	}


	/**
	 *
	 *	Returns the current database id, e.g. when new entry has been created or updated. In case of deletion
	 *	contains id of last delete entry. 
	 */
	public function get_id(){
		return $this->id; 
	}
	


   	/**
     * Inserts arbitrary columns into a table based on the default field names. 
     *
     * @param array $arrData an array with entries of the form field => value. The values are inserted into the corresponding fields of the table.
     * @param bool $autoinc If set to 1, the id fields will be ignored and not inserted; this supposes that the db field will auto_increment
     *
     */
  	public function insert ($arrData=array(), $autoinc=true){

  		if ($this->table == "")
  			throw new InternalExcetion("Aixmodel table insert exception: no table name given!");

  		if (count($arrData)>0){

  		} else if (count($this->arrRow)>0) {
  			$arrData = $this->arrRow; 
  		} else {
  			throw new Exception("Aixmodel table insert exception: no data provided!");
  		}	


  		//construct the array to be passed to db->execute. First param is table name. 
  		//overall form is:  "insert into :1q (:2q, :3q) values (:4q, :5q);
      	$bind = array($this->table);
      	
	    $strSQL = 'INSERT INTO :1 (';
	     
	    $first = true;
	    $bc = 2; //argument counter
	    //we loop two times since we don't know how many fields are good and the order of 
	    //arguments and values needs to be the same. 
	    foreach ($arrData as $field => $value) {
	    	
	    	if (strtolower($field) == "id" && $autoinc) continue; 

			if (in_array($field, $this->fields)) {
			    if ($first) {
			    	$first = false; 					
			    } else {
			    	$strSQL .= ', ';
			    }

			    $strSQL .= ':'.$bc;
			    $bind[] = $field; 
			    $bc++;
			} else {
				if ($this->debug){
					global $firephp; 
					$firephp->log($field, "Warning: Field {$field} not found in table " . $this->table . ". Values will be ignored!");
				}

			}
	    }
	    
	    $strSQL .= ') values (';
	    $first = true; 
		foreach ($arrData as $field => $value) {
			if (in_array($field, $this->fields)) {
				
				//ig
				if (strtolower($field) == "id" && $autoinc) continue; 

			    if ($first) {
					$first = false; 
			    } else {
			    	$strSQL .= ', ';
			    }

			    $strSQL .= ':'.$bc.'q';
			    $bind[] = $value; 
			    $bc++;
			} else {
				if ($this->debug){
					global $firephp; 
					$firephp->log($field, "Warning: Field {$field} not found in table " . $this->table . ". Values will be ignored!");
				}

			}
	    }

	    $strSQL .= ');';

		//execute needs the sql string as first argument. 
		array_unshift($bind, $strSQL);

		
		if ($this->debug){
			global $firephp; 
			$firephp->log($bind, "Array passed to db->execute() in aixmodel insert ");
		}

		$db = DBWrap::get_instance();
		
	    if ($db->Execute($bind)){
	    	$this->id = $db->last_insert_id();
	    	return 1;
	    } else {
	    	return 0; 
	    }
  	}




  	/**
   	 * Generic edit/update function. 
   	 * @param array $arrData the array that contains the data to be updated must contain a field named 'id' that contains the unique id.
   	 */ 
  	public function edit($arrData){

    	if ($this->table == "")
  			throw new InternalException("Aixmodel table edit exception: no table name given!");

      	if (!array_key_exists('id', $arrData))
			throw new InternalException('Edit: Update array ' . $arrData . ' for table ' . $this->table . ' does not contain a field named "id"');
      
		$this->id = $arrData['id']; 

		$bind = array($this->table);
      	$strSQL = 'UPDATE :1 SET ';


	    $first = true;
	    $bc = 2; 

	    foreach ($arrData as $field => $value) {
			if ($field != 'id' and in_array($field, $this->fields)) {
		   		if ($first) { 
		   			$first = false; 
		   		} else {
		   			$strSQL .= ',';
		   		}
		      	
		      	$strSQL .= ':'. $bc++ .  "=:" . $bc++ . "q";

		      	$bind[] = $field; 
		      	$bind[] = $value; 
		  	}
	    }

	    $strSQL .= ' WHERE id=:' .$bc . ';';
	    $bind[] = $this->id; 
      
      	array_unshift($bind, $strSQL);

      	if ($this->debug){
			global $firephp; 
			$firephp->log($bind, "Array passed to db->execute() in aixmodel update ");
		}

 	    $db = DBWrap::get_instance();
      	return $db->Execute($bind);
    
	}



	/**
	 *
	 *	Simple delete operation which works on rows that have no foreign key constraints. 
	 *	Most of the times, this needs to be overwritten by subclasses that respect deletion of dependent rows. 
	 */
	public function delete($id)
	{
		if (isset($id) && $id > 0){
			$this->id = $id; 
		} else if (isset($this->id) && $this->id > 0 ){

		} else {
			throw new Exception("Aixmode delete exception: no ID given");
		}

		if ($this->table == "")
  			throw new InternalException("Aixmodel delete exception: no table name given!");


		$strSQL = "DELETE from :1 where id=:2;";

		$bind = array($strSQL, $this->table, $this->id);

 		$db = DBWrap::get_instance();
      	return $db->Execute($bind);

	}


} //end class aixmodel



?>