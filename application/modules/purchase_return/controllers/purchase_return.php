<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Purchase_return extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('Purchase_return_model', 'model', TRUE);
        $this->load->model('Purchase_return_item_model', 'transmodel', TRUE);

        $this->properti = $this->property->get();
        $this->acl->otentikasi();

        $this->modul = $this->components->get(strtolower(get_class($this)));
        $this->title = strtolower(get_class($this));

        $this->unit = $this->load->library('unit_lib');
        $this->currency = $this->load->library('currency_lib');
        
        $this->vendor = new Vendor_lib();
        $this->user = new Admin_lib(); 
        $this->tax = new Tax_lib(); 
        $this->journalgl = new Journalgl_lib();
        $this->product = new Product_lib();
        $this->purchase = new Purchase_lib();
        $this->pitem = new Purchase_item_lib();
        $this->wt = new Warehouse_transaction_lib();
        $this->ap = new Ap_payment_lib();
        $this->stock = new Stock_lib();
        $this->branch = new Branch_lib();
    }

    private $properti, $modul, $title, $stock, $branch;
    private $vendor,$user,$tax,$journal,$product,$purchase,$pitem,$wt,$unit,$currency,$journalgl,$ap;

    function index()
    {
        $this->get_last();
    }
    
    public function getdatatable($search=null,$vendor='null',$dates='null')
    {
        if(!$search){ $result = $this->model->get_last($this->modul['limit'])->result(); }
        else{ $result = $this->model->search($vendor, $dates)->result(); }
        
        if ($result){
	foreach($result as $res)
	{   
	   $output[] = array ($res->id, $res->no, $res->purchase, tglin($res->dates), strtoupper($res->currency), ucfirst($res->acc), $res->docno, $this->vendor->get_vendor_name($res->vendor),
                              $res->user, $res->log, $this->status($res->status), idr_format($res->tax), idr_format($res->costs), idr_format($res->total + $res->costs), idr_format($res->balance),  $res->notes, $res->cash,
                              $res->approved);
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
        $data['main_view'] = 'purchase_return_view';
	$data['form_action'] = site_url($this->title.'/add_process');
        $data['form_action_update'] = site_url($this->title.'/update_process');
        $data['form_action_del'] = site_url($this->title.'/delete_all');
        $data['form_action_report'] = site_url($this->title.'/report_process');
        $data['form_action_product'] = site_url($this->title.'/report_product_process');
        $data['link'] = array('link_back' => anchor('main/','Back', array('class' => 'btn btn-danger')));
        
        $data['currency'] = $this->currency->combo();
        $data['vendor'] = $this->vendor->combo();
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
        $this->table->set_heading('#','No', 'Code', 'Purchase', 'Date', 'Acc', 'Vendor', 'Total', 'Balance', 'Action');

        $data['table'] = $this->table->generate();
        $data['source'] = site_url($this->title.'/getdatatable');
            
        // Load absen view dengan melewatkan var $data sbgai parameter
	$this->load->view('template', $data);
    }

    private function status($val=null)
    { switch ($val) { case 0: $val = 'C'; break; case 1: $val = 'S'; break; } return $val; }
    
//    ===================== approval ===========================================

    function confirmation($pid)
    {
        $purchase_return = $this->model->get_by_id($pid)->row();

        if ($purchase_return->approved == 1){ echo "warning|$this->title already approved..!"; }
        elseif ($this->valid_period($purchase_return->dates) == FALSE){ echo "error|$this->title Invalid Period..!";  }
        else
        {
          //  $this->cek_journal($purchase_return->dates,$purchase_return->currency);
            $total = $purchase_return->total;

            if ($total == 0){ echo "error|$this->title has no value..!"; }
            else
            {
                $data = array('approved' => 1);
                $this->model->update($pid, $data);

                // journal gl --------------------------------------------------------------------
                 $cm = new Control_model();
        
                 $landed   = $cm->get_id(1); // biaya pengiriman barang
                 $tax      = $cm->get_id(9); // pajak di bayar dimuka
                 $stock    = $cm->get_id(10); // persediaan
                 $ap       = $cm->get_id(11); // hutang usaha
                 $bank     = $cm->get_id(12); // bank
                 $kas      = $cm->get_id(13); // kas
                 $kaskecil = $cm->get_id(14); // kas kecil
                 $account = 0;
                 
                 switch ($purchase_return->acc) { case 'bank': $account = $bank; break; case 'cash': $account = $kas; break; case 'pettycash': $account = $kaskecil; break; }
                 
                 $this->journalgl->new_journal($purchase_return->no,$purchase_return->dates,'PR', strtoupper($purchase_return->currency),
                                               'PR-0'.$purchase_return->no.'-'.$purchase_return->notes, $purchase_return->balance, 
                                               $this->session->userdata('log'));
                 
                 $jid = $this->journalgl->get_journal_id('PR',$purchase_return->no);
                 
                 $this->journalgl->add_trans($jid,$account,$purchase_return->total,0); // bank - D
                 $this->journalgl->add_trans($jid,$stock,0,$purchase_return->total-$purchase_return->tax); // kurang persediaan - K
                 if ($purchase_return->tax > 0){ $this->journalgl->add_trans($jid,$tax,0,$purchase_return->tax); } // pajak pembelian
                 if ($purchase_return->costs > 0)
                 { 
                   $this->journalgl->add_trans($jid,$landed,$purchase_return->costs,0);  // biaya cost
                   $this->journalgl->add_trans($jid,$account,0,$purchase_return->costs);  // bank - K
                 }
                 
                 // journal gl --------------------------------------------------------------------

                // min stock
                $this->min_stock($pid);
                 
                 
                // create warehouse transaction
//                $this->add_warehouse_transaction($purchase_return->no);

                echo "true|$this->title PR-00$purchase_return->no confirmed..!";
            }
        }

    }
    
    private function min_stock($pid)
    {
        $purchase = $this->model->get_by_id($pid)->row();
        $trans = $this->transmodel->get_last_item($pid)->result();
        foreach ($trans as $res) {
            $this->stock->increase_stock($res->product, $purchase->dates, $res->qty);            
            $this->wt->add($purchase->dates, 'PR-'.$purchase->no, $this->branch->get_branch(), $purchase->currency, $res->product, 0, $res->qty, $res->price, $res->qty*$res->price);
        }
    }
    
    private function rollback_stock($pid)
    {
        $purchase = $this->model->get_by_id($pid)->row();
        $trans = $this->transmodel->get_last_item($pid)->result();
        foreach ($trans as $res) {       
            $this->stock->add_stock($res->product, $purchase->dates, $res->qty, $res->price);
            $this->wt->remove($purchase->dates, 'PR-'.$purchase->no);
        }
    }

    function valid_stock($product,$dates)
    {
        $qty = $this->input->post('treturn');
        if ($this->stock->valid_stock($product, $dates, $qty) == FALSE){
           $this->form_validation->set_message('valid_stock', "Invalid Stock..!"); 
           return FALSE; 
        }else{ return TRUE; }
    }

    function valid_confirmation($pid)
    {
        $purchase_return = $this->model->get_by_id($pid)->row();

        if ( $purchase_return->approved == 1 )
        {
           $this->form_validation->set_message('valid_confirmation', "Journal already approved..!"); 
           return FALSE;
        }else { return TRUE; }
    }
//    ===================== approval ===========================================


    function delete($uid)
    {
        if ($this->acl->otentikasi_admin($this->title,'ajax') == TRUE){
        $pr = $this->model->get_by_id($uid)->row();
        
        if ( $this->valid_period($pr->dates) == TRUE && $this->ap->cek_relation_trans($pr->purchase,'no','PR') == TRUE )
        {
           if ($pr->approved == 1){ $this->rollback($uid, $pr->no);  }else { $this->remove($uid, $pr->no);  }
           $this->journalgl->remove_journal('PR', $pr->no); // journal gl
        }
        else{ echo "error|1 $this->title can't removed, journal approved, related to another component..!"; } 
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    private function rollback($uid,$po)
    {
       $this->rollback_stock($uid);
       $trans = array('approved' => 0);
       $this->model->update($uid, $trans);
       echo "true|1 $this->title rollback";
    }
    
    private function remove($uid,$po)
    {
       $this->transmodel->delete_po($uid); // model to delete purchase_return item
       $this->model->force_delete($uid); 
       echo "true|1 $this->title removed";
    }
    
    function add()
    {
        $this->acl->otentikasi2($this->title);

        $data['title'] = $this->properti['name'].' | Administrator '.ucwords($this->modul['title']);
        $data['h2title'] = 'Create New '.$this->modul['title'];
	$data['form_action'] = site_url($this->title.'/add_process');
        $data['form_action_item'] = site_url($this->title.'/add_item/');
        
        $data['currency'] = $this->currency->combo();
        $data['code'] = $this->model->counter();
        $data['user'] = $this->session->userdata("username");
        $data['vendor'] = $this->vendor->combo();
        $data['tax'] = $this->tax->combo();
        $data['venid'] = null;
        $data['default']['currency'] = null;
        
        $data['main_view'] = 'purchase_return_form';
        $data['source'] = site_url($this->title.'/getdatatable');
        $data['link'] = array('link_back' => anchor($this->title,'Back', array('class' => 'btn btn-danger')));
        
        $data['total'] = 0;
        $data['items'] = null;
        $data['purchase_item'] = null;
        
        $this->load->view('template', $data);
    }

    function add_process()
    {
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'purchase_return_form';
	$data['form_action'] = site_url($this->title.'/add_process');
	
        $data['code'] = $this->model->counter();
        $data['user'] = $this->session->userdata("username");
        $data['currency'] = $this->currency->combo();

	// Form validation
        $this->form_validation->set_rules('tpo', 'PO', 'required|callback_valid_po');
        $this->form_validation->set_rules('tno', 'PR - No', 'required|numeric|callback_valid_no');
        $this->form_validation->set_rules('tdate', 'Invoice Date', 'required|callback_valid_period');
        $this->form_validation->set_rules('tnote', 'Note', 'required');
        $this->form_validation->set_rules('tdocno', 'Doc NO', '');

        if ($this->form_validation->run($this) == TRUE)
        {
            $vendor = $this->purchase->get_po($this->input->post('tpo'));
            $purchase_return = array('vendor' => $vendor->vendor, 'currency' => $vendor->currency, 'purchase' => $this->input->post('tpo'), 
                                     'no' => $this->input->post('tno'), 'status' => 0, 'docno' => $this->input->post('tdocno'),
                                     'dates' => $this->input->post('tdate'), 'acc' => $this->input->post('cacc'), 'notes' => $this->input->post('tnote'),
                                     'user' => $this->user->get_id($this->session->userdata('username')), 'log' => $this->session->userdata('log'));
            
            $this->model->add($purchase_return);
            echo "true|One $this->title data successfully saved!|".$this->model->max_id();
        }
        else{ echo "error|".validation_errors(); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }

    function add_trans($pid=null)
    {
        $this->acl->otentikasi2($this->title);
        $this->model->valid_add_trans($pid, $this->title);
        
        $purchase_return = $this->model->get_by_id($pid)->row();
        
        $data['title'] = $this->properti['name'].' | Administrator '.ucwords($this->modul['title']);
        $data['h2title'] = 'Create New '.$this->modul['title'];
	$data['form_action'] = site_url($this->title.'/update_process/'.$pid);
        $data['form_action_item'] = site_url($this->title.'/add_item/'.$pid);
        $data['code'] = $purchase_return->no;
        $data['user'] = $this->session->userdata("username");
        
        $data['main_view'] = 'purchase_return_transform';
        $data['source'] = site_url($this->title.'/getdatatable');
        $data['link'] = array('link_back' => anchor($this->title,'Back', array('class' => 'btn btn-danger')));

        $purchase = $this->purchase->get_po($purchase_return->purchase);
        $data['product'] = $this->pitem->combo($purchase->id);
        
        $data['pid'] = $purchase_return->id;
        $data['venid'] = $purchase_return->vendor;
        $data['default']['po'] = $purchase_return->purchase;
        $data['default']['date'] = $purchase_return->dates;
        $data['default']['acc'] = $purchase_return->acc;
        $data['default']['note'] = $purchase_return->notes;
        $data['default']['user'] = $this->user->get_username($purchase_return->user);
        $data['default']['docno'] = $purchase_return->docno;
        $data['default']['currency'] = $purchase_return->currency;

        $data['default']['tax']      = $purchase_return->tax;
        $data['default']['totaltax'] = $purchase_return->total;
        $data['default']['balance']  = $purchase_return->balance;
        $data['default']['costs']    = $purchase_return->costs;

//        ============================ Purchase Item  ===============================================
        $data['purchase_item'] = $this->pitem->get_last_item($purchase->id)->result();

//        ============================ Purchase_return Item  =========================================
        $data['items'] = $this->transmodel->get_last_item($pid)->result();
        
        $this->load->view('template', $data);
    }


//    ======================  Item Transaction   ===============================================================

    function add_item($pid=null)
    {
        $pr = $this->model->get_by_id($pid)->row();
        $purchase = $this->purchase->get_po($pr->purchase);
        $purchase_item = $this->pitem->get_product_item($purchase->id, $this->input->post('cproduct'));
        
        $this->form_validation->set_rules('cproduct', 'Product', 'required|callback_valid_item['.$pid.']|callback_valid_stock['.$purchase->dates.']');
        $this->form_validation->set_rules('treturn', 'Return', 'required|numeric|callback_valid_qty['.$pr->purchase.']');

        if ($this->form_validation->run($this) == TRUE && $this->valid_confirmation($pid) == TRUE)
        {
            $harga = $this->pitem->get_product_item($purchase->id, $this->input->post('cproduct'));
            $price = $harga->price;
            $tax = intval($harga->tax/$harga->qty*$this->input->post('treturn'));
            
            $pitem = array('product' => $this->input->post('cproduct'), 'purchase_return_id' => $pid, 'qty' => $this->input->post('treturn'),
                           'unit' => $this->product->get_unit($this->input->post('cproduct')),
                           'price' => $price, 'amount' => $this->input->post('treturn') * $price + $tax,
                           'tax' => $tax);
            $this->transmodel->add($pitem);
            $this->update_trans($pid);

            echo 'true';
        }
        elseif ($this->valid_confirmation($pid) == FALSE){ echo "error|Journal already approved..!!"; }
        else{ echo "error|".validation_errors(); }
    }

    private function update_trans($pid)
    {
        $totals = $this->transmodel->total($pid);
        $purchase_return = array('tax' => $totals['tax'], 'total' => $totals['amount']);
	$this->model->update($pid, $purchase_return);
    }

    function delete_item($id)
    {
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){ 
         
        $pid = $this->transmodel->get_by_id($id)->row();    
        if ($this->valid_confirmation($pid->purchase_return_id) == TRUE){

            $this->transmodel->delete($id); // memanggil model untuk mendelete data
            $this->update_trans($pid->purchase_return_id);
            echo 'true|Transaction removed..!';
        
        }else{ echo "warning|Journal approved, can't deleted..!"; }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
//    ==========================================================================================

    // Fungsi update untuk mengupdate db
    function update_process($pid=null)
    {
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){

	// Form validation
        $this->form_validation->set_rules('tpid', 'ID', 'required|callback_valid_confirmation');    
        $this->form_validation->set_rules('tpo', 'PO', 'required');
        $this->form_validation->set_rules('tno', 'PR - No', 'required|numeric');
        $this->form_validation->set_rules('tdate', 'Invoice Date', 'required|callback_valid_period');
        $this->form_validation->set_rules('tnote', 'Note', 'required');
        $this->form_validation->set_rules('tcosts', 'Landed Cost', 'numeric');

        if ($this->form_validation->run($this) == TRUE)
        {
            $purchase_returns = $this->model->get_by_id($pid)->row();

            $purchase_return = array('log' => $this->session->userdata('log'), 'docno' => $this->input->post('tdocno'),
                                     'dates' => $this->input->post('tdate'), 'acc' => $this->input->post('cacc'), 'notes' => $this->input->post('tnote'),
                                     'user' => $this->user->get_id($this->session->userdata('username')), 'costs' => $this->input->post('tcosts'),
                                     'balance' => floatval($this->input->post('tcosts')+$purchase_returns->total)
                             );

            $this->model->update($pid, $purchase_return);
            echo "true|One $this->title data successfully updated!|".$pid;
        }
        else{ echo 'error|'.validation_errors(); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
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

    public function valid_qty($return,$purchase)
    {
        $product = $this->input->post('cproduct');
        
        $purchase_id = $this->purchase->get_po($purchase);
        $qty = $this->pitem->get_product_item($purchase_id->id,$product);
        $qty = $qty->qty;
        
        if ($return > $qty)
        {
            $this->form_validation->set_message('valid_qty', "Invalid Return Qty.!");
            return FALSE;
        }
        else{ return TRUE; }
    }

    public function valid_po($no)
    {
        if ($this->model->valid('purchase',$no) == FALSE)
        {
            $this->form_validation->set_message('valid_po', "Purchase No already registered.!");
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

    public function valid_item($product,$pid)
    {
        if ($this->transmodel->valid_item($product,$pid) == FALSE)
        {
            $this->form_validation->set_message('valid_item', "Product already listed.!");
            return FALSE;
        }
        else {  return TRUE; }
    }

// ===================================== PRINT ===========================================

   function invoice($pid=null)
   {
       $this->acl->otentikasi2($this->title);

       $data['h2title'] = 'Print Invoice'.$this->modul['title'];

       $purchase_return = $this->model->get_by_id($pid)->row();
       $vendor = $this->vendor->get_detail($purchase_return->vendor);

       $data['pono'] = $purchase_return->no;
       $data['logo'] = $this->properti['logo'];
       $data['podate'] = tglin($purchase_return->dates);
       $data['vendor'] = $this->vendor->get_vendor_name($purchase_return->vendor);
       $data['address'] = $vendor->address;
       $data['city'] = $vendor->city;
       $data['phone'] = $vendor->phone1;
       $data['phone2'] = $vendor->phone2;
       $data['user'] = $this->user->get_username($purchase_return->user);
       $data['currency'] = $purchase_return->currency;
       $data['docno'] = $purchase_return->docno;

       $data['cost'] = $purchase_return->costs;
       $data['balance'] = $purchase_return->balance;

       $data['items'] = $this->transmodel->get_last_item($pid)->result();

       // property display
       $data['paddress'] = $this->properti['address'];
       $data['p_phone1'] = $this->properti['phone1'];
       $data['p_phone2'] = $this->properti['phone2'];
       $data['p_city'] = ucfirst($this->properti['city']);
       $data['p_zip'] = $this->properti['zip'];
       $data['p_npwp'] = '';
       $data['p_sitename'] = $this->properti['sitename'];
       $data['p_email'] = $this->properti['email'];

       $this->load->view('purchase_return_invoice', $data);
   }

// ===================================== PRINT ===========================================

// ====================================== REPORT =========================================

    function report_process()
    {
        $this->acl->otentikasi2($this->title);
        $data['title'] = $this->properti['name'].' | Report '.ucwords($this->modul['title']);

        $vendor = $this->input->post('cvendor');
        $cur = $this->input->post('ccurrency');
        
        $period = $this->input->post('reservation');  
        $start = picker_between_split($period, 0);
        $end = picker_between_split($period, 1);
        
        $acc = $this->input->post('cacc');

        $data['currency'] = strtoupper($cur);
        $data['start'] = $start;
        $data['end'] = $end;
        $data['rundate'] = tglin(date('Y-m-d'));
        $data['log'] = $this->session->userdata('log');

//        Property Details
        $data['company'] = $this->properti['name'];

        $data['purchase_returns'] = $this->model->report($cur,$vendor,$start,$end,$acc)->result();
        $total = $this->model->total($cur,$vendor,$start,$end,$acc);
        
        $data['total'] = $total['total'] - $total['tax'];
        $data['tax'] = $total['tax'];
        $data['costs'] = $total['costs'];
        $data['balance'] = $total['total'] + $total['costs'];

        $this->load->view('purchase_return_report_details', $data);
        
    }

// ====================================== REPORT =========================================

   // ====================================== CLOSING ======================================
    function reset_process(){ $this->model->closing(); $this->transmodel->closing(); } 
    
}

?>