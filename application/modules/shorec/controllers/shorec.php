<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once 'definer.php';

class Shorec extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('Shorec_model', 'model', TRUE);
        $this->load->model('Shorec_sounding_model', 'sounding_model', TRUE);

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
        $this->customer = new Customer_lib();
    }

    private $properti, $modul, $title, $api, $customer;
    private $role, $density, $tank, $balance,$ledger;

    // ajax function
    function get_tank_details($tankid,$type){
        echo $this->tank->get_details($tankid, $type);
    }
    
    function index(){
        $this->get_last(); 
    }
         
    public function getdatatable($search=null,$date='null',$cust='null',$type='null')
    {
        if ($search == 'deleted'){ $result = $this->model->get_deleted($this->modul['limit'])->result(); } 
        elseif ($search != 'deleted' && $search != null){ $result = $this->model->search($date,$cust,$type)->result(); }
        else{ $result = $this->model->get_last($this->modul['limit'])->result(); }
        
        $output = null;
        if ($result){
          
         foreach($result as $res)
	 {     
           if ($res->execution == 1){ $exec = 'Y'; }else{ $exec = 'N'; }  
	   $output[] = array ($res->id, tglin($res->dates), $res->docno, $this->get_type($res->type), $this->customer->get_name($res->cust_id), $res->notes, $exec, $res->approved, $res->execution, tglincompletetime($res->eta));
	 } 
         
            $this->output
             ->set_status_header(200)
             ->set_content_type('application/json', 'utf-8')
             ->set_output(json_encode($output))
             ->_display();
             exit;  
        }
    }
    
    private function get_type($val=0){
        switch ($val) {
            case 0: return "Intertank"; break;
            case 1: return "Ship Outward"; break;
            case 2: return "Ship Inward"; break;
            case 3: return "Outward-3-Party"; break;
        }
    }

    function get_last()
    {
        $this->acl->otentikasi1($this->title);

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords('Tank Manager');
        $data['h2title'] = $this->components->get_title($this->title);
        $data['main_view'] = 'shorec_view';
	$data['form_action'] = site_url($this->title.'/add_process');
        $data['form_action_update'] = site_url($this->title.'/update_process');
        $data['form_action_del'] = site_url($this->title.'/delete_all');
        $data['form_action_report'] = site_url($this->title.'/report_process');
        $data['form_action_import'] = site_url($this->title.'/import');
        $data['link'] = array('link_back' => anchor('main/','Back', array('class' => 'btn btn-danger')));

        $data['customer'] = $this->customer->combo();
        $data['array'] = array('','');
        $data['month'] = combo_month();
        $data['default']['month'] = $this->period->month;
        $data['tank'] = $this->tank->combo();
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
        $this->table->set_heading('#','No', 'Date', 'Doc-No', 'Consignee', 'Type', 'ETA', 'Action');

        $data['table'] = $this->table->generate();
        $data['source'] = site_url($this->title.'/getdatatable');
        $data['graph'] = site_url()."/".$this->title."/chart/";
            
        // Load absen view dengan melewatkan var $data sbgai parameter
	$this->load->view('template', $data);
    }
    
    function execution($pid){
        
        if ($this->acl->otentikasi3($this->title,'ajax') == TRUE){
        $journal = $this->model->get_by_id($pid)->row();
        
        if ($journal->execution == 1) { echo "warning|$this->title already executed..!"; }
        elseif ($journal->approved == 0) { echo "warning|$this->title transaction not posted..!"; }
        elseif (date('Y-m-d') < $journal->eta){ echo "error|Not in accordance eta.!"; } 
        elseif ($this->valid_period($journal->dates) == FALSE ){ echo "error|Invalid period..!"; }
        else
        {
           $data = array('execution' => 1);
           if ($this->model->update($pid, $data) == TRUE){ echo "true| $journal->docno executed..!"; }
           else{ echo 'error|Failed To Post Data'; }
           
           // execution function & jurnal accounting and ledger
        }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
        
    }

    function confirmation($pid)
    {
        if ($this->acl->otentikasi3($this->title,'ajax') == TRUE){
        $journal = $this->model->get_by_id($pid)->row();
        $sounding = $this->sounding_model->calculate_diff($pid);
        
        if ($journal->approved == 1) { echo "warning|$this->title already approved..!"; }
        elseif ($journal->eta == NULL || $journal->start_pump == NULL){ echo "error| eta & comm pumping required.!"; } 
        elseif ($journal->diff_sounding == 0 || $journal->diff_sounding == '0' || $journal->diff_sounding == '0'){ echo "error|$this->title has no value..!"; } 
        elseif ($journal->trans_type != $sounding['trans']){ echo "error|Invalid transaction type..!"; } 
        elseif ($journal->eta < $journal->dates){ echo "error|Invalid Interval Journal ETA..!"; }
        elseif ($journal->start_pump < $journal->eta){ echo "error|Invalid Interval Comm Pumping..!"; }
        elseif ($this->valid_period($journal->dates) == FALSE ){ echo "error|Invalid period..!"; }
        else
        {
           $data = array('approved' => 1);
           $this->model->update($pid, $data);
           echo "true| $journal->docno confirmed..!";
        }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
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
                
            $journal = $this->model->get_by_id($uid)->row();
            if ($journal->approved == 1){
                // rollback status
               $data = array('approved' => 0, 'execution' => 0);
               $this->model->update($uid, $data);
               echo "true|1 $this->title successfully rollback..!"; 
            }
            else{ $this->model->delete($uid); echo "true|1 $this->title successfully removed..!";  }    
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
    
    function add_shore_trans($pid=0){
        
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){

        // Form validation
        $this->form_validation->set_rules('tincm_input', 'Before-Sounding', 'required|numeric|is_natural_no_zero');
        $this->form_validation->set_rules('tcorcm_input', 'Before-Corection', 'required|numeric');
        $this->form_validation->set_rules('tacorr_input', 'Before-A.Correction', 'required|numeric|is_natural_no_zero');
        $this->form_validation->set_rules('ttemp_input', 'Before-Temperature', 'required|numeric|is_natural_no_zero');
        $this->form_validation->set_rules('tdensity_input', 'Before-Density', 'required|numeric');
        $this->form_validation->set_rules('tcoeff_input', 'Before-Coeffisien', 'required|numeric');
        $this->form_validation->set_rules('hobv_input', 'Before-OBV', 'required|numeric');
        
        $this->form_validation->set_rules('tadj_input', 'Before-Adjustment', 'required|numeric');
        $this->form_validation->set_rules('tvcv_input', 'Before-VCV', 'required|numeric');
        $this->form_validation->set_rules('ttable56_input', 'Before-Table56', 'required|numeric');
        $this->form_validation->set_rules('hnetkg_input', 'Before-Net Kg', 'required|numeric');
        $this->form_validation->set_rules('tmetricton_input', 'Before-Metrict Ton', '');
        
        $this->form_validation->set_rules('tffa_input', 'Before-FFA', 'required|numeric');
        $this->form_validation->set_rules('tmoisture_input', 'Before-Moisture', 'required|numeric');
        $this->form_validation->set_rules('tdirt_input', 'Before-Dirt', 'required|numeric');

        // ====== after ==========
        $this->form_validation->set_rules('tincm_output', 'After-Sounding', 'required|numeric|is_natural_no_zero');
        $this->form_validation->set_rules('tcorcm_output', 'After-Corection', 'required|numeric');
        $this->form_validation->set_rules('tacorr_output', 'After-A.Correction', 'required|numeric|is_natural_no_zero');
        $this->form_validation->set_rules('ttemp_output', 'After-Temperature', 'required|numeric|is_natural_no_zero');
        $this->form_validation->set_rules('tdensity_output', 'After-Density', 'required|numeric');
        $this->form_validation->set_rules('tcoeff_output', 'After-Coeffisien', 'required|numeric');
        $this->form_validation->set_rules('hobv_output', 'After-OBV', 'required|numeric');
        
        $this->form_validation->set_rules('tadj_output', 'After-Adjustment', 'required|numeric');
        $this->form_validation->set_rules('tvcv_output', 'After-VCV', 'required|numeric');
        $this->form_validation->set_rules('ttable56_output', 'After-Table56', 'required|numeric');
        $this->form_validation->set_rules('hnetkg_output', 'After-Net Kg', 'required|numeric');
        $this->form_validation->set_rules('tmetricton_output', 'After-Metrict Ton', '');
        
        $this->form_validation->set_rules('tffa_output', 'After-FFA', 'required|numeric');
        $this->form_validation->set_rules('tmoisture_output', 'After-Moisture', 'required|numeric');
        $this->form_validation->set_rules('tdirt_output', 'After-Dirt', 'required|numeric');
        
        
        if ($this->form_validation->run($this) == TRUE)
        {   
            $product = array('shore_calculation' => $pid,
                             'in_sounding' => $this->input->post('tincm_input'), 'out_sounding' => $this->input->post('tincm_output'), 
                             'in_corr' => $this->input->post('tcorcm_input'), 'out_corr' => $this->input->post('tcorcm_output'),
                             'in_after_corr' => $this->input->post('tacorr_input'), 'out_after_corr' => $this->input->post('tacorr_output'),
                             'in_temperature' => $this->input->post('ttemp_input'), 'out_temperature' => $this->input->post('ttemp_output'),
                             'in_density' => $this->input->post('tdensity_input'), 'out_density' => $this->input->post('tdensity_output'),
                             'in_coeff' => $this->input->post('tcoeff_input'), 'out_coeff' => $this->input->post('tcoeff_output'),
                             'in_obv' => $this->input->post('hobv_input'), 'out_obv' => $this->input->post('hobv_output'),
                             'in_adj' => $this->input->post('tadj_input'), 'out_adj' => $this->input->post('tadj_output'),
                             'in_vcv' => $this->input->post('tvcv_input'), 'out_vcv' => $this->input->post('tvcv_output'),
                             'in_table56' => $this->input->post('ttable56_input'), 'out_table56' => $this->input->post('ttable56_output'),
                             'in_netkg' => $this->input->post('hnetkg_input'), 'out_netkg' => $this->input->post('hnetkg_output'),  
                             'in_ffa' => $this->input->post('tffa_input'), 'out_ffa' => $this->input->post('tffa_output'),  
                             'in_moisture' => $this->input->post('tmoisture_input'), 'out_moisture' => $this->input->post('tmoisture_output'),  
                             'in_dirt' => $this->input->post('tdirt_input'), 'out_dirt' => $this->input->post('tdirt_output'),  
                             'created' => date('Y-m-d H:i:s'));
            
            if ($this->sounding_model->valid('shore_calculation',$pid) == TRUE){ $result = $this->sounding_model->add($product);
            }else{ $result = $this->sounding_model->update_by_shore($pid,$product);}
            
            if ($result == TRUE){
              $diff = $this->sounding_model->calculate_diff($pid);
              $shore = array('diff_sounding' => $diff['sounding'],
                             'diff_obv' => $diff['obv'], 'diff_netkg' => $diff['netkg']);
              if ($this->model->update($pid, $shore) == TRUE){  echo 'true|sounding transaction successfully saved..!'; }
              else{  echo 'true|'.$this->title.' successfully saved..!'; }
            
            }else{ echo "error|Failure Save Transaction";}
        }
        else{ echo "error|".validation_errors(); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; } 
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
        $this->form_validation->set_rules('tno', 'Document No', 'callback_valid_docno');
        $this->form_validation->set_rules('tdate', 'Transaction date', 'required|callback_valid_period');
        $this->form_validation->set_rules('tnote', 'Note / Remarks', 'required');
        $this->form_validation->set_rules('ccust', 'Consignee', 'required');

        if ($this->form_validation->run($this) == TRUE)
        {
            $product = array('docno' => strtoupper($this->input->post('tno')),
                             'dates' => $this->input->post('tdate'), 'notes' => $this->input->post('tnote'), 
                             'cust_id' => $this->input->post('ccust'),
                             'created' => date('Y-m-d H:i:s'));
            
            if ($this->model->add($product) == TRUE){echo 'true|'.$this->title.' successfully saved..!';}
        }
        else{ echo "error|".validation_errors(); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }    
    
    
   function invoice($pid,$docno=null)
   {
        $this->acl->otentikasi1($this->title);
        
        if ($docno){
           $product = $this->model->get_by_docno($docno)->row(); 
           $pid = $product->id;
        }else{ $product = $this->model->get_by_id($pid)->row();}
        
        $this->model->valid_add_trans($pid, $this->title);
        
        $data['title'] = $this->properti['name'].' | Administrator '.ucwords($this->modul['title']);
        $data['dates'] = $product->dates;
        $data['codetrans'] = $product->docno;
        $data['user'] = $this->session->userdata("username");

        if ($product->fuel == 1){ $fuel = 'Y'; }else{ $fuel = "-"; }
        if ($product->oil_boom == 1){ $boom = 'Y'; }else{ $boom = "-"; }
        
        $data['uid'] = $pid;
        $data['dates'] = $product->dates;
        $data['docno'] = $product->docno;
        $data['instructionno'] = $product->instructionno;
        $data['type'] = $this->get_type($product->type);
        $data['source'] = $this->tank->get_details($product->tank_source,'sku');
        $data['vessel'] = $this->tank->get_details($product->vessel,'sku');
        $data['shipper'] = $product->shipper;
        $data['cust_id'] = $this->customer->get_name($product->cust_id);
        $data['content'] = $product->content;
        $data['notes'] = $product->notes;
        
        $data['fuel'] = $fuel;  
        $data['oil_boom'] = $boom;
        
        $data['eta'] = tglincompletetime($product->eta);
        $data['etb'] = tglincompletetime($product->etb);
        $data['laycan'] = tglincompletetime($product->laycan);
        $data['until'] = tglincompletetime($product->until);
        $data['heating'] = tglincompletetime($product->heating);
        $data['heating_until'] = tglincompletetime($product->heating_until);
        $data['comm_pumping'] = tglincompletetime($product->start_pump);
        $data['comp_pumping'] = tglincompletetime($product->end_pump);
        
        $data['shore_line_cond'] = $product->shore_line_cond;
        $data['before_load'] = $product->before_load;
        $data['cleaning_sys'] = $product->cleaning_sys;
        $data['after_load'] = $product->after_load;
        
        $data['ship_name'] = $product->ship_name;
        $data['ship_rep'] = $product->ship_rep;
        $data['ship_company'] = $product->ship_company;
        $data['buyer_name'] = $product->buyer_name;
        $data['buyer_rep'] = $product->buyer_rep;
        $data['buyer_company'] = $product->buyer_company;
        $data['log'] = $this->session->userdata('log');
        
        // ========================= shore trans ==========================
        
        $trans = $this->sounding_model->get($pid)->row();
        if ($trans){
            
            $data['tincm_input'] = $trans->in_sounding;
            $data['tincm_output'] = $trans->out_sounding;
            $data['tcorcm_input'] = $trans->in_corr;
            $data['tcorcm_output'] = $trans->out_corr;
            $data['tacorr_input'] = $trans->in_after_corr;
            $data['tacorr_output'] = $trans->out_after_corr;
            $data['ttemp_input'] = $trans->in_temperature;
            $data['ttemp_output'] = $trans->out_temperature;

            $data['tdensity_input'] = $trans->in_density;
            $data['tdensity_output'] = $trans->out_density;
            $data['tcoeff_input'] = $trans->in_coeff;
            $data['tcoeff_output'] = $trans->out_coeff;
            $data['tobv_input'] = $trans->in_obv;
            $data['tobv_output'] = $trans->out_obv;
            $data['tadj_input'] = $trans->in_adj;
            $data['tadj_output'] = $trans->out_adj;

            $data['tvcv_input'] = $trans->in_vcv;
            $data['tvcv_output'] = $trans->out_vcv;
            $data['ttable56_input'] = $trans->in_table56;
            $data['ttable56_output'] = $trans->out_table56;
            $data['tnetkg_input'] = $trans->in_netkg;
            $data['tnetkg_output'] = $trans->out_netkg;
            $data['tffa_input'] = $trans->in_ffa;
            $data['tffa_output'] = $trans->out_ffa;
            $data['tmoisture_input'] = $trans->in_moisture;
            $data['tmoisture_output'] = $trans->out_moisture;
            $data['tdirt_input'] = $trans->in_dirt;
            $data['tdirt_output'] = $trans->out_dirt;
            
            $data['diff_net'] = abs($product->diff_netkg);
            $data['diff_obv'] = abs($product->diff_obv);
        }else{
            $data['tincm_input'] = '';
            $data['tincm_output'] = '';
            $data['tcorcm_input'] = '';
            $data['tcorcm_output'] = '';
            $data['tacorr_input'] = '';
            $data['tacorr_output'] = '';
            $data['ttemp_input'] = '';
            $data['ttemp_output'] = '';

            $data['tdensity_input'] = '';
            $data['tdensity_output'] = '';
            $data['tcoeff_input'] = '';
            $data['tcoeff_output'] = '';
            $data['tobv_input'] = '';
            $data['tobv_output'] = '';
            $data['tadj_input'] = '';
            $data['tadj_output'] = '';

            $data['tvcv_input'] = '';
            $data['tvcv_output'] = '';
            $data['ttable56_input'] = '';
            $data['ttable56_output'] = '';
            $data['tnetkg_input'] = '';
            $data['tnetkg_output'] = '';
            $data['tffa_input'] = '';
            $data['tffa_output'] = '';
            $data['tmoisture_input'] = '';
            $data['tmoisture_output'] = '';
            $data['tdirt_input'] = '';
            $data['tdirt_output'] = '';
            
            $data['diff_net'] = '';
            $data['diff_obv'] = '';
        }
        
        $this->load->view('shorec_invoice', $data);
   }
    
    // Fungsi update untuk menset texfield dengan nilai dari database
    function update($uid=null)
    {        
        $this->model->valid_add_trans($uid, $this->title);
        
        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = 'Edit '.$this->modul['title'];
        $data['main_view'] = 'shorec_update';
	$data['form_action'] = site_url($this->title.'/update_process');
        $data['form_action_import'] = site_url($this->title.'/import_density/'.$uid);
        $data['link'] = array('link_back' => anchor($this->title,'Back', array('class' => 'btn btn-danger')));

        $data['source'] = site_url($this->title.'/getdatatable');
        $data['array'] = array('','');
        $data['graph'] = site_url()."/product/chart/";
        $data['tank'] = $this->tank->combo('empty');
        $data['customer'] = $this->customer->combo();
        
        $product = $this->model->get_by_id($uid)->row();
        if ($product->fuel == 1){ $fuel = 'checked'; }else{ $fuel = ""; }
        if ($product->oil_boom == 1){ $boom = 'checked'; }else{ $boom = ""; }
        
        $data['uid'] = $uid;
        $data['default']['dates'] = $product->dates;
        $data['default']['docno'] = $product->docno;
        $data['default']['instructionno'] = $product->instructionno;
        $data['default']['type'] = $product->type;
        $data['default']['source'] = $product->tank_source;
        $data['default']['vessel'] = $product->vessel;
        $data['default']['shipper'] = $product->shipper;
        $data['default']['cust_id'] = $product->cust_id;
        $data['default']['content'] = $product->content;
        $data['default']['notes'] = $product->notes;
        $data['default']['fuel'] = $fuel;  
        $data['default']['oil_boom'] = $boom;
        
        $data['default']['eta'] = $product->eta;
        $data['default']['etb'] = $product->etb;
        $data['default']['laycan'] = $product->laycan;
        $data['default']['until'] = $product->until;
        $data['default']['heating'] = $product->heating;
        $data['default']['heating_until'] = $product->heating_until;
        $data['default']['comm_pumping'] = $product->start_pump;
        $data['default']['comp_pumping'] = $product->end_pump;
        
        if ($product->shore_line_cond == "SINGLE"){ $data['shore_line_cond_1'] = 'checked'; }
        else{ $data['shore_line_cond_2'] = 'checked'; }
        
        if ($product->before_load == "EMPTY"){ $data['before_load_1'] = 'checked'; }
        else{ $data['before_load_2'] = 'checked'; }
        
        if ($product->cleaning_sys == "AIR"){ $data['cleaning_sys_1'] = 'checked'; }
        else{ $data['cleaning_sys_2'] = 'checked'; }
        
        if ($product->after_load == "EMPTY"){ $data['after_load_1'] = 'checked'; }
        else{ $data['after_load_2'] = 'checked'; }
        
        $data['default']['ship_name'] = $product->ship_name;
        $data['default']['ship_rep'] = $product->ship_rep;
        $data['default']['ship_company'] = $product->ship_company;
        $data['default']['buyer_name'] = $product->buyer_name;
        $data['default']['buyer_rep'] = $product->buyer_rep;
        $data['default']['buyer_company'] = $product->buyer_company;
        
        // ========================= shore trans ==========================
        
        $trans = $this->sounding_model->get($uid)->row();
        if ($trans){
            
        $data['default']['tincm_input'] = $trans->in_sounding;
        $data['default']['tincm_output'] = $trans->out_sounding;
        $data['default']['tcorcm_input'] = $trans->in_corr;
        $data['default']['tcorcm_output'] = $trans->out_corr;
        $data['default']['tacorr_input'] = $trans->in_after_corr;
        $data['default']['tacorr_output'] = $trans->out_after_corr;
        $data['default']['ttemp_input'] = $trans->in_temperature;
        $data['default']['ttemp_output'] = $trans->out_temperature;
        
        $data['default']['tdensity_input'] = $trans->in_density;
        $data['default']['tdensity_output'] = $trans->out_density;
        $data['default']['tcoeff_input'] = $trans->in_coeff;
        $data['default']['tcoeff_output'] = $trans->out_coeff;
        $data['default']['tobv_input'] = $trans->in_obv;
        $data['default']['tobv_output'] = $trans->out_obv;
        $data['default']['tadj_input'] = $trans->in_adj;
        $data['default']['tadj_output'] = $trans->out_adj;
        
        $data['default']['tvcv_input'] = $trans->in_vcv;
        $data['default']['tvcv_output'] = $trans->out_vcv;
        $data['default']['ttable56_input'] = $trans->in_table56;
        $data['default']['ttable56_output'] = $trans->out_table56;
        $data['default']['tnetkg_input'] = $trans->in_netkg;
        $data['default']['tnetkg_output'] = $trans->out_netkg;
        $data['default']['tffa_input'] = $trans->in_ffa;
        $data['default']['tffa_output'] = $trans->out_ffa;
        $data['default']['tmoisture_input'] = $trans->in_moisture;
        $data['default']['tmoisture_output'] = $trans->out_moisture;
        $data['default']['tdirt_input'] = $trans->in_dirt;
        $data['default']['tdirt_output'] = $trans->out_dirt;
        
        }
        
        $this->load->view('template', $data);
    }
    
    function valid_vessel($vessel){
        
        $id = $this->input->post('tid');
        $type = $this->model->get_by_id($id)->row();
       
        if ($type->type == 0){
            if (!$vessel){ $this->form_validation->set_message('valid_vessel', "Vessel Tank Required.!"); return FALSE; }
            else{ 
                if ($vessel == $this->input->post('csource')){
                    $this->form_validation->set_message('valid_vessel', "Invalid Vessel Tank..!"); return FALSE;
                }else{ return TRUE; }
            }
        }else{ return TRUE; }        
    }


    public function valid_period($date=null)
    {
        $p = new Period();
        $p->get();

        $month = date('n', strtotime($date));
        $year  = date('Y', strtotime($date));

        if ( intval($p->month) != intval($month) || intval($p->year) != intval($year) )
        {
            $this->form_validation->set_message('valid_period', "Invalid Period.!");
            return FALSE;
        }
        else {  return TRUE; }
    }
    
    function valid_start($val,$field=null){
        
        $start = null; $end = null;
        if ($field == 'ETB'){
            if ($this->input->post('teta')){
                $start = new DateTime($this->input->post('teta'));
                $end = new DateTime($val);
            }else{ return TRUE; }
        }elseif($field == 'Comp'){
            if ($this->input->post('tcomm_pumping')){
                $start = new DateTime($this->input->post('tcomm_pumping'));
                $end = new DateTime($val);
            }else{ return TRUE; }
        }
        if ($start > $end ){ $this->form_validation->set_message('valid_start', "Invalid Interval $field..!"); return FALSE; }
        else{ return TRUE; }
    }
    
    function valid_eta_post($val,$field=null)
    {           
        if ($this->input->post('teta'))
        {
            if (!$val){ $this->form_validation->set_message('valid_eta_post',$field.' Field Required..!'); return FALSE; }
            else{ return TRUE; }
        }
        else{ return TRUE; }
    }
    
    function valid_eta($val,$field=null)
    {
        $id = $this->input->post('tid');
        $res = $this->model->get_by_id($id)->row();
                
        if ($res->eta != NULL)
        {
            if (!$val){ $this->form_validation->set_message('valid_eta',$field.' Field Required..!'); return FALSE; }
            else{ return TRUE; }
        }
        else{ return TRUE; }
    }
        
    function valid_docno($val)
    {
        if ($this->model->valid('docno',$val) == FALSE)
        {
            $this->form_validation->set_message('valid_docno','Document No registered..!');
            return FALSE;
        }
        else{ return TRUE; }
    }
   
    function validating_docno($val)
    {
	$id = $this->input->post('tid');
	if ($this->model->validating('docno',$val,$id) == FALSE)
        {
            $this->form_validation->set_message('validating_docno', "Document No registered!");
            return FALSE;
        }
        else{ return TRUE; }
    }
    
    function validating_instruction($val)
    {
	$id = $this->input->post('tid');
	if ($this->model->validating('instructionno',$val,$id) == FALSE)
        {
            $this->form_validation->set_message('validating_instruction', "Instruction No registered!");
            return FALSE;
        }
        else{ return TRUE; }
    }
    

    private function get_trans_type($val=0){
        switch ($val) {
            case 0:return "OUT"; break;
            case 1:return "OUT"; break;
            case 2: return "IN"; break;
            case 3: return "OUT"; break;
        }
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
            $this->form_validation->set_rules('tno', 'Document No', 'required|callback_validating_docno');
            $this->form_validation->set_rules('tdate', 'Transaction date', 'required|callback_valid_period');
            $this->form_validation->set_rules('tnote', 'Note / Remarks', 'required');
            $this->form_validation->set_rules('ctype', 'Transaction Type', 'required');
            
            if ($this->form_validation->run($this) == TRUE)
            {   
                
                $product = array('docno' => strtoupper($this->input->post('tno')),
                                 'trans_type' => $this->get_trans_type($this->input->post('ctype')),
                                 'dates' => $this->input->post('tdate'), 'notes' => $this->input->post('tnote'), 
                                 'type' => $this->input->post('ctype'), 'fuel' => $this->input->post('cfuel'),
                                 );
                
                $this->model->update($this->input->post('tid'), $product);
                echo 'true|'."One $this->title has successfully updated!";
                
                // end update 1
            }
            else{ echo 'error|'.validation_errors(); }
        }
        elseif ($param == 2)
        {
            $this->form_validation->set_rules('cvessel', 'Vessel', 'callback_valid_vessel');
            $this->form_validation->set_rules('tshipper', 'Shipper', '');
            $this->form_validation->set_rules('ccust', 'Consignee', 'required');
            $this->form_validation->set_rules('tcontent', 'Commodity', 'required');
            $this->form_validation->set_rules('tinstruction', 'Instruction No', 'required|callback_validating_instruction');
            $this->form_validation->set_rules('csource', 'Source Tank', 'required');
            $this->form_validation->set_rules('teta', 'ETA', '');
            $this->form_validation->set_rules('tetb', 'ETB', 'callback_valid_eta_post[ETB]|callback_valid_start[ETB]');
            $this->form_validation->set_rules('tlaycan', 'Laycan Date', 'callback_valid_eta_post[Laycan]');
            $this->form_validation->set_rules('tuntil', 'Until', 'callback_valid_eta_post[Until]');
            $this->form_validation->set_rules('theating', 'Heating Date', 'callback_valid_eta_post[Heating]');
            $this->form_validation->set_rules('theating_until', 'Heating Until', 'callback_valid_eta_post[Heating Until]');
            
            if ($this->form_validation->run($this) == TRUE)
            {
                $product = array('vessel' => $this->input->post('cvessel'),
                                 'shipper' => $this->input->post('tshipper'), 'cust_id' => $this->input->post('ccust'),
                                 'content' => $this->input->post('tcontent'), 'instructionno' => $this->input->post('tinstruction'), 
                                 'tank_source' => $this->input->post('csource'), 'oil_boom' => $this->input->post('cboom'),
                                 'eta' => setnull($this->input->post('teta')), 'etb' => setnull($this->input->post('tetb')), 'laycan' => setnull($this->input->post('tlaycan')),
                                 'until' => setnull($this->input->post('tuntil')), 'heating' => setnull($this->input->post('theating')), 'heating_until' => setnull($this->input->post('theating_until')),
                                );
                
                $this->model->update($this->input->post('tid'), $product);
                echo 'true|'."One $this->title calculation has successfully updated!";
                
                // end update 1
            }
            else{ echo 'error|'.validation_errors(); } 
        }
        elseif ($param == 3)
        {
            $this->form_validation->set_rules('tcomm_pumping', 'Common Pumping', 'callback_valid_eta[Comm Pumping]');
            $this->form_validation->set_rules('tcomp_pumping', 'Complete Pumping', 'callback_valid_eta[Comp Pumping]|callback_valid_start[Comp]');
            $this->form_validation->set_rules('rlinecondition', 'Shore Line Condition', 'required');
            $this->form_validation->set_rules('rbeforeload', 'Before Loading Type', 'required');
            $this->form_validation->set_rules('rcleaning', 'Cleaning System Type', 'required');
            $this->form_validation->set_rules('rafterload', 'After Loading Type', 'required');
            
            $this->form_validation->set_rules('tshipname', 'Ship Name', '');
            $this->form_validation->set_rules('tshiprep', 'Ship Rep', '');
            $this->form_validation->set_rules('tshipcompany', 'Ship Company', '');
            $this->form_validation->set_rules('tbuyername', 'Buyer Name', '');
            $this->form_validation->set_rules('tbuyerrep', 'Buyer Rep', '');
            $this->form_validation->set_rules('tbuyercompany', 'Buyer Company', '');
            
            if ($this->form_validation->run($this) == TRUE){
               
                $product = array('start_pump' => setnull($this->input->post('tcomm_pumping')), 'end_pump' => setnull($this->input->post('tcomp_pumping')),
                                 'shore_line_cond' => $this->input->post('rlinecondition'), 'before_load' => $this->input->post('rbeforeload'),
                                 'cleaning_sys' => $this->input->post('rcleaning'), 'after_load' => $this->input->post('rafterload'),
                                 'ship_name' => $this->input->post('tshipname'), 'ship_rep' => $this->input->post('tshiprep'),
                                 'ship_company' => $this->input->post('tshipcompany'), 'buyer_name' => $this->input->post('tbuyername'),
                                 'buyer_rep' => $this->input->post('tbuyerrep'), 'buyer_company' => $this->input->post('tbuyercompany'),
                                 );
                $this->model->update($this->input->post('tid'), $product);
                echo 'true|One '.$this->title.' execution has successfully updated!'; 
                
            }else{ echo 'error|'.validation_errors(); }
        }
        
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    function report_process()
    {
        $this->acl->otentikasi2($this->title);
        $data['title'] = $this->properti['name'].' | Report '.ucwords($this->modul['title']);

        $type = $this->input->post('ctranstype');
        $cust = $this->input->post('ccust');
        $rtype = $this->input->post('creporttype');
        
        $period = $this->input->post('reservation');  
        $start = picker_between_split($period, 0);
        $end = picker_between_split($period, 1);

        $data['transtype'] = $this->get_type($type);
        $data['cust'] = $this->customer->get_name($cust);
        $data['type'] = strtoupper($rtype);
        $data['start'] = $start;
        $data['end'] = $end;
        $data['rundate'] = tgleng(date('Y-m-d'));
        $data['log'] = $this->session->userdata('log');
        
//        Property Details
        $data['company'] = $this->properti['name'];
        $data['reports'] = $this->model->report($type,$cust,$rtype,$start,$end)->result();
        
        $reptype = $this->input->post('ctype');
        if ($reptype == 0){ $this->load->view('shorec_report', $data); }
        elseif($reptype == 2) { $this->load->view('shorec_pivot', $data); }
        elseif($reptype == 1) { $this->load->view('shorec_report_2', $data); }
        elseif($reptype == 3) { $this->load->view('shorec_pivot_2', $data); }  
    }
       
}

?>