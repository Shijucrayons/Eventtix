<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class menumanager extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->load->model('menu_model','',TRUE);
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


	function index()
	{

	}


	function menulist($lang=4)
	{
		 //set table id in table open tag
		 $content = array();
		   
		 
		
		 $content['lang'] =$lang;
		 
		 
        
        if(isset($this->flashmessage))
        $data['flashmessage'] = $this->flashmessage;
        $data['view'] = 'menulist';
        $data['content'] = $content;

        $this->load->view('admin/template',$data);
	}

	function fetchdata(){

	
	 $lang = $_GET['lang'];
		
		/*$this->datatables->select('id,page_title,page_status')
		->add_column('action','<a href="'.base_url().'admin/cms/edit/$1">Edit</a> | <a href="'.base_url().'admin/cms/delete/$1">Delete</a>','id')
		->edit_column('page_status', '<a href="$1" >Disabled</a>' ,'page_status')
		->where('language',$lang)
        ->unset_column('id')
        ->from('cms');  
        echo $this->datatables->generate();
        */
        
        
        $page = 1;	// The current page
		$sortname = 'id';	 // Sort column
		$sortorder = 'asc';	 // Sort order
		$qtype = '';	 // Search column
		$query = '';	 // Search string
		$rp=10;
		// Get posted data
	   if (isset($_POST['page'])) {
		$page = $_POST['page'];
		}
		if (isset($_POST['sortname'])) {
		$sortname = $_POST['sortname'];
		}
		if (isset($_POST['sortorder'])) {
		$sortorder = $_POST['sortorder'];
		}
		if (isset($_POST['rp'])) {
		$rp = $_POST['rp'];
		}
		
		// Setup paging SQL
		 $pageStart = ($page-1)*$rp;
       
        
        $this->db-> select('*');
		$this->db-> from('menu');
		$this->db->where('language',$lang);
		$this->db->order_by($sortname,$sortorder);
		$this->db->limit($rp,$pageStart);
        $query = $this->db->get();
        
        
        $data = array();
		$data['page'] = $page;
		 $data['total'] = $query -> num_rows();
		$data['rows'] = array();
		
       
        
        if($query -> num_rows() >0 )
		{
			$result = $query -> result();
			
			foreach($result as $row){
			    
			    if($row->menu_status==0){
				    $status = 'Disabled';
			    }else{
				    $status = 'Enabled';
			    }
			
			    $action = '<a href="'.base_url().'admin/menumanager/edit/'.$row->id.'">Edit</a>';
			    $action .=' | <a href="'.base_url().'admin/menumanager/delete/'.$row->id.'">Delete</a>';
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($row->id,$row->menu_title, $status, $action)
			);
			}
			
		}
        
        
       
       // $pagelist = $this->getpagelist($lang);
        
        
        
       
        
       echo json_encode($data); exit(); 
        
       
           
	}
	

	public function add()
	{
		$content = array();
		$content['lang'] =4;
		if(isset($_GET['lang']))
		$content['lang'] = $_GET['lang'];

		if(isset($_POST['menu_title']))
		{

			
		    $content['menu_title'] = $this->input->post('menu_title');
		    $content['menu_loc'] = $this->input->post('menu_loc');
		    $content['link_type'] = $this->input->post('link_type');
			$content['url'] = $this->input->post('url');
		    $content['page_title'] = $this->input->post('page_title');
		    $content['menu_status'] = $this->input->post('menu_status');
		    
		
			$this->form_validation->set_rules('menu_title', 'Menu Title', 'trim|required');
			
			if($this->form_validation->run())
			{
			 	 $this->menu_model->add_menu();
			 	 redirect('admin/menumanager/menulist/?lang='.$content['lang'], 'refresh');
			}
		}
		$data['view'] = 'add_menu';
		$data['content'] = $content;
		$data['mode']=0;
		$data['page_title']=$this->menu_model->get_cms();
		$this->load->view('admin/template',$data);

	}

	function edit($id){

		$content = array();
		//$id = $_GET['id'];
		$pagedata = $this->menu_model->fetchdata($id);
	
		
		foreach($pagedata as $row){
			
			 $content['menu_title'] =$row->menu_title;
			 $content['menu_loc']  = $row->menu_loc;
			 $content['link_type']  = $row->link_type;
			 $content['url']  = $row->url;
			 
			 $content['menu_status'] = $row->menu_status;
			 $content['lang'] = $row->language;
		}
		foreach ($pagedata as $key => $value) {
			$content['page_title'] = $value->page_title;

		}
		
		
	    if(isset($_POST['menu_title']))
		{
		
		   $data['menu_title'] = $content['menu_title'] = $this->input->post('menu_title');
		   $data['menu_loc'] = $content['menu_loc'] =$this->input->post('menu_loc');
		   $data['link_type'] = $content['link_type'] = $this->input->post('link_type');
			$data['url'] = $content['url'] = $this->input->post('url');
		    $data['page_title'] = $content['page_title'] = $this->input->post('page_title');
		    $data['menu_status'] =$content['menu_status'] = $this->input->post('menu_status');
			$data['language'] =$content['lang'] = $this->input->post('language');
			 
			$this->form_validation->set_rules('menu_title', 'Menu Title', 'trim|required');
			
			 if($this->form_validation->run())
			 {
			 	 $this->menu_model->menu_update($data,$id);
			 	  $this->session->set_flashdata('message', 'menu Updated');
			 	 redirect('admin/menumanager/menulist/?lang='.$content['lang'], 'refresh');
			 }
			 
			
		 
		
		}
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		
		$content['mode']=1;
		$content['page_title']=$this->menu_model->get_cms();	
		$data['view'] = 'add_menu';
		$data['content'] = $content;
		$this->load->view('admin/template',$data);
		
	}	


	function delete($id){
	  $pagedata = $this->menu_model->fetchdata($id);
	  foreach($pagedata as $row){
	    $lang = $row->language;
	  }
	  
	  $this->menu_model->menudelete($id);
	   $this->session->set_flashdata('message', 'Menu Deleted');
	   redirect('admin/menumanager/menulist/?lang='.$lang, 'refresh');
	}


}
?>