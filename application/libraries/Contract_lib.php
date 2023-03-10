<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contract_lib extends Custom_Model{

    public function __construct($deleted=NULL)
    {
        $this->deleted = $deleted;
        $this->logs = new Log_lib();
        $this->com = new Components();
        $this->tableName = 'contract_item';
        $this->com = $this->com->get_id('registration');
        $this->field = $this->db->list_fields($this->tableName);
    }

    protected $field;
    

    function get_last($regid=0){
      $this->db->select($this->field);
      $this->db->where('registration_id', $regid);
      return $this->db->get($this->tableName)->result();
    }
    
    function get_contract_combo($regid=null){
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
//        $this->db->where('registration_id', $regid);
        $this->cek_null($regid, 'registration_id');
        $this->db->order_by('origin_no', 'asc'); 
        $val = $this->db->get()->result();
        if ($val){ foreach($val as $row){$data['options'][$row->id] = strtoupper($row->origin_no);} }
        else { $data['options'][''] = '--'; }        
        return $data;
    }
    
    function cek_contract_amount($regid)
    {
        $this->db->select_sum('transfer_amount');
        $this->db->where('registration_id', $regid);
        $query = $this->db->get($this->tableName)->row_array();
        return $query['transfer_amount'];
    }
    
    function cleaning($uid){
       $this->db->where('registration_id', $uid);
       return $this->db->delete($this->tableName);
    }

    function get_details($contract)
    {
        $this->db->select($this->field);
        $this->db->where('id', $contract);
        $query = $this->db->get($this->tableName)->row();
        return $query;
    }
    
    function get_contract_customer($contract)
    {
        $this->db->select('id, cust_id');
        $this->db->where('id', $contract);
        $query = $this->db->get($this->tableName)->row();
        if ($query){ return $query->cust_id; }
        
    }

    function update_balance($no, $amount,$type=0)
    {
        if ($this->cek_approval_contract($no) == TRUE)
        {
           $balance = $this->get_contract_details($no);
           $balance = $balance->balance;
        
           if ($type == 0){ $balance = intval($balance-$amount); }else { $balance = intval($balance+$amount); }
        
           $value = array('balance' => $balance);
           $this->db->where('id', $no);
           $this->db->update($this->tableName, $value); 
           $balance = null;
           
           // update status
           $balance = $this->get_contract_details($no);
           $balance = $balance->balance;
           if ($balance <= 0){ $stts = 1; }else { $stts = 0; }
           
           $value1 = array('status' => $stts);
           $this->db->where('id', $no);
           return $this->db->update($this->tableName, $value1); 
           
        }else{ return FALSE; }  
    }

    function cek_relation($id,$type)
    {
       $this->db->where($type, $id);
       $query = $this->db->get('sales')->num_rows();
       if ($query > 0) { return FALSE; } else { return TRUE; }
    } 
    
    function cek_relation_contract_type($id,$type)
    {
       $this->db->where($type, $id);
       $query = $this->db->get($this->tableName)->num_rows();
       if ($query > 0) { return FALSE; } else { return TRUE; }
    } 
    
    function set_picking_truck($id,$truckid){
        $value1 = array('picking_truck_id' => $truckid);
        $this->db->where('id', $id);
        return $this->db->update($this->tableName, $value1); 
    }
    
    function get_picking_truck($id){
        $this->db->select($this->field);
        $this->db->where('id', $id);
        $query = $this->db->get($this->tableName)->row();
        return $query->picking_truck_id;
    }
    
    function summary($regid){
        $this->db->select_sum('contract_amount');
        $this->db->select_sum('outstanding_amount');
        $this->db->select_sum('transfer_amount');
        $this->db->select_sum('netto_from');
        $this->db->where('registration_id', $regid);
        return $this->db->get($this->tableName)->row_array();
    }
    
    function valid_based_reg($origin,$regid){
        $this->db->select($this->field);
        $this->db->where('registration_id', $regid); 
        $this->db->where('origin_no', $origin); 
        $this->db->where('deleted', $this->deleted);
        $query = $this->db->get($this->tableName)->num_rows();
        if ($query > 0){ return FALSE; }else{ return TRUE; }
    }
    
}

/* End of file Property.php */