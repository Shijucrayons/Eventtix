<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class coursemanager extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper(array('form'));
			$this->load->library('encrypt');
    $this->load->helper('url');
    $this->load->database('',true);
    //$this->load->helper(array('language'));
		$this->load->library('form_validation');
		$this->load->model('user_model','',TRUE);
		$this->load->model('ebook_model','',TRUE);
		$this->load->model('common_model','',TRUE);
		$this->load->model('gift_voucher_model','',TRUE);
		$this->load->model('certificate_model','',TRUE);
		$this->load->model('email_model','',TRUE);
		$this->load->model('sales_model','',TRUE);
		$this->load->model('package_model','',TRUE);
		
		//echo $this->input->ip_address();
		/*$this->load->library('geoip_lib');
		$ip = $this->input->ip_address();*/
    $ip = $this->input->ip_address();
   /* $this->code3= $this->geoip_lib->result_country_code3();
    $this->con_name = $this->geoip_lib->result_country_name();*/
	$this->load->library('ip2country_lib');
	$this->con_name = $this->ip2country_lib->getInfo();
		if(isset($_POST['username'])&&isset($_POST['password']))
		{
			$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
   		$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_check_database');
			$content['username'] = $this->input->post('username');
			if(!$this->form_validation->run() == FALSE)
			{//Go to private area
				redirect('home/campus', 'refresh');
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
		
		
		//$this->con_name ='Australia';
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
		
		
		
		 $this->tr_common['tr_forget_password'] =$this->user_model->translate_('forget_password');		
		 $this->tr_common['tr_name']            =$this->user_model->translate_('name');
		 $this->tr_common['tr_stylist_id']      =$this->user_model->translate_('stylist_id');
         $this->tr_common['tr_code']            =$this->user_model->translate_('style_code');
		 $this->tr_common['tr_return_campus']   =$this->user_model->translate_('tr_return_campus');
		 $this->tr_common['tr_sign_out']        =$this->user_model->translate_('tr_sign_out');
         $this->tr_common['tr_eventTrix_certificate'] =$this->user_model->translate_('eventTrix_certificate');
		 $this->tr_common['tr_eventrix'] =$this->user_model->translate_('eventrix');
		$this->tr_common['tr_why_us']   =$this->user_model->translate_('why_us');
		$this->tr_common['tr_about_us']   =$this->user_model->translate_('about_us');
		$this->tr_common['tr_faq']        =$this->user_model->translate_('faq');		 
    	$this->tr_common['tr_contact_us'] =$this->user_model->translate_('contact_us');
		 $this->tr_common['tr_study_time_remaining'] =$this->user_model->translate_('study_remaining');
     	$this->tr_common['tr_change_photo']   = $this->user_model->translate_('change_foto'); 	  	 
		$this->tr_common['tr_edit_details'] = $this->user_model->translate_('edit_your_account_details');
	//	$this->tr_common['tr_account_details'] = $this->user_model->translate_('your_account_details');
		$this->tr_common['tr_change_password'] = $this->user_model->translate_('change_password');
		$this->tr_common['tr_help_center'] = $this->user_model->translate_('help_center');		
		$this->tr_common['tr_extend_course'] = $this->user_model->translate_('extend_course');
		$this->tr_common['tr_certificate'] = $this->user_model->translate_('certificate');
		$this->tr_common['tr_contact_us'] = $this->user_model->translate_('contact_us');
  	    $this->tr_common['tr_fitting_room'] = $this->user_model->translate_('fitting_room');
	    $this->tr_common['tr_buy_voucher']   =$this->user_model->translate_('buy_voucher');
	    $this->tr_common['tr_enter_password'] = $this->user_model->translate_('enter_password');
		$this->tr_common['tr_enter_new_password'] = $this->user_model->translate_('enter_new_password');
		$this->tr_common['tr_confirm_new_password'] = $this->user_model->translate_('confirm_new_password');
		$this->tr_common['tr_change_password'] = $this->user_model->translate_('change_password');
		 $this->tr_common['tr_return_to']   =$this->user_model->translate_('return_to');
		 $this->tr_common['tr_campus']   =$this->user_model->translate_('campus');
		 $this->tr_common['tr_sign']   =$this->user_model->translate_('sign');
		 $this->tr_common['tr_Out']   =$this->user_model->translate_('Out');
		
		$this->tr_common['tr_terms_use']        =$this->user_model->translate_('terms_use');		 
    	$this->tr_common['tr_privacy_policy'] =$this->user_model->translate_('privacy_policy');
		
		 $this->tr_common['tr_proof_enroll'] =$this->user_model->translate_('proof_enroll');
		 $this->tr_common['tr_proof_enroll_text'] =$this->user_model->translate_('proof_enroll_text');
		
		
		
		$this->language = $this->session->userdata('language');
		$this->student_status = $this->session->userdata('student_logged_in');
		$this->course['base_course']=$this->user_model->get_courses_order($this->language);
		$this->menu['top_menu']=$this->user_model->get_top_menu($this->language);
  }
 	function index(){

  }
  
  function campus_old()
 	{
	   
	
	 if(!$this->session->userdata('student_logged_in')){
		  redirect('home');
		}		
	
	 $stud_id  = $this->session->userdata['student_logged_in']['id'];
	// $stud_id  = $this->session->userdata['student_logged_in']['id'];
  
 // $lang_id = $this->session->userdata('language');
    
   $lang_id  = $this->common_model->get_user_lang_id($stud_id);
  $sess_array1 = array('language' => $lang_id);
  $this->session->set_userdata($sess_array1);
	 
	 $this->tr_common['tr_course_progress'] = $this->user_model->translate_('course_progress');
  	 $this->tr_common['tr_more_info'] =$this->user_model->translate_('more_info');
	 $this->tr_common['tr_study_time_remaining'] =$this->user_model->translate_('study_remaining');
	 $this->tr_common['tr_days'] =$this->user_model->translate_('camp_days');
	 $this->tr_common['tr_from_file'] =$this->user_model->translate_('from_file');
	 $this->tr_common['tr_next_course'] =$this->user_model->translate_('next_course');
	   $this->tr_common['tr_my_courses']   =$this->user_model->translate_('my_courses');
		 $this->tr_common['tr_my_ebooks']   =$this->user_model->translate_('my_ebooks');
		 $this->tr_common['tr_buy_voucher']   =$this->user_model->translate_('buy_voucher');
	  $this->tr_common['tr_ebook_for'] = $this->user_model->translate_('ebook_for');
		 $this->tr_common['tr_reduced_from'] = $this->user_model->translate_('reduced_from');
		 $this->tr_common['tr_add_to_bag'] = $this->user_model->translate_('add_to_bag');
	  $this->tr_common['tr_my_marks'] =$this->user_model->translate_('my_marks_new');
	  $this->tr_common['tr_proof_enroll'] =$this->user_model->translate_('proof_enroll');
	  
	  $this->tr_common['tr_ebook_campus_head'] =$this->user_model->translate_('ebook_campus_head');
	  $this->tr_common['tr_ebook_campus_text'] =$this->user_model->translate_('ebook_campus_text');
      $this->tr_common['tr_ebook_for'] = $this->user_model->translate_('ebook_for');
	  $this->tr_common['tr_reduced_from'] = $this->user_model->translate_('reduced_from');
	  
	  $this->tr_common['tr_buy_now'] = $this->user_model->translate_('buy_now');
	  $this->tr_common['tr_download'] = $this->user_model->translate_('download');
	  $this->tr_common['tr_remove'] = $this->user_model->translate_('remove');
	  $this->tr_common['tr_total_price'] = $this->user_model->translate_('total_price');
	   $this->tr_common['tr_check_out'] = $this->user_model->translate_('check_out');
	  
	  
		
		
	 
	 
	 
	 $content=$this->get_student_deatils_for_popup();
	 
	 if(isset($_POST['pro_pic_submit']))
	 {
		 						$config['upload_path'] = 'public/user/images/profile_pic/';
								$config['allowed_types'] = 'gif|jpg|png';
								$config['max_size']	= '1000';
								//$config['max_width']  = '1024';
								//$config['max_height']  = '768';
								
								
								$this->load->library('upload', $config);
								$this->upload->initialize($config);
						
								
								if ( $this->upload->do_upload('pro_pic'))
								{
									$uploaded = array('upload_data' => $this->upload->data());
									$testdata['user_pic'] = $uploaded['upload_data']['file_name'];
									$this->user_model->update_student_details($testdata,$stud_id);
									redirect('coursemanager/campus','refresh');
									
								}
								else
								{
									
									$error['upResult'] = array('error' => $this->upload->display_errors());
									
									//$content['err_prof_pic'] = var_dump($error['upResult']);
																		
									$this->session->set_flashdata('message', $error['upResult']);
			 	 					redirect('coursemanager/campus', 'refresh');
									
									
									//redirect('coursemanager/campus','');
									//echo "err condition<pre>";var_dump($error['upResult']);
									//exit;
								}
	 }
	 $base_courses = $this->user_model->get_courses_order($lang_id);
	 
	 
	 $i=0;
	 $array_index_marks=0;
	
	 foreach ($base_courses as $row) {
		 $course_array[$i]['course_id'] = $row->course_id;
	 	$course_array[$i]['course_name'] = $row->course_name;


		$course_array[$i]['course_summary'] = $row->course_summary;
		//echo $row->course_id;
        $enrolled=$this->user_model->check_user_registered($stud_id,$row->course_id); // check user registered with this course 
		
		if($enrolled) // if enrollled
		{
			foreach ($enrolled as $value)
			{
				$course_progress_array[$array_index_marks] = $this->get_student_progress($value->course_id);
				$course_progress_array[$array_index_marks]['course_status_id']=$value->course_status;
				//echo $value->course_status;	
				if($value->expired==1) // course expired
				{
					
					$course_array[$i]['course_status'] = $this->user_model->translate_('expired');
					$course_array[$i]['course_status_id'] =$value->course_status;
					if($course_progress_array[$array_index_marks]['progressPercnt'] == 100)
					{
						$this->session->set_flashdata('complete_expired',"Your course has expired however you can buy a course subscription");
						$course_array[$i]['resume_link'] = 'sales/course_access_option/'.$value->course_id;
					}
					else if($course_progress_array[$array_index_marks]['progressPercnt'] < 100)
					{
						$this->session->set_flashdata('notcomplete_expired',"your course has expired....");
						$course_array[$i]['resume_link'] = 'coursemanager/extendcourse';
					}
				}
				else 
				{
				if($value->course_status==0) // course not started
				{				
					$course_array[$i]['course_status'] = $this->user_model->translate_('start');
					$course_array[$i]['course_status_id'] =$value->course_status;
					$course_array[$i]['resume_link'] = 'coursemanager/studentcourse/'.$value->course_id;
				}
				else if($value->course_status=='1') // studying
				{
					$course_array[$i]['course_status'] = $this->user_model->translate_('resume'); 
					$course_array[$i]['course_status_id'] =$value->course_status;
					$resume_link = $this->user_model->get_student_resume_link($stud_id,$value->course_id);
					//echo "<pre>";print_r($resume_link);exit;
					if(!empty($resume_link))
					{
					foreach ($resume_link as $row2)
					{
						$course_array[$i]['resume_link'] = $row2->resume_link;
					}
					}
					else
					{
						$course_array[$i]['resume_link'] = 'coursemanager/studentcourse/'.$value->course_id;
					}
					
				}
				else if($value->course_status==2) // course completed
				{
					$course_array[$i]['course_status'] = $this->user_model->translate_('status_completed'); 
					$course_array[$i]['course_status_id'] =$value->course_status;
					$course_array[$i]['resume_link'] = 'coursemanager/studentcourse/'.$value->course_id;
				}
				else if($value->course_status==3 || $value->course_status==4) // certificate applied or issued
				{
					$course_array[$i]['course_status'] =$this->user_model->translate_('status_completed'); 
					$course_array[$i]['course_status_id'] =$value->course_status;
					$course_array[$i]['resume_link'] = 'coursemanager/studentcourse/'.$value->course_id;
				}
				else if($value->course_status==5) // material access
				{
					$course_array[$i]['course_status'] =$this->user_model->translate_('status_completed'); 
					$course_array[$i]['course_status_id'] =$value->course_status;
					$course_array[$i]['resume_link'] = 'coursemanager/studentcourse/'.$value->course_id;
				}
				else if($value->course_status==6) // archived
				{
					$course_array[$i]['course_status'] =$this->user_model->translate_('archived'); 
					$course_array[$i]['course_status_id'] =$value->course_status;
					$course_array[$i]['resume_link'] = 'coursemanager/studentcourse/'.$value->course_id;
				}
				else if($value->course_status==7) // course expired
				{
					
					$course_array[$i]['course_status'] = $this->user_model->translate_('expired');
					$course_array[$i]['course_status_id'] =$value->course_status;
					if($course_progress_array[$array_index_marks]['progressPercnt'] == 100)
					{
						$this->session->set_flashdata('complete_expired',"Your course has expired however you can buy a course subscription");


						$course_array[$i]['resume_link'] = 'sales/course_access_option/'.$value->course_id;
					}
					else if($course_progress_array[$array_index_marks]['progressPercnt'] < 100)
					{
						$this->session->set_flashdata('notcomplete_expired',"your course has expired....");
						$course_array[$i]['resume_link'] = 'coursemanager/extendcourse';
					}
				}
				
				}
				$array_index_marks++;				
				$i++;
			}
		}
		
      }
	 
	  foreach ($base_courses as $row) {
		   $course_array[$i]['course_id'] = $row->course_id;
	 	$course_array[$i]['course_name'] = $row->course_name;
		$course_array[$i]['course_summary'] = $row->course_summary;
        $enrolled=$this->user_model->check_user_registered($stud_id,$row->course_id); // check user registered with this course 
	 	if(!$enrolled) // not enrolled to course
		{
			if($row->course_status==0)
				{
				$course_array[$i]['course_status_id'] = 100; // Coming soon
				$course_array[$i]['course_status']= 'Coming soon';
				$course_array[$i]['resume_link'] ='home/coursedetails/'.$row->course_id;
				}
				else
				{
				$course_array[$i]['course_status_id'] = 8; // Buy 
				$course_array[$i]['course_status']= $this->user_model->translate_('buy_now');
				$course_array[$i]['resume_link'] ='home/package_buy_another_course/'.$stud_id.'/'.$row->course_id;
				}
			$i++;
		}
	  }
		
		
		$this->load->model('ebook_model');
		$ebArray = $this->ebook_model->fetchEbookByLang($this->language);
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
				//translations
				$content['tr_trndimi_ebooks'] = $this->user_model->translate_('trendimi_ebooks');
				$content['tr_trndimi_ebooks_text'] =$this->user_model->translate_('trndimi_ebooks_text');
				$unit=1;
				if($i==1)
				{ 
				$unit='2';  
				}
				
				$prodectId[$i] = $this->common_model->getProdectId('ebooks','',$unit);	
				$ebookPrice =$this->common_model->getProductFee($prodectId[$i],$this->currId);
				$content['fake_amount'][$i] =$ebookPrice['fake_amount'];
				$content['amount'][$i] =$ebookPrice['amount'];
				//$content['currency_symbol'][$i] =$ebookPrice['currency_symbol'];
				$content['currency_id'][$i] =$ebookPrice['currency_id'];
				
				
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
		
		/*if($this->session->userdata('cart_session_id'))
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
			
		}*/
		
		$currency_id = $this->currId;
		$added_ebook_array = array();
		
		 if($this->session->userdata('cart_session_id'))
		{
			
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('cart_session_id'));
			
			//echo "Session id ".$this->session->userdata('cart_session_id');
			if(!empty($cart_main_details))
			{
				foreach($cart_main_details as $cart_main)
				{
					$data['cart_count'] = $cart_main->item_count;
					$data['cart_amount'] = $cart_main->total_cart_amount;
					$data['currency_symbol'] = $this->common_model->get_currency_symbol_from_id($cart_main->currency_id);
				}
				
				$cart_main_id = $cart_main_details[0]->id;		
				$cart_prod_type = array('ebooks','ebook_guide');
				$ebook_added_in_cart = $this->sales_model->check_product_type_exist_in_cart($cart_main_id,$cart_prod_type);
				if(!empty($ebook_added_in_cart))
				{					
				$added_ebooks = $ebook_added_in_cart[0]->selected_item_ids;						
				$added_ebook_array = explode(',',$added_ebooks);
				}			
			}
			else
			{
				$data['cart_count'] = 0;
				$data['cart_amount'] = 0;
				$data['currency_symbol'] = $this->common_model->get_currency_symbol_from_id($this->currId);
			}
		}
		else
		{
			$data['cart_count'] = 0;
			$data['cart_amount'] = 0;
			$data['currency_symbol'] = $this->common_model->get_currency_symbol_from_id($this->currId);
			
		}		
	  
	   $sess_array = array('cart_source' => '/coursemanager/campus');
		
		$this->session->set_userdata($sess_array);
		
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
		
		
		$content['ebook_array']   	     = $ebook_array;
		$content['ebook_price_details'] = $ebook_price_details;
		$content['ebook_guide_price_details'] = $ebook_guide_price_details;
		
		
		
		//$content['colour_wheel_subscription'] = $this->user_model->colour_wheel_subcribed($stud_id);
		$content['suscribedEbooks'] =$suscribedEbooks= $this->ebook_model->suscribed_ebooks($stud_id);
		
	    $content['added_ebook_array']	= $added_ebook_array;
		$content['studentDetails']=$this->user_model->get_student_details($stud_id);
		$data['content'] = $ebDetails;
		$data['lang_id']				 = $lang_id;
		$data['base_courses'] 			= $base_courses;
		$data['course_array'] 			= $course_array;	
		
		$data['course_progress_array']  = $course_progress_array;
		$data['translate'] = $this->tr_common;
		
		
		/*if($this->session->userdata['ip_address'] == '117.242.192.217')
		{
		$data['view'] 					= 'campus_test';
		}
		else
		{*/
			
			$data['view'] 					= 'campus_old';
		/*}*/
   		$data['content'] 				= $content;
		 
 		$this->load->view('user/campus_template',$data);
		
	
	  
  }
	
 	function campus()
 	{
	   
	
	 if(!$this->session->userdata('student_logged_in')){
		  redirect('home');
		}		
	
	 $stud_id  = $this->session->userdata['student_logged_in']['id'];
	// $stud_id  = $this->session->userdata['student_logged_in']['id'];
  
 // $lang_id = $this->session->userdata('language');
    
   $lang_id  = $this->common_model->get_user_lang_id($stud_id);
  $sess_array1 = array('language' => $lang_id);
  $this->session->set_userdata($sess_array1);
	 
	 $this->tr_common['tr_course_progress'] = $this->user_model->translate_('course_progress');
  	 $this->tr_common['tr_more_info'] =$this->user_model->translate_('more_info');
	 $this->tr_common['tr_study_time_remaining'] =$this->user_model->translate_('study_remaining');
	 $this->tr_common['tr_days'] =$this->user_model->translate_('camp_days');
	 $this->tr_common['tr_from_file'] =$this->user_model->translate_('from_file');
	 $this->tr_common['tr_next_course'] =$this->user_model->translate_('next_course');
	   $this->tr_common['tr_my_courses']   =$this->user_model->translate_('my_courses');
		 $this->tr_common['tr_my_ebooks']   =$this->user_model->translate_('my_ebooks');
		 $this->tr_common['tr_buy_voucher']   =$this->user_model->translate_('buy_voucher');
	  $this->tr_common['tr_ebook_for'] = $this->user_model->translate_('ebook_for');
		 $this->tr_common['tr_reduced_from'] = $this->user_model->translate_('reduced_from');
		 $this->tr_common['tr_add_to_bag'] = $this->user_model->translate_('add_to_bag');
	  $this->tr_common['tr_my_marks'] =$this->user_model->translate_('my_marks_new');
	  $this->tr_common['tr_proof_enroll'] =$this->user_model->translate_('proof_enroll');
	  
	  $this->tr_common['tr_ebook_campus_head'] =$this->user_model->translate_('ebook_campus_head');
	  $this->tr_common['tr_ebook_campus_text'] =$this->user_model->translate_('ebook_campus_text');
      $this->tr_common['tr_ebook_for'] = $this->user_model->translate_('ebook_for');
	  $this->tr_common['tr_reduced_from'] = $this->user_model->translate_('reduced_from');
	  
	  $this->tr_common['tr_buy_now'] = $this->user_model->translate_('buy_now');
	  $this->tr_common['tr_download'] = $this->user_model->translate_('download');
	  $this->tr_common['tr_remove'] = $this->user_model->translate_('remove');
	  $this->tr_common['tr_total_price'] = $this->user_model->translate_('total_price');
	   $this->tr_common['tr_check_out'] = $this->user_model->translate_('check_out');
	  
	  
		
		
	 
	 
	 
	 $content=$this->get_student_deatils_for_popup();
	 
	 if(isset($_POST['pro_pic_submit']))
	 {
		 						$config['upload_path'] = 'public/user/images/profile_pic/';
								$config['allowed_types'] = 'gif|jpg|png';
								$config['max_size']	= '1000';
								//$config['max_width']  = '1024';
								//$config['max_height']  = '768';
								
								/*$config['crop'] =  array(
								'image_library'   => 'gd2',
								'maintain_ratio'  =>  FALSE,
								'width'           =>  250,
								'height'          =>  250,
								);
								$this->image_lib->crop();*/
								
								$this->load->library('upload', $config);
								$this->upload->initialize($config);
						
								
								if ( $this->upload->do_upload('pro_pic'))
								{
									$uploaded = array('upload_data' => $this->upload->data());
									$testdata['user_pic'] = $uploaded['upload_data']['file_name'];
									$this->user_model->update_student_details($testdata,$stud_id);
									redirect('coursemanager/campus','refresh');
									
								}
								else
								{
									
									$error['upResult'] = array('error' => $this->upload->display_errors());
									
									//$content['err_prof_pic'] = var_dump($error['upResult']);
																		
									$this->session->set_flashdata('message', $error['upResult']);
			 	 					redirect('coursemanager/campus', 'refresh');
									
									
									//redirect('coursemanager/campus','');
									//echo "err condition<pre>";var_dump($error['upResult']);
									//exit;
								}
	 }
	 $base_courses = $this->user_model->get_courses_order($lang_id);
	 
	 
	 $i=0;
	 $array_index_marks=0;
	$enrolled_course_array = array();
		$other_course_array =array();
		
		$en_course_count = 0;
		$other_course_count = 0;
		
		$enrolled_courses_html = "";
		$other_courses_html = "";
	 foreach ($base_courses as $row) {
		 $course_array[$i]['course_id'] = $row->course_id;
	 	$course_array[$i]['course_name'] = $row->course_name;


		$course_array[$i]['course_summary'] = $row->course_summary;
		//echo $row->course_id;
        $enrolled=$this->user_model->check_user_registered($stud_id,$row->course_id); // check user registered with this course 
		
		if($enrolled) // if enrollled
		{ // if enrollled
				foreach ($enrolled as $value)
				{
					/*if($en_course_count%2==0){
						$enrolled_courses_html .= '</div><div class="clearfix row margin_bottom">';	
					}*/
					/*$enrolled_courses_html .= '<div class="span6">
                		<div class="camp_gray">
               			<div class="camp_marksimg">
                		<img class="responsive" src="/public/user/outer/course_images/campus_your_courses/'.$row->home_image.'">
                		</div>';*/
					$enrolled_course_array[$en_course_count]['course_id'] = $row->course_id;
	 				$enrolled_course_array[$en_course_count]['course_name'] = $row->course_name;
			
					$progress = $this->get_student_progress($value->course_id);
					/*if($this->session->userdata['ip_address'] == '117.247.185.9')
					{
						echo "<pre>";print_r($progress);exit;
					}*/
					
					$enrolled_course_array[$en_course_count]['course_status_id']=$value->course_status;
					if(isset($value->sample_course) && $value->sample_course=="yes"){
						$enrolled_course_array[$en_course_count]['sample_course'] ="yes";
					}
					//echo $value->expired;exit;
					if($value->expired==1) // course expired
					{
						$enrolled_course_array[$en_course_count]['course_status'] = $this->user_model->translate_('expired');
						$enrolled_course_array[$en_course_count]['course_status_id'] =$value->course_status;
						if($progress['progressPercnt'] == 100)
						{
							if($progress['coursePercentage']<55)
							$pass_text = "(Not Passed)";
							else
							$pass_text = "";
							$enrolled_courses_html .= '
							<div style="padding:20px; background:#fff; margin-bottom:20px">
                   <div class="row text-left">
                    <div class="col-lg-9 col-md-9 col-sm-9">
								<h4><strong>'.$row->course_name.'</strong></h4>
								<div class="progress">
  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="'.$progress['coursePercentage'].'%"
  aria-valuemin="0" aria-valuemax="100" style="width:'.$progress['coursePercentage'].'%">
    '.$progress['coursePercentage'].'% (My Marks)
  </div>
</div>
<div class="progress">
  <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="'.$progress['progressPercnt'].'"
  aria-valuemin="0" aria-valuemax="100" style="width:'.$progress['progressPercnt'].'%">
    '.$progress['progressPercnt'].'% Complete (Course Progress)
  </div>
</div>

 </div>
    
          <div class="col-lg-3 col-md-3 col-sm-3">
          <h4 class="top25m">Completed'.' '.$pass_text.'(Expired)</h4>
          <p><a href="/coursemanager/studentcourse/'.$value->course_id.'" class="btn_1">Go to course</a></p>
		  </div></div></div>
         ';
						}
						else if($progress['progressPercnt'] < 100)
						{
							$enrolled_courses_html .= '
							<div style="padding:20px; background:#fff; margin-bottom:20px">
                   <div class="row text-left">
                    <div class="col-lg-9 col-md-9 col-sm-9">
								<h4><strong>'.$row->course_name.'</strong></h4>
								<div class="progress">
  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="'.$progress['coursePercentage'].'%"
  aria-valuemin="0" aria-valuemax="100" style="width:'.$progress['coursePercentage'].'%">
    '.$progress['coursePercentage'].'% (My Marks)
  </div>
</div>
<div class="progress">
  <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="'.$progress['progressPercnt'].'"
  aria-valuemin="0" aria-valuemax="100" style="width:'.$progress['progressPercnt'].'%">
    '.$progress['progressPercnt'].'% Complete (Course Progress)
  </div>
</div>

 </div>
    
          <div class="col-lg-3 col-md-3 col-sm-3">
          <h4 class="top25m">Expired(Not completed)</h4>
          <p><a href="/coursemanager/extendcourse/'.$value->course_id.'" class="btn_1">Extend</a></p></div></div></div>'
		  ;
						}
					}
					else 
					{
						if($value->course_status==0) // course not started
						{
							$enrolled_courses_html .= '
							<div style="padding:20px; background:#fff; margin-bottom:20px">
                   <div class="row text-left">
                    <div class="col-lg-9 col-md-9 col-sm-9">
								<h4><strong>'.$row->course_name.'</strong></h4>
								<div class="progress">
  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="'.$progress['coursePercentage'].'%"
  aria-valuemin="0" aria-valuemax="100" style="width:'.$progress['coursePercentage'].'%">
    '.$progress['coursePercentage'].'% (My Marks)
  </div>
</div>
<div class="progress">
  <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="'.$progress['progressPercnt'].'"
  aria-valuemin="0" aria-valuemax="100" style="width:'.$progress['progressPercnt'].'%">
    '.$progress['progressPercnt'].'% Complete (Course Progress)
  </div>
</div>

 </div>
    
          <div class="col-lg-3 col-md-3 col-sm-3">
								<h4 class="top25m">Not started</h4>
								<p><a href="/coursemanager/studentcourse/'.$value->course_id.'" target="_self" class="btn_1">go to course</a></p></div>
					</div>
                
				</div>';
						}
						else if($value->course_status=='1') // studying
						{
							$enrolled_courses_html .= '
							<div style="padding:20px; background:#fff; margin-bottom:20px">
                   <div class="row text-left">
                    <div class="col-lg-9 col-md-9 col-sm-9">
								<h4><strong>'.$row->course_name.'</strong></h4>
								<div class="progress">
  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="'.$progress['coursePercentage'].'%"
  aria-valuemin="0" aria-valuemax="100" style="width:'.$progress['coursePercentage'].'%">
    '.$progress['coursePercentage'].'% (My Marks)
  </div>
</div>
<div class="progress">
  <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="'.$progress['progressPercnt'].'"
  aria-valuemin="0" aria-valuemax="100" style="width:'.$progress['progressPercnt'].'%">
    '.$progress['progressPercnt'].'% Complete (Course Progress)
  </div>
</div>

 </div>
    
          <div class="col-lg-3 col-md-3 col-sm-3">
								<h4 class="top25m">Studying</h4>
								<p><a href="/coursemanager/studentcourse/'.$value->course_id.'" target="_self" class="btn_1">go to course</a></p></div>
					</div>
                
				</div>';
						}
						else if($value->course_status==2) // course completed
						{
							if($progress['coursePercentage']<55)
							$pass_text = "(Not Passed)";
							else
							$pass_text = "";
							$enrolled_courses_html .= '
							<div style="padding:20px; background:#fff; margin-bottom:20px">
                   <div class="row text-left">
                    <div class="col-lg-9 col-md-9 col-sm-9">
								<h4><strong>'.$row->course_name.'</strong></h4>
								<div class="progress">
  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="'.$progress['coursePercentage'].'%"
  aria-valuemin="0" aria-valuemax="100" style="width:'.$progress['coursePercentage'].'%">
    '.$progress['coursePercentage'].'% (My Marks)
  </div>
</div>
<div class="progress">
  <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="'.$progress['progressPercnt'].'"
  aria-valuemin="0" aria-valuemax="100" style="width:'.$progress['progressPercnt'].'%">
    '.$progress['progressPercnt'].'% Complete (Course Progress)
  </div>
</div>

 </div>
    
          <div class="col-lg-3 col-md-3 col-sm-3">
								<h4 class="top25m">Completed '.$pass_text.'</h4>
								<p><a href="/coursemanager/studentcourse/'.$value->course_id.'" target="_self" class="btn_1">go to course</a></p></div>
					</div>
                
				</div>';
						}
						else if($value->course_status==3 || $value->course_status==4) // certificate applied or issued
						{
							if($progress['coursePercentage']<55)
							$pass_text = "(Not Passed)";
							else
							$pass_text = "";
							$enrolled_courses_html .= '
							<div style="padding:20px; background:#fff; margin-bottom:20px">
                   <div class="row text-left">
                    <div class="col-lg-9 col-md-9 col-sm-9">
								<h4><strong>'.$row->course_name.'</strong></h4>
								<div class="progress">
  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="'.$progress['coursePercentage'].'%"
  aria-valuemin="0" aria-valuemax="100" style="width:'.$progress['coursePercentage'].'%">
    '.$progress['coursePercentage'].'% (My Marks)
  </div>
</div>
<div class="progress">
  <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="'.$progress['progressPercnt'].'"
  aria-valuemin="0" aria-valuemax="100" style="width:'.$progress['progressPercnt'].'%">
    '.$progress['progressPercnt'].'% Complete (Course Progress)
  </div>
</div>

 </div>
    
          <div class="col-lg-3 col-md-3 col-sm-3">
								<h4 class="top25m">Completed '.$pass_text.'</h4>
								<p><a href="/coursemanager/studentcourse/'.$value->course_id.'" target="_self" class="btn_1">go to course</a></p></div>
					</div>
                
				</div>';
						}
						else if($value->course_status==5) // material access
						{
							$enrolled_courses_html .= '
							<div style="padding:20px; background:#fff; margin-bottom:20px">
                   <div class="row text-left">
                    <div class="col-lg-9 col-md-9 col-sm-9">
								<h4><strong>'.$row->course_name.'</strong></h4>
								<div class="progress">
  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="'.$progress['coursePercentage'].'%"
  aria-valuemin="0" aria-valuemax="100" style="width:'.$progress['coursePercentage'].'%">
    '.$progress['coursePercentage'].'% (My Marks)
  </div>
</div>
<div class="progress">
  <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="'.$progress['progressPercnt'].'"
  aria-valuemin="0" aria-valuemax="100" style="width:'.$progress['progressPercnt'].'%">
    '.$progress['progressPercnt'].'% Complete (Course Progress)
  </div>
</div>

 </div>
    
          <div class="col-lg-3 col-md-3 col-sm-3">
								<h4 class="top25m">Completed</h4>
								<p><a href="/coursemanager/studentcourse/'.$value->course_id.'" target="_self" class="btn_1">go to course</a></p> </div>
</div>

 </div>';
							
						}
						else if($value->course_status==6) // archived
						{
							$enrolled_courses_html .= '
							<div style="padding:20px; background:#fff; margin-bottom:20px">
                   <div class="row text-left">
                    <div class="col-lg-9 col-md-9 col-sm-9">
								<h4><strong>'.$row->course_name.'</strong></h4>
								<div class="progress">
  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="'.$progress['coursePercentage'].'%"
  aria-valuemin="0" aria-valuemax="100" style="width:'.$progress['coursePercentage'].'%">
    '.$progress['coursePercentage'].'% (My Marks)
  </div>
</div>
<div class="progress">
  <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="'.$progress['progressPercnt'].'"
  aria-valuemin="0" aria-valuemax="100" style="width:'.$progress['progressPercnt'].'%">
    '.$progress['progressPercnt'].'% Complete (Course Progress)
  </div>
</div>

 </div>
    
          <div class="col-lg-3 col-md-3 col-sm-3">
								<h4 class="top25m">Completed</h4>
								<p><a href="#" target="_self" class="btn_1">Archived</a></p> </div>
</div>

 </div>';
						}
					
					}

					/*$enrolled_courses_html .= '
					</div>
                	<h5 class="center_aligned">'.$row->course_name.'</h5>
                	</div>';*/				
				$en_course_count++;
			}
		}
		else{
			$product = $this->user_model->get_product_id($row->course_id);
		 foreach ($product as $value) {
      $product_id = $value->id;
    }
	
	$price_details_array = $this->common_model->getProductFee($product_id,$this->currId);
			$showing_courses_count = 6;
			$showing_courses_array = array(1,2,3,4,5,6,7,8,9,10);
			if(!in_array($row->course_id,$showing_courses_array)){
				continue;
			}
			/*if($showing_courses_count==$other_course_count){
				continue;
			}*/
			
			if($row->course_status==0){
				$link= '/home/course_details_all/'.$row->course_id.'';
			}
			else if($row->course_status==1){
				$link= '/home/buy_another_course/stud_id/'.$stud_id.'/cour_id/'.$row->course_id.'';
				
			}
			else{
				$link='#';
				continue;
			}
			$other_courses_html .= '
<div class="col-md-4 col-sm-6 wow zoomIn" data-wow-delay="0.'.$other_course_count.'s">
					<div class="hotel_container">
						<div class="img_container">
							<a href="'.$link.'">
							<img src="/public/user/images/'.$row->campus_image.'" width="800" height="533" class="img-responsive" alt="">
							<div class="short_info hotel">
								'.$row->course_name.'
							</div>
							</a>
						</div>
						<div class="hotel_title" style="text-align:center">
						<a href="'.$link.'" class="btn_1">Buy Now</a>
						</div>
					</div><!-- End box tour -->
				</div><!-- End col-md-4 -->

                ';
			
			$other_course_count++;
			
		}
      }
	 
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
		
	//	echo "Lang id ".$this->language;
		
	$ebook_array = $this->ebook_model->fetchEbookByLang($this->language);
		
		
	$content['ebook_array']   	     = $ebook_array;
	$content['ebook_offer_options'] = $ebook_offer_options;
	$content['ebook_product_ids']   = $ebook_product_ids;
	$content['ebook_units'] 		 = $ebook_units;
	$content['ebook_price_details'] = $ebook_price_details;
		
	$ebook_purchased_details =$this->ebook_model->purchased_ebooks($stud_id);
	  
	$data['ebook_purchased_details'] = $ebook_purchased_details;
		
		
	if($this->session->userdata('cart_session_id'))
	{
			
		$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('cart_session_id'));
			
		//echo "Session id ".$this->session->userdata('cart_session_id');
		if(!empty($cart_main_details))
		{
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
	}
	else
	{
		$data['cart_count'] = 0;
		$data['cart_amount'] = 0;
		$data['currency_symbol'] = $this->common_model->get_currency_symbol_from_id($this->currId);
		
	}		
	 
	$sess_array = array('cart_source' => '/coursemanager/campus');
	$this->session->set_userdata($sess_array);
		
	$content['colour_wheel_subscription'] = $this->user_model->colour_wheel_subcribed($stud_id);
	$suscribedEbooks= $this->ebook_model->suscribed_ebooks($stud_id);
	if(!empty($suscribedEbooks))
	{
		$suscribedEbooks = array_unique($suscribedEbooks);
	}
	$content['enrolled_courses_without_sample'] = $this->user_model->get_courses_student($stud_id); 
	$content['suscribedEbooks']      = $suscribedEbooks;
	$data['lang_id']				 = $lang_id;
	$data['base_courses'] 			= $base_courses;
	$data['enrolled_course_array']   = $enrolled_course_array;	
	$data['other_course_array']	  = $other_course_array;
	
	$data['enrolled_courses_html']   = $enrolled_courses_html;	
	$data['other_courses_html']	  = $other_courses_html;
		$data['translate'] = $this->tr_common;
		
		
		/*if($this->session->userdata['ip_address'] == '117.242.192.217')
		{
		$data['view'] 					= 'campus_test';
		}
		else
		{*/
			
			$data['view'] 					= 'campus';
		/*}*/
   		$data['content'] 				= $content;
		 
 		$this->load->view('user/campus_template',$data);
		
	
	  
  }
	
	function get_student_deatils_for_popup()
	{
	
	  $this->tr_common['tr_account_details']   =$this->user_model->translate_('account_details');	      
	  $this->tr_common['tr_first_name']   =$this->user_model->translate_('First_Name');	      
	  $this->tr_common['tr_email'] =$this->user_model->translate_('email');
	   $this->tr_common['tr_Email_pop'] =$this->user_model->translate_('email');
	  
	  $this->tr_common['tr_telephone'] =$this->user_model->translate_('Telephone');
      $this->tr_common['tr_country'] =$this->user_model->translate_('country');
	  $this->tr_common['tr_dob'] =$this->user_model->translate_('dob');
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
			$content['dob'] = $row->dob;
		  }
		  else $content['dob']=NULL;
		   $content['email'] = $row->email;
		  $content['contact_no'] = $row->contact_number;
		  $content['country_set'] = $row->country_id ;
		  
		  if(($row->user_pic)!=''){
			$content['user_pic'] = $row->user_pic;
		  }
		  else $content['user_pic']=NULL;
		  
		}		
		return $content;
	
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
		$progress['course_status']    = $course_status;
		$progress['course_id']        = $course_id;
		$progress['course_name']      = $course_name;
		$progress['coursePercentage'] = $coursePercentage;
		$progress['progressPercnt']   = $progressPercnt;
		$progress['daysremaining']    = $numberOfDaysRemaining;
		$progress['course_passed']    = $course_passed;
		
		
		return $progress;
		
	
	
	}
	
	
	
	
	function coursedetails(){
		
		if(!$this->session->userdata('student_logged_in')){
		  redirect('home');
		}
			$stud_id=$this->session->userdata['student_logged_in']['id'];
			$courses=$this->user_model->get_courses_student($stud_id);
			foreach ($courses as $value) {
				
			
			$resume_link = $this->user_model->get_student_resume_link($stud_id,$value->course_id);
			}
	   	$content=$this->get_student_deatils_for_popup();
		$course_pagevisited=array();
		$courses_student=array();
		foreach ($resume_link as $key => $value) {
		  if(!in_array($value->course_id, $courses_student)){
			array_push($courses_student,$value->course_id);
			array_push($course_pagevisited,$value->resume_link);
		  }
		}
		$courseParrentArray=array();
		$courseBaseNameArray = array();
		$coursecaptionArray=array();
		$coursebaseid=array();
		$basename=array('styleme','styleyou','makeup','wedding planner','nail artist','hair stylist');
		switch ($this->language) {
		  case 1: $coursebaseid=array('31','32','33','34','35','36');
			break;
		  case 2: $coursebaseid=array('21','22','23','24','25','26');
			break;
		  case 3: $coursebaseid=array('11','12','13','14','15','16');
			break;
		  case 4: $coursebaseid=array('1','2','3','4','5','6');
			break;
		}
		foreach ($courses_student as $value) {
		  $purchasedcourse=$this->user_model->get_purchased_couse($value);
		  foreach ($purchasedcourse as $key => $val) {
			if($val->parent_id==0){
			  array_push($courseParrentArray,$val->course_id);
			}
			else{
			  $parrentArray = explode(',',$val->parent_id);
			  for($p=0;$p<count($parrentArray);$p++){
				array_push($courseParrentArray, $parrentArray[$p]); 
			  }
			}
		  }
		}
		sort($courseParrentArray);
		foreach ($courseParrentArray as $key => $value) {
		  $course_basename=$this->user_model->get_coursebasename($value);
		  foreach ($course_basename as $key => $val) {
			if(!in_array($val->course_name, $courseBaseNameArray)){
			  $courseBaseNameArray[$val->course_id]=$val->course_name;        		
			}
		  }
		  foreach ($courseBaseNameArray as $key => $value) {
			if(!in_array($value, $basename)){
			  array_push($basename, $value);
			  array_push($coursebaseid, $key);
			}
		  }
		}
		/*echo "<pre>";
		print_r($coursebaseid);*/
		foreach ($coursebaseid as $key => $value) {		
		  $course_caption=$this->user_model->get_coursebasename($value);		
		  foreach ($course_caption as $key => $val) {
			array_push($coursecaptionArray, $val->course_summary);
		  }
		}
		$content['country']=$this->user_model->get_country();
		$stud_details=$this->user_model->get_stud_details($stud_id);
		foreach($stud_details as $row){
		  $content['fname'] =$row->first_name;
		  if(($row->dob)!=''){
			$content['dob'] = explode('-',$row->dob);
		  }
		  else $content['dob']=NULL;
		  $content['email'] = $row->email;
		  $content['contact_no'] = $row->contact_number	;
		  $content['country_set'] = $row->country_id ;
		}
		
		for($i=0;$i<count($courses_student);$i++){
		  $pagevisited[$courses_student[$i]]=$course_pagevisited[$i];
		}
		 
		foreach ($courses as $key => $value) {
		  $courid=$value->course_id;
		  $courseUnitArray= $this->user_model->getCourseUnitListing($courid,$stud_id); 
		  if(!empty($courseUnitArray)) {
			$unitSlno            = 0;     
			$completedMarks1     = 0;
			$completedMarks2     = 0; 
			$countCompleted      = 0;
			$countTotal          = 0; 
			$completedPercentage = 0;   
			foreach($courseUnitArray as $key=> $courseUnitArr) { 
			  $percentage    = 0;
			  //$unitId        = $courseUnitArr->course_units_idcourse_units;
			  $unitId        = $courseUnitArr['course_units_idcourse_units'];
			  //whether the unit is completed or not by checking the pages in the unit
			  $unitComplete  = $this->user_model->getUnitCompleteByUser_unit($stud_id,$unitId,$courid); 
			  //  total tasks in the unit
			  $taskArray     = $this->user_model->getTasksInUnit($unitId);
			  $totalTask     = count($taskArray);
			  //  tasks in the unit which is attended by user
			  $userTaskArray = $this->user_model->getTasksForUserInUnit($stud_id,$unitId,$courid); 
			 
			  $totalTaskUser = count($userTaskArray);
			  //the marks obtained by user in a particular unit in a course
			  $marksDetails  = $this->user_model->getUnitMarksForTasks($stud_id,$unitId,$courid);                
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
		  $coursePercentage1=@($completedMarks1/$completedMarks2)*100;
		  $coursePercentage[$courid]=@round($coursePercentage1,2);
		  ////
		  $unitsIdArr = $this->user_model->getCourseUnitListing($courid,$stud_id,1); 
		  $totalUnit=count($unitsIdArr);
		  $valueArr=array();         
		  for ($unitCnt=0;$unitCnt<count($unitsIdArr);$unitCnt++) {
			$unitDetailsArr[] = $this->user_model->get_courseunits($unitsIdArr[$unitCnt]);
			$pageIdsArr = array();
			$pageIdsArr = $this->user_model->getPageIdsForUnits($unitsIdArr[$unitCnt]);
			$studentPageIdsArr = $this->user_model->getStudentProgressPageIds($stud_id,$unitsIdArr[$unitCnt],$courid);
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
		  
		  $userCoursesArr=$this->user_model->getcourses_student_expiry($stud_id,$courid);
		  $courseNameArr = $this->user_model->get_coursebasename($courid);
		  $content['coursename']=$courseNameArr[0]->course_name;
		 
		  if(!empty($userCoursesArr)){
			$courseDetails1 = $this->user_model->getstudent_courseaccess($stud_id,$courid); 
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
			  $numberOfDaysRemaining[$courid] =  ceil($datediff/(60*60*24)); 
			}
			else
			{
			  $your_date = strtotime($accessdate_exp);
			  $datediff = $your_date - $now;
			  $numberOfDaysRemaining[$courid] =  ceil($datediff/(60*60*24));
			}
			if($numberOfDaysRemaining[$courid] < 0)
			{
			  $numberOfDaysRemaining[$courid] = 0;
			}
		  }
		}
		/*$total=array_sum($valueArr); 
		$progressPercnt; round($total/$totalUnit);*/
		$total=array_sum($valueArr); 
		$totalpageattended=round($total/$totalUnit); 
		$x=0;
		$y=count($valueArr);
		foreach($valueArr as $val){
			$x=$x+$val;
		}
		$progressPercnt=$x/$y;
		//return $totalpageattended;
		
		$content['coursePercentage']= $coursePercentage;
		$content['progressPercnt']= $progressPercnt;
		$content['daysremaining']= $numberOfDaysRemaining;
		$content['stud_id']=$stud_id;
		$content['basename']=$basename;
		$content['pagevisited']=$pagevisited;
		$content['courses_student']=$courses_student;
		$content['course_pagevisited']=$course_pagevisited;
		$content['coursebaseid']=$coursebaseid;
		$content['courseBaseNameArray']=$courseBaseNameArray;
		$content['coursecaptionArray']=$coursecaptionArray;
		$content['language']=$this->language;
		return $content;
   
	}
  function expireduser($course_id){
   
    $user_id=$this->session->userdata['student_logged_in']['id'];	
	$course_expiry_details = $this->user_model->getcourses_student_expiry($user_id,$course_id);	    
	$today = date("Y-m-d",time());
	foreach($course_expiry_details as $course_details)
	{
		if($course_details->date_expiry <$today)
		{
			$data=array("expired"=>'1'); // change status to Expired
			$this->user_model->update_student_enrollments($course_id,$user_id,$data);
		}	
	}  
   redirect('coursemanager/campus', 'refresh');
  }
  function studentcourse($cour_id){
    if(!$this->session->userdata('student_logged_in')){
      redirect('home');
    }
    //$content=$this->get_student_progress($cour_id);
	
	$user_id  = $this->session->userdata['student_logged_in']['id'];	
    //$content=$this->get_student_progress($cour_id);
	//******************************************************* hard copy package ****************************
		$product_pruchased_package = array();
		$product_pruchased_package['hardcopy'] = false;
		$product_pruchased_package['poe_hard'] = false;
		$product_pruchased_package['poe']= false;
		$product_pruchased_package['proof_completion_hard']= false;
		$product_pruchased_package['proof_completion']= false;
		$product_pruchased_package['transcript'] = false;
		$product_pruchased_package['transcript_hard']= false;		
		
		$package_subscription_details = $this->package_model->get_package_sucbcriptions_user($user_id,$cour_id);	
		 
	  	if(!empty($package_subscription_details))
		{
			$packgae_sub_id = $package_subscription_details[0]->id;		  
			$product_pruchased_package['hardcopy'] = $this->package_model->get_package_puchases_by_product_type('hardcopy',$user_id,$cour_id,$packgae_sub_id);			
			$product_pruchased_package['poe_hard'] = $this->package_model->get_package_puchases_by_product_type('poe_hard',$user_id,$cour_id,$packgae_sub_id);			
			$product_pruchased_package['poe'] = $this->package_model->get_package_puchases_by_product_type('poe',$user_id,$cour_id,$packgae_sub_id);		
			$product_pruchased_package['proof_completion_hard'] = $this->package_model->get_package_puchases_by_product_type('proof_completion_hard',$user_id,$cour_id,$packgae_sub_id);			
			$product_pruchased_package['proof_completion'] = $this->package_model->get_package_puchases_by_product_type('proof_completion',$user_id,$cour_id,$packgae_sub_id);			
			$product_pruchased_package['transcript_hard'] = $this->package_model->get_package_puchases_by_product_type('transcript_hard',$user_id,$cour_id,$packgae_sub_id);			
			$product_pruchased_package['transcript'] = $this->package_model->get_package_puchases_by_product_type('transcript',$user_id,$cour_id,$packgae_sub_id);			
					
		}
	//******************************************************* hard copy package end session****************************
    $course_id_user=array();
    $studentUserId  = $this->session->userdata['student_logged_in']['id'];
	
	$this->tr_common['tr_course_progress'] = $this->user_model->translate_('course_progress');
    $this->tr_common['tr_more_info'] =$this->user_model->translate_('more_info');
	$this->tr_common['tr_my_marks'] =$this->user_model->translate_('my_marks_new');
	$this->tr_common['tr_study_time_remaining'] =$this->user_model->translate_('study_remaining');
	$this->tr_common['tr_days'] =$this->user_model->translate_('camp_days');
	$this->tr_common['tr_study'] =$this->user_model->translate_('Study');
	$this->tr_common['tr_exercise'] =$this->user_model->translate_('Exercices');
	$this->tr_common['tr_exam'] =$this->user_model->translate_('Exams');
	
	$studentEnrollDetails = $this->user_model->check_user_registered($studentUserId,$cour_id);
	if(empty($studentEnrollDetails))
	{
		redirect('home/buy_another_course/stud_id/'.$studentUserId.'/cour_id/'.$cour_id);
	}
	$student_course_status = $studentEnrollDetails[0]->course_status;
	$content['expired'] = $studentEnrollDetails[0]->expired;
	$content['student_course_status'] = $student_course_status;
	$resume_det = $this->user_model->get_student_resume_link($studentUserId,$cour_id);
	
	if(!empty($resume_det)){
		$content['resume_link'] = base_url().$resume_det[0]->resume_link;
	}
	else{
		$content['resume_link'] = "";
	}
	//******************************************************* hard copy package ****************************
	        if($student_course_status == 0) 			// not started
			{ 
				$certificate_status = 'not_started'; 
		 	}
			else if($student_course_status == 1) 	// studying
			{
				$mark_details = $this->get_student_progress($cour_id); 
				if($mark_details['progressPercnt']==100 && $mark_details['coursePercentage']>=55 && $mark_details['course_passed']==1)
				{
					$certificate_status = 'passed'; 					
				}
				else
				{
					$certificate_status = 'not_passed'; 
				}
			}
			else if($student_course_status == 2) 	// completed
			{ 
				$mark_details = $this->get_student_progress($cour_id); 
				if($mark_details['progressPercnt']==100 && $mark_details['coursePercentage']>=55 && $mark_details['course_passed']==1)
				{
					$certificate_status = 'passed'; 
				}
				else
				{
					$certificate_status = 'not_passed'; 
				}
			}
			else if($student_course_status == 3) 	// certificate applied
			{ 
				$certificate_status = 'pending_approval'; 
				
			}
			else if($student_course_status == 4) 	// certificate isseued
			{ 
				$mark_details = $this->get_student_progress($cour_id); 
				$certificate_status = 'issued';
				
				$pre_purchased_icoes_certificate = $this->user_model->get_pre_purchased_product_status($user_id,$cour_id);
				if(!empty($pre_purchased_icoes_certificate))
				{
				$hard_copy_applied_status = explode(',',$pre_purchased_icoes_certificate[0]->offer_extra_products);
				
				$hard_copy_prepaid_offer_id= $pre_purchased_icoes_certificate[0]->offer_id;
				/*echo "<pre>";
				print_r($pre_purchased_icoes_certificate);
				
				echo "<pre>";
				print_r($hard_copy_applied_status);*/
				
				
				if(in_array('20',$hard_copy_applied_status))
				{
					$hard_copy_prepaid = 1;
				}
				}
				/*if($mark_details['progressPercnt']==100 && $mark_details['coursePercentage']>=55)
				{
					$certificate_status = 'issued';
				}
				else
				{
					$certificate_status = 'not_passed'; 
				}*/
				 		 		
			}
			else if($student_course_status == 5) 	// material access
			{ 
				//$certificate_status = 'material_access'; 
				$mark_details = $this->get_student_progress($cour_id); 
				if($mark_details['progressPercnt']==100 && $mark_details['coursePercentage']>=55)
				{
					$certificate_status = 'passed'; 
				}
				else
				{
					$certificate_status = 'not_passed'; 
				}
				
				
			}
			else if($student_course_status == 6) 	// archieved
			{ 
				//$certificate_status = 'archieved'; 	
				
				$mark_details = $this->get_student_progress($cour_id); 
				if($mark_details['progressPercnt']==100 && $mark_details['coursePercentage']>=55)
				{
					$certificate_status = 'passed'; 
				}
				else
				{
					$certificate_status = 'not_passed'; 
				}	 		
			}
			else if($student_course_status == 7) 	// expired
			{ 
				
				//$certificate_status = 'expired'; 	
				$mark_details = $this->get_student_progress($cour_id); 
				if($mark_details['progressPercnt']==100 && $mark_details['coursePercentage']>=55)
				{
					$certificate_status = 'passed'; 
				}
				else
				{
					$certificate_status = 'not_passed'; 
				}	 		
			}
			//******************************************************* hard copy package end session ****************************
    $courseStud=$this->user_model->get_course_stud($studentUserId);
    foreach ($courseStud as $value) {
      foreach ($value as $val) {
        array_push($course_id_user, $val);
      }
    }
    
    $data['cour_details']=$this->user_model->get_coursename($cour_id);
    $courseUnitsArr= $this->user_model->get_units_forStudent($studentUserId,$cour_id);
	
 // echo "<pre>" ; print_r($courseUnitsArr); exit;
  $keys = array_keys($courseUnitsArr);
  //print_r($keys);exit;
 //echo "<pre>ccouuuuuuuuuuunt".count($courseUnitsArr);print_r($courseUnitsArr); 
    for($i=0;$i<count($courseUnitsArr);$i++) {
		
		//echo $courseUnitsArr[$keys[$i]]."<br>";
		
      $courseUnitsArray= $this->user_model->get_courseunits($courseUnitsArr[$keys[$i]]);
	 	//print_r($courseUnitsArray); exit;
      foreach ($courseUnitsArray as $key1 => $value1) {
		  
         $courseunits[$value1->id]=$value1->unit_name;
		 
		 $coursePages_arr = 
		 $studyP =  $this->user_model->getcourse_sections($value1->id,'0');
		 $exerciseP = $this->user_model->getcourse_sections($value1->id,'1');
		 $examP  =$this->user_model->getcourse_sections($value1->id,'2');
		  
		  if(!empty($studyP))
		  $data['tr_study'][$value1->id] = $this->user_model->translate_('Study');
		 
		  if(!empty($exerciseP))
		$data['tr_exercise'][$value1->id] = $this->user_model->translate_('Exercices');
		 
		  if(!empty($examP))
		$data['tr_exam'][$value1->id] = $this->user_model->translate_('Exams');
		 
		// $data['tr_study'] = $study;
		 //$data['tr_exercise'] = $exercise;
		 //$data['tr_sexam'] = $exam;
		 //echo "<pre>";print_r($data);echo "</pre>";
		 
      }
    }
	
	//btest
	//exit; 
	$data['product_pruchased_package'] = $product_pruchased_package;
	$data['certificate_status'] = $certificate_status;
	//$data['hard_copy_prepaid']  = $hard_copy_prepaid;
	$data['student_course_status'] = $student_course_status;
	$course_name = $this->common_model->get_course_name($cour_id); 
	//$data['sample_course']=$this->user_model->check_sample_course_or_not($studentUserId,$cour_id);
	$data['sample_button']="yes";
    $data['coursename']= $course_name;
    $data['cour_id']=$cour_id;
	//echo "<pre>"; print_r($courseunits); exit;
    $data['courseunits']=$courseunits;
	$content['enrolled_courses_without_sample'] = $this->user_model->get_courses_student($studentUserId); 
	$data['translate'] = $this->tr_common;
    $data['view'] = 'course_modules';
	$course_progress_array = $this->get_student_progress($cour_id);
	$data['course_progress_array']  = $course_progress_array;
	$data['tr_my_course'] =$this->user_model->translate_('my_course');
	$data['translate'] = $this->tr_common;
    $data['content'] = $content;
    $this->load->view('user/template_inner',$data);
  }
  
   function studentcourse_test($cour_id){
	   
    if(!$this->session->userdata('student_logged_in')){
      redirect('home');
    }
    //$content=$this->get_student_progress($cour_id);
	
	$content['for_popup']=$this->get_student_deatils_for_popup();
	//echo "<pre>";print_r($content['for_popup']);exit;
    $course_id_user=array();
    $studentUserId  = $this->session->userdata['student_logged_in']['id'];
	
	$this->tr_common['tr_course_progress'] = $this->user_model->translate_('course_progress');
    $this->tr_common['tr_more_info'] =$this->user_model->translate_('more_info');
	$this->tr_common['tr_my_marks'] =$this->user_model->translate_('my_marks_new');
	$this->tr_common['tr_study_time_remaining'] =$this->user_model->translate_('study_remaining');
	$this->tr_common['tr_days'] =$this->user_model->translate_('camp_days'); 	
    $this->tr_common['tr_more_info'] =$this->user_model->translate_('more_info');

	 $this->tr_common['tr_days'] =$this->user_model->translate_('camp_days');
	
	$this->tr_common['tr_study'] =$this->user_model->translate_('Study');
	$this->tr_common['tr_exercise'] =$this->user_model->translate_('Exercices');
	$this->tr_common['tr_exam'] =$this->user_model->translate_('Exams');
	
		
	$lang_id = $this->session->userdata('language');
	//$enrolled_courses = $this->user_model->get_courses_student($user_id); 
	$studentEnrollDetails = $this->user_model->check_user_registered($studentUserId,$cour_id);
	
	if(empty($studentEnrollDetails))
	{
		redirect('home/buy_another_course/stud_id/'.$studentUserId.'/cour_id/'.$cour_id);
	}
	$student_course_status = $this->user_model->get_student_course_status($cour_id,$studentUserId);
	if($student_course_status==0)
	{
		/*if(isset($_POST['activate_btn']))
		{
			if($this->input->post("activate_btn")=="OK")
			{*/
		$dateNow=date('Y-m-d');
		$user_voucher = $this->user_model->getVoucher_user_course($studentUserId,$cour_id);
		if($user_voucher!=false)
		{
			$expirityDate = $this->user_model->findExpirityDate($cour_id,$dateNow,$user_voucher);
		}
		else
		{
			$expirityDate = $this->user_model->findExpirityDate($cour_id,$dateNow);
		}
		
		$extension_prepaid_id = false;
	   $package_subscription_details = $this->package_model->get_package_sucbcriptions_user($studentUserId,$cour_id);		
	   if(!empty($package_subscription_details))
	   {
		$package_sub_id = $package_subscription_details[0]->id;
		$payment_id = $package_subscription_details[0]->payment_id;
		$extension_prepaid_id = $this->package_model->get_package_puchases_by_product_type('extension',$studentUserId,$cour_id,$package_sub_id);
			if($extension_prepaid_id)
			{
				$extension_id = $this->user_model->get_extension_id($extension_prepaid_id);					
				$extension_period = $this->user_model->get_extension_details($extension_id);
				foreach($extension_period as $key =>$row)
				{
					$period = $row->extension_days;
				}
				
				/* If Included add extension period to the validity of added course */
				
				 $expirityDate=date('Y-m-d', strtotime($expirityDate. ' + '.$period.' days'));	
				 				 
				
			}
			
		
		}
		
		
		$data=array(
		"course_status"=>'1',// change status to Studying
		"date_expiry"=>$expirityDate,
		"start_date"=>date("Y-m-d")); //setting up course expirity date
		$this->user_model->update_student_enrollments($cour_id,$studentUserId,$data);
		if($extension_prepaid_id)
		{
			$pak_sub_array = array('status'=>0);
			$this->package_model->update_package_subscription_details($studentUserId,$cour_id,$package_sub_id,'extension',$pak_sub_array);
			
			$insert_data=array("user_id"=>$studentUserId,"course_id"=>$cour_id,"type"=>'extension',"date_applied"=>$dateNow,"product_id"=>$extension_prepaid_id,"payment_id"=>$payment_id);	 
				$this->user_model->insertQuerys("user_subscriptions",$insert_data);
				
		}		
		

	}
	$i=0;
	 $array_index_marks=0;
     $enrolled=$this->user_model->check_user_registered($studentUserId,$cour_id); // check user registered with this course 
		//echo "<pre>";print_r($enrolled);
				if($enrolled) // if enrollled
		{
			foreach ($enrolled as $value)
			{
				
				$course_progress_array[$array_index_marks] = $this->get_student_progress($value->course_id);
				$course_progress_array[$array_index_marks]['course_status_id']=$value->course_status;
	            if($value->course_status==7) // course expired
				{
					
					$course_array[$i]['course_status'] = $this->user_model->translate_('expired');
					$course_array[$i]['course_status_id'] =$value->course_status;
					if($course_progress_array[$array_index_marks]['progressPercnt'] == 100)
					{
						$this->session->set_flashdata('complete_expired',"Your course has expired however you can buy a course subscription");
						
					}
					else if($course_progress_array[$array_index_marks]['progressPercnt'] < 100)
					{
						$this->session->set_flashdata('notcomplete_expired',"your course has expired....");
						
					}
			}
		
		$array_index_marks++;				
				$i++;
	}}
	
	
	
	
    $courseStud=$this->user_model->get_course_stud($studentUserId);
    foreach ($courseStud as $value) {
      foreach ($value as $val) {
        array_push($course_id_user, $val);
      }
    }
    
    $cour_details=$this->user_model->get_coursename($cour_id);
    $courseUnitsArr= $this->user_model->get_units_forStudent($studentUserId,$cour_id);
	
	 $courseunits_id= $this->user_model->get_courseunits_unitid($cour_id);
	 
  //echo "<pre>"; print_r($courseunits_id); exit;
    for($i=0;$i<count($courseunits_id);$i++)
		{
		
			foreach($courseunits_id as $unit_id)
				{
				$courseUnitsArray= $this->user_model->get_courseunits_byorder($unit_id->course_units);
				//echo "<pre>"; print_r($courseUnitsArray); exit;
				foreach ($courseUnitsArray as $key1 => $value1) {
				
				$courseunits[$value1->id]=$value1->unit_name;
				echo $value1->id; 
				$coursePages_arr = 
				$studyP =  $this->user_model->getcourse_sections($value1->id,'0');
				echo "<pre>"; print_r($studyP); exit;
				$exerciseP = $this->user_model->getcourse_sections($value1->id,'1');
				$examP  =$this->user_model->getcourse_sections($value1->id,'2');
				
				if(!empty($studyP))
				$data['tr_study'][$value1->id] = $this->user_model->translate_('Study');
				
				if(!empty($exerciseP))
				$data['tr_exercise'][$value1->id] = $this->user_model->translate_('Exercices');
				
				if(!empty($examP))
				$data['tr_exam'][$value1->id] = $this->user_model->translate_('Exams');
				
				}
				
				}
		}
	
	$course_name = $this->common_model->get_course_name_and_summary($cour_id); 
	
    $data['coursename']= $course_name['course_name'];
    $data['cour_id']=$cour_id;
    $data['courseunits']=$courseunits;
	
    $data['lang_id']				 = $lang_id;	
    $data['course_progress_array']  = $course_progress_array;
	$data['translate'] = $this->tr_common;
    $data['view'] = 'course_modules_old';
	
	$content = $this->get_student_deatils_for_popup();
	$data['tr_my_course'] =$this->user_model->translate_('my_course');
	$data['translate'] = $this->tr_common;
    $data['content'] = $content;
    $this->load->view('user/course_page_template',$data);
  
	   }
  
  function extendcourse_old(){
   // $content=$this->coursedetails();
 // echo "<pre>";
 	$content=$this->get_student_deatils_for_popup();
 	$course_name_array   = array();
	$price_details_array = array();
	
	$this->tr_common['tr_extend_course']   =$this->user_model->translate_('extend_course');	
	$this->tr_common['tr_extend_txt']   =$this->user_model->translate_('extend_txt');	
	 $this->tr_common['tr_return_to']   =$this->user_model->translate_('return_to');
	 $this->tr_common['tr_campus']   =$this->user_model->translate_('campus');
	 $this->tr_common['tr_sign']   =$this->user_model->translate_('sign');
	 $this->tr_common['tr_Out']   =$this->user_model->translate_('Out');
	$this->tr_common['tr_next']   =$this->user_model->translate_('_next');
	$this->tr_common['tr_course_progress'] = $this->user_model->translate_('course_progress');
	$this->tr_common['tr_course_progress'] = $this->user_model->translate_('course_progress');
    $this->tr_common['tr_more_info'] =$this->user_model->translate_('more_info');
	$this->tr_common['tr_my_marks'] =$this->user_model->translate_('my_marks_new');
	$this->tr_common['tr_study_time_remaining'] =$this->user_model->translate_('study_remaining');
	$this->tr_common['tr_days'] =$this->user_model->translate_('camp_days'); 	 	
	
	$stud_id = $this->session->userdata['student_logged_in']['id'];
	$lang_id = $this->session->userdata('language');
	//$enrolled_courses = $this->user_model->get_courses_student($user_id); 
	$content['studentDetails']=$this->user_model->get_student_details($stud_id);
	$base_courses = $this->user_model->get_courses($lang_id); 
	
	 $i=0;
	 $array_index_marks=0;
	 foreach ($base_courses as $row) {
		 $course_array[$i]['course_id'] = $row->course_id;
	 	$course_array[$i]['course_name'] = $row->course_name;
		$course_array[$i]['course_status'] = $row->course_status;
	//	$course_array[$i]['image'] = $row->home_image;
		$course_array[$i]['image'] = 'event.jpg';
	if($i==0)
		{
     $enrolled=$this->user_model->check_user_registered($stud_id,$row->course_id); // check user registered with this course 
		}
		
		//  $enrolled[$i]['enrolled']=$this->user_model->check_user_registered($stud_id,$row->course_id);
		  
				if($enrolled) // if enrollled
		{
			foreach ($enrolled as $value)
			{
				$course_progress_array[$array_index_marks] = $this->get_student_progress($value->course_id);
				$course_progress_array[$array_index_marks]['course_status_id']=$value->course_status;
	            if($value->course_status==7) // course expired
				{
					
					$course_array[$i]['course_status'] = $this->user_model->translate_('expired');
					$course_array[$i]['course_status_id'] =$value->course_status;
					if($course_progress_array[$array_index_marks]['progressPercnt'] == 100)
					{
						$this->session->set_flashdata('complete_expired',"Your course has expired however you can buy a course subscription");
						$course_array[$i]['resume_link'] = 'sales/course_access_option/'.$value->course_id;
					}
					else if($course_progress_array[$array_index_marks]['progressPercnt'] < 100)
					{
						$this->session->set_flashdata('notcomplete_expired',"your course has expired....");
						$course_array[$i]['resume_link'] = 'coursemanager/extendcourse';
					}
			}
		
		$array_index_marks++;				
				$i++;
	}}}
	//echo "<pre>";print_r($course_array);exit;
	$extention_option = $this->user_model->getTableValues('extension_options');
	$j = 0;
	
	
	foreach($extention_option as $key =>$row)
	{	
		if($lang_id ==4)
		{	
			$extention_details[$j]['extention_option'] = $row->extension_option;	
			$extention_details[$j]['extention_days'] = $row->extension_days;	
		}
		else
		{
			$extention_details[$j]['extention_option'] = $row->extension_option_spanish;
			$extention_details[$j]['extention_days'] = $row->extension_days;	
		}
		$product_id = $this->common_model->getProdectId('extension',$row->id,1);	
		//echo "<br> Product id ".$product_id." Currcode ".$this->currId;
			
		$price_details_array[$j] = $this->common_model->getProductFee($product_id,$this->currId);
		$extention_details[$j]['product_id'] = $product_id;		
		$j++;
	}	
//echo "<pre>";print_r($enrolled);exit;
	 $content['extention_details']    = $extention_details;
	 $content['price_details_array'] = $price_details_array;
	 $content['Enrolled']    =    $enrolled;
	 $content['course_name_array']   = $course_name_array;	
	 $content['stud_id']			 = $stud_id;
		

	$content['curr_id']= $curr_id= $this->currId;
	$content['currency_code'] = $this->currencyCode;
	$symbol = $this->user_model->get_curr_symbol($content['currency_code']);
	$content['currency_symbol'] =$this->currencyCode;
	
	
 
    $content['userDetails']=$this->user_model->get_stud_details($stud_id);
 
    $data['lang_id']				 = $lang_id;
	$data['base_courses'] 			= $base_courses;
	$data['course_array'] 			= $course_array;	
    $data['course_progress_array']  = $course_progress_array;
	
    $data['translate'] = $this->tr_common;
	  
    $data['view'] = 'extend_courseList';
	
	$data['content'] = $content;
    $this->load->view('user/header_full_template',$data);  
	
  }
  
  function extendcourse($course_id){
   // $content=$this->coursedetails();
 // echo "<pre>";
  $content=$this->get_student_deatils_for_popup();
  $course_name_array   = array();
 $price_details_array = array();
 $sess_course_id_array = array('course_id' =>$course_id); 
 $this->session->set_userdata($sess_course_id_array);
 $stud_id = $this->session->userdata['student_logged_in']['id'];
 $lang_id = $this->session->userdata('language');
 $course_details=$this->user_model->get_coursename($course_id);
 foreach($course_details as $row_course){
  $content['course_name']=$row_course->course_name;
  $content['course_image']=$row_course->campus_image;
 }
 $content['studentDetails']=$this->user_model->get_student_details($stud_id);
 
 //$content['base_course']=$base_courses=$this->user_model->get_courses_by_order_for_home($this->language);
  $i=0;
  $array_index_marks=0;
  
 $extention_option = $this->user_model->getTableValues('extension_options');
 $j = 0;
 
 
 foreach($extention_option as $key =>$row)
 { 
  if($lang_id ==4)
  { 
   $extention_details[$j]['extention_option'] = $row->extension_option; 
   $extention_details[$j]['extention_days'] = $row->extension_days;
   $extention_details[$j]['id'] = $row->id; 
  }
  else
  {
   $extention_details[$j]['extention_option'] = $row->extension_option_spanish;
   $extention_details[$j]['extention_days'] = $row->extension_days; 
   $extention_details[$j]['id'] = $row->id; 
  }
  $product_id = $this->common_model->getProdectId('extension',$row->id,1); 
  //echo "<br> Product id ".$product_id." Currcode ".$this->currId;
   
  $price_details_array[$j] = $this->common_model->getProductFee($product_id,$this->currId);
  $extention_details[$j]['product_id'] = $product_id;  
  $j++;
 } 
//echo "<pre>";print_r($enrolled);exit;
  $content['extention_details']    = $extention_details;
  $content['price_details_array'] = $price_details_array;
  //$content['Enrolled']    =    $enrolled;
  //$content['course_name_array']   = $course_name_array; 
  $content['stud_id']    = $stud_id;
  $content['course_id']     = $course_id;

 $content['curr_id']= $curr_id= $this->currId;
 $content['currency_code'] = $this->currencyCode;
 $symbol = $this->user_model->get_curr_symbol($content['currency_code']);
 $content['currency_symbol'] =$this->currencyCode;
 
 
 
    $content['userDetails']=$this->user_model->get_stud_details($stud_id);
     
    $data['lang_id']     = $lang_id;
 //$data['base_courses']    = $base_courses;
 //$data['course_array']    = $course_array; 
    //$data['course_progress_array']  = $course_progress_array;
 
    $data['translate'] = $this->tr_common;
   
    $data['view'] = 'extend_course';
 
 $data['content'] = $content;
    $this->load->view('user/template_inner',$data);  
 
  }
  
    function extendcourse2($course_id){
   // $content=$this->coursedetails();
 // echo "<pre>";
 	$content=$this->get_student_deatils_for_popup();
 	$course_name_array   = array();
	$price_details_array = array();
	
	$this->tr_common['tr_extend_course']   =$this->user_model->translate_('extend_course');	
	$this->tr_common['tr_extend_txt']   =$this->user_model->translate_('extend_txt');	
	 $this->tr_common['tr_return_to']   =$this->user_model->translate_('return_to');
	 $this->tr_common['tr_campus']   =$this->user_model->translate_('campus');
	 $this->tr_common['tr_sign']   =$this->user_model->translate_('sign');
	 $this->tr_common['tr_Out']   =$this->user_model->translate_('Out');
	$this->tr_common['tr_next']   =$this->user_model->translate_('_next');
	$this->tr_common['tr_course_progress'] = $this->user_model->translate_('course_progress');
	$this->tr_common['tr_course_progress'] = $this->user_model->translate_('course_progress');
    $this->tr_common['tr_more_info'] =$this->user_model->translate_('more_info');
	$this->tr_common['tr_my_marks'] =$this->user_model->translate_('my_marks_new');
	$this->tr_common['tr_study_time_remaining'] =$this->user_model->translate_('study_remaining');
	$this->tr_common['tr_days'] =$this->user_model->translate_('camp_days'); 	 	
	
	$stud_id = $this->session->userdata['student_logged_in']['id'];
	$lang_id = $this->session->userdata('language');
	//$enrolled_courses = $this->user_model->get_courses_student($user_id); 
	$content['studentDetails']=$this->user_model->get_student_details($stud_id);
	$base_courses = $this->user_model->get_courses($lang_id); 
	 $i=0;
	 $array_index_marks=0;
	 //echo "<pre>";print_r($base_courses);exit;
	 foreach ($base_courses as $row) {
		 $course_array[$i]['course_id'] = $row->course_id;
	 	$course_array[$i]['course_name'] = $row->course_name;
		$course_array[$i]['course_summary'] = $row->course_summary;
	if($i==0)
		{
     $enrolled=$this->user_model->check_user_registered($stud_id,$row->course_id); // check user registered with this course 
		}
		
		//  $enrolled[$i]['enrolled']=$this->user_model->check_user_registered($stud_id,$row->course_id);
		  
				if($enrolled) // if enrollled
		{
			foreach ($enrolled as $value)
			{
				$course_progress_array[$array_index_marks] = $this->get_student_progress($value->course_id);
				$course_progress_array[$array_index_marks]['course_status_id']=$value->course_status;
	            if($value->course_status==7) // course expired
				{
					
					$course_array[$i]['course_status'] = $this->user_model->translate_('expired');
					$course_array[$i]['course_status_id'] =$value->course_status;
					if($course_progress_array[$array_index_marks]['progressPercnt'] == 100)
					{
						$this->session->set_flashdata('complete_expired',"Your course has expired however you can buy a course subscription");
						$course_array[$i]['resume_link'] = 'sales/course_access_option/'.$value->course_id;
					}
					else if($course_progress_array[$array_index_marks]['progressPercnt'] < 100)
					{
						$this->session->set_flashdata('notcomplete_expired',"your course has expired....");
						$course_array[$i]['resume_link'] = 'coursemanager/extendcourse';
					}
			}
		
		$array_index_marks++;				
				$i++;
	}}}
	$extention_option = $this->user_model->getTableValues('extension_options');
	$j = 0;
	
	
	foreach($extention_option as $key =>$row)
	{	
		if($lang_id ==4)
		{	
			$extention_details[$j]['extention_option'] = $row->extension_option;	
			$extention_details[$j]['extention_days'] = $row->extension_days;	
		}
		else
		{
			$extention_details[$j]['extention_option'] = $row->extension_option_spanish;
			$extention_details[$j]['extention_days'] = $row->extension_days;	
		}
		$product_id = $this->common_model->getProdectId('extension',$row->id,1);	
		//echo "<br> Product id ".$product_id." Currcode ".$this->currId;
			
		$price_details_array[$j] = $this->common_model->getProductFee($product_id,$this->currId);
		$extention_details[$j]['product_id'] = $product_id;		
		$j++;
	}	
//echo "<pre>";print_r($course_progress_array);exit;
	 $content['extention_details']    = $extention_details;
	 $content['price_details_array'] = $price_details_array;
	 $content['Enrolled']    =    $enrolled;
	 $content['course_name_array']   = $course_name_array;	
	 $content['stud_id']			 = $stud_id;
	 $content['course_id']				 = $course_id;
	 $this->load->model('course_model','',TRUE);
	 $content['extend_course_name']		= $this->course_model->extend_course_name($course_id);

	$content['curr_id']= $curr_id= $this->currId;
	$content['currency_code'] = $this->currencyCode;
	$symbol = $this->user_model->get_curr_symbol($content['currency_code']);
	$content['currency_symbol'] =$this->currencyCode;
	
	
 
    $content['userDetails']=$this->user_model->get_stud_details($stud_id);
     
    $data['lang_id']				 = $lang_id;
	$data['base_courses'] 			= $base_courses;
	$data['course_array'] 			= $course_array;	
    $data['course_progress_array']  = $course_progress_array;
	
    $data['translate'] = $this->tr_common;
	  
    $data['view'] = 'extend_course';
	
	$data['content'] = $content;
    //$this->load->view('user/header_full_template',$data);  
	$this->load->view('user/template_inner',$data); 
	
  }
  
  function extendpayment(){
    $content=$this->coursedetails();
    $content['extendpayment']=($_POST);
    $content['extensionDetails']=$this->user_model->getidextn($content['extendpayment']['extension']);
    $idoption =$content['extensionDetails'][0]->idOptions;
    $content['extensionOptions']=$this->user_model->getextnopt($idoption);
    $data['translate'] = $this->tr_common;
    $data['view'] = 'extension_select_payment';
    $data['content'] = $content;
    $this->load->view('user/course_template',$data);
  
  }
  
  function after_extend_pay()
  {
	    $user_id    = $this->uri->segment(3);
		$payment_id = $this->uri->segment(4);
		$course_id  = $this->uri->segment(5);
		$product_id = $this->uri->segment(6);
		
		$extension_id = $this->user_model->get_extension_id($product_id);
		
		$extension_period = $this->user_model->get_extension_details($extension_id);
		
		foreach($extension_period as $key =>$row)
		{
			$period = $row->extension_days;
		}
		$today = date("Y-m-d");
		// $accessdate=date("Y-m-d", strtotime("+$period days"));
		 $status = '1'; // studying
		 $expired = '0'; // studying
		
		 
		 $userCoursesArr=$this->user_model->getcourses_student_expiry($user_id,$course_id);
		
		 foreach($userCoursesArr as $det)
		 {
		 
		 	$cur_expiry_date = $det->date_expiry;
		 }
	    /* $cur_expiry_date = '2013-12-13';
		 echo "<br>Periodd  ".$period;
		 echo "<br>cur_expiry_date  ".$cur_expiry_date;
		 echo "<br>Today  ".$today;*/
		
		 
		 if($cur_expiry_date > $today)
		 {
			// echo "<br>Exp gt";
			 
			// date('Y-m-d', strtotime($dateNow. ' + '.$validityDays.' days'));
			 $accessdate=date('Y-m-d', strtotime($cur_expiry_date. ' + '.$period.' days'));
			/* echo "<br>accessdatee_cur  ".$accessdate;
			 exit;*/
		 }
		 else
		 {
			// echo "<br>Today gt";
			  $accessdate=date('Y-m-d', strtotime($today. ' + '.$period.' days'));
			// $accessdate=date("Y-m-d", strtotime("+$period days")); 
			 //echo "<br>accessdateeee  ".$accessdate;
		 }
		/* echo "<pre>";
		 print_r($userCoursesArr);*/
		 
		  
		// exit;
		/* echo "exit here";
		 exit;*/
		 
    $update_data=array("date_expiry"=>$accessdate,"course_status"=>$status,"expired"=>$expired);
	//echo "<pre>"; print_r($update_data); exit;
	 $this->user_model->update_student_enrollments($course_id,$user_id,$update_data);
	
	 
	 
	  $today = date("Y-m-d");
		 $insert_data=array("user_id"=>$user_id,"course_id"=>$course_id,"type"=>'extension',"date_applied"=>$today,"product_id"=>$product_id,"payment_id"=>$payment_id);
		  
		 $this->user_model->insertQuerys("user_subscriptions",$insert_data);
	
   // $this->user_model->insertQuerys("course_enrollments",$insert_data);
   $this->session->set_flashdata('extend_msg', 'Extended Succesfully, Course expires on :-'.$accessdate);
    redirect('coursemanager/studentcourse/'.$course_id, 'refresh');
		 
		 
  }
  
  
  function after_hardcopy_pay()
  {
	   // $user_id    = $this->uri->segment(3);
		$payment_id = $this->uri->segment(4);
		$course_id  = $this->uri->segment(5);
		$product_id = $this->uri->segment(6);
		$user_id=$this->session->userdata['student_logged_in']['id'];
		echo "<pre>"; print_r($payment_id); exit;	
		//user_subscriptions
		
		$certificate_details =  $this->certificate_model->get_certificate_details($user_id,$course_id);
		foreach($certificate_details as $key => $value)
		{
			$applied_date = $value->applied_on;	
			$certificate_id = $value->id;	
		}
		
		
		
		 $today = date("Y-m-d");
		 $insert_data=array("user_id"=>$user_id,"course_id"=>$course_id,"type"=>'hardcopy',"date_applied"=>$today,"product_id"=>$product_id,"payment_id"=>$payment_id);
		 
		 $this->user_model->insertQuerys("user_subscriptions",$insert_data);
		 
		 $insert_data_hardcopy =array("student_certificate_id	"=>$certificate_id,"user_id"=>$user_id,"course_id"=>$course_id,"hardcopy_apply_date"=>$today,"post_status"=>'pending');
		 
		 $this->user_model->insertQuerys("certificate_hardcopy_applications",$insert_data_hardcopy);
		 
		
		$this->load->helper(array('dompdf', 'file'));	   
  	 if(!$this->session->userdata('student_logged_in')){
		  redirect('home');
		}
	$lang_id = $this->session->userdata('language');
	$user_id=$this->session->userdata['student_logged_in']['id'];
		$this->load->model('certificate_model');
	 //$this->load->library('encrypt');
	// $course_id = $this->encrypt->decode($course_id_encrypted);
	// $course_id = $course_id_encrypted;
	 $course_name = $this->common_model->get_course_name($course_id);
	 
	 $user_details = $this->user_model->get_student_details($user_id); 
	 
	 
	
	 foreach($user_details as $key => $value)
	 {
	 	$certificate_user_name = $value->first_name.' '.$value->last_name;		
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
			$tomail = 'certificates@trendimi.com';
			//$tomail = 'ajithupnp@gmail.com';
					
					  $emailSubject = "Hard copy request : ".$user_mail;
					  $mailContent = "<p>Please find the attachment of hard copy certificate here with it. <p>";
					  
					  $mailContent .= "<p>User name  : ".$certificate_user_name."</p>";
					   $mailContent .= "<p>House Number :  ".$user_house_number."</p>";
					   $mailContent .= "<p>Address :  ".$user_address."</p>";					  
					   $mailContent .= "<p>City :  ".$user_city."</p>";
					   $mailContent .= "<p>Zip code :  ".$user_zip_code."</p>";
					   $mailContent .= "<p>Country : ".$user_country_name."</p>";
						   	
					  $this->email->from('info@trendimi.com', 'Team Trendimi');
					  $this->email->to($tomail); 
					  $this->email->attach($this->path);
					  
					  $this->email->subject($emailSubject);
					  $this->email->message($mailContent);	
					  
					 $this->email->send();
		}
	
	
	
	 redirect('coursemanager/success_hardcopy/'.$product_id, 'refresh');
		
		 
		 
  }
  
    function after_hardcopy_pay_new_payment()
  {
	  
	  
	   // $user_id    = $this->uri->segment(3);
		$payment_id = $this->uri->segment(4);
		$course_id  = $this->uri->segment(5);
		$product_id = $this->uri->segment(6);
		$user_id=$this->session->userdata['student_logged_in']['id'];
		
		//user_subscriptions
		
		if(!$this->session->userdata('student_logged_in')){
		  redirect('home');
		}		
		
		$certificate_details =  $this->certificate_model->get_certificate_details($user_id,$course_id);
		foreach($certificate_details as $key => $value)
		{
			$applied_date = $value->applied_on;	
			$certificate_id = $value->id;	
		}
		
		 $user_lang_id  = $this->common_model->get_user_lang_id($user_id);
		 
		 if($user_lang_id == 3)
			  {
				   setlocale(LC_TIME, 'es_ES');
			  }
			  else
			  {
				  setlocale(LC_TIME, 'en_EN');
			  }
		
		
		
		 $today = date("Y-m-d");
		 $insert_data=array("user_id"=>$user_id,"course_id"=>$course_id,"type"=>'hardcopy',"date_applied"=>$today,"product_id"=>$product_id,"payment_id"=>$payment_id);
		 
		 $this->user_model->insertQuerys("user_subscriptions",$insert_data);
		 
		
		 
		 
		 
		
	 	 $mark_details = $this->get_student_progress($course_id);
	 
	 
	 //progressPercnt
	/* 
	 echo "<pre>";
	 print_r($mark_details);
	 exit;*/
	 $grade='falied';
	 	if($mark_details['progressPercnt'] >= 55 && $mark_details['progressPercnt'] <= 64.99)
	 	{
	 		$grade = $this->user_model->translate_('mark_pass');
		}
		else if($mark_details['progressPercnt'] >= 65 && $mark_details['progressPercnt'] <= 74.99)
	 	{
	 		$grade = $this->user_model->translate_('mark_pass_plus');
		}
		else if($mark_details['progressPercnt'] >= 75 && $mark_details['progressPercnt'] <= 84.99)
	 	{
	 		$grade = $this->user_model->translate_('mark_merit');
		}
		else if($mark_details['progressPercnt'] >= 85 )
	 	{
	 		$grade = $this->user_model->translate_('mark_dist');
		}
		
		if($product_id == 20)
		{
			$postal_type='standard';
		}
		else if($product_id == 22)
		{
			$postal_type = 'express';
		}
			
			 $insert_data_hardcopy =array("student_certificate_id"=>$certificate_id,"user_id"=>$user_id,"course_id"=>$course_id,"hardcopy_apply_date"=>$today,"grade"=>$grade,"completion_date"=>$applied_date,"postal_type"=>$postal_type,"post_status"=>'pending');
	
	/*echo "<pre>";
	print_r($insert_data_hardcopy);*/
	
	$this->user_model->insertQuerys("certificate_hardcopy_applications",$insert_data_hardcopy);
	
		$sendemail = true;	
    	$stud_details=$this->user_model->get_stud_details($user_id);
		
		$course_name = $this->common_model->get_course_name($course_id); 	
		 
		  foreach($stud_details as $val2)
		  {
			  $certificate_user_name = $val2->first_name.'&nbsp;'.$val2->last_name;		
			 $user_country_name = $this->user_model->get_country_name($val2->country_id);
			 $user_house_number = $val2->house_number;
			 $user_address = $val2->address;
			 $user_city = $val2->city;
			 $user_zip_code = $val2->zipcode;
			 $user_mail = $val2->email;
			 
		  }
		
		
		
		if($product_id == 20)
		{
			$postal_type='Standard Posting';
		}
		else if($product_id == 22)
		{
			$postal_type = 'Express Posting';
		}
		
		
		 $postal_id = $this->certificate_model->get_postal_id($product_id); 
		 $postage_details = $this->certificate_model->get_postage_details($postal_id); 
	
	  foreach($postage_details as $row2)
	  {
		  $postal_name = $row2->postage_type;
	  }
	  
	  $content['postal_name'] = $postal_name;
	
	  foreach ($postage_details as $value) {	
						$postal_estimate_time=  str_replace("&#8226;","",$value->delivery_time);			

		}
		
		 $user_lang_id  = $this->common_model->get_user_lang_id($user_id);
		
		if($sendemail)
		{
			/*$this->load->library('email');
			
			
			$tomail = $user_mail;
			$tomail = 'ajithupnp@gmail.com';
					
					  $emailSubject = "Your hardcopy ICEOS accredited certificate";
					  $mailContent = "<p>You have succesfully purchased hardcopy. <p>";
					  $mailContent .= "<p>Regards. <p>";
					  $mailContent .= "<p>Trendimi team. <p>";					  
					  
					  $this->email->from('info@trendimi.net', 'Team Trendimi');
					  $this->email->to($tomail); 					
					  

					  $this->email->subject($emailSubject);
					  $this->email->message($mailContent);	
					  
					 $this->email->send();
					 */
					 
					 
		/*----------------------------------------------------*/
		
		 $this->load->library('email');
   		 $this->load->model('email_model');
    
   		 $row_new = $this->email_model->getTemplateById('iceoes_hardcopy',$user_lang_id);
    	foreach($row_new as $row1)
        {
       
		   $emailSubject = $row1->mail_subject;
		   $mailContent = $row1->mail_content;
        }
      $mailContent = str_replace ( "#firstname#",$stud_details[0]->first_name, $mailContent );
     
      $mailContent = str_replace ( "#courseName#",$course_name, $mailContent); 
     
      
      $mailContent = str_replace ( "#postal_option#", $postal_type, $mailContent );
      $mailContent = str_replace ( "#delivery_period#", $postal_estimate_time, $mailContent );
 
      

      $user_mail = 'bhagathindian@gmail.com';
   	//  $tomail = $studentdata['email'];
	 
	 	$this->email->from('info@trendimi.net', 'Team Trendimi');
       $this->email->to($user_mail); 
       $this->email->cc(''); 
       $this->email->bcc(''); 
       
       $this->email->subject($emailSubject);
       $this->email->message($mailContent); 
       
     	 $this->email->send();
     			 
			$this->email->clear(TRUE);
				
									
							
							//$to_mail = 'info@trendimi.net';
						/*	$to_mail = 'certificates@trendimi.com';
							$to_mail = 'ajithupnp@gmail.com';
							$to_mail = 'bhagathindian@gmail.com';
							
						  $emailSubject = "Hard copy request : ".$user_mail;
						  $mailContent = "<p>Please find the details of hard copy certificate requests. <p>";
						  
						   $mailContent .= "<p><strong>Certificate details</strong> </p>";
						  $mailContent .= "<p>Postal type   : ".$postal_type."</p>";
						  $mailContent .= "<p>Course  : ".$course_name."</p>";
						  $mailContent .= "<p>Grade  : ".$grade."</p>";
						 
						   $mailContent .= "<p><strong>User deatils </strong></p>";
						  $mailContent .= "<p>User name  : ".$certificate_user_name."</p>";
						  $mailContent .= "<p>House Number :  ".$user_house_number."</p>";
						  $mailContent .= "<p>Address :  ".$user_address."</p>";					  
						  $mailContent .= "<p>City :  ".$user_city."</p>";
						  $mailContent .= "<p>Zip code :  ".$user_zip_code."</p>";
						  $mailContent .= "<p>Country : ".$user_country_name."</p>";
								
						  $this->email->from('info@trendimi.com', 'Team Trendimi');
						  $this->email->to($to_mail); 
						//  $this->email->attach($this->path);
						  
						  $this->email->subject($emailSubject);
						  $this->email->message($mailContent);	
						  
						  $this->email->send();									  
						  $this->email->clear(TRUE);		*/	  
			
			
			
			
			
					
		}
//	exit;
	//echo "<br>mail send";	
	 redirect('coursemanager/success_hardcopy/'.$product_id, 'refresh');
		
		 
		 
  
  }
  
  
  function after_hard_pay_test()
  {
	  
	   // $user_id    = $this->uri->segment(3);
		echo "<br>payment_id = ".$payment_id = $this->uri->segment(4);
		echo "<br>course_id = ".$course_id  = $this->uri->segment(5);
		echo "<br>product_id = ".$product_id = $this->uri->segment(6);
		echo "<br>user_id = ".$user_id=$this->session->userdata['student_logged_in']['id'];
		
		//exit;//user_subscriptions
		
		$certificate_details =  $this->certificate_model->get_certificate_details($user_id,$course_id);
		foreach($certificate_details as $key => $value)
		{
			$applied_date = $value->applied_on;	
			$certificate_id = $value->id;	
		}
		
		
		
		 $today = date("Y-m-d");
		 $insert_data=array("user_id"=>$user_id,"course_id"=>$course_id,"type"=>'hardcopy',"date_applied"=>$today);
		 
		// $this->user_model->insertQuerys("user_subscriptions",$insert_data);---------------------------------------------------------------------(1)
		 
		 $insert_data_hardcopy =array("student_certificate_id	"=>$certificate_id,"user_id"=>$user_id,"course_id"=>$course_id,"hardcopy_apply_date"=>$today,"post_status"=>'pending');
		 
		 //$this->user_model->insertQuerys("certificate_hardcopy_applications",$insert_data_hardcopy);---------------------------------------------(2)
		 
		
		$this->load->helper(array('dompdf', 'file'));	   
  	 if(!$this->session->userdata('student_logged_in')){
		  redirect('home');
		}
	$lang_id = $this->session->userdata('language');
	$user_id=$this->session->userdata['student_logged_in']['id'];
		$this->load->model('certificate_model');
	 //$this->load->library('encrypt');
	// $course_id = $this->encrypt->decode($course_id_encrypted);
	// $course_id = $course_id_encrypted;
	 $course_name = $this->common_model->get_course_name($course_id);
	 
	 $user_details = $this->user_model->get_student_details($user_id); 
	 
	 
	
	 foreach($user_details as $key => $value)
	 {
	 	$certificate_user_name = $value->first_name.' '.$value->last_name;		
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
			//$tomail = 'info@trendimi.com';
			$tomail = 'deeputg1992@gmail.com';
			//$tomail = 'ajithupnp@gmail.com';
					
					  $emailSubject = "Hard copy request : ".$user_mail;
					  $mailContent = "<p>Please find the attachment of hard copy certificate here with it. <p>";
					  
					  $mailContent .= "<p>User name  : ".$certificate_user_name."</p>";
					   $mailContent .= "<p>House Number :  ".$user_house_number."</p>";
					   $mailContent .= "<p>Address :  ".$user_address."</p>";					  
					   $mailContent .= "<p>City :  ".$user_city."</p>";
					   $mailContent .= "<p>Zip code :  ".$user_zip_code."</p>";
					   $mailContent .= "<p>Country : ".$user_country_name."</p>";
						   	
					  $this->email->from('info@trendimi.com', 'Team Trendimi');
					  $this->email->to($tomail); 
					  $this->email->attach($this->path);
					  
					  $this->email->subject($emailSubject);
					  $this->email->message($mailContent);	
					  
					 $this->email->send();
		}
		else
		{
			echo "not entered in send mail";
		}
	
	
	
	 redirect('coursemanager/success_hardcopy', 'refresh');
		
		 
		 
  
  }
  
   function success_hardcopy($product_id)
  {
	$content = array();
	$content=$this->get_student_deatils_for_popup();
	
	$lang_id = $this->session->userdata('language');
	
	 $this->tr_common['tr_Your_certificate_will_be_issued_by_the_accreditation_body_ICOES'] =$this->user_model->translate_('Your_certificate_will_be_issued_by_the_accreditation_body_ICOES');
	   $this->tr_common['tr_please_allow']      =$this->user_model->translate_('please_allow');
	  $this->tr_common['tr_for_delivery']      =$this->user_model->translate_('for_delivery');
	  $this->tr_common['tr_Best_regards']      =$this->user_model->translate_('Best_regards');
	  $this->tr_common['tr_TRENDIMI_Team']      =$this->user_model->translate_('TRENDIMI_Team');
	  
	  
	 $postal_id = $this->certificate_model->get_postal_id($product_id); 
	 
	 $postage_details = $this->certificate_model->get_postage_details($postal_id); 
	 //echo "<pre>"; print_r($postage_details); exit;
	 
	
	  foreach($postage_details as $row2)
	  {
		  $postal_name = $row2->postage_type;
	  }
	  
	  $content['postal_name'] = $postal_name;
	
	  foreach ($postage_details as $value) {
		  
		  if($lang_id==4)
		  {		
			$post_det =  str_replace("&#8226;","",$value->delivery_time);	
		  }
		  else
		  {
			  $post_det =  str_replace("&#8226;","",$value->delivery_time_spanish);
		  }
		   $post_det =  str_replace("*",",",$post_det);				
			$content['postal_estimate_time'] =  strip_tags($post_det);						
		}
	
	$data['translate'] = $this->tr_common;
	$data['view'] = 'result_hardcopy';   
	$data['content'] = $content;
    $this->load->view('user/template_inner',$data);
  }
  
  
  function afterextend(){
    $content1 = $this->uri->uri_to_assoc(3);
    $course_id=$content1['cour_id'];
    $days=$content1['days'];
    $status=$content1['status'];
    $accessdate=date("Y-m-d", strtotime("+$days days"));
    $content=$this->coursedetails();
    $student_id=$content['stud_id'];
    $Insertdata=array("users_idusers"=>$student_id,"courses_idcourses"=>$course_id,"access_date_expiry"=>$accessdate,"status"=>$status);
    $this->user_model->insertQuerys("student_course_access",$Insertdata);
    redirect('coursemanager/campus/'.$student_id, 'refresh');
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
  function mymarks($courseId){

    //$content=$this->coursedetails();
	
	//$userId=$content['stud_id'];
	$userId = $this->session->userdata['student_logged_in']['id'];
	//$userCoursesArr=$this->user_model->get_courses_student($userId);
	$course_name = $this->common_model->get_course_name($courseId); 
	$slNo=0;

		$content=$this->get_student_deatils_for_popup();

	
	
	
	
	
	$courseUnitArray=$this->user_model->getCourseUnitListing($courseId,1);
	//echo "<br>-----------------<pre>";print_r($courseUnitArray);echo "</pre>";
	if(!empty($courseUnitArray)) {
		foreach($courseUnitArray as $courseUnitArr) {	
			$unitId        = $courseUnitArr['course_units_idcourse_units'];
			//whether the unit is completed or not by checking the pages in the unit================================================
			//$unitComplete  = $objTest->getUnitCompleteByUser($userId,$unitId,$courseId);	
			$pageIdsArr[$unitId]=$this->user_model->getPageIdsForUnits($unitId);
			$studentPageIdsArr[$unitId] = $this->user_model->getStudentProgressPageIds($userId,$unitId,$courseId);
			
			//========================================================================================================
			//total tasks in the unit
			$taskArray[$unitId]     = $this->user_model->getTasksInUnit_forMarks($unitId);//echo "<p>";print_r($taskArray);exit;
			$userTaskArray[$unitId] = $this->user_model->getTasksForUserInUnitNew($userId,$unitId,$courseId);//echo "<p>";print_r($userTaskArray);echo "</p>";
			//echo "<pre>";print_r($userTaskArray[$unitId]);echo"</pre>";
			//$totalTaskUser = count($userTaskArray);
			
			//the marks obtained by user in a particular unit in a course
			$marksDetails[$unitId]  =  $this->user_model->getUnitMarksForTasks($userId,$unitId,$courseId);								
				
		}
	}
	$marks_data['courseId']            =$courseId;
	$marks_data['courseUnitArray']     =$courseUnitArray;
	$marks_data['unitId']              =$unitId;
	$marks_data['pageIdsArr']          =$pageIdsArr;
	$marks_data['studentPageIdsArr']   =$studentPageIdsArr;	
	$marks_data['taskArray']           =$taskArray;
	$marks_data['userTaskArray']       =$userTaskArray;
	$marks_data['marksDetails']        =$marksDetails;
	
    $content['courseId']               =$courseId;
	$content['userId']                 =$userId;
	                
	$course_progress_array = $this->get_student_progress($courseId);
	
	$data['tr_my_course'] =$this->user_model->translate_('my_course');
	$data['tr_final_score'] =$this->user_model->translate_('final_score');
	$data['tr_view_details'] =$this->user_model->translate_('view_details');
	$data['tr_my_marks_new'] =$this->user_model->translate_('my_marks_new');
	
	$data['course_progress_array']  = $course_progress_array;
	
	$data['course_name'] = $course_name;
	$data['translate'] = $this->tr_common;
   $data['view'] = 'my_marks_new';
    $data['content'] = $content;
	$data['marks_data'] = $marks_data;
	//$data['view'] = 'mymarks';
    //$this->load->view('user/course_template',$data);
	$this->load->view('user/template_inner',$data);
  }
  	
  function courseprogress($courseId){

   // $content=$this->coursedetails();
   $studentUserId=$this->session->userdata['student_logged_in']['id'];
	//$studentUserId=$content['stud_id'];
	
	 $this->tr_common['tr_view_unsaved_pages'] = $this->user_model->translate_('view_unsaved_pages');
	 $this->tr_common['tr_course_progress'] = $this->user_model->translate_('course_progress');
   	$unitsIdArr =  $this->user_model->getCourseUnitListing($courseId,1);
         $i=0;
		 	$content=$this->get_student_deatils_for_popup();
		 
        for ($unitCnt=0;$unitCnt<count($unitsIdArr);$unitCnt++) {
        $i++;
        $unitDetailsArr[$unitCnt] = $this->user_model->get_courseunits($unitsIdArr[$unitCnt]['course_units_idcourse_units']	);
        $pageIdsArr[$unitCnt] = $this->user_model->getPageIdsForUnits($unitsIdArr[$unitCnt]['course_units_idcourse_units']	);
        $studentPageIdsArr[$unitCnt] =  $this->user_model->getStudentProgressPageIds($studentUserId,$unitsIdArr[$unitCnt]['course_units_idcourse_units'],$courseId);
        }
		/*echo "<br>Unit Id arr";
		echo "<pre>";
		print_r($unitsIdArr);
		echo "<br>Unit details array";
		echo "<pre>";
		print_r($unitDetailsArr);
		echo "<br>PageId arr";
		echo "<pre>";
		print_r($pageIdsArr);
		echo "<br>Student PageId arr";
		echo "<pre>";
		print_r($studentPageIdsArr);*/
		
	$content['course_name'] = $this->common_model->get_course_name($courseId);
		
	$content['unitsIdArr']=$unitsIdArr;
	$content['unitDetailsArr1']=$unitDetailsArr;
	$content['pageIdsArr1']=$pageIdsArr;
	$content['studentPageIdsArr1']=$studentPageIdsArr;
    $content['cour_id']=$courseId;
	$data['translate'] = $this->tr_common;
    $data['view'] = 'course_progress';
    $data['content'] = $content;
    $this->load->view('user/template_inner',$data);
  }
  
  function certificate_pre()
  {
	
	
	$content = array();
	$certificate_status =array();
	$course_id = array();
	$course_name_array =array();
	$user_id = $this->session->userdata['student_logged_in']['id'];
	$content=$this->get_student_deatils_for_popup();
	$lang_id = $this->session->userdata('language');
	
	$enrolled_courses = $this->user_model->get_courses_student($user_id); 
	
	
	$this->tr_common['tr_my_certificates']   =$this->user_model->translate_('my_certificates');	
	$this->tr_common['tr_apply_for_certificate']   =$this->user_model->translate_('apply_for_certificate');
	$this->tr_common['tr_click_course_for_apply_certificate']   =$this->user_model->translate_('click_course_for_apply_certificate');		
	
	$this->tr_common['tr_Course_not_started_yet']   =$this->user_model->translate_('Course_not_started_yet');
	
	$this->tr_common['tr_You_must_pass_the_course_before_you_can']   =$this->user_model->translate_('You_must_pass_the_course_before_you_can');
	$this->tr_common['tr_Certficate_request_processing']   =$this->user_model->translate_('Certficate_request_processing');
	
	
	
	$k=0;
	
	foreach ($enrolled_courses as $key => $value) {	
		$course_name_array[$k] = $this->common_model->get_course_name($value->course_id); 
		$course_id[$k] = rawurlencode($value->course_id);
			
		
			if($value->course_status == 0) 			// not started
			{ 
				$certificate_status[$k] = 'not_started'; 
		 	}
			else if($value->course_status == 1) 	// studying
			{
				$mark_details = $this->get_student_progress($value->course_id); 
				/*echo "<pre>";
				print_r($mark_details);
				exit;*/
				if($mark_details['progressPercnt']==100 && $mark_details['coursePercentage']>=55 && $mark_details['course_passed']==1)
				{
					$certificate_status[$k] = 'passed'; 					
				}
				else
				{
					$certificate_status[$k] = 'not_passed'; 
				}
			}
			else if($value->course_status == 2) 	// completed
			{ 
				$mark_details = $this->get_student_progress($value->course_id); 
				if($mark_details['progressPercnt']==100 && $mark_details['coursePercentage']>=55 && $mark_details['course_passed']==1)
				{
					$certificate_status[$k] = 'passed'; 
				}
				else
				{
					$certificate_status[$k] = 'not_passed'; 
				}
			}
			else if($value->course_status == 3) 	// certificate applied
			{ 
				$certificate_status[$k] = 'pending_approval'; 
			}
			else if($value->course_status == 4) 	// certificate isseued
			{ 
				$mark_details = $this->get_student_progress($value->course_id); 
				$certificate_status[$k] = 'issued';
				
				 		 		
			}
			else if($value->course_status == 5) 	// material access
			{ 
				//$certificate_status[$k] = 'material_access'; 
				
				$mark_details = $this->get_student_progress($value->course_id); 
				if($mark_details['progressPercnt']==100 && $mark_details['coursePercentage']>=55 && $mark_details['course_passed']==1)
				{
					//$certificate_status[$k] = 'passed'; 
					$certificate_details =  $this->certificate_model->get_certificate_details($user_id,$value->course_id);
					if(empty($certificate_details))
					{
						$certificate_status[$k] = 'passed'; 
					}
					else
					{
						$certificate_status[$k] = 'issued'; 
					}
				}
				else
				{
					$certificate_status[$k] = 'not_passed'; 
				}	 	
				
				
			}
			else if($value->course_status == 6) 	// archieved
			{ 
				//$certificate_status[$k] = 'archieved'; 	
				$mark_details = $this->get_student_progress($value->course_id); 
				if($mark_details['progressPercnt']==100 && $mark_details['coursePercentage']>=55 && $mark_details['course_passed']==1)
				{
					//$certificate_status[$k] = 'passed'; 
					$certificate_details =  $this->certificate_model->get_certificate_details($user_id,$value->course_id);
					if(empty($certificate_details))
					{
						$certificate_status[$k] = 'passed'; 
					}
					else
					{
						$certificate_status[$k] = 'issued'; 
					}
				}
				else
				{
					$certificate_status[$k] = 'not_passed'; 
				}	 		
			}
			else if($value->course_status == 7) 	// expired
			{ 
				
				//$certificate_status[$k] = 'expired'; 	
				$mark_details = $this->get_student_progress($value->course_id); 
				if($mark_details['progressPercnt']==100 && $mark_details['coursePercentage']>=55 && $mark_details['course_passed']==1)
				{
					$certificate_details =  $this->certificate_model->get_certificate_details($user_id,$value->course_id);
					if(empty($certificate_details))
					{
						//$certificate_status[$k] = 'passed'; 
						$certificate_details =  $this->certificate_model->get_certificate_details($user_id,$value->course_id);
						if(empty($certificate_details))
						{
							$certificate_status[$k] = 'passed'; 
						}
						else
						{
							$certificate_status[$k] = 'issued'; 
						}
					}
					else
					{
						$certificate_status[$k] = 'approved'; 
					}
					
					
						
				}
				else
				{
					$certificate_status[$k] = 'not_passed'; 
				}	 		
			}
			
			
			$k++;
		}
		
	
	$content['enrolled_courses']   = $enrolled_courses;
	$content['certificate_status'] = $certificate_status;	
	$content['course_name_array']  = $course_name_array;
	$content['course_id']		  = $course_id;
	
	
	$content['user_id']=$user_id;
	 $data['translate'] = $this->tr_common;
	$data['view'] = 'certificate_pre';   
    $data['content'] = $content;
    $this->load->view('user/course_template',$data);  
	  
	  
  }
  
  function certificate()
  {
  	$content = array();
	$certificate_status =array();
	$course_id_encrypted = array();
	$course_name_array =array();
	$user_id = $this->session->userdata['student_logged_in']['id'];
	$content=$this->get_student_deatils_for_popup();
	$lang_id = $this->session->userdata('language');
	
	$enrolled_courses = $this->user_model->get_courses_student($user_id); 
	
	
	$this->tr_common['tr_my_certificates']   =$this->user_model->translate_('my_certificates');	
	$this->tr_common['tr_apply_for_certificate']   =$this->user_model->translate_('apply_for_certificate');
	$this->tr_common['tr_click_course_for_apply_certificate']   =$this->user_model->translate_('click_course_for_apply_certificate');
	
	$this->tr_common['tr_Con_Your_download_your_certificate']   =$this->user_model->translate_('Con_Your_download_your_certificate');
	
	$this->tr_common['tr_DOWNLOAD_CERTIFICATE']   =$this->user_model->translate_('DOWNLOAD_CERTIFICATE');
	
	$this->tr_common['tr_Congratulations_Your_eTranscript_is_ready_to_download']   =$this->user_model->translate_('Congratulations_Your_eTranscript_is_ready_to_download');	
	
	$this->tr_common['tr_Apply_for_your_course_eTranscript_today']   =$this->user_model->translate_('Apply_for_your_course_eTranscript_today');
		
	$this->tr_common['tr_Apply_for_hardcopy_of_your_certificate_bypayingasmallfee']   =$this->user_model->translate_('Apply_for_hardcopy_of_your_certificate_bypayingasmallfee');	
	
	$this->tr_common['tr_Celebrate_your_achievement_and_show_off']   =$this->user_model->translate_('Celebrate_your_achievement_and_show_off');	
	$this->tr_common['tr_Apply_eTranscript']   =$this->user_model->translate_('Apply_eTranscript');
	$this->tr_common['tr_Request_hard_copy']   =$this->user_model->translate_('Request_hard_copy');
	$this->tr_common['tr_Proof_of_Completion']   =$this->user_model->translate_('Proof_of_Completion');
	$this->tr_common['tr_Buy_an_eBook']   =$this->user_model->translate_('Buy_an_eBook');
	$this->tr_common['tr_Expand_your_styling_skills_with_extra_courses']   =$this->user_model->translate_('Expand_your_styling_skills_with_extra_courses');
	$this->tr_common['tr_Course_material_subscritption']   =$this->user_model->translate_('Course_material_subscritption');
	$this->tr_common['tr_Sign_up_for_6_or_12_month_course']   =$this->user_model->translate_('Sign_up_for_6_or_12_month_course');
	$this->tr_common['tr_DOWNLOAD_eTranscript']   =$this->user_model->translate_('DOWNLOAD_eTranscript');
	$this->tr_common['tr_Congratulations_Your_proof_of_completion']   =$this->user_model->translate_('Congratulations_Your_proof_of_completion');
	$this->tr_common['tr_DOWNLOAD_proof completion']   =$this->user_model->translate_('DOWNLOAD_proof completion');
	$this->tr_common['tr_Congratulations_Your_course_of_enrolemnt']   =$this->user_model->translate_('Congratulations_Your_course_of_enrolemnt');
	$this->tr_common['tr_DOWNLOAD_course_enrolment']   =$this->user_model->translate_('DOWNLOAD_course_enrolment');
	$this->tr_common['tr_Course_not_started']   =$this->user_model->translate_('Course_not_started');
	$this->tr_common['tr_Course_not_started_yet']   =$this->user_model->translate_('Course_not_started_yet');
	$this->tr_common['tr_Apply_certificate']   =$this->user_model->translate_('Apply_certificate');
	$this->tr_common['tr_You_can_request_your_certificate_now']   =$this->user_model->translate_('You_can_request_your_certificate_now');
	$this->tr_common['tr_Your_certficate_request_under_processing']   =$this->user_model->translate_('Your_certficate_request_under_processing');
	$this->tr_common['tr_You_can_download_your_certificate_after_approval']   =$this->user_model->translate_('You_can_download_your_certificate_after_approval');
	
	$this->tr_common['tr_proof_study']   =$this->user_model->translate_('proof_study');		
	$this->tr_common['tr_proof_study_txt']   =$this->user_model->translate_('proof_study_txt');		
	$this->tr_common['tr_Request_your_ICOES_certificate']   =$this->user_model->translate_('Request_your_ICOES_certificate');	
	$this->tr_common['tr_Apply_for_yourICOES_accredited_hardcopy_certificate'] = $this->user_model->translate_('Apply_for_yourICOES_accredited_hardcopy_certificate');

	
	
	/*echo "<pre>";
	print_r($enrolled_courses);*/
	//exit;
	//$this->load->library('encrypt');
	$k=0;
	
		/*$progress['course_id']        = $course_id;
		$progress['course_name']      = $course_name;
		$progress['coursePercentage'] = $coursePercentage;
		$progress['progressPercnt']   = $progressPercnt;
		$progress['daysremaining']    = $numberOfDaysRemaining;*/
	
	foreach ($enrolled_courses as $key => $value) {	
		$course_name_array[$k] = $this->common_model->get_course_name($value->course_id); 
		$course_id_encrypted[$k] = rawurlencode($value->course_id);
		//echo rawurlencode($value->course_id);
		//	$course_id_encrypted[$k]  = $value->course_id;
		
		
		
		
		
			if($value->course_status == 0) 			// not started
			{ 
				$certificate_status[$k] = 'not_started'; 
		 	}
			else if($value->course_status == 1) 	// studying
			{
				$mark_details = $this->get_student_progress($value->course_id); 
				if($mark_details['progressPercnt']==100 && $mark_details['coursePercentage']>=55)
				{
					$certificate_status[$k] = 'passed'; 					
				}
				else
				{
					$certificate_status[$k] = 'not_passed'; 
				}
			}
			else if($value->course_status == 2) 	// completed
			{ 
				$mark_details = $this->get_student_progress($value->course_id); 
				if($mark_details['progressPercnt']==100 && $mark_details['coursePercentage']>=55)
				{
					$certificate_status[$k] = 'passed'; 
				}
				else
				{
					$certificate_status[$k] = 'not_passed'; 
				}
			}
			else if($value->course_status == 3) 	// certificate applied
			{ 
				$certificate_status[$k] = 'pending_approval'; 
				
			}
			else if($value->course_status == 4) 	// certificate isseued
			{ 
				$mark_details = $this->get_student_progress($value->course_id); 
				$certificate_status[$k] = 'issued';
				/*if($mark_details['progressPercnt']==100 && $mark_details['coursePercentage']>=55)
				{
					$certificate_status[$k] = 'issued';
				}
				else
				{
					$certificate_status[$k] = 'not_passed'; 
				}*/
				 		 		
			}
			else if($value->course_status == 5) 	// material access
			{ 
				//$certificate_status[$k] = 'material_access'; 
				$mark_details = $this->get_student_progress($value->course_id); 
				if($mark_details['progressPercnt']==100 && $mark_details['coursePercentage']>=55)
				{
					//$certificate_status[$k] = 'passed'; 
					
					$certificate_details =  $this->certificate_model->get_certificate_details($user_id,$value->course_id);
					if(empty($certificate_details))
					{
						$certificate_status[$k] = 'passed'; 
					}
					else
					{
						$certificate_status[$k] = 'issued'; 
					}
				}
				else
				{
					$certificate_status[$k] = 'not_passed'; 
				}
				
				
			}
			else if($value->course_status == 6) 	// archieved
			{ 
				//$certificate_status[$k] = 'archieved'; 	
				
				$mark_details = $this->get_student_progress($value->course_id); 
				if($mark_details['progressPercnt']==100 && $mark_details['coursePercentage']>=55)
				{
					//$certificate_status[$k] = 'passed'; 
					
					$certificate_details =  $this->certificate_model->get_certificate_details($user_id,$value->course_id);
					if(empty($certificate_details))
					{
						$certificate_status[$k] = 'passed'; 
					}
					else
					{
						$certificate_status[$k] = 'issued'; 
					}
				}
				else
				{
					$certificate_status[$k] = 'not_passed'; 
				}	 		
			}
			else if($value->course_status == 7) 	// expired
			{ 
				
				//$certificate_status[$k] = 'expired'; 	
				$mark_details = $this->get_student_progress($value->course_id); 
				
				
				if($mark_details['progressPercnt']==100 && $mark_details['coursePercentage']>=55)
				{
				//	$certificate_status[$k] = 'passed'; 				
					
					$certificate_details =  $this->certificate_model->get_certificate_details($user_id,$value->course_id);
					if(empty($certificate_details))
					{
						$certificate_status[$k] = 'passed'; 
					}
					else
					{
						$certificate_status[$k] = 'issued'; 
					}
				}
				else
				{
					$certificate_status[$k] = 'not_passed'; 
				}	 		
			}
			
			$user_subscriptions[$k]['eTranscript'] 		   =  0;
			$user_subscriptions[$k]['hardcopy'] 			  =  0;
			$user_subscriptions[$k]['proof_enrolment'] 	   =  0;
			$user_subscriptions[$k]['material_subscription'] =  0;
			$user_subscriptions[$k]['proof_completion'] 	  =  0;
			
			$subscriptions = $this->user_model->get_user_subscriptions($user_id,$value->course_id);
			
			/*echo "<pre>";
			print_r($subscriptions);*/
			
			if(!empty($subscriptions))
			{
				foreach ($subscriptions as $key => $value2){
			
					if($value2->type == 'hardcopy')
					{
						$user_subscriptions[$k]['hardcopy'] 			  =  1;
					}
					else if($value2->type == 'transcript' || $value2->type == 'transcript_hard')
					{
						$user_subscriptions[$k]['eTranscript'] 		   =  1;
					}
					else if($value2->type == 'poe_soft')
					{
						$user_subscriptions[$k]['proof_enrolment'] 	   =  1;
					}
					else if($value2->type == 'poe_hard')
					{
						$user_subscriptions[$k]['proof_enrolment'] 	   =  1;
					}
					else if($value2->type == 'proof_completion' || $value2->type == 'proof_completion_hard')
					{
						$user_subscriptions[$k]['proof_completion'] 	  =  1;
					}
					else if($value2->type == 'material_subscription')
					{
						$user_subscriptions[$k]['material_subscription'] =  1;
					}
				
				}
			}
			
			
			$k++;
		}
		
		
	/*	echo "<pre>";
		print_r($course_id_encrypted);
		exit;*/
	
	$content['enrolled_courses']   = $enrolled_courses;
	$content['certificate_status'] = $certificate_status;
	$content['user_subscriptions'] = $user_subscriptions;
	$content['course_name_array']  = $course_name_array;
	$content['course_id_encrypted']= $course_id_encrypted;
	
	$content['user_id']=$user_id;
	 $data['translate'] = $this->tr_common;
	$data['view'] = 'certificate';   
    $data['content'] = $content;
    $this->load->view('user/course_template',$data);  
  }
  
  
    function certificate_download($course_id_encrypted)
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
	 	$certificate_user_name = $value->first_name.' '.$value->last_name;		
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
			$courseTitle = 'Event Planner';

		//	$coursename=$this->user_model->translate_('cert_styleme');
			$coursename= 'Event Planner Course';
		}
		/*else if($lang_id==3 && $course_id==11 )
		{
		//	$class='outer_cert_english_styleme';
			$courseTitle = 'Autoimagen';
			//$coursename=$this->user_model->translate_('cert_styleme');
			$coursename='Estilista personal';
		}*/
		/* End Style Me course */	
		
		/* Style You course */

		else if($lang_id==4 && $course_id==2 )
		{ 
		//$class='outer_cert_english_styleyou';
		//$coursename=$this->user_model->translate_('cert_styleyou');
		$courseTitle = 'Wedding Planner Course';
		$coursename = 'Wedding Planner Course';
		}
		/*else if($lang_id==3 && $course_id==12 )
		{
		//$class='outer_cert_spanish_styleyou';
		$courseTitle = 'Personal Shopper';
		//$coursename=$this->user_model->translate_('cert_styleyou');
		$coursename = 'Estilista profesional';
		}*/
		
		/* End Style You course */
		
		/* Make Up course */

		
		
		//$cssLink = base_url();
		/*if($lang_id==3)
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
			
		}*/
		if($lang_id==4)
		{
			//$cssLink = "public/certificate/css/certificate-style_new_english.css";
			//$cssLink = "http://trendimi.net/public/certificate/css/certificate-style_new_2.css";
			
$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>EN-Certificate</title>
<style type="text/css">
/* BEGIN Light */
@font-face {
  font-family: "Open Sans";
  src: url("/public/user/certificate/fonts/Light/OpenSans-Light.eot");
  src: url("/public/user/certificate/fonts/Light/OpenSans-Light.eot?#iefix") format("embedded-opentype"),
       url("/public/user/certificate/fonts/Light/OpenSans-Light.woff") format("woff"),
       url("/public/user/certificate/fonts/Light/OpenSans-Light.ttf") format("truetype"),
       url("/public/user/certificate/fonts/Light/OpenSans-Light.svg#OpenSansLight") format("svg");
  font-weight: 300;
  font-style: normal;
}
/* END Light */


/* BEGIN Regular */
@font-face {
  font-family: "Open Sans";
  src: url("/public/user/certificate/fonts/Regular/OpenSans-Regular.eot");
  src: url("/public/user/certificate/fonts/Regular/OpenSans-Regular.eot?#iefix") format("embedded-opentype"),
       url("/public/user/certificate/fonts/Regular/OpenSans-Regular.woff") format("woff"),
       url("/public/user/certificate/fonts/Regular/OpenSans-Regular.ttf") format("truetype"),
       url("/public/user/certificate/fonts/Regular/OpenSans-Regular.svg#OpenSansRegular") format("svg");
  font-weight: normal;
  font-style: normal;
}
/* END Regular */

/* BEGIN Italic */
@font-face {
  font-family: "Open Sans";
  src: url("/public/user/certificate/fonts/Italic/OpenSans-Italic.eot");
  src: url("/public/user/certificate/fonts/Italic/OpenSans-Italic.eot?#iefix") format("embedded-opentype"),
       url("/public/user/certificate/fonts/Italic/OpenSans-Italic.woff") format("woff"),
       url("/public/user/certificate/fonts/Italic/OpenSans-Italic.ttf") format("truetype"),
       url("/public/user/certificate/fonts/Italic/OpenSans-Italic.svg#OpenSansItalic") format("svg");
  font-weight: normal;
  font-style: italic;
}
/* END Italic */

/* BEGIN Semibold */
@font-face {
  font-family: "Open Sans";
  src: url("/public/user/certificate/fonts/Semibold/OpenSans-Semibold.eot");
  src: url("/public/user/certificate/fonts/Semibold/OpenSans-Semibold.eot?#iefix") format("embedded-opentype"),
       url("/public/user/certificate/fonts/Semibold/OpenSans-Semibold.woff") format("woff"),
       url("/public/user/certificate/fonts/Semibold/OpenSans-Semibold.ttf") format("truetype"),
       url("/public/user/certificate/fonts/Semibold/OpenSans-Semibold.svg#OpenSansSemibold") format("svg");
  font-weight: 600;
  font-style: normal;
}
/* END Semibold */

/* BEGIN Semibold Italic */
@font-face {
  font-family: "Open Sans";
  src: url("/public/user/certificate/fonts/SemiboldItalic/OpenSans-SemiboldItalic.eot");
  src: url("/public/user/certificate/fonts/SemiboldItalic/OpenSans-SemiboldItalic.eot?#iefix") format("embedded-opentype"),
       url("/public/user/certificate/fonts/SemiboldItalic/OpenSans-SemiboldItalic.woff") format("woff"),
       url("/public/user/certificate/fonts/SemiboldItalic/OpenSans-SemiboldItalic.ttf") format("truetype"),
       url("/public/user/certificate/fonts/SemiboldItalic/OpenSans-SemiboldItalic.svg#OpenSansSemiboldItalic") format("svg");
  font-weight: 600;
  font-style: italic;
}
/* END Semibold Italic */

/* BEGIN Bold */
@font-face {
  font-family: "Open Sans";
  src: url("/public/user/certificate/fonts/Bold/OpenSans-Bold.eot");
  src: url("/public/user/certificate/fonts/Bold/OpenSans-Bold.eot?#iefix") format("embedded-opentype"),
       url("/public/user/certificate/fonts/Bold/OpenSans-Bold.woff") format("woff"),
       url("/public/user/certificate/fonts/Bold/OpenSans-Bold.ttf") format("truetype"),
       url("/public/user/certificate/fonts/Bold/OpenSans-Bold.svg#OpenSansBold") format("svg");
  font-weight: bold;
  font-style: normal;
}
/* END Bold */
body, html{margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; color:#666;}
.outer{margin:20px auto 0 auto; background:white url(/public/user/certificate/images/eng-certificate.jpg) center center; width:713px; height:1013px}
h1, h2, h3{text-align:center; margin:0;}
h1{padding:10.5em 0 0 0; font-size:25pt}
h2{padding:2em 0 0 0; font-weight:600; font-size:25pt}
h3{padding:3em 0 0 0}
h3 span{margin-left:1em}
h4, p{font-size:13pt; margin:0; font-weight:600}
h4{padding:25.5em 0 0 2em}
p{padding:0.1em 0 0 2em}
h4 span, p span{width:180px; display:inline-block}
</style>
</head>

<body>
<div class="outer">
<h1>'.$certificate_user_name.'</h1>
<h2>'. $coursename.'</h2>
<h4><span>Date of Award:</span> '.$month.' '.$year.'</h4>
</div>
</body>
</html>
';
			
		}

		


/*echo $html;
		exit;*/
	 $data = pdf_create($html, 'EventTrix_'.$user_id.'_'.$course_id);
     //or
     //$data = pdf_create($html, '', false);
     write_file('name', $data);	
		
		
  
	  
  }
  
  function certificate_download_old($course_id_encrypted)
  {
	  
	   $this->load->helper(array('dompdf', 'file'));
	   
  	 if(!$this->session->userdata('student_logged_in')){
		  redirect('home');
		}
	$lang_id = $this->session->userdata('language');
	$user_id=$this->session->userdata['student_logged_in']['id'];
		
	 //$this->load->library('encrypt');
	// $course_id = $this->encrypt->decode($course_id_encrypted);
	 $course_id = $course_id_encrypted;
	 $course_name = $this->common_model->get_course_name($course_id);
	 $user_details = $this->user_model->get_student_details($user_id); 
	
	 foreach($user_details as $key => $value)
	 {
	 	$certificate_user_name = $value->first_name.' '.$value->last_name;		
	 }
	 $mark_details = $this->get_student_progress($course_id);
	 
	 
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
  
  
  
  
  
  
  
  
  
  
    function eTranscript_download($course_id)
	  {
	  
		  $this->load->helper(array('dompdf', 'file'));
		   $userId = $this->session->userdata['student_logged_in']['id'];	
		  $user_name = $this->common_model->get_user_name($userId);
		  
		$lang_id = $this->session->userdata('language');
		 
		  $course_name = $this->common_model->get_course_name($course_id); 
		  $slNo=0;
		  $course_name = strtolower($course_name);
		  $course_name = ucfirst($course_name);
		  
		  $user_details = $this->user_model->get_student_details($userId); 
	
		 foreach($user_details as $key => $value)
		 {
			$certificate_user_name = $value->first_name.' '.$value->last_name;		
		 }
		 $mark_details = $this->get_student_progress($course_id);
		 
		 
		 $certificate_user_name = strtolower($certificate_user_name);
		 $certificate_user_name = ucwords($certificate_user_name);
		  
		  
	
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
.courseNme{font-size:32pt; font-weight:normal; margin:0; padding:0; color:#df3e8e; text-align:center}
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
.courseNme{font-size:32pt; font-weight:normal; margin:0; padding:0; color:#df3e8e; text-align:center}
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

	if($lang_id==4)
	{	
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
<h3 class="studentNme">Name of student: '.$certificate_user_name.'</h3>
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
	}
	else if($lang_id==3)
	{
		$html .='<div class="outer">
<div class="header">
<h2 class="logotxt">- formación online -</h2>

</div>
<div class="clear"></div>
<div class="content">
<h2 class="letterNme">Expediente académico</h2>
<div class="clear"></div>
<h1 class="courseNme">Curso: '.$course_name.'</h1>
<div class="clear"></div>
<h3 class="studentNme">Nombre del estudiante: '.$certificate_user_name.'</h3>
<div class="clear"></div>
<h3 class="studentNme">Grado Trendimi: '.$grade.'</h3>
<div class="clear"></div>
<table border="0" cellpadding="0" cellspacing="0.1em">
  <tr>
    <th scope="col" class="left" style="border-right:0.2em solid #fff">Módulo</th>
    <th scope="col" class="left" style="border-right:0.2em solid #fff">Título</th>
    <th scope="col" class="center">Título Puntuación</th>
  </tr>
';
	}
 
		
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
					
			$module_name = explode(':',$unitName);		
 $html .= '<tr>
<td class="left">'.($y+1).'</td>
<td class="left">'.$module_name[1].'</td>
<td class="center">'.$unitPercentage.' %</td>
</tr>';
		
				}
				
				} else {
					
					$module_name = explode(':',$marks_data['courseUnitArray'][$y]['unitName']);
$html .= '<tr>
<td class="left">'.($y+1).'</td>
<td class="left">'.$module_name[1].'</td>';

	if($lang_id==4)
	{
		$html .= '<td class="center">No tasks are there in this unit</td></tr>';
	}
	else
	{
		$html .= '<td class="center" style="font-size:10pt" >No hay examenes asignados a este modulo</td></tr>';		
	}

				}
				
				$y++;
				}
				
				
			}	
			
			
			
				 }// end for userCoursesArr
				 
				 
				 
				 
		

if($lang_id==4)
	{		 			
$html .= '</table>
		 <div class="clear"></div>
		 <h3 class="cEo">Francisca Tomàs
		 <span>CEO</span></h3>
		 </div>
		 </div>
		 </body>
		 </html>
		';
	}
	else
	{
		$html .= '</table>
				 <div class="clear"></div>
				 <h3 class="cEo">Francisca Tomàs
				 <span>Directora General/span></h3>
				 </div>
				 </div>
				 </body>
				 </html>
				';
	}
		
		/*echo $html;
		exit;*/
	
	 $data = pdf_create($html, 'eTranscript_'.$userId.'_'.$course_id);
     //or
     //$data = pdf_create($html, '', false);
     write_file('name', $data);		  
	  	  
		  
		  
	  
  }
  
  
  	
  
  function hardcopy_select_postal($course_id)
  {
	  if(!$this->session->userdata('student_logged_in')){
		  redirect('home');
		}
	$lang_id = $this->session->userdata('language');
	$user_id=$this->session->userdata['student_logged_in']['id']; 
	 
  	$content = array();
	$currency_id = $this->currId;
	$currency_code = $this->currencyCode;
	$content=$this->get_student_deatils_for_popup();
	$enrolled_courses = $this->user_model->get_courses_student($user_id); 
	$k=0;
	foreach ($enrolled_courses as $key => $value) {	
		$course_name_array[$k] = $this->common_model->get_course_name($value->course_id); 
		$k++;
	}
	
	$postage_options = $this->certificate_model->get_postage_options();
	
	$postage_details = $this->certificate_model->get_postage_details(1); 
	$product_id = $this->common_model->getProdectId('hardcopy',1,1);
//	$postage_amount = $this->common_model->getProductFee($product_id,2); // get price in GBP
	$postage_amount = $this->common_model->getProductFee($product_id,$currency_id);
	
//	postage_type_spanish
	
	
	
	 foreach ($postage_details as $value) {	
			if($this->session->userdata('language')==4)
			{
				$data['postal_estimate_time'] =  $value->delivery_time;			
			}
			else
			{
				$data['postal_estimate_time'] =  $value->delivery_time_spanish;
			}	
		}
	$data['currency_code']  	  = $currency_code;	
	
		  $data['postal_amount']  = $postage_amount['amount'];		
		  $data['currency_code']   = $postage_amount['currency_code'];
		  $data['curr_id']         = $postage_amount['currency_id'];
		  $data['currency_symbol'] = $postage_amount['currency_symbol'];				
		
	$content['product_id']  = $product_id;	
	$content['postage_options'] = $postage_options;
//	$data['select_payment_option_text'] = 'Select your payment option. You can pay using credit card or you can pay using your paypal account';

	$data['select_payment_option_text'] = $this->user_model->translate_('select_payment_option_hardcopy');
	
	  $data['text_fees']		=  $this->user_model->translate_('Fee_for_Hardcopy_of_certificate');
	  
	  
	//$data['text_fees']		= 'Fee for Hardcopy of Certificate';	
//	$data['text_delivery']	= 'Delivery estimate -';	
	$data['text_delivery']	= $this->user_model->translate_('Delivery_estimate');
	$data['course_id']  		 = $course_id;
	$data['lang_id']  		 = $lang_id;
	
	
	$data['course_name_sl'] = $this->common_model->get_course_name($course_id); 
	$data['course_name_array']   = $course_name_array;
	$data['enrolled_courses']   = $enrolled_courses;
	//$data['currency_code']  	  = $currency_code;
	
	$this->tr_common['tr_submit'] = $this->user_model->translate_('submit_hard');
	$this->tr_common['tr_All_certificates_issuedby_International_Council_of_Online_Education_Standards'] = $this->user_model->translate_('All_certificates_issuedby_International_Council_of_Online_Education_Standards');

	$data['translate'] = $this->tr_common;
	$data['view'] = 'certificate_hardcopy';   
    $data['content'] = $content;
    $this->load->view('user/template_inner',$data);
  
  }
  
  
   function hard_copy_crtificate_confirm($course_id)
  {
	$content = array();
	 
	 
	 if(!$this->session->userdata('student_logged_in')){
		  redirect('home');
		}
	$lang_id = $this->session->userdata('language');
	$user_id=$this->session->userdata['student_logged_in']['id'];
		
	 //$this->load->library('encrypt');
	// $course_id = $this->encrypt->decode($course_id_encrypted);
	// $course_id = $course_id_encrypted;
	 $coursename = $this->common_model->get_course_name($course_id);
	 
	 //echo ""; print_r($coursename); exit;
	 $user_details = $this->user_model->get_student_details($user_id); 
	
	 foreach($user_details as $key => $value)
	 {
	 	$certificate_user_name = $value->first_name.'&nbsp;'.$value->last_name;		
	 }
	 $mark_details = $this->get_student_progress($course_id);
	 
	 
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
		
		/*echo "<pre>";
		
		print_r($certificate_details);
		exit;*/
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
		
		
				
		
		
		//$cssLink = base_url();
		if($lang_id==3)
		{
			$cssLink = base_url()."public/certificate/icoes_certificate/icoes_certificate_spanish.css";
		}
		else if($lang_id==4)
		{
			$cssLink = base_url()."public/certificate/icoes_certificate/icoes_certificate.css";
		}
		
		

		$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Certificate</title>
</head>
<link href="'.$cssLink.'" type="text/css" rel="stylesheet" />
</head>

<body>
<div class="outer">
<div class="innnr">
<div style="clear:both"></div>
<h2 class="name">'.$certificate_user_name.'</h2>
<h3 class="for">'.$this->user_model->translate_('for_success_completion').'</h3>
<h2 class="course">'. $coursename.'</h2>
<h3 class="with">'.$this->user_model->translate_('course_with').' <span> Eventtrix</span> </h3>
<p class="top"><span>'.$this->user_model->translate_('icoes_grade').':</span> ' .$grade.'</p>
<p><span>'.$this->user_model->translate_('date_of_completion').':</span> '.$month.' '.$year.'</h4>
<p><span>'.$this->user_model->translate_('cert_no').':</span> 100-'.$certificate_id.'</p>
</div></div>
</body>
</html>
';


/*echo $html;
exit;*/

if(isset($_POST['product_id']))
{
	
	$product_id =  $this->input->post('product_id');
	$currency_id =  $this->input->post('currency_id');
}
	else
	redirect('coursemanager/hardcopy_select_postal/'.$course_id,'refresh');
	
	//$product_id = 23;
	$content['product_id']  = $product_id;
	$content['currency_id']  = $currency_id;

	$content['certificate_html'] = $html;
		 
	$content['course_id'] = $course_id;	 
	
	$this->tr_common['tr_see_certficate_below'] = $this->user_model->translate_('see_certficate_below');
	
	$this->tr_common['tr_happy_with_certificate'] = $this->user_model->translate_('happy_with_certificate');
	$this->tr_common['tr_not_happy_with_certificate'] = $this->user_model->translate_('not_happy_with_certificate');
	
	$this->tr_common['tr_congrats_from_icoes'] = $this->user_model->translate_('congrats_from_icoes');
	
	
	$data['translate'] = $this->tr_common;	
	  
	 $data['view'] = 'hard_copy_certificate_confirm';   
      $data['content'] = $content;
     // $this->load->view('user/template_inner',$data);
      $this->load->view('user/pop_up_template',$data);
  }
  
  
  
  /*function hardcopy_select_postal_2()
  {
	    if(!$this->session->userdata('student_logged_in')){
		  redirect('home');
		}
//		$lang_id = $this->session->userdata('language');
		$user_id=$this->session->userdata['student_logged_in']['id']; 
		 
		$content = array();
		$data =array();
		$currency_id = $this->currId;
		$currency_code = $this->currencyCode;		
		
		
		
		
		
			
		 $stud_details=$this->user_model->get_stud_details($user_id);
		 
		
		 
		
		 
		 foreach($stud_details as $val2)
		 {
			 $country_name = $this->user_model->get_country_name($val2->country_id);
			 $content['house_number'] = $val2->house_number;
			 $content['address'] = $val2->address;
			 $content['city'] = $val2->city;
			 $content['zip_code'] = $val2->zipcode;
			 $content['country_name'] = $country_name;
		 }
		
		$data['currency_code']  = $currency_code;
		$data['view'] 		    = 'hardcopy_user_details';   
    	$data['content']  		= $content;
    	$this->load->view('user/course_template',$data);		
		  
  }*/
  
  function hardcopy_user_details($course_id)
  {
	  
	if(!$this->session->userdata('student_logged_in')){
	  redirect('home');
	}
	$user_id=$this->session->userdata['student_logged_in']['id']; 
//	echo "<br> Course id ".$course_id;
	if(isset($_POST['product_id']))
	{
	$product_id =  $this->input->post('product_id');
	$currency_id =  $this->input->post('currency_id');
	}
	else
	redirect('coursemanager/hardcopy_select_postal/'.$course_id,'refresh');
	$content = array();
	$student_update_data = array();
	$data =array();
	//$currency_id = $this->currId;
	//$currency_code = $this->currencyCode;	
	
	$content=$this->get_student_deatils_for_popup();
	 
	
	
		//$postage_amount = $this->certificate_model->get_postage_amount($product_id,$currency_id);
		
		$postage_amount = $this->common_model->getProductFee($product_id,$currency_id); // get price in GBP
		
		
 		  $content['amount'] 		  = $postage_amount['amount'];
		  $content['currency_code']   = $postage_amount['currency_code'];
		  $content['curr_id']         = $postage_amount['currency_id'];
		  $content['currency_symbol'] = $postage_amount['currency_symbol'];		
		 
		
		 $stud_details=$this->user_model->get_stud_details($user_id);
		 
		 if(isset($_POST['confirm_address']))
		{			
			$student_update_data['house_number'] = $apartment  = $this->input->post('apartment');
			$student_update_data['address'] = $address1  = $this->input->post('address1');
			//$address2  = $this->input->post('address2');	
			$student_update_data['country_id'] = $country  = $this->input->post('country');
			$student_update_data['zipcode'] = $zip_code  = $this->input->post('zip_code');
			$student_update_data['city'] = $city  = $this->input->post('city');
			
			
			$this->form_validation->set_rules('apartment', 'Apartment/ House number', 'trim|required');
			$this->form_validation->set_rules('address1', 'Address 1', 'required');			
			//$this->form_validation->set_rules('address2', 'Address 2', 'required');
			$this->form_validation->set_rules('country', 'Country', 'required');
			$this->form_validation->set_rules('zip_code', 'Zip code', 'required');
			$this->form_validation->set_rules('city', 'City', 'required');	
			
		/*	echo "<br>Product id ".$product_id;
			echo "<br> Course id ".$course_id;
			exit;*/
			
			if($this->form_validation->run())
			{
				$this->user_model->update_student_details($student_update_data,$user_id);
			 	redirect('coursemanager/confirm_shipping_address/'.$product_id.'/'.$course_id, 'refresh');
			}
			
								
		}		
		 
		
		 
		 foreach($stud_details as $val2)
		 {
			 $content['country_set'] = $val2->country_id;
		//	 $country_name = $this->user_model->get_country_name($val2->country_id);
			 $content['house_number'] = $val2->house_number;
			 $content['address'] = $val2->address;
			 $content['city'] = $val2->city;
			 $content['zip_code'] = $val2->zipcode;
			// $content['country_name'] = $country_name;
		 }
		$content['country'] = $this->user_model->get_country();
		
		
		
		$data['course_id']  		 = $course_id;
		
		$content['user_id'] 	 = $user_id;  
		$content['product_id']  = $product_id;				
		$data['currency_code']  = $content['currency_code'];
		
		$this->tr_common['tr_Apartment_House_number'] = $this->user_model->translate_('Apartment_House_number');
		$this->tr_common['tr_address_1'] = $this->user_model->translate_('address_1');
		$this->tr_common['tr_city'] = $this->user_model->translate_('city');
		$this->tr_common['tr_zip_code'] = $this->user_model->translate_('zip_code');
		$this->tr_common['tr_Country'] = $this->user_model->translate_('Country');
		$this->tr_common['tr_make_payment'] = $this->user_model->translate_('make_payment');
		$this->tr_common['tr_confirm_address_below'] = $this->user_model->translate_('confirm_address_below');
		
		$this->tr_common['tr_yes_confirm_addr'] = $this->user_model->translate_('yes_confirm_addr');
		$this->tr_common['tr_addr_not_confirm'] = $this->user_model->translate_('addr_not_confirm');
		
		
		
		
		$data['translate'] = $this->tr_common;
		$data['view'] 		   = 'hardcopy_user_details';
		//$data['view'] 		   = 'hard_copy_payment';   
    	$data['content']  		= $content;
    	$this->load->view('user/template_inner',$data);	
			
	  
	  
  }
  
 
  
  function confirm_shipping_address($product_id,$course_id)
  {
	  
	  
	  if(!$this->session->userdata('student_logged_in')){
		  redirect('home');
		}
		
			
	  $user_id=$this->session->userdata['student_logged_in']['id'];
	  
	  $content = array();
	  $data =array();
	  $currency_id = $this->currId;
	  $currency_code = $this->currencyCode;	  
	 	   	 
		$content=$this->get_student_deatils_for_popup();	 
			
			 
	  $stud_details=$this->user_model->get_stud_details($user_id);	
		 
	  foreach($stud_details as $val2)
	  {
		 $country_name = $this->user_model->get_country_name($val2->country_id);
		 $content['house_number'] = $val2->house_number;
		 $content['address'] = $val2->address;
		 $content['city'] = $val2->city;
		 $content['zip_code'] = $val2->zipcode;
		 $content['country_name'] = $country_name;
	  }
	  
	  $postal_id = $this->certificate_model->get_postal_id($product_id); 
	  	 
	  
	  $postage_details = $this->certificate_model->get_postage_details($postal_id); 
	
	  foreach($postage_details as $row2)
	  {
		  $postal_name = $row2->postage_type;
	  }
	  
	  $content['postal_name'] = $postal_name;
	
	  foreach ($postage_details as $value) {	
			if($this->session->userdata('language')==4)
			{
				$data['postal_estimate_time'] =  $value->delivery_time;			
			}
			else
			{
				$data['postal_estimate_time'] =  $value->delivery_time_spanish;
			}			
		}
			
	//  $content['text_delivery']	= 'Delivery estimate -';	
	 $data['text_delivery']	= $this->user_model->translate_('Delivery_estimate');
	 
	 /* $postage_amount = $this->certificate_model->get_postage_amount($product_id,$currency_id);
		
		foreach($postage_amount as $value)
		{
			$content['amount'] = $value->amount;
		}*/
		$postage_amount = $this->common_model->getProductFee($product_id,2); // get price in GBP
		
		
 		  $content['amount'] 		  = $postage_amount['amount'];
		  $content['currency_code']   = $postage_amount['currency_code'];
		  $content['curr_id']         = $postage_amount['currency_id'];
		  $content['currency_symbol'] = $postage_amount['currency_symbol'];		
		
		$data['course_id']  	  = $course_id;
		$content['user_id'] 	 = $user_id;  
		$content['product_id']  = $product_id;			
	//	$data['currency_code']  = $currency_code;
	//	$data['curr_id']	    = $currency_id;	
		$data['translate'] = $this->tr_common;	
		$data['view'] 		   = 'hard_copy_payment';   
    	$data['content']  		= $content;
    	$this->load->view('user/course_template',$data);	 
		 
  }
  
  function get_postal_amount($postal_id)
  {
  	$content = array();
	$data =array();
//	$content=$this->get_student_deatils_for_popup();
	$currency_id = $this->currId;
	$currency_code = $this->currencyCode;
	$postage_details = $this->certificate_model->get_postage_details($postal_id); 
	$product_id = $this->common_model->getProdectId('hardcopy',$postal_id,1);
	//$postage_amount = $this->certificate_model->get_postage_amount($product_id,$currency_id);
	
	$postage_amount = $this->common_model->getProductFee($product_id,$currency_id); // get price in GBP
	
	
	
	/* foreach ($postage_details as $value) {	
			$data['postal_estimate_time'] =  $value->delivery_time;			
		}*/
	$data['currency_code']  	  = $currency_code;	
	 foreach ($postage_amount as $value) {	
			$data['postal_amount']  = $postage_amount['amount'];
			
			
		  $data['currency_code']   = $postage_amount['currency_code'];
		  $data['curr_id']         = $postage_amount['currency_id'];
		  $data['currency_symbol'] = $postage_amount['currency_symbol'];
		  $data['product_id']	  = $product_id;
					
		}
	
	
	
	 foreach ($postage_details as $value) {	
	 		if($this->session->userdata('language')==4)
			{
				$data['postal_estimate_time'] =  $value->delivery_time;			
			}
			else
			{
				$data['postal_estimate_time'] =  $value->delivery_time_spanish;
			}
		}
	//$data['text_fees']		= 'Fee for Hardcopy of Certificate';
	  $data['text_fees']		=  $this->user_model->translate_('Fee_for_Hardcopy_of_certificate');
	  	
	//$data['text_delivery']	= 'Delivery estimate -';	
	
	$data['text_delivery']	= $this->user_model->translate_('Delivery_estimate');
//	$data['currency_code']    = $currency_code;	
	/* foreach ($postage_amount as $value) {	
			$data['postal_amount']  = $value->amount;			
		}*/
	echo json_encode($data);  	
  
  }
  
  
  function coursemodules(){

	$this->tr_common['tr_course_progress'] = $this->user_model->translate_('course_progress');
    $this->tr_common['tr_more_info'] =$this->user_model->translate_('more_info');
	$this->tr_common['tr_my_marks'] =$this->user_model->translate_('my_marks_new');
	$this->tr_common['tr_study_time_remaining'] =$this->user_model->translate_('study_remaining');
	$this->tr_common['tr_days'] =$this->user_model->translate_('camp_days');
	$this->tr_common['tr_reset'] =$this->user_model->translate_('reset');
	$this->tr_common['tr_Select'] =$this->user_model->translate_('Select');
	$this->tr_common['tr_answers'] =$this->user_model->translate_('answers');
	$this->tr_common['tr_ok'] =$this->user_model->translate_('ok');
	$this->tr_common['tr_Congratulations_you_completed_your_course_Click_Ok_to_see_special_student_offers'] =$this->user_model->translate_('Congratulations_you_completed_your_course_Click_Ok_to_see_special_student_offers');
	$this->tr_common['tr_no_thanks'] =$this->user_model->translate_('no_thanks');
	$this->tr_common['tr_resit_task']= $this->user_model->translate_('resit_task');
	$this->tr_common['tr_you_can_reset_after_pay']= $this->user_model->translate_('you_can_reset_after_pay');
	$this->tr_common['tr_you_can_resit_now']= $this->user_model->translate_('you_can_resit_now');
	$this->tr_common['tr_one_more_chance_resit_free']= $this->user_model->translate_('one_more_chance_resit_free');
	$this->tr_common['tr_unfortunately_failed']= $this->user_model->translate_('unfortunately_failed');
	$this->tr_common['tr_you_succ_completed_with_score']= $this->user_model->translate_('you_succ_completed_with_score');
	$this->tr_common['tr_you_can_check_test_det_now']= $this->user_model->translate_('you_can_check_test_det_now');
	$this->tr_common['check_result']= $this->user_model->translate_('check_result');
	$this->tr_common['tr_free_resit_task']= $this->user_model->translate_('free_resit_task');
	$this->tr_common['tr_you_completed_task_check_result_after_completion']= $this->user_model->translate_('you_completed_task_check_result_after_completion');
	$this->tr_common['tr_congrats_all_correct']= $this->user_model->translate_('congrats_all_correct');
	
	$content=$this->get_student_deatils_for_popup();
    $content = $this->uri->uri_to_assoc(3);
    $course_id=$content['cour_id'];
    $unit_id=$content['unit_id'];
    $ref=$content['ref'];
    $slPageId = $content['slPageId']; 
    if(!$this->session->userdata('student_logged_in')){
      redirect('home');
    }
	
	
   // $content=$this->coursedetails();
    /*$section=$this->user_model->getcourse_sections($unit_id,$ref);
	
    foreach ($section as $value) {
      $section_id=$value->idcourse_sections;
      $section_name=$value->sectionName;
      $sectionType=$value->sectionType;
    }*/
	
	/* $relation=$this->user_model->getcourse_unit_relation($unit_id,$ref);
	
    foreach ($relation as $row4) {
     
      $ordered_unit[]=$row4->course_units_idcourse_units;
     
    }
	echo "<pre>";var_dump($ordered_unit);exit;*/
	$studentUserId = $this->session->userdata['student_logged_in']['id'];
	$student_course_status = $this->user_model->get_student_course_status($course_id,$studentUserId);
	if($student_course_status==0)
	{
		$dateNow =date("Y-m-d");
		$user_voucher = $this->user_model->getVoucher_user_course($studentUserId,$course_id);
		
		if($user_voucher!=false)
		{
			$expirityDate = $this->user_model->findExpirityDate($course_id,$dateNow,$user_voucher);
		}
		else
		{
			$expirityDate = $this->user_model->findExpirityDate($course_id,$dateNow);
		}		
		
	   $extension_prepaid_id = false;
	   
	   $package_subscription_details = $this->package_model->get_package_sucbcriptions_user($studentUserId,$course_id);		
	   if(!empty($package_subscription_details))
	   {
		$package_sub_id = $package_subscription_details[0]->id;
		$payment_id = $package_subscription_details[0]->payment_id;
		$extension_prepaid_id = $this->package_model->get_package_puchases_by_product_type('extension',$studentUserId,$course_id,$package_sub_id);
		
			if($extension_prepaid_id)
			{
				$extension_id = $this->user_model->get_extension_id($extension_prepaid_id);					
				$extension_period = $this->user_model->get_extension_details($extension_id);
				foreach($extension_period as $key =>$row)
				{
					$period = $row->extension_days;
				}
				
				/* If Included add extension period to the validity of added course */				
				 $expirityDate=date('Y-m-d', strtotime($expirityDate. ' + '.$period.' days'));		 				 
				
			}
			$access_prepaid_id = $this->package_model->get_package_puchases_by_product_type('access',$studentUserId,$course_id,$package_sub_id);
			if($access_prepaid_id)
			{
				$access_id = $this->user_model->get_access_id($access_prepaid_id);					
				//$extension_period = $this->user_model->get_extension_details($access_id);
				if($access_id=='12')
				$period = '365';
				else
				$period = '185';
				
				/* If Included add extension period to the validity of added course */				
				 $expirityDate=date('Y-m-d', strtotime($expirityDate. ' + '.$period.' days'));		 				 
				
			}
		
		}
		
		//$data=array("course_status"=>'1'); // change status to Studying
		$data=array(
		"course_status"=>'1',// change status to Studying
		"date_expiry"=>$expirityDate,
		"start_date"=>date("Y-m-d")); //setting up course expirity date
		$this->user_model->update_student_enrollments($course_id,$studentUserId,$data);
		
		if($extension_prepaid_id)
		{
			$pak_sub_array = array('status'=>0);
			$this->package_model->update_package_subscription_details($studentUserId,$course_id,$package_sub_id,'extension',$pak_sub_array);
			
			$insert_data=array("user_id"=>$studentUserId,"course_id"=>$course_id,"type"=>'extension',"date_applied"=>$dateNow,"product_id"=>$extension_prepaid_id,"payment_id"=>$payment_id);	 
			$this->user_model->insertQuerys("user_subscriptions",$insert_data);
				
		}
	}
	
    $unitArr=$this->user_model->get_courseunits($unit_id);
    foreach ($unitArr as $value) {
      $unitName=$value->unit_name;
    }
    $today = date("Y-m-d",time());
    $student_id=$this->session->userdata['student_logged_in']['id'];
	$course_name = $this->common_model->get_course_name($course_id); 
    $coursename= $course_name;
	
    $userCourseAccess=$this->user_model->getstudent_courseaccess($student_id);
    if(!empty($userCourseAccess)){
      foreach ($userCourseAccess as $value) {
        $expdate = $value->access_date_expiry;
      }
    }
    

    $expiryDtls=$this->user_model->getcourses_student_expiry($student_id,$course_id);
	if(empty($expiryDtls))
	{
		//$this->session->set_flashdata('err_msg',"");
		redirect('coursemanager/campus','refresh');
	}
    
    if(isset($expdate)){
      if(($expiryDtls[0]->date_expiry<$today)&&($expdate<$today)){
        redirect('coursemanager/expireduser/'.$course_id, 'refresh');
      }
    }
    elseif($expiryDtls[0]->date_expiry<$today){
     redirect('coursemanager/expireduser/'.$course_id, 'refresh');
    }
	
    if( !empty($unit_id) ) {
      $sectionpages=$this->user_model->getSectionPages1($unit_id,$ref);
      foreach ($sectionpages as $value) {
        $pages[]=($value);
      }
	 
      if(!empty($pages)) {
        $pageNum=1;
        foreach($pages as $pagesValue){
          $contentLesson = "";
          $contentTaskId = "";
		  $pageGroup = $pagesValue->page_group;
          $pageType = $pagesValue->page_type;
          $pageId = $pagesValue->page_id;
          $isExcercise=$pagesValue->is_exercise;
          if($pageType == "content" || $pageType == "task" ) {
            if ($pageType == "content") {
				
			$isInCon = $this->user_model->isPageidInContent($pageId);
			if(!empty($isInCon))
				{
              $pageContent = $this->user_model->getSectionPageContent($pageId);
			  //print_r($pageContent);
              $contentLesson =  $pageContent;
				}
				else
				{
				continue;
				}
            } 
            else if ($pageType == "task") {
				
				$isInTas = $this->user_model->isPageidInTasks($pageId);
			if(!empty($isInTas))
				{
              $pageContent = $this->user_model->getSectionPageTask($pageId);
              $contentTaskId = $pageContent;
				}
				else
				continue;
            }
            $pageContentModified[] = array("pageNum"=> $pageNum , "contentLesson"=>$contentLesson, "contentTaskId"=>$contentTaskId , "pageType" => $pageType, "idcourse_section_pages"=>$pageId,"isExcercise"=>$isExcercise ); 
            $pageNum++;
          }
           
        }
      }
    }
	//echo "<pre>";print_r($pageContentModified);exit;
	if((count($pageContentModified)<=$slPageId)|| $slPageId<0 || empty($pageContentModified) ){
		//echo "worked";exit;
		redirect('coursemanager/studentcourse/'.$course_id,'refresh');
		}
	
    $taskId = $pageContentModified[$slPageId]['contentTaskId'];
	if(!isset($taskId)){
		//echo "worked";
		//redirect('coursemanager/studentcourse/'.$course_id,'refresh');
		}
    
    $unitComplete = $this->user_model->getUnitCompleteByUser_unit($student_id,$unit_id,$course_id);
	
	if($pageGroup==1) // Get marks for excercise pages
	{
	if($taskId!=''){
        $punctuationArr  = $this->user_model->getuserTestDetails($student_id,$taskId,$course_id);
        if(count($punctuationArr)>0){                     
          $ttQstion =      $punctuationArr[0]->total_questions;
          $ttAnswr  =      $punctuationArr[0]->total_marks;
          $content['excercise_punctuation'] = $ttAnswr."/".$ttQstion;
        }
      }
	
	}
	 
    if($unitComplete==1||in_array($taskId, array(2510,2511,2512,2513,2514))) {
		
//setting up resit details---------------------------------edited on 16-Nov-2013
		if($taskId!=''){
			
		$testResultsRepeated_free = $this->user_model->getuserTestRepeatDetails($student_id,$taskId,$course_id,$pageContentModified[$slPageId]['idcourse_section_pages'],'free_resit');
		
		$testResultsRepeated_paid = $this->user_model->getuserTestRepeatDetails($student_id,$taskId,$course_id,$pageContentModified[$slPageId]['idcourse_section_pages'],'paid_resit');	
  //echo "<pre>";print_r($testResultsRepeated_free);
 // echo "<br>paid<pre>";print_r($testResultsRepeated_paid);
    //$resitFeeDtls        = $this->user_model->getresitfeedetails($student_id,$course_id);
     // $resitFees           = $this->user_model->getresitfee();
    
     if(count($testResultsRepeated_paid)>=1){
       // $buttonText     = "Re-sit task";
	   // $displayText    = "You can re-sit for this task now.";  
	    $buttonText     = $this->user_model->translate_('resit_task');
        $displayText    = $this->user_model->translate_('you_can_resit_now');
		$resitLink      = "coursemanager/Resit/".$student_id."/".$taskId."/".$course_id."/".$pageContentModified[$slPageId]['idcourse_section_pages']."/".$unit_id."/".$slPageId."/".$ref;
        $resitFree      = 1;
      } 
      else if(count($testResultsRepeated_free)>=1){
          $resitFree      = 0 ;
       // $buttonText     = "Re-sit task";
	   // $displayText    = " You can attend this task after paying <b>one time re-sit fee</b>.";
	    $buttonText     = $this->user_model->translate_('resit_task');
        $displayText    = $this->user_model->translate_('you_can_reset_after_pay');
		$resitLink      = "coursemanager/Resit/".$student_id."/".$taskId."/".$course_id."/".$pageContentModified[$slPageId]['idcourse_section_pages']."/".$unit_id."/".$slPageId."/".$ref;
      } 
      else if(count($testResultsRepeated_free)<=0 && count($testResultsRepeated_paid)<=0){
		  
        //$buttonText     = "Free Re-sit task";        
       // $displayText    = "You have one more chance to re-sit the task for free.";  
	   	  
		  $buttonText     = $this->user_model->translate_('free_resit_task');
	   	  $displayText    = $this->user_model->translate_('one_more_chance_resit_free');
		  
		$resitLink      = "coursemanager/Resit/".$student_id."/".$taskId."/".$course_id."/".$pageContentModified[$slPageId]['idcourse_section_pages']."/".$unit_id."/".$slPageId."/".$ref;
        $resitFree      = 1;
      }

      if(in_array($taskId, array(2510,2511,2512,2513,2514))){
      	 $buttonText     = $this->user_model->translate_('free_resit_task');
	   	 $displayText    = $this->user_model->translate_('one_more_chance_resit_free');
		 $resitLink      = "coursemanager/Resit/".$student_id."/".$taskId."/".$course_id."/".$pageContentModified[$slPageId]['idcourse_section_pages']."/".$unit_id."/".$slPageId."/".$ref;
         $resitFree      = 1;
      }
      $resitdetails=array("buttonText"=>$buttonText,"displayText"=>$displayText,"resitFree"=>$resitFree,"resitOption"=>1,"resitLink"=>$resitLink);
	 
    }
      //the marks obtained by user in a particular unit in a course
      $marksDetails=$this->user_model->getUnitMarksForTasks($student_id,$unit_id,$course_id);  
      $punctuation   = '';
      if($taskId!=''){
        $punctuationArr  = $this->user_model->getuserTestDetails($student_id,$taskId,$course_id);
        if(count($punctuationArr)>0){                     
          $ttQstion =      $punctuationArr[0]->total_questions;
          $ttAnswr  =      $punctuationArr[0]->total_marks;
          $punctuation = $ttAnswr."/".$ttQstion;
        }
      }
      if(!empty($marksDetails)) {
        $totalMarks     = $marksDetails['totalMarks'];
        $totalQuestions = $marksDetails['totalQuestions'];
        $markPerc=@($totalMarks/$totalQuestions)*100;
        if($markPerc!=''){
          $percentage=@round($markPerc,2);
          if($percentage>=55){
            $unitPassed=1;
          } 
          else {
            $unitPassed=0;
          }
        }
      } 
    }
	if(isset($unitComplete)&&isset($unitPassed)){
    $marksobtained=array("unitComplete"=>$unitComplete,"unitPassed"=>$unitPassed,"percentage"=>$percentage,"punctuation"=>$punctuation);
  }
  elseif(in_array($taskId, array(2510,2511,2512,2513,2514))){
  	$marksobtained=array("unitComplete"=>0,"unitPassed"=>0,"percentage"=>0,"punctuation"=>'0/0');
  }
  
   
    if($taskId!=''){
      $testDetails=$this->user_model->gettaskdetails($taskId);
	 /* if($this->session->userdata['ip_address'] == '117.242.194.195')
		{
	  		echo "<pre>";print_r($testDetails);exit;
		}*/
      $content['userTestDetails']=$userTestDetails=$this->user_model->getuserTestDetails($student_id,$taskId,$course_id,$pageContentModified[$slPageId]['idcourse_section_pages']);

      $content['template_id']=$template_id=$testDetails[0]->template_id;
      $content['test_top_desc']=$testDetails[0]->test_top_desc;
      $content['test_bot_desc']=$testDetails[0]->test_bot_desc;
      if(count($testDetails)>=1) {
        if($testDetails[0]->template_id!=6) {
          if(count($content['userTestDetails'])>1) {
            
          } 
          else {
            switch ($testDetails[0]->template_id) {
              case 1:$content['taskdetails']= $this->task_1details($student_id,$taskId,$course_id,$pageContentModified[$slPageId]['idcourse_section_pages']);
                break;
              case 2:$content['taskdetails']= $this->task_2details($taskId);
                break;
              case 3:$content['taskdetails']= $this->task_3details($student_id,$taskId,$course_id,$pageContentModified[$slPageId]['idcourse_section_pages']);
                break;
              case 4:$content['taskdetails']= $this->task_4details($student_id,$taskId,$course_id,$pageContentModified[$slPageId]['idcourse_section_pages']);
                break;
              case 5:$content['taskdetails']= $this->task_5details($taskId);
                break;
              case 6:$content['taskdetails']= $this->task_6details($taskId);
                break;
              case 7:$content['taskdetails']= $this->task_7details($student_id,$taskId,$course_id,$pageContentModified[$slPageId]['idcourse_section_pages']);
                break;
              case 8:$content['taskdetails']= $this->task_8details($student_id,$taskId,$course_id,$pageContentModified[$slPageId]['idcourse_section_pages']);
                break;
              case 9:$content['taskdetails']= $this->task_9details($student_id,$taskId,$course_id,$pageContentModified[$slPageId]['idcourse_section_pages']);
                break;
              case 10:$content['taskdetails']= $this->task_10details($student_id,$taskId,$course_id,$pageContentModified[$slPageId]['idcourse_section_pages']);
                break;
              case 11:$content['taskdetails']= $this->task_11details($student_id,$taskId,$course_id,$pageContentModified[$slPageId]['idcourse_section_pages']);
                break;
              case 12:$content['taskdetails']= $this->task_12details($student_id,$taskId,$course_id,$pageContentModified[$slPageId]['idcourse_section_pages']);
                break;
              case 13:$content['taskdetails']= $this->task_13details($student_id,$taskId,$course_id,$pageContentModified[$slPageId]['idcourse_section_pages']);
                break;
              case 14:$content['taskdetails']= $this->task_14details($taskId);
                break;
              case 15:$content['taskdetails']= $this->task_15details($taskId);
                break;
              case 16:$content['taskdetails']= $this->task_16details($taskId);
                break;
              case 17:$content['taskdetails']=$this->task_17details($student_id,$taskId,$course_id,$pageContentModified[$slPageId]['idcourse_section_pages']);
                break;
              case 19:$content['taskdetails']= $this->task_19details($student_id,$taskId,$course_id,$pageContentModified[$slPageId]['idcourse_section_pages']);
                break;
              case 20:$content['taskdetails']= $this->task_20details($student_id,$taskId,$course_id,$pageContentModified[$slPageId]['idcourse_section_pages']);
                break;
              default:
                # code...
                break;
            }
          }  
          
        }
      }
    }
	//echo "<pre>";print_r($content['taskdetails']);exit;
      $statusArr = array(
                "user_id"=>$student_id,
                "course_pages"=>$pageContentModified[$slPageId]['idcourse_section_pages'],
                "unit_id"=>$unit_id,
                "course_id"=>$course_id
                );
	  
    $content['saveStatus'] = $this->user_model->checkPageSaved($statusArr);
	
    if($_POST){
	/*if($this->input->ip_address()=="122.174.219.206")
	{
	echo "<pre>";print_r($_POST);exit;	
	}*/
      $current_url['resume_link'] = current_url();
     // $this->user_model->updateCourse_students($student_id,$course_id,$current_url);
      $submit=$this->input->post('submitTrue');
      $hd_pageType=$this->input->post('hd_pageType');
      $qstCount=$this->input->post('qstCount');
      if($hd_pageType=='task' && $submit==1){
        //echo($template_id);
        if($template_id!=6){
			//echo $pageContentModified[$slPageId]['idcourse_section_pages'];exit;
          $userTestDetails=$this->user_model->getuserTestDetails($student_id,$taskId,$course_id,$pageContentModified[$slPageId]['idcourse_section_pages']);
         // echo count($userTestDetails);
         // echo "<pre>";
         // print_r($userTestDetails);
         // die();
          if(count($userTestDetails)<=0) {
            $correctCount     = 0;
            $insertArray1     = array("user_id"=>$student_id,"task_id"=>$taskId,"page_id"=>$pageContentModified[$slPageId]['idcourse_section_pages'],"total_questions"=>$qstCount,"course_id"=>$course_id);
            $this->user_model->insertQuerys("student_scores",$insertArray1);
            $insertArray1['idtask_evaluation']=$idtask_evaluation=$this->db->insert_id();//id of student_scores table
            $insertArray1['created_on_time'] = date("Y-m-d H:i:s");

           // $this->user_model->insertQuerys("dup_task_evaluation",$insertArray1);
            
			if($template_id == 7 || $template_id == 8 || $template_id == 17 ||$template_id == 19 || $template_id == 20){
              for($i=0;$i<$qstCount;$i++) {
                //$questionId         = $_POST['questionId'.$i];
                $userSelectedAnswerTxt  = trim($this->input->post('user_answer'.$i));
                $userSelectColor        = $this->input->post('user_color'.$i);
                $UserQuestionAnswerArr  = explode(",",$userSelectedAnswerTxt);
                $userSelectedQuestionId         = $UserQuestionAnswerArr[0];//questionId val;
                $userSelectedAnswer             = $UserQuestionAnswerArr[1];//correctAnsweval;
                $userSelectedPosition           = $UserQuestionAnswerArr[2];//user Selected poition;
                if(trim($userSelectedQuestionId)==trim($userSelectedAnswer)){
                  $correct=1;
                  $correctCount++;
                }
                else {
                  $correct=0;
                }
			$answerOriginal  = $userSelectedQuestionId;
			$answer          = $userSelectedAnswer; 
			$insertArray2 = array("question_id"=>$userSelectedQuestionId,"user_answer"=>$answer,"is_correct"=>$correct,"score_id"=>$idtask_evaluation);
                $this->user_model->insertQuerys("student_answer_details",$insertArray2);
                $insertArray2['idtask_evaluation_details']=$this->db->insert_id();
				
                $insertArray2['created_on_time'] = date("Y-m-d H:i:s");
                $insertArrayClick = array("question_id"=>$userSelectedQuestionId,"user_id"=>$student_id,"top_bottom"=>$userSelectedPosition,"color"=>$userSelectColor,"task_id"=>$taskId);
                $this->user_model->insertQuerys("radio_selected_from",$insertArrayClick);
                //$this->user_model->insertQuerys("dup_task_evaluation_details",$insertArray2);
              }
            }
            else if($template_id == 12)
            {
              $correctCount1 =0;
              $radioButtonDetails=$this->user_model->getDressUpQues($taskId);
              for($i=0;$i<1;$i++){
                $k = 0;
                $questionId  = $radioButtonDetails[$i]->id;
                for($t=1;$t<=2;$t++){
                  $correct1 =0;
                  $optionDetails=$this->user_model->getDressUpOptions($questionId,$t);
                  $answerOptId = $optionDetails[0]->id;
                  $userOptionId = $this->input->post('checked_'.$t.'_radio');
                  if($answerOptId == $userOptionId){
                    $correct1 =1;
                  }
                  $userOption = $t.'_'.$userOptionId;
                  $insertArray2 = array("question_id"=>$questionId,"user_answer"=>$userOption,"is_correct"=>$correct1,"score_id"=>$idtask_evaluation);
                  $this->user_model->insertQuerys("student_answer_details",$insertArray2);
                  $insertArray2['idtask_evaluation_details']=$this->db->insert_id();
                  $insertArray2['created_on_time'] = date("Y-m-d H:i:s");
                  $correctCount1 = $correctCount1+$correct1;
                 // $this->user_model->insertQuerys("dup_task_evaluation_details",$insertArray2);
                }
              }
              $correctCount = $correctCount+$correctCount1;
              $qstCount     =   2;
              //include('courseTemplate_12.php');
            }
            else if($template_id == 13)
            { 
              $correctCount = 0;
              $checkedBox   = $this->input->post('checked_box');
              if($checkedBox !=''){
                $insertArrayCheck = array("student_id"=>$student_id,"task_id"=>$taskId,"course_id"=>$course_id,'checkedRadio'=>$checkedBox);
                $this->user_model->insertQuerys("combo_checked_radio",$insertArrayCheck);
              }
              $radioButtonDetails  =$this->user_model->getMultiplebox_questions($taskId);
              for($i=0;$i<count($radioButtonDetails);$i++){
                $k = 0;
                $questionId  = $radioButtonDetails[$i]->id;
                $answerOpt=$this->user_model->getMultiplebox_answers($questionId);
                for($j=0;$j<count($answerOpt);$j++){
					$correct1 =0;
                  $k = $k+1;
                  $boxClicked  = $this->input->post('user_opt'.$k);
				  
				  if($boxClicked==1)
				  $userOption = $answerOpt[$j]->option_id;
				  else
				  $userOption = 0;
				  
				 // echo $userOption;
				  
                  $userClick  = $this->input->post('user_click'.$k);

				  $is_correct = $answerOpt[$j]->is_correct;
                  $originalAns = $answerOpt[$j]->answer;
                  //if(trim($userOption)==trim($originalAns) && $userClick == 1)
				  if($is_correct==1 && $userClick == 1)
				  {
                    $correct1=1;
                    $correctCount++;
                  }
                 // else if(trim($userOption)!=trim($originalAns) && $userClick == 1)
				 else if($is_correct!=1 && $userClick == 1)
				 {
                    $correct1=0;
                   // $correctCount = $correctCount-1;
                  }
                  $insertArray2 = array("question_id"=>$questionId,"user_answer"=>$userOption,"is_correct"=>$correct1,"score_id"=>$idtask_evaluation);
                  $this->user_model->insertQuerys("student_answer_details",$insertArray2);
                  $insertArray2['idtask_evaluation_details']=$this->db->insert_id();
                  $insertArray2['created_on_time'] = date("Y-m-d H:i:s");
                 // $this->user_model->insertQuerys("dup_task_evaluation_details",$insertArray2);
                }
              }
              //include('course_template13.php');
            }
            else if($template_id == 14)
            {
              //include('courseTemplate_14.php');
            }
            else if($template_id == 15)
            {
              //include('courseTemplate_15.php');
            }
            else if($template_id == 16)
            {
              //include('courseTemplate_16.php');
            }
            else if($template_id == 11)
            {
				//echo "<pre>";print_r($_POST);exit;
            }
            else{
				
				//echo "<pre>";print_r($_POST);
              for($i=0;$i<$qstCount;$i++) {
                $answerOriginal  = stripslashes(stripslashes(html_entity_decode($this->input->post('answerOriginal'.$i),ENT_QUOTES, "UTF-8")));
                if($this->input->post('drag_n_drop')=='yes')
                  $answer          = stripslashes(stripslashes(html_entity_decode($this->input->post('drop10'.$i.'hid'),ENT_QUOTES, "UTF-8")));
                else
                  $answer          = stripslashes(stripslashes(html_entity_decode($this->input->post('answer'.$i),ENT_QUOTES, "UTF-8")));
                $questionId      = $this->input->post('questionId'.$i);
                if(trim($answerOriginal)==trim($answer)){
                  $correct=1;
                  $correctCount++;
                }
                else {
                  $correct=0;
                }
                $answerOriginal  = addslashes($this->input->post('answerOriginal'.$i));
				 if($this->input->post('drag_n_drop')=='yes'){
     			 $answer          = addslashes($this->input->post('drop10'.$i.'hid'));}
    			else{
                $answer          = addslashes($this->input->post('answer'.$i));}
			
                $insertArray2 = array("question_id"=>$questionId,"user_answer"=>$answer,"is_correct"=>$correct,"score_id"=>$idtask_evaluation); 
				//echo "<pre>";print_r($insertArray2);exit;
                $this->user_model->insertQuerys("student_answer_details",$insertArray2);
                $insertArray2['idtask_evaluation_details']=$this->db->insert_id();
                $insertArray2['created_on_time'] = date("Y-m-d H:i:s");
               // $this->user_model->insertQuerys("dup_task_evaluation_details",$insertArray2);
              }
            }
            $insertArray3  =  array("total_marks"=>$correctCount,"total_questions"=>$qstCount);
            $this->user_model->update_TaskEvaluation('student_scores',$insertArray3,$idtask_evaluation);
            //$this->user_model->update_TaskEvaluation('dup_task_evaluation',$insertArray3,$idtask_evaluation);
          }
        }
		
        $courseprogress['unit_id']      = $this->input->post('save_unitId');
        $courseprogress['course_pages'] = $this->input->post('save_idcourse_section_pages');
        $insertprogress['user_id']      = $courseprogress['user_id']   =$student_id;
      	$insertprogress['course_id']    = $courseprogress['course_id'] =$course_id;
		$insertprogress['resume_link']  = str_replace(base_url(),'',$current_url['resume_link']);
		//$insertprogress['course_sections_idcourse_sections']=$section_id; 
        $this->user_model->updateStudentCourseProgress($courseprogress);
        //$this->user_model->updateStudentCoursePage($insertprogress);
		 $this->user_model->updateStudentCoursePage($insertprogress);
		 
		// $auto_mail=$this->user_model->check_auto_mail_active_or_not($student_id);
		// if($auto_mail=="1"){
		 if(!$this->user_model->check_completion_date_added($student_id,$course_id))
		 {		
			$course_progress_array = $this->get_student_progress($course_id);	
//			if($course_progress_array['progressPercnt']==100 )
			if($course_progress_array['progressPercnt']==100 && $course_progress_array['course_passed']==1)
			{	
						
				$this->apply_certificate($course_id,1); // 1 - for getting controll back here
				
							
				/*$today = date("Y-m-d");
				$update_array['completion_date'] =$today;	
				$course_status = $this->user_model->get_student_course_status($course_id,$student_id); 	
				
				if($course_status=='1')
				{
					$update_array['course_status']='4';
				}
										
				$this->user_model->update_student_enrollments($course_id,$student_id,$update_array);	*/		
			}	  
		 }
		// }
        if ($slPageId<(count($pageContentModified)-1) ) {
          $slPageId = $slPageId + 1;
		  redirect('/coursemanager/coursemodules/cour_id/'.$course_id.'/unit_id/'.$unit_id.'/ref/'.$ref.'/slPageId/'.$slPageId, 'refresh');
        }
		else
		{
			$content['endPage']=1;
		}
		
      }

      elseif($this->input->post('hd_pageType') != "task"){
		 
         $courseprogress['unit_id']      = $this->input->post('save_unitId');
        $courseprogress['course_pages']  = $this->input->post('save_idcourse_section_pages');
        $insertprogress['user_id']       = $courseprogress['user_id']   =$student_id;
      	$insertprogress['course_id']     = $courseprogress['course_id'] =$course_id;
		$insertprogress['resume_link']   = str_replace(base_url(),'',$current_url['resume_link']);
		//$insertprogress['course_sections_idcourse_sections']=$section_id; 
        $this->user_model->updateStudentCourseProgress($courseprogress);
        //$this->user_model->updateStudentCoursePage($insertprogress);
		 $this->user_model->updateStudentCoursePage($insertprogress);
		 //$auto_mail=$this->user_model->check_auto_mail_active_or_not($student_id);
		 //if($auto_mail=="1"){
			$course_progress_array = $this->get_student_progress($course_id);	

			/*echo "<pre>";
			print_r($course_progress_array);
			exit;*/
		 if(!$this->user_model->check_completion_date_added($student_id,$course_id))
		 {		
			if($course_progress_array['progressPercnt']==100 && $course_progress_array['course_passed']==1)
			{
				
				$this->apply_certificate($course_id,1); // 1 - for getting controll back here
				
								
				/*$today = date("Y-m-d");
				$update_array['completion_date'] =$today;	
				$course_status = $this->user_model->get_student_course_status($course_id,$student_id);
				
				if($course_status=='1')
				{
					$update_array['course_status']='2';
				}			
				$this->user_model->update_student_enrollments($course_id,$student_id,$update_array);*/			
			}	  
		 }
		 //}
		
		
        if ($slPageId<(count($pageContentModified)-1) ) {
          $slPageId = $slPageId + 1;
		 
		   redirect('/coursemanager/coursemodules/cour_id/'.$course_id.'/unit_id/'.$unit_id.'/ref/'.$ref.'/slPageId/'.$slPageId, 'refresh');
        }
		else
		{
			$content['endPage']=1;
		}
		
		
       
      } 
      elseif($this->input->post('submitTrue')==0){
        $content['err_flag']=1;
      }
// here ends the post section
    }
    $che_res= $this->user_model->translate_('check_result');
    $puntuation_label=$this->user_model->translate_('puntuation');
	
	$student_course_status = $this->user_model->get_student_course_status($course_id,$student_id);
	if($student_course_status==0)
	{
		$dateNow=date('Y-m-d');
		$user_voucher = $this->user_model->getVoucher_user_course($student_id,$course_id);
		
		if($user_voucher!=false)
		{
			$expirityDate = $this->user_model->findExpirityDate($course_id,$dateNow,$user_voucher);
		}
		else
		{
			$expirityDate = $this->user_model->findExpirityDate($course_id,$dateNow);
		}
		
		
	   $extension_prepaid_id = false;
	   $package_subscription_details = $this->package_model->get_package_sucbcriptions_user($student_id,$course_id);		
	   if(!empty($package_subscription_details))
	   {
		$package_sub_id = $package_subscription_details[0]->id;
		$payment_id = $package_subscription_details[0]->payment_id;
		$extension_prepaid_id = $this->package_model->get_package_puchases_by_product_type('extension',$student_id,$course_id,$package_sub_id);
			if($extension_prepaid_id)
			{
				$extension_id = $this->user_model->get_extension_id($extension_prepaid_id);					
				$extension_period = $this->user_model->get_extension_details($extension_id);
				foreach($extension_period as $key =>$row)
				{
					$period = $row->extension_days;
				}
				
				/* If Included add extension period to the validity of added course */
				
				 $expirityDate=date('Y-m-d', strtotime($expirityDate. ' + '.$period.' days'));	
				 				 
				
			}
			
		
		}
		
		
		$upload_ext_data=array(
		"course_status"=>'1',// change status to Studying
		"date_expiry"=>$expirityDate,
		"start_date"=>date("Y-m-d")); //setting up course expirity date
		
		$this->user_model->update_student_enrollments($course_id,$student_id,$upload_ext_data);
		if($extension_prepaid_id)
		{
			$pak_sub_array = array('status'=>0);
			$this->package_model->update_package_subscription_details($student_id,$course_id,$package_sub_id,'extension',$pak_sub_array);
			
			$insert_data=array("user_id"=>$student_id,"course_id"=>$course_id,"type"=>'extension',"date_applied"=>$dateNow,"product_id"=>$extension_prepaid_id,"payment_id"=>$payment_id);	 
				$this->user_model->insertQuerys("user_subscriptions",$insert_data);
				
		}
	}
	
	
    if(isset($resitdetails))
      $content['resitdetails']=$resitdetails;
    if(isset($marksobtained))
    $content['marksobtained']=$marksobtained;
	
    $content['check_result']=$che_res;
    $content['puntuation_label']=$puntuation_label;
    $content['sectionType']=$pageType;
	$content['pageGroup']= $pageGroup;
    $content['cour_id']=$course_id;
    $content['unit_id']=$unit_id;
    $content['slPageId']=$slPageId;
    $content['taskId']=$taskId;
    $content['pageContentModified']=$pageContentModified;
    $content['unitName']=$unitName;
    $content['coursename']=$coursename;
	$content['stud_id']=$student_id;
	
	$course_progress_array = $this->get_student_progress($course_id);
	$data['course_progress_array']  = $course_progress_array;
	
	$data['translate'] = $this->tr_common;
	$data['tr_home'] = $this->user_model->translate_('Home');
	$data['tr_task_page'] = $this->user_model->translate_('task_page');
	$data['tr_save_continue'] = $this->user_model->translate_('save_continue');
	$data['tr_study'] =$this->user_model->translate_('Study');
	$data['tr_exercise'] =$this->user_model->translate_('Exercices');
	$data['tr_exam'] =$this->user_model->translate_('Exams');
	$data['tr_my_course'] =$this->user_model->translate_('my_course');
	$data['css_lang']=$this->session->userdata('language');

	if(isset($content['endPage']) && $content['endPage']==1){
		$this->session->set_flashdata('end_page',1);
		redirect('/coursemanager/coursemodules/cour_id/'.$course_id.'/unit_id/'.$unit_id.'/ref/'.$ref.'/slPageId/'.$slPageId, 'refresh');
	}
	
	
	$data['view'] = 'coursepage';	
	
	
    $data['content'] = $content;
	
    $this->load->view('user/template_inner_coursepage',$data);
  }
  function task_1details($student_id,$taskId,$course_id,$idcourse_section_pages){
    $rd_task['rd_questionlist']=$this->user_model->getRd_questions($taskId);
    $rd_task['rd_optionlist']=$this->user_model->getRd_optionlist($taskId);
	
	
	
	//echo "<pre>";print_r($rd_task);
    foreach ( $rd_task['rd_questionlist'] as $key => $value) {
      $rd_task['rd_answerlist'][$key]=$value->answer_desc;
      $rd_task['testResults'][$key]=$this->user_model->getTestDetails($value->id,$student_id,$taskId,$idcourse_section_pages,$course_id);

    }
    return $rd_task;
  }
  function task_2details($taskId){
    return $imgtask['img_task_questionslist']=$this->user_model->getImgTask_questions($taskId);
    }
  function task_3details($student_id,$taskId,$course_id,$idcourse_section_pages){
    $gapfill_task['gapfill_questionlist']=$this->user_model->getGafill_questions($taskId);
    foreach ( $gapfill_task['gapfill_questionlist'] as $key => $value) {
      $ques_id=$value->id;
      $gapfill_task['testResults'][$key]=$this->user_model->getTestDetails($ques_id,$student_id,$taskId,$idcourse_section_pages,$course_id);

    }
    return $gapfill_task;
  }
  function task_4details($student_id,$taskId,$course_id,$idcourse_section_pages){

    $dropTaskArr['dropquestionlist']=$this->user_model->getDropTask_questions($taskId);
    foreach ($dropTaskArr['dropquestionlist'] as $key => $value) {
      $ques_id=$value->id;
      $dropTaskArr['dropoptionlist'][$key]=$this->user_model->getDropOptions($ques_id);
      $dropTaskArr['testResults'][$key]=$this->user_model->getTestDetails($ques_id,$student_id,$taskId,$idcourse_section_pages,$course_id);

    }
    return $dropTaskArr;
  }
  function task_5details($taskId){
    $matchTaskArr['matchtaskquestionlist']=$this->user_model->getMatchTask_qusetions($taskId);
    return $matchTaskArr;
  }
  function task_6details($taskId){
    
  }
  function task_7details($student_id,$taskId,$course_id,$idcourse_section_pages){
     $radioArr['radioquestionlist']=$this->user_model->getRadioImage_questions($taskId);
    $radioArr['radioimage']=$this->user_model->getRadioImage($taskId);
    foreach ($radioArr['radioquestionlist'] as $key => $value) {
      $ques_id=$value->id;
      $radioArr['testResults'][$key]=$this->user_model->getTestDetails($ques_id,$student_id,$taskId,$idcourse_section_pages,$course_id);
      $radioArr['radioselected'][$key]=$this->user_model->getRadioSelected($student_id,$taskId,$ques_id);

    }
    return $radioArr;
  
    
  }
  function task_8details($student_id,$taskId,$course_id,$idcourse_section_pages){
    $radioArr['radioquestionlist']=$this->user_model->getRadioImage_questions($taskId);
    $radioArr['radioimage']=$this->user_model->getRadioImage($taskId);
	//echo "<pre>";print_r( $radioArr['radioquestionlist']);print_r( $radioArr['radioimage']);
    foreach ($radioArr['radioquestionlist'] as $key => $value) {
      $ques_id=$value->id;
      $radioArr['testResults'][$key]=$this->user_model->getTestDetails($ques_id,$student_id,$taskId,$idcourse_section_pages,$course_id);
      $radioArr['radioselected'][$key]=$this->user_model->getRadioSelected($student_id,$taskId,$ques_id);

    }
    return $radioArr;
  
  }
  function task_9details($student_id,$taskId,$course_id,$idcourse_section_pages){
    $multipletask['multiplequestionlist']=$this->user_model->getMultipleTask_questions($taskId);
	$k=0;
    foreach ($multipletask['multiplequestionlist'] as $key => $value) {
      $ques_id=$value->id;
      $multipletask['multipleoptionlist'][$k]=$this->user_model->getMultipleTask_options($ques_id);
      $multipletask['testResults'][$k]=$this->user_model->getTestDetails($ques_id,$student_id,$taskId,$idcourse_section_pages,$course_id);
      $k++;
    }
    
    return $multipletask;
  }
  function task_10details($student_id,$taskId,$course_id,$idcourse_section_pages){
    $imageoptionTaskArr['imageoptionquestionlist']=$this->user_model->getImageoptionTask_questions($taskId);
    foreach ($imageoptionTaskArr['imageoptionquestionlist'] as $key => $value) {
      $ques_id=$value->id;
      $imageoptionTaskArr['imageoptionlist']=$this->user_model->getImageOptions($ques_id);
      $imageoptionTaskArr['testResults']=$this->user_model->getTestDetails($ques_id,$student_id,$taskId,$idcourse_section_pages,$course_id);

    }
    return $imageoptionTaskArr;
  }
  function task_11details($student_id,$taskId,$course_id,$idcourse_section_pages){
	  
	    $radioSelCombo['comboRadio_questions']=$this->user_model->getRadioCombo_questions($taskId);
    foreach ($radioSelCombo['comboRadio_questions'] as $key => $value) {
      $ques_id=$value->id;
      $radioSelCombo['comboRadio_options']=$this->user_model->getRadioCombo_options($ques_id);
      $radioSelCombo['testResults1']=$this->user_model->getTestDetails($ques_id,$student_id,$taskId,$idcourse_section_pages,$course_id);
	}
	
	 $radioSelCombo['comboDrop_questions']=$this->user_model->getDropCombo_questions($taskId);
    foreach ($radioSelCombo['comboDrop_questions'] as $key => $value) {
      $ques_id=$value->id;
      $radioSelCombo['comboDrop_options']=$this->user_model->getDropCombo_options($ques_id);
	  $radioSelCombo['testResults2']=$this->user_model->getTestDetails($ques_id,$student_id,$taskId,$idcourse_section_pages,$course_id);
	}
	return($radioSelCombo);
  }
  function task_12details($student_id,$taskId,$course_id,$idcourse_section_pages){
    $dressradioTaskArr['dressupquestionlist']=$this->user_model->getDressupRadioTask_questions($taskId);
    foreach ($dressradioTaskArr['dressupquestionlist'] as $key => $value) {
      $ques_id=$value->id;
      $dressradioTaskArr['dressupanswerlist1']=$this->user_model->getDressupRadioTask_answerstype1($ques_id);
      $dressradioTaskArr['dressupanswerlist2']=$this->user_model->getDressupRadioTask_answerstype2($ques_id);
      $dressradioTaskArr['testResults']=$this->user_model->getTestDetails($ques_id,$student_id,$taskId,$idcourse_section_pages,$course_id);
      $dressradioTaskArr['testDetails1_0']=$this->user_model->getTest_Details($ques_id,$dressradioTaskArr['dressupanswerlist1'][0]->id,1,$student_id,$taskId);
      $dressradioTaskArr['testDetails1_1']=$this->user_model->getTest_Details($ques_id,$dressradioTaskArr['dressupanswerlist1'][1]->id,1,$student_id,$taskId);
      $dressradioTaskArr['testDetails2_0']=$this->user_model->getTest_Details($ques_id,$dressradioTaskArr['dressupanswerlist2'][0]->id,2,$student_id,$taskId);
      $dressradioTaskArr['testDetails2_1']=$this->user_model->getTest_Details($ques_id,$dressradioTaskArr['dressupanswerlist2'][1]->id,2,$student_id,$taskId);
      $dressradioTaskArr['correct_option1']=$this->user_model->getCorrectDressupoption($ques_id,1);
      $dressradioTaskArr['correct_option2']=$this->user_model->getCorrectDressupoption($ques_id,2);
      $dressradioTaskArr['answercrt1']=$this->user_model->getTest_Details($ques_id,$dressradioTaskArr['correct_option1'][0]->id,1,$student_id,$taskId);
      $dressradioTaskArr['answercrt2']=$this->user_model->getTest_Details($ques_id,$dressradioTaskArr['correct_option2'][0]->id,2,$student_id,$taskId);

    }
    
    return $dressradioTaskArr;
  }
  function task_13details($student_id,$taskId,$course_id,$idcourse_section_pages){
    $MultipleboxArr['multipleboxquestionlist']=$this->user_model->getMultiplebox_questions($taskId);
    $MultipleboxArr['checkedBoxRes']=$this->user_model->getComboChekedRadio($student_id,$taskId);
    foreach ($MultipleboxArr['multipleboxquestionlist'] as $key => $value) {
      $ques_id=$value->id;
      $MultipleboxArr['multipleboxanswerlist']=$this->user_model->getMultiplebox_answers($ques_id);
      $MultipleboxArr['testResults']=$this->user_model->getTestDetails($ques_id,$student_id,$taskId,$idcourse_section_pages,$course_id);

    }
    
    
    return $MultipleboxArr;
  }
  function task_14details($taskId){
    
  }
  function task_15details($taskId){
    
  }
  function task_16details($taskId){
    
  }
  function task_17details($student_id,$taskId,$course_id,$idcourse_section_pages){
    $radioArr['radioquestionlist']=$this->user_model->getRadioImage_questions($taskId);
    $radioArr['radioimage']=$this->user_model->getRadioImage($taskId);
    foreach ($radioArr['radioquestionlist'] as $key => $value) {
      $ques_id=$value->id;
      $radioArr['testResults'][$key]=$this->user_model->getTestDetails($ques_id,$student_id,$taskId,$idcourse_section_pages,$course_id);
      $radioArr['radioselected'][$key]=$this->user_model->getRadioSelected($student_id,$taskId,$ques_id);
    }

    return $radioArr;
  }
  function task_19details($student_id,$taskId,$course_id,$idcourse_section_pages){
    $radioArr['radioquestionlist']=$this->user_model->getRadioImage_questions($taskId);
    $radioArr['radioimage']=$this->user_model->getRadioImage($taskId);
    foreach ($radioArr['radioquestionlist'] as $key => $value) {
      $ques_id=$value->id;
      $radioArr['testResults'][$key]=$this->user_model->getTestDetails($ques_id,$student_id,$taskId,$idcourse_section_pages,$course_id);
      $radioArr['userclicked'][$key]=$this->user_model->getUserClickedPosition_new($student_id,$taskId,$ques_id);

    }
    return $radioArr;
  }
  function task_20details($student_id,$taskId,$course_id,$idcourse_section_pages){  
    $eyebrow['eyebrow_img']=$this->user_model->getEyebrowImage($taskId);
    $eyebrow['eyebrow_question']=$this->user_model->getEyebrowQuestion();
    foreach ($eyebrow['eyebrow_img'] as $key => $value) {
      $img_id=$value->id;
      $eyebrow['eyebrow_question1']=$this->user_model->getEyebrowQuestion($img_id);
    }
    foreach ($eyebrow['eyebrow_question1'] as $key => $value) {
      $ques_id=$value->id;
      $eyebrow['testResults'][$key]=$this->user_model->getTestDetails($ques_id,$student_id,$taskId,$idcourse_section_pages,$course_id);
      $eyebrow['radioselected'][$key]=$this->user_model->getRadioSelected($student_id,$taskId,$ques_id);
    }
    return $eyebrow;
  }
  
  function Resit($student_id,$taskId,$course_id,$coursePageId,$unit_id,$slPage,$ref=2)
	{
		 $oldTestDetails=$this->user_model->getuserTestDetails($student_id,$taskId,$course_id,$coursePageId);
		 
		 $testResultsRepeated_free = $this->user_model->getuserTestRepeatDetails($student_id,$taskId,$course_id,$coursePageId,'free_resit');
		
		$testResultsRepeated_paid = $this->user_model->getuserTestRepeatDetails($student_id,$taskId,$course_id,$coursePageId,'paid_resit');	
 
  $resitDetails['resit_date'] = date("Y-m-d");
  
     if(count($testResultsRepeated_paid)>=1){
       $resitDetails['resit_type']="free_resit";
		$resitDetails['user_id']=$student_id;
		$resitDetails['course_id']=$course_id;
		$resitDetails['task_id']=$taskId;
		$resitDetails['page_id']=$coursePageId;
		$resitDetails['total_questions']= $oldTestDetails[0]->total_questions;
		$resitDetails['total_marks']=$oldTestDetails[0]->total_marks;
      } 
      else if(count($testResultsRepeated_free)>=1)
	  {
        redirect("coursemanager/ResitConfirm/".$student_id."/".$taskId."/".$course_id."/".$coursePageId."/".$unit_id."/".$slPage."/".$ref,'refresh');
      } 
      else if(count($testResultsRepeated_free)<=0 && count($testResultsRepeated_paid)<=0){
		  $resitDetails['resit_type']="free_resit";
		$resitDetails['user_id']=$student_id;
		$resitDetails['course_id']=$course_id;
		$resitDetails['task_id']=$taskId;
		$resitDetails['page_id']=$coursePageId;
		$resitDetails['total_questions']= $oldTestDetails[0]->total_questions;
		$resitDetails['total_marks']=$oldTestDetails[0]->total_marks;
		  
      }
		 
		 //----------------------------------------------------------------------------------------
		      
		$newResitId = $this->user_model->addUserResit($resitDetails);
		$resitDetails['unit_id'] = $unit_id;
		$course_recods = $this->user_model->sectionpageid($resitDetails);
	
		$unserialForm = unserialize($course_recods[0]->course_pages);
		$pageArr = array($resitDetails['page_id']);
		$newUnserial =  array_diff($unserialForm,$pageArr);
		//print_r($newUnserial);
		$serial['course_pages'] = serialize($newUnserial);
		
		$this->user_model->updateCourseRecords($resitDetails,$serial);
		$this->db->where("id",$oldTestDetails[0]->id);
		$this->db->delete('student_scores');
		redirect(base_url()."coursemanager/coursemodules/cour_id/".$course_id."/unit_id/".$unit_id."/ref/".$ref."/slPageId/".$slPage);
		
	}
	
	
	function ResitConfirm()
	{
		
		  $product_id = $this->common_model->getProdectId("retask");
		 
		
		  $content['taskId'] 		= $this->uri->segment(4);
		  $content['course_id']	 = $this->uri->segment(5);
		  $content['coursePageId']  = $this->uri->segment(6);
		  $content['unit_id']	   = $this->uri->segment(7);
		  $content['slPage']		= $this->uri->segment(8);
		  $content['ref']		   = $this->uri->segment(9);
		  
		  $resitFeeDetails =$this->common_model->getProductFee($product_id, $this->currId);
		  $content['amount'] = $resitFeeDetails['amount'];
		  $content['product_id'] = $product_id;
		  $content['curr_id']=$this->currId;
		  $content['currency_code']=$this->currencyCode;
		  
		  $content['stud_id'] = $this->session->userdata['student_logged_in']['id'];
		  
		  $content['content']='';
		  $content['translate'] = $this->tr_common;
		  $content['view']="resit_confirm";
		  $this->load->view("user/template_inner",$content);
		  
	}
	function Resit_AfterPay()
	{
		$payment_id = $this->uri->segment(3);
		$student_id = $this->uri->segment(4);
		$taskId = $this->uri->segment(5);
		$course_id = $this->uri->segment(6);
		$coursePageId = $this->uri->segment(7);
		$unit_id = $this->uri->segment(8);
		$slPage = $this->uri->segment(9);
		$ref = $this->uri->segment(10);
		
		
		//echo "userid = ".$student_id."/ task_id = ".$taskId."/ course_id = ".$course_id."/ corse_page_id = ".$coursePageId."/unitId = ".$unit_id."/ slpageId".$slPage."/ ref = ".$ref;
		//exit;
		
		$oldTestDetails=$this->user_model->getuserTestDetails($student_id,$taskId,$course_id,$coursePageId);
		  $resitDetails['resit_date'] = date("Y-m-d");
		$resitDetails['resit_type']="paid_resit";
		$resitDetails['user_id']=$student_id;
		$resitDetails['course_id']=$course_id;
		$resitDetails['task_id']=$taskId;
		$resitDetails['page_id']=$coursePageId;
		$resitDetails['total_questions']= $oldTestDetails[0]->total_questions;
		$resitDetails['total_marks']=$oldTestDetails[0]->total_marks;
		
		$newResitId = $this->user_model->addUserResit($resitDetails);
		$resitDetails['unit_id'] = $unit_id;
		$course_recods = $this->user_model->sectionpageid($resitDetails);
		$unserialForm = unserialize($course_recods[0]->course_pages);
		$pageArr = array($resitDetails['page_id']);
		$newUnserial =  array_diff($unserialForm,$pageArr);
		//print_r($newUnserial);
		$serial['course_pages'] = serialize($newUnserial);
		
		$this->user_model->updateCourseRecords($resitDetails,$serial);
		$this->db->where("id",$oldTestDetails[0]->id);
		$this->db->delete('student_scores');
		redirect(base_url()."coursemanager/coursemodules/cour_id/".$course_id."/unit_id/".$unit_id."/ref/".$ref."/slPageId/".$slPage);
		 
		  
	}
	
	
	function check_current_password($cur_pass)
	{
		
		$user_id  = $this->session->userdata['student_logged_in']['id'];		
		
		$flag = $this->user_model->check_password($user_id,$cur_pass);		
		
		if(!$flag)
		{
			$data['inval_pass']    = $this->user_model->translate_('invalid_password'); ;		
		}
		$data['err_msg']    = $flag;	
		
		
		echo json_encode($data);  
		
		
	}
	
	
	
	function change_password()
	{
		$user_id = $this->session->userdata['student_logged_in']['id'];
		$content = array();
		
		if(isset($_POST['submit']))
		{
		
		  $studentdata  = array();
		  $current_password   = $this->input->post('cur_pass');
		  $new_password 	   = $this->input->post('new_pass');
		  $confirm_password   = $this->input->post('con_pword');
		 	  
			  /*echo "Cure pass ".$current_password;
			  echo "<br>New pass ".$new_password;
			  echo "<br>Conf pass ".$confirm_password;*/
			  
		  
		    $this->form_validation->set_rules('cur_pass', 'Old password', 'trim|required');		
			$this->form_validation->set_rules('new_pass', 'New password', 'required|min_length[6]');			
		    $this->form_validation->set_rules('con_pword', 'Password', 'required|min_length[6]');			
			
			if($this->form_validation->run())
			{	
			
				if($this->check_current_password_1($current_password))
				{
					if($new_password == $confirm_password)
					{
						$encoded_password = $this->encrypt->encode($new_password);
						
						$new_pass = array('password'=>$encoded_password);
						$this->user_model->update_student_password($user_id,$new_pass);
			 	 		$this->session->set_flashdata('message', 'Password Updated successfully');
			 	 		redirect('coursemanager/campus', 'refresh');
						
					}
				}
			 	
			}
		  
		  
		}
				
		
		
		
		
		
	
	}
	
  function check_current_password_1($curPass)
  {
  	$user_id = $this->session->userdata['student_logged_in']['id'];
	
	$result = $this->user_model->check_password($user_id, $curPass);
	
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
  
	function proof_of_enrollment()
  {
  	$content = array();
	$certificate_status =array();
	$course_id_encrypted = array();
	$course_name_array =array();
	$user_id = $this->session->userdata['student_logged_in']['id'];
	$content=$this->get_student_deatils_for_popup();
	$lang_id = $this->session->userdata('language');
	
	$enrolled_courses = $this->user_model->get_courses_student($user_id); 

	$k=0;
	foreach ($enrolled_courses as $key => $value) {	
		$course_name_array[$k] = $this->common_model->get_course_name($value->course_id); 
		$course_id_encrypted[$k] = rawurlencode($value->course_id);
		
		
		$user_subscriptions[$k]['proof_enrolment'] 	   =  0;
			
			
			$subscriptions = $this->user_model->get_user_subscriptions($user_id,$value->course_id);
			
			/*echo "<pre>";
			print_r($subscriptions);*/
			
			if(!empty($subscriptions))
			{
				
				foreach ($subscriptions as $key => $value2){
			
					
					if($value2->type == 'poe_soft')
					{
						$user_subscriptions[$k]['proof_enrolment'] 	   =  1;
					}
					else if($value2->type == 'poe_hard')
					{
						$user_subscriptions[$k]['proof_enrolment'] 	   =  1;
					}
					
				
				}
			}	
	
		
			$k++;
		}
		
	
			
			
		
		
	/*	echo "<pre>";
		print_r($course_id_encrypted);
		exit;*/
		
		 
		 
	
	$content['enrolled_courses']   = $enrolled_courses;
	$content['user_subscriptions'] = $user_subscriptions;
	
	$content['course_name_array'] = $course_name_array;
	$content['user_id']=$user_id;
	 $data['translate'] = $this->tr_common;
	$data['view'] = 'proof_enroll';   
    $data['content'] = $content;
    $this->load->view('user/course_template',$data);  
  }
  
  function campus_test()
  {
	   
	
	 if(!$this->session->userdata('student_logged_in')){
		  redirect('home');
		}		
	
	 $stud_id  = $this->session->userdata['student_logged_in']['id'];
	// $stud_id  = $this->session->userdata['student_logged_in']['id'];
  
 // $lang_id = $this->session->userdata('language');
    
   $lang_id  = $this->common_model->get_user_lang_id($stud_id);
  $sess_array1 = array('language' => $lang_id);
  $this->session->set_userdata($sess_array1);
	 
	 $this->tr_common['tr_course_progress'] = $this->user_model->translate_('course_progress');
  	 $this->tr_common['tr_more_info'] =$this->user_model->translate_('more_info');
	 $this->tr_common['tr_study_time_remaining'] =$this->user_model->translate_('study_remaining');
	 $this->tr_common['tr_days'] =$this->user_model->translate_('camp_days');
	 $this->tr_common['tr_from_file'] =$this->user_model->translate_('from_file');
	 $this->tr_common['tr_next_course'] =$this->user_model->translate_('next_course');
	   $this->tr_common['tr_my_courses']   =$this->user_model->translate_('my_courses');
		 $this->tr_common['tr_my_ebooks']   =$this->user_model->translate_('my_ebooks');
		 $this->tr_common['tr_buy_voucher']   =$this->user_model->translate_('buy_voucher');
	  $this->tr_common['tr_ebook_for'] = $this->user_model->translate_('ebook_for');
		 $this->tr_common['tr_reduced_from'] = $this->user_model->translate_('reduced_from');
		 $this->tr_common['tr_add_to_bag'] = $this->user_model->translate_('add_to_bag');
	  $this->tr_common['tr_my_marks'] =$this->user_model->translate_('my_marks_new');
	  $this->tr_common['tr_proof_enroll'] =$this->user_model->translate_('proof_enroll');
	  
	  $this->tr_common['tr_ebook_campus_head'] =$this->user_model->translate_('ebook_campus_head');
	  $this->tr_common['tr_ebook_campus_text'] =$this->user_model->translate_('ebook_campus_text');
      $this->tr_common['tr_ebook_for'] = $this->user_model->translate_('ebook_for');
	  $this->tr_common['tr_reduced_from'] = $this->user_model->translate_('reduced_from');
	  
	  $this->tr_common['tr_buy_now'] = $this->user_model->translate_('buy_now');
	  $this->tr_common['tr_download'] = $this->user_model->translate_('download');
	  $this->tr_common['tr_remove'] = $this->user_model->translate_('remove');
	  $this->tr_common['tr_total_price'] = $this->user_model->translate_('total_price');
	   $this->tr_common['tr_check_out'] = $this->user_model->translate_('check_out');
	  
	  
		
		
	 
	 
	 
	 $content=$this->get_student_deatils_for_popup();
	 
	 if(isset($_POST['pro_pic_submit']))
	 {
		 						$config['upload_path'] = 'public/user/images/profile_pic/';
								$config['allowed_types'] = 'gif|jpg|png';
								$config['max_size']	= '1000';
								//$config['max_width']  = '1024';
								//$config['max_height']  = '768';
								
								
								$this->load->library('upload', $config);
								$this->upload->initialize($config);
						
								
								if ( $this->upload->do_upload('pro_pic'))
								{
									$uploaded = array('upload_data' => $this->upload->data());
									$testdata['user_pic'] = $uploaded['upload_data']['file_name'];
									$this->user_model->update_student_details($testdata,$stud_id);
									redirect('coursemanager/campus','refresh');
									
								}
								else
								{
									
									$error['upResult'] = array('error' => $this->upload->display_errors());
									
									//$content['err_prof_pic'] = var_dump($error['upResult']);
																		
									$this->session->set_flashdata('message', $error['upResult']);
			 	 					redirect('coursemanager/campus', 'refresh');
									
									
									//redirect('coursemanager/campus','');
									//echo "err condition<pre>";var_dump($error['upResult']);
									//exit;
								}
	 }
	 $base_courses = $this->user_model->get_courses($lang_id);
	 
	 
	 $i=0;
	 $array_index_marks=0;
	
	 foreach ($base_courses as $row) {
		 $course_array[$i]['course_id'] = $row->course_id;
	 	$course_array[$i]['course_name'] = $row->course_name;

		$course_array[$i]['course_summary'] = $row->course_summary;
		//echo $row->course_id;
        $enrolled=$this->user_model->check_user_registered($stud_id,$row->course_id); // check user registered with this course 
		
		if($enrolled) // if enrollled
		{
			foreach ($enrolled as $value)
			{
				$course_progress_array[$array_index_marks] = $this->get_student_progress($value->course_id);
				$course_progress_array[$array_index_marks]['course_status_id']=$value->course_status;
				//echo $value->course_status;	
				if($value->expired==1) // course expired
				{
					
					$course_array[$i]['course_status'] = $this->user_model->translate_('expired');
					$course_array[$i]['course_status_id'] =$value->course_status;
					if($course_progress_array[$array_index_marks]['progressPercnt'] == 100)
					{
						$this->session->set_flashdata('complete_expired',"Your course has expired however you can buy a course subscription");
						$course_array[$i]['resume_link'] = 'sales/course_access_option/'.$value->course_id;
					}
					else if($course_progress_array[$array_index_marks]['progressPercnt'] < 100)
					{
						$this->session->set_flashdata('notcomplete_expired',"your course has expired....");
						$course_array[$i]['resume_link'] = 'coursemanager/extendcourse';
					}
				}
				else 
				{
				if($value->course_status==0) // course not started
				{				
					$course_array[$i]['course_status'] = $this->user_model->translate_('start');
					$course_array[$i]['course_status_id'] =$value->course_status;
					$course_array[$i]['resume_link'] = 'coursemanager/studentcourse/'.$value->course_id;
				}
				else if($value->course_status=='1') // studying
				{
					$course_array[$i]['course_status'] = $this->user_model->translate_('resume'); 
					$course_array[$i]['course_status_id'] =$value->course_status;
					$resume_link = $this->user_model->get_student_resume_link($stud_id,$value->course_id);
					//echo "<pre>";print_r($resume_link);exit;
					if(!empty($resume_link))
					{
					foreach ($resume_link as $row2)
					{
						$course_array[$i]['resume_link'] = $row2->resume_link;
					}
					}
					else
					{
						$course_array[$i]['resume_link'] = 'coursemanager/studentcourse/'.$value->course_id;
					}
					
				}
				else if($value->course_status==2) // course completed
				{
					$course_array[$i]['course_status'] = $this->user_model->translate_('status_completed'); 
					$course_array[$i]['course_status_id'] =$value->course_status;
					$course_array[$i]['resume_link'] = 'coursemanager/studentcourse/'.$value->course_id;
				}
				else if($value->course_status==3 || $value->course_status==4) // certificate applied or issued
				{
					$course_array[$i]['course_status'] =$this->user_model->translate_('status_completed'); 
					$course_array[$i]['course_status_id'] =$value->course_status;
					$course_array[$i]['resume_link'] = 'coursemanager/studentcourse/'.$value->course_id;
				}
				else if($value->course_status==5) // material access
				{
					$course_array[$i]['course_status'] =$this->user_model->translate_('status_completed'); 
					$course_array[$i]['course_status_id'] =$value->course_status;
					$course_array[$i]['resume_link'] = 'coursemanager/studentcourse/'.$value->course_id;
				}
				else if($value->course_status==6) // archived
				{
					$course_array[$i]['course_status'] =$this->user_model->translate_('archived'); 
					$course_array[$i]['course_status_id'] =$value->course_status;
					$course_array[$i]['resume_link'] = 'coursemanager/studentcourse/'.$value->course_id;
				}
				else if($value->course_status==7) // course expired
				{
					
					$course_array[$i]['course_status'] = $this->user_model->translate_('expired');
					$course_array[$i]['course_status_id'] =$value->course_status;
					if($course_progress_array[$array_index_marks]['progressPercnt'] == 100)
					{
						$this->session->set_flashdata('complete_expired',"Your course has expired however you can buy a course subscription");


						$course_array[$i]['resume_link'] = 'sales/course_access_option/'.$value->course_id;
					}
					else if($course_progress_array[$array_index_marks]['progressPercnt'] < 100)
					{
						$this->session->set_flashdata('notcomplete_expired',"your course has expired....");
						$course_array[$i]['resume_link'] = 'coursemanager/extendcourse';
					}
				}
				
				}
				$array_index_marks++;				
				$i++;
			}
		}
		
      }
	 
	  foreach ($base_courses as $row) {
		   $course_array[$i]['course_id'] = $row->course_id;
	 	$course_array[$i]['course_name'] = $row->course_name;
		$course_array[$i]['course_summary'] = $row->course_summary;
        $enrolled=$this->user_model->check_user_registered($stud_id,$row->course_id); // check user registered with this course 
	 	if(!$enrolled) // not enrolled to course
		{
			if($row->course_status==0)
				{
				$course_array[$i]['course_status_id'] = 100; // Coming soon
				$course_array[$i]['course_status']= 'Coming soon';
				$course_array[$i]['resume_link'] ='home/coursedetails/'.$row->course_id;
				}
				else
				{
				$course_array[$i]['course_status_id'] = 8; // Buy 
				$course_array[$i]['course_status']= $this->user_model->translate_('buy_now');
				$course_array[$i]['resume_link'] ='home/buy_another_course/stud_id/'.$stud_id.'/cour_id/'.$row->course_id;
				}
			$i++;
		}
	  }
		
		
		$this->load->model('ebook_model');
		$ebArray = $this->ebook_model->fetchEbookByLang($this->language);
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
				//translations
				$content['tr_trndimi_ebooks'] = $this->user_model->translate_('trendimi_ebooks');
				$content['tr_trndimi_ebooks_text'] =$this->user_model->translate_('trndimi_ebooks_text');
				$unit=1;
				if($i==1)
				{ 
				$unit='2';  
				}
				
				$prodectId[$i] = $this->common_model->getProdectId('ebooks','',$unit);	
				$ebookPrice =$this->common_model->getProductFee($prodectId[$i],$this->currId);
				$content['fake_amount'][$i] =$ebookPrice['fake_amount'];
				$content['amount'][$i] =$ebookPrice['amount'];
				//$content['currency_symbol'][$i] =$ebookPrice['currency_symbol'];
				$content['currency_id'][$i] =$ebookPrice['currency_id'];
				
				
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
		
		/*if($this->session->userdata('cart_session_id'))
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
			
		}*/
		
		$currency_id = $this->currId;
		$added_ebook_array = array();
		
		 if($this->session->userdata('cart_session_id'))
		{
			
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('cart_session_id'));
			
			//echo "Session id ".$this->session->userdata('cart_session_id');
			if(!empty($cart_main_details))
			{
				foreach($cart_main_details as $cart_main)
				{
					$data['cart_count'] = $cart_main->item_count;
					$data['cart_amount'] = $cart_main->total_cart_amount;
					$data['currency_symbol'] = $this->common_model->get_currency_symbol_from_id($cart_main->currency_id);
				}
				
				$cart_main_id = $cart_main_details[0]->id;		
				$cart_prod_type = array('ebooks','ebook_guide');
				$ebook_added_in_cart = $this->sales_model->check_product_type_exist_in_cart($cart_main_id,$cart_prod_type);
				if(!empty($ebook_added_in_cart))
				{					
				$added_ebooks = $ebook_added_in_cart[0]->selected_item_ids;						
				$added_ebook_array = explode(',',$added_ebooks);
				}			
			}
			else
			{
				$data['cart_count'] = 0;
				$data['cart_amount'] = 0;
				$data['currency_symbol'] = $this->common_model->get_currency_symbol_from_id($this->currId);
			}
		}
		else
		{
			$data['cart_count'] = 0;
			$data['cart_amount'] = 0;
			$data['currency_symbol'] = $this->common_model->get_currency_symbol_from_id($this->currId);
			
		}		
	  
	   $sess_array = array('cart_source' => '/coursemanager/campus');
		
		$this->session->set_userdata($sess_array);
		
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
		
		
		$content['ebook_array']   	     = $ebook_array;
		$content['ebook_price_details'] = $ebook_price_details;
		$content['ebook_guide_price_details'] = $ebook_guide_price_details;
		
		
		
		//$content['colour_wheel_subscription'] = $this->user_model->colour_wheel_subcribed($stud_id);
		$content['suscribedEbooks'] =$suscribedEbooks= $this->ebook_model->suscribed_ebooks($stud_id);
		
	    $content['added_ebook_array']	= $added_ebook_array;
		$content['studentDetails']=$this->user_model->get_student_details($stud_id);
		$data['content'] = $ebDetails;
		$data['lang_id']				 = $lang_id;
		$data['base_courses'] 			= $base_courses;
		$data['course_array'] 			= $course_array;	
		
		$data['course_progress_array']  = $course_progress_array;
		$data['translate'] = $this->tr_common;
		
		
		/*if($this->session->userdata['ip_address'] == '117.242.192.217')
		{
		$data['view'] 					= 'campus_test';
		}
		else
		{*/
			
			$data['view'] 					= 'campus_test';
		/*}*/
   		$data['content'] 				= $content;
		 
 		$this->load->view('user/campus_template',$data);
		
	
	  
  }
	
	function gift_campus()
	{
		$content= array();
		$data=array();
		
		$stud_id=$this->session->userdata['student_logged_in']['id'];
		$Lang_id=$this->language = $this->session->userdata('language');
		$this->tr_common['tr_course_progress'] = $this->user_model->translate_('course_progress');
  	 $this->tr_common['tr_more_info'] =$this->user_model->translate_('more_info');
	 $this->tr_common['tr_study_time_remaining'] =$this->user_model->translate_('study_remaining');
	 $this->tr_common['tr_days'] =$this->user_model->translate_('camp_days');
//	 $this->tr_common['tr_days'] =$this->user_model->translate_('days');
	 $this->tr_common['tr_next_course'] =$this->user_model->translate_('next_course');
	   
	 
	  $this->tr_common['tr_my_marks'] =$this->user_model->translate_('my_marks_new');
	  $this->tr_common['tr_proof_enroll'] =$this->user_model->translate_('proof_enroll');
	  $content=$this->get_student_deatils_for_popup();
	 $base_courses = $this->user_model->get_courses($Lang_id);
	 
	 
	 $i=0;
	 $array_index_marks=0;
	 foreach ($base_courses as $row) {
		 $course_array[$i]['course_id'] = $row->course_id;
	 	$course_array[$i]['course_name'] = $row->course_name;
		$course_array[$i]['course_summary'] = $row->course_summary;
        $enrolled=$this->user_model->check_user_registered($stud_id,$row->course_id); // check user registered with this course 
		
		if($enrolled) // if enrollled
		{
			foreach ($enrolled as $value)
			{
				$course_progress_array[$array_index_marks] = $this->get_student_progress($value->course_id);
				$course_progress_array[$array_index_marks]['course_status_id']=$value->course_status;
				//echo $value->course_status;	
				
				}
				
				
				$array_index_marks++;				
				$i++;
			}
		}
		//echo "<pre>";print_r($course_progress_array);exit;
 
      
	    $ebook_array = $this->ebook_model->fetchEbookByLang($this->language);
		//echo "<pre>";print_r($ebook_array);exit;
	    $base_courses = $this->user_model->get_courses($this->language);
	    $ebook_offer_options = $this->common_model->get_product_by_type('gift_ebook');
		$course_offer_options = $this->common_model->get_product_by_type('gift_course');
		$content['currency_id']=$currency_id = $this->currId;
		foreach($ebook_offer_options as $ebook_det)
		{
			$content['ebookgift_product_id'] = $ebook_det->id; 
  			
						}
		$ebookgift_price_details = $this->common_model->getProductFee($content['ebookgift_product_id'],$currency_id);
		//echo "<pre>";print_r($ebookgift_price_details['fake_amount']);exit;

		
			$content['ebookgift_fake_amount'] = $ebookgift_price_details['fake_amount']; 
			$content['ebookgift_amount'] = $ebookgift_price_details['amount'];
			$content['currency_symbol'] = $ebookgift_price_details['currency_symbol'];
			
		
		
		foreach($course_offer_options as $course_det)
		{
			$content['coursegift_product_id'] = $course_det->id; 
  			
						}
					//echo $currency_id;	exit;
						
		$coursegift_price_details = $this->common_model->getProductFee($content['coursegift_product_id'],$currency_id);
		
			$content['coursegift_fake_amount'] = $coursegift_price_details['fake_amount']; 
			$content['coursegift_amount'] = $coursegift_price_details['amount']; 
  			$content['currency_symbol'] = $coursegift_price_details['currency_symbol']; 
			
			//echo "<pre>";print_r($coursegift_price_details);
		//echo $course_offer_options->id;
		
			
	 for($i=1;$i<3;$i++)
			{
	 $flag=0;
	 while($flag==0)
			{
			$voucher_temp = "GFT";	
			$rand = random_string('numeric', 6);
			if($i==1)
			{
			$data['voucher_temp_ebook']=$voucher_temp_ebook=$voucher_temp.$rand;
			}
			else
			{
			$data['voucher_temp_course']=$voucher_temp_course=$voucher_temp.$rand;
			}
			$isUsed = $this->gift_voucher_model->getDetails_of_vcode($voucher_temp);
			if(empty($isUsed))
			{
				$flag=1;
			}
			}
			}
			$this->session->set_flashdata("voucher_temp_ebook",$voucher_temp_ebook);
			$this->session->set_flashdata("voucher_temp_course",$voucher_temp_course);
			
	//---------------------------
	
	$content['ebook_array']   	     = $ebook_array;
		$content['base_courses']   	     = $base_courses;

	$data['course_progress_array']  = $course_progress_array;
	
		$data['view'] = 'gift_campus';
        $data['content'] = $content;
       $data['translate'] = $this->tr_common;
       $this->load->view('user/campus_template',$data);
		
		
	}
  
  
  function ebook_gift()
	{

		$content= array();
		$data=array();
		
		$data['stud_id']=$user_id=$this->session->userdata['student_logged_in']['id'];
		$Lang_id=$this->language = $this->session->userdata('language');
		$this->tr_common['tr_course_progress'] = $this->user_model->translate_('course_progress');
  	 $this->tr_common['tr_more_info'] =$this->user_model->translate_('more_info');
	 $this->tr_common['tr_study_time_remaining'] =$this->user_model->translate_('study_remaining');
	 $this->tr_common['tr_days'] =$this->user_model->translate_('camp_days');
//	 $this->tr_common['tr_days'] =$this->user_model->translate_('days');
	 $this->tr_common['tr_next_course'] =$this->user_model->translate_('next_course');
	   
	 
	  $this->tr_common['tr_my_marks'] =$this->user_model->translate_('my_marks_new');
	  $this->tr_common['tr_proof_enroll'] =$this->user_model->translate_('tr_proof_enroll');
		    $content=$this->get_student_deatils_for_popup();
	$base_courses = $this->user_model->get_courses($Lang_id);
	 
	 
	 $i=0;
	 $array_index_marks=0;
	 
	 foreach ($base_courses as $row) {
		 $course_array[$i]['course_id'] = $row->course_id;
	 	$course_array[$i]['course_name'] = $row->course_name;
		$course_array[$i]['course_summary'] = $row->course_summary;
		$enrolled=$this->user_model->check_user_registered($user_id,$row->course_id); // check user registered with this course 
		
		if($enrolled) // if enrollled
		{
			foreach ($enrolled as $value)
			{
				
				$course_progress_array[$array_index_marks] = $this->get_student_progress($value->course_id);
				$course_progress_array[$array_index_marks]['course_status_id']=$value->course_status;
				$array_index_marks++;				
				$i++;
			}
		}
	 }
	$ebook_array = $this->ebook_model->fetchEbookByLang($this->language);
		$ebook_offer_options = $this->common_model->get_product_by_type('gift_ebook');
		$content['currency_id']=$currency_id = $this->currId;
		foreach($ebook_offer_options as $ebook_det)
		{
			$content['ebookgift_product_id'] = $ebook_det->id; 
  			
						}
		$ebookgift_price_details = $this->common_model->getProductFee($content['ebookgift_product_id'],$currency_id);
		//echo "<pre>";print_r($ebookgift_price_details['fake_amount']);exit;

		
			$content['ebookgift_fake_amount'] = $ebookgift_price_details['fake_amount']; 
			$content['ebookgift_amount'] = $ebookgift_price_details['amount'];
			$content['currency_symbol'] = $ebookgift_price_details['currency_symbol'];
						
						
		
		$content['ebook_array']   	     = $ebook_array;
		$content['base_courses']   	     = $base_courses;
		//$content['v_code']=$this->session->flashdata('voucher_temp_ebook');
	//---------------------------
	$data['course_progress_array']  = $course_progress_array;
	
		$data['view'] = 'gift_ebook_view';
        $data['content'] = $content;
       $data['translate'] = $this->tr_common;
       $this->load->view('user/campus_template',$data);
		
		
	}
	
	 function course_gift()
	{
		$content= array();
		$data=array();
		
		$data['stud_id']=$user_id=$this->session->userdata['student_logged_in']['id'];
		$Lang_id=$this->language = $this->session->userdata('language');
		$this->tr_common['tr_course_progress'] = $this->user_model->translate_('course_progress');
  	 $this->tr_common['tr_more_info'] =$this->user_model->translate_('more_info');
	 $this->tr_common['tr_study_time_remaining'] =$this->user_model->translate_('study_remaining');
	 $this->tr_common['tr_days'] =$this->user_model->translate_('camp_days');
//	 $this->tr_common['tr_days'] =$this->user_model->translate_('days');
	 $this->tr_common['tr_next_course'] =$this->user_model->translate_('next_course');
	   
	 
	  $this->tr_common['tr_my_marks'] =$this->user_model->translate_('my_marks_new');
	  $this->tr_common['tr_proof_enroll'] =$this->user_model->translate_('tr_proof_enroll');
		    $content=$this->get_student_deatils_for_popup();
	$base_courses = $this->user_model->get_courses($Lang_id);
	 
	 
	 $i=0;
	 $array_index_marks=0;
	 
	 foreach ($base_courses as $row) {
		 $course_array[$i]['course_id'] = $row->course_id;
	 	$course_array[$i]['course_name'] = $row->course_name;
		$course_array[$i]['course_summary'] = $row->course_summary;
		$enrolled=$this->user_model->check_user_registered($user_id,$row->course_id); // check user registered with this course 
		
		if($enrolled) // if enrollled
		{
			foreach ($enrolled as $value)
			{
				
				$course_progress_array[$array_index_marks] = $this->get_student_progress($value->course_id);
				$course_progress_array[$array_index_marks]['course_status_id']=$value->course_status;
				$array_index_marks++;				
				$i++;
			}
		}
	 }
	 $base_courses = $this->user_model->get_courses($this->language);
		$course_offer_options = $this->common_model->get_product_by_type('gift_course');
		$content['currency_id']=$currency_id = $this->currId;
		foreach($course_offer_options as $course_det)
		{
			$content['coursegift_product_id'] = $course_det->id; 
  			
						}
		$coursegift_price_details = $this->common_model->getProductFee($content['coursegift_product_id'],$currency_id);
		
			$content['coursegift_fake_amount'] = $coursegift_price_details['fake_amount']; 
			$content['coursegift_amount'] = $coursegift_price_details['amount']; 
  			$content['currency_symbol'] = $coursegift_price_details['currency_symbol']; 
						
						
		
		
		$content['base_courses']   	     = $base_courses;
	//---------------------------
	$data['course_progress_array']  = $course_progress_array;
	
		$data['view'] = 'gift_course_view';
        $data['content'] = $content;
       $data['translate'] = $this->tr_common;
       $this->load->view('user/campus_template',$data);
		
		
	}
	 function check_out()
	{
		$this->load->helper(array('dompdf', 'file'));
		$content= array();
		//$data=array();
		$Lang_id=$this->language = $this->session->userdata('language');
		$user_id = $this->session->userdata['student_logged_in']['id'];
	    $gift_id=	$this->uri->segment(3);
		$transaction_id=	$this->uri->segment(4);
		
		$gift_array=array();
		$gift_array['transaction_id']=$transaction_id;
		$this->user_model->update_Gift_subscriptions($gift_array,$gift_id);
		$gift_details=$this->user_model->get_item_idByID($gift_id);
		$item_id=$gift_details['item_id'];
		$to=$gift_details['to'];
		$from=$gift_details['from'];
		$message=$gift_details['message'];
		$voucher_code=$gift_details['voucher_code'];
		$voucher_type=$gift_details['type'];
		
		if($voucher_type=="ebook")
		{
		$gift_details=$this->user_model->ebookGift_subscriptions($item_id);	
		$gift_name=$gift_details['ebookName'];
		$cssLink ="public/gift mails/gift_css/ebook_mail.css";
		$url='www.trendimi.com/giftvoucher';
		$end='the course';
		}
		else if($voucher_type=="course")
		{
		$gift_details=$this->user_model->courseGift_subscriptions($item_id);	
		$course_summary=$gift_details['course_summary'];
		$gift_name=$gift_details['course_name'];
		$cssLink ="public/gift mails/gift_css/course_mail.css";
		$url='www.trendimi.com/giftvoucher';
		$end='the course';
		}
		else
		{
		$gift_name='TRENDIMI';
		$voucher_type="Gift Card";
		$cssLink ="public/gift mails/gift_css/ebook_mail.css";
		$url='www.trendimi.com/giftcard';
		$end='your Trendimi experience';
		}
		
		
if($voucher_type=="course")
{
	$summary='<h4>-'.$course_summary.'-</h4>';
	
}
else
{
	$summary='';
}
$html='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>mail</title>
<link href="'.$cssLink.'" rel="stylesheet" type="text/css" />
</head>

<body>
<div class="outer">
<h2>'.$gift_name.' '.$voucher_type.'</h2>
'.$summary.'
<form>
<label>To: '.$to.' - From: '.$from.'</label>
<textarea>'.$message.'</textarea>
</form>
<p>Congratulations! You have received a '.$gift_name.' '.$voucher_type.'. To download it, please go to '.$url.' and introduce your code. Enjoy '.$end.'!</p>
<h3>Code: '.$voucher_code.'</h3>
</div>
</body>
</html>
';
$data = pdf_create($html, 'gift_voucher_'.$user_id.'_'.$item_id,false);

     $this->path = "public/gift mails/pdf/gift_voucher_".$user_id."_".$item_id.".pdf";
	 write_file($this->path, $data);
		
		$sendemail = true;
		
		
		
		 $stud_details=$this->user_model->get_stud_details($user_id);	
		 
		  foreach($stud_details as $val2)
		  {
			 $user_country_name = $this->user_model->get_country_name($val2->country_id);
			 $user_name = $val2->first_name;
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
			$tomail = 'anooprsachin4u@gmail.com';
					
					  $emailSubject = "Gift Voucher : ".$user_mail;
					  $mailContent = "<p>Please find the attachment of ".$voucher_type." certificate here with it. <p>";
					  
					  $mailContent .= "<p>User name  : ".$user_name."</p>";
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
	
	
		
		
		redirect('coursemanager/gift_campus');
	//---------------------------
	
		
		
	}
	
	
	function gift_card()
	{
		$content= array();
		$data=array();
		
		$data['stud_id']=$user_id=$this->session->userdata['student_logged_in']['id'];
		$Lang_id=$this->language = $this->session->userdata('language');
		$this->tr_common['tr_course_progress'] = $this->user_model->translate_('course_progress');
  	 $this->tr_common['tr_more_info'] =$this->user_model->translate_('more_info');
	 $this->tr_common['tr_study_time_remaining'] =$this->user_model->translate_('study_remaining');
	 $this->tr_common['tr_days'] =$this->user_model->translate_('camp_days');
//	 $this->tr_common['tr_days'] =$this->user_model->translate_('days');
	 $this->tr_common['tr_next_course'] =$this->user_model->translate_('next_course');
	   
	 
	  $this->tr_common['tr_my_marks'] =$this->user_model->translate_('my_marks_new');
	  $this->tr_common['tr_proof_enroll'] =$this->user_model->translate_('tr_proof_enroll');
		    $content=$this->get_student_deatils_for_popup();
	$base_courses = $this->user_model->get_courses($Lang_id);
	 
	 
	 $i=0;
	 $array_index_marks=0;
	 
	 foreach ($base_courses as $row) {
		 $course_array[$i]['course_id'] = $row->course_id;
	 	$course_array[$i]['course_name'] = $row->course_name;
		$course_array[$i]['course_summary'] = $row->course_summary;
		$enrolled=$this->user_model->check_user_registered($user_id,$row->course_id); // check user registered with this course 
		
		if($enrolled) // if enrollled
		{
			foreach ($enrolled as $value)
			{
				
				$course_progress_array[$array_index_marks] = $this->get_student_progress($value->course_id);
				$course_progress_array[$array_index_marks]['course_status_id']=$value->course_status;
				$array_index_marks++;				
				$i++;
			}
		}
	 }
	 $base_courses = $this->user_model->get_courses($this->language);
		$course_offer_options = $this->common_model->get_product_by_type('gift_course');
		$content['currency_id']=$currency_id = $this->currId;
		foreach($course_offer_options as $course_det)
		{
			$content['coursegift_product_id'] = $course_det->id; 

  			
						}
		$coursegift_price_details = $this->common_model->getProductFee($content['coursegift_product_id'],$currency_id);
		
			$content['coursegift_fake_amount'] = $coursegift_price_details['fake_amount']; 
			$content['coursegift_amount'] = $coursegift_price_details['amount']; 
  			$content['currency_symbol'] = $coursegift_price_details['currency_symbol']; 
						
						
		
		
		$content['base_courses']   	     = $base_courses;
	//---------------------------
	$data['course_progress_array']  = $course_progress_array;
	
		$data['view'] = 'voucher_card';
        $data['content'] = $content;
       $data['translate'] = $this->tr_common;
       $this->load->view('user/campus_template',$data);
		
		
	}
	
	
  
  function certificate_download_sample($course_id)
  {			  
	$this->load->helper(array('dompdf', 'file'));
	
	 $course_name = $this->common_model->get_course_name($course_id);
	 $certificate_id = 1567;	
	
	 $grade=$this->user_model->translate_('mark_dist');	
		
		/* Style Me course */
		
		if($course_id==1)
		{		
		
			$coursename= 'Event Planner Course';
		}
		else if($course_id==2 )
		{ 	
	
		$coursename = 'Wedding Planner Course';
		}		
		else if($course_id==3 )
		{ 	
		
		$coursename = 'Start Your Business Course';
		}	
		else if($course_id==4 )
		{ 	
		
		$coursename = 'Market Your Business Course';
		}		
					
$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>EN-Certificate</title>
<style type="text/css">
/* BEGIN Light */
@font-face {
  font-family: "Open Sans";
  src: url("/public/user/certificate/fonts/Light/OpenSans-Light.eot");
  src: url("/public/user/certificate/fonts/Light/OpenSans-Light.eot?#iefix") format("embedded-opentype"),
       url("/public/user/certificate/fonts/Light/OpenSans-Light.woff") format("woff"),
       url("/public/user/certificate/fonts/Light/OpenSans-Light.ttf") format("truetype"),
       url("/public/user/certificate/fonts/Light/OpenSans-Light.svg#OpenSansLight") format("svg");
  font-weight: 300;
  font-style: normal;
}
/* END Light */


/* BEGIN Regular */
@font-face {
  font-family: "Open Sans";
  src: url("/public/user/certificate/fonts/Regular/OpenSans-Regular.eot");
  src: url("/public/user/certificate/fonts/Regular/OpenSans-Regular.eot?#iefix") format("embedded-opentype"),
       url("/public/user/certificate/fonts/Regular/OpenSans-Regular.woff") format("woff"),
       url("/public/user/certificate/fonts/Regular/OpenSans-Regular.ttf") format("truetype"),
       url("/public/user/certificate/fonts/Regular/OpenSans-Regular.svg#OpenSansRegular") format("svg");
  font-weight: normal;
  font-style: normal;
}
/* END Regular */

/* BEGIN Italic */
@font-face {
  font-family: "Open Sans";
  src: url("/public/user/certificate/fonts/Italic/OpenSans-Italic.eot");
  src: url("/public/user/certificate/fonts/Italic/OpenSans-Italic.eot?#iefix") format("embedded-opentype"),
       url("/public/user/certificate/fonts/Italic/OpenSans-Italic.woff") format("woff"),
       url("/public/user/certificate/fonts/Italic/OpenSans-Italic.ttf") format("truetype"),
       url("/public/user/certificate/fonts/Italic/OpenSans-Italic.svg#OpenSansItalic") format("svg");
  font-weight: normal;
  font-style: italic;
}
/* END Italic */

/* BEGIN Semibold */
@font-face {
  font-family: "Open Sans";
  src: url("/public/user/certificate/fonts/Semibold/OpenSans-Semibold.eot");
  src: url("/public/user/certificate/fonts/Semibold/OpenSans-Semibold.eot?#iefix") format("embedded-opentype"),
       url("/public/user/certificate/fonts/Semibold/OpenSans-Semibold.woff") format("woff"),
       url("/public/user/certificate/fonts/Semibold/OpenSans-Semibold.ttf") format("truetype"),
       url("/public/user/certificate/fonts/Semibold/OpenSans-Semibold.svg#OpenSansSemibold") format("svg");
  font-weight: 600;
  font-style: normal;
}
/* END Semibold */

/* BEGIN Semibold Italic */
@font-face {
  font-family: "Open Sans";
  src: url("/public/user/certificate/fonts/SemiboldItalic/OpenSans-SemiboldItalic.eot");
  src: url("/public/user/certificate/fonts/SemiboldItalic/OpenSans-SemiboldItalic.eot?#iefix") format("embedded-opentype"),
       url("/public/user/certificate/fonts/SemiboldItalic/OpenSans-SemiboldItalic.woff") format("woff"),
       url("/public/user/certificate/fonts/SemiboldItalic/OpenSans-SemiboldItalic.ttf") format("truetype"),
       url("/public/user/certificate/fonts/SemiboldItalic/OpenSans-SemiboldItalic.svg#OpenSansSemiboldItalic") format("svg");
  font-weight: 600;
  font-style: italic;
}
/* END Semibold Italic */

/* BEGIN Bold */
@font-face {
  font-family: "Open Sans";
  src: url("/public/user/certificate/fonts/Bold/OpenSans-Bold.eot");
  src: url("/public/user/certificate/fonts/Bold/OpenSans-Bold.eot?#iefix") format("embedded-opentype"),
       url("/public/user/certificate/fonts/Bold/OpenSans-Bold.woff") format("woff"),
       url("/public/user/certificate/fonts/Bold/OpenSans-Bold.ttf") format("truetype"),
       url("/public/user/certificate/fonts/Bold/OpenSans-Bold.svg#OpenSansBold") format("svg");
  font-weight: bold;
  font-style: normal;
}
/* END Bold */
body, html{margin:0; padding:0; font-family:Arial, Helvetica, sans-serif; color:#666;}
.outer{margin:20px auto 0 auto; background:white url(/public/user/certificate/images/eng-certificate.jpg) center center; width:713px; height:1013px}
h1, h2, h3{text-align:center; margin:0;}
h1{padding:10.5em 0 0 0; font-size:25pt}
h2{padding:2em 0 0 0; font-weight:600; font-size:25pt}
h3{padding:3em 0 0 0}
h3 span{margin-left:1em}
h4, p{font-size:13pt; margin:0; font-weight:600}
h4{padding:20.5em 0 0 2em}
p{padding:0.1em 0 0 2em}
h4 span, p span{width:180px; display:inline-block}
</style>
</head>

<body>
<div class="outer">
<h1>Taylor Brown</h1>
<h2>'. $coursename.'</h2>
<h3>Course Grade: <span>' .$grade.'</span></h3>
<h4><span>Date of Award:</span> 12 April 2014</h4>
<p><span>Certificate Number:</span> 100-'.$certificate_id.'</p>
</div>
</body>
</html>
';

	 $data = pdf_create($html, 'EventTrix_'.$user_id.'_'.$course_id);     
     write_file('name', $data);			
			
  }
  
   function suggest_new_course()
	{
		if(!$this->session->userdata('student_logged_in')){
			redirect('home');
		}	
		
		$content = array();
		
		if(isset($_POST['course_submit']))
		{
		
			$site_data  = array();
		    $site_data['course_name'] 	= $this->input->post('course_name');
			$site_data['course_info'] 	= $this->input->post('course_info');
			
			$user_id=$this->session->userdata['student_logged_in']['id'];
		    $today = date("Y-m-d");		
			
			$site_data['user_id'] = $user_id;
		    $site_data['date'] = $today;  
			$this->load->model('course_model','',TRUE);
			$user_details = $this->course_model->coursesuggested_user_details($user_id);
			
			foreach($user_details as $row)
			{
			$site_data['first_name'] = $row->first_name;
			$site_data['email'] = $row->email;
				
			}
			
		
			$this->form_validation->set_rules('course_name', 'Course Name', 'trim|required');
			$this->form_validation->set_rules('course_info', 'Course Info', 'trim|required');
			
		
			
			if($this->form_validation->run())
			{	
				 $this->load->model('course_model','',TRUE);
			 	 $this->course_model->add_course_suggestion($site_data);
			 	 $this->session->set_flashdata('message', 'New Course suggestion successfull!');
				 $content['msg_pass']="Many Thanks for submitting your course idea. Our course development team reviews course ideas monthly and based on your submissions selects new courses. As a thank you gift for your vote please accept this discount code: THANKYOU which allows you to purchase any course for just 19.00"; 
			 	 //redirect('coursemanager/suggest_new_course', 'refresh');
			}
		}

	
		//$expiry_date = date("Y-m-d", strtotime("+$period days"));
		

		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		
		if(isset($this->flashmessage)){
		$data['flashmessage'] = $this->flashmessage;
		}
		
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		
		$data['view'] = 'suggest_new_course';
		$data['content'] = $content;
		$data['translate'] = $this->tr_common;
		$this->load->view('user/help_center_template',$data);
	}
	
	 function prof_pic_upoload_ajax(){
	  if($_FILES)
	  {	 
	  	$stud_id  = $this->session->userdata['student_logged_in']['id'];						
			$config['upload_path'] = 'public/user/images/profile_pic/';
			$config['allowed_types'] = 'gif|jpg|jpeg|png|webp';
		  	$config['max_size'] 	  = '100000';
			
			$this->load->library('upload', $config);
			$this->upload->initialize($config);
	
			
			if ( $this->upload->do_upload('pro_pic'))
			{
				$uploaded = array('upload_data' => $this->upload->data());
				$testdata['user_pic'] = $uploaded['upload_data']['file_name'];
				$this->user_model->update_student_details($testdata,$stud_id);
				
				$return['success'] = 1;
				$return['msg'] = "Success!";
				$return['prof_pic'] = $testdata['user_pic'];
				$return['class'] = 'alert-success';
				
			}
			else
			{
				$error['upResult'] = array('error' => $this->upload->display_errors());
				$this->session->set_flashdata('message', $error['upResult']);
				
				$return['success'] = 0;
				$return['msg'] = $error['upResult'];
				$return['class'] = 'alert-danger';
			
			}
	  }
	  else{
		  $return['success'] = 0;
		  $return['msg'] = "Some error occured please try again later.";
		  $return['class'] = 'alert-danger';
	  }
	  
	  echo json_encode($return);
	
  }
	
  
  
}
	
