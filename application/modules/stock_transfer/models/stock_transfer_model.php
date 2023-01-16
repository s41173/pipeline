<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Stock_transfer_model extends Custom_Model
{   
    protected $logs;
    
    function __construct()
    {
        parent::__construct();
        $this->logs = new Log_lib();
        $this->com = new Components();
        $this->com = $this->com->get_id('stock_transfer');
        $this->tableName = 'stock_transfer';
    }
    
    protected $field = array('id', 'no', 'dates', 'currency', 'branch_from', 'branch_to', 'desc', 'staff', 'user', 'balance', 'approved', 'log');
    
    function get_last($limit)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName);
        $this->db->order_by('no', 'desc');
        $this->db->limit($limit);
        return $this->db->get(); 
    }

    function search($date=null)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName);
        $this->cek_null($date,"dates");
        return $this->db->get();
    }

    function get_list($no=null)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName);
        $this->cek_null($no,"no");
        $this->db->where('approved', 1);
        return $this->db->get();
    }

    function counter()
    {
        $this->db->select_max('no');
        $test = $this->db->get($this->tableName)->row_array();
        $userid=$test['no'];
	$userid = $userid+1;
	return $userid;
    }
    
    function max_id()
    {
        $this->db->select_max('id');
        $test = $this->db->get($this->tableName)->row_array();
        $userid=$test['id'];
	$userid = $userid;
	return $userid;
    }

    function get_stock_adjustment_by_no($uid)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName);
        $this->db->where('no', $uid);
        return $this->db->get();
    }

    function valid_no($no)
    {
        $this->db->where('no', $no);
        $query = $this->db->get($this->tableName)->num_rows();
        if($query > 0) { return FALSE; } else { return TRUE; }
    }

    function validating_no($no,$id)
    {
        $this->db->where('no', $no);
        $this->db->where_not_in('id', $id);
        $query = $this->db->get($this->tableName)->num_rows();
        if($query > 0) {  return FALSE; } else { return TRUE; }
    }


//    =========================================  REPORT  =================================================================

    function report($start,$end)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName);
        $this->db->where("dates BETWEEN '".setnull($start)."'AND'".setnull($end)."'");
        $this->db->where('approved', 1);
        $this->db->order_by('no', 'asc');
        return $this->db->get();
    }
    
    function report_category($start,$end)
    {
        $this->db->select('stock_transfer.id, stock_transfer.no, stock_transfer.dates, stock_transfer.currency, stock_transfer.branch_from, stock_transfer.branch_to,
                           stock_transfer.desc, stock_transfer.staff, stock_transfer.user, stock_transfer.balance, stock_transfer.approved, stock_transfer.log,
                           stock_transfer_item.product_id, stock_transfer_item.qty, stock_transfer_item.price');
        
        $this->db->from("stock_transfer,stock_transfer_item");
        $this->db->where("stock_transfer.id = stock_transfer_item.stock_transfer");
        $this->db->where("stock_transfer.dates BETWEEN '".setnull($start)."' AND '".setnull($end)."'");
        $this->db->where('stock_transfer.approved', 1);
        $this->db->order_by('stock_transfer.no', 'asc');
        return $this->db->get();
    }

//    =========================================  REPORT  =================================================================

}

?>