<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class voucher_code extends CI_Controller
{
 	 
	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->library('encrypt');
		$this->load->library('user_agent');
		$this->load->helper(array('form'));
		$this->load->helper('text');
  
		$this->load->library('form_validation');
    	$this->load->helper('url');
    	$this->load->database('',true);
 		$this->load->model('certificate_model','',TRUE);
 		$this->load->model('package_model','',TRUE);
		$this->load->model('common_model','',TRUE);
		$this->load->model('sales_model','',TRUE);
		$this->load->model('cms_model','',TRUE);
		$this->load->model('user_model','',TRUE);
		$this->load->model('ebook_model','',TRUE);
		$this->load->model('voucher_code_model','',TRUE);
		$ip = $this->input->ip_address();
  
		/*$this->load->library('ip2country_lib');
		$this->con_name = $this->ip2country_lib->getInfo();*/

		// fetching from freegeoip
		$this->load->library('ip2country');	
		/*$country_details = $this->ip2country->get_geoip();
		$this->con_name  = $country_details['country_name'];*/

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
		
		$curr_code=$this->user_model->get_currency_id($this->con_name);
		if($popup_message = $this->session->flashdata('popup_message')){
          $this->flashmessage =$popup_message;
     	}
		if($popup_message_public = $this->session->flashdata('popup_message_public')){
          $this->flashmessage_public =$popup_message_public;
     	}
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
    	else 
    	{
	      	$this->currId=1;
	    	$this->currencyCode='EUR';
			$this->currSymbol = '&euro;';
		}
		if(isset($_GET['lang_id'])){
			$newdata = array('language'  => $_GET['lang_id']);
			$this->session->set_userdata($newdata);
		}
		elseif(!$this->session->userdata('language')){
			$newdata = array('language'  => '4');
			$this->session->set_userdata($newdata);
		} 
		$this->language = $this->session->userdata('language');
		$this->student_status = $this->session->userdata('student_logged_in');
		
		$this->course=$this->user_model->get_courses_order($this->language);
		if(empty($this->course))
		{
			$this->course=$this->user_model->get_courses_order(4); // get english courses
		}
		$this->menu['top_menu']=$this->user_model->get_top_menu($this->language);
		
		//---------------common translations --------------------------
		
		$this->tr_common['tr_why_us']   	  = $this->user_model->translate_('why_us');
		$this->tr_common['tr_about_us']   	  = $this->user_model->translate_('about_us');
		$this->tr_common['tr_faq']        	  = $this->user_model->translate_('faq');		 
		$this->tr_common['tr_contact_us'] 	  = $this->user_model->translate_('contact_us');		
		$this->tr_common['tr_return_to']   	  = $this->user_model->translate_('return_to');
		$this->tr_common['tr_campus']   	  = $this->user_model->translate_('campus');
		$this->tr_common['tr_sign']   		  = $this->user_model->translate_('sign');
		$this->tr_common['tr_Out']   		  = $this->user_model->translate_('Out');
		$this->tr_common['tr_return_campus']  = $this->user_model->translate_('tr_return_campus');
		$this->tr_common['tr_sign_out']       = $this->user_model->translate_('tr_sign_out');
		$this->tr_common['tr_eventrix']   	  = $this->user_model->translate_('eventrix');
		$this->tr_common['tr_user_name']      = $this->user_model->translate_('user_name');
		$this->tr_common['tr_password']       = $this->user_model->translate_('password');
		$this->tr_common['tr_terms_use']      = $this->user_model->translate_('terms_use');		 
		$this->tr_common['tr_privacy_policy'] = $this->user_model->translate_('privacy_policy');
    }
  
 
		
  
	 function index(){
	
		$this->tr_common['tr_validate'] =$this->user_model->translate_('validate');
        $this->tr_common['tr_help'] =$this->user_model->translate_('help');
		$this->session->unset_userdata('public_id');
		if(!empty($_POST))
		{
			//echo "<pre>";print_r($_POST);
			$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
			$this->form_validation->set_rules('pass_word', 'Password', 'trim|required|xss_clean|callback_check_database');
			$content['user_pass'] = $this->input->post('username');
			if($this->form_validation->run() == TRUE)
			{
				$this->session->unset_userdata('voucher_cart_session_id');
				$this->session->unset_userdata('enrolling_rep_code');
				
				$return_fn = "home/select_course";
				redirect($return_fn);
			}
		}
		$content['course_array'] = $this->user_model->get_acitive_courses_only($this->language);
		$content['no_login'] = true;	
		$content['translate'] = $this->tr_common;
		
		$title['pageTitle'] = 'Login';
		$content['content'] = $title;
		
	
		$content['view'] = '';
		$this->load->view('user/voucher_code_offers',$content); 
	
		//$content['view'] = 'voucher_code';
		//$this->load->view('user/template_outer',$content); 	  
		
	
	  }
	  
	  
	  function login_form(){
		 
		  
		 if($_POST)
		 { 
			
				$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
				$this->form_validation->set_rules('password_voucher', 'Password', 'trim|required|xss_clean|callback_check_database');
								
				if($this->form_validation->run() == TRUE)
				{				
					redirect('voucher_code/'.$this->session->userdata['voucher_code_applied_details']['product_type']);
				}	
				else
				{
					
					$this->session->set_flashdata('popup_message','Invalid Login.');
					
					redirect('voucher_code/'.$this->session->userdata['voucher_code_applied_details']['product_type']);
				}			
		 }
		
		
	  }




	function gift_voucher(){


		if(!isset($this->session->userdata['gift_voucher_applied_details']))
		{			
			redirect('voucher_code');
		}	
		

		$this->load->model('gift_voucher_model','',TRUE);
		
		$gift_code = $this->session->userdata['gift_voucher_applied_details']['vouchercode'];
		$gift_voucher_details = $this->gift_voucher_model->isValidForDeals($gift_code);

		/*echo "<pre>";
		print_r($gift_voucher_details);
		exit;*/

          // Gift voucher is ready to redeem
          if($gift_voucher_details['code_error']==0 || $gift_voucher_details['code_error']==1 || $gift_voucher_details['code_error']==2)
          {

               if($gift_voucher_details['security_req']=='yes')
               {
                    redirect('gift_voucher/security_code');
               }
               else
               {
                    redirect('gift_voucher');                    
               }
          }
          // gift voucher expired
          // else if($gift_voucher_details['code_error']==2){

          //   $this->session->set_flashdata('gift_voucher_error','According to our records your voucher has expired on '.$gift_voucher_details['expired_on']);

          //   redirect('voucher_code');

          // }
          

		// gift voucher alread used
		else if($gift_voucher_details['code_error']==3)
          {
			
			$this->session->set_flashdata('gift_voucher_error','Your voucher has already been used on '.$gift_voucher_details['used_on']);
			redirect('voucher_code');

		}	

	}

	
	  
	  /**
	   * [course Voucher code for course]
	   * @return [type] [Add courses to cart]
	   */
	  function course_subscription()
	  {
	  		if(!isset($this->session->userdata['voucher_code_applied_details']))
			{
				redirect('voucher_code');
			}	

			$this->check_user_used_this_voucher_code();		
			
			$content = array();
			$added_products_array = array();
			$enrolled_course_array = array();
			
			$voucher_subscription_course_array = $this->voucher_code_model->get_subscription_course_for_voucher($this->session->userdata['voucher_code_applied_details']['voucher_id']);

			$content['currency_id'] = $this->currId;		
			

			if(!$voucher_subscription_course_array[0]->course_ids)
			{
				$course_array = $this->voucher_code_model->get_courses_active($this->language);
			}
			else
			{
				$voucher_code_subscription_course_id_array = explode(',',$voucher_subscription_course_array[0]->course_ids);						
				$course_array = $this->voucher_code_model->get_courses($voucher_code_subscription_course_id_array);
			}

						
			$this->tr_common['tr_trndimi_ebooks'] = $this->user_model->translate_('trendimi_ebooks');
			$this->tr_common['tr_check_out'] = $this->user_model->translate_('check_out');
		    $this->tr_common['tr_total_price'] = $this->user_model->translate_('total_price');
			 
			if(!isset($this->session->userdata['student_logged_in']['id']))
			{
				$user_logged_in = false;
			}
			else
			{

				$voucher_id = $this->session->userdata['voucher_code_applied_details']['voucher_id'];
				$product_id = $this->common_model->getProdectId('voucher_code',$voucher_id);				

				if(!$voucher_subscription_course_array[0]->course_ids)
				{
					$selected_values = 0;
				}
				else
				{
					$selected_values = str_replace(',', '+', $voucher_subscription_course_array[0]->course_ids);
				}


				$this->add_voucher_ebook_course($selected_values,$product_id,$this->currId,'voucher_course_subscription',0);
				redirect('voucher_code/bonus');
				$user_logged_in = true;
				$user_id = $this->session->userdata['student_logged_in']['id'];	
				$enrolled_course_array = $this->user_model->get_enrolled_courses($user_id);
			}
			
			if($this->session->userdata('voucher_cart_session_id'))
			{			
				$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('voucher_cart_session_id'));			
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

			$voucher_id = $this->session->userdata['voucher_code_applied_details']['voucher_id'];
						
			$voucher_course_product_id = $this->common_model->getProdectId('voucher_code',$voucher_id);
				

			if(isset($this->flashmessage))
			$content['flashmessage']=$this->flashmessage;
			$content['added_products_array'] = $added_products_array;	
			$content['voucher_course_product_id'] = $voucher_course_product_id;	
			$content['lang_id'] 			 = $this->language;
			$content['user_logged_in'] 	     = $user_logged_in;
			$content['voucher_subscription_course_array'] = $voucher_subscription_course_array;	
			$content['course_array'] 		 = $course_array;	
			$content['translate'] 		     = $this->tr_common;
			$content['view'] 				 = 'voucher_code_course_subscription';
			$title['pageTitle'] 			 = 'Login';
			$content['content'] 			 = $title;
			$this->load->view('user/template_outer',$content); 
	  }


	  function enrol_course_subscription(){



  		if(!isset($this->session->userdata['voucher_code_applied_details']))
		{
			redirect('voucher_code');
		}			
		
		
		$content = array();
		$added_products_array = array();
		$enrolled_course_array = array();


		$this->tr_common['tr_first_name']   =$this->user_model->translate_('First_Name'); 
		$this->tr_common['tr_last_name']   =$this->user_model->translate_('last_Name'); 		
		$this->tr_common['tr_email']   =$this->user_model->translate_('email'); 
		$this->tr_common['tr_confirm_email']   =$this->user_model->translate_('confirm_email'); 
		$this->tr_common['tr_contact_num']   =$this->user_model->translate_('contact_num'); 	
		  
		
		$this->tr_common['tr_create_stylist_id']   =utf8_decode($this->user_model->translate_('create_stylist_id')); 
		$this->tr_common['tr_create_secret_code']   =$this->user_model->translate_('create_secret_code'); 		
		$this->tr_common['tr_confirm_secret_code']   =$this->user_model->translate_('confirm_secret_code'); 
		 
		
		$this->tr_common['tr_first_name_required']  =$this->user_model->translate_('first_name_required'); 		
		$this->tr_common['tr_last_name_required']   =$this->user_model->translate_('last_name_required');		
		$this->tr_common['tr_user_name_required']   =$this->user_model->translate_('user_name_required'); 		
		$this->tr_common['tr_email_required']   	   =$this->user_model->translate_('email_required'); 
		$this->tr_common['tr_email_exists']   	   =$this->user_model->translate_('email_exists'); 
		$this->tr_common['tr_valid_email_required']   	   =$this->user_model->translate_('valid_email_required'); 
		$this->tr_common['tr_required']   	   		 =$this->user_model->translate_('required'); 
		$this->tr_common['tr_confirm_email']   =$this->user_model->translate_('confirm_email'); 
		
		

		$this->tr_common['tr_registration']     = $this->user_model->translate_('registration');
		$this->tr_common['tr_1_personal_information'] = $this->user_model->translate_('1_personal_information');
		$this->tr_common['tr_2_upgrade_packages'] = $this->user_model->translate_('2_upgrade_packages');
		$this->tr_common['tr_3_review_and_payment'] = $this->user_model->translate_('3_review_and_payment');

		 $this->tr_common['tr_next']   =$this->user_model->translate_('_next'); 

		$this->tr_common['tr_step_3_your_details']   	   	=$this->user_model->translate_('step_3_your_details');  
		$this->tr_common['tr_4_confirmation'] = $this->user_model->translate_('4_confirmation');

		$this->tr_common['step_4_buy_now'] = $this->user_model->translate_('step_4_buy_now');		
		$this->tr_common['tr_Email_ucfirst'] =$this->user_model->translate_('Email_ucfirst');


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
		 		 
          $studentdata['with_coupon']='yes';
		  $studentdata['coupon_code'] = $this->session->userdata['voucher_code_applied_details']['vouchercode'];		
		 
		  $studentdata['reg_date']=date("Y-m-d");

		  $this->check_public_user_used_this_voucher_code($studentdata['email']);
		  
		  $this->form_validation->set_rules('fname', 'First name', 'trim|required');
		  $this->form_validation->set_rules('lname', 'Last Name', 'required');	  
		  $this->form_validation->set_rules('user_name', 'UserName', 'required');
		  $this->form_validation->set_rules('pword', 'Password', 'required|min_length[6]|callback_chkpword[Password]');
		  $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email|callback_chkemail[E-mail]');
		 
		  if($this->form_validation->run())
		  { 	
			
				$this->user_model->add_student_temp($studentdata);
				$pre_user_id=$this->db->insert_id();

				$pre_session_array['pre_user_id'] = $pre_user_id;								
				$sess_array = array('voucher_code_pre_user_id' => $pre_session_array);
			    $this->session->set_userdata($sess_array);

			    //$voucher_subscription_course_array = $this->voucher_code_model->get_subscription_course_for_voucher($this->session->userdata['voucher_code_applied_details']['voucher_id']);
			    
			    $voucher_id = $this->session->userdata['voucher_code_applied_details']['voucher_id'];
				$product_id = $this->common_model->getProdectId('voucher_code',$voucher_id);

				//$selected_values = str_replace(',', '+', $voucher_subscription_course_array[0]->course_ids);	
				$selected_values = 0;		

				$this->add_voucher_ebook_course($selected_values,$product_id,$this->currId,'voucher_course_subscription',0);
				redirect('voucher_code/bonus');


				//redirect('voucher_code/select_course');
					
		  }
		

		}
					
		$this->tr_common['tr_trndimi_ebooks'] = $this->user_model->translate_('trendimi_ebooks');
		$this->tr_common['tr_check_out'] = $this->user_model->translate_('check_out');
	    $this->tr_common['tr_total_price'] = $this->user_model->translate_('total_price');
		 
		if(isset($this->session->userdata['student_logged_in']['id']))
		{
			redirect('voucher_code/course');
		}
		
		
		//$content['added_products_array'] = $added_products_array;	
		$content['lang_id'] 			 = $this->language;
	//	$content['user_logged_in'] 	     = $user_logged_in;
	//	$content['voucher_course_array'] = $voucher_course_array;	
	//	$content['course_array'] 		 = $course_array;	
		$content['translate'] 		     = $this->tr_common;
		$content['view'] 				 = 'voucher_code_enrol_course_subscription';
		$title['pageTitle'] 			 = 'Login';
		$content['content'] 			 = $title;
		$this->load->view('user/template_outer',$content); 


	  

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

	  	if($this->session->userdata('voucher_code_applied')==1){
	  	
	  		$this->session->unset_userdata('voucher_cart_session_id');
			$this->session->unset_userdata('voucher_code_applied');
			$this->session->unset_userdata('voucher_code_applied_details');
			$this->session->unset_userdata('cart_source'); 
			$this->session->unset_userdata('added_user_id');
			$this->session->unset_userdata('voucher_code_pre_user_id');				 

			$data['err_msg']		 = 0;
			echo json_encode($data); 
			exit;	
	  	}
	  	else if($this->session->userdata('gift_voucher_applied')==1)
	  	{	  	
	  		$this->session->unset_userdata('gift_voucher_package_applying_course');
	  		$this->session->unset_userdata('gift_voucher_pre_user_id');
			$this->session->unset_userdata('gift_voucher_applied');
			$this->session->unset_userdata('gift_voucher_applied_details');
			$this->session->unset_userdata('cart_source'); 
			$this->session->unset_userdata('added_user_id');
			$this->session->unset_userdata('voucher_code_pre_user_id');				 
			$this->session->unset_userdata('voucher_code_new_user_id');			
			$this->session->unset_userdata('gift_voucher_cart_session_id');			
			$this->session->unset_userdata('deals');			

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


	  function check_user_used_this_voucher_code(){


	  	// Limit the users using the free voucher codes only once

		if(isset($this->session->userdata['voucher_code_applied_details']) && (isset($this->session->userdata['student_logged_in']['id'])) && ($this->session->userdata['voucher_code_applied_details']['discount_type']=='free') ){

		 	$free_voucher_used = $this->voucher_code_model->check_user_used_this_code($this->session->userdata['voucher_code_applied_details']['voucher_id'],$this->session->userdata['student_logged_in']['id']);

			if($free_voucher_used){											
			   	
				$free_voucher['free_voucher_code'] = true;
				$free_voucher['voucher_code'] = $this->session->userdata['voucher_code_applied_details']['vouchercode'];						
				$sess_array = array('free_voucher_code_used' => $free_voucher);
			    $this->session->set_userdata($sess_array);

			    $this->session->unset_userdata('voucher_cart_session_id');
				$this->session->unset_userdata('voucher_code_applied');
				$this->session->unset_userdata('voucher_code_applied_details');
				$this->session->unset_userdata('cart_source'); 
				$this->session->unset_userdata('added_user_id');
				$this->session->unset_userdata('voucher_code_pre_user_id');	
			 	redirect('voucher_code');

			}

		}
		return true;


		// End Limit the users using the free voucher codes only once

	  }


	  function check_public_user_used_this_voucher_code($public_user_mail)
       {


	  	// Limit the users using the free voucher codes only once

		if(isset($this->session->userdata['voucher_code_applied_details']) && ($this->session->userdata['voucher_code_applied_details']['discount_type']=='free') ){

		 	$free_voucher_used = $this->voucher_code_model->check_public_user_used_this_code($this->session->userdata['voucher_code_applied_details']['voucher_id'],$public_user_mail);

			if($free_voucher_used)
               {											
			   	
				$free_voucher['free_voucher_code'] = true;
				$free_voucher['voucher_code'] = $this->session->userdata['voucher_code_applied_details']['vouchercode'];						
				$sess_array = array('free_voucher_code_used' => $free_voucher);
			    $this->session->set_userdata($sess_array);

			    $this->session->unset_userdata('voucher_cart_session_id');
				$this->session->unset_userdata('voucher_code_applied');
				$this->session->unset_userdata('voucher_code_applied_details');
				$this->session->unset_userdata('cart_source'); 
				$this->session->unset_userdata('added_user_id');
				$this->session->unset_userdata('voucher_code_pre_user_id');	
			 	redirect('voucher_code');

			}

		}
		return true;


		// End Limit the users using the free voucher codes only once

	  }

	  
	  function get_voucher_code_details($voucher_code)
	  {
		  
		  	$voucher_code_details = $this->voucher_code_model->get_voucher_code_details($voucher_code);
			
			if($voucher_code_details)
			{	

				if($voucher_code_details[0]->reusable =='0' && $voucher_code_details[0]->used > 0)
				{
					$data['err_type'] = 'Voucher Code already used';
					$data['err_msg']= 1;						
					echo json_encode($data); 
					exit;
				}
				else
				{
					$sess_array = array('voucher_code_applied' => true);
					$this->session->set_userdata($sess_array);
				
					$data['err_msg']		 = 0;
					$data['voucher_id'] 	 = $voucher_code_details[0]->id;					
					$data['product_type'] 	 = $voucher_code_details[0]->product_type;
					$data['discount_type']   = $voucher_code_details[0]->discount_type;					
					$data['vouchercode'] 	 = $voucher_code_details[0]->vouchercode;
					$data['voucher_code_added_source'] 	 = $voucher_code_details[0]->voucher_source;
										
					$sess_array = array('voucher_code_applied_details' => $data);
				    $this->session->set_userdata($sess_array);				
					echo json_encode($data); 
					exit;		
				}				
				
			}
			else
			{			
				$voucher_code_err_details = $this->voucher_code_model->get_voucher_code($voucher_code);
				if($voucher_code_err_details)
				{
					$currentDate  =date("Y-m-d",time());
					
					if($voucher_code_err_details[0]->status != '0')
					{
						if($voucher_code_err_details[0]->start_date > $currentDate )
						{
							$data['err_type'] = 'Voucher Code not activated';
						}
						else if($voucher_code_err_details[0]->end_date < $currentDate)
						{
							$data['err_type'] = 'Voucher Code expired';
						}
					}
					else
					{
						$data['err_type'] = 'Voucher Code not active';
					}
					
					$data['err_msg']= 1;						
					echo json_encode($data); 
					exit;
				}


				$gift_voucher_details = $this->voucher_code_model->get_gift_voucher_details($voucher_code);

                    if($this->session->userdata['ip_address'] == '117.216.21.108')
                    {
                    //echo "Gift_voucher"; //exit;
                    //echo "<pre>"; print_r($gift_voucher_details); exit;
                    }

				if($gift_voucher_details)
				{
					$sess_array = array('gift_voucher_applied' => true);
					$this->session->set_userdata($sess_array);				
					$data['err_msg']	  = 0;
					$data['voucher_id']   = $gift_voucher_details['idgiftVoucher'];
					$data['vouchercode']  = $gift_voucher_details['giftVoucherCode'];
					$data['discountType'] = $gift_voucher_details['discountType'];
					$data['product_type'] = 'gift_voucher';
					$data['code_exist']   = $gift_voucher_details['code_exist'];	
					$data['security_req'] = $gift_voucher_details['security_req'];
														
					$sess_array = array('gift_voucher_applied_details' => $data);
				     $this->session->set_userdata($sess_array);				
					echo json_encode($data); 
					exit;	
				}
				
				
				$data['err_msg']= 1;	
				$data['err_type'] = 'Voucher Code not found';		
				echo json_encode($data); 
				exit;
			}
				
	  }
	 
	 

	  
	  /**
	   * [ebooks Voucher code for product ebook]
	   * @return [type] [Lists ebooks for the voucher code and adds to the cart]
	   */
	  function ebooks()
	  {	
	 	
		if(!isset($this->session->userdata['voucher_code_applied_details']))
		{
			redirect('voucher_code');
		}
		
		$this->check_user_used_this_voucher_code();

		$content = array();
		$added_products_array = array();

		$content['currency_id'] = $this->currId;
		
		$voucher_ebook_array = $this->voucher_code_model->get_ebooks_for_voucher($this->session->userdata['voucher_code_applied_details']['voucher_id']);
		
		$voucher_code_ebook_id_array = explode(',',$voucher_ebook_array[0]->course_ebook_ids);
				
		$ebook_array = $this->voucher_code_model->get_ebooks($voucher_code_ebook_id_array);
		
		
		$this->tr_common['tr_trndimi_ebooks'] = $this->user_model->translate_('trendimi_ebooks');
		$this->tr_common['tr_check_out'] = $this->user_model->translate_('check_out');
	    $this->tr_common['tr_total_price'] = $this->user_model->translate_('total_price');
		 
		if(!isset($this->session->userdata['student_logged_in']['id']))
		{
			$user_logged_in = false;
		}
		else
		{
			$user_logged_in = true;
		}
		
		if($this->session->userdata('voucher_cart_session_id'))
		{			
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('voucher_cart_session_id'));			
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

		$voucher_id = $this->session->userdata['voucher_code_applied_details']['voucher_id'];			
		$voucher_ebook_product_id = $this->common_model->getProdectId('voucher_code',$voucher_id);
		if(isset($this->flashmessage))
			$content['flashmessage']=$this->flashmessage;
			if(isset($this->flashmessage_public))
			$content['flashmessage_public']=$this->flashmessage_public;
			
		$content['added_products_array']	 = $added_products_array;	
		$content['lang_id'] 			 	 = $this->language;
		$content['user_logged_in'] 	  		 = $user_logged_in;
		$content['voucher_ebook_array'] 	 = $voucher_ebook_array;
			$content['voucher_ebook_product_id'] = $voucher_ebook_product_id;	
		$content['ebook_array'] 		 	 = $ebook_array;	
		$content['translate'] 		   		 = $this->tr_common;
		$content['view'] 					 = 'voucher_code_ebook';
		$title['pageTitle'] 			 	 = 'Login';
		$content['content'] 			 	 = $title;
		$this->load->view('user/template_outer',$content); 	  
	  }
	  


	  /**
	   * [extension Voucher code for product Extension]
	   * @return [type] [Lists and let user to add extension to the cart]
	   */
	  function extension()
	  {
		  	if(!isset($this->session->userdata['voucher_code_applied_details']))
			{
				redirect('voucher_code');
			}
			
			$this->check_user_used_this_voucher_code();

			$content = array();
			$added_products_array = array();
			$lang_id = $this->session->userdata('language');

			$voucher_id = $this->session->userdata['voucher_code_applied_details']['voucher_id'];

			$voucher_details = $this->voucher_code_model->get_voucher($voucher_id);

			$voucher_extension_option = $this->voucher_code_model->get_voucher_products_other($voucher_id);

			$extention_options = $this->voucher_code_model->get_table_values('extension_options');	
			
			$j = 0;
			foreach($extention_options as $key =>$row)
			{
				if($lang_id ==4)
				{			
					$extention_details[$j]['extention_option'] = $row->extension_option;	
				}
				elseif($lang_id ==3)
				{
					$extention_details[$j]['extention_option'] = $row->extension_option_spanish;	
				}
				else
				{
					$extention_details[$j]['extention_option'] = $row->extension_option_french;	
				}
				$product_id = $this->common_model->getProdectId('extension',$row->id,1);			
				$price_details_array[$j] = $this->common_model->getProductFee($product_id,$this->currId);
				$extention_details[$j]['product_id'] = $product_id;		
				$j++;
			}
			

			$extension_product_details = $this->common_model->get_product_details($voucher_extension_option[0]->product_id);
			  
			$extension_item_id =  $extension_product_details[0]->item_id;
			  
			$extension_details = $this->sales_model->get_extension_details_by_units($extension_item_id);								
				
			if($this->session->userdata('language')==3)
			{
				$extention_item_details = $extension_details[0]->extension_option_spanish;
			}
			else
			{								
				$extention_item_details = $extension_details[0]->extension_option;
			} 

			$this->tr_common['tr_trndimi_ebooks'] = $this->user_model->translate_('trendimi_ebooks');
			$this->tr_common['tr_check_out'] = $this->user_model->translate_('check_out');
	    	$this->tr_common['tr_total_price'] = $this->user_model->translate_('total_price');
		 
			if(!isset($this->session->userdata['student_logged_in']['id']))
			{
				$user_logged_in = false;
				$enrolled_course_array = array();
			}
			else
			{
				$user_logged_in = true;
				$user_id = $this->session->userdata['student_logged_in']['id'];	
				$enrolled_course_array = $this->voucher_code_model->get_enrolled_courses($user_id);
			}
		
			if($this->session->userdata('voucher_cart_session_id'))
			{			
				$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('voucher_cart_session_id'));			
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
									$added_products_array[$item_det->product_type]['product_id'] 		= $prod->product_id;
									$added_products_array[$item_det->product_type]['selected_item_ids'] = $item_det->selected_item_ids;									
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

		if($this->session->userdata['voucher_code_applied_details']['discount_type'] == 'offer_price')
		{				
			$voucher_extension_product_id = $this->common_model->getProdectId('voucher_code',$voucher_id);
		}	
		else 
		{				
			//$voucher_extension_product_id = $this->common_model->getProdectId($this->session->userdata['voucher_code_applied_details']['product_type'],$voucher_course_array[0]->item_count_x);
			$voucher_extension_product_id = $voucher_extension_option[0]->product_id;
		}

		$content['currency_id'] = $this->currId;
		$content['voucher_extension_option']     = $voucher_extension_option;		
		$content['extention_details']            = $extention_details;
		$content['extention_item_details']       = $extention_item_details;
		$content['voucher_extension_product_id'] = $voucher_extension_product_id;
		$content['voucher_details'] 			 = $voucher_details;
		
		$content['enrolled_course_array'] = $enrolled_course_array;
		$content['added_products_array']  = $added_products_array;	
		$content['lang_id'] 			  = $this->language;
		$content['user_logged_in'] 	      = $user_logged_in;		
		$content['translate'] 		      = $this->tr_common;
		$content['view'] 				  = 'voucher_code_extension';
		$title['pageTitle'] 			  = 'Login';
		$content['content'] 			  = $title;
		$this->load->view('user/template_outer',$content); 	

	  }



	  /**
	   * [course Voucher code for course]
	   * @return [type] [Add courses to cart]
	   */
	  function course_old()
	  {
	  		if(!isset($this->session->userdata['voucher_code_applied_details']))
			{
				redirect('voucher_code');
			}			
			
			$this->check_user_used_this_voucher_code();

			$content = array();
			$added_products_array = array();
			$enrolled_course_array = array();
			
			$voucher_course_array = $this->voucher_code_model->get_course_for_voucher($this->session->userdata['voucher_code_applied_details']['voucher_id']);
	
			$content['currency_id'] = $this->currId;
			
			$voucher_code_course_id_array = explode(',',$voucher_course_array[0]->course_ebook_ids);
				
			$course_array[1] = $this->voucher_code_model->get_courses($voucher_code_course_id_array,'1');
			$course_array[2] = $this->voucher_code_model->get_courses($voucher_code_course_id_array,'2');
			$course_array[3] = $this->voucher_code_model->get_courses($voucher_code_course_id_array,'3');
			$course_array[4] = $this->voucher_code_model->get_courses($voucher_code_course_id_array,'4');
			$course_array = $this->voucher_code_model->get_courses($voucher_code_course_id_array,'');
						
			$this->tr_common['tr_trndimi_ebooks'] = $this->user_model->translate_('trendimi_ebooks');
			$this->tr_common['tr_check_out'] = $this->user_model->translate_('check_out');
		    $this->tr_common['tr_total_price'] = $this->user_model->translate_('total_price');
			 
			if(!isset($this->session->userdata['student_logged_in']['id']))
			{
				$user_logged_in = false;
			}
			else
			{
				$user_logged_in = true;
				$user_id = $this->session->userdata['student_logged_in']['id'];	
				$enrolled_course_array = $this->voucher_code_model->get_enrolled_courses($user_id);
			}
			
			if($this->session->userdata('voucher_cart_session_id'))
			{			
				$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('voucher_cart_session_id'));			
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

			$voucher_id = $this->session->userdata['voucher_code_applied_details']['voucher_id'];
			if($this->session->userdata['voucher_code_applied_details']['discount_type'] == 'offer_price')
			{				
				$voucher_course_product_id = $this->common_model->getProdectId('voucher_code',$voucher_id);
			}	
			else 
			{				
				$voucher_course_product_id = $this->common_model->getProdectId($this->session->userdata['voucher_code_applied_details']['product_type'],$voucher_course_array[0]->item_count_x);
			}		

			if(isset($this->flashmessage))
			$content['flashmessage']=$this->flashmessage;
			$content['added_products_array'] = $added_products_array;	
			$content['voucher_course_product_id'] = $voucher_course_product_id;	
			$content['lang_id'] 			 = $this->language;
			$content['user_logged_in'] 	     = $user_logged_in;
			$content['voucher_course_array'] = $voucher_course_array;	
			$content['course_array'] 		 = $course_array;	
			$content['translate'] 		     = $this->tr_common;
			$content['view'] 				 = 'voucher_code_course';
			$title['pageTitle'] 			 = 'Login';
			$content['content'] 			 = $title;
			$this->load->view('user/template_outer',$content); 
	  }



	  function course_bundle(){

	  	if(!isset($this->session->userdata['voucher_code_applied_details']))
		{
			redirect('voucher_code');
		}	

		$this->check_user_used_this_voucher_code();	

		$content = array();
		$added_products_array   = array();
		$voucher_bundle_details = $this->voucher_code_model->get_voucher_bundle_details($this->session->userdata['voucher_code_applied_details']['voucher_id']);
		$voucher_id = $this->session->userdata['voucher_code_applied_details']['voucher_id'];

		if(!isset($this->session->userdata['student_logged_in']['id']))
		{
			$user_logged_in = false;
		}
		else
		{
			$user_logged_in = true;
		}
		
		$this->tr_common['tr_trndimi_ebooks'] = $this->user_model->translate_('trendimi_ebooks');
		$this->tr_common['tr_check_out'] = $this->user_model->translate_('check_out');
    	$this->tr_common['tr_total_price'] = $this->user_model->translate_('total_price');

		if($this->session->userdata('voucher_cart_session_id'))
		{			
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('voucher_cart_session_id'));			
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

		if($this->session->userdata['voucher_code_applied_details']['discount_type'] == 'offer_price')
		{				
			$voucher_course_bundle_product_id = $this->common_model->getProdectId('voucher_code',$voucher_id);
		}	
		else 
		{	

			$voucher_course_bundle_product_id = $this->common_model->getProdectId('bundle',$voucher_bundle_details['id']);
		}		

		if(isset($this->flashmessage))
			$content['flashmessage']=$this->flashmessage;

		$content['currency_id'] = $this->currId;
		$content['voucher_bundle_details']     		 = $voucher_bundle_details;
		$content['voucher_course_bundle_product_id'] = $voucher_course_bundle_product_id;
		
		
		$content['added_products_array']  = $added_products_array;	
		$content['lang_id'] 			  = $this->language;
		$content['user_logged_in'] 	      = $user_logged_in;		
		$content['translate'] 		      = $this->tr_common;
		$content['view'] 				  = 'voucher_code_course_bundles';
		$title['pageTitle'] 			  = 'Login';
		$content['content'] 			  = $title;
		$this->load->view('user/template_outer',$content); 	

	} 


	function select_course_bundle(){

		if(!isset($this->session->userdata['voucher_code_applied_details']))
		{
			redirect('voucher_code');
		}	

		$this->check_user_used_this_voucher_code();	
		$content = array();
		$added_products_array   = array();		
		$voucher_bundle_details = $this->voucher_code_model->get_voucher_bundle_details($this->session->userdata['voucher_code_applied_details']['voucher_id']);

		if(!isset($this->session->userdata['student_logged_in']['id']))
		{
			$user_logged_in = false;
		}
		else
		{
			$user_logged_in = true;
		}
		
		$this->tr_common['tr_trndimi_ebooks'] = $this->user_model->translate_('trendimi_ebooks');
		$this->tr_common['tr_check_out'] = $this->user_model->translate_('check_out');
    	$this->tr_common['tr_total_price'] = $this->user_model->translate_('total_price');

		if($this->session->userdata('voucher_cart_session_id'))
		{			
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('voucher_cart_session_id'));			
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

		if($this->session->userdata['voucher_code_applied_details']['discount_type'] == 'offer_price')
		{				
			$voucher_course_bundle_product_id = $this->common_model->getProdectId('voucher_code',$voucher_id);
		}	
		else 
		{	

			$voucher_course_bundle_product_id = $this->common_model->getProdectId('bundle',$voucher_bundle_details['id']);
		}		

		if(isset($this->flashmessage))
			$content['flashmessage']=$this->flashmessage;

		$content['currency_id'] = $this->currId;
		$content['voucher_bundle_details']     		 = $voucher_bundle_details;
		$content['voucher_course_bundle_product_id'] = $voucher_course_bundle_product_id;
		
		
		$content['added_products_array']  = $added_products_array;	
		$content['lang_id'] 			  = $this->language;
		$content['user_logged_in'] 	      = $user_logged_in;		
		$content['translate'] 		      = $this->tr_common;
		$content['view'] 				  = 'voucher_code_select_course_bundle';
		$title['pageTitle'] 			  = 'Login';
		$content['content'] 			  = $title;	
		$this->load->view('user/template_outer',$content); 

	}


	function bundle_enrol_course(){
		

  		if(!isset($this->session->userdata['voucher_code_applied_details']))
		{
			redirect('voucher_code');
		}			
		
		$content = array();
		$added_products_array = array();
		$enrolled_course_array = array();


		$this->tr_common['tr_first_name']      		 = $this->user_model->translate_('First_Name'); 
		$this->tr_common['tr_last_name']       		 = $this->user_model->translate_('last_Name'); 		
		$this->tr_common['tr_email']           		 = $this->user_model->translate_('email'); 
		$this->tr_common['tr_confirm_email']   		 = $this->user_model->translate_('confirm_email'); 
		$this->tr_common['tr_contact_num']     		 = $this->user_model->translate_('contact_num'); 		
		$this->tr_common['tr_create_stylist_id']     = utf8_decode($this->user_model->translate_('create_stylist_id')); 
		$this->tr_common['tr_create_secret_code']    = $this->user_model->translate_('create_secret_code'); 		
		$this->tr_common['tr_confirm_secret_code']   = $this->user_model->translate_('confirm_secret_code'); 		
		$this->tr_common['tr_first_name_required']   = $this->user_model->translate_('first_name_required'); 		
		$this->tr_common['tr_last_name_required']    = $this->user_model->translate_('last_name_required');		
		$this->tr_common['tr_user_name_required']    = $this->user_model->translate_('user_name_required'); 		
		$this->tr_common['tr_email_required']   	 = $this->user_model->translate_('email_required'); 
		$this->tr_common['tr_email_exists']   	     = $this->user_model->translate_('email_exists'); 
		$this->tr_common['tr_valid_email_required']  = $this->user_model->translate_('valid_email_required'); 
		$this->tr_common['tr_required']   	   		 = $this->user_model->translate_('required'); 
		$this->tr_common['tr_confirm_email']   		 = $this->user_model->translate_('confirm_email'); 
		$this->tr_common['tr_registration']     	 = $this->user_model->translate_('registration');
		$this->tr_common['tr_1_personal_information']= $this->user_model->translate_('1_personal_information');
		$this->tr_common['tr_2_upgrade_packages'] 	 = $this->user_model->translate_('2_upgrade_packages');
		$this->tr_common['tr_3_review_and_payment']  = $this->user_model->translate_('3_review_and_payment');
		$this->tr_common['tr_next']   				 = $this->user_model->translate_('_next'); 
		$this->tr_common['tr_step_3_your_details']   = $this->user_model->translate_('step_3_your_details');  
		$this->tr_common['tr_4_confirmation'] 		 = $this->user_model->translate_('4_confirmation');
		$this->tr_common['step_4_buy_now'] 			 = $this->user_model->translate_('step_4_buy_now');		
		$this->tr_common['tr_Email_ucfirst'] 		 = $this->user_model->translate_('Email_ucfirst');


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
		 		 
          $studentdata['with_coupon']='yes';
		  $studentdata['coupon_code'] = $this->session->userdata['voucher_code_applied_details']['vouchercode'];		
		 
		  $studentdata['reg_date']=date("Y-m-d");
		  $this->check_public_user_used_this_voucher_code($studentdata['email']);
		  
		  $this->form_validation->set_rules('fname', 'First name', 'trim|required');
		  $this->form_validation->set_rules('lname', 'Last Name', 'required');	  
		  $this->form_validation->set_rules('user_name', 'UserName', 'required');
		  $this->form_validation->set_rules('pword', 'Password', 'required|min_length[6]|callback_chkpword[Password]');
		  $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email|callback_chkemail[E-mail]');
		 
		  if($this->form_validation->run())
		  { 	
			
				$this->user_model->add_student_temp($studentdata);
				$pre_user_id=$this->db->insert_id();
				$pre_session_array['pre_user_id'] = $pre_user_id;								
				$sess_array = array('voucher_code_pre_user_id' => $pre_session_array);
			    $this->session->set_userdata($sess_array);
				redirect('voucher_code/select_course_bundle');					
		  }		

		}
					
		$this->tr_common['tr_trndimi_ebooks'] = $this->user_model->translate_('trendimi_ebooks');
		$this->tr_common['tr_check_out'] = $this->user_model->translate_('check_out');
	    $this->tr_common['tr_total_price'] = $this->user_model->translate_('total_price');
		 
		if(isset($this->session->userdata['student_logged_in']['id']))
		{
			redirect('voucher_code/course_bundle');
		}		
		
		//$content['added_products_array'] = $added_products_array;	
		$content['lang_id'] 			 = $this->language;	
		$content['translate'] 		     = $this->tr_common;
		$content['view'] 				 = 'voucher_code_enrol_course_bundle';
		$title['pageTitle'] 			 = 'Login';
		$content['content'] 			 = $title;
		$this->load->view('user/template_outer',$content); 

	}


	function add_voucher_course_bundle($bundle_id,$product_id,$currency_id,$source)
	{		
		$selected_values_array = $bundle_id;				
		
		if($this->session->userdata('student_logged_in')){

			$user_id = $this->session->userdata['student_logged_in']['id'];	
		}

		if($this->session->userdata('voucher_code_pre_user_id')){

			$pre_user_id = $this->session->userdata['voucher_code_pre_user_id']['pre_user_id'];	
		}

		$voucher_id = $this->session->userdata['voucher_code_applied_details']['voucher_id'];
				
		if(!$this->session->userdata('voucher_cart_session_id'))
		{	
						
			$product_details = $this->common_model->get_product_details($product_id);
			
			$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
			
			$voucher_details = $this->voucher_code_model->get_voucher($voucher_id);
			
			$product_amount = $org_product_amt = $product_price_details['amount'];
			
			// Product price as per the orignal products	

			$selected_items_org_price = $product_amount;
			
			if($voucher_details[0]->discount_type=='free')
			{			
				$product_amount = 0;
				$discount_amount = $selected_items_org_price;
				$product_price_details['currency_id'] = $currency_id;
			}			
			else if($voucher_details[0]->discount_type=='percentage')
			{	
				$discount_value  = $voucher_details[0]->discount_percentage;				
				$product_amount  = $selected_items_org_price - round(( ($selected_items_org_price * $discount_value) / 100 ),2);									
				$discount_amount = $selected_items_org_price - $product_amount;
			}
			elseif($voucher_details[0]->discount_type=='discount_price')
			{				

			    $currency_id_discount_value = $discount_amount = $this->voucher_code_model->get_amount_for_voucher_code($voucher_id,$currency_id);
				
				if($currency_id_discount_value!='')
				{				
					//$discount_amount = $product_amount-($currency_id_discount_value);					
					$product_amount = $selected_items_org_price - $currency_id_discount_value;

					if($discount_amount<=0)
					{
						$data['err_msg']= 1;
						$data['err_type'] = 'Something went wrong. Plaese contact info@internationalopenacademy.com';					
						echo json_encode($data); 
						exit;	
					}
				
				}
				else
				{
					$data['err_msg']= 1;
					$data['err_type'] = 'Currency not supported. Plaese contact info@internationalopenacademy.com';					
					echo json_encode($data); 
					exit;	
					
				}
			}	

			elseif($voucher_details[0]->discount_type=='offer_price')
			{					
				// Get voucher code product details

				$product_id = $this->common_model->getProdectId('voucher_code',$voucher_id);

				$product_details = $this->common_model->get_product_details($product_id);
			
				$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
				
				$voucher_details = $this->voucher_code_model->get_voucher($voucher_id);
				
				$product_amount = $product_price_details['amount'];

				$discount_amount = $selected_items_org_price - $product_amount;

			}			
					
			
			session_regenerate_id();
			$sess_array = array('voucher_cart_session_id' => session_id()); 	
			$this->session->set_userdata($sess_array);	

			if($this->session->userdata('student_logged_in')){

			$cart_main_insert_array = array("session_id"=>$this->session->userdata('voucher_cart_session_id'),"user_id"=>$user_id,"source"=>$source,"item_count"=>1,"total_cart_amount"=>$product_amount,"currency_id"=>$product_price_details['currency_id'],'voucher_id'=>$voucher_id);
			}

			if($this->session->userdata('voucher_code_pre_user_id')){

				$cart_main_insert_array = array("session_id"=>$this->session->userdata('voucher_cart_session_id'),"pre_user_id"=>$pre_user_id,"source"=>$source,"item_count"=>1,"total_cart_amount"=>$product_amount,"currency_id"=>$product_price_details['currency_id'],'voucher_id'=>$voucher_id);
			}
             if(!$this->session->userdata('student_logged_in') && !$this->session->userdata('voucher_code_pre_user_id')&& $this->session->userdata['voucher_code_applied_details']['product_type']){
  		 	$cart_main_insert_array = array("session_id"=>$this->session->userdata('voucher_cart_session_id'),"user_id"=>0,"source"=>$source,"item_count"=>1,"total_cart_amount"=>$product_amount,"currency_id"=>$product_price_details['currency_id'],'voucher_id'=>$voucher_id);
			}
  		  
		    $cart_main_id = $this->common_model->insert_to_table("sales_cart_main",$cart_main_insert_array);
				
			$user_agent_data = array();
			
			$user_agent_data['cart_main_id']  = $cart_main_id;
			$user_agent_data['session_id'] 	= $this->session->userdata('voucher_cart_session_id');
			$user_agent_data['os'] 			= $this->agent->platform();
			$user_agent_data['browser'] 	   = $this->agent->agent_string();
			
			$this->common_model->insert_to_table("sales_user_agents",$user_agent_data);		
									
			$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$product_id,"item_amount"=>$product_amount,"currency"=>$product_price_details['currency_id'],'discount_amount'=>$discount_amount);					
			
			$cart_items_id = $this->common_model->insert_to_table("sales_cart_items",$item_details_array);		
			
			$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>$product_details[0]->type,"selected_item_ids"=>$bundle_id);
			
			$item_id = $this->common_model->insert_to_table("sales_cart_item_details",$items_array);	
			
			$currency_symbol = $this->common_model->get_currency_symbol_from_id($product_price_details['currency_id']);

			$data['err_msg']= 0;
			$data['amount'] = $product_amount;
			$data['count'] = 1;
			$data['currency_symbol'] = $currency_symbol;
			echo json_encode($data); 
			exit;     	
		}
		else
		{
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('voucher_cart_session_id'));
			foreach($cart_main_details as $cart_main)
			{
				$cart_main_id = $cart_main->id;
				
				$sales_cart_items_id = $this->sales_model->get_cart_items($cart_main_id);					
				
				if(!empty($sales_cart_items_id))
				{
				
					$cart_items_id = $sales_cart_items_id[0]->id;
					$product_details = $this->common_model->get_product_details($product_id);			
					$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
					
					$voucher_details = $this->voucher_code_model->get_voucher($voucher_id);
			
					$product_amount = $org_product_amt = $product_price_details['amount'];
					
					// Product price as per the orignal products						
					$selected_items_org_price = $product_amount;					
					
					if($voucher_details[0]->discount_type=='free')
					{		
						$discount_amount = $selected_items_org_price;			
						$product_amount = 0;	
						$product_price_details['currency_id'] = $currency_id;					
					}			
					else if($voucher_details[0]->discount_type=='percentage')
					{						
						$discount_value  = $voucher_details[0]->discount_percentage;						
						
						$product_amount  = $selected_items_org_price - round(( ($selected_items_org_price * $discount_value) / 100 ),2);									
						$discount_amount = $selected_items_org_price - $product_amount;
					}
					elseif($voucher_details[0]->discount_type=='discount_price')
					{								
						$currency_id_discount_value = $discount_amount = $this->voucher_code_model->get_amount_for_voucher_code($voucher_id,$currency_id);
						
						if($currency_id_discount_value!='')
						{						
							$product_amount = $selected_items_org_price - $currency_id_discount_value;
							if($discount_amount<=0)
							{
								$data['err_msg']= 1;
								$data['err_type'] = 'Something went wrong. Please contact info@internationalopenacademy.com';					
								echo json_encode($data); 
								exit;	
							}
						
						}
						else
						{
							$data['err_msg']= 1;
							$data['err_type'] = 'Currency not supported. Please contact info@internationalopenacademy.com';					
							echo json_encode($data); 
							exit;	
							
						}
					}
					elseif($voucher_details[0]->discount_type=='offer_price')
					{						
						// Get voucher code product details

						$product_id = $this->common_model->getProdectId('voucher_code',$voucher_id);

						$product_details = $this->common_model->get_product_details($product_id);
					
						$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
						
						$voucher_details = $this->voucher_code_model->get_voucher($voucher_id);
						
						$product_amount = $product_price_details['amount'];

						$discount_amount = $selected_items_org_price - $product_amount;

					}										
										
					$item_details_array = array("product_id"=>$product_id,"item_amount"=>$product_amount,"currency"=>$product_price_details['currency_id'],'discount_amount'=>$discount_amount);
					
					$this->sales_model->sales_cart_items_update($cart_items_id,$item_details_array);		
				
					$items_array = array("product_type"=>$product_details[0]->type,"selected_item_ids"=>$bundle_id);
				
					$item_id = $this->sales_model->sales_cart_item_details_update($cart_main_id,$cart_items_id,$items_array);	
					
					$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main->id);
					
					$cart_items_total_amount=@round($cart_items_total_amount,2);
					
					
					$cart_total_items = count($selected_values_array);
					
					$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount);
					$this->sales_model->main_cart_details_update($this->session->userdata('voucher_cart_session_id'),$update_array);
										
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
					
					$voucher_details = $this->voucher_code_model->get_voucher($voucher_id);
			
					$product_amount = $org_product_amt = $product_price_details['amount'];

					// Product price as per the orignal products	
					$selected_items_org_price = $product_amount;
					
					
					if($voucher_details[0]->discount_type=='free')
					{				
						$discount_amount = $selected_items_org_price;	
						$product_amount = 0;
						$product_price_details['currency_id'] = $currency_id;
					}			
					else if($voucher_details[0]->discount_type=='percentage')
					{					
						
						$discount_value  = $voucher_details[0]->discount_percentage;						
						
						$product_amount  = $selected_items_org_price - round(( ($selected_items_org_price * $discount_value) / 100 ),2);									
						$discount_amount = $selected_items_org_price - $product_amount;

					}
					elseif($voucher_details[0]->discount_type=='discount_price')
					{			
					
						$currency_id_discount_value = $discount_amount = $this->voucher_code_model->get_amount_for_voucher_code($voucher_id,$currency_id);
						
						if($currency_id_discount_value!='')
						{				
							//$discount_amount = $product_amount-($currency_id_discount_value);
							$product_amount = $product_amount - $currency_id_discount_value;
							if($discount_amount<=0)
							{
								$data['err_msg']= 1;
								$data['err_type'] = 'Something went wrong. Plaese contact info@internationalopenacademy.com';					
								echo json_encode($data); 
								exit;	
							}
						
						}
						else
						{
							$data['err_msg']= 1;
							$data['err_type'] = 'Currency not supported. Plaese contact info@internationalopenacademy.com';					
							echo json_encode($data); 
							exit;	
							
						}
					}	
					elseif($voucher_details[0]->discount_type=='offer_price')
					{	

						// Get voucher code product details

						$product_id = $this->common_model->getProdectId('voucher_code',$voucher_id);

						$product_details = $this->common_model->get_product_details($product_id);
					
						$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
						
						$voucher_details = $this->voucher_code_model->get_voucher($voucher_id);
						
						$product_amount = $product_price_details['amount'];

						$discount_amount = $selected_items_org_price - $product_amount;

					}						
										
					$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$product_id,"item_amount"=>$product_amount,"currency"=>$product_price_details['currency_id'],'discount_amount'=>$discount_amount);
					
					$cart_items_id = $this->common_model->insert_to_table("sales_cart_items",$item_details_array);		
				
					$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>$product_details[0]->type,"selected_item_ids"=>$bundle_id);
				
					$item_id = $this->common_model->insert_to_table("sales_cart_item_details",$items_array);	
					
					$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main->id);
					
					$cart_items_total_amount=@round($cart_items_total_amount,2);
					
					
					$cart_total_items = count($this->sales_model->get_cart_items($cart_main->id));
					
					$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount);
					$this->sales_model->main_cart_details_update($this->session->userdata('voucher_cart_session_id'),$update_array);
										
					$currency_symbol = $this->common_model->get_currency_symbol_from_id($product_price_details['currency_id']);
					
					$data['err_msg']= 0;
					$data['amount'] = $cart_items_total_amount;
					$data['count'] = count($selected_values_array);
					$data['currency_symbol'] = $currency_symbol;
					echo json_encode($data); 
					exit;
				
				}
				//$cart_items = $this->sales_model->get_cart_items($cart_main->id);
			}
		}
			
	
	  


	  }

	  
	  function course()
	  {
	  		if(!isset($this->session->userdata['voucher_code_applied_details']))
			{
				redirect('voucher_code');
			}			
			
			$this->check_user_used_this_voucher_code();

			$content = array();
			$added_products_array = array();
			$enrolled_course_array = array();
			
			$voucher_course_array = $this->voucher_code_model->get_course_for_voucher($this->session->userdata['voucher_code_applied_details']['voucher_id']);
	
			$content['currency_id'] = $this->currId;
			
			
			$course_categories = $this->voucher_code_model->get_courses_categories();
			
			$voucher_code_course_id_array = explode(',',$voucher_course_array[0]->course_ebook_ids);
			foreach($course_categories as $course_cat){
				
				$category_name_array[$course_cat->id] = $course_cat->category_name;
				
				$category_wise_courses[$course_cat->id]= $this->voucher_code_model->get_category_wise_courses($voucher_code_course_id_array,''.$course_cat->id.'');
			}
					
			
			$course_array = $this->voucher_code_model->get_courses($voucher_code_course_id_array,'');
						
			$this->tr_common['tr_trndimi_ebooks'] = $this->user_model->translate_('trendimi_ebooks');
			$this->tr_common['tr_check_out'] = $this->user_model->translate_('check_out');
		    $this->tr_common['tr_total_price'] = $this->user_model->translate_('total_price');
			 
			if(!isset($this->session->userdata['student_logged_in']['id']))
			{
				$user_logged_in = false;
			}
			else
			{
				$user_logged_in = true;
				$user_id = $this->session->userdata['student_logged_in']['id'];	
				$enrolled_course_array = $this->voucher_code_model->get_enrolled_courses($user_id);
			}
			
			if($this->session->userdata('voucher_cart_session_id'))
			{			
				$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('voucher_cart_session_id'));			
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

			$voucher_id = $this->session->userdata['voucher_code_applied_details']['voucher_id'];
			if($this->session->userdata['voucher_code_applied_details']['discount_type'] == 'offer_price')
			{				
				$voucher_course_product_id = $this->common_model->getProdectId('voucher_code',$voucher_id);
			}	
			else 
			{				
				$voucher_course_product_id = $this->common_model->getProdectId($this->session->userdata['voucher_code_applied_details']['product_type'],$voucher_course_array[0]->item_count_x);
			}		

			if(isset($this->flashmessage))
			$content['flashmessage']=$this->flashmessage;
			$content['added_products_array'] = $added_products_array;	
			$content['voucher_course_product_id'] = $voucher_course_product_id;	
			$content['lang_id'] 			 = $this->language;
			$content['user_logged_in'] 	     = $user_logged_in;
			$content['voucher_course_array'] = $voucher_course_array;	
			$content['course_categories'] 	 = $course_categories;	
			$content['category_name_array']  = $category_name_array;	
			$content['category_wise_courses'] = $category_wise_courses;	
			
			  
			
			$content['course_array'] 		 = $course_array;	
			$content['translate'] 		     = $this->tr_common;
			$content['view'] 				 = 'voucher_code_course_new';
			$title['pageTitle'] 			 = 'Login';
			$content['content'] 			 = $title;
			$this->load->view('user/template_outer',$content); 
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
			$content['pre_user_id'] = $pre_user_id = $this->session->userdata['voucher_code_pre_user_id']['pre_user_id'];
			
		}
			
		$curr_id= $this->currId;	
		$added_pack_id = array();
		if($this->session->userdata('voucher_cart_session_id'))
			{			
				$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('voucher_cart_session_id'));			
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
		
		//$package_details = $this->package_model->get_packages_for_deal('non_user');	
		$package_details = $this->package_model->get_packages('non_user');
		
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
		$data['view'] = 'voucher_package';
		$data['content'] = $content;
		$this->load->view('user/template_outer',$data);
		
	

	  }


	function package_read_more($lang_id,$curr_id)
	{
		$content = array();
				
		$this->tr_common['tr_back']    			= $this->user_model->translate_('back');
		$this->tr_common['tr_personal_details']    = $this->user_model->translate_('personal_details');		
		$this->tr_common['tr_packages']    		= $this->user_model->translate_('packages');
		$this->tr_common['tr_payment_details']     = $this->user_model->translate_('payment_details');
		$this->tr_common['tr_registration']    	= $this->user_model->translate_('registration');
		$this->tr_common['tr_SIGN_UP']   =$this->user_model->translate_('SIGN_UP');
		
		$this->tr_common['tr_1_personal_information'] = $this->user_model->translate_('1_personal_information');
		$this->tr_common['tr_2_upgrade_packages'] = $this->user_model->translate_('2_upgrade_packages');
		$this->tr_common['tr_3_review_and_payment'] = $this->user_model->translate_('3_review_and_payment');
		$this->tr_common['tr_4_confirmation'] = $this->user_model->translate_('4_confirmation');
		
		$package_read_more = $this->package_model->get_read_more_details($lang_id,$curr_id);
		$content['package_read_more'] = $package_read_more[0]->description;
		$content['package_details']   = $this->package_model->get_package_details('non_user',$curr_id);	
		
		$data['translate'] = $this->tr_common;
		$data['view'] = 'voucher_code_package_read_more';
		$data['content'] = $content;
		$this->load->view('user/template_outer',$data);
		
	}


	  function add_package_to_cart($product_id,$currency_id,$package_id,$product_type,$source)
	  {
		
		if(!$this->session->userdata('voucher_cart_session_id'))
		{
			redirect('voucher_code');
		}
		else
		{
			
			$cart_main_details = $this->sales_model->get_cart_main_details_package($this->session->userdata('voucher_cart_session_id'));
			foreach($cart_main_details as $cart_main)
			{
				$cart_main_id = $cart_main->id;
				$product_in_cart = $this->sales_model->check_product_type_exist_in_cart($cart_main->id,$product_type);
				if(empty($product_in_cart))
				{			
					$product_details = $this->common_model->get_product_details($product_id);			
					$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);


					/*If adding package includes course subscription,need to remove the course price aleready added. All courses included in course subcription*/

					$products_in_package = explode(',',$this->package_model->get_products_in_package($package_id));							
																							
					$course_subscription = $this->common_model->get_product_by_type('course_subscription');				

					if(in_array($course_subscription[0]->id,$products_in_package))
					{
						
						$cart_main_details = $this->sales_model->get_cart_main_details_package($this->session->userdata('voucher_cart_session_id'));
						foreach($cart_main_details as $cart_main)
						{
							$cart_main_id = $cart_main->id;					
							$product_in_cart = $this->sales_model->check_product_type_exist_in_cart_new($cart_main->id,'course');
							if(!empty($product_in_cart))
							{
								foreach ($product_in_cart as $prod) {

									$cart_items_id 		 = $prod->cart_items_id;					
									$cart_items_disc_amt = $prod->item_amount + $prod->discount_amount; 	
									$item_details_array  = array("item_amount"=>0,"discount_amount"=>$cart_items_disc_amt);
									//sales_cart_items_update					
									$this->sales_model->sales_cart_items_update($cart_items_id,$item_details_array);									
								}

							}
							else
							{						
								$product_in_cart = $this->sales_model->check_product_type_exist_in_cart_new($cart_main->id,'voucher_code');
								if(!empty($product_in_cart))
								{							
									foreach ($product_in_cart as $prod) {							

										$voucher_product_details = $this->common_model->get_product_details($prod->product_id);								
										$vouhcer_id = $voucher_product_details[0]->item_id;
										$voucher_code_details = $this->voucher_code_model->get_course_for_voucher($vouhcer_id);	
										// If redeemed voucher code is course type
										if(!empty($voucher_code_details))
										{
											$cart_items_id = $prod->cart_items_id;			
											$cart_items_disc_amt = $prod->item_amount + $prod->discount_amount; 	
											$item_details_array = array("item_amount"=>0,"discount_amount"=>$cart_items_disc_amt);								
											//sales_cart_items_update					
											$this->sales_model->sales_cart_items_update($cart_items_id,$item_details_array);	
										}

									}
									
								}
							}

							$product_in_cart = $this->sales_model->check_product_type_exist_in_cart_new($cart_main->id,'ebooks');
							if(!empty($product_in_cart))
							{							
								foreach ($product_in_cart as $prod) {						

									$this->sales_model->delete_item_from_cart_by_product_id($cart_main_id,$prod->sales_cart_items_id,$prod->product_id);									

								}
								
							}
						}
					}

					/* End course subscription included check */

					$discount_amount = $product_price_details['fake_amount'] - $product_price_details['amount'];
										
					$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$product_id,"item_amount"=>$product_price_details['amount'],"currency"=>$product_price_details['currency_id'],'discount_amount'=>$discount_amount);
					
					$cart_items_id = $this->common_model->insert_to_table("sales_cart_items",$item_details_array);		
				
					$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>$product_details[0]->type,"selected_item_ids"=>$package_id);
				
					$item_id = $this->common_model->insert_to_table("sales_cart_item_details",$items_array);	
					
					$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main->id);
					
					$cart_items_total_amount=@round($cart_items_total_amount,2);
					
					
					$cart_total_items = count($this->sales_model->get_cart_items($cart_main->id));
					
					$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount);
					$this->sales_model->main_cart_details_update($this->session->userdata('voucher_cart_session_id'),$update_array);
										
					$currency_symbol = $this->common_model->get_currency_symbol_from_id($product_price_details['currency_id']);

					$sess_array = array('voucher_added_package_id' => $package_id);			
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
					
					/*If adding package includes course subscription,need to remove the course price aleready added. All courses included in course subcription*/

					$products_in_package = explode(',',$this->package_model->get_products_in_package($package_id));							
																							
					$course_subscription = $this->common_model->get_product_by_type('course_subscription');				

					if(in_array($course_subscription[0]->id,$products_in_package))
					{
						
						$cart_main_details = $this->sales_model->get_cart_main_details_package($this->session->userdata('voucher_cart_session_id'));
						foreach($cart_main_details as $cart_main)
						{
							$cart_main_id = $cart_main->id;					
							$product_in_cart = $this->sales_model->check_product_type_exist_in_cart_new($cart_main->id,'course');
							if(!empty($product_in_cart))
							{
								foreach ($product_in_cart as $prod) {

									$cart_items_id 		 = $prod->cart_items_id;					
									$cart_items_disc_amt = $prod->item_amount + $prod->discount_amount; 	
									$item_details_array  = array("item_amount"=>0,"discount_amount"=>$cart_items_disc_amt);
									//sales_cart_items_update					
									$this->sales_model->sales_cart_items_update($cart_items_id,$item_details_array);									
								}

							}
							else
							{						
								$product_in_cart = $this->sales_model->check_product_type_exist_in_cart_new($cart_main->id,'voucher_code');
								if(!empty($product_in_cart))
								{							
									foreach ($product_in_cart as $prod) {							

										$product_details = $this->common_model->get_product_details($prod->product_id);								
										$vouhcer_id = $product_details[0]->item_id;
										$voucher_code_details = $this->voucher_code_model->get_course_for_voucher($vouhcer_id);	
										// If redeemed voucher code is course type
										if(!empty($voucher_code_details))
										{
											$cart_items_id = $prod->cart_items_id;			
											$cart_items_disc_amt = $prod->item_amount + $prod->discount_amount; 	
											$item_details_array = array("item_amount"=>0,"discount_amount"=>$cart_items_disc_amt);								
											//sales_cart_items_update					
											$this->sales_model->sales_cart_items_update($cart_items_id,$item_details_array);	
										}

									}
									
								}
							}

							$product_in_cart = $this->sales_model->check_product_type_exist_in_cart_new($cart_main->id,'ebooks');
							if(!empty($product_in_cart))
							{							
								foreach ($product_in_cart as $prod) {						

									$this->sales_model->delete_item_from_cart_by_product_id($cart_main_id,$prod->sales_cart_items_id,$prod->product_id);									

								}
								
							}
						}
					}

					// If removing package includes course subscription, update the course price to actual price, that we changed to 0 while adding the course subscription included package
					$products_in_package = explode(',',$this->package_model->get_products_in_package($removed_package_id));							
																							
					$course_subscription = $this->common_model->get_product_by_type('course_subscription');				

					if(in_array($course_subscription[0]->id,$products_in_package))
					
					{
						$cart_main_details = $this->sales_model->get_cart_main_details_package($this->session->userdata('voucher_cart_session_id'));
						foreach($cart_main_details as $cart_main)
						{
							$cart_main_id = $cart_main->id;					
							$product_in_cart = $this->sales_model->check_product_type_exist_in_cart_new($cart_main->id,'course');
							if(!empty($product_in_cart))
							{
								foreach ($product_in_cart as $prod) {

									if($prod->item_amount ==0){

										$cart_items_id 		 = $prod->cart_items_id;

										$product_price_details = $this->common_model->getProductFee($prod->product_id,$prod->currency);							

										$cart_items_disc_amt = $prod->discount_amount;

										if($prod->discount_amount > $product_price_details['amount'] ){

										$cart_items_disc_amt = $prod->discount_amount - $product_price_details['amount']; 	

										}
										$item_details_array  = array("item_amount"=>$product_price_details['amount'],"discount_amount"=>$cart_items_disc_amt);	
										//sales_cart_items_update					
										$this->sales_model->sales_cart_items_update($cart_items_id,$item_details_array);

									}
																		
								}

							}
							else
							{						
								$product_in_cart = $this->sales_model->check_product_type_exist_in_cart_new($cart_main->id,'voucher_code');
								if(!empty($product_in_cart))
								{							
									foreach ($product_in_cart as $prod) {							

										$product_details = $this->common_model->get_product_details($prod->product_id);								
										$vouhcer_id = $product_details[0]->item_id;
										$voucher_code_details = $this->voucher_code_model->get_course_for_voucher($vouhcer_id);	
										// If redeemed voucher code is course type
										if(!empty($voucher_code_details))
										{
											$cart_items_id = $prod->cart_items_id;			
											if($prod->item_amount ==0){

												$cart_items_id 		 = $prod->cart_items_id;
												$product_price_details = $this->common_model->getProductFee($prod->product_id,$prod->currency);	
												$cart_items_disc_amt = $prod->discount_amount;
												if($prod->discount_amount > $product_price_details['amount'] ){

													$cart_items_disc_amt = $prod->discount_amount - $product_price_details['amount']; 	

												}
												$item_details_array  = array("item_amount"=>$product_price_details['amount'],"discount_amount"=>$cart_items_disc_amt);					
												//sales_cart_items_update					
												$this->sales_model->sales_cart_items_update($cart_items_id,$item_details_array);

											}
										}

									}
									
								}
							}

							$product_in_cart = $this->sales_model->check_product_type_exist_in_cart_new($cart_main->id,'ebooks');
							if(!empty($product_in_cart))
							{							
								foreach ($product_in_cart as $prod) {						

									$this->sales_model->delete_item_from_cart_by_product_id($cart_main_id,$prod->sales_cart_items_id,$prod->product_id);									

								}
								
							}
						}
					}

					/* End course subscription included check */
							
					/* REmoved already added package and add the package selected  */	
					$product_details = $this->common_model->get_product_details($product_id);			
					$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);				

					$discount_amount = $product_price_details['fake_amount'] - $product_price_details['amount'];
										
					$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$product_id,"item_amount"=>$product_price_details['amount'],"currency"=>$product_price_details['currency_id'],'discount_amount'=>$discount_amount);
					
					$cart_items_id = $this->common_model->insert_to_table("sales_cart_items",$item_details_array);		
				
					$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>$product_details[0]->type,"selected_item_ids"=>$package_id);
				
					$item_id = $this->common_model->insert_to_table("sales_cart_item_details",$items_array);	
					
					$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main->id);
					
					$cart_items_total_amount=@round($cart_items_total_amount,2);
					
					
					$cart_total_items = count($this->sales_model->get_cart_items($cart_main->id));
					
					$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount);
					$this->sales_model->main_cart_details_update($this->session->userdata('voucher_cart_session_id'),$update_array);
										
					$currency_symbol = $this->common_model->get_currency_symbol_from_id($product_price_details['currency_id']);

					$sess_array = array('voucher_added_package_id' => $package_id);			
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



	function remove_package_from_cart($product_id,$package_id)
	{
		if($this->session->userdata('voucher_cart_session_id'))
		{
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('voucher_cart_session_id'));	
			$cart_main_id = $cart_main_details[0]->id;
			$cart_details_by_product = $this->sales_model->get_cart_items_by_product_id($cart_main_id,$product_id);
			$cart_details_id = $cart_details_by_product[0]->id;			
			$this->sales_model->delete_item_from_cart_by_product_id($cart_main_id,$cart_details_id,$product_id);				
			

			/*If removing package that includes course subscription, need to add the course price removed while adding the same package.*/

			$products_in_package = explode(',',$this->package_model->get_products_in_package($package_id));							
																					
			$course_subscription = $this->common_model->get_product_by_type('course_subscription');				

			if(in_array($course_subscription[0]->id,$products_in_package))
			{
				
				$cart_main_details = $this->sales_model->get_cart_main_details_package($this->session->userdata('voucher_cart_session_id'));
				foreach($cart_main_details as $cart_main)
				{
					$cart_main_id = $cart_main->id;					
					$product_in_cart = $this->sales_model->check_product_type_exist_in_cart_new($cart_main->id,'course');
					if(!empty($product_in_cart))
					{
						foreach ($product_in_cart as $prod) {

							$cart_items_id 		 = $prod->cart_items_id;	

							$product_price_details = $this->common_model->getProductFee($prod->product_id,$prod->currency);							

							$cart_items_disc_amt = $prod->discount_amount;

							if($prod->discount_amount > $product_price_details['amount'] ){

							$cart_items_disc_amt = $prod->discount_amount - $product_price_details['amount']; 	

							}
							$item_details_array  = array("item_amount"=>$product_price_details['amount'],"discount_amount"=>$cart_items_disc_amt);
							//sales_cart_items_update					
							$this->sales_model->sales_cart_items_update($cart_items_id,$item_details_array);									
						}

					}
					else
					{						
						$product_in_cart = $this->sales_model->check_product_type_exist_in_cart_new($cart_main->id,'voucher_code');
						if(!empty($product_in_cart))
						{							
							foreach ($product_in_cart as $prod) {							

								$product_details = $this->common_model->get_product_details($prod->product_id);								
								$vouhcer_id = $product_details[0]->item_id;
								$voucher_code_details = $this->voucher_code_model->get_course_for_voucher($vouhcer_id);	
								// If redeemed voucher code is course type
								if(!empty($voucher_code_details))
								{
									$cart_items_id = $prod->cart_items_id;			
									$product_price_details = $this->common_model->getProductFee($prod->product_id,$prod->currency);							

									$cart_items_disc_amt = $prod->discount_amount;

									if($prod->discount_amount > $product_price_details['amount'] ){

									$cart_items_disc_amt = $prod->discount_amount - $product_price_details['amount']; 	

									}
									$item_details_array  = array("item_amount"=>$product_price_details['amount'],"discount_amount"=>$cart_items_disc_amt);								
									//sales_cart_items_update					
									$this->sales_model->sales_cart_items_update($cart_items_id,$item_details_array);	
								}

							}
							
						}
					}


					$product_in_cart = $this->sales_model->check_product_type_exist_in_cart_new($cart_main->id,'ebooks');
					if(!empty($product_in_cart))
					{							
						foreach ($product_in_cart as $prod) {						

							$this->sales_model->delete_item_from_cart_by_product_id($cart_main_id,$prod->sales_cart_items_id,$prod->product_id);									

						}
						
					}

				}
			}

			/* End course subscription included check */

			
			$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main_id);				
			$cart_items_total_amount = @round($cart_items_total_amount,2);		
			$cart_total_items = count($this->sales_model->get_cart_items($cart_main_id));		
			$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount);	
				
			$this->sales_model->main_cart_details_update($this->session->userdata('voucher_cart_session_id'),$update_array);	
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('voucher_cart_session_id'));
			/*echo "<br>After updation";
			echo "<pre>";
			print_r($cart_main_details);	*/
			
			$currency_symbol = $this->common_model->get_currency_symbol_from_id($cart_main_details[0]->currency_id);
			$this->session->unset_userdata('voucher_added_package_id');
			
			$data['err_msg']= 0;
			$data['amount'] = $cart_main_details[0]->total_cart_amount;     
			$data['count'] = $cart_main_details[0]->item_count;
			$data['currency_symbol'] = $currency_symbol;
			echo json_encode($data); 
			exit;   
			
		}
		
	}


	function package_ebooks(){

		$content = array();
		$content['added_ebook_array'] = array();
		$cart_main_id 		   = $this->package_model->get_cart_main_id_from_session_id($this->session->userdata('voucher_cart_session_id'));		
		$package_cart_contents = $this->sales_model->check_product_type_exist_in_cart($cart_main_id,'package');	

		/*echo "Cart main id  ".$cart_main_id;
		echo "<pre>";
		print_r($package_cart_contents);*/
	
		$this->tr_common['tr_total_price'] =$this->user_model->translate_('total_price');
		$this->tr_common['tr_complete_registraion'] = $this->user_model->translate_('complete_registraion');	
		if(!empty($package_cart_contents))
		{

			$this->load->model('ebook_model');
			$added_package_id = $this->session->userdata('voucher_added_package_id');

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
					redirect('/voucher_code/carthhh/');
				}

			}		

					
			$ebook_array = $this->ebook_model->fetchEbookByLang($this->language);		
			if($this->session->userdata('voucher_cart_session_id'))
			{			
				$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('voucher_cart_session_id'));			
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

			$content['ebook_selection_limit'] = $ebook_selection_limit;
			$content['ebook_product_id'] 	  = $ebook_product_id;
			$content['currency_id'] 		  = $this->currId;
			$content['lang_id'] 			  = $this->language;
			$content['ebook_array'] 		  = $ebook_array;	
			$data['view'] 					  = 'voucher_package_ebooks';	
			$data['translate'] 				  = $this->tr_common;	
			$data['content'] 				  = $content;			
			$this->load->view('user/template_outer',$data);	
		}
		else
		{
			redirect('/voucher_code/cart/');
		}
	}


	function validate_package_products_added(){

		$validation = true;
		$voucher_remaining_note='';

		if($this->session->userdata('voucher_added_package_id')){

			$added_package_id = $this->session->userdata('voucher_added_package_id');

			$products_in_package = explode(',',$this->package_model->get_products_in_package($added_package_id));
																							
			$course_subscription = $this->common_model->get_product_by_type('course_subscription');	

			$cart_main_id  = $this->package_model->get_cart_main_id_from_session_id($this->session->userdata('voucher_cart_session_id'));

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

	function enrol_course()
	{

  		if(!isset($this->session->userdata['voucher_code_applied_details']))
		{
			redirect('voucher_code');
		}			
		
		$content = array();
		$added_products_array = array();
		$enrolled_course_array = array();


		$this->tr_common['tr_first_name']   =$this->user_model->translate_('First_Name'); 
		$this->tr_common['tr_last_name']   =$this->user_model->translate_('last_Name'); 		
		$this->tr_common['tr_email']   =$this->user_model->translate_('email'); 
		$this->tr_common['tr_confirm_email']   =$this->user_model->translate_('confirm_email'); 
		$this->tr_common['tr_contact_num']   =$this->user_model->translate_('contact_num'); 	
		  
		
		$this->tr_common['tr_create_stylist_id']   =utf8_decode($this->user_model->translate_('create_stylist_id')); 
		$this->tr_common['tr_create_secret_code']   =$this->user_model->translate_('create_secret_code'); 		
		$this->tr_common['tr_confirm_secret_code']   =$this->user_model->translate_('confirm_secret_code'); 
		 
		
		$this->tr_common['tr_first_name_required']  =$this->user_model->translate_('first_name_required'); 		
		$this->tr_common['tr_last_name_required']   =$this->user_model->translate_('last_name_required');		
		$this->tr_common['tr_user_name_required']   =$this->user_model->translate_('user_name_required'); 		
		$this->tr_common['tr_email_required']   	   =$this->user_model->translate_('email_required'); 
		$this->tr_common['tr_email_exists']   	   =$this->user_model->translate_('email_exists'); 
		$this->tr_common['tr_valid_email_required']   	   =$this->user_model->translate_('valid_email_required'); 
		$this->tr_common['tr_required']   	   		 =$this->user_model->translate_('required'); 
		$this->tr_common['tr_confirm_email']   =$this->user_model->translate_('confirm_email'); 
		
		

		$this->tr_common['tr_registration']     = $this->user_model->translate_('registration');
		$this->tr_common['tr_1_personal_information'] = $this->user_model->translate_('1_personal_information');
		$this->tr_common['tr_2_upgrade_packages'] = $this->user_model->translate_('2_upgrade_packages');
		$this->tr_common['tr_3_review_and_payment'] = $this->user_model->translate_('3_review_and_payment');

		 $this->tr_common['tr_next']   =$this->user_model->translate_('_next'); 

		$this->tr_common['tr_step_3_your_details']   	   	=$this->user_model->translate_('step_3_your_details');  
		$this->tr_common['tr_4_confirmation'] = $this->user_model->translate_('4_confirmation');

		$this->tr_common['step_4_buy_now'] = $this->user_model->translate_('step_4_buy_now');		
		$this->tr_common['tr_Email_ucfirst'] =$this->user_model->translate_('Email_ucfirst');


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
		 		 
          $studentdata['with_coupon']='yes';
		  $studentdata['coupon_code'] = $this->session->userdata['voucher_code_applied_details']['vouchercode'];		
		 
		  $studentdata['reg_date']=date("Y-m-d");
		  
		  $this->check_public_user_used_this_voucher_code($studentdata['email']);

		  $this->form_validation->set_rules('fname', 'First name', 'trim|required');
		  $this->form_validation->set_rules('lname', 'Last Name', 'required');	  
		  $this->form_validation->set_rules('user_name', 'UserName', 'required');
		  $this->form_validation->set_rules('pword', 'Password', 'required|min_length[6]|callback_chkpword[Password]');
		  $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email|callback_chkemail[E-mail]');
		 
		  if($this->form_validation->run())
		  { 	
			
				$this->voucher_code_model->add_student_temp($studentdata);
				$pre_user_id=$this->db->insert_id();

				$pre_session_array=array('pre_user_id' => $pre_user_id);								
				$sess_array = array('voucher_code_pre_user_id' => $pre_session_array);
			    $this->session->set_userdata($sess_array);
				redirect('voucher_code/select_course');
					
		  }
		

		}
					
		$this->tr_common['tr_trndimi_ebooks'] = $this->user_model->translate_('trendimi_ebooks');
		$this->tr_common['tr_check_out'] = $this->user_model->translate_('check_out');
	    $this->tr_common['tr_total_price'] = $this->user_model->translate_('total_price');
		 
		if(isset($this->session->userdata['student_logged_in']['id']))
		{
			redirect('voucher_code/course');
		}
		
		
		//$content['added_products_array'] = $added_products_array;	
		$content['lang_id'] 			 = $this->language;
	//	$content['user_logged_in'] 	     = $user_logged_in;
	//	$content['voucher_course_array'] = $voucher_course_array;	
	//	$content['course_array'] 		 = $course_array;	
		$content['translate'] 		     = $this->tr_common;
		$content['view'] 				 = 'voucher_code_enrol_course';
		$title['pageTitle'] 			 = 'Login';
		$content['content'] 			 = $title;
		$this->load->view('user/template_outer',$content); 


	  }



	function select_course()
	{
  		if(!isset($this->session->userdata['voucher_code_applied_details']))
		{
			redirect('voucher_code');
		}			
		
		$content = array();
		$added_products_array = array();
		$enrolled_course_array = array();

		$voucher_course_array = $this->voucher_code_model->get_course_for_voucher($this->session->userdata['voucher_code_applied_details']['voucher_id']);
	
		$voucher_code_course_id_array = explode(',',$voucher_course_array[0]->course_ebook_ids);
				
		$course_array = $this->voucher_code_model->get_courses($voucher_code_course_id_array);


		if($this->session->userdata('voucher_cart_session_id'))
		{			
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('voucher_cart_session_id'));			
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

		$voucher_id = $this->session->userdata['voucher_code_applied_details']['voucher_id'];
		if($this->session->userdata['voucher_code_applied_details']['discount_type'] == 'offer_price')
		{				
			$voucher_course_product_id = $this->common_model->getProdectId('voucher_code',$voucher_id);
		}	
		else 
		{				
			$voucher_course_product_id = $this->common_model->getProdectId($this->session->userdata['voucher_code_applied_details']['product_type'],$voucher_course_array[0]->item_count_x);
		}	


		$this->tr_common['tr_trndimi_ebooks'] = $this->user_model->translate_('trendimi_ebooks');
		$this->tr_common['tr_check_out'] = $this->user_model->translate_('check_out');
	    $this->tr_common['tr_total_price'] = $this->user_model->translate_('total_price');

		$content['added_products_array'] = $added_products_array;	
		$content['lang_id'] 			 = $this->language;
		//$content['user_logged_in'] 	     = $user_logged_in;
		$content['voucher_course_product_id'] = $voucher_course_product_id;	
		$content['voucher_course_array'] = $voucher_course_array;	
		$content['course_array'] 		 = $course_array;	
		$content['translate'] 		     = $this->tr_common;
		$content['view'] 				 = 'voucher_code_select_course';
		$title['pageTitle'] 			 = 'Login';
		$content['content'] 			 = $title;
		$this->load->view('user/template_outer',$content); 
  	}



	function tool_kit(){


	  	if(!isset($this->session->userdata['voucher_code_applied_details']))
		{
			redirect('voucher_code');
		}	

		$this->check_user_used_this_voucher_code();	

		$content = array();
		$added_products_array   = array();
		$voucher_id  = $this->session->userdata['voucher_code_applied_details']['voucher_id'];
		$voucher_tool_kit_details = $this->voucher_code_model->get_voucher_products_other($voucher_id);
		
		if(!isset($this->session->userdata['student_logged_in']['id']))
		{
			$user_logged_in = false;
		}
		else
		{
			$user_logged_in = true;
		}
		
		$this->tr_common['tr_trndimi_ebooks'] = $this->user_model->translate_('trendimi_ebooks');
		$this->tr_common['tr_check_out'] = $this->user_model->translate_('check_out');
    	$this->tr_common['tr_total_price'] = $this->user_model->translate_('total_price');

		if($this->session->userdata('voucher_cart_session_id'))
		{			
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('voucher_cart_session_id'));			
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
		
		$voucher_tool_kit_product_id = $voucher_tool_kit_details[0]->product_id;		


		if($this->session->userdata['voucher_code_applied_details']['discount_type'] == 'offer_price')
		{				
			$voucher_tool_kit_product_id = $this->common_model->getProdectId('voucher_code',$voucher_id);
		}	
		else 
		{				
			$voucher_tool_kit_product_id = $voucher_tool_kit_details[0]->product_id;		
		}	
		
		$product_details = $this->common_model->get_product_details($voucher_tool_kit_details[0]->product_id);
		$tool_kit_details = $this->voucher_code_model->get_tool_kit_details($product_details[0]->item_id);	
		
		$countrylist=$this->voucher_code_model->get_countries();
		$content['countries'] = $countrylist;
		if(isset($this->flashmessage))
			$content['flashmessage']=$this->flashmessage;

		$content['currency_id'] 			    = $this->currId;
		$content['voucher_tool_kit_product_id'] = $voucher_tool_kit_product_id;
		$content['voucher_tool_kit_details'] 	= $voucher_tool_kit_details;	
		$content['tool_kit_details'] 	      	= $tool_kit_details;	
		$content['added_products_array']  	 	= $added_products_array;	
		$content['lang_id'] 			  	 	= $this->language;
		$content['user_logged_in'] 	      	 	= $user_logged_in;		
		$content['translate'] 		      	 	= $this->tr_common;
		$content['view'] 				  	 	= 'voucher_code_tool_kit';
		$title['pageTitle'] 			  	 	= 'Login';
		$content['content'] 			  	 	= $title;
		$this->load->view('user/template_outer',$content); 	

	}



	  /**
	 * [add_voucher_course_bundle Add tool kit to cart]	
	 * @param [type] $product_id  [product id]
	 * @param [type] $currency_id [Currency id]
	 * @param [type] $source      [Adding source]
	 */
	function add_voucher_tool_kit($product_id,$currency_id,$source)
	{		
		$selected_id = 0;				
		
		if($this->session->userdata('student_logged_in')){

			$user_id = $this->session->userdata['student_logged_in']['id'];	
		}

		if($this->session->userdata('voucher_code_pre_user_id')){

			$pre_user_id = $this->session->userdata['voucher_code_pre_user_id']['pre_user_id'];	
		}

		$voucher_id = $this->session->userdata['voucher_code_applied_details']['voucher_id'];
				
		if(!$this->session->userdata('voucher_cart_session_id'))
		{	
						
			$product_details = $this->common_model->get_product_details($product_id);
			$selected_id = $product_details[0]->item_id;
			
			$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
			
			$voucher_details = $this->voucher_code_model->get_voucher($voucher_id);
			
			$product_amount = $org_product_amt = $product_price_details['amount'];
			
			// Product price as per the orignal products	

			$selected_items_org_price = $product_amount;
			
			if($voucher_details[0]->discount_type=='free')
			{			
				$product_amount = 0;
				$discount_amount = $selected_items_org_price;
				$product_price_details['currency_id'] = $currency_id;
			}			
			else if($voucher_details[0]->discount_type=='percentage')
			{	
				$discount_value  = $voucher_details[0]->discount_percentage;				
				$product_amount  = $selected_items_org_price - round(( ($selected_items_org_price * $discount_value) / 100 ),2);									
				$discount_amount = $selected_items_org_price - $product_amount;
			}
			elseif($voucher_details[0]->discount_type=='discount_price')
			{				

			    $currency_id_discount_value = $discount_amount = $this->voucher_code_model->get_amount_for_voucher_code($voucher_id,$currency_id);
				
				if($currency_id_discount_value!='')
				{				
					//$discount_amount = $product_amount-($currency_id_discount_value);					
					$product_amount = $selected_items_org_price - $currency_id_discount_value;

					if($discount_amount<=0)
					{
						$data['err_msg']= 1;
						$data['err_type'] = 'Something went wrong. Please contact info@trendimi.com';					
						echo json_encode($data); 
						exit;	
					}
				
				}
				else
				{
					$data['err_msg']= 1;
					$data['err_type'] = 'Currency not supported. Please contact info@trendimi.com';					
					echo json_encode($data); 
					exit;	
					
				}
			}	

			elseif($voucher_details[0]->discount_type=='offer_price')
			{					
				// Get voucher code product details

				$product_id = $this->common_model->getProdectId('voucher_code',$voucher_id);

				$product_details = $this->common_model->get_product_details($product_id);
			
				$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
				
				$voucher_details = $this->voucher_code_model->get_voucher($voucher_id);
				
				$product_amount = $product_price_details['amount'];

				$discount_amount = $selected_items_org_price - $product_amount;

			}			
					
			
			session_regenerate_id();
			$sess_array = array('voucher_cart_session_id' => session_id()); 	
			$this->session->set_userdata($sess_array);	

			if($this->session->userdata('student_logged_in')){

			$cart_main_insert_array = array("session_id"=>$this->session->userdata('voucher_cart_session_id'),"user_id"=>$user_id,"source"=>$source,"item_count"=>1,"total_cart_amount"=>$product_amount,"currency_id"=>$product_price_details['currency_id'],'voucher_id'=>$voucher_id);
			}

			if($this->session->userdata('voucher_code_pre_user_id')){

				$cart_main_insert_array = array("session_id"=>$this->session->userdata('voucher_cart_session_id'),"pre_user_id"=>$pre_user_id,"source"=>$source,"item_count"=>1,"total_cart_amount"=>$product_amount,"currency_id"=>$product_price_details['currency_id'],'voucher_id'=>$voucher_id);
			}
             if(!$this->session->userdata('student_logged_in') && !$this->session->userdata('voucher_code_pre_user_id')&& $this->session->userdata['voucher_code_applied_details']['product_type']){
  		 	$cart_main_insert_array = array("session_id"=>$this->session->userdata('voucher_cart_session_id'),"user_id"=>0,"source"=>$source,"item_count"=>1,"total_cart_amount"=>$product_amount,"currency_id"=>$product_price_details['currency_id'],'voucher_id'=>$voucher_id);
			}
  		  
		    $cart_main_id = $this->common_model->insert_to_table("sales_cart_main",$cart_main_insert_array);
				
			$user_agent_data = array();
			
			$user_agent_data['cart_main_id'] = $cart_main_id;
			$user_agent_data['session_id'] 	 = $this->session->userdata('voucher_cart_session_id');
			$user_agent_data['os'] 			 = $this->agent->platform();
			$user_agent_data['browser'] 	 = $this->agent->agent_string();
			
			$this->common_model->insert_to_table("sales_user_agents",$user_agent_data);		
									
			$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$product_id,"item_amount"=>$product_amount,"currency"=>$product_price_details['currency_id'],'discount_amount'=>$discount_amount);					
			
			$cart_items_id = $this->common_model->insert_to_table("sales_cart_items",$item_details_array);		
			
			$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>$product_details[0]->type,"selected_item_ids"=>$selected_id);
			
			$item_id = $this->common_model->insert_to_table("sales_cart_item_details",$items_array);	
			
			$currency_symbol = $this->common_model->get_currency_symbol_from_id($product_price_details['currency_id']);

			$data['err_msg']= 0;
			$data['amount'] = $product_amount;
			$data['count'] = 1;
			$data['currency_symbol'] = $currency_symbol;
			echo json_encode($data); 
			exit;     	
		}
		else
		{
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('voucher_cart_session_id'));
			foreach($cart_main_details as $cart_main)
			{
				$cart_main_id = $cart_main->id;
				
				$sales_cart_items_id = $this->sales_model->get_cart_items($cart_main_id);					
				
				if(!empty($sales_cart_items_id))
				{
				
					$cart_items_id = $sales_cart_items_id[0]->id;
					$product_details = $this->common_model->get_product_details($product_id);	
					$selected_id = $product_details[0]->item_id;			
					$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
					
					$voucher_details = $this->voucher_code_model->get_voucher($voucher_id);			
					$product_amount  = $org_product_amt = $product_price_details['amount'];
					
					// Product price as per the orignal products						
					$selected_items_org_price = $product_amount;					
					
					if($voucher_details[0]->discount_type=='free')
					{		
						$discount_amount = $selected_items_org_price;			
						$product_amount = 0;	
						$product_price_details['currency_id'] = $currency_id;					
					}			
					else if($voucher_details[0]->discount_type=='percentage')
					{						
						$discount_value  = $voucher_details[0]->discount_percentage;						
						
						$product_amount  = $selected_items_org_price - round(( ($selected_items_org_price * $discount_value) / 100 ),2);									
						$discount_amount = $selected_items_org_price - $product_amount;
					}
					elseif($voucher_details[0]->discount_type=='discount_price')
					{								
						$currency_id_discount_value = $discount_amount = $this->voucher_code_model->get_amount_for_voucher_code($voucher_id,$currency_id);
						
						if($currency_id_discount_value!='')
						{						
							$product_amount = $selected_items_org_price - $currency_id_discount_value;
							if($discount_amount<=0)
							{
								$data['err_msg']= 1;
								$data['err_type'] = 'Something went wrong. Please contact info@trendimi.com';					
								echo json_encode($data); 
								exit;	
							}
						
						}
						else
						{
							$data['err_msg']= 1;
							$data['err_type'] = 'Currency not supported. Please contact info@trendimi.com';					
							echo json_encode($data); 
							exit;	
							
						}
					}
					elseif($voucher_details[0]->discount_type=='offer_price')
					{						
						// Get voucher code product details

						$product_id = $this->common_model->getProdectId('voucher_code',$voucher_id);

						$product_details = $this->common_model->get_product_details($product_id);
					
						$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
						
						$voucher_details = $this->voucher_code_model->get_voucher($voucher_id);
						
						$product_amount = $product_price_details['amount'];

						$discount_amount = $selected_items_org_price - $product_amount;

					}										
										
					$item_details_array = array("product_id"=>$product_id,"item_amount"=>$product_amount,"currency"=>$product_price_details['currency_id'],'discount_amount'=>$discount_amount);
					
					$this->sales_model->sales_cart_items_update($cart_items_id,$item_details_array);		
				
					$items_array = array("product_type"=>$product_details[0]->type,"selected_item_ids"=>$selected_id);
				
					$item_id = $this->sales_model->sales_cart_item_details_update($cart_main_id,$cart_items_id,$items_array);	
					
					$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main->id);
					
					$cart_items_total_amount=@round($cart_items_total_amount,2);
					
					
					$cart_total_items = count($selected_values_array);
					
					$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount);
					$this->sales_model->main_cart_details_update($this->session->userdata('voucher_cart_session_id'),$update_array);
										
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
					$selected_id = $product_details[0]->item_id;
					
					$voucher_details = $this->voucher_code_model->get_voucher($voucher_id);
			
					$product_amount = $org_product_amt = $product_price_details['amount'];

					// Product price as per the orignal products	
					$selected_items_org_price = $product_amount;
					
					
					if($voucher_details[0]->discount_type=='free')
					{				
						$discount_amount = $selected_items_org_price;	
						$product_amount = 0;
						$product_price_details['currency_id'] = $currency_id;
					}			
					else if($voucher_details[0]->discount_type=='percentage')
					{					
						
						$discount_value  = $voucher_details[0]->discount_percentage;						
						
						$product_amount  = $selected_items_org_price - round(( ($selected_items_org_price * $discount_value) / 100 ),2);									
						$discount_amount = $selected_items_org_price - $product_amount;

					}
					elseif($voucher_details[0]->discount_type=='discount_price')
					{			
					
						$currency_id_discount_value = $discount_amount = $this->voucher_code_model->get_amount_for_voucher_code($voucher_id,$currency_id);
						
						if($currency_id_discount_value!='')
						{				
							//$discount_amount = $product_amount-($currency_id_discount_value);
							$product_amount = $product_amount - $currency_id_discount_value;
							if($discount_amount<=0)
							{
								$data['err_msg']= 1;
								$data['err_type'] = 'Something went wrong. Plaese contact info@trendimi.com';					
								echo json_encode($data); 
								exit;	
							}
						
						}
						else
						{
							$data['err_msg']= 1;
							$data['err_type'] = 'Currency not supported. Plaese contact info@trendimi.com';					
							echo json_encode($data); 
							exit;	
							
						}
					}	
					elseif($voucher_details[0]->discount_type=='offer_price')
					{	

						// Get voucher code product details

						$product_id = $this->common_model->getProdectId('voucher_code',$voucher_id);

						$product_details = $this->common_model->get_product_details($product_id);
					
						$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
						
						$voucher_details = $this->voucher_code_model->get_voucher($voucher_id);
						
						$product_amount = $product_price_details['amount'];

						$discount_amount = $selected_items_org_price - $product_amount;

					}						
										
					$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$product_id,"item_amount"=>$product_amount,"currency"=>$product_price_details['currency_id'],'discount_amount'=>$discount_amount);
					
					$cart_items_id = $this->common_model->insert_to_table("sales_cart_items",$item_details_array);		
				
					$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>$product_details[0]->type,"selected_item_ids"=>$selected_id);
				
					$item_id = $this->common_model->insert_to_table("sales_cart_item_details",$items_array);	
					
					$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main->id);
					
					$cart_items_total_amount=@round($cart_items_total_amount,2);
					
					
					$cart_total_items = count($this->sales_model->get_cart_items($cart_main->id));
					
					$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount);
					$this->sales_model->main_cart_details_update($this->session->userdata('voucher_cart_session_id'),$update_array);
										
					$currency_symbol = $this->common_model->get_currency_symbol_from_id($product_price_details['currency_id']);
					
					$data['err_msg']= 0;
					$data['amount'] = $cart_items_total_amount;
					$data['count'] = 1;
					$data['currency_symbol'] = $currency_symbol;
					echo json_encode($data); 
					exit;
				
				}
				//$cart_items = $this->sales_model->get_cart_items($cart_main->id);
			}
		}
		

	}
	  



	  /**
	   * [letters Voucher code for letters]
	   * @return [type] [Add letters to cart]
	   */
	  function letters()
	  {
		  
		 if(!isset($this->session->userdata['voucher_code_applied_details']))
			{
				redirect('voucher_code');
			}
			
			$this->check_user_used_this_voucher_code();
			$content = array();
			$added_products_array = array();

			$voucher_letter_option = $this->voucher_code_model->get_voucher_products_other($this->session->userdata['voucher_code_applied_details']['voucher_id']);

			$this->tr_common['tr_trndimi_ebooks'] = $this->user_model->translate_('trendimi_ebooks');
			$this->tr_common['tr_check_out'] = $this->user_model->translate_('check_out');
	    	$this->tr_common['tr_total_price'] = $this->user_model->translate_('total_price');	    	
	    	foreach($voucher_letter_option as $vlo)
	    	{
	    		$letter_product_id = $vlo->product_id;
	    	}
	    	
	    	$letter_product_ids = explode(',',$letter_product_id);

	    	$letter_product_array = array();
	    	for($i=0;$i<(count($letter_product_ids));$i++)
			{
				$letter_product_id = $letter_product_ids[$i];				
				$product_details = $this->common_model->get_product_details($letter_product_id);

				$letter_product_array[$product_details[0]->type] = $letter_product_id;
	    	}

		 
			if(!isset($this->session->userdata['student_logged_in']['id']))
			{
				$user_logged_in = false;
				$enrolled_course_array = array();
			}
			else
			{
				$user_logged_in = true;
				$user_id = $this->session->userdata['student_logged_in']['id'];	
				$enrolled_course_array = $this->voucher_code_model->get_enrolled_courses($user_id);
			}
		
			if($this->session->userdata('voucher_cart_session_id'))
			{			
				$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('voucher_cart_session_id'));			
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
		
		$content['letter_product_array']  = $letter_product_array;
		$content['enrolled_course_array'] = $enrolled_course_array;
		$content['added_products_array']  = $added_products_array;	
		$content['lang_id'] 			  = $this->language;
		$content['user_logged_in'] 	      = $user_logged_in;		
		$content['translate'] 		      = $this->tr_common;
		$content['view'] 				  = 'voucher_code_letters';
		$title['pageTitle'] 			  = 'Login';
		$content['content'] 			  = $title;
		$this->load->view('user/template_outer',$content); 
	  
	  }
	  
	  

	  /**
	   * [hardcopy Voucher code for Hardcopy]
	   * @return [type] [Add hardcopy to cart]
	   */
	  function hardcopy()
	  {
		 if(!isset($this->session->userdata['voucher_code_applied_details']))
			{
				redirect('voucher_code');
			}
			
			$this->check_user_used_this_voucher_code();
			$content = array();
			$added_products_array = array();

			$voucher_hardcopy_option = $this->voucher_code_model->get_voucher_products_other($this->session->userdata['voucher_code_applied_details']['voucher_id']);

			$this->tr_common['tr_trndimi_ebooks'] = $this->user_model->translate_('trendimi_ebooks');
			$this->tr_common['tr_check_out'] = $this->user_model->translate_('check_out');
	    	$this->tr_common['tr_total_price'] = $this->user_model->translate_('total_price');
		 
			if(!isset($this->session->userdata['student_logged_in']['id']))
			{
				$user_logged_in = false;
				$enrolled_course_array = array();
			}
			else
			{
				$user_logged_in = true;
				$user_id = $this->session->userdata['student_logged_in']['id'];	
				$enrolled_course_array = $this->user_model->get_enrolled_courses($user_id);
			}
		
			if($this->session->userdata('voucher_cart_session_id'))
			{			
				$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('voucher_cart_session_id'));			
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
		
		$content['voucher_hardcopy_option']  = $voucher_hardcopy_option;
		$content['enrolled_course_array'] = $enrolled_course_array;
		$content['added_products_array']  = $added_products_array;	
		$content['lang_id'] 			  = $this->language;
		$content['user_logged_in'] 	      = $user_logged_in;		
		$content['translate'] 		      = $this->tr_common;
		$content['view'] 				  = 'voucher_code_hardcopy';
		$title['pageTitle'] 			  = 'Login';
		$content['content'] 			  = $title;
		$this->load->view('user/template_outer',$content); 
	  }
	  
	  function giftcard()
	  {
	  	$this->check_user_used_this_voucher_code();
		  echo "Voucher code giftcard";
		  exit;
	  }
	  


	  function add_voucher_other_poducts($selected_values,$product_id,$currency_id,$source)
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
		
		$user_id = $this->session->userdata['student_logged_in']['id'];	
		$voucher_id = $this->session->userdata['voucher_code_applied_details']['voucher_id'];
		
		
		$product_details = $this->common_model->get_product_details($product_id);	
		$product_type = $product_details[0]->type;		
				
		if(!$this->session->userdata('voucher_cart_session_id'))
		{	
						
			$product_details = $this->common_model->get_product_details($product_id);
			
			$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
			
			$voucher_details = $this->voucher_code_model->get_voucher($voucher_id);
			
			$product_amount = $org_product_amt = $product_price_details['amount'];
						
			if($voucher_details[0]->discount_type=='free')
			{				
				$product_amount = 0;
				$discount_amount = $product_amount;	
				$product_price_details['currency_id'] = $currency_id;
			}			
			else if($voucher_details[0]->discount_type=='percentage')
			{						
				$discount_value  = $voucher_details[0]->discount_percentage;				
				$product_amount  = $product_amount - round(( ($product_amount * $discount_value) / 100 ),2);									
				$discount_amount = $org_product_amt - $product_amount;
			}
			elseif($voucher_details[0]->discount_type=='discount_price')
			{			
			
			    $currency_id_discount_value = $discount_amount = $this->voucher_code_model->get_amount_for_voucher_code($voucher_id,$currency_id);
				
				if($currency_id_discount_value!='')
				{				
					//$discount_amount = $product_amount-($currency_id_discount_value);
					$product_amount = $product_amount - $currency_id_discount_value;
					if($discount_amount<=0)
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
			elseif($voucher_details[0]->discount_type=='offer_price')
			{			
				
				// Product price as per the orignal products

				$product_details = $this->common_model->get_product_details($product_id);
			
				$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
				
				$voucher_details = $this->voucher_code_model->get_voucher($voucher_id);
				
				$org_product_amt = $product_price_details['amount'];


				// Get voucher code product details

				$product_id = $this->common_model->getProdectId('voucher_code',$voucher_id);

				$product_details = $this->common_model->get_product_details($product_id);
			
				$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
				
				$voucher_details = $this->voucher_code_model->get_voucher($voucher_id);
				
				$product_amount = $product_price_details['amount'];

				$discount_amount = $org_product_amt - $product_amount;

			}			
			
			session_regenerate_id();
			$sess_array = array('voucher_cart_session_id' => session_id()); 
			$this->session->set_userdata($sess_array);	
			
			$cart_main_insert_array = array("session_id"=>$this->session->userdata('voucher_cart_session_id'),"user_id"=>$user_id,"source"=>$source,"item_count"=>1,"total_cart_amount"=>$product_amount,"currency_id"=>$product_price_details['currency_id'],'voucher_id'=>$voucher_id);
  		  
		    $cart_main_id = $this->common_model->insert_to_table("sales_cart_main",$cart_main_insert_array);
				
			$user_agent_data = array();
			
			$user_agent_data['cart_main_id']  = $cart_main_id;
			$user_agent_data['session_id'] 	= $this->session->userdata('voucher_cart_session_id');
			$user_agent_data['os'] 			= $this->agent->platform();
			$user_agent_data['browser'] 	   = $this->agent->agent_string();
			
			$this->common_model->insert_to_table("sales_user_agents",$user_agent_data);		
									
			$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$product_id,"item_amount"=>$product_amount,"currency"=>$product_price_details['currency_id'],'discount_amount'=>$discount_amount);					
			
			$cart_items_id = $this->common_model->insert_to_table("sales_cart_items",$item_details_array);		
			
			$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>$product_details[0]->type,"selected_item_ids"=>$new_selected_values);
			
			$item_id = $this->common_model->insert_to_table("sales_cart_item_details",$items_array);	
			
			$currency_symbol = $this->common_model->get_currency_symbol_from_id($product_price_details['currency_id']);

			$data['err_msg']= 0;
			$data['amount'] = $product_amount;
			$data['count'] = 1;
			$data['currency_symbol'] = $currency_symbol;
			echo json_encode($data); 
			exit;     	
		}
		else
		{
			
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('voucher_cart_session_id'));
			foreach($cart_main_details as $cart_main)
			{
				$cart_main_id = $cart_main->id;
				$product_in_cart = $this->sales_model->check_product_type_exist_in_cart($cart_main->id,$product_type);
				if(empty($product_in_cart))
				{	

					$product_details = $this->common_model->get_product_details($product_id);
			
					$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
					
					$voucher_details = $this->voucher_code_model->get_voucher($voucher_id);
					
					$product_amount = $org_product_amt = $product_price_details['amount'];
								
					if($voucher_details[0]->discount_type=='free')
					{				
						$discount_amount = $product_amount;	
						$product_amount = 0;
						$product_price_details['currency_id'] = $currency_id;
					}			
					else if($voucher_details[0]->discount_type=='percentage')
					{						
						$discount_value  = $voucher_details[0]->discount_percentage;				
						$product_amount  = $product_amount - round(( ($product_amount * $discount_value) / 100 ),2);									
						$discount_amount = $org_product_amt - $product_amount;
					}
					elseif($voucher_details[0]->discount_type=='discount_price')
					{			
					
					    $currency_id_discount_value = $discount_amount = $this->voucher_code_model->get_amount_for_voucher_code($voucher_id,$currency_id);
						
						if($currency_id_discount_value!='')
						{				
							//$discount_amount = $product_amount-($currency_id_discount_value);
							$product_amount = $product_amount - $currency_id_discount_value;
							if($discount_amount<=0)
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
								
					$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$product_id,"item_amount"=>$product_amount,"currency"=>$currency_id,'discount_amount'=>$discount_amount);
					
					$cart_items_id = $this->common_model->insert_to_table("sales_cart_items",$item_details_array);		
				
					$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>$product_details[0]->type,"selected_item_ids"=>$selected_values);
				
					$item_id = $this->common_model->insert_to_table("sales_cart_item_details",$items_array);	
					
					$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main->id);
					
					$cart_items_total_amount=@round($cart_items_total_amount,2);
					
					
					$cart_total_items = count($this->sales_model->get_cart_items($cart_main->id));
					
					$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount);
					$this->sales_model->main_cart_details_update($this->session->userdata('voucher_cart_session_id'),$update_array);
										
					$currency_symbol = $this->common_model->get_currency_symbol_from_id($currency_id);
					
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
					
					$product_details = $this->common_model->get_product_details($product_id);
		
					$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
					
					$voucher_details = $this->voucher_code_model->get_voucher($voucher_id);
					
					$product_amount = $org_product_amt = $product_price_details['amount'];
								
					if($voucher_details[0]->discount_type=='free')
					{				
						$discount_amount = $product_amount;	
						$product_amount = 0;
						$product_price_details['currency_id'] = $currency_id;
					}			
					else if($voucher_details[0]->discount_type=='percentage')
					{						
						$discount_value  = $voucher_details[0]->discount_percentage;				
						$product_amount  = $product_amount - round(( ($product_amount * $discount_value) / 100 ),2);									
						$discount_amount = $org_product_amt - $product_amount;
					}
					elseif($voucher_details[0]->discount_type=='discount_price')
					{			
					
					    $currency_id_discount_value = $discount_amount = $this->voucher_code_model->get_amount_for_voucher_code($voucher_id,$currency_id);
						
						if($currency_id_discount_value!='')
						{				
							//$discount_amount = $product_amount-($currency_id_discount_value);
							$product_amount = $product_amount - $currency_id_discount_value;
							if($discount_amount<=0)
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
										
					$item_details_array = array("product_id"=>$product_id,"item_amount"=>$product_amount,"currency"=>$currency_id,'discount_amount'=>$discount_amount);				
					//sales_cart_items_update
					
					$this->sales_model->sales_cart_items_update($cart_items_id,$item_details_array);	
								
					$items_array = array("selected_item_ids"=>$new_selected_values);			
				
					$this->sales_model->sales_cart_item_details_update($cart_main_id,$cart_items_id,$items_array);	
					
					$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main->id);
					
					$cart_items_total_amount=@round($cart_items_total_amount,2);
					
					
					$cart_total_items = count($this->sales_model->get_cart_items($cart_main->id));
					
					$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount);
					$this->sales_model->main_cart_details_update($this->session->userdata('voucher_cart_session_id'),$update_array);
										
					$currency_symbol = $this->common_model->get_currency_symbol_from_id($currency_id);
					
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
	  
	  
	function test($selected_values,$currency_id)
	{	
		$selected_values_array = explode("+",$selected_values);	
		$selected_items_price_details = $this->voucher_code_model->get_selected_items_price_details($this->session->userdata['voucher_code_applied_details']['product_type'] ,$selected_values_array,$currency_id);

		echo "Product amount  -- ".$selected_items_price_details;
		exit;
	}

	  
	  function add_voucher_ebook_course($selected_values,$product_id,$currency_id,$source,$ajax=1)
	  {
		
		//$this->session->userdata['voucher_code_pre_user_id']['pre_user_id']
		  
		/*if(!$this->session->userdata('student_logged_in')){
		  redirect('home');
		}	*/
		
		$new_selected_values = '';	
		$selected_values_array = explode("+",$selected_values);
		
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

		if($this->session->userdata('voucher_code_pre_user_id')){

			$pre_user_id = $this->session->userdata['voucher_code_pre_user_id']['pre_user_id'];	

		}

		$voucher_id = $this->session->userdata['voucher_code_applied_details']['voucher_id'];
				
		if(!$this->session->userdata('voucher_cart_session_id'))
		{		
			
			
			
						
			$product_details = $this->common_model->get_product_details($product_id);
			
			$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
			
			$voucher_details = $this->voucher_code_model->get_voucher($voucher_id);
			
			$product_amount = $org_product_amt = $product_price_details['amount'];


			// Product price as per the orignal products	

			if($this->session->userdata['voucher_code_applied_details']['product_type']=='extension'){

				$selected_items_org_price = $product_amount;
			}
			else
			{
				$selected_items_org_price = $this->voucher_code_model->get_selected_items_price_details($this->session->userdata['voucher_code_applied_details']['product_type'] ,$selected_values_array,$currency_id);	
			}

						
			if($voucher_details[0]->discount_type=='free')
			{		
				
				$product_amount = 0;
				$discount_amount = $selected_items_org_price;
				$product_price_details['currency_id'] = $currency_id;
			}			
			else if($voucher_details[0]->discount_type=='percentage')
			{		
				

				$discount_value  = $voucher_details[0]->discount_percentage;				
				$product_amount  = $selected_items_org_price - round(( ($selected_items_org_price * $discount_value) / 100 ),2);									
				$discount_amount = $selected_items_org_price - $product_amount;
			}
			elseif($voucher_details[0]->discount_type=='discount_price')
			{					
 				
 				$currency_id_discount_value = $discount_amount = $this->voucher_code_model->get_amount_for_voucher_code($voucher_id,$currency_id);
				
				if($currency_id_discount_value!='')
				{				
					//$discount_amount = $product_amount-($currency_id_discount_value);					
					$product_amount = $selected_items_org_price - $currency_id_discount_value;

					if($discount_amount<=0)
					{
						$data['err_msg']= 1;
						$data['err_type'] = 'Something went wrong. Plaese contact info@internationalopenacademy.com';					
						echo json_encode($data); 
						exit;	
					}
				
				}
				else
				{
					$data['err_msg']= 1;
					$data['err_type'] = 'Currency not supported. Plaese contact info@internationalopenacademy.com';					
					echo json_encode($data); 
					exit;	
					
				}
			}	

			elseif($voucher_details[0]->discount_type=='offer_price')
			{			
								
				// Get voucher code product details

				$product_id = $this->common_model->getProdectId('voucher_code',$voucher_id);

				$product_details = $this->common_model->get_product_details($product_id);
			
				$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
				
				$voucher_details = $this->voucher_code_model->get_voucher($voucher_id);
				
				$product_amount = $product_price_details['amount'];
					if($this->session->userdata['voucher_code_applied_details']['product_type']=='course_subscription'){
					$selected_items_org_price = 899;
				}

				$discount_amount = $selected_items_org_price - $product_amount;

			}			
					//echo $selected_items_org_price.'/'.$product_amount;exit;
			
			session_regenerate_id();
			$sess_array = array('voucher_cart_session_id' => session_id()); 	
			$this->session->set_userdata($sess_array);	

			if($this->session->userdata('student_logged_in')){

			$cart_main_insert_array = array("session_id"=>$this->session->userdata('voucher_cart_session_id'),"user_id"=>$user_id,"source"=>$source,"item_count"=>1,"total_cart_amount"=>$product_amount,"currency_id"=>$product_price_details['currency_id'],'voucher_id'=>$voucher_id);
			}

			if($this->session->userdata('voucher_code_pre_user_id')){

				$cart_main_insert_array = array("session_id"=>$this->session->userdata('voucher_cart_session_id'),"pre_user_id"=>$pre_user_id,"source"=>$source,"item_count"=>1,"total_cart_amount"=>$product_amount,"currency_id"=>$product_price_details['currency_id'],'voucher_id'=>$voucher_id);
			}
             if(!$this->session->userdata('student_logged_in') && !$this->session->userdata('voucher_code_pre_user_id')&& $this->session->userdata['voucher_code_applied_details']['product_type']){
  		 $cart_main_insert_array = array("session_id"=>$this->session->userdata('voucher_cart_session_id'),"user_id"=>0,"source"=>$source,"item_count"=>1,"total_cart_amount"=>$product_amount,"currency_id"=>$product_price_details['currency_id'],'voucher_id'=>$voucher_id);
			}
  		  
		    $cart_main_id = $this->common_model->insert_to_table("sales_cart_main",$cart_main_insert_array);
				
			$user_agent_data = array();
			
			$user_agent_data['cart_main_id']  = $cart_main_id;
			$user_agent_data['session_id'] 	= $this->session->userdata('voucher_cart_session_id');
			$user_agent_data['os'] 			= $this->agent->platform();
			$user_agent_data['browser'] 	   = $this->agent->agent_string();
			
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
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('voucher_cart_session_id'));
			foreach($cart_main_details as $cart_main)
			{
				$cart_main_id = $cart_main->id;
				
				$sales_cart_items_id = $this->sales_model->get_cart_items($cart_main_id);					
				
				if(!empty($sales_cart_items_id))
				{
				
					$cart_items_id = $sales_cart_items_id[0]->id;
					$product_details = $this->common_model->get_product_details($product_id);			
					$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
					
					$voucher_details = $this->voucher_code_model->get_voucher($voucher_id);
			
					$product_amount = $org_product_amt = $product_price_details['amount'];


					// Product price as per the orignal products	

					if($this->session->userdata['voucher_code_applied_details']['product_type']=='extension'){

						$selected_items_org_price = $product_amount;
					}
					else
					{
						$selected_items_org_price = $this->voucher_code_model->get_selected_items_price_details($this->session->userdata['voucher_code_applied_details']['product_type'] ,$selected_values_array,$currency_id);	
					}
					
					
					if($voucher_details[0]->discount_type=='free')
					{	

						$discount_amount = $selected_items_org_price;			
						$product_amount = 0;	
						$product_price_details['currency_id'] = $currency_id;					
					}			
					else if($voucher_details[0]->discount_type=='percentage')
					{						
						$discount_value  = $voucher_details[0]->discount_percentage;	
						
						$product_amount  = $selected_items_org_price - round(( ($selected_items_org_price * $discount_value) / 100 ),2);									
						$discount_amount = $selected_items_org_price - $product_amount;
					}
					elseif($voucher_details[0]->discount_type=='discount_price')
					{						
						
						$currency_id_discount_value = $discount_amount = $this->voucher_code_model->get_amount_for_voucher_code($voucher_id,$currency_id);
						
						if($currency_id_discount_value!='')
						{						
							$product_amount = $selected_items_org_price - $currency_id_discount_value;
							if($discount_amount<=0)
							{
								$data['err_msg']= 1;
								$data['err_type'] = 'Something went wrong. Please contact info@eventtrix.com';					
								echo json_encode($data); 
								exit;	
							}
						
						}
						else
						{
							$data['err_msg']= 1;
							$data['err_type'] = 'Currency not supported. Please contact info@eventtrix.com';					
							echo json_encode($data); 
							exit;	
							
						}
					}
					elseif($voucher_details[0]->discount_type=='offer_price')
					{							
						
						// Get voucher code product details

						$product_id = $this->common_model->getProdectId('voucher_code',$voucher_id);

						$product_details = $this->common_model->get_product_details($product_id);
					
						$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
						
						$voucher_details = $this->voucher_code_model->get_voucher($voucher_id);
						
						$product_amount = $product_price_details['amount'];

						$discount_amount = $selected_items_org_price - $product_amount;

					}			
									
										
					$item_details_array = array("product_id"=>$product_id,"item_amount"=>$product_amount,"currency"=>$product_price_details['currency_id'],'discount_amount'=>$discount_amount);
					
					$this->sales_model->sales_cart_items_update($cart_items_id,$item_details_array);		
				
					$items_array = array("product_type"=>$product_details[0]->type,"selected_item_ids"=>$new_selected_values);
				
					$item_id = $this->sales_model->sales_cart_item_details_update($cart_main_id,$cart_items_id,$items_array);	
					
					$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main->id);
					
					$cart_items_total_amount=@round($cart_items_total_amount,2);
					
					
					$cart_total_items = count($selected_values_array);
					
					$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount);
					$this->sales_model->main_cart_details_update($this->session->userdata('voucher_cart_session_id'),$update_array);
										
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
				
					$product_details = $this->common_model->get_product_details($product_id);			
					$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);		
					
					$voucher_details = $this->voucher_code_model->get_voucher($voucher_id);
			
					$product_amount = $org_product_amt = $product_price_details['amount'];

					// Product price as per the orignal products	

					if($this->session->userdata['voucher_code_applied_details']['product_type']=='extension'){

						$selected_items_org_price = $product_amount;
					}
					else
					{
						$selected_items_org_price = $this->voucher_code_model->get_selected_items_price_details($this->session->userdata['voucher_code_applied_details']['product_type'] ,$selected_values_array,$currency_id);	
					}
					
					if($voucher_details[0]->discount_type=='free')
					{				
						$discount_amount = $selected_items_org_price;	
						$product_amount = 0;
						$product_price_details['currency_id'] = $currency_id;
					}			
					else if($voucher_details[0]->discount_type=='percentage')
					{						
						$discount_value  = $voucher_details[0]->discount_percentage;						
						$product_amount  = $product_amount - round(( ($product_amount * $discount_value) / 100 ),2);						
						$discount_amount = $selected_items_org_price - $product_amount;
					}
					elseif($voucher_details[0]->discount_type=='discount_price')
					{			
					
						$currency_id_discount_value = $discount_amount = $this->voucher_code_model->get_amount_for_voucher_code($voucher_id,$currency_id);
						
						if($currency_id_discount_value!='')
						{				
							//$discount_amount = $product_amount-($currency_id_discount_value);
							$product_amount = $product_amount - $currency_id_discount_value;
							if($discount_amount<=0)
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
					elseif($voucher_details[0]->discount_type=='offer_price')
					{			
						
						// Get voucher code product details

						$product_id = $this->common_model->getProdectId('voucher_code',$voucher_id);

						$product_details = $this->common_model->get_product_details($product_id);
					
						$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
						
						$voucher_details = $this->voucher_code_model->get_voucher($voucher_id);
						
						$product_amount = $product_price_details['amount'];

						$discount_amount = $selected_items_org_price - $product_amount;

					}			
						
					
										
					$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$product_id,"item_amount"=>$product_amount,"currency"=>$product_price_details['currency_id'],'discount_amount'=>$discount_amount);
					
					$cart_items_id = $this->common_model->insert_to_table("sales_cart_items",$item_details_array);		
				
					$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>$product_details[0]->type,"selected_item_ids"=>$new_selected_values);
				
					$item_id = $this->common_model->insert_to_table("sales_cart_item_details",$items_array);	
					
					$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main->id);
					
					$cart_items_total_amount=@round($cart_items_total_amount,2);
					
					
					$cart_total_items = count($this->sales_model->get_cart_items($cart_main->id));
					
					$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount);
					$this->sales_model->main_cart_details_update($this->session->userdata('voucher_cart_session_id'),$update_array);
										
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
				//$cart_items = $this->sales_model->get_cart_items($cart_main->id);
			}
		}
			
	
	  }
	  
	  function bonus()
	  {
			if(!isset($this->session->userdata['voucher_code_applied_details']))
			{
				redirect('voucher_code');
			}
			
			/*if(!$this->session->userdata('student_logged_in')){
			  redirect('home');
			}*/
			
			$course_array = array();
			$ebook_array = array();
			$voucher_bonus_other_array = array();
			$voucher_bonus_array = array();
			$added_product_id = 0;
			$extention_details = '';
			$hand_out_details = '';
			$tool_kit_details = '';

			$content['currency_id'] = $this->currId;
			
			$this->tr_common['tr_trndimi_ebooks'] = $this->user_model->translate_('trendimi_ebooks');
			$this->tr_common['tr_check_out'] = $this->user_model->translate_('check_out');
			$this->tr_common['tr_total_price'] = $this->user_model->translate_('total_price');
		 
			
			
			$voucher_id = $this->session->userdata['voucher_code_applied_details']['voucher_id'];
			$voucher_details = $this->voucher_code_model->get_voucher($voucher_id);
			$voucher_bonus_course_ebook_details = $this->voucher_code_model->get_voucher_bonus_course_ebook_details($voucher_id);
			$voucher_bonus_other_details = $this->voucher_code_model->get_voucher_bonus_other_details($voucher_id);


			$enrolled_course_array = array();
			$cart_added_course_array = array();
			$cart_added_course_array_details = array();

			if($this->session->userdata('student_logged_in')){

		 		$user_id = $this->session->userdata['student_logged_in']['id'];
				$enrolled_course_array_1 = $this->user_model->get_enrolled_courses($user_id);

				$cart_main_id = $this->voucher_code_model->get_cart_main_id_from_session_id($this->session->userdata('voucher_cart_session_id'));

			 	if($this->session->userdata['voucher_code_applied_details']['discount_type']=='offer_price')
				$added_course_details = $this->sales_model->check_product_type_exist_in_cart($cart_main_id,'voucher_code');
                else
				$added_course_details = $this->sales_model->check_product_type_exist_in_cart($cart_main_id,$this->session->userdata['voucher_code_applied_details']['product_type']);                 

				$voucher_code_course_id_array = explode(',',$added_course_details[0]->selected_item_ids);	
				
				$cart_added_course_array = $this->voucher_code_model->get_courses_as_list($voucher_code_course_id_array);	
				$cart_added_course_array_details = $this->voucher_code_model->get_courses($voucher_code_course_id_array);
								
				if(!empty($cart_added_course_array)){
					
					$enrolled_course_array =$enrolled_course_array_1+$cart_added_course_array;			
				}	
				else{
					
					$enrolled_course_array =$enrolled_course_array_1;
				}

			}
			else
			{			
				$cart_main_id = $this->voucher_code_model->get_cart_main_id_from_session_id($this->session->userdata('voucher_cart_session_id'));

			 	if($this->session->userdata['voucher_code_applied_details']['discount_type']=='offer_price')
				$added_course_details = $this->sales_model->check_product_type_exist_in_cart($cart_main_id,'voucher_code');
                else
				$added_course_details = $this->sales_model->check_product_type_exist_in_cart($cart_main_id,$this->session->userdata['voucher_code_applied_details']['product_type']);
                 
				$voucher_code_course_id_array = explode(',',$added_course_details[0]->selected_item_ids);

				$cart_added_course_array_details = $this->voucher_code_model->get_courses($voucher_code_course_id_array);

				$enrolled_course_array = $cart_added_course_array = $this->voucher_code_model->get_courses_as_list($voucher_code_course_id_array);			
			}
			
						
			if(!empty($voucher_bonus_course_ebook_details))
			{
				foreach($voucher_bonus_course_ebook_details as $v_bonuses)
				{
					if($v_bonuses->bonus_type == 'ebooks')
					{
						$voucher_bonus_array[$v_bonuses->bonus_type]['course_ebook_ids'] = explode(',',$v_bonuses->course_ebook_ids);				
						$voucher_bonus_array[$v_bonuses->bonus_type]['limit_count'] = $v_bonuses->item_count_x;
						$ebook_array = $this->voucher_code_model->get_ebooks($voucher_bonus_array[$v_bonuses->bonus_type]['course_ebook_ids']);
						
						$total_ebook_count = $this->voucher_code_model->get_active_ebooks_count($this->language);
						// Romana needs to add all ebook to cart automatically if giving all ebook as bonus 
						if($total_ebook_count == $v_bonuses->item_count_x){

							$selected_values = str_replace(',', '+', $v_bonuses->course_ebook_ids);
							$this->add_bonus_products($selected_values,'bonus_ebooks',$this->currId,'voucher_ebooks',0);
						}	
					}
					else if($v_bonuses->bonus_type == 'course')
					{
						$voucher_bonus_array[$v_bonuses->bonus_type]['course_ebook_ids'] = explode(',',$v_bonuses->course_ebook_ids);			
						$voucher_bonus_array[$v_bonuses->bonus_type]['limit_count'] = $v_bonuses->item_count_x;	
						$course_array = $this->voucher_code_model->get_courses($voucher_bonus_array[$v_bonuses->bonus_type]['course_ebook_ids']);
					}
				}
				
			}
			
			
			
			
			if(!empty($voucher_bonus_other_details))
			{
				foreach($voucher_bonus_other_details as $other_bonuses)
				{
				   $voucher_bonus_other_array[$other_bonuses->product_type]['product_ids'] = $other_bonuses->product_ids;	   
				   
				   if($other_bonuses->product_type=='bonus_extension')
				   {
					  $extension_product_details = $this->common_model->get_product_details($other_bonuses->product_ids);
					  
					  $extension_item_id =  $extension_product_details[0]->item_id;
					  
					  $extension_details = $this->sales_model->get_extension_details_by_units($extension_item_id);								
						
						if($this->session->userdata('language')==3)
						{
							$extention_details = $extension_details[0]->extension_option_spanish;
						}
						else
						{								
							$extention_details = $extension_details[0]->extension_option;
						}
				   }
				   else if($other_bonuses->product_type=='bonus_hand_outs'){

				   	  $hand_out_product_details = $this->common_model->get_product_details($other_bonuses->product_ids);

                           //echo "<pre>";print_r($hand_out_product_details);exit;
					  
					  $hand_out_item_id =  $hand_out_product_details[0]->item_id;
					  
					  $hand_out_details = $this->voucher_code_model->get_hand_out_details($hand_out_item_id);	
				   }
				   else if($other_bonuses->product_type=='bonus_tool_kit'){

					   	$product_details = $this->common_model->get_product_details($other_bonuses->product_ids);
						$tool_kit_details = $this->voucher_code_model->get_tool_kit_details($product_details[0]->item_id);

				   }
				   else if($other_bonuses->product_type=='bonus_gift_campaign'){

				   	  $gift_campaign_product_details = $this->common_model->get_product_details($other_bonuses->product_ids);					  
					  $gift_campaign_item_id =  $gift_campaign_product_details[0]->item_id;
					  $content['gift_card_product_details']      =$this->voucher_code_model->get_products_units_by_type_name('giftcard_offer');

					  $gift_card_product_id = $this->common_model->getProdectId('giftcard_offer','',1);
					  $content['gift_cart_product_price'] = $this->common_model->getProductFee($gift_card_product_id,$this->currId);

					//echo "<pre>";print_r($content['gift_card_product_details']);exit;
					 // $gift_campaign_details = $this->voucher_code_model->get_hand_out_details($gift_campaign_item_id);	
				   }
				}
			}
			
			if(empty($voucher_bonus_array) && empty($voucher_bonus_other_array))
			{
				redirect('voucher_code/cart');
			}


			if($this->session->userdata['voucher_code_applied_details']['product_type']== 'course'){

				if(isset($voucher_bonus_other_array['bonus_extension']))
				{
					foreach ($cart_added_course_array_details as $cart_crs) {
					
						$this->add_bonus_products_others($voucher_bonus_other_array['bonus_extension']['product_ids'],$cart_crs->course_id,$this->currId,'',0);
					}
				}
			}
					
			
			if($this->session->userdata('voucher_cart_session_id'))
			{			
				$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('voucher_cart_session_id'));			
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
									$added_products_array[$item_det->product_type]['selected_item_ids'] =  $item_det->selected_item_ids;	
									$added_products_array[$item_det->product_type]['product_id'] = $added_product_id = $prod->product_id;											
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
			
				
			$content['cart_added_course_array_details'] 	= $cart_added_course_array_details;
			$content['voucher_bonus_course_ebook_details']  = $voucher_bonus_course_ebook_details;	
			$content['voucher_bonus_other_details'] 		= $voucher_bonus_other_details;
			$content['voucher_bonus_other_array']   		= $voucher_bonus_other_array;
				
			$content['enrolled_course_array'] = $enrolled_course_array;
  			$content['added_products_array']  = $added_products_array;
			$content['voucher_bonus_array']   = $voucher_bonus_array;				
			$content['voucher_details']	  	  = $voucher_details;
			$content['added_product_id']	  = $added_product_id;
			$content['extention_details']     = $extention_details;		
			$content['hand_out_details']      = $hand_out_details;
			$content['tool_kit_details']      = $tool_kit_details;
		
			$content['lang_id'] 			 = $this->language;			
			$content['ebook_array'] 		 = $ebook_array;	
			$content['course_array'] 		 = $course_array;	
			$content['translate'] 		     = $this->tr_common;
			$content['view'] 				 = 'voucher_bonus';
			$title['pageTitle'] 			 = 'Login';
			$content['content'] 			 = $title;
			//print_r($content);
			//exit;	
			$this->load->view('user/template_outer',$content); 
	  }
	  
	 	  
	  function add_bonus_products($selected_values,$product_type,$currency_id,$source,$ajax=1)
	  {
		  
		/*if(!$this->session->userdata('student_logged_in')){
		  redirect('home');
		}	*/

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
		
		//$user_id = $this->session->userdata['student_logged_in']['id'];	
		$voucher_id = $this->session->userdata['voucher_code_applied_details']['voucher_id'];
		$product_id = $this->common_model->getProdectId($product_type);	

		if(!$this->session->userdata('voucher_cart_session_id'))
		{
			redirect('voucher_code');
		}
		else
		{			
		
			$product_details = $this->common_model->get_product_details($product_id);	
			$product_type = $product_details[0]->type;	

			$bonus_org_product_type = str_replace("bonus_","",$product_type);	

			$selected_items_org_price = $this->voucher_code_model->get_selected_items_price_details($bonus_org_product_type ,$selected_values_array,$currency_id);

				
			//echo $this->session->userdata('cart_session_id');
			
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('voucher_cart_session_id'));
			foreach($cart_main_details as $cart_main)
			{
				$cart_main_id = $cart_main->id;
				$product_in_cart = $this->sales_model->check_product_type_exist_in_cart($cart_main->id,$product_type);
				if(empty($product_in_cart))
				{					
					
					//$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);

					$product_amount = 0;				
					$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$product_id,"item_amount"=>$product_amount,"currency"=>$currency_id,'discount_amount'=>$selected_items_org_price);
					
					$cart_items_id = $this->common_model->insert_to_table("sales_cart_items",$item_details_array);		
				
					$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>$product_details[0]->type,"selected_item_ids"=>$new_selected_values);
				
					$item_id = $this->common_model->insert_to_table("sales_cart_item_details",$items_array);	
					
					$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main->id);
					
					$cart_items_total_amount=@round($cart_items_total_amount,2);
					
					
					$cart_total_items = count($this->sales_model->get_cart_items($cart_main->id));
					
					$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount);
					$this->sales_model->main_cart_details_update($this->session->userdata('voucher_cart_session_id'),$update_array);
										
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
					$this->sales_model->main_cart_details_update($this->session->userdata('voucher_cart_session_id'),$update_array);
										
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
	  

	  function validate_products_added()
	  {
	  		
	  		$validation = true;
	  		$data['show_package'] = false;
	  		$voucher_id = $this->session->userdata['voucher_code_applied_details']['voucher_id'];

	  		$voucher_course_ebook_array = $this->voucher_code_model->get_course_ebook_for_voucher($this->session->userdata['voucher_code_applied_details']['voucher_id']);			
			
			

		
       
			if(!empty($voucher_course_ebook_array))
			{
				foreach($voucher_course_ebook_array as $prod_cou)
				{
					$voucher_product_array[$prod_cou->voucher_type] = $prod_cou->item_count_x;
				}				
			}	
			else
			{
				$voucher_other_product_array = $this->voucher_code_model->get_voucher_products_other($this->session->userdata['voucher_code_applied_details']['voucher_id']);
				if(!empty($voucher_other_product_array))
				{
					foreach($voucher_other_product_array as $other_products)
					{
					   $voucher_product_array[$other_products->voucher_type] = count($other_products->product_id);		
					}
				}
				else
				{
					$voucher_course_bundle_array = $this->voucher_code_model->get_voucher_bundle_details($this->session->userdata['voucher_code_applied_details']['voucher_id']);
					if(!empty($voucher_course_bundle_array))
					{
						foreach($voucher_course_bundle_array as $prod_bundle)
						{
							$voucher_product_array['bundle'] = 1;
						}				
					}

				}
			}		
			
			
			

			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('voucher_cart_session_id'));	


 
			$cart_main_id = $cart_main_details[0]->id;
			foreach($voucher_product_array as $key=>$value)
			{
				 		
                $voucher_remaining_note='';
				$discount_type = $this->session->userdata['voucher_code_applied_details']['discount_type'];
				if($discount_type == 'offer_price')
				{
					$product_in_cart = $this->sales_model->check_product_type_exist_in_cart($cart_main_id,'voucher_code');
				}
				else
				{		 		
					$product_in_cart = $this->sales_model->check_product_type_exist_in_cart($cart_main_id,$key);
				}
				
				if(!empty($product_in_cart))
				{
					$remaining_count = $voucher_product_array[$key] - count(explode(',',$product_in_cart[0]->selected_item_ids));
					if($remaining_count > 0)
					{
						$validation = false;
						
						$voucher_remaining_note.= "Please select ".$remaining_count." more ".$key."(s) from all listed ".$key."s";						
					}
					// Checking cart has only one course and no bonus added to voucher code, add option to add packages
					else
					{
						if($this->session->userdata['voucher_code_applied_details']['product_type'] == 'course' && count(explode(',',$product_in_cart[0]->selected_item_ids)) == 1)
						{	 
							$voucher_id = $this->session->userdata['voucher_code_applied_details']['voucher_id'];

					  		$voucher_bonus_details = $this->voucher_code_model->check_bonus_added($voucher_id);
							if($voucher_bonus_details)
							{
								$data['show_package'] 	 	 = true;								
								$sess_array = array('voucher_package_applying_course' =>$product_in_cart[0]->selected_item_ids); 				
								$this->session->set_userdata($sess_array);	
							}

						}
					}
				}
				else
				{
					
					$validation = false;
					if($voucher_product_array[$key]>1)
					{	
						$voucher_remaining_note .= "Please select ".$voucher_product_array[$key]."more ".$key."(s) from all listed ".$key."s";				
						//$voucher_remaining_note .= ucfirst($key).' ' .$voucher_product_array[$key].';
					}
					else
					{
						$voucher_remaining_note .= "Please select the ".$key."(s) from all listed ".$key."s";		
							
					}

				}
				//$voucher_remaining_note = ' Please select the '.$key.'(s) from the listed '.$key;	 
				//print_r($product_in_cart);
			} 
            // echo $validation;exit;
			$data['validation'] 	 = $validation;
			$data['validation_note'] = $voucher_remaining_note;

			echo json_encode($data); 
		    exit;

	  }

	  function validate_bonus_added()
	  {
	  		$voucher_remaining_note = 'Do not miss out our bonus(es).
	  								   Please choose from the list and continue
	  									';	  		
	  		$validation = true;
	  		$voucher_id = $this->session->userdata['voucher_code_applied_details']['voucher_id'];

	  		$voucher_bonus_course_ebook_details = $this->voucher_code_model->get_voucher_bonus_course_ebook_details($voucher_id);
			$voucher_bonus_other_details = $this->voucher_code_model->get_voucher_bonus_other_details($voucher_id);

            //echo "<pre>";print_r($voucher_bonus_course_ebook_details);
			//echo "<pre>";print_r($voucher_bonus_other_details);exit;
			if(!empty($voucher_bonus_course_ebook_details))
			{
				foreach($voucher_bonus_course_ebook_details as $v_bonuses)
				{
					$voucher_bouns_array[$v_bonuses->bonus_type] = $v_bonuses->item_count_x;
				}				
			}			
			
			//echo "<pre>";print_r($voucher_bonus_other_details);exit;
			if(!empty($voucher_bonus_other_details))
			{
				foreach($voucher_bonus_other_details as $other_bonuses)
				{
				   $voucher_bouns_array[$other_bonuses->product_type] = count($other_bonuses->product_ids);		   
				}
			}

			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('voucher_cart_session_id'));	
           // echo "<pre>";print_r($voucher_bouns_array);exit;
			$cart_main_id = $cart_main_details[0]->id;
			foreach($voucher_bouns_array as $key=>$value)
			{
						
			if($key=='course' || $key=='ebooks')
			$product_in_cart = $this->sales_model->check_product_type_exist_in_cart($cart_main_id,'bonus_'.$key);
			/*elseif($key=='bonus_hardcopy')
			$product_in_cart = $this->sales_model->check_product_type_exist_in_cart($cart_main_id,'hardcopy');*/
			else
			$product_in_cart = $this->sales_model->check_product_type_exist_in_cart($cart_main_id,$key);
			
			
				
				if(!empty($product_in_cart))
				{
					$remaining_count = $voucher_bouns_array[$key] - count(explode(',',$product_in_cart[0]->selected_item_ids));
					if($remaining_count > 0)
					{
						$validation = false;
						$voucher_remaining_note .='Select '.$remaining_count.' more '.ucfirst(str_replace('_',' ',$key)).'
						';
					}
				}
				else
				{
					$validation = false;
					$voucher_remaining_note .= 'Select '.$voucher_bouns_array[$key].' more '.ucfirst(str_replace('_',' ',$key)).'
					';

				}
				//print_r($product_in_cart);
			} 

			$data['validation'] 	 = $validation;
			$data['validation_note'] = $voucher_remaining_note;

			echo json_encode($data); 
		    exit;

	  }


	  function add_bonus_products_others($product_id,$selected_values,$currency_id,$source,$ajax=1)
	  {
		  
		  
		/*if(!$this->session->userdata('student_logged_in')){
		  redirect('home');
		}	*/
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
		
//		$user_id = $this->session->userdata['student_logged_in']['id'];	
		$voucher_id = $this->session->userdata['voucher_code_applied_details']['voucher_id'];
				
		if(!$this->session->userdata('voucher_cart_session_id'))
		{
			redirect('voucher_code');
		}
		else
		{			

			$product_details = $this->common_model->get_product_details($product_id);	
			$product_type = $product_details[0]->type;		
				
			$bonus_org_product_type = str_replace("bonus_","",$product_type);

			if($bonus_org_product_type == 'extension'){

				$item_ids  = $product_details[0]->item_id;

			}else{

				$item_ids  = $selected_values_array;
			}

			$selected_items_org_price = $this->voucher_code_model->get_selected_items_price_details($bonus_org_product_type ,$item_ids,$currency_id);



			//echo $this->session->userdata('cart_session_id');
			
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('voucher_cart_session_id'));
			foreach($cart_main_details as $cart_main)
			{
				$cart_main_id = $cart_main->id;
				
				if($product_type =='bonus_extension')
				{
					$product_in_cart = $this->sales_model->check_product_type_with_selected_id_exist_in_cart($cart_main->id,$product_type,$selected_values);
				}
				else
				{					
					$product_in_cart = $this->sales_model->check_product_type_exist_in_cart($cart_main->id,$product_type);
				}


				if(empty($product_in_cart))
				{					
					
					//$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
					$product_amount = 0;				
					$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$product_id,"item_amount"=>$product_amount,"currency"=>$currency_id,'discount_amount'=>$selected_items_org_price);
					
					$cart_items_id = $this->common_model->insert_to_table("sales_cart_items",$item_details_array);		
				
					$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>$product_details[0]->type,"selected_item_ids"=>$selected_values);
				
					$item_id = $this->common_model->insert_to_table("sales_cart_item_details",$items_array);	
					
					$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main->id);
					
					$cart_items_total_amount=@round($cart_items_total_amount,2);
					
					
					$cart_total_items = count($this->sales_model->get_cart_items($cart_main->id));
					
					$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount);
					$this->sales_model->main_cart_details_update($this->session->userdata('voucher_cart_session_id'),$update_array);
										
					$currency_symbol = $this->common_model->get_currency_symbol_from_id($currency_id);
					
					if($ajax){
						$data['err_msg']= 0;
						$data['amount'] = $cart_items_total_amount;
						$data['count'] = $cart_total_items;
						$data['currency_symbol'] = $currency_symbol;
						echo json_encode($data); 
						exit;
					}
				}
				else // Assumes only one product id with same product_type exits, just need to update selected item id incase
				{	
										
						$cart_items_id = $product_in_cart[0]->cart_items_id;										
						$items_array = array("selected_item_ids"=>$new_selected_values);			
					
						$this->sales_model->sales_cart_item_details_update($cart_main_id,$cart_items_id,$items_array);	
						
						$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main->id);
						
						$cart_items_total_amount=@round($cart_items_total_amount,2);					
						$cart_total_items = count($this->sales_model->get_cart_items($cart_main->id));	
						$currency_symbol = $this->common_model->get_currency_symbol_from_id($currency_id);	
						if($ajax){					
							$data['err_msg']= 0;
							$data['amount'] = $cart_items_total_amount;
							$data['count'] = $cart_total_items;
							$data['currency_symbol'] = $currency_symbol;
							echo json_encode($data); 
							exit;
						}
					
				}
				
				//$cart_items = $this->sales_model->get_cart_items($cart_main->id);
			}
			
		 }
		 
	
	  
	  }
	 

	 function add_bonus_products_others_no_course($product_id,$currency_id)
	  {

		$voucher_id = $this->session->userdata['voucher_code_applied_details']['voucher_id'];
				
		if(!$this->session->userdata('voucher_cart_session_id'))
		{
			redirect('voucher_code');
		}
		else
		{			

			$product_details = $this->common_model->get_product_details($product_id);

			$product_type = $product_details[0]->type;		
				
			$bonus_org_product_type = str_replace("bonus_","",$product_type);

			if($bonus_org_product_type == 'hand_outs'){

				$item_ids  = $product_details[0]->item_id;

			}else{

				$item_ids  = 0;
			}
if($bonus_org_product_type == 'giftcard_offer'){
//$selected_items_org_price = $this->common_model->getProductFee($product_id,$currency_id);
	$selected_items_org_price =0; //because it is the discount amount of bonus. giftcard offer is not bonus.

}
else{
$selected_items_org_price = $this->voucher_code_model->get_selected_items_price_details($bonus_org_product_type,$item_ids,$currency_id);

}

			//echo $this->session->userdata('cart_session_id');
			
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('voucher_cart_session_id'));
			foreach($cart_main_details as $cart_main)
			{
				$cart_main_id = $cart_main->id;					
					
					//$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
					$product_amount = 0;	
					if($bonus_org_product_type == 'giftcard_offer'){
                    $product_amount_array = $this->common_model->getProductFee($product_id,$currency_id);
                    $product_amount=$product_amount_array['amount'];
					}			
					$item_details_array = 
					array("cart_main_id"=>$cart_main_id,"product_id"=>$product_id,
						"item_amount"=>$product_amount,"currency"=>$currency_id,'discount_amount'=>
						$selected_items_org_price);
				
					$cart_items_id = $this->common_model->insert_to_table("sales_cart_items",$item_details_array);	
					$selected_values = $product_details[0]->item_id;		
					//$selected_values = 0;
					$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>$product_details[0]->type,"selected_item_ids"=>$selected_values);
				
					$item_id = $this->common_model->insert_to_table("sales_cart_item_details",$items_array);	
					
					$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main->id);
					
					$cart_items_total_amount=@round($cart_items_total_amount,2);
					
					
					$cart_total_items = count($this->sales_model->get_cart_items($cart_main->id));
					
					$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount);
					$this->sales_model->main_cart_details_update($this->session->userdata('voucher_cart_session_id'),$update_array);
										
					$currency_symbol = $this->common_model->get_currency_symbol_from_id($currency_id);
					
					$data['err_msg']= 0;
					$data['amount'] = $cart_items_total_amount;
					$data['count'] = $cart_total_items;
					$data['currency_symbol'] = $currency_symbol;
					echo json_encode($data); 
					exit;				
			}
			
		 }
		 
	
	  
	  }

	  function add_extra_products_with_price_no_course($product_id,$currency_id){



		if(!$this->session->userdata('voucher_cart_session_id'))
		{
			redirect('voucher_code');
		}
		else
		{
			$product_details = $this->common_model->get_product_details($product_id);

			$product_type = $product_details[0]->type;			
			
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('voucher_cart_session_id'));
			foreach($cart_main_details as $cart_main)
			{
				$cart_main_id = $cart_main->id;

				$product_type_in_cart = $this->sales_model->check_product_type_exist_in_cart($cart_main->id,$product_type);

				if(empty($product_type_in_cart))
				{			
							
					$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
										
					$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$product_id,
						"item_amount"=>$product_price_details['amount'],"currency"=>$product_price_details['currency_id']);
					
					$cart_items_id = $this->common_model->insert_to_table("sales_cart_items",$item_details_array);		
				
					$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>$product_details[0]->type,"selected_item_ids"=>0);
				
					$item_id = $this->common_model->insert_to_table("sales_cart_item_details",$items_array);	
					
					$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main->id);
					
					$cart_items_total_amount=@round($cart_items_total_amount,2);
					
					
					//$cart_total_items = count($this->sales_model->get_cart_items($cart_main->id));
					$cart_total_items = $this->sales_model->get_cart_items_count($cart_main->id);		
					
					$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount);
					$this->sales_model->main_cart_details_update($this->session->userdata('voucher_cart_session_id'),$update_array);
										
					$currency_symbol = $this->common_model->get_currency_symbol_from_id($product_price_details['currency_id']);
					
					$data['err_msg']= 0;
					$data['added_product_amount'] = $product_price_details['amount'];
					$data['added_products_units'] = $product_details[0]->units;
					$data['amount'] = $cart_items_total_amount;
					$data['count'] = $cart_total_items;
					$data['currency_symbol'] = $currency_symbol;					
					echo json_encode($data); 
					exit;
				}
				else // already product type is in cart
				{ 
									
					$cart_item_id = $product_type_in_cart[0]->cart_items_id;					
					/* Remove that product type from cart */
					
					$removing_product_id = $this->sales_model->get_product_id_from_cart_items_id($cart_main_id,$cart_item_id);				
							
					$cart_details_by_product = $this->sales_model->get_cart_items_by_product_id($cart_main_id,$removing_product_id);
			
					$cart_details_id = $cart_details_by_product[0]->id;			
					$this->sales_model->delete_item_from_cart_by_product_id($cart_main_id,$cart_details_id,$removing_product_id);	
							
					/* REmoved already added package and add the package selected  */						
					$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
										
					$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$product_id,"item_amount"=>$product_price_details['amount'],"currency"=>$product_price_details['currency_id']);
					
					$cart_items_id = $this->common_model->insert_to_table("sales_cart_items",$item_details_array);		
				
					$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>$product_details[0]->type,"selected_item_ids"=>0);
				
					$item_id = $this->common_model->insert_to_table("sales_cart_item_details",$items_array);	
					
					$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main->id);
					
					$cart_items_total_amount=@round($cart_items_total_amount,2);
			
					$cart_total_items = $this->sales_model->get_cart_items_count($cart_main->id);		
					
					$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount);
					$this->sales_model->main_cart_details_update($this->session->userdata('voucher_cart_session_id'),$update_array);
										
					$currency_symbol = $this->common_model->get_currency_symbol_from_id($product_price_details['currency_id']);
					
					$data['err_msg']= 0;
					$data['added_product_amount'] = $product_price_details['amount'];
					$data['added_products_units'] = $product_details[0]->units;
					$data['amount'] = $cart_items_total_amount;
					$data['count'] = $cart_total_items;
					$data['currency_symbol'] = $currency_symbol;				
					echo json_encode($data); 
					exit;			
					
				}				
				
			}			

		}


	  }


	function remove_item_from_basket($product_id)
	{
		if($this->session->userdata('voucher_cart_session_id'))
		{
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('voucher_cart_session_id'));	
			$cart_main_id = $cart_main_details[0]->id;
			$cart_details_by_product = $this->sales_model->get_cart_items_by_product_id($cart_main_id,$product_id);
			if($cart_details_by_product){
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
				
			$this->sales_model->main_cart_details_update($this->session->userdata('voucher_cart_session_id'),$update_array);	
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('voucher_cart_session_id'));
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
		
	}
	
	
	function cart()
	{	

		if(!isset($this->session->userdata['voucher_code_applied_details']))
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
		$course_subscription_added = 0;
		
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
		
		
		
		  
		if($this->session->userdata('voucher_cart_session_id'))
		{
			$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('voucher_cart_session_id'));
				
			$currency_id = $cart_main_details[0]->currency_id;
			$currency_symbol = $this->common_model->get_currency_symbol_from_id($cart_main_details[0]->currency_id);
			$bonus=array();		
			foreach($cart_main_details as $cart_main)
			{
						
				$cart_main_id = $cart_main->id;					
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
						$bonus_type[$q]=$item_det->product_type;
						$selected_items = $item_det->selected_item_ids;
						if($item_det->product_type == 'ebooks' || $item_det->product_type == 'bonus_ebooks')
						{
							//**************** Anoop code start ***************************
						   if($item_det->product_type == 'bonus_ebooks'){
								//$bonus_course_product=$this->common_model->getProdectId('course',$item_det->selected_item_ids,1);
								$bonus_ebook_ids = explode(',',$selected_items);
								$bonus_ebook_product = $this->common_model->getProdectId('ebooks','',count($bonus_ebook_ids));
								
								//$data['bonus_item_original_price']=$this->common_model->getProductFee($bonus_course_product,$this->currId);
								//$bounus_prod_price['bonus_ebooks'] = $this->common_model->getProductFee($bonus_ebook_product,$this->currId);
								$bounus_prod_price['bonus_ebooks'] = $this->voucher_code_model->get_selected_items_price_details('ebooks' ,$bonus_ebook_ids,$currency_id);
								$bonus[$q]="yes";
							}
							
							//**************** Anoop code end ***************************
							$ebook_ids = explode(',',$selected_items);
							
							if($item_det->product_type == 'ebooks')
							{
								$product_name[$q] = 'Ebooks';
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
									$purchased_item_names[$q] = '<p>'.$ebook_details[0]->ebookName.'</p>';
									$product_images[$q] = 'public/user/outer/cart_img/'.$ebook_details[0]->image_name;	
								}
								else
								{
									$purchased_item_names[$q] .='<p>'.$ebook_details[0]->ebookName.'</p>';
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
								//$bounus_prod_price['bonus_course']['amount'] = $selected_items_org_price;
								$bounus_prod_price['bonus_course'] = $selected_items_org_price;
								//$bounus_prod_price['bonus_course'] = $this->common_model->getProductFee($bonus_course_product,$this->currId);
								$bonus[$q]="yes";
							}
							
							//**************** Anoop code end ***************************
							$course_ids = explode(',',$selected_items);
							if($item_det->product_type == 'course')
							{
								
								$product_name[$q] = "Course";
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
								$product_images[$q] = 'public/user/outer/img/'.$image_name;
							}
						
							
						}	
						else if($item_det->product_type == 'package')
						{
							$product_type[$q]= 'package';					
							$package_details = $this->package_model->fetch_package($selected_items);

							$products_in_package = explode(',',$package_details[0]->products);	
							$course_subscription = $this->common_model->get_product_by_type('course_subscription');				

							if(in_array($course_subscription[0]->id,$products_in_package))
							{								
								$purchased_item_names[$q] = '12 Months Course Subscription';
								$product_name[$q] = 'All courses';
								$course_subscription_added = 1;
							}
							else
							{								
								$purchased_item_names[$q] = $package_details[0]->package_name;
								$product_name[$q] = $this->user_model->translate_('package');
							}

							//$purchased_item_names[$q] = $package_details[0]->package_name;
							$product_images[$q] = 'public/user/outer/img/'.$package_details[0]->image_name;
						}

						else if($item_det->product_type == 'bundle')
						{
							//$package_id = $selected_items;
							$product_name[$q] = 'Course bundle';
							$product_type[$q]= 'Courses';					
							$bundle_details = $this->voucher_code_model->get_bundle_details($selected_items);
							$purchased_item_names[$q] = $bundle_details[0]->bundle_name;
							$product_images[$q] = 'public/upload/bundle_images/'.$bundle_details[0]->image;
						}

						else if($item_det->product_type == 'tool_kit' || $item_det->product_type == 'bonus_tool_kit')
						{
							//$package_id = $selected_items;
							$product_name[$q] = 'Tool kit';
							$product_type[$q]= 'Tool kit';												
							$purchased_item_names[$q] = 'Tool kit';
							$product_images[$q] = 'public/uploads/tool_kit_images/tool_kit.jpg';
						}
						else if($item_det->product_type == 'hand_outs' || $item_det->product_type == 'bonus_hand_outs')
						{	
							
							if($item_det->product_type == 'bonus_hand_outs'){
								
			                 	$bonus_hand_out_product = $this->common_model->getProdectId('hand_outs',$product_details[0]->item_id,1);
			                 	//$bounus_prod_price['bonus_hand_outs'] = $this->common_model->getProductFee($bonus_hand_out_product,$this->currId);	
			                 	$bounus_prod_price['bonus_hand_outs'] = $this->voucher_code_model->get_product_price($bonus_hand_out_product,$this->currId);
								$bonus[$q]="yes";
							}
														
							if($item_det->product_type == 'hand_outs')
							{
								$product_name[$q] =  'Hand out';
							}
							else
							{
								$product_name[$q] = 'Bonus Hand out';
							}

							$hand_out_details = $this->voucher_code_model->get_hand_out_details($product_details[0]->item_id);
													
							$purchased_item_names[$q] = '<p>'.$hand_out_details[0]['hand_out_type'].'</p>';
							
							$product_images[$q] = 'public/user/outer/img/extend_course.jpg';
						}

						else if($item_det->product_type == 'voucher_code')
						{
							

							if($this->session->userdata['voucher_code_applied_details']['product_type'] == 'course')
							{
								$course_ids = explode(',',$selected_items);
								$product_name[$q] = "Course";
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
									$product_images[$q] = 'public/user/outer/img/'.$image_name;
								}
							}
							else if($this->session->userdata['voucher_code_applied_details']['product_type'] == 'course_bundle')
							{
								//$package_id = $selected_items;
								$product_name[$q] = 'Course bundle';
								$product_type[$q]= 'Courses';					
								$bundle_details = $this->voucher_code_model->get_bundle_details($selected_items);
								$purchased_item_names[$q] = $bundle_details[0]->bundle_name;
								$product_images[$product_type_image[$q]][$q][0] = 'public/user/outer/img/course1.jpg';
								
							}
							else if($this->session->userdata['voucher_code_applied_details']['product_type'] == 'course_subscription')
							{			
								$voucher_subscription_course_array = $this->voucher_code_model->get_subscription_course_for_voucher($this->session->userdata['voucher_code_applied_details']['voucher_id']);

								$product_name[$q] = 'All Courses';
								$product_type[$q]= 'Courses';	
								if($voucher_subscription_course_array[0]->subscription > 730)
								{
									$purchased_item_names[$q] = 'Lifetime access';									
								}
								else if($voucher_subscription_course_array[0]->subscription==730)
                                        {
                                             $purchased_item_names[$q] = '24 Months Course Subscription';                                            
                                        }
                                        else
                                        {
                                             $purchased_item_names[$q] = '12 Months Course Subscription';
                                        }
								$product_images[$q]  = 'public/user/outer/img/course1.jpg';
							}
							elseif($this->session->userdata['voucher_code_applied_details']['product_type'] == 'ebooks')
							{
								$ebook_ids = explode(',',$selected_items);
								$product_name[$q] = "Ebooks";
								for($qq=0;$qq<count($ebook_ids);$qq++)
								{

									$ebook_details = $this->ebook_model->get_ebook_details($ebook_ids[$qq]);	
													
									if($purchased_item_names[$q]=='')
									{
										$purchased_item_names[$q] = '<p>'.$ebook_details[0]->ebookName.'</p>';
										$product_images[$q] = 'public/user/outer/cart_img/'.$ebook_details[0]->image_name;	
									}
									else
									{
										$purchased_item_names[$q] .='<p>'.$ebook_details[0]->ebookName.'</p>';
									}




								}
							}
							elseif($this->session->userdata['voucher_code_applied_details']['product_type'] == 'extension')
							{

								$voucher_id  	  = $this->session->userdata['voucher_code_applied_details']['voucher_id'];

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
								if($image_name!='' ){

								$product_images[$q] = 'public/user/outer/img/'.$image_name;
								}
								else
								{
									$product_images[$q] = 'public/user/outer/img/extend_course.jpg';									
								}
								
							}
							else if($this->session->userdata['voucher_code_applied_details']['product_type'] == 'tool_kit' || $this->session->userdata['voucher_code_applied_details']['product_type'] == 'bonus_tool_kit')
							{							
								$product_name[$q] = 'Tool kit';
								$product_type[$q]= 'Tool kit';												
								$purchased_item_names[$q] = 'Tool kit';
								$product_images[$q] = 'public/uploads/tool_kit_images/tool_kit.jpg';
							}
							elseif($this->session->userdata['voucher_code_applied_details']['product_type'] == 'letters')
							{

								$voucher_id  	  = $this->session->userdata['voucher_code_applied_details']['voucher_id'];

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
								$product_images[$q] = 'public/user/outer/img/proof_enrolement_image.jpg';


							}	

						}						
						else if($item_det->product_type == 'extension' || $item_det->product_type == 'bonus_extension')
						{
							
							
							//**************** Anoop code start ***************************
							if($item_det->product_type == 'bonus_extension'){
								//echo "bonus_extension";exit;
			                 	$bonus_extension_product=$this->common_model->getProdectId('extension',$product_details[0]->item_id,1);
			                 	//$bounus_prod_price['bonus_extension'] = $this->common_model->getProductFee($bonus_extension_product,$this->currId);		
			                 	$bounus_prod_price['bonus_extension'] = $this->voucher_code_model->get_product_price($bonus_extension_product,$this->currId);							
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

							$image_name = $this->sales_model->get_course_image($selected_items); 
							if($image_name!='' ){

							$product_images[$q] = 'public/user/outer/img/'.$image_name;
							}
							else
							{
						
								$product_images[$q] = 'public/user/outer/img/extend_course.jpg';
							}
						}						
						else if($item_det->product_type == 'poe_soft' || $item_det->product_type == 'bonus_poe_soft')
						{
							//**************** Anoop code start ***************************
							if($item_det->product_type == 'bonus_poe_soft'){
								$bonus_poe_product=$this->common_model->getProdectId('poe_soft','',1);
								//$bounus_prod_price['bonus_poe_soft'] =$this->common_model->getProductFee($bonus_poe_product,$this->currId);
								$bounus_prod_price['bonus_poe_soft'] = $this->voucher_code_model->get_product_price($bonus_poe_product,$this->currId);	
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
							$product_images[$q] = 'public/user/outer/img/proof_enrolement_image.jpg';
						}
						else if($item_det->product_type == 'poe_hard' || $item_det->product_type == 'bonus_poe_hard')
						{
							
						//**************** Anoop code start ***************************
							if($item_det->product_type == 'bonus_poe_hard'){
								$bonus_poe_hard_product=$this->common_model->getProdectId('poe_hard','',1);
								//$bounus_prod_price['bonus_poe_hard'] =$this->common_model->getProductFee($bonus_poe_hard_product,$this->currId);
								$bounus_prod_price['bonus_poe_hard'] = $this->voucher_code_model->get_product_price($bonus_poe_hard_product,$this->currId);
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
							$product_images[$q] = 'public/user/outer/img/proof_enrolement_image.jpg';
						}
						else if($item_det->product_type == 'hardcopy' || $item_det->product_type == 'bonus_hardcopy') 
						{
							
							//**************** Anoop code start ***************************
							if($item_det->product_type == 'bonus_hardcopy'){
								$bonus_hardcopy_product=$this->common_model->getProdectId('hardcopy',1,1);
								//$bounus_prod_price['bonus_hardcopy'] =$this->common_model->getProductFee($bonus_hardcopy_product,$this->currId);
								$bounus_prod_price['bonus_hardcopy'] = $this->voucher_code_model->get_product_price($bonus_hardcopy_product,$this->currId);
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
							$product_images[$q] = 'public/user/outer/img/icoes-large.png';
						}					
						else if($item_det->product_type == 'proof_completion' || $item_det->product_type == 'bonus_proof_completion')
						{
							
							//**************** Anoop code start ***************************
							if($item_det->product_type == 'bonus_proof_completion'){
								
								
								$bonus_poc_product=$this->common_model->getProdectId('proof_completion','',1);
								//$bounus_prod_price['bonus_proof_completion'] =$this->common_model->getProductFee($bonus_poc_product,$this->currId);
								$bounus_prod_price['bonus_proof_completion'] = $this->voucher_code_model->get_product_price($bonus_poc_product,$this->currId);
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
							$product_images[$q] = 'public/user/outer/img/proof_enrolement_image.jpg';
						}
						else if($item_det->product_type == 'proof_completion_hard' || $item_det->product_type == 'bonus_proof_completion_hard')
						{
							
							//**************** Anoop code start ***************************
							if($item_det->product_type == 'bonus_proof_completion_hard'){
								$bonus_poc_hard_product=$this->common_model->getProdectId('proof_completion_hard','',1);
								//$bounus_prod_price['bonus_proof_completion_hard'] =$this->common_model->getProductFee($bonus_poc_hard_product,$this->currId);
								$bounus_prod_price['bonus_proof_completion_hard'] = $this->voucher_code_model->get_product_price($bonus_poc_hard_product,$this->currId);
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
							$product_images[$q] = 'public/user/outer/img/proof_enrolement_image.jpg';
						}						
						else if($item_det->product_type == 'transcript' || $item_det->product_type == 'bonus_transcript')
						{
								//**************** Anoop code start ***************************
							if($item_det->product_type == 'bonus_transcript'){
								$bonus_transcript_product=$this->common_model->getProdectId('transcript','',1);
								//$bounus_prod_price['bonus_transcript'] =$this->common_model->getProductFee($bonus_transcript_product,$this->currId);
								$bounus_prod_price['bonus_transcript'] = $this->voucher_code_model->get_product_price($bonus_transcript_product,$this->currId);
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
							$product_images[$q] = 'public/user/outer/img/proof_enrolement_image.jpg';
						}
						else if($item_det->product_type == 'transcript_hard' || $item_det->product_type == 'bonus_transcript_hard')
						{
							
								//**************** Anoop code start ***************************
							if($item_det->product_type == 'bonus_transcript_hard'){
								$bonus_transcript_hard_product=$this->common_model->getProdectId('transcript_hard','',1);
								//$bounus_prod_price['bonus_transcript_hard'] =$this->common_model->getProductFee($bonus_transcript_hard_product,$this->currId);
								$bounus_prod_price['bonus_transcript_hard'] = $this->voucher_code_model->get_product_price($bonus_transcript_hard_product,$this->currId);
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
							$product_images[$q] = 'public/user/outer/img/proof_enrolement_image.jpg';
						}
						else if($item_det->product_type == 'gift_campaign' || $item_det->product_type == 'giftcard_offer' || $item_det->product_type == 'bonus_gift_campaign') 
						{
							
							//**************** Anoop code start ***************************
							if($item_det->product_type == 'bonus_gift_campaign'){
								$bonus_giftcard_product=$this->common_model->getProdectId('gift_campaign','',1);
								//$bounus_prod_price['bonus_gift_campaign'] =$this->common_model->getProductFee($bonus_giftcard_product,$this->currId);
								$bounus_prod_price['bonus_gift_campaign'] = $this->voucher_code_model->get_product_price($bonus_giftcard_product,$this->currId);
								$bonus[$q]="yes";
							}
						
							if($item_det->product_type == 'gift_campaign')
							{
								$product_name[$q] =  'Giftcard';	
								$purchased_item_names[$q] = 'Giftcard'; 
							}
							elseif($item_det->product_type == 'giftcard_offer'){
                           $product_name[$q] =  'Giftcard offer';
                           $purchased_item_names[$q] = 'Giftcard offer';
							}
							else
							{
								$product_name[$q] =  'Bonus Giftcard';	
								$purchased_item_names[$q] = 'Bonus Giftcard'; 
							}
							
							$product_images[$q] = 'public/user/outer/images/gift-card-new.jpg';
						}	
						if($item_det->product_type == 'voucher_code' && $this->session->userdata['voucher_code_applied_details']['product_type'] =='letters')
						{
							$product_name[$q] =  'Letter';
						}
						else if($item_det->product_type == 'access')
						{
							$product_name[$q] =  $this->user_model->translate_('sales_product_name_material_access');					
							$purchased_item_names[$q] = $product_details[0]->item_id.' '.$this->user_model->translate_('months');
							$product_images[$q] = 'public/user/outer/img/extend_course.jpg';
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
			//echo "<pre>";print_r($bounus_prod_price);exit;
			}
			if(isset($bonus)){
				//echo "<pre>";print_r($bonus);exit;
			$data['bonus']      	   = $bonus;
			}
			$data['bonus_type']      	   = $bonus_type;
		
		$content['course_subscription_added'] = $course_subscription_added;	
		$data['discount_amount'] 	  	   = $discount_amount; 
		$content['purchased_item_names']   = $purchased_item_names;
		$content['products_in_cart']  	   = $products_in_cart;
		$content['product_name']      	   = $product_name;
		$content['product_images']  	     = $product_images;
		$content['currency_id'] 			= $currency_id;		
		$content['cart_main_details'] 	  = $cart_main_details;
			
		$data['currency_symbol'] 		   = $currency_symbol;
		$data['translate'] 				 = $this->tr_common;
		$data['view'] 					  = 'voucher_cart';
        $data['content'] = $content;	
		
			        
		$this->load->view('user/template_outer',$data); 
	}
	
	function after_pay($user_id='')
	{
		 
	
		$this->tr_common['tr_Registration_complete'] =$this->user_model->translate_('Registration_complete');
		$this->tr_common['tr_Your_payment_was_a_success_and_now_you_are_on_the_road_to_success'] =$this->user_model->translate_('Your_payment_was_a_success_and_now_you_are_on_the_road_to_success');
		$this->tr_common['tr_Welcome_to_Trendimi'] =$this->user_model->translate_('Welcome_to_Trendimi');
		$this->tr_common['tr_Your_payment_was_a_success_and_here_at_Trendimi_we_see_success_in_your_future'] =$this->user_model->translate_('Your_payment_was_a_success_and_here_at_Trendimi_we_see_success_in_your_future');

		$this->tr_common['tr_Your_course_and_or_supplementary_products_are_now_available_in_your_Virtual_Campus'] =$this->user_model->translate_('Your_course_and_or_supplementary_products_are_now_available_in_your_Virtual_Campus');
		$this->tr_common['tr_Purchased_products'] =$this->user_model->translate_('Purchased_products');
		$this->tr_common['tr_No_products_in_the_cart'] =$this->user_model->translate_('No_products_in_the_cart');

		$this->tr_common['tr_voucher_subscription_text_new_user_1'] = $this->user_model->translate_('voucher_subscription_text_new_user_1');
		$this->tr_common['tr_voucher_subscription_text_new_user_2'] = $this->user_model->translate_('voucher_subscription_text_new_user_2');
		$this->tr_common['tr_voucher_subscription_text_user'] 		= $this->user_model->translate_('voucher_subscription_text_user');
		$content = array();
		//$content=$this->get_student_deatils_for_popup();
		$lang_id = $this->session->userdata('language');
		//if($this->session->userdata('public_id'))
		//$content['public_id']=$this->session->userdata('public_id');
		$purchase_note ='';
		if($user_id!="")
		{
			$userDetails = $this->user_model->get_student_temp($user_id);
			$data['user_name']=$userDetails[0]->username;
			$us_password = $this->encrypt->decode($userDetails[0]->password);
			$str_len = strlen($us_password);    	
		    $char = str_repeat("*", ($str_len-3));
		    $us_password = substr_replace($us_password,$char,3,($str_len-3));
			$data['password']=$us_password;
		}		
		
		if(isset($this->session->userdata['voucher_code_applied_details']))
		$data['voucher_product_type'] = $this->session->userdata['voucher_code_applied_details']['product_type'];
		else
		$data['voucher_product_type'] = '';	
		
		$this->session->unset_userdata('voucher_cart_session_id');
		$this->session->unset_userdata('voucher_code_applied');
		$this->session->unset_userdata('voucher_code_applied_details');
		$this->session->unset_userdata('cart_source'); 
		$this->session->unset_userdata('added_user_id');
		$this->session->unset_userdata('voucher_code_pre_user_id');			
	    $this->session->unset_userdata('ebook_public_email');
	    $this->session->unset_userdata('voucher_package_applying_course');	
		//$this->session->unset_userdata('public_id');
		if(isset($purchase_note))	
		$data['purchase_note'] = $purchase_note;
		else
		$data['purchase_note'] ='';
		$data['translate'] = $this->tr_common;
		$data['view'] = 'voucher_code_after_pay';
        $data['content'] = $content;		
		 if(!$this->session->userdata('student_logged_in')){
			  $this->load->view('user/template_outer',$data);
		 }else
		 {
        $this->load->view('user/template_outer',$data); 
		 }
		
		
	}
	function select_ebook()
	  {
	  		if(!isset($this->session->userdata['voucher_code_applied_details']))
			{
				redirect('voucher_code');
			}			
			
			$content = array();
			$added_products_array = array();
			$enrolled_course_array = array();

			$voucher_ebook_array = $this->voucher_code_model->get_ebooks_for_voucher($this->session->userdata['voucher_code_applied_details']['voucher_id']);
		
			$voucher_code_ebook_id_array = explode(',',$voucher_ebook_array[0]->course_ebook_ids);
					
			$ebook_array = $this->voucher_code_model->get_ebooks($voucher_code_ebook_id_array);
            if($this->input->post('ebook_next'))
			{
				
				$this->form_validation->set_rules('ebook_email', 'Email', 'trim|required|xss_clean');
				$this->form_validation->set_rules('ebook_user_name', 'Name', 'trim|required|xss_clean');
				if($this->form_validation->run() == TRUE)
				{	
				$ebook_public_array['email']=$this->input->post('ebook_email');
				$ebook_public_array['name']=$this->input->post('ebook_user_name');
				$this->check_public_user_used_this_voucher_code($ebook_public_array['email']);
				$inserted=$this->ebook_model->add_public($ebook_public_array);
				$sess_array = array('ebook_public_email' => $this->input->post('ebook_email'),'public_id'=>$inserted); 	
			    $this->session->set_userdata($sess_array);
				
				}
				else{
					$this->session->set_flashdata('popup_message_public','Enter the Name and Email.');
					
				redirect("voucher_code/ebooks");	
				}
			}
          
			if($this->session->userdata('voucher_cart_session_id'))
			{			
				$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('voucher_cart_session_id'));			
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
			$voucher_id = $this->session->userdata['voucher_code_applied_details']['voucher_id'];
			if($this->session->userdata['voucher_code_applied_details']['discount_type'] == 'offer_price')
			{				
				$voucher_ebook_product_id = $this->common_model->getProdectId('voucher_code',$voucher_id);
			}	
			else 
			{				
				$voucher_ebook_product_id = $this->common_model->getProdectId($this->session->userdata['voucher_code_applied_details']['product_type'],$voucher_course_array[0]->item_count_x);
			}	
			$this->tr_common['tr_trndimi_ebooks'] = $this->user_model->translate_('trendimi_ebooks');
			$this->tr_common['tr_check_out'] = $this->user_model->translate_('check_out');
		    $this->tr_common['tr_total_price'] = $this->user_model->translate_('total_price');

			$content['added_products_array'] = $added_products_array;	
			$content['lang_id'] 			 = $this->language;
			//$content['user_logged_in'] 	     = $user_logged_in;
			$content['voucher_ebook_product_id'] = $voucher_ebook_product_id;
			$content['voucher_ebook_array'] = $voucher_ebook_array;	
			$content['ebook_array'] 		 = $ebook_array;	
			$content['translate'] 		     = $this->tr_common;
			$content['view'] 				 = 'voucher_code_ebook_public';
			$title['pageTitle'] 			 = 'Login';
			$content['content'] 			 = $title;
			$this->load->view('user/template_outer',$content); 
	  }
	  
	  


	  function apply_voucher_code($voucher_code,$product_type,$currency_id){

		if(isset($this->session->userdata['voucher_code_applied_details'])){

			$data['err_type'] = 'Voucher already applied';
			$data['err_msg']= 1;						
			echo json_encode($data); 
			exit;
		}
		else
		{	

			$voucher_code_details = $this->voucher_code_model->get_voucher_code_details($voucher_code);
						
			if($voucher_code_details)
			{	

				if($voucher_code_details[0]->reusable =='0' && $voucher_code_details[0]->used > 0)
				{
					$data['err_type'] = 'Voucher Code already used';
					$data['err_msg']= 1;						
					echo json_encode($data); 
					exit;
				}
				else if($voucher_code_details[0]->product_type!=$product_type){

					$data['err_type'] = 'Voucher Code not applicable for '.$product_type;
					$data['err_msg']= 1;						
					echo json_encode($data); 
					exit;
				}
				
				else
				{


					/* Check added voucher code applicable for cart added products */

					$voucher_id = $voucher_code_details[0]->id;		

					$extension_product_id = $this->session->userdata('extension_session_id');

					if($voucher_code_details[0]->product_type == 'extension' && $voucher_code_details[0]->discount_type !='percentage'){

						$is_voucher_applicable = $this->voucher_code_model->voucher_applicable_for_this_product($extension_product_id,$voucher_id,$voucher_code_details[0]->product_type);

						if($is_voucher_applicable['err'] == 1){

								$data['sugg_msg'] = $is_voucher_applicable['sugg_msg'];
								$data['err_msg']= 2;						
								echo json_encode($data); 
								exit;
						}
					}

					/* End Check added voucher code applicable for added products */

					$sess_array = array('voucher_code_applied' => true);
					$this->session->set_userdata($sess_array);				
					
					$data['voucher_id'] 	 = $voucher_code_details[0]->id;					
					$data['product_type'] 	 = $voucher_code_details[0]->product_type;
					$data['discount_type']   = $voucher_code_details[0]->discount_type;					
					$data['vouchercode'] 	 = $voucher_code_details[0]->vouchercode;
					$data['voucher_code_added_source'] 	 = $voucher_code_details[0]->voucher_source;

					$sess_array = array('voucher_code_applied_details' => $data);
				    $this->session->set_userdata($sess_array);				

				    $sess_array = array('voucher_cart_session_id' => $this->session->userdata('cart_session_id'));
					$this->session->set_userdata($sess_array);

					//$this->session->unset_userdata('cart_session_id');
					
					$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('voucher_cart_session_id'));
					foreach($cart_main_details as $cart_main)
					{
						$cart_main_id = $cart_main->id;
						$product_in_cart = $this->sales_model->check_product_type_exist_in_cart($cart_main->id,$product_type);

						if(!empty($product_in_cart))
						{							
											
							$cart_items_id = $product_in_cart[0]->cart_items_id;
							$product_id = $this->sales_model->get_product_from_cart_items_id($cart_items_id);
							
							$product_details = $this->common_model->get_product_details($product_id);
				
							$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
							
							$voucher_details = $this->voucher_code_model->get_voucher($voucher_id);
							
							$product_amount = $org_product_amt = $product_price_details['amount'];
										
							if($voucher_details[0]->discount_type=='free')
							{				
								$discount_amount = $product_amount;	
								$product_amount = 0;
								$product_price_details['currency_id'] = $currency_id;
							}			
							else if($voucher_details[0]->discount_type=='percentage')
							{						
								$discount_value  = $voucher_details[0]->discount_percentage;				
								$product_amount  = $product_amount - round(( ($product_amount * $discount_value) / 100 ),2);									
								$discount_amount = $org_product_amt - $product_amount;
							}
							elseif($voucher_details[0]->discount_type=='discount_price')
							{			
							
							    $currency_id_discount_value = $discount_amount = $this->voucher_code_model->get_amount_for_voucher_code($voucher_id,$currency_id);
								
								if($currency_id_discount_value!='')
								{				
									//$discount_amount = $product_amount-($currency_id_discount_value);
									$product_amount = $product_amount - $currency_id_discount_value;
									if($discount_amount<=0)
									{
										$data['err_msg']= 1;
										$data['err_type'] = 'Something went wrong. Plaese contact info@trendimi.com';	
										echo json_encode($data); 
										exit;	
									}							
								}
								else
								{
									$data['err_msg']= 1;
									$data['err_type'] = 'Currency not supported. Plaese contact info@trendimi.com';		
									echo json_encode($data); 
									exit;									
								}
							}

							elseif($voucher_details[0]->discount_type=='offer_price')
							{			
								
								// Product price as per the orignal products

								$product_details = $this->common_model->get_product_details($product_id);
							
								$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
								
								$voucher_details = $this->voucher_code_model->get_voucher($voucher_id);
								
								$org_product_amt = $product_price_details['amount'];


								// Get voucher code product details

								$product_id = $this->common_model->getProdectId('voucher_code',$voucher_id);

								$product_details = $this->common_model->get_product_details($product_id);
							
								$product_price_details = $this->common_model->getProductFee($product_id,$currency_id);
								
								$voucher_details = $this->voucher_code_model->get_voucher($voucher_id);
								
								$product_amount = $product_price_details['amount'];

								$discount_amount = $org_product_amt - $product_amount;

							}
												
							$item_details_array = array("product_id"=>$product_id,"item_amount"=>$product_amount,"currency"=>$currency_id,'discount_amount'=>$discount_amount);				
							//sales_cart_items_update
							
							$this->sales_model->sales_cart_items_update($cart_items_id,$item_details_array);	
										
							$items_array = array("product_type"=>$product_details[0]->type);			
						
							$this->sales_model->sales_cart_item_details_update($cart_main_id,$cart_items_id,$items_array);	
							
							$cart_items_total_amount = $this->sales_model->get_cart_items_total_amount($cart_main->id);
							
							$cart_items_total_amount=@round($cart_items_total_amount,2);
							
							
							$cart_total_items = count($this->sales_model->get_cart_items($cart_main->id));
							
							$update_array = array("item_count"=>$cart_total_items,"total_cart_amount"=>$cart_items_total_amount);

							$this->sales_model->main_cart_details_update($this->session->userdata('voucher_cart_session_id'),$update_array);
												
							$currency_symbol = $this->common_model->get_currency_symbol_from_id($currency_id);



							
							$data['err_msg']= 0;
							$data['amount'] = $cart_items_total_amount;
							$data['discount_amount'] = $discount_amount;
							$data['count'] = $cart_total_items;
							$data['currency_symbol'] = $currency_symbol;
							echo json_encode($data); 
							exit;
						
					
						}
					}

										
					$sess_array = array('voucher_code_applied_details' => $data);
				    $this->session->set_userdata($sess_array);				
					echo json_encode($data); 
					exit;		
				}				
				
			}
			else
			{			
				$voucher_code_err_details = $this->voucher_code_model->get_voucher_code($voucher_code);
				if($voucher_code_err_details)
				{
					$currentDate  =date("Y-m-d",time());
					
					if($voucher_code_err_details[0]->status != '0')
					{
						if($voucher_code_err_details[0]->start_date > $currentDate )
						{
							$data['err_type'] = 'Voucher Code not activated';
						}
						else if($voucher_code_err_details[0]->end_date < $currentDate)
						{
							$data['err_type'] = 'Voucher Code expired';
						}
					}
					else
					{
						$data['err_type'] = 'Voucher Code not active';
					}
					
					$data['err_msg']= 1;						
					echo json_encode($data); 
					exit;
				}
				
				
				$data['err_msg']= 1;	
				$data['err_type'] = 'Voucher Code not found';		
				echo json_encode($data); 
				exit;
			}

		}

	}



	function process_voucher_codes_applied(){

  		if(!isset($this->session->userdata['voucher_code_applied_details'])){

  			$data['voucher_code_applied'] 	 = false;
			echo json_encode($data); 
		    exit;
  		}
  		else
  		{

  			$voucher_id = $this->session->userdata['voucher_code_applied_details']['voucher_id'];
			$voucher_details = $this->voucher_code_model->get_voucher($voucher_id);
			$voucher_bonus_course_ebook_details = $this->voucher_code_model->get_voucher_bonus_course_ebook_details($voucher_id);
			$voucher_bonus_other_details = $this->voucher_code_model->get_voucher_bonus_other_details($voucher_id);

			if(empty($voucher_bonus_array) && empty($voucher_bonus_other_array))
			{
			
				$data['voucher_code_applied'] 	 = true;
	  			$data['voucher_code_bonus'] 	 = false;
				echo json_encode($data); 
			    exit;
			}
			else
			{
	  			$data['voucher_code_applied'] 	 = true;
	  			$data['voucher_code_bonus'] 	 = true;
				echo json_encode($data); 
			    exit;
				
			}
  		}  		

	}

	// For Stripe payment
	
	function payment_option()
 	{


 		$currency_id = $this->uri->segment(3);		
				
		$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('voucher_cart_session_id'));
		
		foreach($cart_main_details as $cart_main)
		{
			$amount = $cart_main->total_cart_amount;
		
		}
		
		$currency_details = $this->common_model->get_currency_details($currency_id);

		$content['currency_id']		=$currency_id;
		
		$content['currency']		=$currency_details['currency_code'];

		$content['amount']			=$amount;	

		$data['translate']		   = $this->tr_common;
		
		$data['view'] 			   = 'voucher_code_payment_option';
		
		$data['content'] 		   = $content;
		
		$this->load->view('user/template_pay',$data);



 	}
function course_subscription_landing_page()
	 {
	 	$content = array();
	 	$content['course_array'] = $this->user_model->get_acitive_courses_only($this->language);
	 	$content['translate'] 	= $this->tr_common;
	 	$content['view'] 		= '';
		$title['pageTitle'] 	= 'Login';
		$content['content'] 	= $title;
		//$this->load->view('user/template_outer',$content); 	  

		$this->load->view('user/voucher_code_course_subscription_landing_page',$content); 	

	 } 	

}
	