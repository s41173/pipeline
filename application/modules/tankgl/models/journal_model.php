<?php

class Journal_model extends Custom_Model
{   
    function __construct()
    {
        parent::__construct();
        $this->logs = new Log_lib();
        $this->com = new Components();
        $this->tableName = $this->com->get_table($this->com->get_id('tankgl'));
        $this->com = $this->com->get_id('tankgl');
        $this->field = $this->db->list_fields($this->tableName);
    }
    
    protected $field;
    
    function count()
    {
        //method untuk mengembalikan nilai jumlah baris dari database.
        return $this->db->count_all($this->table);
    }
    
    function get_last($limit, $offset=null)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->db->order_by('id', 'desc'); 
        $this->db->limit($limit, $offset);
        return $this->db->get(); 
    }

    function search($no=null,$dates=null,$cust=null)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName);
//        $this->cek_null_string($code, 'doctype');
        $this->cek_null_string($no, 'docno');
        $this->cek_null_string($cust, 'cust_id');
        $this->cek_null_string(picker_split2($dates), 'DATE(dates)');
        $this->db->order_by('dates','asc');
        return $this->db->get(); 
    }
    
    function report($cur=null,$type=null,$start=null,$end=null)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName);
        $this->cek_null($cur, 'currency');
        $this->cek_null($type, 'code');
        $this->between('dates', $start, $end);
        $this->db->where('approved', 1);
        $this->db->order_by('dates','asc');
        return $this->db->get(); 
    }
    
    private function cek_between($start,$end)
    {
        if ($start == null || $end == null ){return null;}
        else { return $this->db->where("dates BETWEEN '".$start."' AND '".$end."'"); }
    }
    
    function counter()
    {
       $this->db->select_max('id');
       $query = $this->db->get($this->tableName)->row_array(); 
       return intval($query['id']); 
    }
    
    function get_transaction($gl=0)
    {
        $this->db->select('id, glid, tank_id, debit, credit, vamount, dirt, ffa, moisture');
        $this->db->from('tank_ledger'); 
        $this->db->where('glid', $gl);
        $this->db->order_by('id', 'asc'); 
        return $this->db->get(); 
    }
    
    function get_glid($id=0)
    {
        $this->db->select('id, glid, tank_id, debit, credit, vamount, dirt, ffa, moisture');
        $this->db->from('tank_ledger'); 
        $this->db->where('id', $id);
        return $this->db->get(); 
    }
    
    function closing_trans(){
        $this->db->truncate('tank_ledger'); 
        $this->db->truncate('tank_balances'); 
    }
    
}

?>