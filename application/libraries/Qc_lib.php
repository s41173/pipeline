<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Qc_lib extends Custom_Model {
    
    public function __construct($deleted=NULL)
    {
        $this->deleted = $deleted;
        $this->logs = new Log_lib();
        $this->com = new Components();
        $this->tableName = 'qc_gk';
        $this->com = $this->com->get_id('qc');
        $this->field = $this->db->list_fields($this->tableName);
    }
    
    protected $com,$field,$trans;

    function valid_based_type($regid,$type){
        if ($type == "CARRIAGE"){
            return $this->cek_trans('registration_id', $regid);
        }else{ return TRUE; }
    }
    
 
    function get_last($regid=0)
    {
       $this->db->select($this->field);
       $this->db->where('registration_id', $regid);
       return $this->db->get($this->tableName);
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
        $this->db->select_sum('netto');
        $this->db->select_sum('netto_from');
        $this->db->where('registration_id', $regid);
        return $this->db->get($this->tableName)->row_array();
    }
    
    function valid_based_contract($regid,$contract){
        $this->db->where('registration_id', $regid);
        $this->db->where('contract_id', $contract);
        $query = $this->db->get($this->tableName)->num_rows();
        if ($query > 0){ return FALSE; }else{ return TRUE; }
    }
    
    
}

/* End of file Property.php */