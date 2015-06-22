<?php
require_once(__ROOT__."php/lib/abstract_data_manager.php");

class data_manager extends abstract_data_manager {
    public function db_table(){
        return 'aixada_account_desc';
    }
    public function title(){
        return i18n('nav_mng_accdec');
    }
    public function col_sort() {
        return "['id','desc']";
    }
    protected function before_delete($values) {
        return $this->chk_related($values, i18n('mon_accountBalances'),
            "SELECT * FROM aixada_account where account_id = -{id}");
    }
    protected function form_fields(){
        return "[{
                name:'id',
                width:'50', align:'right',
                editable:false
            }, {
                name:'active', label:'".i18n_js('active')."',
                width:'80', align:'center',
                edittype:'checkbox', formatter:'checkbox',
                editoptions:{
                    value:'1:0',
                    defaultValue:'1'
                },
                editrules:{ required:true }
            }, {
                name:'description', label:'".i18n_js('description')."',
                width:'400',
                editoptions:{ size:50, maxlength:50 },
                editrules:{ required:true }
            }, {
                name:'account_type', label:'".i18n_js('type')."',
                width:'100',
                editable:false,
                edittype:'select', formatter:'select', 
                editoptions:{
                    value:'1:".i18n_js('treasury')."; 2:".i18n_js('service')."',
                    defaultValue:'1'
                },
                editrules:{ required:true }
        }]";
    }
}
?>
