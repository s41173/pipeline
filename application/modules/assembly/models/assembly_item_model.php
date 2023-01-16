<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Assembly_item_model extends CI_Model
{
    function __construct(){ parent::__construct();}
    
    var $table = 'assembly_trans';
    protected $field = array('id', 'assembly', 'product_id', 'qty', 'price');
    
    function get_last_item($pid,$type=null)
    {
        $this->db->select($this->field);
        $this->db->from($this->table);
        $this->db->where('assembly', $pid);
        $this->cek_null($type, 'type');
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
        $this->db->order_by('id', 'asc'); 
        return $this->db->get()->row(); 
    }

    function total($pid)
    {   
        $query = $this->db->query('SELECT SUM(price) total
        FROM '.$this->table.' WHERE assembly = '.$pid.';');
        return $query->row_array();
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