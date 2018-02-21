<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class logout extends CI_Controller {

	 
	function __construct()
	{
		parent::__construct();
    } 	 
	public function index()
	{
		$this->session->unset_userdata('admin_logged_in');
	   
	   redirect('admin/login', 'refresh');
	}
	
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */