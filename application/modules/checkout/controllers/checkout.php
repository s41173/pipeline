<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Checkout extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('Checkout_model', 'model', TRUE);

        $this->properti = $this->property->get();
        $this->acl->otentikasi();

        $this->modul = $this->components->get(strtolower(get_class($this)));
        $this->title = strtolower(get_class($this));

        $this->bank = new Bank_lib();
        $this->vendor = new Vendor_lib();
        $this->account = new Account_lib();
        $this->ap_payment = new Ap_payment_lib();
        $this->ar_payment = new Ar_payment_lib();
        $this->journal = new Journalgl_lib();
        $this->currency = new Currency_lib();
    }

    private $properti, $modul, $title, $journal, $currency;
    private $bank,$vendor,$account,$ap_payment,$ar_payment;

    function index()
    { $this->get_last(); }
    
    
    public function getdatatable($search=null,$class='null',$publish='null')
    {
        if(!$search){ $result = null; }
        else {$result = $this->Model->search($class,$publish)->result(); }
        
        if ($result){
	foreach($result as $res)
	{  
	   $output[] = array ($res->id, $this->classification->get_name($res->classification_id), $this->classification->get_type($res->classification_id), $res->currency, $res->code, $res->name, $res->alias, $res->acc_no,
                              $res->bank, $res->status, $res->default, $res->bank_stts);
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
        $data['main_view'] = 'check_view';
	$data['form_action'] = site_url($this->title.'/search');
        $data['form_action_report'] = site_url($this->title.'/report_process');
        $data['link'] = array('link_back' => anchor('main/','Back', array('class' => 'btn btn-danger')));
        
        $data['currency'] = $this->currency->combo();
	// ---------------------------------------- //

        // library HTML table untuk membuat template table class zebra
        $tmpl = array('table_open' => '<table id="xdatatable-buttons" class="table table-striped table-bordered">');

        $this->table->set_template($tmpl);
        $this->table->set_empty("&nbsp;");

        //Set heading untuk table
        $this->table->set_heading('#', 'No', 'Cur', 'Check - No', 'Payment', 'Bank', 'Dates', 'Due', 'Amount', 'Action');

        $data['table'] = $this->table->generate();
        $data['source'] = site_url($this->title.'/getdatatable');
            
        // Load absen view dengan melewatkan var $data sbgai parameter
	$this->load->view('template', $data);
    }

    private function code($val)
    {
        if ($val == 'purchase') { $val = 'CD-00'; } elseif ($val == 'ap') { $val = 'DJ-00'; }
        elseif ($val == 'ar') { $val = 'DJ-000'; }
        elseif ($val == 'ar_refund') { $val = 'RF-00'; } elseif ($val == 'nar_refund') { $val = 'NRF-00'; }
        return $val;
    }

    function search()
    {
        $this->acl->otentikasi1($this->title);

        $data['title'] = $this->properti['name'].' | Administrator Find '.ucwords($this->modul['title']);
        $data['h2title'] = 'Find '.$this->modul['title'];
        $data['main_view'] = 'check_view';
	$data['form_action'] = site_url($this->title.'/search');
        $data['form_action_report'] = site_url($this->title.'/report_process');
        $data['link'] = array('link_back' => anchor($this->title,'Back', array('class' => 'btn btn-danger')));
        $data['currency'] = $this->currency->combo();
        
        $period = $this->input->post('reservation');  
        $start = picker_between_split($period, 0);
        $end = picker_between_split($period, 1);
        
        $aps = $this->model->search($this->input->post('tno'), $start, $end, $this->input->post('ctype'))->result();
        $code = $this->code($this->input->post('ctype'));
        
        $tmpl = array('table_open' => '<table id="xdatatable-buttons" class="table table-striped table-bordered">');
        $this->table->set_template($tmpl);
        $this->table->set_empty("&nbsp;");

        //Set heading untuk table
        $this->table->set_heading('No', 'Cur', 'Check - No', 'Payment', 'Bank', 'Dates', 'Due', 'Amount', 'Action');

        $i = 0;
        foreach ($aps as $ap)
        {

            $this->table->add_row
            (
                ++$i, strtoupper($ap->currency), $ap->check_no, $code.$ap->no, $this->account->get_code($ap->account).'-'.$this->account->get_name($ap->account), tglin($ap->dates), tglin($ap->due), number_format($ap->amount),
                anchor($this->title.'/process/'.$ap->no.'/'.$this->input->post('ctype').'/'.$ap->amount.'/'.$ap->account.'/'.$ap->due.'/'.$ap->currency,'<i class="fa fas-2x fa-book"> </i>',array('class' => $this->alert_date($ap->due), 'id' => 'bprocess', 'title' => 'edit / update'))
//                anchor($this->title.'/details/'.$ap->no,'<span>details</span>',array('class' => 'update', 'title' => ''))
            );
        }

        $data['table'] = $this->table->generate();
        $data['source'] = site_url($this->title.'/getdatatable');
        
        $this->load->view('template', $data);
    }
    
    function process($no,$type,$amount=0,$acc,$due,$cur)
    {
       $code = '00';
       $data = array('post_dated_stts' => 1);
       
       if ($type == 'purchase'){ $this->ap_payment->set_post_stts($no, $data); $notes = 'AP-Payment : CD-00'.$no; }
       elseif ($type == 'ap'){ $this->ap_payment_cash->set_post_stts($no, $data); $notes = 'AP : DJ-00'.$no; }
       elseif ($type == 'ar'){ $this->ar_payment->set_post_stts($no, $data); $notes = 'AR : CR-0'.$no; $code = '000'; }
       
       if ($this->journal->cek_journal($code.$no, 'GJ', $due, strtoupper($cur)) == TRUE){
       
       $cm = new Control_model();
       $ap       = $cm->get_id(35); // hutang giro
       $ar       = $cm->get_id(61); // piutang giro
       $account  = $acc;                
        // create journal- GL

       $this->journal->new_journal($code.$no,$due,'GJ', strtoupper($cur),'Cheque Process '.$notes,$amount, $this->session->userdata('log'));
       $dpid = $this->journal->get_journal_id('GJ',$code.$no);
       
       if ($type='ar'){
          $this->journal->add_trans($dpid,$account,$amount,0); // kas
          $this->journal->add_trans($dpid,$ar,0,$amount); // piutang giro
       }else{ $this->journal->add_trans($dpid,$account,0,$amount); // kas
              $this->journal->add_trans($dpid,$ap,$amount,0); // hutang giro
       }
       
       
       $this->session->set_flashdata('message', "Checkout journal created..!");
       } else{ $this->session->set_flashdata('message', "Journal already processed..!"); }
       redirect($this->title);
    }

    private function alert_date($due)
    {
        $due = strtotime($due);
        $now = strtotime(date('Y-m-d'));
        $res = null;
        if ($now > $due) { $res = "btn btn-success btn-xs"; } else { $res = "btn btn-danger btn-xs"; } return $res;
    }

    function report()
    {
        $this->acl->otentikasi2($this->title);

        $data['title'] = $this->properti['name'].' | Administrator Report '.ucwords($this->modul['title']);
        $data['h2title'] = 'Report '.$this->modul['title'];
	$data['form_action'] = site_url($this->title.'/report_process');
        $data['link'] = array('link_back' => anchor($this->title,'<span>back</span>', array('class' => 'back')));

        $this->load->view('checkout_report_panel', $data);
    }

    function report_process()
    {
        $this->acl->otentikasi2($this->title);
        $data['title'] = $this->properti['name'].' | Report '.ucwords($this->modul['title']);

        $type = $this->input->post('ctype');        
        $period = $this->input->post('reservation');  
        $start = picker_between_split($period, 0);
        $end = picker_between_split($period, 1);

        $data['start'] = $start;
        $data['end'] = $end;
        $data['rundate'] = tgleng(date('Y-m-d'));
        $data['log'] = $this->session->userdata('log');
        $data['type'] = $type;

//        Property Details
        $data['company'] = $this->properti['name'];

        $data['reports'] = $this->model->report($start,$end,$type)->result();

        $data['total'] = 0;
        $data['tax'] = 0;
        $data['p1'] = 0;
        $data['p2'] = 0;
        $data['costs'] = 0;
        $data['ptotal'] = 0;

        $this->load->view('checkout_report', $data);

    }
    
                // ====================================== CLOSING ======================================
    function reset_process(){  } 
   
}

?>