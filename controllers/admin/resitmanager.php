<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class resitmanager extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->load->model('course_model','',TRUE);
		if(!$this->session->userdata('admin_logged_in'))
   		redirect('admin/login', 'refresh');
   		
   		if($message = $this->session->flashdata('message')){
          $this->flashmessage =$message;
   		}

        $this->load->database();
        $this->load->helper(array('form'));
		    $this->load->library('form_validation');
		    $this->load->library('Datatables');
        $this->load->library('table');
    }

    function resitlist()
    {
      //set table id in table open tag
      $content = array();
      if(isset($this->flashmessage))
      $data['flashmessage'] = $this->flashmessage;
      $data['view'] = 're_sitlist';
      $data['content'] = $content;
      $this->load->view('admin/template',$data);
    }

    function edit(){

    $content = array();
    //$id = $_GET['id'];
    $pagedata = $this->course_model->fetchresit();
  
    
    foreach($pagedata as $row){
      
       $content['eur'] =$row->eur;
       $content['usd'] =$row->usd;
       $content['jpy'] =$row->jpy;
       $content['sgd'] =$row->sgd;
       $content['cad'] =$row->cad;
       $content['ils'] =$row->ils;
       $content['mxn'] =$row->mxn;
       $content['gbp'] =$row->gbp;
       $content['sek'] =$row->sek;
       $content['pln'] =$row->pln;
       $content['aud'] =$row->aud;
       $content['hkd'] =$row->hkd;
       $content['myr'] =$row->myr;
       $content['twd'] =$row->twd;
       $content['nzd'] =$row->nzd;
       $content['zar'] =$row->zar;
       $content['rmb'] =$row->rmb;
       
    }
    
      if(isset($_POST['eur']))
    {
    
      $content['eur'] =$this->input->post('eur');
      $content['usd'] =$this->input->post('usd');
      $content['jpy'] =$this->input->post('jpy');
      $content['sgd'] =$this->input->post('sgd');
      $content['cad'] =$this->input->post('cad');
      $content['ils'] =$this->input->post('ils');
      $content['mxn'] =$this->input->post('mxn');
      $content['gbp'] =$this->input->post('gbp');
      $content['sek'] =$this->input->post('sek');
      $content['pln'] =$this->input->post('pln');
      $content['aud'] =$this->input->post('aud');
      $content['hkd'] =$this->input->post('hkd');
      $content['myr'] =$this->input->post('myr');
      $content['twd'] =$this->input->post('twd');
      $content['nzd'] =$this->input->post('nzd');
      $content['zar'] =$this->input->post('zar');
      $content['rmb'] =$this->input->post('rmb');
       
      $this->form_validation->set_rules('eur', 'eur', 'trim|numeric|required');
      $this->form_validation->set_rules('usd', 'usd', 'trim|numeric');
      $this->form_validation->set_rules('jpy', 'jpy', 'trim|numeric');
      $this->form_validation->set_rules('sgd', 'sgd', 'trim|numeric');
      $this->form_validation->set_rules('cad', 'cad', 'trim|numeric');
      $this->form_validation->set_rules('ils', 'ils', 'trim|numeric');
      $this->form_validation->set_rules('mxn', 'mxn', 'trim|numeric');
      $this->form_validation->set_rules('gbp', 'gbp', 'trim|numeric');
      $this->form_validation->set_rules('sek', 'sek', 'trim|numeric');
      $this->form_validation->set_rules('pln', 'pln', 'trim|numeric');
      $this->form_validation->set_rules('aud', 'aud', 'trim|numeric');
      $this->form_validation->set_rules('hkd', 'hkd', 'trim|numeric');
      $this->form_validation->set_rules('myr', 'myr', 'trim|numeric');
      $this->form_validation->set_rules('twd', 'twd', 'trim|numeric');
      $this->form_validation->set_rules('nzd', 'nzd', 'trim|numeric');
      $this->form_validation->set_rules('zar', 'zar', 'trim|numeric');
      $this->form_validation->set_rules('rmb', 'rmb', 'trim|numeric');
      
      if($this->form_validation->run())
       {
         $this->course_model->resit_update($content);
          $this->session->set_flashdata('message', 'Resit_ Fees Updated');
         redirect('admin/resitmanager/edit', 'refresh');
       }
       
      
     
    
    }
    if(isset($this->flashmessage))
    $content['flashmessage'] = $this->flashmessage;
    
      
    $data['view'] = 're_sitlist';
    $data['content'] = $content;
    $this->load->view('admin/template',$data);
    
  }



}