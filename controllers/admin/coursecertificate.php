<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class coursecertificate extends CI_Controller {

	 
	function __construct()
	{
		parent::__construct();
		$this->load->model('course_model','',TRUE);
		$this->load->model('certificate_model','',TRUE);
		$this->load->model('common_model','',TRUE);
		$this->load->model('student_model','',TRUE);
		$this->load->model('email_model','',TRUE);
		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		
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
	
	public function certificate(){
		


		$content = array();

		if(isset($this->flashmessage))
        $data['flashmessage'] = $this->flashmessage;
        $data['view'] = 'coursecertifylist';
		$data['content'] = $content;

        $this->load->view('admin/template',$data);
	}

	public function certificatefetch(){


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
		$this->db-> from('course_certificates');
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
			    
			    
			
			    $action = '<a href="'.base_url().'admin/coursecertificate/edit/'.$row->id.'">Edit</a>';
			    $action .=' | <a href="'.base_url().'admin/coursecertificate/delete/'.$row->id.'">Delete</a>';
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($row->id,$row->certificate_name, $action)
			);
			}
			
		}
        
        
       
       // $pagelist = $this->getpagelist($lang);
        
        
        
       
        
       echo json_encode($data); exit(); 

	}

	function addcertificate()
	{	
	    $this->load->helper(array('form'));
		$this->load->library('form_validation');
		$content = array();
		
		
		
		
		// field name, error message, validation rules
		if(isset($_POST['certificatename']))
		{
		
		    $content['certificatename'] = $this->input->post('certificatename');
		   
			$this->form_validation->set_rules('certificatename', 'certificatename', 'trim|required');
			
			if($this->form_validation->run())
			{
			 	 $this->course_model->add_certificate();
			 	 redirect('admin/coursecertificate/certificate', 'refresh');
			}
			
			
		}
		$data['view'] = 'add_coursecertificate';
		$data['content'] = $content;
		$data['mode']=0;         //......'0' for addmode
		$this->load->view('admin/template',$data);
		
	}

	function edit($id){
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		$content = array();
		//$id = $_GET['id'];
		$pagedata = $this->course_model->fetchcertificate($id);
	
		
		foreach($pagedata as $row){
			
			 $content['certificatename'] =$row->certificatename;
			 
		}
		
		
		
	    if(isset($_POST['certificatename']))
		{
		
		     $content['certificatename'] = $this->input->post('certificatename');
			 $this->form_validation->set_rules('certificatename', 'certificatename', 'trim|required');
			 if($this->form_validation->run())
			 {
			 	 $this->course_model->certificateupdate($content,$id);
			 	  $this->session->set_flashdata('message', 'certificate name Updated');
			 	 redirect('admin/coursecertificate/certificate', 'refresh');
			 }
			 
			
		 
		
		}
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		
			
		$data['view'] = 'add_coursecertificate';
		$data['content'] = $content;
		$data['mode']=1;
		$this->load->view('admin/template',$data);
		
	}
	function delete($id){
	  $pagedata = $this->course_model->removecertificate($id);

	   $this->session->set_flashdata('message', 'Certificate Deleted');
	   redirect('admin/coursecertificate/certificate', 'refresh');
	}
	
