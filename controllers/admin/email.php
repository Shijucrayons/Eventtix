<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class email extends CI_Controller {

	 
	function __construct()
	{
		parent::__construct();
		$this->load->model('email_model','',TRUE);
		$this->load->model('email_model','',TRUE);
	
		if(!$this->session->userdata('admin_logged_in'))
   		redirect('admin/login', 'refresh');
   		
   		if($message = $this->session->flashdata('message')){
          $this->flashmessage =$message;
   		}

   		 $this->load->helper(array('form'));
		$this->load->library('form_validation');
   		
	   	$this->load->library('Datatables');
        $this->load->library('table');
        $this->load->database();

    }
	function index()
	{	
	    $content = array();
	    $data['view'] = 'email';
	    $data['content'] = $content;
		$this->load->view('admin/email',$data);	
	}
	
	
	function email_menu()
	{
		 //set table id in table open tag
		 $content = array();
        if(isset($this->flashmessage))
        $data['flashmessage'] = $this->flashmessage;
        $data['view'] = 'emaillist';
        $data['content'] = $content;

        $this->load->view('admin/template',$data);
	}
	
	function emaillists($lang_id=4)
 {
   //set table id in table open tag
  
   $content = array();
   
   $content['langId']= $lang_id;
       
        if(isset($this->flashmessage))
        $data['flashmessage'] = $this->flashmessage;
        $data['view'] = 'emaillist';
        $data['content'] = $content;
  

        $this->load->view('admin/template',$data);
 }
	
	
	function fetchemaildata($lang_id)
	{
		
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
		$this->db-> from('auto_emails');
		$this->db-> where('language_id',$lang_id);
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
			foreach($result as $row)
			{
			    $edit = '<a href="'.base_url().'admin/email/edit_email/'.$row->id.'/'.$row->language_id.'">Edit</a>';
				$viewmail=   '<a target="_blank" href="'.base_url().'admin/email/emailview/'.$row->id.'/'.$row->language_id.'">View email</a>';
			    $data['rows'][] = array(
				'id' => $row->id,
				
				'cell' => array($row->id,$row->mail_for,$row->mail_subject,$edit,$viewmail)
			);
			}
			
		
        
        
       
       // $pagelist = $this->getpagelist($lang);
        
        
        
  }
        
       echo json_encode($data); exit(); 
  
        
       
           
	}
	
	function emailview($id,$lang_id)
	{
		$content = array();
		$this->db-> select('*');
		$this->db-> from('auto_emails');
		$this->db-> where('id',$id);
		$this->db-> where('language_id',$lang_id);
		
		$query = $this -> db -> get();
		foreach($query->result() as $row)
			{
			  // $content['mail_for']=$row->mail_description;
			   $content['mail_subject']=$row->mail_subject;
			   $content['mail_content']=$row->mail_content;
			  
			}
		
		//echo "<pre>"; print_r($content);exit;
		$data['view'] = 'email_view';
        $data['content'] = $content;
        $this->load->view('admin/template',$data);
	}
	
	function edit_email($id,$lang_id)
	{
		
		$this->db-> select('*');
		$this->db-> from('auto_emails');
		$this->db-> where('id',$id);
	    
		
		$query = $this -> db -> get();
		foreach($query->result() as $row)
			{
			   $data['mail_for']=$row->mail_for;
			   $data['mail_subject']=$row->mail_subject;
			   $data['mail_content']=$row->mail_content;
			   $data['language_id']=$row->language_id;
			}
		
		 $content = array();
		 if(isset($_POST['mail_subject']))
		 {
			$coursedata  = array();
			
		   // $cousedata['course_basename'] = $content['course_basename'] = $this->input->post('base_name');
		    
			$coursedata['mail_subject'] = $content['mail_subject'] = $this->input->post('mail_subject');
			$coursedata['mail_content'] = $content['mail_content'] = $this->input->post('mail_content');
			 
			// $coursedata['course_group_id'] = $content['course_group_id'] = $id;
			$this->form_validation->set_rules('mail_subject', 'mail_subject', 'trim|required');
			if($this->form_validation->run())
			{
				
				
				$product_data = array();
				$this->email_model->edit_email($content,$id);
			 	$course=1;
			 	
				
				}
				 
			 
			 
		 }
		 
		 
        if(isset($this->flashmessage))
        $data['flashmessage'] = $this->flashmessage;
		if(isset($course))
		{
			
			$data['view'] = 'emaillist';
		}
		else
		{
        $data['view'] = 'edit_email';
		}
		$data['id'] = $id;
		
		
		
		
        $data['content'] = $content;
		
		
		$data['langId'] = $lang_id;


        $this->load->view('admin/template',$data);
	}
	
	
}