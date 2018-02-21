<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class cms extends CI_Controller {

	 
	function __construct()
	{
		parent::__construct();
		$this->load->model('cms_model','',TRUE);
		$this->load->model('common_model','',TRUE);
		
		
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
	    $content = array();
	    $data['view'] = 'cms';
	    $data['content'] = $content;
		$this->load->view('admin/cms',$data);	
	}
	
	
	function cmslist($lang=4)
	{
		 //set table id in table open tag
		 $content = array();
		   
		
		 $content['lang'] = $lang;
		 
		 
        $tmpl = array ( 'table_open'  => '<table id="big_table" border="1" cellpadding="2" cellspacing="1" class="mytable">' );
        $this->table->set_template($tmpl); 
        
        $this->table->set_heading('Page Title','Status','Action');
        if(isset($this->flashmessage))
        $data['flashmessage'] = $this->flashmessage;
        $data['view'] = 'cmslist';
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
		$this->db-> from('cms');
		//$this->db->where('language',$lang);
		$this->db->order_by($sortname,$sortorder);
		//$this->db->limit($rp,$pageStart);
        $query = $this->db->get();
        
        
        $data = array();
		$data['page'] = $page;
		 $data['total'] = $query -> num_rows();
		$data['rows'] = array();
		
       
        $total_array = $query->result();
		$result = array_slice($total_array,$pageStart,$rp);
		
        if($query -> num_rows() >0 )
		{
			//$result = $query -> result();
			
			foreach($result as $row){
			    
			    if($row->page_status==0){
				    $status = 'Disabled';
			    }else{
				    $status = 'Enabled';
			    }
				
				$language = $this->common_model->get_language_name($row->language);
			
			    $action = '<a href="'.base_url().'admin/cms/edit/'.$row->id.'">Edit</a>';
			    $action .=' | <a href="'.base_url().'admin/cms/delete/'.$row->id.'">Delete</a>';
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($row->id,$row->page_title,$row->page_head,$language, $status, $action)
			);
			}
			
		}
        
        
       
       // $pagelist = $this->getpagelist($lang);
        
        
        
       
        
       echo json_encode($data); exit(); 
        
       
           
	}
	
	function get_imglink($active) {
	  return ($active == 1)? 'image link 1' : 'image link 2';
	}
	
	public function status_val($status){
		if($status==0)
		return 'Disabled';
		else
		return 'Enabled';
	}
	
	
	function cmshome()
	{
		$this->load->view('admin/cms1');
	}
	
	function add()
	{	
	    $this->load->helper(array('form'));
		$this->load->library('form_validation');
		$content = array();
		
		$content['lang'] =4;
		if(isset($_GET['lang']))
		$content['lang'] = $_GET['lang'];
		
		$content['parent']= $this->cms_model->getParentTitles();
		//echo "<pre>";print_r($content['parent']);exit;
		
		
		$content['page_status'] = 0;
		
		
		// field name, error message, validation rules
		if(isset($_POST['page_title']))
		{
			if( $this->input->post('parent')=='english')
			$content['parent'] = 0;
			else
			$content['parent'] =  $this->input->post('parent');
			
		    $content['page_title'] = $this->input->post('page_title');
			$content['page_Head'] = $this->input->post('page_Head');
		    $content['meta_key'] = $this->input->post('meta_key');
		    $content['meta_desc'] = $this->input->post('meta_desc');
		    $content['page_desc'] = $this->input->post('page_desc');
		     
		    $content['page_status'] = $this->input->post('status');
		    $content['menu_loc'] = $this->input->post('menu_loc');
		    $content['lang'] = $this->input->post('language');
		    
		
			$this->form_validation->set_rules('page_title', 'Page Title', 'trim|required');
			
			if($this->form_validation->run())
			{
			 	 $this->cms_model->add_cms($content);
				$this->session->set_flashdata('message', 'CMS page added successfully.');
			 	 redirect('admin/cms/cmslist/?lang='.$content['lang'], 'refresh');
			}
			
			
		}
		$content['mode'] = '0';
		$data['view'] = 'add_cms';
		$data['content'] = $content;
		$this->load->view('admin/template',$data);
		
	}
	
	function edit($id){
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		$content = array();
		//$id = $_GET['id'];
		$pagedata = $this->cms_model->fetchForAdmin($id);
	
		if(isset($_POST['page_title']))
		{
		
		     $content['page_title'] = $this->input->post('page_title');
			 $content['page_Head'] = $this->input->post('page_Head');
			 $content['meta_key']  = $this->input->post('meta_key');
			 $content['meta_desc']  = $this->input->post('meta_desc');
			 $content['page_desc']  = $this->input->post('page_desc');
			 $content['page_status'] = $this->input->post('page_status');
			 $content['language'] = $this->input->post('language');
			 
			 $this->form_validation->set_rules('page_title', 'Page Title', 'trim|required|xss_clean');
			
			 if($this->form_validation->run())
			 {
			 	 $this->cms_model->cmsupdate($content,$id);
			 	  $this->session->set_flashdata('message', 'Page Updated');
			 	 redirect('admin/cms/cmslist/?lang='.$content['language'], 'refresh');
			 }
			 
			
		 
		
		}
	
		
		foreach($pagedata as $row){
			
			 $content['page_title'] =$row->page_title;
			 $content['page_Head'] =$row->page_head;
			 $content['meta_key']  = $row->meta_key;
			 $content['meta_desc']  = $row->meta_desc;
			 $content['page_desc']  = $row->page_desc;
			 $content['page_status'] = $row->page_status;
			 $content['language'] = $row->language;
			 $content['lang'] = $row->language;
		}
		
		
		
	 
		if(isset($this->flashmessage))
		{
		$content['flashmessage'] = $this->flashmessage;
		}
		$content['mode']=1;
		$content['cmsId'] = $id;
		$data['view'] = 'add_cms';
		$data['content'] = $content;
		//print_r($content);exit;
		$this->load->view('admin/template',$data);
		
	}	
	
	function delete($id){
	  $pagedata = $this->cms_model->fetchForAdmin($id);
	  foreach($pagedata as $row){
	    $lang = $row->language;
		$headd = $row->page_head;
	  }
	  
	  $this->cms_model->cmsdelete($id);
	   $this->session->set_flashdata('message', 'CMS page for '.$headd.' deleted');
	   redirect('admin/cms/cmslist/?lang='.$lang, 'refresh');
	}	
	
}
?>