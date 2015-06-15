<?php
require_once(__ROOT__."php/lib/abstract_data_manager.php");

class data_manager extends abstract_data_manager {
    public function db_table(){
        return 'aixada_iva_type';
    }
    public function title(){
        global $Text;
        return $Text['nav_mng_iva'];
    }
    protected function before_delete($values) {
        global $Text;
        return $this->chk_related($values, $Text['nav_mng_products'],
            "select id from aixada_product where iva_percent_id = {id}");
    }
    protected function form_fields(){
        global $Text;
        return "[{
                name:'id',
                editable:false
            }, {
                name:'percent', label:'%',
                width:'90',
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
