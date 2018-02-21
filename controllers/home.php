<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class home extends CI_Controller
{
 	 
	function __construct()
	{

		parent::__construct();
		$this->load->library('session');
		$this->load->library('encrypt');
		$this->load->library('user_agent');
		$this->load->helper(array('form'));
		$this->load->helper('text');
    //$this->load->helper(array('language'));
		$this->load->library('form_validation');
		$this->load->model('user_model','',TRUE);
		$this->load->model('common_model','',TRUE);
		$this->load->model('campaign_model','',TRUE);
		$this->load->model('certificate_model','',TRUE);
		$this->load->model('gift_voucher_model','',TRUE);
		$this->load->model('sales_model','',TRUE);
		$this->load->model('ebook_model','',TRUE);
		$this->load->model('package_model','',TRUE);
		$this->load->model('discount_code_model','',TRUE);
          $this->load->model('course_model','',TRUE);
          //$this->load->model('control_panel/manage_admin_model','',TRUE);

		
		
		//echo $this->input->ip_address();
		if($message = $this->session->flashdata('message')){
          $this->flashmessage =$message;
     }
		
		//$this->load->library('geoip_lib');
		$ip = $this->input->ip_address();
    	//$this->geoip_lib->InfoIP($ip);
    	//$this->code3= $this->geoip_lib->result_country_code3();
     	//$this->con_name = $this->geoip_lib->result_country_name();
		$this->load->library('ip2country_lib');
		$this->con_name = $this->ip2country_lib->getInfo();
		//$this->con_name = 'UK';
      
		if(isset($_POST['username'])&&isset($_POST['password']))
		{
			$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
   			$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_check_database');
			$content['username'] = $this->input->post('username');
			if($this->form_validation->run() == TRUE)
			{//Go to private area
				redirect('coursemanager/campus', 'refresh');
     		 }else
			 {
				 $this->session->set_flashdata('loagin_failed',"Invalid username and password");
				 redirect('home', 'refresh');
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
		
	
		//$objCourseman = new coursemanager();
//      $this->coursemanager->get_student_deatils_for_popup();    
		
		//---------------common translations --------------------------
		 $this->tr_common['tr_forget_password'] =$this->user_model->translate_('forget_password');		
		$this->tr_common['tr_eventrix']   =$this->user_model->translate_('eventrix');
		 $this->tr_common['tr_user_name']      =$this->user_model->translate_('user_name');
         $this->tr_common['tr_password']            =$this->user_model->translate_('password');
		 $this->tr_common['tr_return_to']   =$this->user_model->translate_('return_to');
		 $this->tr_common['tr_campus']   =$this->user_model->translate_('campus');
		 $this->tr_common['tr_sign']   =$this->user_model->translate_('sign');
		 $this->tr_common['tr_Out']   =$this->user_model->translate_('Out');
		 $this->tr_common['tr_my_courses']   =$this->user_model->translate_('my_courses');
		 $this->tr_common['tr_my_ebooks']   =$this->user_model->translate_('my_ebooks');

		$this->tr_common['tr_why_us']   =$this->user_model->translate_('why_us');
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
		
//		$top_menu_base_courses = $this->user_model->get_courses($this->language);


		$this->language = $this->session->userdata('language');
		$this->student_status = $this->session->userdata('student_logged_in');
		$this->course=$this->user_model->get_courses_order($this->language);
		if(empty($this->course))
		{
			$this->course=$this->user_model->get_courses_order(4); // get english courses
		}
		
		$this->menu['top_menu']=$this->user_model->get_top_menu($this->language);
    }
 	public function index()
 	{
		

          $content = array();
        $content['base_course']=$this->user_model->get_popular_courses($this->language);
        $content['language']=$this->language;
        $content['student_status']=$this->student_status;
        $content['topmenu']=$this->menu;
		$content['tr_head_description']=$this->user_model->translate_('head_description');
		$content['tr_user_name']=$this->user_model->translate_('user_name');
        $content['tr_password']=$this->user_model->translate_('password');
		
	    $content['metaKeys'] = "wedding, event, planning, course, online";
	    $content['metaDesc'] = "wedding and event planning courses brought to you by Eventrix.";
		
        $data['translate'] = $this->tr_common;
		
		//echo "<pre>";print_r($content);exit;
        $data['view'] = 'home';
        $data['content'] = $content;
        $this->load->view('user/template_outer',$data);
 	}
 	function check_database($password)
 	{
   					//Field validation succeeded.  Validate against database
   					$username = $this->input->post('username');

   					//query the database
   					$result = $this->user_model->login($username, $password);
					
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
							
							/*$login_detail['last_login'] =  date('Y-m-d H:i:s');
							
						
							
							$this->db->where('user_id',$row->user_id);
							$this->db->update('users',$login_detail);*/
							
							
							
							
							
                  return TRUE;
                }
     					}
     					
   					}
					else
   					{
						
     					$this->form_validation->set_message('check_database','Invalid username or password');
    					return false;
   					}
				
				
	}
	function logout(){
		$this->session->unset_userdata('student_logged_in');
		$this->session->unset_userdata('cart_session_id');
		redirect('/home');
	}
	
	
	function get_student_deatils_for_popup()
	{
	  $this->tr_common['tr_first_name']   =$this->user_model->translate_('First_Name');	      
	  $this->tr_common['tr_email'] =$this->user_model->translate_('email');
	  $this->tr_common['tr_telephone'] =$this->user_model->translate_('Telephone');
      $this->tr_common['tr_country'] =$this->user_model->translate_('country');
	  $this->tr_common['tr_dob'] =$this->user_model->translate_('dob');
	   $this->tr_common['tr_account_details']   =$this->user_model->translate_('account_details');
	   $this->tr_common['tr_save'] =$this->user_model->translate_('save');
	   
	  $this->tr_common['tr_upload_photo'] =$this->user_model->translate_('upload_photo');
	  $this->tr_common['tr_upload'] =$this->user_model->translate_('upload');
 	  $this->tr_common['tr_select_file'] =$this->user_model->translate_('select_file');


	   $this->tr_common['tr_enter_cur_password'] =$this->user_model->translate_('enter_cur_password');
	   $this->tr_common['tr_enter_new_password'] =$this->user_model->translate_('enter_new_password');
 
		 
		
		
		if(!$this->session->userdata('student_logged_in')){
		  redirect('home');
		}
		$stud_id=$this->session->userdata['student_logged_in']['id'];
		
		 $content['country']=$this->user_model->get_country();
		 $stud_details=$this->user_model->get_stud_details($stud_id);
		 foreach($stud_details as $row){
		  $content['fname'] =$row->first_name;
		  if(($row->dob)!=''){
			$content['dob'] = explode('-',$row->dob);
		  }
		  else $content['dob']=NULL;
		   $content['email'] = $row->email;
		  $content['contact_no'] = $row->contact_number;
		  $content['country_set'] = $row->country_id ;
		}		
		return $content;
	
	}
	
	
	
	
 	function coursedetails($course_id)
 	{
		$this->load->model("course_model");
 		//$content['coursedetail']=$this->user_model->get_coursedetails($course_id);
		
//		$stud_id=$this->session->userdata['student_logged_in']['id'];
		//$langId = $this->language;
		
		$langId =  $this->course_model->get_lang_course($course_id);
		
 		$content['courseId'] = $course_id;
 		$content['base_course'] = $this->course;
 		$content['student_status'] = $this->student_status;
 		$content['topmenu'] = $this->menu;
		$content['language'] = $langId;
		$content['style_id'] = $this->user_model->translate_('stylist_id');
		$content['code'] = $this->user_model->translate_('code');
		$content['buy_it'] = $this->user_model->translate_('buy_it_now');
		$content['coursedetail'] = $this->user_model->get_coursedetails($course_id,$content['language']);
		$curr_code = $this->user_model->get_currency_id($this->con_name);
		
	//$langId = $this->language;	
	
	$product = $this->user_model->get_product_id($course_id);
	
	 foreach ($product as $value) {
      $product_id = $value->id;
    }
	
	$price_details_array = $this->common_model->getProductFee($product_id,$this->currId);
	
/*echo "<pre>";
	print_r($price_details_array);
	exit;*/
	
	/*[fake_amount] => 0
    [amount] => 309
    [currency_symbol] => £
    [currency_code] => GBP
    [currency_id] => 2*/
	
	foreach($price_details_array as $price_det)
	{
		$content['amount']= $price_details_array['amount'];
		$content['currency_symbol']= $price_details_array['currency_symbol'];
		$content['currency_code']=  $price_details_array['currency_code'];
		$content['currency_id']=  $price_details_array['currency_id'];
		
	}

   /* if($curr_code!==1) {
      foreach ($curr_code as $value) {
        $curr_id= $value->currency_idcurrency;
        $currency_code=$value->currencyCode;
      }
    }
    else {
      $curr_id=1;
    	$currency_code='EUR';
		}
		
	
	
    $course_fee=$this->user_model->get_course_fee($curr_id,$product_id);
    if($course_fee==''){
      $curr_id=1;
      $currency_code='EUR';
      $course_fee=$this->user_model->get_course_fee($curr_id,$product_id);
    }

	
    foreach ($course_fee as $value) {
      $content['amount']= $value->amount;
    }
    $currency_symbol=$this->user_model->get_curr_symbol($currency_code);
    foreach ($currency_symbol as $value) {
      $content['currency_symbol']= $value->currency_symbol;
    }*/
	$top_menu_base_courses = $this->user_model->get_courses($this->language);	
		
		if(empty($top_menu_base_courses))
		{
			$top_menu_base_courses = $this->user_model->get_courses(4);	
		}
	$data['top_menu_base_courses'] 			= $top_menu_base_courses;
	
	$content['pageTitle'] = $content['coursedetail'][0]->page_title;
	$content['metaKeys'] = $content['coursedetail'][0]->meta_key;
	$content['metaDesc'] = $content['coursedetail'][0]->meta_desc;
	
	$data['translate'] = $this->tr_common;     	
 	$data['view'] = 'coursedetail';
    $data['content'] = $content;
    $this->load->view('user/outerTemplate',$data);
 	}
	
	function enroll($course_id)
	{
		
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
		$content['states']=$this->user_model->get_states();
		$content['reason_to_buy']=$this->user_model->get_reason_buy($this->language);
		
		$content['course']=$this->user_model->get_course($this->language);
		
		
		
		
		$this->tr_common['tr_first_name']   =$this->user_model->translate_('First_Name'); 
		$this->tr_common['tr_last_name']   =$this->user_model->translate_('last_Name'); 		
		$this->tr_common['tr_email']   =$this->user_model->translate_('Email'); 
		$this->tr_common['tr_confirm_email']   =$this->user_model->translate_('confirm_email'); 
		$this->tr_common['tr_telephone'] =$this->user_model->translate_('Telephone');
		$this->tr_common['tr_contact_num']   =$this->user_model->translate_('contact_num'); 		
		$this->tr_common['tr_mobile']   =$this->user_model->translate_('mobile'); 
		$this->tr_common['tr_male']   =$this->user_model->translate_('male'); 		
		$this->tr_common['tr_female']   =$this->user_model->translate_('female'); 
		$this->tr_common['tr_course']   =$this->user_model->translate_('course'); 
		$this->tr_common['tr_comment']   =$this->user_model->translate_('comment'); 
		
			
		$this->tr_common['tr_create_stylist_id']   =utf8_decode($this->user_model->translate_('create_stylist_id')); 
		$this->tr_common['tr_create_secret_code']   =$this->user_model->translate_('create_secret_code'); 		
		$this->tr_common['tr_confirm_secret_code']   =$this->user_model->translate_('confirm_secret_code'); 
		$this->tr_common['tr_gender']   = $this->user_model->translate_('gender'); 
		$this->tr_common['tr_dob']   =$this->user_model->translate_('dob'); 
		$this->tr_common['tr_house_name_no']   =$this->user_model->translate_('house_name_no'); 	
		$this->tr_common['tr_road_street']   =$this->user_model->translate_('road_street'); 	
		$this->tr_common['tr_address']   =$this->user_model->translate_('address_line'); 	
		$this->tr_common['tr_city']   =$this->user_model->translate_('city'); 	
		
		$this->tr_common['tr_zip_code']   =utf8_decode($this->user_model->translate_('zip_code')); 
		$this->tr_common['tr_country']   =$this->user_model->translate_('Country'); 	
		$this->tr_common['tr_v_code']   =$this->user_model->translate_('v_code'); 	
		$this->tr_common['tr_voucher_code_note_text']   =$this->user_model->translate_('voucher_code_note_text'); 		
		$this->tr_common['tr_course']   =$this->user_model->translate_('course'); 	
		$this->tr_common['tr_terms_agree']   =$this->user_model->translate_('terms_agree'); 	
	    $this->tr_common['tr_next']   =$this->user_model->translate_('_next'); 
		 $this->tr_common['tr_SIGN_UP']   =$this->user_model->translate_('SIGN_UP'); 
		$this->tr_common['tr_reason_to_buy']   =$this->user_model->translate_('reason_to_buy'); 
		$this->tr_common['tr_email_mismatch']   =$this->user_model->translate_('email_mismatch'); 
		$this->tr_common['tr_confirm_password']   =$this->user_model->translate_('confirm_secret_code'); 
		$this->tr_common['tr_user_already_exist']   =$this->user_model->translate_('user_exists'); 
		
		$this->tr_common['tr_valid_email_required']   	   =$this->user_model->translate_('valid_email_required');
		
		
		
		
		
		/*$this->tr_common['tr_first_name_required']  =$this->user_model->translate_('first_name_required'); 		
		$this->tr_common['tr_last_name_required']   =$this->user_model->translate_('last_name_required');		
		$this->tr_common['tr_user_name_required']   =$this->user_model->translate_('user_name_required'); 		
		$this->tr_common['tr_email_required']   	   =$this->user_model->translate_('email_required'); 
		$this->tr_common['tr_required']   	   		 =$this->user_model->translate_('required'); */
		
		$this->tr_common['tr_personal_details']   =$this->user_model->translate_('personal_details'); 		
		$this->tr_common['tr_payment_details']   =$this->user_model->translate_('payment_details'); 	
		$this->tr_common['tr_first_name_required']  =$this->user_model->translate_('first_name_required'); 		
		$this->tr_common['tr_last_name_required']   =$this->user_model->translate_('last_name_required');		
		$this->tr_common['tr_user_name_required']   =$this->user_model->translate_('user_name_required'); 		
		$this->tr_common['tr_email_required']   	   =$this->user_model->translate_('email_required'); 
		$this->tr_common['tr_password_required']   	   =$this->user_model->translate_('password_required'); 
		$this->tr_common['tr_weak_password']   	   =$this->user_model->translate_('weak_password'); 
		//$this->tr_common['tr_confirm_password']   	   =$this->user_model->translate_('confirm_password'); 
		$this->tr_common['tr_password_mismatch']   	   =$this->user_model->translate_('password_mismatch'); 
		$this->tr_common['tr_email_exists']   	   =$this->user_model->translate_('email_exists');
		//$this->tr_common['tr_confirm_email']   	   =$this->user_model->translate_('confirm_email');
		$this->tr_common['tr_required']   	   		 =$this->user_model->translate_('required');  
		
		
		$coursename=$this->user_model->get_coursename($course_id);
    
		foreach ($coursename as $key) {
		$content['course_name']=$key->course_name ;
		$content['val_days']=$key->course_validity;
		}
		
		
		$enrolled_course_ids = array();
	  $course_array = array();
	  $coure_list_html = '';
		if(isset($this->session->userdata['student_logged_in']['id']))		
		{	
			$user_id = $this->session->userdata['student_logged_in']['id'];
			$enrolled_courses = $this->user_model->get_courses_student($user_id);
			
			foreach($enrolled_courses as $en_course)
			{
				$enrolled_course_ids[] = $en_course->course_id;			
			}		
			$content['user_user_name'] = 'Hi, '.$this->common_model->get_user_name($user_id);			
		}
		//echo "<pre>";print_r($enrolled_course_ids);exit;
		$content['course_array'] = $this->sales_model->get_course_by_language($this->language,$enrolled_course_ids,'all');
      if(!isset($this->session->userdata['student_logged_in']['id']))	
		{
		if(isset($_POST['fname']))
		{
			
		  
		  $studentdata  = array();
		  $studentdata['first_name'] = $content['fname'] = ucfirst(strtolower($this->input->post('fname')));
		  $studentdata['last_name'] = $content['lname'] = ucfirst(strtolower($this->input->post('lname')));
		  $studentdata['email'] = $content['email'] = $this->input->post('email');
		/*  $studentdata['contact_number'] = $content['mobile'] = $this->input->post('mobile');
		  $studentdata['username'] = $content['user_name'] = $this->input->post('user_name');
		  //$content['pword'] = $this->input->post('pword');
		  $studentdata['password']= $this->encrypt->encode($this->input->post('pword'));
		  $studentdata['gender'] = $content['gender'] = $this->input->post('gender');
		  $studentdata['dob'] = $content['dob_check'] = $this->input->post('dob_check');
		  $studentdata['house_number'] = $content['house_no'] = $this->input->post('house_no');		 
		  $studentdata['address'] = $content['address'] = $this->input->post('address');
          $studentdata['street'] = $content['street'] = $this->input->post('street');
		  $studentdata['zipcode'] = $content['zip_code'] = $this->input->post('zip_code');
          $studentdata['city'] = $content['city'] = $this->input->post('city');
		  $studentdata['country_id'] = $content['country_set'] = $this->input->post('country_id');	  
		  if($studentdata['country_id']=='12'){
		  $us_states= $content['state_set'] = $this->input->post('state');
		  }
		  if(isset($us_states)&& $us_states!=''){
			 
			 $state_details =$this->user_model->get_statename($us_states);
			  foreach($state_details as $row_states){
				 $studentdata['us_states']=$row_states->name_short;   
			  }
			 
		  }	
		 if($this->input->post('reason_id')!='')
		 {
		   $studentdata['reason_id'] = $content['reason_to_buy_set'] = implode(",",$this->input->post('reason_id'));   
		 }
		  */
		/*  echo "<pre>";
		  print_r($studentdata);
		  exit;
		  */
		 $user_course_id = $content['course_set'] = $this->input->post('non_user_course_id');
		  $studentdata['course_id'] = $content['course_set'] = $user_course_id[0];
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
		 // $content['terms'] = $this->input->post('terms');
		  $studentdata['reg_date']=date("Y-m-d");
		  /*$coursename=$this->user_model->get_coursename($content['course_set']);
		  foreach ($coursename as $key) {
			$studentdata['course_validity']=$key->course_validity_id;
		  }*/
		  //$studentdata['active']=0;
		  
		  //$studentdata['user_type_iduser_type']=1;
		  
		  $this->form_validation->set_rules('fname', 'First name', 'trim|required');
		  $this->form_validation->set_rules('lname', 'Last Name', 'required');
		 $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email|callback_chkemail[E-mail]');
		  //$this->form_validation->set_rules('course_id', 'Course', 'callback_validate[Course]');
		  //$this->form_validation->set_rules('terms', 'Acceptance of Terms', 'required');
	/*	  
		  $this->form_validation->set_rules('gender', 'Gender', 'required');
		  $this->form_validation->set_rules('country_id', 'Country', 'callback_validate[Country]');
		  $this->form_validation->set_rules('user_name', 'UserName', 'required');
		  $this->form_validation->set_rules('pword', 'Password', 'required|min_length[6]|callback_chkpword[Password]');
		  $this->form_validation->set_rules('house_no', 'House No', 'required');
		  $this->form_validation->set_rules('street', 'Street', 'required');
		  $this->form_validation->set_rules('city', 'City', 'required');
		  $this->form_validation->set_rules('zip_code', 'Zip-Code', 'required');
		  $this->form_validation->set_rules('mobile', 'Telephone', 'required');
		  */
		  //$this->form_validation->set_rules('voucher_code', 'VoucherCode', 'callback_checkVcode');
		  
		  
		 
		  if($this->form_validation->run())
		  {
			 //echo "here1";exit;
			$enroll_home = array('home_enroll'  => 'home');
            $this->session->set_userdata($enroll_home);
			if($studentdata['with_coupon']=='yes')
			{
				$voucher_session_array['voucher_code'] = $content['coupenCode'] = $this->input->post('voucher_code');
				
				$content['coupenCode'] = $this->input->post('voucher_code');
				//echo $content['coupenCode']."  courseId = ".$content['course_set'];
				$validCode = $this->gift_voucher_model->isValid($content['coupenCode'],$content['course_set']);
				//echo "<pre>";print_r($validCode);exit;
				if($validCode['code_exist']!=1)
				{
					$this->session->set_flashdata('message','Entered Voucher code doesnot exist.');
				}
				else if($validCode['security_req']=='yes')
				{
					
					 $this->session->set_userdata($voucher_session_array);	
					 $studentdata['redemption_code'] = $content['securitycode'] = $this->input->post('securitycode');
					 $config['upload_path'] = 'public/uploads/deals/pdf/';
					 $config['allowed_types'] = 'gif|jpg|png|pdf';
					 $config['max_size'] = '10000';
					 
					 $this->load->library('upload', $config);
					 
					 if (!$this->upload->do_upload('securitypdf'))
					 {
					 $errors = $this->upload->display_errors();
					 $this->session->set_flashdata("voucher_error","Error! ".$errors);
					 redirect('home/enroll/'.$content['course_set']);
					 }
					 else
					 {
					  $uploaded = $this->upload->data();
					  $studentdata['redemption_pdf']=$uploaded['file_name'];
					  $this->user_model->add_student_temp($studentdata);
					  $student_id=$this->db->insert_id();
					  $redirectPath = '/home/package_details/stud_id/'.$student_id.'/cour_id/'.$studentdata['course_id'];
					  redirect($redirectPath, 'refresh');
					 }
    
				
				}
				else
				{
					$this->session->set_userdata($voucher_session_array);	
					$this->user_model->add_student_temp($studentdata);
					$student_id=$this->db->insert_id();
					$redirectPath = '/home/package_details/stud_id/'.$student_id.'/cour_id/'.$studentdata['course_id'];
					redirect($redirectPath, 'refresh');
				}
			}
			else
			{
				//echo "<pre>";print_r($studentdata);exit;
					$this->session->unset_userdata('student_temp_id');	
					$this->session->unset_userdata('cart_session_id');
					$this->session->unset_userdata('package_applying_course');			
					$this->session->unset_userdata('added_user_id');	
					session_regenerate_id();
					
					$this->user_model->add_student_temp($studentdata);
					$student_id=$this->db->insert_id();	
					
				$redirectPath = '/home/package_details/stud_id/'.$student_id.'/cour_id/'.$studentdata['course_id'];

		//	$redirectPath = '/home/paymentDetails/stud_id/'.$student_id.'/cour_id/'.$studentdata['course_id'];
			redirect($redirectPath, 'refresh');
			
		  }
		}


	}
		}
		else
		{
			if(isset($_POST['user_course_id']))
			{
			
			$user_course_id = $content['course_set'] = $this->input->post('user_course_id');
			$user_id = $this->session->userdata['student_logged_in']['id'];
			$redirectPath = "home/buy_another_course/stud_id/".$user_id."/cour_id/".$user_course_id[0];
			redirect($redirectPath, 'refresh');
				
			}
			
		}		
	//$langId = $this->language;	
	//echo "here";exit;
	$top_menu_base_courses = $this->user_model->get_courses($this->language);	
		
		if(empty($top_menu_base_courses))
		{
			$top_menu_base_courses = $this->user_model->get_courses(4);	
		}
	
	
	$data['top_menu_base_courses'] 			= $top_menu_base_courses;
	$data['translate'] = $this->tr_common;
	
	$data['view'] = 'enrolldetail_test';
	$data['content'] = $content;
	$this->load->view('user/template_outer',$data);
		
	}
	
	
	
	function package_details()
	{
		
		$content = array();
		$course_details = array();
		$content = $this->uri->uri_to_assoc(3);
		
		$coursename=$this->user_model->get_coursename($content['cour_id']);
    	$temp_course_id = $content['cour_id'];
		
		
		
		$this->tr_common['tr_upgrade_your_account_avail_only_registration']  = $this->user_model->translate_('upgrade_your_account_avail_only_registration');
		$this->tr_common['tr_save_over']    = $this->user_model->translate_('save_over');
		$this->tr_common['tr_buy_now_for']  = $this->user_model->translate_('buy_now_for');
		$this->tr_common['tr_read_more']  	= $this->user_model->translate_('read_more');
		$this->tr_common['tr_add_to_bag_2']  	= $this->user_model->translate_('add_to_bag_2');
		$this->tr_common['tr_remove_2']  	= $this->user_model->translate_('remove_2');
		$this->tr_common['tr_complete_registraion']  	= $this->user_model->translate_('complete_registraion');
		
		$this->tr_common['tr_package_terms_condetions']  	= $this->user_model->translate_('package_terms_condetions');
				
		$this->tr_common['tr_personal_details']    = $this->user_model->translate_('personal_details');		
		$this->tr_common['tr_packages']    		= $this->user_model->translate_('packages');
		$this->tr_common['tr_payment_details']     = $this->user_model->translate_('payment_details');
		$this->tr_common['tr_registration']     = $this->user_model->translate_('registration');
		$this->tr_common['tr_total_price']     = $this->user_model->translate_('total_price');
		$this->tr_common['tr_SIGN_UP']   =$this->user_model->translate_('SIGN_UP'); 
				
	   
		$content['language']=$this->language;
		$curr_code=$this->user_model->get_currency_id($this->con_name);
			
		$curr_id= $this->currId;	
		if($this->session->userdata('voucher_code'))
		{		
			$vouchercode = $this->session->userdata('voucher_code');
			$voucherDetails = $this->gift_voucher_model->getDetails_of_vcode($vouchercode);
			if($voucherDetails[0]->courses_idcourses=="" || $voucherDetails[0]->courses_idcourses==0)
			{
				//$content['course_set']=$this->input->post('course');
				$content['course_count']=0;
				$tempArray = $this->user_model->get_student_temp($content['stud_id']);
				$courseId[] =  $temp_course_id;
				$coursename =$this->user_model->get_coursename($courseId[0]);
				foreach($coursename as $cname)
				{
				$content['course_name'][]=ucwords($cname->course_name);
				$content['course_id'][]=$cname->course_id ;
				$content['val_days'][]=$cname->course_validity;
				}
				
				$sales_course_id = $courseId[0];
				$sales_course_count = 1;   
				$multiple_course = $sales_course_id;
				
				//$redirectPath = '/sales/payment_details/'.$temp_id.'/'.$sales_course_id;
			}
			else
			{
				$sales_course_id = $voucherDetails[0]->courses_idcourses;
				$multiple_course = '';
				$courseId = explode(",",$voucherDetails[0]->courses_idcourses);	
				/*echo "<pre>";		
				print_r($courseId);*/
				$content['course_count']= $sales_course_count = count($courseId);			
				for($c=0;$c<count($courseId);$c++)
				{				
					$coursename =$this->user_model->get_coursename($courseId[$c]);
					//print_r($coursename);
					foreach($coursename as $cname)
					{
						if(!$this->session->userdata('package_applying_course'))
						{
							$sess_array = array('package_applying_course' =>$cname->course_id); 				
							$this->session->set_userdata($sess_array);	
						}
						$content['course_name'][]=ucwords($cname->course_name);
						$content['course_id'][]=$cname->course_id ;
						$content['val_days'][]=$cname->course_validity;
					}
				}
				
			   //$redirectPath = '/sales/payment_details/'.$temp_id;
			}
			
			$course_product_id = $this->common_model->getProdectId('course',$item_id='',$sales_course_count);
			
		}
		else
		{
			$content['course_count']=1;
			
			foreach ($coursename as $key) {
			$content['course_name']=$key->course_name ;
			$content['val_days']=$key->course_validity;
			}
		
			$product_course = $this->user_model->get_product_id($content['cour_id']); 
			 foreach ($product_course as $value) {
			  $course_product_id = $value->id;
			}
		}
		
		$price_details_array = $this->common_model->getProductFee($course_product_id,$this->currId);
			
		foreach($price_details_array as $price_det)
		{
			$content['course_fee']		= $price_details_array['amount'];
			$content['currency_symbol']   = $price_details_array['currency_symbol'];
			$content['currency_code']	 = $price_details_array['currency_code'];
			$content['curr_id']		   = $price_details_array['currency_id'];
			
		}
		
		if(!$this->session->userdata('cart_session_id'))
		{		
			$sess_array = array('cart_session_id' => session_id()); 
			
			session_regenerate_id();
			
			$this->session->set_userdata($sess_array);	
			
			/*echo "<pre>";
			print_r ($sess_array);
			
			echo "<pre>";
			print_r ($_SESSION);*/
			
		//	echo  "Session id  ".$this->session->userdata['cart_session_id'];	
			
					
			$product_details = $this->common_model->get_product_details($course_product_id);			
			$product_price_details = $this->common_model->getProductFee($course_product_id,$this->currId);			
		
			$pre_user_id = $content['stud_id'];
			$course_id = $content['cour_id'];
			
			if($this->session->userdata('voucher_code'))
			{						
			$cart_main_insert_array = array("session_id"=>$this->session->userdata('cart_session_id'),"pre_user_id"=>$pre_user_id,"source"=>'payment_package',"item_count"=>1,"total_cart_amount"=>0,"currency_id"=>$product_price_details['currency_id'],"gift_voucher_code"=>$this->session->userdata('voucher_code'));
			}
			else
			{
				$cart_main_insert_array = array("session_id"=>$this->session->userdata('cart_session_id'),"pre_user_id"=>$pre_user_id,"source"=>'payment_package',"item_count"=>1,"total_cart_amount"=>$product_price_details['amount'],"currency_id"=>$product_price_details['currency_id']);
			}	
			
		    $cart_main_id = $this->common_model->insert_to_table("sales_cart_main",$cart_main_insert_array);	
			
			if($this->session->userdata('voucher_code'))
			{											
			$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$course_product_id,"item_amount"=>0,"currency"=>$product_price_details['currency_id']);			
			}
			else
			{
				$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$course_product_id,"item_amount"=>$product_price_details['amount'],"currency"=>$product_price_details['currency_id']);
			}
			
			$cart_items_id = $this->common_model->insert_to_table("sales_cart_items",$item_details_array);
			
			
			
			if($this->session->userdata('voucher_code'))
			{				
			$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>$product_details[0]->type,"selected_item_ids"=>$sales_course_id );
			}
			else
			{
				$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>$product_details[0]->type,"selected_item_ids"=>$course_id);
			}
			
			$item_id = $this->common_model->insert_to_table("sales_cart_item_details",$items_array);	
			
			
			$user_agent_data = array();			
			$user_agent_data['cart_main_id']  = $cart_main_id;
			$user_agent_data['session_id'] 	= $this->session->userdata('cart_session_id');
			$user_agent_data['os'] 			= $this->agent->platform();
			$user_agent_data['browser'] 	   = $this->agent->agent_string();		
			
			$this->common_model->insert_to_table("sales_user_agents",$user_agent_data);
			
			
			
			$this->session->unset_userdata('cart_session_type');			
			$sess_array = array('cart_session_type' => $product_details[0]->type);			
			$this->session->set_userdata($sess_array);	
		
			
			
			
			$currency_symbol = $this->common_model->get_currency_symbol_from_id($product_price_details['currency_id']);
			
		
		}
		
		$added_pack_id = array();
		
		$package_details = $this->package_model->get_packages();
		
		
		foreach($package_details as $pak_det)
		{			
			$product_id = $this->common_model->getProdectId('package',$pak_det->id,1);
			$package_fees[$pak_det->id] = $this->common_model->getProductFee($product_id,$this->currId);
			$package_product_id[$pak_det->id] = $product_id;
		}
		
		if($this->session->userdata('cart_session_id'))
		{
			
		//	$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('cart_session_id'));
			$cart_main_details = $this->sales_model->get_cart_main_details_package($this->session->userdata('cart_session_id'),$this->session->userdata('student_temp_id'));
			foreach($cart_main_details as $cart_main)
			{
				$data['cart_count'] = $cart_main->item_count;
				$data['cart_amount'] = $cart_main->total_cart_amount;
				$data['currency_symbol'] = $this->common_model->get_currency_symbol_from_id($cart_main->currency_id);
			}
			
			
			if(!empty($cart_main_details))
			{
				$cart_main_id = $cart_main_details[0]->id;		
				$package_cart_contents = $this->sales_model->check_product_type_exist_in_cart($cart_main_id,'package');
				if(!empty($package_cart_contents))
				{			
					$added_pack_id = explode(",",$package_cart_contents[0]->selected_item_ids);					
				}				
				
			}		
			
		}
		else
		{
			$data['cart_count'] = 0;
			$data['cart_amount'] = 0;
			$data['currency_symbol'] = $this->common_model->get_currency_symbol_from_id($this->currId);
			
		}	
		if(!$this->session->userdata('package_applying_course'))
		{
			$sess_array = array('package_applying_course' =>$content['cour_id']); 				
			$this->session->set_userdata($sess_array);	
		}	
		
		$content['package_details']=$package_details;
		$content['package_fees']=$package_fees;
		
		$content['product_id']=$product_id;
		$content['package_product_id']=$package_product_id;
		$content['added_pack_id'] = $added_pack_id;
		
		$content['curr_id'] = $this->currId;
		$content['lang_id'] = $this->language;
		
		$data['translate'] = $this->tr_common;
		$data['view'] = 'package_details';
		$data['content'] = $content;
		$this->load->view('user/template_outer',$data);
		
	}
	
	function payment_details($course_id,$stud_id)
 	{
		
    $coursename=$this->user_model->get_coursename($course_id);
	$extended_days = '';
    
    foreach ($coursename as $key) {
    $content['course_name']=$key->course_name ;
    $content['val_days']=$key->course_validity;
    }
	
	$this->tr_common['tr_complete_registraion']  = $this->user_model->translate_('complete_registraion');		
	$this->tr_common['tr_personal_details']      = $this->user_model->translate_('personal_details');		
	$this->tr_common['tr_packages']    		  = $this->user_model->translate_('packages');
	$this->tr_common['tr_payment_details']       = $this->user_model->translate_('payment_details');
	$this->tr_common['tr_registration']    	  = $this->user_model->translate_('registration');
	$this->tr_common['tr_camp_days']    	  = $this->user_model->translate_('camp_days');		 
			
	$this->tr_common['tr_course']   =$this->user_model->translate_('course'); 		
	$this->tr_common['tr_amount']   =$this->user_model->translate_('amount'); 		
	$this->tr_common['tr_valid_for']   =$this->user_model->translate_('valid_for'); 		
		
   
    $content['language']=$this->language;
   	$curr_code=$this->user_model->get_currency_id($this->con_name);
		
	$curr_id= $this->currId;	
	
	$product = $this->user_model->get_product_id($course_id); 
	 foreach ($product as $value) {
      $product_id = $value->id;
    }
	
	$price_details_array = $this->common_model->getProductFee($product_id,$this->currId);	
	
	if($this->session->userdata('cart_session_id'))
	{			
			//$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('cart_session_id'));
			$cart_main_details = $this->sales_model->get_cart_main_details_package($this->session->userdata('cart_session_id'),$this->session->userdata('student_temp_id'));
			foreach($cart_main_details as $cart_main)
			{
				$data['cart_count'] = $cart_main->item_count;
				$data['total_price'] = $cart_main->total_cart_amount;
				$data['currency_symbol'] = $this->common_model->get_currency_symbol_from_id($cart_main->currency_id);
				$data['currency_code']=  $this->common_model->get_currency_code_from_id($this->currId);
				$data['curr_id']= $this->currId;
			}
			
			
			if(!empty($cart_main_details))
			{
				$cart_main_id = $cart_main_details[0]->id;		
				$package_cart_contents = $this->sales_model->check_product_type_exist_in_cart($cart_main_id,'package');
				if(!empty($package_cart_contents))
				{			
					$added_pack_id = $package_cart_contents[0]->selected_item_ids;				
					$products_in_package = explode(',',$this->package_model->get_products_in_package($added_pack_id));				
					$extension_options = $this->common_model->get_product_by_type('extension');	
					
					foreach($extension_options as $ext_opt)
					{
						if(in_array($ext_opt->id,$products_in_package))
						{
							
							$extension_details = $this->sales_model->get_extension_details_by_units($ext_opt->item_id);								
							if($this->session->userdata('language')==3)
							{
								$extended_days = " + ".$extension_details[0]->extension_option_spanish;
							}
							else
							{								
								$extended_days = " + ".$extension_details[0]->extension_option;
							}
							
						}
					}
				}				
				
			}	
			else
			{
				$data['cart_count'] = 0;
				$data['total_price'] = 0; 
				$data['currency_symbol'] = $this->common_model->get_currency_symbol_from_id($this->currId);
				$data['currency_code']=  $this->common_model->get_currency_code_from_id($this->currId);
				$data['curr_id']= $this->currId;
			}
			
		}
		else
		{
			
			
			foreach($price_details_array as $price_det)
			{
				$data['total_price']= $price_details_array['amount'];
				$data['currency_symbol']= $price_details_array['currency_symbol'];
				$data['currency_code']=  $price_details_array['currency_code'];
				$data['curr_id']=  $price_details_array['currency_id'];
				
			}
		}		
	if(isset($data['total_price']) && $data['total_price'] == 0){
	redirect('home/withCoupon/'.$stud_id);
	}
	else{
	redirect('home/package_check_out/'.$stud_id);
	}
	/*$content['stud_id'] = $stud_id;
	$content['course_id'] = $course_id;
	$content['product_id']=$product_id;
	$content['extended_days']=$extended_days;
	
	$data['translate'] = $this->tr_common;
    $data['view'] = 'payment_details';
    $data['content'] = $content;
    $this->load->view('user/outerTemplate',$data);*/

  
 
 	}
	
	function package_check_out($stud_id)
	{
		$content = array();	
		$currency_id = $this->currId;			
		$purchased_item_names = array();
		$product_name = array();		
		$products_in_cart = array();
		$cart_main_details = array();
		$extended_days = '';
		$this->tr_common['tr_sl_no'] 	= $this->user_model->translate_('sl_no');		 
		$this->tr_common['tr_options']  = $this->user_model->translate_('options');		 
		$this->tr_common['tr_price'] 	= $this->user_model->translate_('price');		 
		$this->tr_common['tr_remove']   = $this->user_model->translate_('remove');		 
		$this->tr_common['tr_basket_total'] = $this->user_model->translate_('basket_total');		 
		$this->tr_common['tr_continue_shopping'] = $this->user_model->translate_('continue_shopping');		 
		$this->tr_common['tr_secure_checkout'] = $this->user_model->translate_('secure_checkout');	 
		$this->tr_common['tr_shop_cart'] = $this->user_model->translate_('your_shop_basket');
		$this->tr_common['tr_item'] = $this->user_model->translate_('Item');
		$this->tr_common['tr_Product_Name'] = $this->user_model->translate_('Product_Name');
		$this->tr_common['tr_Type'] = $this->user_model->translate_('Type');
		$this->tr_common['tr_apply_your_certificate'] = $this->user_model->translate_('apply_your_certificate');
		$this->tr_common['tr_apply'] = $this->user_model->translate_('apply');
		$this->tr_common['tr_certificate_applied'] = $this->user_model->translate_('certificate_applied');
		$this->tr_common['tr_sales_no_items_in_cart'] = $this->user_model->translate_('sales_no_items_in_cart');
		
		if($this->session->userdata('cart_session_id'))
		{
			//$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('cart_session_id'));	
			$cart_main_details = $this->sales_model->get_cart_main_details_package($this->session->userdata('cart_session_id'),$this->session->userdata('student_temp_id'));
			$currency_id = $cart_main_details[0]->currency_id;
			$currency_symbol = $this->common_model->get_currency_symbol_from_id($cart_main_details[0]->currency_id);
			foreach($cart_main_details as $cart_main)
			{		
				$cart_main_id = $cart_main->id;	
				$coupon_applied = $cart_main->coupon_applied;	
				$coupon_code_applied = $cart_main->coupon_code; 	
				$discount_amount = $cart_main->discount_amount;
				$products_in_cart = $this->sales_model->get_cart_items($cart_main->id);
				/*echo "<br>Products in cart";
				echo "<pre>";
				print_r($products_in_cart);*/
				
				$q=0;
				if(!empty($products_in_cart))
				{
				foreach($products_in_cart as $prod)
				{
					$purchased_item_names[$q] ='';
					
					
					//$product_id_in_cart = $prod->product_id;
					
					$product_details = $this->common_model->get_product_details($prod->product_id);
					/*echo "<br>Cart item  details";
					echo "<pre>";
					print_r($product_details);*/
					
					$cart_item_details = $this->sales_model->get_cart_items_details($cart_main_id,$prod->id);
					
					/*echo "<br>Cart item  details";
					echo "<pre>";
					print_r($cart_item_details);*/
					foreach($cart_item_details as $item_det)
					{
						$selected_items = $item_det->selected_item_ids;
						
						if($item_det->product_type == 'course')
						{
							$course_ids = explode(',',$selected_items);
							
							$product_name[$q] = $this->user_model->translate_('course');
							
						/*	echo "<br>In course";*/
							
							for($qq=0;$qq<count($course_ids);$qq++)
							{
								if($purchased_item_names[$q]=='')
								{
									$purchased_item_names[$q] = $this->common_model->get_course_name($course_ids[$qq]); 
								}
								else
								{
									$purchased_item_names[$q] .= ','.$this->common_model->get_course_name($course_ids[$qq]); 
								}
									$course_details=$this->user_model->get_coursename($course_ids[$qq]);							
								foreach ($course_details as $key) {							
								$content['val_days']=$key->course_validity;
								
								$product_image[$q] = 'public/user/outer/cart_img/'.$key->home_image;
								}
							}
							
						}
						else if($item_det->product_type == 'package')
						{
							//$package_id = $selected_items;
							$product_name[$q] = $this->user_model->translate_('package');
												
							$package_details = $this->package_model->fetch_package($selected_items);
							$purchased_item_names[$q] = $package_details[0]->package_name;
							$product_image[$q] = 'public/user/outer/cart/packages/'.$package_details[0]->image_name;
						}
					}
					//$product_name[$q] =  $product_details[0]->type;
					$q++;
				}
				}
			}
			if(!empty($cart_main_details))
			{
				$cart_main_id = $cart_main_details[0]->id;		
				$package_cart_contents = $this->sales_model->check_product_type_exist_in_cart($cart_main_id,'package');
				if(!empty($package_cart_contents))
				{			
					$added_pack_id = $package_cart_contents[0]->selected_item_ids;				
					$products_in_package = explode(',',$this->package_model->get_products_in_package($added_pack_id));				
					$extension_options = $this->common_model->get_product_by_type('extension');	
					
					foreach($extension_options as $ext_opt)
					{
						if(in_array($ext_opt->id,$products_in_package))
						{
							
							$extension_details = $this->sales_model->get_extension_details_by_units($ext_opt->item_id);								
							if($this->session->userdata('language')==3)
							{
								$extended_days = " + ".$extension_details[0]->extension_option_spanish;
							}
							else
							{								
								$extended_days = " + ".$extension_details[0]->extension_option;
							}
							
						}
					}
				}				
				
			}
		}
		else
		{
			$currency_symbol = $this->common_model->get_currency_symbol_from_id($this->currId);
		}
			
		$content['extended_days']=$extended_days;
		
			
		$data['sales_from'] = 'pacakge_details';		
		$content['purchased_item_names'] = $purchased_item_names;
		$content['products_in_cart']  = $products_in_cart;
		$content['product_name']      = $product_name;
		$content['product_image']  =$product_image;
		$content['currency_id'] = $currency_id;		
		$content['cart_main_details'] = $cart_main_details;
		$content['stud_id'] = $stud_id;	
		
		$data['currency_symbol'] = $currency_symbol;
		$data['translate'] = $this->tr_common;
		$data['view'] = 'package_check_out';
        $data['content'] = $content;				
        $this->load->view('user/template_outer',$data);  
	
		
	}
	function read_more_view($lang_id,$curr_id,$stud_id,$cour_id)
	{
		$content = array();
		
		$content['stud_id'] = $stud_id;
		$content['cour_id'] = $cour_id;
		
		$this->tr_common['tr_back']    			= $this->user_model->translate_('back');
		$this->tr_common['tr_personal_details']    = $this->user_model->translate_('personal_details');		
		$this->tr_common['tr_packages']    		= $this->user_model->translate_('packages');
		$this->tr_common['tr_payment_details']     = $this->user_model->translate_('payment_details');
		$this->tr_common['tr_registration']    	= $this->user_model->translate_('registration');
		$this->tr_common['tr_SIGN_UP']   =$this->user_model->translate_('SIGN_UP'); 
		$package_read_more = $this->package_model->get_read_more_details($lang_id,$curr_id);
		$content['package_read_more'] = $package_read_more[0]->description;
		
		$data['translate'] = $this->tr_common;
		$data['view'] = 'package_read_more';
		$data['content'] = $content;
		$this->load->view('user/outerTemplate',$data);
		
	}
	
	function enroll_2($course_id,$temp_id)
	{
		
		$this->load->model('gift_voucher_model');
		
		$content['voucher_msg'] = $this->session->flashdata('voucher_err');
		$content['voucher_big_msg'] = $this->session->set_flashdata('voucher_big_err');
		
		$this->tr_common['tr_v_code']   =$this->user_model->translate_('v_code'); 	
		$this->tr_common['tr_comments']   =$this->user_model->translate_('comments'); 	
		$this->tr_common['tr_amount']   =$this->user_model->translate_('amount'); 
		$this->tr_common['tr_valid']   =$this->user_model->translate_('valid'); 
			
		$this->tr_common['tr_voucher_code_note_text']   =$this->user_model->translate_('voucher_code_note_text'); 		
		$this->tr_common['tr_course']   =$this->user_model->translate_('course'); 	
		$this->tr_common['tr_terms_agree']   =$this->user_model->translate_('terms_agree'); 	
	    $this->tr_common['tr_next']   =$this->user_model->translate_('_next'); 
		
		$content['cour_id']=$course_id;
		$content['stud_id']=$temp_id;
		$content['course_set']=$course_id;
		$content['language']=$this->language;
		
		$coursename=$this->user_model->get_coursename($course_id);
    
		foreach ($coursename as $key) {
		$content['course_name']=$key->course_name ;
		$content['val_days']=$key->course_validity;
		}
        
		$product = $this->user_model->get_product_id($course_id); 
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
	
	

		if(isset($_POST['step_2_sub']))
		{
		  $studentdata  = array();
	      //$studentdata['course_id'] = $content['cour_id'];
		  
		 if($this->input->post('voucher_code')=='')
		 {
			 $studentdata['with_coupon']='no';		 
		 }
		 else
		 {
		 	$studentdata['with_coupon']='yes';
			$studentdata['coupon_code'] = $content['voucher_code'] = $this->input->post('voucher_code');
		 }
           $studentdata['course_id']= $content['course_id'] = $this->input->post('course_id');
		   //*********************************************
		   $coursename=$this->user_model->get_coursename($studentdata['course_id']);
    
		foreach ($coursename as $key) {
		$content['course_name']=$key->course_name ;
		$content['val_days']=$key->course_validity;
		}
        
		$product = $this->user_model->get_product_id($studentdata['course_id']); 
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
		   //*********************************************
		   
		  $content['terms'] = $this->input->post('terms');
		  $content['comments'] = $this->input->post('comments');
		  if($content['comments']!="")
		  $studentdata['comment'] =  $this->input->post('comments');
		  else
		  $studentdata['comment']='';
		  //$this->form_validation->set_rules('course_id', 'Course', 'callback_validate[Course]');
		  $this->form_validation->set_rules('terms', 'Acceptance of Terms', 'required');
		 
		  if($this->form_validation->run())
		  {
			  if($studentdata['with_coupon']=='yes')
			{
				$content['coupenCode'] = $this->input->post('voucher_code');
				$validCode = $this->gift_voucher_model->isValidForDeals($content['coupenCode']);
				
				//echo "<pre>";print_r($validCode);exit;
			if( $validCode['code_exist']==0)
			 {
				 $this->session->set_flashdata('voucher_err','Entered Voucher code doesnot exist.');
				 redirect('home/enroll_2/'.$course_id.'/'.$temp_id, 'refresh');
			 }
			 else
			 {
				 if( $validCode['code_error']==0 || $validCode['code_error']==1)//not exeeded extended_end_date
				 {
					if($validCode['security_req']=="no")
					{
						
					}
					else if($validCode['security_req']=="yes")
					{
						$studentdata['redemption_code'] = $this->input->post('voucher_code');
					}
				 }
				 else if($validCode['code_error']==2)//extended end date passed goto pay extension
				 {
					  $this->session->set_flashdata('voucher_big_err','<p >According to our records your voucher has expired on '.$validCode['expired_on'].'</p><p>You can extend your voucher for the next 3 month for a small fee. </p><p>To proceed with extension please <a href="'.base_url().'sales/extend_coupon/'.$content['voucher_code'].'" target="_new">click here</a></p><p>If PDF copy of your voucher states that voucher is still valid please email your voucher to: info@eventtrix.com and we will get you started asap. </p>');
					  redirect('home/enroll_2/'.$course_id.'/'.$temp_id, 'refresh');
				 }
				 else if($validCode['code_error']==3)//voucher alredy used
				 {
					 $this->session->set_flashdata('voucher_big_err',"<p class='red'>Your voucher has already been used on ".$validCode['response.used_on']."</p>");
					  redirect('home/enroll_2/'.$course_id.'/'.$temp_id, 'refresh');
				 }
				 else if($validCode['code_error']==4)//code not found
				 {
					  $this->session->set_flashdata('voucher_err','Entered Voucher code doesnot exist.');
				 redirect('home/enroll_2/'.$course_id.'/'.$temp_id, 'refresh');
				 }
				 $redirectPath = '/home/withCoupon/'.$temp_id;
              }
				
				
				
				
					
				
				
			}
			else
			{
				//echo $product_id.'/'.$temp_id.'/'. $content['course_id'];exit;
				//$redirectPath = '/home/paymentDetails/stud_id/'.$temp_id.'/cour_id/'.$course_id;
				$redirectPath = 'processpayment/prepay/'.$product_id.'/'.$temp_id.'/'. $content['course_id'];
				
			}
			
			if($studentdata['comment'] !=''){
				$mail_data=$studentdata['comment'];
			$temp_details=$this->user_model->get_student_temp($temp_id);
			foreach($temp_details as $row_temp){
			$content['first_name']=	$row_temp->first_name;
			$content['last_name']=	$row_temp->last_name;
			$content['email']=	$row_temp->email;
			
			}
		
		       $this->load->library('email');		
				$mailContent='<p>Name:'.$content['first_name'].' '.$content['last_name'].'</p><p>'.$mail_data.'</p>';
				
				$tomail = 'info@eventtrix.com'; 
				$from = $content['email'];
				$subject = "Comments";
	
				//echo $tomail."<Br>";echo $from."<Br>";echo $subject."<Br>".$mailContent;
						   	
					  $this->email->from($from); 
					  $this->email->to($tomail); 
					  $this->email->reply_to($content['email']);
					  $this->email->subject($subject);
					  $this->email->message($mailContent);	
					  $this->email->send();	
			}
			       $this->db->where('id',$temp_id);
					$this->db->update('pre_registrations',$studentdata);
					redirect($redirectPath, 'refresh');
			
		  
		}


		}
		$course = $this->user_model->get_course($this->language);	
			//echo "<pre>";print_r($top_menu_base_courses);exit;

			if(empty($course))
			{
				$course = $this->user_model->get_course(4);	
			}
		
		

		$data['course'] 			= $course;
		
		$data['translate'] = $this->tr_common;
		
		$data['view'] = 'enrolldetail_2';
		$data['content'] = $content;
		$this->load->view('user/outerTemplate',$data);
		
	}
	
	
	function add_package_to_cart($product_id,$currency_id,$package_id,$product_type,$source)
	{
		
		if(!$this->session->userdata('cart_session_id'))
		{		
			$sess_array = array('cart_session_id' => session_id()); 
			
			session_regenerate_id();
			$this->session->set_userdata($sess_array);	
			$product_details = $this->common_model->get_product_details($product_id);			
			$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);			
			
			$cart_main_insert_array = array("session_id"=>$this->session->userdata('cart_session_id'),"user_id"=>$user_id,"source"=>$source,"item_count"=>1,"total_cart_amount"=>$product_price_details['amount'],"currency_id"=>$product_price_details['currency_id']);
			
			//$cart_main_id =1;
  		  
		    $cart_main_id = $this->common_model->insert_to_table("sales_cart_main",$cart_main_insert_array);
			
			$user_agent_data = array();
			
			$user_agent_data['cart_main_id']  = $cart_main_id;
			$user_agent_data['session_id'] 	= $this->session->userdata('cart_session_id');
			$user_agent_data['os'] 			= $this->agent->platform();
			$user_agent_data['browser'] 	   = $this->agent->agent_string();
			
			
			$this->common_model->insert_to_table("sales_user_agents",$user_agent_data);
									
			$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$product_id,"item_amount"=>$product_price_details['amount'],"currency"=>$product_price_details['currency_id']);	
			
			$cart_items_id = $this->common_model->insert_to_table("sales_cart_items",$item_details_array);		
			
			$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>$product_details[0]->type,"selected_item_ids"=>$package_id);
			
			$this->session->unset_userdata('cart_session_type');			
			$sess_array = array('cart_session_type' => $product_details[0]->type);			
			$this->session->set_userdata($sess_array);
			
			$item_id = $this->common_model->insert_to_table("sales_cart_item_details",$items_array);	
			
			$currency_symbol = $this->common_model->get_currency_symbol_from_id($product_price_details['currency_id']);
			
			$data['err_msg']= 0;
			$data['amount'] = $product_price_details['amount'];
			$data['count'] = 1;
			$data['currency_symbol'] = $currency_symbol;
			$data['removed_package_id'] = 0;	
			$data['removed_product_id'] = 0;
			echo json_encode($data); 
			exit;     	
		}
		{
			//echo $this->session->userdata('cart_session_id');
			
			//$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('cart_session_id'));
			$cart_main_details = $this->sales_model->get_cart_main_details_package($this->session->userdata('cart_session_id'),$this->session->userdata('student_temp_id'));
			foreach($cart_main_details as $cart_main)
			{
				$cart_main_id = $cart_main->id;
				$product_in_cart = $this->sales_model->check_product_type_exist_in_cart($cart_main->id,$product_type);
				if(empty($product_in_cart))
				{			
				$product_details = $this->common_model->get_product_details($product_id);			
				$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
									
				$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$product_id,"item_amount"=>$product_price_details['amount'],"currency"=>$product_price_details['currency_id']);
				
				$cart_items_id = $this->common_model->insert_to_table("sales_cart_items",$item_details_array);		
			
				$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>$product_details[0]->type,"selected_item_ids"=>$package_id);
			
				$item_id = $this->common_model->insert_to_table("sales_cart_item_details",$items_array);	
				
				$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main->id);
				
				$cart_items_total_amount=@round($cart_items_total_amount,2);
				
				
				$cart_total_items = count($this->sales_model->get_cart_items($cart_main->id));
				
				$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount);
				$this->sales_model->main_cart_details_update($this->session->userdata('cart_session_id'),$update_array);
									
				$currency_symbol = $this->common_model->get_currency_symbol_from_id($product_price_details['currency_id']);
				
				$data['err_msg']= 0;
				$data['amount'] = $cart_items_total_amount;
				$data['count'] = $cart_total_items;
				$data['currency_symbol'] = $currency_symbol;
				$data['removed_package_id'] = 0;	
				$data['removed_product_id'] = 0;
				echo json_encode($data); 
				exit;
				}
				else // already one package is in cart
				{ 
									
				$cart_item_id = $product_in_cart[0]->cart_items_id;
				$removed_package_id = $product_in_cart[0]->selected_item_ids;
				/* Remove that package from cart and */
				
				$removing_product_id = $this->sales_model->get_product_id_from_cart_items_id($cart_main_id,$cart_item_id);
				
				//echo "REmoviing product id ".$removing_product_id." Cart main id ".$cart_main_id."   Cart Items Id ".$cart_item_id;
				
				$cart_details_by_product = $this->sales_model->get_cart_items_by_product_id($cart_main_id,$removing_product_id);
				
				
			/*	echo "<pre>";
				print_r($cart_details_by_product);*/
				
				$cart_details_id = $cart_details_by_product[0]->id;			
				$this->sales_model->delete_item_from_cart_by_product_id($cart_main_id,$cart_details_id,$removing_product_id);	
				
						
						
						/* REmoved already added package and add the package selected  */	
				$product_details = $this->common_model->get_product_details($product_id);			
				$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
									
				$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$product_id,"item_amount"=>$product_price_details['amount'],"currency"=>$product_price_details['currency_id']);
				
				$cart_items_id = $this->common_model->insert_to_table("sales_cart_items",$item_details_array);		
			
				$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>$product_details[0]->type,"selected_item_ids"=>$package_id);
			
				$item_id = $this->common_model->insert_to_table("sales_cart_item_details",$items_array);	
				
				$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main->id);
				
				$cart_items_total_amount=@round($cart_items_total_amount,2);
				
				
				$cart_total_items = count($this->sales_model->get_cart_items($cart_main->id));
				
				$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount);
				$this->sales_model->main_cart_details_update($this->session->userdata('cart_session_id'),$update_array);
									
				$currency_symbol = $this->common_model->get_currency_symbol_from_id($product_price_details['currency_id']);
				
				$data['err_msg']= 0;
				$data['amount'] = $cart_items_total_amount;
				$data['count'] = $cart_total_items;
				$data['currency_symbol'] = $currency_symbol;
				$data['removed_package_id'] = $removed_package_id;	
				$data['removed_product_id'] = $removing_product_id;			
				echo json_encode($data); 
				exit;
				
				
				
					
				}
				
				//$cart_items = $this->sales_model->get_cart_items($cart_main->id);
			}
			
		
			
		}
	}
	
	
	function change_password()
	{
		$user_id = $this->session->userdata['student_logged_in']['id'];
		$content = array();
		
		if(isset($_POST['submit']))
		{
		
		  $studentdata  = array();
		  $studentdata['passWord']  	 = $this->input->post('cur_pass');
		  $studentdata['email'] 	 	 = $this->input->post('new_pass');
		  $studentdata['contactNumber']  = $this->input->post('conf_pass');
		 
		  
		  
		  
		    $this->form_validation->set_rules('cur_pass', 'Old password', 'trim|required');		
			$this->form_validation->set_rules('new_pass', 'New password', 'required|min_length[6]');			
		    $this->form_validation->set_rules('conf_pass', 'Password', 'required|min_length[6]');			
			
			if($this->form_validation->run())
			{	
			 	$this->user_model->update_student_details($studentdata,$user_id);
			 	 $this->session->set_flashdata('message', 'Student details updated successfully!');
			 	 redirect('coursemanager/campus/'.$user_id, 'refresh');
			}
		  
		  
		}
		
		//redirect('coursemanager/campus/'.$this->session->userdata['student_logged_in']['id'], 'refresh');
		
		
		
	
	}
	
  function check_current_password($curPass)
  {
  	$user_id = $this->session->userdata['student_logged_in']['id'];
	
	$result = $this->user_model->check_current_password($user_id, $curPass);
	
			if(!$result)
			{						
				$this->form_validation->set_message('check_current_password','Current password not match');
				return false;
			}
			else
			{
				 return TRUE;
			}
  	
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
  function chkpword($val,$name)
  {
    $conpword = $this->input->post('con_pword');

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
  function chkemail($val,$name)
  {
    $conemail = $this->input->post('con_email');

    if($val!==$conemail)
    {
       $this->form_validation->set_message('chkemail', 'E-mail doesn\'t match');
                return FALSE;
    }
    else
    {
      return TRUE;
    }
  }
  
  

	function update_student_details()
	{
		$user_id = $this->session->userdata['student_logged_in']['id'];
		
		
		
		if(isset($_POST['submit']))
		{
		
		  $studentdata  = array();
		  $studentdata['first_name']  		= $content['firstName'] = $this->input->post('fname');
		  $studentdata['email'] 	 		= $content['email'] = $this->input->post('email');
		  $studentdata['contact_number'] 	= $content['contactNumber'] = $this->input->post('contact_no');
		  $studentdata['dob'] 	= $content['dob'] = $this->input->post('dob');
		  $studentdata['country_id'] = $content['country_id'] = $this->input->post('country');
		  
		  
		  
		    $this->form_validation->set_rules('fname', 'First name', 'trim|required');		
			$this->form_validation->set_rules('email', 'Email', 'trim|required');			
		    $this->form_validation->set_rules('contact_no', 'contact number', 'trim|required');		
			$this->form_validation->set_rules('country', 'country', 'trim|required');
			$this->form_validation->set_rules('dob', 'Date of Birth', 'trim|required');	
					
			
			if($this->form_validation->run())
			{	
			 	$this->user_model->update_student_details($studentdata,$user_id);
			 	 $this->session->set_flashdata('message', 'Student details updated successfully!');
			 	 redirect('coursemanager/campus/'.$user_id, 'refresh');
			}
		  
		  
		}
	
	}
 	function paymentDetails()
 	{
 		$content = $this->uri->uri_to_assoc(3);
		
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
	
	
	/*$content['currency_code'] = $this->currencyCode;
	$content['curr_id'] = $curr_id;
	
   	$coursefee=$this->user_model->get_coursefee($content['cour_id'],$curr_id);
	
    foreach ($coursefee as $key){
      $content['course_fee']=$key->amount;
    }*/
	
	/*$product=$this->user_model->get_product_id($content['cour_id']);
	foreach ($product as $key){
      $content['product_id']=$key->id;
    }*/
    //echo "<pre>";print_r($content);exit;
	
	$data['translate'] = $this->tr_common;
    $data['view'] = 'paymentdetails';
    $data['content'] = $content;
    $this->load->view('user/template',$data);

  
 
 	}
	
	function buy_another_course_old()
	{
		$content = $this->uri->uri_to_assoc(3);
		
		$content['voucher_msg'] = $this->session->flashdata('voucher_err');
		$content['voucher_big_msg'] = $this->session->set_flashdata('voucher_big_err');
		
		$this->tr_common['tr_comments']   =$this->user_model->translate_('comments'); 	
		$this->tr_common['tr_amount']   =$this->user_model->translate_('amount'); 
		$this->tr_common['tr_valid']   =$this->user_model->translate_('valid'); 
			
		$this->tr_common['tr_voucher_code_note_text']   =$this->user_model->translate_('voucher_code_note_text'); 		
		$this->tr_common['tr_course']   =$this->user_model->translate_('course'); 	
		$this->tr_common['tr_terms_agree']   =$this->user_model->translate_('terms_agree'); 	
	    $this->tr_common['tr_next']   =$this->user_model->translate_('_next'); 
		$this->tr_common['tr_v_code']   =$this->user_model->translate_('v_code'); 	
		
		$coursename=$this->user_model->get_coursename($content['cour_id']);
		
		if($this->session->userdata('current_course_selected'))	
		{
			if($this->session->userdata('current_course_selected') != $content['cour_id'])
			{
				
				$this->session->unset_userdata('coupon_applied');
				$this->session->unset_userdata('coupon_applied_details');	
				$session_array = array("current_course_selected"=>$content['cour_id'] );
				$this->session->set_userdata($session_array);	
			}
			
		}
		else
		{
			$session_array = array("current_course_selected"=>$content['cour_id'] );
			$this->session->set_userdata($session_array);
		}
		
		
		foreach ($coursename as $key) {
		$content['course_name']=$key->course_name ;
		$content['val_days']=$key->course_validity;
		}
	   
		$content['language']=$this->language;
		$curr_code=$this->user_model->get_currency_id($this->con_name);
			
		$curr_id= $this->currId;
		
		$product_id = $this->common_model->getProdectId('extra_course');
		
		$coursefee=$this->common_model->getProductFee($product_id,$curr_id);
		
		/*
		echo "<pre>";
		print_r($coursefee);
		exit;*/
		 $content['product_id']=$product_id;
		foreach ($coursefee as $key){
		  $content['course_fee']=$coursefee['amount'];		  
		  $content['currency_code'] = $coursefee['currency_code'];
		  $content['curr_id'] = $coursefee['currency_id'];
		  $content['currency_symbol'] = $coursefee['currency_symbol'];
		  
		  
		}
		
		/*$product = $this->common_model->get_product_by_type('extra_course');
		foreach ($product as $key){
		  $content['product_id']=$key->id;
		}*/
		//echo "<pre>";print_r($content);exit;
		
		$data['translate'] = $this->tr_common;
		$data['view'] = 'buy_another_payment';
		$data['content'] = $content;
		$this->load->view('user/outerTemplate',$data);
		
		
	}
	
	function buy_another_course($course_id)
	{
		$content = $this->uri->uri_to_assoc(3);
		
		$content['voucher_msg'] = $this->session->flashdata('voucher_err');
		$content['voucher_big_msg'] = $this->session->set_flashdata('voucher_big_err');
		
		$this->tr_common['tr_comments']   =$this->user_model->translate_('comments'); 	
		$this->tr_common['tr_amount']   =$this->user_model->translate_('amount'); 
		$this->tr_common['tr_course_validity']   =$this->user_model->translate_('course_validity'); 
			
		$this->tr_common['tr_voucher_code_note_text']   =$this->user_model->translate_('voucher_code_note_text'); 		
		$this->tr_common['tr_course']   =$this->user_model->translate_('course'); 	
		$this->tr_common['tr_terms_agree']   =$this->user_model->translate_('terms_agree'); 	
	    $this->tr_common['tr_next']   =$this->user_model->translate_('_next'); 
		$this->tr_common['tr_v_code']   =$this->user_model->translate_('v_code'); 	
		
		$coursename=$this->user_model->get_coursename($course_id);
		
		if($this->session->userdata('current_course_selected'))	
		{
			//echo $this->session->userdata('current_course_selected').'/'.$course_id;exit;
			
			if($this->session->userdata('current_course_selected') != $course_id)
			{
				//echo "here";exit;
				$this->session->unset_userdata('coupon_applied');
				$this->session->unset_userdata('coupon_applied_details');	
				$session_array = array("current_course_selected"=>$course_id );
				$this->session->set_userdata($session_array);	
			}
			
		}
		else
		{
			
			$session_array = array("current_course_selected"=>$course_id );
			$this->session->set_userdata($session_array);
		}
		
		
		foreach ($coursename as $key) {
		$content['course_name']=$key->course_name ;
		$content['val_days']=$key->course_validity;
		$content['course_image']=$key->campus_image;
		}
	   
		$content['language']=$this->language;
		$curr_code=$this->user_model->get_currency_id($this->con_name);
			
		$curr_id= $this->currId;
		
		$product_id = $this->common_model->getProdectId('extra_course');
		
		$coursefee=$this->common_model->getProductFee($product_id,$curr_id);

		$content['stud_id'] = $this->session->userdata['student_logged_in']['id'];
		$content['cour_id'] = $course_id;
		
		/*
		echo "<pre>";
		print_r($coursefee);
		exit;*/
		 $content['product_id']=$product_id;
		foreach ($coursefee as $key){
		  $content['course_fee']=$coursefee['amount'];		  
		  $content['currency_code'] = $coursefee['currency_code'];
		  $content['curr_id'] = $coursefee['currency_id'];
		  $content['currency_symbol'] = $coursefee['currency_symbol'];
		  
		  
		}
		
		/*$product = $this->common_model->get_product_by_type('extra_course');
		foreach ($product as $key){
		  $content['product_id']=$key->id;
		}*/
		//echo "<pre>";print_r($content);exit;
		
		$data['translate'] = $this->tr_common;
		$data['view'] = 'buy_another_payment';
		$data['content'] = $content;
		$this->load->view('user/template_inner',$data);
		
		
	}
	
	
	
	
	function apply_coupon_code($coupon_code,$currency_id,$type)
	{
		$this->load->model('discount_code_model','',TRUE);		
		
		
		$coupon_details = $this->discount_code_model->get_coupon_details_from_code($coupon_code,$type);
		
		
		
		/*echo "Herer";
		echo "<pre>";
		print_r($coupon_details);
		exit;*/
		
		if(!empty($coupon_details))
		{
			$data['discount_code'] = $coupon_code;
			$data['discount_code_id'] = $coupon_details[0]->id;
			
			if($this->session->userdata('coupon_applied'))
			{
				/*echo "<pre>";
					print_r($this->session->userdata('coupon_applied_details'));*/
					$data['err_msg']= 1;
					$data['err_type'] = 'Coupon already applied';					
					echo json_encode($data); 
					exit;		
			}
			else
			{
				
				
		
				$product_id = $this->common_model->getProdectId('extra_course');		
				$price_det     = $this->common_model->getProductFee($product_id,$currency_id);
				/*echo "<pre>";
				print_r($price_det);
				exit;*/
				
				$amount = $price_det['amount'];
				$currency_symbol = $price_det['currency_symbol'];
				$currency_code = $price_det['currency_code'];
				
				$discount_value = $coupon_details[0]->discount_value;
					if($coupon_details[0]->discount_type=='percentage')
					{
						//@round($cart_items_total_amount,2);
						//$amount = $cart_items[0]->item_amount;
//						number_format($number, 2, '.', '');
												
						$reduced_amount = $amount - round(( ($amount * $discount_value) / 100 ),2);					
						$discount_amount = $amount-$reduced_amount;
					}
					elseif($coupon_details[0]->discount_type=='price')
					{
					//	$amount = $cart_items[0]->item_amount;
					
				$currency_id_discount_value = $this->discount_code_model->get_amount_for_discount_code($coupon_details[0]->id,$currency_id);
						if($currency_id_discount_value!='')
						{
						
						$reduced_amount = $amount-($currency_id_discount_value);
						$discount_amount = $amount-$reduced_amount;
							if($reduced_amount<=0)
							{
								$data['err_msg']= 1;
								$data['err_type'] = 'Something went wrong. Plaese contact info@eventtrix.com';					
								echo json_encode($data); 
								exit;	
							}
						
						}
						else
						{
							$data['err_msg']= 1;
							$data['err_type'] = 'Currency not supported. Plaese contact info@eventtrix.com';					
							echo json_encode($data); 
							exit;	
							
						}
					}
					$user_id = $this->session->userdata['student_logged_in']['id'];
					$user_agent_data = array();			
					$user_agent_data['user_id']       = $user_id;
					$user_agent_data['course_id'] 	 = $this->session->userdata('current_course_selected');
					$user_agent_data['discount_id']   = $coupon_details[0]->id;				
					$user_agent_data['os'] 			= $this->agent->platform();
					$user_agent_data['browser'] 	   = $this->agent->agent_string();
			
			
					$this->common_model->insert_to_table("discount_codes_user_agents",$user_agent_data);
					
					
					$sess_array = array('coupon_applied' => true);
					$this->session->set_userdata($sess_array);
				
					$data['err_msg']		 = 0;
					$data['amount'] 		  = $reduced_amount;
					$data['discount_amount'] = $discount_amount;					
					$data['currency_symbol'] = $currency_symbol;
					
					$data['currency_code']   = $currency_code;
					
					$sess_array = array('coupon_applied_details' => $data);
				    $this->session->set_userdata($sess_array);
					
					/*echo "<pre>";
					print_r($_SESSION);
					exit;*/
					
					echo json_encode($data); 
					exit;		
					
					
			}
		}
		
					$data['err_msg']= 1;
					$data['err_type'] = 'Coupon not applicable';					
					echo json_encode($data); 
					exit;	
		
	}
	
	
	function process_reg()
	{
		$this->load->model('course_model','',TRUE);
		$this->load->model('payment_model','',TRUE);
		
		$userId =$this->uri->segment(3);
		$paymentId = $this->uri->segment(4);
		$courseId = $this->uri->segment(5);
		
		if(isset($paymentId))
		{
			$langId = $this->course_model->get_lang_course($courseId);
			$dateNow =date('Y-m-d');
			
					
			
			$tempArray = $this->user_model->get_student_temp($userId);
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
				$studentdata['reason_id'] = $row->reason_id;
				$studentdata['country_id'] = $row->country_id;
				$studentdata['us_states'] = $row->us_states;
				$studentdata['reg_date'] = $dateNow;
				$studentdata['dob'] = $row->dob;
				$studentdata['lang_id'] = $langId;
				$studentdata['status']=1;
			}
			$user_id = $this->user_model->add_student($studentdata);
			
			
			$expirityDate = $this->user_model->findExpirityDate($courseId,$dateNow);
			$usersUnit = $this->user_model->get_courseunits_id($courseId);
			foreach($usersUnit as $row)
			{
				$un[$row->units_order] = $row->course_units;
			}
			$student_courseData['student_course_units'] = serialize($un);
			
			
			$student_courseData['course_id'] = $row->course_id;
			$student_courseData['user_id'] = $user_id;
			$student_courseData['date_enrolled'] = $dateNow;
			$student_courseData['date_expiry'] = $expirityDate;
			$student_courseData['enroll_type'] = 'payment';
			$student_courseData['course_status'] = '0';
			
			$courseEnrId = $this->user_model->add_course_student($student_courseData);
			
			$resumeLinkArr['user_id']=$user_id;
			$resumeLinkArr['course_id']=$courseId;
			$resumeLinkArr['resume_link']='coursemanager/studentcourse/'.$courseId;
			$this->user_model->addResumeLink($resumeLinkArr);
		
			// update user id in payment table
			$upArray['user_id']=$user_id ;
					$this->payment_model->userId_updation($upArray,$paymentId);
					if($this->session->userdata['ip_address'] == '117.242.195.15')
		{
			echo "<pre>";
			echo "course id is ".$courseId. " and expiry date is ".$expirityDate;
			print_r($student_courseData);
			
			exit;
		}

			if(isset($paymentId))
			{
				$this->load->library('email');
				$this->load->model('email_model');
				$this->load->library('encrypt');
				
				$en_studId = $this->encrypt->encode($user_id);//encoding student id
				
				
				$row_new = $this->email_model->getTemplateById('new_registration',$langId);
				
				foreach($row_new as $row1)
				{
					
					$emailSubject = $row1->mail_subject;
					$mailContent = $row1->mail_content;
					$mailing_template_id=$row1->id;
				}
				if($langId==3)
				{
				 	$mailContent = str_replace ( "#firstname#",$studentdata['first_name'], $mailContent );
					/*$mailContent = str_replace ( "#click here#","<a href='http://trendimi.net/home/studentActivation/".$en_studId."'>clica aquí</a>", $mailContent );
					
					$mailContent = str_replace ( "#actlink#","http://trendimi.net/home/studentActivation/".$en_studId." ",$mailContent );*/
					$mailContent = str_replace ( "#url#", "<a href='".base_url()."'>Eventrix</a>", $mailContent );
					$mailContent = str_replace ( "#username#", $studentdata['username'], $mailContent );
					$mailContent = str_replace ( "#password#", $this->encrypt->decode($studentdata['password']), $mailContent );
				}
				else
				{
					$mailContent = str_replace ( "#firstname#",$studentdata['first_name'], $mailContent );
					$mailContent = str_replace ( "#click here#","<a href='".base_url()."home/studentActivation/".$en_studId."'>click here</a>", $mailContent );
					
					$mailContent = str_replace ( "#actlink#","".base_url()."home/studentActivation/".$en_studId." ",$mailContent );
					$mailContent = str_replace ( "#url#", "<a href='".base_url()."'>Eventrix</a>", $mailContent );
					$mailContent = str_replace ( "#username#", $studentdata['username'], $mailContent );
					$mailContent = str_replace ( "#password#", $this->encrypt->decode($studentdata['password']), $mailContent );
				}
				  
				  
					$tomail = $studentdata['email'];
					
					$this->email->from('mailer@eventtrix.com', 'Team Eventrix');
					$this->email->to($tomail); 
					$this->email->cc(''); 
					$this->email->bcc(''); 
					$this->email->subject($emailSubject);
					$this->email->message($mailContent);	
					  
					$sent=$this->email->send();
				  if($sent==TRUE){
				  $mailing_histrory=array();
				  $mailing_histrory['email_id']=$tomail;
				  $mailing_histrory['user_id']=$user_id;
				  $mailing_histrory['template_id']=$mailing_template_id;
				  $mailing_histrory['mailing_date']=date("Y-m-d");
				  $this->common_model->add_email_history($mailing_histrory);
				  }
					$en_user_id = urlencode($this->encrypt->encode($user_id));
			redirect('home/couponSuccess/'.$en_user_id,'refresh');
		}
				
			
		}
		
	}
	
	/*---- buy another course processing -----*/
	function process_add_course()
	{
		$this->load->model('course_model','',TRUE);
		$this->load->model('payment_model','',TRUE);
		
		$userId =$this->uri->segment(3);
		$paymentId = $this->uri->segment(4);
		$courseId = $this->uri->segment(5);
		
		if(isset($paymentId))
		{
			$langId = $this->course_model->get_lang_course($courseId);
			$dateNow =date('Y-m-d');
			
		    //$user_name = $this->common_model->get_user_name($userId);
			$course_name = $this->common_model->get_course_name_buy_another($courseId); 
			//echo $course_name;exit;
			
			 $stud_details=$this->user_model->get_stud_details($userId);
			 
			 foreach($stud_details as $row){
			  $first_name = $row->first_name;			  
			  $to_mail    = $row->email;
			  
			}			
			
		
			$expirityDate = $this->user_model->findExpirityDate($courseId,$dateNow);
			$usersUnit = $this->user_model->get_courseunits_id($courseId);
			foreach($usersUnit as $row)
			{
				$un[$row->units_order] = $row->course_units;
			}
			$student_courseData['student_course_units'] = serialize($un);
			
			
			$student_courseData['course_id'] = $row->course_id;
			$student_courseData['user_id'] = $userId;
			$student_courseData['date_enrolled'] = $dateNow;
			$student_courseData['date_expiry'] = $expirityDate;
			if($this->session->userdata('coupon_applied'))
		  	{
				$student_courseData['enroll_type'] = 'payment_discount';
				$this->session->unset_userdata('coupon_applied');
				$this->session->unset_userdata('coupon_applied_details');	
			}
			else
			{
				$student_courseData['enroll_type'] = 'payment';
			}
			$student_courseData['course_status'] = '0';
			
			$courseEnrId = $this->user_model->add_course_student($student_courseData);
			
			$resumeLinkArr['user_id']=$userId;
			$resumeLinkArr['course_id']=$courseId;
			$resumeLinkArr['resume_link']='coursemanager/studentcourse/'.$courseId;
			$this->user_model->addResumeLink($resumeLinkArr);
		
			// update user id in payment table
			$upArray['user_id']=$userId ;
			$this->payment_model->userId_updation($upArray,$paymentId);
			
		
		
		
		
				$this->load->library('email');
				$this->load->model('email_model');
				$this->load->library('encrypt');
				
				$en_studId = $this->encrypt->encode($userId);//encoding student id
				
				
				$row_new = $this->email_model->getTemplateById('new_course',$langId);
				
				foreach($row_new as $row1)
				{
					
					$emailSubject = $row1->mail_subject;
					$mailContent = $row1->mail_content;
					$mailing_template_id=$row1->id;
				}
				
				 	$mailContent = str_replace ( "#firstname#",$first_name, $mailContent );
					$mailContent = str_replace ( "#course_name#",$course_name, $mailContent );
														  
				  
				
					
					$this->email->from('mailer@eventtrix.com', 'Team Eventrix');
					$this->email->to($to_mail); 
					$this->email->cc(''); 
					$this->email->bcc(''); 
					$this->email->subject($emailSubject);
					$this->email->message($mailContent);	
					  
					$sent=$this->email->send();
				  if($sent==TRUE){
				  $mailing_histrory=array();
				  $mailing_histrory['email_id']=$to_mail;
				  $mailing_histrory['user_id']=$userId;
				  $mailing_histrory['template_id']=$mailing_template_id;
				  $mailing_histrory['mailing_date']=date("Y-m-d");
				  $this->common_model->add_email_history($mailing_histrory);
				  }
			redirect('home/paySuccess','refresh');
		}
		
		
	}
		
		
	
	
	
	/*---- buy another course processing end -----*/
	
	function withCoupon($id)
	{
		$this->load->model('gift_voucher_model');
		$this->load->model('course_model');
		
		$pre_user_id = $id;
		//$this->session->userdata('voucher_code')
		
		$vouchercode = $this->session->userdata('voucher_code');
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
			if($langId=='')
			{
				$langId = $this->language;	
			}
			
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
				$studentdata['us_states'] = $row->us_states;
				$studentdata['dob'] = $row->dob;
				$studentdata['reason_id'] = $row->reason_id;
				$studentdata['reg_date'] = $dateNow;
				$studentdata['lang_id'] = $langId;
				$studentdata['reg_type'] = 'voucher_home';
				$content['coupon_code'] = $row->coupon_code;
				$content['redemption_code'] = $row->redemption_code;
				$content['redemption_pdf'] = $row->redemption_pdf;
				
			}
			
			$user_id = $this->user_model->add_student($studentdata);
			
			
			
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
			$student_courseData['enroll_type'] = 'coupon';
			$student_courseData['course_status'] = '0';
			
			$courseEnrId[] = $this->user_model->add_course_student($student_courseData);
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
			
					
					$expirityDate = $this->user_model->findExpirityDate($courseId[$co],$dateNow,$voucherDetails[0]->idgiftVoucher);
					$student_courseData['course_id'] = $courseId[$co];
					$student_courseData['user_id'] = $user_id;
					$student_courseData['date_enrolled'] = $dateNow;
					$student_courseData['date_expiry'] = $expirityDate;
					$student_courseData['enroll_type'] = 'payment';
					$student_courseData['course_status'] = '0';
					$courseEnrId[] = $this->user_model->add_course_student($student_courseData);
				}
			}
			//echo $courseEnrId;
			for($ce=0;$ce<count($courseEnrId);$ce++)
			{
			$resumeLinkArr['user_id']=$user_id;
			$resumeLinkArr['course_id']=$courseId[$ce];
			$resumeLinkArr['resume_link']='coursemanager/studentcourse/'.$courseId[$ce];
			$this->user_model->addResumeLink($resumeLinkArr);
			}
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
				$couponDetails['pdf_name']=$content['redemption_pdf'];
				
				}
				$couponDetails['website_id']=$webVoucherId;
				$couponDetails['date']=$dateNow;
			
			$redeemedCoupenId = $this->user_model->add_redeemedCoupon($couponDetails);
			
			if(isset($redeemedCoupenId))
			{
				
				$this->load->library('email');
				$this->load->model('email_model');
				
				$row_new = $this->email_model->getTemplateById('new_registration',$langId);
				foreach($row_new as $row1)
				  {
					  
					  $emailSubject = $row1->mail_subject;
					  $mailContent = $row1->mail_content;
					  $mailing_template_id=$row1->id;
				  }
				 	$mailContent = str_replace ( "#firstname#",$studentdata['first_name'], $mailContent );
					$mailContent = str_replace ( "#click here#","<a href='".base_url()."home/studentActivation/".$user_id."'>click here</a>", $mailContent );
					
					$mailContent = str_replace ( "#actlink#","".base_url()."home/studentActivation/".$user_id."", $mailContent  );
					
				$mailContent = str_replace ( "#url#", "<a href='".base_url()."'>Eventrix</a>", $mailContent );
				$mailContent = str_replace ( "#username#", $studentdata['username'], $mailContent );
				$mailContent = str_replace ( "#password#", $this->encrypt->decode($studentdata['password']), $mailContent );
				  
				  
				$tomail = $studentdata['email'];
					
				
						   	
					  $this->email->from('mailer@eventtrix.com', 'Team Eventrix');
					  $this->email->to($tomail); 
					  $this->email->cc(''); 
					  $this->email->bcc(''); 
					  
					  $this->email->subject($emailSubject);
					  $this->email->message($mailContent);	
					  
					 $sent=$this->email->send();
					if($sent==TRUE){
				  $mailing_histrory=array();
				  $mailing_histrory['email_id']=$tomail;
				  $mailing_histrory['user_id']=$user_id;
				  $mailing_histrory['template_id']=$mailing_template_id;
				  $mailing_histrory['mailing_date']=date("Y-m-d");
				  $this->common_model->add_email_history($mailing_histrory);
					}
				
				$this->common_model->deactivate_voucher_code($tempArray[0]->coupon_code);	
				
				
			$cart_main_update_array = array("user_id"=>$user_id);								
			$this->sales_model->main_cart_details_update_user_id($this->session->userdata('cart_session_id'),$pre_user_id,$cart_main_update_array);
				
				$this->session->unset_userdata('cart_session_id');
				$this->session->unset_userdata('package_applying_course');			
				$this->session->unset_userdata('added_user_id');
				$this->session->unset_userdata('enrolling_rep_code');
					  
			    $en_user_id = urlencode($this->encrypt->encode($user_id)); 
					  
				redirect('home/couponSuccess/'.$en_user_id,'refresh');
			}
					
		}
	function studentActivation(){
		$this->load->library('encrypt');
		$en_uid = $this->uri->segment(3);
		
		//echo $en_uid."<br>";
		$uid = $en_uid;
		//$uid = $this->encrypt->decode($en_uid);
		//echo $uid;exit;
		$studentdata['status'] ='1';
		$this->load->model('student_model');
		$this->student_model->add_details($studentdata,$uid);
		redirect('home/activated/'.$uid,'refresh');
				
			
	}
	
	
	function paySuccess()
 	{
 			$data['tr_transactionSuccess'] = $this->user_model->translate_('regComplete');
			$data['tr_welcome'] = $this->user_model->translate_('Welcome to Eventrix!');
			$data['tr_successFull'] = $this->user_model->translate_('successFullyRegistered');
			$data['tr_transSuccess_text'] = $this->user_model->translate_('transSuccess_text');
			//echo "<pre>";print_r($data['tr_styleYou']);
			
			$langId = $this->language;	
			$top_menu_base_courses = $this->user_model->get_courses($this->language);	
		
		if(empty($top_menu_base_courses))
		{
			$top_menu_base_courses = $this->user_model->get_courses(4);	
		}
			
			$data['top_menu_base_courses'] 			= $top_menu_base_courses;
			
			$data['translate'] = $this->tr_common;
			$data['view'] = 'paymentSuccess';
			$contents['pageTitle']='paymentSuccess';
			$data['top_menu_base_courses'] 			= $top_menu_base_courses;
			$data['content'] = $contents;
			
			$this->load->view('user/template_inner',$data);
 	}
	function offerSuccess()
 	{
 			$data['tr_transactionSuccess'] = $this->user_model->translate_('regComplete');
			$data['tr_welcome'] = $this->user_model->translate_('Welcome to Eventrix!');
			$data['tr_successFull'] = $this->user_model->translate_('successFullyRegistered');
			$data['tr_transSuccess_text'] = $this->user_model->translate_('transSuccess_text');
			//echo "<pre>";print_r($data['tr_styleYou']);
			
			$langId = $this->language;	
			$top_menu_base_courses = $this->user_model->get_courses($this->language);	
		
		if(empty($top_menu_base_courses))
		{
			$top_menu_base_courses = $this->user_model->get_courses(4);	
		}
			
			$data['top_menu_base_courses'] 			= $top_menu_base_courses;
			$data['translate'] = $this->tr_common;
			$data['view'] = 'offerSuccess';
			$contents['pageTitle']='paymentSuccess';
			$data['content'] = $contents;
			$this->load->view('user/outerTemplate',$data);
 	}
	
	function couponSuccess()
 	{
		$en_user_id = $this->uri->segment(3);
		$user_id = $this->encrypt->decode(urldecode($en_user_id));
		if($user_id!="")
		{
		$userDetails = $this->user_model->get_stud_details($user_id);
		$data['user_name']=$userDetails[0]->username;
		$data['password']=$this->encrypt->decode($userDetails[0]->password);
		}
 			$data['tr_transactionSuccess'] = $this->user_model->translate_('regComplete');
			$data['tr_welcome'] = $this->user_model->translate_('Welcome to Eventrix!');
			$data['tr_voucherAccepted'] = $this->user_model->translate_('successFullyRegistered');
			$data['tr_voucherAccepted_text'] = $this->user_model->translate_('transSuccess_text');
			//echo "<pre>";print_r($data['tr_styleYou']);
			
			$langId = $this->language;	
			$top_menu_base_courses = $this->user_model->get_courses($this->language);	
		
		if(empty($top_menu_base_courses))
		{
			$top_menu_base_courses = $this->user_model->get_courses(4);	
		}
			$data['top_menu_base_courses'] 			= $top_menu_base_courses;
			$data['translate'] = $this->tr_common;
			$data['view'] = 'voucherAccept';
			$contents['pageTitle']='paymentSuccess';
			$data['content'] = $contents;
			$this->load->view('user/template_outer',$data);
 	}
	function buy_another_course_success()
 	{
			$data['tr_transactionSuccess'] = $this->user_model->translate_('regComplete');
			$data['tr_welcome'] = $this->user_model->translate_('Welcome to Eventrix!');
			$data['tr_successFull'] = $this->user_model->translate_('successFullyRegistered');
			$data['tr_transSuccess_text'] = $this->user_model->translate_('transSuccess_text');
			//echo "<pre>";print_r($data['tr_styleYou']);
			
			$langId = $this->language;	
			$top_menu_base_courses = $this->user_model->get_courses($this->language);	
		
		if(empty($top_menu_base_courses))
		{
			$top_menu_base_courses = $this->user_model->get_courses(4);	
		}
			
			$data['top_menu_base_courses'] 			= $top_menu_base_courses;
			
			$data['translate'] = $this->tr_common;
			$data['view'] = 'buy_another_course_success';
			$contents['pageTitle']='paymentSuccess';
			$data['content'] = $contents;
			$this->load->view('user/template_outer',$data);
 	}
	function activated($uid)
 	{
 			$data['tr_userActivated'] = $this->user_model->translate_('reg_cofirm');
			$data['tr_welcome'] = $this->user_model->translate_('Welcome to Eventrix!');
			$data['tr_userActivated_text'] = $this->user_model->translate_('reg_cofirm_text');
			
			$sess_array = array('id' => $uid);
       		$this->session->set_userdata('student_logged_in', $sess_array);
			redirect('coursemanager/campus');
			//echo "<pre>";print_r($data['tr_styleYou']);
			//$data['view'] = 'courseActivated';
			//$contents['pageTitle']='Activated';
			//$data['content'] = $contents;
			//$this->load->view('user/outerTemplate',$data);
 	}
	function EbookBought()
 	{
 		$this->session->unset_userdata('cart_public_session_id');
		//$this->session->userdata('cart_public_session_id');
			$langId = $this->language;	
			$top_menu_base_courses = $this->user_model->get_courses($langId);	
			$data['top_menu_base_courses'] 			= $top_menu_base_courses;
			$data['translate'] = $this->tr_common;
			$data['view'] = 'ebookPurchased';
			$contents['pageTitle']='Eventtrix';
			$data['content'] = $contents;
			$this->load->view('user/template_outer',$data);
 	}
	
	
	function aboutus()
 	{
		$top_menu_base_courses = $this->user_model->get_courses($this->language);	
		
		if(empty($top_menu_base_courses))
		{
			$top_menu_base_courses = $this->user_model->get_courses(4);	
		}			
		$data['top_menu_base_courses'] 			= $top_menu_base_courses;
		
		$data['translate'] = $this->tr_common;
 		$this->load->view('user/aboutus',$data);
 	}
 	
 	function startstyling()
 	{

		$top_menu_base_courses = $this->user_model->get_courses($this->language);	
		
		if(empty($top_menu_base_courses))
		{
			$top_menu_base_courses = $this->user_model->get_courses(4);	
		}			
		$data['top_menu_base_courses'] 			= $top_menu_base_courses;
		
		$data['translate'] = $this->tr_common;
 		$this->load->view('user/startstyling',$data);
 	}
 	
 	function fittingroom()
 	{
		
		$top_menu_base_courses = $this->user_model->get_courses($this->language);	
		
		if(empty($top_menu_base_courses))
		{
			$top_menu_base_courses = $this->user_model->get_courses(4);	
		}			
		$data['top_menu_base_courses'] 			= $top_menu_base_courses;
		$data['translate'] = $this->tr_common;
 		$this->load->view('user/fittingroom',$data);
 	}
 	
 	/*function trendimiebooks()
 	{
 		
		$this->load->model('ebook_model');
		
		if(!isset($this->session->userdata['student_logged_in']['id']))
		{
			$type = 'public';
		}
		else
		{
			$type = 'user';
			$user_id = $this->session->userdata['student_logged_in']['id'];
		}
		
		$ebArray = $this->ebook_model->fetchEbookByLang($this->language);
		if(!empty($ebArray))
		{
			$i = 0;
			foreach($ebArray as $row)
			{
				
				$ebDetails['ebId'][$i] = $row->ebid;
				$ebDetails['ebName'][$i] = $row->ebookName;
				$ebDetails['language'][$i] = $row->language;
				$ebDetails['description'][$i] = $row->description;
				$ebDetails['fileName'][$i] = $row->fileName;
				$ebDetails['sample_ebook_name'][$i] = $row->sample_ebook_name;
				$ebDetails['courseId'][$i] = $row->courseId;
				$ebDetails['picPath'][$i] = $row->image_name;
				
				
				$prodectId = $this->common_model->getProdectId('ebooks');	
				$ebookPrice =$this->common_model->getProductFee($prodectId,$this->currId);
				$ebDetails['fake_amount'][$i] =$ebookPrice['fake_amount'];
				$ebDetails['amount'][$i] =$ebookPrice['amount'];
				
					
				
				
				
			$i++;
			}
		}
		//echo "<pre>";print_r($ebDetails);print_r($ebookPrice);exit;
		
 		$data['view'] = 'trendimiebooks_test1';
        $data['content'] = $ebDetails;
		
        $this->load->view('user/outerTemplate',$data);
	
 	}*/

 	function faq()
 	{
		$data['translate'] = $this->tr_common;
		$data['view'] = 'faq';
		$langId = $this->language;	
		$top_menu_base_courses = $this->user_model->get_courses($langId);
		
		$data['top_menu_base_courses'] 			= $top_menu_base_courses;
		$content['pageTitle']="FAQs";
		$data['content'] =$content;
		$this->load->view('user/outerTemplate',$data);
			//$this->load->view('user/faq',$data);
 	}
	
	function help_center()
 	{
		$content=$this->get_student_deatils_for_popup();		
		

		$data['translate'] = $this->tr_common;
		
		$data['tr_help_center_search_text'] = $this->user_model->translate_('help_center_search_text');
		
		
		if($this->language==4)
		$data['view'] = 'help_center_english';   
		else if($this->language==3)
		$data['view'] = 'help_center_spanish';
		$data['content'] = $content;
		$this->load->view('user/help_center_template',$data);  
		//$this->load->view('user/template_inner',$data); 
		
 	
 	}

 	function contact()
 	{
		$data['translate'] = $this->tr_common;
		$data['view'] = 'contact';
		
		$this->load->view('user/outerTemplate',$data);
		
		
 	}

	
 	function services()
 	{
		$data['translate'] = $this->tr_common;
 		$this->load->view('user/services',$data);
 	}


 	function stylemakeup()
 	{
		$data['translate'] = $this->tr_common;
 		$this->load->view('user/stylemakeup',$data);

 	}

 	function styleyoumakeup()
 	{
		$data['translate'] = $this->tr_common;
 		$this->load->view('user/styleyoumakeup',$data);
 	}

 	function stylemeyou()
 	{
		$data['translate'] = $this->tr_common;
 		$this->load->view('user/stylemeyou',$data);
 	}

 	function stylememakeup()
 	{
		$data['translate'] = $this->tr_common;
 		$this->load->view('user/stylememakeup',$data);
 	}

 	function enrolldetail()
 	{
		$data['translate'] = $this->tr_common;
 		$this->load->view('user/enrolldetail',$data);
 	}

 	/*function termsofuse()
 	{
		$data['translate'] = $this->tr_common;
 		$this->load->view('user/termsofuse',$data);
 	}
 	function privacypolicy()
 	{
 		$this->load->view('user/privacypolicy');
 	}*/
	
	
	
	
	
	function Ebooks_test()
	{
		$this->load->model('ebook_model');
		
		/*for pop up details */
		if(isset($this->session->userdata['student_logged_in']['id']))
		$content=$this->get_student_deatils_for_popup();
		
		 $this->tr_common['tr_ebook_for'] = $this->user_model->translate_('ebook_for');
		 $this->tr_common['tr_reduced_from'] = $this->user_model->translate_('reduced_from');
		 $this->tr_common['tr_add_to_bag'] = $this->user_model->translate_('add_to_bag');
		  
		  
		  
		
		
		if(!isset($this->session->userdata['student_logged_in']['id']))
		{
			$type = 'public';
			$user_id='';
			$item='ebooks_public';
		}
		else
		{
			$type = 'user';
			$user_id = $this->session->userdata['student_logged_in']['id'];
			$ebDetails['userId'] =$user_id;
			$item='ebooks';
		}
		
		//echo $ebDetails['userId'];exit;
		$ebArray = $this->ebook_model->fetchEbookByLang($this->language);
		if(!empty($ebArray))
		{
			$i = 0;
			foreach($ebArray as $row)
			{
				
				$ebDetails['ebId'][$i] = $row->ebid;
				$ebDetails['ebName'][$i] = $row->ebookName;
				$ebDetails['language'][$i] = $row->language;
				$ebDetails['description'] = $row->description;
				$ebDetails['fileName'][$i] = $row->fileName;
				$ebDetails['sample_ebook_name'][$i] = $row->sample_ebook_name;
				$ebDetails['courseId'][$i] = $row->courseId;
				$ebDetails['picPath'][$i] = $row->image_name;
				//translations
				$ebDetails['tr_trndimi_ebooks'] = $this->user_model->translate_('trendimi_ebooks');
				$ebDetails['tr_trndimi_ebooks_text'] =$this->user_model->translate_('trndimi_ebooks_text');
				$unit='1';
				if($i==1)
				{
				$unit='2';
				}
				$prodectId[$i] = $this->common_model->getProdectId($item,'',$unit);	
				$ebookPrice =$this->common_model->getProductFee($prodectId[$i],$this->currId);
				$ebDetails['fake_amount'][$i] =$ebookPrice['fake_amount'];
				$ebDetails['amount'][$i] =$ebookPrice['amount'];
				$ebDetails['currency_symbol'][$i] =$ebookPrice['currency_symbol'];
				$ebDetails['currency_id'][$i] =$ebookPrice['currency_id'];
				
				
				$i++;
			}
		}
	
		if(!isset($this->session->userdata['student_logged_in']['id']))
		{
			$prodArr = $this->common_model->get_product_by_type('ebooks_public');
		}
		else
		{
			$prodArr = $this->common_model->get_product_by_type('ebooks');
		}
		$x=0;
		foreach($prodArr as $row2)
		{
				$ebookPrice =$this->common_model->getProductFee($row2->id,$this->currId);
				$ebDetails['full_fake'][$x] =$ebookPrice['fake_amount'];
				$ebDetails['full_amount'][$x] =$ebookPrice['amount'];
				$ebDetails['full_curr_sym'][$x] =$ebookPrice['currency_symbol'];
				$ebDetails['full_curr_id'][$x] =$ebookPrice['currency_id'];
				$x++;
		}
		
		if($_POST)
		{
		if(isset($_POST['password_ebook']))
		{
			$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
   			$this->form_validation->set_rules('password_ebook', 'Password', 'trim|required|xss_clean|callback_check_database');
			$content['username_ebook'] = $this->input->post('username');
			if($this->form_validation->run() == TRUE)
			{
			//updating email cart to this userId
			$upArr['session_id'] = session_id();
			$upArr['user_id'] = $this->session->userdata['student_logged_in']['id'];
			
				$this->ebook_model->convert_cart($upArr);
				redirect('home/ebookCart/1', 'refresh');
     		}
			
		}
		else if(isset($_POST['public_name']))
		{
			$this->form_validation->set_rules('public_name', 'Name', 'trim|required|xss_clean');
   			$this->form_validation->set_rules('public_email', 'Email', 'trim|required|xss_clean');
			$content['public_name']=$publicdata['name'] = $this->input->post('public_name');
			$content['public_email']=$publicdata['email'] = $this->input->post('public_email');
			
			if($this->form_validation->run() == TRUE)
			{
				$publicdata['ebook_id'] = 0;
				$public_id['public_id'] =$this->ebook_model->add_public($publicdata);
				$this->session->set_userdata($public_id);
				
				$upArr['session_id'] = session_id();
				$upArr['user_id'] = 0;
			
				$this->ebook_model->convert_cart($upArr);
				
				redirect('home/ebookCart', 'refresh');
     		}
			
		}
		}
		//echo "<pre>";print_r($ebDetails);print_r($ebookPrice);exit;
		if(isset($this->session->userdata['student_logged_in']['id']))
		{
			$user_id = $this->session->userdata['student_logged_in']['id'];
		    $ebDetails['suscribedEbooks'] = $this->ebook_model->suscribed_ebooks($user_id);
		}
		
		$data['translate'] = $this->tr_common;
 		$data['view'] = 'ET_eBooks';
        $data['content'] = $ebDetails;
		if($user_id=='')
        $this->load->view('user/cartTemplate',$data);
		else
		$this->load->view('user/innerTemplate',$data);
		
	}
	function ebooks()
	{
		$this->load->model('ebook_model');
		
		/*for pop up details */
		if(isset($this->session->userdata['student_logged_in']['id']))
		$content=$this->get_student_deatils_for_popup();
		
	
		
		if(!isset($this->session->userdata['student_logged_in']['id']))
		{
			$type = 'public';
			$user_id='';
			$lang_id  = $this->language;
		}
		else
		{
			$type = 'user';
			$user_id = $this->session->userdata['student_logged_in']['id'];
			$ebDetails['userId'] =$user_id;
			$lang_id  = $this->common_model->get_user_lang_id($user_id);
		}
		
		//echo $ebDetails['userId'];exit;
		$data['ebook_array']=$ebArray = $this->ebook_model->fetchEbookByLang($this->language);
	//	echo "<pre>";print_r($data['ebook_array']);exit;
		if(!empty($ebArray))
		{
			$i = 0;
			foreach($ebArray as $row)
			{
				
				$ebDetails['ebId'][$i] = $row->ebid;
				$ebDetails['ebName'][$i] = $row->ebookName;
				$ebDetails['language'][$i] = $row->language;
				$ebDetails['description'][$i] = $row->description;
				$ebDetails['fileName'][$i] = $row->fileName;
				$ebDetails['sample_ebook_name'][$i] = $row->sample_ebook_name;
				$ebDetails['courseId'][$i] = $row->courseId;
				$ebDetails['picPath'][$i] = $row->image_name;
				//translations
			
				$prodectId = $this->common_model->getProdectId('ebooks');	
				$ebookPrice =$this->common_model->getProductFee($prodectId,$this->currId);
				$ebDetails['fake_amount'][$i] =$ebookPrice['fake_amount'];
				$ebDetails['amount'][$i] =$ebookPrice['amount'];
				$ebDetails['currency_symbol'][$i] =$ebookPrice['currency_symbol'];
				$ebDetails['currency_id'][$i] =$ebookPrice['currency_id'];
				
				
				$i++;
			}
		}
		//echo "<pre>";print_r($ebDetails);exit;
		if(!isset($this->session->userdata['student_logged_in']['id']))
		{
			$prodArr = $this->common_model->get_product_by_type('ebooks_public');
		}
		else
		{
			$prodArr = $this->common_model->get_product_by_type('ebooks');
		}
		
		$x=0;
		foreach($prodArr as $row2)
		{
				$ebookPrice =$this->common_model->getProductFee($row2->id,$this->currId);
				$ebDetails['full_fake'][$x] =$ebookPrice['fake_amount'];
				$ebDetails['full_amount'][$x] =$ebookPrice['amount'];
				$ebDetails['full_curr_sym'][$x] =$ebookPrice['currency_symbol'];
				$ebDetails['full_curr_id'][$x] =$ebookPrice['currency_id'];
				$x++;
		}
		
		if($_POST)
		{ 
		if(isset($_POST['password_ebook']))
		{
			$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
   			$this->form_validation->set_rules('password_ebook', 'Password', 'trim|required|xss_clean|callback_check_database');
			$content['username_ebook'] = $this->input->post('username');
			if($this->form_validation->run() == TRUE)
			{
			//updating email cart to this userId
			$upArr['session_id'] = $this->session->userdata('cart_public_session_id');
			$upArr['user_id'] = $this->session->userdata['student_logged_in']['id'];
				//echo "<pre>";print_r($upArr);exit;		
				$this->ebook_model->convert_cart($upArr);
				redirect('home/sales_ebooks/1', 'refresh');
     		}
			
		}
		else if(isset($_POST['public_name']))
		{
			$this->form_validation->set_rules('public_name', 'Name', 'trim|required|xss_clean');
   			$this->form_validation->set_rules('public_email', 'Email', 'trim|required|xss_clean');
			$content['public_name']=$publicdata['name'] = $this->input->post('public_name');
			$content['public_email']=$publicdata['email'] = $this->input->post('public_email');
			
			if($this->form_validation->run() == TRUE)
			{
				$publicdata['ebook_id'] = 0;
				$public_id['public_id'] =$this->ebook_model->add_public($publicdata);
				$this->session->set_userdata($public_id);
				
				$upArr['session_id'] = $this->session->userdata('cart_public_session_id');
				$upArr['user_id'] = 0;
			
				$this->ebook_model->convert_cart($upArr);				
				redirect('home/ebookCart', 'refresh');
     		}
			
		}
		}
		//echo "<pre>";print_r($ebDetails);print_r($ebookPrice);exit;
		
		$top_menu_base_courses = $this->user_model->get_courses($this->language);	
		
		if(empty($top_menu_base_courses))
		{
			$top_menu_base_courses = $this->user_model->get_courses(4);	
		}	
		$data['lang_id']				 = $lang_id;		
		$data['top_menu_base_courses']   = $top_menu_base_courses;
		
		$data['translate'] = $this->tr_common;
		
		$data['view'] = 'ebook_guides';
		
        $data['content'] = $ebDetails;
		
		$data['content']['pageTitle'] = "";
		$data['content']['metaDesc'] = "";
		$data['content']['metaKeys'] = "";

		$this->load->view('user/template_outer',$data);
		// $this->load->view('user/template_outer',$data);
		/*if($user_id=='')
        $this->load->view('user/cartTemplate',$data);
		else
		$this->load->view('user/innerTemplate',$data);		*/
	}
		
	function sales_ebooks($add=NULL)
	{
		$this->load->model('ebook_model');
		
		$currency_id = $this->currId;
		
		if(!$this->session->userdata('student_logged_in')){
		  redirect('home');
		}	
		$user_id = $this->session->userdata['student_logged_in']['id'];
		
		if($add!=NULL)
		{
		if($this->session->userdata('sessionId')){
		
		
					
			$sess_array = array('cart_session_id' => session_id()); 
			
			session_regenerate_id();
			
			$this->session->set_userdata($sess_array);	
			
		/*	echo "<pre>";
			print_r ($sess_array);*/
			
			
			
			//echo  "Session id  ".$this->session->userdata('sessionId')."/".$user_id;	exit;	
		
			$ebArray = $this->ebook_model->fetchEbookTemp($user_id,$this->session->userdata('cart_public_session_id'));
		//echo "<pre>";print_r($ebArray);exit;
		
		$y=0;
		$new_selected_values = '';
		foreach($ebArray as $row)
			{
				if($new_selected_values=='')
				{
					$new_selected_values = $row->ebook_id;
				}
				else
				{
					$new_selected_values .=','.$row->ebook_id;
				}
				
			$y++;
			}
			
			
				
				
			//getting price of ebook's package as a product
			$ebook_product_id = $this->common_model->getProdectId('ebooks','',$y);
			$ebookPrice =$this->common_model->getProductFee($ebook_product_id,$this->currId);
				/*echo "<pre>";
				print_r($ebookPrice);
				
				echo "<br> Curr id ".$ebookPrice['currency_id'];*/
			
			$type ='';
			$source = 'ebooks';
			/*
			echo "<br> Product id ".$ebook_product_id;
			echo "<br> Selected valus ".$new_selected_values;
			echo "<br> Type ".$type;
			echo "<br> Surce ".$source;
			echo "<br> Currency Id  ".$ebookPrice['currency_id'];*/
		//	exit;
			
			
			
		
		//$this->add_item_to_cart($ebook_product_id,$selected_values,$ebookPrice['currency_id'],$type,$source);
		
		
		$user_id = $this->session->userdata['student_logged_in']['id'];
		
		//$currency_id = $this->currId;
		//$currency_code = $this->currencyCode;	
		$this->session->unset_userdata('cart_session_id');	
				
		if(!$this->session->userdata('cart_session_id'))
		{		
			$sess_array = array('cart_session_id' => session_id()); 
			
			$this->session->set_userdata($sess_array);	
			
			$sess_array = array('cart_source' => '/home/sales_ebooks/');
			$this->session->set_userdata($sess_array);
			
			/*echo "<pre>";
			print_r ($sess_array);
			
			echo "<pre>";
			print_r ($_SESSION);*/
			
		//	echo  "Session id  ".$this->session->userdata['cart_session_id'];		
			
			$product_details = $this->common_model->get_product_details($ebook_product_id);
			
			$product_price_details = $this->common_model->getProductFee($ebook_product_id,$currency_id);
			
			/*echo "Product id  ".$product_id;
			echo "<br> Selected items ".$new_selected_values;
			echo "<br>";
			
			echo "<pre>";
			print_r($product_details);
			
			echo "<br>";
			
			echo "<pre>";
			print_r($product_price_details);*/
			
			$cart_main_insert_array = array("session_id"=>$this->session->userdata('cart_session_id'),"user_id"=>$user_id,"source"=>$source,"item_count"=>1,"total_cart_amount"=>$product_price_details['amount'],"currency_id"=>$product_price_details['currency_id']);
			
			//$cart_main_id =1;
  		 // echo "<pre>";print_r($cart_main_insert_array);exit;
		    $cart_main_id = $this->common_model->insert_to_table("sales_cart_main",$cart_main_insert_array);
				
			$user_agent_data = array();
			
			$user_agent_data['cart_main_id']  = $cart_main_id;
			$user_agent_data['session_id'] 	= $this->session->userdata('cart_session_id');
			$user_agent_data['os'] 			= $this->agent->platform();
			$user_agent_data['browser'] 	   = $this->agent->agent_string();
			
			
			$this->common_model->insert_to_table("sales_user_agents",$user_agent_data);
		/*	echo "<br> Main insert array ";
			
			echo "<pre>";
			print_r($cart_main_insert_array);*/
				
									
			$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$ebook_product_id,"item_amount"=>$product_price_details['amount'],"currency"=>$product_price_details['currency_id']);
			
			/*echo "<br> Item details array ";
			
			echo "<pre>";
			print_r($item_details_array);*/
			//$cart_items_id =10;
			
			$cart_items_id = $this->common_model->insert_to_table("sales_cart_items",$item_details_array);		
			
			$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>$product_details[0]->type,"selected_item_ids"=>$new_selected_values);
			
			/*echo "<br> Items array ";
			
			echo "<pre>";
			print_r($items_array);*/
			
			$item_id = $this->common_model->insert_to_table("sales_cart_item_details",$items_array);	
			
			$currency_symbol = $this->common_model->get_currency_symbol_from_id($product_price_details['currency_id']);
		
		}
		
	}
		
		
		
		}
				
		
			
		$content = array();
		$ebook_product_ids = array();
		$ebook_units = array();
		$ebook_price_details = array();
		
		
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
		
		redirect('home/ebookCart');
		
	/*	echo "<pre>";
		print_r($course_array);
		exit;*/
		
		
					
	//	$currency_symbol 				= $currency_det[0]->currency_symbol;		
	//	$data['cur_symbol']    		 = $currency_symbol;	
	//	$content['currency_code'] 	   = $currency_code;
	
	
	
	
	
	
	
			
		/*$content['ebook_array']   	     = $ebook_array;
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
		
		
	
		$content['proof_of_study_soft_id']    = $proof_of_study_soft_id;
		$content['proof_of_study_soft_price'] = $proof_of_study_soft_price;
		
		$content['proof_of_study_hard_id']    = $proof_of_study_hard_id;
		$content['proof_of_study_hard_price'] = $proof_of_study_hard_price;
		
		
		$data['translate'] 			  = $this->tr_common;
		
		
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
		
		$data['view'] = 'sales_ebooks';
        $data['content'] = $content;
				
        $this->load->view('user/pop_up_template',$data);*/
		

		
	}
	
	
	
	
	function ebookDownload()
	{
		$this->load->model('ebook_model');
		$ebArray = $this->ebook_model->fetchEbookByLang($this->language);
		$content['suscribedEbooks'] = '';
		if(!empty($ebArray))
		{
			$i = 0;
			foreach($ebArray as $row)
			{
				
				$content['ebId'][$i] = $row->ebid;
				$content['ebName'][$i] = $row->ebookName;
				$content['language'][$i] = $row->language;
				$content['description'] = $row->description;
				$content['fileName'][$i] = $row->fileName;
				$content['sample_ebook_name'][$i] = $row->sample_ebook_name;
				$content['courseId'][$i] = $row->courseId;
				$content['picPath'][$i] = $row->image_name;
				
				$unit=1;
				if($i==1)
				{ 
				$unit='2';  
				}
				
				$prodectId = $this->common_model->getProdectId('ebooks','',$unit);	
				$ebookPrice =$this->common_model->getProductFee($prodectId,$this->currId);
				$content['fake_amount'][$i] =$ebookPrice['fake_amount'];
				$content['amount'][$i] =$ebookPrice['amount'];
				
				
					
				
				
				
			$i++;
			}
		}
		
		if(isset($this->session->userdata['student_logged_in']['id'])){
		$content['user_id'] = $this->session->userdata['student_logged_in']['id'];
		$content['suscribedEbooks'] = $this->ebook_model->suscribed_ebooks($content['user_id']);
	    //echo "<pre>";print_r($suscribedEbooks);exit;
		}
		else
		{
		$content['user_id'] = '';
		//$content['suscribedEbooks'] = $this->ebook_model->suscribed_ebooks($content['user_id']);
		if($_POST)
		{
			if(isset($_POST['username']))
		{
			$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
   			$this->form_validation->set_rules('password_ebook', 'Password', 'trim|required|xss_clean|callback_check_database');
			$content['username_ebook'] = $this->input->post('username');
			if($this->form_validation->run() == TRUE)
			{
				
				redirect('home/ebookDownload', 'refresh');
     		}
			
		}
		else if(isset($_POST['public_email']))
		{
			
   			$this->form_validation->set_rules('public_email', 'Email', 'trim|required|xss_clean');
			
			
			if($this->form_validation->run() == TRUE)
			{
				$content['suscribedEbooks'] =$this->ebook_model->get_public($this->input->post('public_email'));
			}
			
		}
			
		}
		
		}
		
		
		
		//$content['ebDetails']=$ebDetails;
		
		$content['translate'] = $this->tr_common;
		$content['view'] = 'ebookDownload';
		$content['content']['title'] = 'Download Ebook';
		if(!isset($this->session->userdata['student_logged_in']))
		$this->load->view('user/template',$content);
		else
		$this->load->view('user/template',$content);
		
	}

	
	
	
	function addEbookToCart($ebId)
	{
		$this->load->model('ebook_model');
		$sessionData['sessionId'] = session_id();
		$this->session->set_userdata($sessionData);
		/*echo $sessionData['sessionId'];
		echo "<br>sessionId in session".$this->session->userdata('sessionId');*/
		$tempDetails['session_id']=$this->session->userdata('sessionId');
		$tempDetails['ebook_id']=$ebId;
		if(isset($this->session->userdata['student_logged_in']['id']))
		$tempDetails['user_id']=$this->session->userdata['student_logged_in']['id'];
		
		
		
		$ebookTempId = $this->ebook_model->addEbookCart($tempDetails);
		if(isset($ebookTempId))
		echo $ebookTempId;
		else
		echo 0;
		
	}
	function removeEbookFromCart($ebId)
	{
		$this->load->model('ebook_model');
		$sessionData['sessionId'] = session_id();
		$this->session->set_userdata($sessionData);
		
		$tempDetails['session_id']=$this->session->userdata('sessionId');
		$tempDetails['ebook_id']=$ebId;
		
		$ebookTempId = $this->ebook_model->removeEbookCart($tempDetails);
		if(isset($ebookTempId))
		echo $ebookTempId;
		else
		echo 0;
		
	}
	
	
	

	
	function TrendimiUser()
 	{
		if($_POST)
		{
		if(isset($_POST['username_ebook'])&&isset($_POST['password_ebook']))
		{
			$this->form_validation->set_rules('username_ebook', 'Username', 'trim|required|xss_clean');
   			$this->form_validation->set_rules('password_ebook', 'Password', 'trim|required|xss_clean|callback_check_database');
			$content['username_ebook'] = $this->input->post('username');
			if($this->form_validation->run() == TRUE)
			{//Go to private area
				redirect('home/ebookCart', 'refresh');
     		}
			
		}
		else if(isset($_POST['public_name'])&&isset($_POST['public_email']))
		{
			$this->form_validation->set_rules('public_name', 'Name', 'trim|required|xss_clean');
   			$this->form_validation->set_rules('public_email', 'Email', 'trim|required|xss_clean');
			$content['public_name']=$publicdata['name'] = $this->input->post('public_name');
			$content['public_email']=$publicdata['email'] = $this->input->post('public_email');
			if($this->form_validation->run() == TRUE)
			{
				$public_id['public_id'] =$this->emial_model->add_public($publicdata);
				$this->session->set_userdata($public_id);
				redirect('home/ebookCart', 'refresh');
     		}
			
		}
		}
		$content['view']=$view;
		$langId = $this->language;	
		$top_menu_base_courses = $this->user_model->get_courses($langId);
		$data['top_menu_base_courses'] 			= $top_menu_base_courses;
 		$this->load->view('user/outerTemplate',$content);
 	}
	
	
	function sales_extension($extension_product_id=NULL,$ext_cur_id=NULL,$course_id=NULL)
	{
		
		$this->tr_common['tr_FASHION_EBOOKS']   =$this->user_model->translate_('FASHION_EBOOKS'); 
		$this->tr_common['tr_Adviceat_your_fingertips_foranexciting_styling_career']   =$this->user_model->translate_('Adviceat_your_fingertips_foranexciting_styling_career'); 
		$this->tr_common['tr_ebook_text_sales']   =$this->user_model->translate_('ebook_text_sales'); 
		$this->tr_common['tr_ADDTOBAG']   =$this->user_model->translate_('ADDTOBAG'); 
		$this->tr_common['tr_More_options']   =$this->user_model->translate_('More_options'); 
		$this->tr_common['tr_FASHION_COURSES']   =$this->user_model->translate_('FASHION_COURSES'); 
		$this->tr_common['tr_Expandyour_styling_skillswith_extracourses']   =$this->user_model->translate_('Expandyour_styling_skillswith_extracourses'); 
		$this->tr_common['tr_course_text_sales']   =$this->user_model->translate_('course_text_sales'); 
		$this->tr_common['tr_color_wheel']   =$this->user_model->translate_('color_wheel'); 
		$this->tr_common['tr_Professional_decisions_in_an_instantwith_Trendimicolourcards']   =$this->user_model->translate_('Professional_decisions_in_an_instantwith_Trendimicolourcards'); 
		$this->tr_common['tr_color_wheel_text']   =$this->user_model->translate_('color_wheel_text'); 
		$this->tr_common['tr_total_price']   =$this->user_model->translate_('total_price'); 
		$this->tr_common['tr_remove']   =$this->user_model->translate_('remove'); 
		$this->tr_common['tr_ebook_for']   =$this->user_model->translate_('ebook_for'); 
		$this->tr_common['tr_checkout']   =$this->user_model->translate_('checkout');
		$this->tr_common['tr_select']   =$this->user_model->translate_('select');
		$this->tr_common['tr_reduced_from']   =$this->user_model->translate_('reduced_from');
		$this->tr_common['tr_course_for']   =$this->user_model->translate_('course_for');
		$this->tr_common['tr_Downloadablecopy_for']   =$this->user_model->translate_('Downloadablecopy_for');
		$this->tr_common['tr_We_thoughyoumight_likethe_following_offers']   =$this->user_model->translate_('We_thoughyoumight_likethe_following_offers');
		$this->tr_common['tr_No_courses_to_buy']   =$this->user_model->translate_('No_courses_to_buy');
		 
		 $added_ebook_array = array();
		 $added_course_array = array();
		
		$this->load->model('ebook_model');
		
		if(!$this->session->userdata('student_logged_in')){
		  redirect('home');
		}	
		$user_id = $this->session->userdata['student_logged_in']['id'];
		
		
		//echo "Product id Extension  ".$this->input->post('product_id_extension');
		
		
		if($extension_product_id != NULL)
		{
			if($this->session->userdata('extension_session_id'))
			{  
				if($this->session->userdata('extension_session_id')!=$extension_product_id)
				{
					$this->session->unset_userdata('cart_session_id');
				}
			}
		if(!$this->session->userdata('cart_session_id'))
		{	
		
			session_regenerate_id();	
			$sess_array = array('cart_session_id' => session_id()); 			
			$this->session->set_userdata($sess_array);	
			
			
			$sess_array = array('extension_session_id' => $extension_product_id); 			
			$this->session->set_userdata($sess_array);	
			
			$sess_array = array('cart_source' => '/home/sales_extension/');
			$this->session->set_userdata($sess_array);
			
			/*echo "<pre>";
			print_r ($sess_array);
			
			echo "<pre>";
			print_r ($_SESSION);*/
			
		//	echo  "Session id  ".$this->session->userdata['cart_session_id'];		
			
			$product_details = $this->common_model->get_product_details($extension_product_id);
			
			$product_price_details = $this->common_model->getProductFee($extension_product_id,$ext_cur_id);
			
			/*echo "Product id  ".$product_id;
			echo "<br> Selected items ".$new_selected_values;
			echo "<br>";
			
			echo "<pre>";
			print_r($product_details);
			
			echo "<br>";
			
			echo "<pre>";
			print_r($product_price_details);*/
			
			$cart_main_insert_array = array("session_id"=>$this->session->userdata('cart_session_id'),"user_id"=>$user_id,"source"=>'extension',"item_count"=>1,"total_cart_amount"=>$product_price_details['amount'],"currency_id"=>$product_price_details['currency_id']);
			
			//$cart_main_id =1;
  		  
		    $cart_main_id = $this->common_model->insert_to_table("sales_cart_main",$cart_main_insert_array);
			$user_agent_data = array();
			
			$user_agent_data['cart_main_id']  = $cart_main_id;
			$user_agent_data['session_id'] 	= $this->session->userdata('cart_session_id');
			$user_agent_data['os'] 			= $this->agent->platform();
			$user_agent_data['browser'] 	   = $this->agent->agent_string();
			
			
			$this->common_model->insert_to_table("sales_user_agents",$user_agent_data);	
				
		/*	echo "<br> Main insert array ";
			
			echo "<pre>";
			print_r($cart_main_insert_array);*/
				
									
			$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$extension_product_id,"item_amount"=>$product_price_details['amount'],"currency"=>$product_price_details['currency_id']);
			
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
			
			
			$ebook_added_in_cart = $this->sales_model->check_product_type_exist_in_cart($cart_main_id,'ebooks');
			if(!empty($ebook_added_in_cart))
			{					
			$added_ebooks = $ebook_added_in_cart[0]->selected_item_ids;						
			$added_ebook_array = explode(',',$added_ebooks);
			}
			
				
		
			
			//$amount = $product_price_details['amount'];
				
			//sales_cart_item_details
			/*echo "Exit hererer ";
			exit;*/
		/*	$data['err_msg']= 0;
			$data['amount'] = $product_price_details['amount'];
			$data['count'] = 1;
			$data['currency_symbol'] = $currency_symbol;
			echo json_encode($data); 
			exit;     	*/
		}
		else
		{
			
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('cart_session_id'));
			foreach($cart_main_details as $cart_main)
			{
				$cart_main_id = $cart_main->id;
				$product_in_cart = $this->sales_model->check_product_exist_in_cart($cart_main->id,$extension_product_id);
				if(empty($product_in_cart))
				{
				
				$product_details = $this->common_model->get_product_details($extension_product_id);			
				$product_price_details = $this->common_model->getProductFee($extension_product_id,$ext_cur_id);
									
				$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$extension_product_id,"item_amount"=>$product_price_details['amount'],"currency"=>$product_price_details['currency_id']);
				
				$cart_items_id = $this->common_model->insert_to_table("sales_cart_items",$item_details_array);		
			
				$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>'extension',"selected_item_ids"=>$course_id);
			
				$item_id = $this->common_model->insert_to_table("sales_cart_item_details",$items_array);	
				
				$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main->id);
				
				$cart_items_total_amount=@round($cart_items_total_amount,2);
				
				
				$cart_total_items = count($this->sales_model->get_cart_items($cart_main->id));
				
				$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount);
				$this->sales_model->main_cart_details_update($this->session->userdata('cart_session_id'),$update_array);
									
				
				
					
		
			}
		}
		}
		}
		
		if($this->session->userdata('cart_session_id'))
		{
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('cart_session_id'));
			if(!empty($cart_main_details))
			{
				$cart_main_id = $cart_main_details[0]->id;		
				$ebook_added_in_cart = $this->sales_model->check_product_type_exist_in_cart($cart_main_id,'ebooks');
				if(!empty($ebook_added_in_cart))
				{					
				$added_ebooks = $ebook_added_in_cart[0]->selected_item_ids;						
				$added_ebook_array = explode(',',$added_ebooks);
				}				
				$course_added_in_cart = $this->sales_model->check_product_type_exist_in_cart($cart_main_id,'course');
				if(!empty($course_added_in_cart))
				{					
				$added_courses = $course_added_in_cart[0]->selected_item_ids;						
				$added_course_array = explode(',',$added_courses);
				}
			}
			
		}

	
	//	echo "Cart main id ".$cart_main_id;
		
		$content = array();
		$ebook_product_ids = array();
		$ebook_units = array();
		$ebook_price_details = array();
		
		
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
		
		$ebook_guide_offer_options = $this->common_model->get_product_by_type('ebook_guide');
		
		$k=0;
		foreach($ebook_guide_offer_options as $ebook_guide_det)
		{
			$ebook_guide_product_ids[$k] = $ebook_guide_det->id; 
			$ebook_guide_units[$k]  = $ebook_guide_det->units;
  			$ebook_guide_price_details[$k] = $this->common_model->getProductFee($ebook_guide_det->id,$currency_id);
						
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
		
		
		/*
		echo "<pre>";
		print_r($added_ebook_array);
		exit;
		*/
			$lang_id = $this->session->userdata('language');
					
	//	$currency_symbol 				= $currency_det[0]->currency_symbol;		
	//	$data['cur_symbol']    		 = $currency_symbol;	
	//	$content['currency_code'] 	   = $currency_code;		
	
	
		$content['ebook_array']   	     = $ebook_array;
		$content['ebook_offer_options'] = $ebook_offer_options;
		$content['ebook_product_ids']   = $ebook_product_ids;
		$content['ebook_units'] 		 = $ebook_units;
		$content['ebook_price_details'] = $ebook_price_details;
		$content['ebook_guide_price_details'] = $ebook_guide_price_details;
		
		$content['added_ebook_array']	= $added_ebook_array;
		$content['added_course_array']   = $added_course_array;
		
		$content['course_array']   	     = $course_array;		
		$content['course_offer_options'] = $course_offer_options;
		$content['course_product_ids']   = $course_product_ids;
		$content['course_units'] 		 = $course_units;
		$content['course_price_details'] = $course_price_details;
		$content['count_course_to_buy']  = count($course_array);		
		
		
		$data['lang_id'] = $lang_id;
		
		$data['translate'] 			  = $this->tr_common;
		
		
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
		$data['translate'] = $this->tr_common;
		$data['view'] = 'sales_extension';
        $data['content'] = $content;
				
        $this->load->view('user/sales_popup_template',$data);
		
		
	}
	
	function sales_extension_test($extension_product_id=NULL,$ext_cur_id=NULL,$course_id=NULL)
	{
	
			
			$this->tr_common['tr_FASHION_EBOOKS']   =$this->user_model->translate_('FASHION_EBOOKS'); 
			$this->tr_common['tr_Adviceat_your_fingertips_foranexciting_styling_career']   =$this->user_model->translate_('Adviceat_your_fingertips_foranexciting_styling_career'); 
			$this->tr_common['tr_ebook_text_sales']   =$this->user_model->translate_('ebook_text_sales'); 
			$this->tr_common['tr_ADDTOBAG']   =$this->user_model->translate_('ADDTOBAG'); 
			$this->tr_common['tr_More_options']   =$this->user_model->translate_('More_options'); 
			$this->tr_common['tr_FASHION_COURSES']   =$this->user_model->translate_('FASHION_COURSES'); 
			$this->tr_common['tr_Expandyour_styling_skillswith_extracourses']   =$this->user_model->translate_('Expandyour_styling_skillswith_extracourses'); 
			$this->tr_common['tr_course_text_sales']   =$this->user_model->translate_('course_text_sales'); 
			$this->tr_common['tr_color_wheel']   =$this->user_model->translate_('color_wheel'); 
			$this->tr_common['tr_Professional_decisions_in_an_instantwith_Trendimicolourcards']   =$this->user_model->translate_('Professional_decisions_in_an_instantwith_Trendimicolourcards'); 
			$this->tr_common['tr_color_wheel_text']   =$this->user_model->translate_('color_wheel_text'); 
			$this->tr_common['tr_total_price']   =$this->user_model->translate_('total_price'); 
			$this->tr_common['tr_remove']   =$this->user_model->translate_('remove'); 
			$this->tr_common['tr_ebook_for']   =$this->user_model->translate_('ebook_for'); 
			$this->tr_common['tr_checkout']   =$this->user_model->translate_('checkout');
			$this->tr_common['tr_select']   =$this->user_model->translate_('select');
			$this->tr_common['tr_reduced_from']   =$this->user_model->translate_('reduced_from');
			$this->tr_common['tr_course_for']   =$this->user_model->translate_('course_for');
			$this->tr_common['tr_Downloadablecopy_for']   =$this->user_model->translate_('Downloadablecopy_for');
			$this->tr_common['tr_We_thoughyoumight_likethe_following_offers']   =$this->user_model->translate_('We_thoughyoumight_likethe_following_offers');
			$this->tr_common['tr_No_courses_to_buy']   =$this->user_model->translate_('No_courses_to_buy');
			 
			 $added_ebook_array = array();
			 $added_course_array = array();
			
			$this->load->model('ebook_model');
			
			if(!$this->session->userdata('student_logged_in')){
			  redirect('home');
			}	
			$user_id = $this->session->userdata['student_logged_in']['id'];
			
			
			//echo "Product id Extension  ".$this->input->post('product_id_extension');
			
			
			if($extension_product_id != NULL)
			{
				if($this->session->userdata('extension_session_id'))
				{  
					if($this->session->userdata('extension_session_id')!=$extension_product_id)
					{
						$this->session->unset_userdata('cart_session_id');
					}
				}
			if(!$this->session->userdata('cart_session_id'))
			{	
			
				session_regenerate_id();	
				$sess_array = array('cart_session_id' => session_id()); 			
				$this->session->set_userdata($sess_array);	
				
				
				$sess_array = array('extension_session_id' => $extension_product_id); 			
				$this->session->set_userdata($sess_array);	
				
				$sess_array = array('cart_source' => '/home/sales_extension/');
				$this->session->set_userdata($sess_array);
				
				/*echo "<pre>";
				print_r ($sess_array);
				
				echo "<pre>";
				print_r ($_SESSION);*/
				
			//	echo  "Session id  ".$this->session->userdata['cart_session_id'];		
				
				$product_details = $this->common_model->get_product_details($extension_product_id);
				
				$product_price_details = $this->common_model->getProductFee($extension_product_id,$ext_cur_id);
				
				/*echo "Product id  ".$product_id;
				echo "<br> Selected items ".$new_selected_values;
				echo "<br>";
				
				echo "<pre>";
				print_r($product_details);
				
				echo "<br>";
				
				echo "<pre>";
				print_r($product_price_details);*/
				
				$cart_main_insert_array = array("session_id"=>$this->session->userdata('cart_session_id'),"user_id"=>$user_id,"source"=>'extension',"item_count"=>1,"total_cart_amount"=>$product_price_details['amount'],"currency_id"=>$product_price_details['currency_id']);
				
				//$cart_main_id =1;
			  
				$cart_main_id = $this->common_model->insert_to_table("sales_cart_main",$cart_main_insert_array);
				$user_agent_data = array();
				
				$user_agent_data['cart_main_id']  = $cart_main_id;
				$user_agent_data['session_id'] 	= $this->session->userdata('cart_session_id');
				$user_agent_data['os'] 			= $this->agent->platform();
				$user_agent_data['browser'] 	   = $this->agent->agent_string();
				
				
				$this->common_model->insert_to_table("sales_user_agents",$user_agent_data);	
					
			/*	echo "<br> Main insert array ";
				
				echo "<pre>";
				print_r($cart_main_insert_array);*/
					
										
				$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$extension_product_id,"item_amount"=>$product_price_details['amount'],"currency"=>$product_price_details['currency_id']);
				
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
				
				
				$ebook_added_in_cart = $this->sales_model->check_product_type_exist_in_cart($cart_main_id,'ebooks');
				if(!empty($ebook_added_in_cart))
				{					
				$added_ebooks = $ebook_added_in_cart[0]->selected_item_ids;						
				$added_ebook_array = explode(',',$added_ebooks);
				}
				
					
			
				
				//$amount = $product_price_details['amount'];
					
				//sales_cart_item_details
				/*echo "Exit hererer ";
				exit;*/
			/*	$data['err_msg']= 0;
				$data['amount'] = $product_price_details['amount'];
				$data['count'] = 1;
				$data['currency_symbol'] = $currency_symbol;
				echo json_encode($data); 
				exit;     	*/
			}
			else
			{
				
				$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('cart_session_id'));
				foreach($cart_main_details as $cart_main)
				{
					$cart_main_id = $cart_main->id;
					$product_in_cart = $this->sales_model->check_product_exist_in_cart($cart_main->id,$extension_product_id);
					if(empty($product_in_cart))
					{
					
					$product_details = $this->common_model->get_product_details($extension_product_id);			
					$product_price_details = $this->common_model->getProductFee($extension_product_id,$ext_cur_id);
										
					$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$extension_product_id,"item_amount"=>$product_price_details['amount'],"currency"=>$product_price_details['currency_id']);
					
					$cart_items_id = $this->common_model->insert_to_table("sales_cart_items",$item_details_array);		
				
					$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>'extension',"selected_item_ids"=>$course_id);
				
					$item_id = $this->common_model->insert_to_table("sales_cart_item_details",$items_array);	
					
					$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main->id);
					
					$cart_items_total_amount=@round($cart_items_total_amount,2);
					
					
					$cart_total_items = count($this->sales_model->get_cart_items($cart_main->id));
					
					$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount);
					$this->sales_model->main_cart_details_update($this->session->userdata('cart_session_id'),$update_array);
										
					
					
						
			
				}
			}
			}
			}
			
			if($this->session->userdata('cart_session_id'))
			{
				$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('cart_session_id'));
				if(!empty($cart_main_details))
				{
					$cart_main_id = $cart_main_details[0]->id;		
					$ebook_added_in_cart = $this->sales_model->check_product_type_exist_in_cart($cart_main_id,'ebooks');
					if(!empty($ebook_added_in_cart))
					{					
					$added_ebooks = $ebook_added_in_cart[0]->selected_item_ids;						
					$added_ebook_array = explode(',',$added_ebooks);
					}				
					$course_added_in_cart = $this->sales_model->check_product_type_exist_in_cart($cart_main_id,'course');
					if(!empty($course_added_in_cart))
					{					
					$added_courses = $course_added_in_cart[0]->selected_item_ids;						
					$added_course_array = explode(',',$added_courses);
					}
				}
				
			}
	
		
		//	echo "Cart main id ".$cart_main_id;
			
			$content = array();
			$ebook_product_ids = array();
			$ebook_units = array();
			$ebook_price_details = array();
			
			
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
			
			$ebook_guide_offer_options = $this->common_model->get_product_by_type('ebook_guide');
			
			$k=0;
			foreach($ebook_guide_offer_options as $ebook_guide_det)
			{
				$ebook_guide_product_ids[$k] = $ebook_guide_det->id; 
				$ebook_guide_units[$k]  = $ebook_guide_det->units;
				$ebook_guide_price_details[$k] = $this->common_model->getProductFee($ebook_guide_det->id,$currency_id);
							
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
			
			
			echo "Extension Currency id ".$ext_cur_id;
			echo "<br>Currency id ".$currency_id;
			echo "<pre>";
			print_r($course_offer_options);
			print_r($course_product_ids);
			print_r($course_price_details);
			exit;
			
				
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
			
			
			/*
			echo "<pre>";
			print_r($added_ebook_array);
			exit;
			*/
				$lang_id = $this->session->userdata('language');
						
		//	$currency_symbol 				= $currency_det[0]->currency_symbol;		
		//	$data['cur_symbol']    		 = $currency_symbol;	
		//	$content['currency_code'] 	   = $currency_code;		
		
		
			$content['ebook_array']   	     = $ebook_array;
			$content['ebook_offer_options'] = $ebook_offer_options;
			$content['ebook_product_ids']   = $ebook_product_ids;
			$content['ebook_units'] 		 = $ebook_units;
			$content['ebook_price_details'] = $ebook_price_details;
			$content['ebook_guide_price_details'] = $ebook_guide_price_details;
			
			$content['added_ebook_array']	= $added_ebook_array;
			$content['added_course_array']   = $added_course_array;
			
			$content['course_array']   	     = $course_array;		
			$content['course_offer_options'] = $course_offer_options;
			$content['course_product_ids']   = $course_product_ids;
			$content['course_units'] 		 = $course_units;
			$content['course_price_details'] = $course_price_details;
			$content['count_course_to_buy']  = count($course_array);		
			
			
			$data['lang_id'] = $lang_id;
			
			$data['translate'] 			  = $this->tr_common;
			
			
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
			$data['translate'] = $this->tr_common;
			$data['view'] = 'sales_extension';
			$data['content'] = $content;
					
			$this->load->view('user/sales_popup_template',$data);
			
			
		
	}
	
	function sales_certificates($cert_product_id=NULL)
	{
		
				
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
			$student_update_data['country_id'] = $country  = $this->input->post('country');
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
			
			$user_agent_data = array();
			
			$user_agent_data['cart_main_id']  = $cart_main_id;
			$user_agent_data['session_id'] 	= $this->session->userdata('cart_session_id');
			$user_agent_data['os'] 			= $this->agent->platform();
			$user_agent_data['browser'] 	   = $this->agent->agent_string();
			
			
			$this->common_model->insert_to_table("sales_user_agents",$user_agent_data);	
				
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
		
		$data['view'] = 'sales_certificates';
        $data['content'] = $content;
				
        $this->load->view('user/pop_up_template',$data);
		
		
	
		
		
	}
	
	
	function sales_course_completion($course_id=NULL)
	{
		
		$this->load->model('pdf_html_model','',TRUE);
		$course_progress_array = $this->get_student_progress($course_id);
		
		
		$this->tr_common['tr_FASHION_EBOOKS']   =$this->user_model->translate_('FASHION_EBOOKS'); 
		$this->tr_common['tr_Adviceat_your_fingertips_foranexciting_styling_career']   =$this->user_model->translate_('Adviceat_your_fingertips_foranexciting_styling_career'); 
		$this->tr_common['tr_ebook_text_sales']   =$this->user_model->translate_('ebook_text_sales'); 
		$this->tr_common['tr_ADDTOBAG']   =$this->user_model->translate_('ADDTOBAG'); 
		$this->tr_common['tr_More_options']   =$this->user_model->translate_('More_options'); 
		$this->tr_common['tr_FASHION_COURSES']   =$this->user_model->translate_('FASHION_COURSES'); 
		$this->tr_common['tr_Expandyour_styling_skillswith_extracourses']   =$this->user_model->translate_('Expandyour_styling_skillswith_extracourses'); 
		$this->tr_common['tr_course_text_sales']   =$this->user_model->translate_('course_text_sales'); 
		$this->tr_common['tr_color_wheel']   =$this->user_model->translate_('color_wheel'); 
		$this->tr_common['tr_Professional_decisions_in_an_instantwith_Trendimicolourcards']   =$this->user_model->translate_('Professional_decisions_in_an_instantwith_Trendimicolourcards'); 
		$this->tr_common['tr_color_wheel_text']   =$this->user_model->translate_('color_wheel_text'); 
		$this->tr_common['tr_total_price']   =$this->user_model->translate_('total_price'); 
		$this->tr_common['tr_remove']   =$this->user_model->translate_('remove'); 
		$this->tr_common['tr_ebook_for']   =$this->user_model->translate_('ebook_for'); 
		$this->tr_common['tr_checkout']   =$this->user_model->translate_('checkout');
		$this->tr_common['tr_select']   =$this->user_model->translate_('select');
		$this->tr_common['tr_reduced_from']   =$this->user_model->translate_('reduced_from');
		$this->tr_common['tr_course_for']   =$this->user_model->translate_('course_for');
		$this->tr_common['tr_Downloadablecopy_for']   =$this->user_model->translate_('Downloadablecopy_for');
		$this->tr_common['tr_We_thoughyoumight_likethe_following_offers']   =$this->user_model->translate_('We_thoughyoumight_likethe_following_offers');
		$this->tr_common['tr_No_courses_to_buy']   =$this->user_model->translate_('No_courses_to_buy');
				
		
		$this->tr_common['tr_icoes_certificate']   				 = $this->user_model->translate_('icoes_certificate');	
		$this->tr_common['tr_sales_pop_icoes_head']   			  = $this->user_model->translate_('sales_pop_icoes_head');
		$this->tr_common['tr_sales_pop_icoes_txt']   			   = $this->user_model->translate_('sales_pop_icoes_txt');
		$this->tr_common['tr_sales_pop_course_subscription']     = $this->user_model->translate_('sales_pop_course_subscription');
		$this->tr_common['tr_sales_pop_course_subscription_head']= $this->user_model->translate_('sales_pop_course_subscription_head');
		$this->tr_common['tr_sales_pop_course_subscription_txt'] = $this->user_model->translate_('sales_pop_course_subscription_txt');
		$this->tr_common['tr_Proof_of_Completion']   			   = $this->user_model->translate_('Proof_of_Completion');
		$this->tr_common['tr_sales_pop_proof_completion_head']   = $this->user_model->translate_('sales_pop_proof_completion_head');
		$this->tr_common['tr_sales_pop_proof_completion_txt']    = $this->user_model->translate_('sales_pop_proof_completion_txt');
		$this->tr_common['tr_sales_pop_up_etranscript']   		  = $this->user_model->translate_('sales_pop_up_etranscript');		
		$this->tr_common['tr_sales_pop_etranscript_head']   		= $this->user_model->translate_('sales_pop_etranscript_head');
		$this->tr_common['tr_sales_pop_etranscript_txt']   		 = $this->user_model->translate_('sales_pop_etranscript_txt');
		$this->tr_common['tr_sales_pop_no_thanks']   			   = $this->user_model->translate_('sales_pop_no_thanks');
		$this->tr_common['tr_hard_copy_for']   			   		 = $this->user_model->translate_('hard_copy_for');
		$this->tr_common['tr_sales_pop_please_apply_cert_msg']   = $this->user_model->translate_('sales_pop_please_apply_cert_msg');
		$this->tr_common['tr_not_happy_with_certificate']   		= $this->user_model->translate_('not_happy_with_certificate');		
		$this->tr_common['tr_happy_with_proof_letter']   		   = $this->user_model->translate_('happy_with_proof_letter');
		$this->tr_common['tr_see_proof_letter_below']   		    = $this->user_model->translate_('see_proof_letter_below');
		$this->tr_common['tr_happy_with_certificate']   			= $this->user_model->translate_('happy_with_certificate');
		$this->tr_common['tr_see_certficate_below']   			  = $this->user_model->translate_('see_certficate_below');
		$this->tr_common['tr_pdf_included']   			 		  = $this->user_model->translate_('pdf_included');		
		$this->tr_common['tr_months']   			  				= $this->user_model->translate_('months');
		$this->tr_common['tr_certificate'] 					   = $this->user_model->translate_('certificate');
		
		$this->tr_common['tr_you_must_pass_to_apply_certificate']= $this->user_model->translate_('You_must_pass_the_course_before_you_can');
		
		
		
				if(isset($_POST['currency_id']))
				{
					$cert_cur_id = $_POST['currency_id'];				
				}
				
				if($course_id!=NULL)
				{
					$sess_array = array('cart_course_id' => $course_id); 			
					$this->session->set_userdata($sess_array);
				}
				else
				{
					
				}
		
		//echo "Session Course id ".$this->session->userdata('cart_course_id');
		$this->load->model('ebook_model');
		
		if(!$this->session->userdata('student_logged_in')){
		  redirect('home');
		}	
		$user_id = $this->session->userdata['student_logged_in']['id'];
		
		
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
		
		$ebook_guide_offer_options = $this->common_model->get_product_by_type('ebook_guide');
		
		$k=0;
		foreach($ebook_guide_offer_options as $ebook_guide_det)
		{
			$ebook_guide_product_ids[$k] = $ebook_guide_det->id; 
			$ebook_guide_units[$k]  = $ebook_guide_det->units;
  			$ebook_guide_price_details[$k] = $this->common_model->getProductFee($ebook_guide_det->id,$currency_id);
						
			$k++;
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
		$cousre_subscruption = $this->common_model->get_product_by_type('access');
		
		
		$cousre_material_subscription = $this->common_model->get_product_by_type('access');
		
		$q =0;	
		foreach($cousre_material_subscription as $access_data)
		{
			//$material_sub_product_id[$q]  	  = $access_data->id; 			
  			$material_sub_fee_deatils[$q] 	 = $this->common_model->getProductFee($access_data->id,$currency_id);				
			$q++;	
		}
		
		
		
		$q =0;	
		foreach($cousre_material_subscription as $access_data)
		{
			//$material_sub_product_id[$q]  	  = $access_data->id; 			
  			$material_sub_fee_deatils[$q] 	 = $this->common_model->getProductFee($access_data->id,$currency_id);				
			$q++;	
		}
		
		
		$postage_options = $this->certificate_model->get_postage_options();
		
		/*echo "<pre>";
		print_r($postage_options);
		exit;*/
		$icoes_product_id = array();
		
		$q =0;
		foreach($postage_options as $icoes_post)
		{
			//$postage_details = $this->certificate_model->get_postage_details(1); 
			
			$product_id = $this->common_model->getProdectId('hardcopy',$icoes_post->id);
			$postage_amount[$q] = $this->common_model->getProductFee($product_id,$currency_id);
			$icoes_product_id[$q] = $product_id;
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
		
		
		
		
		
		$course_name = $this->common_model->get_course_name($course_id); 
		$stud_details=$this->user_model->get_stud_details($user_id);	
		 
		  foreach($stud_details as $val2)
		  {
			  $certificate_user_name = $val2->first_name.' '.$val2->last_name;
			  $user_first_name = trim($val2->first_name);		
			 $user_country_name = $this->user_model->get_country_name($val2->country_id);
			 $user_house_number = $val2->house_number;
			 $user_address = $val2->address;
			 $user_city = $val2->city;
			 $user_zip_code = $val2->zipcode;
			 $user_mail = $val2->email;
			 
		  }		
	 
    	$certificate_user_name = strtolower($certificate_user_name);
		$certificate_user_name = ucwords($certificate_user_name);
		
		$user_first_name = strtolower($user_first_name);
		$user_first_name = ucfirst($user_first_name);
		
		
		
		
		
		/* -------------  Pop up certificate details -------------*/
		
		/*$hard_copy_prepaid = 0;
		$pre_purchased_icoes_certificate = $this->user_model->get_pre_purchased_product_status($user_id,$course_id);
				if(!empty($pre_purchased_icoes_certificate))
				{
				$hard_copy_applied_status = explode(',',$pre_purchased_icoes_certificate[0]->offer_extra_products);
				
				$hard_copy_prepaid_offer_id[$k]= $pre_purchased_icoes_certificate[0]->offer_id;			
				
				
				if(in_array('20',$hard_copy_applied_status))
				{
					$hard_copy_prepaid = 1;
				}
				}*/
		
		
	 $lang_id = $this->session->userdata('language');
		
	$course_passed = 1;
	
	 $mark_details = $this->get_student_progress($course_id);
	 
	 
	 
	// $mark_details = $this->get_student_progress($value->course_id); 
	

	 
	 
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
		
		
		$content['certificate_html'] = '';
		
		if(!empty($certificate_details))
		{
		   $data['certificate_applied'] = 1;			
		   if($this->session->userdata['ip_address'] == '117.206.30.81')
		   {
			 $certificate_html = $this->pdf_html_model->create_icoes_certificate_html($course_id);
		   }
		   else		   
		   {		
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
		
		
				if($course_id==1)
				{		
					$coursename= 'Event and Hospitality Management';
				}
				else if( $course_id==2 )
				{ 	
				$coursename = 'Wedding Planner';
				}
				
				else if($course_id==3 )
				{				
					$coursename='Starting your own business Course';
				}
				else if($course_id==4 )
				{		
						
					$coursename='Starting your own business Curso';
				}
				/* End Starting your own business course */
				
				/* Marketing your business course */
		
				else if($lang_id==4 && $course_id==9 )
				{		
					$courseTitle = 'Marketing your business';	
					$coursename='Marketing your business Course';
				}
				else if($lang_id==3 && $course_id==19 )
				{		
					$courseTitle = 'Marketing your business';		
					$coursename='Marketing your business Curso';
				}
				/* End Marketing your business course */
				
				//$cssLink = base_url();
				if($lang_id==3)
				{
					$cssLink = base_url()."public/certificate/icoes_certificate/icoes_certificate_spanish.css";
				}
				else if($lang_id==6)
				{
					$cssLink = base_url()."public/certificate/icoes_certificate/icoes_certificate_spanish.css";
				}
				else if($lang_id==4)
				{
					$cssLink = base_url()."public/certificate/icoes_certificate/icoes_certificate.css";
				}
		
		$certificate_html = '<div class="outer">
<div class="innnr">
<div style="clear:both"></div>
<h2 class="name">'.$certificate_user_name.'</h2>
<h3 class="for">'.$this->user_model->translate_('for_success_completion').'</h3>
<h2 class="course">'. $coursename.'</h2>
<h3 class="with">'.$this->user_model->translate_('course_with').' <span> EventTrix</span> </h3>
<p class="top"><span>'.$this->user_model->translate_('icoes_grade').':</span> ' .$grade.'</p>
<p><span>'.$this->user_model->translate_('date_of_completion').':</span> '.$month.' '.$year.'</h4>
<p><span>'.$this->user_model->translate_('cert_no').':</span> 100-'.$certificate_id.'</p>
</div></div>
';
		   }
		   $content['certificate_html'] = $certificate_html;		
		}		
		else
		{
			$data['certificate_applied'] = 0;
		}
		
		 if($this->session->userdata['ip_address'] == '117.206.30.81')
		 {
			$proof_completion_html = $this->pdf_html_model->create_proof_completion_html($course_id);
		 }
		 else
		 {
				  
		  $lang_id  = $this->common_model->get_user_lang_id($user_id); 
		 
		
		  if($lang_id == 3)
		  {
			   setlocale(LC_TIME, 'es_ES');
		  }
		  elseif($lang_id == 4)
		  {
			  setlocale(LC_TIME, 'en_EN');
		  }
		  elseif($lang_id == 6)
		  {
			   setlocale(LC_TIME, 'fr_FR');
		  }
		  
		  $slNo=0;
		  
		  $course_hours  = $this->user_model->get_course_hours($course_id);
		  
		 // $stud_details=$this->user_model->get_stud_details($user_id);
		  
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
		  
		  $certficate_details = $this->user_model->get_certficate_details($user_id,$course_id);
		  if(!empty($certficate_details))
		  {
			   $completed_date_date = $certficate_details['applied_on'];
		  }
		  else
		  {
			  $completed_date_date = date("Y-m-d");
		  }
		  
		  $course_completed_date = explode('-',$completed_date_date);
		  
		  $completed_year  = $course_completed_date[0];
		 // $completed_month = $course_completed_date[1];
		  $completed_date  = $course_completed_date[2];
		 		  
		  $month_name = ucwords(strftime('%B',strtotime($completed_date_date)));
		  
		
		  $date_suffix = date("S",strtotime($completed_date_date));
		  
		  $module_list = $this->user_model->check_user_registered($user_id,$course_id);
		  
		  foreach($module_list as $unit)
		  {
			  $modules = unserialize($unit->student_course_units);
		  }
					
		  $module_count = count($modules);
		 		
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
			  $coursename = 'Starting Your Business';
			  $course_topics = 'Topics covered by the course include: Market research and competitors analysis, funding and available help, Introduction to marketing, Business structures, legislation and regulations, registering your business, Budget and cash flows, accounting and finance, Insurance, premises, suppliers, staff, Home based businesses, Business plan, Launching your business';
		  }
		    elseif($course_id == 4)
		  {
			  $coursename = 'Marketing Your Business';
			  $course_topics = 'Topics covered by the course include: Introduction to marketing, Marketing plan, Low cost marketing techniques, Developing your brand, Setting up and managing website, Social media and online marketing, Public relations and advertising, Sales campaigns and leads generation';
		  }
			 
		
		//  $cssLink = "http://trendimi.net/public/user/css/proof_letters.css";
		
		if($lang_id==4)
		{
		  
		  $proof_completion_html = '		
		  <style>
<style type="text/css">
/* BEGIN Light */
@font-face {
  font-family: "Open Sans";
  src: url("fonts/Light/OpenSans-Light.eot");
  src: url("fonts/Light/OpenSans-Light.eot?#iefix") format("embedded-opentype"),
       url("fonts/Light/OpenSans-Light.woff") format("woff"),
       url("fonts/Light/OpenSans-Light.ttf") format("truetype"),
       url("fonts/Light/OpenSans-Light.svg#OpenSansLight") format("svg");
  font-weight: 300;
  font-style: normal;
}
/* END Light */


/* BEGIN Regular */
@font-face {
  font-family: "Open Sans";
  src: url("fonts/Regular/OpenSans-Regular.eot");
  src: url("fonts/Regular/OpenSans-Regular.eot?#iefix") format("embedded-opentype"),
       url("fonts/Regular/OpenSans-Regular.woff") format("woff"),
       url("fonts/Regular/OpenSans-Regular.ttf") format("truetype"),
       url("fonts/Regular/OpenSans-Regular.svg#OpenSansRegular") format("svg");
  font-weight: normal;
  font-style: normal;
}
/* END Regular */

/* BEGIN Italic */
@font-face {
  font-family: "Open Sans";
  src: url("fonts/Italic/OpenSans-Italic.eot");
  src: url("fonts/Italic/OpenSans-Italic.eot?#iefix") format("embedded-opentype"),
       url("fonts/Italic/OpenSans-Italic.woff") format("woff"),
       url("fonts/Italic/OpenSans-Italic.ttf") format("truetype"),
       url("fonts/Italic/OpenSans-Italic.svg#OpenSansItalic") format("svg");
  font-weight: normal;
  font-style: italic;
}
/* END Italic */

/* BEGIN Semibold */
@font-face {
  font-family: "Open Sans";
  src: url("fonts/Semibold/OpenSans-Semibold.eot");
  src: url("fonts/Semibold/OpenSans-Semibold.eot?#iefix") format("embedded-opentype"),
       url("fonts/Semibold/OpenSans-Semibold.woff") format("woff"),
       url("fonts/Semibold/OpenSans-Semibold.ttf") format("truetype"),
       url("fonts/Semibold/OpenSans-Semibold.svg#OpenSansSemibold") format("svg");
  font-weight: 600;
  font-style: normal;
}
/* END Semibold */

/* BEGIN Semibold Italic */
@font-face {
  font-family: "Open Sans";
  src: url("fonts/SemiboldItalic/OpenSans-SemiboldItalic.eot");
  src: url("fonts/SemiboldItalic/OpenSans-SemiboldItalic.eot?#iefix") format("embedded-opentype"),
       url("fonts/SemiboldItalic/OpenSans-SemiboldItalic.woff") format("woff"),
       url("fonts/SemiboldItalic/OpenSans-SemiboldItalic.ttf") format("truetype"),
       url("fonts/SemiboldItalic/OpenSans-SemiboldItalic.svg#OpenSansSemiboldItalic") format("svg");
  font-weight: 600;
  font-style: italic;
}
/* END Semibold Italic */

/* BEGIN Bold */
@font-face {
  font-family: "Open Sans";
  src: url("fonts/Bold/OpenSans-Bold.eot");
  src: url("fonts/Bold/OpenSans-Bold.eot?#iefix") format("embedded-opentype"),
       url("fonts/Bold/OpenSans-Bold.woff") format("woff"),
       url("fonts/Bold/OpenSans-Bold.ttf") format("truetype"),
       url("fonts/Bold/OpenSans-Bold.svg#OpenSansBold") format("svg");
  font-weight: bold;
  font-style: normal;
}
/* END Bold */

.outer_completion{margin:0 auto; background:url(/public/user/certificate/images/Eventtrix-Proof-of-Enrolement.jpg) center center; width:820px; height:959px}
.innner_completion{padding:12.5em 3em 2em 4em;}
.innner_completion p{padding:0.4em 0.5em; font-size:11pt; margin:0}
.innner_completion ul{margin:0; padding:0.8em 0.7em 0.3em 1.5em; font-size:11pt;}
.innner_completion ul li{padding:0.2em; text-align:left !important; list-style:disc !important}
</style>

<div class="outer_completion">
<div class="innner_completion">
<p>To whom it may concern,</p>
<p>We confirm that, on '.$completed_date.' '.$month_name.' '.$completed_year.', '.$certificate_user_name.' successfully completed our '.$course_name.' online
learning course. '.$certificate_user_name.' graduated with a '.$grade.'  grade.</p>

<p>The course consists '.$course_hours.' online study hours and includes study content, exercises and exams.
Topics covered by the course include:</p>
<ul>
<li>Principles of Event Management & Roles of Event Manager</li>
<li>Types of Events</li>
<li>Working with Clients incl. understanding client needs, preparing event proposals,
signing contracts</li>
<li>Steps for planning an Event incl. budgets, venues, food and beverages,
transportation, speakers</li>
<li>General Etiquette and Protocol incl. invitations, dress codes, table settings and
seating arrangements, greeting etiquette</li>
<li>Day of the Event and Post Event Evaluation</li>
</ul>

<p>We congratulate '.$user_first_name.' on completing '.$course_name.' course and wish her every
success in her future career.</p>
<p>Kind Regards,
</div>
</div>
';
		}
	
		}
		
		$content['proof_completion_html'] = $proof_completion_html;
						
		//$content['hard_copy_prepaid']   = $hard_copy_prepaid;
		$content['course_passed']	   = $course_passed;
		$content['ebook_array']   	     = $ebook_array;
		$content['ebook_offer_options'] = $ebook_offer_options;
		$content['ebook_product_ids']   = $ebook_product_ids;
		$content['ebook_units'] 		 = $ebook_units;
		$content['ebook_price_details'] = $ebook_price_details;
		$content['ebook_guide_price_details'] = $ebook_guide_price_details;
		
		$content['course_array']   	     = $course_array;		
		$content['course_offer_options'] = $course_offer_options;
		$content['course_product_ids']   = $course_product_ids;
		$content['course_units'] 		 = $course_units;
		$content['course_price_details'] = $course_price_details;
		$content['count_course_to_buy']  = count($course_array);
				
		$content['proof_of_completion_soft_id']    = $proof_of_completion_soft_id;
		$content['proof_of_completion_soft_price'] = $proof_of_completion_soft_price;		
		
		$content['cousre_material_subscription']    = $cousre_material_subscription;
		$content['material_sub_fee_deatils'] = $material_sub_fee_deatils;		
		
		$content['proof_of_completion_hard_id']    = $proof_of_completion_hard_id;
		$content['proof_of_completion_hard_price'] = $proof_of_completion_hard_price;
		
		$content['e_transcript_soft_id']    = $e_transcript_soft_id;
		$content['e_transcript_soft_price'] = $e_transcript_soft_price;
		
		$content['icoes_postage_options'] = $postage_options;
		$content['icoes_postage_amount']  = $postage_amount;
		$content['icoes_product_id']  = $icoes_product_id;
		
		$content['course_id'] = $course_id;
		
		$content['course_progress_array'] = $course_progress_array;
				
		$data['translate'] 	  = $this->tr_common;
		$data['lang_id'] = $lang_id;
		
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
		$sess_array = array('cart_source' => '/home/sales_course_completion/'.$course_id.'');
		$this->session->set_userdata($sess_array);			
			
		$added_ebook_array = array();
		$added_course_array = array();
			
		if($this->session->userdata('cart_session_id'))
		{
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('cart_session_id'));
			if(!empty($cart_main_details))
			{
				$cart_main_id = $cart_main_details[0]->id;		
				$ebook_added_in_cart = $this->sales_model->check_product_type_exist_in_cart($cart_main_id,'ebooks');
				if(!empty($ebook_added_in_cart))
				{					
				$added_ebooks = $ebook_added_in_cart[0]->selected_item_ids;						
				$added_ebook_array = explode(',',$added_ebooks);
				}				
				$course_added_in_cart = $this->sales_model->check_product_type_exist_in_cart($cart_main_id,'course');
				if(!empty($course_added_in_cart))
				{					
				$added_courses = $course_added_in_cart[0]->selected_item_ids;						
				$added_course_array = explode(',',$added_courses);
				}
			}
			
		}
		$content['added_ebook_array']	= $added_ebook_array;
		$content['added_course_array']   = $added_course_array;
				
		$sess_array = array('apply_certificate' => 'true');
		$this->session->set_userdata($sess_array);
		
		$data['sales_from'] = 'sales_course_completion';	
		
		$data['view'] = 'sales_course_completion';
        $data['content'] = $content;				
        $this->load->view('user/sales_popup_template',$data);	
	}
		
	function get_product_details($product_id,$currency_id)
	{
		
			$content = array();
			$data =array();
			$price_details_array = array();
			$currency_id = $this->currId;
		//	$currency_code = $this->currencyCode;
			
		//	$price_details = $this->certificate_model->get_postage_amount($product_id,$currency_id);			
		//	$currency_det = $this->user_model->get_curr_symbol($currency_code);
			
			
			
		if($this->session->userdata('cart_session_id'))	
		{
			//echo $this->session->userdata('cart_session_id');
			
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('cart_session_id'));
			foreach($cart_main_details as $cart_main)
			{
				$cart_main_id = $cart_main->id;
				$product_in_cart = $this->sales_model->check_product_exist_in_cart($cart_main->id,$product_id);
				if(empty($product_in_cart))
				{
					$price_details_array = $this->common_model->getProductFee($product_id,$currency_id);
				}
				else
				{
					/*echo "<pre>";
					print_r($product_in_cart);*/
					
					
					foreach($product_in_cart as $prod_cart)
					{
						$product_cart_details = $this->sales_model->get_cart_items_details($cart_main_id,$prod_cart->id);				
					}
					
					/*echo "<pre>";
					print_r($product_cart_details);*/
					
					$selected_items_array = explode(',',$product_cart_details[0]->selected_item_ids);
					
					$data['product_id'] = $product_id;
					$data['selected_item_ids']= $selected_items_array;
					$data['product_exists_in_cart']= 1;
					
					echo json_encode($data);  
					exit;
				}
			}
		}
		else
		{			
			$price_details_array = $this->common_model->getProductFee($product_id,$currency_id);
		}
	
	/*echo "<pre>";
	print_r($price_details_array);
	exit;*/
	
	/*[fake_amount] => 0
    [amount] => 309
    [currency_symbol] => £
    [currency_code] => GBP
    [currency_id] => 2*/
	$data['product_id'] = $product_id;
	foreach($price_details_array as $price_det)
	{
		$data['product_exists_in_cart']= 0;
		$data['amount']= $price_details_array['amount'];
		$data['fake_amount']  = $price_details_array['fake_amount'];	
		$data['currency_symbol']= $price_details_array['currency_symbol'];
		$data['currency_code']=  $price_details_array['currency_code'];
		$data['currency_id']=  $price_details_array['currency_id'];
		
	}
			
			
			
			
		//	$currency_symbol = $currency_det[0]->currency_symbol;
			
		/*	
			$data['cur_symbol']    = $content['currency_symbol'];
			$data['currency_code'] = $content['currency_code'];
			
			 foreach ($price_details as $value) {	
					$data['amount']  = $value->amount;	
					$data['fake_amount']  = $value->fake_amount;	
							
				}*/
			echo json_encode($data);  
			exit;
		
		
	}
	
	function add_item_to_cart($product_id,$selected_values,$currency_id,$course_id_required,$source)
	{
		if(!$this->session->userdata('student_logged_in')){
		  redirect('home');
		}	
		$new_selected_values = '';	
		$selected_values_array = explode("+",$selected_values);
		/*echo "<pre>";
		print_r($selected_values_array);*/
		for($j=0;$j<count($selected_values_array);$j++)
		{
			if($j==0)
			{
				$new_selected_values = $selected_values_array[$j];
			}
			else
			{
				$new_selected_values .=','.$selected_values_array[$j];
			}
		}
		
		
		if($course_id_required == '1')
		{
			if($this->session->userdata('cart_course_id'))
			{
				$new_selected_values = $this->session->userdata('cart_course_id');
			}
			else
			{
				$new_selected_values = $selected_values;
			}
			
		}
		
	
		
		$user_id = $this->session->userdata['student_logged_in']['id'];
		
		//$currency_id = $this->currId;
		//$currency_code = $this->currencyCode;	
			
				
		if(!$this->session->userdata('cart_session_id'))
		{		
			$sess_array = array('cart_session_id' => session_id()); 
			
			$this->session->set_userdata($sess_array);	
			
			/*echo "<pre>";
			print_r ($sess_array);
			
			echo "<pre>";
			print_r ($_SESSION);*/
			
		//	echo  "Session id  ".$this->session->userdata['cart_session_id'];		
			
			$product_details = $this->common_model->get_product_details($product_id);
			
			$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
			
			/*echo "Product id  ".$product_id;
			echo "<br> Selected items ".$new_selected_values;
			echo "<br>";
			
			echo "<pre>";
			print_r($product_details);
			
			echo "<br>";
			
			echo "<pre>";
			print_r($product_price_details);*/
			
			$cart_main_insert_array = array("session_id"=>$this->session->userdata('cart_session_id'),"user_id"=>$user_id,"source"=>$source,"item_count"=>1,"total_cart_amount"=>$product_price_details['amount'],"currency_id"=>$product_price_details['currency_id']);
			
			//$cart_main_id =1;
  		  
		    $cart_main_id = $this->common_model->insert_to_table("sales_cart_main",$cart_main_insert_array);
			
			$user_agent_data = array();
			
			$user_agent_data['cart_main_id']  = $cart_main_id;
			$user_agent_data['session_id'] 	= $this->session->userdata('cart_session_id');
			$user_agent_data['os'] 			= $this->agent->platform();
			$user_agent_data['browser'] 	   = $this->agent->agent_string();
			
			
			$this->common_model->insert_to_table("sales_user_agents",$user_agent_data);
				
		/*	echo "<br> Main insert array ";
			
			echo "<pre>";
			print_r($cart_main_insert_array);*/
				
									
			$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$product_id,"item_amount"=>$product_price_details['amount'],"currency"=>$product_price_details['currency_id']);
			
			/*echo "<br> Item details array ";
			
			echo "<pre>";
			print_r($item_details_array);*/
			//$cart_items_id =10;
			
			$cart_items_id = $this->common_model->insert_to_table("sales_cart_items",$item_details_array);		
			
			$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>$product_details[0]->type,"selected_item_ids"=>$new_selected_values);
			
			/*echo "<br> Items array ";
			
			echo "<pre>";
			print_r($items_array);*/
			
			$this->session->unset_userdata('cart_session_type');			
			$sess_array = array('cart_session_type' => $product_details[0]->type);			
			$this->session->set_userdata($sess_array);
			
			$item_id = $this->common_model->insert_to_table("sales_cart_item_details",$items_array);	
			
			$currency_symbol = $this->common_model->get_currency_symbol_from_id($product_price_details['currency_id']);
			
			//$amount = $product_price_details['amount'];
				
			//sales_cart_item_details
			/*echo "Exit hererer ";
			exit;*/
			$data['err_msg']= 0;
			$data['amount'] = $product_price_details['amount'];
			$data['count'] = 1;
			$data['currency_symbol'] = $currency_symbol;
			echo json_encode($data); 
			exit;     	
		}
		else
		{
			//echo $this->session->userdata('cart_session_id');
			
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('cart_session_id'));
			foreach($cart_main_details as $cart_main)
			{
				$cart_main_id = $cart_main->id;
				$product_in_cart = $this->sales_model->check_product_exist_in_cart($cart_main->id,$product_id);
				if(empty($product_in_cart))
				{
				
				$product_details = $this->common_model->get_product_details($product_id);			
				$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
									
				$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$product_id,"item_amount"=>$product_price_details['amount'],"currency"=>$product_price_details['currency_id']);
				
				$cart_items_id = $this->common_model->insert_to_table("sales_cart_items",$item_details_array);		
			
				$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>$product_details[0]->type,"selected_item_ids"=>$new_selected_values);
			
				$item_id = $this->common_model->insert_to_table("sales_cart_item_details",$items_array);	
				
				$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main->id);
				
				$cart_items_total_amount=@round($cart_items_total_amount,2);
				
				
				$cart_total_items = count($this->sales_model->get_cart_items($cart_main->id));
				
				$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount);
				$this->sales_model->main_cart_details_update($this->session->userdata('cart_session_id'),$update_array);
									
				$currency_symbol = $this->common_model->get_currency_symbol_from_id($product_price_details['currency_id']);
				
				$data['err_msg']= 0;
				$data['amount'] = $cart_items_total_amount;
				$data['count'] = $cart_total_items;
				$data['currency_symbol'] = $currency_symbol;
				echo json_encode($data); 
				exit;
				}
				else
				{
					$data['err_msg']= 1;
					echo json_encode($data); 
					exit;
				}
				
				//$cart_items = $this->sales_model->get_cart_items($cart_main->id);
			}
			
		
			
		}
		
		
			
		
		
		
	}
	
		function sales_check_out_old()
		{
		$content = array();
		$content=$this->get_student_deatils_for_popup();
		$purchased_course_names = '';
		$purchased_ebook_names  = '';
		$purchased_item_names = array();
		$product_name = array();
		
		$products_in_cart = array();
		$cart_main_details = array();
		
		
		 $this->tr_common['tr_sl_no'] 	= $this->user_model->translate_('sl_no');		 
		 $this->tr_common['tr_options']  = $this->user_model->translate_('options');		 
		 $this->tr_common['tr_price'] 	= $this->user_model->translate_('price');		 
		 $this->tr_common['tr_remove']   = $this->user_model->translate_('remove');		 
		 $this->tr_common['tr_basket_total'] = $this->user_model->translate_('basket_total');		 
		 $this->tr_common['tr_continue_shopping'] = $this->user_model->translate_('continue_shopping');		 
		 $this->tr_common['tr_secure_checkout'] = $this->user_model->translate_('secure_checkout');	 
		  $this->tr_common['tr_shop_cart'] = $this->user_model->translate_('your_shop_basket');
		  $this->tr_common['tr_item'] = $this->user_model->translate_('Item');
		  $this->tr_common['tr_Product_Name'] = $this->user_model->translate_('Product_Name');
		  $this->tr_common['tr_Type'] = $this->user_model->translate_('Type');
		 
		  
		  
		  
		  
		  
		if($this->session->userdata('cart_session_id'))
		{
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('cart_session_id'));
			$currency_id = $cart_main_details[0]->currency_id;	
			$currency_symbol = $this->common_model->get_currency_symbol_from_id($cart_main_details[0]->currency_id);
			foreach($cart_main_details as $cart_main)
			{		
				$cart_main_id = $cart_main->id;	
				$coupon_applied = $cart_main->coupon_applied;	
				$coupon_code_applied = $cart_main->coupon_code; 	
				$discount_amount = $cart_main->discount_amount;
				$products_in_cart = $this->sales_model->get_cart_items($cart_main->id);
				
				$q=0;
				if(!empty($products_in_cart))
				{
				foreach($products_in_cart as $prod)
				{
					$purchased_item_names[$q] ='';
					//$product_id_in_cart = $prod->product_id;
					
					$product_details = $this->common_model->get_product_details($prod->product_id);
					/*echo "<br>Cart item  details";
					echo "<pre>";
					print_r($product_details);*/
					
					$cart_item_details = $this->sales_model->get_cart_items_details($cart_main_id,$prod->id);
					
					/*echo "<br>Cart item  details";
					echo "<pre>";
					print_r($cart_item_details);*/
					foreach($cart_item_details as $item_det)
					{
						$selected_items = $item_det->selected_item_ids;
						if($item_det->product_type == 'ebooks')
						{
							$ebook_ids = explode(',',$selected_items);
							
							$product_name[$q] =  'Ebook';
							
							for($qq1=0;$qq1<count($ebook_ids);$qq1++)
							{
								$ebook_details = $this->ebook_model->get_ebook_details($ebook_ids[$qq1]);
								/*echo "<br>Ebook Details";
								echo "<pre>";
								print_r($ebook_details);*/
								if($purchased_item_names[$q]=='')
								{
									$purchased_item_names[$q] = $ebook_details[0]->ebookName;
								}
								else
								{
									$purchased_item_names[$q] .=','.$ebook_details[0]->ebookName;
								}
							}
						}
						
						if($item_det->product_type == 'ebook_guide')
						{
							$ebook_ids = explode(',',$selected_items);
							
							$product_name[$q] =  'Guide';
							
							for($qq1=0;$qq1<count($ebook_ids);$qq1++)
							{
								$ebook_details = $this->ebook_model->get_ebook_details($ebook_ids[$qq1]);
								/*echo "<br>Ebook Details";
								echo "<pre>";
								print_r($ebook_details);*/
								if($purchased_item_names[$q]=='')
								{
									$purchased_item_names[$q] = $ebook_details[0]->ebookName;
								}
								else
								{
									$purchased_item_names[$q] .=','.$ebook_details[0]->ebookName;
								}
							}
						}
						
						else if($item_det->product_type == 'course')
						{
							$course_ids = explode(',',$selected_items);
							
							$product_name[$q] =  'Course';
							
						/*	echo "<br>In course";*/
							
							for($qq=0;$qq<count($course_ids);$qq++)
							{
								if($purchased_item_names[$q]=='')
								{
									$purchased_item_names[$q] = $this->common_model->get_course_name($course_ids[$qq]); 
								}
								else
								{
									$purchased_item_names[$q] .= ','.$this->common_model->get_course_name($course_ids[$qq]); 
								}
							}
							
						}
						else if($item_det->product_type == 'extension')
						{
							$product_name[$q] =  'Course Extension';
							//$purchased_item_names[$q] = 'Course Extension';
							$extension_details = $this->sales_model->get_extension_details_by_units($product_details[0]->units);	
							$purchased_item_names[$q] = $extension_details[0]->extension_option;
						}
						else if($item_det->product_type == 'colour_wheel_soft')
						{
							$product_name[$q] =  'Colour wheel';
							$purchased_item_names[$q] = 'Downloadable copy';
						}
						else if($item_det->product_type == 'colour_wheel_hard')
						{
							$product_name[$q] =  'Colour wheel';							
							$purchased_item_names[$q] = 'Hard copy';
						}
						else if($item_det->product_type == 'poe_soft')
						{
							$product_name[$q] =  'Proof of study';							
							$purchased_item_names[$q] = 'Soft copy';
						}
						else if($item_det->product_type == 'poe_hard')
						{
							$product_name[$q] =  'Proof of study';							
							$purchased_item_names[$q] = 'Hard copy';
						}
						else if($item_det->product_type == 'hardcopy')
						{
							$product_name[$q] =  'Certficate';							
							$purchased_item_names[$q] = 'hard copy';
						}
						/*else if($item_det->product_type == 'cert_hard_2')
						{
							$product_name[$q] =  'Certficate hard copy';						
							$purchased_item_names[$q] = 'Rest of world';
						}
						else if($item_det->product_type == 'cert_hard_3')
						{
							$product_name[$q] =  'Certficate hard copy';						
							$purchased_item_names[$q] = 'EU Airmail';
						}			*/	
						
						else if($item_det->product_type == 'proof_completion')
						{
							$product_name[$q] =  'Proof of completion';							
							$purchased_item_names[$q] = 'Soft copy';
						}
						else if($item_det->product_type == 'proof_completion_hard')
						{
							$product_name[$q] =  'Proof of completion';							
							$purchased_item_names[$q] = 'Hard copy';
						}
						
						else if($item_det->product_type == 'transcript')
						{
							$product_name[$q] =  'eTranscript';							
							$purchased_item_names[$q] = 'Soft copy';
						}
						else if($item_det->product_type == 'transcript_hard')
						{
							$product_name[$q] =  'eTranscript';							
							$purchased_item_names[$q] = 'Hard copy';
						}
						else if($item_det->product_type == 'access')
						{
							$product_name[$q] =  'Material subscription';							
							$purchased_item_names[$q] = $product_details[0]->item_id.' months';
						}
						
						
						
						
						
						
						
					}
					
					
					
					
				
					
					//$product_name[$q] =  $product_details[0]->type;
					$q++;
				}
				}
			}			
			
		}
		else
		{
			$currency_symbol = $this->common_model->get_currency_symbol_from_id(4);
			
		}
		
		//	echo "<br>Purchased course names ".$purchased_course_names;
		//	echo "<br>Purchased ebook name ".$purchased_ebook_names;
		
	/*	 echo "<pre>";
   print_r($cart_main_details);
   echo "<pre>";
   print_r($product_details);
   
   echo "<pre>";
   print_r($product_name);
   
   
   exit;*/
   		
		
			
		$from_location = $this->input->server('HTTP_REFERER', TRUE);
		$data['from_location'] = $from_location;
		$data['coupon_applied'] 			= $coupon_applied;
		$data['coupon_code_applied']   	   = $coupon_code_applied;
		$data['discount_amount'] 	  	   = $discount_amount; 
		$data['sales_from'] = 'sales_extension';
		$content['purchased_item_names'] = $purchased_item_names;
		$content['products_in_cart']  = $products_in_cart;
		$content['product_name']      = $product_name;
		$content['currency_id'] 			= $currency_id;
		$content['cart_main_details'] = $cart_main_details;
		$data['user_id'] = $this->session->userdata['student_logged_in']['id'];
		
		$data['certificate_details'] =  $this->certificate_model->get_certificate_details($this->session->userdata['student_logged_in']['id'],$this->session->userdata('cart_course_id'));
		
		$data['course_name'] = $this->common_model->get_course_name($this->session->userdata('cart_course_id'));
		
		$data['currency_symbol'] = $currency_symbol;
		$data['translate'] = $this->tr_common;
		$data['view'] = 'sales_check_out';
        $data['content'] = $content;				
        $this->load->view('user/course_template',$data);  
	}
	
		function sales_check_out()
		{
		$content = array();
		$content=$this->get_student_deatils_for_popup();
		$purchased_course_names = '';
		$purchased_ebook_names  = '';
		$purchased_item_names = array();
		$product_name = array();
		
		$products_in_cart = array();
		$cart_main_details = array();
		
		
		 $this->tr_common['tr_sl_no'] 	= $this->user_model->translate_('sl_no');		 
		 $this->tr_common['tr_options']  = $this->user_model->translate_('options');		 
		 $this->tr_common['tr_price'] 	= $this->user_model->translate_('price');		 
		 $this->tr_common['tr_remove']   = $this->user_model->translate_('remove');		 
		 $this->tr_common['tr_basket_total'] = $this->user_model->translate_('basket_total');		 
		 $this->tr_common['tr_continue_shopping'] = $this->user_model->translate_('continue_shopping');		 
		 $this->tr_common['tr_secure_checkout'] = $this->user_model->translate_('secure_checkout');	 
		  $this->tr_common['tr_shop_cart'] = $this->user_model->translate_('your_shop_basket');
		  $this->tr_common['tr_item'] = $this->user_model->translate_('Item');
		  $this->tr_common['tr_Product_Name'] = $this->user_model->translate_('Product_Name');
		  $this->tr_common['tr_Type'] = $this->user_model->translate_('Type');
		 
		  
		  
		$coupon_applied ='';
		$coupon_code_applied='';
		$discount_amount=''; 
		  
		  
		if($this->session->userdata('cart_session_id'))
		{
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('cart_session_id'));
			$currency_id = $cart_main_details[0]->currency_id;	
			$currency_symbol = $this->common_model->get_currency_symbol_from_id($cart_main_details[0]->currency_id);
			foreach($cart_main_details as $cart_main)
			{		
				$cart_main_id = $cart_main->id;	
				$coupon_applied = $cart_main->coupon_applied;	
				$coupon_code_applied = $cart_main->coupon_code; 	
				$discount_amount = $cart_main->discount_amount;
				$products_in_cart = $this->sales_model->get_cart_items($cart_main->id);
				
				$q=0;
				if(!empty($products_in_cart))
				{
				foreach($products_in_cart as $prod)
				{
					$purchased_item_names[$q] ='';
					//$product_id_in_cart = $prod->product_id;
					
					$product_details = $this->common_model->get_product_details($prod->product_id);
					/*echo "<br>Cart item  details";
					echo "<pre>";
					print_r($product_details);*/
					
					$cart_item_details = $this->sales_model->get_cart_items_details($cart_main_id,$prod->id);
					
					/*echo "<br>Cart item  details";
					echo "<pre>";
					print_r($cart_item_details);*/
					foreach($cart_item_details as $item_det)
					{
						$selected_items = $item_det->selected_item_ids;
						if($item_det->product_type == 'ebooks')
						{
							$ebook_ids = explode(',',$selected_items);
							
							$product_name[$q] =  'Ebook';
							
							for($qq1=0;$qq1<count($ebook_ids);$qq1++)
							{
								$ebook_details = $this->ebook_model->get_ebook_details($ebook_ids[$qq1]);
								/*echo "<br>Ebook Details";
								echo "<pre>";
								print_r($ebook_details);*/
								if($purchased_item_names[$q]=='')
								{
									$purchased_item_names[$q] = $ebook_details[0]->ebookName;
								}
								else
								{
									$purchased_item_names[$q] .=','.$ebook_details[0]->ebookName;
								}
							}
						}
						
						if($item_det->product_type == 'ebook_guide')
						{
							$ebook_ids = explode(',',$selected_items);
							
							$product_name[$q] =  'Guide';
							
							for($qq1=0;$qq1<count($ebook_ids);$qq1++)
							{
								$ebook_details = $this->ebook_model->get_ebook_details($ebook_ids[$qq1]);
								/*echo "<br>Ebook Details";
								echo "<pre>";
								print_r($ebook_details);*/
								if($purchased_item_names[$q]=='')
								{
									$purchased_item_names[$q] = $ebook_details[0]->ebookName;
								}
								else
								{
									$purchased_item_names[$q] .=','.$ebook_details[0]->ebookName;
								}
							}
						}
						
						else if($item_det->product_type == 'course')
						{
							$course_ids = explode(',',$selected_items);
							
							$product_name[$q] =  'Course';
							
						/*	echo "<br>In course";*/
							
							for($qq=0;$qq<count($course_ids);$qq++)
							{
								if($purchased_item_names[$q]=='')
								{
									$purchased_item_names[$q] = $this->common_model->get_course_name($course_ids[$qq]); 
								}
								else
								{
									$purchased_item_names[$q] .= ','.$this->common_model->get_course_name($course_ids[$qq]); 
								}
							}
							
						}
						else if($item_det->product_type == 'extension')
						{
							$product_name[$q] =  'Course Extension';
							//$purchased_item_names[$q] = 'Course Extension';
							$extension_details = $this->sales_model->get_extension_details_by_units($product_details[0]->item_id);	
							$purchased_item_names[$q] = $extension_details[0]->extension_option;
						}
						else if($item_det->product_type == 'colour_wheel_soft')
						{
							$product_name[$q] =  'Colour wheel';
							$purchased_item_names[$q] = 'Downloadable copy';
						}
						else if($item_det->product_type == 'colour_wheel_hard')
						{
							$product_name[$q] =  'Colour wheel';							
							$purchased_item_names[$q] = 'Hard copy';
						}
						else if($item_det->product_type == 'poe_soft')
						{
							$product_name[$q] =  'Proof of study';							
							$purchased_item_names[$q] = 'Soft copy';
						}
						else if($item_det->product_type == 'poe_hard')
						{
							$product_name[$q] =  'Proof of study';							
							$purchased_item_names[$q] = 'Hard copy';
						}
						else if($item_det->product_type == 'hardcopy')
						{
							$product_name[$q] =  'Certficate';							
							$purchased_item_names[$q] = 'hard copy';
						}
						/*else if($item_det->product_type == 'cert_hard_2')
						{
							$product_name[$q] =  'Certficate hard copy';						
							$purchased_item_names[$q] = 'Rest of world';
						}
						else if($item_det->product_type == 'cert_hard_3')
						{
							$product_name[$q] =  'Certficate hard copy';						
							$purchased_item_names[$q] = 'EU Airmail';
						}			*/	
						
						else if($item_det->product_type == 'proof_completion')
						{
							$product_name[$q] =  'Proof of completion';							
							$purchased_item_names[$q] = 'Soft copy';
						}
						else if($item_det->product_type == 'proof_completion_hard')
						{
							$product_name[$q] =  'Proof of completion';							
							$purchased_item_names[$q] = 'Hard copy';
						}
						
						else if($item_det->product_type == 'transcript')
						{
							$product_name[$q] =  'eTranscript';							
							$purchased_item_names[$q] = 'Soft copy';
						}
						else if($item_det->product_type == 'transcript_hard')
						{
							$product_name[$q] =  'eTranscript';							
							$purchased_item_names[$q] = 'Hard copy';
						}
						else if($item_det->product_type == 'access')
						{
							$product_name[$q] =  'Material subscription';							
							$purchased_item_names[$q] = $product_details[0]->item_id.' months';
						}
						
						
						
						
						
						
						
					}
					
					
					
					
				
					
					//$product_name[$q] =  $product_details[0]->type;
					$q++;
				}
				}
			}			
			
		}
		else
		{
			$currency_symbol = $this->common_model->get_currency_symbol_from_id(4);
			
		}
		
		//	echo "<br>Purchased course names ".$purchased_course_names;
		//	echo "<br>Purchased ebook name ".$purchased_ebook_names;
		
	/*	 echo "<pre>";
   print_r($cart_main_details);
   echo "<pre>";
   print_r($product_details);
   
   echo "<pre>";
   print_r($product_name);
   
   
   exit;*/
   		
		
			
		$from_location = $this->input->server('HTTP_REFERER', TRUE);
		$data['from_location'] = $from_location;
		$data['coupon_applied'] 			= $coupon_applied;
		$data['coupon_code_applied']   	   = $coupon_code_applied;
		$data['discount_amount'] 	  	   = $discount_amount; 
		$data['sales_from'] = 'sales_extension';
		$content['purchased_item_names'] = $purchased_item_names;
		$content['products_in_cart']  = $products_in_cart;
		$content['product_name']      = $product_name;
		$content['currency_id'] 			= $currency_id;
		$content['cart_main_details'] = $cart_main_details;
		$data['user_id'] = $this->session->userdata['student_logged_in']['id'];
		
		$data['certificate_details'] =  $this->certificate_model->get_certificate_details($this->session->userdata['student_logged_in']['id'],$this->session->userdata('cart_course_id'));
		
		$data['course_name'] = $this->common_model->get_course_name($this->session->userdata('cart_course_id'));
		
		$data['currency_symbol'] = $currency_symbol;
		$data['translate'] = $this->tr_common;
		$data['view'] = 'sales_check_out_test';
        $data['content'] = $content;				
        $this->load->view('user/course_template',$data);  
	}
	
	function apply_coupon_code_cart($coupon_code,$currency_id)
	{
		$this->load->model('discount_code_model','',TRUE);		
		
		
		$coupon_details = $this->discount_code_model->get_coupon_details_from_code($coupon_code);
		
		
		
		/*echo "Herer";
		echo "<pre>";
		print_r($coupon_details);
		exit;*/
		
		if(!empty($coupon_details))
		{
			$data['discount_code'] = $coupon_code;
			$data['discount_code_id'] = $coupon_details[0]->id;
			
			$coupon_product_type = $coupon_details[0]->product;
			
			if($this->session->userdata('coupon_applied'))
			{
				/*echo "<pre>";
					print_r($this->session->userdata('coupon_applied_details'));*/
					$data['err_msg']= 1;
					$data['err_type'] = 'Coupon already applied';					
					echo json_encode($data); 
					exit;		
			}
			else
			{
				
				
				
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('cart_session_id'));
	
	foreach($cart_main_details as $cart_main)
			{
				$cart_main_id = $cart_main->id;
			}	
				
				$cart_items_id = $this->sales_model->get_cart_items_id_of_coupon_applied_product($cart_main_id,$coupon_product_type);
				
		   if($cart_items_id)
		   {		
				$product_id    = $this->sales_model->get_product_id_from_cart_items_id($cart_main_id,$cart_items_id);		
				
				//echo "Prodyuct id ".$product_id." Cart items id ".$cart_items_id." Cart main id ".$cart_main_id;
				$price_det     = $this->common_model->getProductFee($product_id,$currency_id);
			/*	echo "<pre>";
				print_r($price_det);
				exit;*/
				
				$amount = $price_det['amount'];
				$currency_symbol = $price_det['currency_symbol'];
				$currency_code = $price_det['currency_code'];
				
				$discount_value = $coupon_details[0]->discount_value;
					if($coupon_details[0]->discount_type=='percentage')
					{
						//@round($cart_items_total_amount,2);
						//$amount = $cart_items[0]->item_amount;
//						number_format($number, 2, '.', '');
												
						$reduced_amount = $amount - round(( $amount * ($discount_value/100)),2);					
						$discount_amount = $amount-$reduced_amount;
					}
					elseif($coupon_details[0]->discount_type=='price')
					{
					//	$amount = $cart_items[0]->item_amount;
					
				$currency_id_discount_value = $this->discount_code_model->get_amount_for_discount_code($coupon_details[0]->id,$currency_id);
						if($currency_id_discount_value!='')
						{
						
						$reduced_amount = $amount-($currency_id_discount_value);
						$discount_amount = $amount-$reduced_amount;
							if($reduced_amount<=0)
							{
								$data['err_msg']= 1;
								$data['err_type'] = 'Something went wrong. Plaese contact admin';					
								echo json_encode($data); 
								exit;	
							}
						
						}
						else
						{
							$data['err_msg']= 1;
							$data['err_type'] = 'Something went wrong. Plaese contact admin';					
							echo json_encode($data); 
							exit;	
							
						}
					}
					
					
	
			}
			else
			{
				$data['err_msg']= 1;
				$data['err_type'] = 'Something went wrong. Plaese contact admin';					
				echo json_encode($data); 
				exit;	
				
			}
				
				
				
				$cart_items = $this->sales_model->get_cart_items($cart_items_id);
				
				/*echo "Cart items id ".$cart_items_id;
				echo "<pre>";
				print_r($cart_items);*/
				
				
				
					
					$update_array = array("item_amount"=>$reduced_amount,"discount_amount"=>$discount_amount);
					$this->sales_model->sales_cart_items_update($cart_items_id,$update_array);
					
					
					$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main_id);
					
					
						$cart_items_total_amount=@round($cart_items_total_amount,2);
					
				
				
					$cart_total_items = count($this->sales_model->get_cart_items($cart_main->id));
				
$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount,"discount_amount"=>$discount_amount,"coupon_applied"=>'yes',"coupon_code"=>$coupon_code);
					$this->sales_model->main_cart_details_update($this->session->userdata('cart_session_id'),$update_array);
				
					
				
				
					
					
					$sess_array = array('coupon_applied' => true);
					$this->session->set_userdata($sess_array);
				
					$data['err_msg']		 = 0;
					$data['amount'] 		  = $cart_items_total_amount;
					$data['discount_amount'] = $discount_amount;					
					$data['currency_symbol'] = $currency_symbol;
					$data['coupon_applied_product'] = $coupon_product_type;
					
					
					
					$data['currency_code']   = $currency_code;
					
					$sess_array = array('coupon_applied_details' => $data);
				    $this->session->set_userdata($sess_array);
					
					/*echo "<pre>";
					print_r($_SESSION);
					exit;*/
					
					echo json_encode($data); 
					exit;		
					
					
			}
		}
		
					$data['err_msg']= 1;
					$data['err_type'] = 'Coupon not applicable';					
					echo json_encode($data); 
					exit;	
		
	}
	
	
	
	
	
	
	
	/*function remove_item_from_cart($cart_item_id,$cart_main_id)
	{
		$this->sales_model->delete_product_from_cart($cart_item_id,$cart_main_id);
		
		$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main_id);				
		$cart_items_total_amount = @round($cart_items_total_amount,2);		
		$cart_total_items = count($this->sales_model->get_cart_items($cart_main_id));		
		$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount);		
		$this->sales_model->main_cart_details_update($this->session->userdata('cart_session_id'),$update_array);		
		
		redirect('home/sales_check_out','refresh');
		
	}*/
	function remove_item_from_cart($cart_item_id,$cart_main_id,$source='',$stud_id='')
	{		
		$cart_item_details = $this->sales_model->get_cart_items_by_product($cart_main_id,$cart_item_id);		
		foreach($cart_item_details as $item_details)
		{
			$product_discount_amount = $item_details->discount_amount;				
		}		
		$this->sales_model->delete_product_from_cart($cart_item_id,$cart_main_id);		
		$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main_id);				
		$cart_items_total_amount = @round($cart_items_total_amount,2);		
		$cart_total_items = count($this->sales_model->get_cart_items($cart_main_id));			
		$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('cart_session_id'));	
			
			foreach($cart_main_details as $cart_main)
			{
				$total_discount_amount = $cart_main->discount_amount;	
			}
		
		if($product_discount_amount==0) // no coupon applied...
		{			
			$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount);		
			$this->sales_model->main_cart_details_update($this->session->userdata('cart_session_id'),$update_array);	
		}
		else // coupon applied...
		{			
			$new_discount_amount =  $total_discount_amount - $product_discount_amount;
			if($new_discount_amount==0) // Coupon applied product removed
			{				
				$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount,
				"discount_amount"=>$new_discount_amount,"coupon_applied"=>'no',"coupon_code"=>'');
				
					if($this->session->userdata('coupon_applied'))
					{
					  $this->session->unset_userdata('coupon_applied');
					  $this->session->unset_userdata('coupon_applied_details');
					}				
			}
			else // Some coupon applied product is still in cart
			{
				$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount,
				"discount_amount"=>$new_discount_amount);				
			}			
		   $this->sales_model->main_cart_details_update($this->session->userdata('cart_session_id'),$update_array);				
		}
		if($source=='package_enrol')
		{			
			redirect('home/package_check_out/'.$stud_id,'refresh');
		}
		else
		{
			redirect('home/sales_check_out','refresh');
		}
		
	}
	function remove_item_from_basket_ajax($product_id)
	{
		if($this->session->userdata('cart_session_id'))
		{
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('cart_session_id'));	
			$cart_main_id = $cart_main_details[0]->id;
			$cart_details_by_product = $this->sales_model->get_cart_items_by_product_id($cart_main_id,$product_id);
			$cart_details_id = $cart_details_by_product[0]->id;
			
			/*echo "<br>Cart main id ".$cart_main_id;
			echo "<pre>";
			print_r($cart_main_details);
			echo "<br>Cart details id ".$cart_details_id;
			echo "<pre>";
			print_r($cart_details_by_product);	*/		
			
			$this->sales_model->delete_item_from_cart_by_product_id($cart_main_id,$cart_details_id,$product_id);	
			
			$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main_id);				
			$cart_items_total_amount = @round($cart_items_total_amount,2);		
			$cart_total_items = count($this->sales_model->get_cart_items($cart_main_id));		
			$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount);	
			/*echo "<pre>";
			print_r($update_array);		*/	
				
			$this->sales_model->main_cart_details_update($this->session->userdata('cart_session_id'),$update_array);	
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('cart_session_id'));
			/*echo "<br>After updation";
			echo "<pre>";
			print_r($cart_main_details);	*/
			
			$currency_symbol = $this->common_model->get_currency_symbol_from_id($cart_main_details[0]->currency_id);
			
			$data['err_msg']= 0;
			$data['amount'] = $cart_main_details[0]->total_cart_amount;     
			$data['count'] = $cart_main_details[0]->item_count;
			$data['currency_symbol'] = $currency_symbol;
			echo json_encode($data); 
			exit;   
			
		}
		
	}
	
	function after_sales_pay()
	{				
		$content = array();
		$content=$this->get_student_deatils_for_popup();
		
		$purchase_note ='';
		
		if($this->session->userdata('cart_session_id'))
		{
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('cart_session_id'));	
			//$cart_main_details = $this->sales_model->get_cart_main_details('eb40a2fa20c23a4c588e8da74e5eabc2');	
			

			foreach($cart_main_details as $cart_main)
			{		
				$cart_main_id = $cart_main->id;			
				$products_in_cart = $this->sales_model->get_cart_items($cart_main->id);
				
				if(!empty($products_in_cart))
				{
				foreach($products_in_cart as $prod)
				{					
					$product_details = $this->common_model->get_product_details($prod->product_id);
					$cart_item_details = $this->sales_model->get_cart_items_details($cart_main_id,$prod->id);
					foreach($cart_item_details as $item_det)
					{
						$selected_items = $item_det->selected_item_ids;
						if($item_det->product_type == 'ebooks')
						{
							$ebook_ids = explode(',',$selected_items);				
							
							$purchase_note .='<p>Download your '.count($ebook_ids).'&nbsp; ebooks from the "my ebooks" page when you return to campus.</p>';
						}
						if($item_det->product_type == 'ebook_guide')
						{
							$ebook_ids = explode(',',$selected_items);				
							
							$purchase_note .='<p>Download your '.count($ebook_ids).'&nbsp; ebook guide from the "my ebooks" page when you return to campus.</p>';
						}
						else if($item_det->product_type == 'course')
						{
							$course_ids = explode(',',$selected_items);		
							
							$purchase_note .="<p>".count($course_ids)."&nbsp; courses have been applied to your account, click on 'start' to activate the course.</p>";
							
						}
						else if($item_det->product_type == 'extension')
						{
					$extension_details = $this->sales_model->get_extension_details_by_units($product_details[0]->item_id);					
							
							$purchase_note .= '<p>'.$extension_details[0]->extension_option.'&nbsp; extension has been applied to your account.</p>'; 
						}
						else if($item_det->product_type == 'colour_wheel_soft')
						{
							
							$purchase_note .= '<p>Downloadable colour wheel will be emailed to you.</p>';
						}
						else if($item_det->product_type == 'colour_wheel_hard')
						{
											
							$purchase_note .= '<p>Hard copy of colour wheel will be posted to your address.</p>';
						}
						
						else if($item_det->product_type == 'poe_soft')
						{
							$purchase_note .= '<p>Downloadable copy of proof of study will be emailed to you.</p>';							
						}
						else if($item_det->product_type == 'poe_hard')
						{
							$purchase_note .= '<p>Hard copy of proof of study will be posted to your address.</p>';
						}
						else if($item_det->product_type == 'hardcopy')
						{
							$purchase_note .= '<p>Hard copy of certificate will be posted to your address.</p>';
						}
						/*else if($item_det->product_type == 'cert_hard_2')
						{
							$product_name[$q] =  'Certficate hard copy';						
							$purchased_item_names[$q] = 'Rest of world';
						}
						else if($item_det->product_type == 'cert_hard_3')
						{
							$product_name[$q] =  'Certficate hard copy';						
							$purchased_item_names[$q] = 'EU Airmail';
						}			*/	
						
						else if($item_det->product_type == 'proof_completion')
						{
							$purchase_note .= '<p>Downloadable copy of proof of completion will be emailed to you.</p>';
						}
						else if($item_det->product_type == 'proof_completion_hard')
						{
							$purchase_note .= '<p>Hard copy of proof of completion will be emailed to you.</p>';
						}
						
						else if($item_det->product_type == 'transcript')
						{
							$purchase_note .= '<p>Downloadable copy of eTranscript will be emailed to you.</p>';
						}
						else if($item_det->product_type == 'transcript_hard')
						{
							$purchase_note .= '<p>Hard copy of eTranscript will be emailed to you.</p>';
						}
						else if($item_det->product_type == 'access')
						{
							$purchase_note .= '<p>'.$product_details[0]->item_id.' months of course material access is added to your account.</p>';
						}
						
						
					}					
					
				}
				}
			}			
			
		}
		
		
		$this->session->unset_userdata('cart_session_id');		
		$this->session->unset_userdata('cart_source'); 	
		
		$data['purchase_note'] = $purchase_note;
		
		$data['translate'] = $this->tr_common;
		$data['view'] = 'sales_succ_message';
        $data['content'] = $content;				
        $this->load->view('user/course_template',$data); 
		
		
	}

	
	function ajaxCheckUsername(){
		
		$this->load->model('user_model');
		
		$username = $_REQUEST['user_name'];
		
		$user_exist = $this->user_model->check_record_exist('username','users',$username);
		
		if($user_exist) {
			echo 'false';}
		else {
			echo 'true';}
		
	}
	
	function ajaxCheckEmail(){
		
		
		$this->load->model('user_model');
		
		$email = $_REQUEST['email'];
		$user_exist = $this->user_model->check_record_exist('email','users',$email);
		
		if($user_exist) {
			echo 'false';}
		else {
			echo 'true';}
		
	}		
		
	function ajaxCheckVouchercode(){
				
		/*$this->load->model('gift_voucher_model');
		
		$vcode = $_REQUEST['vouchercode'];
		$voucher_details = $this->gift_voucher_model->isValid($vcode);*/
			
			//$voucher['valid_voucher']    = $voucher_details['code_exist'];	
			
			/*if($voucher_details['security_req']=='yes'){
			$voucher['security_required'] = true;
			}else{
			$voucher['security_required'] = false;	
			}*/
			
			$voucher['valid_voucher']    = 1;
			$voucher['security_required']    = 1;
			
			echo json_encode($voucher);  	
		
	}	
	
	function ajax_check_voucher_valid($voucher_code,$course_id)
	{
			
		    $voucher_details = $this->gift_voucher_model->isValid($voucher_code,$course_id);			
			
			$voucher['valid_voucher']    = $voucher_details['code_exist'];	
			
			if($voucher['valid_voucher']==1)
			{
				
				if($voucher_details['security_req']=='yes'){
				$voucher['security_required'] = true;
				}else{
				$voucher['security_required'] = false;	
				}			
			}
			else
			{
				$voucher['security_required'] = false;	
			}
			echo json_encode($voucher);  	
		
	}
	
	function apply_certificate_ajax($course_id)
	{
		$user_id = $this->session->userdata['student_logged_in']['id'];	
			
		$this->user_model->insert_certificate_request($course_id,$user_id);
		
		$data=array("course_status"=>'4'); // change course status to Certificate applied
		
		$this->user_model->update_student_enrollments($course_id,$user_id,$data);
		
		
		$data['success']=  'Certificate applied';
		echo json_encode($data);  
		exit;
		
	}
	
	function maintenance()
	{		
 		$this->load->view('launching/index','');
	}
	function notice()
 	{
		$content = array();
		$data['translate'] = $this->tr_common;
		$data['view'] = 'notice';
		$langId = $this->language;	
		$top_menu_base_courses = $this->user_model->get_courses($langId);
		
		$data['top_menu_base_courses'] 			= $top_menu_base_courses;
		
		$data['content'] =$content;
		$this->load->view('user/outerTemplate',$data);		
		
		
 	}
	function add_ebook_public_cart($selected_values,$product_id,$product_type)
	{
		$new_selected_values = '';	
		$selected_values_array = explode("+",$selected_values);
		/*echo "<pre>";
		print_r($selected_values_array);*/
		for($j=0;$j<count($selected_values_array);$j++)
		{
			if($j==0)
			{
				$new_selected_values = $selected_values_array[$j];
			}
			else
			{
				$new_selected_values .=','.$selected_values_array[$j];
			}
		}
		
			if(!$this->session->userdata('cart_public_session_id'))
			{	
				
				$sess_array = array('cart_public_session_id' => session_id()); 			
				session_regenerate_id();
				$this->session->set_userdata($sess_array);	
				
				$sessionData['sessionId'] = session_id();
				$this->session->set_userdata($sessionData);		
				$tempDetails['session_id']	= $this->session->userdata('cart_public_session_id');
				$tempDetails['ebook_id']	  = $new_selected_values;
				$tempDetails['product_type']  = $product_type;
				$tempDetails['product_id']	= $product_id;
				if(isset($this->session->userdata['student_logged_in']['id']))
				$tempDetails['user_id']	   = $this->session->userdata['student_logged_in']['id'];	
				$ebookTempId = $this->ebook_model->addEbookCart($tempDetails);
			
			}
			else
			{
				$added_ebook_details = $this->ebook_model->get_current_ebook_public_cart_items();
				
				if(empty($added_ebook_details))
				{			
					$tempDetails['session_id']	= $this->session->userdata('cart_public_session_id');
					$tempDetails['ebook_id']	  = $new_selected_values;
					$tempDetails['product_type']  = $product_type;
					$tempDetails['product_id']	= $product_id;
					if(isset($this->session->userdata['student_logged_in']['id']))
					$tempDetails['user_id']	   = $this->session->userdata['student_logged_in']['id'];	
					$ebookTempId = $this->ebook_model->addEbookCart($tempDetails);			
				}
				else
				{
					$tempDetails['ebook_id']	  = $new_selected_values;
					$tempDetails['product_type']  = $product_type;
					$tempDetails['product_id']	= $product_id;
					if(isset($this->session->userdata['student_logged_in']['id']))
					$tempDetails['user_id']	   = $this->session->userdata['student_logged_in']['id'];	
					$ebookTempId = $this->ebook_model->updateEbookCart($tempDetails);
				}
				
			}
			
			
			
			
			if(isset($ebookTempId))
			{
				if($product_type=='ebooks')
				{
					// assuming if count of added ebooks greater than 7 means all ebook added 
					if(count($selected_values_array) > 7) 
					{
						$added_ebook_type = array('ebook_type_added'=>'all'); // ebooks_all
						$this->session->set_userdata($added_ebook_type);	
					}
					else
					{
						$added_ebook_type = array('ebook_type_added'=>'ind'); // ebooks individual
						$this->session->set_userdata($added_ebook_type);	
					}
				}
				else
				{
					$added_ebook_type = array('ebook_type_added'=>'offer'); // ebook_offer
					$this->session->set_userdata($added_ebook_type);
				}
				
				$data['err_msg']= 0;			
				$data['count'] = count($selected_values_array);
			//	$data['currency_symbol'] = $currency_symbol;
				echo json_encode($data); 
				exit;     
			}
			else
			{
				$data['err_msg']= 1;			
				//$data['currency_symbol'] = $currency_symbol;
				echo json_encode($data); 
				exit; 
			}
		
		
	}
	
	function get_product_details_ajax($count,$type)
	{
		$data = array();
		$product_id = $this->common_model->getProdectId($type,$item_id='',$count);	
		$data['product_id'] = $product_id;
		echo json_encode($data);  	
	}
	
	function add_item_to_cart_ebook($selected_values,$currency_id,$prodyct_type,$source)
	{
		if(!$this->session->userdata('student_logged_in')){
		  redirect('home');
		}
		$user_id = $this->session->userdata['student_logged_in']['id'];
		
		if(!$this->session->userdata('cart_session_id'))
		{		
			$sess_array = array('cart_session_id' => session_id()); 
			
			$this->session->set_userdata($sess_array);	
			
			$product_id= $this->common_model->getProdectId($prodyct_type,'',1); // Get product id for 1 item
			
			/*echo "<pre>";
			print_r ($sess_array);
			
			echo "<pre>";
			print_r ($_SESSION);*/
			
		//	echo  "Session id  ".$this->session->userdata['cart_session_id'];		
			
			$product_details = $this->common_model->get_product_details($product_id);
			
			$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
			
			/*echo "Product id  ".$product_id;
			echo "<br> Selected items ".$new_selected_values;
			echo "<br>";
			
			echo "<pre>";
			print_r($product_details);
			
			echo "<br>";
			
			echo "<pre>";
			print_r($product_price_details);*/
			
			$cart_main_insert_array = array("session_id"=>$this->session->userdata('cart_session_id'),"user_id"=>$user_id,"source"=>$source,"item_count"=>1,"total_cart_amount"=>$product_price_details['amount'],"currency_id"=>$product_price_details['currency_id']);
			
			//$cart_main_id =1;
  		  
		    $cart_main_id = $this->common_model->insert_to_table("sales_cart_main",$cart_main_insert_array);
				
			$user_agent_data = array();
			
			$user_agent_data['cart_main_id']  = $cart_main_id;
			$user_agent_data['session_id'] 	= $this->session->userdata('cart_session_id');
			$user_agent_data['os'] 			= $this->agent->platform();
			$user_agent_data['browser'] 	   = $this->agent->agent_string();
			
			
			$this->common_model->insert_to_table("sales_user_agents",$user_agent_data);
				
		/*	echo "<br> Main insert array ";
			
			echo "<pre>";
			print_r($cart_main_insert_array);*/
				
									
			$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$product_id,"item_amount"=>$product_price_details['amount'],"currency"=>$product_price_details['currency_id']);
			
			/*echo "<br> Item details array ";
			
			echo "<pre>";
			print_r($item_details_array);*/
			//$cart_items_id =10;
			
			$cart_items_id = $this->common_model->insert_to_table("sales_cart_items",$item_details_array);		
			
			$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>$product_details[0]->type,"selected_item_ids"=>$selected_values);
			
			/*echo "<br> Items array ";
			
			echo "<pre>";
			print_r($items_array);*/
			
			$this->session->unset_userdata('cart_session_type');			
			$sess_array = array('cart_session_type' => $product_details[0]->type);			
			$this->session->set_userdata($sess_array);
			
			$item_id = $this->common_model->insert_to_table("sales_cart_item_details",$items_array);	
			
			$currency_symbol = $this->common_model->get_currency_symbol_from_id($product_price_details['currency_id']);
			
			//$amount = $product_price_details['amount'];
				
			//sales_cart_item_details
			/*echo "Exit hererer ";
			exit;*/
			$data['err_msg']= 0;
			$data['amount'] = $product_price_details['amount'];
			$data['count'] = 1;
			$data['currency_symbol'] = $currency_symbol;
			echo json_encode($data); 
			exit;     	
		}
		{
			//echo $this->session->userdata('cart_session_id');
			
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('cart_session_id'));
			foreach($cart_main_details as $cart_main)
			{
				$cart_main_id = $cart_main->id;
				$product_in_cart = $this->sales_model->check_product_type_exist_in_cart($cart_main->id,$prodyct_type);
				if(empty($product_in_cart))
				{
				
				$product_id= $this->common_model->getProdectId($prodyct_type,'',1); // Get product id for 1 item
				$product_details = $this->common_model->get_product_details($product_id);			
				$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
									
				$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$product_id,"item_amount"=>$product_price_details['amount'],"currency"=>$product_price_details['currency_id']);
				
				$cart_items_id = $this->common_model->insert_to_table("sales_cart_items",$item_details_array);		
			
				$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>$product_details[0]->type,"selected_item_ids"=>$selected_values);
			
				$item_id = $this->common_model->insert_to_table("sales_cart_item_details",$items_array);	
				
				$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main->id);
				
				$cart_items_total_amount=@round($cart_items_total_amount,2);
				
				
				$cart_total_items = count($this->sales_model->get_cart_items($cart_main->id));
				
				$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount);
				$this->sales_model->main_cart_details_update($this->session->userdata('cart_session_id'),$update_array);
									
				$currency_symbol = $this->common_model->get_currency_symbol_from_id($product_price_details['currency_id']);
				
				$data['err_msg']= 0;
				$data['amount'] = $cart_items_total_amount;
				$data['count'] = $cart_total_items;
				$data['currency_symbol'] = $currency_symbol;
				echo json_encode($data); 
				exit;
				}
				else
				{
					
					
				$cart_items_id = $product_in_cart[0]->cart_items_id;
				$added_ebooks = $product_in_cart[0]->selected_item_ids;
				
				$new_selected_values = $product_in_cart[0]->selected_item_ids.','.$selected_values;
				
				$added_ebook_array = explode(',',$added_ebooks);
				
				$added_ebook_count = count($added_ebook_array);
				
				if(!in_array($selected_values,$added_ebook_array))
				{
				
				// Get ebook product id of already added ebook + ebook going to add
				$product_id= $this->common_model->getProdectId($prodyct_type,'',($added_ebook_count+1)); 
				
				$product_details = $this->common_model->get_product_details($product_id);			
				$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
									
				$item_details_array = array("product_id"=>$product_id,"item_amount"=>$product_price_details['amount'],"currency"=>$product_price_details['currency_id']);				
				//sales_cart_items_update
				
				$this->sales_model->sales_cart_items_update($cart_items_id,$item_details_array);	
							
				$items_array = array("selected_item_ids"=>$new_selected_values);			
			
				$this->sales_model->sales_cart_item_details_update($cart_main_id,$cart_items_id,$items_array);	
				
				$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main->id);
				
				$cart_items_total_amount=@round($cart_items_total_amount,2);
				
				
				$cart_total_items = count($this->sales_model->get_cart_items($cart_main->id));
				
				$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount);
				$this->sales_model->main_cart_details_update($this->session->userdata('cart_session_id'),$update_array);
									
				$currency_symbol = $this->common_model->get_currency_symbol_from_id($product_price_details['currency_id']);
				
				$data['err_msg']= 0;
				$data['amount'] = $cart_items_total_amount;
				$data['count'] = $cart_total_items;
				$data['currency_symbol'] = $currency_symbol;
				echo json_encode($data); 
				exit;
				}
				else
				{
					
					$data['err_msg']= 1;
					echo json_encode($data); 
					exit;
				}
				
					
				}
				
				//$cart_items = $this->sales_model->get_cart_items($cart_main->id);
			}
			
		
			
		}
		
	}
	
	function remove_ebook_from_basket($ebook_id,$currency_id,$product_type)
	{
		if($this->session->userdata('cart_session_id'))
		{
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('cart_session_id'));	
			$cart_main_id = $cart_main_details[0]->id;
			
				$product_in_cart = $this->sales_model->check_product_type_exist_in_cart($cart_main_id,$product_type);
				if(!empty($product_in_cart))
				{
					$cart_items_id = $product_in_cart[0]->cart_items_id;
				
					$added_ebooks = $product_in_cart[0]->selected_item_ids;								
					$added_ebook_array = explode(',',$added_ebooks);				
					$added_ebook_count = count($added_ebook_array);
					
					if(in_array($ebook_id,$added_ebook_array))
					{
						
						if($added_ebook_count > 1) // If more than one ebook added in cart just remove one ebook
						{
						// Get ebook product id of already added ebook - ebook going to add
						
						$product_id= $this->common_model->getProdectId($product_type,'',($added_ebook_count-1)); 
						
						$product_details = $this->common_model->get_product_details($product_id);			
						$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
						
						if(($key = array_search($ebook_id, $added_ebook_array)) !== false) {
   							 unset($added_ebook_array[$key]);
						}
						
						$new_selected_values = implode(',',$added_ebook_array);
				
						//echo "<br> New Selected items ".$new_selected_values;
						
						
						$item_details_array = array("product_id"=>$product_id,"item_amount"=>$product_price_details['amount'],"currency"=>$product_price_details['currency_id']);				
				//sales_cart_items_update
						
						
				
						$this->sales_model->sales_cart_items_update($cart_items_id,$item_details_array);	
			
			
						$items_array = array("selected_item_ids"=>$new_selected_values);			
			
						$this->sales_model->sales_cart_item_details_update($cart_main_id,$cart_items_id,$items_array);	
				
						$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main_id);
						
						$cart_items_total_amount=@round($cart_items_total_amount,2);
						
						
						$cart_total_items = count($this->sales_model->get_cart_items($cart_main_id));
						
						$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount);
						$this->sales_model->main_cart_details_update($this->session->userdata('cart_session_id'),$update_array);
											
						$currency_symbol = $this->common_model->get_currency_symbol_from_id($product_price_details['currency_id']);
						
						$data['err_msg']= 0;
						$data['amount'] = $cart_items_total_amount;
						$data['count'] = $cart_total_items;
						$data['currency_symbol'] = $currency_symbol;
						echo json_encode($data); 
						exit;
						
					}
					else // if only one ebook added to cart delete the product type(Ebook)
					{					
						// Get ebook product id of already added ebook - ebook going to add
						
						$product_id= $this->common_model->getProdectId($product_type,'',(1)); 
						$this->remove_item_from_basket_ajax($product_id);						
					}						
			
					}
					
				}
		}
		
		$data['err_msg']= 1;
		echo json_encode($data); 
		exit;
		
	}
	
	
	function add_item_to_cart_ebook_old($product_id,$selected_values,$currency_id,$course_id_required,$source)
	{
		if(!$this->session->userdata('student_logged_in')){
		  redirect('home');
		}	
		$new_selected_values = '';	
		$selected_values_array = explode("+",$selected_values);
		/*echo "<pre>";
		print_r($selected_values_array);*/
		for($j=0;$j<count($selected_values_array);$j++)
		{
			if($j==0)
			{
				$new_selected_values = $selected_values_array[$j];
			}
			else
			{
				$new_selected_values .=','.$selected_values_array[$j];
			}
		}
		
		
		if($course_id_required == '1')
		{
			if($this->session->userdata('cart_course_id'))
			{
				$new_selected_values = $this->session->userdata('cart_course_id');
			}
			else
			{
				$new_selected_values = $selected_values;
			}
			
		}
		
	
		
		$user_id = $this->session->userdata['student_logged_in']['id'];
		
		//$currency_id = $this->currId;
		//$currency_code = $this->currencyCode;	
			
				
		if(!$this->session->userdata('cart_session_id'))
		{		
			$sess_array = array('cart_session_id' => session_id()); 
			
			session_regenerate_id();
			$this->session->set_userdata($sess_array);	
			
			$sess_array = array('cart_source' => '/coursemanager/campus');
			$this->session->set_userdata($sess_array);
			
			/*echo "<pre>";
			print_r ($sess_array);
			
			echo "<pre>";
			print_r ($_SESSION);*/
			
		//	echo  "Session id  ".$this->session->userdata['cart_session_id'];		
			
			$product_details = $this->common_model->get_product_details($product_id);
			
			$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
			
			/*echo "Product id  ".$product_id;
			echo "<br> Selected items ".$new_selected_values;
			echo "<br>";
			
			echo "<pre>";
			print_r($product_details);
			
			echo "<br>";
			
			echo "<pre>";
			print_r($product_price_details);*/
			
			$cart_main_insert_array = array("session_id"=>$this->session->userdata('cart_session_id'),"user_id"=>$user_id,"source"=>$source,"item_count"=>1,"total_cart_amount"=>$product_price_details['amount'],"currency_id"=>$product_price_details['currency_id']);
			
			//$cart_main_id =1;
  		  
		    $cart_main_id = $this->common_model->insert_to_table("sales_cart_main",$cart_main_insert_array);
			
			$user_agent_data = array();
			
			$user_agent_data['cart_main_id']  = $cart_main_id;
			$user_agent_data['session_id'] 	= $this->session->userdata('cart_session_id');
			$user_agent_data['os'] 			= $this->agent->platform();
			$user_agent_data['browser'] 	   = $this->agent->agent_string();
			
			
			$this->common_model->insert_to_table("sales_user_agents",$user_agent_data);	
				
		/*	echo "<br> Main insert array ";
			
			echo "<pre>";
			print_r($cart_main_insert_array);*/
				
									
			$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$product_id,"item_amount"=>$product_price_details['amount'],"currency"=>$product_price_details['currency_id']);
			
			/*echo "<br> Item details array ";
			
			echo "<pre>";
			print_r($item_details_array);*/
			//$cart_items_id =10;
			
			$cart_items_id = $this->common_model->insert_to_table("sales_cart_items",$item_details_array);		
			
			$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>$product_details[0]->type,"selected_item_ids"=>$new_selected_values);
			
			/*echo "<br> Items array ";
			
			echo "<pre>";
			print_r($items_array);*/
			
			$this->session->unset_userdata('cart_session_type');			
			$sess_array = array('cart_session_type' => $product_details[0]->type);			
			$this->session->set_userdata($sess_array);
			
			$item_id = $this->common_model->insert_to_table("sales_cart_item_details",$items_array);	
			
			$currency_symbol = $this->common_model->get_currency_symbol_from_id($product_price_details['currency_id']);
			
			//$amount = $product_price_details['amount'];
				
			//sales_cart_item_details
			/*echo "Exit hererer ";
			exit;*/
			$data['err_msg']= 0;
			$data['amount'] = $product_price_details['amount'];
			$data['count'] = 1;
			$data['currency_symbol'] = $currency_symbol;
			echo json_encode($data); 
			exit;     	
		}
		else
		{
			//echo $this->session->userdata('cart_session_id');
			
						
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('cart_session_id'));
			foreach($cart_main_details as $cart_main)
			{
				$cart_main_id = $cart_main->id;
				
				$sales_cart_items_id = $this->sales_model->get_cart_items($cart_main_id);	
				
				
				
				/*echo "<pre>";							
				print_r($sales_cart_items_id);
				exit;*/
				
				if(!empty($sales_cart_items_id))
				{
				
				$cart_items_id = $sales_cart_items_id[0]->id;
				$product_details = $this->common_model->get_product_details($product_id);			
				$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
									
				$item_details_array = array("product_id"=>$product_id,"item_amount"=>$product_price_details['amount'],"currency"=>$product_price_details['currency_id']);
				
				/*
				
				$cart_items_id = $this->common_model->insert_to_table("sales_cart_items",$item_details_array);		
			
				$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>$product_details[0]->type,"selected_item_ids"=>$new_selected_values);
			
				$item_id = $this->sales_model->sales_cart_items_details_update("sales_cart_item_details",$sales_cart_items_id[0]->);
				
				*/
				
			//	echo "<br>items id ".$sales_cart_items_id[0]->id;
			//	echo "<br>cart items id ".$cart_items_id;
				
				$this->sales_model->sales_cart_items_update($cart_items_id,$item_details_array);		
			
				$items_array = array("selected_item_ids"=>$new_selected_values);
			
				$item_id = $this->sales_model->sales_cart_item_details_update($cart_main_id,$cart_items_id,$items_array);	
				
				$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main->id);
				
				$cart_items_total_amount=@round($cart_items_total_amount,2);
				
				
				$cart_total_items = count($selected_values_array);
				
				$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount);
				$this->sales_model->main_cart_details_update($this->session->userdata('cart_session_id'),$update_array);
									
				$currency_symbol = $this->common_model->get_currency_symbol_from_id($product_price_details['currency_id']);
				
				$data['err_msg']= 0;
				$data['amount'] = $cart_items_total_amount;
				$data['count'] = $cart_total_items;
				$data['currency_symbol'] = $currency_symbol;
				echo json_encode($data); 
				exit;
				
				}
				else
				{
					
				
				$product_details = $this->common_model->get_product_details($product_id);			
				$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
									
				$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$product_id,"item_amount"=>$product_price_details['amount'],"currency"=>$product_price_details['currency_id']);
				
				$cart_items_id = $this->common_model->insert_to_table("sales_cart_items",$item_details_array);		
			
				$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>$product_details[0]->type,"selected_item_ids"=>$new_selected_values);
			
				$item_id = $this->common_model->insert_to_table("sales_cart_item_details",$items_array);	
				
				$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main->id);
				
				$cart_items_total_amount=@round($cart_items_total_amount,2);
				
				
				$cart_total_items = count($this->sales_model->get_cart_items($cart_main->id));
				
				$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount);
				$this->sales_model->main_cart_details_update($this->session->userdata('cart_session_id'),$update_array);
									
				$currency_symbol = $this->common_model->get_currency_symbol_from_id($product_price_details['currency_id']);
				
				$data['err_msg']= 0;
				$data['amount'] = $cart_items_total_amount;
				$data['count'] = $cart_total_items;
				$data['currency_symbol'] = $currency_symbol;
				echo json_encode($data); 
				exit;
				
				}
				
				//$cart_items = $this->sales_model->get_cart_items($cart_main->id);
			}
			
		
			
		}
		
		
			
		
		
		
	}
	
		function get_cart_details_ebooks()
		{			
				$content = array();
				$data =array();
				$price_details_array = array();
				$currency_id = $this->currId;					
				
				if($this->session->userdata('cart_session_id'))	
				{										
					$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('cart_session_id'));
					foreach($cart_main_details as $cart_main)
					{
						$cart_main_id = $cart_main->id;
						$product_in_cart = $this->sales_model->get_cart_items($cart_main->id);
											
						if(!empty($product_in_cart))
						{					
							
							foreach($product_in_cart as $prod_cart)
							{
								$product_cart_details = $this->sales_model->get_cart_items_details($cart_main_id,$prod_cart->id);
								$product_id  = $prod_cart->product_id;
							}							
							$selected_items_array = explode(',',$product_cart_details[0]->selected_item_ids);
							$data['cart_count']= 1;
							$data['product_id'] = $product_id;
							$data['selected_item_ids']= $selected_items_array;
							$data['product_exists_in_cart']= 1;
							
							echo json_encode($data);  
							exit;
						}
					}
					$data['cart_count']= 0;
					echo json_encode($data);  
							exit;
				}
				else
				{
					$data['cart_count']= 0;
					echo json_encode($data);  
							exit;
				}
			
		}

 function syllabus($courseId){

   	

	    $course_name = $this->common_model->get_course_name($courseId);
		$content['course_name']=$course_name['course_name'];
    $data['courseId']=$courseId;
	$data['translate'] = $this->tr_common;
	if($courseId==1)
	{
		$content['pageTitle']="Online Event Planner Course Syllabus EventTrix";
		$content['metaDesc']="EventTrix Event Planner course syllabus";
		$content['metaKeys']="event planner course,online course syllabus,course syllabus,eventtrix syllabus";
	}
	if($courseId==2)
	{
		$content['pageTitle']="Online Wedding Planner Course Syllabus EventTrix";
		$content['metaDesc']="EventTrix Wedding Planner course syllabus";
		$content['metaKeys']="wedding planner course,online course syllabus,couse syllabus,eventtrix syllabus";
	}
	
	
    $data['view'] = 'syllabus';
    $data['content'] = $content;
    $this->load->view('user/outerTemplate',$data);
  }
 function samplecertificate($courseId='1'){

   	

	    $course_name = $this->user_model->get_course_name($courseId);
		$data['course_name']=$course_name;
    $data['courseId']=$courseId;
	
	$content['pageTitle']= $course_name." Certification";
	$content['metaDesc']= "Get your ".$course_name." Certification today with Eventtrix international accredited online course.";
	$content['metaKeys']= "event, planning, certification, management, qualifications, certificate";
	
	$product = $this->user_model->get_product_id($courseId);
		
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
	
	
	$data['translate'] = $this->tr_common;
    $data['view'] = 'sample_certificate';
    $data['content'] = $content;
    $this->load->view('user/outerTemplate',$data);
  }
 
  function lernmore($courseId){

   	

	    $course_name = $this->common_model->get_course_name($courseId);
		$content['course_name']=$course_name['course_name'];
    $data['courseId']=$courseId;
	$data['translate'] = $this->tr_common;
    $data['view'] = 'learn_more';
    $data['content'] = $content;
    $this->load->view('user/outerTemplate',$data);
  }
  
  function course_module_details($courseId)
	{
		
		$content['base_course']=$this->course;      
		$modules = $this->user_model->get_courseunits_id($courseId);
		
		/*echo "<pre>";
		print_r($modules);
		exit;*/
		$i=0;
		foreach($modules as $row)
		{
			$this->db->select('*');
			$this->db->from('unit_courses');
			$this->db->where('id',$row->course_units);
			$query = $this->db->get();
			foreach($query->result() as $row2)
			{
				$content['modules'][$i] = $row2->unit_name;
				$content['sub_head'][$i] = explode('||',$row2->headings);
			}
			$i++;
		}
		
		
		$product = $this->user_model->get_product_id($courseId);
		
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
		foreach($content['base_course'] as $courses)
		{
			$prodectId = $this->common_model->getProdectId("course",$courses->course_id);
			$content['product_details'][$courses->course_id] = $this->common_model->getProductFee($prodectId,$this->currId);
		}
		
		
		//echo "<pre>";print_r($content);exit;
		$content['coursedetail'] = $this->user_model->get_coursedetails($courseId,$this->session->userdata['language']);		
		$data['courseId'] =$courseId;
		$data['course_name'] = $this->user_model->get_course_name($courseId); 
		
		$data['translate'] = $this->tr_common;
		$data['view'] = 'syllabus_new';
		$langId = $this->language;	
		$content['pageTitle'] = 'Course Details';
		
		$data['content'] =$content;
		$this->load->view('user/outerTemplate',$data);
	}



