<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class discount_codes extends CI_Controller
{
 	function __construct()
 	{
  		parent::__construct();
  		
  		if(!$this->session->userdata('admin_logged_in'))
   		redirect('admin/login', 'refresh');
		
		$this->load->library('form_validation');
		$this->load->model('common_model','',TRUE);
		$this->load->model('discount_code_model','',TRUE);
		$this->load->model('course_model','',TRUE);
		
		if($message = $this->session->flashdata('message')){
          $this->flashmessage =$message;
   		}
  		 		
 	}

 	function index()
 	{
 	
 	   
 	}
	
	function browse_discount_codes()
	{
		$content = array();		
		$content['searchmode'] = true;		
		$data['view'] = 'browse_discount_code';		
        $data['content'] = $content;        
        $this->load->view('admin/template',$data);	
	}
	
	function fetch_discount_codes()
	{
		$page = 1;	// The current page
		$sortname = 'id';	 // Sort column
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
				// Setup paging SQL
		$pageStart = ($page-1)*$rp;      
		$this->db-> from('discount_codes');
		
		
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
				
				
			    $action = '<a href="'.base_url().'admin/discount_codes/discount_codes_edit/'.$row->id.'">Edit</a>&nbsp;&nbsp;<a href="'.base_url().'admin/discount_codes/discount_codes_delete/'.$row->id.'">Delete</a>';
			   
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array( $row->id,$row->discount_code,$row->product,$row->start_date,$row->end_date,$action)
			);
			}
			
		}
      
             
       echo json_encode($data); 
		
		
		
	}
	
	function discount_codes_edit($id)
	{
		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		$this->load->model('course_model');

		$content = array();
		$discount_code_details = $this->discount_code_model->get_discount_code($id);
		
		foreach($discount_code_details as $row)
		{
			$content['id'] = $row->id;
			$content['discount_code'] = $row->discount_code;
			$content['discount_type'] = $row->discount_type;
			$content['discount_value'] = $row->discount_value;
			$content['product'] = $row->product;
			$content['start_date'] = $row->start_date;
			$content['end_date'] = $row->end_date;		
				
		}	
		$content['fee']=$this->course_model->get_currency();
		$content['discount_fee']=$this->discount_code_model->fetch_dicount_code_price($discount_code_details[0]->id);
		
		/*echo "<pre>";
		print_r($content['discount_fee']);
		exit;*/
		
		 if(isset($_POST['save_discount_code']))
		{
		
		    
		
			$discount_code_data  = array();
			
		    $discount_code_data['discount_code']  = ($this->input->post('discount_code')); 
			/*$discount_code_data['discount_type']  = ($this->input->post('discount_type'));*/
			$discount_code_data['discount_value'] = ($this->input->post('discount_value'));    		
			$discount_code_data['start_date'] 	 = ($this->input->post('start_date'));
    		$discount_code_data['end_date'] 	   = ($this->input->post('end_date'));
			$discount_code_data['product'] 	   = ($this->input->post('product'));
			
			$fee['_fee']=$content['_fee']=$this->input->post('discount_fee');

		
		  	foreach($fee['_fee'] as $key=>$val){
		  		if($val!==''){
		  			$content['discount_fee_2'][$key]=$val;
		  			
		  		}
		  	}
		    			 
		    $this->form_validation->set_rules('discount_code', 'Start date', 'trim|required');	
			/*$this->form_validation->set_rules('discount_type', 'Start date', 'trim|required');*/		
			if($content['discount_type']=='percentage')
			{
			$this->form_validation->set_rules('discount_value', 'Start date', 'trim|required');
			}
			$this->form_validation->set_rules('start_date', 'Start date', 'trim|required');
			$this->form_validation->set_rules('end_date', 'Start date', 'trim|required');
			$this->form_validation->set_rules('product', 'Product', 'required');
			if($content['discount_type']=='price')
			{
			$this->form_validation->set_rules('discount_fee[1]', 'Default Currency', 'required');
			}
		
			
			if($this->form_validation->run())
			{	
			/*echo "<pre>";
			print_r($discount_code_data);
			echo "<pre>";
			print_r($content['discount_fee']);
			exit;*/
		         $this->discount_code_model->update_discount_code($discount_code_data,$id);
				 if($content['discount_type']=='price')
			{
				 $this->discount_code_model->delete_discount_code_fee($id);
				  foreach ($content['discount_fee_2'] as $key => $value) {
	
					$discount_fee_2['discount_id']  	 = $id;
					$discount_fee_2['currency_id'] 	 = $key;					
					$discount_fee_2['discount_value']  = $value;
					//discount_code_prices					
					$this->discount_code_model->add_discount_code_price($discount_fee_2);
				}	
			}
			 	 $this->session->set_flashdata('message', 'Discount code updated successfully!');
				 redirect('admin/discount_codes/browse_discount_codes', 'refresh');
			}
		}
			
		if(isset($this->flashmessage)){
		$data['flashmessage'] = $this->flashmessage;
		}
		
		$data['view'] = 'discount_code_edit';		
		$data['content'] = $content;
		$data['mode']=1;
		$this->load->view('admin/template',$data);			
	}
	
	function discount_codes_delete($id)
	{
		$this->discount_code_model->delete_discount_code($id);
		$this->discount_code_model->delete_discount_code_fee($id);
		$this->session->set_flashdata('message', 'Discount code deleted successfully!');
		redirect('admin/discount_codes/browse_discount_codes', 'refresh');
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
	
	

	
	function add_discount_code()
	{
		
		$content = array();
		$content['fee']=$this->course_model->get_currency();
		
		if(isset($_POST['save_discount_code']))
		{
		
			$discount_code_data  = array();
			
		    $discount_code_data['discount_code']  = $content['discount_code']  = ($this->input->post('discount_code')); 
			$discount_code_data['discount_type']  = $content['discount_type']  =($this->input->post('discount_type'));
			if($discount_code_data['discount_type']=='percentage')
			{
			$discount_code_data['discount_value'] = $content['discount_value'] = ($this->input->post('discount_value'));  
			}
			$discount_code_data['start_date'] 	 = $content['start_date'] 	 =($this->input->post('start_date'));
    		$discount_code_data['end_date'] 	   = $content['end_date'] 	   =($this->input->post('end_date'));
			$discount_code_data['product'] 	    = $content['product'] 	    =($this->input->post('product'));
			
			$fee['_fee']=$content['_fee']=$this->input->post('discount_fee');

		
		  	foreach($fee['_fee'] as $key=>$val){
		  		if($val!==''){
		  			$content['discount_fee'][$key]=$val;
		  			
		  		}
		  	}
		
			
			$this->form_validation->set_rules('discount_code', 'Start date', 'trim|required');	
			$this->form_validation->set_rules('discount_type', 'Start date', 'trim|required');
			if($discount_code_data['discount_type']=='percentage')
			{
			$this->form_validation->set_rules('discount_value', 'Start date', 'trim|required');
			}
			$this->form_validation->set_rules('start_date', 'Start date', 'trim|required');
			$this->form_validation->set_rules('end_date', 'Start date', 'trim|required');
			$this->form_validation->set_rules('product', 'Product', 'required');
			if($discount_code_data['discount_type']=='price')
			{
			$this->form_validation->set_rules('discount_fee[1]', 'Default Currency', 'required');
			}
			
			if($this->form_validation->run())
			{	
			/*echo "<pre>";
			print_r($discount_code_data);
			echo "<pre>";
			print_r($content['discount_fee']);
			exit;*/
			
			
			
		         $this->discount_code_model->add_discount_code($discount_code_data);
				 
				 $discount_id =$this->db->insert_id();
				 
				 if($discount_code_data['discount_type']=='price')
				 {
				 
					 foreach ($content['discount_fee'] as $key => $value) {
		
						$discount_fee['discount_id']  	 = $discount_id;
						$discount_fee['currency_id'] 	 = $key;					
						$discount_fee['discount_value']  = $value;
						//discount_code_prices					
						$this->discount_code_model->add_discount_code_price($discount_fee);
					}
				 }
				
			 	 $this->session->set_flashdata('message', 'Discount code added successfully!');
				 redirect('admin/discount_codes/browse_discount_codes', 'refresh');
			}
		}
		
		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		
		
		
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		$data['view'] = 'discount_code_add';
		
		
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
			
			 $content['id'] 			   = $row->idgiftVoucher;
			 $content['voucher_code'] 	   = $row->giftVoucherCode;
			 $content['discountType'] 	   = $row->discountType;
     		 $content['discount_value']    = $row->giftVoucherValue;
			 $content['web_site'] 		   = $row->website;
			 $content['courses_idcourses'] = $row->courses_idcourses;		
			 $content['course_id_arr']   = explode(",",$row->courses_idcourses);			 
			 $content['start_date'] 	   = $row->startdate;
			 $content['end_date'] 		   = $row->enddate;
     		 $content['securitycode_req']  = $row->securitycode_req;
			 $content['country_idcountry'] = $row->country_idcountry;
			 $content['pack_id'] 	 	   = $row->package_id;
			 if($content['course_id_arr'][0]!=0)
			 {
			 	$content['lang_id'] = $this->course_model->get_lang_course($content['course_id_arr'][0]);
			 }
			 
		}
		
		
		
	    if(isset($_POST['save_gift_voucher']))
		{
		
		    
		
			$gift_voucher_data  = array();
			
		    $gift_voucher_data['giftVoucherCode'] 	 = ($this->input->post('gift_code'));
    		$gift_voucher_data['discountType'] 	  	 = ($this->input->post('discountType'));
			$gift_voucher_data['giftVoucherValue']	 = ($this->input->post('discount_value'));
    		$gift_voucher_data['securitycode_req'] 	 = ($this->input->post('securityreq'));			
			$gift_voucher_data['website'] 		 	 = ($this->input->post('website'));
    		$gift_voucher_data['country_idcountry']  = ($this->input->post('country'));
			$gift_voucher_data['startdate'] 		 = ($this->input->post('start_date'));
    		$gift_voucher_data['enddate'] 			 = ($this->input->post('end_date'));
			//$gift_voucher_data['language'] 		 	 = ($this->input->post('language'));
			
			// implode(",",$_POST['courseSelect']);
			if($this->input->post('courses')!=0)
			$gift_voucher_data['courses_idcourses'] = implode(",",$this->input->post('courses'));
			//$gift_voucher_data['courses_idcourses']  =  implode(",",($this->input->post('courseSelect')));
    		$gift_voucher_data['package_id'] 		 = ($this->input->post('packages'));
			$gift_voucher_data['active'] 		 	 = 1;
			
		    			 
			$this->form_validation->set_rules('gift_code', 'Gift voucher code', 'trim|required');
			//$this->form_validation->set_rules('discountType', 'Discount type', 'trim|required');
			//$this->form_validation->set_rules('discount_value', 'Discount Value', 'trim|required');
			$this->form_validation->set_rules('start_date', 'Start date', 'trim|required');
			$this->form_validation->set_rules('end_date', 'Start date', 'trim|required');
			$this->form_validation->set_rules('courses', 'Course', 'required');
		
			
			if($this->form_validation->run())
			{	
			
			
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
	
	
	
	


	
	
	
	
		
		
	
	

	
	 
	
	 
	 
	 
	
	
	
	 
	 
	 


}