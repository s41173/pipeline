<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Contract_model extends Custom_Model
{
    protected $logs;
    
    function __construct()
    {
        parent::__construct();
        $this->logs = new Log_lib();
        $this->com = new Components();
        $this->tableName = $this->com->get_table($this->com->get_id('contract'));
        $this->com = $this->com->get_id('contract');
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
    
    function search($date=null,$cust=null)
    {   
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->cek_null_string($date, 'dates');
        $this->cek_null_string($cust, 'cust_id');
//        $this->cek_null_string($type, 'type');
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
    
    function report($cust=null,$status=null,$period=null,$start=null,$end=null)
    {   
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->cek_nol($status, 'status');
        $this->cek_null($cust, 'cust_id');
        if ($period == 0){ $this->cek_between($start, $end); }
        elseif ($period == 1){ $this->cek_between($start, $end, 'starts'); }
        elseif ($period == 2){ $this->cek_between($start, $end, 'ends'); }
        
        $this->db->order_by('id', 'asc'); 
        return $this->db->get(); 
    }
    
    private function cek_between($start,$end,$type='dates')
    {
        if ($start == null || $end == null ){return null;}
        else { return $this->db->where($this->tableName.".".$type." BETWEEN '".$start."' AND '".$end."'"); }
    }
    
    function counter()
    {
        $this->db->select_max('id');
        $test = $this->db->get($this->tableName)->row_array();
        $userid=$test['id'];
	$userid = intval($userid+1);
	return $userid;
    }
    
    function max_id()
    {
        $this->db->select_max('id');
        $test = $this->db->get($this->tableName)->row_array();
        $userid=$test['id'];
	$userid = intval($userid);
	return $userid;
    }    

}

?>