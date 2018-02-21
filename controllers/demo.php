<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class demo extends CI_Controller {

	 
	function __construct()
	{
		parent::__construct();
		$this->load->model('course_model','',TRUE);
		$this->load->model('common_model','',TRUE);
		$this->load->model('user_model','',TRUE);
	
		

   		 $this->load->helper(array('form'));
		$this->load->library('form_validation');
   		
	   	$this->load->library('Datatables');
        $this->load->library('table');
        $this->load->database();
		
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
		
//		$top_menu_base_courses = $this->user_model->get_courses($this->language);


		$this->language = $this->session->userdata('language');
		
		$this->course=$this->user_model->get_courses($this->language);
		if(empty($this->course))
		{
			$this->course=$this->user_model->get_courses(4); // get english courses
		}
		
		$this->menu['top_menu']=$this->user_model->get_top_menu($this->language);

    }
	function index()
	{

	}
	function videos()
	{
		 $content = array();
		 $video_content ='';
        if(isset($this->flashmessage))
        $data['flashmessage'] = $this->flashmessage;
		$course_id =$this->uri->segment(3);
		//echo "<br> Course id ".$course_id;
		
		$coursename = $this->common_model->get_course_name($course_id);
		
		$slNo =$this->uri->segment(4);
		//echo "<br>Sl no ".$slNo;
		
		$this->db-> select('*');
        $this->db-> from('course_demo_videos');
		$this->db-> where('course_id',$course_id);
		$this->db-> where('sl_no',$slNo);		
        $query = $this -> db -> get();
       
            if ($query->num_rows == 1)
            {
				$result=$query -> result();
				
				/*echo "<pre>";
				print_r($result);*/
				foreach($result as $row){
					
		$this->db-> select('*');
        $this->db-> from('content_page');
		$this->db-> where('content_page_id',$row->content_id);	
        $query1 = $this -> db -> get();
       
            if ($query1->num_rows == 1)

            {
				$result1=$query1 -> result();
				foreach($result1 as $row1){
					$video_content = $row1->content;
				
				}
				
			}
				}
			}
		$top_menu_base_courses = $this->user_model->get_courses($this->language);	
		if(empty($top_menu_base_courses))
		{
			$top_menu_base_courses = $this->user_model->get_courses(4);	
		}
		$content['coursename'] = $coursename;
		$content['video_content'] = $video_content;
		
		$data['top_menu_base_courses'] 			= $top_menu_base_courses;
		
		
			
	    $data['translate'] = $this->tr_common;
        $data['content'] = $content;
        $this->load->view('user/sample_video_template',$data);
		
	}
	
	function testvideo (){
		
		$this->load->view('test/test_video.html','');
	}
}