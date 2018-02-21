<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class trendiClub extends CI_Controller
{
 	 
	function __construct()
	{

		parent::__construct();
		$this->load->library('session');
		$this->load->library('form_validation');
		$this->load->library('geoip_lib');
		$this->load->library('encrypt');
		
		$this->load->helper(array('form'));
   
		
		$this->load->model('user_model','',TRUE);
		$this->load->model('common_model','',TRUE);
		$this->load->model('offer_model','',TRUE);
		
		
		//echo $this->input->ip_address();
		if($message = $this->session->flashdata('message')){
          $this->flashmessage =$message;
     }
		
		
		$ip = $this->input->ip_address();
    	$this->geoip_lib->InfoIP($ip);
    	$this->code3= $this->geoip_lib->result_country_code3();
     	$this->con_name = $this->geoip_lib->result_country_name();
      
		if(isset($_POST['username'])&&isset($_POST['password']))
		{
			$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
   			$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_check_database');
			$content['username'] = $this->input->post('username');
			if($this->form_validation->run() == TRUE)
			{//Go to private area
				redirect('offers/upgradeCourse', 'refresh');
      }
		}
   
		if(isset($_GET['lang_id'])){
			$newdata = array(
                   'language'  => $_GET['lang_id']
               );
			$this->session->set_userdata($newdata);
			$ref = $this->input->server('HTTP_REFERER', TRUE);
			redirect($ref, 'location'); 
			
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
		 $currDetl =  $this->common_model->get_currency_details($this->currId);
		 $this->currSymbol =$currDetl['currency_symbol'];
		}
	  }
    else {
      $this->currId=1;
    	$this->currencyCode='EUR';
		$this->currSymbol = '&euro;';
		}
		//---------------common translations --------------------------
		
		 $this->tr_common['tr_forget_password'] =$this->user_model->translate_('forget_password');		
		
		 $this->tr_common['tr_stylist_id']      =$this->user_model->translate_('stylist_id');
         $this->tr_common['tr_code']            =$this->user_model->translate_('style_code');
		 $this->tr_common['tr_return_campus']   =$this->user_model->translate_('tr_return_campus');
		 $this->tr_common['tr_sign_out']        =$this->user_model->translate_('tr_sign_out');

		
		$this->tr_common['tr_about_us']   =$this->user_model->translate_('about_us');
		$this->tr_common['tr_faq']        =$this->user_model->translate_('faq');		 
    	$this->tr_common['tr_contact_us'] =$this->user_model->translate_('contact_us');
		 
     	$this->tr_common['tr_change_photo']   =$this->user_model->translate_('change_foto'); 	  	 
		$this->tr_common['tr_edit_details'] = $this->user_model->translate_('edit_your_account_details');
		$this->tr_common['tr_change_password'] = $this->user_model->translate_('change_password');
		$this->tr_common['tr_help_center'] = $this->user_model->translate_('help_center');		
		$this->tr_common['tr_extend_course'] = $this->user_model->translate_('extend_course');
		$this->tr_common['tr_certificate'] = $this->user_model->translate_('certificate');
		$this->tr_common['tr_contact_us'] = $this->user_model->translate_('contact_us');
  	    $this->tr_common['tr_fitting_room'] =$this->user_model->translate_('fitting_room');
        $this->tr_common['tr_terms_use']        =$this->user_model->translate_('terms_use');		 
    	$this->tr_common['tr_privacy_policy'] =$this->user_model->translate_('privacy_policy');


		$this->language = $this->session->userdata('language');
		$this->student_status = $this->session->userdata('student_logged_in');
		$this->course=$this->user_model->get_courses($this->language);
		$this->menu['top_menu']=$this->user_model->get_top_menu($this->language);
    }
	function index()
	{
		
	}
	function tclubEnroll()
	{
		$content = array();
		if($_POST)
		{
			$content['tclubId'] = $this->input->post('tclubId');
			if($content['tclubId'] !="")
			{
				$this->db->select('*');
				$this->db->from('users');
				$this->db->where("user_id",$content['tclubId']);//user_id => tclub_id
				$query = $this->db->get();
				if($query->num_rows==1)
				{
					$row =$query->row();
					redirect('trendiClub/courseList/tclub/'.$content['tclubId']);
				}
				else
				{
				$this->session->set_flashdata('message',"Unfortunately we couldn't find the Trendi Club Id you entered.Please confirm the TrendiClubId and try again.");
				redirect('trendiClub/tclubEnroll','refresh');
				}
			}
			else
			{
				$this->session->set_flashdata('message',"Unfortunately we couldn't find the Trendi Club Id you entered.Please confirm the TrendiClubId and try again.");
				redirect('trendiClub/tclubEnroll','refresh');
			}
		}
		
		$title['pageTitle'] = "Trendi Club Enroll";
		$content['content'] = $title;
		
		$content['translate'] =$this->tr_common;
		$content['tr_welcome'] = $this->user_model->translate_('Welcome to TRENDIMI!');
		
		$content['view'] = 'tclubEnroll';
		
		$this->load->view('user/outerTemplate',$content);
	}
	
	function validateClubId($clubId)//ajax function
	{
		$data['isValid'] = 0;
		
		$this->db->select('*');
		$this->db->from('users');
		$this->db->where("user_id",$clubId);//user_id => tclub_id
		$query = $this->db->get();
		if($query->num_rows==1)
		{
			$row =$query->row();
			$data['isValid'] = 1;
			$data['first_name'] =$row->first_name;
			$data['last_name']  =$row->last_name;
		}
		echo json_encode($data);
	}
	
	function courseList()
	{
		$use_for = $this->uri->segment(3);
		$arg_1   = $this->uri->segment(4);
		
		$content = array();
        $content['base_course'] =$this->course;
        $content['language']    =$this->language;
		$content['use_for']     =$use_for;
		$content['translate']     =$this->tr_common;
		switch($use_for)
		{
			case 'tclub': 
			{
				$content['tclubId']     =$arg_1;
				$title['pageTitle'] = "Trendimi Courses";
				$content['content']     =$title;
				$content['view']     ="courseList";
				$this->load->view('user/outerTemplate',$content);
				break;
			}
		}
		
	}
	
	function courseDetails($clubId,$course_id)
 	{
		$this->load->model("course_model");
		$langId =  $this->course_model->get_lang_course($course_id);
		
 		$content['courseId'] = $course_id;
		$content['clubId'] = $clubId;
 		$content['base_course'] = $this->course;
 		$content['student_status'] = $this->student_status;
 		$content['topmenu'] = $this->menu;
		$content['language'] = $langId;
		$content['style_id'] = $this->user_model->translate_('stylist_id');
		$content['code'] = $this->user_model->translate_('code');
		$content['buy_it'] = $this->user_model->translate_('buy_it_now');
		$content['coursedetail'] = $this->user_model->get_coursedetails($course_id,$content['language']);
		$curr_code = $this->user_model->get_currency_id($this->con_name);

	
	$product = $this->user_model->get_product_id($course_id); 
	 foreach ($product as $value) {
      $product_id = $value->id;
    }
	
	$price_details_array = $this->common_model->getProductFee($product_id,$this->currId);
	
	foreach($price_details_array as $price_det)
	{
		$content['amount']= $price_details_array['amount'];
		$content['currency_symbol']= $price_details_array['currency_symbol'];
		$content['currency_code']=  $price_details_array['currency_code'];
		$content['currency_id']=  $price_details_array['currency_id'];
		
	}

	$top_menu_base_courses = $this->user_model->get_courses($this->language);	
		
		if(empty($top_menu_base_courses))
		{
			$top_menu_base_courses = $this->user_model->get_courses(4);	
		}
	$data['top_menu_base_courses'] 			= $top_menu_base_courses;
	
	$data['translate'] = $this->tr_common;     	
 	$data['view'] = 'course_details_tClub';
    $data['content'] = $content;
    $this->load->view('user/outerTemplate',$data);
 	}
	
	function enroll(){
		$this->load->model('gift_voucher_model');
		
		
		$content['course_set']=$course_id;
		$content['base_course']=$this->course;
		$content['student_status']=$this->student_status;
		$content['topmenu']=$this->menu;
		$content['language']=$this->language;
		$content['style_id']=$this->user_model->translate_('stylist_id');
		$content['code']=$this->user_model->translate_('code');
		$content['buy_it']=$this->user_model->translate_('buy_it_now');
		$content['country']=$this->user_model->get_country();
		$content['course']=$this->user_model->get_course($this->language);
		
		
		
		$this->tr_common['tr_first_name']   =$this->user_model->translate_('First_Name'); 
		$this->tr_common['tr_last_name']   =$this->user_model->translate_('last_Name'); 		
		$this->tr_common['tr_email']   =$this->user_model->translate_('email'); 
		$this->tr_common['tr_confirm_email']   =$this->user_model->translate_('confirm_email'); 
		$this->tr_common['tr_contact_num']   =$this->user_model->translate_('contact_num'); 		
		
			
		$this->tr_common['tr_create_stylist_id']   =utf8_decode($this->user_model->translate_('create_stylist_id')); 
		$this->tr_common['tr_create_secret_code']   =$this->user_model->translate_('create_secret_code'); 		
		$this->tr_common['tr_confirm_secret_code']   =$this->user_model->translate_('confirm_secret_code'); 
		$this->tr_common['tr_gender']   = $this->user_model->translate_('gender'); 
		$this->tr_common['tr_dob']   =$this->user_model->translate_('dob'); 
		$this->tr_common['tr_house_name_no']   =$this->user_model->translate_('house_name_no'); 	
		$this->tr_common['tr_road_street']   =$this->user_model->translate_('road_street'); 	
		$this->tr_common['tr_address_line']   =$this->user_model->translate_('address_line'); 	
		$this->tr_common['tr_city']   =$this->user_model->translate_('city'); 	
		
		$this->tr_common['tr_zip_code']   =utf8_decode($this->user_model->translate_('zip_code')); 
		$this->tr_common['tr_country']   =$this->user_model->translate_('Country'); 	
		$this->tr_common['tr_v_code']   =$this->user_model->translate_('v_code'); 	
		$this->tr_common['tr_voucher_code_note_text']   =$this->user_model->translate_('voucher_code_note_text'); 		
		$this->tr_common['tr_course']   =$this->user_model->translate_('course'); 	
		$this->tr_common['tr_terms_agree']   =$this->user_model->translate_('terms_agree'); 	
	    $this->tr_common['tr_next']   =$this->user_model->translate_('_next'); 
		
		
		$this->tr_common['tr_first_name_required']  =$this->user_model->translate_('first_name_required'); 		
		$this->tr_common['tr_last_name_required']   =$this->user_model->translate_('last_name_required');		
		$this->tr_common['tr_user_name_required']   =$this->user_model->translate_('user_name_required'); 		
		$this->tr_common['tr_email_required']   	   =$this->user_model->translate_('email_required'); 
		$this->tr_common['tr_required']   	   		 =$this->user_model->translate_('required'); 
		
		
			
		
		
		

		if(isset($_POST['fname']))
		{
		  $studentdata  = array();
		  $studentdata['first_name'] = $content['fname'] = $this->input->post('fname');
		  $studentdata['last_name'] = $content['lname'] = $this->input->post('lname');
		  $studentdata['email'] = $content['email'] = $this->input->post('email');
		//  $studentdata['mobile'] = $content['mobile'] = $this->input->post('mobile');
		  $studentdata['username'] = $content['user_name'] = $this->input->post('user_name');
		  //$content['pword'] = $this->input->post('pword');
		  $studentdata['password']= $this->encrypt->encode($this->input->post('pword'));
		  $studentdata['gender'] = $content['gender'] = $this->input->post('gender');
		  $studentdata['contact_number'] = $content['contact_no'] = $this->input->post('contact_no');
		  $studentdata['house_number'] = $content['house_no'] = $this->input->post('house_no');		 
		  $studentdata['address'] = $content['address'] = $this->input->post('address');
          $studentdata['street'] = $content['street'] = $this->input->post('street');
		  $studentdata['zipcode'] = $content['zip_code'] = $this->input->post('zip_code');
          $studentdata['city'] = $content['city'] = $this->input->post('city');
		  $studentdata['country_id'] = $content['country_set'] = $this->input->post('country_id');
		  $studentdata['course_id'] = $content['course_set'] = $this->input->post('course_id');
		  
		 if($this->input->post('voucher_code')=='')
		 {
			 $studentdata['with_coupon']='no';		 
		 }
		 else
		 {
		 	$studentdata['with_coupon']='yes';
			$studentdata['coupon_code'] = $content['voucher_code'] = $this->input->post('voucher_code');
		 }
		 
		 
		//  $studentdata['comments'] = $content['comments'] = $this->input->post('comments');
		  $content['terms'] = $this->input->post('terms');
		  $studentdata['reg_date']=date("Y-m-d");
		  /*$coursename=$this->user_model->get_coursename($content['course_set']);
		  foreach ($coursename as $key) {
			$studentdata['course_validity']=$key->course_validity_id;
		  }*/
		  //$studentdata['active']=0;
		  
		  //$studentdata['user_type_iduser_type']=1;
		  
		  $this->form_validation->set_rules('fname', 'First name', 'trim|required');
		  $this->form_validation->set_rules('lname', 'Last Name', 'required');
		  $this->form_validation->set_rules('gender', 'Gender', 'required');
		  $this->form_validation->set_rules('country_id', 'Country', 'callback_validate[Country]');
		  $this->form_validation->set_rules('user_name', 'UserName', 'required');
		  $this->form_validation->set_rules('pword', 'Password', 'required|min_length[6]|callback_chkpword[Password]');
		  $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email|callback_chkemail[E-mail]');
		  $this->form_validation->set_rules('course_id', 'Course', 'callback_validate[Course]');
		  $this->form_validation->set_rules('house_no', 'House No', 'required');
		  $this->form_validation->set_rules('street', 'Street', 'required');
		  $this->form_validation->set_rules('city', 'City', 'required');
		  $this->form_validation->set_rules('zip_code', 'Zip-Code', 'required');
		  $this->form_validation->set_rules('contact_no', 'Contact-No', 'required');
		  $this->form_validation->set_rules('terms', 'Acceptance of Terms', 'required');
		  //$this->form_validation->set_rules('voucher_code', 'VoucherCode', 'callback_checkVcode');
		  
		  
		 
		  if($this->form_validation->run())
		  {
			  if($studentdata['with_coupon']=='yes')
			{
				$content['coupenCode'] = $this->input->post('voucher_code');
				//echo $content['coupenCode']."  courseId = ".$content['course_set'];
				$validCode = $this->gift_voucher_model->isValid($content['coupenCode'],$content['course_set']);
				//echo "<pre>";print_r($validCode);exit;
				if($validCode['code_exist']!=1)
				{
					$this->session->set_flashdata('message','Entered Voucher code doesnot exist.');
				}
				/*else if($validCode['security_req']=='yes')
				{
					
				}*/
				else
				{
					$this->user_model->add_student_temp($studentdata);
					$student_id=$this->db->insert_id();
					$redirectPath = '/home/withCoupon/'.$student_id;
					redirect($redirectPath, 'refresh');
				}
			}
			else
			{
			$this->user_model->add_student_temp($studentdata);
			$student_id=$this->db->insert_id();	
			$redirectPath = '/home/paymentDetails/stud_id/'.$student_id.'/cour_id/'.$studentdata['course_id'];
			redirect($redirectPath, 'refresh');
			
		  }
		}


	}
	//$langId = $this->language;	
	$top_menu_base_courses = $this->user_model->get_courses($this->language);	
		
		if(empty($top_menu_base_courses))
		{
			$top_menu_base_courses = $this->user_model->get_courses(4);	
		}
	
	
	$data['top_menu_base_courses'] 			= $top_menu_base_courses;
	$data['translate'] = $this->tr_common;
	
	$data['view'] = 'enrolldetail';
	$data['content'] = $content;
	$this->load->view('user/outerTemplate',$data);
	}
	
	function paymentDetails($temp_id,$course_id)
 	{
		$content['cour_id'] = $course_id;
		$content['stud_id'] = $temp_id;
 		//$content = $this->uri->uri_to_assoc(3);
		
    $coursename=$this->user_model->get_coursename($content['cour_id']);
    
    foreach ($coursename as $key) {
    $content['course_name']=$key->course_name ;
    $content['val_days']=$key->course_validity;
    }
   
    $content['language']=$this->language;
   	$curr_code=$this->user_model->get_currency_id($this->con_name);
		
	$curr_id= $this->currId;	
	
	$product = $this->user_model->get_product_id($content['cour_id']); 
	 foreach ($product as $value) {
      $product_id = $value->id;
    }
	
	$price_details_array = $this->common_model->getProductFee($product_id,$this->currId);
		
	foreach($price_details_array as $price_det)
	{
		$content['course_fee']= $price_details_array['amount'];
		$content['currency_symbol']= $price_details_array['currency_symbol'];
		$content['currency_code']=  $price_details_array['currency_code'];
		$content['curr_id']=  $price_details_array['currency_id'];
		
	}
	
	$content['product_id']=$product_id;
		
	$data['translate'] = $this->tr_common;
    $data['view'] = 'paymentdetails';
    $data['content'] = $content;
    $this->load->view('user/template',$data);

  
 
 	}
	
}