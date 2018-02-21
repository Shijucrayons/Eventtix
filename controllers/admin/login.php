<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class login extends CI_Controller {

	 
	function __construct()
	{
		parent::__construct();
		$this->load->model('admin_model','',TRUE);
		
		/*$this->load->library('geoip_lib');
		$ip = $this->input->ip_address();
    	$this->geoip_lib->InfoIP($ip);
    	$this->code3= $this->geoip_lib->result_country_code3();
     	$this->con_name = $this->geoip_lib->result_country_name();*/
$this->load->library('ip2country_lib');
		$this->con_name = $this->ip2country_lib->getInfo();
    }
 	 
	public function index()
	{
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		$content =array();
		if(isset($_POST['username'])&&isset($_POST['password']))
		{
			 	$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
   				$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_check_database');
				$content['username'] = $this->input->post('username');
				if(!$this->form_validation->run() == FALSE)
				{
     				//Go to private area
     				
					redirect('admin/home', 'refresh');
   					
   				}
			
					
		}
		
		$data['view'] = 'login';
	    $data['content'] = $content;
	    $this->load->view('admin/login',$data);

	}
	function check_database($password)
 	{
   					//Field validation succeeded.  Validate against database
   					$username = $this->input->post('username');

   					//query the database
   					$result = $this->admin_model->login($username, $password);
					if($result)
   					{
     					$sess_array = array();
     					foreach($result as $row)
     					{
       						$sess_array = array(
							'adminId' => $row->id,
							'adminname'=>$row->name,
							'admintype'=>$row->admintype,
							'last_login'=>$row->last_login
							);
       						$this->session->set_userdata('admin_logged_in', $sess_array);
							$login_detail['last_login'] =  date('Y-m-d H:i:s');
							//$login_detail['last_login_country'] =  $this->con_name;
							
							
							$this->db->where('id',$row->id);
							$this->db->update('admin_new',$login_detail);
     					}
						
						
     					return TRUE;
   					}
					else
   					{
						
     					$this->form_validation->set_message('check_database','Invalid username or password');
    					return false;
   					}
				
				
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */