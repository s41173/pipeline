<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tank_density_lib extends Custom_Model {

    public function __construct($deleted=NULL)
    {
        $this->deleted = $deleted;
        $this->tableName = 'tank_density';
    }

    private $ci;
    
    protected $field = array('id', 'tank_id', 'temperature', 'density');
       
    function create($trans)
    {
      return $this->db->insert($this->tableName, $trans);
    }
    
    function get($pid){
       $this->db->where('tank_id', $pid); 
       return $this->db->get($this->tableName)->result();
    }
    
    function fill($pid)
    {
       $this->db->where('tank_id', $pid);
       $num = $this->db->get($this->tableName)->num_rows();
       
       if ($num == 0)
       {
          for ($i=28;$i<=55;$i++){
            $trans = array('tank_id' => $pid, 'temperature' => $i);
            $this->db->insert($this->tableName, $trans); 
          }
       }
    }
    
    function clean($pid)
    {
        $this->db->where('tank_id', $pid);
        return $this->db->delete($this->tableName);
    }
    
    function edit($uid, $users)
    {
        $this->db->where('id', $uid);
        $this->db->update($this->tableName, $users);
        
        $val = array('updated' => date('Y-m-d H:i:s'));
        $this->db->where('id', $uid);
        $this->db->update($this->tableName, $val);
    }

}

/* End of file Property.php */