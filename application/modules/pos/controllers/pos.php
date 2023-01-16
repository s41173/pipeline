
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once 'definer.php';

class Pos extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('Pos_model', 'model', TRUE);
        $this->load->model('Sales_item_pos_model', 'sitem', TRUE);
        $this->load->model('Sales_pos_model', 'salesmodel', TRUE);

        $this->acl->otentikasi();
        $this->properti = $this->property->get();

        $this->modul = $this->components->get(strtolower(get_class($this)));
        $this->title = strtolower(get_class($this));
        $this->role = new Role_lib();
        $this->currency = new Currency_lib();
        $this->sales = new Sales_lib();
        $this->payment = new Payment_lib();
        $this->city = new City_lib();
        $this->product = new Product_lib();
        $this->bank = new Bank_lib();
        $this->category = new Categoryproduct_lib();
        $this->stock = new Stock_lib();
        $this->journalgl = new Journalgl_lib();
        $this->branch = new Branch_lib();
        $this->period = new Period_lib();
        $this->period = $this->period->get();
        $this->stockledger = new Stock_ledger_lib();
        $this->tax = new Tax_lib();
        $this->account = new Account_lib();
        $this->wt = new Warehouse_transaction_lib();
        $this->asmformula = new Assembly_formula_lib();
    }

    private $properti, $modul, $title, $sales, $wt ,$shipping, $bank, $stock, $journalgl, $stockledger, $asmformula;
    private $role, $currency, $customer, $payment, $city, $product ,$category, $branch, $period, $tax, $account;
    
    function index()
    {
//         echo constant("RADIUS_API");
       $this->session->unset_userdata('start'); 
       $this->session->unset_userdata('end');
       $this->get_last(); 
    }
        
//     ============== ajax ===========================
    
    function get_product($pid)
    {
        $res = $this->product->get_detail_based_id($pid);
        if ($res){ echo intval($res->price-$res->discount); }else{ return 0; }
        
    }
    
    function get_product_based_sku($sku)
    {
        $res = $this->product->get_detail_based_sku($sku);
        echo intval($res->price)-intval($res->discount);
    }
    
    function valid_orderid($orderid)
    {
        if ($this->model->valid_orderid($orderid) == TRUE){ echo 'true'; }else{ echo 'false'; }
    }

