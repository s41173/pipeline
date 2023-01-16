<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Product extends MX_Controller
{
    function __construct()
    {
        parent::__construct();
        
        $this->load->model('Product_model', 'model', TRUE);

        $this->properti = $this->property->get();
        $this->acl->otentikasi();

        $this->modul = $this->components->get(strtolower(get_class($this)));
        $this->title = strtolower(get_class($this));
        $this->role = new Role_lib();
        $this->category = new Categoryproduct_lib();
        $this->manufacture = new Manufacture_lib();
        $this->attribute = new Attribute_lib();
        $this->attribute_product = new Attribute_product_lib();
        $this->attribute_list = new Attribute_list_lib();
        $this->currency = new Currency_lib();
        $this->product = new Product_lib();
        $this->wt = new Warehouse_transaction_lib();
        $this->branch = new Branch_lib();
        $this->conversi = new Conversion_lib();
        $this->period = new Period_lib();
        $this->period = $this->period->get();
        $this->stockledger = new Stock_ledger_lib();
        $this->stock = new Stock_lib();
        $this->unit = new Unit_lib();
    }

    private $properti, $modul, $title, $product, $wt, $branch, $conversi, $period, $stockledger, $stock;
    private $role, $category, $manufacture, $attribute, $attribute_product, $attribute_list, $currency, $unit;

    
    function index()
    {
       $this->session->unset_userdata('start'); 
       $this->session->unset_userdata('end');
       $this->get_last(); 
        
//       $first_day_this_month = date('m-01-Y'); // hard-coded '01' for first day
//       $last_day_this_month  = date('m-t-Y');
       
//       $last_day_april_2010 = date('m-t-Y', strtotime('April 21, 2010'));
//       echo $last_day_april_2010;
    }
    
    // ajax
    function set_param(){
        
        $this->session->set_userdata('category', $this->input->post('category'));
        $this->session->set_userdata('size', $this->input->post('size'));
        $this->session->set_userdata('color', $this->input->post('color'));
        $this->session->set_userdata('publish', $this->input->post('publish'));
        $this->session->set_userdata('type', $this->input->post('type'));
        
        echo 'true|Parameter Set...!!';
    }
     
    public function getdatatable($search=null,$branch='null',$cat='null',$col='null',$size='null',$publish='null',$sku='null',$type='null')
    {
        if ($branch == 'null'){ $branch = $this->branch->get_branch(); }
        if(!$search){ $result = $this->model->get_last($this->modul['limit'])->result(); }
        else {
            if ($sku != 'null'){ $result = $this->model->search_sku($sku)->result();  }
            else{ $result = $this->model->search($cat,$col,$size,$publish,$type)->result();  } 
        }
	
        $output = null;
        if ($result){
          
         foreach($result as $res)
	 { 
           $qty = $this->stockledger->get_qty($res->id, $branch, $this->period->month, $this->period->year);  
	   $output[] = array ($res->id, $this->category->get_name($res->category), base_url().'images/product/'.$res->image, $res->sku, $res->name, $res->model,
                              idr_format($res->price), $qty, $res->publish, $res->category, idr_format($res->price-$res->discount), $res->color, $res->size, $this->branch->get_name($branch), $branch
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

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords('Product Manager');
        $data['h2title'] = 'Product Manager';
        $data['main_view'] = 'product_view';
	$data['form_action'] = site_url($this->title.'/add_process');
        $data['form_action_update'] = site_url($this->title.'/update_process');
        $data['form_action_del'] = site_url($this->title.'/delete_all');
        $data['form_action_report'] = site_url($this->title.'/report_process');
        $data['form_action_import'] = site_url($this->title.'/import');
        $data['link'] = array('link_back' => anchor('main/','Back', array('class' => 'btn btn-danger')));

        $data['category'] = $this->category->combo_code();
        $data['manufacture'] = $this->manufacture->combo_all();
        $data['color'] = $this->attribute->combo_color();
        $data['size'] = $this->attribute->combo_size();
        $data['model'] = $this->product->combo_model();
        $data['currency'] = $this->currency->combo();
        $data['branch'] = $this->branch->combo();
        $data['branch_all'] = $this->branch->combo_all();
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
        $this->table->set_heading('#','No', 'Branch', 'Category', 'SKU', 'Name', 'Model', 'C - S', 'Qty', 'Action');

        $data['table'] = $this->table->generate();
        $data['source'] = site_url($this->title.'/getdatatable');
        $data['graph'] = site_url()."/product/chart/";
            
        // Load absen view dengan melewatkan var $data sbgai parameter
	$this->load->view('template', $data);
    }
    
    function chart()
    {
        $data = $this->category->get();
        $datax = array();
        $branch = $this->branch->get_branch_session();
        
        foreach ($data as $res) 
        {  
           $point = array("label" => $res->name , "y" => $this->product->get_product_based_category($res->id, $branch, $this->period->month, $this->period->year));
           array_push($datax, $point);      
        }
        echo json_encode($datax, JSON_NUMERIC_CHECK);
    }
    
    function get_list($target='titem',$branchid=null,$type=0)
    {
        $this->acl->otentikasi1($this->title);

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'product_list';
        $data['form_action'] = site_url($this->title.'/get_list');
        $data['category'] = $this->category->combo_all();
        $data['manufacture'] = $this->manufacture->combo_all();
        $data['branch'] = $this->branch->combo();
        $data['model'] = $this->product->combo_model();

        if ($branchid){ $branch = $branchid; }
        elseif (!$this->input->post('cbranch')){ $branch = $this->branch->get_branch_session(); }
        else{ $branch = $this->input->post('cbranch'); }
        
        
        $currency = $this->input->post('ccurrency');
        $brand = $this->input->post('cmanufacture');
        $category = $this->input->post('ccategory');
        $model = $this->input->post('cmodel');
        
        if ($category){
          $products = $this->model->search_list($category,$brand,$model,$currency,$type)->result();              
        }else{ $products = null; }
        
        $qty = 0;

        $tmpl = array('table_open' => '<table id="example" width="100%" cellspacing="0" class="table table-striped table-bordered">');

            $this->table->set_template($tmpl);
            $this->table->set_empty("&nbsp;");

            //Set heading untuk table
            $this->table->set_heading('No', 'Code', 'Category', 'Manufacture', 'Name', 'Model', 'Size', 'Color', 'Qty', 'Action');

            $i = 0;
            if ($products){
                foreach ($products as $product)
                {
                   $datax = array('name' => 'button', 'type' => 'button', 'class' => 'btn btn-primary', 'content' => 'Select', 'onclick' => 'setvalue(\''.$product->sku.'\',\''.$target.'\')');
                   $qty = $this->stockledger->get_qty($product->id, $branch, $this->period->month, $this->period->year);

                    $this->table->add_row
                    (
                        ++$i, strtoupper($product->sku), $this->category->get_name($product->category), 
                                         $this->manufacture->get_name($product->manufacture), $product->name, $product->model, $product->size,
                                         $product->color, $qty.' '.$product->unit,
                        form_button($datax)
                    );
                }            
            }

            $data['table'] = $this->table->generate();
            $this->load->view('product_list', $data);
    }
    
    function publish($uid = null)
    {
       if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){ 
       $val = $this->model->get_by_id($uid)->row();
       if ($val->publish == 0){ $lng = array('publish' => 1); }else { $lng = array('publish' => 0); }
       $this->model->update($uid,$lng);
       echo 'true|Status Changed...!';
       }else{ echo "error|Sorry, you do not have the right to change publish status..!"; }
    }
    
    function update_all(){
        
       if ($this->acl->otentikasi2($this->title,'ajax') == TRUE){ 
        $cek = $this->input->post('cek');
        $jumlah = count($cek);
        
        if($cek)
        {
          $jumlah = count($cek);
          for ($i=0; $i<$jumlah; $i++)
          {      
            $product = array('category' => $this->session->userdata('category'),
                             'size' => $this->session->userdata('size'),
                             'color' => $this->session->userdata('color'), 
                             'publish' => $this->session->userdata('publish')); 
                
            $this->model->update($cek[$i], $product);
          }
          
          $this->session->unset_userdata('category');
          $this->session->unset_userdata('size');
          $this->session->unset_userdata('color');
          $this->session->unset_userdata('publish');
          $this->session->unset_userdata('type');
            
          $mess = intval($jumlah)." ".$this->title."successfully updated..!!";
          echo 'true|'.$mess;
        }
        else
        { 
          $mess = "No $this->title Selected..!!";
          echo 'false|'.$mess;
        }
        }else{ echo "error|Sorry, you do not have the right to change product attribute..!"; }
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

    function delete($uid)
    {
        if ($this->acl->otentikasi_admin($this->title,'ajax') == TRUE){
                
            if ($this->valid_qty($uid) == TRUE){
               $this->model->delete($uid);
               $this->session->set_flashdata('message', "1 $this->title successfully removed..!");
               echo "true|1 $this->title successfully removed..!"; 
            }
            else{ echo "error|Invalid Product Qty...!!"; }    
        }
        else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
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
        $this->form_validation->set_rules('tsku', 'SKU', 'callback_valid_sku');
        $this->form_validation->set_rules('tname', 'Name', 'required|callback_valid_name');
//        $this->form_validation->set_rules('tmodel', 'Model', 'required|callback_valid_model');
        $this->form_validation->set_rules('tmodel', 'Model', 'required');
        $this->form_validation->set_rules('ccur', 'Currency', 'required');
        $this->form_validation->set_rules('ccategory', 'Category', 'required');
        $this->form_validation->set_rules('cmanufacture', 'Manufacture', 'required');

        if ($this->form_validation->run($this) == TRUE)
        {
            $config['upload_path'] = './images/product/';
            $config['file_name'] = split_space($this->input->post('tname'));
            $config['allowed_types'] = 'jpg|gif|png|jpeg';
            $config['overwrite'] = true;
            $config['max_size']	= '50000';
            $config['max_width']  = '30000';
            $config['max_height']  = '30000';
            $config['remove_spaces'] = TRUE;

            $this->load->library('upload', $config);
            
            if (!$this->input->post('tsku')){ $sku = $this->category->get_code($this->input->post('ccategory')).'-0'.$this->model->counter();
            }else { $sku = $this->input->post('tsku'); }
//
            if ( !$this->upload->do_upload("userfile")) // if upload failure
            {
                $info['file_name'] = null;
                $data['error'] = $this->upload->display_errors();
                $product = array('name' => strtolower($this->input->post('tname')), 'permalink' => split_space($this->input->post('tname')),
                                  'sku' => $sku, 'model' => $this->input->post('tmodel'), 
                                  'currency' => $this->input->post('ccur'), 'category' => $this->input->post('ccategory'),
                                  'manufacture' => $this->input->post('cmanufacture'),
                                  'image' => null, 'created' => date('Y-m-d H:i:s'));
            }
            else
            {
                $info = $this->upload->data();
                $this->crop_image($info['file_name']);
                
                $product = array('name' => strtolower($this->input->post('tname')), 'permalink' => split_space($this->input->post('tname')),
                                  'sku' => $sku, 'model' => $this->input->post('tmodel'), 
                                  'currency' => $this->input->post('ccur'), 'category' => $this->input->post('ccategory'),
                                  'manufacture' => $this->input->post('cmanufacture'),
                                  'image' => $info['file_name'], 'created' => date('Y-m-d H:i:s'));
            }

            $this->model->add($product);
            $this->stockledger->create($this->model->max_id(), $this->branch->get_branch(), $this->period->month, $this->period->year);
            $this->session->set_flashdata('message', "One $this->title data successfully saved!");
//            redirect($this->title);
            
            if ($this->upload->display_errors()){ echo "warning|".$this->upload->display_errors(); }
            else { echo 'true|'.$this->title.' successfully saved..!|'.base_url().'images/product/'.$info['file_name']; }
        }
        else{ echo "error|".validation_errors(); }
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }

    }
    
    private function cek_tick($val)
    {
        if (!$val)
        { return 0;} else { return 1; }
    }
    
    private function split_array($val)
    { return implode(",",$val); }
    
    function remove_img($id,$type='primary')
    {
        $img = $this->model->get_by_id($id)->row();
        
        if ($type == 'primary'){
            $img = $img->image;
            if ($img){ $img = "./images/product/".$img; @unlink("$img"); }
        }else{
            $image = "./images/product/".$img->image; @unlink("$image");
            $img1 = "./images/product/".$img->url1; @unlink("$img1"); 
            $img2 = "./images/product/".$img->url2; @unlink("$img2");
            $img3 = "./images/product/".$img->url3; @unlink("$img3");
            $img4 = "./images/product/".$img->url4; @unlink("$img4");
            $img5 = "./images/product/".$img->url5; @unlink("$img5");
        }
    }
    
    function details($uid=null)
    {        
        $product = $this->model->get_by_id($uid)->row();
        if (!$product){ redirect($this->title); }
	$this->session->set_userdata('langid', $product->id);
        if ($product->type == 0){ $type = 'Raw'; }else{ $type = 'Assembly'; }
        
        echo $product->sku.'|'. $this->category->get_name($product->category).'|'. $this->manufacture->get_name($product->manufacture).'|'.$product->name.'|'.$product->model.'|'.$product->currency.'|'.
             idr_format($product->pricelow).' - '.idr_format($product->price).'|'.$product->qty.'|'.base_url().'images/product/'.$product->image.'|'.
             $product->dimension_class.'|'.$product->weight.'|'.$product->dimension.'|'.$product->color.'|'.$product->size.'|'.
             $product->dimension.'|'. $this->conversi->calculate($this->stock->unit_cost($uid)).'|'. $this->conversi->calculate($this->stock->get_last_stock_price($uid)).'|'.$type;
    }
    
    // Fungsi update untuk menset texfield dengan nilai dari database
    function update($uid=null)
    {        
        $this->model->valid_add_trans($uid, $this->title);
        
        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = 'Edit '.$this->modul['title'];
        $data['main_view'] = 'product_update';
	$data['form_action'] = site_url($this->title.'/update_process');
        $data['link'] = array('link_back' => anchor($this->title,'Back', array('class' => 'btn btn-danger')));

        $data['manufacture'] = $this->manufacture->combo();
        $data['category'] = $this->category->combo_code();
        $data['unit'] = $this->unit->combo();
        $data['currency'] = $this->currency->combo();
        $data['source'] = site_url($this->title.'/getdatatable');
        $data['related'] = $this->product->combo_publish($uid);
        $data['array'] = array('','');
        $data['graph'] = site_url()."/product/chart/";
        
        $data['color'] = $this->attribute->combo_color();
        $data['size'] = $this->attribute->combo_size();
        
        $this->cek_exist_image($uid); // cek exist image
        
        $product = $this->model->get_by_id($uid)->row();
	$this->session->set_userdata('langid', $product->id);
        
        $data['default']['sku'] = $product->sku;
        $data['default']['category'] = $product->category;
        $data['default']['type'] = $product->type;
        $data['default']['manufacture'] = $product->manufacture;
        $data['default']['name'] = $product->name;
        $data['default']['model'] = $product->model;
        $data['default']['permalink'] = $product->permalink;
        $data['default']['currency'] = $product->currency;
        $data['default']['description'] = $product->description;
        $data['default']['sdesc'] = $product->shortdesc;
        $data['default']['spec'] = $product->spesification;
        $data['default']['metatitle'] = $product->meta_title;
        $data['default']['metadesc'] = $product->meta_desc;
        $data['default']['metakeywords'] = $product->meta_keywords;
        $data['default']['price'] = $product->price;
        $data['default']['lowprice'] = $product->pricelow;
        $data['default']['discount'] = $product->discount;
        $data['default']['qty'] = $product->qty;
        $data['default']['min'] = $product->min_order;
        $data['default']['image'] = base_url().'images/product/'.$product->image;
        $data['default']['dclass'] = $product->dimension_class;
        $data['default']['weight'] = $product->weight;
        $data['default']['disc_p'] = @intval($product->discount/$product->price*100);
        $data['default']['dimension'] = $product->dimension;
        $data['default']['color'] = $product->color;
        $data['default']['size'] = $product->size;
        $data['default']['unit'] = $product->unit;
        
        if ($product->dimension)
        {
            $dimension = explode('x', $product->dimension);
            $data['default']['length'] = $dimension[0];
            $data['default']['width'] = $dimension[1];
            $data['default']['height'] = $dimension[2];
        }
        else{
            $data['default']['length'] = '';
            $data['default']['width'] = '';
            $data['default']['height'] = '';
        }

        if ($product->related){
            $related = explode(',', $product->related);
            $data['default']['related'] = $related;
        }
         
        $this->load->helper('editor');
        editor();
        $this->load->view('template', $data);
    }
    
    function image_gallery($pid=null)
    {        
        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = 'Edit '.$this->modul['title'];
        $data['main_view'] = 'article_form';
	$data['form_action'] = site_url($this->title.'/add_image/'.$pid);
        $data['link'] = array('link_back' => anchor($this->title,'Back', array('class' => 'btn btn-danger')));

        $result = $this->model->get_by_id($pid)->row();
        
        // library HTML table untuk membuat template table class zebra
        $tmpl = array('table_open' => '<table id="" class="table table-striped table-bordered">');

        $this->table->set_template($tmpl);
        $this->table->set_empty("&nbsp;");

        //Set heading untuk table
        $this->table->set_heading('No', 'Name', 'Image');
        
        for ($i=1; $i<=5; $i++)
        {   
            switch ($i) {
                case 1:$url = $result->url1; break;
                case 2:$url = $result->url2; break;
                case 3:$url = $result->url3; break;
                case 4:$url = $result->url4; break;
                case 5:$url = $result->url5; break;
            }
            
            if ($url){ if ($result->url_upload == 1){ $url = base_url().'images/product/'.$url; } }
            
            $image_properties = array('src' => $url, 'alt' => 'Image'.$i, 'class' => 'img_product', 'width' => '60', 'title' => 'Image'.$i,);
            $this->table->add_row
            (
               $i, 'Image'.$i, !empty($url) ? img($image_properties) : ''
            );
        }

        $data['table'] = $this->table->generate();
        
        $this->load->view('product_image', $data);
    }
    
    function valid_image($val)
    {
        if ($val == 0)
        {
            if (!$this->input->post('turl')){ $this->form_validation->set_message('valid_image','Image Url Required..!'); return FALSE; }
            else { return TRUE; }            
        }
    }
    
    function add_image($pid)
    {
        if ($this->acl->otentikasi2($this->title) == TRUE){

            $data['title'] = $this->properti['name'].' | Administrator  '.ucwords('Product Manager');
            $data['h2title'] = 'Product Manager';
            $data['link'] = array('link_back' => anchor('admin/','<span>back</span>', array('class' => 'back')));

            // Form validation
            
            $this->form_validation->set_rules('cname', 'Image Attribute', 'required|');
            $this->form_validation->set_rules('userfile', 'Image Value', '');

            if ($this->form_validation->run($this) == TRUE)
            {  
                $result = $this->model->get_by_id($pid)->row();
                if ($result->url_upload == 1)               
                {
                    switch ($this->input->post('cname')) {
                    case 1:$img = "./images/product/".$result->url1; break;
                    case 2:$img = "./images/product/".$result->url2; break;
                    case 3:$img = "./images/product/".$result->url3; break;
                    case 4:$img = "./images/product/".$result->url4; break;
                    case 5:$img = "./images/product/".$result->url5; break;
                  }
                  @unlink("$img"); 
                }
                
                    $config['upload_path'] = './images/product/';
                    $config['file_name'] = split_space($result->name.'_'.$this->input->post('cname'));
                    $config['allowed_types'] = 'jpg|gif|png';
                    $config['overwrite']  = true;
                    $config['max_size']   = '50000';
                    $config['max_width']  = '30000';
                    $config['max_height'] = '30000';
                    $config['remove_spaces'] = TRUE;

                    $this->load->library('upload', $config);
                    
                    if ( !$this->upload->do_upload("userfile")) // if upload failure
                    {
                        $attr = array('url'.$this->input->post('cname') => null, 'url_upload' => 1);
                    }
                    else {$info = $this->upload->data();
                         $attr = array('url'.$this->input->post('cname') => $info['file_name'], 'url_upload' => 1); 
                    } 
                
                $this->model->update($pid, $attr);
                $this->session->set_flashdata('message', "One $this->title data successfully saved!");
                
                echo 'true|Data successfully saved..!'; 
            }
            else
            {
    //            echo validation_errors();
                echo 'error|'.validation_errors();
            }
        }
        else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    function attribute($pid=null,$category=null)
    {        
        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = 'Edit '.$this->modul['title'];
        $data['main_view'] = 'article_form';
	$data['form_action'] = site_url($this->title.'/add_attribute/'.$pid);
        $data['link'] = array('link_back' => anchor($this->title,'Back', array('class' => 'btn btn-danger')));

        $data['attributes'] = $this->attribute->combo($category);  
        $result = $this->attribute_product->get_list($pid)->result();
        
        // library HTML table untuk membuat template table class zebra
        $tmpl = array('table_open' => '<table id="" class="table table-striped table-bordered">');

        $this->table->set_template($tmpl);
        $this->table->set_empty("&nbsp;");

        //Set heading untuk table
        $this->table->set_heading('No','Attribute', 'Value', '#');
        
        $i = 0;
        foreach ($result as $res)
        {
            $this->table->add_row
            (
                ++$i, $this->attribute_list->get_name($res->attribute_id), $res->value,
                anchor('#','<span>delete</span>',array('class'=> 'btn btn-danger btn-sm text-danger', 'id' => $res->id, 'title' => 'delete'))
            );
        }

        $data['table'] = $this->table->generate();
        
        $this->load->view('product_attribute', $data);
    }
    
    function add_attribute($pid)
    {
        if ($this->acl->otentikasi2($this->title) == TRUE){

            $data['title'] = $this->properti['name'].' | Administrator  '.ucwords('Product Manager');
            $data['h2title'] = 'Product Manager';
            $data['link'] = array('link_back' => anchor('admin/','<span>back</span>', array('class' => 'back')));

            // Form validation
            
            $this->form_validation->set_rules('cattribute', 'Attribute List', 'required|maxlength[100]|callback_valid_attribute['.$pid.']');
            $this->form_validation->set_rules('tvalue', 'Attribute Value', 'required');

            if ($this->form_validation->run($this) == TRUE)
            {  
                $attr = array('product_id' => $pid, 'attribute_id' => $this->input->post('cattribute'), 'value' => $this->input->post('tvalue'));
                $this->attribute_product->add($attr);
                $this->session->set_flashdata('message', "One $this->title data successfully saved!");
                
                echo 'true|Data successfully saved..!'; 
            }
            else
            {
    //            echo validation_errors();
                echo 'error|'.validation_errors();
            }
        }
        else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    function delete_attribute($id)
    {
        if ($this->acl->otentikasi2($this->title) == TRUE){
        $this->attribute_product->force_delete($id);
        echo 'true|Attribute Deleted..!';
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    function valid_type($val){
        $product = $this->model->get_by_id($this->session->userdata('langid'))->row();
        if ($product->type == $val){ return TRUE; }else{
           if ($this->valid_qty($this->session->userdata('langid')) == FALSE){
            $this->form_validation->set_message('valid_type', "Cant change product type, qty is greater than 0..!");
            return FALSE; 
           }else{ return TRUE; }      
        }
    }
    
    function valid_qty($pid)
    {
        $qty = $this->stockledger->get_qty($pid, null, $this->period->month, $this->period->year);
        if ($qty != 0){
           $this->form_validation->set_message('valid_qty', "Product Qty is greater than 0..!");
           return FALSE; 
        }else{ return TRUE; }
    }
    
    function valid_low_price($lowprice){
        $price = $this->input->post('tprice');
        if ($lowprice > $price){ $this->form_validation->set_message('valid_low_price', "Invalid Low-Price..!"); return FALSE; }
        else{ return TRUE; }
    }
    
    function valid_attribute($attr,$pid)
    {
        
        if($this->attribute_product->valid($attr, $pid) == FALSE)
        {
          $this->form_validation->set_message('valid_attribute', "Attribute Registered..!");
          return FALSE;
        }
        else{ return TRUE; }
    }

    function valid_role($val)
    {
        if(!$val)
        {
          $this->form_validation->set_message('valid_role', "role type required.");
          return FALSE;
        }
        else{ return TRUE; }
    }
    
    function valid_sku($val)
    {
        if ($this->model->valid('sku',$val) == FALSE)
        {
            $this->form_validation->set_message('valid_sku','SKU registered..!');
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
   
    function validating_sku($val)
    {
	$id = $this->session->userdata('langid');
	if ($this->model->validating('sku',$val,$id) == FALSE)
        {
            $this->form_validation->set_message('validating_sku', "SKU registered!");
            return FALSE;
        }
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

    function validating_name($val)
    {
	$id = $this->session->userdata('langid');
	if ($this->model->validating('name',$val,$id) == FALSE)
        {
            $this->form_validation->set_message('validating_name', "Name registered!");
            return FALSE;
        }
        else{ return TRUE; }
    }
    
    function valid_model($val)
    {
        if ($this->model->valid('model',$val) == FALSE)
        {
            $this->form_validation->set_message('valid_model','Model registered..!');
            return FALSE;
        }
        else{ return TRUE; }
    }

    function validating_model($val)
    {
	$id = $this->session->userdata('langid');
	if ($this->model->validating('model',$val,$id) == FALSE)
        {
            $this->form_validation->set_message('validating_model', "Model registered!");
            return FALSE;
        }
        else{ return TRUE; }
    }
    
    private function crop_image($filename){
        
        $config['image_library'] = 'gd2';
        $config['source_image'] = './images/product/'.$filename;
        $config['maintain_ratio'] = TRUE;
        $config['width']	= 300;
        $config['height']	= 300;

        $this->load->library('image_lib', $config); 
        $this->image_lib->resize();
    }
    
    function cek_exist_image($uid=0){
       $product = $this->model->get_by_id($uid)->row();
       if ($product->image != null){
          $src = './images/product/'.$product->image;
          if(file_exists(FCPATH.$src) != true){ $product = array('image' => null); $this->model->update($uid, $product); }          
       }
    }

    // Fungsi update untuk mengupdate db
    function update_process($param=0)
    {
        if ($this->acl->otentikasi_admin($this->title) == TRUE){

        $data['title'] = $this->properti['name'].' | Productistrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'product_update';
	$data['form_action'] = site_url($this->title.'/update_process');
	$data['link'] = array('link_back' => anchor('admin/','<span>back</span>', array('class' => 'back')));

	// Form validation
        if ($param == 1)
        {
            $this->form_validation->set_rules('tsku', 'SKU', 'required|callback_validating_sku|callback_valid_deleted');
            $this->form_validation->set_rules('ccategory', 'Category', 'required');
            $this->form_validation->set_rules('cunit', 'Unit', 'required');
            $this->form_validation->set_rules('cmanufacture', 'Manufacture', 'required');
            $this->form_validation->set_rules('tname', 'Product Name', 'required|callback_validating_name');
            $this->form_validation->set_rules('tmodel', 'Product Model', 'required');
//            $this->form_validation->set_rules('tmodel', 'Product Model', 'required|callback_validating_model');
            $this->form_validation->set_rules('ccurrency', 'Currency', 'required');
            $this->form_validation->set_rules('tdesc', 'Description', '');
            $this->form_validation->set_rules('ctype', 'Type', 'required|callback_valid_type');
            $this->form_validation->set_rules('tshortdesc', 'Short Description', '');
            
            if ($this->form_validation->run($this) == TRUE)
            {
                // start update 1
                $config['upload_path'] = './images/product/';
                $config['file_name'] = split_space($this->input->post('tname'));
                $config['allowed_types'] = 'jpg|gif|png';
                $config['overwrite'] = true;
                $config['max_size']	= '50000';
                $config['max_width']  = '30000';
                $config['max_height']  = '30000';
                $config['remove_spaces'] = TRUE;

                $this->load->library('upload', $config);
                $data['error'] = null;
                
                if ( !$this->upload->do_upload("userfile")) // if upload failure
                {
                    $info['file_name'] = null;
                    $data['error'] = $this->upload->display_errors();
                    $product = array('name' => strtolower($this->input->post('tname')), 'permalink' => split_space($this->input->post('tname')),
                                     'sku' => $this->input->post('tsku'), 'type' => $this->input->post('ctype'), 'model' => $this->input->post('tmodel'), 
                                     'currency' => $this->input->post('ccurrency'), 'category' => $this->input->post('ccategory'),
                                     'unit' => $this->input->post('cunit'),
                                     'manufacture' => $this->input->post('cmanufacture'), 'shortdesc' => $this->input->post('tshortdesc'),
                                     'description' => $this->input->post('tdesc'));
                }
                else
                {
                    $info = $this->upload->data();
                    $this->crop_image($info['file_name']);
                    
                    $product = array('name' => strtolower($this->input->post('tname')), 'permalink' => split_space($this->input->post('tname')),
                                      'sku' => $this->input->post('tsku'), 'type' => $this->input->post('ctype'), 'model' => $this->input->post('tmodel'), 
                                      'currency' => $this->input->post('ccurrency'), 'category' => $this->input->post('ccategory'),
                                      'unit' => $this->input->post('cunit'),
                                      'manufacture' => $this->input->post('cmanufacture'), 'shortdesc' => $this->input->post('tshortdesc'),
                                      'description' => $this->input->post('tdesc'),
                                      'image' => $info['file_name']);
                }
                
                $this->model->update($this->session->userdata('langid'), $product);
                $this->session->set_flashdata('message', "One $this->title has successfully updated!");
//                echo $data['error'];
                redirect($this->title.'/update/'.$this->session->userdata('langid'));
                
                // end update 1
            }
            else{ $this->session->set_flashdata('message', validation_errors());
                  redirect($this->title.'/update/'.$this->session->userdata('langid'));
                }
        }
        elseif ($param == 2)
        {
            $product = array('meta_title' => $this->input->post('tmetatitle'), 'meta_desc' => $this->input->post('tmetadesc'),
                             'meta_keywords' => $this->input->post('tmetakeywords'), 'spesification' => $this->input->post('tspec')
                             );
            $this->model->update($this->session->userdata('langid'), $product);
            $this->session->set_flashdata('message', "One $this->title has successfully updated!");
            redirect($this->title.'/update/'.$this->session->userdata('langid'));
        }
        elseif ($param == 3)
        {
            $this->form_validation->set_rules('tprice', 'Price', 'required|numeric');
            $this->form_validation->set_rules('tlowprice', 'Low-Price', 'required|numeric|callback_valid_low_price');
            $this->form_validation->set_rules('tdisc_p', 'Discount Percentage', 'numeric');
            $this->form_validation->set_rules('tdiscount', 'Discount', 'required|numeric');
            $this->form_validation->set_rules('tmin', 'Minimum Order', 'required|numeric');
            
            if ($this->form_validation->run($this) == TRUE){
               
                $this->edit_qty($this->session->userdata('langid'), $this->input->post('tqty'));
                $product = array('price' => $this->input->post('tprice'), 'pricelow' => $this->input->post('tlowprice'),
                                 'discount' => $this->input->post('tdiscount'),
                                 'min_order' => $this->input->post('tmin'), 'qty' => $this->input->post('tqty')
                                 );
                $this->model->update($this->session->userdata('langid'), $product);
                echo 'true|One '.$this->title.' price and qty has successfully updated!'; 
            }else{ echo 'error|'.validation_errors(); }
            

        }
        elseif ($param == 4)
        {
            $this->form_validation->set_rules('tlength', 'Length (Dimension)', 'numeric');
            $this->form_validation->set_rules('twidth', 'Width (Dimension)', 'numeric');
            $this->form_validation->set_rules('theight', 'Height (Dimension)', 'numeric');
            $this->form_validation->set_rules('cdimension', 'Dimension Class', '');
            $this->form_validation->set_rules('tweight', 'Weight', 'numeric');
            $this->form_validation->set_rules('ccolor', 'Color', 'required');
            $this->form_validation->set_rules('csize', 'Size', 'required');
            
            $dimension = $this->input->post('tlength').'x'.$this->input->post('twidth').'x'.$this->input->post('theight');
            $product = array('dimension' => $dimension, 'dimension_class' => $this->input->post('cdimension'),
                             'weight' => $this->input->post('tweight'), 'color' => $this->input->post('ccolor'), 
                             'size' => $this->input->post('csize'), 'related' => !empty($this->input->post('crelated')) ? split_array($this->input->post('crelated')) : null
                             );
            $this->model->update($this->session->userdata('langid'), $product);
            echo 'true|One '.$this->title.' dimension has successfully updated!';
        }

        
        }else { echo "error|Sorry, you do not have the right to edit $this->title component..!"; }
    }
    
    private function edit_qty($pid,$eqty)
    {
        $res = $this->model->get_by_id($pid)->row();
        $begin = $res->qty;
        if ($begin > $eqty){ // pengurangan
            $this->wt->add(date('Y-m-d H:i:s'), '', $res->currency, $pid, 0, intval($begin-$eqty), 0, 0, $this->session->userdata('log')); 
        }
        elseif ($begin < $eqty) // penambahan
        {
           $this->wt->add(date('Y-m-d H:i:s'), '', $res->currency, $pid, intval($eqty-$begin), 0, 0, 0, $this->session->userdata('log')); 
        }
    }
    
    function report_process()
    {
        $this->acl->otentikasi2($this->title);
        $data['title'] = $this->properti['name'].' | Report '.ucwords($this->modul['title']);

        $data['rundate'] = tglin(date('Y-m-d'));
        $data['log'] = $this->session->userdata('log');
        $data['category'] = $this->category->get_name($this->input->post('ccategory'));
        $data['manufacture'] = $this->manufacture->get_name($this->input->post('cmanufacture'));
        $data['year'] = $this->input->post('tyear');
        $data['month'] = $this->input->post('cmonth');
        
        $data['branch_id'] = $this->input->post('cbranch');
        $data['branch'] = $this->branch->get_name($this->input->post('cbranch'));
        $data['month'] = $this->input->post('cmonth');
        $data['year'] = $this->input->post('tyear');

//        Property Details
        $data['company'] = $this->properti['name'];
        $data['reports'] = $this->model->report($this->input->post('ccategory'), $this->input->post('cmanufacture'))->result();
        
        if ($this->input->post('ctype') == 0){ $this->load->view('product_report', $data); }
        else { $this->load->view('product_pivot', $data); }
    }
    
    function export_csv(){
        
        $stts = 'true';
        $error = null;
        $sku = $this->input->post('tsku');
        $qty = $this->input->post('tqty');
        
        if ($this->product->valid('sku', $sku) == FALSE){
            
            $name = $this->product->get_name_by_sku($sku);
            
            for ($i=0; $i<$qty; $i++){
              $list[$i] = array('sku'=> $sku, 'name'=> strtoupper(split_space($name)));
            }
        
            $fichier = $sku.'_'.split_space($name).'.csv';
            header( "Content-Type: text/csv;charset=utf-8" );
            header( "Content-Disposition: attachment;filename=\"$fichier\"" );
            header("Pragma: no-cache");
            header("Expires: 0");

            $fp= fopen('php://output', 'w');

            foreach ($list as $fields) 
            {
               fputcsv($fp, $fields, ',');
            }
            fclose($fp);
            exit();
            $error = "CSV Exported...!!";
            
        }else{ $stts = 'false'; $error = 'SKU Not Available..!'; }
        
        $this->session->set_flashdata('message', $error);
        redirect($this->title);
    }
    
    function import()
    {
        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'attendance_import';
	$data['form_action_import'] = site_url($this->title.'/import');
        $data['error'] = null;
	
//        $this->form_validation->set_rules('userfile', 'Import File', '');
        
             // ==================== upload ========================
            
            $config['upload_path']   = './uploads/';
            $config['file_name']     = 'product';
            $config['allowed_types'] = '*';
//            $config['allowed_types'] = 'csv';
            $config['overwrite']     = TRUE;
            $config['max_size']	     = '10000';
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
              $status = $this->import_product($config['file_name'].'.csv');
              $info = $this->upload->data(); 
              $this->session->set_flashdata('message', "One $this->title data successfully imported!");
              if ($status == false){ echo "error|Import Failed..!"; }else{ echo 'true|CSV Successful Uploaded'; }
              
            }                
        
       // redirect($this->title);
        
    }
    
    private function import_product($filename)
    {
        $this->load->helper('file');
//        $csvreader = new CSVReader();
        $csvreader = $this->load->library('csvreader');
        $filename = './uploads/'.$filename;
        
        $result = $csvreader->parse_file($filename);
        $stts = true;
        foreach($result as $res)
        {
           if(isset($res['SKU']) && isset($res['CATEGORY']) && isset($res['MANUFACTURE']) && isset($res['NAME']) && isset($res['MODEL']) && isset($res['QTY']) && isset($res['PRICE']))
           {
              if ($this->valid_sku($res['SKU']) == TRUE  && $this->valid_name($res['NAME'].' '.$res['MODEL']) == TRUE)
              {
                $account = array(
                             'sku' => $res['SKU'],
                             'category' => $this->category->get_id_based_code(strtoupper($res['CATEGORY'])),
                             'manufacture' => $this->manufacture->get_id($res['MANUFACTURE']),
                             'name' => $res['NAME'],
                             'model' => $res['MODEL'],
                             'qty' => 0,
                             'price' => $res['PRICE'],
                             'publish' => 0,
                             'created' => date('Y-m-d H:i:s'));
                $this->model->add($account);
                $this->stockledger->create($this->model->max_id(), $this->branch->get_branch(), $this->period->month, $this->period->year);
              }else{ $stts = false; }
           }else{ $stts = false; }              
        }
        return $stts;
    }
    
    function download()
    {
       $this->load->helper('download');
        
       $data = file_get_contents("uploads/sample/product_sample.csv"); // Read the file's contents
       $name = 'product_sample.csv';    
       force_download($name, $data);
    }
    
    
//    ====================== kartu stock =======================================
    
    function stock_card($pid=0,$branch=0)
    {
        $this->acl->otentikasi1($this->title);

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'product_card';
        
        $data['log']     = $this->session->userdata('log');
        $data['company'] = $this->properti['name'];
        $data['address'] = $this->properti['address'];
        $data['phone1']  = $this->properti['phone1'];
        $data['phone2']  = $this->properti['phone2'];
        $data['fax']     = $this->properti['fax'];
        $data['website'] = $this->properti['sitename'];
        $data['email']   = $this->properti['email'];

        $product = $this->model->get_by_id($pid)->row();

        $data['code'] = $product->sku;
        $data['brand'] = $this->manufacture->get_name($product->manufacture);
        $data['category'] = $this->category->get_name($product->category);
        $data['name'] = $product->name;
        $data['currency'] = $product->currency;
        $data['qty'] = $product->qty;
        $data['unit'] = $product->unit;
        
        if ($branch == 0){ $branch = $this->branch->get_branch_session(); }
        $data['open'] = $this->stockledger->get_trans($pid,$branch, $this->period->month, $this->period->year,'openqty');
        $data['trans'] = $this->wt->get_monthly($pid, $branch, $this->period->month, $this->period->year)->result();
        $data['page'] = $this->title.'/stock_card/'.$pid;
        $this->load->view('product_card', $data);
    }
    
    function stock_card_report($pid=null,$branch=null)
    {
        $this->acl->otentikasi1($this->title);
        if (!$pid){ redirect($this->title.'/ledger'); }

        $data['title'] = $this->properti['name'].' | Administrator  '.ucwords($this->modul['title']);
        $data['h2title'] = $this->modul['title'];
        $data['main_view'] = 'product_card';
        
        $data['log']     = $this->session->userdata('log');
        $data['company'] = $this->properti['name'];
        $data['address'] = $this->properti['address'];
        $data['phone1']  = $this->properti['phone1'];
        $data['phone2']  = $this->properti['phone2'];
        $data['fax']     = $this->properti['fax'];
        $data['website'] = $this->properti['sitename'];
        $data['email']   = $this->properti['email'];

        $product = $this->model->get_by_id($pid)->row();

        $data['code'] = $product->sku;
        $data['brand'] = $this->manufacture->get_name($product->manufacture);
        $data['category'] = $this->category->get_name($product->category);
        $data['name'] = $product->name;
        $data['currency'] = $product->currency;
        $data['qty'] = $product->qty;
        $data['unit'] = $product->unit;
        
        $data['open'] = $this->stockledger->get_prev_balance($pid, $branch, $this->session->userdata('start'), $this->period->month, $this->period->year);
        $data['trans'] = $this->wt->get_transaction($pid, $branch, $this->session->userdata('start'), $this->session->userdata('end'))->result();
        
        $data['page'] = $this->title.'/ledger';
        $this->load->view('product_card', $data);
    }
    
//    ================================= ledger ==================================
    
    function ledger($pid=null)
    {
        $this->acl->otentikasi1($this->title);
        
        $data['branch'] = $this->branch->combo_all();
        if ($pid == null){ $pid = $this->input->post('cproduct'); }
        if ($pid){ $product = $this->model->get_by_id($pid)->row(); $pname = ': '.$product->name;}
        else { $pname = null;}
        
        $period = $this->input->post('reservation');  
        
        if (!$period)
        {
            $start = date('Y-m-01');  $end = date('Y-m-t');
        }
        else { 
            $start = picker_between_split($period, 0);
            $end = picker_between_split($period, 1);
        }
        
        $this->session->set_userdata('start',$start);
        $this->session->set_userdata('end',$end);
        
        $pid = $this->product->get_id_by_sku($this->input->post('titem'));
//        
        $data['source'] = site_url($this->title.'/getledger/'.$pid.'/'.$this->input->post('cbranch'));
        $data['graph'] = site_url($this->title."/chart_ledger/".$pid.'/'.$this->input->post('cbranch'));
//        
//        
        $data['title'] = $this->properti['name'].' | Administrator Product Ledger'.strtoupper($pname);
        $data['h2title'] = 'Product Manager'.strtoupper($pname);
        $data['main_view'] = 'product_ledger';
	$data['form_action'] = site_url($this->title.'/ledger');
        $data['link'] = array('link_back' => anchor('product/','Back', array('class' => 'btn btn-danger')));
//
        $data['product'] = $this->product->combo();
        $data['array'] = array('','');
        
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
        $this->table->set_heading('#','No', 'Code', 'Branch', 'Date', 'Debit', 'Credit', 'Price');

        $data['table'] = $this->table->generate();
        $data['begin'] = $this->stockledger->get_prev_balance($pid, $this->input->post('cbranch'), $this->session->userdata('start'), $this->period->month, $this->period->year);
        $data['debit'] = $this->wt->get_sum_qty($pid, $this->input->post('cbranch'), $this->session->userdata('start'), $this->session->userdata('end'), 0);
        $data['credit'] = $this->wt->get_sum_qty($pid, $this->input->post('cbranch'), $this->session->userdata('start'), $this->session->userdata('end'), 1);
        $data['mutation'] = $data['debit']-$data['credit'];
        $data['end'] = floatval($data['begin']+$data['mutation']);
            
//        // Load absen view dengan melewatkan var $data sbgai parameter
        if ($this->input->post('bsubmit') == 'card'){
            redirect($this->title.'/stock_card_report/'.$pid.'/'.$this->input->post('cbranch'));
        }else{ $this->load->view('template', $data); }
    }
    
    public function getledger($pid=null,$branch=null)
    {   
        if ($pid){ $result = $this->wt->get_transaction($pid, $branch, $this->session->userdata('start'), $this->session->userdata('end'))->result(); 
        
            $output = null;
            if ($result){

             foreach($result as $res)
             {   
               $output[] = array ($res->id, $res->code, $this->branch->get_name($res->branch_id), tglin($res->dates), $res->debit, $res->credit, $res->price, $res->amount, $res->log);
             } 

            $this->output
             ->set_status_header(200)
             ->set_content_type('application/json', 'utf-8')
             ->set_output(json_encode($output, JSON_PRETTY_PRINT))
             ->_display();
             exit;  
            }
        }
        
    }
    
    function chart_ledger($pid=null,$branch=null)
    {   
        if ($pid){
            
//        $opening = $this->stockledger->get_trans($pid, $branch, $this->period->month, $this->period->year, 'openqty');
        $opening = $this->stockledger->get_prev_balance($pid, $branch, $this->session->userdata('start'), $this->period->month, $this->period->year);
        $rest = 0;
        $data = $this->wt->get_transaction($pid, $branch, $this->session->userdata('start'), $this->session->userdata('end'))->result();
        $datax = array();
        foreach ($data as $res) 
        {  
           if ($res->debit > 0){ $rest = intval($rest+$res->debit); }
           if ($res->credit > 0){ $rest = intval($rest-$res->credit); }
           $point = array("label" => tglin($res->dates) , "y" => intval($opening+$rest));
           array_push($datax, $point);      
        }
        echo json_encode($datax, JSON_NUMERIC_CHECK);
        }
    }
    
    function calculate_balance(){ 
       if ($this->stockledger->closing() == TRUE){ redirect('product/ledger'); } 
    }
   
    // ====================================== CLOSING ======================================
    function reset_process(){ $this->model->closing(); $this->model->closing_trans(); } 

}

?>