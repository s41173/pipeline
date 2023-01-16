<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once 'definer.php';

class Contract extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('Contract_model', 'model', TRUE);

        $this->properti = $this->property->get();
        $this->acl = new Acl();
        $this->acl->otentikasi();

        $this->modul = $this->components->get(strtolower(get_class($this)));
        $this->title = strtolower(get_class($this));
        $this->role = new Role_lib();
        $this->currency = new Currency_lib();
        $this->period = new Period_lib();
        $this->period = $this->period->get();
        $this->balance = new Tank_balance_lib();
        $this->ledger = new Tankledger_lib();
        $this->api = new Api_lib();
        $this->customer = new Customer_lib();
        $this->vendor = new Vendor_lib();
        $this->journalgl = new Journalgl_lib();
        $this->tax = new Tax_lib();
        $this->sales = new Csales_lib();
        $this->account = new Account_lib();
    }

    private $properti, $modul, $title, $api, $customer, $vendor, $journalgl;
    private $role,$balance,$ledger,$tax,$sales,$account,$acl;

    
    function index(){
        $this->session->unset_userdata('start'); $this->session->unset_userdata('end'); $this->get_last(); 
    }
         
    public function getdatatable($search=null,$date='null',$cust='null',$type='null')
    {
        if ($search == 'deleted'){ $result = $this->model->get_deleted($this->modul['limit'])->result(); } 
        elseif ($search != 'deleted' && $search != null){ $result = $this->model->search($date,$cust)->result(); }
        else{ $result = $this->model->get_last($this->modul['limit'])->result(); }
        
        $output = null;
        if ($result){
          
         foreach($result as $res)
	 {
           if ($res->status == 0){ $status = 'C'; }else{ $status = 'S'; }
	   $output[] = array ($res->id, tglin($res->dates), $res->docno, $this->customer->get_name($res->cust_id), tglin($res->starts), tglin($res->ends), 
                              $res->approved, $res->amount, $res->balance, $status);
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
        $data['main_view'] = 'contract_view';
	$data['form_action'] = site_url($this->title.'/add_process');
        $data['form_action_update'] = site_url($this->title.'/update_process');
        $data['form_action_del'] = site_url($this->title.'/delete_all');
        $data['form_action_report'] = site_url($this->title.'/report_process');
        $data['form_action_import'] = site_url($this->title.'/import');
        $data['link'] = array('link_back' => anchor('main/','Back', array('class' => 'btn btn-danger')));

        $data['customer'] = $this->customer->combo();
        $data['currency'] = $this->currency->combo();
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
        $this->table->set_heading('#','No', 'Code', 'Date', 'Doc-No', 'Customer', 'Period', 'Amount', 'Balance', 'Status', 'Action');

        $data['table'] = $this->table->generate();
        $data['source'] = site_url($this->title.'/getdatatable');
        $data['graph'] = site_url()."/".$this->title."/chart/";
            
        // Load absen view dengan melewatkan var $data sbgai parameter
	$this->load->view('template', $data);
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
            $sales = $this->sales->get_based_contract($uid)->num_rows();
            
            if($this->valid_period($journal->dates) != TRUE){ echo "error|Invalid period..!"; }
            elseif($sales > 0){ echo "error|transaction has a sales transaction!"; }
            else{
              if ($journal->approved == 1){ $this->rollback($uid); echo "true|1 $this->title successfully rollback..!"; }
              else { $this->remove($uid); echo "true|1 $this->title successfully removed..!"; }
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
        $this->form_validation->set_rules('tno', 'Document No', 'callback_valid_docno');
        $this->form_validation->set_rules('tdate', 'Transaction date', 'required|callback_valid_period');
        $this->form_validation->set_rules('tnote', 'Note / Remarks', 'required');
        $this->form_validation->set_rules('ccust', 'Consignee', 'required');
        
        $this->form_validation->set_rules('tsalesacc', 'Sales-Acc', 'required');
        $this->form_validation->set_rules('taracc', 'AR-Acc', 'required');
        $this->form_validation->set_rules('ttaxacc', 'Tax-Acc', 'required');

        if ($this->form_validation->run($this) == TRUE)
        {
            $product = array('docno' => strtoupper($this->input->post('tno')),
                             'dates' => $this->input->post('tdate'), 'notes' => $this->input->post('tnote'), 
                             'cust_id' => $this->input->post('ccust'),
                             'sales_account' => $this->account->get_id_code($this->input->post('tsalesacc')), 
                             'ar_account' => $this->account->get_id_code($this->input->post('taracc')),
                             'tax_account' => $this->account->get_id_code($this->input->post('ttaxacc')),
                             'created' => date('Y-m-d H:i:s'));
            
            if ($this->model->add($product) == TRUE){echo 'true|'.$this->title.' successfully saved..!';}
        }
        else{ echo "error|".validation_errors(); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    // Fungsi update untuk menset texfield dengan nilai dari database
    function update($uid=null)
    {        
        $this->model->valid_add_trans($uid, $this->title);
        
        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = 'Edit '.$this->modul['title'];
        $data['main_view'] = 'contract_update';
	$data['form_action'] = site_url($this->title.'/update_process/'.$uid);
        $data['link'] = array('link_back' => anchor($this->title,'Back', array('class' => 'btn btn-danger')));

        $data['source'] = site_url($this->title.'/getdatatable');
        $data['array'] = array('','');
        $data['graph'] = site_url()."/product/chart/";
        $data['customer'] = $this->customer->combo();
        $data['tax'] = $this->tax->combo();
        $data['vendor'] = $this->vendor->combo();
        
        $product = $this->model->get_by_id($uid)->row();

        $data['uid'] = $uid;
        $data['default']['date']   = $product->dates;
        $data['default']['docno']  = $product->docno;
        $data['default']['cust']   = $product->cust_id;
        $data['default']['start']  = $product->starts;
        $data['default']['end']    = $product->ends;
        $data['default']['note']  = $product->notes;
        $data['default']['amount'] = $product->amount;
        $data['default']['tax'] = $product->tax;
        $data['default']['taxval'] = $product->tax_val;
        
        $data['default']['salesacc'] = $this->account->get_code($product->sales_account);
        $data['default']['taxacc']   = $this->account->get_code($product->tax_account);
        $data['default']['aracc']    = $this->account->get_code($product->ar_account);
        
        $this->session->set_userdata('langid', $uid);
        $this->load->view('template', $data);
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
    
    
    function valid_docno($val)
    {
        if ($this->model->valid('docno',$val) == FALSE)
        {
            $this->form_validation->set_message('valid_docno','Document No registered..!');
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
    
    function valid_name($val)
    {
        if ($this->model->valid('name',$val) == FALSE)
        {
            $this->form_validation->set_message('valid_name','Name registered..!');
            return FALSE;
        }
        else{ return TRUE; }
    }


    function validating_docno($val)
    {
	$id = $this->session->userdata('langid');
	if ($this->model->validating('docno',$val,$id) == FALSE)
        {
            $this->form_validation->set_message('validating_docno', "Docno registered!");
            return FALSE;
        }
        else{ return TRUE; }
    }
    
    function valid_confirmation($uid=0){
        $val = $this->model->get_by_id($uid)->row();
        if ($val->approved == 1){
          $this->form_validation->set_message('valid_confirmation', "Transaction Posted, Can't Editable..!");
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
	$data['form_action'] = site_url($this->title.'/update_process');
	$data['link'] = array('link_back' => anchor('admin/','<span>back</span>', array('class' => 'back')));

	// Form validation
        $this->form_validation->set_rules('tid', 'ID', 'required|callback_valid_confirmation');
        $this->form_validation->set_rules('tdocno', 'Doc-No', 'required|callback_validating_docno|callback_valid_deleted');
        $this->form_validation->set_rules('tdates', 'Transaction Dates', 'required|callback_valid_period');
        $this->form_validation->set_rules('ccust', 'Customer', 'required');
        $this->form_validation->set_rules('tstart', 'Start Contract', 'required');
        $this->form_validation->set_rules('tend', 'End Contract', 'required');
        $this->form_validation->set_rules('tnote', 'Note / Remarks', 'required');
        $this->form_validation->set_rules('tamount', 'Amount', 'required|numeric|is_natural_no_zero');
        $this->form_validation->set_rules('ctax', 'Tax', 'required|numeric');
        $this->form_validation->set_rules('ttax', 'Tax Value', 'required|numeric');
        $this->form_validation->set_rules('ttotal', 'Total', 'required|numeric|is_natural_no_zero');
        $this->form_validation->set_rules('tsalesacc', 'Sales-Acc', 'required');
        $this->form_validation->set_rules('taracc', 'AR-Acc', 'required');
        $this->form_validation->set_rules('ttaxacc', 'Tax-Acc', 'required');

        if ($this->form_validation->run($this) == TRUE)
        {
            $product = array('docno' => strtolower($this->input->post('tdocno')),
                             'dates' => $this->input->post('tdates'), 'cust_id' => $this->input->post('ccust'),
                             'starts' => $this->input->post('tstart'), 'ends' => $this->input->post('tend'),
                             'tax' => $this->input->post('ctax'), 'tax_val' => $this->input->post('ttax'),
                             'notes' => $this->input->post('tnote'), 'amount' => $this->input->post('ttotal'),
                             'sales_account' => $this->account->get_id_code($this->input->post('tsalesacc')), 
                             'ar_account' => $this->account->get_id_code($this->input->post('taracc')),
                             'tax_account' => $this->account->get_id_code($this->input->post('ttaxacc'))
                            );
            
            $this->model->update($uid, $product);
            echo 'true|'."One $this->title has successfully updated!";
        }
        else{ echo 'error|'.validation_errors(); }
        
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
        $data['reports'] = $this->model->report($this->input->post('ccustomer'), $this->input->post('cstatustype'), $this->input->post('cperiodtype'),$start,$end)->result();
        
        if ($this->input->post('ctype') == 0){ $this->load->view('contract_report', $data); }
        else { $this->load->view('contract_pivot', $data); }
    }
        
   
    // ====================================== CLOSING ======================================
    function reset_process(){ $this->model->closing(); $this->model->closing_trans(); } 
    
    
}

?>