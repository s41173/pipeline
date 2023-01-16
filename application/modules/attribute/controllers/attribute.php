<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Attribute extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('Attribute_model', 'model', TRUE);

        $this->properti = $this->property->get();
        $this->acl->otentikasi();

        $this->modul = $this->components->get(strtolower(get_class($this)));
        $this->title = strtolower(get_class($this));
        $this->category = new Categoryproduct_lib();
        $this->attribute = new Attribute_list_lib();
    }

    private $properti, $modul, $title;
    private $category, $attribute;

    function index()
    {
       $this->get_last(); 
    }
     
    public function getdatatable($search=null)
    {
        if(!$search){ $result = $this->model->get_last($this->modul['limit'])->result(); }
	
        $output = null;
        if ($result){
                
         foreach($result as $res)
	 {
	   $output[] = array ($res->id, $this->category->get_name($res->category_id), $this->attribute->get_name($res->attribute_list_id), $res->orders,
                              $res->created, $res->updated, $res->deleted
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

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'attribute_view';
	$data['form_action'] = site_url($this->title.'/add_process');
        $data['form_action_update'] = site_url($this->title.'/update_process');
        $data['form_action_del'] = site_url($this->title.'/delete_all');
        $data['link'] = array('link_back' => anchor('main/','Back', array('class' => 'btn btn-danger')));

        $data['category'] = $this->category->combo();
        $data['attribute'] = $this->attribute->combo();
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
        $this->table->set_heading('#','No', 'Category', 'Attribute', 'Order', 'Action');

        $data['table'] = $this->table->generate();
        $data['source'] = site_url($this->title.'/getdatatable');
            
        // Load absen view dengan melewatkan var $data sbgai parameter
	$this->load->view('template', $data);
    }
    
    function delete_all()
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
             $this->model->delete($cek[$i]);
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
            $this->model->delete($uid);
            $this->session->set_flashdata('message', "1 $this->title successfully removed..!");

            echo "true|1 $this->title successfully removed..!";
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
        
    }

    function add_process()
    {
        if ($this->acl->otentikasi_admin($this->title,'ajax') == TRUE){

            $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
            $data['h2title'] = $this->modul['title'];
            $data['main_view'] = 'admin_view';
            $data['form_action'] = site_url($this->title.'/add_process');
            $data['link'] = array('link_back' => anchor('admin/','<span>back</span>', array('class' => 'back')));

            // Form validation
            $this->form_validation->set_rules('ccategory', 'Category', 'required|callback_valid['.$this->input->post('cattribute').']');
            $this->form_validation->set_rules('cattribute', 'Attribute Group', 'required');
            $this->form_validation->set_rules('torder', 'Attribute Order', 'required|numeric');

            if ($this->form_validation->run($this) == TRUE)
            {//
                $attribute = array('category_id' => $this->input->post('ccategory'),'attribute_list_id' => $this->input->post('cattribute'),
                                   'orders' => $this->input->post('torder'), 'created' => date('Y-m-d H:i:s'));

                $this->model->add($attribute);
                $this->session->set_flashdata('message', "One $this->title data successfully saved!");
                echo 'true|Data successfully saved..!';
            }
            else
            {
    //            $this->load->view('template', $data);
    //            echo validation_errors();
                echo 'warning|'.validation_errors();
            }
        }
        else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }

    }

    // Fungsi update untuk menset texfield dengan nilai dari database
    function update($uid=null)
    {        
        $attribute = $this->model->get_by_id($uid)->row();
               
	$this->session->set_userdata('langid', $attribute->id);
        
        echo $uid.'|'.$attribute->category_id.'|'.$attribute->attribute_list_id.'|'.$attribute->orders;
    }


    function valid($cat,$attr)
    {   
        if ($this->model->valid_attribute($cat,$attr) == FALSE)
        {
            $this->form_validation->set_message('valid', 'This '.$this->title.' is already registered.!');
            return FALSE;
        }
        else{ return TRUE; }
    }

    function validation($cat,$attr)
    {
	$id = $this->session->userdata('langid');
	if ($this->model->validation_attribute($cat,$attr,$id) == FALSE)
        {
            $this->form_validation->set_message('validation', 'This '.$this->title.' is already registered!');
            return FALSE;
        }
        else{ return TRUE; }
    }

    // Fungsi update untuk mengupdate db
    function update_process()
    {
        if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'admin_update';
	$data['form_action'] = site_url($this->title.'/update_process');
	$data['link'] = array('link_back' => anchor('admin/','<span>back</span>', array('class' => 'back')));

	// Form validation
        $this->form_validation->set_rules('ccategory', 'Category', 'required|callback_validation['.$this->input->post('cattribute').']');
        $this->form_validation->set_rules('cattribute', 'Attribute Group', 'required');
        $this->form_validation->set_rules('torder', 'Attribute Order', 'required|numeric');

        if ($this->form_validation->run($this) == TRUE)
        {  
            $attribute = array('category_id' => $this->input->post('ccategory'),'attribute_list_id' => $this->input->post('cattribute'),
                                'orders' => $this->input->post('torder'));
            
            
	    $this->model->update($this->session->userdata('langid'), $attribute);
            $this->session->set_flashdata('message', "One $this->title has successfully updated!");
          //  $this->session->unset_userdata('langid');
            echo "true|One $this->title has successfully updated..!";

        }
        else{ echo 'warning|'.validation_errors(); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
        // ====================================== CLOSING ======================================
    function reset_process(){ $this->model->closing(); } 

}

?>