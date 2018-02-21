<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class cms extends CI_Controller
 {
	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper(array('form'));
    $this->load->helper('url');
    $this->load->database('',true);
    //$this->load->helper(array('language'));
		$this->load->library('form_validation');
		$this->load->model('cms_model','',TRUE);
		$this->load->model('user_model','',TRUE);
		//echo $this->input->ip_address();
		/*$this->load->library('geoip_lib');*/
		$ip = $this->input->ip_address();
   /* $this->geoip_lib->InfoIP($ip);
    $this->code3= $this->geoip_lib->result_country_code3();
    $this->con_name = $this->geoip_lib->result_country_name();*/
		 $this->load->library('ip2country_lib');
	$this->con_name = $this->ip2country_lib->getInfo();
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
		//$this->course['base_course']=$this->user_model->get_courses($this->language);
		$this->course=$this->user_model->get_courses_order($this->language);
		if(empty($this->course))
		{
			$this->course=$this->user_model->get_courses_order(4); // get english courses
		}
		$this->menu['top_menu']=$this->user_model->get_top_menu($this->language);
		
		//---------------common translations --------------------------
		
		$this->tr_common['tr_why_us']   =$this->user_model->translate_('why_us');
		 $this->tr_common['tr_about_us']   =$this->user_model->translate_('about_us');
		 $this->tr_common['tr_faq']        =$this->user_model->translate_('faq');		 
    	 $this->tr_common['tr_contact_us'] =$this->user_model->translate_('contact_us');		
		 $this->tr_common['tr_return_to']   =$this->user_model->translate_('return_to');
		 $this->tr_common['tr_campus']   =$this->user_model->translate_('campus');
		 $this->tr_common['tr_sign']   =$this->user_model->translate_('sign');
		 $this->tr_common['tr_Out']   =$this->user_model->translate_('Out');
		 $this->tr_common['tr_return_campus']   =$this->user_model->translate_('tr_return_campus');
		 $this->tr_common['tr_sign_out']        =$this->user_model->translate_('tr_sign_out');
		
			$this->tr_common['tr_eventrix']   =$this->user_model->translate_('eventrix');
		 $this->tr_common['tr_user_name']      =$this->user_model->translate_('user_name');
         $this->tr_common['tr_password']            =$this->user_model->translate_('password');
		 
		$this->tr_common['tr_terms_use']        =$this->user_model->translate_('terms_use');		 
    	$this->tr_common['tr_privacy_policy'] =$this->user_model->translate_('privacy_policy');

  }
 	function index(){
		
		
	}
	
	function cmsPage($id)
	{
		$content= array();
		$newResult = $this->cms_model->fetchdata($id,$this->language);
		if(!empty($newResult))
		{
			foreach($newResult as $row1)
			{
				$content['pageTitle']   =$row1->page_title;
				$content['pageHead']    =$row1->page_title;
				$content['pageContent'] =$row1->page_desc;
			}
		}
	
	$top_menu_base_courses = $this->user_model->get_courses($this->language);	
		
		if(empty($top_menu_base_courses))
		{
			$top_menu_base_courses = $this->user_model->get_courses(4);	
		}			
		$data['top_menu_base_courses'] 			= $top_menu_base_courses;
	 $data['translate'] = $this->tr_common;
    $data['view'] = 'cms';
    $data['content'] = $content;
    $this->load->view('user/template_outer',$data);
		
	}
function cmsOuter($id)
	{
		$content= array();
		$content['base_course']=$this->user_model->get_courses($this->language);
		$content['our_team_div']=$this->user_model->get_our_team_html("home");
		$newResult = $this->cms_model->fetchdata($id,$this->language);
		if(!empty($newResult))
		{
			foreach($newResult as $row1)
			{
				$content['pageTitle']   =$row1->page_title;
				$content['pageHead']    =$row1->page_title;
				$content['pageContent'] =$row1->page_desc;
			}
		}
	$content['pageContent'] = str_replace("#our_team_html#",$content['our_team_div'],$content['pageContent']);
	
	$top_menu_base_courses = $this->user_model->get_courses($this->language);	
		
	 $data['translate'] = $this->tr_common;
    $data['view'] = 'cms';
    $data['content'] = $content;
    $this->load->view('user/template_outer',$data);
		
	}	

  }
 	
	