<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class ebooks extends CI_Controller
{
	
	function __construct()
 	{
  		parent::__construct();
  		
  		if(!$this->session->userdata('admin_logged_in'))
   		redirect('admin/login', 'refresh');
		
		$this->load->library('form_validation');
		$this->load->model('common_model','',TRUE);
		$this->load->model('ebook_model','',TRUE);
		
		if($message = $this->session->flashdata('message')){
          $this->flashmessage =$message;
   		}
  		 		
 	}
	
	function index()
 	{
 	
 	   
 	}
	
	function browse_ebook()
	{
		$content = array();
		
		$content['searchmode'] = true;		
		$data['view'] = 'ebook_browse';
		
        $data['content'] = $content;
        
        $this->load->view('admin/template',$data);	
	}
	
	
	
	function fetch_ebooks()
	{
		
		$page = 1;	// The current page
		$sortname = 'ebid';	 // Sort column
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
		$this->db-> from('ebook');
		
		
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
		
			    /* $action_add_price = '<a href="'.base_url().'admin/ebooks/ebook_add_price/'.$row->ebid.'"><input type="button" class="edit_btn right" value="Add Price"></a>';
				 
				  $action_view_price = '<a href="'.base_url().'admin/ebooks/ebook_view_price/'.$row->ebid.'"><input type="button" class="edit_btn right" value="View Price"></a>';
				 
				 $action = '<a href="'.base_url().'admin/ebooks/ebok_edit/'.$row->ebid.'">Edit</a>&nbsp;&nbsp;<a href="'.base_url().'admin/ebooks/ebook_delete/'.$row->ebid.'">Delete</a>';*/
				 
				// $action_add_price = '<a target="_blank" href="'.base_url().'admin/ebooks/ebook_add_price/'.$row->ebid.'">Add Price</a>';
				 
				//  $action_view_price = '<a target="_blank" href="'.base_url().'admin/ebooks/ebook_view_price/'.$row->ebid.'">View Price</a>';
				 
				 $action = '<a href="'.base_url().'admin/ebooks/ebook_edit/'.$row->ebid.'">Edit</a>&nbsp;&nbsp;<a href="'.base_url().'admin/ebooks/ebook_delete/'.$row->ebid.'">Delete</a>';
				 
				 
			   
				 $data['rows'][] = array(
				'id' => $row->ebid,
				'cell' => array($row->ebid,$row->ebookName,$row->language,$row->status,$action)
			);
			}
			
		}
      
             
       echo json_encode($data); exit(); 
		
		
		
	
	}
	
	function add_ebook($lang_id=4)
	{
	
		$content = array();
		
		// field name, error message, validation rules
		if(isset($_POST['save_ebook']))
		{
			$ebook_data  = array();			
		    $ebook_data['ebookName']   = $content['ebook_name'] = $this->input->post('ebook_name');
		    $ebook_data['langid'] 	   = $content['language'] = $this->input->post('language');
		    $ebook_data['courseId']    = $content['base_course'] = $this->input->post('base_course');
			$ebook_data['description'] = $content['ebook_desc'] = $this->input->post('ebook_desc');
			
			$ebook_data['language']  = $this->common_model->get_language_name($ebook_data['langid']);
			
			if ($this->input->post('ebook_pdf')) {
			
				$this->ebook_model->upload_ebook();
				$image_data = $this->upload->data();
				
				$ebook_data['fileName']=$this->input->post('ebook_pdf');
			}
		
					
		    $this->form_validation->set_rules('ebook_name', 'Ebook name', 'trim|required');
			$this->form_validation->set_rules('language', 'Lanaguage', 'required');			
			$this->form_validation->set_rules('base_course', 'Base course', 'required');
			$this->form_validation->set_rules('ebook_desc', 'Description', 'required');			

			if($this->form_validation->run())
			{
				
			 	$this->ebook_model->add_ebook($ebook_data);			 
			 	redirect('admin/ebooks/browse_ebook', 'refresh');
			}
		}	
		$content['lang_id'] = $lang_id;
		$data['view'] = 'ebook_add';
		$content['language']=$this->common_model->get_languages();	
		$content['base_course']=$this->common_model->get_base_courses($lang_id);
		$data['mode'] = 0;	
		$data['content'] = $content;
		$this->load->view('admin/template',$data);
	
	}
	
	function ebook_edit($ebook_id)
	{
		
		$content = array();		
		
		$ebook_details = $this->ebook_model->get_ebook_details($ebook_id);
		
		foreach($ebook_details as $eb_data)
		{
			$content['ebook_id'] = $eb_data->ebid;
			$content['ebook_name'] = $eb_data->ebookName;
			
			$lang_id = $content['lang_id'] = $eb_data->langid;
			
			$content['language'] = $eb_data->language;
			$content['description'] = $eb_data->description;
			$content['file_name'] = $eb_data->fileName;
			$content['sample_ebook_name'] = $eb_data->sample_ebook_name;			
			$content['status'] = $eb_data->status;
			$content['course_id'] = $eb_data->courseId;
			
			$content['image_name'] = $eb_data->image_name;
			
		}
				
		
		// field name, error message, validation rules
		if(isset($_POST['save_ebook']))
		{
			$ebook_data  = array();			
		    $ebook_data['ebookName']   = $content['ebook_name'] = $this->input->post('ebook_name');
		//    $ebook_data['langid'] 	   = $content['language'] = $this->input->post('language');
		    $ebook_data['courseId']    = $content['base_course'] = $this->input->post('base_course');
			$ebook_data['description'] = $content['ebook_desc'] = $this->input->post('ebook_desc');
			
		//	$ebook_data['language']  = $this->common_model->get_language_name($ebook_data['langid']);
			
			
			if ($this->input->post('ebook_pdf')) {
			
				$this->ebook_model->upload_ebook();
				$image_data = $this->upload->data();
				$ebook_data['fileName']=$this->input->post('ebook_pdf');
			}
		
					
		    $this->form_validation->set_rules('ebook_name', 'Ebook name', 'trim|required');
			//$this->form_validation->set_rules('language', 'Lanaguage', 'required');			
		//	$this->form_validation->set_rules('base_course', 'Base course', 'required');
			$this->form_validation->set_rules('ebook_desc', 'Description', 'required');			

			if($this->form_validation->run())
			{
				
			 	$this->ebook_model->update_ebook($ebook_data,$ebook_id);			 
			 	redirect('admin/ebooks/browse_ebook', 'refresh');
			}
		}	
		
		if($lang_id=='')
		{
			$lang_id=4;
		}
		$content['lang_id'] = $lang_id;
		$data['view'] = 'ebook_add';
		$content['language']=$this->common_model->get_languages();	
		$content['base_course']=$this->common_model->get_base_courses($lang_id);
		$data['mode'] = 1;	
		$data['content'] = $content;
		$this->load->view('admin/template',$data);
	
		
	}
	
	
	function ebook_add_price($ebid)
	{
		
		$content = array();
		
		if(isset($_POST['save_price']))
		{
		
			$ebook_price_data  = array();
			
		    $ebook_price_data['ebookId'] 	= $ebid;
			$ebook_price_data['type'] 		= ($this->input->post('user_type'));
			$ebook_price_data['amount']	    = ($this->input->post('ebook_price'));
			$ebook_price_data['fakePrice']	= ($this->input->post('fake_price'));
			$ebook_price_data['currencyId']	= ($this->input->post('currency'));
		    			 
			$this->form_validation->set_rules('currency', 'Currency', 'trim|required');
			$this->form_validation->set_rules('user_type', 'User type', 'trim|required');		
			$this->form_validation->set_rules('ebook_price', 'Price', 'trim|required');			
			
			if($this->form_validation->run())
			{	
			 	$this->ebook_model->add_ebook_price($ebook_price_data);
			 	 $this->session->set_flashdata('message', 'Ebook price added successfully!');
			 	 redirect('admin/ebooks/browse_ebook', 'refresh');
			}
		}
		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		
		if(isset($this->flashmessage)){
		$data['flashmessage'] = $this->flashmessage;
		}
		
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		$data['view'] = 'ebook_price_add';
		$data['currency'] = $this->common_model->get_currency();
		$data['ebid'] = $ebid;
		$data['mode'] = 0;
		$data['content'] = $content;
		$this->load->view('admin/template',$data);			
		
		
		
	
	}
	
	function ebook_view_price($ebid)
	{		
		$content = array();
		$data['view'] = 'ebook_price_view';		
		$data['ebid'] = $ebid;
		$data['mode'] = 0;
		$data['content'] = $content;
		$this->load->view('admin/template',$data);					
		
	}
	
	function fetch_ebook_price($ebid)
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
		$this->db-> from('ebookprice');
		$this->db-> where('ebookId',$ebid);
		
		
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
						
				 
				 $action = '<a href="'.base_url().'admin/ebooks/ebook_price_edit/'.$row->id.'">Edit</a>&nbsp;&nbsp;<a href="'.base_url().'admin/ebooks/ebook_price_delete/'.$row->id.'/'.$row->ebookId.'">Delete</a>';
				 
				 
			   
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($row->id,$row->type,$row->amount,$row->fakePrice,$row->currencyId,$action)
			);
			}
			
		}
      
             
       echo json_encode($data); exit(); 
		
		
		
	
	}
	
	function ebook_price_edit($price_id)
	{
		
		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		$content = array();
		//$id = $_GET['id'];
		$pagedata = $this->ebook_model->fetch_ebook_price($price_id);
	
		
		foreach($pagedata as $row){
			
			 $content['price_id'] 		 = $row->id;
			 $ebid = $content['ebook_id'] 	 = $row->ebookId;
			 $content['user_type'] 	 = $row->type;
     		 $content['amount']   	 = $row->amount;
			 $content['fake_price']  = $row->fakePrice;
			 $content['currency_id'] = $row->currencyId;			 
		}
		
		
		
		
		if(isset($_POST['save_price']))
		{
		
			$ebook_price_data  = array();
			
		    $ebook_price_data['ebookId'] 	= $ebid;
			$ebook_price_data['type'] 		= ($this->input->post('user_type'));
			$ebook_price_data['amount']	    = ($this->input->post('ebook_price'));
			$ebook_price_data['fakePrice']	= ($this->input->post('fake_price'));
			$ebook_price_data['currencyId']	= ($this->input->post('currency'));
		    			 
			$this->form_validation->set_rules('currency', 'Currency', 'trim|required');
			$this->form_validation->set_rules('user_type', 'User type', 'trim|required');		
			$this->form_validation->set_rules('ebook_price', 'Price', 'trim|required');			
			
			if($this->form_validation->run())
			{	
			 	$this->ebook_model->update_ebook_price($ebook_price_data,$price_id);
			 	 $this->session->set_flashdata('message', 'Ebook price updated successfully!');
			 	 redirect('admin/ebooks/ebook_view_price/'.$ebid, 'refresh');
			}
		}
		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		
		if(isset($this->flashmessage)){
		$data['flashmessage'] = $this->flashmessage;
		}
		
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		$data['view'] = 'ebook_price_add';
		$data['currency'] = $this->common_model->get_currency();
		$data['ebid'] = $ebid;
		$data['mode'] = 1;
		$data['content'] = $content;
		$this->load->view('admin/template',$data);			
		
		
	}
	
	function ebook_delete($ebook_id)
	{
		$this->ebook_model->delete_ebook($ebook_id);
		$this->session->set_flashdata('message', 'Ebook deleted successfully!');
		redirect('admin/ebooks/browse_ebook/', 'refresh');
	}
	
	function ebook_price_delete($price_id,$ebid)
	{
		$this->ebook_model->delete_ebook_price($price_id);
		$this->session->set_flashdata('message', 'Ebook price deleted successfully!');
		redirect('admin/ebooks/ebook_view_price/'.$ebid, 'refresh');
		
	}
	
	
	
	function validate($val,$name)
	{
		if($val=='')
		{
			 $this->form_validation->set_message('validate', 'Please Select the '.$name.' field');
                return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	//implementedted on 22-Nov
	function ebook_subscriptions_browse()
	{
		$content = array();
		$data['view'] = 'ebook_subscriptions';		
		$data['content'] = $content;
		$this->load->view('admin/template',$data);	
	}
	function fetch_ebook_subscriptions()
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
		
		// Setup paging SQL
		$pageStart = ($page-1)*$rp;
		
		$this->db-> select('*');
		$this->db-> from('ebooksubscription');		
	    $this->db->order_by($sortname,$sortorder);
	   // $this->db->limit($rp,$pageStart);
        $query = $this->db->get();
	
		$data['page'] = $page;
		$data['total'] = $query -> num_rows();
		$total_array = $query->result();
		$result = array_slice($total_array,$pageStart,$rp);
		$data['rows'] = array();
		     
        $count = 0;
        if($query -> num_rows() >0 )
		{
			//$result = $query -> result();
			
			
		
		foreach($result as $row)
		{
			$ebook_details=array();
			$flag='';
			$input=$row->ebook_id;
			//$input="1,2,3";
			$ebook_id_explode=explode(',',$input);
			for($i=0;$i<count($ebook_id_explode);$i++)
			{
			$this->db-> select('ebookName');
		    $this->db-> from('ebook');
			$this->db-> where('ebid',$ebook_id_explode[$i]);
			$querys = $this->db->get();
			if($flag!='')
			{
				$result_array=$querys -> result();
				foreach($result_array as $row5)
				{
					
			$ebook_details =$ebook_details.",".$row5->ebookName;
				}
			}
			else
			{
				$result_array=$querys -> result();
				foreach($result_array as $row5)
				{
					
			$ebook_details =$row5->ebookName;
				}
				
				$flag=1;
			}
		
			}
			//echo $ebook_details;exit;
			$this->db-> select('*');
		    $this->db-> from('users');
			$this->db-> where('user_id',$row->user_id);
			$query1 = $this->db->get();
			$result1 = $query1 -> result();
			
			foreach($result1 as $row1)
			{
	    	
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($row->id,$row1->first_name,$ebook_details,$row->date_purchased)
			);
		}
		
		}
		}
      
             
       echo json_encode($data); exit(); 
	}
	function ebook_public_browse()
	{
		$content = array();
		$data['view'] = 'ebook_subscriptions_public';		
		$data['content'] = $content;
		$this->load->view('admin/template',$data);	
	}
	function fetch_ebook_subscriptions_public()
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
		
		// Setup paging SQL
		$pageStart = ($page-1)*$rp;
		
		$this->db-> select('*');
		$this->db-> from('ebooksubscription_public');		
	//	$this->db->order_by($sortname,$sortorder);
	  //  $this->db->limit($rp,$pageStart);
        $query = $this->db->get();
	
		$data['page'] = $page;
		$data['total'] = $query -> num_rows();
		$data['rows'] = array();
		     
        $count = 0;
        if($query -> num_rows() >0 )
		{
			$result = $query -> result();
			
			
		
		foreach($result as $row)
		{
			$flag='';
			$ebook_details=array();
			$input=$row->ebook_id;
			//$input="1,2,3";
			$ebook_id_explode=explode(',',$input);
			for($i=0;$i<count($ebook_id_explode);$i++)
			{
			$this->db-> select('ebookName');
		    $this->db-> from('ebook');
			$this->db-> where('ebid',$ebook_id_explode[$i]);
			$querys = $this->db->get();
			if($flag!='')
			{
				$result_array=$querys -> result();
				foreach($result_array as $row5)
				{
					
			$ebook_details =$ebook_details.",".$row5->ebookName;
				}
			}
			else
			{
				$result_array=$querys -> result();
				foreach($result_array as $row5)
				{
					
			$ebook_details =$row5->ebookName;
				}
				
				$flag=1;
			}
		
			}
			//echo $ebook_details;exit;
			
	    	
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($row->id,$row->name,$row->email,$ebook_details,$row->date_purchased)
			);
		}
		
		}
		
          
       echo json_encode($data); exit(); 
	}
	
	/* ---  anoop added*/
	
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
		$material_subscription="material_subscription";
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
		$this->db-> where('type',$material_subscription);
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
	
	
	
}