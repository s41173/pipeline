<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Assembly extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('Assembly_model', 'model', TRUE);
        $this->load->model('Assembly_item_model', 'transmodel', TRUE);
        $this->load->model('Assembly_cost_model', 'costmodel', TRUE);

        $this->properti = $this->property->get();
        $this->acl->otentikasi();

        $this->modul = $this->components->get(strtolower(get_class($this)));
        $this->title = strtolower(get_class($this));

        $this->currency = new Currency_lib();
        $this->load->library('unit_lib');
        $this->product = new Product_lib();
        $this->user = new Admin_lib();
        $this->wt = new Warehouse_transaction_lib();
        $this->tax = new Tax_lib();
        $this->journalgl = new Journalgl_lib();
        $this->account = new Account_lib();
        $this->branch = new Branch_lib();
        $this->stock = new Stock_lib();
        $this->stockledger = new Stock_ledger_lib();
        $this->period = new Period_lib();
        $this->period = $this->period->get();
    }

    private $properti, $modul, $title, $stockvalue=0, $journalgl, $stock, $stockledger;
    private $user,$product,$wt,$opname,$currency,$account,$branch,$period,$tax;

    // ajax function
    function get_unit_price($pid=0){ echo $this->stock->unit_cost($this->product->get_id_by_sku($pid));}
    
    function index(){ $this->get_last(); }
    
    public function getdatatable($search=null,$dates='null',$product='null')
    {
        if(!$search){ $result = $this->model->get_last($this->modul['limit'])->result(); }
        else{ $result = $this->model->search($dates,$this->product->get_id_by_sku($product))->result(); }
        
        if ($result){
	foreach($result as $res)
	{
	   $output[] = array ($res->id, tglin($res->dates), $res->docno, $res->currency, $this->branch->get_name($res->branch_id),$res->approved, $res->log,
                              $this->product->get_name($res->product), $res->qty, $res->project, 'ASM-0'.$res->id);
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
        $data['main_view'] = 'assembly_view';
	$data['form_action'] = site_url($this->title.'/add_process');
        $data['form_action_update'] = site_url($this->title.'/update_process');
        $data['form_action_del'] = site_url($this->title.'/delete_all');
        $data['form_action_report'] = site_url($this->title.'/report_process');
        $data['link'] = array('link_back' => anchor('main/','Back', array('class' => 'btn btn-danger')));
        
        $data['branch'] = $this->branch->get_branch_default();
        $data['currency'] = $this->currency->combo();
        $data['branchcombo'] = $this->branch->combo();
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
        $this->table->set_heading('#', 'No', 'Code', 'Branch', 'Date', 'Code', 'Project', 'Product - Qty', 'Action');

        $data['table'] = $this->table->generate();
        $data['source'] = site_url($this->title.'/getdatatable');
            
        // Load absen view dengan melewatkan var $data sbgai parameter
	$this->load->view('template', $data);
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
        $stock_adjustment = $this->model->get_by_id($pid)->row();

        if ($stock_adjustment->approved == 1) { echo "warning|$this->title already approved..!"; }
        else
        {
            // start transaction 
            $this->db->trans_start();
                      
           // create journal
           $this->create_journal($pid); // create journal

            // add grand product
            $this->stock->add_stock($stock_adjustment->product, $stock_adjustment->dates, $stock_adjustment->qty, floatval($stock_adjustment->amount/$stock_adjustment->qty));
            
            // add wt
            $this->add_warehouse_transaction($stock_adjustment->id);
            $this->db->trans_complete();
           
            $data = array('approved' => 1);
            $this->model->update($pid, $data);
           
           if ($this->db->trans_status() === FALSE){ echo "error|IAJ-00$stock_adjustment->no failed confirmed..!";  }
           else { echo "true|ASM-0$pid confirmed..!"; }
        }

    }
    
    function create_journal($pid)
    {
        $assembly = $this->model->get_by_id($pid)->row();
        $cm = new Control_model();
        $stock = $this->branch->get_acc($assembly->branch_id, 'stock'); // stock
        $cost   = $cm->get_id(25); // biaya2 produksi
        $tax   = $cm->get_id(26); // hutang pajak produksi
        
        $this->journalgl->new_journal('0'.$pid,$assembly->dates,'ASM', strtoupper($assembly->currency),$assembly->docno.' - '.$assembly->notes,floatval($assembly->amount), $this->session->userdata('log'));
        $jid = $this->journalgl->get_journal_id('ASM','0'.$pid);
        
        $this->journalgl->add_trans($jid,$stock, $assembly->amount, 0); // tambah stock grand product
        $this->journalgl->add_trans($jid,$stock, 0, $assembly->unitprice); // stock bahan baku berkurang
        
        if ($assembly->costs > 0){ 
           //$this->journalgl->add_trans($jid,$cost, $assembly->costs, 0); 
           $this->journalgl->add_trans($jid,$assembly->account, 0, $assembly->costs);
        } // biaya produksi
        if ($assembly->taxamount > 0){ $this->journalgl->add_trans($jid,$tax, 0, $assembly->taxamount); } // hutang pajak produksi
        
    }

    private function add_warehouse_transaction($pid)
    {
        $val  = $this->model->get_by_id($pid)->row();
        $list = $this->transmodel->get_last_item($pid)->result();

        foreach ($list as $value)
        {
           $this->wt->add( $val->dates, 'ASM-0'.$val->id, $val->branch_id, $val->currency, $value->product_id, 0, $value->qty,
                           floatval($value->price/$value->qty), $value->price,
                           $this->session->userdata('log'));
           
        }
        
        $this->wt->add( $val->dates, 'ASM-0'.$val->id, $val->branch_id, $val->currency, $val->product, $val->qty, 0,
                        floatval($val->amount/$val->qty), $val->amount,
                        $this->session->userdata('log'));
    }

    private function del_warehouse_transaction($pid=0)
    {
        $val = $this->model->get_by_id($pid)->row();
        $this->wt->remove($val->dates, 'ASM-0'.$pid);        
    }

    private function cek_confirmation($po=null,$page=null)
    {
        $stock_adjustment = $this->model->get_stock_adjustment_by_no($po)->row();

        if ( $stock_adjustment->approved == 1 )
        {
           $this->session->set_flashdata('message', "Can't change value - BPBG-00$po approved..!"); // set flash data message dengan session
           if ($page){ redirect($this->title.'/'.$page.'/'.$po); } else { redirect($this->title); }
        }
    }
//    ===================== approval ===========================================


    function delete($uid)
    {
      if ($this->acl->otentikasi_admin($this->title,'ajax') == TRUE){
        $assembly = $this->model->get_by_id($uid)->row();    
        if ( $assembly->approved == 1 ){ $this->rollback($uid); }else{ $this->remove($uid);}            
      }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    private function rollback($uid)
    {
       $assembly = $this->model->get_by_id($uid)->row();
       if ($this->stock->valid_stock($assembly->product, $assembly->dates, $assembly->qty) == TRUE){
           
            $this->db->trans_start(); 
            $this->journalgl->remove_journal('ASM', '0'.$uid); // journal gl  

            $this->stock->increase_stock($assembly->product,$assembly->dates,$assembly->qty);  // decrease stock

            $this->del_warehouse_transaction($uid); 
            $data = array('approved' => 0);
            $this->model->update($uid, $data);
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE){ echo "warning|1 $this->title canceled rollback..!";}
            else{ echo "true|1 $this->title successfully rollback..!"; }   
            
       }else{ echo "error| Invalid Qty Grand Product"; }
    }
    
    private function remove($uid)
    {
       $this->db->trans_start(); 
       $stockadj = $this->model->get_by_id($uid)->row(); 
       $stockitem = $this->transmodel->get_last_item($uid)->result();
       
       if ($stockitem)
       {
          foreach($stockitem as $res)
          {   
             $this->stock->rollback('ASM', $res->assembly, $res->id); 
          } 
       }

       $this->transmodel->delete_po($uid);
       $this->model->force_delete($uid); 
       $this->db->trans_complete();
       
       if ($this->db->trans_status() === FALSE){ echo "warning|1 $this->title canceled removed..!"; }
       else { echo "true|1 $this->title successfully removed..!"; }
    }

    private function cek_relation($id=null)
    { $return = $this->return_stock->cek_relation($id, $this->title); if ($return == TRUE) { return TRUE; } else { return FALSE; } }

    function add()
    {
        $this->acl->otentikasi2($this->title);

        $data['title'] = $this->properti['name'].' | Administrator '.ucwords($this->modul['title']);
        $data['h2title'] = 'Create New '.$this->modul['title'];
	$data['form_action'] = site_url($this->title.'/add_process');
        $data['form_action_item'] = site_url($this->title.'/add_item/');
        $data['form_action_cost'] = site_url($this->title.'/add_cost/');
        
        $data['currency'] = $this->currency->combo();
        $data['pid'] = null;
        $data['user'] = $this->session->userdata("username");
        $data['account'] = $this->account->combo_asset();
        $data['branchid'] = $this->branch->get_branch_default();
        $data['tax'] = $this->tax->combo();
        
        $data['main_view'] = 'assembly_form';
        $data['source'] = site_url($this->title.'/getdatatable');
        $data['link'] = array('link_back' => anchor($this->title,'Back', array('class' => 'btn btn-danger')));
        
        $data['total'] = 0;
        $data['items'] = null;
        $data['itemcost'] = null;
        
        $this->load->view('template', $data);
    }
    
    function add_process()
    {
         if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){

	// Form validation
        $this->form_validation->set_rules('tproduct', 'Product', 'required');
        $this->form_validation->set_rules('tqty', 'Qty', 'required|numeric|is_natural_no_zero');
        $this->form_validation->set_rules('tdate', 'Invoice Date', 'required|callback_valid_period');
        $this->form_validation->set_rules('tnote', 'Note', 'required');
        $this->form_validation->set_rules('tproject', 'Project', '');
        $this->form_validation->set_rules('ccurrency', 'Currency', 'required');
        $this->form_validation->set_rules('ctax', 'Tax', 'required');
        $this->form_validation->set_rules('tdocno', 'Docno', 'required|callback_valid_no');
        $this->form_validation->set_rules('cacc', 'Account', 'required');

        if ($this->form_validation->run($this) == TRUE)
        {
            $stock_adjustment = array('docno' => $this->input->post('tdocno'), 'product' => $this->product->get_id_by_sku($this->input->post('tproduct')), 'qty' => $this->input->post('tqty'), 
                                      'currency' => $this->input->post('ccurrency'), 'dates' => $this->input->post('tdate'), 'branch_id' => $this->branch->get_branch_default(),
                                      'notes' => $this->input->post('tnote'), 'tax' => $this->input->post('ctax'), 'project' => setnull($this->input->post('tproject')),
                                      'account' => $this->input->post('cacc'),
                                      'log' => $this->session->userdata('log'), 'created' => date('Y-m-d H:i:s'));

            $this->model->add($stock_adjustment);
            echo "true|One $this->title data successfully saved!|".$this->model->max_id();
        }
        else{ echo "error|".validation_errors(); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }

    }
    
    function add_trans($id=null)
    {
        if (!$id){ redirect($this->title); } 
        
        $this->acl->otentikasi2($this->title);
        $this->model->valid_add_trans($id, $this->title);
        
        $cash = $this->model->get_by_id($id)->row();
        
        $data['title'] = $this->properti['name'].' | Administrator '.ucwords($this->modul['title']);
        $data['h2title'] = 'Create New '.$this->modul['title'];
	$data['form_action'] = site_url($this->title.'/update_process/'.$id);
        $data['form_action_item'] = site_url($this->title.'/add_item/'.$id);
        $data['form_action_cost'] = site_url($this->title.'/add_cost/'.$id);
        
        $data['currency'] = $this->currency->combo();
        $data['account'] = $this->account->combo_asset();
        $data['tax'] = $this->tax->combo();
        $data['pid'] = $id;
        $data['branchid'] = $this->branch->get_branch_default();
        
        $data['main_view'] = 'assembly_form';
        $data['source'] = site_url($this->title.'/getdatatable');
        $data['link'] = array('link_back' => anchor($this->title,'Back', array('class' => 'btn btn-danger')));
        
        $data['default']['taxid'] = $cash->tax;
        $data['default']['dates'] = $cash->dates;
        $data['default']['product'] = $this->product->get_sku($cash->product);
        $data['default']['qty'] = $cash->qty;
        $data['default']['currency'] = $cash->currency;
        $data['default']['note'] = $cash->notes;
        $data['default']['branch'] = $cash->branch_id;
        $data['default']['acc'] = $cash->account;
        $data['default']['docno'] = $cash->docno;
        $data['default']['project'] = $cash->project;
        $data['default']['cost'] = $cash->costs;
        $data['default']['tax'] = $cash->taxamount;
        $data['default']['unitprice'] = $cash->unitprice;
        $data['default']['total'] = $cash->amount;
        
        $data['items'] = $this->transmodel->get_last_item($id)->result();
        $data['itemcost'] = $this->costmodel->get_last_item($id)->result();
        $this->load->view('template', $data);
    }

// ========================= Import Process  =========================================================
    
    function import($pid=0)
    {
        if ($pid != 0 && $this->valid_confirmation($pid) == TRUE){
        
	$data['form_action_import'] = site_url($this->title.'/import');
        $data['error'] = null;
	
//        $this->form_validation->set_rules('userfile', 'Import File', '');
        
             // ==================== upload ========================
            
            $config['upload_path']   = './uploads/';
            $config['file_name']     = 'adjustment';
            $config['allowed_types'] = '*';
//            $config['allowed_types'] = 'csv';
            $config['overwrite']     = TRUE;
            $config['max_size']	     = '100000';
            $config['remove_spaces'] = TRUE;
            $this->load->library('upload', $config);
            
            if ( !$this->upload->do_upload("userfile"))
            { 
               $data['error'] = $this->upload->display_errors(); 
               $this->session->set_flashdata('message', "Error imported!");
               echo 'error|'.$this->upload->display_errors(); 
            }
            else
            { 
               // success page 
              $result = $this->import_process($config['file_name'].'.csv',$pid);
              
              $info = $this->upload->data(); 
              $this->session->set_flashdata('message', "One $this->title data successfully imported!");
              
              echo $result;
            }   
        }else{ echo 'error|Failed to import..!!'; }
        
    }
    
    private function valid_qty($val=0){ if ($val > 0){ return TRUE; }else{ return FALSE; } }
    
    function import_process($filename,$pid=0)
    {
        $stts = null;
        $this->load->helper('file');
//        $csvreader = new CSVReader();
        $csvreader = $this->load->library('csvreader');
        $filename = './uploads/'.$filename;
        
        $result = $csvreader->parse_file($filename);
        
        $sucess = 1;
        $error = 1;
        
        $this->db->trans_start();
        foreach($result as $res)
        {
           if(isset($res['SKU']) && isset($res['COA']) && isset($res['QTY']) && isset($res['PRICE']))
           {
              if ($this->product->valid_sku($res['SKU']) == TRUE  && $this->account->valid_coa($res['COA']) == TRUE )
              { 
                    // start transaction 

                    $id = $this->transmodel->counter();

                    $stockadj = $this->model->get_by_id($pid)->row();
                    $account = $this->account->get_id_code($res['COA']);
                    $price = floatval($res['PRICE']);
                    $product = $this->product->get_id_by_sku($res['SKU']);
                    
                    $this->stock->add_stock($product, $stockadj->dates, intval($res['QTY']), $price);

                    $pitem = array('id' => $id, 'product_id' => $this->product->get_id_by_sku($res['SKU']), 'stock_adjustment' => $pid,
                               'qty' => intval($res['QTY']), 'type' => 'in', 'price' => $res['PRICE'], 'account' => $account);

                    $this->transmodel->add($pitem);
                    
                    $sucess++;

//                    if ($this->db->trans_status() == FALSE){  return 'error|Failure Transaction...!!'; } else { return 'true|Success'; }
              }
              else{ $error++;  }
           }              
        }
        $this->db->trans_complete();
        $result = null;
        if ($sucess > 0 && $error == 0){ $result = 'true|'.$sucess.' items uploaded..!!'; }
        elseif ( $sucess == 0 && $error > 0 ) { $result = 'error| Failure Transaction..!'; }
        elseif ($sucess > 0 && $error > 0){ $result = 'warning| '.$sucess.' items uploaded & '.$error.' items error..!!'; }
        return $result;
    }
    
    function download()
    {
       $this->load->helper('download');
        
       $data = file_get_contents("uploads/sample/adjustment_sample.csv"); // Read the file's contents
       $name = 'adjustment_sample.csv';    
       force_download($name, $data);
    }
    
//    ======================  Item Transaction   ===============================================================

    function add_item($pid=null)
    {   
        $this->form_validation->set_rules('tproduct', 'Item Name', 'required|callback_valid_request['.$this->input->post('tqty').']');
        $this->form_validation->set_rules('tqty', 'Qty', 'required|numeric|is_natural_no_zero');   
//        $this->form_validation->set_rules('tamount', 'Unit Price', 'required|numeric|is_natural_no_zero');

        if ($this->form_validation->run($this) == TRUE && $this->valid_confirmation($pid) == TRUE)
        {
            $assembly = $this->model->get_by_id($pid)->row();
            $qty = $this->input->post('tqty');

            // start transaction 
            $this->db->trans_start();
            $id = $this->transmodel->counter();
            $price = $this->stock->min_stock($this->product->get_id_by_sku($this->input->post('tproduct')),
                                   $qty, $pid, 'ASM', $id);
            
            $pitem = array('id' => $id, 'product_id' => $this->product->get_id_by_sku($this->input->post('tproduct')), 'assembly' => $pid,
                           'qty' => $qty, 'price' => $price);

            $this->transmodel->add($pitem);
            $this->update_trans($pid);
            $this->db->trans_complete();
           
            if ($this->db->trans_status() == FALSE){  echo 'error|Failure Transaction...!!'; } else { echo 'true'; }
        }
        elseif ($this->valid_confirmation($pid) == FALSE){ echo "error|Invalid Transaction"; }
        else{ echo 'error|'.validation_errors(); }
    }

    function add_cost($pid=null)
    {   
        $this->form_validation->set_rules('tnotes', 'Notes', 'required');
        $this->form_validation->set_rules('tamount', 'Cost Amount', 'required|numeric|is_natural_no_zero');

        if ($this->form_validation->run($this) == TRUE && $this->valid_confirmation($pid) == TRUE)
        {
            // start transaction 
            $pitem = array('notes' => $this->input->post('tnotes'), 'assembly' => $pid, 'amount' => $this->input->post('tamount'));
            $this->costmodel->add($pitem);
            $this->update_trans($pid);
            echo 'true';
        }
        elseif ($this->valid_confirmation($pid) == FALSE){ echo "error|Invalid Transaction"; }
        else{ echo 'error|'.validation_errors(); }
    }
    
    function delete_cost($id)
    {
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){
        
        $stockitem = $this->costmodel->get_item_by_id($id);
        $stockadj = $this->model->get_by_id($stockitem->assembly)->row();
        
        if ( $this->valid_confirmation($stockitem->assembly) == TRUE ){
          $this->costmodel->delete($id); // memanggil model untuk mendelete data
          $this->update_trans($stockitem->assembly);
          echo 'true|Transaction removed..!';
        }else{ echo "warning|Journal approved, can't deleted..!"; }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    function delete_item($id)
    {
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){
        
        $stockitem = $this->transmodel->get_item_by_id($id);
        $stockadj = $this->model->get_by_id($stockitem->assembly)->row();
        
        if ( $this->valid_confirmation($stockitem->assembly) == TRUE ){
          $this->stock->rollback('ASM', $stockitem->assembly, $id);  
          $this->transmodel->delete($id); // memanggil model untuk mendelete data
          $this->update_trans($stockitem->assembly);
          echo 'true|Transaction removed..!';
        }else{ echo "warning|Journal approved, can't deleted..!"; }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }

//    ==========================================================================================

    // Fungsi update untuk mengupdate db
    function update_process($pid=null)
    {
        $this->acl->otentikasi2($this->title);
	// Form validation
        
        $this->form_validation->set_rules('tid', 'Transcode', 'required|callback_valid_confirmation|callback_valid_branch');
        $this->form_validation->set_rules('tproduct', 'Product', 'required');
        $this->form_validation->set_rules('tqty', 'Qty', 'required|numeric|is_natural_no_zero');
        $this->form_validation->set_rules('tdate', 'Invoice Date', 'required|callback_valid_period');
        $this->form_validation->set_rules('tnote', 'Note', 'required');
        $this->form_validation->set_rules('tproject', 'Project', '');
        $this->form_validation->set_rules('ccurrency', 'Currency', 'required');
        $this->form_validation->set_rules('ctax', 'Tax', 'required');
        $this->form_validation->set_rules('tdocno', 'Docno', 'required');
        $this->form_validation->set_rules('cacc', 'Account', 'required');

        if ($this->form_validation->run($this) == TRUE && $this->valid_confirmation($pid) == TRUE)
        {   
            $stock_adjustment = array('product' => $this->product->get_id_by_sku($this->input->post('tproduct')), 'qty' => $this->input->post('tqty'), 
                                      'currency' => $this->input->post('ccurrency'), 'dates' => $this->input->post('tdate'), 'branch_id' => $this->branch->get_branch_default(),
                                      'account' => $this->input->post('cacc'),
                                      'notes' => $this->input->post('tnote'),'tax' => $this->input->post('ctax'),'project' => setnull($this->input->post('tproject')),
                                      'log' => $this->session->userdata('log'));

            $this->model->update($pid, $stock_adjustment);
            $this->update_trans($pid);
            echo "true|One $this->title data successfully updated!|".$pid;
        }
        elseif ($this->valid_confirmation($pid) != TRUE){ echo "warning|Journal approved, can't deleted..!"; }
        else{ echo 'error|'.validation_errors(); }
    }
    
    private function update_trans($pid){
        
      $result = $this->model->get_by_id($pid)->row();  
      $totals_unit = $this->transmodel->total($pid);
      $totals_cost = $this->costmodel->total($pid);
      $amount = floatval($totals_unit['total'])+floatval($totals_cost['amount']);
      $tax = floatval($result->tax*$amount);
    
      $transaction = array('taxamount' => $tax, 'unitprice' => floatval($totals_unit['total']), 'costs' => floatval($totals_cost['amount']), 
                           'amount' => $amount+$tax);
      return $this->model->update($pid, $transaction);
    }
    
    public function valid_period($date=null)
    {
        $p = new Period();
        $p->get();

        $month = date('n', strtotime($date));
        $year = date('Y', strtotime($date));

        if ( intval($p->month) != intval($month) || intval($p->year) != intval($year) )
        {
            $this->form_validation->set_message('valid_period', "Invalid Period.!");
            return FALSE;
        }
        else {  return TRUE; }
    }
    
    public function valid_account($acc)
    {
        if ($this->input->post('ctype') == 'in')
        {
            if (!$acc){ $this->form_validation->set_message('valid_account', "Account Chart Required.!"); return FALSE; }
            else { return TRUE; }
        }
        else { return TRUE; }
    }
    
    public function valid_branch($uid){
        
        $res = $this->model->get_by_id($uid)->row();
        if ($this->branch->get_branch_default() != $res->branch_id){ 
           $this->form_validation->set_message('valid_branch', "Invalid Branch.!"); return FALSE; 
        }else{ return TRUE; }
    }

    public function valid_no($no)
    {
        if ($this->model->valid_no($no) == FALSE)
        {
            $this->form_validation->set_message('valid_no', "Document No already registered.!");
            return FALSE;
        }
        else {  return TRUE; }
    }
    
    function valid_request($product,$request)
    {
        $branch = $this->input->post('tbranchid');
        $pid = $this->product->get_id_by_sku($product);
        $qty = $this->stockledger->get_qty($pid, $branch, $this->period->month, $this->period->year);
        
        if ($this->input->post('ctype') == 'out'){
            if ($request > $qty){
                $this->form_validation->set_message('valid_request', "Qty Not Enough..!");
                return FALSE;
              }else{ return TRUE; }
        }else{ return TRUE; }
    }

    public function valid_opname($desc)
    {
        if ( $this->opname->cek_begindate() == FALSE )
        {
           $this->form_validation->set_message('valid_opname', "Inventory Taking Not Created...!!");
           return FALSE;
        }
        else { return TRUE; }
    }

    public function valid_confirmation($pid)
    {
        if ($pid){
            $stockin = $this->model->get_by_id($pid)->row();
            if ( $stockin->approved == 1 )
            {
               $this->form_validation->set_message('valid_confirmation', "Can't change value - transaction approved..!");
               return FALSE;
            }
            else { return TRUE; }
        }
        return FALSE;
    }

// ===================================== PRINT ===========================================
  
   function invoice($pid=null)
   {
       $this->acl->otentikasi2($this->title);
       $data['h2title'] = 'Print Invoice'.$this->modul['title'];

       $stock_adjustment = $this->model->get_by_id($pid)->row();

       $data['no'] = $stock_adjustment->id;
       $data['podate'] = tglin($stock_adjustment->dates);
       $data['docno'] = $stock_adjustment->docno;
       $data['currency'] = strtoupper($stock_adjustment->currency);
       $data['log'] = $stock_adjustment->log;
       $data['branch'] = $this->branch->get_name($stock_adjustment->branch_id);
       $data['account'] = $this->account->get_code($stock_adjustment->account).' : '.$this->account->get_name($stock_adjustment->account);
       $data['tax'] = $this->tax->get_code($stock_adjustment->tax);
       
       $data['project'] = $stock_adjustment->project;
       $data['product'] = $this->product->get_sku($stock_adjustment->product).' : '.$this->product->get_name($stock_adjustment->product);
       $data['qty'] = $stock_adjustment->qty;
       $data['unitprice'] = idr_format($stock_adjustment->unitprice);
       $data['cost'] = idr_format($stock_adjustment->costs);
       $data['taxamount'] = idr_format($stock_adjustment->taxamount);
       $data['amount'] = idr_format($stock_adjustment->amount);
       $data['notes'] = $stock_adjustment->notes;

       $data['items'] = $this->transmodel->get_last_item($pid)->result();
       $data['costitems'] = $this->costmodel->get_last_item($pid)->result();
       
       $data['accounting'] = $this->properti['accounting'];
       $data['manager'] = $this->properti['manager'];
       $data['user'] = $this->session->userdata('username');
       $this->load->view('assembly_invoice', $data);
   }

// ===================================== PRINT ===========================================

// ====================================== REPORT =========================================

    function report()
    {
        $this->acl->otentikasi2($this->title);

        $data['title'] = $this->properti['name'].' | Administrator Report '.ucwords($this->modul['title']);
        $data['h2title'] = 'Report '.$this->modul['title'];
	$data['form_action'] = site_url($this->title.'/report_process');
        $data['link'] = array('link_back' => anchor($this->title,'<span>back</span>', array('class' => 'back')));
        
        $this->load->view('stock_adjustment_report_panel', $data);
    }

    function report_process()
    {
        $this->acl->otentikasi2($this->title);
        $data['title'] = $this->properti['name'].' | Report '.ucwords($this->modul['title']);
        
        $period = $this->input->post('reservation');  
        $start = picker_between_split($period, 0);
        $end = picker_between_split($period, 1);
        $branch = $this->input->post('cbranch');
        $currency = $this->input->post('ccurrency');
        $product = $this->input->post('tproduct');
        
        $data['start'] = $start;
        $data['end'] = $end;
        $data['rundate'] = tgleng(date('Y-m-d'));
        $data['log'] = $this->session->userdata('log');
        $data['branch'] = $this->branch->get_name($branch);
        $data['currency'] = $currency;
        $data['product'] = $product.' : '. $this->product->get_name_by_sku($product);

//        Property Details
        $data['company'] = $this->properti['name'];
        $data['reports'] = $this->model->report($start,$end,$branch,$currency, $this->product->get_id_by_sku($product))->result();
        
         $this->load->view('assembly_report', $data);
    }


// ====================================== REPORT =========================================
    
       // ====================================== CLOSING ======================================
    function reset_process(){ $this->model->closing(); $this->transmodel->closing(); } 

}

?>