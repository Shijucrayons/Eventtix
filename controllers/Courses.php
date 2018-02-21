<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Courses extends CI_Controller
{
      
     function __construct()
     {

          parent::__construct();
          $this->load->library('session');
          $this->load->library('encrypt');
          $this->load->library('user_agent');
          $this->load->helper(array('form'));
          $this->load->helper('text');
          //$this->load->helper(array('language'));
          $this->load->library('form_validation');
          $this->load->model('user_model','',TRUE);
          $this->load->model('common_model','',TRUE);
          $this->load->model('certificate_model','',TRUE);
          $this->load->model('gift_voucher_model','',TRUE);
          $this->load->model('sales_model','',TRUE);
          $this->load->model('ebook_model','',TRUE);
          $this->load->model('package_model','',TRUE); 
          $this->load->model('campaign_model','',TRUE);
          $this->load->model('course_model','',TRUE);
         // $this->load->model('free_course_model');    
         // $this->load->model('control_panel/manage_admin_model','',TRUE); 

          //echo $this->input->ip_address();
          if($message = $this->session->flashdata('message')){
               $this->flashmessage =$message;
          }

          //$this->load->library('geoip_lib');
          $ip = $this->input->ip_address();
          //$this->geoip_lib->InfoIP($ip);

          $this->load->library('ip2country_lib');
          $this->con_name = $this->ip2country_lib->getInfo();


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



          if(isset($_POST['username'])&&isset($_POST['password']))
          {
               $langid=$this->session->userdata('language');
               if(isset($langid)&&($langid==6))
               {
                    $this->form_validation->set_rules('username', 'Username', 'trim|requis|xss_clean');
                    $this->form_validation->set_rules('password', 'Password', 'trim|requis|xss_clean|callback_check_database');
               }
               elseif(isset($langid)&&($langid==3))
               {
                    $this->form_validation->set_rules('username', 'Username', 'trim|necesario|xss_clean');
                    $this->form_validation->set_rules('password', 'Password', 'trim|necesario|xss_clean|callback_check_database');
               }
               elseif(isset($langid)&&($langid==4))
               {
                    $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
                    $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_check_database');
               }
               $content['username'] = $this->input->post('username');
               if($this->form_validation->run() == TRUE)
               {//Go to private area
                    redirect('coursemanager/campus', 'refresh');
               }
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

          //---------------common translations --------------------------
          $this->tr_common['tr_forget_password']          = $this->user_model->translate_('forget_password');          
          $this->tr_common['tr_Ebooks']                   = $this->user_model->translate_('Ebooks');
          $this->tr_common['tr_stylist_id']               = $this->user_model->translate_('stylist_id');
          $this->tr_common['tr_code']                     = $this->user_model->translate_('style_code');
          $this->tr_common['tr_return_campus']            = $this->user_model->translate_('tr_return_campus');
          $this->tr_common['tr_sign_out']                 = $this->user_model->translate_('tr_sign_out');
          $this->tr_common['tr_Select']                   = $this->user_model->translate_('Select');
          $this->tr_common['tr_name']                     = $this->user_model->translate_('name');
          
          $this->tr_common['tr_about_us']                 =$this->user_model->translate_('about_us');
          $this->tr_common['tr_faq']                      =$this->user_model->translate_('faq');         
          $this->tr_common['tr_contact_us']               =$this->user_model->translate_('contact_us');
          
          $this->tr_common['tr_change_photo']             =$this->user_model->translate_('change_foto');            
          $this->tr_common['tr_edit_details']             = $this->user_model->translate_('edit_your_account_details');
          $this->tr_common['tr_change_password']          = $this->user_model->translate_('change_password');
          $this->tr_common['tr_help_center']              = $this->user_model->translate_('help_center');        
          $this->tr_common['tr_extend_course']            = $this->user_model->translate_('extend_course');
          $this->tr_common['tr_certificate']              = $this->user_model->translate_('certificate');
          $this->tr_common['tr_contact_us']               = $this->user_model->translate_('contact_us');
          $this->tr_common['tr_fitting_room']             =$this->user_model->translate_('fitting_room');
          $this->tr_common['tr_free_brochure']            =$this->user_model->translate_('free_brochure');
          $this->tr_common['tr_enrol_now']                =$this->user_model->translate_('enrol_now');
          
          $this->tr_common['tr_terms_use']                =$this->user_model->translate_('terms_use');       
          $this->tr_common['tr_privacy_policy']           =$this->user_model->translate_('privacy_policy');
          $this->tr_common['tr_work_with_us']             =$this->user_model->translate_('work_with_us');
          $this->tr_common['tr_testimonials']             =$this->user_model->translate_('testimonials');
          $this->tr_common['tr_accreditation']            =$this->user_model->translate_('accreditation');
          
          $this->tr_common['tr_login']                    =$this->user_model->translate_('login');
          $this->tr_common['tr_buy_now']                  =$this->user_model->translate_('buy_now');
          $this->tr_common['tr_get_free_brochure']        =$this->user_model->translate_('get_free_brochure');
          $this->tr_common['tr_who_we_are']               =$this->user_model->translate_('who_we_are');
          $this->tr_common['tr_Meet_the_team']            =$this->user_model->translate_('Meet_the_team');
          $this->tr_common['tr_why_knowledge_box_campus'] =$this->user_model->translate_('why_knowledge_box_campus');
          $this->tr_common['tr_why_knowledge_box']        =$this->user_model->translate_('why_knowledge_box');
          
          $this->tr_common['tr_Courses']                  =$this->user_model->translate_('Courses');
          $this->tr_common['tr_Home']                     =$this->user_model->translate_('home');
           $this->tr_common['tr_why_us']                     =$this->user_model->translate_('Why us');
          
          
          $this->tr_common['tr_price']                    = $this->user_model->translate_('price');
          $this->tr_common['tr_Syllabus']                 = $this->user_model->translate_('Syllabus');
          
          $this->tr_common['tr_Hours']                    = $this->user_model->translate_('Hours');

          //************************************** Anoop ******************************************
          $this->tr_common['tr_tell_us_what_you_think']                   = $this->user_model->translate_('tell_us_what_you_think');
          $this->tr_common['tr_CHECK_OUT_OUR_COURSES']                    = $this->user_model->translate_('CHECK_OUT_OUR_COURSES');
          $this->tr_common['tr_CHECK_OUT_OUR_COURSES_text']               = $this->user_model->translate_('CHECK_OUT_OUR_COURSES_text');
          $this->tr_common['tr_get_to_know_them']                         = $this->user_model->translate_('get_to_know_them');
          $this->tr_common['tr_registration']                             = $this->user_model->translate_('registration');
          $this->tr_common['tr_YES']                                      = $this->user_model->translate_('YES');
          $this->tr_common['tr_NO']                                       = $this->user_model->translate_('NO');
          $this->tr_common['tr_step_4_buy_now']                           = $this->user_model->translate_('step_4_buy_now');          
          $this->tr_common['tr_TRENDIMI_COURSE_REVIEWS_AND_TESTIMONIALS'] = $this->user_model->translate_('TRENDIMI_COURSE_REVIEWS_AND_TESTIMONIALS');
          $this->tr_common['tr_highest_standards_happiest_students']      = $this->user_model->translate_('highest_standards_happiest_students');
          $this->tr_common['tr_total_price']                              = $this->user_model->translate_('total_price');
          $this->tr_common['tr_Help_Us_Improve']                          = $this->user_model->translate_('Help_Us_Improve');
          $this->tr_common['tr_Send_on_your_valuable_feedback']           = $this->user_model->translate_('Send_on_your_valuable_feedback');
          $this->tr_common['tr_Send']                                     = $this->user_model->translate_('Send');
          //************************************** Anoop ******************************************


          $this->tr_common['tr_enroll_now'] =$this->user_model->translate_('enrol_now');           
          $this->tr_common['tr_first_name'] =$this->user_model->translate_('name');           
          $this->tr_common['tr_email']      =$this->user_model->translate_('email');
          $this->tr_common['tr_telephone']  =$this->user_model->translate_('Telephone');
          $this->tr_common['tr_save']       =$this->user_model->translate_('save');

          $this->course_categories = $this->user_model->get_category_details_home();


          $this->language = $this->session->userdata('language');
          $this->student_status = $this->session->userdata('student_logged_in');
          $this->course=$this->user_model->get_courses($this->language);
          if(empty($this->course))
          {
               $this->course=$this->user_model->get_courses(4); // get english courses
          }

          $this->menu['top_menu']=$this->user_model->get_top_menu($this->language);
     }


     public function index()
     {
          
          $content = array();
          $content['base_course']=$this->course;
          $content['our_team_div']=$this->user_model->get_our_team_html("home",$this->language);
          $content['language']=$this->language;
          $content['student_status']=$this->student_status;
          $content['topmenu']=$this->menu;
          $content['main_page'] = 1;    

          $content['sliding_courses']=$this->user_model->get_sliding_courses($this->language);

          foreach($content['base_course'] as $courses)
          {
               $prodectId = $this->common_model->getProdectId("course",$courses->course_id);
               $content['product_details'][$courses->course_id] = $this->common_model->getProductFee($prodectId,$this->currId);
          }


          $this->tr_common['tr_buy_now'] = $this->user_model->translate_('buy_now');
          $this->tr_common['tr_as_part_of_our_statutory_mission'] = $this->user_model->translate_('as_part_of_our_statutory_mission');
          $this->tr_common['tr_learn_how_to_expertly_care_for'] = $this->user_model->translate_('learn_how_to_expertly_care_for');

          //$seo_details = $this->common_model->get_seo_details(1);
          $seo_details = $this->common_model->get_seo_details('courses',$this->language);

          foreach($seo_details as $seo_det)
          {
          $content['pageTitle'] =  $seo_det->pageTitle;
          $content['metaKeys'] = $seo_det->metaKeys;
          $content['metaDesc'] = $seo_det->metaDesc;
          }

          $data['translate'] = $this->tr_common;
          $data['view'] = 'home';
          $data['content'] = $content;
          $this->load->view('user/template_new',$data);
     }


     function details_old($courseId)
     {
              
               $course_details =$this->user_model->get_coursedetails($courseId, $this->language);
               //echo "<pre>"; print_r($course_details); exit;
               $syllabus = $this->user_model->get_course_syllabus($courseId);
               $content['course_name'] = $course_details[0]->course_name;
               $content['course_summary'] = $course_details[0]->course_summary;
               $content['home_image'] = $course_details[0]->home_image_new;
               $content['home_video'] = $course_details[0]->home_video;
               $content['course_detail'] = $course_details[0]->course_description;
               //$content['course_syllabus'] = $syllabus[0]->syllabus_text;
               $content['course_syllabus'] ='';
               $content['module_count'] =$course_details[0]->modules;
               //$content['student_count'] =$syllabus[0]->student_count;
               $content['student_count'] ='';
               $content['study_hours'] =$course_details[0]->course_hours;
               $content['course_id'] = $courseId;
               $content['course_details'] = $course_details;

               $modules = $this->user_model->get_courseunits_id($courseId);
               $i=0;
               if($modules)
               {
                  foreach($modules as $row)
                    {
                         $this->db->select('*');
                         $this->db->from('unit_courses');
                         $this->db->where('id',$row->course_units);
                         $query = $this->db->get();
                         foreach($query->result() as $row2)
                         {
                              $content['modules'][$i] = $row2->unit_name;
                              $content['sub_head'][$i] = explode('||',$row2->headings);
                         }
                         $i++;
                    }  
               }
               


               if(isset($this->session->userdata['student_logged_in']))
               {

                    $product_id = $this->common_model->getProdectId('extra_course');
               }
               else
               {              
                    $product = $this->user_model->get_product_id($courseId);

                    foreach ($product as $value) {
                     $product_id = $value->id;
                   }
               }
               
               $price_details_array = $this->common_model->getProductFee($product_id,$this->currId);
               
               foreach($price_details_array as $price_det)
               {
                    $content['amount']= $price_details_array['amount'];
                    $content['currency_symbol']= $price_details_array['currency_symbol'];
                    $content['currency_code']=  $price_details_array['currency_code'];
                    $content['currency_id']=  $price_details_array['currency_id'];
                    
               }



               $data['translate'] = $this->tr_common;       
              $data['view'] = 'default_template';
          $data['content'] = $content;
          $this->load->view('user/template_outer',$data);
               
          

          //===============================================================================================
          


         //  $content                   = array();        
         //  $content['language']       = $this->language;
         //  //$content['drop_down_base_course']  = $this->drop_down_base_course;        
         //  $content['course_id']      = $course_id;         

         //  $content['categories'] = $this->user_model->get_category_details_home(); 

         //  $course_detail = $this->course_model->get($course_id,$this->currId);

         //  $course_template_details = $this->course_model->get_course_templates_details($course_id,$course_detail[0]->course_details_template_id);

         //  //echo "<pre>";print_r($course_template_details);exit;
         //  $course_testimonial_ids = explode(',',$course_detail[0]->course_testimonial_ids);
         //  $testimonials = $this->course_model->get_course_testimonials($course_testimonial_ids);

         //  $this->tr_common['tr_buy_now'] = $this->user_model->translate_('buy_now');
         //  $this->tr_common['tr_as_part_of_our_statutory_mission'] = $this->user_model->translate_('as_part_of_our_statutory_mission');
         //  $this->tr_common['tr_learn_how_to_expertly_care_for'] = $this->user_model->translate_('learn_how_to_expertly_care_for');

         // // $seo_details = $this->common_model->get_seo_details(1);
         //  /*$seo_details = $this->common_model->get_seo_details('courses',$this->language);

         //  foreach($seo_details as $seo_det)
         //  {
         //       $content['pageTitle'] = $seo_det->pageTitle;
         //       $content['metaKeys']  = $seo_det->metaKeys;
         //       $content['metaDesc']  = $seo_det->metaDesc;
         //  }*/
          
         //  $content['pageTitle'] = $course_detail[0]->page_title;
         //  $content['metaKeys']  = $course_detail[0]->meta_key;
         //  $content['metaDesc']  = $course_detail[0]->meta_desc;

         //  $added_courses = array();

         //   //echo $this->session->userdata('course_cart_main_id'); exit;

         //  if($this->session->userdata('course_cart_main_id'))
         //  {
         //       $cart_main_details=$this->user_model->get_cart_main_details($this->session->userdata('course_cart_main_id'));

         //       if(isset($cart_main_details))
         //       {  
         //            foreach ($cart_main_details as $cart_items) 
         //            {            
         //                 $added_courses[] = $cart_items->selected_item_ids; 
         //            }         
         //       }
         //  }

         //  if($course_detail[0]->course_details_template_id ==1)
         //  {
         //       $related_course_limit = 4;
         //  }
         //  else
         //  {
         //       $related_course_limit = 5;              
         //  }


         //  $related_courses = array();
         //  $related_courses = $this->course_model->get_course_by_category($course_detail[0]->category_id,$course_id,$course_detail[0]->language_id,$this->currId,$related_course_limit);
         //  if(count($related_courses) < $related_course_limit)
         //  {
         //       if($related_courses)
         //       {
         //            $limit_count = $related_course_limit - count($related_courses);                 
         //       }
         //       else
         //       {
         //            $limit_count = $related_course_limit;                  
         //       }

         //       $related_courses_2 = $this->course_model->get_random_coures($course_id,$course_detail[0]->category_id,$limit_count,$course_detail[0]->language_id,$this->currId);
         //       if($related_courses)
         //       {
         //            $related_courses = array_merge($related_courses,$related_courses_2);                 
         //       }
         //       else
         //       {
         //            $related_courses = $related_courses_2;
         //       }
         //  }

         //  if($course_detail[0]->fake_amount!=0 && $course_detail[0]->fake_amount > $course_detail[0]->amount)
         //  {
         //       $discount_percentage = intval((($course_detail[0]->fake_amount - $course_detail[0]->amount)/$course_detail[0]->amount)*100);
         //  }
         //  else
         //  {
         //       $discount_percentage = 0;
         //  }

         //  $course_detail[0]->discount_percentage = $discount_percentage;    
         

         //  $content['added_courses'] = $added_courses;
         //  $content['related_courses'] = $related_courses;

         //  //$course_modules = $this->course_model->get_course_modules($course_id);       
         //  $course_syllabus = $this->course_model->get_course_syllabus($course_id); 
          
         //  $content['course_syllabus']         = $course_syllabus;        
         //  $content['testimonials']            = $testimonials;
         //  $content['course_detail']           = $course_detail;
         //  $content['course_template_details'] = $course_template_details;
         //  $data['translate']                  = $this->tr_common;

         //  $data['view'] = 'courses/details_template_'.$course_detail[0]->course_details_template_id;         
         //  $data['content'] = $content;
         //  $this->load->view('user/template_outer_course',$data);
                   
     }



     function details($courseId)
     {
          
          $course_details =$this->user_model->get_coursedetails($courseId, $this->language);
          $course_syllabus = $this->course_model->get_course_modules($courseId); 
          //echo "<pre>";print_r($course_syllabus);exit;
          $content['course_name'] = $course_details[0]->course_name;
          $content['course_summary'] = $course_details[0]->course_summary;
          $content['home_image'] = $course_details[0]->home_image;

          $content['home_video'] = $course_details[0]->home_video;
          $content['course_detail'] = $course_details[0]->course_description;
          $content['course_id'] = $courseId;
          $content['course_details'] = $course_details;
          $content['module_count'] =$course_details[0]->modules;
           $content['student_count'] ='';
          $course_detail = $this->course_model->get($courseId,$this->currId);
          //echo "<pre>";print_r($course_detail);exit;
          $author_id=unserialize($course_detail[0]->author_id);
          //$author_details=$this->manage_admin_model->get_author_details($author_id);
          $course_template_details = $this->course_model->get_course_templates_details($courseId,$course_detail[0]->course_details_template_id);
          //echo "<pre>";print_r($course_template_details);exit;
          $course_testimonial_ids = explode(',',$course_detail[0]->course_testimonial_ids);
          $testimonials = $this->course_model->get_course_testimonials($course_testimonial_ids);


          if(isset($this->session->userdata['student_logged_in']))
          {

               $product_id = $this->common_model->getProdectId('extra_course');
          }
          else
          {              
               $product = $this->user_model->get_product_id($courseId);

               foreach ($product as $value) {
                $product_id = $value->id;
              }
          }
          
          $price_details_array = $this->common_model->getProductFee($product_id,$this->currId);
          
          foreach($price_details_array as $price_det)
          {
               $content['amount']= $price_details_array['amount'];
               $content['currency_symbol']= $price_details_array['currency_symbol'];
               $content['currency_code']=  $price_details_array['currency_code'];
               $content['currency_id']=  $price_details_array['currency_id'];
               
          }



          $data['translate'] = $this->tr_common; 
          $content['course_detail']           = $course_detail; 
          $content['course_syllabus']          = $course_syllabus;      
          //$data['view'] = 'coursedetail_all';
          $data['view'] = 'Courses/details_template_'.$course_detail[0]->course_details_template_id;
          $data['content'] = $content;
          $this->load->view('user/template_outer',$data);
          
     }



     function get_courses_by_key()
     {
          $key_word = $_POST['search_course_key'];         
          $get_courses_search = $this->course_model->get_courses_by_key($key_word,$this->language);

          $course_name='';
          if(!empty($get_courses_search))
          {
               $i=0;
               foreach($get_courses_search as $course)
               {
                    $course_name = $course_name."<li><a href='".base_url()."".$course->course_url."'>".$course->course_name."</a></li>";
                    $i++;
               }
          }
               
          $data['err_msg']= 0;    
          $data['courses'] =$course_name;        
          echo json_encode($data); 
          exit;     
     }

     function course_module_details($courseId)
     {
          $this->load->model('course_model');
          $this->tr_common['tr_course_syllabus'] =$this->user_model->translate_('course_syllabus');
          $this->tr_common['tr_course_info'] =$this->user_model->translate_('course_info');
          
          //***************** sample course change start ****************************
          $langId=$this->language;
          $langId = $this->course_model->get_lang_course($courseId);
          if($this->session->userdata('language')!=$langId)
          {
          $newlangdata = array(
                   'language'  => $langId
               );
               $this->session->set_userdata($newlangdata);
               ?>
            <script>
               window.location.reload();
               </script>
            <?
               
          }
          
          if($this->session->userdata('student_logged_in')){
          $stud_id  = $this->session->userdata['student_logged_in']['id'];
          $base_courses = $this->user_model->get_courses($langId); 
      foreach ($base_courses as $row) {
        $enrolled=$this->user_model->check_user_registered($stud_id,$row->course_id); // check user registered with this course 
           
               if($enrolled) // if enrollled
               {
                    foreach ($enrolled as $value)
                    {
                         $data['sample_course']=$this->user_model->check_sample_course_or_not($stud_id,$row->course_id);
                         if(!empty($data['sample_course']) && $data['sample_course']==TRUE){
                         $data['sample_course_user']="sample_course_user"; 
                         }
                        else{
                          $data['strong_user']="yes"; 
                         }
          
                    }
                    
               }
                    
           }
     
          }
          else{
          $data['strong_user']="yes";   
          }
          //***************** sample course change end ****************************

          $content['tr_enroll_now'] = $this->user_model->translate_('enrol_now');
          
          $content['base_course']=$this->user_model->get_courses_by_order_for_home_syllabus($this->language);;      
          $modules = $this->user_model->get_courseunits_id($courseId);
          $i=0;
          foreach($modules as $row)
          {
               $this->db->select('*');
               $this->db->from('unit_courses');
               $this->db->where('id',$row->course_units);
               $query = $this->db->get();
               foreach($query->result() as $row2)
               {
                    $content['modules'][$i] = $row2->unit_name;
                    $content['sub_head'][$i] = explode('||',$row2->headings);
               }
               $i++;
          }
          
          
          
          
          
          foreach($content['base_course'] as $courses)
          {
               $prodectId = $this->common_model->getProdectId("course",$courses->course_id);
               $content['product_details'][$courses->course_id] = $this->common_model->getProductFee($prodectId,$this->currId);
          }
          
          
          //echo "<pre>";print_r($content);exit;
          $content['coursedetail'] = $this->user_model->get_coursedetails($courseId,$this->session->userdata['language']);
          //echo "<pre>";print_r($content['coursedetail']);exit;        
          $data['course_id'] =$courseId;
          $data['course_name'] = $this->common_model->get_course_name($courseId); 
          $data['translate'] = $this->tr_common;
          $data['view'] = 'syllabus_view';
          $langId = $this->language;    
          $content['pageTitle'] = 'Course Details';
          
          $data['content'] =$content;
          $this->load->view('user/template_outer_new',$data);
     }
     
}

?>