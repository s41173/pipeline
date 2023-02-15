<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Acl {

    public function __construct()
    {
        // Do something with $params
        $this->ci = & get_instance();
//        $this->login = new Login_lib();
//        $this->admin = new Admin_lib();
        $this->wb = new Wb_lib();
    }

    private $ci,$login,$admin,$wb;

    public function otentikasi()
    { 
       try {
        if ($this->ci->session->userdata('userid')){
          $request = $this->wb->request_auth('authentication/decode', $this->ci->session->userdata('userid'), null, 1, 'GET');
          if (intval($request[1]) != 200){
             redirect('login');
          }
        }
        else{ redirect('login'); }
      }
      //catch exception
      catch(Exception $e) {
//        echo 'Error Message: ' .$e->getMessage();
          redirect('login');
      }
      
    }

    function otentikasi1($title,$ajax=null)
    {
      try {
        if ($this->ci->session->userdata('userid')){
          $request = $this->wb->request_auth('authentication/decode', $this->ci->session->userdata('userid'), null, 1, 'GET');
          if (intval($request[1]) != 200){
            return FALSE;
          }else{ return TRUE; }
        }
        else{ return FALSE; }
      }
      //catch exception
      catch(Exception $e) {
//        echo 'Error Message: ' .$e->getMessage();
          return FALSE;
      }
        
    }

    function otentikasi2($title,$ajax=null)
    {
//        $this->ci->db->select('id, name, publish, status, aktif, limit, role');
//        $this->ci->db->where('name', $title);
//        $mod = $this->ci->db->get('modul')->row();
//
//        $mod = $mod->role;
//        $mod = explode(",", $mod);
//
//        foreach ($mod as $row)
//        { if ($row == $this->ci->session->userdata('role')) {$val = TRUE; break;} else {$val = FALSE;} }
//
//        if ($val != TRUE || $this->ci->session->userdata('rules') != 2 && $this->ci->session->userdata('rules') != 3)
//        {
//           if ($ajax){ return FALSE; }
//           else{
//             $this->ci->session->set_flashdata('message', 'Sorry, you do not have the right to access '.$title.' component');
//             redirect($title);
//           }
//        }
//         else {return TRUE;}
        return TRUE;
    }

    function otentikasi3($title,$ajax=null)
    {
//        $this->ci->db->select('id, name, publish, status, aktif, limit, role');
//        $this->ci->db->where('name', $title);
//        $mod = $this->ci->db->get('modul')->row();
//
//        $mod = $mod->role;
//        $mod = explode(",", $mod);
//
//        foreach ($mod as $row)
//        { if ($row == $this->ci->session->userdata('role')) {$val = TRUE; break;} else {$val = FALSE;} }
//
//        if ($val != TRUE || $this->ci->session->userdata('rules') != 3)
//        {
//           if ($ajax){ return FALSE; }
//           else{
//             $this->ci->session->set_flashdata('message', 'Sorry, you do not have the right to access '.$title.' component');
//             redirect($title);
//           }
//        }
//        else {return TRUE;}
        return TRUE;
    }

    function otentikasi4($title,$ajax=null)
    {
//        $this->ci->db->select('id, name, publish, status, aktif, limit, role');
//        $this->ci->db->where('name', $title);
//        $mod = $this->ci->db->get('modul')->row();
//
//        $mod = $mod->role;
//        $mod = explode(",", $mod);
//
//        foreach ($mod as $row)
//        { if ($row == $this->ci->session->userdata('role')) {$val = TRUE; break;} else {$val = FALSE;} }
//
//        if ($val != TRUE || $this->ci->session->userdata('rules') != 4)
//        {
//            
//           if ($ajax){ return FALSE; }
//           else{
//             $this->ci->session->set_flashdata('message', 'Sorry, you do not have the right to edit '.$title.' component');
//             redirect($title);
//           } 
//        }
//        else {return TRUE;}
        return TRUE;
        
    }

    function otentikasi_admin($title,$ajax=null)
    {
//        if ($this->ci->session->userdata('rules') != 3)
//        {
//           if ($ajax){ return FALSE;}
//           else 
//           {
//             $this->ci->session->set_flashdata('message', 'Sorry, you do not have the right to edit this value');
//             redirect('main');
//           }  
//        }
//        else {return TRUE;}
        return TRUE;
    }
    
    function otentikasi_QC($title='main',$ajax=null)
    {
        $role = $this->ci->session->userdata('role');
        if ($role == "OP_QCPASS"){
            return TRUE;
        }else{ return FALSE; }
    }
    
    function otentikasi_Tank($title='main',$ajax=null)
    {
        $role = $this->ci->session->userdata('role');
        if ($role == "OP_TANKI" || $role == "OP_UNLOADING"){
            return TRUE;
        }else{ return FALSE; }
    }
    
    function otentikasi_Adminweb($title='main',$ajax=null)
    {
        $role = $this->ci->session->userdata('role');
        if ($role == "ADMINWEB"){
            return TRUE;
        }else{ return FALSE; }
    }
    
    function otentikasi_SPV($title='main',$ajax=null)
    {
        $role = $this->ci->session->userdata('role');
        if ($role == "SPV"){
            return TRUE;
        }else{ return FALSE; }
    }
    
}

/* End of file Property.php */