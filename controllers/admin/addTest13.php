<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class addTest13 extends CI_Controller {

	 
	function __construct()
	{
		parent::__construct();
		//$this->load->model('questionmodel_test','',TRUE);
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
			
			$testdata['task_id']=$task_id;
		    $testdata['question'] = $content['question'] = $this->input->post('question');
		   //$testdata['answer_desc'] = $content['answer_desc'] = $this->input->post('answer_txt');
			
			$this->form_validation->set_rules('question', 'Question', 'required');
			//$this->form_validation->set_rules('answer_txt', 'Answer', 'required');
			$this->form_validation->set_rules('ans[]', 'ans', 'required');
			$this->form_validation->set_rules('ansopt[]', 'ansopt', 'required');
			
			if($this->form_validation->run())
			{	
			
			 	$optiondata['question_id'] = $this->questionmodel->add_13_question($testdata);
			 	$optiondata['answeropt']=$this->input->post('ansopt');
				$optiondata['answer']=$this->input->post('ans');
				$count=count($optiondata['answer']);
				
				for ($i=0; $i<$count; $i++) {
						$insert['question_id']=$optiondata['question_id'];
						$insert['answer']=$optiondata['answer'][$i];
						
						if(in_array(($i+1),$optiondata['answeropt'])){
							$insert['is_correct']='1';
						}
						else{
							$insert['is_correct']='0';
						}
						
						//echo $insert['is_correct'];
						$this->questionmodel->add_13_option($insert);

				}

			 	redirect('admin/addTest13/addquestion/'.$task_id, 'refresh');
			}
		}
/*...............................................................*/


        
        $this->db-> select('*');
		$this->db-> from(' multiple_box_questions');
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
			   $content['action'][$q] = '<a href="'.base_url().'admin/addTest13/edit/'.$row->id.'/'.$row->task_id.'">Edit</a>';
			    $content['action'][$q] .=' | <a href="'.base_url().'admin/addTest13/delete/'.$row->id.'/'.$row->task_id.'">Delete</a>';
				
				$this->db-> select('*');
				$this->db-> from(' multiple_box_options');
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
	
		$data['view'] = 'viewTest13';
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

	

	function edit($id,$task_id){
		$content = array();
		//$content['task_id']=$id;
		
		//$id = $_GET['id'];
		
		$pagedata = $this->questionmodel->fetch_multipleboxquestion($id);
		
		foreach($pagedata as $row){
			$content['question_id']=$row->id;
			
		}
		
		$pageoption=$this->questionmodel->fetch_multipleboxoption($content['question_id']);
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

	
		
		foreach($pagedata as $rowz){

            $content['question_id']  = $rowz->id;
			$content['task_id']  = $rowz->task_id;
			$content['question'] = $rowz->question;
			
			
		}
		
		
		if(isset($_POST['question']))
		{
			
			
			$testdata  = array();
			$val=$testdata['task_id']=$content['task_id'];

			
		    $testdata['question'] = $content['question'] = $this->input->post('question');
		    //$testdata['answer_desc'] = $content['answer_desc'] = $this->input->post('answer_desc');
			//echo $val."<br>".$testdata['question']."<br>".$testdata['answer_desc'];exit;
			$this->form_validation->set_rules('question', 'Question', 'required');
			if($this->form_validation->run())
			{	
			
			
			 	$this->questionmodel->update_multiple_box_question($testdata,$content['task_id']);
			 	
			 	$optiondata['answeropt']=$this->input->post('ansopt');
				$optiondata['answer']=$this->input->post('ans');
				$count=count($optiondata['answer']);
				//echo count($content['option_id']);exit;
				for ($i=0; $i<$count; $i++) {
						$update['question_id']=$content['question_id'];
						$update['answer']=$optiondata['answer'][$i];
						if(isset($content['option_id'][$i]))
						{
						$option_id=$content['option_id'][$i];
						}
						$j=$i+1;
						if(in_array(($i+1),$optiondata['answeropt'])){
							$update['is_correct']='1';
						}
						else{
							$update['is_correct']='0';
						}
						//echo "<pre>";print_r($update['answer'][8]);exit;
						if(count($content['option_id'])<=$i)
						{
							$this->questionmodel->add_13_option($update);
						}
						else
						{
						$this->questionmodel->update_multipe_box_option($update,$option_id);
						}

				}
				//echo "<pre>";print_r($content['option_id']);exit;
				for($i=$count;$i<count($content['option_id']);$i++)
				{
					$this->questionmodel->delete_multiple_box_options($content['option_id'][$i]);
				}
				
			 	redirect('admin/addTest13/addquestion/'.$task_id, 'refresh');
			}
			
		}
			
			//-----------------------------------------------------
			
			
		$this->db-> select('*');
		$this->db-> from(' multiple_box_questions');
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
			   $content['action'][$q] = '<a href="'.base_url().'admin/addTest13/edit/'.$row->id.'/'.$row->task_id.'">Edit</a>';
			    $content['action'][$q] .=' | <a href="'.base_url().'admin/addTest13/delete/'.$row->id.'/'.$row->task_id.'">Delete</a>';
				
				$this->db-> select('*');
				$this->db-> from(' multiple_box_options');
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
		$data['view'] = 'viewTest13';
		$data['content'] = $content;

		$this->load->view('admin/template',$data);
		
	}	


	function delete($task_id){
		
		
	  $id=$this->questionmodel->fetch_box_id($task_id);
		foreach ($id as $key) {
			$val=$key->id;
		}
	 $this->questionmodel->question_multiplebox_delete($task_id);
	 $this->questionmodel->delete_multiplebox_options($val);
	 $this->session->set_flashdata('message', 'Question Deleted');
	   redirect('admin/tasklist/taskdetails', 'refresh');
	}
}