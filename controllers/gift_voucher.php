<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//session_start(); //we need to call PHP's session object to access it through CI
class gift_voucher extends CI_Controller
{
 	 
	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper(array('form'));		
		$this->load->library('encrypt');
		$this->load->library('user_agent');
    	$this->load->helper('url');
    	$this->load->database('',true);
		$this->load->library('form_validation');
		$this->load->model('campaign_model','',TRUE);
		$this->load->model('email_model','',TRUE);
		$this->load->model('voucher_code_model','',TRUE);
		$this->load->model('gift_voucher_model','',TRUE);
		$this->load->model('user_model','',TRUE);
		$this->load->model('ebook_model','',TRUE);
		$this->load->model('sales_model','',TRUE);
		$this->load->model('certificate_model','',TRUE);
		$this->load->model('package_model','',TRUE);	
		
		
		if($popup_message = $this->session->flashdata('popup_message')){
          $this->flashmessage =$popup_message;
    	}
		if($popup_message_public = $this->session->flashdata('popup_message_public')){
          $this->flashmessage_public =$popup_message_public;
     	}
		/*$this->load->library('ip2country_lib');
		$this->con_name = $this->ip2country_lib->getInfo();*/

		// fetching from freegeoip
		//$this->load->library('ip2country');	
		/*$country_details = $this->ip2country->get_geoip();
		$this->con_name  = $country_details['country_name'];*/
          /*
		if(!$this->session->userdata('ip2country_name'))
		{
			$country_details = $this->ip2country->get_geoip();
			$this->con_name  = $country_details['country_name'];

			$country_data = array('ip2country_name'  => $country_details['country_name']);
			$this->session->set_userdata($country_data);
		} 
		else
		{
			$this->con_name  = $this->session->userdata('ip2country_name');
		}
		*/
		$ip = $this->input->ip_address();		
   
