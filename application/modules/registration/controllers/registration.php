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
        $this->qc = new Qc_lib();
        
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');  
    }

    private $properti, $modul, $title, $api;
    private $role,$tax,$acl,$tank,$wb,$contract,$sounding,$qc;

    
    function index(){
        $this->session->unset_userdata('start'); $this->session->unset_userdata('end'); $this->get_last(); 
    }
    
    // ======= ajax function =========
    
    function get_out_standing(){
        
        $stts = 200;
        $datax = (array)json_decode(file_get_contents('php://input'));
        
        $nilai = array('limit' => 1, 'offset' => 0, 'filter' => $datax['origin']); 
        $param = json_encode($nilai, JSON_UNESCAPED_SLASHES,true);
        $contract = $this->wb->request_auth('contract/get_qty', $this->session->userdata('userid'), $param);
        $contract = json_decode($contract, true); 
        
        // get picking id - picking name
        $nilai1 = array('limit' => 1, 'offset' => 0, 'search_type'=> 1, 'filter' => $datax['origin']); 
        $param1 = json_encode($nilai1, JSON_UNESCAPED_SLASHES,true);
        $contract1 = $this->wb->request_auth('contract', $this->session->userdata('userid'), $param1);
        $contract1 = json_decode($contract1, true); 
        
//        print_r($contract1['result'][0]['picking_id']);
//        print_r($contract1['result'][0]['picking_name']);
        
        if ($contract1['result'][0]['no_aju'] <> null && $contract1['result'][0]['no_dokumen'] <> '-' && $contract1['result'][0]['jenis_dokumen'] <> null){
            
            $response = array(
                'origin' => $datax['origin'],
                'amount' => $contract['result'][0]['qty'],
                'oustanding' => $contract['result'][0]['qty_out_standing'],
                'picking_id' => $contract1['result'][0]['picking_id'],
                'picking_name' => $contract1['result'][0]['picking_name'],
                'partner_name' => $contract1['result'][0]['partner_name'],
                'no_segel' => $contract1['result'][0]['no_segel_bc'],
                'error' => null
             );
            
        }else{
          
           $error = null;
           if ($contract1['result'][0]['no_aju'] == null){ $error = "No aju - required"; }
           elseif ($contract1['result'][0]['no_dokumen'] == '-'){ $error = "No dokumen - required"; }
           elseif ($contract1['result'][0]['jenis_dokumen'] == null){ $error = "Jenis dokumen - required"; }
           
           $response = array(
                'origin' => $datax['origin'],
                'amount' => $contract['result'][0]['qty'],
                'oustanding' => $contract['result'][0]['qty_out_standing'],
                'picking_id' => $contract1['result'][0]['picking_id'],
                'picking_name' => $contract1['result'][0]['picking_name'],
                'partner_name' => $contract1['result'][0]['partner_name'],
                'no_segel' => $contract1['result'][0]['no_segel_bc'],
                'error' => $error
             );
           $stts = 400;
        }
        
         $this->output
        ->set_status_header($stts)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response,JSON_UNESCAPED_SLASHES,true))
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
        $this->acl->otentikasi();

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
            $this->table->set_heading('No', 'ID', 'Origin', 'No-Contract', 'DO-No', 'Name', 'Doc-DO', 'Type', 'Product', 'State', 'Partner', 'Doc List', 'Action');

            $i = 0;
            $button = null;
            
            if ($contract['result']){
                foreach ($contract['result'] as $res)
                {
                   if ($this->valid_doc_origin($res['no_aju'], $res['no_dokumen'], $res['jenis_dokumen']) == true){
                       $datax = array('name' => 'button', 'type' => 'button', 'class' => 'btn btn-primary', 'content' => 'Select', 'onclick' => 'setvalue(\''.$res['origin'].'\',\''.$target.'\')');
                       $button = form_button($datax);
                   }
                   $doc = $res['no_aju'].'<br/>'.$res['no_dokumen'].'<br/>'.$res['jenis_dokumen'];
//                    print_r($res['picking_id']);
                    $this->table->add_row
                    (
                        
                        ++$i, $res['picking_id'], strtoupper($res['origin']), strtoupper($res['no_do']), strtoupper($res['no_contract']), strtoupper($res['picking_name']),
                        $res['no_dokumen_do'], $res['picking_type'], $res['product_name'], $res['state'], $res['partner_name'],$doc,
                        $button
                    );
                }            
            }
