<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Payment_trans_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    var $tableName = 'ar_payment_trans';
    
    function get_last_item($po)
    {
        $this->db->select('id, ar_payment, code, no, discount, amount');
        $this->db->from($this->tableName);
        $this->db->where('ar_payment', $po);
        $this->db->order_by('id', 'asc'); 
        return $this->db->get(); 
    }
    
    function get_by_id($id)
    {
       $this->db->select('id, ar_payment, code, no, discount, nominal, amount');
       $this->db->from($this->tableName);
       $this->db->where('id', $id);
       return $this->db->get();  
    }

    function get_last_trans($po,$code='SO')
    {
        $this->db->select('id, ar_payment, code, no, discount, nominal, amount');
        $this->db->from($this->tableName);
        $this->db->where('ar_payment', $po);
        $this->db->where('code', $code);
        $this->db->order_by('id', 'asc');
        return $this->db->get();
    }

    function get_po_details($po)
    {
        $this->db->select('ar_payment_trans.id, ar_payment_trans.ar_payment, ar_payment_trans.code, ar_payment_trans.no, ar_payment_trans.discount, ar_payment_trans.amount, sales.dates');

        $this->db->from("ar_payment_trans, sales");
        $this->db->where('ar_payment_trans.no = sales.id');
        $this->db->where('ar_payment_trans.ar_payment', $po);
        $this->db->order_by('id', 'asc');
        return $this->db->get();
    }

    function get_item_based_po($no,$code,$pid)
    {
        $this->db->select('id, ar_payment, code, no, discount, amount');
        $this->db->from($this->tableName);
        $this->db->where('no', $no);
        $this->db->where('code', $code);
        $this->db->where('ar_payment', $pid);
        $query = $this->db->get()->num_rows();
        if ($query > 0) { return FALSE; } else { return TRUE; }
    }

    function total($po,$code)
    {
        $this->db->select_sum('amount');
        $this->db->select_sum('discount');
        
        $this->db->where('code', $code);
        $this->db->where('ar_payment', $po);
        return $this->db->get($this->tableName)->row_array();
    }

    function total_pr($po)
    {
        $this->db->select_sum('amount');
        $this->db->where('code', 'PR');
        $this->db->where('ar_payment', $po);
        return $this->db->get($this->tableName)->row_array();
    }
    
    function delete($uid)
    {
        $this->db->where('id', $uid);
        $this->db->delete($this->tableName); // perintah untuk delete data dari db
    }

    function delete_payment($uid)
    {
        $this->db->where('ar_payment', $uid);
        $this->db->delete($this->tableName); // perintah untuk delete data dari db
    }
    
    function add($users)
    {
       $this->db->insert($this->tableName, $users);
    }
    
    function closing(){
        $this->db->truncate($this->tableName); 
        $this->db->truncate('trans_ledger'); 
    }
    
}

?>