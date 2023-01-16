<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tank_balance_lib extends Custom_Model {

    public function __construct($deleted=NULL)
    {
        $this->deleted = $deleted;
        $this->tableName = 'tank_balances';
    }
   
    protected $field = array('id', 'tank_id', 'beginning', 'end', 'vamount', 'budget', 'month', 'year');
            
    function create($acc,$month=0,$year=0,$begin=0,$end=0)
    {
       $this->db->select($this->field); 
       $this->db->where('tank_id',$acc);
       $this->db->where('month',$month);
       $this->db->where('year',$year);
       $query = $this->db->get($this->tableName)->num_rows();
       
       if ($query == 0){ $this->fill($acc, $month, $year, $begin, $end); }
       else{ $this->edit($acc, $month, $year, $begin, $end); }
    }
    
    private function edit($acc,$month=0,$year=0,$begin=0,$end=0)
    {
       $trans = array('beginning' => $begin, 'end' => $end);
       $this->db->where('tank_id', $acc);
       $this->db->where('month', $month);
       $this->db->where('year', $year);
       $this->db->update($this->tableName, $trans); 
    }
    
    function fill($acc,$month,$year,$begin=0,$end=0)
    {
       $this->db->where('tank_id',$acc);
       $this->db->where('month',$month);
       $this->db->where('year',$year);
       $num = $this->db->get($this->tableName)->num_rows();
       
       if ($num == 0)
       {
          $trans = array('tank_id' => $acc, 'month' => $month, 'year' => $year, 'beginning' => $begin, 'end' => $end);
          $this->db->insert($this->tableName, $trans); 
       }
    }
    
    /// ========================= vamount ======================================
    
    function create_vamount($acc,$month=0,$year=0,$amt=0)
    {
       $this->db->where('tank_id',$acc);
       $this->db->where('month',$month);
       $this->db->where('year',$year);
       $query = $this->db->get($this->tableName)->num_rows();
       
       if ($query == 0){ $this->fill_vamount($acc, $month, $year, $amt); }
       else{ $this->edit_vamount($acc, $month, $year, $amt); }
    }
    
    private function edit_vamount($acc,$month=0,$year=0,$amt=0)
    {
       $trans = array('vamount' => $amt);
       $this->db->where('tank_id', $acc);
       $this->db->where('month', $month);
       $this->db->where('year', $year);
       $this->db->update($this->tableName, $trans); 
    }
    
    function fill_vamount($acc,$month,$year,$amt=0)
    {
       $this->db->where('tank_id',$acc);
       $this->db->where('month',$month);
       $this->db->where('year',$year);
       $num = $this->db->get($this->tableName)->num_rows();
       
       if ($num == 0)
       {
          $trans = array('tank_id' => $acc, 'month' => $month, 'year' => $year, 'vamount' => $amt);
          $this->db->insert($this->tableName, $trans); 
       }
    }
    
    function remove_balance($acc)
    {
       $this->db->where('tank_id',$acc); 
       $this->db->delete($this->tableName);
    }
    
    function get($acc,$month,$year,$type=null)
    {
       $this->db->select($this->field);  
       $this->db->where('tank_id',$acc);
       $this->db->where('month',$month);
       $this->db->where('year',$year);
       $res = $this->db->get($this->tableName)->row();
       if ($res){ if ($type){ return $res->$type; }else{ return $res; } }else{ return null; }
    }

}

/* End of file Property.php */
