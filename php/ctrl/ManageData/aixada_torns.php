<?php
require_once(__ROOT__."php/lib/abstract_data_manager.php");

class data_manager extends abstract_data_manager {
    public function db_table() {
        return 'aixada_torns';
    }
    public function title(){
        global $Text;
        return $Text['torns'];
    }
    public function col_sort() {
        return "['dataTorn','desc']";
    }
    protected function form_fields() {
        return "[{
                name:'id',
                editable:false
            }, {
                name:'dataTorn', label:'" . i18n_js('data_torn_curt') . "',
                width:'120', align:'center',
                editrules:{date:true, required:true}
            }, {
                name:'ufTorn', label:'" . i18n_js('uf_long') . "',
                width:'350',
                editrules: {required:true,edithidden:true,searchhidden:true},
                edittype: 'select', formatter: 'select',
                editoptions: {
                    value:".$this->get_select_values(
                        "select uf.id, CONCAT( uf.name, ' #', uf.id)
                            from aixada_uf uf where uf.active=1
                            order by uf.name",
                        'sel_uf'
                    )."
                }
        }]";
    }
    function get_select_values($strSQL, $desc_blank_value='(...)') {
        $values = '';
        if ($desc_blank_value) {
            $values .= ",'':'".i18n_js($desc_blank_value)."'";
        }
        $rs = DBWrap::get_instance()->Execute($strSQL);
        while ($row = $rs->fetch_array()) {
            $values .= ",{$row[0]}:'".to_js_str($row[1])."'";
        }
        return "{".($values !== ''? substr($values,1) : '')."}";
    }
}
?>