function unsubscribed($user_id)
	{ 
	  $this->tr_common['tr_Unsubscribed_successfully'] =$this->user_model->translate_('Unsubscribed_successfully');	
	  $this->tr_common['tr_Yournewsletter_hasbeen_unsubscribed_successfully'] =$this->user_model->translate_('Yournewsletter_hasbeen_unsubscribed_successfully');	
	     
		  $unsubscribed['newsletter']="no";
	      $this->db->where('user_id', $user_id);
          $this->db->update('users',$unsubscribed);
		  $langId = $this->language;	
	
		   $data['translate'] = $this->tr_common;
           $data['view'] = 'unsubscribed';
           $contents['pageTitle']='Unsubscribed';
			$data['content'] = $contents;
           $this->load->view('user/outerTemplate',$data);
		 
		
		 
	}

	
		function ebook_guides()
	{
		$content = array();
		$content['base_course']=$this->course;
        $content['language']=$this->language;
        $content['student_status']=$this->student_status;
        $content['topmenu']=$this->menu;
		$content['tr_head_description']=$this->user_model->translate_('head_description');
		$content['tr_user_name']=$this->user_model->translate_('user_name');
        $content['tr_password']=$this->user_model->translate_('password');
		
	    $content['metaKeys'] = "wedding, event, planning, course, online";
	    $content['metaDesc'] = "wedding and event planning courses brought to you by Eventrix.";
		
		
		/*echo "<br>Session id ".$this->session->userdata('sessionId');
		echo "<br>Exists ".$this->ebook_model->check_ebook_already_exists_in_cart($this->session->userdata('sessionId'),$ebook_id);
		exit;*/
		
		
		
		
		 $this->tr_common['tr_ebook_for'] = $this->user_model->translate_('ebook_for');
		 $this->tr_common['tr_reduced_from'] = $this->user_model->translate_('reduced_from');
		 $this->tr_common['tr_add_to_bag'] = $this->user_model->translate_('add_to_bag');
		 $this->tr_common['tr_check_out'] = $this->user_model->translate_('check_out');
		 
		  
		  
		  
		
		if(!isset($this->session->userdata['student_logged_in']['id']))
		{
			$type = 'public';
			$user_id='';
		}
		else
		{
			$type = 'user';
			$user_id = $this->session->userdata['student_logged_in']['id'];
			$ebDetails['userId'] =$user_id;
		}
		
		//echo $ebDetails['userId'];exit;
		$ebArray = $this->ebook_model->fetchEbookByLang($this->language);
		if(!empty($ebArray))
		{
			$i = 0;
			foreach($ebArray as $row)
			{
				
				$ebDetails['ebId'][$i] = $row->ebid;
				if($this->session->userdata('sessionId'))
				{
					
					$ebDetails['already_exists'][$i] = $this->ebook_model->check_ebook_already_exists_in_cart($this->session->userdata('sessionId'),$row->ebid);
				}
				else
				{
					$ebDetails['already_exists'][$i] = 0;
				}
				$ebDetails['ebName'][$i] = $row->ebookName;
				$ebDetails['language'][$i] = $row->language;
				$ebDetails['description'][$i] = $row->description;
				$ebDetails['fileName'][$i] = $row->fileName;
				$ebDetails['sample_ebook_name'][$i] = $row->sample_ebook_name;
				$ebDetails['courseId'][$i] = $row->courseId;
				$ebDetails['picPath'][$i] = $row->image_name;
				//translations
				$ebDetails['tr_trndimi_ebooks'] = $this->user_model->translate_('trendimi_ebooks');
				$ebDetails['tr_trndimi_ebooks_text'] =$this->user_model->translate_('trndimi_ebooks_text');
				
				
				$prodectId = $this->common_model->getProdectId('ebooks');	
				$ebookPrice =$this->common_model->getProductFee($prodectId,$this->currId);
				$ebDetails['fake_amount'][$i] =$ebookPrice['fake_amount'];
				$ebDetails['amount'][$i] =$ebookPrice['amount'];
				$ebDetails['currency_symbol'][$i] =$ebookPrice['currency_symbol'];
				$ebDetails['currency_id'][$i] =$ebookPrice['currency_id'];
				
				
				$i++;
			}
		}
		
		
			$prodArr = $this->common_model->get_product_by_type('ebooks');
		
		/*echo "<pre>";
		print_r($prodArr);
		exit;*/
		
		$x=0;
		foreach($prodArr as $row2)
		{
				$ebookPrice =$this->common_model->getProductFee($row2->id,$this->currId);
				$ebDetails['full_fake'][$x] =$ebookPrice['fake_amount'];
				$ebDetails['full_amount'][$x] =$ebookPrice['amount'];
				$ebDetails['full_curr_sym'][$x] =$ebookPrice['currency_symbol'];
				$ebDetails['full_curr_id'][$x] =$ebookPrice['currency_id'];
				$x++;
		}
		
		if($_POST)
		{
		if(isset($_POST['password_ebook']))
		{
			$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
   			$this->form_validation->set_rules('password_ebook', 'Password', 'trim|required|xss_clean|callback_check_database');
			$content['username_ebook'] = $this->input->post('username');
			if($this->form_validation->run() == TRUE)
			{
			//updating email cart to this userId
			$upArr['session_id'] = session_id();
			$upArr['user_id'] = $this->session->userdata['student_logged_in']['id'];
			
				$this->ebook_model->convert_cart($upArr);
				redirect('home/ebookCart', 'refresh');
     		}
			
		}
		else if(isset($_POST['public_name']))
		{
			$this->form_validation->set_rules('public_name', 'Name', 'trim|required|xss_clean');
   			$this->form_validation->set_rules('public_email', 'Email', 'trim|required|xss_clean');
			$content['public_name']=$publicdata['name'] = $this->input->post('public_name');
			$content['public_email']=$publicdata['email'] = $this->input->post('public_email');
			
			if($this->form_validation->run() == TRUE)
			{
				$publicdata['ebook_id'] = 0;
				$public_id['public_id'] =$this->ebook_model->add_public($publicdata);
				$this->session->set_userdata($public_id);
				
				$upArr['session_id'] = session_id();
				$upArr['user_id'] = 0;
			
				$this->ebook_model->convert_cart($upArr);
				
				redirect('home/ebookCart', 'refresh');
     		}
			
		}
		}
		
		$data['content'] = $ebDetails;
        $data['translate'] = $this->tr_common;
		
		
		$data['view'] = 'ebook_guides';
      //  $data['content'] = $content;
        $this->load->view('user/template',$data);
	}
	
	
	function checkTemp($ebid)
	{
		$this->load->model('ebook_model');
		$sessionData['sessionId'] = session_id();
		$this->session->set_userdata($sessionData);
		$this->db->select('id');
		$this->db->from('ebook_purchase_temp');
		$this->db->where('ebook_id',$ebid);
		$this->db->where('session_id',$this->session->userdata('sessionId'));
		
		
		$query = $this->db->get();
		if(!empty($query))
		{
			$i=0;
			$data =array();
			foreach($query->result() as $row)
			{
				$data['id'][$i]= $row->id;
				$i++;
			}
			$data['count']=$i;
		    
			echo json_encode($data);  
		}
		else
		{
			$data['count']=0;
			echo json_encode($data);
			
		}
		
		
		
			
	}
	
	
	
	function checkEbookTemp()
	{
		$this->load->model('ebook_model');
		
		
		 if(!$this->session->userdata('sessionId'))
		{
			session_regenerate_id();
			$sessionData['sessionId'] = session_id();	
		}
		else
		{
			$sessionData['sessionId'] = session_id();	
		}
		
		$this->session->set_userdata($sessionData);
		$this->db->select('ebook_id');
		$this->db->from('ebook_purchase_temp');
		$this->db->where('session_id',$this->session->userdata('sessionId'));
		
		
		$query = $this->db->get();
		if(!empty($query))
		{
			$i=0;
			$data =array();
			foreach($query->result() as $row)
			{
				//$data['ebid'][$i]= $row->id;
				$data['ebid'][$i]= $row->ebook_id;
				$i++;
			}
			$data['count']=$i;
		    $data['cur_symbol'] = $this->currSymbol;
			echo json_encode($data);  
		}
		else
		{
			$data['count']=0;
			$data['cur_symbol'] = $this->currSymbol;
			echo json_encode($data);
			
		}
		
		
		
			
	}
	
