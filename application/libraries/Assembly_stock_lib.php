<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Assembly_stock_lib extends Custom_Model {

    public function __construct($deleted=NULL)
    {
        $this->tableName = 'stock';
        $this->deleted = $deleted;
        $this->stocktemp = new Stock_temp_lib();
        $this->sales = new Sales_lib();
    }
    
    private $stocktemp, $stockvalue=0, $sales;
    // stock
        
    // -------------------------- bagian min stock ================================
    
    function min_stock($pid,$qty=0,$hpp=0,$sid,$trans_type='SA',$itemid=0) //FIFO / LIFO
    {        
        if ($qty > 0){ $this->stocks($pid,$qty,$hpp,$sid,$trans_type,$itemid); }
        $this->cleaning();
        return $this->stockvalue;
    }
    
    private function stocks($pid,$req,$hpp,$sid, $trans_type='SA',$itemid)
    {
        $salesd = $this->sales->get_detail_sales($sid);
//        $this->stockvalue = $this->stockvalue + intval($res->qty*$res->amount);
//       $this->stocktemp->add_stock($pid, $req, $hpp, $salesd->dates, $trans_type, $sid, $itemid); // pindhkan stock ke table temporary
//        $this->increase_stock($pid, $salesd->dates, $req,$hpp); // kurangkan stock di tabel stock
//        $this->min_stock($pid, intval($req - $res->qty),$sid, $trans_type,$itemid); 
    }
    
    function increase_stock($pid,$date,$aqty,$ahpp=0)
    {
        $this->db->where('product_id', $pid); 
        $this->db->where('dates', $date); 
        $num = $this->db->get($this->tableName)->num_rows();
        
        if ($num > 0)
        {
           $this->db->where('product_id', $pid);
           $this->db->where('dates', $date);
           $val = $this->db->get($this->tableName)->row();
           $qty = intval($val->qty - $aqty);
           $hpp = intval($val->amount - $ahpp);
           
           $res = array('qty' => $qty);
           $this->db->where('id', $val->id);
           $this->db->update($this->tableName, $res);
        }
        $this->cleaning();
    }
    
    // - ---- ----------------- akhir bagian min stock -----------------------------
    
    // -------------- rollback stock dari stock temp ke stock ----------------------
    
    function rollback($transtype='SA', $sid, $itemid=null)
    {
//        $result = $this->stocktemp->get_temp_stock($transtype, $sid, $itemid)->result();
//        foreach ($result as $res) {
////            $this->add_stock($res->product_id, $res->dates, $res->qty, $res->amount);
//        }
//        $this->stocktemp->remove_temp_stock($transtype, $sid, $itemid);
        $this->cleaning();
    }
    
    function add_stock($pid,$date,$qty,$price)
    {   
        $this->db->where('product_id', $pid); 
        $this->db->where('dates', $date); 
        $num = $this->db->get($this->tableName)->num_rows();
        
        if ($num > 0)
        {
            $this->db->where('product_id', $pid); 
            $this->db->where('dates', $date); 
            $val = $this->db->get($this->tableName)->row();
            $qty = intval($qty + $val->qty);

            $res = array('qty' => $qty);
            $this->db->where('id', $val->id);
            $this->db->update($this->tableName, $res);
        }
        else
        {
            $trans = array('product_id' => $pid, 'dates' => $date, 'qty' => $qty, 'amount' => $price);
            $this->db->insert($this->tableName, $trans); 
        }
        $this->cleaning();
    }
    
    function valid_stock($pid,$date,$aqty)
    {
      $this->db->where('product_id', $pid);
      $this->db->where('dates', $date);
      $this->db->where('qty >=', $aqty);
      $num = $this->db->get($this->tableName)->num_rows();
      if ($num > 0){ return TRUE; }else{ return FALSE; }
    }
  
    
    function get_stock($pid,$date)
    {
        $this->db->where('product_id', $pid);
        $this->db->where('dates', $date);
        $this->db->limit(1);
        $num = $this->db->get($this->tableName)->num_rows();
        if ($num > 0)
        {
            $this->db->order_by('dates', 'asc');
            $val = $this->db->get($this->tableName)->row(); 
            return $val;
        }
        else{ return null; }
    }
   
    
    function get_sum_stock($pid)
    {
       $this->db->where('product_id', $pid);
       return $this->db->get($this->tableName)->result();  
    }
    
    function get_sum_qty_stock($pid)
    {
       $this->db->select_sum('qty'); 
       $this->db->where('product_id', $pid);
       $res = $this->db->get($this->tableName)->row_array();  
       return intval($res['qty']);
    }
    
    function get_sum_amount_stock($pid)
    {
       $this->db->select('sum(qty*amount) as amount'); 
       $this->db->where('product_id', $pid);
       $res = $this->db->get($this->tableName)->row_array();  
       return floatval($res['amount']);
    }
    
    function unit_cost($pid){ 
        
        return @round($this->get_sum_amount_stock($pid)/$this->get_sum_qty_stock($pid)); 
        
    }
    
    function get_last_stock_price($pid) // fungsi get harga terakhir stock
    {
      $this->db->where('product_id', $pid);
      $this->db->order_by('dates', 'asc');
      $this->db->limit(1);
      
      $res = $this->db->get($this->tableName)->row();    
      if ($res){ return $res->amount; }else{ return 0; } 
      
    }
    
    function get_end_date_stock($pid,$start) // fungsi mendaptkan tnggal terakhir 
    {
       $this->db->select('dates');
       $this->db->where('product_id', $pid);
       $this->db->where('dates <', $start);
       $this->db->order_by('dates','desc');
       $this->db->limit(1);
       $res = $this->db->get($this->tableName)->row();
       if ($res){ return $res->dates; }else { return null; }
       
    }
    
    function cleaning()
    {
      $this->db->where('qty', 0);
      $this->db->delete($this->tableName);
    }

}

/* End of file Property.php */