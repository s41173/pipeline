<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Ap_payment extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('Appayment_model', 'model', TRUE);
        $this->load->model('Payment_trans_model', 'transmodel', TRUE);

        $this->properti = $this->property->get();
        $this->acl->otentikasi();

        $this->modul = $this->components->get(strtolower(get_class($this)));
        $this->title = strtolower(get_class($this));

        $this->currency = $this->load->library('currency_lib');
        $this->load->library('bank_lib');
        $this->vendor = new Vendor_lib();
        $this->user = new Admin_lib();
        $this->journalgl = new Journalgl_lib();
        $this->cek = new Checkout_lib();
        $this->purchase = new Purchase_lib();
        $this->purchase_return = new Purchase_return_lib();
        $this->account = new Account_lib();
        $this->ledger = new Cash_ledger_lib();
        $this->trans = new Trans_ledger_lib();
        $this->bank = new Bank_lib();
        $this->period = new Period_lib();
        $this->period = $this->period->get();
    }

    private $properti, $modul, $title, $printing, $trans, $bank, $period;
    private $vendor,$user,$journal,$cek,$purchase,$purchase_return, $currency, $account,$ledger;

    public $atts = array('width'=> '800','height'=> '600',
                      'scrollbars' => 'yes','status'=> 'yes',
                      'resizable'=> 'yes','screenx'=> '0','screenx' => '\'+((parseInt(screen.width) - 800)/2)+\'',
                      'screeny'=> '0','class'=> 'print','title'=> 'print', 'screeny' => '\'+((parseInt(screen.height) - 600)/2)+\'');

    function index(){ $this->get_last(); }

    public function getdatatable($search=null,$vendor='null',$dates='null')
    {
        if(!$search){ $result = $this->model->get_last($this->modul['limit'])->result(); }
        else{ $result = $this->model->search($vendor, $dates)->result(); }
        
        if ($result){
	foreach($result as $res)
	{   
	   $output[] = array ($res->id, $res->no, tglin($res->dates),
                              $this->vendor->get_vendor_name($res->vendor), $this->acc($res->acc).' - '.strtoupper($res->currency), $res->check_no, 
                              idr_format($res->amount+$res->over+$res->late), $res->approved);
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
        $data['main_view'] = 'ap_view';
	$data['form_action'] = site_url($this->title.'/add_process');
        $data['form_action_update'] = site_url($this->title.'/update_process');
        $data['form_action_del'] = site_url($this->title.'/delete_all');
        $data['form_action_report'] = site_url($this->title.'/report_process');
        $data['form_action_card'] = site_url($this->title.'/payable_process');
        $data['link'] = array('link_back' => anchor('main/','Back', array('class' => 'btn btn-danger')));
        
        $data['currency'] = $this->currency->combo();
        $data['vendor'] = $this->vendor->combo();
        $data['bank'] = $this->account->combo_asset();
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
        $this->table->set_heading('#','No', 'Code', 'Date', 'Vendor', 'ACC', 'Check No', 'Total', 'Action');

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
    

    private function acc($val=null)
    { switch ($val) { case 'bank': $val = 'Bank'; break; case 'cash': $val = 'Cash'; break; case 'pettycash': $val = 'Petty Cash'; break; } return $val; }
//    ===================== approval ===========================================

    function confirmation($pid)
    {
        $appayment = $this->model->get_by_id($pid)->row();
        $code = 'PO'; 

        if ($appayment->approved == 1)
        {
           echo "warning|$this->title already approved..!";
        }
        else
        {
            $total = $appayment->amount;
//
            if ($total <= 0){  echo "error|CD-00$appayment->no has no value..!"; }
            elseif ($this->cek_po_settled($pid,$code) == FALSE || $this->cek_pr_settled($pid) == FALSE)
            { echo "warning|Transaction $appayment->no has been settled..!"; }
            elseif ($this->valid_check_no($appayment->no,$pid) == FALSE ){ echo "warning|CD-00$appayment->no check no registered..!"; }
            else
            {
                $this->settled_po($pid,$code); // fungsi untuk mensettled kan semua po
                
                // membuat kartu hutang
                $this->trans->add($appayment->acc, 'CD', $appayment->no, $appayment->currency, $appayment->dates, intval($appayment->amount+$appayment->discount-$appayment->late), 0, $appayment->vendor, 'AP'); 
               
               $data = array('approved' => 1);
               $this->model->update($pid, $data);
                
               $cm = new Control_model();
               
               if ($appayment->post_dated == 1){ $account = $cm->get_id(35); }else { $account = $appayment->account; } // bank atau giro
               
               $ap   = $cm->get_id(11);
               $cost = $cm->get_id(5); // biaya denda
               $discount = $cm->get_id(3); // potongan pembelian
               
               $this->journalgl->new_journal('0'.$appayment->no,$appayment->dates,'CD', strtoupper($appayment->currency), 'Payment for : '.$this->get_trans_code($appayment->no).' - '. $this->vendor->get_vendor_name($appayment->vendor).' - '.$this->acc($appayment->acc), $appayment->amount, $this->session->userdata('log'));
               $dpid = $this->journalgl->get_journal_id('CD','0'.$appayment->no);
               
               // cash ledger
               $this->ledger->add($appayment->acc, "CD-00".$appayment->no, $appayment->currency, $appayment->dates, 0, $appayment->amount);
               
               if ($appayment->late > 0){ $this->journalgl->add_trans($dpid,$cost,$appayment->late,0); } // denda keterlambatan
               $this->journalgl->add_trans($dpid,$ap,intval($appayment->amount-$appayment->late),0); // hutang usaha
               $this->journalgl->add_trans($dpid,$account,0,$appayment->amount); // kas, bank, kas kecil
               
               if ($appayment->discount > 0)
               {
                  $this->journalgl->new_journal('0'.$appayment->no,$appayment->dates,'PD', strtoupper($appayment->currency), 'Purchase Discount : '.$this->get_trans_code($appayment->no).' - '. $this->vendor->get_vendor_name($appayment->vendor).' - '.$this->acc($appayment->acc), $appayment->amount, $this->session->userdata('log'));
                  $pdid = $this->journalgl->get_journal_id('PD','0'.$appayment->no);
                  
                  $this->journalgl->add_trans($pdid,$ap,$appayment->discount,0); // hutang usaha
                  $this->journalgl->add_trans($pdid,$discount,0,$appayment->discount); // potongan pembelian
               }
               echo "true|$this->title CD-00$appayment->no confirmed..!";
            }
        }

    }

    private function get_trans_code($po)
    {
        $val = $this->transmodel->get_last_item($po)->result();
        $ress=null;

        foreach ($val as $res)
        { $ress = $ress.$res->code.'-00'.$res->no.','; }

        return $ress;
    }

    private function valid_check_no($no=null,$pid=null)
    {
        $val = $this->model->get_by_no($no)->row();
        if ($val->check_no != null)
        {
            if ($this->model->cek_no($val->check_no,$pid) == FALSE)
            { return FALSE; } else { return TRUE; }
        }
        else { return TRUE; }
    }

    private function settled_po($no,$code='PO')
    {
        $vals = $this->transmodel->get_last_trans($no,$code)->result();
        
        if ($code == 'PO')
        {
            foreach ($vals as $val)
            {
                $p2 = $this->purchase->get_po($val->no);
                $p2 = $p2->p2;
             
                if ($val->amount - $p2 >= 0)
                {
                   $data = array('status' => 1,'p2' => $p2-$val->amount);
                   $this->purchase->settled_po($val->no,$data);
                }
                else
                {
                    $datax = array('p2' => $p2-$val->amount);
                    $this->purchase->settled_po($val->no,$datax);
                }
            }
        }
        
    }

    private function settled_pr($no)
    {
        $vals = $this->transmodel->get_last_trans($no,'PR')->result();
        $data = array('status' => 1);

        foreach ($vals as $val)
        {  $this->purchase_return->settled_pr($val->no,$data); }
    }

    function unsettled_po($no,$code)
    {
        $vals = $this->transmodel->get_last_trans($no,$code)->result();
        
        if ($code == 'PO')
        {
            foreach ($vals as $val)
            {
                $p2 = $this->purchase->get_po($val->no);
                $p2 = $p2->p2;
                $data = array('status' => 0, 'p2'=> $val->amount+$p2);
                $this->purchase->settled_po($val->no,$data);
            }
        } 
    }

    private function unsettled_pr($no)
    {
        $vals = $this->transmodel->get_last_trans($no,'PR')->result();
        $data = array('status' => 0);

        foreach ($vals as $val)
        {  $this->purchase_return->settled_pr($val->no,$data); }
    }

    private function cek_po_settled($id,$code='PO')
    {
        $vals = $this->transmodel->get_last_trans($id,$code)->result();
        $res = FALSE;
        
        if ($code == 'PO')
        {
            foreach ($vals as $val)
            { if ($this->purchase->cek_settled($val->no) == FALSE){ $res = FALSE; break; } else { $res = TRUE; } }
        }

        return $res;
    }

    private function cek_pr_settled($no)
    {
        $vals = $this->transmodel->get_last_trans($no,'PR')->result();
        $num  = $this->transmodel->get_last_trans($no,'PR')->num_rows();
        $res = TRUE;

        if ($num > 0)
        {
           foreach ($vals as $val)
           {
              if ($this->purchase_return->cek_settled($val->no) == FALSE)
              {
                  $res = FALSE;
                  break;
              }
              else { $res = TRUE; }
           }
        }

        return $res;
    }

    private function cek_confirmation($po=null,$page=null)
    {
        $appayment = $this->model->get_ap_payment_by_no($po)->row();

        if ( $appayment->approved == 1 )
        {
           $this->session->set_flashdata('message', "Can't change value - CD-00$po approved..!"); // set flash data message dengan session
           if ($page){ redirect($this->title.'/'.$page.'/'.$po); } else { redirect($this->title); }
        }
    }


//    ===================== approval ===========================================

    function delete($uid)
    {
        if ($this->acl->otentikasi_admin($this->title,'ajax') == TRUE){
        $appayment = $this->model->get_by_id($uid)->row();
        $po = $appayment->no;
        $code = 'PO';
        
        if ( $this->valid_period($appayment->dates) == TRUE && $this->valid_credit_over($appayment->no) == TRUE ) // cek journal harian sudah di approve atau belum
        {
            if ($appayment->approved == 1){ $this->rollback($uid, $po,$code); echo "true|1 $this->title successfully rollback..!"; }
            else {  $this->remove($uid, $po, $code); echo "true|1 $this->title successfully removed..!"; }
        }
        elseif ($this->valid_period($appayment->dates) != TRUE){ echo "error| Invalid period..!"; }
        elseif ($this->valid_credit_over($appayment->no) != TRUE){ echo "error|1 $this->title can't removed, journal approved, related to another component..!"; } 
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    private function rollback($uid,$po,$code)
    {
       $this->unsettled_po($uid,$code);
//       $this->unsettled_pr($po);
       $this->journalgl->remove_journal('CD', '0'.$po);
       $this->journalgl->remove_journal('PD', '0'.$po);
       
       // rollback kartu hutang 
      $appayment = $this->model->get_by_id($uid)->row();
      $this->trans->remove($appayment->dates, 'CD', $appayment->no);
      $this->ledger->remove($appayment->dates, "CD-00".$appayment->no); // remove cash ledger
       
       $data = array('approved' => 0);
       $this->model->update($uid, $data); 
    }
    
    private function remove($uid,$po,$code='PO')
    {
       // remove cash ledger
       $val = $this->model->get_by_id($uid)->row();
       $this->ledger->remove($val->dates, "CD-00".$val->no); 
        
       $this->transmodel->delete_payment($uid); // model to delete appayment item
       $this->model->force_delete($uid); // memanggil model untuk mendelete data 
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
        $data['bank'] = $this->account->combo_asset();
        $data['default']['rate'] = 1;
        $data['pid'] = '';
        $data['venid'] = '';
        $data['default']['currency'] = '';
        
        $data['main_view'] = 'ap_form';
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
//        $this->form_validation->set_rules('tcheck', 'Check No', 'callback_valid_check|callback_valid_check_no');
        $this->form_validation->set_rules('tdate', 'Date', 'required|callback_valid_period');
        $this->form_validation->set_rules('ccurrency', 'Currency', 'required');
        $this->form_validation->set_rules('trate', 'Rate', 'required|numeric|callback_valid_rate');

        if ($this->form_validation->run($this) == TRUE)
        {
            $check = $this->input->post('tcheckaccno').'|'.$this->input->post('tcheckaccname').'|'.$this->input->post('tccbank');
            $appayment = array('vendor' => $this->input->post('cvendor'), 'docno' => $this->input->post('tdocno'),
                               'no' => $this->model->counter(), 'check_no' => null, 'dates' => $this->input->post('tdate'), 
                               'currency' => $this->input->post('ccurrency'), 'acc' => $this->input->post('cacc'), 'rate' => $this->input->post('trate'),
                               'amount' => 0, 'user' => $this->user->get_id($this->session->userdata('username')), 'log' => $this->session->userdata('log'),
                               'check_acc' => $check);
//
            $this->model->add($appayment);
            echo "true|One $this->title data successfully saved!|".$this->model->max_id();
        }
        else{ echo "error|".validation_errors(); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    function add_trans($pid=null)
    {
        $this->acl->otentikasi2($this->title);
        $this->model->valid_add_trans($pid, $this->title);
        
        $appayment = $this->model->get_by_id($pid)->row();
        
        $data['title'] = $this->properti['name'].' | Administrator '.ucwords($this->modul['title']);
        $data['h2title'] = 'Create New '.$this->modul['title'];
	    $data['form_action'] = site_url($this->title.'/update_process/'.$pid);
        $data['form_action_item'] = site_url($this->title.'/add_item/'.$pid);
        $data['currency'] = $this->currency->combo();
        $data['vendor'] = $this->vendor->combo();
        $data['code'] = $appayment->no;
        $data['user'] = $this->session->userdata("username");
        $data['pid'] = $pid;
        
        $data['main_view'] = 'ap_form';
        $data['source'] = site_url($this->title.'/getdatatable');
        $data['link'] = array('link_back' => anchor($this->title,'Back', array('class' => 'btn btn-danger')));

         // account list
        if ($appayment->acc == 'bank'){  $acc = $this->account->combo_asset(); }
        else{  $acc = $this->account->combo_based_classi(7); }
        
        $data['bank'] = $acc;
        $data['venid'] = $appayment->vendor;

        $data['default']['vendor'] = $this->vendor->get_vendor_name($appayment->vendor);
        $data['default']['date'] = $appayment->dates;
        $data['default']['currency'] = $appayment->currency;
        $data['default']['check'] = $appayment->check_no;
        $data['default']['balance'] = $appayment->amount;
        $data['default']['tdiscount'] = $appayment->discount;
        $data['default']['late'] = $appayment->late;
        $data['default']['acc'] = $appayment->acc;
        $data['default']['docno'] = $appayment->docno;
        $data['default']['rate'] = $appayment->rate;
        $data['default']['no'] = $appayment->no;
        $data['default']['status'] = $appayment->post_dated;
        $data['default']['bank'] = $appayment->account;
        
        $check = explode('|', $appayment->check_acc);
        $data['default']['checkaccno'] = $check[0];
        $data['default']['checkaccname'] = $check[1];
        $data['default']['checkaccbank'] = $check[2];

        $data['default']['user'] = $this->user->get_username($appayment->user);

//        ============================ Check  =========================================

        $data['default']['due'] = $appayment->due;
        $data['default']['balancecek'] = $appayment->amount;

//        ============================ Purchase Item  =========================================
        $data['items'] = $this->transmodel->get_last_item($pid)->result();
        
        $this->load->view('template', $data);
    }

//    ======================  Item Transaction   ===============================================================

    function add_item($pid=null)
    {
        $this->form_validation->set_rules('titem', 'Transaction', 'required|callback_valid_po');
        $this->form_validation->set_rules('tnominal', 'Nominal', 'required|numeric');
        $this->form_validation->set_rules('tdiscount', 'Discount', 'required|numeric');
        $this->form_validation->set_rules('tamount', 'Amount', 'required|numeric');

        if ($this->form_validation->run($this) == TRUE && $this->valid_confirmation($pid) == TRUE && $pid != null)
        {
            $code = 'PO';
            $amount = $this->input->post('tamount');

            $pitem = array('ap_payment' => $pid, 'code' => $code, 'no' => $this->input->post('titem'), 'nominal' => $this->input->post('tnominal'), 
                           'discount' => $this->input->post('tdiscount'), 'amount' => $this->calculate_rate($pid,$amount));
            
            $this->transmodel->add($pitem);
            $this->update_trans($pid,$code);
            echo 'true';
        }
        elseif ( $this->valid_confirmation($pid) != TRUE ){ echo "error|Can't change value - Journal approved..!"; }
        elseif (!$pid){ echo "error|Can't change value - Journal not created..!"; }
        else{ echo "error|".validation_errors(); }
    }
    
    private function update_trans($pid,$code='PO')
    {
        $totals = $this->transmodel->total($pid,$code);
        $res = $totals['amount'];
        
        $val = $this->model->get_by_id($pid)->row();
        $res = $res+$val->late;
        
        $appayment = array('amount' => $res, 'discount' => $totals['discount']);
	$this->model->update($pid, $appayment);
    }

    private function calculate_rate($pid,$amount)
    {
        $rate = $this->model->get_by_id($pid)->row();
        $rate = $rate->rate;
        return $rate*$amount;
    }

    function add_return($po=null)
    {
        $this->cek_confirmation($po,'add_trans');

        $this->form_validation->set_rules('treturn', 'Return Transaction', 'required|callback_valid_pr');

        if ($this->form_validation->run($this) == TRUE)
        {
            $purchase = $this->purchase_return->get_pr($this->input->post('treturn'));

            $pitem = array('ap_payment' => $po, 'code' => 'PR', 'no' => $this->input->post('treturn'), 'amount' => $purchase->balance);
            $this->transmodel->add($pitem);
            $this->update_trans($po);

            echo 'true';
        }
        else{   echo validation_errors(); }
    }


    function delete_item($id)
    {
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){ 
            $pid = $this->transmodel->get_by_id($id)->row();
            if ($this->valid_confirmation($pid->ap_payment) == TRUE){
               $this->transmodel->delete($id);
               $this->update_trans($pid->ap_payment,'PO');
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
        $this->form_validation->set_rules('tid', 'ID','required|callback_valid_confirmation');
        $this->form_validation->set_rules('tno', 'Order No','required');
        $this->form_validation->set_rules('tcheck', 'Check No', 'callback_valid_check');
        $this->form_validation->set_rules('tdate', 'Date', 'required|callback_valid_period');
        $this->form_validation->set_rules('cbank', 'Bank', 'required');
        $this->form_validation->set_rules('tdue', 'Due Date', 'callback_valid_check');
        $this->form_validation->set_rules('tbalancecek', 'Cheque Balance', 'required|numeric');
        $this->form_validation->set_rules('tdocno', 'Document No', '');

        if ($this->form_validation->run($this) == TRUE)
        {   
            $check = $this->input->post('tcheckaccno').'|'.$this->input->post('tcheckaccname').'|'.$this->input->post('tccbank');
            $appayment = array('log' => $this->session->userdata('log'), 'acc' => $this->input->post('cacc'), 'dates' => $this->input->post('tdate'), 
                               'account' => $this->input->post('cbank'), 'late' => $this->input->post('tlate'),
                               'due' => setnull($this->input->post('tdue')), 'post_dated' => $this->input->post('cpost'),
                               'check_acc' => $check, 'check_no' => $this->cek_null($this->input->post('tcheck')));

            $this->model->update($pid, $appayment);
            
            $val = $this->model->get_by_id($pid)->row();
            $code = 'PO'; 
            $this->update_trans($pid,$code);
            
            if ($this->input->post('tbalancecek') > $val->amount){ $appayment1 = array('over' => intval($this->input->post('tbalancecek')-$val->amount), 'over_stts' => 1); }
            else{ $appayment1 = array('over' => 0, 'over_stts' => 0); }
            
            $this->model->update($pid, $appayment1);   
            $this->session->set_flashdata('message', "One $this->title has successfully updated!");
            echo "true|One $this->title data successfully updated!|".$pid;
        }
        else{ echo 'error|'.validation_errors(); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    public function valid_period($date=null)
    {
        $month = date('n', strtotime($date));
        $year  = date('Y', strtotime($date));
        
        if ( intval($this->period->month) != intval($month) || intval($this->period->year) != intval($year) )
        {
           $this->form_validation->set_message('valid_period', "Invalid Period.!"); return FALSE;
        }
        else {  return TRUE; }
    }

    public function valid_rate($rate)
    {
        if ($rate == 0)
        {
            $this->form_validation->set_message('valid_rate', "Rate can't 0.!");
            return FALSE;
        }
        else { return TRUE; }
    }

    public function valid_confirmation($pid)
    {
        $val = $this->model->get_by_id($pid)->row();

        if ($val->approved == 1)
        {
            $this->form_validation->set_message('valid_confirmation', "Can't change value - Order approved..!.!");
            return FALSE;
        }
        else {  return TRUE; }
    }

    public function valid_pr($no)
    {
        if ($this->transmodel->get_item_based_po($no,'PR') == FALSE)
        {
            $this->form_validation->set_message('valid_pr', "PR already registered to journal.!");
            return FALSE;
        }
        else { return TRUE; }
    }

    
    public function valid_po($no)
    {
        if ($this->transmodel->get_item_based_po($no,'PO') == FALSE)
        {
            $this->form_validation->set_message('valid_po', "PO already registered to journal.!");
            return FALSE;
        }
        else { return TRUE; }
    }

    function valid_check($val)
    {
        $acc = $this->input->post('tacc');

        if ($acc == 'bank')
        {
            if ($val == null) { $this->form_validation->set_message('valid_check', "Check No / Field Required..!"); return FALSE; }
            else { return TRUE; }
        }
        else { return TRUE; }
    }
    
    function valid_credit_over($no)
    {
        $val = $this->model->get_by_no($no)->row();

        if ($val->credit_over == 1)
        {
           $this->form_validation->set_message('valid_credit_over', "Transaction Has Credited To Another Transaction..!"); return FALSE;
        }
        else { return TRUE; }
    }

// ===================================== PRINT ===========================================

   function invoice($pid=0)
   {
       $this->acl->otentikasi2($this->title);

       $data['h2title'] = 'Print Invoice'.$this->modul['title'];

       $appayment = $this->model->get_by_id($pid)->row();
       $code = 'PO';
//
       $data['pono'] = $appayment->no;
       $data['acc'] = strtoupper($this->acc($appayment->acc));
       $data['podate'] = tgleng($appayment->dates);
       $data['bank'] = $this->account->get_code($appayment->account).' : '.$this->account->get_name($appayment->account);
       $data['docno'] = $appayment->docno;
       $data['vendor'] = $this->vendor->get_vendor_name($appayment->vendor);
       $data['ven_bank'] = $this->vendor->get_vendor_bank($appayment->vendor);
       $data['amount'] = number_format($appayment->amount);
       $data['late'] = number_format($appayment->late);
       $data['over'] = number_format($appayment->over);
       $data['check'] = $appayment->check_no;
       
       $check = explode('|', $appayment->check_acc);
       $data['checkaccno'] = $check[0];
       $data['checkacc'] = $check[1].'-'.$check[2];
       $data['check_type'] = "";
       $data['type'] = '';
       $data['voucher'] = '';
       $data['due'] = isset($appayment->due) ? tglin($appayment->due) : '';
//
       $data['items'] = $this->transmodel->get_po_details($pid)->result();

       $terbilang = $this->load->library('terbilang');
       if ($appayment->currency == 'IDR')
       { $data['terbilang'] = ucwords($terbilang->baca($appayment->amount+$appayment->over)).' Rupiah'; }
       else { $data['terbilang'] = ucwords($terbilang->baca($appayment->amount+$appayment->over)); }
       
       $data['accounting'] = $this->properti['accounting'];
       $data['manager'] = $this->properti['manager'];

       $this->load->view('appayment_invoice', $data); 
   }

// ===================================== PRINT ===========================================

    private function cek_null($val=null){ if ($val) { return $val; } else { return NULL; } }

    //    ================================ REPORT =====================================

    function report_process()
    {
        $this->acl->otentikasi2($this->title);
        $data['title'] = $this->properti['name'].' | Report '.ucwords($this->modul['title']);

        $vendor = $this->input->post('cvendor');
        $acc = $this->input->post('cacc');
        $cur = $this->input->post('ccurrency');
        
        $period = $this->input->post('reservation');  
        $start = picker_between_split($period, 0);
        $end = picker_between_split($period, 1);

        $data['currency'] = $cur;
        $data['start'] = $start;
        $data['end'] = $end;
        $data['acc'] = $acc;
        $data['rundate'] = tgleng(date('Y-m-d'));
        $data['log'] = $this->session->userdata('log');

//        Property Details
        $data['company'] = $this->properti['name'];
        $data['reports'] = $this->model->report($vendor,$start,$end,$acc,$cur)->result();

        $total = $this->model->total($vendor,$start,$end,$acc,$cur);
        $data['total'] = $total['amount'];
        
        $this->load->view('appayment_report', $data);
    }

//    ================================ REPORT =====================================
    
//    ================================ AJAX =====================================
    
    function get_po()
    {
       if ($this->input->post('po')) 
       {
          $purchase = $this->purchase->get_po($this->input->post('po'));
          echo intval($purchase->p2);
       }
       else { echo '0'; }
    }
    
    function payable_process()
    {
        $this->acl->otentikasi2($this->title);
        $data['title'] = $this->properti['name'].' | Report '.ucwords($this->modul['title']);

        $vendor = $this->input->post('cvendor');
        $cur = $this->input->post('ccurrency');
        
        $period = $this->input->post('reservation');  
        $start = picker_between_split($period, 0);
        $end = picker_between_split($period, 1);

        $data['currency'] = $cur;
        $data['start'] = tglin($start);
        $data['end'] = tglin($end);
        
        $data['rundate'] = tgleng(date('Y-m-d'));
        $data['log'] = $this->session->userdata('log');
        
        // properti
        $data['log']     = $this->session->userdata('log');
        $data['company'] = $this->properti['name'];
        $data['address'] = $this->properti['address'];
        $data['phone1']  = $this->properti['phone1'];
        $data['phone2']  = $this->properti['phone2'];
        $data['fax']     = $this->properti['fax'];
        $data['website'] = $this->properti['sitename'];
        $data['email']   = $this->properti['email'];
        
        $trans = new Trans_ledger_lib();
        $data['customer'] = $this->vendor->get_vendor_name($vendor);
        $data['open'] = $trans->get_sum_transaction_open_balance_ap(null, $cur, $start, $vendor, 'AP');
        $data['trans'] = $trans->get_transaction_ap(null, $cur, $start, $end, $vendor, 'AP')->result();
        
        $page = 'payable_card';
        $this->load->view($page, $data);
    }
    
    // ====================================== CLOSING ======================================
    function reset_process(){ $this->model->closing(); $this->transmodel->closing(); }
    
}

?>