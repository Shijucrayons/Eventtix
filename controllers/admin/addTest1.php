<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class addTest1 extends CI_Controller {

	 
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
	
	$dropType = $this->uri->segment(5,1);
	
	$content['dropType']=$dropType;
	
	//$inStep = $this->uri->segment(7,'add');
	
	
		
		$content['task_id']=$task_id;
		
	  
		
		// field name, error message, validation rules
		if(isset($_POST['save']))
		{
			$dropType = $this->input->post('dropType');
			
			if($dropType==1)
			{
				$this->form_validation->set_rules('option', 'Option', 'required|callback_isExist[option,'.$task_id.']');
				  if($this->form_validation->run())
				  {
				  $optiondata['task_id']=$task_id;
				  $optiondata['option']=$this->input->post('option');
				  
				  $this->questionmodel->add_1_option($optiondata);
				  redirect('admin/addTest1/addquestion/'.$task_id.'/1', 'refresh');
				  }
				
			}
			else if($dropType==2)
			{
				$this->form_validation->set_rules('question', 'Question', 'required');
				$this->form_validation->set_rules('answer_txt', 'Answer', 'required');
				
				if($this->form_validation->run())
				{
					$testdata['question'] = $content['question'] = $this->input->post('question');
					$testdata['task_id']=$task_id;
					$testdata['answer_desc'] = $content['answer_desc'] = $this->input->post('answer_txt');
					$testdata['correct_option_id'] = $this->input->post('optionId');
					$this->questionmodel->add_1_question($testdata);
					redirect('admin/addTest1/addquestion/'.$task_id.'/2', 'refresh');
					
				}
			}
		}
/*...............................................................*/
		if($dropType==1)
		{
			$this->db-> select('*');
			$this->db-> from('radio_options');
			$this->db-> where('task_id',$task_id);
			
			$query = $this->db->get();
		
			$data = array();
			if($query -> num_rows() >0 )
			{
				$result = $query -> result();
				$q=0;
				foreach($result as $row)
				{
				   $content['optionName'][$q] = $row->option;
	$content['action'][$q] = '<a href="'.base_url().'admin/addTest1/edit_option/'.$row->option_id.'/'.$task_id.'">Edit</a>';
$content['action'][$q] .=' | <a href="'.base_url().'admin/addTest1/delete_option/'.$row->option_id.'/'.$task_id.'">Delete</a>';					
		  
				  $q++;	
				}
			}
			
		}
		else
		{
			$this->db-> select('*');
			$this->db-> from('radio_questions');
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
				 $content['action'][$q] = '<a href="'.base_url().'admin/addTest1/edit/'.$row->id.'/'.$task_id.'">Edit</a>';
				 $content['action'][$q] .=' | <a href="'.base_url().'admin/addTest1/delete/'.$row->id.'/'.$task_id.'">Delete</a>';
					
					$this->db-> select('*');
					$this->db-> from('radio_options');
					$this->db-> where('option_id',$row->correct_option_id);
					$query1 = $this->db->get();
					
					if($query1 -> num_rows() >0 )
					{
						$result1 = $query1 -> result();
				
						foreach($result1 as $row1)
						{
							 $content['correct'][$q] = $row1->option;
						}
					}
			
			$q++;	
			}
			}
			$optionsArr = $this->questionmodel->getOptionInTask($task_id);
			$op=0;
			foreach($optionsArr as $row3)
			{
				$content['optionsForQ'][$op] = $row3->option;
				$content['optionId'][$op] = $row3->option_id;
				$op++;
			}
			
		}
		
/*...............................................................*/
	
		$data['view'] = 'viewTest1';
	
		$content['mode']=0;
		$data['content'] = $content;
		
		$this->load->view('admin/template',$data);
	
	}
	
	
	function edit($id,$task_id){
		$content = array();
		$question = $this->questionmodel->getQuestion1($id);
		foreach($question as $row){
			$content['currOp']=$row->correct_option_id;
			$content['question_id']=$row->id;
			$content['question']=$row->question;
			$content['answer_txt']=$row->answer_desc;
		}
		
		$options=$this->questionmodel->getOptions1($task_id);
		$op=0;
			foreach($options as $row3)
			{
				$content['optionsForQ'][$op] = $row3->option;
				$content['optionId'][$op] = $row3->option_id;
				$op++;
			}
		$content['dropType'] = 2;
		if(isset($_POST['save']))
		{
			$dropType = $this->input->post('dropType');
			
			if($dropType==1)
			{
				$this->form_validation->set_rules('option', 'Option', 'required|callback_isExist[option,'.$task_id.']');
				  if($this->form_validation->run())
				  {
				 $optiondata['task_id']=$task_id;
				 $optiondata['option']=$this->input->post('option');
				  
				 $this->questionmodel->update_1_option($optiondata);
				 redirect('admin/addTest1/addquestion/'.$task_id.'/1', 'refresh');
				  }
				
			}
			else if($dropType==2)
			{
				$this->form_validation->set_rules('question', 'Question', 'required');
				$this->form_validation->set_rules('answer_txt', 'Answer', 'required');
				
				if($this->form_validation->run())
				{
					$content['task_id']=$task_id;
					$testdata['question'] = $content['question'] = $this->input->post('question');
					$testdata['correct_option_id'] = $this->input->post('optionId');
					$testdata['answer_desc'] = $content['answer_desc'] = $this->input->post('answer_txt');
					$this->db->where('id',$id);
					$this->db->update('radio_questions',$testdata);
					redirect('admin/addTest1/addquestion/'.$task_id.'/2', 'refresh');
					
				}
			}
		}
		
		
				
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		$content['mode']=1;
		$data['view'] = 'viewTest1';
		$data['content'] = $content;

		$this->load->view('admin/template',$data);
		
	}
		
	function delete_option($option_id,$task_id)
	{
		$this->db->where('option_id',$option_id);
		$this->db->delete('radio_options');
		redirect('admin/addTest1/addquestion/'.$task_id);
	}
	function delete($id,$task_id)
	{
		$this->db->where('id',$id);
		$this->db->delete('radio_questions');
		redirect('admin/addTest1/addquestion/'.$task_id);
	}
	function edit_option($option_id,$task_id)
	{
		$options=$this->questionmodel->getTest1_optionById($option_id);
		$op=0;
			foreach($options as $row3)
			{
				$content['optionsForQ'] = $row3->option;
				$content['optionId'] = $row3->option_id;
				$content['task_id'] = $task_id;
				$op++;
			}
			 if(isset($_POST['Submit']))
		     {
		
			$testdata  = array();
		    $testdata['option'] = $this->input->post('ansOpt');
			$this->form_validation->set_rules('ansOpt', 'Option', 'trim|required');
			
			
			if($this->form_validation->run())
			{
				
			 	 $this->questionmodel->update_option($testdata,$option_id);
			 	 $this->session->set_flashdata('message', 'Test Updated');
			 	 redirect('admin/addTest1/addquestion/'.$task_id, 'refresh');
			}
		}	
		if(isset($this->flashmessage))
		$content['flashmessage'] = $this->flashmessage;
		$data['view'] = 'optionedit';
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
	
	function isExist($val,$taskId)
	{
		if($val=='')
		{
			 $this->form_validation->set_message('option', 'Please fill the option field');
                return FALSE;
		}
		else if($this->questionmodel->getOptionInTask($val,$taskId)==TRUE)
		{
			$this->form_validation->set_message('Option', 'This option already used in this task');
                return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

}