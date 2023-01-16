<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once 'definer.php';

class Csales extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('Sales_model', '', TRUE);

        $this->properti = $this->property->get();
//        $this->acl->otentikasi();

        $this->modul = $this->components->get(strtolower(get_class($this)));
        $this->title = strtolower(get_class($this));
        $this->role = new Role_lib();
        $this->currency = new Currency_lib();
        $this->sales = new Product_lib();
        $this->customer = new Customer_lib();
        $this->payment = new Payment_lib();
        $this->city = new City_lib();
        $this->product = new Product_lib();
        $this->bank = new Bank_lib();
        $this->category = new Categoryproduct_lib();
        $this->journalgl = new Journalgl_lib();
        $this->branch = new Branch_lib();
        $this->period = new Period_lib();
        $this->period = $this->period->get();
        $this->tax = new Tax_lib();
        $this->account = new Account_lib();
        $this->arpayment = new Ar_payment_lib();
        $this->trans = new Trans_ledger_lib();
        $this->contract = new Contract_lib();
    }

    private $properti, $modul, $title, $sales, $bank, $journalgl, $arpayment, $contract;
    private $role, $currency, $customer, $payment, $city, $product ,$category, $branch, $period, $tax, $account, $trans;
    
    function index()
    {
//         echo constant("RADIUS_API");
       $this->session->unset_userdata('start'); 
       $this->session->unset_userdata('end');
       $this->get_last(); 
    }
     
    public function getdatatable($search=null,$branch='null',$payment='null',$confirm='null')
    {
        if(!$search){ $result = $this->Sales_model->get_last($this->modul['limit'])->result(); }
        else {$result = $this->Sales_model->search($branch,$payment,$confirm)->result(); }
	
        $output = null;
        if ($result){
                
         foreach($result as $res)
	 {
           $total = intval($res->amount);  
           if ($res->paid_date){ $status = 'S'; }else{ $status = 'C'; } 
           $ship = '-';
           
	   $output[] = array ($res->id, 'SO-0'.$res->id, tglin($res->dates), $this->customer->get_name($res->cust_id), idr_format($total),
                              idr_format($res->shipping), $status, $ship, $res->confirmation, $this->branch->get_name($res->branch_id), 
                              idr_format($res->amount-$res->sender_amount), $res->notes
                             );
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

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords('C-Sales Order');
        $data['h2title'] = 'C-Sales Order';
        $data['main_view'] = 'sales_view';
	$data['form_action'] = site_url($this->title.'/add_process');
        $data['form_action_update'] = site_url($this->title.'/update_process');
        $data['form_action_del'] = site_url($this->title.'/delete_all/hard');
        $data['form_action_report'] = site_url($this->title.'/report_process');
        $data['form_action_import'] = site_url($this->title.'/import');
        $data['form_action_confirmation'] = site_url($this->title.'/payment_confirmation');
        $data['link'] = array('link_back' => anchor('main/','Back', array('class' => 'btn btn-danger')));

        $data['branch'] = $this->branch->get_branch_default();
        $data['branch_combo'] = $this->branch->combo();
        $data['customer'] = $this->customer->combo();
        $data['contract'] = $this->contract->combo();
        $data['bank'] = $this->account->combo_asset();
        $data['array'] = array('','');
        $data['month'] = combo_month();
        $data['year'] = date('Y');
        $data['default']['month'] = date('n');
        
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
        $this->table->set_heading('#','No', 'Code', 'Branch', 'Date', 'Customer', 'Amount', 'Note', '#', 'Action');

        $data['table'] = $this->table->generate();
        $data['source'] = site_url($this->title.'/getdatatable/');
        $data['graph'] = site_url()."/sales/chart/".$this->input->post('cmonth').'/'.$this->input->post('tyear');
            
        // Load absen view dengan melewatkan var $data sbgai parameter
	$this->load->view('template', $data);
    }
    
    function chart($month=null,$year=null)
    {   
        $data = $this->category->get();
        $datax = array();
        foreach ($data as $res) 
        {  
           $tot = $this->Sales_model->get_sales_qty_based_category($res->id,$month,$year); 
           $point = array("label" => $res->name , "y" => $tot);
           array_push($datax, $point);      
        }
        echo json_encode($datax, JSON_NUMERIC_CHECK);
    }
    
    function get_list($currency='IDR',$cust=null,$st=0)
    {
        $this->acl->otentikasi1($this->title);

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['form_action'] = site_url($this->title.'/get_list');
        $data['main_view'] = 'sales_list';
        $data['currency'] = $this->currency->combo();
        $data['customer'] = $this->customer->combo();
        $data['link'] = array('link_back' => anchor($this->title.'/get_list','<span>back</span>', array('class' => 'back')));

        $purchases = $this->Sales_model->get_list($currency,$cust,$st)->result();

        $tmpl = array('table_open' => '<table id="example" width="100%" cellspacing="0" class="table table-striped table-bordered">');

        $this->table->set_template($tmpl);
        $this->table->set_empty("&nbsp;");

        //Set heading untuk table
        $this->table->set_heading('No', 'Code', 'Date', 'Acc', 'Cur', 'Branch', 'Total', 'Action');

        $i = 0;
        foreach ($purchases as $purchase)
        {
           $datax = array('name' => 'button', 'type' => 'button', 'class' => 'btn btn-primary',
                          'content' => 'Select', 'onclick' => 'setvalue(\''.$purchase->id.'\',\'titem\')'
                         );

            $this->table->add_row
            (
                ++$i, 'SO-0'.$purchase->id, tglin($purchase->dates), $this->payment->get_name($purchase->payment_id), strtoupper($purchase->currency), 
                $this->branch->get_name($purchase->branch_id), number_format($purchase->amount-$purchase->sender_amount,2),
                form_button($datax)
            );
        }

        $data['table'] = $this->table->generate();
        $this->load->view('sales_list', $data);
    }
    
    function publish($uid = null)
    {
       if ($this->acl->otentikasi3($this->title,'ajax') == TRUE){ 
         $val = $this->Sales_model->get_by_id($uid)->row();
         $mess = null;
         if ($val->confirmation == 1){ $mess = 'Transaction already posted.'; }
         if ($val->amount <= 0){ $mess = "Transaction has not value"; }
         
         if (!$mess){
            if ($val->cash == 1){ $paid = $val->dates; }else{ 
              // membuat kartu hutang
//              $this->trans->add('bank', 'SO', $val->id, $val->currency, $val->dates, intval($val->amount), 0, $val->cust_id, 'AR');
              $paid = null; 
            }
            $param = array('p1' => 0, 'paid_date' => setnull($paid), 'confirmation' => 1); 
            if ($this->Sales_model->update($uid, $param) == TRUE){
//               $this->create_journal($uid);
               
               echo 'true|Transaction Posted...!';
            }else{ echo "error|Posting Failure..!"; }
         }else{ echo "error|".$mess; }
       }else{ echo "error|Sorry, you do not have the right to change publish status..!"; }
    }
    
    function delete_all($type='hard')
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
             if ($type == 'soft') { $this->Sales_model->delete($cek[$i]); }
             else { $this->shipping->delete_by_sales($cek[$i]);
                    $this->Sales_model->force_delete($cek[$i]);  
             }
             $x=$x+1;
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
           
            $this->Sales_model->valid_add_trans($uid, $this->title);
            $sales = $this->Sales_model->get_by_id($uid)->row();
            if ($sales->confirmation == 1){   
             
             $this->contract->update_balance($sales->contract_id,$sales->amount,1);
             $param = array('confirmation' => 0, 'sender_amount' => 0, 'paid_date' => null, 'updated' => date('Y-m-d H:i:s'));
             $this->Sales_model->update($uid, $param);
             $this->journalgl->remove_journal('CSO', $uid);
             echo "true|1 $this->title successfully rollback..!";
             
            }elseif ($sales->confirmation == 0){
              $this->journalgl->remove_journal('CSO', $uid);
              $this->Sales_model->force_delete($uid);
              echo "true|1 $this->title successfully removed..!";    
            }else{ echo "error|$this->title related to another component..!"; }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    function add($param=0)
    {
        $this->acl->otentikasi2($this->title);
         
        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = 'Create New '.$this->modul['title'];
        $data['main_view'] = 'sales_form';
        if ($param == 0){$data['form_action'] = site_url($this->title.'/add_process'); $data['counter'] = $this->Sales_model->counter(); }
        else { $data['form_action'] = site_url($this->title.'/update_process'); $data['counter'] = $param; }
	
        $data['link'] = array('link_back' => anchor($this->title,'Back', array('class' => 'btn btn-danger')));
        $data['form_action_trans'] = site_url($this->title.'/add_item/0'); 
        $data['form_action_shipping'] = site_url($this->title.'/shipping/0'); 

        $data['branch']           = $this->branch->get_branch_default();
        $data['customer']         = $this->customer->combo();
        $data['contract']         = $this->contract->combo();
        $data['tax']              = $this->tax->combo();
        $data['payment']          = $this->payment->combo();
        $data['source']           = site_url($this->title.'/getdatatable');
        $data['graph']            = site_url()."/sales/chart/";
        $data['city']             = $this->city->combo_city_combine();
        $data['default']['dates'] = date("Y/m/d");

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
        $this->form_validation->set_rules('ccontract', 'Contract', 'required');
        $this->form_validation->set_rules('tdates', 'Transaction Date', 'required|callback_valid_period');
        $this->form_validation->set_rules('tduedates', 'Transaction Due Date', 'required');
        $this->form_validation->set_rules('cpayment', 'Payment Type', 'required');
        $this->form_validation->set_rules('tnote', 'Note', 'required');
        $this->form_validation->set_rules('tcosts', 'Landed Cost', 'numeric');

        if ($this->form_validation->run($this) == TRUE)
        {
            $sales = array('cust_id' => $this->contract->get_contract_customer($this->input->post('ccontract')), 
                           'contract_id' => $this->input->post('ccontract'), 'dates' => $this->input->post('tdates'),
                           'branch_id' => $this->branch->get_branch_default(), 'amount' => intval($this->input->post('tamount')+$this->input->post('ttax')),
                           'tax' => $this->input->post('ctax'), 'tax_val' => $this->input->post('ttax'),
                           'notes' => $this->input->post('tnote'), 'log' => $this->session->userdata('log'),
                           'due_date' => $this->input->post('tduedates'), 'payment_id' => $this->input->post('cpayment'), 
                           'cash' => 0, 'created' => date('Y-m-d H:i:s'));

            $this->Sales_model->add($sales);
            echo "true|One $this->title data successfully saved!|".$this->Sales_model->counter(1);
        }
        else{ $data['message'] = validation_errors(); echo "error|".validation_errors(); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    // Fungsi update untuk menset texfield dengan nilai dari database
    function update($param=0)
    {
        $this->acl->otentikasi2($this->title);
        $this->Sales_model->valid_add_trans($param, $this->title);
        
        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = 'Update '.$this->modul['title'];
        $data['main_view'] = 'sales_form';
        $data['form_action'] = site_url($this->title.'/update_process/'.$param); 
        $data['form_action_trans'] = site_url($this->title.'/add_item/'.$param); 
        $data['form_action_shipping'] = site_url($this->title.'/shipping/'.$param); 
        $data['counter'] = $param; 
	
        $data['link'] = array('link_back' => anchor($this->title,'Back', array('class' => 'btn btn-danger')));

        $data['branch'] = $this->branch->get_branch_default();
        $data['customer'] = $this->customer->combo();
        $data['payment'] = $this->payment->combo();
        $data['contract'] = $this->contract->combo();
        $data['source'] = site_url($this->title.'/getdatatable');
        $data['graph'] = site_url()."/sales/chart/";
        $data['city'] = $this->city->combo_city_combine();
        $data['tax'] = $this->tax->combo();
        
        $sales = $this->Sales_model->get_by_id($param)->row();
        $customer = $this->customer->get_details($sales->cust_id)->row();

        $data['default']['customer'] = $sales->cust_id;
        $data['default']['contract'] = $sales->contract_id;
        $data['default']['email'] = $customer->email;
        $data['default']['ship_address'] = $customer->shipping_address;
        $data['default']['dates'] = $sales->dates;
        $data['default']['due_date'] = $sales->due_date;
        $data['default']['payment'] = $sales->payment_id;
        $data['default']['costs'] = $sales->cost;
        $data['default']['discount'] = $sales->discount;
        $data['default']['cash'] = $sales->cash;
        $data['default']['p1'] = $sales->p1;
        $data['default']['note'] = $sales->notes;
        $data['default']['amount'] = $sales->amount;
        $data['default']['tax'] = $sales->tax;
        $data['default']['taxval'] = $sales->tax_val;
        $data['total'] = $sales->amount;
        $data['shipping'] = $sales->shipping;
        $data['tot_amt'] = intval($sales->amount+$sales->shipping-$sales->tax_val);
        
        // weight total
        $data['tax_total']   = $sales->tax_val;
        $data['discount']    = $sales->discount;
        $data['p1']    = $sales->p1;
        
        $this->load->view('template', $data);
    }
    
    private  function get_romawi($val)
   {
       switch ($val)
       {
           case '01': $val = 'I'; break;
           case '02': $val = 'II'; break;
           case '03': $val = 'III'; break;
           case '04': $val = 'IV'; break;
           case '05': $val = 'V'; break;
           case '06': $val = 'VI'; break;
           case '07': $val = 'VII'; break;
           case '08': $val = 'VIII'; break;
           case '09': $val = 'IX'; break;
           case '10': $val = 'X'; break;
           case '11': $val = 'XI'; break;
           case '12': $val = 'XII'; break;
       }
       return $val;
   } 
    
   function invoice($pid=null,$type=null)
   {
       $this->acl->otentikasi2($this->title);

       $data['h2title'] = 'Print Tax Invoice'.$this->modul['title'];
       
       // property display
       $data['p_name'] = $this->properti['name'];
       $data['paddress'] = $this->properti['address'];
       $data['p_phone1'] = $this->properti['phone1'];
       $data['p_phone2'] = $this->properti['phone2'];
       $data['p_city'] = ucfirst($this->properti['city']);
       $data['p_zip'] = $this->properti['zip'];
       $data['logo'] = $this->properti['logo'];
       $data['p_email'] = $this->properti['email'];
       $data['p_sitename'] = $this->properti['sitename'];

       $sales = $this->Sales_model->get_by_id($pid)->row();
       $year  = date('Y', strtotime($sales->dates));
       
       $customer = $this->customer->get_details($sales->cust_id)->row();
       $data['customer'] = strtoupper($customer->first_name.' '.$customer->last_name);
       $data['address'] = $customer->address;
       $data['city'] = $customer->city;
       $data['phone'] = $customer->phone1.' / '.$customer->phone2;
       
       $data['pono'] = '0'.$sales->id.' / '.$this->get_romawi(date("m",strtotime($sales->dates))).' / P / '.date("Y",strtotime($sales->dates)).' / CO-0'.$sales->contract_id;
       $data['podate'] = tglincomplete($sales->dates);
       $data['desc'] = '';
       $data['notes'] = $sales->notes;
       $data['user'] = $sales->log;
       $data['currency'] = 'IDR';

       $data['symbol'] = 'Rp.'; $matauang = 'rupiah';

       $data['cost'] = $sales->cost;
       $data['p2'] = 0;
       $data['p1'] = $sales->p1;
       $data['discount'] = $sales->discount;
       $data['discountpercent'] = 0;
       $data['tax'] = intval($sales->tax*100);
       $data['tax_val'] = $sales->tax_val;

       if ($sales->tax != 0){ $data['tax_percent'] = ''; }else { $data['tax_percent'] = ''; }
       $data['bruto'] = intval($sales->amount-$sales->tax_val-$sales->cost+$sales->discount);

       $data['total'] = $sales->amount;
       $data['netto'] = intval($sales->amount-$sales->tax_val-$sales->cost-$sales->discount);
       $terbilang = $this->load->library('terbilang');
       $data['terbilang'] = ucwords($terbilang->baca($sales->amount).' '.$matauang);
       
       $data['banklist'] = $this->bank->get_publish();

       $this->load->view('sales_invoice', $data);
   }
    
    function update_process($param)
    {
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'sales_form';
        $data['form_action'] = site_url($this->title.'/update_process/'.$param); 
	$data['link'] = array('link_back' => anchor('category/','<span>back</span>', array('class' => 'back')));

	// Form validation
        $this->form_validation->set_rules('ccontract', 'Contract', 'required');
        $this->form_validation->set_rules('tdates', 'Transaction Date', 'required|callback_valid_period');
        $this->form_validation->set_rules('tduedates', 'Transaction Due Date', 'required');
        $this->form_validation->set_rules('cpayment', 'Payment Type', 'required');
        $this->form_validation->set_rules('tcosts', 'Landed Cost', 'numeric');

        if ($this->form_validation->run($this) == TRUE && $this->valid_confirm($param) == TRUE)
        {   
            
            $sales = array('dates' => $this->input->post('tdates'),
                           'branch_id' => $this->branch->get_branch_default(), 'amount' => intval($this->input->post('tamount')+$this->input->post('ttax')),
                           'tax' => $this->input->post('ctax'), 'tax_val' => $this->input->post('ttax'),
                           'notes' => $this->input->post('tnote'), 'log' => $this->session->userdata('log'),
                           'due_date' => $this->input->post('tduedates'), 'payment_id' => $this->input->post('cpayment'), 
                           'cash' => 0, 'updated' => date('Y-m-d H:i:s'));

            $this->Sales_model->update($param, $sales);
//            $this->mail_invoice($param); // send email confirmation
            echo "true|One $this->title data successfully saved!|".$param;
        }
        elseif ($this->valid_confirm($param) != TRUE){ echo "error|Sales Already Confirmed..!"; }
        else{ echo "error|".validation_errors(); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
        //redirect($this->title.'/update/'.$param);
    }
    
    function confirmation($sid)
    {
        $sales = $this->Sales_model->get_by_id($sid)->row();
	$this->session->set_userdata('langid', $sales->id);
        
        echo $sid.'|'.$sales->sender_name.'|'.$sales->sender_acc.'|'.$sales->sender_bank.'|'.$sales->sender_amount.'|'.$sales->bank_id.'|'.$sales->confirmation.'|'.
             tglin($sales->paid_date).'|'.date("H:i:s", $sales->paid_date);
    }
    
    private function create_journal($sid)
    {
        $this->journalgl->remove_journal('SO', $sid);
        $this->journalgl->remove_journal('CS', $sid);
        $this->journalgl->remove_journal('CR', '0000'.$sid);
        
        $sales = $this->Sales_model->get_by_id($sid)->row();
        $totals = $this->sitem->total($sid);
        
        $cm = new Control_model();
        
        $landed   = $cm->get_id(2);
        $discount = $cm->get_id(4);
        $tax      = $cm->get_id(18);
        $stock    = $this->branch->get_acc($sales->branch_id, 'stock');
        $ar       = $this->branch->get_acc($sales->branch_id, 'ar');
        $bank     = $this->branch->get_acc($sales->branch_id, 'bank');
        $kas      = $this->branch->get_acc($sales->branch_id, 'cash');
        $salesacc = $this->branch->get_acc($sales->branch_id, 'sales');
        $cost     = $this->branch->get_acc($sales->branch_id, 'unit');
        $hpp      = intval($totals['hpp']);
        
        if ($sales->cash == 1){
           if ($this->payment->get_name($sales->payment_id) == 'Cash'){ $account = $kas; } // kas
           else { $account = $bank; }    
        }else{ $account = $ar; }
        
        
        if ($sales->p1 > 0)
        {  
           // create journal- GL
           $this->journalgl->new_journal($sales->id,$sales->dates,'SO','IDR','Sales Order',$sales->amount, $this->session->userdata('log'));
           $this->journalgl->new_journal('0000'.$sales->id,$sales->dates,'CR','IDR','Customer DP Payment : SO'.$sales->id,$sales->p1, $this->session->userdata('log'));
           
           $jid = $this->journalgl->get_journal_id('SO',$sales->id);
           $dpid = $this->journalgl->get_journal_id('CR','0000'.$sales->id);
           
           $this->journalgl->add_trans($jid,$cost, $hpp, 0); // tambah biaya 1 (hpp)
           $this->journalgl->add_trans($jid,$stock,0,$hpp); // kurang persediaan
           $this->journalgl->add_trans($jid,$ar,$sales->p1+$sales->amount,0); // piutang usaha bertambah
           $this->journalgl->add_trans($jid,$salesacc,0,$sales->total); // tambah penjualan
           
           if ($sales->tax > 0){ $this->journalgl->add_trans($jid,$tax,0,$sales->tax); } // pajak penjualan
           if ($sales->cost > 0){ $this->journalgl->add_trans($jid,$landed,0,$sales->cost); } // landed costs
           if ($sales->discount > 0){ $this->journalgl->add_trans($jid,$discount,$sales->discount,0); } // discount
           
           //DP proses
           if ($this->payment->get_name($sales->payment_id) == 'Cash'){ $dp_acc = $kas; } // kas
           else { $dp_acc = $bank; }    
           
           $this->journalgl->add_trans($dpid,$dp_acc,$sales->p1,0); //bank penjualan
           $this->journalgl->add_trans($dpid,$ar,0,$sales->p1); // piutang usaha kurang dp
           
        }
        else
        {   
            $this->journalgl->new_journal($sales->id,$sales->dates,'SO','IDR','Sales Order',$sales->amount, $this->session->userdata('log'));
            $jid = $this->journalgl->get_journal_id('SO',$sales->id);
            
            $this->journalgl->add_trans($jid,$cost, $hpp, 0); // tambah biaya 1 (hpp)
            $this->journalgl->add_trans($jid,$stock, 0, $hpp); // kurang persediaan
            $this->journalgl->add_trans($jid,$account,$sales->p1+$sales->amount,0); // piutang usaha bertambah
            $this->journalgl->add_trans($jid,$salesacc,0,$sales->total); // tambah penjualan
           
            if ($sales->tax > 0){ $this->journalgl->add_trans($jid,$tax,0,$sales->tax); } // pajak penjualan
            if ($sales->cost > 0){ $this->journalgl->add_trans($jid,$landed,0,$sales->cost); } // landed costs
            if ($sales->discount > 0){ $this->journalgl->add_trans($jid,$discount,$sales->discount,0); } // discount
        }
    }

    
    function payment_confirmation()
    {
       if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'sales_form';
	$data['link'] = array('link_back' => anchor('category/','<span>back</span>', array('class' => 'back')));

	// Form validation
        $this->form_validation->set_rules('tcdates', 'Confirmation Date', 'required');
        $this->form_validation->set_rules('taccname', 'Account Name', 'required');
        $this->form_validation->set_rules('taccno', 'Account No', 'required');
        $this->form_validation->set_rules('taccbank', 'Account Bank', 'required');
        $this->form_validation->set_rules('tamount', 'Amount', 'numeric|required|callback_valid_payment_amount');
        $this->form_validation->set_rules('cbank', 'Merchant Bank', 'required');

        if ($this->form_validation->run($this) == TRUE && $this->valid_confirm($this->session->userdata('langid')) == FALSE)
        {
            $res = $this->Sales_model->get_by_id($this->session->userdata('langid'))->row();
            if ($res->paid_date == null){
                $sales = array('confirmation' => 1, 'updated' => date('Y-m-d H:i:s'),
                               'paid_date' => $this->input->post('tcdates'),
                               'sender_name' => $this->input->post('taccname'), 'sender_acc' => $this->input->post('taccno'),
                               'sender_bank' => $this->input->post('taccbank'), 'sender_amount' => $this->input->post('tamount'),
                               'bank_id' => $this->input->post('cbank')
                    );
                $stts = 'confirmed!';
//                if ($this->contract->update_balance($res->contract_id,$this->input->post('tamount')) == TRUE){
                    
                    $this->contract->update_balance($res->contract_id,$this->input->post('tamount'));
                    // update contract balance
                    $this->Sales_model->update($this->session->userdata('langid'), $sales);
                    $this->confirmation_journal($this->session->userdata('langid'));
                    echo "true|One $this->title data payment successfully ".$stts;  
//                }else{ echo "error|One $this->title data payment failure.. ";   }
                 
            }
            else { echo 'error|Sales Order already confirmed...!'; }
        }
        elseif ($this->valid_confirm($this->session->userdata('langid')) != FALSE){ echo "error|Sales Order Not Confirmed..!"; }
        else{ echo "error|". validation_errors(); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; } 
    }
    
    private function confirmation_journal($sid)
    {
        $sales = $this->Sales_model->get_by_id($sid)->row();
        $ar   = $this->branch->get_acc($sales->branch_id, 'ar');
        $bank = $sales->bank_id;
        
        $contract = $this->contract->get_by_id($sales->contract_id)->row();
        
        $cm = new Control_model();
        $salestax = $cm->get_id(18);
        $contracttax = $contract->tax_account; // tax keluaran contract
        $ar = $contract->ar_account; // piutang kontrak
        
        $this->journalgl->new_journal($sales->id,$sales->paid_date,'CSO','IDR','Payment Confirmation - CSO-0'.$sales->id,$sales->amount, $this->session->userdata('log'));
        $jid = $this->journalgl->get_journal_id('CSO',$sales->id);
        
        $this->journalgl->add_trans($jid,$bank, $sales->amount, 0); // tambah bank
        $this->journalgl->add_trans($jid,$ar, 0, $sales->amount); // kurang piutang 
        
        if ($sales->tax_val > 0){
          $this->journalgl->add_trans($jid,$contracttax,$sales->tax_val,0); // tambah hutang tax sales
          $this->journalgl->add_trans($jid,$salestax,0,$sales->tax_val); // kurang hutang tax kontrak
        }
    }
    
    function valid_payment_amount($amount){
        
        $amt = $this->Sales_model->get_by_id($this->session->userdata('langid'))->row();
        $amt = $amt->amount;
        if ($amount < $amt){ $this->form_validation->set_message('valid_payment_amount', "Invalid Payment Amount.!"); return FALSE; }
        else{ return TRUE; }
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
    
    function valid_required($val)
    {
        $stts = $this->input->post('cstts');
        if ($stts == 1){
            if (!$val){
              $this->form_validation->set_message('valid_required', "Field Required..!"); return FALSE;
            }else{ return TRUE; }
        }else{ return TRUE;  }
    }
    
    function valid_login()
    {
        if (!$this->session->userdata('username')){
            $this->form_validation->set_message('valid_login', "Transaction rollback relogin to continue..!");
            return FALSE;
        }else{ return TRUE; }
    }
    
    function valid_request($product,$request)
    {
        $branch = $this->branch->get_branch_default();
        $pid = $this->product->get_id_by_sku($product);
        $qty = $this->stockledger->get_qty($pid, $branch, $this->period->month, $this->period->year);
        
        if ($request > $qty){
            $this->form_validation->set_message('valid_request', "Qty Not Enough..!");
            return FALSE;
        }else{ return TRUE; }
    }
    
    function valid_product($id,$sid)
    {
        if ($this->sitem->valid_product($id,$sid) == FALSE)
        {
            $this->form_validation->set_message('valid_product','Product already listed..!');
            return FALSE;
        }
        else{ return TRUE; }
    }
    
    function valid_name($val)
    {
        if ($this->Sales_model->valid('name',$val) == FALSE)
        {
            $this->form_validation->set_message('valid_name','Name registered..!');
            return FALSE;
        }
        else{ return TRUE; }
    }
    
    function valid_confirm($sid)
    {
        if ($this->Sales_model->valid_confirm($sid) == FALSE)
        {
            $this->form_validation->set_message('valid_confirm','Sales Already Confirmed..!');
            return FALSE;
        }
        else{ return TRUE; }
    }
    
    function valid_ar($sid)
    {
        $val = $this->Sales_model->get_by_id($sid)->row();
        if ($val->ar_status == 1)
        {$this->form_validation->set_message('valid_ar','Sales Related To Another Component..!'); return FALSE;}
        else{ return TRUE; }
    }
    
    function valid_items($sid)
    {
        if ($this->sitem->valid_items($sid) == FALSE)
        {
            $this->form_validation->set_message('valid_items',"Empty Transaction..!");
            return FALSE;
        }
        else{ return TRUE; }
    }
    
    function valid_customer($cust,$contract)
    {
        if ($contract == null){
            if (!$cust){ $this->form_validation->set_message('valid_customer',"Customer Required..!"); return FALSE; }
            else{ return TRUE; }
        }
        else{ return TRUE; }
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
        $paid = $this->input->post('cpaid');
        $confirm = $this->input->post('cconfirm');
        $cust = $this->input->post('ccustomer');
        $branch = $this->input->post('cbranch');

        $data['branch'] = $this->branch->get_name($branch);
        $data['start'] = tglin($start);
        $data['end'] = tglin($end);
        if (!$paid){ $data['paid'] = ''; }elseif ($paid == 1){ $data['paid'] = 'Paid'; }else { $data['paid'] = 'Unpaid'; }
        if (!$confirm){ $data['confirm'] = ''; }elseif ($confirm == 1){ $data['confirm'] = 'Confirmed'; }else { $data['confirm'] = 'Unconfirmed'; }
        
//        Property Details
        $data['company'] = $this->properti['name'];
        $data['reports'] = $this->Sales_model->report($branch,$cust,$start,$end,$paid,$confirm)->result();
//        
        $type = $this->input->post('ctype');
        if ($type == 0){ $this->load->view('sales_report', $data); }
        elseif($type == 1) { $this->load->view('sales_pivot', $data); }
    }   

}

?>