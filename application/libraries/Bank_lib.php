<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bank_lib extends Main_model {

    public function __construct($deleted=NULL)
    {
        $this->deleted = $deleted;
        $this->tableName = 'bank';
    }

    private $ci;
    
    protected $field = array('id', 'acc_name', 'acc_no', 'acc_bank', 'currency',
                             'created', 'updated', 'deleted');
       
    
    function get_details($id)
    {
       $this->db->where('id', $id);
       return $this->db->get($this->tableName); 
    }
    
    function get_publish()
    {
       $this->db->where('invoice_publish', 1);
       return $this->db->get($this->tableName)->result(); 
    }
    
    function combo()
    {
        $this->db->select($this->field);
        $this->db->where('deleted', NULL);
        $this->db->order_by('acc_name', 'asc');
        $val = $this->db->get($this->tableName)->result();
        if ($val){
          foreach($val as $row){ $data['options'][$row->id] = ucfirst($row->acc_no.' : '.$row->acc_bank); }    
        }else{ $data['options'][''] = '--'; }
        
        return $data;
    }


}

/* End of file Property.php */