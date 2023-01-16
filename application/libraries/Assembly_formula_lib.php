<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Assembly_formula_lib extends Custom_Model {
    
    public function __construct($deleted=NULL)
    {
        $this->deleted = $deleted;
        $this->tableName = 'assembly';
        $this->product = new Product_lib();
        $this->stock = new Assembly_stock_lib();
        $this->wt = new Warehouse_transaction_lib();
        $this->sales = new Sales_lib();
        $this->field = $this->db->list_fields($this->tableName);
    }
    
    private $product,$stock,$wt,$sales;
    protected $field;

    function cek_product($pid)
    {
       $this->db->where('product', $pid);
       $this->db->where('formula', 1);
       $this->db->where('approved', 1);
       $query = $this->db->get($this->tableName)->num_rows();
       if ($query > 0) { return TRUE; } else { return FALSE; }
    }
    
    function details($pid,$type='amount'){
       $this->db->where('product', $pid);
       $query = $this->db->get($this->tableName)->row();
       if ($query){ return $query->$type; }
    }
    
    private function get_id_by_product($pid){
       $this->db->where('product', $pid);
       $this->db->where('formula', 1);
       $this->db->where('approved', 1);
       $query = $this->db->get($this->tableName)->row();
       return $query->id;
    }
    
    function min_stock($pid=0,$qty,$sid, $trans_type,$itemid){
       $asid = $this->get_id_by_product($pid);
       $storage = $this->get_by_id($asid)->row();
       $sales = $this->sales->get_detail_sales($sid);
       $this->db->where('assembly', $asid);
       $query = $this->db->get('assembly_trans')->result();
       foreach ($query as $res) {
           $rqty = intval($qty*$res->qty);   
           $hpp = intval($qty*$res->price);
           $this->stock->min_stock($res->product_id, $rqty, $hpp, $sid, $trans_type, $itemid);
           
           // add wt
           $this->wt->add($sales->dates, 'SO-'.$sid, $storage->branch_id, 'IDR', $res->product_id, 0, $rqty,
                           $res->price, $hpp, $this->session->userdata('log'));
           
       }
       
    }
    
    function rollback_stock($pid=0,$qty,$sid, $trans_type,$itemid){
       $asid = $this->get_id_by_product($pid);
       $sales = $this->sales->get_detail_sales($sid);
       $this->db->where('assembly', $asid);
       $query = $this->db->get('assembly_trans')->result();
       foreach ($query as $res) {
           $rqty = intval($qty*$res->qty);   
           $hpp = intval($qty*$res->price);
//           $this->stock->min_stock($res->product_id, $rqty, $hpp, $sid, $trans_type, $itemid);
           $this->stock->rollback('SO', $sid, $res->product_id);
           
           // min wt
           $this->wt->remove($sales->dates, 'SO-'.$sid, $res->product_id);
           
       }
       
    }

}

/* End of file Property.php */