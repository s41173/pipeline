<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tankgl_lib extends Custom_Model {
    
    public function __construct($deleted=NULL)
    {
        $this->deleted = $deleted;
        $this->tableName = 'tank_gl';
        $this->period = new Period_lib();
    }

    private $period;
    private $currency;

    // no, dates, code, currency, notes, balance, log
    public function new_journal($cust=0, $no, $dates, $code, $currency='IDR', $notes, $amount=0)
    {
        $log = $this->session->userdata('log');
        $journal = array('cust_id' => $cust, 'docno' => $no, 'dates' => $dates, 'doctype' => $code, 'currency' => $currency,
                         'notes' => $notes, 'balance' => $amount, 'log' => $log, 'approved' => 1);
        
        if ($this->cek_journal($no,$code, $dates, $currency) == TRUE)
        { $this->db->insert($this->tableName, $journal); $this->currency = $currency;
          $last_id = $this->db->insert_id();
          return $last_id;
        }else{ return 0; }
    }
    
    function cek_journal($no,$code,$date,$currency='IDR')
    {
        $this->db->where('docno', $no);
        $this->db->where('doctype', $code);
        $this->db->where('dates', $date);
        $this->db->where('currency', $currency);
        $num = $this->db->get($this->tableName)->num_rows();
        if ($num > 0){ return FALSE; }else { return TRUE; }
    }
    
    public function add_trans($gl,$tank,$debit=0,$credit=0)
    {
        $trans = array('glid' => $gl, 'tank_id' => $tank, 'debit' => $debit, 
                       'credit' => $credit, 'vamount' => $this->calculate_vamount($tank, $debit, $credit));
        
        if ($this->db->insert('tank_ledger', $trans) == TRUE){
          return $this->update_trans($gl);    
        }
    }
    
    private function update_trans($gl)
    {
        $this->db->select_sum('debit');
        $this->db->where('glid', $gl);
        $res = $this->db->get('tank_ledger')->row_array();
        $res = intval($res['debit']);
        
        $trans = array('balance' => $res);
        $this->db->where('id', $gl);
        return $this->db->update($this->tableName, $trans);
    }
    
    public function get_journal_id($code,$no)
    {
        $this->db->where('code', $code);
        $this->db->where('docno', $no);
        $jid = $this->db->get($this->tableName)->row();
        $jid = $jid->id;
        return $jid;
    }

//    ============================  remove transaction journal ==============================

    function remove_journal($no,$code)
    {
        // ============ update transaction ===================
        $this->db->where('docno', $no);
        $this->db->where('doctype', $code);
        $jid = $this->db->get($this->tableName)->row();
        // ====================================================
        
        if ($jid)
        {
            $this->db->where('glid', $jid->id);
            $this->db->delete('tank_ledger');

            $this->db->where('id', $jid->id);
            $this->db->delete($this->tableName);
            return TRUE;
        }
    }
        
    private function calculate_vamount($acc,$debit=0,$credit=0)
    {
        $res = $debit - $credit;
        return $res;
    }
    
    // cek tank in trsanction table
    function valid_tank_transaction($accid=0)
    {
        $this->db->where('tank_id', $accid);
        $res = $this->db->get('tank_ledger')->num_rows();
        if ($res > 0){ return FALSE; }else{ return TRUE; }
    }
    
}