		if(isset($_POST['username'])&&isset($_POST['password']))
		{
			$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
   			$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_check_database');
			$content['username'] = $this->input->post('username');
			if(!$this->form_validation->run() == FALSE)
			{
				redirect('coursemanager/campus', 'refresh');
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
		$curr_code=1;
		//$curr_code=$this->user_model->get_currency_id($this->con_name);

		if($curr_code!==1)
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
		
		$this->tr_common['tr_our_story'] =$this->user_model->translate_('our_story');
		$this->tr_common['tr_No_courses_to_buy']   =$this->user_model->translate_('No_courses_to_buy');
		$this->tr_common['tr_meet_our_team'] =$this->user_model->translate_('meet_our_team');
		$this->tr_common['tr_meet_our_team_desc'] =$this->user_model->translate_('meet_our_team_desc');
		$this->tr_common['tr_return_campus_head'] =$this->user_model->translate_('return_campus');
		$this->tr_common['tr_sign_out_camp'] =$this->user_model->translate_('sign_out_camp');
		$this->tr_common['tr_camp_days'] =$this->user_model->translate_('camp_days');
		$this->tr_common['tr_comment'] =$this->user_model->translate_('comment');
		$this->tr_common['tr_depth_modules'] =$this->user_model->translate_('depth_modules');
		$this->tr_common['tr_study_tools'] =$this->user_model->translate_('study_tools');
		$this->tr_common['tr_step_by_step_video'] =$this->user_model->translate_('step_by_step_video');
		$this->tr_common['tr_full_course_accreditation'] =$this->user_model->translate_('full_course_accreditation');
		$this->tr_common['tr_Select'] =$this->user_model->translate_('Select');
		$this->tr_common['tr_male'] =$this->user_model->translate_('male');
		$this->tr_common['tr_female'] =$this->user_model->translate_('female');
           $this->tr_common['tr_eventrix'] =$this->user_model->translate_('eventrix');
		$this->tr_common['tr_sign_up'] =$this->user_model->translate_('sign_up');
		$this->tr_common['tr_learn_more_description_text'] =$this->user_model->translate_('learn_more_description_text');
		$this->tr_common['tr_copyright'] =$this->user_model->translate_('copyright');
		$this->tr_common['tr_cosmetics_for_pets'] =$this->user_model->translate_('cosmetics_for_pets');
		$this->tr_common['tr_dog_grooming_tools'] =$this->user_model->translate_('dog_grooming_tools');
		$this->tr_common['tr_video_tut_description'] =$this->user_model->translate_('video_tut_description');
		$this->tr_common['tr_learn_more_desc'] =$this->user_model->translate_('learn_more_desc');
		$this->tr_common['tr_buy_now_holly'] =$this->user_model->translate_('buy_now_holly');
		$this->tr_common['tr_enroll_now'] =$this->user_model->translate_('enroll_now');
		$this->tr_common['tr_videos'] =$this->user_model->translate_('videos');
		$this->tr_common['tr_syllabus'] =$this->user_model->translate_('syllabus');
		$this->tr_common['tr_syllabus_new'] =$this->user_model->translate_('syllabus_new');
		
		$this->tr_common['tr_welcome_holly'] =$this->user_model->translate_('welcome_holly');
		 $this->tr_common['tr_forget_password'] =$this->user_model->translate_('forget_password');
		 $this->tr_common['tr_accredited_online_course'] =$this->user_model->translate_('accredited_online_course');		
		 $this->tr_common['tr_stylist_id']      =$this->user_model->translate_('stylist_id');
         $this->tr_common['tr_code']            =$this->user_model->translate_('style_code');
		 $this->tr_common['tr_return_campus']   =$this->user_model->translate_('tr_return_campus');
		 $this->tr_common['tr_sign_out']        =$this->user_model->translate_('tr_sign_out');
		 $this->tr_common['tr_sign_in']        =$this->user_model->translate_('sign_in');
		 $this->tr_common['tr_ebook']        =$this->user_model->translate_('ebook');
		$this->tr_common['tr_login'] =$this->user_model->translate_('login'); 	
		
		$this->tr_common['tr_about_us']   =$this->user_model->translate_('about_us');
		$this->tr_common['tr_faq']        =$this->user_model->translate_('faq');		 
    	$this->tr_common['tr_contact_us'] =$this->user_model->translate_('contact_us');
		$this->tr_common['tr_learn_more']   =$this->user_model->translate_('Learn_More'); 	  	 
     	$this->tr_common['tr_change_photo']   =$this->user_model->translate_('change_foto'); 	  	 
		$this->tr_common['tr_edit_details'] = $this->user_model->translate_('edit_your_account_details');
		$this->tr_common['tr_change_password'] = $this->user_model->translate_('change_password');
		$this->tr_common['tr_help_center'] = $this->user_model->translate_('help_center');		
		$this->tr_common['tr_extend_course'] = $this->user_model->translate_('extend_course');
		$this->tr_common['tr_certificate'] = $this->user_model->translate_('certificate');
		$this->tr_common['tr_contact_us'] = $this->user_model->translate_('contact_us');
  	    $this->tr_common['tr_fitting_room'] =$this->user_model->translate_('fitting_room');
	   
		$this->tr_common['tr_why_us']   =$this->user_model->translate_('why_us');
		
		$this->tr_common['tr_tele_phone']        =$this->user_model->translate_('tele_phone');		 
		$this->tr_common['tr_terms_use']        =$this->user_model->translate_('terms_use');		 
    	$this->tr_common['tr_privacy_policy'] =$this->user_model->translate_('privacy_policy');
		$this->tr_common['tr_course'] =$this->user_model->translate_('course');
		$this->tr_common['tr_amount'] =$this->user_model->translate_('amount');
		$this->tr_common['tr_valid'] =$this->user_model->translate_('valid');
		$this->tr_common['tr_address'] =$this->user_model->translate_('address');
		$this->tr_common['tr_Email'] =$this->user_model->translate_('Email');
		$this->tr_common['tr_password'] =$this->user_model->translate_('password');
		$this->tr_common['tr_username'] =$this->user_model->translate_('username');
		$this->tr_common['tr_mobile'] =$this->user_model->translate_('mobile');
		$this->tr_common['tr_confirm_password'] =$this->user_model->translate_('confirm_password');
		
		$this->tr_common['tr_course_info'] = $this->user_model->translate_('course_info');
		$this->tr_common['tr_next']   =$this->user_model->translate_('_next');
		$this->tr_common['tr_course_progress'] = $this->user_model->translate_('course_progress');
		$this->tr_common['tr_course_progress'] = $this->user_model->translate_('course_progress');
		$this->tr_common['tr_more_info'] =$this->user_model->translate_('more_info');
		$this->tr_common['tr_my_marks'] =$this->user_model->translate_('my_marks_new');
		$this->tr_common['tr_study_time_remaining'] =$this->user_model->translate_('study_remaining');
		$this->tr_common['tr_days'] =$this->user_model->translate_('camp_days'); 	
		$this->tr_common['tr_login'] =$this->user_model->translate_('login');
		$this->tr_common['tr_learn_more'] =$this->user_model->translate_('learn_more');
		$this->tr_common['tr_featured_course'] =$this->user_model->translate_('featured_course');
		$this->tr_common['tr_learn_how_to_expertly_care'] =$this->user_model->translate_('learn_how_to_expertly_care');
		$this->tr_common['tr_animal_talk'] =$this->user_model->translate_('animal_talk');
		$this->tr_common['tr_animal_talk_description'] =$this->user_model->translate_('animal_talk_description');
		$this->tr_common['tr_pet_care_guru'] =$this->user_model->translate_('pet_care_guru');
		$this->tr_common['tr_pet_care_guru_description'] =$this->user_model->translate_('pet_care_guru_description');
		$this->tr_common['tr_work_with_pets'] =$this->user_model->translate_('work_with_pets');
		$this->tr_common['tr_work_with_pets_description'] =$this->user_model->translate_('work_with_pets_description');
		$this->tr_common['tr_animal_kingdom'] =$this->user_model->translate_('animal_kingdom');
		$this->tr_common['tr_animal_kingdom_description'] =$this->user_model->translate_('animal_kingdom_description');
		$this->tr_common['tr_we_love_pets'] =$this->user_model->translate_('we_love_pets');
		$this->tr_common['tr_we_love_pets_description'] =$this->user_model->translate_('we_love_pets_description');
		$this->tr_common['tr_happy_to_help'] =$this->user_model->translate_('happy_to_help');
		$this->tr_common['tr_happy_to_help_description'] =$this->user_model->translate_('happy_to_help_description');
		$this->tr_common['tr_giving_back'] =$this->user_model->translate_('giving_back');
		$this->tr_common['tr_giving_back_description'] =$this->user_model->translate_('giving_back_description');
		$this->tr_common['tr_student_have_to_say'] =$this->user_model->translate_('student_have_to_say');
		$this->tr_common['tr_going_through_the_course_text'] =$this->user_model->translate_('going_through_the_course_text');
		$this->tr_common['tr_i_liked_that_every_time_text'] =$this->user_model->translate_('i_liked_that_every_time_text');
		$this->tr_common['tr_i_loved_the_course_and_i_feel_text'] =$this->user_model->translate_('i_loved_the_course_and_i_feel_text');
		$this->tr_common['tr_accredicted_courses_trusted_sites'] =$this->user_model->translate_('accredicted_courses_trusted_sites');
		$this->tr_common['tr_accredicted_courses_trusted_sites_description'] =$this->user_model->translate_('accredicted_courses_trusted_sites_description');
		$this->tr_common['tr_newsletter_signup'] =$this->user_model->translate_('newsletter_signup');
		$this->tr_common['tr_Email'] =$this->user_model->translate_('Email');
		$this->tr_common['tr_submit_new']   =$this->user_model->translate_('submit_new');
		$this->tr_common['tr_follow_us']   =$this->user_model->translate_('follow_us');
		$this->tr_common['tr_useful_links']   =$this->user_model->translate_('useful_links');
		$this->tr_common['tr_our_courses']   =$this->user_model->translate_('our_courses');
		$this->tr_common['tr_your_email']   =$this->user_model->translate_('your_email');
		$this->tr_common['tr_dont_panic_text']   =$this->user_model->translate_('dont_panic_text');
		$this->tr_common['tr_name_example_com']   =$this->user_model->translate_('name_example_com');
		$this->tr_common['tr_reasons_to_choose_handh'] =$this->user_model->translate_('reasons_to_choose_handh');
		$this->tr_common['tr_Get_under_pets_fur'] =$this->user_model->translate_('Get_under_pets_fur');
		$this->tr_common['tr_Get_under_pets_fur_description'] =$this->user_model->translate_('Get_under_pets_fur_description');
		$this->tr_common['tr_Lovingly_care_for_your_pet'] =$this->user_model->translate_('Lovingly_care_for_your_pet');
		$this->tr_common['tr_Lovingly_care_for_your_pet_description'] =$this->user_model->translate_('Lovingly_care_for_your_pet_description');
		$this->tr_common['tr_Treat_minor_complaints_yourself'] =$this->user_model->translate_('Treat_minor_complaints_yourself');
		$this->tr_common['tr_Treat_minor_complaints_yourself_description'] =$this->user_model->translate_('Treat_minor_complaints_yourself_description');
		$this->tr_common['tr_Train_your_pack'] =$this->user_model->translate_('Train_your_pack');
		$this->tr_common['tr_Train_your_pack_description'] =$this->user_model->translate_('Train_your_pack_description');
		$this->tr_common['tr_Travel'] =$this->user_model->translate_('Travel');
		$this->tr_common['tr_Travel_description'] =$this->user_model->translate_('Travel_description');
		$this->tr_common['tr_Start_a_business'] =$this->user_model->translate_('Start_a_business');
		$this->tr_common['tr_Start_a_business_description'] =$this->user_model->translate_('Start_a_business_description');
		
		
		
		$this->tr_common['tr_home']   =$this->user_model->translate_('home');
		$this->tr_common['tr_review']   =$this->user_model->translate_('review');
		$this->tr_common['tr_courses']   =$this->user_model->translate_('courses');
		$this->tr_common['tr_read_more']   = $this->user_model->translate_('read_more');
		
		$this->tr_common['tr_joanne_coulter_comment'] =$this->user_model->translate_('joanne_coulter_comment');
		$this->tr_common['tr_joanne_coulter_place'] =$this->user_model->translate_('joanne_coulter_place');


//		$top_menu_base_courses = $this->user_model->get_courses($this->language);
		$this->language = $this->session->userdata('language');
		$this->student_status = $this->session->userdata('student_logged_in');
		$this->course=$this->user_model->get_courses($this->language);
		if(empty($this->course))
		{
			$this->course=$this->user_model->get_courses(4); // get english courses
		}
		
		$this->menu['top_menu']=$this->user_model->get_top_menu($this->language);
		$this->drop_down_base_course=$this->user_model->get_courses_by_order($this->language);
  	
  	}
  
  
  

  	function security_code()
  	{
  		if(!isset($this->session->userdata['gift_voucher_applied_details']))
		{
			redirect('voucher_code');
		}

		if(isset($this->session->userdata['gift_voucher_security_details']))
		{
			redirect('gift_voucher');
		}

		$gift_code = $this->session->userdata['gift_voucher_applied_details']['vouchercode'];

		$gift_code_details = $this->gift_voucher_model->getDetails_of_vcode($gift_code);
	  	
	  	$validation_groupon_site_ids = array('140','143','168','175','198','199','200');

	  	if(in_array($gift_code_details[0]->website, $validation_groupon_site_ids))
	  	{
			$validation_groupon_sites = true;	  		
	  	}
	  	else
	  	{
			$validation_groupon_sites = false;
	  	}

		if($_POST)
	  	{			
		    

			$content['secure'] 	   = $this->input->post('secure');
		    $content['secure_pdf'] = $this->input->post('secure_pdf');
		    		        
			//$this->form_validation->set_rules('secure_pdf', 'Security file', 'trim|required');	
			$this->form_validation->set_rules('secure', 'Security password', 'trim|required');	

			if($this->form_validation->run())
			{				
				$config['upload_path'] = 'public/uploads/deals/pdf/';
				$config['allowed_types'] = 'gif|jpg|png|pdf';
				$config['max_size']	= '10000';
				$config['encrypt_name'] = TRUE;			
				
				$this->load->library('upload', $config);
				$this->upload->initialize($config);		
				
				if($this->upload->do_upload('secure_pdf'))
				{
					$security_uploaded_data = $this->upload->data();						
				}
				else
				{					
					$error['upResult'] = array('error' => $this->upload->display_errors());
					$this->session->set_flashdata('email_msg', $error['upResult']['error']);
					redirect('gift_voucher/security_code');					
				}	
				$security_uploaded_data['secure'] = $this->input->post('secure');				
				
				$this->session->set_userdata('gift_voucher_security_details', $security_uploaded_data);		
       		
				redirect('gift_voucher');
              
			}
			
		}
		
		$content['voucher_applied'] = $this->session->userdata['gift_voucher_applied_details']['vouchercode']; 
		$content['validation_groupon_sites'] 	= $validation_groupon_sites;
		$content['currency_id'] 	= $this->currId;
	  	$content['view']  	  		= "gift_voucher_security_code";
		$content['lang_id'] 		= $this->language;
		$content['translate'] 		= $this->tr_common;
		$title['pageTitle'] 		= 'Login';
		$content['content'] 		= $title;
		$this->load->view('user/template_new',$content); 

  	}



	function index()
	{
	
		if(!isset($this->session->userdata['gift_voucher_applied_details']))
		{
			redirect('voucher_code');
		}	
		
		$gift_code = $this->session->userdata['gift_voucher_applied_details']['vouchercode'];

		$gift_code_details = $this->gift_voucher_model->getDetails_of_vcode($gift_code);

		$added_products_array = array();
		$enrolled_course_ids = array();
	  	$course_array = array();

	  	if(!isset($this->session->userdata['student_logged_in']['id']))
		{
			$user_logged_in = false;
		}
		else
		{
			$user_logged_in = true;
			$user_id = $this->session->userdata['student_logged_in']['id'];	
			$enrolled_course_array = $this->user_model->get_enrolled_courses($user_id);
		}


		if($gift_code_details[0]->courses_idcourses==""||$gift_code_details[0]->courses_idcourses==0)
		{
			$content['course_set']	 = $this->gift_voucher_model->get_course_by_language($this->session->userdata['language'],$enrolled_course_ids,'all');
			$content['course_count'] = 0;
		}
		else
		{
			$course_ids = explode(",",$gift_code_details[0]->courses_idcourses);
			$content['course_count'] = count($course_ids);
			$course_ids 			 = array_diff($course_ids,$enrolled_course_ids);			
			if(!empty($course_ids))		
			{
				//$content['course_set']=$this->gift_voucher_model->get_these_courses_new($course_ids);	
				$content['course_set']=$this->gift_voucher_model->get_these_courses($course_ids);			

			}
			else
			$content['course_set'] = array();
		}

		//$content['course_categories'] = $this->gift_voucher_model->get_course_categories();

		$content['voucher_type'] = $gift_code_details[0]->voucher_type;

		if($content['voucher_type']=='one_course_from_list')
		{
			$content['voucher_type_course_count'] = 1;
		}
		elseif($content['voucher_type']=='two_course_from_list')
		{
			$content['voucher_type_course_count'] = 2;
			if(!isset($this->session->userdata['gift_voucher_course_added']) && $user_logged_in)
			{
				$selected_values = str_replace(",","+",$gift_code_details[0]->courses_idcourses);

				$product_id = $this->common_model->getProdectId('course','',count(explode('+',$selected_values)));		
				
				$this->add_gift_voucher_course($selected_values,$product_id,$this->currId,'gift_voucher_course',$ajax=0);		

				$sess_array = array('gift_voucher_course_added' => 1);			
				$this->session->set_userdata($sess_array);
			}
		}
		elseif($content['voucher_type']=='four_course_from_list')
		{
			$content['voucher_type_course_count'] = 4;
		}
		elseif($content['voucher_type']=='user_select_any_one_course')
		{
			$content['voucher_type_course_count'] = 1;
		}
		elseif($content['voucher_type']=='course_bundle')
		{
			$content['voucher_type_course_count'] = count($course_ids);
		}
		elseif($content['voucher_type']=='one_or_more_predefined')
		{
			
			$content['voucher_type_course_count'] = count($course_ids);

			if(!isset($this->session->userdata['gift_voucher_course_added']) && $user_logged_in)
			{
				$selected_values = str_replace(",","+",$gift_code_details[0]->courses_idcourses);

				$product_id = $this->common_model->getProdectId('course','',count(explode('+',$selected_values)));		
				
				$this->add_gift_voucher_course($selected_values,$product_id,$this->currId,'gift_voucher_course',$ajax=0);		

				$sess_array = array('gift_voucher_course_added' => 1);			
				$this->session->set_userdata($sess_array);
			}

		}
		else
		{
			$content['voucher_type_course_count'] = 1;
		}

		$content['course_product_id'] = $this->common_model->getProdectId('course',$item_id='',$content['voucher_type_course_count']);

		if(isset($_POST['email_sub']))
		{
			
			$email = $this->input->post('deals_email');
			$user_details = $this->user_model->getUserByEmail($email);
			$reddemed_coupon_details = $this->gift_voucher_model->get_redeemedCoupon($this->input->post('vCode'));
							
			if(empty($user_details))
			{
				
				$this->session->set_flashdata('email_msg',"Unfortunately we do not have account associated with this email address. Please email us your full name and PDF copy of your voucher to info@hollyandhugo.com");
				
					if($langId==4)
					redirect('deals');
					if($langId==6)
					redirect('deals_fr');
					if($langId==3)
					redirect('deals_es');
				
			}
			else
			{
				
				if($user_details[0]->user_id==$reddemed_coupon_details->user_id)
				{
					$user_course_arr = $this->user_model->check_user_registered($user_details[0]->user_id,$reddemed_coupon_details->course_id);


					if(empty($user_course_arr))
					{
						$this->session->set_flashdata('email_msg',"Unfortunately this voucher code doesn't matches with your account. Please email us your full name and PDF copy of your voucher to info@hollyandhugo.com");

						if($langId==4)
							redirect('deals');
						if($langId==6)
							redirect('deals_fr');
						if($langId==3)
							redirect('deals_es');
					}
					else
					{
						if($user_details[0]->status==0)
						{		
							$this->load->library('email');
							$this->load->model('email_model');

							$row_new = $this->email_model->getTemplateById('new_registration',$langId);

							$us_password = $this->encrypt->decode($user_details[0]->password);
							$str_len = strlen($us_password);    	
					    		$char = str_repeat("*", ($str_len-3));
					    		$us_password = substr_replace($us_password,$char,3,($str_len-3));
					
							foreach($row_new as $row1)
							{

								$emailSubject = $row1->mail_subject;
								$mailContent = $row1->mail_content;
								$mailing_template_id=$row1->id;
							}

							$mailContent = str_replace ( "#firstname#",$user_details[0]->first_name, $mailContent );
							$mailContent = str_replace ( "#click here#","<a href='https://hollyandhugo.com/home/studentActivation/".$user_details[0]->user_id."'>click here</a>", $mailContent );
							
							$mailContent = str_replace ( "#actlink#","https://hollyandhugo.com/home/studentActivation/".$user_details[0]->user_id."", $mailContent  );
							
							$mailContent = str_replace ( "#url#", "<a href='".base_url()."'>Team Holly and Hugo</a>", $mailContent );
							$mailContent = str_replace ( "#username#", $user_details[0]->username, $mailContent );
							$mailContent = str_replace ( "#password#", $us_password, $mailContent );
							$tomail = $user_details[0]->email;							

							$this->email->from('info@hollyandhugo.com', 'Team Holly and Hugo');
							$this->email->to($tomail); 
							$this->email->cc(''); 
							$this->email->bcc(''); 

							$this->email->subject($emailSubject);
							$this->email->message($mailContent);	

							$sent=$this->email->send();
							if($sent==TRUE)
							{
								$mailing_histrory=array();
								$mailing_histrory['user_id']= $user_details[0]->user_id;
								$mailing_histrory['email_id']=$tomail;
								$mailing_histrory['template_id']=$mailing_template_id;
								$mailing_histrory['mailing_date']=date("Y-m-d");
								$this->common_model->add_email_history($mailing_histrory);
							}
						}
						else
						{
							$dateNow = date('Y-m-d');			
							if($user_course_arr[0]->date_expiry > $dateNow)
							{	  
								$this->load->library('email');
								$this->load->model('email_model');

								$row_new = $this->email_model->getTemplateById('remember_login',$langId);
								foreach($row_new as $row1)
								{

									$emailSubject = $row1->mail_subject;
									$mailContent = $row1->mail_content;
									$mailing_template_id=$row1->id;
								}

								$mailContent = str_replace ( "#firstname#",$user_details[0]->first_name, $mailContent );
								$mailContent = str_replace ( "#username#", $user_details[0]->username, $mailContent );
								$mailContent = str_replace ( "#password#", $this->encrypt->decode($user_details[0]->password), $mailContent );

								$tomail = $user_details[0]->email;

								$this->email->from('info@hollyandhugo.com', 'Team Holly and Hugo');
								$this->email->to($tomail); 
								$this->email->cc(''); 
								$this->email->bcc(''); 

								$this->email->subject($emailSubject);
								$this->email->message($mailContent);	

								$sent=$this->email->send();
								if($sent==TRUE){

									$mailing_histrory=array();
									$mailing_histrory['user_id']= $user_details[0]->user_id;
									$mailing_histrory['email_id']=$tomail;
									$mailing_histrory['template_id']=$mailing_template_id;
									$mailing_histrory['mailing_date']=date("Y-m-d");
									$this->common_model->add_email_history($mailing_histrory);

								}					  


								$day_remain = $this->count_days(strtotime($dateNow),strtotime($user_course_arr[0]->date_expiry));

								$this->session->set_flashdata('email_msg',"We have just sent you an email with your account details. Please note you have activated your course on ".$user_course_arr[0]->date_enrolled." and therefore your remaining study time is ".$day_remain." days. 'If you need more time to complete your course don't forget you can extend your access at any time by going to the Extend Course link in your Virtual Campus");
								redirect('coupon');

							}
							else
							{
								$this->load->library('email');
								$this->load->model('email_model');

								$row_new = $this->email_model->getTemplateById('remember_login',$langId);
								foreach($row_new as $row1)
								{
									$emailSubject = $row1->mail_subject;
									$mailContent = $row1->mail_content;
									$mailing_template_id=$row1->id;
								}

								$mailContent = str_replace ( "#firstname#",$user_details[0]->first_name, $mailContent );
								$mailContent = str_replace ( "#username#", $user_details[0]->username, $mailContent );
								$mailContent = str_replace ( "#password#", $this->encrypt->decode($user_details[0]->password), $mailContent );

								$tomail = $user_details[0]->email;;

								$this->email->from('info@hollyandhugo.com', 'Team Holly and Hugo');
								$this->email->to($tomail); 
								$this->email->cc(''); 
								$this->email->bcc(''); 
								$this->email->subject($emailSubject);
								$this->email->message($mailContent);	
								$sent=$this->email->send();
								if($sent==TRUE)
								{
									$mailing_histrory=array();
									$mailing_histrory['user_id']= $user_details[0]->user_id;
									$mailing_histrory['email_id']=$tomail;
									$mailing_histrory['template_id']=$mailing_template_id;
									$mailing_histrory['mailing_date']=date("Y-m-d");
									$this->common_model->add_email_history($mailing_histrory);
								}

								$this->session->set_flashdata('email_msg',"We have just sent you an email with your account details. Please note you have activated your course on ".$user_course_arr[0]->date_enrolled." and therefore your course has now expired. If you would like to extend your course please go to Extend Course in your Virtual Campus. 
									");
								if($langId==4)
									redirect('deals');
								if($langId==6)
									redirect('deals_fr');
								if($langId==3)
									redirect('deals_es');

							}

						}
					}

				}
				else
				{
					$this->session->set_flashdata('email_msg',"Unfortunately this voucher code doesn't matches with your account. Please email us your full name and PDF copy of your voucher to info@hollyandhugo.com");
					if($langId==4)
						redirect('coupon');
					if($langId==6)
						redirect('deals_fr');
					if($langId==3)
						redirect('deals_es');
				}
			}
			
			
		}

		if($this->session->userdata('gift_voucher_cart_session_id'))
		{			
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('gift_voucher_cart_session_id'));			
			if(!empty($cart_main_details))
			{
				foreach($cart_main_details as $cart_main)
				{
					$content['cart_count'] = $cart_main->item_count;
					$content['cart_amount'] = $cart_main->total_cart_amount;
					$content['currency_symbol'] = $this->common_model->get_currency_symbol_from_id($cart_main->currency_id);
				}
				
				foreach($cart_main_details as $cart_main)
				{		
					$cart_main_id = $cart_main->id;			
					$products_in_cart = $this->sales_model->get_cart_items($cart_main->id);	
					$q=0;
					if(!empty($products_in_cart))
					{
						foreach($products_in_cart as $prod)
						{
							$cart_item_details = $this->sales_model->get_cart_items_details($cart_main_id,$prod->id);								
							foreach($cart_item_details as $item_det)
							{							
								$added_products_array[$item_det->product_type][] =  $item_det->selected_item_ids;												
							}	
						}
					}
							
				}
			}
			else
			{
				$content['cart_count'] = 0;
				$content['cart_amount'] = 0;
				$content['currency_symbol'] = $this->common_model->get_currency_symbol_from_id($this->currId);
			}
		}		
		else
		{
			$content['cart_count'] = 0;
			$content['cart_amount'] = 0;
			$content['currency_symbol'] = $this->common_model->get_currency_symbol_from_id($this->currId);
			
		}

		$this->tr_common['tr_total_price'] = $this->user_model->translate_('total_price');
		$this->tr_common['tr_check_out'] = $this->user_model->translate_('check_out');
		$content['currency_id'] = $this->currId;
		$content['added_products_array'] = $added_products_array;	
	  	$content['view']  	  		= "gift_voucher_index";
		$content['lang_id'] 		= $this->language;
		$content['user_logged_in'] 	= $user_logged_in;		
		$content['translate'] 		= $this->tr_common;
		$title['pageTitle'] 		= 'Login';
		$content['content'] 		= $title;
		$this->load->view('user/template_new',$content); 

	}



	  
	function login_form(){
		 
		  
		if($_POST)
		{ 			
			$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
			$this->form_validation->set_rules('password_voucher', 'Password', 'trim|required|xss_clean|callback_check_database');
							
			if($this->form_validation->run() == TRUE)
			{				
				redirect('gift_voucher');
			}	
			else
			{				
				$this->session->set_flashdata('popup_message','Invalid Login.');				
				redirect('voucher_code/'.$this->session->userdata['gift_voucher_applied_details']['product_type']);
			}			
		}
		
		
	}
	  
	  
	function check_database($password)
 	{   	
		$username = $this->input->post('username');		
		$result = $this->user_model->login($username, $password);
		if($result)
		{
			$sess_array = array();
			foreach($result as $row)
			{ 
               if ($row->status!=1) 
			   {
                
                  $this->form_validation->set_message('popup_message','student is not active');
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
							
                    return TRUE;
                }
     		}
     					
   		}
		else
   		{						
			$langid=$this->session->userdata('language');
			if(isset($langid)&&($langid==6))
			{
			$this->form_validation->set_message('popup_message',"ID de styliste ou code invalide");	
			}elseif(isset($langid)&&($langid==3)){
			$this->form_validation->set_message('popup_message','ID estilista no válida o código');	
			}else{
			$this->form_validation->set_message('popup_message','Invalid stylist ID or code'); }
			
			
			return false;
		}
				
				
	}
	


	function remove_voucher_code()
	{

	  	if($this->session->userdata('gift_voucher_applied')==1){
	  	
	  		$this->session->unset_userdata('gift_voucher_cart_session_id');
			$this->session->unset_userdata('gift_voucher_applied');
			$this->session->unset_userdata('gift_voucher_applied_details');
			$this->session->unset_userdata('cart_source'); 
			$this->session->unset_userdata('added_user_id');
			$this->session->unset_userdata('gift_voucher_pre_user_id');				 

			$data['err_msg']		 = 0;
			echo json_encode($data); 
			exit;	
	  	}
	  	else
	  	{
	  		$data['err_msg']		 = 1;
			echo json_encode($data); 
			exit;		
	  	}

	}

	
	function enrol_course()
	{

  		if(!isset($this->session->userdata['gift_voucher_applied_details']))
		{
			redirect('voucher_code');
		}			
		
		$content = array();
		$added_products_array = array();
		$enrolled_course_array = array();


		$country_isd_codes = $this->gift_voucher_model->get_country_isd_codes();


		$this->tr_common['tr_first_name']   		= $this->user_model->translate_('First_Name'); 
		$this->tr_common['tr_last_name']   			= $this->user_model->translate_('last_Name'); 		
		$this->tr_common['tr_email']   				= $this->user_model->translate_('email'); 		
		$this->tr_common['tr_contact_num']   		= $this->user_model->translate_('contact_num'); 	
		$this->tr_common['tr_create_secret_code']   = $this->user_model->translate_('create_secret_code'); 						 	
		$this->tr_common['tr_first_name_required']  = $this->user_model->translate_('first_name_required'); 		
		$this->tr_common['tr_last_name_required']   = $this->user_model->translate_('last_name_required');		
		$this->tr_common['tr_user_name_required']   = $this->user_model->translate_('user_name_required'); 		
		$this->tr_common['tr_email_required']   	= $this->user_model->translate_('email_required'); 
		$this->tr_common['tr_email_exists']   	    = $this->user_model->translate_('email_exists'); 
		$this->tr_common['tr_valid_email_required'] = $this->user_model->translate_('valid_email_required'); 
		$this->tr_common['tr_required']   	   		= $this->user_model->translate_('required'); 
		$this->tr_common['tr_confirm_email']   		= $this->user_model->translate_('confirm_email'); 
		$this->tr_common['tr_registration']     	= $this->user_model->translate_('registration');			
		$this->tr_common['tr_next']   				= $this->user_model->translate_('_next'); 
		$this->tr_common['tr_step_3_your_details']  = $this->user_model->translate_('step_3_your_details');  
		$this->tr_common['tr_4_confirmation'] 		= $this->user_model->translate_('4_confirmation');		
		$this->tr_common['tr_Email_ucfirst'] 		= $this->user_model->translate_('Email_ucfirst');


		if(isset($_POST['fname']))
		{
			
		  $studentdata  			  = array();
		  $studentdata['first_name']  = $content['fname'] = ucfirst(strtolower($this->input->post('fname')));
		  $studentdata['last_name']   = $content['lname'] =ucfirst(strtolower($this->input->post('lname')));
		  $studentdata['email'] 	  = $content['email'] = $this->input->post('email');	
		  $studentdata['country_id']  = $content['country_set'] = $this->input->post('country_id');		
		  $studentdata['username']    = $content['user_name'] = $this->input->post('email');
		  $studentdata['contact_number'] = $content['contact_no'] = $this->input->post('contact_no');
		  $content['pword'] 		  = $this->input->post('pword');
		  $studentdata['password']	  = $this->encrypt->encode($this->input->post('pword'));		 		 
          $studentdata['with_coupon'] ='yes';
		  $studentdata['coupon_code'] = $this->session->userdata['gift_voucher_applied_details']['vouchercode'];			 
		  $studentdata['reg_date']	  = date("Y-m-d");
		 // $this->check_public_user_used_this_voucher_code($studentdata['email']);
		  
		  $this->form_validation->set_rules('fname', 'First name', 'trim|required');
		  $this->form_validation->set_rules('lname', 'Last Name', 'required');	
		  $this->form_validation->set_rules('contact_no', 'Phone number', 'required');  		 
		  $this->form_validation->set_rules('pword', 'Password', 'required|min_length[6]|callback_chkpword[Password]');
		  $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email|callback_chkemail[E-mail]');
		 
		  if($this->form_validation->run())
		  { 	
			
				$this->user_model->add_student_temp($studentdata);
				$pre_user_id = $this->db->insert_id();
				$pre_session_array['pre_user_id'] = $pre_user_id;								
				$sess_array  = array('gift_voucher_pre_user_id' => $pre_session_array);
			    $this->session->set_userdata($sess_array);
				redirect('gift_voucher/select_course');					
		  }
		

		}
					
		$this->tr_common['tr_trndimi_ebooks'] = $this->user_model->translate_('trendimi_ebooks');
		$this->tr_common['tr_check_out'] = $this->user_model->translate_('check_out');
	    $this->tr_common['tr_total_price'] = $this->user_model->translate_('total_price');
		 
		if(isset($this->session->userdata['student_logged_in']['id']))
		{
			redirect('gift_voucher');
		}
		
		
		$content['country_isd_codes'] 	 = $country_isd_codes;	
		$content['lang_id'] 			 = $this->language;
	//	$content['user_logged_in'] 	     = $user_logged_in;
	//	$content['voucher_course_array'] = $voucher_course_array;	
	//	$content['course_array'] 		 = $course_array;	
		$content['translate'] 		     = $this->tr_common;
		$content['view'] 				 = 'gift_voucher_enrol_course';
		$title['pageTitle'] 			 = 'Login';
		$content['content'] 			 = $title;
		$this->load->view('user/template_new',$content); 

	}



	function select_course()
	{
	
		if(!isset($this->session->userdata['gift_voucher_applied_details']))
		{
			redirect('voucher_code');
		}	
		
		$gift_code = $this->session->userdata['gift_voucher_applied_details']['vouchercode'];

		$gift_code_details = $this->gift_voucher_model->getDetails_of_vcode($gift_code);

		$added_products_array = array();
		$enrolled_course_ids = array();
	  	$course_array = array();

	  	if(!isset($this->session->userdata['student_logged_in']['id']))
		{
			$user_logged_in = false;
		}
		else
		{
			$user_logged_in = true;
			$user_id = $this->session->userdata['student_logged_in']['id'];	
			$enrolled_course_array = $this->user_model->get_enrolled_courses($user_id);
		}


		if($gift_code_details[0]->courses_idcourses==""||$gift_code_details[0]->courses_idcourses==0)
		{
			$content['course_set']	 = $this->gift_voucher_model->get_course_by_language($this->session->userdata['language'],$enrolled_course_ids,'all');
			$content['course_count'] = 0;
		}
		else
		{
			$course_ids = explode(",",$gift_code_details[0]->courses_idcourses);
			$content['course_count'] = count($course_ids);
			$course_ids 			 = array_diff($course_ids,$enrolled_course_ids);			
			if(!empty($course_ids))		
			{
				//$content['course_set']=$this->gift_voucher_model->get_these_courses_new($course_ids);			
				$content['course_set']=$this->gift_voucher_model->get_these_courses($course_ids);		
			}
			else
			$content['course_set'] = array();
		}

		//$content['course_categories'] = $this->gift_voucher_model->get_course_categories();

		$content['voucher_type'] = $gift_code_details[0]->voucher_type;

		if($content['voucher_type']=='one_course_from_list')
		{
			$content['voucher_type_course_count'] = 1;
		}
		elseif($content['voucher_type']=='two_course_from_list')
		{
			$content['voucher_type_course_count'] = 2;
			if(!isset($this->session->userdata['gift_voucher_course_added']) && $user_logged_in)
			{
				$selected_values = str_replace(",","+",$gift_code_details[0]->courses_idcourses);

				$product_id = $this->common_model->getProdectId('course','',count(explode('+',$selected_values)));		
				
				$this->add_gift_voucher_course($selected_values,$product_id,$this->currId,'gift_voucher_course',$ajax=0);		

				$sess_array = array('gift_voucher_course_added' => 1);			
				$this->session->set_userdata($sess_array);
			}
		}
		elseif($content['voucher_type']=='four_course_from_list')
		{
			$content['voucher_type_course_count'] = 4;
		}
		elseif($content['voucher_type']=='user_select_any_one_course')
		{
			$content['voucher_type_course_count'] = 1;
		}
		elseif($content['voucher_type']=='course_bundle')
		{
			$content['voucher_type_course_count'] = count($course_ids);
		}
		elseif($content['voucher_type']=='one_or_more_predefined')
		{
			
			$content['voucher_type_course_count'] = count($course_ids);

			if(!isset($this->session->userdata['gift_voucher_course_added']) && $user_logged_in)
			{
				$selected_values = str_replace(",","+",$gift_code_details[0]->courses_idcourses);

				$product_id = $this->common_model->getProdectId('course','',count(explode('+',$selected_values)));		
				
				$this->add_gift_voucher_course($selected_values,$product_id,$this->currId,'gift_voucher_course',$ajax=0);		

				$sess_array = array('gift_voucher_course_added' => 1);			
				$this->session->set_userdata($sess_array);
			}

		}
		else
		{
			$content['voucher_type_course_count'] = 1;
		}

		$content['course_product_id'] = $this->common_model->getProdectId('course',$item_id='',$content['voucher_type_course_count']);

		if(isset($_POST['email_sub']))
		{
			
			$email = $this->input->post('deals_email');
			$user_details = $this->user_model->getUserByEmail($email);
			$reddemed_coupon_details = $this->gift_voucher_model->get_redeemedCoupon($this->input->post('vCode'));
							
			if(empty($user_details))
			{
				
				$this->session->set_flashdata('email_msg',"Unfortunately we do not have account associated with this email address. Please email us your full name and PDF copy of your voucher to info@hollyandhugo.com");
				
					if($langId==4)
					redirect('deals');
					if($langId==6)
					redirect('deals_fr');
					if($langId==3)
					redirect('deals_es');
				
			}
			else
			{
				
				if($user_details[0]->user_id==$reddemed_coupon_details->user_id)
				{
					$user_course_arr = $this->user_model->check_user_registered($user_details[0]->user_id,$reddemed_coupon_details->course_id);


					if(empty($user_course_arr))
					{
						$this->session->set_flashdata('email_msg',"Unfortunately this voucher code doesn't matches with your account. Please email us your full name and PDF copy of your voucher to info@hollyandhugo.com");

						if($langId==4)
							redirect('deals');
						if($langId==6)
							redirect('deals_fr');
						if($langId==3)
							redirect('deals_es');
					}
					else
					{
						if($user_details[0]->status==0)
						{		
							$this->load->library('email');
							$this->load->model('email_model');

							$row_new = $this->email_model->getTemplateById('new_registration',$langId);

							$us_password = $this->encrypt->decode($user_details[0]->password);
							$str_len = strlen($us_password);    	
					    		$char = str_repeat("*", ($str_len-3));
					    		$us_password = substr_replace($us_password,$char,3,($str_len-3));
							
							foreach($row_new as $row1)
							{

								$emailSubject = $row1->mail_subject;
								$mailContent = $row1->mail_content;
								$mailing_template_id=$row1->id;
							}

							$mailContent = str_replace ( "#firstname#",$user_details[0]->first_name, $mailContent );
							$mailContent = str_replace ( "#click here#","<a href='https://hollyandhugo.com/home/studentActivation/".$user_details[0]->user_id."'>click here</a>", $mailContent );
							
							$mailContent = str_replace ( "#actlink#","https://hollyandhugo.com/home/studentActivation/".$user_details[0]->user_id."", $mailContent  );
							
							$mailContent = str_replace ( "#url#", "<a href='".base_url()."'>Holly and Hugo</a>", $mailContent );
							$mailContent = str_replace ( "#username#", $user_details[0]->username, $mailContent );
							$mailContent = str_replace ( "#password#", $us_password, $mailContent );
							$tomail = $user_details[0]->email;							

							$this->email->from('info@hollyandhugo.com', 'Team Holly and Hugo');
							$this->email->to($tomail); 
							$this->email->cc(''); 
							$this->email->bcc(''); 

							$this->email->subject($emailSubject);
							$this->email->message($mailContent);	

							$sent=$this->email->send();
							if($sent==TRUE)
							{
								$mailing_histrory=array();
								$mailing_histrory['user_id']= $user_details[0]->user_id;
								$mailing_histrory['email_id']=$tomail;
								$mailing_histrory['template_id']=$mailing_template_id;
								$mailing_histrory['mailing_date']=date("Y-m-d");
								$this->common_model->add_email_history($mailing_histrory);
							}
						}
						else
						{
							$dateNow = date('Y-m-d');			
							if($user_course_arr[0]->date_expiry > $dateNow)
							{	  
								$this->load->library('email');
								$this->load->model('email_model');

								$row_new = $this->email_model->getTemplateById('remember_login',$langId);
								foreach($row_new as $row1)
								{

									$emailSubject = $row1->mail_subject;
									$mailContent = $row1->mail_content;
									$mailing_template_id=$row1->id;
								}

								$mailContent = str_replace ( "#firstname#",$user_details[0]->first_name, $mailContent );
								$mailContent = str_replace ( "#username#", $user_details[0]->username, $mailContent );
								$mailContent = str_replace ( "#password#", $this->encrypt->decode($user_details[0]->password), $mailContent );

								$tomail = $user_details[0]->email;

								$this->email->from('info@hollyandhugo.com', 'Team Holly and Hugo');
								$this->email->to($tomail); 
								$this->email->cc(''); 
								$this->email->bcc(''); 

								$this->email->subject($emailSubject);
								$this->email->message($mailContent);	

								$sent=$this->email->send();
								if($sent==TRUE){

									$mailing_histrory=array();
									$mailing_histrory['user_id']= $user_details[0]->user_id;
									$mailing_histrory['email_id']=$tomail;
									$mailing_histrory['template_id']=$mailing_template_id;
									$mailing_histrory['mailing_date']=date("Y-m-d");
									$this->common_model->add_email_history($mailing_histrory);

								}					  


								$day_remain = $this->count_days(strtotime($dateNow),strtotime($user_course_arr[0]->date_expiry));

								$this->session->set_flashdata('email_msg',"We have just sent you an email with your account details. Please note you have activated your course on ".$user_course_arr[0]->date_enrolled." and therefore your remaining study time is ".$day_remain." days. 'If you need more time to complete your course don't forget you can extend your access at any time by going to the Extend Course link in your Virtual Campus");
								redirect('coupon');

							}
							else
							{
								$this->load->library('email');
								$this->load->model('email_model');

								$row_new = $this->email_model->getTemplateById('remember_login',$langId);
								foreach($row_new as $row1)
								{
									$emailSubject = $row1->mail_subject;
									$mailContent = $row1->mail_content;
									$mailing_template_id=$row1->id;
								}

								$mailContent = str_replace ( "#firstname#",$user_details[0]->first_name, $mailContent );
								$mailContent = str_replace ( "#username#", $user_details[0]->username, $mailContent );
								$mailContent = str_replace ( "#password#", $this->encrypt->decode($user_details[0]->password), $mailContent );

								$tomail = $user_details[0]->email;;

								$this->email->from('info@hollyandhugo.com', 'Team Holly and Hugo');
								$this->email->to($tomail); 
								$this->email->cc(''); 
								$this->email->bcc(''); 
								$this->email->subject($emailSubject);
								$this->email->message($mailContent);	
								$sent=$this->email->send();
								if($sent==TRUE)
								{
									$mailing_histrory=array();
									$mailing_histrory['user_id']= $user_details[0]->user_id;
									$mailing_histrory['email_id']=$tomail;
									$mailing_histrory['template_id']=$mailing_template_id;
									$mailing_histrory['mailing_date']=date("Y-m-d");
									$this->common_model->add_email_history($mailing_histrory);
								}

								$this->session->set_flashdata('email_msg',"We have just sent you an email with your account details. Please note you have activated your course on ".$user_course_arr[0]->date_enrolled." and therefore your course has now expired. If you would like to extend your course please go to Extend Course in your Virtual Campus. 
									");
								if($langId==4)
									redirect('deals');
								if($langId==6)
									redirect('deals_fr');
								if($langId==3)
									redirect('deals_es');

							}

						}
					}

				}
				else
				{
					$this->session->set_flashdata('email_msg',"Unfortunately this voucher code doesn't matches with your account. Please email us your full name and PDF copy of your voucher to info@hollyandhugo.com");
					if($langId==4)
						redirect('coupon');
					if($langId==6)
						redirect('deals_fr');
					if($langId==3)
						redirect('deals_es');
				}
			}
			
			
		}

		if($this->session->userdata('gift_voucher_cart_session_id'))
		{			
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('gift_voucher_cart_session_id'));			
			if(!empty($cart_main_details))
			{
				foreach($cart_main_details as $cart_main)
				{
					$content['cart_count'] = $cart_main->item_count;
					$content['cart_amount'] = $cart_main->total_cart_amount;
					$content['currency_symbol'] = $this->common_model->get_currency_symbol_from_id($cart_main->currency_id);
				}
				
				foreach($cart_main_details as $cart_main)
				{		
					$cart_main_id = $cart_main->id;			
					$products_in_cart = $this->sales_model->get_cart_items($cart_main->id);	
					$q=0;
					if(!empty($products_in_cart))
					{
						foreach($products_in_cart as $prod)
						{
							$cart_item_details = $this->sales_model->get_cart_items_details($cart_main_id,$prod->id);								
							foreach($cart_item_details as $item_det)
							{							
								$added_products_array[$item_det->product_type][] =  $item_det->selected_item_ids;												
							}	
						}
					}
							
				}
			}
			else
			{
				$content['cart_count'] = 0;
				$content['cart_amount'] = 0;
				$content['currency_symbol'] = $this->common_model->get_currency_symbol_from_id($this->currId);
			}
		}		
		else
		{
			$content['cart_count'] = 0;
			$content['cart_amount'] = 0;
			$content['currency_symbol'] = $this->common_model->get_currency_symbol_from_id($this->currId);
			
		}

		$this->tr_common['tr_total_price'] = $this->user_model->translate_('total_price');
		$this->tr_common['tr_check_out'] = $this->user_model->translate_('check_out');
		$content['currency_id'] = $this->currId;
		$content['added_products_array'] = $added_products_array;	
	  	$content['view']  	  		= "gift_voucher_select_course";
		$content['lang_id'] 		= $this->language;
		$content['user_logged_in'] 	= $user_logged_in;		
		$content['translate'] 		= $this->tr_common;
		$title['pageTitle'] 		= 'Login';
		$content['content'] 		= $title;
		$this->load->view('user/template_new',$content); 

	}

	
	
	function cart()
	{	

		if(!isset($this->session->userdata['gift_voucher_applied_details']))
		{
			redirect('voucher_code');
		}

		$purchased_course_names = '';
		$purchased_ebook_names  = '';
		$purchased_item_names = array();
		$content = array();
		$product_name = array();
		$coupon_applied ='';
		$coupon_code_applied = '';
		$product_images = array();
		$discount_amount =0;
		$course_subscription_package_added = false;
		
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
		$this->tr_common['tr_apply_your_certificate'] = $this->user_model->translate_('apply_your_certificate');
		$this->tr_common['tr_apply'] = $this->user_model->translate_('apply');
		$this->tr_common['tr_certificate_applied'] = $this->user_model->translate_('certificate_applied');
		$this->tr_common['tr_sales_no_items_in_cart'] = $this->user_model->translate_('sales_no_items_in_cart');
		$this->tr_common['tr_enter_coupon_code'] = $this->user_model->translate_('enter_coupon_code');
		$this->tr_common['tr_discount_amount'] = $this->user_model->translate_('discount_amount');
		$this->tr_common['tr_apply'] = $this->user_model->translate_('apply');
		
		
		
		  
		if($this->session->userdata('gift_voucher_cart_session_id'))
		{
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('gift_voucher_cart_session_id'));
				
			$currency_id = $cart_main_details[0]->currency_id;
			$currency_symbol = $this->common_model->get_currency_symbol_from_id($cart_main_details[0]->currency_id);
			$bonus=array();		
			foreach($cart_main_details as $cart_main)
			{
						
				$cart_main_id = $cart_main->id;					
				$discount_amount = $cart_main->discount_amount;
				$products_in_cart = $this->voucher_code_model->get_cart_items($cart_main->id);
					
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
						$bonus_type[$q]=$product_type_image[$q]=$item_det->product_type;
						$selected_items = $item_det->selected_item_ids;
						if($item_det->product_type == 'ebooks' || $item_det->product_type == 'bonus_ebooks')
						{
							//**************** Anoop code start ***************************
						   if($item_det->product_type == 'bonus_ebooks'){
								//$bonus_course_product=$this->common_model->getProdectId('course',$item_det->selected_item_ids,1);
								$bonus_ebook_ids = explode(',',$selected_items);
								$bonus_ebook_product = $this->common_model->getProdectId('ebooks','',count($bonus_ebook_ids));
								
								//$data['bonus_item_original_price']=$this->common_model->getProductFee($bonus_course_product,$this->currId);
								$bounus_prod_price['bonus_ebooks'] = $this->common_model->getProductFee($bonus_ebook_product,$this->currId);
								$bonus[$q]="yes";
							}
							
							//**************** Anoop code end ***************************
							$ebook_ids = explode(',',$selected_items);
							
							if($item_det->product_type == 'ebooks')
							{
								$product_name[$q] = $this->user_model->translate_('sales_product_name_ebook');
							}
							else if($item_det->product_type == 'bonus_ebooks')
							{
								$product_name[$q] = 'Bonus Ebook';
							}
							
							
							for($qq1=0;$qq1<count($ebook_ids);$qq1++)
							{
								$ebook_details = $this->ebook_model->get_ebook_details($ebook_ids[$qq1]);	
													
								if($purchased_item_names[$q]=='')
								{
									$purchased_item_names[$q] = '<p>'.$ebook_details[0]->ebookName.'</p><br />';
									$product_images[$item_det->product_type][$q][$qq1] = 'public/user/cart/ebooks/'.$ebook_details[0]->image_name;	
								}
								else
								{
									$purchased_item_names[$q].='<p>'.$ebook_details[0]->ebookName.'</p><br />';
									$product_images[$item_det->product_type][$q][$qq1] = 'public/user/cart/ebooks/'.$ebook_details[0]->image_name;	
								}
							}
						}						
						else if($item_det->product_type == 'course' || $item_det->product_type == 'bonus_course')
						{
							//**************** Anoop code start ***************************
							if($item_det->product_type == 'bonus_course'){
								//$bonus_course_product=$this->common_model->getProdectId('course',$item_det->selected_item_ids,1);
								$bonus_course_ids = explode(',',$selected_items);
								//$bonus_course_product = $this->common_model->getProdectId('course','',count($bonus_course_ids));
								
								$selected_items_org_price = $this->voucher_code_model->get_selected_items_price_details('course' ,$bonus_course_ids,$currency_id);

								//$data['bonus_item_original_price']=$this->common_model->getProductFee($bonus_course_product,$this->currId);
								$bounus_prod_price['bonus_course']['amount'] = $selected_items_org_price;
								//$bounus_prod_price['bonus_course'] = $this->common_model->getProductFee($bonus_course_product,$this->currId);
								$bonus[$q]="yes";
							}
							
							//**************** Anoop code end ***************************
							$course_ids = explode(',',$selected_items);
							if($item_det->product_type == 'course')
							{
								
								$product_name[$q] = $this->user_model->translate_('sales_product_name_course');
							}
							else
							{
								$product_name[$q] = 'Bonus Course';
							}
							for($qq=0;$qq<count($course_ids);$qq++)
							{
								if($purchased_item_names[$q]=='')
								{
								  $purchased_item_names[$q] = '<p>'.$this->common_model->get_course_name($course_ids[$qq]).'</p>'; 
								}
								else
								{
								  $purchased_item_names[$q] .= '<p>'.$this->common_model->get_course_name($course_ids[$qq]).'</p>'; 
								}
								$image_name = $this->sales_model->get_course_image($course_ids[$qq]); 	
								$product_images[$item_det->product_type][$q][$qq] = 'public/user/css/template/img/'.$image_name;
							}
						
							
						}	
						else if($item_det->product_type == 'package')
						{
							//$package_id = $selected_items;
							$product_name[$q] = $this->user_model->translate_('package');
							$product_type[$q]= 'package';					
							$package_details = $this->package_model->fetch_package($selected_items);


							$products_in_package = explode(',',$package_details[0]->products);	
							$course_subscription = $this->common_model->get_product_by_type('course_subscription');	

							if(in_array($course_subscription[0]->id,$products_in_package))
							{								
								$course_subscription_package_added = true;	
								$purchased_item_names[$q] = 'All courses'; 						
							}
							else
							{
								$purchased_item_names[$q] = $package_details[0]->package_name;									
							}
							
							$product_images[$item_det->product_type][$q][0] = 'public/user/cart/packages/'.$package_details[0]->image_name;
						}

						else if($item_det->product_type == 'bundle')
						{
							//$package_id = $selected_items;
							$product_name[$q] = 'Course bundle';
							$product_type[$q]= 'Courses';					
							$bundle_details = $this->voucher_code_model->get_bundle_details($selected_items);
							$purchased_item_names[$q] = $bundle_details[0]->bundle_name;
							$product_images[$item_det->product_type][$q][0] = 'public/upload/bundle_images/'.$bundle_details[0]->image;
						}

						else if($this->session->userdata['gift_voucher_applied_details']['product_type'] == 'course_subscription')
						{							
							$product_name[$q] = 'All Courses';
							$product_type[$q]= 'Courses';												
							$purchased_item_names[$q] = 'Course subscription';
							$product_images[$product_type_image[$q]][$q][0] = 'public/user/outer/category_images/category-career-development.jpg';
						}

						else if($item_det->product_type == 'voucher_code')
						{
							
                             $product_type_image[$q]='voucher_code_'.$this->session->userdata['gift_voucher_applied_details']['product_type'];
							if($this->session->userdata['gift_voucher_applied_details']['product_type'] == 'course')
							{
								$course_ids = explode(',',$selected_items);
								$product_name[$q] = $this->user_model->translate_('sales_product_name_course');
								for($qq=0;$qq<count($course_ids);$qq++)
								{
									if($purchased_item_names[$q]=='')
									{
									  $purchased_item_names[$q] = '<p>'.$this->common_model->get_course_name($course_ids[$qq]).'</p>'; 
									}
									else
									{
									  $purchased_item_names[$q] .= '<p>'.$this->common_model->get_course_name($course_ids[$qq]).'</p>'; 
									}
									$image_name = $this->sales_model->get_course_image($course_ids[$qq]); 	
									$product_images[$product_type_image[$q]][$q][$qq] = 'public/user/outer/course_images/'.$image_name;
								}
							}
							else if($this->session->userdata['gift_voucher_applied_details']['product_type'] == 'course_bundle')
							{
								//$package_id = $selected_items;
								$product_name[$q] = 'Course bundle';
								$product_type[$q]= 'Courses';					
								$bundle_details = $this->voucher_code_model->get_bundle_details($selected_items);
								$purchased_item_names[$q] = $bundle_details[0]->bundle_name;
								$product_images[$product_type_image[$q]][$q][0] = 'public/user/outer/category_images/category_image_animal.jpg';
								
							}
							elseif($this->session->userdata['gift_voucher_applied_details']['product_type'] == 'ebooks')
							{
								$ebook_ids = explode(',',$selected_items);
								$product_name[$q] = "Ebooks";
								for($qq=0;$qq<count($ebook_ids);$qq++)
								{

									$ebook_details = $this->ebook_model->get_ebook_details($ebook_ids[$qq]);	
													
									if($purchased_item_names[$q]=='')
									{
										$purchased_item_names[$q] = '<p>'.$ebook_details[0]->ebookName.'</p>';
										$product_images[$product_type_image[$q]][$q][$qq]= 'public/user/cart/ebooks/'.$ebook_details[0]->image_name;	
									}
									else
									{
										$purchased_item_names[$q] .='<p>'.$ebook_details[0]->ebookName.'</p>';
									}




								}
							}
							elseif($this->session->userdata['gift_voucher_applied_details']['product_type'] == 'extension')
							{

								$voucher_id  	  = $this->session->userdata['gift_voucher_applied_details']['voucher_id'];

								$ext_product_id   = $this->voucher_code_model->get_other_products_product_id($voucher_id);

								$product_details  = $this->common_model->get_product_details($ext_product_id);	

								$product_name[$q] =  $this->user_model->translate_('sales_product_name_extension');


								$extension_details = $this->sales_model->get_extension_details_by_units($product_details[0]->item_id);	
								if($this->session->userdata['language']==4)
								{					
									$purchased_item_names[$q] = '<p>'.$extension_details[0]->extension_option.'</p>';
								}
								else if($this->session->userdata['language']==3)
								{
									$purchased_item_names[$q] = '<p>'.$extension_details[0]->extension_option_spanish.'</p>';								
								}
								elseif($this->session->userdata['language']==6)
								{
									$purchased_item_names[$q] = '<p>'.$extension_details[0]->extension_option_french.'</p>';								
								}
								$image_name = $this->sales_model->get_course_image($selected_items); 
								
								$product_images[$product_type_image[$q]][$q][0] = 'public/user/outer/course_images/'.$image_name;
							}
							elseif($this->session->userdata['gift_voucher_applied_details']['product_type'] == 'letters')
							{

								$voucher_id  	  = $this->session->userdata['gift_voucher_applied_details']['voucher_id'];

								$letter_product_id   = $this->voucher_code_model->get_other_products_product_id($voucher_id);
								$product_details  = $this->common_model->get_product_details($letter_product_id);	

								if($product_details[0]->type == 'poe_soft')
								{
									$product_name[$q] =  $this->user_model->translate_('sales_product_name_poe');						
								}
								/*else 
								{
									$product_name[$q] =  'Bonus Proof Of Study';
									$bonus_poe_product=$this->common_model->getProdectId('bonus_extension','',1);
									$bounus_prod_price['bonus_course'] =$this->common_model->getProductFee($bonus_poe_product,$this->currId);				
								}*/
								$purchased_item_names[$q] = '<p>'.$this->user_model->translate_('sales_product_soft_copy').'</p>';
								$product_images[$product_type_image[$q]][$q][0] = 'public/user/campus/images/proof_enrolement_image.jpg';


							}	

						}						
						else if($item_det->product_type == 'extension' || $item_det->product_type == 'bonus_extension')
						{
							
							//**************** Anoop code start ***************************
							if($item_det->product_type == 'bonus_extension'){
								//echo "bonus_extension";exit;
			                 	$bonus_extension_product=$this->common_model->getProdectId('extension',$product_details[0]->item_id,1);
			                 	$bounus_prod_price['bonus_extension'] = $this->common_model->getProductFee($bonus_extension_product,$this->currId);									
								///$bounus_prod_price['bonus_extension']=$extension_product_price_details['amount'];
								$bonus[$q]="yes";
							}
							
							//**************** Anoop code end ***************************
							if($item_det->product_type == 'extension')
							{
								$product_name[$q] =  $this->user_model->translate_('sales_product_name_extension');
							}
							else
							{
								$product_name[$q] = 'Bonus Extension';
							}
							$extension_details = $this->sales_model->get_extension_details_by_units($product_details[0]->item_id);	
							if($this->session->userdata['language']==4)
							{					
								$purchased_item_names[$q] = '<p>'.$extension_details[0]->extension_option.'</p>';
							}
							else if($this->session->userdata['language']==3)
							{
								$purchased_item_names[$q] = '<p>'.$extension_details[0]->extension_option_spanish.'</p>';								
							}
							elseif($this->session->userdata['language']==6)
							{
								$purchased_item_names[$q] = '<p>'.$extension_details[0]->extension_option_french.'</p>';								
							}
							
							//$product_images[$item_det->product_type][$q][0] = 'public/user/campus/images/extend_course.jpg';

							$image_name = $this->sales_model->get_course_image($selected_items); 

							$product_images[$product_type_image[$q]][$q][0] = 'public/user/outer/course_images/'.$image_name;

						}						
						else if($item_det->product_type == 'poe_soft' || $item_det->product_type == 'bonus_poe_soft')
						{
							//**************** Anoop code start ***************************
							if($item_det->product_type == 'bonus_poe_soft'){
								$bonus_poe_product=$this->common_model->getProdectId('poe_soft','',1);
								$bounus_prod_price['bonus_poe_soft'] =$this->common_model->getProductFee($bonus_poe_product,$this->currId);
								$bonus[$q]="yes";
							}
							
							//**************** Anoop code end ***************************
							if($item_det->product_type == 'poe_soft')
							{
								$product_name[$q] =  $this->user_model->translate_('sales_product_name_poe');						
							}
							else 
							{
								$product_name[$q] =  'Bonus Proof Of Study';						
							}
							$purchased_item_names[$q] = '<p>'.$this->user_model->translate_('sales_product_soft_copy').'</p>';
							$product_images[$item_det->product_type][$q][0] = 'public/user/campus/images/proof_enrolement_image.jpg';
						}
						else if($item_det->product_type == 'poe_hard' || $item_det->product_type == 'bonus_poe_hard')
						{
							
						//**************** Anoop code start ***************************
							if($item_det->product_type == 'bonus_poe_hard'){
								$bonus_poe_hard_product=$this->common_model->getProdectId('poe_hard','',1);
								$bounus_prod_price['bonus_poe_hard'] =$this->common_model->getProductFee($bonus_poe_hard_product,$this->currId);
								$bonus[$q]="yes";
							}
							
							//**************** Anoop code end ***************************
							if($item_det->product_type == 'poe_hard')
							{
								$product_name[$q] =  $this->user_model->translate_('sales_product_name_poe');	
							}
							else
							{
								$product_name[$q] =  'Bonus Proof Of Study HardCopy';	
							}
							$purchased_item_names[$q] = '<p>'.$this->user_model->translate_('sales_product_hard_copy').'</p>';
							$product_images[$item_det->product_type][$q][0]= 'public/user/campus/images/proof_enrolement_image.jpg';
						}
						else if($item_det->product_type == 'hardcopy' || $item_det->product_type == 'bonus_hardcopy') 
						{
							
							//**************** Anoop code start ***************************
							if($item_det->product_type == 'bonus_hardcopy'){
								$bonus_hardcopy_product=$this->common_model->getProdectId('hardcopy',1,1);
								$bounus_prod_price['bonus_hardcopy'] =$this->common_model->getProductFee($bonus_hardcopy_product,$this->currId);
								$bonus[$q]="yes";
							}
							
							//**************** Anoop code end ***************************
							if($item_det->product_type == 'hardcopy')
							{
								$product_name[$q] =  $this->user_model->translate_('Certificate');	
							}
							else
							{
								$product_name[$q] =  'Bonus ICOES';	
							}
							$purchased_item_names[$q] = 'ICOES '.strtolower($this->user_model->translate_('sales_product_hard_copy')); 
							$product_images[$item_det->product_type][$q][0] = 'public/user/campus/images/icoes-large.png';
						}					
						else if($item_det->product_type == 'proof_completion' || $item_det->product_type == 'bonus_proof_completion')
						{
							
							//**************** Anoop code start ***************************
							if($item_det->product_type == 'bonus_proof_completion'){
								
								
								$bonus_poc_product=$this->common_model->getProdectId('proof_completion','',1);
								$bounus_prod_price['bonus_proof_completion'] =$this->common_model->getProductFee($bonus_poc_product,$this->currId);
								$bonus[$q]="yes";
								
							}
							
							//**************** Anoop code end ***************************
							if($item_det->product_type == 'proof_completion')
							{
								$product_name[$q] =  $this->user_model->translate_('sales_product_name_proof_completion');						
							}
							else
							{
								$product_name[$q] =  'Bonus Proof Completion';	
							}
							$purchased_item_names[$q] = $this->user_model->translate_('sales_product_soft_copy');
							$product_images[$item_det->product_type][$q][0] = 'public/user/campus/images/proof_enrolement_image.jpg';
						}
						else if($item_det->product_type == 'proof_completion_hard' || $item_det->product_type == 'bonus_proof_completion_hard')
						{
							
							//**************** Anoop code start ***************************
							if($item_det->product_type == 'bonus_proof_completion_hard'){
								$bonus_poc_hard_product=$this->common_model->getProdectId('proof_completion_hard','',1);
								$bounus_prod_price['bonus_proof_completion_hard'] =$this->common_model->getProductFee($bonus_poc_hard_product,$this->currId);
								$bonus[$q]="yes";
							}
							
							//**************** Anoop code end ***************************
							if($item_det->product_type == 'proof_completion')
							{
								$product_name[$q] =  $this->user_model->translate_('sales_product_name_proof_completion');
							}
							else
							{
								$product_name[$q] =  'Bonus Proof Completion HardCopy';	
							}
							$purchased_item_names[$q] = $this->user_model->translate_('sales_product_hard_copy');
							$product_images[$item_det->product_type][$q][0] = 'public/user/campus/images/proof_enrolement_image.jpg';
						}						
						else if($item_det->product_type == 'transcript' || $item_det->product_type == 'bonus_transcript')
						{
								//**************** Anoop code start ***************************
							if($item_det->product_type == 'bonus_transcript'){
								$bonus_transcript_product=$this->common_model->getProdectId('transcript','',1);
								$bounus_prod_price['bonus_transcript'] =$this->common_model->getProductFee($bonus_transcript_product,$this->currId);
								$bonus[$q]="yes";
							}
							
							//**************** Anoop code end ***************************
							if($item_det->product_type == 'transcript')
							{
								$product_name[$q] =  $this->user_model->translate_('sales_product_name_etranscript');	
							}
							else
							{
								$product_name[$q] =  'Bonus Transcript';	
							}
							$purchased_item_names[$q] = $this->user_model->translate_('sales_product_soft_copy');
							$product_images[$item_det->product_type][$q][0] = 'public/user/campus/images/proof_enrolement_image.jpg';
						}
						else if($item_det->product_type == 'transcript_hard' || $item_det->product_type == 'bonus_transcript_hard')
						{
							
							//**************** Anoop code start ***************************
							if($item_det->product_type == 'bonus_transcript_hard'){
								$bonus_transcript_hard_product=$this->common_model->getProdectId('transcript_hard','',1);
								$bounus_prod_price['bonus_transcript_hard'] =$this->common_model->getProductFee($bonus_transcript_hard_product,$this->currId);
								$bonus[$q]="yes";
							}
							
							//**************** Anoop code end ***************************
							if($item_det->product_type == 'transcript_hard')
							{
								$product_name[$q] =  $this->user_model->translate_('sales_product_name_etranscript');
							}
							else
							{
								$product_name[$q] =  'Bonus Transcript';
							}
							$purchased_item_names[$q] = $this->user_model->translate_('sales_product_hard_copy');
							$product_images[$item_det->product_type][$q][0] = 'public/user/campus/images/proof_enrolement_image.jpg';
						}
						if($item_det->product_type == 'voucher_code' && $this->session->userdata['gift_voucher_applied_details']['product_type'] =='letters')
						{
							$product_name[$q] =  'Letter';
							$product_images[$item_det->product_type][$q][0]='';
						}
						else if($item_det->product_type == 'access')
						{
							$product_name[$q] =  $this->user_model->translate_('sales_product_name_material_access');					
							$purchased_item_names[$q] = $product_details[0]->item_id.' '.$this->user_model->translate_('months');
							$product_images[$item_det->product_type][$q][0] = 'public/user/campus/images/extend_course.jpg';
						}
						
					}
					
					$q++;
					
				}
				}
			}			
			
		}
		else
		{
			$currency_symbol = $this->common_model->get_currency_symbol_from_id(4);			
		}   
		
	    if(isset($bounus_prod_price)){
			$data['bounus_prod_price']=$bounus_prod_price;
		}
		if(isset($bonus)){			
			$data['bonus']      	   = $bonus;
		}

		$content['course_subscription_package_added']  = $course_subscription_package_added;
		$data['bonus_type']      	     = $bonus_type;		
		$data['product_type_image'] 	 = $product_type_image; 
		$data['discount_amount'] 	  	 = $discount_amount; 
		$content['purchased_item_names'] = $purchased_item_names;
		$content['products_in_cart']  	 = $products_in_cart;
		$content['product_name']      	 = $product_name;
		$content['product_images']  	 = $product_images;
		$content['currency_id'] 		 = $currency_id;		
		$content['cart_main_details'] 	 = $cart_main_details;			
		$data['currency_symbol'] 		 = $currency_symbol;
		$data['translate'] 				 = $this->tr_common;
		$data['view'] 					 = 'gift_voucher_cart';
        $data['content'] 				 = $content;				        
		$this->load->view('user/template_new',$data); 

	}


	
	function after_pay($user_id='')
	{		 
	
		$this->tr_common['tr_Registration_complete'] =$this->user_model->translate_('Registration_complete');
		$this->tr_common['tr_Your_payment_was_a_success_and_now_you_are_on_the_road_to_success'] =$this->user_model->translate_('Your_payment_was_a_success_and_now_you_are_on_the_road_to_success_voucher');
		$this->tr_common['tr_Welcome_to_Trendimi'] =$this->user_model->translate_('welcome_hollyandhugo');
		$this->tr_common['tr_Your_payment_was_a_success_and_here_at_Trendimi_we_see_success_in_your_future'] =$this->user_model->translate_('Your_payment_was_a_success_and_here_at_Trendimi_we_see_success_in_your_future');

		$this->tr_common['tr_Your_course_and_or_supplementary_products_are_now_available_in_your_Virtual_Campus'] =$this->user_model->translate_('Your_course_and_or_supplementary_products_are_now_available_in_your_Virtual_Campus');
		$this->tr_common['tr_Purchased_products'] =$this->user_model->translate_('Purchased_products');
		$this->tr_common['tr_No_products_in_the_cart'] =$this->user_model->translate_('No_products_in_the_cart');
		$content = array();
		
		$lang_id = $this->session->userdata('language');
		
		$purchase_note ='';
		if($user_id!="")
		{
			$student_data 	   = $this->user_model->get_student_details($user_id);			
			$data['user_name'] = $student_data[0]->username;

			$us_password = $this->encrypt->decode($student_data[0]->password);
			$str_len = strlen($us_password);    	
		    $char = str_repeat("*", ($str_len-3));
		    $us_password = substr_replace($us_password,$char,3,($str_len-3));

			$data['password']  = $this->encrypt->decode($us_password);
		}		
	
		$this->session->unset_userdata('gift_voucher_cart_session_id');
		$this->session->unset_userdata('voucher_code_applied');
		$this->session->unset_userdata('gift_voucher_applied_details');
		$this->session->unset_userdata('cart_source'); 
		$this->session->unset_userdata('added_user_id');
		$this->session->unset_userdata('gift_voucher_pre_user_id');			
	    $this->session->unset_userdata('ebook_public_email');
	    $this->session->unset_userdata('gift_voucher_package_applying_course');	
	    $this->session->unset_userdata('gift_voucher_security_details');	
	    $this->session->unset_userdata('gift_voucher_applied');	
	    $this->session->unset_userdata('deals');	

	    

		
		$data['translate'] = $this->tr_common;
		$data['view'] = 'gift_voucher_after_pay';
        $data['content'] = $content;		
		$this->load->view('user/template_new',$data); 	
		
	}



	function add_gift_voucher_course($selected_values,$product_id,$currency_id,$source,$ajax=1)
	{

		$new_selected_values = '';	
		$selected_values_array = explode("+",$selected_values);

		if(count($selected_values_array)==1){

			$sess_array = array('gift_voucher_package_applying_course' => $selected_values);			
			$this->session->set_userdata($sess_array);
		}
		
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
		
		if($this->session->userdata('student_logged_in')){

			$user_id = $this->session->userdata['student_logged_in']['id'];	

		}

		if($this->session->userdata('gift_voucher_pre_user_id')){

			$pre_user_id = $this->session->userdata['gift_voucher_pre_user_id']['pre_user_id'];	

		}

		$voucher_id = $this->session->userdata['gift_voucher_applied_details']['voucher_id'];
				
		if(!$this->session->userdata('gift_voucher_cart_session_id'))
		{	
						
			$product_details = $this->common_model->get_product_details($product_id);
			
			$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
			
			$voucher_details = $this->gift_voucher_model->fetch_voucher($voucher_id);
			
			$product_amount = $org_product_amt = $product_price_details['amount'];			
					
			$selected_items_org_price = $this->voucher_code_model->get_selected_items_price_details('course' ,$selected_values_array,$currency_id);	
			
					
			$product_amount = 0;
			$discount_amount = $selected_items_org_price;
			$product_price_details['currency_id'] = $currency_id;
			
			
			session_regenerate_id();
			$sess_array = array('gift_voucher_cart_session_id' => session_id()); 	
			$this->session->set_userdata($sess_array);	

			if($this->session->userdata('student_logged_in')){

				$cart_main_insert_array = array("session_id"=>$this->session->userdata('gift_voucher_cart_session_id'),"user_id"=>$user_id,"source"=>$source,"item_count"=>1,"total_cart_amount"=>$product_amount,"currency_id"=>$product_price_details['currency_id'],'gift_voucher_id'=>$voucher_id);
			}

			if($this->session->userdata('gift_voucher_pre_user_id')){

				$cart_main_insert_array = array("session_id"=>$this->session->userdata('gift_voucher_cart_session_id'),"pre_user_id"=>$pre_user_id,"source"=>$source,"item_count"=>1,"total_cart_amount"=>$product_amount,"currency_id"=>$product_price_details['currency_id'],'gift_voucher_id'=>$voucher_id);
			}
             if(!$this->session->userdata('student_logged_in') && !$this->session->userdata('gift_voucher_pre_user_id')&& $this->session->userdata['gift_voucher_applied_details']['product_type']){
  				$cart_main_insert_array = array("session_id"=>$this->session->userdata('gift_voucher_cart_session_id'),"user_id"=>0,"source"=>$source,"item_count"=>1,"total_cart_amount"=>$product_amount,"currency_id"=>$product_price_details['currency_id'],'gift_voucher_id'=>$voucher_id);
			}
  		  
		    $cart_main_id = $this->common_model->insert_to_table("sales_cart_main",$cart_main_insert_array);
				
			$user_agent_data = array();
			
			$user_agent_data['cart_main_id']  = $cart_main_id;
			$user_agent_data['session_id'] 	  = $this->session->userdata('gift_voucher_cart_session_id');
			$user_agent_data['os'] 			  = $this->agent->platform();
			$user_agent_data['browser'] 	  = $this->agent->agent_string();
			
			$this->common_model->insert_to_table("sales_user_agents",$user_agent_data);		
									
			$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$product_id,"item_amount"=>$product_amount,"currency"=>$product_price_details['currency_id'],'discount_amount'=>$discount_amount);					
			
			$cart_items_id = $this->common_model->insert_to_table("sales_cart_items",$item_details_array);		
			
			$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>$product_details[0]->type,"selected_item_ids"=>$new_selected_values);
			
			$item_id = $this->common_model->insert_to_table("sales_cart_item_details",$items_array);	
			
			$currency_symbol = $this->common_model->get_currency_symbol_from_id($product_price_details['currency_id']);

			if($ajax)
			{

				$data['err_msg']= 0;
				$data['amount'] = $product_amount;
				$data['count'] = 1;
				$data['currency_symbol'] = $currency_symbol;
				echo json_encode($data); 
				exit;    
			}
			else 
			{
				return 1;
			} 	
		}
		else
		{
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('gift_voucher_cart_session_id'));
			foreach($cart_main_details as $cart_main)
			{
				$cart_main_id = $cart_main->id;
				
				$sales_cart_items_id = $this->sales_model->get_cart_items($cart_main_id);					
				
				if(!empty($sales_cart_items_id))
				{
				
					$cart_items_id = $sales_cart_items_id[0]->id;

					$product_details = $this->common_model->get_product_details($product_id);
			
					$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
					
					$voucher_details = $this->gift_voucher_model->fetch_voucher($voucher_id);
					
					$product_amount = $org_product_amt = $product_price_details['amount'];			
							
					$selected_items_org_price = $this->voucher_code_model->get_selected_items_price_details('course' ,$selected_values_array,$currency_id);	

					$product_amount = 0;
					$discount_amount = $selected_items_org_price;	
					$product_price_details['currency_id'] = $currency_id;										
										
					$item_details_array = array("product_id"=>$product_id,"item_amount"=>$product_amount,"currency"=>$product_price_details['currency_id'],'discount_amount'=>$discount_amount);
					
					$this->sales_model->sales_cart_items_update($cart_items_id,$item_details_array);		
				
					$items_array = array("product_type"=>$product_details[0]->type,"selected_item_ids"=>$new_selected_values);
				
					$item_id = $this->sales_model->sales_cart_item_details_update($cart_main_id,$cart_items_id,$items_array);	
					
					$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main->id);
					
					$cart_items_total_amount=@round($cart_items_total_amount,2);
					
					
					$cart_total_items = count($selected_values_array);
					
					$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount);
					$this->sales_model->main_cart_details_update($this->session->userdata('gift_voucher_cart_session_id'),$update_array);
										
					$currency_symbol = $this->common_model->get_currency_symbol_from_id($product_price_details['currency_id']);
					
					if($ajax)
					{
						$data['err_msg']= 0;
						$data['amount'] = $cart_items_total_amount;
						$data['count'] = $cart_total_items;
						$data['currency_symbol'] = $currency_symbol;
						echo json_encode($data); 
						exit;
					}
					else 
					{
						return 1;
					}
				
				}
				else
				{	
					$product_details = $this->common_model->get_product_details($product_id);
			
					$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
					
					$voucher_details = $this->gift_voucher_model->fetch_voucher($voucher_id);
					
					$product_amount = $org_product_amt = $product_price_details['amount'];			
							
					$selected_items_org_price = $this->voucher_code_model->get_selected_items_price_details('course' ,$selected_values_array,$currency_id);	
												
					$product_amount = 0;
					$discount_amount = $selected_items_org_price;
					$product_price_details['currency_id'] = $currency_id;	
					
					$product_amount = 0;
					$product_price_details['currency_id'] = $currency_id;					
										
					$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$product_id,"item_amount"=>$product_amount,"currency"=>$product_price_details['currency_id'],'discount_amount'=>$discount_amount);
					
					$cart_items_id = $this->common_model->insert_to_table("sales_cart_items",$item_details_array);		
				
					$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>$product_details[0]->type,"selected_item_ids"=>$new_selected_values);
				
					$item_id = $this->common_model->insert_to_table("sales_cart_item_details",$items_array);	
					
					$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main->id);
					
					$cart_items_total_amount=@round($cart_items_total_amount,2);
					
					
					$cart_total_items = count($this->sales_model->get_cart_items($cart_main->id));
					
					$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount);
					$this->sales_model->main_cart_details_update($this->session->userdata('gift_voucher_cart_session_id'),$update_array);
										
					$currency_symbol = $this->common_model->get_currency_symbol_from_id($product_price_details['currency_id']);
					
					if($ajax)
					{
						$data['err_msg']= 0;
						$data['amount'] = $cart_items_total_amount;
						$data['count'] = count($selected_values_array);
						$data['currency_symbol'] = $currency_symbol;
						echo json_encode($data); 
						exit;
					}
					else 
					{
						return 1;
					}
				
				}
				//$cart_items = $this->sales_model->get_cart_items($cart_main->id);
			}
		}
	  
	}
	


	function remove_item_from_basket($product_id,$ajax=1)
	{
		if($this->session->userdata('gift_voucher_cart_session_id'))
		{
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('gift_voucher_cart_session_id'));	
			$cart_main_id = $cart_main_details[0]->id;
			$cart_details_by_product = $this->sales_model->get_cart_items_by_product_id($cart_main_id,$product_id);
			if($cart_details_by_product)
			{

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
					
				$this->sales_model->main_cart_details_update($this->session->userdata('gift_voucher_cart_session_id'),$update_array);	
				$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('gift_voucher_cart_session_id'));
				/*echo "<br>After updation";
				echo "<pre>";
				print_r($cart_main_details);	*/
				
				$currency_symbol = $this->common_model->get_currency_symbol_from_id($cart_main_details[0]->currency_id);
				
				if($ajax)
				{
					$data['err_msg']= 0;
					$data['amount'] = $cart_main_details[0]->total_cart_amount;     
					$data['count'] = $cart_main_details[0]->item_count;
					$data['currency_symbol'] = $currency_symbol;
					echo json_encode($data); 
					exit;   
				}

			}
			
		}
		
	}



	function remove_package_from_cart($product_id)
	{
		if($this->session->userdata('gift_voucher_cart_session_id'))
		{
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('gift_voucher_cart_session_id'));	
			$cart_main_id = $cart_main_details[0]->id;
			$cart_details_by_product = $this->sales_model->get_cart_items_by_product_id($cart_main_id,$product_id);
			$cart_details_id = $cart_details_by_product[0]->id;		
			$this->sales_model->delete_item_from_cart_by_product_id($cart_main_id,$cart_details_id,$product_id);	
			$product_details = $this->common_model->get_product_details($product_id);

			if($product_details[0]->type=='package'){

				$ebook_in_cart = $this->sales_model->check_product_type_exist_in_cart($cart_main_id,'eBooks');
				/* Remove that ebooks from cart and */					
				if(!empty($ebook_in_cart))
				{
					$cart_item_id = $ebook_in_cart[0]->cart_items_id;
					$remov_ebook_prod_id = $this->sales_model->get_product_id_from_cart_items_id($cart_main_id,$cart_item_id);
					$cart_details_by_product = $this->sales_model->get_cart_items_by_product_id($cart_main_id,$remov_ebook_prod_id);				
					$cart_details_id = $cart_details_by_product[0]->id;	
					$this->sales_model->delete_item_from_cart_by_product_id($cart_main_id,$cart_details_id,$remov_ebook_prod_id);								
				}				
				$this->session->unset_userdata('offer_added_package_id');
			}

			$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main_id);				
			$cart_items_total_amount = @round($cart_items_total_amount,2);		
			$cart_total_items = count($this->sales_model->get_cart_items($cart_main_id));		
			$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount);	
			$this->sales_model->main_cart_details_update($this->session->userdata('cart_session_id'),$update_array);	
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('cart_session_id'));			
			$currency_symbol = $this->common_model->get_currency_symbol_from_id($cart_main_details[0]->currency_id);			
			$data['err_msg'] = 0;
			$data['amount']  = $cart_main_details[0]->total_cart_amount;     
			$data['count']   = $cart_main_details[0]->item_count;
			$data['currency_symbol'] = $currency_symbol;
			echo json_encode($data); 
			exit;   
			
		}
		
	}




	function package()
	{		
		$content = array();
		$course_details = array();
				
		$this->tr_common['tr_upgrade_your_account_avail_only_registration']  = $this->user_model->translate_('upgrade_your_account_avail_only_registration');
		$this->tr_common['tr_save_over']    = $this->user_model->translate_('save_over');
		$this->tr_common['tr_buy_now_for']  = $this->user_model->translate_('buy_now_for');
		$this->tr_common['tr_read_more']  	= $this->user_model->translate_('read_more');
		$this->tr_common['tr_add_to_bag_2']  	= $this->user_model->translate_('add_to_bag_2');
		$this->tr_common['tr_remove_2']  	= $this->user_model->translate_('remove_2');
		$this->tr_common['tr_complete_registraion']  	= $this->user_model->translate_('complete_registraion_new');
		
		$this->tr_common['tr_package_terms_condetions']  	= $this->user_model->translate_('package_terms_condetions');
				
		$this->tr_common['tr_1_personal_information'] = $this->user_model->translate_('1_personal_information');
		$this->tr_common['tr_2_upgrade_packages'] = $this->user_model->translate_('2_upgrade_packages');
		$this->tr_common['tr_3_review_and_payment'] = $this->user_model->translate_('3_review_and_payment');
		$this->tr_common['tr_4_confirmation'] = $this->user_model->translate_('4_confirmation');
		
		$this->tr_common['tr_registration']     = $this->user_model->translate_('registration');
		$this->tr_common['tr_total_price']     = $this->user_model->translate_('total_price');
		$this->tr_common['tr_SIGN_UP']   =$this->user_model->translate_('SIGN_UP'); 
				
	   
		$content['language']=$this->language;
		$curr_code=$this->user_model->get_currency_id($this->con_name);
		if(isset($this->session->userdata['student_logged_in']['id']))
		{
			$content['user_id'] = $this->session->userdata['student_logged_in']['id'];
		}
		else
		{
			$content['pre_user_id'] = $pre_user_id = $this->session->userdata['gift_voucher_pre_user_id']['pre_user_id'];
			
		}
			
		$curr_id= $this->currId;	
		$added_pack_id = array();
		if($this->session->userdata('gift_voucher_cart_session_id'))
		{			
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('gift_voucher_cart_session_id'));			
			if(!empty($cart_main_details))
			{

				$cart_main_id = $cart_main_details[0]->id;		
				$package_cart_contents = $this->sales_model->check_product_type_exist_in_cart($cart_main_id,'package');
				if(!empty($package_cart_contents))
				{			
					$added_pack_id = explode(",",$package_cart_contents[0]->selected_item_ids);					
				}		

				foreach($cart_main_details as $cart_main)
				{
					$content['cart_count'] = $cart_main->item_count;
					$content['cart_amount'] = $cart_main->total_cart_amount;
					$content['currency_symbol'] = $this->common_model->get_currency_symbol_from_id($cart_main->currency_id);
				}
				
				foreach($cart_main_details as $cart_main)
				{		
					$cart_main_id = $cart_main->id;			
					$products_in_cart = $this->sales_model->get_cart_items($cart_main->id);	
					$q=0;
					if(!empty($products_in_cart))
					{
						foreach($products_in_cart as $prod)
						{
							$cart_item_details = $this->sales_model->get_cart_items_details($cart_main_id,$prod->id);								
							foreach($cart_item_details as $item_det)
							{							
								$added_products_array[$item_det->product_type][] =  $item_det->selected_item_ids;												
							}	
						}
					}
							
				}
			}
			else
			{
				$content['cart_count'] = 0;
				$content['cart_amount'] = 0;
				$content['currency_symbol'] = $this->common_model->get_currency_symbol_from_id($this->currId);
			}
		}		
		else
		{
			$content['cart_count'] = 0;
			$content['cart_amount'] = 0;
			$content['currency_symbol'] = $this->common_model->get_currency_symbol_from_id($this->currId);
			
		}
		
		
		$added_course_count = 0;
		$added_courses = array();
		if(isset($added_products_array['course']))
		{
			$added_course_ids = explode(',',$added_products_array['course'][0]);				
			$added_course_count = count($added_course_ids);
		    foreach($added_course_ids as $key => $c_id)
			{
				if(!$this->session->userdata('gift_voucher_package_applying_course'))
				{
					$sess_array = array('gift_voucher_package_applying_course' =>$c_id); 				
					$this->session->set_userdata($sess_array);	
				}
				else if($this->session->userdata('gift_voucher_package_applying_course')!=$c_id)
				{
					$sess_array = array('gift_voucher_package_applying_course' =>$c_id); 				
					$this->session->set_userdata($sess_array);	
				}
				
				$added_courses[$c_id] =$this->user_model->get_course_name($c_id);				
			}			
		}	

		$content['added_course_count'] = $added_course_count;
		$content['added_courses'] = $added_courses;	

		
		//$package_details = $this->package_model->get_packages();
		//$package_details = $this->package_model->get_packages('non_user');

		/*if($this->session->userdata['ip_address'] == '122.174.232.23')
		{	*/	
		/*}
		else
		{
		}*/
		//$package_details = $this->package_model->get_packages_for_deal();
		$package_details = $this->package_model->get_packages();			
		
		
		foreach($package_details as $pak_det)
		{			
			$product_id = $this->common_model->getProdectId('package',$pak_det->id,1);
			$package_fees[$pak_det->id] = $this->common_model->getProductFee($product_id,$this->currId);
			$package_product_id[$pak_det->id] = $product_id;
		}
		
		
		$content['package_details']=$package_details;
		$content['package_fees']=$package_fees;
		
		$content['product_id']=$product_id;
		$content['package_product_id']=$package_product_id;
		$content['added_pack_id'] = $added_pack_id;
		
		//$content['user_id'] = $user_id;		
		$content['curr_id'] = $this->currId;
		$content['lang_id'] = $this->language;
		
		$data['translate'] = $this->tr_common;
		$data['view'] = 'gift_voucher_package';
		$data['content'] = $content;
		$this->load->view('user/template_new',$data);
		
	

	}

	

	function package_ebooks(){

		$content = array();
		$content['added_ebook_array'] = array();
		$cart_main_id 		   = $this->package_model->get_cart_main_id_from_session_id($this->session->userdata('gift_voucher_cart_session_id'));
		$package_cart_contents = $this->sales_model->check_product_type_exist_in_cart($cart_main_id,'package');	
		$this->tr_common['tr_total_price'] =$this->user_model->translate_('total_price');
		$this->tr_common['tr_complete_registraion'] = $this->user_model->translate_('complete_registraion');	
		if(!empty($package_cart_contents))
		{

			$this->load->model('ebook_model');
			$added_package_id = $this->session->userdata('offer_added_package_id');

			$products_in_package = explode(',',$this->package_model->get_products_in_package($added_package_id));
																							
			$course_subscription = $this->common_model->get_product_by_type('course_subscription');	
			$ebook_options = $this->common_model->get_product_by_type('ebooks');	

			if(in_array($course_subscription[0]->id,$products_in_package))
			{				
				
				$ebook_ids = $this->ebook_model->get_all_active_ebook_ids_comma_seperated($this->language);	
				$ebook_ids = str_replace ( ",",'+', $ebook_ids);
				$added_ebook_array = explode('+',$ebook_ids);
				$ebook_product_id = $this->common_model->getProdectId('ebooks','',count($added_ebook_array));
				$product_details = $this->common_model->get_product_details($ebook_product_id);
				$ebook_selection_limit = $product_details[0]->units;			
				$this->add_bonus_products($ebook_ids,'ebooks',$this->currId,'payment_package',0);
				$content['added_ebook_array'] = $added_ebook_array;

			}
			else
			{
				$ebook_ids = $this->ebook_model->get_all_active_ebook_ids_comma_seperated($this->language);	
				$ebook_product_id = $this->common_model->getProdectId('ebooks','',count(explode(',',$ebook_ids)));

				// Remove if all ebooks added in cart by selecting packages having course subscription previously and removed/changed now  
				$this->remove_item_from_basket($ebook_product_id,0);
			
				$ebook_included = false;
				foreach($ebook_options as $eb_opt)
				{
					if(in_array($eb_opt->id,$products_in_package))
					{	
						$ebook_included   = true;
						$ebook_product_id = $eb_opt->id;
						$product_details  = $this->common_model->get_product_details($eb_opt->id);	
						$ebook_selection_limit = $product_details[0]->units;																
					}
				}

				if(!$ebook_included)
				{
					redirect('/gift_voucher/cart_jj');
				}

			}		

					
			$ebook_array = $this->ebook_model->fetchEbookByLang($this->language);		
			if($this->session->userdata('gift_voucher_cart_session_id'))
			{				
				$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('gift_voucher_cart_session_id'));
				//$cart_main_details = $this->sales_model->get_cart_main_details_package($this->session->userdata('gift_voucher_cart_session_id'),$this->session->userdata('student_temp_id'));
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

					$ebook_cart_contents = $this->sales_model->check_product_type_exist_in_cart($cart_main_id,'ebooks');
					if(!empty($ebook_cart_contents))
					{			
						$added_ebook_id = explode(",",$ebook_cart_contents[0]->selected_item_ids);		
						$content['added_ebook_array'] = $added_ebook_id;			
					}	

					
				}		
				
			}
			else
			{
				$data['cart_count'] = 0;
				$data['cart_amount'] = 0;
				$data['currency_symbol'] = $this->common_model->get_currency_symbol_from_id($this->currId);				
			}	

			$content['ebook_selection_limit'] = $ebook_selection_limit;
			$content['ebook_product_id'] 	  = $ebook_product_id;
			$content['currency_id'] 		  = $this->currId;
			$content['lang_id'] 			  = $this->language;
			$content['ebook_array'] 		  = $ebook_array;	
			$data['view'] 					  = 'gift_voucher_package_ebooks';	
			$data['translate'] 				  = $this->tr_common;	
			$data['content'] 				  = $content;
			$this->load->view('user/template_new',$data); 
		}
		else
		{
			redirect('/gift_voucher/cart');
		}
	}


		

	function validate_products_added(){

		$validation = true;
		$voucher_remaining_note='';

		if($this->session->userdata('offer_added_package_id')){


			$added_package_id = $this->session->userdata('offer_added_package_id');

			$products_in_package = explode(',',$this->package_model->get_products_in_package($added_package_id));
																							
			$course_subscription = $this->common_model->get_product_by_type('course_subscription');	

			$cart_main_id  = $this->package_model->get_cart_main_id_from_session_id($this->session->userdata('gift_voucher_cart_session_id'));

			if(in_array($course_subscription[0]->id,$products_in_package))
			{				
				$ebook_in_cart = $this->sales_model->check_product_type_exist_in_cart($cart_main_id,'eBooks');								
				if(empty($ebook_in_cart))
				{
					$validation = false;
					$voucher_remaining_note = 'Please select ebooks';				
				}				

			}
			else
			{

				$ebook_in_cart = $this->sales_model->check_product_type_exist_in_cart($cart_main_id,'eBooks');								
				if(empty($ebook_in_cart))
				{
					$ebook_options = $this->common_model->get_product_by_type('ebooks');				
			
					$ebook_included = false;
					foreach($ebook_options as $eb_opt)
					{
						if(in_array($eb_opt->id,$products_in_package))
						{	
							$ebook_included   = true;
							$ebook_product_id = $eb_opt->id;
							$product_details  = $this->common_model->get_product_details($eb_opt->id);	
							$ebook_selection_limit = $product_details[0]->units;

							$validation = false;
							$voucher_remaining_note = 'Please select '.$ebook_selection_limit.' ebook(s)';																	
						}
					}

					if(!$ebook_included)
					{
						redirect('/gift_voucher/cart');
					}

				}				

			}

			$data['validation'] 	 = $validation;
			$data['validation_note'] = $voucher_remaining_note;

			echo json_encode($data); 
		    exit;

		}
		else
		{
			redirect('/gift_voucher/cart');
		}

		


	}




	function add_bonus_products($selected_values,$product_type,$currency_id,$source,$ajax=1)
	{		
		
		$this->load->model('voucher_code_model');

		$new_selected_values   = '';	
		$selected_values_array = explode("+",$selected_values);
		$new_selected_values   = str_replace ( "+",',', $selected_values );		
		//$user_id = $this->session->userdata['student_logged_in']['id'];		
		$product_id = $this->common_model->getProdectId($product_type,'',count($selected_values_array));	

		if(!$this->session->userdata('gift_voucher_cart_session_id'))
		{
			redirect('/home/package_check_out');
		}
		else
		{			
		
			$product_details = $this->common_model->get_product_details($product_id);	
			$product_type = $product_details[0]->type;	

			$selected_items_org_price = $this->voucher_code_model->get_selected_items_price_details($product_type ,$selected_values_array,$currency_id);		
			
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('gift_voucher_cart_session_id'));
			foreach($cart_main_details as $cart_main)
			{
				$cart_main_id = $cart_main->id;
				$product_in_cart = $this->sales_model->check_product_type_exist_in_cart($cart_main->id,$product_type);
				if(empty($product_in_cart))
				{	
					$product_amount = 0;				
					$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$product_id,"item_amount"=>$product_amount,"currency"=>$currency_id,'discount_amount'=>$selected_items_org_price);
					
					$cart_items_id = $this->common_model->insert_to_table("sales_cart_items",$item_details_array);		
				
					$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>$product_details[0]->type,"selected_item_ids"=>$new_selected_values);
				
					$item_id = $this->common_model->insert_to_table("sales_cart_item_details",$items_array);	
					
					$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main->id);
					
					$cart_items_total_amount=@round($cart_items_total_amount,2);
					
					
					$cart_total_items = count($this->sales_model->get_cart_items($cart_main->id));
					
					$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount);
					$this->sales_model->main_cart_details_update($this->session->userdata('gift_voucher_cart_session_id'),$update_array);
										
					$currency_symbol = $this->common_model->get_currency_symbol_from_id($currency_id);

					if($ajax){
						$data['err_msg']		  = 0;					
						$data['amount'] 		  = $cart_items_total_amount;
						$data['count'] 			  = $cart_total_items;
						$data['currency_symbol']  = $currency_symbol;
						echo json_encode($data); 
						exit;
					}
				}
				else
				{	

									
					$cart_items_id = $product_in_cart[0]->cart_items_id;
					
					$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
					$product_amount = 0;
					$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$product_id,"item_amount"=>$product_amount,"currency"=>$currency_id);
										
					$item_details_array = array("product_id"=>$product_id,"item_amount"=>$product_amount,"currency"=>$currency_id,'discount_amount'=>$selected_items_org_price);				
					//sales_cart_items_update
					
					$this->sales_model->sales_cart_items_update($cart_items_id,$item_details_array);	
								
					$items_array = array("selected_item_ids"=>$new_selected_values);			
				
					$this->sales_model->sales_cart_item_details_update($cart_main_id,$cart_items_id,$items_array);	
					
					$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main->id);
					
					$cart_items_total_amount=@round($cart_items_total_amount,2);
					
					
					$cart_total_items = count($this->sales_model->get_cart_items($cart_main->id));
					
					$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount);
					$this->sales_model->main_cart_details_update($this->session->userdata('gift_voucher_cart_session_id'),$update_array);
										
					$currency_symbol = $this->common_model->get_currency_symbol_from_id($currency_id);
					
					if($ajax){

						$data['err_msg']		  = 0;					
						$data['amount'] 		  = $cart_items_total_amount;
						$data['count'] 			  = $cart_total_items;
						$data['currency_symbol']  = $currency_symbol;
						echo json_encode($data); 
						exit;
					}
					
				}
				
				//$cart_items = $this->sales_model->get_cart_items($cart_main->id);
			}
			
		 }
		 
	
	}


	function add_package_to_cart($product_id,$currency_id,$package_id,$product_type,$source)
	{
		
		if(!$this->session->userdata('gift_voucher_cart_session_id'))
		{
			redirect('voucher_code');
		}
		else
		{
			
			
			$cart_main_details = $this->sales_model->get_cart_main_details_package($this->session->userdata('gift_voucher_cart_session_id'));
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
					$this->sales_model->main_cart_details_update($this->session->userdata('gift_voucher_cart_session_id'),$update_array);
										
					$currency_symbol = $this->common_model->get_currency_symbol_from_id($product_price_details['currency_id']);

					$sess_array = array('offer_added_package_id' => $package_id);			
					$this->session->set_userdata($sess_array);
					
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
					
					$cart_details_by_product = $this->sales_model->get_cart_items_by_product_id($cart_main_id,$removing_product_id);
					
					$cart_details_id = $cart_details_by_product[0]->id;			
					$this->sales_model->delete_item_from_cart_by_product_id($cart_main_id,$cart_details_id,$removing_product_id);	
					
					$ebook_in_cart = $this->sales_model->check_product_type_exist_in_cart($cart_main_id,'ebooks');
					/* Remove that ebooks from cart and */					
					if(!empty($ebook_in_cart))
					{
						$cart_item_id = $ebook_in_cart[0]->cart_items_id;
						$remov_ebook_prod_id = $this->sales_model->get_product_id_from_cart_items_id($cart_main_id,$cart_item_id);
						$cart_details_by_product = $this->sales_model->get_cart_items_by_product_id($cart_main_id,$remov_ebook_prod_id);				
						$cart_details_id = $cart_details_by_product[0]->id;	
						$this->sales_model->delete_item_from_cart_by_product_id($cart_main_id,$cart_details_id,$remov_ebook_prod_id);							
					}

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
					$this->sales_model->main_cart_details_update($this->session->userdata('gift_voucher_cart_session_id'),$update_array);
										
					$currency_symbol = $this->common_model->get_currency_symbol_from_id($product_price_details['currency_id']);

					$sess_array = array('offer_added_package_id' => $package_id);			
					$this->session->set_userdata($sess_array);
					
					$data['err_msg']= 0;
					$data['amount'] = $cart_items_total_amount;
					$data['count'] = $cart_total_items;
					$data['currency_symbol'] = $currency_symbol;
					$data['removed_package_id'] = $removed_package_id;	
					$data['removed_product_id'] = $removing_product_id;			
					echo json_encode($data); 
					exit;
					
			   }				
				
			}

		}
	}


	function change_package_applying_course_ajax($course_id)
	{
		$course_name = ucwords($this->common_model->get_course_name($course_id)); 
		
		if(!$this->session->userdata('gift_voucher_package_applying_course'))
		{
			$sess_array = array('gift_voucher_package_applying_course' =>$course_id); 				
			$this->session->set_userdata($sess_array);	
		}
		else if($this->session->userdata('gift_voucher_package_applying_course')!=$course_id)
		{
			$sess_array = array('gift_voucher_package_applying_course' =>$course_id); 				
			$this->session->set_userdata($sess_array);	
		}
		$data['err_msg'] = 0;
		$data['msg']= 'Package applying Course changed to '.$course_name;			
		echo json_encode($data); 
		exit;

	}
     function redeem_voucher()
     {
          //echo "inside"; exit;

          $this->load->model('common_model'); 
          $this->load->model('course_model');     
          $this->load->library('campaign_monitor_lib');
          if(!$this->session->userdata('student_logged_in')){
               redirect('home');
          }
          $content = array();      
     
          
          $enrolled_course_ids = array();
          $course_array = array();
          if(isset($this->session->userdata['student_logged_in']['id']))        
          {    
               $user_id = $this->session->userdata['student_logged_in']['id'];
               $enrolled_courses = $this->user_model->get_courses_student($user_id);
               if($enrolled_courses)
               {
                    foreach($enrolled_courses as $en_course)
                    {
                         $enrolled_course_ids[] = $en_course->course_id;             
                    }         
               }
          }
          
          $user_details = $this->user_model->get_stud_details($user_id);
          
          if(isset($this->session->userdata['gift_voucher_applied_details']['code']))
          {
                    
               $voucherDetails = $this->gift_voucher_model->getDetails_of_vcode($this->session->userdata['gift_voucher_applied_details']['code']);    
              
               $content['course_cat']=$this->gift_voucher_model->get_course_categories(); 
                 //echo "<pre>"; print_r($content['course_cat']); exit;    
               if($voucherDetails[0]->courses_idcourses==""||$voucherDetails[0]->courses_idcourses==0)
               {
                    
                    $content['course_set']=$this->gift_voucher_model->get_course_in_cat_name_order($this->session->userdata['language'],$enrolled_course_ids);
                    $content['voucher_type_course_count']=1;
                    $content['voucher_type']=$voucherDetails[0]->voucher_type;
                                   
               }
               else
               {
                    
                    $course_ids = explode(",",$voucherDetails[0]->courses_idcourses);     
                    $content['voucher_type']=$voucherDetails[0]->voucher_type;       
                    $course_ids = array_diff($course_ids,$enrolled_course_ids); 
                    //echo "<pre>"; print_r($course_ids); exit;          
                    if(!empty($course_ids))  
                    {    
                         $content['course_set']=$this->gift_voucher_model->get_these_courses_for_voucher($course_ids);

                    }
                    else
                    {
                         $content['course_set'] = array();
                    }

                    if($voucherDetails[0]->voucher_type =='one_course_from_list')
                    {
                         $content['voucher_type_course_count'] = 1;
                    }
                    elseif($voucherDetails[0]->voucher_type =='two_course_from_list')
                    {
                         $content['voucher_type_course_count'] = 2;
                    }
                    elseif($voucherDetails[0]->voucher_type =='four_course_from_list')
                    {
                         $content['voucher_type_course_count'] = 4;
                    }
                    elseif($voucherDetails[0]->voucher_type =='one_or_more_predefined')
                    {
                         $content['voucher_type_course_count'] = count($course_ids);
                    }
                    else{
                         $content['voucher_type_course_count'] = 1;
                    }
               }
          
               
          }
          else
          {
                    $content['course_set'] = array();
          }

          
          if(isset($_POST['submit']))
       {
               
               //echo "<pre>";print_r($_POST);
              $gift_voucher['gift_voucher'] = $this->input->post('gift_voucher');
               $gift_voucher['security'] = $this->input->post('security');
               $gift_voucher['course_id'] = $course_id =  $this->input->post('course_id');
               
               if(isset($this->session->userdata['uploaded']['security_file']))
               {
                    $security_pdf = $this->session->userdata['uploaded']['security_file'];
               }
               else
               {
                    $security_pdf = $this->input->post('secure_pdf');
               }
               $gift_voucher['secure_pdf'] = $security_pdf;

               
               $voucherDetails = $this->gift_voucher_model->getDetails_of_vcode($gift_voucher['gift_voucher']);    
               //echo "<pre>"; print_r($voucherDetails); exit;
              
               
               $this->form_validation->set_rules('gift_voucher', 'Vouchercode', 'trim|required');
              $date_now = date("Y-m-d");
               
               //echo "<br>befor form validation : <pre>";print_r($content);

               
               if($this->form_validation->run())
               {
                   // echo "Submitted"; 
                    //echo "<pre>"; print_r($gift_voucher);
                   // echo "Count ".count($course_id);
                    //exit;

                    
                    $today_date = date('Y-m-d');
                    for($co=0;$co<count($course_id);$co++)
                    {
                         $users_unit = array();
                         $users_unit = $this->user_model->get_courseunits_id($course_id[$co]);
                         $un = array();
                         foreach($users_unit as $row1)
                         {
                              $un[$row1->units_order] = $row1->course_units;
                         }
                         $student_courseData['student_course_units'] = serialize($un);
                                             
                         $expirity_date = $this->user_model->findExpirityDate($course_id[$co],$date_now,$voucherDetails[0]->idgiftVoucher);
                         $student_courseData['course_id'] = $course_id[$co];
                         $student_courseData['user_id'] = $user_id;
                         $student_courseData['date_enrolled'] = $date_now;
                         $student_courseData['date_expiry'] = $expirity_date;
                         $student_courseData['enroll_type'] = 'coupon';
                         $student_courseData['course_status'] = '0';
                         //echo "inside submit"; exit;
                         $courseEnrId[] = $this->user_model->add_course_student($student_courseData);

                         $course_details=$this->course_model->get_coursedetails($course_id[$co]);             
                         $course_name=$course_details[0]->course_name; 
                         foreach($user_details as $stud)
                         {
                              $email_id=$stud->email;
                              $name = ucwords($stud->first_name.' '.$stud->last_name);

                         }
                         $customfields_subscriber_list = array(
                              array(
                                   'Key' => 'Student',
                                   'Value' => 'True'
                                   ),
                              array(
                                   'Key' => 'FirstCourse',
                                   'Value' =>$course_name
                                   ),
                              array(
                                   'Key' => 'Numberofcourses',
                                   'Value' =>'1'
                                   )

                              );

                         $subscribers = array('EmailAddress' => $email_id, 'Name' => $name, 'CustomFields' => $customfields_subscriber_list);
                         $list_id = "4f5a1b40bb2f7342ba0c594bd5dabe0d";
                         $result = $this->campaign_monitor_lib->update_subscribers($list_id,$subscribers);


                         $customfields_course_list = array(
                       
                                   array(
                                        'Key' => 'PurchaseDate',
                                        'Value'=> $today_date 
                                        ),
                                   array(
                                        'Key' => 'CourseStatusDateChange',
                                        'Value'=> $today_date 
                                        ),
                                   array(
                                        'Key' => 'CourseStatusDateChange',
                                        'Value'=> $today_date 
                                        ),
                                   array(
                                        'Key' => 'Course_Status',
                                        'Value'=>'Not started' 
                                        ),
                                   array(
                                        'Key' => 'Course_Number',
                                        'Value'=> '1'
                                        ),
                                   array(
                                        'Key' => 'Course',
                                        'Value'=> $course_name
                                        )
                         );
                         $list_ids = $this->campaign_model->get_campaign_course_list($course_id[$co]);
                         $ids = '';
                         if(isset($list_ids) && $list_ids !='')
                         {
                              $ids = $list_ids[0]->list_id;
                         }

                        $course_list = array('EmailAddress' =>$email_id,'Name' => $name,'CustomFields' => $customfields_course_list);
                        $result_course_list = $this->campaign_monitor_lib->update_subscribers($ids,$course_list);
                    }
                    
                    $website_id = $this->gift_voucher_model->getVoucherWebIdByVcode($gift_voucher['gift_voucher']);
                    $course_names = $this->course_model->get_course_names_comma_seperated($course_id);
                    
                    $coupon_details = array();
                    $coupon_details['user_id'] = $user_id;
                    $coupon_details['course_id']= implode(",",$course_id);
                    $coupon_details['course_names']= $course_names;
                    $coupon_details['coupon_code']= $gift_voucher['gift_voucher'];
                         
                    if(isset($gift_voucher['security']))
                    {         
                         $coupon_details['redemption_code']= $gift_voucher['security'];
                         $coupon_details['pdf_name']= $gift_voucher['secure_pdf'];
                    }
                    $coupon_details['website_id']=$website_id;
                    $coupon_details['date'] = $date_now;
                         
                    $redeemed_coupen_id = $this->user_model->add_redeemedCoupon($coupon_details);
                    
                    $this->common_model->deactivate_voucher_code($gift_voucher['gift_voucher']);
                    $course_type = array();
                    $course_type['user_course_type'] = 'regular';
                    
                    if($user_details[0]->user_course_type=='no_course')
                    {
                         $this->user_model->student_update($course_type,$user_id);
                    }
                   $this->session->unset_userdata('gift_voucher_applied_details');
                    $this->session->unset_userdata('uploaded');
                    $this->session->set_flashdata('redeem_success', count($course_id).' Course(s) Successfully added to your account');
                    //echo $this->session->flashdata('redeem_success').'flash'.count($course_id); exit;
                    redirect('coursemanager/campus','refresh');
                    
               }
               
          }
          
          
     //$data['lang_id']                  = $lang_id;
     //$content['user_course_type'] = $user_details[0]->user_course_type;
     $this->tr_common['tr_no_courses_to_buy']          =$this->user_model->translate_('no_courses_to_buy');  
     $data['translate'] = $this->tr_common;
     $data['view']                      = 'redeem_voucher';
     $data['content']                   = $content;
     //$this->load->view('user/campus_template',$data);
    // $this->load->view('user/new_inner_template',$data);
     $this->load->view('user/help_center_template',$data);
            
  
     }

     function ajax_gift_voucher_status($voucher)
     {

           $today  =date("Y-m-d");
          
          $or_condition = '(extended_end_date >='.$today.' or enddate >='.$today.')';
          $this->db-> select('*');
        $this->db-> from('giftvoucher');
          $this->db-> where('giftVoucherCode',$voucher);
          $this->db-> where('active','1');
          //$this->db-> where($or_condition);          
          $query = $this -> db -> get();
          $data['temp']= $query->num_rows();
          if($query->num_rows()>=1)
          {
               $voucher_code_details = $query->result();
               
               $data['err_msg']         = 0;
               $data['id']              = $voucher_code_details[0]->idgiftVoucher;
               $data['code']            = $voucher_code_details[0]->giftVoucherCode;
               $data['type']            = $voucher_code_details[0]->voucher_type;
               $data['security_code']  = $voucher_code_details[0]->securitycode_req; 
               
                                   
               $sess_array = array('gift_voucher_applied_details' => $data);
               $this->session->set_userdata($sess_array);                  
          }
          else
          {
               $this->db-> select('*');
               $this->db-> from('giftvoucher');
               $this->db-> where('giftVoucherCode',$voucher);
               $query1 = $this -> db -> get();
               $data['err_msg']          = 1;
               if($query1->num_rows()>=1)
               {
                    
                    $voucher_code_details = $query1->result();
                    if($voucher_code_details[0]->active==0)
                    {
                         $data['err_type']         = "inactive";
                         $data['err_message']      = "Voucher Code Already Used";
                    }
                    else if($voucher_code_details[0]->extended_end_date < $today)
                    {
                         $data['err_type']         = "expired";
                         $data['err_message']      = "Voucher Code Expired";
                    }
               }
               else
               {
                    $data['err_type']    = "not_found";
                    $data['err_message'] = "<p style='color: #333;'><b>Voucher Code not found.</b> Please email us a copy of your voucher to info@eventtrix.com</p>";
               }
               
          }
          echo json_encode($data); 
          exit;
          
     
     }


     function remove_gift_voucher()
     {
     
          if(isset($this->session->userdata['gift_voucher_applied_details']['code']))
          {
          
               $this->session->unset_userdata('gift_voucher_applied_details');
               $this->session->unset_userdata('uploaded');
               
               $data['err_msg']          = 0;
               echo json_encode($data); 
               exit;     
          }
          
          else
          {
               $data['err_msg']          = 1;
               echo json_encode($data); 
               exit;          
          }
     
     }
     function get_courses_for_the_gift_voucher($voucher_code)
     {
          //echo "Voucher Code ".$voucher_code; exit;
          
          $enrolled_course_ids = array();
       $course_array = array();
       $coure_list_html = '';
          if(isset($this->session->userdata['student_logged_in']['id']))        
          {    
               $user_id = $this->session->userdata['student_logged_in']['id'];
               $enrolled_courses = $this->user_model->get_courses_student($user_id);
               if($enrolled_courses)
               {
                    foreach($enrolled_courses as $en_course)
                    {
                         $enrolled_course_ids[] = $en_course->course_id;             
                    }    
               }
          }
          //$content['course_array'] = $this->sales_model->get_course_by_language($this->language,$enrolled_course_ids);
          
          if(isset($voucher_code))
          {              
               $voucherDetails = $this->gift_voucher_model->getDetails_of_vcode($voucher_code);     
               $content['course_cat']=$this->gift_voucher_model->get_course_categories(); 
               $content['voucher_type'] = $voucherDetails[0]->voucher_type;     
                    
               if($voucherDetails[0]->courses_idcourses==""||$voucherDetails[0]->courses_idcourses==0)
               {
                    $content['course_set']=$this->gift_voucher_model->get_course_in_cat_name_order($this->session->userdata['language'],$enrolled_course_ids);
                    $content['voucher_type_course_count']=1;  
                    //$content['temp']='inside if for course_set';                  
               }
               else
               {
                    $course_ids = explode(",",$voucherDetails[0]->courses_idcourses);     
                    $course_ids = array_diff($course_ids,$enrolled_course_ids);           
                    if(!empty($course_ids))  
                    {    
                         $content['course_set']=$this->gift_voucher_model->get_these_courses_for_voucher($course_ids);
                         //$content['temp']='inside elseif for course_set';
                         //$content['course_set']=$this->db->last_query();
                    }
                    else
                    {
                         //$content['temp']='inside else for course_set';
                         $content['course_set'] = array();
                    }
                    
                    if($voucherDetails[0]->voucher_type =='one_course_from_list')
                    {
                         $content['voucher_type_course_count'] = 1;
                    }
                    elseif($voucherDetails[0]->voucher_type =='two_course_from_list')
                    {
                         $content['voucher_type_course_count'] = 2;
                    }
                    elseif($voucherDetails[0]->voucher_type =='four_course_from_list')
                    {
                         $content['voucher_type_course_count'] = 4;
                    }
                    elseif($voucherDetails[0]->voucher_type =='one_or_more_predefined')
                    {
                         $content['voucher_type_course_count'] = count($course_ids);
                    }
                    else
                    {
                         $content['voucher_type_course_count'] = 1;
                    }
               }
               $content['err_msg'] = 0;
          }
          else
          {
               $content['err_msg'] = 1;
          }
          
          //echo "<pre>"; print_r($content); exit;
          
          echo json_encode($content); 
          exit;
          
     }
     
          public function upload_security_code($security_code)
     {    
     
          if (isset($_FILES['file']['name']) && !empty($_FILES['file']['name']))
          {
               if ($_FILES['file']['error'] != 4)
               {
                    
                    $config['upload_path'] = 'public/uploads/deals/pdf/';
                    $config['allowed_types'] = 'gif|jpg|png|pdf';
                    $config['max_size'] = '10000';
                    $config['encrypt_name'] = TRUE;
                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);
                                        
                    if ( $this->upload->do_upload('file'))
                    {
                         $sess_array = array();   
                         $uploaded_data = $this->upload->data();                                                        
                         $sess_array['uploaded'] = array('security_file' => $uploaded_data['file_name'],'security_code'=>$security_code);
                         $this->session->set_userdata($sess_array);   
                         echo 1;
                    }
                    else
                    {                        
                         echo $this->upload->display_errors();
                    }
               }
          }
          else
          {
               
               echo "No file selected";
          }
     }
     

}
	