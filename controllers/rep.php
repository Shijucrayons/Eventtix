<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class rep extends CI_Controller
{

function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper(array('form'));
		$this->load->library('user_agent');
    	$this->load->helper('url');
   		$this->load->database('',true);
    
		$this->load->library('form_validation');
		$this->load->library('encrypt');
		
		$this->load->model('user_model','',TRUE);
		$this->load->model('sales_model','',TRUE);
		$this->load->model('common_model','',TRUE);		
		$this->load->model('rep_model','',TRUE);
		$this->load->model('package_model','',TRUE);
		$this->load->model('campaign_model','',TRUE);
		
	    if($message = $this->session->flashdata('message')){
          $this->flashmessage =$message;}
		$ip = $this->input->ip_address();
		$this->load->library('ip2country_lib');
		$this->con_name = $this->ip2country_lib->getInfo();
		
		if(isset($_POST['username'])&&isset($_POST['password']))
		{
			$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
   			$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_check_database');
			$content['username'] = $this->input->post('username');
			if(!$this->form_validation->run() != FALSE)
			{
				redirect('rep/deals/', 'refresh');
      		}
      
		}
		if(isset($_GET['lang_id'])){
			$newdata = array('language'  => $_GET['lang_id']);
			$this->session->set_userdata($newdata);
		}
		elseif(!$this->session->userdata('language')){
			$newdata = array('language'  => '4');
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
		//---------------common translations --------------------------
		 $this->tr_common['tr_stylist_id'] =$this->user_model->translate_('stylist_id');
         $this->tr_common['tr_code']        =$this->user_model->translate_('style_code');
		 $this->tr_common['tr_return_campus']        =$this->user_model->translate_('tr_return_campus');
		 $this->tr_common['tr_sign_out']        =$this->user_model->translate_('tr_sign_out');
		 $this->tr_common['tr_work_with_us'] =$this->user_model->translate_('work_with_us');
		 $this->tr_common['tr_about_us']   =$this->user_model->translate_('about_us');
  		 $this->tr_common['tr_faq']        =$this->user_model->translate_('faq');  
		 $this->tr_common['tr_SIGN_UP']   =$this->user_model->translate_('SIGN_UP'); 
      	 $this->tr_common['tr_contact_us'] =$this->user_model->translate_('contact_us');
		 $this->tr_common['tr_terms_use']        =$this->user_model->translate_('terms_use');   
    	 $this->tr_common['tr_privacy_policy'] =$this->user_model->translate_('privacy_policy');
		 $this->tr_common['tr_testimonials'] =$this->user_model->translate_('testimonials');
		 $this->tr_common['tr_email']   =$this->user_model->translate_('email'); 
		$this->tr_common['tr_create_secret_code']   =$this->user_model->translate_('create_secret_code'); 	
		$this->tr_common['tr_create_stylist_id']   =utf8_decode($this->user_model->translate_('create_stylist_id')); 	
		$this->tr_common['tr_confirm_secret_code']   =$this->user_model->translate_('confirm_secret_code'); 
		$this->tr_common['tr_email_mismatch']   =$this->user_model->translate_('email_mismatch');
		$this->tr_common['tr_user_already_exist']   =$this->user_model->translate_('user_exists');
		$this->tr_common['tr_password_required']   =$this->user_model->translate_('password_required'); 
		$this->tr_common['tr_weak_password']   =$this->user_model->translate_('weak_password'); 
		$this->tr_common['tr_confirm_password']   =$this->user_model->translate_('confirm_password'); 
		$this->tr_common['tr_password_mismatch']   =$this->user_model->translate_('password_mismatch'); 
		$this->tr_common['tr_Select']   =$this->user_model->translate_('Select'); 
		 $this->tr_common['tr_accreditation'] =$this->user_model->translate_('accreditation'); 
		 //************************************** Anoop ******************************************
		$this->tr_common['tr_tell_us_what_you_think'] =$this->user_model->translate_('tell_us_what_you_think');
		$this->tr_common['tr_Help_Us_Improve'] =$this->user_model->translate_('Help_Us_Improve');
		$this->tr_common['tr_Send_on_your_valuable_feedback'] =$this->user_model->translate_('Send_on_your_valuable_feedback');
		$this->tr_common['tr_Send'] =$this->user_model->translate_('Send');
		//************************************** Anoop ******************************************
		 $this->tr_common['tr_forget_password'] =$this->user_model->translate_('forget_password'); 
	  $this->tr_common['tr_login'] =$this->user_model->translate_('login');
	  $this->tr_common['tr_buy_now'] =$this->user_model->translate_('buy_now');
	  $this->tr_common['tr_get_free_brochure'] =$this->user_model->translate_('get_free_brochure');
	  $this->tr_common['tr_who_we_are'] =$this->user_model->translate_('who_we_are');
	  $this->tr_common['tr_Meet_the_team'] =$this->user_model->translate_('Meet_the_team');
	  $this->tr_common['tr_why_trendimi'] =$this->user_model->translate_('why_trendimi');
	  $this->tr_common['tr_Courses'] =$this->user_model->translate_('Courses');
	  $this->tr_common['tr_Home'] =$this->user_model->translate_('home');
		 $this->tr_common['tr_why_us']   =$this->user_model->translate_('why_us');
  }
  function index()
  {
  }
  
  function rep_code($langid=4)
  {
	  
	    if($this->session->userdata('language')!=$langid)
		{
		$newlangdata = array(
                   'language'  => $langid
               );
			$this->session->set_userdata($newlangdata);
			?>
            <script>
			window.location.reload();
			</script>
            <?
			
		}
	    

	  $content=array();
	  $data=array();
	  if(isset($this->flashmessage)&&$this->flashmessage!=''){
		  $content['flashmessage']=$this->flashmessage;
	  }
	  $content['base_course']=$this->course;
	  $content['country']=$this->user_model->get_country();
	  $content['course']=$this->user_model->get_course($this->language);
	  	if(isset($_POST['submit']))
		{
			
			//echo "<pre>";print_r($_POST);exit;
		    $content['rep_Code'] = $this->input->post('rep_Code');
			
		    
			
			$this->form_validation->set_rules('rep_Code', 'Representative code', 'trim|required');
			
			if($this->form_validation->run())
			{
				
				
				if($this->rep_model->rep_codeValidation($content['rep_Code'])==TRUE){
					
					$this->session->unset_userdata('enrolling_rep_code');
					$sess_array = array('enrolling_rep_code' => $content['rep_Code']);			
					$this->session->set_userdata($sess_array);
					
					
					if($this->session->userdata('language')==6)
					{
						redirect('rep/rep_code_2_fr/'.$content['rep_Code'], 'refresh');
					}
					else
					{
						redirect('rep/rep_code_2/'.$content['rep_Code'], 'refresh');
					}
				}
				else{
				$this->session->set_flashdata('message','Entered code doesnot exist.');	
				redirect('rep/rep_code/'.$this->session->userdata('language'), 'refresh');
				
				}
				
			}
			
			
		}
	  
	  $top_menu_base_courses = $this->user_model->get_courses($this->language);	
		
		if(empty($top_menu_base_courses))
		{
			$top_menu_base_courses = $this->user_model->get_courses(4);	
		}			
		$content['top_menu_base_courses'] 			= $top_menu_base_courses;
		
		
      $content['translate'] = $this->tr_common;
	  $content['pageTitle'] = "";
	  $content['metaDesc'] = "";
	  $content['metaKeys'] = "";
	  $data['view']  = "rep_code";
	  $data['content'] = $content;	 
	  $this->load->view('user/template_outer',$data);
  }
  
   function rep_code_2_fr($rep_Code)
  {
	    $content['rep_Code']=$rep_Code;
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
		$this->tr_common['tr_reason_to_buy']   =$this->user_model->translate_('reason_to_buy'); 	
		
		
		$this->tr_common['tr_first_name_required']  =$this->user_model->translate_('first_name_required'); 		
		$this->tr_common['tr_last_name_required']   =$this->user_model->translate_('last_name_required');		
		$this->tr_common['tr_user_name_required']   =$this->user_model->translate_('user_name_required'); 		
		$this->tr_common['tr_email_required']   	   =$this->user_model->translate_('email_required'); 
		$this->tr_common['tr_required']   	   		 =$this->user_model->translate_('required'); 
		$repDetails = $this->rep_model->getDetails_of_rep_code($rep_Code);
		if($repDetails[0]->course_id==""||$repDetails[0]->course_id==0)
		{
			$content['course_set']=$this->user_model->get_active_courses($this->session->userdata['language']);
			$content['course_count']=0;
		}
		else
		{
			 $course_ids = explode(",",$repDetails[0]->course_id);
			$content['course_count']=count($course_ids);
			for($c=0;$c<count($course_ids);$c++)
			{
				$course_namesArr =$this->user_model->get_coursename($course_ids[$c]);
			    $content['course_set'][$course_ids[$c]]=$course_namesArr[0]->course_name;
			}
		}
		
	    $content['base_course']=$this->course;
		$content['student_status']=$this->student_status;
		$content['topmenu']=$this->menu;
		$content['language']=$this->language;
		$content['country']=$this->user_model->get_country();
		$content['reason_to_buy']=$this->user_model->get_reason_buy($this->language);
		
		if(isset($_POST['fname']))
		{
			
		  $studentdata  = array();
		  $studentdata['first_name'] = $content['fname'] = ucfirst(strtolower($this->input->post('fname')));
		  $studentdata['last_name'] = $content['lname'] =ucfirst(strtolower($this->input->post('lname')));
		  $studentdata['email'] = $content['email'] = $this->input->post('email');
		//  $studentdata['mobile'] = $content['mobile'] = $this->input->post('mobile');
		  $studentdata['username'] = $content['user_name'] = $this->input->post('user_name');
		  $content['pword'] = $this->input->post('pword');
		  $studentdata['password']=$this->encrypt->encode($this->input->post('pword'));
		  $studentdata['gender'] = $content['gender'] = $this->input->post('gender');
		  $studentdata['contact_number'] = $content['contact_no'] = $this->input->post('contact_no');
		  $studentdata['house_number'] = $content['house_no'] = $this->input->post('house_no');		 
		  $studentdata['address'] = $content['address'] = $this->input->post('address');
          $studentdata['street'] = $content['street'] = $this->input->post('street');
		  $studentdata['zipcode'] = $content['zip_code'] = $this->input->post('zip_code');
          $studentdata['city'] = $content['city'] = $this->input->post('city');
		  $studentdata['country_id'] = $content['country_set'] = $this->input->post('country_id');
		  
		   if($this->input->post('reason_id')!='')
		 {
		   $studentdata['reason_id'] = $content['reason_to_buy_set'] = implode(",",$this->input->post('reason_id'));   
		 }
		  //$studentdata['reason_id'] = $content['reason_to_buy_set'] = implode(",",$this->input->post('reason_id')); 
		  $content['course_count']=0;
		  $studentdata['course_id'] = $content['course_set'] = $this->input->post('course_id');
          $studentdata['with_coupon']='yes';
		  $studentdata['coupon_code'] =$rep_Code;
		 
		 
		 
		//  $studentdata['comments'] = $content['comments'] = $this->input->post('comments');
		  $content['terms'] = $this->input->post('terms');
		  $studentdata['reg_date']=date("Y-m-d");
		  
		  $this->form_validation->set_rules('fname', 'First name', 'trim|required');
		  $this->form_validation->set_rules('lname', 'Last Name', 'required');
		  $this->form_validation->set_rules('gender', 'Gender', 'required');
		  $this->form_validation->set_rules('country_id', 'Country', 'callback_validate[Country]');
		  $this->form_validation->set_rules('user_name', 'UserName', 'required');
		  $this->form_validation->set_rules('pword', 'Password', 'required|min_length[6]|callback_chkpword[Password]');
		  $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email|callback_chkemail[E-mail]');
		  //$this->form_validation->set_rules('course_id', 'Course', 'callback_validate[Course]');
		  $this->form_validation->set_rules('house_no', 'House No', 'required');
		  $this->form_validation->set_rules('street', 'Street', 'required');
		  $this->form_validation->set_rules('city', 'City', 'required');
		  $this->form_validation->set_rules('zip_code', 'Zip-Code', 'required');
		  $this->form_validation->set_rules('contact_no', 'Contact-No', 'required');
		  $this->form_validation->set_rules('terms', 'Acceptance of Terms', 'required');
		  //$this->form_validation->set_rules('voucher_code', 'VoucherCode', 'callback_checkVcode');
		  
		  
		 
		  if($this->form_validation->run())
		  {
			  	
			//echo "<pre>";print_r($studentdata);exit;
					$this->user_model->add_student_temp($studentdata);
					$student_id=$this->db->insert_id();
				if($content['course_count']==0)
				{
					$redirectPath = '/rep/payment_2/'.$student_id.'/'.$studentdata['course_id'].'/'.$rep_Code;
				}
				else
				{
					$redirectPath = '/rep/payment_2/'.$student_id.'/'.$rep_Code;
				}
				
					
					redirect($redirectPath, 'refresh');
					
		  }
		

		}
	
	/* ---- Remove these script after pacakge pu live, this is for loading old css  ----- */
	if($this->session->userdata('language')==4)
	{	
	$data['load_old_css'] = true;
	}
			
	/* ---- Remove till here  ----- */
	
	$data['translate'] = $this->tr_common;
	
	$data['view'] = 'rep_enroll';
	$data['content'] = $content;
	$this->load->view('user/outerTemplate',$data);
	
		}
		
	function rep_code_2()
   {
	   
	   if($this->session->userdata('cart_session_id')){
			$this->package_model->clear_current_package_details($this->session->userdata('cart_session_id'),'rep');
		}
	   
	    $content['rep_Code']=$rep_Code=$this->session->userdata('enrolling_rep_code');
	    $this->tr_common['tr_first_name']   =$this->user_model->translate_('First_Name'); 
		$this->tr_common['tr_last_name']   =$this->user_model->translate_('last_Name'); 		
		$this->tr_common['tr_email']   =$this->user_model->translate_('email'); 
		$this->tr_common['tr_confirm_email']   =$this->user_model->translate_('confirm_email'); 
		$this->tr_common['tr_contact_num']   =$this->user_model->translate_('contact_num'); 		
		$this->tr_common['tr_validity_text']   	   		 =$this->user_model->translate_('validity_text');  
		$this->tr_common['tr_camp_days']   	   		 =$this->user_model->translate_('camp_days');
		$this->tr_common['tr_course_validity']   	   		 =$this->user_model->translate_('course_validity');
		$this->tr_common['tr_area']   =$this->user_model->translate_('area'); 
		$this->tr_common['tr_male']   =$this->user_model->translate_('male'); 		
		$this->tr_common['tr_female']   =$this->user_model->translate_('female'); 
		  
		$this->tr_common['tr_personal_details']    = $this->user_model->translate_('personal_details');		
		$this->tr_common['tr_packages']    		= $this->user_model->translate_('packages');
		$this->tr_common['tr_payment_details']     = $this->user_model->translate_('payment_details');
		$this->tr_common['tr_registration']     = $this->user_model->translate_('registration');
		$this->tr_common['tr_total_price']     = $this->user_model->translate_('total_price');
		
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
		$this->tr_common['tr_reason_to_buy']   =$this->user_model->translate_('reason_to_buy'); 	
		
		
		$this->tr_common['tr_first_name_required']  =$this->user_model->translate_('first_name_required'); 		
		$this->tr_common['tr_last_name_required']   =$this->user_model->translate_('last_name_required');		
		$this->tr_common['tr_user_name_required']   =$this->user_model->translate_('user_name_required'); 		
		$this->tr_common['tr_email_required']   	   =$this->user_model->translate_('email_required'); 
		$this->tr_common['tr_email_exists']   	   =$this->user_model->translate_('email_exists'); 
		$this->tr_common['tr_valid_email_required']   	   =$this->user_model->translate_('valid_email_required'); 
		$this->tr_common['tr_required']   	   		 =$this->user_model->translate_('required'); 
		$this->tr_common['tr_confirm_email']   =$this->user_model->translate_('confirm_email'); 
		
		$this->tr_common['tr_1_personal_information'] = $this->user_model->translate_('1_personal_information');
		$this->tr_common['tr_2_upgrade_packages'] = $this->user_model->translate_('2_upgrade_packages');
		$this->tr_common['tr_3_review_and_payment'] = $this->user_model->translate_('3_review_and_payment');
		$this->tr_common['tr_4_confirmation'] = $this->user_model->translate_('4_confirmation');
		
		$this->tr_common['tr_step_1_welcome']   	    =$this->user_model->translate_('step_1_welcome');  
		$this->tr_common['tr_if_you_have_voucher']   =$this->user_model->translate_('if_you_have_voucher');  
		$this->tr_common['tr_redeem_here']   	   =$this->user_model->translate_('redeem_here');  
		$this->tr_common['tr_are_you_registering_for_first_time']   	 =$this->user_model->translate_('are_you_registering_for_first_time');  
		$this->tr_common['tr_step_2_select_course']   	   =$this->user_model->translate_('step_2_select_course');  
		$this->tr_common['tr_step_3_your_details']   	   	=$this->user_model->translate_('step_3_your_details');  
		$this->tr_common['tr_step_2_your_account_details']  =$this->user_model->translate_('step_2_your_account_details');  
		$this->tr_common['tr_step_3_select_course']   	   		 =$this->user_model->translate_('step_3_select_course');  
		$this->tr_common['tr_instructions_and_recommendations'] =$this->user_model->translate_('instructions_and_recommendations');  
		$this->tr_common['tr_like_to_recieve_newsletter']    =$this->user_model->translate_('like_to_recieve_newsletter'); 
		
		
		$repDetails = $this->rep_model->getDetails_of_rep_code($rep_Code);
		if($repDetails[0]->course_id==""||$repDetails[0]->course_id==0)
		{
			$content['course_set']=$this->user_model->get_active_courses($this->session->userdata['language']);
			$enrolled_course_ids = array();
			//$content['course_set'] = $this->sales_model->get_course_by_language($this->session->userdata['language'],$enrolled_course_ids);
			$content['course_count']=0;
		}
		else
		{
			 $course_ids = explode(",",$repDetails[0]->course_id);
			$content['course_count']=count($course_ids);
			for($c=0;$c<count($course_ids);$c++)
			{
				$course_namesArr =$this->user_model->get_coursename($course_ids[$c]);
			    $content['course_set'][$course_ids[$c]]=$course_namesArr[0]->course_name;
			}
		}
		
	    $content['base_course']=$this->course;
		$content['student_status']=$this->student_status;
		$content['topmenu']=$this->menu;
		$content['language']=$this->language;
		$content['country']=$this->user_model->get_country();
		$content['states']=$this->user_model->get_states();
		$content['reason_to_buy']=$this->user_model->get_reason_buy($this->language);
		
		
		
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
		$content['course_array'] = $this->sales_model->get_course_by_language($this->language,$enrolled_course_ids);
		
		if(isset($_POST['fname']))
		{
			
			
		  $studentdata  = array();
		  $studentdata['first_name'] = $content['fname'] = ucfirst(strtolower($this->input->post('fname')));
		  $studentdata['last_name'] = $content['lname'] =ucfirst(strtolower($this->input->post('lname')));
		  $studentdata['email'] = $content['email'] = $this->input->post('email');
	
		  
		  $user_course_id = $content['course_set'] = $this->input->post('non_user_course_id');
		  $studentdata['course_id'] = $content['course_set'] = $this->session->userdata('rep_course_id');
		// $studentdata['newsletter'] = $content['newsletter'] = 'yes';
          $studentdata['with_coupon']='yes';
		  $studentdata['coupon_code'] =$rep_Code;
		 
		  $studentdata['reg_date']=date("Y-m-d");
		  
		  $this->form_validation->set_rules('fname', 'First name', 'trim|required');
		  $this->form_validation->set_rules('lname', 'Last Name', 'required');
		  $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email|callback_chkemail[E-mail]');
		
		  if($this->form_validation->run())
		  {
			  	
			//echo "<pre>";print_r($studentdata);exit;
					$this->user_model->add_student_temp($studentdata);
					$student_id=$this->db->insert_id();
				$enroll_home = array('home_enroll'  => 'rep');
                $this->session->set_userdata($enroll_home);
					$redirectPath = '/rep/package_details/'.$student_id.'/'.$studentdata['course_id'];
				
				/*else
				{
					$redirectPath = '/rep/payment_2/'.$student_id;
				}*/
				
					
					redirect($redirectPath, 'refresh');
					
		  }
		

		}
	
	$data['translate'] = $this->tr_common;
	
	$data['view'] = 'rep_enrollment';	
	$data['content'] = $content;	 
	$this->load->view('user/template_outer',$data);
	
		}
		
	function package_details()
	{		
		$content = array();		
		$pre_user_id = $content['stud_id']= $this->uri->segment(3);		
		$course_id   = $content['cour_id'] = $this->uri->segment(4);	
		$temp_course_id = $content['cour_id'];
		$rep_code    = $this->session->userdata('enrolling_rep_code');
		
		
		
		
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
		
		$this->tr_common['tr_1_personal_information'] = $this->user_model->translate_('1_personal_information');
		$this->tr_common['tr_2_upgrade_packages'] = $this->user_model->translate_('2_upgrade_packages');
		$this->tr_common['tr_3_review_and_payment'] = $this->user_model->translate_('3_review_and_payment');
		$this->tr_common['tr_4_confirmation'] = $this->user_model->translate_('4_confirmation');
			
		$coursename  = $this->user_model->get_coursename($course_id); 
    
		foreach ($coursename as $key) {
		$content['course_name']=$key->course_name ;
		$content['val_days']=$key->course_validity;
		}
	   
		$content['language']=$this->language;
		$curr_code=$this->user_model->get_currency_id($this->con_name);
			
		$curr_id= $this->currId;	
		
		$product_course = $this->user_model->get_product_id($course_id); 
		 foreach ($product_course as $value) {
		  $course_product_id = $value->id;
		}
		
		$repDetails = $this->rep_model->getDetails_of_rep_code($rep_code);
				
		$curr_id= $this->currId;
		$content['currency_symbol'] = $this->common_model->get_currency_symbol_from_id($curr_id);
		$content['currency_code']  = $this->currencyCode;
		$content['curr_id'] = $curr_id;
		if($content['currency_code']=='EUR'){
		$content['course_fee']=$repDetails[0]->price_eur;
		}
		elseif($content['currency_code']=='USD'){
		$content['course_fee']=$repDetails[0]->price_usd;
		}
		elseif($content['currency_code']=='GBP'){
		$content['course_fee']=$repDetails[0]->price_gbp;
		}
		else{
		$content['currency_code'] ='EUR';
		$content['course_fee']=$repDetails[0]->price_eur;
		$content['curr_id'] =1;
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
			
			//$product_price_details = $this->common_model->getProductFee($course_product_id,$this->currId);
			
			/*echo "Product id  ".$product_id;
			echo "<br> Selected items ".$new_selected_values;
			echo "<br>";
			
			echo "<pre>";
			print_r($product_details);
			
			echo "<br>";
			
			echo "<pre>";
			print_r($product_price_details);*/
			
			//$pre_user_id = $content['stud_id'];
			//$course_id = $content['cour_id'];
			
			$cart_main_insert_array = array("session_id"=>$this->session->userdata('cart_session_id'),"pre_user_id"=>$pre_user_id,"source"=>'rep_package',"item_count"=>1,"total_cart_amount"=>$content['course_fee'],"currency_id"=>$content['curr_id']);
			
			//$cart_main_id =1;
  		  
		    $cart_main_id = $this->common_model->insert_to_table("sales_cart_main",$cart_main_insert_array);
			
			$user_agent_data = array();
			
			$user_agent_data['cart_main_id']  = $cart_main_id;
			$user_agent_data['session_id'] 	= $this->session->userdata('cart_session_id');
			$user_agent_data['os'] 			= $this->agent->platform();
			$user_agent_data['browser'] 	   = $this->agent->agent_string();
			
			
			$this->common_model->insert_to_table("sales_user_agents",$user_agent_data);
				
	
									
			$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$course_product_id,"item_amount"=>$content['course_fee'],"currency"=>$content['curr_id']);
			
		
			
			$cart_items_id = $this->common_model->insert_to_table("sales_cart_items",$item_details_array);		
			
			$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>$product_details[0]->type,"selected_item_ids"=>$course_id);
			
			$this->session->unset_userdata('cart_session_type');			
			$sess_array = array('cart_session_type' => $product_details[0]->type);			
			$this->session->set_userdata($sess_array);	
		
			
			$item_id = $this->common_model->insert_to_table("sales_cart_item_details",$items_array);	
			
			$currency_symbol = $this->common_model->get_currency_symbol_from_id($content['curr_id']);
			
		
		}
		
		$added_pack_id = array();
		
		$package_details = $this->package_model->get_packages('non_user');
		
		
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
			
			
			
			if(!empty($cart_main_details))
			{
				foreach($cart_main_details as $cart_main)
				{
					$data['cart_count'] = $cart_main->item_count;
					$data['cart_amount'] = $cart_main->total_cart_amount;
					$data['currency_symbol'] = $this->common_model->get_currency_symbol_from_id($cart_main->currency_id);
				}
				
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
		
		//$content['curr_id'] = $this->currId;
		$content['lang_id'] = $this->language;
		
		$data['translate'] = $this->tr_common;
		$data['view'] = 'package_details_rep';
		$data['content'] = $content;
		$this->load->view('user/template_outer',$data);
		
	}
	
	function read_more_view($lang_id,$curr_id,$stud_id,$cour_id)
	{
		$content = array();
		
		$content['stud_id'] = $stud_id;
		$content['cour_id'] = $cour_id;
		
		$this->tr_common['tr_back']     = $this->user_model->translate_('back');
		$this->tr_common['tr_complete_registraion']  	= $this->user_model->translate_('complete_registraion');		
		$this->tr_common['tr_personal_details']    = $this->user_model->translate_('personal_details');		
		$this->tr_common['tr_packages']    		= $this->user_model->translate_('packages');
		$this->tr_common['tr_payment_details']     = $this->user_model->translate_('payment_details');
		$this->tr_common['tr_registration']     = $this->user_model->translate_('registration');
		
		$this->tr_common['tr_1_personal_information'] = $this->user_model->translate_('1_personal_information');
		$this->tr_common['tr_2_upgrade_packages'] = $this->user_model->translate_('2_upgrade_packages');
		$this->tr_common['tr_3_review_and_payment'] = $this->user_model->translate_('3_review_and_payment');
		$this->tr_common['tr_4_confirmation'] = $this->user_model->translate_('4_confirmation');
		
		$package_read_more = $this->package_model->get_read_more_details($lang_id,$curr_id);
		$content['package_read_more'] = $package_read_more[0]->description;
		
		$data['translate'] = $this->tr_common;
		$data['view'] = 'package_read_more_rep';
		$data['content'] = $content;
		$this->load->view('user/template_outer',$data);
		
	}
	
	function payment_details($course_id,$stud_id)
 	{
		
    $coursename=$this->user_model->get_coursename($course_id);
	$extended_days = '';
	$rep_code    = $this->session->userdata('enrolling_rep_code');
	
	$this->tr_common['tr_complete_registraion_rep']  	= $this->user_model->translate_('complete_registraion_rep');		
	$this->tr_common['tr_personal_details']    = $this->user_model->translate_('personal_details');		
	$this->tr_common['tr_packages']    		= $this->user_model->translate_('packages');
	$this->tr_common['tr_payment_details_rep']     = $this->user_model->translate_('payment_details_rep');
	$this->tr_common['tr_registration']     = $this->user_model->translate_('registration');
    $this->tr_common['tr_course'] =$this->user_model->translate_('course');
		$this->tr_common['tr_amount_rep'] =$this->user_model->translate_('amount_rep');
		$this->tr_common['tr_valid_rep'] =$this->user_model->translate_('valid_rep');
		$this->tr_common['tr_camp_days'] =$this->user_model->translate_('camp_days');
    foreach ($coursename as $key) {
    $content['course_name']=$key->course_name ;
    $content['val_days']=$key->course_validity;
    }
   
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
							$extended_days = " + ".$extension_details[0]->extension_option;
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
	
	
		
	
	$content['stud_id'] = $stud_id;
	$content['course_id'] = $course_id;
	$content['product_id']=$product_id;
	$content['extended_days']=$extended_days;
	
	$data['translate'] = $this->tr_common;
    $data['view'] = 'payment_details_rep';
    $data['content'] = $content;
    $this->load->view('user/outerTemplate',$data);

  
 
 	}
		
		function payment_2()
 	{
		 
 		$temp_id = $this->uri->segment(3);
		$content['temp_course_id']=$temp_course_id = $this->uri->segment(4);
		$content['rep_Code']=$rep_Code = $this->uri->segment(5);
		$content['student_temp'] = $temp_id;
		
		//echo $temp_id."<br>-------".$temp_course_id;exit;
		//echo $vouchercode ;
		$repDetails = $this->rep_model->getDetails_of_rep_code($rep_Code);
		if($repDetails[0]->course_id=="" || $repDetails[0]->course_id==0)
		{
			//$content['course_set']=$this->input->post('course');
			$content['course_count']=0;
			$tempArray = $this->user_model->get_student_temp($content['student_temp']);
			$courseId[] =  $temp_course_id;
			$coursename =$this->user_model->get_coursename($courseId[0]);
			foreach($coursename as $cname)
			{
			$content['course_name_rep'][]=$cname->course_name ;
			$content['val_days'][]=$cname->course_validity;
			}
		}
		else
		{
			$courseId = explode(",",$voucherDetails[0]->course_id);
			$content['course_count']=count($courseId);
			for($c=0;$c<count($courseId);$c++)
			{
				$coursename =$this->user_model->get_coursename($courseId[$c]);
			    foreach($coursename as $cname)
				{
				$content['course_name_rep'][]=$cname->course_name ;
				$content['val_days'][]=$cname->course_validity;
				}
			}
		}
	
   
    $content['language']=$this->language;
   	$curr_code=$this->user_model->get_currency_id($this->con_name);
		
	$curr_id= $this->currId;
	$content['currency_code'] = $this->currencyCode;
	$content['curr_id'] = $curr_id;
	if($content['currency_code']=='EUR'){
	$content['course_fee']=$repDetails[0]->price_eur;
	}
	elseif($content['currency_code']=='USD'){
	$content['course_fee']=$repDetails[0]->price_usd;
	}
	elseif($content['currency_code']=='GBP'){
	$content['course_fee']=$repDetails[0]->price_gbp;
	}
	else{
	$content['currency_code'] ='EUR';
	$content['course_fee']=$repDetails[0]->price_eur;
	$content['curr_id'] =1;
	}
	//echo $content['curr_id'];exit;
	
	/* ---- Remove these script after pacakge pu live, this is for loading old css  ----- */
		
	$data['load_old_css'] = true;	
			
	/* ---- Remove till here  ----- */
 
	$data['translate'] = $this->tr_common;
    $data['view'] = 'rep_Code_Payment';
    $data['content'] = $content;
    $this->load->view('user/template',$data);

  
 
 	}
		
	function process_reg_rep()
	{
		$this->load->model('course_model','',TRUE);
		$this->load->model('payment_model','',TRUE);
		
		$userId =$this->uri->segment(3);
		$paymentId = $this->uri->segment(4);
		$courseId = $this->uri->segment(5);
		$rep_Code = $this->uri->segment(6);
		
		if(isset($paymentId))
		{
			$langId = $this->course_model->get_lang_course($courseId);
			$dateNow =date('Y-m-d');
			$product_id=$this->common_model->getProdectId('course',$courseId);
			
			 $insert_data=array("user_id"=>$userId,"course_id"=>$courseId,"type"=>'rep',"date_applied"=>$dateNow,"product_id"=>$product_id,"payment_id"=>$paymentId);		 
		  	$this->user_model->insertQuerys("user_subscriptions",$insert_data);		
			
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
				$studentdata['country_id'] = $row->country_id;
				$studentdata['reason_id'] = $row->reason_id;
				$studentdata['newsletter'] = $row->newsletter;
				$studentdata['reg_date'] = $dateNow;
				$studentdata['lang_id'] = $langId;
				$studentdata['reg_type'] = 'rep_code';
				$studentdata['status']='1';
				$studentdata['us_states']=$row->us_states;
			}
			$user_id = $this->user_model->add_student($studentdata);
			//**********************************start newsletter updation ***************************************
			$stud_details=$this->user_model->get_student_details($user_id);
			foreach($stud_details as $row_stud){
				$email=$row_stud->email;
				$first_name=$row_stud->first_name;
				$last_name=$row_stud->last_name;
				
				$newsletter=$row_stud->newsletter;
			}
			$unsubscribed['first_name']=$first_name;
			$unsubscribed['email']=$email;
			$country_id=$this->common_model->get_country_id_BYname($this->con_name);
			$unsubscribed['country']=$country_id;
			$unsubscribed['user']="yes";
			//$unsubscribed['sourse']="student";
			$unsubscribed['subscribed']=$newsletter;
			$table="newsletter";
		   $newsletter_details=$this->campaign_model->fetch_user_details_byEmail($email,$table);
		   if(!empty($newsletter_details)){
			 foreach($newsletter_details as $row_newsletter){
			 $newsletter_id=$row_newsletter->id;
			 $sourse=$row_newsletter->sourse;
			 }
			 if($sourse!="student"){
			 $unsubscribed['user']="converted";
			 $unsubscribed['converted_date']=date("Y-m-d");
			 }
			 
			 $this->common_model->newsletter_update($newsletter_id,$unsubscribed);
		 }
		 else{
			
			$newsletter_array['first_name']=$first_name;
			$newsletter_array['email']=$email;
			$country_id=$this->common_model->get_country_id_BYname($this->con_name);
			$newsletter_array['country']=$country_id;
			$newsletter_array['subscribed']=$newsletter;
			$newsletter_array['user']="yes";
			$newsletter_array['sourse']="mini_course";
			$newsletter_array['date']=date("Y-m-d");
			$this->common_model->add_newsletter($newsletter_array);
		 }
		//**********************************end newsletter updation ***************************************	
			$rep_id= $this->user_model->get_rep_idBy_rep_code($rep_Code);
			//echo $rep_id;exit;
			$date_rep=date("Y-m-d");
			$rep_data['user_id']=$user_id;
			$rep_data['rep_id']=$rep_id;
			$rep_data['course_id']=$courseId;
			$rep_data['rep_code']=$rep_Code;
			$rep_data['date_rep']=$date_rep;
			
			$this->user_model->add_rep_details($rep_data);
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
					$mailing_template_id = $row1->id;
				}
				if($langId==3)
				{
				 	$mailContent = str_replace ( "#firstname#",$studentdata['first_name'], $mailContent );
					/*$mailContent = str_replace ( "#click here#","<a href='http://staging.trendimi.com/newversion/home/studentActivation/".$en_studId."'>clica aquí</a>", $mailContent );
					
					$mailContent = str_replace ( "#actlink#","http://staging.trendimi.com/newversion/home/studentActivation/".$en_studId." ",$mailContent );*/
					$mailContent = str_replace ( "#url#", "<a href='staging.trendimi.com/newversion'>Trendimi</a>", $mailContent );
					$mailContent = str_replace ( "#username#", $studentdata['username'], $mailContent );
					$mailContent = str_replace ( "#password#", $this->encrypt->decode($studentdata['password']), $mailContent );
				}
				else
				{
					$mailContent = str_replace ( "#firstname#",$studentdata['first_name'], $mailContent );
					/*$mailContent = str_replace ( "#click here#","<a href='http://staging.trendimi.com/newversion/home/studentActivation/".$en_studId."'>click here</a>", $mailContent );
					
					$mailContent = str_replace ( "#actlink#","http://staging.trendimi.com/newversion/home/studentActivation/".$en_studId." ",$mailContent );*/
					$mailContent = str_replace ( "#url#", "<a href='staging.trendimi.com/newversion'>Trendimi</a>", $mailContent );
					$mailContent = str_replace ( "#username#", $studentdata['username'], $mailContent );
					$mailContent = str_replace ( "#password#", $this->encrypt->decode($studentdata['password']), $mailContent );
				}
				  
				  
					$tomail = $studentdata['email'];
					
					$this->email->from('info@trendimi.com', 'Team Trendimi');
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
			redirect('home/couponSuccess/'.$user_id,'refresh');
		}
				
			
		}
		
	}
	
	function login_user_all_data($user_id)
 	{
		 $this->language = $this->session->userdata('language');
		 
		 $users_array = $this->user_model->get_student_details($user_id);
		 $sales_cart_main_details=$this->sales_model->get_paid_cart_details($user_id);
		 $user_agent_details=$this->rep_model->get_user_agent_details($sales_cart_main_details[0]->id);
	     $enrollments_array=$this->user_model->get_courses_student($user_id);
		 echo "---------------***********************User details***********************---------------<br />";
         echo "<pre>";print_r($users_array);
		 echo "---------------***********************User agent details***********************---------------<br />";
		 echo "<pre>";print_r($user_agent_details);
		  echo "---------------***********************Sales cart main details***********************---------------<br />";
		 echo "<pre>";print_r($sales_cart_main_details);
		 echo "---------------***********************Enroll details***********************---------------<br />";
		 echo "<pre>";print_r($enrollments_array);exit;
		 
		$content['language']=$this->language;
		$curr_code=$this->user_model->get_currency_id($this->con_name);
		$curr_id= $this->currId;
		$content['currency_code'] = $this->currencyCode;
		$content['curr_id'] = $curr_id;
	
 
 	}
	
	function rep_code_validation($rep_code){
		
		$data=array();
		
		if($this->rep_model->rep_codeValidation($rep_code)==TRUE){
					
					$this->session->unset_userdata('enrolling_rep_code');
					$sess_array = array('enrolling_rep_code' => $rep_code);			
					$this->session->set_userdata($sess_array);
					$data['success']="1";
					$data['msg']="";
					
				}
				else{
				    $data['success']="0";
					$data['msg']="Entered code doesn't exist";
				
				}
				
	  echo json_encode($data);	
	  
	  
	}
	
}