<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tankledger_lib extends Custom_Model {
    
    public function __construct($deleted=NULL)
    {
        $this->deleted = $deleted;
        $this->tableName = 'tank_ledger';
        $this->period = new Period_lib();
        $this->period = $this->period->get();
        $this->balance = new Tank_balance_lib();
    }

    private $period,$balance;
    
//    ============================  remove transaction journal ==============================

    function delete_trans($jid)
    {
      $this->db->where('glid', $jid);
      return $this->db->delete($this->tableName);
    }
    
    function get_ledger($tank=null,$month=null,$year=null,$type=null)
    {
        if ($type == 'sum'){
            $this->db->select_sum('tank_ledger.vamount');
            $this->db->select_sum('tank_ledger.debit');
            $this->db->select_sum('tank_ledger.credit');
        }else{
            $this->db->select('tank_gl.id, tank_gl.doctype, tank_gl.cust_id, tank_gl.docno, tank_gl.dates, tank_gl.currency, tank_gl.notes, tank_gl.balance,
                               tank_ledger.debit, tank_ledger.credit, tank_ledger.vamount, tank_ledger.dirt, tank_ledger.ffa, tank_ledger.moisture');
        }
                
        $this->db->from('tank_gl, tank_ledger');
        $this->db->where('tank_gl.id = tank_ledger.glid');
        $this->db->where('MONTH(dates)', $month);
        $this->db->where('YEAR(dates)', $year);
        $this->cek_null($tank,"tank_ledger.tank_id");
        $this->db->where('tank_gl.approved', 1);
        $this->db->order_by('dates', 'asc');
        if ($type == 'sum'){ return $this->db->get()->row_array(); }else{ return $this->db->get(); }
    }
    
    function get_ledger_interval($tank=null,$start=null,$end=null,$type=null)
    {
        if ($type == 'sum'){
            $this->db->select_sum('tank_ledger.vamount');
            $this->db->select_sum('tank_ledger.debit');
            $this->db->select_sum('tank_ledger.credit');
        }else{
            $this->db->select('tank_gl.id, tank_gl.doctype, tank_gl.cust_id, tank_gl.docno, tank_gl.dates, tank_gl.currency, tank_gl.notes, tank_gl.balance,
                               tank_ledger.debit, tank_ledger.credit, tank_ledger.vamount, tank_ledger.dirt, tank_ledger.ffa, tank_ledger.moisture');
        }
        
        $this->db->from('tank_gl, tank_ledger');
        $this->db->where('tank_gl.id = tank_ledger.glid');
        $this->db->where("dates BETWEEN '".setnull($start)."' AND '".setnull($end)."'");
        $this->cek_null($tank,"tank_ledger.tank_id");
        $this->db->where('tank_gl.approved', 1);
        $this->db->order_by('dates', 'asc');
        $this->db->order_by('tank_ledger.id', 'asc');
        if ($type == 'sum'){ return $this->db->get()->row_array(); }else{ return $this->db->get(); }
    }
    
    function calculate($page=null)
    {   
        $tank = new Tank_lib();
        $res = null;
        $ps = new Period_lib();
        $next = $ps->next_period();
        $tanks = $tank->get_all()->result();
        foreach ($tanks as $res)
        {    

            $opening = floatval($this->balance->get($res->id, $this->period->month, $this->period->year, 'beginning'));
            $res_trans = $this->get_ledger($res->id, $this->period->month, $this->period->year, 'sum');
            $res_trans = floatval($res_trans['vamount']);
            $end = $opening+$res_trans;
            
            $this->balance->create($res->id, $this->period->month, $this->period->year, $opening, $end);  // create end saldo this month
            $this->balance->create($res->id, $next[0], $next[1], $end, 0); // create beginning saldo next month
        }
        return TRUE;    
    }
    
    function get_prev_balance($pid=null,$date=null){
        
        if ($pid != null && $date != null){
            $date = new DateTime($date); // For today/now, don't pass an arg.
            $date->modify("-1 day");
            $prevdate = $date->format("Y-m-d");
            $bulan = date('n', strtotime($prevdate));
            $tahun = date('Y', strtotime($prevdate));

            $opening = floatval($this->balance->get($pid, $bulan, $tahun, 'beginning'));  // dapatkan saldo awal balance

            $tglawal = date('Y-m-d',strtotime($bulan.'/1/'.$tahun));
            $res_trans = $this->get_ledger_interval($pid, $tglawal, $prevdate, 'sum');
            $res_trans = floatval($res_trans['vamount']);
    //        
            return $opening+$res_trans;
        }else{ return 0; }
    }
    
}