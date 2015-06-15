<?php
require_once(__ROOT__."php/lib/abstract_data_manager.php");

class data_manager extends abstract_data_manager {
    public function db_table(){
        return 'aixada_rev_tax_type';
    }
    public function title(){
        global $Text;
        return $Text['nav_mng_revtax'];
    }
    protected function before_delete($values) {
        global $Text;
        return $this->chk_related($values, $Text['nav_mng_products'],
            "select id from aixada_product where rev_tax_type_id = {id}");
    }
    protected function form_fields(){
        global $Text;
        return "[{
                name:'id',
                editable:false
            }, {
                name:'rev_tax_percent', label:'%',
                width:'80',
                editrules:{required:true, number: true}
            },  {
                name:'name', label:'".$Text['name']."',
                width:'250',
                editrules:{required:true}
            }, {
                name:'description', label:'".$Text['description']."',
                width:'400'
        }]";
    }
}
?>
