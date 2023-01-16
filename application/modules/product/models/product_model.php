<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Product_model extends Custom_Model
{
    protected $logs;
    
    function __construct()
    {
        parent::__construct();
        $this->logs = new Log_lib();
        $this->com = new Components();
        $this->com = $this->com->get_id('product');
        $this->tableName = 'product';
    }
    
    protected $field = array('id', 'sku', 'type', 'category', 'manufacture', 'name', 'model', 'permalink', 'currency',
                             'description', 'shortdesc', 'spesification', 'meta_title', 'meta_desc', 'meta_keywords',
                             'price', 'pricelow', 'discount', 'qty', 'min_order', 'image', 'url_upload', 'url1', 'url2', 'url3', 'url4', 'url5',
                             'dimension', 'dimension_class', 'weight', 'related', 'publish', 'color', 'size', 'unit',
                             'created', 'updated', 'deleted');
    protected $com;
    
    function get_last($limit, $offset=null)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->db->order_by('id', 'desc'); 
        $this->db->limit($limit, $offset);
        return $this->db->get(); 
    }
    
    function search($cat=null,$col=null,$size=null,$publish=null,$type=null)
    {   
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->cek_null_string($cat, 'category');
//        $this->cek_null_string($col, 'color');
        $this->cek_null_string($col, 'model');
        $this->cek_null_string($size, 'size');
        $this->cek_null_string($publish, 'publish');
        $this->cek_null_string($type, 'type');
        
        $this->db->order_by('name', 'asc'); 
        return $this->db->get(); 
    }
    
    function search_sku($sku=null)
    {   
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
//        $this->db->where('deleted', $this->deleted);
        $this->cek_null_string($sku, 'sku');
        $this->db->order_by('name', 'asc'); 
        return $this->db->get(); 
    }
    
    function search_list($cat=null,$manufacture=null,$model=null,$currency=null,$type=0)
    {   
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->cek_null($cat, 'category');
        $this->cek_null($manufacture, 'manufacture');
        $this->cek_null($currency, 'currency');
        $this->cek_null($model, 'model');
        $this->db->where('type',$type);
        $this->db->where('publish',1);
        $this->db->order_by('name', 'asc'); 
        return $this->db->get(); 
    }
    
    function report($cat=null,$manufacture=null)
    {   
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->cek_null($cat, 'category');
        $this->cek_null($manufacture, 'manufacture');
        
        $this->db->order_by('name', 'asc'); 
        return $this->db->get(); 
    }
    
    function counter()
    {
        $this->db->select_max('id');
        $test = $this->db->get($this->tableName)->row_array();
        $userid=$test['id'];
	$userid = intval($userid+1);
	return $userid;
    }
    
    function max_id()
    {
        $this->db->select_max('id');
        $test = $this->db->get($this->tableName)->row_array();
        $userid=$test['id'];
	$userid = intval($userid);
	return $userid;
    }
    
        
    function closing_trans(){
        $this->db->truncate('stock'); 
        $this->db->truncate('stock_ledger');
        $this->db->truncate('stock_temp');
        $this->db->truncate('warehouse_transaction');
    }
    

}

?>