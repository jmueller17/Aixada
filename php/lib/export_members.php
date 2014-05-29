<?php


require_once(__ROOT__ . 'php/lib/abstract_export_manager.php');



class export_members extends abstract_export_manager {
	
	protected $export_only_active = 1; 
	
	
	public function __construct($filename="", $active=1){		
		
		$this->export_table = "members";
		$this->export_only_active = $active; 
		
		parent::__construct($filename);
	
	}
	

	
	protected function read_db_table(){
		$xml_tmp = "";
		
		
		$wherec = ($this->export_only_active)? 'aixada_member.active=1':'';
				
	    $xml_tmp = stored_query_XML_fields('aixada_member_list_all_query', 
						   'aixada_member.uf_id', 
						   'desc', 
						   0, 
						   1000000, 
						   $wherec);
		
		
		$this->xml_result[$this->filename] = $xml_tmp; 
		
		DBWrap::get_instance()->free_next_results(); 
		
	
		
	
	}
	

}


?>