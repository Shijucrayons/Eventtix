<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class home extends CI_Controller
{
 	function __construct()
 	{
  		parent::__construct();
  		
  		if(!$this->session->userdata('admin_logged_in'))
   		redirect('admin/login', 'refresh');
		
		$this->load->library('form_validation');
		$this->load->model('admin_model','',TRUE);
		$this->load->model('common_model','',TRUE);
		
		
		if($message = $this->session->flashdata('message')){
          $this->flashmessage =$message;
   		}
  		 		
 	}

 	function index()
 	{
 	
			  $session_data = $this->session->userdata('admin_logged_in');
			  $data['adminname'] = $session_data['adminname'];	  
 		  
	   		  $session_data = $this->session->userdata('admin_logged_in');
			  $data['adminname'] = $session_data['adminname'];
			   
			   
			   $today=$this->admin_model->date_counter();
			   $data['date_values']=$today;
			   
			   $last_week=$this->admin_model->last_week_counter();
			   $data['last_week_values']=$last_week;
			   
			   $ebook_today=$this->admin_model->ebook_date_counter();
			   $data['ebook_date_values']=$ebook_today;
			   
			   $ebook_lastweek=$this->admin_model->ebook_last_week_counter();
			   $data['ebook_last_week_values']=$ebook_lastweek;
			   
			   $course_completed_today=$this->admin_model->course_completed_count();
			   $data['course_completed_count']=$course_completed_today;
			   
			   $course_completed_lastweek=$this->admin_model->course_completed_last_week();
			   $data['course_completed_last_week']=$course_completed_lastweek;
			   $this->db-> select('*');
			   $this->db-> from('courses');
			   $query = $this -> db -> get();
			   $result=$query->result();
			   //echo "<pre>";print_r($result);exit;
			   $i=0;
			   foreach($result as $rows)
			   {
			   $data['course_count'][$i]=$this->admin_model->count_enrollments($rows->course_id);
			   $data['course_name'][$i]=$rows->course_name;
			   $data['langid'][$i]=$rows->language_id;
			   $i++;
			   }
			  //echo "<pre>";print_r($data['course_name'][4]);exit;
			   $this->load->view('admin/header',$data);
			   $this->load->view('admin/left_menu',$data);
				 $this->load->view('admin/home', $data);
			   $this->load->view('admin/footer');
	  
	  
	  
	  
	  
 	}
	
	function ViewStudents()
	{
	  $session_data = $this->session->userdata('admin_logged_in');
 	  $data['adminname'] = $session_data['adminname'];
	  
	  
	  $this->load->view('admin/header',$data);
	  $this->load->view('admin/left_menu',$data);
	  $this->load->view('admin/viewStudents');
	  
	  
		
	}
	
	function logout(){
		$this->session->unset_userdata('admin_logged_in');
		redirect('admin/home');
	}
	
	function changepassword(){
		
		if(isset($_POST['save_password']))
		{
		
			$admindata  = array();
		    $admindata['password'] = md5($this->input->post('password'));
		    			 
			$this->form_validation->set_rules('old_password', 'Old password', 'trim|required|callback_chkpwd[old_password]');
			$this->form_validation->set_rules('password', 'New password', 'required|min_length[8]
');
			$this->form_validation->set_rules('password1', 'Confirm password', 'trim|required|callback_confirmpwd[password1]');
			
			if($this->form_validation->run())
			{	
			 	$this->admin_model->update_password($admindata);
			 	 $this->session->set_flashdata('message', 'Password successfully changed!');
			 	 redirect('admin/home/changepassword', 'refresh');
			}
		}
		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		
		if(isset($this->flashmessage)){
		$data['flashmessage'] = $this->flashmessage;
		}
		$data['content'] = "Change admin password";
		$session_data = $this->session->userdata('admin_logged_in');
		$data['adminname'] = $session_data['adminname'];
		$data['view'] = 'changepassword';		   
    	$this->load->view('admin/template',$data);
	}
	
		
	function chkpwd()
	  {
		$old_pass = $this->input->post('old_password');
		
		$valid_password = $this->admin_model->check_password($old_pass);
	
		if($valid_password)
		{		   
		   return TRUE;
		}
		else
		{
		    $this->form_validation->set_message('chkpwd', 'Invalid current password');
			return FALSE;
		}
	  }
	
	
	function confirmpwd($val)
	  {
		$conpword = $this->input->post('password');
	
		if($val!==$conpword)
		{
		   $this->form_validation->set_message('confirmpwd', 'Password dosen\'t match');
					return FALSE;
		}
		else
		{
		  return TRUE;
		}
	  }
	  
	  /*---- country add/edit -------*/
	  
	  function browse_country_details()
 {
   //set table id in table open tag
  
   $content = array();
   
        if(isset($this->flashmessage))
        $data['flashmessage'] = $this->flashmessage;
        $data['view'] = 'country_browse';
        $data['content'] = $content;
  

        $this->load->view('admin/template',$data);
  
 }
  function fetch_country_details()
  {
   
    $page = 1; // The current page
  $sortname = 'id';  // Sort column
  $sortorder = 'asc';  // Sort order
  $qtype = '';  // Search column
  $query = '';  // Search string
  $rp=20;
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
  $this->db-> from('countries');
  $this->db->order_by($sortname,$sortorder);
 // $this->db->limit($rp,$pageStart);
  
        $query = $this->db->get();
        
        
         $data = array();
  $data['page'] = $page;
   $data['total'] = $query -> num_rows();
  
   $total_array = $query->result();
	$result = array_slice($total_array,$pageStart,$rp);
  $data['rows'] = array();
  
  
        
        if($query -> num_rows() >0 )
  {
       // $result = $query -> result();
   foreach($result as $row)
   {
    
  $this->db-> select('*');
  $this->db-> from('currency');
  $this->db-> where('id',$row->currency_idcurrency);
  $query1 = $this->db->get();
  if($query1 -> num_rows() >0 )
  {
        $result1 = $query1 -> result();
   foreach($result1 as $row1)
   {
    
    
     $edit = '<a href="'.base_url().'admin/home/edit_country_details/'.$row->id.'">Edit</a>';
    
       $data['rows'][] = array(
    'id' => $row->id,
    
    'cell' => array($row->id,$row->country_name,$row1->currency_code,$edit)
   );
   }
   
  }
   }
        
        
       
       // $pagelist = $this->getpagelist($lang);
        
        
        
  }
        
       echo json_encode($data); exit(); 
  
        
  }
   
   
   function edit_country_details($country_id)
   {
  $this->load->helper(array('form'));
  $this->load->library('form_validation');
  $content = array();
  //$id = $_GET['id'];
  $pagedata = $this->admin_model->fetchCountry($country_id);
     $content['country_id'] =$country_id;
  
  foreach($pagedata as $row){
   
    $content['country_name'] =$row->country_name;
    $content['currency_id'] =$row->currency_idcurrency;
    $this->db-> select('*');
          $this->db-> from('currency');
       $this->db-> where('id',$row->currency_idcurrency);
     $this->db-> limit(1);
  
  $query = $this -> db -> get();
  
  if($query -> num_rows() == 1)
  {
   $result= $query -> result();
  foreach($result as $row1){
   
   $content['currency_code']=$row1->currency_code;
  }
  
  
  }
  
  
  
     if(isset($_POST['save_country_details']))
  {
       $country_data['country_name']   = $content['country_name'] = $this->input->post('country_name');
    $country_data['currency_idcurrency']   = $content['currency_idcurrency'] = $this->input->post('currency_code');
       
    
   $this->form_validation->set_rules('country_name', 'Country Name', 'required');
   $this->form_validation->set_rules('currency_code', 'Currency Name', 'required');   

   if($this->form_validation->run())
   {
    
     $this->admin_model->update_country($country_data,$country_id);    
     redirect('admin/home/browse_country_details', 'refresh');
   }
   
   
  
  }
  if(isset($this->flashmessage))
  {
  $content['flashmessage'] = $this->flashmessage;
  }
  $content['mode']=1;
  $data['view'] = 'edit_country_details';
  $content['currency_code']=$this->common_model->get_currency_codes();
  
  $data['content'] = $content;
  //print_r($content);exit;
  $this->load->view('admin/template',$data);
  
 } 
 
   
  }
	  
	  
  /*---- end country add/edit -------*/
}

?>
