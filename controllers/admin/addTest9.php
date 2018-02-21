<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class addTest9 extends CI_Controller {

	 
	function __construct()
	{
		parent::__construct();
		$this->load->model('questionmodel','',TRUE);
		
		if(!$this->session->userdata('admin_logged_in'))
   		redirect('admin/login', 'refresh');
   		
   		if($message = $this->session->flashdata('message')){
          $this->flashmessage =$message;
   		}
		if($err_msg = $this->session->flashdata('err_msg')){
          $this->err_msg =$err_msg;
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
			
			$testdata['task_id']=$task_id;
		    $testdata['question'] = $content['question'] = $this->input->post('question');
		    $testdata['answer_desc'] = $content['answer_desc'] = $this->input->post('answer_desc');
			
			$this->form_validation->set_rules('question', 'Question', 'required');
			$this->form_validation->set_rules('answer_desc', 'Answer', 'required');
			$this->form_validation->set_rules('ans[]', 'ans', 'required');
			$this->form_validation->set_rules('ansopt', 'ansopt', 'required');
			
			if($this->form_validation->run())
			{	
			 	$optiondata['question_id'] = $this->questionmodel->add_multipe_question($testdata);
				
			 	$optiondata['answer']=$this->input->post('ans');
				$optiondata['answeropt']=$this->input->post('ansopt');
				$count=count($optiondata['answer']);
				
				for ($i=0; $i<$count; $i++) {
						$insert['question_id']=$optiondata['question_id'];
						$insert['answer']=$optiondata['answer'][$i];
						$j=$i+1;
						if($j==$optiondata['answeropt'][0]){
							$insert['is_correct']='1';
						}
						else{
							$insert['is_correct']='0';
						}
						$this->questionmodel->add_multipe_option($insert);

				}

			 	redirect('admin/addTest9/addquestion/'.$task_id, 'refresh');
			}
		}
/*...............................................................*/


        
        $this->db-> select('*');
		$this->db-> from(' multiple_choice_questions');
		$this->db-> where('task_id',$task_id);
		$query = $this->db->get();
		
		$data = array();
        if($query -> num_rows() >0 )
		{
			$result = $query -> result();
			$q=0;
			foreach($result as $row)
			{
			 $content['questions'][$q] = $row->question;
			   $content['action'][$q] = '<a href="'.base_url().'admin/addTest9/edit/'.$row->id.'/'.$task_id.'">Edit</a>';
			    $content['action'][$q] .=' | <a href="'.base_url().'admin/addTest9/delete/'.$row->id.'/'.$task_id.'">Delete</a>';
				
				$this->db-> select('*');
				$this->db-> from(' multiple_choice_options');
				$this->db-> where('question_id',$row->id);
				$query1 = $this->db->get();
				
				if($query1 -> num_rows() >0 )
				{
					$result1 = $query1 -> result();
					$a=0;
					foreach($result1 as $row1)
					{
						 $content['options'][$q][$a] = $row1->answer;
						 $content['correct'][$q][$a] = $row1->is_correct;
						
						 $a++;
					}
				}
		
		$q++;	
		}
		}
/*...............................................................*/
	
		$data['view'] = 'multiplechoice';
		$content['mode']=0;
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
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;

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
		$this->db-> from(' multiple_choice_questions');
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
			   //print_r($row);die;

				
			   $action = '<a href="'.base_url().'admin/multiplechoice/edit/'.$row->id.'/'.$task_id.'">Edit</a>';
			    $action .=' | <a href="'.base_url().'admin/multiplechoice/delete/'.$row->id.'/'.$task_id.'">Delete</a>';
				
				 $data['rows'][] = array(
				'id' => $row->id,
				'cell' => array($row->id,$row->question,$row->answer_desc,$action)
			);
			}
			
		}
        

       echo json_encode($data); exit(); 
        
       
           
	}

	function edit($id,$task_id){
		$content = array();
		//$content['task_id']=$id;
		
		//$id = $_GET['id'];
		
		$pagedata = $this->questionmodel->fetch_multiplequestion($id);
		foreach($pagedata as $row){
			$content['question_id']=$row->id;
		}
		//echo $content['question_id'];exit;
		$pageoption=$this->questionmodel->fetch_multipleoption($content['question_id']);
		//echo "<pre>";print_r($pageoption);exit;
		$options_count=count($pageoption);
		//echo $options_count;exit;
		$i=0;
		foreach($pageoption as $row5){
			$content['option_id'][$i]=$row5->option_id;
			$content['ans'][$i]=$row5->answer;
			$content['ansopt'][$i]=$row5->is_correct;
			$i++;
		}
		//echo "<pre>";print_r($content['option_id']);exit;
		$content['options_count']=$options_count;
		$content['id']=$id;
        $content['task_id']  = $task_id;
	
		
		foreach($pagedata as $rowz){

            $content['question_id']  = $rowz->id;
			$content['question'] = $rowz->question;
			$content['answer_desc'] = $rowz->answer_desc;
			
		}
		
		
		if(isset($_POST['question']))
		{
			
			
			$testdata  = array();
			$val=$testdata['task_id']=$content['task_id'];

			
		    $testdata['question'] = $content['question'] = $this->input->post('question');
		    $testdata['answer_desc'] = $content['answer_desc'] = $this->input->post('answer_desc');
			//echo $val."<br>".$testdata['question']."<br>".$testdata['answer_desc'];exit;
			$this->form_validation->set_rules('question', 'Question', 'required');
			$this->form_validation->set_rules('answer_desc', 'Answer', 'required');
			$this->form_validation->set_rules('ans[]', 'ans', 'required');
			$this->form_validation->set_rules('ansopt', 'ansopt', 'required');
			
			if($this->form_validation->run())
			{	
			
			
			 	$this->questionmodel->update_multiple_question($testdata,$content['question_id']);
			 	
			 	$optiondata['answeropt']=$this->input->post('ansopt');
				$optiondata['answer']=$this->input->post('ans');
				$count=count($optiondata['answer']);
				
					for ($i=0; $i<$count; $i++) {
						$update['question_id']=$content['question_id'];
						$update['answer']=$optiondata['answer'][$i];
						 $option_id=$content['option_id'][$i];
						$j=$i+1;
						if($j==$optiondata['answeropt'][0]){
							$update['is_correct']='1';
						}
						else{
							$update['is_correct']='0';
						}
		
						//echo "<pre>";print_r($update['answer']);exit;
						if(count($content['option_id'])<=$i)
						{
							$this->questionmodel->add_9_option($update);
						}
						else
						{
						$this->questionmodel->update_multipe_option($update,$option_id);
						}

				}
				
				for($i=$count;$i<count($content['option_id']);$i++)
				{    
		            //echo "<pre>";print_r($content['option_id']);exit;
					$this->questionmodel->delete_multiple9_options($content['option_id'][$i]);
				}
				
			 	redirect('admin/addTest9/addquestion/'.$task_id, 'refresh');
			}
			
		}
			
			//-----------------------------------------------------
			
			
		$this->db-> select('*');
		$this->db-> from(' multiple_choice_questions');
		$this->db-> where('task_id',$id);
		$query = $this->db->get();
		
		$data = array();
        if($query -> num_rows() >0 )
		{
			$result = $query -> result();
			//echo "<pre>";print_r($result);exit;
			$q=0;
			foreach($result as $row3)
			{
			 $content['questions'][$q] = $row3->question;
			   $content['action'][$q] = '<a href="'.base_url().'admin/addTest9/edit/'.$row3->task_id.'">Edit</a>';
			    $content['action'][$q] .=' | <a href="'.base_url().'admin/addTest9/delete/'.$row3->task_id.'">Delete</a>';
				
				$this->db-> select('*');
				$this->db-> from(' multiple_choice_options');
				$this->db-> where('question_id',$row3->id);
				$query1 = $this->db->get();
				//echo "<pre>";print_r($result1);exit;
				if($query1 -> num_rows() >0 )
				{
					$result1 = $query1 -> result();
					
					
					$a=0;
					foreach($result1 as $row4)
					{
						
						 $content['options'][$q][$a] = $row4->answer;
						 $content['correct'][$q][$a] = $row4->is_correct;
						
						 $a++;
					}
				}
		
		$q++;	
		}
		}
		
		//----------------------------
			
			
				
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		$content['mode']=1;
		$data['view'] = 'multiplechoice';
		$data['content'] = $content;

		$this->load->view('admin/template',$data);
		
	}	

	function delete($id,$task_id){
		
		
	 $this->questionmodel->question_multiple_delete($id);
	 $this->questionmodel->delete_multiple_options($id);
	   $this->session->set_flashdata('message', 'Question Deleted');
	  redirect('admin/addTest9/addquestion/'.$task_id, 'refresh');
	}
}