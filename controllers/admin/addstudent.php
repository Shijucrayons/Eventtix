<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class addstudent extends CI_Controller {

	 
	function __construct()
	{
		parent::__construct();
		$this->load->model('student_model','',TRUE);
		$this->load->model('common_model','',TRUE);
		$this->load->model('user_model','',TRUE);
		
		if(!$this->session->userdata('admin_logged_in'))
   		redirect('admin/login', 'refresh');
   		
   		if($message = $this->session->flashdata('message')){
          $this->flashmessage =$message;
   		}

   		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		$this->load->library('encrypt');
		
		
    }
	function index()
	{

	}

	function add($lang_id ='4')
	{	
	$this->load->model('course_model');
	  
		$content = array();
		

		
		// field name, error message, validation rules
		if(isset($_POST['fname']))
		{
		
		
			$studentdata  = array();
			
		    $studentdata['first_name'] = $content['fname'] = $this->input->post('fname');
		    $studentdata['last_name'] = $content['lname'] = $this->input->post('lname');
		//    $studentdata['certificateName'] = $content['namecert'] = $this->input->post('namecert');
		    $studentdata['gender'] = $content['gender'] = $this->input->post('gender');
		    $studentdata['dob'] = $content['dob'] = $this->input->post('dob');
		    $studentdata['street'] = $content['street'] = $this->input->post('street');
		    $studentdata['city'] = $content['city'] = $this->input->post('city');
			$studentdata['zipcode'] = $content['zipCode'] = $this->input->post('zipCode');
			$studentdata['country_id'] = $content['country_set'] = $this->input->post('country');
			$studentdata['email'] = $content['email'] = $this->input->post('email');
			$studentdata['contact_number'] = $content['contact_no'] = $this->input->post('contact_no');
			$studentdata['status']='1';
			
		//	$content['terms'] = $this->input->post('terms');
		//	$studentdata['newsletter'] = $content['cb_newsletter'] = $this->input->post('cb_newsletter');
		
			$usersUnit = $this->user_model->get_courseunits_id($this->input->post('course'));
			foreach($usersUnit as $row)
			{
				$un[$row->units_order] = $row->course_units;
			}
			$student_course_data['student_course_units'] = serialize($un);
			$student_course_data['course_id'] = $content['course_set'] = $this->input->post('course');
			$student_course_data['date_enrolled']= $studentdata['reg_date']=date("Y-m-d");
			$student_course_data['course_status'] = '0';
			$student_course_data['enroll_type'] ='admin added';
			
			$langId = $this->course_model->get_lang_course($student_course_data['course_id']);
			$studentdata['lang_id']=$langId;
			
			
			
			//$studentdata['user_type_iduser_type']=1;
			/*$coursename=$this->student_model->get_courseval($content['course_set']);
      		foreach ($coursename as $key) {
        		$studentdata['course_validity']=$key->course_validity;
      		}*/
			
			if ($this->input->post('fname')) {
			
				$this->student_model->do_upload();
				$image_data = $this->upload->data();
				$studentdata['user_pic']=$content['userfile']=$image_data['file_name'];
			}
		
			
		
			//$this->load->view('admin/add_atudent', $data);
		
			
			

		    $this->form_validation->set_rules('fname', 'First name', 'trim|required');
			$this->form_validation->set_rules('lname', 'Last Name', 'required');
			$this->form_validation->set_rules('language', 'Lanaguage', 'required');
			//$this->form_validation->set_rules('namecert', 'Name to be appeared on certificate', 'required');
			$this->form_validation->set_rules('gender', 'Gender', 'required');
			$this->form_validation->set_rules('dob', 'Date of Birth', 'required');
			
			$this->form_validation->set_rules('country', 'Country', 'callback_validate[Country]');
			$this->form_validation->set_rules('email', 'E-mail', 'required|valid_email');
			$this->form_validation->set_rules('course', 'Course', 'callback_validate[Course]');
			//$this->form_validation->set_rules('terms', 'Terms', 'required');
			
			

			if($this->form_validation->run())
			{
				
			 	$this->student_model->add_student($studentdata);
			 	$student_id=$this->db->insert_id();
				
				$student_course_data['user_id'] = $student_id;
				
				$courseEnrllId = $this->student_model->add_student_course($student_course_data);
				
			 	redirect('admin/addstudent/coursedetails/'.$courseEnrllId, 'refresh');
			}
		}	
		$content['language']=$this->common_model->get_languages();	
		$content['lang_id']=$lang_id;
		$data['view'] = 'add_student';
		$content['country']=$this->student_model->get_country();
		$content['course']=$this->common_model->get_base_courses($lang_id);
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
	function coursedetails($courseEnrllId)
	{	$content['courseEnrollId']=$courseEnrllId;
		$courseid=$this->student_model->get_course_idById($courseEnrllId);
		foreach ($courseid as $key) {
	
		 $course_id=$key->course_id;
		 $date_reg=$key->date_enrolled;
		 $content['id_users']=$user_id=$key->user_id;
		}
		$xa='';
		$coursename=$this->student_model->get_coursename($course_id);
		
		foreach ($coursename as $key) {
		$content['course_name']=$key->course_name ;
		$content['validity_id']=$key->course_validity;
		}
		
		//echo "Case   ".$content['validity_id'];
		switch($content['validity_id']){
			case 90: $xa = date("Y-m-d", strtotime("+90 days"));
					break;
			case 100:	$xa = date("Y-m-d", strtotime("+100 days"));
					break;
			case 45: $xa = date("Y-m-d", strtotime("+45 days"));
					break;
			case 40: $xa = date("Y-m-d", strtotime("+40 days"));
					break;
		}
		//$studentdata['expairy_date'] =$xa;
		$student_course['date_expiry']=$xa;
		$product_id = $this->common_model->getProdectId('course',$course_id,1);
		
		$coursefee=$this->student_model->get_coursefee($product_id);
		foreach ($coursefee as $key){
			$content['course_fee']=$key->amount;
		}
		
		$student_course_details = $this->student_model->student_course_details($user_id,$course_id);
		
		
		
		foreach ($student_course_details as $key){
			$course_enrollments_id =$key->id;
		}
		
	
		
	
		if(isset($_POST['uname'])){

			$studentdata['username'] = $content['uname'] = $this->input->post('uname');
			$content['pword'] =$this->input->post('pword');
		   $studentdata['password'] =$this->encrypt->encode($this->input->post('pword'));
		   $content['conpword'] = $this->input->post('conpword');

		    $this->form_validation->set_rules('uname', 'user name', 'trim|required');
			$this->form_validation->set_rules('pword', 'Password', 'required|callback_chkpword[Password]');
			$this->form_validation->set_rules('conpword', 'Confirm Password', 'required');
			if($this->form_validation->run())
			{

			 	$this->student_model->add_details($studentdata,$user_id);
			 	//$this->student_model->add_student_course($student_course);
				$this->student_model->student_course_details_update($courseEnrllId,$student_course);
			 	$this->session->set_flashdata('message', 'Student Added Successfully');
			 	
			 	redirect('admin/browsestudent/studentlist/','refresh');
			}
			

		}

		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		$data['view'] = 'add_student_step2';
		$data['content'] = $content;
		$this->load->view('admin/template',$data);

	}
	
	
	
	
	
	function chkpword($val)
	{
		$conpword = $this->input->post('conpword');

		if($val!==$conpword)
		{
			 $this->form_validation->set_message('chkpword', 'Password dosen\'t match');
                return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	function edit($id){
		
		$content = array();
		//$id = $_GET['id'];
		$content['user_id']=$id;
		$pagedata = $this->student_model->fetchdata($id);
	
		
		foreach($pagedata as $row){
			
			 $content['fname'] =$row->first_name;
			 $content['lname']  = $row->last_name;
			// $content['namecert']  = $row->certificateName;
			 $content['uname']  = $row->username;
			 $content['street']  = $row->street;
			 $content['zipCode'] = $row->zipcode;
			 $content['city'] = $row->city;
			 $content['dob'] = $row->dob;
			 $content['email'] = $row->email;
			 $content['gender'] = $row->gender;
			 $content['contact_no'] = $row->contact_number;
			 $content['country_set'] = $row->country_id ;
		//	 $content['course'] = $row->courses_idcourses;
		//	 $content['cb_newsletter'] = $row->newsletter;
		}

		
		
		
		
	    if(isset($_POST['fname']))
		{
		
		     
			$studentdata  = array();
			
		    $studentdata['first_name'] = $content['fname'] = $this->input->post('fname');
		    $studentdata['last_name'] = $content['lname'] = $this->input->post('lname');
		  //  $studentdata['certificateName'] = $content['namecert'] = $this->input->post('namecert');
		    $studentdata['gender'] = $content['gender'] = $this->input->post('gender');
		    $studentdata['dob'] = $content['dob'] = $this->input->post('dob');
		    $studentdata['street'] = $content['street'] = $this->input->post('street');
		    $studentdata['city'] = $content['city'] = $this->input->post('city');
			$studentdata['zipcode'] = $content['zipCode'] = $this->input->post('zipCode');
			$studentdata['country_id'] = $content['country_set'] = $this->input->post('country');
			$studentdata['email'] = $content['email'] = $this->input->post('email');
			$studentdata['contact_number'] = $content['contact_no'] = $this->input->post('contact_no');
			//$studentdata['course_id'] = $content['course_set'] = $this->input->post('course');
		//	$studentdata['newsletter'] = $content['cb_newsletter'] = $this->input->post('cb_newsletter');


		    $this->form_validation->set_rules('fname', 'First name', 'trim|required');
			$this->form_validation->set_rules('lname', 'Last Name', 'required');
			//$this->form_validation->set_rules('namecert', 'Name to be appeared on certificate', 'required');
			$this->form_validation->set_rules('gender', 'Gender', 'required');
			$this->form_validation->set_rules('dob', 'Date of Birth', 'required');
			
			$this->form_validation->set_rules('country', 'Country', 'callback_validate[Country]');
			$this->form_validation->set_rules('email', 'E-mail', 'required|valid_email');
			//$this->form_validation->set_rules('course', 'Course', 'callback_validate[Course]');
			
			
			 if($this->form_validation->run())
			 {
			 	 $this->student_model->student_update($studentdata,$id);
			 	  $this->session->set_flashdata('message', 'Page Updated');
			 	 redirect('admin/browsestudent/studentlist/','refresh');
			 }
			 
			
		 
		
		}
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		$content['country']=$this->student_model->get_country();
		//$content['country_list']=$this->student_model->get_country();
		$content['course_list']=$this->student_model->get_course();
			
		$data['view'] = 'add_student';
		$data['mode'] = 1;
		$data['content'] = $content;
		$this->load->view('admin/template',$data);
		
	}
	function delete($id){
		
	  $this->student_model->student_delete($id);
	  
	   $this->session->set_flashdata('message', 'Student Deleted');
	 	redirect('admin/browsestudent/studentlist/','refresh');
	}	
	function editcourse($id){
		
		$courseid=$this->student_model->get_courseid($id);
		 
		foreach ($courseid as $key) {
	
		 	$course_access['courses_idcourses'] = $content['course_id']=$key->courses_idcourses;
		 	$content['reg_date']=$key->dateRegistered;
		  	$content['exp_date']=$key->dateExpiry;
		  	$course_access['status']= $content['status']=$key->courseStatus;
		    $content['extended']=$key->extended;
		    $content['subscription']=$key->subscription;
		}
		$course_access['users_idusers']=$content['id_student']=$id;
		$coursename=$this->student_model->get_coursename($content['course_id']);
		
		foreach ($coursename as $key) {
		$content['course_name']=$key->course_name ;
		$content['validity_id']=$key->course_validity_id;
		}
		$access_date_exp=$this->student_model->get_courseaccess($id);

		if($access_date_exp!==NULL){
			foreach($access_date_exp as $row)
			{
				$content['date_exp']= $row->access_date_expiry ;
			}
		}
		else $content['date_exp']='0000-00-00';
		
		
		if(isset($_POST['exp_date']))
		{
			$coursedata  = array();
			if($content['exp_date']!=$this->input->post('exp_date')){
				$coursedata['extended']=$content['extended']=1;

			}
			
		    $coursedata['dateExpiry'] = $content['exp_date'] = $this->input->post('exp_date');
		    	
		    $coursedata['subscription'] = $content['subscription'] = $this->input->post('subscription');
		    if($content['subscription']==6){
		    	
		    	$course_access['access_date_expiry']= date("y-m-d", strtotime("+6 months", strtotime($content['exp_date'])));
		    }
		    elseif($content['subscription']==12){
		    	$course_access['access_date_expiry']= date("y-m-d", strtotime("+12 months", strtotime($content['exp_date'])));
		    }
		   
		     
		      $this->student_model->student_course_update($coursedata,$id);
		       
		      $this->student_model->course_access($course_access,$id);
		      
		      redirect('admin/addstudent/editcourse/'.$id,'refresh');
		
		}

		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		
		$data['view'] = 'edit_student_course';
		$data['content'] = $content;
		$this->load->view('admin/template',$data);
	}
	function ajaxCheckUsername($username){
		
		$this->load->model('user_model');		
		$user_exist = $this->user_model->check_record_exist('username','users',$username);
		
		if($user_exist) {
			echo 1;}
		else {
			echo 0;}
		
	}
	
}
