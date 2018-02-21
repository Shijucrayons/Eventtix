<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 //session_start(); //we need to call PHP's session object to access it through CI
class social_login extends CI_Controller {

     public function __construct()
     {

          parent::__construct();
          $this->load->library('session');
          $this->load->library('encrypt');
          $this->load->library('user_agent');
          $this->load->helper(array('form'));
          $this->load->helper('text');
          $this->load->library('form_validation');
          $this->load->model('user_model','',TRUE);
          $this->load->model('common_model','',TRUE);
          $this->load->model('social_login_model','',TRUE);

          if($message = $this->session->flashdata('message'))
          {
               $this->flashmessage =$message;
          }

          $ip = $this->input->ip_address();
          $this->load->library('ip2country_lib');
          $this->con_name = $this->ip2country_lib->getInfo();

          if(isset($_POST['username'])&&isset($_POST['password']))
          {
               $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
               $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_check_database');
               $content['username'] = $this->input->post('username');
               if($this->form_validation->run() == TRUE)
               {
                    redirect('coursemanager/campus', 'refresh');
               }else
               {
                    $this->session->set_flashdata('loagin_failed',"Invalid username and password");
                    redirect('home', 'refresh');
               }
          }

          if(isset($_GET['lang_id']))
          {
               $newdata = array(
                    'language'  => $_GET['lang_id']
                    );
               $this->session->set_userdata($newdata);
               $ref = $this->input->server('HTTP_REFERER', TRUE);
               redirect($ref, 'location'); 

          }
          elseif(!$this->session->userdata('language'))
          {
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
          else 
          {
               $this->currId=1;
               $this->currencyCode='EUR';
               $this->currSymbol = '&euro;';
          }


   
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
          $this->language = $this->session->userdata('language');
          $this->student_status = $this->session->userdata('student_logged_in');
          $this->course=$this->user_model->get_courses_order($this->language);
          if(empty($this->course))
          {
               $this->course=$this->user_model->get_courses_order(4); // get english courses
          }

          $this->menu['top_menu']=$this->user_model->get_top_menu($this->language);
     }

    


     public function facebook_login()
     { 
          //echo $this->con_name; exit;
         
          $dataarray['lang_id']=$this->session->userdata('language');
          $dataarray['first_name']=$this->input->post('first_name');
          $dataarray['last_name']=$this->input->post('last_name');
          if($this->input->post('gender')=='male')
          {
             $dataarray['gender']= '1';  
          }
          else
          {
              $dataarray['gender']= '2';  
          }
          
          $dataarray['email']=$email=$this->input->post('email'); 
          $dataarray['username']=$this->input->post('email');
          $dataarray['reg_type']='website_nocourse';  
          $dataarray['user_course_type']='no_course';   
          $dataarray['user_type']='registered_lead';
          $token=$this->input->post('access_key');
          $dataarray['reg_date']= date('Y-m-d H:i:s');
          $dataarray['source_registration']='facebook'; 
          $password=uniqid();
          $dataarray['password']=$this->encrypt->encode($password);
          $dataarray['status']='1';
          $where=array('email'=>$email); 

          $loginarray=$this->social_login_model->get_login_data_by_users($where); 
      
         

        
          $action=$this->input->post('action');
       
          if(empty($loginarray) && $email!='')
          {
                //echo "<pre>email"; print_r($dataarray); 
              
               $insert_id=$this->social_login_model->put_login_data_by_users($dataarray);

               $sess_array = array('id' => $insert_id,'username' =>  $dataarray['username'] ,'method'=>'facebook','token'=>$token);
               $this->session->set_userdata('student_logged_in', $sess_array);

               //--- For adding User to Campaign_monitor subscribers List  STARTS -------

               $name = $dataarray['first_name'].' '.$dataarray['last_name'] ;
               $this->load->library('campaign_monitor_lib');

               $length = strlen($password);
               $first = (4- $length);
               $last = ($length -4);
               $password = substr($password, 0, $first). str_repeat('*', $last);

                $CustomFields = array(
                    array(
                         'Key' => 'Regdate',
                         'Value' => date("Y-m-d")
                         ),
                    array(
                         'Key' => 'User_name',
                         'Value' => $email
                         ),
                    array(
                         'Key' => 'Password',
                         'Value' => $password
                         ),
                    array(
                         'Key' => 'Lead_Source',
                         'Value' => 'Facebook'
                         )

                    
                    );
                $subscribers = array('EmailAddress' => $email, 'Name' => $name, 'CustomFields' => $CustomFields);
                $list_id = "10e9c2cd23f6c853de5f85c9cb0ddb29";
                $result = $this->campaign_monitor_lib->add_subsribers($list_id,$subscribers);

               //--- For adding User to Campaign_monitor subscribers List  ENDS-----------

               $sess_array1 = array('language' => 4);
               $this->session->set_userdata($sess_array1);
               $login_detail['last_login'] =  date('Y-m-d H:i:s');
               $this->db->where('user_id',$insert_id);
               $this->db->update('users',$login_detail);
               $dataarray1['user_id']=$insert_id; 
               $dataarray1['type']='facebook'; 
               $dataarray1['access_key']=$this->input->post('access_key');
               $insert_id1=$this->social_login_model->put_social_media_login_data($dataarray1);
               $langId = 4;
               $en_studId = $this->encrypt->encode($insert_id);//encoding student id
               $some_data['message']="Successfully Registered";  
               $some_data = json_encode($some_data);
               print_r($some_data);exit;

               
          }
          else
          {
                //echo "<pre>else"; print_r($email); exit;
               if($email!='')
               {

                    if($loginarray[0]->merged_to > 0)
                    { 
                         $loginarray = $this->social_login_model->get_user_details($loginarray[0]->merged_to);
                    }
                   
                    $sess_array = array('id' => $loginarray[0]->user_id,'username' =>  $loginarray[0]->username ,'method'=>'facebook','token'=>$token);
                    $this->session->set_userdata('student_logged_in', $sess_array);
                    $sess_array1 = array('language' =>4);
                    $this->session->set_userdata($sess_array1);
                    $login_detail['last_login'] =  date('Y-m-d H:i:s');
                    $this->db->where('user_id',$loginarray['0']->user_id);
                    $this->db->update('users',$login_detail);
                    $reg_userid= $loginarray[0]->user_id;
 
                    $some_data['message']="regular_vc";     
                    $some_data = json_encode($some_data);
                    print_r($some_data);exit;
               }
               else
               {
                    $some_data['message']="invalid_login";  
                    $some_data = json_encode($some_data);
                    print_r($some_data);exit;
               }
          }  
     }



    


     public function google_login()
     { 

          $dataarray['lang_id']=$this->session->userdata('language');
          $dataarray['first_name']=$this->input->post('first_name');
          $dataarray['last_name']=$this->input->post('last_name');
          $dataarray['email']=$email=$this->input->post('email'); 
          $dataarray['username']=$this->input->post('email');  
          $dataarray['user_course_type']='no_course';
          $dataarray['user_type']='registered_lead';
          $dataarray['reg_type']='website_nocourse';  
          $dataarray['source_registration']='google'; 
          $password=uniqid();
          $dataarray['password']=$this->encrypt->encode($password);
          $dataarray['status']='1';
          $dataarray['reg_date']= date('Y-m-d H:i:s');
          $where=array('email'=>$email) ; 
          $loginarray=$this->social_login_model->get_login_data_by_users($where); 

          $action=$this->input->post('action');
          $token=$this->input->post('access_key');

          if(empty($loginarray) && $email!='')
          {

                 
               $insert_id=$this->social_login_model->put_login_data_by_users($dataarray);

               $sess_array = array('id' => $insert_id,'username' =>  $dataarray['username'] ,'method'=>'google','token'=>$token);
               $this->session->set_userdata('student_logged_in', $sess_array);

               //--- For adding User to Campaign_monitor subscribers List  STARTS -------

               $name = $dataarray['first_name'].' '.$dataarray['last_name'] ;
               $this->load->library('campaign_monitor_lib');

               $length = strlen($password);
               $first = (4- $length);
               $last = ($length -4);
               $password = substr($password, 0, $first). str_repeat('*', $last);

                $CustomFields = array(
                    array(
                         'Key' => 'Regdate',
                         'Value' => date("Y-m-d")
                         ),
                    array(
                         'Key' => 'User_name',
                         'Value' => $email
                         ),
                    array(
                         'Key' => 'Password',
                         'Value' => $password
                         ),
                    array(
                         'Key' => 'Lead_Source',
                         'Value' => 'Gmail'
                         )

                    
                    );
                $subscribers = array('EmailAddress' => $email, 'Name' => $name, 'CustomFields' => $CustomFields);
                $list_id = "10e9c2cd23f6c853de5f85c9cb0ddb29";
                $result = $this->campaign_monitor_lib->add_subsribers($list_id,$subscribers);
                $some_data['cm']= $result;

               //--- For adding User to Campaign_monitor subscribers List  ENDS-----------

               $sess_array1 = array('language' => 4);
               $this->session->set_userdata($sess_array1);
               $login_detail['last_login'] =  date('Y-m-d H:i:s');
               $this->db->where('user_id',$insert_id);
               $this->db->update('users',$login_detail);
               $dataarray1['user_id']=$insert_id; 
               $dataarray1['type']='google'; 
               $dataarray1['access_key']=$this->input->post('access_key');
               $insert_id1=$this->social_login_model->put_social_media_login_data($dataarray1);
               $langId = 4;
               $en_studId = $this->encrypt->encode($insert_id);//encoding student id
               $some_data['message']="Successfully Registered";  

               $some_data = json_encode($some_data);
               print_r($some_data);exit;
               
          }
          else
          {
               if($email!='')
               {

                    if($loginarray[0]->merged_to > 0)
                    { 
                         $loginarray = $this->social_login_model->get_user_details($loginarray[0]->merged_to);
                    }

                    $sess_array = array('id' => $loginarray[0]->user_id,'username' =>  $loginarray[0]->username ,'method'=>'google','token'=>$token);
                    $this->session->set_userdata('student_logged_in', $sess_array);
                    $sess_array1 = array('language' =>4);
                    $this->session->set_userdata($sess_array1);
                    $login_detail['last_login'] =  date('Y-m-d H:i:s');
                    $this->db->where('user_id',$loginarray['0']->user_id);
                    $this->db->update('users',$login_detail);
                    $reg_userid= $loginarray[0]->user_id;
                    
                    $some_data['message']="regular_vc";     
                    $some_data = json_encode($some_data);
                    print_r($some_data);exit;
                  
               }
               else
               {
                    $some_data['message']="invalid_login";  
                    $some_data = json_encode($some_data);
                    print_r($some_data);exit;
               }
          }  
     }



      function logout(){
     
           if($this->session->userdata['student_logged_in']['method'])
           {

               if($this->session->userdata['student_logged_in']['method']=="google")
               {
                    $this->session->unset_userdata('student_logged_in');
               $this->session->unset_userdata('cart_session_id');
               redirect('https://www.google.com/accounts/Logout?continue=https://appengine.google.com/_ah/logout?continue='.base_url(), 'refresh');
                    
               }
               else
               {
               $this->session->unset_userdata('student_logged_in');
               $this->session->unset_userdata('cart_session_id');
               redirect(base_url());
                    
               }
          }
          else
          {

          $this->session->unset_userdata('student_logged_in');
          $this->session->unset_userdata('cart_session_id');
          redirect(base_url());

          }
          
     }

     function submit_register_deatils()
     {

          $content = array();

          $user_data['first_name']             = $this->input->post('first_name');
          $user_data['last_name']              = $this->input->post('last_name');
          $user_data['email']                  = $email = $this->input->post('email');
          $user_data['username']               = $this->input->post('email');             
          $user_data['reg_date']               = date('Y-m-d H:i:s');
          $password                            = $this->input->post('password');               
          $user_data['password']               = $this->encrypt->encode($password);
          $user_data['status'] = '1';
          $user_data['lang_id'] = '4';
          $data_user['user_detail'] = $user_data;

          $where=array('email'=>$email) ; 
          $loginarray=$this->social_login_model->get_login_data_by_users($where); 
          if(empty($loginarray))
          {
               $userid=$this->social_login_model->put_login_data_by_users($user_data);
               $userdatails=$this->social_login_model->get_user_details($userid);
               $sess_array = array();                                                
               $sess_array = array('id' => $userdatails[0]->user_id,'method'=>'normal' );
               $this->session->set_userdata('student_logged_in', $sess_array);

               $some_data['message'] = "campus";  
               $some_data = json_encode($some_data);
               print_r($some_data);
               exit;               
          }    
          else
          {
               $some_data['message'] = "exist";    
               $some_data = json_encode($some_data);
               print_r($some_data);
               exit;
          }


     }

     public function user_login_check()
     { 
          $email =$this->input->post('email');  
          $password  =$this->input->post('password');
          if($email==NULL && $password==NULL)
          {
               $some_data['message']="Please enter your username and password";   
               $some_data = json_encode($some_data);
               print_r($some_data);exit;
          }
          else if($email==NULL && $password!=NULL)
          {
               $some_data['message']="Please enter your username";   
               $some_data = json_encode($some_data);
               print_r($some_data);exit;  
          }
          else if($email!=NULL && $password==NULL)
          {
               $some_data['message']="Please enter your password";   
               $some_data = json_encode($some_data);
               print_r($some_data);exit;  
          }
          else
          {    
               $result = $this->social_login_model->login($email,$password);
               if($result)
               {
                    $sess_array = array();
                    foreach($result as $row)
                    { 
                         if ($row->status!=1)
                         {

                              $this->form_validation->set_message('check_database','student is not active');
                              $some_data['message']="no";   
                              $some_data = json_encode($some_data);
                              print_r($some_data);exit;
                         }

                         else
                         {
                              if($row->merged_to > 0)
                              { 
                                   $loginarray = $this->social_login_model->get_user_details($row->merged_to);
                                   $sess_array = array('id' => $loginarray[0]->user_id,'username' =>  $loginarray[0]->username ,'method'=>'normal');
                                   $this->session->set_userdata('student_logged_in', $sess_array);
                                   $sess_array1 = array('language' => $loginarray[0]->lang_id);
                                   $this->session->set_userdata($sess_array1);
                                   $login_detail['last_login'] =  date('Y-m-d H:i:s');
                                   $this->db->where('user_id',$loginarray[0]->user_id);
                                   $this->db->update('users',$login_detail);
                                   
                              }
                              else
                              {
                                   $sess_array = array('id' => $row->user_id,'username' => $row->username,'method'=>'normal' );
                                   $this->session->set_userdata('student_logged_in', $sess_array);
                                   $sess_array1 = array('language' => $row->lang_id);
                                   $this->session->set_userdata($sess_array1);
                                   $login_detail['last_login'] =  date('Y-m-d H:i:s');
                                   $this->db->where('user_id',$row->user_id);
                                   $this->db->update('users',$login_detail);
                                   ;   
                              }
                              $some_data['message']="home"; 
                              $some_data = json_encode($some_data);
                              print_r($some_data);exit;
                   
                    
                             
                              
                         }
                    }

               }
               else
               {

                    $this->session->set_flashdata('message_login_popup',"<p>Oops, it seems it didn't go through…</p>
                    <p>When entering your login credentials please note that password field is case-sensitive. Make sure also that for both fields there are no spaces left at the beginning and at the end.
                    </p>
                    <p>If still doesn't work please contact us at info@eventtrix.com send us the discount code and we will help you to take advantage of this offer.</p>");

                    $this->form_validation->set_message('check_database','Invalid stylist ID or code'); 
                 
                    $some_data['message']="invalid";   
                    $some_data = json_encode($some_data);
                    print_r($some_data);exit;
               }
          }
     }


     

}//End

