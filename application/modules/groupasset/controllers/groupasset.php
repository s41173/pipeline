<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Groupasset extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('Asset_model', 'model', TRUE);

        $this->properti = $this->property->get();
        $this->acl->otentikasi();

        $this->modul = $this->components->get(strtolower(get_class($this)));
        $this->title = strtolower(get_class($this));
        $this->product = new Product_lib();
        $this->city = new City_lib();
        $this->conversion = new Conversion_lib();
        $this->account = new Account_lib();
        $this->asset = new Asset_lib();
    }

    private $properti, $modul, $title;
    private $city,$conversion,$account,$asset;

    function index()
    {
       $this->get_last_asset(); 
    }
    
    public function getdatatable($search=null)
    {
        if(!$search){ $result = $this->model->get_last($this->modul['limit'])->result(); }
        
        if ($result){
	foreach($result as $res)
	{
	   $output[] = array ($res->id, $res->code, $res->name, $res->description, $res->period, 
               $this->account->get_code($res->acc_accumulation).':'.$this->account->get_name($res->acc_accumulation),
               $this->account->get_code($res->acc_depreciation).':'.$this->account->get_name($res->acc_depreciation),
               $res->status);
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
       if ($val->status == 0){ $lng = array('status' => 1); }else { $lng = array('status' => 0); }
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

    function get_last_asset()
    {
        $this->acl->otentikasi1($this->title);

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'asset_view';
	$data['form_action'] = site_url($this->title.'/add_process');
        $data['form_action_update'] = site_url($this->title.'/update_process');
        $data['form_action_del'] = site_url($this->title.'/delete_all');
        $data['link'] = array('link_back' => anchor('asset/','Back', array('class' => 'btn btn-danger')));
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
        $this->table->set_heading('#','No', 'Code', 'Name', 'Period', 'Acc-Accumulation', 'Acc-Depreciation', 'Action');

        $data['table'] = $this->table->generate();
        $data['source'] = site_url($this->title.'/getdatatable');
            
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
              if ($img){ $img = "./images/asset/".$img; unlink("$img"); }

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
          if ( $this->asset->cek_relation($uid,'group_id') == TRUE ){
             $this->model->force_delete($uid);
             echo "true|1 $this->title successfully soft removed..!";
          }else{ echo  "invalid|$this->title related to another component..!"; }
       }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }

    function add_process()
    {
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){
	// Form validation
        $this->form_validation->set_rules('tcode', 'Name', 'required|callback_valid_asset');
        $this->form_validation->set_rules('tname', 'Name', 'required|callback_valid_asset_name');
        $this->form_validation->set_rules('tperiod', 'Email', 'numeric|required');
        $this->form_validation->set_rules('tdesc', 'Description', '');
        $this->form_validation->set_rules('taccumulation', 'Accumulation', 'required');
        $this->form_validation->set_rules('tdepreciation', 'Depreciation', 'required');

        if ($this->form_validation->run($this) == TRUE)
        {
           $asset = array('name' => strtolower($this->input->post('tname')), 'code' => $this->input->post('tcode'),
                          'description' => $this->input->post('tdesc'), 'period' => $this->input->post('tperiod'),
                          'acc_accumulation' => $this->account->get_id_code($this->input->post('taccumulation')), 'acc_depreciation' => $this->account->get_id_code($this->input->post('tdepreciation')),
                          'created' => date('Y-m-d H:i:s'));

            $this->model->add($asset);
            echo 'true|'.$this->title.' successfully saved..!';
        }
        else{ echo "error|".validation_errors(); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }

    // Fungsi update untuk menset texfield dengan nilai dari database
    function update($uid=null)
    {        
        $asset = $this->model->get_by_id($uid)->row();
	$this->session->set_userdata('langid', $asset->id);
        
        echo $uid.'|'.$asset->code.'|'.$asset->name.'|'.$asset->description.'|'.
             $asset->period.'|'.$asset->status.'|',
             $this->account->get_code($asset->acc_accumulation).'|'.$this->account->get_code($asset->acc_depreciation);
    }


    public function valid_asset($name)
    {
        if ($this->model->valid('code',$name) == FALSE)
        {
            $this->form_validation->set_message('valid_asset', "This $this->title is already registered.!");
            return FALSE;
        }
        else{ return TRUE; }
    }
    
    public function valid_asset_name($name)
    {
        if ($this->model->valid('name',$name) == FALSE)
        {
            $this->form_validation->set_message('valid_asset_name', "This $this->title is already registered.!");
            return FALSE;
        }
        else{ return TRUE; }
    }

    function validation_asset($name)
    {
	$id = $this->session->userdata('langid');
	if ($this->model->validating('code',$name,$id) == FALSE)
        {
            $this->form_validation->set_message('validation_asset', 'This asset is already registered!');
            return FALSE;
        }
        else { return TRUE; }
    }
    
    function validation_asset_name($name)
    {
	$id = $this->session->userdata('langid');
	if ($this->model->validating('name',$name,$id) == FALSE)
        {
            $this->form_validation->set_message('validation_asset_name', 'This asset is already registered!');
            return FALSE;
        }
        else { return TRUE; }
    }

    // Fungsi update untuk mengupdate db
    function update_process()
    {
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){

	// Form validation
        $this->form_validation->set_rules('tcode', 'Name', 'required|callback_validation_asset');
        $this->form_validation->set_rules('tname', 'Name', 'required|callback_validation_asset_name');
        $this->form_validation->set_rules('tperiod', 'Email', 'numeric|required');
        $this->form_validation->set_rules('tdesc', 'Description', '');
        $this->form_validation->set_rules('taccumulation', 'Accumulation', 'required');
        $this->form_validation->set_rules('tdepreciation', 'Depreciation', 'required');

        if ($this->form_validation->run($this) == TRUE)
        {
            $asset = array('name' => strtolower($this->input->post('tname')), 'code' => $this->input->post('tcode'),
                          'description' => $this->input->post('tdesc'), 'period' => $this->input->post('tperiod'),
                          'acc_accumulation' => $this->account->get_id_code($this->input->post('taccumulation')), 'acc_depreciation' => $this->account->get_id_code($this->input->post('tdepreciation')));

	    $this->model->update($this->session->userdata('langid'), $asset);
            echo 'true|Data successfully saved..!';
        }
        else{ echo 'error|'.validation_errors(); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
        
}

?>