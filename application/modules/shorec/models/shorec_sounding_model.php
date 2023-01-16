<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Shorec_sounding_model extends Custom_Model
{
    protected $logs;
    
    function __construct()
    {
        parent::__construct();
        $this->logs = new Log_lib();
        $this->com = new Components();
        $this->tableName = 'shore_calculation_sounding';
        $this->com = $this->com->get_id('shorec');
        $this->field = $this->db->list_fields($this->tableName);
    }
    
    protected $com,$field;
    
    function get($shoreid)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->db->where('shore_calculation', $shoreid);
        $this->db->order_by('id', 'desc'); 
        return $this->db->get(); 
    }   
    
    function update_by_shore($uid, $users)
    {
        $this->db->where('shore_calculation', $uid);
        return $this->db->update($this->tableName, $users);
    }
    
    function calculate_diff($shoreid){
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->db->where('shore_calculation', $shoreid);
        $res = $this->db->get()->row();
        
        $sounding = floatval($res->in_sounding)-floatval($res->out_sounding);
        $obv = floatval($res->in_obv)-floatval($res->out_obv);
        $netkg = floatval($res->in_netkg)-floatval($res->out_netkg);
        $result['sounding'] = $sounding; $result['obv'] = $obv; $result['netkg'] = $netkg;
        
        if (floatval($res->in_netkg) > floatval($res->out_netkg)){ $result['trans'] = 'OUT'; }
        else{ $result['trans'] = 'IN'; }
        
        return $result;
    }

}

?>