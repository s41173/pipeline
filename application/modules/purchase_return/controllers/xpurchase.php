<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Purchase extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('Purchase_model', 'model', TRUE);
        $this->load->model('Purchase_item_model', 'transmodel', TRUE);

        $this->properti = $this->property->get();
        $this->acl->otentikasi();

        $this->modul = $this->components->get(strtolower(get_class($this)));
        $this->title = strtolower(get_class($this));

        $this->currency = new Currency_lib();
        $this->unit = new Unit_lib();
        $this->vendor = new Vendor_lib();
        $this->user = new Admin_lib();
        $this->tax = new Tax_lib();
        $this->journalgl = new Journalgl_lib();
        $this->product = new Product_lib();
        $this->ap = new Ap_payment_lib();
        $this->branch = new Branch_lib();
        $this->stock = new Stock_lib();
        $this->wt = new Warehouse_transaction_lib();
        $this->trans = new Trans_ledger_lib();
    }

    private $properti, $modul, $title, $request, $branch, $stock, $wt, $trans;
    private $vendor,$user,$tax,$journalgl,$product,$currency,$unit,$ap;
 
    function index()
    {
       $this->get_last();
    }
    
    public function getdatatable($search=null,$vendor='null',$dates='null')
    {
        if(!$search){ $result = $this->model->get_last_purchase($this->modul['limit'])->result(); }
        else{ $result = $this->model->search($vendor, $dates)->result(); }
        
        if ($result){
	foreach($result as $res)
	{
	   $output[] = array ($res->id, $res->no, strtoupper($res->currency), tglin($res->dates), ucfirst($res->acc), $this->vendor->get_vendor_name($res->vendor),
                              $res->notes, idr_format($res->total + $res->costs), idr_format($res->p2), $this->status($res->status),
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
        $data['main_view'] = 'purchase_view';
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
        $this->table->set_heading('#','No', 'Code', 'Cur', 'Date', 'Acc', 'Vendor', 'Total', 'Balance', '#', 'Action');

        $data['table'] = $this->table->generate();
        $data['source'] = site_url($this->title.'/getdatatable');
            
        // Load absen view dengan melewatkan var $data sbgai parameter
	$this->load->view('template', $data);
    }

    public function chart($cur='IDR')
    {
        $fusion = $this->load->library('fusioncharts');
        $chart  = base_url().'public/flash/Column3D.swf';
        
        $ps = new Period();
        $ps->get();
        $py = new Payment_status_lib();
        
        if ($this->input->post('ccurrency')){ $cur = $this->input->post('ccurrency'); }else { $cur = 'IDR'; }
        if ($this->input->post('tyear')){ $year = $this->input->post('tyear'); }else { $year = $ps->year; }
        
        $arpData[0][1] = 'January';
        $arpData[0][2] =  $this->model->total_chart($cur,1,$year);
//
        $arpData[1][1] = 'February';
        $arpData[1][2] =  $this->model->total_chart($cur,2,$year);
//
        $arpData[2][1] = 'March';
        $arpData[2][2] =  $this->model->total_chart($cur,3,$year);
//
        $arpData[3][1] = 'April';
        $arpData[3][2] =  $this->model->total_chart($cur,4,$year);
//
        $arpData[4][1] = 'May';
        $arpData[4][2] =  $this->model->total_chart($cur,5,$year);
//
        $arpData[5][1] = 'June';
        $arpData[5][2] =  $this->model->total_chart($cur,6,$year);
//
        $arpData[6][1] = 'July';
        $arpData[6][2] =  $this->model->total_chart($cur,7,$year);

        $arpData[7][1] = 'August';
        $arpData[7][2] = $this->model->total_chart($cur,8,$year);
        
        $arpData[8][1] = 'September';
        $arpData[8][2] = $this->model->total_chart($cur,9,$year);
//        
        $arpData[9][1] = 'October';
        $arpData[9][2] = $this->model->total_chart($cur,10,$year);
//        
        $arpData[10][1] = 'November';
        $arpData[10][2] = $this->model->total_chart($cur,11,$year);
//        
        $arpData[11][1] = 'December';
        $arpData[11][2] = $this->model->total_chart($cur,12,$year);

        $strXML1 = $fusion->setDataXML($arpData,'','') ;
        $graph   = $fusion->renderChart($chart,'',$strXML1,"Tuition", "98%", 400, false, false) ;
        return $graph;
        
    }
    

    function get_list($currency=null,$vendor=null)
    {
        $this->acl->otentikasi1($this->title);

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['form_action'] = site_url($this->title.'/get_list');
        $data['main_view'] = 'vendor_list';
        $data['currency'] = $this->currency->combo();
        $data['vendor'] = $this->vendor->combo();
        $data['link'] = array('link_back' => anchor($this->title.'/get_list','<span>back</span>', array('class' => 'back')));

        $purchases = $this->model->get_purchase_list($currency,$vendor)->result();

        $tmpl = array('table_open' => '<table id="example" width="100%" cellspacing="0" class="table table-striped table-bordered">');

        $this->table->set_template($tmpl);
        $this->table->set_empty("&nbsp;");

        //Set heading untuk table
        $this->table->set_heading('No', 'Code', 'Date', 'Acc', 'Cur', 'Notes', 'Total', 'Balance', 'Action');

        $i = 0;
        foreach ($purchases as $purchase)
        {
           $datax = array(
                            'name' => 'button',
                            'type' => 'button',
                            'class' => 'btn btn-primary',
                            'content' => 'Select',
                            'onclick' => 'setvalue(\''.$purchase->no.'\',\'titem\')'
                         );

            $this->table->add_row
            (
                ++$i, 'PO-00'.$purchase->no, tglin($purchase->dates), ucfirst($purchase->acc), strtoupper($purchase->currency), $purchase->notes, number_format($purchase->total,2), number_format($purchase->p2,2),
                form_button($datax)
            );
        }

        $data['table'] = $this->table->generate();
        $this->load->view('purchase_list', $data);
    }

    function get_list_all($currency=null,$vendor=null,$st=0)
    {
        $this->acl->otentikasi1($this->title);

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'vendor_list';
        $data['form_action'] = site_url($this->title.'/get_list_all');
        $data['link'] = array('link_back' => anchor($this->title.'/get_list_all','<span>back</span>', array('class' => 'back')));
        $data['currency'] = $this->currency->combo();

        $currency = $this->input->post('ccurrency');
        $vendor = $this->vendor->get_vendor_id($this->input->post('tvendor'));
        
        $purchases = $this->model->get_purchase_list_all($currency,$vendor,$st)->result();

        $tmpl = array('table_open' => '<table id="example" width="100%" cellspacing="0" class="table table-striped table-bordered">');

        $this->table->set_template($tmpl);
        $this->table->set_empty("&nbsp;");

        //Set heading untuk table
        $this->table->set_heading('No', 'Code', 'Date', 'Acc', 'Cur', 'Notes', 'Total', 'Balance', 'Action');

        $i = 0;
        foreach ($purchases as $purchase)
        {
           $datax = array('name' => 'button', 'type' => 'button', 'class' => 'btn btn-primary', 'content' => 'Select', 'onclick' => 'setvalue(\''.$purchase->no.'\',\'titem\')');

           $this->table->add_row
           (
              ++$i, 'PO-00'.$purchase->no, tgleng($purchase->dates), ucfirst($purchase->acc), $purchase->currency, $purchase->notes, number_format($purchase->total,2), number_format($purchase->p2,2),
              form_button($datax)
           );
        }

        $data['table'] = $this->table->generate();
        $this->load->view('purchase_list', $data);
    }

    function item_list($po)
    {
        $this->acl->otentikasi($this->title);
        $purchase = $this->model->get_purchase_by_no($po)->row();
        $items = $this->transmodel->get_last_item($po)->result();
        
        $tmpl = array('table_open' => '<table cellpadding="2" cellspacing="1" class="tablemaster">');

        $this->table->set_template($tmpl);
        $this->table->set_empty("&nbsp;");

        //Set heading untuk table
        $this->table->set_heading('No', 'Product', 'Qty', 'Unit');

        $i = 0;
        foreach ($items as $res)
        {
            $this->table->add_row
            ( ++$i, $this->product->get_name($res->product), $res->qty, $this->product->get_unit($res->product) );
        }

        $data['table'] = $this->table->generate();
        $this->load->view('purchase_item_list', $data); 
    }
    
    private function status($val=0)
    { switch ($val) { case 0: $val = 'D'; break; case 1: $val = 'S'; break; } return $val; }
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
        $purchase = $this->model->get_by_id($pid)->row();

        if ($purchase->approved == 1){ echo "warning|$this->title already approved..!"; }
        elseif ($this->valid_period($purchase->dates) == FALSE){ echo "error|$this->title Invalid Period..!"; }
        else
        {
            $total = $purchase->total;

            if ($total == 0 && $purchase->p2 == 0){  echo "error|$this->title has no value..!"; }
            else
            {
                $this->over_status($purchase->no); // over status
                                
                // add stock & warehouse transaction
                $this->add_stock($pid);
                
                // membuat kartu hutang
                if ($purchase->status == 0){ $this->trans->add($purchase->acc, 'PO', $purchase->no, $purchase->currency, $purchase->dates, 0, $purchase->p2, $purchase->vendor, 'AP'); }

                //  create journal
                $this->create_po_journal($pid, $purchase->dates, strtoupper($purchase->currency), 'PO-00'.$purchase->no.'-'.$purchase->notes, 'PJ',
                                         $purchase->no, 'AP', $purchase->total + $purchase->costs, $purchase->p1,$purchase->p2);
                
                $data = array('approved' => 1);
                $this->model->update($pid, $data);

               echo "true|$this->title PO-00$purchase->no confirmed..!";
            }
        }
        
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    private function add_stock($pid)
    {
       $purchase = $this->model->get_by_id($pid)->row();
       $list = $this->transmodel->get_last_item($pid)->result();
       
       foreach ($list as $res) {
           $this->stock->add_stock($res->product, $purchase->dates, $res->qty, $res->amount); // adding stock
           $this->wt->add($purchase->dates, 'PO-00'.$purchase->no, $this->branch->get_branch(), $purchase->currency, $res->product, $res->qty, 0,
                           $res->price, $res->price*$res->qty,
                           $this->session->userdata('log'));
       }
    }
    
    private function rollback_stock($pid)
    {
       $purchase = $this->model->get_by_id($pid)->row();
       $list = $this->transmodel->get_last_item($pid)->result();
       
       foreach ($list as $res) {
           $this->stock->increase_stock($res->product, $purchase->dates,$res->qty); // rollback stock
       }
       $this->wt->remove($purchase->dates, 'PO-00'.$purchase->no);    
    }
    
    private function cek_confirmation($po=null,$page=null)
    {
        $purchase = $this->model->get_purchase_by_no($po)->row();

        if ( $purchase->approved == 1 )
        {
           $this->session->set_flashdata('message', "Can't change value - PO-00$po approved..!"); // set flash data message dengan session
           if ($page){ redirect($this->title.'/'.$page.'/'.$po); } else { redirect($this->title); } 
        }
    }

//    ===================== approval ===========================================


    private function create_po_journal($pid,$date,$currency,$code,$codetrans,$no,$type,$amount,$p1,$p2)
    {
        $cm = new Control_model();
        
        $landed   = $cm->get_id(1); // biaya pengiriman barang
        $tax      = $cm->get_id(9); // pajak dibayar dimuka
//        $stock    = $cm->get_id(47); // piutang persediaan 
        $stock    = $this->branch->get_default_acc_branch();
        $ap       = $cm->get_id(11); // hutang usaha
        $bank     = $cm->get_id(12); // bank
        $kas      = $cm->get_id(13); // kas
        $kaskecil = $cm->get_id(14); // kas kecil
        $account = 0;
        
        $purchase = $this->model->get_by_id($pid)->row();
        switch ($purchase->acc) { case 'bank': $account = $bank; break; case 'cash': $account = $kas; break; case 'pettycash': $account = $kaskecil; break; }
        
        if ($p1 > 0)
        {  
           // create journal- GL
           $this->journalgl->new_journal('0'.$no,$date,'PJ',$currency,$code,$amount, $this->session->userdata('log'));
           $this->journalgl->new_journal('0'.$no,$date,'CD',$currency,'DP Payment : PJ-00'.$no,$p1, $this->session->userdata('log'));
           
           $jid = $this->journalgl->get_journal_id('PJ','0'.$purchase->no);
           $dpid = $this->journalgl->get_journal_id('CD','0'.$purchase->no);
           
           $this->journalgl->add_trans($jid,$stock,$purchase->total-$purchase->tax-$purchase->discount-$purchase->over_amount,0); // tambah persediaan
           $this->journalgl->add_trans($jid,$ap,0,$purchase->p1+$purchase->p2); // hutang usaha
           if ($purchase->tax > 0){ $this->journalgl->add_trans($jid,$tax,$purchase->tax,0); } // pajak pembelian
           if ($purchase->costs > 0){ $this->journalgl->add_trans($jid,$landed,$purchase->costs,0); } // landed costs
           
           //DP proses
           $this->journalgl->add_trans($dpid,$ap,$purchase->p1,0); // potongan hutang usaha
           $this->journalgl->add_trans($dpid,$account,0,$purchase->p1); // potongan bank pembelian
           
        }
        else 
        { 
           $this->journalgl->new_journal('0'.$no,$date,'PJ',$currency,$code,$amount, $this->session->userdata('log'));
           
           $jid = $this->journalgl->get_journal_id('PJ','0'.$purchase->no);
            
           $this->journalgl->add_trans($jid,$stock,$purchase->total-$purchase->tax-$purchase->discount-$purchase->over_amount,0); // tambah persediaan
           $this->journalgl->add_trans($jid,$ap,0,$purchase->p1+$purchase->p2); // hutang usaha
           if ($purchase->tax > 0){ $this->journalgl->add_trans($jid,$tax,$purchase->tax,0); } // pajak pembelian
           if ($purchase->costs > 0){ $this->journalgl->add_trans($jid,$landed,$purchase->costs,0); } // landed costs
        }
    }

    function delete($uid)
    {
        if ($this->acl->otentikasi_admin($this->title,'ajax') == TRUE){
        $val = $this->model->get_by_id($uid)->row();
        
            if ( $this->valid_period($val->dates) == TRUE && $val->stock_in_stts == 0 && $this->ap->cek_relation_trans($val->no,'no','PO') == TRUE )
            {
               if ($val->approved == 1){ $this->rollback($uid,$val->no); } else { $this->remove($uid,$val->no); }
            }
            else{ echo "error|1 $this->title can't removed, journal approved, related to another component..!"; } 

        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    private function rollback($uid,$po)
    {
      $this->journalgl->remove_journal('PJ', '0'.$po); // journal gl
      $this->journalgl->remove_journal('CD', '0'.$po);   
      $this->over_status($po, 1);
      
      // rollback stock & warehouse transaction
      $this->rollback_stock($uid);
      
      // rollback kartu hutang
      $val = $this->model->get_by_id($uid)->row();
      $this->trans->remove($val->dates, 'PO', $val->no);
      
      $trans = array('approved' => 0);
      $this->model->update($uid, $trans);
      echo "true|1 $this->title successfully rollback..!";
    }
    
    private function remove($uid,$po)
    {
       $this->transmodel->delete_po($uid); // model to delete purchase item
       $this->model->force_delete($uid); // memanggil model untuk mendelete data
       echo "true|1 $this->title successfully removed..!";
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
        $data['over'] = $this->ap->combo_over();
        
        $data['main_view'] = 'purchase_form';
        $data['source'] = site_url($this->title.'/getdatatable');
        $data['link'] = array('link_back' => anchor($this->title,'Back', array('class' => 'btn btn-danger')));
        
        $data['total'] = 0;
        $data['items'] = null;
        
        $this->load->view('template', $data);
    }

    function add_process()
    {
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){

	// Form validation
        $this->form_validation->set_rules('cvendor', 'Vendor', 'required');
        $this->form_validation->set_rules('tno', 'PO - No', 'required|numeric|callback_valid_no');
        $this->form_validation->set_rules('tdate', 'Invoice Date', 'required|callback_valid_period');
        $this->form_validation->set_rules('ccurrency', 'Currency', 'required');
        $this->form_validation->set_rules('tnote', 'Note', 'required');
        $this->form_validation->set_rules('tshipping', 'Shipping', 'required');
        $this->form_validation->set_rules('tdocno', 'Doc NO', '');

        if ($this->form_validation->run($this) == TRUE)
        {
//            if ($this->input->post('tpr')){ $this->add_request($this->input->post('tno'), $this->input->post('tpr')); }
            
            $purchase = array('vendor' => $this->input->post('cvendor'), 'no' => $this->input->post('tno'), 
                              'request' => 0, 'status' => 0, 'docno' => $this->input->post('tdocno'),
                              'dates' => $this->input->post('tdate'), 'acc' => $this->input->post('cacc'), 'currency' => $this->input->post('ccurrency'), 
                              'notes' => $this->input->post('tnote'), 'desc' => $this->input->post('tdesc'), 'shipping_date' => $this->input->post('tshipping'), 
                              'user' => $this->user->get_id($this->session->userdata('username')),
                              'log' => $this->session->userdata('log'));
            
            $this->model->add($purchase);
            
            echo "true|One $this->title data successfully saved!|".$this->model->max_id();
        }
        else{ echo "error|".validation_errors(); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }

    }
    
    private function add_request($po,$req)
    {
       $res = $this->request->get_request($req)->result();  
       
       foreach ($res as $val)
       {
           $pitem = array('product' => $val->product, 'purchase_id' => $po, 'qty' => $val->qty, 'price' => 0, 'amount' => 0, 'tax' => 0);
           $this->transmodel->add($pitem);
       }
    }

    function add_trans($pid=null)
    {
        $this->acl->otentikasi2($this->title);

        $purchase = $this->model->get_by_id($pid)->row();
        
        $data['title'] = $this->properti['name'].' | Administrator '.ucwords($this->modul['title']);
        $data['h2title'] = 'Create New '.$this->modul['title'];
	$data['form_action'] = site_url($this->title.'/update_process/'.$pid);
        $data['form_action_item'] = site_url($this->title.'/add_item/'.$pid);
        $data['currency'] = $this->currency->combo();
        $data['vendor'] = $this->vendor->combo();
        $data['unit'] = $this->unit->combo();
        $data['tax'] = $this->tax->combo();
        $data['code'] = $purchase->no;
        $data['user'] = $this->session->userdata("username");
        
        $data['main_view'] = 'purchase_form';
        $data['source'] = site_url($this->title.'/getdatatable');
        $data['link'] = array('link_back' => anchor($this->title,'Back', array('class' => 'btn btn-danger')));

        $data['over'] = $this->ap->combo_over($purchase->vendor,$purchase->currency);

        $data['default']['vendor'] = $purchase->vendor;
        $data['default']['request'] = $purchase->request;
        $data['default']['date'] = $purchase->dates;
        $data['default']['acc'] = $purchase->acc;
        $data['default']['currency'] = $purchase->currency;
        $data['default']['note'] = $purchase->notes;
        $data['default']['desc'] = $purchase->desc;
        $data['default']['shipping'] = $purchase->shipping_date;
        $data['default']['user'] = $this->user->get_username($purchase->user);
        $data['default']['docno'] = $purchase->docno;

        $data['default']['tax'] = $purchase->tax;
        $data['default']['totaltax'] = $purchase->total;
        $data['default']['p1'] = $purchase->p1;
        $data['default']['costs'] = $purchase->costs;
        $data['default']['total'] = $purchase->p2;
        
        $data['default']['over'] = $purchase->ap_over;
        $data['default']['overamount'] = $purchase->over_amount;

//        ============================ Purchase Item  =========================================
        $data['items'] = $this->transmodel->get_last_item($pid)->result();
        
        $this->load->view('template', $data);
    }

    
    function edit_item($id,$po)
    {
       $this->acl->otentikasi2($this->title); 
       $val = $this->transmodel->get_by_id($id)->row();  
       $data['form_action_item'] = site_url($this->title.'/edit_item_process/'.$id.'/'.$po); 
       
       $data['tax'] = $this->tax->combo();
       
       $data['default']['item'] = $this->product->get_name($val->product);
       $data['default']['qty'] = $val->qty;
       $data['default']['amount'] = $val->price;       
       $data['default']['tax'] = $val->tax;
        
       $this->load->view('purchase_update_item', $data); 
    }
    
    function edit_item_process($id,$po)
    {   
        $this->form_validation->set_rules('titem', 'Item Name', 'required');
        $this->form_validation->set_rules('tqty', 'Qty', 'required|numeric');
        $this->form_validation->set_rules('tamount', 'Unit Price', 'required');

        if ($this->form_validation->run($this) == TRUE && $this->valid_confirmation($po) == TRUE)
        {
           $pitem = array('product' => $this->product->get_id($this->input->post('titem')), 'purchase_id' => $po, 'qty' => $this->input->post('tqty'),
                          'price' => $this->input->post('tamount'),
                          'amount' => $this->input->post('tqty') * $this->input->post('tamount'),
                          'tax' => $this->tax->calculate($this->input->post('ctax'),$this->input->post('tqty'),$this->input->post('tamount')));
            $this->transmodel->update($id,$pitem);
            $this->update_trans($po);
        }
        
        redirect($this->title.'/edit_item/'.$id.'/'.$po);
    }
    
//    ======================  Item Transaction   ===============================================================

    function add_item($pid=null)
    {
        $po = $this->model->get_by_id($pid)->row();
        
        $this->form_validation->set_rules('titem', 'Item Name', 'required');
        $this->form_validation->set_rules('tqty', 'Qty', 'required|numeric');
        $this->form_validation->set_rules('tamount', 'Unit Price', 'required');

        if ($this->form_validation->run($this) == TRUE && $this->valid_confirmation($po->no) == TRUE && $pid != null)
        {   
            $pitem = array('product' => $this->product->get_id_by_sku($this->input->post('titem')), 'purchase_id' => $pid, 'qty' => $this->input->post('tqty'),
                           'price' => $this->input->post('tamount'),
                           'amount' => $this->input->post('tqty') * $this->input->post('tamount'),
                           'tax' => $this->tax->calculate($this->input->post('ctax'),$this->input->post('tqty'),$this->input->post('tamount')));
            $this->transmodel->add($pitem);
            $this->update_trans($pid);
            echo 'true';
        }
        elseif ( $this->valid_confirmation($po->no) != TRUE ){ echo "error|Can't change value - Journal approved..!"; }
        elseif (!$pid){ echo "error|Can't change value - Journal not created..!"; }
        else{ echo 'error|'.validation_errors(); } 
    }

    private function update_trans($pid)
    {
        $totals = $this->transmodel->total($pid);
        $purchase = array('tax' => $totals['tax'], 'total' => $totals['amount'] + $totals['tax']);
	$this->model->update($pid, $purchase);
    }

    function delete_item($id)
    {
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){ 
            
        $pid = $this->transmodel->get_by_id($id)->row();   
        $purchase = $this->model->get_by_id($pid->purchase_id)->row();
        
        if ($this->valid_confirmation($purchase->no) == TRUE){
            $this->transmodel->delete($id); 
            $this->update_trans($pid->purchase_id);
            echo 'true|Transaction removed..!';
        }
        else{ echo "warning|Journal approved, can't deleted..!"; }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
//    ==========================================================================================

    // Fungsi update untuk mengupdate db
    function update_process($pid=null)
    {
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){

	// Form validation
        $this->form_validation->set_rules('cvendor', 'Vendor', 'required');
        $this->form_validation->set_rules('tno', 'PO - No', 'required|numeric|callback_valid_confirmation');
        $this->form_validation->set_rules('tdate', 'Invoice Date', 'required|callback_valid_period');
        $this->form_validation->set_rules('ccurrency', 'Currency', 'required');
        $this->form_validation->set_rules('tnote', 'Note', 'required');
        $this->form_validation->set_rules('tshipping', 'Shipping', 'required');
        $this->form_validation->set_rules('tcosts', 'Landed Cost', 'required|numeric');
        $this->form_validation->set_rules('tp1', 'Down Payment', 'required|numeric');
        $this->form_validation->set_rules('cover', 'Credit / Debit Memo', 'required|numeric|callback_validation_over['.$this->input->post('tno').']');

        if ($this->form_validation->run($this) == TRUE)
        {
            $purchases = $this->model->get_by_id($pid)->row();

            $purchase = array('vendor' => $this->input->post('cvendor'), 'log' => $this->session->userdata('log'), 'docno' => $this->input->post('tdocno'),
                              'dates' => $this->input->post('tdate'), 'acc' => $this->input->post('cacc'), 'currency' => $this->input->post('ccurrency'),
                              'notes' => $this->input->post('tnote'), 'desc' => $this->input->post('tdesc'),
                              'shipping_date' => $this->input->post('tshipping'), 'user' => $this->user->get_id($this->input->post('tuser')),
                              'costs' => $this->input->post('tcosts'), 'p1' => $this->input->post('tp1'), 'over_amount' => $this->input->post('toveramount'), 'ap_over' => $this->input->post('cover'),
                              'p2' => $this->calculate_balance($this->input->post('tcosts'),$purchases->total,$this->input->post('tp1'),$this->input->post('toveramount')),
                              'status' => $this->get_status($this->calculate_balance($this->input->post('tcosts'),$purchases->total,$this->input->post('tp1'),$this->input->post('toveramount')))
                             );

            $this->model->update($pid, $purchase);
            echo "true|One $this->title data successfully updated!|".$pid;
        }
        else{ echo 'error|'.validation_errors(); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    private function over_status($po,$type=0)
    {
       $purchases = $this->model->get_purchase_by_no($po)->row(); 
       
       if ($purchases->ap_over != 0){ $data = array('credit_over' => 1); }
       else { $data = array('credit_over' => 0); }
       
       if ($type != 0){ $data = array('credit_over' => 0); }
       $this->ap->set_over_stts($purchases->ap_over, $data);
    }

    private function calculate_balance($cost,$total,$p1,$over)
    {
        $res=0;
        $res = $cost + $total;
        $res = $res - $p1 - $over;
        return $res;
    }

    private function get_status($p2=null)
    { if ($p2 == 0){ return 1; } else { return 0; } }

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
    
    public function valid_vendor($name)
    {
        if ($this->vendor->valid_vendor($name) == FALSE)
        {
            $this->form_validation->set_message('valid_vendor', "Invalid Vendor.!");
            return FALSE;
        }
        else{ return TRUE; }
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
    
    function validation_over($no,$po)
    {
	if ($no != 0)
        {
            $val = $this->model->get_purchase_by_no($po)->row();
            
            if ($this->model->validating_over($no,$val->id) == FALSE)
            {
               $this->form_validation->set_message('validation_over', 'This Credit / Debit Memo is already registered!');
               return FALSE;  
            }
            else { return TRUE; }
        }
        else{ return TRUE; }
    }

    public function valid_confirmation($no)
    {
        $purchase = $this->model->get_purchase_by_no($no)->row();

        if ($purchase->approved == 1)
        {
            $this->form_validation->set_message('valid_confirmation', "Can't change value - Order approved..!.!");
            return FALSE;
        }
        else {  return TRUE; }
    }

    public function valid_rate($rate)
    {
        if ($rate == 0)
        {
            $this->form_validation->set_message('valid_rate', "Rate can't 0..!");
            return FALSE;
        }
        else {  return TRUE; }
    }

// ===================================== PRINT ===========================================

   function invoice($pid=null,$type=null)
   {
       $this->acl->otentikasi2($this->title);
       $purchase = $this->model->get_by_id($pid)->row();
       $vendor = $this->vendor->get_by_id($purchase->vendor)->row();

       $data['h2title'] = 'Print Invoice'.$this->modul['title'];

       $data['pono'] = $purchase->no;
       $data['logo'] = $this->properti['logo'];
       $data['podate'] = tgleng($purchase->dates);
       $data['vendor'] = $vendor->prefix.' '.$vendor->name;
       $data['address'] = $vendor->address;
       $data['city'] = $vendor->city;
       $data['phone'] = $vendor->phone1;
       $data['phone2'] = $vendor->phone2;
       $data['desc'] = '';
       $data['user'] = $this->user->get_username($purchase->user);
       $data['currency'] = strtoupper($purchase->currency);
       $data['docno'] = $purchase->docno;
       $data['log'] = $this->session->userdata('log');

       $data['cost'] = $purchase->costs;
       $data['p2'] = $purchase->p2;
       $data['p1'] = $purchase->p1;
       $data['over'] = $purchase->over_amount;
       
       if ($purchase->ap_over > 0){ $data['ap_over'] = 'CD-00'.$purchase->ap_over.' / '.tglin($this->ap->get_dates($purchase->ap_over)); }
       else { $data['ap_over'] = ""; }
       
       // terbilang
        $amount = explode('.', $purchase->p2);
       $tt = new Terbilang();
       if ($purchase->currency == 'IDR'){  $data['terbilang'] = $tt->baca($amount[0]).' rupiah'; }
       else { $data['terbilang'] = $tt->baca($amount[0]); } 
       
       $data['items'] = $this->transmodel->get_last_item($pid)->result();

       // property display
       $data['p_name'] = $this->properti['name'];
       $data['paddress'] = $this->properti['address'];
       $data['p_phone1'] = $this->properti['phone1'];
       $data['p_phone2'] = $this->properti['phone2'];
       $data['p_city'] = ucfirst($this->properti['city']);
       $data['p_zip'] = $this->properti['zip'];
       $data['p_npwp'] = '';

       $this->load->view('purchase_invoice', $data);
   }

   
   function invoice_po($po=null,$type=null)
   {
       $this->acl->otentikasi2($this->title);
       $purchase = $this->model->get_purchase_by_no($po)->row();
       $vendor = $this->vendor->get_by_id($purchase->vendor)->row();

       $data['h2title'] = 'Print Invoice'.$this->modul['title'];

       $data['pono'] = $purchase->no;
       $data['logo'] = $this->properti['logo'];
       $data['podate'] = tgleng($purchase->dates);
       $data['vendor'] = $vendor->prefix.' '.$vendor->name;
       $data['address'] = $vendor->address;
       $data['city'] = $vendor->city;
       $data['phone'] = $vendor->phone1;
       $data['phone2'] = $vendor->phone2;
       $data['desc'] = '';
       $data['user'] = $this->user->get_username($purchase->user);
       $data['currency'] = strtoupper($purchase->currency);
       $data['docno'] = $purchase->docno;
       $data['log'] = $this->session->userdata('log');

       $data['cost'] = $purchase->costs;
       $data['p2'] = $purchase->p2;
       $data['p1'] = $purchase->p1;
       $data['over'] = $purchase->over_amount;
       
       if ($purchase->ap_over > 0){ $data['ap_over'] = 'CD-00'.$purchase->ap_over.' / '.tglin($this->ap->get_dates($purchase->ap_over)); }
       else { $data['ap_over'] = ""; }
       
       // terbilang
        $amount = explode('.', $purchase->p2);
       $tt = new Terbilang();
       if ($purchase->currency == 'IDR'){  $data['terbilang'] = $tt->baca($amount[0]).' rupiah'; }
       else { $data['terbilang'] = $tt->baca($amount[0]); } 
       
       $data['items'] = $this->transmodel->get_last_item($purchase->no)->result();

       // property display
       $data['p_name'] = $this->properti['name'];
       $data['paddress'] = $this->properti['address'];
       $data['p_phone1'] = $this->properti['phone1'];
       $data['p_phone2'] = $this->properti['phone2'];
       $data['p_city'] = ucfirst($this->properti['city']);
       $data['p_zip'] = $this->properti['zip'];
       $data['p_npwp'] = '';

       $this->load->view('purchase_invoice', $data);
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
        
        $type = $this->input->post('ctype');
        $status = $this->input->post('cstatus');
        $acc = $this->input->post('cacc');

        $data['currency'] = strtoupper($cur);
        $data['start'] = $start;
        $data['end'] = $end;
        $data['acc'] = $acc;
        $data['rundate'] = tglin(date('Y-m-d'));
        $data['log'] = $this->session->userdata('log');

//        Property Details
        $data['company'] = $this->properti['name'];

        $data['purchases'] = $this->model->report($vendor,$cur,$start,$end,$status,$acc)->result();
        $total = $this->model->total($vendor,$cur,$start,$end,$status,$acc);
        
        $data['total'] = $total['total'] - $total['tax'];
        $data['tax'] = $total['tax'];
        $data['p1'] = $total['p1'];
        $data['p2'] = $total['p2'];
        $data['costs'] = $total['costs'];
        $data['ptotal'] = $total['total'] + $total['costs'];
        
        if ($type == '1'){ $page = "purchase_report_details"; }elseif ($type == '0'){ $page = "purchase_report"; }elseif ($type == '2'){ $page = "purchase_pivot"; }
        if ($this->input->post('cformat') == 0){  $this->load->view($page, $data); }
        elseif ($this->input->post('cformat') == 1)
        {
            $pdf = new Pdf();
            $pdf->create($this->load->view($page, $data, TRUE));
        }
    }
    
    
    function report_product_process()
    {
        $this->acl->otentikasi2($this->title);
        $data['title'] = $this->properti['name'].' | Report '.ucwords($this->modul['title']);

        $product = $this->product->get_id_by_sku($this->input->post('titem'));
        $cur = $this->input->post('ccurrency');
        
        $period = $this->input->post('reservation');  
        $start = picker_between_split($period, 0);
        $end = picker_between_split($period, 1);

        $data['currency'] = strtoupper($cur);
        $data['start'] = $start;
        $data['end'] = $end;
        $data['rundate'] = tgleng(date('Y-m-d'));
        $data['log'] = $this->session->userdata('log');

//        Property Details
        $data['company'] = $this->properti['name'];
        $data['purchases'] = $this->model->report_product($product,$cur,$start,$end)->result();
        
        $page = "purchase_product_report";
        $this->load->view($page, $data);
        
//        if ($type == '0'){ $page = "purchase_product_report"; }elseif ($type == '1'){ $page = "purchase_product_pivot"; }
//        if ($this->input->post('cformat') == 0){  $this->load->view($page, $data); }
//        elseif ($this->input->post('cformat') == 1)
//        {
//            $pdf = new Pdf();
//            $pdf->create($this->load->view($page, $data, TRUE));
//        }
    }
    
// ====================================== REPORT =========================================
    
// ====================================== AJAX =========================================    
   
    function get_over()
    {
       $no = $this->input->post('tno'); 
       if (!$no){ echo '0'; }else { echo $this->ap->get_over_payment($no); }
    }

}

?>