<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class browsestudent extends CI_Controller {

	 
	function __construct()
	{
		parent::__construct();
		
		$this->load->library('encrypt');
		$this->load->library('session');
		$this->load->model('student_model','',TRUE);
		$this->load->model('user_model','',TRUE);
		$this->load->model('common_model');
		
		if(!$this->session->userdata('admin_logged_in'))
   			redirect('admin/login', 'refresh');
   		
   		if($message = $this->session->flashdata('message')){
          $this->flashmessage =$message;
   		}

   		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		
    }
	

	function studentlist(){
		$content = array();
        if(isset($this->flashmessage))
        $data['flashmessage'] = $this->flashmessage;
        $content['country_list']=$this->student_model->get_country();
		$content['course_list']=$this->student_model->get_course();
		$content['searchmode'] = false;
        $data['view'] = 'studentlist';
        $data['content'] = $content;
        
        $this->load->view('admin/template',$data);
		
	}
	
	function fetchstudentdata(){
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
		$sortname = 'user_id ';	 // Sort column
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
       
       
        $this->db-> select('users.user_id, first_name,last_name,username,courses.course_name,courses.course_id,course_enrollments.date_enrolled,course_enrollments.date_expiry,email,country_id,course_enrollments.course_id,status,reg_date');
		$this->db-> from('users');
		$this->db->group_by('users.user_id');
		$this->db->join('course_enrollments','course_enrollments.user_id = users.user_id');
		$this->db->join('courses','courses.course_id = course_enrollments.course_id');
		
	
		if(isset($_GET['fname']) && $_GET['fname']!=''){
			$this->db->like('first_name', $_GET['fname']); 

		}
		if(isset($_GET['lname']) && $_GET['lname']!=''){
			$this->db->like('last_name', $_GET['lname']); 

		}
		if(isset($_GET['uname']) && $_GET['uname']!=''){
			$this->db->where('username', $_GET['uname']); 

		}
		if(isset($_GET['end_date']) && $_GET['end_date']!=''){
			$this->db->where('reg_date <', $_GET['end_date']); 

		}
		if(isset($_GET['start_date']) && $_GET['start_date']!=''){
			$this->db->where('reg_date >=', $_GET['start_date']); 

		}
		if(isset($_GET['email']) && $_GET['email']!=''){
			$this->db->where('email', $_GET['email']); 

		}
		if(isset($_GET['country_id']) && $_GET['country_id']!=''){
			$this->db->where('country_id', $_GET['country_id']); 

		}
		if(isset($_GET['stat']) && $_GET['stat']!=''){
			$this->db->where('course_status', $_GET['stat']); 

		}
		if(isset($_GET['course_id']) && $_GET['course_id']!=''){
			$this->db->where('courses.course_id', $_GET['course_id']); 

		}
		if(isset($_GET['newsletter']) && $_GET['newsletter']!=''){
			$this->db->where('newsletter', $_GET['newsletter']); 

		}		
		if(isset($_GET['vcode']) && $_GET['vcode']!=''){
			$this->db->join('redeemed_coupons','redeemed_coupons.user_id=users.user_id');
			//$this->db->join('redeemed_coupons','redeemed_coupons.user_id=users.user_id');
			$this->db->where('redeemed_coupons.coupon_code', $_GET['vcode']); 
		}
		if(isset($_GET['vweb']) && $_GET['vweb']!=''){
			$this->db->join('redeemed_coupons','redeemed_coupons.user_id=users.user_id');
			$this->db->join('giftvoucher_websites','redeemed_coupons.website_id=giftvoucher_websites.id');
			$this->db->like('giftvoucher_websites.site_name', $_GET['vweb']); 
		}
		
		

		$this->db->order_by($sortname,$sortorder);
		
		
        $query = $this->db->get();
       
        $data = array();
		$data['page'] = $page;
		$data['total'] = $query -> num_rows();
		//$data['total'] = 111;
		
		;
		$total_array = $query->result();
		
		$result = array_slice($total_array,$pageStart,$rp);
		
		$data['rows'] = array();
		
       
        
        if($query -> num_rows() >0 )
		{
			//$result = $query -> result();
			




			foreach($result as $row)
			{
				$date_reg=$row->date_enrolled;
				
			  /* if($row->newsletter==1){
			   	$newsletter='active';
			   }
			   else{
			   	$newsletter='inactive';
			   }*/
			  
			   if($row->status==0)
			   {
			    $enable = '<strong><a href="'.base_url().'admin/browsestudent/enable_student/'.$row->user_id.'">Enable</a></strong>';
				$enable_mail = '<strong><a href="'.base_url().'admin/browsestudent/send_activation_mail/'.$row->user_id.'">Send</a></strong>';
			   }
				else
				{
					$enable='<a href="'.base_url().'admin/browsestudent/disable_student/'.$row->user_id.'">Disable</a>';
					$enable_mail = 'Active';
				}
					$action = '<a href="'.base_url().'admin/browsestudent/details/'.$row->user_id.'" target="blank">Details</a>';
				
				$vc = '<a href="'.base_url().'admin/browsestudent/launchToVC/'.$row->user_id.'" target="blank">VC</a>';
			    //$action = '<a href="'.base_url().'admin/addstudent/edit/'.$row->user_id.'">Edit</a>';
			  //  $action .=' | <a href="'.base_url().'admin/addstudent/delete/'.$row->user_id.'">Delete</a>';
			    
				$data['rows'][] = array(
				'id' => $row->user_id,
				'cell' => array($row->user_id,$row->first_name.' '.$row->last_name,$enable,$enable_mail,$row->course_name,$row->date_enrolled,$action,$vc)
			);
			}
			
		}
        
        
       
       // $pagelist = $this->getpagelist($lang);
        
        
        
  
       echo json_encode($data); exit(); 
        	
	}
		function studentSearch(){
		$content = array();
        if(isset($this->flashmessage))
        $data['flashmessage'] = $this->flashmessage;
		$content['searchmode'] = false;
        $data['view'] = 'studentSearch';
        $data['content'] = $content;
        
        $this->load->view('admin/template',$data);
		}
		
		function search(){
		
		$f_name=$this->input->post('fname');
		$l_name=$this->input->post('lname');
		$u_name=$this->input->post('uname');
		$end_date=$this->input->post('end');
		$start_date=$this->input->post('start');
		$e_mail=$this->input->post('email');
		$country_id=$this->input->post('country');
		$course_id=$this->input->post('course');
		$v_code=$this->input->post('vcode');
		$v_web=$this->input->post('vweb');
		$stat	   =$this->input->post('stat');
   		$newsletter =$this->input->post('newsletter');
		
		$buttonAction = $this->input->post('search');
		
		
		
	
		$content['fname'] = isset($f_name)?$f_name:'';
		$content['lname'] = isset($l_name)?$l_name:'';
		$content['uname'] = isset($u_name)?$u_name:'';
		$content['end'] = isset($end_date)?$end_date:'';
		$content['start'] = isset($start_date)?$start_date:'';
		$content['email'] = isset($e_mail)?$e_mail:'';
		$content['country'] = isset($country_id)?$country_id:'';
		$content['course'] = isset($course_id)?$course_id:'';
		$content['vcode'] = isset($v_code)?$v_code:'';
		$content['vweb'] = isset($v_web)?$v_web:'';
		$content['stat'] = isset($stat)?$stat:'';
		$content['end_date'] = isset($end_date)?$end_date:'';
		$content['start_date'] = isset($start_date)?$start_date:'';
		$content['newsletter'] = isset($newsletter)?$newsletter:'';
		//echo "<pre>";print_r($content);exit;
		
		if($buttonAction=='Generate Excel')
		{
			//echo "<pre>";print_r($content);
		$this->db->start_cache();
	
		if(isset($content['end_date']) && $content['end_date']!=''){
			$this->session->set_flashdata('end_date',$content['end_date']); 

		}
		if(isset($content['start_date']) && $content['start_date']!=''){
			$this->session->set_flashdata('start_date',$content['start_date']);
		}
		if(isset($content['stat']) && $content['stat']!=''){
			$this->session->set_flashdata('stat',$content['stat']); 

		}
		if(isset($content['course']) && $content['course']!=''){
			$this->session->set_flashdata('course',$content['course']);
		}
		if(isset($content['newsletter']) && $content['newsletter']!=''){
			$this->session->set_flashdata('newsletter',$content['newsletter']);
		}
		$this->db->stop_cache();
		redirect('admin/report_gen/genarator_students');
		//redirect('deeps_home/genarator_students_with_pass');
		}
		
		
		
		$content['searchmode'] = true;
		$content['country_list']=$this->student_model->get_country();
		$content['course_list']=$this->student_model->get_course();
				
		$data['view'] = 'studentlist';
		
        $data['content'] = $content;
        
        $this->load->view('admin/template',$data);

	
	}
	
	function searchStud(){
	
		$f_name=$this->input->post('fname');
		$l_name=$this->input->post('lname');
		$u_name=$this->input->post('uname');
		$e_mail=$this->input->post('email');
		$vcode = $this->input->post('vcode');
	
		$content['fname'] = isset($f_name)?$f_name:'';
		$content['lname'] = isset($l_name)?$l_name:'';
		$content['uname'] = isset($u_name)?$u_name:'';
		$content['email'] = isset($e_mail)?$e_mail:'';
		$content['vcode'] = isset($vcode)?$vcode:'';
		
		$content['searchmode'] = true;
		
		$data['view'] = 'studentSearch';
        $data['content'] = $content;
        
        $this->load->view('admin/template',$data);
	}
	
	function enable_student($id){
		$content = array();
		 $current_status=$this->student_model->get_course_status($id);
		 foreach ($current_status as $key) {
		 	 $curr_status=$key->course_status;
		 }
		 if($curr_status==5){
		 	$status['courseStatus']=1;
		 	$this->student_model->update_course_status($status,$id);
		 }
		 else {
		 	$this->session->set_flashdata('message', 'Current status is Studying');
		}
		$status_user['status']='1';
			$this->student_model->user_active($status_user,$id);
			$this->session->set_flashdata('message', 'Student is activated');

        if(isset($this->flashmessage))
        $data['flashmessage'] = $this->flashmessage;
        $content['country_list']=$this->student_model->get_country();
		$content['course_list']=$this->student_model->get_course();
		$content['searchmode'] = false;
        $data['view'] = 'studentlist';
        $data['content'] = $content;
        
        $this->load->view('admin/template',$data);

	}

	function disable_student($id){
		$content = array();
		 $current_status=$this->student_model->get_course_status($id);
		 foreach ($current_status as $key) {
		 	 $curr_status=$key->course_status;
		 }
		 if($curr_status==6){
		 	$status['course_status']=1;
		 	$this->student_model->update_course_status($status,$id);
		 }
		 else {
		 	$this->session->set_flashdata('message', 'Current status is Archived');
		}
		$status_user['status']='0';
			$this->student_model->user_active($status_user,$id);
			$this->session->set_flashdata('message', 'Student is Deactivated');

        if(isset($this->flashmessage))
        $data['flashmessage'] = $this->flashmessage;
        $content['country_list']=$this->student_model->get_country();
		$content['course_list']=$this->student_model->get_course();
		$content['searchmode'] = false;
        $data['view'] = 'studentlist';
        $data['content'] = $content;
        
        $this->load->view('admin/template',$data);

	}
	
	
	function details($userId){
	//$this->load->model('common_model');
		
        $this->db-> select('users.user_id as userId, first_name,last_name,username,password,course_name,courses.course_id as courId, course_enrollments.date_enrolled,enroll_type, course_enrollments.date_expiry,email, country_id,course_enrollments.course_id as courses_s_id, status,address,street, zipcode,city,dob,user_pic,course_enrollments.course_status,gender,contact_number,newsletter,lang_id,last_login');
		$this->db-> from('users');
		$this->db->join('course_enrollments','course_enrollments.user_id = users.user_id');
		$this->db->join('courses','courses.course_id = course_enrollments.course_id');
		$this->db->where('users.user_id',$userId);
		$query = $this->db->get();
	
		$data = array();
		  
        if($query -> num_rows() >0 )
		{
			$data =array();
			$data['courseCount']=$query -> num_rows();
			//echo "<pre>";print_r($query->result());exit;
			$row = $query -> row();
			$user_sub_details=$this->student_model->user_subscription_details($row->userId);
			
			if($user_sub_details!='')
			{
			$i=0;
				foreach($user_sub_details as $row_sub)
                {
                   $content['access_type']= $row_sub->type;
				   $content['date_applied']=$row_sub->date_applied;
				   $i++;
                }
			}
				
				
				$content['user_id']=$row->userId;
				$content['first_name']=$row->first_name;
				$content['last_name']=$row->last_name;
				$content['username']=$row->username;
				$content['contact_num']=$row->contact_number;
				$content['password']=$this->encrypt->decode($row->password);
				$content['lang_id'] = $row->lang_id;
				$content['address']=$row->address;
				$content['street']=$row->street;
				$content['zipcode']=$row->zipcode;
				$content['city']=$row->city;
				
				$content['email']=$row->email;
				$content['country_id']=$row->country_id;
				$content['country_name']=$this->user_model->get_country_name($row->country_id);
				$content['status']=$row->status;
				$content['dob']=$row->dob;
				$gender=$row->gender;
				$content['gender']=($gender==1)?'Male':'Female';
				$content['newsletter']=$row->newsletter;
				$content['picPath']=$row->user_pic;
				$content['last_login']=$row->last_login;
				
				
				$date1 = $content['dob'];
				$date2 = date("Y-m-d");
				$diff = abs(strtotime($date2) - strtotime($date1));
				$content['old'] = floor($diff / (365*60*60*24));
				
				$i=0;
				
				/*echo "<pre>";
				print_r($query->result());
				exit;*/
				
				
				foreach($query->result() as $row)
				{
					$content['course_id'][$i]=$row->courId;
					$content['course_name'][$i]=$row->course_name;
					$content['courseEnrollId'][$i]=$row->courses_s_id;	
					
					$content['date_enrolled'][$i]=$row->date_enrolled;
					$content['date_expiry'][$i]=$row->date_expiry;
					$content['course_status'][$i]=$row->course_status;
					$content['couseStatus'][$i]=$this->common_model->courseStatus_text($row->course_status);
					
					
					
					
					
					/*------ certficate deatils ----- */
					
					
					
					
					if($row->course_status == 0) 			// not started
					{ 
						$certificate_status[$i] = 'not_started'; 
					}
					else if($row->course_status == 1) 	// studying
					{
						$mark_details = $this->get_student_progress($row->courId,$row->userId); 
						if($mark_details['coursePercentage']==100 && $mark_details['progressPercnt']>=55)
						{
							$certificate_status[$i] = 'passed'; 					
						}
						else
						{
							$certificate_status[$i] = 'not_passed'; 
						}
					}
					else if($row->course_status == 2) 	// completed
					{ 
						$mark_details = $this->get_student_progress($row->courId,$row->userId); 
						if($mark_details['coursePercentage']==100 && $mark_details['progressPercnt']>=55)
						{
							$certificate_status[$i] = 'passed'; 
						}
						else
						{
							$certificate_status[$i] = 'not_passed'; 
						}
					}
					else if($row->course_status == 3) 	// certificate applied
					{ 
						//$certificate_status[$i] = 'pending_approval'; 
						$certificate_status[$i] = 'passed';
					}
					else if($row->course_status == 4) 	// certificate isseued
					{ 
						$mark_details = $this->get_student_progress($row->courId,$row->userId); 
						//$certificate_status[$i] = 'issued';
						$certificate_status[$i] = 'passed';
										
					}
					else if($row->course_status == 5) 	// material access
					{ 
						$mark_details = $this->get_student_progress($row->courId,$row->userId); 
						if($mark_details['coursePercentage']==100 && $mark_details['progressPercnt']>=55)
						{
							$certificate_status[$i] = 'passed'; 
						}
						else
						{
							$certificate_status[$i] = 'not_passed'; 
						}	 		
						//$certificate_status[$i] = 'material_access'; 
						
						
					}
					else if($row->course_status == 6) 	// archieved
					{ 
						$mark_details = $this->get_student_progress($row->courId,$row->userId); 
						if($mark_details['coursePercentage']==100 && $mark_details['progressPercnt']>=55)
						{
							$certificate_status[$i] = 'passed'; 
						}
						else
						{
							$certificate_status[$i] = 'not_passed'; 
						}	 		
						
						//$certificate_status[$i] = 'archieved'; 		 		
					}
					else if($row->course_status == 7) 	// expired
					{ 
						
						// $certificate_status[$i] = 'expired'; 	
						$mark_details = $this->get_student_progress($row->courId,$row->userId); 
						if($mark_details['coursePercentage']==100 && $mark_details['progressPercnt']>=55)
						{
							$certificate_status[$i] = 'passed'; 
						}
						else
						{
							$certificate_status[$i] = 'not_passed'; 
						}	 		
					}
							
										
					/*------ Voucher details ----- */
					$this->db->select('*');
					$this->db->from('redeemed_coupons');
					$this->db->join('giftvoucher','coupon_code=giftVoucherCode');
					$this->db->where('user_id',$content['user_id']);
					$redeemed = $this->db->get();
					if($redeemed->num_rows()>=1)
					{
						$redeem_result = $redeemed->result();
						if(in_array($row->courId,explode(',',$redeem_result[0]->course_id)))
						{
						$content['redeem']['type'][$i] = "voucher";
						$content['redeem']['vcode'][$i] = $redeem_result[0]->coupon_code;
						$content['redeem']['req'][$i] = $redeem_result[0]->securitycode_req;
						if($redeem_result[0]->securitycode_req=='yes')
						{
							if(!empty($redeem_result[0]->redemption_code))
							{
								$content['redeem']['security'][$i] = $redeem_result[0]->redemption_code;
							}
														
							if(!empty($redeem_result[0]->pdf_name))
							{
								$content['redeem']['pdf'][$i] = $redeem_result[0]->pdf_name;
							}
							
							
						}
						else
						{
							$content['redeem']['mssage'][$i] = "Security code not required.";
						}
						}
						else
						{
							//echo 'enrolltype'.$row->enroll_type;
						  if(isset($row->enroll_type)&&$row->enroll_type=="admin added")
						  {
							 // echo "admin added";
							  $content['redeem']['type'][$i] = "admin_added";
							  
						  }
						  else
						  {
							  $content['redeem']['err_msg'][$i]="Enroll details are not availouble.";
						  }
						}
					}
					else
					{
					$this->db->select('*');
					$this->db->from('payments');
					$this->db->where('user_id',$content['user_id']);
					$payments = $this->db->get();
						if($payments->num_rows()>=1)
						{
							$pay_result = $payments->result();
							$content['redeem']['type'][$i] = "payment";
							$content['redeem']['tans_id'][$i] =$pay_result[0]->transaction_id; 
							
						}
						else if(isset($row->enroll_type)&&$row->enroll_type=="admin_added")
						{
							$content['redeem']['type'][$i] = "Admin added.";
							
						}
						else
						{
							$content['redeem']['err_msg'][$i]="Enroll details are not availouble.";
						}
					}
					
					
					$i++;
									
				}
				$base_courses =$this->user_model->get_courses($content['lang_id']);
				$nr=0;
				
				foreach($base_courses as $value)
				{
					if(in_array($value->course_id,$content['course_id']))
					{
						continue;
					}
					else
					{
						$content['not_reg_course_id'][$nr]=$value->course_id;
						$content['not_reg_course_name'][$nr]=$value->course_name;
						$nr++;
					}
				
				}
				
				$content['colour_wheel_subscription'] = $this->user_model->colour_wheel_subcribed($userId);
				
				
				
				$content['certificate_status'] =  $certificate_status;
				$data['view'] = 'studentDetails';
				$data['content'] = $content;
				
				$this->load->view('admin/template',$data);
	
		}	
	}
	
	function AddCourse($userId)
	{
	//	$this->load->model('user_model')		;
	//	$this->load->model('common_model');
	//	$this->load->model('user_model');
		$this->load->model('course_model');
		
		$content['userId']=$userId;
		
		if(isset($_POST['add_course']))
		{
			$cbArray = $this->input->post('cb');
			//echo "<pre>";print_r($cbArray);exit;
			if(!empty($cbArray))
			{
			for($z=0;$z<count($cbArray);$z++)
			{
				$dateNow =date('Y-m-d');
			$expirityDate = $this->user_model->findExpirityDate($cbArray[$z],$dateNow);
			$usersUnit = $this->user_model->get_courseunits_id($cbArray[$z]);
			$un=array();
			foreach($usersUnit as $row)
			{
				$un[$row->units_order] = $row->course_units;
			}
			$enrollDetails['student_course_units'] = serialize($un);
			
				$enrollDetails['user_id']=$userId;
				$enrollDetails['course_id']=$cbArray[$z];
				$enrollDetails['date_enrolled']=$dateNow;
				$enrollDetails['date_expiry']=$expirityDate;
				$enrollDetails['enroll_type']="admin added";
				$enrollDetails['course_status']="0";
				$courseEnrId = $this->user_model->add_course_student($enrollDetails);
			}
			}
			else
			{
				$this->session->set_flashdata("message","Please select a course to add.");
				redirect("admin/browsestudent/AddCourse/".$userId,"refresh");
			}
			
		}
		
		$currCourses = $this->user_model->get_course_stud($userId);
		$x=0;
		foreach($currCourses as $row2)
		{
			$content['currCourses'][$x] =$row2->course_id; 
			$x++;
		}
		$userLang = $this->course_model->get_lang_course($content['currCourses'][0]);
		$newArr = $this->common_model->get_base_courses($userLang);//change lang id regarding to student
		
		
		if(!empty($newArr))
		{
			
			$keys = array_keys($newArr);
			for($i=0;$i<count($newArr);$i++)
			{
				$content['courseId'][$i] = $keys[$i];
				$content['courseName'][$i] = $newArr[$keys[$i]];
			
				$modArr[$i] = $this->user_model->getCourseUnitListing_admin($userId,$content['courseId'][$i]);
				//echo "<pre>";print_r($modArr[$i]);echo $modArr[$i][$j]['unitName'];exit;
					for($j=0;$j<count($modArr[$i]);$j++)
					{
						$content['modules'][$i][$j] = $modArr[$i][$j]['unitName'];
						$j++;
					}
					//echo "<pre>";print_r($content['modules'][$i]);exit;
				
			}
			
		
			
		}
		$data['view'] = 'addStudentCourse';
		$data['content'] = $content;
				
		$this->load->view('admin/template',$data);
	
		
		
	}
	
	function users_ebook($user_id)
	{
		//ebook details 
		$this->load->model('ebook_model');
		$lang_id = $this->user_model->getlangIdByUserId($user_id);
		$content['ebooks']=$this->ebook_model->fetchEbookByLang($lang_id);
		$content['user_ebooks']=$this->ebook_model->suscribed_ebooks($user_id);
		$content['user_id'] = $user_id;
		
		//echo "<pre>";print_r($content['user_ebooks']);exit;
		
		if(isset($_POST['add_course']))
		{
			$cbArray = $this->input->post('cb');
			for($z=0;$z<count($cbArray);$z++)
			{
				$ebookids=implode(",",$cbArray);
			}
			
			$prdctId = $this->common_model->getProdectId('ebooks','',count($cbArray));
			$dateNow = date('Y-m-d');
			$subscriDetails['user_id'] 		 = $user_id;
			$subscriDetails['product_id']	  = $prdctId;
			$subscriDetails['ebook_id']		= $ebookids;
			$subscriDetails['date_purchased']  = $dateNow;
			//echo "<pre>";print_r($subscriDetails);exit;
			$subscriptionId = $this->ebook_model->addSubscription_user($subscriDetails);
			
			$this->session->set_flashdata('massage','Ebook(s) assigned to user successfully.');
			redirect('admin/browsestudent/users_ebook/'.$user_id);
		}
				 		
		
		
		
		$data['view'] = 'users_ebook';
        $data['content'] = $content;
        $this->load->view('admin/template',$data);
				
	}
	
	
	
	function generateExcel(){
		$this->load->library('export');
		//$this->load->model('user_model');
		//$sql = $this->user_model->get_courses(4);
		
		$sql = $this->student_model->student_excel();
		
		echo "<pre>";
		print_r($sql);
		exit;
		
			
		
		$this->export->to_excel($sql, 'student_report'); 
	}
	
	function excelExport(){
		
		$sql = $this->user_model->get_courses(4);
	   	$fields = (	$field_array[] = array ("ID", "Course Name", "Summary")  );
	   
	   	foreach ($sql as $row)
			 {
			 $data_array[] = array( $row->course_id, $row->course_name, $row->course_summary );
			 }
		
		$this->load->helper(array('php-excel'));	 
		   $xls = new Excel_XML;
		   $xls->addArray ($field_array);
		   $xls->addArray ($data_array);
		   $xls->generateXML ( "course_list" );
	}
	
	
	function mexcelExport2(){
		$this->load->helper(array('php-excel'));
		$sql = $this->student_model->student_excel();
	   	$fields = (	$field_array[] = array ("Sl No", "Student Name","Email","Course name","Date enrolled","Date expiry")  );
	  // $i=1;
	   	foreach ($sql as $row)
			 {
				 
			 $data_array[] = array($row->first_name,$row->first_name, $row->email, $row->course_name,$row->date_enrolled,$row->date_expiry );
			 //$i++;
			 }
			 
			 /*echo "<pre>";
			 print_r($field_array);
			 
			 echo "<pre>";
			 print_r($data_array);
			 exit;*/
			 
		  $xls = new Excel_XML;
		   $xls->addArray ($field_array);
		   $xls->addArray ($data_array);
		   $xls->generateXML ( "student_list" );
	}
	
	
	function launchToVC($userId)
	{
		$this->load->model('user_model');
		$status = $this->user_model->get_user_status($userId);
		if($status!=0)
		{
		$this->load->model('user_model');
		$sess_array = array('id' => $userId);
		$this->session->set_userdata('student_logged_in', $sess_array);
		$lang_id = $this->user_model->getlangIdByUserId($userId);
		$sess_array1 = array('language' => $lang_id);
		
		$this->session->set_userdata($sess_array1);
		
		redirect('coursemanager/campus','refresh');
		}
		else
		{
			redirect('home','refresh');
		}
	}
	
	
	function update_course_expiry_date($user_id,$course_id,$new_expiry_date)
	{
		
		
		$data 	= array();
		$update_data['date_expiry'] = $new_expiry_date;
		$update_data['course_status'] = '1';
		
		$this->student_model->update_expiry_date($user_id,$course_id,$update_data);
		
		
		$data['new_expiry_date']		= $new_expiry_date;	
		
		echo json_encode($data); 
				
	}
	
	
	function update_course_access_date($user_id,$course_id,$product_id)
	{
		
		$product_details = $this->common_model->get_product_details($product_id);
		
		$period = $product_details[0]->item_id;
		
		$data 	= array();
		
		
		
		$today = date("Y-m-d",time());
		
		$current_expiry_date = $this->student_model->get_expiry_date($user_id,$course_id);
		
		$new_expiry_date = date('Y-m-d', strtotime("+$period months", strtotime($current_expiry_date)));
		
		$update_data['date_expiry']   = $new_expiry_date;
		$update_data['course_status'] = '7';
		
		$this->student_model->update_course_access($user_id,$course_id,$update_data);
		
		$val['user_id']     = $user_id;
		$val['course_id']   = $course_id;
		$val['product_id']   = $product_id;
		$val['applied_on']  = $today;
		$val['expiry_date'] = $new_expiry_date;
		$val['status']	  = '1';
		
		$this->student_model->insert_course_access($val);
		
		
		echo json_encode($data); 
				
	}
	
	
	
	
	function certificate_download($course_id,$user_id)
  	{
		$this->load->model('certificate_model');
	  
	   $this->load->helper(array('dompdf', 'file'));
	   
  /*	 if(!$this->session->userdata('student_logged_in')){
		  redirect('home');
		}*/
	//$lang_id = $this->session->userdata('language');
	//$user_id=$this->session->userdata['student_logged_in']['id'];
		
	 //$this->load->library('encrypt');
	// $course_id = $this->encrypt->decode($course_id_encrypted);
	// $course_id = $course_id_encrypted;
	 $course_name = $this->common_model->get_course_name($course_id);
	 $user_details = $this->user_model->get_student_details($user_id); 
	 
	 
	/* 
	 echo "<pre>";
	 print_r($user_details);
	 exit;*/
	
	 foreach($user_details as $key => $value)
	 {
		$lang_id = $value->lang_id;
	 	$certificate_user_name = $value->first_name.'&nbsp;'.$value->last_name;		
	 }
	 $mark_details = $this->get_student_progress($course_id,$user_id);
	 
	 
	 //progressPercnt
	 
	 /*echo "<pre>";
	 print_r($mark_details);
	 exit;*/
	 $grade='failed';
	 	if($mark_details['coursePercentage'] >= 55 && $mark_details['coursePercentage'] <= 64.99)
	 	{
	 		$grade = $this->user_model->admin_translate_($lang_id,'mark_pass');
		}
		else if($mark_details['coursePercentage'] >= 65 && $mark_details['coursePercentage'] <= 74.99)
	 	{
	 		$grade = $this->user_model->admin_translate_($lang_id,'mark_pass_plus');
		}
		else if($mark_details['coursePercentage'] >= 75 && $mark_details['coursePercentage'] <= 84.99)
	 	{
	 		$grade = $this->user_model->admin_translate_($lang_id,'mark_merit');
		}
		else if($mark_details['coursePercentage'] >= 85 )
	 	{
	 		$grade = $this->user_model->admin_translate_($lang_id,'mark_dist');
		}
		
		$certificate_details =  $this->certificate_model->get_certificate_details($user_id,$course_id);
		foreach($certificate_details as $key => $value)
		{
			$applied_date = $value->applied_on;	
			$certificate_id = $value->id;	
		}
		
		$values = explode('-', $applied_date);
		
	if($values[1]=='1')
   {
       $month=$this->user_model->admin_translate_($lang_id,'month_1');
   }
   else if($values[1]=='2')
   {
      $month=$this->user_model->admin_translate_($lang_id,'month_2');
   }
   else if($values[1]=='3')
   {
      $month=$this->user_model->admin_translate_($lang_id,'month_3');
   }else if($values[1]=='4')
   {
      $month=$this->user_model->admin_translate_($lang_id,'month_4');
   }else if($values[1]=='5')
   {
      $month=$this->user_model->admin_translate_($lang_id,'month_5');
   }else if($values[1]=='6')
   {
      $month=$this->user_model->admin_translate_($lang_id,'month_6');
   }else if($values[1]=='7')
   {
      $month=$this->user_model->admin_translate_($lang_id,'month_7');
   }else if($values[1]=='8')
   {
      $month=$this->user_model->admin_translate_($lang_id,'month_8');
   }else if($values[1]=='9')
   {
      $month=$this->user_model->admin_translate_($lang_id,'month_9');
   }else if($values[1]=='10')
   {
      $month=$this->user_model->admin_translate_($lang_id,'month_10');
   }else if($values[1]=='11')
   {
      $month=$this->user_model->admin_translate_($lang_id,'month_11');
   }else
   {
      $month=$this->user_model->admin_translate_($lang_id,'month_12');
   }
	$year=$values[0];
		
		
	/* Style Me course */
		
		if($lang_id==4 && $course_id==1)
		{
		//	$class='outer_cert_spanish_styleme';
			$courseTitle = 'Personal Fashion Styling';

		//	$coursename=$this->user_model->translate_('cert_styleme');
			$coursename= 'Style Me Course';
		}
		else if($lang_id==3 && $course_id==11 )
		{
		//	$class='outer_cert_english_styleme';
			$courseTitle = 'Autoimagen';
			//$coursename=$this->user_model->translate_('cert_styleme');
			$coursename='Estilista personal';
		}
		/* End Style Me course */	
		
		/* Style You course */

		else if($lang_id==4 && $course_id==2 )
		{ 
		//$class='outer_cert_english_styleyou';
		//$coursename=$this->user_model->translate_('cert_styleyou');
		$courseTitle = 'Professional Fashion Styling';
		$coursename = 'Style You Course';
		}
		else if($lang_id==3 && $course_id==12 )
		{
		//$class='outer_cert_spanish_styleyou';
		$courseTitle = 'Personal Shopper';
		//$coursename=$this->user_model->translate_('cert_styleyou');
		$coursename = 'Estilista profesional';
		}
		
		/* End Style You course */
		
		/* Make Up course */

		else if($lang_id==4 && $course_id==3 )
		{
	//	$class='outer_cert_english_makeup';
		$courseTitle = 'Make Up Artistry';
		$coursename=$this->user_model->translate_('cert_makeup');
		$coursename = 'Make Up Course';
		}
		else if($lang_id==3 && $course_id==13)
		{
		//$class='outer_cert_spanish_makeup';
		$courseTitle = 'Make-up';
		//$coursename=$this->user_model->translate_('cert_makeup');
		$coursename = 'Maquillaje';
		}
		
		/* End Make Up course */
		/* Wedding Planner course */

		else if($lang_id==4 && $course_id==4 )
		{
		//$class='outer_cert_english_wedding';
		$courseTitle = 'Wedding Planning';
		//$coursename=$this->user_model->translate_('cert_wedding');
		$coursename='Wedding Planner Course';
		}
		
		else if($lang_id==3 && $course_id==14 )
		{
		//$class='outer_cert_spanish_wedding';
		$courseTitle = 'Wedding Planner';
		//$coursename=$this->user_model->translate_('cert_wedding');
		$coursename='Organización de bodas';
		}
		
		/* End Wedding Planner course */
		
		
		/* Nail artist course */
		else if($lang_id==4 && $course_id==5)
		{
		//$class='outer_cert_english_hair';
		$courseTitle = 'Nail Artist';
		//$coursename=$this->user_model->translate_('cert_hair');
		$coursename='Nail Artist Course';
		}
		else if($lang_id==3 && $course_id==15 )
		{
		//$class='outer_cert_spanish_hair';
		$courseTitle = 'Nails Art';
		//$coursename=$this->user_model->translate_('cert_hair');
		$coursename='Estilista de uñas';
		}
		
		/* End Nail artist course */
		
		
		
		/* Hair Stylist course */

		else if($lang_id==4 && $course_id==6 )
		{
		//$class='outer_cert_english_hair';
		$courseTitle = 'Hair Stylist';
		//$coursename=$this->user_model->translate_('cert_hair');
		$coursename='Hair Stylist Course';
		}
		else if($lang_id==3 && $course_id==16 )
		{
		//$class='outer_cert_spanish_hair';
		$courseTitle = 'Hair Stylist';
		//$coursename=$this->user_model->translate_('cert_hair');
		$coursename='Estilista del cabello';
		}
		/* End Hair Stylist course */
		
		
		
		//$cssLink = base_url();
		if($lang_id==3)
		{
			$cssLink = "public/certificate/css/certificate-style_new_spanish.css";
			$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>certificate</title>
<link href="'.$cssLink.'" type="text/css" rel="stylesheet" />
</head>

<body>
<div class="raper">
<div id="certificate">
<h1>'.$courseTitle.'</h1>
<div class="clear"></div>
<h2 class="stname">El presente certifica que</h2>
<div class="clear"></div>
<h2>'.$certificate_user_name.'</h2>
<div class="clear"></div>
<div class="course">ha completado satisfactoriamente
el curso de</div>
<div class="clear"></div>
<h3>'. $coursename.'</h3>
<div class="clear"></div>
<h4><span>Grado Trendimi:</span>' .$grade.'</h4>
<div class="clear"></div>
<h5 class="number">Número del certificado</h5>
<div class="clear"></div>
<h5> 100-'.$certificate_id.'</h5>
<div class="clear"></div>
<table>
<tr>
<td>
<p>Fecha de adjudicación</p>
<p>'.$month.' '.$year.'</p>
</td>
<td>
<h6>Francisca Tomas</h6>
<h6>CEO</h6>
</td>
<tr>
</table>
<div class="clear"></div>
<blockquote>Este curso está acreditado por el <i>International Council for Online Educational Standards</i> (ICOES)</blockquote>
</div>
</div>
</body>
</html>
';
		}
		else if($lang_id==4)
		{
			$cssLink = "public/certificate/css/certificate-style_new_english.css";
			//$cssLink = "http://trendimi.net/public/certificate/css/certificate-style_new_2.css";
			
			$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>certificate</title>
<link href="'.$cssLink.'" type="text/css" rel="stylesheet" />
</head>

<body>
<div class="raper">
<div id="certificate">
<h1>'.$courseTitle.'</h1>
<div class="clear"></div>
<h2 class="stname">This is to certify that</h2>
<div class="clear"></div>
<h2>'.$certificate_user_name.'</h2>
<div class="clear"></div>
<div class="course">has successfully completed Trendimi’s</div>
<div class="clear"></div>
<h3>'. $coursename.'</h3>
<div class="clear"></div>
<h4><span>Trendimi Grade:</span>' .$grade.'</h4>
<div class="clear"></div>
<h5 class="number">Certificate Number</h5>
<div class="clear"></div>
<h5> 100-'.$certificate_id.'</h5>
<div class="clear"></div>
<table>
<tr>
<td>
<p>Date of Award</p>
<p>'.$month.' '.$year.'</p>
</td>
<td>
<h6>Francisca Tomas</h6>
<h6>CEO</h6>
</td>
<tr>
</table>
<div class="clear"></div>
<blockquote><i>This course is accredited by the International Council for Online Educational Standards (ICOES)</i></blockquote>
</div>
</div>
</body>
</html>
';
		}

		

/*echo $html;
		exit;*/
	 $data = pdf_create($html, 'TrendimiCertificate_'.$user_id.'_'.$course_id);
     //or
     //$data = pdf_create($html, '', false);
     write_file('name', $data);	
		
		
  }
	
	
	
	
	
	
	function get_student_progress($course_id,$stud_id)
	{
		/*if(!$this->session->userdata('student_logged_in')){
		  redirect('home');
		}*/
		//$stud_id=$this->session->userdata['student_logged_in']['id'];
		$course_status = $this->user_model->get_student_course_status($course_id,$stud_id); 	
		$course_name = $this->common_model->get_course_name($course_id); 
					
		 /*-----------------Start marks,progress calculations------------------------*/
					
					
		if($course_status!=0) // course started
		{		 
		  $courseUnitArray= $this->user_model->getCourseUnitListing_admin($stud_id,$course_id,1); 
		 // 	echo $this->session->userdata['student_logged_in']['id']."<br>-------------------<br><pre>";print_r( $courseUnitArray);echo "</pre>";
		  $total_module = count($courseUnitArray);
		  if(!empty($courseUnitArray)) {
			$unitSlno            = 0;     
			$completedMarks1     = 0;
			$completedMarks2     = 0; 
			$countCompleted      = 0;
			$countTotal          = 0; 
			$completedPercentage = 0;   
			foreach($courseUnitArray as $key=> $courseUnitArr) { 
			  $percentage    = 0;
			 
			  $unitId        = $courseUnitArr['course_units_idcourse_units'];			 
			  
			  //whether the unit is completed or not by checking the pages in the unit
			  $unitComplete  = $this->user_model->getUnitCompleteByUser_unit($stud_id,$unitId,$course_id); 
			  //  total tasks in the unit
			  $taskArray     = $this->user_model->getTasksInUnit($unitId);
			  //echo "<br>-------------------<br><pre>";print_r( $taskArray);echo "</pre>";
			  $totalTask     = count($taskArray);
			  //  tasks in the unit which is attended by user
			  $userTaskArray = $this->user_model->getTasksForUserInUnit($stud_id,$unitId,$course_id); 
			 
			  $totalTaskUser = count($userTaskArray);
			  //the marks obtained by user in a particular unit in a course
			  $marksDetails  = $this->user_model->getUnitMarksForTasks($stud_id,$unitId,$course_id); 
			  //echo "<br>-------------------<br><pre>";print_r( $marksDetails);echo "</pre>";
			                 
			  if(!empty($marksDetails)) {
				  $totalMarks      = $marksDetails['totalMarks'];
				  $totalQuestions  = $marksDetails['totalQuestions'];
				$completedMarks1 = $completedMarks1+$totalMarks ;
				$completedMarks2 = $completedMarks2+$totalQuestions ;
				$markPerc        = @($totalMarks/$totalQuestions)*100;
				if($markPerc!=''){
				  $percentage=@round($markPerc,2);
				}
				
			  } 
			  if($unitComplete==1) {
				$countCompleted++;
			  }
			  //make array contain details for the unit with the unit id as index
			  $unitMarkArray[$unitId]['percentage']          = $percentage;
			  $unitMarkArray[$unitId]['complete']            = $unitComplete;
			  $unitMarkArray[$unitId]['totalTask']           = $totalTask;        
			  $unitMarkArray[$unitId]['totalTaskUser']       = $totalTaskUser;  
			  $unitMarkArray[$unitId]['totalTaskArray']      = $taskArray;        
			  $unitMarkArray[$unitId]['totalTaskUserArray']  = $userTaskArray;          
			  $countTotal++;  
			}
		  }
		 $coursePercentage2=@($completedMarks1/$completedMarks2)*100;
		  //$coursePercentage1 = $coursePercentage2/$total_module;
		  $coursePercentage=@round($coursePercentage2,2);
		 // exit;
		  $unitsIdArr = $this->user_model->getCourseUnitListing_admin($stud_id,$course_id,1); 
		  
		  $totalUnit=count($unitsIdArr);
		  $valueArr=array();         
		  for ($unitCnt=0;$unitCnt<count($unitsIdArr);$unitCnt++) {
			$unitDetailsArr[] = $this->user_model->get_courseunits($unitsIdArr[$unitCnt]['course_units_idcourse_units']	);
			$pageIdsArr = array();
			$pageIdsArr = $this->user_model->getPageIdsForUnits($unitsIdArr[$unitCnt]['course_units_idcourse_units']);
			$studentPageIdsArr = $this->user_model->getStudentProgressPageIds($stud_id,$unitsIdArr[$unitCnt]['course_units_idcourse_units'],$course_id);
			$pageDiffArr = array();
			$progressPercnt=0;
			if (is_array($pageIdsArr) && is_array($studentPageIdsArr)) {
			  $pageDiffArr = array_diff($pageIdsArr,$studentPageIdsArr); 
			  $pageDiffCnt = count($pageDiffArr); 
			  $totalPageCnt = count($pageIdsArr);
			  $progressPercnt = round( ($totalPageCnt - $pageDiffCnt) * 100 / $totalPageCnt );
			}   
			else {
			  $progressPercnt = "0";
			}
			$valueArr[]=$progressPercnt;
		  }
		  
		  $total=array_sum($valueArr); 
		$totalpageattended=round($total/$totalUnit); 
		$x=0;
		$y=count($valueArr);
		foreach($valueArr as $val){
			$x=$x+$val;
		}
		$progressPercnt=$x/$y;
		
		}
		else // course not started
		{
			$progressPercnt=0;
			$coursePercentage=0;
		}
		  /*-----------------End marks,progress calculations------------------------*/
		  
		  /*----------------- Remaining days calculations------------------------*/
		  
		  $userCoursesArr=$this->user_model->getcourses_student_expiry($stud_id,$course_id);

		 $content['coursename'] = $course_name; 
		  if(!empty($userCoursesArr)){
			$courseDetails1 = $this->user_model->getstudent_courseaccess($stud_id,$course_id); 
			$now = time(); // or your date as well
			if($courseDetails1==''){
			  $accessdate_exp='';
			}
			else{
			  $accessdate_exp=$courseDetails1[0]->access_date_expiry;
			}
	
			if($accessdate_exp=='')
			{      
			  $your_date = strtotime($userCoursesArr[0]->date_expiry);
			  $datediff = $your_date - $now;
			  $numberOfDaysRemaining =  ceil($datediff/(60*60*24)); 
			}
			else
			{
			  $your_date = strtotime($accessdate_exp);
			  $datediff = $your_date - $now;
			  $numberOfDaysRemaining =  ceil($datediff/(60*60*24));
			}
			if($numberOfDaysRemaining < 0)
			{
			  $numberOfDaysRemaining = 0;
			}
		  }
		
		 /*----------------- End remaining days calculations------------------------*/
		
		$progressPercnt=@round($progressPercnt,0);
		$progress['course_id']        = $course_id;
		$progress['course_name']      = $course_name;
		$progress['coursePercentage'] = $coursePercentage;
		$progress['progressPercnt']   = $progressPercnt;
		//$progress['daysremaining']    = $numberOfDaysRemaining;
		
		
		
		return $progress;
		
	
	
	}
	
	function course_unit_list($course_id,$user_id)
	{
		/*echo "User id ".$user_id;
		echo "<br>Course id ".$course_id;*/
		
		$content = array();
		$percentage = array();
		 $courseUnitArray= $this->user_model->getCourseUnitListing_admin($user_id,$course_id,1); 
		 
		 /*echo "<pre>";
		 print_r($courseUnitArray);
		 exit;*/
		 $content['course_id'] = $course_id;
		 $content['user_id'] = $user_id;
		 $content['courseUnitArray'] = $courseUnitArray;
		 
		$course_status = $this->user_model->get_student_course_status($course_id,$user_id); 	
		$course_name = $this->common_model->get_course_name($course_id); 
		
		$content['course_name'] = $course_name;		
		 /*-----------------Start marks,progress calculations------------------------*/
					
			
		if($course_status!=0) // course started
		{		 
		  $courseUnitArray= $this->user_model->getCourseUnitListing_admin($user_id,$course_id,1); 
		 // 	echo $this->session->userdata['student_logged_in']['id']."<br>-------------------<br><pre>";print_r( $courseUnitArray);echo "</pre>";
		  if(!empty($courseUnitArray)) {
			
			$completedMarks1     = 0;
			$completedMarks2     = 0; 
			$k =0 ;	
			
			foreach($courseUnitArray as $key=> $courseUnitArr) { 
			  $percentage    = 0;
			  
			  
			 
			  $unitId        = $courseUnitArr['course_units_idcourse_units'];
			  
			  $unit_id[$k] = $unitId;			 
			  
			  
			 // echo "<br>Unit id  ".$unitId;
			  //whether the unit is completed or not by checking the pages in the unit
			  $unitComplete  = $this->user_model->getUnitCompleteByUser_unit($user_id,$unitId,$course_id); 
			  //  total tasks in the unit
			  $taskArray     = $this->user_model->getTasksInUnit($unitId);
			 // echo "<br>-------------------<br><pre>";print_r( $taskArray);echo "</pre>";
			  $totalTask     = count($taskArray);
			  //  tasks in the unit which is attended by user
			  $userTaskArray = $this->user_model->getTasksForUserInUnit($user_id,$unitId,$course_id); 
			  /*echo "<br>-------------------<br><pre>";print_r( $userTaskArray);echo "</pre>";
			  exit;*/
			  $totalTaskUser = count($userTaskArray);
			  //the marks obtained by user in a particular unit in a course
			  $marksDetails  = $this->user_model->getUnitMarksForTasks($user_id,$unitId,$course_id); 
			  //echo "<br>-------------------<br><pre>";print_r( $marksDetails);echo "</pre>";
			                 
			  if(!empty($marksDetails)) {
				  $totalMarks      = $marksDetails['totalMarks'];
				  $totalQuestions  = $marksDetails['totalQuestions'];
				$completedMarks1 = $completedMarks1+$totalMarks ;
				$completedMarks2 = $completedMarks2+$totalQuestions ;
				$markPerc        = @($totalMarks/$totalQuestions)*100;
				if($markPerc!=''){
				  $marks_details[$k]['percenatge'] = @round($markPerc,2);
				  //echo "<br> Percentage ".$percentage;
				}
				 else
			  	{
				  $marks_details[$k]['percenatge'] = 0;
				 // echo "<br> Percentage 0%";
			  	}
			 
				
			  } 
			  else
			  {
				  $marks_details[$k]['percenatge'] = 0;
				 // echo "<br> Percentage 0%";
			  }
			 
			 $k++;
			 
			}
			$content['marks_details'] = $marks_details;
			$content['unit_id'] = $unit_id;
			/*echo "<pre>";
			print_r($content['percentage']);
			exit;*/
			
		  }
		 	//$data['refferer'] = $this->input->server('HTTP_REFERER', TRUE);
		 
		  $data['view'] = 'course_unit_list';		 
		  $data['content'] = $content;				
		  $this->load->view('admin/template',$data);
		  		  
		
		}
		
		
		
		
		
	}
	
	function reset_unit_task()
	{
				
		$user_id = $this->uri->segment(4);
		$course_id = $this->uri->segment(5);
		$unit_id = $this->uri->segment(6);
		
		/*echo "<br>user_id ".$user_id;
		echo "<br>course_id ".$course_id;
		echo "<br>unit_id ".$unit_id;*/
		
		  $userTaskArray = $this->user_model->getTasksForUserInUnit($user_id,$unit_id,$course_id); 
		  
		 /* echo "<pre>";
		  print_r($userTaskArray);
		 exit;*/
		
		
		
		$this->student_model->reset_course_records($user_id,$course_id,$unit_id);
		
		for($k=0;$k<count($userTaskArray);$k++)
		{
			$this->student_model->reset_student_scores($user_id,$course_id,$userTaskArray[$k]['task_id'],$userTaskArray[$k]['page_id']);
//			$k++;
		}
		
			redirect('admin/browsestudent/details/'.$user_id, 'refresh');		
		
	}
	
function noteslist($user_id){
  $content = array();
        if(isset($this->flashmessage))
        $data['flashmessage'] = $this->flashmessage;
  $content['searchmode'] = false;
  $content['user_id']=$user_id;
        $data['view'] = 'noteslist';
        $data['content'] = $content;
        
        $this->load->view('admin/template',$data);
  
 }
 
 
 function fetchnotes($user_id){
        
        $page = 1; // The current page
  $sortname = 'id ';  // Sort column
  $sortorder = 'desc';  // Sort order
  $qtype = '';  // Search column
  $query = '';  // Search string
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
  $this->db-> from('user_notes');
  $this->db-> where('user_id',$user_id);
        $query = $this->db->get();
       
        $data = array();
  $data['page'] = $page;
  $data['total'] = $query -> num_rows();
  $count=0;
        if($query -> num_rows() >0 )
  {
   $result=$query->result();
   foreach($result as $row)
   {
    $count=$count+1;
    $notes_date=$row->notes_date;
     
  $edit = '<a href="'.base_url().'admin/browsestudent/edit_notes/'.$row->id.'" target="blank">Edit</a>';
       
    $data['rows'][] = array(
    'id' => $row->user_id,
    'cell' => array($count,$row->notes,$notes_date,$edit)
   );
   }
   
  }
        
        
       
       // $pagelist = $this->getpagelist($lang);
        
        
        
  
       echo json_encode($data); exit(); 
         
 }
 function add_notes($user_id)
 {
 
  $content = array();
  $content['user_id']=$user_id;
  // field name, error message, validation rules
  if(isset($_POST['save_note']))
  {
   $notes_data  = array(); 
   $notes_data['user_id']=$user_id;
      $notes_data['notes']   = $content['notes'] = $this->input->post('notes');
   $time = time (); 
   //echo $time;exit;
   $note_date=date("Y-m-d H:i:s",$time);
   //echo $note_date;exit;
   $notes_data['notes_date'] = $content['notes_date'] = $note_date;
   
   $this->form_validation->set_rules('notes', 'Note', 'required');   

   if($this->form_validation->run())
   {
    
     $this->student_model->add_notes($notes_data);
     redirect('admin/browsestudent/noteslist/'.$user_id, 'refresh');
   }
   
  } 
  $data['view'] = 'notes_add';
  //$content['language']=$this->common_model->get_languages(); 
  $data['mode'] = 0; 
  $data['content'] = $content;
  $this->load->view('admin/template',$data);
 
 }
 
 function edit_notes($id)
 {
 
  $content = array();
  $content['id']=$id;
  
  $this->db-> select('*');
  $this->db-> from('user_notes');
  $this->db-> where('id',$id);
        $query = $this->db->get();
   if($query -> num_rows() >0 )
  {
   $result=$query->result();
   foreach($result as $row)
   {
    $content['notes']=$row->notes;
    $content['user_id']=$row->user_id;
    
   }
   
     
  // field name, error message, validation rules
  if(isset($_POST['save_note']))
  {
   $notes_data  = array(); 
      $notes_data['notes']   = $content['notes'] = $this->input->post('notes');
   $time = time (); 
   //echo $time;exit;
   $note_date=date("Y-m-d H:i:s",$time);
   //echo $note_date;exit;
   $notes_data['notes_date'] = $content['notes_date'] = $note_date;
   
   $this->form_validation->set_rules('notes', 'Note', 'required');   

   if($this->form_validation->run())
   {
    
     $this->student_model->update_notes($notes_data,$id);
     redirect('admin/browsestudent/noteslist/'.$content['user_id'], 'refresh');
   }
   
  } 
  $data['view'] = 'notes_add';
  //$content['language']=$this->common_model->get_languages(); 
  $data['mode'] = 1; 
  $data['content'] = $content;
  $this->load->view('admin/template',$data);
 
 }
 }
	
	function send_activation_mail($user_id)
	{				
			$tempArray = $this->user_model->get_student_details($user_id);
			//$newArr = $tempArray->row();
			foreach($tempArray as $row)
			{
				$studentdata  = array();
				$studentdata['first_name'] = $row->first_name;
				$studentdata['last_name'] =$row->last_name;
				$studentdata['email'] =$row->email;
				$studentdata['username'] = $row->username;
				$studentdata['password'] = $row->password;
				$studentdata['gender'] = $row->gender;
				$studentdata['contact_number'] = $row->contact_number;
				$studentdata['house_number'] = $row->house_number;		 
				$studentdata['address'] = $row->address;
				$studentdata['street'] = $row->street;
				$studentdata['zipcode'] = $row->zipcode;
				$studentdata['city'] = $row->city;
				$studentdata['country_id'] = $row->country_id;				
				$studentdata['lang_id'] = $row->lang_id;
				$studentdata['status']=0;
				$langId = $row->lang_id;
			}
			
			
			/*$currCourses = $this->user_model->get_course_stud($user_id);		
			$langId = $this->course_model->get_lang_course($content['currCourses'][0]);		*/	
			//$langId = $this->course_model->get_lang_course($courseId);
		
				$this->load->library('email');
				$this->load->model('email_model');
				$this->load->library('encrypt');
				
				$en_studId = $this->encrypt->encode($user_id); //encoding student id				
				$row_new = $this->email_model->getTemplateById('new_registration',$langId);
				
				foreach($row_new as $row1)
				{
					
					$emailSubject = $row1->mail_subject;
					$mailContent = $row1->mail_content;
				}
				if($langId==3)
				{
				 	$mailContent = str_replace ( "#firstname#",$studentdata['first_name'], $mailContent );
					$mailContent = str_replace ( "#click here#","<a href='http://trendimi.net/home/studentActivation/".$en_studId."'>clica aquí</a>", $mailContent );
					
					$mailContent = str_replace ( "#actlink#","http://trendimi.net/home/studentActivation/".$en_studId." ",$mailContent );
					$mailContent = str_replace ( "#url#", "<a href='http://trendimi.net/'>Trendimi</a>", $mailContent );
					$mailContent = str_replace ( "#username#", $studentdata['username'], $mailContent );
					$mailContent = str_replace ( "#password#", $this->encrypt->decode($studentdata['password']), $mailContent );
				}
				else
				{
					$mailContent = str_replace ( "#firstname#",$studentdata['first_name'], $mailContent );
					$mailContent = str_replace ( "#click here#","<a href='http://trendimi.net/home/studentActivation/".$en_studId."'>click here</a>", $mailContent );
					
					$mailContent = str_replace ( "#actlink#","http://trendimi.net/home/studentActivation/".$en_studId." ",$mailContent );
					$mailContent = str_replace ( "#url#", "<a href='http://trendimi.net/'>Trendimi</a>", $mailContent );
					$mailContent = str_replace ( "#username#", $studentdata['username'], $mailContent );
					$mailContent = str_replace ( "#password#", $this->encrypt->decode($studentdata['password']), $mailContent );
				}
				  
				  
					$tomail = $studentdata['email'];
					
					$this->email->from('info@trendimi.net', 'Team Trendimi');
					$this->email->to($tomail); 
					$this->email->cc(''); 
					$this->email->bcc(''); 
					$this->email->subject($emailSubject);
					$this->email->message($mailContent);						  
					$this->email->send();	
					$this->session->set_flashdata('message', 'Activation mail sent');
					//echo "Mail sent to ".$tomail;
					
					redirect('admin/browsestudent/studentlist/', 'refresh');		
	}
	
	
	/*Below function is for genarating exel For devolopers*/
	
	
		
		function exel_gen(){
		
		if($_POST)
		{
		$f_name=$this->input->post('fname');
		$l_name=$this->input->post('lname');
		$u_name=$this->input->post('uname');
		$end_date=$this->input->post('end');
		$start_date=$this->input->post('start');
		$e_mail=$this->input->post('email');
		$country_id=$this->input->post('country');
		$course_id=$this->input->post('course');
		$v_code=$this->input->post('vcode');
		$v_web=$this->input->post('vweb');
		$stat=$this->input->post('stat');
		$user_stat=$this->input->post('user_stat');
		$buttonAction = $this->input->post('search');
		
		
		
	
		$content['fname'] = isset($f_name)?$f_name:'';
		$content['lname'] = isset($l_name)?$l_name:'';
		$content['uname'] = isset($u_name)?$u_name:'';
		$content['end'] = isset($end_date)?$end_date:'';
		$content['start'] = isset($start_date)?$start_date:'';
		$content['email'] = isset($e_mail)?$e_mail:'';
		$content['country'] = isset($country_id)?$country_id:'';
		$content['course'] = isset($course_id)?$course_id:'';
		$content['vcode'] = isset($v_code)?$v_code:'';
		$content['vweb'] = isset($v_web)?$v_web:'';
		$content['stat'] = isset($stat)?$stat:'';
		$content['user_stat'] = isset($user_stat)?$user_stat:'';
		$content['end_date'] = isset($end_date)?$end_date:'';
		$content['start_date'] = isset($start_date)?$start_date:'';
		
		if($buttonAction=='Generate Excel')
		{
			//echo "<pre>";print_r($content);
		$this->db->start_cache();
	
		if(isset($content['end_date']) && $content['end_date']!=''){
			$this->session->set_flashdata('end_date',$content['end_date']); 

		}
		if(isset($content['start_date']) && $content['start_date']!=''){
			$this->session->set_flashdata('start_date',$content['start_date']);
		}
		if(isset($content['stat']) && $content['stat']!=''){
			$this->session->set_flashdata('stat',$content['stat']); 

		}
		if(isset($content['user_stat']) && $content['user_stat']!=''){
			$this->session->set_flashdata('user_stat',$content['user_stat']); 

		}
		if(isset($content['course']) && $content['course']!=''){
			$this->session->set_flashdata('course',$content['course']);
		}
		
		$this->db->stop_cache();
		redirect('deeps_home/genarator_developers');
		//redirect('deeps_home/genarator_students_with_pass');
		}
		
		}
		
		
		$content['country_list']=$this->student_model->get_country();
		$content['course_list']=$this->student_model->get_course();
				
		$data['view'] = 'exel_gen';
		
        $data['content'] = $content;
        
        $this->load->view('admin/template',$data);

	
	}
	
	
	function update_newsletter($user_id)//ajax function
	{
		$userDetails = $this->user_model->get_stud_details($user_id);
		if($userDetails[0]->newsletter=='yes')
		{
			$this->db->set('newsletter','no');
			$msg="Successfully unsuscribed to newsletter.";
		}
		else
		{
			$this->db->set('newsletter','yes');
			$msg="Successfully suscribed to newsletter.";
		}
		$this->db->where('user_id',$user_id);
		$this->db->update('users');
		echo $msg;
	}
	
	function add_colour_wheel_to_student($user_id)
	{
		$data = array();
		 $today = date("Y-m-d");
		 $insert_data=array("user_id"=>$user_id,"course_id"=>'',"type"=>'colour_wheel_soft',"date_applied"=>$today);
		 
		 $this->user_model->insertQuerys("user_subscriptions",$insert_data);
		// $data['suc'] = 'Colour wheel added to student account';
		 echo json_encode($data);
		 exit(); 
		 
	}
	
	function swap_course($user_id,$course_id,$newCourseId)//ajax function
	{
		$dateNow = date('Y-m-d');
		$swap_details['user_id'] =$user_id;
		$swap_details['date'] =$dateNow;
		$swap_details['from_course'] =$course_id;
		$swap_details['to_course'] =$newCourseId;
		
		$this->db->insert('swap_details',$swap_details);
		$insert_id= $this->db->insert_id();
		
		if(isset($insert_id))
		{
		$usersUnit = $this->user_model->get_courseunits_id($newCourseId);
			foreach($usersUnit as $row)
			{
				$un[$row->units_order] = $row->course_units;
			}
			$student_courseData['student_course_units'] = serialize($un);
			$student_courseData['course_id'] = $newCourseId;
			 
			// echo "<pre>";print_r($student_courseData);
			 $this->user_model->update_student_enrollments($course_id,$user_id,$student_courseData);
		
		$data['class']="n_ok";
		$data['text']="Course swaped successfully.";
		}
		else
		{
			$data['class']="n_warning";
		$data['text']="Error! Swapping failed.Please try after some time.";
		}
		echo json_encode($data);
	}
}
