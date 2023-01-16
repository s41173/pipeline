<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Appayment_model extends Custom_Model
{
    protected $logs;
    
    function __construct()
    {
        parent::__construct();
        $this->logs = new Log_lib();
        $this->com = new Components();
        $this->com = $this->com->get_id('ap_payment');
        $this->tableName = 'ap_payment';
    }
    
    protected $field = array('id', 'no', 'tax', 'docno', 'currency', 'post_dated', 'post_dated_stts', 'check_no', 'check_acc', 'account',
                             'bank', 'due', 'vendor', 'dates', 'acc', 'rate', 'discount', 'late', 'amount', 'over', 'over_stts',
                             'credit_over', 'approved', 'user', 'log');
    
    function get_last($limit)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName);
        $this->db->order_by('ap_payment.id', 'desc');
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

    function counter_docno()
    {
        $this->db->select_max('docno');
        $this->db->where('tax', 0);
        $test = $this->db->get($this->tableName)->row_array();
        $userid=$test['docno'];
	$userid = $userid+1;
	return $userid;
    }

    function counter()
    {
        $this->db->select_max('no');
//        $this->db->where('tax', 0);
        $test = $this->db->get($this->tableName)->row_array();
        $userid=$test['no'];
	$userid = $userid+1;
	return $userid;
    }
    
    function max_id()
    {
        $this->db->select_max('id');
//        $this->db->where('tax', 0);
        $test = $this->db->get($this->tableName)->row_array();
        $userid=$test['id'];
	$userid = $userid;
	return $userid;
    }
    
    function update_po($po, $users)
    {
        $this->db->where('no', $po);
        $this->db->update($this->tableName, $users);
        
        $val = array('updated' => date('Y-m-d H:i:s'));
        $this->db->where('no', $po);
        $this->db->update($this->tableName, $val);
        
        $this->logs->insert($this->session->userdata('userid'), date('Y-m-d'), waktuindo(), 'update', $this->com);
    }
    
    function counter_voucher_no($type)
    {
        $this->db->select_max('voucher_no');
        $this->db->where('tax', $type);
        $test = $this->db->get($this->tableName)->row_array();
        $userid=$test['voucher_no'];
	$userid = $userid+1;
	return $userid;
    }
    
    function get_by_id($uid)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName);
        $this->db->where('ap_payment.id', $uid);
        return $this->db->get();
    }

    function get_by_no($uid)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName);
        $this->db->where('ap_payment.no', $uid);
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

    function cek_no($no, $pid)
    {
        $this->db->where('check_no', $no);
        $this->db->where_not_in('id', $pid);
        $num = $this->db->get($this->tableName)->num_rows();

        if ($num > 0) { return FALSE; } else { return TRUE; }
    }

    function report($vendor,$start,$end,$acc,$cur)
    {
        $this->db->select('ap_payment.id, ap_payment.no, ap_payment.docno, ap_payment.check_no, ap_payment.check_acc, ap_payment.post_dated, ap_payment.dates, vendor.prefix, vendor.name, ap_payment.user,
                           ap_payment.amount, ap_payment.discount, ap_payment.late, ap_payment.over, ap_payment.over_stts, ap_payment.credit_over, ap_payment.acc, ap_payment.rate, ap_payment.currency, ap_payment.approved, ap_payment.log');

        $this->db->from('ap_payment, vendor');
        $this->db->where('ap_payment.vendor = vendor.id');
        $this->cek_null($vendor,"ap_payment.vendor");
        $this->cek_null($acc,"ap_payment.acc");
        $this->cek_null($cur,"ap_payment.currency");
        $this->db->where('ap_payment.approved', 1);
        $this->between($start,$end);
        return $this->db->get();
    }

    function total($vendor,$start,$end,$acc,$cur)
    {
        $this->db->select_sum('amount');
        $this->db->select_sum('late');
        $this->db->select_sum('discount');
        
        $this->db->from($this->tableName);
        $this->cek_null($vendor,"vendor");
        $this->cek_null($acc,"ap_payment.acc");
        $this->cek_null($cur,"ap_payment.currency");
        $this->db->where('ap_payment.approved', 1);
        $this->between($start,$end);
        return $this->db->get()->row_array();
    }
    
    function total_chart($cur,$month,$year)
    {
        $this->db->select_sum('amount');
        $this->db->select_sum('late');
        $this->db->select_sum('discount');
        $this->db->select_sum('over');
        
        $this->db->from($this->tableName);
        $this->cek_null($cur,"ap_payment.currency");
        $this->db->where('ap_payment.approved', 1); 
        $this->cek_null($month,"MONTH(dates)");
        $this->cek_null($year,"YEAR(dates)");
        
        $res = $this->db->get()->row_array();
        return intval($res['amount']+$res['over']);
    }

}

?>