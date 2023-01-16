<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Tchange extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('Tchange_model', 'model', TRUE);

        $this->properti = $this->property->get();
        $this->acl->otentikasi();

        $this->modul = $this->components->get(strtolower(get_class($this)));
        $this->title = strtolower(get_class($this));

        $this->user = $this->load->library('admin_lib');
        $this->tank = new Tank_lib();
    }

    private $properti, $modul, $title, $tank;

    // ==== ajax ====
    function get_content($tank){ echo $this->tank->get_details($tank, 'content');}
    
    function index(){ $this->get_last(); }
    
    public function getdatatable($search=null,$tank='null')
    {
        if(!$search){ $result = $this->model->get_last_transfer($this->modul['limit'])->result(); }
        else{ $result = $this->model->search($this->tank->get_id_by_sku($tank))->result(); }
        
        if ($result){
	foreach($result as $res)
	{
	   $output[] = array ($res->id, $res->no, $res->notes, tglin($res->dates), 
                              $this->tank->get_details($res->tank_id, 'sku'), $res->from, $res->to, 
                              $res->approved, $res->log);
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
        $data['main_view'] = 'transfer_view';
	$data['form_action'] = site_url($this->title.'/add_process');
        $data['form_action_update'] = site_url($this->title.'/update_process');
        $data['form_action_del'] = site_url($this->title.'/delete_all');
        $data['form_action_report'] = site_url($this->title.'/report_process');
        $data['link'] = array('link_back' => anchor('main/','Back', array('class' => 'btn btn-danger')));
        
        $data['code'] = $this->model->counter();
        $data['content'] = $this->tank->combo_content();
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
        $this->table->set_heading('#','No', 'Code', 'Date', 'Tank', 'From', 'To', 'Action');

        $data['table'] = $this->table->generate();
        $data['source'] = site_url($this->title.'/getdatatable');
            
        // Load absen view dengan melewatkan var $data sbgai parameter
	$this->load->view('template', $data);
    }

    function confirmation($pid)
    {
        if ($this->acl->otentikasi3($this->title,'ajax') == TRUE){
            
        $transfer = $this->model->get_transfer_by_id($pid)->row();
        
        $p = new Period();
        $p->get();
        
        if ($transfer->approved == 1){ echo "warning|$this->title already approved..!"; }
        elseif ($this->tank->get_qty($transfer->tank_id, $p->month, $p->year) > 0){ echo "error|Invalid Qty..!"; }
        elseif ($this->model->valid_dates($transfer->dates,$transfer->tank_id) == FALSE){ echo 'error|Invalid Period'; }
        else
        {
            $datas = array('content' => $transfer->to);
            $this->tank->update($transfer->tank_id, $datas);
            
            $data = array('approved' => 1);
            $this->model->update_id($pid, $data);
            echo "true|$transfer->no confirmed..!";
        }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
//    ===================== approval ===========================================


    function delete($uid)
    {
        $this->acl->otentikasi_admin($this->title);
        $transfer = $this->model->get_by_id($uid)->row();
        
        $p = new Period();
        $p->get();

        if ($this->tank->get_qty($transfer->tank_id, $p->month, $p->year) > 0){ echo "error|Invalid Qty..!"; }
        elseif ($this->model->valid_dates($transfer->dates,$transfer->tank_id) == FALSE){ echo 'error|Invalid Period'; }
        elseif ($this->valid_period($transfer->dates) == TRUE ) // cek journal harian sudah di approve atau belum
        {
            if ($transfer->approved == 1)
            {
              $datas = array('content' => $transfer->from);
              $this->tank->update($transfer->tank_id, $datas);  
                
              $data = array('approved' => 0);
              $this->model->update_id($uid, $data);
            }
            else{$this->model->force_delete($uid); }
            echo "warning|1 $this->title successfully removed..!";
        }
        else{  echo "error|1 $this->title can't removed, journal approved..!"; } 
    }

    private function counter()
    {
        $res = 0;
        if ( $this->model->count() > 0 )
        {
           $this->model->select_max('no')->get();
           $res = intval($this->model->no+1);
        }
        else{ $res = 1; }
        return $res;
    }

    function add_process()
    {
       if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){

	// Form validation
        $this->form_validation->set_rules('tno', 'Docno', 'required|callback_valid_no');
        $this->form_validation->set_rules('tdate', 'Date', 'required|callback_valid_period');
        $this->form_validation->set_rules('tnote', 'Note', 'required');
        $this->form_validation->set_rules('ctank', 'Storage Tank', 'required');

        if ($this->form_validation->run($this) == TRUE)
        {
            $from = $this->input->post('tfrom');
            if ($this->input->post('tto')){ $to = $this->input->post('tto'); }else{ $to = $this->input->post('cto'); }
            if ($from != $to){
                
              $transfer = array('no' => $this->input->post('tno'), 'from' => strtoupper($from), 'to' => strtoupper($to),
                              'dates' => $this->input->post('tdate'), 'notes' => $this->input->post('tnote'),
                              'tank_id' => $this->input->post('ctank'), 'log' => $this->session->userdata('log'));
            
              $this->model->add($transfer);
              echo "true|One $this->title data successfully saved!|";  
            }else{ echo "error|Invalid Content"; }
        }
        else{ echo "error|".validation_errors(); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    function add_trans($id)
    {
        $this->acl->otentikasi2($this->title);
        $this->model->valid_add_trans($id, $this->title);
        
        $cash = $this->model->where('id',$id)->get();
        
        $data['title'] = $this->properti['name'].' | Administrator '.ucwords($this->modul['title']);
        $data['h2title'] = 'Create New '.$this->modul['title'];
	$data['form_action'] = site_url($this->title.'/update_process/'.$id);
        $data['form_action_item'] = site_url($this->title.'/add_item/'.$id);
        
        $data['customer'] = $this->customer->combo();
        $data['currency'] = $this->currency->combo();
        
        $data['code'] = $cash->no;
        $data['user'] = $this->session->userdata("username");
        $data['account'] = $this->account->combo_asset();
        
        $data['main_view'] = 'cash_form';
        $data['source'] = site_url($this->title.'/getdatatable');
        $data['link'] = array('link_back' => anchor($this->title,'Back', array('class' => 'btn btn-danger')));
        
        $data['default']['dates'] = $cash->dates;
        $data['default']['customer'] = $cash->customer;
        $data['default']['currency'] = $cash->currency;
        $data['default']['note'] = $cash->notes;
        $data['default']['desc'] = $cash->desc;
        $data['default']['acc'] = $cash->acc;
        $data['total'] = $cash->amount;
        $data['items'] = $this->transmodel->get_last_item($cash->id)->result();
        
        $this->load->view('template', $data);
    }


//    ==========================================================================================
    function update($uid=null)
    {        
        $res = $this->model->get_by_id($uid)->row_array();    
        echo implode("|", $res);
    }
    
    // Fungsi update untuk mengupdate db
    function update_process()
    {
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){

	// Form validation
        $this->form_validation->set_rules('tno', 'Docno', 'required');
        $this->form_validation->set_rules('tdate', 'Date', 'required|callback_valid_period');
        $this->form_validation->set_rules('tnote', 'Note', 'required');
        $this->form_validation->set_rules('ctank', 'Storage Tank', 'required');

        if ($this->form_validation->run($this) == TRUE && $this->valid_confirmation($this->input->post('tid')) == TRUE)
        {
            $from = $this->input->post('tfrom');
            if ($this->input->post('tto')){ $to = $this->input->post('tto'); }else{ $to = $this->input->post('cto'); }
            if ($from != $to){
                
              $transfer = array('from' => strtoupper($from), 'to' => strtoupper($to),
                              'dates' => $this->input->post('tdate'), 'notes' => $this->input->post('tnote'),
                              'tank_id' => $this->input->post('ctank'), 'log' => $this->session->userdata('log'));
            
              $this->model->update_id($this->input->post('tid'), $transfer);
              echo "true|One $this->title data successfully saved!|";  
            }else{ echo "error|Invalid Content"; }
        }
        elseif ($this->valid_confirmation($this->input->post('tid')) != TRUE){ echo "warning|Journal approved, can't updated..!"; }
        else{ echo 'error|'.validation_errors(); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
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
    
    public function valid_no($no)
    {
        if ($this->model->valid_no($no) == FALSE)
        {
            $this->form_validation->set_message('valid_no', "Order No already registered.!");
            return FALSE;
        }
        else {  return TRUE; }
    }    

// ===================================== PRINT ===========================================

   function invoice($id=null)
   {
       $this->acl->otentikasi2($this->title);
       $ap = $this->model->get_by_id($id)->row();

       $data['h2title'] = 'Print Invoice'.$this->modul['title'];

       $data['pono'] = $ap->no;
       $data['podate'] = tglin($ap->dates);
       $data['notes'] = $ap->notes;
       $data['from'] = $ap->from;
       $data['to'] = $ap->to;
       $data['tank'] = $this->tank->get_details($ap->tank_id, 'sku');
       $data['log'] = $this->session->userdata('log');
       
       if($ap->approved == 1){ $stts = 'A'; }else{ $stts = 'NA'; }
       $data['stts'] = $stts;
       $this->load->view('transfer_invoice', $data);
   }
   
   function invoice_po($no=null)
   {
       $this->acl->otentikasi2($this->title);
       $ap = $this->model->get_transfer_by_no($no)->row();

       $data['h2title'] = 'Print Invoice'.$this->modul['title'];

       $data['pono'] = $ap->no;
       $data['podate'] = tglin($ap->dates);
       $data['notes'] = $ap->notes;
       $data['from'] = $this->acc_type($ap->from);
       $data['to'] = $this->acc_type($ap->to);
       $data['currency'] = $ap->currency;
       $data['log'] = $this->session->userdata('log');

       $data['amount'] = $ap->amount;
       $terbilang = $this->load->library('terbilang');
       if ($ap->currency == 'IDR')
       { $data['terbilang'] = ucwords($terbilang->baca($ap->amount)).' Rupiah'; }
       else { $data['terbilang'] = ucwords($terbilang->baca($ap->amount)); }
       
       if($ap->approved == 1){ $stts = 'A'; }else{ $stts = 'NA'; }
       $data['stts'] = $stts;

//       if ($ap->approved != 1){ $this->load->view('rejected', $data); }
//       else { $this->load->view('apc_invoice', $data); }
       $this->load->view('transfer_invoice', $data);
   }
   
   function print_expediter($po=null)
   {
       $this->acl->otentikasi2($this->title);

       $data['h2title'] = 'Print Expediter'.$this->modul['title'];

       $cash = $this->Purchase_model->get_journal_by_no($po)->row();

       $data['pono'] = $po;
       $data['podate'] = tgleng($cash->dates);
       $data['vendor'] = $cash->prefix.' '.$cash->name;
       $data['address'] = $cash->address;
       $data['shipdate'] = tgleng($cash->shipping_date);
       $data['city'] = $cash->city;
       $data['phone'] = $cash->phone1;
       $data['phone2'] = $cash->phone2;
       $data['desc'] = $cash->desc;
       $data['user'] = $this->user->get_username($cash->user);
       $data['currency'] = $this->currency->get_code($cash->currency);
       $data['docno'] = $cash->docno;

       $data['cost'] = $cash->costs;
       $data['p2'] = $cash->p2;
       $data['p1'] = $cash->p1;

       $data['items'] = $this->Purchase_item_model->get_last_item($po)->result();

       // property display
       $data['p_name'] = $this->properti['name'];
       $data['paddress'] = $this->properti['address'];
       $data['p_phone1'] = $this->properti['phone1'];
       $data['p_phone2'] = $this->properti['phone2'];
       $data['p_city'] = ucfirst($this->properti['city']);
       $data['p_zip'] = $this->properti['zip'];
       $data['p_npwp'] = $this->properti['npwp'];

       $this->load->view('journal_expediter', $data);
   }

// ===================================== PRINT ===========================================

// ====================================== REPORT =========================================

    function report_process()
    {
        $this->acl->otentikasi2($this->title);
        $data['title'] = $this->properti['name'].' | Report '.ucwords($this->modul['title']);

        $period = $this->input->post('reservation');  
        $start = picker_between_split($period, 0);
        $end = picker_between_split($period, 1);

        $data['start'] = $start;
        $data['end'] = $end;
        $data['rundate'] = tglin(date('Y-m-d'));
        $data['log'] = $this->session->userdata('log');

//        Property Details
        $data['company'] = $this->properti['name'];
        $data['reports'] = $this->model->report($start,$end)->result();
        
        $this->load->view('transfer_report', $data); 
        
    }


// ====================================== REPORT =========================================
    
// ====================================== CLOSING ======================================
   function reset_process(){ $this->model->closing(); }     

}

?>