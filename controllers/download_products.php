<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class download_products extends CI_Controller
{
 	 
	function __construct()
	{

		parent::__construct();
		$this->load->library('session');
		$this->load->library('encrypt');
		$this->load->helper(array('form'));
		$this->load->helper('text');
    	//$this->load->helper(array('language'));
		$this->load->model('sales_model','',TRUE);
		$this->load->library('form_validation');
		$this->load->model('user_model','',TRUE);
		$this->load->model('common_model','',TRUE);
		$this->load->model('certificate_model','',TRUE);
		$this->load->model('gift_voucher_model','',TRUE);
		$this->load->model('ebook_model','',TRUE);
		
		
		//echo $this->input->ip_address();
		if($message = $this->session->flashdata('message')){
          $this->flashmessage =$message;
     }
		
		//$this->load->library('geoip_lib');
		$ip = $this->input->ip_address();
		//$ip = '212.58.253.67';//UK ip
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
			if($this->form_validation->run() == TRUE)
			{//Go to private area
				redirect('coursemanager/campus', 'refresh');
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
		$this->tr_common['tr_Ebooks'] =$this->user_model->translate_('Ebooks');
		 $this->tr_common['tr_stylist_id']      =$this->user_model->translate_('stylist_id');
         $this->tr_common['tr_code']            =$this->user_model->translate_('style_code');
		 $this->tr_common['tr_return_campus']   =$this->user_model->translate_('tr_return_campus');
		 $this->tr_common['tr_sign_out']        =$this->user_model->translate_('tr_sign_out');

		
		$this->tr_common['tr_about_us']   =$this->user_model->translate_('about_us');
		$this->tr_common['tr_faq']        =$this->user_model->translate_('faq');		 
    	$this->tr_common['tr_contact_us'] =$this->user_model->translate_('contact_us');
		 $this->tr_common['tr_eventrix'] =$this->user_model->translate_('eventrix');
     	$this->tr_common['tr_change_photo']   =$this->user_model->translate_('change_foto'); 	  	 
		$this->tr_common['tr_edit_details'] = $this->user_model->translate_('edit_your_account_details');
		$this->tr_common['tr_change_password'] = $this->user_model->translate_('change_password');
		$this->tr_common['tr_help_center'] = $this->user_model->translate_('help_center');		
		$this->tr_common['tr_extend_course'] = $this->user_model->translate_('extend_course');
		$this->tr_common['tr_certificate'] = $this->user_model->translate_('certificate');
		$this->tr_common['tr_contact_us'] = $this->user_model->translate_('contact_us');
  	    $this->tr_common['tr_fitting_room'] =$this->user_model->translate_('fitting_room');
	   	$this->tr_common['tr_testimonials'] =$this->user_model->translate_('testimonials');
		
		$this->tr_common['tr_why_us']   =$this->user_model->translate_('why_us');
		 $this->tr_common['tr_work_with_us'] =$this->user_model->translate_('work_with_us');
		$this->tr_common['tr_terms_use']        =$this->user_model->translate_('terms_use');		 
    	$this->tr_common['tr_privacy_policy'] =$this->user_model->translate_('privacy_policy');
		$this->tr_common['tr_affiliate_disclosure'] =$this->user_model->translate_('affiliate_disclosure');
		$this->tr_common['tr_made_with'] =$this->user_model->translate_('made_with');
		$this->tr_common['tr_in_ireland'] =$this->user_model->translate_('in_ireland');
		$this->tr_common['tr_all_rights_reserved'] =$this->user_model->translate_('all_rights_reserved');
		
		
		$this->tr_common['tr_Products_download']        =$this->user_model->translate_('Products_download');		
		$this->tr_common['tr_Downloadprint_and_keep_forever_Take_your_Trendimi']        =$this->user_model->translate_('Downloadprint_and_keep_forever_Take_your_Trendimi');		
		//***********************************
		$this->tr_common['tr_download_proof_of_study']        =$this->user_model->translate_('download_proof_of_study');
		$this->tr_common['tr_download_proof_of_completion']        =$this->user_model->translate_('download_proof_of_completion');
		$this->tr_common['tr_download_eTranscript']        =$this->user_model->translate_('download_eTranscript');
		$this->tr_common['tr_download']        		= $this->user_model->translate_('download');   
		$this->tr_common['tr_Downloads']        		= $this->user_model->translate_('Downloads');  
		$this->tr_common['tr_sorry_no_downloads']       = $this->user_model->translate_('Sorry_you_ dont_have_document_available_download');
		$this->tr_common['tr_Certificate']       = $this->user_model->translate_('Certificate');
		$this->tr_common['tr_proof_completion']       = $this->user_model->translate_('proof_completion');
		$this->tr_common['tr_proof_of_study']       = $this->user_model->translate_('sales_product_name_poe');
		$this->tr_common['tr_Breakdown_Transcript']       = $this->user_model->translate_('Breakdown_Transcript');
		$this->tr_common['tr_Documents']       = $this->user_model->translate_('Documents');
		
		
//		$top_menu_base_courses = $this->user_model->get_courses($this->language);


		$this->language = $this->session->userdata('language');
		$this->student_status = $this->session->userdata('student_logged_in');
		$this->course=$this->user_model->get_courses($this->language);
		if(empty($this->course))
		{
			$this->course=$this->user_model->get_courses(4); // get english courses
		}
		
		$this->menu['top_menu']=$this->user_model->get_top_menu($this->language);
    }
	
	
	
	function index($course_id=NULL)
	{	
		
		$content = array();
		$subscriptions =array();
		$user_subscriptions = array();
		$enrolled_courses = array();
		$course_name_array = array();
		
		$subscribed_ebooks = array();
		
		if(isset($this->session->userdata['student_logged_in']['id']))
		{
			$content=$this->get_student_deatils_for_popup();
		}		
		
		if(isset($this->session->userdata['student_logged_in']['id']))
		{
			$user_id = $this->session->userdata['student_logged_in']['id'];
			$enrolled_courses = $this->sales_model->get_courses_student_all($user_id);
			$ebook_subscriptions = $this->ebook_model->suscribed_ebooks($user_id);			
			//echo "<pre>";print_r($ebook_subscriptions);exit;
			for($eb=0;$eb<count($ebook_subscriptions);$eb++) {
			
			$eb_temp = $this->ebook_model->get_ebook_details($ebook_subscriptions[$eb]);				

			if($eb_temp!=false)
			{
				if($eb_temp[0]->courseId>0)
				{
					$subscribed_ebooks[$eb_temp[0]->courseId]['ebook_name'] = $eb_temp[0]->ebookName;
					$subscribed_ebooks[$eb_temp[0]->courseId]['course_name'] = $this->common_model->get_course_name($eb_temp[0]->courseId);
					$subscribed_ebooks[$eb_temp[0]->courseId]['file_name'] = $eb_temp[0]->fileName;	
					//$subscribed_ebooks[$eb_temp[0]->courseId]['image_name'] = $eb_temp[0]->image_name;
					
					$subscribed_ebooks[$eb_temp[0]->courseId]['image_name'] = $this->sales_model->get_course_image($eb_temp[0]->courseId); 					
				}
				else
				{
					$subscribed_ebooks[$eb_temp[0]->courseId]['ebook_name']  = $eb_temp[0]->ebookName;
					$subscribed_ebooks[$eb_temp[0]->courseId]['course_name'] = 'Color Guide';
					$subscribed_ebooks[$eb_temp[0]->courseId]['file_name']   = $eb_temp[0]->fileName;	
					$subscribed_ebooks[$eb_temp[0]->courseId]['image_name']  = $eb_temp[0]->image_name;								
				}
			}
			
			}
			
			/*echo "<pre>";
			print_r($enrolled_courses);
			exit;
			*/
			//$k=0;
			foreach($enrolled_courses as $en_course)
			{
				$course_name_array[$en_course->course_id] = ucwords($this->common_model->get_course_name($en_course->course_id)); 
				$course_image_array[$en_course->course_id] = $this->common_model->get_course_home_image($en_course->course_id); 
				$user_subscriptions[$en_course->course_id] = $this->sales_model->get_all_user_subscriptions_test($user_id,$en_course->course_id);
				
				
				$cert_details = $this->user_model->get_certficate_details($user_id,$en_course->course_id);
				if($cert_details!=NULL){
					if($cert_details['issue_status']=="approved"){
						$can_download_cert[$en_course->course_id] = 1; 
					}
					else{
						$can_download_cert[$en_course->course_id] = 0;
					}
				}
				else{
					$can_download_cert[$en_course->course_id] = 0;
				}
				
				
				$data['course_idd'][$en_course->course_id] = $en_course->course_id;
				//$k++;
			}			
		
		

		}
		else
		{
			
			
			if($_POST)
			{
				if(isset($_POST['username']))
				{
					$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
					$this->form_validation->set_rules('password_colour_wheel', 'Password', 'trim|required|xss_clean|callback_check_database');
					$content['username'] = $this->input->post('username');
					if($this->form_validation->run() == TRUE)
					{				
						redirect('download_products', 'refresh');
					}
				}
			
			}
			
			
			
		}
		
		/*$ebook_id_for_course_id = $this->ebook_model->get_ebook_id_for_course_id($en_course->course_id);
				
		$ebook_subsc[$k] = $this->ebook_model->get_ebook_id_for_course_id($user_id,$en_course->course_id,$this->language);*/
		
		$top_menu_base_courses = $this->user_model->get_courses($this->language);	
		
		if(empty($top_menu_base_courses))
		{
			$top_menu_base_courses = $this->user_model->get_courses(4);	
		}			
		$data['top_menu_base_courses'] 			= $top_menu_base_courses;

		$data['course_image_array'] = $course_image_array;
		$data['subscribed_ebooks'] = $subscribed_ebooks;
		
		$data['course_name_array'] = $course_name_array;
		$data['enrolled_courses'] = $enrolled_courses;
		$data['user_subscriptions'] = $user_subscriptions;
		$data['can_download_cert'] = $can_download_cert;
		$data['ebook_subscriptions'] = $ebook_subscriptions;
		$data['translate'] = $this->tr_common;
		$data['view'] = 'download_sales_product';
        $data['content'] = $content;				
        $this->load->view('user/template_inner',$data);
		
	}
	
	
	
	function products_test($course_id=NULL)
	{	
		
		$content = array();
		$subscriptions =array();
		$user_subscriptions = array();
		$enrolled_courses = array();
		$course_name_array = array();
		$user_colour_wheel_subscriptions = array();
		
		if(isset($this->session->userdata['student_logged_in']['id']))
		{
		$content=$this->get_student_deatils_for_popup();
		}
		
		
		if(isset($this->session->userdata['student_logged_in']['id']))
		{
			$user_id = $this->session->userdata['student_logged_in']['id'];
			$enrolled_courses = $this->sales_model->get_courses_student($user_id); 
			
			/*echo "<pre>";
			print_r($enrolled_courses);
			exit;*/
			
			$k=0;
			foreach($enrolled_courses as $en_course)
			{
				$course_name_array[$k] = ucwords($this->common_model->get_course_name($en_course->course_id)); 
				$user_subscriptions[$k] = $this->sales_model->get_all_user_subscriptions_test($user_id,$en_course->course_id);
				$k++;
			}
			
			
			/*echo "<pre>"; 
			print_r($user_subscriptions);
			exit;*/
		$user_colour_wheel_subscriptions = $this->sales_model->get_user_colour_wheel_subscriptions($user_id);	
			
		}
		else
		{
			
			
			if($_POST)
			{
				if(isset($_POST['username']))
				{
					$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
					$this->form_validation->set_rules('password_colour_wheel', 'Password', 'trim|required|xss_clean|callback_check_database');
					$content['username'] = $this->input->post('username');
					if($this->form_validation->run() == TRUE)
					{				
						redirect('download_products', 'refresh');
					}
				}
			
			}
			
			
			
		}
		
		$top_menu_base_courses = $this->user_model->get_courses($this->language);	
		
		if(empty($top_menu_base_courses))
		{
			$top_menu_base_courses = $this->user_model->get_courses(4);	
		}			
		$data['top_menu_base_courses'] 			= $top_menu_base_courses;
		$data['user_colour_wheel_subscriptions'] = $user_colour_wheel_subscriptions;
		$data['course_name_array'] = $course_name_array;
		$data['enrolled_courses'] = $enrolled_courses;
		$data['user_subscriptions'] = $user_subscriptions;
		$data['translate'] = $this->tr_common;
		//$data['view'] = 'colour_wheel_download';
        $data['content'] = $content;				
        $this->load->view('user/download_sales_product_test',$data);
		
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
	$this->tr_common['tr_testimonials'] =$this->user_model->translate_('testimonials');

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
	
	function check_database($password)
 	{
   					//Field validation succeeded.  Validate against database
   					$username = $this->input->post('username');

					/*echo "<br>Login check";
					echo "<br>User name ".$username;
					echo "<br>Password ".$password;
					exit;*/
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
	
}