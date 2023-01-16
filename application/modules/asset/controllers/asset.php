<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Asset extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('Asset_model','model',TRUE);

        $this->properti = $this->property->get();
        $this->acl->otentikasi();

        $this->modul = $this->components->get(strtolower(get_class($this)));
        $this->title = strtolower(get_class($this));

        $this->currency = $this->load->library('currency_lib');
        $this->user = $this->load->library('admin_lib');

        $this->account = new Account_lib();
        $this->asset = new Asset_lib();
        $this->group = new Group_asset_lib();
        $this->trans = new Asset_trans_lib();
    }

    private $properti, $modul, $title, $account;
    private $user,$currency,$group,$asset, $trans;
  
    function index(){ $this->get_last(); }
    
    function get_period_group($group){
        $groups = $this->group->get_details($group);
        if ($groups){echo $groups->period;}else{ echo '0'; }
    }
    
    public function getdatatable($search=null)
    {
        if(!$search){ $result = $this->model->get_last($this->modul['limit'])->result(); }
        
        if ($result){
	 foreach($result as $res)
	 { 
	   $output[] = array ($res->id, $res->code, $res->name, $this->group->get_name($res->group_id), tglin($res->purchase_date).' : '.tglin($res->end_date), idr_format($res->amount), idr_format($res->residual), $res->status);
	 }
         $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($output))->_display();
         exit; 
        }
    }

    function get_last()
    {
        $this->acl->otentikasi1($this->title);

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'asset_view';
	$data['form_action'] = site_url($this->title.'/add_process');
        $data['form_action_update'] = site_url($this->title.'/update_process');
        $data['form_action_del'] = site_url($this->title.'/delete_all');
        $data['link'] = array('link_back' => anchor('main/','Back', array('class' => 'btn btn-danger')));
        
        $data['group'] = $this->group->combo_all('.');
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
        $this->table->set_heading('#','No', 'Code', 'Name', 'Group', 'Period', 'Purchase Price', 'Residual', 'Action');

        $data['table'] = $this->table->generate();
        $data['source'] = site_url($this->title.'/getdatatable');
            
        // Load absen view dengan melewatkan var $data sbgai parameter
	$this->load->view('template', $data);
    }
    
   function invoice($uid=null)
   {
       $this->acl->otentikasi2($this->title);
       $asset = $this->model->get_by_id($uid)->row();
       $data['h2title'] = 'Fixed Asset'.$this->modul['title'];

       $data['code'] = $asset->code;
       $data['name'] = $asset->name;
       $data['group'] = $this->group->get_name($asset->group_id);
       $data['purchase'] = tglin($asset->purchase_date);
       $data['amount'] = num_format(floatval($asset->amount-$asset->residual));

       $data['items'] = $this->trans->get($uid);
       $this->load->view('asset_invoice', $data);
   }
    
    function publish($pid)
    { 
      if ($this->acl->otentikasi3($this->title,'ajax') == TRUE){   
        $result = $this->model->get_by_id($pid)->row();    
        if ($result->status == 0){ $val = array('status' => 1); $stts = 'published';  }else{ $val = array('status' => 0); $stts = 'unpublished'; }
        $this->model->update($pid, $val);
        echo "true| 1 $this->title ".$stts."..!";
      }else{ echo "error|Sorry, you do not have the right to change publish status..!"; }
    }
    
    function add_process()
    {
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){

	// Form validation
        $this->form_validation->set_rules('tcode', 'Code', 'required|callback_valid_code');
        $this->form_validation->set_rules('tname', 'Name', 'required|callback_valid_name');
        $this->form_validation->set_rules('cgroup', 'Group', 'required');
        $this->form_validation->set_rules('tdate', 'Purchase Date', 'required|callback_valid_period');
        $this->form_validation->set_rules('tperiod', 'Period', 'required|numeric');
        
        $this->form_validation->set_rules('tamount', 'Purchase Amount', 'required|numeric');
        $this->form_validation->set_rules('tresidual', 'Residual Amount', 'required|numeric|callback_valid_residual');
        $this->form_validation->set_rules('tcost', 'Monthly Cost', 'required|numeric');
        $this->form_validation->set_rules('tmonths', 'Total Months', 'required|numeric');
        
        $this->form_validation->set_rules('titem', 'Accumulation Account', 'required');
        $this->form_validation->set_rules('tdesc', 'Description', '');

        if ($this->form_validation->run($this) == TRUE)
        {        
            $int = $this->input->post('tmonths');
            $end = date('Y-m-d', strtotime($this->input->post('tdate'). ' + '.$int.' month'));
            $date=date_create(date('Y', strtotime($end))."-".date('n', strtotime($end))."-".get_total_days(date('n', strtotime($end))));
            $end_date = date_format($date,"Y-m-d");
            
            $groupasset = array('code' => $this->input->post('tcode'), 'name' => $this->input->post('tname'), 'group_id' => $this->input->post('cgroup'),
                                'purchase_date' => $this->input->post('tdate'), 'end_date' => $end_date, 'amount' => $this->input->post('tamount'),
                                'residual' => $this->input->post('tresidual'), 'monthly_cost' => $this->input->post('tcost'), 'total_month' => $this->input->post('tmonths'),
                                'account' => $this->account->get_id_code($this->input->post('titem')),
                                'description' => $this->input->post('tdesc'));
            
            $this->model->add($groupasset);
            echo "true|One $this->title data successfully saved!";
        }
        else{echo "error|".validation_errors(); }
       }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    function delete($uid)
    {
      if ($this->acl->otentikasi3($this->title,'ajax') == TRUE){
        if ($this->trans->delete_asset($uid) == true){ $this->model->force_delete($uid); echo "true| $this->title successfully removed..!";}
        else{ echo "error|Failed to remove..!"; }
      }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    function update($uid)
    {
        $asset = $this->model->get_by_id($uid)->row();
        $this->session->set_userdata('langid', $uid);
        
        echo $uid.'|'.$asset->code.'|'.$asset->name.'|'.$asset->group_id.'|'.
             $asset->description.'|'.$asset->purchase_date.'|'.intval($asset->total_month/12).'|'.floatval($asset->amount).'|',
             floatval($asset->residual).'|'.floatval($asset->monthly_cost).'|'.$asset->total_month.'|'.
             $this->account->get_code($asset->account);
    }

    // Fungsi update untuk mengupdate db
    function update_process()
    {
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){

	// Form validation
        $this->form_validation->set_rules('tcode', 'Code', 'required|callback_validating_code');
        $this->form_validation->set_rules('tname', 'Name', 'required|callback_validating_name');
        $this->form_validation->set_rules('cgroup', 'Group', 'required');
        $this->form_validation->set_rules('tdate', 'Purchase Date', 'required|callback_valid_period');
        $this->form_validation->set_rules('tperiod', 'Period', 'required|numeric');
        
        $this->form_validation->set_rules('tamount', 'Purchase Amount', 'required|numeric');
        $this->form_validation->set_rules('tresidual', 'Residual Amount', 'required|numeric|callback_valid_residual');
        $this->form_validation->set_rules('tcost', 'Monthly Cost', 'required|numeric');
        $this->form_validation->set_rules('tmonths', 'Total Months', 'required|numeric');
        
        $this->form_validation->set_rules('titem', 'Accumulation Account', 'required');
        $this->form_validation->set_rules('tdesc', 'Description', '');

        if ($this->form_validation->run($this) == TRUE)
        {
            $int = $this->input->post('tmonths');
            $end = date('Y-m-d', strtotime($this->input->post('tdate'). ' + '.$int.' month'));
            $date=date_create(date('Y', strtotime($end))."-".date('n', strtotime($end))."-".get_total_days(date('n', strtotime($end))));
            $end_date = date_format($date,"Y-m-d");
            
            $groupasset = array('code' => $this->input->post('tcode'), 'name' => $this->input->post('tname'), 'group_id' => $this->input->post('cgroup'),
                                'purchase_date' => $this->input->post('tdate'), 'end_date' => $end_date, 'amount' => $this->input->post('tamount'),
                                'residual' => $this->input->post('tresidual'), 'monthly_cost' => $this->input->post('tcost'), 'total_month' => $this->input->post('ttotalmonth'),
                                'account' => $this->account->get_id_code($this->input->post('titem')),
                                'description' => $this->input->post('tdesc'));
            
            $this->model->update($this->session->userdata('langid'),$groupasset);
            echo "true|One $this->title data successfully saved!";
            $this->session->unset_userdata('langid');
        }
        else{echo "error|".validation_errors(); }
       }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    public function valid_residual($residual)
    {
        $purchase = $this->input->post('tamount');
        if ($residual > $purchase)
        {
            $this->form_validation->set_message('valid_residual', "Invalid Residual Amount..!");
            return FALSE;
        }
        else{ return TRUE; }
    }
    
    public function valid_period($date=null)
    {
        $p = new Period();
        $p->get();
        
        $month = date('n', strtotime($date));
        $year  = date('Y', strtotime($date));

        if ( intval($p->month) != intval($month) || intval($p->year) != intval($year) )
        { $this->form_validation->set_message('valid_period', "Invalid Period.!"); return FALSE; }
        else {  return TRUE; }
        
//        return FALSE;
    }

    public function valid_code($code)
    {
        $val = $this->model->valid('code',$code);

        if ($val == FALSE)
        {
            $this->form_validation->set_message('valid_code', "Invalid Code..!");
            return FALSE;
        }
        else{ return TRUE; }
    }
    
    public function validating_code($code)
    {
        $val = $this->model->validating('code',$code,$this->session->userdata('langid'));

        if ($val == FALSE)
        {
            $this->form_validation->set_message('validating_code', "Invalid Code..!");
            return FALSE;
        }
        else{ return TRUE; }
    }
    
    public function validating_name($name)
    {
        $val = $this->model->validating('name',$name,$this->session->userdata('langid'));

        if ($val == FALSE)
        {
            $this->form_validation->set_message('validating_name', "Invalid Name..!");
            return FALSE;
        }
        else{ return TRUE; }
    }
    
    public function valid_name($name)
    {
        $val = $this->model->valid('name',$name);

        if ($val == FALSE)
        {
            $this->form_validation->set_message('valid_name', "Invalid Name..!");
            return FALSE;
        }
        else{ return TRUE; }
    }
    
    public function validation_cost($acc)
    {
        $this->model->where_not_in('id', $this->session->userdata('curid'));
        $val = $this->model->where('account_id', $acc)->count();

        if ($val > 0)
        {
            $this->form_validation->set_message('validation_cost', "Invalid Account..!");
            return FALSE;
        }
        else{ return TRUE; }
    }
    
    public function report()
    {
        $data['costs'] = $this->model->get();
        $data['log'] = $this->session->userdata('log');
        $data['company'] = $this->properti['name'];
        $this->load->view('cost_report', $data);
    }

}

?>