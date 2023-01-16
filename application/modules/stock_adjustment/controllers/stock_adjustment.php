<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Stock_adjustment extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('Stock_adjustment_model', 'model', TRUE);
        $this->load->model('Stock_adjustment_item_model', 'transmodel', TRUE);

        $this->properti = $this->property->get();
        $this->acl->otentikasi();

        $this->modul = $this->components->get(strtolower(get_class($this)));
        $this->title = strtolower(get_class($this));

        $this->currency = new Currency_lib();
        $this->load->library('unit_lib');
        $this->product = new Product_lib();
        $this->user = new Admin_lib();
        $this->wt = new Warehouse_transaction_lib();
//        $this->opname = new Opname();
        $this->journalgl = new Journalgl_lib();
        $this->account = new Account_lib();
        $this->branch = new Branch_lib();
        $this->stock = new Stock_lib();
        $this->stockledger = new Stock_ledger_lib();
        $this->period = new Period_lib();
        $this->period = $this->period->get();
        $this->csales = new Csales_lib();
    }

    private $properti, $modul, $title, $csales, $journalgl, $stock, $stockledger;
    private $user,$product,$wt,$opname,$currency,$account,$branch,$period;

    function index()
    {
         $this->get_last();
    }
    
    public function getdatatable($search=null,$dates='null',$sales='null')
    {
        if(!$search){ $result = $this->model->get_last($this->modul['limit'])->result(); }
        else{ $result = $this->model->search($dates,$sales)->result(); }
        
        if ($result){
	foreach($result as $res)
	{
	   $output[] = array ($res->id, $res->no, tglin($res->dates), $res->currency, $this->branch->get_name($res->branch_id), $res->desc, $res->staff, 
                              $this->user->get_username($res->user), $res->approved, $res->log);
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
        $data['main_view'] = 'stock_adjustment_view';
	$data['form_action'] = site_url($this->title.'/add_process');
        $data['form_action_update'] = site_url($this->title.'/update_process');
        $data['form_action_del'] = site_url($this->title.'/delete_all');
        $data['form_action_report'] = site_url($this->title.'/report_process');
        $data['link'] = array('link_back' => anchor('main/','Back', array('class' => 'btn btn-danger')));
        
        $data['branch'] = $this->branch->combo_all();
        $data['sales'] = $this->model->combo_sales();
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
        $this->table->set_heading('#', 'No', 'Code', 'Branch', 'Date', 'Currency', 'Notes', 'Staff', 'Action');

        $data['table'] = $this->table->generate();
        $data['source'] = site_url($this->title.'/getdatatable');
            
        // Load absen view dengan melewatkan var $data sbgai parameter
	$this->load->view('template', $data);
    }

    function xget_last()
    {
        $this->acl->otentikasi1($this->title);

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'stock_adjustment_view';
	$data['form_action'] = site_url($this->title.'/search');
        $data['link'] = array('link_back' => anchor('warehouse_reference/','<span>back</span>', array('class' => 'back')));
        
	$uri_segment = 3;
        $offset = $this->uri->segment($uri_segment);

	// ---------------------------------------- //
        $stock_adjustments = $this->model->get_last($this->modul['limit'], $offset)->result();
        $num_rows = $this->model->count_all_num_rows();

        if ($num_rows > 0)
        {
	    $config['base_url'] = site_url($this->title.'/get_last_stock_adjustment');
            $config['total_rows'] = $num_rows;
            $config['per_page'] = $this->modul['limit'];
            $config['uri_segment'] = $uri_segment;
            $this->pagination->initialize($config);
            $data['pagination'] = $this->pagination->create_links(); //array menampilkan link untuk pagination.
            // akhir dari config untuk pagination
            

            // library HTML table untuk membuat template table class zebra
            $tmpl = array('table_open' => '<table cellpadding="2" cellspacing="1" class="tablemaster">');

            $this->table->set_template($tmpl);
            $this->table->set_empty("&nbsp;");

            //Set heading untuk table
            $this->table->set_heading('No', 'Code', 'Date', 'Currency', 'Notes', 'Staff', 'Log', 'Action');

            $i = 0 + $offset;
            foreach ($stock_adjustments as $stock_adjustment)
            {
                $datax = array('name'=> 'cek[]','id'=> 'cek'.$i,'value'=> $stock_adjustment->id,'checked'=> FALSE, 'style'=> 'margin:0px');
                
                $this->table->add_row
                (
                    ++$i, 'IAJ-00'.$stock_adjustment->no, tglin($stock_adjustment->dates), $stock_adjustment->currency, $stock_adjustment->desc, $stock_adjustment->staff, $stock_adjustment->log,
                    anchor($this->title.'/confirmation/'.$stock_adjustment->id,'<span>update</span>',array('class' => $this->post_status($stock_adjustment->approved), 'title' => 'edit / update')).' '.
                    anchor_popup($this->title.'/print_invoice/'.$stock_adjustment->no,'<span>print</span>',$this->atts).' '.
                    anchor($this->title.'/add_trans/'.$stock_adjustment->no,'<span>details</span>',array('class' => 'update', 'title' => '')).' '.
                    anchor($this->title.'/delete/'.$stock_adjustment->id.'/'.$stock_adjustment->no,'<span>delete</span>',array('class'=> 'delete', 'title' => 'delete' ,'onclick'=>"return confirm('Are you sure you will delete this data?')"))
                );
            }

            $data['table'] = $this->table->generate();
        }
        else
        {
            $data['message'] = "No $this->title data was found!";
        }

        // Load absen view dengan melewatkan var $data sbgai parameter
	$this->load->view('template', $data);
    }

    function search()
    {
        $this->acl->otentikasi1($this->title);

        $data['title'] = $this->properti['name'].' | Administrator Find '.ucwords($this->modul['title']);
        $data['h2title'] = 'Find '.$this->modul['title'];
        $data['main_view'] = 'stock_adjustment_view';
	$data['form_action'] = site_url($this->title.'/search');
        $data['link'] = array('link_back' => anchor($this->title,'<span>back</span>', array('class' => 'back')));

        $stock_adjustments = $this->model->search($this->input->post('tno'), $this->input->post('tdate'))->result();
        
        $tmpl = array('table_open' => '<table cellpadding="2" cellspacing="1" class="tablemaster">');

        $this->table->set_template($tmpl);
        $this->table->set_empty("&nbsp;");

        //Set heading untuk table
        $this->table->set_heading('No', 'Code', 'Date', 'Currency', 'Notes', 'Staff', 'Log', 'Action');

        $i = 0;
        foreach ($stock_adjustments as $stock_adjustment)
        {
            $datax = array('name'=> 'cek[]','id'=> 'cek'.$i,'value'=> $stock_adjustment->id,'checked'=> FALSE, 'style'=> 'margin:0px');

            $this->table->add_row
            (
                ++$i, 'IAJ-00'.$stock_adjustment->no, tglin($stock_adjustment->dates), $stock_adjustment->currency, $stock_adjustment->desc, $stock_adjustment->staff, $stock_adjustment->log,
                anchor($this->title.'/confirmation/'.$stock_adjustment->id,'<span>update</span>',array('class' => $this->post_status($stock_adjustment->approved), 'title' => 'edit / update')).' '.
                anchor_popup($this->title.'/print_invoice/'.$stock_adjustment->no,'<span>print</span>',$this->atts).' '.
                anchor($this->title.'/add_trans/'.$stock_adjustment->no,'<span>details</span>',array('class' => 'update', 'title' => '')).' '.
                anchor($this->title.'/delete/'.$stock_adjustment->id.'/'.$stock_adjustment->no,'<span>delete</span>',array('class'=> 'delete', 'title' => 'delete' ,'onclick'=>"return confirm('Are you sure you will delete this data?')"))
            );
        }

        $data['table'] = $this->table->generate();
        $this->load->view('template', $data);
    }


    function get_list()
    {
        $this->acl->otentikasi1($this->title);

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'product_list';
        $data['form_action'] = site_url($this->title.'/get_list');

        $stocks = $this->model->get_list($this->input->post('tno'))->result();

        $tmpl = array('table_open' => '<table cellpadding="2" cellspacing="1" class="tablemaster">');

        $this->table->set_template($tmpl);
        $this->table->set_empty("&nbsp;");

        //Set heading untuk table
        $this->table->set_heading('No', 'Code', 'Date', 'Notes', 'Staff', 'Action');

        $i = 0;
        foreach ($stocks as $stock)
        {
          $datax = array('name' => 'button', 'type' => 'button', 'content' => 'Select', 'onclick' => 'setvalue(\''.$stock->no.'\',\'tbpbg\')');
          $this->table->add_row( ++$i, 'IAJ-00'.$stock->no, tgleng($stock->dates), $stock->desc, $stock->staff, form_button($datax) );
        }

        $data['table'] = $this->table->generate();
        $this->load->view('stock_adjustment_list', $data);
        
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
        elseif ($this->valid_period($stock_adjustment->dates) != TRUE){ echo "error|Invalid Period..!"; }
        else
        {
            // start transaction 
            $this->db->trans_start();
           
            $data = array('approved' => 1);
            $this->model->update($pid, $data);
           
           // create journal
           $balancein = $this->transmodel->total_criteria($stock_adjustment->id,'in');
           $balanceout = $this->transmodel->total_criteria($stock_adjustment->id,'out');
           $this->create_journal($pid, $stock_adjustment->branch_id, $stock_adjustment->dates, 'IDR', 'IAJ-00'.$stock_adjustment->no, $stock_adjustment->no, $balancein, $balanceout); // create journal

           // add wt
           $this->add_warehouse_transaction($stock_adjustment->id);
           $this->db->trans_complete();
           
           if ($this->db->trans_status() === FALSE){ echo "error|IAJ-00$stock_adjustment->no failed confirmed..!";  }
           else { echo "true|IAJ-00$stock_adjustment->no confirmed..!"; }
        }

    }
    
    private function create_journal($pid,$branch,$date,$currency,$code,$no,$amountin,$amountout)
    {
        $itemin = $this->transmodel->get_last_item($pid,'in')->result();
        $itemout = $this->transmodel->get_last_item($pid,'out')->result();
        
        $cm = new Control_model();
        
        $stock   = $this->branch->get_acc($branch, 'stock'); // stock
        
        $this->journalgl->new_journal('00'.$no,$date,'IJ',$currency,$code,intval($amountin+$amountout), $this->session->userdata('log'));
        $jid = $this->journalgl->get_journal_id('IJ','00'.$no);
        
        if ($amountin > 0)
        {
            foreach ($itemin as $res)
            {
               $this->journalgl->add_trans($jid, $res->account, 0, intval($res->qty*$res->price)); // income bertambah 
            } 
            $this->journalgl->add_trans($jid,$stock, $amountin, 0); // tambah persediaan terkait dengan cabang
        }
        
        if ($amountout > 0)
        {
            foreach ($itemout as $res) {
               $this->journalgl->add_trans($jid,$res->account, intval($res->qty*$res->price), 0); // tambah biaya      
            }
           $this->journalgl->add_trans($jid,$stock, 0, $amountout); // kurang persediaan terkait dengan cabang
        }  
    }

    private function add_warehouse_transaction($pid)
    {
        $val  = $this->model->get_by_id($pid)->row();
        $list = $this->transmodel->get_last_item($pid)->result();

        foreach ($list as $value)
        {
           if ($value->type == 'out')
           {
                $this->wt->add( $val->dates, 'IAJ-00'.$val->no, $val->branch_id, $val->currency, $value->product_id, 0, $value->qty,
                           $value->price, $value->price*$value->qty,
                           $this->session->userdata('log'));
           }
           else
           {
                $this->wt->add($val->dates, 'IAJ-00'.$val->no, $val->branch_id, $val->currency, $value->product_id, $value->qty, 0,
                           $value->price, $value->price*$value->qty,
                           $this->session->userdata('log'));
           }
        }
    }

    private function del_warehouse_transaction($po=0)
    {
        $val  = $this->model->get_stock_adjustment_by_no($po)->row();
        $this->wt->remove($val->dates, 'IAJ-00'.$po);        
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
        $val = $this->model->get_by_id($uid)->row();

        if ( $val->approved == 1 ){ $this->rollback($uid,$val->no); }
        else{ $this->remove($uid,$val->no);}

        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    private function rollback($uid,$po)
    {
       $this->db->trans_start(); 
       $this->journalgl->remove_journal('IJ', '00'.$po); // journal gl  
       $this->del_warehouse_transaction($po); 
       $data = array('approved' => 0);
       $this->model->update($uid, $data);
       $this->db->trans_complete();
       
       if ($this->db->trans_status() === FALSE)
       {
         echo "warning|1 $this->title canceled rollback..!";
       }
       else{ echo "true|1 $this->title successfully rollback..!"; }
       
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
           if ($res->type == 'out'){ $this->stock->rollback('SA', $stockitem->stock_adjustment, $res->id);   }
           elseif ($res->type == 'in') { 
               $this->stock->increase_stock($res->product_id, $stockadj->dates, $res->qty); 
           }
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
        $data['form_action_import'] = site_url($this->title.'/import/0/');
        
        $data['currency'] = $this->currency->combo();
        $data['code'] = $this->model->counter();
        $data['pid'] = null;
        $data['user'] = $this->session->userdata("username");
        $data['branch'] = $this->branch->combo();
        $data['sales'] = $this->csales->combo();
        
        $data['main_view'] = 'stock_adjustment_form';
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
        $this->form_validation->set_rules('tno', 'IAJ - No', 'required|numeric|callback_valid_no');
        $this->form_validation->set_rules('tdate', 'Invoice Date', 'required');
        $this->form_validation->set_rules('tnote', 'Note', 'required');
        $this->form_validation->set_rules('tstaff', 'Workshop Staff', 'required');
        $this->form_validation->set_rules('ccurrency', 'Currency', 'required');
        $this->form_validation->set_rules('cbranch', 'Branch / Outlet', 'required');
//        $this->form_validation->set_rules('cbranch', 'Branch / Outlet', 'required');

        if ($this->form_validation->run($this) == TRUE)
        {
            $stock_adjustment = array('no' => $this->input->post('tno'), 'sales_id' => setnull($this->input->post('csales')), 'approved' => 0, 'staff' => $this->input->post('tstaff'), 
                                      'currency' => $this->input->post('ccurrency'), 'dates' => $this->input->post('tdate'), 'branch_id' => $this->input->post('cbranch'),
                                      'desc' => $this->input->post('tnote'), 'user' => $this->user->get_id($this->session->userdata('username')),
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
        $data['form_action_import'] = site_url($this->title.'/import/'.$id);
        
        $data['currency'] = $this->currency->combo();
        $data['branch'] = $this->branch->combo();
        $data['code'] = $cash->no;
        $data['user'] = $this->session->userdata("username");
        $data['sales'] = $this->csales->combo();
        $data['pid'] = $id;
        
        $data['main_view'] = 'stock_adjustment_form';
        $data['source'] = site_url($this->title.'/getdatatable');
        $data['link'] = array('link_back' => anchor($this->title,'Back', array('class' => 'btn btn-danger')));
        
        $data['default']['dates'] = $cash->dates;
        $data['default']['staff'] = $cash->staff;
        $data['default']['currency'] = $cash->currency;
        $data['default']['note'] = $cash->desc;
        $data['default']['branch'] = $cash->branch_id;
        $data['default']['sales'] = $cash->sales_id;
        
        $data['items'] = $this->transmodel->get_last_item($id)->result();
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
        $this->form_validation->set_rules('tqty', 'Qty', 'required|numeric');   
        $this->form_validation->set_rules('titem', 'Account', 'callback_valid_account');

        if ($this->form_validation->run($this) == TRUE && $this->valid_confirmation($pid) == TRUE)
        {
            $stockadj = $this->model->get_by_id($pid)->row();
            
            $type = $this->input->post('ctype');
            $qty = $this->input->post('tqty');

            // start transaction 
            $this->db->trans_start();
            $id = $this->transmodel->counter();
            
            if ($type == 'out')
            {
               $account = $this->account->get_id_code($this->input->post('titem'));
               $price = $this->stock->min_stock($this->product->get_id_by_sku($this->input->post('tproduct')),
                                   $qty, $pid, 'SA', $id);
            }
            elseif ($type == 'in')
            {
                $account = $this->account->get_id_code($this->input->post('titem'));
                $price = $this->input->post('tamount');
                $this->stock->add_stock($this->product->get_id_by_sku($this->input->post('tproduct')), $stockadj->dates, $qty, $this->input->post('tamount'));
            }
            
            $pitem = array('id' => $id, 'product_id' => $this->product->get_id_by_sku($this->input->post('tproduct')), 'stock_adjustment' => $pid,
                           'qty' => $qty, 'type' => $type, 'price' => $price, 'account' => $account,
                           'sales_id' => $this->input->post('csales'), 'notes' => strtolower($this->input->post('tdesc'))
                          );

            $this->transmodel->add($pitem);
            $this->db->trans_complete();
           
            if ($this->db->trans_status() == FALSE){  echo 'error|Failure Transaction...!!'; } else { echo 'true'; }
        }
        else{ echo 'error|'.validation_errors(); }
    }

    function delete_item($id)
    {
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){
        
        $stockitem = $this->transmodel->get_item_by_id($id);
        $stockadj = $this->model->get_by_id($stockitem->stock_adjustment)->row();
        
        if ( $this->valid_confirmation($stockitem->stock_adjustment) == TRUE ){
          
        if ($stockitem->type == 'out'){ 
        
            $this->stock->rollback('SA', $stockitem->stock_adjustment, $id);
        }
        elseif ($stockitem->type == 'in'){ 
            $this->stock->increase_stock($stockitem->product_id,$stockadj->dates,$stockitem->qty); 
        }
            
        $this->transmodel->delete($id); // memanggil model untuk mendelete data
        $this->session->set_flashdata('message', "1 item successfully removed..!"); // set flash data message dengan session 
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
        $this->form_validation->set_rules('tid', 'IAJ - No', 'required|callback_valid_confirmation');
        $this->form_validation->set_rules('tno', 'IAJ - No', 'required|numeric');
        $this->form_validation->set_rules('tdate', 'Invoice Date', 'required');
        $this->form_validation->set_rules('tnote', 'Note', 'required');
        $this->form_validation->set_rules('tstaff', 'Workshop Staff', 'required');
        $this->form_validation->set_rules('cbranch', 'Branch / Outlet', 'required');

        if ($this->form_validation->run($this) == TRUE && $this->valid_confirmation($pid) == TRUE)
        {   
            $stock_adjustment = array('staff' => $this->input->post('tstaff'), 
                                      'sales_id' => setnull($this->input->post('csales')),
                                      'currency' => $this->input->post('ccurrency'), 'dates' => $this->input->post('tdate'), 'branch_id' => $this->input->post('cbranch'),
                                      'desc' => $this->input->post('tnote'), 'user' => $this->user->get_id($this->session->userdata('username')),
                                      'log' => $this->session->userdata('log'));

            $this->model->update($pid, $stock_adjustment);
            echo "true|One $this->title data successfully updated!|".$pid;
        }
        elseif ($this->valid_confirmation($pid) != TRUE){ echo "warning|Journal approved, can't deleted..!"; }
        else{ echo 'error|'.validation_errors(); }
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

    public function valid_no($no)
    {
        if ($this->model->valid_no($no) == FALSE)
        {
            $this->form_validation->set_message('valid_no', "Order No already registered.!");
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
        $stockin = $this->model->get_by_id($pid)->row();

        if ( $stockin->approved == 1 )
        {
           $this->form_validation->set_message('valid_confirmation', "Can't change value - transaction approved..!");
           return FALSE;
        }
        else { return TRUE; }
    }

// ===================================== PRINT ===========================================
  
   function invoice($pid=null,$type=0)
   {
       $this->acl->otentikasi2($this->title);

       $data['h2title'] = 'Print Invoice'.$this->modul['title'];

       if ($type == 0){ $stock_adjustment = $this->model->get_by_id($pid)->row(); } 
       else{ $stock_adjustment = $this->model->get_stock_adjustment_by_no($pid)->row(); }
       

       $data['no'] = $stock_adjustment->no;
       $data['podate'] = tglin($stock_adjustment->dates);
       $data['user'] = $this->user->get_username($stock_adjustment->user);
       $data['staff'] = $stock_adjustment->staff;
       if ($stock_adjustment->sales_id != NULL){ $data['sales'] = 'SO-0'.$stock_adjustment->sales_id;}else{ $data['sales'] = '-'; }
       $data['log'] = $this->session->userdata('log');
       $data['branch'] = $this->branch->get_name($stock_adjustment->branch_id);
       
        // property display
       $data['company'] = $this->properti['name'];
       $data['address'] = $this->properti['address'];
       $data['phone1'] = $this->properti['phone1'];
       $data['phone2'] = $this->properti['phone2'];
       $data['city'] = ucfirst($this->properti['city']);
       $data['zip'] = $this->properti['zip'];
       $data['website'] = $this->properti['sitename'];
       $data['email'] = $this->properti['email'];

       $data['items'] = $this->transmodel->get_last_item($stock_adjustment->id)->result();

       $this->load->view('stock_adjustment_invoice', $data);
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
        
        $data['start'] = $start;
        $data['end'] = $end;
        $data['rundate'] = tgleng(date('Y-m-d'));
        $data['log'] = $this->session->userdata('log');

//        Property Details
        $data['company'] = $this->properti['name'];

        $data['reports'] = $this->model->report($start,$end)->result();
        $data['reports_category'] = $this->model->report_category($start,$end)->result();
        
        if ($this->input->post('ctype') == 0){ $this->load->view('stock_adjustment_report_category', $data);}
        else { $this->load->view('stock_adjustment_report_details', $data); }
    }


// ====================================== REPORT =========================================
    
       // ====================================== CLOSING ======================================
    function reset_process(){ $this->model->closing(); $this->transmodel->closing(); } 

}

?>