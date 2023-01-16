<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Purchase_model extends Custom_Model
{   
    protected $logs;
    
    function __construct()
    {
        parent::__construct();
        $this->logs = new Log_lib();
        $this->com = new Components();
        $this->com = $this->com->get_id('purchase');
        $this->tableName = 'purchase';
    }
    
    protected $field = array('id', 'no', 'dates', 'request', 'docno', 'vendor', 'currency', 'acc', 'user', 'log',
                             'status', 'tax', 'costs', 'p1', 'p2', 'total', 'discount', 'ap_over', 'over_amount',
                             'notes', 'desc', 'shipping_date', 'stock_in_stts', 'approved');
    
    function get_last_purchase($limit)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName);
        $this->db->order_by('id', 'desc');
        $this->db->limit($limit);
        return $this->db->get(); 
    }

    function search($vendor='null',$date='null')
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName);
        $this->cek_null_string($vendor,"vendor");
        $this->cek_null_string($date,"dates");
        return $this->db->get();
    }

    function get_purchase_list($currency=null,$vendor=null,$st=0)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName);
        $this->cek_null($currency,"purchase.currency");
        $this->cek_null($vendor,"purchase.vendor");
//        $this->cek_nol($st,"purchase.status");
        $this->db->where('purchase.status', $st);
        $this->db->where('purchase.approved', 1);
        $this->db->order_by('purchase.dates', 'asc');
        return $this->db->get();
    }

    function get_settled_purchase($currency=null,$vendor=null) // tarik PO yang sudah lunas
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName);
        $this->cek_null($currency,"purchase.currency");
        $this->cek_null($vendor,"purchase.vendor");
        $this->db->where('purchase.approved', 1);
        $this->db->where('purchase.status', 1);
        $this->db->order_by('purchase.dates', 'asc');
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

    function get_purchase_by_no($uid)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName);
        $this->db->where('purchase.no', $uid);
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
    
    function validating_over($no,$id)
    {
        $this->db->where('ap_over', $no);
        $this->db->where_not_in('id', $id);
        $query = $this->db->get($this->tableName)->num_rows();
        if($query > 0) {  return FALSE; } else { return TRUE; }
    }


//    =========================================  REPORT  =================================================================

    function report($vendor,$cur,$start,$end,$status,$acc)
    {
        $this->db->select('purchase.id, purchase.no, purchase.dates, purchase.request, purchase.acc, purchase.docno, vendor.prefix, vendor.name, vendor.address, vendor.phone1, vendor.phone2,
                           vendor.city, purchase.currency, purchase.user, purchase.log, purchase.desc,
                           purchase.status, purchase.tax, purchase.p1, purchase.p2, purchase.total, purchase.notes, purchase.shipping_date,
                           purchase.costs, purchase.approved, purchase.ap_over, purchase.over_amount, purchase.stock_in_stts');

        $this->db->from('purchase, vendor');
        $this->db->where('purchase.vendor = vendor.id');
        $this->db->where("purchase.dates BETWEEN '".setnull($start)."' AND '".setnull($end)."'");
        $this->cek_null($vendor,"purchase.vendor");
        $this->cek_null($cur,"purchase.currency");
        $this->cek_null($status,"purchase.status");
        $this->cek_null($acc,"purchase.acc");

        $this->db->where('purchase.approved', 1);
        $this->db->order_by('purchase.no', 'asc');
        return $this->db->get();
    }
    
    function total($vendor,$cur,$start,$end,$status,$acc)
    {
        $this->db->select_sum('p1');
        $this->db->select_sum('p2');
        $this->db->select_sum('tax');
        $this->db->select_sum('costs');
        $this->db->select_sum('total');
        $this->db->select_sum('over_amount');

        $this->db->from('purchase, vendor');
        $this->db->where('purchase.vendor = vendor.id');
        $this->db->where("purchase.dates BETWEEN '".setnull($start)."' AND '".setnull($end)."'");
        $this->cek_null($vendor,"vendor.name");
        $this->cek_null($cur,"purchase.currency");
        $this->cek_null($status,"purchase.status");
        $this->cek_null($acc,"purchase.acc");
        $this->db->where('purchase.approved', 1);
        return $this->db->get()->row_array();
    }
    
    function total_chart($cur,$month,$year)
    {
        $this->db->select_sum('p1');
        $this->db->select_sum('p2');
        $this->db->select_sum('tax');
        $this->db->select_sum('costs');
        $this->db->select_sum('total');
        $this->db->select_sum('over_amount');

        $this->db->from($this->tableName);
        $this->cek_null($cur,"purchase.currency");
        $this->cek_null($month,"MONTH(dates)");
        $this->cek_null($year,"YEAR(dates)");
        $this->db->where('purchase.approved', 1);
        $res = $this->db->get()->row_array();
        
        return intval($res['total']+$res['costs']);
    }
    
    function report_product($product,$cur,$start,$end)
    {
        $this->db->select('purchase.id, purchase.no, purchase.dates, purchase.acc, purchase.docno, vendor.prefix, vendor.name, vendor.address, vendor.phone1, vendor.phone2,
                           purchase.currency, purchase.user, purchase.log,
                           purchase.notes, purchase.shipping_date, purchase.status, purchase.request,
                           purchase_item.product, purchase_item.qty, purchase_item.price, purchase_item.tax, purchase_item.amount');

        $this->db->from('purchase, purchase_item, vendor');
        $this->db->where('purchase.vendor = vendor.id');
        $this->db->where('purchase_item.purchase_id = purchase.id');
        $this->db->where("purchase.dates BETWEEN '".setnull($start)."' AND '".setnull($end)."'");
        $this->cek_null($product,"purchase_item.product");
        $this->cek_null($cur,"purchase.currency");

        $this->db->order_by('purchase.dates', 'desc');
        return $this->db->get();
    }

//    =========================================  REPORT  =================================================================

}

?>