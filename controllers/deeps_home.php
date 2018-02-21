<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class deeps_home extends CI_Controller
{
 	 
	function __construct()
	{

		parent::__construct();
		$this->load->library('session');
		$this->load->helper(array('form'));
    //$this->load->helper(array('language'));
		$this->load->library('form_validation');
		$this->load->library('encrypt');
		
		$this->load->model('user_model','',TRUE);
		$this->load->model('student_model','',TRUE);
		$this->load->model('email_model','',TRUE);
		$this->load->library('ip2country_lib');
		$this->con_name = $this->ip2country_lib->getInfo();
      
		if(isset($_POST['username'])&&isset($_POST['password']))
		{
			$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
   			$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_check_database');
			$content['username'] = $this->input->post('username');
			if($this->form_validation->run() == TRUE)
			{//Go to private area
				redirect('coursemanager/campus/'.$this->session->userdata['student_logged_in']['id'], 'refresh');
      }
		}
   
		if(isset($_GET['lang_id'])){
			$newdata = array(
                   'language'  => $_GET['lang_id']
               );
			$this->session->set_userdata($newdata);
		}
		elseif(!$this->session->userdata('language')){
			$newdata = array(
                   'language'  => '4'
               );
			$this->session->set_userdata($newdata);
		} 
		
		 $curr_code=$this->user_model->get_currency_id($this->con_name);

      if($curr_code!==1)
	  {
		foreach ($curr_code as $value)
		{
		 $this->currId= $value->currency_idcurrency;
		 $this->currencyCode=$value->currencyCode;
		}
	  }
    else {
      $this->currId=1;
    	$this->currencyCode='EUR';
		}
		

		$this->language = $this->session->userdata('language');
		$this->student_status = $this->session->userdata('student_logged_in');
		$this->course=$this->user_model->get_courses($this->language);
		$this->menu['top_menu']=$this->user_model->get_top_menu($this->language);
    }
	
	function currTest()
	{
		echo   $this->currId=1;
 echo    	$this->currencyCode='EUR';
	}

function mailTest(){
	
		$this->load->library('email');
	$tomail = 'deeputg1992@gmail.com';
					
					  $emailSubject = "Mail Test";
					  $mailContent = "<p>it workked<p>";
						   	
					  $this->email->from('info@trendimi.com', 'Team Trendimi');
					  $this->email->to($tomail); 
					  $this->email->cc(''); 
					  $this->email->bcc(''); 
					  
					  $this->email->subject($emailSubject);
					  $this->email->message($mailContent);	
					  
					 $this->email->send();
					 echo "done";
					  

	
	}
	function ajaxTest()
	{
		$this->load->view('user/ajaxTest');
	}
	function getText()
	{
		echo 'deepu';
	}
	

	
	function prodDetails($prodectId)
	{
		$this->load->model('course_model');
		$prod = $this->common_model->getProductDetail($prodectId,$this->currId);
		echo "<pre>";print_r($prod);
	}
	
	function certificate_sample($course_id)
	{
		
		$this->load->model('certificate_model');
	  
	   $this->load->helper(array('dompdf', 'file'));
	   
  	 if(!$this->session->userdata('student_logged_in')){
		  redirect('home');
		}
	$lang_id = $this->session->userdata('language');
	$user_id=$this->session->userdata['student_logged_in']['id'];
		
	 //$this->load->library('encrypt');
	// $course_id = $this->encrypt->decode($course_id_encrypted);
	// $course_id = $course_id_encrypted;
	 $course_name = $this->common_model->get_course_name($course_id);
	 $user_details = $this->user_model->get_student_details($user_id); 
	
	 foreach($user_details as $key => $value)
	 {
	 	$certificate_user_name = $value->first_name.'&nbsp;'.$value->last_name;		
	 }
	// $mark_details = $this->get_student_progress($course_id);
	 
	 $mark_details['coursePercentage'] = 86;
	 //progressPercnt
	/* 
	 echo "<pre>";
	 print_r($mark_details);
	 exit;*/
	 $grade='falied';
	 	if($mark_details['coursePercentage'] >= 55 && $mark_details['coursePercentage'] <= 64.99)
	 	{
	 		$grade = $this->user_model->translate_('mark_pass');
		}
		else if($mark_details['coursePercentage'] >= 65 && $mark_details['coursePercentage'] <= 74.99)
	 	{
	 		$grade = $this->user_model->translate_('mark_pass_plus');
		}
		else if($mark_details['coursePercentage'] >= 75 && $mark_details['coursePercentage'] <= 84.99)
	 	{
	 		$grade = $this->user_model->translate_('mark_merit');
		}
		else if($mark_details['coursePercentage'] >= 85 )
	 	{
	 		$grade = $this->user_model->translate_('mark_dist');
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
       $month=$this->user_model->translate_('month_1');

   }
   else if($values[1]=='2')
   {
      $month=$this->user_model->translate_('month_2');
   }
   else if($values[1]=='3')
   {
      $month=$this->user_model->translate_('month_3');
   }else if($values[1]=='4')
   {
      $month=$this->user_model->translate_('month_4');
   }else if($values[1]=='5')
   {
      $month=$this->user_model->translate_('month_5');
   }else if($values[1]=='6')
   {
      $month=$this->user_model->translate_('month_6');
   }else if($values[1]=='7')
   {
      $month=$this->user_model->translate_('month_7');
   }else if($values[1]=='8')
   {
      $month=$this->user_model->translate_('month_8');
   }else if($values[1]=='9')
   {
      $month=$this->user_model->translate_('month_9');
   }else if($values[1]=='10')
   {
      $month=$this->user_model->translate_('month_10');
   }else if($values[1]=='11')
   {
      $month=$this->user_model->translate_('month_11');
   }else
   {
      $month=$this->user_model->translate_('month_12');
   }
	$year=$values[0];
		
		
				/* Style Me course */
		
		if($lang_id==4 && $course_id==1)
		{
		//	$class='outer_cert_spanish_styleme';
			$courseTitle = 'Personal Fashion Styling';

		//	$coursename=$this->user_model->translate_('cert_styleme');
			$coursename= 'Style me course';
		}
		else if($lang_id==3 && $course_id==11 )
		{
		//	$class='outer_cert_english_styleme';
			$courseTitle = 'Personal Fashion Styling';
			//$coursename=$this->user_model->translate_('cert_styleme');
			$coursename='Autoimagen curso';
		}
		/* End Style Me course */	
		
		/* Style You course */

		else if($lang_id==4 && $course_id==2 )
		{ 
		//$class='outer_cert_english_styleyou';
		//$coursename=$this->user_model->translate_('cert_styleyou');
		$courseTitle = 'Professional Fashion Styling';
		$coursename = 'Style you course';
		}
		else if($lang_id==3 && $course_id==12 )
		{
		//$class='outer_cert_spanish_styleyou';
		$courseTitle = 'Professional Fashion Styling';
		//$coursename=$this->user_model->translate_('cert_styleyou');
		$coursename = 'Personal shopper curso';
		}
		
		/* End Style You course */
		
		/* Make Up course */

		else if($lang_id==4 && $course_id==3 )
		{
	//	$class='outer_cert_english_makeup';
		$courseTitle = 'Make Up Artistry';
		$coursename=$this->user_model->translate_('cert_makeup');
		$coursename = 'Make up course';
		}
		else if($lang_id==3 && $course_id==13)
		{
		//$class='outer_cert_spanish_makeup';
		$courseTitle = 'Make Up Artistry';
		//$coursename=$this->user_model->translate_('cert_makeup');
		$coursename = 'Maquillaje curso';
		}
		
		/* End Make Up course */
		/* Wedding Planner course */

		else if($lang_id==4 && $course_id==4 )
		{
		//$class='outer_cert_english_wedding';
		$courseTitle = 'Wedding Planning';
		//$coursename=$this->user_model->translate_('cert_wedding');
		$coursename='Wedding Planner course';
		}
		
		else if($lang_id==3 && $course_id==14 )
		{
		//$class='outer_cert_spanish_wedding';
		$courseTitle = 'Wedding Planning';
		//$coursename=$this->user_model->translate_('cert_wedding');
		$coursename='Wedding Planner curso';
		}
		
		/* End Wedding Planner course */
		
		
		/* Nail artist course */
		else if($lang_id==4 && $course_id==5)
		{
		//$class='outer_cert_english_hair';
		$courseTitle = 'Nail Artist';
		//$coursename=$this->user_model->translate_('cert_hair');
		$coursename='Nail Artist course';
		}
		else if($lang_id==3 && $course_id==15 )
		{
		//$class='outer_cert_spanish_hair';
		$courseTitle = 'Nail Artist';
		//$coursename=$this->user_model->translate_('cert_hair');
		$coursename='Nail Artist curso';
		}
		
		/* End Nail artist course */
		
		
		
		/* Hair Stylist course */

		else if($lang_id==4 && $course_id==6 )
		{
		//$class='outer_cert_english_hair';
		$courseTitle = 'Hair Stylist';
		//$coursename=$this->user_model->translate_('cert_hair');
		$coursename='Hair Stylist course';
		}
		else if($lang_id==3 && $course_id==16 )
		{
		//$class='outer_cert_spanish_hair';
		$courseTitle = 'Hair Stylist';
		//$coursename=$this->user_model->translate_('cert_hair');
		$coursename='Hair Stylist curso';
		}
		/* End Hair Stylist course */
		
		
		//$cssLink = base_url();
		if($lang_id==3)
		{
			$cssLink = "public/certificate/css/certificate-style_spanish.css";
		}
		else if($lang_id==4)
		{
			$cssLink = "public/certificate/css/certificate-style.css";
		}

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
<h2>'.$certificate_user_name.'</h2>
<h3>'. $coursename.'</h3>
<h4>'.$this->user_model->translate_('cert_grade').': ' .$grade.'</h4>
<h4> '.$this->user_model->translate_('cert_date').': '.$month.' '.$year.'</h4>
<h4> '.$this->user_model->translate_('cert_no').': 100-'.$certificate_id.'</h4>
<div style="clear:both"></div>
</div>
</div>
<div style="clear:both"></div>
</body>
</html>
';


/*echo $html;
		exit;*/
	 $data = pdf_create($html, 'TrendimiCertificate_'.$user_id.'_'.$course_id);
     //or
     //$data = pdf_create($html, '', false);
     write_file('name', $data);	
		
		
  
		
		
		
	}

//*** generate pdf script ******//
	function pdf()
	{
	 //$this->load->helper('file');
     $this->load->helper(array('dompdf', 'file'));
     // page info here, db calls, etc.     
     //$html = $this->load->view('controller/viewfile', $data, true);
		$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>certificate</title>
		<link href="public/certificate/css/certificate-style.css" type="text/css" rel="stylesheet" />
		</head>
		
		<body>
		<div class="raper">
		<div id="certificate">
		<h1> Certificate in Style Me</h1>
		<h2> Bhagath Prasad</h2>
		<h3>Style Me</h3>
		<h4> Pass </h4>
		<h4> 12-03-2013</h4>
		<h4> 12345-100-1</h4>
		<div style="clear:both"></div>
		</div>
		</div>
		<div style="clear:both"></div>
		</body>
		</html>
		';

     // case1 : thisone used for download pdf file************************
	 
	 	 //$data = pdf_create($html, 'certicate_name');
	  
	 // case2 : thisone used saving the pdf file on disc (or for email attachment)************************ 
    
     	$data = pdf_create($html, 'cert', false);	
		
		echo "<pre>";
		print_r($data);
		exit;
		
		$this->path = "public/certificate/hardcopy/cert.pdf";
		write_file($this->path, $data);
     
		
		
		
		// end case2 ******************************	
		$sendemail = true;
		
		if($sendemail)
		{
			$this->load->library('email');
			$tomail = 'ajithupnp@gmail.com';
					
					  $emailSubject = "certificate is attached";
					  $mailContent = "<p>Check if there is an attachemnt with mail. If its there you are lucky ;) <p>";
						   	
					  $this->email->from('info@trendimi.com', 'Team Trendimi');
					  $this->email->to($tomail); 
					  $this->email->attach($this->path);
					  
					  $this->email->subject($emailSubject);
					  $this->email->message($mailContent);	
					  
					 $this->email->send();
			}
	}
	
	//*** generate excel script ******//
	
	function generateExcel(){
		$this->load->library('export');
		$this->load->model('user_model');
		$sql = $this->user_model->get_courses(4);
		/*echo "<pre>";
		print_r($sql);
		//exit;*/
		
		$this->export->to_excel($sql, 'test_excel'); 
	}
	
	//*** generate excel script 2 we will use this ******//
	function excelExport(){
		$this->load->helper(array('php-excel'));
		$sql = $this->user_model->get_courses(4);
	   	$fields = (	$field_array[] = array ("ID", "Course Name", "Summary")  );
	   
	   	foreach ($sql as $row)
			 {
			 $data_array[] = array( $row->course_id, $row->course_name, $row->course_summary );
			 }
			 
		   $xls = new Excel_XML;
		   $xls->addArray ($field_array);
		   $xls->addArray ($data_array);
		   $xls->generateXML ( "course_list" );
	}
	
	function genarator_reedemed()
	{
		//$this->load->view('admin/header');
		$this->load->helper(array('php-excel'));
		
		
		$start_date =  $this->session->flashdata('start_date');
		$end_date = $this->session->flashdata('end_date'); 
		
	
		if(isset($start_date)&&$start_date!="")
		{
			$this->db->where('redeemed_coupons.date >',$start_date);
		}
		if(isset($end_date)&&$end_date!="")
		{
			$this->db->where('redeemed_coupons.date <',$end_date);
		}
	
		
		$this->db->select('coupon_code,date,redemption_code,course_name,first_name,last_name,site_name,email,contact_number');
		$this->db-> join('courses','redeemed_coupons.course_id = courses.course_id');
		$this->db-> join('users','redeemed_coupons.user_id = users.user_id');
		$this->db-> join('giftvoucher_websites','giftvoucher_websites.id = redeemed_coupons.website_id');
		
		$query = $this->db->get('redeemed_coupons');
		
		$sql=$query->result();

	   	$fields = ($field_array[] = array ("Name","Email","Contact No","Course Name","Date Registered","Voucher code","Security code","Voucher website")  );
		//echo "<pre>";print_r($sql);exit;
	   	foreach ($sql as $row)
			 {
			 $data_array[] = array($row->first_name." ".$row->last_name,$row->email,$row->contact_number,$row->course_name,$row->date,$row->coupon_code,$row->redemption_code,$row->site_name);
			 }
			 
		   $xls = new Excel_XML;
		   $xls->addArray ($field_array);
		   $xls->addArray ($data_array);
		   $xls->generateXML ( "redeemed_vouchers" );
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
	function genarator_students_with_pass()
	{
		//$this->load->view('admin/header');
		$this->load->helper(array('php-excel'));	
	
	$stat =  $this->session->flashdata('stat'); 
	$course =   $this->session->flashdata('course');
	$start_date =  $this->session->flashdata('start_date');
	$end_date = $this->session->flashdata('end_date'); 
	
	if(isset($stat)&&$stat!="")
	{
		$this->db->where('course_enrollments.course_status',$stat);
	}
	if(isset($course)&&$course!="")
	{
		$this->db->where('courses.course_id',$course);
	}
	if(isset($start_date)&&$start_date!="")
	{
		$this->db->where('course_enrollments.date_enrolled >',$start_date);
	}
	if(isset($end_date)&&$end_date!="")
	{
		$this->db->where('course_enrollments.date_enrolled <',$end_date);
	}
	

		
		$this->db-> select('users.user_id, first_name,last_name,username,password,courses.course_name,courses.course_id,course_enrollments.date_enrolled,course_enrollments.date_expiry,email,country_id,course_enrollments.course_id,status,country_id,course_enrollments.course_status as course_stud_status,coupon_code,redemption_code');
		
		$this->db->group_by('users.user_id');
		$this->db->join('course_enrollments','course_enrollments.user_id = users.user_id');
		$this->db->join('courses','courses.course_id = course_enrollments.course_id');
		$this->db->join('redeemed_coupons','redeemed_coupons.user_id=users.user_id');
		$query = $this->db->get('users');
		
		$sql=$query->result();

	   	$fields = ($field_array[] = array ("First Name","Last Name","User Name","Password","Date Expire","Email ID","Country")  );
		// echo "<pre>";print_r($sql);
	   	foreach ($sql as $row)
			 {
				 $country = $this->user_model->get_country_name($row->country_id);
				$password = $this->encrypt->decode($row->password);
				 
$data_array[] = array( $row->first_name,$row->last_name,$row->username,$password,$row->date_enrolled,$row->email,$country);
			 }
			 
		   $xls = new Excel_XML;
		   $xls->addArray ($field_array);
		   $xls->addArray ($data_array);
		   $xls->generateXML ( "course_list" );
	}
	function genarator_developers()
	{
		//$this->load->view('admin/header');
		$this->load->helper(array('php-excel'));	
	
	$stat =  $this->session->flashdata('stat'); 
	$user_stat =  $this->session->flashdata('user_stat'); 
	$course =   $this->session->flashdata('course');
	$start_date =  $this->session->flashdata('start_date');
	$end_date = $this->session->flashdata('end_date'); 
	
	if(isset($stat)&&$stat!="")
	{
		$this->db->where('course_enrollments.course_status',$stat);
	}
	if(isset($user_stat)&&$user_stat!="")
	{
		$this->db->where('users.status',$user_stat);
	}
	if(isset($course)&&$course!="")
	{
		$this->db->where('courses.course_id',$course);
	}
	if(isset($start_date)&&$start_date!="")
	{
		$this->db->where('users.reg_date >',$start_date);
	}
	if(isset($end_date)&&$end_date!="")
	{
		$this->db->where('users.date_enrolled <',$end_date);
	}
	

		
		$this->db-> select('users.user_id, first_name,last_name,username,password,email,country_id,status,reg_date');
		
		$this->db->group_by('users.user_id');
		//$this->db->join('course_enrollments','course_enrollments.user_id = users.user_id');
		//$this->db->join('courses','courses.course_id = course_enrollments.course_id');
		//$this->db->join('redeemed_coupons','redeemed_coupons.user_id=users.user_id');
		$query = $this->db->get('users');
		
		$sql=$query->result();

	   	$fields = ($field_array[] = array ("First Name","Last Name","User Name","Password","Date Enrolled","Email ID","Country","Status")  );
		// echo "<pre>";print_r($sql);
	   	foreach ($sql as $row)
			 {
				 $country = $this->user_model->get_country_name($row->country_id);
				$password = $this->encrypt->decode($row->password);
				 
$data_array[] = array( $row->first_name,$row->last_name,$row->username,$password,$row->reg_date,$row->email,$country,$row->status);
			 }
			 
		   $xls = new Excel_XML;
		   $xls->addArray ($field_array);
		   $xls->addArray ($data_array);
		   $xls->generateXML ( "course_list" );
	}
	
	
	
	// **** encode and decode ********
	function excelExport2(){
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
		   $xls->generateXML ( "course_list" );
	}
	
	function encodetext()
	{
		
		$this->load->library('encrypt');
		//$text = $this->uri->segment(3);	
		$text ="docoscar&";
		echo "encoding ' ".$text; echo " '<br>";	
		
		echo "encoded value -";
		
		echo $encoded = $this->encrypt->encode($text);echo "<br>"; 
		
		echo "decoding ' ".$encoded; echo " '<br>";	
		
		echo "decoded value - ' ";
		
		echo $encoded = $this->encrypt->decode($encoded); echo " ' <br>";
		
	}
	
	
	
	//**** import records from excel sheet ******/
	// this one works !! :)
	function phpexcel(){
		
		$this->load->helper(array('phpexcel'));
		
		$excelrecords = excelReader('public/admin/uploads/couponcodes/vouchersample.xlsx');
		echo "<pre>";
		print_r($excelrecords); 
		
		 for($i=0;$i<count($excelrecords);$i++)
				{	
					$voucher_data[] 	 =$excelrecords[$i][0];
				}
			echo "<pre>";
		print_r($voucher_data); 
	}
	
	function serial_test()
	{
		for($i=0;$i<25;$i++)
		{
			$unserial[] = $i;
		}
		echo $serial =serialize($unserial);
		//echo "<pre>";print_r($unserialized = unserialize("a:9:{i:0;s:2:"18";i:1;s:2:"19";i:2;s:2:"20";i:3;s:2:"21";i:4;s:2:"22";i:5;s:2:"23";i:6;s:2:"24";i:7;s:2:"25";i:8;s:2:"26";}"));
	}
	function exTest(){
		
		echo $this->user_model->findExpirityDate(1,'2013-11-20') ;
		
	}
		function updateStud_units($course_id,$user_id)
	{
		      
		
		$usersUnit = $this->user_model->get_courseunits_id($course_id);
			foreach($usersUnit as $row)
			{
				$un[$row->units_order] = $row->course_units;
			}
			$student_courseData['student_course_units'] = serialize($un);
			 
			// echo "<pre>";print_r($student_courseData);
			 $this->user_model->update_student_enrollments($course_id,$user_id,$student_courseData);
			
		
	}
	
	
	
	
	function sessionCheck()
	{
		echo "<br>---------Userdata-----------<br><pre>";print_r($this->session->userdata);echo"</pre>";
		//echo "<br><br><br><br>*****************************************************************<br>---------Total sessions-----------<br><pre>";print_r($this->session);echo"</pre>";
		
		echo "<br>---------Flashdata-----------<br><pre>";print_r($this->session->flashdata);echo"</pre>";
	}
	
	function set_users()
	{
		$this->load->model('new_model');
		$query = $this->db->get('users');
		$result = $query->result();
		$i = 0;
		foreach($result as $row) 
		{
			/*updating users password to encrypted password*/
			if($row->pass_encoded=='0')
			$user_pass = $this->new_model->update_to_encrypt_pass($row->user_id,$row->password);
			
			/*setting up lang_id field using course-language*/
			$user_lang = $this->new_model->assign_language($row->user_id);
			
			
			//echo "<table border=1><tr><td><pre>";print_r($user_pass);echo "</pre></td>";
			//echo "<td><br>Decoded pass = = = =".$this->encrypt->decode($user_pass['password'])."</td>";
			//echo "<td><pre>";print_r($user_lang);echo "</pre></td></tr></table>";
			
		$i ++;
		/*if($i==50)
		exit;*/
		}
		echo $i;
		
	}
	
	function set_course_enrollments($courseId)
	{
		
		$this->load->model('new_model');
		
		$this->db->where('course_id',$courseId);
		$query = $this->db->get('course_enrollments');
		$result = $query->result();
		
		echo "<pre>";print_r($result);echo "</pre><br>................dfffffffff////////////". count($result);
		
		
		$i = 0;
		foreach($result as $row) 
		{
			/*splitting multiple courses into multiple entries*/
			switch ($row->course_id)
			 {
			case 20:$course_stud =  $this->new_model->split_courses($row->id,20,"1,2");
		   		break;
			case 10:$course_stud =  $this->new_model->split_courses($row->id,10,"7,13");
		   		break;
			case 23:$course_stud =  $this->new_model->split_courses($row->id,23,"6,7,13");
		   		break;
			case 22:$course_stud =  $this->new_model->split_courses($row->id,22,"6,7");
		   		break;
			case 9:$course_stud =  $this->new_model->split_courses($row->id,9,"6,13");
		   		break;
			case 5:$course_stud =  $this->new_model->split_courses($row->id,5,"2,3");
		   		break;
			case 25:$course_stud =  $this->new_model->split_courses($row->id,25,"18,16,17");
		   		break;
			case 24:$course_stud =  $this->new_model->split_courses($row->id,24,"16,17");
		   		break;
			case 4:$course_stud =  $this->new_model->split_courses($row->id,4,"1,3");
		   		break;
			case 21:$course_stud =  $this->new_model->split_courses($row->id,21,"1,2,3");
		   		break;
			
			 }
			
					
			
			
			
			
		$i ++;
		
		}
		echo "<br><br>count : ".$i;
		
		if(isset($course_stud))
			{
			echo"<pre>";print_r($course_stud);echo "</pre>";
			}
		
		
	}
	
	//!!!!!Importand.. change course ids compatible with new verion befour going to next function (set_course_enrollments_3)
	
	function set_courseEnrollments_student_course_units()
	{
		//$this->db->select('course_id');
		//$this->db->distinct('course_id');
		$query = $this->db->get('course_enrollments');
		
		$i= 0 ;
		foreach($query->result() as $enroll_row)
		{
			//echo 	"<br>".$enroll_row->course_id;	
		$usersUnit = $this->user_model->get_courseunits_id($enroll_row->course_id);
		
			$un =array();
			foreach($usersUnit as $row)
			{
				$un[$row->units_order] = $row->course_units;
			}
			
			$student_courseData['student_course_units'] = serialize($un);
			 
			// echo "<pre>";print_r($student_courseData);
			// if($i==1000)
			 //exit;
			$this->user_model->update_student_enrollments($enroll_row->course_id,$enroll_row->user_id,$student_courseData);
		
		$i++;	
		}
		echo "<br>----------------- Count : : :".$i;
	}
	
	
	function decode_password($password)
	{
		
		$password_decoded =$this->encrypt->decode($password);
		echo "<br>decodec :".$password_decoded."<br>";
		
		
		$decoded_password = $this->encrypt->decode($password);
		echo "Decoded password ".$decoded_password;
		
	}
	
	function encode_password($password)
	{
		$password_decoded =$this->encrypt->encode($password);
		echo "<br>decodec :".$password_decoded."<br>";
		echo "<br>length = ".strlen($password_decoded);
	}
	function trans_test()
	{
		header('Content-type: text/html; charset=UTF-8'); 
		$string = $this->user_model->translate_('confirm_email');
		echo "String from data base : ".$string;
		
		/*if (mb_detect_encoding($string, 'utf-8', true) == false) {
			echo "<br>not utf8<br>";
    $string1 = mb_convert_encoding(utf8_decode($string), 'utf-8', 'iso-8859-1');
}
else
{
	echo "<br>its utf8<br>";
	 $string = mb_convert_encoding($string, 'utf-8', 'iso-8859-1');
}*/

//echo "<br>enccoded : =".$string;

/*echo $enc = mb_detect_encoding($string,"UTF-8,ISO-8859-1");

echo '<br>Detected encoding '.$enc."<br />";
//$string1 = utf8_decode($string);
echo 'Fixed result: '.utf8_decode(iconv($enc, "utf-8//IGNORE", $string))."<br />";
echo "Striped string ".$striped = str_replace("ƒ","",$string);
echo "utfDecoded : ".utf8_decode($striped);

*/
/*if(mb_detect_encoding($string)=="UTF-8")
{
	$string2 = utf8_decode(stripslashes($string));
	$string3 = mb_convert_encoding($string,'','utf-8');
	
	echo "<br> utf decoded : ".$string2."<br> mb_decoded = ".$string3;
	
	
	
}*/

echo "-------------------------------------<br> starting of encoding test ";
echo "<br>".$newstring = "amen ss df Móóódulo";
echo  "<br>Encoded with UTF8----------".$en = utf8_encode($newstring);

echo  "<br>Decode with UTF8----------".$de_en = utf8_decode($string);

	}
	
	function update_course_id_gv()
	{
		
		$this->load->model('new_model');
		$multiple_course_gvs = $this->new_model->get_multiple_coure_gvs();
		echo "Mulitiple course f=gv count ".count($multiple_course_gvs);
		
		foreach($multiple_course_gvs as $voucher)
		{
			
			$course_ids_gv = $this->new_model->get_course_ids($voucher->idgiftVoucher);

			
			/*echo "<pre>";
			print_r($course_ids_gv);
			exit;*/
			foreach($course_ids_gv as $split)
			{
				$new_course_ids ='';
				$split_course_ids = explode(",",$split->courses_idcourses);
				 for($i=0;$i<count($split_course_ids);$i++)
		  		{
					
					
					if($split_course_ids[$i] == 6 )
					{
						if($new_course_ids =='')
						{
							$new_course_ids = '11';
						}
						else
						{
							$new_course_ids .= ',11';
						}
					}
					else if($split_course_ids[$i] == 27 )
					{
						if($new_course_ids =='')
						{
							$new_course_ids = '4';
						}
						else
						{
							$new_course_ids .= ',4';
						}
					}
					else if($split_course_ids[$i] == 28 )
					{
						if($new_course_ids =='')
						{
							$new_course_ids = '5';
						}
						else
						{
							$new_course_ids .= ',5';
						}
					}
					/*else if($split_course_ids[$i] == 36 )
					{
						if($new_course_ids =='')
						{
							$new_course_ids = '6';
						}
						else
						{
							$new_course_ids .= ',6';
						}
					}*/
					else if($split_course_ids[$i] == 7 )
					{
						if($new_course_ids =='')
						{
							$new_course_ids = '12';
						}
						else
						{
							$new_course_ids .= ',12';
						}
					}
					else if($split_course_ids[$i] == 13 )
					{
						if($new_course_ids =='')
						{
							$new_course_ids = '13';
						}
						else
						{
							$new_course_ids .= ',13';
						}
					}
					else if($split_course_ids[$i] == 31 )
					{
						if($new_course_ids =='')
						{
							$new_course_ids = '14';
						}
						else
						{
							$new_course_ids .= ',14';
						}
					}
					else if($split_course_ids[$i] == 33 )
					{
						if($new_course_ids =='')
						{
							$new_course_ids = '16';
						}
						else
						{
							$new_course_ids .= ',16';
						}
					}
					else if($split_course_ids[$i] == 34 )
					{
						if($new_course_ids =='')
						{
							$new_course_ids = '15';
						}
						else
						{
							$new_course_ids .= ',15';
						}
					}
					else
					{
						if($new_course_ids =='')
						{
							$new_course_ids = $split_course_ids[$i];
						}
						else
						{
							$new_course_ids .= ','.$split_course_ids[$i];
						}
					}
										
				}
				echo "<br>Gift code ".$split->giftVoucherCode." &nbsp;&nbsp;&nbsp;&nbsp; New course ids ".$new_course_ids;
				
				$this->new_model->update_mulitiple_course_gift($voucher->idgiftVoucher,$new_course_ids);
				echo "Voucher id &nbsp;".$voucher->idgiftVoucher." &nbsp;&nbsp;Voucher code ".$split->giftVoucherCode." &nbsp;&nbsp;Course ids ".$split->courses_idcourses."&nbsp;&nbsp;&nbsp; Updated to ".$new_course_ids;
			}
			
			
		}
		
		
	}
	
	
	
		
   					//Field validation succeeded.  Validate against database
   					//$username = $this->input->post('username');

   					//query the database
   function login_test()
  {
	  
	$username= 'miraclyn_1';
	$password = 'Jesus777';
		
	$this->db-> select('password');
    $this->db-> from('users');
    $this->db-> where('username',$username);
	$query_1 = $this -> db -> get();
	echo "---user array-------<pre>";print_r($query_1->result());
	
	if($query_1 -> num_rows() == 1)
    {
		foreach($query_1->result_array() as $row)
		{
			$password_dec = $row['password'];							 
		}
     
		$password_decoded =$this->encrypt->decode($password_dec);
		echo "<br>decodec :".$password_decoded."<br>";
	}
	else
	{
		 echo "<br>----------------->entered in (num_rows!=1) condition<----------";
	}
	  
    $this->db-> select('user_id,username ,password,status,lang_id');
    $this->db-> from('users');
    $this->db-> where('username',$username);   	
    $this->db-> limit(1);
    $query = $this -> db -> get();
	
	
	
	echo "--------second result------------<pre>";print_r($query->result()); 
	
	
	if($query -> num_rows() == 1)
    {
		
		if($password_decoded == $password)
		{
			echo "<br>---------------------password tacken correctly=-------------------<br>";
		}
      
    }
    else
    {
      	echo "<br>----------------------entered in second else------------";
    }
  }
  
  	function login($username,$password)
 	{
   					//Field validation succeeded.  Validate against database
   					//$username = $this->input->post('username');

   					//query the database
   					$result = $this->user_model->login($username, $password);
					
					echo "REsult ";
					
					echo "<pre>";
					print_r($result);
					
					if($result)
   					{
     					$sess_array = array();
     					foreach($result as $row)
     					{ // echo $row->active;
               if ($row->status!=1) {
                
                  $this->form_validation->set_message('check_database','student is not active');
                  return FALSE;
                }

                else{
       						$sess_array = array('id' => $row->user_id,'username' => $row->username );
       						$this->session->set_userdata('student_logged_in', $sess_array);
							$sess_array1 = array('language' => $row->lang_id);
       						$this->session->set_userdata($sess_array1);
                  return TRUE;
                }
     					}
     					
   					}
					else
   					{
						
     					$this->form_validation->set_message('check_database','Invalid stylist ID or code');
    					return false;
   					}
				
				
	}
	
	function course_perc_report()//report genaration -course percentage vise
	{
		$this->load->helper(array('php-excel'));
		
		$this->db-> select('users.user_id, first_name,last_name,username,password,courses.course_name,courses.course_id as course_set,email,country_id,course_enrollments.course_id,status,country_id,course_enrollments.course_status as course_stud_status,reg_date,newsletter');
		$this->db->join('course_enrollments','course_enrollments.user_id = users.user_id');
		$this->db->join('courses','courses.course_id = course_enrollments.course_id');
		$query = $this->db->get('users');
		foreach($query->result() as $row)
		{
			$this->db->select('user_id');
			$this->db->where('user_id',$row->user_id);
			$this->db->where('course_id',$row->course_set);
			$query2 = $this->db->get('course_records');
			if($query2->num_rows()==0)
			{
				$password = $this->encrypt->decode($row->password);
				$data_array[] = array( $row->user_id,$row->first_name,$row->last_name,$row->username,$password,$row->course_name,
$row->reg_date,$row->email,$row->newsletter);
			}
			
		}
 $fields = ($field_array[] = array ("User Id","First Name","Last Name","Course","Date Enrolled","Email ID","Newsletter")  );
 
 		   $xls = new Excel_XML;
		   $xls->addArray ($field_array);
		   $xls->addArray ($data_array);
		   $xls->generateXML ( "student_list-0-percent" );
	}
	
	function sample_cert($course_id,$user_id)
	{
		$this->user_model->insert_certificate_request($course_id,$user_id);
		echo "inserteed";
	}
	function set_reddemed_coupon()
	{
		
		$this->db->select('coupon_code,giftVoucherCode,website,website_id,id');
		$this->db->from('giftvoucher');
	
		$this->db->join('redeemed_coupons','coupon_code=giftVoucherCode');
		$this->db->where('redeemed_coupons.website_id',0);	
		//$this->db->limit(1);
		
		$query = $this->db->get();
		echo "enter here<pre>";print_r($query->result());exit;
		//echo "<table><tr><td>gift Voucher</td><td>redeem Voucher</td><td>web id in gift voucher</td><td>web id in redeemed</td></tr>";
		foreach ($query->result() as $row)
		{
			$redeemed_details['website_id'] = $row->website;
			if($row->website_id==0)
			{
			$this->db->where('redeemed_coupons.id',$row->id);
			$this->db->update('redeemed_coupons',$redeemed_details);
			//echo "<tr><td>".$row->coupon_code."</td><td>".$row->giftVoucherCode."</td><td>".$row->website."</td><td>".$row->website_id."</td></tr>";
			}
		}
		
	}
	
	function validity_calculate_test()
	{
	 $expirity = $this->user_model->getVoucher_user_course(15675,4);
	 	echo"result = ";print_r($expirity);
		
	}
	
	function my_sample()
	{
		$user_id = 22619;
		$course_id = 3;
		
		 $certficate_details = $this->user_model->get_certficate_details($user_id,$course_id);
		  
		  $completed_date_date = $certficate_details['applied_on'];
		  
		  $course_completed_date = explode('-',$completed_date_date);
		  
		  $date_in_time_frmt = strtotime($certficate_details['applied_on']);
		  
		  $completed_year  = $course_completed_date[0];
		  $month_name = date('F Y', $date_in_time_frmt);
		  $completed_date  = $course_completed_date[2];
		  
		  
		  
		  echo "<br> Monath name ".$month_name;
		  echo "<br>Date ".$completed_date;
		  echo "<br> Year ".$completed_year;
		  exit;
	}
	
	
	
	
		
	function get_student_progress($course_id)
	{
		if(!$this->session->userdata('student_logged_in')){
		  redirect('home');
		}
		$stud_id=$this->session->userdata['student_logged_in']['id'];
		$course_status = $this->user_model->get_student_course_status($course_id,$stud_id); 	
		$course_name = $this->common_model->get_course_name($course_id); 
					
		 /*-----------------Start marks,progress calculations------------------------*/
					
					
		if($course_status!=0) // course started
		{		 
		  $courseUnitArray= $this->user_model->getCourseUnitListing($course_id,1); 
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
		  ////
		  $unitsIdArr = $this->user_model->getCourseUnitListing($course_id,1); 
		  
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
		  $numberOfDaysRemaining =0;
		  
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
		$progress['daysremaining']    = $numberOfDaysRemaining;
		
		
		
		return $progress;
		
	
	
	}
	
	
	
	
	
	 function certificate_download_new($course_id_encrypted)
  {	  
	$this->load->helper(array('dompdf', 'file'));	   
  	 if(!$this->session->userdata('student_logged_in')){
		  redirect('home');
		}
	$lang_id = $this->session->userdata('language');
	$user_id=$this->session->userdata['student_logged_in']['id'];
		$this->load->model('certificate_model');
	 //$this->load->library('encrypt');
	// $course_id = $this->encrypt->decode($course_id_encrypted);
	 $course_id = $course_id_encrypted;
	 $course_name = $this->common_model->get_course_name($course_id);
	 
	 $user_details = $this->user_model->get_student_details($user_id); 
	
	 foreach($user_details as $key => $value)
	 {
	 	$certificate_user_name = $value->first_name.'&nbsp;'.$value->last_name;		
	 }
	 $mark_details = $this->get_student_progress($course_id);
	 
	 
	$certificate_user_name = strtolower($certificate_user_name);
	$certificate_user_name = ucwords($certificate_user_name);
	 
	 //progressPercnt
	/* 
	 echo "<pre>";
	 print_r($mark_details);
	 exit;*/
	 $grade='falied';
	 	if($mark_details['coursePercentage'] >= 55 && $mark_details['coursePercentage'] <= 64.99)
	 	{
	 		$grade = $this->user_model->translate_('mark_pass');
		}
		else if($mark_details['coursePercentage'] >= 65 && $mark_details['coursePercentage'] <= 74.99)
	 	{
	 		$grade = $this->user_model->translate_('mark_pass_plus');
		}
		else if($mark_details['coursePercentage'] >= 75 && $mark_details['coursePercentage'] <= 84.99)
	 	{
	 		$grade = $this->user_model->translate_('mark_merit');
		}
		else if($mark_details['coursePercentage'] >= 85 )
	 	{
	 		$grade = $this->user_model->translate_('mark_dist');
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
       $month=$this->user_model->translate_('month_1');
   }
   else if($values[1]=='2')
   {
      $month=$this->user_model->translate_('month_2');
   }
   else if($values[1]=='3')
   {
      $month=$this->user_model->translate_('month_3');
   }else if($values[1]=='4')
   {
      $month=$this->user_model->translate_('month_4');
   }else if($values[1]=='5')
   {
      $month=$this->user_model->translate_('month_5');
   }else if($values[1]=='6')
   {
      $month=$this->user_model->translate_('month_6');
   }else if($values[1]=='7')
   {
      $month=$this->user_model->translate_('month_7');
   }else if($values[1]=='8')
   {
      $month=$this->user_model->translate_('month_8');
   }else if($values[1]=='9')
   {
      $month=$this->user_model->translate_('month_9');
   }else if($values[1]=='10')
   {
      $month=$this->user_model->translate_('month_10');
   }else if($values[1]=='11')
   {
      $month=$this->user_model->translate_('month_11');
   }else
   {
      $month=$this->user_model->translate_('month_12');
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
	 $data = pdf_create($html, 'TrendimiCertificate_new_'.$user_id.'_'.$course_id);
     //or
     //$data = pdf_create($html, '', false);
     write_file('name', $data);	
		
		
  
	  
  }
  
  
  function after_hardcopy()
  {
	  
	   // $user_id    = $this->uri->segment(3);
	/*	$payment_id = $this->uri->segment(4);
		$course_id  = $this->uri->segment(5);
		$product_id = $this->uri->segment(6);
		$user_id=$this->session->userdata['student_logged_in']['id'];
		
		//user_subscriptions
		
		$certificate_details =  $this->certificate_model->get_certificate_details($user_id,$course_id);
		foreach($certificate_details as $key => $value)
		{
			$applied_date = $value->applied_on;	
			$certificate_id = $value->id;	
		}
		
		
		
		 $today = date("Y-m-d");
		 $insert_data=array("user_id"=>$user_id,"course_id"=>$course_id,"type"=>'hardcopy',"date_applied"=>$today);
		 
		 $this->user_model->insertQuerys("user_subscriptions",$insert_data);
		 
		 $insert_data_hardcopy =array("student_certificate_id	"=>$certificate_id,"user_id"=>$user_id,"course_id"=>$course_id,"hardcopy_apply_date"=>$today,"post_status"=>'pending');
		 
		 $this->user_model->insertQuerys("certificate_hardcopy_applications",$insert_data_hardcopy);
		 */
		
		$this->load->helper(array('dompdf', 'file'));	   
  	 if(!$this->session->userdata('student_logged_in')){
		  redirect('home');
		}
	$lang_id = $this->session->userdata('language');
	$user_id=$this->session->userdata['student_logged_in']['id'];
	/*echo "User id ".$user_id;
	exit;*/
	
		$this->load->model('certificate_model');
	 //$this->load->library('encrypt');
	// $course_id = $this->encrypt->decode($course_id_encrypted);
	 $course_id = 5;
	 $course_name = $this->common_model->get_course_name($course_id);
	 
	 $user_details = $this->user_model->get_student_details($user_id); 
	
	 foreach($user_details as $key => $value)
	 {
	 	$certificate_user_name = $value->first_name.' '.$value->last_name;		
	 }
	 $mark_details = $this->get_student_progress($course_id);
	 
	// $certificate_user_name = 'SARATH  BELWOOD';
	//$certificate_user_name = strtoupper($certificate_user_name);
	 /*echo "<br>Certficate user name ".$certificate_user_name;
	 
	 
	 
	$certificate_user_name = strtolower($certificate_user_name);
	echo "<br>Certficate user name lowered ".$certificate_user_name;
	$certificate_user_name = ucwords($certificate_user_name);
	 
	 echo "<br>Certficate user ucwords ".$certificate_user_name;
	 exit;*/
	 //progressPercnt
	/* 
	 echo "<pre>";
	 print_r($mark_details);
	 exit;*/
	 $grade='falied';
	 	if($mark_details['coursePercentage'] >= 55 && $mark_details['coursePercentage'] <= 64.99)
	 	{
	 		$grade = $this->user_model->translate_('mark_pass');
		}
		else if($mark_details['coursePercentage'] >= 65 && $mark_details['coursePercentage'] <= 74.99)
	 	{
	 		$grade = $this->user_model->translate_('mark_pass_plus');
		}
		else if($mark_details['coursePercentage'] >= 75 && $mark_details['coursePercentage'] <= 84.99)
	 	{
	 		$grade = $this->user_model->translate_('mark_merit');
		}
		else if($mark_details['coursePercentage'] >= 85 )
	 	{
	 		$grade = $this->user_model->translate_('mark_dist');
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
       $month=$this->user_model->translate_('month_1');
   }
   else if($values[1]=='2')
   {
      $month=$this->user_model->translate_('month_2');
   }
   else if($values[1]=='3')
   {
      $month=$this->user_model->translate_('month_3');
   }else if($values[1]=='4')
   {
      $month=$this->user_model->translate_('month_4');
   }else if($values[1]=='5')
   {
      $month=$this->user_model->translate_('month_5');
   }else if($values[1]=='6')
   {
      $month=$this->user_model->translate_('month_6');
   }else if($values[1]=='7')
   {
      $month=$this->user_model->translate_('month_7');
   }else if($values[1]=='8')
   {
      $month=$this->user_model->translate_('month_8');
   }else if($values[1]=='9')
   {
      $month=$this->user_model->translate_('month_9');
   }else if($values[1]=='10')
   {
      $month=$this->user_model->translate_('month_10');
   }else if($values[1]=='11')
   {
      $month=$this->user_model->translate_('month_11');
   }else
   {
      $month=$this->user_model->translate_('month_12');
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


		 $data = pdf_create($html, 'cert_'.$user_id.'_'.$course_id,false);		
	
		//$data = pdf_create($html, '', false);	
		$this->path = "public/certificate/hardcopy/TrendimiCertificate_".$user_id."_".$course_id.".pdf";
		write_file($this->path, $data);
     
		
		// end case2 ******************************	
		$sendemail = true;
		
		
		
		 $stud_details=$this->user_model->get_stud_details($user_id);	
		 
		  foreach($stud_details as $val2)
		  {
			 $user_country_name = $this->user_model->get_country_name($val2->country_id);
			 $user_house_number = $val2->house_number;
			 $user_address = $val2->address;
			 $user_city = $val2->city;
			 $user_zip_code = $val2->zipcode;
			  $user_mail = $val2->email;
			 
		  }
		
		
		
		
		if($sendemail)
		{
			$this->load->library('email');
			//$tomail = 'info@trendimi.net';
			//$tomail = 'certificates@trendimi.net';
			//$tomail = 'ajithupnp@gmail.com';
			$tomail = 'bhagathindian@gmail.com';
					
					  $emailSubject = "Hard copy request : ".$user_mail;
					  $mailContent = "<p>Please find the attachment of hard copy certificate here with it. <p>";
					  
					  $mailContent .= "<p>User name  : ".$certificate_user_name."</p>";
					   $mailContent .= "<p>House Number :  ".$user_house_number."</p>";
					   $mailContent .= "<p>Address :  ".$user_address."</p>";					  
					   $mailContent .= "<p>City :  ".$user_city."</p>";
					   $mailContent .= "<p>Zip code :  ".$user_zip_code."</p>";
					   $mailContent .= "<p>Country : ".$user_country_name."</p>";
						   	
					  $this->email->from('info@trendimi.net', 'Team Trendimi');
					  $this->email->to($tomail); 
					  $this->email->attach($this->path);
					  
					  $this->email->subject($emailSubject);
					  $this->email->message($mailContent);	
					  
					 $this->email->send();
		}
	
	
	
	 redirect('coursemanager/success_hardcopy', 'refresh');
		
		 
		 
  
  }
  
 function eTranscript_download($course_id)
  {
	  
		  $this->load->helper(array('dompdf', 'file'));
		   $userId = $this->session->userdata['student_logged_in']['id'];	
		  $user_name = $this->common_model->get_user_name($userId);
	
		 
		  $course_name = $this->common_model->get_course_name($course_id); 
		  $slNo=0;
	
		  $courseUnitArray=$this->user_model->getCourseUnitListing($course_id,$userId);
		 
		 
		 	if(!empty($courseUnitArray)) {
		foreach($courseUnitArray as $courseUnitArr) {	
			$unitId        = $courseUnitArr['course_units_idcourse_units'];
			//whether the unit is completed or not by checking the pages in the unit================================================
			//$unitComplete  = $objTest->getUnitCompleteByUser($userId,$unitId,$course_id);	
			$pageIdsArr[$unitId]=$this->user_model->getPageIdsForUnits($unitId);
			$studentPageIdsArr[$unitId] = $this->user_model->getStudentProgressPageIds($userId,$unitId,$course_id);
			
			//========================================================================================================
			//total tasks in the unit
			$taskArray[$unitId]     = $this->user_model->getTasksInUnit_forMarks($unitId);//echo "<p>";print_r($taskArray);exit;
			$userTaskArray[$unitId] = $this->user_model->getTasksForUserInUnitNew($userId,$unitId,$course_id);
			
			//the marks obtained by user in a particular unit in a course
			$marksDetails[$unitId]  =  $this->user_model->getUnitMarksForTasks($userId,$unitId,$course_id);								
				
		}
	}


		$marks_data['course_id']=$course_id;
		$marks_data['courseUnitArray']=$courseUnitArray;
		$marks_data['unitId']=$unitId;
		$marks_data['pageIdsArr']=$pageIdsArr;
		$marks_data['studentPageIdsArr']=$studentPageIdsArr;	
		$marks_data['taskArray']=$taskArray;
		$marks_data['userTaskArray']=$userTaskArray;
		$marks_data['marksDetails']=$marksDetails;
		
		 $grade='failed';
		$mark_details = $this->get_student_progress($course_id);		
	 	if($mark_details['coursePercentage'] >= 55 && $mark_details['coursePercentage'] <= 64.99)
	 	{
	 		$grade = $this->user_model->translate_('mark_pass');
		}
		else if($mark_details['coursePercentage'] >= 65 && $mark_details['coursePercentage'] <= 74.99)
	 	{
	 		$grade = $this->user_model->translate_('mark_pass_plus');
		}
		else if($mark_details['coursePercentage'] >= 75 && $mark_details['coursePercentage'] <= 84.99)
	 	{
	 		$grade = $this->user_model->translate_('mark_merit');
		}
		else if($mark_details['coursePercentage'] >= 85 )
	 	{
	 		$grade = $this->user_model->translate_('mark_dist');
		}
		//$cssLink = "public/user/css/eTranscript_make_up.css";
		$html ='';
		
		$html .='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Course eTranscript</title>

</head>
<body>';
		if($course_id == 3 || $course_id == 13)
		{
			
		
$html .='<style>

.outer{height:940px; width:720px; margin:0 auto; font-family: "Open Sans", sans-serif}
.header{text-align:right; padding:20px; background:url(/public/letters/css/logoj.jpg) 485px 20px no-repeat}

.logotxt, .letterNme, .courseNme{font-family: "Great Vibes", cursive;}
.logotxt{font-size:22pt; font-weight:normal; margin:1em 1.8em 0 0; padding:0; color:#df3e8e}
.letterNme{font-size:32pt; font-weight:normal; margin:0; padding:0; color:#a5218c; position:absolute; left:1em; top:-1.3em}
.courseNme{font-size:37pt; font-weight:normal; margin:0; padding:0; color:#df3e8e; text-align:center}
.content{background:#a9dde1; padding:4em 0 2em 0; border-radius:20px; width:720px; float:left; height:696px; position:relative}
.clear{clear:both}
p{font-size:12pt; color:#555; margin:0; padding:0.5em 0; font-family: "Andada", serif; line-height:1.5em}
ul{list-style:none; display:block; font-size:12pt; color:#555; margin:12em 0 0 0; padding:0; font-family: "Andada", serif; line-height:1.4em}
.studentNme{font-size:15pt; margin:0.5em 0 0 0; padding:0; text-align:center; color:#a5218c; font-weight:normal}
.cEo{font-size:13pt;padding:0;color:#555; margin:-5em 0 0 28em; font-weight:normal}
.cEo span{clear:left; display:block}
table{width:100%; background:#a9dde1; margin:2em 0}
table th{padding:0.5em; border-top:0.6em #fff solid; border-bottom:0.2em #fff solid; color:#555}
table td{border-bottom:0.1em #fff solid; padding:0.2em 0.5em; font-size:11pt; font-family: "Open Sans", sans-serif; color:#fff}
.center{text-align:center}
.left{text-align:left}


</style>';
		}
		else
		{
			$html .='<style>

.outer{height:940px; width:720px; margin:0 auto; font-family: "Open Sans", sans-serif; position:relative}
.header{text-align:right; padding:20px; background:url(/public/letters/css/logoj.jpg) 485px 20px no-repeat}

.logotxt, .letterNme, .courseNme{font-family: "Great Vibes", cursive;}
.logotxt{font-size:22pt; font-weight:normal; margin:1em 1.8em 0 0; padding:0; color:#df3e8e}
.letterNme{font-size:32pt; font-weight:normal; margin:0; padding:0; color:#a5218c; position:absolute; left:1em; top:-1.3em}
.courseNme{font-size:37pt; font-weight:normal; margin:0; padding:0; color:#df3e8e; text-align:center}
.content{background:#a9dde1; padding:4em 0 2em 0; border-radius:20px; width:720px; float:left; height:696px; position:relative}
.clear{clear:both}
p{font-size:12pt; color:#555; margin:0; padding:0.5em 0; font-family: "Andada", serif; line-height:1.5em}
ul{list-style:none; display:block; font-size:12pt; color:#555; margin:12em 0 0 0; padding:0; font-family: "Andada", serif; line-height:1.4em}
.studentNme{font-size:15pt; margin:0.5em 0 0 0; padding:0; text-align:center; color:#a5218c; font-weight:normal}
.cEo{font-size:13pt;padding:0;color:#555; position:absolute; bottom:.4em; left:31em; margin:0; font-weight:normal}
.cEo span{clear:left; display:block}
table{width:100%; background:#a9dde1; margin:2em 0}
table th{padding:0.5em; border-top:0.6em #fff solid; border-bottom:0.2em #fff solid; color:#555}
table td{border-bottom:0.1em #fff solid; padding:0.5em; font-size:11pt; font-family: "Open Sans", sans-serif; color:#fff}
.center{text-align:center}
.left{text-align:left}


</style>';
			
			
			
		}

		
$html .='<div class="outer">
<div class="header">
<h2 class="logotxt">- online learning -</h2>

</div>
<div class="clear"></div>
<div class="content">
<h2 class="letterNme">Course eTranscript</h2>
<div class="clear"></div>
<h1 class="courseNme">Course: '.$course_name.'</h1>
<div class="clear"></div>
<h3 class="studentNme">Name of student: '.$user_name.'</h3>
<div class="clear"></div>
<h3 class="studentNme">Trendimi grade: '.$grade.'</h3>
<div class="clear"></div>
<table border="0" cellpadding="0" cellspacing="0.1em">
  <tr>
    <th scope="col" class="left" style="border-right:0.2em solid #fff">Unit</th>
    <th scope="col" class="left" style="border-right:0.2em solid #fff">Module Title</th>
    <th scope="col" class="center">Overall score result</th>
  </tr>
';

 
		
		if(!empty($marks_data))
		{
			if(!empty($marks_data['courseUnitArray'])) {
		
			$unitSlno            = 0;			
			$completedMarks1     = 0;
			$completedMarks2     = 0;	
			$countCompleted      = 0;
			$countTotal          = 0;	
			$completedPercentage = 0;
			
			foreach($marks_data['courseUnitArray'] as $courseUnitArr) {	
			
				$percentage    = 0;
				$unitId        = $courseUnitArr['course_units_idcourse_units'];
				
				//whether the unit is completed or not by checking the pages in the unit================================================
				//$unitComplete  = $objTest->getUnitCompleteByUser($userId,$unitId,$course_id);	
				$pageIdsArr =$marks_data['pageIdsArr'][$unitId];			
				
		        $studentPageIdsArr = $marks_data['studentPageIdsArr'][$unitId];
				if (is_array($pageIdsArr) && is_array($studentPageIdsArr)) {
				$pageDiffArr = array_diff($pageIdsArr,$studentPageIdsArr); 
				
				if(empty($pageDiffArr)) {
				$unitComplete=1;
				}
				else
				{
					$unitComplete=0;
				}
				} else if (!is_array($studentPageIdsArr)){
			      $unitComplete=0;
				}else if(empty($pageIdsArr))
				{
				$unitComplete=1;
				}		
				//========================================================================================================
				//total tasks in the unit
				$taskArray     = $marks_data['taskArray'][$unitId];
				//echo "<pre>-------------<br>";print_r($taskArray);echo "</pre>";
				$totalTask     = count($taskArray);
				//tasks in the unit which is attended by user
						
				$userTaskArray = $marks_data['userTaskArray'][$unitId];
				$totalTaskUser = count($userTaskArray);
				
				//the marks obtained by user in a particular unit in a course
				$marksDetails  =  $marks_data['marksDetails'][$unitId];	
				
				if(!empty($marksDetails)) {
				
					$totalMarks      = $marksDetails['totalMarks'];
					$totalQuestions  = $marksDetails['totalQuestions'];
					
					$completedMarks1 = $completedMarks1+$totalMarks ;
					$completedMarks2 = $completedMarks2+$totalQuestions ;
						
					$markPerc        = @($totalMarks/$totalQuestions)*100;
					
					if($markPerc!=''){
						$percentage=@round($markPerc,2);
					}
					
					//$completedPercentage = $percentage+$completedPercentage ;					
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
		
		$countTotal		 = $countTotal;
		$countCompleted  = $countCompleted;		

$y = 0;
//comented by deepu- activate only if client need details such as failed, pass, etc
if(!empty($marks_data['courseUnitArray'])) {
				$unitSlno=0;
				foreach($marks_data['courseUnitArray'] as $courseUnitArr) {
				
					$InfoText = "Studying";
					
					$unitId   = $courseUnitArr['course_units_idcourse_units'];	
					$unitSlno++;
										
					if(trim($unitId)!='') {
					
						$unitDetails    =$this->user_model->get_courseunits($unitId);
						$sectionArray   = $this->user_model->getcourse_sections($unitId);
						
					}	
										
					$unitPercentage     = $unitMarkArray[$unitId]['percentage'];
					$unitComplete       = $unitMarkArray[$unitId]['complete'];
					$unitTotalTask      = $unitMarkArray[$unitId]['totalTask'];				
					$unitTotalTaskUser  = $unitMarkArray[$unitId]['totalTaskUser'];	
					$totalTaskArray     = $unitMarkArray[$unitId]['totalTaskArray'];	
					$totalTaskUserArray = $unitMarkArray[$unitId]['totalTaskUserArray'];	
					
						
					//show the status for each unit whether passed or not etc
					if( ($unitTotalTask<=$unitTotalTaskUser) and ($unitPercentage<55) and ($unitTotalTaskUser!=0) ){
					
						$InfoText= "Failed";
						
					} else if(($unitTotalTask<=$unitTotalTaskUser) and ($unitPercentage>55) and ($unitTotalTaskUser!=0) ) {
					
						$InfoText= "Passed";
					
					} 


$mark_percentage = array();
	if($unitTotalTask!=0) {
if(!empty($totalTaskArray)) {

	
					foreach($totalTaskArray as $totalTaskArr) {
					
					 	$taskId      = $totalTaskArr['tasks_idtasks'];
					 	$pageId      = $totalTaskArr['idcourse_section_pages'];
						$pageNumber  = $totalTaskArr['pageNumber'];
					//	$sectionName = $totalTaskArr['sectionName'];
						$unitName    = $totalTaskArr['unitName'];
						
					}	
 $html .= '<tr>
<td class="left">'.($y+1).'</td>
<td class="left">'.$unitName.'</td>
<td class="center">'.$unitPercentage.' %</td>
</tr>';
		
				}
				
				} else {
$html .= '<tr>
<td class="left">'.($y+1).'</td>
<td class="left">'.$marks_data['courseUnitArray'][$y]['unitName'].'</td>
<td class="center">No tasks are there in this unit</td>
</tr>';
				}
				
				$y++;
				}
				
				
			}	
			
			
			
				 }// end for userCoursesArr
				 			
$html .= '</table>
<div class="clear"></div>
<h3 class="cEo">Francisca Tomàs
<span>CEO</span></h3>
</div>
</div>
</body>
</html>
';
		
		/*echo $html;
		exit;*/
	
	 $data = pdf_create($html, 'eTrans_'.$userId.'_'.$course_id);
     //or
     //$data = pdf_create($html, '', false);
     write_file('name', $data);		  
	  	  
		  
		  
	  
  }
 function country_cur_test()
 {
	 	//$this->load->library('geoip_lib');
		//$ip = $this->input->ip_address();
		$ip[0] = '184.2.126.8';
		$ip[1] = '50.129.253.96';
		$ip[2] ='70.240.39.27';
		$ip[3] = '76.106.103.56';//united kingdom
		//$ip[4] = "181.231.255.255";
		//$ip[5] = "57.73.18.255";
		//$ip[6] = "2.16.5.255";
		//$ip[7] = "192.88.203.255";
		






		for($i=0;$i<count($ip);$i++)
		{
    	$this->geoip_lib->InfoIP($ip[$i]);
    	//echo "<br>contry name in 3 : ".$this->code3= $this->geoip_lib->result_country_code3();
		echo "<br>sample ip : ".$ip[$i];
     	echo "<br>contry name : ".$this->con_name = $this->geoip_lib->result_country_name();
		
		$curr_code=$this->user_model->get_currency_id($this->con_name);
		//echo "<pre>";print_r($curr_code);
		if($curr_code!=1)
		  {
			foreach ($curr_code as $value)
			{
			 $this->currId= $value->currency_idcurrency;
			 $this->currencyCode=$value->currencyCode;
			}
		  }
		  else 
		  {
			$this->currId=1;
			$this->currencyCode='EUR';
		  }
		  
		  echo "<br>Currency code : ".$this->currencyCode;
		   $content['language']=$this->language;
   			echo "<br><pre>currency code =------------ ";print_r($curr_code=$this->user_model->get_currency_id($this->con_name));
			echo "</pre>";
		  echo "<br>--------------------------------------------------------------------<br>";
		 
		
			$curr_id= $this->currId;
		$content['currency_code'] = $this->currencyCode;
		$content['curr_id'] = $curr_id;
		}
 }
 
 	function generate_sales_report()
	{
		
		//$this->load->view('admin/header');
		$this->load->helper(array('php-excel'));	
	
	$user_name  = $this->session->flashdata('user_name');
	$first_name = $this->session->flashdata('first_name');
	$last_name  = $this->session->flashdata('last_name');
	$start_date = $this->session->flashdata('start_date');
	$end_date   = $this->session->flashdata('end_date'); 
	
	
	
	
	    $this->db-> select('users.user_id, first_name,last_name,username,email,country_id,payments.date,payments.amount,payments.product_id,currency.currency_code,sales_cart_main.source');
		$this->db-> from('payments');
		$this->db-> where('payments.type',"sales");
	//	$this->db->group_by('users.user_id');
		$this->db-> order_by('payments.date', 'asc');
		$this->db->join('users',"payments.user_id = users.user_id");
		$this->db->join('currency',"currency.id = payments.currency_id");
		$this->db->join('sales_cart_main',"sales_cart_main.transaction_id=payments.transaction_id");
		//$this->db->join('payments',"payments.type = 'sales'");
		
	
		if(isset($first_name) && $first_name!=''){
			$this->db->like('first_name', $_GET['first_name']); 
		}
		if(isset($last_name) && $last_name!=''){
			$this->db->like('last_name', $_GET['last_name']); 
		}
		if(isset($user_name) && $user_name!=''){
			$this->db->where('username', $user_name); 
		}		
		if(isset($start_date) && $start_date!=''){
			$this->db->where('payments.date >=', $start_date); 
		}	
		if(isset($end_date) && $end_date!=''){
			$this->db->where('payments.date <', $end_date); 
		}		
		$query = $this->db->get();
		
		$sql=$query->result();
		if(!empty($sql))
		{
			   $fields = ($field_array[] = array ("Sl No.","Name","User id","Product","Source","Amount","Date"));
			   $sl_no =1;
				foreach ($sql as $row)
				 {	
				  $purchased_date = new DateTime($row->date);			
			  	  $purchased_date = date_format($purchased_date,'Y-m-d');					  

 				$product_details = $this->common_model->get_product_details($row->product_id);		 			
				  $product_name = '';
				  
				   if($product_details[0]->type=='course')
				  {
					  $product_name = 'Course';
				  }
				  else if($product_details[0]->type=='hardcopy')
				  {
					  $product_name = 'Certficate hardcopy copy';
				  }				 
				  else if($product_details[0]->type=='extension')
				  {
					  $product_name = 'Extension';
				  }
				  else if($product_details[0]->type=='access')
				  {
					  $product_name = 'Course access';
				  }
				  else if($product_details[0]->type=='ebooks')
				  {
					  $product_name = 'Ebooks';
				  }				  
				  else if($product_details[0]->type=='transcript')
				  {
					  $product_name = 'Transcript';
				  }
				  else if($product_details[0]->type=='transcript_hard')
				  {
					  $product_name = 'Transcript hard copy';
				  }				  
				  else if($product_details[0]->type=='proof_completion')
				  {
					  $product_name = 'Proof completion soft copy';
				  }
				  else if($product_details[0]->type=='proof_completion_hard')
				  {
					  $product_name = 'Proof completion hard copy';
				  }
				  else if($product_details[0]->type=='poe_soft')
				  {
					  $product_name = 'Proof of enrollement soft copy';
				  }				  
				  else if($product_details[0]->type=='poe_hard')
				  {
					  $product_name = 'Proof of enrollement hard copy';
				  }
				  else if($product_details[0]->type=='colour_wheel_soft')
				  {
					  $product_name = 'Colour wheel';
				  }
				  else if($product_details[0]->type=='colour_wheel_hard')
				  {
					  $product_name = 'Colour wheel hard copy';
				  }
				  
				 				 
						 
					$data_array[] = array($sl_no,$row->first_name.' '.$row->last_name,$row->user_id,$product_name,$row->source,$row->amount.' '.$row->currency_code,$purchased_date);
								  $sl_no++;	
								 }
								
							   $xls = new Excel_XML;
							   $xls->addArray ($field_array);
							   $xls->addArray ($data_array);
							   $xls->generateXML ( "Sales_report" );
						}
				else
				{
					$this->session->set_flashdata('message',"Empty result!");
				redirect('admin/sales/sales_purchase_details');
				}

	
	}
	
		function course_progress_array($course_id)
	{
		
		if(!$this->session->userdata('student_logged_in')){
		  redirect('home');
		}
		$course_passed = 1;
		
		echo $course_passed;
		
		$stud_id=$this->session->userdata['student_logged_in']['id'];
		
		//$stud_id= 5876;
		
		echo "User id ".$stud_id;
		$course_status = $this->user_model->get_student_course_status($course_id,$stud_id); 	
		$course_name = $this->common_model->get_course_name($course_id); 
					
		 /*-----------------Start marks,progress calculations------------------------*/
		$numberOfDaysRemaining=0;			
					
		if($course_status!=0) // course started
		{		 
		  $courseUnitArray= $this->user_model->getCourseUnitListing($course_id,1); 
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
			  echo "<pre>";
			  print_r( $marksDetails);
			                 
			  if(!empty($marksDetails)) {
				  $totalMarks      = $marksDetails['totalMarks'];
				  $totalQuestions  = $marksDetails['totalQuestions'];
				  
				  
				$completedMarks1 = $completedMarks1+$totalMarks ;
				$completedMarks2 = $completedMarks2+$totalQuestions ;
				$markPerc        = @($totalMarks/$totalQuestions)*100;
				if($markPerc!=''){
				  $percentage=@round($markPerc,2);
				  if($percentage<55)
				  {
					  $course_passed = 0;
				  }
				  
				 				  
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
		  ////
		  $unitsIdArr = $this->user_model->getCourseUnitListing($course_id,1); 
		  
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
		$progress['daysremaining']    = $numberOfDaysRemaining;
		$progress['course_passed']    = $course_passed;
//		echo "Course passed ".$course_passed;
		
echo "<pre>";		
print_r($progress);
		
		return $progress;
		
	
	
	
	}
	
	
	function apply_certificate($course_id)
  	{
  		$user_id = $this->session->userdata['student_logged_in']['id'];	
		
		$mark_details = $this->get_student_progress($course_id);
		
		if($mark_details['progressPercnt']==100 && $mark_details['coursePercentage']>=55 && $mark_details['course_passed']==1)
		{ 
		
		
			
		$this->user_model->insert_certificate_request($course_id,$user_id);
		
		$data=array("course_status"=>'4'); // change status to Certificate applied
		
		$this->user_model->update_student_enrollments($course_id,$user_id,$data);
		
		
		
		 $stud_details=$this->user_model->get_stud_details($user_id);	
		 
		  foreach($stud_details as $val2)
		  {
			 $user_email= $val2->email;
			 $user_name = $val2->first_name;
			 $lang_id = $val2->lang_id;
		  }
		
		   $course_name = $this->common_model->get_course_name($course_id);
		   
		   $course_name = strtolower($course_name);
		   $course_name = ucwords($course_name);
		   
		  
		
		   $mail_for = "cerificate_approved";
			$email_details = $this->email_model->getTemplateById($mail_for,$lang_id);
			foreach($email_details as $row)
			{
				
				$email_subject = $row->mail_subject;
				$mail_content = $row->mail_content;
			}
			
			
			$tomail = $user_email;
			
		
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
			redirect('coursemanager/certificate_pre', 'refresh');
		}
		
		
		
  
  	}
	
	function sales_certificate($cert_product_id=NULL)
	{
		
			$this->load->model('sales_model','',TRUE);
				
				if(isset($_POST['currency_id']))
				{
					$cert_cur_id = $_POST['currency_id'];
				//	echo "<br>Currency id ".$cert_cur_id;
				}
				
				if(isset($_POST['course_id']))
				{
					$course_id = $_POST['course_id'];
					
					$sess_array = array('cart_course_id' => $course_id); 
			
					$this->session->set_userdata($sess_array);
				}
		
		//echo "Session Course id ".$this->session->userdata('cart_course_id');
		$this->load->model('ebook_model');
		
		if(!$this->session->userdata('student_logged_in')){
		  redirect('home');
		}	
		$user_id = $this->session->userdata['student_logged_in']['id'];
		
		
		if($cert_product_id != NULL && isset($_POST['course_id']))
		{
		$this->session->unset_userdata('cart_session_id');
			
		if(!$this->session->userdata('cart_session_id'))
		{	
		
			 if(isset($_POST['house_number']))
			{			
			$student_update_data['house_number'] = $apartment  = $this->input->post('house_number');
			$student_update_data['address'] = $address1  = $this->input->post('address1');
			//$address2  = $this->input->post('address2');	
		//	$student_update_data['country_id'] = $country  = $this->input->post('country');
			$student_update_data['zipcode'] = $zip_code  = $this->input->post('zip_code');
			$student_update_data['city'] = $city  = $this->input->post('city');
			
			$this->user_model->update_student_details($student_update_data,$user_id);
			}
				
			$sess_array = array('cart_session_id' => session_id()); 
			
			session_regenerate_id();
			
			$this->session->set_userdata($sess_array);	
			
			$sess_array = array('cart_source' => '/home/sales_certificates/');
			$this->session->set_userdata($sess_array);
			
			
			/*echo "<pre>";
			print_r ($sess_array);
			
			echo "<pre>";
			print_r ($_SESSION);*/
			
		//	echo  "Session id  ".$this->session->userdata['cart_session_id'];		
			
			$product_details = $this->common_model->get_product_details($cert_product_id);
			
			$product_price_details = $this->common_model->getProductFee($cert_product_id,$cert_cur_id);
			
			/*echo "Product id  ".$product_id;
			echo "<br> Selected items ".$new_selected_values;
			echo "<br>";
			
			echo "<pre>";
			print_r($product_details);
			
			echo "<br>";
			
			echo "<pre>";
			print_r($product_price_details);*/
			
			$cart_main_insert_array = array("session_id"=>$this->session->userdata('cart_session_id'),"user_id"=>$user_id,"source"=>'certificates',"item_count"=>1,"total_cart_amount"=>$product_price_details['amount'],"currency_id"=>$product_price_details['currency_id']);
			
			//$cart_main_id =1;
  		  
		    $cart_main_id = $this->common_model->insert_to_table("sales_cart_main",$cart_main_insert_array);
				
				
		/*	echo "<br> Main insert array ";
			
			echo "<pre>";
			print_r($cart_main_insert_array);*/
				
									
			$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$cert_product_id,"item_amount"=>$product_price_details['amount'],"currency"=>$product_price_details['currency_id']);
			
			/*echo "<br> Item details array ";
			
			echo "<pre>";
			print_r($item_details_array);*/
			//$cart_items_id =10;
			
			$cart_items_id = $this->common_model->insert_to_table("sales_cart_items",$item_details_array);		
			
			$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>$product_details[0]->type,"selected_item_ids"=>$course_id);
			
			
			$this->session->unset_userdata('cart_session_type');			
			$sess_array = array('cart_session_type' => $product_details[0]->type);			
			$this->session->set_userdata($sess_array);	
			
			/*echo "<br> Items array ";
			
			echo "<pre>";
			print_r($items_array);*/
			
			$item_id = $this->common_model->insert_to_table("sales_cart_item_details",$items_array);	
			
			$currency_symbol = $this->common_model->get_currency_symbol_from_id($product_price_details['currency_id']);
			
		}
		
		}
		
		
		
		
		
		$content = array();
		$ebook_product_ids = array();
		$ebook_units = array();
		$ebook_price_details = array();
		$cert_product_id = array();
		
		$currency_id = $this->currId;
		$currency_code = $this->currencyCode;	
		
		
		
		$ebook_offer_options = $this->common_model->get_product_by_type('ebooks');
		
		$k=0;
		foreach($ebook_offer_options as $ebook_det)
		{
			$ebook_product_ids[$k] = $ebook_det->id; 
			$ebook_units[$k]  = $ebook_det->units;
  			$ebook_price_details[$k] = $this->common_model->getProductFee($ebook_det->id,$currency_id);
						
			$k++;
		}
		
				
		
		$ebook_array = $this->ebook_model->fetchEbookByLang($this->language);
		
		
		
		$course_offer_options = $this->sales_model->get_product_for_sales_by_type('course');
		
		$q =0;
		foreach($course_offer_options as $course_det)
		{
			$course_product_ids[$q] = $course_det->id; 
			$course_units[$q]  = $course_det->units;
  			$course_price_details[$q] = $this->common_model->getProductFee($course_det->id,$currency_id);						
			$q++;
			
		}
		
		$colour_wheel_soft= $this->common_model->get_product_by_type('colour_wheel_soft');		
		foreach($colour_wheel_soft as $wheel_soft)
		{
			$colour_wheel_soft_id = $wheel_soft->id; 			
  			$colour_wheel_soft_price = $this->common_model->getProductFee($wheel_soft->id,$currency_id);			
		}
		
		$colour_wheel_hard = $this->common_model->get_product_by_type('colour_wheel_hard');		
		foreach($colour_wheel_hard as $wheel_hard)
		{
			$colour_wheel_hard_id = $wheel_hard->id; 			
  			$colour_wheel_hard_price = $this->common_model->getProductFee($wheel_hard->id,$currency_id);			
		}
			
		$certficate_hard_copy = $this->common_model->get_product_by_type('hardcopy');
		/*echo "<br>Cetficate hard copy";
		echo "<pre>";
		print_r($certficate_hard_copy);
		exit;*/
		$q =0;	
		foreach($certficate_hard_copy as $cert_hard)
		{
			$cert_product_id[$q]  	  = $cert_hard->id; 			
  			$cert_fee_deatils[$q] 	 = $this->common_model->getProductFee($cert_hard->id,$currency_id);	
			$postage_details		  = $this->sales_model->get_postage_options($cert_hard->item_id); 	
			$cert_postage_deatils[$q] = $postage_details[0]->postage_type;
			$q++;	
		}
		
		
		$proof_of_study_soft = $this->common_model->get_product_by_type('poe_soft');
		foreach($proof_of_study_soft as $poe_soft)
		{
			$proof_of_study_soft_id = $poe_soft->id; 			
  			$proof_of_study_soft_price = $this->common_model->getProductFee($poe_soft->id,$currency_id);			
		}
		
		$proof_of_study_hard = $this->common_model->get_product_by_type('poe_hard');
		foreach($proof_of_study_hard as $poe_hard)
		{
			$proof_of_study_hard_id = $poe_hard->id; 			
  			$proof_of_study_hard_price = $this->common_model->getProductFee($poe_hard->id,$currency_id);			
		}
		
		$proof_of_completion_soft = $this->common_model->get_product_by_type('proof_completion');
		foreach($proof_of_completion_soft as $poc_soft)
		{
			$proof_of_completion_soft_id = $poc_soft->id; 			
  			$proof_of_completion_soft_price = $this->common_model->getProductFee($poc_soft->id,$currency_id);			
		}
		
		$proof_of_completion_hard = $this->common_model->get_product_by_type('proof_completion_hard');
		foreach($proof_of_completion_hard as $poc_hard)
		{
			$proof_of_completion_hard_id = $poc_hard->id; 			
  			$proof_of_completion_hard_price = $this->common_model->getProductFee($poc_hard->id,$currency_id);			
		}
		$e_transcript_soft = $this->common_model->get_product_by_type('transcript');
		foreach($e_transcript_soft as $e_t_soft)
		{
			$e_transcript_soft_id = $e_t_soft->id; 			
  			$e_transcript_soft_price = $this->common_model->getProductFee($e_t_soft->id,$currency_id);			
		}
		
		$e_transcript_hard = $this->common_model->get_product_by_type('transcript_hard');
		foreach($e_transcript_hard as $e_t_hard)
		{
			$e_transcript_hard_id = $e_t_hard->id; 			
  			$e_transcript_hard_price = $this->common_model->getProductFee($e_t_hard->id,$currency_id);			
		}
		
		
		
		$cousre_subscruption = $this->common_model->get_product_by_type('access');
		foreach($proof_of_study_soft as $poe_soft)
		{
			$proof_of_study_soft_id = $poe_soft->id; 			
  			$proof_of_study_soft_price = $this->common_model->getProductFee($poe_soft->id,$currency_id);			
		}
		
		
		$cousre_material_subscription = $this->common_model->get_product_by_type('access');
		
		$q =0;	
		foreach($cousre_material_subscription as $access_data)
		{
			//$material_sub_product_id[$q]  	  = $access_data->id; 			
  			$material_sub_fee_deatils[$q] 	 = $this->common_model->getProductFee($access_data->id,$currency_id);				
			$q++;	
		}
		
		
			/*echo "<br>cert_product_id ";
			echo "<pre>";
			print_r($cert_product_id);
			
			echo "<br>cert_postage_deatils";
			echo "<pre>";
			print_r($cert_postage_deatils);
			
			
			echo "<br> cert_fee_deatils ";
			echo "<pre>";
			print_r($cert_fee_deatils);
		*/
			
			
		$user_id = $this->session->userdata['student_logged_in']['id'];
		$enrolled_course_ids = array();
		$enrolled_courses = $this->user_model->get_courses_student($user_id);
		foreach($enrolled_courses as $en_course)
		{
			$enrolled_course_ids[] = $en_course->course_id;
			
		}
		
		/*echo "<pre>";
		print_r($enrolled_course_ids);*/
		
		$course_array = $this->sales_model->get_course_by_language($this->language,$enrolled_course_ids);
		
		
		
	/*	echo "<pre>";
		print_r($course_array);
		exit;*/
		
		
					
	//	$currency_symbol 				= $currency_det[0]->currency_symbol;		
	//	$data['cur_symbol']    		 = $currency_symbol;	
	//	$content['currency_code'] 	   = $currency_code;
				
		$content['ebook_array']   	     = $ebook_array;
		$content['ebook_offer_options'] = $ebook_offer_options;
		$content['ebook_product_ids']   = $ebook_product_ids;
		$content['ebook_units'] 		 = $ebook_units;
		$content['ebook_price_details'] = $ebook_price_details;
		
		$content['course_array']   	     = $course_array;		
		$content['course_offer_options'] = $course_offer_options;
		$content['course_product_ids']   = $course_product_ids;
		$content['course_units'] 		 = $course_units;
		$content['course_price_details'] = $course_price_details;
		$content['count_course_to_buy']  = count($course_array);
		
		
		$content['colour_wheel_soft_product_id'] = $colour_wheel_soft_id;
		$content['colour_wheel_soft_price'] 	  = $colour_wheel_soft_price;
		
		$content['colour_wheel_hard_product_id']    = $colour_wheel_hard_id;
		$content['colour_wheel_hard_price']		 = $colour_wheel_hard_price;
		
		
		
		$content['cert_product_id']   	    = $cert_product_id;
		$content['cert_fee_deatils']   	   = $cert_fee_deatils;
		$content['postage_details']   	    = $postage_details;
		$content['cert_postage_deatils']   = $cert_postage_deatils;
		
		$content['proof_of_study_soft_id']    = $proof_of_study_soft_id;
		$content['proof_of_study_soft_price'] = $proof_of_study_soft_price;
		
		$content['proof_of_study_hard_id']    = $proof_of_study_hard_id;
		$content['proof_of_study_hard_price'] = $proof_of_study_hard_price;
		
		
		$content['proof_of_completion_soft_id']    = $proof_of_completion_soft_id;
		$content['proof_of_completion_soft_price'] = $proof_of_completion_soft_price;
		
		
		$content['cousre_material_subscription']    = $cousre_material_subscription;
		$content['material_sub_fee_deatils'] = $material_sub_fee_deatils;
		
		
		$content['proof_of_completion_hard_id']    = $proof_of_completion_hard_id;
		$content['proof_of_completion_hard_price'] = $proof_of_completion_hard_price;
		
		$content['e_transcript_soft_id']    = $e_transcript_soft_id;
		$content['e_transcript_soft_price'] = $e_transcript_soft_price;
		
		$content['e_transcript_hard_id']    = $e_transcript_hard_id;
		$content['e_transcript_hard_price'] = $e_transcript_hard_price;
		
		$data['translate'] 	  = $this->tr_common;
		
		
		$currency_det 	= $this->user_model->get_curr_symbol($currency_code);
		
		if($this->session->userdata('cart_session_id'))
		{
			
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('cart_session_id'));
			foreach($cart_main_details as $cart_main)
			{
				$data['cart_count'] = $cart_main->item_count;
				$data['cart_amount'] = $cart_main->total_cart_amount;
				$data['currency_symbol'] = $this->common_model->get_currency_symbol_from_id($cart_main->currency_id);
			}
		}
		else
		{
			$data['cart_count'] = 0;
			$data['cart_amount'] = 0;
			$data['currency_symbol'] = $this->common_model->get_currency_symbol_from_id($this->currId);
			
		}	
		//$data['sales_from'] = 'sales_certificates';	
		
		echo "<pre>";
		print_r($content);
		exit;
		
		$data['view'] = 'sales_certificates';
        $data['content'] = $content;
				
        $this->load->view('user/pop_up_template',$data);
		
		
	
		
		
	
	}
	
	function with_coupen_test($id)
	{
		
		//echo "<pre>";print_r($this->session->userdata['deals']);exit;
		$this->load->model('gift_voucher_model');
		$this->load->model('course_model');
		
		$temp_course = $this->uri->segment(4);
		
		/*if(!isset($this->session->userdata['deals']))
		{
			redirect("deals","refresh");
		}*/
		$vouchercode = 'aazz_1';
		//echo $vouchercode ;
		$voucherDetails = $this->gift_voucher_model->getDetails_of_vcode($vouchercode);
		if($voucherDetails[0]->courses_idcourses==""||$voucherDetails[0]->courses_idcourses==0)
		{
			$content['course_set']=$temp_course;
			$content['course_count']=0;
			$tempArray = $this->user_model->get_student_temp($id);
			$courseId[] =  $tempArray[0]->course_id;
		}
		else
		{
			$courseId = explode(",",$voucherDetails[0]->courses_idcourses);
			echo "<pre>";print_r($courseId);
			$content['course_count']=count($courseId);
			$tempArray = $this->user_model->get_student_temp($id);
			for($c=0;$c<count($courseId);$c++)
			{
				$course_namesArr =$this->user_model->get_coursename($courseId[$c]);
			    $content['course_set'][$courseId[$c]]=$course_namesArr[0]->course_name;
			}
		}
		
						
			$dateNow =date('Y-m-d');
			$langId = $this->course_model->get_lang_course($courseId[0]);
			
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
				$studentdata['reg_date'] = $dateNow;
				$studentdata['lang_id'] = $langId;
				$content['coupon_code'] = $row->coupon_code;
				$content['redemption_code'] = $row->redemption_code;
				
			}
			//$user_id = $this->user_model->add_student($studentdata);
			echo "<pre>";print_r($content);
			echo "<b><br><br>ffoh riva rivalamatha riva riva reevalmatha".var_dump($content['redemption_code']);
			if(isset($content['redemption_code']))
				{		
				echo "<b><br><br><br><br><br>entered";
				}
			
			echo "<br>Add student";
			
			$user_id =28380;
			
			
			/*echo "date noww = ".$dateNow."<br>";
			echo "expirity date = ".$expirityDate."<br>";
			echo "user_idd = ".$user_id;
			exit;*/
			
			if($content['course_count']==0)
			{
			$usersUnit = $this->user_model->get_courseunits_id($courseId[0]);
			foreach($usersUnit as $row1)
			{
				$un[$row1->units_order] = $row1->course_units;
			}
			$student_courseData['student_course_units'] = serialize($un);
			
			$expirityDate = $this->user_model->findExpirityDate($courseId[0],$dateNow,$voucherDetails[0]->idgiftVoucher);
			$student_courseData['course_id'] = $courseId[0];
			$student_courseData['user_id'] = $user_id;
			$student_courseData['date_enrolled'] = $dateNow;
			$student_courseData['date_expiry'] = $expirityDate;
			$student_courseData['enroll_type'] = 'payment';
			$student_courseData['course_status'] = '0';
			
			//$courseEnrId[] = $this->user_model->add_course_student($student_courseData);
			echo "<br> Course count == 0 ---- Add to enrollenmnts";
			
			}
			else
			{
				for($co=0;$co<count($courseId);$co++)
				{
					
					$usersUnit = $this->user_model->get_courseunits_id($courseId[$co]);
					foreach($usersUnit as $row1)
					{
						$un[$row1->units_order] = $row1->course_units;
					}
					$student_courseData['student_course_units'] = serialize($un);
			
					
					$expirityDate = $this->user_model->findExpirityDate($courseId[$co],$dateNow);
					$student_courseData['course_id'] = $courseId[$co];
					$student_courseData['user_id'] = $user_id;
					$student_courseData['date_enrolled'] = $dateNow;
					$student_courseData['date_expiry'] = $expirityDate;
					$student_courseData['enroll_type'] = 'payment';
					$student_courseData['course_status'] = '0';
					//$courseEnrId[] = $this->user_model->add_course_student($student_courseData);
					
					echo "<br> Course count != 0 ---- Add to enrollenmnts";
				}
			}
			//echo $courseEnrId;
			/*for($ce=0;$ce<count($courseEnrId);$ce++)
			{
			$resumeLinkArr['user_id']=$user_id;
			$resumeLinkArr['course_id']=$courseId[$ce];
			$resumeLinkArr['resume_link']='coursemanager/studentcourse/'.$courseId[$ce];
			//$this->user_model->addResumeLink($resumeLinkArr);
			}*/
				//echo "<pre>";print_r($this->session->userdata['deals']['uploaded']);exit;
				$webVoucherId = $this->gift_voucher_model->getVoucherWebIdByVcode($content['coupon_code']);
				
				$couponDetails=array();
				$couponDetails['user_id']=$user_id;
				if($voucherDetails[0]->courses_idcourses==0)
				$couponDetails['course_id']=$courseId[0];
				else
				$couponDetails['course_id']=$voucherDetails[0]->courses_idcourses;
				$couponDetails['coupon_code']=$content['coupon_code'];	
				if(isset($content['redemption_code']))
				{		
				$couponDetails['redemption_code']=$content['redemption_code'];
				$couponDetails['pdf_name']=$this->session->userdata['deals']['uploaded']['upload_data']['file_name'];
				
				}
				$couponDetails['website_id']=$webVoucherId;
				$couponDetails['date']=$dateNow;
			
			//$redeemedCoupenId = $this->user_model->add_redeemedCoupon($couponDetails);
			
			echo "<br> Add to redemption codes";
			
			if(isset($redeemedCoupenId))
			{
				
				$this->load->library('email');
				$this->load->model('email_model');
				
				$row_new = $this->email_model->getTemplateById('new_registration',$langId);
				foreach($row_new as $row1)
				  {
					  
					  $emailSubject = $row1->mail_subject;
					  $mailContent = $row1->mail_content;
				  }
				 	$mailContent = str_replace ( "#firstname#",$studentdata['first_name'], $mailContent );
					$mailContent = str_replace ( "#click here#","<a href='".base_url()."home/studentActivation/".$user_id."'>click here</a>", $mailContent );
					
					$mailContent = str_replace ( "#actlink#","".base_url()."home/studentActivation/".$user_id."", $mailContent  );
					
						$mailContent = str_replace ( "#url#", "<a href='".base_url()."'>Trendimi</a>", $mailContent );
						$mailContent = str_replace ( "#username#", $studentdata['username'], $mailContent );
						$mailContent = str_replace ( "#password#", $this->encrypt->decode($studentdata['password']), $mailContent );
	
				  

				  
				$tomail ='deeputg1992@gmail.com';
					
				
						   	
					  $this->email->from('info@trendimi.com', 'Team Trendimi');
					  $this->email->to($tomail); 
					  $this->email->cc(''); 
					  $this->email->bcc(''); 
					  
					  $this->email->subject($emailSubject);
					  $this->email->message($mailContent);	
					  
					 $this->email->send();
					$this->common_model->deactivate_voucher_code($this->session->userdata['deals']['vCode']);
					$this->session->unset_userdata('deals');
					  
				//redirect('home/couponSuccess/'.$user_id,'refresh');
			}
			
					
		echo "Exit heree";
		exit;
	}
	
	function headerCheck()
	{
		$flag = $this->session->flashdata('flag');
		if($flag)
		{
		redirect('home');
		}
		else
		{
			$this->session->set_flashdata('flag',1);
			echo "here";
		}
		redirect('home');
		header('location:'.base_url().'home');
	}
	
	
	function proof_completion_download($course_id)
	{
		
		  $this->load->helper(array('dompdf', 'file'));
		  $user_id = $this->session->userdata['student_logged_in']['id'];	
		  $user_name = $this->common_model->get_user_name($user_id);	
		  
		  $name = explode('&nbsp;',$user_name);
		
		  $course_name = $this->common_model->get_course_name($course_id); 
		  $course_name =strtolower($course_name);
		   $course_name =ucfirst($course_name);
		  
		  $slNo=0;
		  
		  $course_hours  = $this->user_model->get_course_hours($course_id);
		  
		   $stud_details=$this->user_model->get_stud_details($user_id);
		  
		 $gender_pronoun = '';
		  $gender_pronoun_2 = ''; 
		  
		  if($stud_details[0]->gender == 1)
		  {
			 $gender_pronoun = 'him'; 
			 $gender_pronoun_2 = 'his'; 
			 
		  }
		  else if($stud_details[0]->gender == 2)
		  {
			 $gender_pronoun = 'her';
			 $gender_pronoun_2 = 'her'; 
		  }
		  
		  $certficate_details = $this->user_model->get_proof_of_completion_details_2($user_id,$course_id);
		  if(empty($certficate_details))
			{
				$certficate_details = $this->user_model->get_certficate_details($user_id,$course_id);
			}
			
			echo "<pre>";
			print_r($certficate_details);
			exit;
			  
		  $completed_date_date = $certficate_details['applied_on'];
		  
		  $course_completed_date = explode('-',$completed_date_date);
		  
		  $completed_year  = $course_completed_date[0];
		 // $completed_month = $course_completed_date[1];
		  $completed_date  = $course_completed_date[2];
		  
		   $date_in_time_frmt = strtotime($certficate_details['applied_on']);
		 // $completed_date =2;
		  $month_name  = date('F', $date_in_time_frmt);
		  
		
		  $date_suffix = date("S",strtotime($completed_date_date));
		  
		  $module_list = $this->user_model->check_user_registered($user_id,$course_id);
		  
		  foreach($module_list as $unit)
		  {
			  $modules = unserialize($unit->student_course_units);
		  }
					
		  $module_count = count($modules);
		 
		  
		 /* if($completed_date == 1)
		  {
			  $completed_date = $completed_date.'st';
		  }
		  else if($completed_date == 2)
		  {
			  $completed_date = $completed_date.'nd';
		  }
		  else if($completed_date == 3)
		  {
			  $completed_date = $completed_date.'rd';
		  }
		  else
		  {
			  $completed_date = $completed_date.'th';
		  }*/
		  $course_topics = '';
		  
		  if($course_id == 1)
		  {
			   $course_topics = 'The '.$module_count.' modules on the course included study content, exercises and exams. Topics covered include the importance of good self image, personal care, optimising individual morphology and how to use fashion for best effects.';
		  }
		  elseif($course_id == 2)
		  {
			  $course_topics = 'The '.$module_count.' modules on the course include study content, exercises and exams and prepare the student to become a professional personal shopper/stylist/image consultant. They cover a wide range of topics including career choices, planning, history of fashion & how to use it wisely and career guidance.';
		  }
		  elseif($course_id == 3)
		  {
			  $course_topics = 'The '.$module_count.' modules on the course included study content, exercises and exams. Topics covered include different skin types, professional tools, light and shade tricks and how to enhance features of all shapes.';
		  }
		  elseif($course_id == 4)
		  {
			  $course_topics = 'The '.$module_count.' modules on the course included study content, exercises and exams. Topics covered include different types of ceremony, styling both the venue and wedding party and planning & budgeting.';
		  }
		  elseif($course_id == 5)
		  {
			  $course_topics = 'The '.$module_count.' modules on the course included study content, exercises and exams. Topics covered include hand and foot massage, hand and foot exercises manicure, pedicure, correcting problems, and latest decoration techniques.';
		  }
		  elseif($course_id == 6)
		  {
			  $course_topics = 'The '.$module_count.' modules on the course included study content, exercises and exams. Topics covered include hair and scalp analysis, chemical structure of hair, styling techniques, choosing tools and products and methods of professional care.';
		  }
		  elseif($course_id == 11)
		  {
			  $course_topics = 'Los '.$module_count.' módulos del curso incluyen teoría, ejercicios y examen. Los temas se centran en el estudio sobre la importancia de conseguir una buena imagen de sí mismo, el cuidado personal, la optimización de la morfología individual y en cómo utilizar la moda para conseguir los mejores resultados.';
		  }
		  elseif($course_id == 12)
		  {
			  $course_topics = 'Los '.$module_count.' módulos del curso incluyen teoría, ejercicios y examen. Los temas se centran en las salidas profesionales de una personal shopper, la planificación de una asesoría, la moda, las tendencias, la comunicación y el protocolo. Tutoriales en formato vídeo ayudan a reforzar el contenido del curso y a entenderlo con mayor precisión.';
		  }
		  elseif($course_id == 13)
		  {
			  $course_topics = 'Los '.$module_count.' módulos del curso incluyen teoría, ejercicios y examen. Los temas se centran en un estudio en profundidad de los tipos de piel, la colorimetría, el maquillaje dependiendo de la forma del rostro, además de diferentes técnicas de maquillaje adaptadas a cada ocasión, entre otros. El curso incluye tutoriales en formato vídeo de modelos maquilladas por un profesional que ayudan a reforzar el contenido del curso y a entender las técnicas con mayor precisión.';
		  }
		  elseif($course_id == 14)
		  {
			  $course_topics = 'Los '.$module_count.' módulos del curso incluyen teoría, ejercicios y examen. Los temas se centran en los diferentes tipos de ceremonia, todas las gestiones necesarias para organizar el evento y la preparación del presupuesto.';
		  }
		  elseif($course_id == 15)
		  {
			  $course_topics = 'Los '.$module_count.' módulos del curso incluyen teoría, ejercicios y examen. Los temas se centran en el estudio sobre el masaje de pies y manos, la manicura, la pedicura, la corrección de problemas y las últimas tendencias en maquillaje de uñas.';
		  }
		  elseif($course_id == 16)
		  {
			  $course_topics = 'Los '.$module_count.' módulos del curso incluyen teoría, ejercicios y examen. El curso se focaliza en las técnicas de los peinados de tendencia, el análisis del cuero cabelludo, la estructura química del cabello, y la elección de productos y herramientas, entre otros.';
		  }
		  
		  
		 
		 $lang_id  = $this->common_model->get_user_lang_id($user_id); 
		//  $cssLink = "http://trendimi.net/public/user/css/proof_letters.css";
		
		if($lang_id==4)
		{
		  
		  $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>certificate</title>

</head>

<body>
<style>
	.outer{height:940px; width:720px; margin:0 auto; line-height:20px}
.header{height:26px; text-align:right; padding:74px 20px 20px 20px; background:url(/public/letters/css/logoj.jpg) 485px 15px no-repeat}
.logotxt, .letterNme, .courseNme{font-family: "Great Vibes", cursive;}
.logotxt{font-size:22pt; font-weight:normal; margin:-0.5em 1.8em 0 0; padding:0; color:#df3e8e}
.letterNme{font-size:26pt; font-weight:normal; margin:0; padding:0; color:#a5218c; position:absolute; left:1em; top:-0.7em}
.content{background:#a9dde1 url(/public/letters/css/signature.jpg) 6% 67% no-repeat; padding:4em 2em 2em 2em; border-radius:20px; width:656px; float:left; height:696px; position:relative}
.text{height:500px}
.clear{clear:both}
p{font-size:11pt; color:#555; margin:0; padding:0.5em 0; font-family: "Andada", serif; line-height:1em}
ul{list-style:none; display:block; font-size:12pt; color:#555; margin:0; padding:0; font-family: "Andada", serif; line-height:1.4em}
	
	</style>

<div class="outer">
<div class="header">
<h2 class="logotxt">- online learning -</h2>
</div>
<div class="clear"></div>
<div class="content">
<h2 class="letterNme">Proof of course completion</h2>
<div class="clear"></div>
<div class="text">
<p>To whom it may concern,</p>
<p>We confirm that, on '.$completed_date.' '.$month_name.' '.$completed_year.' , '.$user_name.' successfully completed our '.$course_name.' online learning course, 
this course is '.$course_hours.' online study hours and part of the Trendimi suite of online learning opportunities.</p>
<p>
'.$course_topics.'
</p>
<p>We congratulate '.$name[0].' on completing '.$course_name.' and wish  every success in '.$gender_pronoun_2.' future career.</p>
<p>Kind regards,</p>
</div>
<div class="clear"></div>
<ul>
<li>Francisca Tomàs</li>
<li>Managing Director</li>
<li>Trendimi Ltd</li>
<li>T: UK + 44(0) 20 32904209</li>
<li>T: Ireland +353(0) 21 234 0285</li>
<li>w: www.trendimi.net</li>
<li>e: info@trendimi.com</li>
</ul>
</div>
</div>
</body>
</html>
';
		}
		else
		{
			  
		  $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>certificate</title>

</head>

<body>
<style>
	.outer{height:940px; width:720px; margin:0 auto; line-height:20px}
.header{height:26px; text-align:right; padding:74px 20px 20px 20px; background:url(/public/letters/css/logoj.jpg) 485px 15px no-repeat}
.logotxt, .letterNme, .courseNme{font-family: "Great Vibes", cursive;}
.logotxt{font-size:22pt; font-weight:normal; margin:-0.5em 1.8em 0 0; padding:0; color:#df3e8e}
.letterNme{font-size:26pt; font-weight:normal; margin:0; padding:0; color:#a5218c; position:absolute; left:1em; top:-0.7em}
.content{background:#a9dde1 url(/public/letters/css/signature.jpg) 6% 67% no-repeat; padding:4em 2em 2em 2em; border-radius:20px; width:656px; float:left; height:696px; position:relative}
.text{height:500px}
.clear{clear:both}
p{font-size:11pt; color:#555; margin:0; padding:0.5em 0; font-family: "Andada", serif; line-height:1em}
ul{list-style:none; display:block; font-size:12pt; color:#555; margin:0; padding:0; font-family: "Andada", serif; line-height:1.4em}
	
	</style>

<div class="outer">
<div class="header">
<h2 class="logotxt">- formación online -</h2>
</div>
<div class="clear"></div>
<div class="content">
<h2 class="letterNme">Certificado de finalización de curso</h2>
<div class="clear"></div>
<div class="text">
<p>A quien corresponda,</p>
<p>Confirmamos que el día '.$completed_date.' de '.$month_name.' de '.$completed_year.' , '.$user_name.' completó con éxito el curso online	 de '.$course_hours.' horas '.$course_name.', uno de los curso de formación online ofrecidos por Trendimi.</p>
<p>
'.$course_topics.'
</p>
<p>Felicitamos a '.$name[0].' por haber completado con éxito el curso de '.$course_name.' y le deseamos mucho éxito en su futura carrera.</p>
<p>Un cordial saludo,</p>
</div>
<div class="clear"></div>
<ul>
<li>Francisca Tomàs</li>
<li>Directora General</li>
<li>Trendimi Ltd</li>
<li>T Reino Unido : + 44(0) 20 32904209</li>
<li>T Irlana: +353(0) 21 234 0285</li>
<li>w: www.trendimi.net</li>
<li>e: info@trendimi.com</li>
</ul>
</div>
</div>
</body>
</html>
';
		}
		/*echo $html;
		exit;  */
		
	 $data = pdf_create($html, 'course_completion_'.$user_id.'_'.$course_id);   
     write_file('name', $data);	
		
		  
	  
	}
	
		function generate_access_report()
	{
		
		
		
		//$this->load->view('admin/header');
		$this->load->helper(array('php-excel'));	
	
		
	
	
		
		//$result = $query -> result();
		//echo "<pre>";print_r($result);exit;
			     
       
      			
		
					
					
		$this->db-> select('*');
		$this->db-> from('user_subscriptions');
		$this->db-> where('type','access_6');
		$this->db-> or_where('type','access_12');	     
				
		$query = $this->db->get();
		
		$result=$query->result();
		
		if(!empty($result))
		{
			   $fields = ($field_array[] = array ("Sl No.","User id","Name","Email","Course name","Period","Amount","Source","Date"));
			   $sl_no =1;
				foreach ($result as $row)
				 {				 
				
					$this->db-> select('user_id,first_name,last_name,email');
					$this->db-> from('users');
					$this->db-> where('user_id',$row->user_id);
					$query1 = $this->db->get();
					
					if($query1 -> num_rows() >0 )
					{
						$result1 = $query1 -> result();
						foreach($result1 as $row1)
						{
						$this->db-> select('course_name');
						$this->db-> from('courses');
						$this->db-> where('course_id',$row->course_id);
						$query2 = $this->db->get();
						if($query2 -> num_rows() >0 )
						{
							$result2 = $query2 -> result();
							foreach($result2 as $row2)
						{
							
							$period ='';
							if($row->type == "access_6")
							{
								$period='6 months';
							}
							if($row->type == "access_12")
							{
								$period='12 months';
							}
							
							
							
							
					
						 $name = $row1->first_name.' '.$row1->last_name;
						 
				  
				  $this->db-> select('*');
					$this->db-> from('payments');
					$this->db-> where('user_id',$row->user_id);
					$this->db-> where('product_id','46');// acces products 6 
					
					$query5 = $this->db->get();
					if($query5 -> num_rows() >0 )
					{
						$result5 = $query5 -> result();
						
						/*echo "<pre>";
						print_r($result5);*/
						//exit;
						
						foreach($result5 as $row5)
						{
							$amount1 = $row5->amount;
							$cur_code = $this->common_model->get_currency_code_from_id($row5->currency_id);
							$amount = $amount1.' '.$cur_code;
						}
						
					}
					else
					{
							$this->db-> select('*');
							$this->db-> from('payments');
							$this->db-> where('user_id',$row->user_id);
							$this->db-> where('product_id','47'); // acces products 12 months
							
							$query5 = $this->db->get();
							if($query5 -> num_rows() >0 )
							{
								$result5 = $query5 -> result();
								
								/*echo "<pre>";
								print_r($result5);*/
								//exit;
								
								foreach($result5 as $row5)
								{
									$amount1 = $row5->amount;
									$cur_code = $this->common_model->get_currency_code_from_id($row5->currency_id);
									$amount = $amount1.' '.$cur_code;
								}
								
							}
					}
					
					$source = '';
					
					if($row5->type == 'sales')
					{
						$source = 'POP UP SALES PAGE';	
					}
					else if($row5->type == 'other')
					{
						$source = 'VC';	
					}
					}
				 
				 	}
					}
				 				 
				// $product_details = $this->common_model->get_product_details($row5->product_id);		 					 
					$data_array[] = array($sl_no,$row1->user_id,$name,$row1->email,$row2->course_name,$period,$amount,$source,$row->date_applied);
				  	$sl_no++;	
				 
				 }
		}
				
			   $xls = new Excel_XML;
			   $xls->addArray ($field_array);
			   $xls->addArray ($data_array);
			   $xls->generateXML ( "Acees_report" );
		}
					
else
{
	$this->session->set_flashdata('message',"Empty result!");
redirect('admin/sales/sales_purchase_details');
}

	
	
		
	}
	function redeem_coupons()
	{
		$this->db->select('coupon_code');
		//$this->db->where('redemption_code', '');
		$this->db->where('date >','2013-12-17');
		$this->db->order_by('date');
		$query = $this->db->get('redeemed_coupons');
		$repeted = array();
		$total = array();
		foreach($query->result() as $row)
		{
			if(in_array($row->coupon_code,$total))
			{
				//echo $row->coupon_code.",";
				$repeted[] = $row->coupon_code;
			}
			$total[] = $row->coupon_code;
			
		}
		//echo "<pre>Repeted count = ".count($repeted);
		//echo "<br>Total count = ".count($total);
		
	//print_r($repeted);
	for($i=0;$i<count($repeted);$i++)
	{
		
	}
	}
	function deals_ajax_test($vcode)
	{
		$this->load->model('gift_voucher_model');
		$vArr = $this->gift_voucher_model->isValidforDeals($vcode);
		echo "<pre>";	print_r($vArr);
	}
	function ebook_to_users()
	{
		echo "this is done please make sure to reuse";exit;
		$string = '6682,6706,6740,6748,6789,6820,6903,6949,6961,6978,6998,7254,7340,7399,7641,7685,7816,7817,7972,8065,8141,8466,8547,9452,9818,9910,10471,10615,10726,10873,11057,11333,11478,12618,12768,13356,13370,13488,14969,15242,15905,16699,16936,17322,19441,19606,19858,19862,19967,19971,20007,20234,20378,20442,20444,20615,20747,21013,21071,21161,21171,21599,21619,21637,21668,21678,21763,21772,22017,22046,22126,22143,22157,22318,22320,22481,22510,22517,22556,22564,22597,22657,22903,23062,23070,23164,23186,23188,23191,23235,23284,23352,23390,23401,23425,23471,23517,23522,23529,23530,23551,23608,23611,23639,23725,23736,23765,23808,23836,23877,23892,23966,24007,24118,24155,24263,24303,24318,24393,24448,24460,24468,24472,24488,24515,24525,24553,24557,24628,24634,24635,24660,24695,24707,24751,24776,24779,24783,24785,24792,24793,24827,24844,24854,24884,24897,24957,24960,24974,24982,25000,25022,25032,25065,25150,25164,25165,25187,25237,25257,25265,25290,25308,25358,25365,25372,25408,25455,25503,25547,25559,25570,25628,25684,25701,25771,25791,25800,25805,25831,25877,25889,25902,25907,25923,25926,25941,25983,26003,26013,26040,26093,26105,26135,26156,26163,26215,26231,26238,26285,26348,26361,26387,26391,26394,26405,26421,26427,26440,26460,26467,26476,26493,26511,26560,26599,26608,26664,26697,26708,26709,26763,26832,26884,26912,26922,26931,26974,26999,27009,27035,27061,27095,27129,27137,27185,27202,27260,27261,27288,27317,27329,27439,27451,27475,27505,27564,27610,27668,27782,27877,27903,27949,27976,27988,28001,28029,28040,28051,28094,28153,28157,28194,28263,28296,28303,28323,28343,28354,28389,28822';
		$user_ids = explode(',',$string);
		//echo "<pre>".count($user_ids);
		$nail_have = array();
		$hair_have = array();
		$style_have = array();
		for($i=0;$i<count($user_ids);$i++)
		{
			$this->db-> select('*');
			$this->db-> from('ebooksubscription');
			$this->db-> where('user_id',$user_ids[$i]);
					
			$query = $this -> db -> get();
			  
			  
				if ($query->num_rows >= 1)
				{
					$result= $query -> result();
					foreach($result as $row)
					{
						if(empty($data['ebid']))
						$data['ebid']=explode(',',$row->ebook_id);
						else
						$data['ebid'] = array_merge($data['ebid'],explode(',',$row->ebook_id));
						
					}
				}
				else
				$data['ebid']=array();
				
					$lang_user = $this->user_model->getlangIdByUserId($user_ids[$i]);
					$in_ebook = array();
					if($lang_user==4)
					{
						echo "<br><br><br>--english user--".$user_ids[$i] ;
						if(in_array(10,$data['ebid']))
						{
							$nail_have[] = $user_ids[$i];
							if(in_array(12,$data['ebid']))
							{
								$hair_have[] = $user_ids[$i];
								if(in_array(1,$data['ebid']))
								{
									$style_have[] = $user_ids[$i];
								}
								else
								{
									$in_ebook['product_id'] = 13;
									$in_ebook['ebook_id'] = 1;
									$in_ebook['user_id'] = $user_ids[$i];
									$in_ebook['date_purchased'] = date('Y-m-d');
									$this->db->insert('ebooksubscription',$in_ebook);
									
								}
							}
							else
							{
								$in_ebook['product_id'] = 13;
								$in_ebook['ebook_id'] = 12;
								$in_ebook['user_id'] = $user_ids[$i];
								$in_ebook['date_purchased'] = date('Y-m-d');
								$this->db->insert('ebooksubscription',$in_ebook);
							}
							
						}
						else
						{
							$in_ebook['product_id'] = 13;
							$in_ebook['ebook_id'] = 10;
							$in_ebook['user_id'] = $user_ids[$i];
							$in_ebook['date_purchased'] = date('Y-m-d');
							$this->db->insert('ebooksubscription',$in_ebook);
						}
						
						echo"<br><pre>--- insert array --";print_r($in_ebook);
						
					}
					else
					{
						echo "<br><br><br>-- spanish user--".$user_ids[$i];
						if(in_array(11,$data['ebid']))
						{
							$nail_have[] = $user_ids[$i];
							if(in_array(13,$data['ebid']))
							{
								$hair_have[] = $user_ids[$i];
								if(in_array(2,$data['ebid']))
								{
									$style_have[] = $user_ids[$i];
								}
								else
								{
									$in_ebook['product_id'] = 13;
									$in_ebook['ebook_id'] = 2;
									$in_ebook['user_id'] = $user_ids[$i];
									$in_ebook['date_purchased'] = date('Y-m-d');
									$this->db->insert('ebooksubscription',$in_ebook);
								}
							}
							else
							{
								$in_ebook['product_id'] = 13;
								$in_ebook['ebook_id'] = 13;
								$in_ebook['user_id'] = $user_ids[$i];
								$in_ebook['date_purchased'] = date('Y-m-d');
								$this->db->insert('ebooksubscription',$in_ebook);
							}
							
						}
						else
						{
							$in_ebook['product_id'] = 13;
							$in_ebook['ebook_id'] = 11;
							$in_ebook['user_id'] = $user_ids[$i];
							$in_ebook['date_purchased'] = date('Y-m-d');
							$this->db->insert('ebooksubscription',$in_ebook);
						}
						echo"<br><pre>--- insert array --";print_r($in_ebook);
					}
				
		}
		
		echo "<br>-----------nail have--------------<pre>";print_r($nail_have);
		echo "<br>-----------Hair have--------------<pre>";print_r($hair_have);
		echo "<br>-----------Style have--------------<pre>";print_r($style_have);
	}
	function email_user_check()
	{
		$this->load->model('gift_voucher_model');
		echo $this->gift_voucher_model->email_username_check('deeputg1992@gmail.com','ajith',1);
	}
	
	function user_agent_detect()
	{
		
		$user_agent[0] = 'Mozilla/5.0 (iPad; CPU OS 7_0_4 like Mac OS X) AppleWebKit/537.51.1 (KHTML, like Gecko) Version/7.0 Mobile/11B554a Safar';
		$user_agent[1] = 'Mozilla/5.0 (iPad; CPU OS 7_0_4 like Mac OS X) AppleWebKit/537.51.1 (KHTML, like Gecko) Version/7.0 Mobile/11B554a Safar';
		$user_agent[2] = 'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1700.107 Safari/537.36';
		$user_agent[3] = 'Mozilla/5.0 (Linux; Android 4.2.1; M470BSA Build/JOP40D) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.135 Safar';
		
for($i=0;$i<count($user_agent);$i++)		
{
		
$OSList = array
(
        // Match user agent string with operating systems
        'Windows 3.11' => 'Win16',
        'Windows 95' => '(Windows 95)|(Win95)|(Windows_95)',
        'Windows 98' => '(Windows 98)|(Win98)',
        'Windows 2000' => '(Windows NT 5.0)|(Windows 2000)',
        'Windows XP' => '(Windows NT 5.1)|(Windows XP)',
        'Windows Server 2003' => '(Windows NT 5.2)',
        'Windows Vista' => '(Windows NT 6.0)',
        'Windows 7' => '(Windows NT 7.0)',
        'Windows NT 4.0' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',
        'Windows ME' => 'Windows ME',
        'Open BSD' => 'OpenBSD',
        'Sun OS' => 'SunOS',
        'Linux' => '(Linux)|(X11)',
        'Mac OS' => '(Mac_PowerPC)|(Macintosh)',
        'QNX' => 'QNX',
        'BeOS' => 'BeOS',
        'OS/2' => 'OS/2',
        'Search Bot'=>'(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp)|(MSNBot)|(Ask Jeeves/Teoma)|(ia_archiver)'
);
 
// Loop through the array of user agents and matching operating systems
foreach($OSList as $CurrOS=>$Match)
{
        // Find a match
        if (@eregi($Match, $user_agent[$i]))
        {
                // We found the correct match
                break;
        }
}
// You are using Windows Vista
echo "You are using ".$CurrOS;


$agent = null;

		if ( empty($agent) ) {
			$agent =$user_agent[$i];
	
			if ( stripos($agent, 'Firefox') !== false ) {
				$agent = 'firefox';
			} elseif ( stripos($agent, 'MSIE') !== false ) {
				$agent = 'ie';
			} elseif ( stripos($agent, 'iPad') !== false ) {
				$agent = 'ipad';
			} elseif ( stripos($agent, 'Android') !== false ) {
				$agent = 'android';
			} elseif ( stripos($agent, 'Chrome') !== false ) {
				$agent = 'chrome';
			} elseif ( stripos($agent, 'Safari') !== false ) {
				$agent = 'safari';
			} elseif ( stripos($agent, 'AIR') !== false ) {
				$agent = 'air';
			} elseif ( stripos($agent, 'Fluid') !== false ) {
				$agent = 'fluid';
			}
			echo "<br>your browser : ".$agent;
	
		}
		echo "<br><br><br><br>-----------------------------------------------<br>";
}
	

	}
	
	function after_course_access_pay_test()
	{
		
		// $payment_id = $this->uri->segment(4);
		$this->load->model('certificate_model','',TRUE);
		
		  $course_id  = $this->uri->segment(4);
		  $product_id = $this->uri->segment(5);
		  $user_id=$this->session->userdata['student_logged_in']['id'];
		  
		  echo "<br>Course id ".$course_id;
		  echo "<br>Product id ".$product_id;
		  echo "<br>User id ".$user_id;
		  
		  if($product_id == 46)
		  {
			  $type = 'access_6';
		  }
		  else if($product_id == 47)
		  {
			   $type = 'access_12';
		  }
		  
		  $today = date("Y-m-d");
		  $insert_data=array("user_id"=>$user_id,"course_id"=>$course_id,"type"=>$type,"date_applied"=>$today);
		  
		  echo "<pre>";
		  print_r($insert_data);
		  		 
		//  $this->user_model->insertQuerys("user_subscriptions",$insert_data);
		  
		  
		 $period = $this->certificate_model->get_postal_id($product_id);
		 
		 
		  	 $userCoursesArr=$this->user_model->getcourses_student_expiry($user_id,$course_id);
			 foreach($userCoursesArr as $det)
			 {
			 
				$cur_expiry_date = $det->date_expiry;
			 }
			
			 
			 if($cur_expiry_date > $today)
			 {
				
				 $accessdate=date('Y-m-d', strtotime($cur_expiry_date. ' + '.$period.' months'));
				
			 }
			 else
			 {
				
				  $accessdate=date('Y-m-d', strtotime($today. ' + '.$period.' months'));
				  $accessdate=date("Y-m-d", strtotime("+$period months")); 
				
			 }
										
		  echo "<br>Current acces date ".$cur_expiry_date;		
		 		  
		 $expiry_date = date("Y-m-d", strtotime("+$period days"));
		 
		 echo "<br>Old exipry date ".$expiry_date;
		 
		 $course_status = $this->user_model->get_student_course_status($course_id,$user_id);
		 echo "<br> Course status ".$course_status;
		 
		 if($course_status == 7 || $course_status==6) // if expired or archived change status to completed
		 {
			
			
			 $mark_details = $this->get_student_progress($course_id); 
			 echo "<pre>";
			 print_r($mark_details);
				if($mark_details['progressPercnt']==100)
				{
					//$certificate_status[$k] = 'passed'; 
					$certificate_details =  $this->certificate_model->get_certificate_details($user_id,$course_id);
					if(empty($certificate_details))
					{
						$update_data=array("date_expiry"=>$accessdate,"course_status"=>'2');
					}
					else
					{
						$update_data=array("date_expiry"=>$accessdate,"course_status"=>'4');
					}
				}
				else 
				{
						$update_data=array("date_expiry"=>$accessdate,"course_status"=>'1');
				}
						 
			  	
			
		 }
		 else
		 {
			  $update_data=array("date_expiry"=>$accessdate);
		 }
		// $status = '5';
		
		echo "<pre>";
		print_r($update_data);
    	
	 	// $this->user_model->update_student_enrollments($course_id,$user_id,$update_data);
		  
		  
		  
		//  redirect('coursemanager/certificate');
	
	}
	
	function proof_enrolement_hard_pay()
	{
//		  $payment_id = $this->uri->segment(4);
		  $course_id  = $this->uri->segment(3);
		  $product_id = $this->uri->segment(4);
		  $user_id=$this->session->userdata['student_logged_in']['id'];
		  
		//  echo "<br>Payment id ".$payment_id;
		/*  echo "<br>Course id ".$course_id;
		  echo "<br>Product id ".$product_id;
		  echo "<br>User id ".$user_id;
		  
		  exit;*/
		  
		  $today = date("Y-m-d");
		  $insert_data=array("user_id"=>$user_id,"course_id"=>$course_id,"type"=>'poe_hard',"date_applied"=>$today);		 
		 // $this->user_model->insertQuerys("user_subscriptions",$insert_data);
		  
		  
		  $this->load->helper(array('dompdf', 'file'));
		   $user_details = $this->user_model->get_student_details($user_id); 
	
			 foreach($user_details as $key => $value)
			 {
				$user_name = $value->first_name.' '.$value->last_name;		
			 }		  		   
		  
		  $user_name = strtolower($user_name);
		  $user_name = ucwords($user_name);
		  
		  $name = explode(' ',$user_name);
		
		  
		  $stud_details=$this->user_model->get_stud_details($user_id);
		  
		  $gender_pronoun = '';
		  $gender_pronoun_2 = ''; 
		  
		  if($stud_details[0]->gender == 1)
		  {
			 $gender_pronoun = 'him'; 
			 $gender_pronoun_2 = 'his'; 
			 
		  }
		  else if($stud_details[0]->gender == 2)
		  {
			 $gender_pronoun = 'her';
			 $gender_pronoun_2 = 'her'; 
		  }
		  
		  $course_name = $this->common_model->get_course_name($course_id); 	
		  
		  $course_name = strtolower($course_name);
		  $course_name = ucfirst($course_name);	 	 
		  
		  $course_hours  = $this->user_model->get_course_hours($course_id);		 
		  
		  $course_deatails = $this->user_model->getcourses_student_expiry($user_id,$course_id);
		  
		  foreach($course_deatails as $details)
		  {
			$expiry_date_date = $details->date_expiry;  
		  }
		  
		  $course_expiry_date = explode('-',$expiry_date_date);
		  
		   
		     $user_lang_id  = $this->common_model->get_user_lang_id($user_id);
		  /*------ send mail to admin */
		  
		      if($user_lang_id == 3)
			  {
				   setlocale(LC_TIME, 'es_ES');
			  }
			  else
			  {
				  setlocale(LC_TIME, 'en_EN');
			  }
		   
		  
		  $expiry_year  = $course_expiry_date[0];
		  $expiry_month = $course_expiry_date[1];
		  $expiry_date  = $course_expiry_date[2];
		  
		  
		  $date_in_time_frmt = strtotime($expiry_date_date);
		 // $expiry_date =1;
		  $month_name  = date("F",$date_in_time_frmt);
		  $date_suffix = date("S",strtotime($expiry_date));
		
		  
		  $course_topics = '';
		  
		 if($course_id == 1)
			  {
				  $course_name='Style Me';
				   $course_topics = 'The course content outlines the knowledge, skills and dos & don’ts necessary to become an expert in personal styling. It includes the importance of good self image, personal care, optimising individual morphology and how to use fashion for best effects.';
			  }
			  elseif($course_id == 2)
			  {
				  $course_name='Style You';
				  $course_topics = 'The course content outlines the knowledge, skills and dos & don’ts necessary to become a professional personal shopper/stylist/image consultant. It covers a wide range of topics including career choices, planning, history of fashion & how to use it wisely and career guidance.';
			  }
			  elseif($course_id == 3)
			  {
				  $course_name='Make Up';
				  $course_topics = 'The course content outlines the knowledge, skills and dos & don’ts necessary to become a professional make up artist, including an in-depth understanding of skin types, working with personal colouring, using light and shade and the correct tools. Video clips of live models being made up by a professional make up artist help reinforce course content.';
			  }
			  elseif($course_id == 4)
			  {
				  $course_name='Wedding Planner';
				  $course_topics = 'The course content outlines the knowledge, skills and dos & don’ts necessary to become a professional wedding planner, including décor, styling the wedding party and budgeting.';
			  }
			  elseif($course_id == 5)
			  {
				  $course_name='Nail Artist';
				  $course_topics = 'The course content outlines the knowledge skills, and dos and don’ts necessary to become a professional nail artist, including manicure, pedicure, correcting problems and latest decoration techniques.';
			  }
			  elseif($course_id == 6)
			  {
				  $course_name='Hair Stylist';
				  $course_topics = 'The course content outlines the knowledge skills, and dos and don’ts necessary to become a professional hair stylist, including hair and scalp analysis, chemical structure of hair, choosing products & tools and styling techniques.';
			  }
			  elseif($course_id == 11)
			  {
				  $course_name='Autoimagen';
				  $course_topics = 'Con este curso se adquiere el conocimiento para poder convertirse en un profesional en estilismo personal. El temario se centra en la importancia de conseguir una buena imagen de sí mismo, el cuidado personal, la optimización de la morfología individual y en cómo utilizar la moda para conseguir los mejores resultados.';
			  }
			  elseif($course_id == 12)
			  {
				  $course_name='Personal Shopper';
				  $course_topics = 'Con este curso se adquiere el conocimiento para poder convertirse en un asesor de imagen profesional. El temario se centra en las salidas profesionales de una personal shopper, la planificación de una asesoría, la moda y las tendencias, la comunicación y el protocolo.';
			  }
			  elseif($course_id == 13)
			  {
				  $course_name= 'Maquillaje';
				  $course_topics = 'Con este curso se adquiere el conocimiento para poder convertirse en un profesional en el mundo del maquillaje. El mismo se centra en un estudio en profundidad de los tipos de piel, la colorimetría, el maquillaje dependiendo de la forma del rostro, además de diferentes técnicas de maquillaje adaptadas a cada ocasión. El curso incluye tutoriales en formato vídeo de modelos maquilladas por un profesional que ayudan a reforzar el contenido del curso y a entender las técnicas con mayor precisión.';
			  }
			  elseif($course_id == 14)
			  {
				  $course_name='Wedding Planner';
				  $course_topics = 'Con este curso se adquiere el conocimiento para poder convertirse en un organizador de bodas. El temario incluye los diferentes tipos de ceremonia, todas las gestiones necesarias para organizar el evento y la preparación del presupuesto.';
			  }
			  elseif($course_id == 15)
			  {
				  $course_name='Nail Artist';
				  $course_topics = 'Con este curso se adquiere el conocimiento para poder convertirse en un profesional en estilismo de uñas. El cursose focaliza en el proceso para realizar la manicura, la pedicura, la corrección de problemas e imperfecciones y en las últimas tendencias en maquillaje de uñas. Tutoriales en formato vídeo ayudan a reforzar el contenido del curso y a entenderlo con mayor precisión.';
			  }
			  elseif($course_id == 16)
			  {
				  $course_name='Hair Stylist';
				  $course_topics = '<Con Hair Stylist se adquiere el conocimiento para poder convertirse en un estilista del cabello profesional. Se aprenden las técnicas de los peinados de tendencia, el análisis del cuero cabelludo, la estructura química del cabello, y la elección de productos y herramientas, entre otros.';
			  }
			  
			 // setlocale(LC_TIME, 'en_EN');
			  
			//  $cssLink = "http://trendimi.net/public/letters/css/proof_letters.css";
			  
			  
			  if($user_lang_id == 4)
			  {
				  
			  
			  $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Proof of enrolment</title>
	
	
	</head>
	
	<body>
	<style>
	.outer{height:940px; width:720px; margin:0 auto; line-height:20px}
.header{height:26px; text-align:right; padding:74px 20px 20px 20px; background:url(/public/letters/css/logoj.jpg) 485px 15px no-repeat}
.logotxt, .letterNme, .courseNme{font-family: "Great Vibes", cursive;}
.logotxt{font-size:22pt; font-weight:normal; margin:-0.5em 1.8em 0 0; padding:0; color:#df3e8e}
.letterNme{font-size:32pt; font-weight:normal; margin:0; padding:0; color:#a5218c; position:absolute; left:1em; top:-0.6em}
.content{background:#a9dde1 url(/public/letters/css/signature.jpg) 6% 67% no-repeat; padding:4em 2em 2em 2em; border-radius:20px; width:656px; float:left; height:696px; position:relative}
.text{height:500px}
.clear{clear:both}
p{font-size:11pt; color:#555; margin:0; padding:0.5em 0; font-family: "Andada", serif; line-height:1em}
ul{list-style:none; display:block; font-size:12pt; color:#555; margin:0; padding:0; font-family: "Andada", serif; line-height:1.4em}
	
	</style>
	<div class="outer">
	<div class="header">
	<h2 class="logotxt">- online learning -</h2>
	</div>
	<div class="clear"></div>	
	<div class="content">
	<h2 class="letterNme">Proof of enrolment</h2>
	<div class="clear"></div>
	<div class="text">
	<p>To whom it may concern,</p>
	<p>We confirm that '.$user_name.' has registered with Trendimi online learning institution and has enrolled to study our '.$course_name.' course. </p>
	<p>
	The content, exercises and exams in '.$course_name.' compile to '.$course_hours.' online hours study. '.$name[0].'\'s expected date of 
	completion is '.$expiry_date.' '.$month_name.'  '.$expiry_year.'. This date may be extended if extra time is needed to complete study.
	</p>
	<p>
	'.$course_topics.'
	</p>
	<p>
	We wish '.$name[0].' ever success in completing '.$course_name.' course and in '.$gender_pronoun_2.' future career. 
	</p>
	<p>Kind regards,</p>
	</div>
	<div class="clear"></div>
	<ul>
	<li>Francisca Tomàs</li>
	<li>Managing Director</li>
	<li>Trendimi Ltd</li>
	<li>T: UK + 44(0) 20 32904209</li>
	<li>T: Ireland +353(0) 21 234 0285</li>
	<li>w: www.trendimi.com</li>
	<li>e: info@trendimi.com</li>
	</ul>
	</div>
	</div>
	</body>
	</html>
	';
    }
    else if($user_lang_id == 3)
    {
		 $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Proof of enrolment</title>	
	</head>
	
	<body>
	
	<style>
	.outer{height:940px; width:720px; margin:0 auto; line-height:20px}
.header{height:26px; text-align:right; padding:74px 20px 20px 20px; background:url(/public/letters/css/logoj.jpg) 485px 15px no-repeat}
.logotxt, .letterNme, .courseNme{font-family: "Great Vibes", cursive;}
.logotxt{font-size:22pt; font-weight:normal; margin:-0.5em 1.8em 0 0; padding:0; color:#df3e8e}
.letterNme{font-size:32pt; font-weight:normal; margin:0; padding:0; color:#a5218c; position:absolute; left:1em; top:-0.6em}
.content{background:#a9dde1 url(/public/letters/css/signature.jpg) 6% 67% no-repeat; padding:4em 2em 2em 2em; border-radius:20px; width:656px; float:left; height:696px; position:relative}
.text{height:500px}
.clear{clear:both}
p{font-size:11pt; color:#555; margin:0; padding:0.5em 0; font-family: "Andada", serif; line-height:1em}
ul{list-style:none; display:block; font-size:12pt; color:#555; margin:0; padding:0; font-family: "Andada", serif; line-height:1.4em}
	
	</style>
	
	<div class="outer">
	<div class="header">
	<h2 class="logotxt">- formación online -</h2>
	</div>
	<div class="clear"></div>
	<div class="content">
	<h2 class="letterNme">Certificado de inscripción</h2>
	<div class="clear"></div>
	<div class="text">
	<p>A quien corresponda,</p>
	<p>Confirmamos que '.$user_name.' se ha registrado en la institución de formación online Trendimi y se ha matriculado para estudiar nuestro curso de '.$course_name.'. </p>
	<p>
	El curso está compuesto por una primera parte de teoría, otra de ejercicios y una última de exámenes, y el mismo tiene una duración de
	'.$course_hours.' horas.La fecha prevista para que '.$name[0].' finalice el curso es día el '.$expiry_date.' de '.$month_name.' de '.$expiry_year.'. Esta fecha puede ser extendida si se necesita más tiempo para completar el curso.
	</p>
	<p>'.$course_topics.'</p>
	
	<p>Deseamos a '.$name[0].' mucha suerte para completar el curso '.$course_name.' y para su futura carrera profesional.</p>
	<p>Un cordial saludo,</p>
	</div>
	<div class="clear"></div>
	<ul>
	<li>Francisca Tomàs</li>
	<li>Directora General</li>
	<li>Trendimi Ltd</li>
	<li>T Reino Unido: + 44(0) 20 32904209</li>
	<li>T Irlana: +353(0) 21 234 0285</li>
	<li>w: www.trendimi.com</li>
	<li>e: info@trendimi.com</li>
	</ul>
	</div>
	</div>
	</body>
	</html>
	';
		
		  
    }
		  
		/*echo $html;
		exit;  
		*/
	
		  
		  
		$data = pdf_create($html, 'proof_study_'.$user_id.'_'.$course_id,false);		
	
		//$data = pdf_create($html, '', false);	
		$this->path = "public/certificate/proof_study/proof_study_".$user_id."_".$course_id.".pdf";
		write_file($this->path, $data);
		
		
		$student_data = $this->user_model->get_student_details($user_id);
		
		$user_country_name = $this->user_model->get_country_name($student_data[0]->country_id);
     
		$sendemail = true;
		
		if($sendemail)
		{
			$this->load->library('email');
			$tomail = 'certificates@trendimi.com';			
			
			//$tomail = 'ajithupnp@gmail.com';
					
					  $emailSubject = "Proof of enrollment is attached  ".$student_data[0]->email;;
					  $mailContent = "<p>Please find the attacahment of proof of enrollment here with it.<p>";
					  $mailContent = "<p>User details of Proof of enrollment hard copy applied, <p>";
					  
					  $mailContent .= "<p>User name  : ".$student_data[0]->first_name." ".$student_data[0]->last_name."</p>";
					  $mailContent .= "<p>House Number :  ".$student_data[0]->house_number."</p>";
					  $mailContent .= "<p>Address :  ".$student_data[0]->address."</p>";					  
					  $mailContent .= "<p>City :  ".$student_data[0]->city."</p>";
					  $mailContent .= "<p>Zip code :  ".$student_data[0]->zipcode."</p>";
					  $mailContent .= "<p>Country : ".$user_country_name."</p>";
						   	
					  $this->email->from('info@trendimi.com', 'Team Trendimi');
					  $this->email->to($tomail); 
					  $this->email->attach($this->path);
					  
					  $this->email->subject($emailSubject);
					  $this->email->message($mailContent);	
					  
					  $this->email->send();
					 					 
					 
					 
		}
	
		  
		   $this->email->clear(TRUE);
		  
		  
		  
		  echo "<br>End mail send";
		  
		  
		//  redirect('coursemanager/success_hardcopy', 'refresh');
	
	}
	
	function proof_completion_hard_test_sales()
	{
		
								  		
										/*  certficate proof comletion */
										
										$course_id =4;
										
									  $this->load->helper(array('dompdf', 'file'));
									  $user_id = $this->session->userdata['student_logged_in']['id'];	
									  $user_name = $this->common_model->get_user_name($user_id);
									  
									  $stud_details=$this->user_model->get_stud_details($user_id);		
									  
									  $name = explode('&nbsp;',$user_name);
									
									  $course_name = $this->common_model->get_course_name($course_id); 
									  $slNo=0;
									  
									  $course_hours  = $this->user_model->get_course_hours($course_id);
									  
									  if($stud_details[0]->gender == 1)
									  {
										 $gender_pronoun = 'him'; 
										 $gender_pronoun_2 = 'his'; 
										 
									  }
									  else if($stud_details[0]->gender == 2)
									  {
										 $gender_pronoun = 'her';
										 $gender_pronoun_2 = 'her'; 
									  }
									 
									  
									  $certficate_details = $this->user_model->get_certficate_details($user_id,$course_id);
									  
									   $completed_date_date = $certficate_details['applied_on'];
		  
									  $course_completed_date = explode('-',$completed_date_date);
									  
									  $completed_year  = $course_completed_date[0];
									 // $completed_month = $course_completed_date[1];
									  $completed_date  = $course_completed_date[2];
									  
									   $date_in_time_frmt = strtotime($certficate_details['applied_on']);
									 // $completed_date =2;
									  $month_name  = date('F', $date_in_time_frmt);
									  
									
									  $date_suffix = date("S",strtotime($completed_date_date));
									  
									  $module_list = $this->user_model->check_user_registered($user_id,$course_id);
									  
									  foreach($module_list as $unit)
									  {
										  $modules = unserialize($unit->student_course_units);
									  }
												
									  $module_count = count($modules);
									 
									  
									 /* if($completed_date == 1)
									  {
										  $completed_date = $completed_date.'st';
									  }
									  else if($completed_date == 2)
									  {
										  $completed_date = $completed_date.'nd';
									  }
									  else if($completed_date == 3)
									  {
										  $completed_date = $completed_date.'rd';
									  }
									  else
									  {
										  $completed_date = $completed_date.'th';
									  }*/
									 $course_topics = '';
		  
		  if($course_id == 1)
		  {
			   $course_topics = 'The '.$module_count.' modules on the course included study content, exercises and exams. Topics covered include the importance of good self image, personal care, optimising individual morphology and how to use fashion for best effects.';
		  }
		  elseif($course_id == 2)
		  {
			  $course_topics = 'The '.$module_count.' modules on the course include study content, exercises and exams and prepare the student to become a professional personal shopper/stylist/image consultant. They cover a wide range of topics including career choices, planning, history of fashion & how to use it wisely and career guidance.';
		  }
		  elseif($course_id == 3)
		  {
			  $course_topics = 'The '.$module_count.' modules on the course included study content, exercises and exams. Topics covered include different skin types, professional tools, light and shade tricks and how to enhance features of all shapes.';
		  }
		  elseif($course_id == 4)
		  {
			  $course_topics = 'The '.$module_count.' modules on the course included study content, exercises and exams. Topics covered include different types of ceremony, styling both the venue and wedding party and planning & budgeting.';
		  }
		  elseif($course_id == 5)
		  {
			  $course_topics = 'The '.$module_count.' modules on the course included study content, exercises and exams. Topics covered include hand and foot massage, hand and foot exercises manicure, pedicure, correcting problems, and latest decoration techniques.';
		  }
		  elseif($course_id == 6)
		  {
			  $course_topics = 'The '.$module_count.' modules on the course included study content, exercises and exams. Topics covered include hair and scalp analysis, chemical structure of hair, styling techniques, choosing tools and products and methods of professional care.';
		  }
		  elseif($course_id == 11)
		  {
			  $course_topics = 'Los '.$module_count.' módulos del curso incluyen teoría, ejercicios y examen. Los temas se centran en el estudio sobre la importancia de conseguir una buena imagen de sí mismo, el cuidado personal, la optimización de la morfología individual y en cómo utilizar la moda para conseguir los mejores resultados.';
		  }
		  elseif($course_id == 12)
		  {
			  $course_topics = 'Los '.$module_count.' módulos del curso incluyen teoría, ejercicios y examen. Los temas se centran en las salidas profesionales de una personal shopper, la planificación de una asesoría, la moda, las tendencias, la comunicación y el protocolo. Tutoriales en formato vídeo ayudan a reforzar el contenido del curso y a entenderlo con mayor precisión.';
		  }
		  elseif($course_id == 13)
		  {
			  $course_topics = 'Los '.$module_count.' módulos del curso incluyen teoría, ejercicios y examen. Los temas se centran en un estudio en profundidad de los tipos de piel, la colorimetría, el maquillaje dependiendo de la forma del rostro, además de diferentes técnicas de maquillaje adaptadas a cada ocasión, entre otros. El curso incluye tutoriales en formato vídeo de modelos maquilladas por un profesional que ayudan a reforzar el contenido del curso y a entender las técnicas con mayor precisión.';
		  }
		  elseif($course_id == 14)
		  {
			  $course_topics = 'Los '.$module_count.' módulos del curso incluyen teoría, ejercicios y examen. Los temas se centran en los diferentes tipos de ceremonia, todas las gestiones necesarias para organizar el evento y la preparación del presupuesto.';
		  }
		  elseif($course_id == 15)
		  {
			  $course_topics = 'Los '.$module_count.' módulos del curso incluyen teoría, ejercicios y examen. Los temas se centran en el estudio sobre el masaje de pies y manos, la manicura, la pedicura, la corrección de problemas y las últimas tendencias en maquillaje de uñas.';
		  }
		  elseif($course_id == 16)
		  {
			  $course_topics = 'Los '.$module_count.' módulos del curso incluyen teoría, ejercicios y examen. El curso se focaliza en las técnicas de los peinados de tendencia, el análisis del cuero cabelludo, la estructura química del cabello, y la elección de productos y herramientas, entre otros.';
		  }
		  
		  
		 
		 $lang_id  = $this->common_model->get_user_lang_id($user_id); 
		//  $cssLink = "http://trendimi.net/public/user/css/proof_letters.css";
		
		if($lang_id==4)
		{
		  
		  $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>certificate</title>

</head>

<body>
<style>
	.outer{height:940px; width:720px; margin:0 auto; line-height:20px}
.header{height:26px; text-align:right; padding:74px 20px 20px 20px; background:url(/public/letters/css/logoj.jpg) 485px 15px no-repeat}
.logotxt, .letterNme, .courseNme{font-family: "Great Vibes", cursive;}
.logotxt{font-size:22pt; font-weight:normal; margin:-0.5em 1.8em 0 0; padding:0; color:#df3e8e}
.letterNme{font-size:26pt; font-weight:normal; margin:0; padding:0; color:#a5218c; position:absolute; left:1em; top:-0.7em}
.content{background:#a9dde1 url(/public/letters/css/signature.jpg) 6% 67% no-repeat; padding:4em 2em 2em 2em; border-radius:20px; width:656px; float:left; height:696px; position:relative}
.text{height:500px}
.clear{clear:both}
p{font-size:11pt; color:#555; margin:0; padding:0.5em 0; font-family: "Andada", serif; line-height:1em}
ul{list-style:none; display:block; font-size:12pt; color:#555; margin:0; padding:0; font-family: "Andada", serif; line-height:1.4em}
	
	</style>

<div class="outer">
<div class="header">
<h2 class="logotxt">- online learning -</h2>
</div>
<div class="clear"></div>
<div class="content">
<h2 class="letterNme">Proof of course completion</h2>
<div class="clear"></div>
<div class="text">
<p>To whom it may concern,</p>
<p>We confirm that, on '.$completed_date.' '.$month_name.' '.$completed_year.' , '.$user_name.' successfully completed our '.$course_name.' online learning course, 
this course is '.$course_hours.' online study hours and part of the Trendimi suite of online learning opportunities.</p>
<p>
'.$course_topics.'
</p>
<p>We congratulate '.$name[0].' on completing our '.$course_name.' course and wish '.$gender_pronoun.' every success in '.$gender_pronoun_2.' future career.</p>
<p>Kind regards,</p>
</div>
<div class="clear"></div>
<ul>
<li>Francisca Tomàs</li>
<li>Managing Director</li>
<li>Trendimi Ltd</li>
<li>T: UK + 44(0) 20 32904209</li>
<li>T: Ireland +353(0) 21 234 0285</li>
<li>w: www.trendimi.com</li>
<li>e: info@trendimi.com</li>
</ul>
</div>
</div>
</body>
</html>
';
		}
		else
		{
			  
		  $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>certificate</title>

</head>

<body>
<style>
	.outer{height:940px; width:720px; margin:0 auto; line-height:20px}
.header{height:26px; text-align:right; padding:74px 20px 20px 20px; background:url(/public/letters/css/logoj.jpg) 485px 15px no-repeat}
.logotxt, .letterNme, .courseNme{font-family: "Great Vibes", cursive;}
.logotxt{font-size:22pt; font-weight:normal; margin:-0.5em 1.8em 0 0; padding:0; color:#df3e8e}
.letterNme{font-size:26pt; font-weight:normal; margin:0; padding:0; color:#a5218c; position:absolute; left:1em; top:-0.7em}
.content{background:#a9dde1 url(/public/letters/css/signature.jpg) 6% 67% no-repeat; padding:4em 2em 2em 2em; border-radius:20px; width:656px; float:left; height:696px; position:relative}
.text{height:500px}
.clear{clear:both}
p{font-size:11pt; color:#555; margin:0; padding:0.5em 0; font-family: "Andada", serif; line-height:1em}
ul{list-style:none; display:block; font-size:12pt; color:#555; margin:0; padding:0; font-family: "Andada", serif; line-height:1.4em}
	
	</style>

<div class="outer">
<div class="header">
<h2 class="logotxt">- formación online -</h2>
</div>
<div class="clear"></div>
<div class="content">
<h2 class="letterNme">Certificado de finalización de curso</h2>
<div class="clear"></div>
<div class="text">
<p>A quien corresponda,</p>
<p>Confirmamos que el día '.$completed_date.' de '.$month_name.' de '.$completed_year.' , '.$user_name.' completó con éxito el curso online	 de '.$course_hours.' horas '.$course_name.', uno de los curso de formación online ofrecidos por Trendimi.</p>
<p>
'.$course_topics.'
</p>
<p>Felicitamos a '.$name[0].' por haber completado con éxito el curso de '.$course_name.' y le deseamos mucho éxito en su futura carrera.</p>
<p>Un cordial saludo,</p>
</div>
<div class="clear"></div>
<ul>
<li>Francisca Tomàs</li>
<li>Directora General</li>
<li>Trendimi Ltd</li>
<li>T Reino Unido : + 44(0) 20 32904209</li>
<li>T Irlana: +353(0) 21 234 0285</li>
<li>w: www.trendimi.com</li>
<li>e: info@trendimi.com</li>
</ul>
</div>
</div>
</body>
</html>
';
		}
									echo $html;
									exit;  
									
								/* $data = pdf_create($html, 'proof_completion_'.$user_id.'_'.$course_id);   
								 write_file('name', $data);*/
										
										
								  $data = pdf_create($html, 'proof_completion_'.$user_id.'_'.$course_id,false);		
	
								//$data = pdf_create($html, '', false);	
								$this->path = "public/certificate/proof_completion/proof_completion_".$user_id."_".$course_id.".pdf";
								write_file($this->path, $data);
							 
								
								// end case2 ******************************	
								$sendemail = true;
								
								
								
								if($sendemail)
								{
									  //$to_mail = 'info@trendimi.net';									
									  $to_mail = 'ajithupnp@gmail.com';
									//   $to_mail = 'certificates@trendimi.com';
									  $user_country_name = $this->user_model->get_country_name($student_data[0]->country_id);
									
									  $emailSubject = "Proof of completion Hard copy request : ".$student_data[0]->email;
									  $mailContent = "<p>User details of Proof of completion hard copy applied, <p>";
									  
									  $mailContent .= "<p>User name  : ".$student_data[0]->first_name." ".$student_data[0]->last_name."</p>";
									  $mailContent .= "<p>House Number :  ".$student_data[0]->house_number."</p>";
									  $mailContent .= "<p>Address :  ".$student_data[0]->address."</p>";					  
									  $mailContent .= "<p>City :  ".$student_data[0]->city."</p>";
									  $mailContent .= "<p>Zip code :  ".$student_data[0]->zipcode."</p>";
									  $mailContent .= "<p>Country : ".$user_country_name."</p>";
											
									  $this->email->from('info@trendimi.com', 'Team Trendimi');
									  $this->email->to($to_mail); 
									  $this->email->attach($this->path);
									  
									  $this->email->subject($emailSubject);
									  $this->email->message($mailContent);	
									  
									 $this->email->send();
								}	
									
									
									
		
	}
	
	
	function add_date_test($period)
	{
		$today = date("Y-m-d");
		
		//new DateTime('2000-01-01');
			$today = date(strtotime("2013-08-21"));
			
			//$today =$date->format("Y-m-d");
			
			$userCoursesArr=$this->user_model->getcourses_student_expiry(9089,4);
										 foreach($userCoursesArr as $det)
										 {
										 
											$cur_expiry_date = $det->date_expiry;
										 }
			
			echo "<br>Today ".$cur_expiry_date;
			  $accessdate=date('Y-m-d', strtotime($cur_expiry_date. ' + '.$period.' months'));
			 // $accessdate=date("Y-m-d", strtotime("+$period months")); 
		
		echo "<br>Period ".$period;
		echo "<br>Access date ".$accessdate;
		
	}
	
	
	function test_ip(){
		$this->load->library('ip2country');
		
	 	//$this->load->library('geoip_lib');
		//$ip = $this->input->ip_address();
		$ip[0] = '184.2.126.8';
		$ip[1] = '50.129.253.96';
		$ip[2] ='70.240.39.27';
		$ip[3] = '76.106.103.56';//united kingdom
		//$ip[4] = "181.231.255.255";
		//$ip[5] = "57.73.18.255";
		//$ip[6] = "2.16.5.255";
		//$ip[7] = "192.88.203.255";

		for($i=0;$i<count($ip);$i++)
		{
    	$country_details[] = $this->ip2country->get_country($ip[$i]);
		}
 
		//$country_details = $this->ip2country->get_country();
		echo "<pre>";print_r($country_details);
	}
	
	function check_time(){
		
		$date = date('m/d/Y h:i:s a', time());
		echo $date;
		
	}
	
}
	