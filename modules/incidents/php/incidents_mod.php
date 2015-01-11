<?php


require_once(__ROOT__ . "php/lib/aixmodel.php");


class Incident extends Aixmodel {



	public function __construct(){

		parent::__construct("aixada_incident");
	}


	public function read_form_submit(){  

		parent::read_form_submit();

		$ufs = '';

		foreach($_REQUEST['ufs_concerned'] as $uf){
			$ufs .= $uf . ',';
		}
		$ufs = rtrim($ufs, ',');

		$this->arrRow["ufs_concerned"] = $ufs; 
		$this->arrRow["operator_id"] =  get_session_user_id();

		if ($this->debug){
			global $firephp; 
			$firephp->log($this->arrRow, "Retrieved fields and values from form submit for {$this->table}");
		}

		return $this->arrRow; 

	}


	/**
	 *	Creates a new incidents by calling the parent insert() function. In addition, checks the 
	 *	incidents type and if is "email" or "all" is set, will send out the incident as email. 
	 *	
	 */
	public function insert($arrData=array(), $autoinc=true){

		$rv = parent::insert($arrData, $autoinc);

		$cfg = configuration_vars::get_instance(); 


		//new incident with distribution level email or all
		if (($this->arrRow["incident_type_id"] == 2 || $this->arrRow["incident_type_id"] == 4) && $cfg->internet_connection){

			$to = null;
			
			//if an email list has been set, use it 
			if ($cfg->incidents_email_list != ""){
				$to = $cfg->incidents_email_list; 
			}

			$db = DBWrap::get_instance();
			$rs = $db->squery('get_incidents_by_ids', $this->id, 0);
  			$db->free_next_results();
  			
  			$f = new output_format_htmltable($rs);
  			$htmlTable = $f->format_rs();
	
			Deliver_email::send($this->arrRow["subject"], $htmlTable, $to);
		}

		return $rv; 

	}
}


	
?>