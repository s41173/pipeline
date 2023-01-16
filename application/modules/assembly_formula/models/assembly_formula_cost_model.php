<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Assembly_formula_cost_model extends CI_Model
{
    function __construct(){ parent::__construct(); }
    
    var $table = 'assembly_cost';
    protected $field = array('id', 'assembly', 'notes', 'amount');
    
    function get_last_item($pid)
    {
        $this->db->select($this->field);
        $this->db->from($this->table);
        $this->db->where('assembly', $pid);
        $this->db->order_by('id', 'asc'); 
        return $this->db->get(); 
    }
    
    private function cek_null($val,$field)
    {
        if ($val == null){return null;}
        else {return $this->db->where($field, $val);}
    }
    
    function get_item_by_id($id)
    {
        $this->db->select($this->field);
        $this->db->from($this->table);
        $this->db->where('id', $id);
        return $this->db->get()->row(); 
    }

    function total($po)
    {
        $this->db->select_sum('amount');
        $this->db->where('assembly', $po);
        return $this->db->get($this->table)->row_array();
    }
    
    function delete($uid)
    {
        $this->db->where('id', $uid);
        $this->db->delete($this->table); // perintah untuk delete data dari db
    }

    function delete_po($uid)
    {
        $this->db->where('assembly', $uid);
        $this->db->delete($this->table); // perintah untuk delete data dari db
    }
    
    function add($users)
    {
        $this->db->insert($this->table, $users);
    }

    function report($po)
    {
        $this->db->select("$this->table.id, $this->table.assembly, $this->table.type, product.name as product, product.unit, $this->table.qty, $this->table.price, $this->table.account");
        $this->db->from("$this->table,product");
        $this->db->where("$this->table.product_id = product.id");
        $this->db->where("$this->table.assembly", $po);
        $this->db->order_by("$this->table.id", 'asc');
        return $this->db->get();
    }
    
    function counter()
    {
        $this->db->select_max('id');
        $test = $this->db->get($this->table)->row_array();
        $userid=$test['id'];
	$userid = $userid+1;
	return $userid;
    }
    
    function closing(){
        $this->db->truncate($this->table); 
    }

}

?>