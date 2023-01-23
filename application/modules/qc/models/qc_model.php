<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Qc_model extends Custom_Model
{
    protected $logs;
    
    function __construct()
    {
        parent::__construct();
        $this->logs = new Log_lib();
        $this->com = new Components();
        $this->tableName = $this->com->get_table($this->com->get_id('qc'));
        $this->com = $this->com->get_id('qc');
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
    
    function search($regid=null,$origin=null)
    {   
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->cek_null_string($regid, 'registration_id');
        $this->cek_null_string($origin, 'contract_id');
//        $this->cek_null_string($type, 'type');
        $this->db->order_by('id', 'desc'); 
        return $this->db->get(); 
    }
    
    function get_by_registration($register)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->db->where('registration_id', $register);
        $this->db->order_by('id', 'asc'); 
        return $this->db->get(); 
    }
    
    function get_supplier(){
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->db->order_by('supplier', 'asc'); 
        $this->db->distinct();
        $val = $this->db->get()->result();
        if ($val){ foreach($val as $row){$data['options'][$row->supplier] = strtoupper($row->supplier);} }
        else { $data['options'][''] = '--'; }        
        return $data;
    }
             
    function report($start=null,$end=null)
    {   
        $this->db->select('reg.id as id, reg.code, reg.docno, reg.dates, reg.type, reg.validation, reg.approved, reg.qc_status, '
                . 'contract.origin_no,'
                . 'qc.supplier, qc.gk_no, qc.ffa, qc.moist, qc.imp, qc.description');
        
        $this->db->from('registration as reg, qc_gk as qc, contract_item as contract');
        $this->db->where('reg.id = qc.registration_id');
        $this->db->where('contract.id = qc.contract_id');
        $this->db->where('reg.deleted', $this->deleted);
//        $this->cek_nol($status, 'status');
//        $this->cek_null($cust, 'cust_id');
        $this->between('reg.dates', $start, $end);
        $this->db->order_by('id', 'asc'); 
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

}

?>