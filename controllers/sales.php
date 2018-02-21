<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class sales extends CI_Controller
{
	


    function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper(array('form'));
		$this->load->library('user_agent');	
        $this->load->helper('url');
        $this->load->database('',true);	
      //$this->load->helper(array('language'));
		$this->load->library('form_validation');
		$this->load->library('encrypt');
		
		$this->load->model('user_model','',TRUE);
		$this->load->model('common_model','',TRUE);	
		$this->load->model('campaign_model','',TRUE);		
		$this->load->model('gift_voucher_model','',TRUE);
		$this->load->model('certificate_model','',TRUE);
		$this->load->model('package_model','',TRUE);
		$this->load->model('sales_model','',TRUE);
		$this->load->model('pdf_html_model','',TRUE);
		
		//echo $this->input->ip_address();
		//$this->load->library('geoip_lib');
		$ip = $this->input->ip_address();
    //$this->geoip_lib->InfoIP($ip);
    //$this->code3= $this->geoip_lib->result_country_code3();
    //$this->con_name = $this->geoip_lib->result_country_name();
	$this->load->library('ip2country_lib');
	$this->con_name = $this->ip2country_lib->getInfo();
	
		if(isset($_POST['username'])&&isset($_POST['password']))
		{
			$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
   		$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_check_database');
			$content['username'] = $this->input->post('username');
			if(!$this->form_validation->run() != FALSE)
			{//Go to private area
				redirect('sales/deals/', 'refresh');
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
			 $this->tr_common['tr_forget_password'] =$this->user_model->translate_('forget_password');
		 $this->tr_common['tr_name']            =$this->user_model->translate_('name');
		 $this->tr_common['tr_Email_pop']            =$this->user_model->translate_('Email_pop');
		 $this->tr_common['tr_eventrix'] =$this->user_model->translate_('eventrix');
         $this->tr_common['tr_sign']        =$this->user_model->translate_('sign');
		 $this->tr_common['tr_Out'] =$this->user_model->translate_('Out');
         $this->tr_common['tr_return_to']        =$this->user_model->translate_('return_to');
		 
		 $this->tr_common['tr_campus'] =$this->user_model->translate_('campus');
         $this->tr_common['tr_voucher_registration']        =$this->user_model->translate_('voucher_registration');
		 $this->tr_common['tr_voucher_text1'] =$this->user_model->translate_('voucher_text1');
         $this->tr_common['tr_voucher_text2']        =$this->user_model->translate_('voucher_text2');
		 $this->tr_common['tr_enter_voucher_code']        =$this->user_model->translate_('enter_voucher_code');
		 
		  $this->tr_common['tr_getting_started']        =$this->user_model->translate_('getting_started');
		  $this->tr_common['tr_submit']        =$this->user_model->translate_('submit');
		 
		 $this->tr_common['tr_why_us']   =$this->user_model->translate_('why_us');
		 $this->tr_common['tr_user_name']      =$this->user_model->translate_('user_name');
         $this->tr_common['tr_password']            =$this->user_model->translate_('password');		 
    	
		
		$this->tr_common['tr_change_photo']   = $this->user_model->translate_('change_foto'); 	  	 
		$this->tr_common['tr_edit_details'] = $this->user_model->translate_('edit_your_account_details');
		$this->tr_common['tr_change_password'] = $this->user_model->translate_('change_password');
		$this->tr_common['tr_help_center'] = $this->user_model->translate_('help_center');		
		$this->tr_common['tr_extend_course'] = $this->user_model->translate_('extend_course');
		$this->tr_common['tr_certificate'] = $this->user_model->translate_('certificate');
		$this->tr_common['tr_contact_us'] = $this->user_model->translate_('contact_us');
		
		
		 
		 $this->tr_common['tr_about_us']   =$this->user_model->translate_('about_us');
  		 $this->tr_common['tr_faq']        =$this->user_model->translate_('faq');   
      	 $this->tr_common['tr_contact_us'] =$this->user_model->translate_('contact_us');
		 $this->tr_common['tr_terms_use']        =$this->user_model->translate_('terms_use');   
    	 $this->tr_common['tr_privacy_policy'] =$this->user_model->translate_('privacy_policy');
		 $this->tr_common['tr_enter_password'] = $this->user_model->translate_('enter_password');
		$this->tr_common['tr_enter_new_password'] = $this->user_model->translate_('enter_new_password');
		$this->tr_common['tr_confirm_new_password'] = $this->user_model->translate_('confirm_new_password');
		$this->tr_common['tr_btn_next'] = $this->user_model->translate_('btn_next');
		
  }
 	function index(){
	

  }
 
 
 
	  function apply_proof_study($course_id)
	  {
		
		 if(!$this->session->userdata('student_logged_in')){
		  redirect('home');
		}
		
		$user_id=$this->session->userdata['student_logged_in']['id']; 
		 
		$content = array();
		$currency_id = $this->currId;
		$currency_code = $this->currencyCode;
		
		$this->tr_common['tr_proof_study_can_supply_txt'] = $this->user_model->translate_('proof_study_can_supply_txt');
		$this->tr_common['tr_proof_study']   =$this->user_model->translate_('proof_study');
		$this->tr_common['tr_proof_study_can_use']   =$this->user_model->translate_('proof_study_can_use');
		$this->tr_common['tr_book_now']   =$this->user_model->translate_('book_now');
		
		$this->tr_common['tr_select_option']   =$this->user_model->translate_('select_option');
		$this->tr_common['tr_proof_study_hard_copy']   =$this->user_model->translate_('proof_study_hard_copy');
		$this->tr_common['tr_proof_study_soft_copy']   =$this->user_model->translate_('proof_study_soft_copy');
		
		
		
		$content=$this->get_student_deatils_for_popup();
		$content['topmenu']=$this->get_student_deatils_for_popup();
		$content['heading']=$this->tr_common['tr_proof_study'];
		
		$soft_product_id = $this->common_model->getProdectId('poe_soft','',1);
	//	$soft_amount  = $this->common_model->get_product_amount($soft_product_id,$this->currId);	
		
		
		
		$soft_price_details_array = $this->common_model->getProductFee($soft_product_id,$this->currId);
		
	
		  $data['soft_amount']          = $soft_price_details_array['amount'];		
		  $data['soft_currency_code']   = $soft_price_details_array['currency_code'];
		  $data['soft_curr_id']         = $soft_price_details_array['currency_id'];
		  $data['soft_currency_symbol'] = $soft_price_details_array['currency_symbol'];	
		  
		  $course_name = $this->common_model->get_course_name($course_id);	
		  $content['course_id']  = $course_id;
		  $content['course_name']  = $course_name;
		$hard_product_id = $this->common_model->getProdectId('poe_hard','',1);
		$hard_price_details_array = $this->common_model->getProductFee($hard_product_id,$this->currId);	
	
		  $data['hard_amount']          = $hard_price_details_array['amount'];		
		  $data['hard_currency_code']   = $hard_price_details_array['currency_code'];
		  $data['hard_curr_id']         = $hard_price_details_array['currency_id'];
		  $data['hard_currency_symbol'] = $hard_price_details_array['currency_symbol'];		
		
		
		//$hard_amount  = $this->common_model->get_product_amount($hard_product_id,$this->currId);
		
		$content['soft_product_id'] = $soft_product_id;
		//$content['soft_amount'] = $soft_amount;
		$content['hard_product_id'] = $hard_product_id;
		//$content['hard_amount'] = $hard_amount;
		$content['course_id']  = $course_id;
		$content['user_id']    = $user_id;
		$content['currency_code'] = $this->currencyCode;
		$content['curr_id'] = $currency_id;
		
	   $data['translate'] = $this->tr_common;
	   $data['view'] = 'proof_study';
	   $data['content'] = $content;
	   $this->load->view('user/help_center_template',$data);
		 
		  
	  }
	  
	   function hard_copy_proof_study_confirm($course_id)
	  {
		  
		  
		
		 $user_id = $this->session->userdata['student_logged_in']['id'];	
		 $this->tr_common['tr_proof_study_can_use']   =$this->user_model->translate_('proof_study_can_use');
		 
		 if(isset($_POST['selected_product_id']))
		{
			
			$product_id =  $this->input->post('selected_product_id');
			$currency_id =  $this->input->post('currency_id');
			$amount  = $this->input->post('amount');
		}
		else
		redirect('sales/apply_proof_study/'.$course_id,'refresh');		 
		 
		$product_details = $this->common_model->get_product_details($product_id);				
		 
		if($product_details[0]->type== 'poe_soft')
		{ 	 
			$data=$this->get_student_deatils_for_popup();	
			$data['topmenu']=$this->get_student_deatils_for_popup();	
			$data['heading']='Apply Proof of Study';	
			//$product_id = 23;
			$content['product_id']  = $product_id;
			$content['product_type']  = $product_details[0]->type;
			$content['currency_id']  = $currency_id;
			$content['currency_code']  = $this->common_model->get_currency_code_from_id($currency_id);
			$content['amount']  = $amount;			
			$content['user_id'] 	 = $user_id;				 
			$content['course_id'] = $course_id;	 
			$content['product_name'] = 'Apply Proof of Study';			 
			$data['translate'] = $this->tr_common;
			$data['view'] = 'proof_letters_soft';   
			$data['content'] = $content;
			$this->load->view('user/template_inner',$data);			
		}
		else
		{		
		    $this->load->helper(array('dompdf', 'file'));		
		    
			/*if($this->session->userdata['ip_address'] == '117.247.185.9')
		      {*/
			$html = $this->pdf_html_model->create_proof_enrolement_html_new($course_id);					
						/*}
						
						else{
			$html = $this->pdf_html_model->create_proof_enrolement_html($course_id);					
						}*/
			$content['product_id']  = $product_id;
			$content['currency_id']  = $currency_id;
			$content['amount']  = $amount;		
			$content['letter_html'] = $html;				 
			$content['course_id'] = $course_id;	  
		    $data['view'] = 'hard_copy_proof_study_confirm';   
			 $data['translate'] = $this->tr_common;
		    $data['content'] = $content;
		    $this->load->view('user/pop_up_template',$data);  
		}
		
	  }
	  function proof_enrollemnt_shipping($product_id,$course_id)
	  {			  
			  
			if(!$this->session->userdata('student_logged_in')){
			  redirect('home');
			}
			$user_id=$this->session->userdata['student_logged_in']['id']; 
			
			$content = array();
			$data =array();
			$currency_id = $this->currId;
			$currency_code = $this->currencyCode;	
			
			
			 
			
			
				$postage_amount = $this->certificate_model->get_postage_amount($product_id,$currency_id);
				
				foreach($postage_amount as $value)
				{
					$content['amount'] = $value->amount;
				}
				 $stud_details=$this->user_model->get_stud_details($user_id);
				 
				 if(isset($_POST['confirm_address']))
				{			
					$apartment  = $this->input->post('apartment');
					$address1  = $this->input->post('address1');
					//$address2  = $this->input->post('address2');	
					$country  = $this->input->post('country');
					$zip_code  = $this->input->post('zip_code');
					$city  = $this->input->post('city');
					
					
					$this->form_validation->set_rules('apartment', 'Apartment/ House number', 'trim|required');
					$this->form_validation->set_rules('address1', 'Address 1', 'required');			
					//$this->form_validation->set_rules('address2', 'Address 2', 'required');
					$this->form_validation->set_rules('country', 'Country', 'required');
					$this->form_validation->set_rules('zip_code', 'Zip code', 'required');
					$this->form_validation->set_rules('city', 'City', 'required');	
					
					if($this->form_validation->run())
					{			 
						redirect('sales/confirm_shipping_address/'.$product_id.'/'.$course_id, 'refresh');
					}
					
										
				}		
				 
				
				 
				 foreach($stud_details as $val2)
				 {
					 $country_name = $this->user_model->get_country_name($val2->country_id);
					 $content['house_number'] = $val2->house_number;
					 $content['address'] = $val2->address;
					 $content['city'] = $val2->city;
					 $content['zip_code'] = $val2->zipcode;
					 $content['country_name'] = $country_name;
				 }
				
				$data['course_id']  		 = $course_id;
				
				$content['user_id'] 	 = $user_id;  
				$content['product_id']  = $product_id;				
				$data['currency_code']  = $currency_code;
				$data['view'] 		   = 'proof_enrol_hard_user_details';
				//$data['view'] 		   = 'hard_copy_payment';   
				$data['content']  		= $content;
				$this->load->view('user/course_template',$data);	
					
			  
			  
		  
	   }
	   function proof_enrol_option($course_id)
	   {
		   if(!$this->session->userdata('student_logged_in')){
			  redirect('home');
			}
			$user_id=$this->session->userdata['student_logged_in']['id']; 
			
			
			$currency_id = $this->currId;
			$currency_code = $this->currencyCode;
			
			$data=$this->get_student_deatils_for_popup();
				
			 if(isset($_POST['book_now']))
			{	
				$selected_product_id  = $this->input->post('selected_product_id');
				$content['amount'] = $this->input->post('amount');
				
				if($selected_product_id== 45)
				{
					
					$stud_details=$this->user_model->get_stud_details($user_id);	
		 
				  foreach($stud_details as $val2)
				  {
					 $country_name = $this->user_model->get_country_name($val2->country_id);
					 $content['house_number'] = $val2->house_number;
					 $content['address'] = $val2->address;
					 $content['city'] = $val2->city;
					 $content['zip_code'] = $val2->zipcode;
					 $content['country_set'] = $val2->country_id;
				  }
					
				}
			
				$data['translate'] = $this->tr_common;
			    $data['course_id']  	  = $course_id;				
				$content['user_id'] 	 = $user_id;  
				$content['product_id']  = $selected_product_id;				
				$data['currency_code']  = $currency_code;
				$data['currency_id']  = $currency_id;				
				$data['view'] 		   = 'proof_enrol_confirm';   
				$data['content']  		= $content;
				$this->load->view('user/course_template',$data);	
		   }
		  /* elseif($selected_product_id=45) // soft and hard copy
		   {
			    $data['course_id']  	  = $course_id;				
				$content['user_id'] 	 = $user_id;  
				$content['product_id']  = $product_id;				
				$data['currency_code']  = $currency_code;
				$data['view'] 		   = 'proof_enrol_confirm_hard';
				//$data['view'] 		   = 'hard_copy_payment';   
				$data['content']  		= $content;
				$this->load->view('user/course_template',$data);	
		   }*/
	   }
	   
	   
	  
	  
	  function after_proof_enrolment_soft_pay()
	  {
		  $payment_id = $this->uri->segment(4);
		  $course_id  = $this->uri->segment(5);
		  $product_id = $this->uri->segment(6);
		  $user_id=$this->session->userdata['student_logged_in']['id'];
		  
		  $today = date("Y-m-d");
		  $insert_data=array("user_id"=>$user_id,"course_id"=>$course_id,"type"=>'poe_soft',"date_applied"=>$today,"product_id"=>$product_id,"payment_id"=>$payment_id);		 
		  $this->user_model->insertQuerys("user_subscriptions",$insert_data);
		   redirect('coursemanager/certificate');
	  }
	 
	  function after_proof_enrolment_hard_pay()
	  { 
	      $payment_id = $this->uri->segment(4);
		  $course_id  = $this->uri->segment(5);
		  $product_id = $this->uri->segment(6);
		  $user_id=$this->session->userdata['student_logged_in']['id'];
		  
		  $today = date("Y-m-d");
		  $insert_data=array("user_id"=>$user_id,"course_id"=>$course_id,"type"=>'poe_hard',"date_applied"=>$today,"product_id"=>$product_id,"payment_id"=>$payment_id);		
		  
		  $this->user_model->insertQuerys("user_subscriptions",$insert_data);
		  
		 
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
		 
		  //$course_name =$course_name['course_name'];
		 
		  $course_name = strtolower($course_name);
		  $course_name = ucfirst($course_name);	 	 
		   //echo "<pre>"; print_r($course_name); exit;
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
		
		   $exp_day = date('d M Y',strtotime($expiry_date_date));
		  $course_topics = '';
		  
		 if($course_id == 1)
			  {
				  $course_name='Event and Hospitality Management';
				   $course_topics = 'The course content outlines the knowledge, skills and dos & don’ts necessary to become an expert in personal styling. It includes the importance of good self image, personal care, optimising individual morphology and how to use fashion for best effects.';
			  }
			  elseif($course_id == 2)
			  {
				  $course_name='Wedding Planner';
				  $course_topics = 'The course content outlines the knowledge, skills and dos & don’ts necessary to become a professional personal shopper/stylist/image consultant. It covers a wide range of topics including career choices, planning, history of fashion & how to use it wisely and career guidance.';
			  }
			  
			  
			 // setlocale(LC_TIME, 'en_EN');
			  
			//  $cssLink = "http://trendimi.net/public/letters/css/proof_letters.css";
			  
			  
			  if($user_lang_id == 4)
			  {
				  
			  
			   $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Eventtrix-Proof-of-Enrolement</title>
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

body, html{margin:0; padding:0; font-family: "Open Sans"; color:#666; line-height:1.4em; background:#fff}
.outer{margin:0 auto; background:url(/public/user/certificate/images/Eventtrix-Proof-of-Enrolement.jpg) center center; width:820px; height:959px}
.innner{padding:12.5em 3em 2em 4em;}
p{padding:0.4em 0.5em; font-size:11pt; margin:0}
ul{margin:0; padding:0.8em 0.7em 0.3em 1.5em; font-size:11pt;}
ul li{padding:0.2em}
</style>


</head>

<body>
<div class="outer">
<div class="innner">
<p>To whom it may concern,</p>
<p>We confirm that '.$user_name.' is a student of EventTrix – Event Management Online Training
and has enrolled to our '.$course_name.' course.</p>
<p>The course consists of '.$course_hours.' online study hours and includes study content, exercises and exams.
'.$name[0].'’s expected date of course completion is '.$exp_day.'. This date may be extended if
extra time is needed to complete study.</p>
<p>Topics covered by the course include:</p>
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

<p>We wish '.$name[0].' every success in completing the Event Management course and in her future career.</p>
<p>Kind Regards,</p>
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
<title>Eventtrix-Proof-of-Enrolement</title>
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

body, html{margin:0; padding:0; font-family: "Open Sans"; color:#666; line-height:1.4em; background:#fff}
.outer{margin:0 auto; background:url(/public/user/certificate/images/Eventtrix-Proof-of-Enrolement.jpg) center center; width:820px; height:959px}
.innner{padding:12.5em 3em 2em 4em;}
p{padding:0.4em 0.5em; font-size:11pt; margin:0}
ul{margin:0; padding:0.8em 0.7em 0.3em 1.5em; font-size:11pt;}
ul li{padding:0.2em}
</style>


</head>

<body>
<div class="outer">
<div class="innner">
<p>To whom it may concern,</p>
<p>We conrm that '.$user_name.' is a student of EventTrix – Event Management Online Training
and has enrolled to our '.$course_name.'.</p>
<p>The course consists '.$course_hours.' online study hours and included study content, exercises and exams.
'.$user_name.'/’s expected date of course completion is '.$expiry_date.' '.$expiry_month.' '.$expiry_year.'. This date may be extended if
extra time is needed to complete study.</p>
<p>Principles of Event Management & Roles of Event Manager</p>
<ul>
<li>Types of Events</li>
<li>Working with Clients incl. understanding client needs, preparing event proposals,
signing contracts</li>
<li>Steps for planning an Event incl. budgets, venues, food and beverages,
transportation, speakers</li>
<li>General Etiquette and Protocol incl. invitations, dress codes, table settings and
seating arrangements, greeting etiquette</li>
<li>Day of the Event and Post Event Evaluation</li>
</ul>
<p>We wish '.$user_name.' ever success in completing the Event Management course and in her future career.</p>
<p>Kind Regards,</p>
</div>
</div>
</body>
</html>
	';
		
		  
    }
		  
		//echo $html;
		//exit;  
		
	
		  
		  
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
			if($this->session->userdata('paid_from_sandbox_account'))
						   {
							$to_mail = 'sarathkochooli@gmail.com';
						   }
					   else
						   {
							$to_mail = 'certificates@eventtrix.com';
						   }
						
			//$tomail = 'deeputg1992@gmail.com';
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
						   	
					  $this->email->from('info@eventtrix.com', 'Team EventTrix');
					  $this->email->to($to_mail); 
					  $this->email->attach($this->path);
					  
					  $this->email->subject($emailSubject);
					  $this->email->message($mailContent);	
					  
					  $this->email->send();
					 					 
					 
					 
		}
	
		  
		   $this->email->clear(TRUE);
		  
		  //echo "<pre>"; print_r($product_id); exit; 
		  
		  /*---end mail send-----*/
		  
		   redirect('sales/success_letter_hardcopy/'.$product_id, 'refresh');
		 // redirect('coursemanager/success_hardcopy', 'refresh');
	}
	    function after_proof_enrolment_hard_pay_new()
	  { 
	      $payment_id = $this->uri->segment(4);
		  $course_id  = $this->uri->segment(5);
		  $product_id = $this->uri->segment(6);
		  $user_id=$this->session->userdata['student_logged_in']['id'];
		  
		  $today = date("Y-m-d");
		  $insert_data=array("user_id"=>$user_id,"course_id"=>$course_id,"type"=>'poe_hard',"date_applied"=>$today,"product_id"=>$product_id,"payment_id"=>$payment_id);		 
		  $this->user_model->insertQuerys("user_subscriptions",$insert_data);
		  
		  
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
		   foreach($stud_details as $val2)
		  {
			 $certificate_user_name = $val2->first_name.' '.$val2->last_name;
			 $user_first_name 	   = ucfirst((trim($val2->first_name)));		
			 $user_country_name	 = $this->user_model->get_country_name($val2->country_id);				
			 $user_mail			 = $val2->email;			 
			 $user_house_number 	 = ucfirst(strtolower(trim($val2->house_number)));
			 $user_address 	  	  = ucfirst(strtolower(trim($val2->address)));
			 $user_street 	   	   = ucfirst(strtolower(trim($val2->street)));
			 $user_city 		 	 = ucfirst(strtolower(trim($val2->city)));
			 $user_zip_code     	 = $val2->zipcode;	
			 
		  }	
		  
		  $certificate_user_name = ucwords($certificate_user_name);
		  
		if(strpos($certificate_user_name, '\''))
		{
				$certificate_user_name = preg_replace_callback("/'[a-z]/", function ($matches) {
				return strtoupper($matches[0]);
				}, $certificate_user_name);	
		}
		if(strpos($certificate_user_name, '-'))
		{
			$certificate_user_name = preg_replace_callback("/-[a-z]/", function ($matches) {
			return strtoupper($matches[0]);
			}, $certificate_user_name);	
		}
			 
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
		  $course_name =$course_name['course_name'];
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
		
		   $exp_day = date('d M Y',strtotime($expiry_date_date));
		  $course_topics = '';
		  
		 if($course_id == 1)
			  {
				  $course_name='Event Planner';
				  $course_topics = 'Topics covered by the course include: 
				  <ul>
					<li>Principles of event management & roles of event manager</li>
					<li>Types of events</li>
					<li>Working with clients incl. understanding client needs, preparing event proposals,signing contracts</li>
					<li>Steps for planning an event incl. budgets, venues, food and beverages,transportation, speakers</li>
					<li>General etiquette and protocol incl. invitations, dress codes, table settings andseating arrangements, greeting etiquette</li>
					<li>Day of the event and post event evaluation</li>
				</ul>';
			  }
			  if($course_id == 2)
			  {
				  $course_name='Wedding Planner';
				  $course_topics = 'Topics covered by the course include: 
				    <ul>
						<li>The profession of a wedding planner and types of ceremonies</li>
						<li>Working with client</li>
						<li>Engagement & bachelor parties</li>
						<li>Getting the look and feel right: venues, music, roles, invitations, guests lists, speeches</li>
						<li>Dressing the wedding party</li>
						<li>Perfecting essential details: décor, the banquet, gifts, menu, floral arrangements, honeymoon</li>
						<li>Summing up: final budget, assessment</li>
						<li>Starting and marketing your wedding planner business</li>
						<li>Wedding planning resources</li>
					</ul>';
			  }			 
			  elseif($course_id == 3)
			  {
				  $course_name='Starting Your Business';
				  $course_topics = 'Topics covered by the course include: 
				 <ul>
					<li>Market research and competitors analysis, funding and available help</li>
					<li>Introduction to marketing</li>
					<li>Business structures, legislation and regulations, registering your business</li>
					<li>Budget and cash flows, accounting and finance</li>
					<li>Insurance, premises, suppliers, staff</li>
					<li>Home based businesses</li>
					<li>Business plan</li>
					<li>Launching your business</li>
				</ul>';
			  }
			  elseif($course_id == 4)
			  {
				  $course_name='Marketing Your Business';
				  $course_topics = 'Topics covered by the course include: 
				   <ul>
					<li>Introduction to marketing</li>
					<li>Marketing plan</li>
					<li>Low cost marketing techniques</li>
					<li>Developing your brand</li>
					<li>Setting up and managing website</li>
					<li>Social media and online marketing</li>
					<li>Public relations and advertising</li>
					<li>Sales campaigns and leads generation</li>
				  </ul>';
			  }
			  elseif($course_id == 5)
		  {
			  $course_name='Music & Film Events Manager';
			  $course_topics = 'Topics covered by the course include: 
			  <ul>
<li>Planning the event incl. team, budget, location, date</li>
<li>Marketing / Advertising / Publicity incl. website, tickets, sponsors, partners and promotional materials</li>
<li>Ticket sales and pricing</li>
<li>Availing of help incl. mentoring, networking, state agencies & grants</li>
<li>Budgeting, cash flow, accounting, banking, insurance, contracts</li>
<li>Market research, brand-building, social media</li>
</ul>';

		  }
		   elseif($course_id == 7)
		  {
			  $course_topics = 'Topics covered by the course include: 
			  <ul>
<li>Essential bartending tools and supplies</li>
<li>Hygiene, safety & presentation</li>
<li>Mixology & pouring</li>
<li>Responsibility and flair</li>
<li>Origin and cultivation of coffee </li>
<li>Preparing to brew</li>
<li>Grinding and espresso</li>
<li>Steaming, frothing & texture</li>
</ul>';
		  }
			  
			  
			  
			 // setlocale(LC_TIME, 'en_EN');
			  
			//  $cssLink = "http://trendimi.net/public/letters/css/proof_letters.css";
			  
			  
			  if($user_lang_id == 4)
			  {
				  
			  $cssLink = "public/letters/css/letters_new.css";		  
		  $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Proof of enrollement</title>
<link rel="stylesheet" href="'.$cssLink.'" type="text/css" />
</head>

<body>
<div class="letter_outer">
<div class="letter_header">
<h2 class="letter_letterNme"><span>Proof of</span> Enrollement</h2>
</div>
<div class="clear"></div>
<div class="letter_content">
<div class="letter_address">
<p>'.$certificate_user_name.' </p>
					<p>'.$user_house_number.' '.$user_street.' </p>
					<p>'.$user_address.' </p>
					<p>'.$user_zip_code.' '.$user_city.' </p>
					<p>'.$user_country_name.'</p>
				</div>
				<div class="clear"></div>
				<p>To whom it may concern,</p>
<p>We confirm that '.$certificate_user_name.' is a student of EventTrix - Event Management Online Training
and has enrolled in our '.$course_name.' course.</p>
<p>The course consists of '.$course_hours.' online study hours and includes study content, exercises and exams.
'.$user_first_name.'\'s expected date of course completion is '.$expiry_date.' '.$expiry_month.' '.$expiry_year.'. This date may be extended if
extra time is needed to complete study.</p>
	<p>
	'.$course_topics.'
	</p>
	<p>We wish '.$user_first_name.' every success in completing the '.$course_name.' course and in '.$gender_pronoun_2.' future career.</p>
<div class="clear"></div>
<h4 class="letter_kind_rgrds">
Kind regards,</h4>
<div class="clear"></div>
<ol>
<li>Darren Taylor</li>
<li>CEO</li>
<li>www.eventtrix.com</li>
<li>info@eventtrix.com</li>
</ol>

</div>
</div>
</body>
</html>';
    }
    else if($user_lang_id == 3)
    {
		  $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Eventtrix-Proof-of-Enrolement</title>
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

body, html{margin:0; padding:0; font-family: "Open Sans"; color:#666; line-height:1.4em; background:#fff}
.outer{margin:0 auto; background:url(/public/user/certificate/images/Eventtrix-Proof-of-Enrolement.jpg) center center; width:820px; height:959px}
.innner{padding:12.5em 3em 2em 4em;}
p{padding:0.4em 0.5em; font-size:11pt; margin:0}
ul{margin:0; padding:0.8em 0.7em 0.3em 1.5em; font-size:11pt;}
ul li{padding:0.2em}
</style>


</head>

<body>
<div class="outer">
<div class="innner">
<p>To whom it may concern,</p>
<p>We conrm that '.$user_name.' is a student of EventTrix – Event Management Online Training
and has enrolled to our '.$course_name.'.</p>
<p>The course consists '.$course_hours.' online study hours and included study content, exercises and exams.
'.$user_name.'/’s expected date of course completion is '.$expiry_date.' '.$expiry_month.' '.$expiry_year.'. This date may be extended if
extra time is needed to complete study.</p>
<p>Principles of Event Management & Roles of Event Manager</p>
<ul>
<li>Types of Events</li>
<li>Working with Clients incl. understanding client needs, preparing event proposals,
signing contracts</li>
<li>Steps for planning an Event incl. budgets, venues, food and beverages,
transportation, speakers</li>
<li>General Etiquette and Protocol incl. invitations, dress codes, table settings and
seating arrangements, greeting etiquette</li>
<li>Day of the Event and Post Event Evaluation</li>
</ul>
<p>We wish '.$user_name.' ever success in completing the Event Management course and in her future career.</p>
<p>Kind Regards,</p>
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
			if($this->session->userdata('paid_from_sandbox_account'))
						   {
							$to_mail = 'anoopramachandran04@gmail.com';
						   }
					   else
						   {
							$to_mail = 'certificates@eventtrix.com';
						   }
						
			//$tomail = 'deeputg1992@gmail.com';
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
						   	
					  $this->email->from('info@eventtrix.com', 'Team EventTrix');
					  $this->email->to($to_mail); 
					  $this->email->attach($this->path);
					  
					  $this->email->subject($emailSubject);
					  $this->email->message($mailContent);	
					  
					  $this->email->send();
					 					 
					 
					 
		}
	
		  
		   $this->email->clear(TRUE);
		  
		  
		  
		  /*---end mail send-----*/
		  
		   redirect('sales/success_letter_hardcopy/'.$product_id, 'refresh');
		 // redirect('coursemanager/success_hardcopy', 'refresh');
	}
	  function proof_enrollement_download_test($course_id)
	  {
		  $user_id = $this->session->userdata['student_logged_in']['id'];	
		  $this->load->helper(array('dompdf', 'file'));		 
		  $html = $this->pdf_html_model->create_proof_enrolement_pdf($course_id);		
		  $data = pdf_create($html, 'course_enrolment_'.$user_id.'_'.$course_id);   
    	  write_file('name', $data);		 
	  }
	  
	  function proof_enrollement_download($course_id)
	  {
		  $user_id = $this->session->userdata['student_logged_in']['id'];	
		  $this->load->helper(array('dompdf', 'file'));		 
		  $html = $this->pdf_html_model->create_proof_enrolement_pdf_new($course_id);		
		  $data = pdf_create($html, 'course_enrolment_'.$user_id.'_'.$course_id);   
    	  write_file('name', $data);		 
	  }
	  
	  
	  function apply_proof_completion($course_id)
	  {
		
		
		 if(!$this->session->userdata('student_logged_in')){
		  redirect('home');
		}
		
		$user_id=$this->session->userdata['student_logged_in']['id']; 
		 
		$content = array();
		$currency_id = $this->currId;
		$currency_code = $this->currencyCode;
		
		$this->tr_common['tr_proof_completion_can_supply_txt'] = $this->user_model->translate_('proof_completion_can_supply_txt');
		$this->tr_common['tr_proof_completion']   =$this->user_model->translate_('Proof_of_Completion');
		$this->tr_common['tr_proof_completion_can_use']   =$this->user_model->translate_('proof_completion_can_use');
		$this->tr_common['tr_book_now']   =$this->user_model->translate_('book_now');
		
		$this->tr_common['tr_select_option']   =$this->user_model->translate_('select_option');
		$this->tr_common['tr_proof_completion_hard_copy']   =$this->user_model->translate_('proof_completion_hard_copy');
		$this->tr_common['tr_proof_completion_soft_copy']   =$this->user_model->translate_('proof_completion_soft_copy');
		
		
		
		
		
		$content=$this->get_student_deatils_for_popup();
		$content['topmenu']=$this->get_student_deatils_for_popup();
		$content['heading']=$this->tr_common['tr_proof_completion'] ;
		
		$soft_product_id = $this->common_model->getProdectId('proof_completion','',1);
	//	$soft_amount  = $this->common_model->get_product_amount($soft_product_id,$this->currId);	
		
		
		
		$soft_price_details_array = $this->common_model->getProductFee($soft_product_id,$this->currId);
		
	
		  $data['soft_amount']          = $soft_price_details_array['amount'];		
		  $data['soft_currency_code']   = $soft_price_details_array['currency_code'];
		  $data['soft_curr_id']         = $soft_price_details_array['currency_id'];
		  $data['soft_currency_symbol'] = $soft_price_details_array['currency_symbol'];		
		  
		  $course_name = $this->common_model->get_course_name($course_id);	
		  $content['course_id']  = $course_id;
		  $content['course_name']  = $course_name;
		
		$hard_product_id = $this->common_model->getProdectId('proof_completion_hard','',1);
		
		$hard_price_details_array = $this->common_model->getProductFee($hard_product_id,$this->currId);	
		  $data['hard_amount']          = $hard_price_details_array['amount'];		
		  $data['hard_currency_code']   = $hard_price_details_array['currency_code'];
		  $data['hard_curr_id']         = $hard_price_details_array['currency_id'];
		  $data['hard_currency_symbol'] = $hard_price_details_array['currency_symbol'];		
		
		
		//$hard_amount  = $this->common_model->get_product_amount($hard_product_id,$this->currId);
		
		$content['soft_product_id'] = $soft_product_id;
		//$content['soft_amount'] = $soft_amount;
		$content['hard_product_id'] = $hard_product_id;
		//$content['hard_amount'] = $hard_amount;
		$content['course_id']  = $course_id;
		$content['user_id']    = $user_id;
		$content['currency_code'] = $this->currencyCode;
		$content['curr_id'] = $currency_id;
		
		  $data['translate'] = $this->tr_common;
		  $data['view'] = 'proof_completion';
		  $data['content'] = $content;
		   $this->load->view('user/help_center_template',$data);
		 
		  
	  }
	  
	  	  
	   function hard_copy_proof_completion_confirm($course_id)
  	   {
		
		 $user_id = $this->session->userdata['student_logged_in']['id'];	
		 
		 $this->tr_common['tr_not_happy']   				 = $this->user_model->translate_('No_I_am_not_happy');
		 $this->tr_common['tr_happy_with_proof_letter']   = $this->user_model->translate_('happy_with_proof_letter');
		 $this->tr_common['tr_see_proof_letter_below']   = $this->user_model->translate_('see_proof_letter_below');
		 $this->tr_common['tr_proof_completion_can_supply_txt'] = $this->user_model->translate_('proof_completion_can_supply_txt');
		 
		 
		 if(isset($_POST['selected_product_id']))
		{
			
			$product_id =  $this->input->post('selected_product_id');
			$currency_id =  $this->input->post('currency_id');
			$amount  = $this->input->post('amount');
		}
		else
		redirect('sales/apply_proof_completion/'.$course_id,'refresh');
		 
		 
		 $product_details = $this->common_model->get_product_details($product_id);	
		 		 
		if($product_details[0]->type== 'proof_completion')
		{
			$data=$this->get_student_deatils_for_popup();			
			
			$content['product_id']  = $product_id;
			$content['product_type']  = $product_details[0]->type;
			$content['currency_id']  = $currency_id;
			
			$content['currency_code']  = $this->common_model->get_currency_code_from_id($currency_id);
			$content['amount']  = $amount;
			
			$content['user_id'] 	 = $user_id;		
			$content['product_name'] = 'Apply Proof of Completion';				 
			$content['course_id'] = $course_id;	
			$content['topmenu']=$this->get_student_deatils_for_popup();		 
			$data['translate'] = $this->tr_common;
			$data['view'] = 'proof_letters_soft';   
			$data['content'] = $content;
			$this->load->view('user/template_inner',$data);
		}
		else
		{
		
		  $this->load->helper(array('dompdf', 'file'));	
		 /* if($this->session->userdata['ip_address'] == '117.247.185.9')
		      {
           $html = $this->pdf_html_model->create_proof_completion_html_new($course_id);  
			  }
			  else{*/
		  $html = $this->pdf_html_model->create_proof_completion_html_new($course_id); 
			 /* }*/
		  $content['product_id']  = $product_id;
		  $content['currency_id']  = $currency_id;
		  $content['amount']  = $amount;	
		  $content['letter_html'] = $html;			 
		  $data['translate'] = $this->tr_common;		
		  $content['course_id'] = $course_id;	 	  
	 	  $data['view'] = 'hard_copy_proof_completion_confirm';   
     	  $data['content'] = $content;
     	//  $this->load->view('user/template_inner',$data);
     	   $this->load->view('user/pop_up_template',$data);  
	  
		}
  }
  
      function proof_letters_address_confirm($course_id)
	  {
		  $this->load->library('form_validation');
		  
		   if(!$this->session->userdata('student_logged_in')){
			  redirect('home');
			}
			$user_id=$this->session->userdata['student_logged_in']['id']; 
			
		 $this->tr_common['tr_Fee_for_proof_completion']  = $this->user_model->translate_('Fee_for_proof_completion');
		 $this->tr_common['tr_Fee_for_proof_enrolment']   = $this->user_model->translate_('Fee_for_proof_enrolment'); 
		 
			
		$this->tr_common['tr_Apartment_House_number'] = $this->user_model->translate_('Apartment_House_number');
		$this->tr_common['tr_address_1'] = $this->user_model->translate_('address_1');
		$this->tr_common['tr_street'] = $this->user_model->translate_('road_street');
		
		$this->tr_common['tr_city'] = $this->user_model->translate_('city');
		$this->tr_common['tr_zip_code'] = $this->user_model->translate_('zip_code');
		$this->tr_common['tr_Country'] = $this->user_model->translate_('Country');
		$this->tr_common['tr_make_payment'] = $this->user_model->translate_('make_payment');
		$this->tr_common['tr_confirm_address_below'] = $this->user_model->translate_('confirm_address_below');
		$this->tr_common['tr_additional_adress_if'] = $this->user_model->translate_('additional_adress_if');
		$this->tr_common['tr_donot_duplicate_street_name'] = $this->user_model->translate_('donot_duplicate_street_name');
		$this->tr_common['tr_donot_close_browser'] = $this->user_model->translate_('donot_close_browser');
		$this->tr_common['tr_required'] = $this->user_model->translate_('required');
		
		$this->tr_common['tr_yes_confirm_addr'] = $this->user_model->translate_('yes_confirm_addr');
		$this->tr_common['tr_addr_not_confirm'] = $this->user_model->translate_('addr_not_confirm');
			
			$content['states']=$this->user_model->get_states();
			$currency_id = $this->currId;
			$currency_code = $this->currencyCode;
			
			$data=$this->get_student_deatils_for_popup();
			$data['topmenu']=$this->get_student_deatils_for_popup();
			$data['heading']="Shipping details";
				
			 if(isset($_POST['product_id']))
			{	
				$selected_product_id  = $this->input->post('product_id');
				$content['amount'] = $this->input->post('amount');
				
				$product_details = $this->common_model->get_product_details($selected_product_id);			
				 				
				if($product_details[0]->type== 'proof_completion_hard' || $product_details[0]->type== 'poe_hard')
				{
					
					$stud_details=$this->user_model->get_stud_details($user_id);	
		 
				  foreach($stud_details as $val2)
				  {
					 $country_name = $this->user_model->get_country_name($val2->country_id);
					 $content['house_number'] = trim($val2->house_number);
					 $content['address'] = $val2->address;
					 $content['street'] = $val2->street;
					 $content['city'] = $val2->city;
					 $content['zip_code'] = $val2->zipcode;
					 $content['country_set'] = $val2->country_id;
					  $content['state_set']=$val2->us_states;   
				  }
					
				}
				
				
			
				$data['translate'] = $this->tr_common;
			    $data['course_id']  	  = $course_id;				
				$content['user_id'] 	 = $user_id;  
				$content['product_id']  = $selected_product_id;				
				$data['currency_code']  = $currency_code;
				$data['currency_id']  = $currency_id;				
				$data['view'] 		   = 'proof_completion_confirm';   
				$data['content']  		= $content;
				$this->load->view('user/template_inner',$data);	
		   }
		  /* elseif($selected_product_id=45) // soft and hard copy
		   {
			    $data['course_id']  	  = $course_id;				
				$content['user_id'] 	 = $user_id;  
				$content['product_id']  = $product_id;				
				$data['currency_code']  = $currency_code;
				$data['view'] 		   = 'proof_enrol_confirm_hard';
				//$data['view'] 		   = 'hard_copy_payment';   
				$data['content']  		= $content;
				$this->load->view('user/course_template',$data);	
		   }*/
	   
	  }
	  
	    function after_proof_completion_hard_pay()
	  {
		   $payment_id = $this->uri->segment(4);
		  $course_id  = $this->uri->segment(5);
		  $product_id = $this->uri->segment(6);
		  $user_id=$this->session->userdata['student_logged_in']['id'];
		  
		  $today = date("Y-m-d");
		  $insert_data=array("user_id"=>$user_id,"course_id"=>$course_id,"type"=>'proof_completion_hard',"date_applied"=>$today,"product_id"=>$product_id,"payment_id"=>$payment_id);		 
		  $this->user_model->insertQuerys("user_subscriptions",$insert_data);
		  
		  
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
			  
			  
			  
		  $this->load->helper(array('dompdf', 'file'));
		  $user_id = $this->session->userdata['student_logged_in']['id'];	
		 $user_name = $this->common_model->get_user_name($user_id);	
		  
		  $name = explode('&nbsp;',$user_name);
		
		  $course_name = $this->common_model->get_course_name($course_id); 
		  //$course_name=$course_name['course_name'];
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
		  
		  
		  		  $user_lang_id  = $this->common_model->get_user_lang_id($user_id);
										  /*------ send mail to admin */
										  
		  if($user_lang_id == 3)
		  {
			   setlocale(LC_TIME, 'es_ES');
		  }
		  elseif($user_lang_id == 4)
		  {
			  setlocale(LC_TIME, 'en_EN');
		  }
		  elseif($user_lang_id == 6)
		  {
			   setlocale(LC_TIME, 'fr_FR');
		  }

   
		  $certficate_details = $this->user_model->get_proof_of_completion_details_new($user_id,$course_id);
		  if(empty($certficate_details))
			{
				$certficate_details = $this->user_model->get_certficate_details($user_id,$course_id);
			}			
			
		  if(!empty($certficate_details))
		  {
			   $completed_date_date = $certficate_details['applied_on'];
		  }
		  else
		  {
			  $completed_date_date = date("Y-m-d");
		  }
			  
		//  $completed_date_date = $certficate_details['applied_on'];
		  
		  $course_completed_date = explode('-',$completed_date_date);
		  
		  $completed_year  = $course_completed_date[0];
		 // $completed_month = $course_completed_date[1];
		  $completed_date  = $course_completed_date[2];
		  
		   //$date_in_time_frmt = strtotime($completed_date_date);
		 // $completed_date =2;
		  //$month_name  = date('F', $date_in_time_frmt);
		  
		  $month_name = ucwords(strftime('%B',strtotime($completed_date_date)));
		  
		
		  $date_suffix = date("S",strtotime($completed_date_date));	   
		
		  $module_list = $this->user_model->check_user_registered($user_id,$course_id);
		  
		  foreach($module_list as $unit)
		  {
			  $modules = unserialize($unit->student_course_units);
		  }
					
		  $module_count = count($modules);
		 
		  
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
		 
		  $course_topics = '';
		  
		  
		   if($course_id == 1)
		  {
			   $course_topics = '<ul>
<li>Principles of Event Management & Roles of Event Manager</li>
<li>Types of Events</li>
<li>Working with Clients incl. understanding client needs, preparing event proposals,
signing contracts</li>
<li>Steps for planning an Event incl. budgets, venues, food and beverages,
transportation, speakers</li>
<li>General Etiquette and Protocol incl. invitations, dress codes, table settings and
seating arrangements, greeting etiquette</li>
<li>Day of the Event and Post Event Evaluation</li>
</ul>';
		  }
		  elseif($course_id == 2)
		  {
			  $course_topics = '<ul>
<li>The profession of a wedding planner and types of ceremonies</li>
<li>Working with client</li>
<li>Engagement & bachelor parties</li>
<li>Getting the look and feel right – venues, music, roles, invitations, guests lists, speeches</li>
<li>Dressing the wedding party</li>
<li>Perfecting essential details – décor, the banquet, gifts, menu, floral arrangements, honeymoon</li>
<li>Summing up – final budget, assessment</li>
<li>Starting and marketing your wedding planner business</li>
<li>Wedding planning resources</li>
</ul>';
		  }
		  elseif($course_id == 3)
		  {
			  $course_topics = '<ul>
<li>Market research and competitors analysis, funding and available help
<li>Introduction to marketing.
<li>Business structureslegislation and regulations, registering your business
<li>Budjet and cash flows, accounting and finance
<li>Insurance, premises, suppliers, staf
<li>Home based business
<li>Business plan
<li>Launching your business
</ul>';
		  }
		   elseif($course_id == 4)
		  {
			  $course_topics = '<ul>
<li>Introduction to marketing</li>
<li>Marketing plan</li>
<li>Low cost marketing techniques</li>
<li>Developing your brand</li>
<li>Setting up and managing website</li>
<li>Social media and online marketing</li>
<li>Public relations and advertising</li>
<li>Sales campaigns and leads generation</li>
</ul>';
		  }
		 
		 
		 $lang_id  = $this->common_model->get_user_lang_id($user_id); 
		 
		 
		 	if($lang_id==4)
		{
		  
		  $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Eventtrix-Proof-of-Enrolement</title>
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

body, html{margin:0; padding:0; font-family: "Open Sans"; color:#666; line-height:1.4em; background:#fff}
.outer{margin:0 auto; background:url(/public/user/certificate/images/Eventtrix-Proof-of-Enrolement.jpg) center center; width:820px; height:959px}
.innner{padding:12.5em 3em 2em 4em;}
p{padding:0.4em 0.5em; font-size:11pt; margin:0}
ul{margin:0; padding:0.8em 0.7em 0.3em 1.5em; font-size:11pt;}
ul li{padding:0.2em}
</style>


</head>

<body>
<div class="outer">
<div class="innner">
<p>To whom it may concern,</p>
<p>We conrm that, on '.$completed_date.' '.$month_name.' '.$completed_year.', '.$user_name.' successfully completed our '.$course_name.' online
learning course. '.$user_name.' graduated with a '.$grade.'  grade.</p>

<p>The course consists '.$course_hours.' online study hours and includes study content, exercises and exams.
Topics covered by the course include:</p>
'.$course_topics.'
<p>We congratulate '.$user_name.' on completing '.$course_name.'  course and wish '.$gender_pronoun.' every
success in '.$gender_pronoun.' future career.</p>
<p>Kind Regards,
</div>
</div>
</body>
</html>
';
		}
		
		//echo $html; exit;
		
			  
		$data = pdf_create($html, 'proof_completion_'.$user_id.'_'.$course_id,false);		
	
		//$data = pdf_create($html, '', false);	
		$this->path = "public/certificate/proof_completion/proof_completion_".$user_id."_".$course_id.".pdf";
		write_file($this->path, $data);
     
		$sendemail = true;
		
		$student_data = $this->user_model->get_student_details($user_id);
		
		$user_country_name = $this->user_model->get_country_name($student_data[0]->country_id);
		
		
		if($sendemail)
		{
			$this->load->library('email');
			//$tomail = 'info@trendimi.net';
			//$tomail = 'ajithupnp@gmail.com';
			//$tomail = 'sarathkochooli@gmail.com';
			//$tomail = 'certificates@eventtrix.com';
			
			               if($this->session->userdata('paid_from_sandbox_account'))
						   {
							$tomail = 'ajithupnp@gmail.com';
						   }
						   else
						   {
							$tomail = 'certificates@eventtrix.com';
						   }
						   
			//$tomail = 'deeputg1992@gmail.com';
			//echo "<br>To mail ".$tomail;
					
					   $emailSubject = "Proof of completion is attached  ".$student_data[0]->email;;
					  $mailContent = "<p>Please find the attacahment of proof of completion here with it.<p>";
					  $mailContent = "<p>User details of Proof of completion hard copy applied, <p>";
					  
					  $mailContent .= "<p>User name  : ".$student_data[0]->first_name." ".$student_data[0]->last_name."</p>";
					  $mailContent .= "<p>House Number :  ".$student_data[0]->house_number."</p>";
					  $mailContent .= "<p>Address :  ".$student_data[0]->address."</p>";					  
					  $mailContent .= "<p>City :  ".$student_data[0]->city."</p>";
					  $mailContent .= "<p>Zip code :  ".$student_data[0]->zipcode."</p>";
					  $mailContent .= "<p>Country : ".$user_country_name."</p>";
						   	
						//	echo "<br>Mail content ".$mailContent;
							
					  $this->email->from('info@eventtrix.com', 'Team EventTrix');
					  $this->email->to($tomail); 
					  $this->email->attach($this->path);
					  
					  $this->email->subject($emailSubject);
					  $this->email->message($mailContent);	
					  
					  $this->email->send();
					//  echo "Mail send ";
		}
	
 //redirect('coursemanager/success_hardcopy/'.$product_id, 'refresh');
  redirect('sales/success_letter_hardcopy/'.$product_id, 'refresh');
			  
	  }
	  
	   function after_proof_completion_hard_pay_new()
	  {
		   $payment_id = $this->uri->segment(4);
		  $course_id  = $this->uri->segment(5);
		  $product_id = $this->uri->segment(6);
		  $user_id=$this->session->userdata['student_logged_in']['id'];
		  
		  $today = date("Y-m-d");
		  $insert_data=array("user_id"=>$user_id,"course_id"=>$course_id,"type"=>'proof_completion_hard',"date_applied"=>$today,"product_id"=>$product_id,"payment_id"=>$payment_id);		 
		  $this->user_model->insertQuerys("user_subscriptions",$insert_data);
		  
		  
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
			  
			  
			  
		  $this->load->helper(array('dompdf', 'file'));
		  $user_id = $this->session->userdata['student_logged_in']['id'];	
		 $user_name = $this->common_model->get_user_name($user_id);	
		  
		  $name = explode('&nbsp;',$user_name);
		
		  $course_name = $this->common_model->get_course_name($course_id); 
		  $course_name=$course_name['course_name'];
		  $course_name =strtolower($course_name);
		   $course_name =ucfirst($course_name);
		  
		  $slNo=0;
		  
		  $course_hours  = $this->user_model->get_course_hours($course_id);
		  
		   $stud_details=$this->user_model->get_stud_details($user_id);
		    foreach($stud_details as $key => $value)
		  {
			 $certificate_user_name  = $value->first_name.' '.$value->last_name;	
			 $user_first_name 		= ucwords(strtolower(trim($value->first_name)));		
			 $user_country_name 	  = $this->user_model->get_country_name($value->country_id);
			 $user_house_number 	  = ucfirst(strtolower(trim($value->house_number)));
			 $user_address 	  	   = ucfirst(strtolower(trim($value->address)));
			 $user_street 	        = ucwords(strtolower(trim($value->street)));
			 $user_city 		      = ucwords(strtolower(trim($value->city)));
			 $user_zip_code          = $value->zipcode;		
		  }  
		  
		  	
		  $certificate_user_name = ucwords($certificate_user_name);		
		  
		  if(strpos($certificate_user_name, '\''))
		  {
				$certificate_user_name = preg_replace_callback("/'[a-z]/", function ($matches) {
				return strtoupper($matches[0]);
				}, $certificate_user_name);	
		  }
		  if(strpos($certificate_user_name, '-'))
		  {
			$certificate_user_name = preg_replace_callback("/-[a-z]/", function ($matches) {
			return strtoupper($matches[0]);
			}, $certificate_user_name);	
		  }	
		  
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
		  
		  
		  		  $user_lang_id  = $this->common_model->get_user_lang_id($user_id);
										  /*------ send mail to admin */
										  
		  if($user_lang_id == 3)
		  {
			   setlocale(LC_TIME, 'es_ES');
		  }
		  elseif($user_lang_id == 4)
		  {
			  setlocale(LC_TIME, 'en_EN');
		  }
		  elseif($user_lang_id == 6)
		  {
			   setlocale(LC_TIME, 'fr_FR');
		  }

   
		$completed_date_date = $this->user_model->get_completion_date_from_course_enrolments($user_id,$course_id);
		if(!$completed_date_date)
		{
		   $certficate_details = $this->user_model->get_proof_of_completion_details_new($user_id,$course_id);
			if(empty($certficate_details))
			{
				$certficate_details = $this->user_model->get_certficate_details($user_id,$course_id);
			}
			  
		  $completed_date_date = $certficate_details['applied_on'];
		}
		  
			  
		//  $completed_date_date = $certficate_details['applied_on'];
		  
		  $course_completed_date = explode('-',$completed_date_date);
		  
		  $completed_year  = $course_completed_date[0];
		 // $completed_month = $course_completed_date[1];
		  $completed_date  = $course_completed_date[2];
		  
		 $date_in_time_frmt = strtotime($completed_date_date);		
		  
		  $month_name  = date("F",$date_in_time_frmt);
		  $month_name  = utf8_encode(ucwords(strftime('%B',strtotime($completed_date_date))));
		
		  $date_suffix = date("S",strtotime($completed_date_date));	   
		
		  $module_list = $this->user_model->check_user_registered($user_id,$course_id);
		  
		  foreach($module_list as $unit)
		  {
			  $modules = unserialize($unit->student_course_units);
		  }
					
		  $module_count = count($modules);
		 
		  
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
		 
		  $course_topics = '';
		  
		  
		  if($course_id == 1)
		  {
			   $course_topics = '<ul>
<li>Principles of Event Management & Roles of Event Manager</li>
<li>Working with Clients incl. understanding client needs, preparing event proposals,
signing contracts</li>
<li>Steps for planning an Event incl. budgets, venues, food and beverages,
transportation, speakers</li>
<li>General Etiquette and Protocol incl. invitations, dress codes, table settings and
seating arrangements, greeting etiquette</li>
<li>Day of the Event and Post Event Evaluation</li>
</ul>';
		  }
		  elseif($course_id == 2)
		  {
			  $course_topics = '<ul>
<li>The profession of a wedding planner and types of ceremonies</li>
<li>Working with client</li>
<li>Engagement & bachelor parties</li>
<li>Getting the look and feel right – venues, music, roles, invitations, guests lists, speeches</li>
<li>Dressing the wedding party</li>
<li>Perfecting essential details – décor, the banquet, gifts, menu, floral arrangements, honeymoon</li>
<li>Summing up – final budget, assessment</li>
<li>Starting and marketing your wedding planner business</li>
<li>Wedding planning resources</li>
</ul>';
		  }
		  elseif($course_id == 3)
		  {
			  $course_topics = '<ul>
<li>Market research and competitors analysis, funding and available help
<li>Introduction to marketing.
<li>Business structureslegislation and regulations, registering your business
<li>Budjet and cash flows, accounting and finance
<li>Insurance, premises, suppliers, staf
<li>Home based business
<li>Business plan
<li>Launching your business
</ul>';
		  }
		   elseif($course_id == 4)
		  {
			  $course_topics = '<ul>
<li>Introduction to marketing</li>
<li>Marketing plan</li>
<li>Low cost marketing techniques</li>
<li>Developing your brand</li>
<li>Setting up and managing website</li>
<li>Social media and online marketing</li>
<li>Public relations and advertising</li>
<li>Sales campaigns and leads generation</li>
</ul>';
		  }
		     elseif($course_id == 5)
		  {
			  $course_name='Music & Film Events Manager';
			  $course_topics = '<ul>
<li>Planning the event incl. team, budget, location, date</li>
<li>Marketing / Advertising / Publicity incl. website, tickets, sponsors, partners and promotional materials</li>
<li>Ticket sales and pricing</li>
<li>Availing of help incl. mentoring, networking, state agencies & grants</li>
<li>Budgeting, cash flow, accounting, banking, insurance, contracts</li>
<li>Market research, brand-building, social media</li>
</ul>';
		  }
		   elseif($course_id == 7)
		  {	$course_name='Bartender & Barista';
			  $course_topics = '<ul>
<li>Essential bartending tools and supplies</li>
<li>Hygiene, safety & presentation</li>
<li>Mixology & pouring</li>
<li>Responsibility and flair</li>
<li>Origin and cultivation of coffee </li>
<li>Preparing to brew</li>
<li>Grinding and espresso</li>
<li>Steaming, frothing & texture</li>
</ul>';
		  }		 
		  
		if($user_lang_id==4)
		{  
		  $cssLink = "public/letters/css/letters_new.css";		  
		  $html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Proof of enrollement</title>
<link rel="stylesheet" href="'.$cssLink.'" type="text/css" />
</head>

<body>
<div class="letter_outer">
<div class="letter_header">
<h2 class="letter_letterNme"><span>Completion </span> Letter</h2>
</div>
<div class="clear"></div>
<div class="letter_content">
<div class="letter_address">
<p>'.$certificate_user_name.' </p>
					<p>'.$user_house_number.' '.$user_street.' </p>
					<p>'.$user_address.' </p>
					<p>'.$user_zip_code.' '.$user_city.' </p>
					<p>'.$user_country_name.'</p>
				</div>
				<div class="clear"></div>
<p>To whom it may concern,</p>
<p>We are pleased to  confirm that, on '.$completed_date.' '.$month_name.' '.$completed_year.', '.$certificate_user_name.' successfully completed our '.$course_name.' e-learning course. '.$certificate_user_name.' graduated with a '.$grade.'  grade.</p>

<p>The course consists of '.$course_hours.' study hours and is part of Eventtrix suite of e-learning opportunities. The course educational excellence is assured through accreditation from International Council for Online Educational Standards.</p>
 
<p> The course includes study content with practical examples, and exams. There are a total of '.$module_count.' modules.</p>


<p>Topics covered by the course include:</p>
			<p>
			'.$course_topics.'
			</p>
			<p>We congratulate '.$certificate_user_name.' on completing '.$course_name.'  course and wish '.$gender_pronoun.' every
success in '.$gender_pronoun_2.' future career.</p>
<h4 class="letter_kind_rgrds">
Kind regards,</h4>
<div class="clear"></div>
<ol>
<li>Darren Taylor</li>
<li>CEO</li>
<li>www.eventtrix.com</li>
<li>info@eventtrix.com</li>
</ol>
</div>
</div>
</body>
</html>';
		}
		
		
			  
		$data = pdf_create($html, 'proof_completion_'.$user_id.'_'.$course_id,false);		
	
		//$data = pdf_create($html, '', false);	
		$this->path = "public/certificate/proof_completion/proof_completion_".$user_id."_".$course_id.".pdf";
		write_file($this->path, $data);
     
		$sendemail = true;
		
		$student_data = $this->user_model->get_student_details($user_id);
		
		$user_country_name = $this->user_model->get_country_name($student_data[0]->country_id);
		
		
		if($sendemail)
		{
			$this->load->library('email');
			//$tomail = 'info@trendimi.net';
			//$tomail = 'ajithupnp@gmail.com';
			//$tomail = 'certificates@eventtrix.com';
			               if($this->session->userdata('paid_from_sandbox_account'))
						   {
							$tomail = 'anoopramachandran04@gmail.com';
						   }
						   else
						   {
							$tomail = 'certificates@eventtrix.com';
						   }
			//$tomail = 'deeputg1992@gmail.com';
			//echo "<br>To mail ".$tomail;
					
					   $emailSubject = "Proof of completion is attached  ".$student_data[0]->email;;
					  $mailContent = "<p>Please find the attacahment of proof of completion here with it.<p>";
					  $mailContent = "<p>User details of Proof of completion hard copy applied, <p>";
					  
					  $mailContent .= "<p>User name  : ".$student_data[0]->first_name." ".$student_data[0]->last_name."</p>";
					  $mailContent .= "<p>House Number :  ".$student_data[0]->house_number."</p>";
					  $mailContent .= "<p>Address :  ".$student_data[0]->address."</p>";					  
					  $mailContent .= "<p>City :  ".$student_data[0]->city."</p>";
					  $mailContent .= "<p>Zip code :  ".$student_data[0]->zipcode."</p>";
					  $mailContent .= "<p>Country : ".$user_country_name."</p>";
						   	
						//	echo "<br>Mail content ".$mailContent;
							
					  $this->email->from('info@eventtrix.com', 'Team EventTrix');
					  $this->email->to($tomail); 
					  $this->email->attach($this->path);
					  
					  $this->email->subject($emailSubject);
					  $this->email->message($mailContent);	
					  
					  $this->email->send();
					//  echo "Mail send ";
		}
	
 //redirect('coursemanager/success_hardcopy/'.$product_id, 'refresh');
  redirect('sales/success_letter_hardcopy/'.$product_id, 'refresh');
			  
	  }
	  function after_proof_completion_pay()
	  {
		  $payment_id = $this->uri->segment(4);
		  $course_id  = $this->uri->segment(5);
		  $product_id = $this->uri->segment(6);
		  $user_id=$this->session->userdata['student_logged_in']['id'];
		  
		  $today = date("Y-m-d");
		  $insert_data=array("user_id"=>$user_id,"course_id"=>$course_id,"type"=>'proof_completion',"date_applied"=>$today,"product_id"=>$product_id,"payment_id"=>$payment_id);		 
		  $this->user_model->insertQuerys("user_subscriptions",$insert_data);
		    redirect('coursemanager/certificate');	  
		  
	  }
	  
	  function proof_completion_download_test($course_id)
	  {
		  $user_id = $this->session->userdata['student_logged_in']['id'];	
		  $this->load->helper(array('dompdf', 'file'));		 
		  $html = $this->pdf_html_model->create_proof_completion_pdf($course_id);		
		  $data = pdf_create($html, 'course_completion_'.$user_id.'_'.$course_id);   
    	  write_file('name', $data);			  
	 }
	 
	  function proof_completion_download($course_id)
	  {
		  
		  $user_id = $this->session->userdata['student_logged_in']['id'];	
		  $this->load->helper(array('dompdf', 'file'));		 
		  $html = $this->pdf_html_model->create_proof_completion_pdf_new($course_id);		
		  $data = pdf_create($html, 'course_completion_'.$user_id.'_'.$course_id);   
    	  write_file('name', $data);			  
	 }
	 
	  function apply_eTranscript($course_id)
	  {
		 if(!$this->session->userdata('student_logged_in')){
		  redirect('home');
		}
		
		$user_id=$this->session->userdata['student_logged_in']['id']; 
		 
		$content = array();
		$currency_id = $this->currId;
		$currency_code = $this->currencyCode;
		
		
		
		$product_id = $this->common_model->getProdectId('transcript','',1);
		$amountArr = $this->common_model->getProductFee($product_id,$this->currId);		
		
		
		$content['product_id'] = $product_id;
		$content['course_id']  = $course_id;
		$content['user_id']    = $user_id;
		$content['currency_code'] = $amountArr['currency_code'];
		$content['curr_id'] = $amountArr['currency_id'];;
		$content['amount'] = $amountArr['amount'];
		//echo "<pre>";print_r($amountArr);exit;
		$data=$this->get_student_deatils_for_popup();
		
		$data['translate'] = $this->tr_common;
		$data['view'] = 'apply_eTranscript';
		$data['content'] = $content;
		$this->load->view('user/template_inner',$data);
		 
		  
	  }
	  
	  function after_transcript_pay()
	  {
			
		  $payment_id = $this->uri->segment(4);
		  $course_id  = $this->uri->segment(5);
		  $product_id = $this->uri->segment(6);
		  $user_id=$this->session->userdata['student_logged_in']['id'];  
		
		  
		
		  $today = date("Y-m-d");
		  $insert_data=array("user_id"=>$user_id,"course_id"=>$course_id,"type"=>'transcript',"date_applied"=>$today,"product_id"=>$product_id,"payment_id"=>$payment_id);
		   $this->user_model->insertQuerys("user_subscriptions",$insert_data);
		   $this->session->set_flashdata('message',"eTranscript bought successfully.");
		 
		  redirect('coursemanager/certificate');
		  
	  }
	  
	  function eTranscript_download_test($course_id)
	 {
		  $this->load->helper(array('dompdf', 'file'));
		  $userId = $this->session->userdata['student_logged_in']['id'];	
		  $user_name = $this->common_model->get_user_name($userId);	
          $html = $this->pdf_html_model->create_transcript_pdf($course_id); 		  
	 	  $data = pdf_create($html, 'eTranscript_'.$userId.'_'.$course_id);    
    	  write_file('name', $data);		  
	  }
	  
	   function eTranscript_download($course_id)
	 {
		  $this->load->helper(array('dompdf', 'file'));
		  $userId = $this->session->userdata['student_logged_in']['id'];	
		  $user_name = $this->common_model->get_user_name($userId);	
          $html = $this->pdf_html_model->create_transcript_pdf_new($course_id); 		  
	 	  $data = pdf_create($html, 'eTranscript_'.$userId.'_'.$course_id);    
    	  write_file('name', $data);		  
	  }
	  
	  
	  function course_access_option($course_id)
	  {		  
	  		if(!$this->session->userdata('student_logged_in')){
		  		redirect('home');
			}
			$lang_id = $this->session->userdata('language');
			$user_id=$this->session->userdata['student_logged_in']['id']; 
			
		    $this->tr_common['tr_Course_material_access']  = $this->user_model->translate_('Course_material_access');
			$this->tr_common['tr_Select_period']  = $this->user_model->translate_('Select_period');
			$this->tr_common['tr_Select']  = $this->user_model->translate_('Select');
			$this->tr_common['tr_months']  = $this->user_model->translate_('months');
			$this->tr_common['tr_make_payment']  = $this->user_model->translate_('make_payment');
			
								 
			 
			$content = array();
			$currency_id = $this->currId;
			$currency_code = $this->currencyCode;			
			$course_name = $this->common_model->get_course_name($course_id); 			
			$access_options = $this->certificate_model->get_access_options();	
			
			$data=$this->get_student_deatils_for_popup();
			
			/*$product_id = $this->common_model->getProdectId('transcript','',1);
		$amountArr = $this->common_model->getProductFee($product_id,$this->currId);
		$amount  = $amountArr['amount'];
		
		
		$content['product_id'] = $product_id;
		$content['course_id']  = $course_id;
		$content['user_id']    = $user_id;
		$content['currency_code'] = $amountArr['currency_code'];
		$content['curr_id'] = $amountArr['currency_id'];;
		$content['amount'] = $amount;
			*/
			
			$content['acces_options'] = $access_options;	
			$content['course_name']   = $course_name;	
			$content['user_id']       = $user_id;					
			$data['course_id']  		= $course_id;		
			$data['currency_id']  	  = $currency_id;
			$data['currency_code']  	= $currency_code;
			
		
			$data['translate'] = $this->tr_common;
			
			$data['view'] 			 = 'course_acces_option';   
			$data['content'] 		  = $content;
			//$this->load->view('user/course_template',$data);  
			$this->load->view('user/template_inner',$data);  		   
	  }
	  function get_access_amount($product_id)
	  {
			$content = array();
			$data =array();
			$currency_id = $this->currId;
			$currency_code = $this->currencyCode;
			
			
			
			
			$amountArr = $this->common_model->getProductFee($product_id,$this->currId);
			
			$content['product_id'] = $product_id;
			$content['currency_code'] = $amountArr['currency_code'];
			$content['curr_id'] = $amountArr['currency_id'];;
			$content['amount'] =  $amountArr['amount'];
			
			
			
			
			//$access_amount = $this->certificate_model->get_postage_amount($product_id,$currency_id);
			
			$product_details = $this->common_model->get_product_details($product_id);
			
			foreach($product_details as $det)
			{
				$period = $det->item_id;
			}
			
			//$data['fee_text'] = $this->user_model->translate_('Fee_for_'.$period.'_months_course_access_is'); 
			$data['fee_text'] = 'Fee for '.$period.' months course access is :';
			$data['product_id']    = $product_id;
			$data['currency_code'] = $amountArr['currency_code'];
			
			$data['access_amount']  = $amountArr['amount'];
			 /*foreach ($access_amount as $value) {	
					$data['access_amount']  = $value->amount;			
				}*/
			echo json_encode($data);  	
	}
	
	  function after_course_access_pay()
	  {
		 $payment_id = $this->uri->segment(4);
		  $course_id  = $this->uri->segment(5);
		  $product_id = $this->uri->segment(6);
		  $user_id=$this->session->userdata['student_logged_in']['id'];
		  
		  if($product_id == 46)
		  {
			  $type = 'access_6';
		  }
		  else if($product_id == 47)
		  {
			   $type = 'access_12';
		  }
		  
		  $today = date("Y-m-d");
		  $insert_data=array("user_id"=>$user_id,"course_id"=>$course_id,"type"=>$type,"date_applied"=>$today,"product_id"=>$product_id,"payment_id"=>$payment_id);		 
		  $this->user_model->insertQuerys("user_subscriptions",$insert_data);
		  
		  
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
				 // $accessdate=date("Y-m-d", strtotime("+$period months")); 
				
			 }
										
		//  echo "<br>Current acces date ".$cur_expiry_date;		
		 		  
		// $expiry_date = date("Y-m-d", strtotime("+$period days"));
		 
		// echo "<br>Old exipry date ".$expiry_date;
		 
		 $course_status = $this->user_model->get_student_course_status($course_id,$user_id);
		// echo "<br> Course status ".$course_status;
		 
		 if($course_status == 7 || $course_status==6) // if expired or archived change status to completed
		 {
			
			
			 $mark_details = $this->get_student_progress($course_id); 
			 /*echo "<pre>";
			 print_r($mark_details);*/
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
		
		/*echo "<pre>";
		print_r($update_data);*/
    	
	 	 $this->user_model->update_student_enrollments($course_id,$user_id,$update_data);
		  
		  
		  
	  redirect('coursemanager/certificate');
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
	
	function validatevoucher($vcode)
	{
		
		$vArr = $this->gift_voucher_model->isValidforDeals($vcode);
		
		echo json_encode($vArr);
	}
	function deals_2()
	{
		if(isset($this->session->userdata['student_logged_in']))
		{
			if(isset($this->session->userdata['deals']['vCode']))
			{
				redirect('sales/payment_user/', 'refresh');
				
			}
			else
			{
				redirect('start', 'refresh');
			}
		}
		if(isset($_POST['userId_deals']))
		{
			
			$content['username']=$this->input->post('userId_deals');
			$content['password']=$this->input->post('pass_deals');
			
			
			$this->form_validation->set_rules('userId_deals', 'Username', 'trim|required|xss_clean');
   			$this->form_validation->set_rules('pass_deals', 'Password', 'trim|required|xss_clean|');

			$content['username'] = $this->input->post('userId_deals');
			if($this->form_validation->run() == TRUE)
			{//Go to private area
			$result = $this->user_model->login($content['username'], $content['password']);
					if($result)
   					{
     					$sess_array = array();
     					foreach($result as $row)
     					{ // echo $row->active;
               if ($row->status!=1) {
                
                  $this->session->set_flashdata('message','student is not active');
                 redirect('deals_2');
                }

                else{
       						$sess_array = array('id' => $row->user_id,'username' => $row->username );
       						$this->session->set_userdata('student_logged_in', $sess_array);
							$sess_array1 = array('language' => $row->lang_id);
       						$this->session->set_userdata($sess_array1);
                  $this->session->set_flashdata('message','Login successfull');
                 
                }
     					}
     					
   					}
					else
   					{
						$this->session->set_flashdata('message','Invalid stylist ID or code');
     					 redirect('deals_2');
   					}
			if(isset($this->session->userdata['deals']['vCode']) && isset($this->session->userdata['student_logged_in']))
			{
				redirect('sales/payment_user/', 'refresh');
				
			}
			else
				redirect('coursemanager/deals', 'refresh');
				
      }
			
		}
		
		$top_menu_base_courses = $this->user_model->get_courses($this->language);	
		
		if(empty($top_menu_base_courses))
		{
			$top_menu_base_courses = $this->user_model->get_courses(4);	
		}
		
		$content['top_menu_base_courses'] = $top_menu_base_courses;
		
	  $content['tr_deals']  = $this->user_model->translate_('deals!');
	  $content['tr_getting_started_info']  = $this->user_model->translate_('tr_getting_started_info');
	  $content['tr_isuser']  = $this->user_model->translate_('are_you_a_user_of_trendimi');
	  $content['tr_submit']  = $this->user_model->translate_('submit');
	  $content['tr_login']  = $this->user_model->translate_('tr_getting_started_info');
	    $content['tr_yes']  = $this->user_model->translate_('yes');
	    $content['tr_no']  = $this->user_model->translate_('no');
		$content['tr_code']  = $this->user_model->translate_('create_secret_code');
	    $content['tr_UserId']  = $this->user_model->translate_('UserId');
		$content['tr_LOGIN']  = $this->user_model->translate_('LOGIN');
	  $content['langId']  = $this->session->userdata['language'];
	  
	  $content['translate'] = $this->tr_common;
	  $content['view']  = "deals_2";
	  $this->load->view('user/deals_2',$content);
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
	
	function enroll_old(){
		
		//echo "<pre>";print_r($this->session->userdata['deals']);exit;
		$this->load->model('gift_voucher_model');
		if(!isset($this->session->userdata['deals']['vCode']))
		{
			redirect('start','refresh');
		}
		$vouchercode = $this->session->userdata['deals']['vCode'];
		//echo $vouchercode ;
		$voucherDetails = $this->gift_voucher_model->getDetails_of_vcode($vouchercode);
		if($voucherDetails[0]->courses_idcourses==""||$voucherDetails[0]->courses_idcourses==0)
		{
			$content['course_set']=$this->user_model->get_course($this->session->userdata['language']);
			$content['course_count']=0;
		}
		else
		{
			 $course_ids = explode(",",$voucherDetails[0]->courses_idcourses);
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
		$content['style_id']=$this->user_model->translate_('stylist_id');
		$content['code']=$this->user_model->translate_('code');
		$content['buy_it']=$this->user_model->translate_('buy_it_now');
		$content['country']=$this->user_model->get_country();
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
		  $studentdata['first_name'] = $content['fname'] = ucfirst(strtolower($this->input->post('fname')));
		  $studentdata['last_name'] = $content['lname'] = ucfirst(strtolower($this->input->post('lname')));
		  $studentdata['email'] = $content['email'] = $this->input->post('email');
		//  $studentdata['mobile'] = $content['mobile'] = $this->input->post('mobile');
		  $studentdata['username'] = $content['user_name'] = $this->input->post('user_name');
		  $content['pword'] = $this->input->post('pword');
		  $studentdata['password']=$this->encrypt->encode($this->input->post('pword'));
		  $studentdata['gender'] = $content['gender'] = $this->input->post('gender');
		  $studentdata['contact_number'] = $content['contact_no'] = $this->input->post('contact_no');
		  $studentdata['dob'] = $content['dob_check'] = $this->input->post('dob_check');
		  $studentdata['house_number'] = $content['house_no'] = $this->input->post('house_no');		 
		  $studentdata['address'] = $content['address'] = $this->input->post('address');
          $studentdata['street'] = $content['street'] = $this->input->post('street');
		  $studentdata['zipcode'] = $content['zip_code'] = $this->input->post('zip_code');
          $studentdata['city'] = $content['city'] = $this->input->post('city');
		  $studentdata['country_id'] = $content['country_set'] = $this->input->post('country_id');
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
		  //$this->form_validation->set_rules('dob_check', 'Date', 'required');
		 
		  //$this->form_validation->set_rules('voucher_code', 'VoucherCode', 'callback_checkVcode');
		  
		  
		 
		  if($this->form_validation->run())
		  {
			  	
					$this->user_model->add_student_temp($studentdata);
					$student_id=$this->db->insert_id();
									
					redirect('/sales/enroll_2/'.$student_id, 'refresh');
		  }
		}
		


	
	
	$data['translate'] = $this->tr_common;
	
	$data['view'] = 'deals_enroll';
	$data['content'] = $content;
	$this->load->view('user/outerTemplate',$data);
	}
	
	function enroll(){
		
		//echo "<pre>";print_r($this->session->userdata['deals']);exit;
		$this->load->model('gift_voucher_model');
		if(!isset($this->session->userdata['deals']['vCode']))
		{
			redirect('start','refresh');
		}
		$vouchercode = $this->session->userdata['deals']['vCode'];
		//echo $vouchercode ;
		$voucherDetails = $this->gift_voucher_model->getDetails_of_vcode($vouchercode);
		if($voucherDetails[0]->courses_idcourses==""||$voucherDetails[0]->courses_idcourses==0)
		{
			$content['course_set']=$this->user_model->get_course($this->session->userdata['language']);
			$content['course_count']=0;
		}
		else
		{
			$course_ids = explode(",",$voucherDetails[0]->courses_idcourses);
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
		$content['style_id']=$this->user_model->translate_('stylist_id');
		$content['code']=$this->user_model->translate_('code');
		$content['buy_it']=$this->user_model->translate_('buy_it_now');
		$content['country']=$this->user_model->get_country();
		$content['states']=$this->user_model->get_states();
		$content['reason_to_buy']=$this->user_model->get_reason_buy($this->language);
		
		
		$vouchercode = $this->session->userdata['deals']['vCode'];
		//echo $vouchercode ;
		$voucherDetails = $this->gift_voucher_model->getDetails_of_vcode($vouchercode);
		
		if($voucherDetails[0]->courses_idcourses==""||$voucherDetails[0]->courses_idcourses==0)
		{
			$content['course_set']=$this->user_model->get_course($this->session->userdata['language']);
			$content['course_count']=0;
			foreach($content['course_set'] as $key=>$value)
			{
			$coursename =$this->user_model->get_coursename($key);
			$content['val_days'][$key]['validity']=$voucherDetails[0]->validity;
			$content['val_days'][$key]['price']="0 ".$this->currencyCode;
			}
			
		}
		else
		{
			 $course_ids = explode(",",$voucherDetails[0]->courses_idcourses);
			$content['course_count']=count($course_ids);
			for($c=0;$c<count($course_ids);$c++)
			{
				$course_namesArr =$this->user_model->get_coursename($course_ids[$c]);
			    $content['course_set'][$course_ids[$c]]=$course_namesArr[0]->course_name;
				$content['val_days'][$course_ids[$c]]['validity']=$voucherDetails[0]->validity;
				$content['val_days'][$course_ids[$c]]['price']="0 ".$this->currencyCode;
			}
		}
		
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
		
		$this->tr_common['tr_v_code']   =$this->user_model->translate_('v_code'); 	
		$this->tr_common['tr_voucher_code_note_text']   =$this->user_model->translate_('voucher_code_note_text'); 		
		$this->tr_common['tr_course']   =$this->user_model->translate_('course'); 	

		if(isset($_POST['fname']))
		{
			
		  $studentdata  = array();
		  $studentdata['first_name'] = $content['fname'] = ucfirst(strtolower($this->input->post('fname')));
		  $studentdata['last_name'] = $content['lname'] = ucfirst(strtolower($this->input->post('lname')));
		  $studentdata['email'] = $content['email'] = $this->input->post('email');
		//  $studentdata['mobile'] = $content['mobile'] = $this->input->post('mobile');
		  $studentdata['username'] = $content['user_name'] = $this->input->post('user_name');
		  $content['pword'] = $this->input->post('pword');
		  $studentdata['password']=$this->encrypt->encode($this->input->post('pword'));
		  $studentdata['gender'] = $content['gender'] = $this->input->post('gender');
		  $studentdata['contact_number'] = $content['contact_no'] = $this->input->post('contact_no');
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
		  //$studentdata['reason_id'] = $content['reason_to_buy_set'] = implode(",",$this->input->post('reason_id')); 
		  if($content['course_count']==0)
		  $studentdata['course_id'] = $content['course_set'] = $this->input->post('course_id');

		  $studentdata['with_coupon']='yes';
		  {
		  $studentdata['coupon_code'] = $this->session->userdata['deals']['vCode'];
		  if(isset($this->session->userdata['deals']['is_req'])&&$this->session->userdata['deals']['is_req']==1)
		  {
		  $studentdata['redemption_code'] = $this->session->userdata['deals']['secure'];
		  $studentdata['redemption_pdf'] = $this->session->userdata['deals']['uploaded']['upload_data']['file_name'];
		  }
		  
		  
		  }
		
		 
		 
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
		  //$this->form_validation->set_rules('dob_check', 'Date', 'required');
		 
		  //$this->form_validation->set_rules('voucher_code', 'VoucherCode', 'callback_checkVcode');
		  
		  
		 
		  if($this->form_validation->run())
		  {
			  		$this->session->unset_userdata('student_temp_id');	
					$this->session->unset_userdata('cart_session_id');
					$this->session->unset_userdata('package_applying_course');			
					$this->session->unset_userdata('added_user_id');	
					session_regenerate_id();
				
					$this->user_model->add_student_temp($studentdata);
					$student_id=$this->db->insert_id();									
				$sess_array = array('student_temp_id' => $student_id);			
					$this->session->set_userdata($sess_array);	
					
					
				if($content['course_count']==0)
				{
					//$redirectPath = '/sales/payment_2/'.$student_id.'/'.$studentdata['course_id'];
					
					$redirectPath = '/sales/package_details/'.$student_id.'/'.$studentdata['course_id'];
				}
				else
				{
					//$redirectPath = '/sales/payment_2/'.$student_id;
					$redirectPath = '/sales/package_details/'.$student_id;
				}
				
					
					redirect($redirectPath, 'refresh');
		  }
		  
		}
			
		$data['translate'] = $this->tr_common;	
		$data['view'] = 'deals_enroll_test';
		$data['content'] = $content;
		$this->load->view('user/outerTemplate',$data);
	}
	
	function package_details()
	{
		
		$content = array();
		$course_details = array();
		$temp_id = $this->uri->segment(3);
		$temp_course_id = $this->uri->segment(4);
		$content['stud_id'] = $temp_id;
		
		
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
		
		$vouchercode = $this->session->userdata['deals']['vCode'];
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
			
			$redirectPath = '/sales/payment_details/'.$temp_id.'/'.$sales_course_id;
		}
		else
		{
			$sales_course_id = $voucherDetails[0]->courses_idcourses;
			$multiple_course = '';
			$courseId = explode(",",$voucherDetails[0]->courses_idcourses);			
			$content['course_count']= $sales_course_count = count($courseId);			
			for($c=0;$c<count($courseId);$c++)
			{				
				$coursename =$this->user_model->get_coursename($courseId[$c]);
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
			
		   $redirectPath = '/sales/payment_details/'.$temp_id;
		}
		
		
		
		$course_product_id = $this->common_model->getProdectId('course',$item_id='',$sales_course_count);
		
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
			
			/*echo "Product id  ".$product_id;
			echo "<br> Selected items ".$new_selected_values;
			echo "<br>";
			
			echo "<pre>";
			print_r($product_details);
			
			echo "<br>";
			
			echo "<pre>";
			print_r($product_price_details);*/
		
			
			// Assuming Course price is 0
			
			$cart_main_insert_array = array("session_id"=>$this->session->userdata('cart_session_id'),"pre_user_id"=>$temp_id,"source"=>'deals_package',"item_count"=>1,"total_cart_amount"=>0,"currency_id"=>$product_price_details['currency_id'],'gift_voucher_code'=>$vouchercode);
			
			//$cart_main_id =1;
  		  
		    $cart_main_id = $this->common_model->insert_to_table("sales_cart_main",$cart_main_insert_array);
			
			$user_agent_data = array();
			
			$user_agent_data['cart_main_id']  = $cart_main_id;
			$user_agent_data['session_id'] 	= $this->session->userdata('cart_session_id');
			$user_agent_data['os'] 			= $this->agent->platform();
			$user_agent_data['browser'] 	   = $this->agent->agent_string();
			
			
			$this->common_model->insert_to_table("sales_user_agents",$user_agent_data);
				
	
			// Assuming Course price is 0							
		$item_details_array = array("cart_main_id"=>$cart_main_id,"product_id"=>$course_product_id,"item_amount"=>0,"currency"=>$product_price_details['currency_id']);
			
		
			
			$cart_items_id = $this->common_model->insert_to_table("sales_cart_items",$item_details_array);		
			
			$items_array = array("cart_main_id"=>$cart_main_id,"cart_items_id"=>$cart_items_id,"product_type"=>$product_details[0]->type,"selected_item_ids"=>$sales_course_id);
			
			$item_id = $this->common_model->insert_to_table("sales_cart_item_details",$items_array);	
			
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
			
			//$cart_main_details = $this->sales_model->get_cart_main_details($this->session->userdata('cart_session_id'));
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
		
		$content['package_details']=$package_details;
		$content['package_fees']=$package_fees;
		
		$content['product_id']=$product_id;
		$content['package_product_id']=$package_product_id;
		$content['added_pack_id'] = $added_pack_id;
		
			
	//	$voucher_course_ids = str_replace(",","+",$sales_course_id);
		
		$content['curr_id'] = $this->currId;
		$content['lang_id'] = $this->language;
		//$content['cour_id'] = $sales_course_id;
		$content['cour_id'] = $multiple_course;
		
		$data['translate'] = $this->tr_common;
		$data['view'] = 'package_details_deals';
		$data['content'] = $content;
		$this->load->view('user/template_outer',$data);
		
	}
	
	function payment_details()
 	{
		 
 		
		 $temp_id = $this->uri->segment(3);
		 $temp_course_id  = $this->uri->segment(4);
		/* echo "<br> 1 -> ".$this->uri->segment(1);
		 echo "<br> 2 -> ".$this->uri->segment(2);
		 echo "<br> 3 -> ".$this->uri->segment(3);
		 echo "<br> 4 -> ".$this->uri->segment(4);
		 echo "<br> 5 -> ".$this->uri->segment(5);
		 echo "<br>Temp id ".$temp_id;
		 echo "<br>Course  id ".$temp_course_id;
		 exit;*/
		 
		$this->tr_common['tr_complete_registraion']  = $this->user_model->translate_('complete_registraion');		
		$this->tr_common['tr_personal_details']      = $this->user_model->translate_('personal_details');		
		$this->tr_common['tr_packages']    		  = $this->user_model->translate_('packages');
		$this->tr_common['tr_payment_details']       = $this->user_model->translate_('payment_details');
		$this->tr_common['tr_registration']    	  = $this->user_model->translate_('registration');
		$this->tr_common['tr_camp_days']    	  = $this->user_model->translate_('camp_days');		 
				
		$this->tr_common['tr_course']   =$this->user_model->translate_('course'); 		
		$this->tr_common['tr_amount']   =$this->user_model->translate_('amount'); 		
 		$this->tr_common['tr_valid_for']   =$this->user_model->translate_('valid_for'); 		
			
		 
		 $content['student_temp'] = $temp_id;
		
		$extended_days = '';
		if(!isset($this->session->userdata['deals']))
		{
			redirect("start","refresh");
		}
		
		$vouchercode = $this->session->userdata['deals']['vCode'];
		//echo $vouchercode ;
		$voucherDetails = $this->gift_voucher_model->getDetails_of_vcode($vouchercode);
		if($voucherDetails[0]->courses_idcourses=="" || $voucherDetails[0]->courses_idcourses==0)
		{
			//$content['course_set']=$this->input->post('course');
			$content['course_count']=0;
			$tempArray = $this->user_model->get_student_temp($content['student_temp']);
			$courseId[] =  $temp_course_id;
			$coursename =$this->user_model->get_coursename($courseId[0]);
			foreach($coursename as $cname)
			{
			$content['course_name'][]=$cname->course_name ;
			//$content['val_days'][]=$cname->course_validity;
			}
		}
		else
		{
			$courseId = explode(",",$voucherDetails[0]->courses_idcourses);
			$content['course_count']=count($courseId);
			for($c=0;$c<count($courseId);$c++)
			{
				$coursename =$this->user_model->get_coursename($courseId[$c]);
			    foreach($coursename as $cname)
				{
				$content['course_name'][]=$cname->course_name ;
			//	$content['val_days'][]=$cname->course_validity;
				}
			}
		}
		
		$content['val_days'][] = $voucherDetails[0]->validity;
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
		if($data['total_price']!=0)
		redirect('home/package_check_out/'.$temp_id.'/'.$temp_course_id);
		else
		redirect('home/package_check_out/'.$temp_id.'/'.$temp_course_id);
    //$coursename=$this->user_model->get_coursename($content['cour_id']);
	//echo "<pre>";print_r($coursename[0]);exit;
    
   
    $content['language']=$this->language;
   	$curr_code=$this->user_model->get_currency_id($this->con_name);
		
//	$curr_id= $this->currId;
//	$content['currency_code'] = $this->currencyCode;
//	$content['curr_id'] = $curr_id;
	
	$content['stud_id'] = $temp_id;
	$content['course_id'] = $courseId[0];
//	$content['product_id'] =$product_id;
	$content['extended_days']=$extended_days;
	
	
	$data['translate'] = $this->tr_common;
    $data['view'] = 'payment_details_deals';
    $data['content'] = $content;
    $this->load->view('user/template_outer',$data);

  
 
 	}
	
	function read_more_view($lang_id,$curr_id,$stud_id)
	{
		$content = array();
		
		$content['stud_id'] = $stud_id;	
		$this->tr_common['tr_SIGN_UP']   =$this->user_model->translate_('SIGN_UP'); 
		
		$this->tr_common['tr_back']    			= $this->user_model->translate_('back');
		$this->tr_common['tr_personal_details']    = $this->user_model->translate_('personal_details');		
		$this->tr_common['tr_packages']    		= $this->user_model->translate_('packages');
		$this->tr_common['tr_payment_details']     = $this->user_model->translate_('payment_details');
		$this->tr_common['tr_registration']    	= $this->user_model->translate_('registration');
		
		
		$package_read_more = $this->package_model->get_read_more_details($lang_id,$curr_id);
		$content['package_read_more'] = $package_read_more[0]->description;
		
		$data['translate'] = $this->tr_common;
		$data['view'] = 'package_read_more_deals';
		$data['content'] = $content;
		$this->load->view('user/outerTemplate',$data);
		
	}
	
	
	function enroll_2($student_id){
		
		//echo "<pre>";print_r($this->session->userdata['deals']);exit;
		$this->load->model('gift_voucher_model');
		if(!isset($this->session->userdata['deals']['vCode']))
		{
			redirect('start','refresh');
		}
		$vouchercode = $this->session->userdata['deals']['vCode'];
		//echo $vouchercode ;
		$voucherDetails = $this->gift_voucher_model->getDetails_of_vcode($vouchercode);
		
		if($voucherDetails[0]->courses_idcourses==""||$voucherDetails[0]->courses_idcourses==0)
		{
			$content['course_set']=$this->user_model->get_course($this->session->userdata['language']);
			$content['course_count']=0;
			foreach($content['course_set'] as $key=>$value)
			{
			$coursename =$this->user_model->get_coursename($key);
			$content['val_days'][$key]['validity']=$coursename[0]->course_validity." Days";
			$content['val_days'][$key]['price']="0 ".$this->currencyCode;
			}
			
		}
		else
		{
			 $course_ids = explode(",",$voucherDetails[0]->courses_idcourses);
			$content['course_count']=count($course_ids);
			for($c=0;$c<count($course_ids);$c++)
			{
				$course_namesArr =$this->user_model->get_coursename($course_ids[$c]);
			    $content['course_set'][$course_ids[$c]]=$course_namesArr[0]->course_name;
				$content['val_days'][$course_ids[$c]]['validity']=$course_namesArr[0]->course_validity." Days";
				$content['val_days'][$course_ids[$c]]['price']="0 ".$this->currencyCode;
			}
		}
		
		
		$content['base_course']=$this->course;
		$content['student_status']=$this->student_status;
		$content['topmenu']=$this->menu;
		$content['language']=$this->language;
		$content['style_id']=$this->user_model->translate_('stylist_id');
		$content['code']=$this->user_model->translate_('code');
		$content['buy_it']=$this->user_model->translate_('buy_it_now');
		$content['country']=$this->user_model->get_country();
		
		$this->tr_common['tr_v_code']   =$this->user_model->translate_('v_code'); 	
		$this->tr_common['tr_voucher_code_note_text']   =$this->user_model->translate_('voucher_code_note_text'); 		
		$this->tr_common['tr_course']   =$this->user_model->translate_('course'); 	
		$this->tr_common['tr_terms_agree']   =$this->user_model->translate_('terms_agree'); 	
	    $this->tr_common['tr_next']   =$this->user_model->translate_('_next'); 
		$this->tr_common['tr_required']   	   		 =$this->user_model->translate_('required'); 

		if(isset($_POST))
		{
			
		  $studentdata  = array();
		  
		  if($content['course_count']==0)
		  {
		  $studentdata['course_id'] = $this->input->post('course_id');
		  $direct_url = '/sales/withCoupon/'.$student_id.'/'.$studentdata['course_id'];
		  }
		  else
		  $direct_url = '/sales/withCoupon/'.$student_id;

		  $studentdata['with_coupon']='yes';
		  $studentdata['coupon_code'] = $this->session->userdata['deals']['vCode'];
		  if(isset($this->session->userdata['deals']['is_req'])&&$this->session->userdata['deals']['is_req']==1)
		  $studentdata['redemption_code'] = $this->session->userdata['deals']['secure'];
		  
		  $studentdata['comment'] = $content['comments'] = $this->input->post('comments');
		  $content['terms'] = $this->input->post('terms');
	
		  $this->form_validation->set_rules('terms', 'Accept terms', 'required');
		  if($this->form_validation->run())
		  {
			  if($studentdata['comment'] !=''){
				$mail_data=$studentdata['comment'];
			$temp_details=$this->user_model->get_student_temp($student_id);
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
			  $this->db->where('id',$student_id);
			  $this->db->update('pre_registrations',$studentdata);
			 							  
			  redirect($direct_url, 'refresh');
		  }
		}
		


	
	
	$data['translate'] = $this->tr_common;
	
	$data['view'] = 'deals_enroll_2';
	$data['content'] = $content;
	$this->load->view('user/outerTemplate',$data);
	}
	
	
	function payment_2()
 	{
		 
 		$temp_id = $this->uri->segment(3);
		$temp_course_id = $this->uri->segment(4);
		 $content['student_temp'] = $temp_id;
		
		//echo $temp_id."<br>-------".$temp_course_id;exit;
		if(!isset($this->session->userdata['deals']))
		{
			redirect("start","refresh");
		}
		
		$vouchercode = $this->session->userdata['deals']['vCode'];
		//echo $vouchercode ;
		$voucherDetails = $this->gift_voucher_model->getDetails_of_vcode($vouchercode);	
		
		if($voucherDetails[0]->courses_idcourses=="" || $voucherDetails[0]->courses_idcourses==0)
		{
			//$content['course_set']=$this->input->post('course');
			$content['course_count']=0;
			$tempArray = $this->user_model->get_student_temp($content['student_temp']);
			$courseId[] =  $temp_course_id;
			$coursename =$this->user_model->get_coursename($courseId[0]);
			foreach($coursename as $cname)
			{
			$content['course_name'][]=$cname->course_name ;
			$content['val_days'][]=$cname->course_validity;
			}
			$content['course_idd'] = $temp_course_id;
		}
		else
		{
			$courseId = explode(",",$voucherDetails[0]->courses_idcourses);
			$content['course_count']=count($courseId);
			for($c=0;$c<count($courseId);$c++)
			{
				$coursename =$this->user_model->get_coursename($courseId[$c]);
			    foreach($coursename as $cname)
				{
				$content['course_name'][]=$cname->course_name ;
				$content['val_days'][]=$cname->course_validity;
				}
			}
		}
		
		
    //$coursename=$this->user_model->get_coursename($content['cour_id']);
	//echo "<pre>";print_r($coursename[0]);exit;
    
   
    $content['language']=$this->language;
   	$curr_code=$this->user_model->get_currency_id($this->con_name);
		
	$curr_id= $this->currId;
	$content['currency_code'] = $this->currencyCode;
	$content['curr_id'] = $curr_id;
	
	
   	$coursefee=0;
	
  
	    $content['course_fee']=0;
 
	$data['translate'] = $this->tr_common;
    $data['view'] = 'dealsPayment';
    $data['content'] = $content;
    $this->load->view('user/template',$data);

  
 
 	}
	
	function payment_user()
 	{
		 
 		
		
		//echo $temp_id."<br>-------".$temp_course_id;exit;
		if(!isset($this->session->userdata["student_logged_in"]))
		{
			redirect("start");
		}
		
		$vouchercode = $this->session->userdata['deals']['vCode'];
		//echo $vouchercode ;
		$voucherDetails = $this->gift_voucher_model->getDetails_of_vcode($vouchercode);
		if($voucherDetails[0]->courses_idcourses=="" || $voucherDetails[0]->courses_idcourses==0)
		{
			//$content['course_set']=$this->input->post('course');
			$content['course_count']=0;
			$enrolled_courses = $this->user_model->get_courses_student($this->session->userdata['student_logged_in']['id']);
			foreach($enrolled_courses as $keys)
			{
				$enrlled_ids[]=$keys->course_id;
			}
			 $base_courses = $this->user_model->get_courses($this->language);
			 foreach($base_courses as $cour)
			 {
				 if(in_array($cour->course_id , $enrlled_ids))
				 {
					 continue;
				 }
				 $content['base_courses'][$cour->course_id]=$cour->course_name;
				 $content['val_days'][]=$cour->course_validity;
			 }
			
		}
		else
		{
			$enrolled_courses = $this->user_model->get_courses_student($this->session->userdata['student_logged_in']['id']);
			foreach($enrolled_courses as $keys)
			{
				$enrlled_ids[]=$keys->course_id;
			}
			
			$courseId = explode(",",$voucherDetails[0]->courses_idcourses);
			$content['course_count']=count($courseId);
			//echo "<pre>";print_r($courseId);exit;
			for($c=0;$c<count($courseId);$c++)
			{
				if(in_array($courseId[$c] , $enrlled_ids))
				 {
					 continue;
				 }
				 
				$coursename =$this->user_model->get_coursename($courseId[$c]);
				
			    foreach($coursename as $cname)
				{
				$content['base_courses'][$courseId[$c]]=$cname->course_name ;
				$content['val_days'][]=$cname->course_validity;
				}
			}
			
		}
		
		
    //$coursename=$this->user_model->get_coursename($content['cour_id']);
	//echo "<pre>";print_r($content);exit;
    
   
    $content['language']=$this->language;
   	$curr_code=$this->user_model->get_currency_id($this->con_name);
		
	$curr_id= $this->currId;
	$content['currency_code'] = $this->currencyCode;
	$content['curr_id'] = $curr_id;
	
	
   	$coursefee=0;
	
    //foreach ($coursefee as $key){
      //$content['course_fee']=$key->amount;
	    $content['course_fee']=0;
   // }
	
	//$product=$this->user_model->get_product_id($content['cour_id']);
//	foreach ($product as $key){
//      $content['product_id']=$key->id;
//    }
    //echo "<pre>";print_r($content);exit;
	
	//echo "<pre>";print_r($content);exit;
	
	
	$data['translate'] = $this->tr_common;
    $data['view'] = 'dealsPayment_user';
    $data['content'] = $content;
    $this->load->view('user/template_outer',$data);

  
 
 	}
	
 function deals_old($langId=4)
  {
	  $this->session->unset_userdata('deals');
	  $this->load->library('encrypt');
	  $this->load->library('email');
	 $newdata = array('language'  => $langId);
	 $this->session->set_userdata($newdata);
	  
	/* $newdata = array('language'  => $langId);
	  $this->session->set_userdata($newdata);*/
	  
	  $content['base_course']=$this->course;
	  $content['country']=$this->user_model->get_country();
	  $content['states']=$this->user_model->get_states();
	  $content['course']=$this->user_model->get_course($this->language);
	  
		if(isset($_POST['submit']))
		{
			
			//echo "<pre>";print_r($_POST);exit;
		    $content['vCode'] = $this->input->post('vCode');
			$content['secure'] = $this->input->post('secure');
		    $content['secure_pdf'] = $this->input->post('secure_pdf');
			$content['is_req'] = $this->input->post('is_req');
		    
			$this->form_validation->set_rules('vCode', 'Vouchercode', 'trim|required');
			if($content['is_req']=='1'){
			$this->form_validation->set_rules('secure', 'Security password', 'trim|required');
			//$this->form_validation->set_rules('secure_pdf', 'Security pdf', 'trim|required');
			}
			
			//echo "<br>befor form validation : <pre>";print_r($content);
			if($this->form_validation->run())
			{
			//echo "<br>after  form validation : <pre>";print_r($content);
				$sessArray['vCode'] = $this->input->post('vCode');
				if($content['is_req']=='1'){
					
				$config['upload_path'] = 'public/uploads/deals/pdf/';
				$config['allowed_types'] = 'gif|jpg|png|pdf';
				$config['max_size']	= '7000';
				//$config['max_width']  = '1024';
				//$config['max_height']  = '768';
				
				
				$this->load->library('upload', $config);
				$this->upload->initialize($config);
		
				
				if ( $this->upload->do_upload('secure_pdf'))
				{
					$sessArray['uploaded'] = array('upload_data' => $this->upload->data());
					
				}
				else
				{
					$error['upResult'] = array('error' => $this->upload->display_errors());
					$this->session->set_flashdata('email_msg', $error['upResult']['error']);
					if($langId==4)
					redirect('start');
					else
					redirect('start_es');
					//echo "err condition<pre>";var_dump($error['upResult']);exit;
				}
				
				
				if($this->gift_voucher_model->securityValidation($content['vCode'])==1)
				{
					$sessArray['is_req'] = 1;
					$sessArray['secure'] = $this->input->post('secure');
				}
				else
				{
					/*$secVal = $this->gift_voucher_model->securityValidation($content['vCode']);
					echo $content['vCode']." ".$secVal;
					exit;*/
					if($langId==4)
					redirect('start');
					else
					redirect('start_es');
					
				}
					
				
				
				}
				else
				{
					if($this->gift_voucher_model->securityValidation($content['vCode'])==1)
					{
						$sessArray['is_req'] = 0;
					}
				}
				$this->session->set_userdata('deals', $sessArray);
				
       			
				
			  $this->session->set_flashdata('email_msg', 'Vouchercode accepted');
              redirect('deals_2', 'refresh');
			}
			
			
			
			
			//echo "exit on $ _POST end ";exit;
		}
		
		
		if(isset($_POST['fname']))
		{
		  $this->load->model('email_model');
		  $userdata  = array();
		  $userdata['first_name'] = $content['fname'] = $this->input->post('fname');
		  $userdata['last_name'] = $content['lname'] = $this->input->post('lname');
		  $userdata['email'] = $content['email'] = $this->input->post('email');
		  $userdata['email'] = $content['email'] = $this->input->post('email');
		  $userdata['contact_no'] = $content['contact_no'] = $this->input->post('contact_no');
		  $userdata['country_id'] = $content['country_set'] = $this->input->post('country_id');
		  $userdata['course_id'] = $content['course_set'] = $this->input->post('course_id');
		  $userdata['v_expiry'] = $content['v_expiry'] = $this->input->post('v_expiry');
		  $userdata['v_buy_date'] = $content['v_buy_date'] = $this->input->post('v_buy_date');
		  $userdata['v_website'] = $content['v_website'] = $this->input->post('v_website');
          if($userdata['country_id']=='12'){
		  $us_states= $content['state_set'] = $this->input->post('state');
		  }
		  if(isset($us_states)&& $us_states!=''){
			 
			 $state_details =$this->user_model->get_statename($us_states);
			  foreach($state_details as $row_states){
				 $userdata['us_states']=$row_states->name_short;   
			  }
			 
		  }	
		  $this->form_validation->set_rules('fname', 'First name', 'trim|required');
		  $this->form_validation->set_rules('lname', 'Last Name', 'required');
		  $this->form_validation->set_rules('country_id', 'Country', 'requered');
		  $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email');

		

		  $this->form_validation->set_rules('contact_no', 'Contact-No', 'required');
		  $this->form_validation->set_rules('v_expiry', 'Expirity date ', 'required');
		  $this->form_validation->set_rules('v_buy_date', 'Estimated date of buying the voucher code', 'required');
		  $this->form_validation->set_rules('v_website','On what website did you buy voucher', 'required');
				
	 //echo "<pre>";print_r($_POST);
		 
		  if($this->form_validation->run())
		  {
			 // echo "<pre>";print_r($_POST);exit;
			    $config['upload_path'] = 'public/uploads/deals/complaints/';
				$config['allowed_types'] = 'gif|jpg|png|pdf';
				$config['max_size']	= '10000';
				//$config['max_width']  = '1024';
				//$config['max_height']  = '768';
				
				
				$this->load->library('upload', $config);
				$this->upload->initialize($config);
		
				
				if ( $this->upload->do_upload('pdf'))
				{
					$uploaded = array('upload_data' => $this->upload->data());
					$pdf_name = $uploaded['upload_data']['file_name'];
					
					
				}
				else
				{
					$error['upResult'] = array('error' => $this->upload->display_errors());
					
					 $this->session->set_flashdata('email_msg', $error['upResult']['error']);
					if($langId==4)
					redirect('start');
					else
					redirect('start_es');
				}
			  
			  
			  $newResult = $this->email_model->getTemplateById('voucher_not_found',$this->session->userdata('language'));//get conttact us mail template 
				if(!empty($newResult))
				{
					foreach($newResult as $row1)
					{
						$mailContent = $row1->mail_content;
						$subject =  $row1->mail_subject;
						$mailing_template_id=$row1->id;
					}
				}
			 $course_name = $this->common_model->get_course_name($content['course_set']);
			 $country_name = $this->user_model->get_country_name($content['country_set']);
		
		$mailContent = str_replace("#FirstName#",$content['fname'],$mailContent);
		$mailContent = str_replace("#surname#",$content['lname'],$mailContent);
		$mailContent = str_replace("#email#",$content['email'],$mailContent);
		//$mailContent = str_replace("#callTime#",$content['call'],$mailContent);
		//$mailContent = str_replace("#title#",$content['title'],$mailContent);
		$mailContent = str_replace("#phone#",$content['contact_no'],$mailContent);
		$mailContent = str_replace("#course#",$course_name,$mailContent);
		$mailContent = str_replace("#country#",$country_name,$mailContent);
		$mailContent = str_replace("#expiry_date#",$content['v_expiry'],$mailContent);
		$mailContent = str_replace("#buy_date#",$content['v_buy_date'],$mailContent);
		$mailContent = str_replace("#voucherweb#",$content['v_website'],$mailContent);
		$mailContent = str_replace("#pdf_link#",base_url()."public/uploads/deals/complaints/".$pdf_name."",$mailContent);

				
				
				//$tomail = 'deeputg1992@gmail.com';
				$from = "mailer@eventtrix.com";
				$tomail = 'info@eventtrix.com';
				//$from = $content['email'];
				//$subject = $mailContent['subject'];	
				//echo $tomail."<Br>";echo $from."<Br>";echo $subject."<Br>".$mailContent;exit;
						   	
					  $this->email->from($from); 
					  $this->email->to($tomail); 
					  $this->email->cc(''); 
					  $this->email->bcc(''); 
					  
					  $this->email->subject($subject);
					  $this->email->message($mailContent);	
					  
					  if($this->email->send()){
					   $this->session->set_flashdata('email_msg', 'Your mail hasbeen send to admin.');
					 $content[] ="";
					  }
					else
					{
						$this->session->set_flashdata('email_msg', 'Your mail couldn\'t send now.Please try again later.');
						 
					}
				
			
			if($langId==4)
					redirect('start');
					else
					redirect('start_es');
		
			}
		}
		
		if(isset($_POST['email_sub']))
		{
			
			$email = $this->input->post('deals_email');
			$user_details = $this->user_model->getUserByEmail($email);
			$reddemed_coupon_details = $this->gift_voucher_model->get_redeemedCoupon($this->input->post('vCode'));
			//echo "<br>--------------<pre>";print_r($reddemed_coupon_details);print_r($user_details);exit;
			
			
				
			if(empty($user_details))
			{
				
				$this->session->set_flashdata('email_msg',"Unfortunately we do not have account associated with this email address. Please email us your full name and PDF copy of your voucher to info@eventtrix.com");
				
				if($langId==4)
					redirect('start');
					else
					redirect('start_es');
				
			}
			else
			{
				
				if($user_details[0]->user_id==$reddemed_coupon_details->user_id)
			{
				$user_course_arr = $this->user_model->check_user_registered($user_details[0]->user_id,$reddemed_coupon_details->course_id);
				
				
				if(empty($user_course_arr))
				{
					$this->session->set_flashdata('email_msg',"Unfortunately this voucher code doesn't matches with your account. Please email us your full name and PDF copy of your voucher to info@eventtrix.com");
				
					if($langId==4)
					redirect('start');
					else
					redirect('start_es');
				}
				else
				{
				if($user_details[0]->status==0)
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
							$mailContent = str_replace ( "#firstname#",$user_details[0]->first_name, $mailContent );
							$mailContent = str_replace ( "#click here#","<a href='".base_url()."home/studentActivation/".$user_details[0]->user_id."'>click here</a>", $mailContent );
							
							$mailContent = str_replace ( "#actlink#","".base_url()."/home/studentActivation/".$user_details[0]->user_id."", $mailContent  );
							
								$mailContent = str_replace ( "#url#", "<a href='".base_url()."'>Eventtrix</a>", $mailContent );
								$mailContent = str_replace ( "#username#", $user_details[0]->username, $mailContent );
								$mailContent = str_replace ( "#password#", $this->encrypt->decode($user_details[0]->password), $mailContent );
			
						  
						  
						$tomail = $user_details[0]->email;
							
						//echo"<br>----------------entered in user exsist but not active section<pre>";print_r($mailContent);exit;
									
							  $this->email->from('info@eventtrix.com', 'Team EventTrix');
							  $this->email->to($tomail); 
							  $this->email->cc(''); 
							  $this->email->bcc(''); 
							  
							  $this->email->subject($emailSubject);
							  $this->email->message($mailContent);	
							  
							$sent=$this->email->send();
				  if($sent==TRUE){
				  $mailing_histrory=array();
				  $mailing_histrory['email_id']=$tomail;
				  $mailing_histrory['user_id']=$user_details[0]->user_id;
				  $mailing_histrory['template_id']=$mailing_template_id;
				  $mailing_histrory['mailing_date']=date("Y-m-d");
				  $this->common_model->add_email_history($mailing_histrory);
				  }
				}
				else
				{
					$dateNow = date('Y-m-d');
				 // echo $dateNow."<br>".$user_course_arr[0]->date_expiry;
				  
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
						  
					 // echo"<br>----------------entered in user exist and active but not expired  section<pre>";print_r($mailContent);exit;
								  
							$this->email->from('info@eventtrix.com', 'Team EventTrix');
							$this->email->to($tomail); 
							$this->email->cc(''); 
							$this->email->bcc(''); 
							
							$this->email->subject($emailSubject);
							$this->email->message($mailContent);	
							
						   $sent=$this->email->send();
				  if($sent==TRUE){
				  $mailing_histrory=array();
				  $mailing_histrory['email_id']=$tomail;
				  $mailing_histrory['user_id']=$user_details[0]->user_id;
				  $mailing_histrory['template_id']=$mailing_template_id;
				  $mailing_histrory['mailing_date']=date("Y-m-d");
				  $this->common_model->add_email_history($mailing_histrory);
				  }
						   
						   //$day_remain = $dateNow - $user_course_arr[0]->date_expiry ;
						   
						  
						   $day_remain = $this->count_days(strtotime($dateNow),strtotime($user_course_arr[0]->date_expiry));
						   //echo $day_remain;exit;
						   $this->session->set_flashdata('email_msg',"We have just sent you an email with your account details. Please note you have activated your course on ".$user_course_arr[0]->date_enrolled." and therefore your remaining study time is ".$day_remain." days. 'If you need more time to complete your course don't forget you can extend your access at any time by going to the Extend Course link in your Virtual Campus");
				redirect('start');
						   
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
						  
					  //echo"<br>----------------entered in user exist and active but  expired  section<pre>";print_r($mailContent);exit;
								  
							$this->email->from('info@eventtrix.com', 'Team EventTrix');
							$this->email->to($tomail); 
							$this->email->cc(''); 
							$this->email->bcc(''); 
							
							$this->email->subject($emailSubject);
							$this->email->message($mailContent);	
							
						    $sent=$this->email->send();
				  if($sent==TRUE){
				  $mailing_histrory=array();
				  $mailing_histrory['email_id']=$tomail;
				  $mailing_histrory['user_id']=$user_details[0]->user_id;
				  $mailing_histrory['template_id']=$mailing_template_id;
				  $mailing_histrory['mailing_date']=date("Y-m-d");
				  $this->common_model->add_email_history($mailing_histrory);
				  }
						  $this->session->set_flashdata('email_msg',"We have just sent you an email with your account details. Please note you have activated your course on ".$user_course_arr[0]->date_enrolled." and therefore your course has now expired. If you would like to extend your course please go to Extend Course in your Virtual Campus. 
					");
				redirect('start'); 
						   
					}
				
				}
				}
						
				
			}
			else
			{
				$this->session->set_flashdata('email_msg',"Unfortunately this voucher code doesn't matches with your account. Please email us your full name and PDF copy of your voucher to info@eventtrix.com");
				redirect('start');
			}
			}
			
			
		}
		
			  
		$top_menu_base_courses = $this->user_model->get_courses($this->language);	
		
		if(empty($top_menu_base_courses))
		{
			$top_menu_base_courses = $this->user_model->get_courses(4);	
		}			
		$content['top_menu_base_courses'] 			= $top_menu_base_courses;
			
			
	  $content['tr_deals']  = $this->user_model->translate_('deals!');
	  $content['tr_voucher_validate']  = $this->user_model->translate_('validate_my_voucher');
	  $content['tr_voucher_accepted']  = $this->user_model->translate_('your_gift_voucher_accepted');
	  $content['tr_your_vcode_not_found']  = $this->user_model->translate_('deals!');
	  $content['tr_first_name']   =$this->user_model->translate_('First_Name'); 
	  $content['tr_last_name']   =$this->user_model->translate_('last_Name'); 		
	  $content['tr_email']   =$this->user_model->translate_('email'); 
	  $content['tr_contact_num']   =$this->user_model->translate_('contact_num'); 
	  $content['tr_country']   =$this->user_model->translate_('Country');
	  
	  $content['tr_course']   =$this->user_model->translate_('course');
	  $content['tr_voucher_ex_date']   =$this->user_model->translate_('tr_voucher_ex_date');
	  $content['tr_voucher_estimate_date']   =$this->user_model->translate_('tr_voucher_estimate_date');
	  $content['tr_what_website']   =$this->user_model->translate_('tr_what_website');
	  $content['tr_please_upload_pdf']   =$this->user_model->translate_('tr_please_upload_pdf');
	  
	  $content['tr_your_vouchercode'] =$this->user_model->translate_('tr_your_vouchercode');
	  $content['tr_security_code'] =$this->user_model->translate_('tr_security_code');
	  $content['tr_upload_pdf'] =$this->user_model->translate_('tr_upload_pdf');
      $content['tr_deals_send'] =$this->user_model->translate_('tr_deals_send');
	 
	  
	  
	   			
	   
	  $content['tr_vcode']  = $this->user_model->translate_('Please enter your voucher code here:');
	  $content['tr_submit']  = $this->user_model->translate_('submit');
	  $content['tr_getting_started_info']  = $this->user_model->translate_('tr_getting_started_info');
	  $content['langId']  = $langId;
	  
	  $content['translate'] = $this->tr_common;
	  $content['view']  = "deals";
	  $this->load->view('user/deals',$content);
  }	
	
	function deals($langId=4)
  	{
	  $this->session->unset_userdata('deals');
	  $this->load->library('encrypt');
	  $this->load->library('email');
	 $newdata = array('language'  => $langId);
	 $this->session->set_userdata($newdata);
	  
	/* $newdata = array('language'  => $langId);
	  $this->session->set_userdata($newdata);*/
	  
	  $content['base_course']=$this->course;
	  $content['country']=$this->user_model->get_country();
	  $content['states']=$this->user_model->get_states();
	  $content['course']=$this->user_model->get_course($this->language);
	  
		if(isset($_POST['submit']))
		{
			
			//echo "<pre>";print_r($_POST);exit;
		    $content['vCode'] = $this->input->post('vCode');
			$content['secure'] = $this->input->post('secure');
		    $content['secure_pdf'] = $this->input->post('secure_pdf');
			$content['is_req'] = $this->input->post('is_req');
		    
			$this->form_validation->set_rules('vCode', 'Vouchercode', 'trim|required');
			if($content['is_req']=='1'){
			$this->form_validation->set_rules('secure', 'Security password', 'trim|required');
			//$this->form_validation->set_rules('secure_pdf', 'Security pdf', 'trim|required');
			}
			
			//echo "<br>befor form validation : <pre>";print_r($content);
			if($this->form_validation->run())
			{
			//echo "<br>after  form validation : <pre>";print_r($content);
				$sessArray['vCode'] = $this->input->post('vCode');
				if($content['is_req']=='1'){
					
				$config['upload_path'] = 'public/uploads/deals/pdf/';
				$config['allowed_types'] = 'gif|jpg|png|pdf';
				$config['max_size']	= '7000';
				//$config['max_width']  = '1024';
				//$config['max_height']  = '768';
				
				
				$this->load->library('upload', $config);
				$this->upload->initialize($config);
		
				
				if ( $this->upload->do_upload('secure_pdf'))
				{
					$sessArray['uploaded'] = array('upload_data' => $this->upload->data());
					
				}
				else
				{
					$error['upResult'] = array('error' => $this->upload->display_errors());
					$this->session->set_flashdata('email_msg', $error['upResult']['error']);
					if($langId==4)
					redirect('start');
					else
					redirect('start_es');
					//echo "err condition<pre>";var_dump($error['upResult']);exit;
				}
				
				
				if($this->gift_voucher_model->securityValidation($content['vCode'])==1)
				{
					$sessArray['is_req'] = 1;
					$sessArray['secure'] = $this->input->post('secure');
				}
				else
				{
					/*$secVal = $this->gift_voucher_model->securityValidation($content['vCode']);
					echo $content['vCode']." ".$secVal;
					exit;*/
					if($langId==4)
					redirect('start');
					else
					redirect('start_es');
					
				}
					
				
				
				}
				else
				{
					if($this->gift_voucher_model->securityValidation($content['vCode'])==1)
					{
						$sessArray['is_req'] = 0;
					}
				}
				$this->session->set_userdata('deals', $sessArray);
				
       			
				
			  $this->session->set_flashdata('email_msg', 'Vouchercode accepted');
              redirect('deals_2', 'refresh');
			}
			
			
			
			
			//echo "exit on $ _POST end ";exit;
		}
		if(isset($_POST['fname']))
		{
		  $this->load->model('email_model');
		  $userdata  = array();
		  $userdata['first_name'] = $content['fname'] = $this->input->post('fname');
		  $userdata['last_name'] = $content['lname'] = $this->input->post('lname');
		  $userdata['email'] = $content['email'] = $this->input->post('email');
		  $userdata['email'] = $content['email'] = $this->input->post('email');
		  $userdata['contact_no'] = $content['contact_no'] = $this->input->post('contact_no');
		  $userdata['country_id'] = $content['country_set'] = $this->input->post('country_id');
		  $userdata['course_id'] = $content['course_set'] = $this->input->post('course_id');
		  $userdata['v_expiry'] = $content['v_expiry'] = $this->input->post('v_expiry');
		  $userdata['v_buy_date'] = $content['v_buy_date'] = $this->input->post('v_buy_date');
		  $userdata['v_website'] = $content['v_website'] = $this->input->post('v_website');
          if($userdata['country_id']=='12'){
		  $us_states= $content['state_set'] = $this->input->post('state');
		  }
		  if(isset($us_states)&& $us_states!=''){
			 
			 $state_details =$this->user_model->get_statename($us_states);
			  foreach($state_details as $row_states){
				 $userdata['us_states']=$row_states->name_short;   
			  }
			 
		  }	
		  $this->form_validation->set_rules('fname', 'First name', 'trim|required');
		  $this->form_validation->set_rules('lname', 'Last Name', 'required');
		  $this->form_validation->set_rules('country_id', 'Country', 'requered');
		  $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email');

		

		  $this->form_validation->set_rules('contact_no', 'Contact-No', 'required');
		  $this->form_validation->set_rules('v_expiry', 'Expirity date ', 'required');
		  $this->form_validation->set_rules('v_buy_date', 'Estimated date of buying the voucher code', 'required');
		  $this->form_validation->set_rules('v_website','On what website did you buy voucher', 'required');
				
	 //echo "<pre>";print_r($_POST);
		 
		  if($this->form_validation->run())
		  {
			 // echo "<pre>";print_r($_POST);exit;
			    $config['upload_path'] = 'public/uploads/deals/complaints/';
				$config['allowed_types'] = 'gif|jpg|png|pdf';
				$config['max_size']	= '10000';
				//$config['max_width']  = '1024';
				//$config['max_height']  = '768';
				
				
				$this->load->library('upload', $config);
				$this->upload->initialize($config);
		
				
				if ( $this->upload->do_upload('pdf'))
				{
					$uploaded = array('upload_data' => $this->upload->data());
					$pdf_name = $uploaded['upload_data']['file_name'];
					
					
				}
				else
				{
					$error['upResult'] = array('error' => $this->upload->display_errors());
					
					 $this->session->set_flashdata('email_msg', $error['upResult']['error']);
					if($langId==4)
					redirect('start');
					else
					redirect('start_es');
				}
			  
			  
			  $newResult = $this->email_model->getTemplateById('voucher_not_found',$this->session->userdata('language'));//get conttact us mail template 
				if(!empty($newResult))
				{
					foreach($newResult as $row1)
					{
						$mailContent = $row1->mail_content;
						$subject =  $row1->mail_subject;
						$mailing_template_id=$row1->id;
					}
				}
			 $course_name = $this->common_model->get_course_name($content['course_set']);
			 $country_name = $this->user_model->get_country_name($content['country_set']);
		
		$mailContent = str_replace("#FirstName#",$content['fname'],$mailContent);
		$mailContent = str_replace("#surname#",$content['lname'],$mailContent);
		$mailContent = str_replace("#email#",$content['email'],$mailContent);
		//$mailContent = str_replace("#callTime#",$content['call'],$mailContent);
		//$mailContent = str_replace("#title#",$content['title'],$mailContent);
		$mailContent = str_replace("#phone#",$content['contact_no'],$mailContent);
		$mailContent = str_replace("#course#",$course_name,$mailContent);
		$mailContent = str_replace("#country#",$country_name,$mailContent);
		$mailContent = str_replace("#expiry_date#",$content['v_expiry'],$mailContent);
		$mailContent = str_replace("#buy_date#",$content['v_buy_date'],$mailContent);
		$mailContent = str_replace("#voucherweb#",$content['v_website'],$mailContent);
		$mailContent = str_replace("#pdf_link#",base_url()."public/uploads/deals/complaints/".$pdf_name."",$mailContent);

				
				
				//$tomail = 'deeputg1992@gmail.com';
				$from = "mailer@eventtrix.com";
				$tomail = 'info@eventtrix.com';
				//$from = $content['email'];
				//$subject = $mailContent['subject'];	
				//echo $tomail."<Br>";echo $from."<Br>";echo $subject."<Br>".$mailContent;exit;
						   	
					  $this->email->from($from); 
					  $this->email->to($tomail); 
					  $this->email->cc(''); 
					  $this->email->bcc(''); 
					  
					  $this->email->subject($subject);
					  $this->email->message($mailContent);	
					  
					  if($this->email->send()){
					   $this->session->set_flashdata('email_msg', 'Your mail hasbeen send to admin.');
					 $content[] ="";
					  }
					else
					{
						$this->session->set_flashdata('email_msg', 'Your mail couldn\'t send now.Please try again later.');
						 
					}
				
			
			if($langId==4)
					redirect('start');
					else
					redirect('start_es');
		
			}
		}
		if(isset($_POST['email_sub']))
		{
			
			$email = $this->input->post('deals_email');
			$user_details = $this->user_model->getUserByEmail($email);
			$reddemed_coupon_details = $this->gift_voucher_model->get_redeemedCoupon($this->input->post('vCode'));
			//echo "<br>--------------<pre>";print_r($reddemed_coupon_details);print_r($user_details);exit;
			
			
				
			if(empty($user_details))
			{
				
				$this->session->set_flashdata('email_msg',"Unfortunately we do not have account associated with this email address. Please email us your full name and PDF copy of your voucher to info@eventtrix.com");
				
				if($langId==4)
					redirect('start');
					else
					redirect('start_es');
				
			}
			else
			{
				
				if($user_details[0]->user_id==$reddemed_coupon_details->user_id)
			{
				$user_course_arr = $this->user_model->check_user_registered($user_details[0]->user_id,$reddemed_coupon_details->course_id);
				
				
				if(empty($user_course_arr))
				{
					$this->session->set_flashdata('email_msg',"Unfortunately this voucher code doesn't matches with your account. Please email us your full name and PDF copy of your voucher to info@eventtrix.com");
				
					if($langId==4)
					redirect('start');
					else
					redirect('start_es');
				}
				else
				{
				if($user_details[0]->status==0)
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
							$mailContent = str_replace ( "#firstname#",$user_details[0]->first_name, $mailContent );
							$mailContent = str_replace ( "#click here#","<a href='".base_url()."home/studentActivation/".$user_details[0]->user_id."'>click here</a>", $mailContent );
							
							$mailContent = str_replace ( "#actlink#","".base_url()."/home/studentActivation/".$user_details[0]->user_id."", $mailContent  );
							
								$mailContent = str_replace ( "#url#", "<a href='".base_url()."'>Eventtrix</a>", $mailContent );
								$mailContent = str_replace ( "#username#", $user_details[0]->username, $mailContent );
								$mailContent = str_replace ( "#password#", $this->encrypt->decode($user_details[0]->password), $mailContent );
			
						  
						  
						$tomail = $user_details[0]->email;
							
						//echo"<br>----------------entered in user exsist but not active section<pre>";print_r($mailContent);exit;
									
							  $this->email->from('info@eventtrix.com', 'Team EventTrix');
							  $this->email->to($tomail); 
							  $this->email->cc(''); 
							  $this->email->bcc(''); 
							  
							  $this->email->subject($emailSubject);
							  $this->email->message($mailContent);	
							  
							$sent=$this->email->send();
				  if($sent==TRUE){
				  $mailing_histrory=array();
				  $mailing_histrory['email_id']=$tomail;
				  $mailing_histrory['user_id']=$user_details[0]->user_id;
				  $mailing_histrory['template_id']=$mailing_template_id;
				  $mailing_histrory['mailing_date']=date("Y-m-d");
				  $this->common_model->add_email_history($mailing_histrory);
				  }
				}
				else
				{
					$dateNow = date('Y-m-d');
				 // echo $dateNow."<br>".$user_course_arr[0]->date_expiry;
				  
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
						  
					 // echo"<br>----------------entered in user exist and active but not expired  section<pre>";print_r($mailContent);exit;
								  
							$this->email->from('info@eventtrix.com', 'Team EventTrix');
							$this->email->to($tomail); 
							$this->email->cc(''); 
							$this->email->bcc(''); 
							
							$this->email->subject($emailSubject);
							$this->email->message($mailContent);	
							
						   $sent=$this->email->send();
				  if($sent==TRUE){
				  $mailing_histrory=array();
				  $mailing_histrory['email_id']=$tomail;
				  $mailing_histrory['user_id']=$user_details[0]->user_id;
				  $mailing_histrory['template_id']=$mailing_template_id;
				  $mailing_histrory['mailing_date']=date("Y-m-d");
				  $this->common_model->add_email_history($mailing_histrory);
				  }
						   
						   //$day_remain = $dateNow - $user_course_arr[0]->date_expiry ;
						   
						  
						   $day_remain = $this->count_days(strtotime($dateNow),strtotime($user_course_arr[0]->date_expiry));
						   //echo $day_remain;exit;
						   $this->session->set_flashdata('email_msg',"We have just sent you an email with your account details. Please note you have activated your course on ".$user_course_arr[0]->date_enrolled." and therefore your remaining study time is ".$day_remain." days. 'If you need more time to complete your course don't forget you can extend your access at any time by going to the Extend Course link in your Virtual Campus");
				redirect('start');
						   
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
						  
					  //echo"<br>----------------entered in user exist and active but  expired  section<pre>";print_r($mailContent);exit;
								  
							$this->email->from('info@eventtrix.com', 'Team EventTrix');
							$this->email->to($tomail); 
							$this->email->cc(''); 
							$this->email->bcc(''); 
							
							$this->email->subject($emailSubject);
							$this->email->message($mailContent);	
							
						    $sent=$this->email->send();
				  if($sent==TRUE){
				  $mailing_histrory=array();
				  $mailing_histrory['email_id']=$tomail;
				  $mailing_histrory['user_id']=$user_details[0]->user_id;
				  $mailing_histrory['template_id']=$mailing_template_id;
				  $mailing_histrory['mailing_date']=date("Y-m-d");
				  $this->common_model->add_email_history($mailing_histrory);
				  }
						  $this->session->set_flashdata('email_msg',"We have just sent you an email with your account details. Please note you have activated your course on ".$user_course_arr[0]->date_enrolled." and therefore your course has now expired. If you would like to extend your course please go to Extend Course in your Virtual Campus. 
					");
				redirect('start'); 
						   
					}
				
				}
				}
						
				
			}
			else
			{
				$this->session->set_flashdata('email_msg',"Unfortunately this voucher code doesn't matches with your account. Please email us your full name and PDF copy of your voucher to info@eventtrix.com");
				redirect('start');
			}
			}
			
			
		}
		
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
		
		$this->tr_common['tr_v_code']   =$this->user_model->translate_('v_code'); 	
		$this->tr_common['tr_voucher_code_note_text']   =$this->user_model->translate_('voucher_code_note_text'); 		
		$this->tr_common['tr_course']   =$this->user_model->translate_('course'); 	
		  
		$top_menu_base_courses = $this->user_model->get_courses($this->language);	
		
		if(empty($top_menu_base_courses))
		{
			$top_menu_base_courses = $this->user_model->get_courses(4);	
		}			
		$content['top_menu_base_courses'] 			= $top_menu_base_courses;
			
			
	  $content['tr_deals']  = $this->user_model->translate_('deals!');
	  $content['tr_voucher_validate']  = $this->user_model->translate_('validate_my_voucher');
	  $content['tr_voucher_accepted']  = $this->user_model->translate_('your_gift_voucher_accepted');
	  $content['tr_your_vcode_not_found']  = $this->user_model->translate_('deals!');
	  $content['tr_first_name']   =$this->user_model->translate_('First_Name'); 
	  $content['tr_last_name']   =$this->user_model->translate_('last_Name'); 		
	  $content['tr_email']   =$this->user_model->translate_('email'); 
	  $content['tr_contact_num']   =$this->user_model->translate_('contact_num'); 
	  $content['tr_country']   =$this->user_model->translate_('Country');
	  
	  $content['tr_course']   =$this->user_model->translate_('course');
	  $content['tr_voucher_ex_date']   =$this->user_model->translate_('tr_voucher_ex_date');
	  $content['tr_voucher_estimate_date']   =$this->user_model->translate_('tr_voucher_estimate_date');
	  $content['tr_what_website']   =$this->user_model->translate_('tr_what_website');
	  $content['tr_please_upload_pdf']   =$this->user_model->translate_('tr_please_upload_pdf');
	  
	  $content['tr_your_vouchercode'] =$this->user_model->translate_('tr_your_vouchercode');
	  $content['tr_security_code'] =$this->user_model->translate_('tr_security_code');
	  $content['tr_upload_pdf'] =$this->user_model->translate_('tr_upload_pdf');
      $content['tr_deals_send'] =$this->user_model->translate_('tr_deals_send');
	 
	  
	  
	   			
	   
	  $content['tr_vcode']  = $this->user_model->translate_('Please enter your voucher code here:');
	  $content['tr_submit']  = $this->user_model->translate_('submit');
	  $content['tr_getting_started_info']  = $this->user_model->translate_('tr_getting_started_info');
	  $content['langId']  = $langId;
	  
	  $content['translate'] = $this->tr_common;
	  $data['view']  = "deals";
	  $data['content'] = $content;
	  $this->load->view('user/template_outer',$data);
  }
  
	function withCoupon($id)
	{
		//echo "<pre>";print_r($this->session->userdata['deals']);exit;
		$this->load->model('gift_voucher_model');
		$this->load->model('course_model');
		$pre_user_id = $id;
		
		$temp_course = $this->uri->segment(4);
		
		if(!isset($this->session->userdata['deals']))
		{
			redirect("start","refresh");
		}
		$vouchercode = $this->session->userdata['deals']['vCode'];
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
				$studentdata['reg_date'] = $dateNow;
				$studentdata['lang_id'] = $langId;
				$studentdata['reg_type'] = 'voucher_deals';
				$studentdata['website_id'] = $voucherDetails[0]->website_id;
				$content['coupon_code'] = $row->coupon_code;
				$content['redemption_code'] = $row->redemption_code;
				
			}
			$repeate_stat = $this->gift_voucher_model->email_username_check($studentdata['email'],$studentdata['username'],$id);
			if($repeate_stat==0)
			{
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
			$unsubscribed['last_name']=$last_name;
			$unsubscribed['email']=$email;
			$country_id=$this->common_model->get_country_id_BYname($this->con_name);
			$unsubscribed['country']=$country_id;
			$unsubscribed['user']="yes";
			//$unsubscribed['sourse']="student";
			$unsubscribed['subscribed']=$newsletter;
			$table="newsletter";
		   $newsletter_details=$this->campaign_model->fetch_user_details_byEmail($email,$table);
		  // echo "<pre>";print_r($newsletter_details);exit;
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
			$newsletter_array['last_name']=$last_name;
			$newsletter_array['email']=$email;
			$country_id=$this->common_model->get_country_id_BYname($this->con_name);
			$newsletter_array['country']=$country_id;
			$newsletter_array['subscribed']=$newsletter;
			$newsletter_array['user']="yes";
			$newsletter_array['sourse']="student";
			$newsletter_array['date']=date("Y-m-d");
			$this->common_model->add_newsletter($newsletter_array);
		 }
		//**********************************end newsletter updation ***************************************
			}
			else
			{?>
<script>
window.location.href = '<?=base_url()?>home/couponSuccess';
</script>
			<? exit;}
			
			
			
			
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
			$student_courseData['enroll_type'] = 'coupon';
			$student_courseData['course_status'] = '0';
			
			$courseEnrId[] = $this->user_model->add_course_student($student_courseData);
			}
			else
			{
				for($co=0;$co<count($courseId);$co++)
				{
					
					$usersUnit = $this->user_model->get_courseunits_id($courseId[$co]);
					$un = array();
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
				if($voucherDetails[0]->courses_idcourses==0||$voucherDetails[0]->courses_idcourses=="")
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
				$mailContent = str_replace ( "#url#", "<a href='".base_url()."'>EventTrix</a>", $mailContent );
				$mailContent = str_replace ( "#username#", $studentdata['username'], $mailContent );
				$mailContent = str_replace ( "#password#", $this->encrypt->decode($studentdata['password']), $mailContent );
	
				$tomail = $studentdata['email'];
					
				$this->email->from('info@eventtrix.com', 'Team EventTrix');
				$this->email->to($tomail); 
				$this->email->cc(''); 
				$this->email->bcc(''); 
				$this->email->subject($emailSubject);
				$this->email->message($mailContent);	
					  
				$sent=$this->email->send();
				if($sent==TRUE){
					$mailing_histrory = array();
					$mailing_histrory['email_id']=$tomail;
					$mailing_histrory['user_id']=$user_id;
					$mailing_histrory['template_id']=$mailing_template_id;
					$mailing_histrory['mailing_date']=date("Y-m-d");
					$this->db->insert('email_history',$mailing_histrory);
				}
				$this->common_model->deactivate_voucher_code($this->session->userdata['deals']['vCode']);
				$this->session->unset_userdata('deals');
					
				$cart_main_update_array = array("user_id"=>$user_id);								
				$this->sales_model->main_cart_details_update_user_id($this->session->userdata('cart_session_id'),$pre_user_id,$cart_main_update_array);
				
				$this->session->unset_userdata('cart_session_id');
				$this->session->unset_userdata('package_applying_course');			
				$this->session->unset_userdata('added_user_id');
				if($this->session->userdata('enrolling_rep_code'))
				$this->session->unset_userdata('enrolling_rep_code');
				
				$en_user_id = urlencode($this->encrypt->encode($user_id));
				redirect('home/couponSuccess/'.$en_user_id,'refresh');
			}
			
					
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
		  $coursePercentage1=@($completedMarks1/$completedMarks2)*100;
		  $coursePercentage=@round($coursePercentage1,2);
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
		
		
		
		return $progress;
		
	
	
	}
	
	
	
	
	function withCoupon_user()
	{
		//echo "<pre>";print_r($this->session->userdata['deals']);exit;
		$this->load->model('gift_voucher_model');
		$this->load->model('course_model');
		
		
		
		$vouchercode = $this->session->userdata['deals']['vCode'];
		//echo $vouchercode ;
		$content['coupon_code']=$vouchercode ;
		$voucherDetails = $this->gift_voucher_model->getDetails_of_vcode($vouchercode);
		if($voucherDetails[0]->courses_idcourses=="" || $voucherDetails[0]->courses_idcourses==0)
		{
			$content['course_set']=$this->input->post('course');
			$content['course_count']=0;
			$courseId[] =  $content['course_set'];
		}
		else
		{
			$courseId = explode(",",$voucherDetails[0]->courses_idcourses);
			$content['course_count']=count($courseId);
			
		}
		$user_id=$this->session->userdata['student_logged_in']['id'];
						
			$dateNow =date('Y-m-d');
			$langId = $this->course_model->get_lang_course($courseId[0]);
			
			//$newArr = $tempArray->row();
//echo "<pre>";print_r($courseId);exit;
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
				
				$webVoucherId = $this->gift_voucher_model->getVoucherWebIdByVcode($content['coupon_code']);
				
				$couponDetails=array();
				$couponDetails['user_id']=$user_id;
				$couponDetails['course_id']=$voucherDetails[0]->courses_idcourses;
				$couponDetails['coupon_code']=$content['coupon_code'];	
				if(isset($content['redemption_code']))
				{		
				$couponDetails['redemption_code']=$content['redemption_code'];
				$couponDetails['pdf_name']=$this->session->userdata['deals']['uploaded'];
				
				}
				$couponDetails['website_id']=$webVoucherId;
				$couponDetails['date']=$dateNow;
			
			$redeemedCoupenId = $this->user_model->add_redeemedCoupon($couponDetails);
			//$this->common_model->deactivate_voucher_code($vouchercode);
		
			$this->common_model->deactivate_voucher_code($this->session->userdata['deals']['vCode']);
			$this->session->unset_userdata('deals');
					  	
			
		redirect('home/couponSuccess','refresh');	
			
					
		}
		
		
		function extend_coupon($vouchercode)
		{			
			$voucherDetails = $this->gift_voucher_model->getDetails_of_vcode($vouchercode);
			$dateNow = date('Y-m-d');
			if(!empty($voucherDetails))
			{
				if($voucherDetails[0]->enddate<$dateNow && $voucherDetails[0]->extended_end_date<$dateNow)
				{
					$content['product_id']=$this->common_model->getProdectId('extend_voucher');
					$content['vouchercode']=$vouchercode;
					$feeDetails =$this->common_model->getProductFee($content['product_id'],$this->currId);
					$content['currency_code'] = $this->currencyCode;
					$content['curr_id'] = $this->currId;
					if(empty($feeDetails))
					{
						$feeDetails =$this->common_model->getProductFee($content['product_id'],1);
						$content['currency_code'] = "EUR";
						$content['curr_id'] = 1;
					}
					$content['product_fee'] = $feeDetails['amount'];
				}
				else
				{
					$this->session->set_flashdata('email_msg',"Your voucher not expired yet! Validate your voucher again to get offer.");
					redirect('start');
				}
			}
			else
			{
				$this->session->set_flashdata('email_msg',"Your voucher not expired yet! Validate your voucher again to get offer.");
				redirect('start');
			}
		
		$data['translate'] = $this->tr_common;
		$data['view'] = 'extend_coupon';
		$data['content'] = $content;
		$this->load->view('user/template_outer',$data);
	
	  
	 
		}
		
		function afterExtendVoucher()
	  {
		 $payment_id = $this->uri->segment(3);
		 $vouchercode = $this->uri->segment(4);
		 $voucher_extended_id = $this->uri->segment(5);
		  
		  $voucherDetails = $this->gift_voucher_model->getDetails_of_vcode($vouchercode);
	 // echo "<pre>";print_r($voucherDetails); echo "</pre>";exit;
		  $enddate = $voucherDetails[0]->extended_end_date;
		  
		  $today = date("Y-m-d");
		  $new_endDate = date("Y-m-d", strtotime("+3 months"));
		  
		  $up_voucher_pay_status = array("status"=>"paid");		 
		  $up_voucher = array("extended_end_date"=>$new_endDate);
		  
		  //update payers details
		  $this->db->where('id',$voucher_extended_id);
		  $this->db->update('giftvoucher_extend_user', $up_voucher_pay_status);
		 
		 //updating extended end date
		  $this->db->where('giftVoucherCode',$vouchercode);
		  $this->db->update('giftvoucher', $up_voucher);
		  
		  $this->session->set_flashdata('email_msg',"Thank you for extending your voucher. We are currently applying an extension. Your voucher will be extended within next 15 minutes. To register for the course please go to: www.eventtrix.com/start");
		    redirect('start');	  
		  
	  }
	  

		function count_days( $a, $b )
		{
			
			$gd_a = getdate( $a );
			$gd_b = getdate( $b );
				
			$a_new = mktime( 12, 0, 0, $gd_a['mon'], $gd_a['mday'], $gd_a['year'] );
			$b_new = mktime( 12, 0, 0, $gd_b['mon'], $gd_b['mday'], $gd_b['year'] );
		 
			return round( abs( $a_new - $b_new ) / 86400 );
		}
		
		function get_student_deatils_for_popup()
	{
	  $this->tr_common['tr_account_details']   =$this->user_model->translate_('account_details');	      
	  $this->tr_common['tr_first_name']   =$this->user_model->translate_('First_Name');	      
	  $this->tr_common['tr_email'] =$this->user_model->translate_('email');
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
		}		
		return $content;
	
	}
	function test_download()
	{
		$this->load->helper(array('dompdf', 'file'));
		$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Eventtrix-Proof-of-Enrolement</title>
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

body, html{margin:0; padding:0; font-family: "Open Sans"; color:#666; line-height:1.4em; background:#fff}
.outer{margin:0 auto; background:url(/public/user/certificate/images/Eventtrix-Proof-of-Enrolement.jpg) center center; width:820px; height:959px}
.innner{padding:12.5em 3em 2em 4em;}
p{padding:0.4em 0.5em; font-size:11pt; margin:0}
ul{margin:0; padding:0.8em 0.7em 0.3em 1.5em; font-size:11pt;}
ul li{padding:0.2em}
</style>


</head>

<body>
<div class="outer">
<div class="innner">
<p>To whom it may concern,</p>
<p>We confirm that deepu is a student of EventTrix – Event Management Online Training
and has enrolled to our coursename course.</p>
<p>The course consists cousehourses online study hours and included study content, exercises and exams.
’s expected date of course completion is . This date may be extended if
extra time is needed to complete study.</p>
<!--<p>Principles of Event Management & Roles of Event Manager</p>
<ul>
<li>Types of Events</li>
<li>Working with Clients incl. understanding client needs, preparing event proposals,
signing contracts</li>
<li>Steps for planning an Event incl. budgets, venues, food and beverages,
transportation, speakers</li>
<li>General Etiquette and Protocol incl. invitations, dress codes, table settings and
seating arrangements, greeting etiquette</li>
<li>Day of the Event and Post Event Evaluation</li>
</ul>-->
<p>Topics covered by the course include:</p>
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
<p>We wish  ever success in completing the Event Management course and in her future career.</p>
<p>Kind Regards,</p>
</div>
</div>
</body>
</html>
';

 $data = pdf_create($html, 'course_enrollement');   
     write_file('name', $data);	
	}
	
	
	function apply_certificate_sales($course_id)
	  {
			  
			   $this->load->model('email_model','',TRUE);	  
				$user_id = $this->session->userdata['student_logged_in']['id'];	
					
				$this->user_model->insert_certificate_request($course_id,$user_id);
				
				$data=array("course_status"=>'4'); // change status to Certificate applied
				
				$this->user_model->update_student_enrollments($course_id,$user_id,$data);
				
				 $user_details = $this->user_model->get_student_details($user_id); 				
				//$user_details =  $this->student_model->get_student_byId($user_id);		
				foreach($user_details as $row)
				{		
					$tomail= $row->email;
					$user_name = $row->first_name;
					$lang_id = $row->lang_id;
				}		
				$course_name = $this->common_model->get_course_name($course_id); 		
				$mail_for = "cerificate_approved";
				$email_details = $this->email_model->getTemplateById($mail_for,$lang_id);		
				foreach($email_details as $row)
				{			
					$email_subject = $row->mail_subject;
					$mail_content = $row->mail_content;
					$mailing_template_id=$row1->id;
				}	
				
				$mail_content = str_replace ( "#first_name#", $user_name, $mail_content );		
				$mail_content .= str_replace ( "#course_name#", $course_name, $mail_content );
				//$mailContent = str_replace ( "#Details#", $path, $mailContent );
						
				$this->load->library('email');
			
				 $this->email->from('info@eventtrix.com', 'Team EventTrix');
				$this->email->to($tomail); 
				$this->email->cc(''); 
				$this->email->bcc(''); 		
				$this->email->subject($email_subject);
				$this->email->message($mail_content);			
				$sent=$this->email->send();
			  
			  if($sent==TRUE){
			  $mailing_histrory=array();
			  $mailing_histrory['email_id']=$tomail;
			  $mailing_histrory['user_id']=$user_id;
			  $mailing_histrory['template_id']=$mailing_template_id;
			  $mailing_histrory['mailing_date']=date("Y-m-d");
			  $this->common_model->add_email_history($mailing_histrory);
			  }	
			redirect('home/sales_course_completion/'.$course_id, 'refresh');	 
			  
			  
	  }
	  
	   function success_letter_hardcopy($product_id)
	  {
		
		
		$content = array();
		$content=$this->get_student_deatils_for_popup();
		
		$lang_id = $this->session->userdata('language');
		
		$success_note='';		
		  
		  $this->tr_common['tr_Best_regards']      =$this->user_model->translate_('Best_regards');
		  $this->tr_common['tr_TRENDIMI_Team']      =$this->user_model->translate_('TRENDIMI_Team');
		  $this->tr_common['tr_Payment_Success']      =$this->user_model->translate_('Payment_Success');
		 $postal_id = $this->certificate_model->get_postal_id($product_id); 
		 $postage_details = $this->certificate_model->get_postage_details($postal_id); 
		
			
			if($product_id==64) // proof of enrolemnt hard copy
			{
			   $success_note = $this->user_model->translate_('sales_purchase_note_proof_completion_hard');
			}
			else if($product_id==45)  // proof of completion hard copy
			{			
				$success_note = $this->user_model->translate_('sales_purchase_note_poe_hard');
			}
			else if($product_id==65)  // eTranscript hard copy
			{			
				$success_note = $this->user_model->translate_('sales_purchase_note_transcript');
			}
			  	
		$content['success_note'] = $success_note;
		$data['translate'] = $this->tr_common;
		$data['view'] = 'result_letter_hardcopy';   
		$data['content'] = $content;
		$this->load->view('user/template_inner',$data);
  
	}
	  
	  function change_package_applying_course_ajax($course_id)
	  {
		
		$course_name = ucwords($this->common_model->get_course_name($course_id)); 
		
		if(!$this->session->userdata('package_applying_course'))
		{
			$sess_array = array('package_applying_course' =>$course_id); 				
			$this->session->set_userdata($sess_array);	
		}
		else if($this->session->userdata('package_applying_course')!=$course_id)
		{
			$sess_array = array('package_applying_course' =>$course_id); 				
			$this->session->set_userdata($sess_array);	
		}
		$data['err_msg'] = 0;
		$data['msg']= 'Course changed to '.$course_name;			
		echo json_encode($data); 
		exit; 					
	 }
	 
	 function apply_products_prepaid($course_id,$product_type,$type)
	  {
			
			  $this->load->model('package_model','',TRUE);
			  if(!$this->session->userdata('student_logged_in')){
	 		 		redirect('home');
			  }
			 $user_id=$this->session->userdata['student_logged_in']['id']; 
			 
			  if($type=='hard')
			  {
				  
				  	$content = array();
					$student_update_data = array();
					$data =array();
					//$currency_id = $this->currId;
					//$currency_code = $this->currencyCode;	
					
					$content=$this->get_student_deatils_for_popup();
					
						 $stud_details=$this->user_model->get_stud_details($user_id);
						 
						 if(isset($_POST['confirm_address']))
						{			
							$student_update_data['house_number'] = $apartment  = $this->input->post('apartment');
							$student_update_data['street'] = $street  = $this->input->post('street');
							$student_update_data['address'] = $address1  = $this->input->post('address1');
							//$address2  = $this->input->post('address2');	
							$student_update_data['country_id'] = $country  = $this->input->post('country');
							$student_update_data['zipcode'] = $zip_code  = $this->input->post('zip_code');
							$student_update_data['city'] = $city  = $this->input->post('city');
							
							
							$this->form_validation->set_rules('apartment', 'Apartment/ House number', 'trim|required');
							$this->form_validation->set_rules('address1', 'Address 1', 'required');			
							$this->form_validation->set_rules('street', 'Street', 'required');
							$this->form_validation->set_rules('country', 'Country', 'required');
							$this->form_validation->set_rules('zip_code', 'Zip code', 'required');
							$this->form_validation->set_rules('city', 'City', 'required');	
							
						/*	echo "<br>Product id ".$product_id;
							echo "<br> Course id ".$course_id;
							exit;*/
							
							if($this->form_validation->run())
							{
								$this->user_model->update_student_details($student_update_data,$user_id);
								redirect('coursemanager/apply_hardcopy_icoes_prepaid/'.$course_id.'/'.$offer_id, 'refresh');
							}
							
												
						}		
						 
						
						 
						 foreach($stud_details as $val2)
						 {
							 $content['country_set'] = $val2->country_id;
						//	 $country_name = $this->user_model->get_country_name($val2->country_id);
							
							 $content['house_number'] = $val2->house_number;
							 $content['address'] = $val2->address;
							 $content['street'] = $val2->street;
							 $content['city'] = $val2->city;
							 $content['zip_code'] = $val2->zipcode;
							// $content['country_name'] = $country_name;
						 }
						$content['country'] = $this->user_model->get_country();
						
						
						
						$data['course_id']  	  = $course_id;	
						$data['product_type']   = $product_type;				
						
						$this->tr_common['tr_Apartment_House_number'] = $this->user_model->translate_('Apartment_House_number');
						
						$this->tr_common['tr_address_1'] = $this->user_model->translate_('address_1');
						$this->tr_common['tr_street'] = $this->user_model->translate_('road_street');
						$this->tr_common['tr_city'] = $this->user_model->translate_('city');
						$this->tr_common['tr_zip_code'] = $this->user_model->translate_('zip_code');
						$this->tr_common['tr_Country'] = $this->user_model->translate_('Country');
						$this->tr_common['tr_make_payment'] = $this->user_model->translate_('make_payment');
						$this->tr_common['tr_confirm_address_below'] = $this->user_model->translate_('confirm_address_below');
						$this->tr_common['tr_additional_adress_if'] = $this->user_model->translate_('additional_adress_if');
						$this->tr_common['tr_donot_duplicate_street_name'] = $this->user_model->translate_('donot_duplicate_street_name');
						$this->tr_common['tr_donot_close_browser'] = $this->user_model->translate_('donot_close_browser');
						$this->tr_common['tr_shipping_details']        =$this->user_model->translate_('shipping_details');
						
						
						$this->tr_common['tr_yes_confirm_addr'] = $this->user_model->translate_('yes_confirm_addr');
						$this->tr_common['tr_addr_not_confirm'] = $this->user_model->translate_('addr_not_confirm');
						$this->tr_common['tr_required'] = $this->user_model->translate_('required');
						
						
						
						
						$data['translate'] = $this->tr_common;
						$data['view'] 		   = 'prepaid_address_confirm';
						//$data['view'] 		   = 'hard_copy_payment';   
						$data['content']  		= $content;
						$this->load->view('user/course_template',$data);	
				  
			  }
			  else if($type=='soft')
			  {
				    $package_subscription_details = $this->package_model->get_package_sucbcriptions_user($user_id,$course_id);		
			   if(!empty($package_subscription_details))
			   {
				$package_sub_id = $package_subscription_details[0]->id;
				  $payment_id = $this->package_model->get_payament_id_package($user_id,$course_id);
				  $today = date("Y-m-d");
				  $insert_data=array("user_id"=>$user_id,"course_id"=>$course_id,"type"=>$product_type,"date_applied"=>$today);		 
				  $this->user_model->insertQuerys("user_subscriptions",$insert_data);
				  
				  $pak_sub_array = array('status'=>0);
				  $this->package_model->update_package_subscription_details($studentUserId,$cour_id,$package_sub_id,$product_type,$pak_sub_array);
			   }
				  
				   redirect('coursemanager/certificate');
				  
			  }
			  
		  }
	 function apply_prepaid_products_hard_copy($course_id,$product_type)
	  {
			  
			  $this->load->model('package_model','',TRUE);
			  $user_id=$this->session->userdata['student_logged_in']['id']; 				   
			  $package_subscription_details = $this->package_model->get_package_sucbcriptions_user($user_id,$course_id);
				 			  
			  if(!empty($package_subscription_details))
			  {
				  $packgae_sub_id = $package_subscription_details[0]->id;
				  $payment_id = $package_subscription_details[0]->payment_id;
			  }
			  else
			  {
				  redirect('sales/pacakge_error');
			  }
			  $product_id = $this->package_model->get_package_puchases_by_product_type($product_type,$user_id,$course_id,$packgae_sub_id);		
			  
			  $payment_id = $this->package_model->get_payament_id_package($user_id,$course_id);
			
			  $user_details = $this->user_model->get_student_details($user_id); 		  		  
			  foreach($user_details as $val2)
			  {		
				 $certificate_user_name	= $val2->first_name.' '.$val2->last_name;
				 $user_country_name = $this->user_model->get_country_name($val2->country_id);
				 $user_house_number = ucfirst(strtolower(trim($val2->house_number)));
				 $user_address 	  = ucfirst(strtolower(trim($val2->address)));
				 $user_street 	   = ucfirst(strtolower(trim($val2->street)));
				 $user_city 		 = ucfirst(strtolower(trim($val2->city)));
				 $user_zip_code     = $val2->zipcode;				 		 
			  }
			  
			  $certificate_user_name = strtolower($certificate_user_name);
			  $certificate_user_name = ucwords($certificate_user_name);						  
			  if(strpos($certificate_user_name, '\''))
			  {
					$certificate_user_name = preg_replace_callback("/'[a-z]/", function ($matches) {
					return strtoupper($matches[0]);
					}, $certificate_user_name);	
			  }
			  if(strpos($certificate_user_name, '-'))
			  {
				$certificate_user_name = preg_replace_callback("/-[a-z]/", function ($matches) {
				return strtoupper($matches[0]);
				}, $certificate_user_name);	
			  }
  
			   $today = date("Y-m-d");
			   
			  if($product_type=='transcript_hard')
			  {  
			  
			  $this->load->helper(array('dompdf', 'file'));
		   //   $userId = $this->session->userdata['student_logged_in']['id'];	
		   	
			
			
		     $insert_data=array("user_id"=>$user_id,"course_id"=>$course_id,"product_id"=>$product_id,"type"=>'transcript_hard',"date_applied"=>$today,"payment_id"=>$payment_id); 
			  $this->user_model->insertQuerys("user_subscriptions",$insert_data);
				  
			  $pak_sub_array = array('status'=>0);				
			  $this->package_model->update_package_subscription_details($user_id,$course_id,$packgae_sub_id,$product_type,$pak_sub_array);
		   
		   		 $html = $this->pdf_html_model->create_transcript_pdf($course_id);	
				 
		        $data = pdf_create($html, 'eTranscript_'.$user_id.'_'.$course_id,false);		
			
				//$data = pdf_create($html, '', false);	
				$this->path = "public/transcript/eTranscript_".$user_id."_".$course_id.".pdf";
				write_file($this->path, $data);
			 
				$student_data = $this->user_model->get_student_details($user_id);
				
				$user_country_name = $this->user_model->get_country_name($student_data[0]->country_id);
				
					 $this->load->library('email');
					 $tomail = 'certificates@eventtrix.com';
					//$tomail = 'deeputg1992@gmail.com';
												
					  $emailSubject = "eTranscript is attached  ".$student_data[0]->email;;
					  $mailContent = "<p>Please find the attachment of eTranscript here with it.<p>";
					  $mailContent = "<p>User details of eTranscript hard copy applied, <p>";
					  
					  $mailContent .= "<p>User name  : ".$student_data[0]->first_name." ".$student_data[0]->last_name."</p>";
					  $mailContent .= "<p>House Number :  ".$student_data[0]->house_number."</p>";
					  $mailContent .= "<p>Address :  ".$student_data[0]->address."</p>";					  
					  $mailContent .= "<p>City :  ".$student_data[0]->city."</p>";
					  $mailContent .= "<p>Zip code :  ".$student_data[0]->zipcode."</p>";
					  $mailContent .= "<p>Country : ".$user_country_name."</p>";
							
						//	echo "<br>Mail content ".$mailContent;
							
					  $this->email->from('info@eventtrix.com', 'Team EventTrix');
					  $this->email->to($tomail); 
					  $this->email->attach($this->path);
					  
					  $this->email->subject($emailSubject);
					  $this->email->message($mailContent);	
					  
					  $this->email->send();
							//  echo "Mail send ";
					  redirect('coursemanager/certificate');
				
	 
	 		  }
			  else if($product_type=='hardcopy')
			  {
				  	$this->load->library('email');					
					$certificate_details =  $this->certificate_model->get_certificate_details($user_id,$course_id);
					foreach($certificate_details as $key => $value)
					{
						$applied_date = $this->user_model->get_completion_date_from_course_enrolments($user_id,$course_id);
						if(!$applied_date)
						{
							$applied_date = $value->applied_on;	
						}
						$certificate_id = $value->id;	
					}						
				    $user_lang_id  = $this->common_model->get_user_lang_id($user_id);
						 						 
					if(isset($_POST['house_number']))
					{			
					$student_update_data['house_number'] = $apartment  = $this->input->post('house_number');
					$student_update_data['address'] = $address1  = $this->input->post('address1');
					$student_update_data['street'] = $street  = $this->input->post('street');
					//$address2  = $this->input->post('address2');	
					$student_update_data['country_id'] = $country  = $this->input->post('country');
					$student_update_data['zipcode'] = $zip_code  = $this->input->post('zip_code');
					$student_update_data['city'] = $city  = $this->input->post('city');
					
					$this->user_model->update_student_details($student_update_data,$user_id);
					}
					
					$insert_data=array("user_id"=>$user_id,"course_id"=>$course_id,"type"=>'hardcopy',"date_applied"=>$today,"product_id"=>$product_id,"payment_id"=>$payment_id);
					 
					$this->user_model->insertQuerys("user_subscriptions",$insert_data);
					$mark_details = $this->get_student_progress($course_id);
					
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
					
					$postal_type='standard';
					if($product_id == 20)
					{
						$postal_type='standard';
					}
										
					$insert_data_hardcopy =array("student_certificate_id"=>$certificate_id,"user_id"=>$user_id,"course_id"=>$course_id,"hardcopy_apply_date"=>$today,"grade"=>$grade,"completion_date"=>$applied_date,"postal_type"=>$postal_type,"source"=>'package_prepaid',"post_status"=>'pending',"payment_id"=>$payment_id);
					
					$this->user_model->insertQuerys("certificate_hardcopy_applications",$insert_data_hardcopy);					
					$pak_sub_array = array('status'=>0);					
					$this->package_model->update_package_subscription_details($user_id,$course_id,$packgae_sub_id,$product_type,$pak_sub_array);
					
					$stud_details=$this->user_model->get_stud_details($user_id);					
					$course_name = $this->common_model->get_course_name($course_id); 						 
				    foreach($stud_details as $val2)
				    {			  
					  $user_first_name = trim($val2->first_name);			 
					  $user_mail = $val2->email;			 
				    }
						  
					$user_first_name = strtolower($user_first_name);
					$user_first_name = ucfirst($user_first_name);						
					if($product_id == 20)
					{
						$postal_type='Standard Posting';
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
					$this->load->library('email');
					$this->load->model('email_model');
					
					$row_new = $this->email_model->getTemplateById('iceoes_hardcopy',$user_lang_id);
					foreach($row_new as $row1)
				    {				   
					   $emailSubject = $row1->mail_subject;
					   $mailContent = $row1->mail_content;
					}
					
					$mailContent = str_replace ( "#firstname#",$user_first_name, $mailContent );
					$mailContent = str_replace ( "#lastname#",$last_name, $mailContent );
					$mailContent = str_replace ( "#coursename#",$course_name, $mailContent); 
				   
					
					$mailContent = str_replace ( "#postal_option#", $postal_type, $mailContent );
					$mailContent = str_replace ( "#delivery_period#", $postal_estimate_time, $mailContent );
					$mailContent = str_replace ( "#house_number#", $user_house_number, $mailContent );
					$mailContent = str_replace ( "#street#", $user_street, $mailContent );
					$mailContent = str_replace ( "#address_line3#", $user_address, $mailContent );
					$mailContent = str_replace ( "#postal_code#", $user_zip_code, $mailContent );
					$mailContent = str_replace ( "#city#", $user_city, $mailContent );
					$mailContent = str_replace ( "#country#", $user_country_name, $mailContent );
					 
				    $this->email->from('info@eventtrix.com', 'Team EventTrix');
				    $this->email->to($user_mail); 
				    $this->email->cc(''); 
				    $this->email->bcc(''); 					   
				    $this->email->subject($emailSubject);
				    $this->email->message($mailContent); 					   
				    $this->email->send();								 
				    $this->email->clear(TRUE);				
				    redirect('coursemanager/success_hardcopy/'.$product_id, 'refresh');
				  
			  }
			  else if($product_type=='proof_completion_hard')//completed
			  { 
			  
			  	  $insert_data=array("user_id"=>$user_id,"course_id"=>$course_id,"product_id"=>$product_id,"type"=>'proof_completion_hard',
				  "date_applied"=>$today,"payment_id"=>$payment_id);		 
				  $this->user_model->insertQuerys("user_subscriptions",$insert_data);				  
				  $pak_sub_array = array('status'=>0);				
				  $this->package_model->update_package_subscription_details($user_id,$course_id,$packgae_sub_id,$product_type,$pak_sub_array);  
				  
			$this->load->helper(array('dompdf', 'file'));				  
			$html = $this->pdf_html_model->create_proof_completion_pdf_new($course_id);
			$data = pdf_create($html, 'proof_completion_'.$user_id.'_'.$course_id,false);		
			  
			//$data = pdf_create($html, '', false);	
			$this->path = "public/certificate/proof_completion/proof_completion_".$user_id."_".$course_id.".pdf";
			write_file($this->path, $data);
			
			$student_data = $this->user_model->get_student_details($user_id);		
			$user_country_name = $this->user_model->get_country_name($student_data[0]->country_id);
		
			 $this->load->library('email');
			//$tomail = 'info@trendimi.net';
			$tomail = 'certificates@eventtrix.com';
				
			 // $tomail       = 'certificates@trendimi.com';
			  $emailSubject = "Proof of completion is attached  ".$student_data[0]->email;;
			  $mailContent  = "<p>Please find the attacahment of proof of completion here with it.<p>";
			  $mailContent  = "<p>User details of Proof of completion hard copy applied, <p>";
			  $mailContent .= "<p>User name  : ".$student_data[0]->first_name." ".$student_data[0]->last_name."</p>";
			  $mailContent .= "<p>House Number :  ".$student_data[0]->house_number."</p>";
			  $mailContent .= "<p>Street :  ".$student_data[0]->street."</p>";
			  $mailContent .= "<p>Additional Address Line(if any) :  ".$student_data[0]->address."</p>";
			  $mailContent .= "<p>City :  ".$student_data[0]->city."</p>";
			  $mailContent .= "<p>Zip code :  ".$student_data[0]->zipcode."</p>";
			  $mailContent .= "<p>Country : ".$user_country_name."</p>";
					
			  $this->email->from('info@eventtrix.com', 'Team EventTrix');
			  $this->email->to($tomail); 
			  $this->email->attach($this->path);			  
			  $this->email->subject($emailSubject);
			  $this->email->message($mailContent);			  
			  $this->email->send();
			  $this->email->clear(TRUE);			
			  redirect('coursemanager/certificate');
			 
		}
			  else if($product_type=='poe_hard')//Completed
			  {
					$today = date("Y-m-d");
					$insert_data=array("user_id"=>$user_id,"course_id"=>$course_id,"type"=>'poe_hard',"product_id"=>$product_id,"date_applied"=>$today,"payment_id"=>$payment_id);		 
					$this->user_model->insertQuerys("user_subscriptions",$insert_data);
					
					 $pak_sub_array = array('status'=>0);				
					 $this->package_model->update_package_subscription_details($user_id,$course_id,$packgae_sub_id,$product_type,$pak_sub_array);		
					
					
					$user_lang_id  = $this->common_model->get_user_lang_id($user_id);
					
					$this->load->helper(array('dompdf', 'file'));			  
					$stud_details=$this->user_model->get_stud_details($user_id);
				
					$html = $this->pdf_html_model->create_proof_enrolement_pdf_new($course_id);
		  
				    $data = pdf_create($html, 'proof_study_'.$user_id.'_'.$course_id,false);
		  
				    //$data = pdf_create($html, '', false);	
				    $this->path = "public/certificate/proof_study/proof_study_".$user_id."_".$course_id.".pdf";
				    write_file($this->path, $data);		
				    $student_data = $this->user_model->get_student_details($user_id);		
				    $user_country_name = $this->user_model->get_country_name($student_data[0]->country_id);     
			  
					$this->load->library('email');
					$tomail = 'certificates@eventtrix.com';		
					  
					 // $tomail = 'deeputg1992@gmail.com';
									  
					$emailSubject = "Proof of enrolment is attached  ".$student_data[0]->email;;
					$mailContent = "<p>Please find the attacahment of proof of enrolment here with it.<p>";
					$mailContent = "<p>User details of Proof of enrolment hard copy applied, <p>";
					
					$mailContent .= "<p>User name  : ".$student_data[0]->first_name." ".$student_data[0]->last_name."</p>";
					$mailContent .= "<p>House Number :  ".$student_data[0]->house_number."</p>";
					$mailContent .= "<p>Street :  ".$student_data[0]->street."</p>";
					$mailContent .= "<p>Additional Address Line(if any) :  ".$student_data[0]->address."</p>";					  
				   // $mailContent .= "<p>Address :  ".$student_data[0]->address."</p>";					  
					$mailContent .= "<p>City :  ".$student_data[0]->city."</p>";
					$mailContent .= "<p>Zip code :  ".$student_data[0]->zipcode."</p>";
					$mailContent .= "<p>Country : ".$user_country_name."</p>";
						  
					$this->email->from('info@eventtrix.com', 'Team EventTrix');
					$this->email->to($tomail); 
					$this->email->attach($this->path);
					
					$this->email->subject($emailSubject);
					$this->email->message($mailContent);	
					
					$this->email->send();		
					$this->email->clear(TRUE);
					
					redirect('coursemanager/certificate');		  
				}
			
	 }
	 
	 function not_found_voucher_ajax(){
		 $data = array();
		 
		 
		  $this->load->model('email_model');
		  $userdata  = array();
		  $userdata['first_name'] = $content['fname'] = $this->input->post('fname');
		  $userdata['last_name'] = $content['lname'] = $this->input->post('lname');
		  $userdata['email'] = $content['email'] = $this->input->post('email');
		  $userdata['email'] = $content['email'] = $this->input->post('email');
		  $userdata['contact_no'] = $content['contact_no'] = $this->input->post('contact_no');
		  $userdata['country_id'] = $content['country_set'] = $this->input->post('country_id');
		  $userdata['course_id'] = $content['course_set'] = $this->input->post('course_id');
		  $userdata['v_expiry'] = $content['v_expiry'] = $this->input->post('v_expiry');
		  $userdata['v_buy_date'] = $content['v_buy_date'] = $this->input->post('v_buy_date');
		  $userdata['v_website'] = $content['v_website'] = $this->input->post('v_website');
          
		 	
		  $this->form_validation->set_rules('fname', 'First name', 'trim|required');
		  $this->form_validation->set_rules('lname', 'Last Name', 'required');
		  $this->form_validation->set_rules('country_id', 'Country', 'requered');
		  $this->form_validation->set_rules('email', 'E-mail', 'required|valid_email');
		  $this->form_validation->set_rules('contact_no', 'Contact-No', 'required');
		  //$this->form_validation->set_rules('v_expiry', 'Expirity date ', 'required');
		  //$this->form_validation->set_rules('v_buy_date', 'Estimated date of buying the voucher code', 'required');
		  $this->form_validation->set_rules('v_website','On what website did you buy voucher', 'required');
		 
		  if($this->form_validation->run())
		  {
			 // echo json_encode($_FILES);exit;
			    $config['upload_path'] = 'public/uploads/deals/complaints/';
				$config['allowed_types'] = 'gif|jpg|png|pdf';
				$config['max_size']	= '10000';
				
				$this->load->library('upload', $config);
				$this->upload->initialize($config);
				if ( $this->upload->do_upload('pdf'))
				{
					$uploaded = array('upload_data' => $this->upload->data());
					$pdf_name = $uploaded['upload_data']['file_name'];
				}
				else
				{
					$error['upResult'] = array('error' => $this->upload->display_errors());
					$data['error_code'] = 2;
					$data['msg'] = $error['upResult']['error'];
					$data['class'] = "alert alert-danger";
					
					echo json_encode($data);
					exit;
				}
			  
			  	$newResult = $this->email_model->getTemplateById('voucher_not_found',$this->session->userdata('language'));//get conttact us mail template 
				if(!empty($newResult))
				{
					foreach($newResult as $row1)
					{
						$mailContent = $row1->mail_content;
						$subject =  $row1->mail_subject;
						$mailing_template_id=$row1->id;
					}
				}
				if($content['course_set']!="")
			 	$course_name = $this->common_model->get_course_name($content['course_set']);
				else
				$course_name = "N/A";
			 	$country_name = $this->user_model->get_country_name($content['country_set']);
		
				$mailContent = str_replace("#FirstName#",$content['fname'],$mailContent);
				$mailContent = str_replace("#surname#",$content['lname'],$mailContent);
				$mailContent = str_replace("#email#",$content['email'],$mailContent);
				$mailContent = str_replace("#phone#",$content['contact_no'],$mailContent);
				$mailContent = str_replace("#course#",$course_name,$mailContent);
				$mailContent = str_replace("#country#",$country_name,$mailContent);
				$mailContent = str_replace("#expiry_date#",$content['v_expiry'],$mailContent);
				$mailContent = str_replace("#buy_date#",$content['v_buy_date'],$mailContent);
				$mailContent = str_replace("#voucherweb#",$content['v_website'],$mailContent);
				$mailContent = str_replace("#pdf_link#",base_url()."public/uploads/deals/complaints/".$pdf_name."",$mailContent);

								
				$from = "mailer@eventtrix.com";
				$tomail = 'info@eventtrix.com';
				$tomail = 'deeputg1992@gmail.com';
				//$from = $content['email'];
				//$subject = $mailContent['subject'];	
				//echo $tomail."<Br>";echo $from."<Br>";echo $subject."<Br>".$mailContent;exit;
				$this->load->library('email');
						   	
				$this->email->from($from); 
				$this->email->to($tomail); 
				$this->email->cc(''); 
				$this->email->bcc(''); 
				
				$this->email->subject($subject);
				$this->email->message($mailContent);	
							
				if($this->email->send()){
					$data['err_code'] = 0;
			  		$data['msg'] = "Your mail has been send to admin.";
			  		$data['class'] = "alert alert-danger";
				}
				else{
					$data['err_code'] = 3;
			  		$data['msg'] = "Your mail couldn\'t send now.Please try again later.";
			  		$data['class'] = "alert alert-danger";
				}
		
		  }
		  else{
			  $data['err_code'] = 1;
			  $data['msg'] = "Please fill all mandatory fields.";
			  $data['class'] = "alert alert-danger";
		  }
			
		  echo json_encode($data); 
	 }
	 
	 function sec_code_uploader_ajax(){
		    $content['vCode'] = $this->input->post('vCode');
			$content['secure'] = $this->input->post('secure');
		    $content['secure_pdf'] = $this->input->post('secure_pdf');
			$content['is_req'] = $this->input->post('is_req');
		    
			$this->form_validation->set_rules('vCode', 'Vouchercode', 'trim|required');
			if($content['is_req']=='1'){
			$this->form_validation->set_rules('secure', 'Security password', 'trim|required');
			}
			
			if($this->form_validation->run())
			{
				$sessArray['vCode'] = $this->input->post('vCode');
				if($content['is_req']=='1'){
					$config['upload_path'] = 'public/uploads/deals/pdf/';
					$config['allowed_types'] = 'gif|jpg|png|pdf';
					$config['max_size']	= '7000';				
					
					$this->load->library('upload', $config);
					$this->upload->initialize($config);
					
					if ( $this->upload->do_upload('secure_pdf'))
					{
						$sessArray['uploaded'] = array('upload_data' => $this->upload->data());
					}
					else
					{
						$error['upResult'] = array('error' => $this->upload->display_errors());
						$data['error_code'] = 2;
						$data['msg'] = $error['upResult']['error'];
						$data['class'] = "alert alert-danger";
						
						echo json_encode($data);
						exit;
					}
					
					if($this->gift_voucher_model->securityValidation($content['vCode'])==1)
					{
						$sessArray['is_req'] = 1;
						$sessArray['secure'] = $this->input->post('secure');
					}
					else
					{
						$data['err_code'] = 3;
						$data['msg'] = "Vouvher code validation failed. Reloading the page may fix this issue.";
						$data['class'] = "alert alert-danger";
						echo json_encode($data);
						exit;
					}				
				}
				else{
					if($this->gift_voucher_model->securityValidation($content['vCode'])==1){
						$sessArray['is_req'] = 0;
					}
				}
				$this->session->set_userdata('deals', $sessArray);
				
       			$data['err_code'] = 1;
			  	$data['msg'] = "Voucher code accepted.";
			  	$data['class'] = "alert alert-success";
			}
			else{
			  $data['err_code'] = 1;
			  $data['msg'] = "Please fill all mandatory fields.";
			  $data['class'] = "alert alert-danger";
		  }
			
			echo json_encode($data);
			exit;
		}
		
	function select_course_for_deals(){
		$data['enrolled_course_ids'] = array();
		
		if($this->session->userdata('student_logged_in'))
		$user_id = $this->session->userdata['student_logged_in']['id'];
		else
		$user_id =0;
		 
		$vouchercode = $this->session->userdata['deals']['vCode'];
		
		if($user_id!=0){
			$enrolled_courses = $this->user_model->get_courses_student($user_id);
			if(!empty($enrolled_courses)){
				foreach($enrolled_courses as $en_course){
					$enrolled_course_ids[] = $en_course->course_id;			
				}	
			}
		}
		else{
			$enrolled_course_ids =array();
		}
		
		$voucherDetails = $this->gift_voucher_model->getDetails_of_vcode($vouchercode);
		if($voucherDetails[0]->courses_idcourses==""||$voucherDetails[0]->courses_idcourses==0)
		{
			$data['course_set']=$this->user_model->get_course($this->session->userdata['language']);
			$data['course_count']=0;
		}
		else
		{
			$course_ids = explode(",",$voucherDetails[0]->courses_idcourses);
			$data['course_count']=count($course_ids);
			for($c=0;$c<count($course_ids);$c++){
				$course_namesArr =$this->user_model->get_coursename($course_ids[$c]);
			    $data['course_set'][$course_ids[$c]]=$course_namesArr[0]->course_name;
			}
		}
		
		
		//----------------------------------------------------------------------
		$data['html_content'] = "";
		$data['available_courses'] = 0;
		if(empty($data['course_set']))
		{
			$data['html_content'] .= '<div class="col-md-6 col-sm-6"><strong>No courses to buy!</strong></div>';
		}
		else
		{
			$i=0;
			$close_html_flag = true;
			$data['html_content'] .= '<div class="col-md-6 col-sm-6"><div class="form-group"><ul class="reg">';
			                  
			foreach($data['course_set'] as $key=>$value)
			{
				$i=$i+1;			
				if(count($data['course_set'])>1 && $i>ceil(count($data['course_set'])/2))
				{
					if($close_html_flag)
					{
						$data['html_content'] .='</ul></div></div><div class="col-md-6 col-sm-6"><div class="form-group"><ul class="reg">';
						$close_html_flag = false;
					}
				}
				
				$course_deails = $this->user_model->get_coursename($key);	  
				if($course_deails[0]->course_status==1 && !in_array($key,$enrolled_course_ids))
				{
					$data['available_courses'] ++;
					if($data['course_count']==0){
						$onclick ="add_remove_course(".$key.",'non_user')";
						$cursor = "pointer";
						$icon_class = "icon-circle-empty";
					}
					else{
						$onclick ="";
						$cursor = "notallowed";
						$icon_class = "icon-circle";
					}
					$data['html_content'] .='<li style="cursor:'.$cursor.'"><span id="non_user_course_add_remove_'.$key.'" class="chb_non_user_course_span" onclick='.$onclick.'><i class='.$icon_class.'></i></span>'.ucwords(strtolower($value)).'</li>';
				}
				else
				{
					$data['html_content'] .='<li><span style="cursor:not-allowed" id="non_user_course_add_remove_'.$key.'"><i class=" icon-cancel-circle"></i></span>'.ucwords(strtolower($value)).'</li>';
				}
				
				$data['html_content'] .= '<input type="checkbox"  id="non_user_course_id_'.$key.'"  name="non_user_course_id[]" value="'.$key.'" class="chb_non_user_course_check_box " hidden="hidden" >';
			}
			$data['html_content'] .='</ul></div></div>';
		}

		
		
		echo json_encode($data);	 
	 }
	 
	 function deals_signingup_pre_user(){
		 $data =array();
		  $data['redirectPath'] = 0;
		  $studentdata  = array();
		  $studentdata['first_name'] = $content['fname'] = ucfirst(strtolower($this->input->post('fname')));
		  $studentdata['last_name'] = $content['lname'] = ucfirst(strtolower($this->input->post('lname')));
		  $studentdata['email'] = $content['email'] = $this->input->post('email');
		  $studentdata['username'] = $content['user_name'] = $this->input->post('user_name');
		  $content['pword'] = $this->input->post('pword');
		  $studentdata['password']=$this->encrypt->encode($this->input->post('pword'));
		  $studentdata['gender'] = $content['gender'] = $this->input->post('gender');
		  $studentdata['contact_number'] = $content['contact_no'] = $this->input->post('contact_no');
		  $day=$this->input->post('day');
		  $month=$this->input->post('month');
		  $year=$this->input->post('year');
		 $studentdata['dob'] = $content['dob_check']=$year."/".$month."/".$day;
		  //$studentdata['dob'] = $content['dob_check'] = $this->input->post('dob_check');
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
		 /* if($this->input->post('reason_id')!='')
		  {
			 $studentdata['reason_id'] = $content['reason_to_buy_set'] = implode(",",$this->input->post('reason_id'));   
		  }*/
		  //$studentdata['reason_id'] = $content['reason_to_buy_set'] = implode(",",$this->input->post('reason_id')); 
		  $content['any_course'] = $this->input->post('any_course');
		  if($content['any_course']==1)
		  $studentdata['course_id'] = $content['course_set'] = $this->input->post('course_id');

		  $studentdata['with_coupon']='yes';
		  $studentdata['coupon_code'] = $this->session->userdata['deals']['vCode'];
		  if(isset($this->session->userdata['deals']['is_req'])&&$this->session->userdata['deals']['is_req']==1)
		  {
		  $studentdata['redemption_code'] = $this->session->userdata['deals']['secure'];
		  $studentdata['redemption_pdf'] = $this->session->userdata['deals']['uploaded']['upload_data']['file_name'];
		  }
			
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
		  
		  
		 
		  if($this->form_validation->run())
		  {
				$this->session->unset_userdata('student_temp_id');	
				$this->session->unset_userdata('cart_session_id');
				$this->session->unset_userdata('package_applying_course');			
				$this->session->unset_userdata('added_user_id');	
				session_regenerate_id();
			
				$this->user_model->add_student_temp($studentdata);
				$student_id=$this->db->insert_id();									
				$sess_array = array('student_temp_id' => $student_id);			
				$this->session->set_userdata($sess_array);	
				
				$data['class'] = "alert alert-success";
			 	$data['msg'] = "Success!";	
					
				if($content['any_course']==1)
				{
					$data['error_code'] = 0;
					$data['redirectPath'] = '/sales/package_details/'.$student_id.'/'.$studentdata['course_id'];
				}
				else
				{
					$data['error_code'] = 0;
					$data['redirectPath'] = '/sales/package_details/'.$student_id;
				}					
		  }
		  else{
			  $data['error_code'] = 1;
			  $data['class'] = "alert alert-danger";
			  $data['msg'] = "Please fill all mandatory fields.";
		  }
		  echo json_encode($data);
	 }
	 
	 function set_vcode_session(){
		 $sessArray['vCode'] = $_POST['vCode'];
		 $sessArray['is_req'] = 0;
		  $this->session->set_userdata('deals', $sessArray);
		  
		  $data['err_code'] = 1;
		  $data['msg'] = "Voucher code accepted.";
		  $data['class'] = "alert alert-success";
		  
		  echo json_encode($data);
	 }
  }