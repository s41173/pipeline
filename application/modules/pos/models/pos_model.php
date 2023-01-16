<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Pos_model extends Custom_Model
{
    protected $logs;
    
    function __construct()
    {
        parent::__construct();
        $this->logs = new Log_lib();
        $this->com = new Components();
        $this->com = $this->com->get_id('pos');
        $this->tableName = 'sales_item';
    }
    
    protected $field = array('sales_item.id', 'sales_item.orderid', 'sales_item.sales_id', 'sales_item.product_id', 'sales_item.qty', 'sales_item.tax', 'sales_item.discount', 'sales_item.amount',
                             'sales_item.price', 'sales_item.hpp');
    protected $com;
    
    function get_last($limit, $offset=null)
    {
        $this->db->select($this->field);
        $this->db->from('sales_item, sales');
        $this->db->where('sales.id = sales_item.sales_id');
        $this->db->where('date(sales.dates)', date('Y-m-d'));
        $this->db->order_by('sales_item.id', 'desc'); 
//        $this->db->limit($limit, $offset);
        $this->db->group_by("sales_item.orderid"); 
        return $this->db->get(); 
    }
    
    function search($payment=null,$dates=null)
    {   
        $this->db->select($this->field);
        $this->db->from('sales_item, sales');
        $this->db->where('sales.id = sales_item.sales_id');
        
        $this->cek_null_string($payment, 'sales.payment_id');
        $this->cek_null_string(picker_split2($dates), 'date(sales.dates)');
        
        $this->db->order_by('sales.dates', 'desc'); 
        $this->db->group_by("sales_item.orderid"); 
        return $this->db->get(); 
    }
    
    function counter($type=0)
    {
       $this->db->select_max('id');
       $query = $this->db->get($this->tableName)->row_array(); 
       if ($type == 0){ return intval($query['id']+1); }else { return intval($query['id']); }
    }
    
    function get_amount_based_orderid($orderid){
        
      $this->db->select_sum('amount');  
      $this->db->where('orderid', $orderid);
      $query = $this->db->get($this->tableName)->row_array();
      return floatval($query['amount']);
    }
    
    function get_by_orderid($uid)
    {
        $this->db->select($this->field);
        $this->db->where('orderid', $uid);
        return $this->db->get($this->tableName);
    }
    
    function valid_orderid($orderid){
        
        $this->db->select($this->field);
        $this->db->where('orderid', $orderid);
        $num = $this->db->get($this->tableName)->num_rows();
        if ($num > 0){ return TRUE; }else{ return FALSE; }
    }
    
    function total($orderid)
    {
        $this->db->select_sum('hpp');
        $this->db->select_sum('discount');
        $this->db->select_sum('tax');
        $this->db->select_sum('amount');
        $this->db->select_sum('price');
        $this->db->select_sum('qty');
        $this->db->select_sum('weight');
        $this->db->where('orderid', $orderid);
        return $this->db->get($this->tableName)->row_array();
    }
    

}

?>