function ebookCart()
{
	
	/*for pop up details */
		if(isset($this->session->userdata['student_logged_in']['id']))
		$content=$this->get_student_deatils_for_popup();
	
		$sessionData['sessionId'] = session_id();
		$this->session->set_userdata($sessionData);
		
		$this->load->model('ebook_model');
				
		 $this->tr_common['tr_sl_no'] 	= $this->user_model->translate_('sl_no');		 
		 $this->tr_common['tr_options']  = $this->user_model->translate_('options');		 
		 $this->tr_common['tr_price'] 	= $this->user_model->translate_('price');		 
		 $this->tr_common['tr_remove']   = $this->user_model->translate_('remove');		 
		 $this->tr_common['tr_basket_total'] = $this->user_model->translate_('basket_total');		 
		 $this->tr_common['tr_continue_shopping'] = $this->user_model->translate_('continue_shopping');		 
		 $this->tr_common['tr_secure_checkout'] = $this->user_model->translate_('secure_checkout');		
		
		if(!isset($this->session->userdata['student_logged_in']['id']))
		{
			$upArr['session_id'] = session_id();
			$upArr['user_id'] = 0;
			$this->ebook_model->convert_cart($upArr);
			
			$type = 'public';
			$user_id ='';
		}
		else
		{
			$upArr['session_id'] = session_id();
			$upArr['user_id'] = $this->session->userdata['student_logged_in']['id'];
			$this->ebook_model->convert_cart($upArr);
			
			$type = 'user';
			$user_id = $this->session->userdata['student_logged_in']['id'];
			//echo "entered here";exit;
		}
		//echo $user_id;exit;
		
		//echo $this->session->userdata('cart_session_id');exit;
		$ebArray = $this->ebook_model->fetchEbookTemp($user_id,$this->session->userdata('cart_public_session_id'));
		//echo "<pre>";print_r($ebArray);exit;
		if(!empty($ebArray))
		{
			$i = 0;
			foreach($ebArray as $row)
			{
				
				$product_details = $this->common_model->get_product_details($row->product_id);
				
				if($product_details[0]->type == 'ebooks')
				{
					$ebDetails['product_type'][$i] = 'Ebook';
				}
				else if($product_details[0]->type == 'ebook_offer')
				{
					$ebDetails['product_type'][$i] =  'Ebook offer';
				}
				$ebDetails['tempId'][$i] = $row->id;
				$ebDetails['ebId'][$i] = $ebook_ids = $row->ebook_id;
				$ebDetails['session_id'][$i] = $row->session_id;
				$ebDetails['user_id'][$i] = $row->user_id;
				$ebDetails['tr_secure_checkout'] = $this->user_model->translate_('secure_checkout');
				$ebDetails['tr_shop_cart'] = $this->user_model->translate_('your_shop_basket');
				
				
				$ebook_ids = explode(',',$row->ebook_id);
				$purchased_item_names ='';
			
				for($qq1=0;$qq1<count($ebook_ids);$qq1++)
				{
					$ebook_details = $this->ebook_model->get_ebook_details($ebook_ids[$qq1]);
					/*echo "<br>Ebook Details";
					echo "<pre>";
					print_r($ebook_details);*/
					if($purchased_item_names=='')
					{
						$purchased_item_names = $ebook_details[0]->ebookName;
						$purchased_item_pic = '<img src="/public/user/outer/cart_img/'.$ebook_details[0]->image_name.'">';
					}
					else
					{
						$purchased_item_names.='<br /> <br /><br />'.$ebook_details[0]->ebookName;
						$purchased_item_pic.= '<br /> '.'<img src="/public/user/outer/cart_img/'.$ebook_details[0]->image_name.'">';
					}
					
					//$ebDetails['ebookName'][$i] = $ebook_details[0]->
				}
			
				
				$ebDetails['ebookName'][$i] = $purchased_item_names;
				$ebDetails['ebook_pic'][$i] =$purchased_item_pic;
				
				$ebook_product_id = $row->product_id;
				
			$i++;
			}
			$prodectId1 = $this->common_model->getProdectId('ebooks','',1);
					//$prodectId = $this->common_model->getProdectId('ebooks','',$i);
				
				//$prodectId1 = $this->common_model->getProdectId('ebooks','',1);	
				$ebookPrice1 =$this->common_model->getProductFee($prodectId1,$this->currId);
				$ebDetails['fake_amount_single']=$ebookPrice1['fake_amount'];
				$ebDetails['amount_single']=$ebookPrice1['amount'];
				
				
			//getting price of ebook's package as a product
		//	$prodectId = $this->common_model->getProdectId('ebooks','',$i);
			
			$ebDetails['product_id'] = $ebook_product_id;
			$ebDetails['stud_id'] = $user_id;
			$ebDetails['curr_id'] = $this->currId;
			$ebDetails['cur_symbol'] =$this->currSymbol;
			
				$ebookPrice =$this->common_model->getProductFee($ebook_product_id,$this->currId);
				$ebDetails['fake_amount']=$ebookPrice['fake_amount'];
				$ebDetails['amount']=$ebookPrice['amount'];
			
		}
		else
		{
			redirect("home/ebooks","refresh");
		}
		//echo "<pre>";print_r($ebDetails);print_r($ebookPrice);exit;
		$data['translate'] = $this->tr_common;
 		$data['view'] = 'eBookCartList';
        $data['content'] = $ebDetails;
		$langId = $this->language;	
		
		
     if($user_id=='')
        $this->load->view('user/template_outer',$data);
		else
		$this->load->view('user/template_outer',$data);
	
}
	
	function ebook_payment($product_id,$ebId)
	{
		$this->load->model('ebook_model');
		//if(isset($this->session->userdata['student_logged_in']['id']))
		/*if(!isset($this->session->userdata('sessionId')))
		{*/
			
		$tempDetails['session_id']=$this->session->userdata('sessionId');
		$tempDetails['ebook_id']=$ebId;
		if(isset($this->session->userdata['student_logged_in']['id']))
		$tempDetails['user_id']=$this->session->userdata['student_logged_in']['id'];	
		
		$ebookTempId = $this->ebook_model->addEbookCart($tempDetails);
	/*	}*/
		
		$content = array();
		if(isset($this->session->userdata['student_logged_in']['id']))
		{
			$content=$this->get_student_deatils_for_popup();
			$user_id = $this->session->userdata['student_logged_in']['id'];
			$content['user_id'] = $user_id;
			
		}
		$top_menu_base_courses = $this->user_model->get_courses($this->language);	
		
		$content['product_id'] = $product_id;
		
		if(empty($top_menu_base_courses))
		{
			$top_menu_base_courses = $this->user_model->get_courses(4);	
		}	
		$data['top_menu_base_courses'] 			= $top_menu_base_courses;
				
		$price_details =$this->common_model->getProductFee($product_id,$this->currId);
		
		$data['price_details'] = $price_details;
		$data['translate'] = $this->tr_common;
 		$data['view'] = 'eBookCartList';
		$data['content'] = $content;
       
		if(isset($this->session->userdata['student_logged_in']['id']))
        $this->load->view('user/innerTemplate',$data);
		else
		$this->load->view('user/outerTemplate',$data);
	}



	
	
	
	function afterBuyEbook($transaction_id)
	{
		//echo "entered here!afterBuyEbook";exit;
		//echo $this->session->userdata['public_id'];exit;
		
		$this->load->model('ebook_model');
		
		//echo "<pre>";
		//print_r($_REQUEST);exit;
		
		if(isset($transaction_id))
		{
		if(isset($this->session->userdata['student_logged_in']['id']))
		{
		$userId1=$this->session->userdata['student_logged_in']['id'];
		}
		else
		{
		$userId1='';
		}
		
			
			$tempArray = $this->ebook_model->fetchEbookTemp($userId1,$this->session->userdata('cart_public_session_id'));
			//$newArr = $tempArray->row();
			$i=0;
			$ebookids ='';
			foreach($tempArray as $row)
			{
				$ebData  = array();
				
				if($ebookids=='')
				$ebookids =$row->ebook_id;
				else
				{
				$ebookids .= ','.$row->ebook_id;
				}
				$ebooksID[]=$row->ebook_id;
				$ebData['user_id'] = $row->user_id;
				
				
			$i++;	
			}
			
			
			  	if(!isset($this->session->userdata['student_logged_in']['id']))
				{
							
					$prdctId = $this->common_model->getProdectId('ebooks','',$i);
				}
				else
				{
							
					$prdctId = $this->common_model->getProdectId('ebooks','',$i);
				}
			$dateNow =date('Y-m-d');
						
			$subscriDetails['user_id'] =$ebData['user_id'];
			$subscriDetails['product_id']=$subscriptionPublic['product_id'] = $prdctId;
			$subscriDetails['ebook_id']=$subscriptionPublic['ebook_id'] = $ebookids;
			$subscriDetails['date_purchased']=$subscriptionPublic['date_purchased'] = $dateNow;
			$subscriDetails['payment_id']=$subscriptionPublic['payment_id'] = $transaction_id;
			
			if($subscriDetails['user_id']!=0)
			{
			$subscriptionId = $this->ebook_model->addSubscription_user($subscriDetails);
			}
			else
			{
			$subscriptionId = $this->ebook_model->addSubscription_public($subscriptionPublic,$this->session->userdata['public_id']);
			}
			
			
				if($subscriDetails['user_id']!=0)
				$studentdata = $this->user_model->get_student_details($subscriDetails['user_id']);//user deteils();
				else
				$publicdata = $this->ebook_model->get_public_details($this->session->userdata['public_id']);//user deteils();
				
			
				$this->load->library('email');
				$this->load->model('email_model');
				
				$langId=$this->language;
				
				$row_new = $this->email_model->getTemplateById('ebook_download_link',$langId);
				
				foreach($row_new as $row1)
				{
					
					$emailSubject = $row1->mail_subject;
					$mailContent = $row1->mail_content;
					$mailing_template_id = $row1->id;
				}
				
				/*if($langId==3)
				{
					if($subscriDetails['user_id']!=0)
					{
				 	$mailContent = str_replace ( "#firstname#",$studentdata[0]->first_name, $mailContent );
					$mailContent = str_replace ( "#click here#","<a href='".base_url()."home/ebookDownload/user'>clica aquí</a>", $mailContent );
					}
					else
					{
						$mailContent = str_replace ( "#firstname#",$publicdata[0]->name, $mailContent );
						$mailContent = str_replace ( "#click here#","<a href='".base_url()."/home/ebookDownload/public'>clica aquí</a>", $mailContent );
					}
					
					
					
				}
				else
				{*/
				if($langId==3)
				$click = "aquí";
				else
				$click = "here";
				
				if($subscriDetails['user_id']!=0)
				{
				$mailContent = str_replace ( "#firstname#",$studentdata[0]->first_name, $mailContent );
				$mailContent = str_replace ( "#click here#","<a href='".base_url()."/home/ebookDownload/user'>".$click."</a>", $mailContent );
				$tomail = $studentdata['0']->email;
				}
				else
				{
				$mailContent = str_replace ( "#firstname#",$publicdata[0]->name, $mailContent );
				$mailContent = str_replace ( "#click here#","<a href='".base_url()."/home/ebookDownload/public'> ".$click."</a>", $mailContent );
				$tomail = $publicdata['0']->email;
				}
				//}
					  
				 /* if($subscriDetails['user_id']!=0)
					$tomail = $studentdata['0']->email;
					else
					$tomail = $publicdata['0']->email;*/
					
					$this->email->from('info@eventtrix.com', 'Team Eventtrix');
					$this->email->to($tomail); 
					$this->email->cc(''); 
					$this->email->bcc(''); 
					$this->email->subject($emailSubject);
					$this->email->message($mailContent);	
				    $sent=$this->email->send();	
					if($sent==TRUE){
				  $mailing_histrory=array();
				  $mailing_histrory['email_id']=$tomail;
				  $mailing_histrory['user_id']=$ebData['user_id'];
				  $mailing_histrory['template_id']=$mailing_template_id;
				  $mailing_histrory['mailing_date']=date("Y-m-d");
				  $this->common_model->add_email_history($mailing_histrory);
					}
					$this->session->unset_userdata('sessionId');					
					
					redirect('home/EbookBought','refresh');
						
		}
	}
	
	 function findEbookPrice($count)
	{
		if($count==0)
		{
			$ebDetails['fake_price']=0;
			$ebDetails['amount']=0;
			$ebDetails['cur_symbol']=$this->currSymbol;
		}
		else
		{
			if(!$this->session->userdata('student_logged_in')){
			$prodectId = $this->common_model->getProdectId('ebooks_public','',$count);
			}
			else{
			$prodectId = $this->common_model->getProdectId('ebook_offer','',$count);	
			}
			$ebookPrice =$this->common_model->getProductFee($prodectId,$this->currId);
			$ebDetails['fake_price']=$ebookPrice['fake_amount'];
			$ebDetails['amount']=$ebookPrice['amount'];
			$ebDetails['cur_symbol']=$ebookPrice['currency_symbol'];
		}
		//echo "<pre>";print_r($ebookPrice);
		echo json_encode($ebDetails);  	
	}	

	
	function deleteEbookFromCart($tempId)
	{
		$this->load->model('ebook_model');
						
		$ebookTempId = $this->ebook_model->deleteEbookCart($tempId);
		
		//redirect('deeps_home/EbookCart','refresh');
		redirect('home/EbookCart','refresh');
		
	
	
	}	
	
	function get_student_progress($course_id)
	{
		if(!$this->session->userdata('student_logged_in')){
		  redirect('home');
		}
		$stud_id=$this->session->userdata['student_logged_in']['id'];
		$course_status = $this->user_model->get_student_course_status($course_id,$stud_id); 	
		$course_name = $this->common_model->get_course_name($course_id); 
			
		$course_passed = 1; // for checking every module is passed or not
				
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
		
		
		return $progress;
		
	
	
	}
	
	function course_details_all($courseId)
	{
          echo "here";exit;
          $course_details =$this->user_model->get_coursedetails($courseId, $this->language);
          echo "<pre>";print_r($course_details);exit;
          $syllabus = $this->user_model->get_course_syllabus($courseId);
          $content['course_name'] = $course_details[0]->course_name;
          $content['course_summary'] = $course_details[0]->course_summary;
          $content['home_image'] = $course_details[0]->home_image;

          $content['home_video'] = $course_details[0]->home_video;
          $content['course_detail'] = $course_details[0]->course_description;
          $content['course_syllabus'] = $syllabus[0]->syllabus_text;
          $content['module_count'] =$syllabus[0]->module_count;
          $content['student_count'] =$syllabus[0]->student_count;
          $content['study_hours'] =$syllabus[0]->study_hours;
          $content['course_id'] = $courseId;
          $content['course_details'] = $course_details;
          $course_detail = $this->course_model->get($courseId,$this->currId);
          echo "<pre>";print_r($course_detail);exit;
          $author_id=unserialize($course_detail[0]->author_id);
          //$author_details=$this->manage_admin_model->get_author_details($author_id);
          $course_template_details = $this->course_model->get_course_templates_details($courseId,$course_detail[0]->course_details_template_id);
          echo "<pre>";print_r($course_template_details);exit;
          $course_testimonial_ids = explode(',',$course_detail[0]->course_testimonial_ids);
          $testimonials = $this->course_model->get_course_testimonials($course_testimonial_ids);


          if(isset($this->session->userdata['student_logged_in']))
          {

               $product_id = $this->common_model->getProdectId('extra_course');
          }
          else
          {              
               $product = $this->user_model->get_product_id($courseId);

               foreach ($product as $value) {
                $product_id = $value->id;
              }
          }
          
          $price_details_array = $this->common_model->getProductFee($product_id,$this->currId);
          
          foreach($price_details_array as $price_det)
          {
               $content['amount']= $price_details_array['amount'];
               $content['currency_symbol']= $price_details_array['currency_symbol'];
               $content['currency_code']=  $price_details_array['currency_code'];
               $content['currency_id']=  $price_details_array['currency_id'];
               
          }



          $data['translate'] = $this->tr_common; 
          $content['course_detail']           = $course_detail;       
          //$data['view'] = 'coursedetail_all';
          //$data['view'] = 'Courses/details_template_'.$course_detail[0]->course_details_template_id;
          $data['content'] = $content;
          $this->load->view('user/template_outer',$data);
		
	}


     

	
	function login()
	{
		$content = array();
        $content['language']=$this->language;
		$content['tr_user_name']=$this->user_model->translate_('user_name');
        $content['tr_password']=$this->user_model->translate_('password');
		
	    $content['metaKeys'] = "";
	    $content['metaDesc'] = "";
		
        $data['translate'] = $this->tr_common;
		
		//echo "<pre>";print_r($content);exit;
        $data['view'] = 'login_new';
        $data['content'] = $content;
        $this->load->view('user/template_outer',$data);
	}
	
	public function newsletter_signup()
	{
		  $home_newsletter=array();
		  $home_newsletter['date']=date("Y-m-d");
		$email =$home_newsletter['email']= $_POST['newsletter_email'];
		$result = $this->user_model->getUserByEmail($email);
		$return_arr['mail_sent'] = 0;
		$return_arr['user_exist'] = 0;
		$return_arr['show_msg'] = 0;
		$return_arr['msg'] = "";
		$return_arr['msg_class'] = "";
				
		if(!empty($result))
		{
			foreach($result as $row)
			{
			 
			 $user_id=$row->user_id;
			 $first_name=$row->first_name;
			 $last_name=$row->last_name;
			 }
			 $is_user="YES";
		 }
		else{
		$is_user="NO"; 
		 }
		 
	     $unsubscribed['subscribed']=1;
		 $users_newsletter['newsletter']=1;
		 if($is_user=="YES"){
		 $this->db->where('user_id', $user_id);
         $this->db->update('users',$users_newsletter);
		 }
		 $table="newsletter";
		 $newsletter_details=$this->campaign_model->fetch_user_details_byEmail($email,$table);
		 if(!empty($newsletter_details)){
			 
			 foreach($newsletter_details as $row_newsletter){
			 $newsletter_id=$home_newsletter['newsletter_id']=$row_newsletter->id;
			 }
			 $this->common_model->newsletter_update($newsletter_id,$unsubscribed);
			
		 }
		 else{
			
			
			$newsletter_array['email']=$home_newsletter['email']=$email;
			$country_id=$this->common_model->get_country_id_BYname($this->con_name);
			$newsletter_array['country']=$country_id;
			$newsletter_array['subscribed']=1;
			//$newsletter_array['site']="bartender_barista";
			$newsletter_array['date']=date("Y-m-d");
			 if($is_user=="YES"){
				 $newsletter_array['first_name']=$first_name;
			$newsletter_array['last_name']=$last_name;
			$newsletter_array['sourse']="student";
			$newsletter_array['user']="yes";
			 }
			 else{
				 $newsletter_array['first_name']="";
			$newsletter_array['last_name']="";
			 $newsletter_array['sourse']="home_news_letter";
			 $newsletter_array['user']="no";
			 }
			$home_newsletter['newsletter_id']=$this->common_model->add_newsletter($newsletter_array);
			
		 }
		 $insert_id = $this->common_model->insert_to_table("newsletter_home",$home_newsletter);
			
			    $return_arr['user_exist'] = 1;
				$return_arr['show_msg'] = 1;
				$return_arr['msg'] = "You have successfully subscribed to our newsletter. Thank you.";	
				$return_arr['msg_class'] = "alert-info";
				
		echo json_encode($return_arr);	
					
	}
	
	function enroll_popup($course_id="0"){
		
		
	   
	   if(isset($course_id) && $course_id!="0"){
	   $coursename=$this->common_model->get_course_name($course_id);
	   }
	   else
	   {
		 $coursename="all courses";
	     $course_id=1;	
	   }
	   
	   if(isset($_POST['get_in_name']))
		{
		  $studentdata  = array();
		  $studentdata['name'] = $content['get_name'] = ucfirst(strtolower($_POST['get_in_name']));
		  $studentdata['email'] = $content['get_email'] = $_POST['get_email'];
		  $studentdata['contact_number'] = $content['get_contact_no'] = $_POST['get_contact_no'];
		  //echo "<pre>".$coursename; print_r($studentdata); exit;
		  $this->user_model->add_get_in_touch($studentdata);
		  
		  $this->load->library('email');
   		 $this->load->model('email_model');
    
   		 $row_new = $this->email_model->getTemplateById('get_in_touch',$this->language);
    	foreach($row_new as $row1)
        {
       
		   $emailSubject = $row1->mail_subject;
		   $mailContent = $row1->mail_content;
		   $mailing_template_id=$row1->id;
		   
        }
      $mailContent = str_replace ( "#name#",$studentdata['name'], $mailContent );
      $mailContent = str_replace ( "#course_name#",$coursename, $mailContent); 
	   $mailContent = str_replace ( "#email#",$studentdata['email'], $mailContent );
	  $mailContent = str_replace ( "#contact_number#",$studentdata['contact_number'], $mailContent );
      

     //$tomail = 'info@internationalopenacademy.com';
	$tomail = 'sarathkochooli@gmail.com';
   	 
	 
	 	$this->email->from($studentdata['email']);
       $this->email->to($tomail); 
       $this->email->cc(''); 
       $this->email->bcc(''); 
       
       $this->email->subject($emailSubject);
       $this->email->message($mailContent); 
       
       $sent=$this->email->send();
				  if($sent==TRUE){
				  $mailing_histrory=array();
				  $mailing_histrory['email_id']=$tomail;
				  $mailing_histrory['template_id']=$mailing_template_id;
				  $mailing_histrory['mailing_date']=date("Y-m-d");
				  $this->common_model->add_email_history($mailing_histrory);
				   $return_arr['email_sent'] = 1;
				  }
				  else{
					$return_arr['email_sent'] = 0;  
				  }
     			 
	  
		echo json_encode($return_arr);	
	 
		}
   }
	
	function courses($cat_id ='')
	{

		$content = array();
          $course_array=array();
          $category_array=array();
          $price_details_array = array();
          if($cat_id ==4) // All courses
          {
               $cat_id ='';
          }
          $content['cat_id'] = $cat_id;
          
          if($cat_id=="")
          {

               $content['base_course']= $this->user_model->get_courses_order($this->language);
          }
        else if($cat_id=="learning-program")
        {
          $content['course_bundles']= $this->user_model->get_allbundles($this->language);

          if(!empty($content['course_bundles']))
               {
               $i=0;
               foreach($content['course_bundles'] as $bundle)
               {    

                    $content['bundle_id'][$i]=$bundle->id;
                    $content['bundle_name'][$i]=$bundle->bundle_name;
                    $content['short_description'][$i]=$bundle->short_description;
                    $content['bundle_image'][$i]=$bundle->image;
                    $content['bundle_url'][$i]=$bundle->pageUrl;
                    $where=array('type'=>'bundle','item_id'=>$bundle->id);
                    $product = $this->course_model->get_product($where); 
                    
                     $product_id = $product[0]->id;
                    
                    $content['price_details'] = $this->common_model->getProductFee($product_id,$this->currId);
                    $content['bundle_price'][$i]=$content['price_details']['amount'];
                    $content['bundle_currency_code'][$i]=$content['price_details']['currency_code'];
                    $content['bundle_currency_symbol'][$i]=$content['price_details']['currency_symbol'];
                    $i++;
                    
               }

               }

               //$content['bundle_array'] = $bundle_array;
        }

          else
          {
               $content['base_course']=$this->user_model->get_courses_by_category_for_courses($cat_id,$this->language);
               $content['base_course_bundles']=$this->user_model->get_allbundles_by_category($this->language,$cat_id);

          }
          
          if(!empty($content['base_course_bundles']))
               {
               $i=0;
               foreach($content['base_course_bundles'] as $bundle)
               {    

                    $content['base_bundle_id'][$i]=$bundle->id;
                    $content['base_bundle_name'][$i]=$bundle->bundle_name;
                    $content['base_short_description'][$i]=$bundle->short_description;
                    $content['base_bundle_image'][$i]=$bundle->image;
                    $content['base_bundle_url'][$i]=$bundle->pageUrl;
                    $where=array('type'=>'bundle','item_id'=>$bundle->id);
                    $product = $this->course_model->get_product($where); 
                    
                     $product_id = $product[0]->id;
                    
                    $content['price_details'] = $this->common_model->getProductFee($product_id,$this->currId);
                     $content['base_bundle_price'][$i]=$content['price_details']['amount'];
                     $content['base_bundle_currency_code'][$i]=$content['price_details']['currency_code'];
                     $content['base_bundle_currency_symbol'][$i]=$content['price_details']['currency_symbol'];
                    $i++;
               }

               } 
               
          if(!empty($content['base_course']))
          {
          $i=0;
          foreach($content['base_course'] as $row){
          $course_array['course_id'][$i]=$row->course_id;
          $course_array['course_name'][$i] = $course_name_array[] =$row->course_name;
          $course_array['course_summary'][$i]=$row->course_summary;
          $course_array['course_hours'][$i]=$row->course_hours;
          $course_array['course_status'][$i]=$row->course_status;
          $course_array['course_description'][$i]=$row->course_description;
          $course_array['campus_image'][$i]=$row->campus_image;
          
          
          $course_array['home_image'][$i]=$row->home_image;

                    $product = $this->user_model->get_product_id($row->course_id);
                    foreach($product as $row_prod){
                    $content['price_details']=$this->common_model->getProductFee($row_prod->id,$this->currId);
                    $price_details_array['amount'][$i]=$content['price_details']['amount'];
                    $price_details_array['currSymbol'][$i]=$this->currSymbol;
             }
            $i++;
          }
          }
          $content['category_details']=$this->user_model->get_category_all_details_active();
          $i=0;
          foreach($content['category_details'] as $row_cat){
          $category_array['category_name'][$i]=$row_cat->category_name;
          $category_array['id'][$i]=$row_cat->id;
          $i++;
          }
          $content['course_array'] = $course_array;
          $content['category_array'] = $category_array;
          $content['price_details_array'] = $price_details_array;          
        $content['language']=$this->language;
          //$content['tr_online_courses']=$this->user_model->translate_('tr_online_courses');       
            
        $data['translate'] = $this->tr_common;
          
          $seo_details = $this->common_model->get_seo_details('courses',$this->language);
          $content['pageTitle'] = $seo_details[0]->pageTitle;
          $content['metaDesc'] = $seo_details[0]->metaDesc;
          $content['metaKeys'] = $seo_details[0]->metaKeys;
          
          
        $data['view'] = 'courses';
          $data['content'] = $content;
        $this->load->view('user/template_outer',$data);	
		
	}
	
	function get_user_non_enrolled_courses_ajax()
	{
		
		$user_id = $this->session->userdata['student_logged_in']['id'];			
		$enrolled_course_ids = array();
		$course_array = array();
		$enrolled_courses = $this->user_model->get_courses_student($user_id);
		foreach($enrolled_courses as $en_course)
		{
			$enrolled_course_ids[] = $en_course->course_id;			
		}		
		$course_array = $this->sales_model->get_course_by_language($this->language,$enrolled_course_ids,'all');	
		
		if(empty($course_array))
		{
			$data['err_msg']= 1;					 
			echo json_encode($data); 
			exit;
		}
		else
		{
			$user_type = "'user'";
			$i=0;
			$close_html_flag = true;
			$html = '<section class="process-section enrol_clumns">
			<h5 class="enroll_title orange_bgs">'.$this->user_model->translate_('step_3_select_course').'</h5>
<div class="clear"></div>
<div class="cols-xs-12 col-sm-6"><ul class="enrol_ul">';
			foreach($course_array as $course)
			{
				$i=$i+1;			
				if(count($course_array)>1 && $i>ceil(count($course_array)/2))
				{
					
					if($close_html_flag)
					{
						$html .='</ul>
						</div>
						<div class="cols-xs-12 col-sm-6">
						<ul class="enrol_ul">';
						$close_html_flag = false;
					}
				}
				
				if($course->course_status==1)
				{
					$html .= '<li  style="cursor:pointer"><span id="user_course_add_remove_'.$course->course_id.'" onclick="add_remove_course('.$course->course_id.','.$user_type.')" class="chb_user_course_span"><i class="fa fa-circle-o"></i></span>'.$course->course_name.'</li>';
				}
				else
				{
					$html .= '<li><span id="user_course_add_remove_'.$course->course_id.'" class="chb_user_non_active_course_span"><i class="fa fa-ban"></i></span>'.$course->course_name.'</li>';
				}
					
				
				$html .= '<input type="checkbox"  id="user_course_id_'.$course->course_id.'"  name="user_course_id[]" value="'.$course->course_id.'" class="chb_user_course_check_box hidden_elements" >';		
			}
								
		   $html .='</ul></div>
		   </section>'; 	
		  
		   $data['err_msg']= 0;		  
	       $data['course_html']= $html;			 
		   echo json_encode($data); 
		   exit;
		   		
		}
	}
	
	function enroll_updation($user_id){
		
		$this->tr_common['tr_first_name']   =$this->user_model->translate_('First_Name'); 
		$this->tr_common['tr_last_name']   =$this->user_model->translate_('last_Name'); 		
		$this->tr_common['tr_email']   =$this->user_model->translate_('Email'); 
		$this->tr_common['tr_confirm_email']   =$this->user_model->translate_('confirm_email'); 
		$this->tr_common['tr_telephone'] =$this->user_model->translate_('Telephone');
		$this->tr_common['tr_contact_num']   =$this->user_model->translate_('contact_num'); 		
		$this->tr_common['tr_mobile']   =$this->user_model->translate_('mobile'); 
		$this->tr_common['tr_male']   =$this->user_model->translate_('male'); 		
		$this->tr_common['tr_female']   =$this->user_model->translate_('female'); 
		$this->tr_common['tr_course']   =$this->user_model->translate_('course'); 
		$this->tr_common['tr_comment']   =$this->user_model->translate_('comment'); 
		
			
		$this->tr_common['tr_create_stylist_id']   =utf8_decode($this->user_model->translate_('create_stylist_id')); 
		$this->tr_common['tr_create_secret_code']   =$this->user_model->translate_('create_secret_code'); 		
		$this->tr_common['tr_confirm_secret_code']   =$this->user_model->translate_('confirm_secret_code'); 
		$this->tr_common['tr_gender']   = $this->user_model->translate_('gender'); 
		$this->tr_common['tr_dob']   =$this->user_model->translate_('dob'); 
		$this->tr_common['tr_house_name_no']   =$this->user_model->translate_('house_name_no'); 	
		$this->tr_common['tr_road_street']   =$this->user_model->translate_('road_street'); 	
		$this->tr_common['tr_address']   =$this->user_model->translate_('address_line'); 	
		$this->tr_common['tr_city']   =$this->user_model->translate_('city'); 	
		
		$this->tr_common['tr_zip_code']   =utf8_decode($this->user_model->translate_('zip_code')); 
		$this->tr_common['tr_country']   =$this->user_model->translate_('Country'); 	
		$this->tr_common['tr_v_code']   =$this->user_model->translate_('v_code'); 	
		$this->tr_common['tr_voucher_code_note_text']   =$this->user_model->translate_('voucher_code_note_text'); 		
		$this->tr_common['tr_course']   =$this->user_model->translate_('course'); 	
		$this->tr_common['tr_terms_agree']   =$this->user_model->translate_('terms_agree'); 	
	    $this->tr_common['tr_next']   =$this->user_model->translate_('_next'); 
		 $this->tr_common['tr_SIGN_UP']   =$this->user_model->translate_('SIGN_UP'); 
		$this->tr_common['tr_reason_to_buy']   =$this->user_model->translate_('reason_to_buy'); 
		$this->tr_common['tr_email_mismatch']   =$this->user_model->translate_('email_mismatch'); 
		$this->tr_common['tr_confirm_password']   =$this->user_model->translate_('confirm_secret_code'); 
		$this->tr_common['tr_user_already_exist']   =$this->user_model->translate_('user_exists'); 
		
		$this->tr_common['tr_valid_email_required']   	   =$this->user_model->translate_('valid_email_required');
		
		
		$content=array();
		$content['country']=$this->user_model->get_country();
		$content['states']=$this->user_model->get_states();
		
		$stud_details= $this->user_model->get_stud_details($user_id);
		foreach($stud_details as $row){
			
		$content['fname']=$row->first_name;
		$content['lname']=$row->last_name;
		$content['email']=$row->email;	
			
		}
		if($this->input->post('contact_no')){
			
		$studentdata['dob'] = $content['dob_check'] = date('Y-m-d',strtotime($this->input->post('dob_check')));
		//echo $studentdata['dob'];exit;
		
		 // $studentdata['username'] = $content['username'] = $this->input->post('username');
		  //$content['pword'] = $this->input->post('pword');
		 // $studentdata['password']= $this->encrypt->encode($this->input->post('password'));
		  $studentdata['gender'] = $content['gender'] = $this->input->post('gender');
		  $studentdata['contact_number'] = $content['contact_no'] = $this->input->post('contact_no');
		  $studentdata['house_number'] = $content['house_no'] = $this->input->post('house_no');		 
		  $studentdata['address'] = $content['address'] = $this->input->post('address');
          $studentdata['street'] = $content['street'] = $this->input->post('street');
		  $studentdata['zipcode'] = $content['zip_code'] = $this->input->post('zip_code');
          $studentdata['city'] = $content['city'] = $this->input->post('city');
		  $studentdata['country_id'] = $content['country_set'] = $this->input->post('country_id');
		  	if($this->session->userdata['home_enroll']=="home")
		    $studentdata['reg_type'] ='payment_home';
			elseif($this->session->userdata['home_enroll']=="rep")
		  	$studentdata['reg_type'] ='rep_package';
		  if($studentdata['country_id']=='12'){
		  $us_states= $content['state_set'] = $this->input->post('state');
		  }
		  if(isset($us_states)&& $us_states!=''){
			 
			 $state_details =$this->user_model->get_statename($us_states);
			  foreach($state_details as $row_states){
				 $studentdata['us_states']=$row_states->name_short;   
			  }
			 
		  }	
		  if($this->input->post('newsletter')!='')
		  {
		  	$studentdata['newsletter'] = $content['newsletter'] = $this->input->post('newsletter');	  
		  }
		  else
		  {
			  $studentdata['newsletter'] = $content['newsletter'] = 'yes';	
		  }
		  
		   $content['terms'] = $this->input->post('terms');
		  $studentdata['reg_date']=date("Y-m-d");
		  
		  
		    /*$this->form_validation->set_rules('gender', 'Gender', 'required');
		  $this->form_validation->set_rules('country_id', 'Country', 'callback_validate[Country]');
		  $this->form_validation->set_rules('username', 'UserName', 'required');
		  $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|callback_chkpword[Password]');
		  $this->form_validation->set_rules('house_no', 'House No', 'required');
		$this->form_validation->set_rules('street', 'Street', 'required');
		  $this->form_validation->set_rules('city', 'City', 'required');
		  $this->form_validation->set_rules('zip_code', 'Zip-Code', 'required');
		  
		  $this->form_validation->set_rules('policy_terms', 'Acceptance of Terms', 'required');*/
		  
		 /* $this->form_validation->set_rules('contact_no', 'Contact-No', 'trim|required|xss_clean');
		  //var_dump($this->form_validation->run());exit;
		  if($this->form_validation->run() == TRUE)
		  {    */     
		      
					
					$this->user_model->student_update($studentdata,$user_id);
					if(isset($this->session->userdata['home_enroll'])){
		$this->session->unset_userdata('home_enroll');	
		}
				$redirectPath = "home/couponSuccess/".$user_id;
				redirect($redirectPath, 'refresh');
		/*	}*/
			
			
			
		
		}
		
		
		 $content['language']=$this->language;
		//$content['tr_online_courses']=$this->user_model->translate_('tr_online_courses');		
		  $content['user_id']=$user_id;
        $data['translate'] = $this->tr_common;
		
		
			$content['pageTitle'] = "";
			$content['metaDesc'] = "";
			$content['metaKeys'] = "";
		
		
        $data['view'] = 'enroll_updation';
		$data['content'] = $content;
        $this->load->view('user/template_outer',$data);
		
		
		
	}
	function validate_user_login_ajax_post()
	{
		$user_name = $_POST['user_name'];
		$password = $_POST['password'];
		
				
		$result = $this->user_model->login($user_name, $password);
		if($result)
		{
			$sess_array = array();
			foreach($result as $row)
			{ 
               if ($row->status!=1) 
			   {                
                  $this->form_validation->set_message('check_database','student is not active');
                  return FALSE;
                }
                else
				{
					$sess_array = array('id' => $row->user_id,'username' => $row->username );
					$this->session->set_userdata('student_logged_in', $sess_array);
					$sess_array1 = array('language' => $row->lang_id);
					$this->session->set_userdata($sess_array1);
					
					$login_detail['last_login'] =  date('Y-m-d H:i:s');				
					$this->db->where('user_id',$row->user_id);
					$this->db->update('users',$login_detail);	
									
                    $user_name = $this->common_model->get_user_name($row->user_id);
					
					$data['err_msg']= 0;
					$data['user_name'] = 'Hi, '.$user_name;	
					$data['username'] = $row->username;			
					echo json_encode($data); 
					exit;
					
                }
     		}
     					
   		}
		else
   		{						
     		 $this->form_validation->set_message('check_database','Invalid stylist ID or code');
			 
    	 	 $data['err_msg']= 1;			 
			 echo json_encode($data); 
			 exit;
   		}
				
		
			
		
	}
	
	
	function login_updation($user_id,$username,$password){
		
		if($user_id!='' && $username!='' && $password!=''){
		  $content=array();
		  $studentdata=array();
		  $studentdata['username'] =$username;
		  $studentdata['password']= $this->encrypt->encode($password);
		  
		  $this->user_model->student_update($studentdata,$user_id);
		            $data['err_msg']= 0;			
					echo json_encode($data); 
					exit;
		}
		else{
			
		 $data['err_msg']= 1;			
					echo json_encode($data); 
					exit;	
		}
		
		
	}
	
	function remove_ebook_public_cart()
	{
			$tempDetails['session_id']=$this->session->userdata('cart_public_session_id');			
			$ebookTempId = $this->ebook_model->removeEbookCart($tempDetails);
			$data['err_msg']= 0;			
			$data['count'] = 0;			
			echo json_encode($data); 
			exit; 		
	}
	function login_user()//used for logging in buy onother course users
	{
		//echo "here";exit;
		
		if(isset($this->session->userdata['student_logged_in']))
		{
			
			$return_fn = "home/select_course";
			redirect($return_fn);
		}
		
		if(!empty($_POST))
		{
			//echo "<pre>";print_r($_POST);
			$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
			$this->form_validation->set_rules('pass_word', 'Password', 'trim|required|xss_clean|callback_check_database');
			$content['username'] = $this->input->post('username');
			
			if($this->form_validation->run() == TRUE)
			{
				
				$return_fn = "home/select_course";
				redirect($return_fn);
			}
		}
			
		$content['translate'] = $this->tr_common;
		$content['view'] = 'user_login';
		$title['pageTitle'] = 'Login';
		$content['content'] = $title;
		$content['no_login'] = 1;
		$this->load->view('user/template_outer',$content); 
			
	}
	function select_course()
	{
	
		$content['base_course']=$this->course;
		if(!($this->session->userdata('enrolling_rep_code')))
		{
			if(!($this->session->userdata['student_logged_in']['id']))
		    redirect("home/login_user");	
		}
		if(isset($this->session->userdata['student_logged_in']['id']))		
		{	
			$user_id = $this->session->userdata['student_logged_in']['id'];
		$lang_id  = $this->common_model->get_user_lang_id($user_id);
		$sess_array1 = array('language' => $lang_id);
		$this->session->set_userdata($sess_array1);
		}
		$content['course']=$this->user_model->get_course($this->language);
			
		$enrolled_course_ids = array();
	  $course_array = array();
	  $coure_list_html = '';
		if(isset($this->session->userdata['student_logged_in']['id']))		
		{	
			
			$enrolled_courses = $this->user_model->get_courses_student($user_id);
			
			foreach($enrolled_courses as $en_course)
			{
				$enrolled_course_ids[] = $en_course->course_id;			
			}
			//echo "<pre>";print_r($enrolled_course_ids);exit;		
			$content['user_user_name'] = 'Hi, '.$this->common_model->get_user_name($user_id);			
		}
		//echo "<pre>";print_r($enrolled_course_ids);exit;
		$content['course_array'] = $this->sales_model->get_course_by_language($this->language,$enrolled_course_ids,'all');
		
		if(!isset($this->session->userdata['student_logged_in']['id']))	
		{
		if(isset($_POST['course_submit'])){
			$courseID = $this->input->post('non_user_course_id');
			$content['course_id']= $courseID[0];
			if(!empty($courseID)){
				
				$sess_array_course = array('rep_course_id' => $content['course_id']);
		        $this->session->set_userdata($sess_array_course);
				$return_fn = "rep/rep_code_2";
				redirect($return_fn);
			}
			else{
				$content['err_msg_course'] = "Please select a course.";
			}
		}
		}
		else
		{
			if(isset($_POST['non_user_course_id']))
			{
			
			$non_user_course_id = $content['course_set'] = $this->input->post('non_user_course_id');
			$user_id = $this->session->userdata['student_logged_in']['id'];
			$redirectPath = "home/package_buy_another_course/".$user_id."/".$non_user_course_id[0];
			redirect($redirectPath, 'refresh');
				
			}
			
		}		
	//$langId = $this->language;	
	//echo "here";exit;
	$top_menu_base_courses = $this->user_model->get_courses($this->language);	
		
		if(empty($top_menu_base_courses))
		{
			$top_menu_base_courses = $this->user_model->get_courses(4);	
		}
	
	$data['top_menu_base_courses'] 			= $top_menu_base_courses;
	$data['translate'] = $this->tr_common;
	
		$data['view'] = 'select_course_view';
		$data['content'] = $content;
		$this->load->view('user/template_outer',$data);
		  
	}	
	
		function package_buy_another_course()
	{
		
		$content = array();
		$course_details = array();
		//$content = $this->uri->uri_to_assoc(3);
		$content['stud_id']=$user_id= $this->uri->segment(3);
		$content['cour_id']=$course_id= $this->uri->segment(4);
		$coursename=$this->user_model->get_coursename($content['cour_id']);
    	$temp_course_id = $content['cour_id'];
		
		
		
		$this->tr_common['tr_upgrade_your_account_avail_only_registration']  = $this->user_model->translate_('upgrade_your_account_avail_only_registration');
		$this->tr_common['tr_save_over']    = $this->user_model->translate_('save_over');
		$this->tr_common['tr_buy_now_for']  = $this->user_model->translate_('buy_now_for');
		$this->tr_common['tr_read_more']  	= $this->user_model->translate_('read_more');
		$this->tr_common['tr_add_to_bag_2']  	= $this->user_model->translate_('add_to_bag_2');
		$this->tr_common['tr_remove_2']  	= $this->user_model->translate_('remove_2');
		$this->tr_common['tr_complete_registraion']  	= $this->user_model->translate_('complete_registraion');
		
		$this->tr_common['tr_package_terms_condetions']  	= $this->user_model->translate_('package_terms_condetions');
				
		$this->tr_common['tr_personal_details']    = $this->user_model->translate_('personal_details');		
		$this->tr_common['tr_packages']    		= $this->user_model->translate_('packages');
		$this->tr_common['tr_payment_details']     = $this->user_model->translate_('payment_details');
		$this->tr_common['tr_registration']     = $this->user_model->translate_('registration');
		$this->tr_common['tr_total_price']     = $this->user_model->translate_('total_price');
		$this->tr_common['tr_SIGN_UP']   =$this->user_model->translate_('SIGN_UP'); 
				
	   
		$content['language']=$this->language;
		$curr_code=$this->user_model->get_currency_id($this->con_name);
			
		$curr_id= $this->currId;	
		$content['course_count']=1;
			
			foreach ($coursename as $key) {
			$content['course_name']=$key->course_name ;
			$content['val_days']=$key->course_validity;
			}
			$course_product_id = $this->common_model->getProdectId('extra_course',$content['cour_id']);
			if($course_product_id==false)
			$course_product_id = $this->common_model->getProdectId('extra_course');
		
		$price_details_array = $this->common_model->getProductFee($course_product_id,$this->currId);
			
		foreach($price_details_array as $price_det)
		{
			$content['course_fee']		= $price_details_array['amount'];
			$content['currency_symbol']   = $price_details_array['currency_symbol'];
			$content['currency_code']	 = $price_details_array['currency_code'];
			$content['curr_id']		   = $price_details_array['currency_id'];
			
		}
		
		if(!$this->session->userdata('cart_session_id'))
		{	
		    session_regenerate_id();	
			$sess_array = array('cart_session_id' => session_id()); 
			
			$this->session->set_userdata($sess_array);	
		
			$product_details = $this->common_model->get_product_details($course_product_id);			
			$product_price_details = $this->common_model->getProductFee($course_product_id,$this->currId);			
//********************* Start ( Insertion to sales cart main,sales_cart_items,sales_cart_item_details,sales_user_agents) ***********************************
			$cart_main_insert_array = array("session_id"=>$this->session->userdata('cart_session_id'),"user_id"=>$user_id,"source"=>'extra_course_package',"item_count"=>1,"total_cart_amount"=>$product_price_details['amount'],"currency_id"=>$product_price_details['currency_id']);
		    $cart_main_id = $this->common_model->insert_to_table("sales_cart_main",$cart_main_insert_array);	
			
			$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$course_product_id,"item_amount"=>$product_price_details['amount'],"currency"=>$product_price_details['currency_id']);
			
			
			$cart_items_id = $this->common_model->insert_to_table("sales_cart_items",$item_details_array);
			$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>$product_details[0]->type,"selected_item_ids"=>$course_id);
			
			$item_id = $this->common_model->insert_to_table("sales_cart_item_details",$items_array);	
			
			$user_agent_data = array();			
			$user_agent_data['cart_main_id']  = $cart_main_id;
			$user_agent_data['session_id'] 	= $this->session->userdata('cart_session_id');
			$user_agent_data['os'] 			= $this->agent->platform();
			$user_agent_data['browser'] 	   = $this->agent->agent_string();		
			$this->common_model->insert_to_table("sales_user_agents",$user_agent_data);
//********************* End ( Insertion to sales cart main,sales_cart_items,sales_cart_item_details,sales_user_agents) **************************************
			$this->session->unset_userdata('cart_session_type');			
			$sess_array = array('cart_session_type' => $product_details[0]->type);			
			$this->session->set_userdata($sess_array);	
		
			$currency_symbol = $this->common_model->get_currency_symbol_from_id($product_price_details['currency_id']);
			
		
		}
		
		$added_pack_id = array();
		
		$package_details = $this->package_model->get_packages();
		foreach($package_details as $pak_det)
		{			
			$product_id = $this->common_model->getProdectId('package',$pak_det->id,1);
			$package_fees[$pak_det->id] = $this->common_model->getProductFee($product_id,$this->currId);
			$package_product_id[$pak_det->id] = $product_id;
		}
		
		if($this->session->userdata('cart_session_id'))
		{
	
			$cart_main_details = $this->sales_model->get_cart_main_details_package($this->session->userdata('cart_session_id'),$this->session->userdata('student_temp_id'));
			foreach($cart_main_details as $cart_main)
			{
				$data['cart_count'] = $cart_main->item_count;
				$data['cart_amount'] = $cart_main->total_cart_amount;
				$data['currency_symbol'] = $this->common_model->get_currency_symbol_from_id($cart_main->currency_id);
			}
			
			
			if(!empty($cart_main_details))
			{
				$cart_main_id = $cart_main_details[0]->id;		
				$package_cart_contents = $this->sales_model->check_product_type_exist_in_cart($cart_main_id,'package');
				if(!empty($package_cart_contents))
				{			
					$added_pack_id = explode(",",$package_cart_contents[0]->selected_item_ids);					
				}				
				
			}		
			
		}
		else
		{
			$data['cart_count'] = 0;
			$data['cart_amount'] = 0;
			$data['currency_symbol'] = $this->common_model->get_currency_symbol_from_id($this->currId);
			
		}	
		if(!$this->session->userdata('package_applying_course'))
		{
			$sess_array = array('package_applying_course' =>$content['cour_id']); 				
			$this->session->set_userdata($sess_array);	
		}	
		
		$content['package_details']=$package_details;
		$content['package_fees']=$package_fees;
		
		$content['product_id']=$product_id;
		$content['package_product_id']=$package_product_id;
		$content['added_pack_id'] = $added_pack_id;
		
		$content['curr_id'] = $this->currId;
		$content['lang_id'] = $this->language;
		
		$data['translate'] = $this->tr_common;
		$data['view'] = 'package_buy_another_course';
		$data['content'] = $content;
		$this->load->view('user/template_outer',$data);
		
	}
	
	function buy_another_course_check_out($stud_id,$course_id)
	{
		$content = array();
		$content['stud_id']= $stud_id;
		$content['cour_id']= $course_id;	
		$content['curr_id']=$currency_id = $this->currId;			
		$purchased_item_names = array();
		$product_name = array();		
		$products_in_cart = array();
		$cart_main_details = array();
		$extended_days = '';
		
		$this->tr_common['tr_sl_no'] 	= $this->user_model->translate_('sl_no');		 
		$this->tr_common['tr_options']  = $this->user_model->translate_('options');		 
		$this->tr_common['tr_price'] 	= $this->user_model->translate_('price');		 
		$this->tr_common['tr_remove']   = $this->user_model->translate_('remove');		 
		$this->tr_common['tr_basket_total'] = $this->user_model->translate_('basket_total');		 
		$this->tr_common['tr_continue_shopping'] = $this->user_model->translate_('continue_shopping');		 
		$this->tr_common['tr_secure_checkout'] = $this->user_model->translate_('secure_checkout');	 
		$this->tr_common['tr_shop_cart'] = $this->user_model->translate_('your_shop_basket');
		$this->tr_common['tr_item'] = $this->user_model->translate_('Item');
		$this->tr_common['tr_Product_Name'] = $this->user_model->translate_('Product_Name');
		$this->tr_common['tr_Type'] = $this->user_model->translate_('Type');
		$this->tr_common['tr_apply_your_certificate'] = $this->user_model->translate_('apply_your_certificate');
		$this->tr_common['tr_apply'] = $this->user_model->translate_('apply');
		$this->tr_common['tr_certificate_applied'] = $this->user_model->translate_('certificate_applied');
		$this->tr_common['tr_sales_no_items_in_cart'] = $this->user_model->translate_('sales_no_items_in_cart');
		
		if($this->session->userdata('cart_session_id'))
		{
			$cart_main_details = $this->sales_model->get_cart_main_details_packageby_userid($this->session->userdata('cart_session_id'),$stud_id);
			$currency_id = $cart_main_details[0]->currency_id;
			$currency_symbol = $this->common_model->get_currency_symbol_from_id($cart_main_details[0]->currency_id);
			foreach($cart_main_details as $cart_main)
			{		
				$cart_main_id = $cart_main->id;	
				$coupon_applied = $cart_main->coupon_applied;	
				$coupon_code_applied = $cart_main->coupon_code; 	
				$discount_amount = $cart_main->discount_amount;
				$products_in_cart = $this->sales_model->get_cart_items($cart_main->id);
				
				$q=0;
				if(!empty($products_in_cart))
				{
				foreach($products_in_cart as $prod)
				{
					$purchased_item_names[$q] ='';
					$product_details = $this->common_model->get_product_details($prod->product_id);
					$cart_item_details = $this->sales_model->get_cart_items_details($cart_main_id,$prod->id);
					foreach($cart_item_details as $item_det)
					{
						$selected_items = $item_det->selected_item_ids;
						
						if($item_det->product_type == 'extra_course')
						{
							$course_ids = explode(',',$selected_items);
							
							$product_name[$q] = 'extra course';
							
						/*	echo "<br>In course";*/
							
							for($qq=0;$qq<count($course_ids);$qq++)
							{
								if($purchased_item_names[$q]=='')
								{
									$purchased_item_names[$q] = $this->common_model->get_course_name($course_ids[$qq]); 
								}
								else
								{
									$purchased_item_names[$q] .= ','.$this->common_model->get_course_name($course_ids[$qq]); 
								}
									$course_details=$this->user_model->get_coursename($course_ids[$qq]);							
								foreach ($course_details as $key) {							
								$content['val_days']=$key->course_validity;
								
								$product_image[$q] = 'public/user/outer/cart_img/'.$key->home_image;
								}
							}
							
						}
						else if($item_det->product_type == 'package')
						{
							//$package_id = $selected_items;
							$product_name[$q] = $this->user_model->translate_('package');
												
							$package_details = $this->package_model->fetch_package($selected_items);
							$purchased_item_names[$q] = $package_details[0]->package_name;
							$product_image[$q] = 'public/user/outer/cart/packages/'.$package_details[0]->image_name;
						}
					}
					//$product_name[$q] =  $product_details[0]->type;
					$q++;
				}
				}
			}
			if(!empty($cart_main_details))
			{
				$cart_main_id = $cart_main_details[0]->id;		
				$package_cart_contents = $this->sales_model->check_product_type_exist_in_cart($cart_main_id,'package');
				if(!empty($package_cart_contents))
				{			
					$added_pack_id = $package_cart_contents[0]->selected_item_ids;				
					$products_in_package = explode(',',$this->package_model->get_products_in_package($added_pack_id));				
					$extension_options = $this->common_model->get_product_by_type('extension');	
					
					foreach($extension_options as $ext_opt)
					{
						if(in_array($ext_opt->id,$products_in_package))
						{
							
							$extension_details = $this->sales_model->get_extension_details_by_units($ext_opt->item_id);								
							if($this->session->userdata('language')==3)
							{
								$extended_days = " + ".$extension_details[0]->extension_option_spanish;
							}
							else
							{								
								$extended_days = " + ".$extension_details[0]->extension_option;
							}
							
						}
					}
				}				
				
			}
		}
		else
		{
			$currency_symbol = $this->common_model->get_currency_symbol_from_id($this->currId);
		}
			
		$content['extended_days']=$extended_days;
		
		$data['sales_from'] = 'pacakge_details';		
		$content['purchased_item_names'] = $purchased_item_names;
		$content['products_in_cart']  = $products_in_cart;
		$content['product_name']      = $product_name;
		$content['product_image']  =$product_image;
		$content['currency_id'] = $currency_id;		
		$content['cart_main_details'] = $cart_main_details;
		$content['stud_id'] = $stud_id;	
		
		$data['currency_symbol'] = $currency_symbol;
		$data['translate'] = $this->tr_common;
		$data['view'] = 'buy_another_course_check_out';
        $data['content'] = $content;				
        $this->load->view('user/template_outer',$data);  
	
		
	}
	function apply_coupon_buy_another_course($coupon_code,$currency_id,$type)
	{
		$this->load->model('discount_code_model','',TRUE);		
		
		
		$coupon_details = $this->discount_code_model->get_coupon_details_from_code($coupon_code,$type);
		
		
		
		/*echo "Herer";
		echo "<pre>";
		print_r($coupon_details);
		exit;*/
		
		if(!empty($coupon_details))
		{
			$data['discount_code'] = $coupon_code;
			$data['discount_code_id'] = $coupon_details[0]->id;
			
			if($this->session->userdata('coupon_applied'))
			{
				/*echo "<pre>";
					print_r($this->session->userdata('coupon_applied_details'));*/
					$data['err_msg']= 1;
					$data['err_type'] = 'Coupon already applied';					
					echo json_encode($data); 
					exit;		
			}
			else
			{
				
				
		
				//$product_id = $this->common_model->getProdectId('extra_course');
				$product_id = $this->common_model->getProdectId('extra_course',$this->session->userdata('package_applying_course'));
				if($product_id==false)
				$product_id = $this->common_model->getProdectId('extra_course');		
				$price_det     = $this->common_model->getProductFee($product_id,$currency_id);
				/*echo "<pre>";
				print_r($price_det);
				exit;*/
				
				$amount = $price_det['amount'];
				$currency_symbol = $price_det['currency_symbol'];
				$currency_code = $price_det['currency_code'];
				
				$discount_value = $coupon_details[0]->discount_value;
					if($coupon_details[0]->discount_type=='percentage')
					{
						//@round($cart_items_total_amount,2);
						//$amount = $cart_items[0]->item_amount;
//						number_format($number, 2, '.', '');
												
						$reduced_amount = $amount - round(( ($amount * $discount_value) / 100 ),2);					
						$discount_amount = $amount-$reduced_amount;
					}
					elseif($coupon_details[0]->discount_type=='price')
					{
					//	$amount = $cart_items[0]->item_amount;
					
				$currency_id_discount_value = $this->discount_code_model->get_amount_for_discount_code($coupon_details[0]->id,$currency_id);
						if($currency_id_discount_value!='')
						{
						
						$reduced_amount = $amount-($currency_id_discount_value);
						$discount_amount = $amount-$reduced_amount;
							if($reduced_amount<=0)
							{
								$data['err_msg']= 1;
								$data['err_type'] = 'Something went wrong. Plaese contact info@eventtrix.com';					
								echo json_encode($data); 
								exit;	
							}
						
						}
						else
						{
							$data['err_msg']= 1;
							$data['err_type'] = 'Currency not supported. Plaese contact info@eventtrix.com';					
							echo json_encode($data); 
							exit;	
							
						}
					}
					$user_id = $this->session->userdata['student_logged_in']['id'];
					$user_agent_data = array();			
					$user_agent_data['user_id']       = $user_id;
					$user_agent_data['course_id'] 	 = $this->session->userdata('current_course_selected');
					$user_agent_data['discount_id']   = $coupon_details[0]->id;				
					$user_agent_data['os'] 			= $this->agent->platform();
					$user_agent_data['browser'] 	   = $this->agent->agent_string();
			
			
					$this->common_model->insert_to_table("discount_codes_user_agents",$user_agent_data);
					$cart_main_details = $this->sales_model->get_cart_main_details_packageby_userid($this->session->userdata('cart_session_id'),$user_id);
					foreach($cart_main_details as $cart_main)
					{		
					$cart_main_id = $cart_main->id;			
					
								$cart_main_update_array['total_cart_amount']=($cart_main->total_cart_amount)-$discount_amount;
								$cart_main_update_array['discount_amount']=$discount_amount;
								$cart_main_update_array['coupon_applied']="yes";
								$cart_main_update_array['coupon_code']=$coupon_code;
					            $this->sales_model->main_cart_details_update_total($cart_main->id,$cart_main_update_array);	
						$cart_item= $this->sales_model->get_cart_items_by_cart_main_id($cart_main_id,$product_id);
						foreach ($cart_item as $row_item){
							$update_item_array['discount_amount']=$discount_amount;
							$this->sales_model->update_cart_item($row_item->id,$update_item_array);
						}
					}
						
					
					$sess_array = array('coupon_applied' => true);
					$this->session->set_userdata($sess_array);
				
					$data['err_msg']		 = 0;
					$data['amount'] 		  = $reduced_amount;
					$data['discount_amount'] = $discount_amount;					
					$data['currency_symbol'] = $currency_symbol;
					
					$data['currency_code']   = $currency_code;
					
					$sess_array = array('coupon_applied_details' => $data);
				    $this->session->set_userdata($sess_array);
					
					/*echo "<pre>";
					print_r($_SESSION);
					exit;*/
					
					echo json_encode($data); 
					exit;		
					
					
			}
		}
		
					$data['err_msg']= 1;
					$data['err_type'] = 'Coupon not applicable';					
					echo json_encode($data); 
					exit;	
		
	}
	
	function extend_course_check_out($stud_id)
	{
		 $content = array();
		$course_details = array();
		$this->tr_common['tr_sl_no'] 	= $this->user_model->translate_('sl_no');		 
		$this->tr_common['tr_options']  = $this->user_model->translate_('options');		 
		$this->tr_common['tr_price'] 	= $this->user_model->translate_('price');		 
		$this->tr_common['tr_remove']   = $this->user_model->translate_('remove');		 
		$this->tr_common['tr_basket_total'] = $this->user_model->translate_('basket_total');	
		$this->tr_common['tr_order_total'] = $this->user_model->translate_('order_total');	
		$this->tr_common['tr_course_validity'] = $this->user_model->translate_('course_validity');			 
		$this->tr_common['tr_continue_shopping'] = $this->user_model->translate_('continue_shopping');
		$this->tr_common['tr_shopping_cart'] = $this->user_model->translate_('shopping_cart');				 
		$this->tr_common['tr_secure_checkout'] = $this->user_model->translate_('secure_checkout');	 
		$this->tr_common['tr_shop_cart'] = $this->user_model->translate_('your_shop_basket');
		$this->tr_common['tr_item'] = $this->user_model->translate_('Item');
		$this->tr_common['tr_Product_Name'] = $this->user_model->translate_('Product_Name');
		$this->tr_common['tr_Type'] = $this->user_model->translate_('Type');
		$this->tr_common['tr_apply_your_certificate'] = $this->user_model->translate_('apply_your_certificate');
		$this->tr_common['tr_apply'] = $this->user_model->translate_('apply');
		$this->tr_common['tr_certificate_applied'] = $this->user_model->translate_('certificate_applied');
		$this->tr_common['tr_sales_no_items_in_cart'] = $this->user_model->translate_('sales_no_items_in_cart');
		$this->tr_common['tr_your_shop_basket'] = $this->user_model->translate_('your_shop_basket');
		
		$this->tr_common['tr_camp_days'] =$this->user_model->translate_('camp_days');
		
		
		
		$this->tr_common['tr_1_personal_information'] = $this->user_model->translate_('1_personal_information');
		$this->tr_common['tr_2_upgrade_packages'] = $this->user_model->translate_('2_upgrade_packages');
		$this->tr_common['tr_3_review_and_payment'] = $this->user_model->translate_('3_review_and_payment');
		$this->tr_common['tr_4_confirmation'] = $this->user_model->translate_('4_confirmation');
		
		
		
		
		//$content = $this->uri->uri_to_assoc(3);
		$content['stud_id']=$user_id= $this->uri->segment(3);
		$content['opt_id']=$this->input->post('opt_value');
		if(!$this->session->userdata('opt_id')){
    	$sess_opt_array = array('opt_id' => $this->input->post('opt_value')); 
		$this->session->set_userdata($sess_opt_array);	
		
		}
		$content['language']=$this->language;
		$curr_code=$this->user_model->get_currency_id($this->con_name);
			
		$curr_id= $this->currId;	
		$product_id = $this->common_model->getProdectId('extension',$this->session->userdata('opt_id'));
		//echo $product_id.'/'.$this->currId;exit;
		$price_details_array = $this->common_model->getProductFee($product_id,$this->currId);
			
		foreach($price_details_array as $price_det)
		{
			$content['course_fee']		= $price_details_array['amount'];
			$content['currency_symbol']   = $price_details_array['currency_symbol'];
			$content['currency_code']	 = $price_details_array['currency_code'];
			$content['curr_id']		   = $price_details_array['currency_id'];
			
		}
	   if(!$this->session->userdata('cart_session_id'))
		{	
		    session_regenerate_id();	
			$sess_array = array('cart_session_id' => session_id()); 
			
			$this->session->set_userdata($sess_array);	
		
			$product_details = $this->common_model->get_product_details($product_id);			
			$product_price_details = $this->common_model->getProductFee($product_id,$this->currId);			
//********************* Start ( Insertion to sales cart main,sales_cart_items,sales_cart_item_details,sales_user_agents) ***********************************
			$cart_main_insert_array = array("session_id"=>$this->session->userdata('cart_session_id'),"user_id"=>$user_id,"source"=>'extension',"item_count"=>1,"total_cart_amount"=>$product_price_details['amount'],"currency_id"=>$product_price_details['currency_id']);
		    $cart_main_id = $this->common_model->insert_to_table("sales_cart_main",$cart_main_insert_array);	
			
			$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$product_id,"item_amount"=>$product_price_details['amount'],"currency"=>$product_price_details['currency_id']);
			
			
			$cart_items_id = $this->common_model->insert_to_table("sales_cart_items",$item_details_array);
			$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>$product_details[0]->type,"selected_item_ids"=>$this->session->userdata('opt_id'));
			
			$item_id = $this->common_model->insert_to_table("sales_cart_item_details",$items_array);	
			
			$user_agent_data = array();			
			$user_agent_data['cart_main_id']  = $cart_main_id;
			$user_agent_data['session_id'] 	= $this->session->userdata('cart_session_id');
			$user_agent_data['os'] 			= $this->agent->platform();
			$user_agent_data['browser'] 	   = $this->agent->agent_string();		
			$this->common_model->insert_to_table("sales_user_agents",$user_agent_data);
