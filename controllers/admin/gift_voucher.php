<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class gift_voucher extends CI_Controller
{
 	function __construct()
 	{
  		parent::__construct();
  		
  		if(!$this->session->userdata('admin_logged_in'))
   		redirect('admin/login', 'refresh');
		
		$this->load->library('form_validation');
		$this->load->model('common_model','',TRUE);
		$this->load->model('gift_voucher_model','',TRUE);
		
		if($message = $this->session->flashdata('message')){
          $this->flashmessage =$message;
   		}
  		 		
 	}

 	function index()
 	{
 	
 	   
 	}
	
	function fetch_voucher()
	{
		$page = 1;	// The current page
		$sortname = 'idgiftVoucher';	 // Sort column
		$sortorder = 'desc';	 // Sort order
		$qtype = '';	 // Search column
		$query = '';	 // Search string
		$rp=10;
		// Get posted data
	    if (isset($_POST['page'])) {
		$page = $_POST['page'];
		}
		if (isset($_POST['sortname'])) {
		$sortname = $_POST['sortname'];
		}
		if (isset($_POST['sortorder'])) {
		$sortorder = $_POST['sortorder'];
		}
		if (isset($_POST['rp'])) {
		$rp = $_POST['rp'];
		}
		
		 $this->db-> select('*');
		
		if(isset($_GET['voucher_code']) && $_GET['voucher_code']!=''){
			
			$this->db->like('giftVoucherCode', $_GET['voucher_code']); 			

		}
		if(isset($_GET['voucher_site']) && $_GET['voucher_site']!=''){
			$this->db->where('website',$_GET['voucher_site']); 

		}
		if(isset($_GET['end_date']) && $_GET['end_date']!=''){
			$this->db->where('enddate >', $_GET['end_date']); 

		}
		if(isset($_GET['start_date']) && $_GET['start_date']!=''){
			$this->db->where('startdate <', $_GET['start_date']); 

		}
		if(isset($_GET['course_id']) && $_GET['course_id']!=''){
			$this->db->where('courses_idcourses', $_GET['course_id']); 

		}
		
		if(isset($_GET['status']) && $_GET['status']!=''){
			$this->db->where('active',$_GET['status']); 

		}
		
		
		
		// Setup paging SQL
		$pageStart = ($page-1)*$rp;
       
        
       
		$this->db-> from('giftvoucher');
		
		
		/*if(isset($_GET['translation_identifier']) && $_GET['translation_identifier']!=''){
			$this->db->where('lable_identifier', $_GET['translation_identifier']); 

		}*/
		
		
		
		$this->db->order_by($sortname,$sortorder);
		//$this->db->limit($rp,$pageStart);
        $query = $this->db->get();        
        
        $data = array();
		$data['page'] = $page;
		$data['total'] = $query -> num_rows();
		$data['rows'] = array();
		
		$total_array = $query->result();
		$result = array_slice($total_array,$pageStart,$rp);  
        
        if($query -> num_rows() >0 )
		{
			//$result = $query -> result();
			
			foreach($result as $row){
				
				 if($row->active==0)
				  $enable = '<strong><a href="'.base_url().'admin/gift_voucher/enable_disable_voucher/'.$row->idgiftVoucher.'">Enable</a></strong>';
				else
					$enable='<a href="'.base_url().'admin/gift_voucher/enable_disable_voucher/'.$row->idgiftVoucher.'">Disable</a>';
			
			
			    $action = '<a href="'.base_url().'admin/gift_voucher/voucher_edit/'.$row->idgiftVoucher.'">Edit</a>&nbsp;&nbsp;<a href="'.base_url().'admin/gift_voucher/voucher_delete/'.$row->idgiftVoucher.'">Delete</a>';
			   
				 $data['rows'][] = array(
				'id' => $row->idgiftVoucher,
				'cell' => array($row->giftVoucherCode,$row->enddate,$row->active,$action,$enable)
			);
			}
			
		}
      
             
       echo json_encode($data); 
		
		
		
	}
	
	function browse_voucher()
	{
		$content = array();
		
		$data['view'] = 'gift_voucher_browse';
		$content['searchmode'] = false;		
		$content['website']=$this->gift_voucher_model->get_gift_voucher_website();
		$content['language']=$this->common_model->get_languages();
		$content['course']=$this->common_model->get_parent_courses();
		
        $data['content'] = $content;        
        $this->load->view('admin/template',$data);
		
		
	}
	
	function search_voucher()
	{
		$voucher_code = $this->input->post('v_code');
		$voucher_site = $this->input->post('v_site');
		$course_id       = $this->input->post('course_name');
		$end_date     = $this->input->post('v_end_date');		
		$start_date   = $this->input->post('v_start_date');
		$status       = $this->input->post('v_status');
		
		if($status==2)
		{
			$status='';
		}
		
		$content['voucher_code'] = isset($voucher_code)?$voucher_code:'';
		$content['voucher_site'] = isset($voucher_site)?$voucher_site:'';
		$content['course_id'] = isset($course_id)?$course_id:'';
		$content['end_date'] = isset($end_date)?$end_date:'';
		$content['start_date'] = isset($start_date)?$start_date:'';
		$content['status'] = isset($status)?$status:'';
		
		$content['searchmode'] = true;
		
		$content['website']=$this->gift_voucher_model->get_gift_voucher_website();
		$content['language']=$this->common_model->get_languages();
		$content['course']=$this->common_model->get_parent_courses();
		$data['view'] = 'gift_voucher_browse';
		
        $data['content'] = $content;
        
        $this->load->view('admin/template',$data);

		
	}
	
	function fetch_deal_sites()
	{
		$page = 1;	// The current page
		$sortname = 'id';	 // Sort column
		$sortorder = 'asc';	 // Sort order
		$qtype = '';	 // Search column
		$query = '';	 // Search string
		$rp=10;
		// Get posted data
	    if (isset($_POST['page'])) {
		$page = $_POST['page'];
		}
		if (isset($_POST['sortname'])) {
		$sortname = $_POST['sortname'];
		}
		if (isset($_POST['sortorder'])) {
		$sortorder = $_POST['sortorder'];
		}
		if (isset($_POST['rp'])) {
		$rp = $_POST['rp'];
		}
		
		// Setup paging SQL
		$pageStart = ($page-1)*$rp;
       
        
        $this->db-> select('*');
		$this->db-> from('giftvoucher_websites');
		
		
		/*if(isset($_GET['translation_identifier']) && $_GET['translation_identifier']!=''){
			$this->db->where('lable_identifier', $_GET['translation_identifier']); 

		}*/
		
		
		
		$this->db->order_by($sortname,$sortorder);
		//$this->db->limit($rp,$pageStart);
        $query = $this->db->get();        
        
        $data = array();
		$data['page'] = $page;
		$data['total'] = $query -> num_rows();
		$data['rows'] = array();
		   
		$total_array = $query->result();
		$result = array_slice($total_array,$pageStart,$rp);  
        
        if($query -> num_rows() >0 )
		{
			
			
			foreach($result as $row){
			
			    $action = '<a href="'.base_url().'admin/gift_voucher/edit_deal_sites/'.$row->id.'">Edit</a>&nbsp;&nbsp;<a href="'.base_url().'admin/gift_voucher/delete_deal_sites/'.$row->id.'">Delete</a>';
			   
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($row->site_name,$action)
			);
			}
			
		}
      
             
       echo json_encode($data); exit(); 
		
		
		
		
	}
	
	function browse_deal_sites()
	{
		$content = array();
		$data['view'] 		= 'gift_voucher_browse_deal_sites';
		$data['content']	= $content;
		$content['searchmode'] = true;
		$this->load->view('admin/template',$data);
	}
	function add_deal_sites()
	{
	
		$content = array();
		
		if(isset($_POST['save_site']))
		{
		
			$site_data  = array();
		    $site_data['site_name'] 	= ($this->input->post('site_name'));
			
		    			 
			$this->form_validation->set_rules('site_name', 'Site name', 'trim|required');
		
			
			if($this->form_validation->run())
			{	
			 	$this->gift_voucher_model->add_deal_sites($site_data);
			 	 $this->session->set_flashdata('message', 'Site added successfully!');
			 	 redirect('admin/gift_voucher/browse_voucher', 'refresh');
			}
		}
		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		
		if(isset($this->flashmessage)){
		$data['flashmessage'] = $this->flashmessage;
		}
		
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		$data['view'] = 'gift_voucher_add_deal_site';
		$data['mode'] = 0;
		$data['content'] = $content;
		$this->load->view('admin/template',$data);	
	}
	
	function add_gift_voucher($lang_id ='4')
	{
		
		$content = array();
		
		if(isset($_POST['save_gift_voucher']))
		{
		
			$gift_voucher_data  = array();
			
		    $gift_voucher_data['giftVoucherCode'] 	 = ($this->input->post('gift_code'));
    		//$gift_voucher_data['discountType'] 	  	 = ($this->input->post('discountType'));
			//$gift_voucher_data['giftVoucherValue']	 = ($this->input->post('discount_value'));
			$gift_voucher_data['giftVoucherValue']	 =100;
    		$gift_voucher_data['securitycode_req'] 	 = ($this->input->post('securityreq'));			
			$gift_voucher_data['website'] 		 	 = ($this->input->post('website'));
    		$gift_voucher_data['country_idcountry']  = ($this->input->post('country'));
			$gift_voucher_data['startdate'] 		 = ($this->input->post('start_date'));
    		$gift_voucher_data['enddate'] 			 = ($this->input->post('end_date'));
			$gift_voucher_data['extended_end_date'] 			 = ($this->input->post('extend'));
			//$gift_voucher_data['language'] 		 	 = ($this->input->post('language'));
			//echo "<br>---------after implode-------<pre>";print_r($this->input->post('courses'));exit;
			if($this->input->post('courses')!=0)
			$gift_voucher_data['courses_idcourses'] = implode(",",$this->input->post('courses'));
			
			  
			//$gift_voucher_data['courses_idcourses']  =  implode(",",($this->input->post('courseSelect')));
    		//$gift_voucher_data['package_id'] 		 = ($this->input->post('packages'));
			$gift_voucher_data['active'] 		 	 = 1;
			
		    			 
			$this->form_validation->set_rules('gift_code', 'Gift voucher code', 'trim|required');
			//$this->form_validation->set_rules('discountType', 'Discount type', 'trim|required');
			//$this->form_validation->set_rules('discount_value', 'Discount Value', 'trim|required');
			$this->form_validation->set_rules('start_date', 'Start date', 'trim|required');
			$this->form_validation->set_rules('end_date', 'End date', 'trim|required');
			$this->form_validation->set_rules('extend', 'Extended end date', 'trim|required');
			$this->form_validation->set_rules('courses', 'Course', 'required');
		
			
			if($this->form_validation->run())
			{	
			 	$this->gift_voucher_model->add_gift_voucher($gift_voucher_data);
			 	 $this->session->set_flashdata('message', 'Gift Voucher added successfully!');
			 	 redirect('admin/gift_voucher/add_gift_voucher', 'refresh');
			}
		}
		
		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		
		if(isset($this->flashmessage)){
		$data['flashmessage'] = $this->flashmessage;
		}
		
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		$data['view'] = 'gift_voucher_add';
		$content['country_list']=$this->common_model->get_country();
		$content['website']=$this->gift_voucher_model->get_gift_voucher_website();
		$content['language']=$this->common_model->get_languages();
		$content['course']=$this->common_model->get_base_courses($lang_id);		
		
		$content['lang_id']=$lang_id;
		//$content['packages']=$this->common_model->get_course_packages();
		$data['content'] = $content;
		$data['mode']=0;
		$this->load->view('admin/template',$data);	
	
	}
	
	function voucher_edit($id)
	{
		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		$this->load->model('course_model');
		$content = array();
		//$id = $_GET['id'];
		$pagedata = $this->gift_voucher_model->fetch_voucher($id);
	
		
		foreach($pagedata as $row){
			
			 $content['id'] 			    = $row->idgiftVoucher;
			 $content['voucher_code'] 	  = $row->giftVoucherCode;
			 $content['discountType'] 	  = $row->discountType;
     		 $content['discount_value']    = $row->giftVoucherValue;
			 $content['web_site'] 		  = $row->website;
			 $content['courses_idcourses'] = $row->courses_idcourses;		
			 $content['course_id_arr']     = explode(",",$row->courses_idcourses);			 
			 $content['start_date'] 	    = $row->startdate;
			 $content['end_date'] 		  = $row->enddate;
			 $content['extended_end_date'] = $row->extended_end_date;
     		 $content['securitycode_req']  = $row->securitycode_req;
			 $content['country_idcountry'] = $row->country_idcountry;
			 $content['pack_id'] 	 	   = $row->package_id;
			 $content['lang_id'] = '';
			 
			 if($content['course_id_arr'][0]!=0)
			 {
			 	$content['lang_id'] = $this->course_model->get_lang_course($content['course_id_arr'][0]);
			 }
			 
		}
		
		/*echo "<pre>";
		print_r($_POST);*/
	    
	//   if(isset($_POST['start_date']))	
	if(isset($_POST['save_gift_voucher']))   
		{
		
			$gift_voucher_data  = array();
			
		    $gift_voucher_data['giftVoucherCode'] 	 = ($this->input->post('gift_code'));
    		$gift_voucher_data['discountType'] 	  	 = ($this->input->post('discountType'));
			$gift_voucher_data['giftVoucherValue']	 = ($this->input->post('discount_value'));
    		$gift_voucher_data['securitycode_req'] 	 = ($this->input->post('securityreq'));			
			$gift_voucher_data['website'] 		 	 = ($this->input->post('website'));
    		$gift_voucher_data['country_idcountry']  = ($this->input->post('country'));
			$gift_voucher_data['startdate'] 		   = ($this->input->post('start_date'));
    		$gift_voucher_data['enddate'] 			 = ($this->input->post('end_date'));
			$gift_voucher_data['extended_end_date']  = ($this->input->post('extend'));
			
			// implode(",",$_POST['courseSelect']);
			if($this->input->post('courses')!=0)
			$gift_voucher_data['courses_idcourses'] = implode(",",$this->input->post('courses'));
			//$gift_voucher_data['courses_idcourses']  =  implode(",",($this->input->post('courseSelect')));
    		$gift_voucher_data['package_id'] 		 = ($this->input->post('packages'));
			//$gift_voucher_data['active'] 		 	 = 1;
			
		    			 
			$this->form_validation->set_rules('gift_code', 'Gift voucher code', 'trim|required');
			//$this->form_validation->set_rules('discountType', 'Discount type', 'trim|required');
			//$this->form_validation->set_rules('discount_value', 'Discount Value', 'trim|required');
			$this->form_validation->set_rules('start_date', 'Start date', 'trim|required');
			$this->form_validation->set_rules('end_date', 'Start date', 'trim|required');
			$this->form_validation->set_rules('extend', 'Extended end date', 'trim|required');
			$this->form_validation->set_rules('courses', 'Course', 'required');
		
			
			if($this->form_validation->run())
			{	
			
			/*echo "<pre>";
			print_r($gift_voucher_data);
			exit;*/
			
			
			 	$this->gift_voucher_model->edit_gift_voucher($gift_voucher_data,$id);
			 	 $this->session->set_flashdata('message', 'Gift Voucher updated successfully!');
			 	 redirect('admin/gift_voucher/browse_voucher', 'refresh');
			}
		}
		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		
		if(isset($this->flashmessage)){
		$data['flashmessage'] = $this->flashmessage;
		}
		
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		$data['view'] = 'gift_voucher_add';
		$content['country_list']=$this->common_model->get_country();
		$content['website']=$this->gift_voucher_model->get_gift_voucher_website();
		$content['language']=$this->common_model->get_languages();
		if($content['lang_id']!='')
		{
					$content['course']=$this->common_model->get_base_courses($content['lang_id']);
		}
		else
		{
			$content['course']=$this->common_model->get_base_courses(4);
		}
		
		//$content['packages']=$this->common_model->get_course_packages();
		$data['content'] = $content;
		$data['mode']=1;
		$this->load->view('admin/template',$data);	
				
		
		
	}
	
	function voucher_delete($id)
	{
			$this->gift_voucher_model->delete_gift_voucher($id);
			$this->session->set_flashdata('message', 'Gift Voucher deleted successfully!');
			redirect('admin/gift_voucher/browse_voucher', 'refresh');
	
	}
	
	
	function import_gift_voucher($lang_id ='4')
	{
		$this->load->helper(array('phpexcel'));
		
		$content = array();
		$voucher_data = array();
		if(isset($_POST['upload_gift_voucher']))
		{
		
			
		
			$gift_voucher_data  = array();
			
		    
    		//$gift_voucher_data['discountType'] 	  	 = ($this->input->post('discountType'));
			//$gift_voucher_data['giftVoucherValue']	 = ($this->input->post('discount_value'));
			$gift_voucher_data['giftVoucherValue']	 =100;
    		$gift_voucher_data['securitycode_req'] 	 = ($this->input->post('securityreq'));			
			$gift_voucher_data['website'] 		 	 = ($this->input->post('website'));
    		$gift_voucher_data['country_idcountry']  = ($this->input->post('country'));
			$gift_voucher_data['startdate'] 		 = ($this->input->post('start_date'));
    		$gift_voucher_data['enddate'] 			 = ($this->input->post('end_date'));
			$gift_voucher_data['extended_end_date'] 			 = ($this->input->post('extend'));
			//$gift_voucher_data['language'] 		 	 = ($this->input->post('language'));
			
			// implode(",",$_POST['courseSelect']);
			if($this->input->post('courses')!=0)
			$gift_voucher_data['courses_idcourses'] = implode(",",$this->input->post('courses'));
			//$gift_voucher_data['courses_idcourses']  =  implode(",",($this->input->post('courseSelect')));
    		//$gift_voucher_data['package_id'] 		 = ($this->input->post('packages'));
			$gift_voucher_data['active'] 		 	 = 1;
			
		    			 
		//	$this->form_validation->set_rules('vocher_excel', 'Select Giftvoucher excel', 'trim|required');
			//$this->form_validation->set_rules('discountType', 'Discount type', 'trim|required');
			//$this->form_validation->set_rules('discount_value', 'Discount Value', 'trim|required');
			$this->form_validation->set_rules('start_date', 'Start date', 'trim|required');
			$this->form_validation->set_rules('end_date', 'Endt date', 'trim|required');
			$this->form_validation->set_rules('extend', 'Extended end date', 'trim|required');
			$this->form_validation->set_rules('courses', 'Course', 'required');
		
			
			if($this->form_validation->run())
			{	
			
			/*echo "Hererererererrr";
			exit;*/
			
				$config['upload_path'] = 'public/admin/uploads/couponcodes/';
				$config['allowed_types'] = 'xlsx';
				$config['max_size']	= '100000';
				//$config['max_width']  = '1024';
				//$config['max_height']  = '768';
				
				
				$this->load->library('upload', $config);
				$this->upload->initialize($config);
		
				
				if ( $this->upload->do_upload('vocher_excel'))
				{
					$excel_content = array('upload_data' => $this->upload->data());
									
				}
				
				
				
				
				$excelrecords = excelReader('public/admin/uploads/couponcodes/'.$excel_content['upload_data']['file_name']);
				
				
				
				
				 for($i=0;$i<(count($excelrecords)-2);$i++)
				{
					$gift_voucher_data['giftVoucherCode'] 	 =$excelrecords[$i][0] ;
					
					if($gift_voucher_data['giftVoucherCode']!='')
					{
					 $this->gift_voucher_model->add_gift_voucher($gift_voucher_data);
					}
				}
				
				/*echo "<pre>";
				print_r($voucher_data);
				exit;
				*/
				
				//echo ""
				
			 	
			 	 $this->session->set_flashdata('message', 'Gift Voucher added successfully!');
			 	 redirect('admin/gift_voucher/browse_voucher', 'refresh');
			}
		}
		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		
		if(isset($this->flashmessage)){
		$data['flashmessage'] = $this->flashmessage;
		}
		
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		
		
		
		
		$data['view'] = 'gift_voucher_import';
		$content['country_list']=$this->common_model->get_country();
		$content['website']=$this->gift_voucher_model->get_gift_voucher_website();
		$content['language']=$this->common_model->get_languages();
		$content['course']=$this->common_model->get_base_courses($lang_id);
		$content['lang_id']=$lang_id;
		
		//$content['packages']=$this->common_model->get_course_packages();
		$data['content'] = $content;
	//	$data['mode']=0;
		$this->load->view('admin/template',$data);	
		
		
	}
	
