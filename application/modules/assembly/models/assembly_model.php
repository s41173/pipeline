<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Assembly_model extends Custom_Model
{   
    protected $logs;
    
    function __construct()
    {
        parent::__construct();
        $this->logs = new Log_lib();
        $this->com = new Components();
        $this->com = $this->com->get_id('assembly');
        $this->tableName = 'assembly';
    }
    
    protected $field = array('id', 'dates', 'docno', 'currency', 'account', 'tax', 'branch_id', 'project', 'product', 'qty', 'log',
                             'unitprice', 'costs', 'taxamount', 'amount', 'notes', 'approved', 'formula');
    
    function get_last($limit)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName);
        $this->db->order_by('dates', 'desc');
        $this->db->where('formula', 0);
        $this->db->limit($limit);
        return $this->db->get(); 
    }

    function search($date=null,$product=null)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName);
        $this->cek_null_string($date,"dates");
        $this->cek_null_string($product,"product");
        $this->db->where('formula', 0);
        return $this->db->get();
    }

    function get_list($no=null)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName);
        $this->cek_null($no,"no");
        $this->db->where('approved', 1);
        $this->db->where('formula', 0);
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
        $this->db->where('docno', $no);
        $query = $this->db->get($this->tableName)->num_rows();
        if($query > 0) { return FALSE; } else { return TRUE; }
    }

    function validating_no($no,$id)
    {
        $this->db->where('docno', $no);
        $this->db->where_not_in('id', $id);
        $query = $this->db->get($this->tableName)->num_rows();
        if($query > 0) {  return FALSE; } else { return TRUE; }
    }

//    =========================================  REPORT  =================================================================

    function report($start,$end,$branch=null,$cur=null,$product=null)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName);
        $this->db->where("dates BETWEEN '".setnull($start)."'AND'".setnull($end)."'");
        $this->cek_null($branch, 'branch_id');
        $this->cek_null($cur, 'currency');
        $this->cek_null($product, 'product');
        $this->db->where('formula', 0);
        $this->db->order_by('id', 'asc');
        return $this->db->get();
    }

//    =========================================  REPORT  =================================================================

}

?>