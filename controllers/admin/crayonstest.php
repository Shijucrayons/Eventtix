<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class crayonstest extends CI_Controller {

	 
	function __construct()
	{
		parent::__construct();
		/*$this->load->model('student_model','',TRUE);
		$this->load->library('encrypt');
		$this->load->model('crayons_test_model','',TRUE);
		$this->load->model('user_model','',TRUE);*/
		
		// testing
		//$this->load->library('encrypt');
		//$this->load->library('session');
		$this->load->model('student_model','',TRUE);
		$this->load->model('user_model','',TRUE);
		$this->load->model('common_model');
		
		/*if(!$this->session->userdata('admin_logged_in'))
   			redirect('admin/login', 'refresh');
   		
   		if($message = $this->session->flashdata('message')){
          $this->flashmessage =$message;
   		}

   		$this->load->helper(array('form'));
		$this->load->library('form_validation');*/
		// testing
		
    }
	
	function index()
	{

	}

	function get_version() {

    echo "CI version-"; echo CI_VERSION; // echoes something like 2.3.1
	echo "<br>";
	echo "PHP version- ".phpversion();
	echo "<br>";
	echo "MySQL Version-";
	echo $version = $this->crayons_test_model->get_mysql_version();

	}

	function getuser($id)
	{
		$content['user_details']=$this->student_model->fetchdata($id);
		
		$userDetails = $content['user_details'];
		
		foreach ($userDetails as $key) {
  			 $username =$key->userName ;
     		echo $password =$key->passWord;	
		
   		 }
		 
		$pass = $this->encrypt->decode($password,'MD5');
		
		 echo "pass- ".$pass;
		//echo "<pre>";
		//$userDetails = $content;
		//print_r($userDetails);
		
		  //	$data['view'] = 'crayonstestview';
    		//$data['content'] = $content;
    		//$this->load->view('admin/template',$data);

		
		
	}

	function excelExport(){
		$this->load->helper(array('php-excel'));
		$sql = $this->user_model->get_courses(4);
	   	$fields = (	$field_array[] = array ("ID", "Course Name", "Summary")  );
	   
	   	foreach ($sql as $row)
			 {
			 $data_array[] = array( $row->course_id, $row->course_name, $row->course_summary );
			 }
			 
		   $xls = new Excel_XML;
		   $xls->addArray ($field_array);
		   $xls->addArray ($data_array);
		   //print_r($data_array);
		   $xls->generateXML ( "course_list" );
	}
	
	
}