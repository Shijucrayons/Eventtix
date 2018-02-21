<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class test_page extends CI_Controller
{
 	 
	function __construct()
	{

		parent::__construct();
		$this->load->library('session');
		$this->load->helper(array('form'));
   		$this->load->library('form_validation');
		$this->load->model('user_model','',TRUE);
		$this->load->model('student_model','',TRUE);
		
		
	}
		
	function ajaxCheckUsername(){
		
		$this->load->model('user_model');
		echo $username =$this->uri->segment(3);
		
		$user_exist = $this->user_model->check_record_exist('username','users',$username);
		
		if($user_exist) {
			echo "user already exist";
			return false;}
		else {
			echo "username available";
			return true;}
		
	}
	
	function ajaxCheckEmail(){
		
		$this->load->model('user_model');
		echo $username =$this->uri->segment(3);
		
		$user_exist = $this->user_model->check_record_exist('email','users',$username);
		
		if($user_exist) {
			echo "email already registered";
			return false;}
		else {
			echo "seems all good";
			return true;}
		
	}		
		
	
	function mailTest(){
	
		$this->load->library('email');
		
					$tomail = 'bhagathindian@gmail.com';
					//$tomail = 'ajithupnp@gmail.com';
					//$tomail = 'deeputg1992@gmail.com';
					
					  $emailSubject = "Testing email function";
					  $mailContent = "<p>Hello there, </br> this is a test SMTP mail from <strong>trendimi <strong><p>";
						   	
					  $this->email->from('info@trendimi.com', 'Team Trendimi');
					  $this->email->to($tomail); 
					  $this->email->cc(''); 
					  $this->email->bcc(''); 
					  
					  $this->email->subject($emailSubject);
					  $this->email->message($mailContent);	
					  
					 $this->email->send();
					 $this->email->clear(TRUE);
			echo "<br>First mail sent ";
					  
					  
					  $tomail = 'ajithupnp@gmail.com';
					//$tomail = 'deeputg1992@gmail.com';
					
					  $emailSubject = "Testing email function";
					  $mailContent = "<p>Hello there, </br> this is a test SMTP mail from <strong>trendimi <strong><p>";
						   	
					  $this->email->from('info@trendimi.com', 'Team Trendimi');
					  $this->email->to($tomail); 
					  $this->email->cc(''); 
					  $this->email->bcc(''); 
					  
					  $this->email->subject($emailSubject);
					  $this->email->message($mailContent);	
					  
					 $this->email->send();
					$this->email->clear(TRUE);
				echo "<br>SEcond  mail sent ";
				
				 // $tomail = 'ajithupnp@gmail.com';
					$tomail = 'deeputg1992@gmail.com';
					
					  $emailSubject = "Testing email function";
					  $mailContent = "<p>Hello there, </br> this is a test SMTP mail from <strong>trendimi <strong><p>";
						   	
					  $this->email->from('info@trendimi.com', 'Team Trendimi');
					  $this->email->to($tomail); 
					  $this->email->cc(''); 
					  $this->email->bcc(''); 
					  
					  $this->email->subject($emailSubject);
					  $this->email->message($mailContent);	
					  
					 $this->email->send();
$this->email->clear(TRUE);
				echo "<br>Third mail sent ";
					  
	}
	
	function set_user_course_status_from_stud_cert()
	{
		
		$this->db-> select('*');
		$this->db-> from('students_certificates');
		$query = $this->db->get();
		if($query->num_rows()>0)
		{
			$count =0;
			foreach($query->result() as $row)
			{
				$up_array = array();
				if($row->issue_status=='approved')
				{
					$up_array['course_status'] = '4';
					//$this->db->select('id');
					$this->db->where('user_id',$row->user_id);
					$this->db->where('course_id',$row->course_id);
					//$test = $this->db->get('course_enrollments');
					//echo "<pre>";print_r($test->result());
					$this->db->update('course_enrollments',$up_array);
				$count++;
				}
			}
			echo $count;
		
		}
	
	}
	
}
	