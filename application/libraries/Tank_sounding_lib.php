<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tank_sounding_lib extends Custom_Model {
    
    public function __construct($deleted=NULL)
    {
        $this->deleted = $deleted;
        $this->logs = new Log_lib();
        $this->com = new Components();
        $this->tableName = 'tank_sounding';
        $this->com = $this->com->get_id('registration');
        $this->field = $this->db->list_fields($this->tableName);
    }
    
    protected $com,$field,$trans;

    function cek_relation($id,$type)
    {
       $this->db->where($type, $id);
       $query = $this->db->get($this->tableName)->num_rows();
       if ($query > 0) { return FALSE; } else { return TRUE; }
    }
 
    function get_last($regid=0)
    {
       $this->db->select($this->field);
       $this->db->where('registration_id', $regid);
       return $this->db->get($this->tableName);
    } 
    
    // 0 = BEFORE -- 1 = AFTER
    function get_by_period($regid=0,$period=0){
       $this->db->select($this->field);
       $this->db->where('registration_id', $regid);
       if ($period == 0){ $this->db->where('type', 'BEFORE'); }
       elseif ($period == 1){ $this->db->where('type', 'AFTER'); }
       return $this->db->get($this->tableName)->row();
    }
    
    function count_row_based_registration($regid){
       $this->db->select($this->field);
       $this->db->where('registration_id', $regid);
       $val =  $this->db->get($this->tableName)->num_rows();
       if ($val == 2){ return TRUE; }else{ return FALSE; }
    }
    
    function cleaning($uid){
       $this->db->where('registration_id', $uid);
       return $this->db->delete($this->tableName);
    }
    
    function create($data){
      if ($this->cek_type($data['registration_id'], $data['periodtype']) == TRUE){
         return $this->fill($data);
      }else{
         return $this->edit($data);
      }  
    }
    
    private function fill($data){
        if ($data['type'] == 'SOURCE'){
           $trans = array('source_cm' => $data['sounding'],
                          'source_temp' => $data['temp'],
                          'source_tonase' => $data['tonase'],
                          'registration_id' => $data['registration_id'],
                          'created' => date('Y-m-d H:i:s'),
                          'type' => $data['periodtype']); 
       }
       elseif ($data['type'] == 'DEST'){
           $trans = array('to_cm' => $data['sounding'],
                          'to_temp' => $data['temp'],
                          'to_tonase' => $data['tonase'],
                          'registration_id' => $data['registration_id'],
                          'created' => date('Y-m-d H:i:s'),
                          'type' => $data['periodtype']); 
       }
       return $this->add($trans);
    }
    
    private function edit($data){
        
       if ($data['type'] == 'SOURCE'){
           $trans = array('source_cm' => $data['sounding'],
                          'source_temp' => $data['temp'],
                          'source_tonase' => $data['tonase']); 
       }
       elseif ($data['type'] == 'DEST'){
           $trans = array('to_cm' => $data['sounding'],
                          'to_temp' => $data['temp'],
                          'to_tonase' => $data['tonase']); 
       }
       
       $this->db->where('registration_id', $data['registration_id']);
       $this->db->where('type', $data['periodtype']);
       return $this->db->update($this->tableName, $trans); 
    }
    
    private function cek_type($regid,$periodtype){
       $this->db->select($this->field);
       $this->db->where('registration_id', $regid);
       $this->db->where('type', $periodtype);
       if($this->db->get($this->tableName)->num_rows() > 0){ return FALSE; }
       else{ return TRUE; }
    }
    
    function get_qty_receive($regid=0){
       
       if ($this->count_row_based_registration($regid) == TRUE){
            $before = $this->get_by_period($regid,0);
            $after = $this->get_by_period($regid,1); 

            $qty_source_before = $before->source_tonase;
            $qty_source_after = $after->source_tonase;

            $qty_to_before = $before->to_tonase;
            $qty_to_after = $after->to_tonase;
            return $qty_to_after-$qty_to_before;
       }else{ return 0; }
    }
    
    
}

/* End of file Property.php */