//********************* End ( Insertion to sales cart main,sales_cart_items,sales_cart_item_details,sales_user_agents) **************************************
			$this->session->unset_userdata('cart_session_type');			
			$sess_array = array('cart_session_type' => $product_details[0]->type);			
			$this->session->set_userdata($sess_array);	
		
			$currency_symbol = $this->common_model->get_currency_symbol_from_id($product_price_details['currency_id']);
			
		
		}
		
		
		if($this->session->userdata('cart_session_id'))
		{
			//$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('cart_session_id'));	
			$cart_main_details = $this->sales_model->get_cart_main_details_extend($this->session->userdata('cart_session_id'),$user_id);
			//echo $this->session->userdata('cart_session_id').'/'.$user_id;exit;
			//echo "<pre>";print_r($cart_main_details);exit;
			$currency_id = $cart_main_details[0]->currency_id;
			$currency_symbol = $this->common_model->get_currency_symbol_from_id($cart_main_details[0]->currency_id);
			foreach($cart_main_details as $cart_main)
			{		
				$cart_main_id = $cart_main->id;	
				$coupon_applied = $cart_main->coupon_applied;	
				$coupon_code_applied = $cart_main->coupon_code; 	
				$discount_amount = $cart_main->discount_amount;
				$products_in_cart = $this->sales_model->get_cart_items($cart_main->id);
				
				$q=0;
				if(!empty($products_in_cart))
				{
				foreach($products_in_cart as $prod)
				{
					$purchased_item_names[$q] ='';
					
					$content['product_id']=$prod->product_id;
					$product_details = $this->common_model->get_product_details($prod->product_id);
					
					$cart_item_details = $this->sales_model->get_cart_items_details($cart_main_id,$prod->id);
					
					//echo "<pre>";print_r($cart_item_details);exit;
					foreach($cart_item_details as $item_det)
					{
						
						$selected_items = $item_det->selected_item_ids;
						
						//echo "<pre>" ; print_r($item_det->product_type); exit;
						
						if($item_det->product_type == 'course')
						{
							$course_ids = explode(',',$selected_items);
							$product_type[$q]= 'course';
							$product_name[$q] = $this->user_model->translate_('course');
							
						/*	echo "<br>In course";*/
							
							for($qq=0;$qq<count($course_ids);$qq++)
							{
								if($purchased_item_names[$q]=='')
								{
									$purchased_item_names[$q] = $this->common_model->get_course_name($course_ids[$qq]); 
								}
								else
								{
									$purchased_item_names[$q] .= ','.$this->common_model->get_course_name($course_ids[$qq]); 
								}
								
								$course_details=$this->user_model->get_coursename($course_ids[$qq]);							
								foreach ($course_details as $key) {							
								$content['val_days']=$key->course_validity;								
								$product_image[$q] = '/public/user/images/'.$key->campus_image;
								$course_name[$q]=$key->course_name;
								
								}
							}
							
							
							
						}
						else if($item_det->product_type == 'package')
						{
							//$package_id = $selected_items;
							$product_name[$q] = $this->user_model->translate_('package');
							$product_type[$q]= 'package';					
							$package_details = $this->package_model->fetch_package($selected_items);
							$purchased_item_names[$q] = $package_details[0]->package_name;
							$product_image[$q] = '/public/user/cart/packages/'.$package_details[0]->image_name;
						}
						else if($item_det->product_type == 'extension')
						{
							$product_type[$q] = 'extension';
							
							$extension_details = $this->sales_model->get_extension_details_by_units($selected_items);								
							if($this->session->userdata('language')==3)
							{
								$extended_days = " + ".$extension_details[0]->extension_option_spanish;
							}
							else
							{								
								$extended_days = " + ".$extension_details[0]->extension_option;
							}
							
						
						$course_details=$this->user_model->get_coursename($this->session->userdata('course_id'));							
								foreach ($course_details as $key) {							
								$product_image[$q] = '/public/user/images/'.$key->campus_image;
								$course_name[$q]=$key->course_name;
								
								
					      }	
					   }
					}
					//$product_name[$q] =  $product_details[0]->type;
					$q++;
				}
				}
			}
			
		
		}
		if(isset($extended_days))
		$content['extended_days']=$extended_days;
		$content['course_id']=$this->session->userdata('course_id');
		$data['sales_from'] = 'pacakge_details';
		if(isset($purchased_item_names))		
		$content['purchased_item_names'] = $purchased_item_names;
		$content['products_in_cart']  = $products_in_cart;
		if(isset($product_image))
		$content['product_image']  =$product_image;
		if(isset($course_name))
		$content['course_name']  =$course_name;
		if(isset($product_type))
		$content['product_type']  =$product_type;
	//	$content['product_name']      = $product_name;
		$content['currency_id'] = $currency_id;		
		$content['cart_main_details'] = $cart_main_details;
		$content['user_id'] = $user_id;	
		
		$data['currency_symbol'] = $currency_symbol;
		$data['translate'] = $this->tr_common;
		$data['view'] = 'extend_course_check_out';
        $data['content'] = $content;				
        //$this->load->view('user/template_outer_other',$data);  
		$this->load->view('user/template_inner',$data);  
	
		
	}
   
   
public function our_team($id)
	{
		$language = $this->session->userdata('language');
		$content['our_team_div']=$this->user_model->get_our_team_html("details_page",$this->language,$id);
		$content['our_team_members']=$this->user_model->get_our_team_html("home");
		$data['translate'] = $this->tr_common;
        $data['view'] = 'our_team';
        $data['content'] = $content;
        $this->load->view('user/template_outer',$data);
	}

     	
	
}

?>