<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Registration_model extends Custom_Model
{
    protected $logs;
    
    function __construct()
    {
        parent::__construct();
        $this->logs = new Log_lib();
        $this->com = new Components();
        $this->tableName = $this->com->get_table($this->com->get_id('registration'));
        $this->com = $this->com->get_id('regisration');
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
    
    function search($type=null,$date=null)
    {   
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->cek_null_string($date, 'DATE(dates)');
        $this->cek_null_string($type, 'type');
//        $this->cek_null_string($docno, 'docno');
        $this->db->order_by('id', 'desc'); 
        return $this->db->get(); 
    }
    
    function get_src_tank(){
        $this->db->select('source_tank');
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->db->order_by('source_tank', 'asc'); 
        $this->db->distinct();
        $val = $this->db->get()->result();
        if ($val){ foreach($val as $row){$data['options'][$row->source_tank] = ucfirst($row->source_tank);} }
        else { $data['options'][''] = '--'; }        
        return $data;
    }
    
     function combo_docno(){
        $this->db->select('docno');
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->db->order_by('docno', 'asc'); 
        $this->db->distinct();
        $val = $this->db->get()->result();
        if ($val){ foreach($val as $row){$data['options'][$row->docno] = strtoupper($row->docno);} }
        else { $data['options'][''] = '--'; }        
        return $data;
    }
    
    function get_pic($field='pic1'){
        $this->db->select($field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->db->order_by($field, 'asc'); 
        $this->db->distinct();
        $val = $this->db->get()->result();
        if ($val){ foreach($val as $row){$data['options'][$row->$field] = ucfirst($row->$field);} }
        else { $data['options'][''] = '--'; }        
        return $data;
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
    
    function report($status=null,$start=null,$end=null)
    {   
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->cek_nol($status, 'type');
        $this->cek_between($start, $end, 'dates');
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