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
		
		 $this->load->database();
		 
		 $ip = $this->input->ip_address();
    	//$this->geoip_lib->InfoIP($ip);
    	//$this->code3= $this->geoip_lib->result_country_code3();
     	//$this->con_name = $this->geoip_lib->result_country_name();
		$this->load->library('ip2country_lib');
		$this->con_name = $this->ip2country_lib->getInfo();
		//$this->con_name = 'UK';
		 
		 
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
		 $this->tr_common['tr_why_us']        =$this->user_model->translate_('why_us');		 
    	 $this->tr_common['tr_contact_us'] =$this->user_model->translate_('contact_us');
		  $this->tr_common['tr_eventrix'] =$this->user_model->translate_('eventrix');
		  $this->tr_common['tr_return_campus']   =$this->user_model->translate_('tr_return_campus');
		 $this->tr_common['tr_sign_out']        =$this->user_model->translate_('tr_sign_out');
		  $this->tr_common['tr_sign_in']        =$this->user_model->translate_('sign_in');
		  $this->tr_common['tr_return_to']   =$this->user_model->translate_('return_to');
		 $this->tr_common['tr_campus']   =$this->user_model->translate_('campus');
		 $this->tr_common['tr_sign']   =$this->user_model->translate_('sign');
		 $this->tr_common['tr_Out']   =$this->user_model->translate_('Out');
		 $this->tr_common['tr_user_name']      =$this->user_model->translate_('user_name');
         $this->tr_common['tr_password']            =$this->user_model->translate_('password');
        
		$this->tr_common['tr_terms_use']        =$this->user_model->translate_('terms_use');		 
    	$this->tr_common['tr_privacy_policy'] =$this->user_model->translate_('privacy_policy');
		$this->tr_common['tr_Email']   =$this->user_model->translate_('Email');
		$this->tr_common['tr_Telephone']   =$this->user_model->translate_('phone_contact');
		$this->tr_common['tr_category']   =$this->user_model->translate_('category');
		$this->tr_common['tr_details']   =$this->user_model->translate_('details');
       
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
	  $this->tr_common['tr_Telephone'] =$this->user_model->translate_('Telephone');
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
		
	  // $content['country']=$this->user_model->get_country();
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
			/*$content['country'] = $this->input->post('country');*/
			
		
		    
		   
			//$this->form_validation->set_rules('question', 'Specific questions', 'trim|required|xss_clean|callback_check_database');
			//$this->form_validation->set_rules('email','Email','trim|required|xss_clean');
			
			$this->form_validation->set_rules('descreptionText','Specific Description','trim|required|xss_clean');
			//$this->form_validation->set_rules('call','Call time','trim|required|xss_clean');
			//$this->form_validation->set_rules('title','Title','trim|required|xss_clean');
			$this->form_validation->set_rules('firstName','First name','trim|required|xss_clean');
			$this->form_validation->set_rules('lastName','Last name','trim|required|xss_clean');
			$this->form_validation->set_rules('telePhone','Tele phone number','trim|required|number|xss_clean');
			$this->form_validation->set_rules('email','Email','trim|required|xss_clean');
