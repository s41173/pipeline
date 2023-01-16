<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once 'definer.php';

class Sounding extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('Sounding_model', 'model', TRUE);
        
        $this->properti = $this->property->get();
        $this->acl->otentikasi();

        $this->modul = $this->components->get(strtolower(get_class($this)));
        $this->title = strtolower(get_class($this));
       
        $this->user = new Admin_lib();
        $this->period = new Period_lib();
        $this->tank = new Tank_lib();
        $this->api = new Api_lib();
    }

    private $properti, $modul, $title;
    private $user,$period,$tank,$api;


    function index()
    {
       $this->get_last();
    }
    
//    ===================  ajax function ================
    
    function get_density($tank=0,$temp=0){
        
      $apiid = $this->tank->get_details($tank, 'unique_id');  
      $response = $this->api->request(api.'/'.$apiid.'/suhu/'.$temp, null, 'code', 'GET');
      $result = (array) json_decode($response[0], true); 
      $coeff = $this->tank->get_details($tank, 'coeff');
      
      if ($response[1] == 200){ echo 'true|'.$result['density'][0]['nilai_densitas'].'|'.$coeff; }else{ echo 'error|'.$result['title']; }  
    }
    
    function get_massa($tank=0,$height=0,$temp=0){
        
        $apiid = $this->tank->get_details($tank, 'unique_id');
        $height = floatval($height/100);
        $href = floatval($this->tank->get_details($tank, 'measuring')/100);
        
        $param = '{ "tinggi":'.$height.', "suhu":'.$temp.', "hRef":'.$href.' }';
        $response = $this->api->request(api.'/massa/'.$apiid, $param, 'code', 'POST');
        $result = (array) json_decode($response[0], true);
        
        $reskg = $result['attributes']['result']; // kg
        $resvol = $result['relationships']['data']['result']; // liter
        
        if ($response[1] == 200){ echo 'true|'.$reskg.'|'.$resvol;}else{ echo 'error|'.$result['title']; }
    }
    
    
    public function getdatatable($search=null,$no='null',$dates='null')
    {
        if(!$search){ $result = $this->model->get_last($this->modul['limit'])->result(); }
        else {$result = $this->model->search($no,$dates)->result(); }
        
        if ($result){
	foreach($result as $res)
	{  
	   $output[] = array ($res->id, $res->docno, $this->tank->get_details($res->tank_id, 'sku'), tglin($res->dates), $res->notes, $res->sounding, $res->netkg, $res->approved);
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

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'sounding_view';
	$data['form_action'] = site_url($this->title.'/add_process');
        $data['form_action_update'] = site_url($this->title.'/update_process');
        $data['form_action_del'] = site_url($this->title.'/delete_all');
        $data['form_action_report'] = site_url($this->title.'/report_process');
        $data['link'] = array('link_back' => anchor('main/','Back', array('class' => 'btn btn-danger')));
	// ---------------------------------------- //
       
        $data['tank'] = $this->tank->combo();
        
        $config['first_tag_open'] = $config['last_tag_open']= $config['next_tag_open']= $config['prev_tag_open'] = $config['num_tag_open'] = '<li>';
        $config['first_tag_close'] = $config['last_tag_close']= $config['next_tag_close']= $config['prev_tag_close'] = $config['num_tag_close'] = '</li>';

        $config['cur_tag_open'] = "<li><span><b>";
        $config['cur_tag_close'] = "</b></span></li>";

        // library HTML table untuk membuat template table class zebra
        $tmpl = array('table_open' => '<table id="datatable-buttons" class="table table-striped table-bordered">');

        $this->table->set_template($tmpl);
        $this->table->set_empty("&nbsp;");

        //Set heading untuk table
        $this->table->set_heading('#', 'No', 'Docno', 'Date', 'Tank', 'Result', 'Action');

        $data['table'] = $this->table->generate();
        $data['source'] = site_url($this->title.'/getdatatable');
            
        // Load absen view dengan melewatkan var $data sbgai parameter
	$this->load->view('template', $data);
    }

    function search()
    {
        $this->acl->otentikasi1($this->title);

        $data['title'] = $this->properti['name'].' | Administrator Find '.ucwords($this->modul['title']);
        $data['h2title'] = 'Find '.$this->modul['title'];
        $data['main_view'] = 'journal_view';
	$data['form_action'] = site_url($this->title.'/search');
        $data['link'] = array('link_back' => anchor($this->title,'<span>back</span>', array('class' => 'back')));

        $data['jurnaltype'] = $this->journaltype->combo_all();
        $journals = $this->get_search($this->input->post('tno'), $this->input->post('cref'), $this->input->post('tdate'));
        
        $tmpl = array('table_open' => '<table cellpadding="2" cellspacing="1" class="tablemaster">');

        $this->table->set_template($tmpl);
        $this->table->set_empty("&nbsp;");

        //Set heading untuk table
        $this->table->set_heading('No', 'Code', 'Cur', 'Date', 'Notes', 'Balance', 'Action');

        $i = 0;
        foreach ($journals as $journal)
        {
//                $datax = array('name'=> 'cek[]','id'=> 'cek'.$i,'value'=> $journal->id,'checked'=> FALSE, 'style'=> 'margin:0px');

            $this->table->add_row
            (
                ++$i, $journal->code.'-'.$journal->no, $journal->currency, tglin($journal->dates), $journal->notes, number_format($journal->balance),
                anchor($this->title.'/confirmation/'.$journal->id,'<span>update</span>',array('class' => $this->post_status($journal->approved), 'title' => 'edit / update')).' '.
                anchor_popup($this->title.'/invoice/'.$journal->no.'/'.$journal->code,'<span>print</span>',$this->atts).' '.
                anchor($this->title.'/add_trans/'.$journal->no.'/'.$journal->code,'<span>details</span>',array('class' => 'update', 'title' => '')).' '.
                anchor($this->title.'/delete/'.$journal->id.'/'.$journal->no,'<span>delete</span>',array('class'=> 'delete', 'title' => 'delete' ,'onclick'=>"return confirm('Are you sure you will delete this data?')"))
            );
        }

        $data['table'] = $this->table->generate();
        $this->load->view('template', $data);
    }


    function get_list($currency=null,$vendor=null)
    {
        $this->acl->otentikasi1($this->title);

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['form_action'] = site_url($this->title.'/get_list');
        $data['main_view'] = 'vendor_list';
        $data['currency'] = $this->currency->combo();
        $data['link'] = array('link_back' => anchor($this->title.'/get_list','<span>back</span>', array('class' => 'back')));

        $currency = $this->input->post('ccurrency');
        $vendor = $this->vendor->get_vendor_id($this->input->post('tvendor'));

        $journals = $this->Purchase_model->get_journal_list($currency,$vendor)->result();

        $tmpl = array('table_open' => '<table cellpadding="2" cellspacing="1" class="tablemaster">');

        $this->table->set_template($tmpl);
        $this->table->set_empty("&nbsp;");

        //Set heading untuk table
        $this->table->set_heading('No', 'Code', 'Date', 'Cur', 'Notes', 'Total', 'Balance', 'Action');

        $i = 0;
        foreach ($journals as $journal)
        {
           $datax = array(
                            'name' => 'button',
                            'type' => 'button',
                            'content' => 'Select',
                            'onclick' => 'setvalue(\''.$journal->no.'\',\'titem\')'
                         );

            $this->table->add_row
            (
                ++$i, 'PO-00'.$journal->no, tgleng($journal->dates), $journal->currency, $journal->notes, number_format($journal->total,3), number_format($journal->p2,3),
                form_button($datax)
            );
        }

        $data['table'] = $this->table->generate();
        $this->load->view('journal_list', $data);
    }

//    ===================== approval ===========================================

    private function post_status($val)
    {
       if ($val == 0) {$class = "notapprove"; }
       elseif ($val == 1){$class = "approve"; }
       return $class;
    }

    function confirmation($pid)
    {
        if ($this->acl->otentikasi3($this->title,'ajax') == TRUE){
        $journal = $this->model->get_by_id($pid)->row();
        
        $ps = $this->period->get();

        if ($journal->approved == 1) { echo "warning|$this->title already approved..!"; }
        elseif ($journal->netkg == 0){ echo "error|$this->title has no value..!"; } // validasi sounding harian
        elseif ($this->valid_period($pid,$journal->dates) == FALSE ){ echo "error|Invalid period..!"; }
        else
        {
           $data = array('approved' => 1);
           $this->model->update($pid, $data);
           echo "true| $journal->docno confirmed..!";
        }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    

//    ===================== approval ===========================================


    function delete($uid)
    {
        if ($this->acl->otentikasi_admin($this->title,'ajax') == TRUE){
        $val = $this->model->get_by_id($uid)->row();

           if ($val->approved == 1) 
           {
               $data = array('approved' => 0);
               $this->model->update($uid, $data);
               echo "true|1 $this->title successfully rollback..!";
           }
           else
           {
              $this->model->force_delete($uid);
              echo "true|1 $this->title successfully removed..!";
           }
           
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }

    function counter($type='GJ',$target='non')
    { 
        $res = 0;
        if ( $this->model->count() > 0 )
        {
           $this->model->select_max('id');
           $this->model->where('doctype', $type)->get();
           $res = intval($this->model->id+1).waktuindo();
        }  
        else{ $res = intval(1).'_'.date('m-d-Y').waktuindo(); }
        if ($target=='non'){ return $res; }else{ echo $res; }
    }
    

    function add()
    {
        $this->acl->otentikasi2($this->title);

        $data['title'] = $this->properti['name'].' | Administrator '.ucwords($this->modul['title']);
        $data['h2title'] = 'New '.$this->modul['title'];
	$data['form_action'] = site_url($this->title.'/add_process');

        $data['user'] = $this->session->userdata("username");
        $data['main_view'] = 'sounding_form';
        $data['source'] = site_url($this->title.'/getdatatable');
        $data['link'] = array('link_back' => anchor($this->title,'Back', array('class' => 'btn btn-danger')));
        $data['user'] = $this->session->userdata('username');
        
        $data['tank'] = $this->tank->combo();
        $this->load->view('template', $data);
    }

    function add_process()
    {
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){

	// Form validation
        $this->form_validation->set_rules('tno', 'Doc-No', 'required|callback_valid_no');
        $this->form_validation->set_rules('tdate', 'Invoice Date', 'required');
        $this->form_validation->set_rules('tnote', 'Note / Remarks', 'required');
        $this->form_validation->set_rules('ctank', 'Tank', 'required');
        $this->form_validation->set_rules('tsounding', 'Sounding', 'required|numeric|is_natural_no_zero');
        $this->form_validation->set_rules('tcorr', 'Correction', 'required|numeric');
        $this->form_validation->set_rules('tacorr', 'After Correction', 'required|numeric|is_natural_no_zero');
        $this->form_validation->set_rules('ttemp', 'Temperature', 'required|numeric|is_natural_no_zero');
        $this->form_validation->set_rules('tdensity', 'Density', 'required|numeric');
        $this->form_validation->set_rules('tcoeff', 'Coeffissien', 'required|numeric');
        $this->form_validation->set_rules('tobv', 'OBV', 'required');
        $this->form_validation->set_rules('tadj', 'Adj', 'required|numeric');
        $this->form_validation->set_rules('tvcv', 'VCV', 'required|numeric');
        $this->form_validation->set_rules('tnetkg', 'Net-Kg', 'required');
        $this->form_validation->set_rules('tmetricton', 'Metric Ton', 'required');
        $this->form_validation->set_rules('ttable56', 'Table-56', 'required');
        $this->form_validation->set_rules('tffa', 'FFA', 'required');
        $this->form_validation->set_rules('tmoisture', 'Moisture', 'required');
        $this->form_validation->set_rules('tdirt', 'Dirt', 'required');
        $this->form_validation->set_rules('tuser', 'User', 'required');

        if ($this->form_validation->run($this) == TRUE)
        {            
            $product = array('docno' => strtoupper($this->input->post('tno')), 'dates' => $this->input->post('tdate'), 'notes' => $this->input->post('tnote'), 
                             'tank_id' => $this->input->post('ctank'), 'sounding' => $this->input->post('tsounding'), 'corr' => $this->input->post('tcorr'),
                             'after_corr' => $this->input->post('tacorr'), 'temperature' => $this->input->post('ttemp'), 'density' => $this->input->post('tdensity'),
                             'coeff' => $this->input->post('tcoeff'), 'obv' => $this->input->post('hobv'), 'adj' => $this->input->post('tadj'),
                             'vcv' => $this->input->post('tvcv'), 'table-56' => $this->input->post('ttable56'), 'netkg' => $this->input->post('hnetkg'),
                             'ffa' => $this->input->post('tffa'), 'moisture' => $this->input->post('tmoisture'), 'dirt' => $this->input->post('tdirt'),
                             'log' => $this->session->userdata('log'),
                             'created' => date('Y-m-d H:i:s'));
            
            if ($this->model->add($product) == TRUE){echo 'true|'.$this->title.' successfully saved..!';}
            
        }
        else{ echo 'error|'.validation_errors(); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    function update($id)
    {
        $this->acl->otentikasi2($this->title);
        $this->model->valid_add_trans($id, $this->title);
                 
        $data['title'] = $this->properti['name'].' | Administrator '.ucwords($this->modul['title']);
        $data['h2title'] = 'Edit '.$this->modul['title'];
	$data['form_action'] = site_url($this->title.'/update_process/');
        $data['tank'] = $this->tank->combo();
        $data['main_view'] = 'sounding_form';
        $data['source'] = site_url($this->title.'/getdatatable');
        $data['link'] = array('link_back' => anchor($this->title,'Back', array('class' => 'btn btn-danger')));
        
        $journal = $this->model->get_by_id($id)->row();
        $data['default']['docno'] = $journal->docno;
        $data['default']['dates'] = $journal->dates;
        $data['default']['tank'] = $journal->tank_id;
        $data['default']['notes'] = $journal->notes;
        $data['default']['sounding'] = $journal->sounding;
        $data['default']['corr'] = $journal->corr;
        $data['default']['after_corr'] = $journal->after_corr;
        $data['default']['temperature'] = $journal->temperature;
        $data['default']['density'] = $journal->density;
        $data['default']['coeff'] = $journal->coeff;
        $data['default']['obv'] = $journal->obv;
        $data['default']['adj'] = $journal->adj;
        $data['default']['vcv'] = $journal->vcv;
        $data['default']['table56'] = 0;
        $data['default']['netkg'] = $journal->netkg;
        $data['default']['ffa'] = $journal->ffa;
        $data['default']['moisture'] = $journal->moisture;
        $data['default']['dirt'] = $journal->dirt;
        $data['default']['metric'] = round(floatval($journal->netkg)*floatval($journal->coeff));
        $data['user'] = $this->session->userdata('username');
        $data['default']['tid'] = $id;
        
        $this->load->view('template', $data);
    }


    // Fungsi update untuk mengupdate db
    function update_process()
    {
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){
            
        $jid = $this->input->post('tid');  
        
        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
	$data['form_action'] = site_url($this->title.'/update_process/'.$jid);
	$data['link'] = array('link_back' => anchor('journal/','<span>back</span>', array('class' => 'back')));

	// Form validation
        $this->form_validation->set_rules('tno', 'Doc-No', 'required|callback_validating_no['.$jid.']');
        $this->form_validation->set_rules('tdate', 'Invoice Date', 'required');
        $this->form_validation->set_rules('tnote', 'Note / Remarks', 'required');
        $this->form_validation->set_rules('ctank', 'Tank', 'required');
        $this->form_validation->set_rules('tsounding', 'Sounding', 'required|numeric|is_natural_no_zero');
        $this->form_validation->set_rules('tcorr', 'Correction', 'required|numeric');
        $this->form_validation->set_rules('tacorr', 'After Correction', 'required|numeric|is_natural_no_zero');
        $this->form_validation->set_rules('ttemp', 'Temperature', 'required|numeric|is_natural_no_zero');
        $this->form_validation->set_rules('tdensity', 'Density', 'required|numeric');
        $this->form_validation->set_rules('tcoeff', 'Coeffissien', 'required|numeric');
        $this->form_validation->set_rules('tobv', 'OBV', 'required');
        $this->form_validation->set_rules('tadj', 'Adj', 'required|numeric');
        $this->form_validation->set_rules('tvcv', 'VCV', 'required|numeric');
        $this->form_validation->set_rules('tnetkg', 'Net-Kg', 'required');
        $this->form_validation->set_rules('tmetricton', 'Metric Ton', 'required');
        $this->form_validation->set_rules('ttable56', 'Table-56', 'required');
        $this->form_validation->set_rules('tffa', 'FFA', 'required');
        $this->form_validation->set_rules('tmoisture', 'Moisture', 'required');
        $this->form_validation->set_rules('tdirt', 'Dirt', 'required');
        $this->form_validation->set_rules('tuser', 'User', 'required');
        
        if ($this->form_validation->run($this) == TRUE && $this->valid_confirmation($jid) == TRUE)
        {
            
            $product = array('docno' => strtoupper($this->input->post('tno')), 'dates' => $this->input->post('tdate'), 'notes' => $this->input->post('tnote'), 
                 'tank_id' => $this->input->post('ctank'), 'sounding' => $this->input->post('tsounding'), 'corr' => $this->input->post('tcorr'),
                 'after_corr' => $this->input->post('tacorr'), 'temperature' => $this->input->post('ttemp'), 'density' => $this->input->post('tdensity'),
                 'coeff' => $this->input->post('tcoeff'), 'obv' => $this->input->post('hobv'), 'adj' => $this->input->post('tadj'),
                 'vcv' => $this->input->post('tvcv'), 'table-56' => $this->input->post('ttable56'), 'netkg' => $this->input->post('hnetkg'),
                 'ffa' => $this->input->post('tffa'), 'moisture' => $this->input->post('tmoisture'), 'dirt' => $this->input->post('tdirt'),
                 'log' => $this->session->userdata('log'));
            
            $this->model->update($jid, $product);
            echo "true|One $this->title data successfully updated!|".$jid;
        }
        else{ echo 'error|'.validation_errors(); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }


    public function valid_period($uid,$date=null)
    {
        if ($this->model->validating('dates',$date,$uid) == FALSE)
        {
            $this->form_validation->set_message('valid_period', "Invalid Period.!");
            return FALSE;
        }
        else {  return TRUE; }
    }

    public function valid_no($no)
    {
        if ($this->model->valid('docno',$no) == FALSE)
        {
            $this->form_validation->set_message('valid_no', "Document No already registered.!");
            return FALSE;
        }
        else {  return TRUE; }
    }
    
    public function validating_no($no,$uid)
    {
        if ($this->model->validating('docno',$no,$uid) == FALSE)
        {
            $this->form_validation->set_message('validating_no', "Document No already registered.!");
            return FALSE;
        }
        else {  return TRUE; }
    }

    public function valid_confirmation($id)
    {
        $val = $this->model->get_by_id($id)->row();

        if ($val->approved == 1)
        {
            $this->form_validation->set_message('valid_confirmation', "Can't change value - Journal approved..!.!");
            return FALSE;
        }
        else {  return TRUE; }
    }

// ===================================== PRINT ===========================================
    

   function invoice($pid)
   {
        $this->acl->otentikasi1($this->title);
        $this->model->valid_add_trans($pid, $this->title);
        $journal = $this->model->get_by_id($pid)->row();

        $data['title'] = $this->properti['name'].' | Administrator '.ucwords($this->modul['title']);
        $data['dates'] = $journal->dates;
        $data['codetrans'] = $journal->docno;
        $data['user'] = $this->session->userdata("username");

        $data['tank'] = $this->tank->get_details($journal->tank_id, 'sku');
        $data['notes'] = $journal->notes;
        $data['sounding'] = $journal->sounding;
        $data['corr'] = $journal->corr;
        $data['after_corr'] = $journal->after_corr;
        $data['temperature'] = $journal->temperature;
        $data['density'] = $journal->density;
        $data['coeff'] = $journal->coeff;
        $data['obv'] = $journal->obv;
        $data['adj'] = $journal->adj;
        $data['vcv'] = $journal->vcv;
//        $data['table56'] = $journal->table-56;
        $data['netkg'] = $journal->netkg;
        $data['ffa'] = $journal->ffa;
        $data['moisture'] = $journal->moisture;
        $data['dirt'] = $journal->dirt;
        
        $this->load->view('sounding_invoice', $data);
   }
   
   function invoice_po($docno)
   {
        $this->acl->otentikasi1($this->title);
        $journal = $this->model->where('docno', $docno)->get();
        $this->model->valid_add_trans($journal->id, $this->title);

        $data['title'] = $this->properti['name'].' | Administrator '.ucwords($this->modul['title']);
        $data['code'] = $journal->no;
        $data['codetrans'] = $journal->code;
        $data['user'] = $this->session->userdata("username");

        $data['dates'] = $journal->dates;
        $data['currency'] = $journal->currency;
        $data['notes'] = $journal->notes;
        $data['desc'] = $journal->desc;
        $data['docno'] = $journal->docno;
        $data['balance'] = $journal->balance;
        
        $res = $this->get_debit_credit($journal->id);
        $data['debit']   = $res[0];
        $data['credit']  = $res[1];
        $data['balances'] = idr_format($res[2],2);

//        ============================ Item  =========================================
        $data['items'] = $this->mitem->where('glid', $journal->id)->order_by('id', 'asc')->get();

        $this->load->view('journal_invoice', $data);
   }

// ===================================== PRINT ===========================================

// ====================================== REPORT =========================================

    function report_process()
    {
        $this->acl->otentikasi2($this->title);
        $data['title'] = $this->properti['name'].' | Report '.ucwords($this->modul['title']);

        $cur = $this->input->post('ccurrency');
        $journal = $this->input->post('cjournal');
        
        $period = $this->input->post('reservation');  
        $start = picker_between_split($period, 0);
        $end = picker_between_split($period, 1);

        $data['start'] = $start;
        $data['end'] = $end;
        $data['rundate'] = tgleng(date('Y-m-d'));
        $data['log'] = $this->session->userdata('log');

//        Property Details
        $data['company'] = $this->properti['name'];
        
        $data['journals'] = $this->model->report($cur,$journal,$start,$end)->result();
        $this->load->view('sounding_report', $data); 
    }
    
        // ====================================== CLOSING ====================================== 
   function reset_process(){ $this->model->closing(); $this->model->closing_trans(); }


// ====================================== REPORT =========================================

}

?>