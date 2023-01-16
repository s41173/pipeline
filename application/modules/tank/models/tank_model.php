<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Tank_model extends Custom_Model
{
    protected $logs;
    
    function __construct()
    {
        parent::__construct();
        $this->logs = new Log_lib();
        $this->com = new Components();
        $this->tableName = $this->com->get_table($this->com->get_id('tank'));
        $this->com = $this->com->get_id('tank');
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
    
    function search($sku=null,$publish=null)
    {   
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->cek_null_string($sku, 'sku');
        $this->cek_null_string($publish, 'status');
        $this->db->order_by('sku', 'asc'); 
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
    
    function report($cat=null,$manufacture=null)
    {   
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->cek_null($cat, 'category');
        $this->cek_null($manufacture, 'manufacture');
        
        $this->db->order_by('name', 'asc'); 
        return $this->db->get(); 
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
    
    function closing_trans(){
        $this->db->truncate('tank'); 
        $this->db->truncate('tank_ledger');
        $this->db->truncate('tank_transaction');
    }
    
    function combo_content()
    {
        $this->db->select('content');
        $this->db->distinct();
        $this->db->where('deleted', $this->deleted);
        if ($this->db->get($this->tableName)->num_rows()>0){
            $val = $this->db->get($this->tableName)->result();
            foreach($val as $row){$data['options'][$row->content] = strtoupper($row->content);}
        }else{ $data['options']['CPO'] = strtoupper('CPO'); }
        return $data;
    }
    

}

?>