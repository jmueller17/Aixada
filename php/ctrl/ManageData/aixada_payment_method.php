<?php
require_once(__ROOT__."php/lib/abstract_data_manager.php");

class data_manager extends abstract_data_manager {
    public function db_table(){
        return 'aixada_payment_method';
    }
    public function title(){
        global $Text;
        return $Text['nav_mng_paymeth'];
    }
    protected function allow_modes(){
        return "{edit:true,add:false,del:false}";
    }
    protected function form_fields(){
        global $Text;
        return "[{
                name:'id',
                width:'50',
                editable:false
            }, {
                name:'description', label:'".$Text['name']."',
                width:'250',
                editrules:{required:true}
            },  {
                name:'details', label:'".$Text['description']."',
                width:'600'
            }]";
    }
}
?>
