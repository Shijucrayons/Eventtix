<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class hardcopy extends CI_Controller
{
	
	function __construct()
 	{
  		parent::__construct();
  		
  		if(!$this->session->userdata('admin_logged_in'))
   		redirect('admin/login', 'refresh');
		
		$this->load->library('form_validation');
		$this->load->model('common_model','',TRUE);
		$this->load->model('hardcopy_model','',TRUE);
		
		if($message = $this->session->flashdata('message')){
          $this->flashmessage =$message;
   		}
  		 		
 	}
	
	function index()
 	{
 	
 	   
 	}
	
	function browse_hardcopy()
	{
		$content = array();
		
		$content['searchmode'] = true;		
		$data['view'] = 'hardcopy_browse';
		
        $data['content'] = $content;
        
        $this->load->view('admin/template',$data);	
	}
	
	
	
	function fetch_hardcopy()
	{
		
		$page = 1;	// The current page
		$sortname = 'hcid';	 // Sort column
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
		
		// Setup paging SQL
		$pageStart = ($page-1)*$rp;
		
		$this->db-> select('*');
		$this->db-> from('certificate_postage_options');
		
		
		
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
				$this->db-> where('type',$hardcopy);
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
				 
				 $action = '<a href="'.base_url().'admin/hardcopy/hardcopy_edit/'.$row1->id.'">Edit</a>&nbsp;&nbsp;<a href="'.base_url().'admin/ebooks/ebook_delete/'.$row->id.'">Delete</a>';
				 
				 
			   
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($row->id,$row->postage_type,$EUR,$GBP,$USD,$action_add_price,$action_view_price)
			);
			}
			}
		}
			
		}
      
             
       echo json_encode($data); exit(); 
		
		
		
	
	}
	
	
	
	function hardcopy_add_price($id)
	{
		
		$content = array();
		
		if(isset($_POST['save_price']))
		{
		
			$hardcopy_price_data  = array();
			
		    $hardcopy_price_data['product_id'] 	= $id;
			$hardcopy_price_data['currency_id']	    = ($this->input->post('currency'));
			$hardcopy_price_data['fake_amount']	= ($this->input->post('fake_price'));
			$hardcopy_price_data['amount']	= ($this->input->post('hardcopy_price'));
		    			 
			$this->form_validation->set_rules('currency', 'Currency', 'trim|required');
			$this->form_validation->set_rules('hardcopy_price', 'Price', 'trim|required');			
			
			if($this->form_validation->run())
			{	
			 	$this->hardcopy_model->add_hardcopy_price($hardcopy_price_data);
			 	 $this->session->set_flashdata('message', 'Hardcopy price added successfully!');
			 	 redirect('admin/hardcopy/browse_hardcopy', 'refresh');
			}
		}
		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		
		if(isset($this->flashmessage)){
		$data['flashmessage'] = $this->flashmessage;
		}
		
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		$data['view'] = 'hardcopy_price_add';
		$data['currency'] = $this->common_model->get_currency();
		$data['id'] = $id;
		$data['mode'] = 0;
		$data['content'] = $content;
		$this->load->view('admin/template',$data);			
		
		
		
	
	}
	
	function hardcopy_view_price($id)
	{		
		$content = array();
		$data['view'] = 'hardcopy_price_view';		
		$data['id'] = $id;
		$data['mode'] = 0;
		$data['content'] = $content;
		$this->load->view('admin/template',$data);					
		
	}
	
	function fetch_hardcopy_price($id)
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
		//echo $id;exit;
		$this->db-> select('*');
		$this->db-> from('price_currency');
		$this->db-> where('product_id',$id);
		
		
		//$this->db->order_by($sortname,$sortorder);
		//$this->db->limit($rp,$pageStart);
        $query = $this->db->get();
        
        
        $data = array();
		$data['page'] = $page;
		$data['total'] = $query -> num_rows();
		$data['rows'] = array();
		     
        
        if($query -> num_rows() >0 )
		{
			$result = $query -> result();
			
			foreach($result as $row){
						
				 
				 $action = '<a href="'.base_url().'admin/hardcopy/hardcopy_price_edit/'.$row->id.'">Edit</a>&nbsp;&nbsp;<a href="'.base_url().'admin/hardcopy/hardcopy_price_delete/'.$row->id.'/'.$id.'">Delete</a>';
				 
				 
			   
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($row->id,$row->amount,$row->fake_amount,$row->currency_id,$action)
			);
			}
			
		
		}
      
             
       echo json_encode($data); exit(); 
		
		
		
	
	}
	
	function hardcopy_price_edit($price_id)
	{
		
		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		$content = array();
		//$id = $_GET['id'];
		$pagedata = $this->hardcopy_model->fetch_hardcopy_price($price_id);
	
		
		foreach($pagedata as $row){
			
			 $content['price_id'] 		 = $row->id;
			 $id = $content['product_id'] 	 = $row->product_id;
     		 $content['amount']   	 = $row->amount;
			 $content['fake_amount']  = $row->fake_amount;
			 $content['currency_id'] = $row->currency_id;			 
		}
		
		
		
		
		if(isset($_POST['save_price']))
		{
		
			$hardcopy_price_data  = array();
			
		    $hardcopy_price_data['product_id'] 	= $id;
			$hardcopy_price_data['amount']	    = ($this->input->post('hardcopy_price'));
			$hardcopy_price_data['fake_amount']	= ($this->input->post('fake_price'));
			$hardcopy_price_data['currency_id']	= ($this->input->post('currency'));
		    			 
			$this->form_validation->set_rules('currency', 'Currency', 'trim|required');
			$this->form_validation->set_rules('hardcopy_price', 'Price', 'trim|required');			
			
			if($this->form_validation->run())
			{	
			 	$this->hardcopy_model->update_hardcopy_price($hardcopy_price_data,$price_id);
			 	 $this->session->set_flashdata('message', 'Ebook price updated successfully!');
			 	 redirect('admin/hardcopy/hardcopy_view_price/'.$id, 'refresh');
			}
		}
		
		$this->load->helper(array('form'));
		$this->load->library('form_validation');
		
		if(isset($this->flashmessage)){
		$data['flashmessage'] = $this->flashmessage;
		}
		
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		$data['view'] = 'hardcopy_price_add';
		$data['currency'] = $this->common_model->get_currency();
		$data['id'] = $id;
		$data['mode'] = 1;
		$data['content'] = $content;
		$this->load->view('admin/template',$data);			
		
		
	}
	
	function hardcopy_price_delete($price_id,$id)
	{
		$this->hardcopy_model->delete_hardcopy_price($price_id);
		$this->session->set_flashdata('message', 'Hardcopy price deleted successfully!');
		redirect('admin/hardcopy/hardcopy_view_price/'.$id, 'refresh');
		
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
	
	
	
}