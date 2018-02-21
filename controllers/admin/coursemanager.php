<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class coursemanager extends CI_Controller {

	 
	function __construct()
	{
		parent::__construct();
		$this->load->model('course_model','',TRUE);
		
		if(!$this->session->userdata('admin_logged_in'))
   		redirect('admin/login', 'refresh');
   		
   		if($message = $this->session->flashdata('message')){
          $this->flashmessage =$message;
   		}
   		
	   	$this->load->library('Datatables');
        $this->load->library('table');
        $this->load->database();

    }
	function index()
	{	
	   	
	}
	
	public function coursehours(){
		$content = array();

		if(isset($this->flashmessage))
        $data['flashmessage'] = $this->flashmessage;
        $data['view'] = 'coursehourlist';
		$data['content'] = $content;

        $this->load->view('admin/template',$data);
	}

	public function coursehourfetch(){


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
		$this->db-> from('coursehours');
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
			    
			    
			
			    $action = '<a href="'.base_url().'admin/coursemanager/edithours/'.$row->id.'">Edit</a>';
			    $action .=' | <a href="'.base_url().'admin/coursemanager/deletehours/'.$row->id.'">Delete</a>';
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($row->id,$row->course_hours, $action)
			);
			}
			
		}
        
        
       
       // $pagelist = $this->getpagelist($lang);
        
        
        
       
        
       echo json_encode($data); exit(); 

	}

	function addhour()
	{	
	    $this->load->helper(array('form'));
		$this->load->library('form_validation');
		$content = array();
		
		
		
		
		// field name, error message, validation rules
		if(isset($_POST['course_hours']))
		{
		
		    $content['course_hours'] = $this->input->post('course_hours');
		   
			$this->form_validation->set_rules('course_hours', 'course hour', 'trim|required|xss_clean|numeric');
			
			if($this->form_validation->run())
			{
			 	 $this->course_model->add_hour();
			 	 redirect('admin/coursemanager/coursehours', 'refresh');
			}
			
			
		}
		$data['view'] = 'add_course_hour';
		$data['content'] = $content;
		$data['mode']=0;
		$this->load->view('admin/template',$data);
		
	}

	function edithours($id){
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		$content = array();
		//$id = $_GET['id'];
		$pagedata = $this->course_model->fetchhours($id);
	
		
		foreach($pagedata as $row){
			
			 $content['course_hours'] =$row->course_hours;
			 
		}
		
		
		
	    if(isset($_POST['course_hours']))
		{
		
		     $content['course_hours'] = $this->input->post('course_hours');
			 $this->form_validation->set_rules('course_hours', 'course hour', 'trim|required|xss_clean|numeric');
			 if($this->form_validation->run())
			 {
			 	 $this->course_model->hourupdate($content,$id);
			 	  $this->session->set_flashdata('message', 'Hour Updated');
			 	 redirect('admin/coursemanager/coursehours', 'refresh');
			 }
			 
			
		 
		
		}
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		
			
		$data['view'] = 'add_course_hour';
		$data['content'] = $content;
		$data['mode']=1;
		$this->load->view('admin/template',$data);
		
	}
	function deletehours($id){
	  $pagedata = $this->course_model->removehour($id);

	   $this->session->set_flashdata('message', 'Hour Deleted');
	   redirect('admin/coursemanager/coursehours', 'refresh');
	}

}