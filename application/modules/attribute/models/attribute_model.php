<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Attribute_model extends Custom_Model
{
    protected $logs;
    
    function __construct()
    {
        parent::__construct();
        $this->logs = new Log_lib();
        $this->com = new Components();
        $this->com = $this->com->get_id('attribute');
        $this->tableName = 'attribute';
    }
    
    protected $field = array('id', 'category_id', 'attribute_list_id', 'orders','created', 'updated', 'deleted');
    protected $com;
            

    function get_last($limit, $offset=null)
    {
        $this->db->select($this->field);
        $this->db->from($this->tableName); 
        $this->db->where('deleted', $this->deleted);
        $this->db->order_by('category_id', 'asc'); 
        $this->db->limit($limit, $offset);
        return $this->db->get(); 
    }

 
    function valid_attribute($cat,$attr)
    {
        $this->db->where('category_id', $cat);
        $this->db->where('attribute_list_id', $attr);
        $query = $this->db->get($this->tableName)->num_rows();
                                        
        if($query > 0){ return FALSE; } else{ return TRUE; }
    }

    function validation_attribute($cat,$attr ,$id)
    {
        $this->db->where('category_id', $cat);
        $this->db->where('attribute_list_id', $attr);
        $this->db->where_not_in('id', $id);
        $query = $this->db->get($this->tableName)->num_rows();

        if($query > 0){ return FALSE; } else{ return TRUE; }
    }

}

?>