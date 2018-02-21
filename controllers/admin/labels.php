<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class labels extends CI_Controller
{
 	function __construct()
 	{
  		parent::__construct();
  		
  		if(!$this->session->userdata('admin_logged_in'))
   		redirect('admin/login', 'refresh');
		
		$this->load->library('form_validation');
		$this->load->model('admin_model','',TRUE);
		$this->load->model('label_model','',TRUE);
		
		if($message = $this->session->flashdata('message')){
          $this->flashmessage =$message;
   		}
  		 		
 	}

 	function index()
 	{
 	
 	   
 	}
	
	
	function fetch_labels()
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
		$this->db-> from('label_translation_english');
		//$this->db-> where('lang_id',4);
		
		
		if(isset($_GET['translation_identifier']) && $_GET['translation_identifier']!=''){
			$this->db->like('lable_identifier', $_GET['translation_identifier']); 

		}
		
		
		
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
			
			    $action = '<a href="'.base_url().'admin/labels/label_edit_english/'.$row->id.'">Edit</a>';
			   
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($row->id,$row->lable_identifier,$row->label_description,$row->active,$action)
			);
			}
			
		}
      
             
       echo json_encode($data); exit(); 
		
	}
	
	function fetch_labels_other($language_list='')
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
		$this->db-> from('label_translation_english');
	
	
		
		$this->db->order_by($sortname,$sortorder);
		//$this->db->limit($rp,$pageStart);
        $query = $this->db->get();
        
        
        $data = array();
		$data['page'] = $page;
		$data['total'] = $query -> num_rows();
		$data['rows'] = array();
		
		
		$total_array = $query->result();
		
		$result = array_slice($total_array,$pageStart,$rp);
        $count = 0;
		     
        
        if($query -> num_rows() >0 )
		{
			
			
		//	$result = $query -> result();
			
			foreach($result as $row){
				$count++;
				
				if($language_list!=4)
				{
					 $this->db-> select('*');
		             $this->db-> from('label_translation_other');
					 $this->db-> where('id_lang',$language_list);
					 $this->db-> where('id_english',$row->id);
					$query1 = $this->db->get();
					if($query1 -> num_rows() >0 )
		{
			
			
			$result1 = $query1 -> result();
			//echo "<pre>";print_r($result1);exit;
			foreach($result1 as $row1){
			
				
			
			    $action = '<a href="'.base_url().'admin/labels/label_edit_other/'.$row1->id.'">Edit</a>';
			   
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($count,$row->lable_identifier,$row->label_description,$row1->label_description,$action)
			);
			}
		}
			
		}
		else
		{
			$action = '<a href="'.base_url().'admin/labels/label_edit_english/'.$row->id.'">Edit</a>';
			 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($count,$row->lable_identifier,$row->label_description,$action)
			);
			}
		}
		
		
		}
      
             
       echo json_encode($data); exit(); 
	   
		
		
	}
	
	
	
	
	
	
		
	
	function fetch_labels_spanish()
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
       
        
       /* $this->db-> select('label_translation_other.id as id_spa,label_translation_other.id_english,label_translation_other.id_lang,label_translation_other.label_description as spa_discri,label_translation_other.active as spa_active,label_translation_english.active as eng_active,label_translation_english.id as id_eng,label_translation_english.label_description as eng_discri,label_translation_english.lable_identifier');*/
	   
		$this->db-> select('*'); 
		$this->db-> from('label_translation_english');
		//$this->db-> join('label_translation_other','label_translation_english.id=label_translation_other.id_english');
		//$this->db-> where('label_translation_other.id_lang','3');
		//$this->db-> or_where('label_translation_other.id_lang','4');
		
		
		
		if(isset($_GET['translation_identifier1']) && $_GET['translation_identifier1']!=''){
			$this->db->like('lable_identifier', $_GET['translation_identifier1']); 

		}
		
		
		
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
				
				  $action = '<a href="'.base_url().'admin/labels/label_edit_english/'.$row->id.'">Edit</a>';
				  
				 
				 $data['rows'][] = array(
				'id' => $sl,
				'cell' => array($row->id,$row->lable_identifier,$row->label_description)
			);
			
		
			
		}
			
	}
      
             
       echo json_encode($data); exit(); 
		
	}
	function browse_labels($lang_list=4)
	{
	
		$content['language_list'] 				   =$lang_list;
		//$content['lang'] 				   = $lang;
		//$content['translation_identifier'] = isset($translation_identifier)?$translation_identifier:'';
		//$content['searchmode'] = false;
		
		$data['view'] = 'label_browse_english';
		$content['searchmode'] =0;
        $data['content'] = $content;
        
        $this->load->view('admin/template',$data);
	
	}
	function search_label(){
		
		$lang=$this->input->post('language');
		$label_identifier=$this->input->post('translation_identifier');
		
	
		$content['language'] = $lang;
		$content['idntf'] = isset($label_identifier)?$label_identifier:'';
		
		$content['searchmode'] =1;
	
		$data['view'] = 'label_browse_english';
		
        $data['content'] = $content;
        
        $this->load->view('admin/template',$data);

	
	}
	function search_label_fetch()
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
		
		
        
       /* $this->db-> select('label_translation_other.id as id_spa,label_translation_other.id_english,label_translation_other.id_lang,label_translation_other.label_description as spa_discri,label_translation_other.active as spa_active,label_translation_english.active as eng_active,label_translation_english.id as id_eng,label_translation_english.label_description as eng_discri,label_translation_english.lable_identifier');*/
	  if(isset($_GET['language']) && $_GET['language']==4 ){
		$this->db-> select('*'); 
		$this->db-> from('label_translation_english');
		if(isset($_GET['idntf']) && $_GET['idntf']!=''){
			$this->db->like('lable_identifier', $_GET['idntf'],'both');
		}
		
		$query2 = $this->db->get();
		$pageStart = ($page-1)*$rp;
        $data = array();
		$data['page'] = $page;
		$data['total'] = $query2 -> num_rows();
		if($query2 -> num_rows() >0 )
		{
		
			$result2 = $query2 -> result();
			
			foreach($result2 as $row2){
				
				$action = '<a href="'.base_url().'admin/labels/label_edit_english/'.$row2->id.'">Edit</a>';
				
				$data['rows'][] = array(
				'id' => $row2->id,
				'cell' => array($row2->id,$row2->lable_identifier,$row2->label_description,$action)
			);
				
			
				
			}
		}
	  }
		else
		{
			$this->db-> select('*'); 
		$this->db-> from('label_translation_english');
		         if(isset($_GET['idntf']) && $_GET['idntf']!='')
		              {
			             $this->db->like('lable_identifier', $_GET['idntf']);
	                	}
		$query = $this->db->get();
		$pageStart = ($page-1)*$rp;
        $data = array();
		$data['page'] = $page;
		$data['total'] = $query -> num_rows();
		if($query -> num_rows() >0 )
		{
		
			$result = $query -> result();
			
		
			foreach($result as $row){
				$this->db-> select('*'); 
		$this->db-> from('label_translation_other');
		$this->db->where('id_english',$row->id);
		$this->db->where('id_lang',$_GET['language']);
		$query1 = $this->db->get();
		if($query1 -> num_rows() >0 )
		{
		
			$result1 = $query1 -> result();
			//echo "<pre>";print_r($result1);exit;
			foreach($result1 as $row1){
		//$this->db-> join('label_translation_other','label_translation_english.id=label_translation_other.id_english');
		//$this->db-> where('label_translation_other.id_lang','3');
		//$this->db-> or_where('label_translation_other.id_lang','4');
		$action = '<a href="'.base_url().'admin/labels/label_edit_other/'.$row1->id.'">Edit</a>';
		
		 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($row->id,$row->lable_identifier,$row->label_description,$row1->label_description,$action)
			);
		
		}
		}
			}
		}
		
		
     
		
	
	}
	  echo json_encode($data); exit(); 
	}

	
	
	/*Add English transaltion*/
	
	function add_english_label()
	{
		
		$content = array();
		
		if(isset($_POST['save_label']))
		{
		
			$label_data  = array();
		    $label_data['lable_identifier'] 	= ($this->input->post('identifier'));
			$label_data['label_description'] 	= ($this->input->post('label_description'));
			$label_data['lang_id']	    		= 4;
			$label_data['active']	    		= '1';
		    			 
			$this->form_validation->set_rules('identifier', 'Identifier', 'trim|required|callback_chkunique_identifier[identifier]');
			$this->form_validation->set_rules('label_description', 'Label decsription', 'trim|required');			
			
			if($this->form_validation->run())
			{	
			 	$this->label_model->add_english_label($label_data);
			 	 $this->session->set_flashdata('message', 'Label added successfully!');
			 	 redirect('admin/labels/browse_labels', 'refresh');
			}
		}
		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		
		if(isset($this->flashmessage)){
		$data['flashmessage'] = $this->flashmessage;
		}
		
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		$data['view'] = 'label_add_english';
		$data['mode'] = 0;
		$data['content'] = $content;
		$this->load->view('admin/template',$data);			
		
	}
	/* End Add English transaltion*/
	
	/* Edit English transaltion*/
	
	function label_edit_english($id){
	
	$this->load->helper(array('form'));
	$this->load->library('form_validation');
	$content = array();
	//$id = $_GET['id'];
	$pagedata = $this->label_model->fetch_english_label($id);

	
	foreach($pagedata as $row){
	
	 $content['id_data']				= $row->id;	
	 $content['identifier_data']		= $row->lable_identifier;
	 $content['label_description_data'] = $row->label_description;		 
	}
		
		
	   if(isset($_POST['save_label']))
		{
		
			$label_data  = array();
		    $label_data['lable_identifier'] 	= $content['identifier_data'];
			$label_data['label_description'] 	= ($this->input->post('label_description'));
			$label_data['lang_id']	    		= 4;
			$label_data['active']	    		= '1';
		    			 
			
			$this->form_validation->set_rules('label_description', 'Label decsription', 'trim|required');			
			
			if($this->form_validation->run())
			{	
			 	$this->label_model->edit_english_label($label_data,$id);
			 	 $this->session->set_flashdata('message', 'Label updated successfully!');
			 	 redirect('admin/labels/browse_labels', 'refresh');
			}
		}
		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		
		if(isset($this->flashmessage)){
		$data['flashmessage'] = $this->flashmessage;
		}
		
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		$data['view'] = 'label_add_english';
		$data['mode'] = 1;
		$data['content'] = $content;
		$this->load->view('admin/template',$data);			
		
		
	}
	
	/* End Edit English transaltion*/
	
	/*Add other label transaltion*/
	
	function label_edit_other($id){
	
	$this->load->helper(array('form'));
	$this->load->library('form_validation');
	$content = array();
	//$id = $_GET['id'];
	$pagedata = $this->label_model->fetch_other_label($id);

	
	foreach($pagedata as $row){
	
	 $content['id_data']				= $row->id;	
	 $content['language']		= $row->id_lang;
	 $content['label_description'] = $row->label_description;	
	// echo $content['label_description'];exit;
	 $pagedata1 = $this->label_model->fetch_english_label($row->id_english);	
	 foreach($pagedata1 as $row1){
	 
	 $content['identifier_data']		= $row1->lable_identifier; 
	 }
	}
		
		
	   if(isset($_POST['save_label']))
		{
		
			$label_data  = array();
		    //$label_data['lable_identifier'] 	= $content['identifier_data'];
			$label_data['label_description'] 	= ($this->input->post('label_description'));
			//$label_data['lang_id']	    		= $content['language'];
			$label_data['active']	    		= '1';
		    			 
			
			$this->form_validation->set_rules('label_description', 'Label decsription', 'trim|required');			
			
			if($this->form_validation->run())
			{	
			 	$this->label_model->edit_other_label($label_data,$id);
			 	 $this->session->set_flashdata('message', 'Label updated successfully!');
			 	 redirect('admin/labels/browse_labels', 'refresh');
			}
		}
		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		
		if(isset($this->flashmessage)){
		$data['flashmessage'] = $this->flashmessage;
		}
		
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		$data['view'] = 'label_add_other';
		$data['mode'] = 1;
		$data['content'] = $content;
		$this->load->view('admin/template',$data);			
		
		
	}
	
	
	
	
	
	
		function add_other_label()
		 {
		  
		  $content = array();
		  
		  if(isset($_POST['save_label']))
		  {
		  
		   $other_label_data  = array();
			  $other_label_data['id_english']   = ($this->input->post('label_english'));
		   $other_label_data['label_description']  = ($this->input->post('label_description'));
		   $other_label_data['id_lang']      = ($this->input->post('language'));
		   $other_label_data['active']       = '1';
				  
		   $this->form_validation->set_rules('label_english', 'english label', 'trim|required');
		   $this->form_validation->set_rules('label_description', 'Label decsription', 'trim|required');   
		   
		   if($this->form_validation->run())
		   { 
			 $this->label_model->add_label_other($other_label_data);
			  $this->session->set_flashdata('message', 'Label added successfully!');
			  redirect( 'admin/labels/add_other_label', 'refresh');
		   }
		  }
		  
		  $this->load->helper(array('form'));
		  $this->load->library('form_validation');
		  
		  if(isset($this->flashmessage)){
		  $data['flashmessage'] = $this->flashmessage;
		  }
		  
		  if(isset($this->flashmessage))
		  $content['flashmessage'] = $this->flashmessage;
		  $data['english_label'] = $this->label_model->get_english_label();
		  $data['mode'] = 0;
		  $data['view'] = 'label_add_other';
		  $data['content'] = $content;
		  $this->load->view('admin/template',$data);   
		  
		  
		 }
	/*End Add other label transaltion*/
	
	/* Checking the label exsts or not*/
	
	function chkunique_identifier()
	{
		$label_identifier = $this->input->post('identifier');
		
		$valid_identifier = $this->label_model->check_unique_identifier($label_identifier);
	
		if($valid_identifier)
		{		   
		   return TRUE;
		}
		else
		{
		    $this->form_validation->set_message('chkunique_identifier', 'Label already exists');
			return FALSE;
		}
	/* End Checking the label exsts or not*/
	
	}
	
	
		
	
	
}

?>
