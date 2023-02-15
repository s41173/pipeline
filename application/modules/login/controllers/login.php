<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends MX_Controller {


   public function __construct()
   {
        parent::__construct();

//        $this->load->model('Login_model', '', TRUE);

        $this->load->helper('date');
        $this->log = new Log_lib();
        $this->load->library('email');
//        $this->login = new Login_lib();
//        $this->com = new Components();
//        $this->com = $this->com->get_id('login');
        $this->api = new Api_lib();
        $this->wb = new Wb_lib();
        $this->acl = new Acl();

        $this->properti = $this->property->get();

        // Your own constructor code
   }

   private $date,$time,$log,$login,$acl;
   private $properti,$com,$api,$wb;

   function index()
   {
        $data['pname'] = $this->properti['name'];
        $data['logo'] = $this->properti['logo'];
        $data['form_action'] = site_url('login/login_process');

        $this->load->view('login_view', $data);
    }
    
    private function set_token($userid=null,$token=null){
        
        $param = json_encode(array('userid' => $userid, 'token' => $token)); 
        $response = $this->wb->request('authentication/set_token', $param, 1, 'POST');
//        print_r($response);
        if ($response[1] == 200){ return true; }else{ return false; }
    }
    
    // login to API-WB
    function login_process()
    {
        $datax = (array)json_decode(file_get_contents('php://input')); 
        $status = 200;
        $response = null;

        $username = $datax['user'];
        $password = $datax['pass'];

        $postData = array('username' => $username, 'password' => $password);
        $postString = http_build_query($postData, '', '&');
        
        $result = $this->wb->login_request('api/login-in-cjr', $postString,1,'POST');
//        print_r($result);
        $data = json_decode($result[0], true); 
                
            if ($data['status'] == true)
            {
                $this->date  = date('Y-m-d');
                $this->time  = waktuindo();
                
                $token = $this->login_proc($username, $password);
//                print_r($token);

                $logid = intval($this->log->max_log())+1;
                $waktu = tgleng(date('Y-m-d')).' - '.waktuindo().' WIB';

                $this->log->insert($token, $this->date, $this->time, 'login');
//
                $datasession = array('username' => $username, 'userid' => $token, 'role' => $data['login_level'], 'rules' => null, 'log' => $logid, 'login' => TRUE, 'waktu' => $waktu, 'branch' => null);
//                print_r($datasession);
                $this->session->set_userdata($datasession);
                $response = array('Success' => true,'User' => $datax['user'],'Info' => null);
            }
            else
            {
                $response = array(
                'Success' => false,
                'Info' => 'Invalid Login..!!');
            }
            
        $this->output
        ->set_status_header(201)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))
        ->_display();
        exit;
    }
    
    private function login_proc($username,$password)
    {
        $status = 200;
        $response = null;

        $postdata = json_encode(array('user' => $username, 'pass' => $password));
        $result = $this->wb->request('authentication/login', $postdata,1);
//        echo $result[1];
        
            if (intval($result[1]) == 200)
            {
                $data = json_decode($result[0], true); 
//                print_r($data['token']);
                
                $this->date  = date('Y-m-d');
                $this->time  = waktuindo();
                $token = $data['token'];
                return $token;
            }
            else
            { return null;}
    }

    // Login Oddomw
    function xlogin_process()
    {
        $datax = (array)json_decode(file_get_contents('php://input')); 
        $status = 200;
        $response = null;

        $username = $datax['user'];
        $password = $datax['pass'];
        $postdata = json_encode(array('user' => $username, 'pass' => $password));
        $result = $this->wb->request('authentication/login', $postdata,1);
//        echo $result[1];
        
            if (intval($result[1]) == 200)
            {
                $data = json_decode($result[0], true); 
//                print_r($data['token']);
                
                $this->date  = date('Y-m-d');
                $this->time  = waktuindo();
                $token = $data['token'];

//                $userid = $this->Login_model->get_userid($username);
//                $role = $this->Login_model->get_role($username);
//                $rules = $this->Login_model->get_rules($role);
//                $branch = $this->Login_model->get_branch($username);
                $logid = intval($this->log->max_log())+1;
                $waktu = tgleng(date('Y-m-d')).' - '.waktuindo().' WIB';
//
                $this->log->insert($token, $this->date, $this->time, 'login');
//                $this->login->add($userid, $logid);

                $data = array('username' => $username, 'userid' => $token, 'role' => null, 'rules' => null, 'log' => $logid, 'login' => TRUE, 'waktu' => $waktu, 'branch' => null);
                $this->session->set_userdata($data);
                
                $response = array('Success' => true,'User' => $datax['user'],'Info' => null);
            }
            else
            {
                $response = array(
                'Success' => false,
                'Info' => 'Invalid Login..!!');
            }
            
        $this->output
        ->set_status_header(201)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response))
        ->_display();
        exit;
    }

    // function untuk logout
    function process_logout()
    {
//        $userid = $this->Login_model->get_userid($this->session->userdata('username'));
        $result = $this->wb->request_auth('authentication/logout', $this->session->userdata('userid'), null, 1, 'GET');
//        print_r($result[1]);
        if($result[1] == 200){
          $userid = $this->session->userdata('userid');
          $this->date  = date('Y-m-d');
          $this->time  = waktuindo();

          $this->log->insert($userid, $this->date, $this->time, 'logout');
          $this->session->sess_destroy();
          redirect('login');
        }
    }

    function forgot()
    {
	$data['form_action'] = site_url('login/send_password');
        $data['pname'] = $this->properti['name'];
        $data['logo'] = $this->properti['logo'];
        $this->load->view('forgot_view' ,$data);
    }

    function send_password()
    {
        $datax = (array)json_decode(file_get_contents('php://input')); 

        $username = $datax['user'];
        
        if ($this->Login_model->check_username($username) == FALSE)
        {
           $this->session->set_flashdata('message', 'Username not registered ..!!');

           $response = array(
              'Success' => false,
              'User' => $username,
              'Info' => 'Username / Email not registered...!'); 
        }
        else
        {  
            try
            {
              if ($this->send_email($username) == TRUE){
                  $response = array(
                    'Success' => true,
                    'User' => $username,
                    'Info' => 'Password has been sent to your email..!');  
              }else{
                  $response = array(
               'Success' => false,
               'User' => $username,
               'Info' => 'Password Submission Process Failed..!');  
              }
              
              
            }
            catch(Exception $e) {  
//                echo 'Pesan Error: ' .$e->getMessage();  
                $this->log->insert(0, date('Y-m-d'), waktuindo(), 'error', $this->com, $e->getMessage());
                $response = array(
               'Success' => false,
               'User' => $username,
               'Info' => $e->getMessage());    
            } 
        }
        
        $this->output
        ->set_status_header(201)
        ->set_content_type('application/json', 'utf-8')
        ->set_output(json_encode($response, JSON_PRETTY_PRINT))
        ->_display();
        exit;
    }
    
    // ajax function
    function cek_login(){
//        if ($this->session->userdata('username')){ echo 'true'; }else{ echo 'false'; } 
      if ($this->acl->otentikasi1('main','ajax') == FALSE){ echo 'false'; }else{ echo 'true'; }    
    }
    
        // ====================================== CLOSING ======================================
    function reset_process(){ }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */