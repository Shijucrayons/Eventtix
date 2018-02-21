<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class sales extends CI_Controller {

	function __construct()
 	{
  		parent::__construct();
  		
  		if(!$this->session->userdata('admin_logged_in'))
   		redirect('admin/login', 'refresh');
		$this->load->library('encrypt');
		
		$this->load->library('form_validation');
		$this->load->model('common_model','',TRUE);	
		$this->load->model('sales_model','',TRUE);	
		$this->load->model('offer_model','',TRUE);	
		$this->load->model('user_model','',TRUE);
		$this->load->model('ebook_model','',TRUE);
		
		if($message = $this->session->flashdata('message')){
          $this->flashmessage =$message;
   		}
  		 		
 	}
	
	

	/*function browse_promo_courses()
	{
		
		$content = array();
		
		$content['searchmode'] = true;	
		$data['view'] 		   = 'sales_promo_course_browse';		
        $data['content']       = $content;
        
        $this->load->view('admin/template',$data);	
	}
	*/
	/*function fetch_promo_courses()
	{
		$page = 1;	// The current page
		$sortname = 'idcourses';	 // Sort column
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
		$this->db-> from('courses');
		$this->db-> where('language_id',4);
		$this->db-> where('parent_id ','0');
		$this->db-> where('course_status','1');
		
		$this->db->order_by($sortname,$sortorder);
		$this->db->limit($rp,$pageStart);
        $query = $this->db->get();
        
        
        $data = array();
		$data['page'] = $page;
		$data['total'] = $query -> num_rows();
		$data['rows'] = array();
		     
        
        if($query -> num_rows() >0 )
		{
			$result = $query -> result();
			
			foreach($result as $row){
		
				 
		$action_add_price = '<a href="'.base_url().'admin/sales/course_price_add/'.$row->course_id.'">Add Price</a>';
				 
		$action_view_price = '<a href="'.base_url().'admin/sales/course_price_view/'.$row->course_id.'">View Price</a>';
			   
				 $data['rows'][] = array(
				'id' => $row->course_id,
				'cell' => array($row->course_id,$row->course_name,$action_add_price,$action_view_price)
			);
			}
			
		}
      
             
       echo json_encode($data); exit(); 
		
		
		
	}*/

	
	
	function course_price_add($course_id)
	{
		
		$content = array();
		
		if(isset($_POST['save_price']))
		{
		
			$course_price_data  = array();
			
		    $course_price_data['course_id']  = $course_id;			
			$course_price_data['org_price']	 = ($this->input->post('course_price'));
			$course_price_data['fake_price'] = ($this->input->post('fake_price'));
			$course_price_data['currency_id'] = ($this->input->post('currency'));
		    			 
			$this->form_validation->set_rules('currency', 'Currency', 'trim|required');		
			$this->form_validation->set_rules('course_price', 'Price', 'trim|required');			
			
			if($this->form_validation->run())
			{	
			 	$this->sales_model->add_course_price($course_price_data);
			 	 $this->session->set_flashdata('message', 'Course price added successfully!');
			 	 redirect('admin/sales/browse_promo_courses', 'refresh');
			}
		}
		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		
		if(isset($this->flashmessage)){
		$data['flashmessage'] = $this->flashmessage;
		}
		
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		$data['view'] = 'sales_promo_course_price_add';
		$data['currency'] = $this->common_model->get_currency();
		$data['course_id'] = $course_id;
		$data['course_name'] =  $this->common_model->get_course_name($course_id);
		$data['mode'] = 0;
		$data['content'] = $content;
		$this->load->view('admin/template',$data);			
		
		
		
	
	}
	
	function course_price_view($course_id)
	{
		
		$content = array();
		$data['view'] = 'sales_promo_course_price_view';		
		$data['course_id'] = $course_id;
		$data['mode'] = 0;
		$data['content'] = $content;
		$this->load->view('admin/template',$data);		
	
	}
	
	function fetch_course_price($course_id)
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
		$this->db-> from('promo_courses');
		$this->db-> where('course_id',$course_id);
		
		
		$this->db->order_by($sortname,$sortorder);
		$this->db->limit($rp,$pageStart);
        $query = $this->db->get();
        
        
        $data = array();
		$data['page'] = $page;
		$data['total'] = $query -> num_rows();
		$data['rows'] = array();
		     
        $count = 0;
        if($query -> num_rows() >0 )
		{
			$result = $query -> result();
			
			foreach($result as $row){
						
				 $count= $count+1;
				 $action = '<a href="'.base_url().'admin/sales/course_price_edit/'.$row->id.'">Edit</a>&nbsp;&nbsp;<a href="'.base_url().'admin/sales/course_price_delete/'.$row->id.'/'.$row->course_id.'">Delete</a>';
				 
				 
			   
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($count,$row->org_price,$row->fake_price,$row->currency_id,$action)
			);
			}
			
		}
      
             
       echo json_encode($data); exit(); 
		
		
		
	
	
	
	}
	
	function course_price_edit($price_id)
	{
		
		
		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		$content = array();
		//$id = $_GET['id'];
		$pagedata = $this->sales_model->fetch_course_price($price_id);
	
		
		foreach($pagedata as $row){
			
			 $content['price_id'] 	 = $row->id;
			 $course_id = $content['course_id'] = $row->course_id;			
     		 $content['amount']   	 = $row->org_price;
			 $content['fake_price']  = $row->fake_price;
			 $content['currency_id'] = $row->currency_id;			 
		}
		
		
		
		
		if(isset($_POST['save_price']))
		{
		
			$course_price_data  = array();
			
		    $course_price_data['course_id']  = $course_id;			
			$course_price_data['org_price']	 = ($this->input->post('course_price'));
			$course_price_data['fake_price'] = ($this->input->post('fake_price'));
			$course_price_data['currency_id'] = ($this->input->post('currency'));
		    			 
			$this->form_validation->set_rules('currency', 'Currency', 'trim|required');		
			$this->form_validation->set_rules('course_price', 'Price', 'trim|required');			
			
			if($this->form_validation->run())
			{	
			 	$this->sales_model->course_price_update($price_id,$course_price_data);
			 	 $this->session->set_flashdata('message', 'Course price updated successfully!');
			 	 redirect('admin/sales/browse_promo_courses', 'refresh');
			}
		}
		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		
		if(isset($this->flashmessage)){
		$data['flashmessage'] = $this->flashmessage;
		}
		
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		$data['view'] = 'sales_promo_course_price_add';
		$data['currency'] = $this->common_model->get_currency();
		$data['course_id'] = $course_id;
		$data['course_name'] =  $this->common_model->get_course_name($course_id);
		$data['mode'] = 1;
		$data['content'] = $content;
		$this->load->view('admin/template',$data);	
		
		
	}
	
	function course_price_delete($price_id,$course_id)
	{
		$this->sales_model->delete_course_price($price_id);
		$this->session->set_flashdata('message', 'Course price deleted successfully!');
		redirect('admin/sales/course_price_view/'.$course_id, 'refresh');
		
	
	}
	//
	function browse_eTranscript()
	{
		$content = array();
		$data['view'] = 'sales_eTranscript_browse_fees';		
		$data['mode'] = 0;
		$data['content'] = $content;
		$this->load->view('admin/template',$data);		
	
	}
	
	function fetch_eTranscript_fees()
	{
		
		$page = 1;	// The current page
		$sortname = 'id';	 // Sort column
		$sortorder = 'asc';	 // Sort order
		$qtype = '';	 // Search column
		$query = '';	 // Search string
		$rp=10;
		$pid=37;
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
		$this->db-> from('price_currency');
		$this->db-> where('product_id',$pid);
		
		//$this->db->order_by($sortname,$sortorder);
		//$this->db->limit($rp,$pageStart);
        $query = $this->db->get();
       
        
        $data = array();
		$data['page'] = $page;
		$data['total'] = $query -> num_rows();
		$data['rows'] = array();
		
		$slNo = 0;     
        
        if($query -> num_rows() >0 )
		{
			$result = $query -> result();
			
			foreach($result as $row){
		
				$slNo = $slNo+1; 
		
				 
		 $action = '<a href="'.base_url().'admin/sales/eTranscript_fees_edit/'.$row->id.'">Edit</a>&nbsp;&nbsp;<a href="'.base_url().'admin/sales/eTranscript_fees_delete/'.$row->id.'">Delete</a>';
			   
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($row->id,$row->amount,$row->fake_amount,$row->currency_id,$action)
			);
			}
			
		}
      
             
       echo json_encode($data); exit(); 
		
		
		
	}
	
	/*function eTranscript_price_add()
	{
	
		$content = array();
		
		if(isset($_POST['save_price']))
		{
		
			$eTranscript_price_data  = array();			
		   
			$eTranscript_price_data['org_price']   = ($this->input->post('org_price'));
			$eTranscript_price_data['fake_price']  = ($this->input->post('fake_price'));
			$eTranscript_price_data['currency_id'] = ($this->input->post('currency'));
		    			 
			$this->form_validation->set_rules('currency', 'Currency', 'trim|required');		
			$this->form_validation->set_rules('org_price', 'Price', 'trim|required');			
			
			if($this->form_validation->run())
			{	
			 	$this->sales_model->add_eTranscript_price($eTranscript_price_data);
			 	 $this->session->set_flashdata('message', 'Course price added successfully!');
			 	 redirect('admin/sales/browse_eTranscript', 'refresh');
			}
		}
		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		
		if(isset($this->flashmessage)){
		$data['flashmessage'] = $this->flashmessage;
		}
		
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		$data['view'] = 'sales_eTranscript_fees_add';
		$data['currency'] = $this->common_model->get_currency();		
		$data['mode'] = 0;
		$data['content'] = $content;
		$this->load->view('admin/template',$data);		
	
	}*/
	
/*	function eTranscript_fees_edit($id)
	{
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		$content = array();
		//$id = $_GET['id'];
		$pagedata = $this->sales_model->fetch_eTranscript_price($id);
	
		
		foreach($pagedata as $row){
			
			$content['price_id'] 	 = $row->id;			
     		 $content['amount']   	 = $row->amount;
			 $content['fake_price']  = $row->fake_amount;
			 $content['currency_id'] = $row->currency_id;			 
		}
		
		
		
		$content = array();
		
		if(isset($_POST['save_price']))
		{
		
			$examfee_price_data  = array();			
		   
			$examfee_price_data['amount']   = ($this->input->post('amount'));
			$examfee_price_data['fake_amount']  = ($this->input->post('fake_amount'));
			$examfee_price_data['currency_id'] = ($this->input->post('currency'));
		    			 
			$this->form_validation->set_rules('currency', 'Currency', 'trim|required');		
			$this->form_validation->set_rules('amount', 'Price', 'trim|required');			
			
			if($this->form_validation->run())
			{	
			 	$this->sales_model->add_eTranscript_price($eTranscript_price_data,$id);
			 	 $this->session->set_flashdata('message', 'Exam Fee added successfully!');
			 	 redirect('admin/sales/browse_examfee', 'refresh');
			}
		}
		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		
		if(isset($this->flashmessage)){
		$data['flashmessage'] = $this->flashmessage;
		}
		
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		$data['view'] = 'examfee_browse';
		$data['currency'] = $this->common_model->get_currency();
		$data['price_id'] = $id;
		$data['mode'] = 1;
		$data['content'] = $content;
		$this->load->view('admin/template',$data);	
	
	}*/

	function eTranscript_fees_delete($id)
	{
		$content = array();
		$data['view'] = 'sales_eTranscript_browse_fees';		
		$data['mode'] = 0;
		$data['content'] = $content;
		$this->load->view('admin/template',$data);		
	
	}
	
	function eTranscript_subscriptions()
	{
		$content = array();				
		$data['view'] = 'sales_eTranscript_subscriptions';		
		$data['mode'] = 0;	
		$data['content'] = $content;	
		$this->load->view('admin/template',$data);		
	}
	
	function fetch_eTranscript_subscriptions()
	{
		//$this->load->model('common_model');
	
		$page = 1;	// The current page
		$sortname = 'course_id';	 // Sort column
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
		
		/*$this->db-> select('*');
		$this->db-> from('eTranscript_subcriptions');
		
		
		$this->db->order_by($sortname,$sortorder);
		$this->db->limit($rp,$pageStart);
        $query = $this->db->get();*/
        
        $this->db-> select('*');
        $this->db-> from('eTranscript_subcriptions');		
		//$this->db-> where('parent_id','0');
		//$this->db-> where('course_status','1');
		
		$this->db->order_by($sortname,$sortorder);
		$this->db->limit($rp,$pageStart);
        $query = $this->db->get();
		
        $data = array();
		$data['page'] = $page;
		$data['total'] = $query -> num_rows();
		$data['rows'] = array();
		
		$slNo = 0;     
        
          if($query -> num_rows() >0)
		 {
			$result = $query -> result();			
			foreach($result as $row){	
			
			//$subscribedStudents = $this->common_model->count_eTranscriptSubscription($row->course_id);
			   
				 $data['rows'][] = array(
				'id' => $row->course_id,
				'cell' => array($row->course_id,$row->course_name,$row->language_id)
			);
			}
			
		}
      
             
       echo json_encode($data); exit(); 
	   
	}
	
	function browse_ebook_packages()
	{
		$content = array();
		$data['view'] = 'sales_ebook_pack_browse';		
		$data['mode'] = 0;
		$data['content'] = $content;
		$this->load->view('admin/template',$data);		
	
		
	}
	
	function fetch_ebook_packages()
	{
		$page = 1;	// The current page
		$sortname = 'id';	 // Sort column
		$sortorder = 'asc';	 // Sort order
		$qtype = '';	 // Search column
		$query = '';	 // Search string
		$rp=10;
		$ebooks='ebooks';
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
		$this->db-> from('ebook_package_types');
		
		
		$this->db->order_by($sortname,$sortorder);
		$this->db->limit($rp,$pageStart);
        $query = $this->db->get();
        
        
        $data = array();
		$data['page']  = $page;
		$data['total'] = $query -> num_rows();
		$data['rows']  = array();
		
	
        if($query -> num_rows() >0 )
		{
			$result = $query -> result();
			
			foreach($result as $row){	
			
			
		     	$this->db-> select('id');
		        $this->db-> from('products');
				$this->db-> where('units',$row->id);
				$this->db-> where('type',$ebooks);
				 $query1 = $this->db->get();
				 if($query1-> num_rows() >0 )
		{
			$result1 = $query1 -> result();
			
			foreach($result1 as $row1){	
				 for($i=1;$i<4;$i++)
        {
                        $this->db-> select('*');
                        $this->db-> from('price_currency');
                        $this->db-> where('product_id',$row1->id);
         $this->db-> where('currency_id',$i);
         $query_price = $this->db->get();
                    if($query_price-> num_rows()==1 )
                              {
                             $result_price = $query_price -> result();
                             foreach($result_price as $row_price){
            if($i==1)
                                          $EUR=$row_price->amount;
                              elseif($i==2)
            $GBP=$row_price->amount;
            else
            $USD=$row_price->amount;
           }
         }
         elseif($query_price-> num_rows()>1 )
         {
            if($i==1)
                                          $EUR="more price added";
                              elseif($i==2)
            $GBP="more price added";
            else
            $USD="more price added";
         }
         else
         {
            if($i==1)
                                          $EUR="--";
                              elseif($i==2)
            $GBP="--";
            else
            $USD="--";
         }
        }
		
				$action_add_price = '<a href="'.base_url().'admin/sales/product_price_add/'.$row1->id.'">Add Price</a>';
				 
				$action_view_price = '<a href="'.base_url().'admin/sales/view_product_price/'.$row1->id.'">View Price</a>';
		 		
				//$action = '<a href="'.base_url().'admin/sales/product_price_edit/'.$row1->id.'">Edit</a>';
			   
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($row->id,$row->package_type,$EUR,$GBP,$USD,$action_add_price,$action_view_price)
				);
			}
			
		}
			}
		
		}
             
       echo json_encode($data); exit(); 
		
		
		
	}
	
	function ebook_pack_edit($pack_id)
	{
		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		$content = array();
		//$id = $_GET['id'];
		$pagedata = $this->sales_model->fetch_ebook_pack($pack_id);
	
		
		foreach($pagedata as $row){
			
			 $packages_id = $content['id'] 	 	  = $row->id;
			 $content['package_type'] = $row->type;	
     		 $content['title_eng']    = $row->title_eng;
			 $content['title_spa']    = $row->title_spa;
			 $content['desc_eng']	  = $row->desc_eng;	
			 $content['desc_spa'] 	  = $row->desc_spa;		
			 $content['status']  	  = $row->status;		
			 	 
		}
		
		
		if(isset($_POST['save_price']))
		{
		
			$ebook_pack_data  = array();
			
		    $ebook_pack_data['type']= $pack_id;			
			$ebook_pack_data['title_eng']	 = ($this->input->post('title_eng'));
			$ebook_pack_data['title_spa']   = ($this->input->post('title_spa'));
			$ebook_pack_data['desc_eng']    = ($this->input->post('desc_eng'));
			$ebook_pack_data['desc_spa']    = ($this->input->post('desc_spa'));
		    			 
			$this->form_validation->set_rules('title_eng', 'English Title', 'trim|required');		
			$this->form_validation->set_rules('title_spa', 'Spanish Title', 'trim|required');			
			$this->form_validation->set_rules('desc_eng', 'English Description', 'trim|required');		
			$this->form_validation->set_rules('desc_spa', 'Spanish Description', 'trim|required');			
			
			if($this->form_validation->run())
			{	
			 	$this->sales_model->ebook_pack_update($packages_id,$ebook_pack_data);
			 	 $this->session->set_flashdata('message', 'Pack price updated successfully!');
			 	 redirect('admin/sales/browse_ebook_packages/', 'refresh');
			}
		}
		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		
		if(isset($this->flashmessage)){
		$data['flashmessage'] = $this->flashmessage;
		}
		
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		$data['view'] = 'sales_ebook_pack_add';
		$data['currency'] = $this->common_model->get_currency();
		$data['packages_id'] = $packages_id;
		$data['pack_name'] =  $this->sales_model->get_pack_name($pack_id);
		$data['mode'] = 1;
		$data['content'] = $content;
		$this->load->view('admin/template',$data);	
		
		
		
		
	}
	
	function ebook_pack_price_add($pack_id)
	{
		
		$content = array();
		
		if(isset($_POST['save_price']))
		{
		
			$ebook_price_data  = array();
			
		    $ebook_price_data['package_type']= $pack_id;			
			$ebook_price_data['org_price']	 = ($this->input->post('pack_price'));
			$ebook_price_data['fake_price']  = ($this->input->post('fake_price'));
			$ebook_price_data['currency_id'] = ($this->input->post('currency'));
		    			 
			$this->form_validation->set_rules('currency', 'Currency', 'trim|required');		
			$this->form_validation->set_rules('pack_price', 'Price', 'trim|required');			
			
			if($this->form_validation->run())
			{	
			 	$this->sales_model->add_ebook_pack_price($ebook_price_data);
			 	 $this->session->set_flashdata('message', 'Pack price added successfully!');
			 	 redirect('admin/sales/ebook_pack_price_view/'.$pack_id, 'refresh');
			}
		}
		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		
		if(isset($this->flashmessage)){
		$data['flashmessage'] = $this->flashmessage;
		}
		
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		$data['view'] = 'sales_ebook_pack_price_add';
		$data['currency'] = $this->common_model->get_currency();
		$data['pack_id'] = $pack_id;
		$data['pack_name'] =  $this->sales_model->get_pack_name($pack_id);
		$data['mode'] = 0;
		$data['content'] = $content;
		$this->load->view('admin/template',$data);	
		
		
		
		
	}
	
	/*function ebook_pack_price_view($pack_id)
	{		
		$content = array();
		$data['view'] = 'sales_ebook_pack_price_view';		
		$data['pack_id'] = $pack_id;
		$data['mode'] = 0;
		$data['content'] = $content;
		$this->load->view('admin/template',$data);		
	}*/
	
	function fetch_ebook_pack_price($pack_id)
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
		$this->db-> from('ebook_package_price');
		$this->db-> where('package_type',$pack_id);
		
		
		$this->db->order_by($sortname,$sortorder);
		$this->db->limit($rp,$pageStart);
        $query = $this->db->get();
        
        
        $data = array();
		$data['page'] = $page;
		$data['total'] = $query -> num_rows();
		$data['rows'] = array();
		     
        $count = 0;
        if($query -> num_rows() >0 )
		{
			$result = $query -> result();
			
			foreach($result as $row){
						
				 $count= $count+1;
				 $action = '<a href="'.base_url().'admin/sales/ebook_pack_price_edit/'.$row->id.'">Edit</a>&nbsp;&nbsp;<a href="'.base_url().'admin/sales/ebook_pack_price_delete/'.$row->id.'/'.$row->package_type.'">Delete</a>';
				 
				 
			   
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($count,$row->org_price,$row->fake_price,$row->currency_id,$action)
			);
			}
			
		}
      
             
       echo json_encode($data); exit(); 
		
		
	}
	
	function ebook_pack_price_edit($price_id)
	{
		
		
		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		$content = array();
		//$id = $_GET['id'];
		$pagedata = $this->sales_model->fetch_ebook_price($price_id);
	
		
		foreach($pagedata as $row){
			
			 $content['price_id'] 	 = $row->id;
			 $pack_id = $content['package_type'] = $row->package_type;	
     		 $content['amount']   	 = $row->org_price;
			 $content['fake_price']  = $row->fake_price;
			 $content['currency_id'] = $row->currency_id;			 
		}
		
		
		if(isset($_POST['save_price']))
		{
		
			$ebook_price_data  = array();
			
		    $ebook_price_data['package_type']= $pack_id;			
			$ebook_price_data['org_price']	 = ($this->input->post('pack_price'));
			$ebook_price_data['fake_price']  = ($this->input->post('fake_price'));
			$ebook_price_data['currency_id'] = ($this->input->post('currency'));
		    			 
			$this->form_validation->set_rules('currency', 'Currency', 'trim|required');		
			$this->form_validation->set_rules('pack_price', 'Price', 'trim|required');			
			
			if($this->form_validation->run())
			{	
			 	$this->sales_model->ebook_pack_price_update($price_id,$ebook_price_data);
			 	 $this->session->set_flashdata('message', 'Pack price updated successfully!');
			 	 redirect('admin/sales/ebook_pack_price_view/'.$pack_id, 'refresh');
			}
		}
		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		
		if(isset($this->flashmessage)){
		$data['flashmessage'] = $this->flashmessage;
		}
		
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		$data['view'] = 'sales_ebook_pack_price_add';
		$data['currency'] = $this->common_model->get_currency();
		$data['pack_id'] = $pack_id;
		$data['pack_name'] =  $this->sales_model->get_pack_name($pack_id);
		$data['mode'] = 0;
		$data['content'] = $content;
		$this->load->view('admin/template',$data);	
		
		
		
	}
	
	function browse_ebook_subscriptions()
	{
		$content = array();
		$data['view'] = 'sales_ebook_subscriptions';		
		$data['content'] = $content;
		$this->load->view('admin/template',$data);	
	}
	
	function fetch_ebook_subscriptions()
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
		$this->db-> from('ebooksubscription');		
		
		
		$this->db->order_by($sortname,$sortorder);
		//$this->db->limit($rp,$pageStart);
        $query = $this->db->get();
        
        
        $data = array();
		$data['page'] = $page;
		$data['total'] = $query -> num_rows();
		$total_array = $query->result();
		$result = array_slice($total_array,$pageStart,$rp);
		$data['rows'] = array();
		     
        $count = 0;
        if($query -> num_rows() >0 )
		{
			//$result = $query -> result();
			
			foreach($result as $row){
						
				 $count= $count+1;
				 
				 
				 
			   
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($count,$row->name,$row->email,$row->type,$row->date_purchased)
			);
			}
			
		}
      
             
       echo json_encode($data); exit(); 
	}
	
	
	function browse_email_campaign()
	{
		$content = array();
		$data['view'] = 'sales_browseEmailCampaings';		
		$data['content'] = $content;
		$this->load->view('admin/template',$data);	
	}
	
	function fetch_EmailCampaings()
	{
		
		$page = 1;	// The current page
		$sortname = 'campaign_id';	 // Sort column
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
		
		$this->db-> select('campaign_id,campaign_name,campaign_code,status');
		$this->db-> from('email_campaigns');		
		
		
		$this->db->order_by($sortname,$sortorder);
		$this->db->limit($rp,$pageStart);
        $query = $this->db->get();
        
        
        $data = array();
		$data['page'] = $page;
		$data['total'] = $query -> num_rows();
		$data['rows'] = array();
		     
        $count = 0;
        if($query -> num_rows() >0 )
		{
			$result = $query -> result();
	//echo "<pre>";print_r($result);exit;
			foreach($result as $row){
				$cmpaignId = $row->campaign_id;
				$campaignLink = "http://staging.trendimi.com/campaign_login.php?campId=".$cmpaignId."&mail=*|EMAIL|*";
						
				
				$subscribedStudents=$this->sales_model->campaign_purchases_count($cmpaignId); 
				if($subscribedStudents>0)
				 $listSubscription='<a href="browseCampaignPurchases.php?campId="'.$cmpaignId.'">'.$subscribedStudents.'</a>';
				 else
				 $listSubscription=$subscribedStudents;
				 
				 
				 $viewOffers = "<a href='".base_url()."admin/sales/viewCampOffers/".$cmpaignId."'>View Offers</a>";
                 $viewDetails = "<a href='".base_url()."admin/sales/viewCampTemplate/".$cmpaignId."'>View template</a>"; 
				 $details = "<a href='".base_url()."admin/sales/viewCampDetails/".$cmpaignId."'>Details</a>";
				 // echo $cmpaignId;exit;
				 
			   	 $data['rows'][] = array(
				'id' => $row->campaign_id,
				'cell' => array($cmpaignId,$row->campaign_name,$campaignLink,$listSubscription,$row->status,$viewOffers,$viewDetails,$details)
			);
			}
			
		}
      
             
       echo json_encode($data); exit(); 
	}