/*			$this->form_validation->set_rules('country','Country','trim|required|xss_clean');
*/			
			
			
			
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
						$subject ="Enquiry - ".$content['queryType'];
					}
				}
			//echo "<pre>";print_r($mailContent);exit;
				//----------------------
		
		 $descriptionAray = $this->user_model->getQuestion($this->input->post('question'));
		if(!empty($descriptionAray))
		 $content['question'] =$descriptionAray['0']->question_category;
		 else
		 $content['question'] ="Common Query";
		 
		 //$countryName =$this->user_model->get_country_name($content['country']);
		
		
		$mailContent = str_replace("#query#",$content['queryType'],$mailContent);
		$mailContent = str_replace("#questions#",$content['question'],$mailContent);
		$mailContent = str_replace("#user_query#",$content['descreptionText'],$mailContent);
		//$mailContent = str_replace("#callTime#",$content['call'],$mailContent);
		//$mailContent = str_replace("#title#",$content['title'],$mailContent);
		$mailContent = str_replace("#FirstName#",$content['firstName'],$mailContent);
		$mailContent = str_replace("#LastName#",$content['lastName'],$mailContent);
		$mailContent = str_replace("#phone#",$content['telePhone'],$mailContent);
		$mailContent = str_replace("#email#",$content['email'],$mailContent);
		//$mailContent = str_replace("#country#",$this->con_name,$mailContent);

		$this->load->library('email');		
				
				$tomail = 'info@eventtrix.com';
				//$tomail = 'sarathkochooli@gmail.com';
				//$from = $content['email'];
				//$tomail = 'info@hollyandhugo.com';
				$from = "mailer@eventtrix.com";
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
		
		$seo_details = $this->common_model->get_seo_details('contact',$this->language);
		$content['pageTitle'] = $seo_details[0]->pageTitle;
		$content['metaDesc'] = $seo_details[0]->metaDesc;
		$content['metaKeys'] = $seo_details[0]->metaKeys;
		
		$data['translate'] = $this->tr_common;
		$data['view'] = 'contact';
		$data['content'] = $content;
		$this->load->view('user/template_outer',$data);
		
	
	}
	public function contact_numbers()
 	{
		
 		$content = array();
        $this->tr_common['tr_contact_us'] =$this->user_model->translate_('contact_us');
		 $this->tr_common['tr_faq']        =$this->user_model->translate_('faq');	
		 
		$data['translate'] = $this->tr_common;
        $data['view'] = 'contact_numbers';
        $data['content'] = $content;
        $this->load->view('user/template',$data);
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
		$this->load->view('user/template_outer',$data);
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
							$first_name = $row->first_name;
						}
						
						//$key = $this->config->item('encryption_key');

						 $encryptedUid =$this->encrypt->encode($userId); 
						 $encryptedUid = str_replace("/","bahu_bali",$encryptedUid);
						 $encryptedUid = urlencode($encryptedUid);
						/* echo $encryptedUid;exit;
						 $uid = $this->encrypt->decode(urldecode($encryptedUid));
							echo $uid;exit;*/
						 
						$newResult = $this->email_model->getTemplateById('forgot_password',$this->session->userdata('language'));
				if(!empty($newResult))
				{
					foreach($newResult as $row1)
					{
						$mailContent = $row1->mail_content;
						$subject =  $row1->mail_subject;
					}
				}	
						
					//echo "<pre>";print_r($newResult);exit;
					
							
					$tomail = $email;
					//$tomail = 'deeputg1992@gmail.com';
					// echo  $encryptedUid; exit;
					  //$emailSubject = "Password Reset";
					  $mailContent = str_replace("#firstname#",$first_name,$mailContent);
					  $mailContent = str_replace ( "#click here#","<a href='".base_url()."user/usermanager/resetPassword_test/".$encryptedUid."'>click here</a>", $mailContent );
						   	
					  $this->email->from('info@eventtrix.com', 'Team Event//trix');
					  $this->email->to($tomail); 
					  $this->email->cc($tomail); 
					  $this->email->bcc(''); 
					  
					  $this->email->subject($subject);
					  $this->email->message($mailContent);	
				        
					  $this->email->send();
					  
					  $data['flashmessage']=$this->email->print_debugger();
					  //$this->session->set_flashdata('messages', "An email is sent to your account to access the account. Thank you");
			          $data['flashmessage'] = "We've sent you an email that will allow you to reset your password quickly and easily. If you need any further assistance please contact us at <a href='info@eventtrix.com' target='_blank'>info@eventtrix.com</a> <br>Have a good day.</br><br>EventTrix Team</br>";
					  //redirect('home','refresh');
									
     				}
					else
					{
						$data['flashmessage'] = "Sorry we couldn't find your account. Please contact <a href='info@eventtrix.com' target='_blank'>info@eventtrix.com</a> for assistance.<br> Have a good day.</br><br>EventTrix Team</br>";
						 //$this->session->set_flashdata('messages', "Sorry we couldn't find your account.");
					}
			}
		}
					
		$content=array();			
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
					 	 

	
		
	if(isset($this->flashmessage))
	{
		$content['flashmessage'] = $this->flashmessage;
	}
		$top_menu_base_courses = $this->user_model->get_courses($this->language);	
		
		if(empty($top_menu_base_courses))
		{
			$top_menu_base_courses = $this->user_model->get_courses(4);	
		}			
		$data['top_menu_base_courses'] 			= $top_menu_base_courses;
		
		
		$data['translate'] = $this->tr_common;
		$data['view'] = 'forgotPassword';
		$data['content'] = $content;
		$this->load->view('user/template_outer',$data);
					
	}
	public function forgotPassword_test()
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
							$first_name = $row->first_name;
						}
						
						//$key = $this->config->item('encryption_key');

						 $encryptedUid =$this->encrypt->encode($userId); 
						 $encryptedUid = str_replace("/","bahu_bali",$encryptedUid);
						 $encryptedUid = urlencode($encryptedUid);
						$newResult = $this->email_model->getTemplateById('forgot_password',$this->session->userdata('language'));
				if(!empty($newResult))
				{
					foreach($newResult as $row1)
					{
						$mailContent = $row1->mail_content;
						$subject =  $row1->mail_subject;
					}
				}	
						
					//echo "<pre>";print_r($newResult);exit;
					
							
					$tomail = $email;
					//$tomail = 'deeputg1992@gmail.com';
					// echo  $encryptedUid; exit;
					  //$emailSubject = "Password Reset";
					  $mailContent = str_replace("#firstname#",$first_name,$mailContent);
					  $mailContent = str_replace ( "#click here#","<a href='".base_url()."user/usermanager/resetPassword/".$encryptedUid."'>click here</a>", $mailContent );
						   	
					  $this->email->from('info@eventtrix.com', 'Team Event//trix');
					  $this->email->to($tomail); 
					  $this->email->cc($tomail); 
					  $this->email->bcc(''); 
					  
					  $this->email->subject($subject);
					  $this->email->message($mailContent);	
				        
					  $this->email->send();
					  
					  $data['flashmessage']=$this->email->print_debugger();
					  //$this->session->set_flashdata('messages', "An email is sent to your account to access the account. Thank you");
			          $data['flashmessage'] = "We've sent you an email that will allow you to reset your password quickly and easily.If you need any further assistance please contact us at <a href='info@eventtrix.com' target='_blank'>info@eventtrix.com</a> <br>Have a good day.</br><br>EventTrix Team</br>";
					  //redirect('home','refresh');
									
     				}
					else
					{
						$data['flashmessage'] = "Sorry we couldn't find your account. Please contact <a href='info@eventtrix.com' target='_blank'>info@eventtrix.com</a> for assistance.<br> Have a good day.</br><br>EventTrix Team</br>";
						 //$this->session->set_flashdata('messages', "Sorry we couldn't find your account.");
					}
			}
		}
					
		$content=array();			
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
					 	 

	
		
	if(isset($this->flashmessage))
	{
		$content['flashmessage'] = $this->flashmessage;
	}
		$top_menu_base_courses = $this->user_model->get_courses($this->language);	
		
		if(empty($top_menu_base_courses))
		{
			$top_menu_base_courses = $this->user_model->get_courses(4);	
		}			
		$data['top_menu_base_courses'] 			= $top_menu_base_courses;
		
		
		$data['translate'] = $this->tr_common;
		$data['view'] = 'forgotPassword';
		$data['content'] = $content;
		$this->load->view('user/template_outer',$data);
					
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
		$uid = $this->encrypt->decode(urldecode($encrUid));
		
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
		$this->load->view('user/template_outer',$data);
			
	}
	
	function resetPassword_test($encrUid)
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
		$uid = $this->encrypt->decode(str_replace("bahu_bali","/",urldecode($encrUid)));
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
		$this->load->view('user/template_outer',$data);
			
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
	
	function message_contact($question)
	{
		$content='';
		$result = $this->user_model->getQuestion($question);
		foreach($result as $row)
		{
		$content['result']=$row->description;
		}
		echo $content['result'];
		
		
	}
	
	function reviews(){
        $content=array();
        $lang_id=$this->language;
		$data['translate'] = $this->tr_common;
		$content['testimonials']= $testimonials = $this->common_model->get_testimonials($lang_id);
           $countries = $this->common_model->get_country();
          $countries[0] = 'NA';
           //echo "<pre>";
           //print_r($testimonials);exit;
		$i=0;
		if(!empty($testimonials))
		{
               foreach($testimonials as $review)
			{
                    $flag='';
                    $input=$review->course_id;
                    if($input==0)
                    {
                         $i++;
                    }
                    else
                    {

				$reviews['user_name'][$i]= $review->name;
				$reviews['content'][$i]= $review->content;
				$reviews['original_image'][$i]= $review->original_image;
                    $reviews['thumb_image'][$i]= $review->thumb_image;
				$reviews['country'][$i]= $countries[$review->country_id];

				if($input!='' || $input!='0')
				{
				$course_id_explode=explode(',',$input);
					for($j=0;$j<count($course_id_explode);$j++)
					{
					$this->db-> select('*');
					$this->db-> from('courses');
					$this->db->where('course_id',$course_id_explode[$j]);
					$query2 = $this->db->get();
						if($flag!='')
						{
							$result_array=$query2 -> result();
							foreach($result_array as $row2)
							{
								
						$reviews['course_name'][$i] =$reviews['course_name'][$i].", ".$row2->course_name;
							}
						}
						else
						{
							$result_array=$query2 -> result();
							foreach($result_array as $row2)
							{
								
						$reviews['course_name'][$i] =$row2->course_name;
							}
							
							$flag=1;
						}
					}
				}
			$i++;
			}}
			
		}
		$seo_details = $this->common_model->get_seo_details('reviews',$this->language);
		$content['pageTitle'] = $seo_details[0]->pageTitle;
		$content['metaDesc'] = $seo_details[0]->metaDesc;
		$content['metaKeys'] = $seo_details[0]->metaKeys;
		
		$content['reviews']=$reviews;
          $data['view'] = 'reviews';
		$data['content'] = $content;
		$this->load->view('user/template_outer',$data);
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