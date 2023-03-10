<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once 'definer.php';

class Sounding extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('Sounding_model', 'model', TRUE);

        $this->properti = $this->property->get();
        $this->acl = new Acl();
        $this->acl->otentikasi();

        $this->modul = $this->components->get(strtolower(get_class($this)));
        $this->title = strtolower(get_class($this));
        $this->role = new Role_lib();
        $this->api = new Api_lib();
        $this->wb = new Wb_lib();
        $this->contract = new Contract_lib();
        $this->registration = new Registration_lib();
        
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');  
    }

    private $properti, $modul, $title, $api;
    private $role,$acl,$wb,$contract,$registration;

    
    function index(){
        $this->session->unset_userdata('start'); $this->session->unset_userdata('end'); 
        $this->get_last(); 
    }
    
    public function getdatatable($search=null,$docno='null')
    {
        if ($search != null){ $result = $this->model->search($docno)->result(); }
        else{ $result = $this->model->get_last($this->modul['limit'])->result(); }
        
        $output = null;
        if ($result){
          
         foreach($result as $res)
	 {
           $register = $this->registration->get_by_id($res->registration_id)->row();  
	   $output[] = array ($res->id, $register->docno, $res->type, $res->source_cm, $res->to_cm,
                              $res->source_temp, $res->to_temp, num_format($res->source_tonase), number_format($res->to_tonase),
                              tglincompletetime($res->created,1));
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
        $data['main_view'] = 'sounding_view';
	$data['form_action'] = site_url($this->title.'/add_process');
        $data['form_action_update'] = site_url($this->title.'/update_process');
        $data['form_action_del'] = site_url($this->title.'/delete_all');
        $data['form_action_report'] = site_url($this->title.'/report_process');
        $data['form_action_import'] = site_url($this->title.'/import');
        $data['link'] = array('link_back' => anchor('main/','Back', array('class' => 'btn btn-danger')));

        $data['contract'] = $this->contract->get_contract_combo();
        $data['docno'] = $this->registration->get_docno_combo();
        $data['array'] = array('','');
        $data['month'] = combo_month();
        $data['default']['month'] = date('m');
        
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
        $this->table->set_heading('#','No', 'Doc-No', 'Type', 'Sounding-Cm', 'Temp', 'Tonase', 'Action');

        $data['table'] = $this->table->generate();
        $data['source'] = site_url($this->title.'/getdatatable');
        $data['graph'] = site_url()."/".$this->title."/chart/";
            
        // Load absen view dengan melewatkan var $data sbgai parameter
	$this->load->view('template', $data);
    }
    
    
    function get_register($regid=0)
    {      
//        $this->acl->otentikasi();
        if ($this->registration->cek_trans('id',$regid) == FALSE){ redirect('registration'); }
        
        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = 'Edit '.$this->modul['title'];
        $data['main_view'] = 'qc_form';
	$data['form_action'] = site_url($this->title.'/update_process/'.$regid);
        $data['form_action_item'] = site_url($this->title.'/add_item/'.$regid);
        $data['form_action_sounding'] = site_url($this->title.'/add_sounding_item/'.$regid);
        $data['link'] = array('link_back' => anchor('registration','Back', array('class' => 'btn btn-danger')));

        $data['source'] = site_url('registration/getdatatable');
        $data['array'] = array('','');
        $data['graph'] = site_url()."/product/chart/";
        
        $register = $this->registration->get_by_id($regid)->row();
        
        $data['uid'] = $regid;
        $data['default']['code']   = $register->code;
        $data['default']['date']   = $register->dates;
        $data['default']['docno']  = $register->docno;
        $data['default']['type']   = $register->type;
        $data['default']['tank'] = $register->source_tank.' -> '.$register->to_tank;
        
        $data['contract'] = $this->contract->get_contract_combo($regid);
        $data['supplier'] = $this->model->get_supplier();
        
        $data['items'] = $this->model->get_by_registration($regid)->result();
        
        $this->load->view('template', $data);
    }
    
    function valid_confirmation($uid=0){
        $val = $this->model->get_by_id($uid)->row();
        if ($val->validation == 1){
          $this->form_validation->set_message('valid_confirmation', "Transaction Locked, Can't Editable..!");
          return FALSE;
        }else{ return TRUE; }
    }
    
    function add_item($regid=0)
    {
        $this->form_validation->set_rules('ccontract', 'Origin No', 'required');
        $this->form_validation->set_rules('csupplier', 'Supplier', '');
        $this->form_validation->set_rules('tnogk', 'NO GK', 'required');
        $this->form_validation->set_rules('tffa', 'FFA', 'required');
        $this->form_validation->set_rules('tmoist', 'MOIST', 'required');
        $this->form_validation->set_rules('timp', 'IMP', 'required');
        $this->form_validation->set_rules('tdesc', 'Description', '');
        
        $register = $this->registration->get_by_id($regid)->row();
        $type = TRUE;
        if ($register->type != "CARRIAGE"){ $type = FALSE; }

        if ($this->form_validation->run($this) == TRUE && $this->registration->valid_confirmation($regid) == TRUE && $type == TRUE)
        {
            if ($this->input->post('tsupplier') != ""){ $supplier = strtoupper($this->input->post('tsupplier')); }
            else{ $supplier = $this->input->post('csupplier'); }
            
            $pitem = array('registration_id' => $regid, 
                           'contract_id' => $this->input->post('ccontract'),
                           'supplier' => $supplier,
                           'gk_no' => strtoupper($this->input->post('tnogk')),
                           'ffa' => $this->input->post('tffa'),
                           'moist' => $this->input->post('tmoist'),
                           'imp' => $this->input->post('timp'),
                           'description' => $this->input->post('tdesc'),
                           'created' => date('Y-m-d H:i:s')
                          );
            
            $this->model->add($pitem);
            echo 'true';
        }
        elseif ( $type != TRUE ){ echo "error|Can't change value - Invalid Journal Type..!"; }
        elseif ( $this->registration->valid_confirmation($regid) != TRUE ){ echo "error|Can't change value - Journal validated..!"; }
        else{ echo 'error|'.validation_errors(); } 
    }
    
    function delete_item($id)
    {
        if ($this->acl->otentikasi_admin($this->title,'ajax') == TRUE){
            
            $jid = $this->model->get_by_id($id)->row();
            if ( $this->registration->valid_confirmation($jid->registration_id) == TRUE )
            {
                $this->model->force_delete($id);
                echo 'true|Transaction removed..!';
            }
            else{ echo "warning|Journal approved, can't deleted..!"; }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }

    function report_process()
    {
        $this->acl->otentikasi2($this->title);
        $data['title'] = $this->properti['name'].' | Report '.ucwords($this->modul['title']);

        $data['rundate'] = tglin(date('Y-m-d'));
        $data['log'] = $this->session->userdata('log');
        
        $period = $this->input->post('reservation');  
        $start = picker_between_split($period, 0);
        $end = picker_between_split($period, 1);

//        Property Details
        $data['company'] = $this->properti['name'];
        $data['reports'] = $this->model->report($start,$end)->result();
        
        if ($this->input->post('ctype') == 0){ $this->load->view('sounding_report', $data); }
        else { $this->load->view('sounding_pivot', $data); }
    }
        
}

?>