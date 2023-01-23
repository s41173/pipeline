<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once 'definer.php';

class Registration extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('Registration_model', 'model', TRUE);

        $this->properti = $this->property->get();
        $this->acl = new Acl();
        $this->acl->otentikasi();

        $this->modul = $this->components->get(strtolower(get_class($this)));
        $this->title = strtolower(get_class($this));
        $this->role = new Role_lib();
        $this->period = new Period_lib();
        $this->period = $this->period->get();
        $this->api = new Api_lib();
        $this->tank = new Tank_lib();
        $this->wb = new Wb_lib();
        $this->contract = new Contract_lib();
        $this->sounding = new Tank_sounding_lib();
        
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');  
    }

    private $properti, $modul, $title, $api;
    private $role,$tax,$acl,$tank,$wb,$contract,$sounding;

    
    function index(){
        $this->session->unset_userdata('start'); $this->session->unset_userdata('end'); $this->get_last(); 
    }
    
    // ======= ajax function =========
    
    function get_out_standing(){
        
        $datax = (array)json_decode(file_get_contents('php://input'));
        
        $nilai = array('limit' => 1, 'offset' => 0, 'filter' => $datax['origin']); 
        $param = json_encode($nilai, JSON_UNESCAPED_SLASHES,true);
        $contract = $this->wb->request_auth('contract/get_qty', $this->session->userdata('userid'), $param);
        $contract = json_decode($contract, true); 
        
         $response = array(
                'origin' => $datax['origin'],
                'amount' => $contract['result'][0]['qty'],
                'oustanding' => $contract['result'][0]['qty_out_standing']
             );
        
         $this->output
        ->set_status_header(200)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))
        ->_display();
        exit;
    }
         
    public function getdatatable($search=null,$type='null',$date='null')
    {
        if ($search != null){ $result = $this->model->search($type,$date)->result(); }
        else{ $result = $this->model->get_last($this->modul['limit'])->result(); }
        
        $output = null;
        if ($result){
          
         foreach($result as $res)
	 {
	   $output[] = array ($res->id, $res->code, $res->docno, tglin($res->dates).' <br/> '.timein($res->dates), $res->type,
                              strtoupper($res->source_tank), strtoupper($res->to_tank), 
                              tglin($res->start_transfer).' | '. timein($res->start_transfer), tglin($res->end_transfer).' | '.timein($res->end_transfer), $res->qc_status, $res->approved, 
                              tglincompletetime($res->created), tglincompletetime($res->updated));
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
//        $this->acl->otentikasi1($this->title);

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords('Tank Manager');
        $data['h2title'] = $this->components->get_title($this->title);
        $data['main_view'] = 'registration_view';
	$data['form_action'] = site_url($this->title.'/add_process');
        $data['form_action_update'] = site_url($this->title.'/update_process');
        $data['form_action_del'] = site_url($this->title.'/delete_all');
        $data['form_action_report'] = site_url($this->title.'/report_process');
        $data['form_action_import'] = site_url($this->title.'/import');
        $data['link'] = array('link_back' => anchor('main/','Back', array('class' => 'btn btn-danger')));

        $data['tank'] = $this->tank->combo_api();
        $data['srctank'] = $this->model->get_src_tank();
        $data['docno'] = $this->model->combo_docno();
        $data['array'] = array('','');
        $data['month'] = combo_month();
        $data['default']['month'] = date('m');
        $data['code'] = $this->model->counter().mt_rand(99,9999);
        
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
        $this->table->set_heading('#','No', 'Type', 'Code', 'Doc-No', 'Dates', 'Tank Farm', 'Transfer Period', 'Action');

        $data['table'] = $this->table->generate();
        $data['source'] = site_url($this->title.'/getdatatable');
        $data['graph'] = site_url()."/".$this->title."/chart/";
            
        // Load absen view dengan melewatkan var $data sbgai parameter
	$this->load->view('template', $data);
    }
    
    function get_list($target='titem',$type=0)
    {
        $this->acl->otentikasi1($this->title);

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'product_list';
        $data['form_action'] = site_url($this->title.'/get_list');
        
        $searchtype = $this->input->post('ctype');
        $value = $this->input->post('tvalue');

        if ($searchtype != ""){ 
            $param = json_encode(array('limit' => 50, 'offset' => 0, 'search_type' => $searchtype, 'filter' => $value)); 
        }
        else{ 
            $param = json_encode(array('limit' => 10, 'offset' => 0, 'search_type' => 0, 'filter' => "")); 
        }
        $contract = $this->wb->request_auth('contract', $this->session->userdata('userid'), $param);
        $contract = json_decode($contract, true); 
//        print_r($contract['result'][0]);
        
//        echo '<br/>'.$param;

        $tmpl = array('table_open' => '<table id="example" width="100%" cellspacing="0" class="table table-striped table-bordered">');

        $this->table->set_template($tmpl);
        $this->table->set_empty("&nbsp;");

            //Set heading untuk table
            $this->table->set_heading('No', 'ID', 'Origin', 'No-Contract', 'DO-No', 'Name', 'Doc-DO', 'Type', 'Product', 'State', 'Partner', 'Action');

            $i = 0;
            if ($contract['result']){
                foreach ($contract['result'] as $res)
                {
//                    print_r($res['picking_id']);
                   $datax = array('name' => 'button', 'type' => 'button', 'class' => 'btn btn-primary', 'content' => 'Select', 'onclick' => 'setvalue(\''.$res['origin'].'\',\''.$target.'\')');
                    $this->table->add_row
                    (
                        ++$i, $res['picking_id'], strtoupper($res['origin']), strtoupper($res['no_do']), strtoupper($res['no_contract']), strtoupper($res['picking_name']),
                        $res['no_dokumen_do'], $res['picking_type'], $res['product_name'], $res['state'], $res['partner_name'],
                        form_button($datax)
                    );
                }            
            }
//
            $data['table'] = $this->table->generate();
            $this->load->view('contract_list', $data);
    }
    
   function invoice($pid=null)
   {
       $this->acl->otentikasi2($this->title);
       $ap = $this->model->get_by_id($pid)->row();

       $data['h2title'] = 'Print Invoice'.$this->modul['title'];

       $data['pono'] = "CO-0".$pid;
       $data['docno'] = $ap->docno;
       $data['date'] = tglin($ap->dates);
       $data['customer'] = $this->customer->get_name($ap->cust_id);
       $data['start'] = tglin($ap->starts);
       $data['end'] = tglin($ap->ends);
       $data['notes'] = ucfirst($ap->notes);
       $data['amount'] = $ap->amount;
       $data['balance'] = $ap->balance;
       $data['tax'] = $ap->tax;
       $data['taxval'] = $ap->tax_val;
       $data['approved'] = $ap->approved;
       $data['status'] = $ap->status;
       $data['log'] = $this->session->userdata('log');

       $data['amount'] = $ap->amount;
       $terbilang = $this->load->library('terbilang');
       $data['terbilang'] = ucwords($terbilang->baca($ap->amount));
       
       if($ap->approved == 1){ $stts = 'A'; }else{ $stts = 'NA'; }
       $data['stts'] = $stts;

       $data['items'] = $this->sales->get_based_contract($pid)->result();
       
       $data['accounting'] = $this->properti['accounting'];
       $data['manager'] = $this->properti['manager'];
       $this->load->view('contract_invoice', $data);
   }
    

    function confirmation($pid)
    {
        if ($this->acl->otentikasi3($this->title,'ajax') == TRUE){
        $journal = $this->model->get_by_id($pid)->row();
        
        if ($journal->approved == 1) { echo "warning|$this->title already approved..!"; }
        elseif ($this->valid_period($journal->dates) == FALSE ){ echo "error|Invalid period..!"; }
        elseif ($journal->amount <= 0 ){ echo "error|Invalid Amount..!"; }
        else
        {
           $ar = $journal->ar_account; // piutang dagang
           $tax = $journal->tax_account; // tax keluaran
           $sales = $journal->sales_account; // sales

           // create journal- GL
           $this->journalgl->new_journal('0'.$pid,$journal->dates,'CO','IDR',$journal->notes,$journal->amount, $this->session->userdata('log'));
           $dpid = $this->journalgl->get_journal_id('CO','0'.$pid);

           $this->journalgl->add_trans($dpid,$sales,0, intval($journal->amount-$journal->tax_val)); // penjualan
           $this->journalgl->add_trans($dpid,$ar,$journal->amount,0); // piutang dagang
           if ($journal->tax_val > 0){  
               $this->journalgl->add_trans($dpid,$tax,0,$journal->tax_val); // piutang tax
           }
                  
           $data = array('approved' => 1, 'balance' => $journal->amount);
           $this->model->update($pid, $data);
           echo "true| $journal->docno confirmed..!";
        }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
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

    function delete($uid=0)
    {
         if ($this->acl->otentikasi3($this->title,'ajax') == TRUE){

            $journal = $this->model->get_by_id($uid)->row();
            if ($journal->validation == 1 && $journal->approved == 1){
                $this->rollback($uid);
            }elseif ($journal->validation == 1 && $journal->approved == 0){
              $data = array('validation' => 0);
              if ($this->model->update($uid, $data) == TRUE){ echo 'true|rollback validation success'; }else{ echo 'error|Failed to rollback validation'; } 
            }
            else if ($journal->validation == 0 && $journal->approved == 0){
                if ($this->sounding->cleaning($uid) == TRUE && $this->contract->cleaning($uid)){
                   $this->model->delete($uid);
                   echo "true|1 $this->title successfully removed..!"; 
                }else{ echo 'error|Failure to removed data'; }
            }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    private function rollback($uid)
    {
       $journal = $this->model->get_by_id($uid)->row(); 
//       if($journal->amount != $journal->balance){ echo "error|amount and balance not equal!"; }
//       else{
        $this->journalgl->remove_journal('CO', '0'.$uid);
        $data = array('approved' => 0);
        $this->model->update($uid, $data);   
//       }
    }
    
    private function remove($uid){ $this->model->force_delete($uid); }
    
    function add_process()
    {
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){

	// Form validation
        $this->form_validation->set_rules('tcode', 'Code No', 'required|numeric');
        $this->form_validation->set_rules('tdocno', 'Doc No', 'required|callback_valid_docno');
        $this->form_validation->set_rules('ctype', 'Transaction Type', 'required');
        $this->form_validation->set_rules('tdate', 'Transaction Date', 'required');
        $this->form_validation->set_rules('tnote', 'Note / Remarks', '');
        $this->form_validation->set_rules('csrctank', 'Source Tank', 'callback_valid_sourcetank');
        $this->form_validation->set_rules('cttotank', 'Dest Tank', 'required');
        
        if ($this->form_validation->run($this) == TRUE)
        {
            if ($this->input->post('tsrctank') != ""){ $srctank = $this->input->post('tsrctank');}
            else{ $srctank = $this->input->post('csrctank'); }
            
            $product = array('docno' => strtoupper($this->input->post('tdocno')),
                             'type' => strtoupper($this->input->post('ctype')),
                             'dates' => $this->input->post('tdate'), 'description' => $this->input->post('tnote'), 
                             'code' => $this->input->post('tcode'),
                             'source_tank' => $srctank, 'to_tank' => $this->input->post('cttotank'),
                             'created' => date('Y-m-d H:i:s'));
            
            if ($this->model->add($product) == TRUE){echo 'true|'.$this->title.' successfully saved..!';}
        }
        else{ echo "error|".validation_errors(); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    // Fungsi update untuk menset texfield dengan nilai dari database
    function update($uid=null)
    {      
//        $this->acl->otentikasi();
        $this->model->valid_add_trans($uid, $this->title);
        
        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = 'Edit '.$this->modul['title'];
        $data['main_view'] = 'registration_update';
	$data['form_action'] = site_url($this->title.'/update_process/'.$uid);
        $data['form_action_item'] = site_url($this->title.'/add_item/'.$uid);
        $data['form_action_sounding'] = site_url($this->title.'/add_sounding_item/'.$uid);
        $data['link'] = array('link_back' => anchor($this->title,'Back', array('class' => 'btn btn-danger')));

        $data['source'] = site_url($this->title.'/getdatatable');
        $data['array'] = array('','');
        $data['graph'] = site_url()."/product/chart/";
        $data['pic_ibl'] = $this->model->get_pic('pic_1');
        $data['pic_obl'] = $this->model->get_pic('pic_2');
        $data['pic_kinra'] = $this->model->get_pic('pic_3');
        $data['pic_qc'] = $this->model->get_pic('pic_qc');
        
        $register = $this->model->get_by_id($uid)->row();
        if ($register->validation == 0){
          $databtn = array('name'=>'','id'=>'validatebtn','class'=>'btn btn-primary','value'=> $uid,'type'=>'button','content'=>'Validate');
          $data['button_validate'] = form_button($databtn);
        }else{ $data['button_validate'] = null; }
        
        $data['uid'] = $uid;
        $data['default']['code']   = $register->code;
        $data['default']['date']   = $register->dates;
        $data['default']['docno']  = $register->docno;
        $data['default']['type']   = $register->type;
        $data['default']['srctank'] = $register->source_tank;
        $data['default']['totank'] = $register->to_tank;
        $data['default']['tank'] = $register->source_tank.' -> '.$register->to_tank;
        $data['default']['start'] = $register->start_transfer;
        $data['default']['end']   = $register->end_transfer;
        
        $data['default']['pic_ibl']   = $register->pic_1;
        $data['default']['pic_obl']   = $register->pic_2;
        $data['default']['pic_kinra'] = $register->pic_3;
        $data['default']['pic_qc']    = $register->pic_qc;
        
        $data['default']['ffa'] = $register->qc_ffa;
        $data['default']['m']  = $register->qc_m;
        $data['default']['i']  = $register->qc_i;
        $data['default']['qcstatus'] = $register->qc_status;
        $data['default']['desc'] = $register->description;
        
        $data['items'] = $this->contract->get_last($uid); // item contract
        $data['items_sounding'] = $this->sounding->get_last($uid)->result();
        $data['default']['amt_qty'] = number_format($this->contract->cek_contract_amount($uid));
//        echo $this->contract->cek_contract_amount($uid);
        
        // sounding summary
        $before = $this->sounding->get_by_period($uid,0);
        $after = $this->sounding->get_by_period($uid,1);
        
        if ($this->sounding->get_last($uid)->num_rows() > 1){
            $qty_source_before = $before->source_tonase;
            $qty_source_after = $after->source_tonase;
            
            $qty_to_before = $before->to_tonase;
            $qty_to_after = $after->to_tonase;
            
            $data['qty_kirim'] = abs($qty_source_after-$qty_source_before);
            $data['qty_terima'] = $qty_to_after-$qty_to_before;
            $data['selisih'] = round($data['qty_terima']-$data['qty_kirim'],3);
            $selisih = @floatval($data['selisih']/$data['qty_terima']);
            $data['persentase'] = round($selisih*100,3,PHP_ROUND_HALF_DOWN);
        }else{
            $data['qty_kirim'] = 0;
            $data['qty_terima'] = 0;
            $data['selisih'] = 0;
            $data['persentase'] = 0;
        }
        
        $this->load->view('template', $data);
    }
    
    
    function valid_sourcetank($val)
    {
        if ($this->input->post('csrctank') == "" && $this->input->post('tsrctank') == ""){
            $this->form_validation->set_message('valid_sourcetank','Source Tank Required..!');
            return FALSE;
        }else{ return TRUE; }
    }
    
    function valid_start($start)
    {
        $dates = $this->input->post('tdate');
        if (strtotime($start) < strtotime($dates))
        {
            $this->form_validation->set_message('valid_start','Invalid Start Transfer Date..!');
            return FALSE;
        }
        else{ return TRUE; }
    }
    
    function valid_end($end)
    {
        $start = $this->input->post('tstart');
        if (strtotime($end) < strtotime($start))
        {
            $this->form_validation->set_message('valid_end','Invalid End Transfer Date..!');
            return FALSE;
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
    
    function valid_ibl($val)
    {
        $tibl = $this->input->post('tpicibl');
        if ($val == "" && $tibl == "")
        {
            $this->form_validation->set_message('valid_ibl','PIC IBL Required..!');
            return FALSE;
        }
        else{ return TRUE; }
    }
    
    function valid_obl($val)
    {
        $tibl = $this->input->post('tpicobl');
        if ($val == "" && $tibl == "")
        {
            $this->form_validation->set_message('valid_obl','PIC OBL Required..!');
            return FALSE;
        }
        else{ return TRUE; }
    }
    
    function valid_kinra($val)
    {
        $tibl = $this->input->post('tpickinra');
        if ($val == "" && $tibl == "")
        {
            $this->form_validation->set_message('valid_kinra','PIC Kinra Required..!');
            return FALSE;
        }
        else{ return TRUE; }
    }
    
    function valid_qc($val)
    {
        $tibl = $this->input->post('tpicqc');
        if ($val == "" && $tibl == "")
        {
            $this->form_validation->set_message('valid_qc','PIC QC Required..!');
            return FALSE;
        }
        else{ return TRUE; }
    }
    
    function valid_confirmation($uid=0){
        $val = $this->model->get_by_id($uid)->row();
        if ($val->validation == 1){
          $this->form_validation->set_message('valid_confirmation', "Transaction Locked, Can't Editable..!");
          return FALSE;
        }else{ return TRUE; }
    }

    // Fungsi update untuk mengupdate db
    function update_process($uid=0)
    {
        if ($this->acl->otentikasi2($this->title) == TRUE){

        $data['title'] = $this->properti['name'].' | Productistrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'product_update';
	$data['form_action'] = site_url($this->title.'/update_process/'.$uid);
	$data['link'] = array('link_back' => anchor('admin/','<span>back</span>', array('class' => 'back')));

	// Form validation
        $this->form_validation->set_rules('tdate', 'Transation Date', 'required');
        $this->form_validation->set_rules('tstart', 'Transfer Start', 'required|callback_valid_start');
        $this->form_validation->set_rules('tend', 'Transfer End', 'required|callback_valid_end');
        $this->form_validation->set_rules('cpicibl', 'PIC-IBL', 'callback_valid_ibl');
        $this->form_validation->set_rules('cpicobl', 'PIC-OBL', 'callback_valid_obl');
        $this->form_validation->set_rules('cpickinra', 'PIC-KINRA', 'callback_valid_kinra');
        $this->form_validation->set_rules('cpicqc', 'PIC-QC', 'callback_valid_qc');
        $this->form_validation->set_rules('tqcffa', 'FFA', 'required|numeric');
        $this->form_validation->set_rules('tqcm', 'M', 'required|numeric');
        $this->form_validation->set_rules('tqci', 'I', 'required|numeric');
        $this->form_validation->set_rules('cqcstatus', 'QC-Status', 'required|numeric');
        $this->form_validation->set_rules('csegelstatus', 'Segel-Status', 'required|numeric');
        $this->form_validation->set_rules('tdesc', 'Description', '');

        
        if ($this->form_validation->run($this) == TRUE && $this->valid_confirmation($uid) == TRUE)
        {
            if ($this->input->post('tpicibl') != ""){ $picibl = $this->input->post('tpicibl');}else{ $picibl = $this->input->post('cpicibl'); }
            if ($this->input->post('tpicobl') != ""){ $picobl = $this->input->post('tpicobl');}else{ $picobl = $this->input->post('cpicobl'); }
            if ($this->input->post('tpickinra') != ""){ $pickinra = $this->input->post('tpickinra');}else{ $pickinra = $this->input->post('cpickinra'); }
            if ($this->input->post('tpicqc') != ""){ $picqc = $this->input->post('tpicqc');}else{ $picqc = $this->input->post('cpicqc'); }
            
            $product = array('dates' =>$this->input->post('tdate'),
                             'start_transfer' => $this->input->post('tstart'), 'end_transfer' => $this->input->post('tend'),
                             'pic_1' => $picibl, 'pic_2' => $picobl, 'pic_3' => $pickinra, 'pic_qc' => $picqc,
                             'qc_ffa' => $this->input->post('tqcffa'), 'qc_m' => $this->input->post('tqcm'), 'qc_i' => $this->input->post('tqci'),
                             'qc_status' => $this->input->post('cqcstatus'),'description' => $this->input->post('tdesc'),
                             'segel_status' => $this->input->post('csegelstatus')
                            );
//            
            $this->model->update($uid, $product);
            echo 'true|'."One $this->title has successfully updated!";
        }
        else if($this->form_validation->run($this) != TRUE){ echo 'error|'.validation_errors(); }
        else if($this->valid_confirmation($uid) != TRUE){ echo "error|Journal approved, can't deleted.."; }
        
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    function add_item($uid=0)
    {
        $this->form_validation->set_rules('titem', 'Item Name', 'required');
        $this->form_validation->set_rules('tcontractqty', 'Contract Qty', 'required|numeric|is_natural_no_zero');
        $this->form_validation->set_rules('toustandingqty', 'Outstanding Qty', 'required|numeric|is_natural_no_zero|callback_valid_transfer_qty');
        $this->form_validation->set_rules('ttransferqty', 'Transfer Qty', 'required|numeric|is_natural_no_zero');

        if ($this->form_validation->run($this) == TRUE && $this->valid_confirmation($uid) == TRUE)
        {
            $pitem = array('registration_id' => $uid, 
                           'origin_no' => $this->input->post('titem'),
                           'contract_amount' => $this->input->post('tcontractqty'),
                           'outstanding_amount' => $this->input->post('toustandingqty'),
                           'transfer_amount' => $this->input->post('ttransferqty'),
                           'created' => date('Y-m-d H:i:s')
                          );
            
            $this->contract->add($pitem);
            echo 'true';
        }
        elseif ( $this->valid_confirmation($uid) != TRUE ){ echo "error|Can't change value - Journal validated..!"; }
        else{ echo 'error|'.validation_errors(); } 
    }
    
    function valid_transfer_qty($outstanding){
       $transferqty = $this->input->post('ttransferqty'); 
       if ($outstanding < $transferqty){
          $this->form_validation->set_message('valid_transfer_qty','Invalid Transfer Qty..!');
          return FALSE;
       }else{ return TRUE; }
    }
    
    function add_sounding_item($uid=0)
    {
        $this->form_validation->set_rules('ctanktype', 'Tank Type', 'required');
        $this->form_validation->set_rules('cperiodtype', 'Period Type', 'required');
        $this->form_validation->set_rules('tsounding', 'Sounding', 'required|numeric');
        $this->form_validation->set_rules('ttemp', 'Temperature', 'required|numeric');
        $this->form_validation->set_rules('ttonase', 'Tonase', 'required|numeric');

        if ($this->form_validation->run($this) == TRUE && $this->valid_confirmation($uid) == TRUE)
        {
            $pitem = array('registration_id' => $uid, 
                           'type' => $this->input->post('ctanktype'),
                           'periodtype' => $this->input->post('cperiodtype'),
                           'sounding' => $this->input->post('tsounding'),
                           'temp' => $this->input->post('ttemp'),
                           'tonase' => $this->input->post('ttonase')
                          );
            
            if ($this->sounding->create($pitem) == TRUE){
                echo 'true';
            }else{ echo "error|Failure to post sounding data..!"; }
        }
        elseif ( $this->valid_confirmation($uid) != TRUE ){ echo "error|Can't change value - Journal validated..!"; }
        else{ echo 'error|'.validation_errors(); } 
    }
    
    function validation($regid){
        $register = $this->model->get_by_id($regid)->row();
        $error = null; $status = TRUE;
        
        $qtyreceived = $this->sounding->get_qty_receive($regid);
        $contractamt = $this->contract->cek_contract_amount($regid);
        
        if ($register->validation == 1){ $error = 'Transaction has been validated, Rollback first..!'; $status = FALSE; }
        else if ($register->approved == 1){ $error = 'Transaction has been posted, Cant edited..!'; $status = FALSE; }
        else if ($this->contract->cek_trans('registration_id',$regid) == FALSE){ $error = 'Origin / Contract List Not Found..!'; $status = FALSE; }
        else if ($this->sounding->count_row_based_registration($regid) == FALSE){ $error = 'Sounding List Not Complete..!'; $status = FALSE; }
        else if ($qtyreceived <> $contractamt){ $error = 'Invalid Contract & Received Amount'; $status = FALSE; }
        else if ($register->qc_status != 0){ $error = 'Invalid QC Status'; $status = FALSE; }
        
//        echo 'Qty Received : '.$qtyreceived.'<br>';
//        echo 'Contract Amount : '.$contractamt.'<br>';
        
//        print_r($error.'<br>');
//        var_dump($status);
        if ($status == TRUE){ 
          $data = array('validation' => 1);
          if ($this->model->update($regid, $data) == TRUE){ echo 'true|Validation Success'; }else{ echo 'error|Failed to edit validation'; }
        }
        else{ echo 'error|'.$error; }
    }
    
    
    // fungsi validation
    function valid_tonase($regid){
        $count = $this->sounding->get_last($regid)->num_rows();
        if ($count > 1){
           $before =  $this->sounding->get_by_period($regid, 0);
           $before = floatval($before->to_tonase);
           $after = $this->sounding->get_by_period($regid, 1);
           $after = floatval($after->to_tonase);
           
//           echo 'Before Tonase : '.$before.' kg <br/>';
//           echo 'After Tonase : '.$after.' kg';
           if ($before > $after){ return FALSE;}else{ return TRUE;}
        }else{ return TRUE; }
    }
    
    function delete_item($id)
    {
        if ($this->acl->otentikasi_admin($this->title,'ajax') == TRUE){
            
            $jid = $this->contract->get_by_id($id)->row();
            if ( $this->valid_confirmation($jid->registration_id) == TRUE )
            {
                $this->contract->force_delete($id);
                echo 'true|Transaction removed..!';
            }
            else{ echo "warning|Journal approved, can't deleted..!"; }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    function delete_item_sounding($uid)
    {
        if ($this->acl->otentikasi_admin($this->title,'ajax') == TRUE){
            
            $jid = $this->sounding->get_by_id($uid)->row();
            if ( $this->valid_confirmation($jid->registration_id) == TRUE )
            {
                $this->sounding->force_delete($uid);
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
        $data['reports'] = $this->model->report($this->input->post('cstatustype'),$start,$end)->result();
        
        if ($this->input->post('ctype') == 0){ $this->load->view('registration_report', $data); }
        else { $this->load->view('registration_pivot', $data); }
    }
        
   
    // ====================================== CLOSING ======================================
    function reset_process(){ $this->model->closing(); $this->model->closing_trans(); } 
    
    
}

?>