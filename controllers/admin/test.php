<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class test extends CI_Controller {

	 
	function __construct()
	{
		parent::__construct();
		$this->load->model('course_model','',TRUE);
		
		if(!$this->session->userdata('admin_logged_in'))
   		redirect('admin/login', 'refresh');
   		
   		if($message = $this->session->flashdata('message')){
          $this->flashmessage =$message;
   		}
		if($err_msg = $this->session->flashdata('err_msg')){
          $this->err_msg =$err_msg;
   		}

   		$this->load->helper(array('form'));
		$this->load->library('form_validation');
        $this->load->database();

    }
	function index()
	{

	}

	function testlist()
	{
		 //set table id in table open tag
		 $content = array();
		 
		 $this->db->select('*');
		 $this->db->from('tasks');
		 $query  = $this->db->get();
		 $data['task_arr'] = $query->result();
		 
		 
        if(isset($this->flashmessage))
        $data['flashmessage'] = $this->flashmessage;
		if(isset($this->err_msg))
		$data['err_msg'] =  $this->err_msg;
        $data['view'] = 'testlist';
        $data['content'] = $content;

        $this->load->view('admin/template',$data);
	}


	function add()
	{	
	  
		$content = array();
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		
		if(isset($this->err_msg))
		$data['err_msg'] =  $this->err_msg;
		// field name, error message, validation rules
		if(isset($_POST['test_name']))
		{
		
			$testdata  = array();
		    $testdata['test_name'] = $content['test_name'] = $this->input->post('test_name');
		    $testdata['test_top_desc'] = $content['test_top_desc'] = $this->input->post('test_top_desc');
		    $testdata['test_bot_desc'] = $content['test_bot_desc'] = $this->input->post('test_bot_desc');
			$testdata['template_id'] = $content['template_id'] = $this->input->post('template');
			
			$this->form_validation->set_rules('template', 'Select Template', 'callback_validate[Select Template]');	
			$this->form_validation->set_rules('test_name', 'Test name', 'trim|required');
			$this->form_validation->set_rules('test_top_desc', 'Test top description', 'required');
			
			if($this->form_validation->run())
			{	
			 	$this->course_model->add_test($testdata);
			 	$id=$this->course_model->get_id($testdata['test_name']);
			 	foreach ($id as $key) {
			 		$val=$key->task_id;
			 	}
				
				redirect('admin/addTest'.$testdata['template_id'].'/addquestion/'.$val, 'refresh');
				
			 	/*if($testdata['template_id']==17)
			 	{
			 		redirect('admin/questionradio/addquestion/'.$val, 'refresh');	
			 	}
		 		else if($testdata['template_id']==9){
		 			redirect('admin/multiplechoice/addquestion/'.$val, 'refresh');	
		 		}*/
			 	
			}
		}	
		$data['view'] = 'add_test';
		$content['template']=$this->course_model->get_template();
		$content['mode']=0;
		$data['content'] = $content;
		
		$this->load->view('admin/template',$data);
	}

	function validate($val,$name)
	{
		if($val=='')
		{
			 $this->form_validation->set_message('validate', 'Please Select the '.$name.' field');
                return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	function fetchdata(){

	
	 //$lang = $_GET['lang'];
		
		/*$this->datatables->select('id,page_title,page_status')
		->add_column('action','<a href="'.base_url().'admin/cms/edit/$1">Edit</a> | <a href="'.base_url().'admin/cms/delete/$1">Delete</a>','id')
		->edit_column('page_status', '<a href="$1" >Disabled</a>' ,'page_status')
		->where('language',$lang)
        ->unset_column('id')
        ->from('cms');  
        echo $this->datatables->generate();
        */
        
        
        $page = 1;	// The current page
		$sortname = 'task_id';	 // Sort column
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
		$this->db-> from('tasks');
		$this->db->join('task_templates', 'tasks.template_id = task_templates.idtask_templates');
		//$this->db->where('language',$lang);
		$this->db->order_by($sortname,$sortorder);
		//$this->db->limit($rp,$pageStart);
        $query = $this->db->get();

        
        $data = array();
		$data['page'] = $page;
		 $data['total'] = $query -> num_rows();
		$data['rows'] = array();
		$toalArr = $query->result();
       $result = array_slice($toalArr,$pageStart,$rp);
        
        if($query -> num_rows() >0 )
		{
			//$result = $query -> result();
			
		



			foreach($result as $row)
			{
			   // print_r($row);die;
				
			    $taskEdit = '<a href="'.base_url().'admin/test/edit/'.$row->task_id.'">Edit</a>';
				 $templateEdit = '<a href="'.base_url().'admin/addTest'.$row->template_id.'/addquestion/'.$row->task_id.'">Edit</a>';
			    $detete ='<a href="'.base_url().'admin/test/delete/'.$row->task_id.'">Delete</a>';
				 $data['rows'][] = array(
				'id' => $row->task_id,
				'cell' => array($row->task_id,$row->test_name,$row->templateName,$row->template_id,$taskEdit,$templateEdit,$detete)
			);
			}
			
		}
        
        
       
       // $pagelist = $this->getpagelist($lang);
        
        
        
       
        
       echo json_encode($data); exit(); 
        
       
           
	}

	function edit($id){
		$content = array();
		//$id = $_GET['id'];
		$pagedata = $this->course_model->fetchtest($id);
	    $content['template']=$this->course_model->get_template();
		
		foreach($pagedata as $row)
		{
			$content['test_name']  = $row->test_name;
			$content['test_top_desc'] = $row->test_top_desc;
			$content['test_bot_desc'] = $row->test_bot_desc;
		//	$content['template']  = $row->template_id;
			$content['edit_temp_id']  = $row->template_id;
		}
		
		if(isset($_POST['test_name']))
		{
		
			$testdata  = array();
		    $testdata['test_name'] = $content['test_name'] = $this->input->post('test_name');
		    $testdata['test_top_desc'] = $content['test_top_desc'] = $this->input->post('test_top_desc');
		    $testdata['test_bot_desc'] = $content['test_bot_desc'] = $this->input->post('test_bot_desc');
			/*$testdata['template_id'] = */$content['template_id'] = $this->input->post('template');
			$content['edit_temp_id'] = $content['template_id'];
			//$this->form_validation->set_rules('template', 'Select Template', 'callback_validate[Select Template]');	
			$this->form_validation->set_rules('test_name', 'Test name', 'trim|required');
			$this->form_validation->set_rules('test_top_desc', 'Test top description', 'required');
			
			if($this->form_validation->run())
			{
			 	 $this->course_model->update_test($testdata,$id);
			 	 $this->session->set_flashdata('message', 'Test Updated');
			 	 redirect('admin/test/edit/'.$id, 'refresh');
			}
		}	
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		
		$content['template']=$this->course_model->get_template();
		//echo "<pre>";print_r($content['template']);exit;
		$content['mode']=1;
		$data['view'] = 'add_test';
		$data['content'] = $content;

		$this->load->view('admin/template',$data);
		
	}	

	function delete($id){
	  
	  $this->course_model->test_delete($id);
	   $this->session->set_flashdata('message', 'Test Deleted');
	   redirect('admin/test/testlist', 'refresh');
	}

	


	
}