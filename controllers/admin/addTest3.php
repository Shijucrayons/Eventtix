<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class addTest3 extends CI_Controller {

	 
	function __construct()
	{
		parent::__construct();
		$this->load->model('questionmodel','',TRUE);
		
		if(!$this->session->userdata('admin_logged_in'))
   		redirect('admin/login', 'refresh');
   		
   		if($message = $this->session->flashdata('message')){
          $this->flashmessage =$message;
   		}
		if($message = $this->session->flashdata('err_msg')){
          $this->err_msg =$message;
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
		if(isset($this->err_msg))$data['err_msg']= $this->err_msg;
		if(isset($this->flashmessage))$data['flashmessage']= $this->flashmessage;

		
		
		if(isset($_POST['dropType']))
		{
			
			$testdata  = array();
			
				$this->form_validation->set_rules('question', 'Question', 'required');
			
					
			if($this->form_validation->run())
			{	
			$original_string = $this->input->post('question');
				$dropType = $this->input->post('dropType');
			$startCount  = substr_count($original_string, '[');
			$endCount    = substr_count($original_string, ']');
			//echo $original_string.'/'.$startCount.'/'.$endCount;exit;
				$questionArr = $this->questionmodel->splitQuestion_3($original_string);
				$msg="";
				$class ="";
				if($startCount!=$endCount)
				{
					$msg="All brackets are not closed.";
					$class ="n_warning";
				}
				else if(empty($questionArr['answer']))
				{
					$msg="Brackes are empty";
					$class="n_warning";
				}
				else if($startCount != count($questionArr['answer']))
				{
					$msg="Brackes are empty";
					$class="n_warning";
				}
				
				if($msg!="")
				{
					$this->session->set_flashdata('err_msg',$msg);
					$this->session->set_flashdata('err_class',$class);
					redirect('admin/addTest3/addquestion/'.$task_id, 'refresh');
				}
				else
				{	
				$answer = implode(',',$questionArr['answer']);		
				$testdata['original_string'] = $content['question'] = $original_string;
				$testdata['question_string'] =$questionArr['question'];
				$testdata['answer_string'] = $answer;
				$testdata['task_id'] = $content['task_id'] = $task_id;
				$testdata['type'] = $dropType;
				
			 	$this->questionmodel->add_3_question($testdata);
				$this->session->set_flashdata('message','Gapfill question successfully added.');
			 	redirect('admin/addTest3/addquestion/'.$task_id, 'refresh');
				}
			
			}
			else
			{
				echo "form validation failed";exit;
			}
		}
	$this->db->select("*");
	$this->db->from('gapfill_question');
	$this->db->where('task_id',$task_id);
	$query = $this->db->get();
	if($query->num_rows()>=1)
	{
		$data['questionList']=$query->result();
	}
	
	$data['view'] = 'viewTest3';
	
		$content['mode']=0;
		$data['content'] = $content;
		
		$this->load->view('admin/template',$data);
	
	}

function edit($id,$task_id=NULL)
	{
		if(isset($this->err_msg))$data['err_msg']= $this->err_msg;
		if(isset($this->flashmessage))$data['flashmessage']= $this->flashmessage;

		
		if(isset($_POST['dropType']))
		{
			
			$testdata  = array();
			
				$this->form_validation->set_rules('question', 'Question', 'required');
			
					
			if($this->form_validation->run())
			{	
			$original_string = $this->input->post('question');
				$dropType = $this->input->post('dropType');
			$startCount  = substr_count($original_string, '[');
			$endCount    = substr_count($original_string, ']');
			//echo $original_string.'/'.$startCount.'/'.$endCount;exit;
				$questionArr = $this->questionmodel->splitQuestion_3($original_string);
				
				$msg="";
				$class ="";
				if($startCount!=$endCount)
				{
					$msg="All brackets are not closed.";
					$class ="n_warning";
				}
				else if(empty($questionArr['answer']))
				{
					$msg="Brackes are empty";
					$class="n_warning";
				}
				else if($startCount != count($questionArr['answer']))
				{
					$msg="Brackes are empty or bracket closeing/opening missing.";
					$class="n_warning";
				}
				
				//echo $msg;exit;
				if($msg!="")
				{
					
					$this->session->set_flashdata('err_msg',$msg);
					$this->session->set_flashdata('err_class',$class);
					redirect('admin/addTest3/addquestion/'.$task_id, 'refresh');
				}
				else
				{
					//echo "enter else";	exit;
				$answer = implode(',',$questionArr['answer']);		
				$testdata['original_string'] = $content['question'] = $original_string;
				$testdata['question_string'] =$questionArr['question'];
				$testdata['answer_string'] = $answer;
				$testdata['task_id'] = $content['task_id'] = $task_id;
				$testdata['type'] = $dropType;
				
			 	$this->questionmodel->update_3_question($id,$testdata);
				$this->session->set_flashdata('message','Gapfill question successfully Updated.');
			 	redirect('admin/addTest3/addquestion/'.$task_id, 'refresh');
				}
			
			}
			else
			{
				echo "form validation failed";exit;
			}
		}
	$this->db->select("*");
	$this->db->from('gapfill_question');
	$this->db->where('id',$id);
	$query = $this->db->get();
	if($query->num_rows()>=1)
	{
		$result=$query->result();
		foreach($result as $row)
		{
			$content['question']=$row->original_string;
			$content['tasktype']=$row->type;
		}
	}
	
	$data['view'] = 'viewTest3';
	
		$content['mode']=1;
		$data['content'] = $content;
		
		$this->load->view('admin/template',$data);
	
	}


function delete($id,$task_id)
	{
	 
	  $this->questionmodel->gapfill_delete($id);
	   $this->session->set_flashdata('message', 'Question Deleted');
	   redirect('admin/addTest3/addquestion/'.$task_id, 'refresh');
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