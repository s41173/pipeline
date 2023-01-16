<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Sales_pos_model extends Custom_Model
{
    protected $logs;
    
    function __construct()
    {
        parent::__construct();
        $this->logs = new Log_lib();
        $this->com = new Components();
        $this->com = $this->com->get_id('pos');
        $this->tableName = 'sales';
    }
    
    protected $field = array('id', 'dates', 'cust_id', 'amount', 'tax', 'cost', 'total', 'shipping', 'branch_id',
                             'payment_id', 'bank_id', 'paid_date', 'paid_contact', 'due_date', 'discount', 'p1',
                             'cc_no', 'cc_name', 'cc_bank', 'sender_name', 'sender_acc', 'sender_bank', 'sender_amount', 'confirmation',
                             'approved', 'cash', 'log', 'created', 'updated', 'deleted', 'discount_desc');
    protected $com;
    
    function get_last($limit, $offset=null)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->db->order_by('id', 'desc'); 
        $this->db->limit($limit, $offset);
        return $this->db->get(); 
    }
    
    function search($branch=null,$paid=null,$confirm=null)
    {   
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->cek_null_string($branch, 'branch_id');
        
        if ($paid == '1'){ $this->db->where('paid_date IS NOT NULL'); }
        elseif ($paid == '0'){ $this->db->where('paid_date IS NULL'); }
        
        $this->cek_null_string($confirm, 'confirmation');
        $this->db->order_by('dates', 'desc'); 
        return $this->db->get(); 
    }
    
    function report($branch=null, $cust=null, $start=null,$end=null,$paid=null,$confirm=null)
    {   
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->between('dates', $start, $end);
        
        if ($paid == '1'){ $this->db->where('paid_date IS NOT NULL'); }
        elseif ($paid == '0'){ $this->db->where('paid_date IS NULL'); }
        $this->cek_null($confirm, 'confirmation');
        $this->cek_null($cust, 'cust_id');
        $this->cek_null($branch, 'branch_id');
        $this->db->order_by('dates', 'desc'); 
        return $this->db->get(); 
    }
    
    function counter($type=0)
    {
       $this->db->select_max('id');
       $query = $this->db->get($this->tableName)->row_array(); 
       if ($type == 0){ return intval($query['id']+1); }else { return intval($query['id']); }
    }
    
    function valid_confirm($sid)
    {
       $this->db->where('id', $sid);
       $query = $this->db->get($this->tableName)->row();
       if ($query->confirmation == 1){ return FALSE; }else{ return TRUE; }
    }
    
    function get_sales_qty_based_category($cat=0,$month=null,$year=null)
    {
        if (!$month){ $month = date('n'); }
        if (!$year){ $year = date('Y'); }
        
        $this->db->select_sum('sales_item.qty', 'qtys');
        
        $this->db->from('sales, sales_item, product, category');
        $this->db->where('sales.id = sales_item.sales_id');
        $this->db->where('sales_item.product_id = product.id');
        $this->db->where('product.category = category.id');
        
        $this->db->where('MONTH(sales.dates)', $month);
        $this->db->where('YEAR(sales.dates)', $year);
        $this->db->where('category.id', $cat);
        $this->db->where('sales.confirmation', 1);
        $query = $this->db->get()->row_array();
        return intval($query['qtys']);
    }
    
    function report_category($branch=null,$product=null,$start=null,$end=null,$paid=null,$confirm=null)
    {   
        $this->db->select('sales.id, sales.branch_id, sales.dates, product.sku, product.name, sales_item.qty, sales_item.price, sales.confirmation, '
                . '        category.name as category, manufacture.name as manufacture');
        $this->db->from('sales, sales_item, product, category, manufacture');
        $this->db->where('sales.id = sales_item.sales_id');
        $this->db->where('sales_item.product_id = product.id');
        $this->db->where('product.category = category.id');
        $this->db->where('product.manufacture = manufacture.id');
        
        $this->cek_null($product, 'product.sku');
        $this->cek_null($branch, 'branch_id');
        $this->db->where('sales.deleted', $this->deleted);
        $this->between('sales.dates', $start, $end);
        
        if ($paid == '1'){ $this->db->where('paid_date IS NOT NULL'); }
        elseif ($paid == '0'){ $this->db->where('paid_date IS NULL'); }
        $this->cek_null($confirm, 'confirmation');
        $this->db->order_by('dates', 'desc'); 
        return $this->db->get(); 
    }

}

?>