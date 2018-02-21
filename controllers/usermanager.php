<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class usermanager extends CI_Controller {

	 
	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form'));
		
		$this->load->library('form_validation');
		$this->load->library('encrypt');
		$this->load->library('Datatables');
		$this->load->library('table');
		$this->load->library('email');
		
   		$this->load->model('user_model','',TRUE);
		$this->load->model('email_model','',TRUE);
		$this->load->model('student_model','',TRUE);
		$this->load->model('common_model','',TRUE);
		
		 $this->load->database();
		 
		 
		 if(isset($_GET['lang_id'])){
			$newdata = array('language'  => $_GET['lang_id']);
			$this->session->set_userdata($newdata);
		}
		elseif(!$this->session->userdata('language')){
			$newdata = array('language'  => '4');
			$this->session->set_userdata($newdata);
		} 
		$this->language = $this->session->userdata('language');
		 
		 
		 //---------------common translations --------------------------
		 
		 
		 $this->tr_common['tr_about_us']   =$this->user_model->translate_('about_us');
		 $this->tr_common['tr_faq']        =$this->user_model->translate_('faq');		 
    	 $this->tr_common['tr_contact_us'] =$this->user_model->translate_('contact_us');
		  $this->tr_common['tr_return_campus']   =$this->user_model->translate_('tr_return_campus');
		 $this->tr_common['tr_sign_out']        =$this->user_model->translate_('tr_sign_out');
		 $this->tr_common['tr_why_us']   =$this->user_model->translate_('why_us');
		 $this->tr_common['tr_stylist_id'] =$this->user_model->translate_('stylist_id');
         $this->tr_common['tr_code']        =$this->user_model->translate_('style_code');
         $this->tr_common['tr_eventrix'] =$this->user_model->translate_('eventrix');
		$this->tr_common['tr_terms_use']        =$this->user_model->translate_('terms_use');		 
    	$this->tr_common['tr_privacy_policy'] =$this->user_model->translate_('privacy_policy');
       
	}
	function index()
	{	
	   	
	}
	function contactUs()
	{
		
	 
		$content = array();
		$this->load->library('recaptcha');
      //  $content['recaptcha_html'] = $this->recaptcha->recaptcha_get_html();
	  
	  $this->tr_common['tr_first_name']   =$this->user_model->translate_('First_Name');	 
      $this->tr_common['tr_sur_name'] =$this->user_model->translate_('sur_name');
	  $this->tr_common['tr_email'] =$this->user_model->translate_('email');
	  $this->tr_common['tr_telephone'] =$this->user_model->translate_('Telephone');
      $this->tr_common['tr_country'] =$this->user_model->translate_('country');
	  $this->tr_common['tr_query_type'] =$this->user_model->translate_('Query_Type');
	  
	  $this->tr_common['tr_select_query'] =$this->user_model->translate_('please_select');
	  
	  $this->tr_common['tr_question'] =$this->user_model->translate_('question');
	  $this->tr_common['tr_description'] =$this->user_model->translate_('description');
	  $this->tr_common['tr_thank_u'] =$this->user_model->translate_('thank_u');
	   $this->tr_common['tr_no_thanks'] =$this->user_model->translate_('no_thanks');
	   $this->tr_common['tr_how_can_halp_you'] =$this->user_model->translate_('how_can_halp_you');
	  
	  $this->tr_common['tr_thanks_all_good'] =$this->user_model->translate_('thanks_all_good');
	  
	  
	  $this->tr_common['tr_or_feel_to_contact'] =$this->user_model->translate_('still_need_message');
	  $this->tr_common['tr_contact_numbers'] =$this->user_model->translate_('contact_numbers');
	  
	  
		 
	  
	  $result = $this->user_model->getDescription();
	  $newresult = $this->user_model->getDescriptiondistinct($this->session->userdata('language'));
	  
	    if(empty($newresult))
		{
			$newresult = $this->user_model->getDescriptiondistinct(4);
			
		}
		
	   $content['country']=$this->user_model->get_country();
				//echo "<pre>";print_r($result);exit;
				
					if(!empty($result))
   					{
						$i=0;
						foreach($result as $row)
						{
							$content['id'][$i] = $row->id;
							$content['description'][$i] = $row->description;
							$content['lang_id'][$i] = $row->lang_id;
							$content['question_category'][$i] = $row->question_category;
							$content['query_type'][$i] = $row->query_type;
							$i++;
						}
					}
	  if(!empty($newresult))
   					{
						$i=0;
						foreach($newresult as $row1)
						{
							
							$content['query_type_distinct'][$i] = $row1->query_type;
							$i++;
						}
					}

		// field name, error message, validation rules
		if(isset($_POST['email']))
		{
			
			
			$content['queryType'] = $this->input->post('queryType');
		    $content['question'] = $this->input->post('question');
			$content['descreptionText'] = $this->input->post('descreptionText');
		    //$content['call'] = $this->input->post('call');
		    $content['title'] = $this->input->post('title');
		    $content['firstName'] = $this->input->post('firstName');
			$content['lastName'] = $this->input->post('lastName');
			$content['telePhone'] = $this->input->post('telePhone');
			$content['email'] = $this->input->post('email');
			$content['country'] = $this->input->post('country');
			
		
		    
		   
			//$this->form_validation->set_rules('question', 'Specific questions', 'trim|required|xss_clean|callback_check_database');
			//$this->form_validation->set_rules('email','Email','trim|required|xss_clean');
			
			$this->form_validation->set_rules('descreptionText','Specific Description','trim|required|xss_clean');
			//$this->form_validation->set_rules('call','Call time','trim|required|xss_clean');
			//$this->form_validation->set_rules('title','Title','trim|required|xss_clean');
			$this->form_validation->set_rules('firstName','First name','trim|required|xss_clean');
			$this->form_validation->set_rules('lastName','Last name','trim|required|xss_clean');
			$this->form_validation->set_rules('telePhone','Tele phone number','trim|required|number|xss_clean');
			$this->form_validation->set_rules('email','Email','trim|required|xss_clean');
			$this->form_validation->set_rules('country','Country','trim|required|xss_clean');
			
			
			
			
				if($this->form_validation->run())
			{
				//$this->recaptcha->recaptcha_check_answer();
			//	$valid = $this->recaptcha->getIsValid();
		
				/*if($valid==FALSE)
				{
					//$captchaErr = $this->recaptcha->getError();
					 $this->form_validation->set_message('captchaValidate',$this->recaptcha->getError());
						
				}
				else
				{*/
					
				$newResult = $this->email_model->getTemplateById('contact_us',$this->session->userdata('language'));//get conttact us mail template 
				if(!empty($newResult))
				{
					foreach($newResult as $row1)
					{
						$mailContent = $row1->mail_content;
						$subject =  $row1->mail_subject;
					}
				}
			//echo "<pre>";print_r($mailContent);exit;
				//----------------------
		
		 $descriptionAray = $this->user_model->getQuestion($this->input->post('question'));
		if(!empty($descriptionAray))
		 $content['question'] =$descriptionAray['0']->question_category;
		 else
		 $content['question'] ="Common Query";
		 
		 $countryName =$this->user_model->get_country_name($content['country']);
		
		
		$mailContent = str_replace("#query#",$content['queryType'],$mailContent);
		$mailContent = str_replace("#questions#",$content['question'],$mailContent);
		$mailContent = str_replace("#user_query#",$content['descreptionText'],$mailContent);
		//$mailContent = str_replace("#callTime#",$content['call'],$mailContent);
		//$mailContent = str_replace("#title#",$content['title'],$mailContent);
		$mailContent = str_replace("#FirstName#",$content['firstName'],$mailContent);
		$mailContent = str_replace("#LastName#",$content['lastName'],$mailContent);
		$mailContent = str_replace("#phone#",$content['telePhone'],$mailContent);
		$mailContent = str_replace("#email#",$content['email'],$mailContent);
		$mailContent = str_replace("#country#",$countryName,$mailContent);

		$this->load->library('email');		
				
				//$tomail = 'bhagathindian@gmail.com';
				//$tomail = 'development@trendimi.net';
				//$from = $content['email'];
				$tomail = 'info@trendimi.com';
				$from = "mailer@trendimi.com";
				//$subject = $mailContent['subject'];
	
				//echo $tomail."<Br>";echo $from."<Br>";echo $subject."<Br>".$mailContent;
						   	
					  $this->email->from($from); 
					  $this->email->to($tomail); 
					  $this->email->reply_to($content['email']);
					  //$this->email->cc(''); 
					  //$this->email->bcc(''); 
					  
					  $this->email->subject($subject);
					  $this->email->message($mailContent);	
					  $this->email->send();
					  
					  
					  
					  				  
				//}
			
			redirect ('user/usermanager/Thanks');
		
			}
		}
		$top_menu_base_courses = $this->user_model->get_courses($this->language);	
		
		if(empty($top_menu_base_courses))
		{
			$top_menu_base_courses = $this->user_model->get_courses(4);	
		}
		$data['top_menu_base_courses'] 			= $top_menu_base_courses;
		
		$data['translate'] = $this->tr_common;
		$data['view'] = 'contact';
		$data['content'] = $content;
		$this->load->view('user/outerTemplate',$data);
		
	
	}
	public function Thanks()
	{
		$top_menu_base_courses = $this->user_model->get_courses($this->language);	
		
		if(empty($top_menu_base_courses))
		{
			$top_menu_base_courses = $this->user_model->get_courses(4);	
		}			
		$data['top_menu_base_courses'] 			= $top_menu_base_courses;
	$data['translate'] = $this->tr_common;
		$data['view'] = 'thankspage';
		$content['pageTitle']="Thank You";
		$data['content'] = $content;
		$this->load->view('user/outerTemplate',$data);
	}
	
	public function forgotPassword()
	{
		if(isset($_POST['email']))
		{
			$this->form_validation->set_rules('email','Email','trim|required|xss_clean');
			if($this->form_validation->run())
			{
				$email = $this->input->post('email');
				$result = $this->user_model->getUserByEmail($email);
				//echo "<pre>";print_r($result);exit;
				
					if(!empty($result))
   					{
						
						foreach($result as $row)
						{
							$userId = $row->user_id;
						}
						
						$key = $this->config->item('encryption_key');

						 $encryptedUid =$userId; 
						
					 /* $encryptedUid = urlencode( $this->encrypt->encode($userId, $key, TRUE) );
					  echo $encryptedUid."<br>";
					 $decoded= rawurldecode( $this->encrypt->decode( $encryptedUid, $key) );
						 echo "<br>".$decoded ;exit;*/
							
					$tomail = $email;
					//$tomail = 'deeputg1992@gmail.com';
					
					  $emailSubject = "Password Reset";
					  $mailContent = "Plese <a href= '".base_url()."user/usermanager/resetPassword/".$encryptedUid."'>click here</a> to reset your account password";
						   	
					  $this->email->from('info@trendimi.com', 'Team Trendimi');
					  $this->email->to($tomail); 
					  $this->email->cc(''); 
					  $this->email->bcc(''); 
					  
					  $this->email->subject($emailSubject);
					  $this->email->message($mailContent);	
					  
					  $this->email->send();
					  
					  $data['flashmessage']=$this->email->print_debugger();
					  $data['flashmessage'] = "An email is sent to your email address to access the account. Thank you";
					  //redirect('home','refresh');
									
     				}
					else
					{
						echo "Sorry we couldn't find your account.";
					}
			}
		}
					
		$content=array();			
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		
	
		
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		
		$top_menu_base_courses = $this->user_model->get_courses($this->language);	
		
		if(empty($top_menu_base_courses))
		{
			$top_menu_base_courses = $this->user_model->get_courses(4);	
		}			
		$data['top_menu_base_courses'] 			= $top_menu_base_courses;
		
		
		$data['translate'] = $this->tr_common;
		$data['view'] = 'forgotPassword';
		$data['content'] = $content;
		$this->load->view('user/outerTemplate',$data);
					
	}
	
	function resetPassword($encrUid)
	{
		
	if(isset($_POST['newPass']))
		{
			
			$this->form_validation->set_rules('newPass','New Password','trim|required|xss_clean');
			$this->form_validation->set_rules('confirmPass','Cornfirm Password','trim|required|xss_clean|callback_matchPass[confirmPass]');
			if($this->form_validation->run())
			{
				
				$studentdata['passWord'] =$this->encrypt->encode($this->input->post('newPass'));
				$this->student_model->add_details($studentdata,$encrUid);
				$this->session->set_flashdata('message', 'Password updation sussessfully completed.');
				redirect('home','refresh');
					
					
			}
		}
		
		//$uid = "decription happenning here-------------------------";
		$uid = $encrUid;	
		
		
		
		$newResult = $this->user_model->get_stud_details($uid);
		
		$content=array();
		$content['userId'] = $uid;
		
		if(!empty($newResult))
		{
			foreach($newResult as $row1)
			{
				
     					$sess_array = array();
     					
               if ($row1->status!=1) {
                
                  $this->session->set_flashdata('notActiveStudent','Sorry, you are not an active student.');
               
                }

                else{
       						$sess_array = array('id' => $row1->user_id,'username' => $row1->username );
       						$this->session->set_userdata('student_logged_in', $sess_array);
                 
                }
     					
     					
   					
				
				
			$content['userName'] = $row1->first_name;
			$content['emailId'] = $row1->email;
			}
			
			
		}
		else
		{
			$this->session->set_flashdata('invalidUser','You are not a valid user.');
			redirect('user/usermanager/forgotPassword');
		}
		
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		
		$top_menu_base_courses = $this->user_model->get_courses($this->language);	
		
		if(empty($top_menu_base_courses))
		{
			$top_menu_base_courses = $this->user_model->get_courses(4);	
		}			
		$data['top_menu_base_courses'] 			= $top_menu_base_courses;
		
		
		$data['translate'] = $this->tr_common;
		$data['view'] = 'resetPassword';
		$data['content'] = $content;
		$this->load->view('user/outerTemplate',$data);
			
	}
	
	function changePassword()
	{
		$content=array();
		$stud_id  = $this->session->userdata['student_logged_in']['id'];
		$newResult = $this->user_model->get_stud_details($stud_id);
		$content['states']=$this->user_model->get_states();
		$content['msg_pass']="";
		$content['msg']="";	
					 
		$data['country_name']=$this->common_model->get_country();	
		
		$country_name=$data['country_name'];
		
		if(isset($_POST['save_password']))
		{
			 
				$studentdata['passWord']=$this->encrypt->encode($this->input->post('newPass'));
				 $this->student_model->add_details($studentdata,$stud_id);
			
			$content['msg_pass']="You have successfully updated your password.";
			
		}
		
		if(isset($_POST['save_details']))
		{
			
			$studentdata['email']=$this->input->post('email');
			$studentdata['dob']=$this->input->post('dob');
			$studentdata['gender']=$this->input->post('gender');
			$studentdata['contact_number']=$this->input->post('contact_number');
			$studentdata['street']=$this->input->post('street');
			$studentdata['city']=$this->input->post('city');
			$studentdata['country_id'] = $content['country_set']=$this->input->post('country');
			//================================================
			 if($studentdata['country_id']=='12'){
				  $us_states= $content['state_set'] = $this->input->post('state');
				  }
				  if(isset($us_states)&& $us_states!=''){
					 
					 $state_details =$this->user_model->get_statename($us_states);
					  foreach($state_details as $row_states){
						 $studentdata['us_states']=$row_states->name_short;   
					  }
					 
				  }	
			$this->user_model->update_student_details($studentdata,$stud_id);
			    
				$content['msg']="Details updated successfully.";
			//echo "<pre>";
		//print_r($studentdata);
		//exit;
		}
		foreach($newResult as $row1)
			{
				$current_password=$row1->password;
				$data['first_name']=$row1->first_name;
				$data['last_name']=$row1->last_name;
				$data['email']=$row1->email;
				$data['dob']=$row1->dob;
				$data['gender']=$row1->gender;
				$data['contact_number']=$row1->contact_number;
				$data['street']=$row1->street;
				$data['city']=$row1->city;
				$data['country_id']=$row1->country_id;
				if($data['country_id']=='12'){
				$state=$row1->us_states;
				$state_details =$this->user_model->get_stateid($state);
					  foreach($state_details as $row_states){
						 $content['state_set']=$row_states->id;   
					  }
			  }
		}
		//echo "<pre>";
		//print_r($newResult);
		//exit;
		$data['translate'] = $this->tr_common;
		$data['view'] = 'edit_profile';
		$data['content'] = $content;
		$this->load->view('user/template_inner',$data);
		//$this->load->view('user/edit_profile');
	}
	function ajaxCheckPassword(){
		
		
		$user_id=$this->session->userdata['student_logged_in']['id'];
		$password = $_REQUEST['current_pswd'];
		$user_details = $this->user_model->get_stud_details($user_id);
		foreach($user_details as $row){
			$pass=$this->encrypt->decode($row->password);
		}
		if($password == $pass) {
			echo 'true';}
		else {
			echo 'false';}
		
	}
	function matchPass($conpword)
	{
		$new = $this->input->post('newPass');

		if($new!=$conpword)
		{
			 $this->form_validation->set_message('matchPass', 'Password dosen\'t match');
                return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	function description($question)
	{
		$content='';
		$result = $this->user_model->getQuestion($question);
		foreach($result as $row)
		{
		$content['result']=$row->description;
		}
		echo $content['result'];
		
		
	}
	
	/*function captchaValidate()
	{
		$this->load->library('recaptcha');

		$valid = $this->recaptcha->getIsValid();
		
		if($valid==0)
		{
			//$captchaErr = $this->recaptcha->getError();
			 $this->form_validation->set_message('captchaValidate', 'Captcha keyword mismatch.');
                return FALSE;
		}
		else
		{
			$this->form_validation->set_message('captchaValidate', 'Captcha workked!.');
			return TRUE;
		}
	}*/
	
	
}