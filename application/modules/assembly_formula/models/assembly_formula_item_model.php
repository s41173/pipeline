<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Assembly_formula_item_model extends CI_Model
{
    function __construct(){
        parent::__construct();
        $this->product = new Product_lib();
    }
    
    protected $product;
    var $table = 'assembly_trans';
    protected $field = array('id', 'assembly', 'product_id', 'qty', 'price');
    
    
    function combo()
    {
        $this->db->select($this->field);
        $this->db->order_by('product_id', 'asc');
        $this->db->distinct();
        $val = $this->db->get($this->table)->result();
        if ($val){
          foreach($val as $row){ $data['options'][$row->product_id] = ucfirst($this->product->get_sku($row->product_id)); }    
        }else{ $data['options'][''] = '--'; }
        return $data;
    }
    
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
    
    function get_item_by_product($id)
    {
        $this->db->select($this->field);
        $this->db->from($this->table);
        $this->db->where('product_id', $id);
        $this->db->order_by('id', 'asc'); 
        return $this->db->get();
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
    
    function update($uid,$price){
       $res = array('price' => $price);
       $this->db->where('id', $uid);
       return $this->db->update($this->table, $res); 
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