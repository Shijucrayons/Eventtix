<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class report_gen extends CI_Controller {

	 
	function __construct()
	{
		parent::__construct();
		$this->load->library('encrypt');
		$this->load->model('course_model','',TRUE);
		$this->load->model('common_model','',TRUE);
		$this->load->model('user_model','',TRUE);
		$this->load->model('sales_model','',TRUE);
		$this->load->model('discount_code_model','',TRUE);
	
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
   		
	   	$this->load->library('Datatables');
        $this->load->library('table');
        $this->load->database();

    }
	function index()
	{

	}
	function newsletter()
	{
		if(isset($this->flashmessage))
		$data['flashmessage'] = $this->flashmessage;
		if(isset($this->err_msg))
		$data['err_msg'] = $this->err_msg;
		
		$data['view'] = "newsletter_report";
		$data['content'] = $data;
		$this->load->view('admin/template',$data);
	}
	function user_course()
	{
		$data['search_mode'] = 0;
		if(isset($this->flashmessage))
		$data['flashmessage'] = $this->flashmessage;
		if(isset($this->err_msg))
		$data['err_msg'] = $this->err_msg;
		if(isset($_POST['search']))
		{
			$btn = $this->input->post('search');
			$data['search_mode'] = 1;
			$data['lan'] = $this->input->post('lan');
			$data['course'] = $this->input->post('course');
			if($btn=='Genarate Exel')
			{
				$this->session->set_flashdata('lan',$data['lan']);
				$this->session->set_flashdata('course',$data['course']);
				redirect('admin/report_gen/course_user_gen');
			}
		}
		
		$data['view'] = "user_course_report";
		$data['content'] = $data;
		$this->load->view('admin/template',$data);
	}
	function fetch_newsletter()
	{
        $page = 1;	// The current page
		$sortname = 'first_name';	 // Sort column
		$sortorder = 'asc';	 // Sort order
		$qtype = '';	 // Search column
		$query = '';	 // Search string
		$rp=10;
	
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
		$this->db->distinct('email');
		$this->db-> from('users');
		$this->db->where('newsletter','yes');
		$this->db->order_by($sortname,$sortorder);
		//$this->db->limit($rp,$pageStart);
        $query = $this->db->get();
        
		$total_array = $query->result();
		$result = array_slice($total_array,$pageStart,$rp);
        
        $data = array();
		$data['page'] = $page;
		 $data['total'] = $query -> num_rows();
		$data['rows'] = array();
		
       
        
        if($query -> num_rows() >0 )
		{
			//$result = $query -> result();
			




			foreach($result as $row)
			{
				$country = $this->user_model->get_country_name($row->country_id);
				if($country==false)
				$country = "N/A";
				$password = $this->encrypt->decode($row->password);
				$cur_code = $this->course_model->get_currency_by_country($row->country_id);
	
				 $data['rows'][] = array(
				'id' => $row->user_id,
				'cell' => array($row->email,$row->first_name,$row->last_name,$row->username,$password,$country,$cur_code,$row->reg_date)
			);
			}
			
		}
       
       echo json_encode($data); exit(); 
	}
	function fetch_user_course()
	{
        $page = 1;	// The current page
		$sortname = 'user_id';	 // Sort column
		$sortorder = 'desc';	 // Sort order
		$qtype = '';	 // Search column
		$query = '';	 // Search string
		$rp=100;
	
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
       if(isset($_GET['lan']))
	   $lan = $_GET['lan'];
	   if(isset($_GET['course']))
	   {
		   $course = $_GET['course'];
		   if($lan==0 || $lan=='')
		   {
			   if($course==1)
			   {
				   $this->db->where('course_id',1);
				   $this->db->or_where('course_id',11);
			   }
			   if($course==2)
			   {
				   $this->db->where('course_id',2);
				   $this->db->or_where('course_id',12);
			   }
			   if($course==3)
			   {
				   $this->db->where('course_id',3);
				   $this->db->or_where('course_id',13);
			   }
			   if($course==4)
			   {
				   $this->db->where('course_id',4);
				   $this->db->or_where('course_id',14);
			   }
			   if($course==5)
			   {
				   $this->db->where('course_id',5);
				   $this->db->or_where('course_id',15);
			   }
			   if($course==6)
			   {
				   $this->db->where('course_id',6);
				   $this->db->or_where('course_id',16);
			   }
		   }
		   else if($lan==4)
		   {
			   if($course==1)
			   {
				   $this->db->where('course_id',1);
				  // $this->db->or_where('course_id',11);
			   }
			   if($course==2)
			   {
				   $this->db->where('course_id',2);
				  // $this->db->or_where('course_id',12);
			   }
			   if($course==3)
			   {
				   $this->db->where('course_id',3);
				  // $this->db->or_where('course_id',13);
			   }
			   if($course==4)
			   {
				   $this->db->where('course_id',4);
				   //$this->db->or_where('course_id',14);
			   }
			   if($course==5)
			   {
				   $this->db->where('course_id',5);
				   //$this->db->or_where('course_id',15);
			   }
			   if($course==6)
			   {
				   $this->db->where('course_id',6);
				   //$this->db->or_where('course_id',16);
			   }
		   }
		   else if($lan==3)
		   {
			   if($course==1)
			   {
				  // $this->db->where('course_id',1);
				   $this->db->where('course_id',11);
			   }
			   if($course==2)
			   {
				 //  $this->db->where('course_id',2);
				   $this->db->where('course_id',12);
			   }
			   if($course==3)
			   {
				 //  $this->db->where('course_id',3);
				   $this->db->where('course_id',13);
			   }
			   if($course==4)
			   {
				 //  $this->db->where('course_id',4);
				   $this->db->where('course_id',14);
			   }
			   if($course==5)
			   {
				  // $this->db->where('course_id',5);
				   $this->db->where('course_id',15);
			   }
			   if($course==6)
			   {
				  // $this->db->where('course_id',6);
				   $this->db->where('course_id',16);
			   }
		   }
	   }
        
        $this->db-> select('*');
		//$this->db->distinct('email');
		$this->db-> from('course_enrollments');
		$this->db->order_by($sortname,$sortorder);
		//$this->db->limit($rp,$pageStart);
        $query = $this->db->get();
        
		$total_array = $query->result();
		$result = array_slice($total_array,$pageStart,$rp);
        
        $data = array();
		$data['page'] = $page;
		 $data['total'] = $query -> num_rows();
		$data['rows'] = array();
		
       
        
        if($query -> num_rows() >0 )
		{
			//$result = $query -> result();
			

			/*echo "<pre>";
			print_r($result);*/
			


			foreach($result as $row)
			{
				$user_arr = $this->user_model->get_stud_details($row->user_id);
				
				/*echo "<pre>";
				print_r($user_arr);
				echo "<br>User id ".$row->user_id;
				echo "<br>Country id ".$user_arr[0]->country_id;*/
				
				if(!empty($user_arr))
				{
				
				$country = $this->user_model->get_country_name($user_arr[0]->country_id);
				if($country==false)
				$country = "N/A";
				$password = $this->encrypt->decode($user_arr[0]->password);
				$cur_code = $this->course_model->get_currency_by_country($user_arr[0]->country_id);
				$deal_site = $this->course_model->get_deal_site_name($user_arr[0]->user_id);
				if($deal_site=="")
				$deal_site = "N/A";
				
				$reason = $this->course_model->get_reason($user_arr[0]->reason_id);
				
				
				$reason = '<span title="'.$reason.'">'.$reason.'<span>';
				//echo $deal_site;continue;
				
				 if($row->course_status==0)
				 $status = "Not Started";
				 else if($row->course_status==1)
				 $status = "Studying";
				 else if($row->course_status==2)
				 $status = "Completed";
				 else if($row->course_status==3)
				 $status = "Certificate Requested";
				 else if($row->course_status==4)
				 $status = "Certificate Issued";
				 else if($row->course_status==5)
				 $status = "Matirial Access";
				 else if($row->course_status==6)
				 $status = "Archived";
				 else if($row->course_status==7)
				 $status = "Expired";
				 
				 $course_arr = $this->user_model->get_coursename($row->course_id);
				 
				
				//$user_courses = $this->course_model(get_user_courses_names);
				
				if($row->course_status==0 || $row->course_status==1)
				{
					$icoes_type = '';
					$icoes_source = '';
				}
				else
				{
					$icoes_details = $this->sales_model->get_icoes_hardcopy_details($user_arr[0]->user_id,$row->course_id);
					if(!empty($icoes_details))
					{
						$icoes_type= $icoes_details['postal_type'];
						$icoes_source= $icoes_details['source'];
					}
					else
					{
						$icoes_type= 'Not appiled';
						$icoes_source= '';
					}
				}
	             if($user_arr[0]->lang_id==4)
				{ $language="English"; } else { $language="Spanish"; }
				
	
				 $data['rows'][] = array(
				'id' => $row->user_id,
				'cell' => array($user_arr[0]->email,$user_arr[0]->first_name,$user_arr[0]->last_name,$user_arr[0]->username,$password,$country,$cur_code,$course_arr[0]->course_name,$status,$icoes_type,$deal_site,$user_arr[0]->reg_date,$language,$reason)
			);
				}
			}
			
		}
       
       echo json_encode($data); exit(); 
	}
	
	function newsletter_gen()
	{
		$this->load->helper(array('php-excel'));	
		  
        $this->db-> select('*');
		$this->db->distinct('email');
		$this->db-> from('users');
		$this->db->where('newsletter','yes');
		$this->db->order_by('first_name','asc');
		//$this->db->limit($rp,$pageStart);
        $query = $this->db->get();
		$sql = $query->result();
	   	foreach ($sql as $row)
			 {
				 $country = $this->user_model->get_country_name($row->country_id);
				if($country==false)
				$country = "N/A";
				$password = $this->encrypt->decode($row->password);
				$cur_code = $this->course_model->get_currency_by_country($row->country_id);
				 
		$data_array[] = array($row->email,$row->first_name,$row->last_name,$row->username,$password,$country,$cur_code,$row->reg_date);
			 }
		$field_array[] = array("Email","First Name","Last Name","Username","Password","Country","CurrencyCOde","Reg date");	 
		   $xls = new Excel_XML;
		   $xls->addArray ($field_array);
		   $xls->addArray ($data_array);
		   $xls->generateXML ( "Unique Users report" );
	
	}
	function course_user_gen()
	{
		$this->load->helper(array('php-excel'));	
		
       	   $lan = $this->session->flashdata('lan');
		   $course = $this->session->flashdata('course');
		   if($lan==0 || $lan=='')
		   {
			   if($course==1)
			   {
				   $this->db->where('course_id',1);
				   $this->db->or_where('course_id',11);
			   }
			   if($course==2)
			   {
				   $this->db->where('course_id',2);
				   $this->db->or_where('course_id',12);
			   }
			   if($course==3)
			   {
				   $this->db->where('course_id',3);
				   $this->db->or_where('course_id',13);
			   }
			   if($course==4)
			   {
				   $this->db->where('course_id',4);
				   $this->db->or_where('course_id',14);
			   }
			   if($course==5)
			   {
				   $this->db->where('course_id',5);
				   $this->db->or_where('course_id',15);
			   }
			   if($course==6)
			   {
				   $this->db->where('course_id',6);
				   $this->db->or_where('course_id',16);
			   }
		   }
		   else if($lan==4)
		   {
			   if($course==1)
			   {
				   $this->db->where('course_id',1);
				  // $this->db->or_where('course_id',11);
			   }
			   if($course==2)
			   {
				   $this->db->where('course_id',2);
				  // $this->db->or_where('course_id',12);
			   }
			   if($course==3)
			   {
				   $this->db->where('course_id',3);
				  // $this->db->or_where('course_id',13);
			   }
			   if($course==4)
			   {
				   $this->db->where('course_id',4);
				   //$this->db->or_where('course_id',14);
			   }
			   if($course==5)
			   {
				   $this->db->where('course_id',5);
				   //$this->db->or_where('course_id',15);
			   }
			   if($course==6)
			   {
				   $this->db->where('course_id',6);
				   //$this->db->or_where('course_id',16);
			   }
		   }
		   else if($lan==3)
		   {
			   if($course==1)
			   {
				  // $this->db->where('course_id',1);
				   $this->db->where('course_id',11);
			   }
			   if($course==2)
			   {
				 //  $this->db->where('course_id',2);
				   $this->db->where('course_id',12);
			   }
			   if($course==3)
			   {
				 //  $this->db->where('course_id',3);
				   $this->db->where('course_id',13);
			   }
			   if($course==4)
			   {
				 //  $this->db->where('course_id',4);
				   $this->db->where('course_id',14);
			   }
			   if($course==5)
			   {
				  // $this->db->where('course_id',5);
				   $this->db->where('course_id',15);
			   }
			   if($course==6)
			   {
				  // $this->db->where('course_id',6);
				   $this->db->where('course_id',16);
			   }
		   }
	  
        
        $this->db-> select('*');
		//$this->db->distinct('email');
		$this->db-> from('course_enrollments');
		$this->db->order_by('user_id','desc');
		//$this->db->limit($rp,$pageStart);
        $query = $this->db->get();
        
		
		$result = $query->result();
        
        $data = array();
		   
        
        if($query -> num_rows() >0 )
		{
			foreach($result as $row)
			{
				
				$user_arr = $this->user_model->get_stud_details($row->user_id);
				//echo "<pre>";echo "<br>user = ".$row->user_id;print_r($user_arr);continue;
				if(!empty($user_arr))
				{
				$country = $this->user_model->get_country_name($user_arr[0]->country_id);
				if($country==false)
				$country = "N/A";
				$password = $this->encrypt->decode($user_arr[0]->password);
				$cur_code = $this->course_model->get_currency_by_country($user_arr[0]->country_id);
				$deal_site = $this->course_model->get_deal_site_name($user_arr[0]->user_id);
				if($deal_site=="")
				$deal_site = "N/A";
				
				
				$reason = $this->course_model->get_reason($user_arr[0]->reason_id);
				
				
				 if($row->course_status==0)
				 $status = "Not Started";
				 else if($row->course_status==1)
				 $status = "Studying";
				 else if($row->course_status==2)
				 $status = "Completed";
				 else if($row->course_status==3)
				 $status = "Certificate Requested";
				 else if($row->course_status==4)
				 $status = "Certificate Issued";
				 else if($row->course_status==5)
				 $status = "Matirial Access";
				 else if($row->course_status==6)
				 $status = "Archived";
				 else if($row->course_status==7)
				 $status = "Expired";
				 
				 
				  if($user_arr[0]->gender==2)
				 {
					 $gender = 'Female';
				 }
				 else  if($user_arr[0]->gender==1)
				 {
					 $gender = 'Male';
				 }
				 
				 $course_arr = $this->user_model->get_coursename($row->course_id);
				//$user_courses = $this->course_model(get_user_courses_names);
				
				if($row->course_status==0 || $row->course_status==1)
				{
					$icoes_type = '';
					$icoes_source = '';
				}
				else
				{
					$icoes_details = $this->sales_model->get_icoes_hardcopy_details($user_arr[0]->user_id,$row->course_id);
					if(!empty($icoes_details))
					{
						$icoes_type= $icoes_details['postal_type'];
						$icoes_source= $icoes_details['source'];
					}
					else
					{
						$icoes_type= 'Not appiled';
						$icoes_source= '';
					}
				}
				 if($user_arr[0]->lang_id==4)
				 {$language="English"; } else { $language="Spanish"; }
	
				 $data_array[]= array($user_arr[0]->email,$user_arr[0]->first_name,$user_arr[0]->last_name,$gender,$user_arr[0]->username,$password,$country,$cur_code,$course_arr[0]->course_name,$status,$deal_site,$user_arr[0]->reg_date);
				}
			
			}
		$field_array[] = array("Email","First Name","Last Name","Gender","Username","Password","Country","CurrencyCOde","Course name","Status","Deal site","Reg date");	
		 $xls = new Excel_XML;
		   $xls->addArray ($field_array);
		   $xls->addArray ($data_array);
		   $xls->generateXML ( "Students by Course report"); 	
		}
       
      
	
	}
	
	function genarator_students()
	{
		//$this->load->view('admin/header');
		$this->load->helper(array('php-excel'));	
	
	$stat =  $this->session->flashdata('stat'); 
	$course =   $this->session->flashdata('course');
	$start_date =  $this->session->flashdata('start_date');
	$end_date = $this->session->flashdata('end_date'); 
	$newsletter = $this->session->flashdata('newsletter'); 
	
	if(isset($stat)&&$stat!="")
	{
		if($stat=='7')
		{
			$this->db->where('course_enrollments.expired','1');
			$this->db->where('course_enrollments.course_status !=','0');
		}
		else
		$this->db->where('course_enrollments.course_status',$stat);
	}
	if(isset($course)&&$course!="")
	{
		$this->db->where('courses.course_id',$course);
	}
	if(isset($start_date)&&$start_date!="")
	{
		$this->db->where('reg_date >=',$start_date);
	}
	if(isset($end_date)&&$end_date!="")
	{
		$this->db->where('reg_date <',$end_date);
	}
	if(isset($newsletter)&&$newsletter!="")
	{
		$this->db->where('newsletter',$newsletter);
	}
	

		
		$this->db-> select('users.user_id, first_name,last_name,username,gender,courses.course_name,courses.course_id,course_enrollments.date_enrolled,course_enrollments.date_expiry,email,country_id,course_enrollments.course_id,status,country_id,course_enrollments.course_status as course_stud_status,reg_date,newsletter');
		
		$this->db->group_by('users.user_id');
		$this->db->join('course_enrollments','course_enrollments.user_id = users.user_id');
		$this->db->join('courses','courses.course_id = course_enrollments.course_id');
		//$this->db->join('redeemed_coupons','redeemed_coupons.user_id=users.user_id');
		$query = $this->db->get('users');
		
		$sql=$query->result();
if(!empty($sql))
{
	   	$fields = ($field_array[] = array ("User Id","First Name","Last Name","Gender","Course","Date Enrolled","Email ID","Country","Status","Newsletter")  );
		// echo "<pre>";print_r($sql);
	   	foreach ($sql as $row)
			 {
				 $country = $this->user_model->get_country_name($row->country_id);
				 if($row->course_stud_status==0)
				 $status = "Not Started";
				 else if($row->course_stud_status==1)
				 $status = "Studying";
				 else if($row->course_stud_status==2)
				 $status = "Completed";
				 else if($row->course_stud_status==3)
				 $status = "Certificate Requested";
				 else if($row->course_stud_status==4)
				 $status = "Certificate Issued";
				 else if($row->course_stud_status==5)
				 $status = "Matirial Access";
				 else if($row->course_stud_status==6)
				 $status = "Archived";
				 else if($row->course_stud_status==7)
				 $status = "Expired";
				 
				 if($row->gender==2)
				 {
					 $gender = 'Female';
				 }
				 else  if($row->gender==1)
				 {
					 $gender = 'Male';
				 }
$data_array[] = array( $row->user_id,$row->first_name,$row->last_name,$gender,$row->course_name,$row->date_enrolled,$row->email,$country,$status,$row->newsletter);
			 }

			 
		   $xls = new Excel_XML;
		   $xls->addArray ($field_array);
		   $xls->addArray ($data_array);
		   $xls->generateXML ( "student_list" );
}
else
{
	$this->session->set_flashdata('message',"Empty result!");
redirect('admin/browsestudent/studentlist');
}

	}
	
	
	function discountcodes_report()
	{
		
		if(isset($this->flashmessage))
		$data['flashmessage'] = $this->flashmessage;
		if(isset($this->err_msg))
		$data['err_msg'] = $this->err_msg;
	
		$data['view'] = "discountcode_report";
		$data['content'] = $data;
		$this->load->view('admin/template',$data);
	}
	
	
		function fetch_discountcodes()
	{
        $page = 1;	// The current page
		$sortname = 'id';	 // Sort column
		$sortorder = 'desc';	 // Sort order
		$qtype = '';	 // Search column
		$query = '';	 // Search string
		$rp=10;
	    $slno=1;
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
		//$this->db->distinct('email');
		$this->db-> from('discount_codes_applied');
		$this->db->order_by($sortname,$sortorder);
        $query = $this->db->get();
        
		$total_array = $query->result();
		$result = array_slice($total_array,$pageStart,$rp);
        
        $data = array();
		$data['page'] = $page;
		$data['total'] = $query -> num_rows();
		$data['rows'] = array();
		
       
        
        if($query -> num_rows() >0 )
		{
			$result = $query -> result();
			
			foreach($result as $row)
			{
			   $this->db-> select('*');
		       $this->db-> from('payments');
		       $this->db->where('id',$row->payment_id);
               $query1 = $this->db->get();
			   $result1 = $query1 -> result();
			   //echo "<pre>";print_r($result1);exit;
			    foreach($result1 as $row1)
		     	{ 
				
				 $this->db-> select('*');
		         $this->db-> from('users');
		         $this->db->where('user_id',$row1->user_id);
                 $query2 = $this->db->get();
			     $result2 = $query2 -> result();
			   
			     foreach($result2 as $row2)
		     	 { 
				  //percentage
			      $product_name  =$this->discount_code_model->get_discount_product($row1->discount_id);
				  if($product_name=="course" || $product_name=="extension")
				  { 
				  $course_ebook= $this->discount_code_model->get_course_or_ebookname($row->selected_item_id,$product_name);
				  }
				  if($product_name=="ebooks")
				  {  
				  $course_ebook =$this->discount_code_model->get_course_or_ebookname($row->selected_item_id,$product_name);
				  }    
				  
				  
				  $discountcode_name = $this->discount_code_model->get_discountcode_name($row->discount_id);
				  $discountcode=$discountcode_name['discount_code'];
				  $percentage= $discountcode_name['discount_type'];
				  
				   $purchase_date=date("y-m-d", strtotime($row1->date));
				
				  $country = $this->user_model->get_country_name($row2->country_id);
				if($country==false)
				$country = "N/A";
				$cur_code = $this->discount_code_model->get_currencycode($row1->currency_id);
	           
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($slno,$discountcode,$product_name,$percentage,$purchase_date,$course_ebook,$row1->amount,$cur_code,$row2->first_name,$row2->last_name,$row2->email,$country)
			);
			}
			$slno++;
			}
			
			}
			
		}
       
       echo json_encode($data); exit(); 
	}
	
	
	function dicountcode_gen()
	{
		$this->load->helper(array('php-excel'));
		$data_array=array();	 
       
		 $this->db-> select('*');
		 $this->db-> from('discount_codes_applied');
         $query = $this->db->get();
       
        if($query -> num_rows() >0 )
		{
			$result = $query -> result();
			if($result!='')
			{
			foreach($result as $row)
			{
				
			   $this->db-> select('*');
		       $this->db-> from('payments');
		       $this->db->where('id',$row->payment_id);
               $query1 = $this->db->get();
			   $result1 = $query1 -> result();
			   //echo "<pre>";print_r($result1);exit;
			    foreach($result1 as $row1)
		     	{ 
				
				 $this->db-> select('*');
		         $this->db-> from('users');
		         $this->db->where('user_id',$row1->user_id);
                 $query2 = $this->db->get();
			     $result2 = $query2 -> result();
			   
			     foreach($result2 as $row2)
		     	 { 
				  //percentage
			      $product_name  =$this->discount_code_model->get_discount_product($row1->discount_id);
				  if($product_name=="course")
				  { 
				  $course_ebook= $this->discount_code_model->get_course_or_ebookname($row->selected_item_id,$product_name);
				  }
				  if($product_name=="ebooks")
				  {  
				  $course_ebook =$this->discount_code_model->get_course_or_ebookname($row->selected_item_id,$product_name);
				  }    
			     
				  $discountcode_name = $this->discount_code_model->get_discountcode_name($row->discount_id);
				  $discountcode=$discountcode_name['discount_code'];
				  $percentage= $discountcode_name['discount_type'];
				   $purchase_date=date("d/m/Y", strtotime($row1->date));
				  $country = $this->user_model->get_country_name($row2->country_id);
				if($country==false)
				$country = "N/A";
				$cur_code = $this->discount_code_model->get_currencycode($row1->currency_id);
	           
				$data_array[] = array($discountcode,$product_name,$percentage,$purchase_date,$course_ebook,$row1->amount,$cur_code,$row2->first_name,$row2->last_name,$row2->email,$country);
			
			}
			}
			}
			}
		} 
		$field_array[] = array("Discountcode","Product","Percentage","Date of Purchase","Course/eBook Name","Amount of transaction","Currency of transaction","Customer First Name","Customer Last Name","Customer Email","Country");	 
		   $xls = new Excel_XML;
		   $xls->addArray ($field_array);
		   $xls->addArray ($data_array);
		   $xls->generateXML ( "Discount Codes Report" );
	
	}
	
	
	
}

?>