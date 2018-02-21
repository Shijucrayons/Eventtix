<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class manageunits extends CI_Controller {
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


	function unitlist()
	{
		 //set table id in table open tag
		 $content = array();
        if(isset($this->flashmessage))
        $data['flashmessage'] = $this->flashmessage;
        $data['view'] = 'units';
        $data['content'] = $content;

        $this->load->view('admin/template',$data);
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
		$sortname = 'unit_id';	 // Sort column
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
		$this->db-> from('course_units');
		$this->db->join('unit_type', 'course_units.unit_type_id = unit_type.id');
		//$this->db->where('language',$lang);
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
			   // print_r($row);die;
			    $action = '<a href="'.base_url().'admin/manageunits/edit/'.$row->unit_id.'">Edit</a>';
			    $action .=' | <a href="'.base_url().'admin/manageunits/delete/'.$row->unit_id.'">Delete</a>';
				 $data['rows'][] = array(
				'id' => $row->unit_id,
				'cell' => array($row->unit_id,$row->unitname,$row->unittype_name,$action)
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
		//if(isset($_GET['lang']))
		//$content['lang'] = $_GET['lang'];

		if(isset($_POST['unitname']))
		{
			
			
		    $content['unitname'] = $this->input->post('unitname');
		    $content['unit_type_id'] = $this->input->post('unit_type');
		   
			$this->form_validation->set_rules('unitname', 'unit name', 'trim|required');
			$this->form_validation->set_rules('unit_type', 'Unit Type', 'callback_validate[Unit Type]');
			
			if($this->form_validation->run())
			{
			 	 $this->course_model->manage_unit();
			 	 redirect('admin/manageunits/unitlist', 'refresh');
			}
		}
		$data['view'] = 'add_unit';
		$data['content'] = $content;
		$data['unittype']=$this->course_model->get_unittype();
		
		$this->load->view('admin/template',$data);

	}
	function validate($val,$name){
		if($val==''){
			$this->form_validation->set_message('validate', 'Please Select the '.$name.' field');
			return FALSE;
		}	
	}

	function edit($id){

		$content = array();
		//$id = $_GET['id'];
		$pagedata = $this->course_model->fetch_manageunit($id);
	
		
		foreach($pagedata as $row){
			
			 $content['unitname'] =$row->unitname;
			 $content['unit_type'] =$row->unit_type_id;
			 
		}
		
	    if(isset($_POST['unitname']))
		{
		
		    $content['unitname'] = $this->input->post('unitname');
			$content['unit_type_id'] = $this->input->post('unit_type');
			 
			$this->form_validation->set_rules('unitname', 'unit name', 'trim|required');
			$this->form_validation->set_rules('unit_type', 'Unit Type', 'callback_validate[Unit Type]');
			
			 if($this->form_validation->run())
			 {
			 	 $this->course_model->manageunit_update($content,$id);
			 	  $this->session->set_flashdata('message', 'units Updated');
			 	 redirect('admin/manageunits/unitlist', 'refresh');
			 }
			 
			
		 
		
		}
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		$content['unittype']=$this->course_model->get_unittype();	
		$data['view'] = 'edit_unit';
		$data['content'] = $content;
		$this->load->view('admin/template',$data);
		
	}	


	function delete($id){
	  
	  $this->course_model->delete_unit($id);
	   $this->session->set_flashdata('message', 'Unit type Deleted');
	   redirect('admin/manageunits/unitlist', 'refresh');
	}


}
?>