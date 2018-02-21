<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class course extends CI_Controller {

	 
	function __construct()
	{
		parent::__construct();
		$this->load->model('course_model','',TRUE);
		$this->load->model('common_model','',TRUE);
	
		if(!$this->session->userdata('admin_logged_in'))
   		redirect('admin/login', 'refresh');
   		
   		if($message = $this->session->flashdata('message')){
          $this->flashmessage =$message;
   		}

   		 $this->load->helper(array('form'));
		$this->load->library('form_validation');
   		
	   	$this->load->library('Datatables');
        $this->load->library('table');
        $this->load->database();

    }
	function index()
	{

	}
	function courselist()
	{
		 //set table id in table open tag
		 $content = array();
        if(isset($this->flashmessage))
        $data['flashmessage'] = $this->flashmessage;
        $data['view'] = 'courselist';
        $data['content'] = $content;

        $this->load->view('admin/template',$data);
	}

	function fetchdata(){


	 //$lang = $_GET['lang'];
		
		/*$this->datatables->select('id,page_title,page_status')
		->add_column('action','<a href="'.base_url().'admin/cms/edit/$1">Edit</a> | <a href="'.base_url().'admin/cms/delete/$1">Delete</a>','id')
		->edit_column('page_status', '<a href="$1" >Disabled</a>' ,'page_status')
		->where('language',$lang)
        ->unset_column('id')
        ->from('cms');  
        echo $this->datatables->generate();
        */
        
        
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
       
        
        $this->db-> select('*');
		$this->db-> from('courses');
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
			




			foreach($result as $row)
			{
				
				if($row->language_id==1){
				    $languag = 'Chineese';
			    }
			    elseif ($row->language_id==2) {
			    	$languag = 'Italian';
			    }
				 elseif ($row->language_id==6) {
			    	$languag = 'French';
			    }
			    elseif ($row->language_id==3) {
			    	$languag = 'Spanish';
			    }
			    elseif ($row->language_id==4) {
			    	$languag = 'English';
			    }

				if($row->course_status==0){
				    $status = 'Disabled';
			    }
			    else{
				    $status = 'Enabled';
			    }
			    $action = '<a href="'.base_url().'admin/course/edit/'.$row->course_id.'">Edit</a>';
			    $action .=' | <a href="'.base_url().'admin/course/delete/'.$row->course_id.'">Delete</a>';
			   // $extension ='<a href="'.base_url().'admin/course/extension/'.$row->course_id.'">extension</a>';
				 $data['rows'][] = array(
				'id' => $row->course_id,
				'cell' => array($row->course_id,$row->course_name,$languag,$status,$action)
			);
			}
			
		}
        
        
       
       // $pagelist = $this->getpagelist($lang);
        
        
        
       
        
       echo json_encode($data); exit(); 
        
       
           
	}
	
	function add($lang=4)
	{	
	  
		$content = array();
		$content['lang'] = $lang;
		$content['fee']=$this->course_model->get_currency();
		$content['units']='';
		

		// field name, error message, validation rules
		if(isset($_POST['course_name']))
		{
		
			$cousedata  = array();
			
		    $cousedata['language_id'] = $content['language'] = $this->input->post('language');
		   // $cousedata['course_basename'] = $content['course_basename'] = $this->input->post('base_name');
		    /*if($this->input->post('parent_course')==''){
		    	$cousedata['parent_id']=$content['parent_id']=0;
		    }
		    
		    else
		    {
		    	$cousedata['parent_id'] = $content['parent_id'] = implode(',',$this->input->post('parent_course'));
		    }*/
			
		  
		    $cousedata['course_name'] = $content['course_name'] = $this->input->post('course_name');
		    $cousedata['course_summary'] = $content['course_summary'] = $this->input->post('course_summary');
		    $cousedata['course_description'] = $content['caption_desc'] = $this->input->post('course_desc');
		//  $cousedata['long_description'] = $content['long_desc'] = $this->input->post('long_desc');
			$cousedata['page_title'] = $content['page_title'] = $this->input->post('page_title');
			$cousedata['meta_desc'] = $content['meta_desc'] = $this->input->post('meta_desc');
			$cousedata['meta_key'] = $content['meta_key'] = $this->input->post('meta_key');
		//	$cousedata['country_id'] = $content['country_id'] = $this->input->post('country');
		//	$couse_certificate_data['course_certificate_id'] = $content['course_certificate_id'] = $this->input->post('certificate');
			//$cousedata['course_hours'] = $content['course_hour'] = $this->input->post('time_period');
			$cousedata['course_validity'] = $content['validity'] = $this->input->post('validity');
			$cousedata['course_status'] = $content['course_status'] = $this->input->post('course_status');
			
		    	
		    $content['unit_order'] = $this->input->post('order');
		    $j=0;
		    foreach($content['unit_order'] as $key=>$val){
		    	if($val!==''){
		    		$content['units'][$j]=$key;
		    		$content['order'][$j]=$val;
		    		$j=$j+1;
		    	}
		    }
		    	
		    
		  
		  	$fee['_fee']=$content['_fee']=$this->input->post('course_fee');

		
		  	foreach($fee['_fee'] as $key=>$val){
		  		if($val!==''){
		  			$content['course_fee'][$key]=$val;
		  			
		  		}
		  	}
		  		
		  	
		  	
			//$content['courseicon']=$this->input->post('')
			//if($_POST['parent_course']==''){
			///	$content['parent_id']=0;
			//}
			

			$this->form_validation->set_rules('course_name', 'course name', 'trim|required');
			$this->form_validation->set_rules('course_summary', 'Course summary', 'required');
			$this->form_validation->set_rules('course_desc', 'Course description', 'required');
		//	$this->form_validation->set_rules('country', 'Country', 'callback_validate[Country]');
		//	$this->form_validation->set_rules('certificate', 'Certificate', 'callback_validate[Certificate]');
			$this->form_validation->set_rules('time_period', 'Time Period', 'callback_validate[Time Period]');
			$this->form_validation->set_rules('validity', 'Validity period', 'callback_validate[Validity period]');
			$this->form_validation->set_rules('course_status', 'Course Status', 'required');
			$this->form_validation->set_rules('units', 'Units', 'callback_validate_units[Units]');
			$this->form_validation->set_rules('order', 'order', 'required');
			$this->form_validation->set_rules('course_fee[1]', 'Default Currency', 'required');
			
			
			if($this->form_validation->run())
			{
				
				$product_data = array();
				
			 	$this->course_model->add_course($cousedata);
			 	$course_id=$this->db->insert_id();
				
				$product_data['type']    = 'course'; 
				$product_data['item_id'] = $course_id;
				$product_data['units']   = 1; // 1 for one course
				
				$this->common_model->add_item_to_product($product_data);
				
				$product_id =$this->db->insert_id();
				
								
				$y=count($content['units']);
			 	for ($i=0; $i<$y; $i++){ 
			 		$insert['course_id']=$course_id;
			 		$insert['course_units']=$content['units'][$i];
			 		$insert['units_order']=$content['order'][$i];
			 		$this->course_model->add_unit_order($insert);
			 		# code...
			 	}

				
				
				foreach ($content['course_fee'] as $key => $value) {
	
					$coursefee['product_id']  = $product_id;
					$coursefee['currency_id'] = $key;
					$coursefee['fake_amount'] = 0;
					$coursefee['amount']      = $value;
					
					
					
					$this->course_model->add_course_fees($coursefee);
				}	
 				
	 
			 	redirect('admin/course/courselist/', 'refresh');
			}
		}	
		$data['view'] = 'add_course';
		
		$content['country']=$this->course_model->get_country();
		$content['certificate']=$this->course_model->get_certificate();
		//$content['time']=$this->course_model->get_hours();
		$content['basename']=$this->course_model->get_basename();
		
		$unitsArr = array();
		$unittypes = $this->course_model->fetchUnittypes($lang);
		
		if(isset($unittypes) && count($unittypes)>0)
		foreach($unittypes as $unittype){
			$unitsArr[$unittype->id]['unittype'] = $unittype->course_group_name;
			$unitsArr[$unittype->id]['units'] = array();
			$units = $this->course_model->fetchUnits($unittype->id,$lang);


			if(isset($units) && count($units)>0){
				foreach($units as $unit){
					$unitsArr[$unittype->id]['units'][$unit->id] = $unit->unit_name;

				}
		   }
		
	
		}

		$content['unitsArr'] = $unitsArr;
		



		$data['content'] = $content;
		
		$this->load->view('admin/template',$data);
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


	function validate_units($val,$name)
	{
		if($val==0)
		{
			 $this->form_validation->set_message('validate_units', 'Please Select the '.$name);
                return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	function edit($id){
		$content = array();
		//$id = $_GET['id'];
		$pagedata = $this->course_model->fetchcourse($id);
		$unitoption=$this->course_model->fetch_units_order($id);
		$len=$content['len']=count($unitoption);
		for ($i=0; $i<$len; $i++) { 
			
			//$content['units'][$i]=$unitoption[$i]->course_units;
			//$content['order'][$i]=$unitoption[$i]->order_units;
			$content['unit_order'][$unitoption[$i]->course_units]=$unitoption[$i]->units_order;

		}

		//echo "<pre>";print_r($pagedata);exit;
		foreach($pagedata as $row){
			
			 $lang=$content['lang'] =$row->language_id;
			// $content['base_name_set']  = $row->course_basename;
			 //$content['parent_course']  = $row->meta_desc;
			 $content['course_name']  = $row->course_name;
			 $content['course_summary'] = $row->course_summary;
			 $content['course_desc'] = $row->course_description;
			/* $content['long_desc'] = $row->long_description;*/
			  $content['page_title']=$row->page_title;
			  $content['meta_desc']=$row->meta_desc;
			  $content['meta_key']=$row->meta_key;

			// $content['country_set'] = $row->country_id;
			 
			 
			 $course_certificate_id = $this->course_model->get_certificate_id($id);
			 
			 $content['certificate_set'] = $course_certificate_id;
			 
			 
			 $content['time_period'] = $row->course_hours;
			 $content['validity'] = $row->course_validity;
			 $content['course_status'] = $row->course_status;
			 $content['parent_set']=$row->parent_id;

		}
		if(count($content['parent_set'])>=1)
			$content['parent_id'] = explode(',',$content['parent_set']);
				else
					$content['parent_id'] ='';

		$product_id = $this->common_model->getProdectId('course',$id,1);
	   if(isset($_POST['course_name']))
		{
		
			$cousedata  = array();
			
		    $cousedata['language_id'] = $content['lang'] = $this->input->post('language');
		   // $cousedata['course_basename'] = $content['course_basename'] = $this->input->post('base_name');
		    if($this->input->post('parent_course')==''){
		    	$cousedata['parent_id']=$content['parent_id']=0;
		    }
		    
		    else
		    {
		    	$cousedata['parent_id'] = $content['parent_id'] = implode(',',$this->input->post('parent_course'));
		    }
			
		  	$cousedata['course_name'] = $content['course_name'] = $this->input->post('course_name');
		    $cousedata['course_summary'] = $content['course_summary'] = $this->input->post('course_summary');
		    $cousedata['course_description'] = $content['caption_desc'] = $this->input->post('course_desc');
		//  $cousedata['long_description'] = $content['long_desc'] = $this->input->post('long_desc');
			$cousedata['page_title'] = $content['page_title'] = $this->input->post('page_title');
			$cousedata['meta_desc'] = $content['meta_desc'] = $this->input->post('meta_desc');
			$cousedata['meta_key'] = $content['meta_key'] = $this->input->post('meta_key');
		//	$cousedata['country_id'] = $content['country_id'] = $this->input->post('country');
		//	$cousedata['country_id'] = $content['country_id'] = $this->input->post('country');
		//	$couse_certificate_data['course_certificate_id'] = $content['course_certificate_id'] = $this->input->post('certificate');
			$cousedata['course_hours'] = $content['course_hour'] = $this->input->post('time_period');
			$cousedata['course_validity'] = $content['validity'] = $this->input->post('validity');
			$cousedata['course_status'] = $content['course_status'] = $this->input->post('course_status'); 
		  	if($cousedata['course_summary']=='')
			{
				$cousedata['course_summary'] = '';
			}
		  
		 /* echo "<pre>";
		  print_r($_POST);
		  exit;*/
		  
		    
			$content['unit_order'] = $this->input->post('order');

		    
		    $j=0;
		    foreach($content['unit_order'] as $key=>$val){
		    	if($val!==''){
		    		$content['units'][$j]=$key;
		    		$content['order'][$j]=$val;
		    		$j=$j+1;
		    	}
		    }


		    	
		    
		  
		  	$fee['_fee']=$content['_fee']=$this->input->post('course_fee');

		
		  	foreach($fee['_fee'] as $key=>$val){
		  		if($val!==''){
		  			$content['course_fee'][$key]=$val;
		  			
		  		}
		  	}
		  		
		

			//$content['courseicon']=$this->input->post('')
			//if($_POST['parent_course']==''){
			///	$content['parent_id']=0;
			//}


			$this->form_validation->set_rules('course_name', 'course name', 'trim|required');
			$this->form_validation->set_rules('course_desc', 'Short description', 'required');
			$this->form_validation->set_rules('time_period', 'Time Period', 'callback_validate[Time Period]');
			$this->form_validation->set_rules('validity', 'Validity period', 'callback_validate[Validity period]');
			$this->form_validation->set_rules('course_status', 'Course Status', 'required');
			$this->form_validation->set_rules('units', 'Units', 'callback_validate_units[Units]');
  
			if($this->form_validation->run())
			 {
			 	 $this->course_model->courseupdate($cousedata,$id);

			 	 $this->course_model->delete_unit_order($id);
			 	 $y=count($content['units']);
			 	 for ($i=0; $i<$y; $i++){ 

			 		$insert['course_id']=$id;
			 		$insert['course_units']=$content['units'][$i];
			 		$insert['units_order']=$content['order'][$i];
			 	
		
			 		$this->course_model->add_unit_order($insert);
			 		
			 	}
			 	
			 	$this->course_model->delete_course_fee($product_id);
			 	foreach ($content['course_fee'] as $key => $value) {
	
					$coursefee['product_id']=$product_id;
					$coursefee['amount']=$value;
					$coursefee['currency_id']=$key;
					$this->course_model->add_course_fees($coursefee);
				}	

			 	  $this->session->set_flashdata('message', 'Course Updated');
			 	 redirect('admin/course/courselist/', 'refresh');
			 }
			 
			
		 
		
		}
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		$content['country']=$this->course_model->get_country();
		$content['certificate']=$this->course_model->get_certificate();
		//$content['time']=$this->course_model->get_hours();
		$content['basename']=$this->course_model->get_basename();
		$unitsArr = array();
		$unittypes = $this->course_model->fetchUnittypes($lang);
		//echo "<pre>";print_r($unittypes);
		
		if(isset($unittypes) && count($unittypes)>0)
		foreach($unittypes as $unittype){
			$unitsArr[$unittype->id]['unittype'] = $unittype->course_group_name;
			$unitsArr[$unittype->id]['units'] = array();
			$units = $this->course_model->fetchUnits($unittype->id,$lang);


			if(isset($units) && count($units)>0){
				foreach($units as $unit){
					$unitsArr[$unittype->id]['units'][$unit->id] = $unit->unit_name;

				}
		   }
		}
		
		
		$content['coursefee']=$this->course_model->fetchcoursefees($product_id);
		$content['fee']=$this->course_model->get_currency();
		$content['course_id']=$id;

		


		$content['unitsArr'] = $unitsArr;
		
		$data['view'] = 'edit_course';
		$data['content'] = $content;

		$this->load->view('admin/template',$data);
		
	}	

	function delete($id){
	  
	  	$this->course_model->coursedelete($id);
	   $this->course_model->delete_unit_order($id);

	   $this->session->set_flashdata('message', 'Page Deleted');
	   redirect('admin/course/courselist', 'refresh');
	}

/*	function extension(){
		$content = array();
		//$content['id']=$id;

		if(isset($_POST['days'])){
			$extdata  = array();
			//$extdata['course_id'] = $content['course_id']=$id;
			$extdata['extension'] = $content['extension'] = $this->input->post('days');
			$this->form_validation->set_rules('days', 'days', 'required');
			if($this->form_validation->run())
			 {
			 	 $this->course_model->extension($extdata);
			 	  $this->session->set_flashdata('message', 'Extension Updated');
			 	 redirect('admin/course/courselist/', 'refresh');
			 }

		}
		$extension_options = $this->course_model->get_extension_options();
		
		$content['currency']=$this->course_model->get_currency();
		
		$content['extension_options']=$extension_options;
		$data['view']='extension';
		$data['content'] = $content;
		$this->load->view('admin/template',$data);
	}*/	
	
	
	function managecontent()
	{
		 
		 $content = array();
        if(isset($this->flashmessage))
        $data['flashmessage'] = $this->flashmessage;
        $data['view'] = 'grouplist';
        $data['content'] = $content;

        $this->load->view('admin/template',$data);
	}

	function fetchgroupdata(){


        
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
		$this->db-> from('course_group');
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
			foreach($result as $row)
			{
			    $rename = '<a href="'.base_url().'admin/course/editgroup/'.$row->id.'">Rename</a>';
				$details = '<a href="'.base_url().'admin/course/CourseUnits/'.$row->id.'">Details</a>';
			    $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($row->id,$row->course_group_name,$rename,$details)
			);
			}
			
		}
        
        
       
       // $pagelist = $this->getpagelist($lang);
        
        
        
       
        
       echo json_encode($data); exit(); 
        
       
           
	}
	
	function CourseUnits($id)
	{
		 //set table id in table open tag
		 $content = array();
		  $content['id']=$id;
		  $content['langId']= $this->uri->segment(5,4);
		 	
		$content['groupName'] = $this->course_model->getGroupName($id);	  
        if(isset($this->flashmessage))
        $data['flashmessage'] = $this->flashmessage;
        $data['view'] = 'course_units';
        $data['content'] = $content;
		

        $this->load->view('admin/template',$data);
	}

	function fetchunitsdata($id){


        
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
		$this->db-> from('unit_courses');
		$this->db-> where('course_group_id',$id);
		
		//echo $this->input->get('langId');exit;
		
		if(isset($_GET['langId']))
		$this->db-> where('lang_id',$this->input->get('langId'));
		else
		$this->db-> where('lang_id',4);
		
		
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
			foreach($result as $row)
			{
				$study = '<a href="'.base_url().'admin/course/studyPages/'.$row->id.'">Study</a>';
				$exersice = '<a href="'.base_url().'admin/course/exersicePages/'.$row->id.'">Exercise</a>';
			   	$exam = '<a href="'.base_url().'admin/course/examPages/'.$row->id.'">Exam</a>';
			   
			    $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($row->id,$row->unit_name,$study,$exersice,$exam)
			);
			}
			
		}
        
           
       echo json_encode($data);      
      
	}
	
	function editgroup($id){
		$content = array();
		//$id = $_GET['id'];
		 $content['course_name'] = $this->course_model->getGroupName($id);
		 $content['id'] =$id;	
				
	   if(isset($_POST['course_name']))
		{
		
			$cousedata  = array();
			
		  	$cousedata['course_group_name'] = $content['course_name'] = $this->input->post('course_name');
			$this->form_validation->set_rules('course_group_name', 'course name', 'trim|required');
			if($this->form_validation->run())
			 {
			 	 $this->course_model->groupupdate($cousedata,$id);
                 $this->session->set_flashdata('message', 'Course Group Name Changed');
			 	 redirect('admin/course/grouplist/', 'refresh');
			 }
			 
			}
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		$data['view'] = 'renamecourse';
		$data['content'] = $content;

		$this->load->view('admin/template',$data);
		
	}	
	
	function studyPages($unitId)
	{
		$this->load->library('pagination');
		$this->load->model('content_model');
		
		
		$content['unitId']=$unitId;		
		$groupDetails = $this->course_model->get_group_byUnit($unitId);	
		//echo "<br>------------group details-----------<pre>";print_r($groupDetails);echo "</pre>";
		$content['groupName'] = $groupDetails->course_group_name;
		$content['group_id'] = $groupDetails->id;
		$content['unitName'] = $this->course_model->get_unitName($unitId);
		
		
		$coursePagesArr = $this->content_model->getCoursePagesStudy($unitId);
		
		//echo"<pre>";print_r($coursePagesArr);echo "</pre>";exit;
		$i=0;
		foreach($coursePagesArr as $row)
		{
			
			$content['coursePageId'][$row->page_no] = $row->page_id;
			 $content['page_no'][$i] = $row->page_no;
			
			
			$i++;
		}
		$page = $this->uri->segment(5);
		if($page==''){$page=1;}
		//echo $page;exit;
		$content['curPage']=$page;
		
		
		
		
		
		if(isset($_POST['pageContent']))
		{
			
			
		    $content['pageContent'] = $this->input->post('pageContent');
			
		    
		
			$this->form_validation->set_rules('pageContent', 'Content', 'trim|required');
			
			if($this->form_validation->run())
			{
				$buttonAction = $this->input->post('save');
				//echo $buttonAction;
				$contentArr['content'] =$this->input->post('pageContent');
				//echo "<pre>";print_r($_POST);exit;
				if($buttonAction!="Delete")
				{
					if($this->content_model->is_Page_id_InContent($content['coursePageId'][$page])==TRUE)
					{
						$this->content_model->updateContent($contentArr,$content['coursePageId'][$page]);
					}
					else
					{
						$contentArr['course_page_id']=$content['coursePageId'][$page];
						$this->content_model->addContent($contentArr);
					}
					if($buttonAction!='save')
					{
						$langId=$this->content_model->getLangByUnit($unitId);
						//var_dump((int)$page);
						
						//echo (int)$page+1;exit;
						
						$this->content_model->addCoursePage((int)$page+1,$unitId,'content','0','0',$langId);
						
					}
					
				}
				else
				{
					$langId=$this->content_model->getLangByUnit($unitId);
					$this->content_model->deleteContentPage($content['coursePageId'][$page],$page,$unitId,'content','0','0',$langId);
					$content['curPage'] -=1;
					
					
				}

				//$this->session->set_flashdata('message', 'CMS page added successfully.');
			 	
			}
			
			$coursePagesArr = $this->content_model->getCoursePagesStudy($unitId);
		
		//echo $coursePagesArr;exit;
		
		$i=0;
		foreach($coursePagesArr as $row)
		{
			$content['coursePageId'][$row->page_no] = $row->page_id;
			 $content['page_no'][$i] = $row->page_no;
			
			
			$i++;
		}
		$page = $this->uri->segment(5);
		if($page==''){$page=1;}
		//echo $page;exit;
		$content['curPage']=$page;
		
			
			
		}
		
				
		$content['pageContent'] = $this->content_model->getContent($content['coursePageId'][$page]);
		//echo"<pre>";print_r($content);echo "</pre>";exit;
		
		$config['base_url'] = base_url().'admin/course/studyPages/'.$unitId;
		$config['total_rows'] = $i;
		$config['per_page'] = 1; 
		$config['use_page_numbers'] = TRUE;
		$config["uri_segment"] = 5;
		

$config['num_tag_open'] = '<p>';
$config['num_tag_close'] = '</p>';
$config['full_tag_open'] = '<div class=numberings>';	
$config['full_tag_close'] = '</div>';		
$config['first_tag_open'] = "<p class='numbers'>";
$config['first_tag_close'] = '</p>';
$config['last_tag_open'] = "<p class='numbers'>";
$config['last_tag_close'] = '</p>';
$config['next_tag_open'] = "<p class='numbers'>";
$config['next_tag_close'] = '</p>';
$config['prev_tag_open'] = "<p class='numbers'>";
$config['prev_tag_close'] = '</p>';
$config['cur_tag_open'] = "<p class='pagin'>";
$config['cur_tag_close'] = '</p>';
$config['num_tag_open'] = "<p class='numbers'>";
$config['num_tag_close'] = '</p>';
		
		
		$this->pagination->initialize($config); 
		
		$content['links'] =$this->pagination->create_links();
		
		
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		$data['view'] = 'stydyPage';
		$data['content'] = $content;
		
		$this->load->view('admin/template',$data);
	}
	
	function exersicePages($unitId)
	{
		$this->load->library('pagination');
		$this->load->model('content_model');
		
		
		$content['unitId']=$unitId;		
		$groupDetails = $this->course_model->get_group_byUnit($unitId);	
		$content['groupName'] = $groupDetails->course_group_name;
		$content['group_id'] = $groupDetails->id;
		$content['unitName'] = $this->course_model->get_unitName($unitId);
		
		$coursePagesArr = $this->content_model->getCoursePagesExersice($unitId);
		
		//echo"<pre>";print_r($coursePagesArr);exit;
		if(!empty($coursePagesArr))
		{
		  $i=0;
		  foreach($coursePagesArr as $row)
		  {
			  $content['coursePageId'][$row->page_no] = $row->page_id;
			  $content['type'][$row->page_no] = $row->page_type;
			   $content['page_no'][$i] = $row->page_no;
			  
			  
			  $i++;
		  }
		}
		$page = $this->uri->segment(5);
		if($page==''){$page=1;}
		//echo $page;exit;
		$content['curPage']=$page;
		//----------------------------------------------------------------------------------------------
		if(isset($_POST['save']))
		{
			
			if($this->input->post('typeSel')=='content')
			{
				$content['pageContent'] = $this->input->post('pageContent');
				$this->form_validation->set_rules('pageContent', 'Content', 'trim|required');
			}
			else if($this->input->post('typeSel')=='task')
			{
				$content['pageTaskId'] =$this->input->post('testAssign');
				$this->form_validation->set_rules('testAssign', 'Select one task ', 'trim|required');
				
			}
			else
			{
				$this->form_validation->set_rules('typeSel', 'Select type ', 'required');
				
			}
			
			//echo $this->input->post('typeSel');
			
  if($this->form_validation->run())
  {
	  $buttonAction = $this->input->post('save');
	  //echo $buttonAction;
	  if($this->input->post('typeSel')=='content')
	  {
	  
	  $contentArr['content'] =$this->input->post('pageContent');
	  }
	  else
	  {
		  
	  $contentArr['task_id'] =$this->input->post('testAssign');
	  }
	  
	  
	  if($this->input->post('typeSel')=='content')
	  {
		  // echo "entered in content saving part";exit;
	 // echo "<pre>";print_r($contentArr);exit;
	  if($buttonAction!="Delete")
	  {
		  
		  
		  if($this->content_model->is_Page_id_InContent($content['coursePageId'][$page])==TRUE)
		  {
			  $this->content_model->updateContent($contentArr,$content['coursePageId'][$page]);
		  }
		  else
		  {
			  $contentArr['course_page_id']=$content['coursePageId'][$page];
			  $contentArr['content']=$content['pageContent'];
			  $this->content_model->addContent($contentArr);
		  }
		  if($buttonAction!='Save')
		  {
			  $langId=$this->content_model->getLangByUnit($unitId);
			  //var_dump((int)$page);
			  
			  //echo (int)$page+1;exit;
			  
			  $this->content_model->addCoursePage((int)$page+1,$unitId,'content','1','0',$langId);
			  
		  }
		  
	  }
	  else
	  {
		  $langId=$this->content_model->getLangByUnit($unitId);
		  $this->content_model->deleteContentPage($content['coursePageId'][$page],$page,$unitId,'content','1','0',$langId);
		  $content['curPage'] -=1;
		  
		  
	  }
	  }
	  else
	  {
		  //echo "entered in task saving part";exit;
		    //echo "<pre>";print_r($contentArr);exit;
		  if($buttonAction!="Delete")
		  {
		  
		  if($this->content_model->is_Page_id_InContentTask($content['coursePageId'][$page])==TRUE)
		  {
			  $this->content_model->updateContentTask($contentArr,$content['coursePageId'][$page]);
		  }
		  else
		  {
			  $contentArr['course_page_id']=$content['coursePageId'][$page];
			  $this->content_model->addContentTask($contentArr);
		  }
		  if($buttonAction!='Save')
		  {
			  $langId=$this->content_model->getLangByUnit($unitId);
			  			  
			  $this->content_model->addCoursePage((int)$page+1,$unitId,'task','1','1',$langId);
			  
		  }
		  
		  }
		  else
		  {
		  $langId=$this->content_model->getLangByUnit($unitId);
		  $this->content_model->deleteContentTaskPage($content['coursePageId'][$page],$page,$unitId,'task','1','1',$langId);
		  $content['curPage'] -=1;
		  
		  
		  }
		  
	  }

				//$this->session->set_flashdata('message', 'CMS page added successfully.');
			 	
			}
			
			$coursePagesArr = $this->content_model->getCoursePagesExersice($unitId);
		
		//echo $coursePagesArr;exit;
		$i=0;
		foreach($coursePagesArr as $row)
		{
			$content['coursePageId'][$row->page_no] = $row->page_id;
			 $content['page_no'][$i] = $row->page_no;
			
			
			$i++;
		}
		$page = $this->uri->segment(5);
		if($page==''){$page=1;}
		//echo $page;exit;
		$content['curPage']=$page;
		
			
			
		}
		//----------------------------------------------------------------------------------------------
		
		//echo "<pre>"; print_r($content);exit;
		
		if($content['type'][$page]=='content')
		$content['pageContent'] = $this->content_model->getContent($content['coursePageId'][$page]);
		else if($content['type'][$page]=='task')
		$content['pageTaskId'] = $this->content_model->getPageTask($content['coursePageId'][$page]);
		
		$taskArr =$this->content_model->getTasks();
		//echo "<pre>";print_r($taskArr);exit;
		$t=0;
		foreach($taskArr as $row1)
		{
		$content['testId'][$t]=$row1->task_id;
		$content['testName'][$t]=$row1->test_name;
		$content['template_id'][$content['testId'][$t]]=$row1->template_id;
		$t++;
		}
		
		
		$config['base_url'] = base_url().'admin/course/exersicePages/'.$unitId;
		$config['total_rows'] = $i;
		$config['per_page'] = 1; 
		$config['use_page_numbers'] = TRUE;
		$config["uri_segment"] = 5;
$config['num_tag_open'] = '<p>';
$config['num_tag_close'] = '</p>';
$config['full_tag_open'] = '<div class=numberings>';	
$config['full_tag_close'] = '</div>';		
$config['first_tag_open'] = "<p class='numbers'>";
$config['first_tag_close'] = '</p>';
$config['last_tag_open'] = "<p class='numbers'>";
$config['last_tag_close'] = '</p>';
$config['next_tag_open'] = "<p class='numbers'>";
$config['next_tag_close'] = '</p>';
$config['prev_tag_open'] = "<p class='numbers'>";
$config['prev_tag_close'] = '</p>';
$config['cur_tag_open'] = "<p class='pagin'>";
$config['cur_tag_close'] = '</p>';
$config['num_tag_open'] = "<p class='numbers'>";
$config['num_tag_close'] = '</p>';
		
		
		
		
		
		$this->pagination->initialize($config); 
		
		$content['links'] =$this->pagination->create_links();
		
		
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		$data['view'] = 'exersicePage';
		$data['content'] = $content;
		
		//echo "<pre>";print_r($content);exit;
		$this->load->view('admin/template',$data);
		
	}


	
	function examPages($unitId)
	{
		
		
		$this->load->library('pagination');
		$this->load->model('content_model');
		
		
		$content['unitId']=$unitId;	
		$groupDetails = $this->course_model->get_group_byUnit($unitId);	
		$content['groupName'] = $groupDetails->course_group_name;
		$content['group_id'] = $groupDetails->id;
		$content['unitName'] = $this->course_model->get_unitName($unitId);
		
		$coursePagesArr = $this->content_model->getCoursePagesExam($unitId);
		
		//echo"<pre>";print_r($coursePagesArr);exit;
		$i=0;
		foreach($coursePagesArr as $row)
		{
			$content['coursePageId'][$row->page_no] = $row->page_id;
			$content['page_no'][$i] = $row->page_no;
			
			
			$i++;
		}
		$page = $this->uri->segment(5,1);
		$content['curPage']=$page;
	
		//----------------------------------------------------------------------------------------------
		if(isset($_POST['save']))
		{
			$content['pageTaskId'] =$this->input->post('testAssign');
			$this->form_validation->set_rules('testAssign', 'Select one task ', 'trim|required');
			
		if($this->form_validation->run())
  		{
	  	$buttonAction = $this->input->post('save');	
			
			$contentArr['task_id'] =$content['pageTaskId'];
		  //echo "entered in task saving part";exit;
		    //echo "<pre>";print_r($contentArr);exit;
		  if($buttonAction!="Delete")
		  {
		  
		  if($this->content_model->is_Page_id_InContentTask($content['coursePageId'][$page])==TRUE)
		  {
			  $this->content_model->updateContentTask($contentArr,$content['coursePageId'][$page]);
		  }
		  else
		  {
			  $contentArr['course_page_id']=$content['coursePageId'][$page];
			  $this->content_model->addContentTask($contentArr);
		  }
		  if($buttonAction!='Save')
		  {
			  $langId=$this->content_model->getLangByUnit($unitId);
			  			  
			  $this->content_model->addCoursePage((int)$page+1,$unitId,'task','2','0',$langId);
			  
		  }
		  
		  }
		  else
		  {
		  $langId=$this->content_model->getLangByUnit($unitId);
		  $this->content_model->deleteContentTaskPage($content['coursePageId'][$page],$page,$unitId,'task','2','0',$langId);
		  $content['curPage'] -=1;
		  
		  
		  }
		  
	  

				//$this->session->set_flashdata('message', 'CMS page added successfully.');
			
			$coursePagesArr = $this->content_model->getCoursePagesExersice($unitId);
		
		//echo $coursePagesArr;exit;
		$i=0;
		foreach($coursePagesArr as $row)
		{
			$content['coursePageId'][$row->page_no] = $row->page_id;
			 $content['page_no'][$i] = $row->page_no;
			
			
			$i++;
		}
		$page = $this->uri->segment(5);
		if($page==''){$page=1;}
		//echo $page;exit;
		$content['curPage']=$page;
		
			
			
		}
		}
	
		//----------------------------------------------------------------------------------------------
		
		//echo "<pre>"; print_r($content);exit;
		
		$content['pageTaskId'] = $this->content_model->getPageTask($content['coursePageId'][$page]);
		
		$taskArr =$this->content_model->getTasks();
		//echo "<pre>";print_r($taskArr);exit;
		$t=0;
		foreach($taskArr as $row1)
		{
		$content['testId'][$t]=$row1->task_id;
		$content['testName'][$t]=$row1->test_name;
		$content['template_id'][$content['testId'][$t]]=$row1->template_id;
		$t++;
		}
		
		
		$config['base_url'] = base_url().'admin/course/examPages/'.$unitId;
		$config['total_rows'] = $i;
		$config['per_page'] = 1; 
		$config['use_page_numbers'] = TRUE;
		$config["uri_segment"] = 5;
$config['num_tag_open'] = '<p>';
$config['num_tag_close'] = '</p>';
$config['full_tag_open'] = '<div class=numberings>';	
$config['full_tag_close'] = '</div>';		
$config['first_tag_open'] = "<p class='numbers'>";
$config['first_tag_close'] = '</p>';
$config['last_tag_open'] = "<p class='numbers'>";
$config['last_tag_close'] = '</p>';
$config['next_tag_open'] = "<p class='numbers'>";
$config['next_tag_close'] = '</p>';
$config['prev_tag_open'] = "<p class='numbers'>";
$config['prev_tag_close'] = '</p>';
$config['cur_tag_open'] = "<p class='pagin'>";
$config['cur_tag_close'] = '</p>';
$config['num_tag_open'] = "<p class='numbers'>";
$config['num_tag_close'] = '</p>';		
		
		
		
		
		
		$this->pagination->initialize($config); 
		
		$content['links'] =$this->pagination->create_links();
		
		
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		$data['view'] = 'examPage';
		$data['content'] = $content;
		
		//echo "<pre>";print_r($content);exit;
		$this->load->view('admin/template',$data);
		
		
	}
	
	
	
	
	
	function browse_extension()
	{
		$content = array();
		
		$content['searchmode'] = true;		
		$data['view'] = 'extension_browse';
		
        $data['content'] = $content;
        
        $this->load->view('admin/template',$data);	
	}
	
	
	
	function fetch_extensions()
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
		$this->db-> from('extension_options');
		
		
		
        $query = $this->db->get();
        
        
        $data = array();
		$data['page'] = $page;
		$data['total'] = $query -> num_rows();
		$data['rows'] = array();
		     
        
        if($query -> num_rows() >0 )
		{
			$result = $query -> result();
			
			foreach($result as $row){
				$this->db-> select('id');
		        $this->db-> from('products');
				$this->db-> where('item_id',$row->id);
				$this->db-> where('type','extension');
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
				 
				 //$action = '<a href="'.base_url().'admin/course/extension_edit/'.$row->id.'">Edit</a>&nbsp;&nbsp;<a href="'.base_url().'admin/course/extension_delete/'.$row->id.'">Delete</a>';
				 
				
			   
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($row->id,$row->extension_option,$EUR,$GBP,$USD,$action_add_price,$action_view_price/*,$action*/)
			);
			}
		}
			
		}
		}
      
             
       echo json_encode($data); exit(); 
		
		
		
	
	}
  
  
  
  function extension_add_price($ext_id)
  {
    	  $content = array();		   
		  $product_id  = $this->common_model->getProdectId('extension',$ext_id,1);
		  
			
			if(isset($_POST['save_price']))
			{
				 
				$extension_price_data  = array();
				
				$extension_price_data['product_id']   = $product_id;				
				$extension_price_data['amount']	   = ($this->input->post('extension_price'));
				$extension_price_data['fake_amount']  = ($this->input->post('fake_price'));
				$extension_price_data['currency_id']  = ($this->input->post('currency'));
							 
				$this->form_validation->set_rules('currency', 'Currency', 'trim|required');				
				$this->form_validation->set_rules('extension_price', 'Price', 'trim|required');
				
														
				
				if($this->form_validation->run())
				{	
					$this->course_model->add_extension_price($extension_price_data);
					 $this->session->set_flashdata('message', 'Extension price added successfully!');
					 redirect('admin/course/browse_extension', 'refresh');
				}
			}
			
			$this->load->helper(array('form'));
			$this->load->library('form_validation');
			
			if(isset($this->flashmessage)){
			$data['flashmessage'] = $this->flashmessage;
			}
			
			if(isset($this->flashmessage))
			$content['flashmessage'] = $this->flashmessage;
			$data['view'] = 'extension_price_add';
			$data['currency'] = $this->common_model->get_currency();
			$data['ext_id'] = $ext_id;
			$data['mode'] = 0;
			$data['content'] = $content;
			$this->load->view('admin/template',$data);	
	  
	  
  }
  
  function extension_view_price($ext_id)
  {
	    $content = array();
		$data['view'] = 'extension_price_view';		
		$data['ext_id'] = $ext_id;
		$data['mode'] = 0;
		$data['content'] = $content;
		$this->load->view('admin/template',$data);	   
	  
  }
  
  function fetch_extension_price($ext_id)
  {
	   $product_id  = $this->common_model->getProdectId('extension',$ext_id,1);
	   $k = 0;  
	   
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
		$this->db-> where('product_id',$product_id);
		
		
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
				$k++;		
				 
				 $action = '<a href="'.base_url().'admin/course/extension_price_edit/'.$row->id.'/'.$ext_id.'">Edit</a>&nbsp;&nbsp;<a href="'.base_url().'admin/course/ebook_price_delete/'.$row->id.'/'.$ext_id.'">Delete</a>';
				 
				  $currency_name = $this->common_model->get_currency_name($row->currency_id);
			   
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($k,$row->amount,$row->fake_amount,$currency_name,$action)
			);
			}
			
		}
      
             
       echo json_encode($data); exit(); 
		
	  
  }
	
	
	function extension_price_edit($price_id,$ext_id)
	{
		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		$content = array();
		//$id = $_GET['id'];
		$pagedata = $this->common_model->get_product_fee($price_id);
	
		
		foreach($pagedata as $row){			
			
     		 $content['amount']   	  = $row->amount;
			 $content['fake_price']  = $row->fake_amount;
			 $content['currency_id'] = $row->currency_id;			 
		}
		
		
		
		
		if(isset($_POST['save_price']))
		{
		
			$extension_price_data  = array();			
		    
			$extension_price_data['amount']	   = ($this->input->post('extension_price'));
			$extension_price_data['fake_amount']  = ($this->input->post('fake_price'));
			$extension_price_data['currency_id']  = ($this->input->post('currency'));
		    			 
			$this->form_validation->set_rules('currency', 'Currency', 'trim|required');			
			$this->form_validation->set_rules('extension_price', 'Price', 'trim|required');			
			
			if($this->form_validation->run())
			{	
			 	$this->course_model->update_extension_price($price_id,$extension_price_data);
			 	 $this->session->set_flashdata('message', 'Ebook price updated successfully!');
			 	 redirect('admin/course/extension_view_price/'.$ext_id, 'refresh');
			}
		}
		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		
		if(isset($this->flashmessage)){
		$data['flashmessage'] = $this->flashmessage;
		}
		
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		$data['view'] = 'extension_price_add';
		$data['currency'] = $this->common_model->get_currency();
		$data['price_id'] = $price_id;
		$data['ext_id'] = $ext_id;
		$data['mode'] = 1;
		$data['content'] = $content;
		$this->load->view('admin/template',$data);			
		
		
	}
	
	function ebook_price_delete($price_id,$ext_id)
	{
		$this->course_model->delete_extension_price($price_id);
		$this->session->set_flashdata('message', 'Extension price deleted successfully!');
		redirect('admin/course/extension_view_price/'.$ext_id, 'refresh');
		
	}
	function get_course_ids_by_lang($lang_id)
	{
		$print='';
		$courses = $this->common_model->get_base_courses($lang_id);	
		
		
		 foreach($courses as $key=>$value)
		{		
			
			if($print=='')
			{
				$print = "<option value='".$key."'>".$value."</option>";	
			}
			else
			$print .= '<option value="'.$key.'">'.$value.'</option>';
		}
	
	echo $print;
				
	}
	
}
?>