//browsing redeemed vouchers

function redeemedCoupons()
	{
		$content = array();
		
		$data['view'] = 'redeemed_coupons';
		$data['searchmode'] = false;
        $data['content'] = $content;
        
        $this->load->view('admin/template',$data);
		
		
	}
	
	function fetch_redeemed_coupons()
	{
		$page = 1;	// The current page
		$sortname = 'redeemed_coupons.id';	 // Sort column
		$sortorder = 'asc';	 // Sort order
		$qtype = '';	 // Search column
		$query = '';	 // Search string
		$rp=10;
		// Get posted data
	    if (isset($_POST['page'])) {
		$page = $_POST['page'];
		}
		if (isset($_POST['sortname'])) {
		$sortname = $_POST['sortname'];
		}
		if (isset($_POST['sortorder'])) {
		$sortorder = $_POST['sortorder'];
		}
		if (isset($_POST['rp'])) {
		$rp = $_POST['rp'];
		}
		
		// Setup paging SQL
		$pageStart = ($page-1)*$rp;
       
        
       
		$this->db->select('redeemed_coupons.id as redId,coupon_code,date,redemption_code,course_name,first_name,last_name,site_name,pdf_name,email,contact_number');
		$this->db-> join('courses','redeemed_coupons.course_id = courses.course_id');
		$this->db-> join('users','redeemed_coupons.user_id = users.user_id');
		$this->db-> join('giftvoucher_websites','giftvoucher_websites.id = redeemed_coupons.website_id');
		
		
		
		if(isset($_GET['startDate'])&&$_GET['startDate']!=""){
			$this->db->where('date >=', $_GET['startDate']); 

		}
		if(isset($_GET['endDate'])&&$_GET['endDate']!=""){
			$this->db->where('date <', $_GET['endDate']); 

		}
		
		
		
		$this->db->order_by($sortname,$sortorder);
		//$this->db->limit($rp,$pageStart);
        $query= $this->db-> get('redeemed_coupons');
		
		    
        
        $data = array();
		$data['page'] = $page;
		$data['total'] = $query -> num_rows();
		
		$total_array = $query->result();
		
		$result = array_slice($total_array,$pageStart,$rp);
		
		
		$data['rows'] = array();
		     
       
        if($query -> num_rows() >0 )
		{
			//$result = $query -> result();
			   
			foreach($result as $row){
			
			    if($row->pdf_name=="")
				{
					$pdfLink = "";
				}
				else
				{
					$pdfLink = "<a href='".base_url()."public/uploads/deals/pdf/".$row->pdf_name."' target='_new'>View pdf</a>";
				}
							   
				 $data['rows'][] = array(
				'id' => $row->redId,
				'cell' => array($row->first_name." ".$row->last_name,$row->email,$row->contact_number,$row->course_name,$row->date,$row->coupon_code,$row->redemption_code,$row->site_name,$pdfLink)
			);
			}
			
		}
      
             
       echo json_encode($data); exit(); 
		
	
	}
	
	function search_redeemedCoupons(){
		$content = array();
        if(isset($this->flashmessage))
        $data['flashmessage'] = $this->flashmessage;

		$end_date=$this->input->post('end');
		$start_date=$this->input->post('start');
		$content['end'] = isset($end_date)?$end_date:'';
		$content['start'] = isset($start_date)?$start_date:'';
		$content['searchmode'] = true;
		$data['searchmode'] = true;
		
			if(isset($_POST['generate']))
			{
				$this->load->library('export');
				//$this->db->start_cache();
				 //$this->db-> select('*');
				// $this->db-> from('redeemed_coupons');
				 //$this->db-> join('users','redeemed_coupons.user_id = users.user_id');
			
			
				if(isset($content['start'])){
					$this->db->where('date >=', $content['start']); 
					$this->session->set_flashdata('start_date',$content['start']);
				}
				if(isset($content['end'])){
					$this->db->where('date <=', $content['end']); 
					$this->session->set_flashdata('end_date',$content['end']); 
				}
			//$this->db->stop_cache();
			
			redirect("deeps_home/genarator_reedemed");
			
			//$query = $this->db->get();
			//$result = $query->result();
			//echo "<pre>";print_r($result);
			//$this->session->set_userdata('generate_exel',$result);
			
			/*$sess_array = array('id' => $row->user_id,'username' => $row->username );
       						$this->session->set_userdata('student_logged_in', $sess_array);
							$sess_array1 = array('language' => $row->lang_id);
       						$this->session->set_userdata($sess_array1);*/
			
			//echo "<pre> no worry this is printing";print_r($this->session->userdata);
			
			$this->export->to_excel($result, 'test_excel');
				
			
			}
        $data['view'] = 'redeemed_coupons';
        $data['content'] = $content;
        
        $this->load->view('admin/template',$data);
		}
		
		function enable_disable_voucher($id){
		$content = array();
		 $active_status=$this->gift_voucher_model->get_active_status($id);
		
		 foreach ($active_status as $key) {
		 	 $act_status=$key->active;
		 }
			//  echo $act_status; exit;		 
		 if($act_status==0){
		 	$status['active']=1;
		 	$this->gift_voucher_model->update_active_status($id,$status);
		 }
		 else {
		 	$status['active']=0;
		 	$this->gift_voucher_model->update_disable_status($id,$status);
		}
        if(isset($this->flashmessage))
        $data['flashmessage'] = $this->flashmessage;
		$content['searchmode'] = false;
        $data['view'] = 'gift_voucher_browse';
        $data['content'] = $content;
        
        $this->load->view('admin/template',$data);

	}
	
	
	
	/*--- newly added-------*/

	function edit_deal_sites($id)
	 {
	 
	  $content = array();
	  $pagedata = $this->gift_voucher_model->fetch_site($id);
	 
	  foreach($pagedata as $row){
	   $content['site_name']=$row->site_name;
	   $content['id']=$id;
	  }
	
	  if(isset($_POST['save_site']))
	  {
	  
	   $site_data  = array();
		  $site_data['site_name']  = ($this->input->post('site_name'));
	   
			  
	   $this->form_validation->set_rules('site_name', 'Site name', 'trim|required');
	  
	   
	   if($this->form_validation->run())
	   { 
		 $this->gift_voucher_model->update_deal_sites($site_data,$id);
		  $this->session->set_flashdata('message', 'Site Updated successfully!');
		  redirect('admin/gift_voucher/browse_deal_sites', 'refresh');
	   }
	  }
	  
	  $this->load->helper(array('form'));
	  $this->load->library('form_validation');
	  
	  if(isset($this->flashmessage)){
	  $data['flashmessage'] = $this->flashmessage;
	  }
	  
	  if(isset($this->flashmessage))
	  $content['flashmessage'] = $this->flashmessage;
	  $content['mode']=1;
	  $data['view'] = 'gift_voucher_add_deal_site';
	  $data['content'] = $content;
	  $this->load->view('admin/template',$data); 
	 }
 function delete_deal_sites($id)
 {
   $this->gift_voucher_model->delete_deal_sites($id);
   $this->session->set_flashdata('message', 'Gift Voucher deleted successfully!');
   redirect('admin/gift_voucher/browse_deal_sites', 'refresh');
 
 }


}