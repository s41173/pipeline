<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Stock_transfer_item_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    var $table = 'stock_transfer_item';
    
    function get_last_item($pid)
    {
        $this->db->select('id, stock_transfer, product_id, qty, price, amount');
        $this->db->from($this->table);
        $this->db->where('stock_transfer', $pid);
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
        $this->db->select('id, stock_transfer, product_id, qty, price, amount');
        $this->db->from($this->table);
        $this->db->where('id', $id);
        $this->db->order_by('id', 'asc'); 
        return $this->db->get()->row(); 
    }

    function total($po)
    {
        $this->db->select_sum('amount');
        $this->db->where('stock_transfer', $po);
        return $this->db->get($this->table)->row_array();
    }
    
    function total_criteria($po)
    {
//        $this->db->select_sum('price');
        $amount = 0;
        $this->db->where('stock_transfer', $po);
        $res = $this->db->get($this->table)->result();
        foreach ($res as $val)
        {
           $res1 = intval($val->qty*$val->price);
           $amount = $amount + $res1;
        }
        return intval($amount);
    }

    
    function delete($uid)
    {
        $this->db->where('id', $uid);
        $this->db->delete($this->table); // perintah untuk delete data dari db
    }

    function delete_po($uid)
    {
        $this->db->where('stock_transfer', $uid);
        $this->db->delete($this->table); // perintah untuk delete data dari db
    }
    
    function add($users)
    {
        $this->db->insert($this->table, $users);
    }

    function report($po)
    {
        $this->db->select("$this->table.id, $this->table.stock_transfer, product.name as product, product.unit, $this->table.qty, $this->table.price, $this->table.amount");
        $this->db->from("$this->table,product");
        $this->db->where("$this->table.product_id = product.id");
        $this->db->where("$this->table.stock_transfer", $po);
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