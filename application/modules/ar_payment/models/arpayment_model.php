<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Arpayment_model extends Custom_Model
{
    protected $logs;
    
    function __construct()
    {
        parent::__construct();
        $this->logs = new Log_lib();
        $this->com = new Components();
        $this->com = $this->com->get_id('ar_payment');
        $this->tableName = 'ar_payment';
    }
    
    protected $field = array('id', 'no', 'tax', 'docno', 'currency', 'post_dated', 'post_dated_stts', 'check_no', 'check_acc', 'account',
                             'bank', 'due', 'customer', 'dates', 'acc', 'rate', 'discount', 'late', 'amount', 'over', 'over_stts',
                             'credit_over', 'approved', 'user', 'log');
    
    function get_last($limit)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName);
        $this->db->order_by('ar_payment.id', 'desc');
        $this->db->limit($limit);
        return $this->db->get(); 
    }

    function search($vendor='null',$date='null')
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName);
        $this->cek_null_string($vendor,"customer");
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
        $this->db->where('ar_payment.id', $uid);
        return $this->db->get();
    }

    function get_by_no($uid)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName);
        $this->db->where('ar_payment.no', $uid);
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
        $this->db->select('ar_payment.id, ar_payment.no, ar_payment.docno, ar_payment.check_no, ar_payment.check_acc, ar_payment.post_dated, ar_payment.dates, vendor.prefix, vendor.name, ar_payment.user,
                           ar_payment.amount, ar_payment.discount, ar_payment.late, ar_payment.over, ar_payment.over_stts, ar_payment.credit_over, ar_payment.acc, ar_payment.rate, ar_payment.currency, ar_payment.approved, ar_payment.log');

        $this->db->from('ar_payment, vendor');
        $this->db->where('ar_payment.vendor = vendor.id');
        $this->cek_null($vendor,"ar_payment.vendor");
        $this->cek_null($acc,"ar_payment.acc");
        $this->cek_null($cur,"ar_payment.currency");
        $this->db->where('ar_payment.approved', 1);
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
        $this->cek_null($acc,"ar_payment.acc");
        $this->cek_null($cur,"ar_payment.currency");
        $this->db->where('ar_payment.approved', 1);
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
        $this->cek_null($cur,"ar_payment.currency");
        $this->db->where('ar_payment.approved', 1); 
        $this->cek_null($month,"MONTH(dates)");
        $this->cek_null($year,"YEAR(dates)");
        
        $res = $this->db->get()->row_array();
        return intval($res['amount']+$res['over']);
    }

}

?>