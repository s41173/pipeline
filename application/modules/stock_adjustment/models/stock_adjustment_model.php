<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Stock_adjustment_model extends Custom_Model
{   
    protected $logs;
    
    function __construct()
    {
        parent::__construct();
        $this->logs = new Log_lib();
        $this->com = new Components();
        $this->com = $this->com->get_id('stock_adjustment');
        $this->tableName = 'stock_adjustment';
    }
    
    protected $field = array('id', 'no', 'sales_id', 'dates', 'currency', 'branch_id', 'desc', 'staff', 'user', 'approved', 'log');
    
    function combo_sales()
    {
        $this->db->select('sales_id');
        $this->db->where('deleted', $this->deleted);
        $this->db->order_by('id', 'asc');
        $this->db->where('sales_id IS NOT NULL');
        $this->db->distinct();
//        $this->db->where('approved', 1);
        $val = $this->db->get($this->tableName)->result();
        $data['options'][0] = '-';
        if ($val){ foreach($val as $row){$data['options'][$row->sales_id] = 'SO-0'.$row->sales_id;}}
        return $data;        
    }
    
    function get_last($limit)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName);
        $this->db->order_by('no', 'desc');
        $this->db->limit($limit);
        return $this->db->get(); 
    }

    function search($date=null,$sales=null)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName);
        $this->cek_null_string($date,"dates");
        $this->cek_null_string($sales,"sales_id");
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
        $this->db->select('stock_adjustment.id, stock_adjustment.no, stock_adjustment.sales_id, stock_adjustment.dates, stock_adjustment.currency, stock_adjustment.desc, stock_adjustment.staff, stock_adjustment.user, stock_adjustment.approved, stock_adjustment.log,
                           stock_adjustment_item.type, stock_adjustment_item.product_id, stock_adjustment_item.qty, stock_adjustment_item.price');
        
        $this->db->from("stock_adjustment,stock_adjustment_item");
        $this->db->where("stock_adjustment.id = stock_adjustment_item.stock_adjustment");
        $this->db->where("stock_adjustment.dates BETWEEN '".setnull($start)."' AND '".setnull($end)."'");
        $this->db->where('stock_adjustment.approved', 1);
        $this->db->order_by('stock_adjustment.no', 'asc');
        return $this->db->get();
    }

//    =========================================  REPORT  =================================================================

}

?>