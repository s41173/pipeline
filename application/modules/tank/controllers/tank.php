<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once 'definer.php';

class Tank extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('Tank_model', 'model', TRUE);

        $this->properti = $this->property->get();
        $this->acl->otentikasi();

        $this->modul = $this->components->get(strtolower(get_class($this)));
        $this->title = strtolower(get_class($this));
        $this->role = new Role_lib();
        $this->currency = new Currency_lib();
        $this->period = new Period_lib();
        $this->period = $this->period->get();
        $this->density = new Tank_density_lib();
        $this->tank = new Tank_lib();
        $this->balance = new Tank_balance_lib();
        $this->ledger = new Tankledger_lib();
        $this->api = new Api_lib();
    }

    private $properti, $modul, $title, $api;
    private $role, $density, $tank, $balance,$ledger;

//    function index(){
//        
//        $gl = new Tankgl_lib();
////        $glid = $gl->new_journal(1, date('Y-m-d H:i:s'), 'SO', 'IDR', 'Test Jurnal', 0);
////        if ($glid!=0){
////            print_r($gl->add_trans($glid, 14, 1000));
////        }
//        
//        $gl->remove_journal(1, 'SO');
//    }
    
    function index(){
        $this->session->unset_userdata('start'); $this->session->unset_userdata('end'); $this->get_last(); 
    }
         
    public function getdatatable($search=null,$sku='null',$publish='null')
    {
        if ($search == 'deleted'){ $result = $this->model->get_deleted($this->modul['limit'])->result(); } 
        elseif ($search != 'deleted' && $search != null){ $result = $this->model->search($sku,$publish)->result(); }
        else{ $result = $this->model->get_last($this->modul['limit'])->result(); }
        
        $output = null;
        if ($result){
          
         foreach($result as $res)
	 { 
           $qty = $this->tank->get_qty($res->id, $this->period->month, $this->period->year);  // dapatkan nilai sesuai dengan hasil sounding
	   $output[] = array ($res->id, $res->sku, $res->name, $res->model, $qty, $res->status, floatval($res->weight/1000), $res->content);
	 } 
         
        $this->output
         ->set_status_header(200)
         ->set_content_type('application/json', 'utf-8')
         ->set_output(json_encode($output))
         ->_display();
         exit;  
        }
    }

    function get_last()
    {
        $this->acl->otentikasi1($this->title);

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords('Tank Manager');
        $data['h2title'] = $this->components->get_title($this->title);
        $data['main_view'] = 'tank_view';
	$data['form_action'] = site_url($this->title.'/add_process');
        $data['form_action_update'] = site_url($this->title.'/update_process');
        $data['form_action_del'] = site_url($this->title.'/delete_all');
        $data['form_action_report'] = site_url($this->title.'/report_process');
        $data['form_action_import'] = site_url($this->title.'/import');
        $data['link'] = array('link_back' => anchor('main/','Back', array('class' => 'btn btn-danger')));

        $data['content'] = $this->model->combo_content();
        $data['array'] = array('','');
        $data['month'] = combo_month();
        $data['default']['month'] = $this->period->month;
        
	// ---------------------------------------- //
 
        $config['first_tag_open'] = $config['last_tag_open']= $config['next_tag_open']= $config['prev_tag_open'] = $config['num_tag_open'] = '<li>';
        $config['first_tag_close'] = $config['last_tag_close']= $config['next_tag_close']= $config['prev_tag_close'] = $config['num_tag_close'] = '</li>';

        $config['cur_tag_open'] = "<li><span><b>";
        $config['cur_tag_close'] = "</b></span></li>";

        // library HTML table untuk membuat template table class zebra
        $tmpl = array('table_open' => '<table id="datatable-buttons" class="table table-striped table-bordered">');

        $this->table->set_template($tmpl);
        $this->table->set_empty("&nbsp;");

        //Set heading untuk table
        $this->table->set_heading('#','No', 'Code', 'Name', 'Weight (t)', 'Content', 'Qty (m<sup>3</sup>)', 'Action');

        $data['table'] = $this->table->generate();
        $data['source'] = site_url($this->title.'/getdatatable');
        $data['graph'] = site_url()."/".$this->title."/chart/";
            
        // Load absen view dengan melewatkan var $data sbgai parameter
	$this->load->view('template', $data);
    }
    
    function chart()
    {
        $data = $this->tank->get_all()->result();
        $datax = array();
        
        foreach ($data as $res) 
        {  
           $point = array("label" => $res->sku , "y" => $this->tank->get_qty($res->id, $this->period->month, $this->period->year));
           array_push($datax, $point);      
        }
        echo json_encode($datax, JSON_NUMERIC_CHECK);
    }
    
    function get_list($target='titem',$branchid=null)
    {
        $this->acl->otentikasi1($this->title);

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'tank_list';
        $data['form_action'] = site_url($this->title.'/get_list');
        
        $data['content'] = $this->model->combo_content();
        $products = $this->model->search_list($this->input->post('ccontent'))->result();

        $tmpl = array('table_open' => '<table id="example" width="100%" cellspacing="0" class="table table-striped table-bordered">');
        $this->table->set_template($tmpl);
        $this->table->set_empty("&nbsp;");

        //Set heading untuk table
        $this->table->set_heading('No', 'Code', 'Name', 'Weight (t)', 'Qty (m<sup>3</sup>)', 'Action');    
            
            $i = 0;
            if ($products){

                foreach ($products as $res)
                {
                   $datax = array('name' => 'button', 'type' => 'button', 'class' => 'btn btn-primary', 'content' => 'Select', 'onclick' => 'setvalue(\''.$res->sku.'\',\''.$target.'\')');
//                   $qty = $this->stockledger->get_qty($product->id, $branch, $this->period->month, $this->period->year);
//                   $qty = 0;
                   $qty = $this->tank->get_qty($res->id, $this->period->month, $this->period->year);  // dapatkan nilai sesuai dengan hasil sounding
                    $this->table->add_row
                    (
                        ++$i, $res->sku, $res->name, floatval($res->weight/1000), $qty,    
                        form_button($datax)
                    );
                }            
            }

            $data['table'] = $this->table->generate();
            $this->load->view('tank_list', $data);
    }
    
    function publish($uid = null)
    {
       if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){ 
       $val = $this->model->get_by_id($uid)->row();
       if ($val->status == 0){ $lng = array('status' => 1); }else { $lng = array('status' => 0); }
       $this->model->update($uid,$lng);
       echo 'true|Status Changed...!';
       }else{ echo "error|Sorry, you do not have the right to change publish status..!"; }
    }
    
    function update_all(){
        
       if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){ 
        $cek = $this->input->post('cek');
        $jumlah = count($cek);
        
        if($cek)
        {
          $jumlah = count($cek);
          for ($i=0; $i<$jumlah; $i++)
          {      
            $product = array('category' => $this->session->userdata('category'),
                             'size' => $this->session->userdata('size'),
                             'color' => $this->session->userdata('color'), 
                             'publish' => $this->session->userdata('publish')); 
                
            $this->model->update($cek[$i], $product);
          }
          
          $this->session->unset_userdata('category');
          $this->session->unset_userdata('size');
          $this->session->unset_userdata('color');
          $this->session->unset_userdata('publish');
            
          $mess = intval($jumlah)." ".$this->title."successfully updated..!!";
          echo 'true|'.$mess;
        }
        else
        { 
          $mess = "No $this->title Selected..!!";
          echo 'false|'.$mess;
        }
        }else{ echo "error|Sorry, you do not have the right to change product attribute..!"; }
    }
    
    function delete_all($type='soft')
    {
      if ($this->acl->otentikasi_admin($this->title,'ajax') == TRUE){
      
        $cek = $this->input->post('cek');
        $jumlah = count($cek);

        if($cek)
        {
          $jumlah = count($cek);
          $x = 0;
          for ($i=0; $i<$jumlah; $i++)
          {
             if ($this->valid_qty($cek[$i]) == TRUE){
                if ($type == 'soft') { $this->delete($cek[$i]); }
                else { $this->remove_img($cek[$i],'force');
                       $this->attribute_product->force_delete_by_product($cek[$i]);
                       $this->model->force_delete($cek[$i]);  }
                $x=$x+1;
             }
          }
          $res = intval($jumlah-$x);
          //$this->session->set_flashdata('message', "$res $this->title successfully removed &nbsp; - &nbsp; $x related to another component..!!");
          $mess = "$res $this->title successfully removed &nbsp; - &nbsp; $x related to another component..!!";
          echo 'true|'.$mess;
        }
        else
        { //$this->session->set_flashdata('message', "No $this->title Selected..!!"); 
          $mess = "No $this->title Selected..!!";
          echo 'false|'.$mess;
        }
      }else{ echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
      
    }

    function delete($uid)
    {
        if ($this->acl->otentikasi_admin($this->title,'ajax') == TRUE){
                
            if ($this->valid_qty($uid) == TRUE){
                
               $this->model->delete($uid);
               $this->session->set_flashdata('message', "1 $this->title successfully removed..!");
               echo "true|1 $this->title successfully removed..!"; 
            }
            else{ echo "error|Invalid Product Qty...!!"; }    
        }
        else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    function add()
    {
        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = 'Create New '.$this->modul['title'];
        $data['main_view'] = 'article_form';
	$data['form_action'] = site_url($this->title.'/add_process');
        $data['link'] = array('link_back' => anchor($this->title,'Back', array('class' => 'btn btn-danger')));

        $data['language'] = $this->language->combo();
        $data['category'] = $this->category->combo();
        $data['currency'] = $this->currency->combo();
        $data['source'] = site_url($this->title.'/getdatatable');
        
        $this->load->helper('editor');
        editor();

        $this->load->view('template', $data);
    }

    function add_process()
    {
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'category_view';
	$data['form_action'] = site_url($this->title.'/add_process');
	$data['link'] = array('link_back' => anchor('category/','<span>back</span>', array('class' => 'back')));

	// Form validation
        $this->form_validation->set_rules('tsku', 'SKU', 'callback_valid_sku');
        $this->form_validation->set_rules('tname', 'Name', 'required|callback_valid_name');
        $this->form_validation->set_rules('tmodel', 'Model', 'required|callback_valid_model');
        $this->form_validation->set_rules('tweight', 'Weight', 'required|numeric|is_natural_no_zero');

        if ($this->form_validation->run($this) == TRUE)
        {
            if ($this->input->post('tcontent')){ $content = $this->input->post('tcontent'); }
            else{ $content = $this->input->post('ccontent'); }
            $product = array('name' => strtolower($this->input->post('tname')),
                             'sku' => strtoupper($this->input->post('tsku')), 'model' => $this->input->post('tmodel'), 
                             'weight' => $this->input->post('tweight'), 'content' => strtoupper($content),
                             'created' => date('Y-m-d H:i:s'));
            
            if ($this->model->add($product) == TRUE){
                $mid = $this->model->max_id();
                $this->balance->create($mid, $this->period->month, $this->period->year, 0, 0);
                $this->create_tank_api($mid, strtoupper($this->input->post('tsku')));
//                $this->density->fill($mid); // fill density tank
                echo 'true|'.$this->title.' successfully saved..!';
            }
            
        }
        else{ echo "error|".validation_errors(); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    private function create_tank_api($mid,$code=null){
        
        $param = '{ "namespace":"'.clientcode.'", "kode":"'.$code.'" }';
        $response = $this->api->request(api, $param, 'code');
        $result = (array) json_decode($response[0], true);
        if ($response[1] == 200){
            
             $tank = array('unique_id' => $result['_id']);
             $this->model->update($mid, $tank);
             return $this->edit_tank_detail($mid);           
//           echo 'Status Code : '.$response[1].'<br>'.$result['_id'];    
        }else{ return FALSE; }
    }
    
    // update tank details based api
    private function edit_tank_detail($mid){
   
        $tank = $this->model->get_by_id($mid)->row();
        $response = $this->api->request(api.'/'.$tank->unique_id, null, 'code', 'GET');
        $result = (array) json_decode($response[0], true);
        
        if ($response[1] == 200){
           $value = array('measuring' => intval($result['hRef']*100), 'temperature' => $result['teraTemp'], 'coeff' => $result['koefMuaiRuang'], 'density' => $result['massaJenisCairan']);
           return $this->model->update($mid, $value);
        }else{ return FALSE; }
    }
    
    
    function details($uid=null,$type=null)
    {        
        $product = $this->model->get_by_id($uid)->row();
        if (!$product){ redirect($this->title); }
        
        if ($product->status == 0){ $status = 'Inactive'; }else{ $status = 'Active'; }
        $qty = $this->tank->get_qty($uid, $this->period->month, $this->period->year);
        
        if (!$type){
           
        echo $product->sku.'|'.$product->name.'|'.$product->model.'|'.$product->description.'|'.
             $status.'|'.$product->dimension.'|'.floatval($product->weight/1000).'|'.$qty.'|'.
             $product->min_order.'|'.$product->content.'|'.$product->height.'|'.$product->measuring.'|'.$product->temperature.'|'.
             $product->extra_kg.'|'.$product->extra_percentage.'|'.$product->unique_id;     
        
        }else{  echo $product->measuring.'|'.$product->temperature.'|'.$product->coeff.'|'.$product->density; }
    }
    
    // Fungsi update untuk menset texfield dengan nilai dari database
    function update($uid=null)
    {        
        $this->model->valid_add_trans($uid, $this->title);
        
        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = 'Edit '.$this->modul['title'];
        $data['main_view'] = 'tank_update';
	$data['form_action'] = site_url($this->title.'/update_process');
        $data['form_action_import'] = site_url($this->title.'/import_density/'.$uid);
        $data['link'] = array('link_back' => anchor($this->title,'Back', array('class' => 'btn btn-danger')));

        $data['source'] = site_url($this->title.'/getdatatable');
        $data['array'] = array('','');
        $data['graph'] = site_url()."/product/chart/";
        $data['content'] = $this->model->combo_content();
        
        $product = $this->model->get_by_id($uid)->row();
	$this->session->set_userdata('langid', $product->id);
        if ($product->ring == 1){ $ring = 'checked'; }else{ $ring = ""; }
        $data['default']['ring'] = $ring;  
        
        $data['uid'] = $uid;
        $data['default']['api'] = $product->unique_id;
        $data['default']['sku'] = $product->sku;
        $data['default']['name'] = $product->name;
        $data['default']['model'] = $product->model;
        $data['default']['description'] = $product->description;
        $data['default']['qty'] = $product->qty;
        $data['default']['min'] = $product->min_order;
        $data['default']['weight'] = $product->weight;
        $data['default']['content'] = $product->content;
        
        $data['default']['dimension'] = $product->dimension;
        $data['default']['height'] = $product->height;
        $data['default']['measuring'] = $product->measuring;
        $data['default']['temperature'] = $product->temperature;
        $data['default']['extrakg'] = $product->extra_kg;
        $data['default']['extrapercent'] = $product->extra_percentage;
        $data['default']['coeff'] = $product->coeff;
        $data['default']['density'] = $product->density;
        $data['uniqueid'] = $product->unique_id;
        
        // tank density
//        $data['density'] = $this->density->get($uid);
         
        $this->load->helper('editor');
        editor();
        $this->load->view('template', $data);
    }
    
    
    function valid_qty($pid)
    {
        $qty = $this->tank->get_qty($pid, $this->period->month, $this->period->year); 
        if ($qty != 0){
           $this->form_validation->set_message('valid_qty', "Product Qty is greater than 0..!");
           return FALSE; 
        }else{ return TRUE; }
    }

    function valid_role($val)
    {
        if(!$val)
        {
          $this->form_validation->set_message('valid_role', "role type required.");
          return FALSE;
        }
        else{ return TRUE; }
    }
    
    function valid_sku($val)
    {
        if ($this->model->valid('sku',$val) == FALSE)
        {
            $this->form_validation->set_message('valid_sku','SKU registered..!');
            return FALSE;
        }
        else{ return TRUE; }
    }
    
    function valid_deleted(){
      $id = $this->session->userdata('langid');
      $val = $this->model->get_by_id($id)->row();
      if ($val->deleted != NULL){ $this->form_validation->set_message('valid_deleted', "Product Already Deleted!"); return FALSE; }
      else{ return TRUE; }
    }
   
    function validating_sku($val)
    {
	$id = $this->session->userdata('langid');
	if ($this->model->validating('sku',$val,$id) == FALSE)
        {
            $this->form_validation->set_message('validating_sku', "SKU registered!");
            return FALSE;
        }
        else{ return TRUE; }
    }
    
    function valid_name($val)
    {
        if ($this->model->valid('name',$val) == FALSE)
        {
            $this->form_validation->set_message('valid_name','Name registered..!');
            return FALSE;
        }
        else{ return TRUE; }
    }

    function validating_name($val)
    {
	$id = $this->session->userdata('langid');
	if ($this->model->validating('name',$val,$id) == FALSE)
        {
            $this->form_validation->set_message('validating_name', "Name registered!");
            return FALSE;
        }
        else{ return TRUE; }
    }
    
    function valid_model($val)
    {
        if ($this->model->valid('model',$val) == FALSE)
        {
            $this->form_validation->set_message('valid_model','Model registered..!');
            return FALSE;
        }
        else{ return TRUE; }
    }

    function validating_model($val)
    {
	$id = $this->session->userdata('langid');
	if ($this->model->validating('model',$val,$id) == FALSE)
        {
            $this->form_validation->set_message('validating_model', "Model registered!");
            return FALSE;
        }
        else{ return TRUE; }
    }
    
    private function update_tank_api($mid=0){
        
        $tank = $this->model->get_by_id($mid)->row();
        $param = '{ "namespace":"'.clientcode.'", "kode":"'.$tank->sku.'", "hRef":'.floatval($tank->measuring/100).', "teraTemp":'.$tank->temperature.', "koefMuaiRuang":'.$tank->coeff.', "massaJenisCairan":'.$tank->density.', "statusUseCincin":'.$tank->ring.' }';
        $response = $this->api->request(api.'/'.$tank->unique_id, $param, 'code','PUT');
        
        $result = (array) json_decode($response[0], true);
        if ($response[1] == 200){return TRUE;}else{ return FALSE; }
    }

    // Fungsi update untuk mengupdate db
    function update_process($param=0)
    {
        if ($this->acl->otentikasi_admin($this->title) == TRUE){

        $data['title'] = $this->properti['name'].' | Productistrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'product_update';
	$data['form_action'] = site_url($this->title.'/update_process');
	$data['link'] = array('link_back' => anchor('admin/','<span>back</span>', array('class' => 'back')));

	// Form validation
        if ($param == 1)
        {
            $this->form_validation->set_rules('tsku', 'SKU', 'required|callback_validating_sku|callback_valid_deleted');
            $this->form_validation->set_rules('tname', 'Product Name', 'required|callback_validating_name');
            $this->form_validation->set_rules('tmodel', 'Product Model', 'required|callback_validating_model');
            $this->form_validation->set_rules('tmin', 'Minimum Order', 'required|numeric');
            $this->form_validation->set_rules('tweight', 'Weight', 'required|numeric');
            $this->form_validation->set_rules('tdesc', 'Description', '');
            
            if ($this->form_validation->run($this) == TRUE)
            {
//                if ($this->input->post('tcontent')){ $content = $this->input->post('tcontent'); }
//                else{ $content = $this->input->post('ccontent'); }
            
                $product = array('name' => strtolower($this->input->post('tname')),
                                 'sku' => strtoupper($this->input->post('tsku')), 'model' => $this->input->post('tmodel'),
                                 'weight' => $this->input->post('tweight'),
                                 'min_order' => $this->input->post('tmin'), 'description' => $this->input->post('tdesc')
                                );
                
                $this->model->update($this->input->post('tid'), $product);
                $this->update_tank_api($this->input->post('tid'));
//                $this->session->set_flashdata('message', "One $this->title has successfully updated!");
                echo 'true|'."One $this->title has successfully updated!";
                
                // end update 1
            }
            else{ echo 'error|'.validation_errors(); }
        }
        elseif ($param == 2)
        {
            $this->form_validation->set_rules('tdimension', 'Dimension', '');
            $this->form_validation->set_rules('theight', 'Tank Height', 'required|required');
            $this->form_validation->set_rules('tmeasure', 'Measure Table', 'required');
            $this->form_validation->set_rules('ttemperature', 'Temperature', 'required|numeric');
            $this->form_validation->set_rules('textrakg', 'Extra-Kg', 'required|numeric');
            $this->form_validation->set_rules('textrapercent', 'Extra-%', 'required|numeric');
            
            if ($this->form_validation->run($this) == TRUE)
            {
                echo str_replace(",",".","0,246529");
                $product = array('dimension' => $this->input->post('tdimension'),
                                 'height' => $this->input->post('theight'), 'measuring' => $this->input->post('tmeasure'),
                                 'temperature' => $this->input->post('ttemperature'), 'extra_kg' => $this->input->post('textrakg'), 
                                 'extra_percentage' => $this->input->post('textrapercent'), 'ring' => $this->input->post('cring'),
                                 'coeff' => str_replace(",",".",$this->input->post('tcoeff')), 'density' => str_replace(",",".",$this->input->post('tdensity'))
                                );
                
                $this->model->update($this->input->post('tid'), $product);
                $this->update_tank_api($this->input->post('tid'));
                echo 'true|'."One $this->title dimension has successfully updated!";
                
                // end update 1
            }
            else{ echo 'error|'.validation_errors(); }
            
            
        }
        elseif ($param == 3)
        {
            $this->form_validation->set_rules('tprice', 'Price', 'required|numeric');
            $this->form_validation->set_rules('tlowprice', 'Low-Price', 'required|numeric|callback_valid_low_price');
            $this->form_validation->set_rules('tdisc_p', 'Discount Percentage', 'numeric');
            $this->form_validation->set_rules('tdiscount', 'Discount', 'required|numeric');
            $this->form_validation->set_rules('tmin', 'Minimum Order', 'required|numeric');
            
            if ($this->form_validation->run($this) == TRUE){
               
                $this->edit_qty($this->session->userdata('langid'), $this->input->post('tqty'));
                $product = array('price' => $this->input->post('tprice'), 'pricelow' => $this->input->post('tlowprice'),
                                 'discount' => $this->input->post('tdiscount'),
                                 'min_order' => $this->input->post('tmin'), 'qty' => $this->input->post('tqty')
                                 );
                $this->model->update($this->session->userdata('langid'), $product);
                echo 'true|One '.$this->title.' price and qty has successfully updated!'; 
            }else{ echo 'error|'.validation_errors(); }
            

        }
        elseif ($param == 4)
        {
            $this->form_validation->set_rules('tlength', 'Length (Dimension)', 'numeric');
            $this->form_validation->set_rules('twidth', 'Width (Dimension)', 'numeric');
            $this->form_validation->set_rules('theight', 'Height (Dimension)', 'numeric');
            $this->form_validation->set_rules('cdimension', 'Dimension Class', '');
            $this->form_validation->set_rules('tweight', 'Weight', 'numeric');
            $this->form_validation->set_rules('ccolor', 'Color', 'required');
            $this->form_validation->set_rules('csize', 'Size', 'required');
            
            $dimension = $this->input->post('tlength').'x'.$this->input->post('twidth').'x'.$this->input->post('theight');
            $product = array('dimension' => $dimension, 'dimension_class' => $this->input->post('cdimension'),
                             'weight' => $this->input->post('tweight'), 'color' => $this->input->post('ccolor'), 
                             'size' => $this->input->post('csize'), 'related' => !empty($this->input->post('crelated')) ? split_array($this->input->post('crelated')) : null
                             );
            $this->model->update($this->session->userdata('langid'), $product);
            echo 'true|One '.$this->title.' dimension has successfully updated!';
        }

        
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    private function edit_qty($pid,$eqty)
    {
        $res = $this->model->get_by_id($pid)->row();
        $begin = $res->qty;
        if ($begin > $eqty){ // pengurangan
            $this->wt->add(date('Y-m-d H:i:s'), '', $res->currency, $pid, 0, intval($begin-$eqty), 0, 0, $this->session->userdata('log')); 
        }
        elseif ($begin < $eqty) // penambahan
        {
           $this->wt->add(date('Y-m-d H:i:s'), '', $res->currency, $pid, intval($eqty-$begin), 0, 0, 0, $this->session->userdata('log')); 
        }
    }
    
    function report_process()
    {
        $this->acl->otentikasi2($this->title);
        $data['title'] = $this->properti['name'].' | Report '.ucwords($this->modul['title']);

        $data['rundate'] = tglin(date('Y-m-d'));
        $data['log'] = $this->session->userdata('log');
        $data['category'] = $this->category->get_name($this->input->post('ccategory'));
        $data['manufacture'] = $this->manufacture->get_name($this->input->post('cmanufacture'));
        $data['year'] = $this->input->post('tyear');
        $data['month'] = $this->input->post('cmonth');
        
        $data['branch_id'] = $this->input->post('cbranch');
        $data['branch'] = $this->branch->get_name($this->input->post('cbranch'));
        $data['month'] = $this->input->post('cmonth');
        $data['year'] = $this->input->post('tyear');

//        Property Details
        $data['company'] = $this->properti['name'];
        $data['reports'] = $this->model->report($this->input->post('ccategory'), $this->input->post('cmanufacture'))->result();
        
        if ($this->input->post('ctype') == 0){ $this->load->view('product_report', $data); }
        else { $this->load->view('product_pivot', $data); }
    }
        
    
//    ====================== kartu stock =======================================
    
    function stock_card($pid)
    {
        $this->acl->otentikasi1($this->title);

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'product_card';
        
        $data['log']     = $this->session->userdata('log');
        $data['company'] = $this->properti['name'];
        $data['address'] = $this->properti['address'];
        $data['phone1']  = $this->properti['phone1'];
        $data['phone2']  = $this->properti['phone2'];
        $data['fax']     = $this->properti['fax'];
        $data['website'] = $this->properti['sitename'];
        $data['email']   = $this->properti['email'];

        $product = $this->model->get_by_id($pid)->row();

        $data['code'] = $product->sku;
        $data['name'] = $product->name;
        $data['weight'] = floatval($product->weight/1000);
        $data['content'] = $product->content;
        
        $data['open'] = floatval($this->balance->get($pid, $this->period->month, $this->period->year, 'beginning'));
        $data['trans'] = $this->ledger->get_ledger($pid, $this->period->month, $this->period->year)->result();
        $data['page'] = $this->title.'/stock_card/'.$pid;
        $data['pid'] = $pid;
        
         $this->load->view('tank_card', $data);
    }
    
    function stock_card_report($pid)
    {
        if (!$pid){ redirect($this->title.'/ledger'); }
        $this->acl->otentikasi1($this->title);
        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'product_card';
        
        $data['log']     = $this->session->userdata('log');
        $data['company'] = $this->properti['name'];
        $data['address'] = $this->properti['address'];
        $data['phone1']  = $this->properti['phone1'];
        $data['phone2']  = $this->properti['phone2'];
        $data['fax']     = $this->properti['fax'];
        $data['website'] = $this->properti['sitename'];
        $data['email']   = $this->properti['email'];

        $product = $this->model->get_by_id($pid)->row();
        
        $data['pid'] = $pid;
        $data['code'] = $product->sku;
        $data['name'] = $product->name;
        $data['weight'] = floatval($product->weight/1000);
        $data['content'] = $product->content;
        
        $data['open'] = $this->ledger->get_prev_balance($pid,$this->session->userdata('start')); 
        $data['trans'] = $this->ledger->get_ledger_interval($pid, $this->session->userdata('start'), $this->session->userdata('end'))->result();
       
        $data['page'] = $this->title.'/ledger';
        $this->load->view('tank_card', $data);
    }
   
    // ====================================== CLOSING ======================================
    function reset_process(){ $this->model->closing(); $this->model->closing_trans(); } 

    // ===================================== density process ===============================
    
    function fetch_density($uniqueid=null){
        
//        $uniqueid = '5cc42a644088b662da8b98ab';
        $response = $this->api->request(api.'/'.$uniqueid.'/density', null, 'code','GET');
        $result = (array) json_decode($response[0], true);
        $this->api->response($result['densitas']);
    }
    
    function add_density($tankid=null){
        
        $param = '{ "suhu":"'.$this->input->post('ttanksuhu').'", "nilai_densitas":"'.$this->input->post('ttankdensity').'" }';
        $response = $this->api->request(api.'/'.$tankid.'/density/', $param, 'code');
        $result = (array) json_decode($response[0], true);
        
        if ($response[1] == 200){
           echo 'true|density '.$result['title'].'|'.$response[1];
        }else{ echo 'error|'.$result['title'].'|'.$response[1]; }
    }
    
    function remove_density($tankid=null,$densityid=null){
        
        $response = $this->api->request(api.'/'.$tankid.'/density/'.$densityid, null, 'code', 'DELETE');
        $result = (array) json_decode($response[0], true);
        
        if ($response[1] == 200){
           echo 'true|density '.$result['title'];
        }else{ echo 'error|failed to remove data'; }
    }
    
    function density_update($tankid=null,$densityid=null){
        
        $response = $this->api->request(api.'/'.$tankid.'/density/'.$densityid, null, 'code', 'GET');
        $result = (array) json_decode($response[0], true);
        if ($response[1] == 200){ echo 'true|'.$result['suhu'].'|'.$result['nilai_densitas'];}
        else{ echo 'false|0|0'; }
    }
    
    function density_update_process($tankid=null)
    {
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){

	// Form validation
        $this->form_validation->set_rules('ttanksuhu', 'Temperature', 'required');
        $this->form_validation->set_rules('ttankdensity', 'Density', 'required|numeric');
        $this->form_validation->set_rules('tid', 'ID', 'required');

        if ($this->form_validation->run($this) == TRUE)
        {
            $param = '{ "suhu":"'.$this->input->post('ttanksuhu').'", "nilai_densitas":"'.$this->input->post('ttankdensity').'" }';
            $response = $this->api->request(api.'/'.$tankid.'/density/'.$this->input->post('tid'), $param, 'code', 'PUT');
            $result = (array) json_decode($response[0], true);
            
             if ($response[1] == 200){
                echo 'true|density '.$result['title'];
             }else{ echo 'error|failed to update data'; }
        }
        else{ echo 'error|'.validation_errors(); }

        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    function import_density($tankid=0)
    {
        $data['error'] = null;
	
//        $this->form_validation->set_rules('userfile', 'Import File', '');
        
             // ==================== upload ========================
            
            $config['upload_path']   = './uploads/';
            $config['file_name']     = 'tank_density';
            $config['allowed_types'] = '*';
//            $config['allowed_types'] = 'csv';
            $config['overwrite']     = TRUE;
            $config['max_size']	     = '10000';
            $config['remove_spaces'] = TRUE;
            $this->load->library('upload', $config);
            
            if ( !$this->upload->do_upload("userfile"))
            { 
               $data['error'] = $this->upload->display_errors(); 
               $this->session->set_flashdata('message', "Error imported!");
               echo 'error|'.$this->upload->display_errors(); 
            }
            else
            { 
                $status = true;
               // success page 
              if ($this->density->clean($tankid) == TRUE){ $status = $this->density_import_process($config['file_name'].'.csv',$tankid); }
              $info = $this->upload->data(); 
              if ($status == false){ echo "error|Import Failed..!"; }else{ echo 'true|CSV Successful Uploaded'; }
              
            }                
        
       // redirect($this->title);
        
    }
    
    private function density_import_process($filename,$tankid)
    {
        $this->load->helper('file');
//        $csvreader = new CSVReader();
        $csvreader = $this->load->library('csvreader');
        $filename = './uploads/'.$filename;
        
        $result = $csvreader->parse_file($filename);
        $stts = true;
        foreach($result as $res)
        {
           if(isset($res['TEMPERATURE']) && isset($res['DENSITY']))
           {
             $account = array('tank_id' => $tankid, 'temperature' => $res['TEMPERATURE'], 'density' => $res['DENSITY']);
             $this->density->create($account);  
           }else{ $stts = false; }              
        }
        return $stts;
    }
    
    
    // ===================================== calibration ==================================
    
    function add_calibration($tankid=null){
        
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){

	// Form validation
        $this->form_validation->set_rules('theight', 'Height', 'required|numeric');
        $this->form_validation->set_rules('tvolume', 'Volume', 'required|numeric');

        if ($this->form_validation->run($this) == TRUE)
        {
            $param = '{ "h":"'.floatval($this->input->post('theight')/100).'", "V":"'.$this->input->post('tvolume').'" }';
            $response = $this->api->request(api.'/'.$tankid.'/kalibrasi/', $param, 'code', 'POST');
            $result = (array) json_decode($response[0], true);

            if ($response[1] == 200){
               echo 'true|density '.$result['title'].'|'.$response[1];
            }else{ echo 'error|'.$result['title'].'|'.$response[1]; }
        }
        else{ echo 'error|'.validation_errors(); }

        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; } 
    }
    
    function remove_calibration($tankid=null,$calibrationid=null){
        
        $response = $this->api->request(api.'/'.$tankid.'/kalibrasi/'.$calibrationid, null, 'code', 'DELETE');
        $result = (array) json_decode($response[0], true);
        
        if ($response[1] == 200){
           echo 'true|calibration '.$result['title'];
        }else{ echo 'error|failed to remove data'; }
    }
    
    function calibration_update($tankid=null,$calibration=null){
        
        $response = $this->api->request(api.'/'.$tankid.'/kalibrasi/'.$calibration, null, 'code', 'GET');
        $result = (array) json_decode($response[0], true);
        if ($response[1] == 200){ echo 'true|'.$result['h'].'|'.$result['V'];}
        else{ echo 'false|0|0'; }
    }
    
    function calibration_update_process($tankid=null)
    {
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){

	// Form validation
        $this->form_validation->set_rules('theight', 'Height', 'required|numeric');
        $this->form_validation->set_rules('tvolume', 'Volume', 'required|numeric');
        $this->form_validation->set_rules('tid', 'ID', 'required');

        if ($this->form_validation->run($this) == TRUE)
        {
            $param = '{ "h":"'.floatval($this->input->post('theight')/100).'", "V":"'.$this->input->post('tvolume').'" }';
            $response = $this->api->request(api.'/'.$tankid.'/kalibrasi/'.$this->input->post('tid'), $param, 'code', 'PUT');
            $result = (array) json_decode($response[0], true);
            
             if ($response[1] == 200){
                echo 'true|density '.$result['title'];
             }else{ echo 'error|failed to update data'; }
        }
        else{ echo 'error|'.validation_errors(); }

        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    function fetch_callibration($uniqueid=null){
         
        $response = $this->api->request(api.'/kalibrasi/'.$uniqueid, null, 'code','GET');
        $result = (array) json_decode($response[0], true);
        $this->api->response($result[0]['kalibrasi']);
    }
    
    // ===================================== cincin process ===============================
    
    function fetch_ring($uniqueid=null){
         
        $response = $this->api->request(api.'/cincin/'.$uniqueid, null, 'code','GET');
        $result = (array) json_decode($response[0], true);
        $this->api->response($result);
    }

    function add_ring($tankid=null){
        
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){

	// Form validation
        $this->form_validation->set_rules('tringno', 'Ring No', 'required|numeric');
        $this->form_validation->set_rules('tstart', 'Height Start', 'required|numeric');
        $this->form_validation->set_rules('tend', 'Height End', 'required|numeric');

        if ($this->form_validation->run($this) == TRUE)
        {
            $param = '{ "ringNo":"'.$this->input->post('tringno').'", "h_start":"'.$this->input->post('tstart').'", "h_end":"'.$this->input->post('tend').'", "precision": [ {"h":1,"V":0}, {"h":2,"V":0}, {"h":3,"V":0}, {"h":4,"V":0}, {"h":5,"V":0}, {"h":6,"V":0}, {"h":7,"V":0}, {"h":8,"V":0}, {"h":9,"V":0}, {"h":10,"V":0} ] }';
            $response = $this->api->request(api.'/'.$tankid, $param, 'code', 'POST');
            $result = (array) json_decode($response[0], true);

            if ($response[1] == 200){
               echo 'true|density '.$result['title'].'|'.$response[1];
            }else{ echo 'error|'.$result['title'].'|'.$response[1]; }
        }
        else{ echo 'error|'.validation_errors(); }

        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; } 
    }
    
    function remove_cincin($tankid=null,$cincinid=null){
        
        $response = $this->api->request(api.'/'.$tankid.'/cincin/'.$cincinid, null, 'code', 'DELETE');
        $result = (array) json_decode($response[0], true);
        
        if ($response[1] == 200){
           echo 'true|ring '.$result['title'];
        }else{ echo 'error|failed to remove data'; }
    }
    
    function cincin_update($tankid=null,$cincin=null){
        
        $response = $this->api->request(api.'/'.$tankid.'/cincin/'.$cincin, null, 'code', 'GET');
        $result = (array) json_decode($response[0], true);
        if ($response[1] == 200){ 
            $res1 = $result['ringNo'].'|'.$result['h_start'].'|'.$result['h_end'];
            $res2 = null;
            foreach ($result['precision'] as $res) {
                $res2 = $res2.$res['_id'].'-'.$res['h'].'-'.$res['V'].'|';
            }
            echo 'true|'.$res1.':'.$res2;
        }
        else{ echo 'false|0|0'; }
    }
    
    function update_presisi($tankid,$cincinid,$presisi,$value){
                
        $param = '{ "h":"1", "V":"'.$value.'" }';
        $response = $this->api->request(api.'/'.$tankid.'/cincin/'.$cincinid.'/presisi/'.$presisi, $param, 'code', 'PUT');
       
        $result = (array) json_decode($response[0], true);
        
        if ($response[1] == 200){
            echo 'true|presisi '.$result['title'].'|'.$response[1];
        }else{ echo 'error|'.$result['title'].'|'.$response[1]; }
    }
    
    function cincin_update_process($tankid=null)
    {
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){

	// Form validation
        $this->form_validation->set_rules('tringno', 'Ring No', 'required|numeric');
        $this->form_validation->set_rules('tstart', 'Height Start', 'required|numeric');
        $this->form_validation->set_rules('tend', 'Height End', 'required|numeric');

        if ($this->form_validation->run($this) == TRUE)
        {
        $param = '{ "ringNo":"'.$this->input->post('tringno').'", "h_start":"'.$this->input->post('tstart').'", "h_end":"'.$this->input->post('tend').'"}';
        $response = $this->api->request(api.'/'.$tankid.'/cincin/'.$this->input->post('tid'), $param, 'code', 'PUT');
            $result = (array) json_decode($response[0], true);
            
             if ($response[1] == 200){
                echo 'true|density '.$result['title'];
             }else{ echo 'error|failed to update data'; }
        }
        else{ echo 'error|'.validation_errors(); }

        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    //    ================================= ledger ==================================
    
    function ledger()
    {
        $this->acl->otentikasi1($this->title);
        
        $pname = null;
        $pid = null;
        if ($this->input->post('titem')){ $pid = $this->tank->get_id_by_sku($this->input->post('titem')); $pname = $this->input->post('titem');}
        $period = $this->input->post('reservation');
        if (!$period){ $start = date('Y-m-01');  $end = date('Y-m-t'); }
        else { 
            $start = picker_between_split($period, 0);
            $end = picker_between_split($period, 1);
        }
        
        $this->session->set_userdata('start',$start);
        $this->session->set_userdata('end',$end);
        
        $data['source'] = site_url($this->title.'/getledger/'.$pid);
        $data['graph'] = site_url($this->title."/chart_ledger/".$pid);
        
//        print_r($data['graph']);
        
        $data['title'] = $this->properti['name'].' | Administrator Storage Tank Ledger'.strtoupper($pname);
        $data['h2title'] = 'Tank Ledger : '.$pname;
        $data['main_view'] = 'tank_ledger';
	$data['form_action'] = site_url($this->title.'/ledger');
        $data['link'] = array('link_back' => anchor($this->title,'Back', array('class' => 'btn btn-danger')));
//
        $data['array'] = array('','');
	// ---------------------------------------- //
 
        $config['first_tag_open'] = $config['last_tag_open']= $config['next_tag_open']= $config['prev_tag_open'] = $config['num_tag_open'] = '<li>';
        $config['first_tag_close'] = $config['last_tag_close']= $config['next_tag_close']= $config['prev_tag_close'] = $config['num_tag_close'] = '</li>';

        $config['cur_tag_open'] = "<li><span><b>";
        $config['cur_tag_close'] = "</b></span></li>";

        // library HTML table untuk membuat template table class zebra
        $tmpl = array('table_open' => '<table id="datatable-buttons" class="table table-striped table-bordered">');

        $this->table->set_template($tmpl);
        $this->table->set_empty("&nbsp;");

        //Set heading untuk table
        $this->table->set_heading('No', 'Docno', 'Doctype', 'Date', 'Cur', 'Debit', 'Credit');
        $data['table'] = $this->table->generate();
        
        
        if ($this->input->post('titem')){
           $trans = $this->ledger->get_ledger_interval($pid, $start, $end, 'sum');
           $mutation = floatval($trans['debit'])-floatval($trans['credit']);
        
           $data['begin'] = $this->ledger->get_prev_balance($pid,$start); 
           $data['debit'] = floatval($trans['debit']); $data['credit'] = floatval($trans['credit']);
           $data['mutation'] = $mutation;
           $data['end'] = $this->ledger->get_prev_balance($pid,$start)+$mutation;
           
        }else{ $data['begin'] = 0; $data['debit']=0; $data['credit']=0; $data['mutation']=0; $data['end']=0; }
                    
        if ($this->input->post('bsubmit') == 'card'){
            redirect($this->title.'/stock_card_report/'.$pid);
        }else{ $this->load->view('template', $data); }
    }
    
    public function getledger($tank=null)
    {   
        if ($tank){ $result = $this->ledger->get_ledger_interval($tank, $this->session->userdata('start'), $this->session->userdata('end'))->result(); 
        
            $output = null;
            if ($result){

             foreach($result as $res)
             {   
               $output[] = array ($res->id, $res->docno, $res->doctype, tglin($res->dates), strtoupper($res->currency),
                                  $res->debit, $res->credit, $res->dirt, $res->ffa, $res->moisture);
             } 

            $this->output
             ->set_status_header(200)
             ->set_content_type('application/json', 'utf-8')
             ->set_output(json_encode($output, JSON_PRETTY_PRINT))
             ->_display();
             exit;  
            }
        }
        
    }
    
    function chart_ledger($pid=null)
    {   
        if ($pid){
            
        $rest = 0;    
        
        $opening = $this->ledger->get_prev_balance($pid,$this->session->userdata('start')); 
        
//        $opening = floatval($this->balance->get($pid, $this->period->month, $this->period->year, 'beginning'));        
        $data = $this->ledger->get_ledger_interval($pid, $this->session->userdata('start'), $this->session->userdata('end'))->result();  
        $datax = array();
        foreach ($data as $res) 
        {  
           if ($res->debit > 0){ $rest = intval($rest+$res->debit); }
           if ($res->credit > 0){ $rest = intval($rest-$res->credit); }
           $point = array("label" => tglin($res->dates) , "y" => intval($opening+$rest));
           array_push($datax, $point);      
        }
        echo json_encode($datax, JSON_NUMERIC_CHECK);
        }
    }
    
    function calculate_balance($type=null){ 
       if ($this->ledger->calculate() == TRUE){ if ($type){ redirect($this->title.'/ledger');  } };
    }
    
}

?>