//-....................................................	
	public function viewCampOffers($campId)
	{
		$content = array();
		$data['view'] = 'sales_viewCampOffers';
		$content['campId']		=$campId;
		$data['content'] = $content;
		$this->load->view('admin/template',$data);
	}
	public function fetch_CampOffers($campId)
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
        $this->db-> from('campaign_offers');
		$this->db-> where('campaign_id',$campId);
		
        $query = $this -> db -> get();
		  
	 	
        
        
        $data = array();
		$data['page'] = $page;
		$data['total'] = $query -> num_rows();
		$data['rows'] = array();
		//$data['campaignName'] = 
		     
        $count = 0;
        if($query -> num_rows() >0 )
		{
			$result = $query -> result();
	//echo "<pre>";print_r($result);exit;
			foreach($result as $row){
			
			     $addPrice = "<a href='".base_url()."admin/sales/addCampOffersPrice/".$row->id."'>Add Price</a>";
				 $viewPrice = "<a href='".base_url()."admin/sales/viewCampOffersPrice/".$row->id."'>View Price</a>";
				  $action = '<a href="'.base_url().'admin/sales/campOfferEdit/'.$row->id.'">Edit</a>&nbsp;&nbsp;<a href="'.base_url().'admin/sales/campOfferDelete/'.$row->id.'">Delete</a>';
				 
				 
			   	 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($row->id,$row->offer_name,$row->type,$addPrice,$viewPrice,$action)
			);
			};
		}
		 echo json_encode($data); exit(); 
	}
	
	public function viewCampDetails($campId)
	{
	}
	public function fetchCampDetails($campId)
	{
	}
	
	public function viewCampTemplate($campId)
	{
		$content = array();
		$data['view'] = 'sales_viewCampTemplate';
		$content['campId']		=$campId;
		$template = $this->sales_model->getCampTemplate($campId);
		$content['campName']=$template['campName'];
		$content['campCode']=$template['campCode'];
		$content['campId']=$template['campId'];
		$content['campTemplate']=$template['campTemplate'];
		
		$data['content'] = $content;
		//echo "<pre>";		print_r($content);exit;
		$this->load->view('admin/template',$data);
	}
	public function fetchCampTemplate($campId)
	{
		
	}
	
	public function hardcopy_application()
	{
		$content = array();
		$data['view'] = 'sales_hardcopy_applications';		
		$data['mode'] = 0;
		$data['content'] = $content;
		$this->load->view('admin/template',$data);		
		
	}
	function fetch_hardcopy_application()
	{
		
		$page = 1;	// The current page
		$sortname = 'id';	 // Sort column
		$sortorder = 'asc';	 // Sort order
		$qtype = '';	 // Search column
		$query = '';	 // Search string
		$rp=10;
		$hardcopy='hardcopy';
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
		
		
		$pageStart = ($page-1)*$rp;
		
		
		$this->db-> select('*');
		$this->db-> from('user_subscriptions');		
		$this->db-> where('type',$hardcopy);
		
		$this->db->order_by($sortname,$sortorder);
		$this->db->limit($rp,$pageStart);
        $query = $this->db->get();
		//$arr = $query -> result();
				 
        $data = array();
		$data['page'] = $page;
		$data['total'] = $query -> num_rows();
		$data['rows'] = array();
		     
        $count = 0;
        if($query -> num_rows() >0 )
		{
			$result = $query -> result();
			
			
			
			foreach($result as $row){
						
				 $count= $count+1;				  			
			 
				 $this->db-> select('first_name , last_name');
				 $this->db-> from('users');		
				 $this->db-> where('user_id',$row->user_id);
			     $username = $this->db->get();
		         $name =$username -> result();
			     foreach($name as $row1){
					  
				   $first_name= $row1->first_name." ".$row1->last_name;
				  
				 }
			     $this->db-> select('course_name');
				 $this->db-> from('courses');		
				 $this->db-> where('course_id',$row->course_id);
			     $coursename = $this->db->get();
		         $course =$coursename -> result();
								 
			     foreach($course as $row2){
					  
				   $course_name= $row2->course_name;
				 }
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($count,$first_name,$course_name,$row->date_applied)
			);
				 }
			
		}
      
             
       echo json_encode($data); exit(); 
	}
	
	public function eTranscript_sales_report()
	{
		$content = array();
		$data['view'] = 'sales_eTranscript_report';		
		$data['mode'] = 0;
		$data['content'] = $content;
		$this->load->view('admin/template',$data);		
		
	}
	function fetch_eTranscript_sales_report()
	{
		
		$page = 1;	// The current page
		$sortname = 'id';	 // Sort column
		$sortorder = 'asc';	 // Sort order
		$qtype = '';	 // Search column
		$query = '';	 // Search string
		$rp=10;
		$etranscript='transcript';
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
		$this->db-> from('user_subscriptions');		
		$this->db-> where('type',$etranscript); 
		
		$this->db->order_by($sortname,$sortorder);
		$this->db->limit($rp,$pageStart);
        $query = $this->db->get();
		//$arr = $query -> result();
				 
        $data = array();
		$data['page'] = $page;
		$data['total'] = $query -> num_rows();
		$data['rows'] = array();
		     
        $count = 0;
        if($query -> num_rows() >0 )
		{
			$result = $query -> result();
			
			foreach($result as $row){
						
				 $count= $count+1;				  			
			 
				 $this->db-> select('first_name , last_name');
				 $this->db-> from('users');		
				 $this->db-> where('user_id',$row->user_id);
			     $username = $this->db->get();
		         $name =$username -> result();
			     foreach($name as $row1){
					  
				   $first_name= $row1->first_name." ".$row1->last_name;
				  
				 }
			     $this->db-> select('course_name');
				 $this->db-> from('courses');		
				 $this->db-> where('course_id',$row->course_id);
			     $coursename = $this->db->get();
		         $course =$coursename -> result();
			     foreach($course as $row2){
					  
				   $course_name= $row2->course_name;
				 }
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($count,$first_name,$course_name,$row->date_applied)
			);
				 }
			
		}
      
             
       echo json_encode($data); exit(); 
	}
	
	
	/* ------------ product price mangenmnt----------------*/
	
	
	function product_price_add($id)
	{

		$ref = $this->input->server('HTTP_REFERER', TRUE);
		$data['reffer'] = $ref;
		$content = array();
		$this->db-> select('*');
		$this->db-> from('products');
		$this->db-> where('id',$id);
		$query = $this->db->get();
		$result = $query -> result();
			
			foreach($result as $row){
				$content['type'] =$row->type;
				
			}
		
		
		
		if(isset($_POST['save_price']))
		{
		
			$price_data  = array();
			
		    $price_data['product_id'] 	= $id;
			$price_data['currency_id']	    = ($this->input->post('currency'));
			$price_data['fake_amount']	= ($this->input->post('fake_price'));
			$price_data['amount']	= ($this->input->post('price'));
		    	$data['reffer'] = ($this->input->post('reffer'));		 
			$this->form_validation->set_rules('currency', 'Currency', 'trim|required');
			$this->form_validation->set_rules('price', 'Price', 'trim|required');			
			
			if($this->form_validation->run())
			{	
			 	$this->sales_model->add_price($price_data);
			 	 $this->session->set_flashdata('message', 'Price added successfully!');
			 	 redirect($data['reffer'], 'location');
			}
		}
		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		
		if(isset($this->flashmessage)){
		$data['flashmessage'] = $this->flashmessage;
		}
		
		if(isset($this->flashmessage))
		
		$content['flashmessage'] = $this->flashmessage;
		
		$data['view'] = 'product_price_add';
		$data['currency'] = $this->common_model->get_currency();
		$data['id'] = $id;
		$data['mode'] = 0;
		$data['content'] = $content;
		$this->load->view('admin/template',$data);			
		
		
		
	
	}
	
	function view_product_price($id)
	{		
		$content = array();
		$data['view'] = 'product_price_view';
		$this->db-> select('*');
		$this->db-> from('products');
		$this->db-> where('id',$id);
		$query = $this->db->get();
		$result = $query -> result();
			
			foreach($result as $row){
				$content['type'] =$row->type;
				
			}
			if($id== 46)
			$content['type']= "6 months access";
			if($id== 47)
			$content['type']= "12 months access";
			if($id== 51)
			$content['type']= "individual course";
			if($id== 52)
			$content['type']= "pack of two course";
			if($id== 53)
			$content['type']= "pack of three course";
			if($id== 54)
			$content['type']= "pack of four course";
			if($id== 55)
			$content['type']= "pack of five course";
			if($id== 56)
			$content['type']= "pack of six course";
		$data['id'] = $id;
		$data['mode'] = 0;
		$data['content'] = $content;
		$this->load->view('admin/template',$data);					
		
	}
	
	function fetch_product_price($id)
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
		$this->db-> from('price_currency');
		$this->db-> where('product_id',$id);
		
        $query = $this->db->get();
        
        
        $data = array();
		$data['page'] = $page;
		$data['total'] = $query -> num_rows();
		$data['rows'] = array();
		     
        
        if($query -> num_rows() >0 )
		{
			$result = $query -> result();
			$sl = 1;
			foreach($result as $row){
				
				$currency_code = $this->common_model->get_currency_code_from_id($row->currency_id);
								
				 
				 $action = '<a href="'.base_url().'admin/sales/product_price_edit/'.$row->id.'/'.$id.'">Edit</a>&nbsp;&nbsp;<a href="'.base_url().'admin/sales/product_price_delete/'.$row->id.'/'.$id.'">Delete</a>';
				 
				 
			   
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($sl,$row->amount,$row->fake_amount,$currency_code,$action)
			);
			$sl++;
			}
			
		
		}
      
             
       echo json_encode($data); exit(); 
		
		
		
	
	}
	function product_price_edit($price_id,$id)
	{
		
		$ref = $this->input->server('HTTP_REFERER', TRUE);
		$data['reffer'] = $ref;
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		$content = array();
		$this->db-> select('*');
		$this->db-> from('products');
		$this->db-> where('id',$id);
		$query = $this->db->get();
		$result = $query -> result();
			
			foreach($result as $row){
				$content['type'] =$row->type;
				
			}
		//$id = $_GET['id'];
		$pagedata = $this->sales_model->fetch_product_price($price_id);
	
		
		foreach($pagedata as $row){
			
			 $content['price_id'] 		 = $row->id;
			 $id = $content['product_id'] 	 = $row->product_id;
     		 $content['amount']   	 = $row->amount;
			 $content['fake_amount']  = $row->fake_amount;
			 $content['currency_id'] = $row->currency_id;			 
		}
		
		
		
		
		if(isset($_POST['save_price']))
		{
		    $data['reffer'] =$this->input->post('currency');
			$price_data  = array();
			
		    $price_data['product_id'] 	= $id;
			$price_data['amount']	    = ($this->input->post('price'));
			$price_data['fake_amount']	= ($this->input->post('fake_price'));
			$price_data['currency_id']	= ($this->input->post('currency'));
		    			 
			$this->form_validation->set_rules('currency', 'Currency', 'trim|required');
			$this->form_validation->set_rules('price', 'Price', 'trim|required');			
			
			if($this->form_validation->run())
			{	
			 	$this->sales_model->update_product_price($price_data,$price_id);
			 	 $this->session->set_flashdata('message', 'Price updated successfully!');
			 	 redirect('admin/sales/view_product_price/'.$id, 'refresh');
			}
		}
		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		
		if(isset($this->flashmessage)){
		$data['flashmessage'] = $this->flashmessage;
		}
		
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		$data['view'] = 'product_price_add';
		$data['currency'] = $this->common_model->get_currency();
		$data['id'] = $id;
		$data['mode'] = 1;
		$data['content'] = $content;
		$this->load->view('admin/template',$data);			
		
		
	}
	
	
	function product_price_delete($price_id,$id)
	{
		$this->sales_model->delete_product_price($price_id);
		$this->session->set_flashdata('message', 'Price deleted successfully!');
		redirect('admin/sales/view_product_price/'.$id, 'refresh');
		
	}
	
	
	/* ------------ end product price mangenmnt----------------*/
	
	
	
	function extension_subscription_browse()
	{
		$content = array();
		$data['view'] = 'extension_subscription';		
		$data['content'] = $content;
		$this->load->view('admin/template',$data);	
	}
	
	function fetch_extension_subscription()
	{
		$input='';
		$page = 1;	// The current page
		$sortname = 'id';	 // Sort column
		$sortorder = 'asc';	 // Sort order
		$qtype = '';	 // Search column
		$query = '';	 // Search string
		$rp=10;
		$extension="extension";
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
		$this->db-> from('user_subscriptions');
		$this->db-> where('type',$extension);
        $query = $this->db->get();
		
		$data['page'] = $page;
		$data['total'] = $query -> num_rows();
		$total_array = $query->result();
			$result = array_slice($total_array,$pageStart,$rp);
		$data['rows'] = array();
		     
       
        if($query -> num_rows() >0 )
		{
			
			//$result = $query -> result();
		foreach($result as $row)
		{
	    	$this->db-> select('first_name');
		    $this->db-> from('users');
			$this->db-> where('user_id',$row->user_id);
			$query1 = $this->db->get();
			
			if($query1 -> num_rows() >0 )
		{
			$result1 = $query1 -> result();
			foreach($result1 as $row1)
			{
			$this->db-> select('course_name');
		    $this->db-> from('courses');
			$this->db-> where('course_id',$row->course_id);
			$query2 = $this->db->get();
			if($query2 -> num_rows() >0 )
		{
			$result2 = $query2 -> result();
				foreach($result2 as $row2)
			{
				
			
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($row->id,$row1->first_name,$row2->course_name,$row->date_applied)
			);
		}
		}
		}
		}
		}
		}
		
          
       echo json_encode($data); exit(); 
	}
	
	
	
	function browse_offer()
	{
		$content = array();
		
		$content['searchmode'] = true;		
		$data['view'] = 'offer_browse';
		
        $data['content'] = $content;
        
        $this->load->view('admin/template',$data);	
	}
	
	
	
	function fetch_offer()
	{
		
		$page = 1;	// The current page
		$sortname = 'offer_id';	 // Sort column
		$sortorder = 'asc';	 // Sort order
		$qtype = '';	 // Search column
		$query = '';	 // Search string
		$rp=10;
		$offers='offers';
		
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
		$this->db-> from('offer');
		
        $query = $this->db->get();
        
        
        $data = array();
		$data['page'] = $page;
		$data['total'] = $query -> num_rows();
		$data['rows'] = array();
		     
        
        if($query -> num_rows() >0 )
		{
			$result = $query -> result();
			
			foreach($result as $row){
				$data['lang']=$row->langid;
				if($data['lang']==4)
				{
					$language="English";
				}
				elseif($data['lang']==3)
				{
					$language="Spanish";
				}
				elseif($data['lang']==1)
				{
					$language="Chineese";
				}
				else
				{
					$language="Italian";
				}
				$this->db-> select('id');
		        $this->db-> from('products');
				$this->db-> where('item_id',$row->offer_id);
				$this->db-> where('type',$offers);
				 $query1 = $this->db->get();
				 if($query1-> num_rows() >0 )
		{
			$result1 = $query1 -> result();
			
			foreach($result1 as $row1){
				
              $price = '<a target="_blank" href="'.base_url().'admin/sales/product_price_add/'.$row1->id.'">Add</a>&nbsp;|&nbsp;<a href="'.base_url().'admin/sales/view_product_price/'.$row1->id.'">View</a>';
			    
				$action = '<a href="'.base_url().'admin/sales/offer_edit/'.$row->offer_id.'">Edit</a>';
				$subscription = '<a href="'.base_url().'admin/sales/offer_subscriptions_browse/'.$row->product_id.'">subscription</a>';
				 $data['rows'][] = array(
				'id' => $row->offer_id,
				'cell' => array($row->offer_id,$row->offer_title,$language,$row->offer_description,$row->offer_date,$price,$subscription,$action)
			);
			}
			
		
		}
			}
		}
      
             
       echo json_encode($data); exit(); 
		
		
		
	
	}
	
	function offer_add()
	{
	
		$content = array();
		if(isset($_POST['save_offer']))
		{
			$offer_data  = array();	
			$offer_data_product=array();
			$update_product_id=array();		
		    $offer_data['offer_title']   = $content['offer_title'] = $this->input->post('offer_title');
			$offer_data_product['type']   = $content['type'] = 'offers';
			$offer_data_product['units']   = $content['units'] = '1';
		    $offer_data['langid'] 	   = $content['language'] = $this->input->post('language');
			$offer_title=$this->input->post('offer_title');
			
			if($this->input->post('language')!=4)
			{
				
				$this->db-> select('*');
	        	$this->db-> from('offer');
				$this->db-> where('offer_title',$offer_title);
				$this->db-> where('langid','4');
		        $query = $this->db->get();
				 if($query -> num_rows() >0 )
		    {
			$result = $query -> result();
			
			foreach($result as $row){
				$offer_data['parrent_offer_id'] 	   = $content['parrent_id'] = $row->offer_id;
			}
			}
				
			}
		    $offer_data['offer_description']    = $content['offer_description'] = $this->input->post('offer_description');
			$offer_data['offer_date'] = $content['offer_date'] = $this->input->post('offer_date');
			
				
		    $this->form_validation->set_rules('offer_title', 'Offer title', 'trim|required');
			$this->form_validation->set_rules('language', 'Lanaguage', 'required');			
			$this->form_validation->set_rules('offer_description', 'Description', 'required');
			$this->form_validation->set_rules('offer_date', 'Date', 'required');			

			if($this->form_validation->run())
			{
				
			 	$offer_id=$this->offer_model->add_offer($offer_data);
				$offer_data_product['item_id']   = $content['item_id'] = $offer_id;
				$id=$this->offer_model->add_offer_products($offer_data_product);	
				$update_product_id['product_id']   = $content['product_id'] = $id;
			    $this->offer_model->update_product_id($update_product_id,$offer_id);	
			 	redirect('admin/sales/browse_offer', 'refresh');
			}
			
		}	
		$data['view'] = 'offer_add';
		$content['language']=$this->common_model->get_languages();	
		$data['mode'] = 0;	
		$data['content'] = $content;
		$this->load->view('admin/template',$data);
	
	}
	function offer_edit($offer_id)
	{
		
		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		$content = array();
		//$id = $_GET['id'];
		$pagedata = $this->offer_model->fetch_offer_edit($offer_id);
	
		
		foreach($pagedata as $row){
			
			 $content['offer_id'] 		 = $row->offer_id;
			 $content['offer_title'] 	 = $row->offer_title;
     		 $content['langid']   	 = $row->langid;
			 $content['offer_description']  = $row->offer_description;
			 $content['offer_date'] = $row->offer_date;			 
		}
		if(isset($_POST['save_offer']))
		{
		
			$offer_data  = array();
			
		    $offer_data['offer_title']   = $content['offer_title'] = $this->input->post('offer_title');
		    $offer_data['langid'] 	   = $content['language'] = $this->input->post('language');
			if($this->input->post('language')!=4)
			{
				$offer_title=$this->input->post('offer_title');
				$this->db-> select('*');
	        	$this->db-> from('offer');
				$this->db-> where('offer_title',$offer_title);
		        $query = $this->db->get();
				 if($query -> num_rows() >0 )
		    {
			$result = $query -> result();
			
			foreach($result as $row){
				$offer_data['parrent_offer_id'] 	   = $content['parrent_id'] = $row->offer_id;
			}
			}
				
			}
		    $offer_data['offer_description']    = $content['offer_description'] = $this->input->post('offer_description');
			$offer_data['offer_date'] = $content['offer_date'] = $this->input->post('offer_date');
		    $this->form_validation->set_rules('offer_title', 'Offer title', 'trim|required');
			$this->form_validation->set_rules('language', 'Lanaguage', 'required');			
			$this->form_validation->set_rules('offer_description', 'Description', 'required');
			$this->form_validation->set_rules('offer_date', 'Date', 'required');			

			if($this->form_validation->run())
			{
				
			 	$this->offer_model->update_offer($offer_data,$offer_id);			 
			 	redirect('admin/sales/browse_offer', 'refresh');
			}
		}
		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		
		if(isset($this->flashmessage)){
		$data['flashmessage'] = $this->flashmessage;
		}
		
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		$data['view'] = 'offer_add';
		
		$content['language']=$this->common_model->get_languages();		
		$data['mode'] = 1;
		$data['content'] = $content;
		$this->load->view('admin/template',$data);			
		
		
	}
	//**************************************Offer subscription*************************************************
	function offer_subscriptions_browse($product_id)
	{
		$content = array();
		$data['view'] = 'offer_subscriptions';	
		$data['product_id'] = $product_id;
		$data['content'] = $content;
		$this->load->view('admin/template',$data);	
	}
	
	function fetch_offer_subscriptions($product_id)
	{
		$input='';
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
		
		$pageStart = ($page-1)*$rp;
		$this->db->select('offer.*,payments.*');
        $this->db->from('offer', 'payments');
		$this->db->join('payments', 'offer.product_id = payments.product_id');
		$this->db->where('payments.product_id',$product_id);
		
        $query = $this->db->get();
		
		$data['page'] = $page;
		$data['total'] = $query -> num_rows();
		$data['rows'] = array();
		
        if($query -> num_rows() >0 )
		{
			$result = $query -> result();
		foreach($result as $row)
		{
			
			$this->db-> select('username');
		    $this->db-> from('users');
			$this->db-> where('user_id',$row->user_id);
			$query1 = $this->db->get();
			if($query1 -> num_rows() >0 )
		{
			$result1 = $query1 -> result();
	
			foreach($result1 as $row1)
			{
	    	    
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($row->id,$row1->username,$row->offer_title,$row->payment_date)
			);
		}
		
		}
		}
		}
      
             
       echo json_encode($data); exit(); 
	}
	
	
	
	
	
	
	
	
	
	
	
	public function proof_of_enrollment()
	{
		$content = array();
		$data['view'] = 'proof_of_enrollment';		
		$data['mode'] = 0;
		$data['content'] = $content;
		$this->load->view('admin/template',$data);		
		
	}
	
	function fetch_proof_of_enrollment()
	{
		
		$page = 1;	// The current page
		$sortname = 'id';	 // Sort column
		$sortorder = 'asc';	 // Sort order
		$qtype = '';	 // Search column
		$query = '';	 // Search string
		$rp=10;
		$proof='poe_soft';
		$pr='poe_hard';
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
		$this->db-> from('user_subscriptions');		
		$this->db-> where('type',$proof); 
		$this->db->or_where('type',$pr); 
		  
		$this->db->order_by($sortname,$sortorder);
		$this->db->limit($rp,$pageStart);
        $query = $this->db->get();
		//$arr = $query -> result();
				 
        $data = array();
		$data['page'] = $page;
		$data['total'] = $query -> num_rows();
		$data['rows'] = array();
		     
        $count = 0;
        if($query -> num_rows() >0 )
		{
			$result = $query -> result();
			
			foreach($result as $row){
						
				 $count= $count+1;				  			
			 
				 $this->db-> select('first_name , last_name');
				 $this->db-> from('users');		
				 $this->db-> where('user_id',$row->user_id);
			     $username = $this->db->get();
		         $name =$username -> result();
			     foreach($name as $row1){
					  
				   $first_name= $row1->first_name." ".$row1->last_name;
				  
				 }
			     $this->db-> select('course_name');
				 $this->db-> from('courses');		
				 $this->db-> where('course_id',$row->course_id);
			     $coursename = $this->db->get();
		         $course =$coursename -> result();
			     foreach($course as $row2){
					  
				   $course_name= $row2->course_name;
				 }
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($count,$first_name,$course_name,$row->date_applied)
			);
				 }
			
		}
      
             
       echo json_encode($data); exit(); 
	}
	
	
	
	function proof_completion_subscription_browse()
	{
		$content = array();
		$data['view'] = 'proof_completion_subscription';		
		$data['content'] = $content;
		$this->load->view('admin/template',$data);	
	}
	
	function fetch_proof_completion_subscription()
	{
		$input='';
		$page = 1;	// The current page
		$sortname = 'id';	 // Sort column
		$sortorder = 'asc';	 // Sort order
		$qtype = '';	 // Search column
		$query = '';	 // Search string
		$rp=10;
		$proof_completion="proof_completion";
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
		$this->db-> from('user_subscriptions');
		$this->db-> where('type',$proof_completion);
	//	$this->db->order_by($sortname,$sortorder);
	  //  $this->db->limit($rp,$pageStart);
        $query = $this->db->get();
		//$result = $query -> result();
		//echo "<pre>";print_r($result);exit;
	
		$data['page'] = $page;
		$data['total'] = $query -> num_rows();
		$data['rows'] = array();
		     
       
        if($query -> num_rows() >0 )
		{
			$result = $query -> result();
		foreach($result as $row)
		{
	    	$this->db-> select('first_name');
		    $this->db-> from('users');
			$this->db-> where('user_id',$row->user_id);
			$query1 = $this->db->get();
			
			if($query1 -> num_rows() >0 )
		{
			$result1 = $query1 -> result();
			foreach($result1 as $row1)
			{
			$this->db-> select('course_name');
		    $this->db-> from('courses');
			$this->db-> where('course_id',$row->course_id);
			$query2 = $this->db->get();
			if($query2 -> num_rows() >0 )
		{
			$result2 = $query2 -> result();
				foreach($result2 as $row2)
			{
				
				
			
			//echo $ebook_details;exit;
			
	    	
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($row->id,$row1->first_name,$row2->course_name,$row->date_applied)
			);
		}
		}
		}
		}
		}
		}
		
          
       echo json_encode($data); exit(); 
	}
	
	
	function meterialaccess_subscription_browse()
	{
		$content = array();
		$data['view'] = 'meterial_access_subscription';		
		$data['content'] = $content;
		$this->load->view('admin/template',$data);	
	}
	
	function fetch_meterialaccess_subscription()
	{
		
		$input='';
		$page = 1;	// The current page
		$sortname = 'id';	 // Sort column
		$sortorder = 'asc';	 // Sort order
		$qtype = '';	 // Search column
		$query = '';	 // Search string
		$rp=10;
	//	$material_subscription="access_6 OR access_12";
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
		$this->db-> from('user_subscriptions');
		$this->db-> where('type','access_6');
		$this->db-> or_where('type','access_12');
	//	$this->db->order_by($sortname,$sortorder);
	  //  $this->db->limit($rp,$pageStart);
        $query = $this->db->get();
		//$result = $query -> result();
		//echo "<pre>";print_r($result);exit;
	
		$data['page'] = $page;
		$data['total'] = $query -> num_rows();
		$data['rows'] = array();
		     
        $total_array = $query->result();
		
		$result = array_slice($total_array,$pageStart,$rp);
        if($query -> num_rows() >0 )
		{
		
			
		
			//$result = $query -> result();
			
			/*echo "<pre>";
			print_r($result);*/
		
			foreach($result as $row)
			{		
				$period ='';
				if($row->type == "access_6")
				{
					$period='6 months';
				}
				if($row->type == "access_12")
				{
					$period='12 months';
				}
				
			//	echo "<br> user id ".$row->user_id;
					$this->db-> select('*');
					$this->db-> from('payments');
					$this->db-> where('user_id',$row->user_id);
					$this->db-> where('product_id','46');
					//$this->db-> or_where('product_id',46);
					$query5 = $this->db->get();
					if($query5 -> num_rows() >0 )
					{
						$result5 = $query5 -> result();
						
					
						/*echo "<br>------------------------------";
						echo "<pre>";
						print_r($result5);*/
					//	exit;
						
						
						foreach($result5 as $row5)
						{
							$amount1 = $row5->amount;
							$cur_code = $this->common_model->get_currency_symbol_from_id($row5->currency_id);
							$amount = $amount1.' '.$cur_code;
							//echo "<br>Here amnt ".$amount1.' '.$cur_code;
						}
						
					}
					else
					{
							$this->db-> select('*');
					$this->db-> from('payments');
					$this->db-> where('user_id',$row->user_id);
					$this->db-> where('product_id','47');
					//$this->db-> or_where('product_id',46);
					$query5 = $this->db->get();
					if($query5 -> num_rows() >0 )
					{
						$result5 = $query5 -> result();
						
					
						/*echo "<br>------------------------------";
						echo "<pre>";
						print_r($result5);*/
					//	exit;
						
						
						foreach($result5 as $row5)
						{
							$amount1 = $row5->amount;
							$cur_code = $this->common_model->get_currency_symbol_from_id($row5->currency_id);
							$amount = $amount1.' '.$cur_code;
						//	echo "<br>Here amnt ".$amount1.' '.$cur_code;
						}
						
					}
					}
			//	exit;
				
	    	$this->db-> select('user_id,first_name,last_name');
		    $this->db-> from('users');
			$this->db-> where('user_id',$row->user_id);
			$query1 = $this->db->get();
			
			if($query1 -> num_rows() >0 )
			{
			$result1 = $query1 -> result();
			foreach($result1 as $row1)
			{
			$this->db-> select('course_name');
		    $this->db-> from('courses');
			$this->db-> where('course_id',$row->course_id);
			$query2 = $this->db->get();
			if($query2 -> num_rows() >0 )
			{
			$result2 = $query2 -> result();
			foreach($result2 as $row2)
			{		
				
				
			//$amount =0;	
			$name = $row1->first_name.' '.$row1->last_name;
			$user_id = $row1->user_id;
			
						
					
			}
			}
			}
			}		
					
				//	echo "<br> Amount ".$amount;
				
			
			
			//echo $ebook_details;exit;
			
	    	
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($row1->user_id,$name,$row2->course_name,$period,$amount,$row->date_applied)
			);
		
		}
		}
		
          
       echo json_encode($data); exit(); 
	
	}
	
	
	
	function fetch_meterialaccess_subscription_test()
	{
		
		$input='';
		$page = 1;	// The current page
		$sortname = 'id';	 // Sort column
		$sortorder = 'asc';	 // Sort order
		$qtype = '';	 // Search column
		$query = '';	 // Search string
		$rp=10;
	//	$material_subscription="access_6 OR access_12";
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
		$this->db-> from('user_subscriptions');
		$this->db-> where('type','access_6');
		$this->db-> or_where('type','access_12');
	//	$this->db->order_by($sortname,$sortorder);
	  //  $this->db->limit($rp,$pageStart);
        $query = $this->db->get();
		//$result = $query -> result();
		//echo "<pre>";print_r($result);exit;
	
		$data['page'] = $page;
		$data['total'] = $query -> num_rows();
		$data['rows'] = array();
		     
       
        if($query -> num_rows() >0 )
		{
		
			
		
			$result = $query -> result();
			
			/*echo "<pre>";
			print_r($result);*/
		
			foreach($result as $row)
			{		
				$period ='';
				if($row->type == "access_6")
				{
					$period='6 months';
				}
				if($row->type == "access_12")
				{
					$period='12 months';
				}
				
				echo "<br> user id ".$row->user_id;
					$this->db-> select('*');
					$this->db-> from('payments');
					$this->db-> where('user_id',$row->user_id);
					$this->db-> where('product_id','46');
					//$this->db-> or_where('product_id',46);
					$query5 = $this->db->get();
					if($query5 -> num_rows() >0 )
					{
						$result5 = $query5 -> result();
						
					
						/*echo "<br>------------------------------";
						echo "<pre>";
						print_r($result5);*/
					//	exit;
						
						
						foreach($result5 as $row5)
						{
							$amount1 = $row5->amount;
							$cur_code = $this->common_model->get_currency_symbol_from_id($row5->currency_id);
							$amount = $amount1.' '.$cur_code;
							echo "<br>Here amnt ".$amount1.' '.$cur_code;
							echo $amount;
						}
						
					}
					else
					{
							$this->db-> select('*');
					$this->db-> from('payments');
					$this->db-> where('user_id',$row->user_id);
					$this->db-> where('product_id','47');
					//$this->db-> or_where('product_id',46);
					$query5 = $this->db->get();
					if($query5 -> num_rows() >0 )
					{
						$result5 = $query5 -> result();
						
					
						/*echo "<br>------------------------------";
						echo "<pre>";
						print_r($result5);*/
					//	exit;
						
						
						foreach($result5 as $row5)
						{
							$amount1 = $row5->amount;
							$cur_code = $this->common_model->get_currency_symbol_from_id($row5->currency_id);
							$amount = $amount1.' '.$cur_code;
							echo "<br>Here amnt ".$amount1.' '.$cur_code;
							echo $amount;
						}
						
					}
					}
			//	exit;
				
	    	$this->db-> select('user_id,first_name,last_name');
		    $this->db-> from('users');
			$this->db-> where('user_id',$row->user_id);
			$query1 = $this->db->get();
			
			if($query1 -> num_rows() >0 )
			{
			$result1 = $query1 -> result();
			foreach($result1 as $row1)
			{
			$this->db-> select('course_name');
		    $this->db-> from('courses');
			$this->db-> where('course_id',$row->course_id);
			$query2 = $this->db->get();
			if($query2 -> num_rows() >0 )
			{
			$result2 = $query2 -> result();
			foreach($result2 as $row2)
			{		
				
				
			//$amount =0;	
			$name = $row1->first_name.' '.$row1->last_name;
			$user_id = $row1->user_id;
			
						
					
			}
			}
			}
			}		
					
				//	echo "<br> Amount ".$amount;
				
			
			
			//echo $ebook_details;exit;
			
	    	
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($row1->user_id,$name,$row2->course_name,$period,$amount,$row->date_applied)
			);
		
		}
		}
		
          
       echo json_encode($data); exit(); 
	
	}
	
	
		function browse_course_packages()
	{
		$content = array();
		$data['view'] = 'sales_course_pack_browse';		
		$data['mode'] = 0;
		$data['content'] = $content;
		$this->load->view('admin/template',$data);		
	
		
	}
	
	function fetch_course_packages()
	{ 
		
		$page = 1;	// The current page
		$sortname = 'id';	 // Sort column
		$sortorder = 'asc';	 // Sort order
		$qtype = '';	 // Search column
		$query = '';	 // Search string
		$rp=10;
		$course='course';
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
		$this->db-> from('course_package_types');
		
		
		$this->db->order_by($sortname,$sortorder);
		$this->db->limit($rp,$pageStart);
        $query = $this->db->get();
        
        
        $data = array();
		$data['page']  = $page;
		$data['total'] = $query -> num_rows();
		$data['rows']  = array();
		
	
        if($query -> num_rows() >0 )
		{
			$result = $query -> result();
			
			foreach($result as $row){	
			
			
		     	$this->db-> select('id');
		        $this->db-> from('products');
				$this->db-> where('units',$row->id);
				$this->db-> where('type',$course);
				$this->db-> where('item_id','');
				 $query1 = $this->db->get();
				 
				 if($query1-> num_rows() >0 )
		{
			$result1 = $query1 -> result();
			
			foreach($result1 as $row1){	
				
		
				$action_add_price = '<a href="'.base_url().'admin/sales/product_price_add/'.$row1->id.'">Add Price</a>';
				 
				$action_view_price = '<a href="'.base_url().'admin/sales/view_product_price/'.$row1->id.'">View Price</a>';
		 		
				$action = '<a href="'.base_url().'admin/sales/product_price_edit/'.$row1->id.'">Edit</a>';
			   
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($row->id,$row->package_type,$action_add_price,$action_view_price,$action)
				);
			}
			
		}
			}
		
		}
             
       echo json_encode($data); exit(); 
		
		
		
	}
	
	function sales_purchase_details()
	{
		 if(isset($this->flashmessage))
        $data['flashmessage'] = $this->flashmessage;	
		$content['searchmode'] = false;
        $data['view'] = 'sales_purchase_list';
        $data['content'] = $content;
        
        $this->load->view('admin/template',$data);
	}
	
	function fetch_sales_purchases()
	{
		
		
		$page = 1;	// The current page
		$sortname = 'date';	 // Sort column
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
		
		// Setup paging SQL
		 $pageStart = ($page-1)*$rp;
       
       
        $this->db-> select('users.user_id, first_name,last_name,username,email,country_id,payments.date,payments.amount,payments.product_id,currency.currency_symbol,sales_cart_main.source');
		$this->db-> from('payments');
		$this->db-> where('payments.type',"sales");
	//	$this->db->group_by('users.user_id');
		$this->db-> order_by('payments.date', 'asc');
		$this->db->join('users',"payments.user_id = users.user_id");
		$this->db->join('currency',"currency.id = payments.currency_id");
		$this->db->join('sales_cart_main',"sales_cart_main.transaction_id=payments.transaction_id");
		//$this->db->join('payments',"payments.type = 'sales'");
		
	
		if(isset($_GET['first_name']) && $_GET['first_name']!=''){
			$this->db->like('first_name', $_GET['first_name']); 

		}
		if(isset($_GET['last_name']) && $_GET['last_name']!=''){
			$this->db->like('last_name', $_GET['last_name']); 

		}
		if(isset($_GET['user_name']) && $_GET['user_name']!=''){
			$this->db->where('username', $_GET['user_name']); 

		}	
		
		
		if(isset($_GET['start_date']) && $_GET['start_date']!=''){
			$this->db->where('payments.date >=', $_GET['start_date']); 

		}	
		if(isset($_GET['end_date']) && $_GET['end_date']!=''){
			$this->db->where('payments.date <', $_GET['end_date']); 

		}	

		$this->db->order_by($sortname,$sortorder);
		
		
        $query = $this->db->get();
       
        $data = array();
		$data['page'] = $page;
		$data['total'] = $query -> num_rows();	
		
		$total_array = $query->result();
		
		$result = array_slice($total_array,$pageStart,$rp);
		
		$data['rows'] = array();
		
       
        $sl_no =1;
        if($query -> num_rows() >0 )
		{
			
			foreach($result as $row)
			{
					
				
				$product_details = $this->common_model->get_product_details($row->product_id);
							
					  
				$action = '<a href="'.base_url().'admin/sales/purchase_details/'.$row->user_id.'" target="blank">Details</a>';
				
				
				
				 $product_name = '';
				  
				  if($product_details[0]->type=='course')
				  {
					  $product_name = 'Course';
				  }
				  else if($product_details[0]->type=='hardcopy')
				  {
					  $product_name = 'Certficate hardcopy copy';
				  }				 
				  else if($product_details[0]->type=='extension')
				  {
					  $product_name = 'Extension';
				  }
				  else if($product_details[0]->type=='access')
				  {
					  $product_name = 'Course access';
				  }
				  else if($product_details[0]->type=='ebooks')
				  {
					  $product_name = 'Ebooks';
				  }				  
				  else if($product_details[0]->type=='transcript')
				  {
					  $product_name = 'Transcript';
				  }
				  else if($product_details[0]->type=='transcript_hard')
				  {
					  $product_name = 'Transcript hard copy';
				  }				  
				  else if($product_details[0]->type=='proof_completion')
				  {
					  $product_name = 'Proof completion soft copy';
				  }
				  else if($product_details[0]->type=='proof_completion_hard')
				  {
					  $product_name = 'Proof completion hard copy';
				  }
				  else if($product_details[0]->type=='poe_soft')
				  {
					  $product_name = 'Proof of enrollement soft copy';
				  }				  
				  else if($product_details[0]->type=='poe_hard')
				  {
					  $product_name = 'Proof of enrollement hard copy';
				  }
				  else if($product_details[0]->type=='colour_wheel_soft')
				  {
					  $product_name = 'Colour wheel';
				  }
				  else if($product_details[0]->type=='colour_wheel_hard')
				  {
					  $product_name = 'Colour wheel hard copy';
				  }
				  
				
				$purchased_date = new DateTime($row->date);			
			  	$purchased_date = date_format($purchased_date,'Y-m-d');			  
				$data['rows'][] = array(
				'id' => $row->user_id,
				'cell' => array($sl_no,$row->first_name.' '.$row->last_name,$product_name,$row->source,$row->amount.' '.$row->currency_symbol,$purchased_date,$action)
			);
			$sl_no++;
			}
			
		}
        
        
       
       // $pagelist = $this->getpagelist($lang);
        
        
        
  
       echo json_encode($data); exit(); 
		
		
	}
	
	function search_purchased_user()
	{
		$first_name = $this->input->post('first_name');
		$last_name = $this->input->post('last_name');
		$user_name = $this->input->post('user_name');
		
		$start_date = $this->input->post('start_date');
		$end_date  = $this->input->post('end_date');
		
		$buttonAction = $this->input->post('generate_excel');
		
		
		
						
			$content['first_name'] = isset($first_name)?$first_name:'';
			$content['last_name'] = isset($last_name)?$last_name:'';
			$content['user_name'] = isset($user_name)?$user_name:'';
			$content['start_date'] = isset($start_date)?$start_date:'';
			$content['end_date'] = isset($end_date)?$end_date:'';
			
		if($buttonAction=='Generate Excel')
		{
			
			
			
			$this->db->start_cache();
			
			if(isset($content['first_name']) && $content['first_name']!=''){
				$this->session->set_flashdata('first_name',$content['first_name']);
			}
			if(isset($content['last_name']) && $content['last_name']!=''){
				$this->session->set_flashdata('last_name',$content['last_name']);
			}
			if(isset($content['user_name']) && $content['user_name']!=''){
				$this->session->set_flashdata('user_name',$content['user_name']);
			}
			if(isset($content['start_date']) && $content['start_date']!=''){
				$this->session->set_flashdata('start_date',$content['start_date']);
			}	
			if(isset($content['end_date']) && $content['end_date']!=''){
				$this->session->set_flashdata('end_date',$content['end_date']); 	
			}
						
			$this->db->stop_cache();
			redirect('deeps_home/generate_sales_report');
			
		}
		
		
		$content['searchmode'] = true;		
				
		$data['view'] = 'sales_purchase_list';
		
        $data['content'] = $content;
        
        $this->load->view('admin/template',$data);
	}
	
	function purchase_details($userId)
	{
		 $this->db-> select('users.user_id as userId, first_name,last_name,username,password,course_name,courses.course_id as courId, course_enrollments.date_enrolled, course_enrollments.date_expiry,email, country_id,course_enrollments.course_id as courses_s_id, status,address,street, zipcode,city,dob,user_pic,course_enrollments.course_status,gender,contact_number,newsletter,lang_id');
		$this->db-> from('users');
		$this->db->join('course_enrollments','course_enrollments.user_id = users.user_id');
		$this->db->join('courses','courses.course_id = course_enrollments.course_id');
		$this->db->where('users.user_id',$userId);
		
		$query = $this->db->get();
		
		$data = array();
		  
        if($query -> num_rows() >0 )
		{
			$data =array();
			$data['courseCount']=$query -> num_rows();
			//echo "<pre>";print_r($query->result());exit;
			$row = $query -> row();
			//echo "<pre>";print_r($row);echo"</pre>";
				
				
				$content['user_id']=$row->userId;
				$content['first_name']=$row->first_name;
				$content['last_name']=$row->last_name;
				$content['username']=$row->username;
				$content['contact_num']=$row->contact_number;
				$content['password']=$this->encrypt->decode($row->password);
				$content['lang_id'] = $row->lang_id;
				$content['address']=$row->address;
				$content['street']=$row->street;
				$content['zipcode']=$row->zipcode;
				$content['city']=$row->city;
				
				$content['email']=$row->email;
				$content['country_id']=$row->country_id;
				$content['country_name']=$this->user_model->get_country_name($row->country_id);
				$content['status']=$row->status;
				$content['dob']=$row->dob;
				$gender=$row->gender;
				$content['gender']=($gender==1)?'Male':'Female';
				$content['newsletter']=$row->newsletter;
				$content['picPath']=$row->user_pic;
				
				
				$date1 = $content['dob'];
				$date2 = date("Y-m-d");
				$diff = abs(strtotime($date2) - strtotime($date1));
				$content['old'] = floor($diff / (365*60*60*24));
				
				$i=0;
				
				/*echo "<pre>";
				print_r($query->result());
				exit;*/
				
				$paid_main_cart_details  = $this->sales_model->get_paid_cart_details($userId);
				/*echo "Herer	";
				echo "<pre>";
				print_r($paid_main_cart_details);*/
				//exit;
				
				$user_sales_purchase_details = $this->sales_model->get_user_sales_purchase_details($userId);
				
				
				
				$q=0;
				if(!empty($user_sales_purchase_details))
				{
				foreach($user_sales_purchase_details as $sales_det)
				{
					
					$sales_source[$q] = $this->sales_model->get_cart_source_from_session_id($sales_det->sales_session_id);
					$cart_main_id = $this->sales_model->get_cart_main_id_from_session_id($sales_det->sales_session_id);
					
					
					/*echo "<br>Session id ".$sales_det->sales_session_id;
					echo "<br>Cart main id id ".$cart_main_id;*/
					
					$product_details = $this->common_model->get_product_details($sales_det->product_id);
					
				
					
					$currency_symbol[$q] = $this->common_model->get_currency_symbol_from_id($sales_det->currency_id);
					
					$sales_amount[$q] = $currency_symbol[$q].' '.$sales_det->amount;
					
					$products_in_cart = $this->sales_model->get_cart_items_from_cart_id_product_id($cart_main_id,$sales_det->product_id);
					
					
						if(!empty($products_in_cart))
						{
							foreach($products_in_cart as $prod)
							{
								$purchased_item_names[$q] ='';
								//$product_id_in_cart = $prod->product_id;
								
								$product_details = $this->common_model->get_product_details($prod->product_id);
								/*echo "<br>Cart item  details";
								echo "<pre>";
								print_r($product_details);*/
								
								$cart_item_details = $this->sales_model->get_cart_items_details($cart_main_id,$prod->id);
								
								/*echo "<br>Cart item  details";
								echo "<pre>";
								print_r($cart_item_details);*/
								foreach($cart_item_details as $item_det)
								{
									$selected_items = $item_det->selected_item_ids;
									if($item_det->product_type == 'ebooks')
									{
										$ebook_ids = explode(',',$selected_items);
										
										$product_name[$q] =  'Ebook';
										
										for($qq1=0;$qq1<count($ebook_ids);$qq1++)
										{
											$ebook_details = $this->ebook_model->get_ebook_details($ebook_ids[$qq1]);
											/*echo "<br>Ebook Details";
											echo "<pre>";
											print_r($ebook_details);*/
											if($purchased_item_names[$q]=='')
											{
												$purchased_item_names[$q] = $ebook_details[0]->ebookName;
											}
											else
											{
												$purchased_item_names[$q] .=','.$ebook_details[0]->ebookName;
											}
										}
									}
									else if($item_det->product_type == 'course')
									{
										$course_ids = explode(',',$selected_items);
										
										$product_name[$q] =  'Course';
										
									/*	echo "<br>In course";*/
										
										for($qq=0;$qq<count($course_ids);$qq++)
										{
											if($purchased_item_names[$q]=='')
											{
												$purchased_item_names[$q] = $this->common_model->get_course_name($course_ids[$qq]); 
											}
											else
											{
												$purchased_item_names[$q] .= ','.$this->common_model->get_course_name($course_ids[$qq]); 
											}
										}
										
									}
									else if($item_det->product_type == 'extension')
									{
										$product_name[$q] =  'Course Extension';
										//$purchased_item_names[$q] = 'Course Extension';
										$extension_details = $this->sales_model->get_extension_details_by_units($product_details[0]->units);	
										$purchased_item_names[$q] = $extension_details[0]->extension_option;
									}
									else if($item_det->product_type == 'colour_wheel_soft')
									{
										$product_name[$q] =  'Colour wheel';
										$purchased_item_names[$q] = 'Downloadable copy';
									}
									else if($item_det->product_type == 'colour_wheel_hard')
									{
										$product_name[$q] =  'Colour wheel';							
										$purchased_item_names[$q] = 'Hard copy';
									}
									else if($item_det->product_type == 'poe_soft')
									{
										$product_name[$q] =  'Proof of study';							
										$purchased_item_names[$q] = 'Soft copy';
									}
									else if($item_det->product_type == 'poe_hard')
									{
										$product_name[$q] =  'Proof of study';							
										$purchased_item_names[$q] = 'Hard copy';
									}
									else if($item_det->product_type == 'hardcopy')
									{
										$product_name[$q] =  'Certficate';							
										$purchased_item_names[$q] = 'hard copy';
									}
									/*else if($item_det->product_type == 'cert_hard_2')
									{
										$product_name[$q] =  'Certficate hard copy';						
										$purchased_item_names[$q] = 'Rest of world';
									}
									else if($item_det->product_type == 'cert_hard_3')
									{
										$product_name[$q] =  'Certficate hard copy';						
										$purchased_item_names[$q] = 'EU Airmail';
									}			*/	
									
									else if($item_det->product_type == 'proof_completion')
									{
										$product_name[$q] =  'Proof of completion';							
										$purchased_item_names[$q] = 'Soft copy';
									}
									else if($item_det->product_type == 'proof_completion_hard')
									{
										$product_name[$q] =  'Proof of completion';							
										$purchased_item_names[$q] = 'Hard copy';
									}
									
									else if($item_det->product_type == 'transcript')
									{
										$product_name[$q] =  'eTranscript';							
										$purchased_item_names[$q] = 'Soft copy';
									}
									else if($item_det->product_type == 'transcript_hard')
									{
										$product_name[$q] =  'eTranscript';							
										$purchased_item_names[$q] = 'Hard copy';
									}
									else if($item_det->product_type == 'access')
									{
										/*
										echo "<pre>";
										print_r($product_details);
										exit;*/
										$product_name[$q] =  'Material subscription';							
										$purchased_item_names[$q] = $product_details[0]->item_id.' Months' ;
									}
									
								}
																
								//$product_name[$q] =  $product_details[0]->type;
								
							 }
						}
					$q++;
				}
				
				
				/*echo "<pre>";
				print_r($user_sales_purchase_details);
					   
				
				echo "<br> Sales source";
				echo "<pre>";
				print_r($sales_source);
				
				echo "<br> Sales Amount";
				echo "<pre>";
				print_r($sales_amount);
				
				echo "<br> Product name";
				echo "<pre>";
				print_r($product_name);
				
				echo "<br> Purchased item names";
				echo "<pre>";
				print_r($purchased_item_names);			
			
				exit;*/
				
			
					
					
					
					
					
					//exit;
			//	$user_sales_purchase_deatails = $this->sales_model->get_user_sales_purchase_details($userId);
				
				
				$currency_symbol = $this->common_model->get_currency_symbol_from_id($user_sales_purchase_details[0]->currency_id);
				
				$content['sales_source']	     = $sales_source;
				$content['sales_amount']	     = $sales_amount;
				
				
				$content['purchased_item_names'] = $purchased_item_names;
				$content['products_in_cart']     = $products_in_cart;
				$content['product_name']         = $product_name;
				$content['user_sales_purchase_details']    = $user_sales_purchase_details;	
				$data['currency_symbol'] = $currency_symbol;	
				
				}		
			
				$data['view'] = 'sales_user_details';
				$data['content'] = $content;
				
				$this->load->view('admin/template',$data);
	
		}	
	}
	
	
	function browse_ebook_public_packages()
	{
		$content = array();
		$data['view'] = 'sales_ebook_pack_public_browse';		
		$data['mode'] = 0;
		$data['content'] = $content;
		$this->load->view('admin/template',$data);		
	
		
	}
	
	function fetch_ebook_public_packages()
	{
		$page = 1;	// The current page
		$sortname = 'id';	 // Sort column
		$sortorder = 'asc';	 // Sort order
		$qtype = '';	 // Search column
		$query = '';	 // Search string
		$rp=10;
		$ebooks='ebooks_public';
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
		$this->db-> from('ebook_package_types');
		
		
		$this->db->order_by($sortname,$sortorder);
		$this->db->limit($rp,$pageStart);
        $query = $this->db->get();
        
        
        $data = array();
		$data['page']  = $page;
		$data['total'] = $query -> num_rows();
		$data['rows']  = array();
		
	
        if($query -> num_rows() >0 )
		{
			$result = $query -> result();
			
			foreach($result as $row){	
			
			
		     	$this->db-> select('id');
		        $this->db-> from('products');
				$this->db-> where('units',$row->id);
				$this->db-> where('type',$ebooks);
				 $query1 = $this->db->get();
				 if($query1-> num_rows() >0 )
		{
			$result1 = $query1 -> result();
			
			foreach($result1 as $row1){	
				
		
				$action_add_price = '<a href="'.base_url().'admin/sales/product_price_add/'.$row1->id.'">Add Price</a>';
				 
				$action_view_price = '<a href="'.base_url().'admin/sales/view_product_price/'.$row1->id.'">View Price</a>';
		 		
				//$action = '<a href="'.base_url().'admin/sales/product_price_edit/'.$row1->id.'">Edit</a>';
			   
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($row->id,$row->package_type,$action_add_price,$action_view_price)
				);
			}
			
		}
			}
		
		}
             
       echo json_encode($data); exit(); 
		
		
		
	}
	
	
	
	
}



?>