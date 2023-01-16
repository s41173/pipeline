<?php

class Sounding_model extends Custom_Model
{   
    function __construct()
    {
        parent::__construct();
        $this->logs = new Log_lib();
        $this->com = new Components();
        $this->tableName = $this->com->get_table($this->com->get_id('sounding'));
        $this->com = $this->com->get_id('sounding');
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

    function search($no=null,$dates=null)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName);
//        $this->cek_null_string($code, 'doctype');
        $this->cek_null_string($no, 'docno');
        $this->cek_null_string(picker_split2($dates), 'DATE(dates)');
        $this->db->order_by('dates','asc');
        return $this->db->get(); 
    }
    
    function report($cur=null,$tank=null,$start=null,$end=null)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName);
        $this->cek_null($cur, 'currency');
        $this->cek_null($tank, 'tank_id');
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
    
}

?>