//
            $data['table'] = $this->table->generate();
            $this->load->view('contract_list', $data);
    }
    
    private function valid_doc_origin($noaju=null,$nodokumen=null,$jenisdokumen=null){
        if ($noaju <> null && $nodokumen <> '-' && $jenisdokumen <> null){
            return true;
        }else{ return false; }
       
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
        if ($this->acl->otentikasi1($this->title) == TRUE){
        $journal = $this->model->get_by_id($pid)->row();
        
        if ($journal->approved == 1) { echo "warning|$this->title [$journal->code] already approved..!"; }
        elseif ($journal->validation == 0 ){ echo "error|$this->title [$journal->code] must be validate..!"; }
        else
        {
          if ($journal->type == "PIPELINE"){ $this->post_pipeline($pid); }
          elseif ($journal->type == "CARRIAGE"){ $this->post_gk($pid); } 
        }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    private function post_pipeline($pid){
        $stts = true;
        $register = $this->model->get_by_id($pid)->row();
        $result = $this->contract->get_last($pid);
        
        foreach ($result as $res) {
             $netto_diff = $res->netto_from-$res->transfer_amount;           

             $postData = array(
              'pickingid' => $res->picking_id, 'pickingname' => $res->picking_name,
              'sequence' => 10, 'product_id' => 1, 'product_uom_id' => 3, 'location_id' => 4,  'location_dest_id' => $register->to_tank_id,
              'do' => $register->docno, 'nama_kendaraan' => "", 'no_container' => "", 'no_polisi' => "",
              'transporter' => "PIPANISASI", 'driver_name' => "", 'destination' => "PT INL", 'no_karcis_timbangan' => "",
              'no_surat_jalan' => "", 'tgl_keluar_from' => $register->dates, 'tgl_masuk_truk' => $register->start_transfer, 'tgl_keluar_truk' => $register->end_transfer,
              'bruto_from' => 0, 'tarra_from' => 0, 'netto_from' => $res->netto_from, 'bruto' => 0,
              'tarra' => 0, 'qty_done' => $res->transfer_amount, 'netto_diff' => $res->netto_from-$res->transfer_amount, 'netto_diff_persen' => intval($netto_diff/$res->transfer_amount*100),
              'ffa_from' => $register->ffa_from, 'mni_from' => 0, 'imp_from' => $register->i_from, 'iv_from' => $register->iv_from,
              'mpt_degrees_from' => $register->mpt_from, 'color_from' => $register->color_from, 'ffa' => $register->ffa, 'mni' => 0,
              'imp' => $register->i, 'iv' => $register->iv, 'mpt_degrees' => $register->mpt, 'color' => $register->color,
              'no_segel1' => "", 'no_segel2' => "", 'no_segel3' => "", 'partner_name' => $res->partner_name,
              'no_do' => "-", 'origin' => $res->origin_no, 'qty_box' => 0, 'asal_pks' => 'SEI MANGKEI',
              'state' => NULL, 'create_uid' => 1, 'create_date' => date('Y-m-d H:i:s'), 'write_uid' => 1, 'write_date' => date('Y-m-d H:i:s'),     
              'date' => $register->dates, 'cloud_point' => 0, 'saponifiable_matter' => 0, 'peroxide_value' => 0, 
              'no_segel4' => NULL, 'moist_from' => $register->m_from, 'moist' => $register->m      
            );
//            print_r($postData);
            $postString = http_build_query($postData, '', '&');
            $req = $this->wb->request_auth('contract/post', $this->session->userdata('userid'), $postString, 1, 'POST');      
//            echo $req[1];
            if ($req[1] == 200){
                $response = json_decode($req[0]);
                // edit stockpicking truck id
                if ($this->contract->set_picking_truck($res->id, $response->result->id) == true){
                    echo 'true|truck picking id ['.$response->result->id.'] successful saved';
                }else{ echo 'error|Failed to update truck picking id..!'; $stts = false; }
            
            }else{ 
                echo 'error|['.$req[1].'] - Failed to post..!';
                $stts = false;
                break;
            } 
        }
        if ($stts == true){
          $data = array('approved' => 1);
          $this->model->update($pid, $data);
        }
    }
    
    private function post_gk($pid){
        
        $stts = true;
        $register = $this->model->get_by_id($pid)->row();
        $result = $this->qc->get_last($pid)->result();
        foreach ($result as $res) {
            $contract = $this->contract->get_by_id($res->contract_id)->row();
//            print_r($data);
            
              $netto_diff = $res->netto_from-$res->netto;
              $postData = array(
              'pickingid' => $contract->picking_id, 'pickingname' => $contract->picking_name,
              'sequence' => 10, 'product_id' => 1, 'product_uom_id' => 3, 'location_id' => 4,  'location_dest_id' => 8,
              'do' => $register->docno, 'nama_kendaraan' => "", 'no_container' => $res->gk_no, 'no_polisi' => $res->gk_no,
              'transporter' => "KERETA API", 'driver_name' => $res->driver, 'destination' => "PT INL", 'no_karcis_timbangan' => $res->ticket_no,
              'no_surat_jalan' => "", 'tgl_keluar_from' => $res->vendor_out, 'tgl_masuk_truk' => $res->dryport_incoming, 'tgl_keluar_truk' => $res->dryport_outgoing,
              'bruto_from' => $res->bruto_from, 'tarra_from' => $res->tara_from, 'netto_from' => $res->netto_from, 'bruto' => $res->bruto,
              'tarra' => $res->tara, 'qty_done' => $res->netto, 'netto_diff' => $res->netto_from-$res->netto, 'netto_diff_persen' => intval($netto_diff/$res->netto*100),
              'ffa_from' => $res->ffa_from, 'mni_from' => 0, 'imp_from' => $res->imp_from, 'iv_from' => $res->iv_from,
              'mpt_degrees_from' => $res->mpt_from, 'color_from' => $res->color_from, 'ffa' => $res->ffa, 'mni' => 0,
              'imp' => $res->imp, 'iv' => $res->iv, 'mpt_degrees' => $res->mpt, 'color' => $res->color,
              'no_segel1' => "", 'no_segel2' => "", 'no_segel3' => "", 'partner_name' => $contract->partner_name,
              'no_do' => "-", 'origin' => $contract->origin_no, 'qty_box' => 0, 'asal_pks' => $res->supplier,
              'state' => NULL, 'create_uid' => 1, 'create_date' => date('Y-m-d H:i:s'), 'write_uid' => 1, 'write_date' => date('Y-m-d H:i:s'),     
              'date' => $register->dates, 'cloud_point' => 0, 'saponifiable_matter' => 0, 'peroxide_value' => 0, 
              'no_segel4' => NULL, 'moist_from' => $res->moist_from, 'moist' => $res->moist      
            );
                    
//             print_r($postData);
                    
            $postString = http_build_query($postData, '', '&');
            $req = $this->wb->request_auth('contract/post', $this->session->userdata('userid'), $postString, 1, 'POST');      
//            echo $req[1];
            if ($req[1] == 200){
                $response = json_decode($req[0]);
                // edit stockpicking truck id
                if ($this->qc->set_picking_truck($res->id, $response->result->id) == true){
//                    $label = ;
                    echo 'true|truck picking id ['.$response->result->id.'] successful saved';
                }else{ echo 'error|Failed to update truck picking id..!'; $stts = false; }
            
            }else{ 
                echo 'error|['.$req[1].'] - Failed to post..!';
                $stts = false;
                break;
            }
        }
        // end loop
        
        if ($stts == true){
          $data = array('approved' => 1);
          $this->model->update($pid, $data);
        }
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
    
    function rollback($uid)
    {
       
       $journal = $this->model->get_by_id($uid)->row();
       
       if ($journal->type == "CARRIAGE"){
           $this->rollback_gk($uid);
       }
       elseif ($journal->type == "PIPELINE"){
           $this->rollback_pipeline($uid);
       }
    }
    
    function rollback_pipeline($regid){
        
       $stts = true; 
       $journal = $this->model->get_by_id($regid)->row();
       $result = $this->contract->get_last($regid);
       foreach ($result as $res) {
           $req = $this->wb->request_auth("contract/remove/".$res->picking_truck_id, $this->session->userdata('userid'), null, 1, 'GET');
//           echo $res->id.'<br/>';
//           print_r($req[1]);
//           print_r($req[0]);
           if ($req[1] == 200){
               $this->contract->set_picking_truck($res->id, NULL);
               echo "true|successfull rollback [ $res->picking_truck_id ]";
           }else{ break; $stts = false; echo "error|Failed to rollback [ $res->picking_truck_id ]"; }
       }
       
       if ($stts == true){ 
          $data = array('approved' => 0, 'validation' => 0);
          if ($this->model->update($regid, $data) == true){ 
              echo "true|$this->title [ $journal->code ] has been updated"; 
          }
          else{ echo "error|$this->title [ $journal->code ] failed been updated "; }
       }else{ echo "error|Failed to rollback [ $journal->code ]"; }
    }
    
    function rollback_gk($regid){
        
       $stts = true; 
       $journal = $this->model->get_by_id($regid)->row();
       $result = $this->qc->get_last($regid)->result();
       foreach ($result as $res) {
           $req = $this->wb->request_auth("contract/remove/".$res->picking_truck_id, $this->session->userdata('userid'), null, 1, 'GET');
//           echo $res->id.'<br/>';
//           print_r($req[1]);
//           print_r($req[0]);
           if ($req[1] == 200){
               $this->qc->set_picking_truck($res->id, NULL);
               echo "true|successfull rollback [ $res->picking_truck_id ]";
           }else{ break; $stts = false; echo "error|Failed to rollback [ $res->picking_truck_id ]"; }
       }
       
       if ($stts == true){ 
          $data = array('approved' => 0, 'validation' => 0);
          if ($this->model->update($regid, $data) == true){ 
              echo "true|$this->title [ $journal->code ] has been updated"; 
          }
          else{ echo "error|$this->title [ $journal->code ] failed been updated "; }
       }else{ echo "error|Failed to rollback [ $journal->code ]"; }
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
            $totank = explode('|', $this->input->post('cttotank'));
            
            $product = array('docno' => strtoupper($this->input->post('tdocno')),
                             'type' => strtoupper($this->input->post('ctype')),
                             'dates' => $this->input->post('tdate'), 'description' => $this->input->post('tnote'), 
                             'code' => $this->input->post('tcode'),
                             'source_tank' => $srctank, 'to_tank' => $totank[1], 'to_tank_id' => $totank[0],
                             'created' => date('Y-m-d H:i:s'));
            
            if ($this->model->add($product) == TRUE){echo 'true|'.$this->title.' successfully saved..!';}
        }
        else{ echo "error|".validation_errors(); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    // Fungsi update untuk menset texfield dengan nilai dari database
    function update($uid=null)
    {      
        $this->acl->otentikasi();
        $this->model->valid_add_trans($uid, $this->title);
        $register = $this->model->get_by_id($uid)->row();
        if ($register->type == "PIPELINE"){
            $data['main_view'] = 'registration_update_pipeline';
        }else{ $data['main_view'] = 'registration_update_carriage'; }
        
        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = 'Edit '.$this->modul['title'];
        
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
        $data['default']['netto_from'] = $register->netto_from;
        
        $data['default']['ffa_from'] = $register->ffa_from;
        $data['default']['m_from']  = $register->m_from;
        $data['default']['imp_from']  = $register->i_from;
        $data['default']['iv_from']  = $register->iv_from;
        $data['default']['mpt_from']  = $register->mpt_from;
        $data['default']['color_from']  = $register->color_from;
        
        $data['default']['ffa'] = $register->ffa;
        $data['default']['m']  = $register->m;
        $data['default']['i']  = $register->i;
        $data['default']['iv']  = $register->iv;
        $data['default']['mpt']  = $register->mpt;
        $data['default']['color']  = $register->color;
        
        
        $data['default']['qcstatus'] = $register->qc_status;
        $data['default']['desc'] = $register->description;
        
        $data['items'] = $this->contract->get_last($uid); // item contract
        
        // summary contract
        $con_summary = $this->contract->summary($uid);
        $data['contract_sum'] = $con_summary['contract_amount'];
        $data['outstanding_sum'] = $con_summary['outstanding_amount'];
        $data['transfer_sum'] = $con_summary['transfer_amount'];
        $data['netto_sum'] = $con_summary['netto_from'];
        
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
            
        $register = $this->model->get_by_id($uid)->row();

        $data['title'] = $this->properti['name'].' | Productistrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'product_update';
	$data['form_action'] = site_url($this->title.'/update_process/'.$uid);
	$data['link'] = array('link_back' => anchor('admin/','<span>back</span>', array('class' => 'back')));

	// Form validation
        $this->form_validation->set_rules('tdate', 'Transation Date', 'required');
       
        $this->form_validation->set_rules('cpicibl', 'PIC-IBL', 'callback_valid_ibl');
        $this->form_validation->set_rules('cpicobl', 'PIC-OBL', 'callback_valid_obl');
        $this->form_validation->set_rules('cpickinra', 'PIC-KINRA', 'callback_valid_kinra');
        $this->form_validation->set_rules('cpicqc', 'PIC-QC', 'callback_valid_qc');
        
        if ($register->type == "PIPELINE"){
            
            $this->form_validation->set_rules('tstart', 'Transfer Start', 'required|callback_valid_start');
            $this->form_validation->set_rules('tend', 'Transfer End', 'required|callback_valid_end');
            
            $this->form_validation->set_rules('tffa', 'FFA', 'required|numeric');
            $this->form_validation->set_rules('tmoist', 'Moisture', 'required|numeric');
            $this->form_validation->set_rules('timp', 'Impurities', 'required|numeric');
            $this->form_validation->set_rules('tiv', 'IV', 'required|numeric');
            $this->form_validation->set_rules('tmpt', 'Mpt', 'required|numeric');
            $this->form_validation->set_rules('tcolor', 'Color', 'required|numeric');

            $this->form_validation->set_rules('tnetto_from', 'Netto From', 'required|numeric');

            $this->form_validation->set_rules('tffa_from', 'FFA From', 'required|numeric');
            $this->form_validation->set_rules('tmoist_from', 'Moist From', 'required|numeric');
            $this->form_validation->set_rules('timp_from', 'Imp From', 'required|numeric');
            $this->form_validation->set_rules('tiv_from', 'IV From', 'required|numeric');
            $this->form_validation->set_rules('tmpt_from', 'Mpt From', 'required|numeric');
            $this->form_validation->set_rules('tcolor_from', 'Color From', 'required|numeric');
        }
        

        $this->form_validation->set_rules('cqcstatus', 'QC-Status', 'required|numeric');
        $this->form_validation->set_rules('csegelstatus', 'Segel-Status', 'required|numeric');
        $this->form_validation->set_rules('tdesc', 'Description', '');

        
        if ($this->form_validation->run($this) == TRUE && $this->valid_confirmation($uid) == TRUE)
        {
            if ($this->input->post('tpicibl') != ""){ $picibl = $this->input->post('tpicibl');}else{ $picibl = $this->input->post('cpicibl'); }
            if ($this->input->post('tpicobl') != ""){ $picobl = $this->input->post('tpicobl');}else{ $picobl = $this->input->post('cpicobl'); }
            if ($this->input->post('tpickinra') != ""){ $pickinra = $this->input->post('tpickinra');}else{ $pickinra = $this->input->post('cpickinra'); }
            if ($this->input->post('tpicqc') != ""){ $picqc = $this->input->post('tpicqc');}else{ $picqc = $this->input->post('cpicqc'); }
            
            if ($register->type == "PIPELINE"){
                $product = array('dates' =>$this->input->post('tdate'),
                             'start_transfer' => $this->input->post('tstart'), 'end_transfer' => $this->input->post('tend'),
                             'pic_1' => $picibl, 'pic_2' => $picobl, 'pic_3' => $pickinra, 'pic_qc' => $picqc,
                             'ffa' => $this->input->post('tffa'), 'm' => $this->input->post('tmoist'), 
                             'i' => $this->input->post('timp'), 'iv' => $this->input->post('tiv'), 
                             'mpt' => $this->input->post('tmpt'), 'color' => $this->input->post('tcolor'),
                             
                             'netto_from' => $this->input->post('tnetto_from'),
                             'ffa_from' => $this->input->post('tffa_from'), 'm_from' => $this->input->post('tmoist_from'), 
                             'i_from' => $this->input->post('timp_from'), 'iv_from' => $this->input->post('tiv_from'), 
                             'mpt_from' => $this->input->post('tmpt_from'), 'color_from' => $this->input->post('tcolor_from'),
                
                             'qc_status' => $this->input->post('cqcstatus'),'description' => $this->input->post('tdesc'),
                             'segel_status' => $this->input->post('csegelstatus')
                            );
                
            }elseif ($register->type == "CARRIAGE"){
                $product = array('dates' =>$this->input->post('tdate'),
                             'pic_1' => $picibl, 'pic_2' => $picobl, 'pic_3' => $pickinra, 'pic_qc' => $picqc,
                             'qc_status' => $this->input->post('cqcstatus'),'description' => $this->input->post('tdesc'),
                             'segel_status' => $this->input->post('csegelstatus')
                            );
            }
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
        $register = $this->model->get_by_id($uid)->row();
        
        $this->form_validation->set_rules('titem', 'Item Name', 'required|callback_valid_origin['.$uid.']');
        $this->form_validation->set_rules('tcontractqty', 'Contract Qty', 'required|numeric|is_natural_no_zero');
        $this->form_validation->set_rules('toustandingqty', 'Outstanding Qty', 'required|numeric|is_natural_no_zero|callback_valid_transfer_qty');
        $this->form_validation->set_rules('ttransferqty', 'Received Qty', 'required|numeric|is_natural_no_zero');
        $this->form_validation->set_rules('picking_id', 'Picking-ID', 'required');
        $this->form_validation->set_rules('picking_name', 'Picking-Name', 'required');
        $this->form_validation->set_rules('partner_name', 'Partner-Name', 'required');
        
        if ($register->type == 'PIPELINE'){
          $this->form_validation->set_rules('tfromqty', 'Sent Qty', 'required|numeric|is_natural_no_zero');
        }

        if ($this->form_validation->run($this) == TRUE && $this->valid_confirmation($uid) == TRUE)
        {
            $pitem = array('registration_id' => $uid, 
                           'origin_no' => $this->input->post('titem'),
                           'contract_amount' => $this->input->post('tcontractqty'),
                           'outstanding_amount' => $this->input->post('toustandingqty'),
                           'netto_from' => $this->input->post('tfromqty'),
                           'transfer_amount' => $this->input->post('ttransferqty'),
                           'picking_id' => $this->input->post('picking_id'),
                           'picking_name' => $this->input->post('picking_name'),
                           'partner_name' => $this->input->post('partner_name'),
                           'created' => date('Y-m-d H:i:s')
                          );
            
            $this->contract->add($pitem);
            echo 'true';
        }
        elseif ( $this->valid_confirmation($uid) != TRUE ){ echo "error|Can't change value - Journal validated..!"; }
        else{ echo 'error|'.validation_errors(); } 
    }
    
    function valid_origin($origin,$regid){
       if ($this->contract->valid_based_reg($origin, $regid) === FALSE){
          $this->form_validation->set_message('valid_origin','Origin Already Registered..!');
          return FALSE;
       }else{ return TRUE; }
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
        
        $contractamt = $this->contract->cek_contract_amount($regid);
        
        if ($register->validation == 1){ $error = 'Transaction has been validated, Rollback first..!'; $status = FALSE; }
        else if ($register->approved == 1){ $error = 'Transaction has been posted, Cant edited..!'; $status = FALSE; }
        else if ($this->contract->cek_trans('registration_id',$regid) == FALSE){ $error = 'Origin / Contract List Not Found..!'; $status = FALSE; }
        else if ($this->qc->valid_based_type($regid, $register->type) == FALSE){ $error = "Qc data not found..!"; $status = FALSE; }
        
        $summ = $this->contract->summary($regid);
        if ($register->type == "PIPELINE"){
            
             $qtyreceived = $this->sounding->get_qty_receive($regid);
             $before = $this->sounding->get_by_period($regid,0);
             $after = $this->sounding->get_by_period($regid,1);
             $qty_source_before = $before->source_tonase;
             $qty_source_after = $after->source_tonase;
             
             $qtykirim = abs($qty_source_after-$qty_source_before);
             if ($summ['netto_from'] <> $qtykirim){  $error = "Invalid Contract Sent Amount..!"; $status = FALSE; }
             
             if ($this->sounding->count_row_based_registration($regid) == FALSE){ $error = 'Sounding List Not Complete..!'; $status = FALSE; }
             if ($qtyreceived <> $contractamt){ $error = 'Invalid Contract & Received Amount'; $status = FALSE; }
             if ($register->qc_status != 0){ $error = 'Invalid QC Status'; $status = FALSE; }
        }
        elseif ($register->type == "CARRIAGE"){
            
           $qcsum = $this->qc->summary($regid);
//           if ($summ['netto_from'] <> $qcsum['netto_from']){ $error = 'GK - Invalid Contract Sent Amount'; $status = FALSE; }
           if ($summ['transfer_amount'] <> $qcsum['netto']){ $error = 'GK - Invalid Contract Received Amount'; $status = FALSE; }
           
//           echo 'Netto Contract : '.$summ['transfer_amount'].'<br/>';
//           echo 'GK Netto : '.$qcsum['netto'].'<br/>';
//           echo 'Netto From Contract : '.$summ['netto_from'].'<br/>';
//           echo 'GK Netto From : '.$qcsum['netto_from'].'<br/>';
        }
        
//        echo 'Qty Received : '.$qtyreceived.'<br>';
//        echo 'Contract Amount : '.$contractamt.'<br>';
        
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
            $qcvalid = $this->qc->valid_based_contract($jid->registration_id, $id);
            
            if ( $this->valid_confirmation($jid->registration_id) == TRUE && $qcvalid == TRUE )
            {
                $this->contract->force_delete($id);
                echo 'true|Transaction removed..!';
            }
            elseif ($qcvalid != TRUE){ echo 'error|Contract related to another transaction..!'; }
            elseif ($this->valid_confirmation($jid->registration_id) != TRUE){ echo "error|Journal approved, can't deleted..!"; }
            
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