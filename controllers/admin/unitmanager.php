<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class unitmanager extends CI_Controller {
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


	function index()
	{

	}


	function unitlist($lang=1)
	{
		 //set table id in table open tag
		 $content = array();
		   
		 
		
		 $content['lang'] =$lang;
	
        if(isset($this->flashmessage))
        $data['flashmessage'] = $this->flashmessage;
        $data['view'] = 'unitlist';
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
		$this->db-> from('unit_type');
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
			    
			    $action = '<a href="'.base_url().'admin/unitmanager/edit/'.$row->id.'">Edit</a>';
			    $action .=' | <a href="'.base_url().'admin/unitmanager/delete/'.$row->id.'">Delete</a>';
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($row->id,$row->unittype_name,$action)
			);
			}
			
		}
        
        
       
       // $pagelist = $this->getpagelist($lang);
        
        
        
       
        
       echo json_encode($data); exit(); 
        
       
           
	}
	

	public function add()
	{
		$content = array();
		//$content['lang'] =1;
		if(isset($_GET['lang']))
		$content['lang'] = $_GET['lang'];

		if(isset($_POST['unittype_name']))
		{

			$content['lang']=$this->input->post('language');
		    $content['unittype_name'] = $this->input->post('unittype_name');
		   
			$this->form_validation->set_rules('unittype_name', 'unit type name', 'trim|required|min_length[5]|xss_clean');
			
			if($this->form_validation->run())
			{
			 	 $this->course_model->add_unit();
			 	 redirect('admin/unitmanager/unitlist/'.$content['language'], 'refresh');
			}
		}
		$data['view'] = 'add_unit_types';
		$data['content'] = $content;
		$data['mode']=0;
		
		$this->load->view('admin/template',$data);

	}

	function edit($id){
			
		$content = array();
		//$id = $_GET['id'];
		$pagedata = $this->course_model->fetchunit($id);
	
		
		foreach($pagedata as $row){
			
			 $content['unittype_name'] =$row->unittype_name;
			 $content['language'] = $row->language;
			 
		}
		
	    if(isset($_POST['unittype_name']))
		{
		
		    $content['unittype_name'] = $this->input->post('unittype_name');
			$content['language'] = $this->input->post('language');
			$this->form_validation->set_rules('unittype_name', 'unittype name', 'trim|required');
			
			 if($this->form_validation->run())
			 {
			 	 $this->course_model->unit_update($content,$id);
			 	  $this->session->set_flashdata('message', 'unit type Updated');
			 	 redirect('admin/unitmanager/unitlist/'.$content['language'], 'refresh');
			 }
			 
			
		 
		
		}
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		$content['lang'] = $row->language;
			
		$data['view'] = 'add_unit_types';
		$data['content'] = $content;
		$data['mode']=1;
		$this->load->view('admin/template',$data);
		
	}	


	function delete($id){
	  $pagedata = $this->course_model->fetchunit($id);
	  foreach($pagedata as $row){
	    $lang = $row->language;
	  }
	  
	  $this->course_model->unitdelete($id);
	   $this->session->set_flashdata('message', 'Unit type Deleted');
	   redirect('admin/unitmanager/unitlist/'.$lang, 'refresh');
	}


}
?>