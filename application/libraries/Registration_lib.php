<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Registration_lib extends Custom_Model{

    public function __construct($deleted=NULL)
    {
        $this->deleted = $deleted;
        $this->logs = new Log_lib();
        $this->com = new Components();
        $this->tableName = 'registration';
        $this->com = $this->com->get_id('registration');
        $this->field = $this->db->list_fields($this->tableName);
    }

    protected $field;
    
    function valid_confirmation($regid){
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('id', $regid);
        $result = $this->db->get()->row(); 
        if ($result->validation == 1){ return FALSE; }else{ return TRUE; }
    }
    
     function get_docno_combo(){
        $this->db->select('id,docno');
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->db->order_by('docno', 'asc'); 
        $this->db->distinct();
        $val = $this->db->get()->result();
        if ($val){ foreach($val as $row){$data['options'][$row->id] = strtoupper($row->docno);} }
        else { $data['options'][''] = '--'; }        
        return $data;
    }
    
    
}

/* End of file Property.php */