<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Csales_lib extends Custom_Model {
    
    public function __construct($deleted=NULL)
    {
        $this->deleted = $deleted;
        $this->tableName = 'csales';
        $this->customer = new Customer_lib();
    }
    private $customer;
    
    protected $field = array('id', 'contract_id', 'dates', 'cust_id', 'amount', 'tax', 'cost', 'total', 'shipping',
                             'payment_id', 'bank_id', 'paid_date', 'paid_contact', 'due_date', 
                             'cc_no', 'cc_name', 'cc_bank', 'sender_name', 'sender_acc', 'sender_bank', 'sender_amount', 'confirmation',
                             'approved', 'log', 'pos', 'created', 'updated', 'deleted');

    function cek_relation($id,$type)
    {
       $this->db->where($type, $id);
       $query = $this->db->get('product')->num_rows();
       if ($query > 0) { return FALSE; } else { return TRUE; }
    }
    
    function combo()
    {
        $this->db->select($this->field);
        $this->db->where('deleted', $this->deleted);
        $this->db->where('confirmation', 1);
        $this->db->where('paid_date IS NOT NULL');
        $this->db->order_by('id', 'asc');
//        $this->db->where('approved', 1);
        $val = $this->db->get($this->tableName)->result();
        $data['options'][0] = '-';
        if ($val){ foreach($val as $row){$data['options'][$row->id] = 'SO-0'.$row->id.' - '.tglin($row->dates).' - '.$this->customer->get_name($row->cust_id);}}
        return $data;        
    }
    
    function get_based_contract($contract=0)
    {
        if ($contract!=0)
        {
           $this->db->select($this->field);
           $this->db->where('contract_id', $contract);
           $this->db->where('confirmation', 1);
//           $this->db->where('paid_date IS NOT NULL');
           $res = $this->db->get($this->tableName);
           return $res;
        }
    }
    
    function get_detail_sales($id=null)
    {
        if ($id)
        {
           $this->db->select($this->field);
           $this->db->where('id', $id);
           $res = $this->db->get($this->tableName)->row();
           return $res;
        }
    }
    
    function get_transaction_sales($id=null)
    {
        if ($id)
        {
           $this->db->where('sales_id', $id);
           $res = $this->db->get('sales_item');
           return $res;
        }
    }
    
    function total($pid)
    {
        $this->db->select_sum('tax');
        $this->db->select_sum('amount');
        $this->db->select_sum('price');
        $this->db->select_sum('qty');
        $this->db->select_sum('weight');
        $this->db->where('sales_id', $pid);
        return $this->db->get('sales_item')->row_array();
    }
    
    function cek_shiping_based_sales($sid)
    {
       if ($sid)
        {
           $this->db->select($this->field);
           $this->db->where('sales_id', $sid);
           $res = $this->db->get($this->tableName)->row();
           if ($res){
              if ($res->shipdate){ return true; }else{ return false; } 
           }
           
        } 
    }
    
    // pos
    
    function create_pos($dates,$payment){
        
       $this->db->select($this->field);
       $this->db->where('dates', $dates);
       $this->db->where('payment_id', $payment);
       $this->db->where('pos', 1);
       $num = $this->db->get($this->tableName)->num_rows();
       $res = 0;
       if ($num > 0){
           $res = $this->get_pos_sales_id($dates,$payment);
       }else{ $res = $this->create_pos_sales($dates,$payment); }
       return $res;
    }
    
    private function get_pos_sales_id($dates,$payment){
        
       $this->db->select($this->field);
       $this->db->where('dates', $dates);
       $this->db->where('payment_id', $payment);
       $this->db->where('pos', 1);
       $this->db->where('approved', 0);
       $res = $this->db->get($this->tableName)->row(); 
       return $res->id;
    }
    
    private function create_pos_sales($dates,$payment){
        
        $branch = new Branch_lib();
        if ($payment == 5){ $cash = 1; }else{ $cash = 0; }
        $sales = array('cust_id' => 1, 'dates' => $dates, 'branch_id' => $branch->get_branch_default(), 'pos' => 1,
               'due_date' => $dates, 'payment_id' => $payment, 'cash' => $cash, 'created' => date('Y-m-d H:i:s'));
        $this->db->insert($this->tableName, $sales);
        
        return $this->get_pos_sales_id($dates, $payment);
    }
    
    // ============ AR Payment Purpose ================
    function cek_settled($no=null)
    {
        $this->db->select('paid_date');
        $this->db->where('id', $no);
        $query = $this->db->get($this->tableName)->row();
        if($query->paid_date != null) { return FALSE; } else { return TRUE; }
    }
    
    function get_po($no)
    {
        $this->db->select($this->field);
        $this->db->where('id', $no);
        $query = $this->db->get($this->tableName)->row();
        return $query;
    }
    
    function settled_po($uid, $users)
    {
        $this->db->where('id', $uid);
        return $this->db->update($this->tableName, $users);
    }
    
    function get_branch($uid){
       $this->db->select('branch_id');
       $this->db->where('id', $uid);
       $query = $this->db->get($this->tableName)->row();
       return $query->branch_id;
    }

}

/* End of file Property.php */