//     ============== ajax ===========================
     
    public function getdatatable($search=null,$payment='null',$dates='null')
    {
        if(!$search){ $result = $this->model->get_last($this->modul['limit'])->result(); }
        else {$result = $this->model->search($payment,$dates)->result(); }
	
        $output = null;
        if ($result){
                
         foreach($result as $res)
	 {
           $total = $this->model->get_amount_based_orderid($res->orderid);
           $sales = $this->sales->get_by_id($res->sales_id)->row();
           
	   $output[] = array ($res->id, $res->orderid, 'SO-0'.$res->sales_id, tglin($sales->dates),  idr_format($total), 
                              $this->payment->get_name($sales->payment_id), timein($sales->dates));
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

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords('Sales Order');
        $data['h2title'] = 'POS - Order';
        $data['main_view'] = 'pos_view';
	$data['form_action'] = site_url($this->title.'/add_process');
        $data['form_action_update'] = site_url($this->title.'/update_process');
        $data['form_action_del'] = site_url($this->title.'/delete_all/hard');
        $data['form_action_report'] = site_url($this->title.'/report_process');
        $data['form_action_confirmation'] = site_url($this->title.'/payment_confirmation');
        $data['link'] = array('link_back' => anchor('sales/','Back', array('class' => 'btn btn-danger')));

        $data['payment'] = $this->payment->combo_pos();
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
        $this->table->set_heading('#','No', 'SO', 'Code', 'Date', 'Balance', 'Payment', 'Action');

        $data['table'] = $this->table->generate();
        $data['source'] = site_url($this->title.'/getdatatable/');
        $data['graph'] = site_url()."/sales/chart/".$this->input->post('cmonth').'/'.$this->input->post('tyear');
            
        // Load absen view dengan melewatkan var $data sbgai parameter
	$this->load->view('template', $data);
    }

    function delete($uid)
    {
        if ($this->acl->otentikasi_admin($this->title,'ajax') == TRUE){
           
            $sales = $this->salesmodel->get_by_id($uid)->row();
            if ($sales->confirmation == 1){
                
             $param = array('confirmation' => 0, 'paid_date' => null, 'updated' => date('Y-m-d H:i:s'));
             $this->salesmodel->update($uid, $param);   
             
             $this->journalgl->remove_journal('SO', $uid);
             $this->journalgl->remove_journal('CS', $uid);
             $this->journalgl->remove_journal('CR', '0000'.$uid);
             echo "true|1 $this->title successfully rollback..!";
            }else{
              $this->journalgl->remove_journal('SO', $uid);
              $this->journalgl->remove_journal('CS', $uid);
              $this->journalgl->remove_journal('CR', '0000'.$uid);
              
              $this->stock->rollback('SO', $uid); // rollback stock
              $this->wt->remove($sales->dates, 'SO-'.$uid); // delete wt
              $this->sitem->delete_sales($uid);
              $this->salesmodel->force_delete($uid);
              echo "true|1 $this->title successfully removed..!";    
            }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
        
    }
    
    function add($param=null,$target=null)
    {
        $this->acl->otentikasi2($this->title);
         
        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = 'Create New '.$this->modul['title'];
        
        if (!$param){  $data['default']['dates'] = date('y-m-d'); $data['disabled'] = null;
                       $data['orderid'] = $this->model->counter().mt_rand(99,9999); $data['items'] = null; }
        else{ 
        
            if ($this->model->valid_orderid($param) == FALSE){ redirect('pos/add'); }    
            $orderid = $this->model->get_by_orderid($param)->row();
            $sales = $this->salesmodel->get_by_id($orderid->sales_id)->row();

            $data['orderid'] = $param; 
            $data['default']['dates'] = $sales->dates; 
            $data['default']['payment'] = $sales->payment_id;
            $data['disabled'] = 'disabled';
            $data['items'] = $this->model->get_by_orderid($param)->result();
        }
        
        if ($target==null){ $target = $this->title; }else{ $target = 'sales/update/'.$target; }  
        
        $data['link'] = array('link_back' => anchor($target,'Back', array('class' => 'btn btn-danger')));
        $data['branch'] = $this->branch->get_branch_default();
        $data['tax'] = $this->tax->combo();
        $data['payment'] = $this->payment->combo_pos();
        $data['source'] = site_url($this->title.'/getdatatable');
        
        $total = $this->model->total($param);
        $data['total'] = floatval($total['price']);
        $data['discount'] = floatval($total['discount']);
        $data['tax_total'] = floatval($total['tax']);
        $data['tot_amt'] = floatval($total['amount']);
        
        $data['main_view'] = 'pos_form';
        $this->load->view('template', $data);
    }
 
    function add_item()
    { 
       if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){ 
         
        $sid = $this->sales->create_pos($this->input->post('date'), $this->input->post('payment'), $this->session->userdata('log'), $this->branch->get_branch_default());   
        
         // Form validation
        $this->form_validation->set_rules('product', 'Product', 'required|callback_valid_product['.$sid.']');
        $this->form_validation->set_rules('price', 'Price', 'required|numeric|callback_valid_price');
        $this->form_validation->set_rules('tax', 'Tax Type', 'required');

            if ($this->form_validation->run($this) == TRUE && $this->valid_confirm($sid) == TRUE)
            {
                // start transaction 
                $this->db->trans_start(); 
                
                $pid = $this->product->get_id_by_sku($this->input->post('product'));
                $discount = intval($this->input->post('qty')*$this->input->post('discount'));
                $amt_price = intval($this->input->post('qty')*$this->input->post('price')-$discount);
                $tax = intval($this->input->post('tax')*$amt_price);
                $id = $this->model->counter(); // id nya pos transaction
                
                if ($this->valid_request($this->input->post('product'), $this->input->post('qty')) == TRUE){  
                   if ($this->asmformula->cek_product($pid)==TRUE){ 
                       $hpp = intval($this->input->post('qty')*$this->asmformula->details($pid, 'amount'));
                       $this->asmformula->min_stock($pid,$this->input->post('qty'),$sid,'SO',$id);
                   }
                   else{ 
                       $sales = $this->sales->get_detail_sales($sid);
                       $hpp = $this->stock->min_stock($pid, $this->input->post('qty'), $sid, 'SO', $id); 
                       // add wt
                       $this->wt->add($sales->dates, 'SO-'.$sid, $sales->branch_id, 'IDR', $pid, 0, $this->input->post('qty'),
                                      intval($hpp/$this->input->post('qty')), intval($hpp), $this->session->userdata('log'));
                   }
                }else{ $hpp = 0; }
                
                $sales = array('id' => $id, 'orderid' => $this->input->post('orderid'), 'product_id' => $pid, 'sales_id' => $sid,
                               'qty' => $this->input->post('qty'), 'tax' => $tax, 'discount' => $this->input->post('discount'), 'weight' => $this->product->get_weight($pid),
                               'hpp' => intval($hpp), 'price' => $this->input->post('price'), 'amount' => intval($amt_price+$tax));

                $this->sitem->add($sales);
                $this->update_trans($sid);
                echo "true|Sales Transaction data successfully saved!|".$this->input->post('orderid');
                
                $this->db->trans_complete();
                // end transaction
            }
            else{ echo "error|".validation_errors(); }  
       }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    private function update_trans($sid)
    {
        $totals = $this->sitem->total($sid);
        $price = intval($totals['amount']);
        
        $sales = $this->salesmodel->get_by_id($sid)->row();
        $cost = $sales->cost;
        
        // shipping total        
        $transaction = array('tax' => $totals['tax'], 'total' => $price, 'discount' => $totals['discount'], 
                             'amount' => intval($totals['tax']+$price+$cost-$totals['discount']-$sales->p1), 'shipping' => 0);
	$this->salesmodel->update($sid, $transaction);
    }
    
    function delete_item($id,$sid,$orderid)
    {
        if ($this->acl->otentikasi2($this->title) == TRUE && $this->valid_confirm($sid) == TRUE){ 
            
         // start transaction 
        $this->db->trans_start();    
            $res = $this->sitem->get_by_id($id)->row();
            $sales = $this->sales->get_detail_sales($sid);
            
            if ($this->asmformula->cek_product($res->product_id) == TRUE){
                $this->asmformula->rollback_stock($res->product_id,$res->qty,$sid,'SO',$id);
            }else{         
               if ($res->hpp != 0){ $this->stock->rollback('SO', $sid, $id); }
               $this->wt->remove($sales->dates, 'SO-'.$sid, $res->product_id); // remove wt 
            }

            $this->sitem->delete($id); // memanggil model untuk mendelete data
            $this->update_trans($sid);
            $this->session->set_flashdata('message', "1 item successfully removed..!"); 
            $this->db->trans_complete();
//       end transaction
        }else { $this->session->set_flashdata("error|Sorry, you do not have the right to edit $this->title component..!"); }
        redirect($this->title.'/add/'.$orderid);
    }
    
    private function split_array($val)
    { return implode(",",$val); }
   
    
    // Fungsi update untuk menset texfield dengan nilai dari database
    function update($param=0)
    {
        $this->acl->otentikasi2($this->title);
        
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
        $data['payment'] = $this->payment->combo_pos();
        $data['source'] = site_url($this->title.'/getdatatable');
        $data['graph'] = site_url()."/sales/chart/";
        $data['city'] = $this->city->combo_city_combine();
        $data['product'] = $this->product->combo();
        $data['tax'] = $this->tax->combo();
        
        $sales = $this->salesmodel->get_by_id($param)->row();
        $customer = $this->customer->get_details($sales->cust_id)->row();
        $data['default']['customer'] = $sales->cust_id;
        $data['default']['email'] = $customer->email;
        $data['default']['ship_address'] = $customer->shipping_address;
        $data['default']['dates'] = $sales->dates;
        $data['default']['due_date'] = $sales->due_date;
        $data['default']['payment'] = $sales->payment_id;
        $data['default']['costs'] = $sales->cost;
        $data['default']['discount'] = $sales->discount;
        $data['default']['cash'] = $sales->cash;
        $data['default']['p1'] = $sales->p1;
        $data['total'] = $sales->total;
        $data['shipping'] = $sales->shipping;
        $data['tot_amt'] = intval($sales->amount+$sales->shipping);
        
        // weight total
        $total = $this->sitem->total($param);
        $data['weight'] = round($total['weight']);
        $data['tax_total']    = $sales->tax;
        $data['discount']    = $sales->discount;
        $data['p1']    = $sales->p1;
        
        // transaction table
        $data['items'] = $this->sitem->get_last_item($param)->result();
        $this->load->view('template', $data);
    }
    
   function invoice($orderid=null,$type=null)
   {
       $this->acl->otentikasi2($this->title);

       $data['h2title'] = 'Print Tax Invoice'.$this->modul['title'];

       
       $pos = $this->model->get_by_orderid($orderid)->row();
       $sales = $this->salesmodel->get_by_id($pos->sales_id)->row();
       
       // customer
       //sales
       $data['orderid'] = $pos->orderid;
       $data['date'] = tglin($sales->dates);
       $data['user'] = $this->session->userdata('username');
       $data['currency'] = 'IDR';
       $data['log'] = $this->session->userdata('log');
       $data['discount'] = intval($sales->discount);
       $data['discount_desc'] = $sales->discount_desc;
       

       // sales item
       $data['items'] = $this->model->get_by_orderid($pos->orderid)->result();

       // branch display
       $branch = $this->branch->get_details($sales->branch_id)->row();
       $data['b_name'] = ucfirst($branch->name);
       $data['b_address'] = $branch->address;
       $data['b_phone1'] = $branch->phone;
       $data['b_phone2'] = $branch->mobile;
       $data['b_city'] = ucfirst($branch->city);
       $data['b_zip'] = $branch->zip;
       
       // summary
       $total = $this->model->total($pos->orderid);
       $data['total'] = intval($total['amount']-$sales->discount);

       $this->load->view('pos_invoice', $data);
       
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
        $this->form_validation->set_rules('ccustomer', 'Customer', 'required');
        $this->form_validation->set_rules('tdates', 'Transaction Date', 'required');
        $this->form_validation->set_rules('tduedates', 'Transaction Due Date', 'required');
        $this->form_validation->set_rules('cpayment', 'Payment Type', 'required');
        $this->form_validation->set_rules('tcosts', 'Landed Cost', 'numeric');

        if ($this->form_validation->run($this) == TRUE && $this->valid_confirm($param) == TRUE && $this->valid_items($param) == TRUE)
        {   
            if ($this->input->post('ccash') == 1){ $p1 = 0; $confirm = 1; $paid = $this->input->post('tdates'); }
            else{ $p1 = $this->input->post('tp1'); $confirm = 0; $paid=null;}
            
            $sales = array('cust_id' => $this->input->post('ccustomer'), 'dates' => $this->input->post('tdates'), 'branch_id' => $this->branch->get_branch_default(),
                           'due_date' => $this->input->post('tduedates'), 'payment_id' => $this->input->post('cpayment'), 'cost' => $this->input->post('tcosts'),
                           'p1' => $p1, 'paid_date' => $paid, 'confirmation' => $confirm,
                           'cash' => $this->input->post('ccash'), 
                           'updated' => date('Y-m-d H:i:s'));

            $this->model->update($param, $sales);
            $this->update_trans($param);
            $this->create_journal($param);
            $this->add_wt($param); // add warehouse transaction
//            $this->mail_invoice($param); // send email confirmation
            $this->session->set_flashdata('message', "One $this->title data successfully saved!");
            echo "true|One $this->title data successfully saved!|".$param;
        }
        elseif ($this->valid_confirm($param) != TRUE){ echo "error|Sales Already Confirmed..!"; }
        else{ echo "error|".validation_errors(); $this->session->set_flashdata('message', validation_errors()); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
        //redirect($this->title.'/update/'.$param);
    }
    
    function confirmation($sid)
    {
        $sales = $this->salesmodel->get_by_id($sid)->row();
	$this->session->set_userdata('langid', $sales->id);
        
        echo $sid.'|'.$sales->sender_name.'|'.$sales->sender_acc.'|'.$sales->sender_bank.'|'.$sales->sender_amount.'|'.$sales->bank_id.'|'.$sales->confirmation.'|'.
             tglin($sales->paid_date).'|'.date("H:i:s", $sales->paid_date);
    }
    
    private function add_wt($sid)
    {
        $sales = $this->salesmodel->get_by_id($sid)->row();
        $item = $this->sitem->get_last_item($sid)->result();
        
        $this->wt->remove($sales->dates, 'SO-'.$sid);
        
        foreach ($item as $value) {    
           $hpp = intval($value->hpp/$value->qty); 
           $this->wt->add( $sales->dates, 'SO-'.$sales->id, $sales->branch_id, 'idr', $value->product_id, 0, $value->qty,
                           $hpp, $value->hpp, $this->session->userdata('log')); 
        }
    }
    
    private function create_journal($sid)
    {
        $this->journalgl->remove_journal('SO', $sid);
        $this->journalgl->remove_journal('CS', $sid);
        $this->journalgl->remove_journal('CR', '0000'.$sid);
        
        $sales = $this->salesmodel->get_by_id($sid)->row();
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
        $this->form_validation->set_rules('tcdates', 'Confirmation Date', 'callback_valid_required');
        $this->form_validation->set_rules('taccname', 'Account Name', 'callback_valid_required');
        $this->form_validation->set_rules('taccno', 'Account No', 'callback_valid_required');
        $this->form_validation->set_rules('taccbank', 'Account Bank', 'callback_valid_required');
        $this->form_validation->set_rules('tamount', 'Amount', 'numeric|callback_valid_required');
        $this->form_validation->set_rules('cbank', 'Merchant Bank', 'callback_valid_required');

        if ($this->form_validation->run($this) == TRUE && $this->valid_confirm($this->session->userdata('langid')) == TRUE)
        {
            if ($this->input->post('cstts') == '1'){
                $sales = array('confirmation' => 1, 'updated' => date('Y-m-d H:i:s'),
                               'paid_date' => $this->input->post('tcdates'),
                               'sender_name' => $this->input->post('taccname'), 'sender_acc' => $this->input->post('taccno'),
                               'sender_bank' => $this->input->post('taccbank'), 'sender_amount' => $this->input->post('tamount'),
                               'bank_id' => $this->input->post('cbank')
                    );
                $stts = 'confirmed!';
                $this->salesmodel->update($this->session->userdata('langid'), $sales);
                $this->confirmation_journal($this->session->userdata('langid'));
            }
            else { $sales = array('confirmation' => 0, 'updated' => date('Y-m-d H:i:s')); 
                   $stts = 'unconfirmed!'; 
                   $this->salesmodel->update($this->session->userdata('langid'), $sales);
                   $this->journalgl->remove_journal('CS', $this->session->userdata('langid'));
                $status = true;
            }
            $status = true;
            if ($status == true){
               echo "true|One $this->title data payment successfully ".$stts;  
            }else { echo "error|Error Sending Mail...!! ";   }
        }
        elseif ($this->valid_confirm($this->session->userdata('langid')) != TRUE){ echo "error|Sales Order Already Confirmed..!"; }
        else{ echo "error|". validation_errors(); $this->session->set_flashdata('message', validation_errors()); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; } 
    }
    
    private function confirmation_journal($sid)
    {
        $sales = $this->salesmodel->get_by_id($sid)->row();
        $ar   = $this->branch->get_acc($sales->branch_id, 'ar');
        $bank = $sales->bank_id;
        
        $this->journalgl->new_journal($sales->id,$sales->paid_date,'CS','IDR','Payment Confirmation',$sales->amount, $this->session->userdata('log'));
        $jid = $this->journalgl->get_journal_id('CS',$sales->id);
        
        $this->journalgl->add_trans($jid,$bank, $sales->amount, 0); // tambah bank
        $this->journalgl->add_trans($jid,$ar, 0, $sales->amount); // kurang piutang
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
        
        if ($this->asmformula->cek_product($pid) == TRUE){ return TRUE;}
        else{
            $qty = $this->stockledger->get_qty($pid, $branch, $this->period->month, $this->period->year);
            if ($request > $qty){
                $this->form_validation->set_message('valid_request', "Qty Not Enough..!");
                return FALSE;
            }else{ return TRUE; }
        }
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
        if ($this->salesmodel->valid('name',$val) == FALSE)
        {
            $this->form_validation->set_message('valid_name','Name registered..!');
            return FALSE;
        }
        else{ return TRUE; }
    }
    
    function valid_price($price){
        $pid = $this->product->get_id_by_sku($this->input->post('product'));
        $lowprice = $this->product->get_by_id($pid)->row();
        $lowprice = intval($lowprice->pricelow);
        if ($price < $lowprice){ $this->form_validation->set_message('valid_price','Invalid Sales Price..!'); return FALSE; }
        else{ return TRUE; }
    }
    
    function valid_confirm($sid)
    {
        if ($this->salesmodel->valid_confirm($sid) == FALSE)
        {
            $this->form_validation->set_message('valid_confirm','Sales Already Confirmed..!');
            return FALSE;
        }
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
}

?>