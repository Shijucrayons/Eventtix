<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class deepsFinder extends CI_Controller {

	 
	function __construct()
	{
		parent::__construct();
		$this->load->model('student_model','',TRUE);
		$this->load->library('encrypt');
		$this->load->model('common_model','',TRUE);
		
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
		 $pass = $this->encrypt->decode($password(MD5));
		 //$pass = $this->encrypt->decode($password);
		//$pass = $this->encrypt->encode('trendimi123','MD5');
		
		
		 echo "<br>newwwwwwwwwwwwwwwwwwwwwpass- ".$pass;
		//echo "<pre>";
		//$userDetails = $content;
		//print_r($userDetails);
		
		  //	$data['view'] = 'crayonstestview';
    		//$data['content'] = $content;
    		//$this->load->view('admin/template',$data);

		
		
	}
	function translator($word)
	{
		$content['user_details']=$this->student_model->translate_($label);
		
		echo "<pre>";
		print_r($content);
		echo $content['user_details'];exit;
		
	}
	
function paging()
	{	
	
	$this->load->library('pagination');

$config['base_url'] = base_url().'admin/deepsFinder/paging';
$config['total_rows'] = 200;
$config['per_page'] = 20; 
$config['use_page_numbers'] = TRUE;
$config["uri_segment"] = 4;

$this->pagination->initialize($config); 

echo $this->pagination->create_links();
}
	
	
	
}