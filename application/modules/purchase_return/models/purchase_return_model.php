<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Purchase_return_model extends Custom_Model
{
    protected $logs;
    
    function __construct()
    {
        parent::__construct();
        $this->logs = new Log_lib();
        $this->com = new Components();
        $this->com = $this->com->get_id('purchase_return');
        $this->tableName = 'purchase_return';
    }
    
    protected $field = array('id', 'no', 'purchase', 'dates', 'currency', 'acc', 'docno', 'vendor', 'user', 'log',
                             'status', 'tax', 'costs', 'total', 'balance', 'notes', 'cash', 'approved');
            
    function get_last($limit)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName);
        $this->db->order_by('id', 'desc');
        $this->db->limit($limit);
        return $this->db->get(); 
    }

    function search($vendor,$date)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName);
        $this->cek_null_string($vendor,"vendor");
        $this->cek_null_string($date,"dates");
        return $this->db->get();
    }

    function get_purchase_return_list($currency=null,$vendor=null)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName);
        $this->db->where('purchase_return.status', 0);
        $this->cek_null($vendor,"purchase_return.vendor");
        $this->cek_null($currency,"purchase_return.currency");
        $this->db->where('purchase_return.approved', 1);
        
        $this->db->order_by('purchase_return.dates', 'asc');
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
    
    function get_by_id($uid)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName);
        $this->db->where('purchase_return.id', $uid);
        return $this->db->get();
    }

    function get_by_no($uid)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName);
        $this->db->where('purchase_return.no', $uid);
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

    function report($cur,$vendor,$start,$end,$acc)
    {
        $this->db->select('purchase_return.id, purchase_return.no, purchase_return.purchase, purchase_return.dates, purchase_return.acc, purchase_return.docno, vendor.prefix, vendor.name, vendor.address, vendor.phone1, vendor.phone2,
                           vendor.city, purchase_return.user, purchase_return.log, purchase_return.currency,
                           purchase_return.status, purchase_return.tax, purchase_return.balance, purchase_return.total, purchase_return.notes,
                           purchase_return.costs, purchase_return.approved');

        $this->db->from('purchase_return, vendor');
        $this->db->where('purchase_return.vendor = vendor.id');
        $this->db->where("purchase_return.dates BETWEEN '".setnull($start)."' AND '".setnull($end)."'");
        $this->cek_null($vendor,"purchase_return.vendor");
        $this->cek_null($cur,"purchase_return.currency");
        $this->cek_null($acc,"purchase_return.acc");

        $this->db->where('purchase_return.approved', 1);
        $this->db->order_by('purchase_return.no', 'asc');
        return $this->db->get();
    }
    
    function total($cur,$vendor,$start,$end,$acc)
    {
        $this->db->select_sum('balance');
        $this->db->select_sum('tax');
        $this->db->select_sum('costs');
        $this->db->select_sum('total');

        $this->db->from('purchase_return, vendor');
        $this->db->where('purchase_return.vendor = vendor.id');
        $this->db->where("purchase_return.dates BETWEEN '".setnull($start)."' AND '".setnull($end)."'");
        $this->cek_null($vendor,"purchase_return.vendor");
        $this->cek_null($cur,"purchase_return.currency");
        $this->cek_null($acc,"purchase_return.acc");
        
        $this->db->where('purchase_return.approved', 1);
        return $this->db->get()->row_array();
    }

//    =========================================  REPORT  =================================================================

}

?>