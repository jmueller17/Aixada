<?php
require_once("../../lib/data_manager.php");

class cl extends abstract_data_manager {
    protected function db_table() {
        return 'cistella_turn';
    }
    protected function form_fields() {
        return "[{
                name:'id',
                editable:false
            }, {
                name:'date_turn', label:'Data del torn',
                width:'30',
                editrules:{date:true, required:true}
            }, {
                name:'uf_name', label:'Unitat familiar name',
                width:'300',
                editable:false
            }, {
                name:'uf_id', label:'Unitat familiar id',
                
                editrules: {required:true},
                edittype: 'select', editoptions: {
                    dataUrl:'php/ctrl/ManageData_select.php?table=aixada_uf_active'
                }
        }]";
    }
    protected function sql() {
        return
            "select
                t01.id, t01.uf_id, t01.date_turn, t01.ts,
                uf.name as uf_name
            from cistella_turn t01
            left join aixada_uf uf on t01.uf_id=uf.id";
    }
}
$obj = new cl(); // RUN!
?>