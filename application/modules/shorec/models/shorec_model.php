<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Shorec_model extends Custom_Model
{
    protected $logs;
    
    function __construct()
    {
        parent::__construct();
        $this->logs = new Log_lib();
        $this->com = new Components();
        $this->tableName = $this->com->get_table($this->com->get_id('shorec'));
        $this->com = $this->com->get_id('shorec');
        $this->field = $this->db->list_fields($this->tableName);
    }
    
    protected $com,$field;
    
    function get_last($limit, $offset=null)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->db->order_by('id', 'desc'); 
        $this->db->limit($limit, $offset);
        return $this->db->get(); 
    }
    
    function search($date=null,$cust=null,$type=null)
    {   
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->cek_null_string($date, 'dates');
        $this->cek_null_string($cust, 'cust_id');
        $this->cek_null_string($type, 'type');
        $this->db->order_by('id', 'desc'); 
        return $this->db->get(); 
    }
        
    function search_list($content=null)
    {   
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->cek_null($content, 'content');
        $this->db->where('status',1);
        $this->db->order_by('sku', 'asc'); 
        return $this->db->get(); 
    }
    
    function get_by_docno($docno)
    {
        $this->db->select($this->field);
        $this->db->where('docno', $docno);
        return $this->db->get($this->tableName);
    }
    
    function report($type=null,$cust=null,$field=null,$start=null,$end=null)
    {   
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->cek_nol($type, 'type');
        $this->cek_null($cust, 'cust_id');
        $this->between($field, $start, $end);
        $this->db->order_by('dates', 'asc'); 
        return $this->db->get(); 
    }

}

?>