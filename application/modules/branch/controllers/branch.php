<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Branch extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('Branch_model', 'model', TRUE);

        $this->properti = $this->property->get();
        $this->acl->otentikasi();

        $this->modul = $this->components->get(strtolower(get_class($this)));
        $this->title = strtolower(get_class($this));
        $this->product = new Product_lib();
        $this->city = new City_lib();
        $this->conversion = new Conversion_lib();
        $this->account = new Account_lib();
    }

    private $properti, $modul, $title;
    private $city,$conversion,$account;

    function index()
    {
       $this->get_last_branch(); 
    }
    
    public function getdatatable($search=null)
    {
        if(!$search){ $result = $this->model->get_last($this->modul['limit'])->result(); }
        
        if ($result){
	foreach($result as $res)
	{
	   $output[] = array ($res->id, $res->code, $res->name, $res->address, $res->phone, $res->mobile, $res->email, $res->city, $res->zip, base_url().'images/branch/'.$res->image, $res->publish,
                             $res->defaults, $res->sales_account, $res->stock_account);
	}
            $this->output
            ->set_status_header(200)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($output))
            ->_display();
            exit; 
        }
    }
    
    function publish($uid = null)
    {
       if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){ 
       $val = $this->model->get_by_id($uid)->row();
       if ($val->publish == 0){ $lng = array('publish' => 1); }else { $lng = array('publish' => 0); }
       $this->model->update($uid,$lng);
       echo 'true|Status Changed...!';
       }else{ echo "error|Sorry, you do not have the right to change publish status..!"; }
    }
    
    function defaults($uid = null)
    {        
       if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){ 
           
        $val = $this->model->get_default()->row();
        $lng = array('defaults' => 0);
        $this->model->update($val->id,$lng);

        $lng = array('defaults' => 1);
        $this->model->update($uid,$lng);  
        echo 'true|Defaults Changed..!';
           
       }else{ echo "error|Sorry, you do not have the right to change publish status..!"; }
    }

    function get_last_branch()
    {
        $this->acl->otentikasi1($this->title);

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'branch_view';
	$data['form_action'] = site_url($this->title.'/add_process');
        $data['form_action_update'] = site_url($this->title.'/update_process');
        $data['form_action_del'] = site_url($this->title.'/delete_all');
        $data['link'] = array('link_back' => anchor('main/','Back', array('class' => 'btn btn-danger')));
        $data['city'] = $this->city->combo_city_name();
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
        $this->table->set_heading('#','No', 'Code', 'Name', 'Phone', 'Address', 'City', 'Action');

        $data['table'] = $this->table->generate();
        $data['source'] = site_url('branch/getdatatable');
            
        // Load absen view dengan melewatkan var $data sbgai parameter
	$this->load->view('template', $data);
    }
    
    function delete_all()
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
           if ( $this->cek_relation($cek[$i]) == TRUE ) 
           {
              $img = $this->model->get_by_id($cek[$i])->row();
              $img = $img->image;
              if ($img){ $img = "./images/branch/".$img; unlink("$img"); }

              $this->model->delete($cek[$i]); 
           }
           else { $x=$x+1; }
           
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
      }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }

    function delete($uid,$type='soft')
    {
        if ($this->acl->otentikasi_admin($this->title,'ajax') == TRUE){
        if ($type == 'soft'){
           $this->model->delete($uid);
           $this->session->set_flashdata('message', "1 $this->title successfully removed..!");
           
           echo "true|1 $this->title successfully soft removed..!";
       }
       else
       {
        if ( $this->cek_relation($uid) == TRUE )
        {
           $img = $this->model->get_by_id($uid)->row();
           $img = $img->image;
           if ($img){ $img = "./images/branch/".$img; unlink("$img"); }

           $this->model->delete($uid);
           $this->session->set_flashdata('message', "1 $this->title successfully removed..!");
           
           echo "true|1 $this->title successfully removed..!";
        }
        else { $this->session->set_flashdata('message', "$this->title related to another component..!"); 
        echo  "invalid|$this->title related to another component..!";} 
       }
       }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }

    private function cek_relation($id)
    {
        $product = $this->product->cek_relation($id, $this->title);
        if ($product == TRUE) { return TRUE; } else { return FALSE; }
    }

    function add_process()
    {
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'branch_view';
	$data['form_action'] = site_url($this->title.'/add_process');
	$data['link'] = array('link_back' => anchor('branch/','<span>back</span>', array('class' => 'back')));
        

	// Form validation
        $this->form_validation->set_rules('tcode', 'Name', 'required|callback_valid_branch');
        $this->form_validation->set_rules('tname', 'Name', 'required');
        $this->form_validation->set_rules('tmail', 'Email', '');
        $this->form_validation->set_rules('tphone', 'Phone', '');
        $this->form_validation->set_rules('tmobile', 'Mobile', '');
        $this->form_validation->set_rules('ccity', 'Mobile', 'required');
        $this->form_validation->set_rules('tzip', 'Zip', '');
        $this->form_validation->set_rules('taddress', 'Address', '');
        $this->form_validation->set_rules('tsalesacc', 'Sales Account', 'required');
        $this->form_validation->set_rules('tstockacc', 'Stock Account', 'required');
        $this->form_validation->set_rules('tunitacc', 'Unit Cost Account', 'required');
        $this->form_validation->set_rules('taracc', 'AR-Account', 'required');
        $this->form_validation->set_rules('tbankacc', 'Bank-Account', 'required');
        $this->form_validation->set_rules('tcashacc', 'Cash-Account', 'required');

        if ($this->form_validation->run($this) == TRUE)
        {
            $config['upload_path'] = './images/branch/';
            $config['file_name'] = strtolower($this->input->post('tname'));
            $config['allowed_types'] = 'jpg|gif|png';
            $config['overwrite'] = true;
            $config['max_size']	= '1000';
            $config['max_width']  = '3000';
            $config['max_height']  = '3000';
            $config['remove_spaces'] = TRUE;

            $this->load->library('upload', $config);
//
            if ( !$this->upload->do_upload("userfile")) // if upload failure
            {
                $info['file_name'] = null;
                $data['error'] = $this->upload->display_errors();
                $branch = array('name' => strtolower($this->input->post('tname')), 'code' => $this->input->post('tcode'),
                                'address' => $this->input->post('taddress'),
                                'phone' => $this->input->post('tphone'), 'mobile' => $this->input->post('tmobile'),
                                'email' => $this->input->post('tmail'), 'city' => $this->input->post('ccity'),
                                'zip' => $this->input->post('tzip'),
                                'sales_account' => $this->account->get_id_code($this->input->post('tsalesacc')),
                                'stock_account' => $this->account->get_id_code($this->input->post('tstockacc')),
                                'unit_cost_account' => $this->account->get_id_code($this->input->post('tunitacc')),
                                'ar_account' => $this->account->get_id_code($this->input->post('taracc')),
                                'bank_account' => $this->account->get_id_code($this->input->post('tbankacc')),
                                'cash_account' => $this->account->get_id_code($this->input->post('tcashacc')),
                                'image' => null, 'created' => date('Y-m-d H:i:s'));
            }
            else
            {
                $info = $this->upload->data();
                $branch = array('name' => strtolower($this->input->post('tname')), 'code' => $this->input->post('tcode'),
                                'address' => $this->input->post('taddress'),
                                'phone' => $this->input->post('tphone'), 'mobile' => $this->input->post('tmobile'),
                                'email' => $this->input->post('tmail'), 'city' => $this->input->post('ccity'),
                                'zip' => $this->input->post('tzip'),
                                'sales_account' => $this->account->get_id_code($this->input->post('tsalesacc')),
                                'stock_account' => $this->account->get_id_code($this->input->post('tstockacc')),
                                'unit_cost_account' => $this->account->get_id_code($this->input->post('tunitacc')),
                                'ar_account' => $this->account->get_id_code($this->input->post('taracc')),
                                'bank_account' => $this->account->get_id_code($this->input->post('tbankacc')),
                                'cash_account' => $this->account->get_id_code($this->input->post('tcashacc')),
                                'image' => $info['file_name'], 'created' => date('Y-m-d H:i:s'));
            }

            $this->model->add($branch);
            $this->session->set_flashdata('message', "One $this->title data successfully saved!");
//            redirect($this->title);
            
            if ($this->upload->display_errors()){ echo "warning|".$this->upload->display_errors(); }
            else { echo 'true|'.$this->title.' successfully saved..!|'.base_url().'images/branch/'.$info['file_name']; }
            
          //  echo 'true';
        }
        else{ echo "error|".validation_errors(); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }

    }

    // Fungsi update untuk menset texfield dengan nilai dari database
    function update($uid=null)
    {        
        $branch = $this->model->get_by_id($uid)->row();
	$this->session->set_userdata('langid', $branch->id);
        
        echo $uid.'|'.$branch->code.'|'.$branch->name.'|'.$branch->address.'|'.
             $branch->phone.'|'.$branch->mobile.'|'.$branch->email.'|'.$branch->city.'|',
             $branch->zip.'|'.base_url().'images/branch/'.$branch->image.'|'. $this->account->get_code($branch->sales_account).'|'.
             $this->account->get_code($branch->stock_account).'|'.$this->account->get_code($branch->unit_cost_account).'|'.
             $this->account->get_code($branch->ar_account).'|'.$this->account->get_code($branch->bank_account).'|'.
             $this->account->get_code($branch->cash_account);
    }


    public function valid_branch($name)
    {
        if ($this->model->valid('code',$name) == FALSE)
        {
            $this->form_validation->set_message('valid_branch', "This $this->title is already registered.!");
            return FALSE;
        }
        else{ return TRUE; }
    }

    function validation_branch($name)
    {
	$id = $this->session->userdata('langid');
	if ($this->model->validating('code',$name,$id) == FALSE)
        {
            $this->form_validation->set_message('validation_branch', 'This branch is already registered!');
            return FALSE;
        }
        else { return TRUE; }
    }

    // Fungsi update untuk mengupdate db
    function update_process()
    {
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'branch_update';
	$data['form_action'] = site_url($this->title.'/update_process');
	$data['link'] = array('link_back' => anchor('branch/','<span>back</span>', array('class' => 'back')));

	// Form validation
        $this->form_validation->set_rules('tcode', 'Name', 'required|callback_validation_branch');
        $this->form_validation->set_rules('tname', 'Name', 'required');
        $this->form_validation->set_rules('tmail', 'Email', '');
        $this->form_validation->set_rules('tphone', 'Phone', '');
        $this->form_validation->set_rules('tmobile', 'Mobile', '');
        $this->form_validation->set_rules('ccity', 'City', '');
        $this->form_validation->set_rules('tzip', 'Zip', '');
        $this->form_validation->set_rules('taddress', 'Address', '');
        $this->form_validation->set_rules('tsalesacc', 'Sales Account', 'required');
        $this->form_validation->set_rules('tstockacc', 'Stock Account', 'required');
        $this->form_validation->set_rules('tunitacc', 'Unit Cost Account', 'required');
        $this->form_validation->set_rules('taracc', 'AR-Account', 'required');
        $this->form_validation->set_rules('tbankacc', 'Bank-Account', 'required');
        $this->form_validation->set_rules('tcashacc', 'Cash-Account', 'required');

        if ($this->form_validation->run($this) == TRUE)
        {
            $config['upload_path'] = './images/branch/';
            $config['file_name'] = strtolower($this->input->post('tname_update'));
            $config['allowed_types'] = 'gif|jpg|png';
            $config['overwrite'] = true;
            $config['max_size']	= '10000';
            $config['max_width']  = '10000';
            $config['max_height']  = '10000';
            $config['remove_spaces'] = TRUE;

            $this->load->library('upload', $config);

            if ( !$this->upload->do_upload("userfile_update")) // if upload failure
            {
                $data['error'] = $this->upload->display_errors();
                $branch = array('name' => strtolower($this->input->post('tname')), 'code' => $this->input->post('tcode'),
                                'address' => $this->input->post('taddress'),
                                'phone' => $this->input->post('tphone'), 'mobile' => $this->input->post('tmobile'),
                                'email' => $this->input->post('tmail'), 'city' => $this->input->post('ccity'),
                                'sales_account' => $this->account->get_id_code($this->input->post('tsalesacc')),
                                'stock_account' => $this->account->get_id_code($this->input->post('tstockacc')),
                                'unit_cost_account' => $this->account->get_id_code($this->input->post('tunitacc')),
                                'ar_account' => $this->account->get_id_code($this->input->post('taracc')),
                                'bank_account' => $this->account->get_id_code($this->input->post('tbankacc')),
                                'cash_account' => $this->account->get_id_code($this->input->post('tcashacc')),
                                'zip' => $this->input->post('tzip'));
                
                $img = null;
            }
            else
            {
                $info = $this->upload->data();                
                $branch = array('name' => strtolower($this->input->post('tname')), 'code' => $this->input->post('tcode'),
                                'address' => $this->input->post('taddress'),
                                'phone' => $this->input->post('tphone'), 'mobile' => $this->input->post('tmobile'),
                                'email' => $this->input->post('tmail'), 'city' => $this->input->post('ccity'),
                                'zip' => $this->input->post('tzip'),
                                'sales_account' => $this->account->get_id_code($this->input->post('tsalesacc')),
                                'stock_account' => $this->account->get_id_code($this->input->post('tstockacc')),
                                'unit_cost_account' => $this->account->get_id_code($this->input->post('tunitacc')),
                                'ar_account' => $this->account->get_id_code($this->input->post('taracc')),
                                'bank_account' => $this->account->get_id_code($this->input->post('tbankacc')),
                                'cash_account' => $this->account->get_id_code($this->input->post('tcashacc')),
                                'image' => $info['file_name']);
                
                $img = base_url().'images/branch/'.$info['file_name'];
            }

	    $this->model->update($this->session->userdata('langid'), $branch);
            $this->session->set_flashdata('message', "One $this->title has successfully updated!");
            
            if ($this->upload->display_errors()){ echo "warning|".$this->upload->display_errors(); }
            else { echo 'true|Data successfully saved..!|'.base_url().'images/branch/'.$info['file_name']; }
            
        }
        else{ echo 'error|'.validation_errors(); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    function remove_image($uid)
    {
       $img = $this->model->get_by_id($uid)->row();
       $img = $img->image;
       if ($img){ $img = "./images/branch/".$img; unlink("$img"); } 
    }
    
    // ====================================== CLOSING ======================================
    function reset_process(){ $this->model->closing_defaults(); }

}

?>