//--------------------------------certificate request functions
	
	
	public function certificateRequests(){
		
	
	// $certficate_requests = $this->certificate_model->get_certificate_request_last_month();	
	 /*echo "<pre>";
	 print_r($certficate_requests);
	 */
	 $data['searchmode'] = 0;
	$data['uname'] = $this->input->post('uname');
	$data['name'] = $this->input->post('name');
	$data['email'] = $this->input->post('email');
	if($data['uname']!="" || $data['name']!="" || $data['email']!="")
	{
		$data['searchmode'] = 1;
		
	}
	 
	 
	 
	
		$content = array();		
		// $content['requests'] = $certficate_requests;
		if(isset($this->flashmessage))
        $data['flashmessage'] = $this->flashmessage;
        $data['view'] = 'certificateRequest';
		$data['content'] = $content;

        $this->load->view('admin/template',$data);
	}

	public function certificateRequest_fetch(){


        $page = 1;	// The current page
		$sortname = 'id';	 // Sort column
		$sortorder = 'desc ';	 // Sort order
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
		$pageStart = ($page-1)*$rp;    
		
		if(isset($_GET['uname'])||isset($_GET['name'])||isset($_GET['email']))
		{
			$this->db->select('*');
			$this->db->from('users');
			if(isset($_GET['uname'])&&($_GET['uname']!=""))
			$this->db->where('username',$_GET['uname']);
			
			if(isset($_GET['name'])&&($_GET['name']!=""))
			{
			$this->db->like('first_name',$_GET['name']);
			$this->db->or_like('last_name',$_GET['name']);
			}
			
			if(isset($_GET['email'])&&($_GET['email']!=""))
			$this->db->where('email',$_GET['email']);
			
			$users_query = $this->db->get();
			if($users_query->num_rows()>=1)
			{
				foreach($users_query->result() as $user_row)
				{
					$user_ids[] = $user_row->user_id;
				}
			}
			else
			{
				$user_ids[]=0;
			}
			$this->db->where_in('user_id',$user_ids);
		}
		 
		 
		 $this->db->select('*');
		$this->db->from('students_certificates');
		//$this->db->where('issue_status','approval_pending');	
		
		//$this->db->where('applied_on BETWEEN DATE_SUB( CURDATE( ) , INTERVAL 2 MONTH ) AND CURDATE( )');    
		$this->db->order_by('id','DESC');
	//	$this->db->order_by('issue_status','ASC');
		
		
		//ORDER BY  `students_certificates`.`issue_status` ASC   	
		$query = $this -> db -> get();
		
		/*echo "<pre>";
		print_r($query->result());
		exit;*/
		
		 $data = array();
		$data['page'] = $page;
		$data['total'] = $query -> num_rows();
		$data['rows'] = array();   
        
        //$certficate_requests = $this->certificate_model->get_certificate_request_last_month();		 
			
		//	foreach($certficate_requests as $row){
			$total_array = $query->result();
		
		$result = array_slice($total_array,$pageStart,$rp);
			
		
			if($query -> num_rows() >0 )
			{
				
				
				foreach($result as $row){				
				$date_applied = $row->applied_on;	
							
				$studArr= $this->student_model->get_student_name($row->user_id);
				foreach($studArr as $row2)
				{
					$studName="<a href='".base_url()."admin/browsestudent/details/".$row2->user_id."'>".$row2->first_name."</a>";
				}
				$courseArr = $this->student_model->get_coursename($row->course_id);				
				foreach($courseArr as $row2)
				{
					$courseName=$row2->course_name;
				}
				
				if($row->issue_status=='approval_pending')
				{
					$status = "Approve";
//					$send = '<a href="'.base_url().'admin/coursecertificate/sendcertificate/'.$row->id.'">Approve</a>';
				$send = '<p id="cert_'.$row->id.'"><a href="javascript:void(0)" onClick="approve_certficate('.$row->id.')">Approve</a></p>';
				}
				else if($row->issue_status=='approved')
				{
					$status = "Aproved";
					$send = 'Processed';
				}
				else if($row->issue_status=='rejected')
				{
					$status = "Rejected";
					$send = 'Rejected';
				}
				else
				{
					$status = "";
					$send = '';
				}

				//$hrdcopypostStatus = "aa";
				$delete = "<a href='#'>Delete</a>";
							   
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($row->id,$studName,$courseName,$date_applied,$send,$delete)
			);
			}
		}	        
       echo json_encode($data); exit(); 

	
	}
	
	function approve_certificate_ajax($certificate_id)
	{
		
			$certificate_request_details = $this->certificate_model->getRequestDetails($certificate_id);
			
			foreach($certificate_request_details as $details)
			{
				$user_id= $details->user_id;
				$course_id = $details->course_id;
			}
			
			$user_details =  $this->student_model->get_student_byId($user_id);
			
			foreach($user_details as $row)
			{
				
				$user_email= $row->email;
				$user_name = $row->first_name;
				$lang_id = $row->lang_id;
			}
			
			$course_deatils = $this->course_model->fetchcourse($course_id);
			
			foreach($course_deatils as $row)
			{
				$course_name = $row->course_name;
				
			}
			
			$mail_for = "cerificate_approved";
			$email_details = $this->email_model->getTemplateById($mail_for,$lang_id);
			foreach($email_details as $row)
			{
				
				$email_subject = $row->mail_subject;
				$mail_content = $row->mail_content;
			}
			
			
			$tomail = $user_email;
			//echo "To mail ".$tomail;
			//exit;
			//$tomail="ajithupnp@gmail.com";
			
			//$path = base_url()."/applyForCertificate.php"; //cirtificate link
			$issueDate = date ( "Y-m-d" );
			 $data1 = array(
			  "issued_on"=>$issueDate,
			  "issue_status"=>'approved'
			  );
			$data2 = array(	  
			  "course_status"=>'4'
			  );
			
			//$newStatus = '1';
			$this->certificate_model->update_students_certficate($certificate_id, $data1 );   // function for add Certificates issue date
			$this->certificate_model->update_student_enrollments($course_id,$user_id,$data2); ////function for update student status 
			
				
			
			
			$mail_content = str_replace ( "#first_name#", $user_name, $mail_content );
			$mail_content = str_replace ( "#course_name#", $course_name, $mail_content );
			//$mailContent = str_replace ( "#Details#", $path, $mailContent );
			
			$this->load->library('email');
		
			$this->email->from('info@trendimi.com', 'Team Trendimi');
			$this->email->to($tomail); 
			$this->email->cc(''); 
			$this->email->bcc(''); 
			
			$this->email->subject($email_subject);
			$this->email->message($mail_content);	
			
			$this->email->send();
			
			
			$data['success']=  'Certificate approved and mail send to user';
			echo json_encode($data);  
			exit;
		
		
	}
	
	
	function update_hard_copy_postal_date($hard_id,$postal_date)
	{
		$data = array();
		$update_array= array("post_status"=>'posted',"post_date"=>$postal_date);
		$this->certificate_model->update_hard_copy_postal_date($hard_id,$update_array); 
		$data['postal_date'] = $postal_date;
		echo json_encode($data);  
		exit;
		
	}
	
	function browse_hardcopy_requests()
	{
		$content = array();		
		// $content['requests'] = $certficate_requests;
	$data['searchmode'] = 0;
	$data['uname'] = $this->input->post('uname');
	$data['name'] = $this->input->post('name');
	$data['email'] = $this->input->post('email');
	if($data['uname']!="" || $data['name']!="" || $data['email']!="")
	{
		$data['searchmode'] = 1;
		
	}
	 
		
		if(isset($this->flashmessage))
        $data['flashmessage'] = $this->flashmessage;
        $data['view'] = 'hardcopy_request';
		$data['content'] = $content;

        $this->load->view('admin/template',$data);
	}
	
	function hard_copy_request_fetch()
	{
		 $page = 1;	// The current page
		$sortname = 'id';	 // Sort column
		$sortorder = 'desc ';	 // Sort order
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
		 $pageStart = ($page-1)*$rp;    
		 
		 if(isset($_GET['uname'])||isset($_GET['name'])||isset($_GET['email']))
		{
			$this->db->select('*');
			$this->db->from('users');
			if(isset($_GET['uname'])&&($_GET['uname']!=""))
			$this->db->where('username',$_GET['uname']);
			
			if(isset($_GET['name'])&&($_GET['name']!=""))
			{
			$this->db->like('first_name',$_GET['name']);
			$this->db->or_like('last_name',$_GET['name']);
			}
			
			if(isset($_GET['email'])&&($_GET['email']!=""))
			$this->db->where('email',$_GET['email']);
			
			$users_query = $this->db->get();
			if($users_query->num_rows()>=1)
			{
				foreach($users_query->result() as $user_row)
				{
					$user_ids[] = $user_row->user_id;
				}
			}
			else
			{
				$user_ids[]=0;
			}
			$this->db->where_in('user_id',$user_ids);
		}
		 
		 
		 $this->db->select('*');
		$this->db->from('certificate_hardcopy_applications');
		//$this->db->where('issue_status','approval_pending');	
		
		//$this->db->where('applied_on BETWEEN DATE_SUB( CURDATE( ) , INTERVAL 2 MONTH ) AND CURDATE( )');    
		$this->db->order_by('id','DESC');
	//	$this->db->order_by('issue_status','ASC');
		
		
		//ORDER BY  `students_certificates`.`issue_status` ASC   	
		$query = $this -> db -> get();
		
		/*echo "<pre>";
		print_r($query->result());
		exit;*/
		
		 $data = array();
		$data['page'] = $page;
		$data['total'] = $query -> num_rows();
		$data['rows'] = array();   
        
        //$certficate_requests = $this->certificate_model->get_certificate_request_last_month();		 
			
		//	foreach($certficate_requests as $row){
			$total_array = $query->result();
		
		$result = array_slice($total_array,$pageStart,$rp);
			
		
			if($query -> num_rows() >0 )
			{
				
				
				foreach($result as $row){				
				$date_applied = $row->hardcopy_apply_date;
				
							
				$studArr= $this->student_model->get_student_name($row->user_id);
				foreach($studArr as $row2)
				{
					$studName="<a target='_blank' href='".base_url()."admin/browsestudent/details/".$row2->user_id."'>".$row2->first_name."</a>";
				}
				$courseArr = $this->student_model->get_coursename($row->course_id);				
				foreach($courseArr as $row2)
				{
					$courseName=$row2->course_name;
				}
				
				if($row->post_status=='pending')
				{
					$status = '<p id="status_'.$row->id.'">Pending<p>';
					$post_date = '<p id="post_date_'.$row->id.'"><a href="javascript:void(0)" onClick="enter_post_date('.$row->id.')">Not Send</a></p>';
					//$send = '<a href="'.base_url().'admin/coursecertificate/sendcertificate/'.$row->id.'">Approve</a>';
				}
				else if($row->post_status=='posted')
				{
					$status = "Posted";
					$post_date = $row->post_date;
					//$send = 'Processed';
				}
				
				//$hrdcopypostStatus = "aa";
				//$delete = "<a href='#'>Delete</a>";
							   
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($row->id,$studName,$courseName,$date_applied,$status,$post_date)
			);
			}
		}	        
       echo json_encode($data); exit(); 
		
		
		
	}
	
	function sendcertificate($id)
	{
		$certificate_request_details = $this->certificate_model->getRequestDetails($id);
		
		foreach($certificate_request_details as $details)
		{
			$user_id= $details->user_id;
			$course_id = $details->course_id;
		}
		
		$user_details =  $this->student_model->get_student_byId($user_id);
		
		foreach($user_details as $row)
		{
			
			$user_email= $row->email;
			$user_name = $row->first_name;
			$lang_id = $row->lang_id;
		}
		
		$course_deatils = $this->course_model->fetchcourse($course_id);
		
		foreach($course_deatils as $row)
		{
			$course_name = $row->course_name;
			
		}
		
		$mail_for = "cerificate_approved";
		$email_details = $this->email_model->getTemplateById($mail_for,$lang_id);
		foreach($email_details as $row)
		{
			
			$email_subject = $row->mail_subject;
			$mail_content = $row->mail_content;
		}
		
		
		$tomail = $user_email;
	//$tomail="ajithupnp@gmail.com";
	
	//$path = base_url()."/applyForCertificate.php"; //cirtificate link
	$issueDate = date ( "Y-m-d" );
	 $data1 = array(
	  "issued_on"=>$issueDate,
	  "issue_status"=>'approved'
	  );
	$data2 = array(	  
	  "course_status"=>'4'
	  );
	
	//$newStatus = '1';
	$this->certificate_model->update_students_certficate($id, $data1 );   // function for add Certificates issue date
	$this->certificate_model->update_student_enrollments($course_id,$user_id,$data2); ////function for update student status 
	
		
	
	
	$mail_content = str_replace ( "#first_name#", $user_name, $mail_content );
	$mail_content .= str_replace ( "#course_name#", $course_name, $mail_content );
	//$mailContent = str_replace ( "#Details#", $path, $mailContent );
	
	$this->load->library('email');

	$this->email->from('info@trendimi.com', 'Team Trendimi');
	$this->email->to($tomail); 
	$this->email->cc(''); 
	$this->email->bcc(''); 
	
	$this->email->subject($email_subject);
	$this->email->message($mail_content);	
	
	$this->email->send();
	
	$data['flashmessage']=$this->email->print_debugger();

	
	
	
	redirect('admin/coursecertificate/certificateRequests', 'refresh');
	
	/*if ($st) {
		$color = 'green';
		$sucmsg = "Successfully send an email with links to downlode the certificate to student!";
	

	}*/
	}
	
	
	
	
	
	
	
	public function cant_aprove_certificate()
	{
	
	
	$idcittificate = $_REQUEST ['idCirtificate'];
	$studentmail = $objCerti->getCertificateDetails ( $idcittificate );
	$student = $objStud->getStudentDetails ( $studentmail ['idUsers'] );
	$tomail = $student ['email'];
	//$tomail="subin.2011@gmail.com";
	$supportEmail = $admin ['email'];
	//=======Start of function to sent mail==================================
	$emailtemplate_id = "28";
	$tempMail = $objTemplate->getTemplate ( $emailtemplate_id ); //get cirtificate  template mail
	$email_subject = $tempMail ['subject'];
	$mailContent = $tempMail ['htmlContent'];
	$course1 = $objCerti->getCourceName ( $studentmail ['idCourse'] ); //get course name  
	$mailContent = str_replace ( "#firstname#", $student ['firstName'], $mailContent );
	$mailContent = str_replace ( "#courseName#", $course1, $mailContent );
	
	$mail = $objMail->sendMail ( $tomail, $email_subject, $mailContent, $cc = "", $bcc = "", $accType = "" ); //send mail
	//=======End of function to sent mail==================================
	$issueDate = date ( "Y-m-d" );
	//And once someone’s certificate hasn’t been approved they are removed from the ‘Browse Certificate Request’ list
	//Partial Texts
	// 	idStatus 	status
	// 	 	1 	Studying
	// 	 	2 	Finished but certificate not issued
	// 	 	3 	Requested for certificate
	// 	 	4 	Certificate issued
	// 	 	5 	Enrollment not confirmed
	// 	 	6 	Expired
	// 	 	7 	Archived
	$pt = $objCerti->updateIssueDate ( $idcittificate, $issueDate ); ////function for add Certificates issue date
	$change = $objCerti->updateCourseStatus ( $studentmail ['idCourse'], $studentmail ['idUsers'],  1); ////function for update student status 
	$st = $objCerti->deleteCertificates($idcittificate) ; ////function for remove Certificates	 
	if ($change) {
		$color = 'green';
		$sucmsg = "Successfully send an email!";
	} 
}

	

}