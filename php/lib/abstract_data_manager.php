<?php
require_once(__ROOT__ . "local_config/config.php");
require_once(__ROOT__ . "php/inc/database.php");
require_once(__ROOT__ . "php/utilities/general.php");
/**
 * 
 * Abstract class to handle ....
 */
abstract class abstract_data_manager {
    public function __construct() {
    }
    // To define in each subclass
    abstract public function db_table();
    abstract protected function form_fields();
    // Default public funtions that can be overwritten
    public function title(){
        return $this->db_table();
    }
    public function id_name(){
        return "id";
    }
    public function col_sort() {
        return "null";
    }
    public function callCreateEditorJs() {
        return "$(function (){createEditor(
                    {$this->allow_modes()},
                    {$this->form_fields()},
                    {$this->col_sort()});});";
    }
    // Default internal funtions that can be overwritten
    protected function allow_modes(){
        return "{edit:true,add:true,del:true}";
    }
    protected function sql(){
        return "select * from {$this->db_table()}";
    }

    // Transactions CUD
    // Insert
    public function insert($values) {
        
        if ($this->before_insert($values) === false) {
            exit;
        }
        // Transaction
        $db = DBWrap::get_instance();
        $this->start_transaction($db);
        try {
            $p = $values;
            $p['table'] = $this->db_table();
            $db->Insert($p);
            $new_id = $db->last_insert_id();
        } catch (Exception $e) {
            $this->rollback($db);
            header('HTTP/1.0 401 ' . $e->getMessage());
            die ($e->getMessage());
        }
        $this->after_insert($new_id, $values);
        $this->commit($db);
        
        return $new_id;
    }
    protected function before_insert($values) {
        return true;
    }
    protected function after_insert($new_id, $values) {
    }
    // Update
    public function update($values) {
        
        if ($this->before_update($values) === false) {
            exit;
        }
        // Transaction
        $db = DBWrap::get_instance();
        $this->start_transaction($db);
        try {
            $p = $values;
            $p['table'] = $this->db_table();
            $db->Update($p);
        } catch (Exception $e) {
            $this->rollback($db);
            header('HTTP/1.0 401 ' . $e->getMessage());
            die ($e->getMessage());
        }
        $this->after_update($values);
        $this->commit($db);
                
        return '1';
    }
    protected function before_update($values) {
        return true;
    }
    protected function after_update($values) {
    }
    // delete
    public function get_key($values) {
        return $values[$this->id_name()];
    }
    public function delete($values) {
        if ($this->before_delete($values) === false) {
            exit;
        }
        // Transaction
        $db = DBWrap::get_instance();
        $this->start_transaction($db);
        try {
            $db->Delete($this->db_table(), $this->get_key($values));
        } catch (Exception $e) {
            $this->rollback($db);
            header('HTTP/1.0 401 ' . $e->getMessage());
            die ($e->getMessage());
        }
        $this->after_delete($values);
        $this->commit($db);

        return '1';
    }
    protected function before_delete($values) {
        return true;
    }
    protected function after_delete($values) {
    }
    protected function chk_related($values, $related_title, $sql) {
        $id = $this->get_key($values);
        if ( get_row_query(str_replace('{id}', $id, $sql)) ) {
            throw new Exception(
                i18n('dataman_err_related', array('related'=>$related_title))
            );  
        }
        return true;
    }
    // transactions calls
    protected function start_transaction($db) {
        return $db->Execute("START TRANSACTION;");
    }
    protected function commit($db) {
        return $db->Execute("COMMIT;");
    }
    protected function rollback($db) {
        return $db->Execute("ROLLBACK;");
    }

    // Select
    public function select($params) {
        $req_page = $params['page'];
        $limit = $params['rows'];
        $req_sidx = $params['sidx'];
        $req_sord = $params['sord'];
        /*
          if ($filter_str != '') {
            $strSQL .= ' WHERE ' . $filter_str;
          } */
        $db = DBWrap::get_instance();
        $sql = $this->sql();
        $sql .= ' order by '.$req_sidx.' '.$req_sord;
        if ($req_page != -1 && $limit != 0) {
            $aux_row = get_row_query(
                "SELECT COUNT(*) AS count FROM {$this->db_table()}");
            $total_entries = $aux_row[0];
            list($start, $total_pages) = 
                $db->calculate_page_limits(
                    $total_entries, $req_page, $limit);
            $sql .= ' LIMIT '.$start.', '.$limit;
        }
        $rs = $db->Execute($sql);
        $of = new output_formatter();
        return $of->rowset_to_jqGrid_XML(
            $rs, $total_entries, $req_page, $limit, $total_pages);
    }
}
?>
