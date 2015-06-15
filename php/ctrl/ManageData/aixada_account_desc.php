<?php
require_once(__ROOT__."php/lib/abstract_data_manager.php");

class data_manager extends abstract_data_manager {
    public function db_table(){
        return 'aixada_account_desc';
    }
    public function title(){
        global $Text;
        return $Text['nav_mng_accdec'];
    }
    protected function form_fields(){
        global $Text;
        // See reference: http://www.trirand.com/jqgridwiki/doku.php?id=wiki:common_rules
        return "[{
                name:'id',
                width:'50',
                editable:false
            }, {
                name:'description', label:'".$Text['description']."',
                width:'400',
                editrules:{required:true},
            }, {
                name:'account_type', label:'".$Text['type']."',
                width:'100',
                editrules:{required:true,defaultValue:1, number: true}
            }, {
                name:'active', label:'".$Text['active']."',
                width:'100',
                edittype: 'checkbox',
                editrules:{required:true}
        }]";
    }
}
/*


aixada_account_desc (
  id            smallint    not null auto_increment,
  description   varchar(50) not null,
  account_type  tinyint     default 1, -- 1:treasury, 2:service
  active        tinyint     default 1,
  
  */
?>
