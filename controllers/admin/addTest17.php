<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class addTest17 extends CI_Controller {

	 
	function __construct()
	{
		parent::__construct();
		$this->load->model('questionmodel','',TRUE);
		
		if(!$this->session->userdata('admin_logged_in'))
   		redirect('admin/login', 'refresh');
   		
   		if($message = $this->session->flashdata('message')){
          $this->flashmessage =$message;
   		}

   		$this->load->helper(array('form'));
		$this->load->library('form_validation');
        $this->load->database();

    }
	function index()
	{

	}


	function addquestion($task_id=NULL)
	{	
		$content = array();
		$content['task_id']=$task_id;
		
	  
		
		// field name, error message, validation rules
		if(isset($_POST['question']))
		{
			
			$testdata  = array();
			$testdata_question['task_id']=$testdata_answer['task_id']=$task_id;
		    $testdata_question['question'] = $content['question'] = $this->input->post('question');
		    $testdata_answer['image'] = $content['answer'] = $this->input->post('answer');
			
			$this->form_validation->set_rules('question', 'Question', 'required');
			$this->form_validation->set_rules('answer', 'Answer', 'required');
			
			if($this->form_validation->run())
			{	
			 	//$this->questionmodel->add_question($testdata);
				 $testdata_answer['question_id']=$this->questionmodel->add_17_question($testdata_question);
				$this->questionmodel->add_17_option($testdata_answer);
			 	redirect('admin/addTest17/addquestion/'.$task_id, 'refresh');
			}
		}
		
		$this->db->select('*');
		$this->db->from('radio_image_question');
		$this->db->where('task_id',$task_id);
		$query = $this->db->get();
		if($query->num_rows()>0)
		{
			$i = 0;
			foreach($query->result() as $row)
			{
				$this->db->select('*');
				$this->db->from('radio_image');
				$this->db->where('question_id',$row->id);
				$query1 = $this->db->get();
				$result = $query1->result();
				$content['questions']['question_id'][$i] = $row->id;
				$content['questions']['question'][$i] = $row->question;
				$content['questions']['answer'][$i] = $result[0]->image;
				$content['questions']['order'][$i] = $row->question_order;
				$content['questions']['action'][$i] = '<a href="'.base_url().'admin/addTest17/edit/'.$row->id.'/'.$row->task_id.'">Edit</a>';
			    $content['questions']['action'][$i] .=' | <a href="'.base_url().'admin/addTest17/delete/'.$row->id.'/'.$row->task_id.'">Delete</a>';
				
				$i++;
				
			}
		}
		
			
		$data['view'] = 'radioquestion';
		$content['mode']=0;
		$data['content'] = $content;
		
		$this->load->view('admin/template',$data);
	}
	
	function edit($id,$task_id){
		$content = array();
		//$id = $_GET['id'];
		
		$content = $this->questionmodel->fetch_17_question($id);
		$content['task_id'] =$task_id;
		
		/*foreach($pagedata as $row){
			$content['task_id']  = $row->tasks_idtasks;
			$content['question'] = $row->question;
			$content['answer'] = $row->answer;
		}*/
		//echo "<pre>";print_r($content);exit;
		if(isset($_POST['question']))
		{
			$testdata  = array();
						
		    $testdata['question'] = $content['question'] = $this->input->post('question');
		    $optiondata['image'] = $content['answer'] = $this->input->post('answer');
			
			$this->form_validation->set_rules('question', 'Question', 'required');
			$this->form_validation->set_rules('answer', 'Answer', 'required');
			
			if($this->form_validation->run())
			{	
			 	$this->questionmodel->update_17_question($id,$testdata);
				$this->questionmodel->update_17_option($id,$optiondata);
			 	redirect('admin/addTest17/addquestion/'.$content['task_id'], 'refresh');
			}
		}		
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		$content['mode']=1;
		$data['view'] = 'radioquestion';
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

	function fetchdata($task_id){
		

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
		$this->db-> from(' radio_image_question');
		$this->db-> where('task_id',$task_id);
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
			   // print_r($row);die;

				
			    $action = '<a href="'.base_url().'admin/addTest17/edit/'.$row->id_rd_ques.'">Edit</a>';
			    $action .=' | <a href="'.base_url().'admin/addTest17/delete/'.$row->id_rd_ques.'">Delete</a>';
				 $data['rows'][] = array(
				'id' => $row->id_rd_ques,
				'cell' => array($row->id,$row->question,$row->answer,$action)
			);
			}
			
		}
        
  
       echo json_encode($data); exit(); 
        
       
           
	}

		

	function delete($id){
	  $taskid = $this->questionmodel->fetchtaskid($id);
		foreach ($taskid as $key) {
			$val=$key->tasks_idtasks;
		}
	  $this->questionmodel->question_delete($id);
	   $this->session->set_flashdata('message', 'Question Deleted');
	   redirect('admin/addTest17/addquestion/'.$val, 'refresh');
	}
}