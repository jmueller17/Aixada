<?php
require_once(__ROOT__."php/lib/abstract_data_manager.php");

class data_manager extends abstract_data_manager {
    public function db_table(){
        return 'aixada_stock_movement_type';
    }
    public function title(){
        global $Text;
        return $Text['nav_mng_movtype'];
    }
    protected function allow_modes(){
        return "{edit:true,add:false,del:false}";
    }
    protected function form_fields(){
        global $Text;
        return "[{
            name:'id',
            editable:false
        }, {
            name:'name', label:'".$Text['name']."',
            width:'250',
            editrules:{required:true}
        }, {
            name:'description', label:'".$Text['description']."',
            width:'600'
    }]";
    }
}
?>
