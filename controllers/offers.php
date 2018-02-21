<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class offers extends CI_Controller
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
	function OfferLogIn($offerId)
	{
		if(isset($this->session->userdata['student_logged_in']))
		{
			redirect('offers/upgradeCourse/'.$offerId);
		}
		if(isset($_POST['offer_pass']))
			{
				$logiing_in = $this->user_login($this->input->post('offer_pass'),$this->input->post('offer_uname'));
				//echo "<pre>";print_r($logiing_in);exit;
				if($logiing_in==TRUE)
				{
					redirect('offers/upgradeCourse/'.$offerId);
				}
				else
				{
					$this->session->set_flashdata('err_msg',"Login failed");
					redirect('offers/OfferLogin/'.$offerId);
				}
			}
			
		$content['translate'] = $this->tr_common;
		$content['view'] = 'offerLogin';
		$title['pageTitle'] = 'Login';
		$content['content'] = $title;
		$this->load->view('user/template_no_login',$content); 
			
	}
	function upgradeCourse($offerId)//date : 26-Nov-2013 - for adding new makeup modules for existing students 
	{
		$top_menu_base_courses = $this->user_model->get_courses($this->language);
		//echo "<pre>";print_r($top_menu_base_courses);exit;
		if(empty($top_menu_base_courses))
		{
			$top_menu_base_courses = $this->user_model->get_courses(4);	
		}
		$content['top_menu_base_courses'] 			= $top_menu_base_courses;
		
		if(!isset($this->session->userdata['student_logged_in']))
		{
			redirect('offers/OfferLogin/'.$offerId);
		}
		$userId=$this->session->userdata['student_logged_in']['id'];
		
		$content['offerDetails'] = $this->offer_model->getOfferDetails($offerId);
		$content['userDetails'] = $this->user_model->get_student_details($userId);
		
		$content['eligible'] = 0;
		$content['userCourses'] = $this->user_model->check_user_registered($userId,3);
		if(empty($content['userCourses']))
		{
			$content['userCourses'] = $this->user_model->check_user_registered($userId,13);
		}
		
		
		if(!empty($content['userCourses']))
		{
			//echo"<pre>";print_r($content['userCourses']);echo "</pre>";
			$unserial_units = unserialize($content['userCourses'][0]->student_course_units);
			if(in_array(18,$unserial_units)||in_array(28,$unserial_units))
			{
				$content['eligible']=1;
			}
			else
			$content['eligible'] = 2;
		}
		else
		{
			$content['eligible'] = 0;
		}
		
		$content['product_id'] = $this->common_model->getProdectId('offers',$offerId,1);
		$amontDetails = $this->common_model->getProductFee($content['product_id'], $this->currId);
		$content['product_fee'] = $amontDetails['amount'];
		$content['currency_code'] =  $amontDetails['currency_code'];
		$content['curr_id'] =  $amontDetails['currency_id'];
		$content['currency_symbol'] =  $amontDetails['currency_symbol'];
		
		
		/*if($content['eligible']==0 || $content['eligible']==2)
		{
			$this->session->set_flashdata('message','Offer is not valid for your course');
			redirect('coursemanager/campus');
		}*/
	
		
		$content['translate'] = $this->tr_common;
		$content['view'] = 'course_upgrade';
		$title['pageTitle'] = 'Course Upgrade';
		$content['content'] = $title;
		$this->load->view('user/outerTemplate',$content); 
		
		
	}
	function paymentConfirm($offerId)//date : 26-Nov-2013 - for adding new makeup modules for existing students 
	{
		/*inserting top ourses pages*/
		$top_menu_base_courses = $this->user_model->get_courses($this->language);
		if(empty($top_menu_base_courses))
		$top_menu_base_courses = $this->user_model->get_courses(4);
		
				
		
		$content['top_menu_base_courses'] 			= $top_menu_base_courses;
		
		$content['offerDetails'] = $this->offer_model->getOfferDetails($offerId);
		$content['product_id'] = $this->common_model->getProdectId('offers',$offerId,1);
		$amontDetails = $this->common_model->getProductFee($content['product_id'], $this->currId);
		$content['product_fee'] = $amontDetails['amount'];
		$content['currency_code'] =  $amontDetails['currency_code'];
		$content['curr_id'] =  $amontDetails['currency_id'];
		$content['stud_id'] =  $this->session->userdata['student_logged_in']['id'];
		
		
		$content['translate'] = $this->tr_common;
		$content['view'] = 'offer_payment';
		$data1['pageTitle'] = "Payment Confirm";
		$content['content'] =$data1;
		$this->load->view('user/outerTemplate',$content); 
		
		
	}
	function upgrade_afterPay($offerId,$paymentId)//date : 26-Nov-2013 - for adding new makeup modules for existing students 
	{
		if(!isset($this->session->userdata['student_logged_in']))
		{
			redirect("home");
		}
		
			$content['offerDetails'] = $this->offer_model->getOfferDetails($offerId);
			$content['product_id'] = $this->common_model->getProdectId('offers',$offerId,1);
			$user_id =  $this->session->userdata['student_logged_in']['id'];
			if(isset($paymentId)&&$paymentId!=0&&$paymentId!="")
			{
				if($this->session->userdata['language']==4)
				$courseId = 3;
				else if($this->session->userdata['language']==3)
				$courseId = 13;
				
				
				$studentCourseArr = $this->user_model->check_user_registered($user_id,$courseId);
				$serial = $studentCourseArr[0]->student_course_units;
				$user_curr_units = unserialize($serial);
				 $keys = array_keys($user_curr_units);
				for($u=0;$u<count($user_curr_units);$u++)
				{
					$unit_id[]=$user_curr_units[$keys[$u]];
				}
				//echo "<br>------unitIds-------<br><pre>";print_r($unit_id);exit;
				$usersUnit = $this->user_model->get_courseunits_id($courseId);
				foreach($usersUnit as $row)
				{
					$un[$row->units_order] = $row->course_units;
				}
				$student_courseData['student_course_units'] = serialize($un);
				$dateNow = date('Y-m-d');
				if($studentCourseArr[0]->date_expiry>$dateNow)
				$student_courseData['date_expiry'] = date('Y-m-d', strtotime($studentCourseArr[0]->date_expiry. ' + 15 days'));
				else
				$student_courseData['date_expiry'] = date('Y-m-d', strtotime($dateNow. ' + 15 days'));
				
				if($studentCourseArr[0]->course_status=='7')
				$student_courseData['course_status']='1';
				
				//echo "<br>------updating student enroll-------<br><pre>";print_r($student_courseData);exit;
				$this->user_model->update_student_enrollments($courseId,$user_id,$student_courseData);/*updating student enroll*/

				$user_score_details = $this->user_model->get_userCourse_score($user_id,$courseId);
				if(!empty($user_score_details))
				{
					$makeUp_old_new = $this->offer_model->get_makeUp_old_new_arr();
					foreach($makeUp_old_new as $old_new)
					{
						$makeUp_old[]=$old_new->old_task_id;
						$makeUp_oldPage[] = $old_new->old_page_id;
					}
					foreach($user_score_details as $scores)
					{
						$taskId = $scores->task_id;
						if(in_array($taskId,$makeUp_old))
						{
							$scoreDetails['page_id']=$this->offer_model->newPage_of_oldTask($taskId);
							//echo "<br>------updating score details-------<br><pre>";print_r($scoreDetails);
							$this->user_model->update_student_scores($user_id,$courseId,$taskId,$scoreDetails);/*updating student score*/
						}
						
						
					}
				}
				else
				{
					$makeUp_oldPage=array();
				}
			for($x=0;$x<count($unit_id);$x++)
			{
				$user_record_details = $this->user_model->get_userCourse_records($user_id,$courseId,$unit_id[$x]);
				//echo "<pre>";print_r($user_record_details);exit;
				if(!empty($user_record_details))
				{
					foreach($user_record_details as $recodrds)
					{
						$serial_cur_saved =  $recodrds->course_pages;
						$cur_saved =unserialize($serial_cur_saved); 
						for($i=0;$i<count($cur_saved);$i++)
						{
							if(in_array($cur_saved[$i],$makeUp_oldPage))
							{
								$new_saved[] = $this->offer_model->newPage_of_oldPage($cur_saved[$i]);
							}
						}
						if(!empty($new_saved))
						{
							$serial_new_saved = serialize($new_saved);
							
							$recordData['course_pages'] = $serial_new_saved;
							$recordData['unit_id'] = $this->offer_model->newUnit_of_oldUnit($unit_id[$x]);;
							
							$this->db->where('user_id',$user_id);
							$this->db->where('course_id',$courseId);
							$this->db->where('unit_id',$unit_id[$x]);
      						$this->db->update('course_records',$recordData);
						}
						else
						{
							$this->db->where('user_id',$user_id);
							$this->db->where('course_id',$courseId);
							$this->db->where('unit_id',$unit_id[$x]);
							$this->db->delete('course_records');
							
						}
						
						
					}
				}
			}
			$resumeArr['resume_link']="coursemanager/studentcourse/".$courseId;
			$resumeArr['user_id']=$user_id;
			$resumeArr['course_id']=$courseId;
			
			$this->user_model->updateStudentCoursePage($resumeArr);	
				
				
			}
			else
			{
				echo "payment id not set";
			}
			redirect('home/offerSuccess');
	}
	function user_login($password,$username)
 	{
   					//Field validation succeeded.  Validate against database
   					//$username = $this->input->post('username');

   					//query the database
   					$result = $this->user_model->login($username, $password);
					if($result)
   					{
     					$sess_array = array();
     					foreach($result as $row)
     					{ // echo $row->active;
               if ($row->status!=1) {
                
                  $this->session->set_flashdata('err_msg','student is not active');
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
						
     					$this->session->set_flashdata('err_msg','Invalid code');
    					return false;
   					}
				
				
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
	function paymentConfirm_test($offerId)//date : 26-Nov-2013 - for adding new makeup modules for existing students 
	{
		if( !isset($this->session->userdata['student_logged_in']))
		redirect("offers/upgradeCourse/".$offerId,"refresh");
		/*inserting top ourses pages*/
		$top_menu_base_courses = $this->user_model->get_courses($this->language);
		if(empty($top_menu_base_courses))
		$top_menu_base_courses = $this->user_model->get_courses(4);
		
				
		
		$content['top_menu_base_courses'] 			= $top_menu_base_courses;
		
		$content['offerDetails'] = $this->offer_model->getOfferDetails($offerId);
		$content['product_id'] = $this->common_model->getProdectId('offers',$offerId,1);
		$amontDetails = $this->common_model->getProductFee($content['product_id'], $this->currId);
		$content['product_fee'] = $amontDetails['amount'];
		$content['currency_code'] =  $amontDetails['currency_code'];
		$content['curr_id'] =  $amontDetails['currency_id'];
		$content['stud_id'] =  $this->session->userdata['student_logged_in']['id'];
		
		
		$content['translate'] = $this->tr_common;
		$content['view'] = 'offer_payment';
		$data1['pageTitle'] = "Payment Confirm";
		$content['content'] =$data1;
		$this->load->view('user/outerTemplate',$content); 
		
		
	}
	function test_after_upgrade($offerId,$paymentId)
	{
		
		if(!isset($this->session->userdata['student_logged_in']))
		{
			//redirect("home");
			echo "redirect home";
		}
		
			$content['offerDetails'] = $this->offer_model->getOfferDetails($offerId);
			$content['product_id'] = $this->common_model->getProdectId('offers',$offerId,1);
			$user_id =  $this->session->userdata['student_logged_in']['id'];
			if(isset($paymentId)&&$paymentId!=0&&$paymentId!="")
			{
				if($this->session->userdata['language']==4)
				$courseId = 3;
				else if($this->session->userdata['language']==3)
				$courseId = 13;
				
				echo "<br>Course Id = ".$courseId;
				
				
				$studentCourseArr = $this->user_model->check_user_registered($user_id,$courseId);
				echo "<pre><br>------------------studCourseArr=------------";print_r($studentCourseArr);
				$serial = $studentCourseArr[0]->student_course_units;
				$user_curr_units = unserialize($serial);
				 $keys = array_keys($user_curr_units);
				for($u=0;$u<count($user_curr_units);$u++)
				{
					$unit_id[]=$user_curr_units[$keys[$u]];
				}
				echo "<br>------unitIds-------<br><pre>";print_r($unit_id);
				$usersUnit = $this->user_model->get_courseunits_id($courseId);
					echo "<br>------user unitIds-------<br><pre>";print_r($usersUnit);
				foreach($usersUnit as $row)
				{
					$un[$row->units_order] = $row->course_units;
				}
					echo "<br>------unserialise new unit array-------<br><pre>";print_r($un);
				$student_courseData['student_course_units'] = serialize($un);
				$dateNow = date('Y-m-d');
				if($studentCourseArr[0]->date_expiry>$dateNow)
				$student_courseData['date_expiry'] = date('Y-m-d', strtotime($studentCourseArr[0]->date_expiry. ' + 15 days'));
				else
				$student_courseData['date_expiry'] = date('Y-m-d', strtotime($dateNow. ' + 15 days'));
				
				if($studentCourseArr[0]->course_status=='7')
				$student_courseData['course_status']='1';
				
				echo "<br>------updating student enroll-------<br><pre>";print_r($student_courseData);
				echo "<br><br>update course_enrollments";
				//$this->user_model->update_student_enrollments($courseId,$user_id,$student_courseData);/*updating student enroll*/

				$user_score_details = $this->user_model->get_userCourse_score($user_id,$courseId);
				echo "<br>------User score details-------<br><pre>";print_r($user_score_details);
				if(!empty($user_score_details))
				{
					echo "<br><br>this is the confusion place... entered here??";
					$makeUp_old_new = $this->offer_model->get_makeUp_old_new_arr();
					foreach($makeUp_old_new as $old_new)
					{
						$makeUp_old[]=$old_new->old_task_id;
						$makeUp_oldPage[] = $old_new->old_page_id;
					}
					foreach($user_score_details as $scores)
					{
						$taskId = $scores->task_id;
						if(in_array($taskId,$makeUp_old))
						{
							$scoreDetails['page_id']=$this->offer_model->newPage_of_oldTask($taskId);
						echo "<br>------updating score details-------<br><pre>";print_r($scoreDetails);
							//$this->user_model->update_student_scores($user_id,$courseId,$taskId,$scoreDetails);/*updating student score*/
						}
						
						
					}
				}
				else
				{
					$makeUp_oldPage=array();
				}
			for($x=0;$x<count($unit_id);$x++)
			{
				$user_record_details = $this->user_model->get_userCourse_records($user_id,$courseId,$unit_id[$x]);
				echo "<br><br><br>-----user_course_record details<pre>";print_r($user_record_details);
				if(!empty($user_record_details))
				{
					foreach($user_record_details as $recodrds)
					{
						$serial_cur_saved =  $recodrds->course_pages;
						$cur_saved =unserialize($serial_cur_saved); 
						for($i=0;$i<count($cur_saved);$i++)
						{
							if(in_array($cur_saved[$i],$makeUp_oldPage))
							{
								$new_saved[] = $this->offer_model->newPage_of_oldPage($cur_saved[$i]);
							}
						}
						if(!empty($new_saved))
						{
							$serial_new_saved = serialize($new_saved);
							
							$recordData['course_pages'] = $serial_new_saved;
							$recordData['unit_id'] = $this->offer_model->newUnit_of_oldUnit($unit_id[$x]);;
							
							$this->db->where('user_id',$user_id);
							$this->db->where('course_id',$courseId);
							$this->db->where('unit_id',$unit_id[$x]);
      						//$this->db->update('course_records',$recordData);
							echo "<br>updates course recoreds";
						}
						else
						{
							$this->db->where('user_id',$user_id);
							$this->db->where('course_id',$courseId);
							$this->db->where('unit_id',$unit_id[$x]);
							//$this->db->delete('course_records');
							echo "<br>deleted course recoreds";
							
						}
						
						
					}
				}
			}
			$resumeArr['resume_link']="coursemanager/studentcourse/".$courseId;
			$resumeArr['user_id']=$user_id;
			$resumeArr['course_id']=$courseId;
			
			//$this->user_model->updateStudentCoursePage($resumeArr);	
				
				
			}
			else
			{
				echo "payment id not set";
			}
			//redirect('home/offerSuccess');
	
	}
	
}