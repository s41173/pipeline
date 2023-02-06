<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tank_lib extends Custom_Model {
    
    public function __construct($deleted=NULL)
    {
        $this->deleted = $deleted;
        $this->ledger = new Tankledger_lib();
        $this->balance = new Tank_balance_lib();
        $this->logs = new Log_lib();
        $this->com = new Components();
        $this->tableName = $this->com->get_table($this->com->get_id('registration'));
        $this->com = $this->com->get_id('tank');
        $this->field = $this->db->list_fields($this->tableName);
        $this->wb = new Wb_lib();
    }
    
    protected $com,$field, $wb;
    private $ledger,$balance;

    
    function get_list(){
//        echo 'ini get list';
//        echo $this->session->userdata('userid');
        $result = $this->wb->request_auth('contract/product', $this->session->userdata('userid'), null, null, 'GET');
        $result = json_decode($result, true); 
        $i=0;
//        print_r($result['result'][0]['id']);
        foreach ($result['result'] as $res) {
            echo $res['id'].'<br>';
//            $i++;
//            echo $res['stock_location_name'].'<br/>
        }
//        print_r($result);
    }
    
    function combo_api($type=null)
    {
        if ($type){ $data['options'][''] = '--Select--'; }
        $postdata = json_encode(array('limit' => 50, 'offset' => 0, 'filter' => 0));
        $result = $this->wb->request_auth('contract/product', $this->session->userdata('userid'), $postdata, null, 'POST');
        $result = json_decode($result, true); 
        if ($result){ foreach($result['result'] as $row){$data['options'][$row['location_id'].'|'.$row['stock_location_name']] = strtoupper($row['stock_location_name']);} }
        else { $data['options'][''] = '--'; }        
        return $data;
    }
    
    function combo($type=null)
    {
        if ($type){ $data['options'][''] = '--Select--'; }
        $this->db->select($this->field);
        $this->db->where('deleted', $this->deleted);
        $this->db->where('status', 1);
        $val = $this->db->get($this->tableName)->result();
        if ($val){ foreach($val as $row){$data['options'][$row->id] = ucfirst($row->sku);} }
        else { $data['options'][''] = '--'; }        
        return $data;
    }
    
}

/* End of file Property.php */