<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class packages extends CI_Controller
{
 	function __construct()
 	{
  		parent::__construct();
  		
  		if(!$this->session->userdata('admin_logged_in'))
   		redirect('admin/login', 'refresh');
		
		$this->load->library('form_validation');
		$this->load->model('course_model','',TRUE);
		$this->load->model('common_model','',TRUE);
		
		$this->load->model('package_model','',TRUE);
		$this->load->model('sales_model','',TRUE);
		$this->load->model('certificate_model','',TRUE);
		$this->load->model('user_model','',TRUE);
		
		if($message = $this->session->flashdata('message')){
          $this->flashmessage =$message;
   		}
  		 		
 	}

 	function index()
 	{
 	
 	   
 	}
	
	
	

	
	function package_delete($id)
	{
		$this->package_model->delete_package($id);
		
		 $product_id = $this->common_model->getProdectId('package',$id,1);
		 $this->course_model->delete_course_fee($product_id);	
		 $this->common_model->delete_product($product_id);	
		$this->session->set_flashdata('message', 'Package deleted successfully!');
		redirect('admin/packages/browse_packages', 'refresh');
	}
	
	function package_read_more_old()
	{
		$content = array();
		$content['fee']=$this->course_model->get_currency();
		
		$prod_array = array('hardcopy','extension','ebooks','poe_hard','proof_completion_hard', 'transcript_hard');
		//$prod_array = array('extension');
		$count_prod_array =  count($prod_array);
		for($i=0;$i<$count_prod_array;$i++)
		{		
		
			
			$product_types_complete = $this->package_model->get_product_ids($prod_array[$i]);
			$q=0;
			foreach($product_types_complete as $prod_comp)
			{
				$product_types_array[$prod_array[$i]][$q] = $prod_comp->id; 
				$q++;
			}
			$product_types[$prod_array[$i]] = implode(',',$product_types_array[$prod_array[$i]]);
		
		}
		
		$content['prod_array'] = $prod_array;
		$content['product_types'] = $product_types;
		
		
		/*echo "<pre>";
		print_r($prod_array);
		print_r($product_types);		
		exit;*/
		
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		$data['view'] = 'package_read_more';
		
		
		$data['content'] = $content;
		$data['mode']=0;
		$this->load->view('admin/template',$data);	
	}
	
	function read_more($id)
	{
		$content = array();		
		$read_more_details = $this->package_model->get_read_more_deatils($id);			
		$data['read_more_details'] = $read_more_details;	
		
		if(isset($_POST['update_read_more']))
		{			
			$read_more_data_array  = array();			
			
			$read_more_data_array['description'] 	= $content['read_more_desc']   =($this->input->post('read_more_desc'));
    		$read_more_data_array['status']  = $content['pacakge_status'] =($this->input->post('read_more_status'));			
			$this->form_validation->set_rules('read_more_desc', 'Package Name', 'trim|required');	
			$this->form_validation->set_rules('read_more_status', 'Package Desc', 'trim|required');
			if($this->form_validation->run())
			{	
		         $this->package_model->update_read_more($read_more_data_array,$id);				
			 	 $this->session->set_flashdata('message', 'Read more added successfully!');
				 redirect('admin/packages/read_more/'.$id, 'refresh');
			}
		}
		$data['id'] = $id;	
		$data['view'] = 'read_more';
		$data['content'] = $content;
		$data['mode']=0;
		$this->load->view('admin/template',$data);	
	}
	
	function read_more_add($lang_id=NULL,$cur_id=NULL)
	{
		$content = array();	
		
		if(isset($_POST['add_read_more']))
		{			
			$read_more_data_array  = array();			
			
			$read_more_data_array['lang_id'] 	 = $content['lang_id']   		=($this->input->post('language'));
			$read_more_data_array['currency_id'] = $content['cur_id']   		 =($this->input->post('currency_id'));
			$read_more_data_array['description'] = $content['read_more_desc'] =($this->input->post('read_more_desc'));			
    		$read_more_data_array['status']  	  = $content['pacakge_status'] =($this->input->post('read_more_status'));			
			$this->form_validation->set_rules('read_more_desc', 'Package Name', 'trim|required');	
			$this->form_validation->set_rules('read_more_status', 'Package Desc', 'trim|required');
			if($this->form_validation->run())
			{	
		         $this->package_model->add_read_more($read_more_data_array);				
			 	 $this->session->set_flashdata('message', 'Read more added successfully!');
				 redirect('admin/packages/browse_read_more/', 'refresh');
			}
		}	
		$content['lang_id'] = $lang_id;
		$content['cur_id']  = $cur_id;		
		$data['view'] 	   = 'read_more_add';
		$data['content'] 	= $content;
		$data['mode']=0;
		$this->load->view('admin/template',$data);	
	}
	
	function read_more_edit($id)
	{
		$content = array();	
		
		$read_more_details = $this->package_model->get_read_more_deatils($id);
		if(!empty($read_more_details))
		{
			$content['lang_id'] = $read_more_details[0]->lang_id;
			$content['cur_id'] = $read_more_details[0]->currency_id;
			$content['description'] = $read_more_details[0]->description;
			$content['status'] = $read_more_details[0]->status;
		}
		
		
		if(isset($_POST['add_read_more']))
		{			
			$read_more_data_array  = array();			
			
		//	$read_more_data_array['lang_id'] 	 = $content['lang_id']   		=($this->input->post('language'));
		//	$read_more_data_array['currency_id'] = $content['cur_id']   		 =($this->input->post('currency_id'));
			$read_more_data_array['description'] = $content['read_more_desc'] =($this->input->post('read_more_desc'));			
    		$read_more_data_array['status']  	  = $content['pacakge_status'] =($this->input->post('read_more_status'));			
			$this->form_validation->set_rules('read_more_desc', 'Package Name', 'trim|required');	
			$this->form_validation->set_rules('read_more_status', 'Package Desc', 'trim|required');
			if($this->form_validation->run())
			{	
		         $this->package_model->update_read_more($read_more_data_array,$id);				
			 	 $this->session->set_flashdata('message', 'Read more updated successfully!');
				 redirect('admin/packages/browse_read_more/', 'refresh');
			}
		}	
		$data['id'] = $id;	
		$data['view'] 	   = 'read_more_edit';
		$data['content'] 	= $content;
		$data['mode']=1;
		$this->load->view('admin/template',$data);	
		
	}
	
	
	function browse_read_more()
	{
		$content = array();		
		$content['searchmode'] = true;		
		$data['view'] = 'browse_read_more';		
        $data['content'] = $content;        
        $this->load->view('admin/template',$data);	
	}
	
	function fetch_read_more()
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
		$this->db-> from('pack_read_more');
		
		
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
				
				if($row->lang_id == 4)
				{
					$language = 'English';
				}
				else if($row->lang_id == 3)
				{
					$language = 'Spanish';
				}
				else if($row->lang_id == 2)
				{
					$language = 'Italian';
				}
				else if($row->lang_id == 1)
				{
					$language = 'Chineese';
				}				
				else if($row->lang_id == 6)
				{
					$language = 'French';
				}
				
				$currency_code = $this->common_model->get_currency_code_from_id($row->currency_id);
				
				
			    $action = '<a href="'.base_url().'admin/packages/read_more_edit/'.$row->id.'">Edit</a>&nbsp;&nbsp;<a href="'.base_url().'admin/packages/read_more_delete/'.$row->id.'">Delete</a>';	
				
				$view = '<a href="view_readmore/'.$row->id.'" target="_blank">View Text</a>';
				//$action = 'here';			
				
			   
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array( $row->id,$language,$currency_code,$action,$view)
			);
			}
			
		}
      
             
       echo json_encode($data); 
		
		
		
	}
	
	function add_packages()
	{
		
		$content = array();
		$content['fee']=$this->course_model->get_currency();
		
		$prod_array = array('hardcopy','extension','ebooks','poe_hard','proof_completion_hard', 'transcript_hard');
		//$prod_array = array('extension');
		$count_prod_array =  count($prod_array);
		
		for($i=0;$i<$count_prod_array;$i++)
		{		
			$product_types[$prod_array[$i]] = $this->package_model->get_products($prod_array[$i]);
			$k=0;
			foreach($product_types[$prod_array[$i]] as $each_prod)
			{
				if($prod_array[$i]=='extension')
				{
					$extension_details = $this->sales_model->get_extension_details_by_units($each_prod->item_id);	
					if(!empty($extension_details))
					{
					$product_types_desc[$prod_array[$i]][$k] = $extension_details[0]->extension_option;
					}
					else
					{
						$product_types_desc[$prod_array[$i]][$k] = '';
					}
				//$product_types['desc'] = 	
				}
				elseif($prod_array[$i]=='ebooks')
				{
					
					$product_types_desc[$prod_array[$i]][$k] = $each_prod->units.' Ebook';
				}
				elseif($prod_array[$i]=='hardcopy')
				{
					$postage_options = $this->certificate_model->get_postage_details($each_prod->item_id);
					if(!empty($postage_options))
					{
						$product_types_desc[$prod_array[$i]][$k] = $postage_options[0]->postage_type;
					}
					else
					{
						$product_types_desc[$prod_array[$i]][$k] = '';
					}
				}
				elseif($prod_array[$i]=='poe_hard')
				{
					
					$product_types_desc[$prod_array[$i]][$k] = 'Proof of enrolement Hardcopy';
				}
				elseif($prod_array[$i]=='proof_completion_hard')
				{					
					$product_types_desc[$prod_array[$i]][$k] = 'Proof of Completion Hardcopy';
				}
				elseif($prod_array[$i]=='transcript_hard')
				{					
					$product_types_desc[$prod_array[$i]][$k] = 'Proof of Transcript Hardcopy';
				}
				$k++;
			}
		}
		
		/*echo "<pre>";
		print_r($product_types);
		print_r($product_types_desc);
		exit;*/
		
		if(isset($_POST['save_pacakge']))
		{
			/*echo "<pre>";
			print_r($_POST);
			*/
		
			$package_data_array  = array();
			$sel_products = array();
			$package_data_array['lang_id']    = $content['lang_id']   = ($this->input->post('language'));			
		    $package_data_array['package_name']    = $content['package_name']   = ($this->input->post('package_name')); 
			
			
			$package_data_array['package_description'] 	= $content['package_description']   =($this->input->post('package_desc'));
    		$package_data_array['status']  = $content['pacakge_status'] =($this->input->post('pacakge_status'));
			
			if($this->input->post('product_type'))
			{
				$sel_products = implode(',',($this->input->post('product_type'))); 
			}
			
			$package_data_array['products']   = $sel_products;
			$package_data_array['status']  = $content['status'] =($this->input->post('pacakge_status'));
			
			$fee['_fee']=$content['_fee']=$this->input->post('package_fee');
			
			foreach($fee['_fee'] as $key=>$val){
		  		if($val!==''){
		  			$content['package_fee'][$key]=$val;
		  			
		  		}
		  	}
			
			$fake_fee['_fee']=$content['_fee']=$this->input->post('package_fake_fee');
			
			foreach($fake_fee['_fee'] as $key=>$val){
		  		if($val!==''){
		  			$content['package_fake_fee'][$key]=$val;
		  			
		  		}
		  	}
		
		
			
			$this->form_validation->set_rules('language', 'Language', 'trim|required');	
			$this->form_validation->set_rules('package_name', 'Package Name', 'trim|required');	
			$this->form_validation->set_rules('package_desc', 'Package Desc', 'trim|required');
			
			if(empty($sel_products))
			{
				$this->form_validation->set_rules('product_type', 'Select One product', 'trim|required');
			}	
						
			$this->form_validation->set_rules('package_fee[1]', 'Default Currency', 'required');
			
			
			if($this->form_validation->run())
			{	
			
		         $this->package_model->add_package($package_data_array);				 
				 $package_id =$this->db->insert_id();				 
				 $product_data['type']    = 'package'; 
				 $product_data['item_id'] = $package_id;
				 $product_data['units']   = 1; // 1 for one course
	
				 $this->common_model->add_item_to_product($product_data);
				 $product_id =$this->db->insert_id();			
				 
				 foreach ($content['package_fee'] as $key => $value) {						
					$package_fee['product_id']  = $product_id;
					$package_fee['currency_id'] = $key;
					if($content['package_fake_fee'][$key] !=0)
					{
						$package_fee['fake_amount'] = $content['package_fake_fee'][$key];
					}
					else
					{
						$package_fee['fake_amount'] = 0;
					}
					$package_fee['amount']      = $value;	
					/*echo "<pre>";
					print_r($package_fee);*/
					
									
					$this->course_model->add_course_fees($package_fee);					
						
					}
			 	 $this->session->set_flashdata('message', 'Package added successfully!');
				 redirect('admin/packages/browse_packages', 'refresh');
			}
		}
		
		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		
		$content['prod_array'] = $prod_array;
		$content['product_types'] = $product_types;
		$content['product_types_desc'] = $product_types_desc;
		
		
		
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		$data['view'] = 'packages_add';
		
		$content['language']=$this->common_model->get_languages();
		$data['content'] = $content;
		$data['mode']=0;
		$this->load->view('admin/template',$data);	
	
	}
	
	
	function package_edit($id)
	{		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		$this->load->model('course_model');
		
		$content = array();
		$content['fee']=$this->course_model->get_currency();
		
		$prod_array = array('hardcopy','extension','ebooks','poe_hard','proof_completion_hard', 'transcript_hard');
		//$prod_array = array('extension');
		$count_prod_array =  count($prod_array);
		
		for($i=0;$i<$count_prod_array;$i++)
		{		
			$product_types[$prod_array[$i]] = $this->package_model->get_products($prod_array[$i]);
			$k=0;
			foreach($product_types[$prod_array[$i]] as $each_prod)
			{
				if($prod_array[$i]=='extension')
				{
					$extension_details = $this->sales_model->get_extension_details_by_units($each_prod->item_id);	
					if(!empty($extension_details))
					{
					$product_types_desc[$prod_array[$i]][$k] = $extension_details[0]->extension_option;
					}
					else
					{
						$product_types_desc[$prod_array[$i]][$k] = '';
					}
				//$product_types['desc'] = 	
				}
				elseif($prod_array[$i]=='ebooks')
				{
					
					$product_types_desc[$prod_array[$i]][$k] = $each_prod->units.' Ebook';
				}
				elseif($prod_array[$i]=='hardcopy')
				{
					$postage_options = $this->certificate_model->get_postage_details($each_prod->item_id);
					if(!empty($postage_options))
					{
						$product_types_desc[$prod_array[$i]][$k] = $postage_options[0]->postage_type;
					}
					else
					{
						$product_types_desc[$prod_array[$i]][$k] = '';
					}
				}
				elseif($prod_array[$i]=='poe_hard')
				{
					
					$product_types_desc[$prod_array[$i]][$k] = 'Proof of enrolement Hardcopy';
				}
				elseif($prod_array[$i]=='proof_completion_hard')
				{					
					$product_types_desc[$prod_array[$i]][$k] = 'Proof of Completion Hardcopy';
				}
				elseif($prod_array[$i]=='transcript_hard')
				{					
					$product_types_desc[$prod_array[$i]][$k] = 'Proof of Transcript Hardcopy';
				}
				$k++;
			}
		}
		
		
		
		
		
		$pagedata = $this->package_model->fetch_package($id);
	
		
		foreach($pagedata as $row){
			
			 $content['id'] 			   		 = $row->id;
			 $content['package_name'] 	 	   = $row->package_name;
			  $content['lang_id'] 	 	   	   = $row->lang_id;
			 $content['products'] 	  		   = explode(",",$row->products);
     		 $content['package_description']    = $row->package_description;
			 $content['status'] 		  		 = $row->status;		
			 
			 	
			 
		}
	    
		
		$content['fee']=$this->course_model->get_currency();
		
		$product_id = $this->common_model->getProdectId('package',$id,1);
		$content['package_fee']=$this->package_model->get_product_fees($product_id);	
		
		
		/*echo "<pre>";
		print_r($content);
		exit;*/
		
		if(isset($_POST['save_pacakge']))
		{
			/*echo "<pre>";
			print_r($_POST);
			*/
		
			$package_data_array  = array();
			$sel_products = array();
		    $package_data_array['package_name']    = $content['package_name']   = ($this->input->post('package_name')); 
			
			
			$package_data_array['package_description'] 	= $content['package_description']   =($this->input->post('package_desc'));
    		$package_data_array['status']  = $content['pacakge_status'] =($this->input->post('pacakge_status'));
			
			if($this->input->post('product_type'))
			{
				$sel_products = implode(',',($this->input->post('product_type'))); 
			}
			
			$package_data_array['products']   = $sel_products;
			$package_data_array['status']  = $content['status'] =($this->input->post('pacakge_status'));
			
			$fee['_fee']=$content['_fee']=$this->input->post('package_fee');
			
			/*echo "<pre>";
			print_r($fee);*/
			
			foreach($fee['_fee'] as $key=>$val){
		  		if($val!==''){
		  			$content['package_fee_3'][$key]=$val;
		  			
		  		}
		  	}
			
			$fake_fee['_fee']=$content['_fee']=$this->input->post('package_fake_fee');
			
			foreach($fake_fee['_fee'] as $key=>$val){
		  		if($val!==''){
		  			$content['package_fake_fee'][$key]=$val;
		  			
		  		}
		  	}
			
			$this->form_validation->set_rules('package_name', 'Package Name', 'trim|required');	
			$this->form_validation->set_rules('package_desc', 'Package Desc', 'trim|required');
			
			if(empty($sel_products))
			{
				$this->form_validation->set_rules('product_type', 'Select One product', 'trim|required');
			}	
						
			$this->form_validation->set_rules('package_fee[1]', 'Default Currency', 'required');
			
			if($this->form_validation->run())
			{
			
		         $this->package_model->update_package($package_data_array,$id);			 
				
				 
				 $this->course_model->delete_course_fee($product_id);
				 
				/* echo "<pre>";
				 print_r($content['package_fee_3']);*/
				 
				  foreach ($content['package_fee_3'] as $key => $value) {						
					$package_fee['product_id']  = $product_id;
					$package_fee['currency_id'] = $key;
					if($content['package_fake_fee'][$key] !=0)
					{
						$package_fee['fake_amount'] = $content['package_fake_fee'][$key];
					}
					else
					{
						$package_fee['fake_amount'] = 0;
					}
					$package_fee['amount']      = $value;	
					
				/*	 echo "<pre>";
				 print_r($package_fee);	*/			
					$this->course_model->add_course_fees($package_fee);					
						
					}
			 	 $this->session->set_flashdata('message', 'Package added successfully!');
				 redirect('admin/packages/browse_packages', 'refresh');
			}
		}
		
		if(isset($this->flashmessage)){
		$data['flashmessage'] = $this->flashmessage;
		}
		
		$content['prod_array'] = $prod_array;
		$content['product_types'] = $product_types;
		$content['product_types_desc'] = $product_types_desc;
		$content['language']=$this->common_model->get_languages();
		
		$data['view'] = 'packages_edit';
		
		
		$data['content'] = $content;
		$data['mode']=0;
		$data['content'] = $content;
		$data['mode']=1;
		$this->load->view('admin/template',$data);	
		
	}
		
	function browse_packages()
	{
		$content = array();		
		$content['searchmode'] = true;		
		$data['view'] = 'browse_packages';		
        $data['content'] = $content;        
        $this->load->view('admin/template',$data);	
	}
	
	function fetch_packages()
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
		$this->db-> from('packages');
		
		
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
				
				
				
			    $action = '<a href="'.base_url().'admin/packages/package_edit/'.$row->id.'">Edit</a>&nbsp;&nbsp;<a href="'.base_url().'admin/packages/package_delete/'.$row->id.'">Delete</a>';				
				
			   
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array( $row->id,$row->package_name,$row->status,$action)
			);
			}
			
		}
      
             
       echo json_encode($data); 
		
		
		
	}
	
	function view_readmore($id)
	{
		$content = array();
		
		$read_more_details = $this->package_model->get_read_more_deatils($id);		
		
		$data['description'] = $read_more_details[0]->description;
		$data['content'] = $content;        
        $this->load->view('admin/view_read_more',$data);
	}
	
	
	function package_subscriptions()
	{
		$content = array();		
		$content['searchmode'] = true;		
		$data['view'] = 'package_sbscriptions';		
        $data['content'] = $content;        
        $this->load->view('admin/template',$data);	
		
	}
	
	function fetch_package_subscriptions()
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
		$this->db-> from('package_subscriptions');
		
		
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
			$sl =0;
			foreach($result as $row){
				
				$sl++;
				$user_arr = $this->user_model->get_stud_details($row->user_id);				
				//echo "Course id ".$row->course_id;
				$course_name = $this->common_model->get_course_name($row->course_id);
				//echo "Course Name ".$course_name;
			//	exit;					
				$package_name = $this->package_model->get_package_name($row->package_id);				
				$payment_details = $this->sales_model->get_source_amount_from_payment_id($row->payment_id);			
				$currency_name = $this->common_model->get_currency_code_from_id($payment_details['currency_id']);						
			 
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($sl,$row->date,$payment_details['amount'],$currency_name,$package_name,$user_arr[0]->email,$course_name)
			);
			}
			
		}      
             
       echo json_encode($data); 
		
	}
	
	
	function generate_excel_report()
	{
		
		$this->load->helper(array('php-excel'));	
		
        
        $this->db-> select('*');		
		$this->db-> from('package_subscriptions');
		$this->db->order_by('id','desc');
		//$this->db->limit($rp,$pageStart);
        $query = $this->db->get();
        
		
		$result = $query->result();
        
        $data = array();
		   
        
        if($query -> num_rows() >0 )
		{
			foreach($result as $row)
			{
				
				$user_arr = $this->user_model->get_stud_details($row->user_id);					 
				$course_name = $this->common_model->get_course_name($row->course_id);						
				$package_name = $this->package_model->get_package_name($row->package_id);				
				$payment_details = $this->sales_model->get_source_amount_from_payment_id($row->payment_id);			
				$currency_name = $this->common_model->get_currency_code_from_id($payment_details['currency_id']);	
	
			    $data_array[]= array($row->date,$payment_details['amount'],$currency_name,$package_name,$user_arr[0]->email,$course_name);
				
			}
		$field_array[] = array("Date of Purchase","Amount Paid","Currency Paid","Name of Package","Student Email Address","Course Name");	
		 $xls = new Excel_XML;
		   $xls->addArray ($field_array);
		   $xls->addArray ($data_array);
		   $xls->generateXML ( "Package subscription report"); 	
		}
       
	}
	
	
}